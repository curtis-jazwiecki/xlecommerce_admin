<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require('cron_application_top.php');

define('DIR_FS_EBAY_FEEDS',     DIR_FS_ROOT . 'eBay/feeds/');

$ids = '';
$sql = tep_db_query("select id, downloaded_file from ebay_download_jobs where is_downloaded='1' and file_processed_locally='0'");
if (tep_db_num_rows($sql)){
    while ($entry = tep_db_fetch_array($sql)){
        $response = register_ebay_sold_orders($entry['id'], $entry['downloaded_file']);
        
        if ($response){
            $ids .= $entry['id'] . ', ';
        }
    }
}
if (!empty($ids)){
    $ids = substr($ids, 0, -2);
    tep_db_query("update ebay_download_jobs set file_processed_locally='1' where id in (" . $ids . ")");
}

function register_ebay_sold_orders($order_feed_id, $file_name){
	$file_name = DIR_FS_EBAY_FEEDS . $file_name;
    echo $file_name;
	if (file_exists($file_name)){
		$xml = simplexml_load_file($file_name);
		foreach($xml->SoldReport->children() as $order){
			$details = array();
			
			$details['order_feed_id'] = $order_feed_id;
			
			$details['ebay_order_id'] = (string)$order->OrderID;
			$details['buyer_user_id'] = (string)$order->BuyerUserID;
			$details['buyer_first_name'] = (string)$order->BuyerFirstName;
			$details['buyer_last_name'] = (string)$order->BuyerLastName;
			$details['buyer_email'] = (string)$order->BuyerEmail;
			$details['buyer_phone'] = (string)$order->BuyerPhone;
			$details['shipping_service'] = (string)$order->ShippingService;
			$details['checkout_site_id'] = (string)$order->CheckoutSiteID;
			$details['order_creation_time'] = (string)$order->OrderCreationTime;
			$details['tax_amount'] = (string)$order->TaxAmount;
			$details['insurance_cost'] = (string)$order->InsuranceCost;
			$details['shipping_cost'] = (string)$order->ShippingCost;
			$details['order_total_cost'] = (string)$order->OrderTotalCost;
			$details['shipping_service_token'] = (string)$order->ShippingServiceToken;
			$details['payment_hold_status'] = (string)$order->PaymentHoldStatus;
			$details['ebay_payment_status'] = (string)$order->CheckoutStatus->eBayPaymentStatus;
			$details['payment_method'] = (string)$order->CheckoutStatus->PaymentMethod;
			$details['checkout_status'] = (string)$order->CheckoutStatus->Status;
			$details['external_transaction_id'] = (string)$order->ExternalTransaction->ExternalTransactionID;
			$details['external_transaction_time'] = (string)$order->ExternalTransaction->ExternalTransactionTime;
			$details['external_fee_or_credit_amount'] = (string)$order->ExternalTransaction->FeeOrCreditAmount;
			$details['external_payment_or_refund_amount'] = (string)$order->ExternalTransaction->PaymentOrRefundAmount;
			
			$details['ship_city_name'] = (string)$order->ShipCityName;
			$details['ship_country_name'] = (string)$order->ShipCountryName;
			$details['ship_postal_code'] = (string)$order->ShipPostalCode;
			$details['ship_recipient_name'] = (string)$order->ShipRecipientName;
			$details['ship_reference_id'] = (string)$order->ShipReferenceId;
			$details['ship_state_or_province'] = (string)$order->ShipStateOrProvince;
			$details['ship_street1'] = (string)$order->ShipStreet1;
			$details['ship_street2'] = (string)$order->ShipStreet2;
			
			/**/
			//sandbox invironment does not returns ship address. setting test values
			$details['ship_city_name'] = empty($details['ship_city_name']) ? 'Sierra Vista' : $details['ship_city_name'];
			$details['ship_country_name'] = empty($details['ship_country_name']) ? 'US' : $details['ship_country_name'];
			$details['ship_postal_code'] = empty($details['ship_postal_code']) ? '85650' : $details['ship_postal_code'];
			$details['ship_state_or_province'] = empty($details['ship_state_or_province']) ? 'AZ' : $details['ship_state_or_province'];
			/**/
			
			$details['ship_recipient_name'] = (string)$order->ShipRecipientName;
			$details['ship_reference_id'] = (string)$order->ShipReferenceId;
			$details['ship_state_or_province'] = (string)$order->ShipStateOrProvince;
			$details['ship_street1'] = empty($details['ship_street1']) ? '4619 S Sauk Ave' : $details['ship_street1'];
			$details['ship_street2'] = (string)$order->ShipStreet2;

			$items = array();
			foreach($order->OrderItemDetails->children() as $item){
				$items[] = array(
					'order_line_item_id' => (string)$item->OrderLineItemID, 
					'ebay_item_id' => (string)$item->ItemID, 
					'sku' => (string)$item->SKU,
					'quantity' => (string)$item->QuantitySold, 
					'sale_price' => (string)$item->SalePrice, 
					'tax_amount' => (string)$item->TaxAmount,
					'shipping_cost' => (string)$item->ShippingCost, 
					'total_cost' => (string)$item->TotalCost, 
					'insurance_cost' => (string)$item->InsuranceCost, 
					'listing_site_id' => (string)$item->ListingSiteID, 
					'buyer_payment_transaction_number' => (string)$item->BuyerPaymentTransactionNumber, 
					'Payment_hold_status' => (string)$item->Status->PaymentHoldStatus, 
				); 
			}
			$details['products'] = $items;
			move_ebay_order_to_osc($details);
		}
        return true;
	}
    return false;
}

function move_ebay_order_to_osc(&$details){
	if (!empty($details['ebay_order_id'])){
		$order_exists_query = tep_db_query("select id from ebay_orders where ebay_order_id='" . $details['ebay_order_id'] . "'");
		if (!tep_db_num_rows($order_exists_query)){
			$orders_data = array(
				'customers_name' => $details['buyer_first_name'] . ' ' . $details['buyer_last_name'], 
				'customers_street_address' => $details['ship_street1'], 
				'customers_suburb' => $details['ship_street2'], 
				'customers_city' => $details['ship_city_name'], 
				'customers_postcode' => $details['ship_postal_code'], 
				'customers_state' => $details['ship_state_or_province'], 
				'customers_country' => $details['ship_country_name'], 
				
				'billing_name' => $details['buyer_first_name'] . ' ' . $details['buyer_last_name'], 
				'billing_street_address' => $details['ship_street1'], 
				'billing_suburb' => $details['ship_street2'], 
				'billing_city' => $details['ship_city_name'], 
				'billing_postcode' => $details['ship_postal_code'], 
				'billing_state' => $details['ship_state_or_province'], 
				'billing_country' => $details['ship_country_name'], 
				
				'delivery_name' => $details['ship_recipient_name'], 
				'delivery_street_address' => $details['ship_street1'] . ' ' . $details['ship_street2'], 
				'delivery_suburb' => $details['ship_street2'], 
				'delivery_city' => $details['ship_city_name'], 
				'delivery_postcode' => $details['ship_postal_code'], 
				'delivery_state' => $details['ship_state_or_province'], 
				'delivery_country' => $details['ship_country_name'], 
				
				'customers_email_address' => $details['buyer_email'], 
				'customers_telephone' => $details['buyer_phone'], 
				'date_purchased' => date('Y-m-d H:i', strtotime($details['order_creation_time'])), 
				'payment_method' => $details['payment_method'], 
				'orders_status' => EBAY_ORDER_STATUS_UNSHIPPED, 
			);
			tep_db_perform('orders', $orders_data);
			$osc_order_id = tep_db_insert_id();
			
			$ebay_orders_data = array(
				'order_feed_id' => $details['order_feed_id'], 
				'ebay_order_id' => $details['ebay_order_id'], 
				'osc_order_id' => $osc_order_id, 
				'buyer_user_id' => $details['buyer_user_id'], 
				'shipping_service' => $details['shipping_service'], 
				'checkout_site_id' => $details['checkout_site_id'], 
				'shipping_service_token' => $details['shipping_service_token'], 
				'payment_hold_status' => $details['payment_hold_status'], 
				'ebay_payment_status' => $details['ebay_payment_status'], 
				'checkout_status' => $details['checkout_status'], 
				'external_transaction_id' => $details['external_transaction_id'], 
				'external_transaction_time' => date('Y-m-d H:i', strtotime($details['external_transaction_time'])), 
				'external_fee_or_credit_amount' => $details['external_fee_or_credit_amount'], 
				'external_payment_or_refund_amount' => $details['external_payment_or_refund_amount'], 
				'insurance_cost' => $details['insurance_cost'], 
				'date_added' => 'now()', 
			);
			tep_db_perform('ebay_orders', $ebay_orders_data);
			
			foreach($details['products'] as $item){
				$osc_item_info = get_product_name_by_sku($item['sku']);
				$order_products_data = array(
					'orders_id' => $osc_order_id,
					'products_id' => $osc_item_info['id'], 
					'products_name' => $osc_item_info['name'], 
					'products_model' => $item['sku'], 
					'products_quantity' => $item['quantity'], 
					'products_price' => $item['sale_price'], 
					'final_price' => $item['sale_price'], 
					'products_tax' => $item['tax_amount'],
				);
				tep_db_perform('orders_products', $order_products_data);
				$osc_order_product_id = tep_db_insert_id();
				
				$ebay_order_products_data = array(
					'osc_order_id' => $osc_order_id, 
					'order_line_item_id' => $item['order_line_item_id'], 
					'ebay_order_id' => $details['ebay_order_id'], 
					'ebay_item_id' => $item['ebay_item_id'], 
					'sku' => $item['sku'], 
					'osc_order_product_id' => $osc_order_product_id, 
					'shipping_cost' => $item['shipping_cost'], 
					'insurance_cost' => $item['insurance_cost'], 
					'listing_site_id' => $item['listing_site_id'], 
					'buyer_payment_transaction_number' => $item['buyer_payment_transaction_number'], 
					'Payment_hold_status' => $item['Payment_hold_status'], 
					'date_added' => 'now()', 
				);
				tep_db_perform('ebay_order_products', $ebay_order_products_data);
			}
			
			$status_data = array(
				'orders_id' => $osc_order_id,
				'orders_status_id' => EBAY_ORDER_STATUS_UNSHIPPED, 
				'date_added' => 'now()',
				'customer_notified' =>'0',
				'comments' => '', 
			);
			tep_db_perform('orders_status_history', $status_data);

			$sub_total = $details['order_total_cost'] - ($details['tax_amount'] + $details['shipping_cost']);
			require(DIR_WS_CLASSES . 'currencies.php');
			$currencies = new currencies();
			
			$order_total_data = array(
				'orders_id' => $osc_order_id, 
				'title' => 'Sub-Total:',
				'text' => $currencies->format($sub_total),
				'value' => $sub_total,
				'class' => 'ot_subtotal',
				'sort_order' => '1',
			);
			tep_db_perform('orders_total', $order_total_data);
			
			$order_total_data = array(
				'orders_id' => $osc_order_id, 
				'title' => $details['shipping_service_token'] . ' (' . $details['shipping_service'] . ')',
				'text' => $currencies->format($details['shipping_cost']),
				'value' => $details['shipping_cost'],
				'class' => 'ot_shipping',
				'sort_order' => '2',
			);
			tep_db_perform('orders_total', $order_total_data);
			
			$order_total_data = array(
				'orders_id' => $osc_order_id, 
				'title' => 'Tax:',
				'text' => $currencies->format($details['tax_amount']),
				'value' => $details['tax_amount'],
				'class' => 'ot_tax',
				'sort_order' => '3',
			);
			tep_db_perform('orders_total', $order_total_data);
			
			$order_total_data = array(
				'orders_id' => $osc_order_id, 
				'title' => 'Total:',
				'text' => $currencies->format($details['order_total_cost']),
				'value' => $details['order_total_cost'],
				'class' => 'ot_total',
				'sort_order' => '100',
			);
			tep_db_perform('orders_total', $order_total_data);
			
		}
	}
	return true;
}

function get_product_name_by_sku($sku){
	$resp = array('name' => '', 'id' => '');
	$sql = tep_db_query("select pd.products_name, p.products_id from products p inner join products_description pd on (p.products_id=pd.products_id and pd.language_id='1') where p.products_model='" . $sku . "'");
	if (tep_db_num_rows($sql)){
		$entry = tep_db_fetch_array($sql);
		$resp['name'] = $entry['products_name'];
		$resp['id'] = $entry['products_id'];
	}
	return $resp;
}

?>