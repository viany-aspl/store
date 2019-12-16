<?php
class ControllermposWapi extends Controller 
{
    public function adminmodel($model) 
    {
        $admin_dir = DIR_SYSTEM;
        $admin_dir = str_replace('system/','backoffice/',$admin_dir);
        $file = $admin_dir . 'model/' . $model . '.php';      
        $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);      
        if (file_exists($file)) {
	         include_once($file);         
        	 $this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
        } else {
         trigger_error('Error: Could not load model ' . $model . '!');
         exit();               
        }
    }

 public function index() 
    {//$str=trim(str_replace("auth:","","auth:{\"id\":1,\"token\":1}"));
     
     //print_r(json_decode($str,true));exit;
		$log=new Log("wapi-".date('Y-m-d').".log");
		$log->write("in");
		$this->load->language('api/login');
		$log->write("in1");
		$mcrypt=new MCrypt();
		$this->adminmodel('user/user');
		$this->model_user_user->insert_qr_login($mcrypt->decrypt($this->request->post['username']),$mcrypt->decrypt($this->request->post['user_id']),$mcrypt->decrypt($this->request->post['store_id']),$this->request->server['HTTP_QID']);
		
                $this->auth($this->request->server['HTTP_QID'],$this->request->post);
		unset($this->session->data['api_id']);	
		
		$keys = array(
			'username',
			'user_id',
			'store_id',
			'rid',
            		'eid'
		);		
		$log->write("in3");
		$log->write($this->request->server['HTTP_QID']);
		$log->write($this->request->post);
		if(empty($this->request->post))
		{
	            exit("no input");
		}
		foreach ($keys as $key)
		{
        	    $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;            
       	 	}
		$log->write($this->request->post);
		$json = array();
		$log->write("in5");				
		$log->write($data);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
    }
    private function auth($serv,$data)
    {
        $host    = "127.0.0.1";
        $port    = 9002;      
        $log=new Log("wapi-".date('Y-m-d').".log");
	
        $mcrypt=new MCrypt();
        $serv1=explode(',',$serv);
        $log->write("Message To server :");  
		$log->write($data);  
		$log->write($serv1);  
        $local = "wss://unnatiagro.in/";  //url where this script run
        $fdata=array();
        $fdata["id"]=$mcrypt->decrypt($serv1[1]);//
        $fdata["token"]=$mcrypt->decrypt($serv1[0]);
        $fdata["user"]=$data["user_id"];
        $fdata["store"]=$data["store_id"];
		$log->write('1');  
        $data = "auth:".json_encode($fdata);  //data to be send
        $head = "GET / HTTP/1.1"."\r\n".
        "Upgrade: WebSocket"."\r\n".
        "Connection: Upgrade"."\r\n".
        "Origin: $local"."\r\n".
        "Host: $host"."\r\n".
        "Sec-WebSocket-Key: asdasdaas76da7sd6asd6as7d"."\r\n".
        "Content-Length: ".strlen($data)."\r\n"."\r\n";
        //WebSocket handshake
        $sock = fsockopen($host, $port, $errno, $errstr, 2);
        fwrite($sock, $head ) or die('error:'.$errno.':'.$errstr);
        $headers = fread($sock, 2000);
        $log->write($headers);
        fwrite($sock, $this->hybi10Encode($data)) or die('error:'.$errno.':'.$errstr);
        $wsdata = fread($sock, 2000);
        $log->write($wsdata);
        $log->write(var_dump($this->hybi10Decode($wsdata)));
        fclose($sock);
    }

function hybi10Decode($data)
{
    $bytes = $data;
    $dataLength = '';
    $mask = '';
    $coded_data = '';
    $decodedData = '';
    $secondByte = sprintf('%08b', ord($bytes[1]));
    $masked = ($secondByte[0] == '1') ? true : false;
    $dataLength = ($masked === true) ? ord($bytes[1]) & 127 : ord($bytes[1]);

    if($masked === true)
    {
        if($dataLength === 126)
        {
           $mask = substr($bytes, 4, 4);
           $coded_data = substr($bytes, 8);
        }
        elseif($dataLength === 127)
        {
            $mask = substr($bytes, 10, 4);
            $coded_data = substr($bytes, 14);
        }
        else
        {
            $mask = substr($bytes, 2, 4);       
            $coded_data = substr($bytes, 6);        
        }   
        for($i = 0; $i < strlen($coded_data); $i++)
        {       
            $decodedData .= $coded_data[$i] ^ $mask[$i % 4];
        }
    }
    else
    {
        if($dataLength === 126)
        {          
           $decodedData = substr($bytes, 4);
        }
        elseif($dataLength === 127)
        {           
            $decodedData = substr($bytes, 10);
        }
        else
        {               
            $decodedData = substr($bytes, 2);       
        }       
    }   

    return $decodedData;
}


function hybi10Encode($payload, $type = 'text', $masked = true) {
    $frameHead = array();
    $frame = '';
    $payloadLength = strlen($payload);

    switch ($type) {
        case 'text':
            // first byte indicates FIN, Text-Frame (10000001):
            $frameHead[0] = 129;
            break;

        case 'close':
            // first byte indicates FIN, Close Frame(10001000):
            $frameHead[0] = 136;
            break;

        case 'ping':
            // first byte indicates FIN, Ping frame (10001001):
            $frameHead[0] = 137;
            break;

        case 'pong':
            // first byte indicates FIN, Pong frame (10001010):
            $frameHead[0] = 138;
            break;
    }

    // set mask and payload length (using 1, 3 or 9 bytes)
    if ($payloadLength > 65535) {
        $payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
        $frameHead[1] = ($masked === true) ? 255 : 127;
        for ($i = 0; $i < 8; $i++) {
            $frameHead[$i + 2] = bindec($payloadLengthBin[$i]);
        }

        // most significant bit MUST be 0 (close connection if frame too big)
        if ($frameHead[2] > 127) {
            $this->close(1004);
            return false;
        }
    } elseif ($payloadLength > 125) {
        $payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
        $frameHead[1] = ($masked === true) ? 254 : 126;
        $frameHead[2] = bindec($payloadLengthBin[0]);
        $frameHead[3] = bindec($payloadLengthBin[1]);
    } else {
        $frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
    }

    // convert frame-head to string:
    foreach (array_keys($frameHead) as $i) {
        $frameHead[$i] = chr($frameHead[$i]);
    }

    if ($masked === true) {
        // generate a random mask:
        $mask = array();
        for ($i = 0; $i < 4; $i++) {
            $mask[$i] = chr(rand(0, 255));
        }

        $frameHead = array_merge($frameHead, $mask);
    }
    $frame = implode('', $frameHead);
    // append payload to frame:
    for ($i = 0; $i < $payloadLength; $i++) {
        $frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
    }

    return $frame;
}

        
        
        
    
}