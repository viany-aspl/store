<?php
class ControllerReportIncommingcallreport extends Controller 
{
	public function index() 
	{
		$this->load->language('report/customer_activity');

		$this->document->setTitle('Incoming Call Summary Report');


		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		}
		else {
			$filter_date_start = date('Y-m').'-01';
		}
 

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
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

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
			'text' => $this->language->get('text_home')
		);

		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('report/incommingcall_report', 'token=' . $this->session->data['token'] . $url, 'SSL'),
			'text' => 'Incoming Call Report'
		);

		$this->load->model('report/incommingcall_report');

		$data['activities'] = array();

		$filter_data = array(			
			'filter_date_start'	=> $filter_date_start,
			'filter_date_end'	=> $filter_date_end,
			'start'             => ($page - 1) * 20,
			'limit'             => 20
		);

		//$activity_total = $this->model_report_incommingcall_report->getCustomerActivitiesTotal($filter_data);
		$ret_data=$this->model_report_incommingcall_report->getCustomerActivities($filter_data);
		$data['activities']=$results = $ret_data->rows;
		$activity_total =$ret_data->num_rows;
		

		$data['heading_title'] ='Incomming Call Report';
		$this->load->language('common/information');
		$data['tool_tip']=$this->language->get('report/incommingcall_report');

		$data['tool_tip_style']=$this->language->get('tool_tip_style');
		$data['tool_tip_class']=$this->language->get('tool_tip_class');
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_comment'] = $this->language->get('column_comment');
		$data['column_ip'] = $this->language->get('column_ip');
		$data['column_date_added'] = $this->language->get('column_date_added');

		$data['entry_customer'] = $this->language->get('entry_customer');
		$data['entry_ip'] = $this->language->get('entry_ip');
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');

		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		$pagination = new Pagination();
		$pagination->total = $activity_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/incommingcall_report', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($activity_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($activity_total - $this->config->get('config_limit_admin'))) ? $activity_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $activity_total, ceil($activity_total / $this->config->get('config_limit_admin')));

		$data['filter_customer'] = $filter_customer;
		$data['filter_ip'] = $filter_ip;
		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/incommingcall_report.tpl', $data));
	}
    public function download_excel() 
	{
		

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		}
		else {
			$filter_date_start = date('Y-m').'-01';
		}
 

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}

		$this->load->model('report/incommingcall_report');

		$data['activities'] = array();

		$filter_data = array(			
			'filter_date_start'	=> $filter_date_start,
			'filter_date_end'	=> $filter_date_end,
			'start'             => ($page - 1) * 20,
			'limit'             => 20
		);

		
		$ret_data=$this->model_report_incommingcall_report->getCustomerActivities($filter_data);
		$data['activities']=$results = $ret_data->rows;
		$fields = array(
        'Date',
		'Total Call',
		'Missed Call',
		'Busy', 	
		'Call Later', 	
		'Not Picking',
		'Attempt Later', 	
		'To be attempted', 	
		'Not reachable',
		'Switch Off', 	
		'Enquiry', 	
		'General Call', 	
		'Recieved Call',	
		'Not Interested', 	
		'Answered'
        );
		
		foreach ($data['activities'] as $result)
        {
            
            $fdata[]=array(
                ($result['_id']['yearMonthDay']),
                $result['count'],
                $result['missedcall'],
				$result['Busy'],
                $result['CallLater'],
                $result['NotPicking'],
				$result['AttemptLater'],
				$result['Tobeattempted'],
				$result['Notreachable'],
				$result['SwitchOff'],
				$result['Enquiry'],
				$result['GeneralCall'],
				$result['RecievedCall'],
				$result['NotInterested'],
				$result['Answered']
                );
                
            
        }
		
		$this->download_excel_2($fields,$fdata,'Incoming-Call-Summary-');
	}
	private function download_excel_2($fields,$fdatas,$filename)
	{
		//print_r($fdatas);exit;
        $fileIO = fopen('php://memory', 'w+');
        fputcsv($fileIO, $fields,';');
        
        foreach($fdatas as $fdata)
		{
			fputcsv($fileIO,  $fdata,";");
		}
        fseek($fileIO, 0);
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment;filename="'.$filename.date('Y-m-d-h-i-s').'"');
        header('Cache-Control: max-age=0');
        fpassthru($fileIO);  
        fclose($fileIO);    
	}
        
}