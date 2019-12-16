<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
error_reporting(0);

class ControllerReportInventoryReport extends Controller {
	public function index() 
	{
		$this->load->language('report/Inventory_report');
		$this->load->language('common/information');
		$data['tool_tip']=$this->language->get('inventory/report');

		$data['tool_tip_style']=$this->language->get('tool_tip_style');
		$data['tool_tip_class']=$this->language->get('tool_tip_class');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = '';
		}
                
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
        if ($this->request->get['filter_store']!="") 
		{
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
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('report/inventory_report', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$this->load->model('setting/store');
                $this->load->model('report/Inventory');
		$data['orders'] = array();

		$filter_data = array(
			
			'filter_store' => $filter_store,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		

		$data['orders'] = array();
		if ($this->request->get['filter_store']!="") 
		{
			$ordt= $this->model_report_Inventory->getTotalInventory($filter_data);
     
			$data["total_amount"]=$ordt;
			$query = $this->model_report_Inventory->getInventory_report($filter_data);
			$results= $query->row;
			$order_total= $query->total_rows;

		}
		foreach ($results as $result) 
		{ 
			//print_r($result);
			if(empty($result['pd']['store_price']))
			{
				$price=$result['price_tax'];
			}
			else
			{
				$price=$result['pd']['store_price']+$result['pd']['store_tax_amt'];
			}
			if($result['pd']['mitra_quantity']=='')
				{
					$result['pd']['mitra_quantity']=0;
				}
            $data['orders'][] = array(
                        'product_id' => $result['product_id'],
						'product_name' => $result['model'],
                        'store_id'      => $result['pd']['store_id'],
                        'store_name'      => $result['store_name'],
						'qnty'   => $result['pd']['quantity'],
						'mitra_quantity'   => $result['pd']['mitra_quantity'],
						'Amount'      => number_format((float)$result['Amount'], 2, ".", ""),
						'price'     => number_format((float)$price, 2, ".", ""),
                    );
		}

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

		$this->load->model('localisation/order_status');
		$data['stores'] = $this->model_setting_store->getStores();//print_r($data['stores'] );
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['groups'] = array();

		$data['groups'][] = array(
			'text'  => $this->language->get('text_year'),
			'value' => 'year',
		);

		$data['groups'][] = array(
			'text'  => $this->language->get('text_month'),
			'value' => 'month',
		);

		$data['groups'][] = array(
			'text'  => $this->language->get('text_week'),
			'value' => 'week',
		);

		$data['groups'][] = array(
			'text'  => $this->language->get('text_day'),
			'value' => 'day',
		);
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/inventory_report', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_group'] = $filter_group;
		$data['filter_store'] = $filter_store;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/Inventory_report.tpl', $data));
	}
	public function download_excel()
	{
        if (isset($this->request->get['filter_store'])) {
            $filter_store = $this->request->get['filter_store'];
        } else {
            $filter_store = '';
        }
                
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->load->model('setting/store');
        $this->load->model('report/Inventory');
        $data['orders'] = array();

        $filter_data = array(
            
            'filter_store' => $filter_store
          
        );

        $data['orders'] = array();
		if ($this->request->get['filter_store']!="") 
		{ 
			$results = $this->model_report_Inventory->getInventory_report($filter_data)->rows;
			$store_info=$this->model_setting_store->getStore($filter_store);
			//print_r($store_info['name']);exit;
		}
        
		include_once '../system/library/PHPExcel.php';
		include_once '../system/library/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();
    
		$objPHPExcel->createSheet();
    
		$objPHPExcel->getProperties()->setTitle("export")->setDescription("none");

		$objPHPExcel->setActiveSheetIndex(0);

		// Field names in the first row
		$fields = array(
       
        'Store Name',
        'Product ID',
        'Product Name',
        'Qnty',
		'Mitra Qnty',
        'Price +Tax ',
        'Store Amount',
		'Mitra Amount'
		);
   
		$col = 0;
		foreach ($fields as $field)
		{
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
			$col++;
		}
    
		$row = 2;
    
		foreach($results as $data)
		{   
			//print_r($data);exit;
			if(empty($data['pd']['store_price']))
			{
				$price=$data['price_tax'];
			}
			else
			{
				$price=$data['pd']['store_price']+$data['pd']['store_tax_amt'];
			}
			if($data['pd']['mitra_quantity']=='')
				{
					$data['pd']['mitra_quantity']=0;
				}
           
			$col = 0;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $store_info['name']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data['product_id']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data['model']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data['pd']['quantity']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data['pd']['mitra_quantity']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, number_format((float)$price, 2, ".", ""));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, ($data['pd']['quantity']*number_format((float)$price, 2, ".", "")));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, ($data['pd']['mitra_quantity']*number_format((float)$price, 2, ".", "")));
			$row++;
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		// Sending headers to force the user to download the file
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Inventory_report_'.date('dMy').'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter->save('php://output');
     }

	public function product_wise()
    {
        $this->load->language('report/Inventory_report');

        $this->document->setTitle('Inventory Report (Product Wise)');

        if (isset($this->request->get['filter_name'])) 
		{
            $filter_name = $this->request->get['filter_name'];
        } 
		else
        {
            //$filter_name = '';
        }
        if (isset($this->request->get['filter_name_id'])) 
		{
            $filter_name_id = $this->request->get['filter_name_id'];
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
        if (isset($this->request->get['filter_name'])) 
		{
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_name_id'])) 
		{
            $url .= '&filter_name_id=' . $this->request->get['filter_name_id'];
        }
                
        if (isset($this->request->get['page'])) 
		{
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('report/inventory_report', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $this->load->model('setting/store');
		$this->load->model('setting/setting');
                $this->load->model('report/Inventory');
        $data['orders'] = array();

        $filter_data = array(
            'filter_name' => $filter_name,
            'filter_name_id' => $filter_name_id,
            'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'                  => $this->config->get('config_limit_admin')
        );

        

        $data['orders'] = array();
                if ($this->request->get['filter_name']!="")
                {
                 //$order_total = $this->model_report_Inventory->getTotalInventoryProductWise($filter_data);
                 //$results = $this->model_report_Inventory->getInventory_reportProductWise($filter_data);
 
                }
        	   //$totalss=$this->model_report_Inventory->getTotalInventoryProductWise($filter_data);
                // $order_total =$totalss["total"] ;
	   //$data["total_Qnty"] =$totalss["total_Qnty"] ;
                 $query = $this->model_report_Inventory->getInventory_reportProductWise($filter_data);
            $results=$query->row;
            $order_total=$query->total_rows;
            foreach ($results as $result) 
			{ 
				if(empty($result['pd']['store_price']))
				{
					$price=$result['price_tax'];
				}
				else
				{
					$price=$result['pd']['store_price']+$result['pd']['store_tax_amt'];
				}
				if($result['pd']['mitra_quantity']=='')
				{
					$result['pd']['mitra_quantity']=0;
				}
				$store_name=$this->model_setting_setting->getSettingbykey('config','config_name',$result['pd']['store_id']);
				
                     $data['orders'][] = array(
                                'product_id' => $result['product_id'],
                'product_name' => $result['model'],
                                'store_id'      => $result['pd']['store_id'],
                                'store_name'      => $store_name,
                'qnty'   => $result['pd']['quantity'],
				'mitra_quantity'   => $result['pd']['mitra_quantity'],
                'Amount'      => number_format((float)$result['Amount'], 2, ".", ""),
                'price'     => number_format((float)$price, 2, ".", ""),
                                
                
                                
            );
        }

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

        $this->load->model('localisation/order_status');
                $data['stores'] = $this->model_setting_store->getStores();
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $pagination = new Pagination();
        $pagination->total = $order_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('report/inventory_report/product_wise', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

        $data['filter_name'] = $filter_name;
        
        $data['filter_name_id'] = $filter_name_id;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/Inventory_report_product_wise.tpl', $data));
        }


      
	public function download_excel_product_wise()
	{
         if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else
                {
            //$filter_name = '';
        }
                if (isset($this->request->get['filter_name_id'])) {
            $filter_name_id = $this->request->get['filter_name_id'];
        }
        

        $filter_data = array(
            'filter_name' => $filter_name,
            'filter_name_id' => $filter_name_id
           
        );

        $this->load->model('setting/setting');
        $this->load->model('setting/store');
        $this->load->model('report/Inventory');

        $data['orders'] = array();
        
        //$order_total = $this->model_report_Inventory->getTotalInventoryProductWise($filter_data);
        $results = $this->model_report_Inventory->getInventory_reportProductWise($filter_data);
		
            // Starting the PHPExcel library
    //print_r($this->load->library('PHPExcel'));
    //$this->load->library('PHPExcel/IOFactory');
    include_once '../system/library/PHPExcel.php';
    include_once '../system/library/PHPExcel/IOFactory.php';
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->createSheet();
    
    $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");

    $objPHPExcel->setActiveSheetIndex(0);

    // Field names in the first row
    $fields = array(
		'Store Name',
        'Store ID',
        'Product Name',
		'Product ID',
        'Qnty',
		'Mitra Qnty',
        'Price (With out Tax)',
        'Amount (With out Tax) '
       
    );
   
    $col = 0;
    foreach ($fields as $field)
    {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
        $col++;
    }
     
    // Fetching the table data
    //$this->load->model('report/searchattendance');
    //$results = $this->model_report_searchattendance->getmdoattendance($filter_data);
    
    $row = 2;
    

    foreach($results->rows as $data)
    {         $col = 0;
        //print_r($data);exit;
		$store_name=$this->model_setting_setting->getSettingbykey('config','config_name',$data['pd']['store_id']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $store_name);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data['pd']['store_id']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data['name']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data['pd']['product_id']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data['pd']['quantity']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data['pd']['mitra_quantity']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, ($data['pd']['store_price']+$data['pd']['store_tax_amt']));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, (($data['pd']['store_price']+$data['pd']['store_tax_amt'])*$data['pd']['quantity']));
      
        $row++;
    }

    

    
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    // Sending headers to force the user to download the file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Inventory_report_product_wise_'.date('dMy').'.xls"');
    header('Cache-Control: max-age=0');

    $objWriter->save('php://output');
        
    }
      public function email_excel(){
        
$this->load->model('report/Inventory');
$data['orders'] = array();

 $results = $this->model_report_Inventory->getInventory_report($filter_data);
             
        
    // Starting the PHPExcel library
    //print_r($this->load->library('PHPExcel'));
    //$this->load->library('PHPExcel/IOFactory');
    include_once '../system/library/PHPExcel.php';
    include_once '../system/library/PHPExcel/IOFactory.php';
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->createSheet();
    
    $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");

    $objPHPExcel->setActiveSheetIndex(0);

    // Field names in the first row
    $fields = array(
        
        'Product ID',
        'Product Name',
        'Store name',
        'Qnty',
        'Price',
        'Amount'
    );
   
    $col = 0;
    foreach ($fields as $field)
    {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
        $col++;
    }
    
    $row = 2;
    
    foreach($results as $data)
    {
        $col = 0;
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $data['product_id']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data['Product_name']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data['store_name']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data['Qnty']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data['price']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data['Amount']);
        
        $row++;
    }

    

    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    // Sending headers to force the user to download the file
    //header('Content-Type: application/vnd.ms-excel');
    //header('Content-Disposition: attachment;filename="Inventory_report_'.date('dMy').'.xls"');
    //header('Cache-Control: max-age=0');

    //$objWriter->save('php://output');
    $filename='inventory_report_'.date('ymdhis').'.xls';
    $objWriter->save(DIR_UPLOAD.$filename );
    //
    $mail             = new PHPMailer();

                $body = "<p>Akshamaala Solution Pvt. Ltd.</p>";
                
                $mail->IsSMTP();
                $mail->Host       = "mail.akshamaala.in";
                                                           
                $mail->SMTPAuth   = false;                 
                $mail->SMTPSecure = "";                 
                $mail->Host       = "mail.akshamaala.in";      
                $mail->Port       = 25;                  
                $mail->Username   = "mis@akshamaala.in";  
                $mail->Password   = "mismis";            

                $mail->SetFrom('mail.akshamaala.in', 'Akshamaala');

                $mail->AddReplyTo('mail.akshamaala.in','Akshamaala');

                $mail->Subject    = "Inventory Report";

                $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

                $mail->MsgHTML($body);
                
                //to get the email of supplier
                
                $mail->AddAddress('chetan.singh@akshamaala.com', "Chetan Singh");
                
                

                $mail->AddAttachment(DIR_UPLOAD.$filename);
                
                if(!$mail->Send())
                {
                  echo "Mailer Error: " . $mail->ErrorInfo;
                }
                else
                {
                  if(!unlink(DIR_UPLOAD.$filename))
                  {
                      echo ("Error deleting ");
                  }
                  else
                  {
                     echo ("Deleted ");
                  }
                                  
                }
        
    }  
    public function old_report() {
		$this->load->language('report/Inventory_report');

		$this->document->setTitle('Inventory old report');

		if (isset($this->request->get['filter_date'])) {
			$filter_date = $this->request->get['filter_date'];
		} else {
			$filter_date = '';
		}
		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = '';
		}
                
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
              
                            if ($this->request->get['filter_date']!="") {
			$url .= '&filter_date=' . $this->request->get['filter_date'];
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
			'text' => 'Inventory old report',
			'href' => $this->url->link('report/inventory_report/old_report', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		
                            $this->load->model('report/Inventory');
		$data['orders'] = array();

		$filter_data = array(
			'filter_store'=>$filter_store,
			'filter_date' => $filter_date,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		

		$data['orders'] = array();
                                      // if ($this->request->get['filter_date']!="") {
                                       $ret_data = $this->model_report_Inventory->get_old_report_daily_email($filter_data);
                                       $results =$ret_data->rows ;
										$order_total=$ret_data-num_rows;
                                   // }
		
                          

		foreach ($results as $result) 
		{ //print_r($result);
			         $data['orders'][] = array(
                                'report_date' => $result['report_date'],
		    'filename' => $result['filename'],
			'store_name' => $result['store_name']
			);
		}

		$data['heading_title'] = 'Inventory old report';
		$this->load->model('setting/store');
                            $data['stores'] = $this->model_setting_store->getStores();//print_r($data['stores'] );

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		
		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		
		$url = '';

		
               
                           if ($this->request->get['filter_date']!="") {
			$url .= '&filter_date=' . $this->request->get['filter_date'];
		}
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/inventory_report/old_report', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_date'] = $filter_date;
		

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/Inventory_old_report.tpl', $data));
	}
	
	
	

}