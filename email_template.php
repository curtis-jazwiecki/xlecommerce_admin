<?php
/*
  $Id: coupon_admin.php,v 1.1.2.24 2003/05/10 21:45:20 wilt Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  if(isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['tid']) && !empty($_GET['tid']) ){
      $email_templates_content = tep_db_prepare_input($HTTP_POST_VARS['email_templates_content']);
      tep_db_query("update email_templates set email_templates_content = '".$email_templates_content."' where email_templates_id = '".$_GET['tid']."'");
      tep_redirect(tep_href_link('email_template.php',tep_get_all_get_params(array('action'))));
  }

?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<div id="spiffycalendar" class="text"></div>
<script language="javascript" type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
  mode : "textareas",
  editor_selector : "mceEditor",
  theme : "advanced",
  plugins : "table,advhr,advimage,advlink,emotions,preview,flash,print,contextmenu",
theme_advanced_buttons1_add : "fontselect,fontsizeselect",
theme_advanced_buttons2_add : "separator,preview,separator,forecolor,backcolor",
theme_advanced_buttons2_add_before: "cut,copy,paste,separator",
theme_advanced_buttons3_add_before : "tablecontrols,separator",
theme_advanced_buttons3_add : "emotions,flash,advhr,separator,print",
theme_advanced_toolbar_location : "top",
theme_advanced_toolbar_align : "left",
theme_advanced_path_location : "bottom",
extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
external_link_list_url : "example_data/example_link_list.js",
external_image_list_url : "example_data/example_image_list.js",
flash_external_list_url : "example_data/example_flash_list.js"
});
</script>
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript">
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo $pInfo->products_date_available; ?>",scBTNMODE_CUSTOMBLUE);
</script>
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
  if(isset($_GET['action']) && $_GET['action'] == 'edit'){
      
      $template_query = tep_db_query("SELECT `email_templates_name`,`email_templates_content`,`email_templates_variables` FROM `email_templates` WHERE `email_templates_id` = '".$_GET['tid']."'");
      if(tep_db_num_rows($template_query)){
          $template_array = tep_db_fetch_array($template_query);
          $template_vars = explode(',', $template_array['email_templates_variables']);
          if(is_array($template_vars) && !empty($template_vars)){
              $template_vars_str = implode(' <br/> ', $template_vars);
          }else{
              $template_vars_str = $template_array['email_templates_variables'];
          }
?>
            <td>
               <table class="table table-bordered table-hover">
                    <tr>
                      <td><?php echo $template_array['email_templates_name']; ?></td>
                      <td align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                    </tr>
                  </table>
                <form name="template" method="post" action="email_template.php?action=update&tid=<?php echo $_GET['tid'];?>">
                <table class="table table-bordered table-hover">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                    </tr>
                    <tr>
                        <td>
                           <table class="table table-bordered table-hover">
                                <tr>
                                  <td><b>Please use place holder from below list to add corresponding variables:</b></td>
                                  <td><?php echo $template_vars_str; ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                    </tr>
                    <tr>
                        <td>
                           <table class="table table-bordered table-hover">
                                <tr>
                                  <td align="left"><b>Email/Page Content:</b></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                           <table class="table table-bordered table-hover">
                                <tr>
                                  <td align="center" ><?php echo tep_draw_textarea_field('email_templates_content', 'soft', '110', '30',(!empty($template_array['email_templates_content']) ? stripslashes($template_array['email_templates_content']) : ''),'class="mceEditor"'); ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                    </tr>
                    <tr>
                        <td>
                           <table class="table table-bordered table-hover">
                                <tr>
                                  <td align="left"><a href="<?php echo tep_href_link('email_template.php',tep_get_all_get_params(array('action')));?>"><input type="button" value="Back"></a></td>
                                  <td align="right"><input type="submit" value="Save" name="Save"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                    </tr>
                    
                </table>
                </form>
            </td>
<?php
      }else{
          tep_redirect(tep_href_link('email_template.php',tep_get_all_get_params(array('tid','action'))));
      }
  }else{
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
                                    <td>Email Templates</td>
                                    <td align="right">Action</td>
                                </tr>
                                
                                <?php 
                                $get_template_query = tep_db_query("SELECT `email_templates_id`, `email_templates_name` FROM `email_templates`");
                                $first_tid = '';
                                $count = 0;
                                while($get_template_array = tep_db_fetch_array($get_template_query)){
                                    if($count == 0){
                                        if(!isset($_GET['tid']) || empty($_GET['tid']) ){
                                            $_GET['tid'] = $get_template_array['email_templates_id'];
                                        } 
                                    }
                                    $count++;
                                    
                                    if (isset($_GET['tid']) &&   $_GET['tid'] == $get_template_array['email_templates_id'] ) {
                                        echo '<tr id="defaultSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_EMAIL_TEMPLATE, 'tid=' . $get_template_array['email_templates_id'] ) . '\'">' . "\n";
                                    } else {
                                        echo '<tr onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_EMAIL_TEMPLATE, 'tid=' . $get_template_array['email_templates_id'] ) . '\'">' . "\n";
                                    }
?>
                                    <td><?php echo '<a href="' . tep_href_link(FILENAME_EMAIL_TEMPLATE, 'tid=' . $get_template_array['email_templates_id'] . '&action=edit' ) . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $get_template_array['email_templates_name']; ?></td>
                                    <td align="right"><?php if (isset($_GET['tid']) &&   $_GET['tid'] == $get_template_array['email_templates_id'] ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_EMAIL_TEMPLATE, 'tid=' . $get_template_array['email_templates_id'] )  . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
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


      $heading[] = array('text'=>'Edit');
      
      $contents[] = array('text'=>'' . '<br><a href="'.tep_href_link(FILENAME_EMAIL_TEMPLATE, 'tid=' . $_GET['tid'] . '&action=edit' ).'">'.tep_image_button('button_edit.gif','Edit Template').'</a></center>');
    
         
?>                       
    <td>
<?php
      $box = new box;
      echo $box->infoBox($heading, $contents);
    echo '            </td>' . "\n";
  
    
?>
      </tr>
    </table></td>
    <?php 
  }
    ?>
<!-- body_text_eof //-->
  </tr>
</table>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>