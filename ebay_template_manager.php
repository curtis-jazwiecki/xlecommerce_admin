<?php require ('includes/application_top.php'); ?>

<?php

	if( (isset($_GET['action'])) && ($_GET['action'] == 'save_template') ){
		
		file_put_contents("ebay_template.html", $_POST['ebay_template_content']);
		
		$messageStack->add_session('Ebay Template Updated Successfully...', 'success');  
		
	}

?>


<script language="javascript" src="includes/general.js"></script>
<?php include(DIR_WS_LANGUAGES.$language.'/'.FILENAME_EBAY_TEMPLATE_MANAGER) ?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- body //-->
<section>
<!-- START Page content-->
<section class="main-content">
<h3><?php echo HEADING_TITLE_MANAGE_EBAY_TEMPLATE; ?></h3>
<!-- START panel-->
<div class="panel panel-default">
<!-- START table-responsive-->
<div class="table-responsive">
<!-- START your table-->
<table class="table table-bordered table-hover">
  <tr> 
    <!-- body_text //-->
    <td>
    	<?php
		echo tep_draw_form('ebay_template_frm', FILENAME_EBAY_TEMPLATE_MANAGER,'action=save_template', 'post', ' enctype="multipart/form-data" '); ?>
        	<table class="table table-bordered table-hover">
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        
        <tr>
          <td><table class="table table-bordered table-hover">
              <tr>
                <td> <?php echo HEADING_EBAY_TEMPLATE; ?> </td>
                <td> <?php 
				
						$get_file_content = file_get_contents("ebay_template.html");
						
						echo tep_draw_textarea_field('ebay_template_content', '', '119', '40', $get_file_content, 'class="ckeditor" id="editor1" '); 
					 ?>
                </td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td align="right" colspan="2"><?php echo tep_image_submit('button_update_b.gif', IMAGE_SAVE);?></td>
              </tr>
            </table></td>
        </tr>
      </table>
        </form>
    </td>
  </tr>
</table>
<!-- END your table--> 
<!-- body_eof //-->
<script src="https://cdn.ckeditor.com/4.5.9/standard-all/ckeditor.js"></script>
<script>
	CKEDITOR.replace( 'editor1', {
			fullPage: true,
			extraPlugins: 'docprops',
			// Disable content filtering because if you use full page mode, you probably
			// want to  freely enter any HTML content in source mode without any limitations.
			allowedContent: true,
			height: 450
		} );

</script>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>