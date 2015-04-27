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
<tr>
	<td class="pageHeading"><?php echo $title ?></td>
</tr>
<tr>
	<td>
	
<table border="0" cellpadding="0" cellspacing="2">
<?php	
	//echo'<pre>';
	//print_r($edit); 
if(!strstr($info_group['locked'], 'status')) {
?>
	<tr>
		<td class="main" style="color:#FFFFFF"><?php echo ENTRY_STATUS; ?></td>
		<td class="main" style="color:#FFFFFF">
		<?php 
		
		$t = true;
		$f = false;
		
		if(isset($edit['status']) && $edit['status']==0)
		{
			$t = false;
			$f = true;
		}
		echo tep_draw_radio_field('status', '1', $t, $edit['status']) . '&nbsp;&nbsp;' . STATUS_ACTIVE . '&nbsp;&nbsp;' . tep_draw_radio_field('status', '0', $f, $edit['status']) . '&nbsp;&nbsp;' . STATUS_INACTIVE; 
		?>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="10"></td>
	</tr>
<?php
}


?>
	<tr>
		<td class="main" style="color:#FFFFFF"><?php if ($i == 0) echo ENTRY_TITLE;?><br></td>
		<td style="color:#FFFFFF"><?php echo tep_draw_input_field('lanes_name', stripslashes($edit[lanes_name]), 'maxlength=255'); ?></td>
	</tr>
	<tr>
		<td colspan="2" height="10"></td>
	</tr>
	
	<tr>
		<td class="main" style="color:#FFFFFF"><?php if ($i == 0) echo ENTRY_PARENT_PAGE;?><br></td>
		<td style="color:#FFFFFF">
			<select name="ranges_id">
			<option value="0">Select Range</option>
				<?php
					$rangeQry = tep_db_query("SELECT `ranges_id` , `ranges_name` FROM `".TABLE_RANGES."` WHERE `status` = '1'");
					while($rangeArr = tep_db_fetch_array($rangeQry))
					{
						$sel='';
						if($edit['ranges_id']==$rangeArr['ranges_id'])
							$sel = 'selected="selected"';
						echo '<option value="'.$rangeArr['ranges_id'].'" '.$sel.'>'.$rangeArr['ranges_name'].'</option>';
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="10"></td>
	</tr>
	
	<tr>
		<td class="main" style="color:#FFFFFF"><?php if ($i == 0) echo ENTRY_SORT_ORDER;?><br></td>
		<td style="color:#FFFFFF"><?php echo tep_draw_input_field('sort_order', stripslashes($edit[sort_order]), 'maxlength=255'); ?></td>
	</tr>
	<tr>
		<td colspan="2" height="10"></td>
	</tr>

	
<?php
/*
if(!strstr($info_group['locked'], 'information_description')) {
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
	<tr>
		<td valign="top" class="main" width="100" colspan="2" style="color:#FFFFFF"><?php if ($i == 0) echo ENTRY_DESCRIPTION; ?><p />
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td class="main" valign="top" style="color:#FFFFFF">
			<?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>
            </td>
			<td class="main" valign="top">
			<?php echo tep_draw_textarea_field('descr', '', '119', '40', stripslashes($edit[descr])); ?>
            </td>
		</tr>
		</table>
	</tr>
<?php
    }
} */
?>
	<tr>
		<td colspan="2" align="right"><?php 
				// Decide when to show the buttons (Determine or 'locked' is active)
				if( (empty($info_group['locked'])) || ($lane_action == 'Edit')) {
					echo tep_image_submit('button_insert_b.gif', IMAGE_INSERT);
				}
				echo '&nbsp;<a href="' . tep_href_link(FILENAME_LANES_MANAGER, "", 'NONSSL') . '">' . tep_image_button('button_cancel_b.gif', IMAGE_CANCEL) . '</a>'; 
			?></td>
	</tr>
</table>


	</td>
</tr>
</form>