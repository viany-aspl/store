<?php
class ModelPrinterPrinter extends Model 
{
	public function getprinterquestion($data) 
    {		
		$query = $this->db->query("SELECT",  DB_PREFIX . "printer",'','','','','','','',(int)$data['start']);
		return $query;
    }
	public function getprinteranswer($data) 
    {		
		$query = $this->db->query("SELECT",  DB_PREFIX . "printer",'','','',array('printer_id'=>(int)$data['question_id']),'',1,'',(int)$data['start']);
		return $query;
    }
	public function printer_request($data)
	{
		$sid=$this->db->getNextSequenceValue(DB_PREFIX . "printer_request");
		$data=array(
                    'printer_id'=>(int)$data['printer_id'],
					'store_id'=>(int)$data['store_id'],
					'sid'=>(int)$sid,
                    'billing_name'=>$data['billing_name'],
                    'contact_person_name'=>$data['contact_person_name'],
                    'contact_number'=>$data['contact_number'],
                    'gstn'=>$data['gstn'],
                    'email'=>$data['email'],
                    'shipping_address'=>$data['shipping_address'],
                    'billingtoo'=>$data['billingtoo'],
                    'permanent_address'=>$data['permanent_address'],
                    'request_time'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                    
                );
                $this->db->query("INSERT" , DB_PREFIX . "printer_request",$data);
		return $sid;
	}
	public function addprinter($data) 
    {           
        $data=array(
                    'printer_id'=>(int)$this->db->getNextSequenceValue(DB_PREFIX . "printer"),
                    'name'=>$data['name'],
                    'price'=>$data['price'],
                    'manufacturer_name'=>$data['manufacturer_name'],
                     'model'=>$data['model'],
                    'description'=>$data['description'],
                     'width'=>$data['width'],
                    'color'=>$data['color'],
                     'character'=>$data['character'],
                    'item'=>$data['item'],
                    'warranty'=>$data['warranty'],
                    'manufacturer_helpdesk'=>$data['manufacturer_helpdesk'],
                    'mail'=>$data['mail'],
                    'manufacturer_address'=>$data['manufacturer_address'],
                    'image'=>array($data['image'],$data['image1']),
					'add_time'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                );
        $this->db->query("INSERT" , DB_PREFIX . "printer",$data);
	}
	
	public function check_status($data) 
    {		
		$query = $this->db->query("select",  DB_PREFIX . "printer_request",'','','',array('store_id'=>(int)$data['store_id'],'printer_id'=>(int)$data['printer_id']),'',1,'',(int)0);
		return $query;
    }
    public function getprinterdetails($data) 
    {		
		$query = $this->db->query("SELECT",  DB_PREFIX . "printer",'','','','','',20,'',(int)$data['start']);
		return $query;
    }
    public function getprinterinfo($p_id) 
    {		
        $query = $this->db->query("SELECT",  DB_PREFIX . "printer",'','','',array('printer_id'=>(int)$p_id));
        return $query->row;
    }
	public function getstoreinfo($s_id) 
    {		
        $query = $this->db->query("SELECT",  DB_PREFIX . "store",'','','',array('store_id'=>(int)$s_id));
        return $query->row;
    }
	public function editprinter($p_id, $data) 
	{		
		$data=array(
                  
                   'name'=>$data['name'],
                    'price'=>$data['price'],
                    'manufacturer_name'=>$data['manufacturer_name'],
                     'model'=>$data['model'],
                    'description'=>$data['description'],
                     'width'=>$data['width'],
                    'color'=>$data['color'],
                     'character'=>$data['character'],
                    'item'=>$data['item'],
                    'warranty'=>$data['warranty'],
                    'manufacturer_helpdesk'=>$data['manufacturer_helpdesk'],
                    'mail'=>$data['mail'],
                    'manufacturer_address'=>$data['manufacturer_address'],
                    'image'=>array($data['image'],$data['image1']),
                );
		$where=array('printer_id'=>(int)$p_id);
        $this->db->query("update" , DB_PREFIX . "printer",$where,$data);    
	}

	public function deleteprinter($p_id) 
	{
		$this->db->query("delete",DB_PREFIX . "printer", array('printer_id' =>(int)$p_id));
	}
	public function getLocations($data = array()) 
    {
        if (isset($data['start']) || isset($data['limit'])) 
        {
			if ($data['start'] < 0) 
            {
                    $start = 0;
			}
            else 
            {
                $start = (int)$data['start'];
            }
            if ($data['limit'] < 1) 
            {
                $limit = 20;
            }
            else 
            {
                $limit = (int)$data['limit'];
            }
		}
        $query = $this->db->query('select',DB_PREFIX . 'location','','','','','',$limit,'',$start,array('name'=>1));
  
		return $query;
	}
	public function getprinterrequest($data) 
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
            $match['request_time']=$datedata;
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
        $query = $this->db->query('select','oc_printer_request','','','',$match,'',(int)$data['limit'],'',(int)$data['start'],$sort);
        //print_r($query);    	
		return $query;
	}
}
