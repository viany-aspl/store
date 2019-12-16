<?php
	class ControllerMposSupplierGroup extends Controller 
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
			$mcrypt=new MCrypt();
			$keys = array(
				'store_id',
				'page',
				'name'
				);
			foreach ($keys as $key) 		
			{
				$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
			}
			$this->adminmodel('purchase/supplier_group');
			
			$page=$this->request->post['page'];
			if(empty($page))
			{
				$page=1;
			}
			$start = ($page - 1) * 20;
			$limit = 20;
			$filter_data=array(
			'store_id'=>$this->request->post['store_id'],
			'name'=>$this->request->post['name'],
			'start'=>$start,
			'limit'=>$limit
			);
			$supplier_groups = $this->model_purchase_supplier_group->get_supplier_groups($filter_data);
			$data['supplier_groups'] = $supplier_groups->rows;
			
			$total_suppliers_group = $supplier_groups->num_rows;
			foreach($data['supplier_groups'] as $supplier_group)
			{
				$json['products'][] = array(
					'id'			=> $mcrypt->encrypt($supplier_group['pre_mongified_id']),
					'supplier_group_name'			=> $mcrypt->encrypt($supplier_group['supplier_group_name']),
					'supplier_group_desc'  	=> $mcrypt->encrypt($supplier_group['supplier_group_desc'])
					);
			}
			$json['total']=$mcrypt->encrypt($total_suppliers_group);
			return $this->response->setOutput(json_encode($json));
		}
		/*---------------------Add supplier function starts here--------------*/
		
		
		public function add_supplier_group()
		{
			$log=new Log("supplier_group-".date('Y-m-d').".log");
			$log->write('add_supplier_group called');
			$log->write($this->request->post);
			$mcrypt=new MCrypt();
			$keys = array(
				'supplier_group_name',
				'supplier_group_desc',
				'store_id',
				'user_id',
				'group_id'
				);
			foreach ($keys as $key) 		
			{
				$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
			}
			$log->write($this->request->post);
			
			if ((utf8_strlen($this->request->post['supplier_group_name']) < 3) || (utf8_strlen($this->request->post['supplier_group_name']) > 32)) 
			{
				$json=array('status'=>0,'msg'=>'Group Name Length must be between 3 to 32 characters');
				return $this->response->setOutput(json_encode($json));
			}
			else
			{
				$this->adminmodel('purchase/supplier_group');
				$inserted = $this->model_purchase_supplier_group->insert_supplier_group($this->request->post);
				$log->write($inserted);
				$json=array('status'=>1,'msg'=>'Supplier group added successfully');
				return $this->response->setOutput(json_encode($json));
				
			}
		}
		
		/*----------------------update supplier group function starts here-------------*/
		
		public function update_supplier_group()
		{
			$log=new Log("supplier_group-".date('Y-m-d').".log");
			$log->write('update_supplier_group called');
			$log->write($this->request->post);
			$mcrypt=new MCrypt();
			$keys = array(
				'supplier_group_id',
				'supplier_group_name',
				'supplier_group_desc',
				'store_id',
				'user_id',
				'group_id'
				);
			foreach ($keys as $key) 		
			{
				$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
			}
			$log->write($this->request->post);
			if ((utf8_strlen($this->request->post['supplier_group_name']) < 3) || (utf8_strlen($this->request->post['supplier_group_name']) > 32)) 
			{
				$json=array('status'=>0,'msg'=>'Group Name Length must be between 3 to 32 characters');
				return $this->response->setOutput(json_encode($json));
			}
			else
			{
				$this->adminmodel('purchase/supplier_group');
				$updated = $this->model_purchase_supplier_group->update_supplier_group($update_info);
				$log->write($updated);
				$json=array('status'=>1,'msg'=>'Supplier group updated successfully');
				return $this->response->setOutput(json_encode($json));
				
			}
		}
		/*----------------------update supplier group function ends here-------------------*/
	}
?>