<?php
class ModelPosDiscount extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		$this->language->load('total/total');
		$discount = $this->session->data['discount'];
		if(isset($discount) && (!empty($discount)))
		{
		$total_data[] = array(
			'code'       => 'discount',
			'title'      => 'Discount',
			'text'       =>  $this->currency->format(-$discount),
			'value'      => -$discount,
			'sort_order' => $this->config->get('discount_sort_order')
		);
		$total -= $discount;
		}
	}
}
?>