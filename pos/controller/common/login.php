<?php
class ControllerCommonLogin extends Controller {
	private $error = array();
	public function adminmodel($model) 
    {
        $admin_dir = DIR_SYSTEM;
        $admin_dir = str_replace('system/','backoffice/',$admin_dir);
        $file = $admin_dir . 'model/' . $model . '.php';      
        $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);      
        if (file_exists($file)) {
	         include_once($file);         
        	 $this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
        } else {
         trigger_error('Error: Could not load model ' . $model . '!');
         exit();               
        }
    }

	public function index() 
	{
		$this->load->language('common/login');
                //$this->user->isLogged();
                //print_r($this->request->post);
		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->user->isLogged() && isset($this->request->get['token']) && ($this->request->get['token'] == $this->session->data['token'])) {
			$this->response->redirect($this->url->link('pos/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) 
		{
			$this->session->data['token'] = md5(mt_rand());

			if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], HTTP_SERVER) === 0 || strpos($this->request->post['redirect'], HTTPS_SERVER) === 0 )) 
			{
				$this->response->redirect($this->request->post['redirect'] . '&token=' . $this->session->data['token']);
			} 
			else 
			{
				$this->response->redirect($this->url->link('pos/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
			}
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_login'] = $this->language->get('text_login');
		$data['text_forgotten'] = $this->language->get('text_forgotten');

		$data['entry_username'] = $this->language->get('entry_username');
		$data['entry_password'] = $this->language->get('entry_password');

		$data['button_login'] = $this->language->get('button_login');

		if ((isset($this->session->data['token']) && !isset($this->request->get['token'])) || ((isset($this->request->get['token']) && (isset($this->session->data['token']) && ($this->request->get['token'] != $this->session->data['token']))))) {
			$this->error['warning'] = $this->language->get('error_token');
		}

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

		$data['action'] = $this->url->link('common/login', '', 'SSL');

		if (isset($this->request->post['username'])) {
			$data['username'] = $this->request->post['username'];
		} else {
			$data['username'] = '';
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->get['route'])) {
			$route = $this->request->get['route'];

			unset($this->request->get['route']);
			unset($this->request->get['token']);

			$url = '';

			if ($this->request->get) {
				$url .= http_build_query($this->request->get);
			}

			$data['redirect'] = $this->url->link($route, $url, 'SSL');
		} else {
			$data['redirect'] = '';
		}

		if ($this->config->get('config_password')) {
			$data['forgotten'] = $this->url->link('common/forgotten', '', 'SSL');
		} else {
			$data['forgotten'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('default/template/common/home.tpl', $data));

		/*if((isset($_GET['route']) && strpos($_GET['route'],'pos')!== false) || isset($_POST['is_pos'])){
                    $this->load->model('user/user');
                    $user_group_id = $this->config->get('pos_user_group_id');
                    $data['users'] = $this->model_user_user->getUsersByGroupId($user_group_id);
                
                    $this->response->setOutput($this->load->view('pos/login.tpl', $data));
                }else{
                    $this->response->setOutput($this->load->view('common/login.tpl', $data));
                }*/

		

	}

	protected function validate() 
	{
		if (!isset($this->request->post['username']) || !isset($this->request->post['password']) || !$this->user->login($this->request->post['username'], $this->request->post['password'],11)) {
			$this->error['warning'] = $this->language->get('error_login');
		}

		return !$this->error;
	}
	public function qrlogin()
	{
		$log=new Log("qr-login-".date('Y-m-d').".log");
		$this->adminmodel('user/user');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->qrvalidate()) 
		{
			
			
			$this->session->data['token'] = $this->request->post['token'];

			if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], HTTP_SERVER) === 0 || strpos($this->request->post['redirect'], HTTPS_SERVER) === 0 )) 
			{
				$this->response->redirect($this->request->post['redirect'] . '&token=' . $this->session->data['token']);
			} 
			else 
			{
				$log->write($this->session->data['token']);
				$log->write($this->session->data['user_id']);
				
				$this->model_user_user->update_qr_login_status($this->session->data['user_id'],$this->session->data['token'],1);
				
				//$this->response->redirect($this->url->link('pos/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
				$log->write($this->url->link('pos/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
				$urll=str_replace('&amp;','&',$this->url->link('pos/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
				echo $urll;
				
			}
		}
		else
		{
			echo $this->request->server['REQUEST_METHOD'];
			echo '';
			$this->model_user_user->update_qr_login_status($this->session->data['user_id'],$this->session->data['token'],2);
		}
	}
	protected function qrvalidate() 
	{
		$mcrypt=new MCrypt();
		$users=$this->request->post['users'];
		$users2=explode('-',$users);
		
		$this->request->post['username']=$mcrypt->decrypt($users2[0]);
		$this->request->post['store_id']=$mcrypt->decrypt($users2[1]);
		
		if (!isset($this->request->post['username']) || !isset($this->request->post['store_id']) || !$this->user->qrlogin($this->request->post['username'], $this->request->post['store_id'],'')) 
		{
			$this->error['warning'] = $this->language->get('error_login');
		}

		return !$this->error;
	}

	public function validate_ajax() {
                    if (!isset($this->request->post['username']) || !isset($this->request->post['password']) || !$this->user->login($this->request->post['username'], $this->request->post['password'])) {
                       $json['error'] = 'Error: Invalid username or password.';
                    }else{
                       $this->session->data['token'] = md5(mt_rand());                   
                       $json['success'] = 'Success';
                       $json['token'] = $this->session->data['token'];                                      
                    }
                    echo json_encode($json);
                }

	public function check() {
		$route = '';

		if (isset($this->request->get['route'])) {
			$part = explode('/', $this->request->get['route']);

			if (isset($part[0])) {
				$route .= $part[0];
			}

			if (isset($part[1])) {
				$route .= '/' . $part[1];
			}
		}

		$ignore = array(
			'common/login',
			'common/forgotten',
			'common/reset'
		);

		if (!$this->user->isLogged() && !in_array($route, $ignore)) {
			return new Action('common/login');
		}

		if (isset($this->request->get['route'])) {
			$ignore = array(
				'common/login',
				'common/logout',
				'common/forgotten',
				'common/reset',
				'error/not_found',
				'error/permission'
			);

			$config_ignore = array();

			if ($this->config->get('config_token_ignore')) {
				$config_ignore = unserialize($this->config->get('config_token_ignore'));
			}

			$ignore = array_merge($ignore, $config_ignore);

			if (!in_array($route, $ignore) && (!isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token']))) {
				return new Action('common/login');
			}
		} else {
			if (!isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
				return new Action('common/login');
			}
		}
	}
}