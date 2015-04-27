<?php
require('includes/application_top.php');
$action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');
if (tep_not_null($action)) {
    switch ($action) {
        case 'insert':
        case 'save':
            if (isset($HTTP_GET_VARS['dID'])){
                $department_id = tep_db_prepare_input($HTTP_GET_VARS['dID']);
            }
            $department_name = tep_db_prepare_input($HTTP_POST_VARS['department_name']);
            $department_email = tep_db_prepare_input($HTTP_POST_VARS['department_email']);

            $sql_data_array = array(
                'department_name' => $department_name,
                'department_email' => $department_email,
            );

            if ($action == 'insert') {
                tep_db_perform('departments', $sql_data_array);
                $department_id = tep_db_insert_id();
            } elseif ($action == 'save') {
                tep_db_perform('departments', $sql_data_array, 'update', "department_id = '" . (int)$department_id . "'");
            }

            tep_redirect(tep_href_link('departments.php', 'page=' . $HTTP_GET_VARS['page'] . '&dID=' . $department_id));
            break;
        case 'deleteconfirm':
            $department_id = tep_db_prepare_input($HTTP_GET_VARS['dID']);
            tep_db_query("delete from departments where department_id = '" . (int)$department_id . "'");
            tep_redirect(tep_href_link('departments.php', 'page=' . $HTTP_GET_VARS['page']));
            break;
        case 'delete':
            $department_id = tep_db_prepare_input($HTTP_GET_VARS['dID']);
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
            <td width="100%" valign="top">
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                        <td>
                            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="pageHeading">Departments</td>
                                    <td class="pageHeading" align="right">
                                        <?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top">
                                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                            <tr class="dataTableHeadingRow">
                                                <td class="dataTableHeadingContent">Department Name</td>
                                                <td class="dataTableHeadingContent">Department Email</td>
                                                <td class="dataTableHeadingContent" align="right">Action&nbsp;</td>
                                            </tr>
                                            <?php
                                            $departments_query_raw = "select department_id, department_name, department_email from departments order by department_name";
                                            $departments_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $departments_query_raw, $departments_query_numrows);
                                            $departments_query = tep_db_query($departments_query_raw);
                                            while ($department = tep_db_fetch_array($departments_query)) {
                                                if ((!isset($HTTP_GET_VARS['dID']) || (isset($HTTP_GET_VARS['dID']) && ($HTTP_GET_VARS['dID'] == $department['department_id']))) && !isset($dInfo) && (substr($action, 0, 3) != 'new')) {
                                                    $dInfo = new objectInfo($department);
                                                }

                                                if (isset($dInfo) && is_object($dInfo) && ($department['department_id'] == $dInfo->department_id) ) {
                                                    echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link('departments.php', 'page=' . $HTTP_GET_VARS['page'] . '&dID=' . $dInfo->department_id . '&action=edit') . '\'">' . "\n";
                                                } else {
                                                    echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link('departments.php', 'page=' . $HTTP_GET_VARS['page'] . '&dID=' . $department['department_id']) . '\'">' . "\n";
                                                }
                                            ?>
                                                <td class="dataTableContent"><?php echo $department['department_name']; ?></td>
                                                <td class="dataTableContent"><?php echo $department['department_email']; ?></td>
                                                <td class="dataTableContent" align="right"><?php if (isset($dInfo) && is_object($dInfo) && ($department['department_id'] == $dInfo->department_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link('departments.php', 'page=' . $HTTP_GET_VARS['page'] . '&dID=' . $department['department_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                            <tr>
                                                <td colspan="3">
                                                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                                        <tr>
                                                            <td class="smallText2" valign="top">
                                                                <?php echo $departments_split->display_count($departments_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], 'Displaying %d to %d (of %d departments) '); ?>
                                                            </td>
                                                            <td class="smallText2" align="right">
                                                                <?php echo $departments_split->display_links($departments_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                        if (empty($action)) {
                                                        ?>
                                                        <tr>
                                                            <td colspan="2" align="right">
                                                                <?php echo '<a href="' . tep_href_link('departments.php', 'page=' . $HTTP_GET_VARS['page'] . '&dID=' . $dInfo->department_id . '&action=new') . '">New Department</a>'; ?>
                                                            </td>
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
                                    switch ($action) {
                                        case 'new':
                                            $heading[] = array('text' => '<b>New Department</b>');

                                            $contents = array('form' => tep_draw_form('departments', 'departments.php', 'page=' . $HTTP_GET_VARS['page'] . (isset($dInfo) ? '&dID=' . $dInfo->department_id : '') . '&action=insert'));
                                            $contents[] = array('text' => 'Create new department');
                                            $contents[] = array('text' => '<br>Department Name:<br>' . tep_draw_input_field('department_name'));
                                            $contents[] = array('text' => '<br>Department Email:<br>' . tep_draw_input_field('department_email'));
                                            $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_insert.gif', IMAGE_INSERT) . ' <a href="' . tep_href_link('departments.php', 'page=' . $HTTP_GET_VARS['page'] . '&dID=' . $HTTP_GET_VARS['dID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                                            break;
                                        case 'edit':
                                            $heading[] = array('text' => '<b>Edit Department</b>');

                                            $contents = array('form' => tep_draw_form('departments', 'departments.php', 'page=' . $HTTP_GET_VARS['page'] . '&dID=' . $dInfo->department_id . '&action=save'));
                                            $contents[] = array('text' => 'Edit Department');
                                            $contents[] = array('text' => '<br>Department Name:<br>' . tep_draw_input_field('department_name', $dInfo->department_name));
                                            $contents[] = array('text' => '<br>Department Email:<br>' . tep_draw_input_field('department_email', $dInfo->department_email));
                                            $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_update.gif', IMAGE_UPDATE) . ' <a href="' . tep_href_link('departments.php', 'page=' . $HTTP_GET_VARS['page'] . '&dID=' . $dInfo->department_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                                            break;
                                        case 'delete':
                                            $heading[] = array('text' => '<b>Delete Department</b>');

                                            $contents[] = array('text' => 'Are you sure you want to delete this department?');
                                            $contents[] = array('text' => '<br><b>' . $dInfo->department_name . '</b>');
                                            $contents[] = array('align' => 'center', 'text' => '<br><a href="' . tep_href_link('departments.php', 'page=' . $HTTP_GET_VARS['page'] . '&dID=' . $dInfo->department_id . '&action=deleteconfirm') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a> <a href="' . tep_href_link('departments.php', 'page=' . $HTTP_GET_VARS['page'] . '&dID=' . $dInfo->department_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                                            break;
                                        default:
                                            if (is_object($dInfo)) {
                                                $heading[] = array('text' => '<b>' . $dInfo->department_name . '</b>');

                                                $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link('departments.php', 'page=' . $HTTP_GET_VARS['page'] . '&dID=' . $dInfo->department_id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link('departments.php', 'page=' . $HTTP_GET_VARS['page'] . '&dID=' . $dInfo->department_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
                                                $contents[] = array('text' => '<br>Department Name: ' . $dInfo->department_name);
                                                $contents[] = array('text' => 'Department Email: ' . $dInfo->department_email);
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
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
