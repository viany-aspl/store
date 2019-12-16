<?php
class ModelReportIncommingcallreport extends Model 
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
            $match['timereceived']=$datedata;
        }
        $if=array('$eq'=>array('$status',1));
		$cond=array($if,1,0);
		
		$if2=array('$eq'=>array('$status',2));
		$cond2=array($if2,1,0);
		
		$if3=array('$eq'=>array('$status',3));
		$cond3=array($if3,1,0);
		
		$if4=array('$eq'=>array('$status',4));
		$cond4=array($if4,1,0);
		
		$if5=array('$eq'=>array('$status',5));
		$cond5=array($if5,1,0);
		
		$if6=array('$eq'=>array('$status',8));
		$cond6=array($if6,1,0);
		
		$if7=array('$eq'=>array('$status',10));
		$cond7=array($if7,1,0);
		
		$if8=array('$eq'=>array('$status',12));
		$cond8=array($if8,1,0);
		
		$if9=array('$eq'=>array('$status',14));
		$cond9=array($if9,1,0);
		
		$if10=array('$eq'=>array('$status',17));
		$cond10=array($if10,1,0);
		
		$if11=array('$eq'=>array('$status',19));
		$cond11=array($if11,1,0);
		
		$if12=array('$eq'=>array('$status',21));
		$cond12=array($if12,1,0);
		
		$if13=array('$eq'=>array('$status',27));
		$cond13=array($if13,1,0);
		//$match['billtype']=0;
        $group=array();
		
        $group[]=array(
                    "_id" =>array( 'yearMonthDay'=> array('$dateToString'=>array('format'=>'%Y-%m-%d','date'=>'$timereceived'))),
                    "missedcall" =>array('$sum'=>array('$cond'=>$cond)),
					"Busy" =>array('$sum'=>array('$cond'=>$cond2)),
					"CallLater" =>array('$sum'=>array('$cond'=>$cond3)),
					"NotPicking" =>array('$sum'=>array('$cond'=>$cond4)),
					"AttemptLater" =>array('$sum'=>array('$cond'=>$cond5)),
					"Tobeattempted" =>array('$sum'=>array('$cond'=>$cond6)),
					"Notreachable" =>array('$sum'=>array('$cond'=>$cond7)),
					"SwitchOff" =>array('$sum'=>array('$cond'=>$cond8)),
					"Enquiry" =>array('$sum'=>array('$cond'=>$cond9)),
					"GeneralCall" =>array('$sum'=>array('$cond'=>$cond10)),
					"RecievedCall" =>array('$sum'=>array('$cond'=>$cond11)),
					"NotInterested" =>array('$sum'=>array('$cond'=>$cond12)),
					"Answered" =>array('$sum'=>array('$cond'=>$cond13)),
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
        $query=$this->db->query('join','cc_incomingcall','','',$match,'','',(int)$data['limit'],array(),(int)$data['start'],$sort,'',$group);
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