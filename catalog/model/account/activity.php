<?php
class ModelAccountActivity extends Model {
	public function addActivity($key, $data) {
		if (isset($data['customer_id'])) {
			$customer_id = $data['customer_id'];
		} else {
			$customer_id = 0;
		}
//$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_activity` SET `customer_id` = '" . (int)$customer_id . "', `key` = '" . $this->db->escape($key) . "', `data` = '" . $this->db->escape(serialize($data)) . "', `ip` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', `date_added` = NOW()");
               $datasave=array(
                    'customer_id' =>  (int)$customer_id ,
                   'key' => $this->db->escape($key) ,
                   'data' => (serialize($data)) ,
                   'ip' =>  $this->db->escape($this->request->server['REMOTE_ADDR']) ,
                   'date_added' => new MongoDate(strtotime(date('Y-m-d H:i:s')))
               ) ;
		$this->db->query("insert", DB_PREFIX . "customer_activity",$datasave);
	}
}