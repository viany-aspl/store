<?php
class ModelCatalogFaq extends Model 
{
	////////////////
	public function cat_getList($data) 
	{
		$query = $this->db->query('select','oc_faq_category','','','',$where);
		return $query;
	}
	public function getfaqCat($cat_id)
	{
        $query = $this->db->query("select",DB_PREFIX . "faq_category",'','','',array('id'=>(int)$cat_id));
        return  $query->row;
    }
	public function addFaqCat($data) 
	{
	    
		$cat_id=$this->db->getNextSequenceValue('oc_faq_category');//print_r($data);exit;
		$input_array=array(
			'id'=>(int)$cat_id,
			'image'=>$this->db->escape($data['image']),
			'status'=>(int)($data['status']),
			'date_modified'=> new MongoDate(strtotime(date('Y-m-d'))), 
			'date_added' => new MongoDate(strtotime(date('Y-m-d'))),
			'description'=>$data['description'],
			'name' => $data['name'],
			'sort_order' => (int)$data['sort_order']
		);
	    $this->db->query("insert",DB_PREFIX . "faq_category",$input_array);

		return true;
	}
	public function editFaqCat($cat_id, $data) 
	{
	    $input_array=array(
			'id'=>(int)$cat_id,
			'image'=>$this->db->escape($data['image']),
			'status'=>(int)($data['status']),
			'date_modified'=> new MongoDate(strtotime(date('Y-m-d'))), 
			'description'=>$data['description'],
			'name' => $data['name'],
			'sort_order' => (int)$data['sort_order']
		);
		$this->db->query("update" , DB_PREFIX . "faq_category",array('id'=>(int)$cat_id),$input_array);
    }
	public function deleteFaqCat($cat_id) 
	{
		$this->db->query("delete" , DB_PREFIX . "faq_category",array('id'=>(int)$cat_id));
		
	}
	///////////////
	public function addFaq($data) 
	{
	    
		$faq_id=$this->db->getNextSequenceValue('oc_faq');//print_r($data);exit;
		$input_array=array(
			'faq_id'=>(int)$faq_id,
			'image'=>$this->db->escape($data['image']),
			'status'=>boolval($data['status']),
			'date_modified'=> new MongoDate(strtotime(date('Y-m-d'))), 
			'date_added' => new MongoDate(strtotime(date('Y-m-d'))),
			'faq_description'=>$data['faq_description'],
			'faq_category_id' => (int)$data['category']
		);
	    $this->db->query("insert",DB_PREFIX . "faq",$input_array);

	    
	    foreach($data['faq_description'] as $language_id => $value)
		{
		    $this->db->query("insert",DB_PREFIX . "faq_description",array('faq_id'=>(int)$faq_id,'language_id'=>$language_id,'question'=>$this->db->escape($value['question']),'answer'=>$this->db->escape($value['answer'])));
	    }
	
		return true;
	}

    public function getfaq($faq_id)
	{
        $query = $this->db->query("select",DB_PREFIX . "faq",'','','',array('faq_id'=>(int)$faq_id));
        return  $query->row;
    }

	public function getfaqbycategoryid($cat_id)
	{
        $query = $this->db->query("select",DB_PREFIX . "faq",'','','',array('faq_category_id'=>(int)$cat_id));
        return  $query->rows;
    }


    public function getfaqDescription($faq_id)
	{
        $faq_description_data = array();
		$query = $this->db->query("select",DB_PREFIX . "faq_description",'','','',array('faq_id'=>(int)$faq_id));
		foreach ($query->rows as $result) 
		{
    		$faq_description_data[$result['language_id']] = array(
    				'question'             => $result['question'],
    				'answer'       => $result['answer'],
    		);
    	}
		return $faq_description_data;
    }
    
   
    
	public function editFaq($faq_id, $data) 
	{
	    $input_array=array(
			'faq_id'=>(int)$faq_id,
			'image'=>$this->db->escape($data['image']),
			'status'=>boolval($data['status']),
			'date_modified'=>  new MongoDate(strtotime(date('Y-m-d'))), 
			
			'faq_description'=>$data['faq_description']
		);
		$this->db->query("update" , DB_PREFIX . "faq",array('faq_id'=>(int)$faq_id),$input_array);
        
        $this->db->query("delete" ,DB_PREFIX . "faq_description",array('faq_id'=>(int)$faq_id));

		foreach ($data['faq_description'] as $language_id => $value) 
		{
			$this->db->query("insert",DB_PREFIX . "faq_description",array('faq_id'=>(int)$faq_id,'language_id'=>$language_id,'question'=>$this->db->escape($value['question']),'answer'=>$this->db->escape($value['answer'])));
		}
		
	}

    public function getFaqs() 
	{
		//$sql="SELECT * FROM " . DB_PREFIX . "letscms_faq f LEFT JOIN ".DB_PREFIX."letscms_faq_description fd on f.faq_id=fd.faq_id where fd.language_id='".(int)$this->config->get('config_language_id')."'";
        	$query = $this->db->query('select','oc_faq','','','',$where);
		return $query;
	}


	public function getFaqCategories() 
	{
		$query = $this->db->query('select','oc_faq_category','','','',array('status'=>1),'','','','',array('sort_order'=>1));
		return $query->rows;
	}

    public function deleteFaq($faq_id) 
	{
		$this->db->query("delete" , DB_PREFIX . "faq",array('faq_id'=>(int)$faq_id));
		$this->db->query("delete" , DB_PREFIX . "faq_description",array('faq_id'=>(int)$faq_id));
	}

}

