<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
error_reporting(0);

class ControllerPosTotalCredit extends Controller {
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
		public function email()  
        {
			$mcrypt=new MCrypt();
			$fields_string='';
			$strid=$this->user->getStoreId();
			$username=$this->user->getId();
			$log=new Log("cust-getcust-".date('Y-m-d').".log");

			$log->write($strid);
			$log->write($username);
			
			$fields_string .= 'store_id'.'='.$mcrypt->encrypt($strid).'&'; 
			
			$fields_string .= 'username'.'='.$mcrypt->encrypt($username).'&';
			$fields_string .= 'action'.'='.$mcrypt->encrypt('e').'&'; 
			$fields_string .= 'type'.'='.$mcrypt->encrypt('credit').'&'; 
			$request = HTTP_CATALOG."index.php?route=mpos/customer/getcustomer";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $request);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_USERAGENT,'akshamaala');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
            curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string); 
			$json =curl_exec($ch);
			if(empty($json))
            {
                   
			}
			curl_close($ch); 
			//print_r($json);
			$return_val=json_decode($json,TRUE);
			$this->load->library('email');
			$email=new email($this->registry);
			$email->getstoreemail($strid);
			echo 'Email sent successfully to '.$email->getstoreemail($strid);
		}
	public function index() 
	{
		$this->load->language('report/Inventory_report');
		$this->load->language('common/information');
		$data['tool_tip']=$this->language->get('inventory/report');

		$data['tool_tip_style']=$this->language->get('tool_tip_style');
		$data['tool_tip_class']=$this->language->get('tool_tip_class');

		$this->document->setTitle($this->language->get('heading_credit'));

		$filter_store = $this->user->getStoreId();
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
               
                
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		if (isset($this->request->get['pagetittle'])) 
		{
			$url .= '&pagetittle=' . $this->request->get['pagetittle'];
		}
		$this->adminmodel('pos/pos');
		$data['orders'] = array();

		$filter_data = array(
			'filter_store' => $filter_store,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

	        
                $data['customers']= $this->model_pos_pos->getCustomers((int)$filter_store,''); 
                //print_r($data['customers']);exit;
                $data['total_customers']= sizeof($data['customers']);
		$data['token'] = $this->session->data['token'];
                
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('pos/total_credit', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('default/template/pos/total_credit.tpl', $data));
	}


        
          public function searchCustomer()
		  {
             // echo "done";                            
              if (isset($this->request->get['filter_telephone'])) {
                  
			 $this->load->model('pos/pos');
                            echo $this->request->get['filter_telephone'];
			if (isset($this->request->get['filter_telephone'])) {
				$filter_telephone= $this->request->get['filter_telephone'];
			} else {
				$filter_telephone= '';
			}

			
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_telephone'  => $filter_telephone,
				
				'start'        => 0,
				'limit'        => $limit
			);
         
			$results = $this->model_pos_pos->searchCustomer($filter_data);
                  
			foreach ($results as $result) {
				//print_r($result);
				$json[] = array(
					'customer_id' => $result['customer_id'],
					'name'       => strip_tags(html_entity_decode($result['firstname'], ENT_QUOTES, 'UTF-8')),
					
				);
                                
                                //print_r($json);
			}
                       
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
        
        public function pay_for_credit() 
        {
            
            $this->load->language('report/Inventory_report');
            
            $url='';
            if(!empty($this->request->get['mobile_number']))
            {
                $data['mobile_number']=$this->request->get['mobile_number'];
                $url.='&mobile_number='.$this->request->get['mobile_number'];
            }
            else if(!empty($this->request->post['mobile_number']))
            {
                $data['mobile_number']=$this->request->post['mobile_number'];
                $url.='&mobile_number='.$this->request->post['mobile_number'];
            }
            else 
            {
                $data['mobile_number']='';
            }
            
            if(!empty($this->request->get['aadhar']))
            {
                $data['aadhar']=$this->request->get['aadhar'];
                $url.='&aadhar='.$this->request->get['aadhar'];
            }
            else if(!empty($this->request->post['aadhar']))
            {
                $data['aadhar']=$this->request->post['aadhar'];
                $url.='&aadhar='.$this->request->post['aadhar'];
            }
            else 
            {
                $data['aadhar']='';
            }
            if(!empty($this->request->get['name']))
            {
                $data['name']=$this->request->get['name'];
                $url.='&name='.$this->request->get['name'];
            }
            else if(!empty($this->request->post['name']))
            {
                $data['name']=$this->request->post['name'];
                $url.='&name='.$this->request->post['name'];
            }
            else 
            {
                $data['name']='';
            }
            if(!empty($this->request->get['cash']))
            {
                $data['cash']=$this->request->get['cash'];
                $url.='&cash='.$this->request->get['cash'];
            }
            else if(!empty($this->request->post['cash']))
            {
                $data['cash']=$this->request->post['cash'];
                $url.='&cash='.$this->request->post['cash'];
            }
            else 
            {
                $data['cash']='';
            }
            
            $this->adminmodel('pos/pos');
            if ($this->request->server['REQUEST_METHOD'] == 'POST')
            {
                $mobile_number=$this->request->post['mobile_number'];
                $customer_info=$this->model_pos_pos->getCustomerByPhone($mobile_number);
                if(!empty(($customer_info['customer_id'])))
                {
					if (isset($this->request->get['pagetittle'])) 
					{
						$url .= '&pagetittle=' . $this->request->get['pagetittle'];
					}
                    if(!empty($this->request->post['cash']))
                    {
                        if(is_numeric($this->request->post['cash']))
                        {
                            $customer_id=$customer_info['customer_id'];
                            $sid=$this->user->getStoreId();
                            $cash_amount=$this->request->post['cash'];
                            $result=$this->model_pos_pos->updatecustomercash($customer_id,$cash_amount,$sid); 
                            $this->session->data['success'] = 'Accepted Successfully';
                            $this->response->redirect($this->url->link('pos/total_credit/pay_for_credit', 'token=' . $this->session->data['token'].'&pagetittle=Pay for Credit' , 'SSL'));
                        }
                        else 
                        {
                            $this->session->data['error'] = 'Cash must be Numeric';
                            $this->response->redirect($this->url->link('pos/total_credit/pay_for_credit', 'token=' . $this->session->data['token'].'&pagetittle=Pay for Credit' . $url, 'SSL'));
                        }
                    }
                    else 
                    {
                        $this->session->data['error'] = 'Please Enter the Cash!';
                        $this->response->redirect($this->url->link('pos/total_credit/pay_for_credit', 'token=' . $this->session->data['token'].'&pagetittle=Pay for Credit'  . $url, 'SSL'));
                    }
                }
                else 
                {
                    $this->session->data['error'] = 'Customer details not found ! ';
                    $this->response->redirect($this->url->link('pos/total_credit/pay_for_credit', 'token=' . $this->session->data['token'] .'&pagetittle=Pay for Credit'.  $url, 'SSL'));
                
                }
            }
            if (isset($this->session->data['error'])) 
            {
		$data['error_warning'] = $this->session->data['error'];
                unset($this->session->data['error']);
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
            
            $data['token'] = $this->session->data['token'];
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');   
            $this->response->setOutput($this->load->view('default/template/pos/pay_for_credit.tpl',$data));   
        }
        
	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_telephone'])) {
			$this->adminmodel('pos/pos');
			

			if (isset($this->request->get['filter_telephone'])) {
				$filter_telephone = $this->request->get['filter_telephone'];
			} else {
				$filter_telephone = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_telephone'  => $filter_telephone,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_pos_pos->searchCustomer($filter_telephone,5,$this->user->getStoreId());

			foreach ($results as $result) 
                        {
				$json[] = array(
					'customer_id' => $result['customer_id'],
					'name'       => strip_tags(html_entity_decode($result['firstname']." ".$result['lastname'], ENT_QUOTES, 'UTF-8')),
					'telephone'      => $result['telephone'],
					'aadhar'      => $result['aadhar'],
					'credit'      => $result['credit']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}