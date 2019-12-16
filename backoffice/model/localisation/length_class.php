<?php
class ModelLocalisationLengthClass extends Model {
	public function addLengthClass($data) 
        {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "length_class SET 
                //value = '" . (float)$data['value'] . "'");
                
                $length_class_id =$this->db->getNextSequenceValue('oc_length_class'); 
                $input_array=array('length_class_id'=>(int)$length_class_id,
                                    'value'=>(float)$data['value']
                                );
                $query = $this->db->query("insert",DB_PREFIX . "length_class",$input_array);
		foreach ($data['length_class_description'] as $language_id => $value) 
                {
                    //$this->db->query("INSERT INTO " . DB_PREFIX . "length_class_description SET length_class_id = '" . (int)$length_class_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', unit = '" . $this->db->escape($value['unit']) . "'");
                    $input_array2=array('length_class_id'=>(int)$length_class_id,
                                        'language_id'=>(int)$language_id,
                                        'title'=>$this->db->escape($value['title']),
                                        'unit'=>$this->db->escape($value['unit'])
                                );
                    $query = $this->db->query("insert",DB_PREFIX . "length_class_description",$input_array2);
                 
		}

		$this->cache->delete('length_class');
	}

	public function editLengthClass($length_class_id, $data) 
        {
		//$this->db->query("UPDATE " . DB_PREFIX . "length_class SET value = '" . (float)$data['value'] . "' WHERE length_class_id = '" . (int)$length_class_id . "'");
                $where=array('length_class_id'=>(int)$length_class_id);
                $input_array=array(
                                    'value'=>(float)$data['value']
                                );
                $query = $this->db->query("update",DB_PREFIX . "length_class",$where,$input_array);
                $query = $this->db->query("delete",DB_PREFIX . "length_class_description",$where);
		//$this->db->query("DELETE FROM " . DB_PREFIX . "length_class_description WHERE length_class_id = '" . (int)$length_class_id . "'");

		foreach ($data['length_class_description'] as $language_id => $value) 
                {
                    //$this->db->query("INSERT INTO " . DB_PREFIX . "length_class_description SET length_class_id = '" . (int)$length_class_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', unit = '" . $this->db->escape($value['unit']) . "'");
                    $input_array2=array('length_class_id'=>(int)$length_class_id,
                                        'language_id'=>(int)$language_id,
                                        'title'=>$this->db->escape($value['title']),
                                        'unit'=>$this->db->escape($value['unit'])
                                );
                    $query = $this->db->query("insert",DB_PREFIX . "length_class_description",$input_array2);
                 
                    
                }

		$this->cache->delete('length_class');
	}

	public function deleteLengthClass($length_class_id) 
        {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "length_class WHERE length_class_id = '" . (int)$length_class_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "length_class_description WHERE length_class_id = '" . (int)$length_class_id . "'");
                $where=array('length_class_id'=>(int)$length_class_id);
                $query = $this->db->query("delete",DB_PREFIX . "length_class",$where);
                $query = $this->db->query("delete",DB_PREFIX . "length_class_description",$where);
		$this->cache->delete('length_class');
	}

	public function getLengthClasses($data = array()) 
        {
		if ($data) 
                {
                        /*
			$sql = "SELECT * FROM " . DB_PREFIX . "length_class lc LEFT JOIN 
                         * " . DB_PREFIX . "length_class_description lcd 
                         * ON (lc.length_class_id = lcd.length_class_id) 
                         * WHERE lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

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

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->db->query($sql);

			return $query->rows;*/
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
                                'from' => 'oc_length_class_description',
                                'localField' => 'length_class_id',
                                'foreignField' => 'length_class_id',
                                'as' => 'lcd'
                                );
                        $match=array('lcd.language_id'=>(int)$this->config->get('config_language_id'));
                        $columns=array( 
                            "_id"=> 1,
                            "length_class_id"=> 1,
                            "value"=>1,
                            "lcd.language_id"=>1,
                            "lcd.title"=> 1,
                            "lcd.unit"=>1,
                            );
                        $query = $this->db->query("join",DB_PREFIX . "length_class",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array);
                        $return_array= array();
                        $a=0;
                        foreach($query->row as $row)
                        {
                            //print_r($row['lcd'][0]['title']);
                            $return_array[$a]['length_class_id']=$row['length_class_id'];
                            $return_array[$a]['value']=$row['value'];
                            $return_array[$a]['language_id']=strip_tags($row['lcd'][0]['language_id']);
                            $return_array[$a]['title']=($row['lcd'][0]['title']);
                            $return_array[$a]['unit']=$row['lcd'][0]['unit'];
                            $a++;
                        }
			return $return_array;
		} 
                else 
                {
			$length_class_data = $this->cache->get('length_class.' . (int)$this->config->get('config_language_id'));

			if (!$length_class_data) 
                        {
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class lc LEFT JOIN " . DB_PREFIX . "length_class_description lcd ON (lc.length_class_id = lcd.length_class_id) WHERE lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

				//$length_class_data = $query->rows;
                                $sort_array=array('title'=>1);  
                        $lookup=array(
                                'from' => 'oc_length_class_description',
                                'localField' => 'length_class_id',
                                'foreignField' => 'length_class_id',
                                'as' => 'lcd'
                                );
                        $match=array('lcd.language_id'=>(int)$this->config->get('config_language_id'));
                        $columns=array( 
                            "_id"=> 1,
                            "length_class_id"=> 1,
                            "value"=>1,
                            "lcd.language_id"=>1,
                            "lcd.title"=> 1,
                            "lcd.unit"=>1,
                            );
                        $query = $this->db->query("join",DB_PREFIX . "length_class",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array);
                        $return_array= array();
                         $a=0;
                        foreach($query->row as $row)
                        {
                            //print_r($row['lcd'][0]['title']);
                            $return_array[$a]['length_class_id']=$row['length_class_id'];
                            $return_array[$a]['value']=$row['value'];
                            $return_array[$a]['language_id']=strip_tags($row['lcd'][0]['language_id']);
                            $return_array[$a]['title']=($row['lcd'][0]['title']);
                            $return_array[$a]['unit']=$row['lcd'][0]['unit'];
                            $a++;
                        }

				$this->cache->set('length_class.' . (int)$this->config->get('config_language_id'), $length_class_data);
			}

			return $return_array;
		}
	}

	public function getLengthClass($length_class_id) 
        {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class lc LEFT JOIN 
                //" . DB_PREFIX . "length_class_description lcd ON (lc.length_class_id = lcd.length_class_id) WHERE lc.length_class_id = '" . (int)$length_class_id . "' AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		//return $query->row;
                        $sort_array=array('title'=>1);  
                        $lookup=array(
                                'from' => 'oc_length_class_description',
                                'localField' => 'length_class_id',
                                'foreignField' => 'length_class_id',
                                'as' => 'lcd'
                                );
                        $match=array('length_class_id'=>(int)$length_class_id,'lcd.language_id'=>(int)$this->config->get('config_language_id'));
                        $columns=array( 
                            "_id"=> 1,
                            "weight_class_id"=> 1,
                            "value"=>1,
                            "wcd.language_id"=>1,
                            "wcd.title"=> 1,
                            "wcd.unit"=>1,
                            );
                        $query = $this->db->query("join",DB_PREFIX . "length_class",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array);
                        $return_array= array();
                        foreach($query->row as $row)
                        {
                            $return_array['length_class_id']=$row['length_class_id'];
                            $return_array['value']=$row['value'];
                            $return_array['language_id']=strip_tags($row['lcd'][0]['language_id']);
                            $return_array['title']=htmlentities($row['lcd'][0]['title']);
                            $return_array['unit']=$row['lcd'][0]['unit'];
                
                        }
                        
                        return $return_array;
	}

	public function getLengthClassDescriptionByUnit($unit) 
        {
            //$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class_description WHERE unit = '" . $this->db->escape($unit) . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

            //return $query->row;
            $sort_array=array();  
            $query = $this->db->query("select",DB_PREFIX . "length_class_description",$this->db->escape($unit),'unit','','','',$limit,'',$start,$sort_array);
            return $query->row;
	}

	public function getLengthClassDescriptions($length_class_id) 
        {
		$length_class_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class_description WHERE length_class_id = '" . (int)$length_class_id . "'");
                $sort_array=array();  
                $query = $this->db->query("select",DB_PREFIX . "length_class_description",(int)$length_class_id,'length_class_id','','','',$limit,'',$start,$sort_array);
                
		foreach ($query->rows as $result) 
                {
			$length_class_data[$result['language_id']] = array(
				'title' => $result['title'],
				'unit'  => $result['unit']
			);
		}

		return $length_class_data;
	}

	public function getTotalLengthClasses() 
        {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "length_class");

		//return $query->row['total'];
                $match=array();
            
                $groupbyarray=array(
                 "_id"=> array('$length_class_id'), 
                "total"=> array('$sum'=> 1 ) 
                );
            
                $query = $this->db->query('gettotalcount','oc_length_class',$groupbyarray,$match);
                
		return $query->row[0]['total'];
	}
}