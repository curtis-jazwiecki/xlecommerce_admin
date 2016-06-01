<?php







/*







  $Id: orders.php,v 1.112 2003/06/29 22:50:52 hpdl Exp $















  osCommerce, Open Source E-Commerce Solutions







  http://www.oscommerce.com















  Copyright (c) 2003 osCommerce















  Released under the GNU General Public License







*/















require('includes/application_top.php');







if ($_GET['w']=='n'){







    echo FRAUD_PREVENTION_NOTIFICATION;







    exit();







}















require(DIR_FS_ADMIN . 'amazon_mws/config.php');















require(DIR_WS_CLASSES . 'currencies.php');







$currencies = new currencies();















$orders_statuses = array();







$orders_status_array = array();















$orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int)$languages_id . "'");







while ($orders_status = tep_db_fetch_array($orders_status_query)) {







    $orders_statuses[] = array('id' => $orders_status['orders_status_id'],







                               'text' => $orders_status['orders_status_name']);







    $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];







}















$action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');















function is_multiple_vendor_order($order_id){







	$sql = tep_db_query("select count(orders_id) as count from orders_shipping where orders_id='" . (int)$order_id . "'");







	$info = tep_db_fetch_array($sql);







	if ($info['count']){







		return true;







	} else {







		return false;







	}







}















function get_vendors_by_order($order_id){







	$vendors = array();







	$sql = tep_db_query("select os.vendors_id as id, os.usps_track_num as usps_tracking, os.ups_track_num as ups_tracking, os.fedex_track_num as fedex_tracking, dhl_track_num as dhl_tracking, extra_track_num as extra_tracking, v.vendors_name as name from orders_shipping os inner join vendors v on os.vendors_id=v.vendors_id where os.orders_id='" . (int)$order_id . "'");







	while ($vendor = tep_db_fetch_array($sql)){







		$vendors[] = $vendor;







	}







	return $vendors;







}















function get_country_code_from_name($country_name){







    //get country code from country name







	$sql_query = tep_db_query("select countries_id as id, countries_iso_code_2 as code from " . TABLE_COUNTRIES . " where countries_name='" . $country_name . "'");







	$sql_array = tep_db_fetch_array($sql_query);







	return $sql_array['id'];







}







		







function get_state_code_from_name($state_name, $country_id){







	//get state code from state name and country_id







    if (strlen($state_name)==2){







        return $state_name;







    } else {







        $sql_query = tep_db_query("select zone_code as code from " . TABLE_ZONES . " where zone_country_id='" . (int)$country_id . "' and zone_name='" . $state_name . "'");







        $sql_array = tep_db_fetch_array($sql_query);







        return $sql_array['code'];







    }







}







  







function get_qty_in_stock($orders_products_id){







    $sql = tep_db_query("select products_quantity from " . TABLE_PRODUCTS . " where products_id=(select products_id from " . TABLE_ORDERS_PRODUCTS . " where orders_products_id='" . (int)$orders_products_id . "')");







    if (tep_db_num_rows($sql)){







        $sql_info = tep_db_fetch_array($sql);







        return $sql_info['products_quantity'];







    } else{







        return 0;







    }







}















if (tep_not_null($action)) {







    switch ($action) {







        case 'send_to_obn':







            require_once 'OBN_order_feed_manager.php';







            $orders_id = tep_db_prepare_input($HTTP_GET_VARS['oID']);







            $order_product_ids = tep_db_prepare_input($HTTP_POST_VARS['chk_send_to_obn']);







            $order_feed = new order_feed($orders_id, $order_product_ids);







            $order_feed->get_order_feed();







            $order_feed->move_order_feed_to_obn();







            tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=edit'));







            break;







        case 'update_order':







            $oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);







            $status = tep_db_prepare_input($HTTP_POST_VARS['status']);







            $comments = tep_db_prepare_input($HTTP_POST_VARS['comments']);







            







            //Package Tracking Plus BEGIN







			$tracking_numbers = array();







			if (is_multiple_vendor_order($oID)) {







				foreach($_POST as $key => $value){







					$is_carrier_tracking_field = false;







					if ( stripos($key, 'usps_track_num')!==false ){







						$carrier = 'usps';







						$is_carrier_tracking_field = true;







					} elseif ( stripos($key, 'ups_track_num')!==false ){







						$carrier = 'ups';







						$is_carrier_tracking_field = true;







					} elseif ( stripos($key, 'fedex_track_num')!==false ){







						$carrier = 'fedex';







						$is_carrier_tracking_field = true;







					} elseif ( stripos($key, 'dhl_track_num')!==false ){







						$carrier = 'dhl';







						$is_carrier_tracking_field = true;







					} elseif ( stripos($key, 'extra_track_num')!==false ){







						$carrier = 'extra';







						$is_carrier_tracking_field = true;







					}







					







					if ($is_carrier_tracking_field && !empty($value) ){







						list(,,,$vendor_id) = explode('_', $key);







						$tracking[$carrier][] = array(







							'vendor_id' => $vendor_id, 







							'tracking_num' => $value, 







						);







					}







				}







			} else {







				$usps_track_num = tep_db_prepare_input($HTTP_POST_VARS['usps_track_num']);







				$usps_track_num2 = tep_db_prepare_input($HTTP_POST_VARS['usps_track_num2']);







				$ups_track_num = tep_db_prepare_input($HTTP_POST_VARS['ups_track_num']);







				$ups_track_num2 = tep_db_prepare_input($HTTP_POST_VARS['ups_track_num2']);







				$fedex_track_num = tep_db_prepare_input($HTTP_POST_VARS['fedex_track_num']);







				$fedex_track_num2 = tep_db_prepare_input($HTTP_POST_VARS['fedex_track_num2']);







				$dhl_track_num = tep_db_prepare_input($HTTP_POST_VARS['dhl_track_num']);







				$dhl_track_num2 = tep_db_prepare_input($HTTP_POST_VARS['dhl_track_num2']);







				$extra_track_num = tep_db_prepare_input($HTTP_POST_VARS['extra_track_num']);







			}







            //Package Tracking Plus END























            $order_updated = false;















            $check_status_query = tep_db_query("select customers_name, customers_email_address, orders_status, date_purchased, usps_track_num, usps_track_num2, ups_track_num, ups_track_num2, fedex_track_num, fedex_track_num2, dhl_track_num, dhl_track_num2, extra_track_num from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");







	    







            $check_status = tep_db_fetch_array($check_status_query);















            if ( ($check_status['orders_status'] != $status) || tep_not_null($comments)) {







                tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . (int)$oID . "'");







                //Package Tracking Plus BEGIN







                $customer_notified = '0';







                







                //if ($HTTP_POST_VARS['notify'] == 'on' & ($usps_track_num == '' & $usps_track_num2 == '' & $ups_track_num == '' & $ups_track_num2 == '' & $fedex_track_num == '' & $fedex_track_num2 == '' & $dhl_track_num == '' & $dhl_track_num2 == '' & $extra_track_num == '' ) ) {







                if ($HTTP_POST_VARS['notify'] == 'on' && ($usps_track_num == '' && $usps_track_num2 == '' && $ups_track_num == '' && $ups_track_num2 == '' && $fedex_track_num == '' && $fedex_track_num2 == '' && $dhl_track_num == '' && $dhl_track_num2 == '' && $extra_track_num == '' ) && empty($tracking_numbers)  ) {







                    $notify_comments = '';















                    if ($HTTP_POST_VARS['notify_comments'] == 'on') {







                        $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n";







                        if ($comments == null) $notify_comments = '';







                    }















                    $email = STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL') . "\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);







             







                    tep_mail($check_status['customers_name'], $check_status['customers_email_address'], STORE_NAME . ' ' . EMAIL_TEXT_SUBJECT_1. (int)$oID . EMAIL_TEXT_SUBJECT_2 . $orders_status_array[$status], $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);







                    







                    $customer_notified = '1';















                //} elseif ($HTTP_POST_VARS['notify'] == 'on' & ($usps_track_num == '' or $usps_track_num2 == '' or $ups_track_num == '' or $ups_track_num2 == '' or $fedex_track_num == '' or $fedex_track_num2 == '' or $dhl_track_num == '' or $dhl_track_num2 == '' or $extra_track_num == '' ) ) {







                } elseif ($HTTP_POST_VARS['notify'] == 'on' && ( ($usps_track_num != '' or $usps_track_num2 != '' or $ups_track_num != '' or $ups_track_num2 != '' or $fedex_track_num != '' or $fedex_track_num2 != '' or $dhl_track_num != '' or $dhl_track_num2 != '' or $extra_track_num != '' ) || !empty($tracking_numbers)  )   ) {







                    $notify_comments = '';







                    if ($HTTP_POST_VARS['notify_comments'] == 'on') {







                        $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n";







                        if ($comments == null) $notify_comments = '';







                    }







					







					$usps = array();







					if (!empty($tracking['usps'])) {







						$count = 0;







						foreach($tracking['usps'] as $entry){







							$count++;







							$temp_num = str_replace(' ', '', $entry['tracking_num']);







							$temp_link = 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=' . $temp_num;







							$usps[] = array(







								'text' => 'USPS(' . $count . '): ', 







								'track' => '<a target="_blank" href="' . $temp_link . '">' . $temp_num . '</a>' . "\n", 







							);







						}







					} else {







						if ($usps_track_num == null) {







							$usps_text = '';







							$usps_track = '';







						} else {







							$usps_text = 'USPS(1): ';







							$usps_track_num_noblanks = str_replace(' ', '', $usps_track_num);







							$usps_link = 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=' . $usps_track_num_noblanks;







							$usps_track = '<a target="_blank" href="' . $usps_link . '">' . $usps_track_num . '</a>' . "\n";







						}















						if ($usps_track_num2 == null) {







							$usps_text2 = '';







							$usps_track2 = '';







						} else {







							$usps_text2 = 'USPS(2): ';







							$usps_track_num2_noblanks = str_replace(' ', '', $usps_track_num2);







							$usps_link2 = 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=' . $usps_track_num2_noblanks;







							$usps_track2 = '<a target="_blank" href="' . $usps_link2 . '">' . $usps_track_num2 . '</a>' . "\n";







						}







					}







                    







					$ups = array();







					if (!empty($tracking['ups'])){







						$count = 0;







						foreach($tracking['ups'] as $entry){







							$count++;







							$temp_num = str_replace(' ', '', $entry['tracking_num']);







							$temp_link = 'http://www.apps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . $temp_num . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package ';







							$ups[] = array(







								'text' => 'UPS(' . $count . '): ', 







								'track' => '<a target="_blank" href="' . $temp_link . '">' . $temp_num . '</a>' . "\n", 







							);







						}







					} else {







						if ($ups_track_num == null) {







							$ups_text = '';







							$ups_track = '';







						} else {







							$ups_text = 'UPS(1): ';







							$ups_track_num_noblanks = str_replace(' ', '', $ups_track_num);







							//$ups_link = 'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . $ups_track_num_noblanks . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package ';







							$ups_link = 'http://www.apps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . $ups_track_num_noblanks . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package ';







							$ups_track = '<a target="_blank" href="' . $ups_link . '">' . $ups_track_num . '</a>' . "\n";







						}















						if ($ups_track_num2 == null) {







							$ups_text2 = '';







							$ups_track2 = '';







						} else {







							$ups_text2 = 'UPS(2): ';







							$ups_track_num2_noblanks = str_replace(' ', '', $ups_track_num2);







							//$ups_link2 = 'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . $ups_track_num2_noblanks . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package ';







							$ups_link2 = 'http://www.apps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . $ups_track_num2_noblanks . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package ';







							$ups_track2 = '<a target="_blank" href="' . $ups_link2 . '">' . $ups_track_num2 . '</a>' . "\n";







						}







					}







					







					$fedex = array();







					if (!empty($tracking['fedex'])){







						$count = 0;







						foreach($tracking['fedex'] as $entry){







							$count++;







							$temp_num = str_replace(' ', '', $entry['tracking_num']);







							$temp_link = 'http://www.fedex.com/Tracking?tracknumbers=' . $temp_num . '&action=track&language=english&cntry_code=us';







							$fedex[] = array(







								'text' => 'Fedex(' . $count . '): ', 







								'track' => '<a target="_blank" href="' . $temp_link . '">' . $temp_num . '</a>' . "\n", 







							);







						}







					} else {







						if ($fedex_track_num == null) {







							$fedex_text = '';







							$fedex_track = '';







						} else {







							$fedex_text = 'Fedex(1): ';







							$fedex_track_num_noblanks = str_replace(' ', '', $fedex_track_num);







							$fedex_link = 'http://www.fedex.com/Tracking?tracknumbers=' . $fedex_track_num_noblanks . '&action=track&language=english&cntry_code=us';







							$fedex_track = '<a target="_blank" href="' . $fedex_link . '">' . $fedex_track_num . '</a>' . "\n";







						}















						if ($fedex_track_num2 == null) {







							$fedex_text2 = '';







							$fedex_track2 = '';







						} else {







							$fedex_text2 = 'Fedex(2): ';







							$fedex_track_num2_noblanks = str_replace(' ', '', $fedex_track_num2);







							$fedex_link2 = 'http://www.fedex.com/Tracking?tracknumbers=' . $fedex_track_num2_noblanks . '&action=track&language=english&cntry_code=us';







							$fedex_track2 = '<a target="_blank" href="' . $fedex_link2 . '">' . $fedex_track_num2 . '</a>' . "\n";







						}







					}















					$dhl = array();







					if (!empty($tracking['dhl'])){







						$count = 0;







						foreach($tracking['dhl'] as $entry){







							$count++;







							$temp_num = str_replace(' ', '', $entry['tracking_num']);







							$temp_link = 'http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=' . $temp_num . '&action=track&language=english&cntry_code=us';







							$dhl[] = array(







								'text' => 'DHL(' . $count . '): ', 







								'track' => '<a target="_blank" href="' . $temp_link . '">' . $temp_num . '</a>' . "\n", 







							);







						}







					} else {







						if ($dhl_track_num == null) {







							$dhl_text = '';







							$dhl_track = '';







						} else {







							$dhl_text = 'DHL(1): ';







							$dhl_track_num_noblanks = str_replace(' ', '', $dhl_track_num);







							$dhl_link = 'http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=' . $dhl_track_num_noblanks . '&action=track&language=english&cntry_code=us';







							$dhl_track = '<a target="_blank" href="' . $dhl_link . '">' . $dhl_track_num . '</a>' . "\n";







						}















						if ($dhl_track_num2 == null) {







							$dhl_text2 = '';







							$dhl_track2 = '';







						} else {







							$dhl_text2 = 'DHL(2): ';







							$dhl_track_num2_noblanks = str_replace(' ', '', $dhl_track_num2);







							$dhl_link2 = 'http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=' . $dhl_track_num2_noblanks . '&action=track&language=english&cntry_code=us';







							$dhl_track2 = '<a target="_blank" href="' . $dhl_link2 . '">' . $dhl_track_num2 . '</a>' . "\n";







						}







					}







					







					$extra = array();







					if (!empty($tracking['extra'])){







						$count = 0;







						foreach($tracking['extra'] as $entry){







							$count++;







							$temp_num = str_replace(' ', '', $entry['tracking_num']);







							$extra[] = array(







								'text' => EXTRA_TRACKING_EMAIL . '(' . $count . ') ' . $temp_num, 







								'track' => '', 







							);







						}







					} else {







						if ($extra_track_num == null) {







							$extra_text = '';







						} else {







							$extra_text = EXTRA_TRACKING_EMAIL;







							$extra_track_num_noblanks = str_replace(' ', '', $extra_track_num);







							$extra_text .= ' ' . $extra_track_num_noblanks;







						}







					}















                    $email = 'Dear ' . $check_status['customers_name'] . ',' . "\n\n" . STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . "<a HREF='" . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "'>" .  'order_id=' . (int)$oID . "</a>\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_TRACKING_NUMBER . "\n" . $usps_text . $usps_track . $usps_text2 . $usps_track2 . $ups_text . $ups_track . $ups_text2 . $ups_track2 . $fedex_text . $fedex_track . $fedex_text2 . $fedex_track2 . $dhl_text . $dhl_track . $dhl_text2 . $dhl_track2 .  $extra_text . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);







                    







                    tep_mail($check_status['customers_name'], $check_status['customers_email_address'], STORE_NAME . ' ' . EMAIL_TEXT_SUBJECT_1. (int)$oID . EMAIL_TEXT_SUBJECT_2 . $orders_status_array[$status], $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);















                    $customer_notified = '1';















                //} elseif ($HTTP_POST_VARS['notify'] == 'on' & (tep_not_null($usps_track_num) & tep_not_null($usps_track_num2) & tep_not_null($ups_track_num) & tep_not_null($ups_track_num2) & tep_not_null($fedex_track_num) & tep_not_null($fedex_track_num2) & tep_not_null($dhl_track_num) & tep_not_null($dhl_track_num2) & tep_not_null($extra_track_num) ) ) {







                } elseif ($HTTP_POST_VARS['notify'] == 'on' && ( (tep_not_null($usps_track_num) && tep_not_null($usps_track_num2) && tep_not_null($ups_track_num) && tep_not_null($ups_track_num2) && tep_not_null($fedex_track_num) && tep_not_null($fedex_track_num2) && tep_not_null($dhl_track_num) && tep_not_null($dhl_track_num2) && tep_not_null($extra_track_num) ) || !empty($tracking) ) ) {







                    $notify_comments = '';







					







					$usps = array();







					if (!empty($tracking['usps'])) {







						$count = 0;







						foreach($tracking['usps'] as $entry){







							$count++;







							$temp_num = str_replace(' ', '', $entry['tracking_num']);







							$temp_link = 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=' . $temp_num;







							$usps[] = array(







								'text' => 'USPS(' . $count . '): ', 







								'track' => '<a target="_blank" href="' . $temp_link . '">' . $temp_num . '</a>' . "\n", 







							);







						}







					} else {







						$usps_text = 'USPS(1): ';







						$usps_track_num_noblanks = str_replace(' ', '', $usps_track_num);







						$usps_link = 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=' . $usps_track_num_noblanks;







						$usps_track = '<a target="_blank" href="' . $usps_link . '">' . $usps_track_num . '</a>' . "\n";







						$usps_text2 = 'USPS(2): ';







						$usps_track_num2_noblanks = str_replace(' ', '', $usps_track_num2);







						$usps_link2 = 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=' . $usps_track_num2_noblanks;







						$usps_track2 = '<a target="_blank" href="' . $usps_link2 . '">' . $usps_track_num2 . '</a>' . "\n";







					}















					$ups = array();







					if (!empty($tracking['ups'])){







						$count = 0;







						foreach($tracking['ups'] as $entry){







							$count++;







							$temp_num = str_replace(' ', '', $entry['tracking_num']);







							$temp_link = 'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . $temp_num . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package ';







							$ups[] = array(







								'text' => 'UPS(' . $count . '): ', 







								'track' => '<a target="_blank" href="' . $temp_link . '">' . $temp_num . '</a>' . "\n", 







							);







						}







					} else {







						$ups_text = 'UPS(1): ';







						$ups_track_num_noblanks = str_replace(' ', '', $ups_track_num);







						//$ups_link = 'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . $ups_track_num_noblanks . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package ';







						$ups_link = 'http://www.apps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . $ups_track_num_noblanks . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package ';















						$ups_track = '<a target="_blank" href="' . $ups_link . '">' . $ups_track_num . '</a>' . "\n";







						$ups_text2 = 'UPS(2): ';







						$ups_track_num2_noblanks = str_replace(' ', '', $ups_track_num2);







						//$ups_link2 = 'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . $ups_track_num2_noblanks . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package ';







						$ups_link2 = 'http://www.apps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . $ups_track_num2_noblanks . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package ';







						$ups_track2 = '<a target="_blank" href="' . $ups_link2 . '">' . $ups_track_num2 . '</a>' . "\n";







					}















					$fedex = array();







					if (!empty($tracking['fedex'])){







						$count = 0;







						foreach($tracking['fedex'] as $entry){







							$count++;







							$temp_num = str_replace(' ', '', $entry['tracking_num']);







							$temp_link = 'http://www.fedex.com/Tracking?tracknumbers=' . $temp_num . '&action=track&language=english&cntry_code=us';







							$fedex[] = array(







								'text' => 'Fedex(' . $count . '): ', 







								'track' => '<a target="_blank" href="' . $temp_link . '">' . $temp_num . '</a>' . "\n", 







							);







						}







					} else {







						$fedex_text = 'Fedex(1): ';







						$fedex_track_num_noblanks = str_replace(' ', '', $fedex_track_num);







						$fedex_link = 'http://www.fedex.com/Tracking?tracknumbers=' . $fedex_track_num_noblanks . '&action=track&language=english&cntry_code=us';







						$fedex_track = '<a target="_blank" href="' . $fedex_link . '">' . $fedex_track_num . '</a>' . "\n";







						$fedex_text2 = 'Fedex(2): ';







						$fedex_track_num2_noblanks = str_replace(' ', '', $fedex_track_num2);







						$fedex_link2 = 'http://www.fedex.com/Tracking?tracknumbers=' . $fedex_track_num2_noblanks . '&action=track&language=english&cntry_code=us';







						$fedex_track2 = '<a target="_blank" href="' . $fedex_link2 . '">' . $fedex_track_num2 . '</a>' . "\n";







					}















					$dhl = array();







					if (!empty($tracking['dhl'])){







						$count = 0;







						foreach($tracking['dhl'] as $entry){







							$count++;







							$temp_num = str_replace(' ', '', $entry['tracking_num']);







							$temp_link = 'http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=' . $temp_num . '&action=track&language=english&cntry_code=us';







							$dhl[] = array(







								'text' => 'DHL(' . $count . '): ', 







								'track' => '<a target="_blank" href="' . $temp_link . '">' . $temp_num . '</a>' . "\n", 







							);







						}







					} else {







						$dhl_text = 'DHL(1): ';







						$dhl_track_num_noblanks = str_replace(' ', '', $dhl_track_num);







						$dhl_link = 'http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=' . $dhl_track_num_noblanks . '&action=track&language=english&cntry_code=us';







						$dhl_track = '<a target="_blank" href="' . $dhl_link . '">' . $dhl_track_num . '</a>' . "\n";







						$dhl_text2 = 'DHL(2): ';







						$dhl_track_num2_noblanks = str_replace(' ', '', $dhl_track_num2);







						$dhl_link2 = 'http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=' . $dhl_track_num2_noblanks . '&action=track&language=english&cntry_code=us';







						$dhl_track2 = '<a target="_blank" href="' . $dhl_link2 . '">' . $dhl_track_num2 . '</a>' . "\n";







					}















					$extra = array();







					if (!empty($tracking['extra'])){







						$count = 0;







						foreach($tracking['extra'] as $entry){







							$count++;







							$temp_num = str_replace(' ', '', $entry['tracking_num']);







							$extra[] = array(







								'text' => EXTRA_TRACKING_EMAIL . '(' . $count . ') ' . $temp_num, 







								'track' => '', 







							);







						}







					} else {







						$extra_text = EXTRA_TRACKING_EMAIL;







						$extra_track_num_noblanks = str_replace(' ', '', $extra_track_num);







						$extra_text .= ' ' . $extra_track_num_noblanks;







					}















                    if ($HTTP_POST_VARS['notify_comments'] == 'on') {







                        $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n";







                        if ($comments == null) $notify_comments = '';







                    }















                    //$email = 'Dear ' . $check_status['customers_name'] . ',' . "\n\n" . STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . "<a HREF='" . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "'>" .  'order_id=' . (int)$oID . "</a>\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_TRACKING_NUMBER . "\n" . $usps_text . $usps_track . $usps_text2 . $usps_track2 . $ups_text . $ups_track . $ups_text2 . $ups_track2 . $fedex_text . $fedex_track . $fedex_text2 . $fedex_track2 . $dhl_text . $dhl_track . $dhl_text2 . $dhl_track2 . $extra_text . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);







                    $email = 'Dear ' . $check_status['customers_name'] . ',' . "\n\n" . STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . "<a HREF='" . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "'>" .  'order_id=' . (int)$oID . "</a>\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_TRACKING_NUMBER . "\n" . $usps_text . $usps_track . $usps_text2 . $usps_track2 . $ups_text . $ups_track . $ups_text2 . $ups_track2 . $fedex_text . $fedex_track . $fedex_text2 . $fedex_track2 . $dhl_text . $dhl_track . $dhl_text2 . $dhl_track2 . $extra_text;







					if (!empty($usps)){







						foreach($usps as $entry){







							$email .= $entry['text'] . $entry['track'];







						}







					}







					if (!empty($ups)){







						foreach($ups as $entry){







							$email .= $entry['text'] . $entry['track'];







						}







					}







					if (!empty($fedex)){







						foreach($fedex as $entry){







							$email .= $entry['text'] . $entry['track'];







						}







					}







					if (!empty($dhl)){







						foreach($dhl as $entry){







							$email .= $entry['text'] . $entry['track'];







						}







					}







					if (!empty($extra)){







						foreach($extra as $entry){







							$email .= $entry['text'] . $entry['track'];







						}







					}







					$email .= "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);







                    







                    tep_mail($check_status['customers_name'], $check_status['customers_email_address'], STORE_NAME . ' ' . EMAIL_TEXT_SUBJECT_1 . (int)$oID . EMAIL_TEXT_SUBJECT_2 . $orders_status_array[$status], $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);







                    







                    $customer_notified = '1';







                }







                //Package Tracking Plus END







                







                ######## Points/Rewards Module V2.1rc2a BOF ##################















                if ((USE_POINTS_SYSTEM == 'true') && !tep_not_null(POINTS_AUTO_ON)) {







                    if ((isset($_POST['confirm_points']) && ($_POST['confirm_points'] == 'on'))||(isset($_POST['delete_points']) && ($_POST['delete_points'] == 'on'))) {







                        $comments = ENTRY_CONFIRMED_POINTS  . $comments;







                        







                        $customer_query = tep_db_query("select customer_id, points_pending from " . TABLE_CUSTOMERS_POINTS_PENDING . " where points_status = 1 and points_type = 'SP' and orders_id = '" . (int)$oID . "' limit 1");







                        $customer_points = tep_db_fetch_array($customer_query);







                        if (tep_db_num_rows($customer_query)) {







                            if (tep_not_null(POINTS_AUTO_EXPIRES)) {







                                $expire  = date('Y-m-d', strtotime('+ '. POINTS_AUTO_EXPIRES .' month'));







                                tep_db_query("update " . TABLE_CUSTOMERS . " set customers_shopping_points = customers_shopping_points + '". $customer_points['points_pending'] ."', customers_points_expires = '". $expire ."' where customers_id = '". (int)$customer_points['customer_id'] ."'");







                            } else {







                                tep_db_query("update " . TABLE_CUSTOMERS . " set customers_shopping_points = customers_shopping_points + '". $customer_points['points_pending'] ."' where customers_id = '". (int)$customer_points['customer_id'] ."'");







                            }







			          







                            if (isset($_POST['delete_points']) && ($_POST['delete_points'] == 'on')) {







                                tep_db_query("delete from " . TABLE_CUSTOMERS_POINTS_PENDING . " where orders_id = '" . (int)$oID . "' and points_type = 'SP' limit 1");







                                $sql = "optimize table " . TABLE_CUSTOMERS_POINTS_PENDING . "";







                            }







			          







                            if (isset($_POST['confirm_points']) && ($_POST['confirm_points'] == 'on')) {







                                tep_db_query("update " . TABLE_CUSTOMERS_POINTS_PENDING . " set points_status = 2 where orders_id = '" . (int)$oID . "' and points_type = 'SP' limit 1");







                                $sql = "optimize table " . TABLE_CUSTOMERS_POINTS_PENDING . "";







                            }







                        }







                    }







                }







                ######## Points/Rewards Module V2.1rc2a EOF ##################















                tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . (int)$oID . "', '" . tep_db_input($status) . "', now(), '" . tep_db_input($customer_notified) . "', '" . tep_db_input($comments)  . "')");







                $order_updated = true;







            }



            



            // code to connect avalara.com and commit/void tax #start



            if($order_updated){



                if($status == MODULE_ORDER_TOTAL_AVATAX_COMMIT){



                    



                    if(MODULE_ORDER_TOTAL_AVATAX_DOCUMENT_COMMITING == '0'){ // check global configuration value



                        // commit tax to avalara



                        $postData = "oID". '='.$oID.'&'."mode=commit";



                        $ch = curl_init();  



                        curl_setopt($ch,CURLOPT_URL,HTTP_SERVER.DIR_WS_ADMIN."commit_to_alavara.php");



                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);



                        curl_setopt($ch, CURLOPT_POST, count($postData));



                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    



                        $alavara_response = curl_exec($ch);



                        curl_close($ch);



                        if($alavara_response == "1"){



                            $messageStack->add_session('Tax commited to Avalara.com Successfully...', 'success');    



                        }else if($alavara_response == "-1"){



                            $messageStack->add_session('Error: Connecting Avalara.com, unable to commit tax!', 'warning');



                        }    



                    }



                    



                    



               



                }else if($status == MODULE_ORDER_TOTAL_AVATAX_VOID){



                   // void tax to avalara



                    $postData = "oID". '='.$oID.'&'."mode=cancel";



                    $ch = curl_init();  



                    curl_setopt($ch,CURLOPT_URL,HTTP_SERVER.DIR_WS_ADMIN."commit_to_alavara.php");



                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);



                    curl_setopt($ch, CURLOPT_POST, count($postData));



                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    



                    $alavara_response = curl_exec($ch);



                    curl_close($ch); 



                    if($alavara_response == "1"){



                        $messageStack->add_session('Tax voided to Avalara.com Successfully...', 'success');    



                    }else if($alavara_response == "-1"){



                        $messageStack->add_session('Error: Connecting Avalara.com, unable to void tax!', 'warning');



                    }



                }



                



            }



            // code to connect avalara.com and commit/void tax #ends







      







            //Package Tracking Plus BEGIN







			if (is_multiple_vendor_order($oID) && is_array($tracking)){







			



                foreach($tracking as $carrier => $info){







					switch ($carrier){







						case 'usps':







							foreach($info as $entry){







								tep_db_query("update orders_shipping set usps_track_num = '" . tep_db_input($entry['tracking_num']) . "' where orders_id = '" . (int)$oID . "' and vendors_id='" . (int)$entry['vendor_id'] . "'");







							}







							break;







						case 'ups':







							foreach($info as $entry){







								tep_db_query("update orders_shipping set ups_track_num = '" . tep_db_input($entry['tracking_num']) . "' where orders_id = '" . (int)$oID . "' and vendors_id='" . (int)$entry['vendor_id'] . "'");







							}







							break;







						case 'fedex':







							foreach($info as $entry){







								tep_db_query("update orders_shipping set fedex_track_num = '" . tep_db_input($entry['tracking_num']) . "' where orders_id = '" . (int)$oID . "' and vendors_id='" . (int)$entry['vendor_id'] . "'");







							}







							break;







						case 'dhl':







							foreach($info as $entry){







								tep_db_query("update orders_shipping set dhl_track_num = '" . tep_db_input($entry['tracking_num']) . "' where orders_id = '" . (int)$oID . "' and vendors_id='" . (int)$entry['vendor_id'] . "'");







							}







							break;







						case 'extra':







							foreach($info as $entry){







								tep_db_query("update orders_shipping set extra_track_num = '" . tep_db_input($entry['tracking_num']) . "' where orders_id = '" . (int)$oID . "' and vendors_id='" . (int)$entry['vendor_id'] . "'");







							}







							break;







					}







				}







			} else {







				tep_db_query("update " . TABLE_ORDERS . " set usps_track_num = '" . tep_db_input($usps_track_num) . "' where orders_id = '" . tep_db_input($oID) . "'");







				tep_db_query("update " . TABLE_ORDERS . " set usps_track_num2 = '" . tep_db_input($usps_track_num2) . "' where orders_id = '" . tep_db_input($oID) . "'");







				tep_db_query("update " . TABLE_ORDERS . " set ups_track_num = '" . tep_db_input($ups_track_num) . "' where orders_id = '" . tep_db_input($oID) . "'");







				tep_db_query("update " . TABLE_ORDERS . " set ups_track_num2 = '" . tep_db_input($ups_track_num2) . "' where orders_id = '" . tep_db_input($oID) . "'");







				tep_db_query("update " . TABLE_ORDERS . " set fedex_track_num = '" . tep_db_input($fedex_track_num) . "' where orders_id = '" . tep_db_input($oID) . "'");







				tep_db_query("update " . TABLE_ORDERS . " set fedex_track_num2 = '" . tep_db_input($fedex_track_num2) . "' where orders_id = '" . tep_db_input($oID) . "'");







				tep_db_query("update " . TABLE_ORDERS . " set dhl_track_num = '" . tep_db_input($dhl_track_num) . "' where orders_id = '" . tep_db_input($oID) . "'");







				tep_db_query("update " . TABLE_ORDERS . " set dhl_track_num2 = '" . tep_db_input($dhl_track_num2) . "' where orders_id = '" . tep_db_input($oID) . "'");







				tep_db_query("update " . TABLE_ORDERS . " set extra_track_num = '" . tep_db_input($extra_track_num) . "' where orders_id = '" . tep_db_input($oID) . "'");







			}







            







            $order_updated = true;







            //Package Tracking Plus END















            if ($order_updated == true) {







                $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');







            } else {







                $messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');







            }















            tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=edit'));







            break;







        case 'deleteconfirm':







            $oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);







            tep_remove_order($oID, $HTTP_POST_VARS['restock']);







            tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action'))));







            break;







        case 'captureAuthorizeTransaction':







            $order_id = $_GET['oID'];







            //$transac_comment_query = tep_db_query("select transaction_details, transaction_id from orders where orders_id = '".$order_id."' and is_capture = '0'");







            $transac_comment_query = tep_db_query("select transaction_id from orders where orders_id = '".$order_id."' and is_capture = '0'");







            if(tep_db_num_rows($transac_comment_query)){







                $order_total_query = tep_db_query("SELECT `value` FROM `orders_total` WHERE `orders_id` = '".$order_id."' and `class` = 'ot_total'");







                $order_total_array = tep_db_fetch_array($order_total_query);







                $transac_comment_array = tep_db_fetch_array($transac_comment_query);







                //$transaction_comment = $transac_comment_array['transaction_details'];







                $order_amount = $order_total_array['value'];







                $transaction_id = $transac_comment_array['transaction_id'];







            







            







                $submit_data = array(







                'x_login'               => MODULE_PAYMENT_AUTHORIZENET_AIM_LOGIN, // The login name as assigned to you by authorize.net







                'x_tran_key'            => MODULE_PAYMENT_AUTHORIZENET_AIM_TXNKEY,  // The Transaction Key (16 digits) is generated through the merchant interface







                'x_relay_response'      => 'FALSE', // AIM uses direct response, not relay response







                'x_delim_char'          => '|',







                'x_delim_data'          => 'TRUE', // The default delimiter is a comma







                'x_version'             => '3.1',  // 3.1 is required to use CVV codes







                'x_type'                => 'PRIOR_AUTH_CAPTURE',







                'x_trans_id'            => $transaction_id,







                'x_amount'              => number_format($order_amount, 2),







                'x_email_customer'      => MODULE_PAYMENT_AUTHORIZENET_AIM_EMAIL_CUSTOMER == 'True' ? 'TRUE': 'FALSE',







                'x_email_merchant'      => MODULE_PAYMENT_AUTHORIZENET_AIM_EMAIL_MERCHANT == 'True' ? 'TRUE': 'FALSE',







                'x_invoice_num'         => $order_id);







            







                if (MODULE_PAYMENT_AUTHORIZENET_AIM_TESTMODE == 'Test') {







                    //$url = 'https://secure.authorize.net/gateway/transact.dll';







                    $url = 'https://test.authorize.net/gateway/transact.dll';







                } else {







                    $url = 'https://secure.authorize.net/gateway/transact.dll';







                }







                $data = "";







                foreach( $submit_data as $key => $value )







                        { $data .= "$key=" . urlencode( $value ) . "&"; }







                $data = rtrim( $data, "& " );







                //print_r($data);







                $request = curl_init($url);







                curl_setopt($request, CURLOPT_HEADER, 0);







                curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);







                curl_setopt($request, CURLOPT_POSTFIELDS, $data);







                curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);







                $response = curl_exec($request);







                curl_close ($request);







                $response_array = explode($submit_data["x_delim_char"],$response);















                if($response_array[0] == 1 && $response_array[6] == $transaction_id){







                    //$append_comment = 'Authorization code: '.$response_array[4];







                    //$nen_transaction_comment = 'Capture Details: '."\n".$append_comment."\n". $response."\n".' Authorize Details: '."\n".$transaction_comment;







                    //tep_db_query("update orders set is_capture = '1', transaction_details = '".tep_db_input($nen_transaction_comment)."' where orders_id = '".$order_id."'");







					tep_db_query("update orders set is_capture = '1' where orders_id = '".$order_id."'");







					







					$debug_details = 'Capture Details: '."\n" . 'Authorization code: '.$response_array[4] ."\n". $response."\n".' Authorize Details: '."\n";







					debug_register_order_authorization_details($order_id, $debug_details);







                    $messageStack->add_session('Payment Captured Successfully', 'success');







                }else{







                    $messageStack->add_session('Payment capture failed', 'warning');







                }







            }







            tep_redirect(tep_href_link('orders.php','action=edit&oID='.$order_id));







        break;







        case 'captureFastChargeTransaction':







            $order_id = $_GET['oID'];







            //$transac_comment_query = tep_db_query("select transaction_details, transaction_id from orders where orders_id = '".$order_id."' and is_capture = '0'");







            $transac_comment_query = tep_db_query("select transaction_id from orders where orders_id = '".$order_id."' and is_capture = '0'");







            if(tep_db_num_rows($transac_comment_query)){







                $order_total_query = tep_db_query("SELECT `value` FROM `orders_total` WHERE `orders_id` = '".$order_id."' and `class` = 'ot_total'");







                $order_total_array = tep_db_fetch_array($order_total_query);







                $transac_comment_array = tep_db_fetch_array($transac_comment_query);







                //$transaction_comment = $transac_comment_array['transaction_details'];







                $order_amount = $order_total_array['value'];







                $transaction_id = $transac_comment_array['transaction_id'];







            







            







                $submit_data = array(







                'x_login'               => MODULE_PAYMENT_FASTCHARGE_LOGIN, // The login name as assigned to you by authorize.net







                'x_tran_key'            => MODULE_PAYMENT_FASTCHARGE_TXNKEY,  // The Transaction Key (16 digits) is generated through the merchant interface







                'x_relay_response'      => 'FALSE', // AIM uses direct response, not relay response







                'x_delim_char'          => '|',







                'x_delim_data'          => 'TRUE', // The default delimiter is a comma







                'x_version'             => '3.1',  // 3.1 is required to use CVV codes







                'x_type'                => 'PRIOR_AUTH_CAPTURE',







                'x_trans_id'            => $transaction_id,







                'x_amount'              => number_format($order_amount, 2),







                'x_email_customer'      => MODULE_PAYMENT_FASTCHARGE_EMAIL_CUSTOMER == 'True' ? 'TRUE': 'FALSE',







                'x_email_merchant'      => MODULE_PAYMENT_FASTCHARGE_EMAIL_MERCHANT == 'True' ? 'TRUE': 'FALSE',







                'x_invoice_num'         => $order_id);







            







                if (MODULE_PAYMENT_FASTCHARGE_TESTMODE == 'Test') {







                    //$url = 'https://secure.authorize.net/gateway/transact.dll';







                    $url = 'https://trans.secure-fastcharge.com/cgi-bin/authorize.cgi';







                } else {







                    $url = 'https://trans.secure-fastcharge.com/cgi-bin/authorize.cgi';







                }







                $data = "";







                foreach( $submit_data as $key => $value )







                        { $data .= "$key=" . urlencode( $value ) . "&"; }







                $data = rtrim( $data, "& " );







                //print_r($data);







                $request = curl_init($url);







                curl_setopt($request, CURLOPT_HEADER, 0);







                curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);







                curl_setopt($request, CURLOPT_POSTFIELDS, $data);







                curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);







                $response = curl_exec($request);







                curl_close ($request);







                $response_array = explode($submit_data["x_delim_char"],$response);







                //print_r($response);







                //exit();







                if($response_array[0] == 1 ){







                    //$append_comment = 'Authorization code: '.$response_array[6];







                    //$nen_transaction_comment = 'Capture Details: '."\n".$append_comment."\n". $response."\n".' Authorize Details: '."\n".$transaction_comment;







                    //tep_db_query("update orders set is_capture = '1', transaction_details = '".tep_db_input($nen_transaction_comment)."' where orders_id = '".$order_id."'");







					







					tep_db_query("update orders set is_capture = '1' where orders_id = '".$order_id."'");					







					$debug_details = 'Capture Details: '."\n" . 'Authorization code: '.$response_array[6] ."\n". $response."\n".' Authorize Details: '."\n";







					debug_register_order_authorization_details($order_id, $debug_details);







                    $messageStack->add_session('Payment Captured Successfully', 'success');







                }else{







                    $messageStack->add_session('Payment capture failed', 'warning');







                }







            }







            tep_redirect(tep_href_link('orders.php','action=edit&oID='.$order_id));







        break;







        







    }







    







}















if (($action == 'edit') && isset($HTTP_GET_VARS['oID'])) {







    $oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);















    $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");







    







    $order_exists = true;







    if (!tep_db_num_rows($orders_query)) {







        $order_exists = false;







        $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');







    }







}















include(DIR_WS_CLASSES . 'order.php');







?>



<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">



      



<!-- header //-->



<?php require(DIR_WS_INCLUDES . 'header.php'); ?>



<!-- header_eof //-->







<!-- body //-->







         <section>



         <!-- START Page content-->



         <section class="main-content">



            <h3><?php echo HEADING_TITLE; ?>



               <br>



            </h3>



            <!-- START panel-->



            <div class="panel panel-default">



               <div class="panel-heading"><?php echo HEADING_TITLE; ?>



                  <a href="#" data-perform="panel-dismiss" data-toggle="tooltip" title="Close Panel" class="pull-right">



                     <em class="fa fa-times"></em>



                  </a>



                  <a href="#" data-perform="panel-collapse" data-toggle="tooltip" title="Collapse Panel" class="pull-right">



                     <em class="fa fa-minus"></em>



                  </a>



               </div>



               <!-- START table-responsive-->



               



               <div class="table-responsive">



               <!-- START your table-->



               <table class="table table-bordered table-hover">







                    <?php







                    if (($action == 'edit') && ($order_exists == true)) {







                        $order = new order($oID);







                    ?>







                        <tr>







                            <td>







                                <table class="table table-bordered table-hover">







                                    <tr>







                                        <td colspan="2">







                                        <?php echo HEADING_TITLE . (($order->customer['id']==0)? ' <b>no account!</b>':''); ?>







                                        </td>







                                    </tr>







                                    <tr>







                                        <td align="left"><?php echo "<span style='font-size:18px; color: #000'>Order #" . $_GET['oID']."</span>"; ?><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>







                                        <td align="right">







                                        <?php echo '<a href="' . tep_href_link(FILENAME_ORDERS_EDIT, 'oID=' . $_GET['oID']) . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>' . "<a href=\"javascript:poptastic('invoice.php?oID=" . $_GET["oID"] . "')" . ';">' . tep_image_button('button_invoice.gif', IMAGE_ORDERS_INVOICE) . '</a>' . "<a href=\"javascript:poptastic('packingslip.php?oID=" . $_GET["oID"] . "')" . ';">' . tep_image_button('button_packingslip.gif', IMAGE_ORDERS_PACKINGSLIP) . '</a>' . '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a> '; ?>







                                        </td>







                                    </tr>







                                </table>







                            </td>







                        </tr>







                    <tr>







                        <td>







                            <table class="table table-bordered table-hover">







                                <tr>







                                    <td colspan="3">







                                    <?php //echo tep_draw_separator(); ?>







                                    </td>







                                </tr>







                                <tr>







                                    <td colspan="3">







                                        <b><u>IP Address: <?php echo $order->info['ip_address']; ?></u></b>







                                        </td>







                                </tr>







                                <tr>







                                    <td colspan="3">







                                    Order Placed By: 







                                    <?php







                                    $orders_status_query = tep_db_query("select customer_service_id, is_phone_order from " . TABLE_ORDERS . " where orders_id = '" . $_GET['oID'] . "'");







                                    while ($orders_info = tep_db_fetch_array($orders_status_query)) {







                                        if(($orders_info['is_phone_order'] == 0 || $orders_info['is_phone_order'] == 1) && ($orders_info['customer_service_id'] != "" || $orders_info['customer_service_id'] != NULL)) {







                                            echo $orders_info['customer_service_id'];







                                            if($orders_info['o.is_phone_order'] == 0)







                                                echo " - In Store";







                                            else







                                                echo " - Phone Order";







                                        } else {







                                            echo "Web Order";







                                        }







                                    }















                                    $amazon_order = amazon_manager::get_amazon_order_id($_GET['oID']);







                                    if (!empty($amazon_order)){







                                        echo ' <b>(Amazon order# ' . $amazon_order . ')</b>';







                                    } else {







                                        $ebay_query = tep_db_query("select ebay_order_id from ebay_orders where osc_order_id='" . (int)$orders['orders_id'] . "'");















                                        if (tep_db_num_rows($ebay_query)){







                                            $ebay_info = tep_db_fetch_array($ebay_query);







                                            echo ' <b>(Amazon order# ' . $ebay_info['ebay_order_id'] . ')</b>';







                                        }







                                    }







                                    ?>







                                    </td>







                                </tr>







                                <tr>







                                    <td>







                                        <table class="table table-bordered table-hover">







                                            <tr>







                                                <td>







                                                    <b><?php echo ENTRY_CUSTOMER; ?></b>







                                                </td>







                                                <td>







                                                <?php echo tep_address_format($order->customer['format_id'], $order->customer, 1, '', '<br>'); ?>







                                                </td>







                                            </tr>







                                            <tr>







                                                <td colspan="2">







                                                <?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?>











                                                </td>







                                            </tr>







                                            <tr>







                                                <td>







                                                    <b><?php echo ENTRY_TELEPHONE_NUMBER; ?></b>







                                                </td>







                                                <td>







                                                <?php echo $order->customer['telephone']; ?>







                                                </td>







                                            </tr>







                                            <tr>







                                                <td>







                                                    <b><?php echo ENTRY_EMAIL_ADDRESS; ?></b>







                                                    </td>







                                                    <td>







                                                    <?php echo '<a href="mailto:' . $order->customer['email_address'] . '" style="color: #333"><u>' . $order->customer['email_address'] . '</u></a>'; ?>







                                                    </td>



                                                    </tr>



                                                </table>



                                            </td>



                                            <td>



                                        <table class="table table-bordered table-hover">







                                            <tr>







                                                <td>







                                                    <b><?php echo ENTRY_SHIPPING_ADDRESS; ?></b>







                                                </td>







                                                <td>







                                                <?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br>'); ?>







                                                </td>







                                            </tr>







                                        </table>







                                    </td>







                                    <td>







                                        <table class="table table-bordered table-hover">







                                            <tr>







                                                <td>







                                                    <b><?php echo ENTRY_BILLING_ADDRESS; ?></b>







                                                </td>







                                                <td>







                                                <?php echo tep_address_format($order->billing['format_id'], $order->billing, 1, '', '<br>'); ?>







                                                </td>







                                            </tr>







                                        </table>







                                    </td>







                                </tr>







                            </table>







                        </td>







                    </tr>







                    <tr>







                        <td>







                        <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>







                        </td>







                    </tr>







                    <tr>







                    <td>







                        <table class="table table-bordered table-hover">







                            <tr>







                                <td>







                                    <b><?php echo ENTRY_PAYMENT_METHOD; ?></b>







                                </td>







                                <td>







                                <?php echo $order->info['payment_method']; ?>







                                </td>







                            </tr>







                            <?php 







                            if($order->info['is_capture'] == '0'){







                            ?>







                            <tr>







                                <td>







                                    <b><?php echo 'To capture payment click here:'; ?></b>







                                </td>







                                <td>







                                <?php 















                                if(strpos(strtolower($order->info['payment_method']),'authorize.net') !== false){







                                    echo '<a href="'.tep_href_link('orders.php','action=captureAuthorizeTransaction&oID='.$_GET['oID']).'"><input type="button" name="capture" value="Capture Payment"></a>';







                                }elseif(strpos(strtolower($order->info['payment_method']),'fast charge') !== false){







                                    echo '<a href="'.tep_href_link('orders.php','action=captureFastChargeTransaction&oID='.$_GET['oID']).'"><input type="button" name="capture" value="Capture Payment"></a>';







                                }?>







                                </td>







                            </tr>







                            <?php







                            }







                            ?>







                            <?php







                            if (tep_not_null($order->info['cc_type']) || tep_not_null($order->info['cc_owner']) || tep_not_null($order->info['cc_number'])) {







                            ?>







                            <tr>







                                <td colspan="2">







                                <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>







                                </td>







                            </tr>







                            <tr>







                                <td>







                                <?php echo ENTRY_CREDIT_CARD_TYPE; ?>







                                </td>







                                <td>







                                <?php echo $order->info['cc_type']; ?>







                                </td>







                            </tr>







                            <tr>







                                <td>







                                <?php echo ENTRY_CREDIT_CARD_OWNER; ?>







                                </td>







                                <td>







                                <?php echo $order->info['cc_owner']; ?>







                                </td>







                            </tr>







                            <tr>







                                <td>







                                <?php echo ENTRY_CREDIT_CARD_NUMBER; ?>







                                </td>







                                <td>







                                <?php echo $order->info['cc_number']; ?>







                                </td>







                            </tr>







                            <tr>







                                <td>







                                <?php echo ENTRY_CREDIT_CARD_EXPIRES; ?>







                                </td>







                                <td>







                                <?php echo $order->info['cc_expires']; ?>







                                </td>







                            </tr>







                            <tr>







                                <td>







                                <?php echo 'CVV: '; ?>







                                </td>







                                <td>







                                <?php echo $order->info['cc_cvv']; ?>







                                </td>







                            </tr>







                            <?php







                            }







                            ?>







                        </table>







                    </td>







                </tr>







                <tr>







                    <td>







                    <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>







                    </td>







                </tr>







                <tr>







                    <td>







                        <?php echo tep_draw_form('send_to_obn', FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=send_to_obn'); ?>







                        <table class="table table-bordered table-hover">







                            <tr>







                                <td>







                                <?php echo TABLE_HEADING_PRODUCTS; ?>







                                </td>







                                <td>







                                <?php echo 'Send to OBN'; ?>







                                </td>







                                <td>







                                <?php echo TABLE_HEADING_PRODUCTS_MODEL; ?>







                                </td>







                                <td align="right">







                                <?php echo 'QTY'; ?>







                                </td>







                                <td align="right">







                                <?php echo 'QTY in Stock'; ?>







                                </td>







                                <td align="right">







                                <?php echo TABLE_HEADING_TAX; ?>







                                </td>            







                                <td align="right">







                                <?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?>







                                </td>







                                <td align="right">







                                <?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?>







                                </td>







                                <td align="right">







                                <?php echo TABLE_HEADING_TOTAL_EXCLUDING_TAX; ?>







                                </td>







                                <td align="right">







                                <?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?>







                                </td>







                            </tr>







                            <?php







                            $show_obn_button = 0;







                            for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {







									if (!$show_obn_button && $order->products[$i]['is_xml_feed_product'] && $order->products[$i]['is_ok_for_shipping']=='1'){







										$show_obn_button = 1;







									}















									$ack_resp = '';















									//$sql_ = tep_db_query("select a.response_type, a.response_comments from feeds_orders_obn_to_retailer a inner join feeds_orders_products_retailer_to_obn b on a.retailer_order_feed_id=b.order_feed_id where b.orders_products_id='" . $order->products[$i]['orders_products_id'] . "'");







                                                                        $sql_ = tep_db_query("select a.response_type, a.response_comments from feeds_orders_obn_to_retailer a left join feeds_orders_products_retailer_to_obn b on a.retailer_order_feed_id=b.order_feed_id where b.orders_products_id='" . $order->products[$i]['orders_products_id'] . "'");                                       















									if (tep_db_num_rows($sql_)){







										$info = tep_db_fetch_array($sql_);







										$ack_resp .= '<br>' . $info['response_type'] . ': ' . $info['response_comments'];







									}







		







									echo '          <tr class="dataTableRow">' . "\n" . '            <td class="dataTableContent" valign="top">' . $order->products[$i]['name'];







									if ($order->products[$i]['vendor_name']){







										echo '<br>' . $order->products[$i]['vendor_name'];







									}















									if (isset($order->products[$i]['attributes']) && (sizeof($order->products[$i]['attributes']) > 0)) {







										for ($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j++) {







											echo '<br><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];







											if ($order->products[$i]['attributes'][$j]['price'] != '0') 







												echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';







												







												echo '</i></small></nobr>';







										}







									}















									echo '            </td>' . "\n" . '            <td class="dataTableContent" valign="top">' . (empty($order->products[$i]['is_xml_feed_product']) ? '&nbsp;' : ($order->products[$i]['is_ok_for_shipping']=='1' ? '<input type="checkbox" name="chk_send_to_obn[]" id="chk_send_to_obn[]" value="' . $order->products[$i]['orders_products_id'] . '" ' . (empty($order->products[$i]['sent_to_obn']) ? '' : ' disabled ' ) . ' />' : 'Product Not Eligible for Drop Shipment' ) ) . (empty($order->products[$i]['orders_products_status']) ? '' : ' <br>' . $orders_status_array[$order->products[$i]['orders_products_status']]) . $ack_resp . '</td>' . "\n" . '            <td class="dataTableContent" valign="top" align="center">' . $order->products[$i]['model'] . '</td>' . "\n" . '            <td class="dataTableContent" valign="top" align="right">' . $order->products[$i]['qty'] . '</td>' . "\n" . '            <td class="dataTableContent" valign="top" align="right">' . get_qty_in_stock($order->products[$i]['orders_products_id']) . '</td>' . "\n" . '            <td class="dataTableContent" align="right" valign="top">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n" . '            <td class="dataTableContent" align="right" valign="top"><b>' . $currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" . '            <td class="dataTableContent" align="right" valign="top"><b>' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" . '            <td class="dataTableContent" align="right" valign="top"><b>' . $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" . '            <td class="dataTableContent" align="right" valign="top"><b>' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n";







									echo '          </tr>' . "\n";







                            }















                            if ($show_obn_button){





                                echo '<tr> <td>&nbsp;</td> <td colspan="3"><a href="' . tep_href_link(FILENAME_MANAGE_SHIPPING_LABEL, tep_get_all_get_params()) . '">' . tep_image_button('button_manage_shipping_label.gif', 'Manage shipping label') . '</a> &nbsp;</td>  <td colspan="7"><input type="submit" value="Send Selected Items to OBN" onclick="javascript:return validate_selection(this.form);" />';







                            }



							



							 



							if(tep_db_num_rows(tep_db_query("select 1 from orders_total join orders USING(orders_id) where class = 'ot_avatax' and orders_total.orders_id = '".tep_db_input($oID)."' and orders.avalara_tax_commited = '-1'"))){ 



								echo '<input type="button" value="Avalara Tax Voided" disabled />';



							}else if(tep_db_num_rows(tep_db_query("select 1 from orders_total join orders USING(orders_id) where class = 'ot_avatax' and orders_total.orders_id = '".tep_db_input($oID)."' and orders.avalara_tax_commited = '1'"))){



								echo '<input type="button" value="Avalara Tax Commited" disabled/>';



							}?>



                            



                             <script type="text/javascript">



                                    function OpenAlavarapopup(){



										window.open('commit_to_alavara.php?mode=commit&oID=<?php echo tep_db_input($oID); ?>','1459835701765',"width=400,height=100,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=500,top=200");



										return false;



									}



									function OpenAlavaraCancelpopup(){



										window.open('commit_to_alavara.php?mode=cancel&oID=<?php echo tep_db_input($oID); ?>','1459835701765',"width=400,height=100,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=500,top=200");



										return false;



									}



                                    </script>



                            



                            </td> </tr>







                            <tr>







                                <td align="right" colspan="10">







                                    <table class="table table-bordered table-hover">







                                    <?php







                                    for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++) {







                                        echo '              <tr>' . "\n" . '                <td align="right">' . $order->totals[$i]['title'] . '</td>' . "\n" . '                <td align="right">' . $order->totals[$i]['text'] . '</td>' . "\n" . '              </tr>' . "\n";







                                    }







                                    ?>







                                    </table>







                                    </form>







                                </td>







                            </tr>







                        </table>







                    </td>







                </tr>







                <tr>







                    <td>







                    <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>







                    </td>







                </tr>







                <!-- html for ffl licensee #start -->



                <?php



					$licensee_count = 1;



					$query_ffl_query = tep_db_query("select vendors_id,ffl_licensee from orders_shipping where orders_id = '".(int)$_GET['oID']."' and (ffl_licensee <> '' and ffl_licensee <> '0')");



				if(tep_db_num_rows($query_ffl_query)){ ?>



                <tr>



                  <td>



						<?php	



						echo TEXT_SELECTED_FFL;



                		while($ffl_licensee = tep_db_fetch_array($query_ffl_query)){ ?>



								<div style="font-weight:bold;"> <?php echo $licensee_count++ . '. ('. getVendorDetails($ffl_licensee['vendors_id']) . ' )' .getFFLDealerDetails($ffl_licensee['ffl_licensee']); ?></div>



                        <?php 



						} ?>



                        



                </td>



                    </tr>



				<?php



				}



				?>



                



                <tr>



                    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>



                </tr>



                <!-- html for ffl licensee #ends -->



                



                



                <tr>



                    <td>



                        <table class="table table-bordered table-hover">







                            <tr>







                                <td align="center"><b><?php echo TABLE_HEADING_DATE_ADDED; ?></b></td>







                                <td align="center"><b><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></b></td>







                                <td align="center"><b><?php echo TABLE_HEADING_STATUS; ?></b></td>







                                <td align="center"><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td>







                            </tr>







                            <?php







                            $orders_history_query = tep_db_query("select orders_status_id, date_added, customer_notified, comments from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($oID) . "' order by date_added");







                            if (tep_db_num_rows($orders_history_query)) {







                                while ($orders_history = tep_db_fetch_array($orders_history_query)) {







                                    echo '          <tr>' . "\n" .







                                    '            <td align="center">' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n" .







                                    '            <td align="center">';







                                







                                    if ($orders_history['customer_notified'] == '1') {







                                        echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK) . "</td>\n";







                                    } else {







                                        echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS) . "</td>\n";







                                    }















                                    echo '            <td>' . $orders_status_array[$orders_history['orders_status_id']] . '</td>' . "\n" .







                                    '            <td>' . nl2br(tep_db_output($orders_history['comments'])) . '&nbsp;</td>' . "\n" .







                                    '          </tr>' . "\n";







                                }







                            } else {







                                echo '          <tr>' . "\n" .







                                '            <td colspan="5">' . TEXT_NO_ORDER_HISTORY . '</td>' . "\n" .







                                '          </tr>' . "\n";







                            }







                            ?>







                        </table>



                    </td>



                </tr>







                <tr>







                    <td>







                    <br><b>&nbsp;<?php echo TABLE_HEADING_COMMENTS; ?></b>







                    </td>







                </tr>







                <tr>







                    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>







                </tr>







                <tr>







                <?php 







                echo tep_draw_form('status', FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=update_order'); 







                ?>







                    <td>&nbsp;<?php echo tep_draw_textarea_field('comments', 'soft', '60', '5'); ?></td>







                </tr>







                <tr>







                    <td><?php //echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>







                </tr>







                <!-- Package Tracking Plus BEGIN -->







				<?php 







				if (is_multiple_vendor_order($_GET['oID']) ){







				?>







				<tr>







					<td>







						<table class="table table-bordered table-hover">







							<tr>







				<?php







					$vendors = get_vendors_by_order($_GET['oID']);







					$start = true;







					//echo '<pre>';







					//print_r($order);







					foreach($vendors as $vendor){







				?>







								<tr>







                                <td align="left">







                                  <?php







								  	if($start){







										echo '<b>'.TABLE_HEADING_OBN_TRACKING.'</b>'.$order->info['extra_track_num'].'<br/>';







										$start = false;







									}?>







                                  







                                  







                                  <!-- ups tracking details starts -->







                                    <?php 







									$query_ups_shipping_query = tep_db_query("select shipping_method from orders_shipping where vendors_id = '".$vendor['id']."' and orders_id = '".$_GET['oID']."' and shipping_module = 'upsxml'");















									if(tep_db_num_rows($query_ups_shipping_query)){ 







										echo '<b>'.$vendor['name'].'--'.TABLE_HEADING_UPS_TRACKING.': </b>'.tep_draw_textbox_field('ups_track_num_' . $vendor['id'], '40', '40', '', $vendor['ups_tracking']); ?> &nbsp; <a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=<?php echo $vendor['ups_tracking']; ?>&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package"> <?php echo tep_image_button('button_track.gif', 'Track Package'); ?> </a> &nbsp;







										<?php if(file_exists(DIR_FS_ADMIN.'shipping_labels/'.$_GET['oID'].'_'.$vendor['id'].'_ups_label.gif')){ ?>







                                        <a href="<?php echo 'shipping_labels/'.$_GET['oID'].'_'.$vendor['id'].'_ups_label.gif'; ?>" style="color:black;" target="_blank">Get Label</a>







                                        <?php }else{ ?>







                                        <a href="display_ups_label.php?oID=<?php echo $_GET['oID'] . '&vendor_id=' . $vendor['id']; ?>" target="_blank" id="fedexGetLabel" style="color:black;">Get Label</a>







                                        <?php } ?>







									<?php







                                    }?>







                                    







									<!-- ups tracking details ends -->











                                 <!-- usps tracking details starts -->







                                 <?php







                                 $query_usps_shipping_query = tep_db_query("select shipping_method from orders_shipping where vendors_id = '".$vendor['id']."' and orders_id = '".$_GET['oID']."' and shipping_module = 'usps'");







									if(tep_db_num_rows($query_usps_shipping_query)){ 







										echo '<b>'.$vendor['name'].'--'.TABLE_HEADING_USPS_TRACKING.': </b>'.tep_draw_textbox_field('usps_track_num_' . $vendor['id'], '40', '40', '', $vendor['usps_tracking']); ?> &nbsp; <a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=<?php echo $vendor['usps_tracking']; ?>"><?php echo tep_image_button('button_track.gif', 'Track Package'); ?></a>







                                        <?php if(file_exists(DIR_FS_ADMIN.'shipping_labels/'.$_GET['oID'].'_'.$vendor['id'].'_usps_label.pdf')){ ?>







                                        	<a href="<?php echo 'shipping_labels/'.$_GET['oID'].'_'.$vendor['id'].'_usps_label.pdf'; ?>" style="color:black;" target="_blank">Get Label</a>







									<?php







										} else{?>







											<a href="display_usps_label.php?oID=<?php echo $_GET['oID'] . '&vendor_id=' . $vendor['id'] ; ?>" target="_blank" id="uspsGetLabel" style="color:black;">Get Label</a>







										<?php







                                        }







									} ?>







								 <!-- usps tracking details starts -->	







                                    







                                     <!-- fedex tracking details starts -->







                                     <?php







                                     $query_fedex_shipping_query = tep_db_query("select shipping_method from orders_shipping where vendors_id = '".$vendor['id']."' and orders_id = '".$_GET['oID']."' and shipping_module = 'fedexwebservices'");







										if(tep_db_num_rows($query_fedex_shipping_query)){ 







                                    		echo '<b>'.TABLE_HEADING_FEDEX_TRACKING.'('.$vendor['name'].'):'. tep_draw_textbox_field('fedex_track_num_' . $vendor['id'], '40', '40', '', $vendor['fedex_tracking']); ?>







											







                                            <?php if(file_exists(DIR_FS_ADMIN.'shipping_labels/'.$_GET['oID'].'_'.$vendor['id'].'_fedex_label.gif')){ ?>







                                            <a href="<?php echo 'shipping_labels/'.$_GET['oID'].'_'.$vendor['id'].'_fedex_label.gif'; ?>" style="color:black;" target="_blank">Get Label</a>







                                            







											<?php } else{?>







                                            	<a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=<?php echo $vendor['fedex_tracking']; ?>&action=track&language=english&cntry_code=us"><?php echo tep_image_button('button_track.gif', 'Track Package'); ?></a>&nbsp;<a href="fedex_popup_thermal.php?oID=<?php echo $_GET['oID'] . '&vendor_id=' . $vendor['id']; ?>" target="_blank" id="fedexGetLabel" style="color:black;">Get Label</a>







                                            <?php } ?>







                                            







									<br><br>







									<b>Distributor Tracking Number</b><br>







									<?php echo tep_draw_textbox_field('extra_track_num_' . $vendor['id'], '40', '40', '', $vendor['extra_tracking']); ?>







                                    <?php } ?>







					             <!-- fedex tracking details starts -->







                                      







								</td></tr>







				<?php







					}







				?>







							







						</table>







					</td>







				</tr>







				<tr>







                    <td><table class="table table-bordered table-hover">







                <tr>







				<?php







				} else {







				?>







                <tr>







                    <td><table class="table table-bordered table-hover">







                <tr>







                    <td><b><?php echo TABLE_HEADING_USPS_TRACKING; ?></b></td>







                    <td><?php echo tep_draw_textbox_field('usps_track_num', '40', '40', '', $order->info['usps_track_num']); ?></td>







                    <td><a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=<?php echo $order->info['usps_track_num']; ?>"><?php echo tep_image_button('button_track.gif', 'Track Package'); ?></a></td><td><a href="display_usps_label.php?oID=<?php echo $_GET['oID']; ?>" target="_blank" id="uspsGetLabel" style="color:black;">Get Label</a></td>







                </tr>







                <tr>







                    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>







                </tr>







                <tr>







                    <td><b><?php echo TABLE_HEADING_UPS_TRACKING; ?></b></td>







                    <td><?php echo tep_draw_textbox_field('ups_track_num', '40', '40', '', $order->info['ups_track_num']); ?></td>







                    <td><a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=<?php echo $order->info['ups_track_num']; ?>&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package"><?php echo tep_image_button('button_track.gif', 'Track Package'); ?></a></td><td><a href="display_ups_label.php?oID=<?php echo $_GET['oID']; ?>" target="_blank" id="fedexGetLabel" style="color:black;">Get Label</a></td>







                </tr>







                <tr>







                    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>







                </tr>







                <tr>







                    <td><b><?php echo TABLE_HEADING_FEDEX_TRACKING; ?></b></td>







                    <td><?php echo tep_draw_textbox_field('fedex_track_num', '40', '40', '', $order->info['fedex_track_num']); ?></td>







                    <td><a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=<?php echo $order->info['fedex_track_num']; ?>&action=track&language=english&cntry_code=us"><?php echo tep_image_button('button_track.gif', 'Track Package'); ?></a></td><td><a href="fedex_popup_thermal.php?oID=<?php echo $_GET['oID']; ?>" target="_blank" id="fedexGetLabel" style="color:black;">Get Label</a></td>







                </tr>







                <tr>







                    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>







                </tr>







                <tr>







                    <td><b>Distributor Tracking Number</b></td>







                    <td><?php echo tep_draw_textbox_field('extra_track_num', '40', '40', '', $order->info['extra_track_num']); ?></td>







                </tr>







                <tr>







                    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>







                </tr>







				<?php







				}







				?>







            </table>







        </td>







    </tr>







    <!-- Package Tracking Plus END -->

	<?php

	

    $tracking_data = getAllTrackingDetails((int)$HTTP_GET_VARS['oID']);

	

	if(count($tracking_data) > 0){ ?>

	

	  <tr>

        <td><b>Track Packages</b></td>

      </tr>

      <tr>

        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

      </tr>

      

	

	

		

		 <tr>

         	<td>

            	<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">

                <?php

		foreach($tracking_data as $tracking){?>

                

                <tr>

                    <td><?php echo $tracking['title']; ?></td>

                    <td>

                    <a target="_blank" href="<?php echo $tracking['link']; ?>">

                        <img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0">

                    </a>

                    </td>

				</tr>	

                <?php

    } ?>

                </table>

            </td>

         </tr>

         

         

         

	<?php

    

	

    } ?>

    





    <tr>







        <td>







            <table class="table table-bordered table-hover">







                <tr>







                    <td>







                        <table class="table table-bordered table-hover">







                            <tr>







                                <td><b><?php echo ENTRY_STATUS; ?></b> <?php echo tep_draw_pull_down_menu('status', $orders_statuses, $order->info['orders_status']); ?></td>







                            </tr>







                            <tr>







                                <td><b><?php echo ENTRY_NOTIFY_CUSTOMER; ?></b> <?php echo tep_draw_checkbox_field('notify', '', true); ?></td>







                                <td><b><?php echo ENTRY_NOTIFY_COMMENTS; ?></b> <?php echo tep_draw_checkbox_field('notify_comments', '', true); ?></td>







                            </tr>







                        </table>







                    </td>







                    <td><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></td>







                </tr>







                <!-- // Points/Rewards Module V2.1rc2a check_box_bof //-->







                <?php







                if ((USE_POINTS_SYSTEM == 'true') && !tep_not_null(POINTS_AUTO_ON)) {







                    $p_status_query = tep_db_query("select points_status from " . TABLE_CUSTOMERS_POINTS_PENDING . " where points_status = 1 and points_type = 'SP' and orders_id = '" . (int)$oID . "' limit 1");







                    if (tep_db_num_rows($p_status_query)) {







                        echo '<tr><td colspan="2" class="main"><strong>' . ENTRY_NOTIFY_POINTS . '</strong>&nbsp;' . ENTRY_QUE_POINTS . tep_draw_checkbox_field('confirm_points', '', false) . '&nbsp;' . ENTRY_QUE_DEL_POINTS . tep_draw_checkbox_field('delete_points', '', false) . '&nbsp;&nbsp;</td></tr>';







                    }







                }







                ?>







                <!-- // Points/Rewards Module V2.1rc2a check_box_eof //-->







            </table>







        </td>







        </form>







    </tr>







    <tr>







        <td colspan="2" align="right">







		<?php echo '<a href="' . tep_href_link(FILENAME_ORDERS_EDIT, 'oID=' . $_GET['oID']) . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>' .







		"<a href=\"javascript:poptastic('invoice.php?oID=" . $_GET["oID"] . "')" . ';">' . tep_image_button('button_invoice.gif', IMAGE_ORDERS_INVOICE) . '</a>' .







		"<a href=\"javascript:poptastic('packingslip.php?oID=" . $_GET["oID"] . "')" . ';">' . tep_image_button('button_packingslip.gif', IMAGE_ORDERS_PACKINGSLIP) . '</a>' .







		'<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a> '; ?>







        </td>







    </tr>







                    <?php







                    } else {







                    ?>







    <tr>







        <td>







            <table class="table table-bordered table-hover">







                <tr>







                    <td valign="middle"><?php echo HEADING_TITLE. '&nbsp;&nbsp;'.'<a href="' . tep_href_link(FILENAME_CREATE_ORDER) . '">'. tep_image_button('button_create_order.gif', IMAGE_CREATE_ORDER) . '</a>';  ?></td>







                    <td align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>







                    <td align="right">







                        <table class="table table-bordered table-hover">







                            <tr>







                            <?php echo tep_draw_form('orders', FILENAME_ORDERS, '', 'get'); ?>







                                <td align="right"><?php echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('oID', '', 'size="12"') . tep_draw_hidden_field('action', 'edit'); ?></td>







                                </form>







                            </tr>







                            <tr>







                                <td height="3"></td>







                            </tr>







                            <tr>







                            <?php echo tep_draw_form('status', FILENAME_ORDERS, '', 'get'); ?>







                                <td align="right"><?php echo HEADING_TITLE_STATUS . ' ' . tep_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $orders_statuses), '', 'onChange="this.form.submit();"'); ?></td>







                                </form>







                            </tr>







                        </table>







                    </td>







                </tr>







            </table>







        </td>







    </tr>







    <tr>







        <td><table class="table table-bordered table-hover">







            <tr>







                <td valign="top">







                    <table class="table table-bordered table-hover">







                        <tr>







                            <td><?php echo TABLE_HEADING_CUSTOMERS; ?></td>







                            <td align="right">Order Number</td>







                            <td align="right"><?php echo TABLE_HEADING_ORDER_TOTAL; ?></td>







                            <td align="center"><?php echo TABLE_HEADING_DATE_PURCHASED; ?></td>







                            <td align="right"><?php echo TABLE_HEADING_STATUS; ?></td>







                            <td align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>







                        </tr>







                        <?php







                        if (isset($HTTP_GET_VARS['cID'])) {







                            $cID = tep_db_prepare_input($HTTP_GET_VARS['cID']);







                            $orders_query_raw = "select o.orders_id, o.customers_name, o.customers_id, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total, o.is_phone_order, o.customer_service_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" . (int)$cID . "' and o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and ot.class = 'ot_total' order by orders_id DESC";







                        } elseif (isset($HTTP_GET_VARS['status']) && is_numeric($HTTP_GET_VARS['status']) && ($HTTP_GET_VARS['status'] > 0)) {







                            $status = tep_db_prepare_input($HTTP_GET_VARS['status']);







                            $orders_query_raw = "select o.orders_id, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total, o.is_phone_order, o.customer_service_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and s.orders_status_id = '" . (int)$status . "' and ot.class = 'ot_total' order by o.orders_id DESC";







                        } else {







                            $orders_query_raw = "select o.orders_id, o.customers_id,o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total, o.customer_service_id, o.is_phone_order from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and ot.class = 'ot_total' order by o.orders_id DESC";







                        }















                        $orders_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $orders_query_raw, $orders_query_numrows);







                        $orders_query = tep_db_query($orders_query_raw);







                        







                        while ($orders = tep_db_fetch_array($orders_query)) {







                            if ($orders['customers_id']==0) $orders['customers_name'] = '<b>!!</b> ' . $orders['customers_name'];







                            if ((!isset($HTTP_GET_VARS['oID']) || (isset($HTTP_GET_VARS['oID']) && ($HTTP_GET_VARS['oID'] == $orders['orders_id']))) && !isset($oInfo)) {







                                $oInfo = new objectInfo($orders);







                            }















                                if (isset($oInfo) && is_object($oInfo) && ($orders['orders_id'] == $oInfo->orders_id)) {







                                    echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '\'">' . "\n";







                                } else {







                                    echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID')) . 'oID=' . $orders['orders_id']) . '\'">' . "\n";







                                }







                        ?>







                            <td><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $orders['customers_name']; ?></td>







                            <td align="right">







                            <?php 







                            if (amazon_manager::is_amazon_order($orders['orders_id'])){







                                echo tep_image(DIR_WS_IMAGES . 'icons/amazon.png', 'Amazon Order# ' . amazon_manager::get_amazon_order_id($orders['orders_id'])) . '&nbsp;';







                            } else {







                                $ebay_query = tep_db_query("select ebay_order_id from ebay_orders where osc_order_id='" . (int)$orders['orders_id'] . "'");







                                if (tep_db_num_rows($ebay_query)){







                                    $ebay_info = tep_db_fetch_array($ebay_query);







                                    echo tep_image(DIR_WS_IMAGES . 'icons/ebay.png', 'eBay Order# ' . $ebay_info['ebay_order_id'] ) . '&nbsp;';    







                                }







                            }







                            echo $orders['orders_id']; 







                            ?>







                            </td>







                            <td align="right"><?php echo strip_tags($orders['order_total']); ?></td>







                            <td align="center"><?php echo tep_datetime_short($orders['date_purchased']); ?></td>







                            <td align="right"><?php echo $orders['orders_status_name'] . $ack_resp; ?></td>







                            <td align="right"><?php if (isset($oInfo) && is_object($oInfo) && ($orders['orders_id'] == $oInfo->orders_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID')) . 'oID=' . $orders['orders_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>







                        </tr>







                        <?php







                        }







                        ?>







                        <tr>







                            <td colspan="5">







                                <table class="table table-bordered table-hover">







                                    <tr>







                                        <td><?php echo $orders_split->display_count($orders_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>







                                        <td align="right"><?php echo $orders_split->display_links($orders_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'oID', 'action'))); ?></td>







                                    </tr>







                                </table>







                            </td>







                        </tr>







                    </table>







                </td>







                <?php







                $heading = array();







                $contents = array();















                switch ($action) {







                    case 'delete':







                        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ORDER . '</b>');







                        







                        $contents = array('form' => tep_draw_form('orders', FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=deleteconfirm'));







                        $contents[] = array('text' => TEXT_INFO_DELETE_INTRO . '<br><br><b>' . $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname . '</b>');







                        $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('restock') . ' ' . TEXT_INFO_RESTOCK_PRODUCT_QUANTITY);







                        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');







                    break;







                default:







                    if (isset($oInfo) && is_object($oInfo)) {







                        $heading[] = array('text' => '<b>[' . $oInfo->orders_id . ']&nbsp;&nbsp;' . tep_datetime_short($oInfo->date_purchased) . '</b>');







                        







                        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '">' . tep_image_button('button_details.gif', IMAGE_DETAILS) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');







                        $contents[] = array('align' => 'center', 'text' => "<a href=\"javascript:poptastic('invoice.php?oID=" . $_GET["oID"] . "')" . ';">' . tep_image_button('button_invoice.gif', IMAGE_ORDERS_INVOICE) . '</a>' .







                        "<a href=\"javascript:poptastic('packingslip.php?oID=" . $_GET["oID"] . "')" . ';" style="margin:0 2px;">' . tep_image_button('button_packingslip.gif', IMAGE_ORDERS_PACKINGSLIP) . '</a>' .







                        '<a href="' . tep_href_link(FILENAME_ORDERS_EDIT, 'oID=' . $oInfo->orders_id) . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>');







                        $contents[] = array('text' => '<br>' . TEXT_DATE_ORDER_CREATED . ' ' . tep_date_short($oInfo->date_purchased));







                        if (tep_not_null($oInfo->last_modified)) $contents[] = array('text' => TEXT_DATE_ORDER_LAST_MODIFIED . ' ' . tep_date_short($oInfo->last_modified));







                        $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENT_METHOD . ' '  . $oInfo->payment_method);







                    }







                    break;







                }















                if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {







                    echo '            <td width="25%" valign="top">' . "\n";















                    $box = new box;







                    echo $box->infoBox($heading, $contents);







                    echo '            </td>' . "\n";







                }







                ?>







            </tr>







        </table>







    </td>







</tr>







<?php







  }







?>







</table></td>







<!-- body_text_eof //-->







</tr>







</table>



<!-- END your table-->



<!-- body_eof //-->







<!-- footer //-->



<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>



<!-- footer_eof //-->



<script  type="text/javascript">







            var notice_win = '';







            $(document).ready(function(){ 







            <?php







                $like_string = '';







                $is_not_fraud = fraud_prevention_is_negative($_GET['oID']);







                if (!$is_not_fraud){







            ?>







                    notice_win = window.open('edit_orders.php?w=n',"notice","toolbar=no, menubar=no,resizable=no,location=no,directories=no,status=no,width=500,height=500");







            <?php







                }







            ?>







            })







            .click(function(){







                if(notice_win && !notice_win.closed) 







                    notice_win.focus();







            });







        </script>







        <script type="text/javascript">







            function validate_selection(formRef){







                try{







                    var blnResp = false;







                    for (var i=0; i<formRef.elements.length; i++){







                        if (formRef.elements[i].id=='chk_send_to_obn[]' && !formRef.elements[i].disabled  && formRef.elements[i].checked){







                            blnResp = true;







                            break;







                        }







                    }







                    if (!blnResp){







                        alert('Please select item(s) to invoke \'Send to OBN\' functionality');







                    }















                    return blnResp;







                } catch(e){alert(e);}	







            }















            // Added for Pop-up windows for Invoice and Packing Slips - OBN







            var newwindow;







            function poptastic(url) {







                newwindow=window.open(url,'name','height=600px,width=850px');







                if (window.focus) {newwindow.focus()}







            }







        </script>



<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>