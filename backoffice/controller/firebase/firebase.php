<?php
class ControllerFirebaseFirebase extends Controller 
{
    private $error = array();
	public function getlist() 
	{
		$this->load->language('report/Inventory_report');
		$this->load->language('common/information');
		$data['tool_tip']=$this->language->get('inventory/report');

		$data['tool_tip_style']=$this->language->get('tool_tip_style');
		$data['tool_tip_class']=$this->language->get('tool_tip_class');

		$this->document->setTitle('Notifications');

		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = '';
		}
                
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
                
                if ($this->request->get['filter_store']!="") {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}
                
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'Notifications',
			'href' => $this->url->link('firebase/firebase/getlist', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		$this->load->model('firebase/firebase');
		$data['orders'] = array();

		$filter_data = array(
			
			'filter_store' => $filter_store,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		

		$data['orders'] = array();

		$query = $this->model_firebase_firebase->getnotificationsforWeb($filter_data);
		//print_r($query->num_rows);
		$results= $query->rows;
		$order_total= $query->num_rows;

		foreach ($results as $result) 
		{ //print_r($result);
            $data['orders'][] = array(
                            'title' => $result['title'],
							'message' => $result['message'],
                            'image_url'      => $result['image_url'],
                            'publishedDate'      => $result['publishedDate'],
							'name'   => strtoupper($result['name']),
							'delete'=>$this->url->link('firebase/firebase/deleterow', 'token=' . $this->session->data['token'].$url.'&_id='. $result['_id'], 'SSL'),
							'resend'=>$this->url->link('firebase/firebase/resend', 'token=' . $this->session->data['token'].$url.'&title='. $result['title'].'&message='. $result['message'].'&image_url='. $result['image_url'].'&name='. $result['name'].'&deviceId='. $result['deviceId'].'&userID='. $result['userID'], 'SSL')
                            );
		}

		$data['heading_title'] = 'Notifications';
		
		$data['text_list'] = 'Notifications';
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_all_status'] = $this->language->get('text_all_status');

		$data['column_date_start'] = $this->language->get('column_date_start');
		$data['column_date_end'] = $this->language->get('column_date_end');
		$data['column_title'] = $this->language->get('column_title');
		$data['column_orders'] = $this->language->get('column_orders');
		$data['column_total'] = $this->language->get('column_total');

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_group'] = $this->language->get('entry_group');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('firebase/firebase/getlist', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
		$data['add'] = $this->url->link('firebase/firebase', 'token=' . $this->session->data['token'].$url, 'SSL');
		$data['filter_store'] = $filter_store;
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('firebase/getlist.tpl', $data));
	}
	public function deleterow()
	{
		$url = '';
        if ($this->request->get['filter_store']!="") 
		{
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}
                
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		if (isset($this->request->get['page'])) 
		{
			$_id=  $this->request->get['_id'];
		}
		if(!empty($_id))
		{
			$this->model_firebase_firebase->deleterow($_id);
		}
		$this->session->data['success'] = 'Notification deleted Successfully'; 
		$this->response->redirect($this->url->link('firebase/firebase/getlist', 'token=' . $this->session->data['token'] . $url, 'SSL'));
	}
        public function index()
        {
			$this->load->model('firebase/firebase');
             if ($this->request->server['REQUEST_METHOD'] == 'POST')
             { 
                
                $response=$this->send_notification();
                $this->session->data['response'] = $response;
                $temp_res=json_decode($response);
                if(!$temp_res->success)
                {
                    $error=($temp_res->results[0]->error);
                    $this->session->data['error'] = $error;
                }
                else
                {
                    $this->session->data['success'] = 'Notification Created Successfully'; 
                }
                $this->response->redirect($this->url->link('firebase/firebase', 'token=' . $this->session->data['token'] . $url, 'SSL'));
                //exit;
             }
            
            $this->getForm();
        }
	public function getForm() 
        {
		$this->document->setTitle('Notification');

		$data['heading_title'] = 'Send Notification';
		$this->load->model('user/user');
		$this->load->model('firebase/firebase');
		$logged_user_data = $this->user->getId();
                $data['logged_user'] = $logged_user_data;
		
		$data['token'] = $this->session->data['token'];

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'News',
			'href' => $this->url->link('firabase/firebase', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['cancel'] = $this->url->link('firebase/firebase/getlist', 'token=' . $this->session->data['token'].$url, 'SSL');

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}
                if (isset($this->session->data['json'])) {
			$data['json'] = $this->session->data['json'];

			unset($this->session->data['json']);
		} else {
			$data['json'] = '';
		}
                if (isset($this->session->data['response'])) {
			$data['response'] = $this->session->data['response'];

			unset($this->session->data['response']);
		} else {
			$data['response'] = '';
		}
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['users']=$this->model_firebase_firebase->getstoreusers();
		//print_r($data['users'][0]);
		$this->response->setOutput($this->load->view('firebase/firebase.tpl', $data));
	}
        private function send_notification()
        {
            $this->load->library('firebase/firebase');
            $this->load->library('firebase/push');
            $firebase = new Firebase();
            $push = new Push();

            // optional payload
            $payload = array();
            //$payload['team'] = 'India';
            //$payload['score'] = '5.6';

            // notification title
            $title = isset($this->request->post['title']) ? $this->request->post['title'] : '';
        
            // notification message
            $message = isset($this->request->post['message']) ? $this->request->post['message'] : '';
        
            // push type - single user / topic
            $push_type = isset($this->request->post['push_type']) ? $this->request->post['push_type'] : '';

		            // push type - single user / topic
            $valid_date = isset($this->request->post['valid_date']) ? $this->request->post['valid_date'] : '';
        
            $push->setTitle($title);
            $push->setMessage($message);
            $image_url=$this->getFile();
            $push->setImage($image_url);
            
            $push->setIsBackground(FALSE);
            $push->setPayload($payload);

            $json = '';
            $response = '';
            $deviceId=$this->getdeviceId();
			
			if(empty($deviceId))
			{
				$push_type='topic';
			}
			if ($push_type == 'topic')
            {
				$this->load->model('user/user');
				$filter_data = array(
					'filter_name'  => '',
					'filter_user_group_id'=>11,
					'start'        => 0,
					'limit'        => 100
				);

				$results = $this->model_user_user->getUsers($filter_data);

				foreach ($results as $result) 
				{
					$deviceId = $result['token'];
					$json = $push->getPush();
					$jsonnotification = $push->getPushNotification();
					$response = $firebase->send($deviceId, $json ,$jsonnotification);
					//$response = $firebase->send_to_multiple($deviceId, $json);
					
				}
				//$deviceId=json_encode($deviceId);
				$userID='';
				$username='';
				$name='ALL';
				$email='';
				$store_id='';
				$this->model_firebase_firebase->submit_data($title,$message,$image_url,'ALL',$response,$userID,$username,$name,$email,$store_id,$valid_date);
			}
			//echo $push_type;
			
            
            else if ($push_type == 'individual') 
            {
				$json = $push->getPush();
				$jsonnotification = $push->getPushNotification();
				/*
                
                $regId = $deviceId;
                $response = $firebase->send($regId, $json ,$jsonnotification);
				
				$getuserDetails=$this->getuserDetails();
				$userID=$getuserDetails['user_id'];
				$username=$getuserDetails['username'];
				$name=$getuserDetails['firstname'].' '.$getuserDetails['lastname'];
				$email=$getuserDetails['email'];
				$store_id=$getuserDetails['store_id'];
				$this->model_firebase_firebase->submit_data($title,$message,$image_url,$deviceId,$response,$userID,$username,$name,$email,$store_id,$valid_date);
				*/
				
				foreach ($deviceId as $deviceId2) 
				{
					$deviceId21=explode('----',$deviceId2);
					
					$regId = $deviceId21[0];
					$getuserDetails=$this->getuserDetails($deviceId21[0]);
					//print_r($getuserDetails);
					//exit;
					$response = $firebase->send($regId, $json ,$jsonnotification);
				
					
					$userID=$getuserDetails['user_id'];
					$username=$getuserDetails['username'];
					$name=$getuserDetails['firstname'].' '.$getuserDetails['lastname'];
					$email=$getuserDetails['email'];
					$store_id=$getuserDetails['store_id'];
					$this->model_firebase_firebase->submit_data($title,$message,$image_url,$deviceId2,$response,$userID,$username,$name,$email,$store_id,$valid_date);
				
					
				}
				
				
            }
            
            $this->session->data['json'] = $json;
            return $response;
        }
        private function getdeviceId()
        {
            return $this->request->post['users_id'];
        }
		private function getuserDetails($token)
        {
			$this->load->model('firebase/firebase');
			//echo $users_id;
            $getuserDetails=$this->model_firebase_firebase->getuserDetails($token)->row;
			return $getuserDetails;
        }
        private function getFile()
        {
            $default_file='https://unnatiagro.in/stores/image/cache/no_image-45x45.png';
            $path = DIR_UPLOAD."/notification/"; 
            $file_extensions= array('jpeg','jpg','png','gif');
            $file_name = @$_FILES['include_image']['name'];
            $file_size =@$_FILES['include_image']['size'];
            $file_tmp =@$_FILES['include_image']['tmp_name'];
            $file_type=@$_FILES['include_image']['type'];
            $arrrr=explode('.',$file_name); 
            $exttt=end($arrrr);
            $file_ext= strtolower($exttt);
            if($file_name!="")
            {
                if(in_array($file_ext, $file_extensions)) 
                { 
                    if(!is_writable($path))
                    {
                      return $default_file; 
                    }
                    $new_file_name=date('Ymd')."_".date('his').".".$file_ext;
                    $file_path=$path.$new_file_name;
                    $move= move_uploaded_file($file_tmp,$file_path);
                    if($move)
                    {
                        return HTTPS_CATALOG.'system/upload/notification/'.$new_file_name;//exit;
                    }
                    else ///////if some error in upload the file
                    {
                        return $default_file; 
                    }
                }
                else ///////if file extensions is not matched
                {
                    return $default_file; 
                }
            }///////// if file name is not empty end here
 	    else////////data is submit but no file chossen
	    { 
                return $default_file;    
	    }
        }
        
        public function autocomplete() {
		$json = array();

		if (isset($this->request->get['users'])) {
			$this->load->model('user/user');
			
			if (isset($this->request->get['users'])) {
				$filter_name = $this->request->get['users'];
			} else {
				$filter_name = '';
			}

			
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_user_group_id'=>'',
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_user_user->getUsers($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'user_id' => $result['token'],
					'name'       => strtoupper($result['firstname'])
					
				);
				}

				
			
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}