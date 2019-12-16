<?php

class ModelUnitUnit extends Model {


public function getGrowerIdByCard($card_number)
	{
            //$sql = "SELECT  GROWER_ID,UNIT_ID FROM oc_card_issue where oc_card_issue.CARD_SERIAL_NUMBER='".$card_number."' "; 
            //$query = $this->db->query($sql);
            $this->db->query('select','oc_card_issue',(int)$card_number,'CARD_SERIAL_NUMBER');
            
            return $query->row;
	}

    public function addunit($data) 
    {
        //$sql2="insert into  oc_unit set unit_name='".$data["unit_name"]."',unit_id='".$data["unit_id"]."',company_id='".$data['filter_company']."'  ";
        $s_id=$this->db->getNextSequenceValue('oc_unit');
        
                $input_array=array(
                    'sid'=>(int)$s_id,
                    'unit_name'=>$data["unit_name"],
                    'unit_id'=>(int)$data["unit_id"],
                    'company_id'=>(int)$data['filter_company'],
                    'wallet_balance'=>0
                    );
                
                $query = $this->db->query("insert",DB_PREFIX . "unit",$input_array);
    }


    public function getunit($data = array()) {

        //$sql = "SELECT  * FROM " . DB_PREFIX . "unit ";

	if(!empty($data['filter_company']))
	{
            $col='company_id';
            $colval=(int)$data['filter_company'];
	}
        else
        {
            $col='';
            $colval=''; 
        }
	$match=array();
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
            $sort_array=array();
            $query = $this->db->query("select",DB_PREFIX . "unit",$colval,$col,$match,'','',$limit,'',$start,$sort_array);
            
        return $query;
    }

    public function getTotalunit($data = array()) {
	$sql="SELECT COUNT(*) AS total FROM " . DB_PREFIX . "unit";
	if(!empty($data['filter_company']))
	{
		$sql.=" where oc_unit.company_id='".$data['filter_company']."' ";
	}
        	$query = $this->db->query($sql);

        	return $query->row['total'];
    }
public function getstorebyunitid($unit_id)
{
    //$sql = "SELECT oc_store.* FROM " . DB_PREFIX . "store_to_unit left join oc_store on oc_store_to_unit.store_id=oc_store.store_id where oc_store_to_unit.unit_id='".$unit_id."' ";
        $lookup=array(
                'from' => 'oc_store',
                'localField' => 'store_id',
                'foreignField' => 'store_id',
                'as' => 'oc_store'
            );
            $match=array('unit_id'=>(int)$unit_id);
            
            $sort_array=array();
            $query = $this->db->query("join",DB_PREFIX . "store_to_unit",$lookup,'$oc_store',$match,'','',$limit,'',$start,$sort_array);
            //print_r($query->total_rows);
            foreach($query->row as $row)
            {
                $return_array[]=array(
                            'unit_id'=>$row['unit_id'],
                            'store_id'=>$row['oc_store']['store_id'],
                            'name'=>$row['oc_store']['name'],
                            'url'=>$row['oc_store']['url'],
                            'company_id'=>$row['oc_store']['company_id'],
                            'creditlimit'=>$row['oc_store']['creditlimit'],
                            'currentcredit'=>$row['oc_store']['currentcredit'],
                            'wallet_balance'=>$row['oc_store']['wallet_balance'],
                            'totalrows'=>$query->total_rows    
                        );
            }
            return $return_array;
}
public function getunitsbycompany($company_id)
{
//$sql = "SELECT oc_unit.* FROM " . DB_PREFIX . "unit  where oc_unit.company_id='".$company_id."' ";

$query = $this->db->query('select',DB_PREFIX . 'unit',(int)$company_id,'company_id','','','','','','',array());
                
return $query->rows;
}
    public function getCompanies() 
    {
        $query = $this->db->query('select',DB_PREFIX . 'company', boolval(1),'is_active','','','','','','',array());
        
        return $query->rows;
    }

    public function getUnitValue($data = array()) 
    {
        //$sql = "SELECT * FROM " . DB_PREFIX . "unit where sid='".$data['id']."'";
        $query = $this->db->query('select',DB_PREFIX . 'unit',(int)$data,'sid','','','','','','',array());
        
        return $query->rows;
    }
    public function UpdateUnit($data) 
    {
        $input_array=array('unit_name'=>$data["unit_name"],'unit_id'=>$data["unit_id"],'company_id'=>$data['filter_company']);
        $query2 = $this->db->query('update','oc_unit',array('sid'=>(int)$data["id"]),$input_array);
    }  
    public function getUnitByID($id)
    {
            //$sql = "SELECT * FROM " . DB_PREFIX . "unit  ou left join oc_company occ on occ.company_id= ou.company_id where ou.unit_id='".$id."'";
            $lookup=array(
                'from' => 'oc_company',
                'localField' => 'company_id',
                'foreignField' => 'company_id',
                'as' => 'occ'
            );
            $match=array('unit_id'=>(int)$id);
            
            $sort_array=array();
            $query = $this->db->query("join",DB_PREFIX . "unit",$lookup,'$occ',$match,'','',$limit,'',$start,$sort_array);
            foreach($query->row as $row)
            {
                $return_array[]=array(
                            'unit_id'=>$row['unit_id'],
                            'unit_name'=>$row['unit_name'],
                            'company_id'=>$row['company_id'],
                            'wallet_balance'=>$row['wallet_balance'],
                            'company_name'=>$row['occ']['company_name'],
                            'totalrows'=>$query->total_rows    
                        );
            }
            return $return_array;
    
}
public function getUnitByComapany_UnitID($unitid,$companyid)
{
    //$sql = "SELECT * FROM " . DB_PREFIX . "unit  ou left join oc_company occ on 
    //occ.company_id= ou.company_id where ou.unit_id='".$unitid."' and ou.company_id='".$companyid."' ";
            $lookup=array(
                'from' => 'oc_company',
                'localField' => 'company_id',
                'foreignField' => 'company_id',
                'as' => 'occ'
            );
            $match=array('unit_id'=>(int)$id,'company_id'=>(int)$companyid);
            
            $sort_array=array();
            $query = $this->db->query("join",DB_PREFIX . "unit",$lookup,'$occ',$match,'','',$limit,'',$start,$sort_array);
            foreach($query->row as $row)
            {
                $return_array[]=array(
                            'unit_id'=>$row['unit_id'],
                            'unit_name'=>$row['unit_name'],
                            'company_id'=>$row['company_id'],
                            'wallet_balance'=>$row['wallet_balance'],
                            'company_name'=>$row['occ']['company_name'],
                            'totalrows'=>$query->total_rows    
                        );
            }
            return $return_array;
}  

}
