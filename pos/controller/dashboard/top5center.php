<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class ControllerDashboardTop5center extends Controller {
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
		$this->load->model('sale/order');
		$results = $this->model_sale_order->getTop_5_Products($filter_data);
            
		$this->load->library('fusioncharts');
                
                //$data=file_get_contents('/var/www/html/stores/backoffice/controller/dashboard/data.json');//iterator_to_array($myObj);
		$data=$results;//json_decode($data,true);
                //print_r($results);
            // sorting the data
            asort($data);

            if ($data) {
                
               $categoryArray=array();
           
               $dataseries1=array();
           
               
                           
               foreach ($data as $dataset) { 

                   /*array_push($dataseries1, array(
                       "label" => $dataset["model"]
                   ));*/
                   
                   array_push($dataseries1, array(
                       "label" => $dataset["model"],
                       "value" => $dataset["sales_of_qnty"]
                   ));
                   
               }
           
           
               $arrData = array(
                   "chart" => array(
                           "caption"=> "Top 5 Center",
        "plottooltext"=>
          "<b>$percentValue</b> of web servers run on $label servers",
        "showlegend"=>"1",
        "showpercentvalues"=> "1",
        "legendposition"=> "bottom",
        "usedataplotcolorforlabels"=> "1",
        "theme"=> "fusion"
                   )
               );
                       
               $arrData["categories"]=array(array("category"=>$categoryArray));
                      //print_r($arrData);  
               // creating dataset object//"renderAs"=>'line',
               //"renderAs"=>'line',"parentYAxis"=>'S',
               $arrData["dataset"] = array(array("seriesName"=> "Sale", "data"=>$dataseries1)); 
           
               return   $jsonEncodedData = json_encode($arrData);

            
           }
		//$data['token'] = $this->session->data['token'];

		//return $this->load->view('dashboard/chart.tpl', $data);
	}
	
}
?>
