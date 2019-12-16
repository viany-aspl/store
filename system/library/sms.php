<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sms
 *
 * @author agent
 */
class sms {
public function __construct($registry) {
                $this->config = $registry->get('config');
		$this->db = $registry->get('db');
                //$this->request = $registry->get('request');
		//$this->session = $registry->get('session');
}

public function sendsms($mobile,$message,$customer_info)
{
    $response ="";
	
    if(isset($mobile)&&isset($message))
    {
		$log=new Log("smshit-".date('Y-m-d').".log");
		
		$log->write($message);
		$log->write($customer_info);
                	$api_info=array();
					
				$api_info["User"]=SMS_USERNAME;
                	$api_info["passwd"]=SMS_PASSWORD;
                	$api_info["sid"]=SMS_DISPLAYNAME; 
			$api_info["mobilenumber"]=$mobile;
		$api_info["priority"]="1";
		$api_info["dnd"]="1";
		$api_info["unicode"]="0";
                
		if($message=='2')
		{
			$api_info["unicode"]="1";
			$api_info["DR"]="Y";
			$api_info["Mtype"]="OL";
			$api_info["message"]=str_replace('#',($customer_info['store_name']),($this->getsms($message)["MESSAGE"])); 
			$api_info["message"]=str_replace('*',number_format((float)$customer_info['total'], 0, '.', ''),($api_info["message"])); 
			$api_info["message"]=str_replace('$',($customer_info['store_telephone']),($api_info["message"])); 
			$api_info["message"]= str_replace('%u', '',$this->utf8_to_unicode($api_info["message"]));
			
			
		}
		else if($message=='1')
		{
			$api_info["message"]=str_replace('*',($customer_info['card']),($this->getsms($message)["MESSAGE"]));
			
		}
		else if($message=='5')
		{
			$api_info["message"]=str_replace('*',($customer_info['ttp']),($this->getsms($message)["MESSAGE"]));
			$api_info["message"]=str_replace('@',($customer_info['rqid']),$api_info["message"]);			
		}
		else if($message=='4')
		{
			$api_info["message"]=str_replace('*',($customer_info['pass']),($this->getsms($message)["MESSAGE"]));
			
		}
		else if($message=='6')
		{
			//sms recharge
			$api_info["unicode"]="1";
			$api_info["DR"]="Y";
			$api_info["Mtype"]="OL";
			$api_info["message"]= str_replace('%u', '',$this->utf8_to_unicode($this->getsms($message)["MESSAGE"]));	

		}
                else if($message=='7')
		{
			$api_info["message"]=str_replace('*',($customer_info['otp']),($this->getsms($message)["MESSAGE"]));
			
		}
		else if($message=='25')
		{
			$api_info["message"]=str_replace('*',($customer_info['name']),($this->getsms($message)["MESSAGE"]));
			$api_info["message"]=str_replace('#',($customer_info['ticketid']),$api_info["message"]);		
			$api_info["message"]=str_replace('@',($customer_info['category_name']),$api_info["message"]);
		}
		else if($message=='26')
		{
			$api_info["message"]=str_replace('*',($customer_info['name']),($this->getsms($message)["MESSAGE"]));
			$api_info["message"]=str_replace('#',($customer_info['ticketid']),$api_info["message"]);			
		}
		else if($message=='27')
		{
			$api_info["message"]=str_replace('*',($customer_info['name']),($this->getsms($message)["MESSAGE"]));
			$api_info["message"]=str_replace('#',($customer_info['ticketid']),$api_info["message"]);		
			$api_info["message"]=str_replace('@',($customer_info['category_name']),$api_info["message"]);
		}
		else if($message=='28')
		{
			$api_info["message"]=str_replace('*',($customer_info['name']),($this->getsms($message)["MESSAGE"]));
			$api_info["message"]=str_replace('#',($customer_info['ticketid']),$api_info["message"]);		
			$api_info["message"]=str_replace('@',($customer_info['category_name']),$api_info["message"]);
		}
		else if($message=='29')
		{
			$api_info["message"]=str_replace('*',($customer_info['store_owner_name']),($this->getsms($message)["MESSAGE"]));
			$api_info["message"]=str_replace('#',($customer_info['full_name']),$api_info["message"]);	
			$api_info["message"]=str_replace('&',($customer_info['mobile_number']),$api_info["message"]);				
			$api_info["message"]=str_replace('@',($customer_info['product_name']),$api_info["message"]);
		}
		else if($message=='30')
		{
			$api_info["message"]=str_replace('*',($customer_info['firstname']),($this->getsms($message)["MESSAGE"]));
			
		}
		else if($message=='32')
		{
			//sms recharge
			$api_info["unicode"]="1";
			$api_info["DR"]="Y";
			$api_info["Mtype"]="OL";
			$api_info["message"]=str_replace('*',($customer_info['otp']),($this->getsms($message)["MESSAGE"]));
			$api_info["message"]= str_replace('%u', '',$this->utf8_to_unicode($api_info["message"]));
			

		}
		else if($message=='33')
		{
			//sms recharge
			$api_info["unicode"]="1";
			$api_info["DR"]="Y";
			$api_info["Mtype"]="OL";
			$api_info["message"]=str_replace('*',($customer_info['otp']),($this->getsms($message)["MESSAGE"]));
			$api_info["message"]= str_replace('%u', '',$this->utf8_to_unicode($api_info["message"]));	
			
		}
		else if($message=='34')
		{
			//sms recharge
			$api_info["unicode"]="1";
			$api_info["DR"]="Y";
			$api_info["Mtype"]="OL";
			
			$api_info["message"]= str_replace('%u', '',$this->utf8_to_unicode($this->getsms($message)["MESSAGE"]));	
			
		}
		else if($message=='35')
		{
			$api_info["message"]=str_replace('*',($customer_info['order_id']),($this->getsms($message)["MESSAGE"]));
			$api_info["message"]=str_replace('@',($customer_info['otp']),($api_info["message"]));
		}
		else if($message=='36')
		{
			//sms recharge
			$api_info["unicode"]="1";
			$api_info["DR"]="Y";
			$api_info["Mtype"]="OL";
			
			$api_info["message"]=str_replace('*',($customer_info['otp']),($this->getsms($message)["MESSAGE"]));
			$api_info["message"]= str_replace('%u', '',$this->utf8_to_unicode($api_info["message"]));		
			
		}
		else if($message=='37')
		{
			//sms recharge
			$api_info["unicode"]="1";
			$api_info["DR"]="Y";
			$api_info["Mtype"]="OL";
			$api_info["message"]=str_replace('*',($customer_info['store_name']),($this->getsms($message)["MESSAGE"]));
			$api_info["message"]=str_replace('@',($customer_info['credit']),$api_info["message"]);
			$api_info["message"]=str_replace('#',($customer_info['store_name']),$api_info["message"]);
			$api_info["message"]= str_replace('%u', '',$this->utf8_to_unicode($api_info["message"]));	

		}
		else if($message=='38')
		{
			$api_info["message"]=str_replace('*',($customer_info['store_incharge_name']),($this->getsms($message)["MESSAGE"]));
			$api_info["message"]=str_replace('#',($customer_info['otp']),($api_info["message"]));
		}
		else if($message=='39')
		{
			$api_info["message"]=str_replace('*',($customer_info['store_incharge_name']),($this->getsms($message)["MESSAGE"]));
			
		}
		else
		{
                                          $api_info["message"]=$this->getsms($message)["MESSAGE"];
		}

	  	 $log->write($api_info);
		 /*
                	 $curl = curl_init();
	               // Set SSL if required
              	 if (substr(SMS_HOSTNAME, 0, 5) == 'https') {
	               curl_setopt($curl, CURLOPT_PORT, 443);
                }
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST,'GET');
                curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                //curl_setopt($curl, CURLOPT_URL, SMS_HOSTNAME );
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));
                   $log->write(trim(SMS_HOSTNAME)."?".http_build_query($api_info));
                 curl_setopt($curl, CURLOPT_URL, trim(SMS_HOSTNAME)."?".http_build_query($api_info) );
                $json = curl_exec($curl);       
                $log->write($json);
                $response = json_decode($json, true);
                if ($mobile) {
				

				
			} 
                $this->smsinsert($api_info,$response);
				*/
				$log->write('before curl start');
				$curl = curl_init();
                // Set SSL if required
                if (substr(SMS_HOSTNAME, 0, 5) == 'https') {
                    curl_setopt($curl, CURLOPT_PORT, 443);
                }
	  
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLINFO_HEADER_OUT, true);
				//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, trim($operator_info['QUERY_TYPE']) );
                curl_setopt($curl, CURLOPT_USERAGENT, "unnati");
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
				curl_setopt($curl, CURLOPT_URL, trim(SMS_HOSTNAME));
			
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));
				////
				$json=0;
				if(!empty($mobile))
				{
					$json = curl_exec($curl);
					$log->write('response from curl');
					$log->write($json);
					$first_step = explode( '<html' , $json ); 
					if(sizeof($first_step)>0)
					{
						$log->write($first_step[0]); 
						$json=	$first_step[0];
					}                 
					$response = str_replace(".","",$json);//json_decode($json, true);
					$log->write($json);
					$log->write($response);
					if ($mobile) 
					{
				
					} 
                        
				}		
				//$mobile,$message,$customer_info
				$this->smsinsert($api_info,$response,$customer_info); 
                curl_close($curl);
    }
    return $response;
}
public function smsinsert($data,$api_response,$customer_info)
        {
           
            $input_array=array('mobile_number' =>$data["mobilenumber"],'message'=>$data["message"],'api_response'=>$api_response,'customer_info'=>$customer_info,'create_time'=>date('Y-m-d h:i:s'));
            $this->db->query('insert','oc_sms_trans',$input_array);
			if(strcmp(strtolower($api_response),strtolower('ERROR:Insufficient Credits'))==0)
			{
				$log=new Log("sms-alert-".date('y-m-d').".log");
				
				$log->write('in if '); 
				$log->write($api_response); 
				$this->sms_bal_alert(); 
			}
           
           
        }
private function sms_bal_alert() 
	{
		$log=new Log("sms-alert-".date('y-m-d').".log");
		$log->write("In sms mail");   
		$mail  = new PHPMailer();
		$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear All,
			<br/><br/>
			We have insufficient balance in SMS Account (Unnati-Open Retailer Account).
			
			<br/><br/>
			This is computer generated alert.Please do not reply to this email.
			<br/><br/>
			<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/><br/>
			Thanking you,
			<br/>
			IT Team
			<br/>
			Unnati
			
			<br/>
			<br/>
			<span  style='font-size:7.5pt;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
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

        $mail->Subject    ='SMS Account Balance Alert';

        $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		$mail->MsgHTML($body);
                
		$mail->AddAddress('vipin.kumar@aspltech.com', "Pragya Singh");
		//$mail->AddCC('ashok.prasad@akshamaala.com', "Ashok Prasad");
		//$mail->AddCC('amit.s@akshamaala.com', 'Amit Sinha');
		//$mail->AddCC('subhash.jha@unnati.world', "Subhash Jha");
		$mail->AddCC('hrishabh.gupta@unnati.world', "Hrishabh");
		$mail->AddCC('chetan.singh@akshamaala.com', "Chetan Singh");
		
		
        if(!$mail->Send())
		{
            
        }
        else
        { 
                          
        }
		
	}
    public function utf8_to_unicode($str) {
    $unicode = array();
        $values = array();
        $lookingFor = 1;
        for ($i = 0; $i < strlen($str); $i++) {
            $thisValue = ord($str[$i]);
                if($thisValue < 128){
                    $number = dechex($thisValue);
                    $unicode[] = (strlen($number) == 1) ? '%u000' . $number : "%u00" . $number;
                } else {
                    if (count($values) == 0)
                        $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
                    $values[] = $thisValue;
                    if (count($values) == $lookingFor) {
                        $number = ( $lookingFor == 3 ) ?
                            ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ) :
                            ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64
                            );
                        $number = dechex($number);
                        $unicode[] = (strlen($number) == 3) ? "%u0" . $number : "%u" . $number;
                        $values = array();
                        $lookingFor = 1;
                    } // if
                } // else
            }//for
        return implode("", $unicode);
    }//function  
	
	
           public function addActivity($key, $data) {
		if (isset($data['customer_id'])) {
			$customer_id = $data['customer_id'];
		} else {
			$customer_id = 0;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_activity` SET `customer_id` = '" . (int)$customer_id . "', `key` = '" . $this->db->escape($key) . "', `data` = '" . $this->db->escape(serialize($data)) . "', `ip` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', `date_added` = NOW()");
	}  
            public function getsms($id)
            {
                //$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sms WHERE LOWER(SID) = '" . $this->db->escape(utf8_strtolower($id)) . "'");
                $query = $this->db->query('select',DB_PREFIX . "sms",'','','',array('SID'=>(int)$this->db->escape(utf8_strtolower($id))));
$log=new Log("smshit-".date('Y-m-d').".log");
		$log->write($query);
		return $query->row;
            }

       
        
        public function updateSms($msgid,$TRANSACTIONID)
        {
            
        $message_sent=$this->getsms($msgid)["MESSAGE"];
        
           
            $sql="update  " . DB_PREFIX . "sms_record SET message_sent='".$message_sent."',MESSAGE_PROCESSED='1' where TRANSACTIONID='".$TRANSACTIONID."'";
            $this->db->query($sql);
            $ret_id = $this->db->countAffected();
            return $ret_id;
        
        }
}
