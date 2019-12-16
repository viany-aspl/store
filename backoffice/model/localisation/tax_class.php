<?php
class ModelLocalisationTaxClass extends Model {
	public function addTaxClass($data) {
		
                $tax_class_id=$this->db->getNextSequenceValue('oc_tax_class');
                
                $input_array=array(
                    
                    'tax_class_id'=>(int)$tax_class_id,
                    'title'=>$this->db->escape($data['title']),
                    'description'=>$this->db->escape($data['description']),
                    'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                    );
            
                $query = $this->db->query("insert",DB_PREFIX . "tax_class",$input_array);
                
                if (isset($data['tax_rule'])) 
                {
                    foreach ($data['tax_rule'] as $tax_rule) 
                    {
                        $input_array2=array('tax_class_id' =>(int)$tax_class_id,
                                        'tax_rate_id' =>(int)$tax_rule['tax_rate_id'],
                                        'based'=>$this->db->escape($tax_rule['based']),
                                        'priority'=>(int)$tax_rule['priority']
                                    );
                        $query2 = $this->db->query("insert",DB_PREFIX . "tax_rule",$input_array2);
		
                    }
                }
		$this->cache->delete('tax_class');
	}

	public function editTaxClass($tax_class_id, $data) 
        {
                
             $input_array=array(
                    
                    
                    'title'=>$this->db->escape($data['title']),
                   
                    'description'=>$this->db->escape($data['description']),
                    
                    'date_modified'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                    
                        
                        );
                $where=array('tax_class_id'=>(int)$tax_class_id);
                $query = $this->db->query("update",DB_PREFIX . "tax_class",$where,$input_array);
                
                $query = $this->db->query("delete",DB_PREFIX . "tax_rule",$where); 
                if (isset($data['tax_rule'])) 
                {
                    foreach ($data['tax_rule'] as $tax_rule) 
                    {
                        $input_array2=array('tax_class_id' =>(int)$tax_class_id,
                                        'tax_rate_id' =>(int)$tax_rule['tax_rate_id'],
                                        'based'=>$this->db->escape($tax_rule['based']),
                                        'priority'=>(int)$tax_rule['priority']
                                    );
                        $query2 = $this->db->query("insert",DB_PREFIX . "tax_rule",$input_array2);
		
                    }
                }
			
		$this->cache->delete('tax_class');
	}

	public function deleteTaxClass($tax_class_id) {
		
                $where=array('tax_class_id'=>(int)$tax_class_id);
                $query = $this->db->query("delete",DB_PREFIX . "tax_class",$where); 
                $query = $this->db->query("delete",DB_PREFIX . "tax_rule",$where); 
		$this->cache->delete('tax_class');
	}

	public function getTaxClass($tax_class_id) 
        {
            $start='';
                $limit='';
                $sort_array=array();  
                $query = $this->db->query("select",DB_PREFIX . "tax_class",(int)$tax_class_id,'tax_class_id','','','',$limit,'',$start,$sort_array);
            
		return $query->row; 
	}

	public function getTaxClasses($data = array()) {
            
            if ($data) 
            {
                if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$start = 0;
				}
                                else {
                                        $start = (int)$data['start'];
                                 }
				if ($data['limit'] < 1) {
					$limit = 20;
				}
                                else {
                                        $limit = (int)$data['limit'];
                                 }

				
			}
                $sort_array=array('title'=>1);       
                $query = $this->db->query("select",DB_PREFIX . "tax_class",'','','','','',$limit,'',$start,$sort_array);
            
		return $query; 
            }
            else 
            {
                $start='';
                $limit='';
                $sort_array=array();  
                $query = $this->db->query("select",DB_PREFIX . "tax_class",'','','','','',$limit,'',$start,$sort_array);
            
		return $query; 
            }
	}


	public function getTaxRules($tax_class_id) {
		
                $start='';
                $limit='';
                $sort_array=array();  
                $query = $this->db->query("select",DB_PREFIX . "tax_rule",(int)$tax_class_id,'tax_class_id','','','',$limit,'',$start,$sort_array);
            
		return $query->rows; 
	}

	public function getTotalTaxRulesByTaxRateId($tax_rate_id) 
        {
           $match=array('tax_rate_id'=>(int)$tax_rate_id);
            
                  
            $groupbyarray=array(
                 "_id"=> array('$tax_class_id'), 
                "total"=> array('$sum'=> 1 ) 
            );
            
            $query = $this->db->query('gettotalcount','oc_tax_rule',$groupbyarray,$match);
            return $query->row[0]['total'];
                
	}
}