<?php
class ModelLocalisationCurrency extends Model 
{
    public function addCurrency($data) 
    {
        $currency_id=$this->db->getNextSequenceValue('oc_currency');
        $input_array=array(
            'currency_id'=>(int)$currency_id,
            'title'=>$this->db->escape($data['title']), 
            'code'=>$this->db->escape($data['code']),
            'symbol_left'=>$this->db->escape($data['symbol_left']), 
            'symbol_right'=> $this->db->escape($data['symbol_right']),
            'decimal_place'=>$this->db->escape($data['decimal_place']),
            'value'=>$this->db->escape($data['value']),
            'status'=>boolval($data['status']),
            'date_modified'=>new MongoDate(strtotime(date('Y-m-d')))
        );
	$this->db->query('insert','oc_currency',$input_array);
        
        if ($this->config->get('config_currency_auto')) 
        {
            $this->refresh(true);
	}
        $this->cache->delete('currency');
    }
    public function editCurrency($currency_id, $data) 
    {
        $update_array=array(
            'title'=>$this->db->escape($data['title']), 
            'code'=>$this->db->escape($data['code']), 
            'symbol_left'=>$this->db->escape($data['symbol_left']), 
            'symbol_right'=>$this->db->escape($data['symbol_right']),
            'decimal_place'=>$this->db->escape($data['decimal_place']), 
            'value'=>$this->db->escape($data['value']), 
            'status'=>boolval($data['status']),
            'date_modified' => new MongoDate(strtotime(date('Y-m-d')))
        );
        
        $this->db->query("update ",DB_PREFIX . "currency",array('currency_id'=>(int)$currency_id),$update_array);
        
        $this->cache->delete('currency');
    }
    public function deleteCurrency($currency_id) 
    {
	$this->db->query('delete',DB_PREFIX . 'currency',array('currency_id' =>(int)$currency_id));
        $this->cache->delete('currency');
    }
    public function getCurrency($currency_id) 
    {
	//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "currency WHERE currency_id = '" . (int)$currency_id . "'");
        $query = $this->db->query('select',DB_PREFIX . "currency",'','','',array('currency_id'=>(int)$currency_id));
        return $query->row;
    }
    public function getCurrencyByCode($currency) 
    {
	//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "currency WHERE code = '" . $this->db->escape($currency) . "'");
        $query = $this->db->query('select',DB_PREFIX . "currency",'','','',array('code'=>$this->db->escape($currency)));
        return $query->row;
    }

	public function getCurrencies($data = array()) {
		if ($data) {
                   
                        if (isset($data['start']) || isset($data['limit'])) 
                        {
                            if ($data['start'] < 0) 
                            {
                                $start = 0;
                            }
                            else 
                            {
                                $start = (int)$data['start'];
                            }
                            if ($data['limit'] < 1) 
                            {
                                $limit = 20;
                            }
                            else 
                            {
                                $limit = (int)$data['limit'];
                            }
			
                        }
                        $query = $this->db->query('select',DB_PREFIX . 'currency','','','','','',$limit,'',$start,array('title'=>1));
  
			return $query;
		} else {
			$currency_data = $this->cache->get('currency');

			if (!$currency_data) {
				$currency_data = array();

				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency ORDER BY title ASC");
                                $query = $this->db->query('select',DB_PREFIX . 'currency','','','','','',$limit,'',$start,array('title'=>1));
				return $query;
                                foreach ($query->rows as $result) {
					$currency_data[$result['code']] = array(
						'currency_id'   => $result['currency_id'],
						'title'         => $result['title'],
						'code'          => $result['code'],
						'symbol_left'   => $result['symbol_left'],
						'symbol_right'  => $result['symbol_right'],
						'decimal_place' => $result['decimal_place'],
						'value'         => $result['value'],
						'status'        => $result['status'],
						'date_modified' => $result['date_modified']
					);
				}

				$this->cache->set('currency', $currency_data);
			}

			return $currency_data;
		}
	}

	public function refresh($force = false) {
		if (extension_loaded('curl')) {
			$data = array();

			if ($force) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE code != '" . $this->db->escape($this->config->get('config_currency')) . "'");
			} else {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE code != '" . $this->db->escape($this->config->get('config_currency')) . "' AND date_modified < '" .  $this->db->escape(date('Y-m-d H:i:s', strtotime('-1 day'))) . "'");
			}

			foreach ($query->rows as $result) {
				$data[] = $this->config->get('config_currency') . $result['code'] . '=X';
			}

			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, 'http://download.finance.yahoo.com/d/quotes.csv?s=' . implode(',', $data) . '&f=sl1&e=.csv');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);

			$content = curl_exec($curl);

			curl_close($curl);

			$lines = explode("\n", trim($content));

			foreach ($lines as $line) {
				$currency = utf8_substr($line, 4, 3);
				$value = utf8_substr($line, 11, 6);

				if ((float)$value) {
					$this->db->query("UPDATE " . DB_PREFIX . "currency SET value = '" . (float)$value . "', date_modified = '" .  $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE code = '" . $this->db->escape($currency) . "'");
				}
			}

			$this->db->query("UPDATE " . DB_PREFIX . "currency SET value = '1.00000', date_modified = '" .  $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE code = '" . $this->db->escape($this->config->get('config_currency')) . "'");

			$this->cache->delete('currency');
		}
	}

}