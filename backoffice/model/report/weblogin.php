<?php
class ModelReportWeblogin extends Model 
{
	
	public function getweblogintrans($data) 
	{
		$match=array();
        if(!empty($data['filter_store']))
		{
			$match['store_id']=(int)$data['filter_store'];
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
            $match['start_time']=$datedata;
        }
		
        $query = $this->db->query('select','oc_qr_login_trans','','','',$match);
        //print_r($query);    	
		return $query;
	}
	public function getstoreinfo($s_id) 
    {		
        $query = $this->db->query("SELECT",  DB_PREFIX . "store",'','','',array('store_id'=>(int)$s_id));
        return $query->row;
    }

}