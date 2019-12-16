<?php
class ModelReportWhatsappinv extends Model 
{
	
	public function getwhatsappinv($data) 
	{
		$match=array();
        if(!empty($data['filter_store']))
		{
			$match['store_id']=$data['filter_store'];
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
            $match['submit_time']=$datedata;
        }
		$sort=array("store_name"=>1);
		//$sort=array("ctotal"=>-1);
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
		//,'','',(int)$data['limit'],array(),(int)$data['start'],$sort,'',$group
        $query = $this->db->query('select','oc_whatsapp_inv','','','',$match,'',(int)$data['limit'],'',(int)$data['start'],$sort);
        //print_r($query);    	
		return $query;
	}


}