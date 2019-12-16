<?php
class ModelPurchaseOrderPurchaseOrder extends Model {

        public function getUsers($data)
        {
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
			}
			$where=array();
			
			if(!empty($data['filter_name']))
			{
				$search_string=$data['filter_name'];
				$where['firstname']=new MongoRegex("/.*$search_string/i");
			}
                    
			$query = $this->db->query("select","oc_user",'','','',$where,'',(int)$data['limit'],'',(int)$data['start'],array('firstname'=>1));
			
			return $query->rows;
           
        }
	
        public function check_ware_house_quantity($ware_house,$product_id,$p_qnty)
        {
			$where=array('store_id'=>(int)$ware_house,'product_id'=>(int)$product_id);
			$query = $this->db->query("select","oc_product_to_store",'','','',$where,'',(int)1,'',(int)0);
			
			$store_quantity=$query->row['quantity']; 
			if($store_quantity<$p_qnty)
			{
                return '0';
			}
			else
			{
               return '1';
			}
           
        }
        public function check_ware_house_price($ware_house,$product_id,$p_price)
        {
			$where=array('product_id'=>(int)$product_id);
			$query = $this->db->query("select","oc_product",'','','',$where,'',(int)1,'',(int)0);
			
			$store_price=$query->row['wholesale_price']; 
			if($store_price>$p_price)
			{
                return '0';
			}
			else
			{
               return '1';
			}
           
        }
		public function get_to_supplier_data($supplier_id)
        {
			$where=array('delete_bit'=>0,'pre_mongified_id'=>(int)$supplier_id);
			$query = $this->db->query("select","oc_po_supplier",'','','',$where,'',(int)1,'',(int)0);
            $supplier_data='';
			$supplier_data=$store_data."---".$query->row[first_name]." ".$query->row[last_name]."---".$query->row[ADDRESS]."---".$query->row[telephone]."---".$query->row[email]."---".$query->row[pan]."---".$query->row[gst]."---".$query->row[wallet_balance];
			return $supplier_data;
           
        }
        public function get_to_store_data($store_id)
        {
           $store_data='';
           $query = $this->db->query('select','oc_setting','','','',array('store_id'=>(int)$store_id,'key'=>'config_name'));
           $store_data=$query->row['value']; 
		   
           $query2 = $this->db->query('select','oc_setting','','','',array('store_id'=>(int)$store_id,'key'=>'config_address'));
           $store_data=$store_data."---".$query2->row['value'];
           
           $query2 = $this->db->query('select','oc_setting','','','',array('store_id'=>(int)$store_id,'key'=>'config_telephone'));
           $store_data=$store_data."---".$query2->row['value']; 
           
           $query2 = $this->db->query('select','oc_setting','','','',array('store_id'=>(int)$store_id,'key'=>'config_email'));
           $store_data=$store_data."---".$query2->row['value'];
           
           $query2 = $this->db->query('select','oc_setting','','','',array('store_id'=>(int)$store_id,'key'=>'config_PAN_ID_number'));
           $store_data=$store_data."---".$query2->row['value'];
           
           $query2 = $this->db->query('select','oc_setting','','','',array('store_id'=>(int)$store_id,'key'=>'config_gstn'));
           $store_data=$store_data."---".$query2->row['value'];

           $query2 = $this->db->query('select','oc_setting','','','',array('store_id'=>(int)$store_id,'key'=>'config_MSMFID'));
           $store_data=$store_data."---".$query2->row['value'];
          //echo $store_data;
           return $store_data;
           
        }
	
	
	public function getList($data)
	{ 
		$log=new Log("supplier-purchase-".date('Y-m-d').".log");
		$log->write('in model');
		$log->write($data);
		$where=array();
		
            if(empty($data['store_id']))
			{ 
			$user_group_id=$this->user->getGroupId();
			if($user_group_id==1)
			{
				$user_store_id=0;
			}
			else
			{
				$user_store_id=$this->user->getStoreId();
				$where['store_id'] = (int)$user_store_id;
			}
			}
			else
			{
				$where['store_id'] = (int)$data['store_id'];
			} 
            
			
			$datedata=array();
			$sdate=$data['filter_date_start'];
			$edate=$data['filter_date_end'];
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
                $where['create_date']=$datedata;
            }
            if (!empty($data['filter_supplier']) ) 
            {
				$where['supplier_id']=(int)$data['filter_supplier'];
			}
			if (!empty($data['filter_store']) ) 
            {
				$where['store_id']=(int)$data['filter_store'];
			}
            if ($data['filter_status']!='') 
            {
				$where['status']=(int)$data['filter_status'];
			
            }
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
			}
			
			$log->write($where);
			$query = $this->db->query("select","oc_supplier_po_order",'','','',$where,'',(int)$data['limit'],'',(int)$data['start']);
			//print_r($query);
			return $query;
	}
	

        
        /////////////////////////////////////////////////
        public function check_po_invoice($order_id) {
            $sql="select po_invoice_n from oc_po_invoice where po_order_id='".$order_id."'  ";
            $query = $this->db->query($sql);
            $results = $query->row;
            if (!empty($results['po_invoice_n'])) {
             return $results['po_invoice_n'];
            }
            else
            {
                return 0;
            }
        }
        public function getProduct($product_id)
		{
			$query = $this->db->query("select","oc_product",'','','',array('product_id'=>(int)$product_id),'',(int)1,'',(int)0,array());
			return $query->row['name'];
		}
        public function getProducts($data) 
		{ 
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
			}
			$where=array();
			
			if(!empty($data['filter_name']))
			{
				$search_string=$data['filter_name'];
				$where['name']=new MongoRegex("/.*$search_string/i");
			}
                    
			$query = $this->db->query("select","oc_product",'','','',$where,'',(int)$data['limit'],'',(int)$data['start'],array('name'=>1));
			
			return $query->rows;
	}
    public function submit_purchase_order($data = array()) 
	{ 
		$log=new Log("supplier-purchase-".date('Y-m-d').".log");
		$log->write('in model');
		if(empty($data['user_id']))
		{
			$log->write('in if');
			$user_group_id=$this->user->getGroupId();
			$user_id=$this->user->getId();
			if($user_group_id==1)
			{
				$user_store_id=0;
			}
			else
			{
				$user_store_id=$this->user->getStoreId();
			}
		}
		else
		{
			$log->write('in else ');
			$user_group_id=$data['group_id'];
			$user_id=$data['user_id'];
			$user_store_id=$data['store_id'];
		}
		$total_amount=0;
		
		foreach($data['order_product'] as $prd)
		{
			$total_amount = $total_amount+(($prd['p_price']*$prd['quantity'])+($prd['p_tax']));
		}
		$log->write('after total_amount');
		$log->write($total_amount);
		$sid=$this->db->getNextSequenceValue('oc_supplier_po_order');
		$log->write($sid);
		$input_array=array(
		'sid'=>(int)$sid,
		'store_id'=>(int)$data['filter_store'],
		'store_name'=>$data['store_name'],
		'supplier_id'=>(int)$data['filter_supplier'],
		'supplier_name'=>$data['supplier_name'],
		'contact_person_name'=>$data['contactname'],
		'contact_person_mobile'=>$data['contactmobile'],
		'valid_date'=>new MongoDate(strtotime($data['filter_date'])),
		'inv_no'=>$data['inv_no'],
		'order_product'=>$data['order_product'], 
		'delivery_type'=>$data['delivery_type'],
		'amount'=>(float)$total_amount,
		'remarks'=>$data['rem'],
		'id_prefix'=>'ASPL/PO/',
		'create_date'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
		'status'=>(int)0,
		'user_group_id'=>(int)$user_group_id,
		
		'user_id'=>(int)$user_id,
		); 
        /*
		'product_id'=>(int)$data['product_id'],
		'product_name'=>$data['product_name'][0],
		'rate'=>(float)$data['p_price'],
		'Quantity'=>(int)$data['p_qnty'],
		'amount'=>(float)$data['p_amount'],
		'tax_type'=>$data['p_tax_type'],
		*/
		$log->write($input_array);
        $query = $this->db->query("insert","oc_supplier_po_order",$input_array);
        return $sid;
                     
        }
        

        public function getSuppliers()
        {
            $where=array('delete_bit'=>0);
		
        if (isset($data['start']) || isset($data['limit'])) 
		{
			if ($data['start'] < 0) 
			{
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) 
			{
				$data['limit'] = 200;
			}

		} 
 
		$query = $this->db->query("select","oc_po_supplier",'','','',$where,'',(int)$data['limit'],'',(int)$data['start']);
		
		return $query->rows; 
        }
		


}
?>