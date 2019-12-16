<?php
class ModelExtensionExtension extends Model {
	public function getInstalled($type) {
		$extension_data = array();

		$query = $this->db->query('select',DB_PREFIX . "extension",'','','',array('type'=>$this->db->escape($type)),'','','','','',array('code'=>1));

		foreach ($query->rows as $result) {
			$extension_data[] = $result['code'];
		}

		return $extension_data;
	}

	public function install($type, $code) 
        {
            $extension_id=$this->db->getNextSequenceValue('oc_extension');
            $this->db->query('insert','oc_extension',array('extension_id'=>$extension_id,'type'=>$this->db->escape($type),'code'=>$this->db->escape($code)));
	}

	public function uninstall($type, $code) 
        {
            $this->db->query('delete',DB_PREFIX . "extension", array('type'=>$this->db->escape($type),'code'=>$this->db->escape($code)));
	}
}