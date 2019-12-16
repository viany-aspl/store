<?php
class ControllerDashboardOrder extends Controller {
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
	public function index() {
		$this->load->language('dashboard/order');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_view'] = $this->language->get('text_view');

		$data['token'] = $this->session->data['token'];

		// Total Orders
		$this->adminmodel('sale/order');
		
		
		$order_total = $this->model_sale_order->getTotalOrders(array('filter_store'=>$this->user->getStoreId()));
		
		if ($order_total > 1000000000000) {
			$data['total'] = round($order_total / 1000000000000, 1) . 'T';
		} elseif ($order_total > 1000000000) {
			$data['total'] = round($order_total / 1000000000, 1) . 'B';
		} elseif ($order_total > 1000000) {
			$data['total'] = round($order_total / 1000000, 1) . 'M';
		} elseif ($order_total > 1000) {
			$data['total'] = round($order_total / 1000, 1) . 'K';						
		} else {
			$data['total'] = $order_total;
		}
		return $data['total'];
	}
}
