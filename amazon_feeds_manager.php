<?php

require('includes/application_top.php');

require(DIR_FS_ADMIN . 'amazon_mws/config.php');

$action = isset($_POST['action']) ? $_POST['action'] : '';

if (empty($action)){

    $action = isset($_GET['action']) ? $_GET['action'] : '';

}



$page = isset($_GET['page']) ? $_GET['page'] : '1';

$entries_per_page = 10;

$sql = tep_db_query("select count(*) as count from amazon_feed");

$info = tep_db_fetch_array($sql);

$total_pages = ceil($info['count']/$entries_per_page);

if ($page>$total_pages) $page = $total_pages;

$navigation = '';

if ($total_pages){

   $navigation = 'Page# <select name="navigation">';

   for($i=1; $i<=$total_pages; $i++){

       $navigation .= '<option value="' . $i . '" ' . ($page==$i ? ' selected ' : '') . '>' . $i . '</option>';

   }

   $navigation .= '</select> of ' . $total_pages;

}



switch($action){

    case 'fire_feeds':

        $amazon = new amazon_manager('mws');

        $amazon->submit_product_feed();
        
        $amazon->submit_relationships_feed();

        $amazon->submit_price_feed();

        $amazon->submit_inventory_feed();

        $amazon->submit_image_feed();

        $messageStack->add_session('Product feeds fired', 'success');

        tep_redirect('amazon_feeds_manager.php');

        break;

    case 'fetch_responses':

        $amazon = new amazon_manager('mws');

        $amazon->fetch_feed_submission_results();

        $messageStack->add_session('Operation for fetching Amazon responses invoked', 'success');

        tep_redirect('amazon_feeds_manager.php');

        break;

}

?>

<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">

<script type="text/javascript" src="https://www.google.com/jsapi"></script>

        <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/smoothness/jquery-ui.css" />

        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>

        <script type="text/javascript">

            $(document).ready(function(){

                $('select[name="navigation"]').live('change', function(){

                        location.href = '<?php echo tep_href_link('amazon_feeds_manager.php') ?>?page=' + $(this).val();

                });

            });

        </script>

<!-- header //-->

<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

<!-- header_eof //-->



<!-- body //-->



         <section>

         <!-- START Page content-->

         <section class="main-content">

            <h3>Amazon Feeds Manager

               <br>

            </h3>

            <!-- START panel-->

            <div class="panel panel-default">

               <div class="panel-heading">Amazon Feeds Manager

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

                                <table class="table table-bordered table-hover">

                                    <tr>

                                        <td>

                                            <?php echo 'Amazon Feeds Manager'; ?>

                                            <input type="button" value="Fire Product Feed to Amazon" onClick="location.href='<?php echo tep_href_link('amazon_feeds_manager.php', 'action=fire_feeds'); ?>';" />

                                            &nbsp;

                                            <input type="button" value="Fetch Amazon Responses" onClick="location.href='<?php echo tep_href_link('amazon_feeds_manager.php', 'action=fetch_responses'); ?>';" />

                                        </td>

                                        <td align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>

                                    </tr>

                                </table>

                            </td>

                        </tr>

                        <tr>

                            <td>

                                <?php if ($total_pages){ ?>

                                <div><?php echo $navigation; ?></div>

                                <table border="0">

                                    <tr>

                                        <td"><b>#</b></td>

                                        <td><b>Feed Type</b></td>

                                        <td><b>View Feed</b></td>

                                        <td><b>Submission ID</b></td>

                                        <td><b>Submission Date</b></td>

                                        <td><b>Amazon Response</b></td>

                                        <td><b>Processing Info</b></td>

                                    </tr>

                                    <?php

                                    $sql = tep_db_query("select id, feed_type, filename, document_id, date_submitted, response_id, response_file_name, status, submission_error from amazon_feed order by date_submitted desc limit " . (($page-1)*$entries_per_page) . ", " . $entries_per_page);

                                    $count = (($page - 1) * $entries_per_page);

                                    while($entry = tep_db_fetch_array($sql)){

                                        $count++;

                                        if (!empty($entry['response_file_name']) && file_exists(AMAZON_RESPONSE_DIRECTORY . $entry['response_file_name'])){

                                            $xml = @simplexml_load_file(AMAZON_RESPONSE_DIRECTORY . $entry['response_file_name']);

                                            if ($xml){

                                                $summary_node = $xml->Message->ProcessingReport->ProcessingSummary;

                                                $messages_processed = $summary_node->MessagesProcessed;

                                                $messages_successful = $summary_node->MessagesSuccessful;

                                                $messages_w_error = $summary_node->MessagesWithError;

                                                $messages_w_warning = $summary_node->MessagesWithWarning;

                                                $message = 'Total messages: ' . $messages_processed . ' | ' .

                                                           'Processed: ' . $messages_successful . ' | ' .

                                                           'Errors: ' . $messages_w_error . ' | ' .

                                                           'Warnings: ' . $messages_w_warning;

                                                if (($messages_w_error + $messages_w_warning)>0){

                                                    $error = true;

                                                } else {

                                                    $error = false;

                                                }

                                            }

                                        }

                                        echo '<tr>

                                                <td>' . $count . '</td>

                                                <td>' . $entry['feed_type'] . '</td>

                                                <td align="center">' . (file_exists(AMAZON_OUTGOING_DIRECTORY . $entry['filename']) ? '<a href="' . tep_href_link('amazon_show_file_content.php', 'type=request&file_name=' . $entry['filename']) . '" target="_newwin"><span class="ui-icon ui-icon-document">V</span></a>' : '<span class="ui-icon ui-icon-cancel">--</span>') . '</td>

                                                <td>' . $entry['document_id'] . '</td>

                                                <td>' . $entry['date_submitted'] . '</td>

                                                <td align="center">' . (!empty($entry['response_file_name']) && file_exists(AMAZON_RESPONSE_DIRECTORY . $entry['response_file_name']) ? '<a href="' . tep_href_link('amazon_show_file_content.php', 'type=response&file_name=' . $entry['response_file_name']) . '" target="_newwin"><span class="ui-icon ui-icon-document">V</span></a>' : '--') . '</td>

                                                <td align="center">' . ($error ? '<span class="ui-icon ui-icon-alert" title="' . $message . '">V</span>' : '<span class="ui-icon ui-icon-info" title="' . $message . '">V</span>') . '</td>

                                              </tr>';

                                    }

                                    ?>

				</table>

                                <div><?php echo $navigation; ?></div>

                                <?php } ?>

                            </td>

                        </tr>

                    </table>

                </td>

            </tr>

        </table>

               <!-- END your table-->

<!-- body_eof //-->



<!-- footer //-->

<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>

<!-- footer_eof //-->



<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>