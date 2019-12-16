<?php
class ModelPosReward extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) 
                {
                $log=new Log("order-reward-".date('Y-m-d').".log");
                $log->write($this->session->data['reward']);
		if (isset($this->session->data['reward'])) 
                    {
			$this->language->load('total/reward');

			$points = $this->session->data['points'];//$this->customer->getRewardPoints();
                        $log->write('points');
                        $log->write($points);
			if ($this->session->data['reward'] <= $points) 
                            {
				$discount_total = $this->session->data['reward'];

				$points_total = 0;

				/*foreach ($this->cart->getProducts() as $product) 
                                    {
                                    $log->write('product');
                                    //$log->write($product);
					if ($product['reward']) 
                                            {
						$points_total += $product['reward'];
					}
				}	

				$points = min($points, $points_total);

				foreach ($this->cart->getProducts() as $product) 
                                    {
					$discount = 0;

					if ($product['reward']) {
						$discount = $product['total'] * ($this->session->data['reward'] / $points_total);

						if ($product['tax_class_id']) {
							$tax_rates = $this->tax->getRates($product['total'] - ($product['total'] - $discount), $product['tax_class_id']);

							foreach ($tax_rates as $tax_rate) {
								if ($tax_rate['type'] == 'P') {
									$taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
								}
							}	
						}
					}

					$discount_total += $discount;
				}*/

				$total_data[] = array(
					'code'       => 'reward',
					'title'      => sprintf($this->language->get('text_reward'), $this->session->data['reward']),
					'text'       => $this->currency->format(-$discount_total),
					'value'      => -$discount_total,
					'sort_order' => $this->config->get('reward_sort_order')
				);

				$total -= $discount_total;
			} 
		}
	}

	public function confirm($order_info, $order_total) {
		$this->language->load('total/reward');

		$points = 0;

		$start = strpos($order_total['title'], '(') + 1;
		$end = strrpos($order_total['title'], ')');

		if ($start && $end) {  
			$points = substr($order_total['title'], $start, $end - $start);
		}	

		if ($points) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET customer_id = '" . (int)$order_info['customer_id'] . "', description = '" . $this->db->escape(sprintf($this->language->get('text_order_id'), (int)$order_info['order_id'])) . "', points = '" . (float)-$points . "', date_added = NOW()");				
                    
                     $rdata=array(
                        
                        'order_id'=>(int)$order_info['order_id'],
                        'store_id'=> (int)$order_info['store_id'],
                        'customer_id'=>(int)$order_info['customer_id'],
                        'description'=>$this->db->escape(sprintf($this->language->get('text_order_id'), (int)$order_info['order_id'])),
                        'type'=>$this->db->escape('Redeam'),
                        'points'=> (float)-$points,
                        'date_added'=> new MongoDate(strtotime(date('Y-m-d H:i:s')))
                    
                    );
              
                $this->db->query('insert','oc_customer_reward',$rdata); 
		}
	}		
}
?>