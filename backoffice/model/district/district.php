<?php
class ModelDistrictDistrict extends Model 
{
	
	public function get_all_districts($data)
	{
		$where=array('zone_id'=>1505);
		if (!empty($data['name'])) 
		{
			$search_string=$data['name'];
            $where['name'] = new MongoRegex("/.*$search_string/i");
			
		}
		
        if (isset($data['start']) || isset($data['limit'])) 
		{
			if ($data['start'] < 0) 
			{
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) 
			{
				$data['limit'] = 20;
			}

		} 
 
		$query = $this->db->query("select","oc_zone_to_district",'','','',$where,'',(int)$data['limit'],'',(int)$data['start']);
		
		return $query;
	}
	
}
?>