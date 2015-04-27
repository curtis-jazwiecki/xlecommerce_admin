<?php 
require('includes/application_top.php'); 

switch ($_GET['action']) {
	case 'insert':
		$start_date = '';
		if ($_POST['day'] && $_POST['month'] && $_POST['year']) {
			$start_date = $_POST['year'];
			$start_date .= (strlen($_POST['month']) == 1) ? '0' . $_POST['month'] : $_POST['month'];
			$start_date .= (strlen($_POST['day']) == 1) ? '0' . $POST['day'] : $_POST['day'];
		}
		tep_db_query("insert into product_for_the_day (products_id, start_date, date_added) values ('" . $_POST['products_id'] . "', '" . $start_date . "', now())");
		tep_redirect(tep_href_link('product_per_day.php', 'page=' . $_GET['page']));
		break;
    case 'update':
		$start_date = '';
		if ($_POST['day'] && $_POST['month'] && $_POST['year']) {
			$start_date = $_POST['year'];
			$start_date .= (strlen($_POST['month']) == 1) ? '0' . $_POST['month'] : $_POST['month'];
			$start_date .= (strlen($_POST['day']) == 1) ? '0' . $_POST['day'] : $_POST['day'];
		}

		tep_db_query("update product_for_the_day set last_modified = now(), start_date = '" . $start_date . "' where id = '" . $_POST['pod_id'] . "'");
		tep_redirect(tep_href_link('product_per_day.php', 'page=' . $HTTP_GET_VARS['page'] . '&podID=' . $_POST['pod_id']));
		break;
	case 'setflag':
		set_pod_item_status($_GET['id'], $_GET['flag']);
		tep_redirect(tep_href_link('product_per_day.php', '', 'NONSSL'));
      break;
    case 'deleteconfirm':
		$pod_id = tep_db_prepare_input($_GET['podID']);
		tep_db_query("delete from product_for_the_day where id = '" . (int)$pod_id . "'");
		tep_redirect(tep_href_link('product_per_day.php', 'page=' . $_GET['page']));
		break;
}

function set_pod_item_status($pod_id, $status) {
	if ($status == '1') {
		return tep_db_query("update product_for_the_day set status='1', last_modified=now() where id='" . (int)$_GET['id'] . "'");
	} elseif ($status == '0') {
		return tep_db_query("update product_for_the_day set status='0', last_modified=now() where id='" . (int)$_GET['id'] . "'");
    } else {
		return -1;
	}
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
		<title>Product Per Day Scheduler</title>
		<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
		<script language="javascript" src="includes/general.js"></script>
		<script language="javascript" src="includes/featured.js"></script>
		<?php if ( ($_GET['action'] == 'new') || ($_GET['action'] == 'edit') ) { ?>
		<link rel="stylesheet" type="text/css" href="includes/javascript/calendar.css">
		<script language="JavaScript" src="includes/javascript/calendarcode.js"></script>
		<?php } ?>
	</head>
	<body bgcolor="#FFFFFF" onload="SetFocus();" style="margin:0;">
		<div id="popupcalendar" class="text"></div>
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<table width="780" border="0" align="center" cellpadding="2" cellspacing="2">
			<tr>
				<td width="100%" valign="top">
					<table border="0" width="100%" cellspacing="0" cellpadding="2">
						<tr>
							<td class="pageHeading">Product Per Day Scheduler</td>
							<td class="pageHeading" align="right">
							<?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php 
			if ( ($_GET['action'] == 'new') || ($_GET['action'] == 'edit') ) { 
				$form_action = 'insert';
				if ( ($_GET['action'] == 'edit') && ($_GET['podID']) ) {
					$form_action = 'update';
					$pod_query = tep_db_query("select pftd.id, pftd.products_id, pftd.start_date, pftd.status, pd.products_name from product_for_the_day pftd inner join products p on pftd.products_id=p.products_id inner join products_description pd on  (p.products_id=pd.products_id and pd.language_id='1') where pftd.id='" . (int)$_GET['podID'] . "'");
					$pod = tep_db_fetch_array($pod_query);

					$podInfo = new objectInfo($pod);
				} else {
					$podInfo = new objectInfo(array());
				}
			?>
			<tr>
				<td>
					<form name="new_feature" <?php echo 'action="' . tep_href_link('product_per_day.php', tep_get_all_get_params(array('action', 'info', 'podID')) . 'action=' . $form_action, 'NONSSL') . '"'; ?> method="post">
					<?php if ($form_action == 'update'){
						echo tep_draw_hidden_field('pod_id', $_GET['podID']); 
					}
					?>
						<table border="0" cellspacing="0" cellpadding="2">
							<tr>
								<td class="main" valign="top" style="color:#FFFFFF">Product for Day</td>
								<td class="main" style="color:#FFFFFF">            	
								<?php 
								echo (($podInfo->products_name) 
								? $podInfo->products_name 
								: '<div id="div_listing"></div><span id="span_loader" style="display:none;">' . tep_image('images/ajax-loader.gif') . '</span>');
								echo tep_draw_hidden_field('products_id');
								echo tep_draw_hidden_field('products_price', $sInfo->products_price);
								?>
								</td>
							</tr>
							<tr>
								<td class="main" style="color:#FFFFFF">Start Day</td>
								<td class="main">
								<?php echo tep_draw_input_field('day', substr($podInfo->start_date, 8, 2), 'size="2" maxlength="2" class="cal-TextBox"') . tep_draw_input_field('month', substr($podInfo->start_date, 5, 2), 'size="2" maxlength="2" class="cal-TextBox"') . tep_draw_input_field('year', substr($podInfo->start_date, 0, 4), 'size="4" maxlength="4" class="cal-TextBox"'); ?><a class="so-BtnLink" href="javascript:calClick();return false;" onMouseOver="calSwapImg('BTN_date', 'img_Date_OVER',true);" onMouseOut="calSwapImg('BTN_date', 'img_Date_UP',true);" onClick="calSwapImg('BTN_date', 'img_Date_DOWN');showCalendar('new_feature','dteWhen','BTN_date');return false;"><?php echo tep_image(DIR_WS_IMAGES . 'cal_date_up.gif', 'Calendar', '22', '17', 'align="absmiddle" name="BTN_date"'); ?></a>
								</td>
							</tr>
							<tr>
								<td class="main" align="right" valign="top" colspan="2">
								<br>
								<?php 
								echo (($form_action == 'insert') 
								? tep_image_submit('button_insert_b.gif', IMAGE_INSERT) 
								: tep_image_submit('button_update_b.gif', IMAGE_UPDATE))
								. '&nbsp;&nbsp;&nbsp;<a href="' 
								. tep_href_link('product_per_day.php', 'page=' . $_GET['page'] . '&podID=' . $_GET['podID']) 
								. '">' . 
								tep_image_button('button_cancel_b.gif', IMAGE_CANCEL) 
								. '</a>'; 
								?>
								</td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
			<?php 
			} else { 
			?>
			<tr>
				<td>
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top">
								<table border="0" width="100%" cellspacing="0" cellpadding="2">
									<tr class="dataTableHeadingRow">
										<td class="dataTableHeadingContent">Show Date</td>
										<td class="dataTableHeadingContent">Products</td>
										<td class="dataTableHeadingContent" align="right">Status</td>
										<td class="dataTableHeadingContent" align="right">Action&nbsp;</td>
									</tr>
									<?php
									$listing_query_raw = "select pftd.id, pftd.products_id, pftd.start_date, pftd.status, pftd.date_added, pftd.last_modified, pd.products_name from product_for_the_day pftd inner join products p on pftd.products_id=p.products_id inner join products_description pd on  (p.products_id=pd.products_id and pd.language_id='1') order by pftd.start_date desc";
									$listing_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $listing_query_raw, $listing_query_numrows);
									$listing_query = tep_db_query($listing_query_raw);
									while ($entry = tep_db_fetch_array($listing_query)) {
										if ( ((!$HTTP_GET_VARS['podID']) || ($HTTP_GET_VARS['podID'] == $entry['id'])) && (!$podInfo) ) {
											$podInfo = new objectInfo($entry);
										}
										
										if ( (is_object($podInfo)) && ($entry['id'] == $podInfo->id) ) {
									?>
									<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\''<?php echo tep_href_link('product_per_day.php', 'page=' . $HTTP_GET_VARS['page'] . '&podID=' . $entry->featured_id . '&action=edit'); ?>'\'">
										<?php } else { ?>
									<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\''<?php echo tep_href_link(FILENAME_FEATURED, 'page=' . $HTTP_GET_VARS['page'] . '&sID=' . $featured['featured_id']); ?>'\'">
									<?php } ?>
										<td  class="dataTableContent">
										<b><?php echo $entry['start_date']; ?></b>
										</td>
										<td  class="dataTableContent"><?php echo $entry['products_name']; ?></td>
										<td  class="dataTableContent" align="right">
										<?php
										if ($entry['status'] == '1') {
											echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link('product_per_day.php', 'action=setflag&flag=0&id=' . $entry['id'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
										} else {
											echo '<a href="' . tep_href_link('product_per_day.php', 'action=setflag&flag=1&id=' . $entry['id'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
										}
										?>
										</td>
										<td class="dataTableContent" align="right">
										<?php 
										if ( (is_object($podInfo)) && ($entry['id'] == $podInfo->id) ) { 
											echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); 
										} else { 
											echo '<a href="' . tep_href_link('product_per_day.php', 'page=' . $HTTP_GET_VARS['page'] . '&podID=' . $entry['id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } 
										?>&nbsp;
										</td>
									</tr>
									<?php } ?>
									<tr>
										<td colspan="4">
											<table border="0" width="100%" cellpadding="0"cellspacing="2">
												<tr>
													<td class="smallText2" valign="top">
													<?php echo $listing_split->display_count($listing_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], 'Displaying %d to %d (of %d products)'); ?>
													</td>
													<td class="smallText2" align="right">
													<?php echo $listing_split->display_links($listing_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?>
													</td>
												</tr>
												<?php if (!$HTTP_GET_VARS['action']) { ?>
												<tr>
													<td colspan="2" align="right">
													<?php echo '<a href="' . tep_href_link('product_per_day.php', 'page=' . $HTTP_GET_VARS['page'] . '&action=new') . '">' . tep_image_button('button_new_product.gif', IMAGE_NEW_PRODUCT) . '</a>'; ?>
													</td>
												</tr>
												<?php } ?>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<?php
							$heading = array();
							$contents = array();
							switch ($_GET['action']) {
								case 'delete':
									$heading[] = array('text' => '<b>Delete \'Product of the day\'</b>');

									$contents = array(
										'form' => tep_draw_form('pod', 'product_per_day.php', 'page=' . $_GET['page'] . '&podID=' . $podInfo->id . '&action=deleteconfirm')
									);
									$contents[] = array(
										'text' => 'This action will remove item!'
									);
									$contents[] = array(
										'text' => '<br><b>' . $podInfo->products_name . '</b>'
									);
									$contents[] = array(
										'align' => 'center', 
										'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . '&nbsp;<a href="' . tep_href_link('product_per_day.php', 'page=' . $_GET['page'] . '&podID=' . $podInfo->id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
									break;
								default:
									if (is_object($podInfo)) {
										$heading[] = array(
											'text' => '<b>' . $sInfo->products_name . '</b>'
										);

										$contents[] = array(
											'align' => 'center', 
											'text' => '<a href="' . tep_href_link('product_per_day.php', 'page=' . $_GET['page'] . '&podID=' . $podInfo->id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link('product_per_day.php', 'page=' . $HTTP_GET_VARS['page'] . '&podID=' . $podInfo->id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>'
										);
										$contents[] = array(
											'text' => '<br>Product added on ' . tep_date_short($podInfo->date_added)
										);
										$contents[] = array(
											'text' => 'Last Modified on ' . tep_date_short($podInfo->last_modified)
										);

										$contents[] = array(
											'text' => '<br>Start Date <b>' . tep_date_short($podInfo->start_date) . '</b>'
										);
									}
									break;
								}
								if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
								?>
								<td width="25%" valign="top">
								<?php
								$box = new box;
								echo $box->infoBox($heading, $contents);
								?>
								</td>
								<?php
								}
							?>
						</tr>
					</table>
				</td>
			</tr>
			<?php 
			} 
			?>
		</table>
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<script>
		<?php echo ($_GET['action']=='new' ? 'displaySelection(\'div_listing\', \'C0\', \'F\');' : ''); ?>
		</script>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>