<?php
class ModelCatalogDashboardproduct extends Model {

	public function addProduct($data) 
        {
            $this->load->model('setting/store');
                $storeprd = $this->model_setting_store->getStoresForProducts();
                
		$product_id=$this->db->getNextSequenceValue('oc_dashboardproduct');
                $input_array=array('product_id' =>(int)$product_id,
                                        
                                        'name' =>$this->db->escape($data['product_description'][1]['name']), 
                                        
                                        'location' =>$this->db->escape($data['location']), 
                                        
                                        'date_available' =>new MongoDate(strtotime($data['date_available'])), 
                                        
                                        'status' => boolval($data['status']), 
                                        'sort_order' => (int)$data['sort_order'],
                                        'category_ids' => $data['product_category'],
                                        'category_name' =>$data['product_category_name'],
                                        'product_description'=>$data['product_description'],
                                        'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                                    );
                    $query2 = $this->db->query("insert",DB_PREFIX . "dashboardproduct",$input_array);
		

		if (isset($data['image'])) 
                {
                    $where=array('product_id'=>(int)$product_id);
                    $input_array2=array('image'=>$this->db->escape($data['image']));
                    $query = $this->db->query("update",DB_PREFIX . "dashboardproduct",$where,$input_array2);
					
                }

		foreach ($data['product_description'] as $language_id => $value) 
                {
                    $input_array3=array(
                            'product_id'=>(int)$product_id,
                            'language_id'=> (int)$language_id,
                            'name'=>$this->db->escape($value['name']),
                            'description'=>$this->db->escape($value['description'])
                            
                         );
                    
                   
                    $query2 = $this->db->query("insert",DB_PREFIX . "dashboardproduct_description",$input_array3);
                }

		if (isset($data['product_store'])) 
                {
			foreach ($data['product_store'] as $store_id) 
                        {
                            $input_array4=array(
                            'product_id'=>(int)$product_id,
                            'store_id'=> (int)$store_id,
                            'MOD_DATE'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                             );
                            $query2 = $this->db->query("insert",DB_PREFIX . "dashboardproduct_to_store",$input_array4);
			}
		}

		if (isset($data['product_image'])) 
                {
			foreach ($data['product_image'] as $product_image) 
                        {
                            $input_array9=array(
                                                'product_id'=>(int)$product_id,
                                                'image'=>$this->db->escape($product_image['image']) ,
                                                'sort_order'=>(int)$product_image['sort_order']
                                    );
                            $query9 = $this->db->query("insert",DB_PREFIX . "dashboardproduct_image",$input_array9);
			}
		}
		if (isset($data['product_category'])) 
                {
			foreach ($data['product_category'] as $category_id) 
                        {
                            $input_array11=array(
                                                'product_id'=>(int)$product_id,
                                                'category_id'=>(int)$category_id
                            );
                            $query11 = $this->db->query("insert",DB_PREFIX . "dashboardproduct_to_category",$input_array11);
			
			}
		}
                if (isset($data['product_related'])) 
                {
			foreach ($data['product_related'] as $related_id) 
                        {
                            $where13=array('product_id'=>(int)$product_id,'related_id'=>(int)$related_id);
                            $query = $this->db->query("delete",DB_PREFIX . "dashboardproduct_related",$where13);
                            $input_array13=array(
                                                'product_id'=>(int)$product_id,
                                                'related_id'=>(int)$related_id
                                    );
                            $query13 = $this->db->query("insert",DB_PREFIX . "dashboardproduct_related",$input_array13);
			
			}
		}
		$alias_id=$this->db->getNextSequenceValue('oc_url_alias');
		if ($data['keyword']) 
                {
                    $input_array16=array(
                                        'query'=>'dashboardproduct_id=' . (int)$product_id,
                                        'keyword'=>$this->db->escape($data['keyword']),
                                        'url_alias_id'=>(int)$alias_id
                                    );
                    
                    $query16 = $this->db->query("insert",DB_PREFIX . "url_alias",$input_array16);
                 
		}

		return $product_id;
	}

	public function editProduct($product_id, $data) 
        {
                
		$log=new Log("dashboardproduct-edit-".date('Y-m-d').".log");
                $this->load->model('setting/store');
                $storeprd = $this->model_setting_store->getStoresForProducts();
                $input_array=array(
                    
                    'name' =>$this->db->escape($data['product_description'][1]['name']), 
                    
                    'location'=>$this->db->escape($data['location']),
                    
                    'date_available'=>new MongoDate(strtotime($data['date_available'])),
                    
                    'status'=>boolval($data['status']), 
                    'sort_order' => (int)$data['sort_order'],
                    'category_ids' => $data['product_category'],
                    'category_name' =>$data['product_category_name'],
                    'product_description'=>$data['product_description'],
                    'date_modified'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                        );
                $where=array('product_id'=>(int)$product_id);
                $query = $this->db->query("update",DB_PREFIX . "dashboardproduct",$where,$input_array);
                
		$this->db->query($upd_q);

		if (isset($data['image'])) 
                {
                    $input_array2=array(
                            'image'=>$this->db->escape($data['image'])
                        );
                        $query = $this->db->query("update",DB_PREFIX . "dashboardproduct",$where,$input_array2);
		}
                $where=array('product_id'=>(int)$product_id);
                $query = $this->db->query("delete",DB_PREFIX . "dashboardproduct_description",$where);
		foreach ($data['product_description'] as $language_id => $value) 
                {
                    $input_array3=array(
                            'product_id'=>(int)$product_id,
                            'language_id'=> (int)$language_id,
                            'name'=>$this->db->escape($value['name']),
                            'description'=>$this->db->escape($value['description'])
                            
                         );
                    
                   
                    $query2 = $this->db->query("insert",DB_PREFIX . "dashboardproduct_description",$input_array3);
		}
               
                if (isset($data['product_store'])) 
                {
                    $where11=array('product_id'=>(int)$product_id);
                    $query = $this->db->query("delete",DB_PREFIX . "dashboardproduct_to_store",$where11);
					//print_r($data['product_store']);
					//exit;
						foreach ($data['product_store'] as $store_id) 
                        {
                            
                            $where1=array('store_id'=>(int)$store_id,'product_id'=>(int)$product_id);
                            $query_s = $this->db->query('select',DB_PREFIX . 'dashboardproduct_to_store','','','',$where1,'','','','',array());
                            if($query_s->num_rows)
                            {
                                ///////no need to do anything
                            }
                            else 
                            {
                                $input_array4=array(
                                'product_id'=>(int)$product_id,
                                'store_id'=> (int)$store_id,
                                'MOD_DATE'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                                );
                            
                                $query2 = $this->db->query("insert",DB_PREFIX . "dashboardproduct_to_store",$input_array4);
                            
                            }
                        
                        }
		}
                
                $query = $this->db->query("delete",DB_PREFIX . "dashboardproduct_image",$where);
		if (isset($data['product_image'])) 
                {
			foreach ($data['product_image'] as $product_image) 
                        {
                            $input_array9=array(
                                                'product_id'=>(int)$product_id,
                                                'image'=>$this->db->escape($product_image['image']) ,
                                                'sort_order'=>(int)$product_image['sort_order']
                                    );
                            $query9 = $this->db->query("insert",DB_PREFIX . "dashboardproduct_image",$input_array9);
			}
		}
                
                $query = $this->db->query("delete",DB_PREFIX . "dashboardproduct_to_category",$where);
		if (isset($data['product_category'])) 
                {
			foreach ($data['product_category'] as $category_id) 
                        {
                            $input_array11=array(
                                                'product_id'=>(int)$product_id,
                                                'category_id'=>(int)$category_id
                                    );
                            $query11 = $this->db->query("insert",DB_PREFIX . "dashboardproduct_to_category",$input_array11);
			
			}
		}
                $query = $this->db->query("delete",DB_PREFIX . "dashboardproduct_related",$where);
		if (isset($data['product_related'])) 
                {
			foreach ($data['product_related'] as $related_id) 
                        {
                            $where13=array('product_id'=>(int)$product_id,'related_id'=>(int)$related_id);
                            $query = $this->db->query("delete",DB_PREFIX . "dashboardproduct_related",$where13);
                            $input_array13=array(
                                                'product_id'=>(int)$product_id,
                                                'related_id'=>(int)$related_id
                                    );
                            $query13 = $this->db->query("insert",DB_PREFIX . "dashboardproduct_related",$input_array13);
			
			}
		}
                $where16=array('query'=>'dashboardproduct_id=' . (int)$product_id);
                $query = $this->db->query("delete",DB_PREFIX . "url_alias",$where16);
                $alias_id=$this->db->getNextSequenceValue('oc_url_alias');
		if ($data['keyword']) 
                {
                    $input_array16=array(
                                        'query'=>'dashboardproduct_id=' . (int)$product_id,
                                        'keyword'=>$this->db->escape($data['keyword']),
                                        'url_alias_id'=>(int)$alias_id
                                    );
                    
                    $query16 = $this->db->query("insert",DB_PREFIX . "url_alias",$input_array16);
                    
		}

	}

	public function deleteProduct($product_id) {
            
                $where=array('product_id'=>(int)$product_id);
                $query = $this->db->query("delete",DB_PREFIX . "dashboardproduct",$where); 
           
                $query = $this->db->query("delete",DB_PREFIX . "dashboardproduct_description",$where);
                
                
                $query = $this->db->query("delete",DB_PREFIX . "dashboardproduct_image",$where);
               
                $query = $this->db->query("delete",DB_PREFIX . "dashboardproduct_related",$where);
                
                $where2=array('related_id'=>(int)$product_id);
                $query = $this->db->query("delete",DB_PREFIX . "dashboardproduct_related",$where2);
                
                
                $query = $this->db->query("delete",DB_PREFIX . "dashboardproduct_to_category",$where);
               
                $query = $this->db->query("delete",DB_PREFIX . "dashboardproduct_to_store",$where);
                $where2=array('dashboardproduct_id'=>(int)$product_id);
                $query = $this->db->query("delete",DB_PREFIX . "url_alias",$where2);
                
	}

	public function getProduct($product_id) 
        {
         
                $match=array('product_id'=>(int)$product_id);
            
                $lookup=array(
                'from' => 'oc_dashboardproduct_description',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                );
            
                $columns=array( 
                    "_id"=> 1,
                    "product_id"=> 1,
                    
                    "location"=>1,
                    
                    "image"=>1,
                    "manufacturer_id"=>1,
                    
                    "date_available"=>1,
                    
                    "status"=>1,
                    "sort_order"=>1,
                    "date_added"=>1,
                    "date_modified"=> 1,
                    
                    "pd.language_id"=>1,
                    "pd.name"=>1,
                    "pd.description"=>1,
                    "pd.tag"=>1,
                    
                    "pd.meta_hindi"=>1
                );
            $sort_array=array();
            $limit='';
            $start='';
           
            
            $query = $this->db->query("join",DB_PREFIX . "dashboardproduct",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array);
            //print_r($query->row);
            
            $colval='dashboardproduct_id=' . (int)$product_id ;
            $query2 = $this->db->query("select",DB_PREFIX . "url_alias",$colval,'query','','','',$limit,'',$start,$sort_array);
            
            $return_array=array(
                    "product_id"=> $query->row[0]['product_id'],
                    "name"=>$query->row[0]['name'],
                    
                    "location"=>$query->row[0]['location'],
                    
                    "image"=>$query->row[0]['image'],
                    "manufacturer_id"=>$query->row[0]['manufacturer_id'],
                    
                    "date_available"=>date('Y-m-d',($query->row[0]['date_available']->sec)),
                    
                    "status"=>$query->row[0]['status'],
                    "sort_order"=>$query->row[0]['sort_order'],
                    "date_added"=>date('Y-m-d  H:i:s',($query->row[0]['date_added']->sec)),
                    "date_modified"=> date('Y-m-d  H:i:s',($query->row[0]['date_modified']->sec)),
                    
                    "language_id"=>$query->row[0]['pd'][0]['language_id'],
                    "name"=>$query->row[0]['pd'][0]['name'],
                    "description"=>$query->row[0]['pd'][0]['description'],
                    
                    "keyword"=>$query2->row['keyword'],
                    );
            
            return $return_array;
	}

	public function getProducts($data = array()) {
            
                $match=array();
                if (!empty($data['filter_name'])) 
                {
                    $search_string=$this->db->escape($data['filter_name']);
                    $match['name']=new MongoRegex("/.*$search_string/i");
                   
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) 
                {
                    $match['status']= boolval($data['filter_status']);
                    
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
               
                $columns=array( 
                    "_id"=> 1,
                    "product_id"=> 1,
                    "model"=>1,
                    
                    "location"=>1,
                    
                    "image"=>1,
                    "manufacturer_id"=>1,
                    
                    "status"=>1,
                    
                    "date_added"=>1,
                    "date_modified"=> 1,
                   
                    "product_description"=>1,
                   
                );
            $sort_array=array('product_id'=>1);
            $query = $this->db->query("select",DB_PREFIX . "dashboardproduct",'','','',$match,'',$limit,'',$start,$sort_array);
            return $query;
	}

	public function getProductDescriptions($product_id) 
        {
		$product_description_data = array();
               
            $query = $this->db->query("select",DB_PREFIX . "dashboardproduct_description",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            
            foreach ($query->rows as $result) 
            {
               
               $product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description']
				
			);
            }
            return $product_description_data;
	}

	public function getProductCategories($product_id) {
		$product_category_data = array();
             
            $query = $this->db->query("select",DB_PREFIX . "dashboardproduct_to_category",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            
            foreach ($query->rows as $result) 
            {
               $product_category_data[] = $result['category_id'];
            }
            return $product_category_data;
	}

	public function getProductImages($product_id) 
        {
            $query = $this->db->query("select",DB_PREFIX . "dashboardproduct_image",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            return $query->rows;
	}

	public function getProductStores($product_id) 
        {
            $product_store_data = array();
            $query = $this->db->query("select",DB_PREFIX . "dashboardproduct_to_store",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            foreach ($query->rows as $result) 
            {
		$product_store_data[] = $result['store_id'];
            }
            return $product_store_data;
	}
        
       
	public function getProductRelated($product_id) 
        {
            $product_related_data = array();
            $query = $this->db->query("select",DB_PREFIX . "dashboardproduct_related",'','','',array('product_id'=>(int)$product_id),'',$limit,'',$start,$sort_array);
            foreach ($query->rows as $result) 
            {
		$product_related_data[] = $result['related_id'];
            }
            return $product_related_data;
	}
        
        public function getProductName($product_id) 
        {
            $query = $this->db->query('select',DB_PREFIX . 'dashboardproduct',(int)$product_id,'product_id','','','','','','',array());
            return $query->row;
	}
       

        public function getProductsWithTaxAndCategory($data = array()) 
        {
                $match=array('product_id'=>array('$gt'=>0));
                if (!empty($data['filter_name'])) 
                {
                    $search_string=$this->db->escape($data['filter_name']);
                    $match['name']=new MongoRegex("/.*$search_string/i");
                }
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) 
                {
                    $match['status']= boolval($data['filter_status']);
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
            $lookup=array();
            
            $sort_array=array('product_id'=>1);
            
            $query = $this->db->query("select",DB_PREFIX . "dashboardproduct",$lookup,'','',$match,'',$limit,'',$start,$sort_array);
            
            return $query->rows;
	}
        
}