<?php
class ModelSettingStore extends Model 
{
	public function getStoreUser($store_id) 
    {
        $query = $this->db->query('select',DB_PREFIX .'user','','','',array('store_id'=>(int)$store_id )); 
        return $query->row;
       
    }
	
    public function getCompany($data = array()) 
    {
        $query = $this->db->query('select',DB_PREFIX . "manufacturer",'','','','','','','','',array('name'=>1)); 
        return $query->rows;
       
    }

 public function getFieldActivity($data = array()) 
    {
        $query = $this->db->query('select',DB_PREFIX . "activity_field",'','','','','','','','',array('activityname'=>1)); 
        return $query->rows;
       
    }
    
    public function getUnitsbyStore($store_id) 
    {
        $lookup=array(
                'from' => 'oc_unit',
                'localField' => 'unit_id',
                'foreignField' => 'unit_id',
                'as' => 'oc_unit'
            );
        $match=array('store_id'=>(int)$store_id);
        $start='';
        $limit='';
        $columns=array( 
                    "unit_id"=> 1,
                    "oc_unit.unit_name"=> 1
                );
        $sort_array=array();
        $query = $this->db->query("join",DB_PREFIX . "store_to_unit",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array);
        $return_array= array();
        foreach($query->row as $row)
        {
                $return_array['unit_id']=$row['unit_id'];
                $return_array['name']=strip_tags($row['oc_unit'][0]['unit_name']);
        }
        return $return_array;
    }
    public function get_nearest_store($data) 
    {
        $log=new Log("get_nearest_store-".date('Y-m-d').".log");
        $log->write('get_nearest_store called in model/setting/store'); 

        $sql="select b.*,op.model as product_name,
 111.111 *
    DEGREES(ACOS(COS(RADIANS(Latt))
         * COS(RADIANS(latt_current))
         * COS(RADIANS(Longg - long_currnet))
         + SIN(RADIANS(Latt))
         * SIN(RADIANS(latt_current)))) AS distance_in_km
        
         from (


SELECT
    store_id,
    store_name,
    product_id,
    (CASE
        WHEN Latt = '' THEN '0.0'
        ELSE Latt
    END) AS Latt,
    (CASE
        WHEN Longg = '' THEN '0.0'
        ELSE Longg
    END) AS Longg,
    '".$data['latitude']."' as latt_current,
    '".$data['longitude']."' as long_currnet
FROM
    (SELECT
        os.store_id,
     a.product_id,
            ocs.name AS store_name,
            os.`key`,
            `value`,
            SUBSTR(`value`, 1, INSTR(`value`, '-') - 1) AS Latt,
            SUBSTR(`value`, INSTR(`value`, '-') + 1) AS Longg
    FROM
        oc_setting AS os
        left join
        (select product_id,store_id,quantity from oc_product_to_store where quantity>0)as a
        on a.store_id = os.store_id
    LEFT JOIN oc_store AS ocs ON os.store_id = ocs.store_id
    WHERE
        `key` = 'config_geocode'
            AND ocs.name IS NOT NULL) AS a where product_id in (".$data['product_id']."))as b
            left join oc_product as op on b.product_id=op.product_id  group by store_id
            order by distance_in_km asc limit 10 ";

            $query = $this->db->query($sql);
            $log->write('generated query is :');
            $log->write($sql);  
            $log->write($query->rows); 
            $store_data=array();
            foreach ($query->rows as $storedb)
            {
                         $query2 = $this->db->query("SELECT `value` as config_storestatus FROM oc_setting where store_id='".$storedb['store_id']."' and `key`='config_storestatus' limit 1");
                         $storestatus=$query2->row["config_storestatus"];
                         
                         $query3 = $this->db->query("SELECT ost.type_name as config_storetype FROM oc_setting join oc_store_type as ost on ost.sid=oc_setting.value where store_id='".$storedb['store_id']."' and `key`='config_storetype' limit 1");
                         
                         $sql4="SELECT `value` as config_geocode FROM oc_setting where store_id='".$storedb['store_id']."' and `key`='config_geocode' limit 1";
                         $query4 = $this->db->query($sql4);
                       
                         $sql5="SELECT `value` as config_address FROM oc_setting where store_id='".$storedb['store_id']."' and `key`='config_address' limit 1";
                         $query5 = $this->db->query($sql5);
		if($query4->row["config_geocode"]!="")
		{
                            $store_datan =array(
                                'store_id' => $storedb['store_id'],
                                'name'     => $storedb['store_name'],
                                'product_id'      => $storedb['product_id'],
                                'product_name'      => $storedb['product_name'],
                                'distance' =>$storedb['distance_in_km'],
                                'config_storestatus'=>$storestatus,
                                'config_storetype'=>$query3->row["config_storetype"],
                                'config_geocode'=>$query4->row["config_geocode"],
                                'config_address'=>$query5->row["config_address"]
           
        );
        array_push($store_data,  $store_datan); 
	}
            }

        $log->write($store_data);
        return $store_data;
       
    }

            public function addStore($data) 
            {
				$this->event->trigger('pre.admin.store.add', $data);
                $store_id=$this->db->getNextSequenceValue('oc_store');
                $input_array=array(
                    'store_id'=>(int)$store_id,
                    'name'=>$this->db->escape($data['config_name']),
                    'unit_id'=>$this->db->escape($data['config_unit'][0]),
                    'url'=>$this->db->escape($data['config_url']),
                    'company_id'=>$this->db->escape($data['config_company']),
                    'ssl'=>$this->db->escape($data['config_ssl']),
                    'creditlimit'=>$data['config_creditlimit'],
                    'currentcredit'=>0,
                    'wallet_balance'=>0,
                    'company'=>$this->db->escape($data['config_company_name']),
                    
                    'lastcurrentcredit'=>'',
                    'expense_balance'=>0,
                    'status'=>(int)$this->db->escape($data['config_storestatus']),
                    'store_type_id'=>$this->db->escape($data['config_storetype']),
                    'store_type_name'=>$this->db->escape($data['config_storetype_name'])
                        
                        );
                //'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                $query1 = $this->db->query("insert",DB_PREFIX . "store",$input_array);
		// Layout Route$query = $this->db->query("select",DB_PREFIX . "layout_route",0,'store_id');
		foreach ($query->rows as $layout_route) 
                {
                    $input_array2=array('layout_id'=>(int)$layout_route['layout_id'], 
                                        'route'=>$this->db->escape($layout_route['route']), 
                                        'store_id'=>(int)$store_id);
                    $query2 = $this->db->query("insert",DB_PREFIX . "layout_route",$input_array2);
                }
                foreach($data['config_unit'] as $unit)
                {
                    $input_array3=array('unit_id'=>(int)$unit, 
                                       'store_id'=>(int)$store_id);
                    $query3 = $this->db->query("insert",DB_PREFIX . "store_to_unit",$input_array3);
                }
		$this->cache->delete('store');

		$this->event->trigger('post.admin.store.add', $store_id);
		
		return $store_id;
            }

	public function editStore($store_id, $data) 
        {
            $this->db->query("delete",DB_PREFIX . "store_to_unit",array('store_id'=>(int)$store_id));
            foreach($data['config_unit'] as $unit)
            {
                $input_array3=array('unit_id'=>(int)$unit, 
                                    'store_id'=>(int)$store_id);
                $query3 = $this->db->query("insert",DB_PREFIX . "store_to_unit",$input_array3);

            }
            $this->event->trigger('pre.admin.store.edit', $data);
                $input_array=array(
                    'name'=>$this->db->escape($data['config_name']),
                    'unit_id'=>$this->db->escape($data['config_unit'][0]),
                    'url'=>$this->db->escape($data['config_url']),
                    'company_id'=>(int)$data['config_company'],
                    'ssl'=>$this->db->escape($data['config_ssl']),
                    'creditlimit'=>$data['config_creditlimit'],
                    'company'=>$this->db->escape($data['config_company_name']),
                    'status'=>(int)$this->db->escape($data['config_storestatus']),
                    'store_type_id'=>$this->db->escape($data['config_storetype']),
                    'store_type_name'=>$this->db->escape($data['config_storetype_name'])
                    );
            $query1 = $this->db->query("update",DB_PREFIX . "store",array('store_id'=>(int)$store_id),$input_array);
            $this->cache->delete('store');

            $this->event->trigger('post.admin.store.edit', $store_id);
	}

            public function deleteStore($store_id) 
            {
		$this->event->trigger('pre.admin.store.delete', $store_id);

		$this->db->query("delete" , DB_PREFIX . "store",array('store_id'=>(int)$store_id));
		$this->db->query("delete " ,DB_PREFIX . "layout_route",array('store_id'=>(int)$store_id));

		$this->cache->delete('store');

		$this->event->trigger('post.admin.store.delete', $store_id);
            }

            public function getStore($store_id) 
            {
		$query =$this->db->query('select',DB_PREFIX .'store','','','',array('store_id'=>(int)$store_id ));                         
                        
		return $query->row;
            }


            public function getStoreInv($store_id) 
            {
                $store_data = array(array());
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");


                foreach ($query->rows as $storedb) 
                {
                         $store_datan =array(
			'store_id' => $storedb['store_id'],
			'name'     => $storedb['name'],
			'url'      => $storedb['url']
			
                        );
			//array_push($store_data,  $store_datan); 
                        $store_data=array($store_datan);
                                         							
                }


		return $store_data;
            }

        public function getStores($data = array()) 
        {
            $lookup='';
            $match='store_id';
            $start='';
            $limit='';
            $columns=array('store_id','name','url','status','store_type_id', 'store_type_name');
            $sort_array=array('name'=>1);
            $find_val='';//8;
            $find_column='';//'store_id';
            $search_string=$data['filter_store'];
            $where = array(
                    'name' => new MongoRegex("/.*$search_string/i"),
                  );

            $query = $this->db->query("select",DB_PREFIX . "store",$find_val,$find_column,'',$where,'','',$columns,$start,$sort_array);
            $store_data = array();
            foreach ($query->rows as $storedb) 
            {
                $store_datan =array(
			'store_id' => $storedb['store_id'],
			'name'     => strtoupper($storedb['name']),
			'url'      => $storedb['url'],
                        'config_storestatus'=>$storedb['status'],
                        'config_storetype'=>$storedb['store_type_name']
			
		);
		array_push($store_data,  $store_datan);  
                
            }
            return $store_data;
	}
        ///////////////
        public function getStoresForProducts($data = array()) 
        {
            $start = '';
                        
            $limit = '';
            $lookup='';
            $match='store_id';
            $columns=array('store_id','name','url','status','store_type_id', 'store_type_name');
            $sort_array=array('name'=>-1);
            $find_val='';//8;
            $find_column='';//'store_id';
            $search_string=$data['filter_store'];
            $where = array(
                    'name' => new MongoRegex("/.*$search_string/i"),
                  );

            $query = $this->db->query("select",DB_PREFIX . "store",$find_val,$find_column,'',$where,'','',$columns,$data['start'],$sort_array);
            
            $store_data[] = array(
                        'totalrows' => $query->num_rows,
			'store_id' => 0,
			'name'     => 'Default',
			'url'      => '',
                        'config_storestatus'=>1,
                        'config_storetype'=>''
			
		);
            foreach ($query->rows as $storedb) 
            {  
                $store_datan =array(
                        'totalrows' => $query->num_rows,
			'store_id' => $storedb['store_id'],
			'name'     => $storedb['name'],
			'url'      => $storedb['url'],
                        'config_storestatus'=>$storedb['status'],
                        'config_storetype'=>$storedb['store_type_name']
			
		);
		array_push($store_data,  $store_datan);  
                
            }
            return $store_data;
        }
        ///////////////////////////////////////////////////
        
        //////////////////////////////////////////////////
        public function getOwnStores($data = array()) 
        { 
            $start = '';
                        
            $limit = '';
            $lookup='';
            $match='store_id';
            $columns=array('store_id','name','url','status','store_type_id', 'store_type_name');
            $sort_array=array('name'=>-1);
            $find_val='';//8;
            $find_column='';//'store_id';
            $search_string=$data['filter_store'];
            $where = array(
                    'name' => new MongoRegex("/.*$search_string/i"),
                  );
            $query = $this->db->query("select",DB_PREFIX . "store",$find_val,$find_column,'',$where,'','20',$columns,$data['start'],$sort_array);
            $store_data = array();
            
           
            foreach ($query->rows as $storedb) 
            {
                if(($storedb['status']!="0") && (($storedb['store_type_id']=='1') || ($storedb['store_type_id']=='2')))
		{
                $store_datan =array(
			'store_id' => $storedb['store_id'],
			'name'     => $storedb['name'],
			'url'      => $storedb['url'],
                        'config_storestatus'=>$storedb['status'],
                        'config_storetype'=>$storedb['store_type_name']
			
		);
		array_push($store_data,  $store_datan);  
                }
                
            }
            return $store_data;
    
	}
        //////////////////////////////////////////////////

        public function getWarehouses($data = array()) 
        { 
            $start = '';
                        
            $limit = '';
            $lookup='';
            $match='store_id';
            $columns=array('store_id','name','url','status','store_type_id', 'store_type_name');
            $sort_array=array('name'=>-1);
            $find_val='';
            $find_column='';
            $search_string=$data['filter_store'];
            $where = array(
                    'name' => new MongoRegex("/.*$search_string/i"),
                  );
            $query = $this->db->query("select",DB_PREFIX . "store",$find_val,$find_column,'',$where,'','20',$columns,$data['start'],$sort_array);
            $store_data = array();
            
           
            foreach ($query->rows as $storedb) 
            {
                if(($storedb['status']!="0") && ($storedb['store_type_id']=='2'))
		{
                $store_datan =array(
			'store_id' => $storedb['store_id'],
			'name'     => $storedb['name'],
			'url'      => $storedb['url'],
                        'config_storestatus'=>$storedb['status'],
                        'config_storetype'=>$storedb['store_type_name']
			
		);
		array_push($store_data,  $store_datan);  
                }
                
            }
            return $store_data;
	}
        public function getFranchiseStores($data = array()) 
        {
            $start = '';
            $limit = '';
            $lookup='';
            $match='store_id';
            $columns=array('store_id','name','url','status','store_type_id', 'store_type_name');
            $sort_array=array('name'=>-1);
            $find_val='';//8;
            $find_column='';//'store_id';
            $search_string=$data['filter_store'];
            $where = array(
                    'name' => new MongoRegex("/.*$search_string/i"),
                  );
            $query = $this->db->query("select",DB_PREFIX . "store",$find_val,$find_column,'',$where,'','20',$columns,$data['start'],$sort_array);
            $store_data = array();
            
            foreach ($query->rows as $storedb) 
            {
                if(($storedb['status']!="0") && (($storedb['store_type_id']=='3') || ($storedb['store_type_id']=='4')))
		{
                $store_datan =array(
			'store_id' => $storedb['store_id'],
			'name'     => $storedb['name'],
			'url'      => $storedb['url'],
                        'config_storestatus'=>$storedb['status'],
                        'config_storetype'=>$storedb['store_type_name']
			
		);
		array_push($store_data,  $store_datan);  
                }
                
            }
            return $store_data;
	}
        //company wise
        public function getStoresCompanyWise($company_id) 
        { 
            $lookup='';
            $match='store_id';
            $columns=array('store_id','name','url','status','store_type_id', 'store_type_name');
            $sort_array=array('name'=>-1);
            $find_val=$company_id;
            $find_column='company_id';
            $search_string=$data['filter_store'];
            $where='';
            $query = $this->db->query("select",DB_PREFIX . "store",$find_val,$find_column,'',$where,'',$limit,$columns,$start,$sort_array);
            $store_data = array();
            foreach ($query->rows as $storedb) 
            {
                $store_datan =array(
			'store_id' => $storedb['store_id'],
			'name'     => $storedb['name'],
			'url'      => $storedb['url'],
                        'config_storestatus'=>$storedb['status'],
                        'config_storetype'=>$storedb['store_type_name']
			
		);
		array_push($store_data,  $store_datan);  
                
            }
            return $store_data;
                
	}
        //////////////////////////////////////////////////////////////////

        public function getStoresWeb($data = array()) 
        {
                if (isset($data['start']) || isset($data['limit'])) 
                {
			if ($data['start'] < 0) 
                        {
				$start = 0;
			}
                        else
                        {
                            $start = $data['start'];
                        }
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
                        else
                        {
                            $limit = $data['limit'];
                        }

			
		}   
            $lookup='';
            $match='store_id';
            $columns=array('store_id','name','url','status','store_type_id', 'store_type_name');
            $sort_array=array('name'=>1);
            $find_val='';
            $find_column='';
            $search_string=$data['filter_store'];
            $where = array(
                    'name' => new MongoRegex("/.*$search_string/i"),
                  );

            $query = $this->db->query("select",DB_PREFIX . "store",$find_val,$find_column,'',$where,'',$limit,$columns,$start,$sort_array);
            
            $store_data = array();
            foreach ($query->rows as $storedb) 
            {
				//config_telephone
				$query22 = $this->db->query("select",DB_PREFIX . "setting",'','','',array('store_id'=>(int)$storedb['store_id'],'key'=>'config_telephone'),'','','','','');
                $config_telephone=$query22->row['value'];
				$query223 = $this->db->query("select",DB_PREFIX . "setting",'','','',array('store_id'=>(int)$storedb['store_id'],'key'=>'config_storetype_name'),'','','','','');
                $config_storetype_name=$query223->row['value'];
				//
				$store_datan =array(
                        'totalrows'=>$query->num_rows,
			'store_id' => $storedb['store_id'],
			'name'     => $storedb['name'],
			'url'      => $storedb['url'],
                        'config_storestatus'=>$storedb['status'],
                        'config_storetype'=>$config_storetype_name,
						'config_telephone'=>$config_telephone
			
		);
		array_push($store_data,  $store_datan);  
                
            }
            return $store_data;
	}

	public function getTotalStores($data=array()) 
        {
                if($data['filter_store']!="")
		{
                    $search_string=$this->db->escape($data['filter_store']);
                    $match=array('name'=> new MongoRegex("/.*$search_string/i"));
                }
                $groupbyarray=array(
                 "_id"=> array('$store_id'), 
                "total"=> array('$sum'=> 1 ) 
                );
                $query = $this->db->query('gettotalcount','oc_store',$groupbyarray,$match);
                return $query->row[0]['total']; 
                
	}

	public function getTotalStoresByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_layout_id' AND `value` = '" . (int)$layout_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByLanguage($language) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_language' AND `value` = '" . $this->db->escape($language) . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByCurrency($currency) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_currency' AND `value` = '" . $this->db->escape($currency) . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_country_id' AND `value` = '" . (int)$country_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByZoneId($zone_id) 
        {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_zone_id' AND `value` = '" . (int)$zone_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByCustomerGroupId($customer_group_id) 
        {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_customer_group_id' AND `value` = '" . (int)$customer_group_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByInformationId($information_id) 
        {
		$account_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_account_id' AND `value` = '" . (int)$information_id . "' AND store_id != '0'");

		$checkout_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_checkout_id' AND `value` = '" . (int)$information_id . "' AND store_id != '0'");

		return ($account_query->row['total'] + $checkout_query->row['total']);
	}

	public function getTotalStoresByOrderStatusId($order_status_id) 
        {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_order_status_id' AND `value` = '" . (int)$order_status_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	//transport store
	public function getTransport($data = array()) {

            //$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "transport ORDER BY name");
            $where=array('store_id'=>(int)$sid);
            $query = $this->db->query('select',DB_PREFIX . 'transport','','','',$where,'','','','',array('name'=>1));                                             							                        				
		
            return $query->rows;
	}

	public function getCircles($store_id) 
        {
		                            $log=new Log("custcircle-".date('Y-m-d').".log");
		$sql="SELECT * FROM oc_contractor where `store_id`='".$store_id."' ORDER BY circle_code  ";
		$log->write($sql);
	      $query = $this->db->query("SELECT * FROM oc_contractor where `store_id`='".$store_id."' ORDER BY circle_code  ");
              return $query->rows; 
		
	}
	public function setCash( $store_name,$store_id,$user_id,$amount,$mobile,$name,$update_date)
        {

		$log=new Log("setcash-".date('Y-m-d').".lpg");
              $sql="insert into oc_cash_store_position_trans (`store_name`,`store_id`,`user_id`,`amount`,`mobile`,`name`) "
                      . "values "
                      . "('".$store_name."','".$store_id."','".$user_id."','".$amount."','".$mobile."','".$name."')"; 
              $query = $this->db->query($sql);
              
              $sql="insert into oc_cash_store_position (`store_name`,`store_id`,`user_id`,`amount`,`mobile`,`name`,`update_date`) " 
                      . "values "
                      . "('".$store_name."','".$store_id."','".$user_id."','".$amount."','".$mobile."','".$name."','".$update_date."') ON DUPLICATE KEY UPDATE amount='".$amount."',`update_date`='".$update_date."' ,ucode=(FLOOR( 1 + RAND( ) *60 )) ";
		$log->write($sql);
	             $query = $this->db->query($sql);

              return $query; 
		
	}
	public function getcashtrans($sid) 
        {
		//$query = $this->db->query("SELECT name,store_name,amount,DATE(update_date) as update_date FROM  `oc_cash_store_position_trans`   WHERE store_id='".$sid."' order by SID  desc limit 15");
		$where=array('store_id'=>(int)$sid);
                $query = $this->db->query('select',DB_PREFIX . 'cash_store_position_trans','','','',$where,'',15,'',0,array('SID'=>-1));                                             							                        				
		
                return $query->rows;
	}
	public function getcashpostion($sid) {
		//$query = $this->db->query("SELECT amount FROM  `oc_cash_store_position`   WHERE store_id='".$sid."'  limit 1");
		$where=array('store_id'=>(int)$sid);
                $query = $this->db->query('select',DB_PREFIX . 'cash_store_position','','','',$where,'','','','',array());                                             							                        				
		
                return $query->row["amount"];
	}


	public function updatecurrentcash($circle,$amount,$sid) 
        {
            $where=array('store_id'=>(int)$sid,'circle_code'=>(int)$circle);
            $input_array=array('currentcredit'=>('currentcredit'-(int)$amount));
            $query = $this->db->query("update",DB_PREFIX . "contractor",$where,$input_array);
			
	}	




	public function getProduct($product_id,$contractor_id,$sid) 
        {
                $where=array('store_id'=>(int)$sid,
                    'product_id'=>(int)$product_id,
                    'contractor_id'=>(int)$contractor_id);
                $query = $this->db->query('select',DB_PREFIX . 'oc_contractor_product','','','',$where,'','','','',array());                                             							                        				
		
		return $query->row;
	}
//news
	public function getNewsByID($id) 
        {
            //$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "news where NewsItemID='".$id."'");
            $query = $this->db->query('select',DB_PREFIX . 'news',(int)$id,'NewsItemID','','','','','','',array());                                             							                        				
            return $query->rows;
	}

	public function getNews($data = array()) 
        {
            //$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "news ORDER BY DatePublished desc");
            $query = $this->db->query('select',DB_PREFIX . 'news','','','','','','','','',array('DatePublished'=>-1));
            return $query->rows;
	}


	public function getNewsLatest($data = array()) 
	{
            //$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "news ORDER BY DatePublished desc Limit 4");                                           							                        		
            $query = $this->db->query('select',DB_PREFIX . 'news','','','','','',4,'',0,array('DatePublished'=>-1));
            return $query->rows;
	}
        public function getStorelocation() 
        { 
            $sql=" SELECT `oc_setting`.`value` as store_geo,`oc_store`.`store_id` as store_id,`oc_store`.`name` as store_name,
(select oc_setting.value from oc_setting where oc_setting.store_id=oc_store.store_id and oc_setting.key='config_address' limit 1) as store_address FROM `oc_setting` join `oc_store` on `oc_store`.`store_id`=`oc_setting`.`store_id` WHERE `oc_setting`.`key`='config_geocode'  ";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getstoretypes() 
        {

		//$query = $this->db->query("SELECT DISTINCT * FROM oc_store_type where `status`='1' ");
                $query = $this->db->query('select',DB_PREFIX . 'store_type',1,'status','','','','','','',array());
		return $query->rows;
	}
        public function getbanks() 
        {

		//$query = $this->db->query("SELECT DISTINCT * FROM oc_bank_list  where `status`='1'  order by bank_name asc "); 
                $query = $this->db->query('select',DB_PREFIX . 'bank_list',1,'status','','','','','','',array('bank_name'=>1));
                
		return $query->rows;
	}
	public function getUnits() {

		//$query = $this->db->query("SELECT * FROM `oc_unit`   order by unit_name asc "); 
                $query = $this->db->query('select',DB_PREFIX . 'unit','','','','','','','','',array('unit_name'=>1));
                
		return $query->rows;
	}
	public function getUnitbystore($store_id) {

		//$query = $this->db->query("SELECT unit_id FROM `oc_store_to_unit`  where store_id='".$store_id."' "); 
                $query = $this->db->query('select',DB_PREFIX . 'store_to_unit',(int)$store_id,'store_id','','','','','','',array());
                
		return $query->rows;
	}
	public function getCompanybystore($store_id) {

		//$query = $this->db->query("SELECT oc_store.company_id as company_id  FROM `oc_store`   where oc_store.store_id='".$store_id."' ");  
                $query = $this->db->query('select',DB_PREFIX . 'store',(int)$store_id,'store_id','','','','','','',array());
                
		return $query->row['company_id'];
	}
	public function getstoretype($storetype) 
        {
            //$log=new Log("category-".date('Y-m-d').".log");
            //$sql="select type_name from oc_store_type where `sid`='".$storetype."' limit 1 ";
            //$log->write($sql);
            //$query=$this->db->query($sql);
            //$log->write($query->row["type_name"]);
            $query = $this->db->query('select',DB_PREFIX . 'store_type',(int)$storetype,'sid','','','','','','',array());
                
            return $query->row["type_name"];
		
	}
	public function getWaiveoffdata($data)
{
$sql='select we.from_date,we.to_date,we.response,DATE(we.cr_date) as cr_date,ou.firstname,ou.lastname,os.name,we.cash,we.store_id,(select concat(firstname," ",lastname) as store_user from oc_user where store_id=we.store_id and  user_group_id="11" limit 1) as store_user from oc_waive_exp as we
left join oc_store as os on os.store_id=we.store_id
left join oc_user as ou on ou.user_id=we.user_id';
if (!empty($data['filter_stores_id'])) {
$sql .= " WHERE we.store_id= '" . (int)$data['filter_stores_id'] . "'";
} else {
$sql .= " WHERE we.store_id > '0'";
}
if (!empty($data['filter_date_start'])) {
$sql .= " AND DATE(we.from_date) >= '" . $this->db->escape($data['filter_date_start']) . "'";
}

if (!empty($data['filter_date_end'])) {
$sql .= " AND DATE(we.to_date) <= '" . $this->db->escape($data['filter_date_end']) . "'";
}
$sql .= " AND `type`= '0' ";
$sql.=" order by id desc ";

if (isset($data['start']) || isset($data['limit'])) {
if ($data['start'] < 0) {
$data['start'] = 0;
}

if ($data['limit'] < 1) {
$data['limit'] = 20;
}

$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
}
//echo $sql;
$query= $this->db->query($sql);
return $query->rows;
}
public function getTotalWaiveoffdata($data)
{
$sql='select count(*) as total from (select we.from_date,we.to_date,we.response,we.cr_date,ou.firstname,ou.lastname,os.name as storename from oc_waive_exp as we
left join oc_store as os on os.store_id=we.store_id
left join oc_user as ou on ou.user_id=we.user_id';
if (!empty($data['filter_stores_id'])) {
$sql .= " WHERE we.store_id= '" . (int)$data['filter_stores_id'] . "'";
} else {
$sql .= " WHERE we.store_id > '0'";
}
if (!empty($data['filter_date_start'])) {
$sql .= " AND DATE(we.from_date) >= '" . $this->db->escape($data['filter_date_start']) . "'";
}

if (!empty($data['filter_date_end'])) {
$sql .= " AND DATE(we.to_date) <= '" . $this->db->escape($data['filter_date_end']) . "'";
}
$sql .= " AND `type`= '0' ";

$sql .= ") as aa";
//echo $sql;
$query= $this->db->query($sql);
return $query->row['total'];
}



public function Waiver_Report($data)
{
$sql='select we.from_date,we.to_date,we.response,DATE(we.cr_date) as cr_date,ou.firstname,ou.lastname,os.name,we.cash,we.store_id,we.document_no,(select concat(firstname," ",lastname) as store_user from oc_user where store_id=we.store_id and  user_group_id="11"  and oc_user.user_id=we.store_user_id) as store_user from oc_waive_exp as we
left join oc_store as os on os.store_id=we.store_id
left join oc_user as ou on ou.user_id=we.user_id';
if (!empty($data['filter_stores_id'])) {
$sql .= " WHERE we.store_id= '" . (int)$data['filter_stores_id'] . "'";
} else {
$sql .= " WHERE we.store_id > '0'";
}
if (!empty($data['filter_date_start'])) {
$sql .= " AND DATE(we.cr_date) >= '" . $this->db->escape($data['filter_date_start']) . "'";
}

if (!empty($data['filter_date_end'])) {
$sql .= " AND DATE(we.cr_date) <= '" . $this->db->escape($data['filter_date_end']) . "'";
}
$sql .= " AND `type`= '1' ";
$sql.=" order by id desc ";

if (isset($data['start']) || isset($data['limit'])) {
if ($data['start'] < 0) {
$data['start'] = 0;
}

if ($data['limit'] < 1) {
$data['limit'] = 20;
}

$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
}
//echo $sql;
$query= $this->db->query($sql);
return $query->rows;
}
public function getTotalWaiver_Report($data)
{
$sql='select count(*) as total from ( select we.from_date,we.to_date,we.response,DATE(we.cr_date) as cr_date,ou.firstname,ou.lastname,os.name,we.cash,we.store_id,we.document_no,(select concat(firstname," ",lastname) as store_user from oc_user where store_id=we.store_id and  user_group_id="11"  and oc_user.user_id=we.store_user_id) as store_user from oc_waive_exp as we
left join oc_store as os on os.store_id=we.store_id
left join oc_user as ou on ou.user_id=we.user_id';
if (!empty($data['filter_stores_id'])) {
$sql .= " WHERE we.store_id= '" . (int)$data['filter_stores_id'] . "'";
} else {
$sql .= " WHERE we.store_id > '0'";
}
if (!empty($data['filter_date_start'])) {
$sql .= " AND DATE(we.cr_date) >= '" . $this->db->escape($data['filter_date_start']) . "'";
}

if (!empty($data['filter_date_end'])) {
$sql .= " AND DATE(we.cr_date) <= '" . $this->db->escape($data['filter_date_end']) . "'";
}
$sql .= " AND `type`= '1' ";


$sql .= ") as aa";
//echo $sql;
$query= $this->db->query($sql);
return $query->row['total'];
}



public function getWaiveoffdata_companywise($data)
{
$sql='select we.from_date,we.to_date,we.response,DATE(we.cr_date) as cr_date,ou.firstname,ou.lastname,os.name,we.cash,we.store_id,(select concat(firstname," ",lastname) as store_user from oc_user where store_id=we.store_id and  user_group_id="11" limit 1) as store_user from oc_waive_exp as we
left join oc_store as os on os.store_id=we.store_id
left join oc_user as ou on ou.user_id=we.user_id';
if (!empty($data['filter_stores_id'])) {
$sql .= " WHERE we.store_id= '" . (int)$data['filter_stores_id'] . "'";
} else {
$sql .= " WHERE we.store_id > '0'";
}
if (!empty($data['filter_date_start'])) {
$sql .= " AND DATE(we.from_date) >= '" . $this->db->escape($data['filter_date_start']) . "'";
}

if (!empty($data['filter_date_end'])) {
$sql .= " AND DATE(we.to_date) <= '" . $this->db->escape($data['filter_date_end']) . "'";
}
$sql .=" and os.company_id='".$data['filter_company']."' ";
//$sql .= " AND `type`= '0' ";
$sql.=" order by id desc ";

if (isset($data['start']) || isset($data['limit'])) {
if ($data['start'] < 0) {
$data['start'] = 0;
}

if ($data['limit'] < 1) {
$data['limit'] = 20;
}

$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
}
//echo $sql;
$query= $this->db->query($sql);
return $query->rows;
}
public function getTotalWaiveoffdata_companywise($data)
{
$sql='select count(*) as total from (select we.from_date,we.to_date,we.response,we.cr_date,ou.firstname,ou.lastname,os.name as storename from oc_waive_exp as we
left join oc_store as os on os.store_id=we.store_id
left join oc_user as ou on ou.user_id=we.user_id';
if (!empty($data['filter_stores_id'])) {
$sql .= " WHERE we.store_id= '" . (int)$data['filter_stores_id'] . "'";
} else {
$sql .= " WHERE we.store_id > '0'";
}
if (!empty($data['filter_date_start'])) {
$sql .= " AND DATE(we.from_date) >= '" . $this->db->escape($data['filter_date_start']) . "'";
}

if (!empty($data['filter_date_end'])) {
$sql .= " AND DATE(we.to_date) <= '" . $this->db->escape($data['filter_date_end']) . "'";
}
$sql .=" and os.company_id='".$data['filter_company']."' ";
//$sql .= " AND `type`= '0' ";

$sql .= ") as aa";
//echo $sql;
$query= $this->db->query($sql);
return $query->row['total'];
}



public function Waiver_Report_companywise($data)
{
$sql='select we.from_date,we.to_date,we.response,DATE(we.cr_date) as cr_date,ou.firstname,ou.lastname,os.name,we.cash,we.store_id,we.document_no,(select concat(firstname," ",lastname) as store_user from oc_user where store_id=we.store_id and  user_group_id="11"  and oc_user.user_id=we.store_user_id) as store_user from oc_waive_exp as we
left join oc_store as os on os.store_id=we.store_id
left join oc_user as ou on ou.user_id=we.user_id';
if (!empty($data['filter_stores_id'])) {
$sql .= " WHERE we.store_id= '" . (int)$data['filter_stores_id'] . "'";
} else {
$sql .= " WHERE we.store_id > '0'";
}
if (!empty($data['filter_date_start'])) {
$sql .= " AND DATE(we.cr_date) >= '" . $this->db->escape($data['filter_date_start']) . "'";
}

if (!empty($data['filter_date_end'])) {
$sql .= " AND DATE(we.cr_date) <= '" . $this->db->escape($data['filter_date_end']) . "'";
}

$sql .=" and os.company_id='".$data['filter_company']."' ";
//$sql .= " AND `type`= '1' ";
$sql.=" order by id desc ";

if (isset($data['start']) || isset($data['limit'])) {
if ($data['start'] < 0) {
$data['start'] = 0;
}

if ($data['limit'] < 1) {
$data['limit'] = 20;
}

$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
}
//echo $sql;
$query= $this->db->query($sql);
return $query->rows;
}
public function getTotalWaiver_Report_companywise($data)
{
$sql='select count(*) as total from ( select we.from_date,we.to_date,we.response,DATE(we.cr_date) as cr_date,ou.firstname,ou.lastname,os.name,we.cash,we.store_id,we.document_no,(select concat(firstname," ",lastname) as store_user from oc_user where store_id=we.store_id and  user_group_id="11"  and oc_user.user_id=we.store_user_id) as store_user from oc_waive_exp as we
left join oc_store as os on os.store_id=we.store_id
left join oc_user as ou on ou.user_id=we.user_id';
if (!empty($data['filter_stores_id'])) {
$sql .= " WHERE we.store_id= '" . (int)$data['filter_stores_id'] . "'";
} else {
$sql .= " WHERE we.store_id > '0'";
}
if (!empty($data['filter_date_start'])) {
$sql .= " AND DATE(we.cr_date) >= '" . $this->db->escape($data['filter_date_start']) . "'";
}

if (!empty($data['filter_date_end'])) {
$sql .= " AND DATE(we.cr_date) <= '" . $this->db->escape($data['filter_date_end']) . "'";
}
//$sql .= " AND `type`= '1' ";

$sql .=" and os.company_id='".$data['filter_company']."' ";
$sql .= ") as aa";
//echo $sql;
$query= $this->db->query($sql);
return $query->row['total'];
}


public function getSubUser() {
		$sql="SELECT user_id,concat(firstname,' ',lastname) as name from oc_user  where user_group_id='36' ";
           
	              //echo $sql;
		$query = $this->db->query($sql);
                
		return $query->rows;
	}
	public function getpaidcredit($user_id,$store_id=0) 
	{
		$where=array('customer_id'=>(int)$user_id);
		if(!empty($store_id))
		{
			$where['store_id']=(int)$store_id;
		}
		$log=new Log("paidcreditrans-".date('Y-m-d').".log");
		$log->write('in model for getpaidcredit');
		$log->write($where);
		$query = $this->db->query('select','oc_customer_to_store_trans','','','',$where);
		return $query;
	}

}