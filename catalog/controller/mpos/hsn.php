<?php
class Controllermposhsn extends Controller 
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
	{
		$log=new Log("hsn-".date('Y-m-d').".log");
		$log->write('index called');
		$log->write($this->request->get);
		
		$mcrypt=new MCrypt();
        $this->adminmodel('catalog/hsn');
        //echo $mcrypt->encrypt(1);
		$data['store_id']=$mcrypt->decrypt($this->request->get['store_id']);
		$hsn_code=($this->request->get['hsn_code']);
		$filter_data=array('hsn'=>$hsn_code);
		foreach($this->model_catalog_hsn->gethsn($filter_data)->rows as $row)
		{
			$similar_products='';
			
			foreach($this->model_catalog_hsn->getproductbyhsn($row)->rows as $prd)
			{
				//print_r($prd);
				$similar_products=$similar_products.$prd['name'].',';
			}
			$similar_products=rtrim($similar_products,',');
			$row['similar_products']=$similar_products;
			$data['hsnlist'][] = $row;
			
		}
		
		//exit;
        $this->response->setOutput($this->load->view('default/template/hsn/hsnlist.tpl', $data));
    }
	public function getlist()
	{
		$log=new Log("hsn-".date('Y-m-d').".log");
		$log->write('getlist called');
		$log->write($this->request->get);
		
		$mcrypt=new MCrypt();
        $this->adminmodel('catalog/hsn');
        //echo $mcrypt->encrypt(1);
		$data['store_id']=$mcrypt->decrypt($this->request->get['store_id']);
		foreach($this->model_catalog_hsn->gethsn()->rows as $row)
		{
			if(empty($row['tax_class_name']))
			{
				$row['tax_class_name']='';
			}
			if(empty($row['tax_class_id']))
			{
				$row['tax_class_id']='';
			}
			$data2[]=array('sid'=>$row['sid'],'hsn_code'=>$row['hsn_code'],'hsn_name'=>$row['hsn_name'],'tax_class_name'=>$row['tax_class_name'],'tax_class_id'=>$row['tax_class_id']);
		}
		$json=array('status'=>1,'products'=>$data2);
		
		$this->response->setOutput(json_encode($json));
			
    }
	
    public function success()
	{
		$data['success']=$this->session->data['success']; 
		if(empty($data['success']))
		{
			$data['success']=$this->request->get['message'];
		}
        $this->response->setOutput($this->load->view('default/template/printer/success.tpl', $data));
    }  
	
    
}