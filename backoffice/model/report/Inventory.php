<?php
class ModelReportInventory extends Model {
	
	public function getInventory_report($data = array()) 
	{ 
		$lookup=array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                );
        $match=array();
		if (!empty($data['filter_store']) ) 
		{
			$match['pd.store_id']=(int)$data['filter_store'];	
		}
 
		//$match['pd.quantity']=array('$gt'=>0); 
		$match['$or']=array(array('pd.quantity'=>array('$gt'=>0)),array('pd.mitra_quantity'=>array('$gt'=>0)));
		$group='';//array("_id"=>array('$product_id'));
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
		$or=array('store_id'=>(int)$data['filter_store'],'$or'=> array(array('quantity'=>array('$gt'=>0)),array('mitra_quantity'=>array('$gt'=>0))));
        $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'$pd',$match,'','',(int)$data['limit'],'',(int)$data['start'],'','','',$group);
        $query->total_rows=$this->db->getcount('oc_product_to_store',$or);
		return $query;
	}

	public function getTotalInventory($data = array()) 
	{
		$lookup=array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                );
        $match=array();
		if (!empty($data['filter_store']) ) 
		{
			$match['pd.store_id']=(int)$data['filter_store'];	
		}
 
		$match['$or']=array(array('pd.quantity'=>array('$gt'=>0)),array('pd.mitra_quantity'=>array('$gt'=>0))); 
		
		$group='';
       
        $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'$pd',$match,'','','','',(int)0,'','','',$group);
		foreach($query->rows as $result)
		{
			$totalamount=$totalamount+$result['pd']['quantity']*$result['pd']['store_price'];
		}
		return $totalamount;
	}


   public function getInventory_reportProductWise($data = array()) { //print_r($data);
       
         $lookup=array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                );    
          $match=array();
          $where=array();
		if (!empty($data['filter_name_id']) ) 
		{
    
            $match['product_id']=(int)$data['filter_name_id'];
            $where['product_id']=(int)$data['filter_name_id'];
            
        }
		
        $match['$or']=array(array('pd.quantity'=>array('$gt'=>0)),array('pd.mitra_quantity'=>array('$gt'=>0))); 
        $where['$or']=array(array('quantity'=>array('$gt'=>0)),array('mitra_quantity'=>array('$gt'=>0))); 
 

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
       $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'$pd',$match,'','',(int)$data['limit'],'',(int)$data['start'],'','','',$group);
                $query->total_rows=$this->db->getcount('oc_product_to_store',$where);
                
        return $query;
}

	public function getInventory_report_daily_email($data = array()) { //print_r($data);
$sql="SELECT ots.store_id as store_id,ots.product_id,ots.quantity as Qnty,os.name as store_name,opd.name as Product_name FROM `oc_product_to_store` as ots join oc_store as os on ots.store_id=os.store_id join oc_product as op on ots.product_id=op.product_id

left join oc_product_description as opd on op.product_id=opd.product_id
 where ots.quantity>0 and ots.store_id!=14 "; 	 

            
            
                //echo $sql;
		$query = $this->db->query($sql);
                
		return $query->rows;
	}

        public function setInventory_report_daily_email($filename,$report_date,$store_id,$store_name='')
        {
            $query = $this->db->query('delete','oc_store_inventory_daily_report',array('store_id'=>(int)$store_id,'report_date'=>$report_date));
             
            $query = $this->db->query('insert','oc_store_inventory_daily_report',array('store_id'=>(int)$store_id,'store_name'=>$store_name,'filename'=>$filename,'report_date'=>$report_date));
        }
        public function get_old_report_daily_email($data=array())
        {
			$where=array();
             
            if($data["filter_date"]!="")
            {
				$where['report_date']=$data["filter_date"];
            }
			if($data["filter_store"]!="")
            {
				$where['store_id']=(int)$data["filter_store"];
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
             //echo $sql1;
			 $sort=array('store_name'=>1);
             $query = $this->db->query('select','oc_store_inventory_daily_report','','','',$where,'',(int)$data['limit'],'',(int)$data['start'],$sort);
             return $query;
             
          }
          public function get_total_old_report_daily_email($data=array())
          {
             $sql1=" select count(*) as total from ( select * from  `oc_store_inventory_daily_report`  ";
             if($data["filter_date"]!="")
             {
             $sql1.=" where `report_date`='".$data["filter_date"]."' ";

              
             }
             $sql1.=" ) as aa";

             $query = $this->db->query($sql1);
             return $query->row["total"];
             
          }
          
	
}