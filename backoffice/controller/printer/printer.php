<?php
class ControllerPrinterPrinter extends Controller {
	private $error = array();

	public function index() {
            
		$this->load->language('printer/printer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('printer/printer');

		$this->getList();
	}

	public function add() {
		$this->load->language('printer/printer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('printer/printer');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) 
		{
                     $fol =mkdir(DIR_UPLOAD."printer_doc/". 0777, true);
                         if (!empty($this->request->files['image']['name'])) 
                         {
                                $file_n=explode('.',$this->request->files['image']['name']);
                                $file_ext=end($file_n);
                                $file_upload_printer=date('Y_m_d_h_i_s')."_1.".$file_ext;
                                $path=DIR_UPLOAD."printer_doc/" .$file_upload_printer;
                                move_uploaded_file($this->request->files['image']['tmp_name'], $path);    
                                $this->request->post["image"]=$file_upload_printer;    
                         }
                         else 
                         {
                             $this->request->post["image"]=$this->request->post['image_h'];  
                         }
                         
                         
                          $fol =mkdir(DIR_UPLOAD."printer_doc/". 0777, true);
                         if (!empty($this->request->files['image1']['name'])) 
                         {
                                $file_n=explode('.',$this->request->files['image1']['name']);
                                $file_ext=end($file_n);
                                $file_upload_printer1=date('Y_m_d_h_i_s')."_2.".$file_ext;
                                $path=DIR_UPLOAD."printer_doc/" .$file_upload_printer1;
                                move_uploaded_file($this->request->files['image1']['tmp_name'], $path);    
                                $this->request->post["image1"]=$file_upload_printer1; 
                         }
                         else 
                         {
                             $this->request->post["image1"]=$this->request->post['image1_h'];  
                         }

                            $fol =mkdir(DIR_UPLOAD."printer_doc/". 0777, true);
                         if (!empty($this->request->files['image2']['name'])) 
                         {
                                $file_n=explode('.',$this->request->files['image2']['name']);
                                $file_ext=end($file_n);
                                $file_upload_printer2=date('Y_m_d_h_i_s')."_3.".$file_ext;
                                $path=DIR_UPLOAD."printer_doc/" .$file_upload_printer2;
                                move_uploaded_file($this->request->files['image2']['tmp_name'], $path);    
                                $this->request->post["image1"]=$file_upload_printer2; 
                         }
                         else 
                         {
                             $this->request->post["image2"]=$this->request->post['image2_h'];  
                         }



			$this->model_printer_printer->addprinter($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('printer/printer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
	        $this->load->language('printer/printer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('printer/printer');
                
                // $this->request->get['store_id'];
                
                 $printer_id=$this->request->get['printer_id'];
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
                    
                    $fol =mkdir(DIR_UPLOAD."printer_doc/". 0777, true);
                         if (!empty($this->request->files['image']['name'])) 
                         {
                                $file_n=explode('.',$this->request->files['image']['name']);
                                $file_ext=end($file_n);
                                $file_upload_printer=date('Y_m_d_h_i_s')."_1.".$file_ext;
                                $path=DIR_UPLOAD."printer_doc/" .$file_upload_printer;
                                move_uploaded_file($this->request->files['image']['tmp_name'], $path);    
                                $this->request->post["image"]=$file_upload_printer;    
                         }
                         else 
                         {
                             $this->request->post["image"]=$this->request->post['image_h'];  
                         }
                         
                         
                          $fol =mkdir(DIR_UPLOAD."printer_doc/". 0777, true);
                         if (!empty($this->request->files['image1']['name'])) 
                         {
                                $file_n=explode('.',$this->request->files['image1']['name']);
                                $file_ext=end($file_n);
                                $file_upload_printer1=date('Y_m_d_h_i_s')."_2.".$file_ext;
                                $path=DIR_UPLOAD."printer_doc/" .$file_upload_printer1;
                                move_uploaded_file($this->request->files['image1']['tmp_name'], $path);    
                                $this->request->post["image1"]=$file_upload_printer1; 
                         }
                         else 
                         {
                             $this->request->post["image1"]=$this->request->post['image1_h'];  
                         }
                         
                         
                          $fol =mkdir(DIR_UPLOAD."printer_doc/". 0777, true);
                         if (!empty($this->request->files['image2']['name'])) 
                         {
                                $file_n=explode('.',$this->request->files['image2']['name']);
                                $file_ext=end($file_n);
                                $file_upload_printer2=date('Y_m_d_h_i_s')."_3.".$file_ext;
                                $path=DIR_UPLOAD."printer_doc/" .$file_upload_printer2;
                                move_uploaded_file($this->request->files['image2']['tmp_name'], $path);    
                                $this->request->post["image2"]=$file_upload_printer2; 
                         }
                         else  
                         {
                             $this->request->post["image2"]=$this->request->post['image2_h'];  
                         }
                    
			$this->model_printer_printer->editprinter($printer_id,$this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('printer/printer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('printer/printer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('printer/printer');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $p_id) {
				$this->model_printer_printer->deleteprinter($p_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('printer/printer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
            
         
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'l.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] =   array();

		$data['breadcrumbs'][] =   array(
			'text' =>  $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] =   array(
			'text' =>  $this->language->get('heading_title'),
			'href' =>  $this->url->link('printer/printer', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('printer/printer/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('printer/printer/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['location'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);
                $ret_data=$this->model_printer_printer->getprinterdetails($filter_data);
                
	        $location_total = $ret_data->num_rows;
 
		$results = $ret_data->rows;
  
		foreach ($results as $result) {
                   
			$data['printers'][] =   array(
                         
				'printer_id' => $result['printer_id'],
				'name'        => $result['name'],
				'model'     => $result['model'],
				'edit'        => $this->url->link('printer/printer/edit', 'token=' . $this->session->data['token'] . '&printer_id=' . $result['printer_id'] . $url, 'SSL')
			);
                      // print_r($datas);
                     
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_address'] = $this->language->get('column_address');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('location/functional_area', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_address'] = $this->url->link('location/functional_area', 'token=' . $this->session->data['token'] . '&sort=address' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $location_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('location/functional_area', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($location_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($location_total - $this->config->get('config_limit_admin'))) ? $location_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $location_total, ceil($location_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
                
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('printer/printer_list.tpl', $data));
	}

	protected function getForm() {
            
            $this->load->language('printer/printer');
           
           $this->load->model('printer/printer');
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['zone_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_default'] = $this->language->get('text_default');
		$data['entry_price'] = $this->language->get('entry_price');

		$data['entry_mn'] = $this->language->get('entry_mn');
                $data['entry_model'] = $this->language->get('entry_model');
                $data['entry_name'] = $this->language->get('entry_name');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_character'] = $this->language->get('entry_character');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_color'] = $this->language->get('entry_color');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_item'] = $this->language->get('entry_item');
		$data['entry_warranty'] = $this->language->get('entry_warranty');

		$data['entry_mh'] = $this->language->get('entry_mh');
		$data['entry_ma'] = $this->language->get('entry_ma');
		$data['entry_mail'] = $this->language->get('entry_mail');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = '';
		}
                
                
                if (isset($this->error['price'])) {
			$data['error_price'] = $this->error['price'];
		} else {
			$data['error_price'] = '';
		}
                
                
                 if (isset($this->error['manufacturer_name'])) {
			$data['error_mn'] = $this->error['manufacturer_name'];
		} else {
			$data['error_mn'] = '';
		}
                
                 if (isset($this->error['model'])) {
			$data['error_model'] = $this->error['model'];
		} else {
			$data['error_model'] = '';
		}
                 if (isset($this->error['character'])) {
			$data['error_character'] = $this->error['character'];
		} else {
			$data['error_character'] = '';
		}
                 if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = '';
		}
                 if (isset($this->error['color'])) {
			$data['error_color'] = $this->error['color'];
		} else {
			$data['error_color'] = '';
		}
                 if (isset($this->error['item'])) {
			$data['error_item'] = $this->error['item'];
		} else {
			$data['error_item'] = '';
		}
                 if (isset($this->error['warranty'])) {
			$data['error_warranty'] = $this->error['warranty'];
		} else {
			$data['error_warranty'] = '';
		}
                 if (isset($this->error['manufacturer_helpdesk'])) {
			$data['error_mh'] = $this->error['manufacturer_helpdesk'];
		} else {
			$data['error_mh'] = '';
		}
                 if (isset($this->error['mail'])) {
			$data['error_mail'] = $this->error['mail'];
		} else {
			$data['error_mail'] = '';
		}
                if (isset($this->error['manufacturer_address'])) {
			$data['error_ma'] = $this->error['manufacturer_address'];
		} else {
			$data['error_ma'] = '';
		}
                if (isset($this->error['image'])) {
			$data['error_image'] = $this->error['image'];
		} else {
			$data['error_image'] = '';
		}


                if (isset($this->error['image1'])) {
			$data['error_image1'] = $this->error['image1'];
		} else {
			$data['error_image1'] = '';
		}
                if (isset($this->error['image'])) {
			$data['error_image1'] = $this->error['image1'];
		} else {
			$data['error_image2'] = '';
		}
                 if (isset($this->error['image2'])) {
			$data['error_image2'] = $this->error['image2'];
		} else {
			$data['error_image2'] = '';
		}

                

		

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('printer/printer', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['printer_id'])) {
			$data['action'] = $this->url->link('printer/printer/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('printer/printer/edit', 'token=' . $this->session->data['token'] .  '&printer_id=' . $this->request->get['printer_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('printer/printer', 'token=' . $this->session->data['token'] . $url, 'SSL');
                if (isset($this->request->get['printer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$printer_info = $this->model_printer_printer->getprinterinfo($this->request->get['printer_id']);
                       //print_r($printer_info);
		}
		
		$data['token'] = $this->session->data['token'];

		

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($printer_info)) {
			$data['name'] = $printer_info['name'];
		} else {
			$data['name'] =   '';
		}

		if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (!empty($printer_info)) {
			$data['description'] = $printer_info['description'];
		} else {
			$data['description'] = '';
		}

		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} elseif (!empty($printer_info)) {
			  $data['price'] = $printer_info['price'];
		} else {
			$data['price'] = '';
		}
                if (isset($this->request->post['manufacturer_name'])) {
			$data['manufacturer_name'] = $this->request->post['manufacturer_name'];
		} elseif (!empty($printer_info)) {
			  $data['manufacturer_name'] = $printer_info['manufacturer_name'];
		} else {
			$data['manufacturer_name'] = '';
		}
                
                
                 if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (!empty($printer_info)) {
			  $data['model'] = $printer_info['model'];
		} else {
			$data['model'] = '';
		}
                
                
                if (isset($this->request->post['character'])) {
			$data['character'] = $this->request->post['character'];
		} elseif (!empty($printer_info)) {
			$data['character'] = $printer_info['character'];
		} else {
			$data['character'] = '';
		}
		
                
                 if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($printer_info)) {
			$data['width'] = $printer_info['width'];
		} else {
			$data['width'] = '';
		}
		
                
                 if (isset($this->request->post['color'])) {
			$data['color'] = $this->request->post['color'];
		} elseif (!empty($printer_info)) {
			$data['color'] = $printer_info['color'];
		} else {
			$data['color'] = '';
		}
		
                
                
                 if (isset($this->request->post['item'])) {
			$data['item'] = $this->request->post['item'];
		} elseif (!empty($printer_info)) {
			$data['item'] = $printer_info['item'];
		} else {
			$data['item'] = '';
		}
		
                
                 if (isset($this->request->post['warranty'])) {
			$data['warranty'] = $this->request->post['warranty'];
		} elseif (!empty($printer_info)) {
			$data['warranty'] = $printer_info['warranty'];
		} else {
			$data['warranty'] = '';
		}
		
                
                
                 if (isset($this->request->post['manufacturer_helpdesk'])) {
			$data['manufacturer_helpdesk'] = $this->request->post['manufacturer_helpdesk'];
		} elseif (!empty($printer_info)) {
			$data['manufacturer_helpdesk'] = $printer_info['manufacturer_helpdesk'];
		} else {
			$data['manufacturer_helpdesk'] = '';
		}
		
                
                
                 if (isset($this->request->post['mail'])) {
			$data['mail'] = $this->request->post['mail'];
		} elseif (!empty($printer_info)) {
			$data['mail'] = $printer_info['mail'];
		} else {
			$data['mail'] = '';
		}
		
                
                
                 if (isset($this->request->post['manufacturer_address'])) {
			$data['manufacturer_address'] = $this->request->post['manufacturer_address'];
		} elseif (!empty($printer_info)) {
			$data['manufacturer_address'] = $printer_info['manufacturer_address'];
		} else {
			$data['manufacturer_address'] = '';
		}

                 if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($printer_info)) {
			$data['image'] = $printer_info['image'][0];
		} else {
			$data['image'] = '';
		}
                 if (isset($this->request->post['image1'])) {
			$data['image1'] = $this->request->post['image1'];
		} elseif (!empty($printer_info)) {
			$data['image1'] = $printer_info['image'][1];
		} else {
			$data['image1'] = '';
		}

		if (isset($this->request->post['image2'])) {
			$data['image2'] = $this->request->post['image2'];
		} elseif (!empty($printer_info)) {
			$data['image2'] = $printer_info['image'][2];
		} else {
			$data['image2'] = '';
		}


                
               
		
                
                
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
 
		$this->response->setOutput($this->load->view('printer/printer_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'printer/printer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 132)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ((utf8_strlen($this->request->post['description']) < 3) || (utf8_strlen($this->request->post['description']) > 128)) {
			$this->error['description'] = $this->language->get('error_description');
		}
                
                if ((utf8_strlen($this->request->post['price']) < 3) || (utf8_strlen($this->request->post['price']) > 128)) {
			$this->error['price'] = $this->language->get('error_price');
		}
                
                if ((utf8_strlen($this->request->post['manufacturer_name']) < 3) || (utf8_strlen($this->request->post['manufacturer_name']) > 128)) {
			$this->error['manufacturer_name'] = $this->language->get('error_mn');
		}
                
                
                if ((utf8_strlen($this->request->post['model']) < 3) || (utf8_strlen($this->request->post['model']) > 128)) {
			$this->error['model'] = $this->language->get('error_model');
		}
                
                
                if ((utf8_strlen($this->request->post['character']) < 3) || (utf8_strlen($this->request->post['character']) > 128)) {
			$this->error['character'] = $this->language->get('error_character');
		}
                
                
                if ((utf8_strlen($this->request->post['width']) < 3) || (utf8_strlen($this->request->post['width']) > 128)) {
			$this->error['width'] = $this->language->get('error_width');
		}
                
                
                if ((utf8_strlen($this->request->post['color']) < 3) || (utf8_strlen($this->request->post['color']) > 128)) {
			$this->error['color'] = $this->language->get('error_color');
		}
                
                
                if ((utf8_strlen($this->request->post['item']) < 3) || (utf8_strlen($this->request->post['item']) > 128)) {
			$this->error['item'] = $this->language->get('error_item');
		}
                
                
                if ((utf8_strlen($this->request->post['warranty']) < 3) || (utf8_strlen($this->request->post['warranty']) > 128)) {
			$this->error['warranty'] = $this->language->get('error_warranty');
		}
                
                
                if ((utf8_strlen($this->request->post['manufacturer_helpdesk']) < 3) || (utf8_strlen($this->request->post['manufacturer_helpdesk']) > 128)) {
			$this->error['manufacturer_helpdesk'] = $this->language->get('error_mh');
		}
                
                
                if ((utf8_strlen($this->request->post['mail']) < 3) || (utf8_strlen($this->request->post['mail']) > 128)) {
			$this->error['mail'] = $this->language->get('error_mail');
		}
                
                
                if ((utf8_strlen($this->request->post['manufacturer_address']) < 3) || (utf8_strlen($this->request->post['manufacturer_address']) > 128)) {
			$this->error['manufacturer_address'] = $this->language->get('error_ma');
		}
                
                 if (utf8_strlen(!$this->request->post['image'])) {
			$this->error['image'] = $this->language->get('error_image');
		}

              
                
               
                
                
               
                
                
               
		

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'printer/printer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        
      
      
      
}