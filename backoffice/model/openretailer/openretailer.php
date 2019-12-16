<?php
class ModelOpenretailerOpenretailer extends Model 
{
	public function addproductunverified($data)
    {
				$log=new Log("addproductunverified-".date('Y-m-d').".log");
               $log->write($data);
				$input_array113=array(
                            
                        'name' =>strtoupper($data['productname']),
                        'description'  =>$data['productname'],
                        'tag'  =>$data['productname'],
                        'meta_title'  =>$data['productname'],
						'meta_description'  =>$data['productname'],
						'meta_keyword'  =>$data['productname'],
						'meta_hindi'    =>$data['productname']
                         );
				$last_id=$this->db->getNextSequenceValue('oc_product');
$log->write($last_id);
                $fdata=array(
                     
						'product_id'=>(int)$last_id,
						'user_id'=>(int)$data['PostedBy'],
						'HSTN' =>$data['hstncode'],
                        'price_tax' => (float)$data['product_full_price'],
                        'name' =>strtoupper($data['productname']), 
                        'model' =>strtoupper($data['productname']), 
                        'sku'=>$data['sku'],
                        'upc' =>0, 
                        'ean' =>0, 
                        'jan' =>0, 
                        'isbn' =>0, 
                        'mpn' =>0, 
                        'location' =>'', 
                        'quantity' =>(int)0, 
                        'minimum' =>(int)0, 
                        'subtract' =>(int)$data['subtract'], 
                        'stock_status_id' =>(int)0, 
                        'date_available' =>new MongoDate(strtotime(date('Y-m-d'))),
                        'manufacturer_id' =>(int)0, 
                        'shipping' =>(int)0, 
                        'price' =>(float)$data['product_base_price'], 
                        'points' => (int)0,
                        'weight' =>(float)0, 
                        'weight_class_id' =>(int)0, 
                        'length' =>(float)0, 
                        'width' =>(float)0, 
                        'height' =>(float)0, 
                        'length_class_id' =>(int)0, 
                        'status' => boolval($data['status']), 
                        'tax_class_id' =>(int)$data['gsttype'], 
                        'tax_class_name'=>$data['gsttypename'],
                        'sort_order' =>(int)0,
                        'category_ids' => array($data['category_id'],$data['p_category_id']),
						'category_name' =>array($value[3]),
						'company_id'=>$data['company_id'],
                        'company_name'=>$data['company_name'],
						'user_category_id'=>$data['user_category_id'],
						'user_category_name'=>$data['user_category_name'],
						'gst_change'=>(int)$data['gst_change'],
                        'product_description'=>array('1'=>$input_array113),
                        'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                );
                $this->db->query('insert','oc_product', $fdata);
                
                    $fdata1=array(
                        'product_id' =>(int)$last_id,
                        'language_id'  =>1,
                        'name' =>strtoupper($data['productname']),
                        'description'  =>$data['productname'],
                        'tag'  =>$data['productname'],
                        'meta_title'  =>$data['productname'],
                    'meta_description'  =>$data['productname'],
                    'meta_keyword'  =>$data['productname'],
                   ' meta_hindi'    =>$data['productname']
                    );
                    $this->db->query('insert','oc_product_description', $fdata1);
                    $fdata2=array(
                        'product_id' =>(int)$last_id,
                        'category_id'=>(int)$data['category_id']
                    );
                $this->db->query('insert','oc_product_to_category', $fdata2);
$fdata22=array(
                        'product_id' =>(int)$last_id,
                        'category_id'=>(int)$data['p_category_id']
                    );
                $this->db->query('insert','oc_product_to_category', $fdata22);
				$input_array4=array(
                            'product_id'=>(int)$last_id,
                            'store_id'=> (int)$data['store_id'],
			    'manage_inv'=>boolval($data['manage_inv']),
                            'quantity'=>(int)$data['ProductQuantity'],
                            'store_price'=>(float)$data['product_base_price'],
							'store_tax_type'=>$data['gsttypename'],
                            'store_tax_amt'=>(float)($data['product_full_price']-$data['product_base_price']),
							'MOD_DATE'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                             );
							 $log->write('input_array4');
				
$log->write($input_array4);
                $query222 = $this->db->query("insert",DB_PREFIX . "product_to_store",$input_array4);
				/*
				$input_array5=array(
                            'product_id'=>(int)$last_id,
                            'store_id'=> (int)0,
                            'quantity'=>(int)0,
                            'store_price'=>(float)$data['product_base_price'],
							'store_tax_type'=>$data['gsttypename'],
                            'store_tax_amt'=>(float)($data['product_full_price']-$data['product_base_price']),
							'MOD_DATE'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                             );
                $query220 = $this->db->query("insert",DB_PREFIX . "product_to_store",$input_array5);
				*/
				
                return $last_id;
	}
    public function addproduct($data)
    {
		$log=new Log("addproduct-".date('Y-m-d').".log");
        $input_array113=array(
                            
                        'name' =>strtoupper($data['productname']),
                        'description'  =>$data['productname'],
                        'tag'  =>$data['productname'],
                        'meta_title'  =>$data['productname'],
						'meta_description'  =>$data['productname'],
						'meta_keyword'  =>$data['productname'],
						'meta_hindi'    =>$data['productname']
                         );        
		$last_id=$this->db->getNextSequenceValue('oc_product_temp');
                $fdata=array(
                     'product_id'=>(int)$last_id,
						'user_id'=>(int)$data['PostedBy'],
						'HSTN' =>$data['hstncode'],
                        'price_tax' => (float)$data['product_full_price'],
                        'name' =>strtoupper($data['productname']), 
                        'model' =>strtoupper($data['productname']), 
                        'sku'=>$data['sku'],
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
                        'date_available' =>new MongoDate(strtotime(date('Y-m-d'))),
                        'manufacturer_id' =>(int)0, 
                        'shipping' =>(int)0, 
                        'price' =>(float)$data['product_base_price'], 
                        'points' => (int)0,
                        'weight' =>(float)0, 
                        'weight_class_id' =>(int)0, 
                        'length' =>(float)0, 
                        'width' =>(float)0, 
                        'height' =>(float)0, 
                        'length_class_id' =>(int)0, 
                        'status' => boolval(0), 
                        'tax_class_id' =>(int)$data['gsttype'], 
                        'tax_class_name'=>$data['gsttypename'],
                        'sort_order' =>(int)0,
                        'category_ids' => array($data['category_id']),
						'category_name' =>array($value[3]),
						'company_id'=>$data['company_id'],
                        'company_name'=>$data['company_name'],
						'user_category_id'=>$data['user_category_id'],
						'user_category_name'=>$data['user_category_name'],
						'gst_change'=>(int)$data['gst_change'],
                        'product_description'=>array('1'=>$input_array113),
                        'date_added'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                );
                $this->db->query('insert','oc_product_temp', $fdata);
                
                    $fdata1=array(
                        'product_id' =>(int)$last_id,
                        'language_id'  =>'1',
                        'name' =>strtoupper($data['productname']),
                        'description'  =>$data['productname'],
                        'tag'  =>$data['productname'],
                        'meta_title'  =>$data['productname'],
                    'meta_description'  =>$data['productname'],
                    'meta_keyword'  =>$data['productname'],
                   ' meta_hindi'    =>$data['productname']
                    );
                    $this->db->query('insert','oc_product_description_temp', $fdata1);
                    $fdata2=array(
                        'product_id' =>(int)$last_id,
                        'category_id'=>(int)$data['category_id']
                    );
                 $this->db->query('insert','oc_product_to_category_temp', $fdata2);
                 return $last_id;
	}
	
    public function updateimage($product_id,$file)
	{ 
           $udata=array(
              'image'=> $file,
           );
           $match=array(
             'product_id'=> (int)$product_id   
           );
           $query = $this->db->query('update','oc_product_temp',$match,$udata);
    }
    public function insert_image($product_id,$file)
	{
         
            $fdata=array(
             'product_id'=>(int)$product_id,
               'sort_order'=> 0,
                'image'=>$file
            );
             $this->db->query('insert','oc_product_image_temp',$fdata);
    }







    public function count_image($p_id)
	{
       $match=array('product_id'=>(int)$p_id);
       $groupbyarray=array(
                 "_id"=> '$product_id', 
                "count"=> array('$sum'=> 1 ) 
            );
        $query = $this->db->query('gettotalcount','oc_product_image_temp',$groupbyarray,$match);
        return $query->row[0]['count'];
	}
	public function count_ticket_image($transid)
	{
       $match=array('transid'=>(int)$transid);
       $query = $this->db->query('select','cc_incomingcall','','','',$match);
       return $query->row['images'];
	}
	////////////////
	public function update_ticket_image($transid,$file)
	{ 
		$match=array(
             'transid'=> (int)$transid   
           );
		$query23 = $this->db->query("select","cc_incomingcall",'','','',$match);
		
		$images=$query23->row['images'];
		
		$images[]=$file;
		$udata=array(
              'images'=> $images,
           );
           
           $query = $this->db->query('update','cc_incomingcall',$match,$udata);
    }
	
	public function getstoresetting($store_id,$key)
	{
       $match=array('store_id'=>(int)$store_id,'key'=>$key);
       
        $query = $this->db->query('select','oc_setting','','','',$match);
		//print_r(json_encode($query));
        return $query->row['value'];
	}
	public function addtofavouritedproduct($data)
    {
		$log=new Log("addtofavouritedproduct-".date('Y-m-d').".log");
        $log->write($data);
		$where=array(
                            'product_id'=>(int)$data['product_id'],
                            'store_id'=> (int)$data['store_id']
                             );
		$query = $this->db->query("select",DB_PREFIX . "product_to_store",'','','',$where);
		//echo $query->num_rows;
		if($query->num_rows>0)
		{
			$fdata2=array(
                        'product_id' =>(int)$data['product_id'],
                        'category_id'=>(int)$data['category_id']
                    );
			$this->db->query('insert','oc_product_to_category', $fdata2);
		
			$query222 = $this->db->query("update",DB_PREFIX . "product_to_store",$where,array('favourite'=>1));
			
			
		}
		else
		{
			$input_array4=array(
                            'product_id'=>(int)$data['product_id'],
                            'store_id'=> (int)$data['store_id'],
                            'quantity'=>(int)0,
                            'store_price'=>(float)0,
							'store_tax_type'=>'',
                            'store_tax_amt'=>'',
							'favourite'=>1,
							'MOD_DATE'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                             );
                $query222 = $this->db->query("insert",DB_PREFIX . "product_to_store",$input_array4);
		}
		
		$where23=array('product_id'=>(int)$data['product_id']);
		$query23 = $this->db->query("select",DB_PREFIX . "product",'','','',$where23);
		
		$category_ids=$query23->row['category_ids'];
		if(in_array("44", $category_ids))
		{
			
		}
		else
		{
			$category_name=$query23->row['category_name'];
			$category_name[]='My Shop';
			$category_ids[]='44';
			$query24 = $this->db->query("update",DB_PREFIX . "product",$where23,array('category_name'=>$category_name,'category_ids'=>$category_ids));
		}
		return $data['product_id'];
	}
	public function remove_favourite($data)
    {
		$log=new Log("addtofavouritedproduct-".date('Y-m-d').".log");
        $log->write($data);
		$fdata2=array(
                        'product_id' =>(int)$data['product_id'],
                        'category_id'=>(int)$data['category_id']
                    );
        $this->db->query('delete','oc_product_to_category', $fdata2);
		$where=array(
                            'product_id'=>(int)$data['product_id'],
                            'store_id'=> (int)$data['store_id']
                             );
        $query222 = $this->db->query("update",DB_PREFIX . "product_to_store",$where,array('favourite'=>0));
		
		
		$where23=array('product_id'=>(int)$data['product_id']);
		$query23 = $this->db->query("select",DB_PREFIX . "product",'','','',$where23);
		
		
		$category_ids=$query23->row['category_ids'];
		if(count($category_ids)>1)
		{
			$category_ids=array_diff($category_ids,array('44'));
			$category_name=$query23->row['category_name'];
			$category_name=array_diff($category_name,array('MyList'));
			$category_name=array_diff($category_name,array('My Shop'));
			$query24 = $this->db->query("update",DB_PREFIX . "product",$where23,array('category_name'=>$category_name,'category_ids'=>$category_ids));
		}
		return $data['product_id'];
	}
	public function addtobookmarkproduct($data)
    {
		$log=new Log("addtobookmarkproduct-".date('Y-m-d').".log");
        $log->write($data);
		$where=array(
                            'product_id'=>(int)$data['product_id'],
                            'store_id'=> (int)$data['store_id']
                             );
		$query = $this->db->query("select",DB_PREFIX . "product_to_store",'','','',$where);
		//echo $query->num_rows;
		if($query->num_rows>0)
		{
			
			$query222 = $this->db->query("update",DB_PREFIX . "product_to_store",$where,array('bookmark'=>1));
			
			
		}
		else
		{
			$input_array4=array(
                            'product_id'=>(int)$data['product_id'],
                            'store_id'=> (int)$data['store_id'],
                            'quantity'=>(int)0,
                            'store_price'=>(float)0,
							'store_tax_type'=>'',
                            'store_tax_amt'=>'',
							'bookmark'=>1,
							'MOD_DATE'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                             );
                $query222 = $this->db->query("insert",DB_PREFIX . "product_to_store",$input_array4);
		}
		
		return $data['product_id'];
	}
	public function remove_bookmark($data)
    {
		$log=new Log("addtobookmarkproduct-".date('Y-m-d').".log");
        $log->write($data);
		$fdata2=array(
                        'product_id' =>(int)$data['product_id'],
                        'category_id'=>(int)$data['category_id']
                    );
        $this->db->query('delete','oc_product_to_category', $fdata2);
		$where=array(
                            'product_id'=>(int)$data['product_id'],
                            'store_id'=> (int)$data['store_id']
                             );
        $query222 = $this->db->query("update",DB_PREFIX . "product_to_store",$where,array('bookmark'=>0));
		
		return $data['product_id'];
	}
	public function addproductrating($data)
    { 
		$log=new Log("addproductrating-".date('Y-m-d').".log");
        $log->write($data);
		$where=array(
                            'product_id'=>(int)$data['product_id'],
                            'store_id'=> (int)$data['store_id']
                             );
		$query = $this->db->query("select",DB_PREFIX . "product_rating",'','','',$where);
		if($query->num_rows>0)
		{
			$query222 = $this->db->query("update",DB_PREFIX . "product_rating",$where,array('rating'=>(float)$data['rating'],'MOD_DATE'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))));
		}
		else
		{
			$input_array4=array(
                            'product_id'=>(int)$data['product_id'],
                            'store_id'=> (int)$data['store_id'],
							'rating'=>(float)$data['rating'],
							'MOD_DATE'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                             );
                $query222 = $this->db->query("insert",DB_PREFIX . "product_rating",$input_array4);
		}
		
		return $data['product_id'];
	}
	public function product_request($data)
    { 
		$log=new Log("product_request-".date('Y-m-d').".log");
		$last_id=$this->db->getNextSequenceValue('oc_product_request');
        $log->write($data);
		$input_array4=array(
							'req_id'=>(int)$last_id,
                            'product_id'=>(int)$data['product_id'],
							'product_name'=> $data['product_name'],
                            'store_id'=> (int)$data['store_id'],
							'store_name'=> $data['store_name'],
							'full_name'=>$data['full_name'],
							'mobile_number'=>$data['mobile_number'],
							'REQ_DATE'=>new MongoDate(strtotime(date('Y-m-d h:i:s')))
                             );
        $query222 = $this->db->query("insert",DB_PREFIX . "product_request",$input_array4);
		
		
		return $data['product_id'];
	}
	public function product_request_duplicate_check($data)
    { 
		$log=new Log("product_request-".date('Y-m-d').".log");
		
        $where=array('product_id'=>(int)$data['product_id'],'store_id'=> (int)$data['store_id'],'mobile_number'=>$data['mobile_number']);
		$where['REQ_DATE']=array('$gte'=>new MongoDate(strtotime(date('Y-m-d', strtotime('0 day', strtotime(date('Y-m-d')))) )));
		//print_r($where);
        $query = $this->db->query("select",DB_PREFIX . "product_request",'','','',$where);
		
		return $query;
	}
}

?>
