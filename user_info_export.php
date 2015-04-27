<?php
//////////////////////////////////////////
//
//  Customer Information Exporter
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





//get path of directory containing this script
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<!-- Code related to index.php only -->
<link type="text/css" rel="StyleSheet" href="includes/index.css" />
<link type="text/css" rel="StyleSheet" href="includes/helptip.css" />
<script type="text/javascript" src="includes/javascript/helptip.js"></script>
<!-- code related to index.php EOF -->
</head>
<body>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<table width="760"  border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
    <td valign="top">
      <table width="100%"  border="0" cellspacing="0" cellpadding="0" summary="Table holding Store Information">
        <tr valign="top">
          <td>
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading">Export User Information</td>
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
             </tr>
            </table>
          </td>
        </tr>
      </table>
      <table bgcolor="#DDDDDD" cellspacing="0" cellpadding="10px" border="0" width="760px">
        <tr>
          <td>
            <br />
			<?php echo '<b>Customer Count:</b> '.CUSTOMER_COUNT;?>
            <br />
            <br />
<?php
			//Get # of rows from customers table
			$result_num_rows_customers = tep_db_query("SELECT * FROM customers");

			/* 3 way table joing between customers -> address_book and from address_book -> zones
			
			 SELECT customers.customers_firstname, customers.customers_lastname, customers.customers_email_address, customers.customers_telephone, address_book.entry_street_address, address_book.entry_postcode, address_book.entry_city, zones.zone_name FROM customers, address_book, zones WHERE customers.customers_id = address_book.customers_id AND zones.zone_id = address_book.entry_zone_id;
			*/

			//Add 1 here to offset for the header column
			$num_rows_customers = tep_db_num_rows($result_num_rows_customers) + 1;

			//Set Count Variable to 1 to offset the header column
		  	$i = 1;
			//Create Array
			$data_array_num_rows_customers = array($num_rows_customers);
			//Define header column
			$data_array_num_rows_customers[0] = "First Name,Last Name,Email Address,Telephone Number,Street Address,Postal Code,City,State";

			//populate array
			$result = tep_db_query("SELECT customers.customers_firstname, customers.customers_lastname, customers.customers_email_address, customers.customers_telephone, address_book.entry_street_address, address_book.entry_postcode, address_book.entry_city, zones.zone_name FROM customers, address_book, zones WHERE customers.customers_id = address_book.customers_id AND zones.zone_id = address_book.entry_zone_id");
			while($row = tep_db_fetch_array($result))
			  {
				$data_array_num_rows_customers[$i] = "" . str_replace(",", " - ", $row['customers_firstname']) . "," . str_replace(",", " - ", $row['customers_lastname']) . "," . str_replace(",", " - ", $row['customers_email_address']) . "," . str_replace(",", " - ", $row['customers_telephone']) . ", ". str_replace(",", " - ", $row['entry_street_address']) .", " . str_replace(",", " - ", $row['entry_postcode']) . "," . str_replace(",", " - ", $row['entry_city']) . "," . str_replace(",", " - ", $row['zone_name']);
				$i++;
			  }

			//open .csv file, write each line from array, close .csv file
			//if (@$customer_csv_file = fopen(DIR_FS_ADMIN_EXPORT.'customer_information.csv', 'w')){
			if (@$customer_csv_file = fopen(DIR_FS_ADMIN . 'export_files/customer_information.csv', 'w')){
				foreach ($data_array_num_rows_customers as $line) {
					fputcsv($customer_csv_file, explode(',', $line));
				}
				fclose($customer_csv_file);
			}
		
?>

			<a href="<?php echo DIR_WS_ADMIN; ?>export_files/customer_information.csv" style="text-decoration:underline; color: #333">Click Here to download Customer Infomation (<?php echo number_format(filesize(DIR_FS_ADMIN . 'export_files/customer_information.csv') / 1024, 2); ?>KB)</a><span style="font-size: 9px; color:#000"> - (.csv format)</span>
			<br /><br />
			<span style="font-size: 11px; font-family: Georgia, 'Times New Roman', Times, serif; color:#000">File will be automatically removed from server within 30 minutes. Just reload page to generate a new file.</span>

<?php	/*/
		///////////////////////////////////////////////////////////
		// OPTIONAL, show full list of customer's information
		///////////////////////////////////////////////////////////

			echo "<div align=\"center\">";
          // Display Customer Information //
      		echo "<table width=\"96%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"border:1px solid #CCCCCC; margin: 10px 0 0 0; \">";
            echo "<tr valign='top'><th style='padding-left: 5px;'>First Name</th><th>Last Name</th><th>Email Address</th><th>Telephone</th><th>Street Address</th><th>Postal Code</th><th>City</th><th>State</th></tr>";
			
			$query = "SELECT customers.customers_firstname, customers.customers_lastname, customers.customers_email_address, customers.customers_telephone, address_book.entry_street_address, address_book.entry_postcode, address_book.entry_city, zones.zone_name FROM customers, address_book, zones WHERE customers.customers_id = address_book.customers_id AND zones.zone_id = address_book.entry_zone_id";
			$result = tep_db_query($query);
			
			while($row = tep_db_fetch_array($result))
			  {
			  $temp = $row['entry_street_address'];
			  $entry_street_address = str_replace("$temp", ",", " ");
			  echo "<tr valign='top'>";
			  echo "<td style='padding-left: 5px;'>" . $row['customers_firstname'] . "</td><td>" . $row['customers_lastname'] . "</td><td>" . $row['customers_email_address'] . "</td><td>" . $row['customers_telephone'] . "</td><td>" . $entry_street_address . "</td><td>" . $row['entry_postcode'] . "</td><td>" . $row['entry_city'] . "</td><td>" . $row['zone_name'] . "</td>";
			  echo "</tr>";
			  }
			  
			echo "</table></div>";
/*/ ?>
          </td>
        </tr>
        <tr valign="top">
          <td colspan=2><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
        </tr>
      </table>
      <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0" summary="Footer Banner Table">
        <tr>
          <td align="center"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<!-- former position of disclaimer  -->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>