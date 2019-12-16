<?php
date_default_timezone_set('Asia/Kolkata');
class ModelCcareCcare extends Model 
{
	public function insertincomming($mobile,$customer_id)
    {
        $log=new Log("call-".date('Y-m-d').".log");
        $current_time=date('H:i:s');
        $current_date_time=date('Y-m-d H:i:s');
        
        $check_query = $this->db->query('select','cc_incomingcall','','','',array('mobile'=>$mobile,'channel'=>'Call','status'=>1));
        $mobilecode=substr($mobile, 0, 4);

        $state_query = $this->db->query('select','oc_mobilestate','','','',array('mobilecode'=>(int)$mobilecode));
        $state_code=$state_query->row["stateid"];
        $state_name=$state_query->row["state"];
        $log->write($state_query->row);
        
        if($check_query->row["notimesreceived"]!="")
        {
           $newcount=$check_query->row["notimesreceived"]+1;
           $transid=$check_query->row["transid"];
           $update_query = $this->db->query('update','cc_incomingcall',array('transid'=>(int)$transid),array('notimesreceived'=>(int)$newcount,'status'=>(int)1,'state_name'=>$state_name,'state'=>(int)$state_code,'farmerid'=>(int)$customer_id,'channel'=>'Call','updatetime'=>new MongoDate(strtotime($current_time))));  
           
        }
        else
        {
			$transid=$this->db->getNextSequenceValue('cc_incomingcall');  
			$insert_data=array(
				'transid'=>(int)$transid,
				'mobile'=>$mobile,
				'status'=>(int)1,
				'timereceived'=>new MongoDate(strtotime($current_time)),
				'updatetime'=>new MongoDate(strtotime($current_time)),
				'state_name'=>$state_name,
				'state'=>(int)$state_code,
				'notimesreceived'=>(int)1,
				'farmerid'=>(int)$customer_id,
				'images'=>array(),
				'channel'=>'Call'
			);
		
			$trans_query = $this->db->query('insert','cc_incomingcall',$insert_data); 
        }
		return $transid;
    }
	
	public function insertquery($mobile,$customer_id,$data)
    {
        $log=new Log("call-insertquery-".date('Y-m-d').".log");
        $current_time=date('H:i:s');
        $current_date_time=date('Y-m-d H:i:s');
        
        $check_query = $this->db->query('select','cc_incomingcall','','','',array('mobile'=>$mobile,'status'=>1,'Categories'=>$data['Categories'],'Type'=>$data['Type']));
        $mobilecode=substr($mobile, 0, 4);

        $state_query = $this->db->query('select','oc_mobilestate','','','',array('mobilecode'=>(int)$mobilecode));
        $state_code=$state_query->row["stateid"];
        $state_name=$state_query->row["state"];
        $log->write($state_query->row);
        $log->write($check_query);

        if($check_query->row["notimesreceived"]!="")
        {
           $newcount=$check_query->row["notimesreceived"]+1;
           $log->write($newcount);
           $transid=$check_query->row["transid"];
	   $log->write($transid);
           $update_query = $this->db->query('update','cc_incomingcall',array('transid'=>(int)$transid),array('store_id'=>(int)$data['store_id'],'query'=>$data['query'],'Categories'=>(int)$data['Categories'],'category_name'=>$data['category_name'],'Type'=>$data['Type'],'name'=>$data['name'],'email'=>$data['email'],'notimesreceived'=>(int)$newcount,'status'=>(int)1,'state_name'=>$state_name,'state'=>(int)$state_code,'farmerid'=>(int)$customer_id,'channel'=>'Call','updatetime'=>new MongoDate(strtotime($current_time))));  
           
        }
        else
        {
			$transid=$this->db->getNextSequenceValue('cc_incomingcall'); 
			$log->write($transid);
			$insert_data=array(
				'transid'=>(int)$transid,
				'mobile'=>$mobile,
				'status'=>(int)$data['call_status'],
				'timereceived'=>new MongoDate(strtotime($current_time)),
				'updatetime'=>new MongoDate(strtotime($current_time)),
				'state_name'=>$state_name,
				'state'=>(int)$state_code,
				'notimesreceived'=>(int)1,
				'farmerid'=>(int)$customer_id,
				'channel'=>$data['channel'],
				'store_id'=>(int)$data['store_id'],
				'query'=>$data['query'],
				'Categories'=>$data['Categories'],
				'category_name'=>$data['category_name'],
				'name'=>$data['name'],
				'email'=>$data['email'],
				'Type'=>$data['Type'],
				'images'=>array()
			);
		
			$trans_query = $this->db->query('insert','cc_incomingcall',$insert_data); 
        }
		return $transid;
    }
	public function category_name($cat_id)
	{
		$query = $this->db->query("select","oc_faq_category",'','','',array('id'=>(int)$cat_id),'','','','',array());
	
		return $query;
	}
	public function getCategories($data)
	{
		$query = $this->db->query("select","oc_faq_category",'','','',$where,'','','','',array('sort_order'=>1));
	
		return $query;
	}
	public function getTypes($data)
	{
		$query = $this->db->query("select","oc_call_Type",'','','',$where,'','','','',array('name'=>1));
	
		return $query;
	}
	public function type_name($type_id)
	{
		$query = $this->db->query("select","oc_call_Type",'','','',array('sid'=>(int)$type_id),'','','','',array());
	
		return $query;
	}
	public function ticket_status_name($id)
	{
		$where=array('STATUS_ID'=>(int)$id);
		$query = $this->db->query("select","oc_call_ticket_status",'','','',$where,'','','','',array());
	
		return $query;
	}
	public function get_ticket_open_time($id)
	{
		$where=array('transid'=>(int)$id);
		$query = $this->db->query("select","cc_incomingcall",'','','',$where,'','','','',array());
		
		return $query;
	}
	public function getCount($data)
	{
		$where=array('channel'=>'App');
		if(!empty($data['mobile_number']))
		{
			$where['mobile_number']=(string)$data['mobile_number'];
		}
		$where['ticket_status']=array('$in'=>array(3,4));
		$where['feedbackcount']=(int)0;
		$log=new Log("call-".date('Y-m-d').".log");
		$log->write($where); 
		
		$query = $this->db->query("select","oc_call_history",'','','',$where,'','','','',array());
		//print_r(json_encode($where));
		return $query;
	}
	public function getList($data)
	{
		$where=array('channel'=>'App');
		if(!empty($data['mobile_number']))
		{
			$where['mobile_number']=(string)$data['mobile_number'];
		}
		
		$query = $this->db->query("select","oc_call_history",'','','',$where,'','','','',array('transid'=>-1));
		//print_r(json_encode($where));
		return $query;
	}
	public function getListIncomming($data)
	{
		$where=array('channel'=>'App');
		if(!empty($data['mobile_number']))
		{
			$where['mobile']=$data['mobile_number'];
		}
		if(!empty($data['resolved_open_ticket']))
		{
			$where['transid']=array('$nin'=>$data['resolved_open_ticket']);
		}
		$query = $this->db->query("select","cc_incomingcall",'','','',$where,'','','','',array('transid'=>-1));
		//print_r($where);
		return $query;
	}
	public function insertfeedback($data)
    {
        $log=new Log("call-".date('Y-m-d').".log");
       
        $current_date_time=date('Y-m-d H:i:s');
        
        $transid=$data["transid"];
		$where=array('transid'=>(string)$transid);
		$upd_array=array('feedbackcount'=>$data['feedbackcount'],'feedback'=>$data['feedback'],'feedbacktime'=>new MongoDate(strtotime($current_date_time)));
        $log->write($where);
		$log->write($upd_array);
		$update_query = $this->db->query('update','oc_call_history',$where,$upd_array);  
        return $transid;
    }
	
}