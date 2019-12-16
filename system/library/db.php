<?php
class DB {
	private $db;
	
	public function __construct($driver, $hostname, $username, $password, $database) {
		$class = 'DB\\' . $driver;

		if (class_exists($class)) {
                    if (!isset($this->db)) {
            
                        $this->db = new $class($hostname, $username, $password, $database);
                        }
			
		} else {
                    echo ('Error: Could not load database driver ' . $driver . '!');
			exit('Error: Could not load database driver ' . $driver . '!');
		}
	}
        public function getNextSequenceValue($collection)
        {
            return $this->db->getNextSequenceValue($collection);
        }
		public function getNextInvoiceValue($store_id)
        {
            return $this->db->getNextInvoiceValue($store_id);
        }
        public function dump($sqltype,$collection,$search='',$searchField='',$find='',$where='',$or='',$limit='',$columns='',$start=0,$sort_by='',$lookupwithunwind='',$group='') {
                                    
                                             
            return $this->db->listRows($collection,$search,$searchField,$find,$where,$or,$limit,$columns,$start,$sort_by);            
        
                                }
	public function query($sqltype,$collection,$search='',$searchField='',$find='',$where='',$or='',$limit='',$columns='',$start=0,$sort_by='',$lookupwithunwind='',$groupby='',$matchcount='') {
			$result = false;
			$data = array();
			$count=0;	
                        $rowcount=0;
			switch (strtolower(trim($sqltype)))
			{
				case 'select':				
				$fdata=$this->db->listRows($collection,$search,$searchField,$find,$where,$or,$limit,$columns,$start,$sort_by);
				foreach ($fdata as $row)  
				{						
					$data[] = $row;	
				}	
				$count=$fdata->count();			
				break;
				case 'update':
					$fdata=$this->db->update($collection,$search,$searchField);                                        
                                        $data=$fdata;
                                        $count= sizeof($fdata);
				break;
                                case 'delete':
					$fdata=$this->db->deleterow($collection,$search);
				break;
                                case 'insert':
					$fdata=$this->db->saveObject($collection,$search);
                                        $data=$fdata;
                                        $count= sizeof($fdata);
				break; 
                                case 'upsert':
					$fdata=$this->db->upsert($collection,$search,$searchField);
                                        $data=$fdata;
                                        $count= sizeof($fdata);
				break; 
                             case 'incmodify':
					$fdata=$this->db->incModify($collection,$search,$searchField);
                                        $data=$fdata;
                                        $count= sizeof($fdata);
				break; 
                                case 'gettotalcount':
					$fdata=$this->db->gettotalcount($collection,$search,$searchField,$find,$limit,$columns);
                                        //print_r($fdata);
                                        foreach ($fdata as $row)  {						
					$data[] = $row;	
				}
                                   $count= sizeof($fdata);
				break;
                                case 'gettotalsum':
					$fdata=$this->db->gettotalsum($collection,$search,$searchField,$find,$limit);
                                        //print_r($fdata);
                                        foreach ($fdata as $row)  {						
					$data[] = $row;	
				}
                                   $count= sizeof($fdata);
				break;
                                case 'join':
                                    $fdata=$this->db->lookup($collection,$search,$searchField,$find,$limit,$start,$sort_by,$columns,$lookupwithunwind,$groupby,$matchcount); 
                                    
                                    $lookupdata= array();
                                    foreach ($fdata as $row)  {	
                                         if(is_array($row) && !is_numeric($row))
                                         {
                                            $data[] = $row;	
                                         }
                                       
                                       // $data[] = $row[0]['rowcount'];	
				}
                                 
                                   $count=$fdata['rowcount'];// sizeof($fdata);	
                                   $rowcount=$fdata['rowcount'];
                                    break;
			}
									
				$result = new \stdClass();
                                if($sqltype=='join')
                                {
                                    $result->row = $data;
                                    $result->rows = $data; 
                                }
                                else
                                {
                                  $result->row = (isset($data[0]) ? $data[0] : array());
                                    $result->rows = $data;  
                                }
				
				$result->num_rows = $count;
                                $result->total_rows =$rowcount;
                                if(!empty($lookupdata))
                                {
                                    $result->lookrows = $lookupdata;
                                }
				if ($result) {
					return $result;
				} else {
					$result = new \stdClass();
					$result->row = array();
					$result->rows = array();
					$result->num_rows = 0;
					return $result;
				}
		 
	}

	public function escape($value) {
		return $this->db->escape($value);
	}

	public function countAffected() {
		return $this->db->countAffected();
	}

	public function getLastId() {
		return $this->db->getLastId();
	}
public function search($collection,$where) {
return $this->db->search($collection,$where);
}
 public function listDbs() {
		return $this->db->listDbs();
}
public function getcount($collection,$where){
return $this->db->getcount($collection,$where);
}

	public function listCollections() {
		return $this->db->listCollections();
	}
public function listRows($collectionName,$search='',$searchField='',$find='',$limit='') {
 return $this->db->listRows($collectionName);
}
/*
public function getCollection() {
		return $this->db->getCollection();
	}
public function setCollection() {
return $this->db->setCollection();
}

public function  finddata()
{
  return $this->db->finddata();
}*/

}
