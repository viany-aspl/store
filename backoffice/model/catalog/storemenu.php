<?php
class ModelCatalogStoremenu extends Model {
    
    public function getParent($data = array()) 
    {
        $search_string= $data['filter_name'];
        $match['name']= new MongoRegex("/.*$search_string/i");
        
        $query=$this->db->query('select',DB_PREFIX . "storemenu",'','','',$match,'','','','',array('name'=>1));
	return $query->rows;	
    }
    public function getCategoryName($category_id) 
    {
       
        if(!empty($category_id))
        {
            $match['category_id']= (int)$category_id;
        }
        $query=$this->db->query('select',DB_PREFIX . "storemenu",'','','',$match,'','','','',array('name'=>1));
	//print_r($query->row);
        return $query->row;	
    }
    public function getAllActiveParent($data = array()) 
    {
        $match['status']=1;
        $match['parent_id']=0;
        $query=$this->db->query('select',DB_PREFIX . "storemenu",'','','',$match,'','','','',array('name'=>1));
	return $query;	
    }
    
    public function getAllActiveChild($parent_id) 
    {
        
        $match['status']=1;
        $match['parent_id']=(int)$parent_id;
        $query=$this->db->query('select',DB_PREFIX . "storemenu",'','','',$match,'','','','',array('name'=>1));
	return $query->rows;	
    }
    public function getusermenu($data) 
    {
		$log=new Log("getmenu-".date('Y-m-d').".log");
        if(!empty($data['user_id']))
        {
           $match['user_id']=(int)$data['user_id']; 
        }
        else 
        {
            $match['user_id']=0;
        }
        if(!empty($data['menutype']))
        {
            if($data['menutype']==1)
            {
                $match['menutype']=(int)$data['menutype']; 
            }
        }
        else 
        {
            $match['menutype']=0;
        }
        if(!empty($data['parent_id']))
        {
           $match['parent_id']=(int)$data['parent_id']; 
        }
        $log->write($match);
        $query=$this->db->query('select',DB_PREFIX . "storemenu_to_user",'','','',$match,'','','','',array('sort_order'=>1));
		$return_data=array();
        foreach($query->rows as $row)
        {
           $return_data[]= $row['category_id'];
        }
        return $return_data;
    }
    public function getgroupmenu($data) 
    {
        if(!empty($data['user_group_id']))
        {
           $match['group_id']=(int)$data['user_group_id']; 
        }
        else 
        {
            $match['group_id']=0;
        }
        if(!empty($data['menutype']))
        {
            if($data['menutype']==1)
            {
                $match['menutype']=(int)$data['menutype']; 
            }
        }
        else 
        {
            $match['menutype']=0;
        }
        if(!empty($data['parent_id']))
        {
           $match['parent_id']=(int)$data['parent_id']; 
        } 
        //print_r($match);
        $query=$this->db->query('select',DB_PREFIX . "storemenu_to_group",'','','',$match,'','','','',array('sort_order'=>1));
	$return_data=array();
        foreach($query->rows as $row)
        {
           $return_data[]= $row['category_id'];
        }
        return $return_data;
    }
    public function updatepermission($user_id,$parent_id,$data) 
    {
        $match['user_id']=(int)$user_id;
        $match['category_id']=(int)$parent_id;
        $query=$this->db->query('delete',DB_PREFIX . "storemenu_to_user",$match);
        
	$match2['status']=1;
        $match2['parent_id']=(int)$parent_id;
        $query=$this->db->query('select',DB_PREFIX . "storemenu",'','','',$match2,'','','','',array('sort_order'=>1));
	
        $match22['status']=1;
        $match22['category_id']=(int)$parent_id;
        $query22=$this->db->query('select',DB_PREFIX . "storemenu",'','','',$match22,'','','','',array('sort_order'=>1));
	
        foreach($query->rows as $child)
        {
            $match3['user_id']=(int)$user_id;
            $match3['category_id']=(int)$child['category_id'];
            $query=$this->db->query('delete',DB_PREFIX . "storemenu_to_user",$match3);
        }
        $input_array=array();
        foreach($data as $selected)
        {
            $match223['status']=1;
            $match223['category_id']=(int)$selected;
            $query223=$this->db->query('select',DB_PREFIX . "storemenu",'','','',$match223,'','','','',array('sort_order'=>1));
            $sort_order_child=$query223->row['sort_order'];
            $input_array=array(
                    'category_id'=>(int)$selected,
                    'user_id'=>(int)$user_id, 
                    'store_id'=>0,
                    'menutype'=>1,
                    'parent_id'=>(int)$parent_id,
                    'sort_order'=>(int)$sort_order_child
                  );
            try
            {
                $query = $this->db->query("insert",DB_PREFIX . "storemenu_to_user",$input_array);
            }
            catch(Exception $e)
            {
                //print_r($e);
            }
            
        }
        if(count($data)>0)
        {
            $sort_order_parent=$query22->row['sort_order'];
            $input_array2=array(
                    'category_id'=>(int)$parent_id,
                    'user_id'=>(int)$user_id, 
                    'store_id'=>0,
                    'menutype'=>0,
                    'parent_id'=>0,
                    'sort_order'=>(int)$sort_order_parent
                  );
            try
            {
                
                $query = $this->db->query("insert",DB_PREFIX . "storemenu_to_user",$input_array2);
            }
            catch(Exception $e)
            {
                
            }
        }
        
    }
    public function updategrouppermission($group_id,$parent_id,$data) 
    {
        $match['group_id']=(int)$group_id;
        $match['category_id']=(int)$parent_id;
        $query=$this->db->query('delete',DB_PREFIX . "storemenu_to_group",$match);
        
		$match2['status']=1;
        $match2['parent_id']=(int)$parent_id;
        $query=$this->db->query('select',DB_PREFIX . "storemenu",'','','',$match2,'','','','',array('sort_order'=>1));
	
        $match22['status']=1;
        $match22['category_id']=(int)$parent_id;
        $query22=$this->db->query('select',DB_PREFIX . "storemenu",'','','',$match22,'','','','',array('sort_order'=>1));
	
        foreach($query->rows as $child)
        {
            $match3['group_id']=(int)$group_id;
            $match3['category_id']=(int)$child['category_id'];
            $query=$this->db->query('delete',DB_PREFIX . "storemenu_to_group",$match3);
        }
        $input_array=array();
        foreach($data as $selected)
        {
            $match223['status']=1;
            $match223['category_id']=(int)$selected;
            $query223=$this->db->query('select',DB_PREFIX . "storemenu",'','','',$match223,'','','','',array('sort_order'=>1));
            $sort_order_child=$query223->row['sort_order'];
            $input_array=array(
                    'category_id'=>(int)$selected,
                    'group_id'=>(int)$group_id, 
                    'store_id'=>0,
                    'menutype'=>1,
                    'parent_id'=>(int)$parent_id,
                    'sort_order'=>(int)$sort_order_child
                  );
            try
            {
                $query = $this->db->query("insert",DB_PREFIX . "storemenu_to_group",$input_array);
            }
            catch(Exception $e)
            {
                //print_r($e);
            }
            
        }
        if(count($data)>0)
        {
            $sort_order_parent=$query22->row['sort_order'];
            $input_array2=array(
                    'category_id'=>(int)$parent_id,
                    'group_id'=>(int)$group_id, 
                    'store_id'=>0,
                    'menutype'=>0,
                    'parent_id'=>0,
                    'sort_order'=>(int)$sort_order_parent
                  );
            try
            {
                
                $query = $this->db->query("insert",DB_PREFIX . "storemenu_to_group",$input_array2);
            }
            catch(Exception $e)
            {
                
            }
        }
        
    }
    public function addCategory($data) 
    {
				
		$this->event->trigger('pre.admin.category.add', $data);

		$category_id=$this->db->getNextSequenceValue('oc_storemenu');
                if (isset($data['image'])) 
                {
                    $image = $this->db->escape($data['image']);
				}
                else
                {
                   $image=''; 
                }
                if(empty($data['path']))
                {
                    $data['parent_id']='';
                }
                $input_array=array(
                    'category_id'=>(int)$category_id,
                    'image'=>$image,
                    'parent_id'=>(int)$data['parent_id'], 
                    'path'=>$data['path'], 
                    'sort_order'=>(int)$data['sort_order'], 
                    'status'=>(int)$data['status'],
                    'date_modified' => new MongoDate(strtotime(date('Y-m-d h:i:s'))), 
                    'date_added' => new MongoDate(strtotime(date('Y-m-d h:i:s'))),
                    'category_description'=>$data['category_description'],
                    'name'=> $this->db->escape($data['category_description'][1]['name']),
					'name_hindi'=> $this->db->escape($data['category_description'][1]['name_hindi'])
                    
                );
		$query = $this->db->query("insert",DB_PREFIX . "storemenu",$input_array);
                // MySQL Hierarchical Data Closure Table Pattern
		$level = 0;

		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "storemenu_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");
                $query=$this->db->query('select',DB_PREFIX . "storemenu_path",'','','',array('category_id'=>(int)$data['parent_id']),'','','','',array('level'=>1));
		foreach ($query->rows as $result) 
                {
                    //$this->db->query("INSERT INTO `" . DB_PREFIX . "storemenu_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");
                    $this->db->query('insert',DB_PREFIX . "storemenu_path",array('category_id'=>(int)$category_id ,'path_id' =>(int)$result['path_id'],'level'=>(int)$level));
                    $level++;
		}
                $this->db->query('insert',DB_PREFIX . "storemenu_path",array('category_id'=>(int)$category_id ,'path_id' =>(int)$category_id,'level'=>(int)$level));
		
		$this->cache->delete('storemenu');

		$this->event->trigger('post.admin.category.add', $category_id);

		return $category_id;
	}

	public function editCategory($category_id, $data) 
	{
		//print_r($data);exit;
		$this->event->trigger('pre.admin.category.edit', $data);

                if (isset($data['image'])) 
                {
                    $image = $this->db->escape($data['image']);
				}
                else
                {
                   $image=''; 
                }
                if(empty($data['path']))
                {
                    $data['parent_id']='';
                }
                $input_array=array(
                   
                    'image'=>$image,
                    'parent_id'=>(int)$data['parent_id'], 
                    'path'=>$data['path'], 
                    'sort_order'=>(int)$data['sort_order'], 
                    'status'=>(int)$data['status'],
                    'date_modified' => new MongoDate(strtotime(date('Y-m-d h:i:s'))), 
                    'date_added' => new MongoDate(strtotime(date('Y-m-d h:i:s'))),
                    'category_description'=>$data['category_description'],
                    'name'=> $this->db->escape($data['category_description'][1]['name']),
					'name_hindi'=> $this->db->escape($data['category_description'][1]['name_hindi'])
                    
                );
		$query = $this->db->query("update",DB_PREFIX . "storemenu",array('category_id'=>(int)$category_id),$input_array);
		$query1 = $this->db->query("select",DB_PREFIX . "storemenu_to_user",'','','',array('category_id'=>(int)$category_id));
                foreach($query1->rows as $muser)
                {
                    $query2 = $this->db->query("update",DB_PREFIX . "storemenu_to_user",array('category_id'=>(int)$category_id,'user_id'=>(int)$muser['user_id']),array('sort_order'=>(int)$data['sort_order']));
                }
                $query3 = $this->db->query("select",DB_PREFIX . "storemenu_to_group",'','','',array('category_id'=>(int)$category_id));
                foreach($query3->rows as $muser)
                {
                    $query4 = $this->db->query("update",DB_PREFIX . "storemenu_to_group",array('category_id'=>(int)$category_id,'group_id'=>(int)$muser['group_id']),array('sort_order'=>(int)$data['sort_order']));
                }
		$this->cache->delete('storemenu');

		$this->event->trigger('post.admin.category.edit', $category_id);
	}

	public function deleteCategory($category_id) 
    {
            $this->event->trigger('pre.admin.category.delete', $category_id);
            $this->db->query('delete',DB_PREFIX.'storemenu',array('category_id'=>(int)$category_id));
			$this->db->query('delete',DB_PREFIX.'storemenu_to_group',array('category_id'=>(int)$category_id));
            $this->db->query('delete',DB_PREFIX.'storemenu_to_user',array('category_id'=>(int)$category_id));
            
            $this->cache->delete('storemenu');
            $this->event->trigger('post.admin.category.delete', $category_id);
	}

	public function repairCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "storemenu WHERE parent_id = '" . (int)$parent_id . "'");

		foreach ($query->rows as $category) {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "storemenu_path` WHERE category_id = '" . (int)$category['category_id'] . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "storemenu_path` WHERE category_id = '" . (int)$parent_id . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "storemenu_path` SET category_id = '" . (int)$category['category_id'] . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "storemenu_path` SET category_id = '" . (int)$category['category_id'] . "', `path_id` = '" . (int)$category['category_id'] . "', level = '" . (int)$level . "'");

			$this->repairCategories($category['category_id']);
		}
	}

        public function getChildCategories($parent_id = 0,$filter_store,$filter_role,$user_id) 
        {
            $match=array('parent_id'=>(int)$parent_id);
            
            $query=$this->db->query('select',DB_PREFIX . "storemenu",'','','',$match,'','','','',array());
           
            return $query->rows;
        }

	public function getCategory($category_id) 
        {
            
            $match=array('category_id'=>(int)$category_id);
            $query=$this->db->query('select',DB_PREFIX . "storemenu",'','','',$match,'','','','',array());
            return $query->row;
	}




	public function getCategories($data = array()) 
        {
            if (isset($data['start']) || isset($data['limit'])) 
            {
			if ($data['start'] < 0) {
				$start = 0;
			}
                        else
                        {
                           $start = (int)$data['start']; 
                        }
			if ($data['limit'] < 1) {
				$limit = 20;
			}
                        else 
                        {
                           $limit= (int)$data['limit']; 
                        }
			
            }
            $match=array();
            if(!empty($data['filter_name']))
            {
                $search_string= $data['filter_name'];
                $match['name']= new MongoRegex("/.*$search_string/i");
            }
            
            $query=$this->db->query('select',DB_PREFIX . "storemenu",'','','',$match,'',$limit,'',$start,array('path'=>1,'name'=>1));
            return $query;	
            
	}

	public function getCategoryDescriptions($category_id) {
		$category_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "storemenu_description WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description']
			);
		}

		return $category_description_data;
	}

	public function getCategoryFilters($category_id) { 
		$category_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "storemenu_filter WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_filter_data[] = $result['filter_id'];
		}

		return $category_filter_data;
	}

	public function getCategoryStores($category_id) {
		$category_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "storemenu_to_store WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['store_id'];
		}

		return $category_store_data;
	}
	public function getSubUsers($category_id) {
		$category_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "storemenu_to_user WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['user_id'];
		}

		return $category_store_data;
	}
	


	public function getTotalCategories() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "storemenu");

		return $query->row['total'];
	}
	
		
}
