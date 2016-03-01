<?PHP
  /*
  Module: Information Pages Unlimited
          File date: 2007/02/17
          Based on the FAQ script of adgrafics
          Adjusted by Joeri Stegeman (joeri210 at yahoo.com), The Netherlands

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Released under the GNU General Public License
  */
?>
<tr><td><?php echo $title ?></td></tr>
<tr><td>
<table class="table table-bordered table-hover">
<tr>
	<td align=center><?php echo ID_INFORMATION;?></td>
	<td align=center><?php echo ENTRY_TITLE;?></td>
	<!--BOF RANGE LOGO 09-JAN-2014-->
	<td align=center>LOGO</td>
	<!--EOF RANGE LOGO 09-JAN-2014-->
    <td align=center><?php echo VIEW_LANES;?></td>
    <td align=center><?php echo CALCULATE_VIA;?></td>
   <?php /* <td align=center class="dataTableHeadingContent"><?php echo DAY_HOURS;?></td> */ ?>
	<td align=center><?php echo PUBLIC_INFORMATION;?></td>
	<td align=center colspan=2><?php echo ACTION_INFORMATION;?></td>
</tr>
<?php
$no=1;
if (sizeof($data) > 0) {
    $i=1;
	while (list($key, $val) = each($data)) {
		$no % 2 ? $bgcolor="#DEE4E8" : $bgcolor="#F0F1F1";
?>
  <tr bgcolor="<?php echo $bgcolor?>">
	<td align="center"><?php echo $i++;?></td>
    <td><?php echo $val['ranges_name'];?></td>
    <td><?php if($val['logo']) {?><img src="<?php echo RANGE_LOGO.$val['logo'];?>" width="100"  height="100"><?php } else echo ' ';?></td>
    <td><?php echo '<a style="text-decoration:underline;color:#000;" href="' . tep_href_link(FILENAME_LANES_MANAGER, "ranges_id=".$val['ranges_id'], 'NONSSL') . '">'.VIEW_LANES.'</a>';?></td>
    <td><?php echo $val['calculate_via'];?></td>
    <?php /*<td width="40%" class="dataTableContent"><?php echo $val['day_hours'];?></td> */ ?>
    
    <td nowrap  align="center">
<?php
		if ($val['status'] == 1) {
			echo tep_image(DIR_WS_ICONS . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;';
			echo ( (!strstr($info_group['locked'], 'status')) ? '<a href="' . tep_href_link(FILENAME_RANGES_MANAGER, "range_action=status&ranges_id=$val[ranges_id]&status=$val[status]") . '">' : null);
			echo tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', DEACTIVATION_ID_INFORMATION, 10, 10);
			echo ( (!strstr($info_group['locked'], 'status')) ? '</a>' : null);
		}
		else {
			echo ( (!strstr($info_group['locked'], 'status')) ? '<a href="' . tep_href_link(FILENAME_RANGES_MANAGER, "range_action=status&ranges_id=$val[ranges_id]&status=$val[status]") . '">' : null);
			echo tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', ACTIVATION_ID_INFORMATION, 10, 10);
			echo ( (!strstr($info_group['locked'], 'status')) ? '</a>' : null);
			echo '&nbsp;' . tep_image(DIR_WS_ICONS . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
		}
?></td>
	
    <td align=center>
		<?php echo '<a href="' . tep_href_link(FILENAME_RANGES_MANAGER, "range_action=Edit&ranges_id=$val[ranges_id]", 'NONSSL') . '">' . tep_image(DIR_WS_ICONS . 'edit.gif', EDIT_ID_INFORMATION) . '</a>'; ?></td>
		<?php echo ( empty($info_group['locked']) ? '<td align=center class="dataTableContent"><a href="' . tep_href_link(FILENAME_RANGES_MANAGER, "range_action=Delete&ranges_id=$val[ranges_id]", 'NONSSL') . '">' . tep_image(DIR_WS_ICONS . 'delete.gif', DELETE_ID_INFORMATION) . '</a></td>' : null); ?>
   </tr>
<?php
		$no++;
	}
}
else {
?>
   <tr>
    <td colspan="8"><?php echo ALERT_INFORMATION;?></td>
   </tr>
<?php
}
?>
</table>
</td></tr>
<tr><td align=right>
<?php 
	if( empty($info_group['locked']) ) {
		echo '<a href="' . tep_href_link(FILENAME_RANGES_MANAGER, "range_action=Added", 'NONSSL') . '">' . tep_image_button('button_new_b.gif', ADD_INFORMATION) . '</a> ';
	}
	//echo '<a href="' . tep_href_link(FILENAME_RANGES_MANAGER, "", 'NONSSL') . '">' . tep_image_button('button_cancel_b.gif', IMAGE_CANCEL) . '</a>'; 
?>
</td></tr>