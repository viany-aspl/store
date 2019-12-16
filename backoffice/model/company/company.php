<?php

class ModelCompanyCompany extends Model {

    public function addcompany($data) 
    {
        $company_id=$this->db->getNextSequenceValue('oc_company');
        //$sql2="insert into  oc_company set company_name='".$data["company_name"]."'";
        //$query2 = $this->db->query($sql2);
        $input_array=array(
                    'company_id'=>(int)$company_id,
                    'is_active'=> boolval(1),
                    'company_name'=>$data["company_name"]
                        );
                
        $query = $this->db->query("insert",DB_PREFIX . "company",$input_array);
                
    }


    public function getcompany($data = array()) {

        //$sql = "SELECT  * FROM " . DB_PREFIX . "company ";
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
        $query = $this->db->query('select',DB_PREFIX . 'company','','','',$where,'',$limit,'',$start,array());
                
        //$query = $this->db->query($sql);
        // echo $sql;exit;
        return $query;
    }
    
    public function getcompanyName($data = array()) 
    {

        //$sql = "SELECT  * FROM " . DB_PREFIX . "company ";
	
        //$query = $this->db->query($sql);
        $where=array();
        $query = $this->db->query('select',DB_PREFIX . 'company','','','',$where,'',$limit,'',$start,array());
         
        return $query->rows;
    }

    public function getTotalcompany($data = array()) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "company");

        return $query->row['total'];
    }
   
    public function UpdateCompany($data = array())
    {

    //$sql2="update oc_company set company_name='".$data["company_name"]."' where company_id='".$data["id"]."'";
    //$query2 = $this->db->query($sql2);
        $input_array=array(
                    'company_name'=>$data["company_name"]
                        );
                
        $query = $this->db->query("update",DB_PREFIX . "company",array( 'company_id'=>(int)$data["id"]),$input_array);
    }

public function getCompanyValue($data = array())
{

//$sql = "SELECT * FROM " . DB_PREFIX . "company where company_id='".$data["id"]."'";

//$query = $this->db->query($sql);
$where=array('company_id'=>(int)$data["id"]);
$query = $this->db->query('select',DB_PREFIX . 'company','','','',$where,'',$limit,'',$start,array());
        
return $query->rows;
}

}
