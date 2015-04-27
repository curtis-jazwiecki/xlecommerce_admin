<?php
//////////////////////////////////////////
//
//  Admin File Access
//
//  Jeff Brutsche
//  Outdoor Business Network
//
//////////////////////////////////////////

  require('includes/application_top.php');
/*
  $current_boxes = DIR_FS_ADMIN . DIR_WS_BOXES;
  $current_files = DIR_FS_ADMIN;
*/

if(isset($_POST['update_admin_files_access']))
  {
	if($login_groups_id == 1)
	  $current_box_query = tep_db_query("select admin_files_id, admin_files_name, admin_files_is_boxes, admin_files_to_boxes, admin_groups_id from " . TABLE_ADMIN_FILES . " WHERE admin_groups_id LIKE '%1%' ORDER BY admin_files_to_boxes");
	else
	  $current_box_query = tep_db_query("select admin_files_id, admin_files_name, admin_files_is_boxes, admin_files_to_boxes, admin_groups_id from " . TABLE_ADMIN_FILES . " WHERE admin_groups_id LIKE '%1,2%' ORDER BY admin_files_to_boxes");
	  
	while ($files = tep_db_fetch_array($current_box_query))
	  {
		$level2='';
		$level3='';
		$level4='';
		$level5='';
		$level6='';
		if($login_groups_id == 1) {
			$post_check2 = $files['admin_files_id'].'-2';
		}
		$post_check3 = $files['admin_files_id'].'-3';
		$post_check4 = $files['admin_files_id'].'-4';
		$post_check5 = $files['admin_files_id'].'-5';
		$post_check6 = $files['admin_files_id'].'-6';
		if(isset($_POST["$post_check2"]))
		  { $level2=',2'; }
		if(isset($_POST["$post_check3"]))
		  { $level3=',3'; }
		if(isset($_POST["$post_check4"]))
		  { $level4=',4'; }
		if(isset($_POST["$post_check5"]))
		  { $level5=',5'; }
		if(isset($_POST["$post_check6"]))
		  { $level6=',6'; }
		if($login_groups_id == 1) {
			tep_db_query('UPDATE ' . TABLE_ADMIN_FILES . ' SET admin_groups_id = "1'.$level2.$level3.$level4.$level5.$level6.'" WHERE admin_files_id = "'.$files['admin_files_id'].'"' );
		}
		else {
			tep_db_query('UPDATE ' . TABLE_ADMIN_FILES . ' SET admin_groups_id = "1,2'.$level3.$level4.$level5.$level6.'" WHERE admin_files_id = "'.$files['admin_files_id'].'"' );
		}
	  }
  }
if($login_groups_id == 1)
  {
	$gold_array = array("catalog.php", "modules.php", "customers.php", "taxes.php", "localization.php", "reports.php", "tools.php", "categories.php", "products_attributes.php", "manufacturers.php", "reviews.php", "specials.php", "products_expected.php", "modules.php", "customers.php", "orders.php", "countries.php", "zones.php", "geo_zones.php", "tax_classes.php", "tax_rates.php", "orders_status.php", "stats_products_viewed.php", "stats_products_purchased.php", "stats_customers.php", "mail.php", "newsletters.php", "newsletters.php", "whos_online.php", "banner_statistics.php", "coupon_admin.php", "stats_credits.php", "stats_sales_report.php", "listcategories.php", "listproducts.php", "validcategories.php", "treeview.php", "validproducts.php", "category_specials.php", "information.php", "information_form.php", "information_list.php", "information_manager.php", "header_tags.controller.php", "header_tags_english.php", "header_tags_fill_tags.php", "header_tags_popup_help.php", "price_updater.php", "categories_update.php", "featured.php", "xsell.php", "ajax_catalog_list.php", "xml_feed_manager.php", "categories_july24v.php", "attributesManager.php", "edit_orders.php", "edit_orders_add_product.php", "create_order.php", "create_order_process.php", "edit_order_ajax.php", "admin_file_access.php", "training_support.php", "training_videos.php", "support_ticket.php", "webmail.php", "edit_templates.php", "OBN_custom_price_fix.php", "price_updater_price_fix.php", "products_discontinued.php", "attributeManager.php");

	$ultimate_array = array("currencies.php", "languages.php", "file_manager.php", "easypopulate.php", "qbi_about.php", "qbi_create.php", "qbi_config.php", "qbi_discount.php", "qbi_discmatch.php", "qbi_db.php", "qbi_pay.php", "qbi_paymatch.php", "qbi_products.php", "qbi_productsmatch.php", "qbi_ship.php", "qbi_shipmatch.php", "googlesitemap.php", "stats_keywords.php", "user_info_export.php", "advanced_stats.php", "sales_report_export.php", "categories_frontend.php", "frontendcategories_products.php", "select_templates.php", "select_category_template.php");
	if($_POST['action_action'] == "go_ultimate")
	  {
		$where_string = "(";
		for($a=0; $a < sizeof($ultimate_array); $a++)
		  {
			$where_string .= "admin_files_name = '" . $ultimate_array[$a] . "' ";
			if($ultimate_array[$a] != (sizeof($ultimate_array)-1))
				$where_string .= "OR ";
		  }
		$where_string = substr($where_string, 0, -3);
		$where_string .= ")";
		tep_db_query("UPDATE admin_files SET admin_groups_id = '1,2,3,4,5,6' WHERE ".$where_string);
		tep_db_query("UPDATE configuration SET configuration_value = 'ultimate' WHERE configuration_key = 'ADMIN_LEVEL_OF_SERVICE'");
	  }
	if($_POST['action_action'] == "go_gold")
	  {
		$where_string_2 = "(";
		for($a=0; $a < sizeof($gold_array); $a++)
		  {
			$where_string_2 .= "admin_files_name = '" . $gold_array[$a] . "' ";
			if($gold_array[$a] != (sizeof($gold_array)-1))
				$where_string_2 .= "OR ";
		  }
		$where_string_2 = substr($where_string_2, 0, -3);
		$where_string_2 .= ")";
		tep_db_query("UPDATE admin_files SET admin_groups_id = '1,2,3,4,5,6' WHERE ".$where_string_2);
		tep_db_query("UPDATE configuration SET configuration_value = 'gold' WHERE configuration_key = 'ADMIN_LEVEL_OF_SERVICE'");

		// turn off ultimate files
		$where_string = "(";
		for($a=0; $a < sizeof($ultimate_array); $a++)
		  {
			$where_string .= "admin_files_name = '" . $ultimate_array[$a] . "' ";
			if($ultimate_array[$a] != (sizeof($ultimate_array)-1))
				$where_string .= "OR ";
		  }
		$where_string = substr($where_string, 0, -3);
		$where_string .= ")";
		tep_db_query("UPDATE admin_files SET admin_groups_id = '1' WHERE ".$where_string);

	  }
  }
  
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>User File Access</title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table width="760px" border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading">User File Access</td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td valign="top">
<?php
	if($login_groups_id == 1)
	  {
	    echo '<table border="0" width="100%" cellspacing="0" cellpadding="2"><tr><td>';
		echo '	<form method="post" action="admin_file_access.php" name="Gold Access">';
		echo '		<input type="hidden" name="action_action" value="go_gold" />';
		echo '		<input type="submit" name=" Go Gold " alt=" Go Gold " title=" Go Gold " value=" Go Gold" />';
		echo '	</form>';
		echo "&nbsp;&nbsp;";
		echo '	<form method="post" action="admin_file_access.php" name="Ultimate Access">';
		echo '		<input type="hidden" name="action_action" value="go_ultimate" />';
		echo '		<input type="submit" name=" Go Ultimate " alt=" Go Ultimate " title=" Go Ultimate " value=" Go Ultimate" />';
		echo '	</form>';
		echo '</td></tr></table>';
		if($_POST['action_action'] == "go_gold")
		  { echo '<span style="color: #ffffff">Gold Level Added</span><br />'; }
		elseif($_POST['action_action'] == "go_ultimate")
		  { echo '<span style="color: #ffffff">Ultimate Level Added</span><br />'; }
		else
		  { echo '<br />'; }
	  }



// Get Array of Categories
$count = 0;
  $admin_category_query = tep_db_query("select admin_files_is_boxes, admin_files_name, admin_files_id from " . TABLE_ADMIN_FILES . " where admin_files_is_boxes = '1'");
    while ($files = tep_db_fetch_array($admin_category_query))
      {
		$admin_category_array[$count] = "";
		$count++;
	  }
?>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
				  <form method="post" action="admin_file_access.php" name="admin_files_access">
				  <input type="hidden" name="update_admin_files_access" value="update_admin_files_access" />
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent">File Name</td>
                    <?php
					if($login_groups_id == 1)
	                    echo '<td class="dataTableHeadingContent" align="right">Store Owner Access</td>';
					?>
                    <td class="dataTableHeadingContent" align="right">Manager Access</td>
                    <td class="dataTableHeadingContent" align="right">Sales Access</td>
                    <td class="dataTableHeadingContent" align="right">Developer Access</td>
                    <td class="dataTableHeadingContent" align="right">Staff Access</td>
                  </tr>
<?php
//BOF:mod 10jan2014
/*
//EOF:mod 10jan2014
	if($login_groups_id == 1)
	  $current_box_query = tep_db_query("select admin_files_id, admin_files_name, admin_files_is_boxes, admin_files_to_boxes, admin_groups_id from " . TABLE_ADMIN_FILES . " WHERE admin_groups_id LIKE '%1%' ORDER BY admin_files_to_boxes, admin_files_name");
	else
	  $current_box_query = tep_db_query("select admin_files_id, admin_files_name, admin_files_is_boxes, admin_files_to_boxes, admin_groups_id from " . TABLE_ADMIN_FILES . " WHERE admin_groups_id LIKE '%1,2%' ORDER BY admin_files_to_boxes, admin_files_name");
//BOF:mod 10jan2014
*/
	if($login_groups_id == 1)
	  $current_box_query = tep_db_query("select admin_files_id, admin_files_name, admin_files_is_boxes, admin_files_to_boxes, admin_groups_id, is_functionality from " . TABLE_ADMIN_FILES . " WHERE admin_groups_id LIKE '%1%' ORDER BY is_functionality desc, admin_files_to_boxes, admin_files_name");
	else
	  $current_box_query = tep_db_query("select admin_files_id, admin_files_name, admin_files_is_boxes, admin_files_to_boxes, admin_groups_id, is_functionality from " . TABLE_ADMIN_FILES . " WHERE admin_groups_id LIKE '%1,2%' ORDER BY is_functionality, admin_files_to_boxes, admin_files_name");
//EOF:mod 10jan2014
  while ($files = tep_db_fetch_array($current_box_query))
    {
?>
				<tr bgcolor="#DDDDDD">
                  <td class="dataTableContent">
				  <?php
				  if($files['admin_files_is_boxes'] == 1) echo '<b>';
                //BOF:mod 10jan2014
                if($files['is_functionality'] == 1) echo '<b><span  style="color:green;">';
                //EOF:mod 10jan2014
				  echo $files['admin_files_name'];
                //BOF:mod 10jan2014
                if($files['is_functionality'] == 1) echo '</span></b>';
                //EOF:mod 10jan2014
				  if($files['admin_files_is_boxes'] == 1) echo '</b>';				  
				  ?>
                  </td>
			<?php
				if($login_groups_id == 1) { ?>
                  <td class="dataTableHeadingContent" align="right"><input type="checkbox" name="<?php echo $files['admin_files_id'].'-2'?>" value="true" <?php if(preg_match("/2/" ,$files['admin_groups_id'])) echo 'checked'; ?> /></td>
            <?php } ?>
                  <td class="dataTableHeadingContent" align="right"><input type="checkbox" name="<?php echo $files['admin_files_id'].'-3'?>" value="true" <?php if(preg_match("/3/" ,$files['admin_groups_id'])) echo 'checked'; ?> /></td>
                  <td class="dataTableHeadingContent" align="right"><input type="checkbox" name="<?php echo $files['admin_files_id'].'-4' ?>" value="true" <?php if(preg_match("/4/" ,$files['admin_groups_id'])) echo 'checked'; ?> /></td>
                  <td class="dataTableHeadingContent" align="right"><input type="checkbox" name="<?php echo $files['admin_files_id'].'-5' ?>" value="true" <?php if(preg_match("/5/" ,$files['admin_groups_id'])) echo 'checked'; ?> /></td>
                  <td class="dataTableHeadingContent" align="right"><input type="checkbox" name="<?php echo $files['admin_files_id'].'-6' ?>" value="true" <?php if(preg_match("/6/" ,$files['admin_groups_id'])) echo 'checked'; ?> /></td>
                </tr>
<?php
    } 
?>
                <tr>
                  <td colspan="6">
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                      <tr>
                        <td class="smallText" valign="top" align="right">
						  <input border="0" type="image" title=" Update " alt="Update" src="includes/languages/english/images/buttons/button_update_b.gif">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                </form>
              </table>
            </td>
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
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>