<?php
class ModelInventoryPurchaseOrder extends Model 
{
    public function check_driver_otp($order_id)
    {
		$log=new Log("receive-product-".date('Y-m-d').".log");
        $log->write($order_id);
		$query = $this->db->query('select','oc_po_order','','','',array('id'=>(int)$order_id));
		$log->write($query->row['driver_otp']);
		return $query->row['driver_otp']; 
    }
	
    public function check_quantity($order_id)
    {
        $log=new Log("receive-product-".date('Y-m-d').".log");
        $log->write($order_id);
		$query = $this->db->query('select','oc_po_order','','','',array('id'=>(int)$order_id));
		$log->write($query->row['po_product']['supplier_quantity']);
		return $query->row['po_product']['supplier_quantity']; 
    }
	public function getOrderDetails($data)
    {
        $log=new Log("inv-".date('Y-m-d').".log");
        $log->write($data);
		$query = $this->db->query('select','oc_po_order','','','',array('id'=>(int)$data['id']));
		
		return $query->row; 
    }
    public function view_order_details($order_id)
    {
        $log=new Log("receive-product-".date('Y-m-d').".log");
        $log->write($order_id);
		$query = $this->db->query('select','oc_po_order','','','',array('id'=>(int)$order_id));
		$log->write($query->row['po_product']);
		return $query->row['po_product']; 
    }
	
    public function insert_purchase_order($data = array())
    {
        $log=new Log("po-request-".date('Y-m-d').".log");
        //insert order details
        $log->write($data);
		$ret_id='';
        for($i = 0; $i<count($data['products']); $i++)
        {
            $log->write($i);
            if($data['supplier_id'] != "--Supplier--")
            {
                $order_id=$this->db->getNextSequenceValue('oc_po_order');
                $insert_array1=array(
                    'id'=>(int)$order_id,
                    'order_date'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
                    'user_id'=>(int)$this->session->data['user_id'],
                    'pre_supplier_bit'=>(int)1,
                    'store_id'=>(int)$data['stores'][$i][0],
                    'store_type'=>$data['store_type'],
                    'po_product'=>array(),
                    'po_receive_details'=>array(),
                    'delete_bit'=>(int)0,
                    'receive_bit'=>(int)0,
                    'pending_bit'=>(int)1,
                    'pre_supplier_bit'=>(int)1,
                    'canceled_by'=>(int)0,
                    'approved_by'=>(int)0,
                    'status'=>(int)0,
                    'driver_otp'=>'',
                    'driver_mobile'=>'',
                    'canceled_message'=>'',
                    'receive_date'=>'',
                    'order_sup_send'=>'',
                    'potential_date'=>new MongoDate(strtotime($data['potentialdate'])));
            
                //$sql1='INSERT INTO oc_po_order (order_date,user_id,pre_supplier_bit,store_id,store_type,potential_date) VALUES("' . date('Y-m-d') . '",'.$this->session->data['user_id'].',1,'.$data['stores'][$i][0].',"'.$data['store_type'].'","'.$data['potentialdate'].'")';
                $log->write($insert_array1);
                $this->db->query('insert','oc_po_order',$insert_array1);
            }
            else
            {
				$order_id=$this->db->getNextSequenceValue('oc_po_order');
                $insert_array1=array(
                    'id'=>(int)$order_id,
                    'order_date'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
                    'user_id'=>(int)$this->session->data['user_id'],
                    'pre_supplier_bit'=>(int)1,
                    'store_id'=>(int)$data['stores'][$i][0],
                    'store_type'=>$data['store_type'],
                    'po_product'=>array(),
                    'po_receive_details'=>array(),
					'delete_bit'=>(int)0,
                    'receive_bit'=>(int)0,
					'pending_bit'=>(int)1,
					'pre_supplier_bit'=>(int)1,
					'canceled_by'=>(int)0,
                    'approved_by'=>(int)0,
					'status'=>(int)0,
					'driver_otp'=>'',
                    'driver_mobile'=>'',
                    'canceled_message'=>'',
					'receive_date'=>'',
                    'order_sup_send'=>'',
                    'potential_date'=>new MongoDate(strtotime($data['potentialdate'])));
            
                //$sql2='INSERT INTO oc_po_order (order_date,user_id,store_id,store_type,potential_date) VALUES("' . date('Y-m-d') . '",'.$this->session->data['user_id'].','.$data['stores'][$i][0].',"'.$data['store_type'].'","'.$data['potentialdate'].'")';
                $log->write($insert_array1);
				$this->db->query('insert','oc_po_order',$insert_array1);
            
            }
            if($ret_id=="")
            {
				$ret_id=$order_id;
            }
            else
            {
				$ret_id=$ret_id.",".$order_id;
            }
			$prddata=$this->db->query('select','oc_product','','','',array('product_id'=>(int)$data['products'][$i][0]));
			$prrd=$prddata->row;
			$prd_sku=$prrd['sku'];
            //insert product details
            $sid=$this->db->getNextSequenceValue('oc_po_product');
            $insert_prd_array=array(
                'sid'=>(int)$sid,
                'product_id'=>(int)$data['products'][$i][0],
                'name'=>$data['products'][$i][1],
				'sku'=>$prd_sku,
                'quantity'=>(int)$data['quantity'][$i],
                'order_id'=>(int)$order_id,
                'store_id'=>(int)$data['stores'][$i][0],
                'store_name'=>$data['stores'][$i][1]);
            $this->db->query('insert','oc_po_product',$insert_prd_array);
            $log->write($insert_prd_array);
            
            $product_ids[$i] = $sid;
            if($data['supplier_id'] != "--Supplier--")
            {
                $insert_rec_array=array(
                'quantity'=>(int)$data['quantity'][$i],
                'supplier_quantity'=>(int)$data['quantity'][$i],
                'product_id'=>(int)$data['products'][$i][0],
                'name'=>$data['products'][$i][1],
				'sku'=>$prd_sku,
                'supplier_id'=>(int)$data['supplier_id'],
                'order_id'=>(int)$order_id,
                'store_id'=>(int)$data['stores'][$i][0],
                'store_name'=>$data['stores'][$i][1]);
                $query = $this->db->query("insert","oc_po_receive_details",$insert_rec_array);
            }
            else
            {
                $insert_rec_array=array(
                'quantity'=>(int)0,
                'supplier_quantity'=>(int)0,
                'product_id'=>(int)$data['products'][$i][0],
                'name'=>$data['products'][$i][1],
                'supplier_id'=>(int)-1,
                'order_id'=>(int)$order_id,
                'store_id'=>(int)$data['stores'][$i][0],
                'store_name'=>$data['stores'][$i][1]);
                $query = $this->db->query("insert","oc_po_receive_details",$insert_rec_array);
            }
            $updatedata=array(
                    'po_product'=>$insert_prd_array,
                    'po_receive_details'=>$insert_rec_array
                    );
            $this->db->query('update','oc_po_order',array('id'=>(int)$order_id),$updatedata);
            
        }
        return $ret_id;
    }
	public function get_prd_data($product_id)
	{
		$prddata=$this->db->query('select','oc_product','','','',array('product_id'=>(int)$product_id));
		return $prddata->row;
		
	}
	
    public function getListRecStore($data=array())
    {
        $log=new Log("inv-list-".date('Y-m-d').".log");
        $where=array('store_id'=>(int)$data['store_id']);//'status'=>(int)2,
		$where['$or']=array(array('status'=>(int)2),array('status'=>(int)0),array('status'=>(int)4));
		$query=$this->db->query("SELECT","oc_po_order","","","",$where);
        //$log->write($query->rows);
		$log->write($where);
		$log->write(json_encode($where));
		return $query->rows;
    }
    public function delete($ids) 
    {
        $deleted = false;
        foreach ($ids as $id) 
        {
            if ($this->db->query("UPDATE oc_po_order SET delete_bit = " . 0 . " WHERE id = " . $id))
            {
                $deleted = true;
            }
        }
        if ($deleted) 
        {
            return $deleted;
        } 
        else 
        {
            return false;
        }
    }

    /*-----------------------------insert receive order function starts here-------------------*/
    public function insert_receive_order($received_order_info,$order_id)
    {
        $log=new Log("receiveorder-".date('Y-m-d').".log");
        $log->write($received_order_info);
        $log->write($order_id);
        $poquery = $this->db->query("select","oc_po_order",'','','',array('id'=>(int)$order_id));
        $log->write($poquery);
        
		$mcrypt=new MCrypt();
		$st_query=$this->db->query('select','oc_setting','','','',array('key'=>'config_storetype','store_id'=>(int)$poquery->row['store_id']));
        $storetype=$st_query->row['value'];
        $log->write($storetype);
        
        $this->load->library('trans');
        $trans=new trans($this->registry);
        
        $a=0;
        $totalamount=0;
        foreach($received_order_info['received_product_ids'] as $prd_id)
        {
            $log->write('in loop');
            $log->write($prd_id);
            $prdquery = $this->db->query("select","oc_product_to_store",'','','',array('product_id'=>(int)$prd_id,'store_id'=>(int)$poquery->row['store_id']));
            $log->write($prdquery);
            
            $upd_data2=array('quantity'=>(int)$received_order_info['received_quantities'][$a],'price'=>$received_order_info['prices'][$a]);
            $this->db->query('update','oc_po_receive_details',array('product_id'=>(int)$prd_id,'order_id'=>(int)$order_id),$upd_data2);
            $log->write($upd_data2);
            
            $upd_data22=array('received_products'=>(int)$received_order_info['received_quantities'][$a]);
            $query1 = $this->db->query("update","oc_po_product",array('product_id'=>(int)$prd_id,'order_id'=>(int)$order_id),$upd_data22);
            $log->write($upd_data22);
            
            $upd_data3=array(
                'receive_bit'=>(int)1,
                'status'=>(int)4,
                'receive_date'=>new MongoDate(strtotime($received_order_info['order_receive_date'])),
                'po_receive_details.quantity'=>(int)$received_order_info['received_quantities'][$a],
                'po_product.received_products'=>(int)$received_order_info['received_quantities'][$a]);
            
            $this->db->query('update','oc_po_order',array('id'=>(int)$order_id),$upd_data3);
            $log->write($upd_data3);
            
            $data1=array('quantity'=>(int)$received_order_info['received_quantities'][$a]); 
            $match=array('product_id'=>(int)$prd_id);
            $query2= $this->db->query('incmodify','oc_product',$match,$data1);
            $log->write($data1);
            $log->write($match);
            
            if($prdquery->num_rows>0)
            {
                $log->write('in if');
                $data2=array('mitra_quantity'=>(int)$received_order_info['received_quantities'][$a]); 
                $match2=array('product_id'=>(int)$prd_id,'store_id'=>(int)$poquery->row['store_id']);
                $query2= $this->db->query('incmodify','oc_product_to_store',$match2,$data2);
            }
            else
            {
                $log->write('in else');
                $prd_insert_data=array(
                    'product_id'=>(int)$prd_id,
                    'store_id'=>(int)$poquery->row['store_id'],
                    'quantity'=>(int)0,
                    'store_price'=>(float)0,
                    'store_tax_amt'=>'',
                    'store_tax_type'=>'',
                    'MOD_DATE'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
                    'mitra_quantity'=>(int)$received_order_info['received_quantities'][$a]
                        );
                $query2= $this->db->query('insert','oc_product_to_store',$prd_insert_data);
                $log->write($prd_insert_data);
            }
            try
            {
                $trans->addproducttrans((int)$poquery->row['store_id'],(int)$prd_id,$received_order_info['received_quantities'][$a],$order_id,'CR','PO');      
            } 
            catch (Exception $ex) 
            {
                $log->write($ex->getMessage());
            }
            
            $a++;
        }
        return true;
    }
	
	/*-----------------------------insert receive order function ends here-----------------*/
}
?>