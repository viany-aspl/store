<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
error_reporting(0);

class ControllerReportReport extends Controller 
{
	public function index() 
	{
		$this->load->language('catalog/product');

		$this->document->setTitle('Un verified products');
		$this->load->model('catalog/product');
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_name_id'])) {
			$filter_name_id = $this->request->get['filter_name_id'];
		} else {
			$filter_name_id = null;
		}


		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = null;
		}

		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'Un verified products',
			'href' => $this->url->link('report/report', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);


		$data['products'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_name_id'	  => $filter_name_id,
			'filter_store'   => $filter_store,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);
        $filter_data2 = array(
			'filter_name'	  => $filter_name,
			'filter_name_id'	  => $filter_name_id,
			'filter_store'   => $filter_store,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => 0,
			'limit'           => 100000000
		);       
		$this->load->model('tool/image');
		//$ret_data=$this->model_catalog_product->getProducts($filter_data);
		if(!empty($filter_store))
		{
			$ret_data=$this->model_catalog_product->getProductsUnverified($filter_data);
			$results = $ret_data->rows;
		
			$ret_data2=$this->model_catalog_product->getProductsUnverified2($filter_data2);
			$product_total = count($ret_data2->rows);//$this->model_catalog_product->getProductsUnverified_count($filter_data,$ret_data->num_rows);//
			$data['text_no_results'] = $this->language->get('text_no_results');
		}
		else
		{
			$data['text_no_results'] = 'Please Select Store';
		}
             
		foreach ($results as $result) 
        {
            //print_r($result);exit;      
			if (is_file(DIR_IMAGE . $result['pd']['image'])) 
                        {
                                //echo DIR_IMAGE.$result['image']; echo $result['product_id'];
                                //next line not working
				$image = $this->model_tool_image->resize($result['pd']['image'], 40, 40);
			} 
                        else 
                        {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
                        
			$special = boolval(0);


                            $data['products'][] = array(
                                'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['pd']['name'],
				'model'      => $result['pd']['model'],
                                'price_tax'  => number_format($result['pd']['price_tax'], 2),
                                'price'      => number_format($result['pd']['price'], 2),
				'store_id'       => $result['store_id'],
				'store_name'       => $result['st']['name'],
				'quantity'   => $result['quantity'],
				'status'   => (int)$result['pd']['status']
                            );
                    
		}
		$this->load->model('setting/store');
        $data['stores'] = $this->model_setting_store->getStores();       
                //print_r($data);  
		$data['heading_title'] = 'Un verified products';
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

        $data['column_price_tax'] = 'Price+Tax';//$this->language->get('column_price_tax');
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
                if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
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
			$url .= '&filter_name_id=' . urlencode(html_entity_decode($this->request->get['filter_name_id'], ENT_QUOTES, 'UTF-8'));
		}

		

		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) 
                {
			$url .= '&page=' . $this->request->get['page'];
		}


		
		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/report', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_name_id'] = $filter_name_id;
		
		$data['filter_store'] = $filter_store;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('report/product_unverified_list.tpl', $data));
	}

	public function referral() 
	{
		$this->load->language('catalog/product');

		$this->document->setTitle('Referral');
		$this->load->model('user/user');
		
		if (isset($this->request->get['filter_store'])) 
		{
			$filter_store = $this->request->get['filter_store'];
		} 
		else 
		{
			$filter_store = null;
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';


		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
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
			'text' => 'Referral',
			'href' => $this->url->link('report/report/referral', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);


		$data['products'] = array();

		$filter_data = array(
			'filter_store'   => $filter_store,
			
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);
                
		$this->load->model('tool/image');
                $ret_data=$this->model_user_user->getreferral($filter_data);
                $results = $ret_data->rows;
		$product_total = $ret_data->num_rows;

             
		foreach ($results as $result) 
        {
            
                            $data['products'][] = array(
                                'user_id' => $result['user_id'],
				
				'store_id'       => $result['store_id'],
				
				'store_name'       => $result['store_name'],
				'mobile_number'   => $result['mobile_number'],
				'name'   => $result['name'],
				'submit_time'   => date('d-m-Y',$result['submit_time']->sec)
                            );
                    
		}
		$this->load->model('setting/store');
        $data['stores'] = $this->model_setting_store->getStores();       
               
		$data['heading_title'] ='Referral';
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

               
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
                if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';


		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}

		if (isset($this->request->get['page'])) 
                {
			$url .= '&page=' . $this->request->get['page'];
		}


		
		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/report/referral', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));
		
		$data['filter_store'] = $filter_store;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('report/referral_list.tpl', $data));
	}
	public function premium_farmer() 
	{
		$this->load->language('catalog/product');

		$this->document->setTitle('Premium Farmer');
		$this->load->model('pos/pos');
		if (isset($this->request->get['filter_name'])) 
		{
			$filter_name = $this->request->get['filter_name'];
		} 
		else 
		{
			$filter_name = null;
		}

		if (isset($this->request->get['filter_telephone'])) 
		{
			$filter_telephone = $this->request->get['filter_telephone'];
		} 
		else 
		{
			$filter_telephone = null;
		}


		if (isset($this->request->get['filter_store'])) 
		{
			$filter_store = $this->request->get['filter_store'];
		} 
		else 
		{
			$filter_store = null;
		}

		if (isset($this->request->get['page'])) 
		{
			$page = $this->request->get['page'];
		} 
		else 
		{
			$page = 1;
		}

		$url = '';
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_telephone'])) {
			$url .= '&filter_telephone=' . urlencode(html_entity_decode($this->request->get['filter_telephone'], ENT_QUOTES, 'UTF-8'));
		}

		

		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
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
			'text' => 'Premium Farmer',
			'href' => $this->url->link('report/report/premium_farmer', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);


		$data['products'] = array();

		$filter_data = array(
			'firstname'	  => $filter_name,
			'telephone'	  => $filter_telephone,
			'store_id'   => $filter_store,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);
                
		$ret_data=$this->model_pos_pos->getPremiumCustomersAllStores($filter_data);
        $results = $ret_data->rows;
		$product_total = $ret_data->num_rows;//$this->model_catalog_product->getProductsUnverified_count($filter_data,$ret_data->num_rows);//

             
		foreach ($results as $result) 
        {
            //print_r($result);exit;  
			$store_name='';
			$store_id='';
                        if(is_array($result['store_id']))
                        {
                            foreach($result['store_id'] as $str_id)
                            {
                                $store_name=$store_name.$this->model_pos_pos->getstorename($str_id).',';
                            }
                            $store_name=rtrim($store_name,',');
                            $store_id=$store_id.$str_id.',';
                            $store_id=rtrim($store_id,',');
                        }
                        else
                        {
			$store_name=$this->model_pos_pos->getstorename($result['store_id']);
                        $store_id=$result['store_id'];
                        }			
			$data['products'][] = array(
                'customer_id' => $result['customer_id'],
				
				'name'       => $result['firstname'].' '.$result['lastname'],
				'telephone'      => $result['telephone'],
				                'store_id'       => $store_id,
				'store_name'       => $store_name,
				'credit'   => $result['credit'],
				 'reward'   => $result['reward'],
                            				'dist_name'   => $result['dist_name'],
                            				'village'   => $result['village'],
                            				'card_number'   => $result['card_number'],
                            				'unnati_mitra'   => $result['unnati_mitra']
                );
                    
		}
		$this->load->model('setting/store');
        $data['stores'] = $this->model_setting_store->getStores();       
        
		$data['heading_title'] = 'Premium Farmer';
		
		$data['text_no_results'] = $this->language->get('text_no_results');
		
		
		$data['column_name'] = $this->language->get('column_name');
		
		$data['button_filter'] = $this->language->get('button_filter');
		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) 
		{
			$data['error_warning'] = $this->error['warning'];
		} 
		else 
		{
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) 
		{
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} 
		else 
		{
			$data['success'] = '';
		}
        if (isset($this->session->data['error'])) 
		{
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} 
		else 
		{
			$data['error_warning'] = '';
		}
		
		
		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/report/premium_farmer', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_telephone'] = $filter_telephone;
		
		$data['filter_store'] = $filter_store;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('report/premium_farmer_list.tpl', $data));
	}
	public function premium_farmer_download() 
	{
		$this->load->model('pos/pos');
		if (isset($this->request->get['filter_name'])) 
		{
			$filter_name = $this->request->get['filter_name'];
		} 
		else 
		{
			$filter_name = null;
		}

		if (isset($this->request->get['filter_telephone'])) 
		{
			$filter_telephone = $this->request->get['filter_telephone'];
		} 
		else 
		{
			$filter_telephone = null;
		}


		if (isset($this->request->get['filter_store'])) 
		{
			$filter_store = $this->request->get['filter_store'];
		} 
		else 
		{
			$filter_store = null;
		}

		if (isset($this->request->get['page'])) 
		{
			$page = $this->request->get['page'];
		} 
		else 
		{
			$page = 1;
		}

		$data['products'] = array();

		$filter_data = array(
			'firstname'	  => $filter_name,
			'telephone'	  => $filter_telephone,
			'store_id'   => $filter_store
		);
                
		$ret_data=$this->model_pos_pos->getPremiumCustomersAllStores($filter_data);
        $results = $ret_data->rows;
		//$product_total = $ret_data->num_rows;//$this->model_catalog_product->getProductsUnverified_count($filter_data,$ret_data->num_rows);//
		$fields = array(
        'Name',
        'Telephone',
        'Credit',
        'Reward',
        'Store Name',
		'Store ID',
		'Dist name',
		'Village',
		'Card number',
		'Date Added'
        );
             
		foreach ($results as $result) 
        {
			$date=date('m-d-Y',(($result['date_added']->sec)));
            //print_r(date('d-m-Y',($result['date_added']->sec)));exit;  
			$store_name='';
			$store_id='';
                        if(is_array($result['store_id']))
                        {
                            foreach($result['store_id'] as $str_id)
                            {
                                $store_name=$store_name.$this->model_pos_pos->getstorename($str_id).',';
                            }
                            $store_name=rtrim($store_name,',');
                            $store_id=$store_id.$str_id.',';
                            $store_id=rtrim($store_id,',');
                        }
                        else
                        {
						$store_name=$this->model_pos_pos->getstorename($result['store_id']);
                        $store_id=$result['store_id'];
                        }			
			
				$fdata[]=array(
                strtoupper($result['firstname'].' '.$result['lastname']),
                $result['telephone'],
                
				$result['credit'],
                $result['reward'], 
				$store_name,
				$store_id,
				$result['dist_name'],
				$result['village'],
                $result['card_number'],
				$date
                );
                    
		}
		$this->download_excel_2($fields,$fdata,'Premium-Farmer-');
	}
	private function download_excel_2($fields,$fdatas,$filename)
	{
		//print_r($fdatas);exit;
        $fileIO = fopen('php://memory', 'w+');
        fputcsv($fileIO, $fields,',');
        
        foreach($fdatas as $fdata)
		{
			fputcsv($fileIO,  $fdata,",");
		}
        fseek($fileIO, 0);
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment;filename="'.$filename.date('Y-m-d-h-i-s').'.csv"');
        header('Cache-Control: max-age=0');
        fpassthru($fileIO);  
        fclose($fileIO);    
	}
	public function total_credit() 
	{
		$this->load->language('catalog/product');

		$this->document->setTitle('Total Credit');
		$this->load->model('pos/pos');
		
		if (isset($this->request->get['filter_store'])) 
		{
			$filter_store = $this->request->get['filter_store'];
		} 
		else 
		{
			$filter_store = null;
		}

		if (isset($this->request->get['page'])) 
		{
			$page = $this->request->get['page'];
		} 
		else 
		{
			$page = 1;
		}

		$url = '';
		
		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
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
			'text' => 'Total Credit',
			'href' => $this->url->link('report/report/total_credit', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		$data['products'] = array();

		$filter_data = array(
			'store_id'   => $filter_store,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);
                
		$ret_data=$this->model_pos_pos->getTotalCreditAllStores($filter_data);
        $results = $ret_data->rows;
		$product_total = $ret_data->num_rows;//$this->model_catalog_product->getProductsUnverified_count($filter_data,$ret_data->num_rows);//
		//print_r($ret_data);exit; 
             
		foreach ($results as $result) 
        {
            //print_r($result);exit;  
			$store_name=$this->model_pos_pos->getstorename($result['_id']);			
			$data['products'][] = array(
                
                'store_id'       => $result['_id'],
				'store_name'       =>strtoupper($store_name),
				'credit'   => $result['total']
                );
                    
		}
	usort($data['products'], function ($item1, $item2) {
    if ($item1['store_name'] == $item2['store_name']) return 0;
    return $item1['store_name'] < $item2['store_name'] ? -1 : 1;
});

		$this->load->model('setting/store');
        $data['stores'] = $this->model_setting_store->getStores();       
        
		$data['heading_title'] = 'Total Credit';
		
		
		
		$data['text_no_results'] = $this->language->get('text_no_results');
		
		
		$data['column_name'] = $this->language->get('column_name');
		
		$data['button_filter'] = $this->language->get('button_filter');
		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) 
		{
			$data['error_warning'] = $this->error['warning'];
		} 
		else 
		{
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) 
		{
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} 
		else 
		{
			$data['success'] = '';
		}
        if (isset($this->session->data['error'])) 
		{
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} 
		else 
		{
			$data['error_warning'] = '';
		}
		
		
		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/report/total_credit', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_telephone'] = $filter_telephone;
		
		$data['filter_store'] = $filter_store;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('report/total_credit_list.tpl', $data));
	}
	

}