<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require('cron_application_top.php');
require_once('eBay/get-common/ServiceEndpointsAndTokens.php');
require_once('eBay/get-common/LargeMerchantServiceSession.php');
require_once('eBay/get-common/PrintUtils.php');
require_once('eBay/get-common/MultiPartMessage.php');

define('DIR_FS_EBAY_FEEDS',     DIR_FS_ROOT . 'eBay/feeds/');

function createAbortJobRequest($jobId){
	$request  = '<abortJobRequest xmlns:sct="http://www.ebay.com/soaframework/common/types" xmlns="http://www.ebay.com/marketplace/services">';
	$request .= '<jobId>' . $jobId . '</jobId>';
	$request .= '</abortJobRequest>';
	return $request;
}

if( isset( $_GET['job_id'] ) ){   
	$session = new LargeMerchantServiceSession('XML','XML', EBAY_ENVIRONMENT);
	$request = createAbortJobRequest( $_GET['job_id'] );
	PrintUtils::printXML($request);
	$response = $session->sendBulkDataExchangeRequest('abortJob', $request);
	$xml = simplexml_load_string($response);
	PrintUtils::printXML($response);
}
?>