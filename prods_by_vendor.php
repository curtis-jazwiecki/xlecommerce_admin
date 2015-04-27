<?php

/*

  $ID: prods_by_vendor.php (for use with MVS) by Craig Garrison Sr, BluCollar Sales

  $Loc: /catalog/admin/ $

  $Mod: MVS V1.2 2009/02/28 JCK/CWG $



  osCommerce, Open Source E-Commerce Solutions

  http://www.oscommerce.com



  Copyright (c) 2006 osCommerce



  Released under the GNU General Public License

*/



  require_once ('includes/application_top.php');



  require_once (DIR_WS_CLASSES . 'currencies.php');

  $currencies = new currencies();



  // Test changes 01-19-2008

  $line_filter = '';

  if (isset ($_GET['line']) && $_GET['line'] != '') {

    $line_filter = $_GET['line'];

    $line_filter = preg_replace("(\r\n|\n|\r)", '', $line_filter); // Remove CR &/ LF

    $line_filter = preg_replace("/[^a-z]/i", '', $line_filter); // strip anything we don't want

  }



  $vendors_id = 1;

  if (isset ($_GET['vendors_id']) && $_GET['vendors_id'] != '') {

    $vendors_id = (int) $_GET['vendors_id'];

  }



  $show_order = '';

  if (isset ($_GET['show_order']) && $_GET['show_order'] != '') {

    $show_order = $_GET['show_order'];

    $show_order = preg_replace("(\r\n|\n|\r)", '', $show_order); // Remove CR &/ LF

    $show_order = preg_replace("/[^a-z]/i", '', $show_order); // strip anything we don't want

  }



  switch ($line_filter) {

    case 'prod' :

      $sort_by_filter = 'pd.products_name';

      break;

    case 'vpid' :

      $sort_by_filter = 'p.vendors_prod_id';

      break;

    case 'pid' :

      $sort_by_filter = 'p.products_id';

      break;

    case 'qty' :

      $sort_by_filter = 'p.products_quantity';

      break;

    case 'vprice' :

      $sort_by_filter = 'p.vendors_product_price';

      break;

    case 'price' :

    case '' :

    default :

      $sort_by_filter = 'pd.products_name';

      break;

  }



?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">

<html <?php echo HTML_PARAMS; ?>>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">

<title><?php echo TITLE; ?></title>

<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">

<script language="javascript" src="includes/general.js"></script>

</head>

<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">

<!-- header //-->

<?php require_once(DIR_WS_INCLUDES . 'header.php'); ?>

<!-- header_eof //-->



<!-- body //-->

<table width="780" border="0" align="center" cellpadding="2" cellspacing="2" bgcolor="#030c2c">

  <tr>


<!-- body_text //-->

    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">

      <tr>

        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">

          <tr>

            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>

            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>

          </tr>

        </table></td>

      </tr>

      <tr>

        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">

          <tr>

            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">

              <tr>

<?php



  $vendors_query = tep_db_query ("select vendors_id, 

                                         vendors_name 

                                  from " . TABLE_VENDORS . " 

                                  order by vendors_name

                                ");

  while ($vendors = tep_db_fetch_array ($vendors_query) ) {

    $vendors_array[] = array (

      'id' => $vendors['vendors_id'],

      'text' => $vendors['vendors_name']

    );

  }

?>

                <td class="main" align="left" style="color:#ffffff;"><?php echo TABLE_HEADING_VENDOR_CHOOSE . ' '; ?><?php echo tep_draw_form ('vendors_report', FILENAME_PRODS_VENDORS, '', 'get') . tep_draw_pull_down_menu ('vendors_id', $vendors_array,'','onChange="this.form.submit()";');?></form></td>

                <td class="main" align="left" style="color:#ffffff;"><?php echo '<a href="' . tep_href_link(FILENAME_VENDORS) . '"><b>Go To Vendors List</a>';?><td>

              </tr>

              <?php /*<tr>

                <td class="main" align="left">

<?php



  if ($show_order == 'desc') {

    // Test code -- 3 lines

    echo 'Click for <a href="' . tep_href_link (FILENAME_PRODS_VENDORS, '&vendors_id=' . $vendors_id . '&line=' . $line_filter . '&show_order=asc') . '"><b>ascending order</b></a>';

  } else {

    echo 'Click for <a href="' . tep_href_link (FILENAME_PRODS_VENDORS, '&vendors_id=' . $vendors_id . '&line=' . $line_filter . '&show_order=desc') . '"><b>descending order</b></a>';

  }

?>

              </td> */ ?>

            </tr>

        </table></td>

      </tr>

<?php



  if (isset ($vendors_id)) {



    // $vendors_id = $HTTP_POST_VARS['vendors_id'];

    $vend_query_raw = "select vendors_name as name from " . TABLE_VENDORS . " where vendors_id = '" . $vendors_id . "'";

    $vend_query = tep_db_query ($vend_query_raw);

    $vendors = tep_db_fetch_array ($vend_query);

?>

      <tr>

        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">

          <tr>

            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="1">

              <tr class="dataTableHeadingRow">

                <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_VENDOR; ?></td>

                <td class="dataTableHeadingContent" align="left">
                <?php //echo '<a href="' . tep_href_link(FILENAME_PRODS_VENDORS, 'vendors_id=' . $vendors_id . '&line=prod') . '" style="color:#ffffff;">' . TABLE_HEADING_PRODUCTS_NAME . '</a>'; 
                echo TABLE_HEADING_PRODUCTS_NAME;
                ?>&nbsp;</td>

                <?php /*<td class="dataTableHeadingContent" align="left"><?php echo '<a href="' . tep_href_link(FILENAME_PRODS_VENDORS, 'vendors_id=' . $vendors_id . '&line=vpid') . '">' . TABLE_HEADING_VENDORS_PRODUCT_ID . '</a>'; ?></td> */ ?>

                <td class="dataTableHeadingContent" align="left"><?php echo '<a href="' . tep_href_link(FILENAME_PRODS_VENDORS, 'vendors_id=' . $vendors_id . '&line=pid') . '" style="color:#ffffff;">' .  TABLE_HEADING_PRODUCTS_ID . '</a>'; ?></td>

                <?php /*<td class="dataTableHeadingContent" align="left"><?php echo '<a href="' . tep_href_link(FILENAME_PRODS_VENDORS, 'vendors_id=' . $vendors_id . '&line=qty') . '">' .  TABLE_HEADING_QUANTITY . '</a>'; ?></td>

                <td class="dataTableHeadingContent" align="left"><?php echo '<a href="' . tep_href_link(FILENAME_PRODS_VENDORS, 'vendors_id=' . $vendors_id . '&line=vprice') . '">' .  TABLE_HEADING_VENDOR_PRICE . '</a>'; ?></td>

                <td class="dataTableHeadingContent" align="left"><?php echo '<a href="' . tep_href_link(FILENAME_PRODS_VENDORS, 'vendors_id=' . $vendors_id . '&line=price') . '" style="color:#ffffff;">' .  TABLE_HEADING_PRICE . '</a>'; ?></td> */ ?>

              </tr>

              <tr class="dataTableRow">

                <td class="dataTableContent"><?php echo '<a href="' . tep_href_link(FILENAME_VENDORS, '&vendors_id=' . $vendors_id . '&action=edit') . '" TARGET="_blank" style="color:#ffffff;"><b>' . $vendors['name'] . '</a></b>'; ?></td>

                <td class="dataTableContent"><?php echo ''; ?></td>

                <td class="dataTableContent"><?php echo ''; ?></td>

                <?php /*<td class="dataTableContent"><?php echo ''; ?></td>

                <td class="dataTableContent"><?php echo ''; ?></td>

                <td class="dataTableContent"><?php echo ''; ?></td>

                <td class="dataTableContent"><?php echo ''; ?></td> */ ?>



<?php



    // if (isset($HTTP_GET_VARS['page']) && ($HTTP_GET_VARS['page'] > 1)) $rows = $HTTP_GET_VARS['page'] * MAX_DISPLAY_SEARCH_RESULTS - MAX_DISPLAY_SEARCH_RESULTS;

    $rows = 0;

    if ($show_order == 'desc') {

      $products_query_raw = "select p.products_id, p.vendors_id, pd.products_name, p.products_quantity , p.products_price, p.vendors_product_price, p.vendors_prod_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and p.vendors_id = " . $vendors_id . " and pd.language_id = " . $languages_id . " and pd.products_name<>'' order by " . $sort_by_filter . " desc";

    } elseif ($show_order == 'asc') {

      $products_query_raw = "select p.products_id, p.vendors_id, pd.products_name, p.products_quantity , p.products_price, p.vendors_product_price, p.vendors_prod_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and p.vendors_id = " . $vendors_id . " and pd.language_id = " . $languages_id . " and pd.products_name<>'' order by " . $sort_by_filter . " asc";

    } else {

      $products_query_raw = "select p.products_id, p.vendors_id, pd.products_name, p.products_quantity , p.products_price, p.vendors_product_price, p.vendors_prod_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and p.vendors_id = " . $vendors_id . " and pd.language_id = " . $languages_id . " and pd.products_name<>'' order by " . $sort_by_filter . "";

    }

    $products_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_query_raw, $products_query_numrows);

      //Decide not to use SPLIT pages for the $vendors_id variable not being maintained.

      



    $products_query = tep_db_query ($products_query_raw);

    while ($products = tep_db_fetch_array ($products_query)) {

      $rows++;



      if (strlen ($rows) < 2) {

        $rows = '0' . $rows;

      }

?>

              <tr class="dataTableRow">

<?php



      if ($products['vendors_prod_id'] == '') {

        $products['vendors_prod_id'] = 'None Specified';

      }

?>

                <td class="dataTableContent"><?php echo ''; ?></td>

                <td class="dataTableContent"><?php echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=new_product&pID=' . $products['products_id']) . '" TARGET="_blank" style="color:#000000;"><b>' . $products['products_name'] . '</a></b>'; ?></td>

                <?php /*<td class="dataTableContent"><?php echo $products['vendors_prod_id']; ?></td> */ ?>

                <td class="dataTableContent" class="smallText"><?php echo $products['products_id']; ?></td>

                <?php /*<td class="dataTableContent" align="left"><?php echo $products['products_quantity']; ?>&nbsp;</td>

                <td class="dataTableContent"><?php echo $products['vendors_product_price']; ?></td>

                <td class="dataTableContent" class="smallText"><?php echo $products['products_price']; ?></td> */ ?>

              </tr>

<?php



    }

  }

?>

            </table></td>

          </tr>

          <tr>

            <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">

              <tr>

                <td class="smallText2" valign="top">

<?php



   echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS);

?>

                </td>

                <td class="smallText2" align="right">

<?php



   echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'].$vendors_id);

?>

                </td>

              </tr>

            </table></td>

          </tr>

        </table></td>

      </tr>

    </table></td>

<!-- body_text_eof //-->

  </tr>

</table>

<!-- body_eof //-->



<!-- footer //-->

<?php require_once(DIR_WS_INCLUDES . 'footer.php'); ?>

<!-- footer_eof //-->

</body>

</html>

<?php require_once(DIR_WS_INCLUDES . 'application_bottom.php'); ?>