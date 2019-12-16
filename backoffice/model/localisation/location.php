<?php
    class ModelLocalisationLocation extends Model 
    {
	public function addLocation($data) 
        {           
                $data["location_id"]=(int)$this->db->getNextSequenceValue(DB_PREFIX . "location");
                $this->db->query("INSERT" , DB_PREFIX . "location",$data );
	}

	public function editLocation($location_id, $data) 
        {		
            $this->db->query("update " , DB_PREFIX . "location",array('location_id'=>(int)$location_id),$data);    
	}

	public function deleteLocation($location_id) {
		$this->db->query("delete",DB_PREFIX . "location", array('location_id' =>(int)$location_id));
	}

	public function getLocation($location_id) 
        {
            $query = $this->db->query('select',DB_PREFIX . 'location','','','',array('location_id' =>(int)$location_id),'',1,'',0,array('name'=>1));
            return $query->row;
	}

	public function getLocationStore($location_id) 
        {
		$query = $this->db->query("SELECT",  DB_PREFIX . "location",'','','', array('fax' =>$location_id ));

		return $query->row;
	}



	public function getLocations($data = array()) 
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
                $query = $this->db->query('select',DB_PREFIX . 'location','','','','','',$limit,'',$start,array('name'=>1));
  
		return $query;
	}

}
