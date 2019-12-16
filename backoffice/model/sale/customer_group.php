<?php
class ModelSaleCustomerGroup extends Model {
	public function addCustomerGroup($data) 
        {
            $customer_group_id=$this->db->getNextSequenceValue('oc_customer_group');
            $this->db->query("insert" , DB_PREFIX . "customer_group",array('customer_group_id'=>(int)$customer_group_id,'approval'=>(int)$data['approval'],'sort_order'=>(int)$data['sort_order']));
            foreach ($data['customer_group_description'] as $language_id => $value) 
            {
		$this->db->query("insert" , DB_PREFIX . "customer_group_description",array('customer_group_id'=>(int)$customer_group_id,'language_id'=>(int)$language_id,'name'=>$this->db->escape($value['name']),'description'=>$this->db->escape($value['description'])));
            }
	}

	public function editCustomerGroup($customer_group_id, $data) 
        {
            $this->db->query("update" , DB_PREFIX . "customer_group",array('customer_group_id' =>(int)$customer_group_id),array('approval'=>(int)$data['approval'],'sort_order'=>(int)$data['sort_order']));
            $this->db->query("delete" , DB_PREFIX . "customer_group_description",array('customer_group_id' =>(int)$customer_group_id));
            foreach ($data['customer_group_description'] as $language_id => $value) 
            {
		$this->db->query("insert" , DB_PREFIX . "customer_group_description",array('customer_group_id'=>(int)$customer_group_id,'language_id'=>(int)$language_id,'name'=>$this->db->escape($value['name']),'description'=>$this->db->escape($value['description'])));
            }
	}

	public function deleteCustomerGroup($customer_group_id) 
        {
            $this->db->query("delete" , DB_PREFIX . "customer_group",array('customer_group_id' =>(int)$customer_group_id));
            $this->db->query("delete" , DB_PREFIX . "customer_group_description",array('customer_group_id' =>(int)$customer_group_id));
            $this->db->query("delete" , DB_PREFIX . "product_discount",array('customer_group_id' =>(int)$customer_group_id));
            $this->db->query("delete" , DB_PREFIX . "product_special",array('customer_group_id' =>(int)$customer_group_id));
            $this->db->query("delete" , DB_PREFIX . "product_reward",array('customer_group_id' =>(int)$customer_group_id));
            
	}

	public function getCustomerGroup($customer_group_id) 
        {
            $lookup=array(
                'from' => 'oc_customer_group_description',
                'localField' => 'customer_group_id',
                'foreignField' => 'customer_group_id',
                'as' => 'cgd'
            );
            $match=array('cgd.language_id'=>(int)$this->config->get('config_language_id'),'customer_group_id'=>(int)$customer_group_id);
            $limit=(int)1;
            $start=(int)0;
		
            $sort_array=array('cgd.name'=>1);
            $query = $this->db->query("join",DB_PREFIX . "customer_group",$lookup,'$cgd',$match,'','',$limit,'',$start,$sort_array);
            return $query->row;
	}

	public function getCustomerGroups($data = array()) 
        {
            $lookup=array(
                'from' => 'oc_customer_group_description',
                'localField' => 'customer_group_id',
                'foreignField' => 'customer_group_id',
                'as' => 'cgd'
            );
            $match=array('cgd.language_id'=>(int)$this->config->get('config_language_id'));
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
            $sort_array=array('cgd.name'=>1);
            $query = $this->db->query("join",DB_PREFIX . "customer_group",$lookup,'$cgd',$match,'','',$limit,'',$start,$sort_array);
            return ($query);
            
	}

	public function getCustomerGroupDescriptions($customer_group_id) {
		$customer_group_data = array();

		$query = $this->db->query("select" , DB_PREFIX . "customer_group_description",array('customer_group_id'=> (int)$customer_group_id));

		foreach ($query->rows as $result) {
			$customer_group_data[$result['language_id']] = array(
				'name'        => $result['name'],
				'description' => $result['description']
			);
		}

		return $customer_group_data;
	}

	public function getTotalCustomerGroups() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_group");

		return $query->row['total'];
	}
}