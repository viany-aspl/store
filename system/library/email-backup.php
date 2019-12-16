<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
error_reporting(0);

class email 
{
	public function __construct($registry) 
	{
        $this->config = $registry->get('config');
		$this->db = $registry->get('db');
                
	}
	public function sendmail($mail_subject,$mail_body,$to,$cc='',$bcc='',$file_path='')
	{ 
		$log= new Log('email-'.date('Y-m-d').'.log');
		$log->write($mail_subject);
		
		$log->write($to);
		$log->write($cc);
		$log->write($bcc);
		$bcc=array();
		$names = is_array($mail_body) ? $mail_body : array($mail_body);
		
		$body = "";
		$htmlBody =$body; 
		
		foreach ($names as $name) 
		{
			if (!is_object($name)) 
			{
				$htmlBody .= "<tr><td>".$name."</td></tr>";
			}
		}
		$htmlBody.="</table>";
			
		if(is_numeric($to))
		{
			$to=$this->getstoreemail($to);
		}
		$log->write('updated to : ');
		$log->write($to);
		$asynchMail = new AsynchMail($mail_subject,$htmlBody,$to,$cc,$bcc,$file_path);
		$asynchMail->start();
	}
	public function create_csv($file_name='',$header_array,$dataset)
	{
		$log= new Log('create_csv-'.date('Y-m-d').'.log');
		$log->write($dataset);
		
		$fields = $header_array;
		if(empty($file_name))
		{
			$file_name="report_".date('dMy').'.csv';
		}
		$fileIO = fopen(DIR_UPLOAD.$file_name, 'w+');//'php://memory'
		fputcsv($fileIO, $fields,',');
		foreach($dataset as $data)
    	{
			$fdata=$data;
			fputcsv($fileIO,  $fdata,",");
		}
		fseek($fileIO, 0);
		fclose($fileIO);
	}
	public function getstoreemail($id)
    {
        $query = $this->db->query('select',DB_PREFIX . "setting",'','','',array('store_id'=>(int)$id,'key'=>'config_email'));

		return $query->row['value'];
    }
}

class AsynchMail extends Thread
{
    private $_subject;
	private $_htmlBody;
	private $_cc;
	private $_bcc;
	private $_file_path;
    public function __construct($subject, $htmlBody,$to,$cc='',$bcc='',$file_path='') 
	{
        $this->_subject = $subject;
		$this->_to = $to;  
		$this->_cc = $cc; 
		$this->_bcc = $bcc; 
		$this->_htmlBody= $htmlBody;	
		$this->_file_path= $file_path;
    }
        
    public function run() 
	{
		$log= new Log('email-sync-'.date('Y-m-d').'.log');
		$mail = new PHPMailer();
		$log->write('in sync');
        $log->write($this->_htmlBody);                
		//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
		//$mail->SMTPDebug = 2; //Alternative to above constant
        $mail->IsSMTP();                                                           
        $mail->SMTPAuth   = true;                 
        $mail->SMTPSecure = '';    
		$mail->SMTPAutoTLS = false;             
        $mail->Host       = "smtpout.asia.secureserver.net";      
        $mail->Port       = 25;                  
        $mail->Username   = "report@unnatiagro.in";  
        $mail->Password   = "report#321";
		$mail->From = 'report@unnatiagro.in';
		$mail->FromName = "Help Desk";            
        $mail->Subject    = $this->_subject;
		$mail->WordWrap = 70;		 
        $mail->AltBody    = "No Data in message body!"; // optional, comment out and test
		$this->_htmlBody=str_replace("<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/><br/>",'',$this->_htmlBody);
		$this->_htmlBody=str_replace("border: 1px solid silver;padding: 15px;",'font:16px Arial,Helvetica,sans-serif;',$this->_htmlBody);
		$this->_htmlBody=str_replace("IT Team",'',$this->_htmlBody);		
		//'.date('jS').' '.date('F').', '.date('Y').'
		$this->_htmlBody='<table border="0" cellpadding="0" cellspacing="0" width="100%"  style="font:16px Arial,Helvetica,sans-serif;padding-left:6%;padding-right:10%;">

    <tr>

        <td width="100%" height="100%" align="center" valign="top">

			<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="white" style="margin:0px; padding:10px; border-bottom:1px solid #215885;">

			
				<tr>

					<td width="100%" align="left" height="auto">
						<a style="color:#215884; text-decoration:none;" target="_blank" title="AgriPOS" href="https://unnatiagro.in/">
							<img style="width: 110px;height: 35px;" src="https://unnatiagro.in/images/logo.png">
						</a>
					</td>

					<td width="100%" align="right" height="auto" style="font-size:14px;display:block;padding-top:5px;">
						<a style="color:#215884; text-decoration:none;" target="_blank" title="AgriPOS" href="https://play.google.com/store/apps/details?id=com.unnatiagro.agripos">
							<img style="vertical-align:middle;width: 80px;height: 24px;" src="https://unnatiagro.in/images/play_button.png"  />
						</a>
					</td>

				</tr>
				<!--<tr>

					<td rowspan="2" width="50%" align="left" height="auto"><img style="width: 146px;height: 45px;" src="https://unnatiagro.in/images/logo.png"></td>

				</tr>

				

				<tr>

					<td width="100%" align="right" height="auto" style="font-size:14px; display:block; margin-top:10px;"></td>

					<td width="100%" align="right" height="auto" style="font-size:14px;display:block;">
					Download App <a style="color:#215884; text-decoration:none;" target="_blank" title="Unnati App" href="https://play.google.com/store/apps/details?id=com.unnatiagro.agripos"><img style="vertical-align:middle;" src="https://unnatiagro.in/images/android_icon.png"</a></td>

				</tr>-->

			</table>'.$this->_htmlBody;

			
			$this->_htmlBody=$this->_htmlBody.'
			<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="white" style="padding-left:6%;padding-right:10%;">
				<tr>

					<td width="100%" align="left" height="auto" style="font:10px Arial,Helvetica,sans-serif; font-weight:bold;color:#656565; padding-top:10px; border-top:5px solid #215885;color:#656565;">Customer Service </td>

				</tr>

				<tr>

					<td width="100%" align="left" height="auto" style="font-size:10px;color:#656565;">Have Questions? Feel free to write us at <a href="mailto:info@aspl.ind.in" target="_blank">info@aspl.ind.in</a> or call at <a href="tel:+911204040180">+911204040180</a> </td>

				</tr>

				

				<tr>

					<td width="100%" align="left" height="auto" style="font:10px Arial,Helvetica,sans-serif; font-weight:bold; padding-top:10px;color:#656565;">About Unnati</td>

				</tr>

				<tr>

					<td width="100%" align="left" height="auto" style="font-size:10px;color:#656565;">Unnati is committed to create end to end digital platform in the agronomic business. Through ASPL we provide digital solutions on CRM, BI and Data Analysis. Unnati Agri Retail has contributed in creating a channel of supply and knowledge to farmers. </br>  Unnati AgriPOS is redefining the Retailer and Farmer relationship in a contextual and convenient way using digital platform. AgriPOS allows retailers to use the platform as a POS (Point of Sales) machine using his android phone. Features supported are farmer billing, farmer credit history, store inventory management, price, quantity & quality control, purchase management, smart effective messaging to farmer, GST etc.</td>

				</tr>

			</table>
		</td>

    </tr>
</table>';
        $mail->MsgHTML($this->_htmlBody); 
		$mail->AddAddress($this->_to, $this->_to);
        if(!empty($this->_cc))
		{
			foreach($this->_cc as $cc2)
			{
				$mail->AddCC($cc2, $cc2);
			}
        }
		if(!empty($this->_bcc))
		{
			foreach($this->_bcc as $bcc2)
			{
				$mail->AddBCC($bcc2, $bcc2);
			}
        }
		if(!empty($this->_file_path))
		{
			if(is_array($this->_file_path))
			{
				foreach($this->_file_path as $file)
				{
					$mail->AddAttachment($file); 
				}
			}
			else
			{
				$mail->AddAttachment($this->_file_path); 
			}
		}	
        if(!$mail->Send())
        {
            $log->write("Mailer Error: " . $mail->ErrorInfo);
        }
        else
        {                  
            $log->write("Success "); 
			if(!empty($this->_file_path))	
			{	if(is_array($this->_file_path))
				{
					foreach($this->_file_path as $file)
					{
						unlink($file);
					}
				}
				else
				{
					unlink($this->_file_path);
				}
			}		
        }		
		
    }
}
?>
