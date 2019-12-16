<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');

//ini_set('max_execution_time', 600); //600 seconds = 10 minutes
ini_set('memory_limit','1024M');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ControllerReportProductSales extends Controller 
{
	public function index() 
	{
		$this->load->language('report/product_sale');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_date_start'])) 
		{
			$filter_date_start = $this->request->get['filter_date_start'];
		} 
		else 
		{
			$filter_date_start = date('Y-m-d');
		}

		if (isset($this->request->get['filter_date_end'])) 
		{
			$filter_date_end = $this->request->get['filter_date_end'];
		} 
		else 
		{
			$filter_date_end = date('Y-m-d');
		}
        if (isset($this->request->get['filter_store'])) 
		{
			$filter_store = $this->request->get['filter_store'];
		} 
		if (isset($this->request->get['filter_name'])) 
		{
			$filter_name = $this->request->get['filter_name'];
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

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_name'])) 
		{
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}
		if (isset($this->request->get['filter_store'])) 
		{
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}
		
        if (isset($this->request->get['filter_name_id'])) 
		{
			$url .= '&filter_name_id=' . $this->request->get['filter_name_id'];
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
			'href' => $this->url->link('report/product_sales', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$this->load->model('report/product_sale');
                        $this->load->model('setting/store');
		$data['orders'] = array();

		$filter_data = array(
            'filter_store'	     => $filter_store,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_product_id'           => $filter_name_id,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$order_total = $t1["total"];
		
		$results = $this->model_report_product_sale->getOrders($filter_data);
        $order_total = $results->num_rows;                   
		//print_r($results); 

		foreach ($results->rows as $result1) 
		{ 
			foreach($result1['order_product'] as $result)
			{
				if($result['tax']=='0.00')
				{
					$tax_title='NO-TAX';
				}
				else
				{
					$tax_title1=ceil(($result['tax']*100)/$result['price']);
					$tax_title='GST@'.$tax_title1.'%';
				}
				$data['orders'][] = array(
				'dats' => date($this->language->get('date_format_short'), ($result1['date_added']->sec)),
				'name'          =>$result['name'],
				'store_name'    => strtoupper($result1['store_name']),
                                'store_id'    => $result1['store_id'],
                              
                                'Total_sales'   => $result['total'],
                                'Total_tax'     => $result['tax'],
                                'Total'         => $result['total'],
                                'tax_title'    => $tax_title,
                                'qnty'          => $result["quantity"],
			 'discount_type'          => $result1["discount_type"],
			 'discount_value'          => $result1["discount_value"],
			 'order_id'  => $result1['order_id']
				
			);
			}
		}
 
		$data['heading_title'] = $this->language->get('heading_title');
		$this->load->language('common/information');
		$data['tool_tip']=$this->language->get('report/product_sales');

		$data['tool_tip_style']=$this->language->get('tool_tip_style');
		$data['tool_tip_class']=$this->language->get('tool_tip_class');
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_all_status'] = $this->language->get('text_all_status');

		$data['column_date_start'] = $this->language->get('column_date_start');
		$data['column_date_end'] = $this->language->get('column_date_end');
		$data['column_orders'] = $this->language->get('column_orders');
		$data['column_products'] = $this->language->get('column_products');
		$data['column_tax'] = $this->language->get('column_tax');
		$data['column_total'] = $this->language->get('column_total');

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_group'] = $this->language->get('entry_group');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		//echo $url;
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/product_sales', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_name'] = $filter_name;
		$data['filter_store'] = $filter_store;
        $data['filter_name_id'] = $filter_name_id;
		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/product_sales.tpl', $data));
	}
    public function download_excel() 
	{
        if (isset($this->request->get['filter_date_start'])) 
		{
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			//$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			//$filter_date_end = date('Y-m-d');
		}
        if (isset($this->request->get['filter_store'])) 
		{
			$filter_store = $this->request->get['filter_store'];
		}         
        if (isset($this->request->get['filter_name_id'])) 
		{
			$filter_name_id = $this->request->get['filter_name_id'];
		} 

		$this->load->model('report/product_sale');
                
		$data['orders'] = array();

		$filter_data = array(
                        'filter_store'	     => $filter_store,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_product_id'           => $filter_name_id
			
		);

		
		$file_name="Product_sales_report_".date('dMy').'.xls';
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=".$file_name);  //File name extension was wrong
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);

        $results = $this->model_report_product_sale->getOrders($filter_data);
        echo '<table id="example2" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
					<th>Sale Date</th>
                    <th>Store Name</th>
                    <th>Store ID</th>
                    
                    <th>Product Name</th>
                    <th>Quantity</th>
					<th>Rate(without tax)</th>
					<th>Tax title</th>
					<th>Tax rate</th>
                    <th>Total (Sales + Tax)</th>
                    <th>Order ID</th>
					<th>Discount Type</th>
					<th>Discount Value</th>
                </tr>
                </thead>
                <tbody>';
		$tblbody=" ";
		foreach($results->rows as $data1)
		{ 	
			foreach($data1['order_product'] as $data)
			{
				if($data['tax']=='0.00')
				{
					$tax_title='NO-TAX';
				}
				else
				{
					$tax_title1=ceil(($data['tax']*100)/$data['price']);
					$tax_title='GST@'.$tax_title1.'%';
				}
				
		
			
				$price_without_tax=number_format((float)$data['price'], 2, '.', '');
			
			//echo $price_without_tax;
			echo  '<tr> 
					<td>'.date('Y-m-d',($data1['date_added']->sec)).'</td>
                    <td>'.$data1['store_name'].'</td>
                    <td>'.$data1['store_id'].'</td>
                    <td>'.$data['name'].'</td>
					<td>'.$data['quantity'].'</td>
                    <td>'.number_format((float)($price_without_tax), 2, '.', '').'</td>
					<td>'.$tax_title.'</td>
                    <td>'.number_format((float)$data['tax'], 2, '.', '').'</td>
					<td>'.number_format((float)($data['quantity']*(($price_without_tax)+$data['tax'])), 2, '.', '').'</td>
                    <td>'.$data1['order_id'].'</td>
					<td>'.$data1['discount_type'].'</td>
					<td>'.$data1['discount_value'].'</td>
                   </tr>';

			}
		}
		echo '</tbody>
        </table>';
		exit;
          
        
    }
	public function sales_qnty() 
	{
		$this->load->language('report/product_store_wise_sales');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}
       
		if (isset($this->request->get['filter_name_id'])) {
			$filter_name_id = $this->request->get['filter_name_id'];
		} else {
			$filter_name_id = '';
		}
                if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d');
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
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

                if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}


		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}

		if (isset($this->request->get['filter_name_id'])) {
			$url .= '&filter_name_id=' . $this->request->get['filter_name_id'];
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
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('report/product_sales/sales_qnty', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$this->load->model('report/product_storewisesales');
        $this->load->model('setting/store');
		$data['products'] = array();

		$filter_data = array(
			'filter_name'	     => $filter_name,
			'filter_name_id'     => $filter_name_id,
			'filter_store'       => $filter_store,
                        'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);
		$t1=$this->model_report_product_storewisesales->getTotalsales($filter_data);
		$product_total = $t1->num_rows;
		$total_amount=0;
		$a=1;
		foreach($t1->rows as $result1)
		{
			//print_r($result1['total']);
			
			$total_amount=$total_amount+$result1['total'];
			$a++;
			
			
		}
		
		$results = $this->model_report_product_storewisesales->getSales($filter_data);
		//$product_total = $results[0]['num_rows'];
		foreach ($results as $result) 
		{ 
			//$total_amount=$total_amount+$result['total']+($result['tax']*$result['quantity']);
               // $product_total = $result["totals"];
			$data['products'][] = array(
				'name'       => $result['name'],
				'product_id'      => $result['product_id'],
                'store_name'      => strtoupper($result['store_name']),
				'quantity'   => $result['quantity'],
				
				'total'      => $this->currency->format($result['total']+($result['tax']*$result['quantity']), $this->config->get('config_currency'))
			);
		}
		$this->load->language('common/information');
		$data['tool_tip']=$this->language->get('product_storewisesales');

		$data['tool_tip_style']=$this->language->get('tool_tip_style');
		$data['tool_tip_class']=$this->language->get('tool_tip_class');
		$data['heading_title'] = $this->language->get('heading_title');
		$data['total_amount'] = $total_amount;
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_all_status'] = $this->language->get('text_all_status');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_total'] = $this->language->get('column_total');

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];


		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/product_sales/sales_qnty', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		
		$data['filter_name'] = $filter_name;
                $data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
                $data['filter_name_id'] = $filter_name_id;
                $data['filter_store'] = $filter_store;
                $data['stores'] = $this->model_setting_store->getStores();
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/product_store_wise_sales.tpl', $data));
	}


        public function download_excel_sales_qnty()
		{
        
               if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_name_id'])) {
			$filter_name_id = $this->request->get['filter_name_id'];
		} else {
			$filter_name_id = '';
		}

		if (isset($this->request->get['filter_store'])) {
		        $filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = '';
		}


               if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			//$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}

 
                $this->load->model('report/product_storewisesales');
                
		$data['products'] = array();

		$filter_data = array(
			'filter_name'	     => $filter_name,
			'filter_name_id'     => $filter_name_id,
                        'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_store'       => $filter_store
		);

		

		$results = $this->model_report_product_storewisesales->getSales($filter_data);

		  $fields = array(
        'Store Name',
        'Product Name',
        'Sale Quantity',
        'Total'
    );
           $fileIO = fopen('php://memory', 'w+');
        fputcsv($fileIO, $fields,';');
     
       foreach ($results as $result)
        {
         
           $fdata=array(
                           
                            $result['store_name'],
                             $result['name'],
                            $result['quantity'],
                            number_format((float)($result['total']+($result['tax']*$result['quantity'])),2),
               );
             fputcsv($fileIO,  $fdata,";");
            
       }
         
          fseek($fileIO, 0);
             
           
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment;filename="product_storewisesales_'.date('dMy').'.xls"');
            header('Cache-Control: max-age=0');
                    fpassthru($fileIO);  
                    fclose($fileIO);  
        
    }
        public function email_excel() {
           
    		
		$data['orders'] = array();

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			//$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			//$filter_date_end = date('Y-m-d');
		}
                           if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			//$filter_name = '';
		}
                	if (isset($this->request->get['filter_name_id'])) {
			$filter_name_id = $this->request->get['filter_name_id'];
		} 
		
		$this->load->model('report/product_sale');
		
		$filter_data = array(
                                         'filter_store'	     => $filter_store,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_name'           => $filter_name_id
		);

		//$order_total = $this->model_report_product_sale->getTotalOrders($filter_data);

		$results = $this->model_report_product_sale->getOrders($filter_data);
      //  exit;
    include_once '../system/library/PHPExcel.php';
    include_once '../system/library/PHPExcel/IOFactory.php';
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->createSheet();
    
    $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");

    $objPHPExcel->setActiveSheetIndex(0);

    // Field names in the first row
    $fields = array(
        'Sale Date ',
        'Store Name',
        'Store ID',
        'Product Name',
        'Quantity',
        'Rate(without tax)',
        'Tax title',
        'Tax rate',
        'Total (Sales + Tax)'
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
        $Total=$data['Total_sales']+$data['Total_tax'];
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, date('Y-m-d',strtotime($data['dats'])));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data['store_name']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data['store_id']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data['name']);
        
        //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data['No_of_orders']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data['qnty']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, number_format((float)($data['Total_sales']/$data['qnty']), 2, '.', ''));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data['tax_title']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, number_format((float)$data['Total_tax'], 2, '.', ''));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, number_format((float)($data['qnty']*(($data['Total_sales']/$data['qnty'])+$data['Total_tax'])), 2, '.', ''));
        
        
            
        

        $row++;
    }


    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $filename='product_sales_report_'.date('ymdhis').'.xls';
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

                $mail->Subject    = "Product Sales Report";

                $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

                $mail->MsgHTML($body);
                
                //to get the email of supplier
                
                $mail->AddAddress('chetan.singh@akshamaala.com', "Chetan Singh");
                $mail->AddCC('subhash.jha@unnati.world', "Subhash Jha");
                $mail->AddBCC('vipin.kumar@aspltech.com', "Vipin");

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
}