<?php
/*
  $Id: language_manager.php
  
  Copyright (c) 2015 OBN
  
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('current_path')) {
    $current_path = DIR_FS_DOCUMENT_ROOT."includes/languages/";
    tep_session_register('current_path');
  }

  if (isset($HTTP_GET_VARS['goto'])) {
    $current_path = $HTTP_GET_VARS['goto'];
    $_SESSION['current_path'] = $current_path; 
	tep_redirect(tep_href_link(FILENAME_LANGUAGE_MANAGER));
  }

  if (strstr($current_path, '..')) {
	  $current_path = DIR_FS_DOCUMENT_ROOT."includes/languages/";
  }
  
  if (!is_dir($current_path)) {
	  $current_path =  DIR_FS_DOCUMENT_ROOT."includes/languages/";
  }

  
  if (preg_match('@^' . DIR_FS_DOCUMENT_ROOT."includes/languages/@", $current_path)){
    // do nothing
  }else{
	  $current_path = DIR_FS_DOCUMENT_ROOT."includes/languages/";
  }

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'reset':
        tep_session_unregister('current_path');
        tep_redirect(tep_href_link(FILENAME_LANGUAGE_MANAGER));
        break;
      case 'deleteconfirm':
        if (strstr($HTTP_GET_VARS['info'], '..')) tep_redirect(tep_href_link(FILENAME_LANGUAGE_MANAGER));

        tep_remove($current_path . '/' . $HTTP_GET_VARS['info']);
        if (!$tep_remove_error) tep_redirect(tep_href_link(FILENAME_LANGUAGE_MANAGER));
        break;
      case 'insert':
        if (mkdir($current_path . '/' . $HTTP_POST_VARS['folder_name'], 0777)) {
          tep_redirect(tep_href_link(FILENAME_LANGUAGE_MANAGER, 'info=' . urlencode($HTTP_POST_VARS['folder_name'])));
        }
        break;
      case 'save':
        
        if(count($_POST['constant'])){
            if ($fp = fopen($current_path . '/' . $HTTP_POST_VARS['filename'], 'w+')) {
                fputs($fp, "<?php"."\n");
                foreach($_POST['constant'] as $constantKey => $constantValue){
                    $str_constant = 'define("'.trim(addslashes($constantKey)).'", "'.trim(addslashes($constantValue)).'");'."\n";
                    fputs($fp, $str_constant);
                }
                fputs($fp, "?>");
                fclose($fp);
                tep_redirect(tep_href_link(FILENAME_LANGUAGE_MANAGER, 'info=' . urlencode($HTTP_POST_VARS['filename'])));            
            }
        }
        break;
      case 'processuploads':
        for ($i=1; $i<6; $i++) {
         // if (isset($GLOBALS['file_' . $i]) && tep_not_null($GLOBALS['file_' . $i])) {
          if (isset($_FILES['file_'. $i]) && !empty($_FILES['file_'. $i]['name'])) {
            new upload('file_' . $i, $current_path);
          }
        }

        tep_redirect(tep_href_link(FILENAME_LANGUAGE_MANAGER));
        break;
      case 'download':
        header('Content-type: application/x-octet-stream');
        header('Content-disposition: attachment; filename=' . urldecode($HTTP_GET_VARS['filename']));
        readfile($current_path . '/' . urldecode($HTTP_GET_VARS['filename']));
        exit;
        break;
      case 'upload':
      case 'new_folder':
      case 'new_file':
        $directory_writeable = true;
        if (!is_writeable($current_path)) {
          $directory_writeable = false;
          $messageStack->add(sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, $current_path), 'error');
        }
        break;
      case 'edit':
        if (strstr($HTTP_GET_VARS['info'], '..')) tep_redirect(tep_href_link(FILENAME_LANGUAGE_MANAGER));

        $file_writeable = true;
        if (!is_writeable($current_path . '/' . $HTTP_GET_VARS['info'])) {
          $file_writeable = false;
          $messageStack->add(sprintf(ERROR_FILE_NOT_WRITEABLE, $current_path . '/' . $HTTP_GET_VARS['info']), 'error');
        }
        break;
      case 'delete':
        if (strstr($HTTP_GET_VARS['info'], '..')) tep_redirect(tep_href_link(FILENAME_LANGUAGE_MANAGER));
        break;
    }
  }

  $current_path_array = explode('/', $current_path);
  
  $goto_array = array(array('id' => DIR_FS_DOCUMENT_ROOT."includes/languages/", 'text' => "languages"));
  
  for ($i=0, $n=sizeof($current_path_array); $i<$n; $i++) {
    if(empty($current_path_array[$i])){
        continue;
        
    }
    if($current_path_array[$i] == 'languages'){
		continue;
	}
	if ((isset($document_root_array[$i]) && ($current_path_array[$i] != $document_root_array[$i])) || !isset($document_root_array[$i])) {
      $goto_array[] = array('id' => implode('/', array_slice($current_path_array, 0, $i+1)), 'text' => $current_path_array[$i] =='full' ? 'templates':$current_path_array[$i]);
    }
  }
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
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
               <div class="panel-heading"><?php echo HEADING_TITLE; ?></div>
               <!-- START table-responsive-->
               
               <div class="table-responsive">
               <!-- START your table-->
<table class="table table-bordered table-hover">
  <tr>
<!-- body_text //-->
    <td><table class="table table-bordered table-hover">
      
<?php
  if ( (($action == 'new_file') && ($directory_writeable == true)) || ($action == 'edit') ) {
    if (isset($HTTP_GET_VARS['info']) && strstr($HTTP_GET_VARS['info'], '..')) tep_redirect(tep_href_link(FILENAME_LANGUAGE_MANAGER));

    if (!isset($file_writeable)) $file_writeable = true;
    $file_contents = '';
    if ($action == 'new_file') {
      $filename_input_field = tep_draw_input_field('filename');
    } elseif ($action == 'edit') {
      
        $constantsBeforeInclude = getUserDefinedConstants();
        include($current_path . '/' . $HTTP_GET_VARS['info']);
        $constantsAfterInclude = getUserDefinedConstants();
        $languageConstants = array_diff_assoc($constantsAfterInclude, $constantsBeforeInclude);
      
      /*if ($file_array = file($current_path . '/' . $HTTP_GET_VARS['info'])) {
        $file_contents = addslashes(implode('', $file_array));
      }*/
      $filename_input_field = $HTTP_GET_VARS['info'] . tep_draw_hidden_field('filename', $HTTP_GET_VARS['info']);
    }
?>
      
      <tr><?php echo tep_draw_form('new_file', FILENAME_LANGUAGE_MANAGER, 'action=save'); ?>
        <td><table class="table table-bordered table-hover">
          <tr>
            <td><?php echo TEXT_FILE_NAME; ?></td>
            <td><?php echo $filename_input_field; ?></td>
          </tr>
            
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>   
                 
          <?php
          foreach($languageConstants as $constantKey => $constantValue){?>
          <tr>
            <td><?php echo $constantKey; ?></td>
            <td>
                <textarea name="constant[<?php echo $constantKey; ?>]" id="<?php echo $constantKey; ?>"><?php echo trim(stripslashes($constantValue)); ?></textarea>
            </td>
          </tr>
          <?php } ?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td align="right" colspan="2"><?php if ($file_writeable == true) echo tep_image_submit('button_save.gif', IMAGE_SAVE) . '&nbsp;'; echo '<a href="' . tep_href_link(FILENAME_LANGUAGE_MANAGER, (isset($HTTP_GET_VARS['info']) ? 'info=' . urlencode($HTTP_GET_VARS['info']) : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
          </tr>
        </table></td>
      </form></tr>
<?php
  } else {
    $showuser = (function_exists('posix_getpwuid') ? true : false);
    $contents = array();
    $dir = dir($current_path);
    while ($file = $dir->read()) {
      if ( ($file != '.') && ($file != 'CVS') && ( ($file != '..') || ($current_path != DIR_FS_DOCUMENT_ROOT."images/") ) ) {
        $file_size = number_format(filesize($current_path . '/' . $file)) . ' bytes';

        $permissions = tep_get_file_permissions(fileperms($current_path . '/' . $file));
        if ($showuser) {
          $user = @posix_getpwuid(fileowner($current_path . '/' . $file));
          $group = @posix_getgrgid(filegroup($current_path . '/' . $file));
        } else {
          $user = $group = array();
        }

        $contents[] = array('name' => $file,
                            'is_dir' => is_dir($current_path . '/' . $file),
                            'last_modified' => strftime(DATE_TIME_FORMAT, filemtime($current_path . '/' . $file)),
                            'size' => $file_size,
                            'permissions' => $permissions,
                            'user' => $user['name'],
                            'group' => $group['name']);
      }
    }

    function tep_cmp($a, $b) {
      return strcmp( ($a['is_dir'] ? 'D' : 'F') . $a['name'], ($b['is_dir'] ? 'D' : 'F') . $b['name']);
    }
    usort($contents, 'tep_cmp');
?>

      <tr>
        <td><table class="table table-bordered table-hover">
          <tr>
            <td><table class="table table-bordered table-hover">
              <tr>
                <td><?php echo TABLE_HEADING_FILENAME; ?></td>
                <td align="right"><?php echo TABLE_HEADING_SIZE; ?></td>
                <td align="center"><?php echo TABLE_HEADING_PERMISSIONS; ?></td>
                <td><?php echo TABLE_HEADING_USER; ?></td>
                <td><?php echo TABLE_HEADING_GROUP; ?></td>
                <td align="center"><?php echo TABLE_HEADING_LAST_MODIFIED; ?></td>
                <td align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  for ($i=0, $n=sizeof($contents); $i<$n; $i++) {
    if ((!isset($HTTP_GET_VARS['info']) || (isset($HTTP_GET_VARS['info']) && ($HTTP_GET_VARS['info'] == $contents[$i]['name']))) && !isset($fInfo) && ($action != 'upload') && ($action != 'new_folder')) {
      $fInfo = new objectInfo($contents[$i]);
    }

    if ($contents[$i]['name'] == '..') {
      $goto_link = substr($current_path, 0, strrpos($current_path, '/'));
    } else {
      $goto_link = $current_path . '/' . $contents[$i]['name'];
    }

    if (isset($fInfo) && is_object($fInfo) && ($contents[$i]['name'] == $fInfo->name)) {
      if ($fInfo->is_dir) {
        echo '              <tr id="defaultSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . "\n";
        $onclick_link = 'goto=' . $goto_link;
      } else {
        echo '              <tr id="defaultSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . "\n";
        $onclick_link = 'info=' . urlencode($fInfo->name) . '&action=edit';
      }
    } else {
      echo '              <tr onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . "\n";
      $onclick_link = 'info=' . urlencode($contents[$i]['name']);
    }

    if ($contents[$i]['is_dir']) {
      if ($contents[$i]['name'] == '..') {
        $icon = tep_image(DIR_WS_ICONS . 'previous_level.gif', ICON_PREVIOUS_LEVEL);
      } else {
        $icon = (isset($fInfo) && is_object($fInfo) && ($contents[$i]['name'] == $fInfo->name) ? tep_image(DIR_WS_ICONS . 'current_folder.gif', ICON_CURRENT_FOLDER) : tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER));
      }
      $link = tep_href_link(FILENAME_LANGUAGE_MANAGER, 'goto=' . $goto_link);
    } else {
      $icon = tep_image(DIR_WS_ICONS . 'file_download.gif', ICON_FILE_DOWNLOAD);
      $link = tep_href_link(FILENAME_LANGUAGE_MANAGER, 'action=download&filename=' . urlencode($contents[$i]['name']));
    }
?>
                <td onClick="document.location.href='<?php echo tep_href_link(FILENAME_LANGUAGE_MANAGER, $onclick_link); ?>'"><?php echo '<a href="' . $link . '">' . $icon . '</a>&nbsp;' . $contents[$i]['name']; ?></td>
                <td align="right" onClick="document.location.href='<?php echo tep_href_link(FILENAME_LANGUAGE_MANAGER, $onclick_link); ?>'"><?php echo ($contents[$i]['is_dir'] ? '&nbsp;' : $contents[$i]['size']); ?></td>
                <td align="center" onClick="document.location.href='<?php echo tep_href_link(FILENAME_LANGUAGE_MANAGER, $onclick_link); ?>'"><tt><?php echo $contents[$i]['permissions']; ?></tt></td>
                <td onClick="document.location.href='<?php echo tep_href_link(FILENAME_LANGUAGE_MANAGER, $onclick_link); ?>'"><?php echo $contents[$i]['user']; ?></td>
                <td onClick="document.location.href='<?php echo tep_href_link(FILENAME_LANGUAGE_MANAGER, $onclick_link); ?>'"><?php echo $contents[$i]['group']; ?></td>
                <td align="center" onClick="document.location.href='<?php echo tep_href_link(FILENAME_LANGUAGE_MANAGER, $onclick_link); ?>'"><?php echo $contents[$i]['last_modified']; ?></td>
                <td align="right"><?php if ($contents[$i]['name'] != '..') echo '<a href="' . tep_href_link(FILENAME_LANGUAGE_MANAGER, 'info=' . urlencode($contents[$i]['name']) . '&action=delete') . '">' . tep_image(DIR_WS_ICONS . 'delete.gif', ICON_DELETE) . '</a>&nbsp;'; if (isset($fInfo) && is_object($fInfo) && ($fInfo->name == $contents[$i]['name'])) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_LANGUAGE_MANAGER, 'info=' . urlencode($contents[$i]['name'])) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="7"><table class="table table-bordered table-hover">
                  <tr>
                    <td><?php echo '<a href="' . tep_href_link(FILENAME_LANGUAGE_MANAGER, 'action=reset') . '">' . tep_image_button('button_reset_b.gif', IMAGE_RESET) . '</a>'; ?></td>
                    <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_LANGUAGE_MANAGER, (isset($HTTP_GET_VARS['info']) ? 'info=' . urlencode($HTTP_GET_VARS['info']) . '&' : '') . 'action=upload') . '">' . tep_image_button('button_upload_b.gif', IMAGE_UPLOAD) . '</a>&nbsp;<a href="' . tep_href_link(FILENAME_LANGUAGE_MANAGER, (isset($HTTP_GET_VARS['info']) ? 'info=' . urlencode($HTTP_GET_VARS['info']) . '&' : '') . 'action=new_file') . '">' . tep_image_button('button_new_file_b.gif', IMAGE_NEW_FILE) . '</a>&nbsp;<a href="' . tep_href_link(FILENAME_LANGUAGE_MANAGER, (isset($HTTP_GET_VARS['info']) ? 'info=' . urlencode($HTTP_GET_VARS['info']) . '&' : '') . 'action=new_folder') . '">' . tep_image_button('button_new_folder.gif', IMAGE_NEW_FOLDER) . '</a>'; ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
    $heading = array();
    $contents = array();

    switch ($action) {
      case 'delete':
        $heading[] = array('text' => '<b>' . $fInfo->name . '</b>');

        $contents = array('form' => tep_draw_form('file', FILENAME_LANGUAGE_MANAGER, 'info=' . urlencode($fInfo->name) . '&action=deleteconfirm'));
        $contents[] = array('text' => TEXT_DELETE_INTRO);
        $contents[] = array('text' => '<br><b>' . $fInfo->name . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_LANGUAGE_MANAGER, (tep_not_null($fInfo->name) ? 'info=' . urlencode($fInfo->name) : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'new_folder':
        $heading[] = array('text' => '<b>' . TEXT_NEW_FOLDER . '</b>');

        $contents = array('form' => tep_draw_form('folder', FILENAME_LANGUAGE_MANAGER, 'action=insert'));
        $contents[] = array('text' => TEXT_NEW_FOLDER_INTRO);
        $contents[] = array('text' => '<br>' . TEXT_FILE_NAME . '<br>' . tep_draw_input_field('folder_name'));
        $contents[] = array('align' => 'center', 'text' => '<br>' . (($directory_writeable == true) ? tep_image_submit('button_save.gif', IMAGE_SAVE) : '') . ' <a href="' . tep_href_link(FILENAME_LANGUAGE_MANAGER, (isset($HTTP_GET_VARS['info']) ? 'info=' . urlencode($HTTP_GET_VARS['info']) : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'upload':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_UPLOAD . '</b>');

        $contents = array('form' => tep_draw_form('file', FILENAME_LANGUAGE_MANAGER, 'action=processuploads', 'post', 'enctype="multipart/form-data"'));
        $contents[] = array('text' => TEXT_UPLOAD_INTRO);

        $file_upload = '';
        for ($i=1; $i<6; $i++) $file_upload .= tep_draw_file_field('file_' . $i) . '<br>';

        $contents[] = array('text' => '<br>' . $file_upload);
        $contents[] = array('align' => 'center', 'text' => '<br>' . (($directory_writeable == true) ? tep_image_submit('button_upload.gif', IMAGE_UPLOAD) : '') . ' <a href="' . tep_href_link(FILENAME_LANGUAGE_MANAGER, (isset($HTTP_GET_VARS['info']) ? 'info=' . urlencode($HTTP_GET_VARS['info']) : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      default:
        if (isset($fInfo) && is_object($fInfo)) {
          $heading[] = array('text' => '<b>' . $fInfo->name . '</b>');

          if (!$fInfo->is_dir) $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_LANGUAGE_MANAGER, 'info=' . urlencode($fInfo->name) . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>');
          $contents[] = array('text' => '<br>' . TEXT_FILE_NAME . ' <b>' . $fInfo->name . '</b>');
          if (!$fInfo->is_dir) $contents[] = array('text' => '<br>' . TEXT_FILE_SIZE . ' <b>' . $fInfo->size . '</b>');
          $contents[] = array('text' => '<br>' . TEXT_LAST_MODIFIED . ' ' . $fInfo->last_modified);
        }
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
<?php
  }
function getUserDefinedConstants() {
    $constants = get_defined_constants(true);
    return (isset($constants['user']) ? $constants['user'] : array());  
}
?>
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