<?php
	class ControllerMposSupplier extends Controller 
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
		
		public function getlist() 
		{
			$log=new Log("supplier-".date('Y-m-d').".log");
			$log->write('getlist called');
			$log->write($this->request->post);
			
			$mcrypt=new MCrypt();
			$keys = array(
				'store_id',
				'page',
				'name',
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
			'name'=>$this->request->post['name']
			);
			$log->write($filter_data);
			$this->adminmodel('purchase/supplier');
			$supplier_data=$this->model_purchase_supplier->get_all_suppliers($filter_data);
			$data['suppliers'] = $supplier_data->rows;
			
			$total_suppliers = $supplier_data->num_rows;
			if($this->request->post['action']=='e')
			{ 
			$this->load->library('email');
			$email=new email($this->registry);
			
			$file_name="supplier_list_".date('dMy').'.csv';
			
			$fields = array(
					'Supplier id',
					'First name',
					'Last name',
					'Telephone',
					'Email', 
					'State Name',
					'State id',
					'District',
					'District id',
					'Supplier group id',
					'Supplier group name',
					'Date added',
					'Account',
					'IFSC Code',
					'Bank',
					'ADDRESS',
					'GST',
					'Pan',
					'Location',
					'status'
					);
			
			foreach($data['suppliers'] as $supplier)
    		{
				if($supplier['status']==1)
				{
					$status='Active';
				}
				else
				{
					$status='Deactivate';
				}
				$fdata[] = array(
					($supplier['pre_mongified_id']),
					($supplier['first_name']),
					($supplier['last_name']),
					($supplier['telephone']),
					($supplier['email']), 
					($supplier['state_name']),
					($supplier['state_id']),
					($supplier['fax']),
					($supplier['district_id']),
					($supplier['supplier_group_id']),
					($supplier['supplier_group_name']),
					($supplier['date_added']),
					($supplier['ACC_ID']),
					($supplier['IFSC_CODE']),
					($supplier['BANK_NAME']),
					($supplier['ADDRESS']),
					($supplier['gst']),
					($supplier['pan']),
					($supplier['location']),
					$status
					);
				
				
			}
			
			$email->create_csv($file_name,$fields,$fdata);
			
			$mail_subject="Supplier List ";
			
			$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear Team,
			<br/><br/>
			Please find attached file for Supplier List .
			
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
			foreach($data['suppliers'] as $supplier)
			{
				$json['products'][] = array(
					'id'			=> $mcrypt->encrypt($supplier['pre_mongified_id']),
					'first_name'	=> $mcrypt->encrypt($supplier['first_name']),
					'last_name'  	=> $mcrypt->encrypt($supplier['last_name']),
					'telephone'  	=> $mcrypt->encrypt($supplier['telephone']),
					'email'  	=> $mcrypt->encrypt($supplier['email']),
					'state_name'  	=> $mcrypt->encrypt($supplier['state_name']),
					'state_id'=> $mcrypt->encrypt($supplier['state_id']),					
					'district'  	=> $mcrypt->encrypt($supplier['fax']),
					'district_id'=> $mcrypt->encrypt($supplier['district_id']),
					'supplier_group_id'  	=> $mcrypt->encrypt($supplier['supplier_group_id']),
					'supplier_group_name'  	=> $mcrypt->encrypt($supplier['supplier_group_name']),
					'date_added'  	=> $mcrypt->encrypt($supplier['date_added']),
					'account'  	=> $mcrypt->encrypt($supplier['ACC_ID']),
					'ifsc'  	=> $mcrypt->encrypt($supplier['IFSC_CODE']),
					'bank'  	=> $mcrypt->encrypt($supplier['BANK_NAME']),
					'bid'  	=> $mcrypt->encrypt($supplier['bid']),
					'ADDRESS'  	=> $mcrypt->encrypt($supplier['ADDRESS']),
					'gst'  	=> $mcrypt->encrypt($supplier['gst']),
					'pan'  	=> $mcrypt->encrypt($supplier['pan']),
					'location'  	=> $mcrypt->encrypt($supplier['location']),
					'status'  	=> $mcrypt->encrypt($supplier['status'])
					
					);
			}
			$json['total']=$mcrypt->encrypt($total_suppliers);
			return $this->response->setOutput(json_encode($json));
		}
		}
		public function getlistall() 
		{
			$log=new Log("supplier-".date('Y-m-d').".log");
			$log->write('getlistall called');
			$log->write($this->request->post);
			
			$mcrypt=new MCrypt();
			$keys = array(
				
				'page',
				'name',
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
			'limit'=>500,
			'store_id'=>$this->request->post['store_id'],
			'name'=>$this->request->post['name']
			);
			$log->write($filter_data);
			$this->adminmodel('purchase/supplier');
			$supplier_data=$this->model_purchase_supplier->get_all_suppliersAll($filter_data);
			$data['suppliers'] = $supplier_data->rows;
			
			$total_suppliers = $supplier_data->num_rows;
			
			foreach($data['suppliers'] as $supplier)
			{
				$json['products'][] = array(
					'id'			=> $mcrypt->encrypt($supplier['pre_mongified_id']),
					'first_name'	=> $mcrypt->encrypt($supplier['first_name'].' '.$supplier['last_name']),
					'last_name'  	=> $mcrypt->encrypt($supplier['last_name']),
					'telephone'  	=> $mcrypt->encrypt($supplier['telephone']),
					'email'  	=> $mcrypt->encrypt($supplier['email']), 
					'district'  	=> $mcrypt->encrypt($supplier['fax']),
					'district_id'=> $mcrypt->encrypt($supplier['district_id']),
					'state_name'  	=> $mcrypt->encrypt($supplier['state_name']),
					'state_id'=> $mcrypt->encrypt($supplier['state_id']),
					'supplier_group_id'  	=> $mcrypt->encrypt($supplier['supplier_group_id']),
					'supplier_group_name'  	=> $mcrypt->encrypt($supplier['supplier_group_name']),
					'date_added'  	=> $mcrypt->encrypt($supplier['date_added']),
					'account'  	=> $mcrypt->encrypt($supplier['ACC_ID']),
					'ifsc'  	=> $mcrypt->encrypt($supplier['IFSC_CODE']),
					'bank'  	=> $mcrypt->encrypt($supplier['BANK_NAME']),
					'bid'  	=> $mcrypt->encrypt($supplier['bid']),
					'ADDRESS'  	=> $mcrypt->encrypt($supplier['ADDRESS']),
					'gst'  	=> $mcrypt->encrypt($supplier['gst']),
					'pan'  	=> $mcrypt->encrypt($supplier['pan']),
					'location'  	=> $mcrypt->encrypt($supplier['location']),
					'status'  	=> $mcrypt->encrypt($supplier['status'])
					
					);
			}
			$json['total']=$mcrypt->encrypt($total_suppliers);
			return $this->response->setOutput(json_encode($json));
		
		}
		/*---------------------Add supplier function starts here--------------*/
		
		public function add_supplier()
		{ 
			$log=new Log("supplier-".date('Y-m-d').".log");
			$log->write('add_supplier called');
			$log->write($this->request->post);
			
			$mcrypt=new MCrypt();
			$keys = array(
				'firstname',
				'lastname',
				'email',
				'telephone',
				'state_name',
				'state_id',
				'district',
				'district_id',
				'supplier_group_id',
				'supplier_group_name',
				'location',
				'account',
				'ifsc',
				'bank',
				'bid',
				'bankaddress',
				'gst',
				'pan',
				'store_id',
				'user_id',
				'group_id'
				);
			foreach ($keys as $key) 		
			{
				$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]);   
			}
			
			$this->request->post['fax'] =$this->request->post['district']; 
			//$this->request->post['location'] =$this->request->post['bankaddress'];  
			$log->write($this->request->post);
			if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) 
			{
				$json=array('status'=>0,'msg'=>'First name can not be empty');
				return $this->response->setOutput(json_encode($json));
			}

			else if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) 
			{
				$json=array('status'=>0,'msg'=>'Last name can not be empty');
				return $this->response->setOutput(json_encode($json));
			}
			/*
			else if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) 
			{
				$json=array('status'=>0,'msg'=>'Email must be less then 96 characters');
				return $this->response->setOutput(json_encode($json));
			}
			*/
			else if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) 
			{
				$json=array('status'=>0,'msg'=>'Telephone must be between 3 and 32 characters!');
				return $this->response->setOutput(json_encode($json));
			}
			else if ((utf8_strlen($this->request->post['district']) < 3) || (utf8_strlen($this->request->post['district']) > 32)) 
			{
				$json=array('status'=>0,'msg'=>'District must be between 3 and 32 characters!');
				return $this->response->setOutput(json_encode($json));
			}
			/*
			else if ((utf8_strlen($this->request->post['ifsc']) < 3) || (utf8_strlen($this->request->post['ifsc']) > 11)) 
			{
				$json=array('status'=>0,'msg'=>'IFSC Code must be between 1 and 11 characters!');
				return $this->response->setOutput(json_encode($json));
			}
			else if ((utf8_strlen($this->request->post['bank']) < 3) || (utf8_strlen($this->request->post['bank']) > 50)) 
			{
				$json=array('status'=>0,'msg'=>'Bank Name must be between 3 and 50 characters!');
				return $this->response->setOutput(json_encode($json));
			}

			else if ((utf8_strlen($this->request->post['account']) < 8) || (utf8_strlen($this->request->post['account']) > 25)) 
			{
				$json=array('status'=>0,'msg'=>'Bank Account Number must be between 8 and 25 characters!');
				return $this->response->setOutput(json_encode($json));
			}
			*/
			else
			{	
		 
				$this->adminmodel('purchase/supplier');
				$log->write('check supplier');
				$supplier_data=$this->model_purchase_supplier->get_supplier_by_gst($this->request->post['gst']);
				$log->write($supplier_data);
				if(empty($supplier_data))
				{
					$supplier_data=$this->model_purchase_supplier->get_supplier_by_pan($this->request->post['pan']);
					$log->write('in if data not found by gst');
					$log->write($supplier_data);
				}
				
				if($supplier_data->num_rows>0)
				{
					if($supplier_data->row['gst']==$this->request->post['gst'])
					{
						$json=array('status'=>2,'msg'=>'This GSTN is allready exists !','supplier_id'=>$mcrypt->encrypt($supplier_data->row['pre_mongified_id']));
						return $this->response->setOutput(json_encode($json));
					}
					else if($supplier_data->row['pan']==$this->request->post['pan'])
					{
						$json=array('status'=>2,'msg'=>'This PAN is allready exists !','supplier_id'=>$mcrypt->encrypt($supplier_data->row['pre_mongified_id']));
						return $this->response->setOutput(json_encode($json));
					}
					else
					{
						$json=array('status'=>2,'msg'=>'This PAN/GST is allready exists !','supplier_id'=>$mcrypt->encrypt($supplier_data->row['pre_mongified_id']));
						return $this->response->setOutput(json_encode($json));
					}
				}
				else
				{
					$supplier=$this->model_purchase_supplier->insert_supplier($this->request->post);
					$log->write($supplier);
					$json=array('status'=>1,'msg'=>'Supplier added successfully');
					return $this->response->setOutput(json_encode($json));
				}
				
			}
			
		}
		
		
		/*--------------Edit supplier function starts here-----------------------*/
		
		public function update_supplier()
		{
			$log=new Log("supplier-".date('Y-m-d').".log");
			$log->write('update_supplier called');
			$log->write($this->request->post);
			$mcrypt=new MCrypt();
			$keys = array(
				'firstname',
				'lastname',
				'email',
				'telephone',
				'state_name',
				'state_id',
				'district',
				'district_id',
				'supplier_group_id',
				'supplier_group_name',
				'account',
				'ifsc',
				'bid',
				'bank',
				'bankaddress',
				'gst',
				'pan',
				'store_id',
				'user_id',
				'group_id',
				'supplier_id'
				);
			foreach ($keys as $key) 		
			{
				$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]);   
			}
			
			$this->request->post['first_name'] =$this->request->post['firstname']; 
			$this->request->post['last_name'] =$this->request->post['lastname'];  
			$this->request->post['ACC_ID'] =$this->request->post['account']; 
			$this->request->post['ADDRESS'] =$this->request->post['bankaddress'];  
			$this->request->post['BANK_NAME'] =(int)$this->request->post['bank'];
			$this->request->post['bid'] =(int)$this->request->post['bid'];
			$this->request->post['IFSC_CODE'] =(int)$this->request->post['ifsc'];
			$this->request->post['status'] =(int)1;
			
			$this->request->post['fax'] =$this->request->post['district']; 
			$this->request->post['location'] =$this->request->post['bankaddress'];  
			$this->request->post['pre_mongified_id'] =(int)$this->request->post['supplier_id']; 
			$this->request->post['user_group_id'] =(int)$this->request->post['group_id']; 
			$this->request->post['store_id'] =array((int)$this->request->post['store_id']);  
			$this->request->post['user_id'] =(int)$this->request->post['user_id'];
			$this->request->post['delete_bit'] =(int)0;
			
			unset($this->request->post['bankaddress']);
			unset($this->request->post['supplier_id']);
			unset($this->request->post['group_id']);
			unset($this->request->post['ifsc']);
			unset($this->request->post['bank']);
			unset($this->request->post['account']);
			unset($this->request->post['lastname']);
			unset($this->request->post['firstname']);
			
			$log->write($this->request->post);
			if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) 
			{
				$log->write('First name can not be empty');
				$json=array('status'=>0,'msg'=>'First name can not be empty');
				return $this->response->setOutput(json_encode($json));
			}

			else if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) 
			{
				$log->write('Last name can not be empty');
				$json=array('status'=>0,'msg'=>'Last name can not be empty');
				return $this->response->setOutput(json_encode($json));
			}

			//else if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) 
			//{
				//$json=array('status'=>0,'msg'=>'Email must be less then 96 characters');
				//return $this->response->setOutput(json_encode($json));
			//}
			
			else if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) 
			{
				$log->write('Telephone must be between 3 and 32 characters!');
				$json=array('status'=>0,'msg'=>'Telephone must be between 3 and 32 characters!');
				return $this->response->setOutput(json_encode($json));
			}
			else if ((utf8_strlen($this->request->post['district']) < 3) || (utf8_strlen($this->request->post['district']) > 32)) 
			{
				$log->write('District must be between 3 and 32 characters!');
				$json=array('status'=>0,'msg'=>'District must be between 3 and 32 characters!');
				return $this->response->setOutput(json_encode($json));
			}
			/*
			else if ((utf8_strlen($this->request->post['ifsc']) < 3) || (utf8_strlen($this->request->post['ifsc']) > 11)) 
			{
				$json=array('status'=>0,'msg'=>'IFSC Code must be between 1 and 11 characters!');
				return $this->response->setOutput(json_encode($json));
			}
			else if ((utf8_strlen($this->request->post['bank']) < 3) || (utf8_strlen($this->request->post['bank']) > 50)) 
			{
				$json=array('status'=>0,'msg'=>'Bank Name must be between 3 and 50 characters!');
				return $this->response->setOutput(json_encode($json));
			}

			else if ((utf8_strlen($this->request->post['account']) < 8) || (utf8_strlen($this->request->post['account']) > 25)) 
			{
				$json=array('status'=>0,'msg'=>'Bank Account Number must be between 8 and 25 characters!');
				return $this->response->setOutput(json_encode($json));
			}
			*/
			else
			{	
				$log->write('in else');
				$this->adminmodel('purchase/supplier');
				$log->write('1');
				$updated = $this->model_purchase_supplier->update_supplier($this->request->post);
				$log->write('2');
				$log->write($updated);
				$json=array('status'=>1,'msg'=>'Supplier updated successfully');
				return $this->response->setOutput(json_encode($json));
				
			}
			
		}
		public function link_supplier_to_store()
		{
			$log=new Log("supplier-".date('Y-m-d').".log");
			$log->write('link_supplier_to_store called');
			$log->write($this->request->post);
			$mcrypt=new MCrypt();
			$keys = array(
				'store_id',
				'user_id',
				'supplier_id'
				);
			foreach ($keys as $key) 		
			{
				$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]);   
			}
			
			$log->write($this->request->post);
			if (empty($this->request->post['supplier_id'])) 
			{
				$json=array('status'=>0,'msg'=>'Supplier ID can not be empty');
				return $this->response->setOutput(json_encode($json));
			}

			else if (empty($this->request->post['user_id'])) 
			{
				$json=array('status'=>0,'msg'=>'User ID can not be empty');
				return $this->response->setOutput(json_encode($json));
			}
			else if (empty($this->request->post['store_id'])) 
			{
				$json=array('status'=>0,'msg'=>'Store ID can not be empty');
				return $this->response->setOutput(json_encode($json));
			}
			else
			{	
				$this->adminmodel('purchase/supplier');
				$updated = $this->model_purchase_supplier->link_supplier_to_store($this->request->post);
				$log->write('data return by model');
				$log->write($updated);
				
				if($updated===2)
				{
					$log->write('in if $updated==2');
					$json=array('status'=>0,'msg'=>'Your Store is allready linked to this Supplier');
					return $this->response->setOutput(json_encode($json));
				}
				else
				{
					$json=array('status'=>1,'msg'=>'Supplier Linked successfully');
					return $this->response->setOutput(json_encode($json));
				}
				
			}
		}
	}
?>