<?php
class Controllermposfaq extends Controller 
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
		$mcrypt=new MCrypt();
        $this->adminmodel('catalog/faq');
        $cat_id=$mcrypt->decrypt($this->request->get['cat_id']);
		//echo $mcrypt->encrypt(1);
		$data['categories'] = $this->model_catalog_faq->cat_getList();
		$data['category'] = $this->model_catalog_faq->getfaqCat($cat_id);
		$data['faqs'] = $this->model_catalog_faq->getfaqbycategoryid($cat_id);
		//print_r($data['category']);
        $this->response->setOutput($this->load->view('default/template/faq/faqlist.tpl', $data));
    }
	public function answer()
	{
		$mcrypt=new MCrypt();
        $this->adminmodel('catalog/faq');
        $cat_id=($this->request->get['cat_id']);
		$faq_id=($this->request->get['faq_id']);
		
		$data['category'] = $this->model_catalog_faq->getfaqCat($cat_id);
		$data['faq'] = $this->model_catalog_faq->getfaq($faq_id);
		//print_r($data['faq']);
        $this->response->setOutput($this->load->view('default/template/faq/faq.tpl', $data));
    }
	public function faqbycategory() 
    {
		$log=new Log("faq-by-category-".date('Y-m-d').".log");
		$log->write($this->request->post);		
		$mcrypt=new MCrypt();	
		$log->write($this->request->post);
		if(empty($this->request->post))
		{
            		exit("no input");

		}
		
		$this->request->post['category_id']=$mcrypt->decrypt($this->request->post['category_id']);
		$log->write($this->request->post);	
		$this->adminmodel('tool/image');
		$this->adminmodel('catalog/faq');
		$data=array();      
		$results=$this->model_catalog_faq->getfaqbycategoryid($this->request->post['category_id']);
		$log->write($results);
		foreach ($results as $result) 
		{
			$data['products'][] = array(
				'id'  => $mcrypt->encrypt($result['faq_id']),
				'question'       =>$mcrypt->encrypt( $result['faq_description']['1']['question']),
				'answer'     =>$mcrypt->encrypt( strip_tags(html_entity_decode($result['faq_description']['1']['answer'], ENT_QUOTES, 'UTF-8'))),
				'image'     => $mcrypt->encrypt($result['image']),
				'thumb'     => $mcrypt->encrypt($this->model_tool_image->resize($result['image'], 100, 100))
							);
		}		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode( $data));
		
	}
	public function category() 
    {
		$log=new Log("faq-category-".date('Y-m-d').".log");
		$log->write($this->request->post);		
		$mcrypt=new MCrypt();	
		$log->write($this->request->post);
		if(empty($this->request->post))
		{
            		exit("no input"); 

		}		
		$this->adminmodel('catalog/faq');
		$data=array();      
		$results=$this->model_catalog_faq->getFaqCategories();
		foreach ($results as $result) 
		{
			$data['products'][] = array(
				'id'  =>$mcrypt->encrypt( $result['id']),
				'image'       => $mcrypt->encrypt($result['image']),
				'name'       => $mcrypt->encrypt(str_replace('&amp;','&',$result['name'])),
				'fname'		=>$mcrypt->encrypt(str_replace('&amp;','&',empty($result['description'])? '':$result['description']))			
			);
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode( $data));
		
	}
        
        
    
}