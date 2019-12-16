<?php
class ModelReportProductreconciliation extends Model {
	
	
   public function getOrdersReceived($data = array()) 
   {
       if($data["filter_name_id"]=="")
       {
         $filter_name_id="null";
       }
       else
       {
            $filter_name_id= $data["filter_name_id"];
       }
       if($data["filter_date_start"]=="")
       {
         $filter_date_start="null";
       }
       else
       {
            $filter_date_start="'".$data["filter_date_start"]."'";
       }
       if($data["filter_date_end"]=="")
       {
         $filter_date_end="null";
       }
       else
       {
            $filter_date_end="'".$data["filter_date_end"]."'";
       }

       $sql="  select ocs.name as store_name,podr.store_id, podr.product_id, pop.name as product_name, podr.quantity,poo.order_date,poo.order_sup_send as recive_date, 'Akshamaala' as store_transfer from oc_po_receive_details as podr left join oc_po_order as poo on podr.order_id = poo.id left join oc_store as ocs on ocs.store_id = podr.store_id left join oc_store as ocs1 on ocs1.store_id = podr.supplier_id left join oc_po_product as pop on pop.product_id = podr.product_id where poo.receive_bit=1 and poo.order_sup_send between ifnull(".$filter_date_start.",poo.order_sup_send) and ifnull(".$filter_date_end.",poo.order_sup_send) and podr.product_id=ifnull(".$filter_name_id.", podr.product_id) GROUP BY podr.order_id ORDER BY `poo`.`order_date` DESC  ";
        if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
                
		$query = $this->db->query($sql);
                            //echo $sql;
		return $query->rows;
   }

    public function getTotalOrdersReceived($data = array()) 
   {
       if($data["filter_name_id"]=="")
       {
         $filter_name_id="null";
       }
       else
       {
            $filter_name_id= $data["filter_name_id"];
       }
       if($data["filter_date_start"]=="")
       {
         $filter_date_start="null";
       }
       else
       {
            $filter_date_start="'".$data["filter_date_start"]."'";
       }
       if($data["filter_date_end"]=="")
       {
         $filter_date_end="null";
       }
       else
       {
            $filter_date_end="'".$data["filter_date_end"]."'";
       }

       $sql=" select count(*) as total,sum(quantity) as total_quantity from ( select ocs.name as store_name,podr.store_id, podr.product_id, pop.name as product_name, podr.quantity,poo.order_date,poo.order_sup_send as recive_date, 'Akshamaala' as store_transfer from oc_po_receive_details as podr left join oc_po_order as poo on podr.order_id = poo.id left join oc_store as ocs on ocs.store_id = podr.store_id left join oc_store as ocs1 on ocs1.store_id = podr.supplier_id left join oc_po_product as pop on pop.product_id = podr.product_id where poo.receive_bit=1 and poo.order_sup_send between ifnull(".$filter_date_start.",poo.order_sup_send) and ifnull(".$filter_date_end.",poo.order_sup_send) and podr.product_id=ifnull(".$filter_name_id.", podr.product_id) GROUP BY podr.order_id  ";
        

			$sql .= "   ) as aa";
		
                
		$query = $this->db->query($sql);
                //echo $sql;
		return $query->row;
   }


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
public function getOrders($data = array()) 
   {
       if($data["filter_name_id"]=="")
       {
         $filter_name_id="null";
       }
       else
       {
            $filter_name_id= $data["filter_name_id"];
       }
       if($data["filter_date_start"]=="")
       {
         $filter_date_start="null";
       }
       else
       {
            $filter_date_start="'".$data["filter_date_start"]."'";
       }
       if($data["filter_date_end"]=="")
       {
         $filter_date_end="null";
       }
       else
       {
            $filter_date_end="'".$data["filter_date_end"]."'";
       }

       $sql="  select sum(tt.quantity) as quantity, tt.name,tt.product_id,tt.store_name,ord_date from ( select sum(p.quantity) as quantity, (p.tax*p.quantity)as ttax,o.store_name,p.product_id,p.name,date(p.ORD_DATE)as ord_date from oc_order_product p left JOIN oc_order o on o.order_id=p.order_id GROUP by p.product_id,o.store_id,p.order_id ) as tt where ord_date between ifnull(".$filter_date_start.",ord_date)and ifnull(".$filter_date_end.",ord_date) and product_id=ifnull(".$filter_name_id.",product_id) GROUP by tt.product_id,tt.store_name,ord_date ORDER BY ord_date DESC   ";
        if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
                
		$query = $this->db->query($sql);
                	//echo $sql;
		return $query->rows;
   }

    public function getTotalOrders($data = array()) 
   {
       if($data["filter_name_id"]=="")
       {
         $filter_name_id="null";
       }
       else
       {
            $filter_name_id= $data["filter_name_id"];
       }
       if($data["filter_date_start"]=="")
       {
         $filter_date_start="null";
       }
       else
       {
            $filter_date_start="'".$data["filter_date_start"]."'";
       }
       if($data["filter_date_end"]=="")
       {
         $filter_date_end="null";
       }
       else
       {
            $filter_date_end="'".$data["filter_date_end"]."'";
       }

       $sql=" select count(*) as total,sum(quantity) as total_quantity from ( select sum(tt.quantity) as quantity, tt.name,tt.product_id,tt.store_name,ord_date from ( select sum(p.quantity) as quantity, (p.tax*p.quantity)as ttax,o.store_name,p.product_id,p.name,date(p.ORD_DATE)as ord_date from oc_order_product p left JOIN oc_order o on o.order_id=p.order_id GROUP by p.product_id,o.store_id,p.order_id ) as tt where ord_date between ifnull(".$filter_date_start.",ord_date)and ifnull(".$filter_date_end.",ord_date) and product_id=ifnull(".$filter_name_id.",product_id) GROUP by tt.product_id,tt.store_name,ord_date   ";
        

			$sql .= "   ) as aa";
		
                
		$query = $this->db->query($sql);
                //echo $sql;
		return $query->row;
   }
}
