<?php
//require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
error_reporting(0);
class ControllerPartnerPurchaseOrder extends Controller{
		public function get_current_inventory()
        {
            //echo $this->request->get['filter_id'];
            //echo $this->request->get['filter_ware_house'];
            $this->load->model('partner/purchase_order');
            echo $data_qnty=$this->model_partner_purchase_order->get_ware_house_quantity($this->request->get['filter_id'],$this->request->get['filter_ware_house']);
               
        }
        public function check_ware_house_quantity_by_account()
        {
            //print_r($this->request->post);exit;
            $total_product=count($this->request->post['ware_houses']);
            
            
            
            for($a=0;$a<$total_product;$a++)
            {
               $product_id=$this->request->post['product_id'][$a];
               $product_name=$this->request->post['product_name'][$a];
               $p_qnty=$this->request->post['receive_quantity'][$a];
               $ware_house=$this->request->post['ware_houses'][$a];
               
               $this->load->model('purchase/purchase_order');
               $data_qnty=$this->model_purchase_purchase_order->check_ware_house_quantity($ware_house,$product_id,$p_qnty);
               
               if($data_qnty=="0")
               {
                   echo 'There is not sufficent quantity of '.$product_name.' at ware house';
                   return;
               }
               
               
            }
            //$data_credit=$this->model_partner_purchase_order->check_ship_to_credit($ship_to,$grand_total);
            //if($data_credit=="0")
            //{
             //      echo 'Amount exceed from allowed credit limit ';
             //      return;
            //}
            //product_id
            //$store_id = $this->request->get['store_id'];
            //$this->load->model('partner/purchase_order');
            //echo $data=$this->model_partner_purchase_order->get_to_store_data($store_id);
            
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
            $this->load->model('partner/purchase_order');
            echo $data=$this->model_partner_purchase_order->get_to_store_data($store_id);
            
        }
        
        public function index()
	{
		//set the title of the page
			$this->document->setTitle("Purchase Order Partner");
			
			$data['column_left'] = $this->load->controller('common/column_left');
			
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			
			$url = '';
			$this->load->model('user/user');
			$user_info = $this->model_user_user->getUser($this->user->getId());
		    $data['user_group_id']=$user_info['user_group_id'];
			if (isset($this->request->get['filter_id'])) {
				$url .= '&filter_id=' . $this->request->get['filter_id'];
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
			if (isset($this->request->get['filter_store'])) {
				$url .= '&filter_store=' . $this->request->get['filter_store'];
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
			'text' => "Purchase Order Partner",
			'href' => $this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['add'] = $this->url->link('partner/purchase_order/add', 'token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('partner/purchase_order/delete', 'token=' . $this->session->data['token'] . $url, true);
		$data['filter'] = $this->url->link('partner/purchase_order/filter', 'token=' . $this->session->data['token'] . $url, true);
		/*getting the list of the orders*/
		
                if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];
                        $this->session->data['error_warning']='';
		} else {
			$data['error_warning'] = '';
		}
                if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
                        $this->session->data['success']='';
		} else {
			$data['success'] = '';
		}
                $this->load->model('purchase/purchase_order');
		$this->load->model('partner/purchase_order');
		
                        if (isset($this->request->get['page'])) {
                                $page = $this->request->get['page'];
                        } 
                        else {
                                $page = 1;
                        }
                        if (isset($this->request->get['filter_id'])) {
				$filter_id =  $this->request->get['filter_id'];
			}

			if (isset($this->request->get['filter_date_start'])) {
				$filter_date_start=$this->request->get['filter_date_start'];
			}
                        if (isset($this->request->get['filter_date_end'])) {
				$filter_date_end=$this->request->get['filter_date_end'];
			}
			if (isset($this->request->get['filter_status'])) {
				$filter_status=$this->request->get['filter_status'];
			}
			if (isset($this->request->get['filter_store'])) {
				$filter_store=$this->request->get['filter_store'];
			}
		
                        $filter_data=array(
                            'filter_id'=>$filter_id,
                            'filter_date_start'=>$filter_date_start,
                            'filter_date_end'=>$filter_date_end,
                            'filter_status'=>$filter_status,
		'filter_store'=>$filter_store,
                            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
                            'limit'   => $this->config->get('config_limit_admin')
                        );
		
		$order_list = $this->model_partner_purchase_order->getList($filter_data);
		//$data['order_list']=array();
               
                foreach($order_list as $order)
                {
                    
                    $order_id=$order['id'];
                     $order_information= $this->model_partner_purchase_order->view_order_details($order_id);
                    $total_product=count($order_information['products']);
                    if($total_product>1)
                    { //echo $order_information['products'][0]['ware_house_name'];
                       if($order_information['products'][0]['ware_house_name']!="")
                       {
                       $ware_house_name='Multiple'; 
                       }
                    }
                    else
                    {
                        $ware_house_name=$order_information['products'][0]['ware_house_name'];
                    }
                    //print_r($order_information['products'][0]['ware_house_name']);
                    $data['order_list'][]=array(
                        'id'=>$order_id,
                        'order_date'=>$order['order_date'],
                        'order_sup_send'=>$order['order_sup_send'],
                        'delete_bit'=>$order['delete_bit'],
                        'user_id'=>$order['user_id'],
                        'receive_date'=>$order['receive_date'],
                        'receive_bit'=>$order['receive_bit'],
                        'pending_bit'=>$order['pending_bit'],
                        'pre_supplier_bit'=>$order['pre_supplier_bit'],
                        'order_status_id'=>$order['order_status_id'],
                        'canceled_by'=>$order['canceled_by'],
                        'canceled_message'=>$order['canceled_message'],
                        'store_id'=>$order['store_id'],
                        'store_type'=>$order['store_type'],
                        'potential_date'=>$order['potential_date'],
                        'driver_otp'=>$order['driver_otp'],
                        'driver_mobile'=>$order['driver_mobile'],
                        'firstname'=>$order['firstname'],
                        'lastname'=>$order['lastname'],
                        'store_name'=>$order['store_name'],
                        'creditlimit'=>$order['creditlimit'],
                        'currentcredit'=>$order['currentcredit'],
                        'ware_house_name'=>$ware_house_name,
                        'ware_house_id'=>$order_information['products'][0]['ware_house_id'],
			'product' =>$order['product'],
			'quantity' =>$order['quantity']
                    ); 
                } 
		$data['stores']=$this->model_partner_purchase_order->partner_stores();
		//print_r($order_list);
		$total_orders = $this->model_partner_purchase_order->getTotalOrders($filter_data);
		
		//getting total orders
		$data['view'] = $this->url->link('partner/purchase_order/view_order_details', 'token=' . $this->session->data['token'] . $url, true);
		$data['receive'] = $this->url->link('partner/purchase_order/receive_order', 'token=' . $this->session->data['token'] . $url, true);
		$data['invoice'] = $this->url->link('partner/purchase_order/invoice', 'token=' . $this->session->data['token'] . $url, true);
		$data['download_invoice'] = $this->url->link('partner/purchase_order/download_invoice', 'token=' . $this->session->data['token'] . $url, true);
		/*pagination*/
		$pagination = new Pagination();
		$pagination->total = $total_orders;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin'); 
		$pagination->url = $this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);
		
		$data['pagination'] = $pagination->render();
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($total_orders) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_orders - $this->config->get('config_limit_admin'))) ? $total_orders : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_orders, ceil($total_orders / $this->config->get('config_limit_admin')));
                $data['filter_id']=$filter_id ;
		$data['from']=$filter_date_start ;
                	$data['to']=$filter_date_end ;
                	$data['status']=$filter_status ;
		$data['filter_store']=$filter_store ;
                	$data['token']=$this->request->get['token'];
		
		/*pagination*/
		$data['my_custom_text'] = "This is purchase order page.";
		$this->response->setOutput($this->load->view('partner/purchase_order_list.tpl', $data));
	}
        
	/*----------------------------view_order_details function starts here------------*/
	
	public function view_order_details()
	{
                $this->document->setTitle("View Order");
		$order_id = $this->request->get['order_id'];
		$data['column_left'] = $this->load->controller('common/column_left');
		/*$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');*/
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$url = '';
			
			if (isset($this->request->get['order_id'])) {
				$url .= '&order_id=' . urlencode(html_entity_decode($this->request->get['order_id'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_id'])) {
				$url .= '&filter_id=' . $this->request->get['filter_id'];
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
			'text' => "Home",
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);
			
			$data['breadcrumbs'][] = array(
			'text' => "Purchase Order Partner",
			'href' => $this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url, true)
		);
		
		$this->load->model('partner/purchase_order');
		$data['order_information'] = $this->model_partner_purchase_order->view_order_details($order_id);
		//print_r($data['order_information']);
                $data['cancel'] = $this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url, true);
		$data['pdf_export'] = $this->url->link('partner/purchase_order/view_order_details', 'token=' . $this->session->data['token'] . $url, true);
		if(isset($_GET['export']))
		{
			
			$data['company_name'] = $this->config->get('config_name'); // store name
			$data['company_title'] = $this->config->get('config_title'); // store title
			$data['company_owner'] = $this->config->get('config_owner'); // store owner name
			$data['company_email'] = $this->config->get('config_email'); // store email
			$data['company_address'] = $this->config->get('config_address');//store address
				
			$html = $this->load->view('purchase/print_order.tpl',$data);
			
			//$base_url = $this->config->get('config_url');

			$base_url = HTTP_CATALOG;
			
			//new mPDF($mode, $format, $font_size, $font, $margin_left, $margin_right, $margin_top, $margin_bottom, $margin_header, $margin_footer, $orientation);
			
			$mpdf = new mPDF('c','A4','','' , 5 , 5 , 25 , 10 , 5 , 7); 
			
			$header = '<div class="header"><div class="logo"><img src="'.$base_url.'image/catalog/logo.png" /></div><div class="company"><h3>'.$data['company_name'].'</h3></div></div><hr />';
 
			$mpdf->SetHTMLHeader($header, 'O', false);
				
			$footer = '<div class="footer"><div class="address"><b>Adress: </b>'.$data['company_address'].'</div><div class="pageno">{PAGENO}</div></div>';
				
			$mpdf->SetHTMLFooter($footer);
			
			//$mpdf->setFooter('{PAGENO}');
				 
			$mpdf->SetDisplayMode('fullpage');
 
			$mpdf->list_indent_first_level = 0;
 
			$mpdf->WriteHTML($html);
			
			$mpdf->Output();
		}
		else
		{
			$this->response->setOutput($this->load->view('partner/view_order.tpl',$data));
		}
	}
	
	/*----------------------------view_order_details function ends here--------------*/
	
	
	public function receive_order()
	{
                $this->document->setTitle("Receive Order");
		$order_id = $this->request->get['order_id'];
		$data['order_id'] = $order_id;
		$data['column_left'] = $this->load->controller('common/column_left');
		
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$url = '';
                $this->load->model('partner/purchase_order');
		
			if (isset($this->request->get['filter_id'])) {
				$url .= '&filter_id=' . $this->request->get['filter_id'];
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
                $data['token']=$this->session->data['token'];
                if($this->request->server['REQUEST_METHOD'] == 'POST')
                {
                    //print_r($this->request->post);
                    $data['order_receive_date']=$this->request->post['order_receive_date'];
                    $inserted = $this->model_partner_purchase_order->insert_receive_order($this->request->post,$order_id);
                    $this->session->data['success'] = 'Order Accepeted successfully';
                    $this->response->redirect($this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
            
                }
			
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
			'text' => "Home",
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);
			
			$data['breadcrumbs'][] = array(
			'text' => "Purchase Order Partner",
			'href' => $this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url, true)
		);
		
		//echo "here";	
		$data['order_information'] = $this->model_partner_purchase_order->view_order_details($order_id);
		if($data['order_information']['order_info']['receive_bit']==1)
		{
			$data['receive_bit'] = $data['order_information']['order_info']['receive_bit'];
		}
		else
		{
			$data['ftime_bit'] = 1;
		}
		$data['action'] = $this->url->link('partner/purchase_order/receive_order', 'token=' . $this->session->data['token'] . $url, true);
		$data['cancel'] = $this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url, true);
		//$this->load->model('purchase/supplier');
		//$data['suppliers'] = $this->model_purchase_supplier->get_total_suppliers();
                $data['ware_houses']=$this->model_partner_purchase_order->get_ware_houses();
                //print_r($data['order_information']);//['order_info']['user_id']
                $data['user_id']=$data['order_information']['order_info']['user_id'];
		$this->response->setOutput($this->load->view('partner/receive_order.tpl',$data));
	
	}
	
	/*-----------------------------Receive order function ends here-----------------*/
	/*----------------------------order_invoice function starts here------------*/
	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('partner/purchase_order');
			

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
				'filter_model' => $filter_name,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_partner_purchase_order->getProducts($filter_data);

			foreach ($results as $result) { //print_r($result);
				if($result['price_wo_t']=="")
				{
					$price_w_t=$result['price'];
				}
				else
				{
					$price_w_t=$result['price_wo_t'];
				}
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['model'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
                                        'hstn'=>$result['hstn'],
                                        'price'      => round($result['price'],PHP_ROUND_HALF_UP) ,
                                        'product_tax_type'=>$result['product_tax_type'],
                                        'price_wo_t'=>round($price_w_t,PHP_ROUND_HALF_UP),
                                        'product_tax_rate'=>round($result['product_tax_rate'],PHP_ROUND_HALF_UP)
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
        public function download_invoice()
        {
             $data['column_left'] = $this->load->controller('common/column_left');
             $data['footer'] = $this->load->controller('common/footer');
             $data['header'] = $this->load->controller('common/header');
                
             $order_id = $this->request->get['order_id'];
             $this->load->model('partner/purchase_order');
             $data['order_information'] = $this->model_partner_purchase_order->view_order_details_for_created_invoice($order_id); 
             $data['store_to_data']=$this->model_partner_purchase_order->get_to_store_data($data['order_information']['order_info']['store_to']);
             $data['created_po']=$data['order_information']['order_info']['po_invoice_n'];
             $data['order_id']=$order_id;
             //$this->response->setOutput($this->load->view('partner/order_invoice_print.tpl',$data));
             //print_r( $data['order_information']);
             
             
             $html=$this->load->view('partner/order_invoice_print.tpl',$data);
             require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
             $mpdf = new mPDF('c','A4','','' , 0 , 0 , 25 , 10 , 5 , 7);
             //$stylesheet = file_get_contents('https://unnati.world/shop/admin/view/stylesheet/pos/bootstrap.min.css'); // external css
             //$mpdf->WriteHTML($stylesheet,1); 
             //$stylesheet = file_get_contents('https://unnati.world/shop/admin/view/stylesheet/sheet.css'); // external css
             //$mpdf->WriteHTML($stylesheet,1);
             
             //exit;
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
                        <div class="address"><img src="../image/letterhead_footertext.png" style="width: 120% !important;" /> </div>'
                        . '</div>';

                $mpdf->setAutoBottomMargin = 'stretch';       	 
                $mpdf->SetHTMLFooter($footer);
                    
                $mpdf->SetDisplayMode('fullpage');
    
                $mpdf->list_indent_first_level = 0;
    
                $mpdf->WriteHTML($html);
                
                $filename='Invoice_'.$order_id.'.pdf';
                
                $mpdf->Output($filename,'D');
              
               
                
        }
		private function send_invoice($order_id,$email_address)
	{
		$data['column_left'] = $this->load->controller('common/column_left');
             $data['footer'] = $this->load->controller('common/footer');
             $data['header'] = $this->load->controller('common/header');
                
             $order_id;
             $this->load->model('partner/purchase_order');
             $data['order_information'] = $this->model_partner_purchase_order->view_order_details_for_created_invoice($order_id); 
             $data['store_to_data']=$this->model_partner_purchase_order->get_to_store_data($data['order_information']['order_info']['store_to']);
             $data['created_po']=$data['order_information']['order_info']['po_invoice_n'];
             $data['order_id']=$order_id;
             //$this->response->setOutput($this->load->view('partner/order_invoice_print.tpl',$data));
             
             
             
             $html=$this->load->view('partner/order_invoice_print.tpl',$data);
             require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
             $mpdf = new mPDF('c','A4','','' , 0 , 0 , 25 , 10 , 5 , 7);
            
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
                        <div class="address"><img src="../image/letterhead_footertext.png" style="width: 120% !important;" /> </div>'
                        . '</div>';

                $mpdf->setAutoBottomMargin = 'stretch';       	 
                $mpdf->SetHTMLFooter($footer);
                    
                $mpdf->SetDisplayMode('fullpage');
    
                $mpdf->list_indent_first_level = 0;
    
                $mpdf->WriteHTML($html);
                
                $filename='Invoice_'.$order_id.'.pdf';
                
                $mpdf->Output(DIR_UPLOAD.$filename,'F');


	////////////////////////
		$mail = new PHPMailer();
				//$this->request->post['filter_date']
				$body = "<p>Dear Sir,
					<br/><br/>
					Please find the invoice as attachment. 
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

				$mail->Subject    = "Invoice Created Mail";

				$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

				$mail->MsgHTML($body);
				
				$mail->AddAddress($email_address, $email_address);
				$mail->AddBCC('pragya.singh@aspltech.com','pragya singh');
				$mail->AddCC('ravi.ranjan@unnati.world','Ravi Ranjan');
				$mail->AddCC('ashok.prasad@akshamaala.com','Ashok Prasad');
				$mail->AddBCC('subhash.jha@unnati.world','Subhash Jha');
				
				$mail->AddBCC('vipin.kumar@aspltech.com','Vipin');
				$mail->AddAttachment(DIR_UPLOAD.$filename);
				if(!$mail->Send()) 
				{
				  echo "Mailer Error: " . $mail->ErrorInfo;
				} 
				else
				{
				
				  if(!unlink(DIR_UPLOAD.$filename))
				  {
					  echo ("Error deleting DIR_UPLOAD.$filename");
				  }
				  else
				  {
					 echo ("Deleted DIR_UPLOAD.$filename");
				  }
				
				}
	///////////////////////////

	}
	public function invoice()
	{
                $this->document->setTitle("Purchase Order Invoice");
				$order_id = $this->request->get['order_id'];
				$data['column_left'] = $this->load->controller('common/column_left');
                $this->load->model('partner/purchase_order');
                
                if($this->request->server['REQUEST_METHOD'] == 'POST')
                {
                    $url="&order_id=".$order_id;
                    if(!empty($this->request->post['ware_house']))
                    {
                    $url.="&ware_house=".$this->request->post['ware_house'];
                    }
                    if(!empty($this->request->post['store_to']))
                    {
                    $url.="&store_to=".$this->request->post['store_to'];
                    }
                    
                    $total_product=count($this->request->post['product_id']);
                    $ware_house=$data['ware_house']=$this->request->post['ware_house'];
                    $data['store_to']=$this->request->post['store_to'];
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
                            $this->session->data['error_warning']='There is not sufficent quantity of '.$product_name.' at ware house';
                            $this->response->redirect($this->url->link('partner/purchase_order/invoice', 'token=' . $this->session->data['token'] . $url, true));
                        }
                        if($data_price=="0")
                        {
                            $this->session->data['error_warning']='You can not enter the price less then the base price for '.$product_name;
                            $this->response->redirect($this->url->link('partner/purchase_order/invoice', 'token=' . $this->session->data['token'] . $url, true));
                        }
               
                    }
                    //print_r($this->request->post);exit;
                   
                    $this->model_partner_purchase_order->submit_po_invoice($this->request->post);
					
					$storetodata=$this->model_partner_purchase_order->get_to_store_data($this->request->post['store_to']); 
					$storetodata2=explode('---',$storetodata);
					$storeto_emailid=$storetodata2[3];
					$this->send_invoice($order_id,$storeto_emailid);
					
					////////for partner /////
					$formdata=array(
									
									'received_quantities'=>$this->request->post['p_qnty'],
									'received_product_ids'=>$this->request->post['product_id'],
									'suppliers_ids'=>array($this->request->post['ware_house']),
									'order_receive_date'=>date('Y-m-d'),
									'prices'=>$this->request->post['p_price'],
									'rq'=>array()
									
							);
					$orderdata=$this->model_partner_purchase_order->get_order_data($this->request->post['order_id']);
					//print_r($this->request->post);exit;
					$this->model_partner_purchase_order->insert_partner_receive_order($formdata,$this->request->post['order_id'],$orderdata['store_id'],$orderdata['user_id']);
					
					////////for partner /////  
					
                    $this->session->data['success']='Order Invoice created successfully';
                    $this->response->redirect($this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url, true));
                }
                
                $data['ware_house']=$this->request->get['ware_house'];
                $data['ware_house_id']=$this->request->get['ware_house_id'];
                //$data['store_to']=$this->request->get['store_to'];
                
                $data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
                $data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
			'text' => "Home",
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);
			
			$data['breadcrumbs'][] = array(
			'text' => "Purchase Order Partner",
			'href' => $this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url, true)
		);
		$data['token']=$this->session->data['token'];
                $data['cancel'] = $this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url, true);
                
                $created_po=$this->model_partner_purchase_order->check_po_invoice($order_id);
                if($created_po>0)
                {
                   $data['order_information'] = $this->model_partner_purchase_order->view_order_details_for_created_invoice($order_id); 
                   //print_r($data['order_information']['products']);
                   $data['store_to_data']=$this->model_partner_purchase_order->get_to_store_data($data['order_information']['order_info']['store_to']);
                   $data['created_po']=$created_po;
                }
		else
                {
                   $data['order_information'] = $this->model_partner_purchase_order->view_order_details_invoice($order_id); 
                   //print_r($data['order_information']['products']);
                   $data['created_po']='';
                   
                }
		$data['order_id']=$order_id;
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
		//print_r($data['order_information']['products']);
		$this->response->setOutput($this->load->view('partner/order_invoice.tpl',$data));
		
	}
	
	/*----------------------------order_invoice function ends here--------------*/ 
	
	
	
	
	
	/*-----------------------------insert receive order function starts here-------------------*/
	
	public function insert_receive_order()
	{
		$order_id = $this->request->get['order_id'];
		$received_quantities = $this->request->post['receive_quantity'];
		$suppliers_ids = $this->request->post['supplier'];
		$received_product_ids = $this->request->post['product_id'];
		
		$order_receive_date = $this->request->post['order_receive_date'];
		$prices = $this->request->post['price'];
		$rq = $this->request->post['remaining_quantity'];
		if(isset($this->request->post['disable_bit']))
		{
			$data['disable_bit'] = 1;
		}
		/*print_r($received_quantities);
		print_r($suppliers_ids);
		print_r($received_product_ids);
		print_r($order_receive_date);
		print_r($prices);
		exit;*/
		$received_order_info['received_quantities'] = $received_quantities;
		$received_order_info['received_product_ids'] = $received_product_ids;
		$received_order_info['suppliers_ids'] = $suppliers_ids;
		$received_order_info['order_receive_date'] = $order_receive_date;
		$received_order_info['prices'] = $prices;
		
		$received_order_info['rq'] = $rq;
		$url = ''; 
		if($order_id)
		{
			$url .= '&order_id=' . $order_id;
		}
                //print_r($this->request->post);
                if( $order_receive_date == '')
		{  
                    //echo "here ";exit;
                }
		if((count($received_quantities) != count(array_filter($received_quantities))) || (count($prices) != count(array_filter($prices))) || $order_receive_date == '')
		{  //echo "here else";exit;
			$_SESSION['empty_fields_error'] = 'Warning: Please check the form carefully for errors!';
			
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
			'text' => "Home",
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => "Purchase Order Partner",
			'href' => $this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url, true)
			);
			
			
			$this->load->model('partner/purchase_order');
			$data['order_information'] = $this->model_partner_purchase_order->view_order_details($order_id);
			
			for($i =0; $i<count($data['order_information']['products']); $i++)
			{
				unset($data['order_information']['products'][$i]['quantities']);
				unset($data['order_information']['products'][$i]['prices']);
			}
			
			$start_loop = 0;
			$data['validation_bit'] = 1;
			
			for($i = 0; $i<count($received_product_ids); $i++)
			{
				for($j = $start_loop; $j<count($prices); $j++)
				{
					if($prices[$j] == 'next product')
					{
						$start_loop = $j + 1;
						break;
					}
					else
					{
						$data['order_information']['products'][$i]['quantities'][$j] = $received_quantities[$j];
						$data['order_information']['products'][$i]['suppliers'][$j] = $suppliers_ids[$j];
						$data['order_information']['products'][$i]['prices'][$j] = $prices[$j];
					}
				}
				
				$data['order_information']['products'][$i]['quantities'] = array_values($data['order_information']['products'][$i]['quantities']);
				$data['order_information']['products'][$i]['suppliers'] = array_values($data['order_information']['products'][$i]['suppliers']);
				$data['order_information']['products'][$i]['prices'] = array_values($data['order_information']['products'][$i]['prices']);
				$data['order_information']['products'][$i]['rq'] = $received_order_info['rq'][$i];
			}
			
			$data['order_id'] = $order_id;
			if($order_receive_date)
			{
				$data['order_information']['order_info']['receive_date'] =  $order_receive_date;
			}
			else
			{
				$data['order_information']['order_info']['receive_date'] =  '0000-00-00';
			}
			//echo $order_receive_date;
			$data['action'] = $this->url->link('partner/purchase_order/insert_receive_order', 'token=' . $this->session->data['token'] . $url, true);
			$data['cancel'] = $this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url, true);
			$this->load->model('partner/supplier');
			$data['suppliers'] = $this->model_partner_supplier->get_total_suppliers();
			//$this->response->redirect($this->url->link('partner/purchase_order/receive_order', 'token=' . $this->session->data['token'] . $url, true));
			$this->response->setOutput($this->load->view('partner/receive_order.tpl',$data));
		}
		else
		{
			if(isset($this->request->post['disable_bit']))
			{
				unset($received_order_info['suppliers_ids']);
			}
			$this->load->model('partner/purchase_order');
			$inserted = $this->model_partner_purchase_order->insert_receive_order($received_order_info,$order_id);
			if($inserted)
			{
				$_SESSION['receive_success_message'] = 'Order received Successfully!!';
				$this->response->redirect($this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url, true));
			}
			else
			{
				$_SESSION['something_wrong_message'] = 'Sorry!! something went wrong, try again';
				$this->response->redirect($this->url->link('partner/purchase_order/insert_receive_order', 'token=' . $this->session->data['token'] . $url, true));
			}
				
		}
	}
	
	/*-----------------------------insert receive order function ends here-----------------*/
	/*--------------------Insert Purchase Order starts heres-------------------------------------------------*/
	
	public function insert_purchase_order()
	{
		$data['products'] = $_POST['product'];
		$data['options'] = $_POST['options'];
		$data['option_values'] = $_POST['option_values'];
		$data['quantity'] = $_POST['quantity'];
		$data['supplier_id'] = $_POST['supplier_id'];
		$data['stores'] = $_POST['stores'];
		$data['column_left'] = $this->load->controller('common/column_left');
		/*$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');*/
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['breadcrumbs'] = array();
		$data['token'] = $this->session->data['token'];
		
		/*to let the user add products without options*/
		for($i = 0 ; $i <count($data['options']); $i++)
		{
			if($data['options'][$i] == '')
			{
				$data['options'][$i] = '0_option';
			}
		}
		
		/*to let the user add products without option values*/
		for($i = 0 ; $i <count($data['option_values']); $i++)
		{
			if($data['option_values'][$i] == '')
			{
				$data['option_values'][$i] = '0_optionvalue';
			}
		}
		
		if((in_array("--products--",$data['products'])) || (in_array("--stores--",$data['storess'])) || (in_array("--Product Options--",$data['options'])) || (in_array("--Option Values--",$data['option_values'])) || (count($data['quantity']) != count(array_filter($data['quantity']))) || (count($data['options']) != count(array_filter($data['options']))) || (count($data['products']) != count(array_filter($data['products']))) || (count($data['option_values']) != count(array_filter($data['option_values']))))
		{
                    
			$data['form_bit'] = 0;
			$_SESSION['errors'] = "Warning: Please check the form carefully for errors!";
			/*------------Working with data received starts-----*/
			
			$i = 0;
			foreach($data['products'] as $product)
			{
				if(strrchr($product,"_"))
				{
				$product_names[$i] = explode('_',$product);
				}
				else
				{
					$product_names[$i] = $product;
				}
				$i++;
			}
			$data['product_received'] = $product_names;
			$i = 0;
			foreach($data['options'] as $option)
			{
				if(strrchr($option,"_"))
				{
					$options[$i] = explode('_',$option);
				}
				else
				{
					$options[$i] = $option;
				}
				$i++;
			}
			$data['options_received'] = $options;
			$i = 0;
			foreach($data['option_values'] as $option_value)
			{
				if(strrchr($option_value,"_"))
				{
					$option_values[$i] = explode('_',$option_value);
				}
				else
				{
					$option_values[$i] = $option_value;
				}
				$i++;
			}
			$data['option_values_received'] = $option_values;
			//print_r($data['option_values_received']);
			$data['quantities_received'] = $data['quantity'];
			/*------working with data received ends---------*/
			$this->load->model('catalog/product');
			$products = $this->model_catalog_product->getProducts();
			$i = 0;
			foreach($products as $product)
			{
				$products[$i] = $product['name'];
				$product_ids[$i] = $product['product_id'];
				$i++;
			}
			$data['products'] = $products;
			$data['product_ids'] = $product_ids;
			

			
			$this->load->model('catalog/option');
			$data['options'] = $this->model_catalog_option->getOptions();
			/*$i = 0;
			foreach($data['options_received'] as $option)
			{
				$option_values[$i] = $this->model_catalog_option->getOptionValues($option[0]); 
				$i++;
			}*/
			$data['option_values'] = $option_values;
			$url = '';
			$data['action'] = $this->url->link('partner/purchase_order/insert_purchase_order', 'token=' . $this->session->data['token'] . $url, true);
			$data['cancel'] = $this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url, true);
			$this->load->model('partner/supplier');
			$data['suppliers'] = $this->model_partner_supplier->get_total_suppliers();
		                //stores
                        $this->load->model('setting/store');
                        $data['stores'] = $this->model_setting_store->getStores();  
			$this->load->model('catalog/option');
			$data['options'] = $this->model_catalog_option->getOptions();
			$this->response->setOutput($this->load->view('partner/purchase_order_form.tpl', $data));
			//$this->response->redirect($this->url->link('partner/purchase_order/add', 'token=' . $this->session->data['token'] . $url, true));
		}
		else
		{
			$i = 0;
			foreach($data['products'] as $product)
			{
				$product_names[$i] = explode('_',$product);
				$i++;
			}
			$data['products'] = $product_names;
			//stores
                        $i = 0;
			foreach($data['stores'] as $store)
			{
				$store_names[$i] = explode('_',$store);
				$i++;
			}
			$data['stores'] = $store_names;
                        
                        
			$i = 0;
			foreach($data['options'] as $option)
			{
				if(strrchr($option,"_"))
				{
					$options[$i] = explode('_',$option);
				}
				else
				{
					$options[$i] = $option;
				}
				$i++;
			}
			$data['options'] = $options;
			$i = 0;
			foreach($data['option_values'] as $option_value)
			{
				if(strrchr($option_value,"_"))
				{
					$option_values[$i] = explode('_',$option_value);
				}
				else
				{
					$option_values[$i] = $option_value;
				}
				$i++;
			}
			
			$data['option_values'] = $option_values;
			
			$this->load->model('partner/purchase_order');
			$order_id = $this->model_partner_purchase_order->insert_purchase_order($data);
			
			
			
			if(isset($this->request->post['mail_bit']))
			{
                               
				$data['company_name'] = $this->config->get('config_name'); // store name
				$data['company_title'] = $this->config->get('config_title'); // store title
				$data['company_owner'] = $this->config->get('config_owner'); // store owner name
				$data['company_email'] = $this->config->get('config_email'); // store email
				$data['company_address'] = $this->config->get('config_address');//store address
				
				$this->load->model('partner/purchase_order');
				$data['order_information'] = $this->model_partner_purchase_order->view_order_details($order_id);
		
				
				$html = $this->load->view('partner/mail_purchase_order.tpl',$data);
				
				$base_url = HTTP_CATALOG;
				
				$mpdf = new mPDF('c','A4','','' , 5 , 5 , 25 , 10 , 5 , 7); 
				
				$header = '<div class="header"><div class="logo"><img src="'.$base_url.'image/catalog/logo.png" /></div><div class="company"><h3>'.$data['company_name'].'</h3></div></div><hr />';
	 
				$mpdf->SetHTMLHeader($header, 'O', false);
					
				$footer = '<div class="footer"><div class="address"><b>Adress: </b>'.$data['company_address'].'</div><div class="pageno">{PAGENO}</div></div>';
					
				$mpdf->SetHTMLFooter($footer);
					 
				$mpdf->SetDisplayMode('fullpage');
	 
				$mpdf->list_indent_first_level = 0;
	 
				$mpdf->WriteHTML($html);
				
				$mpdf->Output('../orders/order.pdf','F');
				
				//mailing
				
				$mail             = new PHPMailer();

				$body = "<p>Akshamaala Solution Pvt. Ltd.</p>";
				
				$mail->IsSMTP(); 
				$mail->Host       = "mail.akshamaala.in"; 
														   
				$mail->SMTPAuth   = false;                 
				$mail->SMTPSecure = "";                 
				$mail->Host       = "mail.akshamaala.in";      
				$mail->Port       = 25;                  
				$mail->Username   = "mis@akshamaala.in";  
				$mail->Password   = "mismis";            

				$mail->SetFrom($data['company_email'], $data['company_name']);

				$mail->AddReplyTo($data['company_email'],$data['company_name']);

				$mail->Subject    = "Product Order to Supplier";

				$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

				$mail->MsgHTML($body);
				
				//to get the email of supplier
				$query = $this->db->query('SELECT email FROM oc_po_supplier WHERE id = ' .$data['supplier_id']);
				
				$address = $query->row['email'];
				
				$mail->AddAddress($address, "Turaab Ali");
				
				$file_to_attach = '../orders/order.pdf';

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
			
			if($order_id)
			{
				$_SESSION['success_order_message'] = "The Order has been added";
				$this->response->redirect($this->url->link('partner/purchase_order', 'token=' . $this->session->data['token'] . $url, true));
			}
		}
	}
	
	/*--------------------Insert purchase order ends here----------------------------*/
	
	///////////////////////////////////////////////////////////////////////////////////
	public function download_excel()
	{
		 if (isset($this->request->get['filter_id'])) {
				$filter_id =  $this->request->get['filter_id'];
			}

			if (isset($this->request->get['filter_date_start'])) {
				$filter_date_start=$this->request->get['filter_date_start'];
			}
                        if (isset($this->request->get['filter_date_end'])) {
				$filter_date_end=$this->request->get['filter_date_end'];
			}
			if (isset($this->request->get['filter_status'])) {
				$filter_status=$this->request->get['filter_status'];
			}
			if (isset($this->request->get['filter_store'])) {
				$filter_store=$this->request->get['filter_store'];
			}
		
                        $filter_data=array(
                            'filter_id'=>$filter_id,
                            'filter_date_start'=>$filter_date_start,
                            'filter_date_end'=>$filter_date_end,
                            'filter_status'=>$filter_status,
		'filter_store'=>$filter_store,
                            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
                            'limit'   => $this->config->get('config_limit_admin')
                        );
		$this->load->model('partner/purchase_order');
		$results=$this->model_partner_purchase_order->download_excel($filter_data);
		//print_r($results);
		//exit;

		$file_name="partner_sale_data_".date('dMy').'.xls';
 		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
 		header("Content-Disposition: attachment; filename=".$file_name);  //File name extension was wrong
 		header("Expires: 0");
 		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
 		header("Cache-Control: private",false);

		echo '<table id="example2" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
	      <th>Sale Date</th>
                    <th>Partner Name</th>
                    <th>Partner ID</th>
                    <th>Partner GSTN</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
 	      <th>Rate(without tax)</th>
	      <th>Amount</th>
	      <th>Tax title</th>
	      <th>Tax rate</th>
	      
	      <th>Total Tax</th>
                    <th>Invoice  Amount</th>
                    <th>Invoice Number</th>
	      <th>Purchase Order/ Reference ID</th>
                </tr>
                </thead>
                <tbody>';
$tblbody=" ";
foreach($results as $data)
{


                    echo  '<tr> 
	      <td>'.date('Y-m-d',strtotime($data['create_date'])).'</td>
                    <td>'.$data['name'].'</td>
                    <td>'.$data['store_id'].'</td>
	      <td>'.$data['gstn'].'</td>
                    <td>'.$data['product_name'].'</td>
	      <td>'.$data['p_qnty'].'</td>
                    <td>'.number_format((float)($data['p_price']), 2, '.', '').'</td>
	      <td>'.number_format((float)($data['p_price']*$data['p_qnty']), 2, '.', '').'</td>';
	      if($data['p_tax_type']=="")
	      {
		echo '<td>No tax</td>';
		
	      }
	     else
	      {
		echo '<td>'.$data['p_tax_type'].'</td>';
	      }
	      
	      if($data['p_tax_type']=="GST@5%")
	      {
	       	echo '<td>'.number_format((float)5, 2, '.', '').'</td>';
		$total_tax=($data['p_price']*$data['p_qnty']*5)/100;
	      }
	      else if($data['p_tax_type']=="GST@12%")
	      {
		echo '<td>'.number_format((float)12, 2, '.', '').'</td>';
		$total_tax=($data['p_price']*$data['p_qnty']*12)/100;
	      }
	      else if($data['p_tax_type']=="GST@18%")
	      {
		echo '<td>'.number_format((float)18, 2, '.', '').'</td>';
		$total_tax=($data['p_price']*$data['p_qnty']*18)/100;
	      }
	     else
	      {
		echo '<td>0</td>';
		$total_tax=0;
	      }
                    echo '<td>'.number_format((float)$total_tax, 2, '.', '').'</td>';
	      echo  '<td>'.number_format((float)($data['p_price']*$data['p_qnty']+($total_tax)), 2, '.', '').'</td>
                    <td>'.$data['invoice_number'].'</td>
	      <td>'.$data['id'].'</td>
                   </tr>';


}


echo '</tbody>
          </table>';

 

	}

	public function email_invoice()
        {
             $data['column_left'] = $this->load->controller('common/column_left');
             $data['footer'] = $this->load->controller('common/footer');
             $data['header'] = $this->load->controller('common/header');
                
             $order_id = $this->request->get['order_id'];
             $this->load->model('partner/purchase_order');
             $data['order_information'] = $this->model_partner_purchase_order->view_order_details_for_created_invoice($order_id); 
             $data['store_to_data']=$this->model_partner_purchase_order->get_to_store_data($data['order_information']['order_info']['store_to']);
             $data['created_po']=$data['order_information']['order_info']['po_invoice_n'];
             $data['order_id']=$order_id;

             $inv_number=$data['order_information']['order_info']['po_invoice_prefix']."/".$data['created_po'];

	//print_r($data['order_information']['products'][0]['store_name']);
	//exit;
             //$this->response->setOutput($this->load->view('partner/order_invoice_print.tpl',$data));
             $store_to_data=explode('---',$data['store_to_data']);
             $store_user_email_id=$store_to_data[3];
             
             $html=$this->load->view('partner/order_invoice_print.tpl',$data);

	

             require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
             $mpdf = new mPDF('c','A4','','' , 0 , 0 , 25 , 10 , 5 , 7);
            
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
                        <div class="address"><img src="../image/letterhead_footertext.png" style="width: 120% !important;" /> </div>'
                        . '</div>';

                $mpdf->setAutoBottomMargin = 'stretch';       	 
                $mpdf->SetHTMLFooter($footer);
                    
                $mpdf->SetDisplayMode('fullpage');
    
                $mpdf->list_indent_first_level = 0;
    
                $mpdf->WriteHTML($html);
                
                $filename='Invoice_'.$inv_number.'.pdf';
                $filename=str_replace('/','_',$filename);

                $mpdf->Output(DIR_UPLOAD.$filename,'F');


              $mail             = new PHPMailer();

                $body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear Sir,
			<br/><br/>
			Please find the invoice as per request. The details are as follows:-
			<br/>
			Party Name : ".$data['order_information']['products'][0]['store_name']."
			<br/>
			Invoice Number : ".$inv_number." 
			<br/><br/>
			This is computer generated invoice and does not need signature of stamp.
			<br/><br/>

			Please do not reply to this email.
			<br/><br/>
			Thanking you,
			<br/>
			Care Team
			<br/>
			Unnati
		</p>";
                
                $mail->IsSMTP();
                $mail->Host       = "mail.akshamaala.in";
                                                           
                $mail->SMTPAuth   = false;                 
                $mail->SMTPSecure = "";                 
                $mail->Host       = "mail.akshamaala.in";      
                $mail->Port       = 25;                  
                $mail->Username   = "mis@akshamaala.in";  
                $mail->Password   = "mismis";            

                $mail->SetFrom('mail.akshamaala.in', 'Akshamaala');

                $mail->AddReplyTo('mail.akshamaala.in','Akshamaala');

                $mail->Subject    = "Invoice number - ".$inv_number;

                $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

                $mail->MsgHTML($body);
                
                //to get the email of supplier
                $mail->AddAddress($store_user_email_id, $store_user_email_id);
                $mail->AddCC('ashok.prasad@akshamaala.com', "Ashok Prasad");
                $mail->AddBCC('vipin.kumar@aspltech.com', "Vipin");
	  $mail->AddCC('pragya.singh@aspltech.com', "Pragya Singh");
	 
                $mail->AddBCC('chetan.singh@akshamaala.com', "Chetan Singh");
		
                

                $mail->AddAttachment(DIR_UPLOAD.$filename);
                
                if(!$mail->Send())
                {
                  echo "Mailer Error: " . $mail->ErrorInfo;
                }
                else
                {
                  if(!unlink(DIR_UPLOAD.$filename))
                  {
                      echo ("Error deleting");
                  }
                  else
                  {
                     echo ("Deleted");
                  }
                                  
                }
              
                
        }


	
}

?>