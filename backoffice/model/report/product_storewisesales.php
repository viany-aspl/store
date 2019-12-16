<?php
class ModelReportProductStorewisesales extends Model {
	
	public function getSales($data = array()) 
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
		if(!empty($sdate) && ($edate))
		{
			 $match['date_added']=$datedata;
		}
        if (!empty($data['filter_name_id'])) 
		{
            
                 $match['order_product.product_id']=(int)($data['filter_name_id']);
        }   
        
        
        if (!empty($data['filter_store'])) 
		{
            $match['store_id']=(int)($data['filter_store']);
        }
                
                
                
            $match['order_status_id']=5;
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
           $unwind=array('$order_product');
           $group[]=array("_id"=>array("store"=> '$store_id',
               "prd"=> '$order_product.product_id'
             ),  
               "qunty"=>array('$sum' => '$order_product.quantity'),
                "pname"=>array('$first'=>'$order_product.name'),
               "sname"=>array('$first'=>'$store_name'),
			   "ptax"=>array('$first'=>'$order_product.tax'),
                "total"=>array('$sum'=>'$order_product.total'),
          );
            //$start=0;
            //echo $start;
			$sort=array("sname"=>1,'pname'=>1);
            $query = $this->db->query('join','oc_order','',$unwind,$match,'','',$limit,'',$start,$sort,'',$group,$match);
			//print_r($query);
			//exit;
            foreach($query->rows as $result){
             //print_r($result);
              $return_array[]=array("_id"=>$result['_id'],
                  "store_id"=>$result['store'],
                    'store_name'=>$result['sname'],
                    'quantity'=>$result['qunty'],
                    'name'=>$result['pname'],
                  'product_id'=>$result['prd'],
                    'tax'=>$result['ptax'],
					'total'=>$result['total'],
					'num_rows'=>$query->num_rows,
                     //'totals'=>$query->total_rows    
                );
            }
          return $return_array;
                
	}
public function getTotalsales($data = array()) 
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
		if(!empty($sdate) && ($edate))
		{
			 $match['date_added']=$datedata;
		}
        if (!empty($data['filter_name_id'])) 
		{
            
                 $match['order_product.product_id']=(int)($data['filter_name_id']);
        }   
        
        
        if (!empty($data['filter_store'])) 
		{
            $match['store_id']=(int)($data['filter_store']);
        }
                
                
                
            $match['order_status_id']=5;
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
           $unwind=array('$order_product');
           $group[]=array("_id"=>array("store"=> '$store_id',
               "prd"=> '$order_product.product_id'
             ),  
               "qunty"=>array('$sum' => '$order_product.quantity'),
                "pname"=>array('$first'=>'$order_product.name'),
               "sname"=>array('$first'=>'$store_name'),
			   "ptax"=>array('$first'=>'$order_product.tax'),
                "total"=>array('$sum'=>'$order_product.total'),
          );
            //$start=0;
            //echo $start;
            $query = $this->db->query('join','oc_order','',$unwind,$match,'','','','',0,'','',$group,$match);
			
          return $query;
                
	}

public function getSalescount($data = array()) {


		
             /*   $sql="select sum(case when o.payment_method='Cash' then p.quantity else 0 end)as qnty_of_cash, sum(case when o.payment_method='Tagged' then p.quantity else 0 end)qnty_of_tagged,
 sum(case when o.payment_method='Tagged Cash' then p.quantity else 0 end)as qnty_of_tagged_cash,
  sum(case when o.payment_method='Subsidy' then p.quantity else 0 end)as qnty_of_Subsidy,o.store_name,p.product_id, p.name from oc_order_product p left JOIN oc_order o on o.order_id=p.order_id ";

               $sql.="  where date(p.ORD_DATE)<= '".$data["filter_date_end"]."' ";
       
               if (!empty($data['filter_date_start'])) {
			$sql .= " and date(p.ORD_DATE)>='".$data["filter_date_start"]."' ";
		}

		
                if (!empty($data['filter_name_id'])) {
			$sql .= " and p.product_id='".$data["filter_name_id"]."' ";
		}
		if (!empty($data['filter_store'])) {
                        if (!empty($data['filter_name_id'])) {
                            $sql .= " and o.store_id='".$data["filter_store"]."' ";
                        }
                        else
                        {
                            $sql .= " and  o.store_id='".$data["filter_store"]."' ";
                        }
			
		}

		$sql.=" and o.order_status_id=5 ";


                $sql.=" GROUP by p.product_id,o.store_id ";//,p.order_id



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

		return $query->rows;*/
    
      if (!empty($data['filter_date_start'])) {
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
                
             if (!empty($data['filter_name_id'])) {
            
                 $match['product_id']=(int)($data['filter_name_id']);
        }   
                
                
                
            $match['order_status_id']=5;
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
    
         if (!empty($data['filter_store'])) {
                        if (!empty($data['filter_name_id'])) {
                          //  $sql .= " and o.store_id='".$data["filter_store"]."' ";
                       
                               $match['store_id']=(int)($data['filter_store']);
                            
                        }
                        else
                        {
                            //$sql .= " and  o.store_id='".$data["filter_store"]."' ";
                            $match['store_id']=(int)($data['filter_store']);
                        }
			
		}
               $match['order_status_id']=5;
               // $group=array();
              $group[]=array("_id"=>array("store"=> '$store_id',"prd"=> '$order_product.product_id',
                
             ),  
               "qunty"=>array('$sum' => '$order_product.quantity'),
                "pname"=>array('$first'=>'$order_product.name'),
               "sname"=>array('$first'=>'$store_name'),
               "tmethod" =>array('$first'=>'$payment_method'),
               "cmethod"=> array('$first'=>'$payment_method'),
          );
            $unwind=array('$order_product');
            
           $query = $this->db->query('join','oc_order','',$unwind,$match,'','',$limit,'',$start,'','',$group);
        
            foreach($query->row as $result){
             //print_r($result);
            // exit;
                if($result['cmethod']=='Cash'){
                    $quantity_cash=$result['qunty'];
                }
                else{
                   $quantity_cash=0;
                }
                
                  if($result['tmethod']=='Tagged'){
                    $quantity_tagged=$result['qunty'];
                }
                else{
                   $quantity_tagged=0;
                }
                
              $return_array[]=array("_id"=>$result['_id'],
                    'store_name'=>$result['sname'],
                   "store_id"=>$result['store'],
                    'quantity'=>$result['qunty'],
                    'name'=>$result['pname'],
                    'product_id'=>$result['prd'],
                    'qnty_of_cash'=>$quantity_cash,//$result['order_product']['quantity'],
                    'qnty_of_tagged'=>$quantity_tagged,//$result['order_product']['quantity'],
                    //'total'=>$result['order_product']['total']+$result['order_product']['tax'],
                      //'totals'=>$query->total_rows    
                );
            }
           
           //print_r($return_array);
          return $return_array;
    
    
    
    
	}



	public function getTotalsalescount($data) {

/*$sql="select count(*) as total from ( select o.store_name from oc_order_product p left JOIN oc_order o on o.order_id=p.order_id ";

               $sql.="  where date(p.ORD_DATE)<= '".$data["filter_date_end"]."' ";
       
               if (!empty($data['filter_date_start'])) {
			$sql .= " and date(p.ORD_DATE)>='".$data["filter_date_start"]."' ";
		}

		
                if (!empty($data['filter_name_id'])) {
			$sql .= " and p.product_id='".$data["filter_name_id"]."' ";
		}
		if (!empty($data['filter_store'])) {
                        if (!empty($data['filter_name_id'])) {
                            $sql .= " and o.store_id='".$data["filter_store"]."' ";
                        }
                        else
                        {
                            $sql .= " and  o.store_id='".$data["filter_store"]."' ";
                        }
			
		}

		$sql.=" and o.order_status_id=5 ";


                $sql.=" GROUP by p.product_id,o.store_id) as aa ";

		$query = $this->db->query($sql);
                            //echo $sql;
		return $query->row;*/
            $groupby=array(
                   "_id"=>array("store"=> '$store_id'),  
                   "total_amount"=> array('$sum'=> '$total'),
                  // "quantity"=> array('$sum'=> '$quantity') ,
                  //"total" =>array('$sum'=>'$num_rows'),
                    "total"=> array('$sum'=> 1 )
                 );
                 
                    $query = $this->db->query('gettotalsum','oc_order',$groupby,$match);
                    //print_r($query->row);
                    
             return $query->row[0]; 

                            //return 0;
	}

	public function getSalesCompanyWise($data = array()) {


        
                $sql="select sum(tt.quantity) as quantity,(sum(tt.total)+sum(tt.ttax)) as total,tt.name,tt.store_name,tt.product_id from
                ( select sum(p.quantity) as quantity,sum(p.total) as total,(p.tax) as tax,(p.tax*p.quantity)as ttax,o.store_name,p.product_id,p.name
               from oc_order_product p left JOIN oc_order o on o.order_id=p.order_id
                LEFT JOIN oc_store as os on os.store_id = o.store_id ";

               $sql.="  where date(p.ORD_DATE)<= '".$data["filter_date_end"]."' ";
                $sql .= " AND os.company_id='".$data['filter_company']."' ";
       
               if (!empty($data['filter_date_start'])) {
            $sql .= " and date(p.ORD_DATE)>='".$data["filter_date_start"]."' ";
        }

        
                if (!empty($data['filter_name_id'])) {
            $sql .= " and p.product_id='".$data["filter_name_id"]."' ";
        }
	$sql.=" and o.order_status_id=5 ";
        if (!empty($data['filter_store'])) {
                        if (!empty($data['filter_name_id'])) {
                            $sql .= " and o.store_id='".$data["filter_store"]."' ";
                        }
                        else
                        {
                            $sql .= " and  o.store_id='".$data["filter_store"]."' ";
                        }
            
        }

        


                $sql.=" GROUP by p.product_id,o.store_id,p.order_id ) as tt GROUP by tt.product_id,tt.store_name ";



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

        
    public function getTotalsalesCompanyWise($data) {

$sql="select count(*) as total,sum(total) as total_amount from ( select sum(tt.quantity) as quantity,(sum(tt.total)+sum(tt.ttax)) as total,tt.name,tt.store_name,tt.product_id from
                ( select sum(p.quantity) as quantity,sum(p.total) as total,(p.tax) as tax,(p.tax*p.quantity)as ttax,o.store_name,p.product_id,p.name
               from oc_order_product p left JOIN oc_order o on o.order_id=p.order_id
                 LEFT JOIN oc_store as os on os.store_id = o.store_id";

               $sql.="  where date(p.ORD_DATE)<= '".$data["filter_date_end"]."' ";
               $sql .= " AND os.company_id='".$data['filter_company']."' ";
               
               if (!empty($data['filter_date_start'])) {
            $sql .= " and date(p.ORD_DATE)>='".$data["filter_date_start"]."' ";
        }

        
                if (!empty($data['filter_name_id'])) {
            $sql .= " and p.product_id='".$data["filter_name_id"]."' ";
        }
        if (!empty($data['filter_store'])) {
                        if (!empty($data['filter_name_id'])) {
                            $sql .= " and o.store_id='".$data["filter_store"]."' ";
                        }
                        else
                        {
                            $sql .= " and  o.store_id='".$data["filter_store"]."' ";
                        }
            
        }

        $sql.=" and o.order_status_id=5 ";


                $sql.=" GROUP by p.product_id,o.store_id,p.order_id ) as tt GROUP by tt.product_id,tt.store_name ) as aa ";

        $query = $this->db->query($sql);
                            //echo $sql;
        return $query->row;

                            //return 0;
    }

	  public function getSalescountCompanyWise($data = array()) {


		
                $sql="select sum(case when o.payment_method='Cash' then p.quantity else 0 end)as qnty_of_cash, sum(case when o.payment_method='Tagged' then p.quantity else 0 end)qnty_of_tagged,
 sum(case when o.payment_method='Tagged Cash' then p.quantity else 0 end)as qnty_of_tagged_cash,
  sum(case when o.payment_method='Subsidy' then p.quantity else 0 end)as qnty_of_Subsidy,o.store_name,p.product_id, p.name from oc_order_product p
  left JOIN oc_order o on o.order_id=p.order_id 
      left join oc_store as os on os.store_id = o.store_id";

               $sql.="  where date(p.ORD_DATE)<= '".$data["filter_date_end"]."' ";
              
               if (!empty($data['filter_date_start'])) {
			$sql .= " and date(p.ORD_DATE)>='".$data["filter_date_start"]."' ";
		}

		
                if (!empty($data['filter_name_id'])) {
			$sql .= " and p.product_id='".$data["filter_name_id"]."' ";
		}
		if (!empty($data['filter_store'])) {
                        if (!empty($data['filter_name_id'])) {
                            $sql .= " and o.store_id='".$data["filter_store"]."' ";
                        }
                        else
                        {
                            $sql .= " and  o.store_id='".$data["filter_store"]."' ";
                        }
			
		}
                
$sql.=" and o.order_status_id=5 ";
		
                $sql .= " AND os.company_id='".$data['filter_company']."' ";

                $sql.=" GROUP by p.product_id,o.store_id ";//,p.order_id
 


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



	public function getTotalsalescountCompanyWise($data) {

$sql="select count(*) as total from ( select o.store_name from oc_order_product p left JOIN oc_order o on o.order_id=p.order_id 
        left join oc_store as os on os.store_id = o.store_id";

               $sql.="  where date(p.ORD_DATE)<= '".$data["filter_date_end"]."' ";
                $sql .= " AND os.company_id='".$data['filter_company']."' ";
               if (!empty($data['filter_date_start'])) {
			$sql .= " and date(p.ORD_DATE)>='".$data["filter_date_start"]."' ";
		}

		
                if (!empty($data['filter_name_id'])) {
			$sql .= " and p.product_id='".$data["filter_name_id"]."' ";
		}
		if (!empty($data['filter_store'])) {
                        if (!empty($data['filter_name_id'])) {
                            $sql .= " and o.store_id='".$data["filter_store"]."' ";
                        }
                        else
                        {
                            $sql .= " and  o.store_id='".$data["filter_store"]."' ";
                        }
			
		}

		


                $sql.=" GROUP by p.product_id,o.store_id) as aa ";

		$query = $this->db->query($sql);
                           // echo $sql;
		return $query->row;

                            //return 0; 
	}
}
