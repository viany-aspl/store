<?php
date_default_timezone_set('Asia/Kolkata');
class ControllerCcareIncommingcall extends Controller 
{
	private $error = array();

	public function index() 
	{
		$this->load->language('ccare/ccare');

		$this->document->setTitle('Incomming call');

		$this->load->model('ccare/incommingcall');
		
		$this->getList();
	}
    public function submit_incoming_call_data()
    {
        date_default_timezone_set("Asia/Kolkata");
        
        $mobile=$this->request->get['mobile_number'];
        $current_call_status=$this->request->get['current_call_status'];
        $call_status=$this->request->get['call_status'];
		$ticket_status=$this->request->get['ticket_status'];
		$customer_relation=$this->request->get['customer_relation'];
		$registered_mobile=$this->request->get['registered_mobile'];
        $first_name=$this->request->get['first_name'];
        $last_name=$this->request->get['last_name'];
		$State=$this->request->get['State'];
		$District=$this->request->get['District'];
        $village=$this->request->get['village'];
		$visit_required=$this->request->get['visit_required'];
        $logged_user_data=$this->request->get['logged_user_data'];
        $channel=$this->request->get['channel'];
        $query=$this->request->get['query'];
        $solution=$this->request->get['solution'];
        $transid=$this->request->get['transid'];
		$Categories=$this->request->get['Categories'];
		$Type=$this->request->get['Type'];
		
		$Category_name=$this->request->get['Category_name'];
		$Type_name=$this->request->get['Type_name'];
		
        $data = array(
				'mobile_number'	       => $mobile,
				'customer_mobile'=>$mobile,
				'to'          => (int)$call_status,
				'ticket_status'=>(int)$ticket_status,
				'customer_relation'=>$customer_relation,
				'registered_mobile'=>$registered_mobile,
				'first_name'    => $first_name,
				'last_name'     => $last_name,
				'State'=>$State,
				'District'=>$District,
				'village'              => $village,
				'visit_required'          => $visit_required,
                'from'  => (int)$current_call_status,
                'channel'  => $channel,
                'query'                => $query,
                'solution'             => $solution,
                'logged_user_data'     => $logged_user_data,
                'datetime'             => new MongoDate(strtotime(date('Y-m-d h:i:s'))),
                'transid'              => $transid,
                'trans_id'              => $transid,
				'Categories'              => $Categories,
				'Type'              => $Type,
				'Category_name'              => $Category_name,
				'Type_name'              => $Type_name,
				'current_ticket_status'=>1
			
			);
            
            $this->load->model('ccare/incommingcall');
           
            $result = $this->model_ccare_incommingcall->submit_incoming_call_data($data); 
            //exit;
			$this->load->library('sms'); 
			$sms=new sms($this->registry);
			$category_name=str_replace('&amp;','and',$category_name);
			if((($ticket_status==3) || ($ticket_status==4)) && ($call_status==27))
			{
				
				$databypos=array('name'=>$first_name.' '.$last_name ,'ticketid'=>$transid,'category_name'=>$Category_name);
				
				$sms->sendsms($mobile,"27",$databypos);
			}
			if(($ticket_status==2) && ($call_status==27))
			{
				$databypos=array('name'=>$first_name.' '.$last_name ,'ticketid'=>$transid,'category_name'=>$Category_name);
				
				$sms->sendsms($mobile,"28",$databypos);
			}
			//exit;
			$this->session->data['success']='Call Submit Successfully';
            header('location: '.$_SERVER["HTTP_REFERER"]);
        }
	public function submit_open_call_data()
	{
        date_default_timezone_set("Asia/Kolkata");
        
        //print_r($this->request->get);exit;
        $mobile=$this->request->get['mobile_number'];
        $current_call_status=$this->request->get['current_call_status'];
        $call_status=$this->request->get['call_status'];
		$ticket_status=$this->request->get['ticket_status'];
		$customer_relation=$this->request->get['customer_relation'];
		$registered_mobile=$this->request->get['registered_mobile'];
        $first_name=$this->request->get['first_name'];
        $last_name=$this->request->get['last_name'];
		$State=$this->request->get['State'];
		$District=$this->request->get['District'];
        $village=$this->request->get['village'];
		$visit_required=$this->request->get['visit_required'];
        $logged_user_data=$this->request->get['logged_user_data'];
        $channel=$this->request->get['channel'];
        $query=$this->request->get['query'];
        $solution=$this->request->get['solution'];
        $transid=$this->request->get['transid'];
		$current_ticket_status=$this->request->get['current_ticket_status'];
		$call_trans_id=$this->request->get['call_trans_id'];
		$Categories=$this->request->get['Categories'];
		$Type=$this->request->get['Type'];
		
		$Category_name=$this->request->get['Category_name'];
		$Type_name=$this->request->get['Type_name'];
        $data = array(
				'mobile_number'	       => $mobile,
				'customer_mobile'=>$mobile,
				'to'          => (int)$call_status,
				'ticket_status'=>(int)$ticket_status,
				'customer_relation'=>$customer_relation,
				'registered_mobile'=>$registered_mobile,
				'first_name'    => $first_name,
				'last_name'     => $last_name,
				'State'=>$State,
				'District'=>$District,
				'village'              => $village,
				'visit_required'          => $visit_required,
                'from'  => (int)$current_call_status,
                'channel'  => $channel,
                'query'                => $query,
                'solution'             => $solution,
                'logged_user_data'     => $logged_user_data,
                'datetime'             => new MongoDate(strtotime(date('Y-m-d h:i:s'))),
                'transid'              => $transid,
                'trans_id'              => $transid,
				'current_ticket_status'=>(int)$current_ticket_status,
				'call_trans_id'=>(int)$call_trans_id,
				'Categories'              => $Categories,
				'Type'              => $Type,
				'Category_name'              => $Category_name,
				'Type_name'              => $Type_name,
			
			);
            
            $this->load->model('ccare/incommingcall');
           
            $result = $this->model_ccare_incommingcall->submit_open_call_data($data); 
            //exit;
			$this->load->library('sms'); 
			$sms=new sms($this->registry);
			$category_name=str_replace('&amp;','and',$category_name);
			if((($ticket_status==3) || ($ticket_status==4)) && ($call_status==27))
			{
				$databypos=array('name'=>$first_name.' '.$last_name ,'ticketid'=>$transid,'category_name'=>$Category_name);
				
				$sms->sendsms($mobile,"27",$databypos);
			}
			if(($ticket_status==2) && ($call_status==27))
			{
				$databypos=array('name'=>$first_name.' '.$last_name ,'ticketid'=>$transid,'category_name'=>$Category_name);
				
				$sms->sendsms($mobile,"28",$databypos);
			}
			$this->session->data['success']='Call Submit Successfully';
            header('location: '.$_SERVER["HTTP_REFERER"]);
        }
		protected function getList() 
		{
			if (isset($this->request->get['filter_start_date'])) 
			{
				$data['filter_start_date'] =$filter_start_date = $this->request->get['filter_start_date'];
				$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
			} 
			else 
			{
				$filter_start_date = null;
			}

			if (isset($this->request->get['filter_end_date'])) 
			{
				$data['filter_end_date'] =$filter_end_date = $this->request->get['filter_end_date'];
				$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
			} 
			else 
			{
				$filter_end_date = null;
			}

			if (isset($this->request->get['filter_status'])) 
			{
				$data['filter_status'] =$filter_status = $this->request->get['filter_status'];
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			} 
			else 
			{
				$filter_status = null;
			}
			if (isset($this->request->get['filter_number'])) 
			{
				$data['filter_number'] =$filter_number = $this->request->get['filter_number'];
				$url .= '&filter_number=' . $this->request->get['filter_number'];
			} 
			else 
			{
				$filter_number = null;
			}
			if (isset($this->request->get['filter_order_id'])) 
			{
				$data['filter_order_id'] =$filter_order_id = $this->request->get['filter_order_id'];
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			} 
			else 
			{
				$filter_order_id = null;
			}

			if (isset($this->request->get['filter_customer'])) 
			{
				$data['filter_customer'] =$filter_customer = $this->request->get['filter_customer'];
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			} 
			else 
			{
				$filter_customer = null;
			}

			if (isset($this->request->get['filter_order_status'])) 
			{
				$data['filter_order_status'] =$filter_order_status = $this->request->get['filter_order_status'];
				$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
			} 
			else 
			{
				$filter_order_status = null;
			}

			if (isset($this->request->get['filter_total'])) 
			{
				$data['filter_total'] =$filter_total = $this->request->get['filter_total'];
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			} 
			else 
			{
				$filter_total = null;
			}

			if (isset($this->request->get['filter_date_added'])) 
			{
				$data['filter_date_added'] =$filter_date_added = $this->request->get['filter_date_added'];
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			} 
			else 
			{
				$filter_date_added = null;
			}

			if (isset($this->request->get['filter_date_modified'])) 
			{
				$data['filter_date_modified'] =$filter_date_modified = $this->request->get['filter_date_modified'];
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			} 
			else 
			{
				$filter_date_modified = null;
			}
			if (isset($this->request->get['page'])) 
			{
				$page = $this->request->get['page'];
				$url .= '&page=' . $this->request->get['page'];
			} 
			else 
			{
				$page = 1;
			}
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => 'Incomming Call',
				'href' => $this->url->link('ccare/incommingcall', 'token=' . $this->session->data['token'] . $url, 'SSL')
			);
            $this->load->model('user/user');
            $this->load->model('setting/store');
			$this->load->model('ccare/ccare');
            $data['logged_user_data'] = $this->user->getId();
            $data['current_order_status'] = '1';
			$data['group'] =$this->user->getGroupId();

			$data['orders'] = array();

			$filter_data = array(
				'filter_order_id'      => $filter_order_id,
				'filter_customer'	   => $filter_customer,
				'filter_order_status'  => $filter_order_status,
				'filter_total'         => $filter_total,
				'filter_date_added'    => $filter_date_added,
				'filter_date_modified' => $filter_date_modified,
				'filter_start_date'=>$filter_start_date,
				'filter_end_date'=>$filter_end_date,
				'filter_status'=>$filter_status,
				'filter_number'=>$filter_number,
			
				'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
				'limit'                => $this->config->get('config_limit_admin')
			);
			if($data['group']=="11")
			{
				$filter_data = array(
					'filter_user_id' => $this->user->getId(),
					'filter_order_id'      => $filter_order_id,
					'filter_customer'	   => $filter_customer,
					'filter_order_status'  => $filter_order_status,
					'filter_total'         => $filter_total,
					'filter_date_added'    => $filter_date_added,
					'filter_date_modified' => $filter_date_modified,
					'filter_start_date'=>$filter_start_date,
					'filter_end_date'=>$filter_end_date,
					'filter_status'=>$filter_status,
					'filter_number'=>$filter_number,
			
					'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
					'limit'                => $this->config->get('config_limit_admin')
				);


			}
			$ret_data = $this->model_ccare_incommingcall->getIncomingCall($filter_data);
			$results=$ret_data->rows;
			$order_total = $ret_data->num_rows; //$this->model_ccare_incommingcall->getAllIncomingCall($filter_data);
			foreach ($results as $result) 
			{ 
				$status = $this->model_ccare_incommingcall->getCallStatusByID($result['status']);
				//print_r($result['images'][0]);
				if($result['Type']=='Technical Issue')
				{
					$result['Type']='1';
				}
				if($result['Type']=='App Usage Knowledge')
				{
					$result['Type']='2';
				}
				if($result['Type']=='User Experience Feedback')
				{
					$result['Type']='3';
				}
				if($result['Type']=='Other')
				{
					$result['Type']='4';
				}
				$name=explode(' ',$result['name']);
				$data['orders'][] = array(
							'transid' => $result['transid'],
							'status' => $status,
                            'mobile'     => $result['mobile'],
                            'state_name'    => $result['state_name'],
							'channel'=>$result['channel'],
							'Categories'=>$result['Categories'],
							'Type'=>$result['Type'],
							'first_name'=>$name[0],
							'last_name'=>$name[1],
							'email'=>$result['email'],
							'status_id' => $result['status'],
							'query'=> trim($result['query']),
							'image_1'=> '././../image/'.trim($result['images'][0]),
							'image_2'=> '././../image/'.trim($result['images'][1]),
							'date_added'    => date($this->language->get('date_format_short'), ($result['timereceived']->sec))
				
							);
			}
            
			$data['heading_title'] = 'Incomming Call';
			$data["callstatus"] = $this->model_ccare_incommingcall->getCallStatus();
			$data["ticketsatus"] = $this->model_ccare_incommingcall->getTicketStatus();
			$data["Categories"] = $this->model_ccare_ccare->getCategories()->rows;
			$data["CallTypes"] = $this->model_ccare_ccare->getTypes()->rows;
			$data['token'] = $this->session->data['token'];

			if (isset($this->error['warning'])) 
			{
				$data['error_warning'] = $this->error['warning'];
			} 
			else 
			{
				$data['error_warning'] = '';
			}

			if (isset($this->session->data['success'])) 
			{
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} 
			else 
			{
				$data['success'] = '';
			}

		
			$pagination = new Pagination();
			$pagination->total = $order_total;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('ccare/incommingcall', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
			$this->load->language('common/information');
			$data['tool_tip']=$this->language->get('ccare/incommingcall/report');

			$data['tool_tip_style']=$this->language->get('tool_tip_style');
			$data['tool_tip_class']=$this->language->get('tool_tip_class');
			
		

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('ccare/incommingcall.tpl', $data));
		}
		public function open() 
		{
			$this->load->language('ccare/ccare');

			$this->document->setTitle('Open Tickets');

			$this->load->model('ccare/incommingcall');
			if (isset($this->request->get['filter_start_date'])) 
			{
				$data['filter_start_date'] =$filter_start_date = $this->request->get['filter_start_date'];
				$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
			} 
			else 
			{
				$filter_start_date = null;
			}

			if (isset($this->request->get['filter_end_date'])) 
			{
				$data['filter_end_date'] =$filter_end_date = $this->request->get['filter_end_date'];
				$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
			} 
			else 
			{
				$filter_end_date = null;
			}

			if (isset($this->request->get['filter_status'])) 
			{
				$data['filter_status'] =$filter_status = $this->request->get['filter_status'];
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			} 
			else 
			{
				$filter_status = null;
			}
			if (isset($this->request->get['filter_number'])) 
			{
				$data['filter_number'] =$filter_number = $this->request->get['filter_number'];
				$url .= '&filter_number=' . $this->request->get['filter_number'];
			} 
			else 
			{
				$filter_number = null;
			}
			if (isset($this->request->get['filter_order_id'])) 
			{
				$data['filter_order_id'] =$filter_order_id = $this->request->get['filter_order_id'];
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			} 
			else 
			{
				$filter_order_id = null;
			}

			if (isset($this->request->get['filter_customer'])) 
			{
				$data['filter_customer'] =$filter_customer = $this->request->get['filter_customer'];
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			} 
			else 
			{
				$filter_customer = null;
			}

			if (isset($this->request->get['filter_order_status'])) 
			{
				$data['filter_order_status'] =$filter_order_status = $this->request->get['filter_order_status'];
				$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
			} 
			else 
			{
				$filter_order_status = null;
			}

			if (isset($this->request->get['filter_total'])) 
			{
				$data['filter_total'] =$filter_total = $this->request->get['filter_total'];
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			} 
			else 
			{
				$filter_total = null;
			}

			if (isset($this->request->get['filter_date_added'])) 
			{
				$data['filter_date_added'] =$filter_date_added = $this->request->get['filter_date_added'];
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			} 
			else 
			{
				$filter_date_added = null;
			}

			if (isset($this->request->get['filter_date_modified'])) 
			{
				$data['filter_date_modified'] =$filter_date_modified = $this->request->get['filter_date_modified'];
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			} 
			else 
			{
				$filter_date_modified = null;
			}
			if (isset($this->request->get['page'])) 
			{
				$page = $this->request->get['page'];
				$url .= '&page=' . $this->request->get['page'];
			} 
			else 
			{
				$page = 1;
			}
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => 'Open Tickets',
				'href' => $this->url->link('ccare/incommingcall/open', 'token=' . $this->session->data['token'] . $url, 'SSL')
			);
            $this->load->model('user/user');
            $this->load->model('setting/store');
            $data['logged_user_data'] = $this->user->getId();
            $data['current_order_status'] = '1';
			$data['group'] =$this->user->getGroupId();

			$data['orders'] = array();

			$filter_data = array(
				
				'filter_start_date'=>$filter_start_date,
				'filter_end_date'=>$filter_end_date,
				
				'filter_number'=>$filter_number,
			
				'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
				'limit'                => $this->config->get('config_limit_admin')
			);
			$ret_data = $this->model_ccare_incommingcall->getOpenCall($filter_data);
			$results=$ret_data->rows;
			$order_total = $ret_data->num_rows; //$this->model_ccare_incommingcall->getAllIncomingCall($filter_data);
			foreach ($results as $result) 
			{ 
				$status = $this->model_ccare_incommingcall->getCallStatusByID($result['to']);
				$dataaa = $this->model_ccare_incommingcall->getDataByID($result['transid']);
				$ticket_status = $this->model_ccare_incommingcall->getticketStatusByID($result['ticket_status']);
				//print_r($result);
				
				$data['orders'][] = array(
							'transid' => $result['transid'],
							'ticket_status' => $ticket_status,
							'status' => $status,
                            'mobile'     => $result['mobile_number'],
                            'state_name'    => $result['State'],
							'channel'=>$result['channel'],
							'customer_relation'=>$result['customer_relation'],
							'first_name'=>$result['first_name'],
							'last_name'=>$result['last_name'],
							'State'=>$result['State'],
							'District'=>$result['District'],
							'village'=>$result['village'],
							'query'=>trim($result['query']),
							'solution'=>trim($result['solution']),
							'sid'=>$result['sid'],
							'visit_required'=>$result['visit_required'],
							'ticket_status_id' => $result['ticket_status'],
							'status_id' => $result['to'],
							'Categories' => $result['Categories'],
							'Type' => $result['Type'],
							'Category_name' => $result['Category_name'],
							'Type_name' => $result['Type_name'],
							'date_added'    => date($this->language->get('date_format_short'), ($result['datetime']->sec)),
							'image_1'=> '././../image/'.trim($dataaa['images'][0]),
							'image_2'=> '././../image/'.trim($dataaa['images'][1])
				
							);
			}
            
			$data['heading_title'] = 'Open Tickets';
			$this->load->model('ccare/ccare');
			$data["callstatus"] = $this->model_ccare_incommingcall->getCallStatus();
			$data["ticketsatus"] = $this->model_ccare_incommingcall->getTicketStatus();
			$data["Categories"] = $this->model_ccare_ccare->getCategories()->rows;
			$data["CallTypes"] = $this->model_ccare_ccare->getTypes()->rows;
			$data['token'] = $this->session->data['token'];

			if (isset($this->error['warning'])) 
			{
				$data['error_warning'] = $this->error['warning'];
			} 
			else 
			{
				$data['error_warning'] = '';
			}

			if (isset($this->session->data['success'])) 
			{
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} 
			else 
			{
				$data['success'] = '';
			}

		
			$pagination = new Pagination();
			$pagination->total = $order_total;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('ccare/incommingcall/open', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
			$this->load->language('common/information');
			$data['tool_tip']=$this->language->get('ccare/incommingcall/report');

			$data['tool_tip_style']=$this->language->get('tool_tip_style');
			$data['tool_tip_class']=$this->language->get('tool_tip_class');
			
		

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('ccare/opencall.tpl', $data));
		
		}
		public function resolved_closed() 
		{
			$this->load->language('ccare/ccare');

			$this->document->setTitle('Resolved/Closed Tickets');

			$this->load->model('ccare/incommingcall');
			if (isset($this->request->get['filter_start_date'])) 
			{
				$data['filter_start_date'] =$filter_start_date = $this->request->get['filter_start_date'];
				$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
			} 
			else 
			{
				$filter_start_date = null;
			}

			if (isset($this->request->get['filter_end_date'])) 
			{
				$data['filter_end_date'] =$filter_end_date = $this->request->get['filter_end_date'];
				$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
			} 
			else 
			{
				$filter_end_date = null;
			}

			if (isset($this->request->get['filter_status'])) 
			{
				$data['filter_status'] =$filter_status = $this->request->get['filter_status'];
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			} 
			else 
			{
				$filter_status = null;
			}
			if (isset($this->request->get['filter_number'])) 
			{
				$data['filter_number'] =$filter_number = $this->request->get['filter_number'];
				$url .= '&filter_number=' . $this->request->get['filter_number'];
			} 
			else 
			{
				$filter_number = null;
			}
			if (isset($this->request->get['filter_order_id'])) 
			{
				$data['filter_order_id'] =$filter_order_id = $this->request->get['filter_order_id'];
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			} 
			else 
			{
				$filter_order_id = null;
			}

			if (isset($this->request->get['filter_customer'])) 
			{
				$data['filter_customer'] =$filter_customer = $this->request->get['filter_customer'];
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			} 
			else 
			{
				$filter_customer = null;
			}

			if (isset($this->request->get['filter_order_status'])) 
			{
				$data['filter_order_status'] =$filter_order_status = $this->request->get['filter_order_status'];
				$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
			} 
			else 
			{
				$filter_order_status = null;
			}

			if (isset($this->request->get['filter_total'])) 
			{
				$data['filter_total'] =$filter_total = $this->request->get['filter_total'];
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			} 
			else 
			{
				$filter_total = null;
			}

			if (isset($this->request->get['filter_date_added'])) 
			{
				$data['filter_date_added'] =$filter_date_added = $this->request->get['filter_date_added'];
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			} 
			else 
			{
				$filter_date_added = null;
			}

			if (isset($this->request->get['filter_date_modified'])) 
			{
				$data['filter_date_modified'] =$filter_date_modified = $this->request->get['filter_date_modified'];
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			} 
			else 
			{
				$filter_date_modified = null;
			}
			if (isset($this->request->get['page'])) 
			{
				$page = $this->request->get['page'];
				$url .= '&page=' . $this->request->get['page'];
			} 
			else 
			{
				$page = 1;
			}
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => 'Resolved/Closed Tickets',
				'href' => $this->url->link('ccare/incommingcall/resolved_closed', 'token=' . $this->session->data['token'] . $url, 'SSL')
			);
            $this->load->model('user/user');
            $this->load->model('setting/store');
            $data['logged_user_data'] = $this->user->getId();
            $data['current_order_status'] = '1';
			$data['group'] =$this->user->getGroupId();

			$data['orders'] = array();

			$filter_data = array(
				
				'filter_start_date'=>$filter_start_date,
				'filter_end_date'=>$filter_end_date,
				
				'filter_number'=>$filter_number,
			
				'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
				'limit'                => $this->config->get('config_limit_admin')
			);
			$ret_data = $this->model_ccare_incommingcall->getresolved_closedCall($filter_data);
			$results=$ret_data->rows;
			$order_total = $ret_data->num_rows; //$this->model_ccare_incommingcall->getAllIncomingCall($filter_data);
			foreach ($results as $result) 
			{ 
				$status = $this->model_ccare_incommingcall->getCallStatusByID($result['to']);
				$ticket_status = $this->model_ccare_incommingcall->getticketStatusByID($result['ticket_status']);
				//print_r($result);
				$dataaa = $this->model_ccare_incommingcall->getDataByID($result['transid']);
				$data['orders'][] = array(
							'transid' => $result['transid'],
							'ticket_status' => $ticket_status,
							'status' => $status,
                            'mobile'     => $result['mobile_number'],
                            'state_name'    => $result['State'],
							'channel'=>$result['channel'],
							'customer_relation'=>$result['customer_relation'],
							'first_name'=>$result['first_name'],
							'last_name'=>$result['last_name'],
							'State'=>$result['State'],
							'District'=>$result['District'],
							'village'=>$result['village'],
							'query'=>$result['query'],
							'solution'=>$result['solution'],
							'sid'=>$result['sid'],
							'visit_required'=>$result['visit_required'],
							'ticket_status_id' => $result['ticket_status'],
							'status_id' => $result['to'],
							'Categories' => $result['Categories'],
							'Type' => $result['Type'],
							'Category_name' => $result['Category_name'],
							'Type_name' => $result['Type_name'],
							'date_added'    => date($this->language->get('date_format_short'), ($result['datetime']->sec)),
							'image_1'=> '././../image/'.trim($dataaa['images'][0]),
							'image_2'=> '././../image/'.trim($dataaa['images'][1])
							);
			}
            
			$data['heading_title'] = 'Resolved/Closed Tickets';
			$this->load->model('ccare/ccare');
			$data["callstatus"] = $this->model_ccare_incommingcall->getCallStatus();
			$data["ticketsatus"] = $this->model_ccare_incommingcall->getTicketStatus();
			$data["Categories"] = $this->model_ccare_ccare->getCategories()->rows;
			$data["CallTypes"] = $this->model_ccare_ccare->getTypes()->rows;
			$data['token'] = $this->session->data['token'];

			if (isset($this->error['warning'])) 
			{
				$data['error_warning'] = $this->error['warning'];
			} 
			else 
			{
				$data['error_warning'] = '';
			}

			if (isset($this->session->data['success'])) 
			{
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} 
			else 
			{
				$data['success'] = '';
			}

		
			$pagination = new Pagination();
			$pagination->total = $order_total;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('ccare/incommingcall/resolved_closed', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
			$this->load->language('common/information');
			$data['tool_tip']=$this->language->get('ccare/incommingcall/report');

			$data['tool_tip_style']=$this->language->get('tool_tip_style');
			$data['tool_tip_class']=$this->language->get('tool_tip_class');
			
		

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('ccare/resolved_closed_call.tpl', $data));
		
		}
		public function resolved_closed_download() 
		{
			$this->load->model('ccare/incommingcall');
			if (isset($this->request->get['filter_start_date'])) 
			{
				$data['filter_start_date'] =$filter_start_date = $this->request->get['filter_start_date'];
				$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
			} 
			else 
			{
				$filter_start_date = null;
			}

			if (isset($this->request->get['filter_end_date'])) 
			{
				$data['filter_end_date'] =$filter_end_date = $this->request->get['filter_end_date'];
				$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
			} 
			else 
			{
				$filter_end_date = null;
			}

			if (isset($this->request->get['filter_status'])) 
			{
				$data['filter_status'] =$filter_status = $this->request->get['filter_status'];
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			} 
			else 
			{
				$filter_status = null;
			}
			if (isset($this->request->get['filter_number'])) 
			{
				$data['filter_number'] =$filter_number = $this->request->get['filter_number'];
				$url .= '&filter_number=' . $this->request->get['filter_number'];
			} 
			else 
			{
				$filter_number = null;
			}
			if (isset($this->request->get['filter_order_id'])) 
			{
				$data['filter_order_id'] =$filter_order_id = $this->request->get['filter_order_id'];
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			} 
			else 
			{
				$filter_order_id = null;
			}

			if (isset($this->request->get['filter_customer'])) 
			{
				$data['filter_customer'] =$filter_customer = $this->request->get['filter_customer'];
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			} 
			else 
			{
				$filter_customer = null;
			}

			if (isset($this->request->get['filter_order_status'])) 
			{
				$data['filter_order_status'] =$filter_order_status = $this->request->get['filter_order_status'];
				$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
			} 
			else 
			{
				$filter_order_status = null;
			}

			if (isset($this->request->get['filter_total'])) 
			{
				$data['filter_total'] =$filter_total = $this->request->get['filter_total'];
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			} 
			else 
			{
				$filter_total = null;
			}

			if (isset($this->request->get['filter_date_added'])) 
			{
				$data['filter_date_added'] =$filter_date_added = $this->request->get['filter_date_added'];
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			} 
			else 
			{
				$filter_date_added = null;
			}

			if (isset($this->request->get['filter_date_modified'])) 
			{
				$data['filter_date_modified'] =$filter_date_modified = $this->request->get['filter_date_modified'];
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			} 
			else 
			{
				$filter_date_modified = null;
			}
			if (isset($this->request->get['page'])) 
			{
				$page = $this->request->get['page'];
				$url .= '&page=' . $this->request->get['page'];
			} 
			else 
			{
				$page = 1;
			}
			
            $this->load->model('user/user');
            $this->load->model('setting/store');
            $data['logged_user_data'] = $this->user->getId();
            
			$filter_data = array(
				
				'filter_start_date'=>$filter_start_date,
				'filter_end_date'=>$filter_end_date,
				
				'filter_number'=>$filter_number,
			
				'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
				'limit'                => $this->config->get('config_limit_admin')
			);
			$file_name="Sale_to_retailer_".date('dMy').'.csv';
			
			$fields = array(
				'Ticket ID',
       			'Number',
				'Register Date',
				'Channel',
        		'Ticket Status',
       			'Call Status',
				'Customer Relation ',
				'Is SE visit Required ',
				'Category',
				'Type',
				'First Name',
				'Last Name',
				'State',
				'District',
				'Village',
				'Query',
				'Solution'
				
    		);
			
            $fileIO = fopen('php://memory', 'w+');
			fputcsv($fileIO, $fields,';');
			
			$ret_data = $this->model_ccare_incommingcall->getresolved_closedCall($filter_data);
			$results=$ret_data->rows;
			$order_total = $ret_data->num_rows; //$this->model_ccare_incommingcall->getAllIncomingCall($filter_data);
			foreach ($results as $result) 
			{ 
				$status = $this->model_ccare_incommingcall->getCallStatusByID($result['to']);
				$ticket_status = $this->model_ccare_incommingcall->getticketStatusByID($result['ticket_status']);
				
				$fdata=array(
                            $result['transid'],
                            $result['mobile_number'],
                            date($this->language->get('date_format_short'), ($result['datetime']->sec)),
                            $result['channel'],
                            $ticket_status,
                            $status,
                            $result['customer_relation'],
                            $result['visit_required'],
                            $result['Category_name'],
                            $result['Type_name'],
                            $result['first_name'],
							$result['last_name'],
                            $result['State'],
							$result['District'],
                            $result['village'],
							$result['query'],
                            $result['solution']
                            );
				fputcsv($fileIO,  $fdata,";");
			}
            
			fseek($fileIO, 0);
             
    		header('Content-Type: application/csv');
    		header('Content-Disposition: attachment;filename="Resolved_Closed_Tickets_'.date('dMy').'.xls"');
    		header('Cache-Control: max-age=0');
            fpassthru($fileIO);  
            fclose($fileIO);
		}
		
		public function getretailer_info()
		{
			$this->load->model('ccare/incommingcall');

			if (isset($this->request->get['mobile'])) 
			{
				$mobile = $this->request->get['mobile'];
			} 
			else 
			{
				$mobile = 0;
			}
			if(!empty($mobile))
			{
				$getretailer_info=$this->model_ccare_incommingcall->getretailer_info(array('mobile'=>$mobile));
				print_r(json_encode($getretailer_info));
			}
			
		}
        public function get_order_info()
        {
            
            $this->load->model('ccare/ccare');

			if (isset($this->request->get['order_id'])) 
			{
				$order_id = $this->request->get['order_id'];
			} 
			else 
			{
				$order_id = 0;
			}

			$order_info = $this->model_ccare_ccare->getOrder($order_id);

			if ($order_info) 
			{
			
				$data['order_id'] = $this->request->get['order_id'];

				if ($order_info['invoice_no']) 
				{
					$data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
				} 
				else 
				{
					$data['invoice_no'] = '';
				}
				$data['store_name'] = $order_info['store_name'];
				$data['store_url'] = $order_info['store_url'];
				$data['firstname'] = $order_info['firstname'];
				$data['lastname'] = $order_info['lastname'];

				$data['email'] = $order_info['email'];
				$data['telephone'] = $order_info['telephone'];
				$data['fax'] = $order_info['fax'];
				$data['comment'] = nl2br($order_info['comment']);
				$data['shipping_method'] = $order_info['shipping_method'];
				$data['payment_method'] = $order_info['payment_method'];
				$data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);

				$this->load->model('sale/customer');

				$this->load->model('localisation/order_status');

				$order_status_info = $this->model_localisation_order_status->getOrderStatus($order_info['order_status_id']);

				if ($order_status_info) 
				{
					$data['order_status'] = $order_status_info['name'];
				} 
				else 
				{
					$data['order_status'] = '';
				}


				$data['products'] = array();

				$products = $order_info['order_product'];//$this->model_ccare_ccare->getOrderProducts($this->request->get['order_id']);

				foreach ($products as $product) 
				{
					$data['products'][] = array(
						'order_product_id' => $product['order_product_id'],
						'product_id'       => $product['product_id'],
						'name'    	 	   => $product['name'],
						'model'    		   => $product['model'],
						'option'   		   => $option_data,
						'quantity'		   => $product['quantity'],
						'price'    		   => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
						'total'    		   => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
						
					);
				}
				$totals = $order_info['order_total'];//$this->model_ccare_ccare->getOrderTotals($this->request->get['order_id']);

				foreach ($totals as $total) 
				{ 
					$data['totals'][] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
						);
				}

				$data['order_status_id'] = $order_info['order_status_id'];
                $data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
				$data['date_modified'] = date($this->language->get('date_format_short'), strtotime($order_info['date_modified']));
                if($order_info['date_potential']=="0000-00-00 00:00:00")
				{
					$date_potential_temp="00/00/0000";
				}
				else
				{
					$date_potential_temp=date($this->language->get('date_format_short'), strtotime($order_info['date_potential']));
				}
                $order_info_return='<div class="tab-pane active" id="tab-order">
					<table class="table table-bordered">
						<tr>
							<td>Order ID:</td>
							<td>'.$order_id.'</td>
						</tr>
						<tr>
							<td>Store Name:</td>
							<td>'.$data['store_name'].'</td>
						</tr>
						<tr>
							<td>Customer:</td>
							<td>'.$data['firstname'].' '.$data['lastname'].'</td>
						</tr>
						<tr>
							<td>Customer Group:</td>
							<td>'.$data['customer_group'].'</td>
						</tr>
						<tr>
							<td>E-Mail:</td>
							<td>'.$data['email'].'</td>
						</tr>
						<tr>
							<td>Telephone:</td>
							<td>'.$data['telephone'].'</td>
						</tr>
						<tr>
							<td>Fax:</td>
							<td>'.$data['fax'].'</td>
						</tr>
						<tr>
							<td>Total:</td>
							<td>'.$data['total'].'</td>
						</tr>
						<tr>
							<td>Order Status:</td>
							<td id="order-status">'.$data['order_status'].'</td>
						</tr>
						<tr>
							<td>Date Added:</td>
							<td>'.$data['date_added'].'</td>
						</tr>
						<tr>
							<td>Date Potential:</td>
							<td>'.$date_potential_temp.'</td>
						</tr>
					</table>
				</div>';
           
				$product_info_return_1='<div class="tab-pane" id="tab-product">
				<table class="table table-bordered">
					<thead>              
						<tr>
							<td class="text-left">Store Name</td>
							<td class="text-right">Product Name </td>
							<td class="text-right">Quantity</td>
							<td class="text-right">Price</td> 
							<td class="text-right">Amount</td>
						</tr>
					</thead>
				<tbody>';
				//print_r($products);
				$prod="";
				foreach ($products as $product) 
				{
					$prod.='<tr><td class="text-left">'.$product["store_name"].'</td><td class="text-left">'.$product["product_name"].'</td><td class="text-right">'.$product["qnty"].'</td> <td class="text-right">'.$product["price"].'</td><td class="text-right">'.$product["price"].'</td></tr>';
				} 
				$prod2="";
				$prod3="";    
				foreach ($data['totals'] as $total2) 
				{ 
					$prod3.='<tr>
						<td colspan="4" class="text-right">'.$total2["title"].':</td>
						<td class="text-right">'.$total2["text"].'</td>
					</tr>';
				} 
                
				$product_info_return_2='</tbody>
					</table>
				</div>';
				$product_info_return=$product_info_return_1.$prod.$prod2.$prod3.$product_info_return_2;
                  
				$abc = $order_info_return.'----and----'.$product_info_return.'----and----'.$order_info['call_status'];          
				echo $abc;
              
				//echo $order_info_return.'----and----'.$product_info_return.'----and----'.$order_info['call_status'];           
			}
            
        }
        public function productinfo()
		{
			$product_id=$this->request->get['product_id'];
			$filter_store=$this->request->get['filter_store'];
			$this->load->model("ccare/incommingcall");
			$filter_data=array(
				'filter_name_id'=> $product_id,
				'filter_store'=> $filter_store,
				'start'=>0,
				'limit'=>100

			);

			$inventory = $this->model_ccare_incommingcall->getInventory_reportProductWise($filter_data);

			$tble='<table class="table table-bordered">
					<thead>
						<tr>
							<td class="text-left">SI ID </td>
							<td class="text-left">Store Name </td>
							<td class="text-right">Product Name </td>
							<td class="text-right">Qnty </td>
							<td class="text-right">Price</td>

						</tr>
					</thead><tbody>';

				if ($inventory)
				{
					$aa=1;
					foreach ($inventory as $order) 
					{
						$tble.='<tr>
									<td class="text-left">'.$aa.'</td>
									<td class="text-left">'.$order["store_name"].'</td>
									<td class="text-right">'.$order["Product_name"].'</td>
									<td class="text-right">'.$order["Qnty"].'</td>
									<td class="text-right">'.round($order["price"]).'</td>
								</tr>';

						$aa++;
					}
				} 
				else 
				{
					$tble.='<tr><td>This product is not available anywhere</td></tr>';
				}
			$tble.='</tbody></table>';
			echo $tble;
			exit;
		}
       

}