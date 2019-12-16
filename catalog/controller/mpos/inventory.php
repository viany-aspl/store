<?php
//require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
error_reporting(0);
class Controllermposinventory extends Controller
{
    public function adminmodel($model) 
    {
        $admin_dir = DIR_SYSTEM;
        $admin_dir = str_replace('system/','backoffice/',$admin_dir);
        $file = $admin_dir . 'model/' . $model . '.php';      
        //$file  = DIR_APPLICATION . 'model/' . $model . '.php';
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
	public function resend_otp()
	{
        $mcrypt=new MCrypt();
		$log=new Log("inv-".date('Y-m-d').".log");
		$log->write('resend_otp called');
		$log->write($this->request->post);
		$json = array();
        
        $keys = array(
            'store_id',
			'user_id',
			'id',
			'username'
        );

		foreach ($keys as $key) 
		{
			$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
        }

		$log->write($this->request->post);
		$this->adminmodel('inventory/purchase_order');
		$da_data = $this->model_inventory_purchase_order->getOrderDetails(array('id' => $this->request->post['id']));
        $log->write($da_data);
		
		$this->load->library('sms');   
		$log->write(1);
		$sms=new sms($this->registry);
		$log->write(2);
		$filter_data = array(
            'user_id' => $this->request->post['user_id'],
            'order_id' => $this->request->post['id'],
            'store_mobile' => $da_data['driver_mobile'],
            'otp' => $da_data['driver_otp']
        );
		$sms->sendsms($da_data['driver_mobile'],"35",$filter_data);
        $log->write('after sending msg to farmer');
		
		$json['status'] =1;
		$json['msg'] = 'Success: Message Sent';
		$log->write($json);
		$this->response->setOutput(json_encode($json));
		
	}
    public function orderlist()
    {
        $mcrypt=new MCrypt();
        $this->adminmodel('inventory/purchase_order');
        if (isset($this->request->get['page'])) 
        {
            $page = $mcrypt->decrypt($this->request->get['page']);
        } 
        else 
        {
            $page = 1;
        }
        $log = new Log("inv-" . date('Y-m-d') . ".log");
        $start = ($page - 1) * 20;
        $limit = 20;
		$log->write($this->request->post);
        $uid = $mcrypt->decrypt($this->request->post['username']);
		
		$log->write($uid);
		$log->write($store_id);
		
		$this->adminmodel('user/user');
        $store_id = $mcrypt->decrypt($this->request->post['sid']);
		
		if(empty($store_id))
		{
			$store=$this->model_user_user->getUser($uid);
			$log->write($store['store_id']);
			$store_id=$store['store_id'];
		}
		
        $da_data = $this->model_inventory_purchase_order->getListRecStore(array('store_id' => $store_id, 'start' => $start, 'limit' => $limit));
        $log->write($da_data);
        $data = array("");
		$Pending_Approved=array();
		$Pending=array();
		$Received=array();
        foreach ($da_data as $d_data) 
        {
			$order_status_name='';
			if((empty($d_data['po_product']['sku'])) || ($d_data['po_product']['sku']==''))
			{
				$log->write('in if sku is empty');
				$log->write($d_data['po_product']['product_id']);
				$log->write($d_data['po_product']['product_id']);
				$prd=$this->model_inventory_purchase_order->get_prd_data($d_data['po_product']['product_id']);
				$d_data['po_product']['sku']=$prd['sku'];
				$log->write($d_data['po_product']['sku']);
			}
			if($d_data['status']==2)
			{
				$order_status_name='Pending/Approved';
			
				$Pending_Approved[] = array(
                'id' => $mcrypt->encrypt($d_data['id']),
                'order_date' => $mcrypt->encrypt(date('Y-m-d', $d_data['order_date']->sec)),
                'order_sup_send' => $mcrypt->encrypt(date('Y-m-d', $d_data['order_sup_send']->sec)),
                'delete_bit' => $mcrypt->encrypt($d_data['delete_bit']),
                'user_id' => $mcrypt->encrypt($d_data['user_id']),
                'receive_date' => $mcrypt->encrypt($d_data['receive_date']),
                'receive_bit' => $mcrypt->encrypt($d_data['receive_bit']),
                'pending_bit' => $mcrypt->encrypt($d_data['pending_bit']),
                'pre_supplier_bit' => $mcrypt->encrypt($d_data['pre_supplier_bit']),
                'po_product' => $mcrypt->encrypt(json_encode(array('products'=> array($d_data['po_product'])))),
                'po_receive_details' => $mcrypt->encrypt(json_encode($d_data['po_receive_details'])),
                'order_status_id' => $mcrypt->encrypt($d_data['status']),
				'status' => $mcrypt->encrypt($order_status_name),
                'canceled_message' => $mcrypt->encrypt($d_data['canceled_message']),
                'store_id' => $mcrypt->encrypt($d_data['store_id']),
                'potential_date' => $mcrypt->encrypt(date('Y-m-d', $d_data['potential_date']->sec))
				);
			}
			else if($d_data['status']==4)
			{
				$order_status_name='Received';
				$Received[] = array(
                'id' => $mcrypt->encrypt($d_data['id']),
                'order_date' => $mcrypt->encrypt(date('Y-m-d', $d_data['order_date']->sec)),
                'order_sup_send' => $mcrypt->encrypt(date('Y-m-d', $d_data['order_sup_send']->sec)),
                'delete_bit' => $mcrypt->encrypt($d_data['delete_bit']),
                'user_id' => $mcrypt->encrypt($d_data['user_id']),
                'receive_date' => $mcrypt->encrypt($d_data['receive_date']),
                'receive_bit' => $mcrypt->encrypt($d_data['receive_bit']),
                'pending_bit' => $mcrypt->encrypt($d_data['pending_bit']),
                'pre_supplier_bit' => $mcrypt->encrypt($d_data['pre_supplier_bit']),
                'po_product' => $mcrypt->encrypt(json_encode(array('products'=> array($d_data['po_product'])))),
                'po_receive_details' => $mcrypt->encrypt(json_encode($d_data['po_receive_details'])),
                'order_status_id' => $mcrypt->encrypt($d_data['status']),
				'status' => $mcrypt->encrypt($order_status_name),
                'canceled_message' => $mcrypt->encrypt($d_data['canceled_message']),
                'store_id' => $mcrypt->encrypt($d_data['store_id']),
                'potential_date' => $mcrypt->encrypt(date('Y-m-d', $d_data['potential_date']->sec))
				);
			}
			else
			{
				$order_status_name='Pending';
				$d_data['status']=0;
				$Pending[] = array(
                'id' => $mcrypt->encrypt($d_data['id']),
                'order_date' => $mcrypt->encrypt(date('Y-m-d', $d_data['order_date']->sec)),
                'order_sup_send' => $mcrypt->encrypt(date('Y-m-d', $d_data['order_sup_send']->sec)),
                'delete_bit' => $mcrypt->encrypt($d_data['delete_bit']),
                'user_id' => $mcrypt->encrypt($d_data['user_id']),
                'receive_date' => $mcrypt->encrypt($d_data['receive_date']),
                'receive_bit' => $mcrypt->encrypt($d_data['receive_bit']),
                'pending_bit' => $mcrypt->encrypt($d_data['pending_bit']),
                'pre_supplier_bit' => $mcrypt->encrypt($d_data['pre_supplier_bit']),
                'po_product' => $mcrypt->encrypt(json_encode(array('products'=> array($d_data['po_product'])))),
                'po_receive_details' => $mcrypt->encrypt(json_encode($d_data['po_receive_details'])),
                'order_status_id' => $mcrypt->encrypt($d_data['status']),
				'status' => $mcrypt->encrypt($order_status_name),
                'canceled_message' => $mcrypt->encrypt($d_data['canceled_message']),
                'store_id' => $mcrypt->encrypt($d_data['store_id']),
                'potential_date' => $mcrypt->encrypt(date('Y-m-d', $d_data['potential_date']->sec))
				);
			}
        }
		
		$data['order_list']=array_merge($Pending_Approved,$Pending,$Received);
		
        $log->write($data);
        $this->response->setOutput(json_encode($data));
    }

    public function order_details()
    {
        $mcrypt=new MCrypt();
        $log=new Log("po-receive-".date('Y-m-d').".log");
        $order_id = $mcrypt->decrypt($this->request->get['order_id']);
        $log->write($order_id);							
        $this->adminmodel('inventory/purchase_order');
        $data['order_information'] =$this->model_inventory_purchase_order->view_order_details($order_id);		
        //print_r($data['order_information']['products']);
        $log->write($data['order_information']);
        $this->response->setOutput(json_encode($data['order_information']));
    }
    /*----------------------------view_order_details function ends here--------------*/
    /*-----------------------------insert receive order function starts here-------------------*/
    public function receive_order()
    {
        $mcrypt=new MCrypt();
	$log=new Log("po-receive-".date('Y-m-d').".log");
	$this->adminmodel('inventory/purchase_order');
        $log->write($this->request->post);
	$order_id = $mcrypt->decrypt($this->request->post['order_id']);
	$received_quantities = $this->request->post['receive_quantity'];
	$suppliers_ids = $this->request->post['supplier'];
	if(!empty($received_quantities))
	{
            if(empty($mcrypt->decrypt($received_quantities[0])))
            {
                $log->write('in if quantity under array is empty');
                $datas['error'] = $mcrypt->encrypt('Please Enter Quantity !');
                $datas['receive_message'] = $mcrypt->encrypt('0');
                $this->response->setOutput(json_encode($datas));
		return;
            }
            $database_quantity= $this->model_inventory_purchase_order->check_quantity($order_id); 
            if($mcrypt->decrypt($received_quantities[0])!=$database_quantity)
            {
                $datas['error'] = $mcrypt->encrypt('Please Enter Correct Quantity !');
                $datas['receive_message'] = $mcrypt->encrypt('0');
                $this->response->setOutput(json_encode($datas));
		return;
            }
	}
        else 
        {
            $log->write('in if quantity array is empty');
            $datas['error'] = $mcrypt->encrypt('Please Enter Quantity !');
            $datas['receive_message'] = $mcrypt->encrypt('0');
            $this->response->setOutput(json_encode($datas));
            return;
        }
	$received_product_ids = $this->request->post['product_id'];
        $prices = $this->request->post['price'];
	$user_id=$mcrypt->decrypt($this->request->post['username']);
	$driver_otp=$mcrypt->decrypt($this->request->post['driver_otp']);
        $log->write($user_id."-".$order_id."-".$driver_otp);

        $database_otp= $this->model_inventory_purchase_order->check_driver_otp($order_id); 
	$log->write($database_otp);
	if($driver_otp!=$database_otp)
	{
			$datas['error'] = $mcrypt->encrypt('Entered OTP is wrong. Please try again !');
            $datas['receive_message'] = $mcrypt->encrypt('0');
            $this->response->setOutput(json_encode($datas));
            return;
	}
	$log->write($user_id);
	$received_product_idss=array();
	$i=0;
	foreach($received_product_ids as $pid)
	{
            $received_product_ids[$i]=$mcrypt->decrypt($pid);
            $i++;
	}
        $i=0;
	foreach($prices as $pid)
	{
            $prices[$i]=$mcrypt->decrypt($pid);
            $i++;
	}
        $i=0;
	foreach($received_quantities as $req)
	{
            $received_quantities[$i]=$mcrypt->decrypt($req);
            $i++;
	}
        
        $order_receive_date = date("Y-m-d");//$this->request->post['order_receive_date'];
	$prices = $this->request->post['price'];
	$rq = $this->request->post['remaining_quantity'];
	
	$received_order_info['received_quantities'] = $received_quantities;
	$received_order_info['received_product_ids'] = $received_product_ids;
	$received_order_info['suppliers_ids'] = $suppliers_ids;
	$received_order_info['order_receive_date'] = $order_receive_date;
	$received_order_info['prices'] = $prices;
	$received_order_info['rq'] = $rq;
	$log->write("before check");
        $log->write($received_order_info);
	$this->adminmodel('inventory/purchase_order');
	$inserted = $this->model_inventory_purchase_order->insert_receive_order($received_order_info,$order_id);
	$log->write($inserted);
        if($inserted)
	{
            $log->write('in if');
            $log->write("Order received Successfully!!");
            $data['receive_message'] = $mcrypt->encrypt('Order received Successfully!!');
            $data['error'] = $mcrypt->encrypt('1');
            $this->response->setOutput(json_encode($data));
	}
	else
	{
            $log->write('in else');
            $log->write('Sorry!! something went wrong, try again');
            $data['receive_message'] = $mcrypt->encrypt('0');
            $data['error'] = $mcrypt->encrypt('Sorry!! something went wrong, try again');
            $this->response->setOutput(json_encode($data));	
	}
    }
    /*-----------------------------insert receive order function ends here-----------------*/
    /*--------------------Insert Purchase Order starts heres-------------------------------------------------*/
    public function request_order()
    {
        $mcrypt=new MCrypt();
	$data['products'] = $_POST['product'];
	
	$data['quantity'] = $_POST['quantity'];
	$data['supplier_id'] ="--Supplier--"; //$_POST['supplier_id'];
	$data['stores'] = $_POST['stores'];
	$this->load->library('user');
        $this->user = new User($this->registry);
        $log=new Log("po-request-".date('Y-m-d').".log");
        $log->write( $this->request->post);//$_POST);
        
        $this->session->data['user_id']=$mcrypt->decrypt($_POST['username']);
	
	if((in_array("--products--",$data['products'])) || (in_array("--stores--",$data['stores'])) || (in_array("--Product Options--",$data['options'])) || (in_array("--Option Values--",$data['option_values'])) || (count($data['quantity']) != count(array_filter($data['quantity']))) || (count($data['options']) != count(array_filter($data['options']))) || (count($data['products']) != count(array_filter($data['products']))) || (count($data['option_values']) != count(array_filter($data['option_values']))))
	{
            $log->write("in if");
            $data['form_bit'] = 0;
            $_SESSION['errors'] = "Warning: Please check the form carefully for errors!";
            /*------------Working with data received starts-----*/
            $i = 0;
            foreach($data['products'] as $product)
            {
		if(strrchr($product,"_"))
		{
                    $product_names[$i] = explode('_',$product);
		}
		else
		{
                    $product_names[$i] = $product;
		}
		$i++;
            }
            $data['product_received'] = $product_names;
            
            //print_r($data['option_values_received']);
            $data['quantities_received'] = $data['quantity'];
            /*------working with data received ends---------*/
            $this->load->model('catalog/product');
            $products = $this->model_catalog_product->getProducts();
            $i = 0;
            foreach($products as $product)
            {
		$products[$i] = $product['name'];
		$product_ids[$i] = $product['product_id'];
		$i++;
            }
            $data['products'] = $products;
            $data['product_ids'] = $product_ids;
             
            $url = '';
            
        }
	else
	{
            $log->write("in else");
            $iq = 0;
            foreach($data['quantity'] as $qnty)
            {
                $qntry_final[$iq]=$mcrypt->decrypt($qnty);
		$iq++;					
            }
            $log->write($qntry_final);
            $data['quantity']=$qntry_final;
            $i = 0;
            foreach($data['products'] as $product)
            {
		$productval=explode('_',$product);
		$product=$mcrypt->decrypt($productval[0])."_".$mcrypt->decrypt($productval[1]);					
		$log->write($product);
                $product_names[$i] = explode('_',$product);
		$i++;
            }
            $log->write($product_names);
            $data['products'] = $product_names;
            //stores
            $i = 0;
            foreach($data['stores'] as $store)
            {
                $productval=explode('_',$store);
		$store=$mcrypt->decrypt($productval[0])."_".$mcrypt->decrypt($productval[1]);	
                $log->write($store);
		$store_names[$i] = explode('_',$store);
		$i++;
            }
            $data['stores'] = $store_names;
            $log->write($store_names);
            $data['potentialdate'] =$mcrypt->decrypt($_POST['potentialdate']);
            $log->write("potential date");	
            $log->write($data['potentialdate'] );	
            $this->adminmodel('inventory/purchase_order');
            $log->write("after");	
            $order_id = $this->model_inventory_purchase_order->insert_purchase_order($data);
            $log->write("after id".$order_id);										
            if($order_id)
            {
		$_SESSION['success_order_message'] = "The Order has been added";
		$json['order_id'] = $mcrypt->encrypt( $order_id);
		$json['success'] = $mcrypt->encrypt('Success: new order placed with ID: '.$order_id);
		$this->response->setOutput(json_encode($json));	
            }
	}
    }
	
	/*--------------------Insert purchase order ends here----------------------------*/
	
	///////////////////////////////////////////////////////////////////////////////////
	
}

?>