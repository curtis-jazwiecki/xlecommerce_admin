<?php
require('includes/configure.php');
require(DIR_WS_INCLUDES . 'database_tables.php');
require(DIR_WS_FUNCTIONS . 'database.php');
tep_db_connect() or die('Unable to connect to database server!');
  $configuration_query = tep_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION);
  while ($configuration = tep_db_fetch_array($configuration_query)) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
  }
require(DIR_WS_FUNCTIONS . 'general.php');  
require(DIR_WS_FUNCTIONS . 'sessions.php');

// alavara code #starts
$oldPath = getcwd();
chdir('../');
require_once DIR_WS_MODULES . 'avatax/ava_tax.php';
require_once DIR_WS_MODULES . 'avatax/credentials.php'; 

if( (isset($_GET['mode'])) && ($_GET['mode'] == 'validateConnection') ){
	 
	$json = array();
	 
	try {
		 
		$svc = new TaxServiceSoap(MODULE_ORDER_TOTAL_AVATAX_DEV_STATUS);

		$result = $svc->ping();
		
		if ($result->getResultCode() == SeverityLevel::$Success){
			$json['success'] = 1;	
		}else{
			$json['error'] = 1;
		}
		 
	 }catch (SoapFault $exception) {
	 	$json['error'] = 1;
	 }
	 
	 // add to log table
	updateAvataxLogTable($svc->__getLastRequest(),$svc->__getLastResponse(),'connection_test');
	 
	 
	 
	 echo json_encode($json);
	 
	 exit;
	
}

if( (isset($_GET['mode'])) && ($_GET['mode'] == 'validateAddressDetails') ){
	 
	 $json = array();
	 
	 try {
		 
		$port = new AddressServiceSoap(MODULE_ORDER_TOTAL_AVATAX_DEV_STATUS);
		$address = new Address();
		$address->setLine1($_POST['entry_street_address_original']);
		$address->setLine2($_POST['entry_suburb_original']);
		$address->setCity($_POST['entry_city_original']);
		$address->setRegion($_POST['entry_state_original']);
		$address->setPostalCode($_POST['entry_postcode_original']);
		$result = $port->validate(new ValidateRequest($address,TextCase::$Upper));
		
		if ($result->getResultCode() == SeverityLevel::$Success){
			$addresses = $result->getValidAddresses();
			if (sizeof($addresses) > 0){
				$validAddress = $addresses[0];
				$json['success'] = 1;
				$json['entry_street_address_original'] = $validAddress->getLine1();
				$json['entry_suburb_original'] = $validAddress->getLine2();
				$json['entry_city_original'] = $validAddress->getCity();
				$json['entry_state_original'] = $validAddress->getRegion();
				$json['entry_postcode_original'] = $validAddress->getPostalCode();
				$json['entry_country_id_original'] = $validAddress->getCountry();
			}else{
				$json['error'] = 1;
			}
		}else{
			$json['error'] = 1;
		}
		 
		 
	 }catch (SoapFault $exception) {
	 	$json['error'] = 1;
	 }
	 
	// add to log table
	updateAvataxLogTable($port->__getLastRequest(),$port->__getLastResponse(),'address_test');
	 
	 echo json_encode($json);
	 
	 exit;
}


$oID = (int)$_POST['oID'];

$data = tep_db_fetch_array(tep_db_query("select avalara_data from ".TABLE_ORDERS." where orders_id = '".$oID."'"));

$alavara_data = unserialize($data['avalara_data']);

if( (isset($_POST['mode'])) && ($_POST['mode'] == 'cancel') ){
	try {
		$client3 = new TaxServiceSoap(MODULE_ORDER_TOTAL_AVATAX_DEV_STATUS);
		$cancel_tax_request = new CancelTaxRequest();
		$cancel_tax_request->setDocType($alavara_data['doc_type']); 
		$cancel_tax_request->setDocCode($alavara_data['doc_code']);
		$cancel_tax_request->setCompanyCode(MODULE_ORDER_TOTAL_AVATAX_CODE);
		$cancel_tax_request->setCancelCode('DocVoided');
		$cancel_tax_response = $client3->cancelTax($cancel_tax_request);
		if ($cancel_tax_response->getResultCode() == SeverityLevel::$Success) {
			tep_db_query("update ".TABLE_ORDERS." set avalara_tax_commited = '-1' where orders_id = '".$oID."'");
			echo "1";
		}else{
			echo "-1";   
		}
		
	} catch (SoapFault $exception) {
	    echo "-1";
        /*$msg = 'SOAP Exception: ';

        if ($exception) {
            $msg .= $exception->faultstring;
        }

        echo 'AvaTax message is: ' . $msg;
        echo "<br>";
        echo 'AvaTax last request is: ' . $client3->__getLastRequest();
        echo "<br>";
        echo 'AvaTax last response is: ' . $client3->__getLastResponse();*/
	}
	
	// add to log table
	updateAvataxLogTable($client3->__getLastRequest(),$client3->__getLastResponse(),'cancel_tax');

}else if( (isset($_POST['mode'])) && ($_POST['mode'] == 'commit') ){
	try {
		$client3 = new TaxServiceSoap(MODULE_ORDER_TOTAL_AVATAX_DEV_STATUS);
		$post_tax_request = new PostTaxRequest();
		$post_tax_request->setDocType($alavara_data['doc_type']); 
		$post_tax_request->setDocCode($alavara_data['doc_code']);
		$post_tax_request->setCompanyCode(MODULE_ORDER_TOTAL_AVATAX_CODE);
		$post_tax_request->setDocDate($alavara_data['doc_date']);
		$post_tax_request->setTotalAmount($alavara_data['doc_amount']);
		$post_tax_request->setTotalTax($alavara_data['doc_total_tax']);
		$post_tax_request->setHashCode($alavara_data['doc_hash_code']);
		$post_tax_request->setCommit(true);
		
		// update doc code with our system generated order id #start
		//$post_tax_request->setNewDocCode("OBN-".$oID);
		// update doc code with our system generated order id #ends
		
		$post_tax_response = $client3->postTax($post_tax_request);
		
		if ($post_tax_response->getResultCode() == SeverityLevel::$Success) {
			tep_db_query("update ".TABLE_ORDERS." set avalara_tax_commited = '1' where orders_id = '".$oID."'");
			echo "1";
		}else{
			echo "-1";   
		}
		
	} catch (SoapFault $exception) {
	    echo "-1";
        /*$msg = 'SOAP Exception: ';

        if ($exception) {
            $msg .= $exception->faultstring;
        }

        echo 'AvaTax message is: ' . $msg;
        echo "<br>";
        echo 'AvaTax last request is: ' . $client3->__getLastRequest();
        echo "<br>";
        echo 'AvaTax last response is: ' . $client3->__getLastResponse();*/
	}
		// add to log table
	updateAvataxLogTable($client3->__getLastRequest(),$client3->__getLastResponse(),'commit_tax');

}
require(DIR_WS_INCLUDES.'application_bottom.php');
chdir($oldPath);
?>