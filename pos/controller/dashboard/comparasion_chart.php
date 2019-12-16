<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class ControllerDashboardComparasionChart extends Controller {
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
            $this->adminmodel('report/sale');
            $results = $this->model_report_sale->getsaleorder($_SESSION['config_store_id']);
		$this->load->library('fusioncharts');
                
		$data=$results;//json_decode($data,true);
               
            // sorting the data
            //asort($data);

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
                   
                   /*array_push($dataseries2, array(
                       "value" => $dataset["totalorder"]
                   ));
				   */
               }

			   /*
			   $categoryArray=array();
				
				for ($i = 1; $i <= 12; $i++) 
				{
					$categoryArray[$i-1] = array(
						'label' => date('M', mktime(0, 0, 0, $i))
					);
				}
				print_r($categoryArray);    
				*/
               $arrData = array(
                   "chart" => array(
                           "caption"=> "",
                           "xAxisname"=>"Month",
                           "yAxisname"=>"Sale", 
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
               $arrData["dataset"] = array(array("seriesName"=> "Sale", "data"=>$dataseries1)); 
           
               return   $jsonEncodedData = json_encode($arrData);

            
           }
	}
	public function ordercount() {
            $this->adminmodel('report/sale');
            $results = $this->model_report_sale->getsaleorder($_SESSION['config_store_id']);
		$this->load->library('fusioncharts');
                
		$data=$results;//json_decode($data,true);
               
            // sorting the data
            //asort($data);

            if ($data) {
                
               $categoryArray=array();
           
               $dataseries1=array();
           
//               $dataseries2=array();

               foreach ($data as $dataset) { 

                   array_push($categoryArray, array(
                       "label" => $dataset["month"]
                   ));
                   
                   array_push($dataseries1, array(
                       "value" => $dataset["totalorder"]
                   ));
				   
               }

           
               $arrData = array(
                   "chart" => array(
                           "caption"=> "",
                           "xAxisname"=>"Month",
                           "yAxisname"=>"Order Count",
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
                    
               $arrData["categories"]=array(array("category"=>$categoryArray));
                       
               // creating dataset object//"renderAs"=>'line',
               //"renderAs"=>'line',"parentYAxis"=>'S',
               $arrData["dataset"] = array(array("seriesName"=> "Order Count", "data"=>$dataseries1)); 

               return   $jsonEncodedData = json_encode($arrData);

            
           }
	}
	public function category() {
           
		$filter_data = array(
                    'store_id' => $_SESSION['config_store_id'],
			'sort'  => 'o.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 5
		);
                
		$this->adminmodel('report/sale');
		$results = $this->model_report_sale->getTop_5_category($filter_data);
            
		$this->load->library('fusioncharts');
		$data=$results;//json_decode($data,true);
               
            asort($data);

            if ($data) {
                
               $categoryArray=array();
           
               $dataseries1=array();
           
               
                           
               foreach ($data as $dataset) 
			   { 
                   array_push($dataseries1, array(
                       "label" => $this->model_report_sale->get_category_name($dataset["type"]),
                       "value" => $dataset["total"]
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
	public function bar_chart()
	{
		$category = 8;
		$this->adminmodel('report/sale');
		$data['store_id']=$_SESSION['config_store_id'];
        	$results = $this->model_report_sale->getsaleorderTotal($data);
		$data=array();
		foreach($results as $result)
		{
			$data[]=$result['total'];
		}
		/*
        $data = array(
            164, 150, 132, 144, 125, 149, 145, 146,
            158, 140, 147, 136, 148, 152, 144, 168,
            126, 138, 176, 163, 119, 154, 165, 146,
            173, 142, 147, 135, 153, 140, 135, 161,
            145, 135, 142, 150, 156, 145, 128, 157
        );
		*/
        $min = min($data);
        $max = max($data);
        $limit = ceil(($max - $min) / $category);
        sort($data);
        
		
		if ($data) {
                
               $categoryArray=array();
           
               $dataseries1=array();
           
                     
				for ($i = 0; $i < $category; $i++) 
				{
					$count = 0;
					foreach ($data as $key => $number) 
					{
						if ($number <=  (($min + ($limit - 1)) + ($i * $limit))) 
						{
							$count++;
							unset($data[$key]);
						}
					}
					array_push($categoryArray, array(
                       "label" => ($min + ($i * $limit)) . '-' . (($min + ($limit - 1)) + ($i * $limit))
                   ));
                   
                   array_push($dataseries1, array(
                       "value" => $count
                   ));
					//echo ($min + ($i * $limit)) . '-' . (($min + ($limit - 1)) + ($i * $limit)) . ' => ' . $count.'<br>';
				}
				/*
               foreach ($data as $dataset) { 

                   array_push($categoryArray, array(
                       "label" => $dataset["month"]
                   ));
                   
                   array_push($dataseries1, array(
                       "value" => $dataset["totalorder"]
                   ));
				   
               }
           */
           
               $arrData = array(
                   "chart" => array(
                           "caption"=> "",
                           "xAxisname"=>"Order Total",
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
                       
               $arrData["categories"]=array(array("category"=>$categoryArray));
                       
               // creating dataset object//"renderAs"=>'line',
               //"renderAs"=>'line',"parentYAxis"=>'S',
               $arrData["dataset"] = array(array("seriesName"=> "Average Sales Ticket Size", "data"=>$dataseries1)); 
           
               return   $jsonEncodedData = json_encode($arrData);
		}
	}	
	
}
?>
