<?
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
if (!empty($action) && $action=='initiate_download'){
} else {
	require('cron_application_top.php');
}

include_once('eBay/get-common/AbstractEbayServiceRequest.php');
include_once('eBay/get-common/BulkDataExchangeRequest.php');
include_once('eBay/get-common/DOMUtils.php');
include_once('eBay/get-common/FileTransferServiceDownloadRequest.php');
include_once('eBay/get-common/FileTransferServiceUploadRequest.php');
include_once('eBay/get-common/LargeMerchantServiceSession.php');
include_once('eBay/get-common/PrintUtils.php');
include_once('eBay/get-common/ServiceEndpointsAndTokens.php');
include_once('eBay/get-common/MultiPartMessage.php');

define('DIR_FS_EBAY_FEEDS',     DIR_FS_ROOT . 'eBay/feeds/');

function startDownloadJobRequest($call_name){
	$unique_id = uniqid();
	$requset  = '<?xml version="1.0" encoding="utf-8"?>' . "\n" . 
	$request .= '<startDownloadJobRequest xmlns="http://www.ebay.com/marketplace/services">' . "\n";
	$request .= '<downloadJobType>' . $call_name . '</downloadJobType>' . "\n";
	$request .= '<UUID>' . $unique_id . '</UUID>' . "\n";
	$request .= '</startDownloadJobRequest>';
	return $request;
}

$session = new LargeMerchantServiceSession('XML','XML', EBAY_ENVIRONMENT);

$job_type = 'SoldReport';
$sql = tep_db_query("select id from ebay_download_jobs where job_type='" . tep_db_prepare_input($job_type) . "' and is_open='1'");
if (!tep_db_num_rows($sql)){
    $request = startDownloadJobRequest($job_type);
    $response = $session->sendBulkDataExchangeRequest('startDownloadJob', $request);
    $xml = simplexml_load_string($response);
    if(!empty($xml) && 'Success' == (string)$xml->ack){
        $sql_data = array(
            'environment' => EBAY_ENVIRONMENT, 
            'job_type' => $job_type, 
            'job_id' => (string)$xml->jobId, 
            'status_download_job' => (string)$xml->ack,
            'is_open' => '1',  
			'date_added' => 'now()', 
        );
        tep_db_perform('ebay_download_jobs', $sql_data);
    }
}

?>