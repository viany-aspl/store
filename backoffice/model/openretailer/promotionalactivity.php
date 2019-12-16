<?php
class ModelOpenretailerpromotionalactivity extends Model 
{
   
     public function addActivity($data)
	{
		//echo 'test12';
		//exit('data');
		$log=new Log("addpro_activity-".date('Y-m-d').".log");
        $log->write($data);
		$last_id=$this->db->getNextSequenceValue('oc_promational_activity');
		 
            $fdata=array(
			'auto_activity_id'=>(int)$last_id,
            'store_id'=>(int)$data['store_id'],
			'user_id'=>(int)$data['user_id'],
			'store_name'=>$data['store_name'],
			'activity_id'=>(int)$data['activity_id'],
		    'activity_name'=>$data['activity_name'],
		    'company_id'=>(int)$data['company_id'],
		    'company_name'=>$data['company_name'],
		    'retailer_id'=>(int)$data['retailer_id'],
			'retailer_name'=>$data['retailer_name'],
			'date'=>new MongoDate(strtotime(date('Y-m-d'),$data['date'])),
			'lat'=>$data['lat'],
			'long'=>$data['long'],
			'representative_name'=>$data['representative_name'],
		    'representative_mobile'=>$data['representative_mobile'],
               
            );
             $this->db->query('insert','oc_promational_activity',$fdata);
			  return $last_id;
    }
	
	
	public function count_pro_actvity_image($transid)
	{
       $match=array('auto_activity_id'=>(int)$transid);
       $query = $this->db->query('select','oc_promational_activity','','','',$match);
       return $query->row['images'];
	}
 	public function update_pro_activity_image($transid,$file)
	{ 
		$match=array(
             'auto_activity_id'=> (int)$transid   
           );
		$query23 = $this->db->query("select","oc_promational_activity",'','','',$match);
		
		$images=$query23->row['images'];
		
		$images[]=$file;
		$udata=array(
              'images'=> $images,
           );
           
           $query = $this->db->query('update','oc_promational_activity',$match,$udata);
    }

}

?>