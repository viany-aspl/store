<?php
class ModelPurchaseSupplierGroup extends Model {
	public function insert_supplier_group($data)
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
		$sid=$this->db->getNextSequenceValue('oc_po_supplier_group');
		
		$input_array=
		array(
		'pre_mongified_id'=>(int)$sid,
		'supplier_group_name'=>$data['supplier_group_name'],
		'supplier_group_desc'=>$data['supplier_group_desc'],
		'user_group_id'=>(int)$user_group_id,
		'store_id'=>(int)$user_store_id,
		'user_id'=>(int)$user_id,
		'delete_bit'=>0
		);
		
		$query = $this->db->query("insert","oc_po_supplier_group",$input_array);
		if($query)
		{
			return true;
		}
		else
		{
			return false;
		}
		
	}
	public function get_supplier_groups($data)
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
				//$where['store_id'] = (int)$user_store_id;
			}
		}
		else
		{
			//$where['store_id'] = (int)$data['store_id'];
		}
		if (!empty($data['name'])) 
		{
			$search_string=$data['name'];
            $where['last_name'] = new MongoRegex("/.*$search_string/i");
			
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
 
		$query = $this->db->query("select","oc_po_supplier_group",'','','',$where,'',(int)$data['limit'],'',(int)$data['start']);
		return $query;
	}
	public function get_all_supplier_groups()
	{
		$where=array('delete_bit' =>0);
		$user_group_id=$this->user->getGroupId();
		if($user_group_id==1)
		{
			$user_store_id=0;
			//$where['store_id'] = (int)$user_store_id;
		}
		else
		{
			$user_store_id=$this->user->getStoreId();
			//$where['store_id'] = (int)$user_store_id;
		}
		
		if (!empty($data['name'])) 
		{
			$search_string=$data['name'];
            $where['last_name'] = new MongoRegex("/.*$search_string/i");
			
		}
		$query = $this->db->query("select","oc_po_supplier_group",'','','',$where);
		
		return $query->rows;
	}
	
	
	public function delete_supplier_group($supplier_group_ids)
	{
		
		foreach($supplier_group_ids as $supplier_group_id)
		{
			$query = $this->db->query("update",'oc_po_supplier_group',array('pre_mongified_id'=>(int)$supplier_group_id),array('delete_bit'=>1));
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
	
	/*------------------------delete_supplier_group() funtion starts here---------------------*/
	
	/*-------------------------supplier_group_edit_form function starts here-----------------*/
	
	public function supplier_group_edit_form($supplier_group_id)
	{
		$where=array('pre_mongified_id'=>(int)$supplier_group_id);
		$query = $this->db->query("select","oc_po_supplier_group",'','','',$where,'',(int)1,'',(int)0);
		return $query->row;
	}
	
	/*-------------------------supplier_group_edit_form fucntion ends here-----------------------*/
	
	
	/*------------------------update supplier group function starts here------------------*/
	
	public function update_supplier_group($update_info)
	{
		if(empty($update_info['store_id']))
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
			$user_group_id=$update_info['group_id'];
			$user_id=$update_info['user_id'];
			$user_store_id=$update_info['store_id'];
		}
		$input_array=array(
	
		'supplier_group_name'=>$update_info['supplier_group_name'],
		'supplier_group_desc'=>$update_info['supplier_group_desc'],
		'user_group_id'=>(int)$user_group_id,
		'store_id'=>(int)$user_store_id,
		'user_id'=>(int)$user_id,
		'delete_bit'=>0
		);
		$query = $this->db->query("update",'oc_po_supplier_group',array('pre_mongified_id'=>(int)$update_info['supplier_group_id']),$input_array);
		if($query)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/*------------------------update supplier group function ends here-------------------*/
	
}
?>