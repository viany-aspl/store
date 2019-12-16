<?php

class ModelPurchasePurchaseOrder extends Model {

    public function get_ware_houses() 
    {
        $sql2 = "select name,store_id from oc_store where store_id in 
(select store_id from oc_setting  where oc_setting.`key`='config_storetype' and oc_setting.value in ('2','4') )";
        $query2 = $this->db->query($sql2);
        return $query2->rows;
    }

    public function getList($data) 
    {
        if (($data['filter_status'] == "0") || ($data['filter_status'] == "1")) 
        {
            $where['receive_bit']=(int)$data['filter_status'];// and oc_po_order.canceled_message==''
        }
        if ($data['filter_status'] == "3") 
        {
            $where['canceled_message']=array('$ne'=>'');
        }
        
        if (!empty($data['filter_date_start'])) 
	{
            $sdate=$this->db->escape($data['filter_date_start']);
        }
	if (!empty($data['filter_date_end'])) 
	{
            $edate=$this->db->escape($data['filter_date_end']);
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
        if(!empty($datedata))
	{
            $where['order_date']=$datedata;
        }
        if (!empty($data['filter_id'])) 
        {
            $where['id']=(int)$data['filter_id'];
        }
        if (!empty($data['filter_store'])) 
        {
            $where['store_id']=(int)$data['filter_store'];
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
        $sort=array("id"=>-1);
        $query = $this->db->query('select','oc_po_order','','','',$where,'',(int)$data['limit'],array(),(int)$data['start'],$sort);

        return $query;
    }

    ////////////////////
    public function submit_po_invoice($data = array()) {

        $sql = "select po_invoice_n from oc_po_invoice where po_order_id='" . $data['order_id'] . "'  ";
        $query = $this->db->query($sql);
        $results = $query->row;
        if (empty($results['po_invoice_n'])) {
            $sql2 = "select MAX(po_invoice_n) as po_invoice_n from oc_po_invoice where po_store_id='" . $data['store_id'] . "'  ";
            $query2 = $this->db->query($sql2);

            if ($query2->row['po_invoice_n']) {
                $invoice_no = $query2->row['po_invoice_n'] + 1;
            } else {
                $invoice_no = 1;
            }
            $sql3 = "insert into oc_po_invoice set po_store_id='" . $data['store_id'] . "',po_order_id='" . $data['order_id'] . "',po_ware_house='" . $data['ware_house'] . "',po_invoice_n='" . $invoice_no . "',po_invoice_prefix='ASPL/BB',order_total='" . $data['grand_total'] . "'  ";
            $query = $this->db->query($sql3);
            $insert_id = $this->db->getLastId();

            for ($a = 0; $a < count($data['product_id']); $a++) {
                $product_id = $data['product_id'][$a];
                $product_hsn = $data['product_hsn'][$a];
                $p_amount = $data['p_amount'][$a];
                $product_name = $data['product_name'][$a];
                $p_price = $data['p_price'][$a];
                $p_qnty = $data['p_qnty'][$a];
                $p_tax_rate = $data['p_tax_rate'][$a];
                $p_tax_type = $data['p_tax_type'][$a];
                $po_store_id = $data['store_id'];
                $sql4 = "insert into oc_po_invoice_product set invoice_t_s_id='" . $insert_id . "',"
                        . "invoice_n='" . $invoice_no . "',"
                        . "product_id='" . $product_id . "',"
                        . "product_hsn='" . $product_hsn . "',"
                        . "p_amount='" . $p_amount . "',"
                        . "product_name='" . $product_name . "',  "
                        . "p_qnty='" . $p_qnty . "',  "
                        . "p_tax_rate='" . $p_tax_rate . "',  "
                        . "p_tax_type='" . $p_tax_type . "',  "
                        . "po_store_id='" . $po_store_id . "',  "
                        . "p_price='" . $p_price . "',po_order_id='" . $data['order_id'] . "'";
                $query = $this->db->query($sql4);
            }
        }
        //print_r($data);
    }

    ///////////////////////////////////////////////////////
    public function view_order_details($order_id) 
    {
        $where=array('id'=>(int)$order_id);
        $query = $this->db->query('select','oc_po_order','','','',$where,'',(int)$data['limit'],array(),(int)$data['start'],$sort);
        
        return $query->row;
    }

    public function check_ware_house_quantity($ware_house, $product_id, $p_qnty) {
        $sql = "SELECT `quantity` from  oc_product_to_store where store_id = " . $ware_house . " and `product_id` ='" . $product_id . "'";
        $query = $this->db->query($sql);
        $store_quantity = $query->row['quantity'];
        if ($store_quantity < $p_qnty) {
            return '0';
        } else {
            return '1';
        }
    }

    /* -----------------------------insert receive order function starts here------------------- */

    public function insert_receive_order($received_order_info, $order_id) 
    {
        
        $log = new Log('po-' . date('Y-m-d') . '.log');
        $user_id = $received_order_info['order_by_user_id'];
        $this->load->model('user/user');
        $this->load->model('setting/store');
        $orderby=$this->model_user_user->getUser($received_order_info['order_by_user_id']);
              
        $store_mobile = $orderby['username'];
        $otp = rand(1000, 9999);
        
        $updatedata=array(
            'order_sup_send'=>new MongoDate(strtotime($received_order_info['order_receive_date'])),
            'receive_bit'=>(int)0,
            'status'=>(int)2,
            'pending_bit'=>(int)0,
            'approved_by'=>(int)$received_order_info['approved_by'],
            'pre_supplier_bit'=>(int)1,
            'driver_otp'=>(int)$otp,
            'driver_mobile'=>(int)$store_mobile,
            'po_product.received_products'=>(int)0,
            'po_product.supplier_quantity'=>(int)$received_order_info['supplier_quantity'],
            'po_receive_details.supplier_id'=>(int)$received_order_info['supplier_id'],
            'po_receive_details.supplier_quantity'=>(int)$received_order_info['supplier_quantity']
            );
        $this->db->query('update','oc_po_order',array('id'=>(int)$order_id),$updatedata);
         
        $log->write($updatedata);
        $updatedata2=array(
            'supplier_id'=>(int)$received_order_info['supplier_id'],
            'supplier_quantity'=>(int)$received_order_info['supplier_quantity']
                );
        $this->db->query('update','oc_po_receive_details',array('order_id'=>(int)$order_id),$updatedata2);
        
        $updatedata3=array('received_products'=>(int)0);
        $this->db->query('update','oc_po_product',array('order_id'=>(int)$order_id),$updatedata3);
        
        $this->load->library('trans');
        $trans = new trans($this->registry);

        $filter_data = array(
            'user_id' => $user_id,
            'order_id' => $order_id,
            'store_mobile' => $store_mobile,
            'otp' => $otp
        );

        $this->load->library('sms');
        $sms = new sms($this->registry);
        $sms->sendsms($store_mobile, "35", $filter_data);
        return $otp;
    }

    public function cancel_order($received_order_info, $order_id) 
    {
        $log = new Log('po-' . date('Y-m-d') . '.log');
        $user_id = $received_order_info['order_by_user_id'];
        $this->load->model('user/user');
        $this->load->model('setting/store');
        $orderby=$this->model_user_user->getUser($received_order_info['order_by_user_id']);
              
        $store_mobile = $orderby['username'];
        
        
        $updatedata=array(
            'cancled_date'=>new MongoDate(strtotime(date('Y-m-d h:i:s'))),
            'status'=>(int)3,
            'canceled_by'=>(int)$received_order_info['canceled_by'],
            'canceled_message'=>$received_order_info['reject_Message']
            );
        $this->db->query('update','oc_po_order',array('id'=>(int)$order_id),$updatedata);
         
        $log->write($updatedata);
        
    }
    

}

?>