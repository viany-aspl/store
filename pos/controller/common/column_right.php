<?php
class ControllerCommonColumnRight extends Controller {
	public function index() {
		

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/column_right.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/column_right.tpl', $data);
		} else {
			return $this->load->view('default/template/common/column_right.tpl', $data);
		}
	}
}