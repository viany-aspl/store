<?php
class ModelLocalisationTaxRate extends Model {
	public function addTaxRate($data) 
        {
            $tax_rate_id=$this->db->getNextSequenceValue('oc_tax_rate');
            $input_array=array('tax_rate_id'=>$tax_rate_id,
                                'name'=>$this->db->escape($data['name']),
                                'rate'=>(float)$data['rate'],
                                'type'=>$this->db->escape($data['type']),
                                'geo_zone_id'=>(int)$data['geo_zone_id'],
                                'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))), 
                                'date_modified'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                );
            $this->db->query("insert" , DB_PREFIX . "tax_rate",$input_array);
            if (isset($data['tax_rate_customer_group'])) 
            {
			foreach ($data['tax_rate_customer_group'] as $customer_group_id) 
                        {
				$this->db->query("insert" , DB_PREFIX . "tax_rate_to_customer_group",array('tax_rate_id'=>(int)$tax_rate_id,'customer_group_id'=>(int)$customer_group_id));
			}
		}
	}

	public function editTaxRate($tax_rate_id, $data) 
        {
            $input_array=array(
                                'name'=>$this->db->escape($data['name']),
                                'rate'=>(float)$data['rate'],
                                'type'=>$this->db->escape($data['type']),
                                'geo_zone_id'=>(int)$data['geo_zone_id'],
                                'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))), 
                                'date_modified'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                );
        
		$this->db->query("update" , DB_PREFIX . "tax_rate",array('tax_rate_id'=>(int)$tax_rate_id),$input_array);

		$this->db->query("delete" , DB_PREFIX . "tax_rate_to_customer_group",array('tax_rate_id'=>(int)$tax_rate_id));

		if (isset($data['tax_rate_customer_group'])) {
			foreach ($data['tax_rate_customer_group'] as $customer_group_id) {
				$this->db->query("insert" , DB_PREFIX . "tax_rate_to_customer_group",array('tax_rate_id'=>(int)$tax_rate_id,'customer_group_id'=>(int)$customer_group_id));
			}
		}
	}

	public function deleteTaxRate($tax_rate_id) 
        {
		$this->db->query("delete" , DB_PREFIX . "tax_rate",array('tax_rate_id'=>(int)$tax_rate_id));
		$this->db->query("delete" , DB_PREFIX . "tax_rate_to_customer_group",array('tax_rate_id'=>(int)$tax_rate_id));
	}

	public function getTaxRate($tax_rate_id) {
		//$query = $this->db->query("SELECT tr.tax_rate_id, tr.name AS name, tr.rate, tr.type, tr.geo_zone_id, gz.name AS geo_zone, tr.date_added, tr.date_modified FROM " . DB_PREFIX . "tax_rate tr LEFT JOIN " . DB_PREFIX . "geo_zone gz ON (tr.geo_zone_id = gz.geo_zone_id) WHERE tr.tax_rate_id = '" . (int)$tax_rate_id . "'");
                $query = $this->db->query("select",DB_PREFIX . "tax_rate",'','','',array('tax_rate_id'=>(int)$tax_rate_id),'',$limit,$columns,$start,'','');
            
            
		return $query->row;
	}

	public function getTaxRates($data = array()) {
            
            if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$start = 0;
			}
                        else
                        {
                            $start=(int)$data['start'];
                        }
			if ($data['limit'] < 1) {
				$limit= 20;
			}
                        else
                        {
                            $limit=(int)$data['limit'];
                        }
			
		}
            $tax_query = $this->db->query("select",DB_PREFIX . "tax_rate",'','','','','',$limit,$columns,$start,'','');
            
            return $tax_query;
	}

	public function getTaxRateCustomerGroups($tax_rate_id) {
		$tax_customer_group_data = array();

		$query = $this->db->query("select" , DB_PREFIX . "tax_rate_to_customer_group",array('tax_rate_id'=>(int)$tax_rate_id));
                //print_r($query->rows);
		foreach ($query->rows as $result) {
			$tax_customer_group_data[] = $result['customer_group_id'];
		}

		return $tax_customer_group_data;
	}

}