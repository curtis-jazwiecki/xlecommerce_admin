<?php
/*
  $Id: edit_orders.php 
*/

require('includes/application_top.php');
if ($_GET['w']=='n'){
echo FRAUD_PREVENTION_NOTIFICATION;
    exit();
}

  // include the appropriate functions & classes
  include('order_editor/functions.php');
  include('order_editor/cart.php');
  include('order_editor/order.php');
  include('order_editor/shipping.php');
  include('order_editor/http_client.php');

   
  // Include currencies class
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

 
 //orders status
  $orders_statuses = array();
  $orders_status_array = array();
  $orders_status_query = tep_db_query("SELECT orders_status_id, orders_status_name 
                                       FROM " . TABLE_ORDERS_STATUS . " 
									   WHERE language_id = '" . (int)$languages_id . "'");
									   
  while ($orders_status = tep_db_fetch_array($orders_status_query)) {
    $orders_statuses[] = array('id' => $orders_status['orders_status_id'],
                               'text' => $orders_status['orders_status_name']);
    
	$orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
  }

  $action = (isset($_GET['action']) ? $_GET['action'] : 'edit');

  if (isset($action)) {
    switch ($action) {
    
    ////
    // Update Order
      case 'update_order':
        $oID = tep_db_prepare_input($_GET['oID']);
        $status = tep_db_prepare_input($_POST['status']);
        
        // Set this Session's variables
        if (isset($_POST['billing_same_as_customer'])) $_SESSION['billing_same_as_customer'] = $_POST['billing_same_as_customer'];
        if (isset($_POST['shipping_same_as_billing'])) $_SESSION['shipping_same_as_billing'] = $_POST['shipping_same_as_billing'];
		
        // Update Order Info  
		//figure out the new currency value
		$currency_value_query = tep_db_query("SELECT value 
		                                      FROM " . TABLE_CURRENCIES . " 
											  WHERE code = '" . $_POST['update_info_payment_currency'] . "'");
		$currency_value = tep_db_fetch_array($currency_value_query);

		//figure out the country, state
		$update_customer_state = tep_get_zone_name($_POST['update_customer_country_id'], $_POST['update_customer_zone_id'], $_POST['update_customer_state']);
        $update_customer_country = tep_get_country_name($_POST['update_customer_country_id']);
        $update_billing_state = tep_get_zone_name($_POST['update_billing_country_id'], $_POST['update_billing_zone_id'], $_POST['update_billing_state']);
        $update_billing_country = tep_get_country_name($_POST['update_billing_country_id']);
        $update_delivery_state = tep_get_zone_name($_POST['update_delivery_country_id'], $_POST['update_delivery_zone_id'], $_POST['update_delivery_state']);
        $update_delivery_country = tep_get_country_name($_POST['update_delivery_country_id']);
		
        $sql_data_array = array(
		'customers_name' => tep_db_input(tep_db_prepare_input($_POST['update_customer_name'])),
        'customers_company' => tep_db_input(tep_db_prepare_input($_POST['update_customer_company'])),
        'customers_street_address' => tep_db_input(tep_db_prepare_input($_POST['update_customer_street_address'])),
        'customers_suburb' => tep_db_input(tep_db_prepare_input($_POST['update_customer_suburb'])),
        'customers_city' => tep_db_input(tep_db_prepare_input($_POST['update_customer_city'])),
        'customers_state' => tep_db_input(tep_db_prepare_input($update_customer_state)),
        'customers_postcode' => tep_db_input(tep_db_prepare_input($_POST['update_customer_postcode'])),
        'customers_country' => tep_db_input(tep_db_prepare_input($update_customer_country)),
        'customers_telephone' => tep_db_input(tep_db_prepare_input($_POST['update_customer_telephone'])),
        'customers_email_address' => tep_db_input(tep_db_prepare_input($_POST['update_customer_email_address'])),
                                
		'billing_name' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_name'] : $_POST['update_billing_name']))),
        'billing_company' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_company'] : $_POST['update_billing_company']))),
        'billing_street_address' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_street_address'] : $_POST['update_billing_street_address']))),
        'billing_suburb' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_suburb'] : $_POST['update_billing_suburb']))),
        'billing_city' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_city'] : $_POST['update_billing_city']))),
        'billing_state' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $update_customer_state : $update_billing_state))),
        'billing_postcode' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_postcode'] : $_POST['update_billing_postcode']))),
        'billing_country' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $update_customer_country : $update_billing_country))),
								
								
	'delivery_name' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_name'] : $_POST['update_billing_name']) : $_POST['update_delivery_name']))),
    'delivery_company' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_company'] : $_POST['update_billing_company']) : $_POST['update_delivery_company']))),
    'delivery_street_address' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_street_address'] : $_POST['update_billing_street_address']) : $_POST['update_delivery_street_address']))),
    'delivery_suburb' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_suburb'] : $_POST['update_billing_suburb']) : $_POST['update_delivery_suburb']))),
    'delivery_city' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_city'] : $_POST['update_billing_city']) : $_POST['update_delivery_city']))),
    'delivery_state' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $update_customer_state : $update_billing_state) : $update_delivery_state))),
    'delivery_postcode' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_postcode'] : $_POST['update_billing_postcode']) : $_POST['update_delivery_postcode']))),
    'delivery_country' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $update_customer_country : $update_billing_country) : $update_delivery_country))),
                                
	'payment_method' => tep_db_input(tep_db_prepare_input($_POST['update_info_payment_method'])),
    'currency' => tep_db_input(tep_db_prepare_input($_POST['update_info_payment_currency'])),
    'currency_value' => tep_db_input(tep_db_prepare_input($currency_value['value'])),
    'cc_type' => tep_db_prepare_input($_POST['update_info_cc_type']),
    'cc_owner' => tep_db_prepare_input($_POST['update_info_cc_owner']),
	'cc_number' => tep_db_input(tep_db_prepare_input($_POST['update_info_cc_number'])),
    'cc_expires' => tep_db_prepare_input($_POST['update_info_cc_expires']),
    'last_modified' => 'now()');

        tep_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = \'' . tep_db_input($oID) . '\'');
        $order_updated = true;
        
    
	// UPDATE STATUS HISTORY & SEND EMAIL TO CUSTOMER IF NECESSARY #####

    $check_status_query = tep_db_query("
	                      SELECT customers_name, customers_email_address, orders_status, date_purchased 
	                      FROM " . TABLE_ORDERS . " 
						  WHERE orders_id = '" . (int)$oID . "'");
						  
    $check_status = tep_db_fetch_array($check_status_query); 
	
	
	
    if (($check_status['orders_status'] != $_POST['status']) || (tep_not_null($_POST['comments']))) {

        tep_db_query("UPDATE " . TABLE_ORDERS . " SET 
					  orders_status = '" . tep_db_input($_POST['status']) . "', 
                      last_modified = now() 
                      WHERE orders_id = '" . (int)$oID . "'");
		
		 // Notify Customer ?
      $customer_notified = '0';
			if (isset($_POST['notify']) && ($_POST['notify'] == 'on')) {
			  $notify_comments = '';
			  if (isset($_POST['notify_comments']) && ($_POST['notify_comments'] == 'on')) {
			    $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $_POST['comments']) . "\n\n";
			  }
			  $email = STORE_NAME . "\n" .
			           EMAIL_SEPARATOR . "\n" . 
					   EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" . 
                       EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "\n" . 
					   EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]) . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE2);
			  
			  tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
			  
			  $customer_notified = '1';
			}			  
          		
			tep_db_query("INSERT into " . TABLE_ORDERS_STATUS_HISTORY . " 
			(orders_id, orders_status_id, date_added, customer_notified, comments) 
			values ('" . tep_db_input($_GET['oID']) . "', 
				'" . tep_db_input($_POST['status']) . "', 
				now(), 
				" . tep_db_input($customer_notified) . ", 
				'" . tep_db_input(tep_db_prepare_input($_POST['comments']))  . "')");
			}

        
	// Update Products
	if (is_array($_POST['update_products'])) {
	  foreach($_POST['update_products'] as $orders_products_id => $products_details) {
	  
		//  Update Inventory Quantity
		$order_query = tep_db_query("
		SELECT products_id, products_quantity 
		FROM " . TABLE_ORDERS_PRODUCTS . " 
		WHERE orders_id = '" . (int)$oID . "'
		AND orders_products_id = '" . (int)$orders_products_id . "'");
		$order_products = tep_db_fetch_array($order_query);
		
		// First we do a stock check 
		
		if ($products_details['qty'] != $order_products['products_quantity']){
		$quantity_difference = ($products_details['qty'] - $order_products['products_quantity']);
			if (STOCK_LIMITED == 'true'){
				tep_db_query("UPDATE " . TABLE_PRODUCTS . " SET 
				products_quantity = products_quantity - " . $quantity_difference . ",
				products_ordered = products_ordered + " . $quantity_difference . " 
				WHERE products_id = '" . (int)$order_products['products_id'] . "'");
				} else {
				tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
				products_ordered = products_ordered + " . $quantity_difference . "
				WHERE products_id = '" . (int)$order_products['products_id'] . "'");
			}
		}

	 
	   if ( (isset($products_details['delete'])) && ($products_details['delete'] == 'on') ) {
		 //check first to see if product should be deleted
	   
				 //update quantities first
			   if (STOCK_LIMITED == 'true'){
				tep_db_query("UPDATE " . TABLE_PRODUCTS . " SET 
				products_quantity = products_quantity + " . $products_details["qty"] . ",
				products_ordered = products_ordered - " . $products_details["qty"] . " 
				WHERE products_id = '" . (int)$order_products['products_id'] . "'");
				} else {
				tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
				products_ordered = products_ordered - " . $products_details["qty"] . "
				WHERE products_id = '" . (int)$order_products['products_id'] . "'");
				}
	   
				tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS . "  
							  WHERE orders_id = '" . (int)$oID . "'
							  AND orders_products_id = '" . (int)$orders_products_id . "'");
  
				tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
							  WHERE orders_id = '" . (int)$oID . "'
							  AND orders_products_id = '" . (int)$orders_products_id . "'");
				
				tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . "
							  WHERE orders_id = '" . (int)$oID . "'
							  AND orders_products_id = '" . (int)$orders_products_id . "'");
	   
	   } else {
		 //not deleted=> updated
	   
		// Update orders_products Table
			$Query = "UPDATE " . TABLE_ORDERS_PRODUCTS . " SET
				products_model = '" . $products_details["model"] . "',
				products_name = '" . oe_html_quotes($products_details["name"]) . "',
				products_price = '" . $products_details["price"] . "',
				final_price = '" . $products_details["final_price"] . "',
				products_tax = '" . $products_details["tax"] . "',
				products_quantity = '" . $products_details["qty"] . "'
				WHERE orders_id = '" . (int)$oID . "'
				AND orders_products_id = '$orders_products_id';";
			tep_db_query($Query);
	  
		  // Update Any Attributes
			// Update Any Attributes
			if(isset($products_details['attributes'])) { 
			  foreach($products_details['attributes'] as $orders_products_attributes_id => $attributes_details) {
				$Query = "UPDATE " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set
					products_options = '" . $attributes_details["option"] . "',
					products_options_values = '" . $attributes_details["value"] . "',
					options_values_price ='" . $attributes_details["price"] . "',
					price_prefix ='" . $attributes_details["prefix"] . "'
					where orders_products_attributes_id = '$orders_products_attributes_id';";
					tep_db_query($Query);
				}//end of foreach($products_details["attributes"]
			}// end of if(isset($products_details[attributes]))

		} //end if/else product details delete= on
	  } //end foreach post update products
	}//end if is-array update products
		
	
	  //update any downloads that may exist
      if (is_array($_POST['update_downloads'])) {
	  foreach($_POST['update_downloads'] as $orders_products_download_id => $download_details) {
		$Query = "UPDATE " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " SET
					orders_products_filename = '" . $download_details["filename"] . "',
					download_maxdays = '" . $download_details["maxdays"] . "',
					download_count = '" . $download_details["maxcount"] . "'
					WHERE orders_id = '" . (int)$oID . "'
					AND orders_products_download_id = '$orders_products_download_id';";
					tep_db_query($Query);
			}
		}	//end downloads
		
						
				//delete or update comments
	  if (is_array($_POST['update_comments'])) {
	              foreach($_POST['update_comments'] as $orders_status_history_id => $comments_details) {
	  
	                  if (isset($comments_details['delete'])){
		
			             $Query = "DELETE FROM " . TABLE_ORDERS_STATUS_HISTORY . " 
			                              WHERE orders_id = '" . (int)$oID . "' 
			                              AND orders_status_history_id = '$orders_status_history_id';";
				                          tep_db_query($Query);
				
				        } else {

		                 $Query = "UPDATE " . TABLE_ORDERS_STATUS_HISTORY . " SET
					               comments = '" . $comments_details["comments"] . "'
					               WHERE orders_id = '" . (int)$oID . "'
					               AND orders_status_history_id = '$orders_status_history_id';";
					               tep_db_query($Query);
				        }
				    }	
				}//end comments update section

      $shipping = array();
      
      if (is_array($_POST['update_totals'])) {
        foreach($_POST['update_totals'] as $total_index => $total_details) {
          extract($total_details, EXTR_PREFIX_ALL, "ot");
          if ($ot_class == "ot_shipping") {
           
               $shipping['cost'] = $ot_value;
               $shipping['title'] = $ot_title;
               $shipping['id'] = $ot_id;
			
		  } // end if ($ot_class == "ot_shipping")
        } //end foreach
	  } //end if is_array

       if (tep_not_null($shipping['id'])) {
   			tep_db_query("UPDATE " . TABLE_ORDERS . " SET shipping_module = '" . $shipping['id'] . "' WHERE orders_id = '" . (int)$oID . "'");
       }

        $order = new manualOrder($oID);
        $order->adjust_zones();

        $cart = new manualCart();
        $_SESSION['cart'] = $cart;
        $cart->restore_contents($oID);
        $total_count = $cart->count_contents();
        $total_weight = $cart->show_weight();

        // Get the shipping quotes- if we don't have shipping quotes shipping tax calculation can't happen
        $shipping_modules = new shipping;
        $shipping_quotes = $shipping_modules->quote();
		
		if (DISPLAY_PRICE_WITH_TAX == 'true') { //extract the base shipping cost or the ot_shipping module will add tax to it again
		   $module = substr($GLOBALS['shipping']['id'], 0, strpos($GLOBALS['shipping']['id'], '_'));
		   $tax = tep_get_tax_rate($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
		   $order->info['total'] -= ( $order->info['shipping_cost'] - ($order->info['shipping_cost'] / (1 + ($tax /100))) );
		   $order->info['shipping_cost'] = ($order->info['shipping_cost'] / (1 + ($tax /100)));
		}

		//this is where we call the order total modules
		require( 'order_editor/order_total.php');
		$order_total_modules = new order_total();
        $order_totals = $order_total_modules->process();  

        $current_ot_totals_array = array();
		$current_ot_titles_array = array();
        $current_ot_totals_query = tep_db_query("select class, title from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$oID . "' order by sort_order");
        while ($current_ot_totals = tep_db_fetch_array($current_ot_totals_query)) {
          $current_ot_totals_array[] = $current_ot_totals['class'];
		  $current_ot_titles_array[] = $current_ot_totals['title'];
        }

		tep_db_query("DELETE FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id = '" . (int)$oID . "'");

        $j=1; //giving something a sort order of 0 ain't my bag baby
		$new_order_totals = array();

	    if (is_array($_POST['update_totals'])) {
			 //1
          foreach($_POST['update_totals'] as $total_index => $total_details) { //2
            extract($total_details, EXTR_PREFIX_ALL, "ot");
            if (!strstr($ot_class, 'ot_custom')) { //3
             for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) { //4

			  if ($order_totals[$i]['code'] == 'ot_tax') { //5
			  $new_ot_total = ((in_array($order_totals[$i]['title'], $current_ot_titles_array)) ? false : true);
			  } else { //within 5
			  $new_ot_total = ((in_array($order_totals[$i]['code'], $current_ot_totals_array)) ? false : true);
			  }  //end 5 if ($order_totals[$i]['code'] == 'ot_tax')
 
			  if ( ( ($order_totals[$i]['code'] == 'ot_tax') && ($order_totals[$i]['code'] == $ot_class) && ($order_totals[$i]['title'] == $ot_title) ) || ( ($order_totals[$i]['code'] != 'ot_tax') && ($order_totals[$i]['code'] == $ot_class) ) ) { //6
			  //only good for components that show up in the $order_totals array

				if ($ot_title != '') { //7
                  $new_order_totals[] = array('title' => $ot_title,
                                              'text' => (($ot_class != 'ot_total') ? $order_totals[$i]['text'] : '<b>' . $currencies->format($order->info['total'], true, $order->info['currency'], $order->info['currency_value']) . '</b>'),
                                              'value' => (($order_totals[$i]['code'] != 'ot_total') ? $order_totals[$i]['value'] : $order->info['total']),
                                              'code' => $order_totals[$i]['code'],
                                              'sort_order' => $j);
                $written_ot_totals_array[] = $ot_class;
				$written_ot_titles_array[] = $ot_title;
				$j++;
                } else { //within 7

				  $order->info['total'] += ($ot_value*(-1)); 
				  $written_ot_totals_array[] = $ot_class;
				  $written_ot_titles_array[] = $ot_title; 

                } //end 7

			  } elseif ( ($new_ot_total) && (!in_array($order_totals[$i]['title'], $current_ot_titles_array)) ) { //within 6

                $new_order_totals[] = array('title' => $order_totals[$i]['title'],
                                            'text' => $order_totals[$i]['text'],
                                            'value' => $order_totals[$i]['value'],
                                            'code' => $order_totals[$i]['code'],
                                            'sort_order' => $j);
                $current_ot_totals_array[] = $order_totals[$i]['code'];
				$current_ot_titles_array[] = $order_totals[$i]['title'];
				$written_ot_totals_array[] = $ot_class;
				$written_ot_titles_array[] = $ot_title;
                $j++;
                //echo $order_totals[$i]['code'] . "<br>"; for debugging- use of this results in errors

			  } elseif ($new_ot_total) { //also within 6
                $order->info['total'] += ($order_totals[$i]['value']*(-1));
                $current_ot_totals_array[] = $order_totals[$i]['code'];
				$written_ot_totals_array[] = $ot_class;
				$written_ot_titles_array[] = $ot_title;
              }//end 6
           }//end 4
         } elseif ( (tep_not_null($ot_value)) && (tep_not_null($ot_title)) ) { // this modifies if (!strstr($ot_class, 'ot_custom')) { //3
            $new_order_totals[] = array('title' => $ot_title,
                     'text' => $currencies->format($ot_value, true, $order->info['currency'], $order->info['currency_value']),
                                        'value' => $ot_value,
                                        'code' => 'ot_custom_' . $j,
                                        'sort_order' => $j);
            $order->info['total'] += $ot_value;
			$written_ot_totals_array[] = $ot_class;
		    $written_ot_titles_array[] = $ot_title;
            $j++;
          } //end 3
		  
		    //save ot_skippy from certain annihilation
			 if ( (!in_array($ot_class, $written_ot_totals_array)) && (!in_array($ot_title, $written_ot_titles_array)) && (tep_not_null($ot_value)) && (tep_not_null($ot_title)) && ($ot_class != 'ot_tax') && ($ot_class != 'ot_loworderfee') ) { //7
			//this is supposed to catch the oddball components that don't show up in $order_totals
				 
				    $new_order_totals[] = array(
					        'title' => $ot_title,
                            'text' => $currencies->format($ot_value, true, $order->info['currency'], $order->info['currency_value']),
                            'value' => $ot_value,
                            'code' => $ot_class,
                            'sort_order' => $j);
               //$current_ot_totals_array[] = $order_totals[$i]['code'];
				//$current_ot_titles_array[] = $order_totals[$i]['title'];
				$written_ot_totals_array[] = $ot_class;
				$written_ot_titles_array[] = $ot_title;
                $j++;
				 
				 } //end 7
        } //end 2
	  } else {//within 1
	  // $_POST['update_totals'] is not an array => write in all order total components that have been generated by the sundry modules
	   for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) { //8
	                  $new_order_totals[] = array('title' => $order_totals[$i]['title'],
                                            'text' => $order_totals[$i]['text'],
                                            'value' => $order_totals[$i]['value'],
                                            'code' => $order_totals[$i]['code'],
                                            'sort_order' => $j);
                $j++;
				
			} //end 8
				
		} //end if (is_array($_POST['update_totals'])) { //1
	  
		for ($i=0, $n=sizeof($new_order_totals); $i<$n; $i++) {
          $sql_data_array = array('orders_id' => $oID,
                                  'title' => $new_order_totals[$i]['title'],
                                  'text' => $new_order_totals[$i]['text'],
                                  'value' => $new_order_totals[$i]['value'], 
                                  'class' => $new_order_totals[$i]['code'], 
                                  'sort_order' => $new_order_totals[$i]['sort_order']);
          tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
        }
		
        
        if (isset($_POST['subaction'])) {
          switch($_POST['subaction']) {
            case 'add_product':
              tep_redirect(tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action')) . 'action=edit#products'));
              break;
              
          }
        }
        
		// 1.5 SUCCESS MESSAGE #####
		
		
	// CHECK FOR NEW EMAIL CONFIRMATION

    if ( (isset($_POST['nC1'])) || (isset($_POST['nC2'])) || (isset($_POST['nC3'])) ) {
	//then the user selected the option of sending a new email
    
    tep_redirect(tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action')) . 'action=email')); 
	//redirect to the email case
	 
  } else  { 
     //email? email?  We don't need no stinkin email!
	 
	 if ($order_updated)	{
			$messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
		}

		tep_redirect(tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action')) . 'action=edit'));
		
		}
		
	break;
		
	// 3. NEW ORDER EMAIL ###############################################################################################
	case 'email':
          
		$oID = tep_db_prepare_input($_GET['oID']);
		$order = new manualOrder($oID);
		
		    for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
	  //loop all the products in the order
			 $products_ordered_attributes = '';
	  if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
	    for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
		$products_ordered_attributes .= "\n\t" . $order->products[$i]['attributes'][$j]['option'] . ' ' . $order->products[$i]['attributes'][$j]['value'];
      }
    }
	
	   $products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . $products_model . ' = ' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . $products_ordered_attributes . "\n";
			 }
		   
		//Build the email
	   	 $email_order = STORE_NAME . "\n" . 
                        EMAIL_SEPARATOR . "\n" . 
						EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" .
  EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "\n" .
                	    EMAIL_TEXT_DATE_MODIFIED . ' ' . strftime(DATE_FORMAT_LONG) . "\n\n";

	    $email_order .= EMAIL_TEXT_PRODUCTS . "\n" . 
    	                EMAIL_SEPARATOR . "\n" . 
        	            $products_ordered . 
            	        EMAIL_SEPARATOR . "\n";

	  for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
        $email_order .= strip_tags($order->totals[$i]['title']) . ' ' . strip_tags($order->totals[$i]['text']) . "\n";
      }

	  if ($order->content_type != 'virtual') {
    	$email_order .= "\n" . EMAIL_TEXT_DELIVERY_ADDRESS . "\n" . 
        	            EMAIL_SEPARATOR . "\n" .
						$order->delivery['name'] . "\n";
						if ($order->delivery['company']) {
		                  $email_order .= $order->delivery['company'] . "\n";
	                    }
		$email_order .= $order->delivery['street_address'] . "\n";
		                if ($order->delivery['suburb']) {
		                  $email_order .= $order->delivery['suburb'] . "\n";
	                    }
		$email_order .= $order->customer['city'] . "\n";
		                if ($order->delivery['state']) {
		                  $email_order .= $order->delivery['state'] . "\n";
	                    }
		$email_order .= $order->customer['postcode'] . "\n" .
						$order->delivery['country'] . "\n";
	  }

    	$email_order .= "\n" . EMAIL_TEXT_BILLING_ADDRESS . "\n" .
        	            EMAIL_SEPARATOR . "\n" .
						$order->billing['name'] . "\n";
						if ($order->billing['company']) {
		                  $email_order .= $order->billing['company'] . "\n";
	                    }
		$email_order .= $order->billing['street_address'] . "\n";
		                if ($order->billing['suburb']) {
		                  $email_order .= $order->billing['suburb'] . "\n";
	                    }
		$email_order .= $order->customer['city'] . "\n";
		                if ($order->billing['state']) {
		                  $email_order .= $order->billing['state'] . "\n";
	                    }
		$email_order .= $order->customer['postcode'] . "\n" .
						$order->billing['country'] . "\n\n";

	    $email_order .= EMAIL_TEXT_PAYMENT_METHOD . "\n" . 
    	                EMAIL_SEPARATOR . "\n";
	    $email_order .= $order->info['payment_method'] . "\n\n";
		
		        
			//	if ( ($order->info['payment_method'] == ORDER_EDITOR_SEND_INFO_PAYMENT_METHOD) && (EMAIL_TEXT_PAYMENT_INFO) ) { 
		      //     $email_order .= EMAIL_TEXT_PAYMENT_INFO . "\n\n";
		       //   }
			 //I'm not entirely sure what the purpose of this is so it is being shelved for now

				if (EMAIL_TEXT_FOOTER) {
					$email_order .= EMAIL_TEXT_FOOTER . "\n\n";
				  }

      //code for plain text emails which changes the � sign to EUR, otherwise the email will show ? instead of �
      $email_order = str_replace("�","EUR",$email_order);
	  $email_order = str_replace("&nbsp;"," ",$email_order);

	  //code which replaces the <br> tags within EMAIL_TEXT_PAYMENT_INFO and EMAIL_TEXT_FOOTER with the proper \n
	  $email_order = str_replace("<br>","\n",$email_order);

	  //send the email to the customer
	  tep_mail($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

   // send emails to other people as necessary
  if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
    tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
  }
  
         //do the dirty
 		
		$messageStack->add_session(SUCCESS_EMAIL_SENT, 'success');
		
        tep_redirect(tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action')) . 'action=edit'));
		  
		 break;

        
    ////
    // Edit Order
      case 'edit':
        if (!isset($_GET['oID'])) {
		$messageStack->add(ERROR_NO_ORDER_SELECTED, 'error');
          break;
		  }
        $oID = tep_db_prepare_input($_GET['oID']);
        $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");
        $order_exists = true;
        if (!tep_db_num_rows($orders_query)) {
        $order_exists = false;
          $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
          break;
        }
        
        $order = new manualOrder($oID);
        $shippingKey = $order->adjust_totals($oID);
        $order->adjust_zones();
        
        $cart = new manualCart();
        $cart->restore_contents($oID);
        $total_count = $cart->count_contents();
        $total_weight = $cart->show_weight();

        // Get the shipping quotes
        $shipping_modules = new shipping;
        $shipping_quotes = $shipping_modules->quote();
 
     
        break;
    }
  
  		}

		// currecies drop-down array
		$currency_query = tep_db_query("select distinct title, code from " . TABLE_CURRENCIES . " order by code ASC");  
		$currency_array = array();
  		while($currency = tep_db_fetch_array($currency_query)) {
    		$currency_array[] = array('id' => $currency['code'],
                              		  'text' => $currency['code'] . ' - ' . $currency['title']);
  		}

?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
      
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3><?php echo sprintf(HEADING_TITLE, $oID, tep_datetime_short($order->info['date_purchased'])); ?>
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading"><?php echo sprintf(HEADING_TITLE, $oID, tep_datetime_short($order->info['date_purchased'])); ?>
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
               <table class="table table-condensed table-striped">
               <table class="table table-bordered table-hover">
  <tr>
<!-- body_text //-->
    <td>

 <?php
   
   if (($action == 'edit') && ($order_exists == true)) {
     
	 echo tep_draw_form('edit_order', FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action')) . 'action=update_order');
    
 ?>
  
      <div id="header">
		  <p id="headerTitle"> 
			 <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>
			  <script language="JavaScript" type="text/javascript"><!--
			  //this button only works with javascript and is therefore only displayed on browsers with javascript enabled
              document.write("<a href=\"javascript:newOrderEmail()\"><img src=\"includes/languages/<?php echo $language; ?>/images/buttons/button_new_order_email.gif\" border=\"0\" alt=\"<?php echo IMAGE_NEW_ORDER_EMAIL; ?>\" title=\"<?php echo IMAGE_NEW_ORDER_EMAIL; ?>\" ></a>");
	           //--></script>
			   <?php } ?>
				  
		    <?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $_GET['oID'] . '&action=edit') . '">' . tep_image_button('button_details_b.gif', IMAGE_EDIT) . '</a>'; ?>
		    <?php echo '<a href="' . tep_href_link(FILENAME_ORDERS_INVOICE, 'oID=' . $_GET['oID']) . '" TARGET="_blank">' . tep_image_button('button_invoice_b.gif', IMAGE_ORDERS_INVOICE) . '</a>'; ?>
		    <?php echo '<a href="' . tep_href_link(FILENAME_ORDERS_PACKINGSLIP, 'oID=' . $_GET['oID']) . '" TARGET="_blank">' . tep_image_button('button_packingslip_b.gif', IMAGE_ORDERS_PACKINGSLIP) . '</a>'; ?>
		<?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))) . '">' . tep_image_button('button_back_b.gif', IMAGE_BACK) . '</a> '; ?>
		  
      </p>
	  </div>
	   
	    <div id="ordersMessageStack">
	   	  <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
	    </div>
	   	   
	<?php if (ORDER_EDITOR_USE_AJAX != 'true') { ?>
	<!-- Begin Update Block, only for non-ajax use -->

           <div class="updateBlock">
              <div class="update1"><?php echo HINT_PRESS_UPDATE; ?></div>
              <div class="update2">&nbsp;</div>
              <div class="update3">&nbsp;</div>
              <div class="update4" align="center"><?php echo ENTRY_SEND_NEW_ORDER_CONFIRMATION; ?>&nbsp;<?php echo tep_draw_checkbox_field('nC1', '', false); ?></div>
              <div class="update5"><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></div>
          </div>
	
	  <br>
	  <br>
	  <!-- End of Update Block -->
	  <?php } ?>


    <!-- customer_info bof //-->
            
        <table class="table table-bordered table-hover">
          <tr>
            <td>
            <!-- customer_info bof //-->
            <table class="table table-bordered table-hover">
              <tr> 
                <td colspan="4"><?php echo ENTRY_CUSTOMER; ?></td>
              </tr>
              <tr> 
                <td valign="middle" align="right"><?php echo ENTRY_NAME; ?></td>
                <td colspan="3"><input name="update_customer_name" size="37" value="<?php echo stripslashes($order->customer['name']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_name', encodeURIComponent(this.value))"<?php } ?>></td>
              </tr>
              <tr> 
                <td valign="middle" align="right" nowrap><?php echo ENTRY_COMPANY; ?></td>
                <td colspan="3"><input name="update_customer_company" size="37" value="<?php echo stripslashes($order->customer['company']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_company', encodeURIComponent(this.value))"<?php } ?>></td>
              </tr>
              <tr> 
                <td valign="middle" align="right"><?php echo ENTRY_STREET_ADDRESS; ?></td>
                <td colspan="3"><input name="update_customer_street_address" size="37" value="<?php echo stripslashes($order->customer['street_address']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_street_address', encodeURIComponent(this.value))"<?php } ?>></td>
              </tr>
              <tr> 
                <td valign="middle" align="right"><?php echo ENTRY_SUBURB; ?></td>
                <td colspan="3"><input name="update_customer_suburb" size="37" value="<?php echo stripslashes($order->customer['suburb']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_suburb', encodeURIComponent(this.value))"<?php } ?>></td>
              </tr>
              <tr> 
                <td valign="middle" align="right"><?php echo ENTRY_CITY_STATE; ?></td>
                <td colspan="2"><input name="update_customer_city" size="15" value="<?php echo stripslashes($order->customer['city']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_city', encodeURIComponent(this.value))"<?php } ?>>,</td>
                <td><span id="customerStateMenu">
				<?php if (ORDER_EDITOR_USE_AJAX == 'true') {
				echo tep_draw_pull_down_menu('update_customer_zone_id', tep_get_country_zones($order->customer['country_id']), $order->customer['zone_id'], 'style="width: 200px;" onChange="updateOrdersField(\'customers_state\', this.options[this.selectedIndex].text);"'); 
				} else {
				echo tep_draw_pull_down_menu('update_customer_zone_id', tep_get_country_zones($order->customer['country_id']), $order->customer['zone_id'], 'style="width: 200px;"');
				}?></span><span id="customerStateInput"><input name="update_customer_state" size="15" value="<?php echo stripslashes($order->customer['state']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_state', encodeURIComponent(this.value))"<?php } ?>></span></td>
              </tr>
              <tr> 
                <td valign="middle" align="right"><?php echo ENTRY_POST_CODE; ?></td>
                <td><input name="update_customer_postcode" size="5" value="<?php echo $order->customer['postcode']; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_postcode', encodeURIComponent(this.value))"<?php } ?>></td>
                <td valign="middle" align="right" nowrap><?php echo ENTRY_COUNTRY; ?></td>
                <td>
				<?php if (ORDER_EDITOR_USE_AJAX == 'true') {
				echo tep_draw_pull_down_menu('update_customer_country_id', tep_get_countries(), $order->customer['country_id'], 'style="width: 200px;" onChange="update_zone(\'update_customer_country_id\', \'update_customer_zone_id\', \'customerStateInput\', \'customerStateMenu\'); updateOrdersField(\'customers_country\', this.options[this.selectedIndex].text);"'); 
				} else {
				echo tep_draw_pull_down_menu('update_customer_country_id', tep_get_countries(), $order->customer['country_id'], 'style="width: 200px;" onChange="update_zone(\'update_customer_country_id\', \'update_customer_zone_id\', \'customerStateInput\', \'customerStateMenu\');"'); 
				} ?></td>
              </tr>
              <tr> 
                <td colspan="4"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
              </tr>
              <tr> 
                <td valign="middle" align="right"><?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
                <td colspan="3"><input name="update_customer_telephone" size="15" value="<?php echo $order->customer['telephone']; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_telephone', encodeURIComponent(this.value))"<?php } ?>></td>
              </tr>
              <tr> 
                <td valign="middle" align="right"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
                <td colspan="3"><input name="update_customer_email_address" size="35" value="<?php echo $order->customer['email_address']; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_email_address', encodeURIComponent(this.value))"<?php } ?>></td>
              </tr>
            </table>
			
			<!-- customer_info_eof //-->
            <!-- shipping_address bof -->
            <table class="table table-bordered table-hover">
              <tr>
                <td>
                <table class="table table-bordered table-hover">
                  <tr> 
				   <td onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_SHIPPING_ADDRESS); ?>')"; onMouseout="hideddrivetip()"><?php echo ENTRY_SHIPPING_ADDRESS; ?> 
				   	<script language="JavaScript" type="text/javascript">
                   <!--
                    document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
	               //-->
                  </script>
				  
				</td>
                  </tr>
				  
                  <?php if (ORDER_EDITOR_USE_AJAX != 'true') { ?>
				  <tr> 
                    <td valign="middle"><input type="checkbox" name="shipping_same_as_billing"> <?php echo TEXT_SHIPPING_SAME_AS_BILLING; ?></td>
                  </tr>
				  <?php } ?>
				  
                </table>
                </td>
              </tr>
              <tr id="shippingAddressEntry">
                <td>
                <table class="table table-bordered table-hover">
                  <tr> 
                    <td colspan="4"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
                  </tr>
                  <tr> 
                    <td valign="middle" align="right"><?php echo ENTRY_NAME; ?></td>
                    <td colspan="3"><input name="update_delivery_name" size="37" value="<?php echo stripslashes($order->delivery['name']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('delivery_name', encodeURIComponent(this.value))"<?php } ?>></td>
                  </tr>
                  <tr> 
                    <td valign="middle" align="right"><?php echo ENTRY_COMPANY; ?></td>
                    <td colspan="3"><input name="update_delivery_company" size="37" value="<?php echo stripslashes($order->delivery['company']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('delivery_company', encodeURIComponent(this.value))"<?php } ?>></td>
                  </tr>
                  <tr> 
                    <td valign="middle" align="right"><?php echo ENTRY_STREET_ADDRESS; ?></td>
                    <td colspan="3"><input name="update_delivery_street_address" size="37" value="<?php echo stripslashes($order->delivery['street_address']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('delivery_street_address', encodeURIComponent(this.value))"<?php } ?>></td>
                  </tr>
                  <tr> 
                    <td valign="middle" align="right"><?php echo ENTRY_SUBURB; ?></td>
                    <td colspan="3"><input name="update_delivery_suburb" size="37" value="<?php echo stripslashes($order->delivery['suburb']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('delivery_suburb', encodeURIComponent(this.value))"<?php } ?>></td>
                  </tr>
                  <tr>
                    <td valign="middle" align="right"><?php echo ENTRY_CITY_STATE; ?></td>
                    <td colspan="2"><input name="update_delivery_city" size="15" value="<?php echo stripslashes($order->delivery['city']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('delivery_city', encodeURIComponent(this.value))"<?php } ?>>,</td>
                    <td><span id="deliveryStateMenu">
					<?php if (ORDER_EDITOR_USE_AJAX == 'true') { 
				echo tep_draw_pull_down_menu('update_delivery_zone_id', tep_get_country_zones($order->delivery['country_id']), $order->delivery['zone_id'], 'style="width: 200px;" onChange="updateShippingZone(\'delivery_state\', this.options[this.selectedIndex].text);"'); 
					} else {
					echo tep_draw_pull_down_menu('update_delivery_zone_id', tep_get_country_zones($order->delivery['country_id']), $order->delivery['zone_id'], 'style="width: 200px;"'); 
					} ?>
					</span><span id="deliveryStateInput"><input name="update_delivery_state" size="15" value="<?php echo stripslashes($order->delivery['state']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateShippingZone('delivery_state', encodeURIComponent(this.value))"<?php } ?>></span></td>
                  </tr>
                  <tr> 
                    <td valign="middle" align="right"><?php echo ENTRY_POST_CODE; ?></td>
                    <td valign="top"><input name="update_delivery_postcode" size="5" value="<?php echo $order->delivery['postcode']; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateShippingZone('delivery_postcode', encodeURIComponent(this.value))"<?php } ?>></td>
                    <td valign="middle" align="right"><?php echo ENTRY_COUNTRY; ?></td>
                    <td>
					<?php if (ORDER_EDITOR_USE_AJAX == 'true') {
					echo tep_draw_pull_down_menu('update_delivery_country_id', tep_get_countries(), $order->delivery['country_id'], 'style="width: 200px;" onchange="update_zone(\'update_delivery_country_id\', \'update_delivery_zone_id\', \'deliveryStateInput\', \'deliveryStateMenu\'); updateShippingZone(\'delivery_country\', this.options[this.selectedIndex].text);"'); 
					} else {
					echo tep_draw_pull_down_menu('update_delivery_country_id', tep_get_countries(), $order->delivery['country_id'], 'style="width: 200px;" onchange="update_zone(\'update_delivery_country_id\', \'update_delivery_zone_id\', \'deliveryStateInput\', \'deliveryStateMenu\');"'); 
					}
					?></td>
                  </tr>       
                </table>
                </td>
              </tr>                  
            </table>
            <!-- shipping_address_eof //-->
            </td>
            <td>
            <table class="table table-bordered table-hover">
              <!-- billing_address bof //-->
              <tr>
                <td>
                <table class="table table-bordered table-hover">
                  <tr> 
                    <td colspan="4"><?php echo ENTRY_BILLING_ADDRESS; ?></td>
                  </tr>
				  
				  <?php if (ORDER_EDITOR_USE_AJAX != 'true') { ?>
                  <tr> 
                    <td colspan="4" valign="middle"><input type="checkbox" name="billing_same_as_customer"> <?php echo TEXT_BILLING_SAME_AS_CUSTOMER; ?></td>
                  </tr>
				  <?php } ?>
				  
                </table>
                </td>
              </tr>
              <tr id="billingAddressEntry">
                <td>
                <table class="table table-bordered table-hover">               
                  <tr> 
                    <td valign="middle" align="right"><?php echo ENTRY_NAME; ?></td>
                    <td colspan="3"><input name="update_billing_name" size="37" value="<?php echo stripslashes($order->billing['name']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('billing_name', encodeURIComponent(this.value))"<?php } ?>></td>
                  </tr>
                  <tr> 
                    <td valign="middle" align="right"><?php echo ENTRY_COMPANY; ?></td>
                    <td colspan="3"><input name="update_billing_company" size="37" value="<?php echo stripslashes($order->billing['company']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('billing_company', encodeURIComponent(this.value))"<?php } ?>></td>
                  </tr>
                  <tr> 
                    <td valign="middle" align="right"><?php echo ENTRY_STREET_ADDRESS; ?></td>
                    <td colspan="3"><input name="update_billing_street_address" size="37" value="<?php echo stripslashes($order->billing['street_address']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('billing_street_address', encodeURIComponent(this.value))"<?php } ?>></td>
                  </tr>
                  <tr> 
                    <td valign="middle" align="right"><?php echo ENTRY_SUBURB; ?></td>
                    <td colspan="3"><input name="update_billing_suburb" size="37" value="<?php echo stripslashes($order->billing['suburb']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('billing_suburb', encodeURIComponent(this.value))"<?php } ?>></td>
                  </tr>
                  <tr> 
                    <td valign="middle" align="right"><?php echo ENTRY_CITY_STATE; ?></td>
                    <td colspan="2"><input name="update_billing_city" size="15" value="<?php echo stripslashes($order->billing['city']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('billing_city', encodeURIComponent(this.value))"<?php } ?>>,</td>
                    <td><span id="billingStateMenu">
					<?php if (ORDER_EDITOR_USE_AJAX == 'true') {
					echo tep_draw_pull_down_menu('update_billing_zone_id', tep_get_country_zones($order->billing['country_id']), $order->billing['zone_id'], 'style="width: 200px;" onChange="updateOrdersField(\'billing_state\', this.options[this.selectedIndex].text);"'); 
					} else {
					echo tep_draw_pull_down_menu('update_billing_zone_id', tep_get_country_zones($order->billing['country_id']), $order->billing['zone_id'], 'style="width: 200px;"');
					} ?>
					</span><span id="billingStateInput"><input name="update_billing_state" size="15" value="<?php echo stripslashes($order->billing['state']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('billing_state', encodeURIComponent(this.value))"<?php } ?>></span></td>
                  </tr>
                  <tr> 
                    <td valign="middle" align="right" nowrap><?php echo ENTRY_POST_CODE; ?></td>
                    <td><input name="update_billing_postcode" size="5" value="<?php echo $order->billing['postcode']; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('billing_postcode', encodeURIComponent(this.value))"<?php } ?>></td>
                    <td valign="middle" align="right"><?php echo ENTRY_COUNTRY; ?></td>
                    <td>
					<?php if (ORDER_EDITOR_USE_AJAX == 'true') {
					echo tep_draw_pull_down_menu('update_billing_country_id', tep_get_countries(), $order->billing['country_id'], 'style="width: 200px;" onchange="update_zone(\'update_billing_country_id\', \'update_billing_zone_id\', \'billingStateInput\', \'billingStateMenu\'); updateOrdersField(\'billing_country\', this.options[this.selectedIndex].text);"'); 
					} else {
					echo tep_draw_pull_down_menu('update_billing_country_id', tep_get_countries(), $order->billing['country_id'], 'style="width: 200px;" onchange="update_zone(\'update_billing_country_id\', \'update_billing_zone_id\', \'billingStateInput\', \'billingStateMenu\'); updateOrdersField(\'billing_country\', this.options[this.selectedIndex].text);"'); 
					} ?></td>
                  </tr>
                </table>
                </td>
              </tr>
              <!-- billing_address_eof //-->
              <!-- payment_method bof //-->
              <tr>
                <td>
             
      <table class="table table-bordered table-hover">
        <tr> 
          <td colspan="2" valign="bottom" onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_UPDATE_TO_CC); ?>')" onMouseout="hideddrivetip()"><?php echo ENTRY_PAYMENT_METHOD; ?>
		  		
				  <script language="JavaScript" type="text/javascript">
                   <!--
                    document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
	               //-->
                  </script>
			
			</td>
	      
	         <td valign="bottom" onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_UPDATE_CURRENCY); ?>')" onMouseout="hideddrivetip()"><?php echo ENTRY_CURRENCY_TYPE; ?> 
		  
		  		  <script language="JavaScript" type="text/javascript">
                   <!--
                    document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
	               //-->
                  </script>
				  
             </td>
	         <td><?php echo ENTRY_CURRENCY_VALUE; ?></td>
         </tr>
                  
	     <tr> 
	       <td colspan="2">
	       <?php 
	        //START for payment dropdown menu use this by quick_fixer
  		      if (ORDER_EDITOR_PAYMENT_DROPDOWN == 'true') { 
		
		    // Get list of all payment modules available
            $enabled_payment = array();
            $module_directory = DIR_FS_CATALOG_MODULES . 'payment/';
            $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));

             if ($dir = @dir($module_directory)) {
              while ($file = $dir->read()) {
               if (!is_dir( $module_directory . $file)) {
                if (substr($file, strrpos($file, '.')) == $file_extension) {
                   $directory_array[] = $file;
                 }
               }
             }
            sort($directory_array);
            $dir->close();
           }

          // For each available payment module, check if enabled
          for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) {
          $file = $directory_array[$i];

          include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/' . $file);
          include($module_directory . $file);

          $class = substr($file, 0, strrpos($file, '.'));
          if (tep_class_exists($class)) {
             $module = new $class;
             if ($module->check() > 0) {
              // If module enabled create array of titles
      	       $enabled_payment[] = array('id' => $module->title, 'text' => $module->title);
		
		      //if the payment method is the same as the payment module title then don't add it to dropdown menu
		      if ($module->title == $order->info['payment_method']) {
			      $paymentMatchExists='true';	
		         }
              }
            }
          }
 		//just in case the payment method found in db is not the same as the payment module title then make it part of the dropdown array or else it cannot be the selected default value
		  if ($paymentMatchExists !='true') {
			$enabled_payment[] = array('id' => $order->info['payment_method'], 'text' => $order->info['payment_method']);	
           }
            $enabled_payment[] = array('id' => 'Other', 'text' => 'Other');	
		    //draw the dropdown menu for payment methods and default to the order value
	  		  if (ORDER_EDITOR_USE_AJAX == 'true') {
			  echo tep_draw_pull_down_menu('update_info_payment_method', $enabled_payment, $order->info['payment_method'], 'id="update_info_payment_method" style="width: 150px;" onChange="init(); updateOrdersField(\'payment_method\', this.options[this.selectedIndex].text)"'); 
			  } else {
			  echo tep_draw_pull_down_menu('update_info_payment_method', $enabled_payment, $order->info['payment_method'], 'id="update_info_payment_method" style="width: 150px;" onChange="init();"'); 
			  }
		    }  else { //draw the input field for payment methods and default to the order value  ?>
		  
		   <input name="update_info_payment_method" size="35" value="<?php echo $order->info['payment_method']; ?>" id="update_info_payment_method" onChange="init();<?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?> updateOrdersField('payment_method', encodeURIComponent(this.value));<?php } ?>">
		   
		   <?php } //END for payment dropdown menu use this by quick_fixer ?>
		   
		   </td>
	
	       <td width="20">
	       </td>
	
	        <td>
			 <?php
	         ///get the currency info
              reset($currencies->currencies);
              $currencies_array = array();
                while (list($key, $value) = each($currencies->currencies)) {
                      $currencies_array[] = array('id' => $key, 'text' => $value['title']);
                 }
	
               echo tep_draw_pull_down_menu('update_info_payment_currency', $currencies_array, $order->info['currency'], 'id="update_info_payment_currency" onChange="currency(this.value)"'); 

?>
          </td>

	     <td>
		  <input name="update_info_payment_currency_value" size="15" readonly id="update_info_payment_currency_value" value="<?php echo $order->info['currency_value']; ?>">
		 </td>
      </tr>

                  <!-- credit_card bof //-->
    <tr> 
      <td colspan="6">
	  
	  <table id="optional"><!--  -->
	 <tr>
	    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	  </tr>
	  <tr>
	    <td><?php echo ENTRY_CREDIT_CARD_TYPE; ?></td>
	<td class="main"><input name="update_info_cc_type" size="32" value="<?php echo $order->info['cc_type']; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('cc_type', encodeURIComponent(this.value))"<?php } ?>></td>
	  </tr>
	  <tr>
	    <td><?php echo ENTRY_CREDIT_CARD_OWNER; ?></td>
	    <td><input name="update_info_cc_owner" size="32" value="<?php echo $order->info['cc_owner']; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('cc_owner', encodeURIComponent(this.value))<?php } ?>"></td>
	  </tr>
	  <tr>
	    <td><?php echo ENTRY_CREDIT_CARD_NUMBER; ?></td>
	    <td><input name="update_info_cc_number" size="32" value="<?php echo $order->info['cc_number']; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('cc_number', encodeURIComponent(this.value))"<?php } ?>></td>
	  </tr>
	  <tr>
	    <td><?php echo ENTRY_CREDIT_CARD_EXPIRES; ?></td>
	    <td><input name="update_info_cc_expires" size="4" value="<?php echo $order->info['cc_expires']; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('cc_expires', encodeURIComponent(this.value))"<?php } ?>></td>
	  </tr>
	</table>
	  
   </td>
  </tr>
 </table>
				
				</td>
              </tr>                  
            </table></td>
          </tr>
        </table>
		
	<div id="productsMessageStack">
	  <?php //echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
    </div>

	
	<div> 
	  <a name="products"></a>
		<!-- product_listing bof //-->
         <table class="table table-bordered table-hover" id="productsTable">
			   <tr>
                <td><div align="center"><?php echo TABLE_HEADING_DELETE; ?></div></td>
			    <td><div align="center"><?php echo TABLE_HEADING_QUANTITY; ?></div></td>
                <td><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
                <td><?php echo TABLE_HEADING_TAX; ?></td>
	  <td onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_BASE_PRICE); ?>')"; onMouseout="hideddrivetip()"><?php  echo TABLE_HEADING_BASE_PRICE; ?> <script language="JavaScript" type="text/javascript">
                   <!--
                    document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
	               //-->
                  </script></td>
	  <td onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_PRICE_EXCL); ?>')"; onMouseout="hideddrivetip()"><?php  echo TABLE_HEADING_UNIT_PRICE; ?> <script language="JavaScript" type="text/javascript">
                   <!--
                    document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
	               //-->
                  </script></td>
	  <td onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_PRICE_INCL); ?>')"; onMouseout="hideddrivetip()"><?php  echo TABLE_HEADING_UNIT_PRICE_TAXED; ?> <script language="JavaScript" type="text/javascript">
                   <!--
                    document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
	               //-->
                  </script></td>
	  <td onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_TOTAL_EXCL); ?>')"; onMouseout="hideddrivetip()"><?php  echo TABLE_HEADING_TOTAL_PRICE; ?> <script language="JavaScript" type="text/javascript">
                   <!--
                    document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
	               //-->
                  </script></td>
      <td onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_TOTAL_INCL); ?>')"; onMouseout="hideddrivetip()"><?php  echo TABLE_HEADING_TOTAL_PRICE_TAXED; ?> <script language="JavaScript" type="text/javascript">
                   <!--
                    document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
	               //-->
                  </script></td>
              </tr>
  <?php
  if (sizeof($order->products)) {
    for ($i=0; $i<sizeof($order->products); $i++) {
      $orders_products_id = $order->products[$i]['orders_products_id'];  ?>
			   
			   <tr>
                
				<td><div align="center"><input type="checkbox" name="<?php echo "update_products[" . $orders_products_id . "][delete]"; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onClick="updateProductsField('delete', '<?php echo $orders_products_id; ?>', 'delete', this.checked, this)"<?php } ?>></div></td>
                
				<td><div align="center"><input name="<?php echo "update_products[" . $orders_products_id . "][qty]"; ?>" size="2" onKeyUp="updatePrices('qty', '<?php echo $orders_products_id; ?>')" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('reload1', '<?php echo $orders_products_id; ?>', 'products_quantity', encodeURIComponent(this.value))"<?php } ?> value="<?php echo $order->products[$i]['qty']; ?>" id="<?php echo "update_products[" . $orders_products_id . "][qty]"; ?>"></div></td>
                
				<td><input name="<?php echo "update_products[" . $orders_products_id . "][name]"; ?>" size="50" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('update', '<?php echo $orders_products_id; ?>', 'products_name', encodeURIComponent(this.value))"<?php } ?> value='<?php echo oe_html_quotes($order->products[$i]['name']); ?>'>
    
	<?php
      // Has Attributes?
     if (isset($order->products[$i]['attributes']) && (sizeof($order->products[$i]['attributes']) > 0)) {
        for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
          $orders_products_attributes_id = $order->products[$i]['attributes'][$j]['orders_products_attributes_id'];
				if (ORDER_EDITOR_USE_AJAX == 'true') {
				echo '<br><nobr><small>&nbsp;<i> - ' . "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][option]' size='6' value='" . oe_html_quotes($order->products[$i]['attributes'][$j]['option']) . "' onChange=\"updateAttributesField('simple', 'products_options', '" . $orders_products_attributes_id . "', '" . $orders_products_id . "', encodeURIComponent(this.value))\">" . ': ' . "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][value]' size='10' value='" . oe_html_quotes($order->products[$i]['attributes'][$j]['value']) . "' onChange=\"updateAttributesField('simple', 'products_options_values', '" . $orders_products_attributes_id . "', '" . $orders_products_id . "', encodeURIComponent(this.value))\">" . ': ' . "</i><input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][prefix]' size='1' id='p" . $orders_products_id . "_" . $orders_products_attributes_id . "_prefix' value='" . $order->products[$i]['attributes'][$j]['prefix'] . "' onKeyUp=\"updatePrices('att_price', '" . $orders_products_id . "')\" onChange=\"updateAttributesField('hard', 'price_prefix', '" . $orders_products_attributes_id . "', '" . $orders_products_id . "', encodeURIComponent(this.value))\">" . ': ' . "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][price]' size='7' value='" . $order->products[$i]['attributes'][$j]['price'] . "' onKeyUp=\"updatePrices('att_price', '" . $orders_products_id . "')\" onChange=\"updateAttributesField('hard', 'options_values_price', '" . $orders_products_attributes_id . "', '" . $orders_products_id . "', encodeURIComponent(this.value))\" id='p". $orders_products_id . "a" . $orders_products_attributes_id . "'>";
				} else {
				echo '<br><nobr><small>&nbsp;<i> - ' . "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][option]' size='6' value='" . oe_html_quotes($order->products[$i]['attributes'][$j]['option']) . "'>" . ': ' . "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][value]' size='10' value='" . oe_html_quotes($order->products[$i]['attributes'][$j]['value']) . "'>" . ': ' . "</i><input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][prefix]' size='1' id='p" . $orders_products_id . "_" . $orders_products_attributes_id . "_prefix' value='" . $order->products[$i]['attributes'][$j]['prefix'] . "' onKeyUp=\"updatePrices('att_price', '" . $orders_products_id . "')\">" . ': ' . "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][price]' size='7' value='" . $order->products[$i]['attributes'][$j]['price'] . "' onKeyUp=\"updatePrices('att_price', '" . $orders_products_id . "')\" id='p". $orders_products_id . "a" . $orders_products_attributes_id . "'>";
				}
				echo '</small></nobr>';
			}  //end for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
		
			 //Has downloads?
  
    if (DOWNLOAD_ENABLED == 'true') {
   $downloads_count = 1;
   $d_index = 0;
   $download_query_raw ="SELECT orders_products_download_id, orders_products_filename, download_maxdays, download_count
                         FROM " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . "                               
						 WHERE orders_products_id='" . $orders_products_id . "'
						 AND orders_id='" . (int)$oID . "'
						 ORDER BY orders_products_download_id";
  
		$download_query = tep_db_query($download_query_raw);
		
		//
		if (isset($downloads->products)) unset($downloads->products);
		//
		
		if (tep_db_num_rows($download_query) > 0) {
        while ($download = tep_db_fetch_array($download_query)) {
		
 		$downloads->products[$d_index] = array(
		            'id' => $download['orders_products_download_id'],
		            'filename' => $download['orders_products_filename'],
                    'maxdays' => $download['download_maxdays'],
                    'maxcount' => $download['download_count']);
		
		$d_index++; 
		
		} 
       } 
        
   if (isset($downloads->products) && (sizeof($downloads->products) > 0)) {
    for ($mm=0; $mm<sizeof($downloads->products); $mm++) {  
    $id =  $downloads->products[$mm]['id'];
    echo '<br><small>';
    echo '<nobr>' . ENTRY_DOWNLOAD_COUNT . $downloads_count . "";
    echo ' </nobr><br>' . "\n";
  
      if (ORDER_EDITOR_USE_AJAX == 'true') {
      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_FILENAME . ": <input name='update_downloads[" . $id . "][filename]' size='12' value='" . $downloads->products[$mm]['filename'] . "' onChange=\"updateDownloads('orders_products_filename', '" . $id . "', '" . $orders_products_id . "', this.value)\">";
      echo ' </nobr><br>' . "\n";
      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_MAXDAYS . ": <input name='update_downloads[" . $id . "][maxdays]' size='6' value='" . $downloads->products[$mm]['maxdays'] . "' onChange=\"updateDownloads('download_maxdays', '" . $id . "', '" . $orders_products_id . "', this.value)\">";
      echo ' </nobr><br>' . "\n";
      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_MAXCOUNT . ": <input name='update_downloads[" . $id . "][maxcount]' size='6' value='" . $downloads->products[$mm]['maxcount'] . "' onChange=\"updateDownloads('download_count', '" . $id . "', '" . $orders_products_id . "', this.value)\">";
      } else {
      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_FILENAME . ": <input name='update_downloads[" . $id . "][filename]' size='12' value='" . $downloads->products[$mm]['filename'] . "'>";
      echo ' </nobr><br>' . "\n";
      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_MAXDAYS . ": <input name='update_downloads[" . $id . "][maxdays]' size='6' value='" . $downloads->products[$mm]['maxdays'] . "'>";
      echo ' </nobr><br>' . "\n";
      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_MAXCOUNT . ": <input name='update_downloads[" . $id . "][maxcount]' size='6' value='" . $downloads->products[$mm]['maxcount'] . "'>";
     }
  
     echo ' </nobr>' . "\n";
     echo '<br></small>';
     $downloads_count++;
     } //end  for ($mm=0; $mm<sizeof($download_query); $mm++) {
    }
   } //end download
  } //end if (sizeof($order->products[$i]['attributes']) > 0) {
?>
                </td>
            
			<td><input name="<?php echo "update_products[" . $orders_products_id . "][model]"; ?>" size="12" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('update', '<?php echo $orders_products_id; ?>', 'products_model', encodeURIComponent(this.value))"<?php } ?> value="<?php echo $order->products[$i]['model']; ?>"></td>
            
			<td><input name="<?php echo "update_products[" . $orders_products_id . "][tax]"; ?>" size="5" onKeyUp="updatePrices('tax', '<?php echo $orders_products_id; ?>')" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('reload1', '<?php echo $orders_products_id; ?>', 'products_tax', encodeURIComponent(this.value))"<?php } ?> value="<?php echo tep_display_tax_value($order->products[$i]['tax']); ?>" id="<?php echo "update_products[" . $orders_products_id . "][tax]"; ?>">%</td>
		
		    <td><input name="<?php echo "update_products[" . $orders_products_id . "][price]"; ?>" size="5" onKeyUp="updatePrices('price', '<?php echo $orders_products_id; ?>')" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')"<?php } ?> value="<?php echo number_format((float)$order->products[$i]['price'], 4, '.', ''); ?>" id="<?php echo "update_products[" . $orders_products_id . "][price]"; ?>"></td>
            
			<td><input name="<?php echo "update_products[" . $orders_products_id . "][final_price]"; ?>" size="5" onKeyUp="updatePrices('final_price', '<?php echo $orders_products_id; ?>')" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')"<?php } ?> value="<?php echo number_format((float)$order->products[$i]['final_price'], 4, '.', ''); ?>" id="<?php echo "update_products[" . $orders_products_id . "][final_price]"; ?>"></td>
                
			<td><input name="<?php echo "update_products[" . $orders_products_id . "][price_incl]"; ?>" size="5" value="<?php echo number_format((float)($order->products[$i]['final_price'] * (($order->products[$i]['tax']/100) + 1)), 4, '.', ''); ?>" onKeyUp="updatePrices('price_incl', '<?php echo $orders_products_id; ?>')" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')"<?php } ?> id="<?php echo "update_products[" . $orders_products_id . "][price_incl]"; ?>"></td>
				
			<td><input name="<?php echo "update_products[" . $orders_products_id . "][total_excl]"; ?>" size="5" value="<?php echo number_format((float)$order->products[$i]['final_price'] * $order->products[$i]['qty'], 4, '.', ''); ?>" onKeyUp="updatePrices('total_excl', '<?php echo $orders_products_id; ?>')" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')"<?php } ?> id="<?php echo "update_products[" . $orders_products_id . "][total_excl]"; ?>"></td>
				
			<td><input name="<?php echo "update_products[" . $orders_products_id . "][total_incl]"; ?>" size="5" value="<?php echo number_format((float)(($order->products[$i]['final_price'] * (($order->products[$i]['tax']/100) + 1))) * $order->products[$i]['qty'], 4, '.', ''); ?>" onKeyUp="updatePrices('total_incl', '<?php echo $orders_products_id; ?>')" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')"<?php } ?> id="<?php echo "update_products[" . $orders_products_id . "][total_incl]"; ?>"></td>
				
              </tr>
             			  
<?php
    }
  } else {
    //the order has no products
?>
              <tr>
                <td colspan="10" valign="middle"><?php echo TEXT_NO_ORDER_PRODUCTS; ?></td>
              </tr>
              <tr> 
                <td colspan="10"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
              </tr>
<?php
  }
?>
            </table><!-- product_listing_eof //-->
			
		<div id="totalsBlock">
		<table class="table table-bordered table-hover">
		  <tr><td>
			 
            <table class="table table-bordered table-hover">
              <tr>
                <td>
				  <br>
				    <div>
					  <a href="<?php echo tep_href_link(FILENAME_ORDERS_EDIT_ADD_PRODUCT, 'oID=' . $_GET['oID'] . '&step=1'); ?>" target="addProducts" onClick="openWindow('<?php echo tep_href_link(FILENAME_ORDERS_EDIT_ADD_PRODUCT, 'oID=' . $_GET['oID'] . '&step=1'); ?>','addProducts');return false"><?php echo tep_image_button('button_add_article_g.gif', TEXT_ADD_NEW_PRODUCT); ?></a><input type="hidden" name="subaction" value="">
				    </div>
				  <br>
			    </td>
             
			  <!-- order_totals bof //-->
                <td align="right" rowspan="2">
                  <table class="table table-bordered table-hover">
                    <tr>
                      <td class="dataTableHeadingContent" width="15" nowrap onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_TOTALS); ?>')"; onMouseout="hideddrivetip()"> <script language="JavaScript" type="text/javascript">
                   <!--
                    document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
	               //-->
                  </script></td>
                      <td><?php echo TABLE_HEADING_OT_TOTALS; ?></td>
                      <td colspan="2"><?php echo TABLE_HEADING_OT_VALUES; ?></td>
                    </tr>
<?php
  for ($i=0; $i<sizeof($order->totals); $i++) {
  
    $id = $order->totals[$i]['class'];
	
	if ($order->totals[$i]['class'] == 'ot_shipping') {
	   if (tep_not_null($order->info['shipping_id'])) {
	       $shipping_module_id = $order->info['shipping_id'];
		   } else {
		   //here we could create logic to attempt to determine the shipping module used if it's not in the database
		   $shipping_module_id = '';
		   }
	  } else {
	    $shipping_module_id = '';
	  } //end if ($order->totals[$i]['class'] == 'ot_shipping') {
	 
    $rowStyle = (($i % 2) ? 'dataTableRowOver' : 'dataTableRow');
    if ( ($order->totals[$i]['class'] == 'ot_total') || ($order->totals[$i]['class'] == 'ot_subtotal') || ($order->totals[$i]['class'] == 'ot_tax') || ($order->totals[$i]['class'] == 'ot_loworderfee') ) {
      echo '                  <tr class="' . $rowStyle . '">' . "\n";
      if ($order->totals[$i]['class'] != 'ot_total') {
        echo '                    <td valign="middle" height="15">
		<script language="JavaScript" type="text/javascript">
		<!--
		document.write("<span id=\"update_totals['.$i.']\"><a href=\"javascript:setCustomOTVisibility(\'update_totals['.($i+1).']\', \'visible\', \'update_totals['.$i.']\');\"><img src=\"order_editor/images/plus.gif\" border=\"0\" alt=\"' . IMAGE_ADD_NEW_OT . '\" title=\"' . IMAGE_ADD_NEW_OT . '\"></a></span>");
		//-->
        </script></td>' . "\n";
      } else {
        echo '                    <td valign="middle">&nbsp;</td>' . "\n";
      }
      
      echo '                    <td align="right"><input name="update_totals['.$i.'][title]" value=\'' . trim($order->totals[$i]['title']) . '\' readonly="readonly"></td>' . "\n";
	  
      if ($order->info['currency'] != DEFAULT_CURRENCY) echo '                    <td>&nbsp;</td>' . "\n";
      echo '                    <td align="right">' . $order->totals[$i]['text'] . '<input name="update_totals['.$i.'][value]" type="hidden" value="' . number_format((float)$order->totals[$i]['value'], 2, '.', '') . '"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"></td>' . "\n" .
           '                  </tr>' . "\n";
    } else {
      if ($i % 2) {
        echo '                  	    <script language="JavaScript" type="text/javascript">
		<!--
		document.write("<tr class=\"' . $rowStyle . '\" id=\"update_totals['.$i.']\" style=\"visibility: hidden; display: none;\"><td class=\"dataTableContent\" valign=\"middle\" height=\"15\"><a href=\"javascript:setCustomOTVisibility(\'update_totals['.($i).']\', \'hidden\', \'update_totals['.($i-1).']\');\"><img src=\"order_editor/images/minus.gif\" border=\"0\" alt=\"' . IMAGE_REMOVE_NEW_OT . '\" title=\"' . IMAGE_REMOVE_NEW_OT . '\"></a></td>");
			 //-->
        </script>
			 
			 <noscript><tr class="' . $rowStyle . '" id="update_totals['.$i.']" >' . "\n" .
             '                    <td valign="middle"></td></noscript>' . "\n";
      } else {
        echo '                  <tr class="' . $rowStyle . '">' . "\n" .
             '                    <td valign="middle">
	    <script language="JavaScript" type="text/javascript">
		<!--
		document.write("<span id=\"update_totals['.$i.']\"><a href=\"javascript:setCustomOTVisibility(\'update_totals['.($i+1).']\', \'visible\', \'update_totals['.$i.']\');\"><img src=\"order_editor/images/plus.gif\" border=\"0\" alt=\"' . IMAGE_ADD_NEW_OT . '\" title=\"' . IMAGE_ADD_NEW_OT . '\"></a></span>");
		//-->
        </script></td>' . "\n";
      }

       if (ORDER_EDITOR_USE_AJAX == 'true') {
	  echo '                    <td align="right"><input name="update_totals['.$i.'][title]" id="'.$id.'[title]" value="' . trim($order->totals[$i]['title']) . '" onChange="obtainTotals()"></td>' . "\n" .
           '                    <td align="right"><input name="update_totals['.$i.'][value]" id="'.$id.'[value]" value="' . number_format((float)$order->totals[$i]['value'], 2, '.', '') . '" size="6" onChange="obtainTotals()"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"><input name="update_totals['.$i.'][id]" type="hidden" value="' . $shipping_module_id . '" id="' . $id . '[id]"></td>' . "\n";
		   } else {
	  echo '                    <td align="right"><input name="update_totals['.$i.'][title]" id="'.$id.'[title]" value="' . trim($order->totals[$i]['title']) . '"></td>' . "\n" .
           '                    <td align="right"><input name="update_totals['.$i.'][value]" id="'.$id.'[value]" value="' . number_format((float)$order->totals[$i]['value'], 2, '.', '') . '" size="6"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"><input name="update_totals['.$i.'][id]" type="hidden" value="' . $shipping_module_id . '" id="' . $id . '[id]"></td>' . "\n";
		   }
		   
      if ($order->info['currency'] != DEFAULT_CURRENCY) echo '                    <td align="right">' . $order->totals[$i]['text'] . '</td>' . "\n";
      echo '                  </tr>' . "\n";
    }
  }
?>
                </table>
			  </td>
                <!-- order_totals_eof //-->
              </tr>              
              <tr>
                <td valign="bottom">
                
<?php 
  if (sizeof($shipping_quotes) > 0) {
?>
                <!-- shipping_quote bof //-->
                <table class="table table-bordered table-hover">
                  <tr>
                    <td colspan="3"><?php echo TABLE_HEADING_SHIPPING_QUOTES; ?></td>
                  </tr>
				  
				  				  
<?php
    $r = 0;
    for ($i=0, $n=sizeof($shipping_quotes); $i<$n; $i++) {
      for ($j=0, $n2=sizeof($shipping_quotes[$i]['methods']); $j<$n2; $j++) {
        $r++;
		if (!isset($shipping_quotes[$i]['tax'])) $shipping_quotes[$i]['tax'] = 0;
        $rowClass = ((($r/2) == (floor($r/2))) ? 'dataTableRowOver' : 'dataTableRow');
        echo '                  <tr class="' . $rowClass . '" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this, \'' . $rowClass . '\')" onClick="selectRowEffect(this, ' . $r . '); setShipping(' . $r . ');">' .
             '                    <td valign="top" align="left">
			 <script language="JavaScript" type="text/javascript">
                   <!--
                    document.write("<input type=\"radio\" name=\"shipping\" id=\"shipping_radio_' . $r . '\" value=\"' . $shipping_quotes[$i]['id'] . '_' . $shipping_quotes[$i]['methods'][$j]['id'].'\">");
	               //-->
                  </script>
			 <input type="hidden" id="update_shipping[' . $r . '][title]" name="update_shipping[' . $r . '][title]" value=\''.$shipping_quotes[$i]['module'] . ' (' . $shipping_quotes[$i]['methods'][$j]['title'].'):\'>' . "\n" .
			 '      <input type="hidden" id="update_shipping[' . $r . '][value]" name="update_shipping[' . $r . '][value]" value="'.tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']).'">' . "\n" .
			 '      <input type="hidden" id="update_shipping[' . $r . '][id]" name="update_shipping[' . $r . '][id]" value="' . $shipping_quotes[$i]['id'] . '_' . $shipping_quotes[$i]['methods'][$j]['id'] . '">' . "\n" .
             '      <td>' . $shipping_quotes[$i]['module'] . ' (' . $shipping_quotes[$i]['methods'][$j]['title'] . '):</td>' . "\n" . 
             '      <td align="right">' . $currencies->format(tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" . 
             '                  </tr>';
      }
    }
?>
                  <tr>
                    <td colspan="3"><?php echo sprintf(TEXT_PACKAGE_WEIGHT_COUNT, $shipping_num_boxes . ' x ' . $shipping_weight, $total_count); ?></td>
                  </tr>
                </table>
                <!-- shipping_quote_eof //-->
<?php
  } else {
  echo AJAX_NO_QUOTES;
  }
?>                </td>
              </tr> 
            </table>
		  
		  </td></tr>
		 </table> 
	  </div>
    </div> <!-- this is end of the master div for the whole totals/shipping area -->
		      
	<?php if (ORDER_EDITOR_USE_AJAX != 'true') { ?> 
    <!-- Begin Update Block, only for non-javascript browsers -->

	  <br>
            <div class="updateBlock">
              <div class="update1"><?php echo HINT_PRESS_UPDATE; ?></div>
              <div class="update2">&nbsp;</div>
              <div class="update3">&nbsp;</div>
              <div class="update4" align="center"><?php echo ENTRY_SEND_NEW_ORDER_CONFIRMATION; ?>&nbsp;<?php echo tep_draw_checkbox_field('nC1', '', false); ?></div>
              <div class="update5" align="center"><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></div>
           </div>
		  
	       <br>
            <div><?php //echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></div>
	 
	 <!-- End of Update Block -->  
	 <?php } ?>
		
	  <div id="historyMessageStack">
	    <?php //echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
	  </div>

    <div id="commentsBlock">
    <table class="table table-bordered table-hover" id="commentsTable">
     <tr>
      <td align="left"><?php echo TABLE_HEADING_DELETE; ?></td>
      <td align="left" width="10">&nbsp;</td>
      <td align="left"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
      <td align="left">&nbsp;</td>
      <td align="center"><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></td>
      <td align="left" width="10">&nbsp;</td>
      <td align="left"><?php echo TABLE_HEADING_STATUS; ?></td>
      <td align="left" width="10">&nbsp;</td>
      <td align="left"><?php echo TABLE_HEADING_COMMENTS; ?></td>
    </tr>
    <?php
      $orders_history_query = tep_db_query("SELECT orders_status_history_id, orders_status_id, date_added, customer_notified, comments 
                                            FROM " . TABLE_ORDERS_STATUS_HISTORY . " 
									        WHERE orders_id = '" . (int)$oID . "' 
									        ORDER BY date_added");
        if (tep_db_num_rows($orders_history_query)) {
          while ($orders_history = tep_db_fetch_array($orders_history_query)) {
          
		   $r++;
           $rowClass = ((($r/2) == (floor($r/2))) ? 'dataTableRowOver' : 'dataTableRow');
        
	      if (ORDER_EDITOR_USE_AJAX == 'true') { 
		   echo '  <tr class="' . $rowClass . '" id="commentRow' . $orders_history['orders_status_history_id'] . '" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this, \'' . $rowClass . '\')">' . "\n" .
         '	  <td><div id="do_not_delete"><input name="update_comments[' . $orders_history['orders_status_history_id'] . '][delete]" type="checkbox" onClick="updateCommentsField(\'delete\', \'' . $orders_history['orders_status_history_id'] . '\', this.checked, \'\', this)"></div></td>' . "\n" . 
		 '    <td align="left"> </td>' . "\n" .
         '    <td>' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n" .
         '    <td align="left"> </td>' . "\n" .
         '    <td align="center">';
		 } else {
		 echo '  <tr class="' . $rowClass . '" id="commentRow' . $orders_history['orders_status_history_id'] . '" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this, \'' . $rowClass . '\')">' . "\n" .
         '	  <td align="center"><div id="do_not_delete"><input name="update_comments[' . $orders_history['orders_status_history_id'] . '][delete]" type="checkbox"></div></td>' . "\n" . 
		 '    <td align="left"> </td>' . "\n" .
         '    <td align="center">' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n" .
         '    <td align="left" width="10"> </td>' . "\n" .
         '    <td align="center">';
		 }
      
	   if ($orders_history['customer_notified'] == '1') {
        echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK) . "</td>\n";
         } else {
        echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS) . "</td>\n";
         }
       
	    echo '    <td align="left">&nbsp;</td>' . "\n" .
             '    <td align="left">' . $orders_status_array[$orders_history['orders_status_id']] . '</td>' . "\n";
        echo '    <td align="left">&nbsp;</td>' . "\n" .
             '    <td align="left">';
  
        if (ORDER_EDITOR_USE_AJAX == 'true') { 
		echo tep_draw_textarea_field("update_comments[" . $orders_history['orders_status_history_id'] . "][comments]", "soft", "40", "5", 
  "" .	tep_db_output($orders_history['comments']) . "", "onChange=\"updateCommentsField('update', '" . $orders_history['orders_status_history_id'] . "', 'false', encodeURIComponent(this.value))\"") . '' . "\n" .
		 '    </td>' . "\n";
		 } else {
		 echo tep_draw_textarea_field("update_comments[" . $orders_history['orders_status_history_id'] . "][comments]", "soft", "40", "5", 
  "" .	tep_db_output($orders_history['comments']) . "") . '' . "\n" .
		 '    </td>' . "\n";
		 }
 
        echo '  </tr>' . "\n";
  
        }
       } else {
       echo '  <tr>' . "\n" .
            '    <td colspan="5">' . TEXT_NO_ORDER_HISTORY . '</td>' . "\n" .
            '  </tr>' . "\n";
       }

    ?>
  </table> 
  </div>
				  
      <div>
	  <?php //echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?>
	  </div>
	
<table class="table table-bordered table-hover">
  <tr>
    <td align="left"><?php echo TABLE_HEADING_NEW_STATUS; ?></td>
    <td>&nbsp;</td>
    <td align="left"><?php echo TABLE_HEADING_COMMENTS; ?></td>
  </tr>
	<tr>
	  <td>
		  <table class="table table-bordered table-hover">
		  
        <tr>
          <td><b><?php echo ENTRY_STATUS; ?></b></td>
          <td align="right"><?php echo tep_draw_pull_down_menu('status', $orders_statuses, $order->info['orders_status'], 'id="status"'); ?></td>
        </tr>
        <tr>
          <td><b><?php echo ENTRY_NOTIFY_CUSTOMER; ?></b></td>
          <td align="right"><?php echo oe_draw_checkbox_field('notify', '', false, '', 'id="notify"'); ?></td>
        </tr>
        <tr>
          <td><b><?php echo ENTRY_NOTIFY_COMMENTS; ?></b></td>
          <td align="right"><?php echo oe_draw_checkbox_field('notify_comments', '', false, '', 'id="notify_comments"'); ?></td>
        </tr>
     </table>
	  </td>
    <td>&nbsp;</td>
    <td>
    <?php echo tep_draw_textarea_field('comments', 'soft', '40', '5', '', 'id="comments"'); ?>
    </td>
  </tr>
    
	<?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?> 
	<script language="JavaScript" type="text/javascript">
     <!--
	     document.write("<tr>");
         document.write("<td colspan=\"3\" align=\"right\">");
		 document.write("<input type=\"button\" name=\"comments_button\" value=\"<?php echo oe_html_no_quote(AJAX_SUBMIT_COMMENT); ?>\" onClick=\"javascript:getNewComment();\">");
		 document.write("</td>");
		 document.write("</tr>");
	 //-->
    </script>
	<?php } ?>
				  
  </table>
  
    <div>
	  <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
	</div>
    
	<!-- End of Status Block -->

	<?php if (ORDER_EDITOR_USE_AJAX != 'true') { ?> 
	<!-- Begin Update Block, only for non-javascript browsers -->
	       <div class="updateBlock">
              <div class="update1"><?php echo HINT_PRESS_UPDATE; ?></div>
              <div class="update2">&nbsp;</div>
              <div class="update3">&nbsp;</div>
              <div class="update4" align="center"><?php echo ENTRY_SEND_NEW_ORDER_CONFIRMATION; ?>&nbsp;<?php echo tep_draw_checkbox_field('nC1', '', false); ?></div>
              <div class="update5" align="center"><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></div>
          </div>
		  
	       <br>
            <div><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></div>
	
	<!-- End of Update Block -->
	<?php   }  //end if (ORDER_EDITOR_USE_AJAX != 'true') {
          echo '</form>';
        }
    ?>
  <!-- body_text_eof //-->
      </td>

    </tr>
  </table>
  </table>
<!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<script  type="text/javascript">
    var notice_win;
	$(document).ready(function(){ 
    <?php
    $like_string = '';
    $is_not_fraud = fraud_prevention_is_negative($_GET['oID']);
    if (!$is_not_fraud){
    ?>
    notice_win = window.open('edit_orders.php?w=n',"notice","toolbar=no, menubar=no,resizable=no,location=no,directories=no,status=no,width=500,height=500");
    notice_win.focus();
    
    <?php
    }
    ?>
	}).click(function(){
	    if(notice_win && !notice_win.closed) 
            notice_win.focus();
	});
</script>
  <?php include('order_editor/css.php');  
      //because if you haven't got your css, what have you got?
      ?>

  <?php include('order_editor/javascript.php');  
      //because if you haven't got your javascript, what have you got?
      ?>
<script type="text/javascript">

/***********************************************
* Cool DHTML tooltip script- � Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

/***********************************************
* For Order Editor
* This has to stay here for the tooltips to work correctly
* I tried sticking it with the rest of the javascript, but it has to be inside the <body> tag
*
***********************************************/

var offsetxpoint=-60 //Customize x offset of tooltip
var offsetypoint=20 //Customize y offset of tooltip
var ie=document.all
var ns6=document.getElementById && !document.all
var enabletip=false
if (ie||ns6)
var tipobj=document.all? document.all["dhtmltooltip"] : document.getElementById? document.getElementById("dhtmltooltip") : ""

function ietruebody(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function ddrivetip(thetext, thecolor, thewidth){
if (ns6||ie){
if (typeof thewidth!="undefined") tipobj.style.width=thewidth+"px"
if (typeof thecolor!="undefined" && thecolor!="") tipobj.style.backgroundColor=thecolor
tipobj.innerHTML=thetext
enabletip=true
return false
}
}

function positiontip(e){
if (enabletip){
var curX=(ns6)?e.pageX : event.clientX+ietruebody().scrollLeft;
var curY=(ns6)?e.pageY : event.clientY+ietruebody().scrollTop;
//Find out how close the mouse is to the corner of the window
var rightedge=ie&&!window.opera? ietruebody().clientWidth-event.clientX-offsetxpoint : window.innerWidth-e.clientX-offsetxpoint-20
var bottomedge=ie&&!window.opera? ietruebody().clientHeight-event.clientY-offsetypoint : window.innerHeight-e.clientY-offsetypoint-20

var leftedge=(offsetxpoint<0)? offsetxpoint*(-1) : -1000

//if the horizontal distance isn't enough to accomodate the width of the context menu
if (rightedge<tipobj.offsetWidth)
//move the horizontal position of the menu to the left by it's width
tipobj.style.left=ie? ietruebody().scrollLeft+event.clientX-tipobj.offsetWidth+"px" : window.pageXOffset+e.clientX-tipobj.offsetWidth+"px"
else if (curX<leftedge)
tipobj.style.left="5px"
else
//position the horizontal position of the menu where the mouse is positioned
tipobj.style.left=curX+offsetxpoint+"px"

//same concept with the vertical position
if (bottomedge<tipobj.offsetHeight)
tipobj.style.top=ie? ietruebody().scrollTop+event.clientY-tipobj.offsetHeight-offsetypoint+"px" : window.pageYOffset+e.clientY-tipobj.offsetHeight-offsetypoint+"px"
else
tipobj.style.top=curY+offsetypoint+"px"
tipobj.style.visibility="visible"
}
}

function hideddrivetip(){
if (ns6||ie){
enabletip=false
tipobj.style.visibility="hidden"
tipobj.style.left="-1000px"
tipobj.style.backgroundColor='white'
tipobj.style.width='200'
}
}

document.onmousemove=positiontip

</script>      
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>