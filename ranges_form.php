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
//BOF RANGE LOGO 09-JAN-2014  
 
 if(isset($_GET['delogo']) && isset($_GET['logonam'])) 
 {
	@unlink(RANGE_LOGO.base64_decode($_GET['logonam']));
	$updatelogo = "UPDATE `ranges` SET `logo` = '' WHERE `ranges_id` = '".$_GET['delogo']."'";
	tep_db_query($updatelogo);
 }
 //EOF RANGE LOGO 09-JAN-2014  
?>
<tr>
	<td><?php echo $title ?></td>
</tr>
<tr>
	<td>
	
<table class="table table-bordered table-hover">
<?php	
if(!strstr($info_group['locked'], 'status')) {
?>
	<tr>
		<td><?php echo ENTRY_STATUS; ?></td>
		<td><?php echo tep_draw_radio_field('status', '1', false, $edit['status']) . '&nbsp;&nbsp;' . STATUS_ACTIVE . '&nbsp;&nbsp;' . tep_draw_radio_field('status', '0', false, $edit['status']) . '&nbsp;&nbsp;' . STATUS_INACTIVE; ?></td>
	</tr>
	<tr>
		<td colspan="2" height="10"></td>
	</tr>
<?php
}


?>
<tr>
		<td><?php if ($i == 0) echo CALCULATE_VIA;?><br></td>
		<td>
            <select name="calculate_via">
                <?php
                    
                    if($edit[calculate_via]) {
                        $sel_10_minute = 'selected="selected"';    
                    }
                ?>
                <option value="second" <?php if($edit[calculate_via]=='second') echo 'selected="selected"';?> >Second</option>
                <option value="minute" <?php if($edit[calculate_via]=='minute') echo 'selected="selected"';?>>Minute</option>
                <option value="5_minute" <?php if($edit[calculate_via]=='5_minute') echo 'selected="selected"';?>>5 Minutes</option>
                <option value="10_minute" <?php if($edit[calculate_via]=='10_minute') echo 'selected="selected"';?><?php echo $sel_10_minute;?>>10 Minutes</option>
                <option value="15_minute" <?php if($edit[calculate_via]=='15_minute') echo 'selected="selected"';?>>15 Minutes</option>
                <option value="hour" <?php if($edit[calculate_via]=='hour') echo 'selected="selected"';?>>Hour</option>
                <option value="day" <?php if($edit[calculate_via]=='day') echo 'selected="selected"';?>>Day</option>
            </select>
        </td>
	</tr>
	<tr>
		<td colspan="2"></td>
	</tr>
    <input type="hidden" name="day_hours" value="0">
    <?php /*
    <tr>
		<td class="main" style="color:#FFFFFF"><?php if ($i == 0) echo DAY_HOURS;?><br></td>
		<td style="color:#FFFFFF">
            <input type="text" name="day_hours" value="<?php echo $edit['day_hours'];?>">
               
        </td>
	</tr> */ ?>
	<!--BOF RANGE LOGO 09-JAN-2014-->
	<tr>
		<td colspan="2" height="10"></td>
	</tr>
	<tr>
		<td>Range Logo</td>
		<td><input type="hidden" name="logoname" value="<?php echo $edit[logo]; ?>"><input type="file" name="logo" value="<?php echo $edit[logo];?>"><img src="<?php echo RANGE_LOGO.$edit[logo]?>">
		<?php
		if($edit[ranges_id])
		{
		?>
		<a href="<?php echo $_SERVER['REQUEST_URI'];?>&delogo=<?php echo $edit[ranges_id];?>&logonam=<?php echo $edit[logo];?>">Delete logo</a>
		<?php } ?>
		</td>
	</tr>
	<!--BOF RANGE LOGO 09-JAN-2014-->
	<tr>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td><?php if ($i == 0) echo ENTRY_TITLE;?><br></td>
		<td style="color:#FFFFFF"><?php echo tep_draw_input_field('ranges_name', stripslashes($edit[ranges_name]), 'maxlength=255'); ?></td>
	</tr>
	<tr>
		<td colspan="2"></td>
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
				if( (empty($info_group['locked'])) || ($information_action == 'Edit')) {
					echo tep_image_submit('button_insert_b.gif', IMAGE_INSERT);
				}
				echo '&nbsp;<a href="' . tep_href_link(FILENAME_RANGES_MANAGER, "", 'NONSSL') . '">' . tep_image_button('button_cancel_b.gif', IMAGE_CANCEL) . '</a>'; 
			?></td>
	</tr>
</table>


	</td>
</tr>
</form>