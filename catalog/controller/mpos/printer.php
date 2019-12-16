<?php
class Controllermposprinter extends Controller 
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
		$log=new Log("printer-".date('Y-m-d').".log");
		$log->write('index called');
		$log->write($this->request->get);
		
		$mcrypt=new MCrypt();
        $this->adminmodel('printer/printer');
        //echo $mcrypt->encrypt(1);
		$data['store_id']=$mcrypt->decrypt($this->request->get['store_id']);
		$data['qusetions'] = $this->model_printer_printer->getprinterquestion()->rows;
		//print_r($data['qusetions']);
        $this->response->setOutput($this->load->view('default/template/printer/qusetionlist.tpl', $data));
    }
	public function answer()
	{
		$mcrypt=new MCrypt();
        $this->adminmodel('printer/printer');
		
		$log=new Log("printer-".date('Y-m-d').".log");
		$log->write('answer called');
		$log->write($this->request->get);
		
		$printer_id=($this->request->get['printer_id']);
		$data['store_id']=($this->request->get['store_id']); 
		$data['qusetion'] = $this->model_printer_printer->getprinteranswer(array('question_id'=>$printer_id))->row;
		//print_r($data['qusetion']);
        $this->response->setOutput($this->load->view('default/template/printer/answer.tpl', $data));
    }
	public function request()
	{
		$log=new Log("printer-".date('Y-m-d').".log");
		$log->write('request called');
		$log->write($this->request->post);
		$log->write($this->request->get);
		unset($this->session->data['success']);
		$mcrypt=new MCrypt();
        $this->adminmodel('printer/printer');
		$printer_id=($this->request->get['printer_id']);
		$store_id=($this->request->get['store_id']);
		
		$data['qusetion'] = $this->model_printer_printer->getprinteranswer(array('question_id'=>$printer_id))->row;
		//print_r($data['qusetion']['manufacturer_helpdesk']);
		if ($this->request->server['REQUEST_METHOD'] == 'POST')  
		{
			$this->request->post['printer_id']=$printer_id;
			$this->request->post['store_id']=$store_id;
			$ref_no=$this->model_printer_printer->printer_request($this->request->post);
			$this->load->model('account/activity');
			$activity_data = array(
						'customer_id' =>$store_id,
						'data'        => json_encode($this->request->post),
					);

		
			$this->model_account_activity->addActivity('printer-request', $activity_data);
			$this->load->library('email');
			$email=new email($this->registry);
			
			//////////////////////////
			$mail_subject='AgriPOS Request for printer Ref No : '.$ref_no;
			$mail_body = "<p style='border: 1px solid silver;padding: 15px;'>
			Hi,
			<br/>
			".$this->request->post['billing_name']." has requested a : ".$data['qusetion']['name']." printer with following necessary details. Please co-ordinate with the requester to facilitate this request.
			<br/><br/>
			Billing Name : ".$this->request->post['billing_name']."
			<br/>
			Contact Person Name : ".$this->request->post['contact_person_name']."
			<br/>
			Contact Number : ".$this->request->post['contact_number']."
			<br/>
			Email : ".$this->request->post['email']."
			<br/>
			Shipping Address : ".$this->request->post['shipping_address']."
			
			<br/><br/>
			Billing Address : ".$this->request->post['permanent_address']."
			<br/><br/>
			Thanks,
			<br/>
			Team AgriPOS
			<br/><br/>
			<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/>
			<br/>
			
			<span  style='font-size: 11px;color: red;'>Disclaimer: Unnati AgriPOS is only a technology partner for this communication on product request is not responsible for any logistic, supply, payment, warranty etc. related to this hardware & its request. Please fulfill the request at your own convenience and terms and conditions.</span>
			<br/>
			<span  style='font-size: 11px;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
			
			</p>";
			$to=$data['qusetion']['mail'];   
			$cc=array(); 
			$bcc=array('vipin.kumar@aspltech.com','chetan.singh@akshamaala.com','sumit.kumar@aspltech.com');
			
			$file_path='';
			$supplier_email=$email->sendmail($mail_subject,$mail_body,$to,$cc,$bcc,$file_path);
			////////////////////////////
			$mail_subject2='AgriPOS Request for printer Ref No : '.$ref_no;
			$mail_body2 = "<p style='border: 1px solid silver;padding: 15px;'>
			Hi ".$this->request->post['contact_person_name'].",
			<br/><br/>
			Your request for  a : ".$data['qusetion']['name']." printer has been submitted with following necessary details. You can reach out to the supplier to get this request fulfilled.
			<br/><br/>
			Supplier:
			<br/>
			".$data['qusetion']['name']." 
			</br>
			".$data['qusetion']['manufacturer_address']." 
			<br/><br/>
			Billing Name : ".$this->request->post['billing_name']."
			<br/>
			Contact Person Name : ".$this->request->post['contact_person_name']."
			<br/>
			Contact Number : ".$this->request->post['contact_number']."
			<br/>
			Email : ".$this->request->post['email']."
			<br/>
			Shipping Address : ".$this->request->post['shipping_address']."
			
			<br/><br/>
			Billing Address : ".$this->request->post['permanent_address']."
			<br/><br/>
			Thanks,
			<br/>
			Team AgriPOS
			<br/><br/>
			<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/>
			<br/>
			
			<span  style='font-size: 11px;color: red;'>Disclaimer: Unnati AgriPOS is only a technology partner for this communication on product request is not responsible for any logistic, supply, payment, warranty etc. related to this hardware & its request. Please fulfill the request at your own convenience and terms and conditions.</span>
			<br/>
			<span  style='font-size: 11px;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
			
			</p>";
			$to2=$this->request->post['email'];   
			$cc2=array(); 
			$bcc2=array('vipin.kumar@aspltech.com','chetan.singh@akshamaala.com','sumit.kumar@aspltech.com');
			
			$file_path2='';
			$requester_email=$email->sendmail($mail_subject2,$mail_body2,$to2,$cc2,$bcc2,$file_path2);
			///////////////////
			
			$msg='Your request for printer has been submitted successfully with ref no: '.$ref_no.'.Supplier will contact you shortly.Contact Supplier : '.$data['qusetion']['manufacturer_helpdesk'];
			$this->session->data['success'] = $msg;
			$url='&message='.$msg;
			$this->response->redirect($this->url->link('mpos/printer/success', $url, true));
		}
		else
		{
			$status_data = $this->model_printer_printer->check_status(array('printer_id'=>$printer_id,'store_id'=>$store_id));
			if(($status_data->num_rows)>0)
			{
				$ref_no=$status_data->row['sid'];
				$msg='You have already requested for this printer with ref no: '.$ref_no;
				//$this->response->setOutput($this->load->view('default/template/printer/success.tpl', $data));
				$url='&message='.$msg;
				$this->response->redirect($this->url->link('mpos/printer/already_success', $url, true));
			}
			else
			{
				$this->response->setOutput($this->load->view('default/template/printer/request_form.tpl', $data));
			}
		}
    }
    public function success()
	{
		$data['success']=$this->session->data['success']; 
		if(empty($data['success']))
		{
			$data['success']=$this->request->get['message'];
		}
        $this->response->setOutput($this->load->view('default/template/printer/success.tpl', $data));
    }  
	public function already_success()
	{
		$data['success']=$this->session->data['success']; 
		if(empty($data['success']))
		{
			$data['success']=$this->request->get['message'];
		}
        $this->response->setOutput($this->load->view('default/template/printer/success.tpl', $data));
    }
    public function check_status()
	{
		$mcrypt=new MCrypt();
        $this->adminmodel('printer/printer');
       
		$printer_id=($this->request->get['printer_id']);
		$store_id=($this->request->get['store_id']);
		$data = $this->model_printer_printer->check_status(array('printer_id'=>$printer_id,'store_id'=>$store_id));
		echo (json_encode($data->num_rows));
    }    
    
}