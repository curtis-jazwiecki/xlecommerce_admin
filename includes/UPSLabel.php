<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
function getDigestUPSLabel($params,$is_ajax_call = 0){
    /*
    parameters:
    user_name, password, license_number, 
    from_company_name, from_address1, from_address2 from_city, from_state_code, from_zip, tax_id_type_code
    to_company_name, to_name, to_address1, to_address2, to_city, to_state_code, to_zip, 
    service_code, service_desc, 
    package_type_code, package_type_desc, package_desc
    */
	 
	$xmlRequest1='<?xml version="1.0"?>
		<AccessRequest xml:lang="en-US">
		<AccessLicenseNumber>'.$params['license_number'].'</AccessLicenseNumber>
		<UserId>'.$params['user_name'].'</UserId>
		<Password>'.$params['password'].'</Password>
		</AccessRequest>
		<?xml version="1.0"?>
		<ShipmentConfirmRequest xml:lang="en-US">
		<Request>
			<TransactionReference>
			<CustomerContext>Customer Comment</CustomerContext>
			<XpciVersion/>
			</TransactionReference>
			<RequestAction>ShipConfirm</RequestAction>
			<RequestOption>validate</RequestOption>
		</Request>
		<LabelSpecification>
			<LabelPrintMethod>
				<Code>GIF</Code>
				<Description>gif file</Description>
			</LabelPrintMethod>
		<HTTPUserAgent>'.$_SERVER['HTTP_USER_AGENT'].'</HTTPUserAgent>
			<LabelImageFormat>
				<Code>GIF</Code>
				<Description>gif</Description>
			</LabelImageFormat>
		</LabelSpecification>
		<Shipment>
			<RateInformation>
				<NegotiatedRatesIndicator/>
			</RateInformation>
		<Description/>
		<Shipper>
			<Name>'.$params['from_company_name'].'</Name>
			<ShipperNumber>'.$params['shipper_number'].'</ShipperNumber>
			<Address>
				<AddressLine1>'.$params['from_address1'].'</AddressLine1>
				<City>'.$params['from_city'].'</City>
				<StateProvinceCode>' . $params['from_state_code'] . '</StateProvinceCode>
				<PostalCode>'.$params['from_zip'].'</PostalCode>
				<PostcodeExtendedLow></PostcodeExtendedLow>
				<CountryCode>US</CountryCode>
			</Address>
		</Shipper>
		<ShipTo>
			<CompanyName>'.(empty($params['to_company_name']) ? 'N/A':$params['to_company_name']).'</CompanyName>
			<AttentionName>'.$params['to_name'].'</AttentionName>
			<PhoneNumber>'.$params['to_telephone'].'</PhoneNumber>
			<Address>
				<AddressLine1>'.$params['to_address1'].'</AddressLine1>
				<City>'.$params['to_city'].'</City>
				<StateProvinceCode>'.$params['to_state_code'].'</StateProvinceCode>
				<PostalCode>'.$params['to_zip'].'</PostalCode>
				<CountryCode>US</CountryCode>
			</Address>
		</ShipTo>
		<ShipFrom>
			<CompanyName>'.$params['from_company_name'].'</CompanyName>
			<AttentionName>'.$params['from_name'].'</AttentionName>
			<PhoneNumber>1234567890</PhoneNumber>
			<TaxIdentificationNumber>'.$params['tax_id_type_code'].'</TaxIdentificationNumber>
			<Address>
				<AddressLine1>'.$params['from_address1'].'</AddressLine1>
				<City>'.$params['from_city'].'</City>
				<StateProvinceCode>'.$params['from_state_code'].'</StateProvinceCode>
				<PostalCode>'.$params['from_zip'].'</PostalCode>
				<CountryCode>US</CountryCode>
			</Address>
		</ShipFrom>
		<PaymentInformation>
			<Prepaid>
			<BillShipper>
			<AccountNumber>'.$params['shipper_number'].'</AccountNumber>
			</BillShipper>
		</Prepaid>
		</PaymentInformation>
		<Service>
			<Code>'.$params['service_code'].'</Code>
			<Description>'.$params['service_desc'].'</Description>
		</Service>
		<Package>
			<PackagingType>
				<Code>02</Code>
				<Description>'.$params['package_type_desc'].'</Description>
			</PackagingType>
			<Description>' . $params['package_desc'] . '</Description>
			<ReferenceNumber>
				<Code>00</Code>
				<Value>Package</Value>
			</ReferenceNumber>
			<PackageWeight>
				<UnitOfMeasurement/>
				<Weight>' . $params['weight'] . '</Weight>
			</PackageWeight>
			<LargePackageIndicator/>
			<AdditionalHandling>0</AdditionalHandling>
		</Package>
		</Shipment>
		</ShipmentConfirmRequest>';
		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://wwwcie.ups.com/ups.app/xml/ShipConfirm");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3600);

		$xmlResponse = curl_exec ($ch); // SHIP CONFORMATION RESPONSE
		$xml = $xmlResponse;
		$return_element = new SimpleXMLElement($xml);
		if($return_element->Response->ResponseStatusDescription == 'Failure'){
			if($is_ajax_call == 1){
				echo -1;
				return;
			}else{
				echo "Error Occurred: <font color='#FF0000'>".$return_element->Response->Error->ErrorDescription."</font>";
				exit;
			}
		}
		preg_match_all( "/\<ShipmentConfirmResponse\>(.*?)\<\/ShipmentConfirmResponse\>/s",$xml, $bookblocks );
		foreach( $bookblocks[1] as $block ){
			preg_match_all( "/\<ShipmentDigest\>(.*?)\<\/ShipmentDigest\>/",$block, $author ); // SHIPPING DIGEST
		}
		saveLabel($params['license_number'],$params['user_name'],$params['password'],$author[1][0],$params['order_id'],$params['vendor_id']);
}

function saveLabel($license_number,$user_name,$password,$shipmentdigest,$order_id,$vendor_id){
   // SHIP ACCEPT REQUEST
	$xmlRequest1.='<?xml version="1.0" encoding="ISO-8859-1"?>
	<AccessRequest>
	<AccessLicenseNumber>'.$license_number.'</AccessLicenseNumber>
	<UserId>'.$user_name.'</UserId>
	<Password>'.$password.'</Password>
	</AccessRequest>
	<?xml version="1.0" encoding="ISO-8859-1"?>
	<ShipmentAcceptRequest>
	<Request>
	<TransactionReference>
	<CustomerContext>Customer Comment</CustomerContext>
	</TransactionReference>
	<RequestAction>ShipAccept</RequestAction>
	<RequestOption>1</RequestOption>
	</Request>
	<ShipmentDigest>'.$shipmentdigest.'</ShipmentDigest>
	</ShipmentAcceptRequest>';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://wwwcie.ups.com/ups.app/xml/ShipAccept");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3600);

	$xmlResponse = curl_exec ($ch); // SHIP ACCEPT RESPONSE
	$xml = $xmlResponse;
	
	$data_string = new SimpleXMLElement($xml);
	$ups_tracking_number = $data_string->ShipmentResults->PackageResults->TrackingNumber;
	tep_db_query("update orders_shipping set ups_track_num = '".$ups_tracking_number."' where orders_id = '".$order_id."' and 	vendors_id = '".$vendor_id."'");
	
	$ups_label_image = $data_string->ShipmentResults->PackageResults->LabelImage->GraphicImage;
	
	$filename = DIR_FS_ADMIN.'shipping_labels/'.$order_id.'_'.$vendor_id.'_ups_label.gif'; //this is the original file
	$file = fopen($filename,"w");
	fwrite($file,base64_decode((string)$ups_label_image));
	$degrees = -90; //change this to be whatever degree of rotation you want 
	$source = imagecreatefromgif($filename) or notfound(); 
	$rotate = imagerotate($source,$degrees,0); 
	imagegif($rotate,$filename); //save the new image 
	imagedestroy($source); //free up the memory 
	imagedestroy($rotate); //free up the memory
	fclose($file);
	echo '<img src="'.DIR_WS_ADMIN.'shipping_labels/'.$order_id.'_'.$vendor_id.'_ups_label.gif" id="'.$ups_tracking_number.'" />';
}

function convertStateTo($name, $to='abbrev') {
	
    $states = array(
        array('name'=>'Alabama',            'abbrev'=>'AL'),
        array('name'=>'Alaska',             'abbrev'=>'AK'),
        array('name'=>'Arizona',            'abbrev'=>'AZ'),
        array('name'=>'Arkansas',           'abbrev'=>'AR'),
        array('name'=>'California',         'abbrev'=>'CA'),
        array('name'=>'Colorado',           'abbrev'=>'CO'),
        array('name'=>'Connecticut',        'abbrev'=>'CT'),
        array('name'=>'Delaware',           'abbrev'=>'DE'),
        array('name'=>'Florida',            'abbrev'=>'FL'),
        array('name'=>'Georgia',            'abbrev'=>'GA'),
        array('name'=>'Hawaii',             'abbrev'=>'HI'),
        array('name'=>'Idaho',              'abbrev'=>'ID'),
        array('name'=>'Illinois',           'abbrev'=>'IL'),
        array('name'=>'Indiana',            'abbrev'=>'IN'),
        array('name'=>'Iowa',               'abbrev'=>'IA'),
        array('name'=>'Kansas',             'abbrev'=>'KS'),
        array('name'=>'Kentucky',           'abbrev'=>'KY'),
        array('name'=>'Louisiana',          'abbrev'=>'LA'),
        array('name'=>'Maine',              'abbrev'=>'ME'),
        array('name'=>'Maryland',           'abbrev'=>'MD'),
        array('name'=>'Massachusetts',      'abbrev'=>'MA'),
        array('name'=>'Michigan',           'abbrev'=>'MI'),
        array('name'=>'Minnesota',          'abbrev'=>'MN'),
        array('name'=>'Mississippi',        'abbrev'=>'MS'),
        array('name'=>'Missouri',           'abbrev'=>'MO'),
        array('name'=>'Montana',            'abbrev'=>'MT'),
        array('name'=>'Nebraska',           'abbrev'=>'NE'),
        array('name'=>'Nevada',             'abbrev'=>'NV'),
        array('name'=>'New Hampshire',      'abbrev'=>'NH'),
        array('name'=>'New Jersey',         'abbrev'=>'NJ'),
        array('name'=>'New Mexico',         'abbrev'=>'NM'),
        array('name'=>'New York',           'abbrev'=>'NY'),
        array('name'=>'North Carolina',     'abbrev'=>'NC'),
        array('name'=>'North Dakota',       'abbrev'=>'ND'),
        array('name'=>'Ohio',               'abbrev'=>'OH'),
        array('name'=>'Oklahoma',           'abbrev'=>'OK'),
        array('name'=>'Oregon',             'abbrev'=>'OR'),
        array('name'=>'Pennsylvania',       'abbrev'=>'PA'),
        array('name'=>'Rhode Island',       'abbrev'=>'RI'),
        array('name'=>'South Carolina',     'abbrev'=>'SC'),
        array('name'=>'South Dakota',       'abbrev'=>'SD'),
        array('name'=>'Tennessee',          'abbrev'=>'TN'),
        array('name'=>'Texas',              'abbrev'=>'TX'),
        array('name'=>'Utah',               'abbrev'=>'UT'),
        array('name'=>'Vermont',            'abbrev'=>'VT'),
        array('name'=>'Virginia',           'abbrev'=>'VA'),
        array('name'=>'Washington',         'abbrev'=>'WA'),
        array('name'=>'West Virginia',      'abbrev'=>'WV'),
        array('name'=>'Wisconsin',          'abbrev'=>'WI'),
        array('name'=>'Wyoming',            'abbrev'=>'WY'),
        array('name'=>'Armed Forces Europe','abbrev'=>'AE')
    );

	$return = false;
	foreach ($states as $state) {
        if ($to == 'name') {
            if (strtolower($state['abbrev']) == strtolower($name)){
                $return = $state['name'];
                break;
            }
        } elseif ($to == 'abbrev') {
            if (strtolower($state['name']) == strtolower($name)){
                $return = strtoupper($state['abbrev']);
                break;
			}
		}
	}
	return $return;
}