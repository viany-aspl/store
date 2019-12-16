<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
error_reporting(1);
class ControllermposDashboard extends Controller 
{
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
	public function productdetail()
	{
		$log =new Log("prd-detail-".date('Y-m-d').".log");
		$mcrypt=new MCrypt();
		$this->load->model('account/activity');
		$this->load->model('catalog/product');
		$this->adminmodel('peer/peer');
		//$this->request->get['product_id']=$mcrypt->encrypt(153);
		//$this->request->get['store_id']=$mcrypt->encrypt(170);
		
		$data['prd_id']=$this->request->get['product_id'];
		$data['str_id']=$this->request->get['store_id'];
		$keys = array(
		'product_id',
		'view',
		'store_id'
		);
		
		foreach ($keys as $key) 		
		{
            $this->request->get[$key] =$mcrypt->decrypt($this->request->get[$key]) ;    
		}
       
		if (!empty(($this->request->get['product_id'])) )
		{
			$product_id = (int)$this->request->get['product_id'];
		} 
		else 
		{
			$product_id = 153;
		}
		if (!empty(($this->request->get['store_id'])) )
		{
			$store_id = (int)$this->request->get['store_id'];
		} 
		else 
		{
			//$store_id = 170;
		}
		//$data['prd_id']=$mcrypt->encrypt(153);
		//$data['str_id']=$mcrypt->encrypt(170);
		$data['view']=$this->request->get['view'];
		$log->write($product_id);
		
		$data['near_by_stores']=$this->model_peer_peer->getstoresbyproduct(array('product_id'=>$product_id))->rows;
		//print_r($data['near_by_stores']);
		
		//$this->adminmodel('setting/setting');
		//$config_telephone=$this->model_setting_setting->getSettingbykey('config','config_telephone',$data['store_id']);
				
		$product_info = $this->model_catalog_product->getProduct($product_id);
		$bookmark_info = $this->model_catalog_product->getProductBookmark(array('product_id'=>$product_id,'store_id'=>$store_id));
		$bookmark_count = $this->model_catalog_product->getProductBookmarkCount(array('product_id'=>$product_id,'store_id'=>$store_id));
		$review_info = $this->model_catalog_product->getProductReview(array('product_id'=>$product_id,'store_id'=>$store_id));
		$review_count = $this->model_catalog_product->getProductReviewCount(array('product_id'=>$product_id,'store_id'=>$store_id));
		if(!empty($store_id))
		{
			$reward_list=$data['reward_list'] = $this->model_catalog_product->getProductReward(array('product_id'=>$product_id,'store_id'=>$store_id));
		}
		else
		{
			$reward_list=$data['reward_list'] = $this->model_catalog_product->getProductRewardForAll(array('product_id'=>$product_id));
		}
		//print_r($reward_list);
		//if (in_array("Glenn", $reward_list))
		//{
			
		//}
		//print_r($near_by_stores);exit;
		if(empty($bookmark_info))
		{
			$bookmark_info=0;
		}
		if(empty($review_info))
		{
			$review_info=0;
		}
		if(empty($review_count))
		{
			$review_count=0;
		}
		$data['bookmark']=$bookmark_info;
		$data['bookmark_count']=$bookmark_count;
		$data['review']=$review_info;
		$data['review_count']=$data['review']=$review_count;
		$data['store_id']=$store_id;
		$data['product_id']=$product_id;
		
		$data['product_info']=$product_info;
		if ($product_info) 
		{
			$this->load->model('tool/image');
			if ($product_info['image']) 
			{
				$data['main_image'] = $this->model_tool_image->resize($product_info['image'], 40, 40);
			} 
			else 
			{
				$data['main_image'] = '';
			}
			$data['image'] =$product_info['image'];
			$data['main_image_high_res']	= $data['main_image'];
			$data['images'] = array();
			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);
			foreach ($results as $result) 
			{
				$data['images'][] = array(
					 $this->model_tool_image->resize($result['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
					 $this->model_tool_image->resize($result['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
				);
			}
			
		}
		//end check
		$this->response->setOutput($this->load->view('default/template/product/product_dtl.tpl', $data));
		//$this->response->setOutput(json_encode($data,JSON_UNESCAPED_UNICODE));
	}
	public function index()
	{
		$mcrypt=new MCrypt();
            
            $this->currency->set($this->config->get('config_currency'));  
               
            $this->adminmodel('pos/pos');
            $store_id=$mcrypt->decrypt($this->request->get['store']);
			$_SESSION['config_store_id']=$store_id;
            
            $data['comparasion_chart'] = $this->load->controller('dashboard/comparasion_chart');
			$data['comparasion_chart_order_count'] = $this->load->controller('dashboard/comparasion_chart/ordercount');
			$data['comparasion_chart_category'] = $this->load->controller('dashboard/comparasion_chart/category');
			$data['comparasion_chart_bar_chart'] = $this->load->controller('dashboard/comparasion_chart/bar_chart');
            $data['top5products'] = $this->load->controller('dashboard/top5products');
			//print_r($data['top5products']);
            $this->response->setOutput($this->load->view('default/template/pos/dashboard.tpl', $data));
    }

	public function slider()
	{
		$mcrypt=new MCrypt(); 

		$this->adminmodel('catalog/imgmarque');
			$filter_data = array(			
			'start'             => 0,
			'limit'             => 20
		);

        $this->adminmodel('tool/image');
		
		$faqdata=$this->model_catalog_imgmarque->getFaqs($filter_data); 
		$faq_total =$faqdata->num_rows;

		$results = $faqdata->rows;
       
        $data['products']=array();
		
		foreach ($results as $result) 
		{
			if($result['status']){
			$data['products'][] = array(
				'id'  => $mcrypt->encrypt($result['faq_id']),
				'name'     =>$mcrypt->encrypt( strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))),
				'image'     =>$mcrypt->encrypt( $result['image']),
				'thumb'     =>$mcrypt->encrypt(str_replace('HTTPS_CATALOG','', $this->model_tool_image->resize($result['image'], 100, 100))),
				);}
		}
		return $this->response->setOutput(json_encode($data));
	}	


    public function categories()
    {
        $mcrypt=new MCrypt(); 
        $this->adminmodel('catalog/dashboardcategory');
        $keys = array(
		'store_id',
		'page'
		);
		foreach ($keys as $key) 		
		{
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
		}
        $page=$this->request->post['page'];
		if(empty($page))
		{
            $page=1;
		}
        $limit=20;
        $start=($page-1)*20;
		$filter_data=array(
			'start'=>$start,
			'limit'=>$limit,
			'store_id'=>$this->request->post['store_id']
			);
        $categories=$this->model_catalog_dashboardcategory->getCategories($filter_data);
        
        $total_order=$categories1[0]['totalrows'];
        $child=array();
        foreach($categories as $category)
        {
			$child=array();
            $childs=$this->model_catalog_dashboardcategory->getCategoryChild(array('category_id'=>$category['category_id']))->rows;
            foreach($childs as $childs2)
            {
                $child[]=array(
                    'product_id'=>$mcrypt->encrypt($childs2['product_id']),
                    'name'=>$mcrypt->encrypt($childs2['name']),
                    'image'=>$mcrypt->encrypt($childs2['image']),
                    'product_description'=>$mcrypt->encrypt($childs2['product_description']));
            }
            $json['products'][] = array(
				'category_id' => $mcrypt->encrypt($category['category_id']),
				'name'	=> $mcrypt->encrypt($category['name']),
				'child'	=> ($child),
				
			);
            //print_r($child);
			
        }
        $json['total']=$mcrypt->encrypt($total_order);
		return $this->response->setOutput(json_encode($json));
    }  
	public function catalogcategories()
    {
		$log=new Log("catalogcategories-".date('Y-m-d').".log");
        $log->write($this->request->post);
        $mcrypt=new MCrypt(); 
        $this->adminmodel('catalog/productcatalogcategory');
        $keys = array(
		'store_id',
		'page',
		'service_type'
		);
		foreach ($keys as $key) 		
		{
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
		}
        $page=$this->request->post['page'];
		$log->write($this->request->post);
		if(empty($page))
		{
            $page=1; 
		}
        $limit=20;
        $start=($page-1)*20;
		$filter_data=array(
			'start'=>$start,
			'limit'=>$limit,
			'store_id'=>$this->request->post['store_id'],
			'service_type'=>$this->request->post['service_type']
			);
        $categories=$this->model_catalog_productcatalogcategory->getCategories($filter_data);
		$log->write('count');
        $log->write(count($categories));
        $total_order=$categories1[0]['totalrows'];
        $child=array();
		///////////////
		
		
		///////////
        foreach($categories as $category)
        {
			$log->write('generated url for curl');
			$log->write($category['child_url'].'&store_id='.$this->request->post['store_id']);
			//if($category['child_url']=='mpos/dashboard/promotedproducts')
			{
			$child_data=$this->call_curl($category['child_url'].'&store_id='.$this->request->post['store_id']);
			}
			if(!empty($child_data['categories']))
			{
				$child_data=$child_data['categories'];
				if($category['child_url']=='mpos/product/categories')
				{
					$child_data3=array();
					foreach($child_data as $child_data2)
					{
						$category_id=$mcrypt->decrypt($child_data2['category_id']);
						if($category_id!=44)
						{
							$child_data3[]=array( 'category_id' => $mcrypt->encrypt($category_id),'image' => $child_data2['image'],'name' => $child_data2['name']);
						}
					}
					//$log->write($child_data3);
					$child_data=$child_data3;
				}
			}
			else
			{
				$child_data=$child_data['products'];
			}
			
            $json['products'][] = array(
				'category_id' => $mcrypt->encrypt($category['category_id']),
				'name'	=> $mcrypt->encrypt("Browse By ".$category['name']),
				'childurl'	=> ($category['child_url']),
				'child'=>$child_data
				
			);
            //print_r($child);
        }
        $json['total']=$mcrypt->encrypt($total_order);
		return $this->response->setOutput(json_encode($json));
    }  
	public function call_curl($url)
	{
		
		$surl=HTTPS_SERVER.'index.php?route='.$url;
		//echo $surl;
		$curl_handle=curl_init();
		curl_setopt($curl_handle,CURLOPT_URL,$surl);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl_handle,CURLOPT_HTTPHEADER,$headers);
		$buffer = curl_exec($curl_handle);
		if($buffer === false)
		{
			return 'Curl error: ' . curl_error($curl_handle);
          
		}
		else
		{
			$buffer=json_decode($buffer,true);
        }

		curl_close($curl_handle);
		return $buffer;
	}
	public function promotedproducts() 
    { 
        $this->load->language('api/cart');
		$mcrypt=new MCrypt();	
        $json = array();
		$log =new Log("promotedproducts-".date('Y-m-d').".log");
        $log->write('promotedproducts called in dashboard');
		$log->write($this->request->get);
		$log->write($this->request->post);
		$log->write(($this->request->get['store_id']));
        $this->load->model('catalog/product');
		  $this->adminmodel('setting/setting');
             $this->adminmodel('tool/image');
		$json = array('success' => true, 'products' => array());

       
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
		
			$products = $this->model_catalog_product->promotedproducts(array(
					'promotion_start_date'=>date('Y-m-d'),
					'promotion_end_date'=>date('Y-m-d'),
					'quantity_check'=>1,
					'store_id'=>( $this->request->get['store_id']),
					'for_store'=>$this->config->get('config_store_id')
				));
			
			$this->config->set('config_store_id',( $this->request->get['store_id']));
			$own_products = $this->model_catalog_product->getProducts(array(
				'filter_category_id'        => $category_id,
				'quantity_check'=>1,
				'store_id'=>( $this->request->get['store_id']),
				'for_store'=>'own'.( $this->request->get['store_id'])
				));
		
		$price = array();
		foreach ($products as $key => $row)
		{
			$price[$key] = $row['name'];
		}
		array_multisort($price, SORT_ASC, $products);
		$log->write($products);
        foreach ($products as $product) 
		{ 
            if(empty($product))
            {                            
                continue;
            }
          
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
			//$log->write('updated price 3: '.$product['price']);
          
			 
			$product['image']=$product['image']?$this->model_tool_image->resize($product['image'], 180, 180):'view/image/pos/logo.png';
			$log->write($product['image']);
			$product['image']=str_replace(HTTPS_CATALOG,'',$product['image']);
			$log->write($product['image']);
			 
			 
            $json['products'][] = array(
					'category_id'			=> $mcrypt->encrypt($product['product_id']),
					'name'			=> $mcrypt->encrypt($product['name']),
					'favourite'			=> $mcrypt->encrypt($product['favourite']), 
					'quantity'  	=> $mcrypt->encrypt(empty($product['quantity'])? 0:$product['quantity']),
					'hstn'   		=> $mcrypt->encrypt(empty($product['HSTN'])? 0:$product['HSTN']),
					'description'	=> $mcrypt->encrypt("0"),
					'pirce'			=> $mcrypt->encrypt(($product['price'])),
					'href'			=> $mcrypt->encrypt($this->url->link('product/product', 'product_id=' . $product['product_id'])),
					'thumb'			=> $mcrypt->encrypt($product['image']),
					'image'			=> $mcrypt->encrypt($product['image']),
					'special'		=> $mcrypt->encrypt($product['image']),
					'rating'		=> $mcrypt->encrypt($product['rating']), 
					'tax'			=> $mcrypt->encrypt(($this->tax->getTax($product['price'], $product['tax_class_id']))),//
					'per_tax'       => $mcrypt->encrypt(json_encode($this->tax->getRates($product['price'], $product['tax_class_id']))),
					'category'		=> $this->request->get['category'],
					'subsidy'		=> $mcrypt->encrypt( empty($product['subsidy'])? 0:$product['subsidy']),
					'chemical'	=>	$mcrypt->encrypt( empty($product['model'])? 0:$product['model']),
					'sku'	=>	$mcrypt->encrypt( empty($product['sku'])? 0:$product['sku']),
					'bookmark' =>	$mcrypt->encrypt( empty($product['pd']['bookmark'])? 0:$product['pd']['bookmark']),


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
            //$log->write("done");
            $this->response->setOutput(json_encode($json));
		}
    }
	public function products() 
    { 
        $this->load->language('api/cart');
		$mcrypt=new MCrypt();	
        $json = array();
		$log =new Log("products-dash-".date('Y-m-d').".log");
        $log->write('products called in dashboard');
		$this->request->post['service_type']=$mcrypt->decrypt($this->request->post['service_type']);
		$this->request->post['page']=$mcrypt->decrypt($this->request->post['page']);
		$log->write($this->request->get);
		$log->write($this->request->post);
        $this->load->model('catalog/product');
		
		if(isset($this->request->post['stype'])&&isset($this->request->post['store_emp']))
		{
			$this->config->set('config_store_id','0');

		}
        else if(isset($this->request->post['stype']))
		{
            $this->config->set('config_store_id',$mcrypt->decrypt($this->request->post['stype']));
            $log->write("in stype");
			$log->write($mcrypt->decrypt($this->request->post['stype']));
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
		if (isset($this->request->get['manufacturer'])) 
		{
            $manufacturer_id =$mcrypt->decrypt( $this->request->get['manufacturer']);
		} 
		else 
		{
            $manufacturer_id = 0;
		}
        $log->write($manufacturer_id);
		if (!empty($this->request->post['page'])) 
		{
                $page = $this->request->post['page'];
        } 
		else if (!empty($this->request->get['page'])) 
		{
                $page = $this->request->get['page'];
        } 
		else 
		{
                $page = 1;
        }
		$limit    = 20;
		$log->write('page');
		$log->write($page);
		
		//if(empty($page))
		//{
		//	$offset=0;
		//}
		//else
		//{
			$offset = ($page-1);//*$limit;
		//}		
		$log->write($offset);
		//$log->write($manufacturer_id);
		if(isset($this->request->post['stype']))
		{
			$this->config->set('config_store_id',0);
		}
		if(isset($this->request->post['stype']))
		{
			if(!empty($manufacturer_id))
			{
				$products = $this->model_catalog_product->getProductsByManufacturer(array(
					'manufacturer_id'        => $manufacturer_id,
					'quantity_check'=>0,
					
					'store_id'=>$mcrypt->decrypt( $this->request->post['store_id']),
					'for_store'=>$this->config->get('config_store_id')
				));
			}
			else
			{
				$products = $this->model_catalog_product->getProducts(array(
					'filter_category_id'        => $category_id,
					'quantity_check'=>0,
					'store_id'=>$mcrypt->decrypt( $this->request->post['store_id']),
					'for_store'=>$this->config->get('config_store_id')
				));
			}
		}
		else
		{
			if(!empty($manufacturer_id))
			{            $log->write("in manufactures");
				$products = $this->model_catalog_product->getProductsByManufacturer(array(
					'manufacturer_id'        => $manufacturer_id,
					'quantity_check'=>1,
					
					'store_id'=>$mcrypt->decrypt( $this->request->post['store_id']),
					'for_store'=>$this->config->get('config_store_id')
				));
			}
			else
			{
				$products = $this->model_catalog_product->getProducts(array(
					'filter_category_id'        => $category_id,
					'quantity_check'=>1,
					'store_id'=>$mcrypt->decrypt( $this->request->post['store_id']),
					'for_store'=>$this->config->get('config_store_id')
				));
			}
		}
        if(isset($this->request->post['stype']))
		{
            $this->config->set('config_store_id',$mcrypt->decrypt( $this->request->post['store_id']));
            $log->write("before call to own_products  in product/products");
			if(!empty($manufacturer_id))
			{
				$own_products = $this->model_catalog_product->getProductsByManufacturer(array(
				'manufacturer_id'        => $manufacturer_id,
				'quantity_check'=>1,
				
				'store_id'=>$mcrypt->decrypt( $this->request->post['store_id']),
				'for_store'=>'own'.$mcrypt->decrypt( $this->request->post['store_id'])
				));
			}
			else
			{
				$own_products = $this->model_catalog_product->getProducts(array(
				'filter_category_id'        => $category_id,
				'quantity_check'=>1,
				'store_id'=>$mcrypt->decrypt( $this->request->post['store_id']),
				'for_store'=>'own'.$mcrypt->decrypt( $this->request->post['store_id'])
				));
			}
        }
        $log->write("products count");
		$log->write(count($products));
		
		$log->write("own_products count");
		$log->write(count($own_products));
		
		
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
		$this->adminmodel('setting/setting');
		$this->adminmodel('tool/image');
        foreach ($products as $product) 
		{ 
            //$log->write("data products loop");
			//$log->write($product['image']);
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
                $product['price']=@$own_products[$product['product_id']]['pd']['store_price'];
				$product['favourite']=@$own_products[$product['product_id']]['pd']['favourite'];
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
			//$log->write('updated price 3: '.$product['price']);
            
            $product['image']=$product['image']?$this->model_tool_image->resize($product['image'], 180, 180):'view/image/pos/logo.png';
			//$log->write($product['image']);
			$product['image']=str_replace(HTTPS_CATALOG,'',$product['image']);
            $json['products'][] = array(
					'id'			=> $mcrypt->encrypt($product['product_id']),
					'name'			=> $mcrypt->encrypt($product['name']),
					'favourite'			=> $mcrypt->encrypt($product['favourite']), 
					'quantity'  	=> $mcrypt->encrypt(empty($product['quantity'])? 0:$product['quantity']),
					'hstn'   		=> $mcrypt->encrypt(empty($product['HSTN'])? 0:$product['HSTN']),
					'description'	=> $mcrypt->encrypt("0"),
					'pirce'			=> $mcrypt->encrypt(($product['price'])),
					'href'			=> $mcrypt->encrypt($this->url->link('product/product', 'product_id=' . $product['product_id'])),
					'thumb'			=> $mcrypt->encrypt($product['image']),
					'image'			=> $mcrypt->encrypt($product['image']),
					'special'		=> $mcrypt->encrypt($product['image']),
					'rating'		=> $mcrypt->encrypt(@$product['rating']), 
					'tax'			=> $mcrypt->encrypt(($this->tax->getTax($product['price'], $product['tax_class_id']))),//
					'per_tax'       => $mcrypt->encrypt(json_encode($this->tax->getRates($product['price'], $product['tax_class_id']))),
					'category'		=> (@$this->request->get['category']),
					'subsidy'		=> $mcrypt->encrypt( empty($product['subsidy'])? 0:$product['subsidy']),
					'chemical'	=>	$mcrypt->encrypt( empty($product['model'])? 0:$product['model']),
					'sku'	=>	$mcrypt->encrypt( empty($product['sku'])? 0:$product['sku'])

					);
			$for_log[]=array('id'=> $product['product_id'],'name'=> $product['name']);
		}//////////foreach end here
        	
		if ($this->debugIt) 
		{
            echo '<pre>';
            print_r($json);
            echo '</pre>';
		} 
		else 
		{
            $log->write($for_log);
            $this->response->setOutput(json_encode($json));
		}
    }
}

?>