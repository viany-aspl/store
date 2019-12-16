<?php
class ModelCatalogManufacturer extends Model 
{
	public function addManufacturer($data) 
	{
		$this->event->trigger('pre.admin.manufacturer.add', $data);
		$manufacturer_id=$this->db->getNextSequenceValue('oc_manufacturer');
		
		$this->db->query("insert", DB_PREFIX . "manufacturer",array('manufacturer_id'=>(int)$manufacturer_id,'name'=>$this->db->escape($data['name']),'sort_order'=>(int)$data['sort_order']));
		$where=array('manufacturer_id'=>(int)$manufacturer_id);
		if (isset($data['image'])) 
		{
			$this->db->query("update",DB_PREFIX . "manufacturer",$where,array('image'=>$this->db->escape($data['image'])));
		}

		if (isset($data['manufacturer_store'])) 
		{
			foreach ($data['manufacturer_store'] as $store_id) 
			{
				$this->db->query("insert",DB_PREFIX . "manufacturer_to_store",array('manufacturer_id'=>(int)$manufacturer_id,'store_id'=>(int)$store_id));
			}
		}

		if (isset($data['keyword'])) 
		{
			$this->db->query("insert",DB_PREFIX . "url_alias",array('query'=>'manufacturer_id='.(int)$manufacturer_id,'keyword'=>$this->db->escape($data['keyword'])));
		}

		$this->cache->delete('manufacturer');

		$this->event->trigger('post.admin.manufacturer.add', $manufacturer_id);

		return $manufacturer_id;
	}

	public function editManufacturer($manufacturer_id, $data) 
	{
		$this->event->trigger('pre.admin.manufacturer.edit', $data);
		$upd_data=array('name'=>$this->db->escape($data['name']),'sort_order'=>(int)$data['sort_order']);
		$where=array('manufacturer_id'=>(int)$manufacturer_id);
		$this->db->query("update",DB_PREFIX . "manufacturer",$where,$upd_data);

		if (isset($data['image'])) 
		{
			//print_r($data);exit;
			$this->db->query("update",DB_PREFIX . "manufacturer",$where,array('image'=>$this->db->escape($data['image'])));
		}

		$this->db->query("delete",DB_PREFIX . "manufacturer_to_store",array('manufacturer_id'=>(int)$manufacturer_id));
		if (isset($data['manufacturer_store'])) 
		{
			foreach ($data['manufacturer_store'] as $store_id) 
			{
				$this->db->query("insert",DB_PREFIX . "manufacturer_to_store",array('manufacturer_id'=>(int)$manufacturer_id,'store_id'=>(int)$store_id));
			}
		}
		$this->db->query("delete",DB_PREFIX . "url_alias",array('query'=>'manufacturer_id='.(int)$manufacturer_id));
		
		if ($data['keyword']) 
		{
			$this->db->query("insert",DB_PREFIX . "url_alias",array('query'=>'manufacturer_id='.(int)$manufacturer_id,'keyword'=>$this->db->escape($data['keyword'])));
		}

		$this->cache->delete('manufacturer');

		$this->event->trigger('post.admin.manufacturer.edit');
	}

	public function deleteManufacturer($manufacturer_id) 
	{
		$this->event->trigger('pre.admin.manufacturer.delete', $manufacturer_id);

		$this->db->query("delete",DB_PREFIX . "manufacturer",array('manufacturer_id'=>(int)$manufacturer_id));
		$this->db->query("delete",DB_PREFIX . "manufacturer_to_store",array('manufacturer_id'=>(int)$manufacturer_id));
		$this->db->query("delete",DB_PREFIX . "url_alias",array('query'=>'manufacturer_id='.(int)$manufacturer_id));
		
		$this->cache->delete('manufacturer');

		$this->event->trigger('post.admin.manufacturer.delete', $manufacturer_id);
	}

	public function getManufacturer($manufacturer_id) 
    {
        
		$query = $this->db->query('select',DB_PREFIX . "manufacturer",'','','',array('manufacturer_id'=>(int)$manufacturer_id),'',1,'',0,$sort_array);
        return $query->row;
	}

	public function getManufacturers($data = array()) 
	{
		$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer";

		if (!empty($data['filter_name'])) 
		{
			$search_string=$this->db->escape($data['filter_name']);
            $match['name']=new MongoRegex("/.*$search_string/i");
			
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
		$sort_array=array('name'=>1);
		$query = $this->db->query('select',DB_PREFIX . "manufacturer",'','','',$match,'',$limit,'',$start,$sort_array);

		return $query;
	}

	public function getManufacturerStores($manufacturer_id) 
	{
		$manufacturer_store_data = array();

		$query = $this->db->query('select',DB_PREFIX . "manufacturer_to_store",'','','',array('manufacturer_id'=>(int)$manufacturer_id));

		foreach ($query->rows as $result) 
		{
			$manufacturer_store_data[] = $result['store_id'];
		}

		return $manufacturer_store_data;
	}
	public function getManufacturerProduct($manufacturer_id) 
	{
		$manufacturer_store_data = array();

		$query = $this->db->query('select',DB_PREFIX . "product",'','','',array('manufacturer_id'=>(int)$manufacturer_id));

		return $query;
	}
	
}