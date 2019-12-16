<?php
class ModelPosInventory extends Model {
	
	public function getsale($uid,$sdate,$edate)
	{
            
            $log=new Log("mysale-".date('Y-m-d').".log");
            $log->write("mysale query call");
            $log->write($uid);
            $log->write($sdate);
            $log->write($edate);
            $log->write(date('Y-m-d', strtotime('0 day', strtotime($sdate))) ); 
            $log->write(date('Y-m-d', strtotime('1 day', strtotime($edate))) ); 
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
            
            
                $match=array('user_id'=>(int)$uid,
                    'payment_method'=>'Cash',
                    'order_status_id'=>5,
                     'date_added'=>$datedata
                    ); 
                //,'$lte'=>new MongoDate(strtotime($edate))
                $unwind=array('$order_product','$date_added');
                $group[]=array(
                    "_id" =>('$order_product.product_id'),
                    "quantity" =>array('$sum'=>'$order_product.quantity'),
                    "product_name" =>array('$first'=>'$order_product.name'),
                    "paytype"=>array('$push'=>array('Price'=>'$order_product.price',"Tax"=> '$order_product.tax',"Qnty"=> '$order_product.quantity'))
                    
					);
                $log->write('match');
                $log->write($match);
                
                $query=$this->db->query('join','oc_order','',$unwind,$match,'','','','',0,$sort,'',$group);
                //print_r($query->rows);	exit;
		return $query;
		
		}
                
                
            public function gettodaysales_cash_tageed_subsidy($today_date,$enddate,$store_id) 
            {
                $sdate=$today_date;
                $edate=$enddate;
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
                $match=array('order_status_id'=>5,'store_id'=>(int)$store_id,'date_added'=>$datedata);
                
                    $group=array();
                    $group[]=array(
                    "_id" =>array('store'=>'$store_id',"payment"=>'$payment_method'),
                    "total" =>array('$sum'=>'$total'),
                    "credit" =>array('$sum'=>'$credit'),
                    "cash"=> array('$sum'=>'$cash'),
					"discount"=> array('$sum'=>'$discount'),
                    "count"=> array('$sum'=>1));
               
               
                $group[]=array("_id"=>'$_id.store',"paytype"=>
                    array('$push'=>array("type"=>'$_id.payment',
                    "amount"=>'$total',
                    "credit"=> '$credit',
                    "cash"=> '$cash',
					'discount'=>'$discount',
                    "ocount"=>'$count'
                    )),"ctotal"=>array('$sum'=>'$total'));
                
                $sort=array("ctotal"=>-1);           
                $query=$this->db->query('join','oc_order','','',$match,'','',20,'',0,$sort,'',$group);
                
                $return_array=array();
                $return_array['total']=$query->row[0]['ctotal'];
				$return_array['discount']=$query->row[0]['paytype'][0]['discount'];
                $return_array['cash']=$query->row[0]['paytype'][0]['cash'];
                $return_array['credit']=$query->row[0]['paytype'][0]['credit'];
                $return_array['ototal']=$query->num_rows;
                return $return_array;
	}
	
}