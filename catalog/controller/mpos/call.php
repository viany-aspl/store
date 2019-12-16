<?php
class Controllermposcall extends Controller
{
	public function adminmodel($model) 
	{
		$admin_dir = DIR_SYSTEM;
		$admin_dir = str_replace('system/','backoffice/',$admin_dir);
		$file = $admin_dir . 'model/' . $model . '.php';      
		//$file  = DIR_APPLICATION . 'model/' . $model . '.php';
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
	public function insertfeedback() 
	{
		
		$mcrypt=new MCrypt();
        $log=new Log("call-".date('Y-m-d').".log");
		$log->write('insertfeedback called');
		
		if (!empty($mcrypt->decrypt($this->request->post['mobile']))) 
		{
			$mobile = $mcrypt->decrypt($this->request->post['mobile']); 		
		}
        else if (!empty($mcrypt->decrypt($this->request->get['mobile']))) 
		{
			$mobile = $mcrypt->decrypt($this->request->get['mobile']); 		
		}
        else 
		{
			$mobile = '';
		}
		
		if (!empty($mcrypt->decrypt($this->request->post['store_id'])) )
		{
			$store_id = $mcrypt->decrypt($this->request->post['store_id']); 		
		}
        else if (!empty($mcrypt->decrypt($this->request->get['store_id'])) )
		{
			$store_id = $mcrypt->decrypt($this->request->get['store_id']); 		
		}
        else 
		{
			$store_id = '';
		}
		if (!empty($mcrypt->decrypt($this->request->post['transid'])) )
		{
			$transid = $mcrypt->decrypt($this->request->post['transid']); 		
		}
        else if (!empty($mcrypt->decrypt($this->request->get['transid'])) )
		{
			$transid = $mcrypt->decrypt($this->request->get['transid']); 		
		}
        else 
		{
			$transid = '';
		}
		if (!empty($mcrypt->decrypt($this->request->post['feedbackcount'])) )
		{
			$feedbackcount = $mcrypt->decrypt($this->request->post['feedbackcount']); 		
		}
        else if (!empty($mcrypt->decrypt($this->request->get['feedbackcount']))) 
		{
			$feedbackcount = $mcrypt->decrypt($this->request->get['feedbackcount']); 		
		}
        else 
		{
			$feedbackcount = '';
		}
		if (!empty($mcrypt->decrypt($this->request->post['feedback'])) )
		{
			$feedback = $mcrypt->decrypt($this->request->post['feedback']); 		
		}
        else if (!empty($mcrypt->decrypt($this->request->get['feedback']))) 
		{
			$feedback = $mcrypt->decrypt($this->request->get['feedback']); 		
		}
        else 
		{
			$feedback = '';
		}
        $log->write($this->request->post);
		$log->write($this->request->get);

        $this->adminmodel('ccare/ccare');
		$log->write($mobile);
		
        $results = $this->model_ccare_ccare->insertfeedback(array('mobile'=>$mobile,'transid'=>$transid,'store_id'=>$store_id,'feedbackcount'=>$feedbackcount,'feedback'=>$feedback));
        $log->write($results);
        
		$json=array('status'=>1,'msg'=>'Submitted successfully');
		return $this->response->setOutput(json_encode($json)); 
	}
	public function getCount() 
    {
		$log=new Log("call-".date('Y-m-d').".log");
		$log->write('getCount called');
		$log->write($this->request->post);
		$mcrypt=new MCrypt();
		$keys = array(
		'store_id',
		'mobile_number',
		'page'
        );
		foreach ($keys as $key) 		
		{
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
		}
        //$this->request->post['mobile_number']=8447882446;
		$page=$this->request->post['page'];
		if(empty($page))
		{
            $page=1;
		}
		$filter_data=array(
			'store_id'=>$this->request->post['store_id'],
			'mobile_number'=>$this->request->post['mobile_number']
            );
		$log->write($filter_data);
		$this->adminmodel('ccare/ccare');
		/////////////////
        $order_data=$this->model_ccare_ccare->getCount($filter_data); 
		
        $data['orders'] = $order_data->rows;
		$total_order = $order_data->num_rows;
		
		$log->write($total_order);
		$log->write('final output');
		$json['total']=$mcrypt->encrypt($total_order);
		$log->write($json);
		return $this->response->setOutput(json_encode($json));
		
    }
	public function getList() 
    {
		$log=new Log("call-".date('Y-m-d').".log");
		$log->write('getList called');
		$log->write($this->request->post);
		$mcrypt=new MCrypt();
		$keys = array(
		'store_id',
		'mobile_number',
		'page'
        );
		foreach ($keys as $key) 		
		{
          	  $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
		}
		$log->write($this->request->post);
        //$this->request->post['mobile_number']=8447882446;
		$page=$this->request->post['page'];
		if(empty($page))
		{
            		$page=1;
		}
		$filter_data=array(
			'store_id'=>$this->request->post['store_id'],
			'mobile_number'=>$this->request->post['mobile_number']
            	);
		$log->write($filter_data);
		$this->adminmodel('ccare/ccare');
		/////////////////
        	$order_data=$this->model_ccare_ccare->getList($filter_data);
		
        	$data['orders'] = $order_data->rows;
		$total_order = $total_order+$order_data->num_rows;
		//print_r($data['orders']);
		$json=array();
		$resolved_open_ticket=array();
		foreach($data['orders'] as $order)
		{
			$category_name=$this->model_ccare_ccare->category_name($order['Categories'])->row['name'];
			$type_name=$this->model_ccare_ccare->type_name($order['Type'])->row['name'];
			//echo 'here';
			$ticket_status_name=$this->model_ccare_ccare->ticket_status_name($order['ticket_status'])->row['STATUS_NAME'];
			$open_time=date('Y-m-d h:i:s',$this->model_ccare_ccare->get_ticket_open_time($order['transid'])->row['timereceived']->sec);			
			$log->write('solution');
			$log->write($order['solution']);
			
			if(empty($order['solution']))
			{
				$order['solution']='Solution Awaited';
			}
			if(($order['solution'])=='0')
			{
				$order['solution']='Solution Awaited';
			}
            		$products_r[] = array(
				'transid' => ($order['transid']),
				'ttype' => ('in open or closed'),
				'id' => $mcrypt->encrypt($order['transid']),
				'category_id' => $mcrypt->encrypt($order['Categories']),
				'category_name' => $mcrypt->encrypt(str_replace('&amp;','&',$category_name)),
				'transaction_type' => $mcrypt->encrypt(str_replace('&amp;','&',$type_name)),
				'ticket_status' => $mcrypt->encrypt($order['ticket_status']),
				'ticket_status_name' => $mcrypt->encrypt($ticket_status_name),
				'enddate' => $mcrypt->encrypt(date('Y-m-d h:i:s',$order['datetime']->sec)),
				'startdate' => $mcrypt->encrypt($open_time),
				'question'  	=> $mcrypt->encrypt($order['query']),
				'answer'  	=> $mcrypt->encrypt($order['solution']),
				'feedbackcount'  	=> $mcrypt->encrypt($order['feedbackcount'])
				);
			$resolved_open_ticket[]=(int)$order['transid'];
		}
		/////////////
		$filter_data['resolved_open_ticket']=$resolved_open_ticket;
		$s_data=$this->model_ccare_ccare->getListIncomming($filter_data);
		
        $data['s_data_orders'] = $s_data->rows;
		$total_order = $s_data->num_rows;
		
		$resolved_open_ticket=array();
		foreach($data['s_data_orders'] as $order)
		{
			//print_r($order);
			$ticket_status_name=$this->model_ccare_ccare->ticket_status_name(1)->row['STATUS_NAME'];
			//$open_time=date('Y-m-d h:i:s',$this->model_ccare_ccare->get_ticket_open_time($order['transid'])->row['timereceived']->sec);	
			if(empty($order['solution']))
			{
				$order['solution']='Solution Awaited';
			}
			if(($order['solution'])=='0')
			{
				$order['solution']='Solution Awaited';
			}
            $products_s[] = array(
				'transid' => ($order['transid']),
				'ttype' => ('justsubmit'),
				'id' => $mcrypt->encrypt($order['transid']),
				'category_id' => $mcrypt->encrypt($order['Categories']),
				'category_name' => $mcrypt->encrypt(str_replace('&amp;','&',$order['category_name'])),
				'transaction_type' => $mcrypt->encrypt(str_replace('&amp;','&',$order['Type'])),
				'ticket_status' => $mcrypt->encrypt(1),
				'ticket_status_name' => $mcrypt->encrypt($ticket_status_name),
				'enddate' => $mcrypt->encrypt('NA'),//date('Y-m-d h:i:s',$order['timereceived']->sec)),
				'startdate' => $mcrypt->encrypt(date('Y-m-d h:i:s',$order['timereceived']->sec)),
				'question'  	=> $mcrypt->encrypt($order['query']),
				'answer'  	=> $mcrypt->encrypt($order['solution']),
				'feedbackcount'  	=> $mcrypt->encrypt($order['feedbackcount'])
				);
			
		}
		if(!empty($products_s) && (!empty($products_r)))
		{
				$json['products']=array_merge($products_s,$products_r);
		}
		else if(!empty($products_s) && (empty($products_r)))
		{
				$json['products']=$products_s;
		}
		else if(empty($products_s) && (!empty($products_r)))
		{
				$json['products']=$products_r;
		}	
		$log->write($json);
		$log->write('final output');
		$json['total']=$mcrypt->encrypt($total_order);
		$log->write($json);
		return $this->response->setOutput(json_encode($json));
		
    }
	public function getCategories() 
    {
		$log=new Log("call-".date('Y-m-d').".log");
		$log->write('getCategories called');
		$log->write($this->request->post);
		$mcrypt=new MCrypt();
		$keys = array(
		'store_id',
		'page'
        );
		foreach ($keys as $key) 		
		{
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
		}
        
		$page=$this->request->post['page'];
		if(empty($page))
		{
            $page=1;
		}
		$filter_data=array(
			'store_id'=>$this->request->post['store_id']
            );
		$log->write($filter_data);
		$this->adminmodel('ccare/ccare');
        $order_data=$this->model_ccare_ccare->getCategories($filter_data);

        $data['orders'] = $order_data->rows;
		$total_order = $order_data->num_rows;
		
		$json=array();
		foreach($data['orders'] as $order)
		{
            $json['crops'][] = array(
				'name' => $mcrypt->encrypt(str_replace('&amp;','&',$order['name'])),
				'value'  	=> $mcrypt->encrypt($order['id'])
				);
		}
		
		$log->write($json);
		$log->write('final output');
		$json['total']=$mcrypt->encrypt($total_order);
		$log->write($json);
		return $this->response->setOutput(json_encode($json));
		
    }
	public function getTypes() 
    {
		$log=new Log("call-".date('Y-m-d').".log");
		$log->write('getTypes called');
		$log->write($this->request->post);
		$mcrypt=new MCrypt();
		$keys = array(
		'store_id',
		'page'
        );
		foreach ($keys as $key) 		
		{
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
		}
        
		$page=$this->request->post['page'];
		if(empty($page))
		{
            $page=1;
		}
		$filter_data=array(
			'store_id'=>$this->request->post['store_id']
            );
		$log->write($filter_data);
		$this->adminmodel('ccare/ccare');
        $order_data=$this->model_ccare_ccare->getTypes($filter_data);
		
        $data['orders'] = $order_data->rows;
		$total_order = $order_data->num_rows;
		
		$json=array();
		foreach($data['orders'] as $order)
		{
            $json['crops'][] = array(
				'name' => $mcrypt->encrypt(str_replace('&amp;','&',$order['name'])),
				'value'  	=> $mcrypt->encrypt(str_replace('&amp;','&',$order['value']))
				);
		}
		
		$log->write($json);
		$log->write('final output');
		$json['total']=$mcrypt->encrypt($total_order);
		$log->write($json);
		return $this->response->setOutput(json_encode($json));
		
    }
	public function insertincomming() 
	{
		$mcrypt=new MCrypt();
        $log=new Log("call-".date('Y-m-d').".log");
		if (isset($this->request->post['mobile'])) 
		{
			$mobile = $this->request->post['mobile']; 		
		}
        else if (isset($this->request->get['mobile'])) 
		{
			$mobile = $this->request->get['mobile']; 		
		}
        else 
		{
			$mobile = '';
		}
        $log->write($this->request->post);
		$log->write($this->request->get);

        $this->adminmodel('ccare/ccare');
		$log->write($mobile);
		$results="";
        if($mobile!="")
        {
            $this->adminmodel('sale/customer');       
            $customer_info = $this->model_sale_customer->getCustomerByEmailCall($mobile); 
	        $log->write($customer_info);
            if (empty($customer_info))
            {
                $this->request->post['email']=$mobile;
				$this->request->post['fax']=$mobile;
				$this->request->post['password']=$mobile;
				$this->request->post['customer_group_id']="1";
				$this->request->post['newsletter']='0';        
				$this->request->post['approved']='1';
				$this->request->post['status']='1';
				$this->request->post['safe']='1';
				$this->request->post['address_1']= '';
				$this->request->post['address_2']= '';
				$this->request->post['city']= '';
				$this->request->post['company']='Unnati';
				$this->request->post['country_id']='0';
				$this->request->post['zone_id']='0';
				$this->request->post['postcode']='0';
				$this->request->post['store_id']=0;             
				$this->request->post['address']='';
				$customer_info=$this->model_sale_customer->addCustomer_by_call($this->request->post); 
                $log->write($customer_info);
            }
			$results = $this->model_ccare_ccare->insertincomming($mobile,$customer_info);
            $ret=1;
        }
        else
        {
			$ret=0;
        }
		/*try
		{
			$this->load->library('sms');
			$sms=new sms($this->registry);

			$this->adminmodel('marketing/coupon');
			$all_active_coupons=$this->model_marketing_coupon->getCouponsForMissedCall(); 
			$log->write($all_active_coupons);
			$random_keys=array_rand($all_active_coupons,1);
			$log->write($random_keys);
			$coupon=$all_active_coupons[$random_keys]['code'];
			$log->write($all_active_coupons[$random_keys]);
			$log->write($coupon);
			//$coupon="";
			$coupon_type=$all_active_coupons[$random_keys]['type'];
			if($coupon_type=="P")
			{
				$coupon_discount=number_format((float)$all_active_coupons[$random_keys]['discount'], 2, '.', '')."%";
			}
			else
			{
				$coupon_discount="FLAT Rs. ".number_format((float)$all_active_coupons[$random_keys]['discount'], 2, '.', '');
			}

			if($coupon!="")
			{
				$customer_info=array('coupon'=>$coupon,'coupon_discount'=>$coupon_discount);
				$sms->sendsms($mobile,"12",$customer_info);
			}
			else
			{
                $sms->sendsms($mobile,"9",$customer_info);
			}
			
		} 
		catch (Exception $e) 
		{
			$log->write($e);
		}*/
		$category_name=str_replace('&amp;','and',$category_name);
		$databypos=array('name'=>$customer_info['firstname'],'ticketid'=>$results,'category_name'=>$category_name);
		$this->load->library('sms'); 
		
		$sms=new sms($this->registry);
		$sms->sendsms($mobile,"26",$databypos);
		$this->response->setOutput(json_encode($ret));  
	}
	public function insertquery() 
	{
		
		$mcrypt=new MCrypt();
        	$log=new Log("call-".date('Y-m-d').".log");
		$log->write('insertquery called');
		
		if (!empty($mcrypt->decrypt($this->request->post['mobile']))) 
		{
			$mobile = $mcrypt->decrypt($this->request->post['mobile']); 		
		}
        	else if (!empty($mcrypt->decrypt($this->request->get['mobile']))) 
		{
			$mobile = $mcrypt->decrypt($this->request->get['mobile']); 		
		}
        	else 
		{
			$mobile = '';
		}
		if (isset($this->request->post['category'])) 
		{
			$Categories = $this->request->post['category']; 		
		}
        	else if (isset($this->request->get['category'])) 
		{
			$Categories = $this->request->get['category']; 		
		}
        	else 
		{
			$Categories = '';
		}
		if (isset($this->request->post['category_name'])) 
		{
			$category_name = $this->request->post['category_name']; 		
		}
        	else if (isset($this->request->get['category_name'])) 
		{
			$category_name = $this->request->get['category_name']; 		
		}
        	else 
		{
			$category_name = '';
		}
		if (isset($this->request->post['type'])) 
		{
			$Type = $this->request->post['type']; 		
		}
        	else if (isset($this->request->get['type'])) 
		{
			$Type = $this->request->get['type']; 		
		}
        	else 
		{
			$Type = '';
		}
		if (isset($this->request->post['type'])) 
		{
			$type_name = $this->request->post['type']; 		
		}
        	else if (isset($this->request->get['type'])) 
		{
			$type_name = $this->request->get['type']; 		
		}
        	else 
		{
			$type_name = '';
		}
		if (!empty($this->request->post['store_id'])) 
		{
			$store_id = $mcrypt->decrypt($this->request->post['store_id']); 		
		}
        	else if (!empty($this->request->get['store_id'])) 
		{
			$store_id = $mcrypt->decrypt($this->request->get['store_id']); 		
		}
        	else 
		{
			$store_id = '';
		}
		if (!empty($this->request->post['query'])) 
		{
			$query = ($this->request->post['query']); 		
		}
        	else if (!empty($this->request->get['query'])) 
		{
			$query = ($this->request->get['query']); 		
		}
        	else 
		{
			$query = '';
		}
        	$log->write($this->request->post);
		$log->write($this->request->get);

        	$this->adminmodel('ccare/ccare');
		$log->write($mobile);
		$results="";
        	if($mobile!="")
        	{
            	$this->adminmodel('sale/customer');    
		$this->adminmodel('user/user');
            	$customer_info = $this->model_user_user->getUserByUsername($mobile); 
	        $log->write($customer_info);
            
		$results = $this->model_ccare_ccare->insertquery($mobile,$customer_info,array('Categories'=>(int)$Categories,'category_name'=>$category_name,'Type'=>$Type,'type_name'=>$type_name,'store_id'=>$store_id,'query'=>$query,'channel'=>'App','call_status'=>14));
            	$ret=1;
        	}
        	else
        	{
			$ret=0;
        	}
		$category_name=str_replace('&amp;','and',$category_name);
		$databypos=array('name'=>$customer_info['firstname'],'ticketid'=>$results,'category_name'=>$category_name);
		$this->load->library('sms'); 
		
		$sms=new sms($this->registry);
		$sms->sendsms($mobile,"25",$databypos);
		
		$json=array('status'=>1,'msg'=>'Your query has been registered with us with Ticket ID : '.$results.'. We will contact you shortly.','ticketid'=>$results,);
		return $this->response->setOutput(json_encode($json)); 
	}
	
}
?>