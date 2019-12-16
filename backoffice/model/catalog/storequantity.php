<?php
class ModelCatalogStorequantity extends Model {


public function calculate($value, $tax_class_id, $calculate = true, $fixed_taxes = true) {
		if ($tax_class_id == 0) return $value;
		if ($tax_class_id && $calculate) {
			$amount = $this->getTax($value, $tax_class_id, $fixed_taxes);
	
			return $amount;
			
		} else {
			return $value;
		}
	}
	
	public function getTax($value, $tax_class_id, $fixed_taxes = true) {
		$amount = 0;

		$tax_rates = $this->getRates($value, $tax_class_id);
	
		foreach ($tax_rates as $tax_rate) {
			if (!$fixed_taxes && $tax_rate['type'] == 'F') {
				
			}
			else {
				$amount += $tax_rate['amount'];
			}
		}
		
		return $value + $amount;
	
		//return $amount;
	}
	
	public function getTaxRates($tax_class_id) {
		$tax_rates = array();
		
		$customer_group_id = $this->config->get('config_customer_group_id');
		if ($this->config->get('config_tax_included_store_based') or !$this->config->get('config_tax_included')) {
			$this->store_address = array(
					'country_id' => $this->config->get('config_country_id'),
					'zone_id'    => $this->config->get('config_zone_id')
			);
			$this->shipping_address = array(
					'country_id' => $this->config->get('config_country_id'),
					'zone_id'    => $this->config->get('config_zone_id')
			);
			$this->payment_address = array(
					'country_id' => $this->config->get('config_country_id'),
					'zone_id'    => $this->config->get('config_zone_id')
			);
		}
		else 
                {
			$this->store_address = array(
					'country_id' => $this->config->get('config_country_id'),
					'zone_id'    => $this->config->get('config_zone_id')
			);
			$this->shipping_address = array(
					'country_id' => $this->config->get('config_tax_included_country_id'),
					'zone_id'    => $this->config->get('config_tax_included_zone_id')
			);
			$this->payment_address = array(
					'country_id' => $this->config->get('config_tax_included_country_id'),
					'zone_id'    => $this->config->get('config_tax_included_zone_id')
			);
		}
                
            if ($this->shipping_address) 
            {
                $based='shipping';
            }
            if ($this->payment_address) 
            {
                $based='payment';
            }
            if ($this->store_address) 
            {
                $based='store';
            }
            
            $match=array("tax_class_id"=>$tax_class_id,
                "based"=>$based,
                'tr2cg.customer_group_id'=>1,
                'z2gz.country_id'=>99,
                'z2gz.zone_id'=>1505
                );
            $lookup='';
            $lookupwithunwind=array(
                        array('lookup'=>
                                array(
                                'from' => 'oc_tax_rate',
                                'localField' => 'tax_rate_id',
                                'foreignField' => 'tax_rate_id',
                                'as' => 'tr2'
                                ),
                                'unwind'=>'$tr2'
                        ),
                        array('lookup'=>
                                array(
                                'from' => 'oc_tax_rate_to_customer_group',
                                'localField' => 'tax_rate_id',
                                'foreignField' => 'tax_rate_id',
                                'as' => 'tr2cg'
                                ),
                                'unwind'=>'$tr2cg'
                        ),
                        array('lookup'=>
                                array(
                                'from' => 'oc_zone_to_geo_zone',
                                'localField' => 'tr2.geo_zone_id',
                                'foreignField' => 'geo_zone_id',
                                'as' => 'z2gz'
                                ),
                                'unwind'=>'$z2gz'
                        )
                    );
            
                $columns=array( 
                    "tax_rule_id"=> 1,
                    "tax_class_id"=> 1,
                    "tax_rate_id"=>1,
                    "based"=>1,
                    "priority"=>1,
                  
                    "tr2.geo_zone_id" =>1,
                    "tr2.name" =>1,
                    "tr2.rate" =>1,
                    "tr2.type" =>1,
                    "tr2.date_added" =>1,
                    "tr2.date_modified" =>1,
                    
                    "tr2cg.customer_group_id" => 1,
   
                    "z2gz.zone_to_geo_zone_id"=>1,
                    "z2gz.country_id"=>1,
                    "z2gz.zone_id"=>1,
                    "z2gz.geo_zone_id"=>1,
                    "z2gz.date_added"=>1,
                    "z2gz.date_modified"=>1
                );
            $sort_array=array();
            $limit='';
            $start='';
           
            
            $tax_query = $this->db->query("join",DB_PREFIX . "tax_rule",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array,$lookupwithunwind);
            //print_r($tax_query->rows);
            foreach ($tax_query->rows as $result) 
            {
				$tax_rates[$result['tax_rate_id']] = array(
						'tax_rate_id' => $result['tax_rate_id'],
						'name'        => $result['tr2']['name'],
						'rate'        => $result['tr2']['rate'],
						'type'        => $result['tr2']['type'],
						'priority'    => $result['priority']
				);
            }
            return $tax_rates;   
                
	}
	
	public function getRates($value, $tax_class_id) {
		$tax_rates = array();
	
		$customer_group_id = $this->config->get('config_customer_group_id');
		
		if ($this->config->get('config_tax_included_store_based') or !$this->config->get('config_tax_included')) {
			$this->store_address = array(
					'country_id' => $this->config->get('config_country_id'),
					'zone_id'    => $this->config->get('config_zone_id')
			);
			$this->shipping_address = array(
					'country_id' => $this->config->get('config_country_id'),
					'zone_id'    => $this->config->get('config_zone_id')
			);
			$this->payment_address = array(
					'country_id' => $this->config->get('config_country_id'),
					'zone_id'    => $this->config->get('config_zone_id')
			);
		}
		else {
			$this->store_address = array(
					'country_id' => $this->config->get('config_country_id'),
					'zone_id'    => $this->config->get('config_zone_id')
			);
			$this->shipping_address = array(
					'country_id' => $this->config->get('config_tax_included_country_id'),
					'zone_id'    => $this->config->get('config_tax_included_zone_id')
			);
			$this->payment_address = array(
					'country_id' => $this->config->get('config_tax_included_country_id'),
					'zone_id'    => $this->config->get('config_tax_included_zone_id')
			);
		}
                
                if ($this->shipping_address) 
            {
                $based='shipping';
            }
            if ($this->payment_address) 
            {
                $based='payment';
            }
            if ($this->store_address) 
            {
                $based='store';
            }
            
            $match=array("tax_class_id"=>$tax_class_id,
                "based"=>$based,
                'tr2cg.customer_group_id'=>1,
                'z2gz.country_id'=>99,
                'z2gz.zone_id'=>1505
                );
            $lookup='';
            $lookupwithunwind=array(
                        array('lookup'=>
                                array(
                                'from' => 'oc_tax_rate',
                                'localField' => 'tax_rate_id',
                                'foreignField' => 'tax_rate_id',
                                'as' => 'tr2'
                                ),
                                'unwind'=>'$tr2'
                        ),
                        array('lookup'=>
                                array(
                                'from' => 'oc_tax_rate_to_customer_group',
                                'localField' => 'tax_rate_id',
                                'foreignField' => 'tax_rate_id',
                                'as' => 'tr2cg'
                                ),
                                'unwind'=>'$tr2cg'
                        ),
                        array('lookup'=>
                                array(
                                'from' => 'oc_zone_to_geo_zone',
                                'localField' => 'tr2.geo_zone_id',
                                'foreignField' => 'geo_zone_id',
                                'as' => 'z2gz'
                                ),
                                'unwind'=>'$z2gz'
                        )
                    );
            
                $columns=array( 
                    "tax_rule_id"=> 1,
                    "tax_class_id"=> 1,
                    "tax_rate_id"=>1,
                    "based"=>1,
                    "priority"=>1,
                  
                    "tr2.geo_zone_id" =>1,
                    "tr2.name" =>1,
                    "tr2.rate" =>1,
                    "tr2.type" =>1,
                    "tr2.date_added" =>1,
                    "tr2.date_modified" =>1,
                    
                    "tr2cg.customer_group_id" => 1,
   
                    "z2gz.zone_to_geo_zone_id"=>1,
                    "z2gz.country_id"=>1,
                    "z2gz.zone_id"=>1,
                    "z2gz.geo_zone_id"=>1,
                    "z2gz.date_added"=>1,
                    "z2gz.date_modified"=>1
                );
            $sort_array=array();
            $limit='';
            $start='';
           
            
            $tax_query = $this->db->query("join",DB_PREFIX . "tax_rule",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array,$lookupwithunwind);
            //print_r($tax_query->rows);
            foreach ($tax_query->rows as $result) 
            {
				$tax_rates[$result['tax_rate_id']] = array(
						'tax_rate_id' => $result['tax_rate_id'],
						'name'        => $result['tr2']['name'],
						'rate'        => $result['tr2']['rate'],
						'type'        => $result['tr2']['type'],
						'priority'    => $result['priority']
				);
            }
		$tax_rate_data = array();
	
		foreach ($tax_rates as $tax_rate) {
			if (isset($tax_rate_data[$tax_rate['tax_rate_id']])) {
				$amount = $tax_rate_data[$tax_rate['tax_rate_id']]['amount'];
			} else {
				$amount = 0;
			}
				
			if ($tax_rate['type'] == 'F') {
				$amount += $tax_rate['rate'];
			} elseif ($tax_rate['type'] == 'P') {
				$amount += ($value / 100 * $tax_rate['rate']);
			}
	
			$tax_rate_data[$tax_rate['tax_rate_id']] = array(
					'tax_rate_id' => $tax_rate['tax_rate_id'],
					'name'        => $tax_rate['name'],
					'rate'        => $tax_rate['rate'],
					'type'        => $tax_rate['type'],
					'amount'      => $amount
			);
		}
	
		return $tax_rate_data;
	}

//new
	
	public function editProduct($product_id, $data) 
	{ 
            //$this->event->trigger('pre.admin.product.edit', $data);
		$purchase_price=$this->request->post["purchase_price"];
                if(empty($purchase_price))
                {
                    $purchase_price=$this->request->post["base_price"];
                }
                $wholesale_price=$this->request->post["wholesale_price"];
                if(empty($wholesale_price))
                {
                    $wholesale_price=$this->request->post["base_price"];
                }
                $data['quantity']=0;
                
                $this->load->model('setting/store');
                $storeprd  = $this->model_setting_store->getStoresForProducts();
                
                
		$log=new Log("product-".date('Y-m-d').".log");
                $all_quantity=0;
                $price_withouttax='';
               
		foreach ($storeprd  as $store_id) 
		{ 
                    //$query_s=$this->db->query("select store_price from oc_product_to_store where product_id='".$product_id."' and  store_id = '" . $store_id['store_id'] . "' limit 1");
                    $where1=array('store_id'=>(int)$store_id['store_id'],'product_id'=>(int)$product_id);
                    
                    $query_s = $this->db->query('select',DB_PREFIX . 'product_to_store','','','',$where1,'','','','',array());
                    
                    $old_price= $query_s->row['store_price'];
                   
                    if($query_s->num_rows)
                    {   
                        //echo 'update';
                        $where=array('store_id'=>(int)$store_id['store_id'],'product_id'=>(int)$product_id);
                        $input_array=array('store_price'=>$data['quantitystoreprice'.$store_id['store_id']],
                                            'store_tax_type'=>'',
                                        'store_tax_amt' =>($data['qquantitystoreprice'.$store_id['store_id']]-$data['quantitystoreprice'.$store_id['store_id']]),
                                       'MOD_DATE'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                                        );
                        $query = $this->db->query("update",DB_PREFIX . "product_to_store",$where,$input_array);
			
                        if(!$query->rows['nModified'])
                        { 
                            //return true;//$query->rows['errmsg'];
                        }
                    }
                    else 
                    {
                        //echo 'insert';
                        $input_array3=array(
                                        'product_id'=>(int)$product_id,
                                        'store_id'=>(int)$store_id['store_id'],
                                        'quantity'=>0,
                                        'store_price'=>$data['quantitystoreprice'.$store_id['store_id']],
                                        'store_tax_type'=>'',
                                        'store_tax_amt' =>($data['qquantitystoreprice'.$store_id['store_id']]-$data['quantitystoreprice'.$store_id['store_id']]),
                                        'MOD_DATE'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                                        );
                        $query3 = $this->db->query("insert",DB_PREFIX . "product_to_store",$input_array3);
                        //print_r($query3);
                        if(!$query3->rows['n'])
                        {  
                            //return false;
                        }
                    }
                    
                    $all_quantity=$all_quantity+$data['quantitystore'.$store_id['store_id']];      
                    $price_withouttax=$data['quantitystoreprice'.$store_id['store_id']];
		
                    $input_array4=array(
                                        'product_id'=>(int)$product_id,
                                        'store_id'=>(int)$store_id['store_id'],
                                        'old_price'=>$old_price,
                                        'new_price'=>$data['quantitystoreprice'.$store_id['store_id']],
                                        'user_id' =>$this->user->getId(),
                                       
                                        );
                    
                    $query4 = $this->db->query("insert",DB_PREFIX . "product_price_log",$input_array4);
                    
		}
                    
                    //$upd_q="UPDATE " . DB_PREFIX . "product SET ";
                    //if($this->request->post["for_all_price"]!=="")
                    //{
                    //$upd_q.=" price_tax='".$this->db->escape($this->request->post["for_all_price"])."', ";
                    //}
                    //$upd_q.=" quantity = '" . (int)$all_quantity. "', 
                    //price = '" . (float)$price_withouttax. "',
                    // date_modified = NOW(),`purchase_price`='".$this->db->escape($purchase_price)."',
                    // `wholesale_price`='".$this->db->escape($wholesale_price)."' WHERE product_id = '" . (int)$product_id . "'";
                    if($this->request->post["for_all_price"]!=="")
                    {
                        $where5=array('product_id'=>(int)$product_id);
                        $input_array5=array(
                            'price_tax'=>(float)$this->db->escape($this->request->post["for_all_price"]),
                            'quantity'=>(int)$all_quantity,
                            'price'=>(float)$price_withouttax,
                            'date_modified'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
                            'purchase_price'=>(float)$this->db->escape($purchase_price),
                            'wholesale_price'=>(float)$this->db->escape($wholesale_price)
                            );
                        $query5 = $this->db->query("update",DB_PREFIX . "product",$where5,$input_array5);
                    }
                    else
                    {
                        $where5=array('product_id'=>(int)$product_id);
                        $input_array5=array(
                            'price'=>(float)$price_withouttax,
                            'quantity'=>(int)$all_quantity,
                            'date_modified'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
                            'purchase_price'=>(float)$this->db->escape($purchase_price),
                            'wholesale_price'=>(float)$this->db->escape($wholesale_price)
                            );
                        $query5 = $this->db->query("update",DB_PREFIX . "product",$where5,$input_array5);
                    }
                      
		$this->cache->delete('product');
                return true;
		//$this->event->trigger('post.admin.product.edit', $product_id);
	}
	public function editProductIsec($product_id, $data) 
	{
            
                //print_r($data);exit;
				//$this->event->trigger('pre.admin.product.edit', $data);
				$purchase_price=$this->request->post["purchase_price"];
                if(empty($purchase_price))
                {
                    $purchase_price=$this->request->post["base_price"];
                }
                $wholesale_price=$this->request->post["wholesale_price"];
                if(empty($wholesale_price))
                {
                    $wholesale_price=$this->request->post["base_price"];
                }
                $data['quantity']=0;
                
                $this->load->model('setting/store');
                $storeprd  = $this->model_setting_store->getStoresCompanyWise(3);
                
                foreach ( $storeprd   as $store_id) 
				{
                
                    if($data['quantitystore'.$store_id['store_id']]=='')
                    {
                        $data['quantitystore'.$store_id['store_id']]=0;
                    }
                    $data['quantity']+=$data['quantitystore'.$store_id['store_id']];
                    
                }
				$log=new Log("product-".date('Y-m-d').".log");
                $all_quantity=0;
                $price_withouttax='';
				foreach ($storeprd  as $store_id) 
				{ 
                $query_s=$this->db->query("select store_price from oc_product_to_store where product_id='".$product_id."' and  store_id = '" . $store_id['store_id'] . "' limit 1");
     
				$old_price= $query_s->row['store_price'];
                    
				$sql_p=" INSERT INTO oc_product_to_store SET product_id='".$product_id."', "
					. "store_id = '" . $store_id['store_id'] . "',"
        
					. "store_price='". $data['quantitystoreprice'.$store_id['store_id']]."',"
					." store_tax_amt='". ($data['qquantitystoreprice'.$store_id['store_id']]-$data['quantitystoreprice'.$store_id['store_id']])."' "
					." ON DUPLICATE KEY
					UPDATE "
					. "store_price='".$data['quantitystoreprice'.$store_id['store_id']]."'"
					. ",store_tax_amt='". ($data['qquantitystoreprice'.$store_id['store_id']]-$data['quantitystoreprice'.$store_id['store_id']])."' "
					;

                $log->write($sql_p);
			    $this->db->query($sql_p);
                $all_quantity=$all_quantity+$data['quantitystore'.$store_id['store_id']];      
                $price_withouttax=$data['quantitystoreprice'.$store_id['store_id']];
				
				$this->db->query("insert","oc_product_price_log",array('product_id'=>(int)$product_id,'store_id'=>(int)$store_id['store_id'],'old_price'=>$old_price,'new_price'=>$data['quantitystoreprice'.$store_id['store_id']],'user_id'=>(int)$this->user->getId()));
				
				}
				/*
                if($data['user_group_id'] =="35")
				{    
					$upd_q="UPDATE " . DB_PREFIX . "product SET quantity = '" . (int)$all_quantity. "' WHERE product_id = '" . (int)$product_id . "'";
                }
				else
				{     
				$upd_q="UPDATE " . DB_PREFIX . "product SET ";
				if($this->request->post["for_all_price"]!=="")
				{
					$upd_q.=" price_tax='".$this->db->escape($this->request->post["for_all_price"])."', ";
				}
                $upd_q.=" quantity = '" . (int)$all_quantity. "', price = '" . (float)$price_withouttax. "', date_modified = NOW(),`purchase_price`='".$this->db->escape($purchase_price)."',`wholesale_price`='".$this->db->escape($wholesale_price)."' WHERE product_id = '" . (int)$product_id . "'";
				//echo $upd_q;
				}
				*/
				$upd_q="UPDATE " . DB_PREFIX . "product SET quantity = '" . (int)$all_quantity. "' WHERE product_id = '" . (int)$product_id . "'";
				$log->write($upd_q); 
				$this->db->query($upd_q);
		
				$this->cache->delete('product');

				$this->event->trigger('post.admin.product.edit', $product_id);
	}
	
	public function getProduct($product_id) {
            $match=array();
                if (!empty($data['filter_name'])) 
                {
                    $search_string=$this->db->escape($data['filter_name']);
                    $match=array('pd.name'=> new MongoRegex("/.*$search_string/i"));
                    //$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) 
                {
                    $search_string=$this->db->escape($data['filter_model']);
                    $match=array('model'=> new MongoRegex("/.*$search_string/i"));
                    //$sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) 
                {
                    $search_string=$this->db->escape($data['filter_price']);
                    $match=array('price'=> new MongoRegex("/.*$search_string/i"));
                    //$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) 
                {
                    $match=array('quantity'=>(int)$data['filter_quantity']);
                    //$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) 
                {
                    $match=array('status'=> boolval($data['filter_status']));
                    //$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}
                
                
                if (isset($data['start']) || isset($data['limit'])) 
                {
			if ($data['start'] < 0) {
				$start = 0;
			}
                        else
                        {
                            $start=(int)$data['start'];
                        }

			if ($data['limit'] < 1) {
				$limit = (int)20;
			}
                        else 
                        {
                                $limit = (int)$data['limit'];
                        }

			
		}
                
                
                $lookup=array(
                'from' => 'oc_product_description',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
                );
            
                $columns=array( 
                    "_id"=> 1,
                    "product_id"=> 1,
                    "model"=>1,
                    "sku"=>1,
                    "upc"=>1,
                    "ean"=>1,
                    "jan"=>1,
                    "isbn"=>1,
                    "mpn"=>1,
                    "location"=>1,
                    "quantity"=>1,
                    "stock_status_id"=>1,
                    "image"=>1,
                    "manufacturer_id"=>1,
                    "shipping"=>1,
                    "price"=>1,
                    "points"=>1,
                    "tax_class_id"=>1,
                    "date_available"=>1,
                    "weight"=>1,
                    "weight_class_id"=>1,
                    "length"=>1,
                    "width"=>1,
                    "height"=>1,
                    "length_class_id"=>1,
                    "subtract"=>1,
                    "minimum"=>1,
                    "sort_order"=>1,
                    "status"=>1,
                    "viewed"=>1,
                    "date_added"=>1,
                    "date_modified"=> 1,
                    "HSTN"=>1,
                    "price_tax"=> 1,
                    "purchase_price"=>1,
                    "wholesale_price"=> 1,
                    "pd.language_id"=>1,
                    "pd.name"=>1,
                    "pd.description"=>1,
                    "pd.tag"=>1,
                    "pd.meta_title"=>1,
                    "pd.meta_description"=>1,
                    "pd.meta_keyword"=>1,
                    "pd.meta_hindi"=>1
                );
            $sort_array=array('pd.name'=>1);
            
            
            $query = $this->db->query("join",DB_PREFIX . "product",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array);
            //print_r($query);
            foreach($query->row as $row)
            {
                $return_array[]=array("product_id"=>$row["product_id"],
                    "model"=>$row["model"],
                    "sku"=>$row["sku"],
                    "upc"=>$row["upc"],
                    "ean"=>$row["ean"],
                    "jan"=>$row["jan"],
                    "isbn"=>$row["isbn"],
                    "mpn"=>$row["mpn"],
                    "location"=>$row["location"],
                    "quantity"=>$row["quantity"],
                    "stock_status_id"=>$row["stock_status_id"],
                    "image"=>$row["image"],
                    "manufacturer_id"=>$row["manufacturer_id"],
                    "shipping"=>$row["shipping"],
                    "price"=>$row["price"],
                    "points"=>$row["points"],
                    "tax_class_id"=>$row["tax_class_id"],
                    "date_available"=>$row["date_available"],
                    "weight"=>$row["weight"],
                    "weight_class_id"=>$row["weight_class_id"],
                    "length"=>$row["length"],
                    "width"=>$row["width"],
                    "height"=>$row["height"],
                    "length_class_id"=>$row["length_class_id"],
                    "subtract"=>$row["subtract"],
                    "minimum"=>$row["minimum"],
                    "sort_order"=>$row["sort_order"],
                    "status"=>$row["status"],
                    "viewed"=>$row["viewed"],
                    "date_added"=>$row["date_added"],
                    "date_modified"=> $row["date_modified"],
                    "HSTN"=>$row["HSTN"],
                    "price_tax"=> $row["price_tax"],
                    "purchase_price"=>$row["purchase_price"],
                    "wholesale_price"=> $row["wholesale_price"],
                    "language_id"=>$row["pd"][0]["language_id"],
                    "name"=>$row["pd"][0]["name"],
                    "description"=>$row["pd"][0]["description"],
                    "tag"=>$row["pd"][0]["tag"],
                    "meta_title"=>$row["pd"][0]["meta_title"],
                    "meta_description"=>$row["pd"][0]["meta_description"],
                    "meta_keyword"=>$row["pd"][0]["meta_keyword"],
                    "meta_hindi"=>$row["pd"][0]["meta_hindi"],
                            'totalrows'=>$query->total_rows    
                        );
            }
            
            //$return_array['total_rows']=$query->total_rows;
            return $return_array;
	}


	public function getProductDescriptions($product_id) {
		$product_description_data = array();
                
            $query = $this->db->query("select",DB_PREFIX . "product_description",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            
            foreach ($query->rows as $result) 
            {
               
               $product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
            }
            return $product_description_data;
	}


	public function getProductStores($product_id) {
		$product_store_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
                $query = $this->db->query("select",DB_PREFIX . "product_to_store",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            
		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}
        public function getProductStoresQuantity($product_id) 
        {
		
                $product_store_data = array();
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
                $query = $this->db->query("select",DB_PREFIX . "product_to_store",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            
                foreach ($query->rows as $result) 
                {
                    //$result["tax"]=$this->getTax($result["store_price"], $result["tax_class_id"]);  
                    array_push($product_store_data, $result);
                        
		}

		return $product_store_data;
	}

        
        public function getProductStoresQuantityHtml($product_id) {
		
                $product_store_data='';
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store as prd LEFT JOIN " . DB_PREFIX . "store as st on st.store_id=prd.store_id WHERE product_id = '" . (int)$product_id . "'");		
                foreach ($query->rows as $result) {
                    if($result['name']==''){
                        $storename=  $this->config->get('config_name');
                    }else{
                    $storename=$result['name'];}
                    $product_store_data=$product_store_data.$storename."-".$result['quantity']."<br/>";                        
		}

		return $product_store_data;
	}
        
}