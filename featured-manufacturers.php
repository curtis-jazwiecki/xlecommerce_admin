<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
  Featured Products admin
*/

  require('includes/application_top.php');
  
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  switch ($HTTP_GET_VARS['action']) {
    case 'insert':
      $mQuery = tep_db_query("SELECT configuration_value FROM `configuration` where configuration_id = 1215 ");
      $mlist=tep_db_fetch_array($mQuery);
	  if(!empty($mlist['configuration_value'])){
	  	$m_array = explode (",", $mlist['configuration_value']);
	  }
	  $m_array[] = $_POST['manufacturers'];
	  tep_db_query("UPDATE `configuration` SET `configuration_value` = '".implode(",", $m_array)."' where configuration_id = 1215 ");
      tep_redirect(tep_href_link(basename($PHP_SELF), 'page=' . $HTTP_GET_VARS['page'].'&featured_group='.$featured_group));
      break;
    
    case 'remove':
      $mQuery = tep_db_query("SELECT configuration_value FROM `configuration` where configuration_id = 1215 ");
      $mlist=tep_db_fetch_array($mQuery);
      $m_array = explode (",", $mlist['configuration_value']);
      if(($key = array_search($_GET['m'], $m_array)) !== false) {
    		 unset($m_array[$key]);
	  }
	  $m_array = array_filter(array_values($m_array)); // 'reindex' array and remove blank values
	  tep_db_query("UPDATE `configuration` SET `configuration_value` = '".implode(",", $m_array)."' where configuration_id = 1215 ");
      tep_redirect(tep_href_link(basename($PHP_SELF)));
      break;
  }
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //--> 
<!-- body //-->
<section>
<!-- START Page content-->
<section class="main-content">
<h3>Featured Manufacturers</h3>
<!-- START panel-->
<div class="panel panel-default">
<!-- START table-responsive-->

<div class="table-responsive">
<!-- START your table-->
<table class="table table-bordered table-hover">
    <tr>
  <!-- body_text //-->
  <td><table class="table table-bordered table-hover">
<?php
  if ( ($HTTP_GET_VARS['action'] == 'new') || ($HTTP_GET_VARS['action'] == 'edit') ) {
    $form_action = 'insert';
?>
        <tr>
      <form name="new_feature" <?php echo 'action="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action', 'info', 'm')) . 'action=' . $form_action, 'NONSSL') . '"'; ?> method="post">
        <td><br>
          <table class="table table-bordered table-hover">
            <tr>
              <td> Manufacturers &nbsp; </td>
              <td><select name="manufacturers">
                  <option value="0">Manufacturers</option>
                  <?php
                    $mquery = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");
                    while($mli = tep_db_fetch_array($mquery)){?>
	                  <option value="<?php echo $mli['manufacturers_id'];?>"><?php echo $mli['manufacturers_name'];?></option>
                  <?php   
                    }
                    ?>
                </select></td>
            </tr>
          </table></td>
          </tr>
        
        <tr>
          <td><table class="table table-bordered table-hover">
              <tr>
                <td align="right"><br>
                  <?php echo (($form_action == 'insert') ? tep_image_submit('button_insert_b.gif', IMAGE_INSERT) : tep_image_submit('button_update_b.gif', IMAGE_UPDATE)). '&nbsp;&nbsp;&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $HTTP_GET_VARS['sID']) . '">' . tep_image_button('button_cancel_b.gif', IMAGE_CANCEL) . '</a>'; ?></td>
              </tr>
            </table></td>
      </form>
        </tr>
      
      <?php
  } else {
?>
      <tr>
        <td><table class="table table-bordered table-hover">
            <tr>
              <td><table class="table table-bordered table-hover">
                  <tr>
                    <td>Manufacturers</td>
                    <td>&nbsp;</td>
                  </tr>
                  <?php
						///////////////////////////////////////
						$mQuery = tep_db_query("SELECT configuration_value FROM `configuration` where configuration_id = 1215 ");
						$mlist=tep_db_fetch_array($mQuery);
    
    
						///////////////////////////////////////
						if($mlist[configuration_value] != ''){
							$featured_query_raw = "SELECT `manufacturers_id`,`manufacturers_name` FROM `manufacturers` where manufacturers_id in (".$mlist[configuration_value].") ORDER BY manufacturers_name";
    
							$featured_query = tep_db_query($featured_query_raw);
							while ($featured = tep_db_fetch_array($featured_query)) {

						        echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" >';
							?>
                  
                    				<td><?php echo $featured['manufacturers_name']; ?></td>
                    				<td><input type="button" value="Remove" style="cursor: pointer;" onClick="removeManu('<?php echo $featured['manufacturers_id']; ?>')"></td>
                  				
                                </tr>
                  <?php
    						}
    					}?>
                  <tr>
                    <td colspan="4"><table class="table table-bordered table-hover">
                        <tr>
                          <td colspan="2" align="right">
						  	<?php echo '<a href="' . tep_href_link(basename($PHP_SELF), 'page=' . $HTTP_GET_VARS['page'] . '&action=new') . '"><input type="button" value="Add New Manufacturers"></a>'; ?>
                          </td>
                        </tr>
                      </table></td>
                  </tr>
                </table></td>
              <?php
}
?>
            </tr>
          </table></td>
      </tr>
    </table></td>
  <!-- body_text_eof //-->
    </tr>
</table>
<!-- END your table--> 
<!-- body_eof //--> 
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //--> 
<script type="text/javascript">
function removeManu(mid){
	if(confirm('Do you want to delete this manufacturers from list')){
		location.href='<?php echo $PHP_SELF?>?action=remove&m='+mid;
	}
}
</script>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>