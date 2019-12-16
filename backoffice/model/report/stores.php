<?php
class ModelReportStores extends Model {
	
	
	public function getStores($data = array()) {
	 $match=array();
          $where=array();
	
 	if($data['filter_store']!="")
	{
		
                $match['store_id']=(int)$data['filter_store'];
                 $where['store_id']=(int)$data['filter_store'];
	}
	if($data['filter_name']!="")
	{
		
                $search_string=$data['filter_name'];
                $match['firstname']=new MongoRegex("/.*$search_string/i");
                 $where['firstname']=new MongoRegex("/.*$search_string/i");
	}
        $match['user_group_id']=11;
         $lookup=array(
                'from' => 'oc_user_group',
                'localField' => 'user_group_id',
                'foreignField' => 'user_group_id',
                'as' => 'user_group'
            );
       
       
       
        $where['user_group_id']=11;
        
            if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query("join",DB_PREFIX . "user",$lookup,'$user_group',$match,'','',(int)$data['limit'],'',(int)$data['start']);        
               	$query->total_rows=$this->db->getcount('oc_user',$where);
		return $query;
	}

	public function getTotalStores($data = array()) {
                $sql="select count(*) as total from (SELECT oc_user.firstname,oc_user.lastname,oc_user.username,oc_user.store_id,oc_store.name FROM shop.oc_user join oc_store on oc_user.store_id=oc_store.store_id where user_group_id in (11,36) ";

		if($data['filter_store']!="")
		{
		$sql.=" and oc_store.store_id='".$data['filter_store']."' ";
		}
		if($data['filter_name']!="")
		{
		$sql.=" and concat(oc_user.firstname,' ',oc_user.lastname)  like '%".$data['filter_name']."%' ";
		}
		$sql.=" ) as bb ";
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	 public function getStores_companywise($data = array()) {
	
	$sql="SELECT oc_user.firstname,oc_user.lastname,oc_user.username,oc_user.store_id,oc_store.name FROM shop.oc_user join oc_store on oc_user.store_id=oc_store.store_id where user_group_id='11' "; 
 
        $sql .=" AND oc_store.company_id='".$data['filter_company']."' ";
	if($data['filter_store']!="")
		{
		$sql.=" and oc_store.store_id='".$data['filter_store']."' ";
		}
		if($data['filter_name']!="")
		{
		$sql.=" and concat(oc_user.firstname,' ',oc_user.lastname)  like '%".$data['filter_name']."%' ";
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
              // 	echo $sql; 
		return $query->rows;
	}
        
        public function getTotalStores_companywise($data = array()) {
                $sql="select count(*) as total from (SELECT oc_user.firstname,oc_user.lastname,oc_user.username,oc_user.store_id,oc_store.name "
                        . "FROM shop.oc_user "
                        . "join oc_store on oc_user.store_id=oc_store.store_id "
                        . "where user_group_id='11'";

                
        $sql .=" and oc_store.company_id='".$data['filter_company']."' ";
	if($data['filter_store']!="")
		{
		$sql.=" and oc_store.store_id='".$data['filter_store']."' ";
		}
		if($data['filter_name']!="")
		{
		$sql.=" and concat(oc_user.firstname,' ',oc_user.lastname)  like '%".$data['filter_name']."%' ";
		}
            $sql .= ") as bb";
			//echo $sql;
        $query = $this->db->query($sql);

		return $query->row['total'];
	}

}