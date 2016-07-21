<?php

/*
  $ID: move_vendor_prods.php (for use with MVS) by Craig Garrison Sr, BluCollar Sales
  $Loc: /catalog/admin/ $
  $Mod: MVS V1.2 2009/02/28 JCK/CWG $

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

  require_once ('includes/application_top.php');

  if ($action == 'update') {
    $count_products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " where vendors_id = '" . (int) $delete_vendors_id . "'");
    while ($count_products = tep_db_fetch_array($count_products_query)) {
      $num_products = $count_products['total'];
    }
    $update_query = "update " . TABLE_PRODUCTS . " SET vendors_id = '" . $new_vendors_id . "' where vendors_id = '" . $delete_vendors_id . "';";
    $update_result = tep_db_query($update_query);
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
    <td><table class="table table-bordered">
      
<?php

  if ($action == 'update') {
    $vendor_name_deleted = tep_db_query("select vendors_name from " . TABLE_VENDORS . " where vendors_id = '" . $delete_vendors_id . "'");
    while ($vendor_deleted = tep_db_fetch_array($vendor_name_deleted)) {
      $deleted_vendor = $vendor_deleted['vendors_name'];
    }
    
    $vendor_name_moved = tep_db_query("select vendors_name from " . TABLE_VENDORS . " where vendors_id = '" . $new_vendors_id . "'");
    while ($vendor_moved = tep_db_fetch_array($vendor_name_moved)) {
      $moved_vendor = $vendor_moved['vendors_name'];
    }
    
    if ($update_result) {
?>
      <tr>
        <td align="left">
      </tr>
<?php
      // echo '<br><b>The new Vendor\'s name:  ' . $moved_vendor;
      echo '<br><b>' . $num_products . '</b> products were moved from <b>' . $deleted_vendor . '</b> to <b>' . $moved_vendor . '</b>.<br> You can Go <a href="' . tep_href_link(FILENAME_MOVE_VENDORS) . '"><b>Back and start</b></a> again OR Go <a href="' . tep_href_link(FILENAME_VENDORS) . '"><b>Back To Vendors List</b></a>';
    } else {
?>
      <tr>
        <td align="left">
      </tr>
<?php
      echo '<br><b>NO</b> products were moved from <b>' . $deleted_vendor . '</b> to <b>' . $moved_vendor . '</b>.<br> You should Go <a href="' . tep_href_link(FILENAME_MOVE_VENDORS) . '"><b>Back and start</b></a> over OR Go <a href="' . tep_href_link(FILENAME_VENDORS) . '"><b>Back To Vendors List</b></a>';
    }
?>
<?php 
  } elseif ($action == '') { 
?>
      <tr>
        <td align="left"><?php echo '<a href="' . tep_href_link(FILENAME_VENDORS) . '"><b>Go Back To Vendors List</a>';?></tr>
      <tr>
        <td align="left"><?php echo 'Select the vendors you plan to work with.'; ?>
      </tr>
      <tr bgcolor="#FF0000">
        <td align="left" style="color:#fff;"><?php echo '<b>This action is not easily reversible, and clicking the update button will perform this action immediately, there is no turning back.</b>'; ?>
      </tr>
      <tr>
        <td><table class="table table-bordered table-hover">
          <tr>
            <td><table class="table table-bordered table-hover">
              <tr>
<?php
    echo tep_draw_form ('move_vendor_form', FILENAME_MOVE_VENDORS, tep_get_all_get_params( array ('action') ) . 'action=update', 'post');

    $vendors_array = array (array ('id' => '1',
                                   'text' => 'NONE'
                                  )
                           );
    $vendors_query = tep_db_query ("select vendors_id, 
                                           vendors_name 
                                    from " . TABLE_VENDORS . " 
                                    order by vendors_name
                                 ");
    while ($vendors = tep_db_fetch_array ($vendors_query) ) {
      $vendors_array[] = array ('id' => $vendors['vendors_id'],
                                'text' => $vendors['vendors_name']
                               );
    }
?>
                <td align="left"><?php echo TEXT_VENDOR_CHOOSE_MOVE . ' -->  '; ?><?php echo tep_draw_form('vendors_report', FILENAME_PRODS_VENDORS) . tep_draw_pull_down_menu('delete_vendors_id', $vendors_array);?></td>
              </tr>
              <tr>
                <td align="left"><br><?php echo TEXT_VENDOR_CHOOSE_MOVE_TO . ' -->  '; ?><?php echo tep_draw_form('vendors_report', FILENAME_PRODS_VENDORS) . tep_draw_pull_down_menu('new_vendors_id', $vendors_array);?></td>
              </tr>
              <tr>
                <td><br><?php echo tep_image_submit ('button_update.gif', 'SUBMIT') . ' <a href="' . tep_href_link (FILENAME_MOVE_VENDORS, tep_get_all_get_params (array ('action') ) ) .'">' . tep_image_button ('button_cancel.gif', IMAGE_CANCEL) . '</a>';  ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php 
  } 
?>
      <tr>
        <td colspan="3"><table class="table table-bordered table-hover">
          <tr>
            <td><?php //echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS);?></td>
            <td align="right"><?php //echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'].$vendors_id);?></td>
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