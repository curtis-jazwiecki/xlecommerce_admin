<?php
/*
  $Id: mail.php,v 1.31 2003/06/20 00:37:51 hpdl Exp $

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
  */

  require('includes/application_top.php');
  
  $check_mobile_template_query = tep_db_query("select count(*) as total from " . TABLE_CONFIGURATION . " where configuration_key='MOBILE_TEMPLATE_FOLDER'");
  $result = tep_db_fetch_array($check_mobile_template_query);
  if ($result['total'] <= 0) {
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Mobile Template Folder', 'MOBILE_TEMPLATE_FOLDER', '', 'Set the template for mobile site', '99999', '0', '', '', now())");
  }
  

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

if (isset($_POST['mobile_template'])) {
    tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value='" . $_POST['mobile_template'] . "' where configuration_key='MOBILE_TEMPLATE_FOLDER'");
    $mobile_template=$_POST['mobile_template'];
} else {
    $mobile_template=MOBILE_TEMPLATE_FOLDER;
}
$currentVal = tep_db_query("SELECT `configuration_value` FROM `".TABLE_CONFIGURATION."` WHERE `configuration_id` = '743' LIMIT 0 , 1");
$currentValue = tep_db_fetch_array($currentVal);	

?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">

<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3>Mobile Site
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading">Mobile Site
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

          <tr><form action="<?php echo HTTP_SERVER.$_SERVER['PHP_SELF'];?>" method="post">
            <td><table class="table table-bordered table-hover">
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td>Mobile Site Status</td>
                <td><input type="radio" value="t" name="mstat" <?php if($currentValue['configuration_value']=='True') echo'checked="checked"';?>> ON <input type="radio" value="f" name="mstat" <?php if($currentValue['configuration_value']=='False') echo'checked="checked"';?>> OFF </td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
               <tr>
                <td>Mobile Site Template</td>
                <td>
                <?php
                $templates_folder = DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/full/';
                $default_dir = array('_notes', 'boxes', 'images', 'content');
                $templates_array[]= array('id'=>'',
                                        'text' => 'Please Select');
                if ($handle = opendir($templates_folder)) {
                    while (false !== ($entry = readdir($handle))) {
                     if (is_dir($templates_folder . $entry) && $entry != "." && $entry != ".." && !in_array($entry,$default_dir)) {
                       $templates_array[] = array('id'=>$entry,
                                                  'text' => $entry);
                     }
                  }
               } 
                echo tep_draw_pull_down_menu('mobile_template', $templates_array,$mobile_template)
                ?></td>
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
        <td><table class="table table-bordered table-hover">

          <tr><form action="<?php echo HTTP_SERVER.$_SERVER['PHP_SELF'];?>" method="post"  enctype="multipart/form-data">
            <td><table class="table table-bordered table-hover">
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td>Mobile Site Banner</td>
                <td><input type="file" name="mbanner">&nbsp;<?php echo tep_image_submit('button_upload.gif'); ?></td>
              </tr>
            </table></td>
          </form></tr>

<!-- body_text_eof //-->
        </table></td>
      </tr>
    </table></td> 
  </tr>
</table>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>