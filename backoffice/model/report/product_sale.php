<?php
class ModelReportProductSale extends Model {
	
	
	public function getOrders($data = array()) 
	{ 
		
		$where=array();
		if(!empty($data['filter_product_id']))
        {
            $where['order_product.product_id']= (int)$data['filter_product_id'] ; 
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
                $where['date_added']=$datedata;
            }
            $where['order_status_id']=5;
		    
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
			if (!empty($data['filter_store'])) 
            {
				$where['store_id']= (int)$data['filter_store'] ; 
            }
			$group[]=array(
                    "_id" =>array('product'=>'$order_product.product_id'),
                    );
		$sort=array("date_added"=>-1);
        $query = $this->db->query("select","oc_order",'','','',$where,'',(int)$data['limit'],'',(int)$data['start'],$sort);
		//print_r($query->rows);
		return $query;
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