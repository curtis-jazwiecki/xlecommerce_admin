<?php
require('includes/application_top.php');

$action = isset($_GET['action']) && !empty($_GET['action']) ? strtolower(trim($_GET['action'])) : '';

switch($action){
	case 'remove_from_ebay':
		$sku = trim($_GET['sku']);
		$environment = trim($_GET['environment']);
		
		if (!empty($sku) && !empty($environment)){
			$delete_query = "delete from ebay_product_feed_errors where sku='" . tep_db_prepare_input($sku) . "' and environment='" . tep_db_prepare_input($environment) . "'";
			$update_query = "update products set is_ebay_ok='0' where products_model='" . tep_db_prepare_input($sku) . "'";
			tep_db_query($delete_query);
			tep_db_query($update_query);
			tep_redirect(tep_href_link('ebay_failed_products.php'));
		}
		
		break;
}
?>
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
        <table border="0" align="center" cellpadding="2" cellspacing="2" style="width:100%;">
            <tr>
                <td width="100%" valign="top">
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                            <td>
                                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="pageHeading">
                                        <?php echo '"Failed" Products listing, as of now'; ?>
                                        </td>
                                        <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="color:white;width:100%;">
                <?php
                $sql = tep_db_query("select * from ebay_product_feed_errors where environment='" . EBAY_ENVIRONMENT . "' and ack='failure' order by date_added desc");
                if (tep_db_num_rows($sql)){
                ?>
                    <table border="0" style="width:100%;">
                        <tr class="dataTableHeadingRow">
                            <td class="dataTableHeadingContent"><b>SKU</b></td>
                            <td class="dataTableHeadingContent"><b>Job Type</b></td>
                            <td class="dataTableHeadingContent"><b>Error</b></td>
                            <td class="dataTableHeadingContent"><b>Action</b></td>
                        </tr>
                        <?php while($entry = tep_db_fetch_array($sql)) { ?>
                        <tr class="dataTableRow">
                            <td class="smallText dataTableContent"><?php echo  $entry['sku']; ?></td>
                            <td class="smallText dataTableContent"><?php echo $entry['job_type']; ?></td>
                            <td class="smallText dataTableContent"><?php echo $entry['error_messages']; ?></td>
                            <td class="smallText dataTableContent">
								<a href="<?php echo tep_href_link('ebay_failed_products.php', 'sku=' . $entry['sku'] . '&environment=' . EBAY_ENVIRONMENT . '&action=remove_from_ebay'); ?>"><button>Remove from eBay</button></a>
							</td>
                        </tr>
                        <?php } ?>
                    </table>
                <?php
                } else {
                    echo 'None';
                }
                ?>
                </td>
            </tr>
        </table>
    </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>