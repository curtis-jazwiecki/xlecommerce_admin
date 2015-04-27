<?php
/*
  
*/

  require('includes/application_top.php');
  if(!isset($_GET['type']) || !in_array($_GET['type'],array('m','b'))){
      $_GET['type'] = 'b';
  }
if(!isset($_GET['template_id']) || empty($_GET['template_id'])){
$template_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_STS_TEMPLATE_FOLDER'");
$rowz = tep_db_fetch_array($template_query);
//$selected_template = $rowz['configuration_value'];
$_GET['template_id'] = str_replace('full/','', $rowz['configuration_value']);
}
  
  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {	
      case 'setflag':
			$mblID = $_GET['mblID'];
                        $template_id=$_GET['template_id'];
                        $type = $_GET['type'];
			$flag = $_GET['flag'];
			tep_db_query("update module_boxes_layout set status='" . (int)$flag . "' where `module_boxes_layout_id`='" . (int)$mblID . "' and template_id = '".$template_id."' and type = '".$type."'");
			tep_redirect(tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page']));
			break;
      case 'insert':
      case 'save':
        if (isset($HTTP_GET_VARS['mblID'])) $module_boxes_layout_id = tep_db_prepare_input($HTTP_GET_VARS['mblID']);
        $module_boxes_layout_name = tep_db_prepare_input($HTTP_POST_VARS['title']);
        $layout_ids = '';
        if(is_array($_POST['position'])){
            foreach($_POST['position'] as $pos){
                $position .= $pos.',';
            }
            $position = rtrim($position,',');
        }
        $sql_data_array = array('title' => $module_boxes_layout_name,
                                'sort_order' => $_POST['sort_order'],
                                'status' => $_POST['status'],
                                'position' => $position,
                                'type' => $_GET['type'],
                                'template_id' => $_GET['template_id']);
        

        if ($action == 'insert') {
            $sql_data_array['module_boxes_id'] = $HTTP_POST_VARS['module_boxes_id'];
            $group_id_query = tep_db_query("SELECT `file_group_ids` FROM `module_boxes` WHERE `module_boxes_id` = '".$HTTP_POST_VARS['module_boxes_id']."'");
            $group_id = tep_db_fetch_array($group_id_query);
            $layout_file_id_query = tep_db_query("SELECT `layout_groups_files_id` FROM `layout_groups_files` WHERE `layout_groups_id` in (".$group_id['file_group_ids'].") and status = '1'");
            while($layout_file_id_array = tep_db_fetch_array($layout_file_id_query)){
                $layout_files_ids .= $layout_file_id_array['layout_groups_files_id'].',';
            }
            $layout_files_ids = rtrim($layout_files_ids, ',');
            $sql_data_array['layout_files'] = $layout_files_ids;
            tep_db_perform(TABLE_MODULE_BOXES_LAYOUT, $sql_data_array);
            $module_boxes_layout_id = tep_db_insert_id();
        } elseif ($action == 'save') {
            if(is_array($_POST['layout_files'])){
                foreach($_POST['layout_files'] as $val){
                    $layout_files_ids .= $val.',';
                }
                $layout_files_ids = rtrim($layout_files_ids, ',');
            }
            $sql_data_array['layout_files'] = $layout_files_ids;
          tep_db_perform(TABLE_MODULE_BOXES_LAYOUT, $sql_data_array, 'update', "module_boxes_layout_id = '" . (int)$module_boxes_layout_id . "'");
        }


        if (USE_CACHE == 'true') {
          tep_reset_cache_block('module_boxes_layout');
        }

        tep_redirect(tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&'.(isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'mblID=' . $module_boxes_layout_id));
        break;
      case 'deleteconfirm':
        $module_boxes_layout_id = tep_db_prepare_input($HTTP_GET_VARS['mblID']);

        tep_db_query("delete from " . TABLE_MODULE_BOXES_LAYOUT . " where module_boxes_layout_id = '" . (int)$module_boxes_layout_id . "'");

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('module_boxes_layout');
        }

        tep_redirect(tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page']));
        break;
    }
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table width="780" border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo (isset($_GET['type']) && $_GET['type'] == 'm' ? 'Modules' : 'Boxes'); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
          
          <?php 
          $templates = array();
            $dir = DIR_FS_CATALOG . 'includes/sts_templates/full/';
            if (is_dir($dir)){
                if ($dh = opendir($dir)){
                    while(($file = readdir($dh))!==false){
                        if (is_dir($dir . $file)){
                            if (strpos($file, 'template')!==false && is_numeric(substr($file, -1))){
                                $templates[] =  $file ;
                            }
                        }
                    }
                    closedir($dh);
                }
            }
            sort($templates);
            $template_array = array();
            foreach ($templates as $template){
                $template_array[] = array('id'=>$template,'text'=>$template);
            }
            //print_r($templates);
          ?>
          <td align="right" style="color:white;"><b>Select Template :</b> <?php echo tep_draw_pull_down_menu('template_id',$template_array,$_GET['template_id'],'onchange="location.href=\''.tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'type='.$_GET['type'].'&template_id=').'\'+this.value"');?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent">Title</td>
                <!--<td class="dataTableHeadingContent" align="center">Layout Position</td>-->
                <td class="dataTableHeadingContent" align="center">Status</td>
                <td class="dataTableHeadingContent" align="center">Sort Order</td>
                <td class="dataTableHeadingContent" align="right"><?php echo 'action'; ?>&nbsp;</td>
              </tr>
<?php

  $layout_files_array = array();
  $layout_files_query = tep_db_query("SELECT `layout_groups_files_id`, `layout_group_file`, `sort_order` FROM `layout_groups_files` WHERE `status` = '1' order by `sort_order`");
  while($layout_files = tep_db_fetch_array($layout_files_query)){
      
      $layout_files_array[$layout_files['layout_groups_files_id']] = $layout_files['layout_group_file'];
  }
  
  
  $layout_groups = array();
  $layout_group_files = array();
  $layout_group_query = tep_db_query("SELECT layout_groups_id, layout_groups_name FROM `layout_groups`");
  while($layout_group_array = tep_db_fetch_array($layout_group_query)){
      $layout_groups[$layout_group_array['layout_groups_id']] = $layout_group_array['layout_groups_name'];
      
      //layout group files 
      $layout_group_files_query = tep_db_query("SELECT layout_groups_files_id,`layout_group_file` FROM `layout_groups_files` WHERE `layout_groups_id` = '".$layout_group_array['layout_groups_id']."' and status = '1' order by `sort_order`");
      $file_group_pull_down_array[] = array('id'=>$layout_group_array['layout_groups_id'],'text'=>$layout_group_array['layout_groups_name'],'param' => 'disable');
      while($layout_group_files_array = tep_db_fetch_array($layout_group_files_query)){
          $file_group_pull_down_array[] = array('id'=>$layout_group_files_array['layout_group_file'],'text'=>'&nbsp;&nbsp;'.$layout_group_files_array['layout_group_file']);
          
          $layout_group_files[$layout_group_array['layout_groups_id']] [$layout_group_files_array['layout_groups_files_id']] = $layout_group_files_array['layout_group_file'];
      }
  }
  
  $already_assign_modules = array();
  
  $module_boxes_layout_query = tep_db_query("SELECT `module_boxes_layout_id`, `title`, `module_boxes_id`, `layout_files`, `status`, `sort_order`, `position`, `template_id`, `type` FROM `module_boxes_layout` WHERE `type` = '".$_GET['type']."' and template_id='".$_GET['template_id']."'");

  $position_array = array('l'=>'Left Column','r'=>'Right Column'); 
  while ($module_boxes_layout = tep_db_fetch_array($module_boxes_layout_query)) {
    $already_assign_modules[] = $module_boxes_layout['module_boxes_id']; 
    if ((!isset($HTTP_GET_VARS['mblID']) || (isset($HTTP_GET_VARS['mblID']) && ($HTTP_GET_VARS['mblID'] == $module_boxes_layout['module_boxes_layout_id']))) && !isset($mblInfo) && (substr($action, 0, 3) != 'new')) {
      $mblInfo = new objectInfo($module_boxes_layout);
    }

    if (isset($mblInfo) && is_object($mblInfo) && ($module_boxes_layout['module_boxes_layout_id'] == $mblInfo->module_boxes_layout_id)) {
      echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'page=' . $HTTP_GET_VARS['page'] . '&template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&mblID=' . $module_boxes_layout['module_boxes_layout_id'] . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'page=' . $HTTP_GET_VARS['page'] . '&template_id='.$_GET['template_id'].'&template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&mblID=' . $module_boxes_layout['module_boxes_layout_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $module_boxes_layout['title']; ?></td>
                
               <!-- <td class="dataTableContent" valign="top" align="center">
                    <?php
                   /* if (!empty($module_boxes_layout['position'])){
                        $positions = explode(',', $module_boxes_layout['position']);
                        foreach($positions as $position){
                            echo $position_array[$position].'<br>';
                        }
                    }*/
                    ?>
                </td>-->
                <td class="dataTableContent" align="center">
                    <?php
                        if ($module_boxes_layout['status'] == '1'){
                                echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&action=setflag&flag=0&mblID=' . $module_boxes_layout['module_boxes_layout_id'] . '&page=' . $_GET['page']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                        } else {
                                echo '<a href="' . tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&action=setflag&flag=1&mblID=' . $module_boxes_layout['module_boxes_layout_id'] . '&page=' . $_GET['page']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                        }
                    ?>
                </td>
                <td class="dataTableContent" align="center"><?php echo $module_boxes_layout['sort_order']; ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($mblInfo) && is_object($mblInfo) && ($module_boxes_layout['module_boxes_layout_id'] == $mblInfo->module_boxes_layout_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mblID=' . $module_boxes_layout['module_boxes_layout_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              
<?php
//echo 'hii'.$action;
  if (empty($action)) {
?>
              <tr>
                <td align="right" colspan="5" class="smallText"><?php echo '<a href="' . tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mblID=' . $mblInfo->module_boxes_layout_id . '&action=new') . '">' . tep_image_button('button_insert_b.gif', IMAGE_INSERT) . '</a>'; ?></td>
              </tr>
<?php
  }
?>
            </table></td>
<?php
  $heading = array();
  $contents = array();

    $module_boxes_array = array();
    
    
    $query_moules_boxes = tep_db_query("SELECT `module_boxes_id`,`module_boxes_name`,`file_group_ids` FROM `module_boxes` WHERE `type` = '".$_GET['type']."'");
    
    while ($moules_boxes = tep_db_fetch_array( $query_moules_boxes)){
        if(in_array($moules_boxes['module_boxes_id'], $already_assign_modules)){
            continue;
        }
        
        $module_boxes_array[] = array('id'=>$moules_boxes['module_boxes_id'], 'text' => $moules_boxes['module_boxes_name']);
    }
  
  $position_str = '';
  
  switch ($action) {
    case 'new':
      $heading[] = array('text' => '<b>Module/boxes Layout</b>');
      
      /*foreach($position_array as $pkey => $pval){
        $position_str .= '<input type="checkbox" name="position[]" value="'.$pkey.'"> '.$pval.' <br/>';
      }*/
	  $position_str = '<input type="hidden" name="position[]" value="l">';
        
      $contents = array('form' => tep_draw_form('module_boxes_layout', FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => '');
      $contents[] = array('text' => '<br>Title<br>' . tep_draw_input_field('title'));
      $contents[] = array('text' => '<br>Sort Order <br>' . tep_draw_input_field('sort_order'));
      $contents[] = array('text' => '&nbsp;' . $position_str);
      $contents[] = array('text' => '<br>Status <br><input type="radio" value="1" name="status"> Active   <input type="radio" value="0" name="status"> Inactive' );
      $contents[] = array('text' => '<br>Please select Module or Box <br>' . tep_draw_pull_down_menu('module_boxes_id',$module_boxes_array));
      //$contents[] = array('text' => '<br>Please select Layout group <br>' . tep_draw_multiselect_pull_down_menu('file_group_ids[]',$file_group_pull_down_array));

      
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mblID=' . $HTTP_GET_VARS['mblID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'edit':
      //print_r($layout_group_files);
        //echo $mblInfo->layout_files;
      $heading[] = array('text' => '<b>'.$mblInfo->title.' Edit</b>');
      $layout_files = explode(',',$mblInfo->layout_files);
      
      $module_boxes_group_ids_query = tep_db_query("SELECT `file_group_ids` FROM `module_boxes` WHERE `module_boxes_id` = '".$mblInfo->module_boxes_id."'");
      $module_boxes_group_ids = tep_db_fetch_array($module_boxes_group_ids_query);
      //echo $module_boxes_group_ids['file_group_ids'];
      $module_boxes_group_id_array = explode(',',$module_boxes_group_ids['file_group_ids']);
      //print_r($module_boxes_group_id_array);
      foreach ($module_boxes_group_id_array as $val){
          foreach ($layout_group_files[$val] as $key => $val1 ){
              if(in_array($key, $layout_files)){
                  $file_group_str .= '<input checked type="checkbox" name="layout_files[]" value="'.$key.'" /> &nbsp;'.$val1.'<br/>';
              }else{
                  $file_group_str .= '<input type="checkbox" name="layout_files[]" value="'.$key.'" /> &nbsp;'.$val1.'<br/>';
              }        
          }
      }
      
      //$position_holding = explode(',',$mblInfo->position);
      /*foreach($position_array as $pkey => $pval){
          if(in_array($pkey, $position_holding)){
              $position_str .= '<input type="checkbox" checked name="position[]" value="'.$pkey.'"> '.$pval.' <br/>';
          }else{
              $position_str .= '<input type="checkbox" name="position[]" value="'.$pkey.'"> '.$pval.' <br/>';
          }
        
      }*/
	   $position_str = '<input type="hidden" name="position[]" value="l">';
        
      $contents = array('form' => tep_draw_form('module_boxes_layout', FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mblID=' . $mblInfo->module_boxes_layout_id . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => '');
      
      
      $contents[] = array('text' => '');
      $contents[] = array('text' => '<br>Title<br>' . tep_draw_input_field('title',$mblInfo->title));
      $contents[] = array('text' => '<br>Sort Order <br>' . tep_draw_input_field('sort_order',$mblInfo->sort_order));
      $contents[] = array('text' => '<br>Status <br><input type="radio" '.($mblInfo->status == '1'?' checked ':'').' value="1" name="status"> Active   <input '.($mblInfo->status == '0'?' checked ':'').' type="radio" value="0" name="status"> Inactive' );
      $contents[] = array('text' => '&nbsp;' . $position_str);
      $contents[] = array('text' => '<br>Select Files to Display box <br>' . $file_group_str);
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mblID=' . $mblInfo->module_boxes_layout_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>Module/Boxes Delete</b>');

      $contents = array('form' => tep_draw_form('module_boxes_layout', FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mblID=' . $mblInfo->module_boxes_layout_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $mblInfo->title . '</b>');
      

      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mblID=' . $mblInfo->module_boxes_layout_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (isset($mblInfo) && is_object($mblInfo)) {
        $position_holding = explode(',',$mblInfo->position);
        foreach($position_holding as $pos){
            $position_str .= $position_array[$pos].'<br/>';
        }
        
        $layout_files = explode(',',$mblInfo->layout_files);
        foreach ($layout_files as $fileID){
            $file_str .= $layout_files_array[$fileID].'<br>';
        }
        
        $heading[] = array('text' => '<b>' . $mblInfo->title . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mblID=' . $mblInfo->module_boxes_layout_id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_MODULE_BOXES_LAYOUT, 'template_id='.$_GET['template_id'].'&type='.$_GET['type'].'&page=' . $HTTP_GET_VARS['page'] . '&mblID=' . $mblInfo->module_boxes_layout_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a><br/><br/>');
        $contents[] = array('text' => '<b>Sort Order:</b> <br/>' . $mblInfo->sort_order . '<br/><br/>');
        $contents[] = array('text' => '<b>Status:</b> <br/>' . ($mblInfo->status == '1'?'Active':'Inactive') . '<br/><br/>');
        /*$contents[] = array('text' => '<b>Position:</b> <br/>' . $position_str . '<br/>');*/
        $contents[] = array('text' => '<b>Files:</b> <br/>' . $file_str . '');
        
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
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
