<?php
class ModelUserUser extends Model 
{
	public function insertCreditSmsTrans($data)
	{
		$input_array=array(
		
		'store_id'=>(int)$data["store_id"],
		'store_name'=>$data['store_name'],
		'user_id'=>(int)$data['user_id'],
		'telephone'=>$data['telephone'],
		'customer_name'=>$data['customer_name'],
		'credit'=>(float)$data['credit'],
		'username'=>$data['username'],
		'create_time'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
		);
        $user_trans_query = $this->db->query('insert',"oc_credit_sms_trans",$input_array);    
	}
	public function getCreditSmsTrans($data)
    {
		$where=array(
		'store_id'=>(int)$data['store_id'],
		'telephone'=>$data['telephone'],
		'create_time'=>array('$gte'=>new MongoDate(strtotime(date('Y-m-d'))))
		);
		$log=new Log("credit_sms-".date('Y-m-d').".log");
		$log->write(json_encode($where));
		$query = $this->db->query('select','oc_credit_sms_trans','','','',$where);
    	return $query;
    }
	
	public function insert_qr_login($username,$user_id,$store_id,$serv)
	{
		$mcrypt=new MCrypt();
		$serv1=explode(',',$serv);
		$id=$mcrypt->decrypt($serv1[1]);
        $token=$mcrypt->decrypt($serv1[0]);
		$user_trans_query = $this->db->query( 'insert',"oc_qr_login_trans",array('user_id'=>(int)$user_id,'username'=>$username,'token'=>$token,'store_id'=>(int)$store_id,'status'=>(int)0,'start_time'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))));
	}
	public function update_qr_login_status($user_id,$token,$status)
	{
		$user_trans_query = $this->db->query( 'update',"oc_qr_login_trans",array('user_id'=>(int)$user_id,'token'=>$token),array('status'=>(int)$status,'update_time'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))));
	}
    public function insert_t_n_c($data)
    {
        $query = $this->db->query('insert','oc_t_n_c',array('user_id'=>(int)$data['user_id'],'app_id'=>$data['app_id'],'tnc_id'=>$data['tnc_id'],'accept_time'=>new MongoDate(strtotime(date('Y-m-d')))));
                 
    }
	public function get_t_n_c($data) 
    {
        
        $match=array();
		if(!empty($data['user_id']))
		{
			$match['user_id']=(int)$data['user_id'];
		}
		if(!empty($data['app_id']))
		{
			$match['app_id']=(string)$data['app_id'];
		}
		if(!empty($data['tnc_id']))
		{
			//$match['tnc_id']=(string)$data['tnc_id'];
		}
		$limit=(int)1;
		$start=(int)0;
       
		//print_r(json_encode($match));
        $query = $this->db->query("select",DB_PREFIX . "t_n_c",'','','',$match,'',$limit,'',$start);
        return $query;
    }
	public function insert_referral($data)
    {
        $query = $this->db->query('insert','oc_referral',array('user_id'=>(int)$data['user_id'],'store_id'=>$data['store_id'],'store_name'=>$data['store_name'],'mobile_number'=>$data['mobile_number'],'name'=>$data['name'],'submit_time'=>new MongoDate(strtotime(date('Y-m-d')))));
                 
    }
	public function insert_whatsapp_inv($data)
    {
        $query = $this->db->query('insert','oc_whatsapp_inv',array('user_id'=>(int)$data['user_id'],'store_id'=>$data['store_id'],'store_name'=>$data['store_name'],'mobile_number'=>$data['mobile_number'],'inv_no'=>$data['inv_no'],'name'=>$data['name'].'-'.$data['inv_no'],'submit_time'=>new MongoDate(strtotime(date('Y-m-d')))));
                 
    }
	public function insert_contact($data)
    {

	$data['submit_time']= new MongoDate(strtotime(date('Y-m-d')));	
        $query = $this->db->query('insert','oc_contact',$data);
                 
    }
	public function getreferral($data) 
    {
        
        $match=array();
		if(!empty($data['filter_store']))
		{
			$match['store_id']=(string)$data['filter_store'];
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
           $limit=(int)$data['limit'];
			$start=(int)$data['start'];
        }
		//print_r(json_encode($match));
		$sort=array('submit_time'=>-1);
        $query = $this->db->query("select",DB_PREFIX . "referral",'','','',$match,'',$limit,'',$start,$sort);
        return $query;
    }
        public function update_device_token($token,$user_id)
        {
            $query = $this->db->query('update','oc_user',array('user_id'=>(int)$user_id),array('token'=>$token));
                 
        }
	public function updatestorename($store_id,$storename)
        {
            //$sql = "update oc_store set name='".$storename."' where `store_id`='".$store_id."' ";
            
            $query = $this->db->query('update','oc_store',array('store_id'=>(int)$store_id),array('name'=>$storename));
                 
        }
		
	public function getUserpasswordhistory($user_id)
    	{
		//$sql="SELECT * FROM oc_user_password_trans  WHERE user_id = '" . $user_id . "' and date(datetime)='".date('Y-m-d')."' ";
    		//$query = $this->db->query('select','oc_user_password_trans','','','',array('user_id '=>(int)$user_id));
		$query = $this->db->query('select','oc_user_password_trans','','','',array('user_id'=>(int)$user_id,'datetime'=>array('$gte'=>new MongoDate(strtotime(date('Y-m-d'))))));
    		return $query->rows;
    	}

	public function addUserpasswordhistory($user_id,$username,$imei,$change_forgot)
    	{
            $sid=$this->db->getNextSequenceValue('oc_user_password_trans');
            //$sql="insert into  oc_user_password_trans set user_id = '" . $user_id . "',datetime='".date('Y-m-d h:i:s')."',username='".$username."',imei='".$imei."',change_forgot='".$change_forgot."' ";
            $input_array=array(
                'sid'=>(int)$sid,
                'user_id'=>(int)$user_id,
                'datetime'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
                'username'=>$username,
                'imei'=>$imei,
                'change_forgot'=>$change_forgot);
            $query = $this->db->query('insert','oc_user_password_trans',$input_array);

    	}

	public function get_user_stores($user_id)
    	{
            //$this->db->query("SELECT store_id FROM oc_user_to_store  WHERE user_id = '" . (int)$user_id . "'");
            $query = $this->db->query('select','oc_user_to_store','','','',array('user_id'=>(int)$user_id));
            return $query->rows;
    	}

        public function addUser($data) 
        {
			$log=new Log("signup-".date('Y-m-d').".log");
			$log->write('in model');
			$log->write($data);    
            $user_id=$this->db->getNextSequenceValue('oc_user');
            $input_array=array(
                    'user_id'=>(int)$user_id,
                    'username'=>$this->db->escape($data['username']),
                    'user_group_id'=>(int)$data['user_group_id'],
                    'salt'=>$this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)),
                    'password'=>$this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))),
                    'firstname'=>$this->db->escape($data['firstname']),
                    'lastname'=>$this->db->escape($data['lastname']),
                    'email'=>$this->db->escape($data['email']),
                    'company_id'=>$this->db->escape($data['config_company']),
                    'image'=>$this->db->escape($data['image']),
					'images'=>$this->db->escape(array($data['image'])),
                    'status'=>boolval($data['status']),
                    'store_id'=>(int)$data['user_store_id'][0],
			   'State_Code'=>(int)($data['State_Code']),
			   'State_Name'=>$this->db->escape($data['State_Name']),
			   'Dist_Code'=>(int)($data['Dist_Code']),
			   'Dist_Name'=>$this->db->escape($data['Dist_Name']),
                    'token'=>($data['token']),
                    'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                    );
			$log->write($input_array);
            $query = $this->db->query("insert",DB_PREFIX . "user",$input_array);
            $log->write('after add in table');
            $this->db->query('delete',DB_PREFIX . "user_to_store",array('user_id'=>(int)$user_id));
            foreach($data['user_store_id'] as $store_id)
            {
                $input_array=array(
                'user_id'=>(int)$user_id,
                'store_id'=>(int)$store_id
                );
                $this->db->query('insert',DB_PREFIX . "user_to_store",$input_array);
            }
            $this->db->query('delete',DB_PREFIX . "user_to_unit",array('user_id'=>(int)$user_id));
            foreach($data['config_unit'] as $unit_id)
            {
                $input_array2=array(
                'user_id'=>(int)$user_id,
                'unit_id'=>(int)$unit_id,
                'company_id'=>(int)$data['config_company']
                );
                $this->db->query('insert',DB_PREFIX . "user_to_unit",$input_array2);
            }
            if($data['user_group_id']=="22")/////////for runner
            {
				$this->db->query('delete',DB_PREFIX . "runner_to_store",array('user_id'=>(int)$user_id));
                foreach($data['user_store_id'] as $store_id)
                {   
                    $input_array3=array(
                    'user_id'=>(int)$user_id,
                    'store_id'=>(int)$store_id
                    );
                    $this->db->query('insert',DB_PREFIX . "runner_to_store",$input_array3);
                }
            }
			$log->write('before return user_id');
			$log->write($user_id);
            return $user_id;
        }

    public function editUser($user_id, $data) 
    {
        $log=new Log("editUser-".date('Y-m-d').".log");
        $log->write('editUsercall model');
        if(empty($data['password'])) 
        {
            $log->write('in if');
            $update_array=array(
               'username'=>$this->db->escape($data['username']),
               'user_group_id'=>(int)$data['user_group_id'],
               'firstname'=>$this->db->escape($data['firstname']),
               'lastname'=>$this->db->escape($data['lastname']),
               'email'=>$this->db->escape($data['email']),
               'company_id'=>$this->db->escape($data['config_company']),
               'image'=>$this->db->escape($data['image']),
               'status'=> boolval($data['status']),
               'store_id'=>(int)$data['user_store_id'][0],
			   'State_Code'=>(int)($data['State_Code']),
			   'State_Name'=>$this->db->escape($data['State_Name']),
			   'Dist_Code'=>(int)($data['Dist_Code']),
			   'Dist_Name'=>$this->db->escape($data['Dist_Name'])
               );
            //$log->write($update_array);
            $this->db->query('update',DB_PREFIX . "user",array('user_id'=>(int)$user_id),$update_array); 
        }
        else 
        {
            $log->write('in else');
            $update_array=array(
               'username'=>$this->db->escape($data['username']),
               'user_group_id'=>(int)$data['user_group_id'],
               'firstname'=>$this->db->escape($data['firstname']),
               'lastname'=>$this->db->escape($data['lastname']),
               'email'=>$this->db->escape($data['email']),
               'company_id'=>$this->db->escape($data['config_company']),
               'image'=>$this->db->escape($data['image']),
               'status'=>boolval($data['status']),
               'store_id'=>(int)$data['user_store_id'][0],
               'salt'=>$this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)),
               'password'=>$this->db->escape(sha1($salt . sha1($salt . sha1($data['password']))))
               );
            //$log->write($update_array);
            $this->db->query('update',DB_PREFIX . "user",array('user_id'=>(int)$user_id),$update_array);
        }
        $this->db->query('delete',DB_PREFIX . "user_to_store",array('user_id'=>(int)$user_id));
        foreach($data['user_store_id'] as $store_id)
        {
            $input_array=array(
               'user_id'=>(int)$user_id,
               'store_id'=>(int)$store_id
               );
            $this->db->query('insert',DB_PREFIX . "user_to_store",$input_array);
        
        }
        $this->db->query('delete',DB_PREFIX . "user_to_unit",array('user_id'=>(int)$user_id));
        foreach($data['config_unit'] as $unit_id)
        {
            $input_array2=array(
               'user_id'=>(int)$user_id,
               'unit_id'=>(int)$unit_id,
               'company_id'=>(int)$data['config_company']
               );
            $this->db->query('insert',DB_PREFIX . "user_to_unit",$input_array2);
        }
        
	if($data['user_group_id']=="22")/////////for runner
        {
            $this->db->query('delete',DB_PREFIX . "runner_to_store",array('user_id'=>(int)$user_id));
            foreach($data['user_store_id'] as $store_id)
            {
                $input_array3=array(
                'user_id'=>(int)$user_id,
                'store_id'=>(int)$store_id
                );
                $this->db->query('insert',DB_PREFIX . "runner_to_store",$input_array3);
            }
        }
    }
    public function editPassword($user_id, $password) 
    {
        $update_array=array(
               'salt'=>$this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)),
               'password'=>$this->db->escape(sha1($salt . sha1($salt . sha1($password)))),
                'code'=>''
            );
        $this->db->query('update',DB_PREFIX . "user",array('user_id'=>(int)$user_id),$update_array);
    }
    public function editCode($email, $code) 
    {
	$update_array=array(
                'code'=>$this->db->escape($code)
               );
        $this->db->query('update',DB_PREFIX . "user",array('email'=>$this->db->escape(utf8_strtolower($email))),$update_array);
    }

    public function editCodeUser($email, $code) 
    {
	$update_array=array(
                'code'=>$this->db->escape($code)
               );
        $this->db->query('update',DB_PREFIX . "user",array('username'=>$this->db->escape(utf8_strtolower($email))),$update_array);
    }
    public function deleteUser($user_id) 
    {
	$this->db->query('delete',DB_PREFIX . "user",array('user_id'=>(int)$user_id));
    }

    public function getUser($user_id) 
    {
        $lookup=array(
                'from' => 'oc_user_group',
                'localField' => 'user_group_id',
                'foreignField' => 'user_group_id',
                'as' => 'user_group'
            );
        $match=array('user_id'=>(int)$user_id);
        $query = $this->db->query("join",DB_PREFIX . "user",$lookup,'$user_group',$match);
        return $query->row[0];
    }

	public function getUserByUsername($username) 
    {
		$log=new Log("getUserByUsername-".date('Y-m-d').".log");
        $log->write("getUserByUsername CALL");
		$log->write($username);
		if(strlen($username)<10)
		{
			$query=$this->db->query('select',DB_PREFIX . "user",'','','',array('user_id'=>(int)$this->db->escape($username)));
		}
		else
		{
            $query=$this->db->query('select',DB_PREFIX . "user",'','','',array('username'=>$this->db->escape($username)));
		}
		$log->write($query->row);
        return $query->row;
	}
	
	
	
	
	public function count_upload_image($transid)
	{
		
		$log=new Log("upload_profile-".date('Y-m-d').".log");
		$log->write('count_upload_image called');
       $match=array('order_id'=>(int)$transid);
	   $log->write($match);
       $query = $this->db->query('select','oc_user_temp_img','','','',$match);
	    $log->write($query);
       return $query->row['images'];
	}
	
	public function update_profile_image($transid,$file)
	{ 
	
	 $log=new Log("upload_profile-".date('Y-m-d').".log");
		$log->write('update_profile_image called');
		$match=array(
             'order_id'=> (int)$transid   
           );
		   $log->write($match);
		$query23 = $this->db->query("select","oc_user_temp_img",'','','',$match);
		$log->write($query23);
		if($query23->num_rows>0)
		{
			$images=$query23->row['images'];
		
			$images[]=$file;
			$udata=array(
              'images'=> $images
           );
           $log->write($images);
           $query = $this->db->query('update','oc_user_temp_img',$match,$udata);
		}
		else
		{
			$images=array($file);
			
			$udata2=array(
             'order_id'=> (int)$transid,
			'images'=> $images
           );
			$query = $this->db->query('insert','oc_user_temp_img',$udata2);
		}
    }
	public function update_user_images($user_id,$file,$sid)
	{ 
	
		$log=new Log("signup-".date('Y-m-d').".log");
		$log->write('update_user_images called');
		$match=array(
             'user_id'=> (int)$user_id   
           );
		   $log->write($match);
		
		$udata=array(
				'image'=> $file[0],
				'images'=> $file
           );
           $log->write($udata);
           $query = $this->db->query('update','oc_user',$match,$udata); 
		$deletematch=array('order_id'=> (int)$sid);
		$query = $this->db->query('delete','oc_user_temp_img',$deletematch);
		
    }
	public function getUserByCode($code) 
        {
            $query = $this->db->query('select','oc_user','','','',array('code'=>$this->db->escape($code)));
            return $query->row;
	}

	public function getUsers($data = array()) 
        {
            $lookup=array(array(
                'from' => 'oc_store',
                'localField' => 'store_id',
                'foreignField' => 'store_id',
                'as' => 'oc_store'
                ),array(
                'from' => 'oc_user_group',
                'localField' => 'user_group_id',
                'foreignField' => 'user_group_id',
                'as' => 'oc_user_group'
            ));
            $match=array();
            if($data['filter_user_group_id']!='')
            {
                $match['user_group_id']=(int)$data['filter_user_group_id'];
            }
            if($data['filter_name']!='')
            {
                    $search_string=$data['filter_name'];
                    $match['firstname']=new MongoRegex("/.*$search_string/i");
            }
            if($data['filter_store']!='')
            {
                $match['store_id']=(int)$data['filter_store'];
            }
            if($data['filter_mobile']!='')
            {
                $search_string2=$data['filter_mobile'];
                $match['username']=new MongoRegex("/.*$search_string2/i");
            }
			
			if (!empty($data['filter_date_added'])) 
			{
            
			$sdate=$this->db->escape($data['filter_date_added']);
			$edate=$this->db->escape($data['filter_date_added']);
			}
			 
			$datedata=array();
			if((!empty($data['filter_date_added'])) && (strtotime($sdate)==strtotime($edate)))
			{
                $datedata=array(
                                    '$gt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime($sdate)))  )),
                                    '$lt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('1 day', strtotime($edate))) ))
                                );
			}
			
			if(!empty($datedata))
			{
				$match['date_added']=$datedata;
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
                $limit=(int)$data['limit'];
				$start=(int)$data['start'];
            }
            $sort_array=array('firstname'=>1,'date_added'=>-1);
			//$sort_array=array('date_added'=>-1);
            $query = $this->db->query("join",DB_PREFIX . "user",$lookup,'',$match,'','',$limit,'',$start,$sort_array,'','',$match);
			//print_r($query);
            foreach($query->row as $row)
            {
                $return_array[]=array(
                            'user_id'=>$row['user_id'],
                            'user_group_id'=>$row['user_group_id'],
                            'username'=>$row['username'],
                            'firstname'=>$row['firstname'],
                            'lastname'=>$row['lastname'],
                            'email'=>$row['email'],
							 
                            'store_id'=>$row['store_id'],
                            'cash'=>$row['cash'],
                            'status'=>$row['status'],
                            'date_added'=>date('Y-m-d H:i:s', $row['date_added']->sec),
                            'store_name'=>$row['oc_store'][0]['name'],
                            'user_group_name'=>$row['oc_user_group'][0]['name'],
                            'token'=>$row['token'],
                            'totalrows'=>$query->total_rows    
                    );
            }
            return $return_array;
	}



public function getRetailer($data = array()) 
	{
		
             $lookup=array(array(
                'from' => 'oc_store',
                'localField' => 'store_id',
                'foreignField' => 'store_id',
                'as' => 'oc_store'
                ),array(
                'from' => 'oc_user_group',
                'localField' => 'user_group_id',
                'foreignField' => 'user_group_id',
                'as' => 'oc_user_group'
            ));
            $match=array();
            if($data['filter_user_group_id']!='')
            {
                $match['user_group_id']=(int)$data['filter_user_group_id'];
            }
            if($data['filter_name']!='')
            {
                    $search_string=$data['filter_name'];
                    $match['firstname']=new MongoRegex("/.*$search_string/i");
            }
            if($data['filter_store']!='')
            {
                $match['store_id']=(int)$data['filter_store'];
            }
			
			if($data['filter_Dist_Code']!='')
            {
                $match['Dist_Code']=(int)$data['filter_Dist_Code'];
            }
            if($data['filter_mobile']!='')
            {
                $search_string2=$data['filter_mobile'];
                $match['username']=new MongoRegex("/.*$search_string2/i");
            }
			
			if (!empty($data['filter_date_added'])) 
			{
            
			$sdate=$this->db->escape($data['filter_date_added']);
			$edate=$this->db->escape($data['filter_date_added']);
			}
			 
			$datedata=array();
			if((!empty($data['filter_date_added'])) && (strtotime($sdate)==strtotime($edate)))
			{
                $datedata=array(
                                    '$gt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime($sdate)))  )),
                                    '$lt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('1 day', strtotime($edate))) ))
                                );
			}
			
			if(!empty($datedata))
			{
				$match['date_added']=$datedata;
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
                $limit=(int)$data['limit'];
				$start=(int)$data['start'];
            }
            $sort_array=array('firstname'=>1,'date_added'=>-1);
			//$sort_array=array('date_added'=>-1);
            $query = $this->db->query("join",DB_PREFIX . "user",$lookup,'',$match,'','',$limit,'',$start,$sort_array,'','',$match);
			//print_r($query);
            foreach($query->row as $row)
            {
                $return_array[]=array(
                            'user_id'=>$row['user_id'],
                            'user_group_id'=>$row['user_group_id'],
                            'username'=>$row['username'],
                            'firstname'=>$row['firstname'],
                            'lastname'=>$row['lastname'],
                            'email'=>$row['email'],
							 
                            'store_id'=>$row['store_id'],
                            'cash'=>$row['cash'],
                            'status'=>$row['status'],
                            'date_added'=>date('Y-m-d H:i:s', $row['date_added']->sec),
                            'store_name'=>$row['oc_store'][0]['name'],
                            'user_group_name'=>$row['oc_user_group'][0]['name'],
                            'token'=>$row['token'],
                            'totalrows'=>$query->total_rows    
                    );
            }
            return $return_array;
	}








	public function getTotalUsers($data = array()) 
        {
		$sql="SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` where oc_user.user_id!='' ";
		if($data['filter_user_group_id']!='')
		{

		$sql.=" and oc_user.user_group_id=ifnull('".$data['filter_user_group_id']."',oc_user.user_group_id)";
	}
		if($data['filter_name']!='')
		{
			$sql.="  and concat(oc_user.firstname,' ',oc_user.lastname) like '%".$data['filter_name']."%' ";
		}
if($data['filter_mobile']!='')
		{
			$sql.="  and oc_user.username like '%".$data['filter_mobile']."%' ";
		}
		if($data['filter_store']!='')
		{
			$sql.=" and oc_user.store_id='".$data['filter_store']."' ";
		}
		$query = $this->db->query($sql);

		return $query->row['total']; 
	}

	public function getTotalUsersByGroupId($user_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` WHERE user_group_id = '" . (int)$user_group_id . "'");

		return $query->row['total'];
	}

	public function getTotalUsersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}
        public function getUsersByGroupId($user_group_id) 
        {
            //$query = $this->db->query("SELECT user_id, username FROM `" . DB_PREFIX . "user` WHERE user_group_id = '" . (int)$user_group_id."' LIMIT 1");
            $query = $this->db->query('select','user','','','',array('user_group_id'=>(int)$user_group_id));
            return $query->rows;
        }
	public function getUnitbyUser($user_id) 
        {
            $query = $this->db->query('select','oc_user_to_unit','','','',array('user_id'=>(int)$user_id));
            return $query->rows;
	}
        public function getRegistrationotpTransId($sid) 
        {
            //$query = $this->db->query("SELECT sid,otp,system_trans_id,imei FROM `" . DB_PREFIX . "member_registration_otp_trans` WHERE LCASE(system_trans_id) = '" . $this->db->escape(utf8_strtolower($sid)) . "'");
            $data=array('sid','otp','system_trans_id','imei');
            $query = $this->db->query('select','oc_member_registration_otp_trans','','','',array('system_trans_id'=>$this->db->escape(utf8_strtolower($sid))),'','',$data);
            return $query->row;
        }
        
        
        public function insert_member_registration_otp_trans($data)
        {
            $log=new Log("member_registration_otp_trans-".date('Y-m-d').".log");
            //$sql="insert into oc_member_registration_otp_trans  SET transtype='".$data['ttype']."',otp='".$data['otp']."',trans_detail='".$data['products']."',system_trans_id = '" . $this->db->escape($data['system_trans_id']) . "',imei = '" . $this->db->escape($data['imei']) . "', cr_date=NOW()";               
            
            //$ret_id=$this->db->query($sql);
            //$retid=$this->db->getLastId();
            $last_id=$this->db->getNextSequenceValue('oc_member_registration_otp_trans');
            $fdata=array(
                        'sid'=>$last_id,
                        'transtype'=>$data['ttype'],
                        'otp'=>$data['otp'],
                        'trans_detail'=>$data['products'],
                        'system_trans_id'=>$this->db->escape($data['system_trans_id']),
                        'imei'=> $this->db->escape($data['imei']),
                        'cr_date' => new MongoDate(strtotime(date('Y-m-d')))
                        );
            $this->db->query('insert','oc_member_registration_otp_trans',$fdata);
            return $last_id;
        }
        public function getVerifyUserOtp($sid) 
        {
            //$query = $this->db->query("SELECT sid,otp,system_trans_id,imei FROM `" . DB_PREFIX . "member_registration_otp_trans` WHERE LCASE(sid) = '" . $this->db->escape(utf8_strtolower($sid)) . "'");
            $data=array('sid','otp','system_trans_id','imei');
            $query = $this->db->query('select','oc_member_registration_otp_trans','','','',array('sid'=>(int)$this->db->escape(utf8_strtolower($sid))),'','',$data);
            return $query->row;
        }
        public function getBasicCategory($sid) 
        {
            $data=array('category_id','store_id');
            $query = $this->db->query('select','oc_category_to_store','','','',array('store_id'=>(int)$this->db->escape(utf8_strtolower($sid))),'','',$data);
            return $query->rows;
        }
        
        public function setBasicCategory($category_id,$store_id)
        {
            $input_array=array('category_id'=>(int)$category_id,
                                    'store_id'=>(int)$store_id
                              );
            $this->db->query('insert',DB_PREFIX . 'category_to_store',$input_array);
        }
        public function getGroupMenu($groupid) 
        { 
            $data=array('category_id','store_id','menutype','parent_id','sort_order','group_id');
            $query = $this->db->query('select','oc_storemenu_to_group','','','',array('group_id'=>(int)$this->db->escape($groupid)),'','',$data);
            return $query->rows;
        }
        public function setUserMenu($category_id,$store_id,$menutype,$parent_id,$sort_order,$user)
        {
            $input_array=array('category_id'=>(int)$category_id,
                               'user_id'=>(int)$user,
                               'store_id'=>(int)$store_id,
                               'menutype'=>(int)$menutype,
                               'parent_id'=>(int)$parent_id,
                               'sort_order'=>(int)$sort_order
                              );
            $this->db->query('insert','oc_storemenu_to_user',$input_array);
        }
}