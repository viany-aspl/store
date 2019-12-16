<?php

class ControllerPosPos extends Controller 
{
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
        
        
	public function get_total()
	{
             
            $this->load->library('customer');
            $this->customer = new Customer($this->registry);
            
            $this->load->library('tax');//
            $this->tax = new Tax($this->registry);

            $this->load->library('pos_cart');//
            $this->cart = new Pos_cart($this->registry);  
            
            // Totals
            $this->load->model('pos/extension');

            $total_data = array();					
            $total = 0;
            $json = array();
            $taxes = $this->cart->getTaxes();

            // Display prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $sort_order = array(); 

                $results = $this->model_pos_extension->getExtensions('total');

                foreach ($results as $key => $value) {
                        $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                }

                array_multisort($sort_order, SORT_ASC, $results);

                foreach ($results as $result) {
                    if ($this->config->get($result['code'] . '_status')) {
                        $this->load->model('pos/' . $result['code']);

                        $this->{'model_pos_' . $result['code']}->getTotal($total_data, $total, $taxes);
                    }

                    $sort_order = array(); 

                    foreach ($total_data as $key => $value) {
                        $sort_order[$key] = $value['sort_order'];
                    }

                    array_multisort($sort_order, SORT_ASC, $total_data);			
                }
            }

            echo $this->currency->format($total, false, false, false);
        }
        
		public function index() 
		{ 
            if ((empty($this->user->isLogged()) && empty($this->request->get['token']) && empty($this->session->data['token']))||($this->request->get['token'] != $this->session->data['token']) ) 
            {
				$this->response->redirect($this->url->link('common/home', '', 'SSL'));
			}
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
            foreach ($setti as $setting) 
			{
                if (!$setting['serialized']) 
				{
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
                
                $balance = $this->model_pos_pos->get_user_balance($this->user->getId());
                
                $data['cash'] = $this->currency->format($balance['cash']);
                $data['card'] = $this->currency->format($balance['card']);
                
                
                $data['storename']= $this->model_setting_store->getStore($this->user->getStoreId())["name"];
		$data['storeadd']= $this->config->get('config_address');
                //load template 
                $data['billtype']=$this->request->get['billtype'];
                $data['header'] = $this->load->controller('common/header');
                $data['footer'] = $this->load->controller('common/footer');              
                $data['column_left'] = $this->load->controller('common/column_left');
                $this->response->setOutput($this->load->view('default/template/pos/index.tpl', $data));

	}
	
        public function clearCart()
		{
             
            $this->load->library('customer');
            $this->customer = new Customer($this->registry);

            $this->load->library('tax');//
            $this->tax = new Tax($this->registry);

            $this->load->model('catalog/product');
            
            $this->load->library('pos_cart');//
            $this->cart = new Pos_cart($this->registry);
            
            $this->cart->clear();
            echo 'success: cart destroyed!';
        }
        
        public function removeFromCart()
		{
            
            $this->load->library('customer');
            $this->customer = new Customer($this->registry);

            $this->load->library('tax');//
            $this->tax = new Tax($this->registry);

            $this->adminmodel('catalog/product');
            
            $this->load->library('pos_cart');//
            $this->cart = new Pos_cart($this->registry);
            $this->cart->remove($this->request->post['remove']);                               

            // Totals
            $this->adminmodel('pos/extension');

            $total_data = array();					
            $total = 0;
            $json = array();
            $taxes = $this->cart->getTaxes();

            // Display prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) 
            { 
                $sort_order = array(); 

                $results = $this->model_pos_extension->getExtensions('total');

                foreach ($results as $key => $value) {
                        $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                }

                array_multisort($sort_order, SORT_ASC, $results);

                foreach ($results as $result) 
				{
                    if ($this->config->get($result['code'] . '_status')) {
                        $this->adminmodel('pos/' . $result['code']);

                        $this->{'model_pos_' . $result['code']}->getTotal($total_data, $total, $taxes);
                    }

                    $sort_order = array(); 

                    foreach ($total_data as $key => $value) {
                        $sort_order[$key] = $value['sort_order'];
                    }

                    array_multisort($sort_order, SORT_ASC, $total_data);			
                }
            }

            $json['total_data'] = $total_data;
            $json['total'] = $this->currency->format($total);

            echo json_encode($json);
        }
        
		public function addToCart() 
		{
            $log=new Log("addToCart-".date('Y-m-d').".log");
            $json = array();
            $this->load->library('customer');
            $this->customer = new Customer($this->registry);
            $this->load->library('tax');//
            $this->tax = new Tax($this->registry);
           
            $this->load->library('pos_cart');//
            $this->cart = new Pos_cart($this->registry);
            $this->catmodel('catalog/product');
            $log->write('check1');
            $log->write($this->request->post);
                
            if (isset($this->request->post['product_id'])) 
            {
                $product_id = $this->request->post['product_id'];
            } 
			else if (isset($this->request->get['product_id'])) 
            {
                $product_id = $this->request->get['product_id'];
            } 
            else 
            {
                $product_id = 0;
            }
			if (isset($this->request->post['cat_id'])) 
            {
                $cat_id = $this->request->post['cat_id'];
            } 
			else if (isset($this->request->get['cat_id'])) 
            {
                $cat_id = $this->request->get['cat_id'];
            } 
            else 
            {
                $cat_id = 0;
            }
                   //echo $product_id;
            $product_info = $this->model_catalog_product->getProduct($product_id);
			//print_r($product_info);
            if (!empty($this->request->post['price'])) 
            {
                $product_info['sprice'] = $this->request->post['price'];
            } 
            else 
            {
				if(empty($product_info['sprice']))
				{
					$product_info['sprice'] = $product_info['price'];
				}
				else
				{
					$product_info['sprice'] = $product_info['sprice'];
				}
            }
            $log->write('check2');
			
			if ($product_info) 
			{			
				if (isset($this->request->post['quantity'])) 
				{
					$quantity = $this->request->post['quantity'];
				} 
				else 
				{
					$quantity = 1;
				}
		
				if (!$json) 
				{
                $log->write('check3');
				$this->cart->add($this->request->post['product_id'], $quantity, $option,'',$this->request->get['billtype'],(float)$product_info['sprice'],$cat_id);
				$log->write('check4');
				$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('checkout/cart'));

				// Totals
				$this->adminmodel('pos/extension');
                $log->write('check5');
				$total_data = array();	
                                
				$total = 0;
                                
				$taxes = $this->cart->getTaxes();
                $log->write($taxes);
				// Display prices
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) 
				{
					$sort_order = array(); 
					$results = $this->model_pos_extension->getExtensions('total');
                                           $log->write($results);
					foreach ($results as $key => $value) 
					{
						$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
					}
                                        
					array_multisort($sort_order, SORT_ASC, $results);
                                       //$a=1;
					foreach ($results as $result) 
					{
                                            // echo "done4 ".$a.'<br/><br/>';
						if ($this->config->get($result['code'] . '_status')) 
						{
                                                    
							$this->adminmodel('pos/' . $result['code']);
                                                        //echo $result['code'];
                                                        if($result['code']!='credit'){
							$this->{'model_pos_' . $result['code']}->getTotal($total_data, $total, $taxes);
                                                        }
                                                       // echo "done5 ".$a.'<br/><br/>';
                        }
                                                       
						$sort_order = array(); 
                                                
						foreach ($total_data as $key => $value) 
						{
                                                    
							$sort_order[$key] = $value['sort_order'];
                                                        
						}
                                                  
						array_multisort($sort_order, SORT_ASC, $total_data);
                                             
                                             //$a++;
					}
                                        //echo "done4";
				}
                                
                $json['total_data'] = $total_data;
				$json['total'] = $this->currency->format($total);
			} 
		}
        //html for cartecho
        $json['products'] = array();
		
		foreach ($this->cart->getProducts() as $product) 
		{
			
            $log->write('check8');
            $log->write($product);
			$option_data = array();

			foreach ($product['option'] as $option) 
			{
				if ($option['type'] != 'file') 
				{
					$value = $option['value'];	
				} 
				else 
				{
					$filename = $this->encryption->decrypt($option['value']);

					$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
				}			

				$option_data[] = array(								   
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
					'type'  => $option['type']
				);
			}

			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) 
			{
				$price = $this->currency->format($product['price']);
			} 
			else 
			{
				$price = false;
			}

			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) 
			{
				$total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']);
			} 
			else 
			{
				$total = false;
			}
			//tax 
			$log->write('check9');
			$a = $product['price']*$product['quantity'];
            $log->write($product['tax_class_id']);
			$log->write($this->config->get('config_tax'));
            //$log->write($this->config);//print_r($product);
            // exit;
            $b = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
            $tax = $this->currency->format($b - $a);
			//print_r($product['price']);
			//print_r('<br><br>');
			//print_r($product['quantity']);
			//print_r('<br><br>');
            //print_r($a);
			//print_r('<br><br>');
			//print_r($b);
			//print_r('<br><br>');
			//print_r($tax);
			//print_r('<br><br>');
			
			if(!empty($product['product_id']))
			{
				$json['products'][] = array(
				'key'       => $product['key'],
                'id'       => $product['product_id'],
				'name'      => $product['name'],
				'model'     => $product['model'], 
				'option'    => $option_data,
				'quantity'  => $product['quantity'],
				'category_id'  => $product['category_id'],
				'price'     => $price,	
                'tax'       => $tax,	
				'total'     => $total,	
				'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id']),
				);
				//print_r($product);
				//print_r('<br><br>');
				//print_r($json['products']);
				//print_r('<br><br>');
			}
			
		}
		$log->write('return array');
		$log->write($json['products']);
        
		$this->response->setOutput(json_encode($json));		
	}
        
        public function updateCart()
		{
         
            $qty = $this->request->post['quantity'];
            $key = $this->request->post['key'];
            $this->session->data['cart'][$key] = (int)$qty;            
            
            $json = array();

            $this->load->library('customer');
            $this->customer = new Customer($this->registry);

            $this->load->library('tax');//
            $this->tax = new Tax($this->registry);

            $this->load->library('pos_cart');//
            $this->cart = new Pos_cart($this->registry);

            $this->load->model('pos/extension');

            $total_data = array();					
            $total = 0;
            $taxes = $this->cart->getTaxes();

            // Display prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $sort_order = array(); 

                    $results = $this->model_pos_extension->getExtensions('total');

                    foreach ($results as $key => $value) {
                            $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                    }

                    array_multisort($sort_order, SORT_ASC, $results);

                    foreach ($results as $result) {
                            if ($this->config->get($result['code'] . '_status')) {
                                    $this->load->model('pos/' . $result['code']);

                                    $this->{'model_pos_' . $result['code']}->getTotal($total_data, $total, $taxes);
                            }

                            $sort_order = array(); 

                            foreach ($total_data as $key => $value) {
                                    $sort_order[$key] = $value['sort_order'];
                            }

                            array_multisort($sort_order, SORT_ASC, $total_data);			
                    }
            }

            $json['total_data'] = $total_data;
            $json['total'] = $this->currency->format($total);

            //html for cart
            $json['products'] = array();

            foreach ($this->cart->getProducts() as $product) {

                    $option_data = array();

                    foreach ($product['option'] as $option) {
                            if ($option['type'] != 'file') {
                                    $value = $option['value'];	
                            } else {
                                    $filename = $this->encryption->decrypt($option['value']);

                                    $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                            }				

                            $option_data[] = array(								   
                                    'name'  => $option['name'],
                                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
                                    'type'  => $option['type']
                            );
                    }

                    // Display prices
                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                            $price = $this->currency->format($product['price']);
                    } else {
                            $price = false;
                    }

                    // Display prices
                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                            $total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']);
                    } else {
                            $total = false;
                    }
                        
                    //tax 
                    $a = $product['price']*$product['quantity'];
                    $b = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
                    $tax = $this->currency->format($b - $a);

                    $json['products'][] = array(
                            'key'       => $product['key'],
                            'name'      => $product['name'],
                            'model'     => $product['model'], 
                            'option'    => $option_data,
                            'quantity'  => $product['quantity'],
                            'price'     => $price,	
                            'total'     => $total,	
                            'tax'       => $tax,	
                            'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                    );
            }
            
         

            $this->response->setOutput(json_encode($json));	
            
        }
            public function profile()
            {
                $this->document->setTitle($this->language->get('heading_profile'));
                $this->adminmodel('user/user');
                $data['token'] = $this->request->get['token'];
                if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}
		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}						          
             
                if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}
                if (isset($this->error['card'])) {
			$data['error_card'] = $this->error['card'];
		} else {
			$data['error_card'] = '';
		}
		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$user_info = $this->model_user_user->getUser($this->user->getId());
		}
                //print_r($this->config);exit;
                $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => 'Dashboard',
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'Profile',
			'href' => $this->url->link('pos/pos/profile', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
                
		
		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($user_info)) {
			$data['firstname'] = $user_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($user_info)) {
			$data['lastname'] = $user_info['lastname'];
		} else {
			$data['lastname'] = '';
		}
                if (!empty($user_info)) {
			$data['image'] = $user_info['image'];
		} else {
			$data['image'] = '';
		}
		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($user_info)) {
			$data['email'] = $user_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($user_info)) {
			$data['telephone'] = $user_info['username'];
		} else {
			$data['telephone'] = '';
		}
                if (isset($this->request->post['store_name'])) {
			$data['store_name'] = $this->request->post['store_name'];
		} elseif (!empty($this->config->get('config_name'))) {
			$data['store_name'] = $this->config->get('config_name');
		} else {
			$data['store_name'] = '';
		}
                if (isset($this->request->post['store_name'])) {
			$data['store_name'] = $this->request->post['store_name'];
		} elseif (!empty($this->config->get('config_name'))) {
			$data['store_name'] = $this->config->get('config_name');
		} else {
			$data['store_name'] = '';
		}
                if (isset($this->request->post['proprietor'])) {
			$data['proprietor'] = $this->request->post['proprietor'];
		} elseif (!empty($this->config->get('config_owner'))) {
			$data['proprietor'] = $this->config->get('config_owner');
		} else {
			$data['proprietor'] = '';
		}
                if (isset($this->request->post['store_address'])) {
			$data['store_address'] = $this->request->post['store_address'];
		} elseif (!empty($this->config->get('config_address'))) {
			$data['store_address'] = $this->config->get('config_address');
		} else {
			$data['store_address'] = '';
		}
                $data['heading_profile'] = $this->language->get('heading_profile'); 
                $data['header'] = $this->load->controller('common/header');
                $data['footer'] = $this->load->controller('common/footer');              
                $data['column_left'] = $this->load->controller('common/column_left');
                $this->response->setOutput($this->load->view('default/template/pos/profile.tpl', $data));
            }
		public function subscription()
        {
            $this->document->setTitle('Subscription');
            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');              
            $data['column_left'] = $this->load->controller('common/column_left');
			/*
			$request = "https://unnatiagro.in/price/";
			
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_USERAGENT,'akshamaala');
		//curl_setopt($ch, CURLOPT_POST, true);
		//curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
            		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string); 
		$json =curl_exec($ch);
		if(empty($json))
                {
                    //$log->write(curl_error($ch));	
   		}
		curl_close($ch); 
		echo $json;exit;
		$return_val=json_decode($json,TRUE);
			*/
            $this->response->setOutput($this->load->view('default/template/pos/subscription.tpl', $data));
        }
		public function terms_condition()
        {
            $this->document->setTitle('Subscription');
            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');              
            $data['column_left'] = $this->load->controller('common/column_left');
            $this->response->setOutput($this->load->view('default/template/pos/terms_condition.tpl', $data));
        }
			
        public function install() {
		/*
		if (!$this->checkVqmod()) {
			// not existing
			$this->language->load('pos/pos');
			$this->session->data['error'] = $this->language->get('text_vqmod_not_installed');
			$this->load->model('setting/extension');
			// remove from the extension table
			$this->model_setting_extension->uninstall('module', 'pos');
			return false;
		}
		*/
		
		// create tables
		$this->load->model('pos/pos');
                // add default settings
		$this->load->model('setting/setting');   
                // create vqmod files
		$this->createFile();
		
		// copy language file is English not set to default
		//$this->copyLangFile();

		             
		//$this->model_setting_setting->editSetting('POS', array('pos_user_group_id' => 'Credit Card'));
                
                // add permission for report
                $this->load->model('user/user_group');
                $this->model_user_user_group->addPermission($this->user->getId(), 'access', 'pos/pos');
                $this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'pos/pos');
	}

	public function uninstall() {
            	// $this->load->model('pos/pos');
		// $this->model_pos_pos->deleteModuleTables();

                $this->load->model('setting/setting');
                
		 // remove the files
		 $this->deleteFile();

		
		// $this->model_setting_setting->deleteSetting('POS');
	}
	
	private function checkVqmod() {
		return file_exists(DIR_APPLICATION . '/../vqmod');
	}

	private function createFile() {
            $path = dirname(DIR_APPLICATION);
            rename($path . '/pos_',$path . '/pos');
            
            //set module status = 1
            $this->model_setting_setting->editSetting('pos', array('pos_user_group_id'=> 1, 'pos_status' => 1));
            
            //rename(DIR_APPLICATION.'../admin/controller/pos/pos.php_',DIR_APPLICATION.'../admin/controller/pos/pos.php');
            unlink(DIR_APPLICATION.'../vqmod/mods.cache');
            rename(DIR_APPLICATION . '../vqmod/xml/pos.xml_',DIR_APPLICATION . '../vqmod/xml/pos.xml');
            
	}

	private function deleteFile() {
            //set module status = 0
            $this->model_setting_setting->editSetting('pos', array('pos_user_group_id'=> 1, 'pos_status' => 0));

            //rename(DIR_APPLICATION.'../admin/controller/pos/pos.php',DIR_APPLICATION.'../admin/controller/pos/pos.php_');            
            rename(DIR_APPLICATION . '../vqmod/xml/pos.xml',DIR_APPLICATION . '../vqmod/xml/pos.xml_');
            
            $path = dirname(DIR_APPLICATION);
            rename($path . '/pos',$path . '/pos_');
            //unlink(DIR_APPLICATION . '../vqmod/xml/pos.xml');
	}

        private function getStoreId() {
		if (isset($this->request->get['store_id'])) {
			$store_id = $this->request->get['store_id'];
		} else {
			$store_id = 0;
			// get the default store id
			$this->load->model('setting/store');
			$stores = $this->model_setting_store->getStores();
			if (!empty($stores)) {
				$store_id = $stores[0]['store_id'];
			}
		}
		return $store_id;
	}
	
        public function searchAffiliate(){
            $this->load->model('pos/pos');
            $q = $this->request->get['q'];
            $json = $this->model_pos_pos->searchAffiliate($q);
            return $this->response->setOutput(json_encode($json));
        }
        
        
        public function searchCustomer(){
            $this->load->model('pos/pos');
            $q = $this->request->get['q'];
            $json = $this->model_pos_pos->searchCustomer($q);
            return $this->response->setOutput(json_encode($json));
        }


public function searchProductsAu(){
            
            $this->load->model('pos/pos');
            if (isset($this->request->get['q'])) {
                $q = $this->request->get['q'];
            } else {
                $q = '';
            }
            
            if (isset($this->request->get['page'])) {
                $page = $this->request->get['page'];
            } else {
                $page = 1;
            }
            
            $limit    = 20;
            $offset   = ($page-1)*$limit;

            
            $products = $this->model_pos_pos->searchProducts($q,$limit,$offset);
            
	return $this->response->setOutput(json_encode($products));

}
        
	public function searchProducts(){
            
            $this->load->model('pos/pos');
           //echo $this->request->post['page'];
           //exit('done');
            if (isset($this->request->post['q'])) {
                $q = $this->request->post['q'];
            } else {
                $q = '';
            }
            
            if (isset($this->request->post['page'])) {
                $page = $this->request->post['page'];
            } else {
                $page = 1;
            }
            
            $limit    = 20;
            $offset   = ($page-1)*$limit;
            $total    = $this->model_pos_pos->total_search_products($q); 
            
            $products = $this->model_pos_pos->searchProducts($q,$limit,$offset);
//
            //check if last page 
            $total_pages = ceil($total/$limit);
            if($total_pages > $page){
                $json['has_more'] = 1;
            }
             
            $json['products'] = array();
            foreach ($products as $product) {
                    $json['products'][] = array('type' => 'P',
                        'name' => $product['name'],
                        'image' => !empty($product['image']) ? '../image/'.$product['image'] : '../image/no_image.jpg',
                        'price_text' => $this->currency->format($product['price']), //, $currency_code, $currency_value
                        //'stock_text' => $product['quantity'], // . ' ' . $this->language->get('text_remaining'),
                       // 'hasOptions' => $product['options'] ? '1' : '0',
                        'id' => $product['product_id'],
			'store_price_text' => $this->currency->format($product['price']),//$product['store_price']),
                        'stock_text' => $product['quantity'],
                       
                    );
            }

            return $this->response->setOutput(json_encode($json));
        }
        
        public function getCategoryItems() 
        {
			$mcrypt=new MCrypt();
			$this->load->language('api/cart');
		
			$json = array();
			$log =new Log("web-prdinv-".date('Y-m-d').".log");
			$log->write('getCategoryItems called');
			$log->write($this->request->get);
			$log->write($this->request->post);
			$this->catmodel('catalog/product');
		
			$json = array('success' => true, 'products' => array());

        
			if(!empty($this->request->post['category_id']))
			{
					$category_id = $this->request->post['category_id'];
            }
			else if (!empty($this->request->get['category_id']))
			{
					$category_id = $this->request->get['category_id'];
			}
			$log->write($category_id);
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
			$store=$this->config->get('config_store_id');
			
			$log->write($store);
			if($this->request->get['billtype']==1)/////////open 
			{
				$this->config->set('config_store_id',0);
			}
			
			if($this->request->get['billtype']==1)
			{
				$products = $this->model_catalog_product->getProducts(array(
					'filter_category_id'        => $category_id,
					'quantity_check'=>0,
					'store_id'=>$store,'start'=>$offset,$limit=>20,
					'for_store'=>$this->config->get('config_store_id')
					));
			}
			else
			{
				$products = $this->model_catalog_product->getProducts(array(
					'filter_category_id'        => $category_id,
					'quantity_check'=>1,
					'store_id'=>$store,'start'=>$offset,$limit=>20,
					'for_store'=>$this->config->get('config_store_id')
				));
			}
			//if($this->request->get['billtype']==1)/////////open 
			{
				$log->write("before call to own_products  in product/products");
				$this->config->set('config_store_id',$store);
				$own_products = $this->model_catalog_product->getProducts(array(
				'filter_category_id'        => $category_id,
				'store_id'=>$this->config->get('config_store_id'),
				'for_store'=>'own'.$store
				));//'quantity_check'=>1,
				//$log->write('own_products');
				//$log->write(($own_products));
			}
			//$log->write("data products");
			//$log->write($products);
			//print_r($own_products);exit;
			foreach ($products as $product) 
			{ 
				
					
				
				if(empty($product))
				{                            
                continue;
				}
				$log->write($product);
				$log->write('array_key_exists');
				$log->write(($product['product_id']));
				$log->write(($own_products));
				$log->write(array_key_exists($product['product_id'],$own_products));
				if(!empty($own_products)&&(array_key_exists($product['product_id'],$own_products)))
				{
					$log->write('in if own_products is not empty and product_id in own_products for product_id: '.$product['product_id']);
					$log->write($own_products[$product['product_id']]['pd']['store_price']);
					$product['price']=$own_products[$product['product_id']]['pd']['store_price'];
					$product['favourite']=$own_products[$product['product_id']]['pd']['favourite'];
					$product['store_tax_amt']=$own_products[$product['product_id']]['pd']['store_tax_amt'];
				}
				else
				{
					//$log->write('in else own_products is not empty and product_id in own_products for product_id: '.$product['product_id']);
					if(empty($product['pd']['store_price'])||$product['pd']['store_price']==0.0000)
					{
						//$log->write('in if store_price is empty or 0 for product_id: '.$product['product_id']);
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
				if(empty($product['price']))
				{
					$product['price']=$product['price_tax'];
				}
				$this->adminmodel('setting/setting');
				
				if(empty($product['favourite'])) 
				{						
					$product['favourite']=0;   
				}
				if(empty($product['price']))
				{
					$product['price']=0.0;
				}
				$json['products'][] = array(
					'type' => 'P',
					'id'			=> $product['product_id'],
					'pid'			=> $mcrypt->encrypt($product['product_id']),
					'name'			=> $product['name'],
					'favourite'			=> $product['favourite'],
					'chemical_name' => $product['model'],
                    'image' => $image,
                    'price_text' => $this->currency->format($product['price']), 
                    'stock_text' => $product['quantity'],
					'tax' => $product['store_tax_amt'],
					
					'store_price_text' => $this->currency->format($product['price']+$product['store_tax_amt']),	
					);
			}//////////foreach end here
        	return $this->response->setOutput(json_encode($json));
		       
	}
	
	
	public function getProductOptions() {
		$json = array();
		$option_data = array();
		
		$this->load->model('catalog/product');
		$this->load->model('catalog/option');
		$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);
		
		foreach ($product_options as $product_option) {
			$option_info = $this->model_catalog_option->getOption($product_option['option_id']);
			
			if ($option_info) {				
				if ($option_info['type'] == 'select' || $option_info['type'] == 'radio' || $option_info['type'] == 'checkbox' || $option_info['type'] == 'image') {
					$option_value_data = array();
					
					foreach ($product_option['product_option_value'] as $product_option_value) {
						$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);
				
						if ($option_value_info) {
							$option_value_data[] = array(
								'product_option_value_id' => $product_option_value['product_option_value_id'],
								'option_value_id'         => $product_option_value['option_value_id'],
								'name'                    => $option_value_info['name'],
								'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
								'price_prefix'            => $product_option_value['price_prefix']
							);
						}
					}
				
					$option_data[] = array(
						'product_option_id' => $product_option['product_option_id'],
						'option_id'         => $product_option['option_id'],
						'name'              => $option_info['name'],
						'type'              => $option_info['type'],
						'option_value'      => $option_value_data,
						'required'          => $product_option['required']
					);	
				} else {
					$option_data[] = array(
						'product_option_id' => $product_option['product_option_id'],
						'option_id'         => $product_option['option_id'],
						'name'              => $option_info['name'],
						'type'              => $option_info['type'],
						'option_value'      =>    $product_option['value'],
						'required'          => $product_option['required']
					);				
				}
			}
		}
		
		$json['option_data'] = $option_data;
		$this->response->setOutput(json_encode($json));
	}
	
	public function addOrder() {
		
		unset($this->session->data['shipping_method']);
                 
                $data = array();
                
                //validation 
                $errors = '';
               
                $payment_method = $this->request->post['payment_method'];
                $is_guest = $this->request->post['is_guest'];
                $customer_id = $this->request->post['customer_id'];
                $card_no = $this->request->post['card_no'];
                $data['comment'] = $this->request->post['comment'];
                 
                if($is_guest=='false' && $customer_id==''){
                    $errors .= 'Select the customer.<br />';
                }
                
                if(($payment_method  == 'Card') && $card_no==''){
                    $errors .= 'Enter the card number.<br />';
                }
                
                if($errors != ''){                   
                    $data['errors'] = $errors;
                    $this->response->setOutput(json_encode($data));
                    return;
                }
                
		$this->load->model('pos/pos');
                
		$data['store_id'] = $this->user->getStoreId();
		
		$default_country_id = $this->config->get('config_country_id');
		$default_zone_id = $this->config->get('config_zone_id');
                
                $data['shipping_country_id'] = $default_country_id;
		$data['shipping_zone_id'] = $default_zone_id;
		$data['payment_country_id'] = $default_country_id;
		$data['payment_zone_id'] = $default_zone_id;
		$data['customer_id'] = 0;
		$data['customer_group_id'] = 1;
		$data['firstname'] = 'Walkin';
		$data['lastname'] = "Customer";
		$data['email'] = '';
		$data['telephone'] = '';
		$data['fax'] = '';
                
		$data['payment_firstname'] = 'Walkin';
		$data['payment_lastname'] = "Customer";
		$data['payment_company'] = '';
		$data['payment_company_id'] = '';
		$data['payment_tax_id'] = '';
		$data['payment_address_1'] = '';
		$data['payment_address_2'] = '';
		$data['payment_city'] = '';
		$data['payment_postcode'] = '';
		$data['payment_country_id'] = '';
		$data['payment_zone_id'] = '';
		$data['payment_method'] = $payment_method;
		$data['payment_code'] = 'in_store';
		$data['shipping_firstname'] = '';
		$data['shipping_lastname'] = '';
		$data['shipping_company'] = '';
		$data['shipping_address_1'] = '';
		$data['shipping_address_2'] = '';
		$data['shipping_city'] = '';
		$data['shipping_postcode'] = '';
		$data['shipping_country_id'] = '';
		$data['shipping_zone_id'] = '';
		$data['shipping_method'] = 'Pickup From Store';
		$data['shipping_code'] = 'pickup.pickup';		
		$data['order_status_id'] = 5;
		$data['affiliate_id'] = isset( $this->request->post['affiliate_id'])? $this->request->post['affiliate_id']:0;
                $data['card_no'] = $card_no;
		$data['user_id'] = $this->user->getId();
                
                //override for customer 
                if($is_guest=='false'){
                    
                    $customer = $this->model_pos_pos->getCustomer($customer_id);
                    
                    $data['customer_id'] = $customer_id;
                    $data['customer_group_id'] = $customer['customer_group_id'];
                    $data['firstname'] = $customer['firstname'];
                    $data['lastname'] = $customer['lastname'];
                    $data['email'] = $customer['email'];
                    $data['telephone'] = $customer['telephone'];
                    $data['fax'] = $customer['fax'];

                    $data['payment_firstname'] = $customer['firstname'];
                    $data['payment_lastname'] = $customer['lastname'];
                }				
               
                //get product list 
                $this->load->library('customer');
                $this->customer = new Customer($this->registry);
                
                $this->load->library('tax');//
                $this->tax = new Tax($this->registry);
            
                $this->load->library('pos_cart');//
                $this->cart = new Pos_cart($this->registry);
                 
                $data['order_product'] = array();
                
                foreach ($this->cart->getProducts() as $product) {
			
			$option_data = array();

			foreach ($product['option'] as $option) {
                            
				if ($option['type'] != 'file') {
					$value = $option['option_value'];	
				} else {
					$filename = $this->encryption->decrypt($option['option_value']);

					$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
				}				

				$option_data[] = array(								   
					'product_option_id'  => $option['product_option_id'],
                                        'product_option_value_id'  => $option['product_option_value_id'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
					'type'  => $option['type'],
                                        'name'  => $option['name'],
				);
			}

			$data['order_product'][] = array(
                                'product_id'   => $product['product_id'],
				'name'         => $product['name'],
				'model'        => $product['model'], 
				'quantity'     => $product['quantity'],                            
				'price'        => $product['price'],
				'total'        => $product['price']*$product['quantity'],
                                'tax'          => $this->tax->getTax($product['price'], $product['tax_class_id']),
                                'reward'       => $product['reward'],
				'order_option' => $option_data,
			);
		}//foreach products 
                
                $this->load->model('pos/extension');

                $total_data = array();					
                $total = 0;
                $taxes = $this->cart->getTaxes();

                // Display prices
                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                        $sort_order = array(); 

                        $results = $this->model_pos_extension->getExtensions('total');

                        foreach ($results as $key => $value) {
                                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                        }

                        array_multisort($sort_order, SORT_ASC, $results);

                        foreach ($results as $result) {
                                if ($this->config->get($result['code'] . '_status')) {
                                        $this->load->model('pos/' . $result['code']);

                                        $this->{'model_pos_' . $result['code']}->getTotal($total_data, $total, $taxes);
                                }

                                $sort_order = array(); 

                                foreach ($total_data as $key => $value) {
                                        $sort_order[$key] = $value['sort_order'];
                                }

                                array_multisort($sort_order, SORT_ASC, $total_data);			
                        }
                }

                $data['order_total'] = $total_data;
                
                if(isset($this->session->data['voucher'])){
                    $data['order_voucher'] = $this->session->data['voucher'];
                }
               
                //end of order total 
                $json['customer_name'] = $data['firstname'].' '.$data['lastname'];
		$json['customer_mobile'] = $data['telephone'];
               
                $order_id = $this->model_pos_pos->addOrderOpen($data);
              
                unset($this->session->data['discount_amount']);
                
                //recore for counter payment 
                if($payment_method  == 'Card'){                    
                    $cash = 0;
                    $card = $total;
                }else{
                    $cash = $total;
                    $card = 0;
                }
                 
                $data = array(
                 'user_id' => $this->user->getId(),
                 'cash' => $cash,
                 'card' => $card,                 
                );
                
                $this->model_pos_pos->addPayment($data);
               
                $json['order_id'] = $order_id;
                
                $balance = $this->model_pos_pos->get_user_balance($this->user->getId());
                
                $json['cash'] = $this->currency->format($balance['cash']);
                 
                $json['card'] = $this->currency->format($balance['card']);
  
                $json['success'] = 'Success: new order placed with ID: '.$order_id;
                $json['order_id'] = $order_id;
                $url.="&order_id=".$order_id;
                //echo "done45";
               // $this->response->setOutput(json_encode($json));	
                
                
                $this->response->redirect($this->url->link('pos/pos/order_summary', 'token=' . $this->session->data['token'] . $url,'SSL'));

                
                
	}//END add order 
        
	  public function getProductByBarcode() {
            
          // print_r("done");exit;
            
            
            if ($this->request->post['barcode']) {
                $barcode = $this->request->post['barcode'];
            } else {
                $barcode = false;
            }
            
            $this->load->model('pos/pos');
                
            $product = $this->model_pos_pos->getProductByBarcode($barcode);
            
            $json['product_id'] = $product['product_id'];
                     
            $json['has_option'] = $product['options'] ? '1' : '0';
            
            $this->response->setOutput(json_encode($json));	
        }
       
        public function logout(){
            $this->user->logout();

            unset($this->session->data['token']);

            $this->response->redirect($this->url->link('pos/pos', '', 'SSL'));
        }
        //new addition
        public function addcustomer(){
             
             //
              $json = array();
             
             if($this->request->post['firstname']==''){
                 $json['error'] = 'Error: Please firstname name.';
                 echo json_encode($json);
                 die();
             }
             if($this->request->post['lastname']==''){
                 $json['error'] = 'Error: Please enter lastname name.';
                 echo json_encode($json);
                 die();
             }
             if($this->request->post['telephone']==''){
                 $json['error'] = 'Error: Please enter telephone name.';
                 echo json_encode($json);
                 die();
             }

             if($this->request->post['card']=='')
                 {
			$this->request->post['card']=="0";
                 	//$json['error'] = 'Error: Please enter card number.';
	                // echo json_encode($json);
        	        // die();
             }
             if($this->request->post['village']=='')
                 {
                 $json['error'] = 'Error: Please enter village name.';
                 echo json_encode($json);
                 die();
             }
             
             //check mobilenummber exits
             $this->load->model('sale/customer');       
             	if (isset($this->request->post['telephone']) && ($this->request->server['REQUEST_METHOD'] == 'POST')) 
                    {
			$customer_info = $this->model_sale_customer->getCustomerByEmail($this->request->post['telephone']);
		}
             
             if (empty($customer_info)){
                 unset($this->session->data['cid']);
             $this->request->post['email']=$this->request->post['telephone'];
             $this->request->post['fax']=$this->request->post['telephone'];
             $this->request->post['password']=$this->request->post['telephone'];
             $this->request->post['newsletter']='0';        
             $this->request->post['approved']='1';
             $this->request->post['status']='1';
             $this->request->post['safe']='1';
              $this->request->post['address_1']= $this->request->post['village'];
                 $this->request->post['address_2']= $this->request->post['village'];
                 $this->request->post['city']= $this->request->post['village'];
                 $this->request->post['company']='Unnati';
             $this->request->post['country_id']='0';
             $this->request->post['zone_id']='0';
             $this->request->post['postcode']='0';
             $this->request->post['store_id']=$this->user->getStoreId();             
             $this->request->post['address']=array($this->request->post);
             $this->model_sale_customer->addCustomer($this->request->post);
             
             if(isset($this->session->data['cid']))
             {
                 $json['id']=$this->session->data['cid'];
             }
             }else{
                 $json['error'] = 'Error: customer already exists with this telephone.';
                 echo json_encode($json);
                 die();
             }
             //html update              
             
             
             /*$json['html'] = '<tr><td>'.$row['name'].'</td><td align="center">'.$row['date_created'].'</td><td align="center">';
             $json['html'].= "[<a data_cart_holder_id='".$row["cart_holder_id"]."' href='#' class='select'>Select</a>]&nbsp;";
             $json['html'].= "[<a data_cart_holder_id='".$row["cart_holder_id"]."' href='#' class='delete'>Delete</a>]</td></tr>"; 
             */
             $json['success'] = 'Success: customer added.';
             
             echo json_encode($json);
             //                                       
        }
        
        
         public function customer(){
             
            
            $this->document->setTitle($this->language->get('heading_profile'));
            $this->adminmodel('sale/customer');
            $data['token'] = $this->request->get['token'];
             if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}
		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}						          
             
                if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}
                if (isset($this->error['card'])) {
			$data['error_card'] = $this->error['card'];
		} else {
			$data['error_card'] = '';
		}
		if (isset($this->request->get['customer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$customer_info = $this->model_sale_customer->getCustomer($this->request->get['customer_id']);
		}
                //$customer_info = $this->model_sale_customer->getCustomer($this->user->getId());
                $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => 'Dashboard',
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'Profile',
			'href' => $this->url->link('pos/pos/customer', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
                $this->adminmodel('sale/customer_group');
		
		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($customer_info)) {
			$data['firstname'] = $customer_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($customer_info)) {
			$data['lastname'] = $customer_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($customer_info)) {
			$data['email'] = $customer_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($customer_info)) {
			$data['telephone'] = $customer_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($this->request->post['village'])) {
			$data['village'] = $this->request->post['village'];
		} elseif (!empty($customer_info)) {
			$data['village'] = $customer_info['village'];
		} else {
			$data['village'] = '';
		}
                if (isset($this->request->post['card'])) {
			$data['card'] = $this->request->post['card'];
		} elseif (!empty($customer_info)) {
			$data['card'] = $customer_info['card'];
		} else {
			$data['card'] = '';
		}
               $data['heading_profile'] = $this->language->get('heading_profile'); 
                $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');              
            $data['column_left'] = $this->load->controller('common/column_left');
             $this->response->setOutput($this->load->view('default/template/pos/customer_form.tpl', $data));
         }
        //end addition
        public function orders(){
            
            $this->document->setTitle($this->language->get('heading_title'));
		
            $limit = 6;//per page limit 
            
            $page = 1;            

            $data = array(
                'order'  => 'DESC',
                'start'  => ($page - 1) * $limit,
                'limit'  => $limit
            );
            
            $this->load->model('sale/order');
            
            $order_total = $this->model_sale_order->getTotalOrders($data);
            
            $this->load->model('localisation/order_status');
            $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
            
            //get orders 
            $result = $this->model_sale_order->getOrderspos($data);
            
            $data['rows'] = array();
            foreach($result as $row){
                $row['total'] = $this->currency->format($row['total']);
                $data['rows'][] = $row;
            }
            
            $data['text_missing'] = 'Missing Orders';
            $data['currency_code'] = $this->config->get('config_currency');
	    $data['currency_value'] = '1.0';
	    $data['store_id'] = $this->user->getStoreId();
	    $data['token'] = $this->session->data['token'];
                
            $pagination = new Pagination();
            $pagination->total = $order_total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = $this->url->link('pos/pos/ordersAJAX', 'token=' . $this->session->data['token'].'&page={page}', 'SSL');

            $data['pagination'] = $pagination->render();

            $data['filter_order_id'] = '';
            $data['filter_customer'] = '';
            $data['filter_order_status_id'] = '';
            $data['filter_total'] = '';
            $data['filter_date_added'] = '';
            $data['filter_date_modified'] = '';
            
            $this->response->setOutput($this->load->view('pos/orders.tpl', $data));
        }
        
        public function getOrder(){

            $this->load->model('sale/order');

            $json = array();

            $this->load->library('customer');
            $this->customer = new Customer($this->registry);

            $this->load->library('tax');//
            $this->tax = new Tax($this->registry);

            $this->load->library('pos_cart');//
            $this->cart = new Pos_cart($this->registry);

            $this->load->model('catalog/product');

            $order_products = $this->model_sale_order->getOrderProducts($this->request->get['order_id']);
            $this->cart->clear();
            foreach ($order_products as $order_product) {
                if (isset($order_product['order_option'])) {
                    $order_option = $order_product['order_option'];
                } elseif (isset($this->request->get['order_id'])) {
                    $order_option = $this->model_sale_order->getOrderOptions($this->request->get['order_id'], $order_product['order_product_id']);
                } else {
                    $order_option = array();
                }

                $this->cart->add($order_product['product_id'], $order_product['quantity'], $order_option);
            }

               //html for cart
            $json['products'] = array();
                      
            foreach ($this->cart->getProducts() as $product) {

                    $option_data = array();

                    foreach ($product['option'] as $option) {
                            if ($option['type'] != 'file') {
                                    $value = $option['option_value'];	
                            } else {
                                    $filename = $this->encryption->decrypt($option['option_value']);

                                    $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                            }				

                            $option_data[] = array(								   
                                    'name'  => $option['name'],
                                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
                                    'type'  => $option['type']
                            );
                    }

                    // Display prices
                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                            $price = $this->currency->format($product['price']);
                    } else {
                            $price = false;
                    }

                    // Display prices
                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                            $total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']);
                    } else {
                            $total = false;
                    }
                          
                    //tax 
                    $a = $product['price']*$product['quantity'];
                    $b = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
                    $tax = $this->currency->format($b - $a);
                    
                    $json['products'][] = array(
                            'key'       => $product['key'],
                            'name'      => $product['name'],
                            'model'     => $product['model'], 
                            'option'    => $option_data,
                            'quantity'  => $product['quantity'],
                            'price'     => $price,	
                            'total'     => $total,
                            'tax'       => $tax,
                            'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                    );
            }//foreach product in cart generate html 

            // Totals
            $this->load->model('pos/extension');

            $total_data = array();					
            $total = 0;
            $taxes = $this->cart->getTaxes();

            // Display prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $sort_order = array(); 

                    $results = $this->model_pos_extension->getExtensions('total');

                    foreach ($results as $key => $value) {
                            $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                    }

                    array_multisort($sort_order, SORT_ASC, $results);

                    foreach ($results as $result) {
                            if ($this->config->get($result['code'] . '_status')) {
                                    $this->load->model('pos/' . $result['code']);

                                    $this->{'model_pos_' . $result['code']}->getTotal($total_data, $total, $taxes);
                            }

                            $sort_order = array(); 

                            foreach ($total_data as $key => $value) {
                                    $sort_order[$key] = $value['sort_order'];
                            }

                            array_multisort($sort_order, SORT_ASC, $total_data);			
                    }
            }

            //get order comment 
            $json['comment'] = $this->db->query('select comment from `'.DB_PREFIX.'order` where order_id="'.$this->request->get['order_id'].'"')->row['comment'];
            
            $json['total_data'] = $total_data;
            $json['total'] = $this->currency->format($total);
            
            //customer info             
            $this->load->model('pos/pos');
            $json['customer'] = $this->model_pos_pos->getCustomer['customer_id'];
            $json['order_id'] = $this->request->get['order_id'];
            echo json_encode($json);
        }//get order 
        
        public function hold_cart_delete(){
            $this->load->model('pos/pos');
           // echo "hi";
            echo $this->request->post['cart_holder_id'];
            $this->model_pos_pos->hold_cart_delete($this->request->post['cart_holder_id']);
             //echo "hello";
        }
        
        public function hold_cart(){
            
             $json = array();
             
            if($_POST['name']==''){
                 $json['error'] = 'Error: Please enter hold name.';
                 echo json_encode($json);
                 die();
             }
            
             $data = array(
                 'name' => $this->request->post['name'],
                 'cart' => $this->session->data['cart'],
                 'user_id' =>  $this->user->getId(),
             );
                  
             $this->load->model('pos/pos');
             //echo 'done';   
             $id = $this->model_pos_pos->hold_cart($data);
            
             //html update              
             $row = $this->model_pos_pos->hold_cart_select($id);
               // echo 'done1'; 
             $json['html'] = '<tr><td>'.$row['name'].'</td><td align="center">'.$row['date_created'].'</td><td align="center">';
             $json['html'].= "[<a data_cart_holder_id='".$row["cart_holder_id"]."' href='#' class='select'>Select</a>]&nbsp;";
             $json['html'].= "[<a data_cart_holder_id='".$row["cart_holder_id"]."' href='#' class='delete'>Delete</a>]</td></tr>"; 
             
             $json['success'] = 'Success: cart moved to hold list.';
             
             echo json_encode($json);
        }
        
        public function hold_cart_select(){
            
            $this->load->model('pos/pos');
             
            $row = $this->model_pos_pos->hold_cart_select($_POST['cart_holder_id']);
            
            $this->session->data['cart'] = unserialize($row['cart']);
             
            $json['products'] = array();
                      
            $this->load->library('customer');
            $this->customer = new Customer($this->registry);

            $this->load->library('tax');//
            $this->tax = new Tax($this->registry);

            $this->load->library('pos_cart');//
            $this->cart = new Pos_cart($this->registry);
            
            foreach ($this->cart->getProducts() as $product) {

                    $option_data = array();

                    foreach ($product['option'] as $option) {
                            if ($option['type'] != 'file') {
                                    $value = $option['option_value'];	
                            } else {
                                    $filename = $this->encryption->decrypt($option['option_value']);

                                    $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                            }				

                            $option_data[] = array(								   
                                    'name'  => $option['name'],
                                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
                                    'type'  => $option['type']
                            );
                    }

                    // Display prices
                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                            $price = $this->currency->format($product['price']);
                    } else {
                            $price = false;
                    }

                    // Display prices
                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                            $total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']);
                    } else {
                            $total = false;
                    }
                                                            
                    //tax 
                    $a = $product['price']*$product['quantity'];
                    $b = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
                    $tax = $this->currency->format($b - $a);

                    $json['products'][] = array(
                        'key'       => $product['key'],
                        'name'      => $product['name'],
                        'model'     => $product['model'], 
                        'option'    => $option_data,
                        'quantity'  => $product['quantity'],
                        'price'     => $price,	
                        'total'     => $total,
                        'tax'       => $tax,
                        'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                    );
            }//foreach product in cart generate html 

            // Totals
            $this->load->model('pos/extension');

            $total_data = array();					
            $total = 0;
            $taxes = $this->cart->getTaxes();

            // Display prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $sort_order = array(); 

                    $results = $this->model_pos_extension->getExtensions('total');

                    foreach ($results as $key => $value) {
                            $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                    }

                    array_multisort($sort_order, SORT_ASC, $results);

                    foreach ($results as $result) {
                            if ($this->config->get($result['code'] . '_status')) {
                                    $this->load->model('pos/' . $result['code']);

                                    $this->{'model_pos_' . $result['code']}->getTotal($total_data, $total, $taxes);
                            }

                            $sort_order = array(); 

                            foreach ($total_data as $key => $value) {
                                    $sort_order[$key] = $value['sort_order'];
                            }

                            array_multisort($sort_order, SORT_ASC, $total_data);			
                    }
            }

            $json['total_data'] = $total_data;
            $json['total'] = $this->currency->format($total);
            
            $json['success'] = 'Success: cart restored from hold list.';
             
            echo json_encode($json);
        }
        public function product_payment()
        {
            $this->session->data['transid']= uniqid();
            $this->load->library('customer');
            $this->customer = new Customer($this->registry);
            
            $data['cart_detail']=$this->request->post;
            $data['cart_detail_json']=json_encode($this->request->post);
            //print_r();exit;
            $this->adminmodel('pos/pos');
            $data['customer'] = $this->model_pos_pos->getCustomer(['customer_id']);
            $data['token'] = $this->session->data['token'];
            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');
            $data['column_left'] = $this->load->controller('common/column_left');
            $this->response->setOutput($this->load->view('default/template/pos/payment.tpl',$data));
        }
        public function submit_order() 
        {
		$log=new Log("addorder-web-".date('Y-m-d').".log");
		$log->write($this->request->get);
		$log->write($this->request->post);
                $mcrypt=new MCrypt();
		
		$strid=$this->user->getStoreId();
		$username=$this->user->getId();
		$order_total=urldecode($this->request->post['order_total']);
                $order_details=json_decode(urldecode($this->request->post['order_detail']),true);
		//print_r(json_decode(urldecode($this->request->post['order_detail']),true));
                $prd_dtl=array();
                for($i=0;$i<count($order_details['product_id']);$i++)
                {
                    $prd_dtl[]=array(   
                                    "product_tax"=>$order_details['tax'][$i],
                                    "product_quantity"=>$order_details['quantity'][$i],
                                    "product_id"=>$order_details['product_id'][$i],
                                    "product_name"=>$order_details['name'][$i],
                                    "product_price"=>$order_details['price'][$i],
                                    "category_id"=>$mcrypt->encrypt($order_details['category_id'][$i])
                                    );
                }
                
		$request = HTTP_CATALOG."index.php?route=mpos/openretailer/addorder";
		$log->write($request);
		$fields_string .= 'prddtl'.'='.$mcrypt->encrypt(json_encode($prd_dtl)).'&'; 
		$fields_string .= 'payment_method'.'='.$mcrypt->encrypt('Cash').'&'; 
		//$fields_string .= 'customer_id'.'='.$mcrypt->encrypt().'&'; 
		$fields_string .= 'customer_mob'.'='.$mcrypt->encrypt($this->request->post['customer_mob']).'&'; 
		$fields_string .= 'store_id'.'='.$mcrypt->encrypt($strid).'&'; 
		$fields_string .= 'user_id'.'='.$mcrypt->encrypt($username).'&'; 
		$fields_string .= 'affiliate_id'.'='.$mcrypt->encrypt('0').'&'; 
		$fields_string .= 'utype'.'='.$mcrypt->encrypt('11').'&'; 
		$fields_string .= 'eid'.'='.$mcrypt->encrypt('1111').'&'; 

		$fields_string .= 'fname'.'='.$mcrypt->encrypt(urldecode($this->request->post['farmer_name'])).'&'; 
		$fields_string .= 'lname'.'='.$mcrypt->encrypt(urldecode('0')).'&'; 
		$fields_string .= 'stname'.'='.$mcrypt->encrypt('store').'&'; 
		$fields_string .= 'vname'.'='.$mcrypt->encrypt(urldecode('0')).'&'; 
		$fields_string .= 'cde'.'='.$mcrypt->encrypt(urldecode('0')).'&'; 
		
		$fields_string .= 'chkepos'.'='.$mcrypt->encrypt('0').'&'; 
		$fields_string .= 'docs'.'='.$mcrypt->encrypt('0').'&'; 
		$fields_string .= 'doc_number'.'='.$mcrypt->encrypt('0').'&'; 
		
		
		$fields_string .= 'chkcash'.'='.$mcrypt->encrypt('0').'&'; 
		$fields_string .= 'amtcash'.'='.$mcrypt->encrypt($this->request->post['cash']).'&'; 
		
		$fields_string .= 'transid'.'='.$mcrypt->encrypt($this->session->data['transid']).'&'; 
		$fields_string .= 'deliveryreceipt'.'='.$mcrypt->encrypt('yes').'&'; 
		$fields_string .= 'deliverymode'.'='.$mcrypt->encrypt('2').'&'; 
		
		$fields_string .= 'approvaltype'.'='.$mcrypt->encrypt('1').'&'; 
		$fields_string .= 'credit_amount'.'='.$mcrypt->encrypt($this->request->post['credit']).'&'; 
                $fields_string .= 'billtype'.'='.$mcrypt->encrypt($this->request->post['billtype']).'&'; 
		rtrim($fields_string, '&');
		$log->write($fields_string);
		$log->write('transid : '.$this->session->data['transid']);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_USERAGENT,'akshamaala');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
            		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string); 
		$json =curl_exec($ch);
		if(empty($json))
                {
                    $log->write(curl_error($ch));	
   		}
		curl_close($ch); 
		$log->write($json);
		$return_val=json_decode($json,TRUE);
		$log->write($return_val);
                
                //print_r(($return_val));exit;
		if((empty($return_val['error']))  && (!empty($return_val['success']))) ////&& ($return_val['error']=='Warning: Please enter a coupon code!') 
		{
			$log->write('no error');
			$data['success']=$return_val['success'];
			$data['order_id']=$return_val['order_id'] ;
                        $data['order_id_encrypted']=$mcrypt->encrypt($return_val['order_id']) ;
			$data['invoice_no']=$return_val['invoice_no'] ;
			$data['orddate']=$return_val['orddate']; 
			$data['gtax']=$mcrypt->decrypt($return_val['gtax']);  
			$ttax=json_decode($data['gtax'],TRUE);
			$log->write($ttax);
			$tax_return='';
			foreach ($ttax as $key => $value) 
			{
			    $log->write($value['title']); 
			    $log->write($value['value']); 
			    $finaltax = round(($value['value'] /  2), 2);
			    $log->write( $value['title'].'  '.$finaltax); 
			    if (strpos($value['title'], '18') !== false) 
			    {	
				$tax_return.="CGST @9% ".$finaltax."</br>" ;
				$tax_return.="SGST @9% ".$finaltax."</br>" ;
			    }
  			    if (strpos($value['title'], '12') !== false) 
			    {

				$tax_return.="CGST @6% ".$finaltax."</br>" ;
				$tax_return.="SGST @6% ".$finaltax."</br>" ;
			    }
			    if (strpos($value['title'], '5') !== false) 
			    {

			      	$tax_return.="CGST @2.5% ".$finaltax."</br>" ;
				$tax_return.="SGST @2.5% ".$finaltax."</br>" ;
			    }
			    if (strpos($value['title'], '28') !== false) 
			    {	
			  	$tax_return.="CGST @14% ".$finaltax."</br>" ;
				$tax_return.="SGST @14% ".$finaltax."</br>" ;
			     }

			  }///////foreach end here
			   $data['gtax']=$tax_return;

			  $data['error']='';
			  
		}///////else of if no error in addorder is end here
		else if((!empty($return_val['error'])) && ($return_val['success']=='-1'))
		{
			$log->write('some error in addorder : - '.$return_val['error']);
			$data['success']='';
			$data['order_id']='';
			$data['invoice_no']='';
			$data['error']=$return_val['error'];
		}
		else///////// any other error 
		{
			$log->write('some error : - '.$return_val['error']);
			$data['success']='';
			$data['order_id']='';
			$data['invoice_no']='';
			$data['error']=$return_val['error'];
		}
		//print_r($data['detail']); 
		$log->write($data);
		$this->response->setOutput(json_encode($data));
		
	}
        
            public function order_summary()
            {
                $mcrypt=new MCrypt();
                $order_id=$mcrypt->decrypt($this->request->get['order_id']);       
                $this->adminmodel('sale/order');
                //$order_id=1275671234;
                if(!empty( $order_id))
                {  
                    $data['order_info']=$order_info = $this->model_sale_order->getOrder($order_id);
                    
                }
                
                $data['products'] = array();
                
		$products = $order_info['products'];//$this->model_sale_order->getOrderProducts($this->request->get['order_id']);

			foreach ($products as $product) {
				

				$data['products'][] = array(
					'order_product_id' => $product['order_product_id'],
					'product_id'       => $product['product_id'],
					'name'    	 	   => $product['name'],
					'model'    		   => $product['model'],
					
					'quantity'		   => $product['quantity'],
					'price'    		   => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    		   => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					'href'     		   => $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $product['product_id'], 'SSL')
				);
			}

			$totals = $order_info['totals']; //$this->model_sale_order->getOrderTotals($this->request->get['order_id']);
                        $tax_array=array();
			foreach ($totals as $total) 
                        {
                            $tax_rate='';
                            
                            $data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
				);
                            if($total['code']=='tax')
                            {
                                $tax_title1= split('@', $total['title']);
                                $tax_rate1= split('%', $tax_title1[1]);
                                $tax_rate= $tax_rate1[0];
                                if($tax_rate>0)
                                {
                                    $tax_array[]=array('title'=>'CGST@'.($tax_rate/2).'%','value'=>$this->currency->format(($total['value']/2), $order_info['currency_code'], $order_info['currency_value']));
                                    $tax_array[]=array('title'=>'SGST@'.($tax_rate/2).'%','value'=>$this->currency->format(($total['value']/2), $order_info['currency_code'], $order_info['currency_value'])); 
                                }
                                
                            }
			}
                $data['tax_array']=$tax_array;
                
		$data['order_status_id'] = $order_info['order_status_id'];
                
                $data['store_name'] = $order_info['store_name'];
                $data['store_address'] = $this->config->get('config_address');
                $data['store_address']= str_replace(',', ',<br>', $data['store_address']);
                $data['store_tin'] = $this->config->get('config_tin');
                $data['store_cin'] = $this->config->get('config_cin');
                $data['store_gstn'] = $this->config->get('config_gstn');
                $data['store_telephone'] = $this->config->get('config_telephone');
                $data['config_email'] = $this->config->get('config_email');
                
				$data['firstname'] = $order_info['firstname'];
                $data['firstname']= str_replace('-', ' ', $data['firstname']);
				$data['lastname'] = $order_info['lastname'];
                $data['telephone'] = $order_info['telephone'];//str_pad(substr($order_info['telephone'],5,-1),10, "X", STR_PAD_LEFT );
                /*$data['telephone'] [0]='X';
				$data['telephone'] [1]='X';
				$data['telephone'] [2]='X';
				$data['telephone'] [3]='X';
				$data['telephone'] [4]='X';
				$data['telephone'] [5]='X';
				*/
				
                $data['order_data']=json_encode($data);
                $data['header'] = $this->load->controller('common/header');
                $data['footer'] = $this->load->controller('common/footer');
                $data['column_left'] = $this->load->controller('common/column_left');
                //print_r($data);exit;
                if(!empty($order_info))
                {
                    $this->response->setOutput($this->load->view('default/template/pos/order_summary.tpl',$data));
                }
                else 
                {
                    $this->response->setOutput($this->load->view('default/template/common/not_found.tpl',$data));
                }
            }
    public function add_to_favourite()
    {        
        $log=new Log("addtofavouritedproduct-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
		$log->write('add_to_favourite called');
        $log->write($this->request->post);
        $log->write($this->request->get);
        $data=array();
       
		$data['store_id']=$this->user->getStoreId();
		if(!empty($this->request->post['product_id']))
		{
			$data['product_id']=$this->request->post['product_id'];
		}
		else if(!empty($this->request->get['product_id']))
		{
			$data['product_id']=$this->request->get['product_id'];
		}
		$data['category_id']=44;
		
        $this->adminmodel('openretailer/openretailer');
        $log->write("model");
        $log->write($data);
        $datas=array();
        if(!empty($data['product_id']) )
        {
            $log->write("in if");
           
            $prdid = $this->model_openretailer_openretailer->addtofavouritedproduct($data); 
            echo '1';
        }        
        else
        {
            $log->write("in else");
            echo '0';
        }
                
    }  
	public function remove_favourite()
    {        
        $log=new Log("addtofavouritedproduct-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
		$log->write('remove_favourite called');
        $log->write($this->request->post);
        $log->write($this->request->get);
        $data=array();
       
		$data['store_id']=$this->user->getStoreId();
		if(!empty($this->request->post['product_id']))
		{
			$data['product_id']=$this->request->post['product_id'];
		}
		else if(!empty($this->request->get['product_id']))
		{
			$data['product_id']=$this->request->get['product_id'];
		}
		
		$data['category_id']=44;
		
        $this->adminmodel('openretailer/openretailer');
        $log->write("model");
        $log->write($data);
        $datas=array();
        if(!empty($data['product_id']) )
        {
            $log->write("in if");
           
            $prdid = $this->model_openretailer_openretailer->remove_favourite($data); 
            echo '1';
        }        
        else
        {
            $log->write("in else");
            echo '2';
        }
                
    }  	
}
?>