<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
define('RETAILER_TOKEN_ID', 					OBN_RETAILER_TOKEN);
		
class order_feed {	
	protected $order_id;
	protected $order_product_ids;
	protected $order_info;
	protected $order_product_info;
	protected $order_total;
	protected $order_tax;
	public $xml;
	public $payload_id;
	
	public function __construct($cur_order_id = '', $cur_order_product_ids = array()){
		$this->order_id = $cur_order_id;
		$this->order_product_ids = $cur_order_product_ids;
		$order_total = 0;
		$order_tax = 0;
		$this->order_info = array();
		$this->fetch_orders_db_values();
		$this->order_product_info = array();
		$this->fetch_orders_products_db_values();
			
	}
	
	private function fetch_orders_db_values(){
		$sql = tep_db_query("select
							customers_id,
							customers_name,
							customers_company,
							customers_street_address,
							customers_suburb,
							customers_city,
							customers_postcode,
							customers_state,
							customers_country,
							customers_telephone,
							customers_email_address,
							delivery_name,
							delivery_company,
							delivery_street_address,
							delivery_suburb,
							delivery_city,
							delivery_postcode,
							delivery_state,
							delivery_country,
							billing_name,
							billing_company,
							billing_street_address,
							billing_suburb,
							billing_city,
							billing_postcode,
							billing_state,
							billing_country,
							date_purchased from orders where orders_id='" . $this->order_id . "'");
		$order_info = tep_db_fetch_array($sql);
		$this->order_info['customers_id'] 				= $order_info['customers_id'];  
		$this->order_info['customers_name'] 			= $order_info['customers_name'];
		$this->order_info['customers_company'] 			= $order_info['customers_company'];
		$this->order_info['customers_street_address'] 	= $order_info['customers_street_address'];
		$this->order_info['customers_suburb'] 			= $order_info['customers_suburb'];
		$this->order_info['customers_city'] 			= $order_info['customers_city'];
		$this->order_info['customers_postcode'] 		= $order_info['customers_postcode'];
		$this->order_info['customers_state'] 			= $order_info['customers_state'];
		$this->order_info['customers_country'] 			= $order_info['customers_country'];
		$this->order_info['customers_telephone'] 		= $order_info['customers_telephone'];
		$this->order_info['customers_email_address'] 	= $order_info['customers_email_address'];
		$this->order_info['delivery_name'] 				= $order_info['delivery_name'];
		$this->order_info['delivery_company'] 			= $order_info['delivery_company'];
		$this->order_info['delivery_street_address'] 	= $order_info['delivery_street_address'];
		$this->order_info['delivery_suburb'] 			= $order_info['delivery_suburb'];
		$this->order_info['delivery_city'] 				= $order_info['delivery_city'];
		$this->order_info['delivery_postcode'] 			= $order_info['delivery_postcode'];
		$this->order_info['delivery_state'] 			= $order_info['delivery_state'];
		$this->order_info['delivery_country'] 			= $order_info['delivery_country'];
		$this->order_info['billing_name'] 				= $order_info['billing_name'];
		$this->order_info['billing_company'] 			= $order_info['billing_company'];
		$this->order_info['billing_street_address'] 	= $order_info['billing_street_address'];
		$this->order_info['billing_suburb'] 			= $order_info['billing_suburb'];
		$this->order_info['billing_city'] 				= $order_info['billing_city'];
		$this->order_info['billing_postcode'] 			= $order_info['billing_postcode'];
		$this->order_info['billing_state'] 				= $order_info['billing_state'];
		$this->order_info['billing_country'] 			= $order_info['billing_country'];
		$this->order_info['date_purchased'] 			= $order_info['date_purchased'];
	}
	
	private function fetch_orders_products_db_values(){
		foreach($this->order_product_ids as $order_product_id){
			$this->order_product_info[$order_product_id] = array();
			$sql = tep_db_query("select
								a.products_id,
								a.products_model,
								a.products_name,
								a.products_price,
								a.final_price,
								a.products_tax,
								a.products_quantity
								from orders_products a where orders_products_id='" . $order_product_id . "'");
			$op_info = tep_db_fetch_array($sql);
			$this->order_product_info[(string)$order_product_id]['orders_products_id']	= $order_product_id;
			$this->order_product_info[(string)$order_product_id]['products_id'] 		= $op_info['products_id'];
			$this->order_product_info[(string)$order_product_id]['products_model'] 		= $op_info['products_model'];
			$this->order_product_info[(string)$order_product_id]['products_name'] 		= $op_info['products_name'];
			$this->order_product_info[(string)$order_product_id]['products_price'] 		= $op_info['products_price'];
			$this->order_product_info[(string)$order_product_id]['final_price'] 		= $op_info['final_price'];
			$this->order_product_info[(string)$order_product_id]['products_tax'] 		= $op_info['products_tax'];
			$this->order_product_info[(string)$order_product_id]['products_quantity'] 	= $op_info['products_quantity'];
			$this->order_product_info[(string)$order_product_id]['attributes'] 			= array();
			
			$this->order_tax += ($op_info['products_tax'] * $op_info['products_quantity']);
			$this->order_total += (($op_info['final_price'] + $op_info['products_tax']) * $op_info['products_quantity']);
			
			$sql_01 = tep_db_query("select 
									products_options,
									products_options_values,
									options_values_price,
									price_prefix
									from orders_products_attributes where orders_id='" . 
									$this->order_id . "' and orders_products_id='" . $order_product_id . "'");
			while($attr_info = tep_db_fetch_array($sql_01)){
				$this->order_product_info[(string)$order_product_id]['attrubutes']['products_options'] 			= $attr_info['products_options'];
				$this->order_product_info[(string)$order_product_id]['attrubutes']['products_options_values'] 	= $attr_info['products_options_values'];
				$this->order_product_info[(string)$order_product_id]['attrubutes']['options_values_price'] 		= $attr_info['options_values_price'];
				$this->order_product_info[(string)$order_product_id]['attrubutes']['price_prefix'] 				= $attr_info['price_prefix'];
			}
		}
	}
	
	private function set_shipping_details(&$ship_agent, &$ship_method){
		// sample value: Shipping Fees (United States) (Standard):
		// 30 | 39
		$sql = tep_db_query("select title from orders_total where orders_id='" . $this->order_id . "' and class='ot_shipping'");
		if (tep_db_num_rows($sql)){
			$sql_info = tep_db_fetch_array($sql);
			$title = trim($sql_info['title']);
			
			$pos_1 = strpos($title, '(');
			$temp = $pos_1;
			while ($pos_1 !== false){
				$temp = $pos_1;
				$pos_1 = strpos($title, '(', $pos_1+1);
			}
			$pos_1 = $temp;
			$pos_2 = strpos($title, ')', $pos_1);
			
			$ship_agent = trim(substr($title, 0, $pos_1));
			$ship_method = trim(substr($title, $pos_1+1, $pos_2 - $pos_1 - 1));
		}
	}
	
	public function get_order_feed(){
		$this->payload_id = time();
		
		$this->xml = '<?xml version="1.0" encoding="UTF-8"?>' .
					 '<Order>' .
					 	'<Header>' . 
					 		'<RetailerTokenID>' .
					 			RETAILER_TOKEN_ID .
					 		'</RetailerTokenID>' .
					 		'<GenerationDate>' .
					 			date('Y-m-d H:i') .
					 		'</GenerationDate>' .
					 		'<PayloadID>' .
							 	$this->payload_id .  
					 		'</PayloadID>' .
						'</Header>' . 
						'<OrderDetails>' .
							'<OrderID>' . 
								$this->order_id . 
							'</OrderID>' . 
							'<DatePurchased>' .
								 date('Y-m-d H:i', strtotime($this->order_info['date_purchased'])) .
							'</DatePurchased>' .
							'<BuyerDetails>' .
								'<BuyerName>' . 
									htmlspecialchars($this->order_info['customers_name']) . 
								'</BuyerName>' .
								'<BuyerCompany>' .
									 htmlspecialchars($this->order_info['customers_company']) .
								'</BuyerCompany>' .
								'<BuyerStreetAddress>' .
									 htmlspecialchars($this->order_info['customers_street_address']) .
								'</BuyerStreetAddress>' .
								'<BuyerStreetAddress2>' .
									 htmlspecialchars($this->order_info['customers_suburb']) .
								'</BuyerStreetAddress2>' .
								'<BuyerCity>' .
									 htmlspecialchars($this->order_info['customers_city']) .
								'</BuyerCity>' .
								'<BuyerPostCode>' .
									 htmlspecialchars($this->order_info['customers_postcode']) .
								'</BuyerPostCode>' .
								'<BuyerState>' .
									 $this->get_state_code($this->order_info['customers_state']) .
								'</BuyerState>' .
								'<BuyerCountry>' .
									 htmlspecialchars($this->order_info['customers_country']) .
								'</BuyerCountry>' .
								'<BuyerTelephone>' .
									 htmlspecialchars($this->order_info['customers_telephone']) .
								'</BuyerTelephone>' .
								'<BuyerEmailAddress>' .
									 $this->order_info['customers_email_address'] .
								'</BuyerEmailAddress>' .
							'</BuyerDetails>' .
							'<DeliveryDetails>' .
								'<DeliveryName>' .
									 htmlspecialchars($this->order_info['delivery_name']) .
								'</DeliveryName>' .
								'<DeliveryCompany>' .
									 htmlspecialchars($this->order_info['delivery_company']) .
								'</DeliveryCompany>' .
								'<DeliveryStreetAddress>' .
									 htmlspecialchars($this->order_info['delivery_street_address']) .
								'</DeliveryStreetAddress>' .
								'<DeliveryStreetAddress2>' .
									 htmlspecialchars($this->order_info['delivery_suburb']) .
								'</DeliveryStreetAddress2>' .
								'<DeliveryCity>' .
									 htmlspecialchars($this->order_info['delivery_city']) .
								'</DeliveryCity>' .
								'<DeliveryPostCode>' .
									 htmlspecialchars($this->order_info['delivery_postcode']) .
								'</DeliveryPostCode>' .
								'<DeliveryState>' .
									 $this->get_state_code($this->order_info['delivery_state']) .
								'</DeliveryState>' .
								'<DeliveryCountry>' .
									 htmlspecialchars($this->order_info['delivery_country']) .
								'</DeliveryCountry>' .
							'</DeliveryDetails>' .
							'<BillingDetails>' .
								'<BillingName>' .
									 htmlspecialchars($this->order_info['billing_name']) .
								'</BillingName>' .
								'<BillingCompany>' .
									 htmlspecialchars($this->order_info['billing_company']) .
								'</BillingCompany>' .
								'<BillingStreetAddress>' .
									 htmlspecialchars($this->order_info['billing_street_address']) .
								'</BillingStreetAddress>' .
								'<BillingStreetAddress2>' .
									 htmlspecialchars($this->order_info['billing_suburb']) .
								'</BillingStreetAddress2>' .
								'<BillingCity>' .
									 htmlspecialchars($this->order_info['billing_city']) .
								'</BillingCity>' .
								'<BillingPostCode>' .
									 htmlspecialchars($this->order_info['billing_postcode']) .
								'</BillingPostCode>' .
								'<BillingState>' .
									 $this->get_state_code($this->order_info['billing_state']) .
								'</BillingState>' .
								'<BillingCountry>' .
									 htmlspecialchars($this->order_info['billing_country']) .
								'</BillingCountry>' .
							'</BillingDetails>' .
						'</OrderDetails>' .
						'<Products>';

		foreach($this->order_product_info as $product){
			$this->xml .=	'<Product>' .
								'<OrderProductID>' . 
									$this->order_id . '_' . $product['orders_products_id'] . 
								'</OrderProductID>' .
								'<ProductModel>' . 
									$product['products_model'] . 
								'</ProductModel>' .
								'<ProductName>' . 
									//$product['products_name'] .
									htmlspecialchars($product['products_name']) . 
								'</ProductName>' .
								'<ProductPrice currencyCode="USD">' . 
									number_format($product['products_price'], 2, '.', '') .
								'</ProductPrice>' .
								/*'<ProductFinalPrice>' . 
									number_format($product['final_price'], 2, '.', '')  .
								'</ProductFinalPrice>' .
								'<ProductTax>' . 
									number_format($product['products_tax'], 2, '.', '') .
								'</ProductTax>' .*/
								'<ProductQuantity>' . 
									$product['products_quantity'] .
								'</ProductQuantity>' .
								'<ProductAttributes>';
								
			foreach($product['attributes'] as $attribute){
				$this->xml .=		'<ProductAttribute>' .
										'<Option>' . 
											htmlspecialchars($attributes['products_options']) .
										'</Option>' .
										'<Value>' . 
											htmlspecialchars($attributes['products_options_values']) .
										'</value>' .
										'<Price>' . 
											number_format($attributes['options_values_price'], 2, '.', '') .
										'</Price>' .
										'<PricePrefix>' . 
											$attributes['price_prefix'] .
										'</PricePrefix>' .
									'<ProductAttribute>';
			}
								
			$this->xml .=		'</ProductAttributes>' .
							'</Product>';
		}
		$ship_agent = '';
		$ship_method = '';
		$this->set_shipping_details($ship_agent, $ship_method);
		$this->xml .=	'</Products>' .
						'<ShippingDetails>' .
                                    //'<ShippingAgent>' . $ship_agent . '</ShippingAgent>'  .
                                    //'<ShippingMethod>' . $ship_method . '</ShippingMethod>'  .
                                    '<ShippingAgent>' . htmlspecialchars($ship_agent) . '</ShippingAgent>'  .
                                    '<ShippingMethod>' . htmlspecialchars($ship_method) . '</ShippingMethod>'  .
						'</ShippingDetails>' .
					 '</Order>';
		/*$this->xml = '<?xml version="1.0" encoding="UTF-8"?>' . 
			   '<' . NODE_ORDER_FEED_ROOT . '>' .
			   		'<' . NODE_RETAILER_TOKEN . '>' .
			   			RETAILER_TOKEN_ID . 
			   		'</' . NODE_RETAILER_TOKEN . '>' .
			   		'<' . NODE_ORDER . '>' .
			   			$this->order_id .
			   		'</' . NODE_ORDER . '>' .
			   		'<' . NODE_ORDER_DATE . '>' .
			   			date('Y-m-d H:i', strtotime($this->order_info['date_purchased'])) .
			   		'</' . NODE_ORDER_DATE . '>' .
			   		'<' . NODE_CUSTOMER . '>' .
			   			'<' . NODE_CUSTOMER_NAME . '>' .
			   				$this->order_info['customers_name'] . 
			   			'</' . NODE_CUSTOMER_NAME . '>' .
			   			'<' . NODE_CUSTOMER_TELEPHONE . '>' .
			   				$this->order_info['customers_telephone'] . 
			   			'</' . NODE_CUSTOMER_TELEPHONE . '>' .
			   		'</' . NODE_CUSTOMER . '>' .
			   		'<' . NODE_BILL_TO . '>' .
			   			'<' . NODE_BILL_TO_NAME . '>' .
			   				$this->order_info['billing_name'] . 
			   			'</' . NODE_BILL_TO_NAME . '>' .
			   			'<' . NODE_BILL_TO_ADDRESS1 . '>' .
			   				$this->order_info['billing_street_address'] .
			   			'</' . NODE_BILL_TO_ADDRESS1 . '>' .
			   			'<' . NODE_BILL_TO_ADDRESS2 . '>' .
			   				$this->order_info['billing_suburb'] .
			   			'</' . NODE_BILL_TO_ADDRESS2 . '>' .
			   			'<' . NODE_BILL_TO_CITY . '>' .
			   				$this->order_info['billing_city'] .
			   			'</' . NODE_BILL_TO_CITY . '>' .
			   			'<' . NODE_BILL_TO_STATE . '>' .
			   				$this->order_info['billing_state'] .
			   			'</' . NODE_BILL_TO_STATE . '>' .
			   			'<' . NODE_BILL_TO_ZIP . '>' .
			   				$this->order_info['billing_postcode'] .
			   			'</' . NODE_BILL_TO_ZIP . '>' .
			   			'<' . NODE_BILL_TO_COUNTRY . '>' .
			   				$this->order_info['billing_country'] .
			   			'</' . NODE_BILL_TO_COUNTRY . '>' .
			   		'</' . NODE_BILL_TO . '>' .
			   		'<' . NODE_SHIP_TO . '>' .
			   			'<' . NODE_BILL_TO_NAME . '>' .
			   				$this->order_info['delivery_name'] . 
			   			'</' . NODE_BILL_TO_NAME . '>' .
			   			'<' . NODE_BILL_TO_ADDRESS1 . '>' .
			   				$this->order_info['delivery_street_address'] .
			   			'</' . NODE_BILL_TO_ADDRESS1 . '>' .
			   			'<' . NODE_BILL_TO_ADDRESS2 . '>' .
			   				$this->order_info['delivery_suburb'] .
			   			'</' . NODE_BILL_TO_ADDRESS2 . '>' .
			   			'<' . NODE_BILL_TO_CITY . '>' .
			   				$this->order_info['delivery_city'] .
			   			'</' . NODE_BILL_TO_CITY . '>' .
			   			'<' . NODE_BILL_TO_STATE . '>' .
			   				$this->order_info['delivery_state'] .
			   			'</' . NODE_BILL_TO_STATE . '>' .
			   			'<' . NODE_BILL_TO_ZIP . '>' .
			   				$this->order_info['delivery_postcode'] .
			   			'</' . NODE_BILL_TO_ZIP . '>' .
			   			'<' . NODE_BILL_TO_COUNTRY . '>' .
			   				$this->order_info['delivery_country'] .
			   			'</' . NODE_BILL_TO_COUNTRY . '>' .
			   		'</' . NODE_SHIP_TO . '>' .
			   		'<' . NODE_PRODUCTS . '>';
			   		
		//foreach($this->order_product_info as )
			   			
		$this->xml .=		'</' . NODE_PRODUCTS . '>' .
  						'</' . NODE_ORDER_FEED_ROOT . '>';*/
	}
	
		private function get_state_code($state_name){
	 if (strlen($state_name)==2){
		return $state_name;
	     }
	else{
		$resp = '';
		$sql = tep_db_query("select zone_code from zones where zone_name='" . str_replace("'", "''", $state_name) . "'");
		if (tep_db_num_rows($sql)){
			$sql_info = tep_db_fetch_array($sql);
			$resp = $sql_info['zone_code'];
		}
		return $resp;
		}
	}
	
	public function move_order_feed_to_obn(){
		global $messageStack;
		/*$cur_timestamp = time();
		$file_path = DIR_FS_OBN_FEED . RETAILER_TOKEN_ID . '/outgoing/';
		$file_name = $cur_timestamp . '.xml';
		echo $file_path . $file_name . '<br>';
  		$handle = fopen($file_path . $file_name , 'x');
  		chmod($file_path . $file_name, 0777);
  		fwrite($handle, $this->xml);
  		fclose($handle);
  		
  		$conn_id = ftp_connect(SUPPLIER_FTP_SERVER);
  		$login_result = ftp_login($conn_id, SUPPLIER_FTP_USERNAME, SUPPLIER_FTP_PASSWORD);
  		ftp_chdir($conn_id, SUPPLIER_FTP_ORDERS_DIR);
  		$contents = ftp_nlist($conn_id, ".");
  		$retailer_dir_exists = 0;
  		foreach($contents as $file){
			if ($file==RETAILER_TOKEN_ID){
				$retailer_dir_exists = 1;
				break;
			}
		}
		if (!$retailer_dir_exists){
			ftp_mkdir($conn_id, RETAILER_TOKEN_ID);
		}
		ftp_chdir($conn_id, RETAILER_TOKEN_ID);
		$contents = ftp_nlist($conn_id, ".");
		$retailer_dir_exists = 0;
  		foreach($contents as $file){
			if ($file==SUPPLIER_FTP_DIR_IN){
				$retailer_dir_exists = 1;
				break;
			}
		}
		if (!$retailer_dir_exists){
			ftp_mkdir($conn_id, SUPPLIER_FTP_DIR_IN);
		}
		ftp_chdir($conn_id, SUPPLIER_FTP_DIR_IN);
		echo file_exists($file_path . $file_name) . '<br>';
		$fp = fopen($file_path . $file_name, 'r');
		
		ftp_fput($conn_id, $file_name, $fp, FTP_ASCII);
		
		fclose($fp);
		ftp_close($conn_id);*/
		
		$file_path = DIR_FS_OBN_FEED . RETAILER_TOKEN_ID . '/outgoing/';
		if (!file_exists(DIR_FS_OBN_FEED . RETAILER_TOKEN_ID . '/')){
			mkdir(DIR_FS_OBN_FEED . RETAILER_TOKEN_ID . '/', 0777);
		}
		if (!file_exists($file_path)){
			mkdir($file_path, 0777);
		}
		$file_name = time() . '.xml';
		//echo $file_path . $file_name . '<br>';
  		$handle = fopen($file_path . $file_name , 'x');
  		chmod($file_path . $file_name, 0777);
  		fwrite($handle, $this->xml);
  		fclose($handle);
  		
  		$handle = fopen($file_path . $file_name , 'r');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, OBN_FILE_ORDER_FEED_URL);
		curl_setopt($ch, CURLOPT_PUT, 1);
		curl_setopt($ch, CURLOPT_UPLOAD, 1);
		curl_setopt($ch, CURLOPT_REFERER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_INFILE, $handle);
		curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file_path . $file_name));
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		if ($resp = curl_exec($ch)) {		
		curl_close($ch);
		} else {
		$error = curl_error($ch);
		$messageStack->add_session($error, 'warning');	
		}		
		fclose($handle);
		if ($resp !== false){
			if ($resp=='SUCCESS'){
				$sql_array = array('orders_id' => $this->order_id,
								   'payload_id' => $this->payload_id,
								   'orders_status' => ORDER_STATUS_FEED_MOVED,
								   'date_added' => 'now()');
		  		tep_db_perform('feeds_orders_retailer_to_obn', $sql_array);
		  		$order_feed_id = tep_db_insert_id();
		  		foreach($this->order_product_ids as $order_product_id){
		  			$sql_array = array('order_feed_id' => $order_feed_id,
									   'orders_products_feed_reference' => $this->order_id . '_' . $order_product_id,
					  				   'orders_products_id' => $order_product_id,
					  				   'orders_products_status' => ORDER_STATUS_FEED_MOVED,
									   'date_added' => 'now()');
		  			tep_db_perform('feeds_orders_products_retailer_to_obn', $sql_array);
		  		}
		  		$messageStack->add_session('Order feed successfully moved to OBN', 'success');
			} else {
				$messageStack->add_session('Error encountered while moving order feed to OBN', 'warning');
			}
		}
		else {
			$messageStack->add_session('Error encountered while moving order feed to OBN', 'warning');
		}
	}
}



?>