<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
//require("includes/configure.php");
//require("includes/functions/database.php");

//Connect to the DB - OBN
tep_db_connect() or die('Unable to connect to database server!');

//Get Token Folder - OBN
$token_raw = tep_db_query("Select configuration_value FROM configuration WHERE configuration_key = 'OBN_RETAILER_TOKEN'");
$token = tep_db_fetch_array($token_raw);

//Prime Variables - OBN
$counts=0;
$output=array();
$stringData='';

//$yesterday_orders_raw = tep_db_query("select o.orders_id, o.date_purchased FROM orders o WHERE o.date_purchased < '" . date('Y-m-d',mktime(0,0,0,date('m'),date('d')-1,date('y'))) . " 23:59:59' AND o.date_purchased > '" . date('Y-m-d',mktime(0,0,0,date('m'),date('d')-1,date('y'))) . " 00:00:00'");
$yesterday_orders_raw = tep_db_query("select o.orders_id, o.date_purchased FROM orders o");

while ($row = tep_db_fetch_array($yesterday_orders_raw))
  {
	$prod_counts=0;
    $order_id='';

	$order_totl_raw = tep_db_query("SELECT orders_total_id, orders_id, title, text, value, class, sort_order FROM orders_total WHERE orders_id = '" . $row['orders_id'] . "'");
	while ($rows = tep_db_fetch_array($order_totl_raw))
	  {
		if($rows['class'] == 'ot_total')
		  {
			$total = $rows['value'];
		  }
	  }

	echo $counts . " - Order Id - " . $row['orders_id'] . " | Date/Time - " . $row['date_purchased'] . " | Order Total - " . $total . "<br />\n";

    $order_id = $row['orders_id'];

	$output[$counts] = "::" . $row['orders_id'] . "::" . $total;

	$order_prod_raw = tep_db_query("SELECT op.orders_products_id, op.orders_id, op.products_id, op.products_model, op.products_name, op.products_price, op.final_price, m.manufacturers_name FROM orders_products op JOIN products p ON p.products_id = op.orders_products_id JOIN manufacturers m ON p.manufacturers_id = m.manufacturers_id WHERE orders_id = '" . $row['orders_id'] . "'");
	while ($rowss = tep_db_fetch_array($order_prod_raw))
	  {
	    $prod_counts++;
        echo "&nbsp;&nbsp;&nbsp;" . $prod_counts . " - " . $rowss['products_model'] . " - " . $rowss['manufacturers_name'] . " - " . $rowss['products_name'] . " - " . $rowss['final_price'] . " - " . "<br />";
		$output[$counts] .= $rowss['products_model'] . "::" . $rowss['manufacturers_name'] . "::" . $rowss['products_name'] . "::" . $rowss['final_price'];
	  }
	$output[$counts] .= "::";
	$counts++;
  }
//Disconnect to the DB - OBN
tep_db_close();

//BOF write the output file - OBN
//$myFile = '../../' . $token['configuration_value'] . '/' . date('Y-m-d') . '_orders.txt';
$myFile = '../../' . $token['configuration_value'] . '/' . $token['configuration_value'] . '_total_orders.txt';
$fh = fopen($myFile, 'w') or die("can't open file");

echo '<br /><br />';

for($x=0;sizeof($output) > $x; $x++)
  {
	if($x>0)
      $stringData .= "\n";
    $stringData .= $output[$x];
	echo $output[$x];
    echo '<br />';
  }

fwrite($fh, $stringData);
fclose($fh);
//EOF write the output file - OBN


echo '<br />';
echo '<br />total rows effected - ' . $counts . "\n\n";
echo "<br />\End Script \n\n";
?>