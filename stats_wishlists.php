<?php

/*

  $Id: stats_wishlists.php,v 1.00 2005/06/15

  

  Aaron Hiatt aaron@scaredrabbit.com

  http://www.scaredrabbit.com

  

  osCommerce, Open Source E-Commerce Solutions

  http://www.oscommerce.com



  Copyright (c) 2003 osCommerce



  Released under the GNU General Public License

*/



  require('includes/application_top.php');



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

  <tr>

<!-- body_text //-->

    <td><table class="table table-bordered table-hover">

      <tr>

        <td><table class="table table-bordered table-hover">

          <tr>

            <td><table class="table table-bordered table-hover">

              <tr>

                <td align="center"><?php echo TABLE_HEADING_NUMBER; ?></td>

                <td><?php echo TABLE_HEADING_CUSTOMERS; ?></td>

				<td><?php echo TABLE_HEADING_CUSTOMERS_COMPANY; ?></td>

				<td><?php echo TABLE_HEADING_CUSTOMERS_WISHLIST; ?></td>

				<!--Uncomment if you use the Separate Pricing Contribution

				<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMERS_GROUP_NAME; ?></td>

				-->

              </tr>

<?php

  if (isset($HTTP_GET_VARS['page']) && ($HTTP_GET_VARS['page'] > 1)) $rows = $HTTP_GET_VARS['page'] * MAX_DISPLAY_SEARCH_RESULTS - MAX_DISPLAY_SEARCH_RESULTS;

  $customers_query_raw = "select a.entry_company, pd.products_name, w.products_id, c.customers_id, c.customers_firstname, c.customers_lastname from " . TABLE_WISHLIST . " w, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_CUSTOMERS . " c left join " . TABLE_ADDRESS_BOOK . " a on c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id where c.customers_id = w.customers_id and w.products_id = pd.products_id group by c.customers_firstname, c.customers_lastname order by c.customers_lastname desc";

  $customers_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $customers_query_raw, $customers_query_numrows);

  

// fix counted customers

  $customers_query_numrows = tep_db_query("select customers_id from " . TABLE_WISHLIST . " group by customers_id");

  $customers_query_numrows = tep_db_num_rows($customers_query_numrows);

  

  $rows = 0;

  $customers_query = tep_db_query($customers_query_raw);

  

  while ($customers = tep_db_fetch_array($customers_query)) {  

	

    $rows++;



    if (strlen($rows) < 2) {

    $rows = '0' . $rows;

    }

	

?>

              <tr onMouseOver="rowOverEffect(this)" onMouseOut="rowOutEffect(this)" onClick="document.location.href='<?php echo tep_href_link(FILENAME_CUSTOMERS, 'selected_box=customers&page=1&cID=' . $customers['customers_id'], 'NONSSL'); ?>'">

                <td align="center"><?php echo $rows; ?>.</td>

                <td><?php echo $customers['customers_firstname'] . ' ' . $customers['customers_lastname']; ?></td>

				<td><?php echo $customers['entry_company']; ?></td>

                <td>

				<?php

				 

                $products_query = tep_db_query("select w.products_id, pd.products_id, pd.products_name, c.customers_id from " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_CUSTOMERS . " c, " . TABLE_WISHLIST . " w where w.customers_id = " . $customers['customers_id'] . " and w.products_id = pd.products_id and c.customers_id = w.customers_id group by pd.products_id order by pd.products_name");

                while ($products = tep_db_fetch_array($products_query)) {

				

				echo $products['products_name'] . '<br>';

				

				}

				?>

				</td>

				<!--Uncomment if you use the Separate Pricing Contribution

				<td class="dataTableContent" valign="top"><?php echo $customers['customers_group_name']; ?></td>

				-->

              </tr>

<?php

  }

?>

            </table></td>

          </tr>

          <tr>

            <td colspan="3"><table class="table table-bordered table-hover">

              <tr>

                <td><?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>

                <td align="right"><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?>&nbsp;</td>

              </tr>

            </table></td>

          </tr>

        </table></td>

      </tr>

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