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

function createUploadFileRequest($taskReferenceId, $fileReferenceId, $fileSize){
  $request  = '<uploadFileRequest xmlns:sct="http://www.ebay.com/soaframework/common/types" xmlns="http://www.ebay.com/marketplace/services">';
  $request .= '<taskReferenceId>' . $taskReferenceId . '</taskReferenceId>';
  $request .= '<fileReferenceId>' . $fileReferenceId . '</fileReferenceId>';
  $request .= '<fileFormat>gzip</fileFormat>';
  $request .= '<fileAttachment>';
  $request .= '<Size>' . $fileSize . '</Size>';
  $request .= '<Data><xop:Include xmlns:xop="http://www.w3.org/2004/08/xop/include" href="cid:'
  . MultiPartMessage::$URN_UUID_ATTACHMENT . '" /></Data>';
  $request .= '</fileAttachment>';
  $request .= '</uploadFileRequest>';

  return $request;
}

function createAbortJobRequest($jobId){
  $request  = '<abortJobRequest xmlns:sct="http://www.ebay.com/soaframework/common/types" xmlns="http://www.ebay.com/marketplace/services">';
  $request .= '<jobId>' . $jobId . '</jobId>';
  $request .= '</abortJobRequest>';

  return $request;
}

function getFeedXml($job_type, &$items){
    $xml = null;
	if ($job_type=='AddFixedPriceItem' || $job_type=='ReviseFixedPriceItem' || $job_type=='RelistFixedPriceItem'){
		if ($job_type=='AddFixedPriceItem'){
			//$sql = tep_db_query("select * from products p inner join products_description pd on (p.products_id=pd.products_id and pd.language_id='1') inner join products_extended pe on p.products_id=pe.osc_products_id where p.products_status='1' and p.is_ebay_ok='1' and p.ebay_category_id>0 and item_listed_on_ebay='0'");
			$sql = tep_db_query("select * from products p inner join products_description pd on (p.products_id=pd.products_id and pd.language_id='1') left join products_extended pe on p.products_id=pe.osc_products_id left join ebay_product_feed_errors pfe on (p.products_model=pfe.sku and pfe.environment='" . EBAY_ENVIRONMENT . "') where (pfe.sku is null or pfe.ack!='failure') and p.products_status='1' and p.is_ebay_ok='1' and p.ebay_category_id>0  and p.products_quantity>='" . (int)EBAY_MIN_STOCK_QTY . "'");
    	} elseif ($job_type=='RelistFixedPriceItem' || $job_type=='ReviseFixedPriceItem'){
			//$sql = tep_db_query("select * from products p inner join products_description pd on (p.products_id=pd.products_id and pd.language_id='1') inner join products_extended pe on p.products_id=pe.osc_products_id where p.products_status='1' and p.is_ebay_ok='1' and p.ebay_category_id>0 and p.item_listed_on_ebay='1'");
			$sql = tep_db_query("select * from products p inner join products_description pd on (p.products_id=pd.products_id and pd.language_id='1') left join products_extended pe on p.products_id=pe.osc_products_id left join ebay_product_feed_errors pfe on (p.products_model=pfe.sku and pfe.environment='" . EBAY_ENVIRONMENT . "') where (pfe.sku is null or pfe.ack!='failure') and p.products_status='1' and p.is_ebay_ok='1' and p.ebay_category_id>0 and p.item_listed_on_ebay='1'");
		}
        
		if ($sql){
			if (tep_db_num_rows($sql)){
				$template_content = file_get_contents('ebay_template.html');
				$return_policy = 'If you are not happy with your purchase, you may return eligible items for refund provided you obtain a Return Request through EBAY. Return shipping is the responsibility of the buyer. All returns MUST be received within 14 days of original purchase date. Any returns that are received and accepted after 14 days or sent back without a return Ebay authorization will have a 20% restocking fee applied, or refused at our discretion. All returns must be NEW and UNOPENED in the original packaging. All tags and information packets must be attached to the item. Any return sent back that does not meet these requirements will be returned to the sender at their expense. Damaged or Defective Items: In the event an item is damaged or defective and it is within the 14 day return period please let us know exactly what the issue is. All items are reviewed upon return and any item found actually not to be defective will be returned to the sender at their expense. If it is passed the 14 day return policy please contact us or the product manufacturer as they are responsible for all warranty issues. BUYERS REMORSE All items returned within the 14 day return period for buyer\'s remorse or an attempt to mislead or falsely claim that an item is defective will have a 20% restocking fee applied, or refused at our discretion. FREE SHIPPING Items that are marked "free shipping" that are returned will also be charged a 20% restocking fee to cover shipping expenses incurred during the sale. PRICING AND TYPOGRAPHICAL ERRORS - In the event that an Vision Outfitters product is mistakenly listed at an incorrect price or there is an error in the listing due to web interference , Vision Outfitters reserves the right to refuse or cancel any orders placed for product listed at the incorrect price. Vision Outfitters reserves the right to refuse or cancel any such orders whether or not the order has been confirmed and your credit card charged. If your credit card has already been charged for the purchase and your order is cancelled, Vision Outfitters shall issue a credit to your credit card account in the amount of the incorrect price( NOTE: if you paid by paypal your paypal account will receive the refund). If the order was already shipped and an error is discovered after shipment Vision Outfitters will refund the price paid over our manufacturer cost for the item listed in error or you are welcome to return the item for a full refund. NON-DELIVERABLE ADDRESS - If a package is returned because of a non-deliverable address the original shipping WILL NOT BE REFUNDED and the item will be returned to stock with a 20% restocking fee applied. Buyer will also be responsible for an cost incurred to the shipper to have package returned back to us. PLEASE MAKE SURE YOUR ADDRESS IS CORRECT!';
                $items = '';
				$count = 1;
				$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . 
							"<BulkDataExchangeRequests>\n" . 
								"<Header>\n" .
									"<Version>" . EBAY_API_VERSION . "</Version>\n" . 
									"<SiteID>0</SiteID>\n" .
								"</Header>\n";
				while($entry = tep_db_fetch_array($sql)){
					$image = $entry['products_largeimage'];
					if (strpos($image, 'http://')!==false){
						if (strpos($image, 'https://')!==false){
							$image = str_replace('https://', 'http://', $image);
						}
					} else {
						$image = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . $image;
					}
					if (!empty($image)){
						$info = @getimagesize($image);
						if ($info){
							$image_name = basename($image);
							$src_width = $info[0];
							$src_height = $info[1];
							$image_extension = substr($info['mime'], strpos($info['mime'], '/')+1);
							$dst_width = $dst_height = '500';
							if ($src_width<$dst_width && $src_height<$dst_width){
								$dst_x = ($dst_width - $src_width)/2;
								$dst_y = ($dst_height - $src_height)/2;
								
								$dst_image = imagecreatetruecolor($dst_width, $dst_height);
								$white = imagecolorallocate($dst_image, 255, 255, 255);
								imagefill($dst_image, 0, 0, $white);
								switch (strtolower($image_extension)){
									case 'jpg':
									case 'jpeg':
										$src_image = imagecreatefromjpeg($image);
										break;
									case 'gif':
										$src_image = imagecreatefromgif($image);
										break;
									case 'png':
										$src_image = imagecreatefrompng($image);
										break;
								}
								
								if ($src_image){
									imagecopy($dst_image, $src_image, $dst_x, $dst_y, 0, 0, $src_width, $src_height);
									$modified_image_name = $image_name;
									switch (strtolower($image_extension)){
										case 'jpg':
										case 'jpeg':
											imagejpeg($dst_image, DIR_FS_CATALOG_IMAGES . $modified_image_name);
											$image = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . $modified_image_name;
											if (strpos($image, 'https://')!==false){
												$image = str_replace('https://', 'http://', $image);
											}
											break;
										case 'gif':
											imagegif($dst_image, DIR_FS_CATALOG_IMAGES . $modified_image_name);
											$image = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . $modified_image_name;
											if (strpos($image, 'https://')!==false){
												$image = str_replace('https://', 'http://', $image);
											}
											break;
										case 'png':
											//imagepng($dst_image, DIR_FS_CATALOG_IMAGES . $modified_image_name);
											$modified_image_name = substr($modified_image_name, 0, -4) . '.jpg';
											imagejpeg($dst_image, DIR_FS_CATALOG_IMAGES . $modified_image_name );
											$image = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . $modified_image_name;
											if (strpos($image, 'https://')!==false){
												$image = str_replace('https://', 'http://', $image);
											}
											break;
									}
								}
							}
						}
					}
                    //$return_policy = 'If you are not satisfied, return the item for refund.';
					//$template = '';
					//if ($entry['products_model'] == 'PA-PRP1'){
						//$template = file_get_contents('ebay_template.html');
						$place_holders = array('{ITEM_TITLE}', '{ITEM_DESCRIPTION}', '{ITEM_IMAGE}');
						$place_holder_values = array($entry['products_name'], $entry['products_description'], $image);
						$template = str_replace($place_holders, $place_holder_values, $template_content);
                        //$return_policy = 'If you are not happy with your purchase, you may return eligible items for refund provided you obtain a Return Request through EBAY. Return shipping is the responsibility of the buyer. All returns MUST be received within 14 days of original purchase date. Any returns that are received and accepted after 14 days or sent back without a return Ebay authorization will have a 20% restocking fee applied, or refused at our discretion. All returns must be NEW and UNOPENED in the original packaging. All tags and information packets must be attached to the item. Any return sent back that does not meet these requirements will be returned to the sender at their expense. Damaged or Defective Items: In the event an item is damaged or defective and it is within the 14 day return period please let us know exactly what the issue is. All items are reviewed upon return and any item found actually not to be defective will be returned to the sender at their expense. If it is passed the 14 day return policy please contact us or the product manufacturer as they are responsible for all warranty issues. BUYERS REMORSE All items returned within the 14 day return period for buyer’s remorse or an attempt to mislead or falsely claim that an item is defective will have a 20% restocking fee applied, or refused at our discretion. FREE SHIPPING Items that are marked "free shipping" that are returned will also be charged a 20% restocking fee to cover shipping expenses incurred during the sale. PRICING AND TYPOGRAPHICAL ERRORS - In the event that an Vision Outfitters product is mistakenly listed at an incorrect price or there is an error in the listing due to web interference , Vision Outfitters reserves the right to refuse or cancel any orders placed for product listed at the incorrect price. Vision Outfitters reserves the right to refuse or cancel any such orders whether or not the order has been confirmed and your credit card charged. If your credit card has already been charged for the purchase and your order is cancelled, Vision Outfitters shall issue a credit to your credit card account in the amount of the incorrect price( NOTE: if you paid by paypal your paypal account will receive the refund). If the order was already shipped and an error is discovered after shipment Vision Outfitters will refund the price paid over our manufacturer cost for the item listed in error or you are welcome to return the item for a full refund. NON-DELIVERABLE ADDRESS - If a package is returned because of a non-deliverable address the original shipping WILL NOT BE REFUNDED and the item will be returned to stock with a 20% restocking fee applied. Buyer will also be responsible for an cost incurred to the shipper to have package returned back to us. PLEASE MAKE SURE YOUR ADDRESS IS CORRECT!';
					//}
                    if ((int)$entry['products_quantity']<(int)EBAY_MIN_STOCK_QTY){
                        $entry['products_quantity'] = '0';
                    }

					$ebay_price = number_format($entry['products_price'] * EBAY_PRICE_MARKUP_COEFFICIENT, 2);
					
					$xml .=		"<" . $job_type . "Request xmlns=\"urn:ebay:apis:eBLBaseComponents\">\n";
					$xml .=         "<Item>\n";
					$xml .=             "<ApplicationData>" . $entry['products_model'] . "</ApplicationData>\n";
					if (EBAY_ENVIRONMENT=='production'){
						$xml .=			"<AutoPay>true</AutoPay>\n";
					}
                    $xml .=             "<CategoryMappingAllowed>true</CategoryMappingAllowed>\n";
					$xml .=             "<ConditionID>1000</ConditionID>\n";
                    $xml .=             "<Country>US</Country>\n";
					if ($job_type=='AddFixedPriceItem'){
                    $xml .=             "<Currency>USD</Currency>\n";
					}
                    //$xml .=             "<Description>" . htmlspecialchars($entry['products_description']) . "</Description>\n";
					$xml .=             "<Description>" . (!empty($template) ? "<![CDATA[" . $template . "]]>" : htmlspecialchars($entry['products_description']) ) . "</Description>\n";
                    $xml .=             "<DispatchTimeMax>" . EBAY_DISPATCH_TIME_MAX . "</DispatchTimeMax>\n";
                    $xml .=             "<InventoryTrackingMethod>SKU</InventoryTrackingMethod>\n";
                    $xml .=             "<ListingDuration>" . EBAY_ITEM_LISTING_DURATION . "</ListingDuration>\n";
					if ($job_type=='AddFixedPriceItem' || $job_type=='RelistFixedPriceItem' ){
                    $xml .=             "<ListingType>FixedPriceItem</ListingType>";
					}
                    $xml .=             "<OutOfStockControl>true</OutOfStockControl>\n";
                    $xml .=             "<PaymentMethods>VisaMC</PaymentMethods>\n";
					if (EBAY_ENVIRONMENT=='production'){
						$xml .=			"<PaymentMethods>PayPal</PaymentMethods>\n";
						$xml .=			"<PayPalEmailAddress>" . EBAY_EMAIL_ADDRESS_PAYPAL . "</PayPalEmailAddress>\n";
					}
                    $xml .=             "<PictureDetails>\n";
					$xml .=                 "<PictureURL>" . $image . "</PictureURL>\n";
					$xml .=             "</PictureDetails>\n";
                    $xml .=             "<PostalCode>" . EBAY_STORE_OWNER_POSTAL_CODE . "</PostalCode>\n";
                    $xml .=             "<PrimaryCategory>\n";
					$xml .=                 "<CategoryID>" . $entry['ebay_category_id'] . "</CategoryID>\n";
					$xml .=             "</PrimaryCategory>\n";
					if (!empty($entry['upc_ean'])){
					$xml .=             "<ProductListingDetails>\n";
					$xml .=                 "<UPC>" . $entry['upc_ean'] . "</UPC>\n";
					$xml .=             "</ProductListingDetails>\n";
					}
                    $xml .=             "<Quantity>" . $entry['products_quantity'] . "</Quantity>\n";
                    $xml .=             "<ReturnPolicy>\n";
					$xml .=                 "<Description><![CDATA[" . htmlspecialchars($return_policy,ENT_SUBSTITUTE) . "]]></Description>\n";
                    $xml .=                 "<RefundOption>" . EBAY_RETURN_POLICY . "</RefundOption>\n";
                    $xml .=                 "<ReturnsAcceptedOption>" . (EBAY_RETURN_POLICY_STATUS == 'True' ? 'ReturnsAccepted' : 'ReturnsNotAccepted') . "</ReturnsAcceptedOption>\n";
                    $xml .=                 "<ReturnsWithinOption>" . EBAY_RETURN_POLICY_WITHIN . "</ReturnsWithinOption>\n";
					$xml .=                 "<ShippingCostPaidByOption>" . EBAY_RETURN_POLICY_SHIPPING_PAID_BY . "</ShippingCostPaidByOption>\n";
					$xml .=             "</ReturnPolicy>\n";
                    $xml .=             "<ShippingDetails>\n";
					$xml .=                 "<ShippingServiceOptions>\n";
					$xml .=                     "<ShippingService>ShippingMethodStandard</ShippingService>\n";
					$xml .=                     "<ShippingServiceAdditionalCost currencyID=\"USD\">9.95</ShippingServiceAdditionalCost>\n";
					$xml .=                     "<ShippingServiceCost>9.95</ShippingServiceCost>\n";
					$xml .=                     "<ShippingServicePriority>1</ShippingServicePriority>\n";
					$xml .=                 "</ShippingServiceOptions>\n";
					$xml .=             	"<ShippingType>Flat</ShippingType>\n";
					$xml .=             "</ShippingDetails>\n";
					if ($job_type=='AddFixedPriceItem'){
                    $xml .=             "<Site>US</Site>";
					}
                    $xml .=             "<SKU>" . $entry['products_model'] . "</SKU>\n";
                    $xml .=             "<StartPrice>" . $ebay_price . "</StartPrice>\n";
					$xml .=             "<Title>" . htmlspecialchars($entry['products_name']) . "</Title>\n";
					$xml .=         "</Item>\n";
					$xml .=         "<ErrorLanguage>en_US</ErrorLanguage>\n";
					$xml .=         "<MessageID>" . $count . "</MessageID>\n";
					$xml .=         "<Version>" . EBAY_API_VERSION . "</Version>\n";
					$xml .=         "<WarningLevel>High</WarningLevel>\n";
					$xml .=		"</" . $job_type . "Request>\n";

					$count++;
                    
                    $items .= $entry['products_id'] . ', ';
				}
				$xml .=		"</BulkDataExchangeRequests>"; 
			}
		}
	} elseif ($job_type=='EndFixedPriceItem'){
        $items = '';
 		//$sql = tep_db_query("select * from products where (products_status='0' or is_ebay_ok='0') and item_listed_on_ebay='1'");
		$sql = tep_db_query("select * from products where ( (products_status='0' and is_ebay_ok='1') or (is_ebay_ok='0' and item_listed_on_ebay='1') )");
    /*
    //below query was set to remove all items with 0 inventory but it seems to cause issue s when relist feed fires
    //activation previous query
        $sql = tep_db_query("select * from products where ( (products_status='0' and is_ebay_ok='1') or (is_ebay_ok='0' and item_listed_on_ebay='1') or ( (is_ebay_ok='1' or item_listed_on_ebay='1') and products_quantity<" . (int)EBAY_MIN_STOCK_QTY . " ) )");
        */
		if (tep_db_num_rows($sql)){
				$count = 1;
				$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . 
							"<BulkDataExchangeRequests>\n" . 
								"<Header>\n" .
									"<Version>" . EBAY_API_VERSION . "</Version>\n" . 
									"<SiteID>0</SiteID>\n" .
								"</Header>\n";
				while($entry = tep_db_fetch_array($sql)){
					$xml .=		"<EndFixedPriceItemRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">\n";
					$xml .=			"<EndingReason>NotAvailable</EndingReason>\n";
					$xml .=			"<SKU>" . $entry['products_model'] . "</SKU>\n";
					$xml .=         "<ErrorLanguage>en_US</ErrorLanguage>\n";
					$xml .=         "<MessageID>" . $count . "</MessageID>\n";
					$xml .=         "<Version>" . EBAY_API_VERSION . "</Version>\n";
					$xml .=         "<WarningLevel>High</WarningLevel>\n";
					$xml .=		"</EndFixedPriceItemRequest>\n";
					
					$count++;
                    
                    $items .= $entry['products_id'] . ', ';
				}
				$xml .=		"</BulkDataExchangeRequests>"; 
		}
	} elseif($job_type=='ReviseInventoryStatus'){
        $items = '';
 		//$sql = tep_db_query("select products_model, products_quantity, products_price from products where products_status='1' and is_ebay_ok='1' and item_listed_on_ebay='1'");
 		$sql = tep_db_query("select p.products_model, p.products_quantity, p.products_price from products p left join ebay_product_feed_errors pfe on (p.products_model=pfe.sku and pfe.environment='" . EBAY_ENVIRONMENT . "') where (pfe.sku is null or pfe.ack!='failure') and p.products_status='1' and p.is_ebay_ok='1' and p.item_listed_on_ebay='1'");
		if (tep_db_num_rows($sql)){
				$count = 1;
				$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . 
							"<BulkDataExchangeRequests>\n" . 
								"<Header>\n" .
									"<Version>" . EBAY_API_VERSION . "</Version>\n" . 
									"<SiteID>0</SiteID>\n" .
								"</Header>\n";
				while($entry = tep_db_fetch_array($sql)){
                    if ((int)$entry['products_quantity']<(int)EBAY_MIN_STOCK_QTY){
                        $entry['products_quantity'] = '0';
                    }
					$ebay_price = number_format($entry['products_price'] * EBAY_PRICE_MARKUP_COEFFICIENT, 2);
					
					$xml .=		"<ReviseInventoryStatusRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">\n";
					$xml .=			"<InventoryStatus>\n";
					$xml .=				"<Quantity>" . $entry['products_quantity'] . "</Quantity>\n";
					$xml .=				"<SKU>" . $entry['products_model'] . "</SKU>\n";
					$xml .=				"<StartPrice>" . $ebay_price . "</StartPrice>\n";
					$xml .=			"</InventoryStatus>\n";
					$xml .=         "<ErrorLanguage>en_US</ErrorLanguage>\n";
					$xml .=         "<MessageID>" . $count . "</MessageID>\n";
					$xml .=         "<Version>" . EBAY_API_VERSION . "</Version>\n";
					$xml .=         "<WarningLevel>High</WarningLevel>\n";
					$xml .=		"</ReviseInventoryStatusRequest>\n";
					
					$count++;
				}
				$xml .=		"</BulkDataExchangeRequests>"; 
		}
	} elseif ($job_type=='OrderAck'){
        $items = '';
        $sql = tep_db_query("select id, order_line_item_id, ebay_order_id, sku from ebay_order_products where ack_generated='0'");
        if (tep_db_num_rows($sql)){
				$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . 
							"<BulkDataExchangeRequests>\n" . 
								"<Header>\n" .
									"<Version>" . EBAY_API_VERSION . "</Version>\n" . 
									"<SiteID>0</SiteID>\n" .
								"</Header>\n";
                while($entry = tep_db_fetch_array($sql)){
                    $xml .=     "<OrderAckRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">\n";
                    $xml .=         "<OrderID>" . $entry['ebay_order_id'] . "</OrderID>\n";
                    $xml .=         "<OrderLineItemID>" . $entry['order_line_item_id'] . "</OrderLineItemID>\n";
                    $xml .=     "</OrderAckRequest>\n";
            
                    $items .= "'" . $entry['id'] . "', ";
                }
				$xml .=		"</BulkDataExchangeRequests>";
        }
	} elseif ($job_type=='SetShipmentTrackingInfo'){
        $items = '';
        $sql = tep_db_query("select id, order_line_item_id, ebay_order_id from ebay_order_products where fire_shipment_feed='1' and is_shipped='0'");
        if (tep_db_num_rows($sql)){
				$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . 
							"<BulkDataExchangeRequests>\n" . 
								"<Header>\n" .
									"<Version>" . EBAY_API_VERSION . "</Version>\n" . 
									"<SiteID>0</SiteID>\n" .
								"</Header>\n";
                while($entry = tep_db_fetch_array($sql)){
                    $xml .=     "<SetShipmentTrackingInfoRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">\n";
                    $xml .=         "<OrderID>" . $entry['ebay_order_id'] . "</OrderID>\n";
                    $xml .=         "<OrderLineItemID>" . $entry['order_line_item_id'] . "</OrderLineItemID>\n";
                    $xml .=     "</SetShipmentTrackingInfoRequest>\n";
            
                    $items .= "'" . $entry['id'] . "', ";
                }
				$xml .=		"</BulkDataExchangeRequests>";
        }
	}
	return $xml;
}

$session = new LargeMerchantServiceSession('XML','XML', EBAY_ENVIRONMENT);

$sql = tep_db_query("select id, job_type, job_id, file_reference_id from ebay_jobs where is_open='1' and status_create_job='Success' and uploaded_file is null order by id");
if (tep_db_num_rows($sql)){
	while ($entry = tep_db_fetch_array($sql)){
        if ($debug_mode){
            echo "job type" . $entry['job_type'] . "\n";
        }
        $items = '';
		$feed = getFeedXml($entry['job_type'], $items);
        if (!empty($feed)){
            if ($debug_mode){
                echo "$feed\n";
            }
    		//$unique_id = microtime(true);
    		$unique_id = time();
    		$file_name = $entry['job_type'] . '_' . $unique_id . '.xml';
    		file_put_contents(DIR_FS_EBAY_FEEDS . $file_name, $feed);
    		$gz_file_name = $entry['job_type'] . '_' . $unique_id . '.gz';
    		$fp = gzopen (DIR_FS_EBAY_FEEDS . $gz_file_name, 'w9');
    		gzwrite ($fp, file_get_contents(DIR_FS_EBAY_FEEDS . $file_name));
    		gzclose($fp);
    		$handle = fopen(DIR_FS_EBAY_FEEDS . $gz_file_name, 'r');
    		$file_data = fread( $handle, filesize(DIR_FS_EBAY_FEEDS . $gz_file_name) );
    		fclose($handle);
            if ($debug_mode){
                echo "compressed file name: $gz_file_name\n";
            }

    		$requestBody = createUploadFileRequest($entry['job_id'], $entry['file_reference_id'], strlen($file_data) );
    		$request = MultiPartMessage::build($requestBody, $file_data);
    		$responseXML = $session->sendFileTransferServiceUploadRequest($request);
    		$xml = simplexml_load_string($responseXML);
            $sql_data = array(
                'uploaded_file' => $gz_file_name, 
                'last_modified' => 'now()', 
            );
            if (!empty($xml)){
                $sql_data['status_upload_file'] = (string)$xml->ack;
                if ((string)$xml->ack=='Success'){
                    if (!empty($items)){
                        $items = substr($items, 0, -2);
                        switch($entry['job_type']){
                            case 'AddFixedPriceItem':
                                tep_db_query("update products set item_listed_on_ebay='1' where products_id in(" . $items . ")");
                                break;
                            case 'ReviseFixedPriceItem':
							case 'RelistFixedPriceItem':
                                break;
                            case 'EndFixedPriceItem':
                                tep_db_query("update products set item_listed_on_ebay='0' where products_id in(" . $items . ")");
                                break;
                            case 'OrderAck':
                                tep_db_query("update ebay_order_products set ack_generated='1' where id in (" . $items . ")");
                                break;
							case 'SetShipmentTrackingInfo':
								tep_db_query("update ebay_order_products set fire_shipment_feed='0', is_shipped='1', last_modified=now() where id in (" . $items . ")");
								break;
                        }
                    }
                } else {
					$request_abort = createAbortJobRequest($entry['job_id']);
					//$response_abort = $session->sendBulkDataExchangeRequest('abortJob', $request);
					$response_abort = $session->sendBulkDataExchangeRequest('abortJob', $request_abort);
					tep_db_query("update ebay_jobs set is_open='0' where id='" . (int)$entry['id'] . "'");
				}
            } else {
                $sql_data['status_upload_file'] = 'Custom:XML error'; 
            }
            tep_db_perform('ebay_jobs', $sql_data, 'update', "id='" . $entry['id'] . "'");
            if ($debug_mode){
                PrintUtils::printXML($responseXML);
                echo "\n";
            }
        } else {
            if ($debug_mode){
                echo "No xml feed\n";
            }
        }
	}
} else {
    if ($debug_mode){
        echo "CreateUploadJob not yet created\n";
    }
}

if ($debug_mode){
    echo 'End: ' . date('c') . "\n";
}
?>