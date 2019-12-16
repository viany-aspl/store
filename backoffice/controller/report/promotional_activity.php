<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');

//ini_set('max_execution_time', 600); //600 seconds = 10 minutes
ini_set('memory_limit','1024M');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ControllerReportPromotionalActivity extends Controller 
{
	public function index() 
	{
		$this->load->language('report/product_sale');

		$this->document->setTitle($this->language->get('Promotional Activity'));

		if (isset($this->request->get['filter_date_start'])) 
		{
			$filter_date_start = $this->request->get['filter_date_start'];
		} 
		else 
		{
			$filter_date_start = date('Y-m-d');
		}

		if (isset($this->request->get['filter_date_end'])) 
		{
			$filter_date_end = $this->request->get['filter_date_end'];
		} 
		else 
		{
			$filter_date_end = date('Y-m-d');
		}
        if (isset($this->request->get['filter_mobile'])) 
		{
			$filter_mobile = $this->request->get['filter_mobile'];
		} 
		if (isset($this->request->get['filter_name'])) 
		{
			$filter_name = $this->request->get['filter_name'];
		} 
		
		if (isset($this->request->get['filter_company'])) 
		{
			$filter_company = $this->request->get['filter_company'];
		} 
		
       
		if (isset($this->request->get['page'])) 
		{
			$page = $this->request->get['page'];
		} 
		else 
		{
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_name'])) 
		{
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}
		if (isset($this->request->get['filter_mobile'])) 
		{
			$url .= '&filter_mobile=' . $this->request->get['filter_mobile'];
		}
		
       if (isset($this->request->get['filter_company'])) 
		{
			$url .= '&filter_company=' . $this->request->get['filter_company'];
		}
		


		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('Promotional Activity'),
			'href' => $this->url->link('report/promotional_activity', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$this->load->model('report/promotional_activity');
                        $this->load->model('setting/store');
		$data['orders'] = array();

		$filter_data = array(
            'filter_mobile'	     => $filter_mobile,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_name'           => $filter_name,
			'filter_company'           => $filter_company,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$order_total = $t1["total"];
		
		$results = $this->model_report_promotional_activity->getpromotionalactivity($filter_data);
        $order_total = $results->num_rows;                   
		//print_r($results); 

		
			foreach($results->rows  as $result)
			{
				///print_r($result);
				
				$data['activities'][] = array(
				'dats' => date('Y-m-d', ($result['date']->sec)),
					'_id'          =>$result['_id'],
				'auto_activity_id'          =>$result['auto_activity_id'],
				'lat'    => ($result['lat']),
				'long'    => ($result['long']),
				'activity_id'    => ($result['activity_id']),
                'store_id'    => $result['store_id'],
				'store_name'   => $result['store_name'],
				'activity_name'     => $result['activity_name'],
				'company_name'         => $result['company_name'],
				'retailer_name'    => $result['retailer_name'],
				'representative_name'          => $result["representative_name"],
			    'representative_mobile'          => $result["representative_mobile"],
				 'images'          => $result["images"],
				
			    
				
			);
			}
		
 
		$data['heading_title'] = $this->language->get('heading_title');
		$this->load->language('common/information');
		$data['tool_tip']=$this->language->get('report/product_sales');

		$data['tool_tip_style']=$this->language->get('tool_tip_style');
		$data['tool_tip_class']=$this->language->get('tool_tip_class');
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_all_status'] = $this->language->get('text_all_status');

		$data['column_date_start'] = $this->language->get('column_date_start');
		$data['column_date_end'] = $this->language->get('column_date_end');
		$data['column_orders'] = $this->language->get('column_orders');
		$data['column_products'] = $this->language->get('column_products');
		$data['column_tax'] = $this->language->get('column_tax');
		$data['column_total'] = $this->language->get('column_total');

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_group'] = $this->language->get('entry_group');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		//echo $url;
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/product_sales', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_name'] = $filter_name;
		$data['filter_company'] = $filter_company;
		$data['filter_mobile'] = $filter_mobile;
		$data['filter_store'] = $filter_store;
        $data['filter_name_id'] = $filter_name_id;
		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();
		$data['getactivities'] = $this->model_report_promotional_activity->getActivity();
		$data['companies'] = $this->model_report_promotional_activity->getCompany();
		//print_r($data['activities'] );
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/promotional_activity.tpl', $data));
	}
	
	
	
	public function promotional_activity_graph() 
	{
		$this->load->language('report/product_sale');

		$this->document->setTitle($this->language->get('Promotional Activity Graph'));

		if (isset($this->request->get['filter_date_start'])) 
		{
			$filter_date_start = $this->request->get['filter_date_start'];
		} 
		else 
		{
			$filter_date_start = date('Y-m-d');
		}

		if (isset($this->request->get['filter_date_end'])) 
		{
			$filter_date_end = $this->request->get['filter_date_end'];
		} 
		else 
		{
			$filter_date_end = date('Y-m-d');
		}
        if (isset($this->request->get['filter_mobile'])) 
		{
			$filter_mobile = $this->request->get['filter_mobile'];
		} 
		if (isset($this->request->get['filter_name'])) 
		{
			$filter_name = $this->request->get['filter_name'];
		} 
		
       if (isset($this->request->get['filter_company'])) 
		{
			$filter_company = $this->request->get['filter_company'];
		} 
		if (isset($this->request->get['page'])) 
		{
			$page = $this->request->get['page'];
		} 
		else 
		{
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_name'])) 
		{
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}
		if (isset($this->request->get['filter_mobile'])) 
		{
			$url .= '&filter_mobile=' . $this->request->get['filter_mobile'];
		}
		
		if (isset($this->request->get['filter_company'])) 
		{
			$url .= '&filter_company=' . $this->request->get['filter_company'];
		}
		
       


		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('Promotional Activity Graph'),
			'href' => $this->url->link('report/promotional_activity', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$this->load->model('report/promotional_activity');
                        $this->load->model('setting/store');
		$data['orders'] = array();

		$filter_data = array(
            'filter_mobile'	     => $filter_mobile,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_name'           => $filter_name,
			'filter_company'           => $filter_company,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$order_total = $t1["total"];
		
		$results = $this->model_report_promotional_activity->getpromotionalactivity($filter_data);
        $order_total = $results->num_rows;                   
		//print_r($results); 

		
			foreach($results->rows  as $result)
			{
				//print_r($result['date']);
				
				$data['activities'][] = array(
				'dats' => $result['date'],
					'_id'          =>$result['_id'],
				'auto_activity_id'          =>$result['auto_activity_id'],
				'lat'    => ($result['lat']),
				'long'    => ($result['long']),
				'activity_id'    => ($result['activity_id']),
                'store_id'    => $result['store_id'],
				'store_name'   => $result['store_name'],
				'activity_name'     => $result['activity_name'],
				'company_name'         => $result['company_name'],
				'retailer_name'    => $result['retailer_name'],
				'representative_name'          => $result["representative_name"],
			    'representative_mobile'          => $result["representative_mobile"],
				 'images'          => $result["images"],
				
			    
				
			);
			}
		
 
		$data['heading_title'] = $this->language->get('heading_title');
		$this->load->language('common/information');
		$data['tool_tip']=$this->language->get('report/product_sales');

		$data['tool_tip_style']=$this->language->get('tool_tip_style');
		$data['tool_tip_class']=$this->language->get('tool_tip_class');
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_all_status'] = $this->language->get('text_all_status');

		$data['column_date_start'] = $this->language->get('column_date_start');
		$data['column_date_end'] = $this->language->get('column_date_end');
		$data['column_orders'] = $this->language->get('column_orders');
		$data['column_products'] = $this->language->get('column_products');
		$data['column_tax'] = $this->language->get('column_tax');
		$data['column_total'] = $this->language->get('column_total');

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_group'] = $this->language->get('entry_group');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		//echo $url;
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/product_sales', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_name'] = $filter_name;
		$data['filter_company'] = $filter_company;
        $data['filter_name_id'] = $filter_name_id;
		$data['companies'] = $this->model_report_promotional_activity->getCompany();
		$data['promationchart'] = $this->load->controller('report/promotional_activity/graph');
		$data['getactivities'] = $this->model_report_promotional_activity->getActivity();
		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/promotional_activity_graph.tpl', $data));
	}
	
	public function graph() 
	{
		if (isset($this->request->get['filter_date_start'])) 
		{
			$filter_date_start = $this->request->get['filter_date_start'];
		} 
		else 
		{
			$filter_date_start = date('Y-m-d');
		}

		if (isset($this->request->get['filter_date_end'])) 
		{
			$filter_date_end = $this->request->get['filter_date_end'];
		} 
		else 
		{
			$filter_date_end = date('Y-m-d');
		}
        if (isset($this->request->get['filter_mobile'])) 
		{
			$filter_mobile = $this->request->get['filter_mobile'];
		} 
		if (isset($this->request->get['filter_name'])) 
		{
			$filter_name = $this->request->get['filter_name'];
		} 
		 if (isset($this->request->get['filter_company'])) 
		{
			$filter_company = $this->request->get['filter_company'];
		} 
       
		if (isset($this->request->get['page'])) 
		{
			$page = $this->request->get['page'];
		} 
		else 
		{
			$page = 1;
		}
		
		
				$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_name'])) 
		{
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}
		if (isset($this->request->get['filter_mobile'])) 
		{
			$url .= '&filter_mobile=' . $this->request->get['filter_mobile'];
		}
		
		if (isset($this->request->get['filter_company'])) 
		{
			$url .= '&filter_company=' . $this->request->get['filter_company'];
		}
		
       


		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		
		$filter_data = array(
            'filter_mobile'	     => $filter_mobile,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_name'           => $filter_name,
			'filter_company'           => $filter_company,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);
        $this->load->model('report/promotional_activity');
		$results = $this->model_report_promotional_activity->getcountforchart($filter_data);
		
        
		$this->load->library('fusioncharts');
         
		 
                //$data=file_get_contents('/var/www/html/stores/backoffice/controller/dashboard/data.json');//iterator_to_array($myObj);
		$data=$results;//json_decode($data,true);
        

            if ($data) 
			{
                
               //$categoryArray=array();
           
               $dataseries1=array();
           
               //$dataseries2=array();
                           
               foreach ($data as $dataset) 
			   { 
                   
                   array_push($dataseries1, array(
				       "label" => $dataset["activityname"],
                       "value" => $dataset["total"]
                   ));
                   
                   
               }
           
           
               $arrData = array(
                   "chart" => array(
                           "caption"=> "Activity Chart",
                           "xAxisname"=>"Activity",
                           "yAxisname"=>"Count",
                            "sYAxisname"=>"", 
                           "numberPrefix"=>"",
                           "paletteColors"=> "#876EA1, #72D7B2",
                           "useplotgradientcolor"=> "0",
                           "plotBorderAlpha"=> "0",
                           "bgColor"=> "#FFFFFFF",
                           "canvasBgColor"=> "#FFFFFF",
                           "showCanvasBorder"=> "0",
                           "showBorder"=> "0",
                           "divLineAlpha"=> "40",
                           "divLineColor"=> "#DCDCDC",
                           "alternateHGridColor"=> "#DCDCDC",
                           "alternateHGridAlpha"=> "15",
                           "showValues"=> "0",
                           "labelDisplay"=> "auto",
                           "baseFont"=> "Assistant",
                           "baseFontColor"=> "#000000",
                           "outCnvBaseFont"=> "Assistant",
                           "outCnvBaseFontColor"=> "#000000",
                           "baseFontSize"=> "13",
                           "outCnvBaseFontSize"=> "13",
                           "labelFontColor"=> "#000000",
                           "captionFontColor"=> "#153957",
                           "captionFontBold"=> "1",
                           "captionFontSize"=> "20",
                           "subCaptionFontColor"=> "#153957",
                           "subCaptionfontSize"=> "17",
                           "subCaptionFontBold"=> "0",
                           "captionPadding"=> "20",
                           "valueFontBold"=> "0",
                           "showAxisLines"=> "1",
                           "yAxisLineColor"=> "#DCDCDC",
                           "xAxisLineColor"=> "#DCDCDC",
                           "xAxisLineAlpha"=> "15",
                           "yAxisLineAlpha"=> "15",
                           "toolTipPadding"=> "7",
                           "toolTipBorderColor"=> "#DCDCDC",
                           "toolTipBorderThickness"=> "0",
                           "toolTipBorderRadius"=> "2",
                           "showShadow"=> "0",
                           "toolTipBgColor"=> "#153957",
                           "toolTipBgAlpha"=> "90",
                           "toolTipColor"=> "#FFFFFF",
                           "legendBorderAlpha"=> "0",
                           "legendShadow"=> "0",
                           "legendItemFontSize"=> "14"
                   )
               );
                       
               //$arrData["categories"]=array(array("category"=>$categoryArray));
                 
               $arrData["dataset"] = array(array("seriesName"=> "Count", "data"=>$dataseries1));//, array("seriesName"=> "Order","renderAs"=>'line',"parentYAxis"=>'S',  "data"=>$dataseries2)); 
           
               return   $jsonEncodedData = json_encode($arrData);

            
           }
		
	}
	
	
	
    public function download_excel() 
	{
       if (isset($this->request->get['filter_date_start'])) 
		{
			$filter_date_start = $this->request->get['filter_date_start'];
		} 
		else 
		{
			$filter_date_start = date('Y-m-d');
		}

		if (isset($this->request->get['filter_date_end'])) 
		{
			$filter_date_end = $this->request->get['filter_date_end'];
		} 
		else 
		{
			$filter_date_end = date('Y-m-d');
		}
        if (isset($this->request->get['filter_mobile'])) 
		{
			$filter_mobile = $this->request->get['filter_mobile'];
		} 
		if (isset($this->request->get['filter_name'])) 
		{
			$filter_name = $this->request->get['filter_name'];
		} 
		

		$this->load->model('report/promotional_activity');
                
		$data['orders'] = array();

		$filter_data = array(
                        'filter_mobile'	     => $filter_mobile,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_name'           => $filter_name
			
		);

		
		$file_name="promotional_activity_report".date('dMy').'.xls';
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=".$file_name);  //File name extension was wrong
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);

        $results = $this->model_report_promotional_activity->getpromotionalactivity($filter_data);
        echo '<table id="example2" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
					<th>ActivityID</th>
                    <th>Activity</th>
                    <th>Activity Date</th>
                    
                    <th>Representative Name</th>
                    <th>Representative Mobile</th>
					<th>Company Name</th>
					<th>Store Name</th>
					
                </tr>
                </thead>
                <tbody>';
		$tblbody=" ";
		foreach($results->rows as $data1)
		{ 	
			
				
			
			//echo $price_without_tax;
			echo  '<tr> 
					<td>'.$data1['activity_id'].'</td>
                    <td>'.$data1['activity_name'].'</td>
                    <td>'.date('Y-m-d',($data1['date']->sec)).'</td>
                    <td>'.$data1['representative_name'].'</td>
					<td>'.$data1['representative_mobile'].'</td>
                   
				
                    <td>'.$data1['company_name'].'</td>
					<td>'.$data1['store_name'].'</td>
					
                   </tr>';

			}
		
		echo '</tbody>
        </table>';
		exit;
          
        
    }
	

}