<?php
require('cron_application_top.php');
require_once('eBay/get-common/ServiceEndpointsAndTokens.php');
require_once('eBay/get-common/LargeMerchantServiceSession.php');
require_once('eBay/get-common/PrintUtils.php');
require_once('eBay/get-common/MultiPartMessage.php');

define('DIR_FS_EBAY_FEEDS',     DIR_FS_ROOT . 'eBay/feeds/');

$debug_mode = false;
if ($debug_mode){
    echo 'Start: ' . date('c') . "\n";
}

function createGetJobStatusRequest($jobId){
  $request  = '<getJobStatusRequest xmlns:sct="http://www.ebay.com/soaframework/common/types" xmlns="http://www.ebay.com/marketplace/services">';
  $request .= '<jobId>' . $jobId . '</jobId>';
  $request .= '</getJobStatusRequest>';

  return $request;
}

$session = new LargeMerchantServiceSession('XML','XML', EBAY_ENVIRONMENT);

$sql = tep_db_query("select id, job_id from ebay_jobs where (status_processing is null or status_processing not in ('Failure', 'Aborted', 'Completed', 'Failed', 'custom:Skip', 'custom:XML_error') ) and status_validation='Success' and environment='" . EBAY_ENVIRONMENT . "' order by  id");
if (tep_db_num_rows($sql)){
    while ($entry = tep_db_fetch_array($sql)){
        if ($debug_mode){
            echo 'job id: ' . $entry['job_id'] . "\n";
        }
        $request = createGetJobStatusRequest($entry['job_id']);
        $response = $session->sendBulkDataExchangeRequest('getJobStatus', $request);
        $xml = simplexml_load_string($response);
        $sql_data = array(
            'last_modified' => 'now()', 
        );
        if (!empty($xml)){
            $sql_data['status_processing'] = (string)$xml->jobProfile->jobStatus;
            if ((string)$xml->jobProfile->jobStatus == 'Completed'){
                $sql_data['response_file_reference_id'] = (string)$xml->jobProfile->fileReferenceId;
                $sql_data['response_xml'] = $xml->asXML();
            }
        } else {
            $sql_data['status_processing'] = 'Custom:XML_error';
        }
        tep_db_perform('ebay_jobs', $sql_data, 'update', "id='" . (int)$entry['id'] . "'");
    }
}

$sql = tep_db_query("select id, job_id from ebay_download_jobs where (job_status is null or job_status not in ('Failure', 'Aborted', 'Completed', 'Failed', 'custom:Skip', 'custom:XML_error') ) and status_download_job='Success' and environment='" . EBAY_ENVIRONMENT . "' order by id");
if (tep_db_num_rows($sql)){
    while ($entry = tep_db_fetch_array($sql)){
        if ($debug_mode){
            echo 'job id: ' . $entry['job_id'] . "\n";
        }
        $request = createGetJobStatusRequest($entry['job_id']);
        $response = $session->sendBulkDataExchangeRequest('getJobStatus', $request);
        $xml = simplexml_load_string($response);
        $sql_data = array(
            'last_modified' => 'now()', 
        );
        if (!empty($xml)){
            $sql_data['job_status'] = (string)$xml->jobProfile->jobStatus;
            if ((string)$xml->jobProfile->jobStatus == 'Completed'){
                $sql_data['file_reference_id'] = (string)$xml->jobProfile->fileReferenceId;
                $sql_data['response_xml'] = $xml->asXML();
            }
        } else {
            $sql_data['job_status'] = 'Custom:XML_error';
        }
        tep_db_perform('ebay_download_jobs', $sql_data, 'update', "id='" . (int)$entry['id'] . "'");
    }
}

if ($debug_mode){
    echo 'End: ' . date('c') . "\n";
}
?>