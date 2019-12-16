<?php
class ControllerDashboardActivity extends Controller {

	public function index() { 
		$this->load->language('dashboard/recent');

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_no_results'] = $this->language->get('text_no_results');
		
		
		$data['button_view'] = $this->language->get('button_view');

		$data['token'] = $this->session->data['token'];

		// Last 5 Orders
		$data['orders'] = array();

		$filter_data = array(
			'sort'  => 'o.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 5
		);

		if($this->user->getGroupId()!="1")
                    {
		$filter_data = array(
                    'filter_user_id' => $this->user->getId(),
			'sort'  => 'o.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 5
		);
                }
		$this->load->model('sale/order');
		$results = $this->model_sale_order->getTop_5_Products($filter_data);

		foreach ($results as $result) { //print_r($result);
			$data['orders'][] = array(
				'product_name'   => $result['model'],
				
				'sales_of_qnty'      => $result['sales_of_qnty']
			);
		}

		return $this->load->view('dashboard/activity.tpl', $data);
	}


}
