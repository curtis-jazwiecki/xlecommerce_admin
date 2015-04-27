<?php
/**********
* Get the helper class and save and display shipping labels
*
**********/
require_once('includes/application_top.php');
require('includes/USPSLabel.php');

// get the order details from the DB
$order_id = intval( $_GET['oID'] );
$vendor_id = isset($_GET['vendor_id']) && intval($_GET['vendor_id'])>=1 ? intval($_GET['vendor_id']) : 1;
// just kill it if the order id is weird
if( !$order_id ){
	die("sorry, invalid order number");
}

// get the order info
$query = tep_db_query("SELECT * FROM orders WHERE orders_id = $order_id");
$result = tep_db_fetch_array($query);
// Debug
// var_dump($result);
// echo "<hr/>";

function parseShippingMethod($shipping_string){
	
	$parsed_shipping_method = '';
	$last_position =  strrpos($shipping_string,"(");
	$tmp = array();
	if($last_position){
		$parsed_shipping_method = str_replace(array( '(', ')' ), '', substr($shipping_string,$last_position));
		if(strpos($parsed_shipping_method,"<sup>")){
			$tmp = explode("<sup>",$parsed_shipping_method);
			$parsed_shipping_method = trim($tmp[0]);
		}
	}else{
		$parsed_shipping_method = $shipping_string;
		$parsed_shipping_method = str_replace(array( '(', ')' ), '', substr($shipping_string,$last_position));
		if(strpos($parsed_shipping_method,"<sup>")){
			$tmp = explode(",",$parsed_shipping_method);
			$parsed_shipping_method = trim($tmp[0]);
		}
	}
	return $parsed_shipping_method;
}

$query_shipping_query = tep_db_query("select shipping_method from orders_shipping where vendors_id = '".$vendor_id."' and orders_id = '".$order_id."' and shipping_module = 'usps'");

if(tep_db_num_rows($query_shipping_query)){
	$query_shipping = tep_db_fetch_array($query_shipping_query);
}else{
	$query_shipping = tep_db_fetch_array(tep_db_query("select title as orders_shipping from orders_total where orders_id = '".$order_id."' and class = 'ot_shipping'"));
}

$parsed_shipping_code = parseShippingMethod($query_shipping['shipping_method'],$shipping_language_array);





$product_weight_query = tep_db_fetch_array(tep_db_query("select SUM(products_weight) as products_weight from products as p join orders_products as op USING(products_id) where op.orders_id = '".$order_id."' and op.vendors_id = '".$vendor_id."' group by p.vendors_id"));
// make the request



if ($vendor_id){
	$get_vendor_details = tep_db_fetch_array(tep_db_query("select * from vendors where vendors_id = '".(int)$vendor_id."'"));
	
	$from_params_array = array(
		"userName" 	 => @constant('MODULE_SHIPPING_USPS_USERID_'.$vendor_id),
		"FromName" 	 => $get_vendor_details['vendors_name'],
		"FromAddress2" => $get_vendor_details['vendor_street'],
		"FromCity" 	 => $get_vendor_details['vendor_city'],
		"FromState" 	=> $get_vendor_details['vendor_state'],
		"FromZip5" 	 => $get_vendor_details['vendors_zipcode'],
		"vendor_add2"  => $get_vendor_details['vendor_add2']
	);
	
	$to_params_array = array(
		"ToName" 			 => $result['customers_name'],
		"ToAddress2" 		 => $result['delivery_street_address'],
		"ToCity" 			 => $result['delivery_city'],
		"ToState" 	    	=> convert_state($result['delivery_state'],'abbrev'),
		"ToZip5" 	 	 	 => $result['delivery_postcode'],
		"to_delivery_company"=> $result['delivery_company'],
		"to_delivery_suburb" => $result['delivery_suburb'],
		"products_weight" 	=> $product_weight_query['products_weight'],
		"shipping_code" 	  => $parsed_shipping_code
	);

	$USPSResponse = USPSLabel($from_params_array,$to_params_array);
	
} else {
	$USPSResponse = USPSLabel(MODULE_SHIPPING_USPS_USERID, SHIPPING_LABEL_USPS_FROM_NAME,SHIPPING_LABEL_USPS_FROM_ADDRESS,SHIPPING_LABEL_USPS_FROM_CITY,SHIPPING_LABEL_USPS_FROM_STATE,SHIPPING_LABEL_USPS_FROM_ZIP, $result['customers_name'] ,$result['customers_street_address'], $result['customers_city'], convert_state( $result['customers_state'], 'abbrev' ), $result['customers_postcode'], $product_weight_query['products_weight'], $parsed_shipping_code);
}
// Debug
// var_dump($response);

tep_db_query("update orders_shipping set usps_track_num = '".$USPSResponse['DelivConfirmCertifyV4.0Response']['DeliveryConfirmationNumber']['VALUE']."' where orders_id = '".$order_id."' and 	vendors_id = '".$vendor_id."'");
// get the label from the returned array
$USPSLabel = $USPSResponse['DelivConfirmCertifyV4.0Response']['DeliveryConfirmationLabel']['VALUE'];

// turn label into pdf
$label_pdf = base64_decode($USPSLabel);

// write the pdf
$label_name = $order_id.'_'.$vendor_id.'_usps_label.pdf';
@file_put_contents('shipping_labels/'.$label_name, $label_pdf);

// this is just the public path where we saved the pdf
$pdf_url = "/ad_obnv6/shipping_labels/$label_name";

// durr.. inject the js????
$inject_js = <<<JSSCRIPT
	<script type="text/javascript">
		window.open('$pdf_url');
	</script>
JSSCRIPT;
echo $inject_js;


function convert_state($name, $to='name') {
	$states = array(
	array('name'=>'Alabama', 'abbrev'=>'AL'),
	array('name'=>'Alaska', 'abbrev'=>'AK'),
	array('name'=>'Arizona', 'abbrev'=>'AZ'),
	array('name'=>'Arkansas', 'abbrev'=>'AR'),
	array('name'=>'California', 'abbrev'=>'CA'),
	array('name'=>'Colorado', 'abbrev'=>'CO'),
	array('name'=>'Connecticut', 'abbrev'=>'CT'),
	array('name'=>'Delaware', 'abbrev'=>'DE'),
	array('name'=>'Florida', 'abbrev'=>'FL'),
	array('name'=>'Georgia', 'abbrev'=>'GA'),
	array('name'=>'Hawaii', 'abbrev'=>'HI'),
	array('name'=>'Idaho', 'abbrev'=>'ID'),
	array('name'=>'Illinois', 'abbrev'=>'IL'),
	array('name'=>'Indiana', 'abbrev'=>'IN'),
	array('name'=>'Iowa', 'abbrev'=>'IA'),
	array('name'=>'Kansas', 'abbrev'=>'KS'),
	array('name'=>'Kentucky', 'abbrev'=>'KY'),
	array('name'=>'Louisiana', 'abbrev'=>'LA'),
	array('name'=>'Maine', 'abbrev'=>'ME'),
	array('name'=>'Maryland', 'abbrev'=>'MD'),
	array('name'=>'Massachusetts', 'abbrev'=>'MA'),
	array('name'=>'Michigan', 'abbrev'=>'MI'),
	array('name'=>'Minnesota', 'abbrev'=>'MN'),
	array('name'=>'Mississippi', 'abbrev'=>'MS'),
	array('name'=>'Missouri', 'abbrev'=>'MO'),
	array('name'=>'Montana', 'abbrev'=>'MT'),
	array('name'=>'Nebraska', 'abbrev'=>'NE'),
	array('name'=>'Nevada', 'abbrev'=>'NV'),
	array('name'=>'New Hampshire', 'abbrev'=>'NH'),
	array('name'=>'New Jersey', 'abbrev'=>'NJ'),
	array('name'=>'New Mexico', 'abbrev'=>'NM'),
	array('name'=>'New York', 'abbrev'=>'NY'),
	array('name'=>'North Carolina', 'abbrev'=>'NC'),
	array('name'=>'North Dakota', 'abbrev'=>'ND'),
	array('name'=>'Ohio', 'abbrev'=>'OH'),
	array('name'=>'Oklahoma', 'abbrev'=>'OK'),
	array('name'=>'Oregon', 'abbrev'=>'OR'),
	array('name'=>'Pennsylvania', 'abbrev'=>'PA'),
	array('name'=>'Rhode Island', 'abbrev'=>'RI'),
	array('name'=>'South Carolina', 'abbrev'=>'SC'),
	array('name'=>'South Dakota', 'abbrev'=>'SD'),
	array('name'=>'Tennessee', 'abbrev'=>'TN'),
	array('name'=>'Texas', 'abbrev'=>'TX'),
	array('name'=>'Utah', 'abbrev'=>'UT'),
	array('name'=>'Vermont', 'abbrev'=>'VT'),
	array('name'=>'Virginia', 'abbrev'=>'VA'),
	array('name'=>'Washington', 'abbrev'=>'WA'),
	array('name'=>'West Virginia', 'abbrev'=>'WV'),
	array('name'=>'Wisconsin', 'abbrev'=>'WI'),
	array('name'=>'Wyoming', 'abbrev'=>'WY'),
	array('name'=>'Armed Forces Europe', 'abbrev'=>'AE')
	);

	$return = false;
	foreach ($states as $state) {
		if ($to == 'name') {
			if (strtolower($state['abbrev']) == strtolower($name)){
				$return = $state['name'];
				break;
			}
		} else if ($to == 'abbrev') {
			if (strtolower($state['name']) == strtolower($name)){
				$return = strtoupper($state['abbrev']);
				break;
			}
		}
	}
	return $return;
}
?>