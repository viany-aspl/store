<?php
class ControllerCatalogHsn extends Controller {
	private $error = array();

	public function index() 
	{
		$this->load->language('setting/store');

		$this->document->setTitle('HSN');

		$this->load->model('catalog/hsn');

		$this->getList();
	}
       	public function add() 
		{
		
		$this->document->setTitle("HSN");

		$this->load->model('catalog/hsn');

		
		$this->getform();
	}

	public function edit() 
	{
		$this->document->setTitle("HSN");

		$this->load->model('catalog/hsn');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && (!empty($this->request->post['sid'])) ) 
					{ 
                            if($this->request->post['hsn_name'] !="")
                            {
                                $hsn_id = $this->model_catalog_hsn->edithsn($this->request->post);
                                if($hsn_id)
                                {			
                                    $this->session->data['success'] ="HSN Updated Sucessfully !";

                                    $this->response->redirect($this->url->link('catalog/hsn', 'token=' . $this->session->data['token'], 'SSL'));
                                }
                                else 
                                {
                                    $this->session->data['error'] ="Some error occur!";

                                    $this->response->redirect($this->url->link('catalog/hsn', 'token=' . $this->session->data['token'], 'SSL'));
                                
                                }
                        
                    		}
                	}
		$this->getform();
	}
	public function deletehsn() 
	{

		$this->load->model('catalog/hsn');

		if (!empty($this->request->get['sid'])) 
					{ 
                            
                                $hsn_id = $this->model_catalog_hsn->deletethsn($this->request->get);
                                if($hsn_id)
                                {			
                                    $this->session->data['success'] ="HSN Deleted Sucessfully !";

                                    $this->response->redirect($this->url->link('catalog/hsn', 'token=' . $this->session->data['token'], 'SSL'));
                                }
                                else 
                                {
                                    $this->session->data['error'] ="Some error occur!";

                                    $this->response->redirect($this->url->link('catalog/hsn', 'token=' . $this->session->data['token'], 'SSL'));
                                
                                }
                        
                    		
                	}
		$this->getform();
	}

	protected function getList() 
	{
		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		if (isset($this->request->get['page'])) {
			$page=$this->request->get['page'];
		}
		else
		{
			$page=1;
		}
		$data['breadcrumbs'] = array();

		

		$data['breadcrumbs'][] = array(
			'text' => 'Unit List',
			'href' => $this->url->link('catalog/hsn', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['token'] = $this->session->data['token'];

		$data['hsn'] = array();
		$filter_data = array(
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);
        $resultsdata=$this->model_catalog_hsn->gethsn($filter_data);
		$order_total= $resultsdata->num_rows;

		$results = $resultsdata->rows;

		foreach ($results as $result) 
		{
			$data['hsn'][] = array(
                                'sid' => $result['sid'],
				'hsn_code' => $result['hsn_code'],
				'hsn_name'     => $result['hsn_name'],
				'tax_class_name' => $result['tax_class_name'],
				'tax_class_id'     => $result['tax_class_id'],
				'editlink'=>$this->url->link('catalog/hsn/edit', 'token=' . $this->session->data['token'] . '&sid='.$result['sid'] , 'SSL'),
				'deletelink'=>$this->url->link('catalog/hsn/deletehsn', 'token=' . $this->session->data['token'] . '&sid='.$result['sid'] , 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		

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
		$data['redirect']=$this->url->link('catalog/hsn/add', 'token=' . $this->session->data['token']);
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/hsn', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/hsn_list.tpl', $data));
	}
    protected function getform() 
	{
		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'HSN Add',
			'href' => $this->url->link('catalog/hsn', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['token'] = $this->session->data['token'];
	        	$data['cancel']=$this->url->link('catalog/hsn', 'token=' . $this->session->data['token'], 'SSL');
                	if (($this->request->server['REQUEST_METHOD'] == 'POST') ) 
					{
                            if($this->request->post['hsn_name'] !="")
                            {
                                $hsn_id = $this->model_catalog_hsn->addhsn($this->request->post);
                                if($hsn_id)
                                {			
                                    $this->session->data['success'] ="HSN Added Sucessfully !";

                                    $this->response->redirect($this->url->link('catalog/hsn', 'token=' . $this->session->data['token'], 'SSL'));
                                }
                                else 
                                {
                                    $this->session->data['error'] ="This HSN Code is already Exist!";

                                    $this->response->redirect($this->url->link('catalog/hsn', 'token=' . $this->session->data['token'], 'SSL'));
                                
                                }
                        
                    		}
                	}


		$data['hsn'] = array();
		if(!empty($this->request->get['sid']))
		{

			$data['hsncode'] = $this->model_catalog_hsn->gethsnbyid(array('sid'=>$this->request->get['sid']))->row;
			
			$data['heading_title'] = 'Edit HSN';
			$data['action']=$this->url->link('catalog/hsn/edit', 'token=' . $this->session->data['token'], 'SSL');
		}
		else
		{
			$data['heading_title'] = 'Add HSN';
			$data['action']=$this->url->link('catalog/hsn/add', 'token=' . $this->session->data['token'], 'SSL');
		}
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_url'] = $this->language->get('column_url');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}
		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses()->rows;
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/hsn_add.tpl', $data));
	}
      

}