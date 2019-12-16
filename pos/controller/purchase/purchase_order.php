<?php
	class ControllerPurchasePurchaseOrder extends Controller 
	{
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
		public function email()  
        {
            if (isset($this->request->get['filter_supplier'])) 
			{
                $filter_supplier =  $this->request->get['filter_supplier'];
			}

			if (isset($this->request->get['filter_date_start'])) 
			{
                $filter_date_start=$this->request->get['filter_date_start'];
			}
            else 
			{
                $filter_date_start=date('Y-')."-01-01";
            }
            if (isset($this->request->get['filter_date_end'])) 
			{
                $filter_date_end=$this->request->get['filter_date_end'];
			}
            else 
			{
                $filter_date_end=date('Y-m-d');
            }
			
			$mcrypt=new MCrypt();
			$fields_string='';
			$strid=$this->user->getStoreId();
			$username=$this->user->getId();
			$log =new Log("prdinv-".date('Y-m-d').".log");

			$log->write($strid);
			$log->write($username);
			
			$fields_string .= 'store_id'.'='.$mcrypt->encrypt($strid).'&'; 
			
			$fields_string .= 'start_date'.'='.$mcrypt->encrypt($filter_date_start).'&';
			$fields_string .= 'end_date'.'='.$mcrypt->encrypt($filter_date_end).'&';
			$fields_string .= 'action'.'='.$mcrypt->encrypt('e').'&'; 
			$request = HTTP_CATALOG."index.php?route=mpos/purchase/getlist";
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
                   
			}
			curl_close($ch); 
			//print_r($json);
			$return_val=json_decode($json,TRUE);
			$this->load->library('email');
			$email=new email($this->registry);
			$email->getstoreemail($strid);
			echo 'Email sent successfully to '.$email->getstoreemail($strid);
		}
		public function index() 
		{
			$url = '';
			$data['add'] = $this->url->link('purchase/purchase_order/add_order', 'token=' . $this->session->data['token'].'&pagetittle=Add Purchase Order' . $url, true);
			
			$data['filter'] = $this->url->link('purchase/purchase_order', 'token=' . $this->session->data['token'] . $url, true);
			
			
			$this->adminmodel('purchase/supplier_group');
			$data['supplier_groups'] = $this->model_purchase_supplier_group->get_all_supplier_groups();
			
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$this->adminmodel('purchase/supplier');
			
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}
			if (isset($this->request->get['filter_supplier'])) 
			{
				$url .= '&filter_supplier=' . $this->request->get['filter_supplier'];
			}

			if (isset($this->request->get['filter_date_start'])) 
			{
				$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
			}
			if (isset($this->request->get['filter_date_end'])) 
			{
				$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
			
			}
			if (isset($this->request->get['filter_supplier'])) 
			{
                $filter_supplier =  $this->request->get['filter_supplier'];
			}

			if (isset($this->request->get['filter_date_start'])) 
			{
                $filter_date_start=$this->request->get['filter_date_start'];
			}
            else 
			{
                $filter_date_start=date('Y-')."-01-01";
            }
            if (isset($this->request->get['filter_date_end'])) 
			{
                $filter_date_end=$this->request->get['filter_date_end'];
			}
            else 
			{
                $filter_date_end=date('Y-m-d');
            }
			 
			$start = ($page - 1) * 20;
			$limit = 20;
			
			$filter_data=array(
                            
                            'filter_date_start'=>$filter_date_start,
                            'filter_date_end'=>$filter_date_end,
                            'filter_supplier'=>$filter_supplier,
		
                            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
                            'limit'   => $this->config->get('config_limit_admin')
                );
			$this->adminmodel('purchaseorder/purchase_order');
			$created_po=$this->model_purchaseorder_purchase_order->getList($filter_data);
			//print_r($created_po);
			$data['order_list'] = $created_po->rows;
			
			$total_suppliers = $created_po->num_rows;
			$data['token']=$this->session->data['token'];
			if (isset($this->request->get['pagetittle'])) 
			{
				$url .= '&pagetittle=' . $this->request->get['pagetittle'];
			}
			/*pagination*/
			$pagination = new Pagination();
			$pagination->total = $total_suppliers;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('purchase/purchase_order', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);
			
			$data['pagination'] = $pagination->render();
			
			$data['results'] = sprintf($this->language->get('text_pagination'), ($total_suppliers) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_suppliers - $this->config->get('config_limit_admin'))) ? $total_suppliers : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_suppliers, ceil($total_suppliers / $this->config->get('config_limit_admin')));
			$_SESSION['success_message']=$this->session->data['success'];
			unset($this->session->data['success']);
			/*pagination*/
			
			$this->response->setOutput($this->load->view('default/template/purchase/purchase_order_list.tpl',$data));
		}
		/*---------------------Add supplier function starts here--------------*/
		
		public function add_order()
		{
			$data['heading_title'] = 'Add Purchase Order';
			
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) 
			{ //print_r($this->request->post);
			//	exit;
				$this->adminmodel('purchaseorder/purchase_order');
				$supplier_data=$this->model_purchase_supplier->insert_supplier($this->request->post);
				
				$this->session->data['success'] = 'Supplier added sucessfully';
				$this->response->redirect($this->url->link('purchase/supplier', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			}
			else
			{ 
				$data['token']=$this->session->data['token'];
				$this->adminmodel('purchaseorder/purchase_order');                
                $data['suppliers'] = $this->model_purchaseorder_purchase_order->getSuppliers();
				$data['store_data']=$this->model_purchaseorder_purchase_order->get_to_store_data($this->user->getStoreId());
				$url = '';
				
				$data['action'] = $this->url->link('purchase/purchase_order/add_order', 'token=' . $this->session->data['token'] . $url, true);
				$data['cancel'] = $this->url->link('purchase/purchase_order', 'token=' . $this->session->data['token'] .'&pagetittle=Purchase Order' . $url, true);
				$data['header'] = $this->load->controller('common/header');
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['footer'] = $this->load->controller('common/footer');
				
				$this->response->setOutput($this->load->view('default/template/purchase/po_form.tpl', $data));
			
			}
			
		}
		public function get_to_supplier_data()
        {
            $supplier_id = $this->request->get['supplier_id'];
            $this->adminmodel('purchaseorder/purchase_order');
           echo $data=$this->model_purchaseorder_purchase_order->get_to_supplier_data($supplier_id);
            
        }
		/*---------------------Add supplier function ends here--------------*/
		
		
		
		public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->adminmodel('purchaseorder/purchase_order');
			

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
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
			if(!empty($filter_name))
			{
				$results = $this->model_purchaseorder_purchase_order->getProducts($filter_data);
			}
			//print_r($results);
			foreach ($results as $result) { //print_r($result);
				
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
                    'hstn'=>$result['HSTN'],
                    'price'      => number_format((float)$result['price_tax'], 2, '.', ''),
                    'product_tax_type'=>$result['tax_class_name'],
                    'price_wo_t'=>number_format((float)$result['price'], 2, '.', ''),
                    'product_tax_rate'=>number_format((float)($result['price_tax']-$result['price']), 2, '.', '')
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
        
	public function user_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->adminmodel('purchaseorder/purchase_order');
			

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
				'filter_model' => $filter_name,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_purchaseorder_purchase_order->getUsers($filter_data);
			//print_r($results);
			foreach ($results as $result) { //print_r($result);
				
				$json[] = array(
					
					'name'       => strip_tags(html_entity_decode($result['firstname']." ".$result['lastname'], ENT_QUOTES, 'UTF-8')),
					'mobile_number'      => $result['username']
                                        
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
        
		
		
	}
?>