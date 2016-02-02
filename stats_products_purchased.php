<?php
/*
  $Id: stats_products_purchased.php,v 1.29 2003/06/29 22:50:52 hpdl Exp $

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
                <td><?php echo TABLE_HEADING_NUMBER; ?></td>
                <td><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td align="center"><?php echo TABLE_HEADING_PURCHASED; ?>&nbsp;</td>
              </tr>
<?php
  if (isset($HTTP_GET_VARS['page']) && ($HTTP_GET_VARS['page'] > 1)) $rows = $HTTP_GET_VARS['page'] * MAX_DISPLAY_SEARCH_RESULTS - MAX_DISPLAY_SEARCH_RESULTS;
  $products_query_raw = "select p.products_id, p.products_ordered, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pd.language_id = '" . $languages_id. "' and p.products_ordered > 0 group by pd.products_id order by p.products_ordered DESC, pd.products_name";
  $products_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_query_raw, $products_query_numrows);

  $rows = 0;
  $products_query = tep_db_query($products_query_raw);
  while ($products = tep_db_fetch_array($products_query)) {
    $rows++;

    if (strlen($rows) < 2) {
      $rows = '0' . $rows;
    }
?>
              <tr onMouseOver="rowOverEffect(this)" onMouseOut="rowOutEffect(this)" onClick="document.location.href='<?php echo tep_href_link(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $products['products_id'] . '&origin=' . FILENAME_STATS_PRODUCTS_PURCHASED . '?page=' . $HTTP_GET_VARS['page'], 'NONSSL'); ?>'">
                <td><?php echo $rows; ?>.</td>
                <td><?php echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $products['products_id'] . '&origin=' . FILENAME_STATS_PRODUCTS_PURCHASED . '?page=' . $HTTP_GET_VARS['page'], 'NONSSL') . '">' . $products['products_name'] . '</a>'; ?></td>
                <td align="center"><?php echo $products['products_ordered']; ?>&nbsp;</td>
              </tr>
<?php
  }
?>
            </table></td>
          </tr>
          <tr>
            <td colspan="3"><table class="table table-bordered table-hover">
              <tr>
                <td><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                <td align="right"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?>&nbsp;</td>
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