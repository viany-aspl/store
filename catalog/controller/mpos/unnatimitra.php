<?php
class Controllermposunnatimitra extends Controller 
{
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
		$log=new Log("unnatimitra-".date('Y-m-d').".log");
		$log->write('index called');
		$log->write($this->request->get);
		$mcrypt=new MCrypt();
        $this->adminmodel('catalog/unnatimitra');
		if(!empty($this->request->get['store_id']))
		{
			$data['store_id']=$mcrypt->decrypt($this->request->get['store_id']);
			
		}
		else
		{
			$data['store_id']='';//170;
		}
		$data['orders'] = $this->model_catalog_unnatimitra->getlist($data)->rows;
		$data['store_id']=$this->request->get['store_id'];
		//print_r(json_encode($data['orders'][0]['pd']));
		$this->adminmodel('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();
        $this->response->setOutput($this->load->view('default/template/unnatimitra/calculator.tpl', $data));
    }
    public function statement()
    {
        $log=new Log("unnatimitra-".date('Y-m-d').".log");
		$log->write('statement called');
		$log->write($this->request->get);
		$mcrypt=new MCrypt();
        $this->adminmodel('catalog/unnatimitra');
        $this->adminmodel('sale/customer');
		$data['store_id']=$this->request->get['store_id'];
        if((isset($_SESSION['mobile'])) && (!empty($_SESSION['mobile'])))
        {
            $time = $_SERVER['REQUEST_TIME'];

            $timeout_duration = 120;

            if (isset($_SESSION['LAST_ACTIVITY']) && 
            ($time - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) 
            {
                session_unset();
                session_destroy();
                session_start();
                unset($_SESSION['mobile']);
                $this->response->redirect($this->url->link('mpos/unnatimitra/statement', '', 'SSL'));
                   
            }
            $_SESSION['LAST_ACTIVITY'] = $time;
            
            $customer_info = $this->model_sale_customer->getCustomers(array('filter_telephone'=>$_SESSION['mobile']));
            $log->write($customer_info->row['customer_id']);
            $data['orders'] = $this->model_catalog_unnatimitra->getStatement(array('customer_id'=>$customer_info->row['customer_id']))->rows;
            $this->response->setOutput($this->load->view('default/template/unnatimitra/statement.tpl', $data));
        }
        else
        {
			//echo 'here';
			//print_r($data);
            $data['sid']= uniqid();//date('Ymdhis').rand(1000, 9999);//
            $this->response->setOutput($this->load->view('default/template/unnatimitra/statement_otp.tpl', $data));
        }
    }
    public function getotp()
    {
        $mcrypt=new MCrypt();
        $log=new Log("unnatimitra-".date('Y-m-d').".log");
		$log->write('getotp called');
        unset($_SESSION['mobile']);
        $this->adminmodel('user/user');
		$data['store_id']=$this->request->get['store_id'];
        $datatrans['otp']= rand(1000, 9999); 
        $datatrans['system_trans_id']= ($this->request->post['sid']);   
        $datatrans['imei']= ($this->request->post['sid']);   
        $datatrans['ttype']= ('Unnati-Mitra-statement');
        $datatrans['products']= $this->request->post;
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
        $mobile=$this->request->post['mobile'];
        $log->write($mobile);
        //SMS LIB
        $this->load->library('sms');    
        $sms=new sms($this->registry);            
        $data=array();
        $data=$datatrans;
        $log->write('before sending to sms');
        $log->write($data);
        $sms->sendsms($mobile,"36",$data);
        
        $this->response->redirect($this->url->link('mpos/unnatimitra/submitotp&mb='.$mcrypt->encrypt($this->request->post['mobile']).'&ssid='.$mcrypt->encrypt($query_return), '', 'SSL'));
        
    }
    public function submitotp()
    {
        $log=new Log("unnatimitra-".date('Y-m-d').".log");
		$log->write('submitotp called');
        unset($_SESSION['mobile']);
		$log->write($this->request->get);
        $log->write($this->request->post);
		$mcrypt=new MCrypt();
		$data['store_id']=$this->request->get['store_id'];
        $this->adminmodel('catalog/unnatimitra');
        if((isset($_SESSION['mobile'])) && (!empty($_SESSION['mobile'])))
        {
            $this->response->redirect($this->url->link('mpos/unnatimitra/statement&mb='.$mcrypt->encrypt($this->request->post['mobile']), '', 'SSL'));
        }
        else
        {
            if(isset($_SESSION['error']) && (!empty($_SESSION['error'])))
            {
                $data['error']=$_SESSION['error'];
                unset($_SESSION['error']);
            }
            $data['mobile']=$mcrypt->decrypt($this->request->get['mb']);
            $data['sid']=($this->request->get['ssid']);
            if(empty($data['sid']))
            {
                $data['sid']=($this->request->get['sid']);
            }
            $this->response->setOutput($this->load->view('default/template/unnatimitra/statement_otp_submit.tpl', $data));
        }
    }
    public function checkotp()
    {
        $log=new Log("unnatimitra-".date('Y-m-d').".log");
		$log->write('checkotp called');
        
		$log->write($this->request->get);
        $log->write($this->request->post);
		$mcrypt=new MCrypt();
		$data['store_id']=$this->request->get['store_id'];
        $this->adminmodel('catalog/unnatimitra');
        if((isset($_SESSION['mobile'])) && (!empty($_SESSION['mobile'])))
        {
            $this->response->redirect($this->url->link('mpos/unnatimitra/statement&mb='.$mcrypt->encrypt($this->request->post['mobile']), '', 'SSL'));
        }
        else
        {
            if(isset($this->request->post['ttp']) && (!empty(($this->request->post['ttp']))))
            {
                /////////verify otp
                $this->adminmodel('user/user');
                //echo $this->request->post['sid'];exit;
                $checkOtpTrans=$this->model_user_user->getVerifyUserOtp($mcrypt->decrypt($this->request->post['sid']));
                //print_r($checkOtpTrans);exit;
                $log->write($checkOtpTrans);
                if(!empty($checkOtpTrans))
                {
                    if((!empty($checkOtpTrans['otp']))&&(strlen($checkOtpTrans['otp'])==4)&&($checkOtpTrans['otp']==$this->request->post['ttp']))
                    {
                        unset($_SESSION['mobile']);
                        $_SESSION['mobile']=$this->request->post['mobile']; 
                        $this->response->redirect($this->url->link('mpos/unnatimitra/statement&mb='.$mcrypt->encrypt($this->request->post['mobile']), '', 'SSL'));
                    }
                    else 
                    {
                        $_SESSION['error']='OTP not matched with the system.';  
                        $this->response->redirect($this->url->link('mpos/unnatimitra/submitotp&mb='.$mcrypt->encrypt($this->request->post['mobile']).'&sid='.$this->request->post['sid'], '', 'SSL'));
        
                    }
                }
                else 
                {
                    $_SESSION['error']='OTP not matched with the system.';  
                    $this->response->redirect($this->url->link('mpos/unnatimitra/submitotp&mb='.$mcrypt->encrypt($this->request->post['mobile']).'&sid='.$this->request->post['sid'], '', 'SSL'));
                }
            }
            else 
            {
                $this->response->redirect($this->url->link('mpos/unnatimitra/submitotp&mb='.$mcrypt->encrypt($this->request->post['mobile']).'&sid='.$this->request->post['sid'], '', 'SSL'));
            }
        }
    }
    public function logout()
    {
        unset($_SESSION['mobile']);
        
        $this->response->redirect($this->url->link('mpos/unnatimitra/statement', '', 'SSL'));
                    
    }
}