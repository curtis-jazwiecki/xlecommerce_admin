<?php
require('includes/application_top.php');
include('order_editor/functions.php');
include('order_editor/cart.php');
include('order_editor/order.php');
include('order_editor/shipping.php');
include('order_editor/http_client.php');

$method = null;
$preferred_payment_method = $_POST['preferred_payment_method'];
$oID = $_POST['oID'];
$cc_owner = $_POST['cc_owner'];
$cc_type = $_POST['cc_type'];
$cc_number = $_POST['cc_number'];
$cc_expiry_month = str_pad($_POST['cc_expiry_month'], 2, '0', STR_PAD_LEFT);
$cc_expiry_year = substr($_POST['cc_expiry_year'], 2);
$cc_cvv = $_POST['cc_cvv'];

$order = new manualOrder($oID);

$response = array();

if (!empty($preferred_payment_method)){
	switch($preferred_payment_method){
		case 'authorizenet_aim':
			$_POST['authorizenet_aim_cc_owner'] = $cc_owner;
			$_POST['cc_type'] = $cc_type;
			$_POST['authorizenet_aim_cc_number'] = $cc_number;
			$_POST['authorizenet_aim_cc_expires_month'] = $cc_expiry_month;
			$_POST['authorizenet_aim_cc_expires_year'] = $cc_expiry_year;
			$_POST['authorizenet_aim_cc_cvv'] = $cc_cvv;
			include_once(DIR_FS_CATALOG_LANGUAGES . 'english/modules/payment/authorizenet_aim.php');
			include_once(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/authorizenet_aim.php');
			include_once(DIR_WS_MODULES . 'payment/admin_authorizenet_aim.php');
			$method = new admin_authorizenet_aim();
			break;
		case 'fastcharge':
			$_POST['fastcharge_cc_owner'] = $cc_owner;
			$_POST['cc_type'] = $cc_type;
			$_POST['fastcharge_cc_number'] = $cc_number;
			$_POST['fastcharge_cc_expires_month'] = $cc_expiry_month;
			$_POST['fastcharge_cc_expires_year'] = $cc_expiry_year;
			$_POST['fastcharge_cc_cvv'] = $cc_cvv;
			include_once(DIR_FS_CATALOG_LANGUAGES . 'english/modules/payment/fastcharge.php');
			include_once(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/fastcharge.php');
			include_once(DIR_WS_MODULES . 'payment/admin_fastcharge.php');
			$method = new admin_fastcharge();
			break;
	}
	
	if (!empty($method)){
		include(DIR_WS_CLASSES . 'cc_validation.php');
		$cc_validation = new cc_validation();
		$result = $cc_validation->validate($cc_number, $cc_expiry_month, $cc_expiry_year, $cc_cvv);
		
		$error = '';
		switch ($result) {
			case -1:
				$error = sprintf(TEXT_CCVAL_ERROR_UNKNOWN_CARD, substr($cc_validation->cc_number, 0, 4));
				break;
			case -2:
			case -3:
			case -4:
				$error = TEXT_CCVAL_ERROR_INVALID_DATE;
				break;
			case false:
				$error = TEXT_CCVAL_ERROR_INVALID_NUMBER;
				break;
		}
		
		if ( ($result == false) || ($result < 1) ) {
			$response['error'] = $error;
			echo json_encode($response);
			exit;
		}
		$method->cc_card_type = $cc_validation->cc_type;
		$method->cc_card_number = $cc_validation->cc_number;
		$method->cc_expiry_month = $cc_validation->cc_expiry_month;
		$method->cc_expiry_year = $cc_validation->cc_expiry_year;
		
		$error_status = $method->before_process();
		if ($error_status){
			echo json_encode($error_status);
			exit;
		}
		tep_db_query("update orders set payment_method='Credit Card : " . $preferred_payment_method  . "' where orders_id='" . (int)$oID . "'");
		$method->after_process();
	}
}
?>