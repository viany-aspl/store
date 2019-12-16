<?php
class ModelPosTax extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		$log=new Log("tax-".date('Y-m-d').".log");
		$log->write('getTotal called in pos/tax');
		$log->write($total_data);
		$log->write($total);
		$log->write($taxes);
		foreach ($taxes as $key => $value) {
					
			if ($value > 0) {
				
				$log->write($key);
				$log->write($value);
				$total_data[] = array(
					'code'       => 'tax',
					'title'      => $this->tax->getRateName($key), 
					'text'       => $this->currency->format($value),
					'value'      => number_format((float)$value, 2, '.', ''),//$value,
					'sort_order' => $this->config->get('tax_sort_order')
				);

				$total += $value;
				$log->write($total);
				$log->write($total_data);
			}
		}
	}
}
?>