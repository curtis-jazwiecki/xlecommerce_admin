<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
$oID = (int)$_GET['oID'];
if (empty($_GET['oID'])) {
	$title = "No Labels Found";
	$message = 'Please specify an order number to view the order\'s shipping label(s).';
}else{
	$current_label = isset($_GET['label_id']) && is_numeric($_GET['label_id']) ? intval($_GET['label_id']) : 1;
	$total_labels = tep_get_label_count($oID);
	
	if ($total_labels == 0) {
		$title = "No Labels Found";
		$message = "No labels found for order #{$oID}";
	} else {
		$title = "Label {$current_label} of {$total_labels}";
		$label_filename = sprintf('labels/shipExpressLabel-%s-%s.png',$oID, $current_label - 1);
	}
}

function tep_manifest_data($oID, $active) {
	if (!$active) {
		$packages_query = tep_db_query("select tracking_num from " . TABLE_SHIPPING_MANIFEST . " where orders_id = " . $oID . "");
	} else {	
		$packages_query = tep_db_query("select tracking_num from " . TABLE_SHIPPING_MANIFEST . " where orders_id = " . $oID . " and multiple = '" . $active . "'");
	}
	
	while ($val = tep_db_fetch_array($packages_query)) {
		$tracking_num = $val['tracking_num'];
	}
	
	return $tracking_num;
}

function tep_get_label_count($oID) {
	$count = 0;
	$file_prefix = "shipExpressLabel-$oID-";
	$dir = 'labels';
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (preg_match('/^'.$file_prefix.'.*.png/', $file)) {
					$count++;
					error_log("found match $file");
				}
			}
			closedir($dh);
		}
	}
	return $count;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
		<title><?php echo $title; ?></title>
		<style type="text/css">
			<!--
			table {
				width: 380;
			}
			td {
				font-family: Verdana, Arial, sans-serif;
				font-size: 12px;
			}
			-->
			</style>
				<style type="text/css" media="screen">
			.navigation {
				position:relative;
				float:center;
			}
			</style>
				<style type="text/css" media="print">
			body {
				background-color:#FFFFFF;
				background-image:none;
				color:#000000
			}
			.navigation {
				display:none;
			}
		</style>
		<script language="JavaScript1.1" type="text/javascript">
		var NS4 = (document.layers) ? true : false ;var resolution = 96;if (NS4 && navigator.javaEnabled()){var toolkit = java.awt.Toolkit.getDefaultToolkit();resolution = toolkit.getScreenResolution();}
		</script>
    </head>
    <body>
		<?php if (isset($message)) : ?>
		<p class="error"><?php echo $message; ?></p>
		<?php else : ?>
		<table border="0" width="380">
			<tr>
				<td align="center" colspan="3"><img src="images/pixel_trans.gif" alt="" border="0" height="20" width="1"></td>
			</tr>
			<tr>
				<td align="center" width="20%">
					<a href="#" onclick="window.print(); return false"> <img class="navigation" src="includes/languages/english/images/buttons/button_print.gif" border="0" alt="IMAGE_ORDERS_PRINT" title=" IMAGE_ORDERS_PRINT "> </a>
				</td>
				<td align="center" width="60%">
					<img src="images/pixel_trans.gif" alt="" border="0" height="20" width="1">
					<?php
						// links for multiple packages
			
						if ($current_label != 1) {
							echo '<a href="fedex_popup_thermal.php?num='.$num.'&oID='.$oID.'&label_id='.($current_label - 1).'" target="_self">&lt;- back</a> &nbsp; ';
						}
						else {
							echo '&lt;- back';
						}
						
						echo " &nbsp; $current_label of $total_labels &nbsp; ";
						
						if ($current_label < $total_labels) {
							echo ' &nbsp; <a href="fedex_popup_thermal.php?num='.$num.'&oID='.$oID.'&label_id='.($current_label + 1).'">next -&gt;</a>';
						}
						else {
							echo ' &nbsp; next -&gt;';
						}		
					?>
				</td>
				<td align="center" width="20%">
					<a href="orders.php?oID=<?php echo $oID; ?>">
						<img class="navigation" src="includes/languages/english/images/buttons/button_back.gif" border="0" alt="IMAGE_ORDERS_BACK" title=" IMAGE_ORDERS_BACK ">
					</a>
				</td>
			</tr>
			<?php if ($num) : ?>
			<tr>
				<td colspan="3" style="text-align:center;">
					<?php echo '<a href="track_fedex.php?oID='.$oID.'&num='.$num.'&fedex_gateway=track">Track: ' . $num . '</a> &nbsp; '; ?>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<td colspan="3"><img src="images/pixel_trans.gif" border="0" alt="" width="1" height="20"></td>
			</tr>
		</table>
		<script language="JavaScript" type="text/javascript">
			document.write('<img style="width:' + (384 * resolution )/100 + 'px; height:' + (576 * resolution )/100 + 'px;" alt="ASTRA Barcode" src="<?php echo $label_filename; ?>">');
		</script>
		<?php endif; ?>
	</body>
</html>
