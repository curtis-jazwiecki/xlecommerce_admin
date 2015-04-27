<?php
require('includes/application_top.php');

$keyword = urldecode($_GET['keyword']);

$matches = array();
$matches_query = tep_db_query("select * from admin_functionality_search where keywords like '%" . tep_db_input($keyword) . "%' order by parent, name");
while($entry = tep_db_fetch_array($matches_query)){
	$parent = trim($entry['parent']);
	if (!array_key_exists($parent, $matches)) $matches[$parent] = array();
	
	$file_name = $entry['file'];
	$query_string = '';
	$pos = strpos($file_name, '?');
	if ($pos!==false){
		$file_name = substr($entry['file'], 0, $pos);
		$query_string = substr($entry['file'], $pos+1);
	}
	
	$matches[$parent][] = '<a href="' . tep_href_link($file_name, $query_string) . '" style="font-size:12px;color:black;">' . $entry['name'] . '</a>';
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
	<body style="margin:0">
	<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft_"></table></td>
				<td width="100%" valign="top">
					<table border="0" width="100%" cellspacing="0" cellpadding="2">
						<tr>
							<td>
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td class="pageHeading">Matches for "<?php echo $keyword; ?>"</td>
										<td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
						</tr>
						<tr>
							<td  style="background-color:white;">
							<?php if (empty($matches)) { ?>
								<div class="main" style="text-align:center;margin:50px 0 50px 0;color:red;"><b>No Match Located for: "<?php echo $keyword; ?>"</b></div>
							<?php } else { ?>
							<?php foreach($matches as $parent => $entries){ ?>
								<div class="main" style="margin:30px 0 10px 0"><b><?php echo $parent; ?></b></div>
								<?php foreach($entries as $entry) { ?>
								<div class="main" style="margin:5px 0 0 25px;;"><?php echo $entry; ?></div>
								<?php } ?>
							<?php } ?>
							<?php } ?>
							</td>
						</tr>
						<tr>
							<td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<br>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>