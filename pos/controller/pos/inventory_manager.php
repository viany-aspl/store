<?php

class ControllerPosInventoryManager extends Controller {
	private $error = array(); 
	public function adminmodel($model) 
        {
            $admin_dir = DIR_SYSTEM;
            $admin_dir = str_replace('system/','backoffice/',$admin_dir);
            $file = $admin_dir . 'model/' . $model . '.php';      
            $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
            if (file_exists($file)) 
            {
                include_once($file);
                $this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
            } 
            else 
            {
                trigger_error('Error: Could not load model ' . $model . '!');
                exit();               
            }
        }
        
        public function catmodel($model) 
        {
            $admin_dir = DIR_SYSTEM;
            $admin_dir = str_replace('system/','catalog/',$admin_dir);
            $file = $admin_dir . 'model/' . $model . '.php';      
            $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
            if (file_exists($file)) 
            {
                include_once($file);
                $this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
            } 
            else 
            {
                trigger_error('Error: Could not load model ' . $model . '!');
                exit();               
            }
        }
        
            public function index() 
            {

                $this->adminmodel('setting/setting');
                $this->adminmodel('pos/pos');
                $this->adminmodel('tool/image');
                $this->adminmodel('setting/store');
                
                //remove cart products 
                $this->load->library('tax');//
                $this->tax = new Tax($this->registry);
            
                $this->load->library('pos_cart');//
                $this->cart = new Pos_cart($this->registry);
                $this->cart->clear();    
                
                unset($this->session->data['voucher']);
                unset($this->session->data['coupon']);
                
                $this->load->library('customer');
                $this->customer = new Customer($this->registry);
            
		$this->language->load('pos/pos');

		$this->document->setTitle($this->language->get('heading_title'));
		
                $this->load->library('user');
                
		$data['user'] = $this->user->getUserName();                
		$this->config->set('config_store_id',$this->user->getStoreId());
                $data['currency_code'] = $this->config->get('config_currency');
		$data['currency_value'] = '1.0';
		$data['store_id'] = $this->user->getStoreId();
		$data['token'] = $this->session->data['token'];
                $data['text_select'] = 'Select';
                $data['button_upload'] = 'Upload';
                $data['name'] = $this->config->get('config_name'); 
                $data['default_amount'] = $this->currency->format('0.00');
                $data['logged'] = $this->user->getUserNameShow();//'You are logged in as '.$this->user->getUserName();
		
				
		// Settings
		$setti=$this->model_setting_setting->getSettingsql('config',$this->user->getStoreId());
                foreach ($setti as $setting) {
                    if (!$setting['serialized']) {
                    $this->config->set($setting['key'], $setting['value']);
                    } else {
                    $this->config->set($setting['key'], unserialize($setting['value']));
                    }
                }
		$this->adminmodel('sale/customer_group');
                 
		$data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
                
		if (isset($this->request->post['customer_group_id'])) {
			$data['customer_group_id'] = $this->request->post['customer_group_id'];
		} elseif (!empty($customer_info)) {
			$data['customer_group_id'] = $customer_info['customer_group_id'];
		} else {
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}               
                //get categories 
                $categories = $this->model_pos_pos->getTopCategories();
                
		$data['categories'] = array();
		
		foreach ($categories as $category_info) {
                    $data['categories'][] = array(
                        'category_id' => $category_info['category_id'],
                        'image'       => $category_info['image']?$this->model_tool_image->resize($category_info['image'], 70, 70):'view/image/pos/logo.png',
                        'name'        => $category_info['name'],
                    );
		}
                
                $data['storename']= $this->model_setting_store->getStore($this->user->getStoreId())["name"];
		$data['storeadd']= $this->config->get('config_address');
                //load template 
                $data['header'] = $this->load->controller('common/header');
                $data['footer'] = $this->load->controller('common/footer');              
                $data['column_left'] = $this->load->controller('common/column_left');
                $this->response->setOutput($this->load->view('default/template/pos/update_price.tpl', $data));

            }
            public function quantity() 
            {

                $this->adminmodel('setting/setting');
                $this->adminmodel('pos/pos');
                $this->adminmodel('tool/image');
                $this->adminmodel('setting/store');
                
                //remove cart products 
                $this->load->library('tax');//
                $this->tax = new Tax($this->registry);
            
                $this->load->library('pos_cart');//
                $this->cart = new Pos_cart($this->registry);
                $this->cart->clear();    
                
                unset($this->session->data['voucher']);
                unset($this->session->data['coupon']);
                
                $this->load->library('customer');
                $this->customer = new Customer($this->registry);
            
		$this->language->load('pos/pos');

		$this->document->setTitle($this->language->get('heading_title'));
		
                $this->load->library('user');
                
		$data['user'] = $this->user->getUserName();                
		$this->config->set('config_store_id',$this->user->getStoreId());
                $data['currency_code'] = $this->config->get('config_currency');
		$data['currency_value'] = '1.0';
		$data['store_id'] = $this->user->getStoreId();
		$data['token'] = $this->session->data['token'];
                $data['text_select'] = 'Select';
                $data['button_upload'] = 'Upload';
                $data['name'] = $this->config->get('config_name'); 
                $data['default_amount'] = $this->currency->format('0.00');
                $data['logged'] = $this->user->getUserNameShow();//'You are logged in as '.$this->user->getUserName();
		
				
		// Settings
		$setti=$this->model_setting_setting->getSettingsql('config',$this->user->getStoreId());
                foreach ($setti as $setting) {
                    if (!$setting['serialized']) {
                    $this->config->set($setting['key'], $setting['value']);
                    } else {
                    $this->config->set($setting['key'], unserialize($setting['value']));
                    }
                }
		$this->adminmodel('sale/customer_group');
                 
		$data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
                
		if (isset($this->request->post['customer_group_id'])) {
			$data['customer_group_id'] = $this->request->post['customer_group_id'];
		} elseif (!empty($customer_info)) {
			$data['customer_group_id'] = $customer_info['customer_group_id'];
		} else {
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}               
                //get categories 
                $categories = $this->model_pos_pos->getTopCategories();
                
		$data['categories'] = array();
		
		foreach ($categories as $category_info) {
                    $data['categories'][] = array(
                        'category_id' => $category_info['category_id'],
                        'image'       => $category_info['image']?$this->model_tool_image->resize($category_info['image'], 70, 70):'view/image/pos/logo.png',
                        'name'        => $category_info['name'],
                    );
		}
                
                $data['storename']= $this->model_setting_store->getStore($this->user->getStoreId())["name"];
		$data['storeadd']= $this->config->get('config_address');
                //load template 
                $data['header'] = $this->load->controller('common/header');
                $data['footer'] = $this->load->controller('common/footer');              
                $data['column_left'] = $this->load->controller('common/column_left');
                $this->response->setOutput($this->load->view('default/template/pos/update_quantity.tpl', $data));

            }
        public function getCategoryItems() 
        {
            $json['categories'] = $json['products'] = array();
            $parent_category_id = $this->request->post['category_id'];
            if(!empty($parent_category_id))
			{
				$parent_category_id = $this->request->get['category_id'];
			}
            $this->adminmodel('pos/pos');
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
            $offset = ($page-1)*$limit;               
            $total = 10;//$this->model_pos_pos->total_products($parent_category_id); 
			$this->catmodel('catalog/product');
           
			$products =$this->model_catalog_product->getProductsInv(array(
            'filter_category_id'        => $parent_category_id,
			'start'=> $offset,
			'limit'=> $limit,
            ));
            
            $own_products = $this->model_catalog_product->getProductsInv(array(
            'filter_category_id'        => $parent_category_id,
            'quantity_check'=>1
            ));
            $this->language->load('pos/pos');
            foreach ($products as $product) 
            {
                if(!empty($product['image']))
                {
                    if(is_file('../image/'.$product['image']))
                    {
                        $image='../image/'.$product['image'];
                    }
                    else 
                    {
                        $image='../image/no_image.jpg';
                    }
                }
                else 
                {
                    $image='../image/no_image.jpg';
                }
                if(!empty($own_products)&&(array_key_exists($product['product_id'],$own_products)))
                {
                    $product['price']=$own_products[$product['product_id']]['pd']['store_price']+$own_products[$product['product_id']]['pd']['store_tax_amt'];
                }
                else
                {
                    if(empty($product['pd']['store_price'])||$product['pd']['store_price']==0.0000)
                    {
                        $product['price']=$product['price'];
                    }
                    else
                    {
                        $product['price']=$product['pd']['store_price'];
                    }
                }
                if(!empty($own_products)&&(array_key_exists($product['product_id'],$own_products)))
                {
                    $product['quantity']=$own_products[$product['product_id']]['pd']['quantity'];//$productprice['quantity'];
                }
                else
                {
                    $product['quantity']=$product['pd']['quantity'];
                }
                $json['products'][] = array('type' => 'P',
                            'name' => $product['name'],
							'chemical_name' => $product['model'],
                            'image' => $image,
                            'price_text' => ($product['price']), //, $currency_code, $currency_value
                            'stock_text' => $product['quantity'], // . ' ' . $this->language->get('text_remaining'),
                            //'hasOptions' => $product['options'] ? '1' : '0',
                            'id' => $product['product_id'],
                            'tax' => $product['tax'],
			    'store_price_text' => $this->currency->format($product['price']),//$product['store_price']) 		
                        );       
            }
            return $this->response->setOutput(json_encode($json));
	}
	public function autocomplete() 
        {
            $json = array();
            if (isset($this->request->get['filter_name'])) 
            {
		$this->adminmodel('pos/pos');
		if (isset($this->request->get['filter_name'])) 
                {
                    $filter_name = $this->request->get['filter_name'];
		} 
                else 
                {
                    $filter_name = '';
		}
                if (isset($this->request->get['limit'])) 
                {
                    $limit = $this->request->get['limit'];
		}
                else 
                {
                    $limit = 5;
		}
                $limit    = 5;
                $offset = 0;               
                $total = 5;//$this->model_pos_pos->total_products($parent_category_id); 
                if(!empty($filter_name))
                {
                    $products = $this->model_pos_pos->getProductsAll($parent_category_id, $limit, $offset,$filter_name);
                }
		
                $this->catmodel('catalog/product');
                $own_products = $this->model_catalog_product->getProducts(array(
                    'filter_category_id'        => $parent_category_id,
                    'quantity_check'=>1
                    ));
                $this->language->load('pos/pos');
                foreach ($products as $product) 
                {
                    if(!empty($product['image']))
                    {
                        if(is_file('../image/'.$product['image']))
                        {
                            $image='../image/'.$product['image'];
                        }
                        else 
                        {
                            $image='../image/no_image.jpg';
                        }
                    }
                    else 
                    {
                        $image='../image/no_image.jpg';
                    }
                    if(!empty($own_products)&&(array_key_exists($product['product_id'],$own_products)))
                    {
                        $product['price']=$own_products[$product['product_id']]['pd']['store_price']+$own_products[$product['product_id']]['pd']['store_tax_amt'];
                    }
                    else
                    {
                        if(empty($product['pd']['store_price'])||$product['pd']['store_price']==0.0000)
                        {
                            $product['price']=$product['price'];
                        }
                        else
                        {
                            $product['price']=$product['pd']['store_price'];
                        }
                    }
                    if(!empty($own_products)&&(array_key_exists($product['product_id'],$own_products)))
                    {
                        $product['quantity']=$own_products[$product['product_id']]['pd']['quantity'];//$productprice['quantity'];
                    }
                    else
                    {
                        $product['quantity']=$product['pd']['quantity'];
                    }
                    $json[] = array('type' => 'P',
                            'name' => $product['name'],
							'chemical_name' => $product['model'],
                            'image' => $image,
                            'price_text' => $this->currency->format($product['price']), //, $currency_code, $currency_value
                            'stock_text' => $product['quantity'], // . ' ' . $this->language->get('text_remaining'),
                            //'hasOptions' => $product['options'] ? '1' : '0',
                            'id' => $product['product_id'],
                            'tax' => $product['tax'],
			    'store_price_text' => $this->currency->format($product['price']),//$product['store_price']) 		
                        );       
                }
                //print_r($json['products']);
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
            }    
	}
        public function update_price()
        {
            $this->request->post['username'] =$this->user->getId();
            $this->request->post['store_id'] =$this->config->get('config_store_id');
            $this->request->post['product_id'] =$this->request->get['product_id'];
            $this->request->post['price'] =$this->request->get['new_price'];
            $this-> adminmodel('catalog/product');
	
            $output=$this->model_catalog_product->openretailerupdateprice($this->request->post);
            echo $this->request->post['price'];//$this->request->post['price'];
        }
        public function update_quantity()
        {
            $this->request->post['username'] =$this->user->getId();
            $this->request->post['store_id'] =$this->config->get('config_store_id');
            $this->request->post['product_id'] =$this->request->get['product_id'];
            $this->request->post['quantity'] =$this->request->get['new_quantity'];
            $this-> adminmodel('catalog/product');
	
            $output=$this->model_catalog_product->openretailerupdateqty($this->request->post);
            echo $this->request->post['quantity'];//$this->request->post['price'];
        }
}
?>