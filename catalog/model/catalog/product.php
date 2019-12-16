<?php
class ModelCatalogProduct extends Model 
{
	public function getProductmanufacturer($data)
	{
		$where=array(
                            'manufacturer_id'=>(int)$data['manufacturer_id']
                            
                             );
		$query = $this->db->query("select",DB_PREFIX . "manufacturer",'','','',$where);
		return $query->row['name'];
	}
	
	public function getProductBookmark($data)
	{
		$where=array(
                            'product_id'=>(int)$data['product_id'],
                            'store_id'=> (int)$data['store_id']
                             );
		$query = $this->db->query("select",DB_PREFIX . "product_to_store",'','','',$where);
		return $query->row['bookmark'];
	}
	public function getProductBookmarkCount($data)
	{
		$where=array(
                            'product_id'=>(int)$data['product_id'],
                            'bookmark'=> (int)1
                             );
		$query = $this->db->query("select",DB_PREFIX . "product_to_store",'','','',$where);
		return $query->num_rows;
	}
	public function getProductReview($data)
	{
		$where=array(
                            'product_id'=>(int)$data['product_id'],
                            'store_id'=> (int)$data['store_id']
                             );
		$query = $this->db->query("select",DB_PREFIX . "product_rating",'','','',$where);
		return $query->row['rating'];
	}
	public function getProductReviewCount($data)
	{
		$where=array(
                            'product_id'=>(int)$data['product_id']
                             );
		$query = $this->db->query("select",DB_PREFIX . "product_rating",'','','',$where);
		$rating_sum=0;
		foreach($query->rows as $row)
		{
			$rating_sum=$rating_sum+$row['rating'];
		}
		$rating_avg=$rating_sum/($query->num_rows);
		return $rating_avg;
	}
	public function getProductReward($data)
	{
        $log =new Log("prdinv-reward-".date('Y-m-d').".log");
		$where=array(
                            'product_id'=>(int)$data['product_id'],
                            'store_id'=>(int)$data['store_id']
                             );
                $log->write(json_encode($where));
		$query = $this->db->query("select",DB_PREFIX . "product_reward",'','','',$where);
		
		$log->write($query);
                if(($query->row['valid_till']->sec)>=(strtotime(date('Y-m-d'))))
                {
                    return $query->row['points'];
                }
                else
                {
                    return 0;
                }
	}
	public function getProductRewardForAll($data)
	{
        $log =new Log("prdinv-reward-".date('Y-m-d').".log");
		$where=array(
                            'product_id'=>(int)$data['product_id']
                            
                             );
		//echo $data['product_id'];
        $log->write(json_encode($where));
		$query = $this->db->query("select",DB_PREFIX . "product_reward",'','','',$where);
		
		$log->write($query);
		foreach($query->rows as $row)
		{
			if(($row['valid_till']->sec)>=(strtotime(date('Y-m-d'))))
            {
                $return_points=$query->row['points'];
            }
            else
            {
                $return_points= 0;
            }
		}
		return $return_points;
	}
	public function getProductRewardByPid($data)
	{
        $log =new Log("prdinv-reward-".date('Y-m-d').".log");
		$where=array(
                            'product_id'=>(int)$data['product_id']
                            
                             );
		$where['valid_till']=array('$gte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime(date('Y-m-d'))))  )));
        $log->write(json_encode($where));
		$query = $this->db->query("select",DB_PREFIX . "product_reward",'','','',$where);
		
		$log->write($query);
        return $query->rows;
                
	}
	public function getProduct($product_id) 
    {
            $log =new Log("prdinv-getproduct-".date('Y-m-d').".log");
            
            $log->write($product_id);
            if(empty($product_id))
            {
                return false;
                
            }
            $match=array('product_id'=>(int)$product_id,'opst.store_id'=>(int)$this->config->get('config_store_id'));  //,'status'=>true         
            $lookup=array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'opst'  
                
                );
            $matchcount=array('product_id'=>(int)$product_id);   //,'status'=>true        
            $log->write(json_encode($match ));
            $log->write( json_encode($matchcount ));
            $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'$opst',$match,'','','','','','','','',$matchcount);
            $log->write( $query );
            if ($query->num_rows) 
            {
				
				$log->write("in");
                $query->row=$query->row[0];
				$log->write( $query->row );
				$mquery = $this->db->query("select",DB_PREFIX . "manufacturer",'','','',array('manufacturer_id'=>(int)$query->row['manufacturer_id']));
                //print_r($query->row);exit;
                    return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'HSTN'             => $query->row['HSTN'],
				'description'      => $query->row['product_description'][1]['description'],
				'meta_title'       => $query->row['product_description'][1]['meta_title'],
				'meta_hindi'    => $query->row['product_description'][1]['meta_hindi'],
				'meta_description' => $query->row['product_description'][1]['meta_description'],
				'meta_keyword'     => $query->row['product_description'][1]['meta_keyword'],
				'tag'              => $query->row['product_description'][1]['tag'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'ean'              => $query->row['ean'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'fquantity'         => $query->row['fquantity'],
				'squantity'         => $query->row['opst']['quantity'],
				'stock_status'     => $query->row['stock_status'],
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $mquery->row['name'],
				'price'            => ($query->row['price'] ? $query->row['price'] : $query->row['price']),
				'sprice'            => ($query->row['store_price'] ? $query->row['store_price'] : $query->row['opst']['store_price']),
				'subsidy'          => $query->row['subsidy'],
				'special'          => $query->row['special'],
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'subtract'         => $query->row['subtract'],
				'rating'           => round($query->row['rating']),
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'category_name'           => $query->row['category_name'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed']
			);
		} else {
			return false;
		}
	}
        public function getProductOpen($product_id,$billtype,$category_id) 
        {
            $log =new Log("prdinv-".date('Y-m-d').".log");
            
			$log->write('getProductOpen called');
            $log->write($product_id);
			$log->write($billtype);
			$log->write($category_id);
            if(empty($product_id))
            {
                return false;
                
            }
            if($billtype==1)
            {
                $match=array('product_id'=>(int)$product_id,'opst.store_id'=>(int)0,'status'=>true);           
            }
            else 
            {
				if($category_id==44)
				{
					$match=array('product_id'=>(int)$product_id,'opst.store_id'=>(int)$this->config->get('config_store_id')); 
				}
				else
				{
					$match=array('product_id'=>(int)$product_id,'opst.store_id'=>(int)$this->config->get('config_store_id'),'status'=>true);  
				}					
            }
            
            $lookup=array(
                  'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'opst'  
                
                );
            $matchcount=array('product_id'=>(int)$product_id,'status'=>true);           
            $log->write( $match );
            $log->write( $matchcount );
            $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'$opst',$match,'','','','','','','','',$matchcount);
            $log->write( $query );
            if ($query->num_rows) 
            {
                $log->write("in");
                $query->row=$query->row[0];
                    return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'HSTN'             => $query->row['HSTN'],
				'description'      => $query->row['product_description'][1]['description'],
				'meta_title'       => $query->row['product_description'][1]['meta_title'],
				'meta_hindi'    => $query->row['product_description'][1]['meta_hindi'],
				'meta_description' => $query->row['product_description'][1]['meta_description'],
				'meta_keyword'     => $query->row['product_description'][1]['meta_keyword'],
				'tag'              => $query->row['product_description'][1]['tag'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'ean'              => $query->row['ean'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'fquantity'         => $query->row['fquantity'],
				'squantity'         => $query->row['opst']['quantity'],
				'stock_status'     => $query->row['stock_status'],
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'sprice'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['opst']['store_price']),
				'subsidy'          => $query->row['subsidy'],
				'special'          => $query->row['special'],
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'subtract'         => $query->row['subtract'],
				'rating'           => round($query->row['rating']),
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed']
			);
		} else {
			return false;
		}
	}
	public function getProductOpenUnVerified($product_id,$billtype) 
        {
            $log =new Log("prdinv-UnVerified-".date('Y-m-d').".log");
            
            $log->write($product_id);
            if(empty($product_id))
            {
                return false;
                
            }
           /* if($billtype==1)
            {
                $match=array('product_id'=>(int)$product_id,'opst.store_id'=>(int)0);           
            }
            else 
            {*/
                $match=array('product_id'=>(int)$product_id,'opst.store_id'=>(int)$this->config->get('config_store_id'));           
            //}
            
            $lookup=array(
                  'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id', 
                'as' => 'opst'  
                
                );
            $matchcount=array('product_id'=>(int)$product_id);           
            $log->write( $match );
            $log->write( $matchcount );
            $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'$opst',$match,'','','','','','','','',$matchcount);
            $log->write( $query );
            if ($query->num_rows) 
            {
                $log->write("in");
                $query->row=$query->row[0];
                    return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'HSTN'             => $query->row['HSTN'],
				'description'      => $query->row['product_description'][1]['description'],
				'meta_title'       => $query->row['product_description'][1]['meta_title'],
				'meta_hindi'    => $query->row['product_description'][1]['meta_hindi'],
				'meta_description' => $query->row['product_description'][1]['meta_description'],
				'meta_keyword'     => $query->row['product_description'][1]['meta_keyword'],
				'tag'              => $query->row['product_description'][1]['tag'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'ean'              => $query->row['ean'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'fquantity'         => $query->row['fquantity'],
				'squantity'         => $query->row['opst']['quantity'],
				'stock_status'     => $query->row['stock_status'],
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'sprice'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['opst']['store_price']),
				'subsidy'          => $query->row['subsidy'],
				'special'          => $query->row['special'],
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'subtract'         => $query->row['subtract'],
				'rating'           => round($query->row['rating']),
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed']
			);
		} else {
			return false;
		}
	}
    public function getProducts($data = array()) 
    {
        $log=new Log("prdinv-model-".date('Y-m-d').".log");
        $log->write('in model getProducts for '.$data['for_store']);
        $log->write($data);
        if (isset($data['start']) || isset($data['limit'])) 
        {
            if ($data['start'] < 0) 
            {
				$start = 0;
            }
            else
            {
                $start=(int)$data['start'];
            }
            if ($data['limit'] < 1) 
            {
				$limit = (int)20;
            }
            else 
            {
                $limit = (int)$data['limit'];
            }

			
		}
        $lookup=array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                );
				/*
				$lookup=array(array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                ),array(
                'from' => 'oc_product_to_category',
                'localField' => 'category_ids',
                'foreignField' => 'category_id',
                'as' => 'pc'
                ));
				*/
        $sort_array=array('name'=>1);
		if($data['filter_category_id']==44)
		{
			//$log->write("44 in");
			//$match=array('status'=>false);
		}
		else
		{
			if($data['for_store']!='inventory_report')
			{
				$match=array('status'=>true);
			}
		}
        if (!empty($data['filter_category_id'])) 
        {
            $match['category_ids']=$data['filter_category_id'];
        }
        if (!empty($data['filter_product_id'])) 
        {
            $match['product_id']=$data['filter_product_id'];
        }
        if (!empty($data['product_name'])) 
        {
            $search_string=$data['product_name'];
            $match['name'] = new MongoRegex("/.*$search_string/i");
        }
		if($data['filter_category_id']==44)
		{
			
				$storeid=$data['store_id'];
			
		}
		else
		{
			$storeid=$this->config->get('config_store_id');
		}
		//if(!empty($storeid))
		//{
        $match['pd.store_id']=(int)$storeid;
		//}
        if(!empty($storeid))
        {
			$log->write('in if store id not empty -'.$storeid);
			//$data['quantity_check']==1 ---> open
			if(empty($data['quantity_check'])&&($data['filter_category_id']==44))
			{}				
            if(!empty($data['quantity_check'])) 
            {
				$log->write('in if quantity check is  empty-'.$data['quantity_check']);
				if($data['filter_category_id']!=44)
				{
					$log->write('in if category id is not 44-'.$data['filter_category_id']);
					if(empty($data['invtype']))
					{
						//$match['pd.quantity']=array('$gt'=>0); 
					}
					else
					{
						//$match['pd.mitra_quantity']=array('$gt'=>0); 

					}
					//$match['$or']=array(array('pd.quantity'=>array('$gt'=>0)),array('pd.mitra_quantity'=>array('$gt'=>0)));
				}
				if($data['filter_category_id']==44 && (!empty($data['for_store'])))
				{
					$log->write('in if category id is  44-'.$data['filter_category_id'].' and for_store not empty ');
					if(empty($data['invtype']))
					{
						//$match['pd.quantity']=array('$gt'=>0); 
					}
					else
					{
						//$match['pd.mitra_quantity']=array('$gt'=>0); 

					}
					//$match['$or']=array(array('pd.quantity'=>array('$gt'=>0)),array('pd.mitra_quantity'=>array('$gt'=>0)));
				}
				
				
            }
			if(!empty($data['for_store']) && $data['for_store']=='inventory_report')
			{
				$log->write('in if  for_store is inventory_report ');
				if(empty($data['invtype']))
				{
					//$match['pd.quantity']=array('$gt'=>0); 
				}
				else
				{
					//$match['pd.mitra_quantity']=array('$gt'=>0); 
				}
				$match['$or']=array(array('pd.quantity'=>array('$gt'=>0)),array('pd.mitra_quantity'=>array('$gt'=>0)));
			}
			
        }
		
        //print_r($match);exit;
		$log->write('generated where in model');
        $log->write($match);
		$log->write(json_encode($match));
		$columns=array();
        $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'$pd',$match,'','',$limit,$columns,$start,$sort_array);
        foreach ($query->row as $result) 
		{	 
			$result['num_rows']=$query->num_rows;
            		$product_data[$result['product_id']] = $result;//$this->getProduct($result[0]['product_id']);

		}
        return $product_data;
    }
	public function getInventoryProducts($data = array()) 
    {
        $log =new Log("invproducts-".date('Y-m-d').".log");
        $log->write('in model getInventoryProducts for '.$data['service_type']);
        $log->write($data);
        if (isset($data['start']) || isset($data['limit'])) 
        {
            if ($data['start'] < 0) 
            {
				$start = 0;
            }
            else
            {
                $start=(int)$data['start'];
            }
            if ($data['limit'] < 1) 
            {
				$limit = (int)20;
            }
            else 
            {
                $limit = (int)$data['limit'];
            }
		}
        $lookup=array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                );
				
        $sort_array=array('name'=>1);
		$match=array();
		if (!empty($data['filter_product_id'])) 
        {
            $match['product_id']=$data['filter_product_id'];
        }
        if (!empty($data['product_name'])) 
        {
            $search_string=$data['product_name'];
            $match['name'] = new MongoRegex("/.*$search_string/i");
        }
		
        $match['pd.store_id']=(int)$this->config->get('config_store_id');
		
		if(!empty($data['service_type']) && $data['service_type']=='inventory')
		{
			$log->write('in if  service_type is inventory ');
			//$match['pd.quantity']=array('$gt'=>0);
		}
		if(!empty($data['service_type']) && $data['service_type']=='mitra_inventory')
		{
			$log->write('in if  service_type is mitra_inventory ');
			//$match['pd.mitra_quantity']=array('$gt'=>0); 
		}	
		
        //print_r($match);exit;
		$log->write('generated where in model');
        $log->write($match);
		$log->write(json_encode($match));
		$columns=array();
        $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'$pd',$match,'','',$limit,$columns,$start,$sort_array);
        foreach ($query->row as $result) 
		{	 
			$result['num_rows']=$query->num_rows;
            		$product_data[$result['product_id']] = $result;//$this->getProduct($result[0]['product_id']);

		}
        return $product_data;
    }
	public function getProductsPO($data = array()) 
    {
        $log =new Log("prdinv-po-".date('Y-m-d').".log");
        $log->write('in model getProductsPO for '.$data['for_store']);
        $log->write($data);
        if (isset($data['start']) || isset($data['limit'])) 
        {
            if ($data['start'] < 0) 
            {
				$start = 0;
            }
            else
            {
                $start=(int)$data['start'];
            }
            if ($data['limit'] < 1) 
            {
				$limit = (int)20;
            }
            else 
            {
                $limit = (int)$data['limit'];
            }

			
		}
        $lookup=array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                );
				
        $sort_array=array('name'=>1);
		$match=array('status'=>true);
		
        if (!empty($data['filter_category_id'])) 
        {
            $match['category_ids']=$data['filter_category_id'];
        }
        if (!empty($data['filter_product_id'])) 
        {
            $match['product_id']=$data['filter_product_id'];
        }
        if (!empty($data['product_name'])) 
        {
            $search_string=$data['product_name'];
            $match['name'] = new MongoRegex("/.*$search_string/i");
        }
		if($data['filter_category_id']==44)
		{
			
				$storeid=$data['store_id'];
			
		}
		else
		{
			$storeid=$this->config->get('config_store_id');
		}
		$match['pd.store_id']=(int)$storeid;
		$match['wholesale_price']=array('$gt'=>0); 
        
        //print_r($match);exit;
		$log->write('generated where in model');
        $log->write($match);
		$log->write(json_encode($match));
		$columns=array();
        $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'$pd',$match,'','',$limit,$columns,$start,$sort_array);
        foreach ($query->row as $result) 
		{	 
			$result['num_rows']=$query->num_rows;
            $product_data[$result['product_id']] = $result;//$this->getProduct($result[0]['product_id']);

		}
        return $product_data;
    }
	public function getProductsInv($data = array()) 
    {
        $log=new Log("prdinv-model-".date('Y-m-d').".log");
        $log->write('in model getProductsInv for '.$data['for_store']);
        $log->write($data);
        if (isset($data['start']) || isset($data['limit'])) 
        {
            if ($data['start'] < 0) 
            {
				$start = 0;
            }
            else
            {
                $start=(int)$data['start'];
            }
            if ($data['limit'] < 1) 
            {
				$limit = (int)20;
            }
            else 
            {
                $limit = (int)$data['limit'];
            }

			
		}
        $lookup=array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                );
				
        $sort_array=array('name'=>1);
		if($data['filter_category_id']==44)
		{
			//$log->write("44 in");
			//$match=array('status'=>false);
		}
		
        if (!empty($data['filter_category_id'])) 
        {
            $match['category_ids']=$data['filter_category_id'];
        }
        if (!empty($data['filter_product_id'])) 
        {
            $match['product_id']=$data['filter_product_id'];
        }
        if (!empty($data['product_name'])) 
        {
            $search_string=$data['product_name'];
            $match['name'] = new MongoRegex("/.*$search_string/i");
        }
		if($data['filter_category_id']==44)
		{
			
				$storeid=$data['store_id'];
			
		}
		else
		{
			$storeid=$this->config->get('config_store_id');
		}
		if(!empty($data['quantity_check']))
		{
			$match['pd.quantity']=array('$gt'=>0); 
		}
        $match['pd.store_id']=(int)$storeid;
		
		
        //print_r($match);exit;
		$log->write('generated where in model');
        $log->write($match);
		$log->write(json_encode($match));
        $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'$pd',$match,'','',$limit,$columns,$start,$sort_array);
        foreach ($query->row as $result) 
		{	 
			$result['num_rows']=$query->num_rows;
            		$product_data[$result['product_id']] = $result;//$this->getProduct($result[0]['product_id']);

		}
        return $product_data;
    }
	public function getProductsByManufacturer($data = array()) 
    {
        $log =new Log("products-dash-".date('Y-m-d').".log");
        $log->write('in model getProductsByManufacturer for '.$data['for_store']);
        $log->write($data);
        if (isset($data['start']) || isset($data['limit'])) 
        {
            if ($data['start'] < 0) 
            {
				$start = 0;
            }
            else
            {
                $start=(int)$data['start'];
            }
            if ($data['limit'] < 1) 
            {
				$limit = (int)20;
            }
            else 
            {
                $limit = (int)$data['limit'];
            }

			
		}
        $lookup=array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                );
				/*
				$lookup=array(array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                ),array(
                'from' => 'oc_product_to_category',
                'localField' => 'category_ids',
                'foreignField' => 'category_id',
                'as' => 'pc'
                ));
				*/
        $sort_array=array('name'=>1);
		if(@$data['filter_category_id']==44)
		{
			//$log->write("44 in");
			//$match=array('status'=>false);
		}
		else
		{
			if($data['for_store']!='inventory_report')
			{
				$match=array('status'=>true);
			}
		}
        if (!empty($data['manufacturer_id'])) 
        {
            $match['manufacturer_id']=(int)$data['manufacturer_id'];
        }
        if (!empty($data['filter_product_id'])) 
        {
            $match['product_id']=$data['filter_product_id'];
        }
        if (!empty($data['product_name'])) 
        {
            $search_string=$data['product_name'];
            $match['name'] = new MongoRegex("/.*$search_string/i");
        }
		if(@$data['filter_category_id']==44)
		{
			
				$storeid=$data['store_id'];
			
		}
		else
		{
			$storeid=$this->config->get('config_store_id');
		}
        //$match['pd.store_id']=(int)$storeid;
        if(!empty($storeid))
        {
			$log->write('in if store id not empty -'.$storeid);
			//$data['quantity_check']==1 ---> open
			if(empty($data['quantity_check'])&&($data['filter_category_id']==44))
			{}				
            if(!empty($data['quantity_check'])) 
            {
				$log->write('in if quantity check is  empty-'.$data['quantity_check']);
				if(@$data['filter_category_id']!=44)
				{
					//$log->write('in if category id is not 44-'.$data['filter_category_id']);
					//$match['pd.quantity']=array('$gt'=>0); 
				}
				if(@$data['filter_category_id']==44 && (!empty($data['for_store'])))
				{
					//$log->write('in if category id is not 44-'.$data['filter_category_id']);
					//$match['pd.quantity']=array('$gt'=>0); 
				}
				
				
            }
			if($data['for_store']=='inventory_report')
			{
				//$match['pd.quantity']=array('$gt'=>0); 
			}
			
        }
		//$match['product_id']=(int)(153); 
		//$limit=1;
        //print_r($match);exit;
		$log->write('generated where in model');
        $log->write($match);
		$log->write(json_encode($match));
		
		$log->write('start');
        $log->write(@$start);
		
		$log->write('limit');
        $log->write(@$limit);
		$columns=array();
        $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'$pd',$match,'','',(int)$limit,$columns,(int)$start,$sort_array);
        $log->write($query->num_rows);
        foreach ($query->row as $result) 
		{	 
			$result['num_rows']=$query->num_rows;
            		$product_data[$result['product_id']] = $result;//$this->getProduct($result[0]['product_id']);

		}
        return $product_data;
    }
	public function promotedproducts($data = array()) 
    {
        $log=new Log("promotedproducts-".date('Y-m-d').".log");
        $log->write('in model promotedproducts for '.$data['for_store']);
        $log->write($data);
        if (isset($data['start']) || isset($data['limit'])) 
        {
            if ($data['start'] < 0) 
            {
				$start = 0;
            }
            else
            {
                $start=(int)$data['start'];
            }
            if ($data['limit'] < 1) 
            {
				$limit = (int)20;
            }
            else 
            {
                $limit = (int)$data['limit'];
            }
		}
        $lookup=array(
                'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd');
				
        $sort_array=array('name'=>1);
		
		$match=array('status'=>true);
			
		if (!empty($data['promotion_start_date'])) 
		{
            
			$sdate=$this->db->escape($data['promotion_start_date']);
        }
		if (!empty($data['promotion_end_date'])) 
		{
            
			$edate=$this->db->escape($data['promotion_end_date']);
        } 
		
		//$match['promotion_start_date']=array('$gte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('-2 day', strtotime($sdate)))  )));
		$match['promotion_end_date']=array('$gte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('1 day', strtotime($edate))) )));
        
        if (!empty($data['product_name'])) 
        {
            $search_string=$data['product_name'];
            $match['name'] = new MongoRegex("/.*$search_string/i");
        }
		//$storeid=$this->config->get('config_store_id');
		
        //$match['pd.store_id']=(int)$data['store_id'];
        
		//print_r($match);
		//exit;
		$log->write('generated where in model');
        $log->write($match);
		$log->write(json_encode($match));
        $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'$pd',$match,'','',$limit,$columns,$start,$sort_array);
        foreach ($query->row as $result) 
		{	 
			$result['num_rows']=$query->num_rows;
            $product_data[$result['product_id']] = $result;//$this->getProduct($result[0]['product_id']);

		}
        return $product_data;
    }
	public function getProductSpecials($data = array()) 
	{
		$sql = "SELECT DISTINCT ps.product_id, (SELECT AVG(rating) FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) GROUP BY ps.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'ps.price',
			'rating',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$product_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getLatestProducts($limit) {
		$product_data = $this->cache->get('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$product_data) {
			$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.date_added DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}

			$this->cache->set('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getPopularProducts($limit) 
	{
		$product_data = array();

		$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.viewed DESC, p.date_added DESC LIMIT " . (int)$limit);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getBestSellerProducts($limit) 
	{
		$product_data = $this->cache->get('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$product_data) {
			$product_data = array();

			$query = $this->db->query("SELECT op.product_id, SUM(op.quantity) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}

			$this->cache->set('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getProductAttributes($product_id) 
	{
		$product_attribute_group_data = array();

		$product_attribute_group_query = $this->db->query("SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int)$product_id . "' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");

		foreach ($product_attribute_group_query->rows as $product_attribute_group) {
			$product_attribute_data = array();

			$product_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND a.attribute_group_id = '" . (int)$product_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");

			foreach ($product_attribute_query->rows as $product_attribute) {
				$product_attribute_data[] = array(
					'attribute_id' => $product_attribute['attribute_id'],
					'name'         => $product_attribute['name'],
					'text'         => $product_attribute['text']
				);
			}

			$product_attribute_group_data[] = array(
				'attribute_group_id' => $product_attribute_group['attribute_group_id'],
				'name'               => $product_attribute_group['name'],
				'attribute'          => $product_attribute_data
			);
		}

		return $product_attribute_group_data;
	}

	public function getProductOptions($product_id) 
	{
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'name'                    => $product_option_value['name'],
					'image'                   => $product_option_value['image'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}

	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity > 1 AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");

		return $query->rows;
	}

	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getProductRelated($product_id) {
		$product_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.product_id = '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$product_data[$result['related_id']] = $this->getProduct($result['related_id']);
		}

		return $product_data;
	}

	public function getProductLayoutId($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getCategories($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

	public function getTotalProducts($data = array()) {
$log=new Log("prdcount.log");
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$sql .= "pd.tag LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tag'])) . "%'";
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
		$log->write($sql);
		$query = $this->db->query($sql);

		return $query->row['total'];
	}


        public function getTotalQntyProducts($data = array()) 
        {
			
			$match=array();
		
			if (!empty($data['product_name'])) 
			{
            $search_string=$data['product_name'];
            $match['name'] = new MongoRegex("/.*$search_string/i");
			}
			$match['quantity']=array('$gt'=>0); 
        
            $match['store_id']=(int)$this->config->get('config_store_id');
            if(!empty($data['filter_product_id']))
            {
              $match['product_id']= (int)$data['filter_product_id']; 
            }
			$query = $this->db->getcount("oc_product_to_store",$match);

			return $query;
	}


	public function getTotalInventoryAmount( $data)
    {
        $match=array('store_id'=>(int)$data['store_id']);
		
		$match['$or']=array(array('quantity'=>array('$gt'=>0)),array('mitra_quantity'=>array('$gt'=>0)));
        if (!empty($data['product_name'])) 
        {
            $search_string=$data['product_name'];
            $match['name'] = new MongoRegex("/.*$search_string/i");
                  
	}
        if (!empty($data['filter_product_id'])) 
        {
            
            $match['product_id'] = (int)$data['filter_product_id'];
                  
	}
        $log =new Log("prdinv-".date('Y-m-d').".log");
		$log->write('in model for getTotalInventoryAmount');
		$log->write('generated where ');
		$log->write($match);
        $query =$this->db->query('select','oc_product_to_store','','','',$match);
        //$log->write($query->rows);
        return array_sum(array_map(function($element)
        {
			if($element['store_price']=='0.0')
			{
				$prd=$this->getprd($element['product_id']);
				$element['total']=$element['quantity']*($prd['price_tax']);
			}
			else
			{
				$element['total']=$element['quantity']*($element['store_price']+$element['store_tax_amt']);
			}
        	
		
			$log =new Log("invproducts-".date('Y-m-d').".log");
			//$log->write($element);
        	return $element['total'];
        
        }, $query->rows)
        );

}
	public function getTotal_mitra_InventoryAmount( $data)
    {
        $match=array('store_id'=>(int)$data['store_id']);
		
		//$match['$or']=array(array('quantity'=>array('$gt'=>0)),array('mitra_quantity'=>array('$gt'=>0)));
		$match['mitra_quantity']=array('$gt'=>0);
        if (!empty($data['product_name'])) 
        {
            $search_string=$data['product_name'];
            $match['name'] = new MongoRegex("/.*$search_string/i");
                  
	}
        if (!empty($data['filter_product_id'])) 
        {
            
            $match['product_id'] = (int)$data['filter_product_id'];
                  
	}
        $log =new Log("invproducts-".date('Y-m-d').".log");
		$log->write('in model for getTotalInventoryAmount');
		$log->write('generated where ');
		$log->write($match);
        $query =$this->db->query('select','oc_product_to_store','','','',$match);
        //$log->write($query->rows);
        return array_sum(array_map(function($element)
        {
			if($element['store_price']=='0.0')
			{
				$prd=$this->getprd($element['product_id']);
				
				$element['mitra_total']=$element['mitra_quantity']*($prd['price_tax']);
			}
			else
			{
				/*
				$pirce=($this->tax->calculate($element['store_price'], 
					$prd['tax_class_id'], $this->config->get('config_tax'))+
					($this->tax->getTax($element['store_price'], $prd['tax_class_id'])));
					
				*/
				$element['mitra_total']=$element['mitra_quantity']*($element['store_price']+$element['store_tax_amt']);
			}
		
		//$log =new Log("prdinv-".date('Y-m-d').".log");
		//$log->write($element['total']);
        return $element['mitra_total'];
        
        }, $query->rows)
        );

	}
	
	private function getprd($product_id)
	{
		$match['product_id'] = (int)$product_id;
		$query =$this->db->query('select','oc_product','','','',$match);
		return $query->row;
	}


}
