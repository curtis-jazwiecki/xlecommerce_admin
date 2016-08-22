<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
	require('cron_application_top.php');
	
	define('ORDER_STATUS_ACKNOWLEDGED_BY_SUPPLIER',	ORDER_STATUS_ACK);
	define('ORDER_STATUS_SHIPPED_BY_SUPPLIER',		ORDER_STATUS_SHIPPED);
	define('RETAILER_FEED_DIRECTORY',				DIR_FS_OBN_FEED . OBN_RETAILER_TOKEN . '/');
	define('RETAILER_INCOMING_FOLDER',				'incoming/');
	define('RETAILER_ARCHIVES_FOLDER',				'archives/');
	define('KEYWORD_ACK_FILE',						'order_ack');
	define('KEYWORD_ASN_FILE',						'order_ship');
	
	define('NODE_HEADER',							'Header');
	define('NODE_GENERATION_DATE',					'GenerationDate');
	define('NODE_PAYLOAD_ID',						'PayloadID');
	define('NODE_RESPONSE',							'Response');
	define('ATTR_ORDER_REFERENCE',					'orderReference');
	define('ATTR_PAYLOAD_REFERENCE',				'payloadReference');
	define('NODE_ERROR',							'Error');
	define('NODE_TYPE',								'Type');
	define('NODE_SHIPPING_CHARGES',					'ShippingCharges');
	define('NODE_COMMENTS',							'Comments');
	define('NODE_DETAILS',							'Details');
	define('NODE_SHIPPING_DETAILS',					'ShippingDetails');
	define('NODE_SHIPPING_AGENT',					'ShippingAgent');
	define('NODE_SHIPMENT_TRACKING_ID',				'ShipmentTrackingID');
	define('NODE_SHIPMENT_DATE',					'ShipmentDate');
	define('NODE_PRODUCTS',							'Products');
	define('NODE_PRODUCT',							'Product');
	define('NODE_PRODUCT_MODEL',					'ProductModel');
	define('NODE_PRODUCT_QUANTITY',					'ProductQuantity');
	define('NODE_UNIT_PRICE',						'UnitPrice');
	
	
	class ack_asn_feed_handler{
		public function __construct(){
			if (!file_exists(RETAILER_FEED_DIRECTORY)){
				mkdir(RETAILER_FEED_DIRECTORY, 0777);
			}
			if (!file_exists(RETAILER_FEED_DIRECTORY . RETAILER_INCOMING_FOLDER)){
				mkdir(RETAILER_FEED_DIRECTORY . RETAILER_INCOMING_FOLDER, 0777);
			}
			if (!file_exists(RETAILER_FEED_DIRECTORY . RETAILER_ARCHIVES_FOLDER)){
				mkdir(RETAILER_FEED_DIRECTORY . RETAILER_ARCHIVES_FOLDER, 0777);
			}
		}
		
		public function init(){
			if (is_dir(RETAILER_FEED_DIRECTORY . RETAILER_INCOMING_FOLDER)){
				
				if ($dh = opendir(RETAILER_FEED_DIRECTORY . RETAILER_INCOMING_FOLDER)){
					while (($file = readdir($dh)) !== false){
						if ($file!='.' && $file!='..'){
							$pos = strpos($file, KEYWORD_ACK_FILE);
							if ($pos !== false){
								$this->handle_ack_file($file);
							}
						}
					}
				}
				
				if ($dh = opendir(RETAILER_FEED_DIRECTORY . RETAILER_INCOMING_FOLDER)){
					while (($file = readdir($dh)) !== false){
						if ($file!='.' && $file!='..'){
							$pos = strpos($file, KEYWORD_ASN_FILE);
							if ($pos !== false){
								$this->handle_asn_file($file);
							}
						}
					}
				}
				
			}
		}
		
		private function obn_payload_id_exists($payload_id){
			$sql_ = tep_db_query("select obn_order_feed_id from feeds_orders_obn_to_retailer where payload_id='" . $payload_id . "'");
			if (tep_db_num_rows($sql_)){
				return true;
			} else {
				return false;
			}
		}
		
		private function handle_ack_file($file_name){
			$xml = simplexml_load_file(RETAILER_FEED_DIRECTORY . RETAILER_INCOMING_FOLDER . $file_name);
			$generation_date = (string)$xml->{NODE_HEADER}->{NODE_GENERATION_DATE};
			$payload_id = (string)$xml->{NODE_HEADER}->{NODE_PAYLOAD_ID};
			$order_ref = (string)$xml->{NODE_RESPONSE}[ATTR_ORDER_REFERENCE];
			$payload_ref = (string)$xml->{NODE_RESPONSE}[ATTR_PAYLOAD_REFERENCE];
			$error = (string)$xml->{NODE_RESPONSE}->{NODE_ERROR};
			$type = (string)$xml->{NODE_RESPONSE}->{NODE_TYPE};
			$shipping_charges = (string)$xml->{NODE_RESPONSE}->{NODE_SHIPPING_CHARGES};
			$comments = (string)$xml->{NODE_RESPONSE}->{NODE_COMMENTS};
			
			echo "$generation_date $payload_id $order_ref $payload_ref $type $shipping_charges $comments<br>";
			if (!$this->obn_payload_id_exists($payload_id)){
				$sql = tep_db_query("select order_feed_id, orders_id from feeds_orders_retailer_to_obn where payload_id='" . $payload_ref . "'");
				if (tep_db_num_rows($sql)){
					$sql_info = tep_db_fetch_array($sql);
					$retailer_feed_id = $sql_info['order_feed_id'];
					$orders_id = $sql_info['orders_id'];
				} else {
					$retailer_feed_id = '0';
					$orders_id = '0';
				}
				
				if (empty($error)){
					$sql_array = array('retailer_order_feed_id' => $retailer_feed_id,
										'payload_id' => $payload_id,
										'generation_date' => $generation_date,
										'is_ack_feed' => '1',
										'response_type' => $type,
										'response_comments' => $comments,
										'date_added' => 'now()');
					tep_db_perform('feeds_orders_obn_to_retailer', $sql_array);
					
					if ($orders_id){
						/*$sql_array = array('orders_id' => $orders_id,
											'orders_status_id' => ORDER_STATUS_ACKNOWLEDGED_BY_SUPPLIER,
											'date_added' => 'now()',
											'customer_notified' => '0');
						tep_db_perform('orders_status_history', $sql_array);*/
						tep_db_query("update feeds_orders_retailer_to_obn set orders_status='" . ORDER_STATUS_ACKNOWLEDGED_BY_SUPPLIER . "' where order_feed_id='" . $retailer_feed_id . "' and orders_id='" . $orders_id . "'");
						tep_db_query("update feeds_orders_products_retailer_to_obn set orders_products_status='" . ORDER_STATUS_ACKNOWLEDGED_BY_SUPPLIER . "' where order_feed_id='" . $retailer_feed_id . "'");
					}
				} else {
					$sql = tep_db_query("select count(retailer_order_feed_id) as count from feeds_orders_obn_to_retailer where retailer_order_feed_id='" . $retailer_feed_id . "' and is_ack_feed='0' and is_asn_feed='0' and response_comments like 'ERROR:%' group by retailer_order_feed_id");
					$info = tep_db_fetch_array($sql);
					$entry_exists = (int)$info['count']>=1 ? true : false;
					
					$sql_array = array('retailer_order_feed_id' => $retailer_feed_id,
										'payload_id' => $payload_id,
										'generation_date' => $generation_date,
										'response_comments' => 'ERROR:' . $error, 
										'date_added' => 'now()');
					if ($entry_exists){
						tep_db_perform('feeds_orders_obn_to_retailer', $sql_array, 'update', "retailer_order_feed_id='" . $retailer_feed_id . "'");
					} else {
						tep_db_perform('feeds_orders_obn_to_retailer', $sql_array);
					}
					
					if ($orders_id){
						$insert_record = true;
						if ($entry_exists){
							$sql = tep_db_query("select orders_status_id from orders_status_history where orders_id='" . $orders_id . "' order by date_added desc limit 0, 1");
							if(tep_db_num_rows($sql)){
								$info = tep_db_fetch_array($sql);
								if ((int)$info['orders_status_id']==(int)ORDER_STATUS_FEED_ERROR){
									$insert_record = false;
								}
							}
						}
						if($insert_record){
							/*
							$sql_array = array('orders_id' => $orders_id,
												'orders_status_id' => ORDER_STATUS_FEED_ERROR,
												'date_added' => 'now()',
												'customer_notified' => '0',
												'comments' => $error);
							tep_db_perform('orders_status_history', $sql_array);
							*/
							tep_db_query("update feeds_orders_retailer_to_obn set orders_status='" . ORDER_STATUS_FEED_ERROR . "' where order_feed_id='" . $retailer_feed_id . "' and orders_id='" . $orders_id . "'");
							tep_db_query("update feeds_orders_products_retailer_to_obn set orders_products_status='" . ORDER_STATUS_FEED_ERROR . "' where order_feed_id='" . $retailer_feed_id . "'");
						}
					}
				}
			}
			rename(RETAILER_FEED_DIRECTORY . RETAILER_INCOMING_FOLDER . $file_name, RETAILER_FEED_DIRECTORY . RETAILER_ARCHIVES_FOLDER . $file_name);
		}
		
		private function handle_asn_file($file_name){
			$xml = simplexml_load_file(RETAILER_FEED_DIRECTORY . RETAILER_INCOMING_FOLDER . $file_name);
			$generation_date = (string)$xml->{NODE_HEADER}->{NODE_GENERATION_DATE};
			$payload_id = (string)$xml->{NODE_HEADER}->{NODE_PAYLOAD_ID};
			$order_ref = (string)$xml->{NODE_DETAILS}[ATTR_ORDER_REFERENCE];
			$payload_ref = (string)$xml->{NODE_DETAILS}[ATTR_PAYLOAD_REFERENCE];
			$shipping_agent = (string)$xml->{NODE_DETAILS}->{NODE_SHIPPING_DETAILS}->{NODE_SHIPPING_AGENT};
			$shipping_method = (string)$xml->{NODE_DETAILS}->{NODE_SHIPPING_DETAILS}->{NODE_SHIPPING_METHOD};
			$shipping_charges = (string)$xml->{NODE_DETAILS}->{NODE_SHIPPING_DETAILS}->{NODE_SHIPPING_CHARGES};
			$shipment_tracking_id = (string)$xml->{NODE_DETAILS}->{NODE_SHIPPING_DETAILS}->{NODE_SHIPMENT_TRACKING_ID};
			$shipment_date = (string)$xml->{NODE_DETAILS}->{NODE_SHIPPING_DETAILS}->{NODE_SHIPMENT_DATE};
			
			echo "$generation_date $payload_id $order_ref $payload_ref $shipping_agent $shipping_method $shipping_charges $shipment_tracking_id $shipment_date<br>";
			if (!$this->obn_payload_id_exists($payload_id)){
				foreach($xml->{NODE_DETAILS}->{NODE_PRODUCTS}->children() as $product){
					$product_model = (string)$product->{NODE_PRODUCT_MODEL};
					$product_quantity = (string)$product->{NODE_PRODUCT_QUANTITY};
					$unit_price = (string)$product->{NODE_UNIT_PRICE};
					
					echo "$product_model $product_quantity $unit_price<br>";
				}
				
				$sql = tep_db_query("select order_feed_id, orders_id from feeds_orders_retailer_to_obn where payload_id='" . $payload_ref . "'");
				if (tep_db_num_rows($sql)){
					$sql_info = tep_db_fetch_array($sql);
					$retailer_feed_id = $sql_info['order_feed_id'];
					$orders_id = $sql_info['orders_id'];
				} else {
					$retailer_feed_id = '0';
					$orders_id = '0';
				}			
				
				$sql_array = array('retailer_order_feed_id' => $retailer_feed_id,
									'payload_id' => $payload_id,
									'generation_date' => $generation_date,
									'is_asn_feed' => '1',
									'date_added' => 'now()');
				tep_db_perform('feeds_orders_obn_to_retailer', $sql_array);
                                //BOF:mod amazon_mws
                                $obn_asn_feed_id = tep_db_insert_id();
                                //EOF:mod amazon_mws
				
				if ($orders_id){
					/*$sql_array = array('orders_id' => $orders_id,
										'orders_status_id' => ORDER_STATUS_SHIPPED_BY_SUPPLIER,
										'date_added' => 'now()',
										'customer_notified' => '0');
					tep_db_perform('orders_status_history', $sql_array);*/
					tep_db_query("update feeds_orders_retailer_to_obn set orders_status='" . ORDER_STATUS_SHIPPED_BY_SUPPLIER . "' where order_feed_id='" . $retailer_feed_id . "' and orders_id='" . $orders_id . "'");
					tep_db_query("update feeds_orders_products_retailer_to_obn set orders_products_status='" . ORDER_STATUS_SHIPPED_BY_SUPPLIER . "' where order_feed_id='" . $retailer_feed_id . "'");
					if (!empty($shipment_tracking_id)){
						$sql = tep_db_query("select extra_track_num from orders where orders_id='" . $orders_id . "'");
						$sql_info = tep_db_fetch_array($sql);
						$extra_track_num = $sql_info['extra_track_num'];
						if (empty($extra_track_num)){
							$extra_track_num = $shipment_tracking_id;
						} else {
							$extra_track_num .= '|' . $shipment_tracking_id;
						}
						tep_db_query("update orders set extra_track_num='" . $extra_track_num . "' where orders_id='" . $orders_id . "'");
					}
                                    //BOF:mod amazon_mws
                                    if (amazon_manager::is_amazon_order($orders_id)){
                                        $amazon_order_id = amazon_manager::get_amazon_order_id($orders_id);
                                        $fulfillment_data = array(
                                            'orders_id' => $orders_id,
                                            'amazon_order_id' => $amazon_order_id,
                                            'shipment_date' => $shipment_date,
                                            'shipping_agent' => $shipping_agent,
                                            'shipping_method' => $shipping_method,
                                            'obn_asn_feed_id' => $obn_asn_feed_id,
                                        );
                                        tep_db_perform('amazon_fulfillment_info', $fulfillment_data);
                                        $amazon = new amazon_manager('mwso');
                                        $amazon->submit_order_fulfillment_feed($orders_id);
                                    }
                                    //EOF:mod amazon_mws
				}
			}
			@rename(RETAILER_FEED_DIRECTORY . RETAILER_INCOMING_FOLDER . $file_name, RETAILER_FEED_DIRECTORY . RETAILER_ARCHIVES_FOLDER . $file_name);
		}
	}
	
	$feed = new ack_asn_feed_handler();
	$feed->init();
	echo 'Done';
?>
