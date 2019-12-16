<?php
    class ModelPosPos extends Model 
    {
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
	public function addOrderOpen($data) 
    {
        $log=new Log("orderopen-sql-".date('Y-m-d').".log");
        $log->write("addOrderOpen call");
		$log->write($data);
        $this->adminmodel('setting/store');
        
        $this->adminmodel('setting/setting');
  /*
        $setting_info = $this->model_setting_setting->getSetting('setting', $data['store_id']);
   
        if (isset($setting_info['config_invoice_prefix'])) 
        {
            $invoice_prefix = $setting_info['config_invoice_prefix'];
			$store_name = $setting_info['config_name'];
            $store_url = $setting_info['config_url'];
        } 
        else */
        {
            $invoice_prefix = $this->config->get('config_invoice_prefix');
			$store_name = $this->config->get('config_name');
            $store_url = $this->config->get('config_url');//HTTP_CATALOG;
        }
        $shipping_country = ''; 
        $shipping_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
       
        $shipping_zone = '';   
        
        $payment_country = ''; 
        $payment_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';     
        
        $payment_zone = '';   
         
        $this->load->model('localisation/currency');

        $currency_info = $this->model_localisation_currency->getCurrencyByCode($this->config->get('config_currency'));
  
        if ($currency_info) 
        {
            $currency_id = $currency_info['currency_id'];
            $currency_code = $currency_info['code'];
            $currency_value = $currency_info['value'];
        } 
        else 
        {
            $currency_id = 0;
            $currency_code = $this->config->get('config_currency');
            $currency_value = 1.00000;   
        }
		$tdata=array();
		if (isset($data['order_total'])) 
        {  
            foreach ($data['order_total'] as $order_total) 
            {
                
                
                $tdata[]=array(
                         
                        'text'=>$this->db->escape($order_total['text']) ,
                        'code'=>$this->db->escape($order_total['code']) ,
                        'title'=>$this->db->escape($order_total['title']),
                        'value'=> (float)$order_total['value'],
                        'sort_order'=>  (int)$order_total['sort_order'],
                        );
                
                
            }
            
        }
		$data['order_total']=$tdata;
        $order_id = $this->db->getNextSequenceValue("oc_order");  
        //$sqlorder="INSERT INTO `" . DB_PREFIX . "order` SET card_no ='" . $this->db->escape($data['card_no']) . "',  invoice_prefix = '" . $this->db->escape($invoice_prefix) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($store_name) . "',store_url = '" . $this->db->escape($store_url) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($payment_country) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($payment_zone) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($payment_address_format) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($shipping_country) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($shipping_zone) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($shipping_address_format) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', subsidy_category = '" . $this->db->escape($data['subsidy_cat_id']) . "', comment = '" . $this->db->escape($data['comment']) . "', order_status_id = '" . (int)$data['order_status_id'] . "', affiliate_id  = '" . (int)$data['affiliate_id'] . "', language_id = '" . (int)$this->config->get('config_language_id') . "', currency_id = '" . (int)$currency_id . "', currency_code = '" . $this->db->escape($currency_code) . "', currency_value = '" . (float)$currency_value . "', date_added = NOW(), date_modified = NOW()";
        
		
		$fdata=array(
        'order_id'=>(int)$order_id,
        'card_no'=>$this->db->escape($data['card_no']),
        'invoice_prefix'=>$this->db->escape($invoice_prefix),
        'invoice_no'=>(int)0,
        'store_id'=>(int)$data['store_id'],
        'store_name'=>$this->db->escape($store_name),
        'store_url'=>$this->db->escape($store_url),
        'customer_id'=>(int)$data['customer_id'],
        'customer_group_id'=>(int)$data['customer_group_id'],
        'firstname'=>$this->db->escape($data['firstname']),
        'lastname'=>$this->db->escape($data['lastname']),
        'email'=>$this->db->escape($data['email']),
        'telephone'=>$this->db->escape($data['telephone']),
        'fax'=>$this->db->escape($data['fax']),
        'payment_firstname'=>$this->db->escape($data['payment_firstname']),
        'payment_lastname'=>$this->db->escape($data['payment_lastname']),
        'payment_company'=>$this->db->escape($data['payment_company']),
        'payment_address_1'=>$this->db->escape($data['payment_address_1']),
        'payment_address_2'=>$this->db->escape($data['payment_address_2']),
        'payment_city'=>$this->db->escape($data['payment_city']),
        'payment_postcode'=>$this->db->escape($data['payment_postcode']),
        'payment_country'=>$this->db->escape($payment_country),
        'payment_country_id'=>(int)$data['payment_country_id'],
        'payment_zone'=>$this->db->escape($payment_zone),
        'payment_zone_id'=>(int)$data['payment_zone_id'],
        'payment_address_format'=>$this->db->escape($payment_address_format),
        'payment_method'=>$this->db->escape($data['payment_method']),
        'payment_code'=>$this->db->escape($data['payment_code']),
        'shipping_firstname'=> $this->db->escape($data['shipping_firstname']),
        'shipping_lastname'=>$this->db->escape($data['shipping_lastname']),
        'shipping_company'=>$this->db->escape($data['shipping_company']),
        'shipping_address_1'=>$this->db->escape($data['shipping_address_1']),
        'shipping_address_2'=>$this->db->escape($data['shipping_address_2']),
        'shipping_city'=>$this->db->escape($data['shipping_city']),
        'shipping_postcode'=>$this->db->escape($data['shipping_postcode']),
        'shipping_country'=>$this->db->escape($data['shipping_country']),
        'shipping_country_id'=>(int)$data['shipping_country_id'],
        'shipping_zone_id'=>(int)$data['shipping_zone_id'],
        'shipping_zone'=>$this->db->escape($shipping_zone),
        'shipping_address_format'=>$this->db->escape($shipping_address_format),
        'shipping_code'=>$this->db->escape($data['shipping_code']),
        'shipping_method'=>$this->db->escape($data['shipping_method']),
        'subsidy_category'=>$this->db->escape($data['subsidy_cat_id']),
        'comment'=>$this->db->escape($data['comment']),
        'user_id'=>(int)$data['user_id'],
        'total'=>(float)0,
        'credit'=> (float)0,
        'cash'=>  ((float)0),
		'reward'=>  ((float)0), 
		'tax'=>  ((float)0), 
        'commission'=>  (float)$commission,
        'order_status_id'=>(int)$data['order_status_id'],
        'affiliate_id'=>(int)$data['affiliate_id'],
        'language_id'=>(int)$this->config->get('config_language_id'),
        'currency_id'=>(int)$currency_id ,
        'currency_code'=>$this->db->escape($currency_code),
        'currency_value'=>(float)$currency_value,
        'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))),
        'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))),
        'order_product'=>$data['order_product'],
        'order_total'=>   $data['order_total'],
        'billtype'  => (int)$data['billtype'],
		'transid'=>$data['transid']
        );
        $log->write($fdata);
        $query = $this->db->query('insert','oc_order',$fdata);
        $log->write($order_id);
		$inv_no=$this->createInvoiceNo($fdata); 
        $log->write("data from product start");
        $log->write($data['order_product']);
        $log->write("data from product end");
        $totalopen=0;
        $subtotalopen=0;
        $taxopen=0;
        $log->write($data['order_product']);
        if (isset($data['order_product'])) 
        {  
			$order_reward=0;
            foreach ($data['order_product'] as $order_product) 
            { 
                $order_product['price']=str_replace("Rs.","",$order_product['price']);
                $order_product['price']=str_replace(",","",$order_product['price']);
                $order_product_id =$this->db->getNextSequenceValue("oc_order_product");  
                $pdata=array(
                        'order_product_id'=>(int)$order_product_id,
                        'order_id'=>(int)$order_id,
                        'product_id'=>(int)$order_product['product_id'],
                        'name'=>$this->db->escape($order_product['name']),
                        'model'=> $this->db->escape($order_product['model']),
                        'quantity'=> (int)$order_product['quantity'],
                        'price'=>(float)$order_product['price'],
                        'total'=>(float)$order_product['total'],
                        'tax'=>(float)$order_product['tax'],
                       
						'tax_class_id'=>(int)$order_product['tax_class_id'],
                        'tax_class_name'=>$order_product['tax_class_name'],
                        'tax_class_rate'=>$order_product['tax_class_rate'],
						'reward'=>($order_product['product_mitra']==1)? (float)($order_product['quantity']*$order_product['reward']): (float)(0),
                        'product_mitra'=>(int)$order_product['product_mitra'],
                        'category_id'=>(int)$order_product['category_id'],
                    );
                $log->write($pdata);
                $this->db->query('insert','oc_order_product',$pdata);
                $totalopen=$totalopen+(($order_product['quantity'] *$order_product['price'])+($order_product['quantity'] *$order_product['tax']));
                $subtotalopen=$subtotalopen+($order_product['quantity'] *$order_product['price']);
                $taxopen=$taxopen+($order_product['quantity'] *$order_product['tax']);
				
                if($order_product['product_mitra']==1)
                {
                    $data1=array('mitra_quantity'=>-(int)$order_product["quantity"]); 
                    $order_reward=$order_reward+(float)($order_product["quantity"]*$order_product['reward']);
                }
                else 
                {
                    $data1=array('quantity'=>-(int)$order_product["quantity"]); 
                } 
                $match=array('product_id'=>(int)$order_product["product_id"],'store_id'=>(int)$data["store_id"]);
				
				$this->load->library('trans');
                $trans=new trans($this->registry);
                if($order_product['subtract']==0)
                {
                    $query2= $this->db->query('incmodify','oc_product_to_store',$match,$data1);
                    $trans->addproducttrans($data['store_id'],$order_product['product_id'],$order_product['quantity'],$order_id,'DB','LED BILLING',$data['billtype']);  
                } 
				else
				{
					$trans->addproducttrans($data['store_id'],$order_product['product_id'],$order_product['quantity'],$order_id,'DB','OPEN BILLING',$data['billtype']);
				}	
				if($order_product['product_id']==3496)
				{
					$this->load->model('ccare/ccare');
					$prd_call_query='Product Promation for '.$order_product['name'];
					$this->model_ccare_ccare->insertquery($data['telephone'],(int)$data['customer_id'],array('Categories'=>(int)$order_product['category_id'],'category_name'=>'','Type'=>'Other','type_name'=>'','store_id'=>$data['store_id'],'query'=>$prd_call_query,'channel'=>'Sales','call_status'=>14));
				}
            }
			$log->write('order_reward');
			$log->write($order_reward);
			if(!empty($order_reward))
            {
                $rdata=array(
                        
                        'order_id'=>(int)$order_id,
                        'store_id'=> (int)$data['store_id'],
                        'store_name'=> $store_name,
                        'inv_no'=>$inv_no,
                        'customer_id'=>(int)$data['customer_id'],
                        'description'=>$this->db->escape('Order-Reward'),
                        'type'=>$this->db->escape('Add'),
                        'points'=> (float)$order_reward,
                        'date_added'=> new MongoDate(strtotime(date('Y-m-d H:i:s')))
                    
                    );
                $log->write($rdata);
                $this->db->query('insert','oc_customer_reward',$rdata); 
                $data1=array('reward'=>(float)$order_reward); 
                $query2= $this->db->query('incmodify','oc_customer',array('customer_id'=>(int)$data['customer_id']),$data1);
            }
			$log->write('reward');
			$log->write($data['reward']);
            if((!empty($data['reward'])) && ($data['reward']!='0.0'))
            {
                $rdata=array(
                        
                        'order_id'=>(int)$order_id,
                        'store_id'=> (int)$data['store_id'],
                        'store_name'=> $store_name,
                        'inv_no'=>$inv_no,
                        'customer_id'=>(int)$data['customer_id'],
                        'description'=>$this->db->escape('Order-Reward-Redeem'),
                        'type'=>$this->db->escape('Redeem'),
                        'points'=> (float)$data['reward'],
                        'date_added'=> new MongoDate(strtotime(date('Y-m-d H:i:s')))
                    
                    );
                $log->write($rdata);
                $this->db->query('insert','oc_customer_reward',$rdata); 
                $data1=array('reward'=>-(float)$data['reward']); 
                $query2= $this->db->query('incmodify','oc_customer',array('customer_id'=>(int)$data['customer_id']),$data1);
            }
        }
        // Get the total       
        $log->write($data['order_total']);
        if (isset($data['order_total'])) 
        {  
			$order_total_tax=0;
            foreach ($data['order_total'] as $order_total) 
            {
                
                $order_total_id =$this->db->getNextSequenceValue("oc_order_total"); 
                $tdata=array(
                        'order_total_id'=> (int)$order_total_id ,   
                        'order_id'=>(int)$order_id,
                        'code'=>$this->db->escape($order_total['code']) ,
                        'title'=>$this->db->escape($order_total['title']),
                        'value'=> (float)$order_total['value'],
                        'sort_order'=>  (int)$order_total['sort_order'],
                        );
                $log->write($tdata);
                $this->db->query('insert','oc_order_total',$tdata);
				if(($order_total['code']!='sub_total') && ($order_total['code']!='discount') && ($order_total['code']!='total'))
				{
					$order_total_tax=$order_total_tax+(float)$order_total['value'];
				}
            }
            
        }
        $total =$totalopen;
        // Affiliate
        $affiliate_id = 0;
        $commission = 0;

        if (!empty($this->request->post['affiliate_id']))
        {
            $affiliate_id = (int)$this->request->post['affiliate_id'];
        }
        $log->write("data");
           
        if(!empty($data['credit_amount']))
        {
            $log->write("getcount");
            $getcount=$this->db->getcount('oc_customer_to_store',array('store_id'=>(int)$data['store_id'], 'customer_id'=>(int)$data['customer_id']));
            $log->write( $getcount);
            if(empty($getcount))
            {
                $sidd=$this->db->getNextSequenceValue('oc_customer_to_store');
                $cdata=array(
                        'credit'=>(float)$data['credit_amount'],
                        'store_id'=>(int)$data['store_id'] ,
                        'customer_id'=>(int)$data['customer_id'],
                        'sid'=> (int)$sidd
                    );
                $log->write($cdata);
                $this->db->query('insert','oc_customer_to_store',$cdata);
            }
            else
            {
                $cdata=array(
                    'credit'=>(float)$data['credit_amount']
                    );
                $log->write($cdata);
                $this->db->query('incmodify','oc_customer_to_store',array('store_id'=>(int)$data['store_id'],'customer_id'=>(int)$data['customer_id']),$cdata);
            }
            $cdata2=array(
                    'credit'=>(float)$data['credit_amount']
                    );
                $log->write($cdata);
                $this->db->query('incmodify','oc_customer',array('store_id'=>(int)$data['store_id'],'customer_id'=>(int)$data['customer_id']),$cdata2);
            $discnt=0.0;
		if(isset($data['discount']) && (!empty($data['discount'])))
		{
			$upd_cash_trans=(float) number_format(((float)($total-$data['discount']-$data['credit_amount'])), 2, '.', '');
			$discnt=$data['discount'];
		}
		else
		{
			$upd_cash_trans=(float) number_format(((float)($total-$data['credit_amount'])), 2, '.', '');
			$discnt=0.0;
		}
		
            $ctdata=array(
                    'credit'=>(float)$data['credit_amount'],
                    'cash'=>(float)$upd_cash_trans,
					'order_id'=>$inv_no,
		    'discount'=>(float)$discnt,
                    'store_id'=> (int)$data['store_id'],
                    'customer_id'=>  (int)$data['customer_id'],
                    'trans_type'=>  'CR',
                 'create_time'=> new MongoDate(strtotime(date("Y-m-d  H:i:s")))
                    );
            $log->write($ctdata);
            $this->db->query('insert','oc_customer_to_store_trans',$ctdata);
     
        }
		$redeem=!empty($this->session->data['redeem_points'])?$this->session->data['redeem_points']:0;
                $log->write('upd_cash');
                $log->write($upd_cash);
		if(isset($data['discount']) && (!empty($data['discount'])))
		{
                    
                    
			$upd_cash=(float) number_format(((float)($total-$data['discount']-$data['credit_amount']-$redeem)), 2, '.', '');
		}
		else
		{
			$upd_cash=(float) number_format(((float)($total-$data['credit_amount']-$redeem)), 2, '.', '');
		}
                
                $log->write($upd_cash);
		if(!empty($data['credit_amount']))
                {
                    $log->write($this->session->data['redeem_points']);
                /*if(isset($this->session->data['redeem_points']) && (!empty($this->session->data['redeem_points'])))
		{
			$data['credit_amount']=(float) number_format(((float)($data['credit_amount']-$this->session->data['redeem_points'])), 2, '.', '');
		}
		else
		{
			$data['credit_amount']=(float) number_format(((float)($data['credit_amount']-$this->session->data['redeem_points'])), 2, '.', '');
		}
                */
                }
                $log->write($upd_cash);
		
		
		//$log->write($data);
		$log->write($data['discount']);
		if((!empty($data['discount'])) && ($data['discount']!=0.0))
		{
			if($data['credit_amount']>0)
			{
				if($upd_cash>0)
				{
					$pay_method='CC Discount';
				}
				else
				{
					$pay_method='Credit Discount';
				}
		
			}
			else
			{
				$pay_method='Cash Discount';
			}
		}
		else
		{
			if($data['credit_amount']>0)
			{
				if($upd_cash>0)
				{
					$pay_method='Cash Credit';
				}
				else
				{
					$pay_method='Credit';
				}
		
			}
			else
			{
				$pay_method='Cash';
			}
		}
        $updatedata=array(
            'total'=>(float)$total,
            'credit'=> (float)$data['credit_amount'],
			'reward'=> (float)$this->session->data['redeem_points'],//not clear
            'cash'=> $upd_cash,
            'affiliate_id'=>  (int)$affiliate_id,
            'commission'=>  (float)$commission,
			'discount'=> (float)$data['discount'],
			'pay_method'=>$pay_method,
			'tax'=>$order_total_tax
            );
        $log->write($updatedata);
        $this->db->query('update','oc_order',array('order_id'=>(int)$order_id),$updatedata);
        $log->write("data 1");
	
        return array('order_id'=>$order_id,'inv_no'=>$inv_no,'total'=>(float)$total);
        //return $order_id;
    }
	public function confirm_coupon($order_info, $code) 
    {
            $log=new Log("order-coupon".date('Y-m-d').".log");
        
            $log->write('confirm_coupon is called');
            $log->write($order_info);
            $this->load->model('checkout/coupon');

            $coupon_info = $this->model_checkout_coupon->getCoupon($code);
            $log->write($coupon_info);
            if ($coupon_info) 
            {
                $sql="INSERT INTO `" . DB_PREFIX . "coupon_history` SET coupon_id = '" . (int)$coupon_info['coupon_id'] . "', order_id = '" . (int)$order_info['order_id'] . "', customer_id = '" . (int)$order_info['customer_id'] . "', amount = '" .$order_info['order_total'] [1]['value']. "', date_added = NOW()";
                $log->write($sql);
                $this->db->query($sql);
            }
    }
    public function addPayment($data) 
    {
            if(!empty($data['store_id']))
            {
                $store=$data['store_id'];
            }   
            else
            {
                $store=0;
            }       
            if( ($data['payment_method']=="Cash") )
            {
                $log=new Log("addpayment-".date('Y-m-d').".log");
                try
                { 
                    $data1=array('cash'=>(float)$data['cash']); 
                    $match=array('user_id'=>(int)$data["user_id"]);
                    $query2= $this->db->query('incmodify','oc_user',$match,$data1);
                    $this->load->library('trans');
                    $trans=new trans($this->registry);
                    $trans->addstoretrans($data['cash'],$store,$data['user_id'],'CR',$data["order_id"],$data['payment_method'],$data['total']);  
                    } 
                    catch (Exception $e)
                    {
                        $log->write($e->getMessage()); 
                    }
            }
        }
    
        public function updateinventory($data)
        {
            $log=new Log('updateinventory-'.date('Y-m-d').'.log');
            //quantity  to update in store
            $log->write('updateinventory called from : '.$data['web_app']);
            $getsql="select * from oc_order_product WHERE order_id = '" . (int)$data['order_id'] . "' ";
            $log->write($getsql);
            $getsqlres=$this->db->query($getsql);
            $product_rows=$getsqlres->rows;
            if (!empty($product_rows)) 
            {		
      		foreach ($product_rows as $order_product) 
                {	
                    $sqlpdeduct="UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'";
                    $log->write($sqlpdeduct);
                    $this->db->query($sqlpdeduct);
                    $sqlpdeduct2="UPDATE " . DB_PREFIX . "product_to_store SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND store_id = '".(int)$data['store_id']."'";
                    $log->write($sqlpdeduct2);
                    $this->db->query($sqlpdeduct2);
                    try
                    { 
                        $this->load->library('trans');
                        $trans=new trans($this->registry);
                        $trans->addproducttrans($data['store_id'],$order_product['product_id'],$order_product['quantity'],$data['order_id'],'DB','SALE',$data['web_app']);  
                    } 
                    catch (Exception $e)
                    {
                        $log->write($e->getMessage());
                    }
                }
            }
        }
   
	public function editOrder($order_id, $data) {
		$this->load->model('localisation/country');

		$this->load->model('localisation/zone');

		$country_info = $this->model_localisation_country->getCountry($data['shipping_country_id']);

		if ($country_info) {
			$shipping_country = $country_info['name'];
			$shipping_address_format = $country_info['address_format'];
		} else {
			$shipping_country = '';	
			$shipping_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}	

		$zone_info = $this->model_localisation_zone->getZone($data['shipping_zone_id']);

		if ($zone_info) {
			$shipping_zone = $zone_info['name'];
		} else {
			$shipping_zone = '';			
		}	

		$country_info = $this->model_localisation_country->getCountry($data['payment_country_id']);

		if ($country_info) {
			$payment_country = $country_info['name'];
			$payment_address_format = $country_info['address_format'];			
		} else {
			$payment_country = '';	
			$payment_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';					
		}

		$zone_info = $this->model_localisation_zone->getZone($data['payment_zone_id']);

		if ($zone_info) {
			$payment_zone = $zone_info['name'];
		} else {
			$payment_zone = '';			
		}			

		// Restock products before subtracting the stock later on
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

			foreach($product_query->rows as $product) {
				$this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_id = '" . (int)$product['product_id'] . "' AND subtract = '1'");

				$option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

				foreach ($option_query->rows as $option) {
					$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
				}
			}
		}

		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($payment_country) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($payment_zone) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($payment_address_format) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "',  shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($shipping_country) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($shipping_zone) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($shipping_address_format) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', order_status_id = '" . (int)$data['order_status_id'] . "', affiliate_id  = '" . (int)$data['affiliate_id'] . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'"); 
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");

		if (isset($data['order_product'])) {
			foreach ($data['order_product'] as $order_product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_product_id = '', order_id = '" . (int)$order_id . "', product_id = '" . (int)$order_product['product_id'] . "', name = '" . $this->db->escape($order_product['name']) . "', model = '" . $this->db->escape($order_product['model']) . "', quantity = '" . (int)$order_product['quantity'] . "', price = '" . (float)$order_product['price'] . "', total = '" . (float)$order_product['total'] . "', tax = '" . (float)$order_product['tax'] . "', reward = '" . (int)$order_product['reward'] . "'");

				$order_product_id = $this->db->getLastId();

				$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

				if (isset($order_product['order_option'])) {
					foreach ($order_product['order_option'] as $order_option) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_option_id = '', order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$order_option['product_option_id'] . "', product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "', name = '" . $this->db->escape($order_option['name']) . "', `value` = '" . $this->db->escape($order_option['value']) . "', `type` = '" . $this->db->escape($order_option['type']) . "'");


						$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'"); 

		if (isset($data['order_voucher'])) {	
			foreach ($data['order_voucher'] as $order_voucher) {	
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_voucher_id = '', order_id = '" . (int)$order_id . "', voucher_id = '" . (int)$order_voucher['voucher_id'] . "', description = '" . $this->db->escape($order_voucher['description']) . "', code = '" . $this->db->escape($order_voucher['code']) . "', from_name = '" . $this->db->escape($order_voucher['from_name']) . "', from_email = '" . $this->db->escape($order_voucher['from_email']) . "', to_name = '" . $this->db->escape($order_voucher['to_name']) . "', to_email = '" . $this->db->escape($order_voucher['to_email']) . "', voucher_theme_id = '" . (int)$order_voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($order_voucher['message']) . "', amount = '" . (float)$order_voucher['amount'] . "'");

				$this->db->query("UPDATE " . DB_PREFIX . "voucher SET order_id = '" . (int)$order_id . "' WHERE voucher_id = '" . (int)$order_voucher['voucher_id'] . "'");
			}
		}

		// Get the total
                $total = 0;
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'"); 
                
		if (isset($data['order_total'])) {		
      		foreach ($data['order_total'] as $order_total) {	
      			$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($order_total['code']) . "', title = '" . $this->db->escape($order_total['title']) . "', `value` = '" . (float)$order_total['value'] . "', sort_order = '" . (int)$order_total['sort_order'] . "'");
			}
			
			$total += $order_total['value'];
		}

		// Affiliate
		$affiliate_id = 0;
		$commission = 0;

		if (!empty($this->request->post['affiliate_id'])) {
			$affiliate_id = (int)$this->request->post['affiliate_id'];
		}
		
		if ($affiliate_id > 0 ) {
			$this->load->model('sale/affiliate');
			
			$affiliate_info = $this->model_sale_affiliate->getAffiliate($affiliate_id);
			
			if ($affiliate_info) {
				$commission = ($total / 100) * $affiliate_info['commission']; 
			}
		}
		
		// Update order total			 
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET user_id = '".$this->user->getId()."', total = '" . (float)$total . "', affiliate_id = '" . (int)$affiliate_id . "', commission = '" . (float)$commission . "' WHERE order_id = '" . (int)$order_id . "'"); 	
		
		return $order_id;
	}

	// add for Browse begin
	public function getTopStoreCategories($sid) 
        {
           
            $match=array('parent_id'=>0,'category_store'=>(int)$sid);
            $sort_array=array('sort_order'=>1);
            $columns=array();
            $unwind='$ocs';
            $query = $this->db->query("select",DB_PREFIX . "category",'','',$match,'','','',$columns,$start,$sort_array);
            
            $return_array= array();
            $a=0;
            foreach($query->rows as $row)
            {
                $return_array[$a]['category_id']=$row['category_id'];
                $return_array[$a]['parent_id']=$row['parent_id'];
                $return_array[$a]['image']=$row['image'];
                $return_array[$a]['top']=$row['top'];
                $return_array[$a]['column']=$row['column'];
                $return_array[$a]['sort_order']=$row['sort_order'];
                $return_array[$a]['status']=$row['status'];
                $return_array[$a]['name']=strip_tags($row['category_description'][1]['name']);
                $return_array[$a]['keyword']=htmlentities($row['category_description'][1]['meta_keyword']);
                $return_array[$a]['ocs']=$row['category_id'];
                
                $a++;
            }
          
            return $return_array;
	}
	public function getTopCategories() 
        {
            $match=array('status'=>true);
            
            $sort_array=array('sort_order'=>1);
            $query = $this->db->query("select",DB_PREFIX . "category",'','',$match,'','',$limit,'',$start,$sort_array);
           // print_r($query->rows);
            foreach($query->rows as $row)
            {
                $return_array[]=array(
                            'category_id'=>$row['category_id'],
							'image'=>$row['image'],
                            'name'=>$row['category_description'][1]['name'],
                            'totalrows'=>$query->num_rows    
                        );
            }
            return $return_array;
            
	}
        public function getCategories() 
        {
            $query = $this->db->query("SELECT c.image, c.category_id, c.parent_id, cd.name FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON c.category_id = cd.category_id WHERE cd.language_id = '". (int)$this->config->get('config_language_id') . "'");
            return $query->rows;
	}
        
	public function getSubCategories($category_id) 
        {
            $query = $this->db->query("SELECT c.category_id, c.image, cd.name FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON c.category_id = cd.category_id WHERE cd.language_id = '". (int)$this->config->get('config_language_id') . "' AND c.parent_id = '" . $category_id . "'");
            return $query->rows;
	}
	public function getProducts($data = array()) 
    {
        $log=new Log("prdinv-".date('Y-m-d').".log");
        $log->write('in model getProducts');
        $log->write($data);
        if (isset($data['start']) || isset($data['limit'])) 
        {
            if ($data['start'] < 0) 
            {
				$start = 0;
            }
            else
            {
                $start=(int)$data['start'];
            }
            if ($data['limit'] < 1) 
            {
				$limit = (int)20;
            }
            else 
            {
                $limit = (int)$data['limit'];
            }

			
		}
		else
		{
			$start=0;
			$limit=20;
		}
        $lookup=array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                );
        $sort_array=array('product_id'=>1);
		if($data['filter_category_id']==44)
		{
			$log->write("44 in");
			//$match=array('status'=>false);
		}
		else
		{
			$match=array('status'=>true);
		}
        
        if (!empty($data['filter_category_id'])) 
        {
            $match['category_ids']=$data['filter_category_id'];
        }
        if (!empty($data['filter_product_id'])) 
        {
            $match['product_id']=$data['filter_product_id'];
        }
        if (!empty($data['product_name'])) 
        {
            $search_string=$data['product_name'];
            $match['name'] = new MongoRegex("/.*$search_string/i");
        }
		//if($data['filter_category_id']==44)
		//{
			//$storeid=$data['store_id'];
		//}
		//else
		//{
			$storeid=$this->config->get('config_store_id');
		//}
        
        $match['pd.store_id']=(int)$storeid;
        if(!empty($storeid))
        {
            if(!empty($data['quantity_check']))
            {
                $match['pd.quantity']=array('$gt'=>0); 
            }
        }
        //print_r($match);//exit;
		//echo $start;
        $log->write($match);
        $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'$pd',$match,'','',$limit,$columns,$start,$sort_array);
		//print_r($match);//exit;
        foreach ($query->row as $result) 
		{	 
            
            $product_data[$result['product_id']] = $result;//$this->getProduct($result[0]['product_id']);

		}
        return $product_data;
		}
        public function getProductsAll($category_id, $limit = 20, $offset = 0,$product_name='') 
        {
            $log=new Log("prdinvall-".date('Y-m-d').".log");
            
        
            $lookup=array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                );
            $sort_array=array('product_id'=>1);
            $match=array('status'=> boolval(1));
            if (!empty($category_id)) 
            {
                $match['category_ids']=$category_id;
            }
            if(!empty($product_name))
            {
                $search_string="/.*".$product_name."/i";
                $match['name']=new MongoRegex($search_string); 
            }
            
            $match['pd.store_id']=(int)0;//$storeid;
            if(!empty($storeid))
            {
                $match['pd.quantity']=array('$gt'=>0); 
            }
           //$log->write($match);
            $query = $this->db->query("join", "oc_product",$lookup,'$pd',$match,'','',$limit,'',$offset,$sort_array);
            
            foreach ($query->row as $result) 
            {	 
                
                $product_data[$result['product_id']] = $result;//$this->getProduct($result[0]['product_id']);

            }
            return $product_data;   
		}
        public function getTotalCredit($data)
		{
            if (!empty($data['store_id'])) 
            {
				$match['store_id']=(int)$this->db->escape($data['store_id']);
            }  
            $group=array();
            $group[]=array("_id"=>'$store_id',"total"=>array('$sum'=>'$credit'));
            $sort=array("ctotal"=>-1);
            $query=$this->db->query('join','oc_customer_to_store','','',$match,'','','',array(),'',$sort,'',$group);
            return $query->row;
        }
		public function getTotalCreditAllStores($data)
		{
            if (!empty($data['store_id'])) 
            {
				$match['store_id']=(int)$this->db->escape($data['store_id']);
            }  
			if (isset($data['start']) || isset($data['limit'])) 
			{
				if ($data['start'] < 0) 
                {
                    $data['start'] = 0;
				}
                if ($data['limit'] < 1) 
                {
                    $data['limit'] = 20;
				}
				$limit=(int)$data['limit'];
				$start=(int)$data['start'];
			}
            $group=array();
            $group[]=array("_id"=>'$store_id',"total"=>array('$sum'=>'$credit'),"count"=>array('$sum'=>1));
            $sort=array("_id"=>1);
            $query=$this->db->query('join','oc_customer','','',$match,'','',$limit,array(),$start,$sort,'',$group,$match);
            return $query;
        }
		public function getCustomerTotalRewardSum($data)
		{
			
            if (!empty($data['customer_id'])) 
            {
				$match['customer_id']=(int)$this->db->escape($data['customer_id']);
            } 
			if (!empty($data['store_id'])) 
            {
				$match['store_id']=(int)$this->db->escape($data['store_id']);
            }
			//$match['type']='Add';	
			if (isset($data['start']) || isset($data['limit'])) 
			{
				if ($data['start'] < 0) 
                {
                    $data['start'] = 0;
				}
                if ($data['limit'] < 1) 
                {
                    $data['limit'] = 20;
				}
				$limit=(int)$data['limit'];
				$start=(int)$data['start'];
			}
            $group=array();
            $group[]=array("_id"=>'$type',"total"=>array('$sum'=>'$points'));
            $sort=array("_id"=>1);
			
            $query=$this->db->query('join','oc_customer_reward','','',$match,'','',$limit,array(),$start,$sort,'',$group,$match);
            return $query;
        }
		public function getPremiumCustomersAllStores($data)
        {		
            $where=array();
			$where['credit']=array('$gt'=>0);
            if(!empty($data['store_id']))
            {
                $where['store_id']=(int)$this->db->escape($data['store_id']);
            }
            
            if(!empty($data['telephone']))
            {
				$search_string=$data['telephone'];
                $search_string="/.*".$search_string."/i";
                $where['telephone']=new MongoRegex($search_string); 
            }
			if(!empty($data['firstname']))
            {
				$search_string2=$data['firstname'];
                $search_string2="/.*".$search_string2."/i";
                $where['firstname']=new MongoRegex($search_string2); 
            }
			if (isset($data['start']) || isset($data['limit'])) 
			{
				if ($data['start'] < 0) 
                {
                    $data['start'] = 0;
				}
                if ($data['limit'] < 1) 
                {
                    $data['limit'] = 20;
				}
				$limit=(int)$data['limit'];
				$start=(int)$data['start'];
			}
			
            $query = $this->db->query('select',DB_PREFIX . "customer",'','','',$where,'',$limit,'',$start,array('firstname'=>1));
            return $query;
        }
        public function getCustomers($sid,$uid,$type='')
        {		
            
            if($type=='credit')
            {
                $where=array('store_id'=>(int)$this->db->escape($sid),'credit'=>array('$gt'=>0));
            }
            else
            {
               $where=array('store_id'=>(int)$this->db->escape($sid)); 
            }
	
            $query = $this->db->query('select',DB_PREFIX . "customer",'','','',$where,'',10,'',0,array('customer_id'=>-1));
            return $query->rows;
        }
        public function getCustomer($customer_id)
        {
            $query = $this->db->query('select',DB_PREFIX . "customer",'','','',array('customer_id'=>(int)$this->db->escape($customer_id)));
            return $query->row;
        }
        public function getCustomerByPhone($telephone)
        {
            $query = $this->db->query('select',DB_PREFIX . "customer",'','','',array('telephone'=>$this->db->escape($telephone)));
            
	    return $query->row;
        }
		public function getCustomerByPhoneStore($telephone,$sid)
        {
			$log=new Log("order-customer-".date('Y-m-d').".log");
	        $log->write($telephone);
        	$log->write($sid);
            $query = $this->db->query('select',DB_PREFIX . "customer",'','','',array('telephone'=>$this->db->escape($telephone),'store_id'=>(int)$this->db->escape($sid)));
            $log->write($query);
			return $query->row;
        }

        public function searchCustomer($q,$limit='',$store_id='')
        {
            $log=new Log("cust-serch".date('Y-m-d').".log");  
            $search_string=($q);
            $log->write( $search_string);
            $where=array();
            if(!empty($search_string))
            {
                $search_string="/.*".$search_string."/i";
                $where['telephone']=new MongoRegex($search_string); 
            }
            if(!empty($store_id))
            {
                
                $where['store_id']=(int)$store_id; 
            }
                $log->write( $where);   
            $query = $this->db->query('select',DB_PREFIX . "customer",'','','', $where,'',$limit);     
                         
			return $query->rows;
        }
		public function searchCustomerStoreBased($q,$sid)
        {
            $log=new Log("cust-serch".date('Y-m-d').".log");  
            $search_string=($q);
            $log->write( $search_string);
            $or=array('store_id'=>(int)$sid,'telephone'=>new MongoRegex("/.*$search_string/i"));
            $log->write( $or);   
            $query = $this->db->query('select',DB_PREFIX . "customer",'','','', ( $or),'','10');     
            $log->write($query);              
			return $query->rows;
        }
        public function getstorename($store_id)
        {  
            $query = $this->db->query('select',DB_PREFIX . 'store','','','',array('store_id'=>(int)$store_id),'','', $data);
                
            return $query->row['name']; 
			
        } 
        public function getOrder($order_id) 
        {
		$order_query = $this->db->query("SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$reward = 0;
                            return array(
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'customer'                => $order_query->row['customer'],
				'customer_group_id'       => $order_query->row['customer_group_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'email'                   => $order_query->row['email'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
                                'card_no' => $order_query->row['card_no'],
				'custom_field'            => unserialize($order_query->row['custom_field']),
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
			
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
		
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_custom_field'    => unserialize($order_query->row['payment_custom_field']),
				'payment_method'          => $order_query->row['payment_method'],
				'payment_code'            => $order_query->row['payment_code'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
		
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],
				
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_custom_field'   => unserialize($order_query->row['shipping_custom_field']),
				'shipping_method'         => $order_query->row['shipping_method'],
				'shipping_code'           => $order_query->row['shipping_code'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'reward'                  => $reward,
				'order_status_id'         => $order_query->row['order_status_id'],
				'affiliate_id'            => $order_query->row['affiliate_id'],
		
				'commission'              => $order_query->row['commission'],
				'language_id'             => $order_query->row['language_id'],
		
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'ip'                      => $order_query->row['ip'],
				'forwarded_ip'            => $order_query->row['forwarded_ip'],
				'user_agent'              => $order_query->row['user_agent'],
				'accept_language'         => $order_query->row['accept_language'],
				'date_added'              => $order_query->row['date_added'],
				'date_modified'           => $order_query->row['date_modified'],
				'unit_id'=> $order_query->row['unit_id'] 
			);
		} else {
			return;
		}
	}
	
	public function createInvoiceNo($order_info) 
	{
            $log=New log('invoice-'.date('Y-m-d').'.log');
            $log->write('invoice generate hit-');        
            $log->write($order_info);
            if ($order_info && !$order_info['invoice_no']) 
            {
		$invoice_no=$this->db->getNextInvoiceValue($order_info['store_id']);
		$this->db->query('update',DB_PREFIX . "order",array('order_id' =>(int)$order_info['order_id']),array('invoice_no'=>(int)$invoice_no));
                return $order_info['invoice_prefix'] . $invoice_no; 
            }
	}
       
	public function getproductprice($storeid,$productid)
	{ 
		$sql="SELECT store_price from oc_product_to_store where store_id='".$storeid."' and product_id='".$productid."'";

		$query = $this->db->query($sql);

		return $query->row['store_price'];

	} 
	public function getusermobile($userid)
	{ 
		$sql="SELECT username,email from oc_user where user_id='".$userid."' "; 

		$query = $this->db->query($sql);

		return $query->row;
 
	}
	public function get_user_balance($user_id)
        {
            $log=new Log("cash-new".date('Y-m-d').".log");
            $data=array('cash','card');
            $query = $this->db->query('select',DB_PREFIX . 'user','','','',array('user_id'=>(int)$user_id),'','', $data);
            $log->write($query->row); 
            return $query->row;   
        }
	public function get_store_cash_balance($store_id)
        { 
            $log=new Log("cash-new".date('Y-m-d').".log");
            $sql='SELECT sum(cash) as cash FROM `' . DB_PREFIX . 'user` WHERE store_id="'.$store_id.'" and user_group_id=11 group by store_id ';
            $query = $this->db->query($sql);  
            $log->write($sql);
            $log->write($query->row); 
            return $query->row;   
        }
	public function get_store_balance($store_id)
        {
            $query = $this->db->query('SELECT currentcredit FROM `' . DB_PREFIX . 'store` WHERE store_id="'.$store_id.'"');
            return $query->row['currentcredit'];   
        }
		

	
	public function storeprice($pid,$store_id)
        {
            $log=new Log('order_istance-'.date('Y-m-d').'.log');
            $sql="select store_price,quantity from oc_product_to_store where store_id='".$store_id."'  and product_id='".$pid."'";
            $query = $this->db->query($sql);
            $log->write($sql);
            return $query->row;
        }
        public function getCustomerCredit($customer_id,$store_id=0)
        {
            $log=new Log("cust-te-".date('Y-m-d').".log");
            $log->write($customer_id);
            $log->write($store_id);
			$where=array();
			if(!empty($customer_id))
			{
				$where['customer_id']=(int)$customer_id;
			}
			if(!empty($store_id))
			{
				$where['store_id']=(int)$store_id;
			}
            $query = $this->db->query('select', DB_PREFIX . 'customer_to_store','','','',$where);
            $log->write($query->row);
            if(empty($query->row['credit']))
            {
                $query->row['credit']=0.0;
            }
            $log->write($query->row);
            return $query->row['credit'];
        }
	public function check_order_instance($instance_id)
        {
            $log=new Log('order_istance-'.date('Y-m-d').'.log');
            $log->write($instance_id);
            if(!empty($instance_id))
            {
                $query = $this->db->query('select',DB_PREFIX . "order",'','','',array('transid'=>$this->db->escape($instance_id)),'',1,'',0);
                $rows=$query->row;
                $log->write($rows);
                if(count($rows)>0)
                {
                    return $rows['order_id'];
                }
            }
        }
    
    public function updatecustomercash($customer_id,$cash_amount,$sid) 
    {            
        $log=new Log("updatecreditbalance-".date('Y-m_d').".log");
		$log->write('in model');
        $log->write($customer_id);
		$log->write($cash_amount);
		$log->write($sid);
        $data=array(
       'credit'=>(float)0,
       'cash' =>(float)$cash_amount ,
       'store_id' => (int)$sid ,
       'customer_id' => (int)$customer_id,
       'trans_type'=>'DR',
	   'order_id'=>'N/A',
	   'discount'=>(float)0,
        'create_time'=> new MongoDate(strtotime(date("Y-m-d  H:i:s")))
        );
        $this->db->query("insert","oc_customer_to_store_trans", $data); 
        $data1=array('credit'=>-(float)$cash_amount); 
        $match=array('product_id'=>(float)$order_product["product_id"],'store_id'=>(int)$data["store_id"]);
        $query12= $this->db->query('incmodify','oc_customer',array('customer_id'=>$customer_id),$data1);//'store_id'=>(int)$sid,
        return $query2= $this->db->query('incmodify','oc_customer_to_store',array('store_id'=>(int)$sid,'customer_id'=>$customer_id),$data1);
    }
 
}    
?>