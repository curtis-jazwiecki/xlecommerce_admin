<?php
/*
  $Id: orders.php,v 1.112 2003/06/29 22:50:52 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  //BOF:authorization_check
  handle_authorization(basename(__FILE__));
  //EOF:authorization_check

  //tep_db_query("ALTER TABLE `orders_products` ADD `set_for_shipment_export` ENUM( '0', '1') NOT NULL DEFAULT '0'");  
  //$sql = tep_db_query("select max(orders_status_id) as max_id from orders_status");
  //$info = tep_db_fetch_array($sql);
  //echo $info['max_id'];
  //tep_db_query("INSERT INTO orders_status (orders_status_id ,language_id ,orders_status_name) VALUES ('21', '1', 'Order Processed DS')");
  //tep_db_query("update orders set order_exported_status='-1' where date_purchased<'2011-11-29 00:00:00'");
  //tep_db_query("update orders set order_exported_status='0' where date_purchased>='2011-11-29 00:00:00'");
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

  if (tep_not_null($action)) {
    switch ($action) {
      //BOF:shipment_export
      case 'shipment_export':
          $oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);
          $orders_id = $oID;
          $operation_mode = tep_db_prepare_input($HTTP_POST_VARS['operation_mode']);
          if ($operation_mode=='shipment_export'){
            $order_product_ids = tep_db_prepare_input($HTTP_POST_VARS['chk_shipment_export']);		
            if (!empty($order_product_ids)){
                $sql = tep_db_query("select order_exported_status, orders_status from orders where orders_id='" . (int)$orders_id . "'");
		$info = tep_db_fetch_array($sql);
		$order_exported_status = $info['order_exported_status'];
		$order_status = $info['orders_status'];
		switch($order_exported_status){
                    case '0':
                        tep_db_query("update orders_products set set_for_shipment_export='0' where orders_id='" . (int)$orders_id . "'");
			foreach($order_product_ids as $order_product_id){
                            tep_db_query("update orders_products set set_for_shipment_export='1' where orders_products_id='" . (int)$order_product_id . "'");
			}
			tep_db_query("insert into orders_status_history (orders_id, orders_status_id, date_added) values ('" . (int)$orders_id . "', '" . (int)$order_status . "', now())");
			tep_db_query("update orders set orders_status='21' where orders_id='" . (int)$orders_id . "'");
			$messageStack->add_session('Items marked for "Shipment Export"', 'success');
			tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=edit'));
			break;
                    case '1':
                        $messageStack->add('Order already exported. Operation terminated', 'error');
			break;
                    case '-1':
                        $messageStack->add('Order marked as "Skip for Shipment Export". Operation terminated', 'error');
			break;
                    default:
                        $messageStack->add('Error while locating order reference.', 'error');
                }
            }
          }
          break;
      //EOF:shipment_export
      case 'update_order':
        $oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);
        $status = tep_db_prepare_input($HTTP_POST_VARS['status']);
        $comments = tep_db_prepare_input($HTTP_POST_VARS['comments']);
        //Package Tracking Plus BEGIN
        $usps_track_num = tep_db_prepare_input($HTTP_POST_VARS['usps_track_num']);
		$usps_track_num2 = tep_db_prepare_input($HTTP_POST_VARS['usps_track_num2']);
        $ups_track_num = tep_db_prepare_input($HTTP_POST_VARS['ups_track_num']);
		$ups_track_num2 = tep_db_prepare_input($HTTP_POST_VARS['ups_track_num2']);
        $fedex_track_num = tep_db_prepare_input($HTTP_POST_VARS['fedex_track_num']);
		$fedex_track_num2 = tep_db_prepare_input($HTTP_POST_VARS['fedex_track_num2']);
        $dhl_track_num = tep_db_prepare_input($HTTP_POST_VARS['dhl_track_num']);
		$dhl_track_num2 = tep_db_prepare_input($HTTP_POST_VARS['dhl_track_num2']);
//Package Tracking Plus END

        $order_updated = false;
        $check_status_query = tep_db_query("select customers_name, customers_email_address, orders_status, date_purchased,usps_track_num, usps_track_num2, ups_track_num, ups_track_num2, fedex_track_num, fedex_track_num2, dhl_track_num, dhl_track_num2 from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");
        $check_status = tep_db_fetch_array($check_status_query);

        if ( ($check_status['orders_status'] != $status) || tep_not_null($comments)) {
          tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . (int)$oID . "'");
/*
          $customer_notified = '0';
          if (isset($HTTP_POST_VARS['notify']) && ($HTTP_POST_VARS['notify'] == 'on')) {
            $notify_comments = '';
            if (isset($HTTP_POST_VARS['notify_comments']) && ($HTTP_POST_VARS['notify_comments'] == 'on')) {
              $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n\n";
            }

            $email = STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL') . "\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);

            tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

            $customer_notified = '1';
          }**/
          //Package Tracking Plus BEGIN
          $customer_notified = '0';
          if ($HTTP_POST_VARS['notify'] == 'on' & ($usps_track_num == '' & $usps_track_num2 == '' & $ups_track_num == '' & $ups_track_num2 == '' & $fedex_track_num == '' & $fedex_track_num2 == '' & $dhl_track_num == '' & $dhl_track_num2 == '' ) ) {
            $notify_comments = '';
            if ($HTTP_POST_VARS['notify_comments'] == 'on') {
              $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n";
              if ($comments == null)
                $notify_comments = '';
            }

            $email = 'Dear ' . $check_status['customers_name'] . ',' . "\n\n" . STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . "<a HREF='" . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "'>" .  'order_id=' . (int)$oID . "</a>\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n" . EMAIL_SEPARATOR . "\n\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);
            tep_mail($check_status['customers_name'], $check_status['customers_email_address'], STORE_NAME . ' ' . EMAIL_TEXT_SUBJECT_1. (int)$oID . EMAIL_TEXT_SUBJECT_2 . $orders_status_array[$status], $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
            $customer_notified = '1';

          }else if ($HTTP_POST_VARS['notify'] == 'on' & ($usps_track_num == '' or $usps_track_num2 == '' or $ups_track_num == '' or $ups_track_num2 == '' or $fedex_track_num == '' or $fedex_track_num2 == '' or $dhl_track_num == '' or $dhl_track_num2 == '' ) ) {
            $notify_comments = '';
            if ($HTTP_POST_VARS['notify_comments'] == 'on') {
              $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n";
              if ($comments == null)
                $notify_comments = '';
            }
            if ($usps_track_num == null) {
              $usps_text = '';
			  $usps_track = '';
            }else{
              $usps_text = 'USPS(1): ';
              $usps_track_num_noblanks = str_replace(' ', '', $usps_track_num);
              $usps_link = 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=' . $usps_track_num_noblanks;
              $usps_track = '<a target="_blank" href="' . $usps_link . '">' . $usps_track_num . '</a>' . "\n";
            }
            if ($usps_track_num2 == null) {
              $usps_text2 = '';
			  $usps_track2 = '';
            }else{
              $usps_text2 = 'USPS(2): ';
              $usps_track_num2_noblanks = str_replace(' ', '', $usps_track_num2);
              $usps_link2 = 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=' . $usps_track_num2_noblanks;
              $usps_track2 = '<a target="_blank" href="' . $usps_link2 . '">' . $usps_track_num2 . '</a>' . "\n";
            }
            if ($ups_track_num == null) {
              $ups_text = '';
			  $ups_track = '';
            }else{
              $ups_text = 'UPS(1): ';
              $ups_track_num_noblanks = str_replace(' ', '', $ups_track_num);
              $ups_link = 'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . $ups_track_num_noblanks . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package ';
              $ups_track = '<a target="_blank" href="' . $ups_link . '">' . $ups_track_num . '</a>' . "\n";
            }
            if ($ups_track_num2 == null) {
              $ups_text2 = '';
			  $ups_track2 = '';
            }else{
              $ups_text2 = 'UPS(2): ';
              $ups_track_num2_noblanks = str_replace(' ', '', $ups_track_num2);
              $ups_link2 = 'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . $ups_track_num2_noblanks . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package ';
              $ups_track2 = '<a target="_blank" href="' . $ups_link2 . '">' . $ups_track_num2 . '</a>' . "\n";
            }
            if ($fedex_track_num == null) {
              $fedex_text = '';
			  $fedex_track = '';
            }else{
              $fedex_text = 'Fedex(1): ';
              $fedex_track_num_noblanks = str_replace(' ', '', $fedex_track_num);
              $fedex_link = 'http://www.fedex.com/Tracking?tracknumbers=' . $fedex_track_num_noblanks . '&action=track&language=english&cntry_code=us';
              $fedex_track = '<a target="_blank" href="' . $fedex_link . '">' . $fedex_track_num . '</a>' . "\n";
            }
            if ($fedex_track_num2 == null) {
              $fedex_text2 = '';
			  $fedex_track2 = '';
            }else{
              $fedex_text2 = 'Fedex(2): ';
              $fedex_track_num2_noblanks = str_replace(' ', '', $fedex_track_num2);
              $fedex_link2 = 'http://www.fedex.com/Tracking?tracknumbers=' . $fedex_track_num2_noblanks . '&action=track&language=english&cntry_code=us';
              $fedex_track2 = '<a target="_blank" href="' . $fedex_link2 . '">' . $fedex_track_num2 . '</a>' . "\n";
            }
            if ($dhl_track_num == null) {
              $dhl_text = '';
			  $dhl_track = '';
            }else{
              $dhl_text = 'DHL(1): ';
              $dhl_track_num_noblanks = str_replace(' ', '', $dhl_track_num);
              $dhl_link = 'http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=' . $dhl_track_num_noblanks . '&action=track&language=english&cntry_code=us';
              $dhl_track = '<a target="_blank" href="' . $dhl_link . '">' . $dhl_track_num . '</a>' . "\n";
            }
            if ($dhl_track_num2 == null) {
              $dhl_text2 = '';
			  $dhl_track2 = '';
            }else{
              $dhl_text2 = 'DHL(2): ';
              $dhl_track_num2_noblanks = str_replace(' ', '', $dhl_track_num2);
              $dhl_link2 = 'http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=' . $dhl_track_num2_noblanks . '&action=track&language=english&cntry_code=us';
              $dhl_track2 = '<a target="_blank" href="' . $dhl_link2 . '">' . $dhl_track_num2 . '</a>' . "\n";
            }

            $email = 'Dear ' . $check_status['customers_name'] . ',' . "\n\n" . STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . "<a HREF='" . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "'>" .  'order_id=' . (int)$oID . "</a>\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_TRACKING_NUMBER . "\n" . $usps_text . $usps_track . $usps_text2 . $usps_track2 . $ups_text . $ups_track . $ups_text2 . $ups_track2 . $fedex_text . $fedex_track . $fedex_text2 . $fedex_track2 . $dhl_text . $dhl_track . $dhl_text2 . $dhl_track2 . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);
            tep_mail($check_status['customers_name'], $check_status['customers_email_address'], STORE_NAME . ' ' . EMAIL_TEXT_SUBJECT_1. (int)$oID . EMAIL_TEXT_SUBJECT_2 . $orders_status_array[$status], $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
            $customer_notified = '1';

          }else if ($HTTP_POST_VARS['notify'] == 'on' & (tep_not_null($usps_track_num) & tep_not_null($usps_track_num2) & tep_not_null($ups_track_num) & tep_not_null($ups_track_num2) & tep_not_null($fedex_track_num) & tep_not_null($fedex_track_num2) & tep_not_null($dhl_track_num) & tep_not_null($dhl_track_num2) ) ) {
            $notify_comments = '';
            if ($HTTP_POST_VARS['notify_comments'] == 'on') {
              $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n";
              if ($comments == null)
                $notify_comments = '';
            }
            $usps_text = 'USPS(1): ';
            $usps_track_num_noblanks = str_replace(' ', '', $usps_track_num);
            $usps_link = 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=' . $usps_track_num_noblanks;
            $usps_track = '<a target="_blank" href="' . $usps_link . '">' . $usps_track_num . '</a>' . "\n";
            $usps_text2 = 'USPS(2): ';
            $usps_track_num2_noblanks = str_replace(' ', '', $usps_track_num2);
            $usps_link2 = 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=' . $usps_track_num2_noblanks;
            $usps_track2 = '<a target="_blank" href="' . $usps_link2 . '">' . $usps_track_num2 . '</a>' . "\n";
            $ups_text = 'UPS(1): ';
            $ups_track_num_noblanks = str_replace(' ', '', $ups_track_num);
            $ups_link = 'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . $ups_track_num_noblanks . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package ';
            $ups_track = '<a target="_blank" href="' . $ups_link . '">' . $ups_track_num . '</a>' . "\n";
            $ups_text2 = 'UPS(2): ';
            $ups_track_num2_noblanks = str_replace(' ', '', $ups_track_num2);
            $ups_link2 = 'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . $ups_track_num2_noblanks . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package ';
            $ups_track2 = '<a target="_blank" href="' . $ups_link2 . '">' . $ups_track_num2 . '</a>' . "\n";
            $fedex_text = 'Fedex(1): ';
            $fedex_track_num_noblanks = str_replace(' ', '', $fedex_track_num);
            $fedex_link = 'http://www.fedex.com/Tracking?tracknumbers=' . $fedex_track_num_noblanks . '&action=track&language=english&cntry_code=us';
            $fedex_track = '<a target="_blank" href="' . $fedex_link . '">' . $fedex_track_num . '</a>' . "\n";
            $fedex_text2 = 'Fedex(2): ';
            $fedex_track_num2_noblanks = str_replace(' ', '', $fedex_track_num2);
            $fedex_link2 = 'http://www.fedex.com/Tracking?tracknumbers=' . $fedex_track_num2_noblanks . '&action=track&language=english&cntry_code=us';
            $fedex_track2 = '<a target="_blank" href="' . $fedex_link2 . '">' . $fedex_track_num2 . '</a>' . "\n";
            $dhl_text = 'DHL(1): ';
            $dhl_track_num_noblanks = str_replace(' ', '', $dhl_track_num);
            $dhl_link = 'http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=' . $dhl_track_num_noblanks . '&action=track&language=english&cntry_code=us';
            $dhl_track = '<a target="_blank" href="' . $dhl_link . '">' . $dhl_track_num . '</a>' . "\n";
            $dhl_text2 = 'DHL(2): ';
            $dhl_track_num2_noblanks = str_replace(' ', '', $dhl_track_num2);
            $dhl_link2 = 'http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=' . $dhl_track_num2_noblanks . '&action=track&language=english&cntry_code=us';
            $dhl_track2 = '<a target="_blank" href="' . $dhl_link2 . '">' . $dhl_track_num2 . '</a>' . "\n";

            $email = 'Dear ' . $check_status['customers_name'] . ',' . "\n\n" . STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . "<a HREF='" . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "'>" .  'order_id=' . (int)$oID . "</a>\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_TRACKING_NUMBER . "\n" . $usps_text . $usps_track . $usps_text2 . $usps_track2 . $ups_text . $ups_track . $ups_text2 . $ups_track2 . $fedex_text . $fedex_track . $fedex_text2 . $fedex_track2 . $dhl_text . $dhl_track . $dhl_text2 . $dhl_track2 . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);
            tep_mail($check_status['customers_name'], $check_status['customers_email_address'], STORE_NAME . ' ' . EMAIL_TEXT_SUBJECT_1 . (int)$oID . EMAIL_TEXT_SUBJECT_2 . $orders_status_array[$status], $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
            $customer_notified = '1';
          }
//Package Tracking Plus END
######## Points/Rewards Module BOF ##################

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
######## Points/Rewards Module EOF ##################
          tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . (int)$oID . "', '" . tep_db_input($status) . "', now(), '" . tep_db_input($customer_notified) . "', '" . tep_db_input($comments)  . "')");

          $order_updated = true;
          
		  }
		  //Package Tracking Plus BEGIN
        tep_db_query("update " . TABLE_ORDERS . " set usps_track_num = '" . tep_db_input($usps_track_num) . "' where orders_id = '" . tep_db_input($oID) . "'");
        tep_db_query("update " . TABLE_ORDERS . " set usps_track_num2 = '" . tep_db_input($usps_track_num2) . "' where orders_id = '" . tep_db_input($oID) . "'");
        tep_db_query("update " . TABLE_ORDERS . " set ups_track_num = '" . tep_db_input($ups_track_num) . "' where orders_id = '" . tep_db_input($oID) . "'");
        tep_db_query("update " . TABLE_ORDERS . " set ups_track_num2 = '" . tep_db_input($ups_track_num2) . "' where orders_id = '" . tep_db_input($oID) . "'");
        tep_db_query("update " . TABLE_ORDERS . " set fedex_track_num = '" . tep_db_input($fedex_track_num) . "' where orders_id = '" . tep_db_input($oID) . "'");
        tep_db_query("update " . TABLE_ORDERS . " set fedex_track_num2 = '" . tep_db_input($fedex_track_num2) . "' where orders_id = '" . tep_db_input($oID) . "'");
        tep_db_query("update " . TABLE_ORDERS . " set dhl_track_num = '" . tep_db_input($dhl_track_num) . "' where orders_id = '" . tep_db_input($oID) . "'");
        tep_db_query("update " . TABLE_ORDERS . " set dhl_track_num2 = '" . tep_db_input($dhl_track_num2) . "' where orders_id = '" . tep_db_input($oID) . "'");
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
    }
  }

  if (($action == 'edit') && isset($HTTP_GET_VARS['oID']) && $HTTP_GET_VARS['cname'] == '') {
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
<script type="text/javascript">
//BOF:shipment_export
function validate_shipment_export_selection(formRef){
	try{
		var blnResp = false;
		for (var i=0; i<formRef.elements.length; i++){
			if (formRef.elements[i].id=='chk_shipment_export[]' && !formRef.elements[i].disabled  && formRef.elements[i].checked){
				blnResp = true;
				break;
			}
		}
		if (!blnResp){
			alert('Please select item(s) for shipment export');
		} else {
			formRef.operation_mode.value='shipment_export';
		}
		return blnResp;
	} catch(e){alert(e);}	
}
//EOF:shipment_export
</script>
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
  <tr>
    <td><table class="table table-bordered table-hover">
<!-- left_navigation //-->
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td><table class="table table-bordered table-hover">
<?php
  if (($action == 'edit') && ($order_exists == true)) {
    $order = new order($oID);
?>
      <tr>
        <td><table class="table table-bordered table-hover">
          <tr>
            <td><?php echo HEADING_TITLE; ?></td>
            <td align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
          <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS_EDIT, 'oID=' . $_GET['oID']) . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS_INVOICE, 'oID=' . $_GET['oID']) . '" TARGET="_blank">' . tep_image_button('button_invoice.gif', IMAGE_ORDERS_INVOICE) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS_PACKINGSLIP, 'oID=' . $_GET['oID']) . '" TARGET="_blank">' . tep_image_button('button_packingslip.gif', IMAGE_ORDERS_PACKINGSLIP) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a> '; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table class="table table-bordered table-hover">
          <tr>
            <td colspan="3"><?php echo tep_draw_separator(); ?></td>
          </tr>
          <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td valign="top"><b><?php echo ENTRY_CUSTOMER; ?></b></td>
                <td><?php echo tep_address_format($order->customer['format_id'], $order->customer, 1, '', '<br>'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
              </tr>
              <tr>
                <td><b><?php echo ENTRY_TELEPHONE_NUMBER; ?></b></td>
                <td><?php echo $order->customer['telephone']; ?></td>
              </tr>
              <tr>
                <td><b><?php echo ENTRY_EMAIL_ADDRESS; ?></b></td>
                <td><?php echo '<a href="mailto:' . $order->customer['email_address'] . '"><u>' . $order->customer['email_address'] . '</u></a>'; ?></td>
              </tr>
            </table></td>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td><b><?php echo ENTRY_SHIPPING_ADDRESS; ?></b></td>
                <td><?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br>'); ?></td>
              </tr>
            </table></td>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td valign="top"><b><?php echo ENTRY_BILLING_ADDRESS; ?></b></td>
                <td><?php echo tep_address_format($order->billing['format_id'], $order->billing, 1, '', '<br>'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table class="table table-bordered table-hover">
          <tr>
            <td><b><?php echo ENTRY_PAYMENT_METHOD; ?></b></td>
            <td><?php echo $order->info['payment_method']; ?></td>
          </tr>
<?php
    if (tep_not_null($order->info['cc_type']) || tep_not_null($order->info['cc_owner']) || tep_not_null($order->info['cc_number'])) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td><?php echo ENTRY_CREDIT_CARD_TYPE; ?></td>
            <td><?php echo $order->info['cc_type']; ?></td>
          </tr>
          <tr>
            <td><?php echo ENTRY_CREDIT_CARD_OWNER; ?></td>
            <td><?php echo $order->info['cc_owner']; ?></td>
          </tr>
          <tr>
            <td><?php echo ENTRY_CREDIT_CARD_NUMBER; ?></td>
            <td><?php echo $order->info['cc_number']; ?></td>
          </tr>
          <tr>
            <td><?php echo ENTRY_CREDIT_CARD_EXPIRES; ?></td>
            <td><?php echo $order->info['cc_expires']; ?></td>
          </tr>
<?php
    }
?>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td>
            <?php 
            //BOF:shipment_export
            echo tep_draw_form('shipment_export', FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=shipment_export'); 
            //EOF:shipment_export
            ?>
          <table class="table table-bordered table-hover">
          <tr>
            <td colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
            <!--BOF:shipment_export -->
            <td><?php echo 'Shipment Export'; ?></td>
            <!--EOF:shipment_export -->
            <td><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
            <td align="right"><?php echo TABLE_HEADING_TAX; ?></td>
            <td align="right"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></td>
            <td align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
            <td align="right"><?php echo TABLE_HEADING_TOTAL_EXCLUDING_TAX; ?></td>
            <td align="right"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
		$modelnumber = $order->products[$i]['model'];
		$wpcheck = substr($modelnumber, 0, 2);
		if ($wpcheck == 'WP' || $wpcheck=='wp') {
		
		$modelnum = substr($modelnumber, 2);
		$ch = curl_init('https://www.wpsorders.com/wpsonline/u1ITEM1.pgm?DEALER=D2040546&ITEM=' . $modelnum . '&OPTION=stock');
		curl_setopt($ch, CURLOPT_NOBODY, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);
		$num_search = "SS" . $modelnumber;

		$product_quantity_query = tep_db_query("SELECT products_quantity FROM " . TABLE_PRODUCTS . " WHERE products_model = '" . $num_search . "'");
		
		while ($order_quantity = tep_db_fetch_array($product_quantity_query)) {
    	$quantity[] = array('qty' => $order_quantity['products_quantity'],
							'model' => $order_quantity['products_model']);
		
		  }
   		$qty = $quantity[0]['qty'];
		if ($qty == NULL) $qty = 0;

		$stock_numbers = explode("|", $response);
		}
		
		
      echo '          <tr>' . "\n" .
           '            <td valign="top" align="right">' . $order->products[$i]['qty'] . '&nbsp;x</td>' . "\n" .
           '            <td valign="top">' . $order->products[$i]['name'];

      if (isset($order->products[$i]['attributes']) && (sizeof($order->products[$i]['attributes']) > 0)) {
        for ($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j++) {
          echo '<br><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
          if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
          echo '</i></small></nobr>';
        }
      }
      
      //BOF:shipment_export
      $item_export_status_query = tep_db_query("select set_for_shipment_export from orders_products where orders_products_id='" . (int)$order->products[$i]['orders_products_id'] . "'");
      $info = tep_db_fetch_array($item_export_status_query);
      $item_export_status = $info['set_for_shipment_export'];
      //EOF:shipment_export

      echo '            </td>' . "\n" .
		   //BOF:shipment_export
		   '<td><input type="checkbox" name="chk_shipment_export[]" id="chk_shipment_export[]" value="' . $order->products[$i]['orders_products_id'] . '" ' . ($item_export_status=='1' ? ' checked ' : '') . ' /></td>' .
		   //EOF:shipment_export
           '            <td>' . $order->products[$i]['model'] . '</td>' . "\n" .
           '            <td align="right">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n" .
           '            <td align="right"><b>' . $currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" .
           '            <td align="right"><b>' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" .
           '            <td align="right"><b>' . $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" .
           '            <td align="right"><b>' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n";
      echo '          </tr>' . "\n";
	  
	  if ($wpcheck == 'WP' || $wpcheck=='wp') {
	  echo '<tr>';
	  echo '<td align="right" valign="top"></td>';
	  echo '<td align="left" valign="top" colspan="8">Model No.:' . $stock_numbers[0] . ' | Jazz: ' . $qty  . ' | ID:' . $stock_numbers[1] . ' | CA:' . $stock_numbers[2] . ' | TN:' . $stock_numbers[3] . ' | PA:' . $stock_numbers[4] . ' | IN:' . $stock_numbers[5] . '</td>';
	  echo '</tr>';
	  }
    }
          //BOF:shipment_export
         echo '<tr>
             <td colspan=2"></td>
             <td align="left" colspan="7">
                <input type="submit" value="Mark Items for Shipment Export" onclick="javascript:return validate_shipment_export_selection(this.form);" />
		<input type="hidden" name="operation_mode" id="operation_mode" value="" />
              </td></tr>';
          //EOF:shipment_export
?>
          <tr>
            <td align="right" colspan="9"><table border="0" cellspacing="0" cellpadding="2">
<?php
    for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++) {
      echo '              <tr>' . "\n" .
           '                <td align="right">' . $order->totals[$i]['title'] . '</td>' . "\n" .
           '                <td align="right">' . $order->totals[$i]['text'] . '</td>' . "\n" .
           '              </tr>' . "\n";
    }
?>
            </table></td>
          </tr>
        </table>
            <!--BOF:shipment_export-->
            </form>
            <!--EOF:shipment_export-->            
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><table border="1" cellspacing="0" cellpadding="5">
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
        </table></td>
      </tr>
      <tr>
        <td><br><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
      </tr>
      <tr><?php echo tep_draw_form('status', FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=update_order'); ?>
        <td><?php echo tep_draw_textarea_field('comments', 'soft', '60', '5'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <!-- Package Tracking Plus BEGIN -->
	  <tr>
	    <td><table class="table table-bordered table-hover">
		  <tr>
            <td><b><?php echo TABLE_HEADING_USPS_TRACKING; ?></b></td>
			<td><?php echo tep_draw_textbox_field('usps_track_num', '40', '40', '', $order->info['usps_track_num']); ?></td>
			<td><a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=<?php echo $order->info['usps_track_num']; ?>"><?php echo tep_image_button('button_track.gif', 'Track Package'); ?></a></td>
			<td><?php echo tep_draw_textbox_field('usps_track_num2', '40', '40', '', $order->info['usps_track_num2']); ?></td>
			<td><a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=<?php echo $order->info['usps_track_num2']; ?>"><?php echo tep_image_button('button_track.gif', 'Track Package'); ?></a></td>
          </tr>
          <tr>
            <td colspan="5"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td><b><?php echo TABLE_HEADING_UPS_TRACKING; ?></b></td>
			<td><?php echo tep_draw_textbox_field('ups_track_num', '40', '40', '', $order->info['ups_track_num']); ?></td>
			<td><a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=<?php echo $order->info['ups_track_num']; ?>&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package"><?php echo tep_image_button('button_track.gif', 'Track Package'); ?></a></td>
			<td><?php echo tep_draw_textbox_field('ups_track_num2', '40', '40', '', $order->info['ups_track_num2']); ?></td>
			<td><a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=<?php echo $order->info['ups_track_num2']; ?>&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package"><?php echo tep_image_button('button_track.gif', 'Track Package'); ?></a></td>
          </tr>
          <tr>
            <td colspan="5"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td><b><?php echo TABLE_HEADING_FEDEX_TRACKING; ?></b></td>
			<td><?php echo tep_draw_textbox_field('fedex_track_num', '40', '40', '', $order->info['fedex_track_num']); ?></td>
			<td><a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=<?php echo $order->info['fedex_track_num']; ?>&action=track&language=english&cntry_code=us"><?php echo tep_image_button('button_track.gif', 'Track Package'); ?></a></td>
			<td><?php echo tep_draw_textbox_field('fedex_track_num2', '40', '40', '', $order->info['fedex_track_num2']); ?></td>
			<td><a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=<?php echo $order->info['fedex_track_num2']; ?>&action=track&language=english&cntry_code=us"><?php echo tep_image_button('button_track.gif', 'Track Package'); ?></a></td>
          </tr>
          <tr>
            <td colspan="5"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td><b><?php echo TABLE_HEADING_DHL_TRACKING; ?></b></td>
			<td><?php echo tep_draw_textbox_field('dhl_track_num', '40', '40', '', $order->info['dhl_track_num']); ?></td>
			<td><a target="_blank" href="http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=<?php echo $order->info['dhl_track_num']; ?>&action=track&language=english&cntry_code=us"><?php echo tep_image_button('button_track.gif', 'Track Package'); ?></a></td>
			<td><?php echo tep_draw_textbox_field('dhl_track_num2', '40', '40', '', $order->info['dhl_track_num2']); ?></td>
			<td><a target="_blank" href="http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=<?php echo $order->info['dhl_track_num2']; ?>&action=track&language=english&cntry_code=us"><?php echo tep_image_button('button_track.gif', 'Track Package'); ?></a></td>
          </tr>
          <tr>
            <td colspan="5"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
		</table></td>
	  </tr>
<!-- Package Tracking Plus END -->

      <tr>
        <td><table class="table table-bordered table-hover">
          <tr>
            <td><table class="table table-bordered table-hover">
              <tr>
                <td><b><?php echo ENTRY_STATUS; ?></b> <?php echo tep_draw_pull_down_menu('status', $orders_statuses, $order->info['orders_status']); ?></td>
              </tr>
              <tr>
                <td><b><?php echo ENTRY_NOTIFY_CUSTOMER; ?></b> <?php echo tep_draw_checkbox_field('notify', '', true); ?></td>
                <td><b><?php echo ENTRY_NOTIFY_COMMENTS; ?></b> <?php echo tep_draw_checkbox_field('notify_comments', '', true); ?></td>
              </tr>
            </table></td>
            <td><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></td>
            <?php
    if ((USE_POINTS_SYSTEM == 'true') && !tep_not_null(POINTS_AUTO_ON)) {
	    $p_status_query = tep_db_query("select points_status from " . TABLE_CUSTOMERS_POINTS_PENDING . " where points_status = 1 and points_type = 'SP' and orders_id = '" . (int)$oID . "' limit 1");
	    if (tep_db_num_rows($p_status_query)) {
		    echo '<tr><td class="main"><strong>' . ENTRY_NOTIFY_POINTS . '</strong>&nbsp;' . ENTRY_QUE_POINTS . tep_draw_checkbox_field('confirm_points', '', false) . '&nbsp;' . ENTRY_QUE_DEL_POINTS . tep_draw_checkbox_field('delete_points', '', false) . '&nbsp;&nbsp;</td></tr>';
	    }
    }
?>
          </tr>
        </table></td>
      </form></tr>
      <tr>
        <td colspan="2" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS_EDIT, 'oID=' . $_GET['oID']) . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS_INVOICE, 'oID=' . $HTTP_GET_VARS['oID']) . '" TARGET="_blank">' . tep_image_button('button_invoice.gif', IMAGE_ORDERS_INVOICE) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS_PACKINGSLIP, 'oID=' . $HTTP_GET_VARS['oID']) . '" TARGET="_blank">' . tep_image_button('button_packingslip.gif', IMAGE_ORDERS_PACKINGSLIP) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
      </tr>
<?php
  } else {
?>
      <tr>
        <td><table class="table table-bordered table-hover">
          <tr>
            <?php /*<td class="pageHeading"><?php echo HEADING_TITLE. '<a href="' . tep_href_link(FILENAME_CREATE_ORDER) . '">'. tep_image_button('button_create_order.gif', IMAGE_CREATE_ORDER) . '</a>';  ?></td> */ ?>
			<td><?php echo '<a href="' . tep_href_link('create_order_process_POS.php') . '">'. tep_image_button('button_create_order.gif', IMAGE_CREATE_ORDER) . '</a>';  ?></td>
            <td align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td align="right"><table class="table table-bordered table-hover">
              <tr><?php echo tep_draw_form('orders', FILENAME_ORDERS, '', 'get'); ?>
                <td align="right"><?php echo 'Customer Name: ' .  tep_draw_input_field('cname') . tep_draw_separator('pixel_trans.gif', 10, 1) .  HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('oID', '', 'size="12"') . tep_draw_hidden_field('action', 'edit') . tep_draw_separator('pixel_trans.gif', 10, 1) .tep_image_submit('button_search.gif', 'Search'); ?></td>
              </form></tr>
              <tr><?php echo tep_draw_form('status', FILENAME_ORDERS, '', 'get'); ?>
                <td align="right"><?php echo HEADING_TITLE_STATUS . ' ' . tep_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $orders_statuses), '', 'onChange="this.form.submit();"'); ?></td>
              </form></tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table class="table table-bordered table-hover">
          <tr>
            <td><table class="table table-bordered table-hover">
              <tr>
                <td><?php echo TABLE_HEADING_CUSTOMERS; ?></td>
                <td align="right"><?php echo TABLE_HEADING_ORDER_TOTAL; ?></td>
                <td align="center"><?php echo TABLE_HEADING_DATE_PURCHASED; ?></td>
                <td align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    if (isset($HTTP_GET_VARS['cID'])) {
      $cID = tep_db_prepare_input($HTTP_GET_VARS['cID']);
      $orders_query_raw = "select o.orders_id, o.customers_name, o.customers_id, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value,  o.customer_service_id, s.orders_status_name, ot.text as order_total from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" . (int)$cID . "' and o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and ot.class = 'ot_total' order by orders_id DESC";
    } elseif (isset($HTTP_GET_VARS['status']) && is_numeric($HTTP_GET_VARS['status']) && ($HTTP_GET_VARS['status'] > 0)) {
      $status = tep_db_prepare_input($HTTP_GET_VARS['status']);
      $orders_query_raw = "select o.orders_id, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name,  o.customer_service_id, ot.text as order_total from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and s.orders_status_id = '" . (int)$status . "' and ot.class = 'ot_total' order by o.orders_id DESC";
       } elseif (isset($HTTP_GET_VARS['cname']) && ($HTTP_GET_VARS['cname'] != '')) {
      $cname = tep_db_prepare_input($HTTP_GET_VARS['cname']);
      $orders_query_raw = "select o.orders_id, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name,  o.customer_service_id, ot.text as order_total from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and o.customers_name like CONVERT( _utf8 '%" . $cname . "%' USING latin1 ) and ot.class = 'ot_total' order by o.orders_id DESC";
    } else {
      $orders_query_raw = "select o.orders_id, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, o.customer_service_id,  ot.text as order_total from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and ot.class = 'ot_total' order by o.orders_id DESC";
    }
    

    $orders_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $orders_query_raw, $orders_query_numrows);
    $orders_query = tep_db_query($orders_query_raw);
    while ($orders = tep_db_fetch_array($orders_query)) {
    if ((!isset($HTTP_GET_VARS['oID']) || (isset($HTTP_GET_VARS['oID']) && ($HTTP_GET_VARS['oID'] == $orders['orders_id']))) && !isset($oInfo)) {
        $oInfo = new objectInfo($orders);
      }

      if (isset($oInfo) && is_object($oInfo) && ($orders['orders_id'] == $oInfo->orders_id)) {
        echo '              <tr id="defaultSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '\'">' . "\n";
      } else {
        echo '              <tr onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID')) . 'oID=' . $orders['orders_id']) . '\'">' . "\n";
      }
?>
                <td><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $orders['customers_name']; ?></td>
                <td align="right"><?php echo strip_tags($orders['order_total']); ?></td>
                <td align="center"><?php echo tep_datetime_short($orders['date_purchased']); ?></td>
                <td align="right"><?php echo $orders['orders_status_name']; ?></td>
                <td align="right"><?php if (isset($oInfo) && is_object($oInfo) && ($orders['orders_id'] == $oInfo->orders_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID')) . 'oID=' . $orders['orders_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="5"><table class="table table-bordered table-hover">
                  <tr>
                    <td><?php echo $orders_split->display_count($orders_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
                    <td align="right"><?php echo $orders_split->display_links($orders_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
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
        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_ORDERS_INVOICE, 'oID=' . $oInfo->orders_id) . '" TARGET="_blank">' . tep_image_button('button_invoice.gif', IMAGE_ORDERS_INVOICE) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS_PACKINGSLIP, 'oID=' . $oInfo->orders_id) . '" TARGET="_blank">' . tep_image_button('button_packingslip.gif', IMAGE_ORDERS_PACKINGSLIP) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS_EDIT, 'oID=' . $oInfo->orders_id) . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>');
        $contents[] = array('text' => '<br>' . TEXT_DATE_ORDER_CREATED . ' ' . tep_date_short($oInfo->date_purchased));
        if (tep_not_null($oInfo->last_modified)) $contents[] = array('text' => TEXT_DATE_ORDER_LAST_MODIFIED . ' ' . tep_date_short($oInfo->last_modified));
        $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENT_METHOD . ' '  . $oInfo->payment_method);
         // ### BEGIN ORDER MAKER ###
       $contents[] = array('text' => '<br>' . TEXT_INFO_CUSTOMER_SERVICE_ID . ' '  . $oInfo->customer_service_id);
       // ### END ORDER MAKER ###

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
        </table></td>
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

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>