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

$debug_mode = false;
if ($debug_mode){
    echo 'Start: ' . date('c') . "\n";
}

 $job_ids = array();
 
 $download = false;
	
 if(isset($_GET['job_id']) && !empty($_GET['job_id'])){
    $jobs_array[] = $_GET['job_id'];
    $download = true;
 }else{
    $sql = tep_db_query("select id, job_id from ebay_jobs where (status_processing is null or status_processing not in ('Failure', 'Aborted', 'Completed', 'Failed', 'custom:Skip', 'custom:XML_error') ) and status_validation='Success' and environment='" . EBAY_ENVIRONMENT . "' order by  id");
	if (tep_db_num_rows($sql)){
		while ($entry = tep_db_fetch_array($sql)){
            $jobs_array[] = $entry['job_id'];
        }
    }    
}
 
 
foreach($jobs_array as $job_id){
    $session = new LargeMerchantServiceSession('XML','XML', EBAY_ENVIRONMENT);   
    $request = createGetJobStatusRequest($job_id);
    $response = $session->sendBulkDataExchangeRequest('getJobStatus', $request);
    $xml = simplexml_load_string($response);
    if ($xml->jobProfile->jobStatus == 'Completed') {
    
    	$sql_data = array(
		  'last_modified'                 => 'now()',
          'status_processing'             => (string)$xml->jobProfile->jobStatus,
          'response_file_reference_id'    => (string)$xml->jobProfile->fileReferenceId,
          'response_xml'                  => $xml->asXML()
        );
        
        $file_reference_id = $xml->jobProfile->fileReferenceId;
        
        $session = new LargeMerchantServiceSession('XML', 'XML', EBAY_ENVIRONMENT);
    
        $request = createDownloadRequest($job_id, $file_reference_id);
    
        $response = $session->sendFileTransferServiceDownloadRequest($request);
    
        $responseXML = parseForResponseXML($response);
    
        $responseDOM = DOMUtils::createDOM($responseXML);
    
        $ack = strtolower($responseDOM->getElementsByTagName('ack'));
        if ($ack == 'failure') {
            PrintUtils::printDOM($responseDOM);
            die;
        }
        $uuid = parseForXopIncludeUUID($responseDOM);
    
        $fileBytes = parseForFileBytes($uuid, $response);
    
        writeZipFile($fileBytes, 'ebay_response/' . $file_reference_id . '.zip');
    	
    	$archive_file_name = 'ebay_response/' . $file_reference_id . '.zip';
        
     if ($xml->jobProfile->jobType == 'AddFixedPriceItem') {	
    	parseEbayResponseFeed($file_reference_id);
	   }
        if($download){
            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename=$archive_file_name");
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile("$archive_file_name");
            exit;    
        }
    
    } else {
        $sql_data['status_processing'] = 'Custom:XML_error';
        echo 'The feed is not yet processed by ebay';
    }
    tep_db_perform('ebay_jobs', $sql_data, 'update', "job_id='" . (int)$job_id . "'");
    PrintUtils::printXML($response);
}
 
function createGetJobStatusRequest($jobId){
  $request  = '<getJobStatusRequest xmlns:sct="http://www.ebay.com/soaframework/common/types" xmlns="http://www.ebay.com/marketplace/services">';
  $request .= '<jobId>' . $jobId . '</jobId>';
  $request .= '</getJobStatusRequest>';

  return $request;
}

function createDownloadRequest($taskReferenceId, $fileReferenceId)
{
    $request = '<downloadFileRequest xmlns:sct="http://www.ebay.com/soaframework/common/types" xmlns="http://www.ebay.com/marketplace/services">';
    $request .= '<taskReferenceId>' . $taskReferenceId . '</taskReferenceId>';
    $request .= '<fileReferenceId>' . $fileReferenceId . '</fileReferenceId>';
    $request .= '</downloadFileRequest>';

    return $request;
}

/**
 * Parses for the Error Message in the MIME multipart message.
 * @param $response MIME multipart message
 * @return string XML Error Message Response
 */
function parseForErrorMessage($response)
{
    $beginErrorMessage = strpos($response, '<?xml');
    $endErrorMessage = strpos($response, '</errorMessage>', $beginErrorMessage);
    $endErrorMessage += strlen('</errorMessage>');

    return substr($response, $beginErrorMessage, $endErrorMessage - $beginErrorMessage);
}

/**
 * Parses for the XML Response in the MIME multipart message.
 * @param string $response MIME multipart message
 * @return string XML Response
 */
function parseForResponseXML($response)
{
    $beginResponseXML = strpos($response, '<?xml');

    $endResponseXML = strpos($response, '</downloadFileResponse>', $beginResponseXML);

    //Assume a service level error and die.
    if ($endResponseXML === false)
    {
        $errorXML = parseForErrorMessage($response);
        PrintUtils::printXML($errorXML);
        die();
    }

    $endResponseXML += strlen('</downloadFileResponse>');

    return substr($response, $beginResponseXML, $endResponseXML - $beginResponseXML);
}

/**
 * Parses for the file bytes between the MIME boundaries.
 * @param $uuid UUID corresponding to the Content-ID of the file bytes.
 * @param string $response MIME multipart message
 * @return string bytes of the file
 */
function parseForFileBytes($uuid, $response)
{
    $contentId = 'Content-ID: <' . $uuid . '>';

    $mimeBoundaryPart = strpos($response, '--MIMEBoundaryurn_uuid_');

    $beginFile = strpos($response, $contentId, $mimeBoundaryPart);
    $beginFile += strlen($contentId);

    //Accounts for the standard 2 CRLFs.
    $beginFile += 4;

    $endFile = strpos($response, '--MIMEBoundaryurn_uuid_', $beginFile);

    //Accounts for the standard 1 CRLFs.
    $endFile -= 2;

    $fileBytes = substr($response, $beginFile, $endFile - $beginFile);

    return $fileBytes;
}

/**
 * Parses the XML Response for the UUID to ascertain the
 * index of the file bytes in the MIME Message.
 * @param DomDocument $responseDOM DOM of the XML Response.
 * @return string UUID referring to the message body
 */
function parseForXopIncludeUUID($responseDOM)
{
    $xopInclude = $responseDOM->getElementsByTagName('Include')->item(0);
    $uuid = $xopInclude->getAttributeNode('href')->nodeValue;
    $uuid = substr($uuid, strpos($uuid, 'urn:uuid:'));

    return $uuid;
}

/**
 * Writes the response file's bytes to disk.
 * @param string $bytes bytes comprising a file
 * @param string $zipFilename name of the zip to be created
 */
function writeZipFile($bytes, $zipFilename)
{
  //  echo "<p><b>Writing File to $zipFilename : ";

    $handler = fopen($zipFilename, 'wb') or die("Failed. Cannot Open $zipFilename to Write!</b></p>");
    fwrite($handler, $bytes);
    fclose($handler);

   // echo 'Success.</b></p>';
}

function parseEbayResponseFeed($feed_file){
	$zip = new ZipArchive;
	$res = $zip->open("ebay_response/".$feed_file);
	if ($res === TRUE) {
		$zip->extractTo("ebay_response/");
		$zip->close();

		$files = glob("ebay_response/*xml");
	
		if (is_array($files)) {
			foreach($files as $filename) {
				$xml_file = file_get_contents($filename, FILE_TEXT);
				$xml = simplexml_load_string($xml_file);	
				foreach($xml->AddFixedPriceItemResponse as $items){
					if((string)$items->Ack == 'Warning' || (string)$items->Ack == 'Success'){
						tep_db_query("update products set item_listed_on_ebay='1' where products_model = '".$items->SKU."'");
					}
				}
			}
			@unlink("ebay_response/".$filename);
		}
	}
}

if ($debug_mode){
    echo 'End: ' . date('c') . "\n";
}
?>