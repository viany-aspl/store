<?php
class Controllermpossale extends Controller {


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
    public function mysale()
    {
        $log=new Log("mysale-".date('Y-m-d').".log");
		$mcrypt=new MCrypt();
		$log->write($this->request->post);
		$log->write($this->request->get);
		$uid=$mcrypt->decrypt($this->request->post['username']);
		$sid=$mcrypt->decrypt($this->request->post['store_id']); 
		if(isset($this->request->get['sdate']))
        {
            $sdate=$mcrypt->decrypt($this->request->get['sdate']);
            $edate=$mcrypt->decrypt($this->request->get['edate']);
		}
		else
        {
            $sdate=date('Y-m-'.'01');
            $edate=date('Y-m-d');
		}
		$log->write($edate);
        $this->load->model('account/customer');
		$jsons = $this->model_account_customer->getsale($uid,$sdate,$edate);
        
		$this->request->post['action']=$mcrypt->decrypt( $this->request->post['action']);
		if($this->request->post['action']=='e')
		{ 
			$this->load->library('email');
			$email=new email($this->registry);
			$file_name="my-sales-".date('dMy').'.csv';
			$fields = array(
				'ID',
				'Product Name',
				'Price',
				'quantity'
				);
			foreach ($jsons as $ids) 
			{	
				$temp_price=0;
				foreach($ids['paytype'] as $paytype)
				{
					$temp_price=($temp_price+($paytype['Price']+$paytype['Tax'])*$paytype['Qnty']);
			
				}
				if((is_array($ids)) && (!empty($ids)))
				{
					$fdata[] = array(
									'id'       =>($ids['_id']),
									'name'       =>($ids['product_name']),
									'pirce' =>($temp_price),
									'quantity' => ($ids['quantity'])
								);
                
				}
			}
			$email->create_csv($file_name,$fields,$fdata);
			$mail_subject="My Sales Report from ".$sdate." to ".$edate;
			
			$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear Team,
			<br/><br/>
			Please find attached file for My Sales Report.
			
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
			$to=$sid;   
			$cc=array();
			$bcc=unserialize(MAIL_BCC);
			$file_path=DIR_UPLOAD.$file_name;
            $email->sendmail($mail_subject,$body,$to,$cc,$bcc,$file_path);
			
			$json=array('status'=>1,'msg'=>'sent');
			$log->write("sent" );
			$log->write('return array');
			$log->write($json);
			$data['products'][]=$json;
			$this->response->setOutput(json_encode($data));
		}
		else
		{
			$totalvalue=0;
			foreach ($jsons as $ids) 
			{	
				$log->write('in foreach loop');
				$log->write($ids['paytype']);  
			
				$temp_price=0;
				foreach($ids['paytype'] as $paytype)
				{
					$temp_price=($temp_price+($paytype['Price']+$paytype['Tax'])*$paytype['Qnty']);
			
				}
				$log->write($temp_price);
				if((is_array($ids)) && (!empty($ids)))
				{
					$log->write('in if');
					$json['products'][] = array(
									'id'       =>$mcrypt->encrypt($ids['_id']),
									'name'       =>$mcrypt->encrypt($ids['product_name']),
									'pirce' =>$mcrypt->encrypt($temp_price),
									'quantity' => $mcrypt->encrypt($ids['quantity']),
								);
					$totalvalue=$totalvalue+($temp_price);
				}
			}
	
			$json['total']=$mcrypt->encrypt(round($totalvalue,2,PHP_ROUND_HALF_UP));			  
			$log->write("return value");
			$log->write($json);
       
			$this->response->setOutput(json_encode($json));
		}

    }
    public function gettodaysales()
    {  
 
		$log=new Log("today-".date('Y-m-d').".log");
		$log->write($_POST); 
		$log->write($_GET); 
		$mcrypt=new MCrypt();
		
		$store_id =$mcrypt->decrypt($_POST['store_id']);
		if(isset($this->request->get['sdate']))
                {
                    $sdate=$mcrypt->decrypt($this->request->get['sdate']);
                    $edate=$mcrypt->decrypt($this->request->get['edate']);
                }
                else
                {
                    $sdate=date('Y-m-d');
                    $edate=date('Y-m-d');
                }
              
		$this->adminmodel('sale/order');
		$results=$this->model_sale_order->gettodaysales_cash_tageed_subsidy($sdate,$edate,$store_id); 
        
		$this->request->post['action']=$mcrypt->decrypt( $this->request->post['action']);
		if($this->request->post['action']=='e')
		{ 
			$this->load->library('email');
			$email=new email($this->registry);
			$file_name="Today-sales-".date('dMy').'.csv';
			$fields = array(
				'Cash',
				'Credit',
				'Discount',
				'Total'
				);
			$fdata[]=array($results['cash'],$results['credit'],$results['discount'],$results['total']);
			
			$email->create_csv($file_name,$fields,$fdata);
			$mail_subject="Today Sales Report from ".$sdate." to ".$edate;
			
			$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear Team,
			<br/><br/>
			Please find attached file for Today Sales Report.
			
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
			$to=$store_id;   
			$cc=array();
			$bcc=unserialize(MAIL_BCC);
			$file_path=DIR_UPLOAD.$file_name;
            $email->sendmail($mail_subject,$body,$to,$cc,$bcc,$file_path);
			
			$json=array('status'=>1,'msg'=>'sent');
			$log->write("sent" );
			$log->write('return array');
			$log->write($json);
			$data['products'][]=$json;
			$this->response->setOutput(json_encode($data));
		}
		else
		{
			if(!empty($store_id))
			{
                $datas['products'][] = array(
					'name'=>$mcrypt->encrypt("Cash"),
					'price'        =>$mcrypt->encrypt($results['cash'])											
                    );
                $datas['products'][] = array(
					'name'=>$mcrypt->encrypt("Credit"),
					'price'        =>$mcrypt->encrypt($results['credit'])											
                    );
				$datas['products'][] = array(
					'name'=>$mcrypt->encrypt("Discount"),
					'price'        =>$mcrypt->encrypt($results['discount'])									
                    );
                $datas['total']=$mcrypt->encrypt($results['total']);
			}
			else
			{ 
                $datas['products'][] = array(
					'name'=>$mcrypt->encrypt("Cash"),
					'price'        =>''											
                    );
                $datas['products'][] = array(
					'name'=>$mcrypt->encrypt("Credit"),
					'price'        =>''											
                    );
				$datas['products'][] = array(
					'name'=>$mcrypt->encrypt("Discount"),
					'price'        =>''											
                    );
                $datas['total']='';
			}
			$log->write($datas); 
			$this->response->setOutput(json_encode($datas));
		}
    }
	public function gettodaysalesdtl()
	{  
		$log=new Log("gettodaysalesdtls-".date('Y-m-d').".log");
		$log->write($_POST); 
		$log->write($_GET); 
		$mcrypt=new MCrypt();
		
		$store_id = $mcrypt->decrypt($_POST['store_id']);
		if(isset($this->request->get['sdate']))
		{
			$sdate=$mcrypt->decrypt($this->request->get['sdate']);
		}
		else
		{
		
		}
		if(isset($this->request->get['edate'])){
			$edate=$mcrypt->decrypt($this->request->get['edate']);
		}
		else
		{
			
		}
      
		$this->adminmodel('sale/order');
		$results=$this->model_sale_order->gettodaysalesdtl($sdate,$edate,$store_id); 
        $this->request->post['action']=$mcrypt->decrypt( $this->request->post['action']);
		if($this->request->post['action']=='e')
		{ 
			$email=new email($this->registry);
			$file_name="today-transaction".date('dMy').'.csv';
			$fields = array(
				'Customer Name',
				'Customer Telephone',
				'Order id',
				'Invoice number',
				'Order Date',
				'Total'
				);
			foreach ($results as $result) 
			{
				$fdata[] = array(
				$result['firstname'],
				$result['telephone'],				
				$result['order_id'],	
				$result['invoice_prefix'].'-'.$result['invoice_no'],	
				date('Y-m-d',$result['date_added']->sec),
				$this->currency->format($result['total'], $this->config->get('config_currency'))				
				);
			}
			$email->create_csv($file_name,$fields,$fdata);
			$mail_subject="Today Transaction Report from ".$sdate." to ".$edate;
			
			$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear Team,
			<br/><br/>
			Please find attached file for Today Sales Report.
			
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
			$to=$store_id;   
			$cc=array();
			$bcc=unserialize(MAIL_BCC);
			$file_path=DIR_UPLOAD.$file_name;
            $email->sendmail($mail_subject,$body,$to,$cc,$bcc,$file_path);
			
			$json=array('status'=>1,'msg'=>'sent');
			$log->write("sent" );
			$log->write('return array');
			$log->write($json);
			$data['saledtl'][]=$json;
			$this->response->setOutput(json_encode($data));
		}
		else
		{
			foreach ($results as $result) 
			{
				$data['saledtl'][] = array(
				'fname'       => $mcrypt->encrypt($result['firstname']),
				'telephone'          => $mcrypt->encrypt($result['telephone']),				
				'order_id'         => $mcrypt->encrypt($result['order_id']),	
				'invoice_no'         => $mcrypt->encrypt($result['invoice_prefix'].'-'.$result['invoice_no']),	
				'date_added'         => $mcrypt->encrypt(date('Y-m-d',$result['date_added']->sec)),
				'total'          =>$mcrypt->encrypt( $this->currency->format($result['total'], $this->config->get('config_currency')))				
				);
			}
			$this->response->setOutput(json_encode($data));
		}
	}
	public function gettodayproductsalesdtl()
		{  
 
		$log=new Log("gettodayproductsalesdtl-".date('Y-m-d').".log");
		$log->write($_POST); 
		$log->write($_GET); 
		$mcrypt=new MCrypt();
		
		
	
	if(isset($this->request->post['order_id'])){
			$orderid=$mcrypt->decrypt($this->request->post['order_id']);
		}
	else{
		$orderid=1685;
		$store_id=170;
	}
      
		$this->adminmodel('sale/order');
		
		$results=$this->model_sale_order->gettodayproductsalesdtl($sdate,$edate,$orderid,$store_id); 
        //$log->write($results);  
		//print_r($results);
		
		
		foreach ($results as $result) 
		{
                    //$result=$result[0];
                       //$log->write($result); 
                foreach ($result['order_product'] as $order_product) 
				{ 
                    $log->write($order_product); 
					if(empty($order_product['reward']))
					{
						$order_product['reward']=0;
					}
					$data['saleproductdtl'][] = array(
						'name'       => $mcrypt->encrypt($order_product['name']),
						'quantity'          => $mcrypt->encrypt($order_product['quantity']),
						'reward'=>$mcrypt->encrypt(($order_product['reward']*$order_product['quantity'])),
						'price'          =>$mcrypt->encrypt( ($order_product['price']+$order_product['tax'])),
						//'price'          =>$mcrypt->encrypt( $this->currency->format(($order_product['price']+$order_product['tax']), $this->config->get('config_currency'))),
						'pricetest'          =>($order_product['price']+$order_product['tax'])				
						);
				}
		}
		$log->write($data);  
		$this->response->setOutput(json_encode($data));

		}
		
		

public function mysaletag()
{

		$log=new Log("mysale-tag-".date('Y-m-d').".log");
		$mcrypt=new MCrypt(); 
		$log->write($this->request->post);
		$log->write($this->request->get);
			$uid=$mcrypt->decrypt($this->request->post['username']);
			$sid=$mcrypt->decrypt($this->request->post['store_id']);
		if(isset($this->request->get['sdate']))
		{
			$sdate=$mcrypt->decrypt($this->request->get['sdate']);
		}
	else{
	}
             		$this->load->model('account/customer');
	$jsons = $this->model_account_customer->getsaleTagged($uid,$sdate,$sid);
		$log->write($jsons);

	$log->write("Total check");

	if(empty($sdate))
	{
		$sdate=date('Y-m-d');
	}
	
	$total_tagged_sale=round($this->model_account_customer->getStoreSaleTagged($uid,$sdate,$sid)["total"],2,PHP_ROUND_HALF_UP);
	$json['total']=$mcrypt->encrypt($total_tagged_sale);			  
		$log->write(round($total_tagged_sale));
	foreach ($jsons as $ids) {		
	$json['products'][] = array(
                        'id'       =>$mcrypt->encrypt($ids['product_id']), 
                        'name'       =>$mcrypt->encrypt($ids['name']),
			'pirce' =>$mcrypt->encrypt( $ids['price']+$ids['tax']),
			'quantity' => $mcrypt->encrypt($ids['quantity']),
						
                                            );
	}

		$this->response->setOutput(json_encode($json));

}


function customersale()
{

		$log=new Log("customersale-".date('Y-m-d').".log");
		$mcrypt=new MCrypt();
		$log->write($this->request->post);
		$log->write($this->request->get);
			$uid=$mcrypt->decrypt($this->request->post['username']);
			$sid=$mcrypt->decrypt($this->request->post['store_id']);

		if (isset($this->request->get['sdate'])) {
			$filter_date_start = $this->request->get['sdate'];
		} else {
			$filter_date_start = '';
		}

		if (isset($this->request->get['edate'])) {
			$filter_date_end = $this->request->get['edate'];
		} else {
			$filter_date_end = '';
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = 5;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}


		$this->adminmodel('report/customer');

		$data['products'] = array();

		$filter_data = array(
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_order_status_id' => $filter_order_status_id,
			'filter_store'		=> $sid,	
			'start'                  => $mcrypt->decrypt($this->request->post['start']),
			'limit'                  => $this->config->get('config_limit_admin')
		);


		$results = $this->model_report_customer->getOrders($filter_data);

		foreach ($results as $result) {
			$data['products'][] = array(
				'id'       => $mcrypt->encrypt($result['customer']),
				'name'          => $mcrypt->encrypt($result['email']),				
				'pirce'         => $mcrypt->encrypt($result['orders']),
				'quantity'       => $mcrypt->encrypt($result['products']),
				'total'          =>$mcrypt->encrypt( $this->currency->format($result['total'], $this->config->get('config_currency')))				
			);
		}
		$this->response->setOutput(json_encode($data));

}



public function mysalesub()
{

		$log=new Log("mysale.log");
		$mcrypt=new MCrypt();
		$log->write($this->request->post);
		$log->write($this->request->get);
			$uid=$mcrypt->decrypt($this->request->post['username']);
			$sid=$mcrypt->decrypt($this->request->post['store_id']);
		if(isset($this->request->get['sdate'])){
			$sdate=$mcrypt->decrypt($this->request->get['sdate']);
	}
	else{
	}
             		$this->load->model('account/customer');
	$jsons = $this->model_account_customer->getsaleSub($uid,$sdate);
		$log->write($jsons);

	$log->write("Total check");

	if(empty($sdate))
	{
		$sdate=date('Y-m-d');
	}

	$json['total']=$mcrypt->encrypt(round($this->model_account_customer->getUserSaleSub($uid,$sdate)["total"],2,PHP_ROUND_HALF_UP));			  
	foreach ($jsons as $ids) {		
	$json['products'][] = array(
                        'id'       =>$mcrypt->encrypt($ids['product_id']),
                        'name'       =>$mcrypt->encrypt($ids['name']),
			'pirce' =>$mcrypt->encrypt( $ids['price']+$ids['tax']),
			'quantity' => $mcrypt->encrypt($ids['quantity']),
						
                                            );
	}


		$this->response->setOutput(json_encode($json));

}


public function mysalechq()
{

		$log=new Log("mysale.log");
		$mcrypt=new MCrypt();
		$log->write($this->request->post);
		$log->write($this->request->get);
			$uid=$mcrypt->decrypt($this->request->post['username']);
			$sid=$mcrypt->decrypt($this->request->post['store_id']);
		if(isset($this->request->get['sdate'])){
			$sdate=$mcrypt->decrypt($this->request->get['sdate']);
	}
	else{
	}
             		$this->load->model('account/customer');
	$jsons = $this->model_account_customer->getsaleChq($uid,$sdate);
		$log->write($jsons);

	$log->write("Total check");

	if(empty($sdate))
	{
		$sdate=date('Y-m-d');
	}

	$json['total']=$mcrypt->encrypt(round($this->model_account_customer->getUserSaleChq($uid,$sdate)["total"],2,PHP_ROUND_HALF_UP));			  
	foreach ($jsons as $ids) {		
	$json['products'][] = array(
                        'id'       =>$mcrypt->encrypt($ids['product_id']),
                        'name'       =>$mcrypt->encrypt($ids['name']),
			'pirce' =>$mcrypt->encrypt( $ids['price']+$ids['tax']),
			'quantity' => $mcrypt->encrypt($ids['quantity']),
						
                                            );
	}


		$this->response->setOutput(json_encode($json));

}


public function getcontractor_transactions()
		{  
 
		$log=new Log("contractor-".date('Y-m-d').".log");
		$log->write($_POST); 

		 $mcrypt=new MCrypt();
		$circle_code = $mcrypt->decrypt($_POST['circle_code']);
		$cr_dr_1 = $mcrypt->decrypt($_POST['cr_dr']);	
		if($cr_dr_1=="1") { $cr_dr="cr"; } elseif($cr_dr_1=="2") { $cr_dr="dr"; }else { $cr_dr=""; } 
		$limit_start = $mcrypt->decrypt($_POST['limit_start']);	
		$limit_end = $mcrypt->decrypt($_POST['limit_end']);
		$this->adminmodel('stock/purchase_order');
		$results=$this->model_stock_purchase_order->getcontractor_transactions($circle_code,$cr_dr,$limit_start,$limit_end); 
                            $log->write($results);  
		$totals=0;
		foreach ($results as $result) {
			$datas['products'][] = array(
				
				'product_id'        =>$mcrypt->encrypt( $result['product_id']),
				'order_id'      =>$mcrypt->encrypt( $result['order_id']),
				'name'      =>$mcrypt->encrypt( $result['name']),
				'quantity'        =>$mcrypt->encrypt( $result['quantity']),
				'pirce'		=>$mcrypt->encrypt( str_replace("Rs.","",$result['price'])/$result['quantity']),
				'tax'		=>$mcrypt->encrypt( $result['tax']),
				'total'		=>$mcrypt->encrypt(str_replace("Rs.","",$result['price'])),
				'date'		=>$mcrypt->encrypt(date('d/m/Y',strtotime($result['crdate'])))  
											
			);
		$totals=$totals+(str_replace("Rs.","",$result['price']));  
		}
		$datas["total"]=$mcrypt->encrypt($totals); //$mcrypt->encrypt('123');
		 $log->write($datas); 
		$this->response->setOutput(json_encode($datas));

		}


            
		
		

}