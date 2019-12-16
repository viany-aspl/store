<?php
	class ModelCatalogProductCatalogCategory extends Model 
	{
		public function addCategory($data) 
        {
            $category_id=$this->db->getNextSequenceValue('oc_product_catalog');
            $input_array=array(
                    'category_id'=>(int)$category_id,
                    'image'=>$data['image'],
                    'parent_id'=>(int)$data['parent_id'],
                    'top'=>(isset($data['top']) ? (int)$data['top'] : 0),
                    'column'=>(int)$data['column'],
                    'sort_order'=>(int)$data['sort_order'],
                    'status'=>(boolval($data['status'])),
                    'date_modified'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
                    'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
                    'category_description'=> $data['category_description'],
                    'category_store'=>$data['category_store']
                        );
                
                $query = $this->db->query("insert",DB_PREFIX . "product_catalog",$input_array);
                
                foreach ($data['category_description'] as $language_id => $value) 
                {
                    $input_array2=array('category_id' =>(int)$category_id,
                                        'language_id' =>(int)$language_id,
                                        'name'=>$this->db->escape($value['name']),
                                        'description'=>$this->db->escape($value['description']),
                                        'meta_title'=>$this->db->escape($value['meta_title']),
                                        'meta_description'=>$this->db->escape($value['meta_description']),
                                        'meta_keyword'=>$this->db->escape($value['meta_keyword']),
                                        'meta_hindi'=>$this->db->escape(null),
										'child_url'=>$value['child_url']
                                    );
                    $query2 = $this->db->query("insert",DB_PREFIX . "product_catalog_description",$input_array2);
				}
                // MySQL Hierarchical Data Closure Table Pattern
                $level = 0;
                $query3 = $this->db->query('select',DB_PREFIX . 'product_catalog_path',(int)$data['parent_id'],'category_id','','','','','','',array('level'=>1));
                
                foreach ($query3->rows as $result) 
                {
                    $input_array3=array('category_id' =>(int)$category_id,
                                        'path_id' =>(int)$result['path_id'],
                                        'level'=>(int)$level
                            );
                    $this->db->query('insert',DB_PREFIX . 'product_catalog_path',$input_array3);
                    $level++;
                }
                $input_array4=array('category_id'=>(int)$category_id,
                                    'path_id'=>(int)$category_id,
                                    'level'=>(int)$level
                                    );
                
                $this->db->query('insert',DB_PREFIX . 'product_catalog_path',$input_array4);
                ///////////////////////////////////
                if (isset($data['category_store'])) 
                {
                    foreach ($data['category_store'] as $store_id) 
                    {
                        $input_array5=array('category_id'=>(int)$category_id,
                                    'store_id'=>(int)$store_id
                                    );
                        $this->db->query('insert',DB_PREFIX . 'product_catalog_to_store',$input_array5);
                    }
                }
                if (isset($data['keyword'])) 
                {
                    $input_array6=array('query'=>"'productcatalogcategory_id=" . (int)$category_id . "'",
                                    'keyword'=>$this->db->escape($data['keyword'])
                                );
                    $this->db->query('insert',DB_PREFIX . 'url_alias',$input_array6);
                        
                }
                $this->cache->delete('category');
                return $category_id;
		}

		public function editCategory($category_id, $data) 
		{
			$input_array=array(
                    'image'=>$data['image'],
                    'parent_id'=>(int)$data['parent_id'],
                    'top'=>(isset($data['top']) ? (int)$data['top'] : 0),
                    'column'=>(int)$data['column'],
                    'sort_order'=>(int)$data['sort_order'],
                    'status'=>(boolval($data['status'])),
                    'date_modified'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
                    'category_description'=> $data['category_description'],
                    'category_store'=>$data['category_store'],
					'child_url'=>$data['child_url']
                    );
            $where=array('category_id'=>(int)$category_id);
            $query = $this->db->query("update",DB_PREFIX . "product_catalog",$where,$input_array);
            $query = $this->db->query("delete",DB_PREFIX . "product_catalog_description",$where);
            foreach ($data['category_description'] as $language_id => $value) 
            {
                $input_array2=array('category_id' =>(int)$category_id,
                                        'language_id' =>(int)$language_id,
                                        'name'=>$this->db->escape($value['name']),
                                        'description'=>$this->db->escape($value['description']),
                                        'meta_title'=>$this->db->escape($value['meta_title']),
                                        'meta_description'=>$this->db->escape($value['meta_description']),
                                        'meta_keyword'=>$this->db->escape($value['meta_keyword']),
                                        'meta_hindi'=>$this->db->escape(null),
										'child_url'=>$this->db->escape($value['child_url'])
                                    );
                $query2 = $this->db->query("insert",DB_PREFIX . "product_catalog_description",$input_array2);
			}
            ////////////////////////////////
            // MySQL Hierarchical Data Closure Table Pattern
			$query3 = $this->db->query('select',DB_PREFIX . 'product_catalog_path','','','',array('category_id'=>(int)$data['parent_id']),'','','','',array('level'=>1));
            if ($query3->rows) 
            {
				foreach ($query->rows as $category_path) 
                {
					// Delete the path below the current one
                    $delete_where=array('category_id' =>(int)$category_path['category_id'],'level'=>array('$lt'=>(int)$category_path['level']));
                    $this->db->query('delete',DB_PREFIX . 'product_catalog_path',$delete_where);
					$path = array();
					// Get the nodes new parents
                    $query3 = $this->db->query('select',DB_PREFIX . 'product_catalog_path',(int)$data['parent_id'],'category_id','','','','','','',array('level'=>1));
                    foreach ($query3->rows as $result) 
					{
						$path[] = $result['path_id'];
					}
					// Get whats left of the nodes current path
					$query3 = $this->db->query('select',DB_PREFIX . 'product_catalog_path',(int)$category_path['category_id'],'category_id','','','','','','',array('level'=>1));
					foreach ($query3->rows as $result) 
                    {
						$path[] = $result['path_id'];
					}

					// Combine the paths with a new level
					$level = 0;

					foreach ($path as $path_id) 
                    {
						$where=array('category_id'=>(int)$category_path['category_id']);
                        $input_array=array('path_id'=>(int)$path_id,'level' =>(int)$level);
                        $query = $this->db->query("update",DB_PREFIX . "product_catalog_path",$where,$input_array);
						$level++;
					}
				}
			} 
            else 
            {
				$where=array('category_id'=>(int)$category_id);
                $query = $this->db->query("delete",DB_PREFIX . "product_catalog_path",$where);
				$level = 0;
				$query = $this->db->query('select',DB_PREFIX . 'product_catalog_path',(int)$data['parent_id'],'category_id','','','','','','',array('level'=>1));
                
				foreach ($query->rows as $result) 
                {
					$input_array3=array('category_id' =>(int)$category_id,
                                        'path_id' =>(int)$result['path_id'],
                                        'level'=>(int)$level
                                    );
                    $this->db->query('insert',DB_PREFIX . 'product_catalog_path',$input_array3);
					$level++;
				}
                $where=array('category_id'=>(int)$category_id);
                $input_array=array('path_id'=>(int)$category_id,'level' =>(int)$level);
                $query = $this->db->query("update",DB_PREFIX . "product_catalog_path",$where,$input_array);
				//$this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', level = '" . (int)$level . "'");
			}
			$where=array('category_id'=>(int)$category_id);
            $query = $this->db->query("delete",DB_PREFIX . "product_catalog_filter",$where);
            if (isset($data['category_filter'])) 
            {
				foreach ($data['category_filter'] as $filter_id) 
                {
                    $input_array5=array('category_id'=>(int)$category_id,
                                    'filter_id'=>(int)$filter_id
                                    );
                    $this->db->query('insert',DB_PREFIX . 'product_catalog_filter',$input_array5);
				}
			}
            $where=array('category_id'=>(int)$category_id);
            $query = $this->db->query("delete",DB_PREFIX . "product_catalog_to_store",$where);
            if (isset($data['category_store'])) 
            {
                foreach ($data['category_store'] as $store_id) 
                {
                    $input_array5=array('category_id'=>(int)$category_id,
                                    'store_id'=>(int)$store_id
                                    );
                    $this->db->query('insert',DB_PREFIX . 'product_catalog_to_store',$input_array5);
                }
            }
            $where=array('query'=>"'productcatalogcategory_id=" . (int)$category_id . "'");
			$query = $this->db->query("delete",DB_PREFIX . "url_alias",$where);
            if (isset($data['keyword'])) 
            {
                $input_array6=array('query'=>"'productcatalogcategory_id=" . (int)$category_id . "'",
                                    'keyword'=>$this->db->escape($data['keyword'])
                                );
                $this->db->query('insert',DB_PREFIX . 'url_alias',$input_array6);
            }
            $this->cache->delete('category');
            
		}
		public function deleteCategory($category_id) 
        {
			$where=array('category_id'=>(int)$category_id);
            $query = $this->db->query("delete",DB_PREFIX . "product_catalog_path",$where); 
            $query3 = $this->db->query('select',DB_PREFIX . 'product_catalog_path',(int)$category_id,'path_id','','','','','','',array());
            foreach ($query3->rows as $result) 
            {
				$this->deleteCategory($result['category_id']);
			}
            $query = $this->db->query("delete",DB_PREFIX . "product_catalog",$where);
            $query = $this->db->query("delete",DB_PREFIX . "product_catalog_description",$where);
            $query = $this->db->query("delete",DB_PREFIX . "product_catalog_filter",$where);
            $query = $this->db->query("delete",DB_PREFIX . "product_catalog_to_store",$where);
            $query = $this->db->query("delete",DB_PREFIX . "product_catalog_to_layout",$where);
            $query = $this->db->query("delete",DB_PREFIX . "product_to_product_catalog",$where);
                
            $where3=array('query'=>"'productcatalogcategory_id=" . (int)$category_id . "'");
            $query = $this->db->query("delete",DB_PREFIX . "url_alias",$where3);
            $this->cache->delete('category');
		}

		public function getCategory($category_id) 
		{
                
            $lookup=array(array(
                'from' => 'oc_product_catalog_description',
                'localField' => 'category_id',
                'foreignField' => 'category_id',
                'as' => 'description'
            ),array(
                'from' => 'oc_product_catalog_path',
                'localField' => 'description.category_id',
                'foreignField' => 'path_id',
                'as' => 'path'
            )
            );
            
            $match=array('category_id'=>(int)$category_id);
            $start='';
            $limit='';
            $columns=array( 
                    "_id"=> 1,
                    "category_id"=> 1,
                    "parent_id"=>1,
                    "image"=>1,
                    "top"=>1,
                    "column"=>1,
                    "sort_order"=>1,
                    "status"=>1,
                    "description.name"=> 1,
                    "description.meta_keyword"=>1,
                    "path.path_id"=> 1
                );
            $sort_array=array();
            $query = $this->db->query("join",DB_PREFIX . "product_catalog",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array);
            //print_r($query->row);
            $return_array= array();
            foreach($query->row as $row)
            {
                $return_array['category_id']=$row['category_id'];
                $return_array['parent_id']=$row['parent_id'];
                $return_array['image']=$row['image'];
                $return_array['top']=$row['top'];
                $return_array['column']=$row['column'];
                $return_array['sort_order']=$row['sort_order'];
                $return_array['status']=$row['status'];
                $return_array['name']=strip_tags($row['description'][0]['name']);
                $return_array['keyword']=htmlentities($row['description'][0]['meta_keyword']);
                $return_array['path']=$row['path'][0]['path_id'];
                
               
                ////////////////
                
            }
            //print_r($return_array); 
            return $return_array;
		}
		public function getCategories($data = array()) 
        {
			$log=new Log("catalogcategories-".date('Y-m-d').".log");
			$log->write('in model');
			$log->write($data);
            $match['status']=true;
            if(!empty($data['filter_name']))
            {   
                $search_string= $data['filter_name'];
                $match['category_description'][1]['name']= new MongoRegex("/.*$search_string/i");
            }
            if(!empty($data['store_id']))
            {   
                //$match['category_store']= $data['store_id'];
            }
			
            if($data['service_type']=='catalogue')
            {   
				//////////all
                $log->write('in if');
            }
			else if($data['service_type']=='dashboard')
            {   
                $match['top']= (int)1;
				$log->write('in else if');
            }
			else
			{
				///////////all
				$log->write('in else');				
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
				$limit=(int)$data['limit']; 
				$start=(int)$data['start'];
			}		
			$log->write(json_encode($match));
            $sort_array=array('sort_order'=>1);
            $query = $this->db->query("select",DB_PREFIX . "product_catalog",'','','',$match,'',$limit,'',$start,$sort_array);
            $log->write($query);
            foreach($query->rows as $row)
            {
                $return_array[]=array(
                            'category_id'=>$row['category_id'],
                            'sort_order'=>$row['sort_order'],
                            'name'=>$row['category_description'][1]['name'],
							'child_url'=>$row['category_description'][1]['child_url'],
                            'totalrows'=>$query->num_rows    
                        );
            }
            return $return_array;
		}
		public function getCategoryDescriptions($category_id) 
		{
            
            $sortby=array();
            $columns=array('language_id',
                'name',
                'meta_title',
                'meta_description',
                'meta_keyword',
                'description',
				'child_url'
				);
            $groupbyarray=array();
            $match=array('category_id'=>$category_id);
            $query = $this->db->query('select','oc_product_catalog_description',$category_id,'category_id','','','','',$columns,'',$sortby);
           
            foreach ($query->rows as $result) 
			{
				$category_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description'],
				'child_url'      => $result['child_url']
				);
			}
            return $category_description_data;
		}

		public function getCategoryFilters($category_id) 
		{
			$category_filter_data = array();
            $columns=array('filter_id');
			//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");
            $query = $this->db->query('select','oc_product_catalog_filter',$category_id,'category_id','','','','',$columns,'',$sortby);
           
			foreach ($query->rows as $result) 
			{
				$category_filter_data[] = $result['filter_id'];
			}

			return $category_filter_data;
		}
		public function getCategoryStores($category_id) 
		{
			$category_store_data = array();
			$columns=array('store_id');
			$query = $this->db->query('select','oc_product_catalog_to_store',$category_id,'category_id','','','','',$columns,'',$sortby);
           
			foreach ($query->rows as $result) 
			{
				$category_store_data[] = $result['store_id'];
			}
			return $category_store_data;
		}
		public function getCategoryChild($data = array()) 
		{
            $match=array();
            if(!empty($data['category_id']))
            {   
                $match['category_ids']= (string)$data['category_id'];
            }
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
            $columns=array( 
                    "_id"=> 1,
                    "product_id"=> 1,
                    "name"=>1,
                    "location"=>1,
					"image"=>1,
					"status"=>1,
					"date_added"=>1,
					"date_modified"=> 1,
					"product_description"=>1,
                );
            //print_r(json_encode($match));
            $sort_array=array('product_id'=>1);
            $query = $this->db->query("select",DB_PREFIX . "product_catalog",'','','',$match,'',$limit,'',$start,$sort_array);
            return $query;
		}
	}
