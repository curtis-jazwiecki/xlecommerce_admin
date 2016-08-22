<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require('cron_application_top.php');

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
$debug_mode = false;
function createDownloadRequest($taskReferenceId, $fileReferenceId){
	$request  = '<downloadFileRequest xmlns:sct="http://www.ebay.com/soaframework/common/types" xmlns="http://www.ebay.com/marketplace/services">';
	$request .= '<taskReferenceId>' . $taskReferenceId . '</taskReferenceId>';
	$request .= '<fileReferenceId>' . $fileReferenceId . '</fileReferenceId>';
	$request .= '</downloadFileRequest>';
        
	return $request;
}


function parseForResponseXML($response){
	$beginResponseXML = strpos($response, '<?xml');
		
	$endResponseXML = strpos($response, '</downloadFileResponse>', $beginResponseXML);
		
	//Assume a service level error and die.
	if($endResponseXML === FALSE) {
		$errorXML = parseForErrorMessage($response);		if ($debug_mode){
			PrintUtils::printXML($errorXML);		}
		die();
	}	
		
	$endResponseXML += strlen('</downloadFileResponse>');
		
	return substr($response, $beginResponseXML, $endResponseXML - $beginResponseXML);
}

function parseForXopIncludeUUID($responseDOM){
	$xopInclude = $responseDOM->getElementsByTagName('Include')->item(0);
	$uuid = $xopInclude->getAttributeNode('href')->nodeValue;
	$uuid = substr($uuid, strpos($uuid,'urn:uuid:'));
		
	return $uuid;
}

function parseForFileBytes($uuid, $response){
	$contentId = 'Content-ID: <' . $uuid . '>';
		
	$mimeBoundaryPart = strpos($response,'--MIMEBoundaryurn_uuid_');
		
	$beginFile = strpos($response, $contentId, $mimeBoundaryPart);
	$beginFile += strlen($contentId);
		
	//Accounts for the standard 2 CRLFs.
	$beginFile += 4;
		
	$endFile = strpos($response,'--MIMEBoundaryurn_uuid_',$beginFile);
		
	//Accounts for the standard 1 CRLFs.
	$endFile -= 2;
		
	$fileBytes = substr($response, $beginFile, $endFile - $beginFile);
		
	return $fileBytes;
}

function writeZipFile($bytes, $zipFilename){	if ($debug_mode){		echo "<p><b>Writing File to $zipFilename : ";	}
	
		
	$handler = fopen($zipFilename, 'wb') or die("Failed. Cannot Open $zipFilename to Write!</b></p>");
	fwrite($handler, $bytes);
	fclose($handler);
			if ($debug_mode){
		echo 'Success.</b></p>';	}
}

function download_file($job_type, $job_id, $file_reference_id){
	global $session;
	$file_name = null;
	
	$request = createDownloadRequest($job_id, $file_reference_id);
	$response = $session->sendFileTransferServiceDownloadRequest($request);
	$responseXML = parseForResponseXML($response);
	$responseDOM = DOMUtils::createDOM($responseXML);
        	if ($debug_mode){
		PrintUtils::printDOM($responseDOM);	}
	$uuid = parseForXopIncludeUUID($responseDOM);
	$fileBytes = parseForFileBytes($uuid, $response);
        
	$zip_file_name = DIR_FS_EBAY_FEEDS . $job_type . '_' . microtime(true) . '.zip';
	writeZipFile($fileBytes, $zip_file_name);
        
	$xml = simplexml_load_string($responseXML);
	if(!empty($xml) && 'Success' == (string)$xml->ack){
		$zip = new ZipArchive();
		$resource = $zip->open($zip_file_name);
		if ($resource===true){
			for($i=0; $i<=$zip->numFiles; $i++){
				$file_name = $zip->getNameIndex($i);
				if (strpos($file_name, '_report')!==false || strpos($file_name, '_response')!==false){
					$zip->extractTo(DIR_FS_EBAY_FEEDS, array($file_name));
					break;
				}
			}
		}
	}
	return $file_name;
}

function process_ebay_response_for_item($job_type, $job_id, $sku=null, $item_node=null){
	if (!empty($item_node) && !empty($sku)){
		$ack = (string)$item_node->Ack;
		$message = empty($item_node->Message) ? null : (string)$item_node->Message;
		$error_messages = '';
		foreach($item_node->Errors as $error){
			$error_text = '';

			$severity_code = (string)$error->SeverityCode;
			if (!empty($severity_code)) $error_text .= 'Severity Code: ' . $severity_code . '<br>';
			
			$short_message = (string)$error->ShortMessage;
			if (!empty($short_message)) $error_text .= 'Short Message: ' . $short_message . '<br>';
			
			$long_message = (string)$error->LongMessage;
			if (!empty($long_message)) $error_text .= 'Long Message: ' . $long_message . '<br>';
			
			$error_code = (string)$error->ErrorCode;
			if (!empty($error_code)) $error_text .= 'Error Code: ' . $error_code . '<br>';
			
			if (!empty($error_text)){
				$error_text .= '<br>';
				$error_messages .= $error_text;
			}
		}
		
		if (!empty($error_messages)) $error_messages = substr($error_messages, 0, -4);
		
		if (!empty($error_messages) || !empty($message)){			if ($job_type!='RelistFixedPriceItem'){				tep_db_query("insert ignore into ebay_product_feed_errors (sku, job_type, environment, ack, error_messages, message, date_added) values ('" . tep_db_prepare_input($sku) . "', '" . tep_db_prepare_input($job_type) . "', '" . tep_db_prepare_input(EBAY_ENVIRONMENT) . "', '" . tep_db_prepare_input($ack) . "', " . (empty($error_messages) ? "null" : "'" .tep_db_prepare_input($error_messages) . "'")  . ", " . (empty($message) ? "null" : "'" .tep_db_prepare_input($message) . "'") . ", now()) on duplicate key update job_type='" . tep_db_prepare_input($job_type) . "', ack='" . tep_db_prepare_input($ack) . "', error_messages=" . (empty($error_messages) ? "null" : "'" .tep_db_prepare_input($error_messages) . "'")  . ", message=" . (empty($message) ? "null" : "'" .tep_db_prepare_input($message) . "'") . ", date_added=now()");			}

		} else {
			tep_db_query("delete from ebay_product_feed_errors where sku='" . tep_db_prepare_input($sku) . "' and job_type='" . tep_db_prepare_input($job_type) . "' and environment='" . tep_db_prepare_input(EBAY_ENVIRONMENT) . "'");
		}
	} else {
		$sql = tep_db_query("select uploaded_file as request, report_file as response from ebay_jobs where job_id='" . tep_db_prepare_input($job_id) . "'");
		if (tep_db_num_rows($sql)){
			$info = tep_db_fetch_array($sql);
			if (!empty($info['request']) && !empty($info['response'])){
				$request_feed = DIR_FS_EBAY_FEEDS . substr($info['request'], 0, -2) . 'xml';
				$response_feed = DIR_FS_EBAY_FEEDS . $info['response'];
				if (file_exists($request_feed) && file_exists($response_feed)){
					$request = @simplexml_load_file($request_feed);
					$response = @simplexml_load_file($response_feed);
					if ($request && $response){
						$status = array();
						foreach($response as $entry){
							$status[(string)$entry->CorrelationID] = $entry;
						}
						foreach($request->children() as $product){
							$message_id = (string)$product->MessageID;
							if (!empty($message_id)){
								if (array_key_exists($message_id, $status)){
									$sku = (string)$product->Item->SKU;
									$ack = (string)$status[$message_id]->Ack;
									process_ebay_response_for_item($job_type, $job_id, $sku, $status[$message_id]);
								}
							}
						}
					}
				}
			}
		}
	}
}

$session = new LargeMerchantServiceSession('XML','XML', EBAY_ENVIRONMENT);

$sql = tep_db_query("select id, job_type, job_id, file_reference_id from ebay_download_jobs where status_download_job='Success' and is_open='1' and job_status='Completed' order by id");
if (tep_db_num_rows($sql)){
    while($entry = tep_db_fetch_array($sql)){
		$file_name = download_file($entry['job_type'], $entry['job_id'], $entry['file_reference_id']);
		if (!empty($file_name)){
				$sql_data = array(
					'is_open' => '0', 
					'is_downloaded' => '1', 
					'downloaded_file' => $file_name, 
					'last_modified' => 'now()', 
				);
				tep_db_perform('ebay_download_jobs', $sql_data, 'update', "id='" . (int)$entry['id'] . "'");
		}
    }
}

$sql = tep_db_query("select id, job_type, job_id, response_file_reference_id  from ebay_jobs where status_processing='Completed' and response_file_reference_id is not null and report_downloaded='0' order by id");
if (tep_db_num_rows($sql)){
    while($entry = tep_db_fetch_array($sql)){
		$file_name = download_file($entry['job_type'], $entry['job_id'], $entry['response_file_reference_id']);
		if (!empty($file_name)){
				$sql_data = array(
					'report_downloaded' => '1', 
					'report_file' => $file_name, 
					'last_modified' => 'now()', 
				);
				tep_db_perform('ebay_jobs', $sql_data, 'update', "id='" . (int)$entry['id'] . "'");
				
				
				switch($entry['job_type']){
					case 'AddFixedPriceItem':
					case 'ReviseFixedPriceItem':
					case 'EndFixedPriceItem':
					case 'RelistFixedPriceItem':
						$xml = simplexml_load_file(DIR_FS_EBAY_FEEDS . $file_name);
						if ($xml){
							foreach($xml->children() as $item){
								$sku = empty($item->SKU) ? null : (string)$item->SKU;
								process_ebay_response_for_item($entry['job_type'], $entry['job_id'], $sku, $item);
								/*
								$sku = (string)$item->SKU;
								$ack = (string)$item->Ack;
								$message = (string)$item->Message;
								$error_messages = '';
								foreach($item->Errors as $error){
									$error_text = (string)$error->LongMessage;
									$severity_code = (string)$error->SeverityCode;
									if (!empty($error_text)){
										$error_messages .= "$severity_code: $error_text<br>";
									}
								}
								if (!empty($sku)){
									if (!empty($error_messages) || !empty($message)){
										tep_db_query("insert ignore into ebay_product_feed_errors (sku, job_type, environment, ack, error_messages, message, date_added) values ('" . tep_db_prepare_input($sku) . "', '" . tep_db_prepare_input($entry['job_type']) . "', '" . tep_db_prepare_input(EBAY_ENVIRONMENT) . "', '" . tep_db_prepare_input($ack) . "', " . (empty($error_messages) ? "null" : "'" .tep_db_prepare_input($error_messages) . "'")  . ", " . (empty($message) ? "null" : "'" .tep_db_prepare_input($message) . "'") . ", now())");
									} else {
										tep_db_query("delete from ebay_product_feed_errors where sku='" . tep_db_prepare_input($sku) . "' and job_type='" . tep_db_prepare_input($entry['job_type']) . "' and environment='" . tep_db_prepare_input(EBAY_ENVIRONMENT) . "'");
									}
								}
								*/
							}
						}
						break;
					case 'ReviseInventoryStatus':
						$xml = simplexml_load_file(DIR_FS_EBAY_FEEDS . $file_name);
						if ($xml){
							foreach($xml->children() as $item){
								$sku = (string)$item->InventoryStatus->SKU;
								$ack = (string)$item->Ack;
								$message = (string)$item->Message;
								$error_messages = '';
								foreach($item->Errors as $error){
									$error_text = (string)$error->LongMessage;
									$severity_code = (string)$error->SeverityCode;
									if (!empty($error_text)){
										$error_messages .= "$severity_code: $error_text<br>";
									}
								}

								if (!empty($sku)){
									if (!empty($error_messages) || !empty($message)){/*
										tep_db_query("insert ignore into ebay_product_feed_errors (sku, job_type, environment, ack, error_messages, message, date_added) values ('" . tep_db_prepare_input($sku) . "', '" . tep_db_prepare_input($entry['job_type']) . "', '" . tep_db_prepare_input(EBAY_ENVIRONMENT) . "', '" . tep_db_prepare_input($ack) . "', " . (empty($error_messages) ? "null" : "'" .tep_db_prepare_input($error_messages) . "'")  . ", " . (empty($message) ? "null" : "'" .tep_db_prepare_input($message) . "'") . ", now())");*/
									} else {
										tep_db_query("delete from ebay_product_feed_errors where sku='" . tep_db_prepare_input($sku) . "' and job_type='" . tep_db_prepare_input($entry['job_type']) . "' and environment='" . tep_db_prepare_input(EBAY_ENVIRONMENT) . "'");
									}
								}
							}
						}
						break;
					case 'OrderAck':
					case 'SetShipmentTrackingInfo':
						$xml = simplexml_load_file(DIR_FS_EBAY_FEEDS . $file_name);
						if ($xml){
							foreach($xml->children() as $order){
								$order_line_item_id = (string)$order->OrderLineItemID;
								$ack = (string)$order->Ack;
								$error_messages = '';
								foreach($order->Errors as $error){
									$error_text = (string)$error->LongMessage;
									$severity_code = (string)$error->SeverityCode;
									if (!empty($error_text)){
										$error_messages .= "$severity_code: $error_text<br>";
									}
								}
								
								if (!empty($order_line_item_id)){
									if (!empty($severity_code) && !empty($message)){
										tep_db_query("insert ignore into ebay_order_feed_errors (order_line_item_id, job_type, environment, ack, error_messages, date_added) values ('" . tep_db_prepare_input($order_line_item_id) . "', '" . tep_db_prepare_input($entry['job_type']) . "', '" . tep_db_prepare_input(EBAY_ENVIRONMENT) . "', '" . tep_db_prepare_input($ack) . "', '" . tep_db_prepare_input($error_messages) . "', now())");
									} else {
										tep_db_query("delete from ebay_order_feed_errors where order_line_item_id='" . tep_db_prepare_input($order_line_item_id) . "' and job_type='" . tep_db_prepare_input($entry['job_type']) . "' and environment='" . tep_db_prepare_input(EBAY_ENVIRONMENT) . "'");
									}
								}
							}
						}
						break;
				}
		}
    }
}

?>