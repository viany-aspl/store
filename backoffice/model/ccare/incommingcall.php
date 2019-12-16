<?php
date_default_timezone_set('Asia/Kolkata');

class ModelCcareIncommingcall extends Model 
{
	public function getretailer_info($data)
	{
        $query = $this->db->query('select','oc_user','','','',array('username'=>$data['mobile']),'','','','',array());
        return $query->row;
	}
	public function getCallStatus()
	{
		$where['STATUS_ID']=array('$in'=>array(2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27));
        $query = $this->db->query('select','oc_callstatus','','','',$where,'','','','',array('STATUS_NAME'=>1));
        return $query->rows;
	}
	public function getTicketStatus()
	{
        $query = $this->db->query('select','oc_call_ticket_status','','','','','','','','',array('STATUS_ID'=>1));
        return $query->rows;
	}
	public function getCallStatusByID($status_id)
	{
        $query = $this->db->query('select','oc_callstatus','','','',array('STATUS_ID'=>(int)$status_id),'','','','',array('STATUS_NAME'=>1));
        return $query->row['STATUS_NAME'];
	}
	public function getticketStatusByID($status_id)
	{
        $query = $this->db->query('select','oc_call_ticket_status','','','',array('STATUS_ID'=>(int)$status_id),'','','','',array('STATUS_NAME'=>1));
        return $query->row['STATUS_NAME'];
	}
    public function getCrop()
    {
        $query = $this->db->query('select','oc_crop');
        return $query->rows;
        
    }
    public function getStoreLocationdtl($dist_id)
    {
        $sql ="SELECT oc_store.*,oc_setting.value as address FROM oc_store join oc_setting on oc_setting.store_id=oc_store.store_id where oc_store.name like '".$dist_id."-%' and oc_setting.key='config_address' ";
        $query = $this->db->query($sql);
        return array();//$query->rows;
        
    }
	public function getDataByID($transid)
    {
		$query = $this->db->query('select','cc_incomingcall','','','',array('transid'=>(int)$transid),'','','','');
        return $query->row;  
        
    }
     
    public function getIncomingCall($data)
    {
        $where=array();
		if (!empty($data['filter_start_date'])) 
		{
			$sdate=$this->db->escape($data['filter_start_date']);
		}

		if (!empty($data['filter_end_date'])) 
		{
			$edate=$this->db->escape($data['filter_end_date']);
			
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
         
        if((!empty($datedata)) && (!empty($sdate)) && (!empty($edate)))
		{
            $where['timereceived']=$datedata;
        }
		if (!empty($data['filter_status'])) 
		{
			$where['status']=(int)$this->db->escape($data['filter_status']);
		} 
		else
		{
			$where['status']=array('$in'=>array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26));
		}
		if (!empty($data['filter_number'])) 
		{
			$search_string=$data['filter_number'];
            $where['mobile']=new MongoRegex("/.*$search_string/i");
			
		} 
		
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
		//print_r(json_encode($where));
        $query = $this->db->query('select','cc_incomingcall','','','',$where,'',(int)$data['limit'],'',(int)$data['start'],array('timereceived'=>-1));
        return $query;
    }
	public function getOpenCall($data)
    {
        $where=array();
		if (!empty($data['filter_start_date'])) 
		{
			$sdate=$this->db->escape($data['filter_start_date']);
		}

		if (!empty($data['filter_end_date'])) 
		{
			$edate=$this->db->escape($data['filter_end_date']);
			
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
         
        if((!empty($datedata)) && (!empty($sdate)) && (!empty($edate)))
		{
            $where['datetime']=$datedata;
        }
		if (!empty($data['ticket_status'])) 
		{
			$where['ticket_status']=(int)$this->db->escape($data['ticket_status']);
		} 
		else
		{
			$where['ticket_status']=array('$in'=>array(1,2));
		}
		if (!empty($data['filter_status'])) 
		{
			$where['to']=(int)$this->db->escape($data['filter_status']);
		} 
		else
		{
			$where['to']=array('$in'=>array(2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27));
		}
		if (!empty($data['filter_number'])) 
		{
			$search_string=$data['filter_number'];
            $where['mobile_number']=new MongoRegex("/.*$search_string/i");
		} 
		
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
		//print_r(json_encode($where));
        $query = $this->db->query('select','oc_call_history','','','',$where,'',(int)$data['limit'],'',(int)$data['start'],array('datetime'=>-1));
        return $query;
    }
	public function getresolved_closedCall($data)
    {
        $where=array();
		if (!empty($data['filter_start_date'])) 
		{
			$sdate=$this->db->escape($data['filter_start_date']);
		}

		if (!empty($data['filter_end_date'])) 
		{
			$edate=$this->db->escape($data['filter_end_date']);
			
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
         
        if((!empty($datedata)) && (!empty($sdate)) && (!empty($edate)))
		{
            $where['datetime']=$datedata;
        }
		if (!empty($data['ticket_status'])) 
		{
			$where['ticket_status']=(int)$this->db->escape($data['ticket_status']);
		} 
		else
		{
			$where['ticket_status']=array('$in'=>array(3,4));
		}
		if (!empty($data['filter_status'])) 
		{
			$where['to']=(int)$this->db->escape($data['filter_status']);
		} 
		else
		{
			$where['to']=array('$in'=>array(2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27));
		}
		if (!empty($data['filter_number'])) 
		{
			$search_string=$data['filter_number'];
            $where['mobile_number']=new MongoRegex("/.*$search_string/i");
		} 
		
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
		//print_r(json_encode($where));
        $query = $this->db->query('select','oc_call_history','','','',$where,'',(int)$data['limit'],'',(int)$data['start'],array('datetime'=>-1));
        return $query;
    }
	public function submit_incoming_call_data($data= array())
	{
		$log=new Log("call_trans_history-".date('Y-m-d').".log");
		//print_r($data);exit;
		//////////first enter a row in call trans history///////
		$cid=$this->db->getNextSequenceValue('oc_call_trans_history');
		
		$call_data=array('sid'=>(int)$cid,'from'=>(int)$data['from'],'to'=>(int)$data['to'],'ticket_id'=>(int)$data['transid'],'transtime'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))));
		 
		$trans_query = $this->db->query('insert','oc_call_trans_history',$call_data);
		$log->write($call_data); 
		
        if($data['to']==27)
		{
			///////////////////
			$sid=$this->db->getNextSequenceValue('oc_call_history');
			$insert_data=$data;
			$insert_data['sid']=(int)$sid;
			$insert_data['current_call_trans_id']=(int)$cid;
			$insert_data['feedbackcount']=(int)0;
			$insert_data['feedback']='';
			$transs_id=$cid; 
		
			$trans_query = $this->db->query('insert','oc_call_history',$insert_data);
			$log->write($insert_data); 
			
			
			$tid=$this->db->getNextSequenceValue('oc_call_ticket_trans_history');
			
			$ticket_data=array(
				'sid'=>(int)$tid,
				'ticket_id'=>(int)$data['transid'],
				'call_trans_id'=>(int)$sid,
				'ticket_status'=>(int)$data['ticket_status'],
				'from'=>(int)$data['current_ticket_status'],
				'to'=>(int)$data['ticket_status'],
				'query'=>$data['query'],
				'solution'=>$data['solution'],
				'transtime'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
			);
			$trans_query = $this->db->query('insert','oc_call_ticket_trans_history',$ticket_data);
			$log->write($ticket_data); 
        
		}
		
        $update_data=array(
						'status'=>(int)$data["to"]
					);
		$call_status_query = $this->db->query('update','cc_incomingcall',array('transid'=>(int)$data["transid"]),$update_data);
        $log->write($update_data);
        
	}
	public function submit_open_call_data($data= array())
	{
		$log=new Log("call_trans_history-".date('Y-m-d').".log");
		//////////first enter a row in call trans history///////
		$cid=$this->db->getNextSequenceValue('oc_call_trans_history');
		
		$call_data=array('sid'=>(int)$cid,'from'=>(int)$data['from'],'to'=>(int)$data['to'],'ticket_id'=>(int)$data['transid'],'transtime'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))));
		 
		$trans_query = $this->db->query('insert','oc_call_trans_history',$call_data);
		$log->write($call_data); 
		
        if($data['to']==27)
		{
			
			$tid=$this->db->getNextSequenceValue('oc_call_ticket_trans_history');
			
			$ticket_data=array(
				'sid'=>(int)$tid,
				'ticket_id'=>(int)$data['transid'],
				'call_trans_id'=>(int)$data['call_trans_id'],
				'ticket_status'=>(int)$data['ticket_status'],
				'from'=>(int)$data['current_ticket_status'],
				'to'=>(int)$data['ticket_status'],
				'query'=>$data['query'],
				'solution'=>$data['solution'],
				'transtime'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
			);
			$trans_query = $this->db->query('insert','oc_call_ticket_trans_history',$ticket_data);
			$log->write($ticket_data); 
			$update_data=array(
						'ticket_status'=>(int)$data['ticket_status'],
						'to'=>(int)$data["to"],
						'current_ticket_status'=>(int)$data['ticket_status'],
						'solution'=>$data['solution'],
						'feedbackcount'=>(int)0
					);
		}
		else
		{
				$update_data=array(
						
						'to'=>(int)$data["to"]
					);
		}
        
		$call_status_query = $this->db->query('update','oc_call_history',array('sid'=>(int)$data['call_trans_id']),$update_data);
        $log->write($update_data);
        
	}

}