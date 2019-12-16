<?php

class POS{
    public $api_id;
    public $api_name;
    public $api_store_id;
    public $api_group_id;
    public $api_cash;
    public $api_card;		
    public $success;
    public $api_mail;	
    public $error;
    public $api_cid;
    public $api_url;
    public $api_proprietor_name; 
    }
class ControllermposLogin extends Controller 
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
	public function update_t_n_c() 
    {
		$log=new Log("update_t_n_c-".date('Y-m-d').".log");
		$log->write($this->request->post);
		$mcrypt=new MCrypt();
		$keys = array(
			'userId',
			'app_id',
			'tnc_id'
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
		$this->request->post['user_id'] =$this->request->post['userId'] ;
		$this->adminmodel('user/user');
		$check_for_t_n_c=$this->model_user_user->get_t_n_c($this->request->post);
		if($check_for_t_n_c->num_rows>0)
		{
			$data=array('status'=>'1','msg'=>'Done'); 
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode( $data));
		}
		else
		{
			$api_info = $this->model_user_user->insert_t_n_c($this->request->post);
			$data=array('status'=>'1','msg'=>'Done'); 
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode( $data));
		}
		
	}
    public function index() 
    {
		$log=new Log("login-".date('Y-m-d').".log");

		$log->write("in");
		$this->load->language('api/login');
		$log->write("in1");
		// Delete old login so not to cause any issues if there is an error
		unset($this->session->data['api_id']);
	
		$mcrypt=new MCrypt();
		$keys = array(
			'username',
			'password',
			'utype',
			'eid',
            'id',
			'app_id'
		);

		$data=new POS();
		$log->write("in3");
		$log->write($this->request->post);
		if(empty($this->request->post))
		{
            exit("no input");

		}
		foreach ($keys as $key)
		{
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
            
       	 }

		$log->write($this->request->post);
		$json = array();
		$this->load->model('account/api');
        $log->write("in5");
		$this->adminmodel('user/user');
		$this->adminmodel('setting/store');
		$api_info = $this->model_account_api->loginm($this->request->post['username'], $this->request->post['password']);
		$log->write('data from model ');
		$log->write($api_info);

		if(($api_info) && ((empty($api_info['status'])) || ($api_info['status']==0)))
		{
			$log->write('in if status is false');
			$data->success = '0'; 
            $data->error = 'You are not an Active User.Please contact AgriPOS';
		
			$log->write($data);
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($data));
			
		}
		else
		{
		if ($api_info) 
		{ 
            //check emp type match
            $utype="1";//$this->request->post['utype'];
            if(($utype=="1"&&$api_info['user_group_id']=="11")||($utype=="1"&&$api_info['user_group_id']=="14")||($utype=="1"&&$api_info['user_group_id']=="16")||($utype=="1"&&$api_info['user_group_id']=="22")||($utype=="1"&&$api_info['user_group_id']=="26") ||($utype=="1"&&$api_info['user_group_id']=="27") ||($utype=="1"&&$api_info['user_group_id']=="29")|| ($utype=="1"&&$api_info['user_group_id']=="34") ||($utype=="1"&&$api_info['user_group_id']=="35")||($utype=="1"&&$api_info['user_group_id']=="36")) 
            { 
                if($api_info['user_group_id']==29) 
                {
                    $api_info['user_group_id']=27;
				}
				$api_info['app_id']=$this->request->post['app_id'];
                $log->write("in");
                $data->api_id = $mcrypt->encrypt($api_info['user_id']);
                                       
                $data->api_name =$mcrypt->encrypt($api_info['firstname']." ".$api_info['lastname']); 
                $data->api_proprietor_name =$mcrypt->encrypt($api_info['firstname']." ".$api_info['lastname']); 
			
                $data->api_store_id =$mcrypt->encrypt($api_info['store_id']);
                $data->api_group_id =$mcrypt->encrypt($api_info['user_group_id']);
                $data->api_cash=$mcrypt->encrypt($api_info['cash']);
                $data->api_card=$mcrypt->encrypt($api_info['card']);
				$data->api_mail=$mcrypt->encrypt($api_info['email']);
				$data->State_Code=$mcrypt->encrypt($api_info['State_Code']);
				$data->State_Name=$mcrypt->encrypt($api_info['State_Name']);
				$data->Dist_Code=$mcrypt->encrypt($api_info['Dist_Code']);
				$data->Dist_Name=$mcrypt->encrypt($api_info['Dist_Name']);
				$data->api_url=$mcrypt->encrypt($this->model_setting_store->getstore($api_info['store_id'])["url"]);
				$data->api_cid=$mcrypt->encrypt( $this->model_setting_store->getStore(( $api_info['store_id']))["company_id"]);	
				
				if($api_info['user_group_id']!="14"){
					$check_for_t_n_c=$this->model_user_user->get_t_n_c($api_info);
					if($check_for_t_n_c->num_rows>0)
					{
						$data->t_and_c = $mcrypt->encrypt('1');
					}
					else
					{
						$data->t_and_c = $mcrypt->encrypt('0');
					}
					}
					else if($api_info['user_group_id']=="14"){$data->t_and_c = $mcrypt->encrypt('1');}
					else
					{
						$data->t_and_c = $mcrypt->encrypt('0');
					}
                $data->success = $this->language->get('text_success');
                $data->error="0";
                                        
                $this->adminmodel('user/user');
                $this->model_user_user->update_device_token($this->request->post['id'],$api_info['user_id']);
            }
            else
            {
				$data->error=$this->language->get('error_login');
				$json['error'] ='Please Retry, User not active'; //$this->language->get('error_login');
            }
            $log->write($data);
            $log->write("outer");
		} 
		else
		{
            $data->error=$this->language->get('error_login');
            $json['error'] = 'User name OR Password is wrong'; //$this->language->get('error_login');
		}
		$log->write('output');
		$log->write($data);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
		}
	
    }
    public function change() 
    {
        $mcrypt=new MCrypt();
	$this->adminmodel('user/user');
	$log=new Log("change-".date('Y-m-d').".log");
	$log->write($this->request->post);
	$log->write($mcrypt->decrypt($this->request->post['username']));
	$json=array();
	$json['success']=$mcrypt->encrypt("Error");
	$json['type']       =$mcrypt->encrypt("0");  		

	$user=	$this->model_user_user->getUserByUsername($mcrypt->decrypt($this->request->post['username']));
		
        $log->write($user);
	////////////check passwordhistory start here//////////////
	$passwordhistory=$this->model_user_user->getUserpasswordhistory($user['user_id']);
	//print_r($passwordhistory);
        $log->write($passwordhistory);
	if(count($passwordhistory)>2)
	{
            $log->write('in if limit exceed');
            $json['success']=$mcrypt->encrypt("Password request reached its usage limit!"); 
            $json['type']        =$mcrypt->encrypt("0");  
            $this->response->setOutput(json_encode($json));
            return;
	}
	////////////check passwordhistory end here//////////////
	
	if($user)
        {
            $code = sha1(uniqid(mt_rand(), true));
            $log->write("in else");
            $this->model_user_user->editCodeUser($user['username'], $code);
            $user_info = $this->model_user_user->getUserByCode($code);
            $log->write($user_info);
            if ($user_info) 
            {	
		$pass=$mcrypt->decrypt($this->request->post['pid']);//$this->getToken(8);
		$this->model_user_user->editPassword($user_info['user_id'], $pass);
		$user_info['pass']=$pass;
		//send sms
		$log->write("send sms");
		$this->load->library('sms');		
        	$sms=new sms($this->registry);
		$log->write("send sms sending".$user_info['username']);
		//$user_info['telephone']
		$user_info['store_incharge_name']=strtoupper($user_info['firstname']);
               	$sms->sendsms($user_info['username'],"39",$user_info);   
		$log->write("send sms done");
		/////////////insert history////////
		try
		{
                    $this->model_user_user->addUserpasswordhistory($user['user_id'],$user['username'],$mcrypt->decrypt($this->request->post['imei']),'change');
		}
		catch(Exception $el)
		{
                    $log->write($el);
		}
		/////////insert history end here//////// 
		$json['success']=$mcrypt->encrypt("Your password has been reset successfully");
		$json['type']        =$mcrypt->encrypt("1");      			
            }
	
	}
        else
        {
            $json['success']=$mcrypt->encrypt("No user found");
            $json['type']        =$mcrypt->encrypt("0");
	}
	$this->response->setOutput(json_encode($json));			
    }
    //
    public function forgotten() 
    {
        $mcrypt=new MCrypt();
		$this->adminmodel('user/user');
		$log=new Log("forgot-".date('Y-m-d').".log");
		$json=array();
		$json['success']=$mcrypt->encrypt("Error");
		$json['type']       =$mcrypt->encrypt("0");  	
		$log->write($this->request->post);	
		$log->write($mcrypt->decrypt($this->request->post['uid']));
		$user=	$this->model_user_user->getUserByUsername($mcrypt->decrypt($this->request->post['uid']));
		$log->write($user);
		////////////check passwordhistory start here//////////////
		$passwordhistory=	$this->model_user_user->getUserpasswordhistory($user['user_id']);
		$log->write($passwordhistory);
		if(count($passwordhistory)>2)
		{
            $log->write('in if limit exceed');
            $json['success']=$mcrypt->encrypt("Password request reached its usage limit!");
            $json['type']        =$mcrypt->encrypt("0");  
            $this->response->setOutput(json_encode($json));
            return;
		}
		////////////check passwordhistory end here//////////////
        if($user)
        {
            $code = sha1(uniqid(mt_rand(), true));
            //$this->model_user_user->editCode($user['email'], $code);
            $log->write("in else");
            $this->model_user_user->editCodeUser($user['username'], $code);
            $user_info = $this->model_user_user->getUserByCode($code);
            $log->write("info");
            $log->write($user_info);
            if ($user_info) 
            {	
			$pass=sprintf("%06d", mt_rand(1, 999999)); //$this->getToken(8);
			$this->model_user_user->editPassword($user_info['user_id'], $pass);
			$user_info['pass']=$pass;
			//send sms
			$log->write("send sms");
			$this->load->library('sms');		
        	$sms=new sms($this->registry);
			$log->write("send sms sending");
			//$user_info['telephone'] 
            $sms->sendsms($user_info['username'],"4",$user_info);    
			$log->write("send sms done");

		/////////////insert history////////
		try
		{
                    $log->write('in try');
                    $this->model_user_user->addUserpasswordhistory($user['user_id'],$user['username'],$mcrypt->decrypt($this->request->post['imei']),'forgot'); 
                    $log->write('in try 2');
		}
		catch(Exception $el)
		{
                    $log->write('in catch');
                    $log->write($el);
		}
		/////////insert history end here//////// 
                $json['success']=$mcrypt->encrypt("New password has been send to your registered mobile number.");
		$json['type']        =$mcrypt->encrypt("1");      			
            }
		}
		$this->response->setOutput(json_encode($json));			
    }
    public function memberRequest()
    {
        $log=new Log("memberRequest_trans-".date('Y-m-d').".log");
        $log->write("memberRequest CALL");
        $log->write($this->request->post);
        $mcrypt=new MCrypt();    
        $keys = array(
            'sid',
            'name',
            'mobile',
            'mail',
            'password',
			'imei'    ,
			'transtype'
          );
        $log->write("check");
        foreach ($keys as $key) 
        {
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
        }
        $log->write($this->request->post);
        $this->adminmodel('user/user');
        $user_info = $this->model_user_user->getUserByUsername($this->request->post['mobile']);
        $log->write("check1");
        $log->write($user_info);
        $jsond=array();
        if(empty($user_info)&& ($this->request->post['transtype'])=='signup')
        {
            $log->write("check in if");
            $jsond=$this->generateOTP($this->request->post);
        }
        else if(!empty($user_info) && $user_info['status']=='0')
        {
            $jsond['status']="0";
            $jsond['message'] = ('User already registered.Please Login.');
        }
		
        else if(!empty($user_info)&&$user_info['status']=='1' && ($this->request->post['transtype'])!='signup')
        {
			$this->request->post['store_incharge_name']=$user_info['firstname'];
            $jsond=$this->generateOTP($this->request->post);    
        }
		
        else if(empty($user_info))
        {
            $jsond['status']="0";
            $jsond['message'] = ('Customer not found with this mobile number');
        }
        else
        {
            $jsond['status']="0";
            $jsond['message'] = ('Customer already exists with this mobile number');
        }
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
            $datatrans['ttype']= ($this->request->post['transtype']);
			$datatrans['store_incharge_name']= strtoupper($this->request->post['store_incharge_name']);
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
				$datatrans['otp']=$checkRepeatTrans["otp"];
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
				if($datatrans['ttype']=='forget')
				{
					$sms->sendsms($mobile,"38",$data);
				}
				else
				{
					$sms->sendsms($mobile,"7",$data);
				}
                /*************otp sms end******************/                
                $json['order_id'] = $mcrypt->encrypt( $query_return);
                $json['status']="1";
                $json['message'] = $mcrypt->encrypt('Success: new order placed with ID: '.$query_return);                        
                $log->write($json);
            }
            else
            {
                $json['status']="0";
                $json['message'] = ('Error in submission');
            }    
        }
        else
        {
            $json['status']="0";
            $json['message'] = ('One item transfered at one time');
        }
        $log->write('return data');
        $log->write($json);
        return $json;
    }

    function signup($data)
    {
        $this->adminmodel('setting/store');
        $this->adminmodel('user/user');
        $this->adminmodel('setting/setting');
        
        $log=new Log("signup-".date('Y-m-d').".log");
        $this->request->post=$data;        
        $mcrypt=new MCrypt();
        $log=new Log("signup-".date('Y-m-d').".log");
        $log->write('signupcall');
        $log->write($this->request->post);
        $json = array();
        if($this->request->post['name']=='')
        {
            $json['error'] = 'Error: Please enter Name.';
        }
            
        if($this->request->post['sid']=='')
        {
            $json['error'] = 'Error: Please enter sid.';
        }  
        if($this->request->post['mobile']=='')
        {
            $json['error'] = 'Error: Please enter telephone name.';
        }
        if($this->request->post['password']=='')
        {
            $json['error'] = 'Error: Please enter password.';
        }
        if($this->request->post['mail']=='')
        {
            $json['error'] = 'Error: Please enter email.';
        }
        
        //SMS LIB
        $this->load->library('sms');
        $keys = array(
            'sid',
            'name',
            'mobile',
            'mail',
            'password',
            'ttp',
            'token',
			'State_Code',
			'State_Name',
			'Dist_Code',
			'Dist_Name'
        );
        $log->write("check");
        foreach ($keys as $key) 
        {
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
        }
        $log->write($this->request->post);
        //check for otp
        $checkOtpTrans=$this->model_user_user->getVerifyUserOtp($this->request->post['sid']);
        $log->write($checkOtpTrans);
        if(!empty($checkOtpTrans))
        {
            if((!empty($checkOtpTrans['otp']))&&(strlen($checkOtpTrans['otp'])==4)&&($checkOtpTrans['otp']==$this->request->post['ttp']))
            {     
                $log->write("in");
                /////////////////for store//////////
                if(isset($this->request->post['mobile'])&& (strlen($this->request->post['mobile'])==10))
                {
                    $user_info = $this->model_user_user->getUserByUsername($this->request->post['mobile']);
                    if(empty($user_info))
                    { 
						$this->request->post['name']=strtoupper($this->request->post['name']);
                        $log->write("in user info");
                        $this->request->post['config_name']=strtoupper($this->request->post['name']);     
                        $this->request->post['config_url']='';
                        $this->request->post['config_company']='';
                        $this->request->post['config_ssl']='';
                        $this->request->post['config_storetype']=6;
						$this->request->post['config_storestatus']=1;
                        $log->write($this->request->post);
                        $store_id =$this->model_setting_store->addStore($this->request->post);
                        
                        $this->model_setting_setting->editSettingValue('sub_total', 'sub_total_status', 1, $store_id);
                        $this->model_setting_setting->editSettingValue('sub_total', 'sub_total_sort_order', 1, $store_id);
                        
                        $this->model_setting_setting->editSettingValue('tax', 'tax_status', 1, $store_id);
                        $this->model_setting_setting->editSettingValue('tax', 'tax_sort_order', 5, $store_id);
                        
                        $this->model_setting_setting->editSettingValue('total', 'total_status', 1, $store_id);
                        $this->model_setting_setting->editSettingValue('total', 'total_sort_order', 9, $store_id);
                        
						$this->model_setting_setting->editSettingValue('discount', 'discount_status', 1, $store_id);
                        $this->model_setting_setting->editSettingValue('discount', 'discount_sort_order', 6, $store_id);
						
                        $this->model_setting_setting->editSettingValue('config', 'config_invoice_prefix', 'INV-'.date('Y').'-00', $store_id);
						
                        $this->model_setting_setting->editSettingValue('config', 'config_registration_date', new MongoDate(strtotime(date('Y-m-d h:i:s'))), $store_id);
                        $store_info = $this->model_setting_setting->getSetting('config',0);
                        foreach ($store_info as $key => $value) 
                        {
                            if($key=='config_meta_title')
                            {
                               $value=strtoupper($this->request->post['name']); 
                            }
                            if($key=='config_meta_description')
                            {
                               $value=$this->request->post['name']; 
                            }
                            if($key=='config_telephone')
                            {
                               $value=$this->request->post['mobile']; 
                            }
                            if($key=='config_email')
                            {
                               $value=$this->request->post['mail']; 
                            }
                            if($key=='config_owner')
                            {
                               $value=strtoupper($this->request->post['name']); 
                            }
                            if($key=='config_name')
                            {
                               $value=strtoupper($this->request->post['name']); 
                            }
                            if($key=='config_storetype')
                            {
                               $value=6; 
                            }
                            if($key=='config_storetype_name')
                            {
                               $value='OPEN'; 
                            }
                            $this->model_setting_setting->editSettingValue('config', $key, $value, $store_id);
                        }
						$this->model_setting_setting->updateBillingStatus('billing','',$store_id,$msg='');
                        $category_info = $this->model_user_user->getBasicCategory(0);
                        foreach ($category_info as $category_info2) 
                        {
                            $this->model_user_user->setBasicCategory($category_info2['category_id'],$store_id);
                        }
                        
                        /////////////for user///////////////////////////
                        if(!empty($store_id) )
                        {
							$name1=explode(' ',$this->request->post['name']);
                            $log->write("in store_id");
                            $this->request->post['username']=($this->request->post['mobile']);
                            $this->request->post['user_group_id']='11';
                            $this->request->post['password']=($this->request->post['password']);
                            $this->request->post['confirm']=($this->request->post['password']);    
                            $this->request->post['firstname']=strtoupper($name1[0]);
							if(count($name1)>1)
							{
								$this->request->post['lastname']=strtoupper($name1[1]);
							}
							else
							{
								$this->request->post['lastname']='';
							}
                            $this->request->post['email']=($this->request->post['mail']);

                            $this->request->post['config_company']='';
                            $this->request->post['image']='';
                            $this->request->post['status']=1;//CONFIG_NEW_USER_STATUS;  
                            $this->request->post['user_store_id']=array($store_id);
			
                            $log->write($this->request->post);
                            $log->write("before adduser");
                            $user =$this->model_user_user->addUser($this->request->post); 
							
							$user_images =$this->model_user_user->count_upload_image($this->request->post['sid']); 
							$this->model_user_user->update_user_images($user,$user_images,$this->request->post['sid']);
							
                            $log->write($user);
                            $log->write('before getting user group menu');
                            $group_menu_info = $this->model_user_user->getGroupMenu(11);
                            //$log->write($group_menu_info);
                            foreach ($group_menu_info as $group_menu_info2) 
                            {
                                $this->model_user_user->setUserMenu($group_menu_info2['category_id'],$store_id,$group_menu_info2['menutype'],$group_menu_info2['parent_id'],$group_menu_info2['sort_order'],$user);
                            }
                            
                            if(!empty($user))
                            {
				$log->write('in if ');
                                $this->request->post['card']=$user;
                                $data=array('status'=>'1','message'=>'Signup successfully, please login using the same credentials');
                                $sms=new sms($this->registry);
                                $sms->sendsms($this->request->post['mobile'],"1",$this->request->post);
				$log->write('in if 2');				
								//$this->load->library('email');
								//$email=new email($this->registry);
				$log->write('in if 3');
								$mailbody = "<p style='border: 1px solid silver;padding: 15px;'>
										Dear ".$this->request->post['firstname'].' '.$this->request->post['lastname'].",
										<br/>
										Welcome to Unnati AgriPOS, mobility POS solution for retailers. Your UID is ".$user.". 
										<br/>
										For more information, help and support, you can reach us at 01204040180.
										<br/>
										This is computer generated email.Please do not reply to this email. 
										<br/><br/>
										<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
										<br/><br/>
										Thanking you,

										<br/>
										AgriPOS
			
										<br/><br/>
										<span  style='font-size:7.5pt;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
										</p>";
				$log->write('in if 4');
                                //$email->sendmail('Welcome to AgriPOS',$mailbody,$this->request->post['mail'],array("chetan.singh@akshamaala.com"),array('vipin.kumar@aspltech.com'));
				$log->write('in if 5');
                            }
                            else
                            {
				$log->write('Unable to create user');
                                $data=array('status'=>'0','message'=>'Unable to create user');  
                            }
            
                        }
                        else
                        {
			$log->write('Some error occur in creating the store');
                            $data=array('status'=>'0','message'=>'Some error occur in creating the store');  
                        }
                    }
                    else
                    {
			$log->write('Customer already exists with this mobile number');
                        $data=array('status'=>'1','message'=>'Customer already exists with this mobile number');
                    }
                }
                else
                {
		$log->write('Mobile number can not be empty');
                    $data=array('status'=>'0','message'=>'Mobile number can not be empty');  
                }
            }
            else
            {
		$log->write('OTP not matched with the system');
                $data=array('status'=>'0','message'=>'OTP not matched with the system.');  
            }
        }
        else
        {
	$log->write('Wrong transaction details');
            $data=array('status'=>'0','message'=>'Wrong transaction details.');  
        }
        $log->write($data);
        ////////////////////////////////////////////////////////////////////////////////////////
        return $data;  
    }

    public function verifyotp() 
    {
        $mcrypt=new MCrypt();
        $log=new Log("verifyotp-".date('Y-m-d').".log");
        $log->write('verifyotp-call');
        $log->write($this->request->post);
        $senddata=$this->request->post;
        $json = array();
        if($this->request->post['name']=='')
        {
            $json['error'] = 'Error: Please enter Name.';
        }
        if($this->request->post['sid']=='')
        {
            $json['error'] = 'Error: Please enter sid.';
        }  
        if($this->request->post['mobile']=='')
        {
            $json['error'] = 'Error: Please enter telephone name.';
        }
        if($this->request->post['password']=='')
        {
            $json['error'] = 'Error: Please enter password.';
        }
        if($this->request->post['mail']=='')
        {
            $json['error'] = 'Error: Please enter email.';
        }
        $this->adminmodel('setting/store');
        $this->adminmodel('user/user');
        //SMS LIB
        $this->load->library('sms');
             $keys = array(
            'sid',
            'name',
            'mobile',
            'mail',
            'password',
            'ttp',
            'transtype'
          );
        $log->write("check");
        foreach ($keys as $key) 
        {
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
        }
        $log->write($this->request->post);
        //check for otp
        $checkOtpTrans=$this->model_user_user->getVerifyUserOtp($this->request->post['sid']);
        $log->write("check2");
        $log->write($checkOtpTrans);
        if(!empty($checkOtpTrans))
        {
            if((!empty($checkOtpTrans['otp']))&&(strlen($checkOtpTrans['otp'])==4)&&($checkOtpTrans['otp']==$this->request->post['ttp']))
            {
                $log->write("check3");        
                $log->write($this->request->post['mobile']);
                $log->write(strlen($this->request->post['mobile']));
                /////////////////for store//////////
                if(isset($this->request->post['mobile'])&& (strlen($this->request->post['mobile'])==10))
                {
                    $user_info = $this->model_user_user->getUserByUsername($this->request->post['mobile']);
                    $log->write("check4");
                    $log->write($user_info);
                    if(!empty($user_info) && ($user_info['status']=='1'))
                    {
                        $log->write("check5");
                        $log->write(strtolower($this->request->post['transtype']));
                        //call signup $senddata
                        if(strtolower($this->request->post['transtype'])=='signup')
                        {
                            $data=$this->signup($senddata);
                        }
                        else
                        {
                            $data=array('status'=>'2','message'=>'correct transaction details.');
                        }
                        //else just success
                    }
                    else if(empty($user_info))
                    {
                        if(strtolower($this->request->post['transtype'])=='signup')
                        {
                            $log->write("before sending to signup");
                            $data=$this->signup($senddata);
                        }
                        else
                        {
                            $data=array('status'=>'0','message'=>'Unable to process request send by you.');
                        }
                    }
                    else
                    {
			$log->write("Your are not registered user");
                        $data=array('status'=>'0','message'=>'Your are not registered user');  
                    }
                }
                else
                {
                    $data=array('status'=>'0','message'=>'OTP not matched with the system.');  
                }
            }
            else
            {
                $data=array('status'=>'0','message'=>'Wrong transaction details.');  
            }
            $log->write("check6");
            ////////////////////////////////////////////////////////////////////////////////////////
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($data));
        }
    }
    
    public function getversion() 
    {
		$log=new Log("ver-".date('Y-m-d').".log");
		$this->load->language('api/login');
		// Delete old login so not to cause any issues if there is an error
		unset($this->session->data['api_id']);
			
		$mcrypt=new MCrypt();
		$keys = array(
			'username',
			'vid',
			);

		$log->write($this->request->post);
		foreach ($keys as $key) 
        {
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ; 
        }
        $log->write($this->request->post);
		$json = array();
        $this->load->model('account/api');		

		$this->load->model('account/activity');
        $activity_data = array(
			'customer_id' => $this->request->post['username'],
			'name'        => $this->request->post
			);
        //$this->model_account_activity->addActivity('version', $activity_data);
		$log->write("going to play store");
		//get version
		/*
		$html = file_get_contents('https://play.google.com/store/apps/details?id=com.unnatiagro.agripos');
		//$first_step = explode( '<div class="content" itemprop="softwareVersion">' , $html );
		$first_step = explode( 'Current Version' , $html );
		//$second_step = explode("</div>" , $first_step[1] );
		$second_step1 = explode('<span class="htlgb">' , $first_step[1] );
		//$log->write($second_step1);
	$second_step = explode('</span></div></span></div><div class="hAyfc"><div class="BgcNfc">Requires Android</div>' , $second_step1[2] );
			*/
	//print_r($second_step[0]);
	//$log->write($second_step);
	$output='1.13';//trim($second_step[0]);	
	$log->write($output);
	$log->write(bccomp($output, $this->request->post['vid']));
	if(bccomp($output, $this->request->post['vid'],2)==1)
	{
            $log->write("in =1");
            $json['ver']=$mcrypt->encrypt("1");
	}
	else if(bccomp($output, $this->request->post['vid'],2)==-1)
	{
            $log->write("in = -1");
            $json['ver']=$mcrypt->encrypt("0");
	}
	else
        {
            $log->write("in else");
            $json['ver']=$mcrypt->encrypt("0");
	}
	$json['ver']=$mcrypt->encrypt("0");
	$log->write($mcrypt->decrypt($json['ver']));
	$this->response->setOutput(json_encode($json));
    }
   /* function test()
    {
        $this->adminmodel('user/user');
        $group_menu_info = $this->model_user_user->getGroupMenu(11);
        print_r($group_menu_info);
    }*/

 function crypto_rand_secure($min, $max)
{
    $range = $max - $min;
    if ($range < 1) return $min; // not so random...
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd > $range);
    return $min + $rnd;
}

 function getToken($length)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i=0; $i < $length; $i++) {
        $token .=$codeAlphabet[$this->crypto_rand_secure(0, $max-1)];
    }

    return $token;
}


    

	public function getversioncard() 
	{
			$log=new Log("vercard-".date('Y-m-d').".log");
			$log->write("in");
			$this->load->language('api/login');
			$log->write("in1");
		// Delete old login so not to cause any issues if there is an error
		unset($this->session->data['api_id']);
			$log->write("in2");
		 $mcrypt=new MCrypt();
		$keys = array(
			'username',
			'vid',
			);

		$log->write($this->request->post);
		foreach ($keys as $key) {
            

                	$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
            
        		}



			$log->write($this->request->post);
		$json = array();

		$this->load->model('account/api');		

				$this->load->model('account/activity');

				$activity_data = array(
					'customer_id' => $this->request->post['username'],
					'name'        => $this->request->post
				);

				//$this->model_account_activity->addActivity('version', $activity_data);
		$log->write("going to play store");
			//get version
			$html = file_get_contents('https://play.google.com/store/apps/details?id=com.aspl.dsclqrcode');
			//$first_step = explode( '<div class="content" itemprop="softwareVersion">' , $html );
			//$second_step = explode("</div>" , $first_step[1] );
			//$first_step = explode( '<div class="content" itemprop="softwareVersion">' , $html );
			$first_step = explode( 'Current Version' , $html );
			
			//$second_step = explode("</div>" , $first_step[1] );
			$second_step1 = explode('<span class="htlgb">' , $first_step[1] );
			$second_step = explode('</span></div></div><div class="hAyfc">' , $second_step1[1] );
			//print_r($second_step[0]);
			$output=trim($second_step[0]);	
					$log->write($output);
			$log->write(bccomp($output, $this->request->post['vid']));
			if(bccomp($output, $this->request->post['vid'],2)==1)
			{
				$json['ver']=$mcrypt->encrypt("1");
			}
			else if(bccomp($output, $this->request->post['vid'],2)==-1)
			{
				$json['ver']=$mcrypt->encrypt("0");
			}
			else{
				$json['ver']=$mcrypt->encrypt("0");
			}
			$log->write($mcrypt->decrypt($json['ver']));
			$this->response->setOutput(json_encode($json));

}
        
public function getcashinhand()
{
     $log=new Log("logincash.log");
     $log->write("in getcashinhand");
		
    $mcrypt=new MCrypt();
		
    $log->write($this->request->post);
          
              $this->request->post['user_id'] =$mcrypt->decrypt($this->request->post['user_id']) ;
	$this->request->post['store_id'] =$mcrypt->decrypt($this->request->post['store_id']) ;
            
    

    $log->write($this->request->post);
    $json = array();

    $this->load->model('account/customer');
    $num_rows=$this->model_account_customer->getcashinhand($this->request->post['user_id'],$this->request->post['store_id']);
    //echo $num_rows=$this->model_account_customer->getcashinhand(78,22); 

if($num_rows>0)
{
$this->response->addHeader('Content-Type: application/json');
$this->response->setOutput($mcrypt->encrypt(0));
}  
else
{ 
$this->response->addHeader('Content-Type: application/json');
$this->response->setOutput($mcrypt->encrypt(2));   
}
}  
    //edit user
    public function edituser()
    {
        $mcrypt=new MCrypt();
        $log=new Log("editUser-".date('Y-m-d').".log");
        $log->write('editUsercall');
        $log->write($this->request->post);
        $json = array();
        $this->adminmodel('user/user');
        $this->adminmodel('setting/setting');
        if($this->request->post['proprietorname']=='')
        {
            $json['error'] = 'Error: Please enter ProprietorName.';
        }
          
        if($this->request->post['storename']=='')
        {
            $json['error'] = 'Error: Please enter Store Name.';
        }
        if($this->request->post['gst']=='')
        {
            $json['error'] = 'Error: Please enter GST.';
        }
        if($this->request->post['storeaddress']=='')
        {
            $json['error'] = 'Error: Please enter Store Name.';
        }
        $keys = array(
            'userId',
            'username',
            'proprietorname',
            'email',
            'store_id',
            'gst',
            'storeaddress',
            'storename',
			'State_Code',
			'State_Name',
			'Dist_Code',
			'Dist_Name'
          );
        $log->write("check");
        foreach ($keys as $key) 
        {
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
        }
        $user_id= $this->request->post['userId'];
        $store_id= $this->request->post['store_id'];
        $this->request->post['firstname']=($this->request->post['proprietorname']);
        $this->request->post['email']=($this->request->post['email']);
        $this->request->post['store_id']=($this->request->post['store_id']);
        $log->write($this->request->post);
        $user=$this->model_user_user->getUser($this->request->post['username']);
        $log->write($user);
        if(!empty($user))
        {
            $log->write('in if');
            $this->request->post['username']=$user['username'];
            $this->request->post['status']=$user['status'];
            $this->request->post['user_group_id']=$user['user_group_id'];
            $this->request->post['user_store_id'][0]=$this->request->post['store_id'];
            $log->write('before sending to edituser');
            $log->write($this->request->post);
            $user = $this->model_user_user->editUser($user_id,$this->request->post);
        }
        else
        {
            $data['error']='Some error occur! ';  
        }
        $this->request->post['code']='config';
        $this->request->post['store_id']=($this->request->post['store_id']);  
        
        $key='config_gstn';
        $value=$this->request->post['gst'];
        $log->write('before sending to change gst');
        $log->write(array($key=>$value));
        $this->model_setting_setting->editSetting($this->request->post['code'],array($key=>$value),$store_id);
        
        $key1='config_address';
        $value1=$this->request->post['storeaddress'];
        $log->write('before sending to change address');
        $log->write(array($key1=>$value1));
        $this->model_setting_setting->editSetting($this->request->post['code'],array($key1=>$value1),$store_id);
        
        $key2='config_email';
        $value2=$this->request->post['email'];
        $log->write('before sending to change email');
        $log->write(array($key2=>$value2));
        $this->model_setting_setting->editSetting($this->request->post['code'],array($key2=>$value2),$store_id);
        
        $key3='config_owner';
        $value3=$this->request->post['proprietorname'];
        $log->write('before sending to change proprietorname');
        $log->write(array($key3=>$value3));
        $this->model_setting_setting->editSetting($this->request->post['code'],array($key3=>$value3),$store_id);
        
        $key4='config_name';
        $value4=$this->request->post['storename'];
        $log->write('before sending to change store name');
        $log->write(array($key4=>$value4));
        $this->model_setting_setting->editSetting($this->request->post['code'],array($key4=>$value4),$store_id);
        
        $this->request->post['storename']=$this->request->post['storename'];
        $user = $this->model_user_user->updatestorename($store_id, $this->request->post['storename']);
        if(isset($user_id))
        {
            $log->write('in final success');
            $data['success']=$mcrypt->encrypt("Update successfully.");
        }
        else
        {
            $data['error']='Some error occur in creating the store';  
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
         
      }
      
}
