<?php
class ModelCatalogUnnatimitra extends Model 
{
	public function getlist($data = array()) 
	{
            $lookup=array(
                        array(
                            'from' => 'oc_product_to_store',
                            'localField' => 'product_id',
                            'foreignField' => 'product_id',
                            'as' => 'pd'
                        ),
						array(
                            'from' => 'oc_store',
                            'localField' => 'store_id',
                            'foreignField' => 'store_id',
                            'as' => 'st'
                        ),
						array(
                            'from' => 'oc_product',
                            'localField' => 'product_id',
                            'foreignField' => 'product_id',
                            'as' => 'pd2'
                        )
                    );
			$lookupwithunwind=array(
                        array('lookup'=>
                                array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
            ),
                                'unwind'=>'$pd'
                        ),
                        array('lookup'=>
                                array(
                'from' => 'oc_store',
                'localField' => 'store_id',
                'foreignField' => 'store_id',
                'as' => 'st'
            ),
                                'unwind'=>'$st'
                        )
                    );
			//$lookup='';
			$lookupwithunwind='';
            $match=array();
            if (!empty($data['store_id'])) 
            {
                $match['store_id']= (int)$data['store_id'];
                $match['pd.store_id']= (int)$data['store_id'];
            }
			$match['valid_till']=array('$gte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime(date('Y-m-d'))))  )));
            //$sort_array=array('pd.product_id'=>1);
            $group=array();
            $query = $this->db->query("join",DB_PREFIX . "product_reward",$lookup,'',$match,'','',2000,'',0,$sort_array,$lookupwithunwind,$group);
            //print_r($query->rows);exit;
            return $query;
	}
	public function getStatement($data = array()) 
	{
            $match=array('points'=>array('$ne'=>'0'));
            if (!empty($data['customer_id'])) 
            {
                $match['customer_id']= (int)$data['customer_id'];
            }
           
            $sort_array=array('order_id'=>-1);
            $group=array();
            if(isset($data['start']))
            {
                $start=(int)$data['start'];
            }
            else
            {
                $start=0;
            }
            if(isset($data['limit']))
            {
                $limit=(int)$data['limit'];
            }
            else
            {
                $limit=200000000;
            }
            $query = $this->db->query("select",DB_PREFIX . "customer_reward",'','','',$match,'',(int)$limit,'',(int)$start,$sort_array,'');
            //print_r($query);exit;
            return $query;
	}

}