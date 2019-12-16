<?php
class ModelSaleCustomer extends Model 
{


public function getstates($data=array()) 
    {
        $where=array('status'=>true);
        $group=array(
            array(
                '_id'=>array('code'=>'$State_Code','name'=>'$State_Name')
            )
        );
        $sort=array('_id.name'=>1);
        $query=$this->db->query('join','oc_state_district_map','','',$where,'','',(int)$data['limit'],array(),(int)$data['start'],$sort,'',$group);
        //$query = $this->db->query("join" , DB_PREFIX . "state_district_map" ,'','',$where,'');
        return $query;
    }

    public function getdisctricts($data=array()) 
    {
        $where=array();
        if(!empty($data['state_code']))
        {
            $where['State_Code']=(int)$data['state_code'];
        }
        $sort=array('District_Name'=>1);
        $query=$this->db->query('select','oc_state_district_map','','','',$where,'',(int)$data['limit'],array(),(int)$data['start'],$sort,'',$group);
        return $query;
    }

	public function getCustomerByTelephone($telephone) 
	{
		$query = $this->db->query("select" , DB_PREFIX . "customer" ,'','','',array('telephone' =>$this->db->escape($telephone)));

		return $query->row;
	}
    public function update_customer_info($customerID,$data)
    {
        if (isset($data['address'])) 
        {
            $this->db->query("delete from  " . DB_PREFIX . "address where customer_id = '" . (int)$customerID . "' ");
			foreach ($data['address'] as $address) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($address['firstname']) . "', lastname = '" . $this->db->escape($address['lastname']) . "', company = '" . $this->db->escape($address['company']) . "', address_1 = '" . $this->db->escape($address['address_1']) . "', address_2 = '" . $this->db->escape($address['address_2']) . "', city = '" . $this->db->escape($address['city']) . "', postcode = '" . $this->db->escape($address['postcode']) . "', country_id = '" . (int)$address['country_id'] . "', zone_id = '" . (int)$address['zone_id'] . "', crop1 = '" . (int)$address['crop1'] . "',acre1 = '" . (int)$address['acre1'] . "',crop2 = '" . (int)$address['crop2'] . "',acre2 = '" . (int)$address['acre2'] . "', custom_field = '" . $this->db->escape(isset($address['custom_field']) ? serialize($address['custom_field']) : '') . "'");

				if (isset($address['default'])) {
					$address_id = $this->db->getLastId();  

					$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
				}
			}
		} 
    }

    public function addCustomer($data) 
    {
        $log=new Log("addcustomer-".date('Y-m-d').".log");
		$log->write("in model for addcustomer ");
        $customer_id=$this->db->getNextSequenceValue('oc_customer');
        $existing=$this->getCustomers(array('filter_telephone'=>$data['telephone']));//,'filter_store_id'=>(int)$data['store_id']));
		
        if($existing->num_rows==0)
        {
			$log->write("in if customer info empty ");
            $input_array=array(
            'customer_id'=>(int)$customer_id,
            'customer_group_id'=>(int)$data['customer_group_id'],
            'firstname'=>$this->db->escape($data['firstname']),
            'lastname'=>$this->db->escape($data['lastname']),
            'email'=>$this->db->escape($data['email']),
            'telephone'=>$this->db->escape($data['telephone']),
            'fax'=>$this->db->escape($data['fax']),
            'custom_field'=>$this->db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : ''),
            'newsletter'=>(int)$data['newsletter'],
            'salt'=>$this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)),
            'password'=>$this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))),
            'status'=>boolval($data['status']),
            'approved'=>boolval($data['approved']),
            'safe'=>(int)$data['safe'],
            'store_id'=>array((int)$this->db->escape(isset($data['store_id'])?$data['store_id']:'0')),
            'card'=>$this->db->escape(isset($data['card'])?$data['card']:'0'),
            'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
            'addedby'=>$this->db->escape($data['username']),
            'aadhar'=>$this->db->escape($data['aadhar']),
            'otp'=>$this->db->escape($data['otp']),
            'ip'=>$this->db->escape($data['ip']),
            'imei'=>$this->db->escape($data['imei']),
            'scheme'=>$this->db->escape($data['scheme']),
                'state_code'=>$this->db->escape($data['state_code']),
                'state_name'=>$this->db->escape($data['state_name']),
                'dist_code'=>$this->db->escape($data['dist_code']),
                'dist_name'=>$this->db->escape($data['dist_name']),
                'village'=>$this->db->escape($data['village']),
                'card_number'=>$this->db->escape($data['card_number']),
                'unnati_mitra'=>(int)$this->db->escape($data['unnati_mitra']),
            'reward'=>(int)0,
            'credit'=> (float)0
			);
        
			$this->db->query('insert',DB_PREFIX . "customer",$input_array);
			$this->session->data['cid']=$customer_id ;
			if (isset($data['address'])) 
			{
				foreach ($data['address'] as $address) 
				{
					$address_id = $this->db->getNextSequenceValue('oc_address');
					$input_array2=array(
                    'address_id'=>(int)$address_id,
                    'customer_id'=>(int)$customer_id,
                    'firstname'=>$this->db->escape($address['firstname']), 
                    'lastname'=>$this->db->escape($address['lastname']), 
                    'company'=>$this->db->escape($address['company']),
                    'address_1'=>$this->db->escape($address['address_1']),
                    'address_2'=>$this->db->escape($address['address_2']),
                    'city'=>$this->db->escape($address['city']), 
                    'postcode'=>$this->db->escape($address['postcode']),
                    'country_id'=>(int)$address['country_id'],
                    'zone_id'=>(int)$address['zone_id'],
                    'crop1'=>(int)$address['crop1'],
                    'acre1'=>(int)$address['acre1'],
                    'crop2'=>(int)$address['crop2'],
                    'acre2'=>(int)$address['acre2'],
                    'custom_field'=>$this->db->escape(isset($address['custom_field']) ? serialize($address['custom_field']) : '')
                    );
					$this->db->query('insert',DB_PREFIX . "address",$input_array2);
					if (isset($address['default'])) 
					{
						$this->db->query('update',DB_PREFIX . "customer",array('customer_id'=>(int)$customer_id),array('address_id' =>(int)$address_id ));
					}
				}
			}
			return $customer_id;
		}
		else 
		{
                    //return 'existing';
					$log->write('if customer already for this store');
                    $customer=$existing->row;
					$log->write($customer['store_id']);
                    if (!in_array($data['store_id'], $customer['store_id']))
                    {
                        $customer['store_id'][]=(int)$data['store_id'];
						$log->write($customer['store_id']);
                        //$customer['credit'][]=array((int)$data['store_id']=> (float)0);
                        $query24 = $this->db->query("update",DB_PREFIX . "customer",array('telephone'=>$data['telephone']),array('store_id'=>$customer['store_id']));
                    }
		}
	}
	public function updateCustomer($data) 
    {
		$log=new Log("addcustomer-".date('Y-m-d').".log");
		$log->write("in model for updateCustomer ");
        $input_array=array(
            'firstname'=>$this->db->escape($data['firstname']),
            'state_code'=>$this->db->escape($data['state_code']),
                'state_name'=>$this->db->escape($data['state_name']),
                'dist_code'=>$this->db->escape($data['dist_code']),
                'dist_name'=>$this->db->escape($data['dist_name']),
                'village'=>$this->db->escape($data['village']),
                'card_number'=>$this->db->escape($data['card_number']),
            'unnati_mitra'=>(int)$this->db->escape($data['unnati_mitra'])
            
           );
        $this->db->query('update',DB_PREFIX . "customer",array('telephone'=>$this->db->escape($data['telephone'])),$input_array);
			
	}
    public function addCustomer_by_mobile($data) 
    {
       $log=new Log("addcust-".date('Y-m-d').".log");
        $customer_id=$this->db->getNextSequenceValue('oc_customer');
        
        $input_array=array(
            'customer_id'=>(int)$customer_id,
            'customer_group_id'=>(int)$data['customer_group_id'],
            'firstname'=>$this->db->escape($data['firstname']),
            'lastname'=>$this->db->escape($data['lastname']),
            'email'=>$this->db->escape($data['email']),
            'telephone'=>$this->db->escape($data['telephone']),
            'fax'=>$this->db->escape($data['fax']),
            'custom_field'=>$this->db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : ''),
            'newsletter'=>(int)$data['newsletter'],
            'salt'=>$this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)),
            'password'=>$this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))),
            'status'=>boolval($data['status']),
            'approved'=>boolval($data['approved']),
            'safe'=>(int)$data['safe'],
            'store_id'=>(int)$this->db->escape(isset($data['store_id'])?$data['store_id']:'0'),
            'card'=>$this->db->escape(isset($data['card'])?$data['card']:'0'),
            'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
            'addedby'=>$this->db->escape($data['username']),
            'aadhar'=>$this->db->escape($data['aadhar']),
            'otp'=>$this->db->escape($data['otp']),
            'ip'=>$this->db->escape($data['ip']),
            'imei'=>$this->db->escape($data['imei']),
            'scheme'=>$this->db->escape($data['scheme']));
        
        $this->db->query('insert',DB_PREFIX . "customer",$input_array);
       
        $this->session->data['cid']=$customer_id ;
	if (isset($data['address'])) 
        {
            foreach ($data['address'] as $address) 
            {
                $address_id = $this->db->getNextSequenceValue('oc_address');
		$input_array2=array(
                    'address_id'=>(int)$address_id,
                    'customer_id'=>(int)$customer_id,
                    'firstname'=>$this->db->escape($address['firstname']), 
                    'lastname'=>$this->db->escape($address['lastname']), 
                    'company'=>$this->db->escape($address['company']),
                    'address_1'=>$this->db->escape($address['address_1']),
                    'address_2'=>$this->db->escape($address['address_2']),
                    'city'=>$this->db->escape($address['city']), 
                    'postcode'=>$this->db->escape($address['postcode']),
                    'country_id'=>(int)$address['country_id'],
                    'zone_id'=>(int)$address['zone_id'],
                    'crop1'=>(int)$address['crop1'],
                    'acre1'=>(int)$address['acre1'],
                    'crop2'=>(int)$address['crop2'],
                    'acre2'=>(int)$address['acre2'],
                    'custom_field'=>$this->db->escape(isset($address['custom_field']) ? serialize($address['custom_field']) : '')
                    );
                $this->db->query('insert',DB_PREFIX . "address",$input_array2);
		if (isset($address['default'])) 
                {
                    $this->db->query('update',DB_PREFIX . "customer",array('customer_id'=>(int)$customer_id),array('address_id' =>(int)$address_id ));
                }
            }
	}     
        return $customer_id;
    }
    public function addCustomer_by_call($data) 
    {
        $log=new Log("addcust-".date('Y-m-d').".log");
        $customer_id=$this->db->getNextSequenceValue('oc_customer');
        
        $input_array=array(
            'customer_id'=>(int)$customer_id,
            'customer_group_id'=>(int)$data['customer_group_id'],
            'firstname'=>$this->db->escape($data['firstname']),
            'lastname'=>$this->db->escape($data['lastname']),
            'email'=>$this->db->escape($data['email']),
            'telephone'=>$this->db->escape($data['telephone']),
            'fax'=>$this->db->escape($data['fax']),
            'custom_field'=>$this->db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : ''),
            'newsletter'=>(int)$data['newsletter'],
            'salt'=>$this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)),
            'password'=>$this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))),
            'status'=>boolval($data['status']),
            'approved'=>boolval($data['approved']),
            'safe'=>(int)$data['safe'],
            'store_id'=>(int)$this->db->escape(isset($data['store_id'])?$data['store_id']:'0'),
            'card'=>$this->db->escape(isset($data['card'])?$data['card']:'0'),
            'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
            'addedby'=>$this->db->escape($data['username']),
            'aadhar'=>$this->db->escape($data['aadhar']),
            'otp'=>$this->db->escape($data['otp']),
            'ip'=>$this->db->escape($data['ip']),
            'imei'=>$this->db->escape($data['imei']),
            'scheme'=>$this->db->escape($data['scheme']));
        
        $this->db->query('insert',DB_PREFIX . "customer",$input_array);
       
        $this->session->data['cid']=$customer_id ;
		if (isset($data['address'])) 
        {
            foreach ($data['address'] as $address) 
            {
                $address_id = $this->db->getNextSequenceValue('oc_address');
		$input_array2=array(
                    'address_id'=>(int)$address_id,
                    'customer_id'=>(int)$customer_id,
                    'firstname'=>$this->db->escape($address['firstname']), 
                    'lastname'=>$this->db->escape($address['lastname']), 
                    'company'=>$this->db->escape($address['company']),
                    'address_1'=>$this->db->escape($address['address_1']),
                    'address_2'=>$this->db->escape($address['address_2']),
                    'city'=>$this->db->escape($address['city']), 
                    'postcode'=>$this->db->escape($address['postcode']),
                    'country_id'=>(int)$address['country_id'],
                    'zone_id'=>(int)$address['zone_id'],
                    'crop1'=>(int)$address['crop1'],
                    'acre1'=>(int)$address['acre1'],
                    'crop2'=>(int)$address['crop2'],
                    'acre2'=>(int)$address['acre2'],
                    'custom_field'=>$this->db->escape(isset($address['custom_field']) ? serialize($address['custom_field']) : '')
                    );
                $this->db->query('insert',DB_PREFIX . "address",$input_array2);
		if (isset($address['default'])) 
                {
                    $this->db->query('update',DB_PREFIX . "customer",array('customer_id'=>(int)$customer_id),array('address_id' =>(int)$address_id ));
                }
            }
	}
        return $customer_id;
    }
    public function editCustomer($customer_id, $data) 
    {
		if (!isset($data['custom_field'])) {
			$data['custom_field'] = array();
		}

		$this->db->query("update" , DB_PREFIX . "customer",array('customer_id' =>(int)$customer_id),array('customer_group_id'=>(int)$data['customer_group_id'],'firstname'=>$this->db->escape($data['firstname']),'lastname'=>$this->db->escape($data['lastname']),'email'=>$this->db->escape($data['email']),'telephone'=>$this->db->escape($data['telephone']),'fax'=>$this->db->escape($data['fax']),'custom_field'=>$this->db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : ''),'newsletter'=>(int)$data['newsletter'],'status'=>boolval($data['status']),'approved'=>boolval($data['approved']),'safe'=>(int)$data['safe']));

		if ($data['password']) 
                {
			$this->db->query("update" , DB_PREFIX . "customer",array('customer_id' =>(int)$customer_id),array('salt'=>$this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)),'password'=>$this->db->escape(sha1($salt . sha1($salt . sha1($data['password']))))));
		}

		$this->db->query("delete" , DB_PREFIX . "address",array('customer_id' =>(int)$customer_id));

		if (isset($data['address'])) 
                {
			foreach ($data['address'] as $address) 
                        {
				if (!isset($address['custom_field'])) 
                                {
					$address['custom_field'] = array();
				}

				$this->db->query("insert" , DB_PREFIX . "address",array('address_id'=> (int)$address['address_id'],'customer_id'=>(int)$customer_id,'firstname'=>$this->db->escape($address['firstname']),'lastname'=>$this->db->escape($address['lastname']),'company'=>$this->db->escape($address['company']),'address_1'=>$this->db->escape($address['address_1']),'address_2'=>$this->db->escape($address['address_2']),'city'=>$this->db->escape($address['city']),'postcode'=>$this->db->escape($address['postcode']),'country_id'=>(int)$address['country_id'],'zone_id'=>(int)$address['zone_id'],'custom_field'=>$this->db->escape(isset($address['custom_field']) ? serialize($address['custom_field']) : '')));

				if (isset($address['default'])) 
                                {
					$address_id = $address['address_id'];//$this->db->getLastId();

					$this->db->query("update" , DB_PREFIX . "customer",array('customer_id'=>(int)$customer_id),array('address_id'=>(int)$address_id));
				}
			}
		}
	}

	public function editToken($customer_id, $token) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET token = '" . $this->db->escape($token) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function deleteCustomer($customer_id) 
        {
            $this->db->query("delete" , DB_PREFIX . "customer",array('customer_id'=>(int)$customer_id));
            $this->db->query("delete" , DB_PREFIX . "customer_reward",array('customer_id'=>(int)$customer_id));
            $this->db->query("delete" , DB_PREFIX . "customer_transaction",array('customer_id'=>(int)$customer_id));
            $this->db->query("delete" , DB_PREFIX . "customer_ip",array('customer_id'=>(int)$customer_id));
            $this->db->query("delete" , DB_PREFIX . "address",array('customer_id'=>(int)$customer_id));
	}

	public function getCustomer($customer_id) 
        {
            $where=array('customer_id'=>(int)$customer_id);
            $sort_data = array(
			'customer_id'=>-1
		);

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 1;
			}
		}

		$query = $this->db->query('select',DB_PREFIX . "customer",'','','',$where,'',(int)$data['limit'],'',(int)$data['start'],$sort_data);

		return $query->row;
		
	}

	public function getCustomerByEmail($email) 
	{
		$query = $this->db->query("select" , DB_PREFIX . "customer" ,'','','',array('email' =>$this->db->escape(utf8_strtolower($email))));

		return $query->row;
	}
public function getCustomerByEmailApp($email) {
                            $log=new Log("addcust-".date('Y-m-d').".log");

		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
                            if(count($query->row)>0)
                            {
                               $sql2="update oc_customer SET custom_field = 'app'  where email = '" . $this->db->escape($email) . "'";

                               $log->write($sql2);
                              
		   $this->db->query($sql2);
                            }
		return $query->row;
	}
	public function getCustomerByEmailCall($mobile) 
	{
		$query = $this->db->query('select',DB_PREFIX . "customer",'','','',array('telephone'=>$mobile));//"SELECT DISTINCT  customer_id FROM " . DB_PREFIX . "customer WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
		if($query->num_rows>0)
		{
            return $query->row["customer_id"];
		}
		
	}
	public function getCustomers($data = array()) {
		
                $where=array();
		
                
		if (!empty($data['filter_name'])) 
                {
                    $search_string=$data['filter_name'];
                    $where['firstname']=new MongoRegex("/.*$search_string/i");
		}

		if (!empty($data['filter_email'])) 
                {
                    $search_string2=$data['filter_email'];
                    $where['email']=new MongoRegex("/.*$search_string2/i");
		
		}
                if (!empty($data['filter_telephone'])) 
                {
                   $search_string3=$data['filter_telephone'];
                    $where['telephone']=new MongoRegex("/.*$search_string3/i");
		
		}

		if (!empty($data['filter_customer_group_id'])) 
                {
                    $where['customer_group_id']=  (int)$this->db->escape($data['filter_customer_group_id']) ;
		}

		if (!empty($data['filter_ip'])) 
                {
                    $where['ip']=($data['filter_ip']);
			//$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
                        
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) 
                {
                    $where['status']=boolval($data['filter_status']);
		}

		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) 
                {
                    $where['approved']=  boolval($this->db->escape($data['filter_approved'])) ;
		}
		
		if (isset($data['filter_store_id']) && !is_null($data['filter_store_id'])) 
                {
                    $where['store_id']=  (int)$this->db->escape($data['filter_store_id']) ;
		}
		
		if (!empty($data['filter_date_added'])) 
                {
                    $where['date_added']= array('$gte'=>new MongoDate( $this->db->escape($data['filter_date_added'])));
		}
		$sort_data = array(
			'customer_id'=>-1 
		);

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
		}

		$query = $this->db->query('select',DB_PREFIX . "customer",'','','',$where,'',(int)$data['limit'],'',(int)$data['start'],$sort_data);

		return $query;
	}

	public function approve($customer_id) {
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET approved = '1' WHERE customer_id = '" . (int)$customer_id . "'");

			$this->load->language('mail/customer');

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);

			if ($store_info) {
				$store_name = $store_info['name'];
				$store_url = $store_info['url'] . 'index.php?route=account/login';
			} else {
				$store_name = $this->config->get('config_name');
				$store_url = HTTP_CATALOG . 'index.php?route=account/login';
			}

			$message  = sprintf($this->language->get('text_approve_welcome'), $store_name) . "\n\n";
			$message .= $this->language->get('text_approve_login') . "\n";
			$message .= $store_url . "\n\n";
			$message .= $this->language->get('text_approve_services') . "\n\n";
			$message .= $this->language->get('text_approve_thanks') . "\n";
			$message .= $store_name;

			$mail = new Mail($this->config->get('config_mail'));
			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($store_name);
			$mail->setSubject(sprintf($this->language->get('text_approve_subject'), $store_name));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}

	public function getAddress($address_id) {
		$address_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");

		if ($address_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");

			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");

			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}

			return array(
				'address_id'     => $address_query->row['address_id'],
				'customer_id'    => $address_query->row['customer_id'],
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
				'zone_id'        => $address_query->row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $address_query->row['country_id'],
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format,
				'custom_field'   => unserialize($address_query->row['custom_field'])
			);
		}
	}

	public function getAddresses($customer_id) {
		$address_data = array();

		$query = $this->db->query("SELECT address_id FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");

		foreach ($query->rows as $result) {
			$address_info = $this->getAddress($result['address_id']);

			if ($address_info) {
				$address_data[$result['address_id']] = $address_info;
			}
		}

		return $address_data;
	}

	public function getTotalCustomers($data = array()) {
               
            $match=array();
            if(!empty($data['filter_customer_group_id']))
            {
                   $match['customer_group_id']=(int)$data['filter_customer_group_id'];
            }
            if(!empty($data['filter_store_id']))
            {
                   $match['store_id']=(int)$data['filter_store_id'];
            }
            $groupbyarray=array(
                 "_id"=> '$status', 
                "count"=> array('$sum'=> 1 ) 
            );
           
            //$query = $this->db->query('gettotalcount','oc_customer',$groupbyarray,$match);
            $query = $this->db->getcount('oc_customer',$match);
            
            return $query;
            
	}

	public function getTotalCustomersAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE status = '0' OR approved = '0'");

		return $query->row['total'];
	}

	public function getTotalAddressesByCustomerId($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalAddressesByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE country_id = '" . (int)$country_id . "'");

		return $query->row['total'];
	}

	public function getTotalAddressesByZoneId($zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE zone_id = '" . (int)$zone_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomersByCustomerGroupId($customer_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE customer_group_id = '" . (int)$customer_group_id . "'");

		return $query->row['total'];
	}

	public function addHistory($customer_id, $comment) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_history SET customer_id = '" . (int)$customer_id . "', comment = '" . $this->db->escape(strip_tags($comment)) . "', date_added = NOW()");
	}

	public function getHistories($customer_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT comment, date_added FROM " . DB_PREFIX . "customer_history WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalHistories($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_history WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function addTransaction($customer_id, $description = '', $amount = '', $order_id = 0) {
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', date_added = NOW()");

			$this->load->language('mail/customer');

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);

			if ($store_info) {
				$store_name = $store_info['name'];
			} else {
				$store_name = $this->config->get('config_name');
			}

			$message  = sprintf($this->language->get('text_transaction_received'), $this->currency->format($amount, $this->config->get('config_currency'))) . "\n\n";
			$message .= sprintf($this->language->get('text_transaction_total'), $this->currency->format($this->getTransactionTotal($customer_id)));

			$mail = new Mail($this->config->get('config_mail'));
			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($store_name);
			$mail->setSubject(sprintf($this->language->get('text_transaction_subject'), $this->config->get('config_name')));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}

	public function deleteTransaction($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getTransactions($customer_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalTransactions($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total  FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTransactionTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalTransactionsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function addReward($customer_id, $description = '', $points = '', $order_id = 0) {
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', points = '" . (int)$points . "', description = '" . $this->db->escape($description) . "', date_added = NOW()");

			$this->load->language('mail/customer');

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);

			if ($store_info) {
				$store_name = $store_info['name'];
			} else {
				$store_name = $this->config->get('config_name');
			}

			$message  = sprintf($this->language->get('text_reward_received'), $points) . "\n\n";
			$message .= sprintf($this->language->get('text_reward_total'), $this->getRewardTotal($customer_id));

			$mail = new Mail($this->config->get('config_mail'));
			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($store_name);
			$mail->setSubject(sprintf($this->language->get('text_reward_subject'), $store_name));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}

	public function deleteReward($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "' AND points > 0");
	}

	public function getRewards($customer_id, $start = 0, $limit = 10) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalRewards($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getRewardTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomerRewardsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getIps($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->rows;
	}

	public function getTotalIps($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomersByIp($ip) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($ip) . "'");

		return $query->row['total'];  
	}

	public function addBanIp($ip) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_ban_ip` SET `ip` = '" . $this->db->escape($ip) . "'");
	}

	public function removeBanIp($ip) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_ban_ip` WHERE `ip` = '" . $this->db->escape($ip) . "'");
	}

	public function getTotalBanIpsByIp($ip) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_ban_ip` WHERE `ip` = '" . $this->db->escape($ip) . "'");

		return $query->row['total'];
	}
	
	public function getTotalLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");

		return $query->row;
	}	

	public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");
	}
        public function update_otp($otp,$customerID,$customermobile,$aadhar)
{
$log=new Log("addcust-".date('Y-m-d').".log");
$sql="update oc_customer set otp='".$otp."',aadhar='".$aadhar."' where customer_id='".$this->db->escape($customerID)."'  ";
$query=$this->db->query($sql);

$log->write($sql); 
}
public function approved_customerotp_update_status($customerID,$otp)
{
$log=new Log("addcust-".date('Y-m-d').".log");
$sql="update oc_customer set approved='1' where customer_id='".$this->db->escape($customerID)."'  ";
$query=$this->db->query($sql);

$log->write($sql);
}

public function verifycustomerotp($customerID,$otp)
{
$log=new Log("addcust-".date('Y-m-d').".log");
$sql="select otp from oc_customer where customer_id='".$this->db->escape($customerID)."' limit 1 ";
$query=$this->db->query($sql);

$log->write($sql);

$db_otp=$query->row['otp'];

$log->write($db_otp);

if($db_otp==$otp)
{
  return "1";
}
else
{
  return "0";
}

}
}
