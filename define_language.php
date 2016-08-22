<?php
/*
  $Id: define_language.php,v 1.15 2003/07/08 21:51:37 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

  require('includes/application_top.php');

  if (!isset($HTTP_GET_VARS['lngdir'])) $HTTP_GET_VARS['lngdir'] = $language;

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'save':
        if (isset($HTTP_GET_VARS['lngdir']) && isset($HTTP_GET_VARS['filename'])) {
          if ($HTTP_GET_VARS['filename'] == $HTTP_GET_VARS['lngdir'] . '.php') {
            $file = DIR_FS_CATALOG_LANGUAGES . $HTTP_GET_VARS['filename'];
          } else {
            $file = DIR_FS_CATALOG_LANGUAGES . $HTTP_GET_VARS['lngdir'] . '/' . $HTTP_GET_VARS['filename'];
          }

          if (file_exists($file)) {
            if (file_exists('bak' . $file)) {
              @unlink('bak' . $file);
            }

            @rename($file, 'bak' . $file);

            $new_file = fopen($file, 'w');
            $file_contents = stripslashes($HTTP_POST_VARS['file_contents']);
            fwrite($new_file, $file_contents, strlen($file_contents));
            fclose($new_file);
          }
          tep_redirect(tep_href_link(FILENAME_DEFINE_LANGUAGE, 'lngdir=' . $HTTP_GET_VARS['lngdir']));
        }
        break;
    }
  }

  $languages_array = array();
  $languages = tep_get_languages();
  $lng_exists = false;
  for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
    if ($languages[$i]['directory'] == $HTTP_GET_VARS['lngdir']) $lng_exists = true;

    $languages_array[] = array('id' => $languages[$i]['directory'],
                               'text' => $languages[$i]['name']);
  }

  if (!$lng_exists) $HTTP_GET_VARS['lngdir'] = $language;
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
               
               <!-- START table-responsive-->
               
               <div class="table-responsive">
               <!-- START your table-->
<table class="table table-bordered table-hover">
  <tr>
<!-- body_text //-->
    <td><table class="table table-bordered table-hover">
      <tr>
        <td><table class="table table-bordered table-hover">
          <tr><?php echo tep_draw_form('lng', FILENAME_DEFINE_LANGUAGE, '', 'get'); ?>
            <td><?php echo HEADING_TITLE; ?></td>
            <td align="right"><?php echo tep_draw_separator('pixel_trans.gif', '1', HEADING_IMAGE_HEIGHT); ?></td>
            <td align="right"><?php echo tep_draw_pull_down_menu('lngdir', $languages_array, $language, 'onChange="this.form.submit();"'); ?></td>
          </form></tr>
        </table></td>
      </tr>
      <tr>
        <td><table class="table table-bordered table-hover">
<?php
  if (isset($HTTP_GET_VARS['lngdir']) && isset($HTTP_GET_VARS['filename'])) {
    if ($HTTP_GET_VARS['filename'] == $HTTP_GET_VARS['lngdir'] . '.php') {
      $file = DIR_FS_CATALOG_LANGUAGES . $HTTP_GET_VARS['filename'];
    } else {
      $file = DIR_FS_CATALOG_LANGUAGES . $HTTP_GET_VARS['lngdir'] . '/' . $HTTP_GET_VARS['filename'];
    }

    if (file_exists($file)) {
      $file_array = file($file);
      $contents = implode('', $file_array);

      $file_writeable = true;
      if (!is_writeable($file)) {
        $file_writeable = false;
        $messageStack->reset();
        $messageStack->add(sprintf(ERROR_FILE_NOT_WRITEABLE, $file), 'error');
        echo $messageStack->output();
      }

?>
          <tr><?php echo tep_draw_form('language', FILENAME_DEFINE_LANGUAGE, 'lngdir=' . $HTTP_GET_VARS['lngdir'] . '&filename=' . $HTTP_GET_VARS['filename'] . '&action=save'); ?>
            <td><table class="table table-bordered table-hover">
              <tr>
                <td>&bsp;</td>
              </tr>
              <tr>
                <td><b><?php echo $HTTP_GET_VARS['filename']; ?></b></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_textarea_field('file_contents', 'soft', '80', '20', $contents, (($file_writeable) ? '' : 'readonly')); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td align="right"><?php if ($file_writeable == true) { echo tep_image_submit('button_save.gif', IMAGE_SAVE) . '&nbsp;<a href="' . tep_href_link(FILENAME_DEFINE_LANGUAGE, 'lngdir=' . $HTTP_GET_VARS['lngdir']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; } else { echo '<a href="' . tep_href_link(FILENAME_DEFINE_LANGUAGE, 'lngdir=' . $HTTP_GET_VARS['lngdir']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; } ?></td>
              </tr>
            </table></td>
          </form></tr>
<?php
    } else {
?>
          <tr>
            <td><b><?php echo TEXT_FILE_DOES_NOT_EXIST; ?></b></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td><?php echo '<a href="' . tep_href_link(FILENAME_DEFINE_LANGUAGE, 'lngdir=' . $HTTP_GET_VARS['lngdir']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
          </tr>
<?php
    }
  } else {
    $filename = $HTTP_GET_VARS['lngdir'] . '.php';
?>
          <tr>
            <td><table class="table table-bordered table-hover">
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td><a href="<?php echo tep_href_link(FILENAME_DEFINE_LANGUAGE, 'lngdir=' . $HTTP_GET_VARS['lngdir'] . '&filename=' . $filename); ?>" style="color: #000"><b><?php echo $filename; ?></b></a></td>
<?php
    $left = false;
    if ($dir = dir(DIR_FS_CATALOG_LANGUAGES . $HTTP_GET_VARS['lngdir'])) {
      $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
      while ($file = $dir->read()) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          echo '                <td><a href="' . tep_href_link(FILENAME_DEFINE_LANGUAGE, 'lngdir=' . $HTTP_GET_VARS['lngdir'] . '&filename=' . $file) . '" style="color: #000">' . $file . '</a></td>' . "\n";
          if (!$left) {
            echo '              </tr>' . "\n" .
                 '              <tr>' . "\n";
          }
          $left = !$left;
        }
      }
      $dir->close();
    }
?>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_FILE_MANAGER, 'current_path=' . DIR_FS_CATALOG_LANGUAGES . $HTTP_GET_VARS['lngdir']) . '">' . tep_image_button('button_file_manager.gif', IMAGE_FILE_MANAGER) . '</a>'; ?></td>
          </tr>
<?php
  }
?>
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