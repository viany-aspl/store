<?php
class User {
	private $user_id;
	private $username;
	private $usernameshow;
        private $usergroupname;
        private $user_store_id;
	private $permission = array();

	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->config=$registry->get('config');

		if (isset($this->session->data['user_id'])) {
			//$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->session->data['user_id'] . "' AND status = '1'");
$user_query =$this->db->query('select',DB_PREFIX . "user",'','','',array('user_id'=>(int)$this->db->escape($this->session->data['user_id']),'status'=>true ));

			if ($user_query->num_rows) {
				$this->user_id = $user_query->row['user_id'];
				$this->username = $user_query->row['username'];
				$this->usernameshow = $user_query->row['firstname']." ".$user_query->row['lastname'];
				$this->user_group_id = $user_query->row['user_group_id'];
                                $this->user_store_id= $user_query->row['store_id'];
				//$this->db->query("UPDATE " . DB_PREFIX . "user SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE user_id = '" . (int)$this->session->data['user_id'] . "'");
				$uwdata=array('user_id'=>$this->session->data['user_id']);
				$udata=array('ip'=>$this->db->escape($this->request->server['REMOTE_ADDR']));
				$this->db->query("UPDATE" , DB_PREFIX . "user",$uwdata,$udata);
				//setting to change
                                    $this->config->set('config_store_id', $this->user_store_id);
                                // Settings
                                     //  $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' OR store_id = '" . (int)  $this->config->get('config_store_id') . "' ORDER BY store_id ASC");
 $query = $this->db->query('select',DB_PREFIX . "setting",'','','','',array('store_id'=>'0','store_id'=>$this->config->get('config_store_id') ));

                                        foreach ($query->rows as $result) {
                                            if (!$result['serialized']) {
                                                $this->config->set($result['key'], $result['value']);
                                                } else {
                                                    $this->config->set($result['key'], unserialize($result['value']));
                                                }
                                            }

                                 //end setting			

		
				//$user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");
$user_group_query = $this->db->query( 'select',DB_PREFIX . "user_group",'','','',array('user_group_id'=>(int)$user_query->row['user_group_id']));
$this->usergroupname=$user_group_query->row['name'];
				$permissions = unserialize($user_group_query->row['permission']);

				if (is_array($permissions)) {
					foreach ($permissions as $key => $value) {
						$this->permission[$key] = $value;
					}
				}
			} else {
				$this->logout();
			}
		}
	}

	public function login($username, $password,$usertype='') 
	{
		/*$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE username = '" . $this->db->escape($username) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'");*/



$pwd=array($this->db->escape(md5($password)));
$where_query=array();
if(!empty($usertype))
{
 $where_query= array('username'=>$this->db->escape($username),'status'=>true,'user_group_id'=>(int)$usertype ) ;   
}else{
 $where_query= array('username'=>$this->db->escape($username),'status'=>true ); 
}
$user_query = $this->db->query('select',DB_PREFIX . "user",'','','',$where_query);


$val=SHA1(($user_query->row['salt']. SHA1(($user_query->row['salt']. SHA1( $this->db->escape($password) )))));
if ($user_query->num_rows  && ($user_query->row['password']==$val||$user_query->row['password']==$pwd) ) {

			$this->session->data['user_id'] = $user_query->row['user_id'];

			$this->user_id = $user_query->row['user_id'];
			$this->username = $user_query->row['username'];
			$this->usernameshow = $user_query->row['firstname']." ".$user_query->row['lastname'];
			$this->user_group_id = $user_query->row['user_group_id'];
                        $this->user_store_id= $user_query->row['store_id'];
			
			//setting to change
                                    $this->config->set('config_store_id', $this->user_store_id);
                                // Settings "SELECT * FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' OR store_id = '" . (int)  $this->config->get('config_store_id') . "' ORDER BY store_id ASC"
                                       $query = $this->db->query('select',DB_PREFIX . "setting",'','','','',array('store_id'=>'0','store_id'=>$this->config->get('config_store_id') ));

                                        foreach ($query->rows as $result) {
                                            if (!$result['serialized']) {
                                                $this->config->set($result['key'], $result['value']);
                                                } else {
                                                    $this->config->set($result['key'], unserialize($result['value']));
                                                }
                                            }

                                 //end setting "SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'"
			
			$user_group_query = $this->db->query( 'select',DB_PREFIX . "user_group",$user_query->row['user_group_id'],'user_group_id');

			$permissions = unserialize($user_group_query->row['permission']);

			if (is_array($permissions)) {
				foreach ($permissions as $key => $value) {
					$this->permission[$key] = $value;
				}
			}


			return true;
		} else {
			return false;
		}
	}
	public function qrlogin($username, $store_id,$usertype='') 
	{

		$where_query=array();
		if(!empty($usertype))
		{
			$where_query= array('user_id'=>(int)$this->db->escape($username),'status'=>true,'user_group_id'=>(int)$usertype ) ;   
		}
		else
		{
			$where_query= array('user_id'=>(int)$this->db->escape($username),'status'=>true ); 
		}
		$user_query = $this->db->query('select',DB_PREFIX . "user",'','','',$where_query);
		$log=new Log("qr-login-".date('Y-m-d').".log");
		if(($user_query->row['user_group_id']==11) || ($user_query->row['user_group_id']==14))
		{
			
		}
		else
		{
			$log->write('user group not authrozied ');
			return false;
		}
		if ($user_query->num_rows  && ($user_query->row['store_id']==$store_id) ) 
		{
			$log->write('in if');
			$this->session->data['user_id'] = $user_query->row['user_id'];

			$this->user_id = $user_query->row['user_id'];
			$this->username = $user_query->row['username'];
			$this->usernameshow = $user_query->row['firstname']." ".$user_query->row['lastname'];
			$this->user_group_id = $user_query->row['user_group_id'];
            $this->user_store_id= $user_query->row['store_id'];
			
			//setting to change
                                    $this->config->set('config_store_id', $this->user_store_id);
                                // Settings "SELECT * FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' OR store_id = '" . (int)  $this->config->get('config_store_id') . "' ORDER BY store_id ASC"
                                       $query = $this->db->query('select',DB_PREFIX . "setting",'','','','',array('store_id'=>'0','store_id'=>$this->config->get('config_store_id') ));

                                        foreach ($query->rows as $result) {
                                            if (!$result['serialized']) {
                                                $this->config->set($result['key'], $result['value']);
                                                } else {
                                                    $this->config->set($result['key'], unserialize($result['value']));
                                                }
                                            }

                                 //end setting "SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'"
			
			$user_group_query = $this->db->query( 'select',DB_PREFIX . "user_group",$user_query->row['user_group_id'],'user_group_id');

			$permissions = unserialize($user_group_query->row['permission']);

			if (is_array($permissions)) {
				foreach ($permissions as $key => $value) {
					$this->permission[$key] = $value;
				}
			}


			return true;
		} 
		else 
		{
			$log->write('in else ');
			return false;
		}
	}

	public function logout() {
		unset($this->session->data['user_id']);

		$this->user_id = '';
		$this->username = '';
		$this->session->destroy();
	}

	
	public function getPermission() 
	{
		
		return  $this->permission;
		
	}
	
	public function hasPermission($key, $value) 
	{
		if (isset($this->permission[$key])) {
			return in_array($value, $this->permission[$key]);
		} else {
			return false;
		}
	}

	public function isLogged() {
		return $this->user_id;
	}

	public function getId() {
		return $this->user_id;
	}

	public function getUserName() {
		return $this->username;
	}
	
        public function getUserNameShow() {
		return $this->usernameshow;
	}
        public function getUsergroupname() {
		return $this->usergroupname;
	}

	public function getGroupId() {
		return $this->user_group_id;
	}	
        	public function getStoreId() {
		return $this->user_store_id;
	}	
}
