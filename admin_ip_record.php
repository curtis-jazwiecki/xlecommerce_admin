<?php
/*
  $Id: coupon_admin.php,v 1.1.2.24 2003/05/10 21:45:20 wilt Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript">
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo $pInfo->products_date_available; ?>",scBTNMODE_CUSTOMBLUE);
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table width="780" border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
<!-- body_text //-->
<?php 
if(isset($_GET['userid']) && $_GET['userid'] != ''){
    $get_user_query = tep_db_query("SELECT `admin_groups_id`, `admin_firstname`, `admin_lastname`, `admin_email_address`, `admin_email_username` FROM `admin` WHERE `admin_id` = '".$_GET['userid']."'");
    $get_user = tep_db_fetch_array($get_user_query);
    $title = ' of '.$get_user['admin_firstname'].' '.$get_user['admin_lastname'];
}else{
    $title = '';
}
 
?>
      <td width="100%" valign="top">         
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
                <td>
                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                          <td class="pageHeading"><?php echo HEADING_TITLE . $title ; ?></td>
                          <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="75%" valign="top">
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        
                        
                        <tr>
                          <td>
                              <table border="0" width="100%" cellspacing="0" cellpadding="2" >
                                <tr class="dataTableHeadingRow">
                                    <td class="dataTableHeadingContent" width="30%">Admin</td>
                                    <td class="dataTableHeadingContent" width="25%">Ip Address</td>
                                    <td class="dataTableHeadingContent" width="20%">Success</td>
                                    <td class="dataTableHeadingContent" width="25%">Time</td>
                                </tr>
                                
                                <?php 
                                
                                $first_tid = '';
                                $count = 0;
                                if(isset($_GET['userid']) && $_GET['userid'] != ''){
                                    $get_records_query = tep_db_query("SELECT `user_name`, `identifier`, `success`, `date_added`, `user_id` FROM `action_recorder` WHERE `module` = 'ar_admin_login' and  `user_id` = '".$_GET['userid']."' order by date_added desc");
                                }else{
                                    $get_records_query = tep_db_query("SELECT `user_name`, `identifier`, `success`, `date_added`, `user_id` FROM `action_recorder` WHERE `module` = 'ar_admin_login' order by date_added desc");
                                }
                                while($get_records_array = tep_db_fetch_array($get_records_query)){
                                    ?>
                                  <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" >
                                    <td class="dataTableContent"><?php echo $get_records_array['user_name']; ?></td>
                                    <td class="dataTableContent"><?php echo $get_records_array['identifier'];?></td>
                                    <td class="dataTableContent"><?php echo ($get_records_array['success'] == '1'?'<img src="images/tick.gif" width="16" height="16">':'<img src="images/cross.gif" width="16" height="16">')?></td>
                                    <td class="dataTableContent"><?php echo $get_records_array['date_added'];  ?></td>
                                  </tr>
                  <?php
                                }
                                ?>
                              </table>
                          
                          </td>
                        </tr>
                    </table>
                </td>

<?php
  
    $heading = array();
    $contents = array();

      $get_admin_query = tep_db_query("SELECT `admin_id`, `admin_firstname`, `admin_lastname` FROM `admin`");
      $admin_str = '<option onclick="location.href=\''.tep_href_link(FILENAME_ADMIN_IP_RECORD).'\'" >All Admins</option>';
      while($get_admin_array = tep_db_fetch_array($get_admin_query)){
          if(isset($_GET['userid']) && $_GET['userid'] != ''){
              if($_GET['userid'] == $get_admin_array['admin_id']){
                  $admin_str .= '<option SELECTED >'.$get_admin_array['admin_firstname'].' '.$get_admin_array['admin_lastname'].'</option>';
              }else{
                  $admin_str .= '<option onclick="location.href=\''.tep_href_link(FILENAME_ADMIN_IP_RECORD,'userid='.$get_admin_array['admin_id']).'\'">'.$get_admin_array['admin_firstname'].' '.$get_admin_array['admin_lastname'].'</option>';
              }
          }else{ 
              $admin_str .= '<option onclick="location.href=\''.tep_href_link(FILENAME_ADMIN_IP_RECORD,'userid='.$get_admin_array['admin_id']).'\'">'.$get_admin_array['admin_firstname'].' '.$get_admin_array['admin_lastname'].'</option>';
          }
          
      }
      $heading[] = array('text'=>'Select an Admin');
      
      $contents[] = array('text'=>'Admin:' . '<br><select>'.$admin_str.'</select>');
    
         
?>                       
    <td width="25%" valign="top">
<?php
      $box = new box;
      echo $box->infoBox($heading, $contents);
    echo '            </td>' . "\n";
  
    
?>
      </tr>
    </table></td>
    <?php 
  
    ?>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>