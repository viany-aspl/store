<?php
class ControllerDashboardSale extends Controller {
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
		

		$this->adminmodel('report/sale');

		
		$sale_total = $this->model_report_sale->getTotalSales(array('filter_store'=>$this->config->get('config_store_id')));

		if ($sale_total > 1000000000000) {
			$data['total'] = round($sale_total / 1000000000000, 1) . 'T';
		} elseif ($sale_total > 1000000000) {
			$data['total'] = round($sale_total / 1000000000, 1) . 'B';
		} elseif ($sale_total > 1000000) {
			$data['total'] = round($sale_total / 1000000, 1) . 'M';
		} elseif ($sale_total > 1000) {
			$data['total'] = round($sale_total / 1000, 1) . 'K';
		} else {
			$data['total'] = round($sale_total);
		}

                $credit_total = $this->model_report_sale->getTotalCredit(array('filter_store'=>$this->config->get('config_store_id')));

		if ($credit_total > 1000000000000) {
			$data['credit'] = round($credit_total / 1000000000000, 1) . 'T';
		} elseif ($credit_total > 1000000000) {
			$data['credit'] = round($credit_total / 1000000000, 1) . 'B';
		} elseif ($credit_total > 1000000) {
			$data['credit'] = round($credit_total / 1000000, 1) . 'M';
		} elseif ($credit_total > 1000) {
			$data['credit'] = round($credit_total / 1000, 1) . 'K';
		} else {
			$data['credit'] = round($credit_total);
		}
                $cash_total=$sale_total-$credit_total;
                if ($cash_total > 1000000000000) {
			$data['cash'] = round($cash_total / 1000000000000, 1) . 'T';
		} elseif ($cash_total > 1000000000) {
			$data['cash'] = round($cash_total / 1000000000, 1) . 'B';
		} elseif ($cash_total > 1000000) {
			$data['cash'] = round($cash_total / 1000000, 1) . 'M';
		} elseif ($cash_total > 1000) {
			$data['cash'] = round($cash_total / 1000, 1) . 'K';
		} else {
			$data['cash'] = round($cash_total);
		}
		return $data;
	}
        
}
