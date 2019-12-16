<?php
class ModelPosExtension extends Model {
	function getExtensions($type) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "'");
                $query = $this->db->query('select', DB_PREFIX .'extension','','','',array('type'=>$this->db->escape($type)));
		return $query->rows;
	}
}
?>