<?php
class Pos_cart {
	private $config;
	private $db;
	private $data = array();

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		$this->db = $registry->get('db');
		$this->tax = $registry->get('tax');
		$this->weight = $registry->get('weight');
                $this->user = $registry->get('user');

		if (!isset($this->session->data['cart']) || !is_array($this->session->data['cart'])) {
			$this->session->data['cart'] = array();
		}
	}

	public function getProducts() 
	{
        $log=new Log("poscarttax-".date('Y-m-d').".log"); 
		$log->write($this->data);
        $log->write($this->session->data);
		$log->write($this->session->data['config_store_id']);
		if (!$this->data) 
		{
			foreach ($this->session->data['cart'] as $key => $quantity) 
			{
				$product = unserialize(base64_decode($key));
				$log->write($product['product_id']);
				$product_id = $product['product_id'];
				$category_id = $product['category_id'];
                $billtype=$product['billtype'];
				$appprice=$product['price'];
				$stock = true;
				// Options				
				$options = array();				
				// Profile				
				$recurring_id = 0;				                    
                    $sort_array=array('name'=>1);
                     $lookup=array(
                  'from' => 'oc_product_to_store',
                'localField' => 'product_id',
                'foreignField' => 'product_id',
                'as' => 'opst'  
                );
				$match=array();
                $log->write("billtype");
                $log->write($billtype);
                    $match['product_id']=(int)$product_id;
                    if(empty($billtype))
					{
						if(!empty($this->user->getStoreId()))
						{
							$match['opst.store_id']=(int)$this->user->getStoreId();
						}
						else 
						{
							$match['opst.store_id']=(int)$this->session->data['config_store_id'];
						}
                    } 
					else 
					{
							
								$match['opst.store_id']=(int)$this->session->data['config_store_id'];//0
							
                    }
                    //$match['language_id']=(int)$this->config->get('config_language_id');
                    //$match['status']=true;
                     $log->write($match);
		$product_query = $this->db->query("join",DB_PREFIX . "product",$lookup,'$opst',$match,'','','','','',$sort_array);
   //$this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."product_to_store  s on s.product_id=p.product_id WHERE p.product_id = '" . (int)$product_id . "' AND s.store_id='".$this->user->getStoreId()."' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
                                $log->write($product_query);
				if ($product_query->num_rows) {
					$option_price = 0;
					$option_points = 0;
					$option_weight = 0;

					$option_data = array();
                                        $product_query->row=$product_query->row[0];
                                        //$log->write($product_query->row);
                                        if(isset($product_query->row['opst']['store_price'])&& $product_query->row['opst']['store_price']=='0.0000' )
                                            {
					$price = $product_query->row['price'];
                                        }else{
                                            $price = $product_query->row['opst']['store_price'];
                                        }
					$price=$appprice;
					// Product Discounts
					$discount_quantity = 0;

					foreach ($this->session->data['cart'] as $key_2 => $quantity_2) {
						$product_2 = (array)unserialize(base64_decode($key_2));

						if ($product_2['product_id'] == $product_id) {
							$discount_quantity += $quantity_2;
						}
					}
					// Stock
					if (!$product_query->row['opst']['quantity'] || ($product_query->row['opst']['quantity'] < $quantity)) {
						$stock = false;
					}
					$this->data[$key] = array(
						'key'             => $key,
						'product_id'      => $product_query->row['product_id'],
						'name'            => $product_query->row['name'],
						'model'           => $product_query->row['model'],
						'shipping'        => $product_query->row['shipping'],
						'image'           => $product_query->row['image'],
						'category_id'           => $category_id,
						'option'          => $option_data,
						'download'        => $download_data,
						'quantity'        => $quantity,
						'minimum'         => $product_query->row['minimum'],
						'subtract'        => $product_query->row['subtract'],
						'stock'           => $stock,
						'price'           => ($price + $option_price),
						'total'           => ($price + $option_price) * $quantity,
						'reward'          => $reward * $quantity,
						'points'          => ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $quantity : 0),
						'tax_class_id'    => $product_query->row['tax_class_id'],
						'weight'          => ($product_query->row['weight'] + $option_weight) * $quantity,
						'weight_class_id' => $product_query->row['weight_class_id'],
						'length'          => $product_query->row['length'],
						'width'           => $product_query->row['width'],
						'height'          => $product_query->row['height'],
						'length_class_id' => $product_query->row['length_class_id'], 
						'recurring'       => $recurring
					);
				} else {
					$this->remove($key);
				}
			}
		}
                $log->write($this->data);
		return $this->data;
	}

	public function getRecurringProducts() {
		$recurring_products = array();

		foreach ($this->getProducts() as $key => $value) {
			if ($value['recurring']) {
				$recurring_products[$key] = $value;
			}
		}

		return $recurring_products;
	}

	public function add($product_id, $qty = 0, $option = array(), $recurring_id = 0,$billtype=0,$price,$category_id=0) {
		$this->data = array();

		$product['product_id'] = (int)$product_id;
                $product['billtype']=(int)$billtype;
				$product['price']=$price;
				$product['category_id'] = (int)$category_id;
		if ($option) 
		{
			$product['option'] = $option;
		}

		if ($recurring_id) 
		{
			$product['recurring_id'] = (int)$recurring_id;
		}

		$key = base64_encode(serialize($product));

		if ((int)$qty && ((int)$qty > 0)) {
			if (!isset($this->session->data['cart'][$key])) {
				$this->session->data['cart'][$key] = (int)$qty;
			} else {
				$this->session->data['cart'][$key] += (int)$qty;
			}
		}
	}

	public function update($key, $qty) {
		$this->data = array();

		if ((int)$qty && ((int)$qty > 0) && isset($this->session->data['cart'][$key])) {
			$this->session->data['cart'][$key] = (int)$qty;
		} else {
			$this->remove($key);
		}
	}

	public function remove($key) {
		$this->data = array();

		unset($this->session->data['cart'][$key]);
	}

	public function clear() {
		$this->data = array();

		$this->session->data['cart'] = array();
	}

	public function getWeight() {
		$weight = 0;

		foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
			}
		}

		return $weight;
	}

	public function getSubTotal() {
		$total = 0;

		foreach ($this->getProducts() as $product) {
			$total += $product['total'];
		}

		return $total;
	}

	public function getTaxes() {
		$tax_data = array();

		foreach ($this->getProducts() as $product) {
			if ($product['tax_class_id']) {
				$tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
						$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
					} else {
						$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
					}
				}
			}
		}

		return $tax_data;
	}

	public function getTotal() {
		$total = 0;

		foreach ($this->getProducts() as $product) {
			$total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
		}

		return $total;
	}

	public function countProducts() {
		$product_total = 0;

		$products = $this->getProducts();

		foreach ($products as $product) {
			$product_total += $product['quantity'];
		}

		return $product_total;
	}

	public function hasProducts() {
		return count($this->session->data['cart']);
	}

	public function hasRecurringProducts() {
		return count($this->getRecurringProducts());
	}

	public function hasStock() {
		$stock = true;

		foreach ($this->getProducts() as $product) {
			if (!$product['stock']) {
				$stock = false;
			}
		}

		return $stock;
	}

	public function hasShipping() {
		$shipping = false;

		foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				$shipping = true;

				break;
			}
		}

		return $shipping;
	}

	public function hasDownload() {
		$download = false;

		foreach ($this->getProducts() as $product) {
			if ($product['download']) {
				$download = true;

				break;
			}
		}

		return $download;
	}
}