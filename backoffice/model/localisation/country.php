<?php
class ModelLocalisationCountry extends Model {
	public function addCountry($data) {
               
            $country_id=$this->db->getNextSequenceValue('oc_country');
                        $input_array=array('country_id' =>(int)$country_id,'name'=>$this->db->escape($data['name']),'iso_code_2'=>$this->db->escape($data['iso_code_2']),'iso_code_3'=>$this->db->escape($data['iso_code_3']),'address_format'=>$this->db->escape($data['address_format']),'postcode_required'=>(int)$data['postcode_required'],'status'=>(int)$data['status']);
		$this->db->query("insert" , DB_PREFIX . "country",$input_array);

		$this->cache->delete('country');
	}

	public function editCountry($country_id, $data) {
		$this->db->query("update" , DB_PREFIX . "country",array('country_id' =>(int)$country_id),array('name'=>$this->db->escape($data['name']),'iso_code_2'=>$this->db->escape($data['iso_code_2']),'iso_code_3'=>$this->db->escape($data['iso_code_3']),'address_format'=>$this->db->escape($data['address_format']),'postcode_required'=>(int)$data['postcode_required'],'status'=>(int)$data['status']));

		$this->cache->delete('country');
	}

	public function deleteCountry($country_id) {
		$this->db->query("delete" , DB_PREFIX . "country",array('country_id'=>(int)$country_id));

		$this->cache->delete('country');
	}

	public function getCountry($country_id) {
		$query = $this->db->query("select",DB_PREFIX . "country",array('country_id'=>(int)$country_id));

		return $query->row;
	}

	public function getCountries($data = array()) {
		if ($data) 
                {
                        
                        if (isset($data['start']) || isset($data['limit'])) 
                        {
                            if ($data['start'] < 0) 
                            {
                                $start = 0;
                            }
                            else 
                            {
                                $start = (int)$data['start'];
                            }
                            if ($data['limit'] < 1) 
                            {
                                $limit = 20;
                            }
                            else 
                            {
                                $limit = (int)$data['limit'];
                            }
			
                        }
                        $query = $this->db->query('select',DB_PREFIX . 'country','','','','','',$limit,'',$start,array('name'=>1));
  
			return $query;
                        
		} 
                else 
                {
			$country_data = $this->cache->get('country');

			if (!$country_data) {
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country ORDER BY name ASC");
                                $query = $this->db->query('select',DB_PREFIX . 'country','','','','','',$limit,'',$start,array('name'=>1));
				$country_data = $query;

				$this->cache->set('country', $country_data);
			}

			return $country_data;
		}
	}

}