<?php
//////////////////////////////////////////
//
//  Sales Report Export
//
//  Jeff Brutsche
//  Outdoor Business Network
//
//////////////////////////////////////////

  require('includes/application_top.php');

  $my_account_query = tep_db_query ("select a.admin_id, a.admin_firstname, a.admin_lastname, a.admin_email_address, a.admin_created, a.admin_modified, a.admin_logdate, a.admin_lognum, g.admin_groups_name from " . TABLE_ADMIN . " a, " . TABLE_ADMIN_GROUPS . " g where a.admin_id= " . $login_id . " and g.admin_groups_id= " . $login_groups_id . "");
  $myAccount = tep_db_fetch_array($my_account_query);
  define('STORE_ADMIN_NAME',$myAccount['admin_firstname'] . ' ' . $myAccount['admin_lastname']);
  define('TEXT_WELCOME','Welcome <strong>' . STORE_ADMIN_NAME . '</strong> to <strong>' . STORE_NAME . '</strong> Administration!');

// Store Status code 
if (DOWN_FOR_MAINTENANCE == 'false'){
  $store_status = '<font color="#009900">Active</font>';
  } else {
  $store_status = '<font color="#FF0000">Maintanace</font>';
  }
// Store Status Code EOF


//Customer Count Code
$customer_query = tep_db_query("select count(customers_id) as customercnt from " . TABLE_CUSTOMERS);
$customercount = tep_db_fetch_array($customer_query);
define('CUSTOMER_COUNT',$customercount['customercnt']);

//Customer Subscribed Count Code
$customer_query = tep_db_query("select count(customers_id) as customercnt from " . TABLE_CUSTOMERS." where customers_newsletter=1");
$customercount = tep_db_fetch_array($customer_query);
define('CUSTOMER_SUBSCRIBED_COUNT',$customercount['customercnt']);

setlocale(LC_MONETARY, 'en_US');



//get path of directory containing this script
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<link type="text/css" rel="StyleSheet" href="includes/index.css" />
<link type="text/css" rel="StyleSheet" href="includes/helptip.css" />
<script type="text/javascript" src="includes/javascript/helptip.js"></script>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3>Sales Report Export
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading">Sales Report Export
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
    <td>
      <table class="table table-bordered table-hover" summary="Table holding Store Information">
        <tr>
          <td>
			<div style="width: 100%; margin: 0 0 0 10px">
<?php	// Prime Variables
			$l = 0;
			$employee_sales = array();
			$total_array = array();
			$list_array = array();

			if(isset($_POST['submit_date']) && $_POST['submit_date'] != "")
              {	$submit_date = $_POST['submit_date'];
				$submit_date = explode("/", $submit_date);
				$todays_date = "$submit_date[2]-$submit_date[0]-$submit_date[1]";
				$list_array[0] = "Orders from: $todays_date";
				echo "Orders from: <b>$todays_date</b>";
				$file_output = $todays_date; }
			elseif(isset($_POST['submit_month']) && $_POST['submit_month'] != "" &&  $_POST['submit_month'] != NULL)
              {	$submit_date = $_POST['submit_month'];
				$submit_date = explode("/", $submit_date);
				if(strlen($submit_date[0]) == 1)
				  $submit_date[0] = "0".$submit_date[0];
				$todays_date = "$submit_date[1]-$submit_date[0]-1";
				$end_of_month_date = "$submit_date[1]-$submit_date[0]-31";
				$todays_date_explode = explode("-",$todays_date);
				$list_array[0] = "Total orders to date for the moth: ".$todays_date_explode[1]."/".$todays_date_explode[0]."";
				echo "Total orders to date for the moth: <b>".$todays_date_explode[1]."/".$todays_date_explode[0]."</b>";
				$file_output = $todays_date_explode[1]."-".$todays_date_explode[0]; }
			elseif(isset($_POST['submit_product_number']) && $_POST['submit_product_number'] != "")
              { $submit_product_number = $_POST['submit_product_number'];
			  	$list_array[0] = "Product Search for: ".$submit_product_number;
				echo "Product Search for: <b>".$submit_product_number."</b>"; }
			else
              { $todays_date = date("Y-m-d");
				$list_array[0] = "Orders from today: ".$todays_date;
				echo "Orders from today: <b>".$todays_date."</b>";
				$file_output = $todays_date; }
?>
            </div>
			<div style="width: 100%; clear:both;">
			  <div style="width: 30%; float: left; margin: 0 0 0 10px">
				Enter in a date to check
    	        <br />
				<form action='sales_report_export.php' method='post'>
					<input name='submit_date' value=''/> <input type='submit' value='submit' name='submit' /><br>
					<span> ex.(mm/dd/yyyy)</span>
				</form><br />
			  </div>
			  <div style="width: 30%; float: left;">
				Enter in a month to check
    	        <br />
				<form action='sales_report_export.php' method='post'>
					<input name='submit_month' value=''/> <input type='submit' value='submit' name='submit' /><br>
					<span> ex.(mm/yyyy)</span>
				</form><br />
			  </div>
			  <div style="width: 30%; float: left;">
				Enter Product number to search for
    	        <br />
				<form action='sales_report_export.php' method='post'>
					<input name='submit_product_number' value=''/> <input type='submit' value='submit' name='submit' />
				</form>
			  </div>
			</div>

			<table class="table table-bordered table-hover">
			  <tr>
			    <th>Order ID</th>
				<th>Customer Name</th>
				<th>Street Address</th>
				<th>Date Purchased</th>
				<th>Payment Method</th>
				<th>Sold By</th>
				<th>Ordered From</th>
				<th>Subtotal</th>
				<th>Shipping</th>
				<th>Custom 2</th>
				<th>Custom 3</th>
				<th>Tax</th>
				<th>Total</th>
			  </tr>
<?php
			if(isset($_POST['submit_date']) && $_POST['submit_date'] != NULL)
              {
				$result_todays_sales = tep_db_query("SELECT o.date_purchased, o.payment_method, o.orders_id, o.customers_name, o.customers_street_address, o.date_purchased, o.customer_service_id FROM orders o where date_purchased>='".$todays_date." 00:00:00' and date_purchased<='".$todays_date." 23:59:59' ORDER BY orders_id");
			  }
			elseif(isset($_POST['submit_month']) && $_POST['submit_month'] != NULL)
              {
				$result_todays_sales = tep_db_query("SELECT o.date_purchased, o.payment_method, o.orders_id, o.customers_name, o.customers_street_address, o.date_purchased, o.customer_service_id FROM orders o where date_purchased>='".$todays_date." 00:00:00' and date_purchased<='".$end_of_month_date." 23:59:59' ORDER BY orders_id");
			  }
			elseif(isset($_POST['submit_product_number']) && $_POST['submit_product_number'] != NULL)
              {
				$result_todays_sales = tep_db_query("SELECT o.date_purchased, o.payment_method, o.orders_id, o.customers_name, o.customers_street_address, o.date_purchased, o.customer_service_id, op.orders_id, op.products_model FROM orders o inner join orders_products op where o.orders_id = op.orders_id  and op.products_model = '".$submit_product_number."' ORDER BY o.orders_id");
			  }
			else
			  {
				$result_todays_sales = tep_db_query("SELECT o.date_purchased, o.payment_method, o.orders_id, o.customers_name, o.customers_street_address, o.date_purchased, o.customer_service_id FROM orders o where date_purchased>='".$todays_date." 00:00:00' and date_purchased<='".$todays_date." 23:59:59' ORDER BY orders_id");
			  }
			$total_tax = 0;
			//   and date_purchased<='".$todays_date." 23:59:59'
			while($row = tep_db_fetch_array($result_todays_sales))
			  {
				$subtotal = '0.0000';
				$shipping = '0.0000';
				$custom = '0.0000';
				$custom2 = '0.0000';
				$custom3 = '0.0000';
				$tax = '0.0000';
				$total = '0.0000';
				$results_sales_total = tep_db_query("SELECT value, class, orders_id FROM orders_total where ".$row['orders_id']."=orders_id");
				while($row2 = tep_db_fetch_array($results_sales_total))
				  {
					if($row2['class'] == "ot_subtotal")
						$subtotal = $row2['value'];
					elseif($row2['class'] == "ot_shipping")
						$shipping = $row2['value'];
					elseif($row2['class'] == "ot_total")
						$total = $row2['value'];
					elseif($row2['class'] == "ot_custom")
						$custom2 = $row2['value'];
					elseif($row2['class'] == "ot_custom_3")
						$custom3 = $row2['value'];
					elseif($row2['class'] == "ot_tax")
						$tax = $row2['value'];
                                        
                                        $total_tax += $tax;
				  }
				if($row['is_phone_order'] == 1) $is_phone_order_result = "Phone";
				elseif($row['customer_service_id'] != "") $is_phone_order_result = "In-Store";
				else $is_phone_order_result = "Online";
				echo 	"<tr>".
						"<td>".$row['orders_id']."</td>".
				  		"<td>".$row['customers_name']."</td>".
						"<td>".$row['customers_street_address']."</td>".
						"<td>".$row['date_purchased']."</td>".
						"<td>".strip_tags($row['payment_method'])."</td>".
						"<td>".$row['customer_service_id']."</td>".
						"<td>".$is_phone_order_result."</td>".
						"<td align='right'>".$subtotal."</td>".
						"<td align='right'>".$shipping."</td>".
						"<td align='right'>".$custom2."</td>".
						"<td align='right'>".$custom3."</td>".
						"<td align='right'>".$tax."</td>".
						"<td align='right'>".$total."</td>".
						"</tr>";
						
				// Total up daily sales
				if(preg_match("/PayPal/i", strip_tags($row['payment_method'])) && $row['customer_service_id'] == "") $paypal_web_total += $total;
				elseif(strip_tags($row['payment_method']) == "Cash on Delivery" && $row['customer_service_id'] == "") $cash_on_delivery_web_total += $total;
				elseif(strip_tags($row['payment_method']) == "Cash" && $row['customer_service_id'] == "") $cash_on_delivery_web_total += $total;
				elseif(preg_match("/Credit Card : Authorize.net/i", strip_tags($row['payment_method'])) && $row['customer_service_id'] == "") $cc_authorize_net_web_total += $total;
				elseif(preg_match("/Payment on Local Pickup/i", strip_tags($row['payment_method'])) && $row['customer_service_id'] == "") $payment_on_local_pickup_web_total += $total;
				elseif(preg_match("/Accounts Receivable/i", strip_tags($row['payment_method'])) && $row['customer_service_id'] == "") $payment_on_local_pickup_web_total += $total;
				elseif(preg_match("/Check/i", strip_tags($row['payment_method'])) && $row['customer_service_id'] == "") $check_web_total += $total;

				// Total phone sales by employees
				elseif(preg_match("/PayPal/i", strip_tags($row['payment_method'])) && $row['is_phone_order'] == 1) $paypal_phone_total += $total;
				elseif(strip_tags($row['payment_method']) == "Cash on Delivery" && $row['is_phone_order'] == 1) $cash_on_delivery_phone_total += $total;
				elseif(strip_tags($row['payment_method']) == "Cash" && $row['is_phone_order'] == 1) $cash_on_delivery_phone_total += $total;
				elseif(preg_match("/Credit Card : Authorize.net/i", strip_tags($row['payment_method'])) && $row['is_phone_order'] == 1) $cc_authorize_net_phone_total += $total;
				elseif(preg_match("/Payment on Local Pickup/i", strip_tags($row['payment_method'])) && $row['is_phone_order'] == 1) $payment_on_local_pickup_phone_total += $total;
				elseif(preg_match("/Accounts Receivable/i", strip_tags($row['payment_method'])) && $row['is_phone_order'] == 1) $payment_on_local_pickup_phone_total += $total;
				elseif(preg_match("/Check/i", $row['payment_method']) && $row['is_phone_order'] == 1) $check_phone_total += $total;

				// Total in-store sales by employees
				elseif(preg_match("/PayPal/i", strip_tags($row['payment_method']))) $paypal_total += $total;
				elseif(strip_tags($row['payment_method']) == "Cash on Delivery") $cash_on_delivery_total += $total;
				elseif(strip_tags($row['payment_method']) == "Cash") $cash_on_delivery_total += $total;
				elseif(preg_match("/Credit Card : Authorize.net/i", strip_tags($row['payment_method']))) $cc_authorize_net_total += $total;
				elseif(preg_match("/Payment on Local Pickup/i", strip_tags($row['payment_method']))) $payment_on_local_pickup_total += $total;
				elseif(preg_match("/Accounts Receivable/i", strip_tags($row['payment_method']))) $payment_on_local_pickup_total += $total;
				elseif(preg_match("/Check/i", $row['payment_method'])) $check_total += $total;

				// Other total
				else $other_total += $total;

				// Total totals
				if(preg_match("/PayPal/i", strip_tags($row['payment_method']))) $paypal_total_total += $total;
				elseif(strip_tags($row['payment_method']) == "Cash on Delivery") $cash_total_total += $total;
				elseif(strip_tags($row['payment_method']) == "Cash") $cash_total_total += $total;
				elseif(preg_match("/Credit Card : Authorize.net/i", strip_tags($row['payment_method']))) $cc_total_total += $total;
				elseif(preg_match("/Payment on Local Pickup/i", strip_tags($row['payment_method']))) $payment_total_total += $total;
				elseif(preg_match("/Accounts Receivable/i", strip_tags($row['payment_method']))) $payment_total_total += $total;
				elseif(preg_match("/Check/i", $row['payment_method'])) $check_total_total += $total;

				else $other_total_total += $total;

				if($row['customer_service_id'] != "")
				  {
					if(!in_array(strtolower($row['customer_service_id']), $employee_sales))
					  {
						$employee_sales[$l] = strtolower($row['customer_service_id']);
						$total_array[$l] += $total;
						$l++;
					  }
					else
					  {
						for($q=0; (sizeof($employee_sales)-1)>=$q; $q++)
						  {
							if($employee_sales[$q] == strtolower($row['customer_service_id']))
							  $total_array[$q] += $total;
						  }
					  }
				  }
				$subtotal_total = $subtotal;
				$shipping_total = $shipping;
				$custom_total = $custom;
				$custom2_total = $custom2;
				$custom3_total = $custom3;
				$tax_total = $tax;
				$subtotal_total = $subtotal;
				$total_total += $total;
			  }
			echo "</table>";
?>

			<div style="width: 100%; margin-top: 10px;">
              <div style="width: 48%; float: left">
				<table class="table table-bordered table-hover">
                  <tr>
                	<th align="left" width="33%"><b>Sales By</b></th>
                	<th align="left" width="33%"></th>
                	<th align="left" width="*"></th>
                  </tr>
<?php	for($q=0; (sizeof($employee_sales)-1)>=$q; $q++)
		  {
			echo '<tr>'.
                 '	<td align="right">'.$employee_sales[$q].'</td>'.
                 '	<td align="left"></td>'.
                 '	<td align="right">'.money_format("%(#10n", ($total_array[$q])).'</td>'.
                 '</tr>';
		  }
?>
				</table>
              </div>
              <div style="width: 48%; float: right">
				<table class="table table-bordered table-hover">
                  <tr>
                	<th align="left"><b>Store-Walk In</b></th>
                	<th align="right"></th>
                	<th align="right"><?php echo ($cc_authorize_net_total + $paypal_total + $cash_on_delivery_total + $payment_on_local_pickup_total + $check_total); ?></th>
                  </tr>
                  <tr>
                	<td align="right">Credit</td>
                	<td align="right"><?php echo money_format("%(#10n", ($cc_authorize_net_total)); ?></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">Paypal</td>
                	<td align="right"><?php echo money_format("%(#10n", ($paypal_total)); ?></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">Cash</td>
                	<td align="right"><?php echo money_format("%(#10n", ($cash_on_delivery_total)); ?></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">A/R</td>
                	<td align="right"><?php echo money_format("%(#10n", ($payment_on_local_pickup_total)); ?></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">Gift Card/Points</td>
                	<td align="right"></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">Check</td>
                	<td align="right"><?php echo money_format("%(#10n", ($check_total)); ?></td>
                	<td align="right"></td>
                  </tr>
				</table>
                <br />
				<table class="table table-bordered table-hover">
                  <tr>
                	<th align="left"><b>Web Orders</b></th>
                	<th align="right"></th>
                	<th align="right"><?php echo ($cc_authorize_net_web_total + $paypal_web_total + $cash_on_delivery_web_total + $payment_on_local_pickup_web_total + $check_web_total); ?></th>
                  </tr>
                  <tr>
                	<td align="right">Credit</td>
                	<td align="right"><?php echo money_format("%(#10n", ($cc_authorize_net_web_total)); ?></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">Paypal</td>
                	<td align="right"><?php echo money_format("%(#10n", ($paypal_web_total)); ?></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">Cash</td>
                	<td align="right"><?php echo money_format("%(#10n", ($cash_on_delivery_web_total)); ?></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">A/R</td>
                	<td align="right"><?php echo money_format("%(#10n", ($payment_on_local_pickup_web_total)); ?></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">Gift Card/Points</td>
                	<td align="right"></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">Check</td>
                	<td align="right"><?php echo money_format("%(#10n", ($check_web_total)); ?></td>
                	<td align="right"></td>
                  </tr>
				</table>
                <br />
				<table class="table table-bordered table-hover">
                  <tr>
                	<th align="left"><b>Phone</b></th>
                	<th align="left"></th>
                	<th align="right"><?php echo ($cc_authorize_net_phone_total + $paypal_phone_total + $cash_on_delivery_phone_total + $payment_on_local_pickup_phone_total + $check_phone_total); ?></th>
                  </tr>
                  <tr>
                	<td align="right">Credit</td>
                	<td align="right"><?php echo money_format("%(#10n", ($cc_authorize_net_phone_total)); ?></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">Paypal</td>
                	<td align="right"><?php echo money_format("%(#10n", ($paypal_phone_total)); ?></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">Cash</td>
                	<td align="right"><?php echo money_format("%(#10n", ($cash_on_delivery_phone_total)); ?></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">A/R</td>
                	<td align="right"><?php echo money_format("%(#10n", ($payment_on_local_pickup_phone_total)); ?></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">Gift Card/Points</td>
                	<td align="right"></td>
                	<td align="right"></td>
                  </tr>
                  <tr>
                	<td align="right">Check</td>
                	<td align="right"><?php echo money_format("%(#10n", ($check_phone_total)); ?></td>
                	<td align="right"></td>
                  </tr>
				</table>
                <br />
				<table class="table table-bordered table-hover">
                  <tr>
                	<th align="left"><b>Other</b></th>
                	<th align="left"></th>
                	<th align="right"><?php echo $other_total_total; ?></th>
                  </tr>
                  <tr>
                	<td align="right">Credit</td>
                	<td align="left"></td>
                	<td align="left"></td>
                  </tr>
                  <tr>
                	<td align="right">Paypal</td>
                	<td align="right"></td>
                	<td align="left"></td>
                  </tr>
                  <tr>
                	<td align="right">Cash</td>
                	<td align="right"></td>
                	<td align="left"></td>
                  </tr>
                  <tr>
                	<td align="right">A/R</td>
                	<td align="right"></td>
                	<td align="left"></td>
                  </tr>
                  <tr>
                	<td align="right">Gift Card/Points</td>
                	<td align="right"></td>
                	<td align="left"></td>
                  </tr>
                  <tr>
                	<td align="right">Check</td>
                	<td align="left"></td>
                	<td align="left"></td>
                  </tr>
                  <tr>
                	<td align="right">Other</td>
                	<td align="right"><?php echo money_format("%(#10n", ($other_total)); ?></td>
                	<td align="left"></td>
                  </tr>
				</table>
                <br />
				<table class="table table-bordered table-hover">
                  <tr>
                	<th align="right"><b>Total Credit Card</b></th>
                	<th align="right"><?php echo money_format("%(#10n", ($cc_total_total)); ?></th>
                	<th align="right"></th>
                  </tr>
                  <tr>
                	<th align="right"><b>Total Deposit</b></th>
                	<th align="right"><?php echo money_format("%(#10n", ($cash_total_total)); ?></th>
                	<th align="right"></th>
                  </tr>
                  <tr>
                	<th align="right"><b>Total Paypal</b></th>
                	<th align="right"><?php echo money_format("%(#10n", ($paypal_total_total)); ?></th>
                	<th align="right"></th>
                  </tr>
                  <tr>
                	<th align="right"><b>Total A/R</b></th>
                	<th align="right"><?php echo money_format("%(#10n", ($payment_total_total)); ?></th>
                	<th align="right"></th>
                  </tr>
                  <tr>
                	<th align="right"><b>Total Check</b></th>
                	<th align="right"><?php echo money_format("%(#10n", ($check_total_total)); ?></th>
                	<th align="right"></th>
                  </tr>
                  <tr>
                	<th align="right"><b>Total</b></th>
                	<th align="right"></th>
                	<th align="right"><?php echo $total_total; ?></th>
                  </tr>
                  <tr>
                	<th align="right"><b>Total Tax</b></th>
                	<th align="right"></th>
                	<th align="right"><?php echo money_format("%(#10n", ($total_tax)); ?></th>
                  </tr>
				</table>
                <br />
				<table class="table table-bordered table-hover">
                  <tr>
                	<th align="left" width="33%"><b>Over/Short</b></th>
                	<th align="right" width="33%"></th>
                	<th align="right" width="*"></th>
                  </tr>
				</table>
              </div>
            </div>
        </td>
    </tr>
    </table>
    
    <!--BLOCK CODE ENDS -->
   
     
    </td>
  </tr>
</table>
<!-- END your table-->
<!-- body_eof //-->
<?php
// Generate an XML file for this day/month

$list_array[1] = "Store-Walk In, , ".($cc_authorize_net_total + $paypal_total + $cash_on_delivery_total + $payment_on_local_pickup_total)."";
$list_array[2] = "Credit,$cc_authorize_net_total,";
$list_array[3] = "Paypal,$paypal_total,";
$list_array[4] = "Cash,$cash_on_delivery_total,";
$list_array[5] = "A/R,$payment_on_local_pickup_total,";
$list_array[6] = "Gift Card/Points, ,";
$list_array[7] = "Check, ,";
$list_array[8] = ", ,";
$list_array[9] = "Web Orders, ,".($cc_authorize_net_web_total + $paypal_web_total + $cash_on_delivery_web_total + $payment_on_local_pickup_web_total)."";
$list_array[10] = "Credit,$cc_authorize_net_web_total,";
$list_array[11] = "Paypal,$paypal_web_total,";
$list_array[12] = "Cash,$cash_on_delivery_web_total,";
$list_array[13] = "A/R,$payment_on_local_pickup_web_total,";
$list_array[14] = "Gift Card/Points,,";
$list_array[15] = "Check,,";
$list_array[16] = ",,";
$list_array[17] = "Phone,,".($cc_authorize_net_phone_total + $paypal_phone_total + $cash_on_delivery_phone_total + $payment_on_local_pickup_phone_total)."";
$list_array[18] = "Credit,$cc_authorize_net_phone_total,";
$list_array[19] = "Paypal,$paypal_phone_total,";
$list_array[20] = "Cash,$cash_on_delivery_phone_total,";
$list_array[21] = "A/R,$payment_on_local_pickup_phone_total,";
$list_array[22] = "Gift Card/Points,,";
$list_array[23] = "Check,,";
$list_array[24] = "Other,,$other_total_total";
$list_array[25] = "Credit,,";
$list_array[26] = "Paypal,,";
$list_array[27] = "Cash,,";
$list_array[28] = "A/R,,";
$list_array[29] = "Gift Card/Points,,";
$list_array[30] = "Check,,";
$list_array[31] = "Other,,$other_total";
$list_array[32] = ",,";
$list_array[33] = "Total Credit Card,$cc_total_total,";
$list_array[34] = "Total Deposit,$cash_total_total,";
$list_array[35] = "Total Paypal,$paypal_total_total,";
$list_array[36] = "Total A/R,$payment_total_total,";
$list_array[37] = "Total Sales,,$total_total";
$list_array[38] = ",,";
$list_array[39] = "Over/Short,,";
$list_array[40] = ",,";
$list_array[40] = "Sales By Employees,,";

	$qq = 42;
	for($q=0; (sizeof($employee_sales)-1)>=$q; $q++)
	  {
		$qq++;
		$list_array[$qq] = "$employee_sales[$q]:,,$total_array[$q]";
	  }
	if(!isset($_POST['submit_product_number']))
	  {
		if(isset($_POST['submit_month']) && $_POST['submit_month'] != "" &&  $_POST['submit_month'] != NULL)
		  {
			if (@$fp = fopen(DIR_FS_ADMIN_EXPORT.$file_output."-monthly-report.csv", 'w')){
				foreach ($list_array as $line)
					{ fputcsv($fp, explode(',', $line)); }
				fclose($fp);
			}
		  }
		else
		  {
			if (@$fp = fopen(DIR_FS_ADMIN_EXPORT.$file_output."-daily-report.csv", 'w')){
				foreach ($list_array as $line)
					{ fputcsv($fp, explode(',', $line)); }
				fclose($fp);
			}
		  }
	  }
?>
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>