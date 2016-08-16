<?php
/*
  $Id: specials.php,v 1.41 2003/06/29 22:50:52 hpdl Exp $

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

  require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
  
  // BOF Separate Pricing Per Customer
      $customers_groups_query = tep_db_query("select customers_group_name, customers_group_id from " . TABLE_CUSTOMERS_GROUPS . " order by customers_group_id ");
    while ($existing_groups = tep_db_fetch_array($customers_groups_query)) {
         $input_groups[] = array("id" => $existing_groups['customers_group_id'], "text" => $existing_groups['customers_group_name']);
        $all_groups[$existing_groups['customers_group_id']] = $existing_groups['customers_group_name'];
    }
// EOF Separate Pricing Per Customer


  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
		case 'autocomplete':
			$results = array();
			$query = tep_db_query("select products_id, products_name from products_description where products_name like '%" . $_REQUEST['term'] . "%'");
			while ($entry = tep_db_fetch_array($query)){
				$results[] = array('value' => $entry['products_id'], 'label' => $entry['products_name']);
			}
			echo json_encode($results);
			exit();
      case 'setflag':
        tep_set_specials_status($HTTP_GET_VARS['id'], $HTTP_GET_VARS['flag']);

        tep_redirect(tep_href_link(FILENAME_SPECIALS, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'sID=' . $HTTP_GET_VARS['id'], 'NONSSL'));
        break;
      case 'insert':
        $products_id = tep_db_prepare_input($HTTP_POST_VARS['products_id']);
        $products_price = tep_db_prepare_input($HTTP_POST_VARS['products_price']);
        $specials_price = tep_db_prepare_input($HTTP_POST_VARS['specials_price']);
        $day = tep_db_prepare_input($HTTP_POST_VARS['day']);
        $month = tep_db_prepare_input($HTTP_POST_VARS['month']);
        $year = tep_db_prepare_input($HTTP_POST_VARS['year']);
		
		// BOF Separate Pricing Per Customer
        $customers_group = tep_db_prepare_input($_POST['customers_group']);
        $price_query = tep_db_query("select customers_group_price from " . TABLE_PRODUCTS_GROUPS. " WHERE products_id = ".(int)$products_id . " AND customers_group_id  = ".(int)$customers_group);
        while ($gprices = tep_db_fetch_array($price_query)) {
            $products_price = $gprices['customers_group_price'];
        }
       if (substr($specials_price, -1) == '%' && $customers_group == '0') {
          $new_special_insert_query = tep_db_query("select products_id, products_price from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
          $new_special_insert = tep_db_fetch_array($new_special_insert_query);

          $products_price = $new_special_insert['products_price'];
          $specials_price = ($products_price - (($specials_price / 100) * $products_price));
       } elseif (substr($specials_price, -1) == '%' && $customers_group != '0') {
 				$specials_price = ($products_price - (($specials_price / 100) * $products_price));
        }
// EOF Separate Pricing Per Customer


        if (substr($specials_price, -1) == '%') {
          $new_special_insert_query = tep_db_query("select products_id, products_price from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
          $new_special_insert = tep_db_fetch_array($new_special_insert_query);

          $products_price = $new_special_insert['products_price'];
          $specials_price = ($products_price - (($specials_price / 100) * $products_price));
        }

        $expires_date = '';
        if (tep_not_null($day) && tep_not_null($month) && tep_not_null($year)) {
          $expires_date = $year;
          $expires_date .= (strlen($month) == 1) ? '0' . $month : $month;
          $expires_date .= (strlen($day) == 1) ? '0' . $day : $day;
        }

      //  tep_db_query("insert into " . TABLE_SPECIALS . " (products_id, specials_new_products_price, specials_date_added, expires_date, status) values ('" . (int)$products_id . "', '" . tep_db_input($specials_price) . "', now(), '" . tep_db_input($expires_date) . "', '1')");
        
        // BOF Separate Pricing Per Customer
    tep_db_query("insert into " . TABLE_SPECIALS . " (products_id, specials_new_products_price, specials_date_added, expires_date, status, customers_group_id,discount) values ('" . (int)$products_id . "', '" . tep_db_input($specials_price) . "', now(), '" . tep_db_input($expires_date) . "', '1', ".(int)$customers_group.",'" . tep_db_input($HTTP_POST_VARS['specials_price']) . "')");
// EOF Separate Pricing Per Customer


        tep_redirect(tep_href_link(FILENAME_SPECIALS, 'page=' . $HTTP_GET_VARS['page']));
        break;
      case 'update':
        
        $specials_id = tep_db_prepare_input($HTTP_POST_VARS['specials_id']);
        $products_price = tep_db_prepare_input($HTTP_POST_VARS['products_price']);
        $specials_price = tep_db_prepare_input($HTTP_POST_VARS['specials_price']);
        $day = tep_db_prepare_input($HTTP_POST_VARS['day']);
        $month = tep_db_prepare_input($HTTP_POST_VARS['month']);
        $year = tep_db_prepare_input($HTTP_POST_VARS['year']);

        if (substr($specials_price, -1) == '%'){
          $specials_price = ($products_price - (($specials_price / 100) * $products_price));
        } 

        $expires_date = '';
        if (tep_not_null($day) && tep_not_null($month) && tep_not_null($year)) {
          $expires_date = $year;
          $expires_date .= (strlen($month) == 1) ? '0' . $month : $month;
          $expires_date .= (strlen($day) == 1) ? '0' . $day : $day;
        }

        tep_db_query("update " . TABLE_SPECIALS . " set specials_new_products_price = '" . tep_db_input($specials_price) . "', specials_last_modified = now(), expires_date = '" . tep_db_input($expires_date) . "', discount = '" . tep_db_input($HTTP_POST_VARS['specials_price']) . "' where specials_id = '" . (int)$specials_id . "'");

        tep_redirect(tep_href_link(FILENAME_SPECIALS, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $specials_id));
        break;
      case 'deleteconfirm':
        $specials_id = tep_db_prepare_input($HTTP_GET_VARS['sID']);

        tep_db_query("delete from " . TABLE_SPECIALS . " where specials_id = '" . (int)$specials_id . "'");

        tep_redirect(tep_href_link(FILENAME_SPECIALS, 'page=' . $HTTP_GET_VARS['page']));
        break;
    }
  }
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<div id="popupcalendar" class="text" style="position:absolute; z-index:99999;"></div>         
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
      
<?php
  if ( ($action == 'new') || ($action == 'edit') ) {
    $form_action = 'insert';
    if ( ($action == 'edit') && isset($HTTP_GET_VARS['sID']) ) {
      $form_action = 'update';

     // $product_query = tep_db_query("select p.products_id, pd.products_name, p.products_price, s.specials_new_products_price, s.expires_date from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_SPECIALS . " s where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = s.products_id and s.specials_id = '" . (int)$HTTP_GET_VARS['sID'] . "'");
      // BOF Separate Pricing Per Customer
      $product_query = tep_db_query("select p.products_id, pd.products_name, p.products_price, s.specials_new_products_price, s.expires_date, s.customers_group_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_SPECIALS . " s where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = s.products_id and s.specials_id = '" . (int)$HTTP_GET_VARS['sID'] . "'");

    $product = tep_db_fetch_array($product_query);
      
      $customer_group_price_query = tep_db_query("select customers_group_price from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" . $product['products_id']. "' and customers_group_id =  '" . $product['customers_group_id'] . "'");
         if ($customer_group_price = tep_db_fetch_array($customer_group_price_query)) {
            $product['products_price']= $customer_group_price['customers_group_price'];
         }
// EOF Separate Pricing Per Customer 	

    //  $product = tep_db_fetch_array($product_query);

      $sInfo = new objectInfo($product);
    } else {
      $sInfo = new objectInfo(array());

// create an array of products on special, which will be excluded from the pull down menu of products
// (when creating a new product on special)
    // BOF Separate Pricing Per Customer
/*      $specials_array = array();

      
      $specials_query = tep_db_query("select p.products_id from " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s where s.products_id = p.products_id");
      while ($specials = tep_db_fetch_array($specials_query)) {
        $specials_array[] = $specials['products_id'];
 } */
    $specials_array = array();
    $specials_query = tep_db_query("select p.products_id, s.customers_group_id from " .  TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s where s.products_id = p.products_id");
    while ($specials = tep_db_fetch_array($specials_query)) {
       $specials_array[] = (int)$specials['products_id'].":".(int)$specials['customers_group_id'];
    }
    }

    if(isset($HTTP_GET_VARS['sID']) && $sInfo->customers_group_id!= '0') {
        $customer_group_price_query = tep_db_query("select customers_group_price from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" . $sInfo->products_id . "' and customers_group_id =  '" . $sInfo->customers_group_id . "'");
          if ($customer_group_price = tep_db_fetch_array($customer_group_price_query)) {
            $sInfo->products_price = $customer_group_price['customers_group_price'];
          }
       }

      
    
?>
      <tr><form name="new_special" <?php echo 'action="' . tep_href_link(FILENAME_SPECIALS, tep_get_all_get_params(array('action', 'info', 'sID')) . 'action=' . $form_action, 'NONSSL') . '"'; ?> method="post"><?php if ($form_action == 'update') echo tep_draw_hidden_field('specials_id', $HTTP_GET_VARS['sID']); ?>
        <td><br><table class="table table-bordered table-hover">
          <tr>
            <td><?php echo TEXT_SPECIALS_PRODUCT; ?>&nbsp;</td>
            <td>
			<?php 
			//echo (isset($sInfo->products_name)) ? $sInfo->products_name . ' <small>(' . $currencies->format($sInfo->products_price) . ')</small>' : tep_draw_products_pull_down('products_id', 'style="font-size:10px"', $specials_array, 'specials'); echo tep_draw_hidden_field('products_price', (isset($sInfo->products_price) ? $sInfo->products_price : '')); 
			echo (isset($sInfo->products_name) ? $sInfo->products_name . ' <small>(' . $currencies->format($sInfo->products_price) . ')</small>' : '<div class="ui-widget"></div><input id="product" style="width:400px;" /><input type="hidden" name="products_id" id="productid" />');
            echo tep_draw_hidden_field('products_price', (isset($sInfo->products_price) ? $sInfo->products_price : ''));
			?>
			</td>
                        
                                  </tr>
<!-- BOF Separate Pricing per Customer -->
          <tr>
            <td><?php echo TEXT_SPECIALS_GROUPS; ?>&nbsp;</td>
            <td><?php if (isset($sInfo->customers_group_id)) {
            for ($x=0; $x<count($input_groups); $x++) {
              if ($input_groups[$x]['id'] == $sInfo->customers_group_id) {
            echo $input_groups[$x]['text'];
              }
            } // end for loop
           } else {
         echo tep_draw_pull_down_menu('customers_group', $input_groups, (isset($sInfo->customers_group_id)? $sInfo->customers_group_id:''));
         } ?> </td>
<!-- EOF Separate Pricing per Customer -->

          </tr>
          <tr>
            <td><?php echo TEXT_SPECIALS_SPECIAL_PRICE; ?>&nbsp;</td>
            <td><?php echo tep_draw_input_field('specials_price', (isset($sInfo->specials_new_products_price) ? $sInfo->specials_new_products_price : '')); ?></td>
          </tr>
          <tr>
            <td><?php echo TEXT_SPECIALS_EXPIRES_DATE; ?>&nbsp;</td>
            <td><?php echo tep_draw_input_field('day', (isset($sInfo->expires_date) ? substr($sInfo->expires_date, 8, 2) : ''), 'size="2" maxlength="2" class="cal-TextBox"') . tep_draw_input_field('month', (isset($sInfo->expires_date) ? substr($sInfo->expires_date, 5, 2) : ''), 'size="2" maxlength="2" class="cal-TextBox"') . tep_draw_input_field('year', (isset($sInfo->expires_date) ? substr($sInfo->expires_date, 0, 4) : ''), 'size="4" maxlength="4" class="cal-TextBox"'); ?><a class="so-BtnLink" href="javascript:calClick();return false;" onMouseOver="calSwapImg('BTN_date', 'img_Date_OVER',true);" onMouseOut="calSwapImg('BTN_date', 'img_Date_UP',true);" onClick="calSwapImg('BTN_date', 'img_Date_DOWN');showCalendar('new_special','dteWhen','BTN_date');return false;"><?php echo tep_image(DIR_WS_IMAGES . 'cal_date_up.gif', 'Calendar', '22', '17', 'align="absmiddle" name="BTN_date"'); ?></a></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table class="table table-bordered table-hover">
          <tr>
            <td><br><?php echo TEXT_SPECIALS_PRICE_TIP; ?></td>
            <td align="right"><br><?php echo (($form_action == 'insert') ? tep_image_submit('button_insert_b.gif', IMAGE_INSERT) : tep_image_submit('button_update_b.gif', IMAGE_UPDATE)). '&nbsp;&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_SPECIALS, 'page=' . $HTTP_GET_VARS['page'] . (isset($HTTP_GET_VARS['sID']) ? '&sID=' . $HTTP_GET_VARS['sID'] : '')) . '">' . tep_image_button('button_cancel_b.gif', IMAGE_CANCEL) . '</a>'; ?></td>
          </tr>
        </table></td>
      </form></tr>
<?php
  } else {
?>
      <tr>
        <td><table class="table table-bordered table-hover">
          <tr>

            <td><table class="table table-bordered table-hover">
              <tr>
                <td><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td align="right"><?php echo TABLE_HEADING_PRODUCTS_PRICE; ?></td>
                <td align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
   // $specials_query_raw = "select p.products_id, pd.products_name, p.products_price, s.specials_id, s.specials_new_products_price, s.specials_date_added, s.specials_last_modified, s.expires_date, s.date_status_change, s.status from " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = s.products_id order by pd.products_name";
// BOF Separate Pricing Per Customer
    $all_groups = array();
    $customers_groups_query = tep_db_query("select customers_group_name, customers_group_id from " . TABLE_CUSTOMERS_GROUPS . " order by customers_group_id ");
    while ($existing_groups =  tep_db_fetch_array($customers_groups_query)) {
      $all_groups[$existing_groups['customers_group_id']] = $existing_groups['customers_group_name'];
    }

   $specials_query_raw = "select p.products_id, pd.products_name, p.products_price, s.specials_id, s.customers_group_id, s.specials_new_products_price, s.specials_date_added, s.specials_last_modified, s.expires_date, s.date_status_change, s.status from " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = s.products_id order by pd.products_name";

   $customers_group_prices_query = tep_db_query("select s.products_id, s.customers_group_id, pg.customers_group_price from " . TABLE_SPECIALS . " s LEFT JOIN " . TABLE_PRODUCTS_GROUPS . " pg using (products_id, customers_group_id) ");

 while ($_customers_group_prices = tep_db_fetch_array($customers_group_prices_query)) {
 $customers_group_prices[] = $_customers_group_prices;
 }

    $specials_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $specials_query_raw, $specials_query_numrows);
    $specials_query = tep_db_query($specials_query_raw);
  //  while ($specials = tep_db_fetch_array($specials_query)) {
    
    // BOF Separate Pricing Per Customer
    $no_of_rows_in_specials = tep_db_num_rows($specials_query);
    while ($specials = tep_db_fetch_array($specials_query)) {
    for ($y = 0; $y < $no_of_rows_in_specials; $y++) {
    if ( tep_not_null($customers_group_prices[$y]['customers_group_price']) && $customers_group_prices[$y]['products_id'] == $specials['products_id'] && $customers_group_prices[$y]['customers_group_id'] == $specials['customers_group_id']) {
    $specials['products_price'] = $customers_group_prices[$y]['customers_group_price'] ;
    } // end if (tep_not_null($customers_group_prices[$y]['customers_group_price'] etcetera
    } // end for loop
// EOF Separate Pricing Per Customer

      if ((!isset($HTTP_GET_VARS['sID']) || (isset($HTTP_GET_VARS['sID']) && ($HTTP_GET_VARS['sID'] == $specials['specials_id']))) && !isset($sInfo)) {
        $products_query = tep_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . (int)$specials['products_id'] . "'");
        $products = tep_db_fetch_array($products_query);
        $sInfo_array = array_merge($specials, $products);
        $sInfo = new objectInfo($sInfo_array);
      }

      if (isset($sInfo) && is_object($sInfo) && ($specials['specials_id'] == $sInfo->specials_id)) {
        echo '                  <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_SPECIALS, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $sInfo->specials_id . '&action=edit') . '\'">' . "\n";
      } else {
        echo '                  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_SPECIALS, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $specials['specials_id']) . '\'">' . "\n";
      }
?>
                <td><?php echo $specials['products_name']; ?></td>
               <?php /* <td  class="dataTableContent" align="right"><span class="oldPrice"><?php echo $currencies->format($specials['products_price']); ?></span> <span class="specialPrice"><?php echo $currencies->format($specials['specials_new_products_price']); ?></span></td>*/ ?>
                <!-- BOF Separate Pricing Per Customer -->
                <td align="right"><span class="oldPrice"><?php echo $currencies->format($specials['products_price']); ?></span> <span class="specialPrice"><?php echo $currencies->format($specials['specials_new_products_price']) . " (".  $all_groups[$specials['customers_group_id']] . ")"; ?></span></td>
<!-- EOF Separate Pricing per Customer -->

                <td align="right">
<?php
      if ($specials['status'] == '1') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_SPECIALS, 'action=setflag&flag=0&id=' . $specials['specials_id'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_SPECIALS, 'action=setflag&flag=1&id=' . $specials['specials_id'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
                <td align="right"><?php if (isset($sInfo) && is_object($sInfo) && ($specials['specials_id'] == $sInfo->specials_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_SPECIALS, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $specials['specials_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
      </tr>
<?php
    }
?>
              <tr>
                <td colspan="4"><table class="table table-bordered table-hover">
                  <tr>
                    <td><?php echo $specials_split->display_count($specials_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_SPECIALS); ?></td>
                    <td align="right"><?php echo $specials_split->display_links($specials_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>
                  </tr>
<?php
  if (empty($action)) {
?>
                  <tr>
                    <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_SPECIALS, 'page=' . $HTTP_GET_VARS['page'] . '&action=new') . '">' . tep_image_button('button_new_product.gif', IMAGE_NEW_PRODUCT) . '</a>'; ?></td>
                  </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_SPECIALS . '</b>');

      $contents = array('form' => tep_draw_form('specials', FILENAME_SPECIALS, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $sInfo->specials_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $sInfo->products_name . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . '&nbsp;<a href="' . tep_href_link(FILENAME_SPECIALS, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $sInfo->specials_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (is_object($sInfo)) {
        $heading[] = array('text' => '<b>' . $sInfo->products_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_SPECIALS, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $sInfo->specials_id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_SPECIALS, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $sInfo->specials_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_DATE_ADDED . ' ' . tep_date_short($sInfo->specials_date_added));
        $contents[] = array('text' => '' . TEXT_INFO_LAST_MODIFIED . ' ' . tep_date_short($sInfo->specials_last_modified));
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_info_image($sInfo->products_image, $sInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT));
        $contents[] = array('text' => '<br>' . TEXT_INFO_ORIGINAL_PRICE . ' ' . $currencies->format($sInfo->products_price));
        $contents[] = array('text' => '' . TEXT_INFO_NEW_PRICE . ' ' . $currencies->format($sInfo->specials_new_products_price));
      //  $contents[] = array('text' => '' . TEXT_INFO_PERCENTAGE . ' ' . number_format(100 - (($sInfo->specials_new_products_price / $sInfo->products_price) * 100)) . '%');
        // BOF edited for SPPC where a product price can be 0 if there is no group price, gives a warning divison by zero
        $contents[] = array('text' => '' . TEXT_INFO_PERCENTAGE . ' ' . number_format(100 - (($sInfo->specials_new_products_price / ($sInfo->products_price == 0 ? $sInfo->specials_new_products_price : $sInfo->products_price)) * 100)) . '%');
// EOF edited for SPPC


        $contents[] = array('text' => '<br>' . TEXT_INFO_EXPIRES_DATE . ' <b>' . tep_date_short($sInfo->expires_date) . '</b>');
        $contents[] = array('text' => '' . TEXT_INFO_STATUS_CHANGE . ' ' . tep_date_short($sInfo->date_status_change));
      }
      break;
  }
  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
}
?>
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
<script type="text/javascript">
  jQuery(function() {
    jQuery( "#product" ).autocomplete({
      source: "specials.php?action=autocomplete",
      minLength: 3,
      select: function( event, ui ) {
		event.preventDefault();
		jQuery('#productid').val(ui.item.value);
		jQuery('#product').val(ui.item.label);
      }
    });
  });
</script>
<?php
  if ( ($action == 'new') || ($action == 'edit') ) {
?>
<link rel="stylesheet" type="text/css" href="includes/javascript/calendar.css">
<script language="JavaScript" src="includes/javascript/calendarcode.js"></script>
<?php
  }
?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>