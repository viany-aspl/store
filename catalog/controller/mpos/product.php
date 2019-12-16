<?php

class ControllermposProduct extends Controller 
{
    
    private $debugIt = false;
   
    public function adminmodel($model) 
	{
      
      $admin_dir = DIR_SYSTEM;
      $admin_dir = str_replace('system/','backoffice/',$admin_dir);
      $file = $admin_dir . 'model/' . $model . '.php';      
      //$file  = DIR_APPLICATION . 'model/' . $model . '.php';
      $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
      
      if (file_exists($file)) {
         include_once($file);
         
         $this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
      } else {
         trigger_error('Error: Could not load model ' . $model . '!');
         exit();               
      }
   }
	
    public function sproductsinv()
    {
        $log =new Log("prdinv-sproductsinv-".date('Y-m-d').".log");
		$log->write("sproductsinv called ");
		$log->write($this->request->get);
		$log->write($this->request->post);
		$mcrypt=new MCrypt();    
		
		$this->request->post['service_type']=$mcrypt->decrypt($this->request->post['service_type']);
		$this->request->post['page']=$mcrypt->decrypt($this->request->post['page']);
		$log->write($this->request->post);
		if($this->request->post['service_type']=='PO_Request')
		{
			$this->get_product_for_po($this->request->post,$this->request->get);
			return;
		}
        
        $this->adminmodel('pos/pos');            
		$this->load->library('user');
		unset($this->session->data['user_id']);
		$this->session->data['user_id']=$mcrypt->decrypt( $this->request->post['username']);
        $this->user = new User($this->registry);
        
        if (isset($this->request->get['q'])) 
        {
            $q = $mcrypt->decrypt($this->request->get['q']);
			$return_type='products';
			$json = array('success' => true, 'products' => array());
        } 
		else if (isset($this->request->get['search'])) 
		{
			$q = ($this->request->get['search']);
			$return_type='records';
			$json = array('success' => true, 'records' => array());
		}
        else 
        {
           $q = '';
		   $return_type='products';
		   $json = array('success' => true, 'products' => array());
        }
		
        if (isset($this->request->get['page'])) 
        {
            $page = $this->request->get['page'];
        } 
        else 
        {
            $page = 1;
        }
        $limit    = 20;
        $offset   = ($page-1)*$limit;
        $log->write("products ".$q);
        $this->config->set('config_store_id',$this->user->getStoreId());
        $this->load->model('catalog/product'); 
        //$products = $this->model_pos_pos->searchProductsStore($q,$limit,$offset);
		if($this->request->post['service_type']=='Sale_To_Retailer')
		{
			$products = $this->model_catalog_product->getProducts(array(
			'start'=> $offset,
			'limit'=> $limit,
            'product_name'=>$q,
			'quantity_check'=>1
            ));
		}
		else
		{
			$products = $this->model_catalog_product->getProducts(array(
			'start'=> $offset,
			'limit'=> $limit,
            'product_name'=>$q,
			'for_store'=>'inventory_report'
            ));
		}
        $this->config->set('config_store_id',$mcrypt->decrypt( $this->request->post['store_id']));
        $log->write("before call to own_products  in product/products");
        $own_products = $this->model_catalog_product->getProducts(array(
            'product_name'=>$q,
            'quantity_check'=>1
			));
        //$log->write($products);
        $log->write(sizeof($products));
        $total=0;
		foreach ($products as $product) 
        {
            $log->write("data products loop");
			$log->write($product['category_ids']);
		$category_id=0;
		foreach($product['category_ids'] as $category_ids)
		{
			if($category_ids!=44)
			{
				$category_id=$category_ids;
			}
		}
            if(empty($product))
            {                            
                continue;
            }
			if(empty($product['manufacturer']))
			{
				$manufacturer=$this->model_catalog_product->getProductmanufacturer(array('manufacturer_id'=>$product['manufacturer_id']));
			}
			else
			{
				$manufacturer=$product['manufacturer'];
			}
			$log->write('manufacturer');
			$log->write($manufacturer);
            $log->write('array_key_exists');
            $log->write(array_key_exists($product['product_id']));
            $log->write(array_key_exists($own_products));
            $log->write(array_key_exists($product['product_id'],$own_products));
            if(!empty($own_products)&&(array_key_exists($product['product_id'],$own_products)))
            {
                $log->write('in if own_products is not empty and product_id in own_products for product_id: '.$product['product_id']);
                $product['price']=$own_products[$product['product_id']]['pd']['store_price'];
            }
            else
            {
                $log->write('in else own_products is not empty and product_id in own_products for product_id: '.$product['product_id']);
                if(empty($product['pd']['store_price'])||$product['pd']['store_price']==0.0000)
			{
                    $log->write('in if store_price is empty or 0 for product_id: '.$product['product_id']);
                    $product['price']=$product['price'];
			}
			else
			{
                    $log->write('in else store_price is empty or 0 for product_id: '.$product['product_id']);
                    $product['price']=$product['pd']['store_price'];
			}
            }
            if(empty($product['HSTN']))
            {
				$product['HSTN']="0000";
            }
            $this->adminmodel('pos/pos');
            if(!empty($own_products)&&(array_key_exists($product['product_id'],$own_products)))
            {
                $product['quantity']=$own_products[$product['product_id']]['pd']['quantity'];//$productprice['quantity'];
            }
            else
            {
                $product['quantity']=$product['pd']['quantity'];
            }
			if(!empty($own_products)&&(array_key_exists($product['product_id'],$own_products)))
            {
                $product['mitra_quantity']=$own_products[$product['product_id']]['pd']['mitra_quantity'];
            }
            else
            {
                $product['mitra_quantity']=$product['pd']['mitra_quantity'];
            }
            if(empty($product['price']))
            {
                $product['price']=0.0;
            }
			if($product['price']=='0.0')
			{
				$product['price']=$product['price_tax'];
			}
           
            $price_with_tax=($product['price'])+(($this->tax->getTax($product['price'], $product['tax_class_id'])));
			if($return_type=='records')
			{
				$pprice=($product['price']);
				$pid=($product['product_id']);
				$pname=($product['name']);
			}
			else
			{
				$pprice=$mcrypt->encrypt($this->currency->format($product['price']));
				$pid=$mcrypt->encrypt($product['product_id']);
				$pname=$mcrypt->encrypt($product['name']);
			}
            $json[$return_type][] = array(
					'id'			=> $pid,
					'name'			=> $pname,
					'quantity'  	=> $mcrypt->encrypt(empty($product['quantity'])? 0:$product['quantity']),
					'mitra_quantity'  	=> $mcrypt->encrypt(empty($product['mitra_quantity'])? 0:$product['mitra_quantity']),
					'hstn'   		=> $mcrypt->encrypt(empty($product['HSTN'])? 0:$product['HSTN']),
					'hsn'   		=> $mcrypt->encrypt(empty($product['HSTN'])? 0:$product['HSTN']),
					'description'	=> $mcrypt->encrypt("0"),
					'pirce'			=> $pprice,
					'price'			=> $pprice ,
					'sku'			=> $mcrypt->encrypt($product['sku']),
					'manufacturer_id'			=> $mcrypt->encrypt($product['manufacturer_id']),
					'manufacturer_name'			=> $mcrypt->encrypt($manufacturer) ,
					'href'			=> $mcrypt->encrypt($this->url->link('product/product', 'product_id=' . $product['product_id'])),
					'thumb'			=> $mcrypt->encrypt($product['image']),
					'special'		=> $mcrypt->encrypt($special),
					'rating'		=> $mcrypt->encrypt($product['rating']), 
					'subtract'		=> $mcrypt->encrypt($product['subtract']), 
					'tax'			=> $mcrypt->encrypt(($this->tax->getTax($product['price'], $product['tax_class_id']))),//
					'per_tax'       => $mcrypt->encrypt(json_encode($this->tax->getRates($product['price'], $product['tax_class_id']))),
                    'pricewithtax'  => $mcrypt->encrypt($price_with_tax),
					'category'		=> $this->request->get['category'],
					'category_id'	=>$mcrypt->encrypt($category_id),
					'subsidy'		=> $mcrypt->encrypt( empty($product['subsidy'])? 0:$product['subsidy'])
				);
            $total=$total+($product['quantity']*($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))+($this->tax->getTax($product['price'], $product['tax_class_id']))));
		}
		$json['total']=$mcrypt->encrypt($total);

		$json['listcount']=$mcrypt->encrypt(sizeof($products));
		return $this->response->setOutput(json_encode($json));
    }
	private function get_product_for_dashboard($ppost,$gget)
	{
		$mcrypt=new MCrypt();	
        $json = array();
		$log =new Log("prdinv-pur-".date('Y-m-d').".log");
        $log->write('products called');
		
		if (!empty($ppost['page'])) 
		{
                $page = $ppost['page'];
        } 
		else if (!empty($gget['page'])) 
		{
                $page = $gget['page'];
        } 
		else 
		{
                $page = 1;
        }
		$limit    = 20;
		$log->write('page');
		$log->write($page);
		$offset = ($page-1);//*$limit;
			
		$log->write($offset);
		
		$this->load->model('catalog/product');
		if (isset($gget['category'])) 
		{
        	$category_id =$mcrypt->decrypt($gget['category']);
		} 
		else 
		{
            $category_id = 0;
		}
		$products = $this->model_catalog_product->getProducts(array(
            		'filter_category_id'        => $category_id,
            		
					'store_id'=>$mcrypt->decrypt( $ppost['store_id']),
					'for_store'=>'own'.$mcrypt->decrypt( $ppost['store_id'])
            ));
		$log->write("products count");
		$log->write(count($products));
		
		$products=array_slice($products,$offset,$limit);
		
		$log->write("products count after slice");
		$log->write(count($products));
			
		$price = array();
		foreach ($products as $key => $row)
		{
			$price[$key] = $row['name'];
		}
		array_multisort($price, SORT_ASC, $products);
		//$log->write($products);
		$own_products=array();
		$this->adminmodel('setting/setting');
		$this->adminmodel('tool/image');
		$this->load->model('catalog/product');
        foreach ($products as $product) 
		{ 
            //$log->write("data products loop");
			
            if(empty($product))
            {                            
                continue;
            }
          
            //$log->write('array_key_exists');
            //$log->write(array_key_exists($product['product_id']));
            //$log->write(array_key_exists($own_products));
            //$log->write(array_key_exists($product['product_id'],$own_products));
            if(!empty($own_products)&&(array_key_exists($product['product_id'],$own_products)))
            {
                //$log->write('in if own_products is not empty and product_id in own_products for product_id: '.$product['product_id']);
                $product['price']=$own_products[$product['product_id']]['pd']['store_price'];
				$product['favourite']=$own_products[$product['product_id']]['pd']['favourite'];
            }
            else
            {
                //$log->write('in else own_products is not empty and product_id in own_products for product_id: '.$product['product_id']);
                if(empty($product['pd']['store_price'])||$product['pd']['store_price']==0.0000)
				{
                   // $log->write('in if store_price is empty or 0 for product_id: '.$product['product_id']);
                    $product['price']=$product['price'];
					$product['favourite']=$product['pd']['favourite'];
				}
				else
				{
                    //$log->write('in else store_price is empty or 0 for product_id: '.$product['product_id']);
                    $product['price']=$product['pd']['store_price'];
					$product['favourite']=$product['pd']['favourite'];
				}
            }
            if(empty($product['HSTN']))
            {
				$product['HSTN']="0000";
            }
            $this->adminmodel('pos/pos');
            if(!empty($own_products)&&(array_key_exists($product['product_id'],$own_products)))
            {
                $product['quantity']=$own_products[$product['product_id']]['pd']['quantity'];//$productprice['quantity'];
            }
            else
            {
                $product['quantity']=$product['pd']['quantity'];
            }
			//$log->write('updated price 1: '.$product['price']);
            if(empty($product['price']))
            {
                $product['price']=$product['price_tax'];
            }
			if(empty($product['favourite']))
			{						
				$product['favourite']=0;   
			}
			//$log->write('updated price 2: '.$product['price']);
			if(empty($product['price']))
			{
				$product['price']=0.0;
			}
			$points = $this->model_catalog_product->getProductReward(array('product_id'=>$product['product_id'],'store_id'=>$mcrypt->decrypt( $this->request->post['store_id'])));
                        
			$log->write('points');
                        
			$log->write($points);
			if(empty($points))
			{
				$points=0;
			}
			$log->write($points);
			//$log->write($product['image']);
			//$product['image']=urlencode($product['image']);
			//$log->write($product['image']);
			//$log->write('updated price 3: '.$product['price']);
			$log->write($product['image']);
            $product['image']=$product['image']?$this->model_tool_image->resize($product['image'], 180, 180):'view/image/pos/logo.png';
			$log->write($product['image']);
			$product['image']=str_replace(HTTPS_CATALOG,'',$product['image']);
			$log->write($product['image']);
			
            $bookmark_info = $this->model_catalog_product->getProductBookmark(array('product_id'=>$product['product_id'],'store_id'=>$mcrypt->decrypt( $this->request->post['store_id'])));
			$bookmark_count = $this->model_catalog_product->getProductBookmarkCount(array('product_id'=>$product['product_id'],'store_id'=>$mcrypt->decrypt( $this->request->post['store_id'])));
			$review_count = $this->model_catalog_product->getProductReviewCount(array('product_id'=>$product['product_id'],'store_id'=>$mcrypt->decrypt( $this->request->post['store_id'])));

            $json['products'][] = array(
					'id'			=> $mcrypt->encrypt($product['product_id']),
					'name'			=> $mcrypt->encrypt(strtoupper($product['name'])), 
					'favourite'			=> $mcrypt->encrypt($product['favourite']), 
					'quantity'  	=> $mcrypt->encrypt(empty($product['quantity'])? 0:$product['quantity']),
					'mitra_quantity'  	=> $mcrypt->encrypt(empty($product['pd']['mitra_quantity'])? 0:$product['pd']['mitra_quantity']),
					'hstn'   		=> $mcrypt->encrypt(empty($product['HSTN'])? 0:$product['HSTN']),
					'reward'   		=> $mcrypt->encrypt(empty($points)? 0:$points),
					'description'	=> $mcrypt->encrypt("0"),
					'pirce'			=> $mcrypt->encrypt(($product['price'])),
					'wholesale_price'=> $mcrypt->encrypt(($product['wholesale_price'])),
					'href'			=> $mcrypt->encrypt($this->url->link('product/product', 'product_id=' . $product['product_id'])),
					'thumb'			=> $mcrypt->encrypt($product['image']),
					'special'		=> $mcrypt->encrypt($product['image']),
					'rating'		=> $mcrypt->encrypt($product['rating']), 
					'subtract'		=> $mcrypt->encrypt($product['subtract']),
					'tax'			=> $mcrypt->encrypt(($this->tax->getTax($product['price'], $product['tax_class_id']))),//
					'per_tax'       => $mcrypt->encrypt(json_encode($this->tax->getRates($product['price'], $product['tax_class_id']))),
					'category'		=> $this->request->get['category'],
					'subsidy'		=> $mcrypt->encrypt( empty($product['subsidy'])? 0:$product['subsidy']),
					'chemical'	=>	$mcrypt->encrypt( empty($product['model'])? 0:$product['model']),
					'sku'	=>	$mcrypt->encrypt( empty($product['sku'])? 0:$product['sku']),
					'bookmark'	=>	$mcrypt->encrypt( empty($product['bookmark'])? 0:$product['bookmark']),
					'bookmark_count'	=>	$mcrypt->encrypt( empty($product['bookmark_count'])? 0:$product['bookmark_count']),
					'review_count'	=>	$mcrypt->encrypt( empty($product['review_count'])? 0:$product['review_count'])
					);
		}//////////foreach end here
        	
		if ($this->debugIt) 
		{
            echo '<pre>';
            print_r($json);
            echo '</pre>';
		} 
		else 
		{
            //$log->write($json);
            $this->response->setOutput(json_encode($json));
		}
	}
	private function get_product_for_po($ppost,$gget)
	{
		$mcrypt=new MCrypt();	
        $json = array();
		$log =new Log("prdinv-po-".date('Y-m-d').".log");
        $log->write('products called');
		$this->load->model('catalog/product');
		if (isset($gget['category'])) 
		{
        	$category_id =$mcrypt->decrypt($gget['category']);
		} 
		else 
		{
            $category_id = 0;
		}
		$products = $this->model_catalog_product->getProductsPO(array(
            		'filter_category_id'        => $category_id,
            		
					'store_id'=>$mcrypt->decrypt( $ppost['store_id']),
					'for_store'=>'own'.$mcrypt->decrypt( $ppost['store_id'])
            ));
		$price = array();
		foreach ($products as $key => $row)
		{
			$price[$key] = $row['name'];
		}
		array_multisort($price, SORT_ASC, $products);
		//$log->write($products);
		$own_products=array();
		$this->adminmodel('setting/setting');
		$this->adminmodel('tool/image');
		$this->load->model('catalog/product');
        foreach ($products as $product) 
		{ 
            //$log->write("data products loop");
			
            if(empty($product))
            {                            
                continue;
            }
          
            //$log->write('array_key_exists');
            //$log->write(array_key_exists($product['product_id']));
            //$log->write(array_key_exists($own_products));
            //$log->write(array_key_exists($product['product_id'],$own_products));
            if(!empty($own_products)&&(array_key_exists($product['product_id'],$own_products)))
            {
                //$log->write('in if own_products is not empty and product_id in own_products for product_id: '.$product['product_id']);
                $product['price']=$own_products[$product['product_id']]['pd']['store_price'];
				$product['favourite']=$own_products[$product['product_id']]['pd']['favourite'];
            }
            else
            {
                //$log->write('in else own_products is not empty and product_id in own_products for product_id: '.$product['product_id']);
                if(empty($product['pd']['store_price'])||$product['pd']['store_price']==0.0000)
				{
                   // $log->write('in if store_price is empty or 0 for product_id: '.$product['product_id']);
                    $product['price']=$product['price'];
					$product['favourite']=$product['pd']['favourite'];
				}
				else
				{
                    //$log->write('in else store_price is empty or 0 for product_id: '.$product['product_id']);
                    $product['price']=$product['pd']['store_price'];
					$product['favourite']=$product['pd']['favourite'];
				}
            }
            if(empty($product['HSTN']))
            {
				$product['HSTN']="0000";
            }
            $this->adminmodel('pos/pos');
            if(!empty($own_products)&&(array_key_exists($product['product_id'],$own_products)))
            {
                $product['quantity']=$own_products[$product['product_id']]['pd']['quantity'];//$productprice['quantity'];
            }
            else
            {
                $product['quantity']=$product['pd']['quantity'];
            }
			//$log->write('updated price 1: '.$product['price']);
            if(empty($product['price']))
            {
                $product['price']=$product['price_tax'];
            }
			if(empty($product['favourite']))
			{						
				$product['favourite']=0;   
			}
			//$log->write('updated price 2: '.$product['price']);
			if(empty($product['price']))
			{
				$product['price']=0.0;
			}
			$points = $this->model_catalog_product->getProductReward(array('product_id'=>$product['product_id'],'store_id'=>$mcrypt->decrypt( $this->request->post['store_id'])));
                        
			$log->write('points');
                        
			$log->write($points);
			if(empty($points))
			{
				$points=0;
			}
			$log->write($points);
			//$log->write($product['image']);
			//$product['image']=urlencode($product['image']);
			//$log->write($product['image']);
			//$log->write('updated price 3: '.$product['price']);
			$log->write($product['image']);
            $product['image']=$product['image']?$this->model_tool_image->resize($product['image'], 180, 180):'view/image/pos/logo.png';
			$log->write($product['image']);
			$product['image']=str_replace(HTTPS_CATALOG,'',$product['image']);
			$log->write($product['image']);
			
            $bookmark_info = $this->model_catalog_product->getProductBookmark(array('product_id'=>$product['product_id'],'store_id'=>$mcrypt->decrypt( $this->request->post['store_id'])));
			$bookmark_count = $this->model_catalog_product->getProductBookmarkCount(array('product_id'=>$product['product_id'],'store_id'=>$mcrypt->decrypt( $this->request->post['store_id'])));
			$review_count = $this->model_catalog_product->getProductReviewCount(array('product_id'=>$product['product_id'],'store_id'=>$mcrypt->decrypt( $this->request->post['store_id'])));

            $json['products'][] = array(
					'id'			=> $mcrypt->encrypt($product['product_id']),
					'name'			=> $mcrypt->encrypt(strtoupper($product['name'])), 
					'favourite'			=> $mcrypt->encrypt($product['favourite']), 
					'quantity'  	=> $mcrypt->encrypt(empty($product['quantity'])? 0:$product['quantity']),
					'mitra_quantity'  	=> $mcrypt->encrypt(empty($product['pd']['mitra_quantity'])? 0:$product['pd']['mitra_quantity']),
					'hstn'   		=> $mcrypt->encrypt(empty($product['HSTN'])? 0:$product['HSTN']),
					'reward'   		=> $mcrypt->encrypt(empty($points)? 0:$points),
					'description'	=> $mcrypt->encrypt("0"),
					'pirce'			=> $mcrypt->encrypt(($product['price'])),
					'wholesale_price'=> $mcrypt->encrypt(($product['wholesale_price'])),
					'href'			=> $mcrypt->encrypt($this->url->link('product/product', 'product_id=' . $product['product_id'])),
					'thumb'			=> $mcrypt->encrypt($product['image']),
					'special'		=> $mcrypt->encrypt($product['image']),
					'rating'		=> $mcrypt->encrypt($product['rating']), 
					'subtract'		=> $mcrypt->encrypt($product['subtract']),
					'tax'			=> $mcrypt->encrypt(($this->tax->getTax($product['price'], $product['tax_class_id']))),//
					'per_tax'       => $mcrypt->encrypt(json_encode($this->tax->getRates($product['price'], $product['tax_class_id']))),
					'category'		=> $this->request->get['category'],
					'subsidy'		=> $mcrypt->encrypt( empty($product['subsidy'])? 0:$product['subsidy']),
					'chemical'	=>	$mcrypt->encrypt( empty($product['model'])? 0:$product['model']),
					'sku'	=>	$mcrypt->encrypt( empty($product['sku'])? 0:$product['sku']),
					'bookmark'	=>	$mcrypt->encrypt( empty($product['bookmark'])? 0:$product['bookmark']),
					'bookmark_count'	=>	$mcrypt->encrypt( empty($product['bookmark_count'])? 0:$product['bookmark_count']),
					'review_count'	=>	$mcrypt->encrypt( empty($product['review_count'])? 0:$product['review_count'])
					);
		}//////////foreach end here
        	
		if ($this->debugIt) 
		{
            echo '<pre>';
            print_r($json);
            echo '</pre>';
		} 
		else 
		{
			if(empty($json))
			{
				$json['products']=array();
				$log->write($json);
			}
            
            $this->response->setOutput(json_encode($json));
		}
	}
    public function products() 
    {
		$this->load->language('api/cart');
		$mcrypt=new MCrypt();	
        $json = array();
		$log =new Log("prdinv-pur-".date('Y-m-d').".log");
        $log->write('products called');
		$this->request->post['service_type']=$mcrypt->decrypt($this->request->post['service_type']);
		$this->request->post['page']=$mcrypt->decrypt($this->request->post['page']);
		$log->write($this->request->get);
		$log->write($this->request->post);
		if($this->request->post['service_type']=='catalogue')
		{
			$this->get_product_for_dashboard($this->request->post,$this->request->get);
			return;
		}
		if($this->request->post['service_type']=='purchase_request')
		{
			$this->get_product_for_po($this->request->post,$this->request->get);
			return;
		}
			
		$this->load->model('catalog/product');
		//$this->load->library('user');
		$log->write("data");
		//$this->session->data['user_id']=$mcrypt->decrypt( $this->request->post['username']);
        	//$this->user = new User($this->registry);
		$log->write("data re");
		if(isset($this->request->post['stype'])&&isset($this->request->post['store_emp']))
		{
			$this->config->set('config_store_id','0');

		}
        	else if(isset($this->request->post['stype']))
		{
            		$this->config->set('config_store_id',$mcrypt->decrypt($this->request->post['stype']));
            		$log->write("in stype");
		}
		else
		{
            		$this->config->set('config_store_id',$mcrypt->decrypt( $this->request->post['store_id']));
        	}
		$json = array('success' => true, 'products' => array());

	        if (isset($this->request->get['category'])) 
		{
        	    $category_id =$mcrypt->decrypt( $this->request->get['category']);
		} 
		else 
		{
            		$category_id = 0;
		}
        	$log->write($category_id);
		if (!empty($mcrypt->decrypt($this->request->post['page'])) )
		{
                	$page = $mcrypt->decrypt($this->request->post['page']);
        	} 
		else if (!empty($mcrypt->decrypt($this->request->get['page'])) )
		{
                	$page = $mcrypt->decrypt($this->request->get['page']);
        	} 
		else 
		{
                	$page = 1;
        	}
		$limit    = 20;
        	$offset = ($page-1)*$limit; 
		if(isset($this->request->post['stype']))
		{
			$this->config->set('config_store_id',0);
		}
		
		$log->write($mcrypt->decrypt($this->request->get['service_type']));
		if(isset($this->request->post['stype']))
		{
		$products = $this->model_catalog_product->getProducts(array(
            		'filter_category_id'        => $category_id,
			'quantity_check'=>0,
			'store_id'=>$mcrypt->decrypt( $this->request->post['store_id']),
			'for_store'=>$this->config->get('config_store_id')
		));
		}else
		{
			
			$products = $this->model_catalog_product->getProducts(array(
            		'filter_category_id'        => $category_id,
			'quantity_check'=>1,
			'store_id'=>$mcrypt->decrypt( $this->request->post['store_id']),
			'for_store'=>$this->config->get('config_store_id')
		));
		}
        	if(isset($this->request->post['stype']))
		{
            	$this->config->set('config_store_id',$mcrypt->decrypt( $this->request->post['store_id']));
            	$log->write("before call to own_products  in product/products");
            	$own_products = $this->model_catalog_product->getProducts(array(
            		'filter_category_id'        => $category_id,
            		'quantity_check'=>1,
			'store_id'=>$mcrypt->decrypt( $this->request->post['store_id']),
			'for_store'=>'own'.$mcrypt->decrypt( $this->request->post['store_id'])
            ));
        }
        	$log->write("data products");
		//$log->write($products);
		$price = array();
		foreach ($products as $key => $row)
		{
			$price[$key] = $row['name'];
		}
		array_multisort($price, SORT_ASC, $products);
		//$log->write($products);
		$this->adminmodel('setting/setting');
		$this->adminmodel('tool/image');
		$this->load->model('catalog/product');
        foreach ($products as $product) 
		{ 
            $log->write("data products loop");
			$log->write($product['name']);
			$log->write($product['price']);
            if(empty($product))
            {                            
                continue;
            }
          
            //$log->write('array_key_exists');
            //$log->write(array_key_exists($product['product_id']));
            //$log->write(array_key_exists($own_products));
            //$log->write(array_key_exists($product['product_id'],$own_products));
            if(!empty($own_products)&&(array_key_exists($product['product_id'],$own_products)))
            {
                //$log->write('in if own_products is not empty and product_id in own_products for product_id: '.$product['product_id']);
                $product['price']=$own_products[$product['product_id']]['pd']['store_price'];
				$product['favourite']=$own_products[$product['product_id']]['pd']['favourite'];
            }
            else
            {
                //$log->write('in else own_products is not empty and product_id in own_products for product_id: '.$product['product_id']);
                if(empty($product['pd']['store_price'])||$product['pd']['store_price']==0.0000)
				{
                   // $log->write('in if store_price is empty or 0 for product_id: '.$product['product_id']);
                    $product['price']=$product['price'];
					$product['favourite']=$product['pd']['favourite'];
				}
				else
				{
                    //$log->write('in else store_price is empty or 0 for product_id: '.$product['product_id']);
                    $product['price']=$product['pd']['store_price'];
					$product['favourite']=$product['pd']['favourite'];
				}
            }
			
            if(empty($product['HSTN']))
            {
				$product['HSTN']="0000";
            }
            $this->adminmodel('pos/pos');
            if(!empty($own_products)&&(array_key_exists($product['product_id'],$own_products)))
            {
                $product['quantity']=$own_products[$product['product_id']]['pd']['quantity'];//$productprice['quantity'];
            }
            else
            {
                $product['quantity']=$product['pd']['quantity'];
            }
			$log->write('updated price 1: '.$product['price']);
            if(empty($product['price']))
            {
                $product['price']=$product['price_tax'];
            }
			if(empty($product['favourite']))
			{						
				$product['favourite']=0;   
			}
			$log->write('updated price 2: '.$product['price']);
			if(empty($product['price']))
			{
				$product['price']=0.0;
			}
			$points = $this->model_catalog_product->getProductReward(array('product_id'=>$product['product_id'],'store_id'=>$mcrypt->decrypt( $this->request->post['store_id'])));
                        
			//$log->write('points');
                        
			//$log->write($points);
			if(empty($points))
			{
				$points=0;
			}
			//$log->write($points);
			//$product['image']=urlencode($product['image']);
			//$log->write($product['image']);
			//$log->write('updated price 3: '.$product['price']);
			//$log->write($product['image']);
            $product['image']=$product['image']?$this->model_tool_image->resize($product['image'], 180, 180):'view/image/pos/logo.png';
			//$log->write($product['image']);
			$product['image']=str_replace(HTTPS_CATALOG,'',$product['image']);
			//$log->write($product['image']);
			
            $bookmark_info = $this->model_catalog_product->getProductBookmark(array('product_id'=>$product['product_id'],'store_id'=>$mcrypt->decrypt( $this->request->post['store_id'])));
			$bookmark_count = $this->model_catalog_product->getProductBookmarkCount(array('product_id'=>$product['product_id'],'store_id'=>$mcrypt->decrypt( $this->request->post['store_id'])));
			$review_count = $this->model_catalog_product->getProductReviewCount(array('product_id'=>$product['product_id'],'store_id'=>$mcrypt->decrypt( $this->request->post['store_id'])));

            $json['products'][] = array(
					'id'			=> $mcrypt->encrypt($product['product_id']),
					'name'			=> $mcrypt->encrypt(strtoupper($product['name'])), 
					'favourite'			=> $mcrypt->encrypt($product['favourite']), 
					'quantity'  	=> $mcrypt->encrypt(empty($product['quantity'])? 0:$product['quantity']),
					'mitra_quantity'  	=> $mcrypt->encrypt(empty($product['pd']['mitra_quantity'])? 0:$product['pd']['mitra_quantity']),
					'hstn'   		=> $mcrypt->encrypt(empty($product['HSTN'])? 0:$product['HSTN']),
					'reward'   		=> $mcrypt->encrypt(empty($points)? 0:$points),
					'description'	=> $mcrypt->encrypt("0"),
					'pirce'			=> $mcrypt->encrypt(($product['price'])),
					'wholesale_price'=> $mcrypt->encrypt(($product['wholesale_price'])),
					'href'			=> $mcrypt->encrypt($this->url->link('product/product', 'product_id=' . $product['product_id'])),
					'thumb'			=> $mcrypt->encrypt($product['image']),
					'special'		=> $mcrypt->encrypt($product['image']),
					'rating'		=> $mcrypt->encrypt($product['rating']), 
					'subtract'		=> $mcrypt->encrypt($product['subtract']),
					
					'tax'			=> $mcrypt->encrypt(($this->tax->getTax($product['price'], $product['tax_class_id']))),//
					'per_tax'       => $mcrypt->encrypt(json_encode($this->tax->getRates($product['price'], $product['tax_class_id']))),
					'category'		=> $this->request->get['category'],
					'subsidy'		=> $mcrypt->encrypt( empty($product['subsidy'])? 0:$product['subsidy']),
					'chemical'	=>	$mcrypt->encrypt( empty($product['model'])? 0:$product['model']),
					'sku'	=>	$mcrypt->encrypt( empty($product['sku'])? 0:$product['sku']),
					'bookmark'	=>	$mcrypt->encrypt( empty($product['bookmark'])? 0:$product['bookmark']),
					'bookmark_count'	=>	$mcrypt->encrypt( empty($product['bookmark_count'])? 0:$product['bookmark_count']),
					'review_count'	=>	$mcrypt->encrypt( empty($product['review_count'])? 0:$product['review_count'])
					);
		}//////////foreach end here
        	
		if ($this->debugIt) 
		{
            echo '<pre>';
            print_r($json);
            echo '</pre>';
		} 
		else 
		{
            //$log->write($json);
            $this->response->setOutput(json_encode($json));
		}
    }
    /////////////////products function end here
    public function viewproducttemp()
    {
        $log=new Log("getproducttemp-".date('Y-m-d').".log");
		$mcrypt=new MCrypt();                    
		$json = array();               
		$this-> adminmodel('catalog/producttemp');
        $log->write($this->request->post);   
        $log->write($mcrypt->decrypt($this->request->post['store_id']));
		$log->write($mcrypt->decrypt($this->request->post['username']));
		$categories = $this->model_catalog_producttemp->getProductsRequest(array(
            'filter_parent'=> '1','filter_store'=>$mcrypt->decrypt($this->request->post['store_id']),
            'filter_role'=>$mcrypt->decrypt($this->request->post['rid']),
            'user_id' => $mcrypt->decrypt($this->request->post['username'])
            ));	
        foreach ($categories as $category_info) 
		{
            $sdata="";
             if(($category_info['status'])==3)
             {
                 $sdata="Already in system";
                 
             }
             else  if(($category_info['status'])==1)
               {
                   $sdata="Approved";
                  
               }
             else  if(($category_info['status'])==2)
               {
                   $sdata="Resubmit";
                   
               }
			   else  if(($category_info['status'])==0)
               {
                   $sdata="Pending";
                   
               }
            $json['products'][] = array(

					'id' => $mcrypt->encrypt($category_info['product_id']),
          
					'name'        =>$mcrypt->encrypt( $category_info['model']),
			
					'username' =>$mcrypt->encrypt($category_info['username'])	,
			
					'HSTN'	=>$mcrypt->encrypt($category_info['HSTN'])	,
			
					'sku'	=>$mcrypt->encrypt($category_info['sku'])	,
			
					'status'	=>	$mcrypt->encrypt($sdata	)							                    
				);
		
		}    
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json,JSON_UNESCAPED_UNICODE));
    }
	public function invproducts()  
    {
        $this->load->language('api/cart');
        $json = array();

		$mcrypt=new MCrypt();                    
		$log =new Log("invproducts-".date('Y-m-d').".log");
		$log->write('invproducts called ');
		$log->write($this->request->get);
		$log->write($this->request->post);
		$log->write($mcrypt->decrypt( $this->request->post['store_id']));
		$log->write($mcrypt->decrypt( $this->request->post['username']));
		$this->load->model('catalog/product');
		$this->load->library('user');
        
		$this->session->data['user_id']=$mcrypt->decrypt( $this->request->post['username']);
        $this->user = new User($this->registry);
		
        $this->config->set('config_store_id',$mcrypt->decrypt( $this->request->post['store_id']));
		$json = array('success' => true, 'products' => array());

		
		$products = $this->model_catalog_product->getInventoryProducts(
			array(
			'start'=> $mcrypt->decrypt( $this->request->post['start']),
			'limit'=> $mcrypt->decrypt($this->request->post['limit']),
			'service_type'=>($this->request->post['service_type']),
			'invtype' => $mcrypt->decrypt($this->request->post['inv_type'])
			)
		);
		
		
		$gettotal=$this->model_catalog_product->getTotalInventoryAmount(array('store_id'=>$mcrypt->decrypt( $this->request->post['store_id'])));
		$get_mitra_total=$this->model_catalog_product->getTotal_mitra_InventoryAmount(array('store_id'=>$mcrypt->decrypt( $this->request->post['store_id'])));
		$log->write('gettotal');
		$log->write($gettotal);
		
		$log->write('get_mitra_total');
		$log->write($get_mitra_total);
		
		$ttotal= number_format((float)$gettotal,2,'.','');// round($gettotal);
		$json['total']=$mcrypt->encrypt($ttotal);
		
		$mtotal=number_format((float)$get_mitra_total,2,'.','');//round($get_mitra_total);
		$json['mtotal']=$mcrypt->encrypt($mtotal);
		
		$log->write('ttotal');
		$log->write($ttotal);
		
		$log->write('mtotal');
		$log->write($mtotal);
		
		$this->request->post['action']=$mcrypt->decrypt( $this->request->post['action']);
		if($this->request->post['action']=='e')
		{ 
			$log->write('in if action is email');
			$log->write($products);
			$this->load->library('email');
			$email=new email($this->registry);
			$file_name="inventory_report_".date('dMy').'.csv';
			$fields = array(
				'Product id',
				'Name',
				'Quantity',
				'Mitra Quantity',
				'Price',
				'Tax',
				'Price With Tax',
				
				'Total Amount',
				'Total Mitra Amount'
				);
			$fdata=array();
			foreach ($products as $product) 
			{
				$log->write('in loop start ');
				if(empty($product))
				{    
					continue;
				}

				$log->write($product['price']);
				if(!empty($product['pd']['store_price']))
				{
					$product['price']=$product['pd']['store_price'];
				}
				else
				{
					$product['price']=$product['price'];
				}
				if(empty($product['pd']['quantity']))
				{
					$pquantity=0;
				}
				else
				{
					$pquantity=$product['pd']['quantity'];
				}
				if(empty($product['pd']['mitra_quantity']))
				{
					$mquantity=0;
				}
				else
				{
					$mquantity=$product['pd']['mitra_quantity'];
				}
				$price_with_tax=($product['price'])+(($this->tax->getTax($product['price'], $product['tax_class_id'])));
				$ttax=$this->tax->getTax($product['price'], $product['tax_class_id']);
				$fdata[] = array($product['product_id'],
					$product['name'],
					$pquantity, 
					$mquantity, 
					($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))+($this->tax->getTax($product['price'], $product['tax_class_id']))),
					$ttax,
					$price_with_tax,
					
					($pquantity*$price_with_tax), 
					($mquantity*$price_with_tax)
					
				);
				$log->write('in loop end');
			}
			$email->create_csv($file_name,$fields,$fdata);	
			$mail_subject="Inventory Report";
			
			$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear Team,
			<br/><br/>
			Please find attached file for Inventory Report.
			
			<br/><br/>
			This is computer generated email.Please do not reply to this email.
			<br/><br/>
			<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/><br/>
			Thanking you,
			<br/>
			IT Team
			<br/>
			AgriPOS
			
			<br/>
			<br/>
			<span  style='font-size:7.5pt;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
			</p>";
			$to=$mcrypt->decrypt( $this->request->post['store_id']);   
			$cc=array();
			$bcc=array('vipin.kumar@aspl.ind.in','hrishabh.gupta@aspl.ind.in','chetan.singh@aspl.ind.in');
			
			$file_path=DIR_UPLOAD.$file_name;
            $email->sendmail($mail_subject,$body,$to,$cc,$bcc,$file_path);
			$datatt=array();
			$json=array('status'=>1,'msg'=>'sent');
			$log->write("sent" );
			$log->write('return array');
			$log->write($json);
			$datatt['products'][]=$json;
			$this->response->setOutput(json_encode($datatt));
		}
		else
		{
			$log->write('in if action is view');
        foreach ($products as $product) 
        {
            if(empty($product))
            {    
                continue;
            }
	    $category_id=0;
		foreach($product['category_ids'] as $category_ids)
		{
			if($category_ids!=44)
			{
				$category_id=$category_ids;
			}
		}
            if ($product['image']) 
            {
                $image = $product['image'];
            } 
            else 
            {
                $image = false;
		    }

			if ((float)$product['special']) 
            {
				$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
			}
            else 
            {
                $special = false;
			}

            $log->write($product['price']);
            if(!empty($product['pd']['store_price']))
            {
                $product['price']=$product['pd']['store_price'];
            }
            else
            {
                $product['price']=$product['price'];
            }
			if(empty($product['pd']['quantity']))
			{
                $pquantity=0;
			}
			else
			{
                $pquantity=$product['pd']['quantity'];
			}
			if(empty($product['pd']['mitra_quantity']))
			{
                $mquantity=0;
			}
			else
			{
                $mquantity=$product['pd']['mitra_quantity'];
			}
			$price_with_tax=($product['price'])+(($this->tax->getTax($product['price'], $product['tax_class_id'])));
			$json['products'][] = array(
					'id'			=> $mcrypt->encrypt($product['product_id']),
					'name'			=> $mcrypt->encrypt($product['name']),
					'quantity'		=> $mcrypt->encrypt($pquantity), 
					'mitra_quantity'		=> $mcrypt->encrypt($mquantity), 
					'fquantity'		=> $mcrypt->encrypt($product['fquantity']), 
					'description'	=> $mcrypt->encrypt($product['description']),
					'pirce'			=> $mcrypt->encrypt( ($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))+($this->tax->getTax($product['price'], $product['tax_class_id'])))),
					'href'			=> $mcrypt->encrypt($this->url->link('product/product', 'product_id=' . $product['product_id'])),
					'thumb'			=> $mcrypt->encrypt($image),
					'special'		=> $mcrypt->encrypt($special),
					'rating'		=> $mcrypt->encrypt($product['rating']),
					'sku'		=> $mcrypt->encrypt($product['sku']),
					'category_id'		=> $mcrypt->encrypt($category_id),
					'pricewithtax'  => $mcrypt->encrypt($price_with_tax),
					'tax'			=> $mcrypt->encrypt(($this->tax->getTax($product['price'], $product['tax_class_id'])))
				);
				
			
        }
		$count=count($products);//$this->model_catalog_product->getTotalQntyProducts(array());//
		$log->write('return totalcount');
		$log->write($count);
		$json['listcount']=$mcrypt->encrypt($count);//$this->model_catalog_product->getTotalQntyProducts(array()));
        if ($this->debugIt) 
        {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
        } 
        else 
        {
			$this->response->setOutput(json_encode($json));
        }
		}
	}
	public function productdetail()
	{
		$log =new Log("prd-detail-".date('Y-m-d').".log");
		$mcrypt=new MCrypt();
		$this->load->model('account/activity');
		
		if (isset($this->request->get['product_id'])) 
		{
			$product_id = (int)$this->request->get['product_id'];
		} 
		else 
		{
			$product_id = 0;
		}
		$log->write($product_id);
		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) 
		{
			$url = '';
			$this->load->model('catalog/review');
			//$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);
			$data['description']=$product_info['description'];
			
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) 
			{
				$data['price'] = ($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
			} 
			else 
			{
				$data['price'] = false;
			}

			$data['price_formatted']=	($this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'))));
			$data['id'] = (int)$this->request->get['product_id'];
			$data['remote_id'] = (int)$this->request->get['product_id'];
			$data['brand'] = $product_info['manufacturer'];
			$data['category'] =	$this->request->get['category_id'];
			$data['discount_price']="0";
			$data['discount_price_formated'] = '0';
			$data['currency'] ='INR';
			$data['code']='1';	
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['name'] = (empty($product_info['meta_hindi'])?$product_info['name']:$product_info['meta_hindi']);
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['chemical'] =( empty($product_info['model'])? 0:$product_info['model']);
			$data['sku']	=( empty($product_info['sku'])? 0:$product_info['sku']);
			if ($product_info['quantity'] <= 0) 
			{
				$data['stock'] = $product_info['stock_status'];

			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}

			$this->load->model('tool/image');

			if ($product_info['image']) {
				$data['url'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
			} else {
				$data['url'] = '';
			}

			if ($product_info['image']) {
				$data['main_image'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
			} else {
				$data['main_image'] = '';
			}

			$data['main_image_high_res']	= $data['main_image'];
			$data['images'] = array();
			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);
			foreach ($results as $result) {
				$data['images'][] = array(
					 $this->model_tool_image->resize($result['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
					 $this->model_tool_image->resize($result['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
				);
			}
			
			$datasize=array('id'=>"1","remote_id"=>"1",value=>"1");
			array_push($datasize,array('id'=>"2","remote_id"=>"2",value=>"2"));
			$data['variants'][] = array(
							'id'=>"1",
							'color'=>array('id'=>"1","remote_id"=>"1",value=>"1",code=>"1",img=>"1") ,
							'size'=> $datasize,
							'images'=>$data['images'],
							'code'=>"1",
							'related'=>array()
							);
		}
		//end check
		$this->response->setOutput(json_encode($data,JSON_UNESCAPED_UNICODE));
	}
	public function manufacturers()
    {
        $this->language->load('api/cart');
        $json = array();
        $log=new Log("manufacturers-".date('Y-m-d').".log");
        $log->write($this->request->post);
		$mcrypt=new MCrypt();
          
        $this-> adminmodel('catalog/manufacturer');
        $manufacturers = $this->model_catalog_manufacturer->getManufacturers()->rows;
        $this-> adminmodel('tool/image');
		$json['manufacturers'] = array();
		
		foreach ($manufacturers as $manufactur) 
		{
			$prdsquery = $this->model_catalog_manufacturer->getManufacturerProduct($manufactur['manufacturer_id']);
			$log->write($manufactur['manufacturer_id']);
			$log->write($manufactur['name']);
			$log->write($prdsquery->num_rows);
			if($prdsquery->num_rows>0)
			{
				if(!empty($manufactur['image']))
				{
					$image=$manufactur['image']?$this->model_tool_image->resize($manufactur['image'], 180, 180):'view/image/pos/logo.png';
				$image=str_replace('HTTPS_CATALOG','',$image);
				}
				else
				{
					$image='';
				}
				$manufactur['name']=str_replace('&amp;','&',$manufactur['name']);
				$json['products'][] = array(
                                    'category_id' => $mcrypt->encrypt($manufactur['manufacturer_id']),
                                    'image'       =>$mcrypt->encrypt($image),
                                    'name'        =>$mcrypt->encrypt( $manufactur['name']),
                                    );
			}
		}
		
        $log->write($json); 
                	                                                          
        if ($this->debugIt) 
        {
            echo '<pre>';
            print_r($json);
            echo '</pre>';
		}
        else 
        {
            $this->response->setOutput(json_encode($json));
		}        
    }
	public function Categories()
    {
        $this->language->load('api/cart');
        $json = array();
        $log=new Log("category-".date('Y-m-d').".log");
        $log->write($this->request->post);
		$mcrypt=new MCrypt();
          
        $this-> adminmodel('pos/pos');
        //$this-> adminmodel('setting/store');
        $this-> adminmodel('tool/image');
        $this->request->post['store_id']=$mcrypt->decrypt($this->request->post['store_id']);
        $log->write($this->request->post);
        $this->config->set('config_store_id',$this->request->post['store_id']);
        if(isset($this->request->post['store_id'])&&isset($this->request->post['store_emp']))
        {
            $categories = $this->model_pos_pos->getTopStoreCategories('0');
		}

		else if(isset($this->request->post['store_id'])&&(!empty($this->request->post['store_id'])))
		{
            $log->write("in if");
            $categories = $this->model_pos_pos->getTopStoreCategories($this->request->post['store_id']);
		}
        else
        {
            $log->write("in else");
            $categories = $this->model_pos_pos->getTopCategories();
        }
		//$log->write('after getting data from model');
        //$log->write($categories);

		$json['categories'] = array();
		
		foreach ($categories as $category_info) 
		{
			$image=$category_info['image']?$this->model_tool_image->resize($category_info['image'], 180, 180):'view/image/pos/logo.png';
			$image=str_replace('HTTPS_CATALOG','',$image);
            $json['categories'][] = array(
                                    'category_id' => $mcrypt->encrypt($category_info['category_id']),
                                    'image'       =>$mcrypt->encrypt($image),
                                    'name'        =>$mcrypt->encrypt( $category_info['name']),
                                    );
		}
		$this->session->data['user_id']=$mcrypt->decrypt($this->request->post['username']);
        $this->load->library('user');
        $this->user = new User($this->registry);
        $balance = 0;//$this->model_pos_pos->get_user_balance($this->user->getId());

        $json['cash'] =$mcrypt->encrypt( $this->currency->format(0));
        $json['card'] =$mcrypt->encrypt( $this->currency->format(0));

        $json['hold_carts'] =$mcrypt->encrypt("");// $this->model_pos_pos->get_hold_cart_list_user("1");
		$json['hold_cr'] =$mcrypt->encrypt("Live");
		$json['systype'] =$mcrypt->encrypt("System");
		$json['headoffice'] =$mcrypt->encrypt($this->config->get('config_head_office'));
        $json['storename']=$mcrypt->encrypt($this->config->get('config_name'));
		$log->write('store name');
		$log->write($this->config->get('config_name'));
		
		$json['storeaddress']= $mcrypt->encrypt($this->config->get('config_address'));
		$json['geocode']=$mcrypt->encrypt($this->config->get('config_geocode'));
        $json['storestatus']=$mcrypt->encrypt($this->config->get('config_storestatus'));
        $json['storecin']=$mcrypt->encrypt($this->config->get('config_cin'));
        $json['storetin']=$mcrypt->encrypt($this->config->get('config_tin'));
        $json['storegst']=$mcrypt->encrypt($this->config->get('config_gstn'));
        $json['storemsmfid']=$mcrypt->encrypt($this->config->get('config_MSMFID'));
        $json['storetype']=$mcrypt->encrypt($this->config->get('config_storetype_name'));
		$json['storetypeid']=$mcrypt->encrypt($this->config->get('config_storetype'));
		$json['printer_status']=($this->config->get('config_printer'));
		if($json['printer_status']=='')
		{
		
			$json['printer_status']=0;
		}
		$log->write($this->config->get('config_gstn'));
        $log->write($json); 
                	                                                          
        if ($this->debugIt) 
        {
            echo '<pre>';
            print_r($json);
            echo '</pre>';
		}
        else 
        {
            $this->response->setOutput(json_encode($json));
		}        
    }

	public function storemenu()
    {
        $log=new Log("getmenu-".date('Y-m-d').".log");
	$mcrypt=new MCrypt();    
	$this->language->load('api/cart');
                
	$json = array();                    
                    
	$this-> adminmodel('catalog/storemenu');
                    
	$this-> adminmodel('setting/store');
                    
	$this-> adminmodel('tool/image');
         $log->write($this->request->post);            
	//$this->load->model('pos/pos');
                    
	//get categories 
        $log->write($mcrypt->decrypt($this->request->post['store_id']));

	$categories = $this->model_catalog_storemenu->getusermenu(array(
            'filter_parent'=> '1','filter_store'=>$mcrypt->decrypt($this->request->post['store_id']),
            'filter_role'=>$mcrypt->decrypt($this->request->post['rid']),
            'user_id' => $mcrypt->decrypt($this->request->post['username']),
            'menutype'=>0
            ));
	//$mcrypt->decrypt($this->request->post['username'])	 
	
		
	$json['navigation'] = array();
		
		
	foreach ($categories as $category_id) 
	{
                $getCategorydetail=  $this->model_catalog_storemenu->getCategoryName($category_id);
		//print_r($getCategorydetail);exit;
                $subcategories = $this->model_catalog_storemenu->getusermenu(array('user_id' => $mcrypt->decrypt($this->request->post['username']),'menutype'=>1,'parent_id'=>$category_id));
                $subcategoriesData=array();
                foreach($subcategories as $subcategory)
                {
                   $getSubCategorydetail=  $this->model_catalog_storemenu->getCategoryName($subcategory);
                   $billtype=0;
			//Open Billing
                   if(strtolower($getSubCategorydetail['name'])==strtolower("Product Selection"))
                   {
                       $billtype=1;
                   }
                   $subcategoriesData[]=array(
                       'id' => ($subcategory),
          
					'name' =>(str_replace('&amp;','&', $getSubCategorydetail['name'])),
			
					'original_id' =>($subcategory)	,
			
					'image'	=>($getSubCategorydetail['image'])	,
			
					'tab'	=>($getSubCategorydetail['category_description'][1]['meta_title'])	,
			
					'mob'	=>($getSubCategorydetail['category_description'][1]['meta_keyword'])	,
                                        'billtype' => $billtype    
			
                   );
                }
                $json['navigation'][] = array(

					'id' => ($category_id),
          
					'name' =>( str_replace('&amp;','&',$getCategorydetail['name'])),
			
					'original_id' =>($category_id)	,
			
					'image'	=>($getCategorydetail['image'])	,
			
					'tab'	=>($getCategorydetail['category_description'][1]['meta_title'])	,
			
					'mob'	=>($getCategorydetail['category_description'][1]['meta_keyword'])	,
			
					'children'   => $subcategoriesData
                    
					);
		
	} 
	$log->write('return data');	
	$log->write($json);
	if ($this->debugIt) 
	{
			
		echo '<pre>';
			
		print_r($json);
			
		echo '</pre>';
		
	} 
		
	else 
	{
			
		$this->response->addHeader('Content-Type: application/json');
			
		$this->response->setOutput(json_encode($json,JSON_UNESCAPED_UNICODE));
		
	}        
    
}
//end storemenu

    
    public function getstorecr()
	{	
		$json = array();
		$log=new Log("storecr.log");
		$log->write($this->request->post);
		 $mcrypt=new MCrypt();

				//log to system table
				$this->load->model('account/activity');
				$activity_data = array(
					'customer_id' => $mcrypt->decrypt($this->request->post['username']),
					'data'        => json_encode($this->request->post),
				);

				$this->model_account_activity->addActivity('getStoreCR', $activity_data);
	                    $this-> adminmodel('pos/pos');
				$this->request->post['store_id']=$mcrypt->decrypt($this->request->post['store_id']);
			$json['hold_cr'] =$mcrypt->encrypt("Live");//$this->model_pos_pos->get_store_balance($this->request->post['store_id']));
		return $this->response->setOutput(json_encode($json));
	}



	/*farmer  data */
    public function CategoriesFarmer()
	{
        $this->language->load('api/cart');
        $json = array();
		$this-> adminmodel('pos/pos');
        $this-> adminmodel('setting/store');
        $this-> adminmodel('tool/image');
		$categories = $this->model_pos_pos->getTopCategories();
		$mcrypt=new MCrypt();
		$json['navigation'] = array();
		

		$datasub=array();           
		foreach ($categories as $category_info) 
		{
					$datasub[]=array('id'=>($category_info['category_id']),
						 'type'=>'category',
						'name'=>(empty($category_info['meta_hindi'])?$category_info['name']:$category_info['meta_hindi']),
						'original_id' =>($category_info['category_id'])	

							);
		}
		$json['navigation'][] = array(
                        'id' => ("1"),
                        'name'        =>("इनपुट"),
			'original_id' =>("1")	,
			'children'   =>($datasub )
			);
        if ($this->debugIt) 
		{
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} 
		else 
		{
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json,JSON_UNESCAPED_UNICODE));
		}        
    }
	public function productsfarmer() 
	{
		return $this->sproductsinv();
		
		$this->load->language('api/cart');
		$json = array();
		
		$log =new Log("prdinv-".date('Y-m-d').".log");
		$log->write('productsfarmer called ');
		$log->write($this->request->get);
		$log->write($this->request->post);
		$mcrypt=new MCrypt();
		$this->load->model('catalog/product');

		$this->config->set('config_store_id','0');//( $this->request->post['store_id']));
		$json = array( 'metadata' => array());

		/*check category id parameter*/
		if (isset($this->request->get['category'])) {
			$category_id =		 ( $this->request->get['category']);
		} else {
			$category_id = 0;
		}

		if (isset($this->request->get['search'])) 
			{
				$filter_name =( $this->request->get['search']);
			} 
			else 
			{
				$filter_name = '';
			}
		$products = $this->model_catalog_product->getProducts(array(
			'filter_category_id'        => $category_id,
			'filter_name'=>$filter_name
		));



		foreach ($products as $product) 
		{
			if(empty($product['manufacturer']))
			{
				$manufacturer=$this->model_catalog_product->getProductmanufacturer(array('manufacturer_id'=>$product['manufacturer']));
			}
			else
			{
				$manufacturer=$product['manufacturer'];
			}

			if ($product['image']) {
				$image = $product['image'];
			} else {
				$image = false;
			}

			if ((float)$product['special']) {
				$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$special = false;
			}
			$json['metadata']['links']=array('first'=>'1','last'=>'1','next'=>'1','prev'=>'1','self'=>'1');
			$json['metadata']['sorting']="";
			$json['metadata']['records_count']="3";
//records ['metadata'] ($this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))))
			$json['records'][] = array(
					'id'			=> ($product['product_id']),
					'remote_id'		=>($product['product_id']),
					'name'			=> (empty($product['meta_hindi'])?$product['name']:$product['meta_hindi']),
					'description'	=> ($product['description']),
					'price'			=> $product['price'] ,
					'hsn'			=> $product['HSTN'] ,
					'sku'			=> $product['sku'] ,
					'manufacturer_id'			=> $product['manufacturer_id'],
					'manufacturer_name'			=> $manufacturer ,
					'price_formatted'=>	($this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')))),
					'category'=>	$category_id,
					'brand' => 'Unnati',	
					'discount_price' => '11',
					'discount_price_formated' => '11',
					'currency' =>'INR',
					'code'=>'1',	
					'url'			=> ($this->url->link('product/product', 'product_id=' . $product['product_id'])),
					'main_image'			=> "https://unnati.world/shop/image/". ($image),
					'main_image_high_res'	=> "https://unnati.world/shop/image/".($image),
					'images' => array(),
					'variants'=>array(),	
					'special'		=> ($special),
					'rating'		=> ($product['rating']),
					'tax'			=> (round($this->tax->getTax($product['price'], $product['tax_class_id']),2, PHP_ROUND_HALF_UP))
			);
		}
        
		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			//$this->response->setOutput(json_encode($json));
			$this->response->setOutput(json_encode($json,JSON_UNESCAPED_UNICODE));
		}
	}


//circle
//circle inventory
public function invwebloanproducts() 
{
            $this->load->language('api/cart');
                $json = array();

		 $mcrypt=new MCrypt();                    {
		$log =new Log("loanprdinv.log");

		$log->write($this->request->get);
		$log->write($this->request->post);


//log to system table
				$this->load->model('account/activity');
				$activity_data = array(
					'customer_id' => $mcrypt->decrypt($this->request->post['username']),
					'data'        => $this->request->post
				);

				$this->model_account_activity->addActivity('Loan inventory', $activity_data);

		$this->load->model('catalog/product');
		$this->load->library('user');
$log->write("data");
		$this->session->data['user_id']=$mcrypt->decrypt( $this->request->post['username']);
                $this->user = new User($this->registry);
$log->write("data re");
$log->write("data");
	
		//get store id
		$this->config->set('config_store_id',$mcrypt->decrypt( $this->request->post['store_id']));
		$json = array('success' => true, 'products' => array());


		

		$products = $this->model_catalog_product->getloanProducts(array(
			'start'=> $mcrypt->decrypt( $this->request->post['start']),
			'limit'=> $mcrypt->decrypt($this->request->post['limit']),
			'user'=> $mcrypt->decrypt($this->request->post['username'])

		));
		$json['listcount']=$mcrypt->encrypt(sizeof($products));

		$log->write($products);
		$json['total']=$mcrypt->encrypt(round($this->model_catalog_product->getTotalLoanInventoryAmount($mcrypt->decrypt( $this->request->post['store_id'])) ));

		foreach ($products as $product) {



					$log->write($product);


			$json['products'][] = array(
					'id'			=> ($product[0]['product_id']),
					'name'			=> ($product[0]['name']),
					'quantity'		=> ($product[0]['quantity']),
					'price'			=> (str_replace("Rs.","",$product[0]['price'])+str_replace("Rs.","",$product[0]['tax']) ),
					'tax'			=> ($product[0]['tax'])
			);
		}
        }
		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
		$log->write($json);
			$this->response->setOutput(json_encode($json));
		}
	}

//circle inventory
public function invloanproducts() {
            $this->load->language('api/cart');
                $json = array();

		 $mcrypt=new MCrypt();                    {
		$log =new Log("loanprdinv-".date('Y-m-d').".log");

		$log->write($this->request->get);
		$log->write($this->request->post);
		$log->write($mcrypt->decrypt($this->request->post['username']));
	$log->write($mcrypt->decrypt($this->request->post['circle_code']));



//log to system table
				$this->load->model('account/activity');
				$activity_data = array(
					'customer_id' => $mcrypt->decrypt($this->request->post['username']),
					'data'        => $this->request->post
				);

				$this->model_account_activity->addActivity('Loan inventory', $activity_data);

		$this->load->model('catalog/product');
		$this->load->library('user');
$log->write("data");
		$this->session->data['user_id']=$mcrypt->decrypt( $this->request->post['username']);
                $this->user = new User($this->registry);
$log->write("data re");
$log->write("data");
	
		//get store id
		$this->config->set('config_store_id',$mcrypt->decrypt( $this->request->post['store_id']));
		$json = array('success' => true, 'products' => array());


		

		$products = $this->model_catalog_product->getloanProducts(array(
			'start'=> $mcrypt->decrypt( $this->request->post['start']),
			'limit'=> $mcrypt->decrypt($this->request->post['limit']),
			'user'=> $mcrypt->decrypt($this->request->post['circle_code'])

		));
		$json['listcount']=$mcrypt->encrypt(sizeof($products));

		$log->write($products);
		$json['total']=$mcrypt->encrypt(round($this->model_catalog_product->getTotalLoanInventoryAmount($mcrypt->decrypt( $this->request->post['store_id'])) ));

		foreach ($products as $product) {



					$log->write($product);


			$json['products'][] = array(
					'id'			=> $mcrypt->encrypt($product[0]['product_id']),
					'name'			=> $mcrypt->encrypt($product[0]['name']),
					'quantity'		=> $mcrypt->encrypt($product[0]['quantity']),
					'pirce'			=> $mcrypt->encrypt(str_replace("Rs.","",$product[0]['price'])+str_replace("Rs.","",$product[0]['tax']) ),
					'tax'			=> $mcrypt->encrypt($product[0]['tax'])
			);
		}
        }
		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
		$log->write($json);
			$this->response->setOutput(json_encode($json));
		}
	}

public function productsrelatedtocrop() {

            $this->load->language('api/cart');
                $json = array();

                    {
		$log =new Log("prdgen-".date('Y-m-d').".log");
		$log->write($this->request->get);
		$log->write($this->request->post);
		 $mcrypt=new MCrypt();
		$this->adminmodel('catalog/product');
		//		$this->load->library('user');
		$log->write("data");
		//		$this->session->data['user_id']=( $this->request->post['username']);
  		//              $this->user = new User($this->registry);
		
		$log->write("data");
	
		//get store id
		$this->config->set('config_store_id','0');//( $this->request->post['store_id']));
		$json = array( 'metadata' => array());

		$log->write($this->request->get);

		/*check category id parameter*/
		if (isset($this->request->get['crop_id'])) 
		{
			$crop_id =( $this->request->get['crop_id']);
		} 
		else 
		{
			$crop_id = 0;
		}
		if (isset($this->request->get['category'])) 
		{
			$category_id =	( $this->request->get['category']);
		} 
		else 
		{
			$category_id = 0;
		}
		if (!isset($this->request->get['search']))
		{
		
			$log->write("in catgory");
		
			if($crop_id=='true')
			{
				$log->write('if crop_id is true');
				$products = $this->model_catalog_product->getProductsByCategoryId($category_id);
			}
			else
			{
				$log->write('if crop_id is not true');
				$products = $this->model_catalog_product->getProductsRelatedToCrop(array(
					'filter_crop_id'        =>$crop_id ///'12'  // 
				));
			}
		

		}
		else
		{
					$log->write("in serach");
			//search product by name filter_name
					$products = $this->model_catalog_product->getProductsRelatedToCrop(array(
			'filter_name'        => $this->request->get['search'],
                                          'filter_crop_id'        =>$crop_id ///'12'  // 
		));
				
			}
                           //echo "here";
                            $count_pr=count($products);
		foreach ($products as $product) {

			if ($product['image']) {
				$image = $product['image'];
			} else {
				$image = false;
			}

			if ((float)$product['special']) {
				$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$special = false;
			}
			$json['metadata']['links']=array('first'=>($this->url->link('product/product', 'product_id=' . $product['product_id'])),'last'=>($this->url->link('product/product', 'product_id=' . $product['product_id'])),'next'=>($this->url->link('product/product', 'product_id=' . $product['product_id'])),'prev'=>($this->url->link('product/product', 'product_id=' . $product['product_id'])),'self'=>($this->url->link('product/product', 'product_id=' . $product['product_id'])));
			$json['metadata']['sorting']="";
			$json['metadata']['records_count']=$count_pr;
                                          //records ['metadata'] ($this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))))
			$json['records'][] = array(
					'id'			=> ($product['product_id']),
					'remote_id'		=>($product['product_id']),
					'name'			=> (empty($product['meta_hindi'])?$product['name']:$product['meta_hindi']),
					'description'	=> ($product['description']),
					'price'			=> $product['price'] ,
					'price_formatted'=>	($this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')))),
					'category'=>	$category_id,
					'brand' => 'Unnati',	
					'discount_price' => '11',
					'discount_price_formated' => '11',
					'currency' =>'INR',
					'code'=>'1',	
					'url'			=> ($this->url->link('product/product', 'product_id=' . $product['product_id'])),
					'main_image'			=> "https://unnati.world/shop/image/". ($image),
					'main_image_high_res'	=> "https://unnati.world/shop/image/".($image),
					'images' => array(),
					'variants'=>array(),	
					'special'		=> ($special),
					'rating'		=> ($product['rating']),
					'tax'			=> (round($this->tax->getTax($product['price'], $product['tax_class_id']),2, PHP_ROUND_HALF_UP))
			);
		}
        }
		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}
	public function getsubsidycategory()
	{   
        $json = array();
        $log=new Log("getsubsidycategory-".date('Y-m-d').".log");
        $log->write($this->request->post);
         $mcrypt=new MCrypt();

                $filter_data = array(
                    'store_id' => $mcrypt->decrypt($this->request->post['store_id'])
                );
                $this-> adminmodel('subsidy/subsidy');

                $cat_data=$this->model_subsidy_subsidy->getsubsidycategory($filter_data);
	  $log->write($cat_data);
                $data=array();
                foreach($cat_data as $cat_dat)
                {   
                    $cat_product_data=$this->model_subsidy_subsidy->getsubsidycategory_products($filter_data,$cat_dat['category_id']);
                    $product=array();
                    foreach($cat_product_data as $cat_product_da)
                    {
                        $product[]=array(
                        'product_id'=>$mcrypt->encrypt($cat_product_da['product_id']),
                        'product_name'=>$mcrypt->encrypt($cat_product_da['product_name']),
                        'subsidy'=>$mcrypt->encrypt($cat_product_da['subsidy'])
                        );
                    }
                    $data['category'][]=array(
                        'category_id'=>$mcrypt->encrypt($cat_dat['category_id']),
                        'category_name'=>$mcrypt->encrypt($cat_dat['category_name']),
                        'product'=>$product
                        );
                    //print_r($cat_dat);
                }
                 //print_r($data);
                 //$json['hold_cr'] =$mcrypt->encrypt($this->model_pos_pos->get_store_balance($this->request->post['store_id']));
                 $log->write($data);
        return $this->response->setOutput(json_encode($data));
	}
	public function categories_with_product()
	{
            $this->language->load('api/cart');
            $json = array();
            $log=new Log("categories_with_product-".date('y-m-d').".log");
            $log->write($this->request->post);
            $mcrypt=new MCrypt();

            $this-> adminmodel('pos/pos');
                   
            $this-> adminmodel('tool/image');
            $this-> adminmodel('catalog/product');
            $this->request->post['store_id']=$mcrypt->decrypt($this->request->post['store_id']);
            $log->write($this->request->post);

            if(isset($this->request->post['store_id']))
            {
                $categories = $this->model_pos_pos->getTopStoreCategories($this->request->post['store_id']);
            }
            else
            {
                $categories = $this->model_pos_pos->getTopStoreCategories('0');
            }
            $log->write($categories);

            $json['categories'] = array();
           
            foreach ($categories as $category_info)
            {
                    $products_array=array();
                    $products = $this->model_catalog_product->getProductsByCategoryId($category_info['category_id']);
                    $log->write($products);
                    foreach ($products as $product)
                    {
                        //print_r($product);
                   
                    if ($product['image'])
                    {
                        $image = $product['image'];
                    }
                    else
                    {
                        $image = false;
                    }

                    if ((float)$product['special'])
                    {
                        $special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
                    }
                    else
                    {
                        $special = false;
                    }
                    $log->write($product['price']);
                    if(empty($product['price'])||$product['price']==0.0000)
                    {
                        $product['price']=$product['sprice'];
                    }

                        $products_array[] = array(
                            'id'            => $mcrypt->encrypt($product['product_id']),
                            'name'            => $mcrypt->encrypt($product['name']),
                            'quantity'        => $mcrypt->encrypt($product['quantity']),
                            'description'    => $mcrypt->encrypt("0"),
                            'pirce'            => $mcrypt->encrypt($this->currency->format($product['price'])),
                            'href'            => $mcrypt->encrypt($this->url->link('product/product', 'product_id=' . $product['product_id'])),
                            'thumb'            => $mcrypt->encrypt($image),
                            'special'        => $mcrypt->encrypt($special),
                            'rating'        => $mcrypt->encrypt($product['rating']),
                            'tax'            => $mcrypt->encrypt(($this->tax->getTax($product['price'], $product['tax_class_id']))),
                            'category'        => $this->request->get['category'],
                            'subsidy'        => $mcrypt->encrypt( empty($product['subsidy'])? 0:$product['subsidy'])

                        );
                    }
           
                    $json['categories'][] = array(
                        'category_id' => $mcrypt->encrypt($category_info['category_id']),
                        'image'       =>$mcrypt->encrypt( $category_info['image']?$this->model_tool_image->resize($category_info['image'], 70, 70):'view/image/pos/logo.png'),
                        'name'        =>$mcrypt->encrypt( $category_info['name']),
                        'products'=> $products_array
                    );
            }
       
            $log->write($json);
               
            $this->response->setOutput(json_encode($json));
               
	}

public function gettimelinestatus()
{
     $log =new Log("deliverstatus-".date('Y-m-d').".log");
     $mcrypt=new MCrypt();
     
     $log->write($this->request->post );
     
     if (isset($this->request->post['product_id'])) {
        $product_id = (int) $mcrypt->decrypt($this->request->post['product_id']);
        } else {
             $product_id = 0;
        }
                  
         $log->write($product_id );
         $query=$this->db->query("select","oc_product_temp",'','','',array('product_id'=>(int)$product_id));
          $log->write($query->rows );
         foreach($query->rows as $result){
             $log->write("in" );
             $log->write(unserialize($result['remark'])['comments']);       
             $sdata="";
             if($result['status']==3)
             {
                 $sdata="Already in system";
                 $result['status']="";
             }
               if($result['status']==1)
               {
                   $sdata="Approved";
                   $result['status']="";
               }
               if($result['status']==2)
               {
                   $sdata="Resubmit";
                   $result['status']="Active";
               }
                $json['mDataList'][]=array(
                 'mMessage'=>unserialize($result['remark'])['comments']." (".$sdata.")",
                 'mDate'=> date('d-m-Y h:i:s', $result['date_modified']->sec),
                    'mStatus'=>$result['status'],
                  );
         }
                    
       $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json,JSON_UNESCAPED_UNICODE));
    }

}
