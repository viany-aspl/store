<?php
class ModelModuleFaq extends Model {
	public function addFaq($data) {
	    
		$faq_id = $this->db->getLastId();
	    
	    foreach($data['faq_description'] as $language_id => $value){
		    $this->db->query("INSERT INTO " . DB_PREFIX . "letscms_faq_description SET faq_id='".(int)$faq_id."',language_id='".$language_id."', question = '" . $this->db->escape($value['question']) . "', answer = '" . $this->db->escape($value['answer']). "'");
	    }
	
		return true;
	}

    public function getfaq($faq_id){
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "letscms_faq where faq_id='".(int)$faq_id."'");
        return  $query->row;
    }

    public function getfaqDescription($faq_id){
        $faq_description_data = array();
    
    		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "letscms_faq_description WHERE faq_id = '" . (int)$faq_id . "'");
    
    		foreach ($query->rows as $result) {
    			$faq_description_data[$result['language_id']] = array(
    				'question'             => $result['question'],
    				'answer'       => $result['answer'],
    				'image'       => $result['image'],
    			);
    		}
    
    		return $faq_description_data;
    }
    
   
    
	public function editFaq($faq_id, $data) {
	    
		$this->db->query("UPDATE " . DB_PREFIX . "letscms_faq SET image = '" . $this->db->escape($data['image']) . "',status='".(int)$data['status']."', date_modified = NOW() WHERE faq_id = '" . (int)$faq_id . "'");
        
        $this->db->query("DELETE FROM " . DB_PREFIX . "letscms_faq_description WHERE faq_id = '" . (int)$faq_id . "'");

		foreach ($data['faq_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "letscms_faq_description SET faq_id = '" . (int)$faq_id . "', language_id = '" . (int)$language_id . "', question = '" . $this->db->escape($value['question']) . "', answer = '" . $this->db->escape($value['answer']) . "'");
		}
		
	}

	public function getTotalFaqs() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "letscms_faq");

		return $query->row['total'];
	}

    public function getFaqs($limit) {
        $data_faqs=array();
        
        
		$query = $this->db->query("select" , DB_PREFIX . "faq",'','','','','',$limit);
		
		foreach($query->rows as $faq){
		    $data_faqs[]=array(
		        'faq_id'=> $faq['faq_id'],
		        'image'=> $faq['image'],
		        'question'=> $faq['faq_description']['1']['question'],
		        'answer'=> $faq['faq_description']['1']['answer'],
		        );
		}
		
		return $data_faqs;
	}

    public function deleteFaq($faq_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "letscms_faq WHERE faq_id = '" . (int)$faq_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "letscms_faq_description WHERE faq_id = '" . (int)$faq_id . "'");
	}
}