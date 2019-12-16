<?php
class ControllerCommonColumnLeft extends Controller {
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
		
        public function index() 
        {
			$this-> adminmodel('catalog/storemenu');
                    
			$this-> adminmodel('setting/store');
			$categories = $this->model_catalog_storemenu->getusermenu(array(
				'filter_parent'=> '1','filter_store'=>$this->user->getStoreId(),
				'filter_role'=>11,
				'user_id' => $this->user->getId(),
				'menutype'=>0
            ));
	
			$json['navigation'] = array();
		
			$data['menus'][] = array(
				'id'       => 'menu-dashboard',
				'icon'	   => '../image/catalog/Icon New/home_blue.png',
				'name'	   => 'Dashboard',
				'href'     => $this->url->link('pos/dashboard', 'token=' . $this->session->data['token'].'&pagetittle=Dashboard', true),
				'children' => array()
			);
			
			foreach ($categories as $category_id) 
			{
				$children=array();
				$not_in_array=array('144','117');
                $getCategorydetail=  $this->model_catalog_storemenu->getCategoryName($category_id);
				
                $subcategories = $this->model_catalog_storemenu->getusermenu(array('user_id' => $this->user->getId(),'menutype'=>1,'parent_id'=>$category_id));
                $subcategoriesData=array();
                foreach($subcategories as $subcategory)
                {
                   $getSubCategorydetail=  $this->model_catalog_storemenu->getCategoryName($subcategory);
                   $billtype=0;
                   if(strtolower($getSubCategorydetail['name'])==strtolower("Open Billing"))
                   {
                       $billtype=1;
                   }
					//if(($getSubCategorydetail['category_id']!=144) && ($getSubCategorydetail['category_id']!=117))
					if(($getSubCategorydetail['category_id']!=144) && ($getSubCategorydetail['category_id']!=117))
					{ 	
						
						$children[] = array(
						'name'	   => $getSubCategorydetail['name'],
						'icon'	   =>'../image/'.$getSubCategorydetail['image'],// "<img class='fa web_icon img-fluid' src='../image/".$getSubCategorydetail['image']."' />", 
						'href'     => $this->url->link($getSubCategorydetail['category_description'][1]['web_class'], 'token=' . $this->session->data['token'].'&pagetittle='.$getSubCategorydetail['name'], true),
						'children' => array()		
						);
				   }
                }
				if(($getCategorydetail['category_id']!=140) && ($getCategorydetail['category_id']!=145) && ($getCategorydetail['category_id']!=149))
				{
					if(count($subcategories)>0)
					{
						
					}
					$data['menus'][] = array(
					'id'       => 'menu-dashboard',
					'icon'	   => '../image/'.$getCategorydetail['image'],//"<img class='fa web_icon img-fluid' src='../image/".$getCategorydetail['image']."' />",
					'name'	   => $getCategorydetail['name'],
					'href'     => $this->url->link(($getCategorydetail['category_description'][1]['web_class']), 'token=' . $this->session->data['token'].'&pagetittle='.$getCategorydetail['name'], true),
					'children' => $children
					);
                }
		
			} 
			
            ////////////////////////////////////////////////
		$this->adminmodel('tool/image');
		$this->adminmodel('catalog/faq');

		$data['faqs'] = array();

		$faqs = $this->model_catalog_faq->getFaqs($this->config->get('faq_limit'));
		$data['faq_limit'] = $this->language->get('faq_limit');
		$faq_limit = $this->config->get('faq_limit');
		foreach ($faqs->rows as $faq) 
		{
			$data['faqs'][] = array(
				'faq_id' => $faq['faq_id'],
				'question'        => $faq['faq_description'][1]['question'],
				'answer'    => html_entity_decode($faq['faq_description'][1]['answer'], ENT_QUOTES, 'UTF-8'),
				'image'        => $this->model_tool_image->resize($faq['image'],100,100)
			);
		}
		
		///////////////////////////////////////////////
            $data['logout_action']=HTTP_SERVER.'index.php?route=common/logout';
            $data['profile_link']=HTTP_SERVER.'index.php?route=pos/pos/profile&token='.$this->session->data['token'];
            
            $data['UserNameShow']=$this->user->getUserNameShow(); 
            $data['Usergroupname']=$this->user->getUsergroupname(); 
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/column_left.tpl')) {
                    
			return $this->load->view($this->config->get('config_template') . '/template/common/column_left.tpl', $data);
		} else {
                    
                        return $this->load->view(DIR_TEMPLATE.'common/column_left.tpl', $data);
			//return $this->load->view('default/template/common/column_left.tpl', $data);
		}
               
	}
	public function catmodel($model) 
        {
            $admin_dir = DIR_SYSTEM;
            $admin_dir = str_replace('system/','catalog/',$admin_dir);
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
}