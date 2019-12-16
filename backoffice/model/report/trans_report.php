<?php
class ModelReportTransreport extends Model 
{
	public function getCustomerActivities($data = array()) 
	{
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
        $if=array('$eq'=>array('$key','addcustomer'));//addcustomer
		$cond=array($if,1,0);
		
		$if2=array('$eq'=>array('$key','Order-Open-Billing'));//Order-Open-Billing
		$cond2=array($if2,1,0);
		
		$if3=array('$eq'=>array('$key','Order-Led-Billing'));//Order-Led-Billing
		$cond3=array($if3,1,0);
		
		$if4=array('$eq'=>array('$key','addproductunverified'));//addproductunverified
		$cond4=array($if4,1,0);
		
		$if5=array('$eq'=>array('$key','addtofavouritedproduct'));//addtofavouritedproduct
		$cond5=array($if5,1,0);
		
		
		$if6=array('$eq'=>array('$key','remove_favourite'));//remove_favourite
		$cond6=array($if6,1,0);
		
		$if7=array('$eq'=>array('$key','addtobookmarkproduct'));//addtobookmarkproduct
		$cond7=array($if7,1,0);
		
		$if8=array('$eq'=>array('$key','remove_bookmark'));//remove_bookmark
		$cond8=array($if8,1,0);
		
		$if9=array('$eq'=>array('$key','addproductrating'));//addproductrating
		$cond9=array($if9,1,0);
		
		$if10=array('$eq'=>array('$key','product_request'));//product_request
		$cond10=array($if10,1,0);
		
		$if11=array('$eq'=>array('$key','invoice'));//invoice
		$cond11=array($if11,1,0);
		
		$if12=array('$eq'=>array('$key','printer-request'));//printer_request
		$cond12=array($if12,1,0);
		//$match['billtype']=0;
        $group=array();
		
        $group[]=array(
                    "_id" =>array( 'yearMonthDay'=> array('$dateToString'=>array('format'=>'%Y-%m-%d','date'=>'$date_added'))),
                    "addcustomer" =>array('$sum'=>array('$cond'=>$cond)),
					"Order_Open_Billing" =>array('$sum'=>array('$cond'=>$cond2)),
					"Order_Led_Billing" =>array('$sum'=>array('$cond'=>$cond3)),
					"addproductunverified" =>array('$sum'=>array('$cond'=>$cond4)),
					"addtofavouritedproduct" =>array('$sum'=>array('$cond'=>$cond5)),
					"remove_favourite" =>array('$sum'=>array('$cond'=>$cond6)),
					"addtobookmarkproduct" =>array('$sum'=>array('$cond'=>$cond7)),
					"remove_bookmark" =>array('$sum'=>array('$cond'=>$cond8)),
					"addproductrating" =>array('$sum'=>array('$cond'=>$cond9)),
					"product_request" =>array('$sum'=>array('$cond'=>$cond10)),
					"invoice" =>array('$sum'=>array('$cond'=>$cond11)),
					"printer_request" =>array('$sum'=>array('$cond'=>$cond12)),
					"count"=> array('$sum'=>1)
					);
					/*
        $group[]=array("_id"=>'$_id.transid',"paytype"=>array('$push'=>array("type"=>'$_id.timereceived',
                    "missedcall"=>'$missedcall',
					"Closed"=>'$Closed',
					"Resolved"=>'$Resolved',
					"in_process"=>'$in_process',
					"ocount"=>'$count',
                    )));
					*/
        $sort=array("timereceived"=>-1);
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
        $query=$this->db->query('join','oc_customer_activity','','',$match,'','',(int)$data['limit'],array(),(int)$data['start'],$sort,'',$group);
		//print_r($query);exit;
		
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