<?php
require('cron_application_top.php');
require_once('eBay/get-common/ServiceEndpointsAndTokens.php');
require_once('eBay/get-common/LargeMerchantServiceSession.php');
require_once('eBay/get-common/PrintUtils.php');

$debug_mode = false;
if ($debug_mode){
    echo 'Start: ' . date('c') . "\n";
}

function createStartUploadJobRequest($jobId){
  $request  = '<startUploadJobRequest xmlns:sct="http://www.ebay.com/soaframework/common/types" xmlns="http://www.ebay.com/marketplace/services">';
  $request .= '<jobId>' . $jobId . '</jobId>';
  $request .= '</startUploadJobRequest>';

  return $request;
}

$session = new LargeMerchantServiceSession('XML','XML', EBAY_ENVIRONMENT);

$sql = tep_db_query("select id, job_id from ebay_jobs where is_open='1' and status_create_job='Success' and status_upload_file='Success' and status_validation is null");
if (tep_db_num_rows($sql)){
    while ($entry = tep_db_fetch_array($sql)){
        if ($debug_mode){
            echo 'job id: ' . $entry['job_id'] . "\n";
        }
        $request = createStartUploadJobRequest($entry['job_id']);
        $response = $session->sendBulkDataExchangeRequest('startUploadJob', $request);
        $xml = simplexml_load_string($response);
        if (!empty($xml)){
            $sql_data = array(
              'status_validation' => (string)$xml->ack,
              'is_open' => '0',  
              'last_modified' => 'now()',   
            );
			if ((string)$xml->ack!='Success'){
				$error_category = (string)$xml->errorMessage->error->category;
				if (!empty($error_category)){
					$sql_data['validation_error_category'] = $error_category;
				}
			}
        } else {
            $sql_data = array(
              'status_validation' => 'Custom:XML error',
              'last_modified' => 'now()',   
            );
        }
        tep_db_perform('ebay_jobs', $sql_data, 'update', "id='" . $entry['id'] . "'");
        if ($debug_mode){
            PrintUtils::printXML($response);
            echo "\n";
        }
    }
}

if ($debug_mode){
    echo 'End: ' . date('c') . "\n";
}
?>