<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class ControllerDashboardComparasionChart extends Controller {
	public function index() {
            $this->load->model('report/sale');
            $results = $this->model_report_sale->getsaleorder();
		$this->load->library('fusioncharts');
                
                //$data=file_get_contents('/var/www/html/stores/backoffice/controller/dashboard/data.json');//iterator_to_array($myObj);
		$data=$results;//json_decode($data,true);
                //print_r($results);
            // sorting the data
           // asort($data);

            if ($data) {
                
               $categoryArray=array();
           
               $dataseries1=array();
           
               $dataseries2=array();
                           
               foreach ($data as $dataset) { 

                   array_push($categoryArray, array(
                       "label" => $dataset["month"]
                   ));
                   
                   array_push($dataseries1, array(
                       "value" => $dataset["total"]
                   ));
                   
                   array_push($dataseries2, array(
                       "value" => $dataset["totalorder"]
                   ));
               }
           
           
               $arrData = array(
                   "chart" => array(
                           "caption"=> "Comparison of Order trend and Sales trend",
                           "xAxisname"=>"Month",
                           "yAxisname"=>"Sale",
                            "sYAxisname"=>"Count", 
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
                       
               $arrData["categories"]=array(array("category"=>$categoryArray));
                       
               // creating dataset object//"renderAs"=>'line',
               //"renderAs"=>'line',"parentYAxis"=>'S',
               $arrData["dataset"] = array(array("seriesName"=> "Sale", "data"=>$dataseries1), array("seriesName"=> "Order","renderAs"=>'line',"parentYAxis"=>'S',  "data"=>$dataseries2)); 
           
               return   $jsonEncodedData = json_encode($arrData);

            
           }
		//$data['token'] = $this->session->data['token'];

		//return $this->load->view('dashboard/chart.tpl', $data);
	}
	
}
?>
