<?php
class ControllerSettingBillcontrol extends Controller {
    private $error = array();

    public function index() 
    {
        $this->document->setTitle("Bill Controls");
		$url = '';

		if (isset($this->request->get['page'])) 
		{
			$url .= '&page=' . $this->request->get['page'];
			$page = $this->request->get['page'];
		}
		else 
		{
			$page = 1;
		}
		if (isset($this->request->get['filter_store'])) 
		{
			$url .= '&filter_store=' . $this->request->get['filter_store'];
			$data['filter_store']=$this->request->get['filter_store'];
		}
		$filter_data = array(
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin'),
			'filter_store'=>$data['filter_store']
		);
		$this->load->model('setting/store');
		$this->load->model('setting/setting');
		$ret_data=$this->model_setting_store->getStoresWeb($filter_data);
		$store_total = $ret_data[0]['totalrows'];//$this->model_setting_store->getTotalStores($filter_data);

		$results = $ret_data;
		foreach ($results as $result) 
		{ 
			
			$data['stores'][] = array(
				'store_id' => $result['store_id'],
				'config_telephone' => $result['config_telephone'],
				'name'     => $result['name'],
                'config_storestatus'     => $result['config_storestatus'],
                'config_storetype'     => $result['config_storetype'],
				'currentstatus'=>$this->model_setting_setting->getBillingStatus('billing',$result['store_id'])[0],
				'msg'=>$this->model_setting_setting->getBillingStatus('billing',$result['store_id'])[1]
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $store_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('setting/billcontrol', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($store_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($store_total - $this->config->get('config_limit_admin'))) ? $store_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $store_total, ceil($store_total / $this->config->get('config_limit_admin')));

        
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['token']=$this->session->data['token'];
	
	
		$data['currentstatusALL']=$this->model_setting_setting->getBillingStatusALL('billing');
        $this->response->setOutput($this->load->view('setting/bill_control_form.tpl', $data));
    }
    public function updatestatusALL() 
    {
        $this->load->model('setting/setting');
      	$this->request->get['currentstatus'];
	echo $this->model_setting_setting->updateBillingStatusALL('billing',$this->request->get['currentstatus'],$this->request->get['closed_message']);
    }
    public function updatestatus() 
    {
        $this->load->model('setting/setting');
      	$this->request->get['currentstatus'];
	echo $this->model_setting_setting->updateBillingStatus('billing',$this->request->get['currentstatus'],$this->request->get['store_id'],$this->request->get['closed_message']);
    } 
       
}