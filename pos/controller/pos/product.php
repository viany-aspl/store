<?php

class ControllerPosProduct extends Controller {
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
            $this->adminmodel('pos/product');
            $this->load->language('report/Inventory_report');
            $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
            $data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_sale'),
			'href' => $this->url->link('pos/product', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
            
            $results= $this->model_pos_product->getCompany();
            foreach($results as $result)
            {
                $data['company'][]= array(
                                   'id' => $result['company_id'],
				'name' => $result['company_name'],
                    );
            }
            $categories = $this->model_pos_product->getTopCategories();
            $data['categories'] = array();
            foreach ($categories as $category_info) 
            {
                $data['categories'][] = array(
                        'category_id' => $category_info['category_id'],
                        'image'       => $category_info['image']?$this->model_tool_image->resize($category_info['image'], 70, 70):'view/image/pos/logo.png',
                        'name'        => $category_info['name'],
                    );
            }
            
            $data['token'] = $this->session->data['token'];
            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');              
            $data['column_left'] = $this->load->controller('common/column_left');
            $this->response->setOutput($this->load->view('default/template/pos/addproduct.tpl',$data));

	}
        public function addproduct()
        {
            $this->adminmodel('pos/product');
            if(!empty($this->request->post))
            {
                $this->model_pos_product->addproduct($this->request->post); 
                $this->session->data['success'] = 'Accepted Successfully';
                $this->response->redirect($this->url->link('pos/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
            }
            else 
            {
                $this->session->data['error_warning'] = 'Some Error Occured';
                $this->response->redirect($this->url->link('pos/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
            }
        }
	public function viewproductrequest() 
        {
            $this->adminmodel('pos/product');
            if (isset($this->request->get['page'])) 
            {
		$page = $this->request->get['page'];
            } 
            else 
            {
		$page = 1;
            }
            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

            $data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_sale'),
			'href' => $this->url->link('pos/product/viewproductrequest', 'token=' . $this->session->data['token'] . $url, 'SSL')
		); 
                
            $products = $this->model_pos_product->getProductsRequest($this->user->getId());   
            //print_r($products);
            $order_total= $products[0]['totalrows'];
            foreach ($products as $product) 
            {
                $data['product'][] = array(
                        'id' => $product['product_id'],
                       'name'        => $product['model'],
                        'username'        => $product['username'],
                        'status'        => $product['status'],
                );
            }
            $data['heading_sale'] = $this->language->get('heading_sale');
            $data['heading_sale'] = $this->language->get('heading_sale');
            $data['token'] = $this->session->data['token'];
            $pagination = new Pagination();
            $pagination->total = $order_total;
            $pagination->page = $page;
            $pagination->limit = $this->config->get('config_limit_admin');
            $pagination->url = $this->url->link('pos/product/viewproductrequest', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
            $data['pagination'] = $pagination->render();
            $data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
            
            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');              
            $data['column_left'] = $this->load->controller('common/column_left');
            
            $this->response->setOutput($this->load->view('default/template/pos/viewproductrequest.tpl',$data));
            
        }
        public function autocomplete() 
        {
            $json = array();
            if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) 
            {
		$this->adminmodel('catalog/product');
		$this->adminmodel('catalog/option');

		if (isset($this->request->get['filter_name'])) 
                {
                    $filter_name = $this->request->get['filter_name'];
		} 
                else 
                {
                    $filter_name = '';
		}
                if (isset($this->request->get['filter_model'])) 
                {
                    $filter_model = $this->request->get['filter_model'];
		} 
                else 
                {
                    $filter_model = '';
		}
                if (isset($this->request->get['limit'])) 
                {
                    $limit = $this->request->get['limit'];
		} 
                else 
                {
                    $limit = 5;
		}
                $filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => $limit
			);
			if(!empty($filter_name))
			{
                $results = $this->model_catalog_product->getProducts($filter_data)->rows;
			}
                foreach ($results as $result) 
                {
                    $option_data = array();

                    $product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

                    foreach ($product_options as $product_option) 
                    {
			$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

			if ($option_info) 
                        {
                            $product_option_value_data = array();

                            foreach ($product_option['product_option_value'] as $product_option_value) 
                            {
				$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

				if ($option_value_info) 
                                {
                                    $product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix']
								);
				}
                            }
                            $option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
						);
			}
                    }
                    $json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
				);
		}
            }
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
	}
        
}
?>