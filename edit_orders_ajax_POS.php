<?php
  /*
  $Id: edit_orders_ajax.php v5.0.5 08/27/2007 djmonkey1 Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
  
  */
  
  require('includes/application_top.php');
  
  // output a response header
  header('Content-type: text/html; charset=' . CHARSET . '');

  // include the appropriate functions & classes
  include('order_editor/functions.php');
  include('order_editor/cart.php');
  include('order_editor/order.php');
  include('order_editor/shipping.php');
  include('order_editor/http_client.php');
  include(DIR_WS_LANGUAGES . $language. '/' . FILENAME_ORDERS_EDIT);

   
  // Include currencies class
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
  
  //$action 
  //all variables are sent by $_GET only or by $_POST only, never together
  if (sizeof($_GET) > 0) {
     $action = $_GET['action']; 
  } elseif (sizeof($_POST) > 0) {
	 $action = $_POST['action']; 
	 }
   
  //1.  Update most the orders table
  if ($action == 'update_order_field') {
	 tep_db_query("UPDATE " . TABLE_ORDERS . " SET " . $_GET['field'] . " = '" . oe_iconv($_GET['new_value']) . "' WHERE orders_id = '" . $_GET['oID'] . "'");
	 
	  
	  //generate responseText
	  echo $_GET['field'];
	 

  }
  
    //BOF:range manager
    if ($action=='set_fields_by_telephone'){
        $response = array(
            'name' => '', 
            'company' => '', 
            'address1' => '', 
            'address2' => '', 
            'city' => '', 
            'state' => '', 
            'postcode' => '', 
            'email' => '', 
            'telephone' => '', 
            'match_located' => '0', 
        );
        $fetch_telephone_query = tep_db_query("select customers_telephone from orders where orders_id='" . (int)$_GET['oID'] . "'");
        if (tep_db_num_rows($fetch_telephone_query)){
            $info = tep_db_fetch_array($fetch_telephone_query);
            if (!empty($info['customers_telephone'])){
                $customer_query = tep_db_query("select c.customers_firstname, c.customers_lastname, c.customers_email_address, c.customers_telephone, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, z.zone_name from orders o inner join customers c on o.customers_telephone=c.customers_telephone left join address_book ab on c.customers_default_address_id=ab.address_book_id left join zones z on ab.entry_zone_id=z.zone_id where o.orders_id='" . (int)$_GET['oID'] . "'");
                if (tep_db_num_rows($customer_query)){
                    $customer_info = tep_db_fetch_array($customer_query);
                    $name = $customer_info['customers_firstname'] . (!empty($customer_info['customers_lastname']) ? ' ' . $customer_info['customers_lastname'] : '');
                    $company = !empty($customer_info['entry_company']) ? $customer_info['entry_company'] : '';
                    $address1 = !empty($customer_info['entry_street_address']) ? $customer_info['entry_street_address'] : '';
                    $address2 = !empty($customer_info['entry_suburb']) ? $customer_info['entry_suburb'] : '';
                    $city = !empty($customer_info['entry_city']) ? $customer_info['entry_city'] : '';
                    $postcode = !empty($customer_info['entry_postcode']) ? $customer_info['entry_postcode'] : '';
                    $state = !empty($customer_info['zone_name']) ? $customer_info['zone_name'] : '';
                    $email = !empty($customer_info['customers_email_address']) ? $customer_info['customers_email_address'] : '';
                    $telephone = !empty($customer_info['customers_telephone']) ? $customer_info['customers_telephone'] : '';
                    tep_db_query("update orders set customers_name='" . tep_db_input($name) . "', customers_company='" . tep_db_input($company) . "', customers_street_address='" . tep_db_input($address1) . "', customers_suburb='" . tep_db_input($address1) . "', customers_city='" . tep_db_input($city) . "', customers_postcode='" . tep_db_input($postcode) . "', customers_state='" . tep_db_input($state) . "', customers_email_address='" . tep_db_input($email) . "' where orders_id='" . (int)$_GET['oID'] . "'");
                    
                    $response['name'] = $name; 
                    $response['company'] = $company; 
                    $response['address1'] = $address1; 
                    $response['address2'] = $address2; 
                    $response['city'] = $city;
                    $response['state'] = $state; 
                    $response['postcode'] = $postcode; 
                    $response['email'] = $email;
                    $response['telephone'] = $telephone; 
                    $response['match_located'] = '1';
                }
            }
        }
        echo json_encode($response);
        exit;
    }
    
    
    if ($action=='set_fields_by_email'){
        $response = array(
            'name' => '', 
            'company' => '', 
            'address1' => '', 
            'address2' => '', 
            'city' => '', 
            'state' => '', 
            'postcode' => '', 
            'email' => '', 
            'telephone' => '', 
            'match_located' => '0',
        );
        $fetch_email_query = tep_db_query("select customers_email_address from orders where orders_id='" . (int)$_GET['oID'] . "'");
        if (tep_db_num_rows($fetch_email_query)){
            $info = tep_db_fetch_array($fetch_email_query);
            if (!empty($info['customers_email_address'])){
                $customer_query = tep_db_query("select c.customers_firstname, c.customers_lastname, c.customers_email_address, c.customers_telephone, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, z.zone_name from orders o inner join customers c on o.customers_email_address=c.customers_email_address left join address_book ab on c.customers_default_address_id=ab.address_book_id left join zones z on ab.entry_zone_id=z.zone_id where o.orders_id='" . (int)$_GET['oID'] . "'");
                if (tep_db_num_rows($customer_query)){
                    $customer_info = tep_db_fetch_array($customer_query);
                    $name = $customer_info['customers_firstname'] . (!empty($customer_info['customers_lastname']) ? ' ' . $customer_info['customers_lastname'] : '');
                    $company = !empty($customer_info['entry_company']) ? $customer_info['entry_company'] : '';
                    $address1 = !empty($customer_info['entry_street_address']) ? $customer_info['entry_street_address'] : '';
                    $address2 = !empty($customer_info['entry_suburb']) ? $customer_info['entry_suburb'] : '';
                    $city = !empty($customer_info['entry_city']) ? $customer_info['entry_city'] : '';
                    $postcode = !empty($customer_info['entry_postcode']) ? $customer_info['entry_postcode'] : '';
                    $state = !empty($customer_info['zone_name']) ? $customer_info['zone_name'] : '';
                    $email = !empty($customer_info['customers_email_address']) ? $customer_info['customers_email_address'] : '';
                    $telephone = !empty($customer_info['customers_telephone']) ? $customer_info['customers_telephone'] : '';
                    tep_db_query("update orders set customers_name='" . tep_db_input($name) . "', customers_company='" . tep_db_input($company) . "', customers_street_address='" . tep_db_input($address1) . "', customers_suburb='" . tep_db_input($address1) . "', customers_city='" . tep_db_input($city) . "', customers_postcode='" . tep_db_input($postcode) . "', customers_state='" . tep_db_input($state) . "', customers_email_address='" . tep_db_input($email) . "' where orders_id='" . (int)$_GET['oID'] . "'");
                    
                    $response['name'] = $name; 
                    $response['company'] = $company; 
                    $response['address1'] = $address1; 
                    $response['address2'] = $address2; 
                    $response['city'] = $city;
                    $response['state'] = $state; 
                    $response['postcode'] = $postcode; 
                    $response['email'] = $email;
                    $response['telephone'] = $telephone; 
                    $response['match_located'] = '1';
                }
            }
        }
        echo json_encode($response);
        exit;
    }
    //EOF:range manager
  
  //2.  Update the orders_products table for qty, tax, name, or model
  if ($action == 'update_product_field') {
			
		if ($_GET['field'] == 'products_quantity') {
			// Update Inventory Quantity
			$order_query = tep_db_query("
			SELECT products_id, products_quantity 
			FROM " . TABLE_ORDERS_PRODUCTS . " 
			WHERE orders_id = '" . $_GET['oID'] . "'
			AND orders_products_id = '" . $_GET['pid'] . "'");
			$orders_product_info = tep_db_fetch_array($order_query);
			
			// stock check 
			
			if ($_GET['new_value'] != $orders_product_info['products_quantity']){
			$quantity_difference = ($_GET['new_value'] - $orders_product_info['products_quantity']);
				if (STOCK_LIMITED == 'true'){
				    tep_db_query("UPDATE " . TABLE_PRODUCTS . " SET 
					products_quantity = products_quantity - " . $quantity_difference . ",
					products_ordered = products_ordered + " . $quantity_difference . " 
					WHERE products_id = '" . $orders_product_info['products_id'] . "'");
					} else {
					tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
					products_ordered = products_ordered + " . $quantity_difference . "
					WHERE products_id = '" . $orders_product_info['products_id'] . "'");
				} //end if (STOCK_LIMITED == 'true'){
			} //end if ($_GET['new_value'] != $orders_product_info['products_quantity']){
		}//end if ($_GET['field'] = 'products_quantity'
		
	  tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . " SET " . $_GET['field'] . " = '" . oe_iconv($_GET['new_value']) . "' WHERE orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $_GET['oID'] . "'");
	
	
	
	  //generate responseText
	  echo $_GET['field'];

  }
  
  //3.  Update the orders_products table for price and final_price (interdependent values)
  if ($action == 'update_product_value_field') {
	  tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . " SET products_price = '" . tep_db_input(tep_db_prepare_input($_GET['price'])) . "', final_price = '" . tep_db_input(tep_db_prepare_input($_GET['final_price'])) . "' WHERE orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $_GET['oID'] . "'");
	  
	  //generate responseText
	  echo TABLE_ORDERS_PRODUCTS;

  }
  
    //4.  Update the orders_products_attributes table 
if ($action == 'update_attributes_field') {
	  
	  tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " SET " . $_GET['field'] . " = '" . oe_iconv($_GET['new_value']) . "' WHERE orders_products_attributes_id = '" . $_GET['aid'] . "' AND orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $_GET['oID'] . "'");
	  
	  if (isset($_GET['final_price'])) {
	    
		tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . " SET final_price = '" . tep_db_input(tep_db_prepare_input($_GET['final_price'])) . "' WHERE orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $_GET['oID'] . "'");
	  
	  }
	  
	  //generate responseText
	  echo $_GET['field'];

  }
  
    //5.  Update the orders_products_download table 
if ($action == 'update_downloads') {
	  tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " SET " . $_GET['field'] . " = '" . tep_db_input(tep_db_prepare_input($_GET['new_value'])) . "' WHERE orders_products_download_id = '" . $_GET['did'] . "' AND orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $_GET['oID'] . "'");
	  
	 //generate responseText
	  echo $_GET['field'];

  }
  
  //6. Update the currency of the order
  if ($action == 'update_currency') {
  	  tep_db_query("UPDATE " . TABLE_ORDERS . " SET currency = '" . tep_db_input(tep_db_prepare_input($_GET['currency'])) . "', currency_value = '" . tep_db_input(tep_db_prepare_input($_GET['currency_value'])) . "' WHERE orders_id = '" . $_GET['oID'] . "'");
  
  	 //generate responseText
	  echo $_GET['currency'];
  
  }//end if ($action == 'update_currency') {
  
  
  //7.  Update most any field in the orders_products table
  if ($action == 'delete_product_field') {
  
  		  	       //  Update Inventory Quantity
			      $order_query = tep_db_query("
			      SELECT products_id, products_quantity 
			      FROM " . TABLE_ORDERS_PRODUCTS . " 
			      WHERE orders_id = '" . $_GET['oID'] . "'
			      AND orders_products_id = '" . $_GET['pid'] . "'");
			      $order = tep_db_fetch_array($order_query);

		   			 //update quantities first
			       if (STOCK_LIMITED == 'true'){
				    tep_db_query("UPDATE " . TABLE_PRODUCTS . " SET 
					products_quantity = products_quantity + " . $order['products_quantity'] . ",
					products_ordered = products_ordered - " . $order['products_quantity'] . " 
					WHERE products_id = '" . (int)$order['products_id'] . "'");
					} else {
					tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
					products_ordered = products_ordered - " . $order['products_quantity'] . "
					WHERE products_id = '" . (int)$order['products_id'] . "'");
					}
		   
                    tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS . "  
	                              WHERE orders_id = '" . $_GET['oID'] . "'
					              AND orders_products_id = '" . $_GET['pid'] . "'");
      
	                tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
	                              WHERE orders_id = '" . $_GET['oID'] . "'
                                  AND orders_products_id = '" . $_GET['pid'] . "'");
	                
					tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . "
	                              WHERE orders_id = '" . $_GET['oID'] . "'
                                  AND orders_products_id = '" . $_GET['pid'] . "'");
								  
      //generate responseText
	  echo TABLE_ORDERS_PRODUCTS;

  }

  
  //8. Update the orders_status_history table
  if ($action == 'delete_comment') {
      
	  tep_db_query("DELETE FROM " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_status_history_id = '" . $_GET['cID'] . "' AND orders_id = '" . $_GET['oID'] . "'");
	  
	  //generate responseText
	  echo TABLE_ORDERS_STATUS_HISTORY;
	  
	  }
	  

  //9. Update the orders_status_history table
  if ($action == 'update_comment') {
      
	  tep_db_query("UPDATE " . TABLE_ORDERS_STATUS_HISTORY . " SET comments = '" . oe_iconv($_GET['comment']) . "' WHERE orders_status_history_id = '" . $_GET['cID'] . "' AND orders_id = '" . $_GET['oID'] . "'");
	  
	  //generate responseText
	  echo TABLE_ORDERS_STATUS_HISTORY;
	  
	  }
	  

  //10. Reload the shipping and order totals block 
    if ($action == 'reload_totals') {
         
	   $oID = $_POST['oID'];
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
    tep_db_query("UPDATE " . TABLE_ORDERS . " SET shipping_module = '" . $shipping['id'] . "' WHERE orders_id = '" . $_POST['oID'] . "'");
	   }
	   
		$order = new manualOrder($oID);
		$order->adjust_zones();
				
		$cart = new manualCart();
        $cart->restore_contents($oID);
        $total_count = $cart->count_contents();
        $total_weight = $cart->show_weight();
		
		$_SESSION['cart'] = $cart;
		
		// Get the shipping quotes
        $shipping_modules = new shipping;
        $shipping_quotes = $shipping_modules->quote();
		
		if (DISPLAY_PRICE_WITH_TAX == 'true') {//extract the base shipping cost or the ot_shipping module will add tax to it again
		   $module = substr($GLOBALS['shipping']['id'], 0, strpos($GLOBALS['shipping']['id'], '_'));
		   $tax = tep_get_tax_rate($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
		   $order->info['total'] -= ( $order->info['shipping_cost'] - ($order->info['shipping_cost'] / (1 + ($tax /100))) );
           $order->info['shipping_cost'] = ($order->info['shipping_cost'] / (1 + ($tax /100)));
		   }

 		//this is where we call the order total modules
		require( 'order_editor/order_total.php');
		$order_total_modules = new order_total();
		
		// remove avalara module #start
		if(($key = array_search('ot_avatax.php', $order_total_modules->modules)) !== false) {
			unset($order_total_modules->modules[$key]);
			$order_total_modules->modules = array_values($order_total_modules->modules);
		}
		// remove avalara module #ends	
		
        $order_totals = $order_total_modules->process();  
	    $current_ot_totals_array = array();
		$current_ot_titles_array = array();
		$written_ot_totals_array = array();
		$written_ot_titles_array = array();
		//how many weird arrays can I make today?
		
        $current_ot_totals_query = tep_db_query("select class, title from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$oID . "' order by sort_order");
        while ($current_ot_totals = tep_db_fetch_array($current_ot_totals_query)) {
          $current_ot_totals_array[] = $current_ot_totals['class'];
		  $current_ot_titles_array[] = $current_ot_totals['title'];
        }
		
        tep_db_query("delete from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$oID . "'");
        
        $j=1; //giving something a sort order of 0 ain't my bag baby
		$new_order_totals = array();
		/*
		if (is_array($_POST['update_totals'])) { //1
          foreach($_POST['update_totals'] as $total_index => $total_details) { //2
            extract($total_details, EXTR_PREFIX_ALL, "ot");
		    echo $ot_title. '<br>';
		    }
		   }
		 exit();
		 */   
	    if (is_array($_POST['update_totals'])) { //1
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
                  $new_order_totals[] = array('title' => tep_db_input($ot_title),
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
                                  'title' => oe_iconv($new_order_totals[$i]['title']),
                                  'text' => $new_order_totals[$i]['text'],
                                  'value' => $new_order_totals[$i]['value'], 
                                  'class' => $new_order_totals[$i]['code'], 
                                  'sort_order' => $new_order_totals[$i]['sort_order']);
          tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
        }


        $order = new manualOrder($oID);
        $shippingKey = $order->adjust_totals($oID);
        $order->adjust_zones();
        
        $cart = new manualCart();
        $cart->restore_contents($oID);
        $total_count = $cart->count_contents();
        $total_weight = $cart->show_weight();

		
  
  ?>
  
		<table width="100%">
		 <tr><td>
			 
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
			  <tr>
                <td valign="top" width="100%">
				 <br>
				   <div>
					<?php /* <a href="javascript:openWindow('<?php echo tep_href_link(FILENAME_ORDERS_EDIT_ADD_PRODUCT, 'oID=' . $_POST['oID'] . '&step=1'); ?>','addProducts');"><?php echo tep_image_button('button_add_article.gif', TEXT_ADD_NEW_PRODUCT); ?></a><input type="hidden" name="subaction" value=""> */ ?>
					<input name="item_no" id="item_no" />
					</div>
					<br>
				</td>
               
             
			  <!-- order_totals bof //-->
                <td align="right" rowspan="2" valign="top" nowrap class="dataTableRow" style="border: 1px solid #C9C9C9;">
                <table border="0" cellspacing="0" cellpadding="2">
                  <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" width="15" nowrap onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_TOTALS); ?>')"; onMouseout="hideddrivetip()"><img src="images/icon_info.gif" border="0" width="13" height="13"></td>
                    <td class="dataTableHeadingContent" nowrap><?php echo TABLE_HEADING_OT_TOTALS; ?></td>
                    <td class="dataTableHeadingContent" colspan="2" nowrap><?php echo TABLE_HEADING_OT_VALUES; ?></td>
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
    if ((!strstr($order->totals[$i]['class'], 'ot_custom')) && ($order->totals[$i]['class'] != 'ot_shipping')) {
      echo '                  <tr class="' . $rowStyle . '">' . "\n";
      if ($order->totals[$i]['class'] != 'ot_total') {
        echo '                    <td class="dataTableContent" valign="middle" height="15"><span id="update_totals['.$i.']"><a href="javascript:setCustomOTVisibility(\'update_totals['.($i+1).']\', \'visible\', \'update_totals['.$i.']\');">' . tep_image('order_editor/images/plus.gif', IMAGE_ADD_NEW_OT) . '</a></span></td>' . "\n";
      } else {
        echo '                    <td class="dataTableContent" valign="middle">&nbsp;</td>' . "\n";
      }
      
      echo '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][title]" value=\'' . trim($order->totals[$i]['title']) . '\' readonly="readonly"></td>' . "\n";
	  
      if ($order->info['currency'] != DEFAULT_CURRENCY) echo '                    <td class="dataTableContent">&nbsp;</td>' . "\n";
      echo '                    <td align="right" class="dataTableContent" nowrap>' . $order->totals[$i]['text'] . '<input name="update_totals['.$i.'][value]" type="hidden" value="' . number_format((float)$order->totals[$i]['value'], 2, '.', '') . '"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"></td>' . "\n" .
           '                  </tr>' . "\n";
    } else {
      if ($i % 2) {
        echo '                  <tr class="' . $rowStyle . '" id="update_totals['.$i.']" style="visibility: hidden; display: none;">' . "\n" .
             '                    <td class="dataTableContent" valign="middle" height="15"><a href="javascript:setCustomOTVisibility(\'update_totals['.($i).']\', \'hidden\', \'update_totals['.($i-1).']\');">' . tep_image('order_editor/images/minus.gif', IMAGE_REMOVE_NEW_OT) . '</a></td>' . "\n";
      } else {
        echo '                  <tr class="' . $rowStyle . '">' . "\n" .
             '                    <td class="dataTableContent" valign="middle" height="15"><span id="update_totals['.$i.']"><a href="javascript:setCustomOTVisibility(\'update_totals['.($i+1).']\', \'visible\', \'update_totals['.$i.']\');">' . tep_image('order_editor/images/plus.gif', IMAGE_ADD_NEW_OT) . '</a></span></td>' . "\n";
      }

      echo '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][title]" id="'.$id.'[title]" value=\'' . trim($order->totals[$i]['title']) . '\'  onChange="obtainTotals()"></td>' . "\n" .
           '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][value]" id="'.$id.'[value]" value="' . number_format((float)$order->totals[$i]['value'], 2, '.', '') . '" size="6"  onChange="obtainTotals()"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"><input name="update_totals['.$i.'][id]" type="hidden" value="' . $shipping_module_id . '" id="' . $id . '[id]"></td>' . "\n";
      if ($order->info['currency'] != DEFAULT_CURRENCY) echo '                    <td align="right" class="dataTableContent" nowrap>' . $order->totals[$i]['text'] . '</td>' . "\n";
      echo '                  </tr>' . "\n";
    }
  }
?>
                </table></td>
                <!-- order_totals_eof //-->
              </tr>              
              <tr>
                <td valign="bottom">
                
<?php 
/*
  if (sizeof($shipping_quotes) > 0) {
?>
                <!-- shipping_quote bof //-->
                <table border="0" width="550" cellspacing="0" cellpadding="2" style="border: 1px solid #C9C9C9;">
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" colspan="3"><?php echo TABLE_HEADING_SHIPPING_QUOTES; ?></td>
                  </tr>
<?php
    $r = 0;
    for ($i=0, $n=sizeof($shipping_quotes); $i<$n; $i++) {
      for ($j=0, $n2=sizeof($shipping_quotes[$i]['methods']); $j<$n2; $j++) {
        $r++;
		if (!isset($shipping_quotes[$i]['tax'])) $shipping_quotes[$i]['tax'] = 0;
        $rowClass = ((($r/2) == (floor($r/2))) ? 'dataTableRowOver' : 'dataTableRow');
        echo '                  <tr class="' . $rowClass . '" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this, \'' . $rowClass . '\')" onclick="selectRowEffect(this, ' . $r . '); setShipping(' . $r . ');">' . "\n" .
             
    '      <td class="dataTableContent" valign="top" align="left" width="15px">' . "\n" .
	
	'      <input type="radio" name="shipping" id="shipping_radio_' . $r . '" value="' . $shipping_quotes[$i]['id'] . '_' . $shipping_quotes[$i]['methods'][$j]['id'].'">' . "\n" .
			 
	'      <input type="hidden" id="update_shipping['.$r.'][title]" name="update_shipping['.$r.'][title]" value=\''.$shipping_quotes[$i]['module'] . ' (' . $shipping_quotes[$i]['methods'][$j]['title'].'):\'>' . "\n" .
			
    '      <input type="hidden" id="update_shipping['.$r.'][value]" name="update_shipping['.$r.'][value]" value="'.tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']).'">' . "\n" .
	
	'      <input type="hidden" id="update_shipping[' . $r . '][id]" name="update_shipping[' . $r . '][id]" value="' . $shipping_quotes[$i]['id'] . '_' . $shipping_quotes[$i]['methods'][$j]['id'] . '">' . "\n" .
    
	'        <td class="dataTableContent" valign="top">' . $shipping_quotes[$i]['module'] . ' (' . $shipping_quotes[$i]['methods'][$j]['title'] . '):</td>' . "\n" . 
    
	'        <td class="dataTableContent" align="right">' . $currencies->format(tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" . 
             '                  </tr>';
      }
    }
?>
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" colspan="3"><?php echo sprintf(TEXT_PACKAGE_WEIGHT_COUNT, $shipping_num_boxes . ' x ' . $shipping_weight, $total_count); ?></td>
                  </tr>
                </table>
                <!-- shipping_quote_eof //-->
<?php
  } else {
  echo AJAX_NO_QUOTES;
  }
  */
?>
                </td>
              </tr> 
            </table>
			
		  
		  </td></tr>
		</table>
	   
  
<?php   }//end if ($action == 'reload_shipping') {  
     
	
	//11. insert new comments
	 if ($action == 'insert_new_comment') {  
	 
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
			   
   // UPDATE STATUS HISTORY & SEND EMAIL TO CUSTOMER IF NECESSARY #####
        $usps_track_num = tep_db_prepare_input($_GET['usps_track_num']);
		$usps_track_num2 = tep_db_prepare_input($_GET['usps_track_num2']);
        $ups_track_num = tep_db_prepare_input($_GET['ups_track_num']);
		$ups_track_num2 = tep_db_prepare_input($_GET['ups_track_num2']);
        $fedex_track_num = tep_db_prepare_input($_GET['fedex_track_num']);
		$fedex_track_num2 = tep_db_prepare_input($_GET['fedex_track_num2']);
        $dhl_track_num = tep_db_prepare_input($_GET['dhl_track_num']);
		$dhl_track_num2 = tep_db_prepare_input($_GET['dhl_track_num2']);
		$oID = tep_db_prepare_input($_GET['oID']);
    $check_status_query = tep_db_query("
	                      SELECT customers_name, customers_email_address, usps_track_num, usps_track_num2, ups_track_num, ups_track_num2, fedex_track_num, fedex_track_num2, dhl_track_num, dhl_track_num2, orders_status, date_purchased 
	                      FROM " . TABLE_ORDERS . " 
						  WHERE orders_id = '" . $_GET['oID'] . "'");
						  
    $check_status = tep_db_fetch_array($check_status_query); 
	
  if (($check_status['orders_status'] != $_GET['status']) || (tep_not_null($_GET['comments']))) {

        tep_db_query("UPDATE " . TABLE_ORDERS . " SET 
					  orders_status = '" . tep_db_input($_GET['status']) . "', 
                      last_modified = now() 
                      WHERE orders_id = '" . $_GET['oID'] . "'");
		
		 /*// Notify Customer ?
      $customer_notified = '0';
			if (isset($_GET['notify']) && ($_GET['notify'] == 'true')) {
			  $notify_comments = '';
			  if (isset($_GET['notify_comments']) && ($_GET['notify_comments'] == 'true')) {
			   $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, oe_iconv($_GET['comments'])) . "\n\n";
			  }
			  $email = STORE_NAME . "\n" .
			           EMAIL_SEPARATOR . "\n" . 
					   EMAIL_TEXT_ORDER_NUMBER . ' ' . $_GET['oID'] . "\n" . 
	EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $_GET['oID'], 'SSL') . "\n" . 
					   EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . 
					   sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$_GET['status']]) . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE2);
			  
			  tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
			  
			  $customer_notified = '1';
			}*/
			// Notify Customer ?
		 // Notify Customer ?
//Package Tracking Plus BEGIN
          $customer_notified = '0';
          if ($_GET['notify'] == 'on' & ($usps_track_num == '' & $usps_track_num2 == '' & $ups_track_num == '' & $ups_track_num2 == '' & $fedex_track_num == '' & $fedex_track_num2 == '' & $dhl_track_num == '' & $dhl_track_num2 == '' ) ) {
            $notify_comments = '';
            if ($_GET['notify_comments'] == 'on') {
              $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n";
              if ($comments == null)
                $notify_comments = '';
                        }

            $email = 'Dear ' . $check_status['customers_name'] . ',' . "\n\n" . STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . "<a HREF='" . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "'>" .  'order_id=' . (int)$oID . "</a>\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n" . EMAIL_SEPARATOR . "\n\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);
            tep_mail($check_status['customers_name'], $check_status['customers_email_address'], STORE_NAME . ' ' . EMAIL_TEXT_SUBJECT_1. (int)$oID . EMAIL_TEXT_SUBJECT_2 . $orders_status_array[$status], $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
            $customer_notified = '1';

  }else   if ($_GET['notify'] == 'on' & ($usps_track_num == '' or $usps_track_num2 == '' or $ups_track_num == '' or $ups_track_num2 == '' or $fedex_track_num == '' or $fedex_track_num2 == '' or $dhl_track_num == '' or $dhl_track_num2 == '' ) ) {
            $notify_comments = '';
            if ($_GET['notify_comments'] == 'on') {
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

  }else   if ($_GET['notify'] == 'on' & (tep_not_null($usps_track_num) & tep_not_null($usps_track_num2) & tep_not_null($ups_track_num) & tep_not_null($ups_track_num2) & tep_not_null($fedex_track_num) & tep_not_null($fedex_track_num2) & tep_not_null($dhl_track_num) & tep_not_null($dhl_track_num2) ) ) {
          $notify_comments = '';
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
          if ($_GET['notify_comments'] == 'on') {
            $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n";
            if ($comments == null)
                $notify_comments = '';
                        }

            $email = 'Dear ' . $check_status['customers_name'] . ',' . "\n\n" . STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . "<a HREF='" . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "'>" .  'order_id=' . (int)$oID . "</a>\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_TRACKING_NUMBER . "\n" . $usps_text . $usps_track . $usps_text2 . $usps_track2 . $ups_text . $ups_track . $ups_text2 . $ups_track2 . $fedex_text . $fedex_track . $fedex_text2 . $fedex_track2 . $dhl_text . $dhl_track . $dhl_text2 . $dhl_track2 . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);
            tep_mail($check_status['customers_name'], $check_status['customers_email_address'], STORE_NAME . ' ' . EMAIL_TEXT_SUBJECT_1 . (int)$oID . EMAIL_TEXT_SUBJECT_2 . $orders_status_array[$status], $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
            $customer_notified = '1';
         }
//Package Tracking Plus END

			  
          		
			tep_db_query("INSERT into " . TABLE_ORDERS_STATUS_HISTORY . " 
			(orders_id, orders_status_id, date_added, customer_notified, comments) 
			values ('" . tep_db_input($_GET['oID']) . "', 
				'" . tep_db_input($_GET['status']) . "', 
				now(), 
				" . tep_db_input($customer_notified) . ", 
				'" . oe_iconv($_GET['comments'])  . "')");
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


?>
  <table style="border: 1px solid #C9C9C9;" cellspacing="0" cellpadding="2" class="dataTableRow" id="commentsTable">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_DELETE; ?></td>
    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></td>
    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_STATUS; ?></td>
   <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_COMMENTS; ?></td>
   </tr>
<?php
$r = 0;
$orders_history_query = tep_db_query("SELECT orders_status_history_id, orders_status_id, date_added, customer_notified, comments 
                                    FROM " . TABLE_ORDERS_STATUS_HISTORY . " 
									WHERE orders_id = '" . tep_db_prepare_input($_GET['oID']) . "' 
									ORDER BY date_added");
if (tep_db_num_rows($orders_history_query)) {
  while ($orders_history = tep_db_fetch_array($orders_history_query)) {
          
		$r++;
        $rowClass = ((($r/2) == (floor($r/2))) ? 'dataTableRowOver' : 'dataTableRow');
        
	     echo '  <tr class="' . $rowClass . '" id="commentRow' . $orders_history['orders_status_history_id'] . '" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this, \'' . $rowClass . '\')">' . "\n" .
         '	  <td class="smallText" align="center"><div id="do_not_delete"><input name="update_comments[' . $orders_history['orders_status_history_id'] . '][delete]" type="checkbox" onClick="updateCommentsField(\'delete\', \'' . $orders_history['orders_status_history_id'] . '\', this.checked, \'\', this)"></div></td>' . "\n" . 
		 '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
         '    <td class="smallText" align="center">' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n" .
         '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
         '    <td class="smallText" align="center">';
    if ($orders_history['customer_notified'] == '1') {
      echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK) . "</td>\n";
    } else {
      echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS) . "</td>\n";
    }
    echo '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
         '    <td class="smallText" align="left">' . $orders_status_array[$orders_history['orders_status_id']] . '</td>' . "\n";
    echo '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
         '    <td class="smallText" align="left">' . 
  
  tep_draw_textarea_field("update_comments[" . $orders_history['orders_status_history_id'] . "][comments]", "soft", "40", "5", 
  "" .	tep_db_output($orders_history['comments']) . "", "onChange=\"updateCommentsField('update', '" . $orders_history['orders_status_history_id'] . "', 'false', encodeURIComponent(this.value))\"") . '' . "\n" .
		 
		 '    </td>' . "\n";
 
    echo '  </tr>' . "\n";
  
      }
    } else {
      echo '  <tr>' . "\n" .
       '    <td class="smallText" colspan="5">' . TEXT_NO_ORDER_HISTORY . '</td>' . "\n" .
       '  </tr>' . "\n";
      }
	
  ?>
  
  </table>
  
  <?php   }  // end if ($action == 'insert_new_comment') { 	 
     
	 //12. insert shipping method when one doesn't already exist
     if ($action == 'insert_shipping') {
	  
	  $order = new manualOrder($_GET['oID']);
	  
	  $Query = "INSERT INTO " . TABLE_ORDERS_TOTAL . " SET
	                orders_id = '" . $_GET['oID'] . "', 
					title = '" . tep_db_input(urldecode($_GET['title'])) . "', 
					text = '" . $currencies->format($_GET['value'], true, $order->info['currency'], $order->info['currency_value']) ."',
					value = '" . $_GET['value'] . "',
					class = 'ot_shipping',
					sort_order = '" . $_GET['sort_order'] . "'";
					tep_db_query($Query);
					
	  tep_db_query("UPDATE " . TABLE_ORDERS . " SET shipping_module = '" . $_GET['id'] . "' WHERE orders_id = '" . $_GET['oID'] . "'");
	
	    $order = new manualOrder($_GET['oID']);
        $shippingKey = $order->adjust_totals($_GET['oID']);
        $order->adjust_zones();
        
        $cart = new manualCart();
        $cart->restore_contents($_GET['oID']);
        $total_count = $cart->count_contents();
        $total_weight = $cart->show_weight();
		$_SESSION['cart'] = $cart;
		// Get the shipping quotes
        $shipping_modules = new shipping;
        $shipping_quotes = $shipping_modules->quote();
  
  ?>
  
		<table width="100%">
		 <tr><td>
			 
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
			  <tr>
                <td valign="top" width="100%">
				 <br>
				   <div>
					<a href="javascript:openWindow('<?php echo tep_href_link(FILENAME_ORDERS_EDIT_ADD_PRODUCT, 'oID=' . $_GET['oID'] . '&step=1'); ?>','addProducts');"><?php echo tep_image_button('button_add_article.gif', TEXT_ADD_NEW_PRODUCT); ?></a><input type="hidden" name="subaction" value="">
					<input name="item_no" id="item_no" />
					</div>
					<br>
				</td>
               
             
			  <!-- order_totals bof //-->
                <td align="right" rowspan="2" valign="top" nowrap class="dataTableRow" style="border: 1px solid #C9C9C9;">
                <table border="0" cellspacing="0" cellpadding="2">
                  <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" width="15" nowrap onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_TOTALS); ?>')"; onMouseout="hideddrivetip()"><img src="images/icon_info.gif" border="0" width="13" height="13" onLoad="reloadTotals()"></td>
                    <td class="dataTableHeadingContent" nowrap><?php echo TABLE_HEADING_OT_TOTALS; ?></td>
                    <td class="dataTableHeadingContent" colspan="2" nowrap><?php echo TABLE_HEADING_OT_VALUES; ?></td>
                  </tr>
<?php
  for ($i=0; $i<sizeof($order->totals); $i++) {
   
    $id = $order->totals[$i]['class'];
	
    if ($order->totals[$i]['class'] == 'ot_shipping') {
	    $shipping_module_id = $order->info['shipping_id'];
	  } else {
	    $shipping_module_id = '';
	  } //end if ($order->totals[$i]['class'] == 'ot_shipping') {
   
    $rowStyle = (($i % 2) ? 'dataTableRowOver' : 'dataTableRow');
    if ( ($order->totals[$i]['class'] == 'ot_total') || ($order->totals[$i]['class'] == 'ot_subtotal') || ($order->totals[$i]['class'] == 'ot_tax') || ($order->totals[$i]['class'] == 'ot_loworderfee') ) {
      echo '                  <tr class="' . $rowStyle . '">' . "\n";
      if ($order->totals[$i]['class'] != 'ot_total') {
        echo '                    <td class="dataTableContent" valign="middle" height="15"><span id="update_totals['.$i.']"><a href="javascript:setCustomOTVisibility(\'update_totals['.($i+1).']\', \'visible\', \'update_totals['.$i.']\');">' . tep_image('order_editor/images/plus.gif', IMAGE_ADD_NEW_OT) . '</a></span></td>' . "\n";
      } else {
        echo '                    <td class="dataTableContent" valign="middle">&nbsp;</td>' . "\n";
      }
      
      echo '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][title]" value="' . trim($order->totals[$i]['title']) . '" readonly="readonly"></td>' . "\n";
	  
      if ($order->info['currency'] != DEFAULT_CURRENCY) echo '                    <td class="dataTableContent">&nbsp;</td>' . "\n";
      echo '                    <td align="right" class="dataTableContent" nowrap>' . $order->totals[$i]['text'] . '<input name="update_totals['.$i.'][value]" type="hidden" value="' . number_format((float)$order->totals[$i]['value'], 2, '.', '') . '"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"></td>' . "\n" .
           '                  </tr>' . "\n";
    } else {
      if ($i % 2) {
        echo '                  <tr class="' . $rowStyle . '" id="update_totals['.$i.']" style="visibility: hidden; display: none;">' . "\n" .
             '                    <td class="dataTableContent" valign="middle" height="15"><a href="javascript:setCustomOTVisibility(\'update_totals['.($i).']\', \'hidden\', \'update_totals['.($i-1).']\');">' . tep_image('order_editor/images/minus.gif', IMAGE_REMOVE_NEW_OT) . '</a></td>' . "\n";
      } else {
        echo '                  <tr class="' . $rowStyle . '">' . "\n" .
             '                    <td class="dataTableContent" valign="middle" height="15"><span id="update_totals['.$i.']"><a href="javascript:setCustomOTVisibility(\'update_totals['.($i+1).']\', \'visible\', \'update_totals['.$i.']\');">' . tep_image('order_editor/images/plus.gif', IMAGE_ADD_NEW_OT) . '</a></span></td>' . "\n";
      }

      echo '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][title]" id="'.$id.'[title]" value="' . trim($order->totals[$i]['title']) . '"  onChange="obtainTotals()"></td>' . "\n" .
           '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][value]" id="'.$id.'[value]" value="' . number_format((float)$order->totals[$i]['value'], 2, '.', '') . '" size="6"  onChange="obtainTotals()"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"><input name="update_totals['.$i.'][id]" type="hidden" value="' . $shipping_module_id . '" id="' . $id . '[id]"></td>' . "\n";
      if ($order->info['currency'] != DEFAULT_CURRENCY) echo '                    <td align="right" class="dataTableContent" nowrap>' . $order->totals[$i]['text'] . '</td>' . "\n";
      echo '                  </tr>' . "\n";
    }
  }
?>
                </table></td>
                <!-- order_totals_eof //-->
              </tr>              
              <tr>
                <td valign="bottom">
                
<?php 
  if (sizeof($shipping_quotes) > 0) {
?>
                <!-- shipping_quote bof //-->
                <table border="0" width="550" cellspacing="0" cellpadding="2" style="border: 1px solid #C9C9C9;">
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" colspan="3"><?php echo TABLE_HEADING_SHIPPING_QUOTES; ?></td>
                  </tr>
<?php
    $r = 0;
    for ($i=0, $n=sizeof($shipping_quotes); $i<$n; $i++) {
      for ($j=0, $n2=sizeof($shipping_quotes[$i]['methods']); $j<$n2; $j++) {
        $r++;
		if (!isset($shipping_quotes[$i]['tax'])) $shipping_quotes[$i]['tax'] = 0;
        $rowClass = ((($r/2) == (floor($r/2))) ? 'dataTableRowOver' : 'dataTableRow');
        echo '                  <tr class="' . $rowClass . '" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this, \'' . $rowClass . '\')" onclick="selectRowEffect(this, ' . $r . '); setShipping(' . $r . ');">' . "\n" .
                 
    '   <td class="dataTableContent" valign="top" align="left" width="15px">' . "\n" .
	
	'   <input type="radio" name="shipping" id="shipping_radio_' . $r . '" value="' . $shipping_quotes[$i]['id'] . '_' . $shipping_quotes[$i]['methods'][$j]['id'].'">' . "\n" .
			 
	'   <input type="hidden" id="update_shipping['.$r.'][title]" name="update_shipping['.$r.'][title]" value="'.$shipping_quotes[$i]['module'] . ' (' . $shipping_quotes[$i]['methods'][$j]['title'].'):">' . "\n" .
			
    '   <input type="hidden" id="update_shipping['.$r.'][value]" name="update_shipping['.$r.'][value]" value="'.tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']).'">' . "\n" .
	
	'      <input type="hidden" id="update_shipping[' . $r . '][id]" name="update_shipping[' . $r . '][id]" value="' . $shipping_quotes[$i]['id'] . '_' . $shipping_quotes[$i]['methods'][$j]['id'] . '">' . "\n" .

			 '<td class="dataTableContent" valign="top">' . $shipping_quotes[$i]['module'] . ' (' . $shipping_quotes[$i]['methods'][$j]['title'] . '):</td>' . "\n" . 

			 '<td class="dataTableContent" align="right">' . $currencies->format(tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" . 
             '                  </tr>';
      }
    }
?>
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" colspan="3"><?php echo sprintf(TEXT_PACKAGE_WEIGHT_COUNT, $shipping_num_boxes . ' x ' . $shipping_weight, $total_count); ?></td>
                  </tr>
                </table>
                <!-- shipping_quote_eof //-->
  
  <?php
     } else {
     echo AJAX_NO_QUOTES;
     }
   ?>
                </td>
              </tr> 
            </table>
			
		  
		  </td></tr>
		</table>
	 
   <?php	 } //end if ($action == 'insert_shipping') {  

  //13. new order email 
   
    if ($action == 'new_order_email')  {
	
		$order = new manualOrder($_GET['oID']);
		
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
						EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$_GET['oID'] . "\n" .
                        EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$_GET['oID'], 'SSL') . "\n" .
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
		//BOF:mod 20120716
		/*
		//EOF:mod 20120716
		$email_order .= $order->customer['city'] . "\n";
		//BOF:mod 20120716
		*/
		$email_order .= $order->delivery['city'] . "\n";
		//EOF:mod 20120716
		                if ($order->delivery['state']) {
		                  $email_order .= $order->delivery['state'] . "\n";
	                    }
		//BOF:mod 20120716
		/*
		//EOF:mod 20120716
		$email_order .= $order->customer['postcode'] . "\n" .
						$order->delivery['country'] . "\n";
		//BOF:mod 20120716
		*/
		$email_order .= $order->delivery['postcode'] . "\n" .
						$order->delivery['country'] . "\n";
		//EOF:mod 20120716
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
		//BOF:mod 20120716
		/*
		//EOF:mod 20120716
		$email_order .= $order->customer['city'] . "\n";
		//BOF:mod 20120716
		*/
		$email_order .= $order->billing['city'] . "\n";
		//EOF:mod 20120716
		                if ($order->billing['state']) {
		                  $email_order .= $order->billing['state'] . "\n";
	                    }
		//BOF:mod 20120716
		/*
		//EOF:mod 20120716
		$email_order .= $order->customer['postcode'] . "\n" .
						$order->billing['country'] . "\n\n";
		//BOF:mod 20120716
		*/
		$email_order .= $order->billing['postcode'] . "\n" .
						$order->billing['country'] . "\n\n";
		//EOF:mod 20120716

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
      
	  //code for plain text emails which changes the € sign to EUR, otherwise the email will show ? instead of €
      $email_order = str_replace("€","EUR",$email_order);
	  $email_order = str_replace("&nbsp;"," ",$email_order);

	  //code which replaces the <br> tags within EMAIL_TEXT_PAYMENT_INFO and EMAIL_TEXT_FOOTER with the proper \n
	  $email_order = str_replace("<br>","\n",$email_order);

	  //send the email to the customer
	  tep_mail($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

   // send emails to other people as necessary
  if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
    tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
  }
  
  ?>
	
	<table>
	  <tr>
	    <td class="messageStackSuccess">
		  <?php echo tep_image(DIR_WS_ICONS . 'success.gif', ICON_SUCCESS) . '&nbsp;' . sprintf(AJAX_SUCCESS_EMAIL_SENT, $order->customer['email_address']); ?>
		</td>
	  </tr>
	</table>
	
	<?php } //end if ($action == 'new_order_email')  {  ?>