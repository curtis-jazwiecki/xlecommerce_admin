<?php
/*
  $Id: layout_manager.php,v 1.55 2003/06/29 22:50:52 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

  require('includes/application_top.php');

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');
    if(!isset($_SESSION['fetch_files']) || $_SESSION['fetch_files'] == '' || $_GET['fetch_files'] == true){
        foreach (glob(DIR_FS_DOCUMENT_ROOT."*.php") as $filename) {
            $query1 = tep_db_query("select * from ".TABLE_LAYOUT_GROUPS_FILES." where layout_group_file like '".basename($filename)."'");
            if(!tep_db_num_rows($query1)){
                tep_db_query("insert into ".TABLE_LAYOUT_GROUPS_FILES." (layout_group_file, status) values ('".basename($filename)."','1')");
            }
                            //$root_file_names .= '<br/> <input type="checkbox" name="layout_groups_files[]" id="" value="'.basename($filename).'"> &nbsp;'.basename($filename).'<br/>';
        }
        $_SESSION['fetch_files'] = 'completed';
        tep_redirect( tep_href_link(FILENAME_LAYOUT_MANAGER, tep_get_all_get_params(array('fetch_files'))));
    }
  if (tep_not_null($action)) {
    switch ($action) {
      case 'setflag':
        $mID = $_GET['mID'];
        $flag = $_GET['flag'];
        tep_db_query("update layout set layout_status='" . (int)$flag . "' where layout_groups_id='" . (int)$mID . "'");
        tep_redirect( tep_href_link(FILENAME_LAYOUT_MANAGER, tep_get_all_get_params(array('action', 'flag')) ) );
        break;
    
      case 'insert':
      case 'save':
        
		$duplicate_files = array();
		if (isset($HTTP_GET_VARS['mID'])) $layout_groups_id = tep_db_prepare_input($HTTP_GET_VARS['mID']);
        $layout_groups_name = tep_db_prepare_input($HTTP_POST_VARS['layout_groups_name']);
        $sql_data_array = array('layout_groups_name' => $layout_groups_name);

        if ($action == 'insert') {
          tep_db_perform(TABLE_LAYOUT_GROUPS, $sql_data_array);
          $layout_groups_id = tep_db_insert_id();
        } elseif ($action == 'save') {
          tep_db_perform(TABLE_LAYOUT_GROUPS, $sql_data_array, 'update', "layout_groups_id = '" . (int)$layout_groups_id . "'");
        }
        // add layout files start
		if(count($_POST['layout_groups_files']) > 0){
                    foreach($_POST['layout_groups_files'] as $id => $files){				
                        tep_db_query("update ".TABLE_LAYOUT_GROUPS_FILES." set  layout_groups_id = '".$layout_groups_id."', status = '1' where layout_groups_files_id = '".$id."'");
                    }
		}	
		tep_redirect(tep_href_link(FILENAME_LAYOUT_MANAGER, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'mID=' . $layout_groups_id));
        break;
      case 'deleteconfirm':
        $layout_groups_id = tep_db_prepare_input($HTTP_GET_VARS['mID']);
        tep_db_query("delete from " . TABLE_LAYOUT_GROUPS . " where layout_groups_id = '".(int)$layout_groups_id."'");
        tep_db_query("update ".TABLE_LAYOUT_GROUPS_FILES." set  layout_groups_id = '0' where layout_groups_id = '".(int)$layout_groups_id."'");
        tep_redirect(tep_href_link(FILENAME_LAYOUT_MANAGER, 'page=' . $HTTP_GET_VARS['page']));
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
    <td><table class="table table-bordered table-hover">
      <tr>
        <td><table class="table table-bordered table-hover">
          <tr>
            <td><?php echo HEADING_TITLE; ?></td>
            <td align="right"><a href="<?php echo tep_href_link(FILENAME_LAYOUT_MANAGER, tep_get_all_get_params(array('fetch_files')).'&fetch_files=true')?>"><input type="button" name="fetch_new_files" value="Fetch New Files"></a></a></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table class="table table-bordered table-hover">
          <tr>
            <td><table class="table table-bordered table-hover">
              <tr>
                <td><?php echo TABLE_HEADING_LAYOUT_MANAGER; ?></td>
                <td align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
	          </tr>
<?php
    $all_layout_group_files_query = tep_db_query("SELECT `layout_groups_files_id`, `layout_group_file` FROM `layout_groups_files` WHERE `status` = '1' order by `sort_order`, layout_group_file ");
    while($all_layout_group_files = tep_db_fetch_array($all_layout_group_files_query)){
        $all_layout_group_files_array[$all_layout_group_files['layout_groups_files_id']] = $all_layout_group_files['layout_group_file'];
    }


//BOC HTC
  $layout_query_raw = "select * from " . TABLE_LAYOUT_GROUPS . " order by layout_groups_name";
//EOC HTC
  $layout_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $layout_query_raw, $layout_query_numrows);
  $layout_query = tep_db_query($layout_query_raw);
  while ($layout = tep_db_fetch_array($layout_query)) {
    if ((!isset($HTTP_GET_VARS['mID']) || (isset($HTTP_GET_VARS['mID']) && ($HTTP_GET_VARS['mID'] == $layout['layout_groups_id']))) && !isset($mInfo) && (substr($action, 0, 3) != 'new')) {
      $LAYOUT_products = array();
	  $mInfo_array = array_merge($layout, $LAYOUT_products);
      $mInfo = new objectInfo($mInfo_array);
    }

    if (isset($mInfo) && is_object($mInfo) && ($layout['layout_groups_id'] == $mInfo->layout_groups_id)) {
      echo '              <tr id="defaultSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_LAYOUT_MANAGER, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $layout['layout_groups_id'] . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_LAYOUT_MANAGER, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $layout['layout_groups_id']) . '\'">' . "\n";
    }
?>
                <td><?php echo $layout['layout_groups_name']; ?></td>
                
                <td align="right"><?php if (isset($mInfo) && is_object($mInfo) && ($layout['layout_groups_id'] == $mInfo->layout_groups_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_LAYOUT_MANAGER, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $layout['layout_groups_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table class="table table-bordered table-hover">
                  <tr>
                    <td><?php echo $layout_split->display_count($layout_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_LAYOUT); ?></td>
                    <td align="right"><?php echo $layout_split->display_links($layout_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
<?php
  if (empty($action)) {
?>
              <tr>
                <td align="right" colspan="2"><?php echo '<a href="' . tep_href_link(FILENAME_LAYOUT_MANAGER, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->layout_groups_id . '&action=new') . '">' . tep_image_button('button_insert_b.gif', IMAGE_INSERT) . '</a>'; ?></td>
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
      $heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_LAYOUT . '</b>');
      $contents = array('form' => tep_draw_form('layout', FILENAME_LAYOUT_MANAGER, 'action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_NEW_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_LAYOUT_NAME . '<br>' . tep_draw_input_field('layout_groups_name	'));
        foreach ($all_layout_group_files_array as $id => $filename) {
            $root_file_names .= '<br/> <input type="checkbox" name="layout_groups_files['.$id.']" id="" value="'.basename($filename).'"> &nbsp;'.basename($filename).'<br/>';
        }
	  $contents[] = array('text' => '<br>'.TEXT_LAYOUT_FILES.'<br>'.$root_file_names);
	  $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_LAYOUT_MANAGER, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $HTTP_GET_VARS['mID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_EDIT_LAYOUT . '</b>');
      $contents = array('form' => tep_draw_form('layout', FILENAME_LAYOUT_MANAGER, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->layout_groups_id . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_EDIT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_LAYOUT_NAME . '<br>' . tep_draw_input_field('layout_groups_name', $mInfo->layout_groups_name));
	  
	  $layout_files = array();
	  $layout_files_query = tep_db_query("select * from ".TABLE_LAYOUT_GROUPS_FILES." where layout_groups_id = '".$mInfo->layout_groups_id."'");
	  while($result = tep_db_fetch_array($layout_files_query)){
	  	$layout_files[] = $result['layout_group_file']; 	
	  }
	  
	  foreach ($all_layout_group_files_array as $id => $filename) {
            $chk = '';
            if(in_array(basename($filename),$layout_files)){
                    $chk = 'checked="checked"';
            }
            $root_file_names .= '<br/> <input type="checkbox" name="layout_groups_files['.$id.']" id="" value="'.basename($filename).'" '.$chk.'> &nbsp;'.basename($filename).'<br/>';
	  }
	  $contents[] = array('text' => '<br>'.TEXT_LAYOUT_FILES.'<br>'.$root_file_names);
	  
	  
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_LAYOUT_MANAGER, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->layout_groups_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_DELETE_LAYOUT . '</b>');
      $contents = array('form' => tep_draw_form('layout', FILENAME_LAYOUT_MANAGER, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->layout_groups_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>'.$mInfo->layout_groups_name.'</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_LAYOUT_MANAGER, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->layout_groups_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      
	  break;
    default:
      if (isset($mInfo) && is_object($mInfo)) {
        $heading[] = array('text' => '<b>' . $mInfo->layout_groups_name . '</b>');
        $contents[] = array(
			'align' => 'center', 
			'text' => '<a href="' . tep_href_link(FILENAME_LAYOUT_MANAGER, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->layout_groups_id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_LAYOUT_MANAGER, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->layout_groups_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>'
		);
		
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