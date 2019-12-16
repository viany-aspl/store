<?php
class ControllerMposPeer extends Controller 
{
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
    public function save()
    {
		$log=new Log("peer-".date('Y-m-d').".log");
		$log->write('save called');
		$log->write($this->request->post);
		$log->write($this->request->get);
       
		$mcrypt=new MCrypt();
		$keys = array(
            'category_name',
            'store_id',
            'product_id',
            'store_name',
            'negotiation',
            'action',
            'group_id',
            'share_detail',
            'quantity',
            'product_name',
            'offer_price',
            'category_id',
            'validate',
            'user_id',
            'lat',
            'lng',
            'remarks'
		);
		foreach ($keys as $key) 		
		{
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]);   
		}
        if(empty($this->request->post['action']))
		{
            $json=array('status'=>0,'msg'=>'Please select Sale/Rent');
            return $this->response->setOutput(json_encode($json));
		}
        if(empty($this->request->post['product_id']))
		{
            $json=array('status'=>0,'msg'=>'Please select Product');
            return $this->response->setOutput(json_encode($json));
		}
        if(empty($this->request->post['quantity']))
		{
            $json=array('status'=>0,'msg'=>'Please enter quantity');
            return $this->response->setOutput(json_encode($json));
		}
        $share_detail1=explode(',',$this->request->post['share_detail']);
        $share_detail_array=array();
        foreach($share_detail1 as $share_detail2)
        {
            $share_detail_array[]=$share_detail2;
        }
        $this->request->post['share_detail']=$share_detail_array;
        $this->request->post['group_id']=11;
        $this->request->post['loc']=array("lon"=>(float)$this->request->post['lng'],"lat"=>(float)$this->request->post['lat']);
		
		$log->write($this->request->post);

		$now = time(); // or your date as well
		$your_date = strtotime($this->request->post['validate']);
		$datediff = $your_date-$now ;

		$number_days=round($datediff / (60 * 60 * 24));
		$log->write($now);
		$log->write('number_days');
		$log->write($number_days);
		if(($number_days>=-1) && ($number_days<=90))
		{
			$this->adminmodel('peer/peer');
			$pdata=$this->model_peer_peer->getProductCheck(array('store_id'=>$this->request->post['store_id'],'product_id'=>$this->request->post['product_id']));
			if($pdata->num_rows>0)
			{
				$json=array('status'=>0,'msg'=>'Product is already exist for this date range!');
				return $this->response->setOutput(json_encode($json));
			}
			else
			{
			
				$log->write($this->request->post);
			
				$supplier_data=$this->model_peer_peer->submit_order($this->request->post);
				$log->write($supplier_data);
				$json=array('status'=>1,'msg'=>'Sale Registered');
				return $this->response->setOutput(json_encode($json));
			}
			
		}
		else
		{
			$json=array('status'=>0,'msg'=>'Validate should be max for 90 days');
			return $this->response->setOutput(json_encode($json));
		}
		/*
		if($number_days<0)
		{
			$Date=date('Y-m-d');
			$this->request->post['validate']=date('Y-m-d', strtotime($Date. ' + 90 days'));
		}
		if($number_days>90)
		{
			
			$Date=date('Y-m-d');
			$this->request->post['validate']=date('Y-m-d', strtotime($Date. ' + 90 days'));
		}
		*/
		
		
    }
    public function getlist() 
    { 
		$log=new Log("peer-".date('Y-m-d').".log");
		$log->write('getlist called');
		$log->write($this->request->post);
		$mcrypt=new MCrypt();
		$keys = array(
		'store_id',
		'page',
        'lat',
        'lng',
		'start_date',
		'end_date',
		'action'
		);
		foreach ($keys as $key) 		
		{
            $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
		}
        if(empty($this->request->post['lat']))
		{
            $json=array('status'=>0,'msg'=>'Lattitude not recevied');
            return $this->response->setOutput(json_encode($json));
		}
        if(empty($this->request->post['lng']))
		{
            $json=array('status'=>0,'msg'=>'Lontitude not recevied');
            return $this->response->setOutput(json_encode($json));
		}
		$page=$this->request->post['page'];
		if(empty($page))
		{
            $page=1;
		}
		$filter_data=array(
			'start'=>$start,
			'limit'=>$limit,
			'store_id'=>$this->request->post['store_id'],
            'lat'=>$this->request->post['lat'],
            'lng'=>$this->request->post['lng'],
			'filter_date_start'=>$this->request->post['start_date'],
			'filter_date_end'=>$this->request->post['end_date'],
			'filter_status'=>1
			);
		$log->write($filter_data);
		$this->adminmodel('peer/peer');
        if((!empty($filter_data['lat'])) && (!empty($filter_data['lng'])))
        {
            $order_data=$this->model_peer_peer->getList($filter_data);
			//$log->write($order_data);
        }
        $data['orders'] = $order_data->rows;
		$total_order = $order_data->num_rows;
	
		if($this->request->post['action']=='e')
		{ 
			$this->load->library('email');
			$email=new email($this->registry);
			
			$file_name="Sale_to_retailer_".date('dMy').'.csv';
			$fields = array(
				'Category',
				'Product Name',
				'Offer Price',
				'Min qty',
				'Validity',
				'Telephone',
				'Email',
				'Store Name'

			);
			
			
			foreach($data['orders'] as $data)
    		{
				$store_data=array();
				$store_data=$this->model_peer_peer->get_store_data($data['store_id']);
				$fdata[]=array(
                        $data['category_name'],
                        $data['product_name'],
                        $data['offer_price'],
						$data['quantity'],
						date('d-m-Y',$data['validate']->sec),
						$store_data['telephone'],
						$store_data['email'],
						$store_data['name']
					);
				
			}
			
			$email->create_csv($file_name,$fields,$fdata);
			
			$mail_subject="Sale to_retailer  ";
			
			$body = "<p style='border: 1px solid silver;padding: 15px;'>
			Dear Team,
			<br/><br/>
			Please find attached file for Sale to_retailer.
			
			<br/><br/>
			This is computer generated email.Please do not reply to this email.
			<br/><br/>
			<img src='https://unnati.world/shop/image/cache/no_image-45x45.png' />
			<br/><br/>
			Thanking you,
			<br/>
			IT Team
			<br/>
			AgriPOS
			
			<br/>
			<br/>
			<span  style='font-size:7.5pt;color: green;'> Please consider the environment before printing this email. Ask yourself whether you really need a hard copy.</span>
			</p>";
			$to=$this->request->post['store_id'];   
			$cc=array();
			$bcc=array('vipin.kumar@aspltech.com','hrishabh.gupta@unnati.world','chetan.singh@akshamaala.com');
			
			$file_path=DIR_UPLOAD.$file_name;
            $email->sendmail($mail_subject,$body,$to,$cc,$bcc,$file_path);
			
			$json=array('status'=>1,'msg'=>'sent');
			$log->write("sent" );
			$log->write('return array');
			//$log->write($json);
			$this->response->setOutput(json_encode($json));
		}
		else
		{
			$json1=array();
			$json=array();
			foreach($data['orders'] as $order)
			{
            $store_data=array();
            $store_data=$this->model_peer_peer->get_store_data($order['store_id']);
			
			if($order['store_id']==$this->request->post['store_id'])
			{
				$own='1';
			}
			else
			{
				$own='0';
			}
            if(in_array($this->request->post['store_id'],$order['fav_store_ids']))
			{
				$favourite=1;
				$json1['products_fav'][] = array(
				'category_name' => $mcrypt->encrypt($order['category_name']),
				'store_id'	=> $mcrypt->encrypt($order['store_id']),
				'create_date'	=> $mcrypt->encrypt(date('d-m-Y',$order['create_date']->sec)),
				'product_id'  	=> $mcrypt->encrypt($order['product_id']),
				'store_name'  	=> $mcrypt->encrypt($store_data['name']),
				
				'negotiation'  	=> $mcrypt->encrypt($order['negotiation']),
				'action'  	=> $mcrypt->encrypt($order['action']),
                'group_id'  	=> $mcrypt->encrypt($order['group_id']),
                'share_detail'  => $mcrypt->encrypt($order['share_detail']),
                'quantity'      => $mcrypt->encrypt($order['quantity']),
                'product_name'  => $mcrypt->encrypt($order['product_name']),
                'offer_price'  	=> $mcrypt->encrypt($order['offer_price']),
                'category_id'   => $mcrypt->encrypt($order['category_id']),
                'validate'  	=> $mcrypt->encrypt(date('d-m-Y',$order['validate']->sec)),
                'user_id'       => $mcrypt->encrypt($order['user_id']),
                'lat'  	        => $mcrypt->encrypt($order['lat']),
                'lng'  	        => $mcrypt->encrypt($order['lng']),
                'location'      => $mcrypt->encrypt($order['loc']),
				'status'  	=> $mcrypt->encrypt($order['status']),
				'sid'  	        => $mcrypt->encrypt($order['sid']),
				'remarks'  	=> $mcrypt->encrypt($order['remarks']),
                'telephone'  	=> $mcrypt->encrypt($store_data['telephone']),
                'email'  	=> $mcrypt->encrypt($store_data['email']),
				'id'  	=> $mcrypt->encrypt($order['sid']),
				'favourite'  	=> $mcrypt->encrypt($favourite),
				'own'  	=> $mcrypt->encrypt($own)
				);
			}
			else
			{
				$favourite=0;
				$json1['products_un'][] = array(
				'category_name' => $mcrypt->encrypt($order['category_name']),
				'store_id'	=> $mcrypt->encrypt($order['store_id']),
				'create_date'	=> $mcrypt->encrypt(date('d-m-Y',$order['create_date']->sec)),
				'product_id'  	=> $mcrypt->encrypt($order['product_id']),
				'store_name'  	=> $mcrypt->encrypt($store_data['name']),
				
				'negotiation'  	=> $mcrypt->encrypt($order['negotiation']),
				'action'  	=> $mcrypt->encrypt($order['action']),
                'group_id'  	=> $mcrypt->encrypt($order['group_id']),
                'share_detail'  => $mcrypt->encrypt($order['share_detail']),
                'quantity'      => $mcrypt->encrypt($order['quantity']),
                'product_name'  => $mcrypt->encrypt($order['product_name']),
                'offer_price'  	=> $mcrypt->encrypt($order['offer_price']),
                'category_id'   => $mcrypt->encrypt($order['category_id']),
                'validate'  	=> $mcrypt->encrypt(date('d-m-Y',$order['validate']->sec)),
                'user_id'       => $mcrypt->encrypt($order['user_id']),
                'lat'  	        => $mcrypt->encrypt($order['lat']),
                'lng'  	        => $mcrypt->encrypt($order['lng']),
                'location'      => $mcrypt->encrypt($order['loc']),
				'status'  	=> $mcrypt->encrypt($order['status']),
				'sid'  	        => $mcrypt->encrypt($order['sid']),
				'remarks'  	=> $mcrypt->encrypt($order['remarks']),
                'telephone'  	=> $mcrypt->encrypt($store_data['telephone']),
                'email'  	=> $mcrypt->encrypt($store_data['email']),
				'id'  	=> $mcrypt->encrypt($order['sid']),
				'favourite'  	=> $mcrypt->encrypt($favourite),
				'own'  	=> $mcrypt->encrypt($own) 
				);
			}
			if(!empty($json1['products_un']) && (!empty($json1['products_fav'])))
			{
				$json['products']=array_merge($json1['products_fav'],$json1['products_un']);
			}
			else if(!empty($json1['products_un']) && (empty($json1['products_fav'])))
			{
				$json['products']=$json1['products_un'];
			}
			else if(empty($json1['products_un']) && (!empty($json1['products_fav'])))
			{
				$json['products']=$json1['products_fav'];
			}			
		}
		
		//$log->write($json1);
		$log->write('final output');
		$json['total']=$mcrypt->encrypt($total_order);
		//$log->write($json);
		return $this->response->setOutput(json_encode($json));
		}
    }
    function addtofavourite()
    {        
        $log=new Log("peer-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
		$log->write('addtofavourite called');
        $log->write($this->request->post);
        $log->write($this->request->get);
        $data=array();
       
		$data['store_id']=$mcrypt->decrypt($this->request->post['store_id']);
		$data['sid']=$mcrypt->decrypt($this->request->post['peer_id']);
		
        $this->adminmodel('peer/peer');
        $log->write("model");
        $log->write($data);
        $datas=array();
        if(!empty($data['sid']) )
        {
            $log->write("in if");
           
            $prdid = $this->model_peer_peer->addtofavourite($data); 
            $datas['msg']=$mcrypt->encrypt("Added to favourite.");
            $datas['status']=$mcrypt->encrypt(1);
        }        
        else
        {
            $log->write("in else");
            $datas['msg']=$mcrypt->encrypt("Peer ID can not empty");
			$datas['status']=$mcrypt->encrypt(0);
        }
        if(!empty($datas))
        {
            $this->response->setOutput(json_encode($datas,JSON_UNESCAPED_UNICODE));
           
        }            
    }
	function remove_favourite()
    {        
        $log=new Log("peer-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
		$log->write('remove_favourite called');
        $log->write($this->request->post);
        $log->write($this->request->get);
        $data=array();
       
		$data['store_id']=$mcrypt->decrypt($this->request->post['store_id']);
		$data['sid']=$mcrypt->decrypt($this->request->post['peer_id']);
		
        $this->adminmodel('peer/peer');
        $log->write("model");
        $log->write($data);
        $datas=array();
        if(!empty($data['sid']) )
        {
            $log->write("in if");
           
            $prdid = $this->model_peer_peer->remove_favourite($data); 
            $datas['msg']=$mcrypt->encrypt("Removed from favourite.");
            $datas['status']=$mcrypt->encrypt(1);
      
        }        
        else
        {
            $log->write("in else");
            $datas['msg']=$mcrypt->encrypt("Peer ID can not empty");
			$datas['status']=$mcrypt->encrypt(0);
        }
        if(!empty($datas))
        {
            $this->response->setOutput(json_encode($datas,JSON_UNESCAPED_UNICODE));
           
        }            
    }
	function delete_peer()
    {        
        $log=new Log("peer-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
		$log->write('delete_peer called');
        $log->write($this->request->post);
        $log->write($this->request->get);
        $data=array();
       
		$data['store_id']=$mcrypt->decrypt($this->request->post['store_id']);
		$data['sid']=$mcrypt->decrypt($this->request->post['peer_id']);
		
        $this->adminmodel('peer/peer');
        $log->write("model");
        $log->write($data);
        $datas=array();
        if(!empty($data['sid']) )
        {
            $log->write("in if");
           
            $prdid = $this->model_peer_peer->delete_peer($data); 
            $datas['msg']=$mcrypt->encrypt("Deleted Successfully.");
            $datas['status']=$mcrypt->encrypt(1);
        }        
        else
        {
            $log->write("in else");
            $datas['msg']=$mcrypt->encrypt("Peer ID can not empty");
			$datas['status']=$mcrypt->encrypt(0);
        }
        if(!empty($datas))
        {
            $this->response->setOutput(json_encode($datas,JSON_UNESCAPED_UNICODE));
           
        }            
    }
}
?>