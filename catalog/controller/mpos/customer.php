<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
class ControllermposCustomer extends Controller 
{
	public function adminmodel($model) 
	{
      
      $admin_dir = DIR_SYSTEM;
      $admin_dir = str_replace('system/','backoffice/',$admin_dir);
      $file = $admin_dir . 'model/' . $model . '.php';      
      //$file  = DIR_APPLICATION . 'model/' . $model . '.php';
      $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
      
      if (file_exists($file)) {
         include_once($file);
         
         $this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
      } else {
         trigger_error('Error: Could not load model ' . $model . '!');
         exit();               
      }
	}
	public function credit_sms()
	{
        $mcrypt=new MCrypt();
		$log=new Log("credit_sms-".date('Y-m-d').".log");
		$log->write($this->request->post);
		$json = array();
        
        $keys = array(
            'store_id',
            'store_name',
			'user_id',
			'telephone',
            'customer_name',
            'credit',
			'username'
        );

		foreach ($keys as $key) 
		{
			$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
        }
		
		$log->write($this->request->post);
		
		$this->adminmodel('user/user');
		$log->write(1);
        $msgtrans=$this->model_user_user->getCreditSmsTrans($this->request->post);
		$log->write(2);
		if($msgtrans->num_rows==0)
		{
			$this->load->library('sms');   
			$sms=new sms($this->registry);
			$sms->sendsms($this->request->post['telephone'],"37",$this->request->post);
			$log->write('after sending msg to farmer');
			$msgtrans=$this->model_user_user->insertCreditSmsTrans($this->request->post);
			$json['status'] =1;
			$json['msg'] = 'Success: Message Sent';
			$log->write($json);
			$this->response->setOutput(json_encode($json));
		}
		else
		{
			$log->write('in else ');
			$json['status'] =1;
			$json['msg'] = 'Message already Sent';
			$log->write($json);
			$this->response->setOutput(json_encode($json));
		}
		
	}
	public function addcustomer()
	{
        $mcrypt=new MCrypt();
		$log=new Log("addcustomer-".date('Y-m-d').".log");
		$log->write($this->request->post);
		$json = array();
        if($this->request->post['firstname']=='')
		{
            $json['error'] = 'Error: Please Enter name.';
		}
        if($this->request->post['telephone']=='')
		{
            $json['error'] = 'Error: Please enter telephone.';
		}
		
        $keys = array(
            'store_id',
            'firstname',
			'username',
			'telephone',
			'user_id',
            'state_code',
            'state_name',
            'dist_code',
            'dist_name',
            'village',
            'card_number',
            'ttp',
            'unnati_mitra'
        );

		foreach ($keys as $key) 
		{
			$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
        }

		$log->write($this->request->post);
		if(isset($this->request->post['unnati_mitra']) && (!empty(($this->request->post['unnati_mitra']))))
                {
                    /////////verify otp
                    $this->adminmodel('user/user');
                    $checkOtpTrans=$this->model_user_user->getVerifyUserOtp($mcrypt->decrypt($this->request->post['sid']));
                    $log->write($checkOtpTrans);
                    if(!empty($checkOtpTrans))
                    {
                        if((!empty($checkOtpTrans['otp']))&&(strlen($checkOtpTrans['otp'])==4)&&($checkOtpTrans['otp']==$this->request->post['ttp']))
                        {
                            
                        }
                        else 
                        {
                            $json['status']=0;
                            $json['msg'] = 'OTP not matched with the system.';
                            $log->write($json);
                            $this->response->setOutput(json_encode($json));
                            return;
                        }
                    }
                    else 
                    {
                        $json['status']=0;
                        $json['msg'] = 'OTP not matched with the system.';
                        $log->write($json);
                        $this->response->setOutput(json_encode($json));
                        return;
                    }
                    
                }
		$this->request->post['firstname']=strtoupper($this->request->post['firstname']);
		
		$this->load->library('sms');   
		$sms=new sms($this->registry);
		$this->adminmodel('sale/customer'); 
                $this->adminmodel('setting/setting'); 

                $store_info = $this->model_setting_setting->getSetting('config',$this->request->post['store_id']);
                $log->write('store_info');
                //$log->write($store_info);
                $retailer_telephone='';
                
                $retailer_telephone=$store_info['config_telephone']; 
                
                $log->write('retailer_telephone');
                $log->write($retailer_telephone);
				
		$this->adminmodel('sale/customer');      
        if (isset($this->request->post['telephone']) )
        {
			$customer_info = $this->model_sale_customer->getCustomers(array('filter_telephone'=>$this->request->post['telephone'],'filter_store_id'=>(int)$this->request->post['store_id']));
        }
        $log->write($customer_info->row);
        if (empty($customer_info->num_rows))
		{
            $log->write("in if customer info is empty");
            unset($this->session->data['cid']);
            $this->request->post['email']=($this->request->post['telephone']);
            $this->request->post['fax']=($this->request->post['telephone']);
            $this->request->post['password']=($this->request->post['telephone']);
			$this->request->post['customer_group_id']="1";
            $this->request->post['newsletter']='0';       
            $this->request->post['approved']='0';
            $this->request->post['status']='1';
            $this->request->post['safe']='1';
            $this->request->post['address_1']= ($this->request->post['village']);
            $this->request->post['address_2']= ($this->request->post['village']);
            $this->request->post['city']= ($this->request->post['village']);
            $this->request->post['company']='Unnati';
            $this->request->post['country_id']='0';
            $this->request->post['zone_id']='0';
            $this->request->post['postcode']='0';
            $this->request->post['store_id']=$this->request->post['store_id'];            
            $this->request->post['address']=array($this->request->post);
            $this->request->post['aadhar']=($this->request->post['aadhar']);
            
            
            $customer_id=$this->model_sale_customer->addCustomer($this->request->post);
			if(isset($customer_id))
			{
				if(isset($this->request->post['unnati_mitra']) && (!empty(($this->request->post['unnati_mitra']))))
                            {
                                $sms->sendsms($this->request->post['telephone'],"34",$this->request->post);
                                $log->write('after sending msg to farmer');
                                $sms->sendsms($retailer_telephone,"30",$this->request->post);
                                $log->write('after sending msg to retailer');
                            }
                            else
                            {
				$sms->sendsms($this->request->post['telephone'],"1",$this->request->post);
                            }
				$json['id']=$customer_id;
				$json['status'] =1;
				$json['msg'] = 'Success: customer added.';
				$log->write($json);
				$this->response->setOutput(json_encode($json));
				
			}
			else
			{
				$json['status']=0;
				$json['msg'] = 'Some error occur. Please try again.';
				$log->write($json);
				$this->response->setOutput(json_encode($json));;
			}
        }
        else
        {
			$log->write("in if customer info is not empty");
            $this->model_sale_customer->updateCustomer($this->request->post);
			if(isset($this->request->post['unnati_mitra']) && (!empty(($this->request->post['unnati_mitra']))))
                        {
                            $sms->sendsms($this->request->post['telephone'],"34",$this->request->post);
                            $sms->sendsms($retailer_telephone,"30",$this->request->post);
                        }
			$json['id']=$customer_info->row['customer_id'];
			$json['status'] = 1;
			$json['msg'] = 'Success: customer updated.';
			$log->write($json);
			$this->response->setOutput(json_encode($json));
        }
                                                
        
	}
	public function memberRequest()
    {
        $log=new Log("memberRequest_trans-".date('Y-m-d').".log");
        $log->write("memberRequest CALL in mpos/customer");
        $log->write($this->request->post);
        $mcrypt=new MCrypt();    
        $keys = array(
            'sid',
            'mobile',
            'imei',
			'msg_type'
          );
        $log->write("check");
        foreach ($keys as $key) 
        {
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
        }
        $log->write($this->request->post);
        $this->adminmodel('user/user');
        $jsond=$this->generateOTP($this->request->post);    
        
        // $log->write("returned");  
        $log->write("returned");
        $log->write($jsond);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($jsond));
        
    }
    function generateOTP($data)
    {
        $this->request->post=$data;
        $mcrypt=new MCrypt();
        $log=new Log("memberRequest_trans_otp-".date('Y-m-d').".log");
		$log->write('generateOTP called in customer');
		$log->write($this->request->post);
		$log->write($this->request->get);
        $this->adminmodel('user/user');
        $json= array();
        $datatrans=array();                
        $datatrans['otp']= rand(1000, 9999);                    
        $datatrans['products']=serialize($this->request->post);
        $log->write($datatrans['products']);
        if(!empty($this->request->post['mobile'])&& (strlen($this->request->post['mobile'])==10))
        {
            $datatrans['system_trans_id']= ($this->request->post['sid']);   
            $datatrans['imei']= ($this->request->post['imei']);   
            $datatrans['ttype']= ('Unnati-Mitra');
            $checkRepeatTrans=$this->model_user_user->getRegistrationotpTransId($this->request->post['sid']);
            $log->write('checkRepeatTrans');
            $log->write($checkRepeatTrans);
            if(empty($checkRepeatTrans))
            {
                $query_return=$this->model_user_user->insert_member_registration_otp_trans($datatrans);
                $log->write($query_return);
            }
            else
            {
				$datatrans['otp']=$checkRepeatTrans["otp"].' ';
                $query_return=$checkRepeatTrans["sid"];
            }
            if(!empty($query_return))
            {
                /*************otp sms******************/
                $mobile=$this->request->post['mobile'];
                $log->write($mobile);
                //SMS LIB
                $this->load->library('sms');    
                $sms=new sms($this->registry);            
                $data=array();
                $data=$datatrans;
                $log->write('before sending to sms');
                $log->write($data);
				if($this->request->post['msg_type']=='redeem')
				{
					$sms->sendsms($mobile,"32",$data);
				}
				else if($this->request->post['msg_type']=='unnati_mitra')
				{
					$sms->sendsms($mobile,"33",$data);
				}
				else
				{
					$sms->sendsms($mobile,"7",$data);
				}
                /*************otp sms end******************/                
                $json['order_id'] = $mcrypt->encrypt( $query_return);
                $json['status']="1";
                $json['msg'] = $mcrypt->encrypt('Success: OTP sent to mobile number: ');                        
                $log->write($json);
            }
            else
            {
                $json['status']="0";
                $json['msg'] = ('Error in submission');
            }    
        }
        else
        {
            $json['status']="0";
            $json['msg'] = ('Please check mobile number');
        }
        $log->write('return data');
        $log->write($json);
        return $json;
    }

	public function getbank()
	{
		$mcrypt=new MCrypt();
        $log=new Log("getbank-".date('Y-m-d').".log");
        $this->load->model('account/customer');                          			
		$jsons = $this->model_account_customer->getbank($uid);
		
		$log->write($jsons);
		foreach ($jsons as $ids) 
		{		
			$json['crops'][]=array('id'=>$mcrypt->encrypt($ids['bank_id']),'name'=>$mcrypt->encrypt($ids['bank']));						                                         
		}
		//print_r($json);
		$this->response->setOutput(json_encode($json));

	}
	public function test()
	{
		$mcrypt=new MCrypt();
		//a8aa55776aa4949832efa7f01d5425a6/0d732c03662a5cac072e2437b2c95ca0
		echo $mcrypt->decrypt($this->request->get['value']);
	}
	public function winv() 
    {
		$log=new Log("winv-".date('Y-m-d').".log");
		$log->write($this->request->post);
		$namearray=explode('/',$this->request->post['name']);
		$this->request->post['name']=$namearray[0];
		$this->request->post['inv_no']=$namearray[1];
		$mcrypt=new MCrypt();
		$keys = array(
			'user_id',
			'store_id',
			'store_name',
			'mobile_number',
			'name',
			'inv_no'
		);

		
		$log->write($this->request->post);
		if(empty($this->request->post))
		{
            exit("no input");

		}
		foreach ($keys as $key)
		{
            		$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
            
       		}
		$this->request->post['name']=strtoupper($this->request->post['name']);
		$this->adminmodel('user/user');
       
		$api_info = $this->model_user_user->insert_whatsapp_inv($this->request->post);
		$data=array('status'=>'1','msg'=>'Done'); 
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode( $data));
		
	}

	public function contact() 
    {
		$log=new Log("contact-".date('Y-m-d').".log");
		$log->write($this->request->post);
		if($this->request->post['name']=='')
		{
		$data=array('status'=>'0','msg'=>'Error: Please Enter name.'); 
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode( $data));
		return;
		}
        if($this->request->post['phone']=='')
		{
		$data=array('status'=>'0','msg'=>'Error: Please enter telephone.'); 
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode( $data));
		return;
		}

  		if($this->request->post['email']=='')
		{
		$data=array('status'=>'0','msg'=>'Error: Please enter email.'); 
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode( $data));
		return;
		}
				
		$log->write($this->request->post);
		if(empty($this->request->post))
		{
            exit("no input");

		}
		
		$this->request->post['name']=strtoupper($this->request->post['name']);
		$this->adminmodel('user/user');
		$this->adminmodel('ccare/ccare');
		$api_info = $this->model_user_user->insert_contact($this->request->post);
		
		$results = $this->model_ccare_ccare->insertquery($this->request->post['phone'],$this->request->post,array('Categories'=>(int)9,'category_name'=>'Other','Type'=>(int)4,'type_name'=>'Other','store_id'=>$store_id,'query'=>$this->request->post['msg'],'channel'=>'Web','call_status'=>17,'name'=>$this->request->post['name'],'email'=>$this->request->post['email']));
		//send mail
		$this->load->library('email');
		$email=new email($this->registry);
		$mail_subject='Contact us : Unnati AgriPOS ';
		$mailbody = "<p style='border: 1px solid silver;padding: 15px;'>
			Hi,
			<br/><br/>
			".$this->request->post['name']." has tried to reach out to Unnati AgriPOS with following query. Please resolve and reach out to the person with a solution.
			<br/><br/>
			Name : ".$this->request->post['name']."
			<br/><br/>
			Email : ".$this->request->post['email']."
			<br/><br/>
			Mobile : ".$this->request->post['phone']."
			<br/><br/>
			Message : ".$this->request->post['msg']."
			<br/><br/>
			<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/><br/>
			Thanks,
			<br/>
			Team AgriPOS
			
			<br/>
			<br/>
			<span  style='font-size:7.5pt;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
			</p>";
			$to='shalini.arya@aspl.ind.in'; 
			$bcc=array();
			$cc=array("sumit.kumar@aspl.ind.in");
			$email->sendmail($mail_subject,$mailbody,$to,$bcc,$cc);			

			/////////////////
			$mail_subject2='Unnati AgriPOS :Thanks for contacting us ';
			$mailbody2 = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear ".$this->request->post['name'].",
			<br/><br/>
			We appreciate your interest in Unnati AgriPOS. To address your query/concern, our customer care executive will reach out to you shortly.
			<br/><br/>
			Meanwhile to know more about Unnati AgriPOS, please visit our website https://unnatiagro.in/ for detailed information.
			<br/><br/>
			If you have further questions or need additional clarifications, please do not hesitate to contact us on +91 120 4040180.
			<br/><br/>
			We look forward to your patronage.
			
			<br/><br/>
			<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/><br/>
			Best Regards,
			<br/>
			Team AgriPOS
			
			<br/>
			<br/>
			<span  style='font-size:7.5pt;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
			</p>";
			$to2=$this->request->post['email']; 
			$bcc2=array();
			$cc2=array("sumit.kumar@aspl.ind.in");
			$email->sendmail($mail_subject2,$mailbody2,$to2,$bcc2,$cc2);			

			//end mail
			$data=array('status'=>'1','msg'=>'Done'); 
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode( $data));
		
	}
	public function referral() 
    {
		$log=new Log("referral-".date('Y-m-d').".log");
		$log->write($this->request->post);
		$mcrypt=new MCrypt();
		$keys = array(
			'user_id',
			'store_id',
			'store_name',
			'mobile_number',
			'name'
		);

		
		$log->write($this->request->post);
		if(empty($this->request->post))
		{
            exit("no input");

		}
		foreach ($keys as $key)
		{
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
            
       	}
		$this->request->post['name']=strtoupper($this->request->post['name']);
		$this->adminmodel('user/user');
       
		$api_info = $this->model_user_user->insert_referral($this->request->post);
		$data=array('status'=>'1','msg'=>'Done'); 
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode( $data));
		
	}
	

   public function getRetailer()
    {
		$log=new Log("getRetailer-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
	    $this->adminmodel('user/user');
		$log->write($this->request->get);
	    $log->write('data1');
	    $log->write($this->request->post);
	    $dist_code=$mcrypt->decrypt($this->request->post['district_id']);
		$log->write('data2');
	 $log->write( $dist_code);
	 $data=array('filter_Dist_Code'=>(int)$dist_code);
	 $log->write('data3');
	 $log->write( $data);
	$jsons = $this->model_user_user->getRetailer($data);
        foreach ($jsons as $ids) 
        {		
		
		    
            $json['crops'][] = array(
                        'id' => $mcrypt->encrypt($ids['user_id']),
                        'name'       =>$mcrypt->encrypt($ids['firstname'].'-'.$ids['username']), 
                        );
	}
        $this->response->setOutput(json_encode($json));
    }

   public function getCompany()
    {
        $mcrypt=new MCrypt();
	$this->adminmodel('setting/store');
	$jsons = $this->model_setting_store->getCompany();
        foreach ($jsons as $ids) 
        {		
            $json['crops'][] = array(
                        'id' => $mcrypt->encrypt($ids['manufacturer_id']),
                        'name'       =>$mcrypt->encrypt($ids['name']), 
                        );
	}
        $this->response->setOutput(json_encode($json));
    }

 public function getFieldActivity()
    {
        $mcrypt=new MCrypt();
	$this->adminmodel('setting/store');
	$jsons = $this->model_setting_store->getFieldActivity();
        foreach ($jsons as $ids) 
        {		
            $json['crops'][] = array(
                        'id' => $mcrypt->encrypt($ids['activityid']),
                        'name'       =>$mcrypt->encrypt($ids['activityname']), 
                        );
	}
        $this->response->setOutput(json_encode($json));
    }

    public function updatecreditbalance()
    {
        $mcrypt=new MCrypt();
        $this->adminmodel('pos/pos');
        $log=new Log("updatecreditbalance-".date('Y-m_d').".log");
        $log->write("updatecreditbalance called");	
		$log->write($this->request->post);	
		$log->write($this->request->get);	
        $mobile_no=$mcrypt->decrypt($this->request->post['mobile_no']);
        $cash_amount=$mcrypt->decrypt($this->request->post['cash_amount']);
        $sid=$mcrypt->decrypt($this->request->post['store_id']);
        $json = $this->model_pos_pos->searchCustomer($mobile_no,'',$sid);
		$log->write('after call to model');
		$log->write($json);
	
        $log->write($this->request->post);	
		$result=$this->model_pos_pos->updatecustomercash($json[0]['customer_id'],$cash_amount,$sid);
        if(!empty($result))
        {
            $json=1;
        }
        else
        {
            $json=0;
        }
        $log->write($json);	
        $this->response->setOutput($json);

    }
	public function getTotalCredit()
    {
        $log=new Log("totalcredit-".date('Y-m-d').".log");
        $log->write($this->request->get);
		$mcrypt=new MCrypt();
        $this->adminmodel('pos/pos');
        $store_id=$mcrypt->decrypt($this->request->post['store_id']);
        $json = $this->model_pos_pos->getTotalCredit(array('store_id'=>(int)$store_id));
        //print_r($json[0]['total']);
		$log->write($json);
        $njson['api_ids'][] = array('api_credit_balance'=>$mcrypt->encrypt($json[0]['total']));
        return $this->response->setOutput(json_encode($njson));
    }
	public function getpaidcreditrans()
	{
			$log=new Log("paidcreditrans-".date('Y-m-d').".log");
					$log->write("getpaidcreditrans called");
            $mcrypt=new MCrypt();
			
	        $store_id=$mcrypt->decrypt($this->request->post['store_id']);
            $user_id=$mcrypt->decrypt($this->request->post['user_id']);
			 
			$log->write($this->request->post);
			$log->write($store_id);
			$log->write($user_id);
			
	         $this->adminmodel('setting/store'); 
	      	 
	        $jsons=$this->model_setting_store->getpaidcredit($user_id,$store_id)->rows;
		
			//$log->write($jsons);
			$json=array();
			foreach ($jsons as $ids) {	

				if(round($ids['cash'])>0 and $ids['credit'] >0)
				{
				$ids['transtype']='Cash Credit';
				}
				else if($ids['cash']>0 and $ids['credit'] ==0)
				{
				$ids['transtype']='Paid for credit';
				}
				else if( round($ids['cash'])==0.0 and $ids['credit'] >0)
				{
				$ids['transtype']='Credit';
				}
				else{
				$ids['transtype']='Unknown';
				}
				
			$json['paiddtl'][] = array(
                        'credit_amount' => $mcrypt->encrypt($ids['cash']),
			'discount'=>  $mcrypt->encrypt($ids['discount']), 
			'invoice_no'=> $mcrypt->encrypt($ids['order_id']),			
                        'dat'       =>$mcrypt->encrypt(date('d-M-y', ($ids['create_time']->sec))),
						'transaction_type'       =>$mcrypt->encrypt($ids['transtype']),
						'total_credit'       =>$mcrypt->encrypt($ids['credit']),
                                     );
				}
			$log->write($json);
            $this->response->setOutput(json_encode($json));
}
    public function getcustomerpurchaseproductdtl()
    {  
        $log=new Log("getcustomerpurchaseproductdtl-".date('Y-m-d').".log");
		$log->write('getcustomerpurchaseproductdtl called'); 
	$log->write($_POST); 
	$log->write($_GET); 
	$mcrypt=new MCrypt();
	if(isset($this->request->post['telephone']))
        {
            $telephone=$mcrypt->decrypt($this->request->post['telephone']);
            $store_id=$mcrypt->decrypt($this->request->post['store_id']);
	}
	else
        {
            
	}
        $log->write($telephone); 
        $log->write($store_id); 
        $this->adminmodel('sale/order');
                
		$results1=$this->model_sale_order->getcustomerpurchaseproductdtl($telephone,$store_id); 
                $results= $results1->rows;
                //$log->write($results);  				
		foreach ($results as $result) 
		{
			if(empty($result['reward']))
                    {$result['reward']=0;}
			if(empty($result['cash']))
                    {$result['cash']=0;}
			$data['products'][] = array(
				'order_id'       => $mcrypt->encrypt($result['order_id']),
				'invoice_no'       => $mcrypt->encrypt($result['invoice_prefix'].'-'.$result['invoice_no']),
				'firstname'          => $mcrypt->encrypt($result['firstname']),
				'cash'          =>$mcrypt->encrypt( ($result['cash'])),
				'credit'          =>$mcrypt->encrypt( ($result['credit'])),
				'total'=>$mcrypt->encrypt(($result['total'])),
				'reward'=>$mcrypt->encrypt(($result['reward'])),
				'discount' =>	$mcrypt->encrypt(($result['discount'])),
				'date_added' =>	$mcrypt->encrypt(date('d,M Y',$result['date_added']->sec))
			);
		}
               
		$this->response->setOutput(json_encode($data));
                

		}


public function getStore(){

	$mcrypt=new MCrypt();
	$this->adminmodel('setting/store');
	$this->adminmodel('setting/setting');
	//$jsons = $this->model_setting_store->getStores();
	$log=new Log("store-login-".date('Y-m-d').".log");
	$this->adminmodel('user/user');
	$get_user_info=$this->model_user_user->getUser($mcrypt->decrypt($this->request->post["username"]));
	$log->write($get_user_info);
	$user_group=$get_user_info["user_group_id"];
	$user_store=$get_user_info["store_id"];
	$company_id = $this->model_setting_store->getCompanybystore($user_store);
    	$log->write($company_id);

	//$jsons =$this->model_setting_store->getStoresCompanyWise($company_id);
	$jsons =$this->model_setting_store->getOwnStores(array());
	if($user_group=='26')
	{
		$filter_data=array('filter_user'=>$mcrypt->decrypt($this->request->post["username"]));
		$jsons = $this->model_setting_store->getStoresByUser($filter_data); 
		foreach ($jsons as $ids) 
		{
			if(!empty($ids['store_id']))
			{

 				if($ids['store_id']!='19')
				{

 					if($ids['store_id']!='14')
					{
   						if($ids['config_storestatus']=='1')
						{


							$json['crops'][] = array(
                       								 'id' => $mcrypt->encrypt($ids['store_id']),
                       								 'name'       =>$mcrypt->encrypt($ids['name']),
									'address'   =>	$mcrypt->encrypt($this->model_setting_setting->getSettingbykey('config','config_address',$ids['store_id']))
                                    							 );
						}/////////end if of status
		
					}////////end if of store id 14
				}////////////////end if of storeb id 19
			}////////end if of store not empty
		}//////////end of foreach loop

	}
	else if($company_id=='3')
	{
			$jsons =$this->model_setting_store->getStoresCompanyWise($company_id);
			$log->write('in else if company id is 3 means isec');
			$log->write($jsons);

			foreach ($jsons as $ids) 
			{
				if(!empty($ids['store_id']))
				{

 					if($ids['store_id']!='19')
					{

 						if($ids['store_id']!='14')
						{

							if($ids['store_id']!=$mcrypt->decrypt($this->request->post['store_id']))
							{
								if($ids['config_storestatus']=='1')
								{
									$json['crops'][] = array(
                        								'id' => $mcrypt->encrypt($ids['store_id']),
                        								'name'       =>$mcrypt->encrypt($ids['name']),
									'address'   =>	$mcrypt->encrypt($this->model_setting_setting->getSettingbykey('config','config_address',$ids['store_id']))
                                     							);
								}/////////end if of status
							}////////end if of store is not own store
						}////////end if of store id 14
					}////////end if of store id 19
				}/////////end if of store not empty
			}/////////end of foreach loop
	}/////////////end of else if company id is 3 means isec
	else
	{
			$log->write('in else');
			$log->write($jsons);

			foreach ($jsons as $ids) 
			{
				if(!empty($ids['store_id']))
				{

 					if($ids['store_id']!='19')
					{

 						if($ids['store_id']!='14')
						{

							if($ids['store_id']!=$mcrypt->decrypt($this->request->post['store_id']))
							{
								if($ids['config_storestatus']=='1')
								{
									$json['crops'][] = array(
                        								'id' => $mcrypt->encrypt($ids['store_id']),
                        								'name'       =>$mcrypt->encrypt($ids['name']),
									'address'   =>	$mcrypt->encrypt($this->model_setting_setting->getSettingbykey('config','config_address',$ids['store_id']))
                                     							);
								}/////////end if of status
							}////////end if of store is not own store
						}////////end if of store id 14
					}////////end if of store id 19
				}/////////end if of store not empty
			}/////////end of foreach loop
	}/////////////end of else

             $this->response->setOutput(json_encode($json));
}


//news
public function getNewsLatest(){

		    $mcrypt=new MCrypt();
$log=new Log("newslatest-".date('Y-m-d').".log");
$log->write($this->request->post);

$url="https://news.google.com/news/rss/headlines/section/q/agriculture/agriculture?ned=hi_in&hl=hi&gl=IN";
$news = simplexml_load_file($url);

$feeds = array();

$i = 0;
$dat["crops"]=array();
foreach ($news->channel->item as $item) 
{
 
	if($i==2)
	{
		break;
	}   
    preg_match('@src="([^"]+)"@', $item->description, $match);
    $parts = explode('<font size="-1">', $item->description);

    $feeds[$i]['title'] = (string) $item->title;
    $feeds[$i]['link'] = (string) $item->link;
    $feeds[$i]['image'] = $match[1];
    $feeds[$i]['site_title'] = strip_tags($parts[1]);
    $feeds[$i]['story'] = strip_tags($parts[2]);
    $feeds[$i]['date']=  (string) $item->pubDate;
    $json["crops"][]=array(
                            'id'=>('1234'),
		'name'=>(strip_tags($parts[2])),
                            'by'=>(string) $item->title,
		'desc'=>(strip_tags($parts[0])),
		'date'	=>date('D d M Y',strtotime($item->pubDate)),
		'link'=>((string) $item->link),
		'imgread'=>("http://".$match[1])
	);
    $i++;
}
$log->write($json);
//echo '<pre>';
//print_r(json_encode($dat["crops"]));
             $this->response->setOutput(json_encode($json));
}

public function getNews(){

		    $mcrypt=new MCrypt();
	        $this->adminmodel('setting/store');

$url="https://news.google.com/news/rss/headlines/section/q/agriculture/agriculture?ned=hi_in&hl=hi&gl=IN";
$news = simplexml_load_file($url);

$feeds = array();

$i = 0;
$dat["crops"]=array();
foreach ($news->channel->item as $item) 
{
    //print_r((string)$item->title);
    preg_match('@src="([^"]+)"@', $item->description, $match);
    $parts = explode('<font size="-1">', $item->description);

    $feeds[$i]['title'] = (string) $item->title;
    $feeds[$i]['link'] = (string) $item->link;
    $feeds[$i]['image'] = $match[1];
    $feeds[$i]['site_title'] = strip_tags($parts[1]);
    $feeds[$i]['story'] = strip_tags($parts[2]);
    $feeds[$i]['date']=  (string) $item->pubDate;
    $json["crops"][]=array(
                            'id'=>('1234'),
		'name'=>(strip_tags($parts[1])),
                            'by'=>(string) $item->title,
		'desc'=>(strip_tags($parts[0])),
		'date'	=>date('D d M Y',strtotime($item->pubDate)),
		'link'=>((string) $item->link),
		'imgread'=>("http://".$match[1])
	);
    $i++;
}

             $this->response->setOutput(json_encode($json));
} 



public function getNewsById(){

		    $mcrypt=new MCrypt();
            $log=new Log("newsid.log");
             $log->write($this->request->post);
             $log->write($this->request->get);
             $log->write(($this->request->get['id']));
	        $this->adminmodel('setting/store');
			$jsons = $this->model_setting_store->getNewsByID(($this->request->get['id']));
			$json=array('crops'=>array());
			foreach ($jsons as $ids) {		
			$json['crops'][] = array(
                        'id' => ($ids['NewsItemID']),
                        'name'       =>($ids['NewsHeader']),
			'by'	=>($ids['PublishedBy']),
			'desc' =>	($ids['NewsDetails']),
			'img'	=>	($ids['NewsImage']),
			'date'	=>	($ids['DatePublished'])	
                                     );
				}
		$this->response->addHeader('Content-Type: application/json');
             $this->response->setOutput(json_encode($json));
}
//end news

    

public function getTransport(){

		    $mcrypt=new MCrypt();
	        $this->adminmodel('setting/store');
			$jsons = $this->model_setting_store->getTransport();

			foreach ($jsons as $ids) {		
			$json['crops'][] = array(
                        'id' => $mcrypt->encrypt($ids['trans_id']),
                        'name'       =>$mcrypt->encrypt($ids['name']),
                                     );
				}
             $this->response->setOutput(json_encode($json));
}

public function upload()
{

//upload file
            $log=new Log("upload-".date('Y-m-d').".log");
             $log->write($this->request->post);
             $log->write($this->request->files);
             //log to table
        
   
                $this->load->model('account/activity');

       
        //
            
        $this->load->language('api/upload');

        $json = array();



        if (!$json) {
            if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
                // Sanitize the filename
                $filename = html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8');

                if ((utf8_strlen($filename) < 1) || (utf8_strlen($filename) > 128)) {
                    $json['error'] = $this->language->get('error_filename');
                }

                // Allowed file extension types
                $allowed = array();

                $extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

                $filetypes = explode("\n", $extension_allowed);

                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

               /* if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
                    $json['error'] = $this->language->get('error_filetype');
                }*/

                // Allowed file mime types
                $allowed = array();

                $mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

                $filetypes = explode("\n", $mime_allowed);
		$log->write($filetypes);
                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

                /*if (!in_array($this->request->files['file']['type'], $allowed)) {
                    $json['error'] = $this->language->get('error_filetype');
                }*/

                // Check to see if any PHP files are trying to be uploaded
                $content = file_get_contents($this->request->files['file']['tmp_name']);

                if (preg_match('/\<\?php/i', $content)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Return any upload error
                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }
            } else {
                $json['error'] = $this->language->get('error_upload');
            }
        }

        if (!$json) {
            $file = $filename . '.' . md5(mt_rand());

            move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD . $file);

            // Hide the uploaded file name so people can not link to it directly.
                       
            $json['success'] = $this->language->get('text_upload');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    
        
//

}

public function setgeo()
{
		//name,address,geocode,telephone,fax,image,open,comment
		$log=new Log("geo-".date('Y-m-d').".log");
		$mcrypt=new MCrypt();
		$keys = array(
			'username',
			'geocode',
			'store_id',
			'name',
			'address'
		);
		$log->write("in geo");
		$log->write($this->request->post);
		foreach ($keys as $key) {            
                	$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;            
        	}
		$this->request->post['fax']=$this->request->post['store_id'];
		$log->write($this->request->post);
		$this->load->language('localisation/location');
		$this->adminmodel('localisation/location');
		$data=$this->model_localisation_location->getLocationStore($this->request->post['store_id']);
		$log->write($data);
		if(empty($data)){
		$this->model_localisation_location->addLocation($this->request->post);		 
		}else{
			$this->model_localisation_location->editLocation($data["location_id"],$this->request->post);		 
		}
		$this->adminmodel('setting/setting');
		$this->model_setting_setting->editSettingValue('config','config_geocode',$this->request->post['geocode'],$this->request->post['store_id']);
		//also update geocode in setting table as per store
		$json['success'] = $mcrypt->encrypt("Success: Geo data saved");
		$json['geocode'] = $mcrypt->encrypt($this->request->post['geocode']);
		$this->response->setOutput(json_encode($json));

}

public function fortest()
{
	////send mail to admin////
								$this->send_accept_mail_to_admin(110,100,8,212,499,36);
								
								/////////////
}
private function send_accept_mail_to_admin($ce_id,$amount,$store_id,$user_id,$current_cash,$utype)
{
	
		$this->adminmodel('runner/cash');
		$ce_name=$this->model_runner_cash->get_name_by_id($ce_id);
		$store_incharge_name=$this->model_runner_cash->get_name_by_id($user_id);	
		$store_name=$this->model_runner_cash->get_store_name_by_id($store_id);
		
        $asyncOperationMail=new AsyncOperationMail($ce_name,$amount,$store_name,$store_incharge_name,$current_cash,$utype);
        $asyncOperationMail->start();	
        
}

public function gethelp()
{

	    $mcrypt=new MCrypt();

             $this->load->model('account/customer');       
                    
			$jsons = $this->model_account_customer->gethelp();

foreach ($jsons as $ids) {		
$json['crops'][] = array(
                        'id' => $mcrypt->encrypt($ids['help_id']),
                        'name'       =>$mcrypt->encrypt($ids['help']),
			'aname' =>$mcrypt->encrypt($ids['help_name']),
			'anum' => $mcrypt->encrypt("Email - ".$ids['help_email']),
			'atype' => $mcrypt->encrypt($ids['help_type']),
			'acode' => $mcrypt->encrypt("Phone - ".$ids['help_number']),
			'abranch' => $mcrypt->encrypt($ids['help_branch'])

                                            );
}

$this->response->setOutput(json_encode($json));



}

public function getStoreNoAdd(){

		    $mcrypt=new MCrypt();
	        $this->adminmodel('setting/store');
			$jsons = $this->model_setting_store->getStores();
			foreach ($jsons as $ids) {		
			$json['crops'][] = array(
                        'id' => $mcrypt->encrypt($ids['id']),
                        'name'       =>$mcrypt->encrypt($ids['name']),
                                     );
				}
             $this->response->setOutput(json_encode($json));
}


        public function addcustomer_by_mobile()
        {
             
            $mcrypt=new MCrypt();
            $log=new Log("addcust-".date('Y-m-d').".log");

            $log->write($this->request->post);
            $json = array();
             
            if($this->request->post['telephone']=='')
            {
                 $json['error'] = 'Error: Please enter telephone number.';
             }
             if($this->request->post['longtitude']=='')
             {
                 $json['error'] = 'Error: Please enter longtitude.';

             }
             if($this->request->post['lattitude']=='')
             {
                 $json['error'] = 'Error: Please enter lattitude.';
             }
             
             $log->write("check");
             //check mobilenummber exits
            $keys = array(
			'username',
			'store_id',
			'telephone',
			'village',
			'pincode',
			'firstname',
			'card',
			'crop1',
			'crop2',
			'acre1',
			'acre2',
                        'longtitude',
                        'lattitude',
                        'muid'	
		);

            foreach ($keys as $key) 
            {
                $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;//$this->request->get[$key]; //
            }
            $log->write($this->request->post);
            $this->adminmodel('sale/customer');       
            if (isset($this->request->post['telephone']) ) 
            {
                $customer_info = $this->model_sale_customer->getCustomerByEmailApp($this->request->post['telephone']);
            }
            $log->write($customer_info);
            if (empty($customer_info))
            {
             $log->write("check if");
             unset($this->session->data['cid']);
             $this->request->post['email']=($this->request->post['telephone']);
             $this->request->post['fax']=($this->request->post['telephone']);
             $this->request->post['password']=($this->request->post['telephone']);
             $this->request->post['customer_group_id']="1";
             $this->request->post['newsletter']='0';        
             $this->request->post['approved']='1';
             $this->request->post['status']='1';
             $this->request->post['safe']='1';
             $this->request->post['address_1']= ($this->request->post['village']);
             $this->request->post['address_2']= ($this->request->post['village']);
             $this->request->post['city']= ($this->request->post['village']);
             $this->request->post['company']='Unnati';
             $this->request->post['country_id']='0';
             $this->request->post['zone_id']='0';
             $this->request->post['postcode']='0';
             $this->request->post['store_id']=$this->request->post['store_id'];             
             $this->request->post['address']=array($this->request->post);
             $this->request->post['imei']=$this->request->post['muid'];    
             $this->model_sale_customer->addCustomer_by_mobile($this->request->post);
             
            if(isset($this->session->data['cid']))
            {
                 $json['id']=$mcrypt->encrypt($this->session->data['cid']);
                 $log->write($this->session->data['cid']);
               
            }
           
            }
             else
             {
                 if(!isset($json['error']))
                 {
                    $json['id']=$mcrypt->encrypt($customer_info["customer_id"]);
                    $log->write($customer_info["customer_id"]);
	   }
             }
             //html update              
             
             if(!isset($json['error']))
             {
                            $json['success'] = 'Success: customer added.';
                            $log->write($this->request->post['telephone']);
	              	
                            $json['telephone'] = $mcrypt->encrypt($this->request->post['telephone']); 
	              //sms send
          	              //SMS LIB
		$this->load->library('sms');	
		$sms=new sms($this->registry);
                            $sms->sendsms($this->request->post['telephone'],"1",$this->request->post);       
             }
	$log->write($json);	          

            $this->response->setOutput(json_encode($json));
             //                                       
        }

        public function Customer()
        {
            $log=new Log("cust-".date('Y-m-d').".log");
            $log->write($this->request->get);
			$mcrypt=new MCrypt();
            $this->adminmodel('pos/pos');
            $q =$mcrypt->decrypt($this->request->get['q']);   
			$store_id =$mcrypt->decrypt($this->request->get['store_id']); 			
            $log->write("in");
            $log->write($store_id);
            $json = $this->model_pos_pos->searchCustomer($q,10,$store_id); //$store_id          
            $log->write($json);
            $njson['api_ids'] = array();
            foreach ($json as $ids) 
            {
                $jsons=$ids;
                $log->write('in loop');
                $log->write('store_id'.$store_id);
                $log->write('customer_id'.$jsons['customer_id']);
                
                $credit=$this->model_pos_pos->getCustomerCredit($jsons['customer_id'],$store_id);
                if(empty($credit))
                {
                    $credit=0;
                }
				$log->write('credit');
                $log->write($credit);
		$log->write("village name");
		$log->write($jsons['village']);	
                $njson['api_ids'][] = array(
                        'api_id' => $mcrypt->encrypt($jsons['customer_id']),
                        'api_name'       =>$mcrypt->encrypt($jsons['firstname']." ".$jsons['lastname']),
                        'api_cash'        =>$mcrypt->encrypt($jsons['telephone']),
						'reward'        =>$mcrypt->encrypt($jsons['reward']),
						'api_credit_balance'        =>$mcrypt->encrypt($credit),
					    'api_aadhar'        =>$mcrypt->encrypt($jsons['aadhar']),
                     'unnati_mitra'        =>$mcrypt->encrypt($jsons['unnati_mitra']),
                                
                                'state_code'=>$mcrypt->encrypt($jsons['state_code']),
                                'state_name'=>$mcrypt->encrypt($jsons['state_name']),
                                'dist_code'=>$mcrypt->encrypt($jsons['dist_code']),
                                'dist_name'=>$mcrypt->encrypt($jsons['dist_name']),
                                'village'=>$mcrypt->encrypt(strtoupper(trim($jsons['village']))),
                                'card_number'=>$mcrypt->encrypt($jsons['card_number']),
								'card_type'=>$mcrypt->encrypt('Normal')
                    );
            }				
            return $this->response->setOutput(json_encode($njson));
        }
        public function getcustomer()
        {
            $log=new Log("cust-getcust-".date('Y-m-d').".log");
            $log->write($this->request->post);
			$log->write($this->request->get);

			$mcrypt=new MCrypt();
            $this->adminmodel('pos/pos');
            $this->load->model('account/customer');		
            $sid =$mcrypt->decrypt($this->request->post['store_id']);
            $uid =$mcrypt->decrypt($this->request->post['username']);
            $type =$mcrypt->decrypt($this->request->post['type']);
            $action =$mcrypt->decrypt($this->request->post['action']);
            $njson['products'] = array();
            $njson['api_ids'] = array();
            if(isset($this->request->get['q']))
            {
				$log->write("IN QUI");
                $q =$mcrypt->decrypt($this->request->get['q']);
				if(!empty($this->request->get['sid']))
				{
					//$sid=$mcrypt->decrypt($this->request->get['sid']);
				}
					$json = $this->model_pos_pos->searchCustomer($q,'',$sid);
            
					foreach ($json as $ids) 
					{
     
                    $date_added="0000-00-00";
					$log->write("before check in if array");
					$log->write($mcrypt->decrypt($this->request->get['sid']));
					$log->write($ids['store_id']);
					if (in_array($mcrypt->decrypt($this->request->get['sid']), $ids['store_id']))
                    {
						$log->write("IN if in array");
						$customer_balance=$this->model_pos_pos->getCustomerCredit($ids['customer_id'],$mcrypt->decrypt($this->request->get['sid']));
						$log->write('customer_balance');
						$log->write($customer_balance);
					}
					else
					{
						$log->write("IN else");
						$customer_balance=0;
						$log->write('customer_balance');
						$log->write($customer_balance);
						
					}
					
					$total_earn=0;
					$total_Redeem=0;
					$getCustomerEarnReward=$this->model_pos_pos->getCustomerTotalRewardSum(array('customer_id'=>$ids['customer_id'],'store_id'=>$sid))->row;
					foreach($getCustomerEarnReward as $rw)
					{
						if($rw['_id']=='Add')
						{
							$total_earn=$rw['total'];
						}
						if($rw['_id']=='Redeem')
						{
							$total_Redeem=$rw['total'];
						}
					}
					
					$log->write('total_earn');
					$log->write($total_earn);
					
					$log->write('total_Redeem');
					$log->write($total_Redeem);
					
                    $njson['products'][] = array(
                                'id' => $mcrypt->encrypt($ids['customer_id']),
                                'name'       =>$mcrypt->encrypt($ids['firstname']." ".$ids['lastname']),
                                'telephone'        =>$mcrypt->encrypt($ids['telephone']),
								'unnati_mitra'        =>$mcrypt->encrypt($ids['unnati_mitra']),
                        'reward'        =>$mcrypt->encrypt($ids['reward']), 
                                'balance'        =>$mcrypt->encrypt($customer_balance),
                                'api_aadhar'        =>$mcrypt->encrypt($ids['aadhar']),
                                'date_added' =>$mcrypt->encrypt($date_added) ,
                                'state_code'=>$mcrypt->encrypt($ids['state_code']),
                                'state_name'=>$mcrypt->encrypt($ids['state_name']),
                                'dist_code'=>$mcrypt->encrypt($ids['dist_code']),
                                'dist_name'=>$mcrypt->encrypt($ids['dist_name']),
                                'village'=>$mcrypt->encrypt($ids['village']),
                                'card_number'=>$mcrypt->encrypt($ids['card_number']),
								'total_earn'=>$mcrypt->encrypt($total_earn),
								'total_Redeem'=>$mcrypt->encrypt($total_Redeem),
								'card_type'=>$mcrypt->encrypt('Normal')
                            );
                    
					}
            }
            elseif (isset($this->request->get['cid'])) 
            {
                $cid =$mcrypt->decrypt($this->request->get['cid']);
                $json = $this->model_pos_pos->getCustomer($cid); 
                $log->write("in cide");
                //$log->write($json);
                $ids=$json;
                //foreach ($json as $ids) {
                $log->write("in cid array");
                $log->write($ids);
                $log->write("in cid array after");
                $log->write($ids['customer_id']);
                $date_added="0000-00-00";    
                $customer_balance=$this->model_pos_pos->getCustomerCredit($ids['customer_id'],$sid);//$ids['credit'];//
				$total_earn=0;
				$total_Redeem=0;
				$getCustomerEarnReward=$this->model_pos_pos->getCustomerTotalRewardSum(array('customer_id'=>$ids['customer_id'],'store_id'=>$sid))->row;
				foreach($getCustomerEarnReward as $rw)
				{
					if($rw['_id']=='Add')
					{
						$total_earn=$rw['total'];
					}
					if($rw['_id']=='Redeem')
					{
						$total_Redeem=$rw['total'];
					}
				}
				$log->write('total_earn');
				$log->write($total_earn);
				$log->write('total_Redeem');
				$log->write($total_Redeem);
				
                if($customer_balance>0)
                {
                    $njson['products'][] = array(
                        'id' => $mcrypt->encrypt($ids['customer_id']),
                        'name'       =>$mcrypt->encrypt($ids['firstname']." ".$ids['lastname']),
                        'telephone'        =>$mcrypt->encrypt($ids['telephone']),
						'unnati_mitra'        =>$mcrypt->encrypt($ids['unnati_mitra']),
                         'reward'        =>$mcrypt->encrypt($ids['reward']), 
						'balance'        =>$mcrypt->encrypt($customer_balance),
						'api_aadhar'        =>$mcrypt->encrypt($ids['aadhar']),
						'date_added' =>$mcrypt->encrypt($ids['date_added']) ,
                        'state_code'=>$mcrypt->encrypt($ids['state_code']),
                        'state_name'=>$mcrypt->encrypt($ids['state_name']),
                        'dist_code'=>$mcrypt->encrypt($ids['dist_code']),
                        'dist_name'=>$mcrypt->encrypt($ids['dist_name']),
                        'village'=>$mcrypt->encrypt($ids['village']),
                        'card_number'=>$mcrypt->encrypt($ids['card_number']),
						'total_earn'=>$mcrypt->encrypt($total_earn),
						'total_Redeem'=>$mcrypt->encrypt($total_Redeem),
								'card_type'=>$mcrypt->encrypt('Normal')
                    );
                }
				//}
                $log->write("in cid loop after");
     
            }
            else
            {
                $log->write("IN ELSE");
                $jsons = $this->model_pos_pos->getCustomers($sid,$uid,$type);
				//$log->write($jsons);
                foreach ($jsons as $ids) 
                {
                    if($this->model_account_customer->getLastOrderDate($ids['customer_id'])["date_added"]=="")
                    {
                        $date_added="";
                    }
                    else
                    {
                        $date_added=date('d/m/Y',strtotime($this->model_account_customer->getLastOrderDate($ids['customer_id'])["date_added"]));
                    }
                    $customer_balance=$this->model_pos_pos->getCustomerCredit($ids['customer_id'],$sid);//$ids['credit'];//
					$log->write($ids['customer_id']);
					$log->write($sid);
					$total_earn=0;
					$total_Redeem=0;
					$getCustomerEarnReward=$this->model_pos_pos->getCustomerTotalRewardSum(array('customer_id'=>$ids['customer_id'],'store_id'=>$sid))->row;
					foreach($getCustomerEarnReward as $rw)
					{
						if($rw['_id']=='Add')
						{
							$total_earn=$rw['total'];
						}
						if($rw['_id']=='Redeem')
						{
							$total_Redeem=$rw['total'];
						}
					}
					
					$log->write('total_earn');
					$log->write($total_earn);
					
					$log->write('total_Redeem');
					$log->write($total_Redeem);
					
                    $njson['products'][] = array(
                        'id' => $mcrypt->encrypt($ids['customer_id']),
                        'name'       =>$mcrypt->encrypt($ids['firstname']." ".$ids['lastname']),
                        'telephone'        =>$mcrypt->encrypt($ids['telephone']),
						'unnati_mitra'        =>$mcrypt->encrypt($ids['unnati_mitra']),
                         'reward'        =>$mcrypt->encrypt($ids['reward']),
                        'api_aadhar'        =>$mcrypt->encrypt($ids['aadhar']),
						'balance'        =>$mcrypt->encrypt($customer_balance),
						'date_added' =>$mcrypt->encrypt($date_added),
                        'state_code'=>$mcrypt->encrypt($ids['state_code']),
                        'state_name'=>$mcrypt->encrypt($ids['state_name']),
                        'dist_code'=>$mcrypt->encrypt($ids['dist_code']),
                        'dist_name'=>$mcrypt->encrypt($ids['dist_name']),
                        'village'=>$mcrypt->encrypt($ids['village']),
                        'card_number'=>$mcrypt->encrypt($ids['card_number']),
						'total_earn'=>$mcrypt->encrypt($total_earn),
						'total_Redeem'=>$mcrypt->encrypt($total_Redeem),
								'card_type'=>$mcrypt->encrypt('Normal')
			
                    );
            
		}
            }	
			if($action=='e')
			{
				$log->write('in email');
				//$njson['products']
				$this->load->library('email');
				$email=new email($this->registry);
			
				$file_name="Customer_credit_".date('dMy').'.csv';
				$fields = array(
				'Customer id',
				'Name',
				'Balance',
				'Reward Balance',
				'Total Reward Earn',
				'Total Reward Reddem',
				'Date added'
				);
			
			$log->write($njson);
			foreach($njson['products'] as $data)
    		{
				
				$fdata[]=array(
                        $mcrypt->decrypt($data['id']),
						$mcrypt->decrypt($data['name']),
						$mcrypt->decrypt($data['balance']),
						$mcrypt->decrypt($data['reward']),
						$mcrypt->decrypt($data['total_earn']),
						$mcrypt->decrypt($data['total_Redeem']),
                        $mcrypt->decrypt($data['date_added'])
                    );
				
			}
			
			$email->create_csv($file_name,$fields,$fdata);
			
			$mail_subject="Customers";
			
			$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear Team,
			<br/><br/>
			Please find attached file for Customers.
			
			<br/><br/>
			This is computer generated email.Please do not reply to this email.
			<br/><br/>
			<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/><br/>
			Thanking you,
			
			<br/>
			AgriPOS
			
			<br/>
			<br/>
			<span  style='font-size:7.5pt;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
			</p>";
			$to=$sid;   
			$cc=array();
			$bcc=array('vipin.kumar@asp.ind.in','hrishabh.gupta@asp.ind.in','chetan.singh@akshamaala.com');
			
			$file_path=DIR_UPLOAD.$file_name;
            $email->sendmail($mail_subject,$body,$to,$cc,$bcc,$file_path);
			
			$json1=array('status'=>1,'msg'=>'sent');
			$log->write("sent" );
			
			$json['products'][]=$json1;
			$log->write('return array');
			$log->write($json);
			$this->response->setOutput(json_encode($json));
				
			}
			else
			{
				return $this->response->setOutput(json_encode($njson));
			}
        }
		public function getstoreledger()
	{

        $mcrypt=new MCrypt();
                  $log=new Log("store-ledger-".date('Y-m-d').".log");

                     $this->adminmodel('report/storelazer');  
                  $store_id=$mcrypt->decrypt($this->request->post['store_id']);
        $filter_date_start=$mcrypt->decrypt($this->request->post['filter_date_start']);
        $filter_date_end=$mcrypt->decrypt($this->request->post['filter_date_end']);
        $page=$mcrypt->decrypt($this->request->post['page']);
        //$store_id=8;
        $filter_data = array(
            'filter_date_start'         => $filter_date_start,
            'filter_date_end'         => $filter_date_end,
            'filter_stores_id' => $store_id,
            'start'                  => ($page - 1) * 20,
            'limit'                  => 20
        );
    $results = $this->model_report_storelazer->getStorecash($filter_data);
//print_r($results);
    $json=array();
    foreach ($results as $result) {
           
            if($result['Withdrawals']!="")
            {
            $amount="-".($result['Withdrawals']);
            }
            if($result['Deposite']!="")
            {
            $amount=($result['Deposite']);
            }
            $data['ledger'][] = array(
                'amount'       => $mcrypt->encrypt($amount),
                'tr_type'      => $mcrypt->encrypt($result['Mode']),
                'remarks'      => $mcrypt->encrypt($result['remarks']),
                'order_id'   => $mcrypt->encrypt($result['order_id']),
                'payment_method'      => $mcrypt->encrypt($result['Mode']),
                            'updated_credit'      => $mcrypt->encrypt($result['Credit_Balance']),
                            'updated_cash'      => $mcrypt->encrypt($result['Cash_Balance']),
                            'create_time'      => $mcrypt->encrypt($result['Date']),
                            'store_name'      => $mcrypt->encrypt($result['store_name']),
                            'user_name'      => $mcrypt->encrypt($result['user_Name'])

                           
                               
            );
//print_r($result);
        }
//print_r($data);

$log->write($jsons);
$this->response->setOutput(json_encode($data,JSON_UNESCAPED_UNICODE));
//$this->response->setOutput();
}
		public function index() 
        {
            $this->load->language('api/customer');
            // Delete past customer in case there is an error
		unset($this->session->data['customer']);

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error']['warning'] = $this->language->get('error_permission');
		} else {
			// Add keys for missing post vars
			$keys = array(
				'customer_id',
				'customer_group_id',
				'firstname',
				'lastname',
				'email',
				'telephone',
				'fax'
			);

			foreach ($keys as $key) {
				if (!isset($this->request->post[$key])) {
					$this->request->post[$key] = '';
				}
			}

			// Customer
			if ($this->request->post['customer_id']) {
				$this->load->model('account/customer');

				$customer_info = $this->model_account_customer->getCustomer($this->request->post['customer_id']);

				if (!$customer_info || !$this->customer->login($customer_info['email'], '', true)) {
					$json['error']['warning'] = $this->language->get('error_customer');
				}
			}

			if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
				$json['error']['firstname'] = $this->language->get('error_firstname');
			}

			if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
				$json['error']['lastname'] = $this->language->get('error_lastname');
			}

			if ((utf8_strlen($this->request->post['email']) > 96) || (!preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email']))) {
				$json['error']['email'] = $this->language->get('error_email');
			}

			if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
				$json['error']['telephone'] = $this->language->get('error_telephone');
			}

			// Customer Group
			if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
				$customer_group_id = $this->request->post['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}

			// Custom field validation
			$this->load->model('account/custom_field');

			$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

			foreach ($custom_fields as $custom_field) {
				if (($custom_field['location'] == 'account') && $custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
					$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
			}

			if (!$json) {
				$this->session->data['customer'] = array(
					'customer_id'       => $this->request->post['customer_id'],
					'customer_group_id' => $customer_group_id,
					'firstname'         => $this->request->post['firstname'],
					'lastname'          => $this->request->post['lastname'],
					'email'             => $this->request->post['email'],
					'telephone'         => $this->request->post['telephone'],
					'fax'               => $this->request->post['fax'],
					'custom_field'      => isset($this->request->post['custom_field']) ? $this->request->post['custom_field'] : array()
				);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


public function setCash()
{ 
    $mcrypt=new MCrypt();
    $this->adminmodel('setting/store');
    $log=new Log("setcash-".date('Y-m-d').".lpg");
    $store_name=$mcrypt->decrypt($this->request->post['store_name']);
    $store_id=$mcrypt->decrypt($this->request->post['store_id']);
    $user_id=$mcrypt->decrypt($this->request->post['user_id']);
    $amount=$mcrypt->decrypt($this->request->post['amount']);
    $mobile=$mcrypt->decrypt($this->request->post['user_id']);
    $name=$mcrypt->decrypt($this->request->post['name']);
    $day=$mcrypt->decrypt($this->request->post['day']);
    $log->write($this->request->post);
    if(!empty($day))
     {
        $update_date=date('Y-m-d', strtotime(' -1 day'));
     }
     else
     {
       $update_date=date('Y-m-d h:i:s');
     }
     $jsons = $this->model_setting_store->setCash( $store_name,$store_id,$user_id,$amount,$mobile,$name,$update_date);  

     $json['success'] = 'Success: Transaction added.';

     $this->response->setOutput(json_encode($json));

	
}

public function getcashtrans()
{
$mcrypt=new MCrypt();
$log=new Log("cash.log");
$log->write($this->request->post);
$this->adminmodel('setting/store');      
      $sid=$mcrypt->decrypt($this->request->post['store_id']);
      $jsons = $this->model_setting_store->getcashtrans($sid);
$log->write($jsons);

$lamount = $this->model_setting_store->getcashpostion($sid);

$json['lamt']=$mcrypt->encrypt($lamount);
foreach ($jsons as $ids) {		
$json['crops'][] = array(
                        'name'       =>$mcrypt->encrypt($ids['name']),
                        'aname'       =>$mcrypt->encrypt($ids['store_name']),
			'pirce' =>$mcrypt->encrypt($ids['amount']),
			'date_added' => $mcrypt->encrypt($ids['update_date']),
						
                                            );
}

$this->response->setOutput(json_encode($json));



}


 public function getStorelocation(){
                            $log=new Log("Storelocation-".date('Y-m-d').".log");
		$mcrypt=new MCrypt();
	        $this->adminmodel('setting/store');
			       
                
		$jsons = $this->model_setting_store->getStorelocation();
                            $log->write($ids);

                          foreach ($jsons as $ids) {
                                          if($ids['store_geo']!="")
                                          {
			$json['geolocations'][] = array(
                'store_id' => $mcrypt->encrypt($ids['store_id']),
                'store_name'       =>$mcrypt->encrypt($ids['store_name']),
                'store_geo'       =>$mcrypt->encrypt($ids['store_geo']),
	  'store_address'       =>$mcrypt->encrypt($ids['store_address'])
                                 
			
                 );
		}	
                  }
		

             $this->response->setOutput(json_encode($json));
	}

	public function getnotifications()
	{

	    $mcrypt=new MCrypt();
        $log=new Log("notification-".date('Y-m-d').".log");
		$log->write('getnotifications called');
		$log->write($this->request->post);
		$log->write($this->request->get);
		$this->adminmodel('firebase/firebase');      
        $sid=$mcrypt->decrypt($this->request->post['store_id']);
		$username=$mcrypt->decrypt($this->request->post['username']);
		$log->write($sid);
		$log->write($username);
		$jsons = $this->model_firebase_firebase->getmynotifications(array('store_id'=>$sid,'username'=>$username));
		//$log->write($jsons);
		foreach ($jsons as $ids) 
		{		
			$json1['notifications_my'][] = array(
                                          'description'       =>$mcrypt->encrypt($ids['message']),
									'imgurl' => $mcrypt->encrypt($ids['image_url']),
									'title' => $mcrypt->encrypt($ids['title']),
									'heading' => $mcrypt->encrypt($ids['title']),
									'create_time ' => $mcrypt->encrypt($ids['publishedDate ']),
								);
		}
		$jsonsall = $this->model_firebase_firebase->getallnotifications();
		//$log->write($jsonsall);
		foreach ($jsonsall as $ids) 
		{		
			$json1['notifications_all'][] = array(
                                          'description'       =>$mcrypt->encrypt($ids['message']),
									'imgurl' => $mcrypt->encrypt($ids['image_url']),
									'title' => $mcrypt->encrypt($ids['title']),
									'heading' => $mcrypt->encrypt($ids['title']),
									'create_time ' => $mcrypt->encrypt($ids['publishedDate ']),
								);
		}
		if(!empty($json1['notifications_all']) && (!empty($json1['notifications_my'])))
			{
				$json['notifications']=array_merge($json1['notifications_my'],$json1['notifications_all']);
			}
			else if(!empty($json1['notifications_all']) && (empty($json1['notifications_my'])))
			{
				$json['notifications']=$json1['notifications_all'];
			}
			else if(empty($json1['notifications_all']) && (!empty($json1['notifications_my'])))
			{
				$json['notifications']=$json1['notifications_my'];
			}
		$this->response->setOutput(json_encode($json,JSON_UNESCAPED_UNICODE));
	}
	public function getnotificationscount()
	{

	   		$mcrypt=new MCrypt();
                  	$log=new Log("notofication-".date('Y-m-d').".log");
             		$this->adminmodel('notification/notification');                          			
			$uid=$mcrypt->decrypt($this->request->post['username']);
			$jsons = $this->model_notification_notification->getnotificationscount($uid);
			$log->write($jsons);
			$json='';
			foreach ($jsons as $ids) 
			{		
			$json=$mcrypt->encrypt($ids['count']);						                                         
			}
			$this->response->setOutput($json);

	}
	
public function getUnitsbyStore(){
			$log=new Log("getUnitsbyStore-".date('Y-m-d').".log");
            $mcrypt=new MCrypt();
	        $store_id=$mcrypt->decrypt($this->request->post['store_id']);
            $company_id=$mcrypt->decrypt($this->request->post['companyid']);
			$log->write($this->requiest->post);
			$log->write($store_id);
			$log->write($company_id);
	        $this->adminmodel('setting/store');
	        
	        $db_data=$this->model_setting_store->getUnitsbyStore($store_id);
			$log->write($db_data);
			$json=array();
			foreach($db_data as $dbd)
			{
				$json['units'][]=array('unit_id'=>$mcrypt->encrypt($dbd['unit_id']),'unit_name'=>$mcrypt->encrypt($dbd['unit_name']));
			}
			$log->write($json);
            $this->response->setOutput(json_encode($json));
}


//start

public function getstates()
	{
            $mcrypt=new MCrypt();
            $log=new Log("getstates-".date('Y-m-d').".log");
            $this->adminmodel('sale/customer');                          			
            $jsons = $this->model_sale_customer->getstates(array())->rows;
            $log->write($jsons);
            foreach ($jsons as $ids) 
            {	
		$json['crops'][]=array(
                                'State_Code'=>$mcrypt->encrypt($ids['_id']['code']),
                                'State_Name'=>$mcrypt->encrypt(strtoupper($ids['_id']['name']))
                                );						                                         
            }
            //print_r($json);
            $this->response->setOutput(json_encode($json));

	}
        public function getdisctricts()
	{
            $mcrypt=new MCrypt();
            $log=new Log("getdisctricts-".date('Y-m-d').".log");
            $this->adminmodel('sale/customer'); 
            $log->write($this->request->get);
            $log->write($this->request->post);
            $log->write($mcrypt->decrypt($this->request->post['State_Code']));
            if(isset($this->request->post['State_Code']))
            {
                $state_code=$mcrypt->decrypt($this->request->post['State_Code']);
            }
            else 
            {
                $state_code=1;
            }
            $jsons = $this->model_sale_customer->getdisctricts(array('state_code'=>$state_code))->rows;
            //$log->write($jsons);
            foreach ($jsons as $ids) 
            {	
                //print_r($ids);
		$json['crops'][]=array(
                                'Dist_Code'=>$mcrypt->encrypt($ids['District_Code']),
                                'Dist_Name'=>$mcrypt->encrypt(strtoupper($ids['District_Name']))
                                );						                                         
            }
            //print_r($json);
            $this->response->setOutput(json_encode($json));

	}

//end


public function verifyotp(){

                      $mcrypt=new MCrypt();
	        $customerID=$mcrypt->decrypt($this->request->post['customerID']);
                      $otp=$mcrypt->decrypt($this->request->post['otp']);
	        $this->adminmodel('setting/store');
	        $this->adminmodel('setting/setting');
	        $jsons = $this->model_setting_store->getStores();
			
                     $this->response->setOutput(json_encode(1));
}
public function verifycustomerotp()
{
	        $mcrypt=new MCrypt();
	        $customerID=$mcrypt->decrypt($this->request->post['customerID']);
                      $otp=$mcrypt->decrypt($this->request->post['otp']);
	        $this->adminmodel('sale/customer');    
	        $res=$this->model_sale_customer->verifycustomerotp($customerID,$otp);
                      if($res=="1")
                      {
                           $res=$this->model_sale_customer->approved_customerotp_update_status($customerID,$otp);
                           $json['success'] = 'Verified Successfully';
                      }
                      else
                      {
		$json['error'] = 'OTP is not Matched';
                      }	
                     $this->response->setOutput(json_encode($json));
}
}
//////////////////////////
/*class AsyncOperationMail extends Thread {

    public function __construct($ce_name,$amount,$store_name,$store_incharge_name,$current_cash,$utype) {
        $this->ce_name = $ce_name;
        $this->amount = $amount;
        $this->store_name = $store_name; 
        $this->store_incharge_name = $store_incharge_name;
        $this->current_cash = $current_cash;
        $this->utype = $utype;
    }

    public function run() 
    {

	$log=new Log("cash-new-".date('Y-m-d').".log");
	
	$log->write('come in run at thread for mail'); 
	$log->write($this->ce_name." && ".$this->amount." && ".$this->store_name." && ".$this->store_incharge_name." && ".$this->current_cash." && ".$this->utype);
	$ce_name=$this->ce_name ;
        $amount=$this->amount;
        $store_name=$this->store_name;
        $store_incharge_name=$this->store_incharge_name;
        $current_cash=$this->current_cash;
        $utype=$this->utype;
        	
        $mail  = new PHPMailer();
		if($utype==11)
		{
		$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear All,
			<br/><br/>
			<b>".$ce_name."</b> (Collection Executive) have recieved Amount Rs <b>".$amount."</b>  from ".$store_name." and Store Executive  <b>".$store_incharge_name." </b> .
			<br/>
			Before Deposit Cash IN-Hand for <b>".$store_incharge_name." : ".$current_cash."</b>
			<br/>
			After Deposit Current Cash IN-Hand for <b>".$store_incharge_name." : ".($current_cash-$amount)."</b>
			<br/><br/>
			This is computer generated alert. Please do not reply to this email.
			<br/><br/>
			<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/><br/>
			Thanking you, 
			<br/>
			IT Team
			<br/>
			Unnati
			<br/>
			<br/>
			<span  style='font-size:7.5pt;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
		</p>";
		}
		if($utype==36)
		{
		$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear All,
			<br/><br/>
			<b>".$ce_name."</b> (Store Executive) have recieved Amount Rs <b>".$amount."</b>  from ".$store_name." and Sub User  <b>".$store_incharge_name." </b> .
			<br/>
			Before Deposit Cash IN-Hand for <b>".$store_incharge_name." : ".$current_cash."</b>
			<br/>
			After Deposit Current Cash IN-Hand for <b>".$store_incharge_name." : ".($current_cash-$amount)."</b>
			<br/><br/>
			This is computer generated alert. Please do not reply to this email.
			<br/><br/>
			<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/><br/>
			Thanking you,
			<br/>
			IT Team
			<br/>
			Unnati
			<br/>
			<br/>
			<span  style='font-size:7.5pt;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
		</p>";
		}
        $mail->IsSMTP();
        $mail->Host       = "mail.akshamaala.in";
                                                           
        $mail->SMTPAuth   = false;                 
        $mail->SMTPSecure = "";                 
        $mail->Host       = "mail.akshamaala.in";      
        $mail->Port       = 25;                  
        $mail->Username   = "mis@akshamaala.in";  
        $mail->Password   = "mismis";            

        $mail->SetFrom('mail.akshamaala.in', 'Akshamaala');

        $mail->AddReplyTo('mail.akshamaala.in','Akshamaala');

        $mail->Subject    = "Cash Collection - ".$ce_name;

        $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		$mail->MsgHTML($body);
                
        
        	$mail->AddAddress('reena.rajput@unnati.world', 'Reena Rajput');
        
        	$mail->AddCC('vipin.kumar@aspltech.com', "Vipin");
	//$mail->AddCC('ashok.prasad@akshamaala.com', "Ashok Prasad");
		$mail->AddCC('pragya.singh@aspltech.com', "Pragya Singh");
		$mail->AddBCC('chetan.singh@akshamaala.com', "Chetan Singh");
		
		
        if(!$mail->Send())
		{
            //$this->response->setOutput(json_encode("Mailer Error: " . $mail->ErrorInfo)); 
        }
		else
		{
			//echo 'sent';
		}
    }
	
	
}
/////////////////////////////////////////////////
/////////////////////////////////////////////////
class AsyncOperation extends Thread {

    public function __construct($mobile,$muid,$scheme) {
        $this->mobile = $mobile;
        $this->muid = $muid;
        $this->scheme = $scheme;
        

    }

    public function run() {

	$log=new Log("recharge-".date('Y-m-d').".log");
		 $mcrypt=new MCrypt();
	$log->write('come in run at thread'); 
	$log->write($this->mobile."&&".$this->muid."&&".$this->scheme);
	$log->write($this->products);
	if (($this->mobile) && ($this->muid) && ($this->scheme) ) 
	{

		
	       	$request = "https://unnati.world/shop/index.php?route=mpos/recharge/rechargetest&mobile=".$this->mobile."&muid=".$this->muid."&scheme_id=".$this->scheme;
		$log->write($request);
		//$fields_string .= 'products'.'='.$mcrypt->encrypt(json_encode($this->products,true)).'&'; 
		rtrim($fields_string, '&');
		$log->write($fields_string);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);  
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POST, 1);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);  
		$json =curl_exec($ch);
		curl_close($ch); 
		$log->write($json);
        }	

    } 
		
		
}*/
