<?php
class ModelReportSale extends Model {
	public function get_category_name($category_id) 
	{
            $match=array();
            
            $match['category_id']=(int)$category_id;
            
            $query = $this->db->query('select','oc_category','','','',$match);
                
            return $query->row['category_description'][1]['name'];
	}
	// Sales
	public function getTotalSales($data = array()) {
            $match=array();
            if (!empty($data['filter_date_added'])) 
            {
                        $match['date_added']=new MongoDate(strtotime($data['filter_date_added']));
                        
            }
            if (!empty($data['filter_store'])) 
            {
                        $match['store_id']=(int)$data['filter_store'];
                        
            }
            $groupbyarray=array(
                 "_id"=> '$order_status_id', 
                "sum"=> array('$sum'=> '$total' ) 
            );
            $match['order_status_id']=5;
            
            $query = $this->db->query('gettotalsum','oc_order',$groupbyarray,$match);
                
            return $query->row[0]['sum'];
	}
	public function getTotalCredit($data = array()) 
        {
            $match=array();
            if (!empty($data['filter_date_added'])) 
            {
                        $match['date_added']=new MongoDate(strtotime($data['filter_date_added']));
                        
            }
            if (!empty($data['filter_store'])) 
            {
                        $match['store_id']=(int)$data['filter_store'];
                        
            }
            $groupbyarray=array(
                 "_id"=> '$order_status_id', 
                "sum"=> array('$sum'=> '$credit' ) 
            );
            $match['order_status_id']=5;
            
            $query = $this->db->query('gettotalsum','oc_order',$groupbyarray,$match);
                
            return $query->row[0]['sum'];
	}	
	// Map
	public function getTotalOrdersByCountry() {
		$query = $this->db->query("SELECT COUNT(*) AS total, SUM(o.total) AS amount, c.iso_code_2 FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "country` c ON (o.payment_country_id = c.country_id) WHERE o.order_status_id > '0' GROUP BY o.payment_country_id");

		return $query->rows;
	}
		
	// Orders
	public function getTotalOrdersByDay() {
            
            $match=array();
            $match['order_status_id']=5;
            $match['date_added']=array(
                   '$gte'=>new MongoDate(strtotime(date('Y-m-d')))
                   );
                  
            $groupbyarray=array(
                 "_id"=> array('$hour'=> '$date_added'), 
                "total"=> array('$sum'=> 1 ) 
            );
            
            $query = $this->db->query('gettotalcount','oc_order',$groupbyarray,$match);
            
            for ($i = 0; $i < 24; $i++) {
			$order_data[$i] = array(
				'hour'  => $i,
				'total' => 0
			);
		}
            foreach ($query->row as $result) 
            {
			$order_data[$result['_id']] = array(
				'hour'   => $result['_id'],
				'total' => $result['total']
			);
            }
		return $order_data;
	}

	public function getTotalOrdersByWeek() {
            /*
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}		
		
		$order_data = array();

		$date_start = strtotime('-' . date('w') . ' days');

		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', $date_start + ($i * 86400));

			$order_data[date('w', strtotime($date))] = array(
				'day'   => date('D', strtotime($date)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, date_added 
             * FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(",", $implode) . ") 
             * AND DATE(date_added) >= DATE('" . $this->db->escape(date('Y-m-d', $date_start)) . "') 
             * GROUP BY DAYNAME(date_added)");

		foreach ($query->rows as $result) {
			$order_data[date('w', strtotime($result['date_added']))] = array(
				'day'   => date('D', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $order_data;
                */
            /*
            $monday = strtotime("last sunday");
$monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;

$sunday = strtotime(date("Y-m-d",$monday)." +6 days");

$this_week_sd = date("Y-m-d",$monday);
$this_week_ed = date("Y-m-d",$sunday);

//echo "Current week range from $this_week_sd to $this_week_ed ";
//exit;
            $match=array();
            $match['order_status_id']=5;
            $match['date_added']=array(
                   '$gte'=>new MongoDate(strtotime($this_week_sd))
                   );
                  
            $groupbyarray=array(
                 "_id"=> array('$dayOfWeek'=> '$date_added'), 
                "total"=> array('$sum'=> 1 ) 
            );
            $sortarray=array(
                 "_id"=>  -1 );
              //print_r($sortarray);
            $query = $this->db->query('gettotalcount','oc_order',$groupbyarray,$match,$sortarray);
            //print_r($query->row);exit;
            //echo $date_start = strtotime($this_week_sd);
            //echo '<br/>';
            $date_start = strtotime('-' . date('w') . ' days');

		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', $date_start + ($i * 86400));

			$order_data[date('w', strtotime($date))] = array(
				'day'   => date('D', strtotime($date)),
				'total' => 0
			);
		}
            print_r($order_data);
            
            foreach ($query->row as $result) 
            {
			$order_data[$result['_id']] = array(
				'day'   => date('d', strtotime(date('Y-m-').$result['_id'])),
				'total' => $result['total']
			);
            }
            echo "<br/><br/><br/>";
            print_r($order_data);
            exit;
		return $order_data;
            */
            return '';
	}

	public function getTotalOrdersByMonth() {
           
            $match=array();
            $match['order_status_id']=5;
            $match['date_added']=array(
                   '$gte'=>new MongoDate(strtotime(date('Y-m').'-01'))
                   );
                  
            $groupbyarray=array(
                 "_id"=> array('$dayOfMonth'=> '$date_added'), 
                "total"=> array('$sum'=> 1 ) 
            );
            
            $query = $this->db->query('gettotalcount','oc_order',$groupbyarray,$match);
            
            for ($i = 1; $i <= date('t'); $i++) {
			$date = date('Y') . '-' . date('m') . '-' . $i;

			$order_data[date('j', strtotime($date))] = array(
				'day'   => date('d', strtotime($date)),
				'total' => 0
			);
		}
            foreach ($query->row as $result) 
            {
			$order_data[$result['_id']] = array(
				'day'   => date('d', strtotime($result['_id'])),
				'total' => $result['total']
			);
            }
		return $order_data;
	}

	public function getTotalOrdersByYear() {
            
            $match=array();
            $match['order_status_id']=5;
            $match['date_added']=array(
                   '$gte'=>new MongoDate(strtotime(date('Y').'-01-01')),
                   '$lte'=>new MongoDate(strtotime(date('Y').'-12-31'))
                   );
                  
            $groupbyarray=array(
                 "_id"=> array('$month'=> '$date_added'), 
                "total"=> array('$sum'=> 1 ) 
            );
            
            $query = $this->db->query('gettotalcount','oc_order',$groupbyarray,$match);
               
                $order_data = array();
		for ($i = 1; $i <= 12; $i++) {
			$order_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i)),
				'total' => 0
			);
		}

		foreach ($query->row as $result) {
			$order_data[$result['_id']] = array(
				'month' => date('M', mktime(0, 0, 0, $result['_id'])),
				'total' => $result['total']
			);
		}

		return $order_data;
            
	}
	
	public function getOrders($data = array()) {
		//$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, COUNT(*) AS `orders`, (SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id) AS products, (SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'tax' GROUP BY ot.order_id) AS tax, SUM(o.total) AS `total` FROM `" . DB_PREFIX . "order` o";


               $sql = "SELECT oc_store.name as store_name,MIN(o.date_added) AS date_start, "
                    . "MAX(o.date_added) AS date_end, COUNT(*) AS `orders`,"
                    . " (SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op "
                    . "WHERE op.order_id = o.order_id GROUP BY op.order_id) AS products, "
                    . "(SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot "
                    . "WHERE ot.order_id = o.order_id AND ot.code = 'tax' "
                    . "GROUP BY ot.order_id) AS tax,(SELECT SUM(ot.value) FROM `oc_order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'sub_total' GROUP BY ot.order_id) AS sub_total, SUM(o.total) AS `total` "
                    . "FROM `" . DB_PREFIX . "order` o join oc_store on o.store_id=oc_store.store_id";


		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
                if (!empty($data['filter_store'])) 
                {
                $sql .=" AND o.store_id='".(int)$data['filter_store']."'";
                }
                
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
                 
		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY DATE(o.date_added),o.store_id";//YEAR(o.date_added), WEEK(o.date_added)";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added)";
				break;
		}

		$sql .= " ORDER BY o.date_added DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
                //echo $sql;
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalOrders($data = array()) {

                $sql = "select count(*) as total,sum(total) as amount_total,sum(orders) as orders from (SELECT oc_store.name as store_name,MIN(o.date_added) AS date_start, "
                    . "MAX(o.date_added) AS date_end, COUNT(*) AS `orders`,"
                    . " (SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op "
                    . "WHERE op.order_id = o.order_id GROUP BY op.order_id) AS products, "
                    . "(SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot "
                    . "WHERE ot.order_id = o.order_id AND ot.code = 'tax' "
                    . "GROUP BY ot.order_id) AS tax,(SELECT SUM(ot.value) FROM `oc_order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'sub_total' GROUP BY ot.order_id) AS sub_total, SUM(o.total) AS `total` "
                    . "FROM `" . DB_PREFIX . "order` o join oc_store on o.store_id=oc_store.store_id";


		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
                if (!empty($data['filter_store'])) 
                {
                $sql .=" AND o.store_id='".(int)$data['filter_store']."'";
                }
                
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
                 
		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY DATE(o.date_added),o.store_id";//YEAR(o.date_added), WEEK(o.date_added)";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added)";
				break;
		}

		$sql .= " ORDER BY o.date_added DESC) as aaa";

		$query = $this->db->query($sql);

		return $query->row;
	}

	public function getTaxes($data = array()) {
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, ot.title, SUM(ot.value) AS total, COUNT(o.order_id) AS `orders` FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (ot.order_id = o.order_id) WHERE ot.code = 'tax'";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY YEAR(o.date_added), WEEK(o.date_added), ot.title";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), ot.title";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added), ot.title";
				break;
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalTaxes($data = array()) {
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), WEEK(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'month':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
		}

		$sql .= " LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (o.order_id = ot.order_id) WHERE ot.code = 'tax'";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getShipping($data = array()) {
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, ot.title, SUM(ot.value) AS total, COUNT(o.order_id) AS `orders` FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (o.order_id = ot.order_id) WHERE ot.code = 'shipping'";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY YEAR(o.date_added), WEEK(o.date_added), ot.title";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), ot.title";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added), ot.title";
				break;
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalShipping($data = array()) {
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), WEEK(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'month':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
		}

		$sql .= " LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (o.order_id = ot.order_id) WHERE ot.code = 'shipping'";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
         //////////////////sale graph start here////////////////////
 public function getTotalSaleByDay() {
            $match=array();
            $match['order_status_id']=5;
            $match['date_added']=array(
                   '$gte'=>new MongoDate(strtotime(date('Y-m-d')))
                   );
                  
            $groupbyarray=array(
                 "_id"=> array('$hour'=> '$date_added'), 
                "total"=> array('$sum'=> '$total' ) 
            );
            
            $query = $this->db->query('gettotalcount','oc_order',$groupbyarray,$match);
            
            for ($i = 0; $i < 24; $i++) {
			$order_data[$i] = array(
				'hour'  => $i,
				'total' => 0
			);
		}
            foreach ($query->row as $result) 
            {
			$order_data[$result['_id']] = array(
				'hour'   => $result['_id'],
				'total' => $result['total']
			);
            }
		return $order_data;
	}
              public function getTotalSaleByWeek() {
		
		return $order_data;
	}
        public function getTotalSaleByMonth($year='',$month='') 
        {
           
            $match=array();
            $match['order_status_id']=5;
            $match['date_added']=array(
                   '$gte'=>new MongoDate(strtotime(date('Y-m').'-01'))
                   );
                  
            $groupbyarray=array(
                 "_id"=> array('$dayOfMonth'=> '$date_added'), 
                "total"=> array('$sum'=> '$total' ) 
            );
            
            $query = $this->db->query('gettotalsum','oc_order',$groupbyarray,$match);
            //print_r($query->row);
            for ($i = 1; $i <= date('t'); $i++) {
			$date = date('Y') . '-' . date('m') . '-' . $i;

			$order_data[date('j', strtotime($date))] = array(
				'day'   => date('d', strtotime($date)),
				'total' => 0
			);
		}
            foreach ($query->row as $result) 
            {
			$order_data[$result['_id']] = array(
				'day'   => date('d', strtotime($result['_id'])),
				'total' => $result['total']
			);
            }
		return $order_data;
	
            
	}
 public function getTotalSaleByYear() {
     /*
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}
				
		$order_data = array();

		for ($i = 1; $i <= 12; $i++) {
			$order_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT SUM(total) AS total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(",", $implode) . ") AND YEAR(date_added) = YEAR(NOW()) GROUP BY MONTH(date_added)");

		foreach ($query->rows as $result) {
			$order_data[date('n', strtotime($result['date_added']))] = array(
				'month' => date('M', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $order_data;
                */
            $match=array();
            $match['order_status_id']=5;
            $match['date_added']=array(
                   '$gte'=>new MongoDate(strtotime(date('Y').'-01-01')),
                   '$lte'=>new MongoDate(strtotime(date('Y').'-12-31'))
                   );
                  
            $groupbyarray=array(
                 "_id"=> array('$month'=> '$date_added'), 
                "total"=> array('$sum'=> '$total' ) 
            );
            
            $query = $this->db->query('gettotalsum','oc_order',$groupbyarray,$match);
               
                $order_data = array();
		for ($i = 1; $i <= 12; $i++) {
			$order_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i)),
				'total' => 0
			);
		}

		foreach ($query->row as $result) {
			$order_data[$result['_id']] = array(
				'month' => date('M', mktime(0, 0, 0, $result['_id'])),
				'total' => $result['total']
			);
		}

		return $order_data;
	}            

//company wise
public function getOrdersCompanywise($data = array()) {
		//$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, COUNT(*) AS `orders`, (SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id) AS products, (SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'tax' GROUP BY ot.order_id) AS tax, SUM(o.total) AS `total` FROM `" . DB_PREFIX . "order` o";


               $sql = "SELECT oc_store.name as store_name,MIN(o.date_added) AS date_start, "
                    . "MAX(o.date_added) AS date_end, COUNT(*) AS `orders`,"
                    . " (SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op "
                    . "WHERE op.order_id = o.order_id GROUP BY op.order_id) AS products, "
                    . "(SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot "
                    . "WHERE ot.order_id = o.order_id AND ot.code = 'tax' "
                    . "GROUP BY ot.order_id) AS tax,(SELECT SUM(ot.value) FROM `oc_order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'sub_total' GROUP BY ot.order_id) AS sub_total, SUM(o.total) AS `total` "
                    . "FROM `" . DB_PREFIX . "order` o join oc_store on o.store_id=oc_store.store_id
                       
                      where oc_store.company_id='".$data['filter_company']."' ";


		if (!empty($data['filter_order_status_id'])) {
			$sql .= " and o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= "and o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
                if (!empty($data['filter_store'])) 
                {
                $sql .=" AND o.store_id='".(int)$data['filter_store']."'";
                }
                
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
            
                 
		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY DATE(o.date_added),o.store_id";//YEAR(o.date_added), WEEK(o.date_added)";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added)";
				break;
		}
                
		$sql .= " ORDER BY o.date_added DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
               // echo $sql;
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalOrdersCompanywise($data = array()) {

                $sql = "select count(*) as total,sum(total) as amount_total,sum(orders) as orders from (SELECT oc_store.name as store_name,MIN(o.date_added) AS date_start, "
                    . "MAX(o.date_added) AS date_end, COUNT(*) AS `orders`,"
                    . " (SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op "
                    . "WHERE op.order_id = o.order_id GROUP BY op.order_id) AS products, "
                    . "(SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot "
                    . "WHERE ot.order_id = o.order_id AND ot.code = 'tax' "
                    . "GROUP BY ot.order_id) AS tax,(SELECT SUM(ot.value) FROM `oc_order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'sub_total' GROUP BY ot.order_id) AS sub_total, SUM(o.total) AS `total` "
                    . "FROM `" . DB_PREFIX . "order` o join oc_store on o.store_id=oc_store.store_id 
                        
                       where oc_store.company_id='".$data['filter_company']."' ";


		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
                if (!empty($data['filter_store'])) 
                {
                $sql .=" AND o.store_id='".(int)$data['filter_store']."'";
                }
                
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
                 
		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY DATE(o.date_added),o.store_id";//YEAR(o.date_added), WEEK(o.date_added)";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added)";
				break;
		}

		$sql .= " ORDER BY o.date_added DESC) as aaa";

		$query = $this->db->query($sql);

		return $query->row;
	}
	public function getsaleorderTotal($data=array()) {
    
            $groupbyarray=array();
            $match=array();
			if(!empty($data['store_id']))
			{
				$match['store_id']=(int)$data['store_id'];
			}
            $sort_array=array();
			
            $limit=''; 
            $unwind='';
            $match['order_status_id']=5;
			$query=$this->db->query('select','oc_order','','','',$match,'','',array('total'),(int)0,'','','');
			//print_r($query->rows);exit;
			
            return $query->rows;
            
	}
	public function getTop_5_Products($data=array()) {
    
            $groupbyarray=array(
                 "_id"=> '$order_product.product_id', 
                "sales_of_qnty"=> array('$sum'=> '$order_product.quantity'),
                "model"=>array('$first'=> '$order_product.name')
            );
            $match=array('order_product.quantity'=>array('$gt'=>0));
			if(!empty($data['store_id']))
			{
				$match['store_id']=(int)$data['store_id'];
			}
            $sort_array=array("sales_of_qnty"=>-1);
			
            $limit=5; 
            $unwind=('$order_product');
            $query =$this->db->query('gettotalcount','oc_order',$groupbyarray,$match,$sort_array,'','',$limit,$unwind);
            //print_r($query->row);
            return $query->row;
            
	}
	public function getTop_5_category($data=array()) {
    
	
            $groupbyarray=array(
			array(
			"_id"=>array("str"=> '$store_id',"cat"=> '$order_product.category_id'),
            "total"=>array('$sum'=> '$order_product.total' )    
            ),
			array(
			"_id"=> '$_id.str',
			"prd"=>array(
				'$push'=>array("type"=>'$_id.cat',"total"=>'$total')
				)
			)
			);
            $match=array('order_product.quantity'=>array('$gt'=>0));
			if(!empty($data['store_id']))
			{
				$match['store_id']=(int)$data['store_id'];
			}
            $sort_array=array();
			
            $limit=5; 
            $unwind=('$order_product');
			//$query =$this->db->query('join','oc_order','','',$match,'','',(int)$data['limit'],array(),(int)$data['start'],$sort,'',$group);
            $query =$this->db->query('join','oc_order','',$unwind,$match,'','',$limit,array(),(int)0,$sort_array,'',$groupbyarray);
            //print_r($query->row[0]['prd']);
			//exit;
            return $query->row[0]['prd'];
            
	}
	
        public function getsaleorder($store_id='',$year='') 
		{
			$match=array();
            $match['order_status_id']=5;
			if(!empty($store_id))
			{
				$match['store_id']=(int)$store_id;
			}
			if(!empty($year))
			{
				$match['date_added']=array(
                   '$gte'=>new MongoDate(strtotime($year.'-01-01')),
                   '$lte'=>new MongoDate(strtotime($year.'-12-31'))
                   );
			}
			else
			{
            $match['date_added']=array(
                   '$gte'=>new MongoDate(strtotime(date('Y').'-01-01')),
                   '$lte'=>new MongoDate(strtotime(date('Y').'-12-31'))
                   );
			} 
            $groupbyarray=array(
                 "_id"=> array('$month'=> '$date_added'), 
                "totalorder"=> array('$sum'=> 1 ),
                "total"=> array('$sum'=> '$total' ) 
            );
            $sort_array=array('_id'=>1);
            $query = $this->db->query('gettotalcount','oc_order',$groupbyarray,$match,$sort_array);
               
                $order_data = array();
		for ($i = 1; $i <= 12; $i++) 
		{
			$order_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i,1)),
				'totalorder' => 0,
                            'total' => 0
			);
		}
		
		foreach ($query->row as $result) {
			$order_data[$result['_id']] = array(
				'month' => date('M', mktime(0, 0, 0, $result['_id'],1)),
				'totalorder' => $result['totalorder'],
                                'total' => $result['total']
			);
		}		
		return $order_data;
	}


}