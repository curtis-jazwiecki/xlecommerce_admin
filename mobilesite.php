<?php
/*
  $Id: mail.php,v 1.31 2003/06/20 00:37:51 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');
  //print_r($_POST);
if(is_uploaded_file($_FILES['mbanner']['tmp_name']))  
{
	$tmp_name = $_FILES["mbanner"]["tmp_name"];
	$name = $_FILES["mbanner"]["name"];
	if(move_uploaded_file($tmp_name, DIR_FS_CATALOG.DIR_WS_IMAGES.'mobilebanner/'.$name)) 
	{
		$updateQry = tep_db_query("UPDATE `".TABLE_CONFIGURATION."` SET `configuration_value` = '".$name."' WHERE `configuration_id` = '744'");
	}	
}
  
if($_POST['mstat'])
{
	if($_POST['mstat']=='t') $nval = 'True';
	if($_POST['mstat']=='f') $nval = 'False';
	//echo'<h1>123213</h1>';
	
	$updateQry = tep_db_query("UPDATE `".TABLE_CONFIGURATION."` SET `configuration_value` = '".$nval."' WHERE `configuration_id` = '743'");
}
$currentVal = tep_db_query("SELECT `configuration_value` FROM `".TABLE_CONFIGURATION."` WHERE `configuration_id` = '743' LIMIT 0 , 1");
$currentValue = tep_db_fetch_array($currentVal);	

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table width="780" border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">Mobile Site</td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">

          <tr><form action="<?php echo HTTP_SERVER.$_SERVER['PHP_SELF'];?>" method="post">
            <td><table border="0" cellpadding="0" cellspacing="2">
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main" style="color:#FFFFFF">Mobile Site Status</td>
                <td style="color:#fff;"><input type="radio" value="t" name="mstat" <?php if($currentValue['configuration_value']=='True') echo'checked="checked"';?>> ON <input type="radio" value="f" name="mstat" <?php if($currentValue['configuration_value']=='False') echo'checked="checked"';?>> OFF </td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right"><?php echo tep_image_submit('button_update.gif'); ?></td>
              </tr>
            </table></td>
          </form></tr>

<!-- body_text_eof //-->
        </table></td>
      </tr>
	   <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">

          <tr><form action="<?php echo HTTP_SERVER.$_SERVER['PHP_SELF'];?>" method="post"  enctype="multipart/form-data">
            <td><table border="0" cellpadding="0" cellspacing="2">
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main" style="color:#FFFFFF">Mobile Site Banner</td>
                <td style="color:#fff;"><input type="file" name="mbanner">&nbsp;<?php echo tep_image_submit('button_upload.gif'); ?></td>
              </tr>
            </table></td>
          </form></tr>

<!-- body_text_eof //-->
        </table></td>
      </tr>
    </table></td> 
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
