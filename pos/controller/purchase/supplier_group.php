<?php
	class ControllerPurchaseSupplierGroup extends Controller 
	{
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
			$url = '';
			$data['add'] = $this->url->link('purchase/supplier_group/add_supplier_group', 'token=' . $this->session->data['token'] . $url, true);
			$data['delete'] = $this->url->link('purchase/supplier_group/delete_supplier_group', 'token=' . $this->session->data['token'] . $url, true);
			$data['edit'] = $this->url->link('purchase/supplier_group/supplier_group_edit_form', 'token=' . $this->session->data['token'] . $url, true);
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => "Home",
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => "Supplier Groups",
				'href' => $this->url->link('purchase/supplier_group', 'token=' . $this->session->data['token'] . $url, true)
			);
			$this->adminmodel('purchase/supplier_group');
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}
			$start = ($page - 1) * 20;
			$limit = 20;
			$filter_data=array(
			'start'=>$start,
			'limit'=>$limit
			);
			$supplier_groups = $this->model_purchase_supplier_group->get_supplier_groups($filter_data);
			$data['supplier_groups'] = $supplier_groups->rows;
			
			$total_suppliers_group = $supplier_groups->num_rows;
			
			$pagination = new Pagination();
			$pagination->total = $total_suppliers_group;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('purchase/supplier_group', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);
			
			$data['pagination'] = $pagination->render();
			
			$data['results'] = sprintf($this->language->get('text_pagination'), ($total_suppliers_group) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_suppliers_group - $this->config->get('config_limit_admin'))) ? $total_suppliers_group : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_suppliers_group, ceil($total_suppliers_group / $this->config->get('config_limit_admin')));

			
			/*pagination*/
			
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$this->response->setOutput($this->load->view('default/template/purchase/supplier_group_list.tpl',$data));
		}
		/*---------------------Add supplier function starts here--------------*/
		
		public function add_supplier_group()
		{
			$url = '';
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => "Home",
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => "Supplier Groups",
				'href' => $this->url->link('purchase/supplier_group', 'token=' . $this->session->data['token'] . $url, true)
			);
			$data['action'] = $this->url->link('purchase/supplier_group/insert_supplier_group', 'token=' . $this->session->data['token'] . $url, true);
			$data['cancel'] = $this->url->link('purchase/supplier_group', 'token=' . $this->session->data['token'] . $url, true);
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$this->response->setOutput($this->load->view('default/template/purchase/supplier_group_form.tpl', $data));
		}
		
		/*---------------------Add supplier function ends here--------------*/
		
		public function insert_supplier_group()
		{
			$url = '';
			$data['supplier_group_name'] = $_POST['sgname'];
			$data['supplier_group_desc'] = $_POST['sgdescription']; 
			if ((utf8_strlen($_POST['sgname']) < 3) || (utf8_strlen($_POST['sgname']) > 32)) {
				$_SESSION['name_error'] = "Name is required!length should between 3 to 32";
				$this->response->redirect($this->url->link('purchase/supplier_group/add_supplier_group', 'token=' . $this->session->data['token'] . $url, true));
			}
			else
			{
				$this->adminmodel('purchase/supplier_group');
				$inserted = $this->model_purchase_supplier_group->insert_supplier_group($data);
				if($inserted)
				{
					$_SESSION['success_message'] = "Success: New supplier group added! ";
					$this->response->redirect($this->url->link('purchase/supplier_group', 'token=' . $this->session->data['token'] . $url, true));
				}
				else
				{
					$_SESSION['unsuccess_message'] = "Something went woring,try again";
					$this->response->redirect($this->url->link('purchase/supplier_group/add_supplier_group', 'token=' . $this->session->data['token'] . $url, true));
				}
			}
		}
		
		
		/*----------------------supplier_group_edit_form function starts here-------------------*/
		
		public function supplier_group_edit_form()
		{
			$url = '';
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => "Home",
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => "Suppliers",
				'href' => $this->url->link('purchase/supplier_group', 'token=' . $this->session->data['token'] . $url, true)
			);
			$data['action'] = $this->url->link('purchase/supplier_group/update_supplier_group', 'token=' . $this->session->data['token'] . $url, true);
			$data['cancel'] = $this->url->link('purchase/supplier_group', 'token=' . $this->session->data['token'] . $url, true);
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$supplier_group_id = $this->request->get['supplier_group_id'];
			$this->adminmodel('purchase/supplier_group');
			$data['supplier_group_info'] = $this->model_purchase_supplier_group->supplier_group_edit_form($supplier_group_id);
			$this->response->setOutput($this->load->view('default/template/purchase/supplier_group_edit_form.tpl', $data));
		}
		
		/*----------------------supplier_group_edit_form function starts here-------------------*/
		
		/*----------------------update supplier group function starts here-------------*/
		
		public function update_supplier_group()
		{
			$url = '';
			$update_info = array(
				'supplier_group_id' => $this->request->post['supplier_group_id'],
				'supplier_group_name' => $this->request->post['sgname'],
				'supplier_group_desc' => $this->request->post['sgdescription']
			);
			
			$this->adminmodel('purchase/supplier_group');
			$updated = $this->model_purchase_supplier_group->update_supplier_group($update_info);
			if($updated)
			{
				$_SESSION['update_success_message'] = "Success!! Supplier group updated successfully";
				$this->response->redirect($this->url->link('purchase/supplier_group', 'token=' . $this->session->data['token'] . $url, true));
			}
			else
			{
				$url .= '&supplier_group_id=' . $this->request->post['supplier_group_id'];
				$_SESSION['update_unsuccess_message'] = "Sorry!! Something went wrong, try again";
				$this->response->redirect($this->url->link('purchase/supplier/edit_supplier_form', 'token=' . $this->session->data['token'] . $url, true));
			}
		}
		
		/*----------------------update supplier group function ends here-------------------*/
	}
?>