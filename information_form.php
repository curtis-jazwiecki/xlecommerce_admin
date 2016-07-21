<?PHP
  /*
  Module: Information Pages Unlimited
          File date: 2007/02/17
          Based on the FAQ script of adgrafics
          Adjusted by Joeri Stegeman (joeri210 at yahoo.com), The Netherlands

CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
  */
?>
<tr>
	<td><?php echo $title ?></td>
</tr>
<tr>
	<td>
	
<table class="table table-bordered table-hover">
<?php	
if(!strstr($info_group['locked'], 'visible')) {
?>
	<tr>
		<td><?php echo ENTRY_STATUS; ?></td>
		<td><?php echo tep_draw_radio_field('visible', '1', false, $edit['visible']) . '&nbsp;&nbsp;' . STATUS_ACTIVE . '&nbsp;&nbsp;' . tep_draw_radio_field('visible', '0', false, $edit['visible']) . '&nbsp;&nbsp;' . STATUS_INACTIVE; ?></td>
	</tr>
	<tr>
		<td colspan="2"></td>
	</tr>
<?php
}
?>
	<tr>
		<td>Hidden Page</td>
		<td><?php echo tep_draw_radio_field('is_hidden', '1', ($edit['is_hidden']=='1')) . '&nbsp;&nbsp;Yes&nbsp;&nbsp;' . tep_draw_radio_field('is_hidden', '0', ($edit['is_hidden']=='0')) . '&nbsp;&nbsp;No'; ?></td>
	</tr>
	<tr>
		<td colspan="2"></td>
	</tr>
<?php
if(!strstr($info_group['locked'], 'parent_id')) {
?>
	<tr>
		<td><?php echo ENTRY_PARENT_PAGE; ?></td>
		<td>
<?php
if ((sizeof($data) > 0) )
{
	$options = '<option value="NULL">-';
	reset($data);
	while (list($key, $val) = each($data)) 
	{
		$selected = ($val['information_id'] == $edit['parent_id']) ? 'selected' : '';
		$options .= '<option value="' . $val['information_id'] . '" ' . $selected . '>' . $val['information_title'];
	}
	echo '<select name="parent_id">' . $options . '</select>';
}
else echo '<span class="messageStackError">' . WARNING_PARENT_PAGE .'</span>';
?>
		</td>
	</tr>
	<tr>
		<td colspan="2"></td>
	</tr>
<?php
}

if(!strstr($info_group['locked'], 'sort_order')) {
?>
	<tr>
		<td><?php echo ENTRY_SORT_ORDER;?></td>
		<td><?php if ($edit[sort_order]) {$no=$edit[sort_order];}; echo tep_draw_input_field('sort_order', "$no", 'size=3 maxlength=4'); ?></td>
	</tr>
	<tr>
		<td colspan="2"></td>
	</tr>	
<?php
}
if(!strstr($info_group['locked'], 'information_title')) {
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
	<tr>
		<td><?php if ($i == 0) echo ENTRY_TITLE;?><br></td>
		<td><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('information_title[' . $languages[$i]['id'] . ']', (($languages[$i]['id'] == $languages_id) ? stripslashes($edit[information_title]) : tep_get_information_entry($information_id, $languages[$i]['id'], 'information_title')), 'maxlength=255'); ?></td>
	</tr>
	<tr>
		<td colspan="2"></td>
	</tr>
<?php
    }
}
?>
	<tr>
		<td>Title Tag</td>
		<td>
		<input type="text" style="width: 300px;" maxlength="255" value="<?php echo stripslashes($edit[information_meta_title]); ?>" name="information_meta_title">
		</td>
	</tr>
        <tr>
		<td>Description Meta Tag</td>
		<td>
		<input type="text" style="width: 600px;" maxlength="255" value="<?php echo stripslashes($edit[information_meta_tag]); ?>" name="information_meta_tag">
		</td>
	</tr>
	<tr>
		<td colspan="2"></td>
	</tr>
	<tr valign="top">
		<td>Keywords Meta Tag<br /><span>(Separate<br />by Commas)</span></td>
		<td>
		<input type="text" style="width: 600px;" maxlength="255" value="<?php echo stripslashes($edit[information_meta_tag_keywords]); ?>" name="information_meta_tag_keywords">
		</td>
	</tr>
	<tr>
		<td colspan="2"></td>
	</tr>
<?php
if(!strstr($info_group['locked'], 'information_description')) {
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
	<tr>
		<td colspan="2"><?php if ($i == 0) echo ENTRY_DESCRIPTION; ?><p />
		<table class="table table-bordered table-hover">
		<tr>
			<td>
			<?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>
            </td>
			<td>
			<?php 
			//echo tep_draw_textarea_field('information_description[' . $languages[$i]['id'] . ']', '', '119', '40', (($languages[$i]['id'] == $languages_id) ? stripslashes($edit[information_description]) : tep_get_information_entry($information_id, $languages[$i]['id'], 'information_description'))); 
			echo tep_draw_textarea_field('information_description[' . $languages[$i]['id'] . ']', '', '119', '40', (($languages[$i]['id'] == $languages_id) ? stripslashes($edit[information_description]) : tep_get_information_entry($information_id, $languages[$i]['id'], 'information_description')), 'class="ckeditor"'); 
			?>
            </td>
		</tr>
		</table>
	</tr>
<?php
    }
}
?>
	<tr>
		<td colspan="2" align="right"><?php 
				// Decide when to show the buttons (Determine or 'locked' is active)
				if( (!empty($info_group['locked'])) || ($information_action == 'Edit')) {
					echo tep_image_submit('button_insert_b.gif', IMAGE_INSERT);
				}
				echo '&nbsp;<a href="' . tep_href_link(FILENAME_INFORMATION_MANAGER, "gID=$gID", 'NONSSL') . '">' . tep_image_button('button_cancel_b.gif', IMAGE_CANCEL) . '</a>'; 
			?></td>
	</tr>
</table>


	</td>
</tr>
</form>