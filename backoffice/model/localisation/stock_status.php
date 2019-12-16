<?php
class ModelLocalisationStockStatus extends Model {
	public function addStockStatus($data) {
		foreach ($data['stock_status'] as $language_id => $value) {
			if (isset($stock_status_id)) 
                        {
                                //$this->db->query("INSERT INTO " . DB_PREFIX . "stock_status SET stock_status_id = '" . (int)$stock_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
                                
                                $input_array=array('stock_status_id'=>(int)$stock_status_id,
                                            'language_id'=>(int)$language_id,
                                            'name'=>$this->db->escape($value['name'])
                                        );
                                $query = $this->db->query("insert",DB_PREFIX . "stock_status",$input_array);
                                
			} 
                        else 
                        {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "stock_status SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");

				$stock_status_id =$this->db->getNextSequenceValue('oc_stock_status'); //$this->db->getLastId();
                                $input_array=array('stock_status_id'=>(int)$stock_status_id,
                                            'language_id'=>(int)$language_id,
                                            'name'=>$this->db->escape($value['name'])
                                        );
                                $query = $this->db->query("insert",DB_PREFIX . "stock_status",$input_array);
                                
			}
		}

		$this->cache->delete('stock_status');
	}

	public function editStockStatus($stock_status_id, $data) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . (int)$stock_status_id . "'");
                $where=array('stock_status_id'=>(int)$stock_status_id);
                $query = $this->db->query("delete",DB_PREFIX . "stock_status",$where);
		foreach ($data['stock_status'] as $language_id => $value) 
                {
                    $input_array=array('stock_status_id'=>(int)$stock_status_id,
                        'language_id'=>(int)$language_id,
                        'name'=>$this->db->escape($value['name'])
                            );
                    $query = $this->db->query("insert",DB_PREFIX . "stock_status",$input_array);
                    //$this->db->query("INSERT INTO " . DB_PREFIX . "stock_status SET stock_status_id = '" . (int)$stock_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		$this->cache->delete('stock_status');
	}

	public function deleteStockStatus($stock_status_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . (int)$stock_status_id . "'");
                $where=array('stock_status_id'=>(int)$stock_status_id);
                $query = $this->db->query("delete",DB_PREFIX . "stock_status",$where);
		$this->cache->delete('stock_status');
	}

	public function getStockStatus($stock_status_id) 
        {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . (int)$stock_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                $sort_array=array('name'=>1);  
                $query = $this->db->query("select",DB_PREFIX . "stock_status",(int)$stock_status_id,'stock_status_id','','','',$limit,'',$start,$sort_array);
            
		return $query->row;
	}

	public function getStockStatuses($data = array()) {
		if ($data) 
                {
                        /*
			$sql = "SELECT * FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sql .= " ORDER BY name";

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
                                        $limit = $data['limit'];
                                }
				//$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			//$query = $this->db->query($sql);
                        
                        $sort_array=array('name'=>1);  
                        $query = $this->db->query("select",DB_PREFIX . "stock_status",(int)$this->config->get('config_language_id'),'language_id','','','',$limit,'',$start,$sort_array);
            
			return $query->rows;
		} 
                else 
                {
			$stock_status_data = $this->cache->get('stock_status.' . (int)$this->config->get('config_language_id'));

			if (!$stock_status_data) {
				//$query = $this->db->query("SELECT stock_status_id, name FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
                                $sort_array=array('name'=>1);  
                                $query = $this->db->query("select",DB_PREFIX . "stock_status",(int)$this->config->get('config_language_id'),'language_id','','','',$limit,'',$start,$sort_array);
            
				$stock_status_data = $query->rows;

				$this->cache->set('stock_status.' . (int)$this->config->get('config_language_id'), $stock_status_data);
			}

			return $stock_status_data;
		}
	}

	public function getStockStatusDescriptions($stock_status_id) {
		$stock_status_data = array();
                $sort_array=array();  
                $query = $this->db->query("select",DB_PREFIX . "stock_status",(int)$stock_status_id,'stock_status_id','','','',$limit,'',$start,$sort_array);
            
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		foreach ($query->rows as $result) 
                {
			$stock_status_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $stock_status_data;
	}

	public function getTotalStockStatuses() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
                $match['language_id']=(int)$this->config->get('config_language_id');
            
                $groupbyarray=array(
                 "_id"=> array('$stock_status_id'), 
                "total"=> array('$sum'=> 1 ) 
                );
            
                $query = $this->db->query('gettotalcount','oc_stock_status',$groupbyarray,$match);
                
		return $query->row[0]['total'];
	}
}