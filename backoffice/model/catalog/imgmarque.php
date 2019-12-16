<?php
class ModelCatalogImgmarque extends Model 
{
	public function addFaq($data) 
	{
	    
		$faq_id=$this->db->getNextSequenceValue('oc_image_slider');//print_r($data);exit;
		$input_array=array(
			'faq_id'=>(int)$faq_id,
			'image'=>$this->db->escape($data['image']),
			'name'=>($data['question']),
			'status'=>boolval($data['status']),
			'date_modified'=> new MongoDate(strtotime(date('Y-m-d'))), 
			'date_added' => new MongoDate(strtotime(date('Y-m-d')))
			
		);
	    $this->db->query("insert",DB_PREFIX . "image_slider",$input_array);

	    
	    
	
		return true;
	}

    public function getfaq($faq_id)
	{
        $query = $this->db->query("select",DB_PREFIX . "image_slider",'','','',array('faq_id'=>(int)$faq_id));
        return  $query->row;
    }

    public function getfaqDescription($faq_id)
	{
        $faq_description_data = array();
		$query = $this->db->query("select",DB_PREFIX . "image_slider_description",'','','',array('faq_id'=>(int)$faq_id));
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
			'name'=>($data['question']),
			'status'=>boolval($data['status']),
			'date_modified'=>  new MongoDate(strtotime(date('Y-m-d'))), 
			'date_added' => new MongoDate(strtotime(date('Y-m-d')))
		);
		$this->db->query("update" , DB_PREFIX . "image_slider",array('faq_id'=>(int)$faq_id),$input_array);
        
        		
	}


    public function getFaqs() 
	{
		//$sql="SELECT * FROM " . DB_PREFIX . "letscms_faq f LEFT JOIN ".DB_PREFIX."letscms_faq_description fd on f.faq_id=fd.faq_id where fd.language_id='".(int)$this->config->get('config_language_id')."'";
        $query = $this->db->query('select','oc_image_slider','','','','');
		return $query;
	}

    public function deleteFaq($faq_id) 
	{
		$this->db->query("delete" , DB_PREFIX . "image_slider",array('faq_id'=>(int)$faq_id));

			}

}

