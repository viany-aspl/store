<?php
class ModelCatalogProductTemp extends Model {

        public function approved($p_id)
        {
          
            $fdata=array('status'=>1);
            //$match=array('product_id'=>(int)$p_id);
            $query = $this->db->query('update',DB_PREFIX.'product_temp',array('product_id'=>(int)$p_id),$fdata);
            return $query;
            
        }
        
        public function pending($p_id,$data)
        {
        
            $log=new Log("updateproduct-".date('Y-m-d').".log");
             unset($data['product_id']);
             $serialize_data=serialize($data);
             $input_array=array('remark'=>$serialize_data,
                 'status'=>(int)2
                 );
             $query = $this->db->query('update',DB_PREFIX .'product_temp',array('product_id'=>(int)$p_id),$input_array);
             $log->write('update');
             $log->write($query);
        }
        
        
        public function send_data($p_id)
        { 
           $log=new Log("updateproduct-".date('Y-m-d').".log");
                      $lookup=array(array(
                'from' => 'oc_product_description_temp',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                 'as' => 'opdt'
                  ),array(
                    'from' => 'oc_product_to_category_temp',
                    'localField' => 'product_id',
                    'foreignField' => 'product_id',
                    'as' => 'opct'
                )
                ); 
                 $columns=array(
                    "_id"=> 1,
                     "HSTN"=>1,
                     "model"=> 1,
                     " price"=>1,
                     "tax_class_id"=>1,
                     "image"=>1,
                     "sku"=>1,
                     "upc"=>1,
                     "ean"=>1,
                     "jan"=>1,
                     "isbn"=>1,
                     "mpn"=>1,
                     "quantity"=>1,
                     "location"=>1,
                    " status"=>1,
                     "company_name"=>1,
                     "stock_status_id"=>1,
                    " manufacturer_id"=>1,
                     "weight"=>1,
                     "weight_class_id"=>1,
                     "length"=>1,
                     "width"=>1,
                     "height"=>1,
                     "length_class_id"=>1,
                     "subtract"=>1,
                     "minimum"=>1,
                     "sort_order"=>1,
                     "viewed"=>1,
                     "date_modified"=>1,
                     "date_available"=>1,
                     "purchase_price"=>1,
                     "wholesale_price"=>1,
                     "shipping"=>1,
                     "points"=>1,
                     "category_id"=>1,
                     "date_added" =>1,
                     "opdt.product_id"  =>1,
                     "opdt.language_id"  =>1,
                     "opdt.name"  =>1,
                     "opdt.description"  =>1,
                     "opdt.tag"  =>1,
                     "opdt.meta_title"  =>1,
                     "opdt.meta_description"  =>1,
                     "opdt.meta_keyword"  =>1,
                     "opdt.meta_hindi"    =>1,
                     "opct.product_id"  =>1,
                     "opct.category_id"    =>1
                );     
                      
               $match=array('product_id'=>(int)$p_id);
               $query = $this->db->query('join','oc_product_temp',$lookup,'',$match,'','','',$columns);
               return $query->row;
            
                    }
        
      
        
         public function insert_send_data($senddata,$p_id)
         { 
             
             ///print_r($senddata);
             //exit;
        
             $log=new Log("addproduct_main-".date('Y-m-d').".log");
             $last_id=$this->db->getNextSequenceValue('oc_product');
          
          $fdata=array('HSTN'=>$senddata[0]['HSTN'],
              'product_id'=>$last_id,
             'name'=>$senddata[0]['model'],
              'model'=>$senddata[0]['model'],
              'price'=>$senddata[0]['price'],
              'tax_class_id'=>$senddata[0]['tax_class_id'],
              'image'=>$senddata[0]['image'],
              'sku'=>$senddata[0]['sku'],
              'upc'=>$senddata[0]['upc'],
              'ean'=>$senddata[0]['ean'],
              'jan'=>$senddata[0]['jan'],
              'isbn'=>$senddata[0]['isbn'],
              'mpn'=>$senddata[0]['mpn'],
              'quantity'=>$senddata[0]['quantity'],
              'location'=>$senddata[0]['location'],
              'status'=>true,
              'company_name'=>$senddata[0]['company_name'],
              'stock_status_id'=>$senddata[0]['stock_status_id'],
              'manufacturer_id'=>$senddata[0]['manufacturer_id'],
              'weight'=>$senddata[0]['weight'],
              'weight_class_id'=>$senddata[0]['weight_class_id'],
              'length'=>$senddata[0]['length'],
              'width'=>$senddata[0]['width'],
              'height'=>$senddata[0]['height'],
              'length_class_id'=>$senddata[0]['length_class_id'],
              'subtract'=>$senddata[0]['subtract'],
              'minimum'=>$senddata[0]['minimum'],
              'sort_order'=>$senddata[0]['sort_order'],
              'viewed'=>$senddata[0]['viewed'],
              'date_modified'=>new MongoDate(strtotime(date('Y-m-d'))),
              'date_available'=>new MongoDate(strtotime(date('Y-m-d'))),
               'purchase_price'=>$senddata[0]['purchase_price'],
               'wholesale_price'=>$senddata[0]['wholesale_price'],
               'shipping'=>$senddata[0]['shipping'],
               'points'=>$senddata[0]['points'],
               'category_ids'=>array((string)$senddata[0]['category_id']),
               'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
              );
             $log->write($fdata);
             $query = $this->db->query('insert', DB_PREFIX . 'product',$fdata);
             $log->write($query); 
            
               $fdata1=array('language_id'=>$senddata[0]['opdt'][0]['language_id'],
              'product_id'=>(int)$last_id,
             
              'name'=>$senddata[0]['opdt'][0]['name'],
              'description'=>$senddata[0]['opdt'][0]['description'],
              'tag'=>$senddata[0]['opdt'][0]['tag'],
              'meta_title'=>$senddata[0]['opdt'][0]['meta_title'],
              'meta_description'=>$senddata[0]['opdt'][0]['meta_description'],
              'meta_keyword'=>$senddata[0]['opdt'][0]['meta_keyword'],
                   );
                 $query = $this->db->query('insert', DB_PREFIX . 'product_description',$fdata1);
                 
              
                    // $fdata4=array('user_id'=>$last_id);
               //  $match=array('product_id'=>(int)$p_id);
                 // $query = $this->db->query('update',DB_PREFIX .'product_temp',$match,$fdata4);
                 
                 
                  $fdata2=array('product_id'=>$last_id,
                     'category_id'=>$senddata[0]['opct'][0]['category_id'],
                     ); 
                 
                  $query = $this->db->query('insert', DB_PREFIX . 'product_to_category',$fdata2);
				  
				$store_product_array=array(
							"product_id" =>(int)$last_id,
							"store_id" =>0,
							"quantity" =>0,
							"store_price" =>0,
							"store_tax_type" =>0,
							"store_tax_amt"=>0,
							"MOD_DATE" =>new MongoDate(strtotime(date('Y-m-d h:i:d')))
						);
				$query = $this->db->query('insert', DB_PREFIX . 'product_to_store',$store_product_array); 
                 return $last_id;
        
        }
        
        
        
        public function deleteProduct($product_id) {
		

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_temp WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description_temp WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category_temp WHERE product_id = '" . (int)$product_id . "'");
		
	}
        
        
        
         public function getProductsRequest($data = array()) {
                 $lookup=array(array(
                    'from' => 'oc_user',
                    'localField' => 'user_id',
                    'foreignField' => 'user_id',
                    'as' => 'ou'
                 ),array(
                    'from' => 'oc_store',
                    'localField' => 'ou.store_id',
                    'foreignField' => 'store_id',
                    'as' => 'os'
                 ));
                 
                 
                if (isset($data['start']) || isset($data['limit'])) 
                {
					if ($data['start'] < 0) {
						$start = 0;
					}
                        else
                        {
                            $start=(int)$data['start'];
                        }

					if ($data['limit'] < 1) {
						$limit = (int)20;
					}
                        else 
                        {
                                $limit = (int)$data['limit'];
                        }

			
		}
		$match=array();
		
		if(!empty($data['user_id']))
		{
			$match=array('user_id'=>(int)$data['user_id']);
		}
		$sort_array=array('product_id'=>-1);
                $columns=array('date_added'=>1,'product_id'=>1,'image'=>1,'os.name'=>1,'ou.username'=>1,'ou.firstname'=>1,'ou.lastname'=>1,'sku'=>1,'HSTN'=>1,'model'=>1,'status'=>1);
                $query =  $this->db->query("join",DB_PREFIX . "product_temp",$lookup,'$ou',$match,'','',$limit,$columns,$start,$sort_array);
				//print_r($query);
                 foreach($query->row as $row)
                {
                $return_array[]=array(
                 'product_id'=>$row['product_id'],
                 'image'=>$row['image'],
                 'username'=>$row['ou']['username'],
				  'storeinchargename'=>$row['ou']['firstname']." ".$row['ou']['lastname'],
                 'sku'=>$row['sku'],
                 'HSTN'=>$row['HSTN'],
                 'date_added'=>$row['date_added'],
                  'model'=>$row['model'],
                  'status'=>$row['status'],
				  'storename'=>$row['os'][0]['name'],
                  'totalrows'=>$query->total_rows    
             );
            }
			//print_r($return_array);
            return $return_array;
             
	}
        public function getFilterProductsRequest($data = array()){
            $logs=new Log("filterproduct-".date('Y-m-d').".log");
             $match=array();
            if (!empty($data['filter_name'])) {
			//$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                $search_string=$this->db->escape($data['filter_name']);
                    $match['name']=$search_string;//new MongoRegex("/.*$search_string/i");
            }
            
            
            
               $lookup='';
               $lookupwithunwind=array(
                        array('lookup'=>
                                array(
                                    'from' => 'oc_product_to_category',
                                    'localField' => 'product_id',
                                    'foreignField' => 'product_id',
                                    'as' => 'opc'
                                    ),
                                    'unwind'=>'$opc'
                            ),
                            array('lookup'=>
                                array(
                                    'from' => 'oc_category_description',
                                    'localField' => 'opc.category_id',
                                    'foreignField' => 'category_id',
                                    'as' => 'ocd'
                                ),
                                'unwind'=>'$ocd'
                        )
                    );
             
                $columns=array( 
                    "_id"=> 1,
                    "ocd.category_id"=> 1,
                    "product_id"=>1,
                    "name"=>1,
                    "ocd.name"=>1,
                    "name"=>1,
                    );
                 if (isset($data['start']) || isset($data['limit'])) 
                 {
			if ($data['start'] < 0) {
				$start = 0;
			}
                        else
                        {
                            $start=(int)$data['start'];
                        }

			if ($data['limit'] < 1) {
				$limit = (int)20;
			}
                        else 
                        {
                                $limit = (int)$data['limit'];
                        }
		}
                
              $query = $this->db->query('join','oc_product_description',$lookup,'',$match,'','',$limit,$columns,$start,'',$lookupwithunwind);
        
              foreach($query->row as $row){
            
               $return_array[]=array("product_id"=>$row["product_id"],
                   "prd_name"=>$row["name"],
                   "cat_name"=>$row['ocd']["name"]
                );
            }
             return $return_array;
        }
         
        
          public function get_image_productbyID($product_id){
        $query = $this->db->query('select','oc_product_image_temp','','','',array('product_id'=>(int)$product_id));      
        return $query->rows;
}

   public function already_syatem($p_id,$data)
        {
             unset($data['product_id']);
             $serialize_data=serialize($data);
             $udata=array('remark'=>$serialize_data,
                 'status'=>(int)3);
             $match=array(
            'product_id'=>(int)$p_id
            );
             $query = $this->db->query('update',DB_PREFIX .'product_temp',$match,$udata);
        }
        
        
        
      
        public function getProduct($data = array()) {
          $match=array();
                if (!empty($data['filter_name'])) 
                {
                    $search_string=$this->db->escape($data['filter_name']);
                    $match['pd.name']=new MongoRegex("/.*$search_string/i");
                    //$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		


		if (isset($data['filter_status']) && !is_null($data['filter_status'])) 
                {
                    $match['status']= boolval($data['filter_status']);
                    //$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}
                
                
                if (isset($data['start']) || isset($data['limit'])) 
                {
			if ($data['start'] < 0) {
				$start = 0;
			}
                        else
                        {
                            $start=(int)$data['start'];
                        }

			if ($data['limit'] < 1) {
				$limit = (int)20;
			}
                        else 
                        {
                                $limit = (int)$data['limit'];
                        }

			
		}
                
                
                $lookup=array(
                'from' => 'oc_product_description',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                );
            
                $columns=array( 
                    "_id"=> 1,
                    "product_id"=> 1,
                    "model"=>1,
                    "sku"=>1,
                    "upc"=>1,
                    "ean"=>1,
                    "jan"=>1,
                    "isbn"=>1,
                    "mpn"=>1,
                    "location"=>1,
                    "quantity"=>1,
                    "stock_status_id"=>1,
                    "image"=>1,
                    "manufacturer_id"=>1,
                    "shipping"=>1,
                    "price"=>1,
                    "points"=>1,
                    "tax_class_id"=>1,
                    "date_available"=>1,
                    "weight"=>1,
                    "weight_class_id"=>1,
                    "length"=>1,
                    "width"=>1,
                    "height"=>1,
                    "length_class_id"=>1,
                    "subtract"=>1,
                    "minimum"=>1,
                    "sort_order"=>1,
                    "status"=>1,
                    "viewed"=>1,
                    "date_added"=>1,
                    "date_modified"=> 1,
                    "HSTN"=>1,
                    "price_tax"=> 1,
                    "purchase_price"=>1,
                    "wholesale_price"=> 1,
                    "pd.language_id"=>1,
                    "pd.name"=>1,
                    "pd.description"=>1,
                    "pd.tag"=>1,
                    "pd.meta_title"=>1,
                    "pd.meta_description"=>1,
                    "pd.meta_keyword"=>1,
                    "pd.meta_hindi"=>1
                );
            $sort_array=array('pd.name'=>1);
            
            
            $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array);
            //print_r($query->row);exit;
            foreach($query->row as $row)
            {
                $return_array[]=array("product_id"=>$row["product_id"],
                    "model"=>$row["model"],
                    "sku"=>$row["sku"],
                    "upc"=>$row["upc"],
                    "ean"=>$row["ean"],
                    "jan"=>$row["jan"],
                    "isbn"=>$row["isbn"],
                    "mpn"=>$row["mpn"],
                    "location"=>$row["location"],
                    "quantity"=>$row["quantity"],
                    "stock_status_id"=>$row["stock_status_id"],
                    "image"=>$row["image"],
                    "manufacturer_id"=>$row["manufacturer_id"],
                    "shipping"=>$row["shipping"],
                    "price"=>$row["price"],
                    "points"=>$row["points"],
                    "tax_class_id"=>$row["tax_class_id"],
                    "date_available"=>date('Y-m-d',($row['date_available']->sec)),
                    "weight"=>$row["weight"],
                    "weight_class_id"=>$row["weight_class_id"],
                    "length"=>$row["length"],
                    "width"=>$row["width"],
                    "height"=>$row["height"],
                    "length_class_id"=>$row["length_class_id"],
                    "subtract"=>$row["subtract"],
                    "minimum"=>$row["minimum"],
                    "sort_order"=>$row["sort_order"],
                    "status"=>$row["status"],
                    "viewed"=>$row["viewed"],
                    "date_added"=>date('Y-m-d  H:i:s',($row['date_added']->sec)),
                    "date_modified"=> date('Y-m-d  H:i:s',($row['date_modified']->sec)),
                    "HSTN"=>$row["HSTN"],
                    "price_tax"=> $row["price_tax"],
                    "purchase_price"=>$row["purchase_price"],
                    "wholesale_price"=> $row["wholesale_price"],
                    "language_id"=>$row["pd"][0]["language_id"],
                    "name"=>$row["pd"][0]["name"],
                    "description"=>$row["pd"][0]["description"],
                    "tag"=>$row["pd"][0]["tag"],
                    "meta_title"=>$row["pd"][0]["meta_title"],
                    "meta_description"=>$row["pd"][0]["meta_description"],
                    "meta_keyword"=>$row["pd"][0]["meta_keyword"],
                    "meta_hindi"=>$row["pd"][0]["meta_hindi"],
                            'totalrows'=>$query->total_rows    
                        );
            }
            
            //$return_array['total_rows']=$query->total_rows;
            return $return_array;
	}
        
        
        
        
        
          public function getProduct_company($data = array()) 
		  {
            //print_r($data);
              
             $match=array();
                if (!empty($data['filter_company_name'])) 
                {
                    $search_string=$this->db->escape($data['filter_company_name']);
                    $match['name']=new MongoRegex("/.*$search_string/i");
                    //$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) 
                {
                    $match['status']= boolval($data['filter_status']);
                    //$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}
          if (isset($data['start']) || isset($data['limit'])) 
                {
			if ($data['start'] < 0) {
				$start = 0;
			}
                        else
                        {
                            $start=(int)$data['start'];
                        }

			if ($data['limit'] < 1) {
				$limit = (int)20;
			}
                        else 
                        {
                                $limit = (int)$data['limit'];
                        }

			
		}
		//$match['is_active']=true;
		$sort_array=array('name'=>1);
		//$match=array();
                // $sort_array=array('company_id'=>1);
              //$query = $this->db->query('select','oc_manufacturer','','','',$match);
			  $query = $this->db->query("select",DB_PREFIX . "manufacturer",'','','',$match,'','','','',$sort_array);
			  //print_r($match);
			  //print_r($query);
	      //$sql = "SELECT * FROM  oc_company    where is_active=1";
	       //echo $sql;	
		return $query->rows;
	}
        
        
        
}