<?php
class ModelLocalisationWeightClass extends Model {
	public function addWeightClass($data) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "weight_class SET value = '" . (float)$data['value'] . "'");

		//$weight_class_id = $this->db->getLastId();
                $weight_class_id =$this->db->getNextSequenceValue('oc_weight_class'); 
                $input_array=array('weight_class_id'=>(int)$weight_class_id,
                                    'value'=>(float)$data['value']
                                );
                $query = $this->db->query("insert",DB_PREFIX . "weight_class",$input_array);
                                
		foreach ($data['weight_class_description'] as $language_id => $value) 
                {
                    //$this->db->query("INSERT INTO " . DB_PREFIX . "weight_class_description SET weight_class_id = '" . (int)$weight_class_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', unit = '" . $this->db->escape($value['unit']) . "'");
                    $input_array2=array('weight_class_id'=>(int)$weight_class_id,
                                        'language_id'=>(int)$language_id,
                                        'title'=>$this->db->escape($value['title']),
                                        'unit'=>$this->db->escape($value['unit'])
                                );
                    $query = $this->db->query("insert",DB_PREFIX . "weight_class_description",$input_array2);
                 
		}

		$this->cache->delete('weight_class');
	}

	public function editWeightClass($weight_class_id, $data) 
        {
		//$this->db->query("UPDATE " . DB_PREFIX . "weight_class SET value = '" . (float)$data['value'] . "' WHERE weight_class_id = '" . (int)$weight_class_id . "'");
                //$weight_class_id =$this->db->getNextSequenceValue('oc_weight_class'); 
                $where=array('weight_class_id'=>(int)$weight_class_id);
                $input_array=array(
                                    'value'=>(float)$data['value']
                                );
                //print_r($where);exit;
                $query = $this->db->query("update",DB_PREFIX . "weight_class",$where,$input_array);
                
                $query = $this->db->query("delete",DB_PREFIX . "weight_class_description",$where);
		//$this->db->query("DELETE FROM " . DB_PREFIX . "weight_class_description WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		foreach ($data['weight_class_description'] as $language_id => $value) 
                {
                    //$this->db->query("INSERT INTO " . DB_PREFIX . "weight_class_description SET weight_class_id = '" . (int)$weight_class_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', unit = '" . $this->db->escape($value['unit']) . "'");
                    $input_array2=array('weight_class_id'=>(int)$weight_class_id,
                                        'language_id'=>(int)$language_id,
                                        'title'=>$this->db->escape($value['title']),
                                        'unit'=>$this->db->escape($value['unit'])
                                );
                    $query = $this->db->query("insert",DB_PREFIX . "weight_class_description",$input_array2);
                 
		}

		$this->cache->delete('weight_class');
	}

	public function deleteWeightClass($weight_class_id) 
        {
            $where=array('weight_class_id'=>(int)$weight_class_id);
            $query = $this->db->query("delete",DB_PREFIX . "weight_class",$where);
            $query = $this->db->query("delete",DB_PREFIX . "weight_class_description",$where);
            //$this->db->query("DELETE FROM " . DB_PREFIX . "weight_class WHERE weight_class_id = '" . (int)$weight_class_id . "'");
            //$this->db->query("DELETE FROM " . DB_PREFIX . "weight_class_description WHERE weight_class_id = '" . (int)$weight_class_id . "'");

            //$this->cache->delete('weight_class');
	}

	public function getWeightClasses($data = array()) 
        {
		if ($data) 
                {
                        /*
			$sql = "SELECT * FROM " . DB_PREFIX . "weight_class wc "
                                . "LEFT JOIN " . DB_PREFIX . "weight_class_description "
                                . "wcd ON (wc.weight_class_id = wcd.weight_class_id) "
                                . "WHERE wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sort_data = array(
				'title',
				'unit',
				'value'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY title";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
                        */
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$start = 0;
				}
                                else {
                                        $start = $data['start'];
                                }
				if ($data['limit'] < 1) {
					$limit = 20;
				}
                                else {
                                        $limit = (int)$data['limit'];
                                }
				
			}

			$sort_array=array('title'=>1);  
                        $lookup=array(
                                'from' => 'oc_weight_class_description',
                                'localField' => 'weight_class_id',
                                'foreignField' => 'weight_class_id',
                                'as' => 'wcd'
                                );
                        $match=array('wcd.language_id'=>(int)$this->config->get('config_language_id'));
                        $columns=array( 
                            "_id"=> 1,
                            "weight_class_id"=> 1,
                            "value"=>1,
                            "wcd.language_id"=>1,
                            "wcd.title"=> 1,
                            "wcd.unit"=>1,
                            );
                        $query = $this->db->query("join",DB_PREFIX . "weight_class",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array);
                        $return_array= array();
                        $a=0;
                        foreach($query->row as $row)
                        {
                            $return_array[$a]['weight_class_id']=$row['weight_class_id'];
                            $return_array[$a]['value']=$row['value'];
                            $return_array[$a]['language_id']=strip_tags($row['wcd'][0]['language_id']);
                            $return_array[$a]['title']=htmlentities($row['wcd'][0]['title']);
                            $return_array[$a]['unit']=$row['wcd'][0]['unit'];
                            $a++;
                        }
			return $return_array;
		} 
                else 
                {
			$weight_class_data = $this->cache->get('weight_class.' . (int)$this->config->get('config_language_id'));

			if (!$weight_class_data) 
                        {
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

				//$weight_class_data = $query->rows;
                        $sort_array=array();  
                        $lookup=array(
                                'from' => 'oc_weight_class_description',
                                'localField' => 'weight_class_id',
                                'foreignField' => 'weight_class_id',
                                'as' => 'wcd'
                                );
                        $match=array('wcd.language_id'=>(int)$this->config->get('config_language_id'));
                        $columns=array( 
                            "_id"=> 1,
                            "weight_class_id"=> 1,
                            "value"=>1,
                            "wcd.language_id"=>1,
                            "wcd.title"=> 1,
                            "wcd.unit"=>1,
                            );
                        $query = $this->db->query("join",DB_PREFIX . "weight_class",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array);
                        $return_array= array();
                        $a=0;
                        foreach($query->row as $row)
                        {
                            $return_array[$a]['weight_class_id']=$row['weight_class_id'];
                            $return_array[$a]['value']=$row['value'];
                            $return_array[$a]['language_id']=strip_tags($row['wcd'][0]['language_id']);
                            $return_array[$a]['title']=htmlentities($row['wcd'][0]['title']);
                            $return_array[$a]['unit']=$row['wcd'][0]['unit'];
                            $a++;
                        }
                            $weight_class_data= $return_array;

                            $this->cache->set('weight_class.' . (int)$this->config->get('config_language_id'), $weight_class_data);
			}

			return $weight_class_data;
		}
	}

	public function getWeightClass($weight_class_id) 
        {
                        //$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wc.weight_class_id = '" . (int)$weight_class_id . "' AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
                        $sort_array=array('title'=>1);  
                        $lookup=array(
                                'from' => 'oc_weight_class_description',
                                'localField' => 'weight_class_id',
                                'foreignField' => 'weight_class_id',
                                'as' => 'wcd'
                                );
                        $match=array('weight_class_id'=>(int)$weight_class_id,'wcd.language_id'=>(int)$this->config->get('config_language_id'));
                        $columns=array( 
                            "_id"=> 1,
                            "weight_class_id"=> 1,
                            "value"=>1,
                            "wcd.language_id"=>1,
                            "wcd.title"=> 1,
                            "wcd.unit"=>1,
                            );
                        $query = $this->db->query("join",DB_PREFIX . "weight_class",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array);
                        $return_array= array();
                        foreach($query->row as $row)
                        {
                            $return_array['weight_class_id']=$row['weight_class_id'];
                            $return_array['value']=$row['value'];
                            $return_array['language_id']=strip_tags($row['wcd'][0]['language_id']);
                            $return_array['title']=htmlentities($row['wcd'][0]['title']);
                            $return_array['unit']=$row['wcd'][0]['unit'];
                
                        }
                        
                        return $return_array;
	}

	public function getWeightClassDescriptionByUnit($unit) 
        {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class_description WHERE unit = '" . $this->db->escape($unit) . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                $sort_array=array();  
                $query = $this->db->query("select",DB_PREFIX . "weight_class_description",$this->db->escape($unit),'unit','','','',$limit,'',$start,$sort_array);
                return $query->row;
		
	}

	public function getWeightClassDescriptions($weight_class_id) 
        {
		$weight_class_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class_description WHERE weight_class_id = '" . (int)$weight_class_id . "'");
                $sort_array=array();  
                $query = $this->db->query("select",DB_PREFIX . "weight_class_description",(int)$weight_class_id,'weight_class_id','','','',$limit,'',$start,$sort_array);
                
		foreach ($query->rows as $result) 
                {
			$weight_class_data[$result['language_id']] = array(
				'title' => $result['title'],
				'unit'  => $result['unit']
			);
		}

		return $weight_class_data;
	}

	public function getTotalWeightClasses() 
        {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "weight_class");
                $match=array();
            
                $groupbyarray=array(
                 "_id"=> array('$weight_class_id'), 
                "total"=> array('$sum'=> 1 ) 
                );
            
                $query = $this->db->query('gettotalcount','oc_weight_class',$groupbyarray,$match);
                
		return $query->row[0]['total'];
		
	}
}