<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
error_reporting(0);
ini_set('max_execution_time', 3000);  //3000 seconds = 50 minutes 

class ControllerReportSaleSummary extends Controller 
{
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function index() 
	{
		$this->load->language('report/sale_report');
		$this->load->language('report/sale_order');
		$this->document->setTitle('Sale Summary');

		$this->load->language('common/information');
		$data['tool_tip']=$this->language->get('report/sale_report');

		$data['tool_tip_style']=$this->language->get('tool_tip_style');
		$data['tool_tip_class']=$this->language->get('tool_tip_class');


		
		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}

		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = 0;
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
			'text' => 'Sale Summary(Category)',
			'href' => $this->url->link('report/sale_summary', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
	
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_group'] = $this->language->get('entry_group');

		$this->load->model('setting/store');
        $this->load->model('report/sale_summary');
		$data['orders'] = array();

		$filter_data = array(
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_store'           => $filter_store,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$t1=$this->model_report_sale_summary->getTotalSale_summary($filter_data);
		$data['orders'] = array();
        $results = $this->model_report_sale_summary->getSale_summary($filter_data);
        
        $total_cash_all=$t1["Cash"];
        $total_credit_all=$t1["Credit"];
        $total_discount_all=$t1["Discount"];
		foreach ($results as $result) 
		{
			//print_r($result);exit;
			$store_id='';
			$store_name='';
			$openbilling= '';
			$ledbilling= '';
			$cashsum=0;
			$creditsum=0;
			$discountsum=0;
			
			$CC_Discount=0;
			$Credit_Discount=0;
			$Cash_Discount=0;
			$Cash_Credit=0;
			$Credit=0;
			$Cash=0;
			
			foreach($result as $result2)
			{
				$store_id= $result2['store_id'];
				$openbilling= $openbilling+$result2['openbilling'];
				$ledbilling= $ledbilling+$result2['ledbilling'];
				$store_name=$result2['store_name'];
				$order_count=$result2['cash_order'];
				$cashsum=$cashsum+$result2['Cash'];	
				$creditsum=$creditsum+$result2['Credit'];
				$discountsum=$discountsum+$result2['discount'];
				//echo $result2['type'];echo '</br/></br/></br/>';
				if($result2['type']=='CC Discount')
				{
					$CC_Discount=$result2['order_count'];
				}
				if($result2['type']=='Credit Discount')
				{
					$Credit_Discount=$result2['order_count'];
				}
				if($result2['type']=='Cash Discount')
				{
					$Cash_Discount=$result2['order_count'];
				}
				if($result2['type']=='Cash Credit')
				{
					$Cash_Credit=$result2['order_count'];
				}
				if($result2['type']=='Credit')
				{
					$Credit=$result2['order_count'];
				}
				if($result2['type']=='Cash')
				{
					$Cash=$result2['order_count'];
				}
				
			}
			$data['orders'][] = array(
                             		'store_id' => $store_id,
									'store_name' => $store_name,
									'CC_Discount_count' => $CC_Discount,
									'Credit_Discount_count' => $Credit_Discount,
									'Cash_Discount_count' => $Cash_Discount,
									'Cash_Credit_count' => $Cash_Credit,
									'Credit_count' => $Credit,
									'Cash_count' => $Cash,
									'openbilling' => $openbilling,
									'ledbilling' => $ledbilling,
									'cash'		=>$this->currency->format($cashsum),	
									'credit'	=>$this->currency->format($creditsum),
									'discount'	=>$this->currency->format($discountsum),
									'total'=>$this->currency->format($cashsum+$creditsum+$discountsum)
								);
			
			$total_cash=$total_cash+$cashsum;
            $total_credit=$total_credit+$creditsum;
			$total_discount=$total_discount+$discountsum;
			
			$order_total=$result[0]['totalcount'];
		}
		
		$data['token'] = $this->session->data['token'];
                
		$url = '';
				
		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}	
		
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/sale_summary', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_store'] = $filter_store;
		$data['stores'] = $this->model_setting_store->getStores();
	
		$data['total_cash'] = $total_cash;
        $data['total_credit'] = $total_credit;
        $data['total_discount'] = $total_discount;

		$data['total_cash_all'] = $total_cash_all;
        $data['total_credit_all'] = $total_credit_all;
        $data['total_discount_all'] = $total_discount_all;                   
		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/sale_summary.tpl', $data));
	}
	//////////////////////////////////////////////
	public function download_excel() 
	{

		if (isset($this->request->get['filter_date_start'])) 
		{
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}

		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = 0;
		}


		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		

		$filter_data = array(
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_store'           => $filter_store
			
		);

        $this->load->model('report/sale_summary');
       

		$data['orders'] = array();

		$results = $this->model_report_sale_summary->getSale_summary($filter_data);
                
		                
    include_once '../system/library/PHPExcel.php';
    include_once '../system/library/PHPExcel/IOFactory.php';
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->createSheet();
    
    $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");

    $objPHPExcel->setActiveSheetIndex(0);

    // Field names in the first row
    $fields = array(
         'Store name', 
		'Store ID',		 
		 'Cash', 	
		 'Credit', 	
		 'Disc',
		 'No. Cash', 	
		 'No. Cash Credit',
		 'No. Cash Disc',
		 'No. Credit',
		 'No. Credit Disc',
		 'No. CC Disc', 	
		 'No. OB', 	
		 'No. ILB',
		 'Total'
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
			//print_r($result);exit;
			$store_id='';
			$store_name='';
			$openbilling= '';
			$ledbilling= '';
			$cashsum=0;
			$creditsum=0;
			$discountsum=0;
			
			$CC_Discount=0;
			$Credit_Discount=0;
			$Cash_Discount=0;
			$Cash_Credit=0;
			$Credit=0;
			$Cash=0;
			
			foreach($result as $result2)
			{
				$store_id= $result2['store_id'];
				$openbilling= $openbilling+$result2['openbilling'];
				$ledbilling= $ledbilling+$result2['ledbilling'];
				$store_name=$result2['store_name'];
				$order_count=$result2['cash_order'];
				$cashsum=$cashsum+$result2['Cash'];	
				$creditsum=$creditsum+$result2['Credit'];
				$discountsum=$discountsum+$result2['discount'];
				//echo $result2['type'];echo '</br/></br/></br/>';
				if($result2['type']=='CC Discount')
				{
					$CC_Discount=$result2['order_count'];
				}
				if($result2['type']=='Credit Discount')
				{
					$Credit_Discount=$result2['order_count'];
				}
				if($result2['type']=='Cash Discount')
				{
					$Cash_Discount=$result2['order_count'];
				}
				if($result2['type']=='Cash Credit')
				{
					$Cash_Credit=$result2['order_count'];
				}
				if($result2['type']=='Credit')
				{
					$Credit=$result2['order_count'];
				}
				if($result2['type']=='Cash')
				{
					$Cash=$result2['order_count'];
				}
				
			}
			
			
			$col = 0;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $store_name); 
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $store_id); 
     
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $cashsum);  
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $creditsum);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $discountsum);
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $Cash);  
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $Cash_Credit);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $Cash_Discount);
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $Credit);  
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $Credit_Discount);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $CC_Discount);
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $openbilling);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $ledbilling);
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $row, ($cashsum+$creditsum+$discountsum));
          
             //'Cash_subsidy'	=>$this->currency->format($result['Cash_subsidy']),
   
        $row++;
		}
    

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    // Sending headers to force the user to download the file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="sale_summary_subsidycash_'.date('dMy').'.xls"');
    header('Cache-Control: max-age=0');

    $objWriter->save('php://output');
        
    }
//////////////////////////////////////////////


}