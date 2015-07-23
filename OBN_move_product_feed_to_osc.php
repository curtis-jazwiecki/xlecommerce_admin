<?php
$cron_script='yes';
require_once('cron_application_top.php');
//$status_id = updateOBN('start');
require_once('OBN_global_feed_to_osc.php');
$feed = new global_feed_to_osc();
$feed->product_feed_to_osc();
//updateOBN('complete',$status_id);
// below runs the custom price updates after the main cron has completed
include('OBN_custom_price_fix.php');

/*function updateOBN($status = 'start',$status_id='') {
  $params = array('cron_status' => $status,
				  'retailer_token' => OBN_RETAILER_TOKEN,
				  'retailer_site' => HTTP_SERVER,
				  'script_name' => 'OBN_move_product_feed_to_osc.php',
				  'status_id' => $status_id);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'http://67.227.172.78/admin/update_retailers_status.php');
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
  $resp = curl_exec($ch);
  curl_close($ch);
  return $resp;	
}*/

//require_once('OBN_order_data_to_prog.php');
?>
