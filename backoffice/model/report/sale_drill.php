<?php
class ModelReportSaleDrill extends Model 
{


	public function getYearWiseSale($data = array()) 
	{	

	

		$group[]=array('_id'=>array('$year'=>'$date_added'),'total'=>array('$sum'=>'$total'));

		  				
		$match['order_status_id']=5;
        	$query=$this->db->query('join','oc_order','','',$match,'','','','','','','',$group);
        
        	return $query;  
	}




	public function getSale_summary($data = array()) 
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
        if (!empty($data['filter_store'])) 
		{
            $match['store_id']=(int)$this->db->escape($data['filter_store']);
        }  
        if(!empty($datedata))
		{
            $match['date_added']=$datedata;
        }
        $if=array('$eq'=>array('$billtype',1));
		$cond=array($if,1,0);
		
		$if2=array('$eq'=>array('$billtype',0));
		$cond2=array($if2,1,0);
		//$match['billtype']=0;
                    $group=array();
                    $group[]=array(
                    "_id" =>array('store'=>'$store_id', "payment"=>'$pay_method'),
                    "total" =>array('$sum'=>'$total'),
                    "credit" =>array('$sum'=>'$credit'),
                    "cash"=> array('$sum'=>'$cash'),
					"discount"=> array('$sum'=>'$discount'),
                    "count"=> array('$sum'=>1),
					"openbilling" =>array('$sum'=>array('$cond'=>$cond)),
					"ledbilling" =>array('$sum'=>array('$cond'=>$cond2)),
					
                    "name"=> array('$first'=>'$store_name')
					);
        $group[]=array("_id"=>'$_id.store',"paytype"=>array('$push'=>array("type"=>'$_id.payment',
                    "amount"=>'$total',
					"credit"=> '$credit',
                    "cash"=> '$cash',
					"discount"=> '$discount',
                    "ocount"=>'$count',
					"creditcount"=>'$creditcount',
					"openbilling"=>'$openbilling',
					"ledbilling"=>'$ledbilling',
                    "sname"=>'$name'
                    )),"ctotal"=>array('$sum'=>'$total'));
        $sort=array("ctotal"=>-1);
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
        $query=$this->db->query('join','oc_order','','',$match,'','',(int)$data['limit'],array(),(int)$data['start'],$sort,'',$group);
		//print_r($query->num_rows);exit;
		foreach($query->row as $result)
		{
			foreach($result['paytype'] as $result2)
			{
				
				$return_array[$result["_id"]][]=array(
					"_id"=>$result["_id"],
					'type'=>$result2['type'],
                    'Credit'=>$result2['credit'],
                    'Cash'=>$result2['cash'],
					'discount'=>$result2['discount'],
                    'order_count'=>$result2['ocount'],
					'openbilling'=>$result2['openbilling'],
					'ledbilling'=>$result2['ledbilling'],
                    'store_name'=>strtoupper($result2['sname']),
					'store_id'=>$result['_id'],
                    'totalcount'=>$query->num_rows
                      );
			}
		}
	
		/*
        foreach($query->row as $result)
		{
            $return_array[]=array(
					"_id"=>$result["_id"],
					'type'=>$result['paytype'][0]['type'],
                    'Credit'=>$result['paytype'][0]['credit'],
                    'Cash'=>$result['paytype'][0]['cash'],
                    'cash_order'=>$result['paytype'][0]['ocount'],
                    'store_name'=>strtoupper($result['paytype'][0]['sname']),
					'store_id'=>$result['_id'],
                    'total'=>$query->total_rows
                      );
                    
        }
		
		$rows = count($return_array);
		$cols = count($return_array[0]); // assumes non empty matrix
		$ridx = 0;
		$cidx = 0;

		$out = array();

		foreach($return_array as $rowidx => $row)
		{
			foreach($row as $colidx => $val)
			{
				$out[$ridx][$cidx] = $val;
				$ridx++;
				if($ridx >= $rows)
				{
					$cidx++;
					$ridx = 0;
				}
			}
		}
		*/
		
        //print_r($return_array);
		//exit;
        return $return_array; 
	}
	

private function flip_row_col_array($array) {
    $out = array();
    foreach ($array as  $rowkey => $row) {
		
        foreach($row as $colkey => $col){
            $out[$colkey][$rowkey]=$col;
        }
    }
    return $out;
}


	//////////////////////////

	public function getTotalSale_summary($data = array()) 
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
                
        if (!empty($data['filter_store'])) 
		{
            $match['store_id']=(int)$this->db->escape($data['filter_store']);    
        }
        $match['order_status_id']=5;
        if(!empty($datedata))
		{
            $match['date_added']=$datedata;
        }
        $groupby=array(
                   "_id"=> '$order_status_id',
                   "Total"=> array('$sum'=> '$total'),
                   "Cash"=> array('$sum'=> '$cash') ,
                  "Credit" =>array('$sum'=>'$credit'),
                  "Discount" =>array('$sum'=>'$discount')    
                 );
        $query = $this->db->query('gettotalsum','oc_order',$groupby,$match);
        //print_r($query->row[0]);exit;
        return $query->row[0]; 
	}

	


	public function getTilldateorder($data = array()) 
	{	  				
		$match['order_status_id']=5;
        $query=$this->db->query('select','oc_order','','','',$match,'','',array('order_id'),(int)0,'','','');
        
        return $query->num_rows;  
	}
	public function getYesterdayorder($data = array()) 
	{	  				
		$match['order_status_id']=5;
		$datedata=array();
		$sdate=date('Y-m-d');
		$edate=date('Y-m-d'); 
        if(strtotime($sdate)==strtotime($edate))
        {
                $datedata=array(
                                '$gt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('-1 day', strtotime($sdate)))  )),
                                '$lt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime($edate))) ))
                                    
                                );
        }
		
        if(!empty($datedata))
		{
            $match['date_added']=$datedata;
        }
        $query=$this->db->query('select','oc_order','','','',$match,'','',array('order_id'),(int)0,'','','');
        
        return $query->num_rows;  
	}
	public function getTilldateregister($data = array()) 
	{	  				
		$match['user_group_id']=11;
        $query=$this->db->query('select','oc_user','','','',$match,'','','',(int)0,'','','');
        
        return $query;  
	}
	public function getYesterdayregister($data = array()) 
	{	  				
		$match['user_group_id']=11;
		$datedata=array();
		$sdate=date('Y-m-d');
		$edate=date('Y-m-d'); 
        if(strtotime($sdate)==strtotime($edate))
        {
            $datedata=array(
                        '$gt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('-1 day', strtotime($sdate)))  )),
                        '$lt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime($edate))) ))
                        );
        }
		
        if(!empty($datedata))
		{
            $match['date_added']=$datedata;
        }
        $query=$this->db->query('select','oc_user','','','',$match,'','',array('user_id'),(int)0,'','','',$match);
        
        return $query;  
	}


}