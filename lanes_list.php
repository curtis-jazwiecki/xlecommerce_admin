<?PHP
  /*
  Module: Information Pages Unlimited
          File date: 2007/02/17
          Based on the FAQ script of adgrafics
          Adjusted by Joeri Stegeman (joeri210 at yahoo.com), The Netherlands

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.  */
?>
<tr><td><?php echo $title ?></td></tr>
<script type="text/javascript">
    function range(rangeid)
    {
        //alert(rangeid);
        location.href = '<?php echo FILENAME_LANES_MANAGER;?>?ranges_id='+rangeid;
    }
</script>
<tr>
    <td>
        <select onchange="range(this.value)">
            <option>Select Range</option>
            <?php
            
                $range_query = tep_db_query("select * from " . TABLE_RANGES . " order by `ranges_name` asc ");
            	$c=0;
	           while ($rangelist = tep_db_fetch_array($range_query)) {
	               $sel='';
                   if($rangelist['ranges_id']==$_GET['ranges_id'])
                        $sel='selected="selected"';
	               echo'<option value="'.$rangelist['ranges_id'].'" '.$sel.'>'.$rangelist['ranges_name'].'</option>';
	           }
            ?>
        </select>
    </td>
</tr>
<tr><td>
<table class="table table-bordered table-hover">
<tr>
	<td align=center><?php echo ID_INFORMATION;?></td>
	<td align=center><?php echo ENTRY_TITLE;?></td>
	<td align=center><?php echo ENTRY_PARENT_PAGE;?></td>
	<td align=center><?php echo PUBLIC_INFORMATION;?></td>
	<td align=center><?php echo ENTRY_SORT_ORDER; ?></td>
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
    <td><?php echo $val['lanes_name'];?></td>
    <td align=center><?php echo $val['ranges_name'];?></td>
    <td align="center">
<?php
		if ($val['status'] == 1) {
			echo tep_image(DIR_WS_ICONS . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;';
			echo ( (!strstr($info_group['locked'], 'status')) ? '<a href="' . tep_href_link(FILENAME_LANES_MANAGER, "lane_action=status&lanes_id=$val[lanes_id]&status=$val[status]") . '">' : null);
			echo tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', DEACTIVATION_ID_INFORMATION, 10, 10);
			echo ( (!strstr($info_group['locked'], 'status')) ? '</a>' : null);
		}
		else {
			echo ( (!strstr($info_group['locked'], 'status')) ? '<a href="' . tep_href_link(FILENAME_LANES_MANAGER, "lane_action=status&lanes_id='$val[lanes_id]&status=$val[status]") . '">' : null);
			echo tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', ACTIVATION_ID_INFORMATION, 10, 10);
			echo ( (!strstr($info_group['locked'], 'status')) ? '</a>' : null);
			echo '&nbsp;' . tep_image(DIR_WS_ICONS . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
		}
?></td>
	<td align="center"><?php echo $val['sort_order'];?></td>
    <td align=center>
		<?php echo '<a href="' . tep_href_link(FILENAME_LANES_MANAGER, "lane_action=Edit&lanes_id=$val[lanes_id]", 'NONSSL') . '">' . tep_image(DIR_WS_ICONS . 'edit.gif', EDIT_ID_INFORMATION) . '</a>'; ?></td>
		<?php echo ( empty($info_group['locked']) ? '<td align=center class="dataTableContent"><a href="' . tep_href_link(FILENAME_LANES_MANAGER, "lane_action=Delete&lanes_id=$val[lanes_id]", 'NONSSL') . '">' . tep_image(DIR_WS_ICONS . 'delete.gif', DELETE_ID_INFORMATION) . '</a></td>' : null); ?>
   </tr>
<?php
		$no++;
	}
    
    $laneCount = tep_db_num_rows(tep_db_query("SELECT l.lanes_id as lanes_id, l.lanes_name as lanes_name, l.descr as descr , l.sort_order as sort_order, l.status as status,r.ranges_name as ranges_name FROM ".TABLE_LANES." as l,".TABLE_RANGES." as r where l.ranges_id = r.ranges_id $where"));
    //echo'<h1>'.$laneCount.'</h1>';
    $pagecount = intval($laneCount/$end);
    //echo'<h2>'.$pagecount.'</h2>';
    $paggination = '<ul>';
    for($i=0;$i<$pagecount;$i++)
    {
        $j = $i+1;
        $paggination .= '<li><a href="'.$_SERVER['REQUEST_URI'].'&page='.$i.'">'.$j.'</li>';    
    }
    $paggination .= '</ul>';
    echo'<tr><td align="center" colspan="7" style="text-align:center;font-weight:bold;">'. $paggination.'</td></tr>';
}
else {
?>
   <tr>
    <td colspan=7><?php echo ALERT_INFORMATION;?></td>
   </tr>
<?php
}
?>
</table>
</td></tr>
<tr><td align=right>
<?php 
	if( empty($info_group['locked']) ) {
		echo '<a href="' . tep_href_link(FILENAME_LANES_MANAGER, "lane_action=Added", 'NONSSL') . '">' . tep_image_button('button_new_b.gif', ADD_INFORMATION) . '</a> ';
	}
	//echo '<a href="' . tep_href_link(FILENAME_LANES_MANAGER, "", 'NONSSL') . '">' . tep_image_button('button_cancel_b.gif', IMAGE_CANCEL) . '</a>'; 
?>
<p style="float:left;"><a href="<?php echo FILENAME_RANGES_MANAGER;?>"><input type="button" value="Range Manager"></a></p>
</td></tr>