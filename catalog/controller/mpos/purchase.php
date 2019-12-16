<?php
	class ControllerMposPurchase extends Controller 
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
		
		public function add_order()
		{
			$log=new Log("supplier-purchase-".date('Y-m-d').".log");
			$log->write('add_order called');
			$log->write($this->request->post);
			
			$mcrypt=new MCrypt();
			$keys = array(
				'store_id',
				'store_name',
				'supplier_id',
				'supplier_name',
				'contactname',
				'contactmobile',
				'req_date',
				'prddtl',
				'delivery_type',
				'remarks',
				'user_id',
				'group_id',
				'inv_no'
				);
			foreach ($keys as $key) 		
			{
				$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]);   
			}
			/*
			'product_price',
				'product_id',
				'product_name',
				'p_qnty',
				'p_amount',
				'p_tax_type',
			*/
			$prds=json_decode($this->request->post['prddtl'],true);
			
			if(empty($prds))
			{
				$json=array('status'=>0,'msg'=>'No Product Found');
				return $this->response->setOutput(json_encode($json));
			}
			$order_product=array();
			foreach ($prds as $product) 
			{
				$order_product[] = array(
				
				'product_id' => (int)$product['product_id'],
				'product_name' => $product['product_name'],
				'batch' => $product['batch'],
				'p_price' => (int)$product['p_price'],
				'hstn' => $product['hstn'], 
				'quantity' => $product['quantity'], 
				'p_amount' => $product['p_amount'], 
				'p_tax' => $product['tax'], 
				'p_tax_type' => $product['per_tax'] 
				);
			}
			$this->request->post['filter_store'] =$this->request->post['store_id']; 
			$this->request->post['filter_supplier'] =$this->request->post['supplier_id'];  
			$this->request->post['filter_date'] =$this->request->post['req_date'];
			
			$this->request->post['order_product'] =$order_product;
			
			
			if(empty($this->request->post['filter_date']))
			{
				$this->request->post['filter_date']=date('Y-m-d');
			}
			
			
			$this->request->post['rem'] =$this->request->post['remarks'];
			
			$log->write($this->request->post);
			/*
			if ((utf8_strlen($this->request->post['contactname']) < 1) || (utf8_strlen(trim($this->request->post['contactname'])) > 32)) 
			{
				$json=array('status'=>0,'msg'=>'Person name can not be empty');
				return $this->response->setOutput(json_encode($json));
			}

			else if ((utf8_strlen($this->request->post['contactmobile']) > 10)  || (utf8_strlen($this->request->post['contactmobile']) < 10)) 
			{
				$json=array('status'=>0,'msg'=>'Contact mobile length must be 10');
				return $this->response->setOutput(json_encode($json));
			}
			
			
			else
				*/
			{	
				$log->write(1);
				$this->adminmodel('purchaseorder/purchase_order');
				$log->write(2);
				$supplier_data=$this->model_purchaseorder_purchase_order->submit_purchase_order($this->request->post);
				$log->write(3);
				$log->write($supplier_data);
				$json=array('status'=>1,'msg'=>'Purchase Order added successfully');
				return $this->response->setOutput(json_encode($json));
				
			}
			
		}
		public function getlist() 
		{
			$log=new Log("supplier-purchase-".date('Y-m-d').".log");
			$log->write('getlist called');
			$log->write($this->request->post);
			
			$mcrypt=new MCrypt();
			$keys = array(
				'store_id',
				'page',
				'name',
				'start_date',
				'end_date',
				'action'
				);
			foreach ($keys as $key) 		
			{
				$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
			}
			$page=$this->request->post['page'];
			if(empty($page))
			{
				$page=1;
			}
			$filter_data=array(
			'start'=>$start,
			'limit'=>$limit,
			'store_id'=>$this->request->post['store_id'],
			'name'=>$this->request->post['name'],
			'filter_date_start'=>$this->request->post['start_date'],
			'filter_date_end'=>$this->request->post['end_date']
			);
			
			$log->write($filter_data);
			$this->adminmodel('purchaseorder/purchase_order');
			$order_data=$this->model_purchaseorder_purchase_order->getList($filter_data);
			$data['orders'] = $order_data->rows;
			
			$total_order = $order_data->num_rows;
			if($this->request->post['action']=='e')
			{ 
			$this->load->library('email');
			$email=new email($this->registry);
			
			$file_name="update_purchase_".date('dMy').'.csv';
			$fields = array(
				'Supplier',
				'Date',
				'Invoice Number',
				'Product Details'

			);
			
			
			foreach($data['orders'] as $data)
    		{
				foreach($data['order_product'] as $product)
				{
					$order_product[]=array(
					'product id' => (int)$product['product_id'],
					'product name' => $product['product_name'],
					'price' => (int)$product['p_price'],
					'hstn' => $product['hstn'], 
					'quantity' => $product['quantity'], 
					'tax' => $product['p_tax'], 
					'amount' => $product['p_price']*$product['quantity'],
					'total amount' => ($product['p_price']*$product['quantity'])+($product['p_tax']),
					'tax type' => $product['p_tax_type']
					);
				}
				$fdata[]=array(
                        $data['supplier_name'],
                        date('d-m-Y',$data['valid_date']->sec),
                        $data['inv_no'],
						json_encode($order_product)
					);
				
			}
			
			$email->create_csv($file_name,$fields,$fdata);
			
			$mail_subject="Update Purchase ";
			
			$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear Team,
			<br/><br/>
			Please find attached file for Update Purchase.
			
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
			$bcc=array('vipin.kumar@aspl.ind.in','hrishabh.gupta@aspl.ind.in','chetan.singh@aspl.ind.in');
			
			$file_path=DIR_UPLOAD.$file_name;
            $email->sendmail($mail_subject,$body,$to,$cc,$bcc,$file_path);
			
			$json1=array('status'=>1,'msg'=>'sent');
			$log->write("sent" );
			
			$json['products'][]=$json1;
			$log->write('return array');
			$log->write($json);
			$this->response->setOutput(json_encode($json));
		}
		else
		{
			foreach($data['orders'] as $order)
			{
				$order_product=array();
				foreach($order['order_product'] as $product)
				{
					$order_product[]=array(
					'product_id' => (int)$product['product_id'],
					'product_name' => $product['product_name'],
					'p_price' => (int)$product['p_price'],
					'hstn' => $product['hstn'], 
					'quantity' => $product['quantity'], 
					'p_tax' => $product['p_tax'], 
					'p_amount' => $product['p_price']*$product['quantity'],
					'total_amount' => ($product['p_price']*$product['quantity'])+($product['p_tax']),
					'p_tax_type' => $product['p_tax_type']
					);
				}
				$json['products'][] = array(
					'supplier_id'  	=> $mcrypt->encrypt($order['supplier_id']),
					'supplier_name'			=> $mcrypt->encrypt(str_replace('&amp;', '&',htmlspecialchars_decode($order['supplier_name']))),
					'create_date'	=> $mcrypt->encrypt(date('d-m-Y',$order['create_date']->sec)),
					'inv_no'  	=> $mcrypt->encrypt($order['inv_no']),
					'amount'  	=> $mcrypt->encrypt($order['amount']),
					'order_product'  	=> $mcrypt->encrypt($order_product),
					'store_name'  	=> $mcrypt->encrypt($order['store_name']),
					'status'  	=> $mcrypt->encrypt($order['status']),
					'sid'  	=> $mcrypt->encrypt($order['sid']),
					'contactname'  	=> $mcrypt->encrypt($order['contactname']),
					'contactmobile'  	=> $mcrypt->encrypt($order['contactmobile']),
					'req_date'  	=> $mcrypt->encrypt(date('d-m-Y',$order['valid_date']->sec)),
					
					'remarks'  	=> $mcrypt->encrypt($order['remarks'])
					);
					//'inv_no'  	=> $mcrypt->encrypt($order['id_prefix'].$order['sid']),
			}
			$json['total']=$mcrypt->encrypt($total_order);
			return $this->response->setOutput(json_encode($json));
		}
		}
	}
?>