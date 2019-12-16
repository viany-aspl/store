<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
error_reporting(0);

class ControllerPosInventoryReport extends Controller {
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
		public function email()  
        {
            if (isset($this->request->get['filter_name_id'])) 
			{
				$sdate = $this->request->get['filter_name_id'];
			} 
			
			if (isset($this->request->get['filter_name'])) 
			{
				$edate = $this->request->get['filter_name'];
			} 
			
			$mcrypt=new MCrypt();
			$fields_string='';
			$strid=$this->user->getStoreId();
			$username=$this->user->getId();
			$log =new Log("prdinv-".date('Y-m-d').".log");

			$log->write($strid);
			$log->write($username);
			
			$fields_string .= 'store_id'.'='.$mcrypt->encrypt($strid).'&'; 
			
			$fields_string .= 'username'.'='.$mcrypt->encrypt($username).'&';
			$fields_string .= 'action'.'='.$mcrypt->encrypt('e').'&'; 
			$request = HTTP_CATALOG."index.php?route=mpos/product/invproducts";
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
            
		$this->load->language('report/Inventory_report');
		$this->load->language('common/information');
		$data['tool_tip']=$this->language->get('inventory/report');

		$data['tool_tip_style']=$this->language->get('tool_tip_style');
		$data['tool_tip_class']=$this->language->get('tool_tip_class');

		$this->document->setTitle($this->language->get('heading_title'));

		
		$filter_store = $this->user->getStoreId();
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
                if (isset($this->request->get['filter_name'])) {
			$data['filter_product']=$filter_product = $this->request->get['filter_name'];
		} else {
			$filter_product = '';
		}
                if (isset($this->request->get['filter_name_id'])) {
			$data['filter_product_id']=$filter_product_id = $this->request->get['filter_name_id'];
		} else {
			$filter_product_id = '';
		}
		$url = '';
               
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
                if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . $this->request->get['filter_product'];
		}
		
        $this->catmodel('catalog/product');
		$data['orders'] = array();
                $this->config->set('config_store_id',(int)$filter_store);
		$filter_data = array(
			
                        'filter_product_id' => (int)$filter_product_id,
			'filter_store' => (int)$filter_store,
                        'store_id' => (int)$filter_store,
						'for_store'=>'inventory_report',
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);
                //print_r($filter_data);exit;
                $data['listcount']=$this->model_catalog_product->getTotalQntyProducts(array());

		$products = $this->model_catalog_product->getProducts($filter_data);
                $data['total']=round($this->model_catalog_product->getTotalInventoryAmount($filter_data) );
        $log=new Log("prdinv-".date('Y-m-d').".log");
         
		foreach ($products as $product) 
        { 
			 $order_total =$product['num_rows'];
				if($product['product_id']==2285)
				{
					$log->write($product);
				}
                    if(empty($product))
                    {    
                        continue;
                    }

                    if ($product['image']) 
                    {
                        $image = $product['image'];
                    } 
                    else 
                    {
                        $image = false;
                    }

                    
                    $special = false;
		
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
                    $price_with_tax=($product['price'])+(($this->tax->getTax($product['price'], $product['tax_class_id'])));
                    $data['products'][] = array(
					'id'			=> ($product['product_id']),
					'name'			=> ($product['name']),
					'quantity'		=> ($pquantity), 
					'fquantity'		=> ($product['fquantity']), 
					'description'	=> ($product['description']),
					'pirce'			=> ( $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))+($this->tax->getTax($product['price'], $product['tax_class_id'])))),
					'href'			=> ($this->url->link('product/product', 'product_id=' . $product['product_id'])),
					'thumb'			=> ($image),
					'special'		=> ($special),
					'rating'		=> ($product['rating']),
					'pricewithtax'  => ($price_with_tax),
					'tax'			=> (($this->tax->getTax($product['price'], $product['tax_class_id'])))
			);
		}
                //print_r($data);exit;
		$data['token'] = $this->session->data['token'];
        if (isset($this->request->get['pagetittle'])) 
		{
			$url .= '&pagetittle=' . $this->request->get['pagetittle'];
		}       
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('pos/inventory_report', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_group'] = $filter_group;
		$data['filter_store'] = $filter_store;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('default/template/pos/Inventory_report.tpl', $data));
	}

        public function update_quantity()
		{
        $data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_all_status'] = $this->language->get('text_all_status');

		$data['column_date_start'] = $this->language->get('column_date_start');
		$data['column_date_end'] = $this->language->get('column_date_end');
		$data['column_title'] = $this->language->get('column_title');
		$data['column_orders'] = $this->language->get('column_orders');
		$data['column_total'] = $this->language->get('column_total');

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_group'] = $this->language->get('entry_group');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_filter'] = $this->language->get('button_filter');
            $data['token'] = $this->session->data['token'];
         $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');   
         $this->response->setOutput($this->load->view('pos/update_quantity.tpl', $data));   
        }
        
        
        
         public function update_price() 
		 {
            $data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_all_status'] = $this->language->get('text_all_status');

		$data['column_date_start'] = $this->language->get('column_date_start');
		$data['column_date_end'] = $this->language->get('column_date_end');
		$data['column_title'] = $this->language->get('column_title');
		$data['column_orders'] = $this->language->get('column_orders');
		$data['column_total'] = $this->language->get('column_total');

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_group'] = $this->language->get('entry_group');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_filter'] = $this->language->get('button_filter');
            $data['token'] = $this->session->data['token'];
         $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');   
         $this->response->setOutput($this->load->view('pos/update_price.tpl', $data));   
        }
        
	

}