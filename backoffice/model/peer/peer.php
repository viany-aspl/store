<?php
class ModelPeerPeer extends Model 
{
	public function getstoresbyproduct($data)
	{
		$log=new Log("peer-".date('Y-m-d').".log");
		$log->write('in model');
		$where=array();
		$sdate=date('Y-m-d');
		$edate=date('Y-m-d');
		$where['validate']=array('$gte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('1 day', strtotime($edate))) )));
        
        if (!empty($data['product_id']) ) 
        {
            $where['product_id']=(int)$data['product_id'];
		}
		$where['status']=(int)1;
		
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
		$log->write($where);
		$log->write(json_encode($where));
		$query = $this->db->query("select","oc_peer",'','','',$where,'',(int)$data['limit'],'',(int)$data['start'],array('sid'=>-1));
		//$log->write($query);
		//print_r($query);
		return $query;
	}
    public function getList($data)
    { 
		$log=new Log("peer-".date('Y-m-d').".log");
		$log->write('in model');
		$where=array();
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
            
		$datedata=array();
		$sdate=$data['filter_date_start'];
		$edate=$data['filter_date_end'];
        if(strtotime($sdate)==strtotime($edate))
        {
            $datedata=array(
                            '$gt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime($sdate)))  )),
                            '$lt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('1 day', strtotime($edate))) ))
                            );
        }
        else
        {
            $datedata=array(
                            '$gte'=>new MongoDate(strtotime($sdate)),
                            '$lte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('1 day', strtotime($edate)))))
                            );
        }
        if((!empty($sdate)) && (!empty($edate)))
        {
                $where['validate']=$datedata;
        }
		
		$datedatavalidate=array(
                            '$gte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime(date('Y-m-d')))))),
                            //'$lte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('1 day', strtotime(date('Y-m-d'))))))
                            );
        
        $where['validate']=$datedatavalidate;
        
		
        if((!empty($data['lat'])) && (!empty($data['lng'])))
        {
            $where['loc']=array('$near'=>array((float)$data['lng'],(float)$data['lat']),'$maxDistance'=> 0.006278449);
			/////radians=40(km)/6371=0.006278449
        }
        if (!empty($data['filter_store']) ) 
        {
            //$where['store_id']=(int)$data['filter_store'];
		}
        if ($data['filter_status']!='') 
        {
            $where['status']=(int)$data['filter_status'];
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
		$log->write($where);
		$log->write(json_encode($where));
		$query = $this->db->query("select","oc_peer",'','','',$where,'',(int)$data['limit'],'',(int)$data['start'],array('sid'=>-1));
		//$log->write($query);
		//print_r($query);
		return $query;
    }
	public function getProductCheck($data)
    { 
		$log=new Log("peer-".date('Y-m-d').".log");
		$log->write('in model for getProductCheck');
		$where=array();
		$where['store_id'] = (int)$data['store_id'];
		$where['product_id'] = (int)$data['product_id'];
		
		$datedata=array();
		$sdate=date('Y-m-d');
		$edate=date('Y-m-d');
       
        $where['validate']=array('$gte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('1 day', strtotime($edate))) )));
        //$where['validate']=$datedatavalidate;
        
        $where['status']=(int)1;
		//print_r($where);
		$log->write($where);
		$log->write(json_encode($where));
		$query = $this->db->query("select","oc_peer",'','','',$where,'',(int)1,'',(int)0,array('sid'=>-1));
		//$log->write($query);
		//print_r($query);
		return $query;
    }
    public function submit_order($data = array()) 
    { 
	$log=new Log("peer-save-".date('Y-m-d').".log");
	$log->write('in model');
	$sid=$this->db->getNextSequenceValue('oc_peer');
	$log->write($sid);
        
	$input_array=array(
		'sid'=>(int)$sid,
		'store_id'=>(int)$data['store_id'],
		'store_name'=>$data['store_name'],
		'category_id'=>(int)$data['category_id'],
		'category_name'=>$data['category_name'],
		'product_id'=>(int)$data['product_id'],
		'negotiation'=>$data['negotiation'],
		'action'=>$data['action'],
		'share_detail'=>$data['share_detail'],
		'quantity'=>$data['quantity'], 
		'product_name'=>$data['product_name'],
		'offer_price'=>(float)$data['offer_price'],
                'lat'=>(float)$data['lat'],
                'lng'=>(float)$data['lng'],
                'loc'=>$data['loc'],
		'validate'=>new MongoDate(strtotime($data['validate'])),
		'remarks'=>$data['remarks'],
		'create_date'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
		'status'=>(int)1,
		'user_group_id'=>(int)$data['group_id'],
		'user_id'=>(int)$data['user_id'],
		); 
        
	$log->write($input_array);
        $query = $this->db->query("insert","oc_peer",$input_array);
        return $sid;
    }
    public function get_store_data($store_id)
    {
           $store_data=array();
           
           $query = $this->db->query('select','oc_setting','','','',array('store_id'=>(int)$store_id,'key'=>'config_telephone'));
           $store_data['telephone']=$query->row['value']; 
           
           $query2 = $this->db->query('select','oc_setting','','','',array('store_id'=>(int)$store_id,'key'=>'config_email'));
           $store_data['email']=$query2->row['value']; 
		   
		   $query2 = $this->db->query('select','oc_setting','','','',array('store_id'=>(int)$store_id,'key'=>'config_name'));
           $store_data['name']=$query2->row['value']; 
           
           return $store_data;
           
    }
	public function addtofavourite($data)
    {
		$log=new Log("peer-".date('Y-m-d').".log");
        $log->write($data);
		
		$where23=array('sid'=>(int)$data['sid']);
		$query23 = $this->db->query("select",DB_PREFIX . "peer",'','','',$where23);
		
		$fav_store_ids=$query23->row['fav_store_ids'];
		if(in_array((int)$data['store_id'], $fav_store_ids))
		{
			
		}
		else
		{
			$fav_store_ids[]=(int)$data['store_id'];
			$query24 = $this->db->query("update",DB_PREFIX . "peer",$where23,array('fav_store_ids'=>$fav_store_ids));
		}
		return $data['sid'];
	}
	public function remove_favourite($data)
    {
		$log=new Log("peer-".date('Y-m-d').".log");
        $log->write($data);
		
		$where23=array('sid'=>(int)$data['sid']);
		$query23 = $this->db->query("select",DB_PREFIX . "peer",'','','',$where23);
		
		$log->write('fav_store_ids');
		$fav_store_ids=$query23->row['fav_store_ids'];
		$log->write($fav_store_ids);
		$log->write(sizeof($fav_store_ids));
		if(sizeof($fav_store_ids)>0)
		{
			$fav_store_ids=array_diff($fav_store_ids,array((int)$data['store_id']));
			$log->write($fav_store_ids);
			$query24 = $this->db->query("update",DB_PREFIX . "peer",$where23,array('fav_store_ids'=>$fav_store_ids));
		}
		return $data['sid'];
	}
	public function delete_peer($data)
    {
		$log=new Log("peer-".date('Y-m-d').".log");
        $log->write($data);
		
		$where=array('sid'=>(int)$data['sid'],'store_id'=>(int)$data['store_id']);
		$query = $this->db->query("update",DB_PREFIX . "peer",$where,array('status'=>(int)0));
		
		return $data['sid'];
	}
}
?>