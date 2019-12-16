<?php

class riprecharge {

   public $client_key="s924phas9zvofx76ich2iwc0nvlsnqds";
   public $user_id="10325";
   public $outlet_id="10019567";
 public function __construct($registry,$mobile,$order_id,$store_id,$products) {
        $this->mobile = $mobile;
        $this->order_id = $order_id;
        $this->store_id = $store_id;
        $this->products = $products;
	$this->config = $registry->get('config');
		$this->db = $registry->get('db');

    }


   public function adminmodel($model) 
   {
      
      $admin_dir = DIR_SYSTEM;
      $admin_dir = str_replace('system/','admin/',$admin_dir);
      $file = $admin_dir . 'model/' . $model . '.php';      
      //$file  = DIR_APPLICATION . 'model/' . $model . '.php';
      $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
      
      if (file_exists($file)) {
         include_once($file);
         
         $this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
      } else {
         trigger_error('Error: Could not load model ' . $model . '!');
         exit();               
      }
   }

public function recharge() {
                          
                            $log=new Log("recharge-func-".date('Y-m-d').".log");		
                            $log->write("in recharge");
   
	$mobile = $this->mobile;
        $order_id = $this->order_id;
        $store_id = $this->store_id;
        $prds = $this->products;		
                            
                         
		   $log->write($prds);

		$this->adminmodel('recharge/recharge');

		$recharge_amount=0;

                            $get_products=$this->model_recharge_recharge->get_master_products($store_id);

		foreach($get_products as $get_product)
                            {
                               $eligible_product[]=$get_product["product_id"];
                            }
                            

                            foreach($prds as $prd)
	              {	
		  $log->write($prd['product_id']);
		  $log->write($prd['product_quantity']);
                              $product_id=$prd['product_id'];
                              //$prd['product_quantity'];
                              if (in_array($prd['product_id'], $eligible_product))
		  {
		   
                                 $get_product_recharge_quantity=$this->model_recharge_recharge->get_product_recharge_quantity($product_id,$store_id,date('Y-m-d'));

	                   if(($prd['product_quantity']>=$get_product_recharge_quantity["product_quantity"]) && (count($get_product_recharge_quantity)>0)) /////////// quantity is greater then or equal eligible quantity
		     {
                                         $log->write( "quantity is greater then or equal eligible quantity so go to next");
                                         $get_recharge_scheme_status=$this->model_recharge_recharge->get_recharge_scheme_status($get_product_recharge_quantity["scheme_id"],$mobile);
                                         if(count($get_recharge_scheme_status)>0)
                                         {
                                           $log->write( "already get the recharge for this scheme : ".$get_product_recharge_quantity["scheme_name"]." for mobile : ".$mobile);
                                         }
                                         else
                                         {
                                         $log->write( "first recharge for this scheme so go to next");
                                         $recharge_amount=$get_product_recharge_quantity["recharge_amount"];
                                         $product_id=$prd['product_id'];
                                         $product_quantity=$prd['product_quantity'];
                                         $scheme_id=$get_product_recharge_quantity["scheme_id"];
                                         }
                                 }
                                 else//////quantity is less the eligible quantity
                                 {
                                     $log->write( "quantity is less then eligible quantity");
                                  }
		  }
                              else//////////this product is not register for the recharge in master table
                              {
                                
                                $log->write( "this product is not register for the recharge in master table");
                              }
		}

                           ////////////if all condition are true the send for recharge///////////////////////
	              $success_status=0;
		if($recharge_amount>0) //////////then call the recharge api
		{
                                 $opertor=$this->GetOperator($mobile);
                                 $opertor_json=json_decode($opertor);
                                 $op_error_code=$opertor_json->errorCode;
                                 if($op_error_code=="")
                                 {
					$op_error_code=$opertor_json->ErrorCode;
                                 }
		     $error_code=array(1,13);
                                 if(!in_array($op_error_code, $error_code))/////////////////if there is no error in getting the operator code
                                 {
                                   $operator_name=json_decode(@$opertor)[0]->operator_name;
                                   $operator_code=json_decode(@$opertor)[0]->operator_code;
                                   $log->write( $operator_code."-".$operator_name); 
                                 
                                  if($operator_code=="28") ///////////send to airtel prepaid
                                  {
                                          
                                          $Rec_Res=$this->PreRechargeAir($mobile,$operator_code,$recharge_amount);
			
			$RecRes=json_decode($Rec_Res);
			$ResErr=$RecRes->errorCode; //12 Try after 30 Mins
			$ResErrMsg=$RecRes->message;
			$ResAmt=$RecRes->charged_amount;
			$ResCom=$RecRes->commission;
			$ResDT=$RecRes->datetime; //Recharge Datetime
			$ResNum=$RecRes->msisdn; //Recharge Number
			$ResNwOc=$RecRes->operator_code; //Recharge Nw Opt Code
			$ResNw=$RecRes->operator_name; //Recharge Nw Opt Name
			$ResNwTransID=$RecRes->opr_transid; //Recharge Nw Opt TransID
			$ResRecCode=$RecRes->response_code; //Recharge Rocket Response Code
			$ResRocTransID=$RecRes->rocket_trans_id;//Recharge Rocket TransID
			$ResSevTyp=$RecRes->service_family; //Recharge Type Prepaid/Postpaid
			$ResSerSts=$RecRes->status;
			//Recharge Status Success Pending Review Refund Archived
			$ResTransAmt=$RecRes->trans_amount; //Recharge Request Amount
			$rocket_tbl_id=$this->model_recharge_recharge->insertIntoRechargeTransRocket($ResErr,$ResErrMsg,$ResAmt,$ResCom,$ResDT,$ResNum,$ResNwOc,$ResNw,$ResNwTransID,$ResRecCode,$ResRocTransID,$ResSevTyp,$ResSerSts,$ResTransAmt);
                                          if($ResErr==229)
			{
			   $success_status=2;
			}
                                  }
                                  
                                  else ///////////for all others////////////////
                                  { 
                                          $Rec_Res=$this->PreRecharge($mobile,$operator_code,$recharge_amount);
			//echo $Rec_Res=$this->test();exit;
                                          $RecRes=json_decode($Rec_Res);
			$ResErr=$RecRes->errorCode; //12 Try after 30 Mins
			$ResErrMsg=$RecRes->message;
			$ResAmt=$RecRes->charged_amount;
			$ResCom=$RecRes->commission;
			$ResDT=$RecRes->datetime; //Recharge Datetime
			$ResNum=$RecRes->msisdn; //Recharge Number
			$ResNwOc=$RecRes->operator_code; //Recharge Nw Opt Code
			$ResNw=$RecRes->operator_name; //Recharge Nw Opt Name
			$ResNwTransID=$RecRes->opr_transid; //Recharge Nw Opt TransID
			$ResRecCode=$RecRes->response_code; //Recharge Rocket Response Code
			$ResRocTransID=$RecRes->rocket_trans_id;//Recharge Rocket TransID
			$ResSevTyp=$RecRes->service_family; //Recharge Type Prepaid/Postpaid
			$ResSerSts=$RecRes->status;
			//Recharge Status Success Pending Review Refund Archived
			$ResTransAmt=$RecRes->trans_amount; //Recharge Request Amount
                                          $rocket_tbl_id=$this->model_recharge_recharge->insertIntoRechargeTransRocket($ResErr,$ResErrMsg,$ResAmt,$ResCom,$ResDT,$ResNum,$ResNwOc,$ResNw,$ResNwTransID,$ResRecCode,$ResRocTransID,$ResSevTyp,$ResSerSts,$ResTransAmt);
			if($ResErr==229)
			{
			   $success_status=2;
			}
                                  }
                                  $post_data=array(
                              	 'order_id'=>$order_id,
                               	 'store_id'=>$store_id,
                               	 'mobile'	     => $mobile,
                              	 'product_id' => $product_id,
                              	 'product_quantity' => $product_quantity,
                               	 'recharge_amount' => $recharge_amount,
			 'scheme_id' => $scheme_id,
                                           'status' => $success_status,
                                           'rocket_trans_id' => $ResRocTransID,
                                           'rocket_err_code' =>$ResErr,
                                           'rocket_tbl_id' => $rocket_tbl_id 
		      );
                                  $recharge_id=$this->model_recharge_recharge->insertIntoRechargeTrans($post_data,$operator_name,$operator_code);
                	      $log->write($recharge_id);
		      if($success_status!=2)///////////if status is pending or success then send sms
		      {
			  
                                            $log->write("if status is pending or success then send sms");
		                //send sms
                                           $this->load->library('sms');
                                           $sms=new sms($this->registry);
			 $sms->sendsms($mobile,"6",$prds);   
			 //exit;
                                            
			  echo $recharge_id;
		      }
		   }
                               else/////////////// when operator not found for the mobile
                               {
                                  $post_data=array(
                              	 'order_id'=>$order_id,
                               	 'store_id'=>$store_id,
                               	 'mobile'	     => $mobile,
                              	 'product_id' => $product_id,
                              	 'product_quantity' => $product_quantity,
                               	 'recharge_amount' => $recharge_amount,
			 'scheme_id' => $scheme_id,
                                           'status' => 2,
                                           'rocket_trans_id' => '',
                                           'rocket_err_code' =>$ResErr,
			 'rocket_tbl_id' => $rocket_tbl_id
		      );
                                  $recharge_id=$this->model_recharge_recharge->insertIntoRechargeTrans($post_data,$operator_name,$operator_code);
                	      $log->write($recharge_id);
                               }
                                    

                            }
		
			
                            
		

		//$this->response->setOutput(json_encode($recharge_id));
	}


function GetOperator($sender)
{
$log=new Log("recharge-".date('Y-m-d').".log");
$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Content-Type: application/json';

$surl="https://ws.rocketinpocket.com/recharge/v1/operators/mobile/+91".$sender."?client_id=".$this->user_id."&client_key=".$this->client_key;

$log->write($surl);

    $curl_handle=curl_init();
    curl_setopt($curl_handle,CURLOPT_URL,$surl);
    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($curl_handle,CURLOPT_HTTPHEADER,$headers);
  	$buffer = curl_exec($curl_handle);
	if($buffer === false)
	{
		return 'Curl error: ' . curl_error($curl_handle);
                            $log->write(curl_error($curl_handle));
	}
	else
	{
		$log->write($buffer);
		return $buffer;
                           
	}

curl_close($curl_handle);
}
// GetOperator($sender);
//************************************MSG  FUNCTION END*****************************************//
//****************************** REQUEST PREPAID RECHARGE FUNCTION *****************************//
function PreRecharge($sender,$code,$amt)
{

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Content-Type: application/json';
$surl="https://ws.rocketinpocket.com/recharge/v1/recharge/mobile?client_id=".$this->user_id."&client_key=".$this->client_key."&msisdn=".$sender."&operator_code=".$code."&amount=".$amt."&live=true";
$log=new Log("recharge-".date('Y-m-d').".log");
$log->write("Non-Airtel: ".$surl);

    $curl_handle=curl_init();
    curl_setopt($curl_handle,CURLOPT_URL,$surl);
	curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($curl_handle,CURLOPT_HTTPHEADER,$headers);
    $buffer = curl_exec($curl_handle);
	if($buffer === false)
	{
		echo 'Curl error: ' . curl_error($curl_handle);
                $log->write(curl_error($curl_handle));
	}
	else
	{
		
		return $buffer;
	}	
curl_close($curl_handle);

}
//*************************** REQUEST PREPAID RECHARGE FUNCTION END ***************************//
//*************************** REQUEST PREPAID RECHARGE FUNCTION AIR*****************************//
function PreRechargeAir($sender,$code,$amt)
{
echo "come";
$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Content-Type: application/json';
echo $surl="https://ws.rocketinpocket.com/recharge/v1/recharge/mobile?outlet_id=10019619&client_id=".$this->user_id."&client_key=".$this->client_key."&msisdn=".$sender."&operator_code=".$code."&amount=".$amt."&live=true";

$log=new Log("recharge-".date('Y-m-d').".log");
$log->write("Airtel: ".$surl);

    $curl_handle=curl_init();
    curl_setopt($curl_handle,CURLOPT_URL,$surl);
	curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($curl_handle,CURLOPT_HTTPHEADER,$headers);
    $buffer = curl_exec($curl_handle);
	if($buffer === false)
	{
		echo 'Curl error: ' . curl_error($curl_handle);
                $log->write(curl_error($curl_handle));
	}
	else
	{
		return $buffer;
	}	
curl_close($curl_handle);

}
//*************************** REQUEST PREPAID RECHARGE FUNCTION END ***************************//
//*************************** REQUEST POSTPAID RECHARGE FUNCTION ******************************//
function PostRecharge($sender,$code,$amt)
{
$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Content-Type: application/json';
$surl="https://ws.rocketinpocket.com/recharge/v1/recharge/mobile/bill?client_id=".$this->user_id."&client_key=".$this->client_key."&msisdn=".$sender."&operator_code=".$code."&amount=".$amt."&live=true";

$log=new Log("recharge-".date('Y-m-d').".log");
$log->write(curl_error($curl_handle));
    $curl_handle=curl_init();
    curl_setopt($curl_handle,CURLOPT_URL,$surl);
	curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($curl_handle,CURLOPT_HTTPHEADER,$headers);
    $buffer = curl_exec($curl_handle);
	if($buffer === false)
	{
		echo 'Curl error: ' . curl_error($curl_handle);
	}
	else
	{
		return $buffer;
	}	
curl_close($curl_handle);

}
//*************************** REQUEST POSTPAID RECHARGE FUNCTION END ***************************//


             
}
?>