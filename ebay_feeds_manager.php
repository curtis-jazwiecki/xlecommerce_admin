<?php
require('includes/application_top.php');

$action = isset($_POST['action']) ? $_POST['action'] : '';
if (empty($action)){
    $action = isset($_GET['action']) ? $_GET['action'] : '';
}

switch($action){
    case 'initiate_upload':
        ob_start();
        include_once('ebay_create_upload_jobs.php');
        ob_end_clean();
        tep_redirect(tep_href_link('ebay_feeds_manager.php'));
        break;
    case 'initiate_download':
        ob_start();
        include_once('ebay_create_download_jobs.php');
        ob_end_clean();
        tep_redirect(tep_href_link('ebay_feeds_manager.php'));
        break;
}

$page = isset($_GET['page']) ? $_GET['page'] : '1';
$entries_per_page = 10;

$sql = tep_db_query("select count(*) as count from ebay_jobs");
$info = tep_db_fetch_array($sql);
$upload_jobs_entries_count = $info['count'];

$sql = tep_db_query("select count(*) as count from ebay_download_jobs");
$info = tep_db_fetch_array($sql);
$download_jobs_entries_count = $info['count'];

$total_entries = $upload_jobs_entries_count + $download_jobs_entries_count;

$total_pages = ceil($total_entries/$entries_per_page);
if ($page>$total_pages) $page = $total_pages;
$navigation = '';
if ($total_pages){
   $navigation = 'Page# <select name="navigation">';
   for($i=1; $i<=$total_pages; $i++){
       $navigation .= '<option value="' . $i . '" ' . ($page==$i ? ' selected ' : '') . '>' . $i . '</option>';
   }
   $navigation .= '</select> of ' . $total_pages;
}

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
        <title><?php echo TITLE; ?></title>
        <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
        <script language="javascript" src="includes/general.js"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/smoothness/jquery-ui.css" />
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $('select[name="navigation"]').live('change', function(){
                        location.href = '<?php echo tep_href_link('ebay_feeds_manager.php') ?>?page=' + $(this).val();
                });
            });
        </script>
    </head>
    <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
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
                                        <td class="pageHeading">
                                            <?php echo 'eBay Feeds Manager'; ?>                                            <input type="button" value="Initiate Upload Jobs" onclick="location.href='<?php echo tep_href_link('ebay_feeds_manager.php', 'action=initiate_upload'); ?>';" />
                                            &nbsp;
                                            <input type="button" value="Initiate Download Jobs" onclick="location.href='<?php echo tep_href_link('ebay_feeds_manager.php', 'action=initiate_download'); ?>';" style="display:none;" />                                        </td>
                                        <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:white;width:100%;">
                            <?php if ($total_pages){ ?>
                            <div class="smallText" style="padding:10px 0 10px 0;"><?php echo $navigation; ?></div>
                            <table border="0" style="width:100%;">
                                <tr class="dataTableHeadingRow">
                                    <td class="dataTableHeadingContent"><b>#</b></td>
                                    <td class="dataTableHeadingContent"><b>Environment</b></td>
                                    <td class="dataTableHeadingContent"><b>Job Type</b></td>
                                    <td class="dataTableHeadingContent"><b>Job ID</b></td>
                                    <td class="dataTableHeadingContent"><b>File reference ID</b></td>
                                    <td class="dataTableHeadingContent"><b>Is Open</b></td>
                                </tr>
                            <?php
                            $sql = tep_db_query("(select date_added, id, environment, job_type, job_id, file_reference_id, is_open, 'uploads' as action from ebay_jobs) union (select date_added, id, environment, job_type, job_id, '' as file_reference_id, is_open, 'downloads' as action from ebay_download_jobs) order by 1 desc limit " . (($page-1)*$entries_per_page) . ", " . $entries_per_page);
                            $count = (($page - 1) * $entries_per_page);
                            while($entry = tep_db_fetch_array($sql)){
                                $count++;
                            ?>
                                <tr class="dataTableRow">
                                    <td class="smallText dataTableContent"><?php echo $count; ?></td>
                                    <td class="smallText dataTableContent"><?php echo $entry['environment']; ?></td>
                                    <td class="smallText dataTableContent"><?php echo $entry['job_type']; ?></td>
                                    <td class="smallText dataTableContent"><?php echo $entry['job_id']; ?></td>
                                    <td class="smallText dataTableContent"><?php echo $entry['file_reference_id']; ?></td>
                                    <td class="smallText dataTableContent"><?php echo $entry['is_open']; ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                            </table>
                            <div class="smallText" style="padding:10px 0 10px 0;"><?php echo $navigation; ?></div>
                            <?php } ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <iframe src="ebay_failed_products.php" style="width:100%;height:500px;"></iframe>
                </td>
            </tr>
        </table>
        <!-- body_text_eof //-->
        <!-- body_eof //-->
        <!-- footer //-->
        <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
        <!-- footer_eof //-->
        <br>
    </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>