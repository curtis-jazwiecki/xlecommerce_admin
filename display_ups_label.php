<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require_once('includes/application_top.php');
require('includes/UPSLabel.php');

$order_id = intval( $_GET['oID'] );
$vendor_id = isset($_GET['vendor_id']) && intval($_GET['vendor_id'])>=1 ? intval($_GET['vendor_id']) : 1;
if( !$order_id ){
    die("sorry, invalid order number");

}
$query = tep_db_query("SELECT * FROM orders WHERE orders_id = $order_id");
$result = tep_db_fetch_array($query);


// code to get shipping method starts //
$shipping_language_array = array(
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_01"		=>"UPS Next Day Air",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_02"		=>"UPS 2nd Day Air",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_03"		=>"UPS Ground",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_07"		=>"UPS Worldwide Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_08"		=>"UPS Worldwide Expedited",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_11"		=>"UPS Standard",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_12"		=>"UPS 3 Day Select",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_13"		=>"UPS Next Day Air Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_14"		=>"UPS Next Day Air Early A.M.",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_54"		=>"UPS Worldwide Express Plus",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_59"		=>"UPS 2nd Day Air A.M.",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_65"		=>"UPS Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_01"	=>"UPS Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_02"	=>"UPS Expedited",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_07"	=>"UPS Worldwide Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_08"	=>"UPS Worldwide Expedited",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_11"	=>"UPS Standard",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_12"	=>"UPS 3 Day Select",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_13"	=>"UPS Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_14"	=>"UPS Express Early A.M.",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_65"	=>"UPS Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_07"		=>"UPS Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_08"		=>"UPS Expedited",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_11"		=>"UPS Standard",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_54"		=>"UPS Worldwide Express Plus",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_65"		=>"UPS Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_82"		=>"UPS Today Standard",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_83"		=>"UPS Today Dedicated Courier",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_84"		=>"UPS Today Intercity",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_85"		=>"UPS Today Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_86"		=>"UPS Today Express Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_01"		=>"UPS Next Day Air",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_02"		=>"UPS 2nd Day Air",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_03"		=>"UPS Ground",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_07"		=>"UPS Worldwide Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_08"	    =>"UPS Worldwide Expedited",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_14"	    =>"UPS Next Day Air Early A.M.",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_54"	    =>"UPS Worldwide Express Plus",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_65"	    =>"UPS Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_MEXICO_ORIGIN_07" 	=>"UPS Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_MEXICO_ORIGIN_08" 	=>"UPS Expedited",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_MEXICO_ORIGIN_54" 	=>"UPS Express Plus",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_MEXICO_ORIGIN_65" 	=>"UPS Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_OTHER_ORIGIN_07"  	 =>"UPS Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_OTHER_ORIGIN_08"  	 =>"UPS Worldwide Expedited",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_OTHER_ORIGIN_11"  	 =>"UPS Standard",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_OTHER_ORIGIN_54"  	 =>"UPS Worldwide Express Plus",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_OTHER_ORIGIN_65"  	 =>"UPS Saver",
);

function parseShippingMethod($shipping_string,$shipping_language_array){
	
	$parsed_shipping_method = '';
	$last_position =  strrpos($shipping_string,"(");
	$tmp = array();
	$selected_shipping = array();
	if($last_position){
		$parsed_shipping_method = str_replace(array( '(', ')' ), '', substr($shipping_string,$last_position));
		if(strpos($parsed_shipping_method,",")){
			$tmp = explode(",",$parsed_shipping_method);
			$parsed_shipping_method = trim($tmp[0]);
			if(in_array($parsed_shipping_method,$shipping_language_array)){
				$selected_shipping[] = substr(array_search($parsed_shipping_method, $shipping_language_array),-2); 
				$selected_shipping[] = $parsed_shipping_method; 
			}
		}
	}else{
		$parsed_shipping_method = $shipping_string;
		$parsed_shipping_method = str_replace(array( '(', ')' ), '', substr($shipping_string,$last_position));
		if(strpos($parsed_shipping_method,",")){
			$tmp = explode(",",$parsed_shipping_method);
			$parsed_shipping_method = trim($tmp[0]);
			if(in_array($parsed_shipping_method,$shipping_language_array)){
				$selected_shipping[] = substr(array_search($parsed_shipping_method, $shipping_language_array),-2); 
				$selected_shipping[] = $parsed_shipping_method; 
			}
		}
	}
	return $selected_shipping;
}

$query_shipping_query = tep_db_query("select shipping_method from orders_shipping where vendors_id = '".$vendor_id."' and orders_id = '".$order_id."' and shipping_module = 'upsxml'");

if(tep_db_num_rows($query_shipping_query)){
	$query_shipping = tep_db_fetch_array($query_shipping_query);
}else{
	$query_shipping = tep_db_fetch_array(tep_db_query("select title as orders_shipping from orders_total where orders_id = '".$order_id."' and class = 'ot_shipping'"));
}

$parsed_shipping_code = parseShippingMethod($query_shipping['shipping_method'],$shipping_language_array);

$product_weight_query = tep_db_fetch_array(tep_db_query("select SUM(products_weight) as products_weight from products as p join orders_products as op USING(products_id) where op.orders_id = '".$order_id."' and op.vendors_id = '".$vendor_id."' group by p.vendors_id"));
// code to get shipping method ends //

$params = array(
	'user_name'             => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_RATES_USERNAME_' . $vendor_id) : MODULE_SHIPPING_UPSXML_RATES_USERNAME),
    'password'              => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_RATES_PASSWORD_' . $vendor_id) : MODULE_SHIPPING_UPSXML_RATES_PASSWORD),
    'license_number'        => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_RATES_ACCESS_KEY_' . $vendor_id) : MODULE_SHIPPING_UPSXML_RATES_ACCESS_KEY),
    'from_company_name'     => STORE_NAME,
    'from_address1'         => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_ORIGIN_ADDRESS_' . $vendor_id) : MODULE_SHIPPING_UPSXML_ORIGIN_ADDRESS),
    'from_address2'         => '',
    'from_city'             => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_RATES_CITY_' . $vendor_id) : MODULE_SHIPPING_UPSXML_RATES_CITY),
    'from_state_code'       => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_RATES_STATEPROV_' . $vendor_id) : MODULE_SHIPPING_UPSXML_RATES_STATEPROV),
    'from_zip'              => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_RATES_POSTALCODE_' . $vendor_id) : MODULE_SHIPPING_UPSXML_RATES_POSTALCODE),
    'tax_id_type_code'      => '',
    'to_company_name'       => $result['delivery_company'],
    'to_name'               => $result['delivery_name'],
    'to_address1'           => $result['delivery_street_address'],
    'to_address2'           => $result['delivery_suburb'],
    'to_city'               => $result['delivery_city'],
	'to_telephone'          => $result['customers_telephone'],
    'to_state_code'         => convertStateTo($result['delivery_state']),
    'to_zip'                => $result['delivery_postcode'],
    'service_code'          => $parsed_shipping_code[0],
    'service_desc'          => $parsed_shipping_code[1],
    'package_type_code'     => $parsed_shipping_code[0],
    'package_type_desc'     => $parsed_shipping_code[1],
    'package_desc'          => '',
	'shipper_number'        => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_RATES_UPS_ACCOUNT_NUMBER_' . $vendor_id) : MODULE_SHIPPING_UPSXML_RATES_UPS_ACCOUNT_NUMBER),
	'order_id'			  => $order_id,
	'weight'			    => $product_weight_query['products_weight'],
	'vendor_id'			 => $vendor_id
);

$ups_digests = getDigestUPSLabel($params);