<?
/**
 * track_fedex.php
 *
 * This is the updated tracking script to view the tracking status of shipped products. It uses
 * the FedEx Web Service API and requires a production API key to work.
 *
 * CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
 */
require('includes/application_top.php');

// debugging? 
	
$debug = 0; // 1 for yes, 0 for no

$action = $HTTP_GET_VARS['action'];
$order = $HTTP_GET_VARS['oID'];
$tracking_number = $HTTP_GET_VARS['num'];

require_once(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'library/fedex-common.php5');

$path_to_wsdl = DIR_FS_CATALOG . DIR_WS_INCLUDES . "wsdl/TrackService_v5.wsdl";

ini_set("soap.wsdl_cache_enabled", "0");

$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

$request['WebAuthenticationDetail'] = array(
	'UserCredential' =>array(
		'Key' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_KEY, 
		'Password' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_PWD
	)
);
$request['ClientDetail'] = array(
	'AccountNumber' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_ACT_NUM, 
	'MeterNumber' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_METER_NUM
);

$request['TransactionDetail'] = array('CustomerTransactionId' => '*** Track Request v5 using PHP ***');
$request['Version'] = array(
	'ServiceId' => 'trck', 
	'Major' => '5', 
	'Intermediate' => '0', 
	'Minor' => '0'
);
$request['PackageIdentifier'] = array(
	'Value' => $tracking_number, // Replace 'XXX' with a valid tracking identifier
	'Type' => 'TRACKING_NUMBER_OR_DOORTAG');

$response = null;
try 
{
	if(setEndpoint('changeEndpoint'))
	{
		$newLocation = $client->__setLocation(setEndpoint('endpoint'));
	}
	
	$response = $client->track($request);

	if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR')
	{
		if ($debug) {
			echo '<table border="1">';
			echo '<tr><th>Tracking Details</th><th>&nbsp;</th></tr>';
			trackDetails($response->TrackDetails, '');
			echo '</table>';
			
			printSuccess($client, $response);
		}
	}
	else
	{
		//echo "<!-- " . printError($client, $response) . " -->";
	} 
	
} catch (SoapFault $exception) {
	//echo "<!-- " . printFault($exception, $client) . " -->";
}

/*// create new FedExDC object
// For tracking results you do not need an account# or meter#
	$fed = new FedExDC();
	
//tracking example
	$track_Ret = $fed->ref_track(array(1537 => $tracking_number,));

// debug (prints array of all data returned)

if ($debug) {

	echo '<pre>';

	if ($error = $fed->getError()) {
	  echo "ERROR :". $error;
		} else {
	  echo $fed->debug_str. "\n<BR>";
	  print_r($track_Ret);
	  echo "\n\n";
	  for ($i=1; $i<=$track_Ret[1584]; $i++) {
	  	echo PACKAGE_DELIVERED_ON . $track_Ret['1720-'.$i];
	    echo '\n' . PACKAGE_SIGNED_BY . $track_Ret['1706-'.$i];
		  }
		}

	echo '</pre>';
	}
*/
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
	<title><?php echo TITLE; ?></title>
	<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
	<script language="javascript" src="includes/general.js"></script>
</head> <!-- header //-->
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
	<tr>
		<td width="<?php echo BOX_WIDTH; ?>" valign="top">
			<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
				<!-- left_navigation //-->
				<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
				<!-- left_navigation_eof //-->
			</table>
		</td>
		
		<!-- body_text //-->
		<td width="100%" valign="top">
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
				<tr>
					<td width="100%">
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td class="pageHeading">
									<?php echo HEADING_TITLE . ($order ? ', ' . ORDER_NUMBER . $order : '') ?>
								</td>
								<td class="pageHeading" align="right">
									<?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?>
								</td>
								<td class="pageHeading" align="right">
									<?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))) . 'oID=' . $order . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table width="80%" border="0" cellspacing="0" cellpadding="2">
							<tr>
								<td><?php echo tep_draw_separator('pixel_trans.gif', 1, 15); ?></td>
							</tr>
							<tr>
								<td valign="top">
									<table width="100%" border="0" cellspacing="0" cellpadding="2">
									<?php if ($response -> HighestSeverity == 'FAILURE' || $response -> HighestSeverity == 'ERROR') : ?>
										<tr>
											<td class="main"><?php echo $response -> Notifications -> Message; ?></td>
										</tr> 
									<?php 
										else :
											
											foreach($response->TrackDetails as $detail) :
												// list destination
												$dest_city = $detail->DestinationAddress->City;
												$dest_state = $detail->DestinationAddress->StateOrProvinceCode;
												$dest_zip = $detail->DestinationAddress->PostalCode;
												$signed_by = $detail->DeliverySignatureName;
												
												// format delivery time
												if ($signed_by) {
													$delivery_time= strtotime($detail->EstimatedDeliveryTimestamp);
												}
			
												/*	for ($i=1; $i<=$track_Ret[1584]; $i++) {
											// list destination
														$dest_city = $track_Ret['15-'.$i];
														$dest_state = $track_Ret['16-'.$i];
														$dest_zip = $track_Ret['17-'.$i];
														$signed_by = $track_Ret['1706-'.$i];
														$delivery_date = $track_Ret['1720-'.$i];
														$delivery_time = $track_Ret['1707-'.$i];
														
											// format delivery time
														if ($signed_by)
														$delivery_date = strtotime($delivery_date);
														$delivery_date = date("F j, Y", $delivery_date);
														
											// format time, determine am or pm
														$hour = substr($delivery_time,0,2);
														$minute = substr($delivery_time,2,2);
														if ($hour >= 12) {
															$time_mod = 'pm';
											// make pm hours non-military time
															if ($hour > 12) {
																$hour = ($hour - 12);
																}
															}
														else {
															$time_mod = 'am';
															}*/
											
											// everyone (other than error messages) gets a status report
										?>
											<tr>
												<td class="main"><b><?php echo PACKAGE_DESTINATION ?></b></td>
											</tr>
											<tr>
												<td class="main"><?php echo $dest_city . ', ' . $dest_state . ' ' . $dest_zip ?></td>
											</tr>
											<tr>
												<td><?php echo tep_draw_separator('pixel_trans.gif', 1, 15); ?></td>
											</tr>
											<tr>
												<td class="main"><b><?php echo PACKAGE_STATUS ?></b></td>
											</tr>
											<?php if ($signed_by) :
						
												// if left without signature, let them know
												// (add more as they appear)
												if (strstr($signed_by,'F.RONTDOOR')) {
													$signed_by = '<tr><td class="main">' . DELIVERED_FRONTDOOR . '</td></tr>';
													}
												if (strstr($signed_by,'S.IDEDOOR')) {
													$signed_by = '<tr><td class="main">' . DELIVERED_SIDEDOOR . '</td></tr>';
													}
												if (strstr($signed_by,'G.ARAGE')) {
													$signed_by = '<tr><td class="main">' . DELIVERED_GARAGE . '</td></tr>';
													}
												if (strstr($signed_by,'B.ACKDOOR')) {
													$signed_by = '<tr><td class="main">' . DELIVERED_BACKDOOR . '</td></tr>';
													}
											?>
												<tr>
													<td class="main">
														<?php echo PACKAGE_DELIVERED_ON . date("F j, Y", $delivery_date) . PACKAGE_DELIVERED_AT . date("g:ia", $delivery_date) ?>
													</td>
												</tr>
												<tr>
													<td><?php echo tep_draw_separator('pixel_trans.gif', 1, 15); ?></td>
												</tr>
												<tr>
													<td class="main"><b><?php echo PACKAGE_SIGNED_BY ?></b></td>
												</tr>
												<tr>
													<td class="main"><?php echo $signed_by ?></td>
												</tr>
												<?php else : ?>
												<tr>
													<td class="main"><b><?php echo PACKAGE_IN_TRANSIT ?></b></td>
												</tr>
												<tr>
													<td class="main">
														<?php 
														foreach($detail->TrackEvents as $track_event) { 
															$status_note = $track_event->EventDescription;
															$status_city = $track_event->ArrivalLocation->City;
															$status_state = $track_event->ArrivalLocation->StateOrProvinceCode;
														
															echo "<p>{$status_note} : {$status_city}, {$status_state}</p>";
														}
														?>
													</td>
												</tr>
												<?php endif; ?>
											<?php endforeach; ?>
										<?php endif; ?>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td> <!-- body_text_eof //--> 
		</tr>
	</table> <!-- body_eof //--> 
	<!-- footer //-->
	<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
	<!-- footer_eof //--> 
	<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); 
?>
