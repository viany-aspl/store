 <?php
//require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
error_reporting(0);
class ControllerPurchaseorderPurchaseOrder extends Controller{
    public function index()
    {
                
	$this->document->setTitle("Create PO");
	$data['column_left'] = $this->load->controller('common/column_left');
	$data['footer'] = $this->load->controller('common/footer');
	$data['header'] = $this->load->controller('common/header');
	$url = '';
        if (isset($this->request->get['filter_supplier'])) 
        {
	    $url .= '&filter_supplier=' . $this->request->get['filter_supplier'];
	}

	if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
	}
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
	}
	if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
	}

	if (isset($this->request->get['page'])) {
	    $url .= '&page=' . $this->request->get['page'];
	}
                        
                $data['breadcrumbs'] = array();

                $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
                );
			
			$data['breadcrumbs'][] = array(
			'text' => "Create PO",
			'href' => $this->url->link('invoice/purchase_order', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['add'] = $this->url->link('purchaseorder/purchase_order/purchase_add', 'token=' . $this->session->data['token'] . $url, true);
		
		$this->load->model('purchaseorder/purchase_order');
		//print_r($this->request->get);exit;
                if (isset($this->request->get['page'])) {
                            $page = $this->request->get['page'];
                } 
                else {
                            $page = 1;
                }
                if (isset($this->request->get['filter_supplier'])) {
                            $filter_supplier =  $this->request->get['filter_supplier'];
		}

		if (isset($this->request->get['filter_date_start'])) {
                            $filter_date_start=$this->request->get['filter_date_start'];
		}
                else {
                            $filter_date_start=date('Y-m')."-01";
                }
                if (isset($this->request->get['filter_date_end'])) {
                            $filter_date_end=$this->request->get['filter_date_end'];
		}
                else {
                            $filter_date_end=date('Y-m-d');
                }
	if (isset($this->request->get['filter_status'])) {
                            $filter_status =  $this->request->get['filter_status'];
		}		

			
                $filter_data=array(
                            
                            'filter_date_start'=>$filter_date_start,
                            'filter_date_end'=>$filter_date_end,
                            'filter_supplier'=>$filter_supplier,
		'filter_status'=>$filter_status,
                            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
                            'limit'   => $this->config->get('config_limit_admin')
                );
		
		$created_po=$this->model_purchaseorder_purchase_order->getList($filter_data);
		$data['order_list'] = $created_po->rows;
		
		$total_orders = $created_po->num_rows;
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];

			unset($this->session->data['error_warning']);
		} else {
			$data['error_warning'] = '';
		}
		/*pagination*/
		$pagination = new Pagination();
		$pagination->total = $total_orders;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('purchaseorder/purchase_order', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);
		$data['suppliers'] = $this->model_purchaseorder_purchase_order->getSuppliers();
                
		$data['pagination'] = $pagination->render();
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($total_orders) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_orders - $this->config->get('config_limit_admin'))) ? $total_orders : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_orders, ceil($total_orders / $this->config->get('config_limit_admin')));
                
		$data['filter_id']=$filter_id ;
		$data['filter_date_start']=$filter_date_start ;
                	$data['filter_date_end']=$filter_date_end ;
                	$data['filter_supplier']=$filter_supplier ;
		$data['filter_status']=$filter_status;
                	$data['token']=$this->request->get['token'];


		$this->load->model('user/user');
		$user_info = $this->model_user_user->getUser($this->user->getId());
		//print_r($user_info);
		$data['user_group_id']=$user_info['user_group_id'];
		$data['my_custom_text'] = "This is purchase order page.";
		$this->response->setOutput($this->load->view('purchaseorder/purchase_order_list.tpl', $data));
	}
        public function purchase_invoice()
    {
                
	$this->document->setTitle("Invoice Update");
	$data['column_left'] = $this->load->controller('common/column_left');
	$data['footer'] = $this->load->controller('common/footer');
	$data['header'] = $this->load->controller('common/header');
	$url = '';
        if (isset($this->request->get['filter_supplier'])) 
        {
	    $url .= '&filter_supplier=' . $this->request->get['filter_supplier'];
	}

	if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
	}
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
	}
	if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
	}

	if (isset($this->request->get['page'])) {
	    $url .= '&page=' . $this->request->get['page'];
	}
                        
                $data['breadcrumbs'] = array();

                $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
                );
			
			$data['breadcrumbs'][] = array(
			'text' => "Invoice Update",
			'href' => $this->url->link('invoice/purchase_order', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['add'] = $this->url->link('purchaseorder/purchase_order/purchase_add', 'token=' . $this->session->data['token'] . $url, true);
		
		$this->load->model('purchaseorder/purchase_order');
		//print_r($this->request->get);exit;
                if (isset($this->request->get['page'])) {
                            $page = $this->request->get['page'];
                } 
                else {
                            $page = 1;
                }
                if (isset($this->request->get['filter_supplier'])) {
                            $filter_supplier =  $this->request->get['filter_supplier'];
		}

		if (isset($this->request->get['filter_date_start'])) {
                            $filter_date_start=$this->request->get['filter_date_start'];
		}
                else {
                            $filter_date_start=date('Y-m')."-01";
                }
                if (isset($this->request->get['filter_date_end'])) {
                            $filter_date_end=$this->request->get['filter_date_end'];
		}
                else {
                            $filter_date_end=date('Y-m-d');
                }
			

			
                $filter_data=array(
                            
                            'filter_date_start'=>$filter_date_start,
                            'filter_date_end'=>$filter_date_end,
                            'filter_supplier'=>$filter_supplier,
                            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
                            'limit'   => $this->config->get('config_limit_admin')
                );
		
		
		$data['order_list'] = $this->model_purchaseorder_purchase_order->getinvoiceList($filter_data);
		
		$total_orders = $this->model_purchaseorder_purchase_order->getinvoiceTotalOrders($filter_data);
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];

			unset($this->session->data['error_warning']);
		} else {
			$data['error_warning'] = '';
		}
		/*pagination*/
		$pagination = new Pagination();
		$pagination->total = $total_orders;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('purchaseorder/purchase_order/purchase_invoice', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);
		$data['suppliers'] = $this->model_purchaseorder_purchase_order->getSuppliers();
                
		$data['pagination'] = $pagination->render();
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($total_orders) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_orders - $this->config->get('config_limit_admin'))) ? $total_orders : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_orders, ceil($total_orders / $this->config->get('config_limit_admin')));
                
		$data['filter_id']=$filter_id ;
		$data['filter_date_start']=$filter_date_start ;
                	$data['filter_date_end']=$filter_date_end ;
                	$data['filter_supplier']=$filter_supplier ;
                	$data['token']=$this->request->get['token'];
		$data['my_custom_text'] = "This is purchase order page.";
		$this->response->setOutput($this->load->view('purchaseorder/purchase_invoice_list.tpl', $data));
	}
	 
        public function purchase_payment()
    {
                
	$this->document->setTitle("Purchase Payment");
	$data['column_left'] = $this->load->controller('common/column_left');
	$data['footer'] = $this->load->controller('common/footer');
	$data['header'] = $this->load->controller('common/header');
	$url = '';
        if (isset($this->request->get['filter_supplier'])) 
        {
	    $url .= '&filter_supplier=' . $this->request->get['filter_supplier'];
	}

	if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
	}
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
	}
	if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
	}

	if (isset($this->request->get['page'])) {
	    $url .= '&page=' . $this->request->get['page'];
	}
                        
                $data['breadcrumbs'] = array();

                $data['breadcrumbs'][] = array(
                'text' => 'Purchase Payment',
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
                );
			
			$data['breadcrumbs'][] = array(
			'text' => "Purchase Payment",
			'href' => $this->url->link('purchase/purchase_order/purchase_payment', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['add'] = $this->url->link('purchaseorder/purchase_order/purchase_add', 'token=' . $this->session->data['token'] . $url, true);
		
		$this->load->model('purchaseorder/purchase_order');
		//print_r($this->request->get);exit;
                if (isset($this->request->get['page'])) {
                            $page = $this->request->get['page'];
                } 
                else {
                            $page = 1;
                }
                if (isset($this->request->get['filter_supplier'])) {
                            $filter_supplier =  $this->request->get['filter_supplier'];
		}

		if (isset($this->request->get['filter_date_start'])) {
                            $filter_date_start=$this->request->get['filter_date_start'];
		}
                else {
                            $filter_date_start=date('Y-m')."-01";
                }
                if (isset($this->request->get['filter_date_end'])) {
                            $filter_date_end=$this->request->get['filter_date_end'];
		}
                else {
                            $filter_date_end=date('Y-m-d');
                }
			

			
                $filter_data=array(
                            
                            'filter_date_start'=>$filter_date_start,
                            'filter_date_end'=>$filter_date_end,
                            'filter_supplier'=>$filter_supplier,
                            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
                            'limit'   => $this->config->get('config_limit_admin')
                );
		$data['noresult']='No Result Found';
		if(!empty($filter_supplier))
		{
		$data['order_list'] = $this->model_purchaseorder_purchase_order->getpaymentList($filter_data);
		
		$total_orders11 = $this->model_purchaseorder_purchase_order->getpaymentTotalOrders($filter_data);
		$total_orders=$total_orders11['total_orders'];
		$data['total_outstanding']=$total_orders11['total_outstanding'];  
		$supplier_data=$this->model_purchaseorder_purchase_order->get_to_supplier_data($filter_supplier);
		$supplier_data2=explode('---',$supplier_data);
		//print_r($data['order_list']);
		$data['supplier_wallet_balance'] = $supplier_data2[7];
		}
		else
		{
			$data['noresult']='Please Select Supplier';
		}
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];

			unset($this->session->data['error_warning']);
		} else {
			$data['error_warning'] = '';
		}
		/*pagination*/
		$pagination = new Pagination();
		$pagination->total = $total_orders;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('purchaseorder/purchase_order/purchase_payment', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);
		$data['suppliers'] = $this->model_purchaseorder_purchase_order->getSuppliers();
                
		$data['pagination'] = $pagination->render();
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($total_orders) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_orders - $this->config->get('config_limit_admin'))) ? $total_orders : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_orders, ceil($total_orders / $this->config->get('config_limit_admin')));
                
		$data['filter_id']=$filter_id ;
		$data['filter_date_start']=$filter_date_start ;
                	$data['filter_date_end']=$filter_date_end ;
                	$data['filter_supplier']=$filter_supplier ;
                	$data['token']=$this->request->get['token'];
		$data['my_custom_text'] = "This is purchase order page.";
		$this->response->setOutput($this->load->view('purchaseorder/purchase_payment_list.tpl', $data));
	}
        public function getreamrks() { 
            
            $pono = $this->request->get['po_id']; 
           
            $this->load->model('purchaseorder/purchase_order');
			
            echo $submit_d=$this->model_purchaseorder_purchase_order->getremarks($pono);         
	
        }
        public function adjustpayment() { 
            
            $pono = $this->request->get['pono']; 
            $amount = $this->request->get['amount'];
            $this->load->model('purchaseorder/purchase_order');
			 $data['logged_user_data'] = $this->user->getId();
			
            $submit_d=$this->model_purchaseorder_purchase_order->adjustpayment($pono,$amount,'adjustment',$data['logged_user_data']);        
			$file_path=$this->create_pdf_for_payment($this->request->get['pono']);
			$this->send_payment_email($file_path,'',$this->request->get['pono']); 
	         
			//exit;
            $this->session->data['success']='Payment Adjustment is done Successfully';                 
            //$this->response->redirect($this->url->link('purchaseorder/purchase_order/purchase_payment_list', 'token=' . $this->session->data['token'] . $url, true));
                
            
        }
        public function create_pdf_for_payment($order_id)
        {
            
             //$data['column_left'] = $this->load->controller('common/column_left');
            // $data['footer'] = $this->load->controller('common/footer');
             //$data['header'] = $this->load->controller('common/header');
               
             
             $this->load->model('purchaseorder/purchase_order');
             $data['order_information'] = $this->model_purchaseorder_purchase_order->view_order_details_for_created_invoice_b2b($order_id);

             $data['store_to_data']=$this->model_purchaseorder_purchase_order->get_to_store_data($data['order_information']['order_info']['store_id']);
             $data['created_po']=$data['order_information']['order_info']['po_invoice_n'];
             $data['invoice_id']=$order_id;
	
	$data['supplier_name']=$data['order_information']['order_info']['first_name'].' '.$data['order_information']['order_info']['last_name'];
	$data['supplier_address']=$data['order_information']['order_info']['ADDRESS'];
	$data['supplier_ac']=$data['order_information']['order_info']['ACC_ID'];
	$data['supplier_ifsc']=$data['order_information']['order_info']['IFSC_CODE'];
	$data['amount']=$this->request->get['amount'];
	$data['tr_number']='NA'; 

	$data['invoice_amount']= $data['order_information']['order_info']['invoice_amount'];
              $data['invoice_number']= $data['order_information']['order_info']['invoice_no'];
	$data['invoice_date']= $data['order_information']['order_info']['invoice_date'];
	$data['bank_name']=$this->request->post['payment_bank'];
	$data['payment_method']=$this->request->post['payment_method'];

             //print_r($data['order_information']['order_info']);
             //$this->response->setOutput($this->load->view('purchaseorder/purchase_order_payment_print.tpl',$data));
            //exit;

             $html=$this->load->view('purchaseorder/purchase_order_payment_print.tpl',$data);
             require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
             $mpdf = new mPDF('c','A3','','' , 0 , 0 , 25 , 10 , 5 , 7);
            
                $header = '<br/><div class="header" style="margin-top: 20px;">
        <div class="logo">
        <img style="float: right;margin-right: 40px;height: 30px;" src="https://unnati.world/shop/image/catalog/logo.png"  />
        </div>
        
        <img src="https://unnati.world/shop/image/letterhead_topline.png" style="height: 10px; width: 120% !important;;" />
    </div>';
               
                $header = '<div class="header" style="">
                  
<div class="logo" style="width: 100%;" >
<div style="padding-left: 50px;">
<img src="https://unnati.world/shop/image/letterhead_text.png" style="height: 40px; width: 121px;margin-top: 20px;marging-left: 100px;" />
<img src="https://unnati.world/shop/image/letterhead_log.png" style="height: 55px; width: 121px;float: right;" />
</div>
                         </div>
<img src="https://unnati.world/shop/image/letterhead_topline.png" style="height: 10px; width: 120% !important;" />

</div>';
                $mpdf->setAutoTopMargin = 'stretch';
                $mpdf->SetHTMLHeader($header, 'O', false);
                 
                $footer = '<div class="footer">
                       
                        <img src="https://unnati.world/shop/image/letterhead_bottomline.png" style="height: 10px; width: 150% !important;" />
                        <div class="address"><img src="https://unnati.world/shop/image/letterhead_footertext.png" style="width: 120% !important;" /> </div>'
                        . '</div>';

                $mpdf->setAutoBottomMargin = 'stretch';            
                $mpdf->SetHTMLFooter($footer);
                   
                $mpdf->SetDisplayMode('fullpage');
   
                $mpdf->list_indent_first_level = 0;
   
                $mpdf->WriteHTML($html);
               $supplier_name=str_replace('&','-',$data['supplier_name']);
	 $supplier_name=str_replace(' ','-',$supplier_name);
                $filename=DIR_UPLOAD.'Supplier/'.$supplier_name. '_po_payment_'.$order_id.'.pdf';
               
                $mpdf->Output($filename,'F');
                return $filename;
             
           
               
        }
        public function send_payment_email($file_path,$supplier_id,$order_id)
        {
			$this->load->model('purchaseorder/purchase_order');
			
             			$data['order_information'] = $this->model_purchaseorder_purchase_order->view_order_details_for_created_invoice_b2b($order_id);
			//print_r($data['order_information']['order_info']['invoice_no']);
			
			$dbdata=$this->model_purchaseorder_purchase_order->get_to_supplier_data($data['order_information']['order_info']['supplier_id']);
                                	$dbdata2=explode('---',$dbdata);
			//print_r($dbdata2);exit;
                                	$mail = new PHPMailer();

				 $body = "<p>Dear ".$dbdata2[1].",
					<br/><br/>
					We would like to inform you that the following payment has been released .
					
					<br/><br/>
					Invoice number  : ".$data['order_information']['order_info']['invoice_no']."
					<br/><br/>
					Amount : ".$this->request->get['amount']."
					
					<br/><br/>
					Thanking you for your support and we look forward towards your continued support.
					<br/><br/>
					<strong>
					With Warm Regards,
					<br/>
					Account & Billing
					</strong>
					
					
				</p>";
				
				$mail->IsSMTP(); 
				$mail->Host       = "mail.akshamaala.in"; 
														   
				$mail->SMTPAuth   = false;                 
				$mail->SMTPSecure = "";                 
				$mail->Host       = "mail.akshamaala.in";      
				$mail->Port       = 25;                  
				$mail->Username   = "mis@akshamaala.in";  
				$mail->Password   = "mismis";            

				$mail->SetFrom('accounts@unnati.world', 'Account');

				$mail->AddReplyTo('accounts@unnati.world', 'Account');

				$mail->Subject    = "Payment update Mail";

				$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

				$mail->MsgHTML($body);
				
				//to get the email of supplier
                                		
                                		$address = $dbdata2[4];
                                		//$address = 'vipin.kumar@aspltech.com';
                               		 //echo $file_path;
				//exit;
				$mail->AddAddress($address, $dbdata2[1]);
				$mail->AddBCC('pragya.singh@aspltech.com','pragya singh');
				$mail->AddCC('ravi.ranjan@unnati.world','Ravi Ranjan');
				$mail->AddCC('ashok.prasad@akshamaala.com','Ashok Prasad');
				$mail->AddBCC('subhash.jha@unnati.world','Subhash Jha'); 
				//$mail->AddAddress('vipin.kumar@aspltech.com', $dbdata2[1]);
				$file_to_attach = $file_path;

				$mail->AddAttachment($file_to_attach);
				
				if(!$mail->Send()) 
				{
				  echo "Mailer Error: " . $mail->ErrorInfo;
				} 
				else
				{
				  if(!unlink($file_to_attach))
				  {
					  echo ("Error deleting $file_to_attach");
				  }
				  else
				  {
					 echo ("Deleted $file_to_attach");
				  }
				}
        }

        
        
        
	 public function check_ware_house_quantity()
        {
            //print_r($this->request->post);exit;
            $total_product=count($this->request->post['product_id']);
            $ware_house=$this->request->post['ware_house'];
            $ship_to=$this->request->post['ship_to'];
            $grand_total=$this->request->post['grand_total'];
            for($a=0;$a<$total_product;$a++)
            {
               $product_id=$this->request->post['product_id'][$a];
               $product_name=$this->request->post['product_name'][$a];
               $p_qnty=$this->request->post['p_qnty'][$a];
               $p_price=$this->request->post['p_price'][$a];
               
               $this->load->model('partner/purchase_order');
               $data_qnty=$this->model_partner_purchase_order->check_ware_house_quantity($ware_house,$product_id,$p_qnty);
               $data_price=$this->model_partner_purchase_order->check_ware_house_price($ware_house,$product_id,$p_price);
               if($data_qnty=="0")
               {
                   echo 'There is not sufficent quantity of '.$product_name.' at ware house';
                   return;
               }
               if($data_price=="0")
               {
                   echo 'You can not enter the price less then the base price for '.$product_name;
                   return;
               }
               
            }
            $data_credit=$this->model_partner_purchase_order->check_ship_to_credit($ship_to,$grand_total);
            if($data_credit=="0")
            {
                   echo 'Amount exceed from allowed credit limit ';
                   return;
            }
            //product_id
            //$store_id = $this->request->get['store_id'];
            //$this->load->model('partner/purchase_order');
            //echo $data=$this->model_partner_purchase_order->get_to_store_data($store_id);
            
        }
        public function get_to_store_data()
        {
            $store_id = $this->request->get['store_id'];
            $this->load->model('purchaseorder/purchase_order');
            echo $data=$this->model_purchaseorder_purchase_order->get_to_store_data($store_id);
            
        }
         public function get_to_supplier_data()
        {
            $supplier_id = $this->request->get['supplier_id'];
            $this->load->model('purchaseorder/purchase_order');
           echo $data=$this->model_purchaseorder_purchase_order->get_to_supplier_data($supplier_id);
            
        }
	/*----------------------------order_invoice function starts here------------*/
	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('purchaseorder/purchase_order');
			

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				
				'start'        => 0,
				'limit'        => $limit
			);
			if(!empty($filter_name))
			{
				$results = $this->model_purchaseorder_purchase_order->getProducts($filter_data);
			}
			//print_r($results);
			foreach ($results as $result) { //print_r($result);
				
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
                    'hstn'=>$result['HSTN'],
                    'price'      => round($result['price_tax'],PHP_ROUND_HALF_UP) ,
                    'product_tax_type'=>$result['tax_class_name'],
                    'price_wo_t'=>round($result['price'],PHP_ROUND_HALF_UP),
                    'product_tax_rate'=>round(($result['price_tax']-$result['price']),PHP_ROUND_HALF_UP)
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
        
	public function user_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('purchaseorder/purchase_order');
			

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_name,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_purchaseorder_purchase_order->getUsers($filter_data);
			//print_r($results);
			foreach ($results as $result) { //print_r($result);
				
				$json[] = array(
					
					'name'       => strip_tags(html_entity_decode($result['firstname']." ".$result['lastname'], ENT_QUOTES, 'UTF-8')),
					'mobile_number'      => $result['username']
                                        
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
        
    public function purchase_add()
    {
                $this->document->setTitle("Purchase Order");
                $order_id = $this->request->get['order_id'];
                $data['column_left'] = $this->load->controller('common/column_left');
                $this->load->model('purchaseorder/purchase_order');
                
                $this->load->model('setting/store');               
                $data['stores'] = $this->model_setting_store->getStores();
                $this->load->model('purchaseorder/purchase_order');                
                $data['suppliers'] = $this->model_purchaseorder_purchase_order->getSuppliers();
                
             
                if($this->request->server['REQUEST_METHOD'] == 'POST')
                { 
                    $this->load->model('purchaseorder/purchase_order');                 
					//print_r($this->request->post);exit;
                    $submit_d=$this->model_purchaseorder_purchase_order->submit_purchase_order($this->request->post);
					
                    //$po_number=$submit_d['po_no'];
                    $order_id=$submit_d;
                    $log=new Log('supplier-'.date('Y-m-d').'.log');
					$log->write($this->request->post);
                    if(($this->request->post['buttonvalue']=='save_email') && (isset($this->request->post['buttonvalue'])))
                    {
                       $file_path=$this->create_pdf_order($order_id); 
                       //$this->send_email($file_path,$this->request->post['filter_supplier'],$order_id);
                        //print_r($this->request->post['filter_supplier']); exit;
                    }
                    
                    $this->session->data['success']='Purchase Order  Successfully PO Number : ASPL/BB/'.$submit_d;
                 
                    $this->response->redirect($this->url->link('purchaseorder/purchase_order', 'token=' . $this->session->data['token'] . $url, true));
                }
               
                $data['ware_house']=$this->request->get['ware_house'];
                //$data['store_to']=$this->request->get['store_to'];
               
                $data['footer'] = $this->load->controller('common/footer');
                $data['header'] = $this->load->controller('common/header');
                $data['breadcrumbs'] = array();

                $data['breadcrumbs'][] = array(
                                'text' => "Home",
                                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
                                );
           
                $data['breadcrumbs'][] = array(
                                'text' => "B2B Invoice",
                                'href' => $this->url->link('invoice/purchase_order/b2b', 'token=' . $this->session->data['token'] . $url, true)
                                );
                $data['token']=$this->session->data['token'];
                $data['cancel'] = $this->url->link('purchaseorder/purchase_order', 'token=' . $this->session->data['token'] . $url, true);
               
                $created_po=$this->model_purchaseorder_purchase_order->check_po_invoice($order_id);
                $created_po="";
                
                $data['order_id']=$order_id;
                if (isset($this->session->data['success']))
                {
                    $data['success'] = $this->session->data['success'];

                    unset($this->session->data['success']);
                }
                else
                {
                    $data['success'] = '';
                }
                if (isset($this->session->data['error_warning']))
                {
                    $data['error_warning'] = $this->session->data['error_warning'];

                    unset($this->session->data['error_warning']);
                }
                else
                {
                    $data['error_warning'] = '';
                }
              
       
                $this->response->setOutput($this->load->view('purchaseorder/purchase_order_add.tpl',$data));
       
    }

    public function update_po_qnty()
    {
	$po_id=$this->request->get['po_id'];
	$old_qnty=$this->request->get['old_qnty'];
	$new_qnty=$this->request->get['new_qnty'];
	$rate=$this->request->get['rate'];
	$remarks=$this->request->get['remarks'];
	$amount=$rate*$new_qnty;

	$this->load->model('user/user');
	$user_info = $this->model_user_user->getUser($this->user->getId());
	//print_r($user_info );
	$data['user_group_id']=$user_info['user_group_id'];
	$this->load->model('purchaseorder/purchase_order');
	$this->model_purchaseorder_purchase_order->update_po_qnty($po_id,$old_qnty,$new_qnty,$user_info['user_id'],$amount,$remarks);
	echo '1';
    }
public function get_prn_list()
    {
	$store_id=$this->request->get['store_id'];
	$product_id=$this->request->get['product_id'];
	$this->load->model('purchaseorder/purchase_order');
	$get_prn_list_array=$this->model_purchaseorder_purchase_order->get_prn_list(array('store_id'=>$store_id,'product_id'=>$product_id));
	
	echo '<option value=""> SELECT PURCHASE REQUEST NUMBER</option>';
	foreach($get_prn_list_array as $get_prn_list2)
	{
		echo '<option value="',$get_prn_list2['po_id'].'">'.$get_prn_list2['po_id'].'-('.$get_prn_list2['product_name'].'-'.$get_prn_list2['quantity'].')-'.date('d M Y',strtotime($get_prn_list2['order_date'])).'</option>';
	}
	
    }
    public function purchase_invoice_add_old()
    {
                $this->document->setTitle("Purchase Invoice");
                $order_id = $this->request->get['pono'];
                $data['column_left'] = $this->load->controller('common/column_left');
                $this->load->model('purchaseorder/purchase_order');
                
                $this->load->model('setting/store');               
                $data['stores'] = $this->model_setting_store->getStores();
                $this->load->model('purchaseorder/purchase_order');                
                $data['suppliers'] = $this->model_purchaseorder_purchase_order->getSuppliers();
                
             
                if($this->request->server['REQUEST_METHOD'] == 'POST')
                {
                        $this->load->model('purchaseorder/purchase_order');                 
                  
	          
                        $submit_d=$this->model_purchaseorder_purchase_order->submit_purchase_invoice($order_id,$this->request->post);
						if($submit_d==0)
						{
							$this->session->data['error']='Invoice Number is already Added';
                 
							$this->response->redirect($this->url->link('purchaseorder/purchase_order/purchase_invoice', 'token=' . $this->session->data['token'] . $url, true));
						}
		 //print_r($this->request->post);
		//exit;
	          $supplier_id=$this->model_purchaseorder_purchase_order->getPODetailsbyID($order_id) ;			
	          $this->send_invoice_email('',$supplier_id);	
                        //exit;		
	          $path = "../system/upload/Supplier/"; 
                        
                        $file_extensions= array("pdf","doc","docx","zip","rar",'jpeg','jpg','png');
                 
                         $file_name = @$_FILES['snapshot']['name'];

                        
                        if($file_name!="")
                        {
		$file_size =@$_FILES['snapshot']['size'];
                        	$file_tmp =@$_FILES['snapshot']['tmp_name'];
                        	$file_type=@$_FILES['snapshot']['type'];
                        	$arrrr=explode('.',$file_name); 
                        	$exttt=end($arrrr);
                        	$file_ext= strtolower($exttt);
                            if(in_array($file_ext, $file_extensions)) 
                            { 
                    
                                if(is_writable($path))
                                {
                                    //echo "yes";exit;
                                }
                                else 
                                {
                                    
                                }
                            $new_file_name='invoice'.$submit_d.date('dmy')."_".date('his').".".$file_ext;
                            $file_path=$path.$new_file_name;
                            $move= move_uploaded_file($file_tmp,$file_path);
                            if($move)
                            {
                            
                                $this->model_purchaseorder_purchase_order->update_file($submit_d,$new_file_name);
                               
                            }
                      
                      
                      
                        }
						}
						//exit;
                    //$po_number=$submit_d['po_no'];
                    $order_id=$submit_d;
                    
                    if($this->request->post['buttonvalue']=='save_email')
                    {
                        //$file_path=$this->create_pdf_order($order_id);
                        //$this->send_email($file_path,$this->request->post['filter_supplier']);
                        //print_r($this->request->post['filter_supplier']); exit;
                    }
                    
                    $this->session->data['success']='Purchase Invoice  Successfully';
                 
                    $this->response->redirect($this->url->link('purchaseorder/purchase_order/purchase_invoice', 'token=' . $this->session->data['token'] . $url, true));
                }
               
                $data['ware_house']=$this->request->get['ware_house'];
                //$data['store_to']=$this->request->get['store_to'];
               
                $data['footer'] = $this->load->controller('common/footer');
                $data['header'] = $this->load->controller('common/header');
                $data['breadcrumbs'] = array();

                $data['breadcrumbs'][] = array(
                                'text' => "Home",
                                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
                                );
           
                $data['breadcrumbs'][] = array(
                                'text' => "B2B Invoice",
                                'href' => $this->url->link('invoice/purchase_order/b2b', 'token=' . $this->session->data['token'] . $url, true)
                                );
                $data['token']=$this->session->data['token'];
                $data['cancel'] = $this->url->link('purchaseorder/purchase_order/purchase_invoice', 'token=' . $this->session->data['token'] . $url, true);
               
                $created_po=$this->model_purchaseorder_purchase_order->check_po_invoice($order_id);
                $created_po="";
                //print_r($order_id);
                $data['order_information'] = $this->model_purchaseorder_purchase_order->view_order_details_for_created_invoice_b2b($order_id);
                  //print_r($data['order_information']);
                $data['store_to_data']=$this->model_purchaseorder_purchase_order->get_to_store_data($data['order_information']['order_info']['store_id']);
               
               // print_r( $data['store_to_data']);
                $data['order_id']=$order_id;
                if (isset($this->session->data['success']))
                {
                    $data['success'] = $this->session->data['success'];

                    unset($this->session->data['success']);
                }
                else
                {
                    $data['success'] = '';
                }
                if (isset($this->session->data['error_warning']))
                {
                    $data['error_warning'] = $this->session->data['error_warning'];

                    unset($this->session->data['error_warning']);
                }
                else
                {
                    $data['error_warning'] = '';
                }
              
       
                $this->response->setOutput($this->load->view('purchaseorder/purchase_invoice_add.tpl',$data));
       
    }
   public function purchase_invoice_add()
    {
                $this->document->setTitle("Purchase Invoice");
                $order_id = $this->request->get['pono'];
                $data['column_left'] = $this->load->controller('common/column_left');
                $this->load->model('purchaseorder/purchase_order');
                
                $this->load->model('setting/store');               
                $data['stores'] = $this->model_setting_store->getStores();
                $this->load->model('purchaseorder/purchase_order');                
                $data['suppliers'] = $this->model_purchaseorder_purchase_order->getSuppliers();
                
             
                if($this->request->server['REQUEST_METHOD'] == 'POST')
                {
                        $this->load->model('purchaseorder/purchase_order');                 
                  
	          //print_r($this->request->post);
		//exit; 
                        $submit_d=$this->model_purchaseorder_purchase_order->submit_purchase_invoice($order_id,$this->request->post);
						if($submit_d==0)
						{
							$this->session->data['error']='Oops! Some error occur,Please try again.';
                 
							$this->response->redirect($this->url->link('purchaseorder/purchase_order/purchase_invoice', 'token=' . $this->session->data['token'] . $url, true));
							exit;
						}
						$supplier_id=$this->model_purchaseorder_purchase_order->getPODetailsbyID($order_id) ;			
	          $this->send_invoice_email('',$supplier_id);	 
                        //exit;		
	          $path = "../system/upload/Supplier/"; 
                        
                        $file_extensions= array("pdf","doc","docx","zip","rar",'jpeg','jpg','png');
                 
                         $file_name = @$_FILES['snapshot']['name'];
  
                        
                        if($file_name!="")
                        {
		$file_size =@$_FILES['snapshot']['size'];
                        	$file_tmp =@$_FILES['snapshot']['tmp_name'];
                        	$file_type=@$_FILES['snapshot']['type'];
                        	$arrrr=explode('.',$file_name); 
                        	$exttt=end($arrrr);
                        	$file_ext= strtolower($exttt);
                            if(in_array($file_ext, $file_extensions)) 
                            { 
                    
                                if(is_writable($path))
                                {
                                    //echo "yes";exit;
                                }
                                else 
                                {
                                    
                                }
                            $new_file_name='invoice'.$submit_d.date('dmy')."_".date('his').".".$file_ext;
                            $file_path=$path.$new_file_name;
                            $move= move_uploaded_file($file_tmp,$file_path);
                            if($move)
                            {
                            
                                $this->model_purchaseorder_purchase_order->update_file($submit_d,$new_file_name);
                               
                            }
                      
                      
                      
                        }
						}
						//exit;
                    //$po_number=$submit_d['po_no'];
                    $order_id=$submit_d;
                    
                    if($this->request->post['buttonvalue']=='save_email')
                    {
                        //$file_path=$this->create_pdf_order($order_id);
                        //$this->send_email($file_path,$this->request->post['filter_supplier']);
                        //print_r($this->request->post['filter_supplier']); exit;
                    }
                    
                    $this->session->data['success']='Purchase Invoice  Successfully';
                 
                    $this->response->redirect($this->url->link('purchaseorder/purchase_order/purchase_invoice', 'token=' . $this->session->data['token'] . $url, true));
                }
               
                $data['ware_house']=$this->request->get['ware_house'];
                //$data['store_to']=$this->request->get['store_to'];
               
                $data['footer'] = $this->load->controller('common/footer');
                $data['header'] = $this->load->controller('common/header');
                $data['breadcrumbs'] = array();

                $data['breadcrumbs'][] = array(
                                'text' => "Home",
                                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
                                );
           
                $data['breadcrumbs'][] = array(
                                'text' => "B2B Invoice",
                                'href' => $this->url->link('invoice/purchase_order/b2b', 'token=' . $this->session->data['token'] . $url, true)
                                );
                $data['token']=$this->session->data['token'];
                $data['cancel'] = $this->url->link('purchaseorder/purchase_order/purchase_invoice', 'token=' . $this->session->data['token'] . $url, true);
               
                $created_po=$this->model_purchaseorder_purchase_order->check_po_invoice($order_id);
                $created_po="";
                //print_r($order_id);
                $data['order_information'] = $this->model_purchaseorder_purchase_order->view_order_details_for_created_invoice_b2b($order_id);
                  //print_r($data['order_information']);
                $data['store_to_data']=$this->model_purchaseorder_purchase_order->get_to_store_data($data['order_information']['order_info']['store_id']);
               
                $this->load->model('setting/store');               
                $data['stores'] = $this->model_setting_store->getOwnStores();

                $data['order_id']=$order_id;
                if (isset($this->session->data['success']))
                {
                    $data['success'] = $this->session->data['success'];

                    unset($this->session->data['success']);
                }
                else
                {
                    $data['success'] = '';
                }
                if (isset($this->session->data['error_warning']))
                {
                    $data['error_warning'] = $this->session->data['error_warning'];

                    unset($this->session->data['error_warning']);
                }
                else
                {
                    $data['error_warning'] = '';
                }
              
       
                $this->response->setOutput($this->load->view('purchaseorder/purchase_invoice_add.tpl',$data));
       
    }
   
    /*----------------------------order_invoice function ends here--------------*/
   
        public function send_email($file_path,$supplier_id,$order_id)
        {
		$this->load->model('purchaseorder/purchase_order');
		$product_name=$this->model_purchaseorder_purchase_order->getProduct($this->request->post['product_id']);
		$data['order_information'] = $this->model_purchaseorder_purchase_order->view_order_details_for_created_invoice_b2b($order_id);
		//print_r($data['order_information']['order_info']['invoice_no']);
			
		$dbdata=$this->model_purchaseorder_purchase_order->get_to_supplier_data($data['order_information']['order_info']['supplier_id']);
                            $dbdata2=explode('---',$dbdata);

		$mail_subject='ASPL PO : '.$dbdata2[1].'_ASPL/PO/'.$data['order_information']['order_info']['sid'].'_'.$product_name.'_'.$data['order_information']['order_info']['create_date']; 
                                $mail = new PHPMailer();

				$body = "<p>Dear Sir,
					<br/><br/>
					We are pleased to share the purchase order for the following material. 
					
					<br/><br/>
					Name of Product  : ".$product_name."
					<br/><br/>
					Quantity : ".$this->request->post['p_qnty']."
					<br/><br/>
					We look forward towards your acknowledgement and supply of the material as per the terms and conditions agreed.
					<br/><br/>
					<strong>
					With Warm Regards,
					<br/>
					Account & Billing
					</strong>
					<br/><br/>
					<span style='font-size:10px;'><i>
						This is an auto generated mail and please do not reply to this mail. In case of clarification please call accounts / billing team.
					</i></span>
					
				</p>";
				
				$mail->IsSMTP(); 
				$mail->Host       = "mail.akshamaala.in"; 
														   
				$mail->SMTPAuth   = false;                 
				$mail->SMTPSecure = "";                 
				$mail->Host       = "mail.akshamaala.in";      
				$mail->Port       = 25;                  
				$mail->Username   = "mis@akshamaala.in";  
				$mail->Password   = "mismis";            

				$mail->SetFrom('accounts@unnati.world', 'Account');

				$mail->AddReplyTo('accounts@unnati.world', 'Account');

				$mail->Subject    = $mail_subject;//"Po Raised Mail";

				$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

				$mail->MsgHTML($body);
				
				//to get the email of supplier
                                		$this->load->model('purchaseorder/purchase_order');
				$dbdata=$this->model_purchaseorder_purchase_order->get_to_supplier_data($supplier_id);
                                		$dbdata2=explode('---',$dbdata);
                                		$address = $dbdata2[4];
                                		//$address = 'vipin.kumar@aspltech.com';
                               		 //echo $file_path;
				//exit;$dbdata2[1]
				$mail->AddAddress($address, $address);
				$mail->AddCC('ravi.ranjan@unnati.world','Ravi Ranjan');
				$mail->AddCC('ashok.prasad@akshamaala.com','Ashok Prasad');
				$mail->AddBCC('subhash.jha@unnati.world','Subhash Jha');
				$mail->AddBCC('pragya.singh@aspltech.com','pragya singh');
				
				$file_to_attach = $file_path;

				$mail->AddAttachment($file_to_attach);
				
				if(!$mail->Send()) 
				{
				  echo "Mailer Error: " . $mail->ErrorInfo;
				} 
				else
				{
				  if(!unlink($file_to_attach))
				  {
					  echo ("Error deleting $file_to_attach");
				  }
				  else
				  {
					 echo ("Deleted $file_to_attach");
				  }
				}
        }
        public function send_invoice_email($file_path,$supplier_id)
        {
		//print_r($this->request->post);
		//$this->request->post['filter_date']."
                                $mail = new PHPMailer();
				//$this->request->post['filter_date']
				$body = "<p>Dear Sir,
					<br/><br/>
					Please be updated that the invoice has been accepted. The same shall be processed as per agreed terms and conditions.
					
					<br/><br/>
					Invoice number  : ".$this->request->post['invoiceno']."
					<br/><br/>
					Amount : ".$this->request->post['grand_total']."
					<br/><br/>
					Date of Receipt : ".DATE('d-m-Y')."
					<br/><br/>
					We look forward towards your support on the subject.
					<br/><br/>
					<strong>
					With Warm Regards,
					<br/>
					Account & Billing
					</strong>
					<br/><br/>
					<span style='font-size:10px;'><i>
						This is an auto generated mail and please do not reply to this mail. In case of clarification please call accounts / billing team.
					</i></span>
					
				</p>";
				
				$mail->IsSMTP(); 
				$mail->Host       = "mail.akshamaala.in"; 
														   
				$mail->SMTPAuth   = false;                 
				$mail->SMTPSecure = "";                 
				$mail->Host       = "mail.akshamaala.in";      
				$mail->Port       = 25;                  
				$mail->Username   = "mis@akshamaala.in";  
				$mail->Password   = "mismis";            

				$mail->SetFrom('accounts@unnati.world', 'Account');

				$mail->AddReplyTo('accounts@unnati.world', 'Account');

				$mail->Subject    = "Invoice update Mail";

				$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

				$mail->MsgHTML($body);
				
				//to get the email of supplier
                                		$this->load->model('purchaseorder/purchase_order');
				$dbdata=$this->model_purchaseorder_purchase_order->get_to_supplier_data($supplier_id);
                                		$dbdata2=explode('---',$dbdata);
                                		$address = $dbdata2[4];
                                		//$address = 'vipin.kumar@aspltech.com';
                               		 //echo $file_path;
				//exit;
				$mail->AddAddress($address, $address);
				$mail->AddBCC('pragya.singh@aspltech.com','pragya singh');
				$mail->AddCC('ravi.ranjan@unnati.world','Ravi Ranjan');
				$mail->AddCC('ashok.prasad@akshamaala.com','Ashok Prasad');
				$mail->AddBCC('subhash.jha@unnati.world','Subhash Jha');
				
				//$dbdata2[1]
				//$file_to_attach = $file_path;

				//$mail->AddAttachment($file_to_attach);
				
				if(!$mail->Send()) 
				{
				  echo "Mailer Error: " . $mail->ErrorInfo;
				} 
				else
				{
				/*
				  if(!unlink($file_to_attach))
				  {
					  echo ("Error deleting $file_to_attach");
				  }
				  else
				  {
					 echo ("Deleted $file_to_attach");
				  }
				*/
				}
        }
        
        public function create_pdf_order($order_id)
        {
            
             $data['column_left'] = $this->load->controller('common/column_left');
             $data['footer'] = $this->load->controller('common/footer');
             $data['header'] = $this->load->controller('common/header');
               
             
             $this->load->model('purchaseorder/purchase_order');
             $data['order_information'] = $this->model_purchaseorder_purchase_order->view_order_details_for_created_invoice_b2b($order_id);
	$id_prefix=str_replace("/","_",$data['order_information']['order_info']['id_prefix']);
             $data['store_to_data']=$this->model_purchaseorder_purchase_order->get_to_store_data($data['order_information']['order_info']['store_id']);
             $data['created_po']=$data['order_information']['order_info']['po_invoice_n'];
             $data['invoice_id']=$order_id;
             //exit;
             //$this->response->setOutput($this->load->view('purchaseorder/purchase_order_print.tpl',$data));
            
             $html=$this->load->view('purchaseorder/purchase_order_print.tpl',$data);
             require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
             $mpdf = new mPDF('c','A3','','' , 0 , 0 , 25 , 10 , 5 , 7);
            
                $header = '<br/><div class="header" style="margin-top: 20px;">
        <div class="logo">
        <img style="float: right;margin-right: 40px;height: 30px;" src="https://unnati.world/shop/image/catalog/logo.png"  />
        </div>
        
        <img src="https://unnati.world/shop/image/letterhead_topline.png" style="height: 10px; width: 120% !important;;" />
    </div>';
               
                $header = '<div class="header" style="">
                  
<div class="logo" style="width: 100%;" >
<div style="padding-left: 50px;">
<img src="https://unnati.world/shop/image/letterhead_text.png" style="height: 40px; width: 121px;margin-top: 20px;marging-left: 100px;" />
<img src="https://unnati.world/shop/image/letterhead_log.png" style="height: 55px; width: 121px;float: right;" />
</div>
                         </div>
<img src="https://unnati.world/shop/image/letterhead_topline.png" style="height: 10px; width: 120% !important;" />

</div>';
                $mpdf->setAutoTopMargin = 'stretch';
                $mpdf->SetHTMLHeader($header, 'O', false);
                 
                $footer = '<div class="footer">
                       
                        <img src="https://unnati.world/shop/image/letterhead_bottomline.png" style="height: 10px; width: 150% !important;" />
                        <div class="address"><img src="https://unnati.world/shop/image/letterhead_footertext.png" style="width: 120% !important;" /> </div>'
                        . '</div>';

                $mpdf->setAutoBottomMargin = 'stretch';            
                $mpdf->SetHTMLFooter($footer);
                   
                $mpdf->SetDisplayMode('fullpage');
   
                $mpdf->list_indent_first_level = 0;
   
                $mpdf->WriteHTML($html);
               $supplier_name=str_replace('&','-',$data['order_information']['order_info']['first_name'].'_'.$data['order_information']['order_info']['last_name']);
	$supplier_name=str_replace(' ','-',$supplier_name);
                $filename=DIR_UPLOAD.'Supplier/'.$supplier_name. '_PO_'.$id_prefix.$order_id.'.pdf';
               
                $mpdf->Output($filename,'F');
                return $filename;
             
           
               
        }
        public function download_purchase_order()
        {
            
             $data['column_left'] = $this->load->controller('common/column_left');
             $data['footer'] = $this->load->controller('common/footer');
             $data['header'] = $this->load->controller('common/header');
               
             $order_id = $this->request->get['invoice_id'];
             $this->load->model('purchaseorder/purchase_order');
             $data['order_information'] = $this->model_purchaseorder_purchase_order->view_order_details_for_created_invoice_b2b($order_id);
	//print_r($data['order_information']['order_info']['id_prefix']);exit;
	$id_prefix=str_replace("/","_",$data['order_information']['order_info']['id_prefix']);
             $data['store_to_data']=$this->model_purchaseorder_purchase_order->get_to_store_data($data['order_information']['order_info']['store_id']);
             $data['created_po']=$data['order_information']['order_info']['po_invoice_n'];
             $data['invoice_id']=$order_id;
             $this->response->setOutput($this->load->view('purchaseorder/purchase_order_print.tpl',$data));
            
             $html=$this->load->view('purchaseorder/purchase_order_print.tpl',$data);
             require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
             $mpdf = new mPDF('c','A3','','' , 0 , 0 , 25 , 10 , 5 , 7);
            
                $header = '<br/><div class="header" style="margin-top: 20px;">
        <div class="logo">
        <img style="float: right;margin-right: 40px;height: 30px;" src="https://unnati.world/shop/image/catalog/logo.png"  />
        </div>
        
        <img src="https://unnati.world/shop/image/letterhead_topline.png" style="height: 10px; width: 120% !important;;" />
    </div>';
               
                $header = '<div class="header" style="">
                  
<div class="logo" style="width: 100%;" >
<div style="padding-left: 50px;">
<img src="https://unnati.world/shop/image/letterhead_text.png" style="height: 40px; width: 121px;margin-top: 20px;marging-left: 100px;" />
<img src="https://unnati.world/shop/image/letterhead_log.png" style="height: 55px; width: 121px;float: right;" />
</div>
                         </div>
<img src="https://unnati.world/shop/image/letterhead_topline.png" style="height: 10px; width: 120% !important;" />

</div>';
                $mpdf->setAutoTopMargin = 'stretch';
                $mpdf->SetHTMLHeader($header, 'O', false);
                 
                $footer = '<div class="footer">
                       
                        <img src="https://unnati.world/shop/image/letterhead_bottomline.png" style="height: 10px; width: 150% !important;" />
                        <div class="address"><img src="https://unnati.world/shop/image/letterhead_footertext.png" style="width: 120% !important;" /> </div>'
                        . '</div>';

                $mpdf->setAutoBottomMargin = 'stretch';            
                $mpdf->SetHTMLFooter($footer);
                   
                $mpdf->SetDisplayMode('fullpage');
   
                $mpdf->list_indent_first_level = 0;
   
                $mpdf->WriteHTML($html);
               $supplier_name=str_replace('&','-',$data['order_information']['order_info']['first_name'].'_'.$data['order_information']['order_info']['last_name']);
                 $filename=$supplier_name. '_PO_'.$id_prefix.$order_id.'.pdf';
               
                $mpdf->Output($filename,'D');
          
           
               
        } 

}

?>