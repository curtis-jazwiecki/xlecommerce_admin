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
      tep_redirect(tep_href_link(FILENAME_FEATURED, '', 'NONSSL'));
      break;
    case 'insert':
      $mQuery = tep_db_query("SELECT configuration_value FROM `configuration` where configuration_id = 1236 ");
      $mlist=tep_db_fetch_array($mQuery);
      tep_db_query("UPDATE `configuration` SET `configuration_value` = '".$mlist['configuration_value'].$_POST['featuredCategory'].","."' where configuration_id = 1236 ");
      tep_redirect(tep_href_link(basename($PHP_SELF), 'page=' . $HTTP_GET_VARS['page'].'&featured_group='.$featured_group));
      break;
    
    case 'remove':
      $mQuery = tep_db_query("SELECT configuration_value FROM `configuration` where configuration_id = 1236 ");
      $mlist=tep_db_fetch_array($mQuery);
      
      $newMval = str_replace(",".$_GET['m'].",", ",", $mlist['configuration_value']);
      
      tep_db_query("UPDATE `configuration` SET `configuration_value` = '".$newMval."' where configuration_id = 1236 ");
      tep_redirect(tep_href_link(basename($PHP_SELF)));
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
<?php /*<script language="javascript" src="includes/featured.js"></script>
<?php
  if ( ($HTTP_GET_VARS['action'] == 'new') || ($HTTP_GET_VARS['action'] == 'edit') ) {
?>
<link rel="stylesheet" type="text/css" href="includes/javascript/calendar.css">
<script language="JavaScript" src="includes/javascript/calendarcode.js"></script>
<?php
  }
  */
?> 
<script>
    function removeManu(mid)
    {
        if(confirm('Do you want to delete this category from list'))
        {
            location.href='<?php echo $PHP_SELF?>?action=remove&m='+mid;
        }
    }
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<div id="popupcalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<table width="780" border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">
                Featured Category 
            </td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
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
      <tr><form name="new_feature" <?php echo 'action="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action', 'info', 'sID')) . 'action=' . $form_action, 'NONSSL') . '"'; ?> method="post"><?php if ($form_action == 'update') echo tep_draw_hidden_field('featured_id', $HTTP_GET_VARS['sID']); ?>
        <td><br><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main" valign="top" style="color:#FFFFFF">
				Categories &nbsp;
			</td>
            <td class="main" style="color:#FFFFFF"> 
                <?php echo tep_draw_pull_down_menu3('featuredCategory', tep_get_category_tree3(), '', '');?>
                
                <?php  //tep_get_category_tree3();?>
                
				<?php /*
					//echo ($sInfo->products_name) ? $sInfo->products_name : tep_draw_products_pull_down('products_id', 'style="font-size:10px"', $featured_array); echo tep_draw_hidden_field('products_price', $sInfo->products_price); 
					echo (($sInfo->products_name) ? $sInfo->products_name : '<div id="div_listing"></div><span id="span_loader" style="display:none;">' . tep_image('images/ajax-loader.gif') . '</span>');
					echo tep_draw_hidden_field('products_id');
					echo tep_draw_hidden_field('products_price', $sInfo->products_price);					*/
				?>
			</td>
          </tr>
          
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main" align="right" valign="top"><br><?php echo (($form_action == 'insert') ? tep_image_submit('button_insert_b.gif', IMAGE_INSERT) : tep_image_submit('button_update_b.gif', IMAGE_UPDATE)). '&nbsp;&nbsp;&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $HTTP_GET_VARS['sID']) . '">' . tep_image_button('button_cancel_b.gif', IMAGE_CANCEL) . '</a>'; ?></td>
          </tr>
        </table></td>
      </form></tr>
<?php
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent">Categories</td>
                 <td class="dataTableHeadingContent">&nbsp;</td>
              </tr>
<?php
///////////////////////////////////////
    $mQuery = tep_db_query("SELECT configuration_value FROM `configuration` where configuration_id = 1236 ");
    $mlist=tep_db_fetch_array($mQuery);
    
    
///////////////////////////////////////
    $featured_query_raw = "SELECT `categories_id`,`categories_name` FROM `".TABLE_CATEGORIES_DESCRIPTION."` where categories_id in (".substr($mlist[configuration_value],1,-1).") ORDER BY categories_name";
    //echo $featured_query_raw;
   // $featured_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $featured_query_raw, $featured_query_numrows);
    $featured_query = tep_db_query($featured_query_raw);
    while ($featured = tep_db_fetch_array($featured_query)) {
    /*  if ( ((!$HTTP_GET_VARS['sID']) || ($HTTP_GET_VARS['sID'] == $featured['featured_id'])) && (!$sInfo) ) {

        $products_query = tep_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $featured['products_id'] . "'");
        $products = tep_db_fetch_array($products_query);
        $sInfo_array = array_merge($featured, $products);
        $sInfo = new objectInfo($sInfo_array);
      }

      if ( (is_object($sInfo)) && ($featured['featured_id'] == $sInfo->featured_id) ) {
        echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $sInfo->featured_id . '&action=edit') . '\'">' . "\n";
      } else {*/
        echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" >' . "\n";
     // }
?>
                <td  class="dataTableContent"><?php echo $featured['categories_name']; ?></td>
                <td  class="dataTableContent"><input type="button" value="Remove" style="cursor: pointer;" onclick="removeManu('<?php echo $featured['categories_id']; ?>')"></td>
                
               
      </tr>
<?php
    }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellpadding="0"cellspacing="2">
                  <?php /*<tr>
                    <td class="smallText2" valign="top"><?php echo $featured_split->display_count($featured_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_FEATURED); ?></td>
                    <td class="smallText2" align="right"><?php echo $featured_split->display_links($featured_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>
                  </tr> */?>

                  <tr> 
                    <td colspan="2" align="right"><?php echo '<a href="' . tep_href_link(basename($PHP_SELF), 'page=' . $HTTP_GET_VARS['page'] . '&action=new') . '"><input type="button" value="Add New Categories"></a>'; ?></td>
                  </tr>

                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($HTTP_GET_VARS['action']) {
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_FEATURED . '</b>');

      $contents = array('form' => tep_draw_form('featured', FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $sInfo->featured_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $sInfo->products_name . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . '&nbsp;<a href="' . tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $sInfo->featured_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (is_object($sInfo)) {
        $heading[] = array('text' => '<b>' . $sInfo->products_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $sInfo->featured_id . '&action=edit&featured_group='.$featured_group) . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $sInfo->featured_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
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
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<script type="text/javascript">
<?php echo ($HTTP_GET_VARS['action']=='new' ? 'displaySelection(\'div_listing\', \'C0\', \'F\');' : ''); ?>
</script>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>