<?php
class ControllerUserMenu extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/storemenu');

		$this->document->setTitle('User Menu ');

		$this->load->model('catalog/storemenu');

		$this->getList();
	}
        public function groupmenu() {
		$this->load->language('catalog/storemenu');

		$this->document->setTitle('User Group Menu ');

		$this->load->model('catalog/storemenu');

		$this->getListforGroup();
	}
        public function updatepermission()
        {
            
            $user_id=$this->request->get['user_id'];
            $parent_id=$this->request->get['category_id'];
            $this->load->model('catalog/storemenu');
            //print_r($this->request->post['selected']);
            $update_data=$this->model_catalog_storemenu->updatepermission($user_id,$parent_id,$this->request->get['selected']);
            echo json_encode(1);
            exit;
            
        }
        public function updategrouppermission()
        {
            
            $user_group_id=$this->request->get['user_group_id'];
            $parent_id=$this->request->get['category_id'];
            $this->load->model('catalog/storemenu');
            //print_r($this->request->post['selected']);
            $update_data=$this->model_catalog_storemenu->updategrouppermission($user_group_id,$parent_id,$this->request->get['selected']);
            echo json_encode(1);
            exit;
            
        }
	protected function getList() {
		if (isset($this->request->get['user_id'])) {
			$data['user_id'] = $this->request->get['user_id'];
		} else {
			$data['user_id'] = '';
		}
                if (isset($this->request->get['name'])) {
			$data['name'] = $this->request->get['name'];
		} else {
			$data['name'] = '';
		}
                if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		}
                else
                {
                  $page='1';  
                }
		$url = '';

		if (isset($this->request->get['user_id'])) {
			$url .= '&user_id=' . $this->request->get['user_id'];
		}
                if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		if (isset($this->request->get['name'])) {
			$url .= '&name=' . $this->request->get['name'];
		}
		$data['breadcrumbs'] = array();
                $data['token']=$this->session->data['token'];
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'User Menu ',
			'href' => $this->url->link('user/menu', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		
		$data['categories'] = array();

		$filter_data = array(
			
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
                $returndata=$this->model_catalog_storemenu->getAllActiveParent($filter_data);
                //print_r($returndata);
		$category_total =$returndata->num_rows;//$this->model_catalog_storemenu->getTotalCategories();

		$results = $returndata->rows;
                $data['access']=$this->model_catalog_storemenu->getusermenu(array('user_id'=>$data['user_id'],'menutype'=>2));
                //print_r($data['access']);
		foreach ($results as $result) {
			$child_data=$this->model_catalog_storemenu->getAllActiveChild($result['category_id']);
			
                        $cname=$result['name'];
			$data['categories'][] = array(
				'category_id' => $result['category_id'],
				'name'        => $cname,
				'child'  => $child_data,
				'edit'        => $this->url->link('catalog/storemenu/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'] . $url, 'SSL'),
				'delete'      => $this->url->link('catalog/storemenu/delete', 'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'] . $url, 'SSL')
			);
		}
                $data['cancel'] = $this->url->link('user/user', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['heading_title'] = 'User Menu ';

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_rebuild'] = $this->language->get('button_rebuild');

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


		$pagination = new Pagination();
		$pagination->total = $category_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('user/menu', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('user/menu_list.tpl', $data));
	}
        protected function getListforGroup() {
		if (isset($this->request->get['user_group_id'])) {
			$data['user_group_id'] = $this->request->get['user_group_id'];
		} else {
			$data['user_group_id'] = '';
		}
                if (isset($this->request->get['name'])) {
			$data['name'] = $this->request->get['name'];
		} else {
			$data['name'] = '';
		}
                if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		}
                else
                {
                  $page='1';  
                }
		$url = '';

		if (isset($this->request->get['user_group_id'])) {
			$url .= '&user_group_id=' . $this->request->get['user_group_id'];
		}
                if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		if (isset($this->request->get['name'])) {
			$url .= '&name=' . $this->request->get['name'];
		}
		$data['breadcrumbs'] = array();
                $data['token']=$this->session->data['token'];
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'User Group Menu ',
			'href' => $this->url->link('user/menu/grouplist', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		
		$data['categories'] = array();

		$filter_data = array(
			
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
                $returndata=$this->model_catalog_storemenu->getAllActiveParent($filter_data);
                //print_r($returndata);
		$category_total =$returndata->num_rows;//$this->model_catalog_storemenu->getTotalCategories();

		$results = $returndata->rows;
                $data['access']=$this->model_catalog_storemenu->getgroupmenu(array('user_group_id'=>$data['user_group_id'],'menutype'=>2));
                //print_r($data['access']);
		foreach ($results as $result) {
			$child_data=$this->model_catalog_storemenu->getAllActiveChild($result['category_id']);
			
                        $cname=$result['name'];
			$data['categories'][] = array(
				'category_id' => $result['category_id'],
				'name'        => $cname,
				'child'  => $child_data,
				'edit'        => $this->url->link('catalog/storemenu/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'] . $url, 'SSL'),
				'delete'      => $this->url->link('catalog/storemenu/delete', 'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'] . $url, 'SSL')
			);
		}
                $data['cancel'] = $this->url->link('user/user_permission', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['heading_title'] = 'User Group Menu ';

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_rebuild'] = $this->language->get('button_rebuild');

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


		$pagination = new Pagination();
		$pagination->total = $category_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('user/menu/grouplist', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('user/group_menu_list.tpl', $data));
	}
        

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/storemenu');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_catalog_storemenu->getParent($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}