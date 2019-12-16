<?php
  
class ControllerCatalogUpload extends Controller {
    public function index() {
        
             include_once '../system/library/PHPReadExcel.php';
              $this->load->model('catalog/upload');    
	     
              if (($this->request->server['REQUEST_METHOD'] == 'POST') ){
                if (is_uploaded_file($this->request->files['tfile']['tmp_name'])) {
                $content = file_get_contents($this->request->files['tfile']['tmp_name']);
               
                
                } else {
                    $content = false;
                } 
             
                if($content){
                   
                    $read=new PHPReadExcel($this->request->files['tfile']['tmp_name'],$this->request->files['tfile']['name']);
                 $category_id=$this->request->post['category_id'];
                    //print_r($category_id);
                    $arr=$read->getSheetData();
               unset($arr['0']);
                    $Res= $this->model_catalog_upload->readExcel($arr,$category_id);
            
              } else {
                $this->error['warning'] = $this->language->get('error_empty');
                }
               }
                $data['heading_title'] = $this->language->get('Product Upload');
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

                $data['column_price_tax'] = 'Price+Tax';//$this->language->get('column_price_tax');
		$data['entry_name'] = $this->language->get('Product Upload');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_category'] = $this->language->get('Category');

		$data['button_copy'] = $this->language->get('button_copy');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

                $data['config_tax_included'] = $this->config->get('config_tax_included');

		$data['token'] = $this->session->data['token'];

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_price'] = $filter_price;
		$data['filter_quantity'] = $filter_quantity;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;
                $data['categories'] = $this->model_catalog_upload->getCategories();
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		//echo "here";
		$this->response->setOutput($this->load->view('catalog/product_list_upload.tpl', $data));
	}

	
}