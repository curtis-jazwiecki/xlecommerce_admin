<?php
/*
  $Id: stats_sales_report.php,v 0.01 2002/11/27 19:02:22 cwi Exp $
  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  // default view (daily)
  $sales_report_default_view = 2;
  // report views (1: hourly 2: daily 3: weekly 4: monthly 5: yearly)
  $sales_report_view = $sales_report_default_view;
  if ( ($HTTP_GET_VARS['report']) && (tep_not_null($HTTP_GET_VARS['report'])) )
    { $sales_report_view = $HTTP_GET_VARS['report']; }
  if ($sales_report_view > 5)
    { $sales_report_view = $sales_report_default_view; }
?>
<?php 
if ($sales_report_view == 2) {
$report = 2;
} 
if ($report == 1) {
$summary1 = AVERAGE_HOURLY_TOTAL;
$summary2 = TODAY_TO_DATE;
$report_desc = REPORT_TYPE_HOURLY;
} 
else 
if ($report == 2) {
$summary1 = AVERAGE_DAILY_TOTAL;
$summary2 = WEEK_TO_DATE;
$report_desc = REPORT_TYPE_DAILY;
} 
else 
{
if ($report == 3) {
$summary1 = AVERAGE_WEEKLY_TOTAL;
$summary2 = MONTH_TO_DATE;
$report_desc = REPORT_TYPE_WEEKLY;
} else {
if ($report == 4) {
$summary1 = AVERAGE_MONTHLY_TOTAL;
$summary2 = YEAR_TO_DATE;
$report_desc = REPORT_TYPE_MONTHLY;
} else {
if ($report == 5) {
$summary1 = AVERAGE_YEARLY_TOTAL;
$summary2 = YEARLY_TOTAL;
$report_desc = REPORT_TYPE_YEARLY;
}
}
}
}

  // check start and end Date
  $startDate = "";
  if ( ($HTTP_GET_VARS['startDate']) && (tep_not_null($HTTP_GET_VARS['startDate'])) ) {
    $startDate = $HTTP_GET_VARS['startDate'];
  }
  $endDate = "";
  if ( ($HTTP_GET_VARS['endDate']) && (tep_not_null($HTTP_GET_VARS['endDate'])) ) {
    $endDate = $HTTP_GET_VARS['endDate'];
  }

  require(DIR_WS_CLASSES . 'sales_report.php');
  $report = new sales_report($sales_report_view, $startDate, $endDate);

?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo DIR_WS_CLASSES . 'currencies.php'; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<SCRIPT LANGUAGE="JavaScript1.2" SRC="jsgraph/graph.js"></SCRIPT>
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
					<td colspan=2>
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td class="pageHeading"><?php echo $report_desc . ' ' . HEADING_TITLE; ?></td>
								<td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
							</tr>
						</table>
					</td>
				</tr>
			    <tr>
					<td colspan=2>
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
<td align=right class="link4">
<?php
  echo '<a href="' . tep_href_link(FILENAME_STATS_SALES_REPORT, 'report=1', 'NONSSL') . '">' . REPORT_TYPE_HOURLY .'</a>  ';
  echo '<a href="' . tep_href_link(FILENAME_STATS_SALES_REPORT, 'report=2', 'NONSSL') . '">' . REPORT_TYPE_DAILY .'</a>  ';
  echo '<a href="' . tep_href_link(FILENAME_STATS_SALES_REPORT, 'report=3', 'NONSSL') . '">' . REPORT_TYPE_WEEKLY . '</a>  ';
  echo '<a href="' . tep_href_link(FILENAME_STATS_SALES_REPORT, 'report=4', 'NONSSL') . '">' . REPORT_TYPE_MONTHLY . '</a>  ';
  echo '<a href="' . tep_href_link(FILENAME_STATS_SALES_REPORT, 'report=5', 'NONSSL') . '">' . REPORT_TYPE_YEARLY . '</a>  ';
?>
</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td valign=top width=200 align=center bgcolor="#DDDDDD">
<?php 
if ($sales_report_view > 1) {
if ($report->size > 1) {
  echo tep_draw_separator('pixel_trans.gif', 250,10).'<br>';
  $last_value = 0;
  $order_cnt = 0;
  $sum = 0;
  for ($i = 0; $i < $report->size; $i++) {
    if ($last_value != 0) {
      $percent = 100 * $report->info[$i]['sum'] / $last_value - 100;
    } else {
      $percent = "0";
    }
    $sum += $report->info[$i]['sum'];
    $avg += $report->info[$i]['avg'];
    $order_cnt += $report->info[$i]['count'];
    $last_value = $report->info[$i]['sum'];
}
}
//define variables for graph
if ($report->size > 1) {
$scale_x = ($sum / $report->size) / ($report->size);
$scale_y = $scale_x + 50;
$scale_z = $scale_y / 100;
$scale = round($scale_z) * 100;
?>
<SCRIPT LANGUAGE="JavaScript1.2">
var g = new Graph(<?php 
if ($report->size > 2){
echo '200';
} else {
echo ($report->size * 50);} ?>,100,true);
g.addRow(<?php
  for ($i = 0; $i < $report->size; $i++) {
if ($report->info[$i]['sum'] == ""){
	echo '0';
	}else{
	echo $report->info[$i]['sum'];
}
	  if (($i+1) < $report->size) {
		echo ',';
	  }
	}
	echo ');';
	echo '
	';
?>
<?php  if ($sales_report_view == 2){
echo 'g.addRow(';
  for ($i = 0; $i < $report->size; $i++) {
if ($report->info[$i]['sum'] == ""){
	echo '0';
	}else{
	echo $report->info[$i]['avg'];
}
	  if (($i+1) < $report->size) {
		echo ',';
	  }
	}
	echo ');';
	echo '
	';
echo 'g.setLegend("daily total","avg. order");';
	echo '
	';
}?>
<?php
	echo 'g.setXScaleValues("';
  for ($i = 0; $i < $report->size; $i++) {
  if ($sales_report_view == 4){
	echo substr($report->info[$i]['text'] . $date_text[$i], 0,3);
  }else{
	echo substr($report->info[$i]['text'] . $date_text[$i], 0,5);
  }
	  if (($i+1) < $report->size) {
		echo '","';
	  }
	}
	echo '");';
?>

g.scale = <?php echo $scale; ?>;
g.build();
</SCRIPT>
<?php
}
}
?>
					</td>
			        <td width=100% valign=top bgcolor="#dddddd">
						<table border="0" width="100%" cellspacing="0" cellpadding="2">
							<tr>
								<td valign="top">
									<table border="0" width="100%" cellspacing="0" cellpadding="2">
										<tr class="dataTableHeadingRow">
											<td class="dataTableHeadingContent"></td>
											<td class="dataTableHeadingContent" align=center><?php echo TABLE_HEADING_ORDERS; ?></td>
											<td class="dataTableHeadingContent" align=right><?php echo TABLE_HEADING_CONV_PER_ORDER; ?></td>
											<td class="dataTableHeadingContent" align=right><?php echo TABLE_HEADING_CONVERSION; ?></td>
											<td class="dataTableHeadingContent" align=right><?php echo TABLE_HEADING_VARIANCE; ?></td>
										</tr>
<?php

  $last_value = 0;
  $sum = 0;
  for ($i = 0; $i < $report->size; $i++) {
    if ($last_value != 0) {
      $percent = 100 * $report->info[$i]['sum'] / $last_value - 100;
    } else {
      $percent = "0";
    }
    $sum += $report->info[$i]['sum'];
    $avg += $report->info[$i]['avg'];
    $last_value = $report->info[$i]['sum'];
?>
										<tr class="dataTableRow" onMouseOver="this.className='dataTableRowOver';this.style.cursor='hand'" onMouseOut="this.className='dataTableRow'">
							                <td class="dataTableContent">
<?php
    if (strlen($report->info[$i]['link']) > 0 ) {
      echo '<span class="link3"><a href="' . tep_href_link(FILENAME_STATS_SALES_REPORT, $report->info[$i]['link'], 'NONSSL') . '">';
    }
    echo $report->info[$i]['text'] . $date_text[$i];
    if (strlen($report->info[$i]['link']) > 0 ) {
      echo '</a></span>';
    }
?></td>
											<td class="dataTableContent" align=center><?php echo $report->info[$i]['count']?></td>
											<td class="dataTableContent"align=right><?php echo $currencies->format($report->info[$i]['avg'])?></td>
											<td class="dataTableContent" align=right><?php echo $currencies->format($report->info[$i]['sum'])?></td>
											<td class="dataTableContent" align=right>
<?php
    if ($percent == 0){
      echo "---";
    } else {
      echo number_format($percent,0) . "%";
    }
?>
</td>
										</tr>
<?php
 }
?>

<?php
  if (strlen($report->previous . " " . $report->next) > 1) {
?>
										<tr>
											<td width=100% colspan=5>
												<table width=100%>
													<tr>
														<td align=left class="link3">
<?php
    if (strlen($report->previous) > 0) {
      echo '<a href="' . tep_href_link(FILENAME_STATS_SALES_REPORT, $report->previous, 'NONSSL') . '">&lt;&lt;&nbsp;Previous</a>';
    }
?>
														</td>
										                <td align=right class="link3">
<?php
    if (strlen($report->next) > 0) {
      echo '<a href="' . tep_href_link(FILENAME_STATS_SALES_REPORT, $report->next, 'NONSSL') . '">Next&nbsp;&gt;&gt;</a>';
      echo "";
    }
?>
														</td>
													</tr>
												</table>
											</td>
										</tr>
<?php
  }
?>

									</table>
									<p>
									<table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php if ($order_cnt != 0){
?>
										<tr class="dataTableRow">
											<td class="dataTableContent" width=100% align=right><?php echo '<b>'. AVERAGE_ORDER . ' </b>' ?></td>
											<td class="dataTableContent"align=right><?php echo $currencies->format($sum / $order_cnt) ?></td>
										</tr>
<?php } ?>
										<tr class="dataTableRow">
											<td class="dataTableContent" width=100% align=right><?php echo '<b>'. $summary1 . ' </b>' ?></td>
											<td class="dataTableContent"align=right><?php echo $currencies->format($sum / $report->size) ?></td>
										</tr>
										<tr class="dataTableRow">
											<td class="dataTableContent" width=100% align=right><?php echo '<b>'. $summary2 . ' </b>' ?></td>
											<td class="dataTableContent" align=right><?php echo $currencies->format($sum) ?></td>
										</tr>
									</table>
								</td>
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
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
