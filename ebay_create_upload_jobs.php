<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
if (!empty($action) && $action=='initiate_upload'){
    $argv[1] = 'AddFixedPriceItem';
} else {
	require('cron_application_top.php');
}

if (isset($_GET['jobtype'])) {
  $argv[1] = $_GET['jobtype'];  
}


require_once('eBay/get-common/ServiceEndpointsAndTokens.php');
require_once('eBay/get-common/LargeMerchantServiceSession.php');
require_once('eBay/get-common/PrintUtils.php');

$debug_mode = false;
if ($debug_mode){
    echo 'Start: ' . date('c') . "\n";
}

function createUploadJobRequest($jobType, $uuid){
  $request  = '<createUploadJobRequest xmlns:sct="http://www.ebay.com/soaframework/common/types" xmlns="http://www.ebay.com/marketplace/services">';
  $request .= '<uploadJobType>' . $jobType . '</uploadJobType>';
  $request .= '<UUID>' . $uuid . '</UUID>';
  $request .= '</createUploadJobRequest>';
  return $request;
}

function createUploadJob($job_type){
	global $session, $debug_mode;
	$open_job_query = tep_db_query("select id from ebay_jobs where job_type='" . tep_db_prepare_input($job_type) . "' and is_open='1' and status_upload_file is null and status_validation is null");
	$matches = tep_db_num_rows($open_job_query);
	if (!$matches){
		$unique_id = microtime(true);
        $error_message = '';
		$request = createUploadJobRequest($job_type, $unique_id);
		$response = $session->sendBulkDataExchangeRequest('createUploadJob', $request);
		$xml = simplexml_load_string($response);
		$sql_data = array(
			'environment' => EBAY_ENVIRONMENT, 
			'job_type' => $job_type, 
			'job_id' => (string)$xml->jobId,
			'file_reference_id' => (string)$xml->fileReferenceId, 
			'status_create_job' => (string)$xml->ack,
            'date_added' => 'now()',  
		);
		if (!empty($xml)){
			if ((string)$xml->ack!='Success'){
				$sql_data['is_open'] = '0';
				$error_category = (string)$xml->errorMessage->error->category;
				if (!empty($error_category)){
                    $error_message = (string)$xml->errorMessage->error->message;
					$sql_data['create_job_error_category'] = $error_category . ':' . $error_message;
				}
			}
		} else {
			$sql_data['status_create_job'] = 'Custom:XML error';
			$sql_data['is_open'] = '0';
		}
		tep_db_perform('ebay_jobs', $sql_data);
        if ($debug_mode){
            PrintUtils::printXML($response);
            echo "\n";
        }
        if ($error_message == 'Maximum of one job per job-type in non-terminated state is allowed') {
  
          $request = '<getJobsRequest xmlns="http://www.ebay.com/marketplace/services">' . 
                     ' <creationTimeFrom>'  . date("Y-m-d\TH:i:s.u", strtotime("-1 month")) . '</creationTimeFrom>' . 
                     ' <creationTimeTo>'  . date("Y-m-d\TH:i:s.u") . '</creationTimeTo>' . 
                     '<jobStatus>Created</jobStatus>' .
                     '<jobStatus>InProcess</jobStatus>' .
                     '<jobType>' . $job_type . '</jobType>' . 
                     '</getJobsRequest>' ;          
          $response = $session->sendBulkDataExchangeRequest('getJobs', $request);
          $xml=simplexml_load_string($response);
          foreach ($xml->jobProfile as $jobs => $data) {
           $jobId = $data->jobId;
 
	       $request  = '<abortJobRequest xmlns:sct="http://www.ebay.com/soaframework/common/types" xmlns="http://www.ebay.com/marketplace/services">';
	       $request .= '<jobId>' . $jobId . '</jobId>';
	       $request .= '</abortJobRequest>';
	       $response = $session->sendBulkDataExchangeRequest('abortJob', $request);
	       $xml = simplexml_load_string($response);  
           	if (!empty($xml)){
			 if ((string)$xml->ack!='Success'){ 
			     exit;
                 }
                } 
          tep_db_query("update ebay_jobs set status_processing='Aborted', is_open='0' where job_id='" . $jobId . "'");      
          }
           
         createUploadJob($job_type);  

        }
        /*if (strtolower($sql_data['status_create_job'])!='success'){
            $to = 'tech1@outdoorbusinessnetwork.com';
            $subject = 'ebay job stuck: ' . $sql_data['job_type'] . '#' . tep_db_insert_id();
            $message = 'ebay job stuck: ' . $sql_data['job_type'] . '#' . tep_db_insert_id();
            $headers = 'From: notification@visionoutfitter.com' . "\r\n";
            mail($to, $subject, $message, $headers);
        }*/
	} else {
        if ($debug_mode){
            echo "job already open\n";
        }
	}
}

$session = new LargeMerchantServiceSession('XML','XML', EBAY_ENVIRONMENT);

if ($argv[1]=='ReviseFixedPriceItem' || $argv[1]=='RelistFixedPriceItem'){
	if ($argv[1]=='ReviseFixedPriceItem'){
		$job_type = 'ReviseFixedPriceItem';
	} elseif ($argv[1]=='RelistFixedPriceItem'){
		$job_type = 'RelistFixedPriceItem';
	}
	
	if ($debug_mode){
		echo $job_type . ' job handling start: ' . date('c') . "\n";
	}

	//$sql = tep_db_query("select count(products_id) as count from products where products_status='1' and is_ebay_ok='1' and ebay_category_id>0 and item_listed_on_ebay='1'");
	$sql = tep_db_query("select count(products_id) as count from products where products_status='1' and is_ebay_ok='1' and ebay_category_id>0 and item_listed_on_ebay='1' and products_model not in (select sku from ebay_product_feed_errors where environment='" . EBAY_ENVIRONMENT . "' and ack='failure')");
	$info = tep_db_fetch_array($sql);
	if ($info['count']>0){
		createUploadJob($job_type);
	} else {
		if ($debug_mode){
			echo "No entries to move\n";
		}
	}
	if ($debug_mode){
		echo $job_type . ' job handling end: ' . date('c') . "\n";
	}
}

if ($argv[1]== 'AddFixedPriceItem'){
	$job_type = 'AddFixedPriceItem';
	if ($debug_mode){
		echo $job_type . ' job handling start: ' . date('c') . "\n";
	}
	$sql = tep_db_query("select count(products_id) as count from products where products_status='1' and is_ebay_ok='1' and ebay_category_id>0 and item_listed_on_ebay='0' and products_model not in (select sku from ebay_product_feed_errors where environment='" . EBAY_ENVIRONMENT . "' and ack='failure')");
	$info = tep_db_fetch_array($sql);
	if ($info['count']>0){
		createUploadJob($job_type);
	} else {
		if ($debug_mode){
			echo "No entries to move\n";
		}
	}
	if ($debug_mode){
		echo $job_type . ' job handling end: ' . date('c') . "\n";
	}
}

if ($argv[1]=='EndFixedPriceItem'){
	$job_type = 'EndFixedPriceItem';
	if ($debug_mode){
		echo $job_type . ' job handling start: ' . date('c') . "\n";
	}
	//$sql = tep_db_query("select count(products_id) as count from products where (products_status='0' or is_ebay_ok='0') and item_listed_on_ebay='1'");
	$sql = tep_db_query("select count(products_id) as count from products where ( (products_status='0' and is_ebay_ok='1') or (is_ebay_ok='0' and item_listed_on_ebay='1') )");
    /*
    //below query was set to remove all items with 0 inventory but it seems to cause issue s when relist feed fires
    //activation previous query
    $sql = tep_db_query("select count(products_id) as count from products where ( (products_status='0' and is_ebay_ok='1') or (is_ebay_ok='0' and item_listed_on_ebay='1')  or ( (is_ebay_ok='1' or item_listed_on_ebay='1') and products_quantity<" . (int)EBAY_MIN_STOCK_QTY . " )  )");
    */
	$info = tep_db_fetch_array($sql);
	if ($info['count']>0){
		createUploadJob($job_type);	
	} else {
		if ($debug_mode){
			echo "No entries to move\n";
		}
	}
	if ($debug_mode){
		echo $job_type . ' job handling end: ' . date('c') . "\n";
	}
}

if ($argv[1]=='ReviseInventoryStatus'){
	$job_type = 'ReviseInventoryStatus';
	if ($debug_mode){
		echo $job_type . ' job handling start: ' . date('c') . "\n";
	}
	$sql = tep_db_query("select count(products_id) as count from products where products_status='1' and is_ebay_ok='1' and item_listed_on_ebay='1' and products_model not in (select sku from ebay_product_feed_errors where environment='" . EBAY_ENVIRONMENT . "' and ack='failure')");
	$info = tep_db_fetch_array($sql);
	if ($info['count']>0){
		createUploadJob($job_type);	
	} else {
		if ($debug_mode){
			echo "No entries to move\n";
		}
	}
	if ($debug_mode){
		echo $job_type . ' job handling end: ' . date('c') . "\n";
	}
}

if ($argv[1]=='OrderAck'){
	$job_type = 'OrderAck';
	if ($debug_mode){
		echo $job_type . ' job handling start: ' . date('c') . "\n";
	}
	$sql = tep_db_query("select count(id) as count from ebay_order_products where ack_generated='0'");
	$info = tep_db_fetch_array($sql);
	if ($info['count']>0){
		createUploadJob($job_type);
	} else {
		if ($debug_mode){
			echo "No entries to move\n";
		}
	}
	if ($debug_mode){
		echo $job_type . ' job handling end: ' . date('c') . "\n";
	}
}

if ($argv[1]=='SetShipmentTrackingInfo'){
	$job_type = 'SetShipmentTrackingInfo';
	if ($debug_mode){
		echo $job_type . ' job handling start: ' . date('c') . "\n";
	}
	$sql = tep_db_query("select count(id) as count from ebay_order_products where fire_shipment_feed='1' and is_shipped='0'");
	$info = tep_db_fetch_array($sql);
	if ($info['count']>0){
		createUploadJob($job_type);
	} else {
		if ($debug_mode){
			echo "No entries to move\n";
		}
	}
	if ($debug_mode){
		echo $job_type . ' job handling end: ' . date('c') . "\n";
	}

	if ($debug_mode){
		echo 'End: ' . date('c') . "\n";
	}
}
?>