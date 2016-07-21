<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
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
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $('select[name="navigation"]').live('change', function(){
                        location.href = '<?php echo tep_href_link('ebay_feeds_manager.php') ?>?page=' + $(this).val();
                });
            });
        </script>


<!-- body //-->

               <!-- START your table-->
<table class="table table-bordered table-hover">
            <tr>
                <td>
                    <table class="table table-bordered table-hover">
                        <tr>
                            <td>
                                <table class="table table-bordered table-hover">
                                    <tr>
                                        <td>
                                        <?php echo '"Failed" Products listing, as of now'; ?>
                                        </td>
                                        <td align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                <?php
                $sql = tep_db_query("select * from ebay_product_feed_errors where environment='" . EBAY_ENVIRONMENT . "' and ack='failure' order by date_added desc");
                if (tep_db_num_rows($sql)){
                ?>
                    <table class="table table-bordered table-hover">
                        <tr>
                            <td><b>SKU</b></td>
                            <td><b>Job Type</b></td>
                            <td><b>Error</b></td>
                            <td><b>Action</b></td>
                        </tr>
                        <?php while($entry = tep_db_fetch_array($sql)) { ?>
                        <tr>
                            <td><?php echo  $entry['sku']; ?></td>
                            <td><?php echo $entry['job_type']; ?></td>
                            <td><?php echo $entry['error_messages']; ?></td>
                            <td>
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
               <!-- END your table-->
<!-- body_eof //-->



<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>