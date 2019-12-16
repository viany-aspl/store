<?php
class ControllerCatalogProductTemp extends Controller {
private static function compare_tax_rates ($a, $b) {
		return ($a['priority'] < $b['priority']) ? -1 : 1;
	}
	function json_taxrates () {
		header('Content-Type: application/json');
		$result = array();
		
		$class_id = (int) $this->request->post['tax_class_id'];
		
		if (!isset($class_id)) {
			$result['status'] = 'error';
			$result['error'] = 'Invalid input';
		}
		else {
			$this->load->model('catalog/producttemp');
			$tax_rates = $this->model_catalog_producttemp->getTaxRates($class_id);
			usort($tax_rates, array( __CLASS__, "compare_tax_rates"));
			$result['status'] = 'ok';
			$result['tax_rates'] = $tax_rates;
		}
		$this->response->setOutput(json_encode($result));
	}
	private $error = array();
        
        public function index() {
		$this->load->language('catalog/producttemp');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/producttemp');

		$this->getList();
	}
        
        
	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}
                
                if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
                
                if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
                $data['token']=$this->session->data['token'];
		//$data['add'] = $this->url->link('catalog/producttemp/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		//$data['copy'] = $this->url->link('catalog/producttemp/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/producttemp/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
              
		$data['products'] = array();
                
                $filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);
                
                 //echo $this->user->getId();
                $this->load->model('tool/image');

		$product_total = $this->model_catalog_producttemp->getTotalProducts($filter_data);

		$results = $this->model_catalog_producttemp->getProducts($filter_data);
                //print_r($results);
		foreach ($results as $result) {
                    //echo $result['image'];
                    
                    
                    
			if (is_file(DIR_IMAGE . $result['image'])) {
				//$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				//$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;
                             
			$product_specials = $this->model_catalog_producttemp->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $product_special['price'];

					break;
				}
			}
                     //$productbyid = $this->model_catalog_producttemp->get_productbyID($result['product_id']);
                   //print_r($productbyid);
			$data['products'][] = array(
                            
				'product_id' => $result['product_id'],
				'image'      => $image,
                                
				'name'       => $result['name'],
				'model'      => $result['model'],
                                'price_tax'  => number_format($result['price_tax'], 2),
                                //'price_tax'  => number_format(round($this->model_catalog_product->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), 2), 2),
				'price'      => $result['price'],
				'special_tax' => ($special ? number_format(round($this->model_catalog_producttemp->calculate($special, $result['tax_class_id'], $this->config->get('config_tax')), 2), 2) : ''),
                                		'edit_quantity'=>$this->url->link('catalog/store_quantity/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $url, 'SSL'),
				'special'    => $special,
				'quantity'   => $result['quantity'],
				'status'     => $result['status'],
				'edit'       => $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $url, 'SSL'),
                                'squantity'   => $this->model_catalog_producttemp->getProductStoresQuantityHtml($result['product_id']),
                            
			);
		}          
                //print_r($data['products']);
                $data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $this->language->get('text_list');
		//$data['text_enabled'] = $this->language->get('text_enabled');
		//$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

                $data['column_price_tax'] = $this->language->get('column_price_tax');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_copy'] = $this->language->get('button_copy');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

                $data['config_tax_included'] = $this->config->get('config_tax_included');

		$data['token'] = $this->session->data['token'];
                
                
                if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';
                
                
                if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
                
                $data['sort_name'] = $this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . '&sort=pd.name' . $url, 'SSL');
		$data['sort_model'] = $this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . '&sort=p.model' . $url, 'SSL');
		$data['sort_price'] = $this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . '&sort=p.price' . $url, 'SSL');
		$data['sort_quantity'] = $this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . '&sort=p.quantity' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . '&sort=p.status' . $url, 'SSL');
		$data['sort_order'] = $this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $url, 'SSL');
		$data['sort_price_tax'] = $this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . '&sort=p.pricetax' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_price'] = $filter_price;
		$data['filter_quantity'] = $filter_quantity;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		//echo "here";
		$this->response->setOutput($this->load->view('catalog/product_temp_list.tpl', $data));
	}
        
        
        

               public function delete() {
		$this->load->language('catalog/producttemp');

		$this->document->setTitle($this->language->get('heading_title'));

		 $this->load->model('catalog/producttemp');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catalog_product->deleteProduct($product_id);
			}
                    $url = '';
			$this->session->data['success'] = $this->language->get('text_success');

			//$this->response->redirect($this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}
	
        
        
        
        
	public function accept()
        {
	//$mcrypt=new MCrypt();
          //print_r($this->request->get);
          //exit;
           $p_id=$this->request->get['product_id'];
            if ($this->request->get['product_id'] != '')
            {
                $this->request->get['product_id']=($this->request->get['product_id']);
                $this->load->model('catalog/producttemp');
                $updateBill = $this->model_catalog_producttemp->approved($this->request->get['product_id']);
                
                //print_r($updateBill);
                
                if($updateBill)
                {
                 $senddata = $this->model_catalog_producttemp->send_data($this->request->get['product_id']);
               // print_r($senddata);
               // exit;
             
                 $data = $this->model_catalog_producttemp->insert_send_data($senddata,$p_id);
                 $this->session->data['success'] = 'Accepted Successfully';
                //$this->response->redirect($this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . $url, 'SSL'));
                }
                else 
                {
                  $this->session->data['error_warning'] = "Oops ! Some error occur.Please try again.";
                 //$this->response->redirect($this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . $url, 'SSL'));
                 
                }
                
            }
            else
            {
                $this->session->data['error_warning'] = "You can't take this action.";
               $this->response->redirect($this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . $url, 'SSL'));
                
            }
            
            
        }

	public function rejected()
        {
	 //print_r($this->request->post);
         $this->load->model('catalog/producttemp');
         $p_id=$this->request->post['product_id'] ;
         
         //if(!empty($pid)){
        // print_r($pid);
      //  print_r($this->request->post);
         $updateBill = $this->model_catalog_producttemp->pending($p_id,$this->request->post);
         // print_r($updateBill);
                if($updateBill)
                {
                $this->session->data['success'] = 'Pending Successfully';
                //$this->response->redirect($this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . $url, 'SSL'));
                }
                else 
                {
                  $this->session->data['error_warning'] = "Oops ! Some error occur.Please try again.";
                  //$this->response->redirect($this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . $url, 'SSL'));
                 
                }
         }
         
        // else {
              // $this->session->data['error_warning'] = "You can't take this action.";
              // $this->response->redirect($this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . $url, 'SSL'));
             
         //}
            
        //}

         
         public function already_syatem()
        {
	 //print_r($this->request->post);
         $this->load->model('catalog/producttemp');
         $p_id=$this->request->post['product_id'] ;
         
         //if(!empty($pid)){
         $updateBill = $this->model_catalog_producttemp->already_syatem($p_id,$this->request->post);
         
                if($updateBill)
                {
                $this->session->data['success'] = 'Already In System';
                //$this->response->redirect($this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . $url, 'SSL'));
                }
                else 
                {
                  $this->session->data['error_warning'] = "Oops ! Some error occur.Please try again.";
                  //$this->response->redirect($this->url->link('catalog/producttemp', 'token=' . $this->session->data['token'] . $url, 'SSL'));
                 
                }
         }
	

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/producttemp')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        
        
        public function product_request_temp() 
		{
           
		$this->load->language('catalog/productrequests');
                 $this->load->model('catalog/producttemp');
                 $this->document->setTitle('Product Request');
                 
               if (isset($this->request->get['filter_company_name'])) {
			$filter_company_name = $this->request->get['filter_company_name'];
		} else {
			$filter_company_name = null;
		}
                
                if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

                if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}   
                
                
                $url='';
                 
                 if (isset($this->request->get['filter_company_name'])) {
			$url .= '&filter_company_name=' . urlencode(html_entity_decode($this->request->get['filter_company_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		
                
                 $filter_data = array(
			'filter_company_name'	  => $filter_company_name,
			'filter_name'	  => $filter_name,
			
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);
                 $results = $this->model_catalog_producttemp->getProductsRequest($filter_data);
                
                 $product_total = $results[0]['totalrows'];
                  foreach ($results as $result) {
                
			if (is_file(DIR_IMAGE . $result['image'])) {
				//$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				//$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
                        
                    $image_array = $this->model_catalog_producttemp->get_image_productbyID($result['product_id']);
                   // print_r($productbyid['image']);
                        
			$data['products'][] = array(
                            
				'product_id' => $result['product_id'],
                                'image'=> $image_array,
                                'username' => $result['username'],
								'storeinchargename' => $result['storeinchargename'],
                                'sku' => $result['sku'],
                                'HSTN' => $result['HSTN'],
				//'name'       => $result['name'],
				'model'      => $result['model'],
				'storename'      => $result['storename'],
                                'price_tax'  => number_format($result['price_tax'], 2),
                                //'price_tax'  => number_format(round($this->model_catalog_product->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), 2), 2),
				'price'      => $result['price'],
				'quantity'   => $result['quantity'],
				'status'     => $result['status'],
				'date_added'     => $result['date_added'],
			);
                        
                        
		}          
                 
            if((!empty($filter_company_name)) || (!empty($filter_name))) 
            {
            $product_request= $this->model_catalog_producttemp->getFilterProductsRequest($filter_data);
             // print_r($product_request);
            }
          
              foreach ($product_request as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				//$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				//$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$data['productrequest'][] = array(
				'pname'       => $result['prd_name'],
                                'cname'       => $result['cat_name'],
			);
		}         
               
                $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/producttemp/product_request_temp', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
                
                
                 $data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $this->language->get('text_list');
              
                $data['column_product_request'] = $this->language->get('column_product_request');
                $data['column_username'] = $this->language->get('column_username');
		$data['column_company_name'] = $this->language->get('column_company_name');
		$data['column_hsn'] = $this->language->get('column_hsn');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
                $data['text_check'] = $this->language->get('text_check');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

                $data['column_price_tax'] = $this->language->get('column_price_tax');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_copy'] = $this->language->get('button_copy');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

                $data['config_tax_included'] = $this->config->get('config_tax_included');
                
                $data['token']=$this->session->data['token'];
                
                $pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/producttemp/product_request_temp', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();
                $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));
                
                
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		//echo "here";
		$this->response->setOutput($this->load->view('catalog/product_request_temp.tpl', $data));
	}
        public function get_productimagebyID()
        { 
            $this->load->model('catalog/producttemp');
            $image_array = $this->model_catalog_producttemp->get_image_productbyID($this->request->get['product_id']);
           // print_r($image_array);
            foreach($image_array as $image_array1)
            {
                //echo '<img id="image" src="'."./././image/catalog/".$image_array1['image'].'" class="img-thumbnail" />';
           echo '<img id="image" src="'."../image/".$image_array1['image'].'" class="img-thumbnail" style="height: 100px;width: 100px;"/>';
                }
        }
        
        
        public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			  $this->load->model('catalog/producttemp');
			$this->load->model('catalog/option');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				
				'start'        => 0,
				'limit'        => $limit
			);
                        if($filter_name)
                        {
			$results = $this->model_catalog_producttemp->getProduct($filter_data);
                       
			foreach ($results as $result) {
				//print_r($result);
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					
				);
                                
                                //print_r($json);
			}
                        }
                       
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
        
        
        
         public function autocomplete_company() 
		 {
		$json = array();
//echo $this->request->get['filter_company_name'];
		if (isset($this->request->get['filter_company_name'])) {
			  $this->load->model('catalog/producttemp');
			$this->load->model('catalog/option');

			if (isset($this->request->get['filter_company_name'])) {
				$filter_company_name = $this->request->get['filter_company_name'];
			} else {
				$filter_company_name = '';
			}

			
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_company_name'  => $filter_company_name,
				
				'start'        => 0,
				'limit'        => $limit
			);
         
			$results = $this->model_catalog_producttemp->getProduct_company($filter_data);
                       //print_r($results);
			foreach ($results as $result) {
				

				$json[] = array(
					'company_id' => $result['manufacturer_id'],
					'company_name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
        
        
}