<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
error_reporting(0);

class ControllerReportWhatsappinv extends Controller 
{
	public function index() 
	{ 
		$heading='WhatsApp Invoice'; 
		$link=$data['link']='report/whatsappinv';
		$this->load->language('catalog/product');

		$this->document->setTitle($heading);
		$this->load->model('report/whatsappinv');
		if (isset($this->request->get['filter_store'])) 
		{
			$filter_store =$data['filter_store']= $this->request->get['filter_store'];
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		} 
		else 
		{
			$filter_store = null;
		}
		if (isset($this->request->get['filter_date_start'])) 
		{
			$filter_date_start=$data['filter_date_start'] = $this->request->get['filter_date_start'];
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		} 
		else 
		{
			$filter_date_start =$data['filter_date_start'] = date('Y-m').'-01';
		}
		if (isset($this->request->get['filter_date_end'])) 
		{
			$filter_date_end =$data['filter_date_end']= $this->request->get['filter_date_end'];
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		} 
		else 
		{
			$filter_date_end =$data['filter_date_end']= date('Y-m-d');
		}
		if (isset($this->request->get['page'])) 
		{
			$page = $this->request->get['page'];
			$url .= '&page=' . $this->request->get['page'];
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
			'text' => $heading,
			'href' => $this->url->link($link, 'token=' . $this->session->data['token'] . $url, 'SSL')
		);


		$data['products'] = array();

		$filter_data = array(
			
			'filter_store'   => $filter_store,
			'filter_date_start'=>$filter_date_start,
			'filter_date_end'=>$filter_date_end,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);
				
		$this->load->model('tool/image');
		$ret_data=$this->model_report_whatsappinv->getwhatsappinv($filter_data);
		$results = $ret_data->rows;
		//print_r($results);
			$product_total = ($ret_data->num_rows);
			$data['text_no_results'] = $this->language->get('text_no_results');
		  
		foreach ($results as $result) 
        {
            $data['products'][] = array(
                                'store_id' => $result['store_id'],
				
				'store_name'       => $result['store_name'],
				'mobile_number'      => $result['mobile_number'],
                
				'name'       => $result['name'],
				'submit_time'       => date('Y-m-d',$result['submit_time']->sec)
                            );
                    
		}
		$this->load->model('setting/store');
        $data['stores'] = $this->model_setting_store->getStores();       
                //print_r($data);  
		$data['heading_title'] = $heading;
		
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
		
		
		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($link, 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_store'] = $filter_store;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('report/whatsappinv.tpl', $data));
	}
	public function download_excel()
	{
		$this->load->model('report/whatsappinv');
		if (isset($this->request->get['filter_store'])) 
		{
			$filter_store = $this->request->get['filter_store'];
		} 
		else 
		{
			$filter_store = null;
		}
		if (isset($this->request->get['filter_date_start'])) 
		{
			$filter_date_start= $this->request->get['filter_date_start'];
			
		} 
		else 
		{
			$filter_date_start = null;
		}
		if (isset($this->request->get['filter_date_end'])) 
		{
			$filter_date_end = $this->request->get['filter_date_end'];
			
		} 
		else 
		{
			$filter_date_end = null;
		}
		
		$filter_data = array(
			
			'filter_store'   => $filter_store,
			'filter_date_start'=>$filter_date_start,
			'filter_date_end'=>$filter_date_end
		);
				
		$this->load->model('tool/image');
		$ret_data=$this->model_report_whatsappinv->getwhatsappinv($filter_data);
		$results = $ret_data->rows;
		//print_r($results);
		$fields = array(
        'Store Name',
        'Store ID',
        'Mobile Number',
        'Name',
        'Date'
        );
		
		foreach ($results as $result)
        {
            
            $fdata[]=array(
                strtoupper($result['store_name']),
                $result['store_id'],
                $result['mobile_number'],
               
                $result['name'],            
                date('Y-m-d',$result['submit_time']->sec)
                
                );
                
            
        }
		
		$this->download_excel_2($fields,$fdata,'whatsAppInv-');
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

}