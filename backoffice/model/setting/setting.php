<?php
class ModelSettingSetting extends Model 
{
	public function getBilling($key,$store_id) 
	{
            $where=array('key'=>$key,'store_id'=>'ALL');
			$query = $this->db->query('select',DB_PREFIX . 'billing_status','','','',$where,'','','','',array());
			
			if($query->row['value'])
			{ 
				$where2=array('key'=>$key,'store_id'=>(int)$store_id);
				$query2 = $this->db->query('select',DB_PREFIX . 'billing_status','','','',$where2,'','','','',array());
				
				return array($query2->row['value'],$query2->row['msg']);
			}
			else
			{
				return array($query->row['value'],$query->row['msg']);
			}
            
	}
	public function getBillingStatusALL($key) 
	{
            $where=array('key'=>$key);
            $query = $this->db->query('select',DB_PREFIX . 'billing_status','','','',$where,'','','','',array());
            return $query->row['value'];
	}
	public function updateBillingStatusALL($key,$current_status,$msg='') 
	{	echo $msg;
		if($current_status=='1')
		{
			$new_status='0';
			if(empty($msg))
			{
				$msg='Billing is closed  due to maintenance';
			}
		}
		if($current_status=='0')
		{
			$new_status='1';
		}
		//$sql=" update oc_billing_status set `value`='".$new_status."' where `key`='".$key."'  ";
                $this->db->query('update',DB_PREFIX . 'billing_status',array('key'=>$key,'store_id'=>'ALL'),array('value'=>(int)$new_status,'msg'=>$msg));
		return 1;
		
		
	}
	public function getBillingStatus($key,$store_id) 
	{
            $where=array('key'=>$key,'store_id'=>(int)$store_id);
            $query = $this->db->query('select',DB_PREFIX . 'billing_status','','','',$where,'','','','',array());
            return array($query->row['value'],$query->row['msg']);
	}
	public function updateBillingStatus($key,$current_status,$store_id,$msg='Billing is closed  due to maintenance') 
	{ 
		//echo $current_status;
		
		if(empty($current_status))
		{
			$where=array('key'=>$key,'store_id'=>(int)$store_id);
			$query1 = $this->db->query('select',DB_PREFIX . 'billing_status','','','',$where,'','','','',array());
			
			$new_status='1';
			if($query1->num_rows>0)
			{
				$this->db->query('update',DB_PREFIX . 'billing_status',array('key'=>$key,'store_id'=>(int)$store_id),array('value'=>(int)$new_status,'msg'=>$msg));
				return 1;
			}
			else
			{
				$this->db->query('insert',DB_PREFIX . 'billing_status',array('key'=>$key,'store_id'=>(int)$store_id,'value'=>(int)1,'msg'=>$msg));
				return 1;
			}
		}
		else
		{
			$new_status='0';
			$query22=$this->db->query('update',DB_PREFIX . 'billing_status',array('key'=>$key,'store_id'=>(int)$store_id),array('value'=>(int)$new_status,'msg'=>$msg));
			print_r($query22);
			return 1;
		}
		
		
		
	}
	public function getSetting($code, $store_id = 0) 
        {
            $setting_data = array();
            $where=array('store_id'=>(int)$store_id,'code'=>$this->db->escape($code));
            $query = $this->db->query('select',DB_PREFIX . 'setting','','','',$where,'','','','',array());
            foreach ($query->rows as $result) 
            {
                if (!$result['serialized']) 
                {
                    $setting_data[$result['key']] = $result['value'];
		} 
                else 
                {
                    $setting_data[$result['key']] = unserialize($result['value']); 
                }
            }
            return $setting_data;
	}
        public function getcredit($store_id) 
        {
            $query = $this->db->query('select',DB_PREFIX . 'store',(int)$store_id,'store_id','','','','','','',array());
            return $query->row;
        }
        public function getSettingsql($code, $store_id = 0) 
        {
            $where=array('store_id'=>(int)$store_id,'code'=>$this->db->escape($code));
            $query = $this->db->query('select',DB_PREFIX . 'setting','','','',$where,'','','','',array());
            return $query->rows;
            
	}

	public function getSettingbykey($code,$key, $store_id = 0) 
        {
            $where=array('store_id'=>(int)$store_id,'code'=>$this->db->escape($code),'key'=>$this->db->escape($key));
			
            $query = $this->db->query('select',DB_PREFIX . 'setting','','','',$where,'','','','',array());
            return $query->row['value'];
	}

	public function editSetting($code, $data, $store_id = 0) 
        {
            
            foreach ($data as $key => $value) 
            {
                $this->db->query('delete',DB_PREFIX . 'setting',array('store_id'=>(int)$store_id,'code'=>$this->db->escape($code),'key'=>$this->db->escape($key)));
            
                $this->editSettingValue($code, $key, $value, $store_id);    
            }
	}

	public function deleteSetting($code, $store_id = 0) 
        {
            $this->db->query('delete',DB_PREFIX . 'setting',array('store_id'=>(int)$store_id,'code'=>$this->db->escape($code)));
        }
        
	public function editSettingValue($code = '', $key = '', $value = '', $store_id = 0) 
        {
            $setting_id=$this->db->getNextSequenceValue('oc_setting');;
            if (!is_array($value)) 
            {
                if($key=="config_storetype")
                {
                   $value=(int)$this->db->escape($value); 
                }
                else
                {
                    $value=$this->db->escape($value); 
                }
                $input_array=array(
                    'setting_id'=>(int)$setting_id,
                    'value'=>$value,
                    'code'=>$this->db->escape($code),
                    'key'=>$this->db->escape($key),
                    'store_id'=>(int)$store_id,
                    'serialized'=>0
                );
                $this->db->query('insert',DB_PREFIX . 'setting',$input_array);
            } 
            else 
            {
                if($key=="config_storetype")
                {
                   $value=(int)$this->db->escape($value); 
                }
                else
                {
                    $value=$this->db->escape($value); 
                }
		$input_array=array(
                    'setting_id'=>(int)$setting_id,
                    'value'=>serialize($value),
                    'code'=>$this->db->escape($code),
                    'key'=>$this->db->escape($key),
                    'store_id'=>(int)$store_id,
                    'serialized'=>1
                );
                $this->db->query('insert',DB_PREFIX . 'setting',$input_array);
            }
	}
       
         
}
