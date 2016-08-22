<?php

/*

  $Id: order.php,v 1.7 2003/06/20 16:23:08 hpdl Exp $



  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.

*/



  class order {

    var $info, $totals, $products, $customer, $delivery;



    function order($order_id) {

      $this->info = array();

      $this->totals = array();

      $this->products = array();

      $this->customer = array();

      $this->delivery = array();



      $this->query($order_id);

    }



    function query($order_id) {

     /*Query Upadetd for Order Tracking*/

	  

	 //   $order_query = tep_db_query("select customers_id, customers_name, customers_company, customers_street_address, customers_suburb, customers_city, customers_postcode, customers_state, customers_country, customers_telephone, customers_email_address, customers_address_format_id, delivery_name, delivery_company, delivery_street_address, delivery_suburb, delivery_city, delivery_postcode, delivery_state, delivery_country, delivery_address_format_id, billing_name, billing_company, billing_street_address, billing_suburb, billing_city, billing_postcode, billing_state, billing_country, billing_address_format_id, payment_method, cc_type, cc_owner, cc_number, cc_expires, currency, currency_value, date_purchased, orders_status, last_modified, usps_track_num, usps_track_num2, ups_track_num, ups_track_num2, fedex_track_num, fedex_track_num2, dhl_track_num, dhl_track_num2, extra_track_num, cc_cvv from " . TABLE_ORDERS . " where orders_id = '" . (int)$order_id . "'");

              // BOF add SPPC customers_group_name to the info

      $order_query = tep_db_query("select customers_name, customers_company, customers_street_address, customers_suburb, customers_city, customers_postcode, customers_state, customers_country, o.customers_telephone, o.customers_email_address, customers_address_format_id, customers_group_name, delivery_name, delivery_company, delivery_street_address, delivery_suburb, delivery_city, delivery_postcode, delivery_state, delivery_country, delivery_address_format_id, billing_name, billing_company, billing_street_address, billing_suburb, billing_city, billing_postcode, billing_state, billing_country, billing_address_format_id, payment_method, cc_type, cc_owner, cc_number, cc_expires, transaction_id, is_capture, currency, currency_value, date_purchased, orders_status, last_modified, ip_address, extra_track_num from " . TABLE_ORDERS . " o left join " . TABLE_CUSTOMERS . " using(customers_id) left join " . TABLE_CUSTOMERS_GROUPS . " using(customers_group_id) where orders_id = '" . (int)$order_id . "'");

      // EOF add SPPC customer_group_name to the info



	    

      $order = tep_db_fetch_array($order_query);



      $totals_query = tep_db_query("select title, text from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$order_id . "' order by sort_order");

      while ($totals = tep_db_fetch_array($totals_query)) {

        $this->totals[] = array('title' => $totals['title'],

                                'text' => $totals['text']);

      }



     //Array updated for Order Tracking



 $this->info = array('currency' => $order['currency'],

                          'currency_value' => $order['currency_value'],

                          'payment_method' => $order['payment_method'],

                          'cc_type' => $order['cc_type'],

                          'cc_owner' => $order['cc_owner'],

                          'cc_number' => $order['cc_number'],

                          'cc_expires' => $order['cc_expires'],

                          'cc_cvv' => $order['cc_cvv'],

                          'is_capture' => $order['is_capture'],

                          'transaction_id' => $order['transaction_id'],

                          'date_purchased' => $order['date_purchased'],

                          'orders_status' => $order['orders_status'],

                           'usps_track_num' => $order['usps_track_num'],

                          'usps_track_num2' => $order['usps_track_num2'],

                          'ups_track_num' => $order['ups_track_num'],

                          'ups_track_num2' => $order['ups_track_num2'],

                          'fedex_track_num' => $order['fedex_track_num'],

                          'fedex_track_num2' => $order['fedex_track_num2'],

                          'dhl_track_num' => $order['dhl_track_num'],

                          'dhl_track_num2' => $order['dhl_track_num2'],

                          'extra_track_num' => $order['extra_track_num'],

                          'last_modified' => $order['last_modified'],

                          'ip_address' =>  $order['ip_address'],

                          );

                          

      $this->customer = array('id' => $order['customers_id'],

	                          'name' => $order['customers_name'],

                              'company' => $order['customers_company'],

                              'street_address' => $order['customers_street_address'],

                              'suburb' => $order['customers_suburb'],

                              'city' => $order['customers_city'],

                              'postcode' => $order['customers_postcode'],

                              'state' => $order['customers_state'],

                              'country' => $order['customers_country'],

                              'format_id' => $order['customers_address_format_id'],

                                        // BOF SPPC

                              'customers_group_name' => $order['customers_group_name'],

                              // EOF SPPC



                              'telephone' => $order['customers_telephone'],

                              'email_address' => $order['customers_email_address']);



      $this->delivery = array('name' => $order['delivery_name'],

                              'company' => $order['delivery_company'],

                              'street_address' => $order['delivery_street_address'],

                              'suburb' => $order['delivery_suburb'],

                              'city' => $order['delivery_city'],

                              'postcode' => $order['delivery_postcode'],

                              'state' => $order['delivery_state'],

                              'country' => $order['delivery_country'],

                              'format_id' => $order['delivery_address_format_id']);



      $this->billing = array('name' => $order['billing_name'],

                             'company' => $order['billing_company'],

                             'street_address' => $order['billing_street_address'],

                             'suburb' => $order['billing_suburb'],

                             'city' => $order['billing_city'],

                             'postcode' => $order['billing_postcode'],

                             'state' => $order['billing_state'],

                             'country' => $order['billing_country'],

                             'format_id' => $order['billing_address_format_id']);

//MVS Start

      $orders_shipping_id = '';

      $check_new_vendor_data_query = tep_db_query("select orders_shipping_id, orders_id, vendors_id, vendors_name, shipping_module, shipping_method, shipping_cost, vendor_order_sent from " . TABLE_ORDERS_SHIPPING . " where orders_id = '" . (int) $order_id . "'");

      while ($checked_data = tep_db_fetch_array($check_new_vendor_data_query)) {

        $this->orders_shipping_id = $checked_data['orders_shipping_id'];

      }

      

      if (tep_not_null($this->orders_shipping_id)) {

        $index2 = 0;

        //let's get the Vendors

        $vendor_data_query = tep_db_query("select orders_shipping_id, orders_id, vendors_id, vendors_name, shipping_module, shipping_method, shipping_cost, shipping_tax, vendor_order_sent from " . TABLE_ORDERS_SHIPPING . " where orders_id = '" . (int) $order_id . "'");

        while ($vendor_order = tep_db_fetch_array($vendor_data_query)) {

/*

          $this->products[$index2] = array (

            'Vid' => $vendor_order['vendors_id'],

            'Vname' => $vendor_order['vendors_name'],

            'Vmodule' => $vendor_order['shipping_module'],

            'Vmethod' => $vendor_order['shipping_method'],

            'Vcost' => $vendor_order['shipping_cost'],

            'Vship_tax' => $vendor_order['shipping_tax'],

            'Vorder_sent' => $vendor_order['vendor_order_sent'], //a yes=sent a no=not sent

            'Vnoname' => 'Shipper',

            'spacer' => '-'

          );



          
		  //BOF:mvs_internal_mod

		  /*

		  //EOF:mvs_internal_mod

          $orders_products_query = tep_db_query("select orders_products_id, products_name, products_model, products_price, products_tax, products_quantity, final_price, vendors_id from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int) $order_id . "' and vendors_id = '" . (int) $vendor_order['vendors_id'] . "'");

		  //BOF:mvs_internal_mod

		  */

		//$orders_products_query = tep_db_query("select a.orders_products_id, a.products_name, a.products_model, a.products_price, a.products_tax, a.products_quantity,a.final_price, a.vendors_id, b.xml_feed_id, c.internal_id, c.orders_products_status, a.is_ok_for_shipping, d.vendors_name from " . TABLE_ORDERS_PRODUCTS . " a left join products_extended b on a.products_id=b.osc_products_id left join feeds_orders_products_retailer_to_obn c on a.orders_products_id=c.orders_products_id inner join vendors d on a.vendors_id=d.vendors_id where a.orders_id = '" . (int)$order_id . "' and a.vendors_id='" . (int) $vendor_order['vendors_id'] . "' group by orders_products_id");
		
		// modified on 19-01-2016 #start
        // removed line [and a.vendors_id='" . (int) $vendor_order['vendors_id'] . "'] 
        $orders_products_query = tep_db_query("select a.orders_products_id, a.products_name, a.products_model, a.products_price, a.products_tax, a.products_quantity,a.final_price, a.vendors_id, b.xml_feed_id, c.internal_id, c.orders_products_status, a.is_ok_for_shipping, d.vendors_name from " . TABLE_ORDERS_PRODUCTS . " a left join products_extended b on a.products_id=b.osc_products_id left join feeds_orders_products_retailer_to_obn c on a.orders_products_id=c.orders_products_id inner join vendors d on a.vendors_id=d.vendors_id where a.orders_id = '" . (int)$order_id . "' group by orders_products_id");
        // modified on 19-01-2016 #ends

		//EOF:mvs_internal_mod
        $index = 0;



          while ($orders_products = tep_db_fetch_array($orders_products_query)) {

            $this->products[$index] = array(

                'qty' => $orders_products['products_quantity'],

                'name' => $orders_products['products_name'],

                'model' => $orders_products['products_model'],

                'tax' => $orders_products['products_tax'],

                'price' => $orders_products['products_price'],
                'vendor_name' => $orders_products['vendors_name'],

                'vendor_ship' => $orders_products['shipping_module'],

                'shipping_method' => $orders_products['shipping_method'],

                'shipping_cost' => $orders_products['shipping_cost'],

                'orders_products_status' => $orders_products['orders_products_status'],

                'final_price' => $orders_products['final_price'],

                'is_xml_feed_product'=>(empty($orders_products['xml_feed_id']) ? 0 : 1),

                'orders_products_id'=>$orders_products['orders_products_id'],

                'sent_to_obn'=>$orders_products['internal_id'],

                'is_ok_for_shipping' => $orders_products['is_ok_for_shipping'], 

            );

/*
            $this->products[$index2]['orders_products'][$index] = array (

              'qty' => $orders_products['products_quantity'],

              'name' => $orders_products['products_name'],

              'tax' => $orders_products['products_tax'],

              'model' => $orders_products['products_model'],

              'price' => $orders_products['products_price'],

              'vendor_name' => $orders_products['vendors_name'],

              'vendor_ship' => $orders_products['shipping_module'],

              'shipping_method' => $orders_products['shipping_method'],

              'shipping_cost' => $orders_products['shipping_cost'],

              'final_price' => $orders_products['final_price'],

              'spacer' => '-', 

            );
*/


            $subindex = 0;

            $attributes_query = tep_db_query("select products_options, products_options_values, options_values_price, price_prefix from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . (int) $order_id . "' and orders_products_id = '" . (int) $orders_products['orders_products_id'] . "'");

            if (tep_db_num_rows($attributes_query)) {

              while ($attributes = tep_db_fetch_array($attributes_query)) {

                $this->products[$index]['attributes'][$subindex] = array (

                  'option' => $attributes['products_options'],

                  'value' => $attributes['products_options_values'],

                  'prefix' => $attributes['price_prefix'],

                  'price' => $attributes['options_values_price']

                );



                $subindex++;

              }

            }

            $index++;

          }

          $index2++;

        }

      } else { // old order, use the regular osC data

//MVS End

      $index = 0;

      //$orders_products_query = tep_db_query("select orders_products_id, products_name, products_model, products_price, products_tax, products_quantity, final_price from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$order_id . "'");

      $orders_products_query = tep_db_query("select a.orders_products_id, a.products_name, a.products_model, 

	  											a.products_price, a.products_tax, a.products_quantity, 

												a.final_price, b.xml_feed_id, c.internal_id, c.orders_products_status, a.is_ok_for_shipping from " . 

												TABLE_ORDERS_PRODUCTS . " a 

												left join products_extended b on a.products_id=b.osc_products_id 

												left join feeds_orders_products_retailer_to_obn c on a.orders_products_id=c.orders_products_id 

												where a.orders_id = '" . 

												(int)$order_id . "' group by orders_products_id");

      while ($orders_products = tep_db_fetch_array($orders_products_query)) {

      	

        $this->products[$index] = array('qty' => $orders_products['products_quantity'],

                                        'name' => $orders_products['products_name'],

                                        'model' => $orders_products['products_model'],

                                        'tax' => $orders_products['products_tax'],

                                        'price' => $orders_products['products_price'],

                                        'orders_products_status' => $orders_products['orders_products_status'],

                                        'final_price' => $orders_products['final_price'],

										'is_xml_feed_product'=>(empty($orders_products['xml_feed_id']) ? 0 : 1),

										'orders_products_id'=>$orders_products['orders_products_id'],

										'sent_to_obn'=>$orders_products['internal_id'],

										'is_ok_for_shipping' => $orders_products['is_ok_for_shipping']);



        $subindex = 0;

        $attributes_query = tep_db_query("select products_options, products_options_values, options_values_price, price_prefix from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . (int)$order_id . "' and orders_products_id = '" . (int)$orders_products['orders_products_id'] . "'");

        if (tep_db_num_rows($attributes_query)) {

          while ($attributes = tep_db_fetch_array($attributes_query)) {

            $this->products[$index]['attributes'][$subindex] = array('option' => $attributes['products_options'],

                                                                     'value' => $attributes['products_options_values'],

                                                                     'prefix' => $attributes['price_prefix'],

                                                                     'price' => $attributes['options_values_price']);



            $subindex++;

          }

        }

        $index++;

        }

//MVS

      }

    }

  }

?>

