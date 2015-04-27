<?php
require('includes/application_top.php');

$action = $_POST['action'];

switch($action){
	case 'add':
		$category_id = (int)$_POST['category_id'];
		$group_id = (int)$_POST['group_id'];
		$discount = (float)$_POST['discount'];
		
		$response = array(
			'category_id' => $category_id, 
			'group_id' => $group_id, 
		);
		
		if (!empty($group_id)){
			tep_db_query("insert into categories_groups (customers_group_id, customers_group_discount, categories_id) values ('" . $group_id . "', '" . $discount . "', '" . $category_id . "') on duplicate key update customers_group_discount='" . $discount . "' ");
		} else {
			$groups_query = tep_db_query("select customers_group_id from customers_groups order by customers_group_name");
			while ($entry = tep_db_fetch_array($groups_query)){
				tep_db_query("insert into categories_groups (customers_group_id, customers_group_discount, categories_id) values ('" . $entry['customers_group_id'] . "', '" . $discount . "', '" . $category_id . "') on duplicate key update customers_group_discount='" . $discount . "' ");
			}
		}
		echo json_encode($response);
		exit;
		break;
	case 'remove':
		$category_id = (int)$_POST['category_id'];
		$group_id = (int)$_POST['group_id'];
		
		$response = array(
			'category_id' => $category_id, 
			'group_id' => $group_id, 
			'error' => '', 
		);
		
		tep_db_query("delete from categories_groups where customers_group_id='" . $group_id . "' and categories_id='" . $category_id . "'");
		
		echo json_encode($response);
		exit;
		break;
	case 'update_category':
		$old_category_id = (int)$_POST['old_category_id'];
		$new_category_id = (int)$_POST['new_category_id'];
		$group_id = (int)$_POST['group_id'];
		
		$response = array(
			'old_category_id' => $old_category_id, 
			'new_category_id' => $new_category_id, 
			'group_id' => $group_id, 
			'error' => '', 
			'new_category_name' => '', 
		);
		
		$check_query = tep_db_query("select categories_id from categories_groups where customers_group_id='" . $group_id . "' and categories_id='" . $new_category_id . "'");
		if (tep_db_num_rows($check_query)){
			$response['error'] = 'Customer Group and category Mapping Already exists!';
		} else {
			tep_db_query("update categories_groups set categories_id='" . $new_category_id . "' where customers_group_id='" . $group_id . "' and categories_id='" . $old_category_id . "'");
			
			$category_query = tep_db_query("select categories_name from categories_description where categories_id='" . $new_category_id . "'");
			$info = tep_db_fetch_array($category_query);
			$response['new_category_name'] = $info['categories_name'];
		}
		echo json_encode($response);
		exit;
		break;
	case 'update_group':
		$old_group_id = (int)$_POST['old_group_id'];
		$new_group_id = (int)$_POST['new_group_id'];
		$category_id = (int)$_POST['category_id'];
		
		$response = array(
			'old_group_id' => $old_group_id, 
			'new_group_id' => $new_group_id, 
			'category_id' => $category_id, 
			'error' => '', 
			'new_group_name' => '', 
		);
		
		$check_query = tep_db_query("select customers_group_id from categories_groups where customers_group_id='" . $new_group_id . "' and categories_id='" . $category_id . "'");
		if (tep_db_num_rows($check_query)){
			$response['error'] = 'Customer Group and category Mapping Already exists!';
		} else {
			tep_db_query("update categories_groups set customers_group_id='" . $new_group_id . "' where customers_group_id='" . $old_group_id . "' and categories_id='" . $category_id . "'");
			
			$group_query = tep_db_query("select customers_group_name from customers_groups where customers_group_id='" . $new_group_id . "'");
			$info = tep_db_fetch_array($group_query);
			$response['new_group_name'] = $info['customers_group_name'];
		}
		echo json_encode($response);
		exit;
		break;
	case 'update_discount':
		$category_id = (int)$_POST['category_id'];
		$group_id = (int)$_POST['group_id'];
		$new_discount = (float)$_POST['new_discount'];
		$response = array(
			'category_id' => $category_id, 
			'group_id' => $group_id, 
			'new_discount' => $new_discount, 
			'error' => '', 
		);
		tep_db_query("update categories_groups set customers_group_discount='" . $new_discount . "' where customers_group_id='" . $group_id . "' and categories_id='" . $category_id . "'");
		echo json_encode($response);
		exit;
		break;
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
	<body style="margin:0;">
	<!-- header //-->
	<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
	<!-- header_eof //-->

	<!-- body //-->
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td width="<?php echo BOX_WIDTH; ?>" valign="top">
					<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft_">
					<!-- left_navigation //-->
					<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					<!-- left_navigation_eof //-->
					</table>
				</td>
				<!-- body_text //-->
				<td width="100%" valign="top">
					<table border="0" width="100%" cellspacing="0" cellpadding="2">
						<tr>
							<td>
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td class="pageHeading">Customer Groups to Categories</td>
										<td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
						</tr>
						<tr>
							<td width="100%" valign="top" style="background-color:white;">
							<?php
							function getCustomerGroups($skip_all_option = false){
								$response = array();
								if (!$skip_all_option){
									$response[] = array(
										'id' => '', 
										'text' => 'Apply to All',
									);
								}
								$groups_query = tep_db_query("select customers_group_id, customers_group_name from customers_groups order by customers_group_name");
								while ($entry = tep_db_fetch_array($groups_query)){
									$response[] = array(
										'id' => $entry['customers_group_id'], 
										'text' => $entry['customers_group_name'],
									);
								}
								return $response;
							}
							?>
								<style>
									.ui-icon, .modifiable {
										cursor:pointer;
									}
								</style>
								<script>
									var img_loader = new Image();
									img_loader.src = '<?php echo DIR_WS_ICONS?>ajax-loader-small.gif';
										
									jQuery(function(){
										jQuery('table#panel').css('display', 'none');
										
										jQuery(document)
											.keyup(function(event){
												if (event.keyCode==27){
													removeOpenEdits();
												}
											})
											.on('click', 'span#refresh', function(){
												location.href = 'customer_groups_to_categories.php';
											})
											.on('click', 'span#hide, button#cancel', function(){
												removeOpenEdits();
												
												jQuery('table#panel').css('display', 'none');
												jQuery('span#hide').attr({
													'id': 'show', 
													'class': 'ui-icon ui-icon-circle-plus', 
													'title': 'New Entry'
												});
												jQuery('select[name="category"]').val('0');
												jQuery('select[name="group"]').val('');
												jQuery('input[name="discount"]').val('');
											})
											.on('click', 'span#show', function(){
												removeOpenEdits()
												
												jQuery('table#panel').css('display', '');
												jQuery(this).attr({
													'id': 'hide', 
													'class': 'ui-icon ui-icon-circle-minus', 
													'title': 'Hide Panel'
												});
											})
											.on('click', 'button#add', function(){
												removeOpenEdits()
												
												jQuery.ajax({
													url: 'customer_groups_to_categories.php', 
													method: 'post', 
													data: {
														action: 'add', 
														category_id: jQuery('select[name="category"]').val(), 
														group_id: jQuery('select[name="group"]').val(), 
														discount: jQuery('input[name="discount"]').val()
													}, 
													success: function(response){
														location.href = 'customer_groups_to_categories.php';
													}
												});
											})
											.on('click', 'span#remove', function(){
												removeOpenEdits()
												
												if (confirm('This action will remove entry!')){
													jQuery(this).parent().parent().find('td').each(function(){
														jQuery(this).css('text-decoration', 'line-through');
													});
													
													jQuery.ajax({
														url: 'customer_groups_to_categories.php', 
														method: 'post', 
														dataType: 'json', 
														data: {
															action: 'remove', 
															category_id: jQuery(this).attr('category_id'), 
															group_id: jQuery(this).attr('group_id') 
														}, 
														success: function(response){
															jQuery('span#remove[group_id="' + response.group_id + '"][category_id="' + response.category_id + '"]').css('display', 'none');
															jQuery('span#category[group_id="' + response.group_id + '"][category_id="' + response.category_id + '"]').attr('deleted', '1');
															jQuery('span#group[group_id="' + response.group_id + '"][category_id="' + response.category_id + '"]').attr('deleted', '1');
															jQuery('span#discount[group_id="' + response.group_id + '"][category_id="' + response.category_id + '"]').attr('deleted', '1');
														}
													});
												}
											})
											.on('click', 'span#category', function(){
												removeOpenEdits();
												
												if (jQuery(this).attr('deleted') && jQuery(this).attr('deleted')=='1') return false;
												
												jQuery(this)
												.css('display', 'none')
												.parent()
												.find('span#category_container')
												.html(jQuery('div#category_content').html())
												.css('display', '')
												.find('select')
												.focus()
												.val(jQuery(this).attr('category_id'))
												.change(function(){
													new_category_id = jQuery(this).val();
													
													/*jQuery(this)
													.css('display', 'none')
													.parent()
													.append(jQuery('div#loader_content').html());*/
													
													jQuery.ajax({
														url: 'customer_groups_to_categories.php', 
														method: 'post', 
														dataType: 'json',
														data: {
															action: 'update_category', 
															old_category_id: jQuery(this).parent().attr('category_id'), 
															new_category_id: new_category_id,
															group_id: jQuery(this).parent().attr('group_id') 
														}, 
														success: function(response){
															if (response.error!=''){
																alert(response.error);
															} else {
																updateCategoryTo(response.group_id, response.old_category_id, response.new_category_id, response.new_category_name);
																removeOpenEdits();
															}
														}
													});
												});
												
											})
											.on('click', 'span#group', function(){
												removeOpenEdits();
												
												if (jQuery(this).attr('deleted') && jQuery(this).attr('deleted')=='1') return false;
												
												jQuery(this)
												.css('display', 'none')
												.parent()
												.find('span#group_container')
												.html(jQuery('div#group_content').html())
												.css('display', '')
												.find('select')
												.focus()
												.val(jQuery(this).attr('group_id'))
												.change(function(){
													jQuery.ajax({
														url: 'customer_groups_to_categories.php', 
														method: 'post', 
														dataType: 'json',
														data: {
															action: 'update_group', 
															old_group_id: jQuery(this).parent().attr('group_id'), 
															new_group_id: jQuery(this).val(),
															category_id: jQuery(this).parent().attr('category_id') 
														}, 
														success: function(response){
															if (response.error!=''){
																alert(response.error);
															} else {
																updateGroupTo(response.category_id, response.old_group_id, response.new_group_id, response.new_group_name);
																removeOpenEdits();
															}
														}
													});
												});
												
											})
											.on('click', 'span#discount', function(){
												removeOpenEdits();
												
												if (jQuery(this).attr('deleted') && jQuery(this).attr('deleted')=='1') return false;
												
												jQuery(this)
												.css('display', 'none')
												.parent()
												.find('span#discount_container')
												.html(jQuery('div#discount_content').html())
												.css('display', '')
												.find('input')
												.focus()
												.val(jQuery(this).html())
												.keypress(function(event){
													if (event.keyCode==13){
														jQuery.ajax({
															url: 'customer_groups_to_categories.php', 
															method: 'post', 
															dataType: 'json',
															data: {
																action: 'update_discount', 
																category_id: jQuery(this).parent().attr('category_id'), 
																group_id: jQuery(this).parent().attr('group_id'), 
																new_discount: jQuery(this).val()
															}, 
															success: function(response){
																if (response.error!=''){
																	alert(response.error);
																} else {
																	updateDiscountTo(response.category_id, response.group_id, response.new_discount);
																	removeOpenEdits();
																}
															}
														});
													}
												});
											})

									});
									
									function removeOpenEdits(){
										jQuery('span#category_container').html('').css('display', 'none');
										jQuery('span#group_container').html('').css('display', 'none');
										jQuery('span#discount_container').html('').css('display', 'none');
										jQuery('span#category').css('display', '');
										jQuery('span#group').css('display', '');
										jQuery('span#discount').css('display', '');
									}
									
									function updateCategoryTo(group_id, old_category_id, new_category_id, new_category_name){
										jQuery('span#category_container[group_id="' + group_id + '"][category_id="' + old_category_id + '"]').attr('category_id', new_category_id);
										jQuery('span#category[group_id="' + group_id + '"][category_id="' + old_category_id + '"]').attr('category_id', new_category_id);
										jQuery('span#group_container[group_id="' + group_id + '"][category_id="' + old_category_id + '"]').attr('category_id', new_category_id);
										jQuery('span#group[group_id="' + group_id + '"][category_id="' + old_category_id + '"]').attr('category_id', new_category_id);
										jQuery('span#discount_container[group_id="' + group_id + '"][category_id="' + old_category_id + '"]').attr('category_id', new_category_id);
										jQuery('span#discount[group_id="' + group_id + '"][category_id="' + old_category_id + '"]').attr('category_id', new_category_id);
										jQuery('span#remove[group_id="' + group_id + '"][category_id="' + old_category_id + '"]').attr('category_id', new_category_id);
										
										jQuery('span#category[group_id="' + group_id + '"][category_id="' + new_category_id + '"]').html(new_category_name);
									}
									
									function updateGroupTo(category_id, old_group_id, new_group_id, new_group_name){
										jQuery('span#category_container[group_id="' + old_group_id + '"][category_id="' + category_id + '"]').attr('group_id', new_group_id);
										jQuery('span#category[group_id="' + old_group_id + '"][category_id="' + category_id + '"]').attr('group_id', new_group_id);
										jQuery('span#group_container[group_id="' + old_group_id + '"][category_id="' + category_id + '"]').attr('group_id', new_group_id);
										jQuery('span#group[group_id="' + old_group_id + '"][category_id="' + category_id + '"]').attr('group_id', new_group_id);
										jQuery('span#discount_container[group_id="' + old_group_id + '"][category_id="' + category_id + '"]').attr('group_id', new_group_id);
										jQuery('span#discount[group_id="' + old_group_id + '"][category_id="' + category_id + '"]').attr('group_id', new_group_id);
										jQuery('span#remove[group_id="' + old_group_id + '"][category_id="' + category_id + '"]').attr('group_id', new_group_id);
										
										jQuery('span#group[group_id="' + new_group_id + '"][category_id="' + category_id + '"]').html(new_group_name);
									}
									
									function updateDiscountTo(category_id, group_id, new_discount){
										jQuery('span#discount[group_id="' + group_id + '"][category_id="' + category_id + '"]').html(new_discount);
									}
								</script>
								<table border="0" width="100%" cellspacing="0" cellpadding="2">
									<tr>
										<td>
											<table border="0" width="100%" cellspacing="0" cellpadding="2">
												<tr>
													<td>
														<span id="show" class="ui-icon ui-icon-circle-plus" title="New Entry"></span>
													</td>
													<td align="right">
														<b><span id="refresh" class="ui-icon ui-icon-arrowrefresh-1-e" title="Refresh"></span></b>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<table id="panel" border="0" cellspacing="0" cellpadding="5">
												<tr>
													<td valign="top" valign="middle" class="smallText">
														<b>Category</b>*
													</td>
													<td valign="top" valign="middle" class="smallText">
														<b>Group</b>
													</td>
													<td valign="top" valign="middle" class="smallText">
														<b>Discount (%)</b>
													</td>
													<td valign="top" valign="middle" class="smallText">
														<b>Action</b>
													</td>
												</tr>
												<tr>
													<td valign="top" valign="middle">
													<?php echo tep_draw_pull_down_menu('category', tep_get_category_tree()); ?>
													</td>
													<td valign="top" valign="middle">
													<?php echo tep_draw_pull_down_menu('group', getCustomerGroups()); ?>
													</td>
													<td valign="top" valign="middle">
														<input type="text" name="discount" maxlength="4" size="5" />
													</td>
													<td valign="middle">
														<button id="add" style="cursor:pointer;">Add</button>
														<button id="cancel" style="cursor:pointer;">Cancel</button>
													</td>
												</tr>
												<tr>
													<td colspan="4" class="smallText">
														* select only root level categories
														<br>
														Discount level will be overwritten if customer group & category association already exists.
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
									</tr>
									<tr>
										<td>
											<table border="0" width="100%" cellspacing="0" cellpadding="2">
												<tr class="dataTableHeadingRow">
													<td class="dataTableHeadingContent" width="20%">Category</td>
													<td class="dataTableHeadingContent" width="20%">Group</td>
													<td class="dataTableHeadingContent" width="20%" align="right">Discount</td>
													<td class="dataTableHeadingContent" width="40%">&nbsp;</td>
												</tr>
												<?php
												$query_raw = "select catg.customers_group_id, catg.customers_group_discount, catg.categories_id, custg.customers_group_name, cd.categories_name from categories_groups catg inner join customers_groups custg on catg.customers_group_id=custg.customers_group_id inner join categories_description cd on (catg.categories_id=cd.categories_id and cd.language_id='1') order by cd.categories_name, custg.customers_group_name";
												$query_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $query_raw, $query_numrows);
												$query = tep_db_query($query_raw);
												$entries_count = tep_db_num_rows($query);
												while ($entry = tep_db_fetch_array($query)){
												?>
												<tr class="dataTableRow">
													<td class="dataTableContent" valign="middle">
														<span id="category_container" group_id="<?php echo $entry['customers_group_id']; ?>" category_id="<?php echo $entry['categories_id']?>"></span>
														<span id="category" group_id="<?php echo $entry['customers_group_id']; ?>" category_id="<?php echo $entry['categories_id']?>" class="modifiable"><?php echo $entry['categories_name']; ?></span>
													</td>
													<td class="dataTableContent" valign="middle">
														<span id="group_container" group_id="<?php echo $entry['customers_group_id']; ?>" category_id="<?php echo $entry['categories_id']?>"></span>
														<span id="group" group_id="<?php echo $entry['customers_group_id']; ?>" category_id="<?php echo $entry['categories_id']?>" class="modifiable"><?php echo $entry['customers_group_name']; ?></span>
													</td>
													<td class="dataTableContent" valign="middle" align="right">
														<span id="discount_container" group_id="<?php echo $entry['customers_group_id']; ?>" category_id="<?php echo $entry['categories_id']?>"></span>
														<span id="discount" group_id="<?php echo $entry['customers_group_id']; ?>" category_id="<?php echo $entry['categories_id']?>" class="modifiable"><?php echo number_format($entry['customers_group_discount'], 2); ?></span>%
													</td>
													<td class="dataTableContent" align="right" valign="middle">
														<span id="remove" class="ui-icon ui-icon-circle-close" group_id="<?php echo $entry['customers_group_id']; ?>" category_id="<?php echo $entry['categories_id']?>" title="Remove"></span>
													</td>
												</tr>
												<?php } ?>
												<?php if ($entries_count){ ?>
												<tr>
													<td colspan="4" class="smallText">
														<b>To modify, click on category, group, or discount value.</b>
													</td>
												</tr>
												<?php } ?>
												<tr>
													<td colspan="4">
														<table border="0" width="100%" cellspacing="0" cellpadding="2">
															<tr>
																<td class="smallText" valign="top">
																<?php echo $query_split->display_count($query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], 'Displaying %d to %d (of 6 entries)'); ?>
																</td>
																<td class="smallText" align="right">
																<?php echo $query_split->display_links($query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?>
																</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
											<div id="category_content" style="display:none;"><?php echo tep_draw_pull_down_menu('', tep_get_category_tree()); ?></div>
											<div id="group_content" style="display:none;"><?php echo tep_draw_pull_down_menu('group', getCustomerGroups(true)); ?></div>
											<div id="discount_content" style="display:none;"><input type="text"  maxlength="4" size="5" /></div>
											<div id="loader_content" style="display:none;"><img id="loader" src="<?php echo DIR_WS_ICONS?>ajax-loader-small.gif" /></div>
										</td>
									</tr>
								</table>
							</td>
							<!-- body_text_eof //-->
						</tr>
					</table>
				</td>
				<!-- body_eof //-->
			</tr>
		</table>

		<!-- footer //-->
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
		<br>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>