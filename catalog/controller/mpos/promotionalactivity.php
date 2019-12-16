<?php
class Controllermpospromotionalactivity  extends Controller
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

    function addpro_activity()
   
    {     

	
        $log=new Log("addpro_activity-".date('Y-m-d').".log");
        $mcrypt=new MCrypt();
		$log->write('Addproactivity called');
        $log->write($this->request->post);
        //$log->write($this->request->get);
       // $data=array();
	
     
       $keys = array(
		'store_id',
		'user_id',
		'store_name',
		'activity_id',
		'activity_name',
		'company_id',
		'company_name',
		'retailer_id',
		'retailer_name',
		'representative_name',
		'date',
		'lat',
		'long',
		'representative_mobile'
			
		);
		
		
		 foreach ($keys as $key) 
        {
           	$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
        }
		
		
		$log->write($this->request->post);
		//echo "hello12";
		$this->adminmodel('openretailer/promotionalactivity');
		//echo "hello1";
		$data=$this->model_openretailer_promotionalactivity->addActivity($this->request->post);
        $log->write("model");
        $log->write($data);
		$datas=array();
       
       // if( !empty($data['store_id']) )
       // {
            $log->write("in if");
           
            $datas['status']=(1);
			$datas['order_id']=($data);
            $datas['message']=("Promotional Activity submitted successfully.");
            
        //}        
        //else
       // {
           // $log->write("in else");
           // $datas['error']=(0);
			//$datas['status']=0;
       //$datas['success']=("Promotional Activity can not empty");
        //}
        if(!empty($datas))
        {
            $this->response->setOutput(json_encode($datas,JSON_UNESCAPED_UNICODE));
           
        }            
    }
	
 
  

	
	
}
?>