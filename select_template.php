<?php
/*
  $Id: countries.php,v 1.28 2003/06/29 22:50:51 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if(isset($_POST['update_template']))
  {
	$new_template_selection = $_POST['product_listing_value'];
	$template_update_query = tep_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '".$new_template_selection."' where configuration_key = 'PRODUCT_LISTING_TEMPLATE'");
  }

// Begin Template Check
  $template_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'PRODUCT_LISTING_TEMPLATE'");
  $rowz = tep_db_fetch_array($template_query);
  $selected_template = $rowz['configuration_value'];
// End Template Check

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table width="780" border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td>
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading">Select Product Listing Template<br /><span style="font-size: 14px">(Seen on search page and categories page)</span></td>
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
		    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="countries">
            <input type="hidden" name="update_template" value="update_template" />
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top">
                  <table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr class="dataTableHeadingRow">
                	  <td class="dataTableHeadingContent">Preview Layout</td>
	                  <td class="dataTableHeadingContent" align="center">Selected</td>
              		</tr>
<?php
	// Below displays options for template layout - OBN
?>
                    <tr bgcolor="#DDDDDD">
                      <td class="dataTableContent"><b>Standard Layout</b><br />4 columns with small pictures, displays 20 per page. Results usually fit on one page.</td>
                      <td class="dataTableContent" align="center"><input type="radio" name="product_listing_value" value="0" <?php if($selected_template == 0) echo "checked"; ?> /></td>
                    </tr>
                    <tr bgcolor="#DDDDDD">
                      <td class="dataTableContent"><b>Column Layout</b><br />1 product per row, similar to auction sites.</td>
                      <td class="dataTableContent" align="center"><input type="radio" name="product_listing_value" value="1" <?php if($selected_template == 1) echo "checked"; ?> /></td>
                    </tr>
                    <tr bgcolor="#DDDDDD">
                      <td class="dataTableContent"><b>Large Picture Layout</b><br />3 columns, displays larger pictures, name, description, price, and stock information (displays 30 products per page)</td>
                      <td class="dataTableContent" align="center"><input type="radio" name="product_listing_value" value="2" <?php if($selected_template == 2) echo "checked"; ?> /></td>
                    </tr>
                    <tr bgcolor="#DDDDDD">
                      <td class="dataTableContent"><b>Large Descriptive Layout</b><br />1 product per row, displays larger pictures, name, description, price, and stock information</td>
                      <td class="dataTableContent" align="center"><input type="radio" name="product_listing_value" value="3" <?php if($selected_template == 3) echo "checked"; ?> /></td>
                    </tr>
                    <tr>
           				<td colspan="5" align="right"><input border="0" type="image" title="Update" alt="Update" src="includes/languages/english/images/buttons/button_update_b.gif"></td>
					</tr>
            	  </table>
                </td>
          	  </tr>
        	</table>
            </form>
          </td>
      	</tr>
      </table>
    </td>
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