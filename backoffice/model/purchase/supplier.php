<?php
class ModelPurchaseSupplier extends Model 
{
	public function insert_supplier($data)
	{
		if(empty($data['store_id']))
		{
			$user_group_id=$this->user->getGroupId();
			$user_id=$this->user->getId();
			if($user_group_id==1)
			{
				$user_store_id=0;
			}
			else
			{
				$user_store_id=$this->user->getStoreId();
			}
		}
		else
		{
			$user_group_id=$data['group_id'];
			$user_id=$data['user_id'];
			$user_store_id=$data['store_id'];
		}
		$sid=$this->db->getNextSequenceValue('oc_po_supplier');
		
		$input_array=
		array(
		'pre_mongified_id'=>(int)$sid,
		'first_name'=>$data['firstname'],
		'last_name'=>$data['lastname'],
		'email'=>$data['email'],
		'telephone'=>$data['telephone'],
		'fax'=>$data['fax'],
		'state_name'=>$data['state_name'],
		'state_id'=>(int)$data['state_id'],
		'district'=>$data['district'],
		'district_id'=>(int)$data['district_id'],
		'supplier_group_id'=>$data['supplier_group_id'],
		'supplier_group_name'=>$data['supplier_group_name'],
		'date_added'=>new MongoDate(strtotime(date('Y-m-d'))),
		'ACC_ID'=>$data['account'],
		'IFSC_CODE'=>$data['ifsc'],
		'BANK_NAME'=>$data['bank'],
		'bid'=>(int)$data['bid'],
		'ADDRESS'=>$data['bankaddress'],
		'gst'=>$data['gst'],
		'pan'=>$data['pan'],
		'location'=>$data['location'],
		'status'=>(int)$data['status'],
		'user_group_id'=>(int)$user_group_id,
		'store_id'=>array((int)$user_store_id),
		'user_id'=>(int)$user_id,
		'delete_bit'=>0
		);
		
		$query = $this->db->query("insert","oc_po_supplier",$input_array);
		if($query)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function get_all_suppliers($data)
	{
		$where=array('delete_bit'=>0);
		if(empty($data['store_id']))
		{ 
			$user_group_id=$this->user->getGroupId();
			if($user_group_id==1)
			{
				$user_store_id=0;
			}
			else
			{
				$user_store_id=$this->user->getStoreId();
				$where['store_id'] = (int)$user_store_id;
			}
		}
		else
		{
			$where['store_id'] = (int)$data['store_id'];
		}
		
		if (!empty($data['name'])) 
		{
			$search_string=$data['name'];
            $where['last_name'] = new MongoRegex("/.*$search_string/i");
			
		}
		if (!empty($data['supplier_group'])) 
		{
			$where['supplier_group_name']=$data['supplier_group'];
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

		} 
 
		$query = $this->db->query("select","oc_po_supplier",'','','',$where,'',(int)$data['limit'],'',(int)$data['start']);
		
		return $query;
	}
	public function get_all_suppliersAll($data)
	{
		$where=array('delete_bit'=>0);
		
		if (!empty($data['name'])) 
		{
			$search_string=$data['name'];
            $where['last_name'] = new MongoRegex("/.*$search_string/i");
			
		}
		if (!empty($data['supplier_group'])) 
		{
			$where['supplier_group_name']=$data['supplier_group'];
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

		} 
 
		$query = $this->db->query("select","oc_po_supplier",'','','',$where,'',(int)$data['limit'],'',(int)$data['start']);
		
		return $query;
	}
	
	/*---------------------get total count supplier function ends here-----------------*/
	/*------------------delete supplier function starts here---------------------*/
	
	public function delete_supplier($supplier_ids)
	{
		foreach($supplier_ids as $supplier_id)
		{
			$query = $this->db->query("update",'oc_po_supplier',array('pre_mongified_id'=>(int)$supplier_id),array('delete_bit'=>1));
		}
		if($query)
		{
			return true;
			
		}
		else
		{
			return false;
		}
	}
	
	public function edit_supplier_form($supplier_id)
	{	
		$where=array('pre_mongified_id'=>(int)$supplier_id);
		$query = $this->db->query("select","oc_po_supplier",'','','',$where,'',(int)1,'',(int)0);
		
		return $query->row;
	}
	public function get_supplier_by_key($data)
	{	
		$log=new Log("supplier-".date('Y-m-d').".log");
		$log->write('in model get_supplier_by_key');
		$log->write($data);
		$where=array();
		if(!empty($data['gst']))
		{
			$where['gst']=$data['gst'];
		}
		if(!empty($data['pan']))
		{
			$where['pan']=$data['pan'];
		}
		if(!empty($data['supplier_id']))
		{
			$where['pre_mongified_id']=(int)$data['supplier_id'];
		}
		$log->write($where);
		$query = $this->db->query("select","oc_po_supplier",'','','',$where,'',(int)1,'',(int)0);
		
		return $query;
	}
	public function get_supplier_by_gst($gst)
	{	
		$log=new Log("supplier-".date('Y-m-d').".log");
		$log->write('in model get_supplier_by_gst');
		$log->write($gst);
		$where=array();
		if(!empty($gst))
		{
			$where['gst']=$gst;
		}
		
		$log->write($where);
		$query = $this->db->query("select","oc_po_supplier",'','','',$where,'',(int)1,'',(int)0);
		
		return $query;
	}
	public function get_supplier_by_pan($pan)
	{	
		$log=new Log("supplier-".date('Y-m-d').".log");
		$log->write('in model get_supplier_by_pan');
		$log->write($pan);
		$where=array();
		if(!empty($pan))
		{
			$where['pan']=$pan;
		}
		
		$log->write($where);
		$query = $this->db->query("select","oc_po_supplier",'','','',$where,'',(int)1,'',(int)0);
		
		return $query;
	}
	public function get_supplier_by_store_supplier($store_id,$supplier_id)
	{	
		$log=new Log("supplier-".date('Y-m-d').".log");
		$log->write('in model get_supplier_by_store');
		$log->write($store_id);
		$where=array();
		if(!empty($store_id))
		{
			$where['store_id']=(int)$store_id;
		}
		if(!empty($supplier_id))
		{
			$where['pre_mongified_id']=(int)$supplier_id;
		}
		$log->write($where);
		$query = $this->db->query("select","oc_po_supplier",'','','',$where,'',(int)1,'',(int)0);
		
		return $query;
	}
	
	/*---------------------edit supplier funciton ends here---------------------*/
	
	/*--------------------update supplier function starts here----------------------*/
	
	public function update_supplier($update_info)
	{
		$log=new Log("supplier-".date('Y-m-d').".log");
		
		$query = $this->db->query("update",'oc_po_supplier',array('pre_mongified_id'=>(int)$update_info['pre_mongified_id']),$update_info);
		$log->write($query);
		if($query)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function link_supplier_to_store($supplier_data)
	{
		$log=new Log("supplier-".date('Y-m-d').".log");
		$log->write('in model link_supplier_to_store');
		$log->write($supplier_data);
		$where=array();
		if(!empty($supplier_data['supplier_id']))
		{
			$where['pre_mongified_id']=(int)$supplier_data['supplier_id'];
		}
		
		if(!empty($supplier_data['user_id']))
		{
			//$where['user_id']=$supplier_data['user_id']; 
		}
		$get_supplier_by_store=$this->get_supplier_by_store_supplier($supplier_data['store_id'],$supplier_data['supplier_id'])->row;
		
		$log->write('get_supplier_by_store num_rows');
		$log->write($get_supplier_by_store);
		
		if((!empty($get_supplier_by_store['pre_mongified_id'])) && ($get_supplier_by_store['pre_mongified_id']==$supplier_data['supplier_id']))
		{
			$log->write('in if store is allready linked');
			return 2;
		}
		
		$get_supplier=$this->get_supplier_by_key($supplier_data)->row;
		
		$store_ids=$get_supplier['store_id'];
		$log->write($store_ids);
		$update_store=array();
		
		if(is_array($store_ids))
		{
			$log->write('in if array');
			foreach($$store_ids as $store)
			{
				if(!empty($store))
				{
					$update_store[]=(int)$store;
				}
			}
		}
		else
		{
			$log->write('in else ');
			if(!empty($store_ids))
			{
				$update_store[]=$store_ids;
			}
		}
		$update_store[]=(int)$supplier_data['store_id'];
		
		$update_info=array('store_id'=>$update_store);
		
		
		$log->write($update_info);
		$log->write($where);		
		$query = $this->db->query("update",'oc_po_supplier',$where,$update_info);
		if($query)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
}
?>