<?php
class ModelReportPromotionalActivity extends Model 
{
	
	public function getcountforchart($data=array()) 
	{
		//print_r($data);
			$where=array();
			$activity_match=array('isactive'=>true);
		if(!empty($data['filter_name']))
        {
            $where['activity_id']= (int)$data['filter_name'] ; 
			$activity_match['activityid']= (int)$data['filter_name'] ;
        }
		if(!empty($data['filter_company']))
        {
            $where['company_id']= (int)$data['filter_company'] ; 
        }
		
		
		
		if(!empty($data['filter_mobile']))
        {
            $where['representative_mobile']= $data['filter_mobile'] ; 
        }
		if (!empty($data['filter_date_start']))
        {
                        $sdate=$data['filter_date_start'];
        }

		if (!empty($data['filter_date_end'])) 
		{
            $edate=$data['filter_date_end'];
                        
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
            if(!empty($datedata))
			{
                $where['date']=$datedata;
            }
			if(!empty($year))
			{
				$match['date']=array(
                   '$gte'=>new MongoDate(strtotime($year.'-01-01')),
                   '$lte'=>new MongoDate(strtotime($year.'-12-31'))
                   );
			}
			else
			{
            $match['date']=array(
                   '$gte'=>new MongoDate(strtotime(date('Y').'-01-01')),
                   '$lte'=>new MongoDate(strtotime(date('Y').'-12-31'))
                   );
			} 
			
			
			
			$query1=$this->db->query('select','oc_activity_field','','','',$activity_match,'','','',0,array('activityid'=>1),'','');
			
			foreach($query1->rows as $row)
			{
				$total=0;
				$where['activity_id']=(int)$row['activityid'];
				//print_r(json_encode($activity_match));
				$query = $this->db->query("select","oc_promational_activity",'','','',$where,'','','',(int)0,$sort);
				$total=count($query->rows);
				$order_data[$row['activityid']] = array(
				'activityid'=>$row['activityid'],
				'activityname'=>$row['activityname'],
				'total'=>$total,
				);
				
				
			}
			return $order_data;
            //return $query=$this->db->query('join','oc_promational_activity','',$unwind,$match,'','','','',0,$sort,'',$group);
			//print_r($query);
			exit;
			
            $groupbyarray=array(
                 "_id"=> array('$month'=> '$date_added'), 
                "totalorder"=> array('$sum'=> 1 ),
                "total"=> array('$sum'=> '$total' ) 
            );
            $sort_array=array('_id'=>1);
            //$query = $this->db->query('gettotalcount','oc_order',$groupbyarray,$match,$sort_array);
               
            $order_data = array();
		for ($i = 1; $i <= 12; $i++) 
		{
			$order_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i,1)),
				'totalorder' => 0,
                            'total' => 0
			);
		}
		
		foreach ($query->row as $result) {
			$order_data[$result['_id']] = array(
				'month' => date('M', mktime(0, 0, 0, $result['_id'],1)),
				'totalorder' => $result['totalorder'],
                                'total' => $result['total']
			);
		}		
		return $order_data;
	}
	public function getpromotionalactivity($data = array()) 
	{ 
		//print_r($data);
		$where=array();
		if(!empty($data['filter_name']))
        {
            $where['activity_id']= (int)$data['filter_name'] ; 
        }
		if(!empty($data['filter_mobile']))
        {
            $where['representative_mobile']= $data['filter_mobile'] ; 
        }
		
		if(!empty($data['filter_company']))
        {
            $where['company_id']= (int)$data['filter_company'] ; 
        }
		
     if (!empty($data['filter_date_start']))
        {
                        $sdate=$data['filter_date_start'];
        }

		if (!empty($data['filter_date_end'])) 
		{
            $edate=$data['filter_date_end'];
                        
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
            if(!empty($datedata))
			{
                $where['date']=$datedata;
            }
           // $where['order_status_id']=5;
		    
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
			
			
		//$sort=array("date_added"=>-1);
        $query = $this->db->query("select","oc_promational_activity",'','','',$where,'',(int)$data['limit'],'',(int)$data['start'],$sort);
		//print_r($query->rows);
		//exit;
		return $query;
	}

	public function getActivity() 
	{
		    $sort=array('activityname'=>1);
             $query = $this->db->query('select','oc_activity_field','','','','','','','','',$sort);
            //print_r( $query->rows);
            return $query->rows;
	}
	
		public function getCompany() 
	{
		  $sort=array('name'=>1);
             $query = $this->db->query('select','oc_manufacturer','','','','','','','','',$sort);
            //print_r( $query->rows);
            return $query->rows;
	}
	
	
	public function getTotalOrders($data = array()) 
	{
		$match['order_status_id']=5;
        $groupbyarray=array(
                 "_id"=>$order_id ,
                "total"=> array('$sum'=> 1 )
            );
             $query = $this->db->query('gettotalcount','oc_order',$groupbyarray,$match);
            //print_r( $query->row);
            return $query->row[0];
	}
        

        public function exgetOrders($data = array()) 
        {   
            $log=new Log("product_sales_report_gstr1-".date('Y-m-d').".log");
            
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
           
                $match=array('store_id'=>(int)$data['filter_store'],
                    "order_total.code"=>'tax',
                    'order_status_id'=>5,
                     'date_added'=>$datedata
                    ); 
				
				$unwind=array('$order_total','$order_total');
				$group[]=array(
                    "_id" =>('$order_total.title'),
                    "total" =>array('$sum'=>'$order_total.value')
					);
                $log->write('match');
                $log->write(json_encode($match));
                $log->write('group');
                $log->write(json_encode($group));
                $query=$this->db->query('join','oc_order','',$unwind,$match,'','','','',0,$sort,'',$group);
                $log->write(($query));
                return $query->rows;
		}
}