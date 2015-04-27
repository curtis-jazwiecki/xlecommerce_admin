<?
function dd($d){
	echo '<pre>';
	print_r($d);
	echo '</pre>';die;
}

// ship_fedex.php
// for managing federal express shipments
// - makes a form for adding additional info about a shipment
// - sends a ship request to fedex
// - returns a shipping label & formats it for printing
// - cancels an existing ship request
// - builds a shipping manifest

// debugging
// setting to 1 displays the array of all shipping
// and manifest data when a ship request is made
	$debug = 0;
	
	
  require('includes/application_top.php');
  //require(DIR_WS_FUNCTIONS . 'ship_fedex.php');
  include(DIR_FS_CATALOG.DIR_WS_MODULES . 'shipping/fedexwebservices.php');
  
  define('TABLE_SHIPPING_MANIFEST','shipping_manifest');
 
	$action = $HTTP_GET_VARS['action'];
	$order = $HTTP_GET_VARS['oID'];

// *** EDIT VARIABLES BELOW **
$lastshipment = 17;            // Define last shipment time (ex: 17 would be 5pm on your server)
$send_email_on_shipping = 1;   // Set to 0 to disable, set to 1 to enable automatic email of tracking number

// Modify the variable here and in admin/fedex_popup.php
$thermal_printing = 1;         // set the printing type, thermal_printing = 0 for laser, thermal_printing = 1 for label printer

////
// make a new ship request

	if($action=='ship') {

		if (!$order) {
			die (ERROR_NO_ORDER_NUMBER);
			}

		include(DIR_WS_INCLUDES . 'abbreviate.php'); // used to abbreviate state & country names
		require(DIR_WS_INCLUDES . 'fedexdc.php');
		
		
  		
  		// array of characters we don't want in phone numbers
		$unwanted = array('(',')','-','.',' ','/');

		

		// get the country we're shipping from
		$country_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'STORE_COUNTRY'");
		$country_value = tep_db_fetch_array($country_query);
		$country = tep_get_country_name($country_value['configuration_value']);
		// abbreviate it for fedex (United States = US etc.)
		$senders_country = abbreviate_country($country);

		// get sender's fedex info from configuration table
		// (requires installation & configuration of FedEx RealTime Quotes)

		
		$fedexRequestData = array();
	
		
		// get all information from the order record
		$order_query = tep_db_query("select * from orders where orders_id = $order");

		$order_info = tep_db_fetch_array($order_query);
		
		// abbreviate the delivery state (function is in abbreviate.php)
		$order_info['delivery_state'] = abbreviate_state($order_info['delivery_state']);

		// abbreviate the delivery country (function is in abbreviate.php)
		$order_info['delivery_country'] = abbreviate_country($order_info['delivery_country']);
		
		// get rid of dashes, parentheses and periods in customer's telephone number
		$order_info['customers_telephone'] = trim(str_replace($unwanted, '', $order_info['customers_telephone']));

		$fedexRequestData['order_info'] = $order_info;
		// get the transaction value
		//		$value_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $order . "' and class='ot_subtotal'");
		//		$order_value = tep_db_fetch_array($value_query);
		//		$order_value = round($order_value['value'], 0);

		//// some form variables

		// format the form date (comes in as mm-dd-yyyy)
		$date_array = explode('-',$HTTP_POST_VARS['pickup_date']);
		$corrected_date = $date_array[2] . $date_array[0] . $date_array[1];

		// determine whether the ship date is today or later
		if ($corrected_date == date(Ymd)) {
			$future = 'N'; // today
			}
		else {
			$future = 'Y';  // later date
			}

		
		// start the array for fedex
		$shipData = array(
		      'signature_type'=> $HTTP_POST_VARS['signature_type'], // signature type
		      'packaging_type'=> $HTTP_POST_VARS['packaging_type'], // packaging type (01 is customer packaging)
		      'service_type'=> $HTTP_POST_VARS['service_type'], //
		      'delivery_city'=>   $order_info['delivery_city'],
		      'bill_type'=>   $HTTP_POST_VARS['bill_type'], // payment type (1 is bill to sender)
		      'dropoff_type'=> $HTTP_POST_VARS['dropoff_type'], // drop off type (1 is regular pickup)
		      'printing_type' => (($thermal_printing) ? 7 : 5), // label media (5 is plain paper, 7 is 4x6 image)
		      'package_invoice' => $HTTP_POST_VARS['package_invoice'], // invoice number
		      'package_reference' => $HTTP_POST_VARS['package_reference'], // reference number
		      'package_po' => $HTTP_POST_VARS['package_po'], // purchase order number
		      'package_department' => $HTTP_POST_VARS['package_department'], // department name
			  'ship_date' => $corrected_date,  // ship date
			  'future_day' => $future, // future day shipment
			  'saturday_delivery' => $HTTP_POST_VARS['saturday_delivery'], // Saturday delivery
			  'hold_at_location' => $HTTP_POST_VARS['hold_at_location'], // Hold At Fedex Location
			  'hal_address' => $HTTP_POST_VARS['hal_address'], // Hold At Location address
			  'hal_city' => $HTTP_POST_VARS['hal_city'], // Hold At Location city
			  'hal_state' => $HTTP_POST_VARS['hal_state'], // Hold At Location state
			  'hal_postcode' => $HTTP_POST_VARS['hal_postcode'], // Hold At Location postal code
			  'hal_phone' => $HTTP_POST_VARS['hal_phone'], // Hold At Location phone number
			  'autopod' => $HTTP_POST_VARS['autopod'], // Automatic Proof of Delivery,
			  'hold_at_location' => $HTTP_POST_VARS['hold_at_location'],
		 	  'oversized' => $HTTP_POST_VARS['oversized'],
			  'LabelFormatType' => $HTTP_POST_VARS['LabelFormatType'],
			  'ImageType' 		=> $HTTP_POST_VARS['ImageType'],
			  'LabelStockType' => $HTTP_POST_VARS['LabelStockType']
		
		);
		

		
		$pakage_details = array();
		$unitUsed = MODULE_SHIPPING_FEDEX_WEB_SERVICES_WEIGHT;
		$pakage_details['PackageCount'] = $HTTP_POST_VARS['package_num'];
		$pakage_details['Total_Weight'] = $HTTP_POST_VARS['package_weight'];
		$pakage_details['Units'] = $unitUsed;
		
		
		###Validate multiple pakages###
		if($pakage_details['PackageCount'] > 1)
		{
			$pakage_details['pakages'] = $HTTP_POST_VARS['data'];
		}
		else
		{
			 $pakage_details['dimension'] = array(
												 'dim_height' => $HTTP_POST_VARS['dim_height'], // "your package" height dimension
												  'dim_width' => $HTTP_POST_VARS['dim_width'], // "your package" width dimension
												  'dim_length' => $HTTP_POST_VARS['dim_length'] // "your package length dimension
												 );	
		}
			
			$fedexRequestData['shipData'] 		= $shipData;
			$fedexRequestData['pakage_details'] = $pakage_details;
			
			$shipSuccess = false;
			
		
			##Fedex Object
			$trackNum = null;
  			$fedexServ = new fedexwebservices();
			$serviceResponse = $fedexServ->shipRequest($fedexRequestData);
			
			$trackingNumber = null;
			$shipSuccess = $serviceResponse['success'];
			
			if($shipSuccess){
				
				$trackNum = $serviceResponse['trackingNo']->TrackingNumber;
					// store the tracking number
					tep_db_query("update " . TABLE_ORDERS . " set fedex_tracking='" . $trackNum . "' where orders_id = " . $order . "");
					
					// add comment to order history
					$fedex_comments = ORDER_HISTORY_DELIVERED . $trackNum;
		
					// ...mark the order record "delivered"...
					$update_status = array ('orders_status' => 3);
					tep_db_perform(TABLE_ORDERS, $update_status, 'update', "orders_id = '" . $order . "'");
		
					if ($send_email_on_shipping) {
						$customer_notified = '1';
					}
					else {
						$customer_notified = '0';
					}
		
					tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . $order . "', '3', now(), '" . $customer_notified . "', '" . $fedex_comments  . "')");
		
					
					// send email automatically on shipping
					if ($send_email_on_shipping) {
				        $check_status_query = tep_db_query("select customers_name, customers_email_address, orders_status, date_purchased from " . TABLE_ORDERS . " where orders_id = '" . (int)$order . "'");
			    	    $check_status = tep_db_fetch_array($check_status_query);
		
			   			if (tep_not_null($trackNum)) {
							$email_notify_tracking = sprintf(EMAIL_TEXT_TRACKING_NUMBER) . "\n" . URL_TO_TRACK1 . nl2br(tep_output_string_protected($trackNum)) . "\n\n";
						};
		
	            $email_txt = STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . $order . "\n\n" . EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $order, 'SSL') . "\n\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . "<br>" . $email_notify_tracking . "\n" . sprintf(EMAIL_TEXT_STATUS_UPDATE, 'Shipped');
						tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email_txt, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
					}
		
					// ... and display the new label without manifest entry for express shipments
					$ship_type_query = tep_db_query("select shipping_type from " . TABLE_SHIPPING_MANIFEST . " where orders_id = '" . $order . "'");
					$ship_type = tep_db_fetch_array($ship_type_query);
					if ($service_type < 89) {
						$delete_manifest_query = tep_db_query("delete from " . TABLE_SHIPPING_MANIFEST . " where orders_id = '" . $order . "'");
					}	
			}
			
	}
	elseif ( $action == 'cancel' ) 
	{
		if (!$order) 
		{
			echo ERROR_NO_ORDER_SPECIFIED;
		} 
		else 
		{
			// get the tracking number from the order record
			$fedex_tracking_query = tep_db_query("select fedex_tracking from " . TABLE_ORDERS . " where orders_id = '" . $order . "'");
			$r = tep_db_fetch_array($fedex_tracking_query);
			$fedex_tracking = $r['fedex_tracking'];

			// get the shipment type from the shipping manifest
			$ship_type_query = tep_db_query("select shipping_type from " . TABLE_SHIPPING_MANIFEST . " where orders_id = '" . $order . "'");
			$ship_type = tep_db_fetch_array($ship_type_query);
			if (($ship_type['shipping_type'] == 90) or ($ship_type['shipping_type'] == 92)) {
				$ship_type = 'FDXG';
			}
			else {
				$ship_type = 'FDXE';
			}

			// remove shipment data from the shipping manifest
			$delete_manifest_query = tep_db_query("delete from " . TABLE_SHIPPING_MANIFEST . " where orders_id = '" . $order . "'");
			if (!$delete_manifest_query) {
				echo COULD_NOT_DELETE_ENTRIES;
			}
			
			$fedexServ = new fedexwebservices();
			$response = $fedexServ->cancelShipment($order, $fedex_tracking);
		
			if ($response -> HighestSeverity == 'FAILURE' && $response -> HighestSeverity == 'ERROR')
			{
				die(ERROR . $response -> Notifications -> Message);
			}

			// delete the tracking number from the order record
			$delete_trackNum = array('fedex_tracking' => '');

			tep_db_perform(TABLE_ORDERS, $delete_trackNum, 'update', "orders_id = '" . $order . "'");

			// ...mark the order record "cancelled"...
			$update_status = array ('orders_status' => 4);
			tep_db_perform(TABLE_ORDERS, $update_status, 'update', "orders_id = '" . $order . "'");

			// ...add a comment to the order history to show what we've done...
			$fedex_comments = ORDER_HISTORY_CANCELLED . $trackNum;

			tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . $order . "', 6, now(), '', '" . $fedex_comments  . "')");

			// ...delete the record from the manifest...
			tep_db_query("delete from " . TABLE_SHIPPING_MANIFEST . " where orders_id = '" . $order . "'");

			// ...delete any stored labels...
			$file_prefix = "shipExpressLabel-$order-";
			$dir = 'labels';
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if (preg_match('/^'.$file_prefix.'.*.png/', $file)) {
							unlink("labels/{$file}");
						}
					}
					closedir($dh);
				}
			}
			
			// ...and refresh the orders page
			tep_redirect(FILENAME_ORDERS . '?oID=' . $order);
		}
	}
////
// make the form for additional shipment information

	elseif($action=='new') {
		$order = $HTTP_GET_VARS['oID'];

// determine if we're using test or production gateway
		$value_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_SERVER'");
		$value = tep_db_fetch_array($value_query);
		$fedex_gateway = $value['configuration_value'];

// Moved up so we can now detect company or not
// get orders information
		$order_query = tep_db_query("select * from orders where orders_id = $order");
		$order_info = tep_db_fetch_array($order_query);

		// abbreviate the delivery country (function is in abbreviate.php)
		$delivery_country = $order_info['delivery_country'];


// arrays for shipping options; include as many or as few as you like
		$packaging_type = array();
		$packaging_type[] = array('id' => 'YOUR_PACKAGING', 'text' => 'Your Packaging');
		$packaging_type[] = array('id' => 'FEDEX_PAK', 'text' => 'FedEx Pak');
		$packaging_type[] = array('id' => 'FEDEX_BOX', 'text' => 'FedEx Box');
		$packaging_type[] = array('id' => 'FEDEX_TUBE', 'text' => 'FedEx Tube');
		$packaging_type[] = array('id' => 'FEDEX_ENVELOPE', 'text' => 'FedEx Envelope');


		$LabelFormatType =  array();
		$LabelFormatType[] = array('id' => 'COMMON2D', 'text' => 'Common 2D');
		$LabelFormatType[] = array('id' => 'LABEL_DATA_ONLY', 'text' => 'Label Data Only');
		
		$LabelStockType = array();
		$LabelStockType[] = array('id' => 'STOCK_4X6', 'text' => 'STOCK_4X6');
		$LabelStockType[] = array('id' => '75_LEADING_DOC_TAB', 'text' => '75_LEADING_DOC_TAB');
		$LabelStockType[] = array('id' => 'STOCK_4X6.75_TRAILING_DOC_TAB', 'text' => 'STOCK_4X6.75_TRAILING_DOC_TAB');
		$LabelStockType[] = array('id' => 'STOCK_4X8', 'text' => 'STOCK_4X8');
		$LabelStockType[] = array('id' => 'STOCK_4X9_LEADING_DOC_TAB', 'text' => 'STOCK_4X9_LEADING_DOC_TAB');
		$LabelStockType[] = array('id' => 'STOCK_4X9_TRAILING_DOC_TAB', 'text' => 'STOCK_4X9_TRAILING_DOC_TAB');
		$LabelStockType[] = array('id' => 'PAPER_4X6', 'text' => 'PAPER_4X6');
		$LabelStockType[] = array('id' => 'PAPER_4X8', 'text' => 'PAPER_4X8');
		$LabelStockType[] = array('id' => 'PAPER_4X9', 'text' => 'PAPER_4X9');
		$LabelStockType[] = array('id' => 'PAPER_7X4.75', 'text' => 'PAPER_7X4.75');
		$LabelStockType[] = array('id' => 'PAPER_8.5X11_BOTTOM_HALF_LABEL', 'text' => 'PAPER_8.5X11_BOTTOM_HALF_LABEL');
		
		
		
		$ImageType =  array();
		$ImageType[] = array('id' => 'PDF', 'text' => 'PDF');
		$ImageType[] = array('id' => 'DPL', 'text' => 'DPL');
		$ImageType[] = array('id' => 'EPL2', 'text' => 'EPL2');
		$ImageType[] = array('id' => 'PNG', 'text' => 'PNG');
		$ImageType[] = array('id' => 'ZPLII', 'text' => 'ZPLII');
		
		$service_type = array();
		$service_type[] = array('id' => 'STANDARD_OVERNIGHT', 'text' => 'FedEx Standard Overnight');
		$service_type[] = array('id' => 'FEDEX_EXPRESS_SAVER', 'text' => 'FedEx Express Saver');
		$service_type[] = array('id' => 'FEDEX_FIRST_FREIGHT', 'text' => 'FedEx First Freight');
		$service_type[] = array('id' => 'FEDEX_1_DAY_FREIGHT', 'text' => 'FedEx 1 Day freight');
		$service_type[] = array('id' => 'FEDEX_3_DAY_FREIGHT', 'text' => 'FedEx 3 Day freight');
		$service_type[] = array('id' => 'GROUND_HOME_DELIVERY', 'text' => 'FedEx Ground Home Delivery');
		$service_type[] = array('id' => 'FEDEX_GROUND', 'text' => 'FedEx Ground');
		$service_type[] = array('id' => 'FEDEX_2_DAY_AM', 'text' => 'FedEx 2day AM');
		$service_type[] = array('id' => 'FEDEX_2_DAY', 'text' => 'FedEx 2day');
		$service_type[] = array('id' => 'PRIORITY_OVERNIGHT', 'text' => 'FedEx Priority');
		$service_type[] = array('id' => 'FEDEX_FREIGHT_ECONOMY', 'text' => 'FedEx Freight Economy');
		$service_type[] = array('id' => 'FEDEX_FREIGHT_PRIORITY', 'text' => 'FedEx Freight Priority');
		$service_type[] = array('id' => 'INTERNATIONAL_PRIORITY_FREIGHT', 'text' => 'INTERNATIONAL PRIORITY FREIGHT');
		$service_type[] = array('id' => 'INTERNATIONAL_ECONOMY_FREIGHT', 'text' => 'INTERNATIONAL ECONOMY FREIGHT');
		$service_type[] = array('id' => 'INTERNATIONAL_PRIORITY', 'text' => 'INTERNATIONAL PRIORITY');

		
		$bill_type = array();
		$bill_type[] = array('id' => 'SENDER', 'text' => 'Bill sender (Prepaid)');
		$bill_type[] = array('id' => 'RECIPIENT', 'text' => 'Bill recipient');
		$bill_type[] = array('id' => 'THIRD_PARTY', 'text' => 'Bill third party');
		$bill_type[] = array('id' => 'COLLECT', 'text' => 'Collect');

		$dropoff_type = array();
		$dropoff_type[] = array('id' => 'REGULAR_PICKUP', 'text' => 'Regular pickup');
		$dropoff_type[] = array('id' => 'REQUEST_COURIER', 'text' => 'Request courier');
		$dropoff_type[] = array('id' => 'DROP_BOX', 'text' => 'Drop box');
		$dropoff_type[] = array('id' => 'BUSINESS_SERVICE_CENTER', 'text' => 'Drop at BSC');
		$dropoff_type[] = array('id' => 'STATION', 'text' => 'Drop at station');

		$oversized = array();
		$oversized[] = array('id' => 0, 'text' => '');
		$oversized[] = array('id' => 1, 'text' => 1);
		$oversized[] = array('id' => 2, 'text' => 2);
		$oversized[] = array('id' => 3, 'text' => 3);

		// array for Saturday delivery
		$saturday_delivery = array();
		$saturday_delivery[] = array('id' => 0, 'text' => 'N');
		$saturday_delivery[] = array('id' => 1, 'text' => 'Y');

		// array for Hold At Fedex Location
		$hold_at_location = array();
		$hold_at_location[] = array('id' => 0, 'text' => 'N');
		$hold_at_location[] = array('id' => 1, 'text' => 'Y');

		// arrays for signature services
		$signature_type = array();
		$signature_type[] = array('id' => '0', 'text' => 'None Required');
		$signature_type[] = array('id' => '2', 'text' => 'Anyone can sign (res only)');
		$signature_type[] = array('id' => '3', 'text' => 'Signature Required');
		$signature_type[] = array('id' => '4', 'text' => 'Adult Signature');

		// arrays for AutoPOD
		$autopod = array();
		$autopod[] = array('id' => 0, 'text' => 'N');
		$autopod[] = array('id' => 1, 'text' => 'Y');

// get & format tomorrow's date for default pickup date
		$dayofweek = strftime("%A", mktime());

		// get the current timestamp into an array
		$timestamp = time();
		$date_time_array = getdate($timestamp);

		$datehours = $date_time_array['hours'];

		if ($dayofweek == Saturday) {
			$default_pickup_date = date('m-d-Y',strtotime('+2 day'));
		} else if ($dayofweek == Sunday) {
			$default_pickup_date = date('m-d-Y',strtotime('+1 day'));

		// Seeing if order after last pickup time, if so set order for tommorrow by default
		} else if ($datehours > $lastshipment) {
			$default_pickup_date = date('m-d-Y',strtotime('+1 day'));
		} else {
		$default_pickup_date = date('m-d-Y',strtotime('today'));
		}

// get the shipping method
		$shipping_query = tep_db_query("select title from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $order . "' and class='ot_shipping'");
		$shipping_method = tep_db_fetch_array($shipping_query);
		$shipping_method = trim($shipping_method['title'], ':');

		$shipping_method_keywords = array('90' => 'Home Delivery',
										  '92' => 'Ground Service',
										  '01' => 'Priority',
										  '03' => '2 Day Air',
										  '05' => 'Standard Overnight',
										  '06' => 'First Overnight',
										  '20' => 'Express Saver');

		// Detect if company if so change to ground service, else default to home delivery.
		if ($order_info['delivery_company']) {
			$shipping_type='92'; // default to Fedex Ground
		} else {
			$shipping_type='90'; // default to Fedex Home
		}
		while (list($shipping_index, $shipping_keyword) = each($shipping_method_keywords)){
			if (false !== strpos($shipping_method, $shipping_keyword)){
	    		  $shipping_type=$shipping_index;
	    		  break 1;
	    	}
    	}

		// get the order qty and item weights
		$order_qty_query = tep_db_query("select * from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . $order . "'");
		$order_qty = 0;
		$order_weight = 0;
		$order_item_html = '';
		if (tep_db_num_rows($order_qty_query)) {
      		while ($order_qtys = tep_db_fetch_array($order_qty_query)){
	      		$order_item_html = $order_item_html . '          <tr>' . "\n" .
             		'            <td class="smallText" align="left">' . $order_qtys['products_quantity'] . ' * ' .
             		$order_qtys['products_name'] . '</td>' . "\n" .
             		'            <td class="smallText" align="left">';
            	$order_qty = $order_qty + $order_qtys['products_quantity'];
            	$products_id = $order_qtys['products_id'];
				$products_weight_query = tep_db_query("select * from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
				if (tep_db_num_rows($products_weight_query)) {
					$products_weights = tep_db_fetch_array($products_weight_query);
					$order_weight = $order_weight + ($order_qtys['products_quantity'] * ($products_weights['products_weight']));
					$item_weights[] = $products_weights['products_weight'];
				}
        	}

            // Find out which weighs more tare or percentage shipping rate and display that rate

        	$order_weight_tar 	  = $order_weight  + SHIPPING_BOX_WEIGHT;
        	$order_weight_percent = ($order_weight * (SHIPPING_BOX_PADDING / 100 + 1));

        	if($order_weight_percent < $order_weight_tar) {
        	$order_weight = $order_weight_tar;
        	} else {
        	$order_weight = $order_weight_percent;
        	}



				$order_weight = round($order_weight,1);
				$order_weight = sprintf("%01.1f", $order_weight);
		}
 	}

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>

<script language="javascript">
function validate() {
	weight=ship_fedex.package_weight.value;
	if (weight=='') {
		alert('<?php echo ENTER_PACKAGE_WEIGHT; ?>');
		event.returnValue=false;
		}
	package_num=ship_fedex.package_num.value;
	if (package_num=='') {
		alert('<?php echo ENTER_NUMBER_PACKAGES; ?>');
		event.returnValue=false;
		}
	}
</script>

</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
<tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
							<td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
<?
	// if it's a new shipment, write out the form for more data
	if ($action=='new') {
?>
          <tr>
            <td class="pageHeading"><?php
						echo HEADING_TITLE;
						if ($order) {
							echo ', ' . ORDER_NUMBER . $order;
							}
						elseif (!$order) {
							echo ERROR_NO_ORDER_SPECIFIED;
							}
						?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="pageHeading" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, 'oID=' . $order) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
          </tr>

          <tr>
          <td colspan="3" class="main" align="left">
          <table border=0 cellpadding=0 cellspacing=0 width="500">
          <tr>
          <td class="main" align="left">
          <b>Sold To:</b><br>
          <?php echo $order_info['delivery_company']; ?><br>&nbsp;
          <?php echo $order_info['customers_name']; ?><br>&nbsp;
          <?php echo $order_info['customers_street_address']; ?><br>&nbsp;
          <?php echo $order_info['customers_city']; ?><br>&nbsp;
          <?php echo $order_info['customers_state']; ?><br>&nbsp;
          <?php echo $order_info['customers_postcode']; ?><br>&nbsp;
          <?php echo $order_info['customers_telephone']; ?><br>&nbsp;
          <td class="main" align="left">
          <b>Shipping To:</b><br>
          <?php echo $order_info['delivery_company']; ?><br>&nbsp;
          <?php echo $order_info['delivery_name']; ?><br>&nbsp;
          <?php echo $order_info['delivery_street_address']; ?><br>&nbsp;
		  <?php echo $order_info['delivery_suburb']; ?><br>&nbsp;
          <?php echo $order_info['delivery_city']; ?><br>&nbsp;
          <?php echo $order_info['delivery_state']; ?><br>&nbsp;
          <?php echo $order_info['delivery_postcode']; ?><br>&nbsp;
          <?php echo $order_info['delivery_phone']; ?><br>&nbsp;</td>
          </tr>
          </table>
          </td>
          </tr>
          <tr>
		  <td colspan="3" class="main" align="left"><b>Shipping Method:</b><br><?php echo $shipping_method ?><br>&nbsp;</td>
		  </tr>
            <?php echo $order_item_html;?>

          </table></td>
						</tr>
        <tr>
							<td>
        		<?php
        			// if quantity = 1, skip to shipping directly
        			if ($order_qty == 1){
					    echo tep_draw_form('ship_fedex', FILENAME_SHIP_FEDEX, 'cPath=' . $cPath . '&cID=' . $HTTP_GET_VARS['cID'] . '&oID=' . $order . '&action=ship', 'post', 'enctype="multipart/form-data"  onsubmit="validate();"');
					}
					// otherwise, go to a 2nd screen to key in individual weights
					else {
						echo tep_draw_form('ship_fedex', FILENAME_SHIP_FEDEX, 'cPath=' . $cPath . '&cID=' . $HTTP_GET_VARS['cID'] . '&oID=' . $order . '&action=post1', 'post', 'enctype="multipart/form-data"  onsubmit="validate();"');
					}
				?>
					<input type="hidden" name="order_item_html" value='<?php echo urlencode(serialize($order_item_html)); ?>'/>
					<input type="hidden" name="item_weights" value='<?php echo urlencode(serialize($item_weights)); ?>'/>
					<input type="hidden" name="fedex_gateway" value="<? echo $fedex_gateway; ?>"/>
					<table width="70%" border="0" cellspacing="0" cellpadding="2">
          	<tr>
          	  <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '15'); ?></td>
          	</tr>
          				<tr>
							<td class="main" align="right"><b><u>Required Fields</u></b></td>
							<td></td>
							<td></td>
						</tr>

						<tr>
							<td class="main" align="right">Number of Packages:</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_input_field('package_num',$order_qty,'size="2"'); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">Oversized?</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_pull_down_menu('oversized',$oversized); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">Packaging Type ("other" for ground shipments):</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_pull_down_menu('packaging_type',$packaging_type); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">Type of Service:</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_pull_down_menu('service_type',$service_type, $shipping_type); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">Payment Type:</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_pull_down_menu('bill_type',$bill_type); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">Dropoff Type:</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_pull_down_menu('dropoff_type',$dropoff_type); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">Pickup date (yyyymmdd):</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_input_field('pickup_date',$default_pickup_date,'size="9"'); ?></td>
						</tr>
						<?php
						// get the transaction value
								$value_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $order . "' and class='ot_subtotal'");
								$order_value = tep_db_fetch_array($value_query);
								$order_value = round($order_value['value'], 0);
						?>
						<tr>
							<td class="main" align="right">Declared Value ($):</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_input_field('declare_value', (string) $order_value, 'size="2"'); ?></td>
							</tr>
						<tr>
						<?php
						$declare_value = round($declare_value, 0);

							if ($package_num > 1) {
								echo '<td class="main" align="right">' . TOTAL_WEIGHT . '</td>';
								}
							else {
								echo '<td class="main" align="right">' . PACKAGE_WEIGHT . '</td>';
								}
						?>
								<td class="main">&nbsp;</td>
										<td class="main"><?php echo tep_draw_input_field('package_weight',(string) $order_weight,'size="2"'); ?></td>
										<td></td>
									</tr>

						<tr>
										<td class="main">&nbsp;</td>
										<td></td>
										<td></td>
									</tr>
						<tr>
							<td class="main" align="right"><b><u>Optional Fields</u></b></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td class="main" align="right">Your Package Dimensions:</td>
							<td class="main">&nbsp;</td>
							<td class="main">&nbsp;Length:&nbsp;<?php echo tep_draw_input_field('dim_length','','size="5" maxlength="30"'); ?>&nbsp;Width:&nbsp;<?php echo tep_draw_input_field('dim_width','','size="5" maxlength="30"'); ?>&nbsp;Height:&nbsp;<?php echo tep_draw_input_field('dim_height','','size="5" maxlength="30"'); ?></td>
						</tr>
						<tr>
							<td class="main" align="right" valign="top">Saturday Delivery?</td>
							<td class="main">&nbsp;</td>
							<td class="main">

												<?php echo tep_draw_pull_down_menu('saturday_delivery',$saturday_delivery); ?>&nbsp;&nbsp;<div class="smallText">
												<font color="#d02727">Only Priority or 2Day Shipping Service Allowed For Saturday Delivery</font></div>
										</td>
						</tr>
						<tr>
							<td class="main" align="right">Invoice #:</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_input_field('package_invoice',$order,'size="33" maxlength="30"'); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">Reference #:</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_input_field('package_reference',$order,'size="33" maxlength="30"'); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">Purchase Order #:</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_input_field('package_po','','size="33" maxlength="30"'); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">Department Name:</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_input_field('package_department','','size="10" maxlength="10"'); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">Signature Options:</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_pull_down_menu('signature_type',$signature_type); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">Hold At Fedex Location (HAL):</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_pull_down_menu('hold_at_location',$hold_at_location); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">HAL Address:</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_input_field('hal_address','','size="33"'); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">HAL City:</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_input_field('hal_city','','size="33"'); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">HAL State:</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_input_field('hal_state','','size="33"'); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">HAL Postal Code:</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_input_field('hal_postcode','','size="33"'); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">HAL Phone #:</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_input_field('hal_phone','','size="33"'); ?></td>
						</tr>

						<tr>
							<td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '15'); ?></td>
						</tr>
						<tr>
							<td class="main" align="right"><b><u>Fedex Ground Fields</u></b></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td class="main" align="right">Automatic Proof of Delivery?</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_pull_down_menu('autopod',$autopod); ?></td>
						</tr>

						<tr>
							<td class="main" align="right"><b><u>Label Specification</u></b></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td class="main" align="right">Label Format Type</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_pull_down_menu('LabelFormatType',$LabelFormatType); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">Image Type</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_pull_down_menu('ImageType',$ImageType); ?></td>
						</tr>
						<tr>
							<td class="main" align="right">Label Specification</td>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_pull_down_menu('LabelStockType',$LabelStockType); ?></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><?php echo tep_image_submit('button_submit.gif', IMAGE_SUBMIT); ?></td>
						<tr>
										<td></td>
										<td></td>
										<td></td>

					</table>
		</form>
<?
		}
	
	elseif ($action=='ship') {

		if($shipSuccess):?>
          	<tr>
          		<td colspan="2" class="pageHeading">
          			<br/>
          			<?php echo HEADING_TITLE; ?>
					<br/>
				</td>
				<td valign="top" class="pageHeading" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, 'oID=' . $order) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>				
			</tr>
			<tr>
				<td colspan="3">
					Shipment is Booked Successfully<br/>
					<h3><a target="_blank" href="<?php echo SHIP_LABEL ?>">Download the Label</a></h3>
					<strong>Tracking Number:<?php echo $trackNum;  ?></strong>
					<strong>Order Number:<?php echo  $order; ?></strong>
				</td>
			</tr>	
				<?php foreach($serviceResponse['labels'] as $key => $shipLabel):?>
					<tr>
							<td colspan="3">
			          			<a href="<?php echo $shipLabel ; ?>" target="_blank">Download the Package Label <?php echo $key++ ?></a>
							</td>	
					</tr>
				<?php endforeach; ?>
          <?php else:?>
          	<tr>
            	<td colspan="2"  class="pageHeading"><br/>Error! Shipment could not be booked</td>
            	<td valign="top" class="pageHeading"><br/><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, 'oID=' . $order) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
            </tr>
            <tr>
            	<td colspan="3"  class="pageHeading"><br/><?php echo $serviceResponse['error']; ?></td>
            </tr>	
          <?php endif;
	}	
	// new form accepts total weight and individual weights
	// if there are multiple packages
	elseif ($action=='post1') {
		$package_num = $HTTP_POST_VARS['package_num'];
		$order_item_html = unserialize(urldecode($HTTP_POST_VARS['order_item_html']));
		$item_weights = unserialize(urldecode($HTTP_POST_VARS['item_weights']));
?>
          <tr>
            <td class="pageHeading"><?php
						echo HEADING_TITLE;
						if ($order) {
							echo ', ' . ORDER_NUMBER . $order;
							}
						elseif (!$order) {
							echo ERROR_NO_ORDER_SPECIFIED;
							}
						?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="pageHeading" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, 'oID=' . $order) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
            <?php echo $order_item_html;?>
          </tr>
        </table></td>
      </tr>
      <tr>
				<td>
					<? echo tep_draw_form('ship_fedex', 'ship_fedex.php', 'cPath=' . $cPath . '&cID=' . $HTTP_GET_VARS['cID'] . '&oID=' . $order . '&action=ship', 'post', 'enctype="multipart/form-data"  onsubmit="validate();"'); ?>
					<input type="hidden" name="fedex_gateway" value="<? echo $fedex_gateway; ?>"/>
					<input type="hidden" name="package_num" value = "<?php echo $package_num; ?>"/>
					<input type="hidden" name="oversized" value="<?php echo $HTTP_POST_VARS['oversized']; ?>"/>
					<input type="hidden" name="saturday_delivery" value="<?php echo $HTTP_POST_VARS['saturday_delivery']; ?>"/>
					<input type="hidden" name="signature_type" value="<?php echo $HTTP_POST_VARS['signature_type']; ?>" />
					<input type="hidden" name="hold_at_location" value="<?php echo $HTTP_POST_VARS['hold_at_location']; ?>"/>
					<input type="hidden" name="hal_address" value="<?php echo $HTTP_POST_VARS['hal_address']; ?>"/>
					<input type="hidden" name="hal_city" value="<?php echo $HTTP_POST_VARS['hal_city']; ?>"/>
					<input type="hidden" name="hal_state" value="<?php echo $HTTP_POST_VARS['hal_state']; ?>"/>
					<input type="hidden" name="hal_postcode" value="<?php echo $HTTP_POST_VARS['hal_postcode']; ?>"/>
					<input type="hidden" name="hal_phone" value="<?php echo $HTTP_POST_VARS['hal_phone']; ?>"/>
					<input type="hidden" name="dim_height" value="<?php echo $HTTP_POST_VARS['dim_height']; ?>"/>
					<input type="hidden" name="dim_width" value="<?php echo $HTTP_POST_VARS['dim_width']; ?>"/>
					<input type="hidden" name="dim_length" value="<?php echo $HTTP_POST_VARS['dim_length']; ?>"/>
					<input type="hidden" name="residential" value="<?php echo $HTTP_POST_VARS['residential']; ?>"/>
					<input type="hidden" name="packaging_type" value="<?php echo $HTTP_POST_VARS['packaging_type']; ?>"/>
					<input type="hidden" name="service_type" value="<?php echo $HTTP_POST_VARS['service_type']; ?>"/>
					<input type="hidden" name="payment_type" value="<?php echo $HTTP_POST_VARS['payment_type']; ?>"/>
					<input type="hidden" name="bill_type" value="<?php echo $HTTP_POST_VARS['bill_type']; ?>"/>
					<input type="hidden" name="dropoff_type" value="<?php echo $HTTP_POST_VARS['dropoff_type']; ?>"/>
					<input type="hidden" name="pickup_date" value="<?php echo $HTTP_POST_VARS['pickup_date']; ?>"/>
					<input type="hidden" name="package_invoice" value="<?php echo $HTTP_POST_VARS['package_invoice']; ?>"/>
					<input type="hidden" name="package_reference" value="<?php echo $HTTP_POST_VARS['package_reference']; ?>"/>
					<input type="hidden" name="package_po" value="<?php echo $HTTP_POST_VARS['package_po']; ?>"/>
					<input type="hidden" name="package_department" value="<?php echo $HTTP_POST_VARS['package_department']; ?>"/>
					<input type="hidden" name="autopod" value="<?php echo $HTTP_POST_VARS['autopod']; ?>"/>
					<input type="hidden" name="LabelFormatType" value="<?php echo $HTTP_POST_VARS['LabelFormatType']; ?>"/>
					<input type="hidden" name="ImageType" value="<?php echo $HTTP_POST_VARS['ImageType']; ?>"/>
					<input type="hidden" name="LabelStockType" value="<?php echo $HTTP_POST_VARS['LabelStockType']; ?>"/>
					<table width="70%" border="0" cellspacing="0" cellpadding="2">
          	<tr>
          	  <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '15'); ?></td>
          	</tr>
						<tr>
							<td class="main" align="right">
<?php
						if ($package_num > 1) {
							echo TOTAL_WEIGHT . '</td>';
							}
						else {
							echo PACKAGE_WEIGHT . '</td>';
							}
?>
							<td class="main">&nbsp;</td>
							<td class="main"><?php echo tep_draw_input_field('package_weight','','size="2"'); ?></td>
						</tr>

<?php
						if ($package_num > 1) {
							
							for ($i = 1; $i <= $package_num; $i++) {
								echo '<tr><td><br/></td></tr><tr>';
								echo '<td class="main" align="right">Package #' . $i . ' Weight:</td>';
								echo '<td class="main">&nbsp;</td>';
								$item_weight_rounded = sprintf("%01.1f", array_pop($item_weights));
								echo '<td class="main">' . tep_draw_input_field('data['.($i - 1).'][weight]', $item_weight_rounded, 'size="2"') . '</td>';
								?>
								<td class="main">&nbsp;Length:&nbsp;<?php echo tep_draw_input_field('data['.($i - 1).'][dim_length]','','size="5" maxlength="30"'); ?>&nbsp;Width:&nbsp;<?php echo tep_draw_input_field('data['.($i - 1).'][dim_width]','','size="5" maxlength="30"'); ?>&nbsp;Height:&nbsp;<?php echo tep_draw_input_field('data['.($i - 1).'][dim_height]','','size="5" maxlength="30"'); ?></td>
								<?php 
								echo '</tr>';
								}
							
						}
?>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><?php echo tep_image_submit('button_submit.gif', IMAGE_SUBMIT); ?></td>
						<tr>
					</table>
					</form>
<?php
			}
?>
					</td>
			</tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php');?>
}
?><i></i>
