<?php
class Controllermposopenretailer  extends Controller
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
	public function test()
	{
		$this->adminmodel('setting/setting'); 
        $currentstatus=$this->model_setting_setting->getBilling('billing',170);
		print_r($currentstatus[1]);
	}
    public function addOrder() 
    {
        $log=new Log("order-open-".date('Y-m-d').".log");
        $log->write('Add order Call');
        $log->write($this->request->post);
        //$log->write($this->request->server['HTTP_UPN']);
        //$this->adminmodel('card/integration'); 
        $this->adminmodel('unit/unit');
        $this->load->model('checkout/order');
        $this->load->model('account/activity');
        $this-> adminmodel('pos/pos');
        $data = array(); 
        $mcrypt=new MCrypt();
        $this->load->model('account/api'); 
        $this->adminmodel('setting/setting'); 
        $currentstatus=$this->model_setting_setting->getBilling('billing',$mcrypt->decrypt($this->request->post['store_id']));//array(1,"Billing is closed  due to maintenance");//
		if(empty($currentstatus[0]))
		{
			$json['error']=$currentstatus[1];
			$json['success'] = "-1";
			if(isset($this->request->post['lumpsum']))
			{
				$json['dscl_submission'] = "-1";
			}
			$this->response->setOutput(json_encode($json));	
			return;
		}
	
        $api_info = $this->model_account_api->UserAuthorization($mcrypt->decrypt($this->request->post['user_id']));
        $log->write($api_info);
        if(empty($api_info))
        {
            $json['error']="User is not Authorized";
            $json['success'] = "-1";
            $this->response->setOutput(json_encode($json)); 
            return;
        }
		if($mcrypt->decrypt($this->request->post['store_id']) !=$api_info['store_id'])
		{
            $json['error']="Store user is not Authorized";
            $json['success'] = "-1";
            $this->response->setOutput(json_encode($json)); 
            return;
		}
		$log->write('before check transid');
		$log->write($mcrypt->decrypt($this->request->post['transid']));
		if(empty($mcrypt->decrypt($this->request->post['transid'])))
		{
            $json['error']="Unable to find transaction id";
            $json['success'] = "-1";
            $this->response->setOutput(json_encode($json)); 
            return;
		}
        $order_istance=$this->model_pos_pos->check_order_instance($mcrypt->decrypt($this->request->post['transid']));
		$log->write($order_istance);
        if(!empty($order_istance))
        {
            $get_bill=$order_istance;
            $log->write('order already placed with this instance '.$get_bill);
            $json['success'] = 'Success: Order already placed with this instance with ID: '.$get_bill;
            $json['order_id'] = $get_bill;
            $gtax=$this->model_checkout_order->getgtax($get_bill);
            $json['gtax']= $mcrypt->encrypt(json_encode($gtax)); 
            $this->response->setOutput(json_encode($json)); 
            return;
        }
        //$this->model_pos_pos->insert_order_instance($mcrypt->decrypt($this->request->post['transid']),$mcrypt->decrypt($this->request->post['store_id']));
        //$log->write(base64_decode($this->request->server['HTTP_UPN']));
        $keys = array(
                'store_id',
                'payment_method',
                'customer_id',
                'affiliate_id',
                'user_id',
                'prddtl',
                'customer_mobile',
                'customer_mob',
                'amtcash',
                'subcash',
                'sub',
                'docs',
                'doc_number',
                'comment',
                'stock_fm',                
                'coupon',
                'credit_amount',
                'billtype',
				'transid',
				'discount',
            'reward'
                );
        foreach ($keys as $key) 
        {
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
        }
		$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' => ($this->request->post['user_id']),
						'data'        => json_encode($this->request->post),
					);

		if($this->request->post['billtype']!=1)
		{
			$this->model_account_activity->addActivity('Order-Led-Billing', $activity_data);
		}
		else
		{
			$this->model_account_activity->addActivity('Order-Open-Billing', $activity_data);
		}
        $log->write('After Decrypt');
        $log->write($this->request->post);
        $log->write('After  Print  Decrypted value');
        //check for mobile is set of not
        if(empty($this->request->post['customer_mob']))
        { 
            //mobile number not defined
            $json['error']="Mobile number not defined";
            $json['success'] = "-1";
            $this->response->setOutput(json_encode($json)); 
            return;
        }
        $prds=json_decode($this->request->post[prddtl],true);
		//$log->write("product");
		//$log->write($prds);
		if(empty($prds))
        { 
            //mobile number not defined
            $json['error']="No product found";
            $json['success'] = "-1";
            $this->response->setOutput(json_encode($json)); 
            return;
        }

        unset($this->session->data['user_id']);
        $this->session->data['user_id']=$this->request->post['user_id'] ;
        $customer_id =$this->request->post['customer_id'];
        $log->write("init user-".$customer_id);
        $log->write("user phone= ".$this->request->post['customer_mob']);
        //check customer
        if(isset($customer_id))
        {
            $log->write("user id in customer_mob -".$this->request->post['customer_mob']);
			$log->write("user id in store_id -".$this->request->post['store_id']);
            $customer_id=$this->model_pos_pos->getCustomerByPhoneStore($this->request->post['customer_mob'],$this->request->post['store_id'])["customer_id"];
            $log->write($customer_id);            
            if(empty($customer_id))
            {
                 $log->write("in customer add");
                $this->addcustomer($this->request->post['store_id']);
                $customer_id=$this->model_pos_pos->getCustomerByPhoneStore($this->request->post['customer_mob'],$this->request->post['store_id'])["customer_id"];
                $log->write("user= ".$customer_id);
                $this->request->post['customer_id']=$customer_id;
            }
        }
		$log->write('customer_id- '.$customer_id);
        $customer_r=$this->model_pos_pos->getCustomer($customer_id);
		$log->write('customer_r');
		$log->write($customer_r);
		if((!empty($this->request->post['reward'])) && ($this->request->post['reward']!='0.0'))
        {
            /////////verify otp
            $this->adminmodel('user/user');
            $checkOtpTrans=$this->model_user_user->getVerifyUserOtp($mcrypt->decrypt($this->request->post['sid']));
            $log->write($checkOtpTrans);
            if(!empty($checkOtpTrans))
            {
                if((!empty($checkOtpTrans['otp']))&&(strlen($checkOtpTrans['otp'])==4)&&($checkOtpTrans['otp']==$mcrypt->decrypt($this->request->post['ttp'])))
                {
                }
                else 
                {
                    $json['error']="OTP not matched with the system.";
                    $json['success'] = "-1";
                    $this->response->setOutput(json_encode($json)); 
                    return;
                    
                }
            }
            else 
            {
                $json['error']="OTP not matched with the system.";
                $json['success'] = "-1";
                $this->response->setOutput(json_encode($json)); 
                return;
            }
                
            unset($this->session->data['reward']);
            unset($this->session->data['points']);
            /////check the customer reward
			
            if($customer_r['reward']<$this->request->post['reward'])
            {
                $json['error']="Availbale reward points are less then trying to redeem";
                $json['success'] = "-1";
                $this->response->setOutput(json_encode($json)); 
                return;
            }
            else
            {
                $this->session->data['reward']=$this->request->post['reward'] ;
                $this->session->data['points']=$customer_r['reward'] ;
            }
            
        }
        $log->write('session_reward');
        $log->write($this->session->data['reward']);
        
        $data['store_id'] = $this->request->post['store_id'];
        $this->config->set('config_store_id',$data['store_id']);
        $this->load->model('catalog/product');
        $this-> adminmodel('setting/store');
        $this->load->library('user');
        $this->user = new User($this->registry);
        $this->load->library('customer');
		$this->customer = new Customer($this->registry);
		
		$this->load->library('tax');//
		$this->tax = new Tax($this->registry);
        $this->tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
		$this->load->library('pos_cart');//
		$this->cart = new Pos_cart($this->registry);
        $data['order_product'] = array();
		$log->write("product data ");
		//$log->write($prds);

		foreach ($prds as $productt) 
		{
            $option_data = array();
			$log->write($productt['product_id']);
			$log->write("product_tax_per ");
			$product_tax_per=json_decode($productt['product_tax_per'],true);
            $log->write($product_tax_per);
			$k=array_keys($product_tax_per);
			$log->write($product_tax_per[(int)$k[0]]);
			
					
			$log->write('in product loop category_id '.$mcrypt->decrypt($productt['category_id']));
			if($mcrypt->decrypt($productt['category_id'])=='44')
			{
				$product = $this->model_catalog_product->getProductOpenUnVerified($productt['product_id'],$this->request->post['billtype']);
			}
			else
			{
				$product = $this->model_catalog_product->getProductOpen($productt['product_id'],$this->request->post['billtype'],$mcrypt->decrypt($productt['category_id']));
			}
			$this->request->post['billtype']=$product['subtract'];
            //$log->write("product data from db ");
            //$log->write($product);
            if(empty($product['product_id']))
            {
			$json['error']="Product not found !";
			$json['success'] = "-1";
			$this->response->setOutput(json_encode($json)); 
			return;
            }
            if($this->request->post['billtype']!=1)
            {
				if($product['squantity']>=$productt['product_quantity'])
                {
					
				}
				else
				{
					if(empty($productt['product_mitra']))
					{
						$json['error']="Product quantity not found";
						$json['success'] = "-1";
						$this->response->setOutput(json_encode($json)); 
						return;
					}
				}	
            }
            
            $log->write("user tax");
            $log->write($product['price']);
            $log->write( $product['tax_class_id']);
            $log->write($this->tax->getTax($product['price'], $product['tax_class_id']));
			if($customer_r['unnati_mitra']==1)
			{
				$points = $this->model_catalog_product->getProductReward(array('product_id'=>$product['product_id'],'store_id'=> $this->request->post['store_id']));
            }
			else
			{
				$log->write('customer is not unnati mitra');
				$points =0;
			}
            $log->write("points");
            $log->write($points);  
            if(!empty($productt['product_mitra']) && (empty($points)) && (!empty($this->request->post['reward'])) && ($this->request->post['reward']!='0.0'))
            {
                $log->write("No points found for this product ");
                //$json['error']="No points found for this product";
                //$json['success'] = "-1";
                //$this->response->setOutput(json_encode($json)); 
                //return;
            }
            $productt['product_price']=str_replace("Rs.","",$productt['product_price']);
            $productt['product_price']=str_replace(",","",$productt['product_price']);
            $data['order_product'][] = array(
				'subtract'=>(int)$product['subtract'],
				'product_id' => (int)$product['product_id'],
				'name' => $product['name'],
				'model' => $product['model'], 
				'quantity' => (int)$productt['product_quantity'], 
				'product_mitra' => (int)$productt['product_mitra'],
				'price' => (float)$productt['product_price'],
				'total' => (float)($productt['product_price']*$productt['product_quantity']),
				'tax' => (float)($this->tax->getTax($productt['product_price'], $product['tax_class_id'])),
				'reward' => $points,
				'tax_class_id' => $product['tax_class_id'],
				'tax_class_name' => $product_tax_per[$k[0]]['name'],
				'tax_class_rate' => $product_tax_per[$k[0]]['rate'],
				'order_option' => $option_data,
                'category_id'=>(int)$mcrypt->decrypt($productt['category_id'])
			);
            $this->cart->add($productt['product_id'], $productt['product_quantity'], $option,'',$this->request->post['billtype'],(float)$productt['product_price']);
        }//foreach products 
        $log->write("final product data ");
		$log->write($data['order_product']);
        $log->write("redeem_points");
        $log->write($this->request->post['reward']);
		//$log->write($data['order_product']);
        unset($this->session->data['redeem_points']);
        $this->session->data['redeem_points']=$this->request->post['reward'];//$prd_total_price ;
        //$reward=$prd_total_price-($this->request->post['reward']);
        $data['reward'] = $this->request->post['reward'];
		
        $log->write("after product submit");
        unset($this->session->data['shipping_method']);         
        $errors = '';
        $payment_method = $this->request->post['payment_method'];
        $is_guest = $this->request->post['is_guest'];
        //$customer_id =$this->request->post['customer_id'];
        $card_no = $this->request->post['card_no'];
        $data['comment'] = $this->request->post['comment'];
        if($is_guest=='false' && $customer_id=='')
        {
            $errors .= 'Select the customer.<br />';  
        }
        if(($payment_method == 'Card') && $card_no=='')
        {
            $errors .= 'Enter the card number.<br />';
        }
        if($errors != '')
        { 
            $data['errors'] = $errors;
            $this->response->setOutput(json_encode($data));
            return;
        }
        $data['billtype'] =(int) $this->request->post['billtype'];
        $data['store_id'] = $this->request->post['store_id'];
        $data['credit_amount']=$this->request->post['credit_amount'];
        $default_country_id = $this->config->get('config_country_id');
        $default_zone_id = $this->config->get('config_zone_id');
        $data['shipping_country_id'] = $default_country_id;
        $data['shipping_zone_id'] = $default_zone_id;
        $data['payment_country_id'] = $default_country_id;
        $data['payment_zone_id'] = $default_zone_id;
		$data['transid']=$this->request->post['transid'];
        $data['customer_id'] = 0;
        $data['customer_group_id'] = 1;
        if(!empty($mcrypt->decrypt($this->request->post['fname'])))
        {
            $data['firstname'] = $mcrypt->decrypt($this->request->post['fname']);
        }
        else
        {
            $data['firstname'] = 'Walkin';
        }
        $data['lastname'] = "Customer";
        $data['email'] = '';
        $data['telephone'] = $this->request->post['customer_mob'] ;
        $data['fax'] = ''; 
        if(!empty($mcrypt->decrypt($this->request->post['fname'])))
        {
            $data['payment_firstname'] = $mcrypt->decrypt($this->request->post['fname']);
        }
        else
        {
        $data['payment_firstname'] = 'Walkin';}
        $data['payment_lastname'] = "Customer";
        $data['payment_company'] = '';//$this->request->post['spray'];
        $data['payment_company_id'] = '';
        $data['payment_tax_id'] = '';
        $data['payment_address_1'] = '';
        $data['payment_address_2'] = '';
        $data['payment_city'] = '';
        $data['payment_postcode'] = '';
        $data['payment_country_id'] = '';
        $data['payment_zone_id'] = '';
        $data['payment_method'] = $payment_method;
        $data['payment_code'] = 'in_store';
        if($payment_method=='Cash')
        {
            $data['shipping_firstname'] = $mcrypt->decrypt($this->request->post['growercode']) ;
        }
        elseif($payment_method=='Subsidy')
        {
            $data['shipping_firstname'] = $mcrypt->decrypt($this->request->post['growercode']) ;
        }
        $data['shipping_firstname'] = '';
        $data['shipping_lastname'] = '';
        $data['shipping_company'] = '';
        $data['shipping_address_1'] = '';
        $data['shipping_address_2'] = '';
        $data['shipping_city'] = '';
        $data['shipping_postcode'] = '';
        $data['shipping_country_id'] = '';
        $data['shipping_zone_id'] = ''; 
        $data['shipping_method'] = 'Pickup From Store';
        $data['shipping_code'] = 'pickup.pickup';
        $log->write("payment_method");
        $log->write($payment_method);
        $data['order_status_id'] = 5;
        $this->request->post['order_status_id']=5;
        $data['affiliate_id'] = isset( $this->request->post['affiliate_id'])? $this->request->post['affiliate_id']:0;
 
		$data['card_no'] = $card_no;
		$log->write("data");
		$data['user_id'] = $this->request->post['user_id'];
		$log->write("user id");
		$log->write($customer_id);
		$is_guest='false';
		//override for customer 
		if($is_guest=='false')
		{
            $log->write("false");
            $customer = $this->model_pos_pos->getCustomer($customer_id);
            $log->write($customer);
            $this->session->data['customer_id']=$customer_id;
            $data['customer_id'] = $customer_id;
            $data['customer_group_id'] = $customer['customer_group_id'];
            $data['firstname'] = $customer['firstname'];
            $data['lastname'] = $customer['lastname'];
            $data['email'] = $customer['email'];
            $data['telephone'] = $customer['telephone'];
            $data['fax'] = $customer['fax'];
            $data['payment_firstname'] = $mcrypt->decrypt($this->request->post['growername']);//$customer['firstname'];
            $data['payment_lastname'] = $mcrypt->decrypt($this->request->post['aadhar_number']);
		} 
        $this->load->library('sms'); 
		$this-> adminmodel('pos/extension');
        $total_data = array(); 
		$total = 0;
		$taxes = $this->cart->getTaxes();
               
		if(isset($this->request->post['discount']) && (!empty($this->request->post['discount'])))
		{
			$this->session->data['discount']=$this->request->post['discount'];
			$data['discount']=$this->request->post['discount'];
		}
		$log->write("after getTaxes");
        $log->write($taxes);
		// Display prices
		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) 
		{
            $sort_order = array();
            $results = $this->model_pos_extension->getExtensions('total');
            foreach ($results as $key => $value) 
            {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }
            array_multisort($sort_order, SORT_ASC, $results);
            $log->write("before order total loop");
            $log->write($results);
            //$log->write($this->config);
            foreach ($results as $result) 
            {
				if ($this->config->get($result['code'] . '_status')) 
				{
					$log->write($result['code']);
					$this-> adminmodel('pos/' . $result['code']);
                    $this->{'model_pos_' . $result['code']}->getTotal($total_data, $total, $taxes);
					$log->write('after get value');					
                    $log->write($result['code']);
				}
                $sort_order = array();
                foreach ($total_data as $key => $value) 
				{
                    $sort_order[$key] = $value['sort_order'];
				}
                array_multisort($sort_order, SORT_ASC, $total_data); 
            }
		}
        $log->write("final total data ");
        $log->write($total_data);
        $data['amtcash']=$this->request->post['amtcash'];
        $data['subsidy']=$this->request->post['subcash'];
        $data['sub']=$this->request->post['sub'];
        $data['order_total'] = $total_data;
        $this->load->library('user');
        $this->user = new User($this->registry);
        if(isset($this->session->data['voucher']))
        {
            $data['order_voucher'] = $this->session->data['voucher'];
        }
        $json['customer_name'] = $data['firstname'].' '.$data['lastname'];
		$log->write($json);
		if(empty($json['customer_name']))
		{
			$json['customer_name']='NA';
			$log->write($json);
		}
        
        $this->adminmodel('pos/credit');
 
        $databypos=$this->model_pos_pos->addOrderOpen($data); 
		$log->write("addOrderOpen end");
		$log->write("databypos");
        $order_id=$databypos['order_id']; 
        $log->write($databypos);
        if(!empty($data['credit_amount']))
        {
            
            $order_info=array();
            $order_info['customer_id']=$customer_id;
            $order_info['order_id']=$order_id;
            $order_total=array();
            $order_total['value']=-$data['credit_amount'];
            $this->model_pos_credit->confirm($order_info,$order_total);
        }
        $data['oid']=$order_id;
        unset($this->session->data['discount_amount']);
        unset($this->session->data['reward']);
        unset($this->session->data['redeem_points']);

        $cash = $total;
        $card = 0;
        $datapayment = array(
            'user_id' => $this->request->post['user_id'],
            'cash' => $cash,
            'card' => $card, 
            'store_id'=>$this->request->post['store_id'],
            'order_id'=>$order_id,
            'payment_method'=>$this->request->post['payment_method'],
            'total'=>$total
        );
        if($this->request->post['payment_method'] == 'Cash')
        {
            $log->write('Payment Method is: '.$this->request->post['payment_method']); 
            $log->write($datapayment); 
            $this->model_pos_pos->addPayment($datapayment); 
        }
 
        $json['order_id'] = $order_id;
        $log->write("Genereted Invoice number - ".$order_id);
        $balance = 0;//$this->model_pos_pos->get_user_balance($this->user->getId());
        $json['cash'] = 0;//$this->currency->format($balance['cash']);
        $json['card'] = 0;//$this->currency->format($balance['card']);
        $log->write("done----".$customer_id);

        if (isset($this->request->post['order_status_id'])) 
        {
            $order_status_id = $this->request->post['order_status_id'];
        } 
        else 
        {
            $order_status_id = $this->config->get('config_order_status_id');
        }
        $json['invoice_no']=$databypos['inv_no'];//$data['store_id'].'-'.date('Y').'-'.
        $json['success'] = 'New order placed';//with ID: '.$json['invoice_no'];
 
        $json['orddate'] = date('Y-m-d h:i:s A');
        $log->write($json);
        $log->write("before call to get_order_total");
        $json['coupon_discount']=0;//$this->model_pos_pos->get_order_total($order_id,'coupon');
        $log->write("after call to get_order_total");
 
        $gtax=$this->model_checkout_order->getgtax($order_id);
        $log->write(json_encode($gtax));
        $json['gtax']= $mcrypt->encrypt(json_encode($gtax));
        $log->write($json['gtax']);
        $log->write('before sending sms');
		$sms=new sms($this->registry);
		$databypos['store_telephone']=$this->config->get('config_telephone');
		$databypos['store_name']=$this->config->get('config_name');
		$databypos['store_owner_name']=$this->config->get('config_owner');
        $sms->sendsms($this->request->post['customer_mob'],"2",$databypos);
		
		$this->load->library('email');
		$email=new email($this->registry);
		$mailbody = "<p style='border: 0px solid silver;padding: 15px;'>
										Dear ".$databypos['store_owner_name'].",<br/>
										New order has been placed with invoice number: <b>".$databypos['inv_no']."</b> and following details:
										
										<br/>";
										$mailbody=$mailbody." <table style='border: 1px solid silver; width: 99%;'>
										<tr>
										<td style='border-right: 1px solid silver;border-top: 1px solid silver;width: 30%;padding: 5px;'><b>Reference No</b></td>
										<td style='border-top: 1px solid silver;width: 70%;padding: 5px;'>".$order_id."</td>
										</tr>
										<tr>
										<td style='border-right: 1px solid silver;border-top: 1px solid silver;width: 30%;padding: 5px;'><b>Store Name</b></td>
										<td style='border-top: 1px solid silver;width: 70%;padding: 5px;'>".$databypos['store_name']."</td>
										</tr>
										<tr>
										<td style='border-right: 1px solid silver;border-top: 1px solid silver;width: 30%;padding: 5px;'><b>Customer Name</b></td>
										<td style='border-top: 1px solid silver;width: 70%;padding: 5px;'>".$data['firstname']."</td>
										</tr>
										<td style='border-right: 1px solid silver;border-top: 1px solid silver;width: 30%;padding: 5px;'><b>Customer Telephone</b></td>
										<td style='border-top: 1px solid silver;width: 70%;padding: 5px;'>".$this->request->post['customer_mob']."</td>
										</tr> "; 
										
										$mailbody=$mailbody."
										</table><br/><br/>";
										
										$mailbody=$mailbody." <table style='border: 1px solid silver; width: 99%;'>
										<thead>
										<tr><td colspan='6'><center><b>Item Details</b></center></td></tr>
										<tr>
										<th style='border-right: 1px solid silver;border-top: 1px solid silver;'>Sr. No.</th>
										<th style='border-right: 1px solid silver;border-top: 1px solid silver;'>Product Name</th>
										<th style='border-right: 1px solid silver;border-top: 1px solid silver;'>Quantity</th>
										<th style='border-right: 1px solid silver;border-top: 1px solid silver;'>Unit Price</th>
										<th style='border-right: 1px solid silver;border-top: 1px solid silver;'>Tax</th>
										<th style='border-top: 1px solid silver;'>Total</th>
										</tr></thead> ";
										$pn=1;
										foreach($data['order_product'] as $prrd)
										{
											$mailbody=$mailbody."<tr>
											<td style='border-right: 1px solid silver;border-top: 1px solid silver;text-align: center;'>".$pn."</td>
											<td style='border-right: 1px solid silver;border-top: 1px solid silver;text-align: center;'>".$prrd['name']."</td>
											<td style='border-right: 1px solid silver;border-top: 1px solid silver;text-align: center;'>".$prrd['quantity']."</td>
											<td style='border-right: 1px solid silver;border-top: 1px solid silver;text-align: center;'>".RUPPE_SIGN. number_format((float)$prrd['price'],2)."</td>
											<td style='border-right: 1px solid silver;border-top: 1px solid silver;text-align: center;'>".RUPPE_SIGN. number_format((float)$prrd['tax'],2)."</td>
											<td style='border-top: 1px solid silver;text-align: left;padding-left: 10px;'>".RUPPE_SIGN. number_format((float)($prrd['quantity']*($prrd['price']+$prrd['tax'])),2)."</td>
											</tr> ";
											$pn++;
										}
										
										$mailbody=$mailbody."
										<tr>
										<td colspan='4' style='border-top: 1px solid silver;'></td>
										<td style='border-left: 1px solid silver;border-right: 1px solid silver;border-top: 1px solid silver;text-align: left;padding-left: 10px;'><b>Discount</b></td>
										<td style='border-top: 1px solid silver;text-align: left;padding-left: 10px;'>".RUPPE_SIGN. number_format((float)($this->request->post['discount']),2)."</td>
										</tr>
										<tr>
										<td colspan='4'></td>
										<td style='border-left: 1px solid silver;border-right: 1px solid silver;border-top: 1px solid silver;text-align: left;padding-left: 10px;'><b>Order Total</b></td>
										<td style='border-top: 1px solid silver;text-align: left;padding-left: 10px;'>".RUPPE_SIGN. number_format((float)$databypos['total'],2)."</td>
										</tr>
										<tr>
										<td colspan='4'></td>
										<td style='border-left: 1px solid silver;border-right: 1px solid silver;border-top: 1px solid silver;text-align: left;padding-left: 10px;'><b>Cash</b></td>
										<td style='border-top: 1px solid silver;text-align: left;padding-left: 10px;'>".RUPPE_SIGN. number_format((float)$this->request->post['amtcash'],2)."</td>
										</tr>
										<tr>
										<td colspan='4'></td>
										<td style='border-left: 1px solid silver;border-right: 1px solid silver;border-top: 1px solid silver;text-align: left;padding-left: 10px;'><b>Credit</b></td>
										<td style='border-top: 1px solid silver;text-align: left;padding-left: 10px;'>".RUPPE_SIGN. number_format((float)($this->request->post['credit_amount']),2)."</td>
										</tr>
										</tbody></table>
										<br/><br/> 
										This is a computer generated email. Please do not reply to this email.
										
										<br/><br/>
										<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
										<br/><br/>
										Thanking you,
										
										<br/>
										AgriPOS
			
										<br/><br/>
										<span  style='font-size:7.5pt;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
										</p>";
        //$email->sendmail('New Order Placed - '.$databypos['inv_no'],$mailbody,$this->config->get('config_email'),array(),array('vipin.kumar@aspltech.com','hrishabh.gupta@unnati.world',"chetan.singh@akshamaala.com","sumit.kumar@aspltech.com"));
                           
						   
		$log->write('after sending sms');
        $log->write($json);
        $log->write("complete");
        $this->response->setOutput(json_encode($json));

    }//END add order
    public function getgtax()
    {
        $this->load->model('checkout/order');
        $gtax=$this->model_checkout_order->getgtax($this->request->get['order_id']);
        print_r(json_encode($gtax));
    }
    //ADD Product 
    function addproduct()
    {        
        $log=new Log("addproduct-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
	$log->write('Addproduct called');
        $log->write($this->request->post);
        $log->write($this->request->get);
        $data=array();
	$data['PostedBy']=$mcrypt->decrypt($this->request->post['userId']);        
        $data['productname']=$mcrypt->decrypt($this->request->post['ProductName']);
        $data['sku']=$mcrypt->decrypt($this->request->post['SKU']);
        $data['hstncode']=$mcrypt->decrypt($this->request->post['HstnCode']);
        $data['gsttype']=$mcrypt->decrypt($this->request->post['GstType']);        
        $data['ImageCount']=$mcrypt->decrypt($this->request->post['ImageCount']);
        $data['category_id']=$mcrypt->decrypt($this->request->post['Categoryid']);
		$data['store_id']=$mcrypt->decrypt($this->request->post['store_id']);
		$data['gst_change']=$mcrypt->decrypt($this->request->post['gst_change']);
		$data['ProductQuantity']=$mcrypt->decrypt($this->request->post['ProductQuantity']);
		
		$data['product_full_price']=$mcrypt->decrypt($this->request->post['product_full_price']);
		$data['product_base_price']=$mcrypt->decrypt($this->request->post['product_base_price']);
		
		$data['user_category_id']=$mcrypt->decrypt($this->request->post['category_id']);
		$data['user_category_name']=$mcrypt->decrypt($this->request->post['category_name']);
		if(empty($data['user_category_id']))
		{
			$data['user_category_id']=0;
		}
		
		if($data['category_id']==100)
		{
			$data['category_id']=44;
		}
		
		else if($data['gsttype']==0)
		{
			$data['gsttype']=12;
			$data['gsttypename']='No-TAX';
		}
		else if($data['gsttype']==1)
		{
			$data['gsttype']=14;
			$data['gsttypename']='GST@5%';
		}
		else if($data['gsttype']==2)
		{
			$data['gsttype']=15;
			$data['gsttypename']='GST@12%';
		}
		else if($data['gsttype']==3)
		{
			$data['gsttype']=16;
			$data['gsttypename']='GST@18%';
		}
		else if($data['gsttype']==4)
		{
			$data['gsttype']=17;
			$data['gsttypename']='GST@28%';
		}
		else if($data['gsttype']==0)
		{
			$data['gsttype']=12;
			$data['gsttypename']='No-TAX';
		}
		else if($data['gsttype']==12)
		{
			$data['gsttype']=12;
			$data['gsttypename']='GST@12%';
		}
		else if($data['gsttype']==18)
		{
			$data['gsttype']=18;
			$data['gsttypename']='GST@18%';
		}
		
        $data['company_name']=$mcrypt->decrypt($this->request->post['Companyname']); 
        $data['company_id']=$mcrypt->decrypt($this->request->post['Companyid']); 
		/*
        $data['PostedBy']=$mcrypt->decrypt($this->request->post['userId']);        
        $data['productname']=$mcrypt->decrypt($this->request->post['ProductName']);
        $data['sku']=$mcrypt->decrypt($this->request->post['SKU']);
        $data['hstncode']=$mcrypt->decrypt($this->request->post['HstnCode']);
        $data['gsttype']=$mcrypt->decrypt($this->request->post['GstType']);        
        $data['ImageCount']=$mcrypt->decrypt($this->request->post['ImageCount']);
        $data['category_id']=$mcrypt->decrypt($this->request->post['Categoryid']);
		
		if($data['category_id']==100)
		{
			$data['category_id']=27;
		}
		
		if($data['gsttype']==0)
		{
			$data['gsttype']=12;
		}
		if($data['gsttype']==1)
		{
			$data['gsttype']=14;
		}
		if($data['gsttype']==2)
		{
			$data['gsttype']=15;
		}
		if($data['gsttype']==3)
		{
			$data['gsttype']=16;
		}
		if($data['gsttype']==4)
		{
			$data['gsttype']=17;
		}
		
        $data['company_name']=$mcrypt->decrypt($this->request->post['Companyname']); 
        $data['company_id']=$mcrypt->decrypt($this->request->post['Companyid']); 
		*/
		$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' => ($data['PostedBy']),
						'data'        => json_encode($data),
					);

		
		$this->model_account_activity->addActivity('addproduct', $activity_data);
		
		
        $this->adminmodel('openretailer/openretailer');
        $log->write("model");
        $log->write($data);
        $datas=array();
        if( !empty($data['productname']) )
        {
            $log->write("in if");
           
            $prdid = $this->model_openretailer_openretailer->addproduct($data);
            $datas['success']=$mcrypt->encrypt("Product data submitted successfully.");
            $datas['product']=$mcrypt->encrypt($prdid);
            $datas['product_id']=$mcrypt->encrypt($prdid);
        }        
        else
        {
            $log->write("in else");
            $datas['success']=$mcrypt->encrypt("Product Name can not empty");
        }
        if(!empty($datas))
        {
            $this->response->setOutput(json_encode($datas,JSON_UNESCAPED_UNICODE));
           
        }            
    }
	//ADD Product 
    function addproductunverified()
    {        
        $log=new Log("addproductunverified-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
		$log->write('addproductunverified called');
        $log->write($this->request->post);
        $log->write($this->request->get);
        $data=array();
		$data['manage_inv']=$mcrypt->decrypt($this->request->post['manage_inv']); 
		
		if($data['manage_inv']=='true')
		{
			$data['subtract']=0;
		}	
		else
		{
			$data['subtract']=1;
		}
        $data['PostedBy']=$mcrypt->decrypt($this->request->post['userId']);        
        $data['productname']=$mcrypt->decrypt($this->request->post['ProductName']);
        $data['sku']=$mcrypt->decrypt($this->request->post['SKU']);
        $data['hstncode']=$mcrypt->decrypt($this->request->post['HstnCode']);
        $data['gsttype']=$mcrypt->decrypt($this->request->post['GstType']);        
        $data['ImageCount']=$mcrypt->decrypt($this->request->post['ImageCount']);
        $data['category_id']=$mcrypt->decrypt($this->request->post['Categoryid']);
        $data['p_category_id']=$mcrypt->decrypt($this->request->post['P_Categoryid']);
		$data['store_id']=$mcrypt->decrypt($this->request->post['store_id']);
		$data['gst_change']=$mcrypt->decrypt($this->request->post['gst_change']);
		$data['ProductQuantity']=$mcrypt->decrypt($this->request->post['ProductQuantity']);
		
		$data['product_full_price']=$mcrypt->decrypt($this->request->post['product_full_price']);
		$data['product_base_price']=$mcrypt->decrypt($this->request->post['product_base_price']);
		
		$data['user_category_id']=$mcrypt->decrypt($this->request->post['category_id']);
		$data['user_category_name']=$mcrypt->decrypt($this->request->post['category_name']);
		if(empty($data['user_category_id']))
		{
			$data['user_category_id']=0;
		}
		
		if($data['category_id']==100)
		{
			$data['category_id']=44;
		}
		
		else if($data['gsttype']==0)
		{
			$data['gsttype']=12;
			$data['gsttypename']='No-TAX';
		}
		else if($data['gsttype']==1)
		{
			$data['gsttype']=14;
			$data['gsttypename']='GST@5%';
		}
		else if($data['gsttype']==2)
		{
			$data['gsttype']=15;
			$data['gsttypename']='GST@12%';
		}
		else if($data['gsttype']==3)
		{
			$data['gsttype']=16;
			$data['gsttypename']='GST@18%';
		}
		else if($data['gsttype']==4)
		{
			$data['gsttype']=17;
			$data['gsttypename']='GST@28%';
		}
		else if($data['gsttype']==0)
		{
			$data['gsttype']=12;
			$data['gsttypename']='No-TAX';
		}
		else if($data['gsttype']==12)
		{
			$data['gsttype']=12;
			$data['gsttypename']='GST@12%';
		}
		else if($data['gsttype']==18)
		{
			$data['gsttype']=18;
			$data['gsttypename']='GST@18%';
		}
	
        $data['company_name']=$mcrypt->decrypt($this->request->post['Companyname']); 
        $data['company_id']=$mcrypt->decrypt($this->request->post['Companyid']); 
		$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' => ($data['PostedBy']),
						'data'        => json_encode($data),
					);
	$this->model_account_activity->addActivity('addproductunverified', $activity_data);
        $this->adminmodel('openretailer/openretailer');
        $log->write("model");
        $log->write($data);
        $datas=array();
        if( !empty($data['productname']) )
        {
            $log->write("in if");
           	$data['status']=1;
            $prdid = $this->model_openretailer_openretailer->addproductunverified($data); 
            $datas['success']=$mcrypt->encrypt("Product data submitted successfully.");
            $datas['product']=$mcrypt->encrypt($prdid);
            $datas['product_id']=$mcrypt->encrypt($prdid);
        }        
        else
        {
            $log->write("in else");
            $datas['success']=$mcrypt->encrypt("Product Name can not empty");
        }
        if(!empty($datas))
        {
            $this->response->setOutput(json_encode($datas,JSON_UNESCAPED_UNICODE));
           
        }            
    }
	
    public function getCredit()
    {
        $mcrypt=new MCrypt();
	$this->adminmodel('openretailer/openretailer');
	$m =$mcrypt->decrypt($this->request->post['mobile']);
	//$jsons = $this->model_openretailer_openretailer->getCredit($m);
        $json['credit']='1212';
        $this->response->setOutput(json_encode($json));
    }
    public function addcustomer($sid)
    {
        $mcrypt=new MCrypt(); 
        $this->request->post['card']="0"; 
		
        if(!empty($this->request->post['fname']))
        {
            $this->request->post['firstname']=$mcrypt->decrypt($this->request->post['fname']).'-'.$mcrypt->decrypt($this->request->post['lname']);
        }
		
        if(!empty($this->request->post['growername']))
        {
            $this->request->post['firstname']=$mcrypt->decrypt($this->request->post['growername']);
        }
        if(!empty($this->request->post['aadhar_number']))
        {
            $this->request->post['aadhar']=$mcrypt->decrypt($this->request->post['aadhar_number']);
        }
 
        $this->request->post['lastname']='';
        if(isset($this->request->post['vname']))
        {
            $this->request->post['village']=$mcrypt->decrypt($this->request->post['vname']); 
        } 
        else if(isset($this->request->post['growercode']))
        {
            $this->request->post['village']=$mcrypt->decrypt($this->request->post['growercode']); 
        } 
        if(!empty($this->request->post['ip']))
        {
            $this->request->post['ip']=$mcrypt->decrypt($this->request->post['ip']);
        }
		$this->request->post['firstname']=strtoupper($this->request->post['firstname']);
        $this->adminmodel('sale/customer'); 
        unset($this->session->data['cid']);
        $this->request->post['email']=$this->request->post['customer_mob'];
        $this->request->post['fax']=$this->request->post['customer_mob'];
        $this->request->post['telephone']=$this->request->post['customer_mob'];
        $this->request->post['customer_group_id']="1";
        $this->request->post['password']=$this->request->post['customer_mob'];
        $this->request->post['newsletter']='0'; 
        $this->request->post['approved']='1';
        $this->request->post['status']='1';
        $this->request->post['safe']='1';
        $this->request->post['address_1']= $this->request->post['village'];
        $this->request->post['address_2']= $this->request->post['village'];
        $this->request->post['city']= $this->request->post['village'];
        $this->request->post['ip']= $this->request->post['ip'];
        $this->request->post['company']='Unnati';
        $this->request->post['country_id']='0';
        $this->request->post['zone_id']='0';
        $this->request->post['postcode']='0';
        $this->request->post['store_id']=$sid; 
        $this->request->post['address']=array($this->request->post);
        $this->model_sale_customer->addCustomer($this->request->post); 
	$this->load->model('account/activity');
	$activity_data = array(
						'customer_id' => ($sid),
						'data'        => json_encode($this->request->post),
					);
	$this->model_account_activity->addActivity('addcustomer', $activity_data);
    }

    function updatequantity()   
    {
        $log=new Log("updatequantity-".date('Y-m-d').".log");
        $log->write('updatequantity called');
        $log->write($this->request->post);
		$mcrypt=new MCrypt();
		$keys = array(
		'username',
                    'store_id',
                    'product_id',
                    'quantity'
                    );
        foreach ($keys as $key) 
        {            
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
        }
		$log->write($this->request->post);
		$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' => ($this->request->post['username']),
						'data'        => json_encode($this->request->post),
					);
	$this->model_account_activity->addActivity('updatequantity', $activity_data);
		$this-> adminmodel('catalog/product');
        $output=$this->model_catalog_product->openretailerupdateqty($this->request->post);
		$this->response->setOutput($output);				
    }
    function updateprice()   
    {
        $log=new Log("openretailer-price-".date('Y-m-d').".log");
        $log->write('updateprice called');
        $log->write($this->request->post);
        $mcrypt=new MCrypt();
		$keys = array(
		'username',
		'store_id',
		'product_id',
		'price',
		'inv'
		);
        foreach ($keys as $key) 
        {            
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
        }
	$log->write($this->request->post);
	$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' => ($this->request->post['username']),
						'data'        => json_encode($this->request->post),
					);
	$this->model_account_activity->addActivity('updateprice', $activity_data);
	$this-> adminmodel('catalog/product');
	$log->write("model call");
        $output=$this->model_catalog_product->openretailerupdateprice($this->request->post);
	if($this->request->post['inv']>0)
	{
		$log->write("in if for quantity update");
		$this->request->post['quantity']=$this->request->post['inv'];
		$this->model_catalog_product->openretailerupdateqty($this->request->post);
	}
	$log->write("model call end");
	$log->write($output);
	$output=1;
	$log->write($output);
	$this->response->setOutput($output);				
    }
	public function invoice()
    {
        $mcrypt=new MCrypt();
		//$this->request->get['order_id']=$mcrypt->encrypt(1578); 
        $order_id=$mcrypt->decrypt($this->request->get['order_id']);   
		$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' =>$order_id,
						'data'        => json_encode($order_id),
					);
	$this->model_account_activity->addActivity('invoice', $activity_data);
        $this->adminmodel('sale/order');
        if(!empty( $order_id))
        {  
            $data['order_info']=$order_info = $this->model_sale_order->getOrder($order_id);
        }
        $data['products'] = array();
                
		$products = $order_info['products'];
		foreach ($products as $product) 
		{
			$data['products'][] = array(
					'order_product_id' => $product['order_product_id'],
					'product_id'       => $product['product_id'],
					'name'    	 	   => $product['name'],
					'model'    		   => $product['model'],
					
					'quantity'		   => $product['quantity'],
					'price'    		   => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    		   => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					'href'     		   => $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $product['product_id'], 'SSL')
				);
		}
		$totals = $order_info['totals'];
        $tax_array=array();
		foreach ($totals as $total) 
        {
            $tax_rate='';
            $data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
				);
            if($total['code']=='tax')
            {
                $tax_title1= split('@', $total['title']);
                $tax_rate1= split('%', $tax_title1[1]);
                $tax_rate= $tax_rate1[0];
                if($tax_rate>0)
                {
                    $tax_array[]=array('title'=>'CGST@'.($tax_rate/2).'%','value'=>$this->currency->format(($total['value']/2), $order_info['currency_code'], $order_info['currency_value']));
                    $tax_array[]=array('title'=>'SGST@'.($tax_rate/2).'%','value'=>$this->currency->format(($total['value']/2), $order_info['currency_code'], $order_info['currency_value'])); 
                }
             }
		}
        $data['tax_array']=$tax_array;
                
		$data['order_status_id'] = $order_info['order_status_id'];
        $data['store_name'] = $order_info['store_name'];
		$this->adminmodel('openretailer/openretailer');
		
        $data['store_address'] = $this->model_openretailer_openretailer->getstoresetting($order_info['store_id'],"config_address");
        $data['store_address']= str_replace(',', ',<br>', $this->model_openretailer_openretailer->getstoresetting($order_info['store_id'],'store_address'));
        $data['store_tin'] = $this->model_openretailer_openretailer->getstoresetting($order_info['store_id'],'config_tin');
        $data['store_cin'] = $this->model_openretailer_openretailer->getstoresetting($order_info['store_id'],'config_cin');
        $data['store_gstn'] = $this->model_openretailer_openretailer->getstoresetting($order_info['store_id'],'config_gstn');
        $data['store_telephone'] = $this->model_openretailer_openretailer->getstoresetting($order_info['store_id'],'config_telephone');
        $data['config_email'] = $this->model_openretailer_openretailer->getstoresetting($order_info['store_id'],'config_email');
                
		$data['firstname'] = $order_info['firstname'];
        $data['firstname']= str_replace('-', ' ', $data['firstname']);
		$data['lastname'] = $order_info['lastname'];
		$data['telephone'] =$order_info['telephone'];
		//echo $order_info['telephone']=8447882446;
		//echo '<br/>';
        //echo $data['telephone'] = str_pad(substr($order_info['telephone'],0,-4),10, "X", STR_PAD_LEFT );
        
        if(!empty($order_info))
        {
			$this->response->setOutput($this->load->view('default/template/pos/order_summary.tpl',$data));
        }
        else 
        {
            $this->response->setOutput($this->load->view('default/template/common/not_found.tpl',$data));
        }
    }
	function addtofavouritedproduct()
    {        
        $log=new Log("addtofavouritedproduct-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
		$log->write('addtofavouritedproduct called');
        $log->write($this->request->post);
        $log->write($this->request->get);
        $data=array();
       
		$data['store_id']=$mcrypt->decrypt($this->request->post['store_id']);
		$data['product_id']=$mcrypt->decrypt($this->request->post['product_id']);
		
		$data['category_id']=44;
		
        $this->adminmodel('openretailer/openretailer');
        $log->write("model");
        $log->write($data);
		$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' => ($data['store_id']),
						'data'        => json_encode($data),
					);

		
		$this->model_account_activity->addActivity('addtofavouritedproduct', $activity_data);
        $datas=array();
        if(!empty($data['product_id']) )
        {
            $log->write("in if");
           
            $prdid = $this->model_openretailer_openretailer->addtofavouritedproduct($data); 
            $datas['msg']=$mcrypt->encrypt("Product added to my store.");
            $datas['status']=$mcrypt->encrypt(1);
        }        
        else
        {
            $log->write("in else");
            $datas['msg']=$mcrypt->encrypt("Product ID can not empty");
			$datas['status']=$mcrypt->encrypt(0);
        }
        if(!empty($datas))
        {
            $this->response->setOutput(json_encode($datas,JSON_UNESCAPED_UNICODE));
           
        }            
    }
	function remove_favourite()
    {        
        $log=new Log("addtofavouritedproduct-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
		$log->write('remove_favourite called');
        $log->write($this->request->post);
        $log->write($this->request->get);
        $data=array();
       
		$data['store_id']=$mcrypt->decrypt($this->request->post['store_id']);
		$data['product_id']=$mcrypt->decrypt($this->request->post['product_id']);
		
		$data['category_id']=44;
		$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' => ($data['store_id']),
						'data'        => json_encode($data),
					);

		
		$this->model_account_activity->addActivity('remove_favourite', $activity_data);
        $this->adminmodel('openretailer/openretailer');
        $log->write("model");
        $log->write($data);
        $datas=array();
        if(!empty($data['product_id']) )
        {
            $log->write("in if");
           
            $prdid = $this->model_openretailer_openretailer->remove_favourite($data); 
            $datas['msg']=$mcrypt->encrypt("Product removed from My Store.");
            $datas['status']=$mcrypt->encrypt(1);
      
        }        
        else
        {
            $log->write("in else");
            $datas['msg']=$mcrypt->encrypt("Product ID can not empty");
			$datas['status']=$mcrypt->encrypt(0);
        }
        if(!empty($datas))
        {
            $this->response->setOutput(json_encode($datas,JSON_UNESCAPED_UNICODE));
           
        }            
    }
	function addtobookmarkproduct()
    {        
        $log=new Log("addtobookmarkproduct-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
		$log->write('addtobookmarkproduct called');
        $log->write($this->request->post);
        $log->write($this->request->get);
        $data=array();
       
		$data['store_id']=($this->request->get['store_id']);
		$data['product_id']=($this->request->get['product_id']);
		
		$data['category_id']=44;
		$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' => ($data['store_id']),
						'data'        => json_encode($data),
					);

		
		$this->model_account_activity->addActivity('addtobookmarkproduct', $activity_data);
        $this->adminmodel('openretailer/openretailer');
		$this->load->model('catalog/product');
        $log->write("model");
        $log->write($data);
        $datas=array();
        if(!empty($data['product_id']) )
        {
            $log->write("in if");
           
            $prdid = $this->model_openretailer_openretailer->addtobookmarkproduct($data); 
            $datas['msg']=$mcrypt->encrypt("Product added to bookmark.");
            $datas['status']=(1);
			$bookmark_count = $this->model_catalog_product->getProductBookmarkCount(array('product_id'=>$data['product_id'],'store_id'=>$data['store_id']));
			$datas['bookmark_count']=$bookmark_count;
        }        
        else
        {
            $log->write("in else");
            $datas['msg']=$mcrypt->encrypt("Product ID can not empty");
			$datas['status']=$mcrypt->encrypt(0);
        }
        if(!empty($datas))
        {
            $this->response->setOutput(json_encode($datas,JSON_UNESCAPED_UNICODE));
           
        }            
    }
	function remove_bookmark()
    {        
        $log=new Log("addtobookmarkproduct-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
		$log->write('remove_bookmark called');
        $log->write($this->request->post);
        $log->write($this->request->get);
        $data=array();
       
		$data['store_id']=($this->request->get['store_id']);
		$data['product_id']=($this->request->get['product_id']);
		
		$data['category_id']=44;
		$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' => ($data['store_id']),
						'data'        => json_encode($data),
					);

		
		$this->model_account_activity->addActivity('remove_bookmark', $activity_data);
        $this->adminmodel('openretailer/openretailer');
		$this->load->model('catalog/product');
        $log->write("model");
        $log->write($data);
        $datas=array();
        if(!empty($data['product_id']) )
        {
            $log->write("in if");
           
            $prdid = $this->model_openretailer_openretailer->remove_bookmark($data); 
            $datas['msg']=$mcrypt->encrypt("Product removed from bookmark.");
			
            $datas['status']=(1);
			$bookmark_count = $this->model_catalog_product->getProductBookmarkCount(array('product_id'=>$data['product_id'],'store_id'=>$data['store_id']));
			$datas['bookmark_count']=$bookmark_count;
        }        
        else
        {
            $log->write("in else");
            $datas['msg']=$mcrypt->encrypt("Product ID can not empty");
			$datas['status']=$mcrypt->encrypt(0);
        }
        if(!empty($datas))
        {
            $this->response->setOutput(json_encode($datas,JSON_UNESCAPED_UNICODE));
           
        }            
    }
	function addproductrating()
    {        
        $log=new Log("addproductrating-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
		$log->write('addproductrating called');
        $log->write($this->request->post);
        $log->write($this->request->get);
        $data=array();
       
		$data['store_id']=$mcrypt->decrypt($this->request->get['store_id']);
		$data['product_id']=$mcrypt->decrypt($this->request->get['product_id']);
		$data['rating']=($this->request->get['rating']);
		$data['category_id']=44;
		$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' => ($data['store_id']),
						'data'        => json_encode($data),
					);

		
		$this->model_account_activity->addActivity('addproductrating', $activity_data);
        $this->adminmodel('openretailer/openretailer');
		$this->load->model('catalog/product');
        $log->write("model");
        $log->write($data);
        $datas=array();
        if(!empty($data['product_id']) )
        {
            $log->write("in if");
           
            $prdid = $this->model_openretailer_openretailer->addproductrating($data); 
            $datas['msg']=$mcrypt->encrypt("Rating Saved.");
            $datas['status']=(1);
			$review_count = $this->model_catalog_product->getProductReviewCount(array('product_id'=>$data['product_id'],'store_id'=>$data['store_id']));
			$datas['review_count']=$review_count;
        }        
        else
        {
            $log->write("in else");
            $datas['msg']=$mcrypt->encrypt("Product ID can not empty");
			$datas['status']=$mcrypt->encrypt(0);
        }
        if(!empty($datas))
        {
            $this->response->setOutput(json_encode($datas,JSON_UNESCAPED_UNICODE));
           
        }            
    }
	function product_request()
    {        
        $log=new Log("product_request-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
		$log->write('product_request called');
        $log->write($this->request->post);
        $log->write($this->request->get);
        $data=array();
       
		$data['store_id']=($this->request->get['store_id']);
		$data['store_name']=($this->request->get['store_name']);
		$data['product_id']=($this->request->get['product_id']);
		$data['product_name']=($this->request->get['product_name']);
		$data['full_name']=($this->request->get['full_name']);
		$data['mobile_number']=($this->request->get['mobile_number']);
		
        $this->adminmodel('openretailer/openretailer');
		$this->load->model('account/activity');
		$activity_data = array(
						'customer_id' => ($data['store_id']),
						'data'        => json_encode($data),
					);

		
		$this->model_account_activity->addActivity('product_request', $activity_data);
        $log->write($data);
        $datas=array();
        if((!empty($data['product_id'])) && (!empty($data['store_id']) ))
        {
            $log->write("in if");
			$prdidcheck = $this->model_openretailer_openretailer->product_request_duplicate_check($data); 
			//print_r(json_encode($prdidcheck));
			if($prdidcheck->num_rows>0)
			{
				$log->write("in if already requested");
				$datas['msg']=("You have already requested for this product");
				$datas['status']=(2);
			}
			else
			{
				$prdid = $this->model_openretailer_openretailer->product_request($data); 
				$datas['msg']=('Request Send Successfully ');
				$datas['status']=(1);
			
				$this->adminmodel('setting/setting');
				$config_telephone=$this->model_setting_setting->getSettingbykey('config','config_telephone',$data['store_id']);
				$config_name=$this->model_setting_setting->getSettingbykey('config','config_name',$data['store_id']);
				$config_owner=$this->model_setting_setting->getSettingbykey('config','config_owner',$data['store_id']);
				$this->load->library('sms');
				$sms=new sms($this->registry);
				$databypos['store_telephone']=$config_telephone;
				$databypos['store_name']=$config_name;
				$databypos['store_owner_name']=$config_owner;
				$databypos['full_name']=$data['full_name'];
				$databypos['mobile_number']=$data['mobile_number'];
				$databypos['product_name']=$data['product_name'];
				$sms->sendsms($config_telephone,"29",$databypos);
				
			}
		
        }        
        else
        {
            $log->write("in else");
            $datas['msg']=("Product ID can not empty");
			$datas['status']=(0);
        }
        if(!empty($datas))
        {
            $this->response->setOutput(json_encode($datas,JSON_UNESCAPED_UNICODE));
           
        }            
    }
}
?>
