<?php
class trans {
public function __construct($registry) {
                $this->config = $registry->get('config');
	  $this->db = $registry->get('db');
                
}
public function addproducttrans($store_id,$product_id,$quantity,$order_id,$cr_db,$trans_type,$billtype='')
{
			$log= new Log('product-trans-'.date('Y-m-d').'.log');	
			$query1 = $this->db->query("select","oc_product_to_store",'','','',array('store_id'=>(int)$store_id,'product_id' =>(int)$product_id));
			$log->write($query1);
			$current_quantity=$query1 ->row['quantity'];
			$current_mitra_quantity=$query1 ->row['mitra_quantity'];			
			$data=array(
                        'store_id'=>(int)$store_id,
                        'product_id' =>(int)$product_id,
                        'quantity'=>(int)$quantity,
                        'order_id'=>(int)$order_id,
                        'cr_db'=>$cr_db,
                        'trans_type'=>$trans_type,
						'current_quantity'=>(int)$current_quantity,
						'current_mitra_quantity'=>(int)$current_mitra_quantity,
						'billtype'=>$billtype,
						'trans_time'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))
                        );                        
                        $log->write($data);
			
			$query = $this->db->query("insert","oc_product_trans",$data);
			
			
}
public function addstoretrans($cash,$store,$user_id,$tr_type,$order_id,$trans_method,$total_amount,$remarks='')
{
			$log= new Log('store-trans-'.date('Y-m-d').'.log');                                                                                                
			
                                $data=array(
                                'amount' => (float) $cash,
                                'store_id' => (int) $store,
                                'user_id' => (int)$user_id,
                                'tr_type' =>$tr_type,
                               'order_id' =>(int)$order_id,
                                'payment_method' =>$trans_method,
                                'total_amount' =>(float)$total_amount,
                                'updated_credit' =>$updated_credit,
                               'updated_cash' =>$updated_cash,
                                'remarks'=>$remarks,
								'trans_time'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))
                                );
                        
			                       
                        $log->write($data);
			$query = $this->db->query("insert","oc_store_cash_trans",$data);
			
			
}
public function addattendencetrans($user_id,$location_lat,$location_long,$attendence_type)
{
			$log= new Log('attendance-trans-'.date('Y-m-d').'.log');
			$p_sql = " insert into oc_attendence_trans set user_id='".$user_id."',location_lat ='".$location_lat."',location_long='".$location_long."',attendence_type='".$attendence_type."' ";
			$log->write($p_sql);
			$query = $this->db->query($p_sql);
			
			
}

public function addwalletcredit($store_id,$transaction_type,$payment_type,$amount,$invoice_number,$remarks='')
{
			$log= new Log('wallet-trans-'.date('Y-m-d').'.log');
                        
                        
	          $up_credit_sql="select wallet_balance from oc_store where store_id='".$store_id."' limit 1 ";
	          $log->write($up_credit_sql);
                        $query=$this->db->query($up_credit_sql);
                        $old_balance=$query->row['wallet_balance'];
                        $new_balance=$old_balance+$amount;
                        
	          $p_sql = "update oc_store set wallet_balance =  wallet_balance+".$amount." where store_id =  ".$store_id;
	          $log->write($p_sql);
	          $query = $this->db->query($p_sql);
	

	          $p_sql = "insert into oc_partner_invoice_adjustment set amount =  ".$amount.", store_id =  ".$store_id.",transaction_type = '".$transaction_type."',payment_type='".$payment_type."',`invoice_number`='".$invoice_number."',`remarks`='".$remarks."',wallet_balance='".$new_balance."',cr_date='".date('Y-m-d')."' ";
	          $log->write($p_sql);
	          $query = $this->db->query($p_sql);	
			
}

public function addwalletdebit($store_id,$transaction_type,$payment_type,$amount,$invoice_number,$remarks='')
{
			$log= new Log('wallet-trans-'.date('Y-m-d').'.log');
                        
                        
	          $up_credit_sql="select wallet_balance from oc_store where store_id='".$store_id."' limit 1 ";
	          $log->write($up_credit_sql);
                        $query=$this->db->query($up_credit_sql);
                        $old_balance=$query->row['wallet_balance'];
                        $new_balance=$old_balance-$amount;
                        
	          $p_sql = "update oc_store set wallet_balance =  wallet_balance-".$amount." where store_id =  ".$store_id;
	          $log->write($p_sql);
	          $query = $this->db->query($p_sql);
	

	          $p_sql = "insert into oc_partner_invoice_adjustment set invoice_amount =  ".$amount.", store_id =  ".$store_id.",transaction_type = '".$transaction_type."',payment_type='".$payment_type."',`invoice_number`='".$invoice_number."',`remarks`='".$remarks."',wallet_balance='".$new_balance."',cr_date='".date('Y-m-d')."' ";
	          $log->write($p_sql);
	          $query = $this->db->query($p_sql);	
			
}

public function storewalletcredit($store_id,$amount,$remarks='')
{
 $log= new Log('wallet-trans-'.date('Y-m-d').'.log');


 $up_credit_sql="select wallet_balance from oc_store where store_id='".$store_id."' limit 1 ";
 $log->write($up_credit_sql);
 $query=$this->db->query($up_credit_sql);
 $old_balance=$query->row['wallet_balance'];
 $new_balance=$old_balance+$amount;

 $p_sql = "update oc_store set wallet_balance = wallet_balance+".$amount.",currentcredit=currentcredit+".$amount." where store_id = ".$store_id;
 $log->write($p_sql);
 $query = $this->db->query($p_sql);




}


}
