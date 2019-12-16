<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class ControllerDashboardTop5products extends Controller {
    public function adminmodel($model) 
        {
            $admin_dir = DIR_SYSTEM;
            $admin_dir = str_replace('system/','backoffice/',$admin_dir);
            $file = $admin_dir . 'model/' . $model . '.php';      
            $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
            if (file_exists($file)) 
            {
                include_once($file);
                $this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
            } 
            else 
            {
                trigger_error('Error: Could not load model ' . $model . '!');
                exit();               
            }
        }
		public function index() {
           
		$filter_data = array(
                    'store_id' => $_SESSION['config_store_id'],
			'sort'  => 'o.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 5
		);
                
		$this->adminmodel('report/sale');
		$results = $this->model_report_sale->getTop_5_Products($filter_data);
            
		$this->load->library('fusioncharts');
		$data=$results;//json_decode($data,true);
               
            asort($data);

            if ($data) {
                
               $categoryArray=array();
           
               $dataseries1=array();
           
               
                           
               foreach ($data as $dataset) { 
                   array_push($dataseries1, array(
                       "label" => $dataset["model"],
                       "value" => $dataset["sales_of_qnty"]
                   ));
                   
               }
           
           
               $arrData = array(
                   "chart" => array(
                           "caption"=> "",
                       'bgColor'=>'#fff',
                       'bgAlpha'=>'0',
        "showPercentInTooltip"=>'0',
        "showlegend"=>"1",
        "showpercentvalues"=> "1",
        "legendposition"=> "bottom",
        "usedataplotcolorforlabels"=> "1",	
        "theme"=> "fint",
		"pieRadius"=>100 
                   )
               );
                       
               $arrData["categories"]=array(array("category"=>$categoryArray));
                      //print_r($arrData);  
               // creating dataset object//"renderAs"=>'line',
               //"renderAs"=>'line',"parentYAxis"=>'S',
               $arrData["dataset"] = array(array("seriesName"=> "Sale", "data"=>$dataseries1)); 
           
               return   $jsonEncodedData = json_encode($arrData);

            
           }
	}
		/*
	public function index() {
           
            $filter_data = array(
			'sort'  => 'o.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 5
		);

		if($this->user->getGroupId()!="1")
                    {
		$filter_data = array(
                    'filter_user_id' => $this->user->getId(),
			'sort'  => 'o.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 5
		);
                }
		$this->adminmodel('sale/order');
		$results = $this->model_sale_order->getTop_5_Products($filter_data);
            
		$this->load->library('fusioncharts');
                
		$data=$results;//json_decode($data,true);
                
            asort($data);

            if ($data) {
                
               $categoryArray=array();
           
               $dataseries1=array();
           
               
                           
               foreach ($data as $dataset) { 

                   
                   array_push($dataseries1, array(
                       "label" => $dataset["model"],
                       "value" => $dataset["sales_of_qnty"]
                   ));
                   
               }
           
           
               $arrData = array(
                   "chart" => array(
                           "caption"=> "",
                       'bgColor'=>'#fff',
                       'bgAlpha'=>'0',
        "showPercentInTooltip"=>'0',
        "showlegend"=>"1",
        "showpercentvalues"=> "1",
        "legendposition"=> "bottom",
        "usedataplotcolorforlabels"=> "1",
        "theme"=> "fint"
                   )
               );
                       
               $arrData["categories"]=array(array("category"=>$categoryArray));
                   
               $arrData["dataset"] = array(array("seriesName"=> "Sale", "data"=>$dataseries1)); 
           
               return   $jsonEncodedData = json_encode($arrData);

            
           }
		
	}
	*/
	
}
?>
