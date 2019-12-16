<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of createVillage
 *
 * @author agent
 */
class Controllercustomertranstransation extends Controller {
    public function  index(){
        
        $this->load->language('geo/searchgeo');
        

           
        $data['heading_title'] = $this->language->get('heading_title');
        
        if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
                
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
                
              
               $this->getList();
               
               
              
    }
    
    protected function getList() {
       
        $data['token'] = $this->session->data['token'];
	$this->document->setTitle('Customer Transaction');
        //echo $this->request->get['tab'];
        $url="";
        //if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
            if($this->request->get['tab']=="1")
            {
                $data['tab1'] ="active";
                $data['tab2'] ="";
                $data['tab3'] ="";
                $data['tab4'] ="";
                $data['tab5'] ="";
                $data['tab6'] ="";
                $data['tab7'] ="";
	  $data['tab8'] ="";
            }
             else if($this->request->get['tab']=="2")
            {
                $data['tab1'] ="";
                $data['tab2'] ="active";
                $data['tab3'] ="";
                $data['tab4'] ="";
                $data['tab5'] ="";
                $data['tab6'] ="";
                $data['tab7'] ="";
	  $data['tab8'] ="";
            }
             else if(trim($this->request->get['tab'])=="3")
            {
                $data['tab1'] ="";
                $data['tab2'] ="";
                $data['tab3'] ="active";
                $data['tab4'] ="";
                $data['tab5'] ="";
                $data['tab6'] ="";
                $data['tab7'] ="";
	  $data['tab8'] ="";
            }
             else if($this->request->get['tab']=="4")
            {
                $data['tab1'] ="";
                $data['tab2'] ="";
                $data['tab3'] ="";
                $data['tab4'] ="active";
                $data['tab5'] ="";
                $data['tab6'] ="";
                $data['tab7'] ="";
	  $data['tab8'] ="";
            }
             else if($this->request->get['tab']=="5")
            {
                $data['tab1'] ="";
                $data['tab2'] ="";
                $data['tab3'] ="";
                $data['tab4'] ="";
                $data['tab5'] ="active";
                $data['tab6'] ="";
                $data['tab7'] ="";
	$data['tab8'] ="";
            }
             else if($this->request->get['tab']=="6")
            {
                $data['tab1'] ="";
                $data['tab2'] ="";
                $data['tab3'] ="";
                $data['tab4'] ="";
                $data['tab5'] ="";
                $data['tab6'] ="active";
                $data['tab7'] ="";
	  $data['tab8'] ="";
            }
           else  if($this->request->get['tab']=="7")
            {
                $data['tab1'] ="";
                $data['tab2'] ="";
                $data['tab3'] ="";
                $data['tab4'] ="";
                $data['tab5'] ="";
                $data['tab6'] ="";
                $data['tab7'] ="active";
	  $data['tab8'] ="";
            }
        else  if($this->request->get['tab']=="8")
            {
                $data['tab1'] ="";
                $data['tab2'] ="";
                $data['tab3'] ="";
                $data['tab4'] ="";
                $data['tab5'] ="";
                $data['tab6'] ="";
                $data['tab7'] ="";
	  $data['tab8'] ="active";
            }
        else{
             $data['tab1'] ="active";
                $data['tab2'] ="";
                $data['tab3'] ="";
                $data['tab4'] ="";
                $data['tab5'] ="";
                $data['tab6'] ="";
                $data['tab7'] ="";
        }
        
        
        if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}

		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = 'null';
		}
                 if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = 'null';
		}
                if (isset($this->request->get['filter_name_id'])) {
			$filter_name_id = $this->request->get['filter_name_id'];
		} else {
			$filter_name_id = 'null';
		}
		
   if(1)//$this->request->get['filter_store']!="")
   {     

if($this->request->get['tab']=="3" || $this->request->get['tab']=="4"){
       
		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = null;
		}
          
}
  
		if (isset($this->request->get['page'])) {
	         $page = $this->request->get['page'];
		} else {
		 $page = 1;
		}
                          
		if (isset($this->request->get['page2'])) {
			$page2 = $this->request->get['page2'];
		} else {
		 $page2 = 1;
		}
		
		if (isset($this->request->get['page3'])) {
			$page3 = $this->request->get['page3'];
		} else {
		 $page3 = 1;
		}
		if (isset($this->request->get['page4'])) {
			$page4 = $this->request->get['page4'];
		} else {
		 $page4 = 1;
		}
		if (isset($this->request->get['page5'])) {
			$page5 = $this->request->get['page5'];
		} else {
		 $page5 = 1;
		}
		if (isset($this->request->get['page6'])) {
			$page6 = $this->request->get['page6'];
		} else {
		 $page6 = 1;
		}
                            if (isset($this->request->get['page7'])) {
			$page7 = $this->request->get['page7'];
		} else {
		 $page7 = 1;
		}
		 if (isset($this->request->get['page8'])) {
			$page8 = $this->request->get['page8'];
		} else {
		 $page8 = 1;
		}
		
		$this->load->model('customertrans/transation');
                            

		$data['orders'] = array();

		$filter_data = array(
                        'filter_store'	             => $filter_store,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_name'	             => $filter_name,
                        'filter_company'    => '2',
                                          'filter_name_id'	     => $filter_name_id,
			'start'                      => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                      => $this->config->get('config_limit_admin')
		);
		$filter_data2 = array(
         'filter_store'	             => $filter_store,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_name'	             => $filter_name,
                     'filter_company'    => '2',
                                          'filter_name_id'	     => $filter_name_id,
			'start'                      => ($page2 - 1) * $this->config->get('config_limit_admin'),
			'limit'                      => $this->config->get('config_limit_admin')
		);
		$filter_data3 = array(
                                         'filter_store'	             => $filter_store,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
                         'filter_company'    => '2',
			'start'                      => ($page3 - 1) * $this->config->get('config_limit_admin'),
			'limit'                      => $this->config->get('config_limit_admin')
		);
		$filter_data4 = array(
                                         'filter_store'	             => $filter_store,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
                         'filter_company'    => '2',
			'start'                      => ($page4 - 1) * $this->config->get('config_limit_admin'),
			'limit'                      => $this->config->get('config_limit_admin')
		);
		$filter_data5 = array(
                                         'filter_store'	             => $filter_store,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
                         'filter_company'    => '2',
			'start'                      => ($page5 - 1) * $this->config->get('config_limit_admin'),
			'limit'                      => $this->config->get('config_limit_admin')
		);
		$filter_data6 = array(
                                           'filter_store'	             => $filter_store,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
                         'filter_company'    => '2',
			'start'                      => ($page6 - 1) * $this->config->get('config_limit_admin'),
			'limit'                      => $this->config->get('config_limit_admin')
		);
		$filter_data7 = array(
         'filter_store'	             => $filter_store,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
                         'filter_company'    => '2',
			'start'                      => ($page7 - 1) * $this->config->get('config_limit_admin'),
			'limit'                      => $this->config->get('config_limit_admin')
		);
		$filter_data8 = array(
                                          'filter_store'	             => $filter_store,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
                         'filter_company'    => '2',
			'start'                      => ($page8 - 1) * $this->config->get('config_limit_admin'),
			'limit'                      => $this->config->get('config_limit_admin')
		);
             
                
/***********************************   Material received ***********************************************/ 
                
              // if($this->request->get['tab']=="1"){
                   
		  //$order_total = $this->model_customertrans_transation->getTotalOrdersReceived_comapnywise($filter_data);

		  $res = $this->model_customertrans_transation->getcustomerreward($filter_data);
                
                  $results=$res->rows;
                   $order_total = $res->num_rows;
                  
              
		foreach ($results as $result) { //print_r($result);
                       
			$data['customers'][] = array(
				'store_name'   => $result['store_name'],
                                 'store_id'   => $result['store_id'],
                            'inv_no'   => $result['inv_no'],
                            'customer_id'   => $result['customer_id'],
                            'type'   => $result['type'],
                            'points'   => $result['points'],
                            'date_added'   => date($this->language->get('date_format_short'),(($result['date_added']->sec)))
                        );     
		}

           // }
                  
/*********************************  end  Material received ****************************************/ 
                  
                //if($this->request->get['tab']=="2"){
             // $order_total2 = $this->model_customertrans_transation->getTotalOrders_companywise($filter_data2);

		$res = $this->model_customertrans_transation->getcustomerstore($filter_data2);
//print_r($customer2);
                 $customer2=$res->rows;
                   $order_total2 = $res->num_rows;
		foreach ($customer2 as $result) { //print_r($result);
                       
			$data['customers2'][] = array(
				'store_id'   => $result['store_id'],
				'customer_id'   => $result['customer_id'],
				'credit'     => $result['credit']
				
                        );
		}
              //  }
                
 /*****************************************SALE**********************************************/               
                //if($this->request->get['tab']=="3"){
                
                  
                   //$t3=$this->model_report_sale_summary->getTotalSale_category($filter_data3);
		
		   $data['customer3'] = array();

		   //$results3 = $this->model_report_sale_summary->getSale_summary_category($filter_data3);
                    $res = $this->model_customertrans_transation->getcustomertranstype($filter_data3);
                     $customer3 =$res->rows;
                   $order_total3= $res->num_rows;
                   //print_r($results);die;
                	
                       foreach ($customer3 as $result) {      //print_r($result);
			
			         $data['customers3'][] = array(
                             		'store_id' => $result['store_id'],
				'customer_id' => $result['customer_id'],
				'credit' => $result['credit'],
				'trans_type' => $result['trans_type'],
				'subsidy_order' => $result['Subsidy_order'],
				'create_time' => date($this->language->get('date_format_short'),(($result['create_time']->sec))),
				'cash'		=>$this->currency->format($result['Cash']),	
			);
		}
           // }
           //print_r($data['orders3']);

	         
                  //print_r($data['orders3']);
/********************************************END SALE*********************************************/                  
       
            
          
/***************************************************AMOUNT DEPOSIT********************************/
        
    //if($this->request->get['tab']=="4"){
        
           
	     $res= $this->model_customertrans_transation->getproductrewardstrans($filter_data4);
              $customer4 =$res->rows;
              $order_total4= $res->num_rows;
		foreach ($customer4 as $result) { //print_r($result);
			         $data['customers4'][] = array(
                                'store_id' => $result['store_id'],
				'product_name' => $result['product_name'],
				'old_points'   => $result['old_points'],
				'product_id'      => $result['product_id'],
				'customer_group_id'     => $result['customer_group_id'],
                                'valid_till'=>date($this->language->get('date_format_short'),(($result['valid_till']->sec))),  
				'start_date'=>date($this->language->get('date_format_short'),(($result['start_date']->sec))),
                                     'update_date'=> date($this->language->get('date_format_short'),(($result['update_date']->sec))),
				'points'      => $result['points'],
				
			);
		}
         
         
    //}
/******************************************END AMOUNT DEPOSIT*******************************************/


/*************************product sales count start here for tab 5 in result 4*******************************/
		$this->load->model('report/product_storewisesales');  

		$t5=$this->model_report_product_storewisesales->getTotalsalesCompanyWise($filter_data5);
		$order_total5 = $t5["total"];
		$data["total_amount_all_product_sales"]= $t5["total_amount"];
		$total_amount5=0;
		$results5 = $this->model_report_product_storewisesales->getSalesCompanyWise($filter_data5);

		foreach ($results5 as $result) { $total_amount5=$total_amount5+$result['total'];
			$data['orders5'][] = array(
				'name'       => $result['name'],
				'product_id'      => $result['product_id'],
                                'store_name'      => $result['store_name'],
				'quantity'   => $result['quantity'],
				
				'total'      => $this->currency->format($result['total'], $this->config->get('config_currency'))
			);
		}
$data["total_amount4"]=$total_amount4;
/*=========================Product sales end here for tab 5 in result4===================*/       

    
    
/***********************************current inventory************************************************/
              	//if($this->request->get['tab']=="6"){
    
                  
                //}
    /***********************************end current inventory************************************************/

/***************************************** DATE WISE SALE**********************************************/               
                //if($this->request->get['tab']=="7"){
                
           // }
                  
/********************************************END DATE WISE SALE*********************************************/         
/***************************************** STORE EXPENSE START**********************************************/               
                //if($this->request->get['tab']=="8"){
                
                  
           // }
                  
/********************************************STORE EXPENSE END*********************************************/  
$this->load->model('setting/store');       
		$url = '';
                            if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}
                           if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
     
		if (isset($this->request->get['page2'])) {
			$url .= '&page2=' . $this->request->get['page2'];
		}
		if (isset($this->request->get['page3'])) {
			$url .= '&page3=' . $this->request->get['page3'];
		}
		if (isset($this->request->get['page4'])) {
			$url .= '&page4=' . $this->request->get['page4'];
		}
		if (isset($this->request->get['page5'])) {
			$url .= '&page5=' . $this->request->get['page5'];
		}
		if (isset($this->request->get['page6'])) {
			$url .= '&page6=' . $this->request->get['page6'];
		}      
                            if (isset($this->request->get['page7'])) {
			$url .= '&page7=' . $this->request->get['page7'];
		}  
		 if (isset($this->request->get['page8'])) {
			$url .= '&page8=' . $this->request->get['page8'];
		}  
     
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('customertrans/transation', 'token=' . $this->session->data['token'] . $url . '&page={page}&tab=1', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
           
            
          
		$pagination2 = new Pagination();
		$pagination2->total = $order_total2;
		$pagination2->page = $page2;
		$pagination2->limit = $this->config->get('config_limit_admin');
		$pagination2->url = $this->url->link('customertrans/transation', 'token=' . $this->session->data['token'] . $url . '&page2={page}&tab=2', 'SSL');

		$data['pagination2'] = $pagination2->render(); 

		$data['results2'] = sprintf($this->language->get('text_pagination'), ($order_total2) ? (($page2 - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page2 - 1) * $this->config->get('config_limit_admin')) > ($order_total2 - $this->config->get('config_limit_admin'))) ? $order_total2 : ((($page2 - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total2, ceil($order_total2 / $this->config->get('config_limit_admin')));
            
            
           
		$pagination3 = new Pagination();
		$pagination3->total = $order_total3;
		$pagination3->page = $page3;
		$pagination3->limit = $this->config->get('config_limit_admin');
		$pagination3->url = $this->url->link('customertrans/transation', 'token=' . $this->session->data['token'] . $url . '&page3={page}&tab=3', 'SSL');

		$data['pagination3'] = $pagination3->render();

		$data['results3'] = sprintf($this->language->get('text_pagination'), ($order_total3) ? (($page3 - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page3 - 1) * $this->config->get('config_limit_admin')) > ($order_total3 - $this->config->get('config_limit_admin'))) ? $order_total3 : ((($page3 - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total3, ceil($order_total3 / $this->config->get('config_limit_admin')));
         
            
          
		$pagination4 = new Pagination();
		$pagination4->total = $order_total4;
		$pagination4->page = $page4;
		$pagination4->limit = $this->config->get('config_limit_admin');
		$pagination4->url = $this->url->link('customertrans/transation', 'token=' . $this->session->data['token'] . $url . '&page4={page}&tab=4', 'SSL');

		$data['pagination4'] = $pagination4->render();

		$data['results4'] = sprintf($this->language->get('text_pagination'), ($order_total4) ? (($page4 - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page4 - 1) * $this->config->get('config_limit_admin')) > ($order_total4 - $this->config->get('config_limit_admin'))) ? $order_total4 : ((($page4 - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total4, ceil($order_total4 / $this->config->get('config_limit_admin')));
         


            
		$pagination5 = new Pagination();
		$pagination5->total = $order_total5;
		$pagination5->page = $page5;
		$pagination5->limit = $this->config->get('config_limit_admin');
		$pagination5->url = $this->url->link('customertrans/transation', 'token=' . $this->session->data['token'] . $url . '&page5={page}&tab=5', 'SSL');

		$data['pagination5'] = $pagination5->render();

		$data['results5'] = sprintf($this->language->get('text_pagination'), ($order_total5) ? (($page5 - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page5 - 1) * $this->config->get('config_limit_admin')) > ($order_total5 - $this->config->get('config_limit_admin'))) ? $order_total5 : ((($page5 - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total5, ceil($order_total5 / $this->config->get('config_limit_admin')));

                            $order_total6=$order_total6['total'];                  

		$pagination6 = new Pagination();
		$pagination6->total = $order_total5;
		$pagination6->page = $page6;
		$pagination6->limit = $this->config->get('config_limit_admin');
		$pagination6->url = $this->url->link('customertrans/transation', 'token=' . $this->session->data['token'] . $url . '&page6={page}&tab=6', 'SSL');

		$data['pagination6'] = $pagination6->render();
                            //print_r($order_total6);print_r($filter_data);
		$data['results6'] = sprintf($this->language->get('text_pagination'), ($order_total6) ? (($page6 - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page6 - 1) * $this->config->get('config_limit_admin')) > ($order_total6 - $this->config->get('config_limit_admin'))) ? $order_total6 : ((($page6 - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total6, ceil($order_total6 / $this->config->get('config_limit_admin')));
       
            
           
		$pagination7 = new Pagination();
		$pagination7->total = $order_total7;
		$pagination7->page = $page7;
		$pagination7->limit = $this->config->get('config_limit_admin');
		$pagination7->url = $this->url->link('customertrans/transation', 'token=' . $this->session->data['token'] . $url . '&page7={page}&tab=7', 'SSL');

		$data['pagination7'] = $pagination7->render();

		$data['results7'] = sprintf($this->language->get('text_pagination'), ($order_total7) ? (($page7 - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page7 - 1) * $this->config->get('config_limit_admin')) > ($order_total7 - $this->config->get('config_limit_admin'))) ? $order_total7 : ((($page7 - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total7, ceil($order_total7 / $this->config->get('config_limit_admin')));
            
		$pagination8 = new Pagination();
		$pagination8->total = $order_total8;
		$pagination8->page = $page8;
		$pagination8->limit = $this->config->get('config_limit_admin');
		$pagination8->url = $this->url->link('customertrans/transation', 'token=' . $this->session->data['token'] . $url . '&page8={page}&tab=8', 'SSL');

		$data['pagination8'] = $pagination8->render();

		$data['results8'] = sprintf($this->language->get('text_pagination'), ($order_total8) ? (($page8 - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page8 - 1) * $this->config->get('config_limit_admin')) > ($order_total8 - $this->config->get('config_limit_admin'))) ? $order_total8 : ((($page8 - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total8, ceil($order_total8 / $this->config->get('config_limit_admin')));
            
}
     $this->load->model('setting/store'); 
		$data['stores'] = $this->model_setting_store->getStores();
      $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'Customer Transation',
			'href' => $this->url->link('customertrans/transation', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

                            $data['entry_date_start'] = 'Start date';
		$data['entry_date_end'] = 'End date';
		

		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];
		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_store'] = $filter_store;
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
          $this->response->setOutput($this->load->view('customertrans/transation_form.tpl', $data));

        

    }
    
    
    
    public function download_excel_customerrewards() {

          if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}

		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = 0;
		}


		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		

		$filter_data = array(
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_store'           => $filter_store
			
		);

     $this->load->model('customertrans/transation');
       

		$data['customers'] = array();

		$results = $this->model_customertrans_transation->getcustomerreward($filter_data);
                
		                
    include_once '../system/library/PHPExcel.php';
    include_once '../system/library/PHPExcel/IOFactory.php';
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->createSheet();
    
    $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");

    $objPHPExcel->setActiveSheetIndex(0);
    // Field names in the first row
    $fields = array(
         'Store Name',
         'Store Id',
         'Customer Id',      
         'Invoice Number',      
         'Transaction Type',
         'Point',
         'Date',
     
    ); 
    $col = 0;
    foreach ($fields as $field)
    {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
        $col++;
    }
     
    $row = 2; 
    foreach($results->rows as $data)
    {
        $col = 0;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $data['store_name']); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data['store_id']); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data['customer_id']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data['inv_no']);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data['type']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data['points']);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row,date('Y-m-d',($data['date_added']->sec)));
        
        
      
             //'Cash_subsidy'	=>$this->currency->format($result['Cash_subsidy']),
   
        $row++;
    }

     $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    
    // Sending headers to force the user to download the file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="customer'.date('dMy').'.xls"');
    header('Cache-Control: max-age=0');

    $objWriter->save('php://output');
        
    }
    
    
     public function download_customer_to_trans() {

          if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}

		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = 0;
		}


		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		

		$filter_data = array(
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_store'           => $filter_store
			
		);

     $this->load->model('customertrans/transation');
       

		$data['customers'] = array();

		$results = $this->model_customertrans_transation->getcustomertranstype($filter_data);
                
	             
    include_once '../system/library/PHPExcel.php';
    include_once '../system/library/PHPExcel/IOFactory.php';
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->createSheet();
    
    $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");

    $objPHPExcel->setActiveSheetIndex(0);
    // Field names in the first row
    $fields = array(
        
         'Store Id',
         'Customer Id',      
         'Credit',      
         'Cash',
         'Trans TYpe',
         'Date',
     
    ); 
    $col = 0;
    foreach ($fields as $field)
    {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
        $col++;
    }
     
    $row = 2; 
    foreach($results->rows as $data)
    {
        $col = 0;
       
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $data['store_id']); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data['customer_id']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data['credit']);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data['cash']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data['trans_type']);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, date('Y-m-d',$data['create_time']->sec));
        
        
      
             //'Cash_subsidy'	=>$this->currency->format($result['Cash_subsidy']),
   
        $row++;
    }

     $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    
    // Sending headers to force the user to download the file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="customer_trans_type'.date('dMy').'.xls"');
    header('Cache-Control: max-age=0');

    $objWriter->save('php://output');
        
    }
    
    
    
     public function download_customer_to_store() {

		
		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = 0;
		}


		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		

		$filter_data = array(
			
			'filter_store'           => $filter_store
			
		);

     $this->load->model('customertrans/transation');
       

		$data['customers'] = array();

		$results = $this->model_customertrans_transation->getcustomerstore($filter_data);
                
		                
    include_once '../system/library/PHPExcel.php';
    include_once '../system/library/PHPExcel/IOFactory.php';
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->createSheet();
    
    $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");

    $objPHPExcel->setActiveSheetIndex(0);
    // Field names in the first row
    $fields = array(
         
         'Store Id',
         'Customer Id',      
        
         'Credit'
        
     
    ); 
    $col = 0;
    foreach ($fields as $field)
    {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
        $col++;
    }
     
    $row = 2; 
    foreach($results->rows as $data)
    {
        $col = 0;
     
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $data['store_id']); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data['customer_id']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data['credit']);  
       //'Cash_subsidy'	=>$this->currency->format($result['Cash_subsidy']),
   
        $row++;
    }

     $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    
    // Sending headers to force the user to download the file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="customer_to_store'.date('dMy').'.xls"');
    header('Cache-Control: max-age=0');

    $objWriter->save('php://output');
        
    }
    
   
    public function download_product_rewards() {

		  if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}
		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = 0;
		}


		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		

		$filter_data = array(
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_store'           => $filter_store
			
		);

     $this->load->model('customertrans/transation');
       

		$data['customers'] = array();

		$results = $this->model_customertrans_transation->getproductrewardstrans($filter_data);
               // print_r($results);
		                
    include_once '../system/library/PHPExcel.php';
    include_once '../system/library/PHPExcel/IOFactory.php';
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->createSheet();
    
    $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");

    $objPHPExcel->setActiveSheetIndex(0);
    // Field names in the first row
    $fields = array(
             
        'Store Id', 	
		'Old Points', 	
		'Product Name', 	
		'Product Id', 	
		'Valid Till',
		'Start Date', 	
		'Update Date', 	
		'Points'
      
     
    ); 
    $col = 0;
    foreach ($fields as $field)
    {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
        $col++;
    }
     
    $row = 2; 
    foreach($results->rows as $data)
    {
        $col = 0;
     
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $data['store_id']); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data['old_points']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data['product_name']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data['product_id']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, date('Y-m-d',($data['valid_till']->sec)));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, date('Y-m-d',($data['start_date']->sec)));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, date('Y-m-d',($data['update_date']->sec)));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $data['points']);
       
       
       // $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data['date_added']);
        
        
      
             //'Cash_subsidy'	=>$this->currency->format($result['Cash_subsidy']),
   
        $row++;
    }

     $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    
    // Sending headers to force the user to download the file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="product_rewards'.date('dMy').'.xls"');
    header('Cache-Control: max-age=0');

    $objWriter->save('php://output');
        
    }
    
   
    
 
}



            