<?php
class ModelLocalisationOrderStatus extends Model {
	public function addOrderStatus($data) 
        {
		foreach ($data['order_status'] as $language_id => $value) {
			if (isset($order_status_id)) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "order_status SET order_status_id = '" . (int)$order_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			       $order_status_id=$this->db->getNextSequenceValue('oc_order_status');
              $fdata=array(
                          'order_status_id'=>(int)$order_status_id ,
                               'language_id'=>(int)$language_id,
                               'name'=>$this->db->escape($value['name'])
                          );
                             $this->db->query('insert',DB_PREFIX . 'order_status',$fdata); 
                            
                        } else {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "order_status SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");

				//$order_status_id = $this->db->getLastId();
                            
                             $order_status_id=$this->db->getNextSequenceValue('oc_order_status');
              $fdata=array(
                          'order_status_id'=>(int)$order_status_id ,
                               'language_id'=>(int)$language_id,
                               'name'=>$this->db->escape($value['name'])
                          );
                             $this->db->query('insert',DB_PREFIX . 'order_status',$fdata); 
			}
		}

		$this->cache->delete('order_status');
	}

	public function editOrderStatus($order_status_id, $data) {
		$this->db->query("delete",DB_PREFIX . "order_status",array('order_status_id'=>(int)$order_status_id));

		foreach ($data['order_status'] as $language_id => $value) {
			$this->db->query("insert" , DB_PREFIX . "order_status",array('order_status_id'=>(int)$order_status_id,'language_id'=>(int)$language_id,'name'=>$this->db->escape($value['name'])));
		}

		$this->cache->delete('order_status');
	}

	public function deleteOrderStatus($order_status_id) {
		$this->db->query("delete",DB_PREFIX . "order_status",array('order_status_id'=>(int)$order_status_id));

		$this->cache->delete('order_status');
	}

	public function getOrderStatus($order_status_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                $where=array('order_status_id'=>(int)$order_status_id );
                $query = $this->db->query('select',DB_PREFIX . 'order_status','','','',$where,'','','','',array('name'=>1));
		return $query->row;
	}

	public function getOrderStatuses($data = array()) {
		if ($data) {
                    
                    if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
                        $limit=(int)$data['limit'];
			$start=(int)$data['start'];
		}
                $where=array('language_id'=>(int)$this->config->get('config_language_id'));
                $query = $this->db->query('select',DB_PREFIX . 'order_status','','','',$where,'',$limit,'',$start,array('name'=>1));
          
			return $query;
		} else {
			$order_status_data = $this->cache->get('order_status.' . (int)$this->config->get('config_language_id'));

			if (!$order_status_data) {
				//$query = $this->db->query("SELECT order_status_id, name FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
                                $where=array('language_id'=>(int)$this->config->get('config_language_id'));
                                $query = $this->db->query('select',DB_PREFIX . 'order_status','','','',$where,'',$limit,'',$start,array('name'=>1));
          
				$order_status_data = $query;

				$this->cache->set('order_status.' . (int)$this->config->get('config_language_id'), $order_status_data);
			}

			return $order_status_data;
		}
	}

//

	public function getOrderStatusesTag($data = array()) {
		if ($data) {
                       
                    if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
                        $limit=(int)$data['limit'];
			$start=(int)$data['start'];
		}
        $where=array();
        $query = $this->db->query('select',DB_PREFIX . 'order_status','','','',$where,'',$limit,'',$start,array('name'=>1));
          
			return $query->rows;
		} else 
                    
                {
			
                    //$query = $this->db->query("SELECT order_status_id, name FROM " . DB_PREFIX . "order_status WHERE order_status_id in ('1','5') AND language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
                    $query = $this->db->query('select',DB_PREFIX . 'order_status','','','',$where,'',$limit,'',$start,array('name'=>1));
                    $order_status_data = $query->rows;


			}

			return $order_status_data;
		}
	
//

	public function getOrderStatusDescriptions($order_status_id) {
		$order_status_data = array();

		$query = $this->db->query("select",DB_PREFIX . "order_status",array('order_status_id'=>(int)$order_status_id));

		foreach ($query->rows as $result) {
			$order_status_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $order_status_data;
	}

}