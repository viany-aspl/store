<?php
class ControllerDashboardChart extends Controller {
	public function index() {
		$this->load->language('dashboard/chart');

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_day'] = $this->language->get('text_day');
		$data['text_week'] = $this->language->get('text_week');
		$data['text_month'] = $this->language->get('text_month');
		$data['text_year'] = $this->language->get('text_year');
		$data['text_view'] = $this->language->get('text_view');

		$data['token'] = $this->session->data['token'];

		return $this->load->view('dashboard/chart.tpl', $data);
	}
	
	

             public function sale_chart_month() {
		$this->load->language('dashboard/chart');

		$json = array();
		
		$this->load->model('report/sale');
		$this->load->model('report/customer');

		$json['sale'] = array();
		$json['customer'] = array();
		$json['xaxis'] = array();

		$json['sale']['label'] = 'Sales';
		//$json['customer']['label'] = $this->language->get('text_customer');
		$json['sale']['data'] = array();
		$json['customer']['data'] = array();

		if (isset($this->request->get['range'])) {
			$range = $this->request->get['range'];
		} else {
			$range = 'day';
		}

		switch ($range) {
			default:
			case 'day':
				$results = $this->model_report_sale->getTotalSaleByDay();

				foreach ($results as $key => $value) {
					$json['sale']['data'][] = array($key, $value['total']);
				}

				$results = $this->model_report_customer->getTotalCustomersByDay();

				foreach ($results as $key => $value) {
					//$json['customer']['data'][] = array($key, $value['total']);
				}

				for ($i = 0; $i < 24; $i++) {
					$json['xaxis'][] = array($i, $i);
				}
				break;
			case 'week':
				$results = $this->model_report_sale->getTotalSaleByWeek();

				foreach ($results as $key => $value) {
					$json['sale']['data'][] = array($key, $value['total']);
				}
				
				$results = $this->model_report_customer->getTotalCustomersByWeek();

				foreach ($results as $key => $value) {
					//$json['customer']['data'][] = array($key, $value['total']);
				}

				$date_start = strtotime('-' . date('w') . ' days');

				for ($i = 0; $i < 7; $i++) {
					$date = date('Y-m-d', $date_start + ($i * 86400));

					$json['xaxis'][] = array(date('w', strtotime($date)), date('D', strtotime($date)));
				}
				break;
			case 'month':
				$results = $this->model_report_sale->getTotalSaleByMonth();

				foreach ($results as $key => $value) {
					$json['sale']['data'][] = array($key, $value['total']);
				}

				$results = $this->model_report_customer->getTotalCustomersByMonth();

				foreach ($results as $key => $value) {
					//$json['customer']['data'][] = array($key, $value['total']);
				}

				for ($i = 1; $i <= date('t'); $i++) {
					$date = date('Y') . '-' . date('m') . '-' . $i;

					$json['xaxis'][] = array(date('j', strtotime($date)), date('d', strtotime($date)));
				}
				break;
			case 'year':
				$results = $this->model_report_sale->getTotalSaleByYear();

				foreach ($results as $key => $value) {
					$json['sale']['data'][] = array($key, $value['total']);
				}

				$results = $this->model_report_customer->getTotalCustomersByYear();

				foreach ($results as $key => $value) {
					//$json['customer']['data'][] = array($key, $value['total']);
				}

				for ($i = 1; $i <= 12; $i++) {
					$json['xaxis'][] = array($i, date('M', mktime(0, 0, 0, $i)));
				}
				break;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}