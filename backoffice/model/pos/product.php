<?php

class ModelPosproduct extends Model {
	
public function getCompany($data = array())
    {
        $query = $this->db->query('select',DB_PREFIX . "company",'');
        return $query->rows;
       
    }
    
    
    
    public function getTopCategories() {
		// get all categories
		//$query = $this->db->query("SELECT c.image, c.category_id, c.parent_id, cd.name,cd.meta_hindi FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON c.category_id = cd.category_id WHERE c.parent_id = 0 and cd.language_id = '". (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, LCASE(cd.name)");
		//return $query->rows;
            
              $lookup=array(
                'from' => 'oc_category_description',
                'localField' => 'category_id',
                'foreignField' => 'category_id',
                'as' => 'table2'
            );
            $match=array('status'=>true);
            
            $sort_array=array('category_id'=>1);
            $query = $this->db->query("join",DB_PREFIX . "category",$lookup,'$table2',$match,'','',$limit,'',$start,$sort_array);
            //print_r($query->total_rows);
            foreach($query->row as $row)
            {
                $return_array[]=array(
                            'category_id'=>$row['category_id'],
                          
                            'name'=>$row['table2']['name'],
                            'totalrows'=>$query->total_rows    
                        );
            }
              //print_r($return_array);exit;
            return $return_array;
            
	}
        
        
        
    public function addproduct($data)
    {
        $log=new Log("addproduct-".date('Y-m-d').".log");
        $last_id=$this->db->getNextSequenceValue('oc_product_temp');
        $fdata1=array(
                    'product_id' =>(int)$last_id,
                    'language_id'  =>'1',
                    'name' =>$data['productname'],
                    'description'  =>$data['productname'],
                    'tag'  =>$data['productname'],
                    'meta_title'  =>$data['productname'],
                    'meta_description'  =>$data['productname'],
                    'meta_keyword'  =>$data['productname'],
                    ' meta_hindi'    =>$data['productname']
                    );
        $fdata2=array(
                    'product_id' =>(int)$last_id,
                    'category_id'=>(int)$data['category_id']
                    );
        $fdata=array(
                     'product_id'=>(int)$last_id,
                    'user_id'=>(int)$this->user->getID(),
                    'HSTN' =>$data['hstncode'],
                    'model'=>$data['productname'],
                    'price'=>'0',
                    'tax_class_id'=>(int)$data['gsttype'],
                    'sku'=>$data['sku'],
                    'company_id'=>$data['company_id'],
                    'company_name'=>$data['company_name'],
                    'category_id'=>(int)$data['category_id'],
                    'upc'=>'0',
                    'ean'=>'0',
                    'jan'=>'0',
                    'image'=>$data['image'],
                    'isbn'=>'0',
                    'mpn'=>'0',
                    'quantity'=>'0',
                    'location'=>'0',
                    'status'=>'0',
                    'stock_status_id'=>'0',
                    'manufacturer_id'=>'0',
                    'weight'=>'0',
                    'weight_class_id'=>'0',
                    'length'=>'0',
                    'width'=>'0',
                    'height'=>'0',
                    'length_class_id'=>'0',
                    'subtract'=>'1',
                    'minimum'=>'1',
                    'sort_order'=>'0',
                    'viewed'=>'0',
                    'date_modified'=>new MongoDate(strtotime(date('Y-m-d'))),
                    'purchase_price'=>'0',
                    'wholesale_price'=>'0',
                    'shipping'=>'1',
                    'price_tax'=>'0',
                    'points'=>'0',
                    'date_added' =>new MongoDate(strtotime(date('Y-m-d'))),
                    'product_description'=>$fdata1,
                    'product_to_category'=>$fdata2
                );
            $this->db->query('insert','oc_product_temp', $fdata);
            $this->db->query('insert','oc_product_description_temp', $fdata1);
                   
            $this->db->query('insert','oc_product_to_category_temp', $fdata2);
            return $last_id;
	}
        
        
        
        public function getProductsRequest($data = array()) {
                 $lookup=array(
                    'from' => 'oc_user',
                    'localField' => 'user_id',
                    'foreignField' => 'user_id',
                    'as' => 'ou'
                 );
                 
                 
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
        
        $match=array('user_id'=>1);
                $columns=array('product_id'=>1,'image'=>1,'ou.username'=>1,'sku'=>1,'model'=>1,'status'=>1);
                $query =  $this->db->query("join",DB_PREFIX . "product_temp",$lookup,'$ou','','',$match,$limit,$columns,$start);
             
                 foreach($query->row as $row)
                {
                $return_array[]=array(
                 'product_id'=>$row['product_id'],
                 'image'=>$row['image'],
                 'username'=>$row['ou']['username'],
                 'sku'=>$row['sku'],
                 'HSTN'=>$row['HSTN'],
                    //'name'=>$row['name'],
                  'model'=>$row['model'],
                  'status'=>$row['status'],
                  'totalrows'=>$query->total_rows    
             );
            }
            return $return_array;
             
    }
        
}    
?>