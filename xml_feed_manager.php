<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
	require('includes/application_top_xml.php');
	require('includes/classes/xml_feed_handler.php');
	/* 26SEP2008
	echo '<style>' .
			'td {font-family:verdana; font-size:10px;}' .
		 '</style>';
	*/
	
	/*$xml_feed = new xml_feed_reader();
	echo '<table cellspacing="0" cellpadding="2" border="1" width="100%">' .
		 	'<tr>' .
		 		'<td>' . KEY_CATALOG_VERSION . '</td>' .
		 		'<td width="*">&nbsp;' . $xml_feed->feed_data[KEY_CATALOG_VERSION] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_DOCUMENT_ID . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_DOCUMENT_ID] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_VENDOR_ID . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_VENDOR_ID] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_VENDOR_NAME . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_VENDOR_NAME] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_VENDOR_NUMBER . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_VENDOR_NUMBER] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_RETAILER_ID . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_RETAILER_ID] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_RETAILER_NAME . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_RETAILER_NAME] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_RETAILER_CUSTOMER_NUMBER . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_RETAILER_CUSTOMER_NUMBER] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_GENERATION_DATE . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_GENERATION_DATE] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_CATALOG_ID . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_CATALOG_ID] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_CATALOG_DESCRIPTION . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_CATALOG_DESCRIPTION] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_EFFECTIVE_DATE . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_EFFECTIVE_DATE] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_END_DATE . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_END_DATE] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_AVAILABLE_TO_SHIP_DATE . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_AVAILABLE_TO_SHIP_DATE] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_LAST_AVAILABLE_TO_SHIP_DATE. '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_LAST_AVAILABLE_TO_SHIP_DATE] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_PUBLICATION_DATE . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_PUBLICATION_DATE] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_CONTRACT_REFERENCE . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_CONTRACT_REFERENCE] . '</td>' .
		 	'</tr>' .
		 	'<tr>' .
		 		'<td>' . KEY_CONTRACT_DATE . '</td>' .
		 		'<td>&nbsp;' . $xml_feed->feed_data[KEY_CONTRACT_DATE] . '</td>' .
		 	'</tr>' .
		 '</table>';
	$count = 0;
	echo '<table cellspacing="0" cellpadding="2" border="1" width="100%">';
	foreach($xml_feed->products as $key=>$value){
		$count++;
		echo '<tr>' .
			'<td valign="top">&nbsp;<nobr>Product No ' .$count  . '<nobr></td>' .
			'<td>' .
				'<table cellspacing="0" cellpadding="2" border="0">' .
					'<tr>' .
						'<td>' . KEY_PRODUCT_DESCRIPTION_SHORT . '</td>' .
						'<td>&nbsp;' . $value[KEY_PRODUCT_DESCRIPTION_SHORT] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_UPC_EAN . '</td>' .
						'<td>&nbsp;' . $value[KEY_UPC_EAN] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_UNIT_COST . '</td>' .
						'<td>&nbsp;' . $value[KEY_UNIT_COST] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_UNIT_COST . '</td>' .
						'<td>&nbsp;' . $value[KEY_UNIT_COST] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_UNIT_COST_CURRENCY . '</td>' .
						'<td>&nbsp;' . $value[KEY_UNIT_COST_CURRENCY] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_UNIT_MSRP . '</td>' .
						'<td>&nbsp;' . $value[KEY_UNIT_MSRP] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_UNIT_MSRP_CURRENCY . '</td>' .
						'<td>&nbsp;' . $value[KEY_UNIT_MSRP_CURRENCY] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_VENDOR_STYLE . '</td>' .
						'<td>&nbsp;' . $value[KEY_VENDOR_STYLE] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_VENDOR_SKU . '</td>' .
						'<td>&nbsp;' . $value[KEY_VENDOR_SKU] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_VENDOR_COLOR . '</td>' .
						'<td>&nbsp;' . $value[KEY_VENDOR_COLOR] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_VENDOR_SIZE. '</td>' .
						'<td>&nbsp;' . $value[KEY_VENDOR_SIZE] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_FULFILLER . '</td>' .
						'<td>&nbsp;' . $value[KEY_FULFILLER] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_PRODUCT_NAME . '</td>' .
						'<td>&nbsp;' . $value[KEY_PRODUCT_NAME] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_PRODUCT_ID . '</td>' .
						'<td>&nbsp;' . $value[KEY_PRODUCT_ID] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_MINIMUM_ACCEPTABLE_PRICE . '</td>' .
						'<td>&nbsp;' . $value[KEY_MINIMUM_ACCEPTABLE_PRICE] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_BUYER_SKU . '</td>' .
						'<td>&nbsp;' . $value[KEY_BUYER_SKU] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_BUYER_COLOR . '</td>' .
						'<td>&nbsp;' . $value[KEY_BUYER_COLOR] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_BUYER_SIZE . '</td>' .
						'<td>&nbsp;' . $value[KEY_BUYER_SIZE] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_CATEGORY . '</td>' .
						'<td>&nbsp;' . $value[KEY_CATEGORY] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_PACKAGED_WEIGHT . '</td>' .
						'<td>&nbsp;' . $value[KEY_PACKAGED_WEIGHT] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_PACKAGED_WEIGHT_UNIT . '</td>' .
						'<td>&nbsp;' . $value[KEY_PACKAGED_WEIGHT_UNIT] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_PRODUCT_WEIGHT . '</td>' .
						'<td>&nbsp;' . $value[KEY_WEIGHT] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_PRODUCT_WEIGHT_UNIT . '</td>' .
						'<td>&nbsp;' . $value[KEY_PRODUCT_WEIGHT_UNIT] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_EFFECTIVE_DATE . '</td>' .
						'<td>&nbsp;' . $value[KEY_EFFECTIVE_DATE] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_END_DATE . '</td>' .
						'<td>&nbsp;' . $value[KEY_END_DATE] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_AVAILABLE_TO_SHIP_DATE . '</td>' .
						'<td>&nbsp;' . $value[KEY_AVAILABLE_TO_SHIP_DATE] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_TRADE_NAME . '</td>' .
						'<td>&nbsp;' . $value[KEY_TRADE_NAME] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_BRAND_NAME . '</td>' .
						'<td>&nbsp;' . $value[KEY_BRAND_NAME] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_COUNTRY_OF_ORIGIN . '</td>' .
						'<td>&nbsp;' . $value[KEY_COUNTRY_OF_ORIGIN] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_TARIFF_CODE . '</td>' .
						'<td>&nbsp;' . $value[KEY_TARIFF_CODE] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_MINIMUM_PURCHASE_QUANTITY . '</td>' .
						'<td>&nbsp;' . $value[KEY_MINIMUM_PURCHASE_QUANTITY] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_PURCHASING_QUANTITY . '</td>' .
						'<td>&nbsp;' . $value[KEY_PURCHASING_QUANTITY] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_PURCHASING_UOM . '</td>' .
						'<td>&nbsp;' . $value[KEY_PURCHASING_UOM] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_PURCHASING_UPC_EAN . '</td>' .
						'<td>&nbsp;' . $value[KEY_PURCHASING_UPC_EAN] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_SELLING_QUANTITY . '</td>' .
						'<td>&nbsp;' . $value[KEY_SELLING_QUANTITY] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_SELLING_UOM . '</td>' .
						'<td>&nbsp;' . $value[KEY_SELLING_UOM] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_SELLING_UPC_EAN . '</td>' .
						'<td>&nbsp;' . $value[KEY_SELLING_UPC_EAN] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td valign="top">' . KEY_PRODUCT_DESCRIPTION_LONG . '</td>' .
						'<td>&nbsp;' . $value[KEY_PRODUCT_DESCRIPTION_LONG] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_RGB_COLOR . '</td>' .
						'<td>&nbsp;' . $value[KEY_RGB_COLOR] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_PRODUCT_URI . '</td>' .
						'<td>&nbsp;' . $value[KEY_PRODUCT_URI] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . KEY_PRODUCT_PRINT_READY_IMAGE_URI . '</td>' .
						'<td>&nbsp;' . $value[KEY_PRODUCT_PRINT_READY_IMAGE_URI] . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td valign="top">' . KEY_FEATURES . '</td>';
						echo '<td>&nbsp;';
						if (is_array($value[KEY_FEATURES])){
							foreach($value[KEY_FEATURES] as $value_1){
								echo $value_1 . '<br>';
							}							
						}
						echo '</td>' . 
					'</tr>' .
					'<tr>' .
						'<td valign="top">' . KEY_SPEC_LIST . '</td>';
						echo '<td>&nbsp;';
						if (is_array($value[KEY_SPEC_LIST])){
							foreach($value[KEY_SPEC_LIST] as $key_1=>$value_1){
								echo $key_1 . ' - ' . $value_1. '<br>';
							}							
						}
						echo '</td>' . 
					'</tr>';
					echo '<tr>' .
						'<td>' . KEY_IMAGE_NAME . '</td>' .
						'<td>' . $value[KEY_IMAGE_NAME] . '</td>' .
					'</tr>';
					//echo '<tr>' .
					//	'<td valign="top">' . KEY_IMAGE_LIST . '</td>';
					//	echo '<td>&nbsp;';
					//	if (is_array($value[KEY_IMAGE_LIST])){
					//		foreach($value[KEY_IMAGE_LIST] as $key_1=>$value_1){
					//			echo $key_1 . ' - ' . $value_1. '<br>';
					//		}							
					//	}
					//	echo '</td>' . 
					//'</tr>';
					echo '<tr>' .
						'<td valign="top">' . KEY_XSELL_LIST . '</td>';
						echo '<td>&nbsp;';
						if (is_array($value[KEY_XSELL_LIST])){
							foreach($value[KEY_XSELL_LIST] as $value_1){
								echo $value_1. '<br>';
							}							
						}
						echo '</td>' . 
					'</tr>';
				echo '</table>' .
			'</td>' .
		'</tr>';
	}
	echo '</table>';*/
	//echo '<br><br>';
	//var_dump($xml_feed->products);
	/*$xml = new xml_feed_handler();
	$count = 0;	
	echo //'<tr>' .
			//'<td valign="top">Common values</td>' .
			//'<td>' .
				'<table border="0">' .
					'<tr>' .
						'<td>document_id</td>' .
						'<td>' . $xml->document_id . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>gen_date_timestamp</td>' .
						'<td>' . $xml->gen_date_timestamp . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>catalog_id</td>' .
						'<td>' . $xml->catalog_id . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>url_image_thumbs</td>' .
						'<td>' . $xml->url_image_thumbs . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>url_image_medium</td>' .
						'<td>' . $xml->url_image_medium . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>url_image_large</td>' .
						'<td>' . $xml->url_image_large . '</td>' .
					'</tr>' .
				'<table>';
			//'</td>' .
		//'</tr>';
	echo '<table cellspacing="0" cellpadding="2" border="1">';
	foreach($xml->products as $product){
		$xml->set_current_product($product);
		$count++;
		echo '<tr>' .
			'<td valign="top"><nobr>Product ' . $count . '<nobr></td>' .
				'<td valign="top">' .
					'<table cellspacing="0" cellpadding="2" border="0">';
						foreach($xml->product_info as $key=>$value){
							echo '<tr>' .
								'<td valign="top">' . $key . '</td>' .
								'<td>' . $value . '</td>' .
							'<tr>';
						}
						echo '<tr>' .
								'<td>Other Attributes</td>' .
								'<td>';
								if (count($xml->product_attributes)){
									foreach($xml->product_attributes as $key_1 =>$value_1){
										echo $key_1 . '=>' . $value_1 . ' || ';
									}
								} else{
									echo 'None';
								} 
								echo '</td>' .
							'</tr>';
						echo '<tr>' .
								'<td>Category levels</td>' .
								'<td>';
									foreach($xml->cat_path as $key_1 =>$value_1){
										echo $key_1 . '=>' . $value_1 . ' || ';
									}
						echo '</td></tr>';
						echo '<tr><td>category_id</td><td>' . $xml->category_id . '</td></tr>';
					echo '</table>' . 
			'</td>' .
		'</tr>';
	}
	echo '</table>';*/
	
	//define('TEXT_PROP', '');
	//define('TEXT_KEY', '');
	$xml = new xml_feed_handler();
	//$count = 0;
	/*echo '<table cellspacing="2" cellpadding="0" border="0">' .
			'<tr>' .
				'<td>'. TEXT_PROP . 'document_id</td>' .
				'<td>:</td>' .
				'<td width="*">' . $xml->document_id . '</td>' .
			'</tr>' .
			'<tr>' .
				'<td>'. TEXT_PROP . 'gen_date_timestamp</td>' .
				'<td>:</td>' .
				'<td>' . $xml->gen_date_timestamp . '</td>' .
			'</tr>' .
			'<tr>' .
				'<td>'. TEXT_PROP . 'catalog_id</td>' .
				'<td>:</td>' .
				'<td>' . $xml->catalog_id . '</td>' .
			'</tr>' .
			'<tr>' .
				'<td>'. TEXT_PROP . 'url_image_thumbs</td>' .
				'<td>:</td>' .
				'<td>' . $xml->url_image_thumbs . '</td>' .
			'</tr>' .
			'<tr>' .
				'<td>'. TEXT_PROP . 'url_image_medium</td>' .
				'<td>:</td>' .
				'<td>' . $xml->url_image_medium . '</td>' .
			'</tr>' .
			'<tr>' .
				'<td>'. TEXT_PROP . 'url_image_large</td>' .
				'<td>:</td>' .
				'<td>' . $xml->url_image_large . '</td>' .
			'</tr>' .
			'<tr>' .
				'<td>'. TEXT_PROP . 'xml_feed_id</td>' .
				'<td>:</td>' .
				'<td>' . $xml->xml_feed_id . '</td>' .
			'</tr>' .
			'</table>';*/
					foreach($xml->products as $product){
						$xml->set_current_product($product);
						/*$count++;
						echo '<table>' .
								'<tr>' .
									'<td valign="top">Product '. $count . '</td>' .
									'<td valign="top">:</td>' .
									'<td>' .
										'<table>' .
											'<tr>' .
												'<td>'. KEY_DESCRIPTION . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_DESCRIPTION] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_UPC_EAN . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_UPC_EAN] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_UNIT_COST . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_UNIT_COST] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_UNIT_COST_CURRENCY . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_UNIT_COST_CURRENCY] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_UNIT_MSRP . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_UNIT_MSRP] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_UNIT_MSRP_CURRENCY . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_UNIT_MSRP_CURRENCY] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_VENDOR_SKU . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_VENDOR_SKU] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_FULFILLER . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_FULFILLER] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_PRODUCT_NAME . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_PRODUCT_NAME] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_PRODUCT_ID . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_PRODUCT_ID] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_PRODUCT_WEIGHT . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_PRODUCT_WEIGHT] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_SALES_PRICE . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_SALES_PRICE] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_MINIMUM_ACCEPTABLE_PRICE . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_MINIMUM_ACCEPTABLE_PRICE] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_DESCRIPTION_NRA_NUMBER . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_DESCRIPTION_NRA_NUMBER] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_MODEL . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_MODEL] . '</td>' .
											'</tr>';
										echo '<tr>' .
												'<td>Other Attributes</td>' .
												'<td>:</td>' .
												'<td>';
												$temp = '';
												if (count($xml->product_attribs)){
													foreach($xml->product_attribs as $key =>$value){
														$temp .= $key . '=>' . $value . ' || '; 
													}
													$temp = substr($temp, 0, strlen($temp)-4); 
												} else{
													$temp = 'None';
												} 
												echo $temp . '</td>' .
											'</tr>';
									echo 	'<tr>' .
												'<td>'. KEY_CATEGORY_PATH . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_CATEGORY_PATH] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_BRAND_NAME . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_BRAND_NAME] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td>'. KEY_SELLING_QTY . '</td>' .
												'<td>:</td>' .
												'<td>'. $xml->product_info[KEY_SELLING_QTY] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td valign="top">'. KEY_DESCRIPTION_LONG . '</td>' .
												'<td valign="top">:</td>' .
												'<td>'. $xml->product_info[KEY_DESCRIPTION_LONG] . '</td>' .
											'</tr>' .
											'<tr>' .
												'<td valign="top">'. KEY_IMAGE_NAME . '</td>' .
												'<td valign="top">:</td>' .
												'<td>'. $xml->product_info[KEY_IMAGE_NAME] . '</td>' .
											'</tr>';
										echo '<tr>' .
												'<td>product_category_path</td>' .
												'<td>:</td>' .
												'<td>';
												$temp = '';
												foreach($xml->product_category_path as $key =>$value){
													$temp .= $key . '=>' . $value . ' || '; 
												}
												$temp = substr($temp, 0, strlen($temp)-4); 
												echo $temp . '</td>' .
											'</tr>';
										echo '<tr>' .
												'<td valign="top">category_id</td>' .
												'<td valign="top">:</td>' .
												'<td>'. $xml->product_category_id . '</td>' .
											'</tr>' . 
											'<tr>' .
												'<td valign="top">product_osc_id_exists</td>' .
												'<td valign="top">:</td>' .
												'<td>'. ($xml->product_osc_id_exists ? 'Yes' : 'No') . '</td>' .
											'</tr>' . 
											'<tr>' .
												'<td valign="top">product_osc_id</td>' .
												'<td valign="top">:</td>' .
												'<td>'. $xml->product_osc_id . '</td>' .
											'</tr>';
									echo '</table>' .
									'</td>' .
								'</tr>' .
							 '</table>';*/
							 /*if ($count==2){
								break;
							}*/
					}


	
	require(DIR_WS_INCLUDES . 'application_bottom.php');
?>