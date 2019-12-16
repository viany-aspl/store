<?php
class ModelCatalogProduct extends Model 
{
	public function deleteRewards($data) 
    {
        $data['store']=json_decode($data['store']);
        foreach($data['store'] as $store_id)
        {
            $query = $this->db->query("select",DB_PREFIX . "product_reward",'','','',array('product_id'=>(int)$data['product_id'],'store_id'=>(int)$store_id));
        
            if(($query->num_rows)>0)
            {
              $query = $this->db->query("delete",DB_PREFIX . "product_reward",array('product_id'=>(int)$data['product_id'],'store_id'=>(int)$store_id));
            }
        }
    }
	public function add_product_reward($data) 
	{
			//print_r($data);exit;
            $log=new Log("product-reward-".date('Y-m-d').".log");
            //for($a=0;count($data['product_id']);$a++)
            $a=0;
            foreach($data['product_id'] as $prd)
            {
                if(!empty($prd))
                {
					foreach($data['store_id'][$a] as $store_id)
					{
						$where1=array('product_id'=>(int)$prd,'store_id'=>(int)$store_id);//,'valid_till'=>array('$lt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime($data['valid_till'][$a])))  ))));
						$query = $this->db->query("select",DB_PREFIX . "product_reward",'','','',$where1);
						
						if($query->num_rows>0)
						{
							//echo '<br/>';
							//echo $query->row['valid_till']->sec;
							//echo '<br/>';
							//echo strtotime($data['valid_till'][$a]);
							//echo '<br/>';
							if(((float)$query->row['points']!=(float)$data['reward_points'][$a]) || ($query->row['valid_till']->sec!=strtotime($data['valid_till'][$a])))
							{
								//echo 'will update';
								$where=array('sid'=>(int)$query->row['sid']);
								$input_array=array(
								'store_id'=>(int)$store_id,
								'product_name'=>$data['product_name'][$a],
								'product_id'=>(int)$prd,
								'product_sku'=>$data['product_sku'][$a],
								'customer_group_id'=>(int)0,
								'valid_till'=>new MongoDate(strtotime($data['valid_till'][$a])),
								'update_date'=>new MongoDate(strtotime(date('Y-m-d'))),
								'points'=>(float)$data['reward_points'][$a],
								
								);
                    
								$query2 = $this->db->query("update",DB_PREFIX . "product_reward",$where,$input_array);
								
								$input_array_trans=array(
								'old_valid_date'=>new MongoDate($query->row['valid_till']->sec),
								'old_points'=>(float)$query->row['points'],
								'store_id'=>(int)$store_id,
								'product_name'=>$data['product_name'][$a],
								'product_id'=>(int)$prd,
								'product_sku'=>$data['product_sku'][$a],
								'customer_group_id'=>(int)0,
								'valid_till'=>new MongoDate(strtotime($data['valid_till'][$a])),
								'start_date'=>new MongoDate(strtotime(date('Y-m-d'))),
								'update_date'=>new MongoDate(strtotime(date('Y-m-d'))),
								'points'=>(float)$data['reward_points'][$a],
							);
							//print_r($input_array_trans);exit;
							$query3 = $this->db->query("insert",DB_PREFIX . "product_reward_trans_history",$input_array_trans);
							}
							
						}
						else 
						{
							$sid=$this->db->getNextSequenceValue('oc_product_reward');
							$input_array=array(
								'sid'=>(int)$sid,
								'store_id'=>(int)$store_id,
								'product_name'=>$data['product_name'][$a],
								'product_id'=>(int)$prd,
								'product_sku'=>$data['product_sku'][$a],
								'customer_group_id'=>(int)0,
								'valid_till'=>new MongoDate(strtotime($data['valid_till'][$a])),
								'start_date'=>new MongoDate(strtotime(date('Y-m-d'))),
								'update_date'=>new MongoDate(strtotime(date('Y-m-d'))),
								'points'=>(float)$data['reward_points'][$a],
							);
                    
							$query = $this->db->query("insert",DB_PREFIX . "product_reward",$input_array);
               
							$input_array_trans=array(
								'old_valid_date'=>'',
								'old_points'=>(float)0,
								'store_id'=>(int)$store_id,
								'product_name'=>$data['product_name'][$a],
								'product_id'=>(int)$prd,
								'product_sku'=>$data['product_sku'][$a],
								'customer_group_id'=>(int)0,
								'valid_till'=>new MongoDate(strtotime($data['valid_till'][$a])),
								'start_date'=>new MongoDate(strtotime(date('Y-m-d'))),
								'update_date'=>new MongoDate(strtotime(date('Y-m-d'))),
								'points'=>(float)$data['reward_points'][$a],
							);
                    
							$query = $this->db->query("insert",DB_PREFIX . "product_reward_trans_history",$input_array_trans);
						}
						$pquery = $this->db->query("select",DB_PREFIX . "product_to_store",'','','',array('product_id'=>(int)$prd,'store_id'=>(int)$store_id));
						if($pquery->num_rows==0)
						{
							$pinput_array=array(
								'product_id'=>(int)$prd,
								'store_id'=>(int)$store_id,
								'quantity'=>(int)0,
								'store_price'=>(float)0,
								'store_tax_amt'=>'',
								'store_tax_type'=>'',
								'mitra_quantity'=>(int)0
							);
                    
							$query = $this->db->query("insert",DB_PREFIX . "product_to_store",$pinput_array);
                
						}
					}
				}
                else 
                {
					$this->session->data['error']='Error for '.$data['product_name'][$a].', Please check and submit again';
                }
               $a++;
                
            }
	
	}
        public function getRewardsList($data)
        {
            $where=array();
            if(!empty($data['store_id']))
            {
                $where['store_id']=(int)$data['store_id'];
            }
            if(!empty($data['product_id']))
            {
                $where['product_id']=(int)$data['product_id'];
            }
			$where['valid_till']=array('$gte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime(date('Y-m-d'))))  )));
            //{$group:  { _id: {"pid":"$product_id","pts":"$points"}, stores: {$push: "$store_id"} } }
            $groupbyarray=array(array(
                 '_id'=>array("pid"=>'$product_id',"pts"=>'$points'),
                'stores'=>array('$push'=>'$store_id'),
                'product_name'=>array('$push'=>'$product_name'),
                'valid_till'=>array('$push'=>'$valid_till'),
				'product_sku'=>array('$push'=>'$product_sku')
            ));
            $sortby=array('product_name'=>1);
            $query = $this->db->query("join",DB_PREFIX . "product_reward",'','',$where,'','','','','',$sortby,'',$groupbyarray);
            //print_r($query);
            //exit;
            return $query;
        }
		public function getRewardsList_expired($data)
        {
            $where=array();
            if(!empty($data['store_id']))
            {
                $where['store_id']=(int)$data['store_id'];
            }
            if(!empty($data['product_id']))
            {
                $where['product_id']=(int)$data['product_id'];
            }
			$where['valid_till']=array('$lt'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime(date('Y-m-d'))))  )));
            //{$group:  { _id: {"pid":"$product_id","pts":"$points"}, stores: {$push: "$store_id"} } }
            $groupbyarray=array(array(
                 '_id'=>array("pid"=>'$product_id',"pts"=>'$points'),
                'stores'=>array('$push'=>'$store_id'),
                'product_name'=>array('$push'=>'$product_name'),
                'valid_till'=>array('$push'=>'$valid_till'),
				'product_sku'=>array('$push'=>'$product_sku')
            ));
            $sortby=array('product_name'=>1);
            $query = $this->db->query("join",DB_PREFIX . "product_reward",'','',$where,'','','','','',$sortby,'',$groupbyarray);
            //print_r($query);
            //exit;
            return $query;
        }
	public function editProductpromotion($product_id, $data) 
	{
		$log=new Log("product-edit-".date('Y-m-d').".log");

		$input_array=array(
                    'promotion_start_date'=>new MongoDate(strtotime($data['filter_date_start'])),
                    'promotion_end_date'=>new MongoDate(strtotime($data['filter_date_end'])),
					'date_modified'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                );
                $where=array('product_id'=>(int)$product_id);
                $query = $this->db->query("update",DB_PREFIX . "product",$where,$input_array);
               
		$this->cache->delete('product');

	}
	public function addProduct($data) 
	{
		
		$this->event->trigger('pre.admin.product.add', $data);
                $data['quantity']=0;
                $this->load->model('setting/store');
                $storeprd = $this->model_setting_store->getStoresForProducts();
				
                foreach ($storeprd as $store_id) 
                {
                
                    if($data['quantitystore'.$store_id['store_id']]=='')
                    {
                        $data['quantitystore'.$store_id['store_id']]=0;
                    }
                    $data['quantity']+=$data['quantitystore'.$store_id['store_id']];
                    
                }
                
		//$this->db->query("INSERT INTO " . DB_PREFIX . "product SET HSTN = '" . $this->db->escape($data['hstn']) . "',price_tax='".$this->db->escape($data["price_tax"])."',model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW()");
                
		$product_id=$this->db->getNextSequenceValue('oc_product');
                $input_array=array('product_id' =>(int)$product_id,
                                        'HSTN' =>$this->db->escape($data['hstn']),
                                        'price_tax' => doubleval($this->db->escape($data["price_tax"])),
                                        'name' =>strtoupper($this->db->escape($data['product_description'][1]['name'])), 
                                        'model' =>strtoupper($this->db->escape($data['model'])), 
                                        'sku' => $this->db->escape($data['sku']), 
                                        'upc' =>$this->db->escape($data['upc']), 
                                        'ean' =>$this->db->escape($data['ean']), 
                                        'jan' =>$this->db->escape($data['jan']), 
                                        'isbn' =>$this->db->escape($data['isbn']), 
                                        'mpn' =>$this->db->escape($data['mpn']), 
                                        'location' =>$this->db->escape($data['location']), 
                                        'quantity' =>(int)$data['quantity'], 
                                        'minimum' =>(int)$data['minimum'], 
                                        'subtract' =>(int)$data['subtract'], 
                                        'stock_status_id' =>(int)$data['stock_status_id'], 
                                        'date_available' =>new MongoDate(strtotime($data['date_available'])), 
                                        'manufacturer_id' =>(int)$data['manufacturer_id'], 
										'manufacturer'=>$data['manufacturer'],
                                        'shipping' =>(int)$data['shipping'], 
                                        'price' =>(float)$data['price'], 
                                        'points' => (int)$data['points'],
                                        'weight' =>(float)$data['weight'], 
                                        'weight_class_id' =>(int)$data['weight_class_id'], 
                                        'length' =>(float)$data['length'], 
                                        'width' =>(float)$data['width'], 
                                        'height' =>(float)$data['height'], 
                                        'length_class_id' =>(int)$data['length_class_id'], 
                                        'status' => boolval($data['status']), 
                                        'tax_class_id' =>(int)$this->db->escape($data['tax_class_id']), 
                                        'tax_class_name'=>$this->db->escape($data['tax_class_name']),
                                        'sort_order' =>(int)$data['sort_order'],
                                        'category_ids' => $data['product_category'],
                                        'category_name' =>$data['product_category_name'],
                                        'product_description'=>$data['product_description'],
                                        'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                                    );
                    $query2 = $this->db->query("insert",DB_PREFIX . "product",$input_array);
		

		if (isset($data['image'])) 
        {
                    $where=array('product_id'=>(int)$product_id);
                    $input_array2=array('image'=>$this->db->escape($data['image']));
                    $query = $this->db->query("update",DB_PREFIX . "product",$where,$input_array2);
			//$query = $this->db->query("insert",DB_PREFIX . "product_image",$where,$input_array2);		
                    //$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		foreach ($data['product_description'] as $language_id => $value) 
                {
                    //$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
                     $input_array3=array(
                            'product_id'=>(int)$product_id,
                            'language_id'=> (int)$language_id,
                            'name'=>strtoupper($this->db->escape($value['name'])),
                            'description'=>$this->db->escape($value['description']),
                            'tag'=>$this->db->escape($value['tag']),
                            'meta_title'=>$this->db->escape($value['meta_title']),
                            'meta_description'=>$this->db->escape($value['meta_description']),
                            'meta_keyword'=>$this->db->escape($value['meta_keyword'])
                         );
                    
                   
                    $query2 = $this->db->query("insert",DB_PREFIX . "product_description",$input_array3);
                }

		if (isset($data['product_store'])) 
                {
			foreach ($storeprd as $store_id) 
                        {
                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET  product_id = '" . (int)$product_id . "',store_id = '" . (int)$store_id['store_id'] . "',quantity = '" . (int)$data['quantitystore'.$store_id['store_id']] . "',store_price='".(float)$data['quantitystoreprice'.$store_id['store_id']]."'");
                            $input_array4=array(
                            'product_id'=>(int)$product_id,
                            'store_id'=> (int)$store_id['store_id'],
                            'quantity'=>(int)$data['quantitystore'.$store_id['store_id']],
                            'store_price'=>(float)$data['quantitystoreprice'.$store_id['store_id']]
                             );
                            $query2 = $this->db->query("insert",DB_PREFIX . "product_to_store",$input_array4);
			}
		}
		else
		{
			$input_array4=array(
                            'product_id'=>(int)$product_id,
                            'store_id'=> (int)0,
                            'quantity'=>(int)0,
                            'store_price'=>(float)$data['quantitystoreprice'.$store_id['store_id']]
                             );
                            $query2 = $this->db->query("insert",DB_PREFIX . "product_to_store",$input_array4);
		}

		if (isset($data['product_attribute'])) 
                {
			foreach ($data['product_attribute'] as $product_attribute) 
                        {
				if ($product_attribute['attribute_id']) 
                                {
					//$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");
                                        $where5=array('product_id'=>(int)$product_id,'attribute_id'=>(int)$product_attribute['attribute_id']);
                       
                                        $query5 = $this->db->query("delete",DB_PREFIX . "product_attribute",$where5);
					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) 
                                        {
                                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "',attribute_id = '" . (int)$product_attribute['attribute_id'] . "',language_id = '" . (int)$language_id . "',text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
                                                $input_array4=array(
                                                        'product_id'=>(int)$product_id,
                                                        'attribute_id'=>(int)$product_attribute['attribute_id'],
                                                        'language_id'=>(int)$language_id,
                                                        'text'=>$this->db->escape($product_attribute_description['text'])
                                                        );
                                                $query2 = $this->db->query("insert",DB_PREFIX . "product_attribute",$input_array4);
					}
				}
			}
		}

		if (isset($data['product_option'])) 
                {
			foreach ($data['product_option'] as $product_option) 
                        {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') 
                                {
					if (isset($product_option['product_option_value'])) 
                                        {
                                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");
                                            //$product_option_id = $this->db->getLastId();
                                                $product_option_id=$this->db->getNextSequenceValue('oc_product_option');
						
                                                $input_array5=array(
                                                        'product_option_id'=>(int)$product_option_id,
                                                        'product_id'=>(int)$product_id,
                                                        'option_id'=>(int)$product_option['option_id'],
                                                        'required'=>(int)$product_option['required']
                                                        );
                                                $query5 = $this->db->query("insert",DB_PREFIX . "product_option",$input_array5);
						
						foreach ($product_option['product_option_value'] as $product_option_value) 
                                                {
                                                    //$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "',option_id = '" . (int)$product_option['option_id'] . "',option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "',subtract = '" . (int)$product_option_value['subtract'] . "',price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "',weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
                                                    $input_array6=array(
                                                        'product_option_id'=>(int)$product_option_id,
                                                        'product_id'=>(int)$product_id,
                                                        'option_id'=>(int)$product_option['option_id'],
                                                        'option_value_id'=>(int)$product_option_value['option_value_id'],
                                                        'quantity'=>(int)$product_option_value['quantity'],
                                                        'subtract'=>(int)$product_option_value['subtract'],
                                                        'price'=>(float)$product_option_value['price'],
                                                        'price_prefix'=>$this->db->escape($product_option_value['price_prefix']),
                                                        'points'=>(int)$product_option_value['points'],
                                                        'points_prefix'=>$this->db->escape($product_option_value['points_prefix']),
                                                        'weight'=>(float)$product_option_value['weight'],
                                                        'weight_prefix'=>$this->db->escape($product_option_value['weight_prefix'])
                                                        );
                                                    $query6 = $this->db->query("insert",DB_PREFIX . "product_option_value",$input_array6);
						
						}
					}
				} 
                                else 
                                {
                                    //$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "',required = '" . (int)$product_option['required'] . "'");
                                    $input_array6=array(
                                                        
                                                        'product_id'=>(int)$product_id,
                                                        'option_id'=>(int)$product_option['option_id'],
                                                        'value'=>$this->db->escape($product_option['value']),
                                                        'required'=>(int)$product_option['required']
                                                        );
                                    $query6 = $this->db->query("insert",DB_PREFIX . "product_option",$input_array6);
						
				}
			}
		}

		if (isset($data['product_discount'])) 
                {
			foreach ($data['product_discount'] as $product_discount) 
                        {
                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$product_discount['store_id'] . "',customer_group_id = '" . (int)$product_discount['customer_group_id'] . "',quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "',date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
                                                $input_array7=array(
                                                        
                                                        'product_id'=>(int)$product_id,
                                                        'store_id'=>(int)$product_discount['store_id'],
                                                        'customer_group_id'=>(int)$product_discount['customer_group_id'],
                                                        'quantity'=>(int)$product_discount['quantity'],
                                                        'priority'=>(int)$product_discount['priority'],
                                                        'price'=>(float)$product_discount['price'],
                                                        'date_start'=>new MongoDate(strtotime($product_discount['date_start'])),
                                                        'date_end'=>new MongoDate(strtotime($product_discount['date_end']))
                                                        );
                                    $query7 = $this->db->query("insert",DB_PREFIX . "product_discount",$input_array7);
				
			}
		}

		if (isset($data['product_special'])) 
                {
			foreach ($data['product_special'] as $product_special) 
                        {
                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "',priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
                            $input_array8=array(
                                                'product_id'=>(int)$product_id,
                                                'customer_group_id'=>(int)$product_special['customer_group_id'],
                                                'priority'=>(int)$product_special['priority'],
                                                'price'=>(float)$product_special['price'],
                                                'date_start'=>new MongoDate(strtotime($product_special['date_start'])),
                                                'date_end'=>new MongoDate(strtotime($product_special['date_end']))
                                                        );
                            $query8 = $this->db->query("insert",DB_PREFIX . "product_special",$input_array8);
			}
		}

		if (isset($data['product_image'])) 
                {
			foreach ($data['product_image'] as $product_image) 
                        {
                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
                            
                            $input_array9=array(
                                                'product_id'=>(int)$product_id,
                                                'image'=>$this->db->escape($product_image['image']) ,
                                                'sort_order'=>(int)$product_image['sort_order']
                                    );
                            $query9 = $this->db->query("insert",DB_PREFIX . "product_image",$input_array9);
			}
		}

		if (isset($data['product_download'])) 
                {
			foreach ($data['product_download'] as $download_id) 
                        {
                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
                            $input_array10=array(
                                                'product_id'=>(int)$product_id,
                                                'download_id'=>(int)$download_id
                                    );
                            $query10 = $this->db->query("insert",DB_PREFIX . "product_to_download",$input_array10);
			
			}
		}

		if (isset($data['product_category'])) 
                {
			foreach ($data['product_category'] as $category_id) 
                        {
                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
                            $input_array11=array(
                                                'product_id'=>(int)$product_id,
                                                'category_id'=>(int)$category_id
                            );
                            $query11 = $this->db->query("insert",DB_PREFIX . "product_to_category",$input_array11);
			
			}
		}

		if (isset($data['product_filter'])) 
                {
			foreach ($data['product_filter'] as $filter_id) 
                        {
                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
                            $input_array12=array(
                                                'product_id'=>(int)$product_id,
                                                'filter_id'=>(int)$filter_id
                                    );
                            $query12 = $this->db->query("insert",DB_PREFIX . "product_filter",$input_array12);
			
			}
		}

		if (isset($data['product_related'])) 
                {
			foreach ($data['product_related'] as $related_id) 
                        {
                            //$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
                            $where13=array('product_id'=>(int)$product_id,'related_id'=>(int)$related_id);
                            $query = $this->db->query("delete",DB_PREFIX . "product_related",$where13);
                            $input_array13=array(
                                                'product_id'=>(int)$product_id,
                                                'related_id'=>(int)$related_id
                                    );
                            $query13 = $this->db->query("insert",DB_PREFIX . "product_related",$input_array13);
			
			}
		}

		if (isset($data['product_reward'])) 
                {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) 
                        {
                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "',points = '" . (int)$product_reward['points'] . "'");
                            $input_array14=array(
                                                'product_id'=>(int)$product_id,
                                                'customer_group_id'=>(int)$customer_group_id,
                                                'points'=>(int)$product_reward['points']
                                    );
                            //$query14 = $this->db->query("insert",DB_PREFIX . "product_reward",$input_array14);
			
			}
		}

		if (isset($data['product_layout'])) 
                {
			foreach ($data['product_layout'] as $store_id => $layout_id) 
                        {
                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
                            $input_array15=array(
                                                'product_id'=>(int)$product_id,
                                                'store_id'=>(int)$store_id,
                                                'layout_id'=>(int)$layout_id
                                    );
                            $query15 = $this->db->query("insert",DB_PREFIX . "product_to_layout",$input_array15);
			
			}
		}

		$alias_id=$this->db->getNextSequenceValue('oc_url_alias');
		if ($data['keyword']) 
                {
                    //$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
                    $input_array16=array(
                                        'query'=>'product_id=' . (int)$product_id,
                                        'keyword'=>$this->db->escape($data['keyword']),
                                        'url_alias_id'=>(int)$alias_id
                                    );
                    
                    $query16 = $this->db->query("insert",DB_PREFIX . "url_alias",$input_array16);
                   // print_r(json_encode($input_array16));exit;
		}

		if (isset($data['product_recurrings'])) 
                {
			foreach ($data['product_recurrings'] as $recurring) 
                        {
                            //$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ",`recurring_id` = " . (int)$recurring['recurring_id']);
                            $input_array17=array(
                                        'product_id'=>(int)$product_id,
                                        'customer_group_id'=>(int)$recurring['customer_group_id'],
                                        'recurring_id'=>(int)$recurring['recurring_id']
                                    );
                            $query17 = $this->db->query("insert",DB_PREFIX . "product_recurring",$input_array17);
			}
		}

		$this->cache->delete('product');

		//$this->event->trigger('post.admin.product.add', $product_id);

		return $product_id;
	}

	public function editProduct($product_id, $data) 
	{
               // print_r($data);exit;
		///$this->event->trigger('pre.admin.product.edit', $data);
                $data['quantity']=0;
                //print_r($data['product_store']);
                $this->load->model('setting/store');
                $storeprd  = $this->model_setting_store->getStoresForProducts();
                
                foreach ( $storeprd   as $store_id) 
				{
                
                    if($data['quantitystore'.$store_id['store_id']]=='')
                    {
                        $data['quantitystore'.$store_id['store_id']]=0;
                    }
                    $data['quantity']+=$data['quantitystore'.$store_id['store_id']];
                    
                }
		$log=new Log("product-edit-".date('Y-m-d').".log");

		//$upd_q="UPDATE " . DB_PREFIX . "product SET HSTN='" . $this->db->escape($data['hstn']) . "',price_tax='".$this->db->escape($data["price_tax"])."',model = '" . $this->db->escape($data['model']) . "',sku = '" . $this->db->escape($data['sku']) . "',upc = '" . $this->db->escape($data['upc']) . "',ean = '" . $this->db->escape($data['ean']) . "',jan = '" . $this->db->escape($data['jan']) . "',isbn = '" . $this->db->escape($data['isbn']) . "',mpn = '" . $this->db->escape($data['mpn']) . "',location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "',manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "',weight = '" . (float)$data['weight'] . "',weight_class_id = '" . (int)$data['weight_class_id'] . "',length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "',sort_order = '" . (int)$data['sort_order'] . "',date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'";
                
                $input_array=array(
                    'HSTN'=>$this->db->escape($data["hstn"]),
                    'price_tax'=>doubleval($this->db->escape($data["price_tax"])),
                    'name' =>strtoupper($this->db->escape($data['product_description'][1]['name'])), 
                    'model'=>strtoupper($this->db->escape($data['model'])),
                    'sku'=>$this->db->escape($data['sku']),
                    'upc'=>$this->db->escape($data['upc']),
                    'ean'=>$this->db->escape($data['ean']),
                    'jan'=>$this->db->escape($data['jan']),
                    'isbn'=>$this->db->escape($data['isbn']),
                    'mpn'=>$this->db->escape($data['mpn']),
                    'location'=>$this->db->escape($data['location']),
                    'quantity'=>(int)$data['quantity'],
                    'minimum'=>(int)$data['minimum'],
                    'subtract'=>(int)$data['subtract'],
                    'stock_status_id'=>(int)$data['stock_status_id'],
                    'date_available'=>new MongoDate(strtotime($data['date_available'])),
                    'manufacturer_id'=>(int)$data['manufacturer_id'],
					'manufacturer'=>$data['manufacturer'],
                    'shipping'=>(int)$data['shipping'],
                    'price'=>(float)$data['price'],
                    'points' =>(int)$data['points'],
                    'weight'=>(float)$data['weight'],
                    'weight_class_id'=>(int)$data['weight_class_id'],
                    'length'=>(float)$data['length'], 
                    'width'=>(float)$data['width'], 
                    'height'=>(float)$data['height'], 
                    'length_class_id'=>(int)$data['length_class_id'], 
                    'status'=>boolval($data['status']), 
                    'tax_class_id'=>(int)($data['tax_class_id']),
                    'tax_class_name'=>$this->db->escape($data['tax_class_name']),
                    'sort_order'=>(int)$data['sort_order'],
                    'category_ids' => $data['product_category'],
                    'category_name' =>$data['product_category_name'],
                    'product_description'=>$data['product_description'],
                    'date_modified'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                        );
                $where=array('product_id'=>(int)$product_id);
                $query = $this->db->query("update",DB_PREFIX . "product",$where,$input_array);
                
		//$log->write($upd_q);
		$this->db->query($upd_q);

		if (isset($data['image'])) 
           {
			//$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
                        $input_array2=array(
                            'image'=>$this->db->escape($data['image'])
                        );
                        $query = $this->db->query("update",DB_PREFIX . "product",$where,$input_array2); 
		}

		//$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
                $where=array('product_id'=>(int)$product_id);
                $query = $this->db->query("delete",DB_PREFIX . "product_description",$where);
		foreach ($data['product_description'] as $language_id => $value) 
                {
                    //$p_ds_sql="INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "' on DUPLICATE KEY UPDATE product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "' ";
                    //$this->db->query($p_ds_sql);
                    
                    $input_array3=array(
                            'product_id'=>(int)$product_id,
                            'language_id'=> (int)$language_id,
                            'name'=>strtoupper($this->db->escape($value['name'])),
                            'description'=>$this->db->escape($value['description']),
                            'tag'=>$this->db->escape($value['tag']),
                            'meta_title'=>$this->db->escape($value['meta_title']),
                            'meta_description'=>$this->db->escape($value['meta_description']),
                            'meta_keyword'=>$this->db->escape($value['meta_keyword'])
                         );
                    
                   
                    $query2 = $this->db->query("insert",DB_PREFIX . "product_description",$input_array3);
		}
                //$query = $this->db->query("delete",DB_PREFIX . "product_to_store",$where);
                if (isset($data['product_store'])) 
                {
			foreach ($data['product_store'] as $store_id) 
                        {
                            $where1=array('store_id'=>(int)$store_id,'product_id'=>(int)$product_id);
                            $query_s = $this->db->query('select',DB_PREFIX . 'product_to_store','','','',$where1,'','','','',array());
                            if($query_s->num_rows)
                            {
                                ///////no need to do anything
                            }
                            else 
                            {
                                $input_array4=array(
                                'product_id'=>(int)$product_id,
                                'store_id'=> (int)$store_id,
                                'quantity'=>(int)$data['quantitystore'.$store_id],
                                'store_price'=>(float)$data['quantitystoreprice'.$store_id],
                                'MOD_DATE'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                                );
                            
                                $query2 = $this->db->query("insert",DB_PREFIX . "product_to_store",$input_array4);
                            
                            }
                        
                        }
		}
                
                $query = $this->db->query("delete",DB_PREFIX . "product_attribute",$where);
		
		if (!empty($data['product_attribute'])) 
                {
			foreach ($data['product_attribute'] as $product_attribute) 
                        {
                            
				if (1) //$product_attribute['attribute_id']
                                {
                                    $where22=array('product_id'=>(int)$product_id,'attribute_id'=>(int)$product_attribute['attribute_id']);
					//$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");
                                        $query = $this->db->query("delete",DB_PREFIX . "product_attribute",$where22);
					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) 
                                        {
                                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "',attribute_id = '" . (int)$product_attribute['attribute_id'] . "',language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
                                            $input_array51=array(
                                                        
                                                        'product_id'=>(int)$product_id,
                                                        'attribute_id'=>(int)$product_attribute['attribute_id'],
                                                        'language_id'=>(int)$language_id,
                                                        'text'=>$this->db->escape($product_attribute_description['text'])
                                                        );
                                                $query51 = $this->db->query("insert",DB_PREFIX . "product_attribute",$input_array51);
						
					}
				}
			}
		}
                $query = $this->db->query("delete",DB_PREFIX . "product_option",$where);
                $query = $this->db->query("delete",DB_PREFIX . "product_option_value",$where);
		if (isset($data['product_option'])) 
                {
			foreach ($data['product_option'] as $product_option) 
                        {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') 
                                {
					if (isset($product_option['product_option_value'])) 
                                        {
                                            $product_option_id=$this->db->getNextSequenceValue('oc_product_option');
						
                                                $input_array5=array(
                                                        'product_option_id'=>(int)$product_option_id,
                                                        'product_id'=>(int)$product_id,
                                                        'option_id'=>(int)$product_option['option_id'],
                                                        'required'=>(int)$product_option['required']
                                                        );
                                                $query5 = $this->db->query("insert",DB_PREFIX . "product_option",$input_array5);
						
						foreach ($product_option['product_option_value'] as $product_option_value) 
                                                {
                                                    $input_array6=array(
                                                        'product_option_id'=>(int)$product_option_id,
                                                        'product_id'=>(int)$product_id,
                                                        'option_id'=>(int)$product_option['option_id'],
                                                        'option_value_id'=>(int)$product_option_value['option_value_id'],
                                                        'quantity'=>(int)$product_option_value['quantity'],
                                                        'subtract'=>(int)$product_option_value['subtract'],
                                                        'price'=>(float)$product_option_value['price'],
                                                        'price_prefix'=>$this->db->escape($product_option_value['price_prefix']),
                                                        'points'=>(int)$product_option_value['points'],
                                                        'points_prefix'=>$this->db->escape($product_option_value['points_prefix']),
                                                        'weight'=>(float)$product_option_value['weight'],
                                                        'weight_prefix'=>$this->db->escape($product_option_value['weight_prefix'])
                                                        );
                                                    $query6 = $this->db->query("insert",DB_PREFIX . "product_option_value",$input_array6);
						
						}
					}
				} 
                                else 
                                {
                                    $input_array6=array(
                                                        
                                                        'product_id'=>(int)$product_id,
                                                        'option_id'=>(int)$product_option['option_id'],
                                                        'value'=>$this->db->escape($product_option['value']),
                                                        'required'=>(int)$product_option['required']
                                                        );
                                    $query6 = $this->db->query("insert",DB_PREFIX . "product_option",$input_array6);
					
				}
			}
		}
                $query = $this->db->query("delete",DB_PREFIX . "product_discount",$where);
		if (isset($data['product_discount'])) 
                {
			foreach ($data['product_discount'] as $product_discount) 
                        {
                            $input_array7=array(
                                                        'product_id'=>(int)$product_id,
                                                        'store_id'=>(int)$product_discount['store_id'],
                                                        'customer_group_id'=>(int)$product_discount['customer_group_id'],
                                                        'quantity'=>(int)$product_discount['quantity'],
                                                        'priority'=>(int)$product_discount['priority'],
                                                        'price'=>(float)$product_discount['price'],
                                                        'date_start'=>new MongoDate(strtotime($product_discount['date_start'])),
                                                        'date_end'=>new MongoDate(strtotime($product_discount['date_end']))
                                                        );
                                    $query7 = $this->db->query("insert",DB_PREFIX . "product_discount",$input_array7);
				
			}
		}
                $query = $this->db->query("delete",DB_PREFIX . "product_special",$where);
		if (isset($data['product_special'])) 
                {
			foreach ($data['product_special'] as $product_special) 
                        {
                            $input_array8=array(
                                                'product_id'=>(int)$product_id,
                                                'customer_group_id'=>(int)$product_special['customer_group_id'],
                                                'priority'=>(int)$product_special['priority'],
                                                'price'=>(float)$product_special['price'],
                                                'date_start'=>new MongoDate(strtotime($product_special['date_start'])),
                                                'date_end'=>new MongoDate(strtotime($product_special['date_end']))
                                                        );
                            $query8 = $this->db->query("insert",DB_PREFIX . "product_special",$input_array8);
			}
		}
                $query = $this->db->query("delete",DB_PREFIX . "product_image",$where);
		if (isset($data['product_image'])) 
                {
			foreach ($data['product_image'] as $product_image) 
                        {
                            $input_array9=array(
                                                'product_id'=>(int)$product_id,
                                                'image'=>$this->db->escape($product_image['image']) ,
                                                'sort_order'=>(int)$product_image['sort_order']
                                    );
                            $query9 = $this->db->query("insert",DB_PREFIX . "product_image",$input_array9);
			}
		}
                $query = $this->db->query("delete",DB_PREFIX . "product_to_download",$where);
		if (isset($data['product_download'])) 
                {
			foreach ($data['product_download'] as $download_id) 
                        {
                            $input_array10=array(
                                                'product_id'=>(int)$product_id,
                                                'download_id'=>(int)$download_id
                                    );
                            $query10 = $this->db->query("insert",DB_PREFIX . "product_to_download",$input_array10);
			
			}
		}
                $query = $this->db->query("delete",DB_PREFIX . "product_to_category",$where);
		if (isset($data['product_category'])) 
                {
			foreach ($data['product_category'] as $category_id) 
                        {
                            $input_array11=array(
                                                'product_id'=>(int)$product_id,
                                                'category_id'=>(int)$category_id
                                    );
                            $query11 = $this->db->query("insert",DB_PREFIX . "product_to_category",$input_array11);
			
			}
		}
                $query = $this->db->query("delete",DB_PREFIX . "product_filter",$where);
		if (isset($data['product_filter'])) 
                {
			foreach ($data['product_filter'] as $filter_id) 
                        {
                            $input_array12=array(
                                                'product_id'=>(int)$product_id,
                                                'filter_id'=>(int)$filter_id
                                    );
                            $query12 = $this->db->query("insert",DB_PREFIX . "product_filter",$input_array12);
			}
		}

		//$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
                $query = $this->db->query("delete",DB_PREFIX . "product_related",$where);
		if (isset($data['product_related'])) 
                {
			foreach ($data['product_related'] as $related_id) 
                        {
				//$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				//$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
                            $where13=array('product_id'=>(int)$product_id,'related_id'=>(int)$related_id);
                            $query = $this->db->query("delete",DB_PREFIX . "product_related",$where13);
                            $input_array13=array(
                                                'product_id'=>(int)$product_id,
                                                'related_id'=>(int)$related_id
                                    );
                            $query13 = $this->db->query("insert",DB_PREFIX . "product_related",$input_array13);
			
			}
		}

		//$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
               // $query = $this->db->query("delete",DB_PREFIX . "product_reward",$where);
		if (isset($data['product_reward'])) 
                {
			foreach ($data['product_reward'] as $customer_group_id => $value) 
                        {
                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
                            $input_array14=array(
                                                'product_id'=>(int)$product_id,
                                                'customer_group_id'=>(int)$customer_group_id,
                                                'points'=>(int)$product_reward['points']
                                    );
                            //$query14 = $this->db->query("insert",DB_PREFIX . "product_reward",$input_array14);
			}
		}

		//$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
                $query = $this->db->query("delete",DB_PREFIX . "product_to_layout",$where);
		if (isset($data['product_layout'])) 
                {
			foreach ($data['product_layout'] as $store_id => $layout_id) 
                        {
                            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
                            $input_array15=array(
                                                'product_id'=>(int)$product_id,
                                                'store_id'=>(int)$store_id,
                                                'layout_id'=>(int)$layout_id
                                    );
                            $query15 = $this->db->query("insert",DB_PREFIX . "product_to_layout",$input_array15);
			}
		}

		//$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");
                $where16=array('query'=>'product_id=' . (int)$product_id);
                $query = $this->db->query("delete",DB_PREFIX . "url_alias",$where16);
                $alias_id=$this->db->getNextSequenceValue('oc_url_alias');
		if ($data['keyword']) 
                {
                    //$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
                    $input_array16=array(
                                        'query'=>'product_id=' . (int)$product_id,
                                        'keyword'=>$this->db->escape($data['keyword']),
                                        'url_alias_id'=>(int)$alias_id
                                    );
                    
                    $query16 = $this->db->query("insert",DB_PREFIX . "url_alias",$input_array16);
                    
		}

		//$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int)$product_id);
                $query = $this->db->query("delete",DB_PREFIX . "product_recurring",$where);
		if (isset($data['product_recurrings'])) 
                {
			foreach ($data['product_recurrings'] as $recurring) 
                        {
                            //$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ", `recurring_id` = " . (int)$recurring['recurring_id']);
                            $input_array17=array(
                                        'product_id'=>(int)$product_id,
                                        'customer_group_id'=>(int)$recurring['customer_group_id'],
                                        'recurring_id'=>(int)$recurring['recurring_id']
                                    );
                            $query17 = $this->db->query("insert",DB_PREFIX . "product_recurring",$input_array17);
			}
		}

		$this->cache->delete('product');

		//$this->event->trigger('post.admin.product.edit', $product_id);
	}

	public function copyProduct($product_id) 
	{
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p "
                        . "LEFT JOIN " . DB_PREFIX . "product_description pd "
                        . "ON (p.product_id = pd.product_id) "
                        . "WHERE p.product_id = '" . (int)$product_id . "' "
                        . "AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
                
                $prd_data=$this->getProduct($product_id);
                
		if ($prd_data['product_id']) 
                {
			$data = array();

			$data = $query->row;

			$data['sku'] = '';
			$data['upc'] = '';
			$data['viewed'] = '0';
			$data['keyword'] = '';
			$data['status'] = '0';
                        $data['model'] = $prd_data['model'];
			$data = array_merge($data, array('product_attribute' => $this->getProductAttributes($product_id)));
			$data = array_merge($data, array('product_description' => $this->getProductDescriptions($product_id)));
			$data = array_merge($data, array('product_discount' => $this->getProductDiscounts($product_id)));
			$data = array_merge($data, array('product_filter' => $this->getProductFilters($product_id)));
			$data = array_merge($data, array('product_image' => $this->getProductImages($product_id)));
			$data = array_merge($data, array('product_option' => $this->getProductOptions($product_id)));
			$data = array_merge($data, array('product_related' => $this->getProductRelated($product_id)));
			$data = array_merge($data, array('product_reward' => $this->getProductRewards($product_id)));
			$data = array_merge($data, array('product_special' => $this->getProductSpecials($product_id)));
			$data = array_merge($data, array('product_category' => $this->getProductCategories($product_id)));
			$data = array_merge($data, array('product_download' => $this->getProductDownloads($product_id)));
			$data = array_merge($data, array('product_layout' => $this->getProductLayouts($product_id)));
			$data = array_merge($data, array('product_store' => $this->getProductStores($product_id)));
			$data = array_merge($data, array('product_recurrings' => $this->getRecurrings($product_id)));

			$this->addProduct($data);
		}
               
	}

	public function deleteProduct($product_id) 
	{
            
                $where=array('product_id'=>(int)$product_id);
                $query = $this->db->query("delete",DB_PREFIX . "product",$where); 
                $query = $this->db->query("delete",DB_PREFIX . "product_attribute",$where);
                $query = $this->db->query("delete",DB_PREFIX . "product_description",$where);
                $query = $this->db->query("delete",DB_PREFIX . "product_discount",$where);
                $query = $this->db->query("delete",DB_PREFIX . "product_filter",$where);
                $query = $this->db->query("delete",DB_PREFIX . "product_image",$where);
                $query = $this->db->query("delete",DB_PREFIX . "product_option",$where);
                $query = $this->db->query("delete",DB_PREFIX . "product_option_value",$where);
                $query = $this->db->query("delete",DB_PREFIX . "product_related",$where);
                
                $where2=array('related_id'=>(int)$product_id);
                $query = $this->db->query("delete",DB_PREFIX . "product_related",$where2);
                
                //$query = $this->db->query("delete",DB_PREFIX . "product_reward",$where);
                $query = $this->db->query("delete",DB_PREFIX . "product_special",$where);
                $query = $this->db->query("delete",DB_PREFIX . "product_to_category",$where);
                $query = $this->db->query("delete",DB_PREFIX . "product_to_download",$where);
                $query = $this->db->query("delete",DB_PREFIX . "product_to_layout",$where);
                $query = $this->db->query("delete",DB_PREFIX . "product_to_store",$where);
                $query = $this->db->query("delete",DB_PREFIX . "review",$where);
                $query = $this->db->query("delete",DB_PREFIX . "product_recurring",$where);
                $query = $this->db->query("delete",DB_PREFIX . "url_alias",$where);
                
	}
	public function getProductImage($product_id) 
	{
		//echo $product_id;
		$query = $this->db->query("select",DB_PREFIX . "product_image",'','','',array('product_id'=>(int)$product_id));
		return $query;
	}
	public function getProduct($product_id) 
	{
        
                $match=array('product_id'=>(int)$product_id);
            
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
					"tax_class_name"=>1,
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
					'promotion_start_date'=>1,
					'promotion_end_date'=>1,
                    "pd.language_id"=>1,
                    "pd.name"=>1,
                    "pd.description"=>1,
                    "pd.tag"=>1,
                    "pd.meta_title"=>1,
                    "pd.meta_description"=>1,
                    "pd.meta_keyword"=>1,
                    "pd.meta_hindi"=>1
                );
            $sort_array=array();
            $limit='';
            $start='';
			$query = $this->db->query("join",DB_PREFIX . "product",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array);
            //print_r($query->row);
            
            $colval='product_id=' . (int)$product_id ;
            $query2 = $this->db->query("select",DB_PREFIX . "url_alias",$colval,'query','','','',$limit,'',$start,$sort_array);
            
            $return_array=array(
                    "product_id"=> $query->row[0]['product_id'],
                    "model"=>$query->row[0]['model'],
                    "sku"=>$query->row[0]['sku'],
                    "upc"=>$query->row[0]['upc'],
                    "ean"=>$query->row[0]['ean'],
                    "jan"=>$query->row[0]['jan'],
                    "isbn"=>$query->row[0]['isbn'],
                    "mpn"=>$query->row[0]['mpn'],
                    "location"=>$query->row[0]['location'],
                    "quantity"=>$query->row[0]['quantity'],
                    "stock_status_id"=>$query->row[0]['stock_status_id'],
                    "image"=>$query->row[0]['image'],
                    "manufacturer_id"=>$query->row[0]['manufacturer_id'],
                    "shipping"=>$query->row[0]['shipping'],
                    "price"=>$query->row[0]['price'],
                    "points"=>$query->row[0]['points'],
                    "tax_class_id"=>$query->row[0]['tax_class_id'],
					"tax_class_name"=>$query->row[0]['tax_class_name'],
                    "date_available"=>date('Y-m-d',($query->row[0]['date_available']->sec)),
                    "weight"=>$query->row[0]['weight'],
                    "weight_class_id"=>$query->row[0]['weight_class_id'],
                    "length"=>$query->row[0]['length'],
                    "width"=>$query->row[0]['width'],
                    "height"=>$query->row[0]['height'],
                    "length_class_id"=>$query->row[0]['length_class_id'],
                    "subtract"=>$query->row[0]['subtract'],
                    "minimum"=>$query->row[0]['minimum'],
                    "sort_order"=>$query->row[0]['sort_order'],
                    "status"=>$query->row[0]['status'],
                    "viewed"=>$query->row[0]['viewed'],
                    "date_added"=>date('Y-m-d  H:i:s',($query->row[0]['date_added']->sec)),
                    "date_modified"=> date('Y-m-d  H:i:s',($query->row[0]['date_modified']->sec)),
                    "HSTN"=>$query->row[0]['HSTN'],
                    "price_tax"=> $query->row[0]['price_tax'],
                    "purchase_price"=>$query->row[0]['purchase_price'],
                    "wholesale_price"=> (float)$query->row[0]['wholesale_price'],
                    "language_id"=>$query->row[0]['pd'][0]['language_id'],
                    "name"=>$query->row[0]['pd'][0]['name'],
                    "description"=>$query->row[0]['pd'][0]['description'],
                    "tag"=>$query->row[0]['pd'][0]['tag'],
                    "meta_title"=>$query->row[0]['pd'][0]['meta_title'],
                    "meta_description"=>$query->row[0]['pd'][0]['meta_description'],
                    "meta_keyword"=>$query->row[0]['pd'][0]['meta_keyword'],
                    "meta_hindi"=>$query->row[0]['pd'][0]['meta_hindi'],
                    "keyword"=>$query2->row['keyword'],
					'promotion_start_date'=>$query->row[0]['promotion_start_date'],
					'promotion_end_date'=>$query->row[0]['promotion_end_date'],
                    );
            
            return $return_array;
	}

	public function getProducts($data = array()) 
	{
        $match=array();
		if (!empty($data['filter_name'])) 
        {
            $search_string=$this->db->escape($data['filter_name']);
            $match['name']=new MongoRegex("/.*$search_string/i");
                   
		}

		if (!empty($data['filter_model'])) 
        {
            $search_string=$this->db->escape($data['filter_model']);
            $match['model']= new MongoRegex("/.*$search_string/i");
                   
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) 
        {
            $search_string= floatval($data['filter_price']);
            $match['price']= array('$type'=>1,'$eq'=>$search_string);//new MongoRegex("/^$search_string.*$/si");
                    
		}

		if (isset($data['filter_category']) && !is_null($data['filter_category'])) 
        {
            $match['category_ids']=(string)$data['filter_category'];
                    
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) 
                {
                    $match['status']= boolval($data['filter_status']);
                    
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
                    "product_description"=>1,
                   
                );
            $sort_array=array('product_id'=>1);
            $query = $this->db->query("select",DB_PREFIX . "product",'','','',$match,'',$limit,'',$start,$sort_array);
            return $query;
	}

	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p "
                        . "LEFT JOIN " . DB_PREFIX . "product_description pd "
                        . "ON (p.product_id = pd.product_id) "
                        . "LEFT JOIN " . DB_PREFIX . "product_to_category p2c "
                        . "ON (p.product_id = p2c.product_id) "
                        . "WHERE p.status='1' "
                        . "and pd.language_id = '" . (int)$this->config->get('config_language_id') . "' "
                        . "AND p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");

		return $query->rows;
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

	public function getProductCategories($product_id) {
		$product_category_data = array();
             
            $query = $this->db->query("select",DB_PREFIX . "product_to_category",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            
            foreach ($query->rows as $result) 
            {
               $product_category_data[] = $result['category_id'];
            }
            return $product_category_data;
	}

	public function getProductFilters($product_id) {
		$product_filter_data = array();
               
            $query = $this->db->query("select",DB_PREFIX . "product_filter",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            
            foreach ($query->rows as $result) 
            {
               $product_filter_data[] = $result['filter_id'];
            }
            return $product_filter_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_data = array();

		//$product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' GROUP BY attribute_id");
                $product_attribute_query = $this->db->query("select",DB_PREFIX . "product_attribute",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            
		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();

			//$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");
                        $product_attribute_description_query = $this->db->query("select",DB_PREFIX . "product_attribute",(int)$product_id,'product_id','',array('attribute_id'=>(int)$product_attribute['attribute_id']),'',$limit,'',$start,$sort_array);
            
			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}

			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}

		return $product_attribute_data;
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");
                
		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int)$product_option['product_option_id'] . "'");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'points'                  => $product_option_value['points'],
					'points_prefix'           => $product_option_value['points_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}

	public function getProductImages($product_id) 
        {
            $query = $this->db->query("select",DB_PREFIX . "product_image",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            return $query->rows;
	}

	public function getProductDiscounts($product_id) 
        {
            $query = $this->db->query("select",DB_PREFIX . "product_discount",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            return $query->rows;
	}

	public function getProductSpecials($product_id) 
        {
            $query = $this->db->query("select",DB_PREFIX . "product_special",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            return $query->rows;
	}

	public function getProductRewards($product_id) {
		$product_reward_data = array();
                $query = $this->db->query("select",DB_PREFIX . "product_reward",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            
		foreach ($query->rows as $result) 
                {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $product_reward_data;
	}

	public function getProductStores($product_id) 
        {
            $product_store_data = array();
            $query = $this->db->query("select",DB_PREFIX . "product_to_store",(int)$product_id,'product_id','','','',$limit,'',$start,$sort_array);
            foreach ($query->rows as $result) 
            {
		$product_store_data[] = $result['store_id'];
            }
            return $product_store_data;
	}
        public function getProductStoresQuantity($product_id) 
        {
            $product_store_data = array();
            $query = $this->db->query("select",DB_PREFIX . "product_to_store",'','','',array('product_id'=>(int)$product_id),'',$limit,'',$start,$sort_array);
            foreach ($query->rows as $result) 
            {
                array_push($product_store_data, $result);
            }
            return $product_store_data;
	}
	public function getProductsUnverified($data = array()) 
	{
        
			$lookupwithunwind=array(
                        array('lookup'=>
                                array(
                'from' => 'oc_product',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
            ),
                                'unwind'=>'$pd'
                        ),
                        array('lookup'=>
                                array(
                'from' => 'oc_store',
                'localField' => 'store_id',
                'foreignField' => 'store_id',
                'as' => 'st'
            ),
                                'unwind'=>'$st'
                        )
                    );
					$lookup=array(
                        array(
                'from' => 'oc_product',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
            ),
             array(
                'from' => 'oc_store',
                'localField' => 'store_id',
                'foreignField' => 'store_id',
                'as' => 'st'
            )
                    );
					$unwind=array('$pd','$st');
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
		 $match=array();
                if (!empty($data['filter_name'])) 
                {
                    $search_string=$this->db->escape($data['filter_name']);
                    $match['pd.name']=new MongoRegex("/.*$search_string/i");
                   
		}

		if (!empty($data['filter_model'])) 
                {
                    $search_string=$this->db->escape($data['filter_model']);
                    $match['pd.model']= new MongoRegex("/.*$search_string/i");
                   
		}
		if (!empty($data['filter_store'])) 
                {
                   
                    $match['store_id']= (int)$data['filter_store'];
					$matchcount['store_id']= (int)$data['filter_store'];
					
                   
		}
            $match['pd.status']=false;
			$matchcount['pd.status']=false;
            $sort_array=array('pd.name'=>1);
			$group=array();
            $query = $this->db->query("join",DB_PREFIX . "product_to_store",'','',$match,'','',$limit,'',$start,$sort_array,$lookupwithunwind,$group);
			//print_r($query);exit;
            return $query;
	}
	public function getProductsUnverified2($data = array()) 
	{
        
			$lookupwithunwind=array(
                        array('lookup'=>
                                array(
                'from' => 'oc_product',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
            ),
                                'unwind'=>'$pd'
                        ),
                        array('lookup'=>
                                array(
                'from' => 'oc_store',
                'localField' => 'store_id',
                'foreignField' => 'store_id',
                'as' => 'st'
            ),
                                'unwind'=>'$st'
                        )
                    );
					$lookup=array(
                        array(
                'from' => 'oc_product',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'pd'
            ),
             array(
                'from' => 'oc_store',
                'localField' => 'store_id',
                'foreignField' => 'store_id',
                'as' => 'st'
            )
                    );
					$unwind=array('$pd','$st');
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
		 $match=array();
                if (!empty($data['filter_name'])) 
                {
                    $search_string=$this->db->escape($data['filter_name']);
                    $match['pd.name']=new MongoRegex("/.*$search_string/i");
                   
		}

		if (!empty($data['filter_model'])) 
                {
                    $search_string=$this->db->escape($data['filter_model']);
                    $match['pd.model']= new MongoRegex("/.*$search_string/i");
                   
		}
		if (!empty($data['filter_store'])) 
                {
                   
                    $match['store_id']= (int)$data['filter_store'];
					$matchcount['store_id']= (int)$data['filter_store'];
					
                   
		}
            $match['pd.status']=false;
			$matchcount['pd.status']=false;
            $sort_array=array('pd.product_id'=>1);
			$group=array();
            $query = $this->db->query("join",DB_PREFIX . "product_to_store",'','',$match,'','',$limit,'',$start,$sort_array,$lookupwithunwind,$group);
			//print_r($query);exit;
            return $query;
	}
	public function getProductsUnverified_count($data,$all_count) 
    {
        $match=array();
        
        
			$match['status']=false;
			return $query = $this->db->getcount(DB_PREFIX . "product",$match);
		
        
	}
        public function getProductStoresQuantityHtml($product_id) 
        {
		
            $product_store_data='';
            $lookup=array(
                'from' => 'oc_store',
                'localField' => 'store_id',
                'foreignField' => 'store_id',
                'as' => 'st'
            );
            $match['product_id']=(int)$product_id;
			$match['$or']=array(array('quantity'=>array('$gt'=>0)));//,array('mitra_quantity'=>array('$gt'=>0))
            $sort_array=array('st.name'=>1);
            $query = $this->db->query("join",DB_PREFIX . "product_to_store",$lookup,'$st',$match,'','',$limit,'',$start,$sort_array);
             
                foreach ($query->row as $result) 
                {   //print_r($result);exit;
                    if($result['st']['name']=='')
                    {
                        $storename=  $this->config->get('config_name');
                    }
                    else
                    {
                        $storename=$result['st']['name'];}
                        $product_store_data=$product_store_data.$storename."-".$result['quantity']."<br/>";                        
		}

		return $product_store_data;
	}
        
	public function getProductLayouts($product_id) 
        {
            $product_layout_data = array();
            $query = $this->db->query("select",DB_PREFIX . "product_to_layout",'','','',array('product_id'=>(int)$product_id),'',$limit,'',$start,$sort_array);
            foreach ($query->rows as $result) 
            {
		$product_layout_data[$result['store_id']] = $result['layout_id'];
            }
            return $product_layout_data;
	}

	public function getProductRelated($product_id) 
        {
            $product_related_data = array();
            $query = $this->db->query("select",DB_PREFIX . "product_related",'','','',array('product_id'=>(int)$product_id),'',$limit,'',$start,$sort_array);
            foreach ($query->rows as $result) 
            {
		$product_related_data[] = $result['related_id'];
            }
            return $product_related_data;
	}
        public function getCrop($product_id) 
        {
            $product_related_data = array();
            $query = $this->db->query("select",DB_PREFIX . "crop",'','','',array('sid'=>(int)$product_id),'',$limit,'',$start,$sort_array);
            return $query->row;
	}
	public function getTotalProductsByTaxClassId($tax_class_id) 
        {
            $groupbyarray=array(
                 "_id"=> array('$tax_class_id'), 
                "total"=> array('$sum'=> 1 ) 
            );
            $query = $this->db->query("gettotalcount",DB_PREFIX . "product",$groupbyarray,array('tax_class_id'=>(int)$tax_class_id),'','','',$limit,'',$start,$sort_array);
            return $query->row[0]['total'];
	}
	public function getTotalProductsByManufacturerId($manufacturer_id) 
        {
            $groupbyarray=array(
                 "_id"=> array('$manufacturer_id'), 
                "total"=> array('$sum'=> 1 ) 
                );
            $query = $this->db->query("gettotalcount",DB_PREFIX . "product",$groupbyarray,array('manufacturer_id'=>(int)$manufacturer_id),'','','',$limit,'',$start,$sort_array);
            return $query->row[0]['total'];
	}

	public function getTotalProductsByAttributeId($attribute_id) 
        {
            $groupbyarray=array(
                 "_id"=> array('$attribute_id'), 
                "total"=> array('$sum'=> 1 ) 
                );
            $query = $this->db->query("gettotalcount",DB_PREFIX . "product_attribute",$groupbyarray,array('attribute_id'=>(int)$attribute_id),'','','',$limit,'',$start,$sort_array);
            return $query->row[0]['total'];
	}

	public function getTotalProductsByOptionId($option_id) 
        {
            $groupbyarray=array(
                 "_id"=> array('$option_id'), 
                "total"=> array('$sum'=> 1 ) 
                );
            $query = $this->db->query("gettotalcount",DB_PREFIX . "product_option",$groupbyarray,array('option_id'=>(int)$option_id),'','','',$limit,'',$start,$sort_array);
            return $query->row[0]['total'];
	}

	public function getTotalProductsByProfileId($recurring_id) 
        {
            $groupbyarray=array(
                 "_id"=> array('$recurring_id'), 
                "total"=> array('$sum'=> 1 ) 
                );
                $query = $this->db->query("gettotalcount",DB_PREFIX . "product_recurring",$groupbyarray,array('recurring_id'=>(int)$recurring_id),'','','',$limit,'',$start,$sort_array);
            
		return $query->row[0]['total'];
	}
        public function getTotalProductsByLayoutId($layout_id) 
        {
            $groupbyarray=array(
                 "_id"=> array('$layout_id'), 
                "total"=> array('$sum'=> 1 ) 
                );
            $query = $this->db->query("gettotalcount",DB_PREFIX . "product_to_layout",$groupbyarray,array('layout_id'=>(int)$layout_id),'','','',$limit,'',$start,$sort_array);
            return $query->row[0]['total'];
	}
        public function getCrops($data = array()) 
        {
            if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$start = 0;
			}
                        else {
                                $start = (int)$data['start'];
                        }

			if ($data['limit'] < 1) {
				$limit = 20;
			}
                        else {
                                $limit = (int)$data['limit'];
                        }

			
		}
                 $query3 = $this->db->query('select',DB_PREFIX . 'crop','','','','','','','','',array());
                 return $query3->rows;
	}
        public function getProductsRelatedToCropdtl($data = array())
        {
            $query3 = $this->db->query('select',DB_PREFIX . 'crop_article',(int)$data["filter_crop_id"],'crop_id','','','','','','',array());
            return $query->rows;
        }

        public function getProductName($product_id) 
        {
            $query = $this->db->query('select',DB_PREFIX . 'product',(int)$product_id,'product_id','','','','','','',array());
            return $query->row;
	}
        public function getHsnCodes()
        {
            $query = $this->db->query('select',DB_PREFIX . 'product_hsn','','','','','','','','',array());
            return $query->rows;
        }

        public function getProductsWithTaxAndCategory($data = array()) 
        {
                $match=array('product_id'=>array('$gt'=>0));
                if (!empty($data['filter_name'])) 
                {
                    $search_string=$this->db->escape($data['filter_name']);
                    $match['name']=new MongoRegex("/.*$search_string/i");
                }

		if (!empty($data['filter_model'])) 
                {
                    $search_string=$this->db->escape($data['filter_model']);
                    $match['model']= new MongoRegex("/.*$search_string/i");
                }

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) 
                {
                    $search_string= floatval($data['filter_price']);
                    $match['price']= array('$type'=>1,'$eq'=>$search_string);//new MongoRegex("/^$search_string.*$/si");
                }

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) 
                {
                    $match['quantity']=(int)$data['filter_quantity'];
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) 
                {
                    $match['status']= boolval($data['filter_status']);
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
            $lookup=array();
            
            $sort_array=array('product_id'=>1);
            
            $query = $this->db->query("select",DB_PREFIX . "product",$lookup,'','',$match,'',$limit,'',$start,$sort_array);
            
            return $query->rows;
	}
        public function openretailerupdateqty($data)
        {
            $log=new Log("updatequantity-".date('Y-m-d').".log");
            $data1=array('quantity'=>(int)$data["quantity"]); 
            $log->write($data1);
        
            $query=$this->db->query('select','oc_product_to_store','','','',array('store_id'=>(int)$data["store_id"],'product_id'=>(int)$data["product_id"]));
            if(count($query->rows)>0)
            {
                $match=array('product_id'=>(int)$data["product_id"],'store_id'=>(int)$data["store_id"]);
                //$query2= $this->db->query('incmodify','oc_product_to_store',$match,$data1);
		$query2= $this->db->query('update','oc_product_to_store',array('product_id'=>(int)$data["product_id"],'store_id'=>(int)$data["store_id"]),$data1);
                $log->write($query2);
                return $query2->rows['nModified'];
            }
            else
            {
                $input_array=array(
                "product_id" => (int)$data["product_id"],
                "store_id" =>(int)$data["store_id"],
                "quantity" => (int)$data["quantity"],
                "store_price" =>0,
                "store_tax_type" =>'',
                "store_tax_amt" =>'',
                "MOD_DATE" => new MongoDate(strtotime(date('Y-m-d h:i:s')))
                );
            $query=$this->db->query('insert','oc_product_to_store',$input_array);
            return $query->rows['ok'];
            }
        }
        public function openretailerupdateprice($data)
        {
            $log=new Log("openretailer-price-".date('Y-m-d').".log");
            $log->write("in model ");
            $log->write($data);
            $lookupwithunwind=array(
                        array('lookup'=>
                                array(
                                'from' => 'oc_tax_rule',
                                'localField' => 'tax_class_id',
                                'foreignField' => 'tax_class_id',
                                'as' => 'otr'
                                ),
                                'unwind'=>'$otr'
                        ),
                        array('lookup'=>
                                array(
                                'from' => 'oc_tax_rate',
                                'localField' => 'otr.tax_rate_id',
                                'foreignField' => 'tax_rate_id',
                                'as' => 'ota'
                                ),
                                'unwind'=>'$ota'
                        )
                    );
                $columns=array(
                    "_id"=>1,
                    "tax_class_id"=>1,
                    "otr.tax_rate_id"=>1,
                    "rate"=>1,
                    "type"=>1,
                );
            $match=array('product_id'=>(int)$data['product_id']);
            $queryprice = $this->db->query("join",DB_PREFIX . "product",'','',$match,'','','','','','',$lookupwithunwind);
            $log->write('check price');
            $log->write($queryprice->row[0]);
            if( strtolower($queryprice->row[0]['ota']['type'])=='p')
            {
                $data["price"] =  (($data["price"])* 100)/(100+$queryprice->row[0]['ota']['rate'] ) ;
                $newtax = ($queryprice->row[0]['ota']['rate'] / 100) * $data["price"];
            }
            $data1=array(
                'store_price'=>$data["price"],
                'store_tax_amt'=>$newtax
            );
            $log->write($data1);
            //////check product is in table or not
            $query=$this->db->query('select','oc_product_to_store','','','',array('store_id'=>(int)$data["store_id"],'product_id'=>(int)$data["product_id"]));
            if(count($query->rows)>0)
            {
                $query2= $this->db->query('update','oc_product_to_store',array('product_id'=>(int)$data["product_id"],'store_id'=>(int)$data["store_id"]),$data1);
                return $query2->rows['nModified'];
            }
            else
            {
                $input_array=array(
                "product_id" => (int)$data["product_id"],
                "store_id" =>(int)$data["store_id"],
                "quantity" => 0,
                "store_price" =>$data["price"],
                "store_tax_type" =>'',
                "store_tax_amt" =>$newtax,
                "MOD_DATE" => new MongoDate(strtotime(date('Y-m-d h:i:s')))
                );
           
            $query=$this->db->query('insert','oc_product_to_store',$input_array);
            return $query->rows['ok']; 
            }
        }
	public function calculate($value, $tax_class_id, $calculate = true, $fixed_taxes = true) 
	{
		if ($tax_class_id == 0) return $value;
		if ($tax_class_id && $calculate) {
			$amount = $this->getTax($value, $tax_class_id, $fixed_taxes);
	
			return $amount;
			
		} else {
			return $value;
		}
	}
	
	public function getTax($value, $tax_class_id, $fixed_taxes = true) 
	{
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
	
	public function getTaxRates($tax_class_id) 
	{
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
            
            $match=array("tax_class_id"=>(int)$tax_class_id,
                "based"=>$based
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
                    "tr2.date_modified" =>1
                );
            $sort_array=array();
            $limit='';
            $start='';
           
            
            $tax_query = $this->db->query("join",DB_PREFIX . "tax_rule",$lookup,'',$match,'','',$limit,$columns,$start,$sort_array,$lookupwithunwind);
            //print_r($tax_query->row);
            foreach ($tax_query->row as $result) 
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
	
	public function getRates($value, $tax_class_id) 
	{
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
                'tr2cg.customer_group_id'=> (int)$customer_group_id ,
                'z2gz.country_id'=>(int)$this->store_address['country_id'],
                'z2gz.zone_id'=>(int)$this->store_address['zone_id'] 
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
                    "tr2.date_modified" =>1
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


}
