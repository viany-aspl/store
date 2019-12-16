<?php
	class ControllerPurchaseSupplier extends Controller {
		public function index() {
			$url = '';
			$data['add'] = $this->url->link('purchase/supplier/add_supplier', 'token=' . $this->session->data['token'] . $url, true);
			$data['delete'] = $this->url->link('purchase/supplier/delete_supplier', 'token=' . $this->session->data['token'] . $url, true);
			$data['filter'] = $this->url->link('purchase/supplier/filter_supplier', 'token=' . $this->session->data['token'] . $url, true);
			$data['edit'] = $this->url->link('purchase/supplier/edit_supplier_form', 'token=' . $this->session->data['token'] . $url, true);
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
				'text' => "Home",
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => "Suppliers",
				'href' => $this->url->link('purchase/supplier', 'token=' . $this->session->data['token'] . $url, true)
			);
			
			$this->load->model('purchase/supplier_group');
			$data['supplier_groups'] = $this->model_purchase_supplier_group->get_all_supplier_groups();
			
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$this->load->model('purchase/supplier');
			
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}
			if (isset($this->request->get['name'])) {
				$data['filter_name']=$name = $this->request->get['name'];
			} else {
				$name = '';
			}
			if (isset($this->request->get['supplier_group'])) {
				$data['filter_supplier_group']=$supplier_group = $this->request->get['supplier_group'];
			} else {
				$supplier_group = '';
			}
			$start = ($page - 1) * 20;
			$limit = 20;
			
			$filter_data=array(
			'start'=>$start,
			'limit'=>$limit,
			'name'=>$name,
			'supplier_group'=>$supplier_group
			);
			
			$supplier_data=$this->model_purchase_supplier->get_all_suppliers($filter_data);
			$data['suppliers'] = $supplier_data->rows;
			
			$total_suppliers = $supplier_data->num_rows;
			$data['token']=$this->session->data['token'];
			
			/*pagination*/
			$pagination = new Pagination();
			$pagination->total = $total_suppliers;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('purchase/supplier', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);
			
			$data['pagination'] = $pagination->render();
			
			$data['results'] = sprintf($this->language->get('text_pagination'), ($total_suppliers) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_suppliers - $this->config->get('config_limit_admin'))) ? $total_suppliers : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_suppliers, ceil($total_suppliers / $this->config->get('config_limit_admin')));
			$_SESSION['success_message']=$this->session->data['success'];
			unset($this->session->data['success']);
			/*pagination*/
			
			$this->response->setOutput($this->load->view('purchase/supplier_list.tpl',$data));
		}
		/*---------------------Add supplier function starts here--------------*/
		
		public function add_supplier()
		{
			$data['heading_title'] = 'Add Supplier';
			
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) 
			{
				$this->load->model('purchase/supplier');
				$supplier_data=$this->model_purchase_supplier->insert_supplier($this->request->post);
				//print_r($this->request->post);
				//exit;
				$this->session->data['success'] = 'Supplier added sucessfully';
				$this->response->redirect($this->url->link('purchase/supplier', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			}
			else
			{
				if(isset($_SESSION['error_address'])||isset($_SESSION['error_account'])||isset($_SESSION['error_bank'])||isset($_SESSION['error_ifsc'])||isset($_SESSION['error_district'])||isset($_SESSION['error_firstname']) || isset($_SESSION['error_firstname']) || isset($_SESSION['error_firstname']) || isset($_SESSION['error_firstname']))
				{
				$data['supplier_group_id'] = $this->request->post['supplier_group_id'];
				$data['supplier_group_name'] = $this->request->post['supplier_group_name'];
				$data['first_name'] = $this->request->post['firstname'];
				$data['last_name'] = $this->request->post['lastname'];
				$data['email'] = $this->request->post['email'];
				$data['telephone'] = $this->request->post['telephone'];
				$data['fax'] = $this->request->post['fax'];
				$data['bank'] = $this->request->post['bank'];
				$data['address'] = $this->request->post['bankaddress'];	
				$data['ifsc'] = $this->request->post['ifsc'];
				$data['account']=$this->request->post['account'];
				$data['location'] =$this->request->post['location'];
				$data['bankaddress'] =$this->request->post['bankaddress'];
				
				$data['gst']=$this->request->post['gst'];
				$data['status']=$this->request->post['status'];
				$data['pan']=$this->request->post['pan'];
				}
				$url = '';
				$data['breadcrumbs'] = array();

				$data['breadcrumbs'][] = array(
					'text' => "Home",
					'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
				);

				$data['breadcrumbs'][] = array(
					'text' => "Suppliers",
					'href' => $this->url->link('purchase/supplier', 'token=' . $this->session->data['token'] . $url, true)
				);
				$data['action'] = $this->url->link('purchase/supplier/add_supplier', 'token=' . $this->session->data['token'] . $url, true);
				$data['cancel'] = $this->url->link('purchase/supplier', 'token=' . $this->session->data['token'] . $url, true);
				$data['header'] = $this->load->controller('common/header');
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['footer'] = $this->load->controller('common/footer');
				$this->load->model('purchase/supplier_group');
				$data['supplier_groups'] = $this->model_purchase_supplier_group->get_all_supplier_groups();
				$this->response->setOutput($this->load->view('purchase/supplier_form.tpl', $data));
			
			}
			
		}
		
		/*---------------------Add supplier function ends here--------------*/
		
		private function validateForm()
		{
			if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
				$_SESSION['error_firstname'] = "First Name must be between 1 and 32 characters!";
				return false;
			}

			else if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
				$_SESSION['error_lastname'] = "Last Name must be between 1 and 32 characters!";
				return false;
			}

			else if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
				$_SESSION['error_email'] = "E-Mail Address does not appear to be valid!";
				return false;
			}
			
			else if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
				$_SESSION['error_telephone'] = "Telephone must be between 3 and 32 characters!";
				return false;
			}
			else if ((utf8_strlen($this->request->post['fax']) < 3) || (utf8_strlen($this->request->post['fax']) > 32)) {
				$_SESSION['error_district'] = "District must be between 3 and 32 characters!";
				return false;
			}
			
			else if ((utf8_strlen($this->request->post['ifsc']) < 3) || (utf8_strlen($this->request->post['ifsc']) > 11)) {
				$_SESSION['error_ifsc'] = "IFSC Code must be between 1 and 11 characters!";
				return false;
			}
			else if ((utf8_strlen($this->request->post['bank']) < 3) || (utf8_strlen($this->request->post['bank']) > 50)) {
				$_SESSION['error_bank'] = "Bank Name must be between 3 and 50 characters!";
				return false;
			}

			else if ((utf8_strlen($this->request->post['account']) < 8) || (utf8_strlen($this->request->post['account']) > 25)) {
				$_SESSION['error_account'] = "Bank Account Number must be between 8 and 25 characters!";
				return false;
			}
			else if ((utf8_strlen($this->request->post['bankaddress']) < 1)) {
				$_SESSION['error_address'] = "Bank Address Number must be between 1 and 10 characters!";
				return false;
			}
			else
			{	
				return true;
			}
			
		}
		
		
		/*--------------Edit supplier function starts here-----------------------*/
		
		public function edit_supplier_form()
		{
			$data['heading_title'] = 'Edit Supplier';
			//print_r($this->request->post);
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) 
			{
				$update_info = array(
				"supplier_group_id" => $this->request->post['supplier_group_id'],
				"supplier_group_name" => $this->request->post['supplier_group_name'],
				"first_name" => $this->request->post['firstname'],
				"last_name" => $this->request->post['lastname'],
				"email" => $this->request->post['email'],
				"telephone" => $this->request->post['telephone'],
				"fax" => $this->request->post['fax'],
				
				"ACC_ID"=>$this->request->post['account'],
				"ADDRESS"=>$this->request->post['bankaddress'],
				"BANK_NAME"=>$this->request->post['bank'],
				"IFSC_CODE"=>$this->request->post['ifsc'],
				"location"=>$this->request->post['location'],
				"pan"=>$this->request->post['pan'],
				"gst"=>$this->request->post['gst'] ,
				"status"=>$this->request->post['status'],
				"pre_mongified_id"=>(int)$this->request->post['supplier_id'],
				'delete_bit'=>0
			);
			
			
			$this->load->model('purchase/supplier');
			$updated = $this->model_purchase_supplier->update_supplier($update_info);
			//exit;
				$_SESSION['update_success_message'] = "Success!! Supplier updated successfully";
				$this->response->redirect($this->url->link('purchase/supplier', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			}
			else
			{
				if(isset($_SESSION['error_address'])||isset($_SESSION['error_account'])||isset($_SESSION['error_bank'])||isset($_SESSION['error_ifsc'])||isset($_SESSION['error_district'])||isset($_SESSION['error_firstname']) || isset($_SESSION['error_firstname']) || isset($_SESSION['error_firstname']) || isset($_SESSION['error_firstname']))
				{
				$data['supplier_info']['supplier_group_id'] = $this->request->post['supplier_group_id'];
				$data['supplier_info']['supplier_group_name'] = $this->request->post['supplier_group_name'];
				$data['supplier_info']['first_name'] = $this->request->post['firstname'];
				$data['supplier_info']['last_name'] = $this->request->post['lastname'];
				$data['supplier_info']['email'] = $this->request->post['email'];
				$data['supplier_info']['telephone'] = $this->request->post['telephone'];
				$data['supplier_info']['fax'] = $this->request->post['fax'];
				$data['supplier_info']['BANK_NAME'] = $this->request->post['bank'];
				$data['supplier_info']['ADDRESS'] = $this->request->post['bankaddress'];	
				$data['supplier_info']['IFSC_CODE'] = $this->request->post['ifsc'];
				$data['supplier_info']['ACC_ID']=$this->request->post['account'];
				$data['supplier_info']['location'] =$this->request->post['location'];
				$data['supplier_info']['bankaddress'] =$this->request->post['bankaddress'];
				
				$data['supplier_info']['gst']=$this->request->post['gst'];
				$data['supplier_info']['status']=$this->request->post['status'];
				$data['supplier_info']['pan']=$this->request->post['pan'];
				$supplier_id = $this->request->post['supplier_id'];
				}
				else
				{
					$supplier_id = $this->request->get['supplier_id'];
					$this->load->model('purchase/supplier');
					$data['supplier_info'] = $this->model_purchase_supplier->edit_supplier_form($supplier_id);

				}
				$data['supplier_id']=$supplier_id;
				$url = '';
				$data['breadcrumbs'] = array();

				$data['breadcrumbs'][] = array(
					'text' => "Home",
					'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
				);

				$data['breadcrumbs'][] = array(
					'text' => "Suppliers",
					'href' => $this->url->link('purchase/supplier', 'token=' . $this->session->data['token'] . $url, true)
				);
				$data['action'] = $this->url->link('purchase/supplier/edit_supplier_form', 'token=' . $this->session->data['token'] . $url, true);
				$data['cancel'] = $this->url->link('purchase/supplier', 'token=' . $this->session->data['token'] . $url, true);
				$data['header'] = $this->load->controller('common/header');
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['footer'] = $this->load->controller('common/footer');
				$this->load->model('purchase/supplier_group');
				$data['supplier_groups'] = $this->model_purchase_supplier_group->get_all_supplier_groups();
				$this->response->setOutput($this->load->view('purchase/supplier_edit_form.tpl', $data));
			
			}
			
		}
		/*------------------Delete supplier function starts here-------------*/
		
		public function delete_supplier()
		{
			$url = '';
			$supplier_ids = $this->request->post['selected'];
			$this->load->model('purchase/supplier');
			if(count($supplier_ids) == 0)
			{
				$this->response->redirect($this->url->link('purchase/supplier', 'token=' . $this->session->data['token'] . $url, true));
			}
			$deleted = $this->model_purchase_supplier->delete_supplier($supplier_ids);
			if($deleted)
			{
				$_SESSION['delete_success_message'] = "Success!! Supplier Successfully Deleted";
				$this->response->redirect($this->url->link('purchase/supplier', 'token=' . $this->session->data['token'] . $url, true));
			}
			else
			{
				$_SESSION['delete_unsuccess_message'] = "Warning: This supplier cannot be deleted as it is currently assigned to products! ";
				$this->response->redirect($this->url->link('purchase/supplier', 'token=' . $this->session->data['token'] . $url, true));
			}
		}
		
		/*------------------Delete supplier function ends here---------------*/
		
	}
?>