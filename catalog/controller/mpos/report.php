<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
error_reporting(1);
class ControllermposReport extends Controller {
    public function adminmodel($model) {
      
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
   public function test()
   {
	   $log=new Log("product_sales_report_gstr1".date('Y-m-d').".log");
		$log->write('GSTR 1 order wise call ');

		unset($this->session->data['filter_date_start']);
		unset($this->session->data['filter_date_end']);
		unset($this->session->data['filter_name_id']);
		unset($this->session->data['store_id']);

        $log->write($this->request->post);

		$mcrypt=new MCrypt();
                        
		$keys = array(
			'filter_date_start',
			'filter_date_end',
			'filter_name_id',
			'store_id',
			'action'
		);
 
		
		foreach ($keys as $key) 		
		{
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
 		}
		if(empty($this->request->post['filter_date_start']))
        {
                    $this->request->post['filter_date_start']=date('Y-m').'01';
        }
        if(empty($this->request->post['filter_date_end']))
        {
                    $this->request->post['filter_date_end']=date('Y-m-d');
        }
		$this->request->post['filter_date_start']='2018-05-01';
		$this->request->post['filter_date_end']='2018-12-30';
		$this->request->post['store_id']='301';
		$this->request->post['action']='e';
		$filter_data = array(
                        'filter_store'	     => $this->request->post['store_id'],
			'filter_date_start'	     => $this->request->post['filter_date_start'],
			'filter_date_end'	     => $this->request->post['filter_date_end'],
			'filter_name'           => '',
			'start'=>0,
                                'limit'=>1000
		);
		$log->write($filter_data);
		$this->adminmodel('report/product_sale');
		$results = $this->model_report_product_sale->getOrders($filter_data);
        
		if($this->request->post['action']=='e')
		{ 
			$log->write('in if email');
			
			$log->write('in if email');
			$this->load->library('email');
			$email=new email($this->registry);
			
			$file_name="Product_sales_report_".date('dMy').'.csv';
			$fields = array(
				'Sale Date',
				'Store Name',
				'Store ID',
				'Product Name',
				'Quantity',
				'Rate(without tax)',
				'Tax title',
				'Tax rate',
				'Total (Sales + Tax)',
				'Order ID',
				'Discount Type',
				'Discount Value'

			);
			
			
			foreach($results->rows as $data)
    		{
				if(!empty($data['discount_value']))
					{
						if($data['discount_type']=='P')
						{
							$price_without_tax=((number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', ''))-((number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', ''))*number_format((float)$data['discount_value'], 2, '.', '')/100));
						}
						else if($data['discount_type']=='F')
						{
							$price_without_tax=((number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', ''))-$data['discount_value']);
						}
						else
						{
							$price_without_tax=number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', '');
						}
					}
					else
					{
							$price_without_tax=number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', '');
					}

				//print_r($data);exit;
				$fdata[]=array(
                        date('Y-m-d',($data['date_added']->sec)),
                        $data['store_name'],
                        $data['store_id'],
						$data['order_product'][0]['name'],
						$data['order_product'][0]['quantity'],
						number_format((float)($data['order_product'][0]['price']), 2, '.', ''),
						$data['order_total'][1]['title'],
						number_format((float)$data['order_product'][0]['tax'], 2, '.', ''),
						number_format((float)($data['order_product'][0]['quantity']*(($data['order_product'][0]['price'])+$data['order_product'][0]['tax'])), 2, '.', ''),
						$data['order_id'],
						$data['discount_type'],
						$data['discount_value']
					);
				
			}
			
			$email->create_csv($file_name,$fields,$fdata);
			
			$mail_subject="GSTR 1 (Product sales) Report from ".$this->request->post['filter_date_start']." to ".$this->request->post['filter_date_end'];
			
			$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear Team,
			<br/><br/>
			Please find attached file for GSTR 1.
			
			<br/><br/>
			This is computer generated email.Please do not reply to this email.
			<br/><br/>
			<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/><br/>
			Thanking you,
			<br/>
			IT Team
			<br/>
			AgriPOS
			
			<br/>
			<br/>
			<span  style='font-size:7.5pt;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
			</p>";
			$to='vipin.kumar@aspl.ind.in';//$this->request->post['store_id'];   
			$cc=array();
			$bcc=unserialize(MAIL_BCC);
			
			$file_path=DIR_UPLOAD.$file_name;
            $email->sendmail($mail_subject,$body,$to,$cc,$bcc,$file_path);
			$json=array('status'=>1,'msg'=>'sent');
			$log->write("sent" );
			$log->write('return array');
			$log->write($json);
			$this->response->setOutput(json_encode($json));
			print_r(unserialize(MAIL_BCC));exit;
	
		}
		else
		{
			$file_name="Product_sales_report_".date('dMy').'.xls';
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=".$file_name);  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);

			echo '<table id="example2" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
					<th>Sale Date</th>
                    <th>Store Name</th>
                    <th>Store ID</th>
                    
                    <th>Product Name</th>
                    <th>Quantity</th>
					<th>Rate(without tax)</th>
					<th>Tax title</th>
					<th>Tax rate</th>
                    <th>Total (Sales + Tax)</th>
                    <th>Order ID</th>
					<th>Discount Type</th>
					<th>Discount Value</th>
                </tr>
                </thead>
                <tbody>';
			$tblbody=" ";
			foreach($results->rows as $data)
			{
					if(!empty($data['discount_value']))
					{
						if($data['discount_type']=='P')
						{
							$price_without_tax=((number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', ''))-((number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', ''))*number_format((float)$data['discount_value'], 2, '.', '')/100));
						}
						else if($data['discount_type']=='F')
						{
							$price_without_tax=((number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', ''))-$data['discount_value']);
						}
						else
						{
							$price_without_tax=number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', '');
						}
					}
					else
					{
							$price_without_tax=number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', '');
					}


                    echo  '<tr> 
				<td>'.date('Y-m-d',($data['date_added']->sec)).'</td>
                    <td>'.$data['store_name'].'</td>
                    <td>'.$data['store_id'].'</td>
                    <td>'.$data['order_product'][0]['name'].'</td>
				<td>'.$data['order_product'][0]['quantity'].'</td>
                    <td>'.number_format((float)($price_without_tax), 2, '.', '').'</td>
				<td>'.$data['order_total'][1]['title'].'</td>
                    <td>'.number_format((float)$data['order_product'][0]['tax'], 2, '.', '').'</td>
				<td>'.number_format((float)($data['order_product'][0]['quantity']*(($price_without_tax)+$data['order_product'][0]['tax'])), 2, '.', '').'</td>
                    <td>'.$data['order_id'].'</td>
				<td>'.$data['discount_type'].'</td>
				<td>'.$data['discount_value'].'</td>
                   </tr>';


			}
			echo '</tbody>
			</table>';
			exit;
		}
		
   }
    public function gstr1()
    {
   	
		$log=new Log("product_sales_report_gstr1-".date('Y-m-d').".log");
		$log->write('GSTR 1 Call');
		
		unset($this->session->data['filter_date_start']);
		unset($this->session->data['filter_date_end']);
		unset($this->session->data['filter_name_id']);
		unset($this->session->data['store_id']);

        $log->write($this->request->post);

		$mcrypt=new MCrypt();
                        
		$keys = array(
			'filter_date_start',
			'filter_date_end',
			'filter_name_id',
			'store_id',
			'action'
		);
 
		
		foreach ($keys as $key) 		
		{
              	    $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
 		}
        if(empty($this->request->post['filter_date_start']))
        {
                    $this->request->post['filter_date_start']=date('Y-m').'01';
        }
        if(empty($this->request->post['filter_date_end']))
        {
                    $this->request->post['filter_date_end']=date('Y-m-d');
        }
		$log->write($this->request->post);  
		$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' => ($this->request->post['store_id']),
						'data'        => json_encode($this->request->post),
					);

		$this->model_account_activity->addActivity('GSTR1', $activity_data);
		$json = array();		
		$data['orders'] = array();
                $filter_data = array(
         			'filter_store'	     => $this->request->post['store_id'],
                                'filter_date_start'	     => $this->request->post['filter_date_start'],
                                'filter_date_end'	     => $this->request->post['filter_date_end'],
                                'filter_name'           => $this->request->post['filter_name_id'],
                                'start'=>0,
                                'limit'=>1000
                            );
		$log->write($filter_data); 
                
		$this->adminmodel('report/product_sale');
		$results = $this->model_report_product_sale->exgetOrders($filter_data);
		$log->write($results); 
		if($this->request->post['action']=='e')
		{ 
			$log->write('in if email');
			$this->load->library('email');
			$email=new email($this->registry);
			
			$file_name="gstr1_report_".date('dMy').'.csv';
			$fields = array(
				'Type',
				'Place Of Supply',
				'Rate',
				'Taxable Value',
				'Cess Amount',
				'E-Commerce GSTIN'

			);
			
			
			foreach($results as $data)
    		{
				$fdata[]=array(
                        'E',
                        'UP',
                        $data['_id'],
						$data['total'],
						'',
						''
					);
				
			}
			
			$email->create_csv($file_name,$fields,$fdata);
			
			$mail_subject="GSTR 1 Report from ".$this->request->post['filter_date_start']." to ".$this->request->post['filter_date_end'];
			
			$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear Team,
			<br/><br/>
			Please find attached file for GSTR 1.
			
			<br/><br/>
			This is computer generated email.Please do not reply to this email.
			<br/><br/>
			<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/><br/>
			Thanking you,
			<br/>
			IT Team
			<br/>
			AgriPOS
			
			<br/>
			<br/>
			<span  style='font-size:7.5pt;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
			</p>";
			$to=$this->request->post['store_id'];   
			$cc=array();
			$bcc=array('vipin.kumar@aspl.ind.in','chetan.singh@akshamaala.com');
			
			$file_path=DIR_UPLOAD.$file_name;
            $email->sendmail($mail_subject,$body,$to,$cc,$bcc,$file_path);
			
			$json=array('status'=>1,'msg'=>'sent');
			$log->write("sent" );
			$log->write('return array');
			$log->write($json);
			$this->response->setOutput(json_encode($json));
			
		}
		else
		{
			$log->write("in else ");
            $file_name="gstr1_report_".date('dMy').'.xls';
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=".$file_name);  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			$table='<table><tr><td>Type</td>'
                        . '<td>Place Of Supply</td>'
                        . '<td>Rate</td>'
                        . '<td>Taxable Value</td>'
                        . '<td>Cess Amount</td>'
                        . '<td>E-Commerce GSTIN</td></tr>';
                
            foreach($results as $data)
			{
                $table.='<tr><td>E</td>
                                <td>UP</td>
                                <td>'.$data['_id'].'</td>
                                <td>'.$data['total'].'</td>
                                <td></td>
                                <td></td>
                            </tr>';
                    
            }
            $table.='<table>';
			$log->write($table);
            echo $table;
            exit;
		
		}
    }
	public function gstr1_order_wise()
    {
		$log=new Log("product_sales_report_gstr1-".date('Y-m-d').".log");
		$log->write('GSTR 1 order wise call ');

		unset($this->session->data['filter_date_start']);
		unset($this->session->data['filter_date_end']);
		unset($this->session->data['filter_name_id']);
		unset($this->session->data['store_id']);

        $log->write($this->request->post);

		$mcrypt=new MCrypt();
                        
		$keys = array(
			'filter_date_start',
			'filter_date_end',
			'filter_name_id',
			'store_id',
			'action'
		);
 
		
		foreach ($keys as $key) 		
		{
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
 		}
		if(empty($this->request->post['filter_date_start']))
        {
                    $this->request->post['filter_date_start']=date('Y-m').'01';
        }
        if(empty($this->request->post['filter_date_end']))
        {
                    $this->request->post['filter_date_end']=date('Y-m-d');
        }
		
		$filter_data = array(
                        'filter_store'	     => $this->request->post['store_id'],
			'filter_date_start'	     => $this->request->post['filter_date_start'],
			'filter_date_end'	     => $this->request->post['filter_date_end'],
			'filter_name'           => '',
			'start'=>0,
                                'limit'=>1000
		);
		$log->write($filter_data);
		$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' => ($this->request->post['store_id']),
						'data'        => json_encode($this->request->post),
					);

		$this->model_account_activity->addActivity('GSTR1-Order', $activity_data);
		$this->adminmodel('report/product_sale');
		$this->adminmodel('catalog/product');
		$results = $this->model_report_product_sale->getOrders($filter_data);
        
		if($this->request->post['action']=='e')
		{ 
			$log->write('in if email');
			
			$log->write('in if email');
			$this->load->library('email');
			$email=new email($this->registry);
			
			$file_name="Product_sales_report_".date('dMy').'.csv';
			$fields = array(
				'Sale Date',
				'Store Name',
				'Store ID',
				'Product Name',
				'Quantity',
				'Rate(without tax)',
				'Tax title',
				'Tax rate',
				'Total (Sales + Tax)',
				'Order ID',
				'Discount Type',
				'Discount Value'

			);
			
			
			foreach($results->rows as $data)
    		{
				
				foreach($data['order_product'] as $prd)
				{
					$log->write('in loop');
					if(!empty($data['discount_value']))
					{
						if($data['discount_type']=='P')
						{
							$price_without_tax=((number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', ''))-((number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', ''))*number_format((float)$data['discount_value'], 2, '.', '')/100));
						}
						else if($data['discount_type']=='F')
						{
							$price_without_tax=((number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', ''))-$data['discount_value']);
						}
						else
						{
							$price_without_tax=number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', '');
						}
					}
					else
					{
							$price_without_tax=number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', '');
					}
					//$log->write('product_id'.$prd['product_id']);
					//$log->write('tax_class_name'.$prd['tax_class_name']);
					if(empty($prd['tax_class_name']))
					{
						$prdtax=$this->model_catalog_product->getProduct($prd['product_id']);
						//$log->write('prdtax');
						//$log->write($prdtax);
						$tax_title=$prdtax['tax_class_name'];
					}
					else
					{
						$tax_title=$prd['tax_class_name'];
					}
					
					
					$fdata[]=array(
                        date('Y-m-d',($data['date_added']->sec)),
                        $data['store_name'],
                        $data['store_id'],
						$prd['name'],
						$prd['quantity'],
						number_format((float)($prd['price']), 2, '.', ''),
						$tax_title,
						number_format((float)$prd['tax'], 2, '.', ''),
						number_format((float)($prd['quantity']*(($prd['price'])+$prd['tax'])), 2, '.', ''),
						$data['order_id'],
						$data['discount_type'],
						$data['discount_value']
					);
				}
				
			}
			$log->write($fdata);
			$email->create_csv($file_name,$fields,$fdata);
			
			$mail_subject="GSTR 1 (Product sales) Report from ".$this->request->post['filter_date_start']." to ".$this->request->post['filter_date_end'];
			
			$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear Team,
			<br/><br/>
			Please find attached file for GSTR 1.
			
			<br/><br/>
			This is computer generated email.Please do not reply to this email.
			<br/><br/>
			<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/><br/>
			Thanking you,
			<br/>
			IT Team
			<br/>
			AgriPOS
			
			<br/>
			<br/>
			<span  style='font-size:7.5pt;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
			</p>";
			$to=$this->request->post['store_id'];   
			$cc=array();
			$bcc=array('vipin.kumar@aspltech.com','hrishabh.gupta@unnati.world','chetan.singh@akshamaala.com');
			$file_path=DIR_UPLOAD.$file_name;
            $email->sendmail($mail_subject,$body,$to,$cc,$bcc,$file_path);
			$json=array('status'=>1,'msg'=>'sent');
			$log->write("sent" );
			$log->write('return array');
			$log->write($json);
			$this->response->setOutput(json_encode($json));
			
	
		}
		else
		{
			$file_name="Product_sales_report_".date('dMy').'.xls';
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=".$file_name);  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);

			echo '<table id="example2" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
					<th>Sale Date</th>
                    <th>Store Name</th>
                    <th>Store ID</th>
                    
                    <th>Product Name</th>
                    <th>Quantity</th>
					<th>Rate(without tax)</th>
					<th>Tax title</th>
					<th>Tax rate</th>
                    <th>Total (Sales + Tax)</th>
                    <th>Order ID</th>
					<th>Discount Type</th>
					<th>Discount Value</th>
                </tr>
                </thead>
                <tbody>';
			$tblbody=" ";
			foreach($results->rows as $data)
			{
					if(!empty($data['discount_value']))
					{
						if($data['discount_type']=='P')
						{
							$price_without_tax=((number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', ''))-((number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', ''))*number_format((float)$data['discount_value'], 2, '.', '')/100));
						}
						else if($data['discount_type']=='F')
						{
							$price_without_tax=((number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', ''))-$data['discount_value']);
						}
						else
						{
							$price_without_tax=number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', '');
						}
					}
					else
					{
							$price_without_tax=number_format((float)$data['Total_sales']/$data['qnty'], 2, '.', '');
					}


                    echo  '<tr> 
				<td>'.date('Y-m-d',($data['date_added']->sec)).'</td>
                    <td>'.$data['store_name'].'</td>
                    <td>'.$data['store_id'].'</td>
                    <td>'.$data['order_product'][0]['name'].'</td>
				<td>'.$data['order_product'][0]['quantity'].'</td>
                    <td>'.number_format((float)($data['order_product'][0]['price']), 2, '.', '').'</td>
				<td>'.$data['order_total'][1]['title'].'</td>
                    <td>'.number_format((float)$data['order_product'][0]['tax'], 2, '.', '').'</td>
				<td>'.number_format((float)($data['order_product'][0]['quantity']*(($data['order_product'][0]['price'])+$data['order_product'][0]['tax'])), 2, '.', '').'</td>
                    <td>'.$data['order_id'].'</td>
				<td>'.$data['discount_type'].'</td>
				<td>'.$data['discount_value'].'</td>
                   </tr>';


			}
			echo '</tbody>
			</table>';
			exit;
		}
	}
			
	public function gstr2()
    {
            
		$log=new Log("product_sales_report_gstr2-".date('Y-m-d').".log");
		$log->write('GSTR 2 Call');

		unset($this->session->data['filter_date_start']);
		unset($this->session->data['filter_date_end']);
		unset($this->session->data['filter_name_id']);
		unset($this->session->data['store_id']);

                        $log->write($this->request->post);

		$mcrypt=new MCrypt();
                        
		$keys = array(
			'filter_date_start',
			'filter_date_end',
			'filter_name_id',
			'store_id',
			'action'
		);
 
		
		foreach ($keys as $key) 		
		{
              	    $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
 		}
                if(empty($this->request->post['filter_date_start']))
                {
                    $this->request->post['filter_date_start']=date('Y-m').'01';
                }
                if(empty($this->request->post['filter_date_end']))
                {
                    $this->request->post['filter_date_end']=date('Y-m-d');
                }
		$log->write($this->request->post); 
		$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' => ($this->request->post['store_id']),
						'data'        => json_encode($this->request->post),
					);

		$this->model_account_activity->addActivity('GSTR2', $activity_data);
		$json = array();		
		$data['orders'] = array();
                $filter_data = array(
         			'store_id'	     => $this->request->post['store_id'],
                                'filter_date_start'	     => $this->request->post['filter_date_start'],
                                'filter_date_end'	     => $this->request->post['filter_date_end'],
                                'filter_name'           => $this->request->post['filter_name_id'],
                                'start'=>0,
                                'limit'=>1000
                            );
		$log->write('filter_data'); 
		$log->write($filter_data); 
		
		$this->adminmodel('purchaseorder/purchase_order');
		$log->write('1'); 
		$results = $this->model_purchaseorder_purchase_order->getList($filter_data)->rows;
		$log->write('2'); 
		$log->write($results); 
		if($this->request->post['action']=='e')
		{
			$log->write('in if email');
			$this->load->library('email');
			$email=new email($this->registry);
			$fields = array(
				'Supplier Name',
				'Delivery Address',
				'Product Name',
				'Quantity',
				'Rate (without tax)',
				'Sub Total',
				'Tax title',
				'Purchase Order / Reference ID'

			);
			$file_name="gst2_report_".date('dMy').'.csv';
			foreach($results as $data)
    		{
				foreach($data['order_product'] as $prd)
				{
					$fdata[]=array(
                        $data['supplier_name'],
                        $data['store_name'],
                        $prd['product_name'],
						$prd['quantity'],
						$prd['p_price'],
						($prd['quantity']*$prd['p_price']),
						$prd['p_tax_type'],
                        $data['id_prefix'].$data['sid']
					);
					fputcsv($fileIO,  $fdata,",");
				}
			}
			$log->write('fdata');
			$log->write($fdata);
			$email->create_csv($file_name,$fields,$fdata);
			
			$mail_subject="GSTR 2 Report from ".$this->request->post['filter_date_start']." to ".$this->request->post['filter_date_end'];
			
			$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear Team,
			<br/><br/>
			Please find attached file for GSTR 2.
			
			<br/><br/>
			This is computer generated email.Please do not reply to this email.
			<br/><br/>
			<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/><br/>
			Thanking you,
			<br/>
			IT Team
			<br/>
			AgriPOS
			
			<br/>
			<br/>
			<span  style='font-size:7.5pt;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
			</p>";
			$to=$this->request->post['store_id'];   
			$cc=array();
			$bcc=array('vipin.kumar@aspltech.com','hrishabh.gupta@unnati.world','chetan.singh@akshamaala.com');
			
			$file_path=DIR_UPLOAD.$file_name;
            $email->sendmail($mail_subject,$body,$to,$cc,$bcc,$file_path);
			$json=array('status'=>1,'msg'=>'sent');
			$log->write("sent" );
			$log->write('return array');
			$log->write($json);
			$this->response->setOutput(json_encode($json));
			
		}
		else
		{
			$log->write('in else of email');
			$file_name="gst2_report_".date('dMy').'.xls';
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=".$file_name);  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
		
            $table='<table><tr><td>Supplier Name</td>'
                        . '<td>Delivery Address</td>'
                        . '<td>Product Name</td>'
                        . '<td>Quantity</td>'
                        . '<td>Rate (without tax)</td>'
                        . '<td>Sub Total</td>
						
						<td>Tax title</td>
						<td>Purchase Order / Reference ID</td>
						</tr>';
                
            foreach($results as $data)
    		{
                    //print_r($data);
                    $table.='<tr><td>'.$data['supplier_name'].'</td>
                                <td>'.$data['store_name'].'</td>
                                <td>'.$data['product_name'].'</td>
                                <td>'.$data['Quantity'].'</td>
                                <td>'.$data['rate'].'</td>
                                <td>'.$data['amount'].'</td>
								<td>'.$data['tax_type'].'</td>
								
								<td>'.$data['id_prefix'].$data['sid'].'</td>
                            </tr>';
                    
            }
            $table.='<table>';
            echo $table;
            exit;
		}
	}
}

?>