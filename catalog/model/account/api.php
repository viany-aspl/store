<?php
class ModelAccountApi extends Model {
	public function login($username, $password) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api` WHERE username = '" . $this->db->escape($username) . "' AND password = '" . $this->db->escape($password) . "' AND status = '1'");

		return $query->row;
	}

	public function loginm($username, $password) 
	{
        $log=new Log("login-".date('Y-m-d').".log");
		
		$data=array();
		$pwd=array($this->db->escape(md5($password)));
		$log->write('generated password');
		$log->write($pwd);
		$user_query = $this->db->query('select',DB_PREFIX . "user",'','','',array('username'=>$this->db->escape($username) ));
		$log->write($user_query->row['password']);
		$val=SHA1(($user_query->row['salt']. SHA1(($user_query->row['salt']. SHA1( $this->db->escape($password) )))));
		$log->write('generated val');
		$log->write($val);
		if ($user_query->num_rows  && ($user_query->row['password']==$val||$user_query->row['password']==$pwd) ) 
		{
			$data= $user_query->row;

		}
		return $data;


	}
	public function UserAuthorization($username) {
		//$sql="SELECT * FROM `" . DB_PREFIX . "user` WHERE user_id = '" . $this->db->escape($username) . "'  AND status = '1'";
		//$query = $this->db->query($sql);
                $query = $this->db->query('select',DB_PREFIX . "user",'','','',array('user_id'=>(int)$this->db->escape($username),'status'=>true ));
		return $query->row;
	} 

}