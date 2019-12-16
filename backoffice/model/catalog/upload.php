<?php
class ModelCatalogUpload extends Model 
{
	public function readExcel($data,$category_id) 
	{
		$log=new Log("UploadSalesExcel-".date('Y-m-d').".log");
		foreach($data as $key=>$val)
		{
			$value= array_filter($val);
			$log->write($value);
    
			$p_id=$this->db->getNextSequenceValue('oc_product');
     
			$input_array113=array(
                            'name'=>$value[2],
                            'description'=>$value[2],
                            'tag'=>$value[2],
                            'meta_title'=>$value[2],
                            'meta_description'=>$value[2],
                            'meta_keyword'=>$value[2]
                         );
						 
			$product_id=$this->db->getNextSequenceValue('oc_product');
                $input_array=array('product_id' =>(int)$product_id,
                        'HSTN' =>0,
                        'price_tax' => (float)2,
                        'name' =>$value[2], 
                        'model' =>$value[1], 
                        'sku' => $value[4], 
                        'upc' =>0, 
                        'ean' =>0, 
                        'jan' =>0, 
                        'isbn' =>0, 
                        'mpn' =>0, 
                        'location' =>'', 
                        'quantity' =>(int)0, 
                        'minimum' =>(int)0, 
                        'subtract' =>(int)0, 
                        'stock_status_id' =>(int)0, 
                        'date_available' =>new MongoDate(strtotime($data['date_available'])), 
                        'manufacturer_id' =>(int)0, 
                        'shipping' =>(int)0, 
                        'price' =>(float)1, 
                        'points' => (int)0,
                        'weight' =>(float)0, 
                        'weight_class_id' =>(int)0, 
                        'length' =>(float)0, 
                        'width' =>(float)0, 
                        'height' =>(float)0, 
                        'length_class_id' =>(int)0, 
                        'status' => boolval(1), 
                        'tax_class_id' =>(int)12, 
                        'tax_class_name'=>'NO-TAX',
                        'sort_order' =>(int)0,
                        'category_ids' => array($category_id),
                        'category_name' =>array($value[3]),
                        'product_description'=>array('1'=>$input_array113),
                        'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                    );
			$query2 = $this->db->query("insert",DB_PREFIX . "product",$input_array);
    
			$input_array3=array(
                            'product_id'=>(int)$product_id,
                            'language_id'=> (int)1,
                            'name'=>$value[2],
                            'description'=>$value[2],
                            'tag'=>$value[2],
                            'meta_title'=>$value[2],
                            'meta_description'=>$value[2],
                            'meta_keyword'=>$value[2]
                         );
                    
                   
                    $query2 = $this->db->query("insert",DB_PREFIX . "product_description",$input_array3);
                    
                    $input_array4=array(
                            'product_id'=>(int)$product_id,
                            'store_id'=> (int)0,
                            'quantity'=>(int)0,
                            'store_price'=>(float)2
                             );
                            $query222 = $this->db->query("insert",DB_PREFIX . "product_to_store",$input_array4);
			 $input_array11=array(
                                                'product_id'=>(int)$product_id,
                                                'category_id'=>(int)$category_id
                            );
                            $query11 = $this->db->query("insert",DB_PREFIX . "product_to_category",$input_array11);
		}
	}
	public function getCategories() 
	{
		$query= $this->db->query('select','oc_category_description');
		return $query->rows;
	}
}