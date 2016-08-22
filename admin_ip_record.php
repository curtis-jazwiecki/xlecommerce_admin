<?php
/*
  $Id: coupon_admin.php,v 1.1.2.24 2003/05/10 21:45:20 wilt Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

  require('includes/application_top.php');

?>

<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript">
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo $pInfo->products_date_available; ?>",scBTNMODE_CUSTOMBLUE);
</script>
<?php require('includes/account_check.js.php'); ?>
<div id="spiffycalendar" class="text"></div>        
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
<?php 
if(isset($_GET['userid']) && $_GET['userid'] != ''){
    $get_user_query = tep_db_query("SELECT `admin_groups_id`, `admin_firstname`, `admin_lastname`, `admin_email_address`, `admin_email_username` FROM `admin` WHERE `admin_id` = '".$_GET['userid']."'");
    $get_user = tep_db_fetch_array($get_user_query);
    $title = ' of '.$get_user['admin_firstname'].' '.$get_user['admin_lastname'];
}else{
    $title = '';
}
 
?>
      <td>         
        <table class="table table-bordered table-hover">
           <tr>
                <td>
                    <table class="table table-bordered table-hover">
                        <tr>
                          <td>
                              <table class="table table-bordered table-hover"> 
                                <tr>
                                    <td>Admin</td>
                                    <td>Ip Address</td>
                                    <td>Success</td>
                                    <td>Time</td>
                                </tr>
                                
                                <?php 
                                
                                $first_tid = '';
                                $count = 0;
                                if(isset($_GET['userid']) && $_GET['userid'] != ''){
                                    $get_records_query = tep_db_query("SELECT `user_name`, `identifier`, `success`, `date_added`, `user_id` FROM `action_recorder` WHERE `module` = 'ar_admin_login' and  `user_id` = '".$_GET['userid']."' order by date_added desc");
                                    
                                    //die("SELECT `user_name`, `identifier`, `success`, `date_added`, `user_id` FROM `action_recorder` WHERE `module` = 'ar_admin_login' and  `user_id` = '".$_GET['userid']."' order by date_added desc");
                                    
                                }else{
                                    $get_records_query = tep_db_query("SELECT `user_name`, `identifier`, `success`, `date_added`, `user_id` FROM `action_recorder` WHERE `module` = 'ar_admin_login' order by date_added desc");
                                }
                                while($get_records_array = tep_db_fetch_array($get_records_query)){
                                    ?>
                                  <tr id="defaultSelected" class="dataTableRowSelected" onMouseOver="rowOverEffect(this)" onMouseOut="rowOutEffect(this)" >
                                    <td><?php echo $get_records_array['user_name']; ?></td>
                                    <td><?php echo $get_records_array['identifier'];?></td>
                                    <td><?php echo ($get_records_array['success'] == '1'?'<img src="images/tick.gif" width="16" height="16">':'<img src="images/cross.gif" width="16" height="16">')?></td>
                                    <td><?php echo $get_records_array['date_added'];  ?></td>
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
    <td valign="top">
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
  </td>
  </tr>
</table>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>