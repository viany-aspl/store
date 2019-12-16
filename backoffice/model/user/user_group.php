<?php
class ModelUserUserGroup extends Model {
	public function addUserGroup($data) 
        {
            //$this->db->query("INSERT INTO " . DB_PREFIX . "user_group SET name = '" . $this->db->escape($data['name']) . "', permission = '" . (isset($data['permission']) ? $this->db->escape(serialize($data['permission'])) : '') . "'");
            $user_group_id=$this->db->getNextSequenceValue('oc_user_group');
            $permission=(isset($data['permission']) ? $this->db->escape(serialize($data['permission'])) : ''); 
            $permission=str_replace('\\', '', $permission);
            $input_array=array(
                'user_group_id'=>(int)$user_group_id,
                'name'=>$this->db->escape($data['name']),
                'permission'=>$permission
                );
            
            $query = $this->db->query("insert",DB_PREFIX . "user_group",$input_array);
	}

	public function editUserGroup($user_group_id, $data) 
        {
            //$this->db->query("UPDATE " . DB_PREFIX . "user_group SET name = '" . $this->db->escape($data['name']) . "',permission = '" . (isset($data['permission']) ? $this->db->escape(serialize($data['permission'])) : '') . "' WHERE user_group_id = '" . (int)$user_group_id . "'");
            $input_array=array(
               'name'=>$this->db->escape($data['name']),
                'permission'=>  (isset($data['permission']) ? $this->db->escape(serialize($data['permission'])) : '')
                );
            $input_array=str_replace('\\', '', $input_array);
            //print_r($input_array);exit;
            $query = $this->db->query("update",DB_PREFIX . "user_group",array( 'user_group_id'=>(int)$user_group_id),$input_array);
	}

	public function deleteUserGroup($user_group_id) 
        {
            //$this->db->query("DELETE FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_group_id . "'");
            $query = $this->db->query("delete",DB_PREFIX . "user_group",array( 'user_group_id'=>(int)$user_group_id));
	}

	public function getUserGroup($user_group_id) 
        {
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_group_id . "'");
                $query=$this->db->query('select',DB_PREFIX . "user_group",'','','',array('user_group_id'=>(int)$user_group_id));
		//print_r($query);
                $user_group = array(
			'name'       => $query->row['name'],
			'permission' => unserialize($query->row['permission'])
		);

		return $user_group;
	}

	public function getUserGroups($data = array()) {
            /*
		$sql = "SELECT * FROM " . DB_PREFIX . "user_group";

		$sql .= " ORDER BY name";

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
                */
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
                $query = $this->db->query('select',DB_PREFIX . 'user_group','','','',$where,'',$limit,'',$start,array('name'=>1));
          
		return $query;
	}

        
	public function getTotalUserGroups() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user_group");

		return $query->row['total'];
	}

	public function addPermission($user_group_id, $type, $route) {
		$user_group_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_group_id . "'");

		if ($user_group_query->num_rows) {
			$data = unserialize($user_group_query->row['permission']);

			$data[$type][] = $route;

			$this->db->query("UPDATE " . DB_PREFIX . "user_group SET permission = '" . $this->db->escape(serialize($data)) . "' WHERE user_group_id = '" . (int)$user_group_id . "'");
		}
	}

	public function removePermission($user_group_id, $type, $route) {
		$user_group_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_group_id . "'");

		if ($user_group_query->num_rows) {
			$data = unserialize($user_group_query->row['permission']);

			$data[$type] = array_diff($data[$type], array($route));

			$this->db->query("UPDATE " . DB_PREFIX . "user_group SET permission = '" . $this->db->escape(serialize($data)) . "' WHERE user_group_id = '" . (int)$user_group_id . "'");
		}
	}
}