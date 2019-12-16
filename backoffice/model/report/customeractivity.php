<?php
class ModelReportCustomeractivity extends Model 
{
	public function getCustomerActivities($data = array()) 
	{
            
            if(!empty($data['filter_store'])){
                  $match['customer_id']=(int)$data['filter_store'];
            }

		if (!empty($data['filter_date_start'])) 
		{
            
			$sdate=$this->db->escape($data['filter_date_start']);
        }
		if (!empty($data['filter_date_end'])) 
		{
            
			$edate=$this->db->escape($data['filter_date_end']);
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
            $match['date_added']=$datedata;
        }
    
        
        
	$group=array(array('_id'=> array('$toUpper'=>'$key'), 
                "total"=> array('$sum'=> 1) ));
         
        $sort=array("key"=>1);
        
        $query=$this->db->query('join','oc_customer_activity','','',$match,'','',(int)20,array(),(int)0,$sort,'',$group);
	//print_r(json_encode($query));
        //exit;
		
        return $query; 
	
	
	}

        public function incomingcall_answer()
        {
            $sql="SELECT ci.datereceived,ci.mobile,ocs.STATUS_NAME,cf.* FROM shop.cc_incomingcall as ci
            left join ccare_feedback as cf on ci.transid=ci.transid
            left join oc_callstatus as ocs on ocs.STATUS_ID=ci.status where ci.status='27'";
            $implode = array();

				
		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(ci.datereceived) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(ci.datereceived) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
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
//echo $sql;
            $query = $this->db->query($sql);
            return $query->rows;
        }
}