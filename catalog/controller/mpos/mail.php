<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');

ini_set("display_errors", "On");
error_reporting(E_ALL ^ E_NOTICE);
class Controllermposmail extends Controller 
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
	public function inventory_report()
	{
		$this->load->library('email');
		$email=new email($this->registry);
		$this->adminmodel('setting/store');
		$this->adminmodel('report/Inventory');
		$results=$this->model_setting_store->getStoresWeb(array());
		$fields = array(
				'Store Name',
				
				'Product Name',
				
				'Quantity',
				'Unit Price',
				'Amount'

			);
		foreach($results as $store)
		{
			$fdata=array();
			if($store['store_id']==170)
			{
			//print_r($store);
			$query = $this->model_report_Inventory->getInventory_report(array('filter_store' => $store['store_id']));
			$results= $query->row;
			foreach ($results as $result) 
			{
				$fdata[]=array(
                        strtoupper($store['name']),
                        
                        $result['model'],
						//$result['product_id'],
						$result['pd']['quantity'],
						number_format((float)$result['pd']['store_price'], 2, ".", ""),
						number_format((float)($result['pd']['store_price']*$result['pd']['quantity']), 2, ".", "")
					);
			}
			
			$file_name='inventory_report_'.$store['name'].'-'.date('ymdhis').'.csv';
			$file_name=str_replace(' ','_',$file_name);
			$email->create_csv($file_name,$fields,$fdata);
			
			$to=$store['store_id'];//'vipin.kumar@aspltech.com';   
			
			$cc=array();//'amit.s@akshamaala.com','ashok.prasad@akshamaala.com',"hrishabh.gupta@unnati.world");
			$bcc=unserialize(MAIL_BCC);//,'chetan.singh@akshamaala.com','sumit.kumar@aspltech.com');
			
			$file_path=array(DIR_UPLOAD.$file_name);
			$mail_subject='Daily Inventory report - '.$store['name'];
			$body = "<p style='border: 1px solid silver;padding: 15px;'>
			
			Please find attached file for Daily Inventory report.
			
			<br/><br/><br/>
			This is a computer generated email. Please do not reply to this email.
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
            $email->sendmail($mail_subject,$body,$to,$cc,$bcc,$file_path);
			$email->create_csv('inventory/'.$file_name,$fields,$fdata);
			$this->model_report_Inventory->setInventory_report_daily_email($file_name,date('Y-m-d'),$store['store_id'],$store['name']);
			echo 'done';
			}
		}
		
		
		//$this->load->library('email');
        //$sms=new email($this->registry);
		//$sms->sendmail("2","test");

	}
	
	public function summary()
	{
		$this->load->library('email');
		$email=new email($this->registry);
		
		///////////////////////////////////
		$this->adminmodel('report/sale_summary');
        $data['orders'] = array();
		$filter_store=0;
        $filter_data = array(
            
            'filter_store' => $filter_store
          
        );
			
		$Tilldateorder = $this->model_report_sale_summary->getTilldateorder($filter_data);
		$Yesterdayorder = $this->model_report_sale_summary->getYesterdayorder($filter_data);
		
		$Tilldateregister1=$this->model_report_sale_summary->getTilldateregister($filter_data);
		$Tilldateregister = $Tilldateregister1->num_rows;
		$Yesterdayregister = $this->model_report_sale_summary->getYesterdayregister($filter_data)->num_rows;
		
		$file_name1='summary_'.date('ymdhis').'.csv';
		
		$fields1 = array(
				'Invoice genrated',
				''

			);
		$fdata1=array();
		$fdata1[]=array(
				'Till Date',
				'Yesterday'				

			);
		$fdata1[]=array(
                        $Tilldateorder,
                        $Yesterdayorder
                       
						
					);
					
		$fdata1[]=array('','');			
		$fdata1[]=array('','');			
		$fdata1[]=array('','');			
		$fdata1[] = array('Retailer Registration','');
		$fdata1[]=array(
				'Till Date',
				'Yesterday'				

			);
		$fdata1[]=array(
                      
                        $Tilldateregister,
						$Yesterdayregister
						
					);
				
		$email->create_csv($file_name1,$fields1,$fdata1);
		
		///////////////////////////////
		
		$file_name="order_details_".date('dMy').'.csv';
		$fields = array(
				'Order ID',
				'Payment Method',
				'Customer',
				'Store',
				'Total',
				'Date Added',
				'Products'

			);
		$this->adminmodel('sale/order');
		$results = $this->model_sale_order->getOrders(array('filter_date_added'=>date('Y-m-d',strtotime("-1 days")),'filter_date_modified'=>date('Y-m-d',strtotime("-1 days")),'limit'=>1000000))->rows;
		foreach($results as $data)
    	{
			$fdata[]=array(
                        $data['order_id'],
                        $data['payment_method'],
                        strtoupper($data['payment_firstname'].' ('.$data['telephone'].')'),
						strtoupper($data['store_name']),
						$data['total'],
						date('d/m/Y',$data['date_added']->sec),
						json_encode($data['order_product'])
					);
		}
		$email->create_csv($file_name,$fields,$fdata);
		
		///////////////////////////////
		
		$file_name3="registration_details_".date('dMy').'.csv';
		$fields3 = array(
				'User ID',
				'Username',
				'Name',
				'Store ID',
				'Store Name',
				'Date Added'

			);
		$this->adminmodel('openretailer/openretailer');
		
		foreach($Tilldateregister1->rows as $data)
    	{ 	
			$storename = $this->model_openretailer_openretailer->getstoresetting($data['store_id'],'config_name');
			
			$fdata3[]=array(
                        $data['user_id'],
                        $data['username'],
                       strtoupper( $data['firstname'].' '.$data['lastname']),
						$data['store_id'],
						strtoupper($storename),
						date('d/m/Y',$data['date_added']->sec),
					);
		}
		$email->create_csv($file_name3,$fields3,$fdata3);
		
		///////////////////////////////
		
			$mail_subject="Yesterday Summary Report";
			
			$body = "<p style='border: 1px solid silver;padding: 15px;'>
			
			Please find attached file for Summary Report.
			
			<br/><br/>
			This is a computer generated email. Please do not reply to this email.
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
			
			$to=MAIL_TO;   
			$cc=unserialize(MAIL_CC);
			$bcc=unserialize(MAIL_BCC);
			
			$file_path=array(DIR_UPLOAD.$file_name1,DIR_UPLOAD.$file_name,DIR_UPLOAD.$file_name3);
           			 $email->sendmail($mail_subject,$body,$to,$cc,$bcc,$file_path);
	} 
	public function test()
	{
		$this->load->library('email');
		$email=new email($this->registry);
		$mail_subject='test for mail header';
		$body = "<p style='border: 1px solid silver;padding: 15px;'>
			
			Please find attached file for Summary Report.
			
			<br/><br/>
			This is a computer generated email. Please do not reply to this email.
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
		$to='vipin.kumar@aspl.ind.in';
		$bcc='';
		$cc=array('chetan.singh@akshamaala.com','sumit.kumar@aspl.ind.in','ashish.khullar@aspl.ind.in','vipinchahal.chahal@gmail.com');
		$file_path='';
		$email->sendmail($mail_subject,$body,$to,$cc,$bcc,$file_path);
	}
}

?>