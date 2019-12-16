<?php
class Modelcustomertranstransation extends Model {
	
	
	public function getcustomerreward($data = array()) {
               $log=new Log("custotrans-".date('Y-m-d').".log");
               $where=array();
               if (!empty($data['filter_date_start'])) {
			
                     $sdate=$this->db->escape($data['filter_date_start']) ;
		}

		if (!empty($data['filter_date_end'])) {
			
                       $edate =$this->db->escape($data['filter_date_end']) ;
		}
            
                $datedata=array();
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
                $where['date_added']=$datedata;
                   if ((!empty($data['filter_store'])) && $data['filter_store']!=0)
				   {
			
                         $where['store_id']= (int)$data['filter_store'] ; 
		} 
                
                $sort_data = array(
			'order_id'=>-1						
		);
                
                if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			
		}
        
		$query = $this->db->query('select',DB_PREFIX . "customer_reward",'','','',$where,'',(int)$data['limit'],'',(int)$data['start'],$sort_data);
                //print_r($where);
                // exit;
		//return $query->rows;
                return $query;
	}

	public function getcustomerstore($data = array()) {
            
             $log=new Log("custotrans-".date('Y-m-d').".log");
               $where=array();
             /*  if (!empty($data['filter_date_start'])) {
			
                     $sdate=$this->db->escape($data['filter_date_start']) ;
		}

		if (!empty($data['filter_date_end'])) {
			
                       $edate =$this->db->escape($data['filter_date_end']) ;
		}
            
                $datedata=array();
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
                $where['date_added']=$datedata;*/
                   if ((!empty($data['filter_store'])) && $data['filter_store']!=0)
				   {
			
                         $where['store_id']= (int)$data['filter_store'] ; 
		} 
                
               
                
                if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
                
                $log->write($sql);
		$query = $this->db->query('select',DB_PREFIX . "customer_to_store",'','','',$where,'',(int)$data['limit'],'',(int)$data['start']);
               // print_r($query->rows);
                //exit;
		//return $query->rows;
                return $query;

                
               
	}
	//////////////////////////////////////////////////
public function getcustomertranstype($data = array()) {
    
      $log=new Log("custotrans-".date('Y-m-d').".log");
               $where=array();
               if (!empty($data['filter_date_start'])) {
			
                     $sdate=$this->db->escape($data['filter_date_start']) ;
		}

		if (!empty($data['filter_date_end'])) {
			
                       $edate =$this->db->escape($data['filter_date_end']) ;
		}
            
                $datedata=array();
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
                $where['create_time']=$datedata;
                   if ((!empty($data['filter_store'])) && $data['filter_store']!=0){
			
                         $where['store_id']= (int)$data['filter_store'] ; 
		} 
                
                $sort_data = array(
			'order_id'=>-1						
		);
                
                if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
                
                $log->write($sql);
		$query = $this->db->query('select',DB_PREFIX . "customer_to_store_trans",'','','',$where,'',(int)$data['limit'],'',(int)$data['start'],$sort_data);
                // print_r($query->rows);
                // exit;
		//return $query->rows;
                return $query;
    
	
	} 

	public function getproductrewardstrans($data = array()) {
            
            $log=new Log("custotrans-".date('Y-m-d').".log");
               $where=array();
               if (!empty($data['filter_date_start'])) {
			
                     $sdate=$this->db->escape($data['filter_date_start']) ;
		}

		if (!empty($data['filter_date_end'])) {
			
                       $edate =$this->db->escape($data['filter_date_end']) ;
		}
            
                $datedata=array();
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
                $where['start_date']=$datedata;
                   if ((!empty($data['filter_store'])) && $data['filter_store']!=0) {
			
                         $where['store_id']= (int)$data['filter_store'] ; 
		} 
                
                $sort_data = array(
			'order_id'=>-1						
		);
                
                if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
                
                $log->write($sql);
		$query = $this->db->query('select',DB_PREFIX . "product_reward_trans_history",'','','',$where,'',(int)$data['limit'],'',(int)$data['start'],$sort_data);
                return $query;
            
               
	}


   
}