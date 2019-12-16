<?php
date_default_timezone_set("Asia/Calcutta");
class ModelFirebaseFirebase extends Model 
{
    public function submit_data($title,$message,$image_url,$deviceId,$response,$userID,$username,$name,$email,$store_id,$validdate)
    {
        $publishedDate=date('Y-m-d h:i:s');
        $input_array=array(
                'title'=>$title,
                'message'=>$message,
                'image_url'=>$image_url,
                'deviceId'=>$deviceId,
                'response'=>$response,
				'userID'=>(int)$userID,
				'username'=>$username,
				'name'=>$name,
				'email'=>$email,
				'store_id'=>(int)$store_id,
                'publishedDate'=>$publishedDate,
				'validDate'=>new MongoDate(strtotime($validdate))
                );
        $query = $this->db->query('insert','oc_notifications',$input_array);
              
    }
    public function getallnotifications()
    {
		
        $query = $this->db->query('select','oc_notifications','','','',array('deviceId'=>'ALL'));
        return $query->rows;
              
    }
	public function getstoreusers()
    {
		//$query = $this->db->query('join','oc_user','','','',array('user_group_id'=>(int)(11),'status'=>boolval(1)),'','','','','');
        //return $query->rows;
		
		$lookup=array(
                        array(
                            'from' => 'oc_store',
                            'localField' => 'store_id',
                            'foreignField' => 'store_id',
                            'as' => 'st'
                        )
                    );
            $match=array('user_group_id'=>(int)(11),'status'=>boolval(1));
            $group=array();
            $query = $this->db->query("join",DB_PREFIX . "user",$lookup,'',$match,'','','','',0,$sort_array,'',$group);
            return $query->rows;
              
    }
	public function getmynotifications($data)
    {
		$log=new Log("notification-".date('Y-m-d').".log");
		$log->write('getnotifications called in model');
		$edate=date('Y-m-d');
		
		$datedata=array();
		//if((!empty($data['filter_date_added'])) && (strtotime($edate)))
		{
            $datedata=array(
                                    '$gte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime($edate)))  ))
                                );
		}
		if(!empty($datedata))
		{
			$match['validDate']=$datedata;
		}
		$match['userID']=(int)$data['username'];
		$log->write($match);
        $query = $this->db->query('select','oc_notifications','','','',$match);
        return $query->rows;
              
    }
	public function getnotificationsforWeb($data)
    {
		$match=array();
		if(!empty($data['filter_store']))
		{
			$match['store_id']=(int)$data['filter_store'];
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
        $query = $this->db->query('select','oc_notifications','','','',$match,'',(int)$data['limit'],'',(int)$data['start']);
        return $query;
              
    }
	public function deleterow($_id)
    {
        $query = $this->db->query('delete','oc_notifications',array('_id'=>new MongoID($_id)));
        return $query->rows;
              
    }
	public function getuserDetails($token)
    {
        $query = $this->db->query('select','oc_user','','','',array('token'=>$token));
        return $query;
              
    }

}