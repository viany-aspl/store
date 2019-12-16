<?php

//require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
require_once(DIR_SYSTEM . '/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');
error_reporting(0);

class ControllerPurchasePurchaseOrder extends Controller 
{

    public function check_ware_house_quantity() {
        //print_r($this->request->post);exit;
        $total_product = count($this->request->post['ware_houses']);
        for ($a = 0; $a < $total_product; $a++) {
            $product_id = $this->request->post['product_id'][$a];
            $product_name = $this->request->post['product_name'][$a];
            $p_qnty = $this->request->post['receive_quantity'][$a];
            $ware_house = $this->request->post['ware_houses'][$a];

            $this->load->model('purchase/purchase_order');
            $data_qnty = $this->model_purchase_purchase_order->check_ware_house_quantity($ware_house, $product_id, $p_qnty);

            if ($data_qnty == "0") {
                echo 'There is not sufficent quantity of ' . $product_name . ' at ware house';
                return;
            }
        }
    }

    public function index() 
	{
        //set the title of the page
        $this->document->setTitle("Unnati Krishi Kendra Requisition List");

        $data['column_left'] = $this->load->controller('common/column_left');
        /* $data['column_right'] = $this->load->controller('common/column_right');
          $data['content_top'] = $this->load->controller('common/content_top');
          $data['content_bottom'] = $this->load->controller('common/content_bottom'); */
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $url = '';
        $this->load->model('user/user');
        $user_info = $this->model_user_user->getUser($this->user->getId());
        $data['user_group_id'] = $user_info['user_group_id'];

        if (isset($this->request->get['filter_id'])) {
            $url .= '&filter_id=' . $this->request->get['filter_id'];
        }

        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if (isset($this->request->get['filter_store'])) {
            $url .= '&filter_store=' . $this->request->get['filter_store'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => "Unnati Krishi Kendra Requisition",
            'href' => $this->url->link('purchase/purchase_order', 'token=' . $this->session->data['token'] . $url, true)
        );

        $data['add'] = $this->url->link('purchase/purchase_order/add', 'token=' . $this->session->data['token'] . $url, true);
        $data['delete'] = $this->url->link('purchase/purchase_order/delete', 'token=' . $this->session->data['token'] . $url, true);
        $data['filter'] = $this->url->link('purchase/purchase_order/filter', 'token=' . $this->session->data['token'] . $url, true);
        /* getting the list of the orders */

        if (isset($this->session->data['error_warning'])) {
            $data['error_warning'] = $this->session->data['error_warning'];
            $this->session->data['error_warning'] = '';
        } else {
            $data['error_warning'] = '';
        }
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            $this->session->data['success'] = '';
        } else {
            $data['success'] = '';
        }

        $this->load->model('purchase/purchase_order');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }
        if (isset($this->request->get['filter_id'])) {
            $filter_id = $this->request->get['filter_id'];
        }

        if (isset($this->request->get['filter_date_start'])) {
            $filter_date_start = $this->request->get['filter_date_start'];
        } else {
            $filter_date_start = date('Y-m') . "-01";
        }
        if (isset($this->request->get['filter_date_end'])) {
            $filter_date_end = $this->request->get['filter_date_end'];
        } {
            $filter_date_end = date('Y-m-d');
        }
        //if (($this->request->get['filter_status'])) {
        $filter_status = $this->request->get['filter_status'];
        //}
        if (isset($this->request->get['filter_store'])) {
            $filter_store = $this->request->get['filter_store'];
        }

        $filter_data = array(
            'filter_id' => $filter_id,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_status' => $filter_status,
            'filter_store' => $filter_store,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );
        $retdata = $this->model_purchase_purchase_order->getList($filter_data);
        $order_list = $retdata->rows;
        //$data['order_list']=array();
        $this->load->model('user/user');
        $this->load->model('setting/store');
        $this->load->model('purchase/supplier');
        foreach ($order_list as $order) 
		{
            $supplier_name = '';
            $orderby = $this->model_user_user->getUser($order['user_id']);
            $store_name = $this->model_setting_store->getStore($order['store_id']);
            //print_r($orderby);
            $order_id = $order['id'];
            $order_information = $order['po_product']; //$this->model_purchase_purchase_order->view_order_details($order_id);
            $total_product = count($order_information['products']);
            if ($total_product > 1) 
			{
                $ware_house_name = 'Multiple';
            } else {

                if ($order['po_receive_details']['supplier_id'] != '-1') { //print_r($order['po_receive_details']['supplier_id']);
                    $ware_house_name = $this->model_purchase_supplier->get_supplier_by_key(array('supplier_id' => $order['po_receive_details']['supplier_id']));
                    $supplier_name = $ware_house_name->row['first_name'] . ' ' . $ware_house_name->row['last_name'];
                }
            }

            $data['order_list'][] = array(
                'id' => $order_id,
                'order_date' => date('Y-m-d', ($order['order_date']->sec)),
                'order_sup_send' => $order['order_sup_send'],
                'delete_bit' => $order['delete_bit'],
                'user_id' => $order['user_id'],
                'receive_date' => date('Y-m-d', ($order['receive_date']->sec)),
                'receive_bit' => $order['receive_bit'],
                'pending_bit' => $order['pending_bit'],
                'pre_supplier_bit' => $order['pre_supplier_bit'],
                'order_status_id' => $order['status'],
                'canceled_by' => $order['canceled_by'],
                'canceled_message' => $order['canceled_message'],
                'store_id' => $order['store_id'],
                'potential_date' => date('Y-m-d', ($order['potential_date']->sec)),
                'driver_otp' => $order['driver_otp'],
                'driver_mobile' => $order['driver_mobile'],
                'firstname' => $orderby['firstname'],
                'lastname' => $orderby['lastname'],
                'store_name' => $store_name['name'],
                'creditlimit' => $order['creditlimit'],
                'currentcredit' => $order['currentcredit'],
                'supplier_name' => $supplier_name,
                'product' => $order['po_product']['name'],
                'quantity' => $order['po_product']['quantity'],
                'po_receive_details' => $order['po_receive_details']
            );
        }
        //print_r($order_list);
        $total_orders = $retdata->num_rows;

        //getting total orders
        $data['view'] = $this->url->link('purchase/purchase_order/view_order_details', 'token=' . $this->session->data['token'] . $url, true);
        $data['receive'] = $this->url->link('purchase/purchase_order/receive_order', 'token=' . $this->session->data['token'] . $url, true);
		$data['stores'] = $this->model_setting_store->getStores();

        /* pagination */
        $pagination = new Pagination();
        $pagination->total = $total_orders;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('purchase/purchase_order', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($total_orders) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_orders - $this->config->get('config_limit_admin'))) ? $total_orders : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_orders, ceil($total_orders / $this->config->get('config_limit_admin')));
        $data['filter_id'] = $filter_id;
        $data['from'] = $filter_date_start;
        $data['to'] = $filter_date_end;
        $data['status'] = $filter_status;
        $data['filter_store'] = $filter_store;
        $data['token'] = $this->request->get['token'];
        $this->load->model('setting/store');
       
        /* pagination */
        $data['my_custom_text'] = "This is purchase order page.";
        $this->response->setOutput($this->load->view('purchase/purchase_order_list.tpl', $data));
    }

    public function download_excel() 
	{
        $this->load->model('purchase/purchase_order');
        if (isset($this->request->get['filter_id'])) {
            $filter_id = $this->request->get['filter_id'];
        }

        if (isset($this->request->get['filter_date_start'])) {
            $filter_date_start = $this->request->get['filter_date_start'];
        } else {
            $filter_date_start = date('Y-m') . "-01";
        }
        if (isset($this->request->get['filter_date_end'])) {
            $filter_date_end = $this->request->get['filter_date_end'];
        } {
            $filter_date_end = date('Y-m-d');
        }
        //if (($this->request->get['filter_status'])) {
        $filter_status = $this->request->get['filter_status'];
        //}
        if (isset($this->request->get['filter_store'])) {
            $filter_store = $this->request->get['filter_store'];
        }
        $page = 1;
        $filter_data = array(
            'filter_id' => $filter_id,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_status' => $filter_status,
            'filter_store' => $filter_store,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => 1000
        );

        $order_list = $this->model_purchase_purchase_order->getList($filter_data);
        //$results=$this->model_purchase_purchase_order->download_excel($filter_data);
        $this->load->model('user/user');
        $this->load->model('setting/store');
        $this->load->model('purchase/supplier');
        foreach ($order_list->rows as $order) {
            $supplier_name = '';
            $orderby = $this->model_user_user->getUser($order['user_id']);
            $store_name = $this->model_setting_store->getStore($order['store_id']);
            //print_r($orderby);
            $order_id = $order['id'];
            $order_information = $order['po_product']; //$this->model_purchase_purchase_order->view_order_details($order_id);
            $total_product = count($order_information['products']);
            if ($total_product > 1) {
                $ware_house_name = 'Multiple';
            } else {
                if ($order['po_receive_details']['supplier_id'] != '-1') { //print_r($order['po_receive_details']['supplier_id']);
                    $ware_house_name = $this->model_purchase_supplier->get_supplier_by_key(array('supplier_id' => $order['po_receive_details']['supplier_id']));
                    $supplier_name = $ware_house_name->row['first_name'] . ' ' . $ware_house_name->row['last_name'];
                }
            }

            $data['order_list'][] = array(
                'id' => $order_id,
                'order_date' => date('Y-m-d', ($order['order_date']->sec)),
                'order_sup_send' => $order['order_sup_send'],
                'delete_bit' => $order['delete_bit'],
                'user_id' => $order['user_id'],
				'status' => $order['status'],
                'receive_date' => date('Y-m-d', ($order['receive_date']->sec)),
                'receive_bit' => $order['receive_bit'],
                'pending_bit' => $order['pending_bit'],
                'pre_supplier_bit' => $order['pre_supplier_bit'],
                'order_status_id' => $order['order_status_id'],
                'canceled_by' => $order['canceled_by'],
                'canceled_message' => $order['canceled_message'],
                'store_id' => $order['store_id'],
                'potential_date' => date('Y-m-d', ($order['potential_date']->sec)),
                'driver_otp' => $order['driver_otp'],
                'driver_mobile' => $order['driver_mobile'],
                'firstname' => $orderby['firstname'],
                'lastname' => $orderby['lastname'],
                'store_name' => $store_name['name'],
                'creditlimit' => $order['creditlimit'],
                'currentcredit' => $order['currentcredit'],
                'supplier_name' => $supplier_name,
                'product' => $order['po_product']['name'],
                'quantity' => $order['po_product']['quantity']
            );
        }

        //print_r($data['order_list']);exit;
        $file_name = "Unnati_Krishi_Kendra_Requisition_" . date('dMy') . '.xls';
        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=" . $file_name);  //File name extension was wrong
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);

        echo '<table id="example2" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
	      <th>Requisition ID</th>
                    <th>Date</th>
                    <th>Ordered By</th>
                    <th>Store Name</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
 	      <th>Supplier/Ware House</th>
	      <th>Status</th>
                </tr>
                </thead>
                <tbody>';
        $tblbody = " ";
        foreach ($data['order_list'] as $data) {


            echo '<tr> 
	      <td>' . $data['id'] . '</td>
	      <td>' . date('Y-m-d', strtotime($data['order_date'])) . '</td>
                    <td>' . $data['firstname'] . ' ' . $data['lastname'] . '</td>
                    <td>' . $data['store_name'] . '</td>
	      <td>' . $data['product'] . '</td>
                    <td>' . $data['quantity'] . '</td>
	      <td>' . $data['supplier_name'] . '</td>';
            if ($data['status'] == "0") {
                echo '<td>Pending</td>';
            }
			else if ($data['status'] == "2") {
                echo '<td>Pending</td>';
            }
			else if ($data['status'] == "3") {
                echo '<td>Cancled</td>';
            }
			else if ($data['status'] == "4") {
                echo '<td>Received</td>';
            }
			else {
                echo '<td>Pending</td>';
            }



            echo '</tr>';
        }


        echo '</tbody>
          </table>';
    }

    ///////////////////////////////////////////////////////////////////////////////////

    /* ----------------------------view_order_details function starts here------------ */

    public function view_order_details() 
	{
        $this->document->setTitle("View Order");
        $order_id = $this->request->get['order_id'];
        $data['column_left'] = $this->load->controller('common/column_left');
        /* $data['column_right'] = $this->load->controller('common/column_right');
          $data['content_top'] = $this->load->controller('common/content_top');
          $data['content_bottom'] = $this->load->controller('common/content_bottom'); */
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $url = '';

        if (isset($this->request->get['order_id'])) {
            $url .= '&order_id=' . urlencode(html_entity_decode($this->request->get['order_id'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_id'])) {
            $url .= '&filter_id=' . $this->request->get['filter_id'];
        }

        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => "Home",
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => "Unnati Krishi Kendra Requisition",
            'href' => $this->url->link('purchase/purchase_order', 'token=' . $this->session->data['token'] . $url, true)
        );

        $this->load->model('purchase/purchase_order');
        $data['order_information'] = $this->model_purchase_purchase_order->view_order_details($order_id);
        $this->load->model('user/user');
        $this->load->model('setting/store');
        $this->load->model('purchase/supplier');
        $orderby = $this->model_user_user->getUser($data['order_information']['user_id']);
        $data['orderby'] = $orderby['firstname'] . ' ' . $$orderby['lastname'];
        $store_name = $this->model_setting_store->getStore($data['order_information']['store_id']);
        $data['store_name'] = $store_name['name'];
        if (($data['order_information']['po_receive_details']['supplier_id'] != '-1') && ($data['order_information']['po_receive_details']['supplier_id'] != '0')) {
            $ware_house_name = $this->model_purchase_supplier->get_supplier_by_key(array('supplier_id' => $data['order_information']['po_receive_details']['supplier_id']));
            $data['ware_house_name'] = $ware_house_name->row['first_name'] . ' ' . $ware_house_name->row['last_name'];
        } else {
            $data['suppliers'] = $this->model_purchase_supplier->get_all_suppliersAll(array())->rows;
        }
        $data['token'] = $this->session->data['token'];
        $data['order_id'] = $data['order_information']['id'];
        $data['order_by_user_id'] = $data['order_information']['user_id'];
        $data['cancel'] = $this->url->link('purchase/purchase_order', 'token=' . $this->session->data['token'] . $url, true);
        $data['submit_action'] = $this->url->link('purchase/purchase_order/receive_order', 'token=' . $this->session->data['token'] . $url, true);
        $this->response->setOutput($this->load->view('purchase/view_order.tpl', $data));
    }

    /* ----------------------------view_order_details function ends here-------------- */

    public function receive_order() 
	{
        if (!empty($this->request->get['order_by_user_id'])) {
            $this->request->get['approved_by'] = $this->user->getId();

            $this->load->model('purchase/purchase_order');
            $data['order_receive_date'] = $this->request->get['order_receive_date'];
            $inserted = $this->model_purchase_purchase_order->insert_receive_order($this->request->get, $this->request->get['order_id']);
            $this->session->data['success'] = 'Order Accepeted successfully';
            $this->response->redirect($this->url->link('purchase/purchase_order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        } else {
            $this->session->data['error_warning'] = 'Some error occur!';
            $this->response->redirect($this->url->link('purchase/purchase_order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }
    }

    public function cancel_order() 
	{
        if (!empty($this->request->get['order_by_user_id'])) {
            $this->request->get['canceled_by'] = $this->user->getId();

            $this->load->model('purchase/purchase_order');
            $data['order_receive_date'] = $this->request->get['order_receive_date'];
            $inserted = $this->model_purchase_purchase_order->cancel_order($this->request->get, $this->request->get['order_id']);
            $this->session->data['success'] = 'Order Cancled successfully';
            $this->response->redirect($this->url->link('purchase/purchase_order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        } else {
            $this->session->data['error_warning'] = 'Some error occur!';
            $this->response->redirect($this->url->link('purchase/purchase_order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }
    }

    /* -----------------------------Receive order function ends here----------------- */

    public function insert_purchase_order() 
	{
        $data['products'] = $_POST['product'];
        $data['options'] = $_POST['options'];
        $data['option_values'] = $_POST['option_values'];
        $data['quantity'] = $_POST['quantity'];
        $data['supplier_id'] = $_POST['supplier_id'];
        $data['stores'] = $_POST['stores'];
        $data['column_left'] = $this->load->controller('common/column_left');
        /* $data['column_right'] = $this->load->controller('common/column_right');
          $data['content_top'] = $this->load->controller('common/content_top');
          $data['content_bottom'] = $this->load->controller('common/content_bottom'); */
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $data['breadcrumbs'] = array();
        $data['token'] = $this->session->data['token'];

        /* to let the user add products without options */
        for ($i = 0; $i < count($data['options']); $i++) {
            if ($data['options'][$i] == '') {
                $data['options'][$i] = '0_option';
            }
        }

        /* to let the user add products without option values */
        for ($i = 0; $i < count($data['option_values']); $i++) {
            if ($data['option_values'][$i] == '') {
                $data['option_values'][$i] = '0_optionvalue';
            }
        }

        if ((in_array("--products--", $data['products'])) || (in_array("--stores--", $data['storess'])) || (in_array("--Product Options--", $data['options'])) || (in_array("--Option Values--", $data['option_values'])) || (count($data['quantity']) != count(array_filter($data['quantity']))) || (count($data['options']) != count(array_filter($data['options']))) || (count($data['products']) != count(array_filter($data['products']))) || (count($data['option_values']) != count(array_filter($data['option_values'])))) {
            $data['form_bit'] = 0;
            $_SESSION['errors'] = "Warning: Please check the form carefully for errors!";
            /* ------------Working with data received starts----- */

            $i = 0;
            foreach ($data['products'] as $product) {
                if (strrchr($product, "_")) {
                    $product_names[$i] = explode('_', $product);
                } else {
                    $product_names[$i] = $product;
                }
                $i++;
            }
            $data['product_received'] = $product_names;
            $i = 0;
            foreach ($data['options'] as $option) {
                if (strrchr($option, "_")) {
                    $options[$i] = explode('_', $option);
                } else {
                    $options[$i] = $option;
                }
                $i++;
            }
            $data['options_received'] = $options;
            $i = 0;
            foreach ($data['option_values'] as $option_value) {
                if (strrchr($option_value, "_")) {
                    $option_values[$i] = explode('_', $option_value);
                } else {
                    $option_values[$i] = $option_value;
                }
                $i++;
            }
            $data['option_values_received'] = $option_values;
            //print_r($data['option_values_received']);
            $data['quantities_received'] = $data['quantity'];
            /* ------working with data received ends--------- */
            $this->load->model('catalog/product');
            $products = $this->model_catalog_product->getProducts();
            $i = 0;
            foreach ($products as $product) {
                $products[$i] = $product['name'];
                $product_ids[$i] = $product['product_id'];
                $i++;
            }
            $data['products'] = $products;
            $data['product_ids'] = $product_ids;



            $this->load->model('catalog/option');
            $data['options'] = $this->model_catalog_option->getOptions();
            /* $i = 0;
              foreach($data['options_received'] as $option)
              {
              $option_values[$i] = $this->model_catalog_option->getOptionValues($option[0]);
              $i++;
              } */
            $data['option_values'] = $option_values;
            $url = '';
            $data['action'] = $this->url->link('purchase/purchase_order/insert_purchase_order', 'token=' . $this->session->data['token'] . $url, true);
            $data['cancel'] = $this->url->link('purchase/purchase_order', 'token=' . $this->session->data['token'] . $url, true);
            $this->load->model('purchase/supplier');
            $data['suppliers'] = $this->model_purchase_supplier->get_total_suppliers();
            //stores
            $this->load->model('setting/store');
            $data['stores'] = $this->model_setting_store->getStores();
            $this->load->model('catalog/option');
            $data['options'] = $this->model_catalog_option->getOptions();
            $this->response->setOutput($this->load->view('purchase/purchase_order_form.tpl', $data));
            //$this->response->redirect($this->url->link('purchase/purchase_order/add', 'token=' . $this->session->data['token'] . $url, true));
        } else {
            $i = 0;
            foreach ($data['products'] as $product) {
                $product_names[$i] = explode('_', $product);
                $i++;
            }
            $data['products'] = $product_names;
            //stores
            $i = 0;
            foreach ($data['stores'] as $store) {
                $store_names[$i] = explode('_', $store);
                $i++;
            }
            $data['stores'] = $store_names;


            $i = 0;
            foreach ($data['options'] as $option) {
                if (strrchr($option, "_")) {
                    $options[$i] = explode('_', $option);
                } else {
                    $options[$i] = $option;
                }
                $i++;
            }
            $data['options'] = $options;
            $i = 0;
            foreach ($data['option_values'] as $option_value) {
                if (strrchr($option_value, "_")) {
                    $option_values[$i] = explode('_', $option_value);
                } else {
                    $option_values[$i] = $option_value;
                }
                $i++;
            }

            $data['option_values'] = $option_values;

            $this->load->model('purchase/purchase_order');
            $order_id = $this->model_purchase_purchase_order->insert_purchase_order($data);



            if (isset($this->request->post['mail_bit'])) {

                $data['company_name'] = $this->config->get('config_name'); // store name
                $data['company_title'] = $this->config->get('config_title'); // store title
                $data['company_owner'] = $this->config->get('config_owner'); // store owner name
                $data['company_email'] = $this->config->get('config_email'); // store email
                $data['company_address'] = $this->config->get('config_address'); //store address

                $this->load->model('purchase/purchase_order');
                $data['order_information'] = $this->model_purchase_purchase_order->view_order_details($order_id);


                $html = $this->load->view('purchase/mail_purchase_order.tpl', $data);

                $base_url = HTTP_CATALOG;

                $mpdf = new mPDF('c', 'A4', '', '', 5, 5, 25, 10, 5, 7);

                $header = '<div class="header"><div class="logo"><img src="' . $base_url . 'image/catalog/logo.png" /></div><div class="company"><h3>' . $data['company_name'] . '</h3></div></div><hr />';

                $mpdf->SetHTMLHeader($header, 'O', false);

                $footer = '<div class="footer"><div class="address"><b>Adress: </b>' . $data['company_address'] . '</div><div class="pageno">{PAGENO}</div></div>';

                $mpdf->SetHTMLFooter($footer);

                $mpdf->SetDisplayMode('fullpage');

                $mpdf->list_indent_first_level = 0;

                $mpdf->WriteHTML($html);

                $mpdf->Output('../orders/order.pdf', 'F');

                //mailing

                $mail = new PHPMailer();

                $body = "<p>Akshamaala Solution Pvt. Ltd.</p>";

                $mail->IsSMTP();
                $mail->Host = "mail.akshamaala.in";

                $mail->SMTPAuth = false;
                $mail->SMTPSecure = "";
                $mail->Host = "mail.akshamaala.in";
                $mail->Port = 25;
                $mail->Username = "mis@akshamaala.in";
                $mail->Password = "mismis";

                $mail->SetFrom($data['company_email'], $data['company_name']);

                $mail->AddReplyTo($data['company_email'], $data['company_name']);

                $mail->Subject = "Product Order to Supplier";

                $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

                $mail->MsgHTML($body);

                //to get the email of supplier
                $query = $this->db->query('SELECT email FROM oc_po_supplier WHERE id = ' . $data['supplier_id']);

                $address = $query->row['email'];

                $mail->AddAddress($address, "Turaab Ali");

                $file_to_attach = '../orders/order.pdf';

                $mail->AddAttachment($file_to_attach);

                if (!$mail->Send()) {
                    echo "Mailer Error: " . $mail->ErrorInfo;
                } else {
                    if (!unlink($file_to_attach)) {
                        echo ("Error deleting $file_to_attach");
                    } else {
                        echo ("Deleted $file_to_attach");
                    }
                }
            }

            if ($order_id) {
                $_SESSION['success_order_message'] = "The Order has been added";
                $this->response->redirect($this->url->link('purchase/purchase_order', 'token=' . $this->session->data['token'] . $url, true));
            }
        }
    }

    /* --------------------Insert purchase order ends here---------------------------- */
}

?>