<?php

class ModelCatalogHsn extends Model {

    public function addhsn($data) 
    {
            $query1 = $this->db->query('select',DB_PREFIX . 'product_hsn','','','',array('hsn_code'=>(int)$data['hsn_code']),'','','','',array('level'=>1));
           
            if ($query1->num_rows) 
            {
                return ;
            }
            else 
            {
                $sid=$this->db->getNextSequenceValue('oc_product_hsn');
       
                $input_array=array(
                    'sid'=>(int)$sid,
                    'hsn_code'=>(int)$data['hsn_code'],
                    'hsn_name'=>$data['hsn_name'],
					'tax_class_name'=>$data['tax_class_name'],
					'tax_class_id'=>(int)$data['tax_class_id']
                    );
                $query = $this->db->query("insert",DB_PREFIX . "product_hsn",$input_array);
                return $sid; 
            }
    }
	public function edithsn($data) 
    {
            $query1 = $this->db->query('select',DB_PREFIX . 'product_hsn','','','',array( 'sid'=>(int)$data['sid']),'','','','',array('level'=>1));
           
            if ($query1->num_rows) 
            {
				if($data['tax_class_name']=='SELECT')
				{
					$data['tax_class_name']='';
				}
                $input_array=array(
                   'hsn_code'=>(int)$data['hsn_code'],
                    'hsn_name'=>$data['hsn_name'],
					'tax_class_name'=>$data['tax_class_name'],
					'tax_class_id'=>(int)$data['tax_class_id']
                    );
					
                $query = $this->db->query("update",DB_PREFIX . "product_hsn",array( 'sid'=>(int)$data['sid']),$input_array);
				//print_r($data);exit;	
                return $data['sid']; 
            }
            else 
            {
                return;
            }
    }
	public function deletethsn($data) 
    {	
            
				
                $query = $this->db->query("delete",DB_PREFIX . "product_hsn",array( 'sid'=>(int)$data['sid']));
				
                return $data['sid']; 
           
    }

    public function gethsn($data = array()) 
	{

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
                
        $where=array();
        if(!empty($data['hsn']))   
		{
			$where['hsn_code']=(int)$data['hsn'];
		}			
        $query = $this->db->query('select',DB_PREFIX . 'product_hsn','','','',$where,'',$limit,'',$start,array('sid'=>-1));
        return $query;
    }
	public function gethsnbyid($data = array()) 
	{
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
                
        $where=array();
                  
        $query = $this->db->query('select',DB_PREFIX . 'product_hsn','','','',array('sid'=>(int)$data['sid']),'',(int)1,'',$start,array('sid'=>-1));
        
        return $query;
    }
	public function getproductbyhsn($data = array()) 
	{

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
                
        $where=array();
                 
        $query = $this->db->query('select',DB_PREFIX . 'product','','','',array('HSTN'=>trim($data['hsn_code'])),'',(int)5,'',0,array('name'=>-1));
        //print_r($data['hsn_code']); 
		return $query;
    }
    public function getTotalhsn($data = array()) 
	{
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_hsn");

        return $query->row['total'];
    }


}
