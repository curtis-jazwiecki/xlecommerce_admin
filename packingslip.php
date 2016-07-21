<?php
/*
  $Id: packingslip.php,v 1.7 2003/06/20 00:40:10 hpdl Exp $
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

  require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  $oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);
  $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");

  include(DIR_WS_CLASSES . 'order.php');
  $order = new order($oID);
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">

<style type="text/css">
body{ background-color: #FFF; }
</style>

</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- body_text //-->
<table border="0" width="100%" cellspacing="0" cellpadding="2" bgcolor="#FFFFFF">
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td class="pageHeading2"><?php echo nl2br(STORE_NAME_ADDRESS); ?></td>
        <td class="pageHeading2" align="right">
        <?php echo tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . 'store_logo.png', 'cloudcommerce', '204', '50'); ?>
		<?php //echo tep_image(DIR_WS_IMAGES . 'oscommerce.gif', 'osCommerce', '204', '50'); ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td colspan="2"><?php echo tep_draw_separator(); ?></td>
      </tr>
      <tr>
        <td class="main">Order # <?php echo $_GET['oID']; ?></td>
      </tr>
      <tr>
        <td colspan="2"></td>
      </tr>
      <tr>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo ENTRY_SOLD_TO; ?></b></td>
          </tr>
          <tr>
            <td class="main"><?php echo tep_address_format($order->customer['format_id'], $order->customer, 1, '', '<br>'); ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo $order->customer['telephone']; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo '<a href="mailto:' . $order->customer['email_address'] . '"><u>' . $order->customer['email_address'] . '</u></a>'; ?></td>
          </tr>
        </table></td>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo ENTRY_SHIP_TO; ?></b></td>
          </tr>
          <tr>
            <td class="main"><?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br>'); ?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main"><b><?php echo ENTRY_PAYMENT_METHOD; ?></b></td>
        <td class="main"><?php echo $order->info['payment_method']; ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
<?php

//MVS start
  $index = 0;
  $order_packslip_query = tep_db_query ("select vendors_id, orders_products_id, products_name, products_model, products_price, products_tax, products_quantity, final_price from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int) $oID . "'");
  while ($order_packslip_data = tep_db_fetch_array( $order_packslip_query) ) {
    $packslip_products[$index] = array (
      'qty' => $order_packslip_data['products_quantity'],
      'name' => $order_packslip_data['products_name'],
      'model' => $order_packslip_data['products_model'],
      'tax' => $order_packslip_data['products_tax'],
      'price' => $order_packslip_data['products_price'],
      'final_price' => $order_packslip_data['final_price']
    );

    $subindex = 0;
    $packslip_attributes_query = tep_db_query ("select products_options, products_options_values, options_values_price, price_prefix from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . (int) $oID . "' and orders_products_id = '" . (int) $order_packslip_data['orders_products_id'] . "'");
    if (tep_db_num_rows ($packslip_attributes_query)) {
      while ($packslip_attributes = tep_db_fetch_array ($packslip_attributes_query) ) {
        $packslip_products[$index]['packslip_attributes'][$subindex] = array (
          'option' => $packslip_attributes['products_options'],
          'value' => $packslip_attributes['products_options_values'],
          'prefix' => $packslip_attributes['price_prefix'],
          'price' => $packslip_attributes['options_values_price']
        );

        $subindex++;
      }
    }
    $index++;
  }
?>
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
      </tr>
<?php
    for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
      echo '      <tr class="dataTableRow">' . "\n" .
           '        <td class="dataTableContent" valign="top" align="right">' . $order->products[$i]['qty'] . '&nbsp;x</td>' . "\n" .
           '        <td class="dataTableContent" valign="top">' . $order->products[$i]['name'];

      if (isset($order->products[$i]['attributes']) && (sizeof($order->products[$i]['attributes']) > 0)) {
        for ($j=0, $k=sizeof($order->products[$i]['attributes']); $j<$k; $j++) {
          echo '<br><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
          echo '</i></small></nobr>';
        }
      }

      echo '        </td>' . "\n" .
           '        <td class="dataTableContent" valign="top">' . $order->products[$i]['model'] . '</td>' . "\n" .
           '      </tr>' . "\n";
    }
?>
    </table>
    <br /><br /><br />
    <form><input type="button" value=" Print this page "onclick="window.print();return false;" /></form> 
    </td>
  </tr>
</table>
<!-- body_text_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
