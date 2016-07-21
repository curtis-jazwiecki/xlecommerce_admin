<?php
/*
  $Id: import_avatax_codes.php,v 1.55 2016/06/01 22:50:52 hpdl Exp $
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

  require('includes/application_top.php');

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'insert':
      
		if(is_uploaded_file($_FILES['avatax_code_file']['tmp_name'])){
			
			$filename = "export_files/".$_FILES['avatax_code_file']['name'];
			$headers = array();
			$csv_data_array = array();
			move_uploaded_file($_FILES['avatax_code_file']['tmp_name'],$filename);
			$start = true;
			if (($handle = fopen($filename, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					if($start){
						$headers = $data;
						$start = false;
						continue;
					}
					$csv_data_array[] = array_combine($headers, $data);
					
				}
				fclose($handle);
				
				foreach($csv_data_array as $csv_data){
					
					tep_db_query("INSERT INTO ".AVATAX_CODE_LIST." (`tax_code`, `tax_code_description`, `tax_code_type`, `tax_code_category`) VALUES ('".tep_db_input($csv_data['Tax Code'])."', '".tep_db_input($csv_data['Description'])."', '".tep_db_input($csv_data['Type'])."', '".tep_db_input($csv_data['Category'])."') ON DUPLICATE KEY UPDATE tax_code_description = '".tep_db_input($csv_data['Description'])."',tax_code_type = '".tep_db_input($csv_data['Type'])."',tax_code_category = '".tep_db_input($csv_data['Category'])."'");
					
				}
				
				tep_redirect(tep_href_link(FILENAME_AVATAX_CODE_LIST, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] : '')));
			}
		}
	  break;
    }
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
    
      </tr>
      <tr>
        <td><table class="table table-bordered table-hover">
          <tr>
            <td>
              <tr>
                <th><?php echo TABLE_HEADING_AVATAX_CODES; ?></th>
				<th align="left"><?php echo TABLE_TAX_DESCRIPTION; ?></th>
                <th align="left"><?php echo TABLE_TAX_TYPE; ?></th>
                <th align="left"><?php echo TABLE_TAX_CATEGORY; ?></th>
              </tr>
<?php
//BOC HTC
  $tax_code_query_raw = "select * from " . AVATAX_CODE_LIST . " order by tax_code desc";
//EOC HTC
  $tax_code_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $tax_code_query_raw, $tax_code_query_numrows);
  $tax_code_query = tep_db_query($tax_code_query_raw);
  while ($tax_code = tep_db_fetch_array($tax_code_query)) {
    if ((!isset($HTTP_GET_VARS['mID']) || (isset($HTTP_GET_VARS['mID']) && ($HTTP_GET_VARS['mID'] == $tax_code['tax_code']))) && !isset($mInfo) && (substr($action, 0, 3) != 'new')) {
      
      $mInfo_array = $tax_code;
      $mInfo = new objectInfo($mInfo_array);
    }

    if (isset($mInfo) && is_object($mInfo) && ($tax_code['tax_code'] == $mInfo->tax_code)) {
      echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_AVATAX_CODE_LIST, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $tax_code['tax_code'] . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_AVATAX_CODE_LIST, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $tax_code['tax_code']) . '\'">' . "\n";
    }
?>
                <td><?php echo $tax_code['tax_code']; ?></td>
                <td><?php echo $tax_code['tax_code_description']; ?></td>
                <td><?php echo $tax_code['tax_code_type']; ?></td>
                <td><?php echo $tax_code['tax_code_category']; ?></td>
                
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="4"><table class="table table-bordered table-hover">
                  <tr>
                    <td><?php echo $tax_code_split->display_count($tax_code_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_AVATAX_CODES); ?></td>
                    <td align="right"><?php echo $tax_code_split->display_links($tax_code_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
<?php
  if (empty($action)) {
?>
              <tr>
                <td align="right" colspan="4"><?php echo '<a href="' . tep_href_link(FILENAME_AVATAX_CODE_LIST, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->tax_code . '&action=new') . '">' . tep_image_button('button_insert_b.gif', IMAGE_INSERT) . '</a>'; ?></td>
              </tr>
<?php
  }
?>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_AVATAX_CODE . '</b>');

      $contents = array('form' => tep_draw_form('manufacturers', FILENAME_AVATAX_CODE_LIST, 'action=insert'.(isset($HTTP_GET_VARS['page']) ? '&page=' . $HTTP_GET_VARS['page'] : ''), 'post', 'enctype="multipart/form-data"'));
      
	  $contents[] = array('text' => '<br>' . TEXT_AVATAX_CODE_FILE . '<br>' . tep_draw_file_field('avatax_code_file'));
      
	  $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_AVATAX_CODE_LIST, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $HTTP_GET_VARS['mID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      
	  break;
    
    default:

      if (isset($mInfo) && is_object($mInfo)) {
        $heading[] = array('text' => '<b>' . $mInfo->tax_code . '</b>');

        $contents[] = array('text' => '<br><b>' . TEXT_TAX_CODE . '</b> ' . $mInfo->tax_code);
        $contents[] = array('text' => '<br><b>' . TEXT_TAX_DESCRIPTION . '</b> ' . $mInfo->tax_code_description);
		
		$contents[] = array('text' => '<br><b>' . TEXT_TAX_TYPE . '</b> ' . $mInfo->tax_code_type);
        $contents[] = array('text' => '<br><b>' . TEXT_TAX_CATEGORY . '</b> ' . $mInfo->tax_code_category);
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<td width="25%" valign="top">' . "\n";
    $box = new box;
    echo $box->infoBox($heading, $contents);
    echo '</td>' . "\n";
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
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>