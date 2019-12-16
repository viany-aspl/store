<?php
class ModelReportInventoryLedger extends Model 
{
	
	public function getproducttrans($data = array()) 
	{
             $lookup=array(array(
                'from' => 'oc_product',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'op'
            ),array(
                'from' => 'oc_store',
                'localField' => 'store_id',
                'foreignField' => 'store_id',
                'as' => 'os'
            )           
            );    
              
             if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
                        $limit=(int)$data['limit'];
            $start=(int)$data['start'];
        }
            
           $groupby=array('product_id','store_id');
            
           $columns=array(
                    "_id"=> 1,
                    "quantity"=>1,
                    "order_id"=> 1,
                    "cr_db"=> 1,
                    "trans_type"=> 1,
                    "current_quantity"=> 1,
					"current_mitra_quantity"=> 1,
                    "trans_time"=> 1,
                    "op.model"=> 1,
                     "os.name"=> 1
                );  
           
        if (!empty($data['filter_store'])) 
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
		if(!empty($sdate) && ($edate))
		{
			 $match['trans_time']=$datedata;
		}
        if (!empty($data['filter_product_id'])) 
		{
            
                 $match['product_id']=(int)($data['filter_product_id']);
        } 
		
        $query = $this->db->query("join","oc_product_trans",$lookup,'',$match,'','',$limit,$columns,$start,array('trans_time'=>-1,'op.model'=>1));
        
        $a=0;
                 foreach($query->row as $row)
                 {
                //print_r($row);  
               // exit;
               // $return_array['id']=$row['_id'];
               // $return_array[$a]['product_id']=$row['op'][0]['product_id'];
                $return_array[$a]['cr_db']=$row['cr_db'];
                $return_array[$a]['order_id']=$row['order_id'];
                $return_array[$a]['quantity']=$row['quantity'];
                 $return_array[$a]['trans_type']=$row['trans_type'];
                $return_array[$a]['current_quantity']=$row['current_quantity'];
				$return_array[$a]['current_mitra_quantity']=$row['current_mitra_quantity'];
                $return_array[$a]['trans_time']=$row['trans_time'];
                $return_array[$a]['model']=$row['op'][0]['model'];
                $return_array[$a]['name']=$row['os'][0]['name'];
               // $return_array[$a]['price']=$row['op'][0]['price'];
               // print_r($return_array);
                $a++;
            }
         // print_r($return_array);
         //$query = $this->db->query($sql);
               return  $return_array;
            
            
	}
	public function getproducttransTotal($data = array()) 
	{
		$lookup=array(array(
                'from' => 'oc_product',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'op'
            ),array(
                'from' => 'oc_store',
                'localField' => 'store_id',
                'foreignField' => 'store_id',
                'as' => 'os'
            )           
            );    
              
           $groupby=array('product_id','store_id');
            
           $columns=array(
                    "_id"=> 1,
                    "quantity"=>1,
                    "order_id"=> 1,
                    "cr_db"=> 1,
                    "trans_type"=> 1,
                    "current_quantity"=> 1,
                    "trans_time"=> 1,
                    "op.model"=> 1,
                     "os.name"=> 1
                );  
           
        if (!empty($data['filter_store'])) 
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
		if(!empty($sdate) && ($edate))
		{
			 $match['trans_time']=$datedata;
		}
        if (!empty($data['filter_product_id'])) 
		{
            
                 $match['product_id']=(int)($data['filter_product_id']);
        } 
       
        $query = $this->db->query("join","oc_product_trans",$lookup,'',$match,'','','',$columns,0);
		return count($query->rows);
	}
	public function getTotalPurchased($data) {
		$sql = "SELECT COUNT(*) as total from (SELECT op.quantity,op.order_id,op.cr_db,op.trans_type,DATE(op.trans_time) as trans_time ,product.model,store.name FROM `oc_product_trans` as op
LEFT JOIN oc_store as store on store.store_id=op.store_id
LEFT JOIN oc_product as product on product.product_id=op.product_id)";
	
		if (!empty($data['filter_stores_id'])) {
			$sql .= " WHERE op.store_id= '" . (int)$data['filter_stores_id'] . "'";
		} else {
			$sql .= " WHERE op.store_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(op.trans_time) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(op.trans_time) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}