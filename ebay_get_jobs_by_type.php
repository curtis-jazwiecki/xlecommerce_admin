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

function createGetJobsRequest($job_status='', $job_type='', $timestamp_from='', $timestamp_upto=''){
	/**
	* API URL for reference: http://developer.ebay.com/DevZone/bulk-data-exchange/CallRef/getJobs.html
	*         job_status values:
	*         Aborted, Completed, Created, Failed, InProcess, Scheduled
	*         
	*         job_type values:
	*         ActiveInventoryReport, AddFixedPriceItem, AddItem, EndFixedPriceItem, EndItem, FeeSettlementReport, OrderAck, RelistFixedPriceItem, RelistItem, ReviseFixedPriceItem, ReviseInventoryStatus, ReviseItem, SetShipmentTrackingInfo, SoldReport, UploadSiteHostedPictures, VerifyAddFixedPriceItem, VerifyAddItem
	*/
	$request  = '<getJobsRequest xmlns="http://www.ebay.com/marketplace/services">' . "\n";
	if (!empty($timestamp_from)){
		$request .= '<creationTimeFrom>' . $data('c', $timestamp_from) . '</creationTimeFrom>' . "\n";
	}
	if (!empty($timestamp_upto)){
		$request .= '<creationTimeTo>' . $data('c', $timestamp_upto) . '</creationTimeTo>' . "\n";
	}
	if (!empty($job_status)){
		$request .= '<jobStatus>' . trim($job_status) . '</jobStatus>' . "\n";
	}
	if (!empty($job_type)){
		$request .= '<jobType>' . trim($job_type) . '</jobType>' . "\n";
	}
	$request .= '</getJobsRequest>';
	return $request;
}

if( isset( $_GET['type'] ) && isset( $_GET['status'] ) ){   
	$session = new LargeMerchantServiceSession('XML','XML', EBAY_ENVIRONMENT);
	$request = createGetJobsRequest($_GET['status'], $_GET['type']);
	PrintUtils::printXML($request);
	$response = $session->sendBulkDataExchangeRequest('getJobs', $request);
	$xml = simplexml_load_string($response);
	PrintUtils::printXML($response);
}
?>