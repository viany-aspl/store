<?php
	class ControllerPosPeer extends Controller 
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
		public function index() 
		{
			$url = '';
			if (isset($this->request->get['pagetittle'])) 
			{
				$url .= '&pagetittle=' . $this->request->get['pagetittle'];
			}
			$data['add'] = $this->url->link('pos/peer/add_sale', 'token=' . $this->session->data['token'] . $url.'&pagetittle=Register Sale', true);
			
			$data['filter'] = $this->url->link('pos/peer', 'token=' . $this->session->data['token'] . $url, true);
			
			$this->adminmodel('peer/peer');
			
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			
			if (isset($this->request->get['page'])) 
			{
				$page = $this->request->get['page'];
			} 
			else 
			{
				$page = 1;
			}
			if (isset($this->request->get['lat'])) 
			{
				$url .= '&lat=' . $this->request->get['lat'];
				$lat =$data['lat']=  $this->request->get['lat'];
			}

			if (isset($this->request->get['lng'])) 
			{
				$url .= '&lng=' . $this->request->get['lng'];
				$lng =$data['lng']=  $this->request->get['lng'];
			}
			if (isset($this->request->get['start_date'])) 
			{
				$url .= '&start_date=' . $this->request->get['start_date'];
				$start_date =$data['start_date']=  $this->request->get['start_date'];
			}
			else 
			{
                $start_date=$data['start_date']=date('Y-m').'-01';
            }
			if (isset($this->request->get['end_date'])) 
			{
				$url .= '&end_date=' . $this->request->get['end_date'];
				$end_date =$data['end_date']=  $this->request->get['end_date'];
			}
            else 
			{
                $end_date=$data['end_date']=date('Y-m-d');
            }
			 
			$start = ($page - 1) * 20;
			$limit = 20;
			
			$filter_data=array(
			'start'=>$start,
			'limit'=>$limit,
			//'store_id'=>$this->user->getStoreId(),
            'lat'=>$this->request->get['lat'],
            'lng'=>$this->request->get['lng'],
			'filter_date_start'=>$this->request->get['start_date'],
			'filter_date_end'=>$this->request->get['end_date']
			);
		
			$this->adminmodel('peer/peer');
			if((!empty($filter_data['lat'])) && (!empty($filter_data['lng'])))
			{
				$order_data=$this->model_peer_peer->getList($filter_data);
			}
			$products_un=array();
			$products_fav=array();
			foreach($order_data->rows as $order)
			{
				$store_data=array();
				$store_data=$this->model_peer_peer->get_store_data($order['store_id']);
				if(in_array($this->user->getStoreId(),$order['fav_store_ids']))
				{
					$favourite=1;
					$products_fav[] = array(
					'category_name' => ($order['category_name']),
					'store_id'	=> ($order['store_id']),
					'create_date'	=> (date('d-m-Y',$order['create_date']->sec)),
					'product_id'  	=>($order['product_id']),
					'store_name'  	=> ($store_data['name']),
					'negotiation'  	=> ($order['negotiation']),
					'action'  	=> ($order['action']),
					'group_id'  	=> ($order['group_id']),
					'share_detail'  => ($order['share_detail']),
					'quantity'      => ($order['quantity']),
					'product_name'  => ($order['product_name']),
					'offer_price'  	=> ($order['offer_price']),
					'category_id'   => ($order['category_id']),
					'validate'  	=> (date('d-m-Y',$order['validate']->sec)),
					'user_id'       => ($order['user_id']),
					'lat'  	        => ($order['lat']),
					'lng'  	        => ($order['lng']),
					'location'      => ($order['loc']),
					'status'  	=> ($order['status']),
					'sid'  	        => ($order['sid']),
					'remarks'  	=> ($order['remarks']),
					'telephone'  	=> ($store_data['telephone']),
					'email'  	=> ($store_data['email']),
					'id'  	=> ($order['sid']),
					'favourite'  	=> ($favourite)
					);
				}
				else
				{
					$favourite=0;
					$products_un[] = array(
						'category_name' => ($order['category_name']),
						'store_id'	=> ($order['store_id']),
						'create_date'	=> (date('d-m-Y',$order['create_date']->sec)),
						'product_id'  	=> ($order['product_id']),
						'store_name'  	=> ($store_data['name']),
						'negotiation'  	=> ($order['negotiation']),
						'action'  	=> ($order['action']),
						'group_id'  	=> ($order['group_id']),
						'share_detail'  => ($order['share_detail']),
						'quantity'      => ($order['quantity']),
						'product_name'  => ($order['product_name']),
						'offer_price'  	=> ($order['offer_price']),
						'category_id'   =>($order['category_id']),
						'validate'  	=> (date('d-m-Y',$order['validate']->sec)),
						'user_id'       => ($order['user_id']),
						'lat'  	        => ($order['lat']),
						'lng'  	        => ($order['lng']),
						'location'      => ($order['loc']),
						'status'  	=> ($order['status']),
						'sid'  	   => ($order['sid']),
						'remarks'  	=> ($order['remarks']),
						'telephone' => ($store_data['telephone']),
						'email'  	=> ($store_data['email']),
						'id'  	=> ($order['sid']),
						'favourite'  	=>($favourite)
					);
				}
				$data['products']=array_merge($products_fav,$products_un);
	
			}
			$total_suppliers =(int)($order_data->num_rows);//exit;
			
			$data['token']=$this->session->data['token'];
			
			/*pagination*/
			$pagination = new Pagination();
			$pagination->total = $total_suppliers;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('pos/peer', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);
			
			$data['pagination'] = $pagination->render();
			
			$data['results'] = sprintf($this->language->get('text_pagination'), ($total_suppliers) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_suppliers - $this->config->get('config_limit_admin'))) ? $total_suppliers : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_suppliers, ceil($total_suppliers / $this->config->get('config_limit_admin')));
			if (isset($this->session->data['error'])) 
            {
				$data['error_warning'] = $this->session->data['error'];
                unset($this->session->data['error']);
            } 
            else 
            {
				$data['error_warning'] = '';
            }

            if (isset($this->session->data['success'])) 
            {
				$data['success'] = $this->session->data['success'];
                unset($this->session->data['success']);
            } 
            else 
            {
				$data['success'] = '';
            }
			/*pagination*/
			//print_r($products_un);exit;
			$this->response->setOutput($this->load->view('default/template/peer/peer_list.tpl',$data));
		}
		/*---------------------Add supplier function starts here--------------*/
		
		public function add_sale()
		{
			$this->adminmodel('peer/peer');
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) 
			{ 
				if (isset($this->request->get['pagetittle'])) 
				{
					$url .= '&pagetittle=' . $this->request->get['pagetittle'];
				}
				$this->request->post['group_id']=11;
				$this->request->post['loc']=array("lon"=>(float)$this->request->post['lng'],"lat"=>(float)$this->request->post['lat']);
				$this->request->post['store_id']=$this->user->getStoreId();
				$this->request->post['store_name']= $this->config->get('config_name');
				$this->request->post['user_id']=$this->user->getId();
				$now = time(); // or your date as well
				$your_date = strtotime($this->request->post['validate']);
				$datediff = $your_date-$now ;

				$number_days=round($datediff / (60 * 60 * 24));
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
				//print_r($this->request->post);
				//exit;
				$supplier_data=$this->model_peer_peer->submit_order($this->request->post);
				
				$this->session->data['success'] = 'Sale to Retailer added sucessfully';
				$this->response->redirect($this->url->link('pos/peer', 'token=' . $this->session->data['token'].'&pagetittle=Sale to Retailer' . $url, 'SSL'));
			}
			else
			{ 
				$data['token']=$this->session->data['token'];
				$this->adminmodel('catalog/category');
				$data['categories']=$this->model_catalog_category->getCategories();
				if (isset($this->request->get['pagetittle'])) 
				{
					$url .= '&pagetittle=' . $this->request->get['pagetittle'];
				}
				$data['action'] = $this->url->link('pos/peer/add_sale', 'token=' . $this->session->data['token'] . $url, true);
				$data['cancel'] = $this->url->link('pos/peer', 'token=' . $this->session->data['token'].'&pagetittle=Sale to Retailer' . $url, true);
				$data['header'] = $this->load->controller('common/header');
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['footer'] = $this->load->controller('common/footer');
				
				$this->response->setOutput($this->load->view('default/template/peer/peer_form.tpl', $data));
			
			}
			
		}
		
		
		public function autocomplete() 
		{
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->adminmodel('purchaseorder/purchase_order');
			

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				
				'start'        => 0,
				'limit'        => $limit
			);
			if(!empty($filter_name))
			{
				$results = $this->model_purchaseorder_purchase_order->getProducts($filter_data);
			}
			//print_r($results);
			foreach ($results as $result) { //print_r($result);
				
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
                    'hstn'=>$result['HSTN'],
                    'price'      => number_format((float)$result['price_tax'], 2, '.', ''),
                    'product_tax_type'=>$result['tax_class_name'],
                    'price_wo_t'=>number_format((float)$result['price'], 2, '.', ''),
                    'product_tax_rate'=>number_format((float)($result['price_tax']-$result['price']), 2, '.', '')
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
    function addtofavourite()
    {        
        $log=new Log("peer-".date('Y-m-d').".log");
        
		$log->write('addtofavourite called by web');
        
        $log->write($this->request->get);
        $data=array();
       
		$data['store_id']=$this->user->getStoreId();
		$data['sid']=$this->request->get['peer_id'];
		
        $this->adminmodel('peer/peer');
        $log->write("model");
        $log->write($data);
        $datas=array();
        if(!empty($data['sid']) )
        {
            $log->write("in if");
           
            $prdid = $this->model_peer_peer->addtofavourite($data); 
            
            $datas=1;
        }        
        else
        {
            $log->write("in else");
            
			$datas=0;
        }
        $this->response->setOutput(json_encode($datas,JSON_UNESCAPED_UNICODE));
           
                    
    }
	function remove_favourite()
    {        
        $log=new Log("peer-".date('Y-m-d').".log");
        
		$log->write('remove_favourite called by web');
        
        $log->write($this->request->get);
        $data=array();
       
		$data['store_id']=$this->user->getStoreId();
		$data['sid']=$this->request->get['peer_id'];
		
        $this->adminmodel('peer/peer');
        $log->write("model");
        $log->write($data);
        $datas=array();
        if(!empty($data['sid']) )
        {
            $log->write("in if");
           
            $prdid = $this->model_peer_peer->remove_favourite($data); 
            
            $datas=1;
      
        }        
        else
        {
            $log->write("in else");
            
			$datas=0;
        }
        $this->response->setOutput(json_encode($datas,JSON_UNESCAPED_UNICODE));
           
                   
    }
		
	}
?>