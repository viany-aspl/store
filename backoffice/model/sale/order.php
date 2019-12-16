<?php
class ModelSaleOrder extends Model {


	public function getOrder($order_id) { 
            $where=array("order_id"=>(int)$order_id );
		$order_query =$this->db->query('select',DB_PREFIX . "order",'','','',$where);//"SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$reward = 0;

			$order_product_query =$order_query->row['order_product']; //$this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

			foreach ($order_product_query as $product) {
				$reward += $product['reward'];
			}

			/*$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
				$language_filename = $language_info['filename'];
				$language_directory = $language_info['directory'];
			} else {
				$language_code = '';
				$language_filename = '';
				$language_directory = '';
			}*/

			return array(
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'customer'                => $order_query->row['customer'],
				'customer_group_id'       => $order_query->row['customer_group_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'email'                   => $order_query->row['email'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
                                'card_no' => $order_query->row['card_no'],
				'custom_field'            => unserialize($order_query->row['custom_field']),
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_custom_field'    => unserialize($order_query->row['payment_custom_field']),
				'payment_method'          => $order_query->row['payment_method'],
				'payment_code'            => $order_query->row['payment_code'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_custom_field'   => unserialize($order_query->row['shipping_custom_field']),
				'shipping_method'         => $order_query->row['shipping_method'],
				'shipping_code'           => $order_query->row['shipping_code'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'reward'                  => $reward,
				'order_status_id'         => $order_query->row['order_status_id'],
				'affiliate_id'            => $order_query->row['affiliate_id'],
				'affiliate_firstname'     => $affiliate_firstname,
				'affiliate_lastname'      => $affiliate_lastname,
				'commission'              => $order_query->row['commission'],
				'language_id'             => $order_query->row['language_id'],
				'language_code'           => $language_code,
				'language_filename'       => $language_filename,
				'language_directory'      => $language_directory,
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'ip'                      => $order_query->row['ip'],
				'forwarded_ip'            => $order_query->row['forwarded_ip'],
				'user_agent'              => $order_query->row['user_agent'],
				'accept_language'         => $order_query->row['accept_language'],
				'date_added'              => $order_query->row['date_added'],
				'date_modified'           => $order_query->row['date_modified'],
				'tagged'		=>   $order_query->row['credit'],
				'cash'		=>   $order_query->row['cash'],
                'credit'		=>   $order_query->row['credit'],
				'subsidy'		=>   $order_query->row['subsidy'],
				'discount'		=>   $order_query->row['discount'],
                                'products'=>$order_query->row['order_product'],
                                'totals'=>$order_query->row['order_total'],

			);
		} else {
			return;
		}
	}

	public function getOrders($data = array()) 
        {
            $where=array();
            if (isset($data['filter_order_status'])) 
            {
		$where['order_status_id']=(int)$data['filter_order_status'];
            } 
            else 
            {
		$where['order_status_id']=5;
            }
            if (!empty($data['filter_customer'])) 
            {
                $where['telephone']=(string)$data['filter_customer'] ;
            }
            if (!empty($data['filter_order_id'])) 
            {
		$where['order_id']= (int)$data['filter_order_id'] ;
            }
            
            if (!empty($data['filter_payment'])) 
            {
		$where['pay_method']= $data['filter_payment'] ;
            }
            if (!empty($data['filter_customer'])) 
            {
		$search_string=( $this->db->escape($data['filter_customer']));
                //$where['firstname']= new MongoRegex("/.*.$search_string./i"); 
            }
            if (!empty($data['filter_date_added'])) 
            {
		$sdate=$this->db->escape($data['filter_date_added']) ;
            }
            if (!empty($data['filter_date_modified'])) 
            {
		$edate =$this->db->escape($data['filter_date_modified']) ;
            }
            $datedata=array();
            if(strtotime($sdate)==strtotime($edate))
            {
                $datedata=array(
                            '$gt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime($sdate)))  )),
                            '$lt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('1 day', strtotime($edate))) ))
                            );
            }
            else
            {
                $datedata=array(
                            '$gte'=>new MongoDate(strtotime($sdate)),
                            '$lte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('1 day', strtotime($edate)))))
                            );
            }
            if((!empty($sdate)) && (!empty($edate)))
            {
                $where['date_added']=$datedata;
            }
            
            if (!empty($data['filter_store'])) 
            {
				$where['store_id']= (int)$data['filter_store'] ; 
            }
			if (!empty($data['billtype'])) 
            {
				if($data['billtype']=='2')
				{
					$data['billtype']=(int)0;
				}
				$where['billtype']= (int)$data['billtype'] ; 
            }
			
            if(!empty($data['filter_user_id']))
            {
                $where['user_id']= (int)$data['filter_user_id'] ; 
            }
            if(!empty($data['filter_product_id']))
            {
                $where['order_product.product_id']= (int)$data['filter_product_id'] ; 
            }
            
            $sort_data = array('order_id'=>-1);
            if (isset($data['start']) || isset($data['limit'])) 
            {
		if ($data['start'] < 0) 
                {
                    $data['start'] = 0;
		}
                if ($data['limit'] < 1) 
                {
                    $data['limit'] = 20;
		}
                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            }
            //print_r(json_encode($where));exit;
            $query = $this->db->query('select',DB_PREFIX . "order",'','','',$where,'',(int)$data['limit'],'',(int)$data['start'],$sort_data);
            return $query;
	}
	public function getTotalOrders($data = array()) { 
             
            $match=array();
                if (!empty($data['filter_order_status'])) 
                {
                   
                    $match['order_status_id']=(int)$data['filter_order_status'];
		} 
                else 
                {
                    
                    $match['order_status_id']=5;
		}

		if (!empty($data['filter_order_id'])) 
                {
                    $match['order_id']=(int)$data['filter_order_id'];
                 
		}
		
		if (!empty($data['filter_payment'])) 
                {
                    $match['payment_method']=$data['filter_payment'];
		}
		
		if (!empty($data['filter_date_added'])) 
                {
                        $match['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'])));
                        
                }

		if (!empty($data['filter_date_modified'])) 
                {
                    $match['date_added']=array('$lte'=>new MongoDate(strtotime($data['filter_date_modified'])));
                }

		if (!empty($data['filter_total'])) 
                {
                    $match['total']=(int)$data['filter_total'];
		}
                if (!empty($data['filter_store'])) 
                {
                    $match['store_id']=(int)$data['filter_store'];
		}
                if(!empty($data['filter_user_id']))
                {
                   $match['user_id']=(int)$data['filter_user_id'];
                }

            $groupbyarray=array(
                 "_id"=> '$order_status_id', 
                "count"=> array('$sum'=> 1 ) 
            );
            
                //$query = $this->db->query('gettotalcount','oc_order',$groupbyarray,$match);
		$query = $this->db->getcount('oc_order',$match);
                
               // print_r(json_encode($match));
		return $query;
	}
	public function getOrderProducts($order_id) 
        {
            $query = $this->db->query("select",DB_PREFIX . "order",'','','',array('order_id'=>(int)$order_id));
            return $query->row['order_product'];
	}
	public function getOrderTotals($order_id) 
        {
            $query = $this->db->query('select',DB_PREFIX . "order_total",'','','',array('order_id'=>(int)$order_id));//"SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");
            return $query->rows;
	}
	public function getProductSubsidy($product_id,$store_id) 
        {
            $query = $this->db->query("select",DB_PREFIX . "product_subsidy",'','','',array('store_id' =>(int)$store_id,'product_id' =>(int)$product_id,'customer_group_id' =>(int)$this->config->get('config_customer_group_id'),'quantity'=>array('$gte'=>1)));
            return $query->row["subsidy"];
	}

        public function getOrderStoreId($order_id) 
        {
            $query = $this->db->query("select",DB_PREFIX . "order",'','','',array('order_id' => (int)$order_id));
            return $query->row["store_id"];
	}
        public function getOrderUser($order_id) 
        {
		$query = $this->db->query("select", DB_PREFIX . "order", '','','',array('order_id' => (int)$order_id));
		return $query->row["user_id"];
	}
        public function gettodaysales_cash_tageed_subsidy($sdate,$edate,$store_id) 
        {
                $log=new Log("today-".date('Y-m-d').".log");
                 $datedata=array();
            if(strtotime($sdate)==strtotime($edate))
            {
                $datedata=array(
                                    '$gt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime($sdate)))  )),
                                    '$lt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('1 day', strtotime($edate))) ))
                                    
                                );
            }
            else
            {
                $datedata=array(
                                    '$gte'=>new MongoDate(strtotime($sdate)),
                                    '$lte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('1 day', strtotime($edate)))))
                                );
            }
            
                $match=array('order_status_id'=>5,'store_id'=>(int)$store_id,
                    'date_added'=>$datedata);
                //print_r($match);
                    $group=array();
                    $group[]=array(
                    "_id" =>array('store'=>'$store_id',"payment"=>'$payment_method'),
                    "total" =>array('$sum'=>'$total'),
                    "credit" =>array('$sum'=>'$credit'),
                    "cash"=> array('$sum'=>'$cash'),
	   "discount"=>array('$sum'=>'$discount'),	
                    "count"=> array('$sum'=>1));
               
               
                $group[]=array("_id"=>'$_id.store',"paytype"=>
                    array('$push'=>array("type"=>'$_id.payment',
                    "amount"=>'$total',
                    "credit"=> '$credit',
                    "cash"=> '$cash',
	"discount"=>'$discount',	
                    "ocount"=>'$count'
                    )),"ctotal"=>array('$sum'=>'$total'));
                $log->write($match); 
                $sort=array("ctotal"=>-1);           
                $query=$this->db->query('join','oc_order','','',$match,'','','','','',$sort,'',$group);
                $return_array=array();
                $return_array['total']=$query->row[0]['ctotal'];
                $return_array['cash']=$query->row[0]['paytype'][0]['cash'];
                $return_array['credit']=$query->row[0]['paytype'][0]['credit'];
                $return_array['discount']=$query->row[0]['paytype'][0]['discount'];
                return $return_array;
	}
        public function getTop_5_Orders($data=array()) 
        {
            $groupbyarray=array(
                 "_id"=> '$store_id', 
                "total"=> array('$sum'=> '$total'),
                "store_name"=>array('$first'=> '$store_name')
            );
            $match['order_status_id']=5;
            $sort_array=array("total"=>-1);
            $limit=5; 
            $query =$this->db->query('gettotalcount','oc_order',$groupbyarray,$match,$sort_array,'','',$limit);
          
            return $query->row;
	}
        public function getTop_5_Products($data=array()) {
    
            $groupbyarray=array(
                 "_id"=> '$order_product.product_id', 
                "sales_of_qnty"=> array('$sum'=> '$order_product.quantity'),
                "model"=>array('$first'=> '$order_product.name')
            );
            $match=array('order_product.quantity'=>array('$gt'=>0));
            $sort_array=array("sales_of_qnty"=>-1);
            $limit=5; 
            $unwind=('$order_product');
            $query =$this->db->query('gettotalcount','oc_order',$groupbyarray,$match,$sort_array,'','',$limit,$unwind);
            //print_r($query->row);
            return $query->row;
            
	}
	public function getOrderInfo($order_id) 
        {
            $query = $this->db->query("select",DB_PREFIX . "order",'','','',array('order_id'=>(int)$order_id));
            return $query->row;
        }
	public function getsubsidyCategoryName($cat_id) 
        {
            $query = $this->db->query("select",'oc_category_subsidy','','','',array('category_id'=>(int)$cat_id));
            return $query->row['category_name']; 
        }
        public function getcustomerpurchaseproductdtl($telephone,$store_id) 
        {
			$log=new Log("getcustomerpurchaseproductdtl-".date('Y-m-d').".log");
			$log->write('in model getcustomerpurchaseproductdtl '); 
			$where=array('telephone'=>$telephone,'store_id'=>(int)$store_id);
			$log->write($where); 
            $query = $this->db->query('select','oc_order','','','',$where,'','','','',array('order_id'=>-1));
           
            return $query;
        }
	public function gettodayproductsalesdtl($sdate,$edate,$orderid,$store_id) 
        {
            $log=new Log("gettodayproductsalesdtl-".date('Y-m-d').".log");
            $log->write($orderid); 		
            $log->write("gettodayproductsalesdtl call"); 
            $query = $this->db->query('select','oc_order','','','',array('order_id'=>(int)$orderid));
            $log->write($sql); 
            //$log->write($query->rows); 
            return $query->rows;
	
	}
	public function gettodaysalesdtl($sdate,$edate,$store_id) 
        {
            $log=new Log("gettodaysalesdtl-".date('Y-m-d').".log");
            $log->write($sdate); 		
            if(empty($sdate))
            {
		$log->write("today sale "); 
		
		$query = $this->db->query('select','oc_order','','','',array('store_id'=>(int)$store_id,'order_status_id'=>5));
                
            }
            else
            {
		$log->write("filter on date  sale "); 
		$datedata=array();
            if(strtotime($sdate)==strtotime($edate))
            {
                $datedata=array(
                                    '$gt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime($sdate)))  )),
                                    '$lt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('1 day', strtotime($edate))) ))
                                    
                                );
            }
            else
            {
                $datedata=array(
                                    '$gte'=>new MongoDate(strtotime($sdate)),
                                    '$lte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('1 day', strtotime($edate)))))
                                );
            }
                
	
		$query = $this->db->query('select','oc_order','','','',array('store_id'=>(int)$store_id,'order_status_id'=>5,'date_added'=>$datedata));
            }
            $log->write($query->rows); 
            return $query->rows;
	}
    }