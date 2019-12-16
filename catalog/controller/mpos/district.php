<?php
	class ControllerMposDistrict extends Controller 
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
		public function getlist() 
		{ 
			$mcrypt=new MCrypt();
			$keys = array(
				'store_id',
				'page',
				'name'
				);
			foreach ($keys as $key) 		
			{
				$this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;    
			}
			$this->adminmodel('district/district');
			
			$page=$this->request->post['page'];
			if(empty($page))
			{
				$page=1;
			}
			$start = ($page - 1) * 20;
			$limit = 200;
			$filter_data=array(
			'store_id'=>$this->request->post['store_id'],
			'name'=>$this->request->post['name'],
			'start'=>$start,
			'limit'=>$limit
			);
			$districts = $this->model_district_district->get_all_districts($filter_data);
			$data['districts'] = $districts->rows;
			
			$total_districts = $districts->num_rows;
			foreach($data['districts'] as $district)
			{
				$json['products'][] = array(
					'district_id'			=> $mcrypt->encrypt($district['district_id']),
					'name'			=> $mcrypt->encrypt($district['name']),
					'district_code'  	=> $mcrypt->encrypt($district['district_code'])
					);
			}
			$json['total']=$mcrypt->encrypt($total_districts);
			return $this->response->setOutput(json_encode($json));
		}
		
	}
?>