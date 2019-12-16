<?php
class ControllerCatalogStorequantity extends Controller {
private static function compare_tax_rates ($a, $b) {
		return ($a['priority'] < $b['priority']) ? -1 : 1;
	}
	function json_taxrates () {
		header('Content-Type: application/json');
		$result = array();
		
		$class_id = (int) $this->request->post['tax_class_id'];
		
		if (!isset($class_id)) {
			$result['status'] = 'error';
			$result['error'] = 'Invalid input';
		}
		else {
			$this->load->model('catalog/product');
			$tax_rates = $this->model_catalog_product->getTaxRates($class_id);
			usort($tax_rates, array( __CLASS__, "compare_tax_rates"));
			$result['status'] = 'ok';
			$result['tax_rates'] = $tax_rates;
		}
		$this->response->setOutput(json_encode($result));
	}
	private $error = array();

	
	public function edit() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/storequantity');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                        //print_r($this->request->post);exit;
			$return_value=$this->model_catalog_storequantity->editProduct($this->request->get['product_id'], $this->request->post);
                        if($return_value)
                        {
                            $this->session->data['success'] = $this->language->get('text_success');
                        }
                        else
                        {
                            $this->session->data['error'] = 'Error in Updation. Please try again !';
                        }
			$url = '';
			if (isset($this->request->get['hstn'])) {
				$url .= '&hstn=' . urlencode(html_entity_decode($this->request->get['hstn'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
                        //echo $url;exit;
			$this->response->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
		$this->getForm();
	}

	protected function getForm() {
                $this->load->model('catalog/product');
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		
		$data['text_select'] = $this->language->get('text_select');
		$data['text_percent'] = $this->language->get('text_percent');
		$data['text_amount'] = $this->language->get('text_amount');

		$data['entry_name'] = $this->language->get('entry_name');
		
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_stock_status'] = $this->language->get('entry_stock_status');
                $data['entry_price_tax'] = $this->language->get('entry_price_tax'); //EDIT
		$data['entry_price'] = $this->language->get('entry_price');
                $data['config_tax_included'] = $this->config->get('config_tax_included');
		$data['entry_tax_class'] = $this->language->get('entry_tax_class');
		
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
		$data['entry_download'] = $this->language->get('entry_download');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		$data['button_image_add'] = $this->language->get('button_image_add');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['button_recurring_add'] = $this->language->get('button_recurring_add');

                $data['tab_quantity']= $this->language->get('tab_quantity');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
                $url = '';
		if (isset($this->request->get['hstn'])) {
			$url .= '&hstn=' . urlencode(html_entity_decode($this->request->get['hstn'], ENT_QUOTES, 'UTF-8'));
		} 
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

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
			'text' => 'Products (Store quantity)',
			'href' => $this->url->link('catalog/store_quantity', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['product_id'])) {
			//$data['action'] = $this->url->link('catalog/product/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('catalog/store_quantity/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $this->request->get['product_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		}

		$data['token'] = $this->session->data['token'];

		
		
		
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->post['product_store'])) {
			$data['product_store'] = $this->request->post['product_store'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_store'] = $this->model_catalog_product->getProductStores($this->request->get['product_id']);
		} else {
			$data['product_store'] = array(0);
		}

                //store 
                if (isset($this->request->post['product_store'])) {
			$data['product_store_quantity'] = $this->request->post['product_store'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_store_quantity'] = $this->model_catalog_product->getProductStoresQuantity($this->request->get['product_id']);
		} else {
			$data['product_store_quantity'] = array(0);
		}
                
		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['tax_class_id'])) {
			$data['tax_class_id'] = $this->request->post['tax_class_id'];
		} elseif (!empty($product_info)) {
			$data['tax_class_id'] = $product_info['tax_class_id'];
		} else {
			$data['tax_class_id'] = 0;
		}

                $tax_rates = $this->model_catalog_product->getTaxRates($data['tax_class_id']);
                usort($tax_rates, array( __CLASS__, "compare_tax_rates"));
                $data['js_rates'] = json_encode($tax_rates);
                $data['json_tax_rates'] = $this->url->link('catalog/product/json_taxrates', 'token=' . $this->session->data['token'] . $url, 'SSL');		
                $prd_data=$this->model_catalog_product->getProductName($this->request->get['product_id']);
                $data["product_name"] = $prd_data['model'];
                $data["base_price"] = $prd_data['price'];
                $data["purchase_price"] = $prd_data['purchase_price'];
                $data["wholesale_price"] = $prd_data['wholesale_price'];
                //print_r($data);
                
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
                
		$this->response->setOutput($this->load->view('catalog/store_quantity_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}


		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}