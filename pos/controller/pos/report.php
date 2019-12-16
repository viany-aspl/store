<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
error_reporting(0);

class ControllerPosReport extends Controller {
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
    public function index() 
    {
		$this->load->language('report/Inventory_report');
		$this->load->language('common/information');
		$data['tool_tip']=$this->language->get('inventory/report');

		$data['tool_tip_style']=$this->language->get('tool_tip_style');
		$data['tool_tip_class']=$this->language->get('tool_tip_class');

		$this->document->setTitle($this->language->get('heading_title'));

               
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
                
                
                if (isset($this->request->get['filter_date_start'])) {
			$sdate = $this->request->get['filter_date_start'];
		} else {
			$sdate=date('Y-m').'01';//"2018-08-08";
		}

		if (isset($this->request->get['filter_date_end'])) {
			$edate = $this->request->get['filter_date_end'];
		} else {
			$edate=date('Y-m-d');//"2018-08-10";
		}
		$this->adminmodel('pos/Inventory');
		$data['orders'] = array();
 
                $uid=$this->user->getId();
                
                $results1= $this->model_pos_Inventory->getsale($uid,$sdate,$edate);
      
                $results=$results1->rows;
                //print_r($results);
                $order_total=$results1->num_rows;
                foreach ($results as $sale) 
                {
					$temp_price=0;
					foreach($sale['paytype'] as $paytype)
					{
						$temp_price=($temp_price+($paytype['Price']+$paytype['Tax'])*$paytype['Qnty']);
				
					}
                    $data['sale'][]= array(
									'product_id' => $sale['_id'],
									'product_name' => $sale['product_name'],
									'quantity'      =>  $sale['quantity'],
									'price'      =>  $temp_price
								);
					$totalvalue=$totalvalue+($temp_price);
				}    
                //print_r($data['sale']);exit;
                $url = '';
                if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}
                
                if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		if (isset($this->request->get['pagetittle'])) 
		{
			$url .= '&pagetittle=' . $this->request->get['pagetittle'];
		}
		$data['total']=$totalvalue;


		$data['token'] = $this->session->data['token'];

                if ($this->request->get['filter_store']!="") {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('pos/inventory_report', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $sdate;
		$data['filter_date_end'] = $edate;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('default/template/pos/sale_by_product.tpl', $data));
    }
	public function sale_by_product_email() 
    {
            if (isset($this->request->get['filter_date_start'])) 
			{
				$sdate = $this->request->get['filter_date_start'];
			} 
			else 
			{
				$sdate=date('Y-m').'01';//"2018-08-08";
			}
			if (isset($this->request->get['filter_date_end'])) 
			{
				$edate = $this->request->get['filter_date_end'];
			} 
			else 
			{
				$edate=date('Y-m-d');//"2018-08-10";
			}
			$mcrypt=new MCrypt();
			$fields_string='';
			$strid=$this->user->getStoreId();
			$username=$this->user->getId();
			$fields_string .= 'store_id'.'='.$mcrypt->encrypt($strid).'&'; 
			$fields_string .= 'sdate'.'='.$mcrypt->encrypt($sdate).'&'; 
			$fields_string .= 'edate'.'='.$mcrypt->encrypt($edate).'&'; 
			$fields_string .= 'username'.'='.$mcrypt->encrypt($username).'&';
			$fields_string .= 'action'.'='.$mcrypt->encrypt('e').'&'; 
			$request = HTTP_CATALOG."index.php?route=mpos/sale/mysale";
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
	public function sale_book_email() 
    {
            if (isset($this->request->get['filter_date_start'])) 
			{
				$sdate = $this->request->get['filter_date_start'];
			} 
			else 
			{
				$sdate=date('Y-m').'01';//"2018-08-08";
			}
			if (isset($this->request->get['filter_date_end'])) 
			{
				$edate = $this->request->get['filter_date_end'];
			} 
			else 
			{
				$edate=date('Y-m-d');//"2018-08-10";
			}
			$mcrypt=new MCrypt();
			$fields_string='';
			$strid=$this->user->getStoreId();
		
			$fields_string .= 'store_id'.'='.$mcrypt->encrypt($strid).'&'; 
			$fields_string .= 'sdate'.'='.$mcrypt->encrypt($sdate).'&'; 
			$fields_string .= 'edate'.'='.$mcrypt->encrypt($edate).'&'; 
			$fields_string .= 'action'.'='.$mcrypt->encrypt('e').'&'; 
			$request = HTTP_CATALOG."index.php?route=mpos/sale/gettodaysales";
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
    public function sale_book() 
    {
        $this->load->language('report/Inventory_report');
        if (isset($this->request->get['filter_date_start'])) 
		{
			$sdate = $this->request->get['filter_date_start'];
		} 
		else 
		{
			$sdate=date('Y-m').'01';//"2018-08-08";
		}

		if (isset($this->request->get['filter_date_end'])) 
		{
			$edate = $this->request->get['filter_date_end'];
		} 
		else 
		{
			$edate=date('Y-m-d');//"2018-08-10";
		}
        $data['s_book'] = array();
        $this->adminmodel('pos/Inventory');
        $store_id=(int)$this->user->getStoreId();
        $results=$this->model_pos_Inventory->gettodaysales_cash_tageed_subsidy($sdate,$edate,$store_id); 
        $data['amount']= array(
				'discount'        =>($results['discount']),
				'cash'        =>($results['cash']),
				'credit'        =>($results['credit'])											
                    );
		$data['total']=($results['total']);
        $url = '';
				
		if (isset($this->request->get['filter_date_start'])) 
		{
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) 
		{
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}
		if (isset($this->request->get['pagetittle'])) 
		{
			$url .= '&pagetittle=' . $this->request->get['pagetittle'];
		}
        $data['filter_date_start'] = $sdate;
		$data['filter_date_end'] = $edate;
        $data['token'] = $this->session->data['token'];
                
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');   
        $this->response->setOutput($this->load->view('default/template/pos/sale_book.tpl', $data));   
    }
	public function update_price() 
    {
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_all_status'] = $this->language->get('text_all_status');

		$data['column_date_start'] = $this->language->get('column_date_start');
		$data['column_date_end'] = $this->language->get('column_date_end');
		$data['column_title'] = $this->language->get('column_title');
		$data['column_orders'] = $this->language->get('column_orders');
		$data['column_total'] = $this->language->get('column_total');

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_group'] = $this->language->get('entry_group');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_filter'] = $this->language->get('button_filter');
        $data['token'] = $this->session->data['token'];
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');   
        $this->response->setOutput($this->load->view('default/template/pos/update_price.tpl', $data));   
    }
    public function gst() 
    { 
        $data['token'] = $this->session->data['token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');   
        $this->response->setOutput($this->load->view('default/template/pos/gst.tpl', $data));   
    }
    public function download_gstr1()
    {
        $log=new Log("product_sales_report_gstr1".date('Y-m-d').".log");
		$log->write('GSTR 1 Call');

		unset($this->session->data['filter_date_start']);
		unset($this->session->data['filter_date_end']);
		unset($this->session->data['filter_name_id']);
		unset($this->session->data['store_id']);
		$log->write($this->request->post);

		$mcrypt=new MCrypt();
                        
		if(empty($this->request->post['filter_date_start']))
        {
            $this->request->post['filter_date_start']=date('Y-m').'01';
        }
        if(empty($this->request->post['filter_date_end']))
        {
            $this->request->post['filter_date_end']=date('Y-m-d');
        }
		$log->write($this->request->post);  	
		$json = array();		
		$data['orders'] = array();
                $filter_data = array(
         			'filter_store'	     => $this->user->getStoreId(),//$this->request->post['store_id'],
                                'filter_date_start'	     => $this->request->post['filter_date_start'],
                                'filter_date_end'	     => $this->request->post['filter_date_end'],
                                'filter_name'           => $this->request->post['filter_name_id'],
                                'start'=>0,
                                'limit'=>1000
                            );
		$log->write($filter_data); 
                
		$this->adminmodel('report/product_sale');
		$results = $this->model_report_product_sale->exgetOrders($filter_data);
                $file_name="Product_sales_report_".date('dMy').'.xls';
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
                    //print_r($data['paytype'][0]['tax']);
                    $table.='<tr><td>E</td>
                                <td>UP</td>
                                <td>'.$data['_id'].'</td>
                                <td>'.$data['paytype'][0]['tax'].'</td>
                                <td></td>
                                <td></td>
                            </tr>';
                    
                }
                $table.='<table>';
                echo $table;
                exit;
		
        }
	public function email_gstr1()  
    {
        if (isset($this->request->get['filter_date_start'])) 
		{
			$filter_date_start = $this->request->get['filter_date_start'];
		} 
		if (isset($this->request->get['filter_date_end'])) 
		{
			$filter_date_end = $this->request->get['filter_date_end'];
		} 
		
			$mcrypt=new MCrypt();
			$fields_string='';
			$strid=$this->user->getStoreId();
			$username=$this->user->getId();
			$log =new Log("prdinv-".date('Y-m-d').".log");

			$log->write($strid);
			$log->write($username);
			
			$fields_string .= 'store_id'.'='.$mcrypt->encrypt($strid).'&'; 
			
			$fields_string .= 'username'.'='.$mcrypt->encrypt($username).'&';
			$fields_string .= 'filter_date_start'.'='.$mcrypt->encrypt($filter_date_start).'&';
			$fields_string .= 'filter_date_end'.'='.$mcrypt->encrypt($filter_date_end).'&';
			$fields_string .= 'action'.'='.$mcrypt->encrypt('e').'&'; 
			$request = HTTP_CATALOG."index.php?route=mpos/report/gstr1";
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
    public function download_gstrorder_wise()
    {
        $log=new Log("product_sales_report_gstr1".date('Y-m-d').".log");
		$log->write('GSTR 1 Call');

                //echo $this->user->getStoreId();exit;
		$mcrypt=new MCrypt();
                        
		if(empty($this->request->post['filter_date_start']))
        {
            $this->request->post['filter_date_start']=date('Y-m').'01';
        }
        if(empty($this->request->post['filter_date_end']))
        {
            $this->request->post['filter_date_end']=date('Y-m-d');
        }
		$this->adminmodel('report/product_sale');
                
		$data['orders'] = array();

		$filter_data = array(
                        'filter_store'	     => $this->user->getStoreId(),
			'filter_date_start'	     => $this->request->post['filter_date_start'],
			'filter_date_end'	     => $this->request->post['filter_date_end'],
			'filter_name'           => ''
		);
		$results = $this->model_report_product_sale->getOrders($filter_data);
        //print_r($results);exit;
		
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
		foreach($results as $data)
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
			<td>'.date('Y-m-d',strtotime($data['date_added']->sec)).'</td>
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
	public function email_gstr_order_wise()  
    {
        if (isset($this->request->get['filter_date_start'])) 
		{
			$filter_date_start = $this->request->get['filter_date_start'];
		} 
		if (isset($this->request->get['filter_date_end'])) 
		{
			$filter_date_end = $this->request->get['filter_date_end'];
		} 
		
			$mcrypt=new MCrypt();
			$fields_string='';
			$strid=$this->user->getStoreId();
			$username=$this->user->getId();
			$log =new Log("prdinv-".date('Y-m-d').".log");

			$log->write($strid);
			$log->write($username);
			
			$fields_string .= 'store_id'.'='.$mcrypt->encrypt($strid).'&'; 
			
			$fields_string .= 'username'.'='.$mcrypt->encrypt($username).'&';
			$fields_string .= 'filter_date_start'.'='.$mcrypt->encrypt($filter_date_start).'&';
			$fields_string .= 'filter_date_end'.'='.$mcrypt->encrypt($filter_date_end).'&';
			$fields_string .= 'action'.'='.$mcrypt->encrypt('e').'&'; 
			$request = HTTP_CATALOG."index.php?route=mpos/report/gstr1_order_wise";
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
	public function download_gstr2()
    {
        $log=new Log("product_sales_report_gstr2".date('Y-m-d').".log");
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
			'store_id'
		);
 
        if(empty($this->request->post['filter_date_start']))
        {
            $this->request->post['filter_date_start']=date('Y-m').'01';
        }
        if(empty($this->request->post['filter_date_end']))
        {
            $this->request->post['filter_date_end']=date('Y-m-d');
        }
		$log->write($this->request->post);  	
		$json = array();		
		$data['orders'] = array();
                $filter_data = array(
         			'filter_store'	     => $this->user->getStoreId(),//$this->request->post['store_id'],
                                'filter_date_start'	     => $this->request->post['filter_date_start'],
                                'filter_date_end'	     => $this->request->post['filter_date_end'],
                                'filter_name'           => $this->request->post['filter_name_id'],
								
                                'start'=>0,
                                'limit'=>1000
                            );
		$log->write($filter_data); 
                
		$this->adminmodel('purchaseorder/purchase_order');
		$results = $this->model_purchaseorder_purchase_order->getList($filter_data)->rows;
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
	public function email_gstr2()  
    {
        if (isset($this->request->get['filter_date_start'])) 
		{
			$filter_date_start = $this->request->get['filter_date_start'];
		} 
		if (isset($this->request->get['filter_date_end'])) 
		{
			$filter_date_end = $this->request->get['filter_date_end'];
		} 
		
			$mcrypt=new MCrypt();
			$fields_string='';
			$strid=$this->user->getStoreId();
			$username=$this->user->getId();
			$log =new Log("prdinv-".date('Y-m-d').".log");

			$log->write($strid);
			$log->write($username);
			
			$fields_string .= 'store_id'.'='.$mcrypt->encrypt($strid).'&'; 
			
			$fields_string .= 'username'.'='.$mcrypt->encrypt($username).'&';
			$fields_string .= 'filter_date_start'.'='.$mcrypt->encrypt($filter_date_start).'&';
			$fields_string .= 'filter_date_end'.'='.$mcrypt->encrypt($filter_date_end).'&';
			$fields_string .= 'action'.'='.$mcrypt->encrypt('e').'&'; 
			$request = HTTP_CATALOG."index.php?route=mpos/report/gstr2";
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
    public function premium_farmer() 
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
               
                
		if (isset($this->request->get['page'])) 
		{
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
		$pagination->url = $this->url->link('pos/report/premium_farmer', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

                $this->response->setOutput($this->load->view('default/template/pos/premium_farmer.tpl', $data));   
        }
		public function premium_farmer_email()  
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
			$fields_string .= 'type'.'='.$mcrypt->encrypt('').'&'; 
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
        public function customer_trans() 
        {
                $this->load->language('report/Inventory_report');
            
                if (isset($this->request->get['filter_date_start'])) {
			$sdate = $this->request->get['filter_date_start'];
		} else {
			$sdate=date('Y-m').'01';//"2018-08-08";
		}

		if (isset($this->request->get['filter_date_end'])) {
			$edate = $this->request->get['filter_date_end'];
		} else {
			$edate=date('Y-m-d');//"2018-08-10";
		}
             
                $data['s_book'] = array();
                $this->adminmodel('sale/order');
                
                $store_id=(int)$this->user->getStoreId();
                
                $results=$this->model_sale_order->getcustomerpurchaseproductdtl($this->request->get['mobile_number'],$store_id); 
                $data['results']=$results->rows;
                
		 
                $data['total']=(int)($results->num_rows);
                
                
                $url = '';
				
		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}
              
                $data['filter_date_start'] = $sdate;
		$data['filter_date_end'] = $edate;
        if (isset($this->request->get['pagetittle'])) 
		{
			$url .= '&pagetittle=' . $this->request->get['pagetittle'];
		}        
                $data['token'] = $this->session->data['token'];
                
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer'); 
                $data['mobile_number']=$this->request->get['mobile_number'];
                $data['aadhar']=$this->request->get['aadhar'];
                $data['name']=$this->request->get['name'];
                $data['credit']=$this->request->get['credit'];
                $data['return_url']=$_SERVER['HTTP_REFERER'];
                $this->response->setOutput($this->load->view('default/template/pos/customer_trans.tpl', $data));   
            }
            public function orderInfo()
            {
                $order_id=$this->request->get['order_id'];
                $this->adminmodel('sale/order');
                $order_info = $this->model_sale_order->getOrder($order_id);
               
		$results= $order_info['products'];
		$results2=$order_info['totals'];
		foreach ($results as $result) {//print_r($result); 
                    $data['orders'][] = array(
				'name' => $result['name'],
				'quantity'   => $result['quantity'],
				'price'      => $result['price'],
				'tax'     => $result['tax'],  
				'total'      => $result['total']
			);
		}
                
		$data['order_total']=$order_info['total'];
		$data['order_cash']=$order_info['cash'];
		$data['order_credit']=$order_info['credit'];
		
		$data['order_date']=date('d-m-Y',$order_info['date_added']->sec);
                $this->response->setOutput(json_encode($data));
	
		
            }
	public function customer_credit_trans() 
            {
                $this->load->language('report/Inventory_report');
            
                if (isset($this->request->get['filter_date_start'])) {
			$sdate = $this->request->get['filter_date_start'];
		} else {
			$sdate=date('Y-m').'01';//"2018-08-08";
		}

		if (isset($this->request->get['filter_date_end'])) {
			$edate = $this->request->get['filter_date_end'];
		} else {
			$edate=date('Y-m-d');//"2018-08-10";
		}
             

		     
                $data['s_book'] = array();
                $this->adminmodel('setting/store');
                
                $store_id=(int)$this->user->getStoreId();
                
                $results=$this->model_setting_store->getpaidcredit($this->request->get['customer_id']); 
				
                //$data['results']=$results->rows;
                foreach ($results->rows as $ids) 
				{	

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
					else
					{
						$ids['transtype']='Unknown';
					}
				
					$data['results'][] = array(
                        'credit_amount' => ($ids['cash']),
			'discount'=>  ($ids['discount']), 
			'invoice_no'=> ($ids['order_id']),			
                        'dat'       =>(date('d-m-Y', ($ids['create_time']->sec))),
						'transaction_type'       =>($ids['transtype']),
						'total_credit'       =>($ids['credit']),
                                     );
				}
		 
                $data['total']=(int)($results->num_rows);
                
                
                $url = '';
				
		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}
         if (isset($this->request->get['pagetittle'])) 
		{
			$url .= '&pagetittle=' . $this->request->get['pagetittle'];
		}     
                $data['filter_date_start'] = $sdate;
		$data['filter_date_end'] = $edate;
                
                $data['token'] = $this->session->data['token'];
                
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer'); 
                $data['mobile_number']=$this->request->get['mobile_number'];
                $data['aadhar']=$this->request->get['aadhar'];
                $data['name']=$this->request->get['name'];
                $data['credit']=$this->request->get['credit'];
                $data['return_url']=$_SERVER['HTTP_REFERER'];
                $this->response->setOutput($this->load->view('default/template/pos/customer_credit_trans.tpl', $data));   
            }
            
}