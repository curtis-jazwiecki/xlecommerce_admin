<?php
/*
  $Id: module_boxes.php,v 1.55 2003/06/29 22:50:52 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  if(!isset($_GET['type']) || !in_array($_GET['type'],array('m','b'))){
      $_GET['type'] = 'b';
  }

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
		
      case 'insert':
      case 'save':
        if (isset($HTTP_GET_VARS['mbID'])) $module_boxes_id = tep_db_prepare_input($HTTP_GET_VARS['mbID']);
        $module_boxes_name = tep_db_prepare_input($HTTP_POST_VARS['module_boxes_name']);
        $layout_ids = '';
        //print_r($_POST);
        //exit();
        if(is_array($_POST['file_group_ids'])){
            foreach($_POST['file_group_ids'] as $val){
                $layout_ids .= $val.',';
            }
            $layout_ids = rtrim($layout_ids, ',');
        }
        $sql_data_array = array('module_boxes_name' => $module_boxes_name,
                                'type' => $_GET['type'],
                                 'file_group_ids' => $layout_ids);
        

        if ($action == 'insert') {
          $sql_data_array['module_boxes_file'] = $HTTP_POST_VARS['file'];
          tep_db_perform(TABLE_MODULE_BOXES, $sql_data_array);
          $module_boxes_id = tep_db_insert_id();
        } elseif ($action == 'save') {
          
          tep_db_perform(TABLE_MODULE_BOXES, $sql_data_array, 'update', "module_boxes_id = '" . (int)$module_boxes_id . "'");
        }


        if (USE_CACHE == 'true') {
          tep_reset_cache_block('module_boxes');
        }

        tep_redirect(tep_href_link(FILENAME_MODULE_BOXES, 'type='.$_GET['type'].'&'.(isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'mbID=' . $module_boxes_id));
        break;
      case 'deleteconfirm':
        $module_boxes_id = tep_db_prepare_input($HTTP_GET_VARS['mbID']);

        tep_db_query("delete from " . TABLE_MODULE_BOXES . " where module_boxes_id = '" . (int)$module_boxes_id . "'");
        tep_db_query("delete from " . TABLE_MODULE_BOXES_LAYOUT . " where module_boxes_id = '" . (int)$module_boxes_id . "'");

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('module_boxes');
        }

        tep_redirect(tep_href_link(FILENAME_MODULE_BOXES, 'type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page']));
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
            <h3><?php echo (isset($_GET['type']) && $_GET['type'] == 'm' ? 'Modules' : 'Boxes'); ?>
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading"><?php echo (isset($_GET['type']) && $_GET['type'] == 'm' ? 'Modules' : 'Boxes'); ?>
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
            <td><table class="table table-bordered table-hover">
              <tr>
                <td>Title</td>
                <td align="center">Layout Groups</td>
                <td align="center">File</td>
                <td align="right"><?php echo 'action'; ?>&nbsp;</td>
              </tr>
<?php
  $layout_groups = array();
  $layout_group_query = tep_db_query("SELECT layout_groups_id, layout_groups_name FROM `layout_groups`");
  while($layout_group_array = tep_db_fetch_array($layout_group_query)){
      $layout_groups[$layout_group_array['layout_groups_id']] = $layout_group_array['layout_groups_name'];
      $file_group_pull_down_array[] = array('id'=>$layout_group_array['layout_groups_id'],'text'=>$layout_group_array['layout_groups_name']);
  }
$already_assign_files = array();
  $module_boxes_query = tep_db_query("SELECT `module_boxes_id`, `module_boxes_name`, `type`, `file_group_ids`, `module_boxes_file` FROM `module_boxes` WHERE `type` = '".$_GET['type']."'");

  
  while ($module_boxes = tep_db_fetch_array($module_boxes_query)) {
    $already_assign_files[] = $module_boxes['module_boxes_file']; 
    if ((!isset($HTTP_GET_VARS['mbID']) || (isset($HTTP_GET_VARS['mbID']) && ($HTTP_GET_VARS['mbID'] == $module_boxes['module_boxes_id']))) && !isset($mbInfo) && (substr($action, 0, 3) != 'new')) {
      $mbInfo = new objectInfo($module_boxes);
    }

    if (isset($mbInfo) && is_object($mbInfo) && ($module_boxes['module_boxes_id'] == $mbInfo->module_boxes_id)) {
      echo '              <tr id="defaultSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MODULE_BOXES, 'page=' . $HTTP_GET_VARS['page'] . '&type='.$_GET['type'].'&mbID=' . $module_boxes['module_boxes_id'] . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MODULE_BOXES, 'page=' . $HTTP_GET_VARS['page'] . '&type='.$_GET['type'].'&mbID=' . $module_boxes['module_boxes_id']) . '\'">' . "\n";
    }
?>
                <td><?php echo $module_boxes['module_boxes_name']; ?></td>
                <td align="center">
                    <?php
                    if (!empty($module_boxes['file_group_ids'])){
                        $group_id = explode(',', $module_boxes['file_group_ids']);
                        foreach($group_id as $gid){
                            echo $layout_groups[$gid].'<br>';
                        }
                    }
                    ?>
                </td>
                <td align="center"><?php echo $module_boxes['module_boxes_file']; ?></td>
                <td align="right"><?php if (isset($mbInfo) && is_object($mbInfo) && ($module_boxes['module_boxes_id'] == $mbInfo->module_boxes_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_MODULE_BOXES, 'type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mbID=' . $module_boxes['module_boxes_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              
<?php
  if (empty($action)) {
?>
              <tr>
                <td align="right" colspan="4"><?php echo '<a href="' . tep_href_link(FILENAME_MODULE_BOXES, 'type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mbID=' . $mbInfo->module_boxes_id . '&action=new') . '">' . tep_image_button('button_insert_b.gif', IMAGE_INSERT) . '</a>'; ?></td>
              </tr>
<?php
  }
?>
            </table></td>
<?php
  $heading = array();
  $contents = array();

    $files = array();
    $file_array = array();
    $dir = DIR_FS_CATALOG;
    if($_GET['type'] == 'm'){
        $dir .= DIR_WS_MODULES;
        $file_array = scandir($dir);
    }else{
        $dir .= DIR_WS_BOXES;
        $file_array = scandir($dir);
    }
    foreach ($file_array as $file){
        if(in_array($file, $already_assign_files)){
            continue;
        }
        $file_prop = explode('.', $file);
        if(strtolower($file_prop[1]) != 'php'){
            continue;
        }
        $files[] = array('id'=>$file, 'text' => $file);
    }
  
  switch ($action) {
    case 'new':
      $heading[] = array('text' => '<b>Module/boxes</b>');
      $file_group_str = '';
      foreach ($layout_groups as $key => $val){
          $file_group_str .= '<input type="checkbox" name="file_group_ids[]" value="'.$key.'" /> &nbsp;'.$val.'<br/>';
      }
      
      $contents = array('form' => tep_draw_form('module_boxes', FILENAME_MODULE_BOXES, 'type='.$_GET['type'].'&action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => '');
      $contents[] = array('text' => '<br><b>Title</b><br>' . tep_draw_input_field('module_boxes_name'));
      $contents[] = array('text' => '<br><b>Please select File:</b> <br>' . tep_draw_pull_down_menu('file',$files));
      //$contents[] = array('text' => '<br>Please select Layout group <br>' . tep_draw_multiselect_pull_down_menu('file_group_ids[]',$file_group_pull_down_array));
      $contents[] = array('text' => '<br><b>Please select Layout group:</b> <br><br>' . $file_group_str);

      
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_MODULE_BOXES, 'type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mbID=' . $HTTP_GET_VARS['mbID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'edit':
      $heading[] = array('text' => '<b>Module/Boxes Edit</b>');
        
      $file_group_str = '';
      $select_groups = explode(',',$mbInfo->file_group_ids);
      foreach ($layout_groups as $key => $val){
          if(in_array($key,$select_groups)){
            $file_group_str .= '<input type="checkbox" checked name="file_group_ids[]" value="'.$key.'" /> &nbsp;'.$val.'<br/>';
          }else{
            $file_group_str .= '<input type="checkbox" name="file_group_ids[]" value="'.$key.'" /> &nbsp;'.$val.'<br/>';
          }
      }
      $contents = array('form' => tep_draw_form('module_boxes', FILENAME_MODULE_BOXES, 'type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mbID=' . $mbInfo->module_boxes_id . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => '');
      
      
      $contents[] = array('text' => '<br><b>Title</b><br>' . tep_draw_input_field('module_boxes_name',$mbInfo->module_boxes_name));
      //$contents[] = array('text' => '<br>Please select Layout group <br>' . tep_draw_multiselect_pull_down_menu('file_group_ids[]',$file_group_pull_down_array, explode(',',$mbInfo->file_group_ids)));
      $contents[] = array('text' => '<br><b>Please select Layout group:</b> <br><br>' . $file_group_str);
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_MODULE_BOXES, 'type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mbID=' . $mbInfo->module_boxes_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>Module/Boxes Delete</b>');

      $contents = array('form' => tep_draw_form('module_boxes', FILENAME_MODULE_BOXES, 'type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mbID=' . $mbInfo->module_boxes_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $mbInfo->module_boxes_name . '</b>');
      

      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_MODULE_BOXES, 'type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mbID=' . $mbInfo->module_boxes_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (isset($mbInfo) && is_object($mbInfo)) {
        $heading[] = array('text' => '<b>' . $mbInfo->module_boxes_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_MODULE_BOXES, 'type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mbID=' . $mbInfo->module_boxes_id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_MODULE_BOXES, 'type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mbID=' . $mbInfo->module_boxes_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
        
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
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