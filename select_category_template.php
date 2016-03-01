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
	$new_template_selection = $_POST['category_listing_value'];
	$template_update_query = tep_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '".$new_template_selection."' where configuration_key = 'CATEGORY_LISTING_TEMPLATE'");
  }

// Get selected template
  $template_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'CATEGORY_LISTING_TEMPLATE'");
  $rows = tep_db_fetch_array($template_query);
  $selected_template = $rows['configuration_value'];
// End selected template

?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3>Select Category Listing Template
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading">Select Category Listing Template<br /><span>(Seen on categories page)</span>
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
    <td>
      <table class="table table-bordered table-hover">
        <tr>
          <td>
		    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="countries">
            <input type="hidden" name="update_template" value="update_template" />
            <table class="table table-bordered table-hover">
              <tr>
                <td>
                  <table class="table table-bordered table-hover">
                    <tr>
                	  <td>Preview Layout</td>
	                  <td align="center">Selected</td>
              		</tr>
<?php
	// Below displays options for template layout - OBN
?>
                    <tr>
                      <td><b>Standard Layout</b><br />4 columns with small pictures, name above picture. Results usually fit on one page.</td>
                      <td align="center"><input type="radio" name="category_listing_value" value="0" <?php if($selected_template == 0) echo "checked"; ?> /></td>
                    </tr>
                    <tr>
                      <td><b>Larger Picture Layout - 3 Column</b><br />3 colums with larger picture, name below pictures. Takes up more space than the standard layout.</td>
                      <td align="center"><input type="radio" name="category_listing_value" value="1" <?php if($selected_template == 1) echo "checked"; ?> /></td>
                    </tr>
                    <tr>
                      <td><b>Larger Picture Layout - 2 Column</b><br />2 columns, displays larger pictures. Takes up more space than the Medium Picture Layout.</td>
                      <td align="center"><input type="radio" name="category_listing_value" value="2" <?php if($selected_template == 2) echo "checked"; ?> /></td>
                    </tr>
                    <tr>
                      <td><b>List Layout</b><br />One Column.</td>
                      <td align="center"><input type="radio" name="category_listing_value" value="3" <?php if($selected_template == 3) echo "checked"; ?> /></td>
                    </tr>
                    <tr>
                      <td><b>Large List Layout</b><br />One Column, with larger pictures.</td>
                      <td align="center"><input type="radio" name="category_listing_value" value="4" <?php if($selected_template == 4) echo "checked"; ?> /></td>
                    </tr>
                    <tr>
                      <td><b>List Layout</b><br />One Column, no pictures, just a list of the categories</td>
                      <td align="center"><input type="radio" name="category_listing_value" value="5" <?php if($selected_template == 5) echo "checked"; ?> /></td>
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
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>