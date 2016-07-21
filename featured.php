<?php

/*

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
  

  Featured Products admin

*/



  require('includes/application_top.php');

  

   

  if($_GET['featured_group'])

  {

    $featured_group = $_GET['featured_group'];

    $featured_title = $dictionary[$featured_group].'_featured_product_title';

    //die($featured_title); 

    $fea = $dictionary[$featured_group];

  }

  else

      $featured_group = 0;

      

  

  function tep_set_featured_status($featured_id, $status) {

    if ($status == '1') {

      return tep_db_query("update " . TABLE_FEATURED . " set status = '1', expires_date = NULL, date_status_change = NULL where featured_id = '" . $featured_id . "'");

    } elseif ($status == '0') {

      return tep_db_query("update " . TABLE_FEATURED . " set status = '0', date_status_change = now() where featured_id = '" . $featured_id . "'");

    } else {

      return -1;

    }

  }



  require(DIR_WS_CLASSES . 'currencies.php');

  $currencies = new currencies();



  switch ($HTTP_GET_VARS['action']) {

    case 'setflag':

      tep_set_featured_status($HTTP_GET_VARS['id'], $HTTP_GET_VARS['flag']);

      tep_redirect(tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $HTTP_GET_VARS['id'] . '&featured_group=' . $HTTP_GET_VARS['featured_group']));

      break;

    case 'insert':

      $expires_date = '';

      if ($HTTP_POST_VARS['day'] && $HTTP_POST_VARS['month'] && $HTTP_POST_VARS['year']) {

        $expires_date = $HTTP_POST_VARS['year'];

        $expires_date .= (strlen($HTTP_POST_VARS['month']) == 1) ? '0' . $HTTP_POST_VARS['month'] : $HTTP_POST_VARS['month'];

        $expires_date .= (strlen($HTTP_POST_VARS['day']) == 1) ? '0' . $HTTP_POST_VARS['day'] : $HTTP_POST_VARS['day'];

      }

      tep_db_query("insert into " . TABLE_FEATURED . " (products_id, featured_date_added, expires_date, status, featured_group) values ('" . $HTTP_POST_VARS['products_id'] . "', now(), '" . $expires_date . "', '1','".$featured_group."')");

      tep_redirect(tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'].'&featured_group='.$featured_group));

      break;

    case 'update':

      $expires_date = '';

      if ($HTTP_POST_VARS['day'] && $HTTP_POST_VARS['month'] && $HTTP_POST_VARS['year']) {

        $expires_date = $HTTP_POST_VARS['year'];

        $expires_date .= (strlen($HTTP_POST_VARS['month']) == 1) ? '0' . $HTTP_POST_VARS['month'] : $HTTP_POST_VARS['month'];

        $expires_date .= (strlen($HTTP_POST_VARS['day']) == 1) ? '0' . $HTTP_POST_VARS['day'] : $HTTP_POST_VARS['day'];

      }



      tep_db_query("update " . TABLE_FEATURED . " set featured_last_modified = now(), expires_date = '" . $expires_date . "' where featured_id = '" . $HTTP_POST_VARS['featured_id'] . "'");

      tep_redirect(tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $HTTP_POST_VARS['featured_id'] . '&featured_group=' . $HTTP_GET_VARS['featured_group']));

      break;

    case 'deleteconfirm':

       $featured_id = tep_db_prepare_input($HTTP_GET_VARS['sID']);

      

      tep_db_query("delete from " . TABLE_FEATURED . " where featured_id = '" . tep_db_input($featured_id) . "'");



      tep_redirect(tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'].'&featured_group='.$HTTP_GET_VARS['featured_group']));

      break;

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

      <tr>

        <td><table class="table table-bordered table-hover">

          <tr>

            <td>

                <?php

                    switch($featured_group)

                    {

                        case 1:

                            echo one_featured_product_title;

                        break;

                        

                        case 2:

                            echo two_featured_product_title;

                        break;

                        

                        case 3:

                            echo three_featured_product_title;

                        break;

                    

                        default:

                            echo HEADING_TITLE;

                        break;

                        

                    }

                ?>

            </td>

            <td align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>

          </tr>

        </table></td>

      </tr>

<?php

  if ( ($HTTP_GET_VARS['action'] == 'new') || ($HTTP_GET_VARS['action'] == 'edit') ) {

    $form_action = 'insert';

    if ( ($HTTP_GET_VARS['action'] == 'edit') && ($HTTP_GET_VARS['sID']) ) {

	  $form_action = 'update';



      $product_query = tep_db_query("select p.products_id, pd.products_name, s.expires_date from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_FEATURED . " s where p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_id = s.products_id and s.featured_id = '" . $HTTP_GET_VARS['sID'] . "' order by pd.products_name");

      $product = tep_db_fetch_array($product_query);



      $sInfo = new objectInfo($product);

    } else {

      $sInfo = new objectInfo(array());



// create an array of featured products, which will be excluded from the pull down menu of products

// (when creating a new featured product)

      $featured_array = array();

      $featured_query = tep_db_query("select p.products_id from " . TABLE_PRODUCTS . " p, " . TABLE_FEATURED . " s where s.products_id = p.products_id");

      //$featured_query = tep_db_query("select p.products_id from " . TABLE_PRODUCTS . " p, " . TABLE_FEATURED . " s left join " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c on p.products_id = p2c.products_id left join " . TABLE_CATEGORIES . " c on p2c.categories_id = c.categories_id where s.products_id = p.products_id OR p.products_status = '0' OR c.categories_status = '0'");



	  while ($featured = tep_db_fetch_array($featured_query)) {

        $featured_array[] = $featured['products_id'];

      }

    }

?>

      <tr><form name="new_feature" <?php echo 'action="' . tep_href_link(FILENAME_FEATURED, tep_get_all_get_params(array('action', 'info', 'sID')) . 'action=' . $form_action.'&featured_group='.$featured_group, 'NONSSL') . '"'; ?> method="post"><?php if ($form_action == 'update') echo tep_draw_hidden_field('featured_id', $HTTP_GET_VARS['sID']); ?>

        <td><br><table class="table table-bordered table-hover">

          <tr>

            <td>

				<?php 

					echo TEXT_FEATURED_PRODUCT;

				?>&nbsp;

			</td>

            <td>            	

				<?php 

					//echo ($sInfo->products_name) ? $sInfo->products_name : tep_draw_products_pull_down('products_id', 'style="font-size:10px"', $featured_array); echo tep_draw_hidden_field('products_price', $sInfo->products_price); 

					echo (($sInfo->products_name) ? $sInfo->products_name : '<div id="div_listing"></div><span id="span_loader" style="display:none;">' . tep_image('images/ajax-loader.gif') . '</span>');

					echo tep_draw_hidden_field('products_id');

					echo tep_draw_hidden_field('products_price', $sInfo->products_price);					

				?>

			</td>

          </tr>

          <tr>

            <td><?php echo TEXT_FEATURED_EXPIRES_DATE; ?>&nbsp;</td>

            <td><?php echo tep_draw_input_field('day', substr($sInfo->expires_date, 8, 2), 'size="2" maxlength="2" class="cal-TextBox"') . tep_draw_input_field('month', substr($sInfo->expires_date, 5, 2), 'size="2" maxlength="2" class="cal-TextBox"') . tep_draw_input_field('year', substr($sInfo->expires_date, 0, 4), 'size="4" maxlength="4" class="cal-TextBox"'); ?><a class="so-BtnLink" href="javascript:calClick();return false;" onMouseOver="calSwapImg('BTN_date', 'img_Date_OVER',true);" onMouseOut="calSwapImg('BTN_date', 'img_Date_UP',true);" onClick="calSwapImg('BTN_date', 'img_Date_DOWN');showCalendar('new_feature','dteWhen','BTN_date');return false;"><?php echo tep_image(DIR_WS_IMAGES . 'cal_date_up.gif', 'Calendar', '22', '17', 'align="absmiddle" name="BTN_date"'); ?></a></td>

          </tr>

        </table></td>

      </tr>

      <tr>

        <td><table class="table table-bordered table-hover">

          <tr>

            <td align="right"><br><?php echo (($form_action == 'insert') ? tep_image_submit('button_insert_b.gif', IMAGE_INSERT) : tep_image_submit('button_update_b.gif', IMAGE_UPDATE)). '&nbsp;&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $HTTP_GET_VARS['sID'].'&featured_group='.$_GET['featured_group']) . '">' . tep_image_button('button_cancel_b.gif', IMAGE_CANCEL) . '</a>'; ?></td>

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

                <td align="right">&nbsp;</td>

                <td align="right"><?php echo TABLE_HEADING_STATUS; ?></td>

                <td align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>

              </tr>

<?php

    $featured_query_raw = "select p.products_id, pd.products_name, s.featured_id, s.featured_date_added, s.featured_last_modified, s.expires_date, s.date_status_change, s.status from " . TABLE_PRODUCTS . " p, " . TABLE_FEATURED . " s, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_id = s.products_id and s.featured_group = '".$featured_group."' order by pd.products_name";

    $featured_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $featured_query_raw, $featured_query_numrows);

    $featured_query = tep_db_query($featured_query_raw);

    while ($featured = tep_db_fetch_array($featured_query)) {

      if ( ((!$HTTP_GET_VARS['sID']) || ($HTTP_GET_VARS['sID'] == $featured['featured_id'])) && (!$sInfo) ) {



        $products_query = tep_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $featured['products_id'] . "'");

        $products = tep_db_fetch_array($products_query);

        $sInfo_array = array_merge($featured, $products);

        $sInfo = new objectInfo($sInfo_array);

      }



      if ( (is_object($sInfo)) && ($featured['featured_id'] == $sInfo->featured_id) ) {

        echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $sInfo->featured_id . '&action=edit'.'&featured_group='.$_GET['featured_group']) . '\'">' . "\n";

      } else {

        echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $featured['featured_id'].'&featured_group='.$_GET['featured_group']) . '\'">' . "\n";

      }

?>

                <td><?php echo $featured['products_name']; ?></td>

                <td align="right">&nbsp;</td>

                <td align="right">

<?php

      if ($featured['status'] == '1') {

        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_FEATURED, 'action=setflag&flag=0&id=' . $featured['featured_id'].'&featured_group='.$_GET['featured_group'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';

      } else {

        echo '<a href="' . tep_href_link(FILENAME_FEATURED, 'action=setflag&flag=1&id=' . $featured['featured_id'].'&featured_group='.$_GET['featured_group'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);

      }

?></td>

                <td align="right"><?php if ( (is_object($sInfo)) && ($featured['featured_id'] == $sInfo->featured_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $featured['featured_id'].'&featured_group='.$_GET['featured_group']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>

      </tr>

<?php

    }

?>

              <tr>

                <td colspan="4"><table class="table table-bordered table-hover">

                  <tr>

                    <td><?php echo $featured_split->display_count($featured_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_FEATURED); ?></td>

                    <td align="right"><?php echo $featured_split->display_links($featured_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>

                  </tr>

<?php

  if (!$HTTP_GET_VARS['action']) {

?>

                  <tr> 

                    <td colspan="2" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&action=new&featured_group='.$featured_group) . '">' . tep_image_button('button_new_product.gif', IMAGE_NEW_PRODUCT) . '</a>'; ?></td>

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

  switch ($HTTP_GET_VARS['action']) {

    case 'delete':

      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_FEATURED . '</b>');



      $contents = array('form' => tep_draw_form('featured', FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $_GET['sID'] .'&featured_group=' . $_GET['featured_group'] . '&action=deleteconfirm'));

      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);

      $contents[] = array('text' => '<br><b>' . $sInfo->products_name . '</b>');

      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . '&nbsp;<a href="' . tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $sInfo->featured_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');

      break;

    default:

      if (is_object($sInfo)) {

        $heading[] = array('text' => '<b>' . $sInfo->products_name . '</b>');



        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $sInfo->featured_id . '&action=edit&featured_group='.$featured_group) . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $sInfo->featured_id . '&action=delete&featured_group='.$featured_group) . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');

        $contents[] = array('text' => '<br>' . TEXT_INFO_DATE_ADDED . ' ' . tep_date_short($sInfo->featured_date_added));

        $contents[] = array('text' => '' . TEXT_INFO_LAST_MODIFIED . ' ' . tep_date_short($sInfo->featured_last_modified));

        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_info_image($sInfo->products_image, $sInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT));



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

<script language="javascript" src="includes/featured.js"></script>

<?php

  if ( ($HTTP_GET_VARS['action'] == 'new') || ($HTTP_GET_VARS['action'] == 'edit') ) {

?>

<link rel="stylesheet" type="text/css" href="includes/javascript/calendar.css">

<script language="JavaScript" src="includes/javascript/calendarcode.js"></script>

<?php

  }

?>

<script type="text/javascript">

<?php echo ($HTTP_GET_VARS['action']=='new' ? 'displaySelection(\'div_listing\', \'C0\', \'F\');' : ''); ?>

</script>

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>