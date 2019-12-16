<?php
class ControllerCatalogReward extends Controller 
{
	private $error = array();

	public function index() 
        { 
            $this->load->model('catalog/product'); 
            if ($this->request->server['REQUEST_METHOD'] == 'POST') 
            {
				
                $this->model_catalog_product->add_product_reward($this->request->post);
                print_r($this->request->post);
				//exit;
                $this->session->data['success'] ="Data updated Sucessfully !";
                $this->response->redirect($this->url->link('catalog/reward', 'token=' . $this->session->data['token'], 'SSL'));
                                
            }
			
		$this->load->language('setting/store');
                $title='Reward Points';
		$this->document->setTitle($title);

		$url = '';

		$data['breadcrumbs'] = array();

		

		$data['breadcrumbs'][] = array(
			'text' => 'Unit List',
			'href' => $this->url->link('catalog/reward', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['token'] = $this->session->data['token'];

		$data['hsn'] = array();
		$filter_data = array(
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);
                $resultsdata=$this->model_catalog_product->getRewardsList($filter_data);
		$order_total= 0;//$resultsdata->num_rows;

		$results = $resultsdata->rows;

		foreach ($results as $result) 
                {
                    //print_r($result['product_sku']);
                        $store=$result['stores'];
			$data['rewards'][] = array(
                                'store_id' => $store,
				'product_name' => $result['product_name'][0],
				'product_id'   => $result['_id']['pid'],
				'product_sku'   => $result['product_sku'][0],
                                'customer_group_id' => $result['customer_group_id'],
                                'valid_till'   => $result['valid_till'][0],
                                'points'     => $result['_id']['pts']
                                );
                    
		}
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $title;
		$data['text_no_results'] = $this->language->get('text_no_results');
		

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
                        unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
                $this->load->model('setting/store');
                $data['stores'] = $this->model_setting_store->getStores();
		//$data['stores'] = $this->model_setting_store->getStores();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/view_reward.tpl', $data));
	}
	public function expired() 
        { 
            $this->load->model('catalog/product'); 
           
			
		$this->load->language('setting/store');
                $title='Reward Points';
		$this->document->setTitle($title);

		$url = '';

		$data['breadcrumbs'] = array();

		

		$data['breadcrumbs'][] = array(
			'text' => 'Unit List',
			'href' => $this->url->link('catalog/reward', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['token'] = $this->session->data['token'];

		$data['hsn'] = array();
		$filter_data = array(
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);
            
		$resultsdata_expired=$this->model_catalog_product->getRewardsList_expired($filter_data);
	

		$results_expired = $resultsdata_expired->rows;

		foreach ($results_expired as $result) 
                {
                    //print_r($result['product_sku']);
                        $store=$result['stores'];
			$data['rewards_expired'][] = array(
                                'store_id' => $store,
				'product_name' => $result['product_name'][0],
				'product_id'   => $result['_id']['pid'],
				'product_sku'   => $result['product_sku'][0],
                                'customer_group_id' => $result['customer_group_id'],
                                'valid_till'   => $result['valid_till'][0],
                                'points'     => $result['_id']['pts']
                                );
                    
		}
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $title;
		$data['text_no_results'] = $this->language->get('text_no_results');
		

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
                        unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
                $this->load->model('setting/store');
                $data['stores'] = $this->model_setting_store->getStores();
		//$data['stores'] = $this->model_setting_store->getStores();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/view_reward_expired.tpl', $data));
	}
        public function delete()
        {
            //print_r($this->request->get);
            $this->load->model('catalog/product');
            $resultsdata=$this->model_catalog_product->deleteRewards($this->request->get);
            $this->session->data['success'] ="Data updated Sucessfully !";
			//if($this->request->get['type']=='expired')
            $this->response->redirect($this->url->link('catalog/reward'.'/'.$this->request->get['type'], 'token=' . $this->session->data['token'], 'SSL'));
                
        }
        
        public function unnati_mitra_statement() 
        {
            $this->load->model('catalog/unnatimitra');
            $this->load->model('sale/customer');
            $this->load->language('setting/store');
            $title='Unnati Mitra Statement';
            $this->document->setTitle($title);
            $url = '';
                if(!empty($this->request->get['mobile']))
                {
                    $data['filter_mobile']=$mobile=$this->request->get['mobile'];
                    $customer_info = $this->model_sale_customer->getCustomers(array('filter_telephone'=>$mobile));
                    $customer_id=$customer_info->row['customer_id'];
                }
                if(!empty($this->request->get['page']))
                {
                    $page=$this->request->get['page'];
                }
                else 
                {
                    $page=1;
                }
		$data['breadcrumbs'] = array();
                $data['breadcrumbs'][] = array(
			'text' => 'Statement List',
			'href' => $this->url->link('catalog/reward/unnati_mitra_statement', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['token'] = $this->session->data['token'];
                
		$filter_data = array(
                        'customer_id'=>$customer_id,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);
                $resultsdata=$this->model_catalog_unnatimitra->getStatement($filter_data);
                $filter_data2 = array(
                    'customer_id'=>$customer_id
		);
                
		$order_total= $this->model_catalog_unnatimitra->getStatement($filter_data2)->num_rows;
                
		$results = $resultsdata->rows;
                //print_r($results);
		foreach ($results as $result) 
                {
                    $data['rewards'][] = array(
                        'order_id'     => $result['order_id'],
                        'store_id'     => $result['store_id'],
                        'store_name'     => $result['store_name'],
                        'inv_no'     => $result['inv_no'],
                        'customer_id'     => $result['customer_id'],
                        'type'     => $result['type'],
                        'points'     => $result['points'],
                        'date_added'     => $result['date_added']
                                );
                    
		}

		$data['heading_title'] = $title;
		$data['entry_name'] = $this->language->get('Mobile Number');
                $data['button_filter'] = $this->language->get('Search');
		
		$data['text_list'] = $title;
		$data['text_no_results'] = $this->language->get('text_no_results');
		

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
                        unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
                $this->load->model('setting/store');
                $data['stores'] = $this->model_setting_store->getStores();
		//$data['stores'] = $this->model_setting_store->getStores();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/unnati_mitra_statement.tpl', $data));
	}
    public function unnati_mitra_statement_download_excel()
    {
        $this->load->model('catalog/unnatimitra');
        $this->load->model('sale/customer');
        if(!empty($this->request->get['mobile']))
        {
            $data['filter_mobile']=$mobile=$this->request->get['mobile'];
            $customer_info = $this->model_sale_customer->getCustomers(array('filter_telephone'=>$mobile));
            $customer_id=$customer_info->row['customer_id'];
        }
        $filter_data = array(
                        'customer_id'=>$customer_id,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);
        $resultsdata=$this->model_catalog_unnatimitra->getStatement($filter_data);
        $results = $resultsdata->rows;
        include_once '../system/library/PHPExcel.php';
        include_once '../system/library/PHPExcel/IOFactory.php';
        $objPHPExcel = new PHPExcel();
    
        $objPHPExcel->createSheet();
    
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");

        $objPHPExcel->setActiveSheetIndex(0);

        // Field names in the first row
        $fields = array(
                'Date', 
		'Invoice',		 
		 'Pos', 	
		 'Earn/Redeem'
        ); 
        $col = 0;
        foreach ($fields as $field)
        {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
            $col++;
        } 	
        $row = 2; 
	foreach ($results as $result) 
	{
            $data['rewards'][] = array(
                        'order_id'     => $result['order_id'],
                        'store_id'     => $result['store_id'],
                        'store_name'     => $result['store_name'],
                        'inv_no'     => $result['inv_no'],
                        'customer_id'     => $result['customer_id'],
                        'type'     => $result['type'],
                        'points'     => $result['points'],
                        'date_added'     => $result['date_added']
                                );
            $col = 0;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, date('d-m-Y',$result['date_added']->sec)); 
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $result['inv_no']); 
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $result['store_name']);  
            if($result['type']=='Add')
            {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, '+'.number_format((float)($result['points']),2,'.',''));
            }
            if($result['type']=='Redeem')
            {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, '-'.number_format((float)($result['points']),2,'.',''));
            }
   
            $row++;
	}
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        // Sending headers to force the user to download the file
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Unnati_Mitra_Statement'.date('dMy').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
      }
}