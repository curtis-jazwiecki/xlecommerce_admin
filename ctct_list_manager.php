<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
ini_set('display_errors', '1');
require('includes/application_top.php');
include(DIR_WS_CLASSES . 'ctctWrapper.php');

$action = $_GET['action'];
$id 	= $_GET['id'];
$ctc_id = $_GET['ctc_id'];
$action = empty($action) ? 'view' : $action;

$heading 			= '';
$active_list 		= '';
$active_contact 	= '';
$flag_select_last 	= false;
/* logic to register all lists in osc. keeping the logic as it maye be required in future */
/*$contactlist = new ListsCollection();
$lists_col = $contactlist->getLists();
foreach($lists_col[0] as $entry){
	$sql = tep_db_query("select * from ctct_lists where ctct_list_id='" . $entry->getId() . "'");
	if (tep_db_num_rows($sql)){
		$data_array = array('list_name' 		=> $entry->getName(), 
							'list_sort_order' 	=> $entry->getSortOrder(), 
							'list_is_default' 	=> ($entry->getOptInDefault()=='true' ? '1' : '0'), 
							'last_modified'		=> 'now()');
		tep_db_perform('ctct_lists', $data_array, 'update', "ctct_list_id='" . $entry->getId() . "'");
	} else {
		$data_array = array('ctct_list_id'		=> $entry->getId(), 
							'list_name' 		=> $entry->getName(), 
							'list_sort_order' 	=> $entry->getSortOrder(), 
							'list_is_default' 	=> ($entry->getOptInDefault()=='true' ? '1' : '0'), 
							'date_added'		=> 'now()');
		tep_db_perform('ctct_lists', $data_array);
	}
}*/
		
switch($action){
	case 'manage_subscribers':
	case 'view_ctc':
	case 'edit_ctc':
	case 'remove_ctc':
	case 'new_ctc':
	$lists = array();
	$sql = tep_db_query("select * from ctct_lists where list_name not in ('Active', 'Do Not Mail', 'Removed') order by list_sort_order, list_name");
	while($entry = tep_db_fetch_array($sql)){
		$lists[] = $entry;
	}

		$contactlist = new ListsCollection();
		$lists_col = $contactlist->getLists();
		foreach($lists_col[0] as $entry){
			if ($entry->getId()==$id){
				$active_list = $entry;
				$list_name = $active_list->getName();
				break;
			}
		}
		$list_col_obj = new ListsCollection();
		$contacts = $list_col_obj->getListMembers($entry);
		if (empty($ctc_id) && count($contacts[0])){
			$active_contact =  $contacts[0][0];
			$ctc_id = $active_contact->getId();
		} else {
			foreach($contacts[0] as $contact){
				if($ctc_id==$contact->getId()) {
					$active_contact =  $contact;
					break;
				}
			}
		}
		if ($action=='new_ctc'){
			$heading = 'Create New Contact';
		}
		break;
	case 'new_ctc_add':
		$lists_d = $_POST['lists'];
		$email_d = $_POST['email'];
		$fname_d = $_POST['fname'];
		$lname_d = $_POST['lname'];
		
		$params = array('email_address' => $email_d, 
						'first_name'	=> $fname_d, 
						'last_name'		=> $lname_d, 
						'lists'			=> $lists_d);
		$contact_obj = new Contact($params);
		
		$contacts_collection_obj = new ContactsCollection();
		$resp = $contacts_collection_obj->searchByEmail($email_d);
		if ($resp){
			$status = $contacts_collection_obj->updateContact($resp[0][0], $contact_obj);
		} else {
			$status = $contacts_collection_obj->createContact($contact_obj);
		}
		tep_redirect('ctct_list_manager.php?action=manage_subscribers&id=' . $id);
		break;
	case 'view':
	case 'new':
	case 'selectlast':
	case 'edit':
		if ($action=='selectlast'){
			$action = 'view';
			$flag_select_last = true;
		}
		$contactlist = new ListsCollection();
		$response = $contactlist->getLists();
		if ($action=='new'){
			$heading = 'New List Creation';
		}
		break;
	case 'submit':
		$contactlist = new ListsCollection();
		
		$name = $_POST['txtName'];
		$sort_order = $_POST['txtSortOrder'];
		$is_default = $_POST['chkIsDefault']=='1' ? 'true' : 'false';
		$list_id = $_POST['hdnId'];
		
		$list_obj = new ListObj();
		$list_obj->setName($name);
		$list_obj->setOptInDefault($is_default);
		$list_obj->setSortOrder($sort_order);
		if (!empty($list_id)){
			$list_to_be_edited = NULL;
			$lists_col = $contactlist->getLists();
			foreach($lists_col[0] as $entry){
				if ($entry->getId()==$list_id){
					$list_to_be_edited = $entry;
					break;
				}
			}
			if (!empty($list_to_be_edited)){
				$code = $contactlist->updateList($list_to_be_edited, $list_obj);
				$data_array = array('list_name' 		=> $name, 
									'list_sort_order' 	=> $sort_order, 
									'list_is_default' 	=> ($is_default=='true' ? '1' : '0'), 
									'last_modified'		=> 'now()');
				tep_db_perform('ctct_lists', $data_array, 'update', "ctct_list_id='" . $list_id . "'");
				tep_redirect(tep_href_link('ctct_list_manager.php?action=view&id=' . $list_id));
			}
		} else {
			$code = $contactlist->createList($list_obj);
			$lists_col = $contactlist->getLists();
			foreach($lists_col[0] as $entry){
				$sql = tep_db_query("select * from ctct_lists where ctct_list_id='" . $entry->getId() . "'");
				if (!tep_db_num_rows($sql)){
					$data_array = array('ctct_list_id'		=> $entry->getId(), 
										'list_name' 		=> $entry->getName(), 
										'list_sort_order' 	=> $entry->getSortOrder(), 
										'list_is_default' 	=> ($entry->getOptInDefault()=='true' ? '1' : '0'), 
										'date_added'		=> 'now()');
					tep_db_perform('ctct_lists', $data_array);
				}
			}
			tep_redirect(tep_href_link('ctct_list_manager.php?action=selectlast'));
		}
		break;
	case 'delete':
		$list_to_be_deleted = '';
		$contactlist = new ListsCollection();
		$lists_col = $contactlist->getLists();
		foreach($lists_col[0] as $entry){
			if ($entry->getId()==$id){
				$list_to_be_deleted = $entry;
				break;
			}
		}
		if (!empty($list_to_be_deleted)){
			$code = $contactlist->deleteList($list_to_be_deleted);
			tep_db_query("delete from ctct_lists where ctct_list_id='" . $id . "'");
		}
		tep_redirect(tep_href_link('ctct_list_manager.php'));
		break;
}
?>
<!DOCTYPE html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>" />
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css" />
</head>
<body style="margin:0 0 0 0;" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<table width="780px" border="0" align="center" cellpadding="2" cellspacing="2" style="margin: 0px auto;">
	<tr>
	<!-- body_text //-->
    	<td width="100%" valign="top">
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
      			<tr>
        			<td>
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
          					<tr>
            					<td class="pageHeading"><?php echo 'Constant Contact: List Manager' . (empty($list_name) ? '' : ' (' . $list_name . ')'); ?></td>
            					<td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          					</tr>
        				</table>
					</td>
      			</tr>
      			<tr>
        			<td>
						<table border="0" width="100%" cellspacing="0" cellpadding="2">
          					<tr>
								<td class="content">
								<?php if ($action=='view' || $action=='new' || $action=='edit'){ ?>
									<table cellspacing="0" cellpadding="0" border="0" width="100%">
										<tr>
											<td width="75%" valign="top">
												<table cellpadding="0" cellspacing="0" class="ctct" border="0" width="100%">
													<tr class="smallText2">
														<th align="left" class="main"><nobr>List Name</nobr></th>
														<th align="left" class="main"><nobr>Sort Order</nobr></th>
														<th align="left" class="main"><nobr>Is Default</nobr></th>
														<th align="right" width="100%" class="main">&nbsp;</th>
													</tr>
									<?php
									$count = 0;
									foreach($response[0] as $list){
										$count++;
										$name = $list->getName();
										if (!($name=='Active' || $name=='Do Not Mail' || $name=='Removed')){
											if (empty($id) && $action!='new'){
												if (!$flag_select_last){
													$id = $list->getId();
												} else {
													if (count($response[0])==$count){
														$id = $list->getId();
													}
												}
												
											}
											if ($id==$list->getId()){
												$heading = 'Listing: ' . $name;
												$active_list = $list;
											}
											echo '	<tr ' . ($id==$list->getId() ? ' class="selected" ' : '') . ' class="smallText2">';
											echo '		<td class="main"><nobr>' . $name . '</nobr></td>' . 
												 '		<td class="main"><nobr>' . $list->getSortOrder() . '</nobr></td>' .
												 '		<td class="main"><nobr>' . ($list->getOptInDefault()=='true' ? 'Yes' : 'No') . '</nobr></td>' . 
												 '		<td align="right" class="main">' .
												 '			<a href="' . tep_href_link('ctct_list_manager.php', tep_get_all_get_params(array('action', 'id')) . 'action=delete&id=' . $list->getId()) . '" onclick="return confirm(\'Are you sure you would like to delete the selected list?\nAny contacts that are only listed in a deleted list will be removed.\nOnce a list is deleted, it cannot be recovered.\')">' . tep_image(DIR_WS_IMAGES . 'icons/icon_delete.png', 'Delete', '16', '16') . '</a>&nbsp;' .
												 '			<a href="' . tep_href_link('ctct_list_manager.php', tep_get_all_get_params(array('action', 'id')) . 'action=edit&id=' . $list->getId()) . '">' . tep_image(DIR_WS_IMAGES . 'icons/icon_edit.png', 'Edit', '16', '16') . '</a>&nbsp;' .
												 '			<a href="' . tep_href_link('ctct_list_manager.php', tep_get_all_get_params(array('action', 'id')) . 'action=view&id=' . $list->getId()) . '">' . ($id==$list->getId() ? tep_image(DIR_WS_IMAGES . 'icons/icon_active.png', 'Selected', '16', '16') : tep_image(DIR_WS_IMAGES . 'icons/icon_inactive.png', 'Select', '16', '16')) . '</a>&nbsp;' .
												 '		</td>';
											echo '	</tr>';
										}
									} ?>
												</table>
											</td>
											<td width="25%" valign="top" style="height:100%;background-color:#eeeeee;padding:10px 5px 10px 5px;">
											<?php if ($action=='new') { ?>
												<table cellspacing="0" cellpadding="0" border="0" width="100%">
													<tr><td class="infoBoxHeading"><?php echo $heading; ?></td></tr>
													<tr>
														<td class="infoBoxContent">
															<form name="frmList" method="post" action="ctct_list_manager.php?action=submit">
															List name:<br /><?php echo tep_draw_input_field('txtName', '', '', true); ?>
															<br /><br />
															Sort order:<br /><?php echo tep_draw_input_field('txtSortOrder', '', '', true); ?>
															<br /><br />
															Is default list:<br /><?php echo tep_draw_selection_field('chkIsDefault', 'radio', '1', false) . '&nbsp;Yes&nbsp;&nbsp;' . tep_draw_selection_field('chkIsDefault', 'radio', '0', true) . '&nbsp;No'; ?>
															<br /><br />
															<div align="center">
																<input type="button" value="Cancel" onClick="location.href='<?php echo tep_href_link('ctct_list_manager.php', tep_get_all_get_params(array('action', 'id'))); ?>'" />
																&nbsp;&nbsp;
																<input type="submit" value="Submit" />
															</div>
															<br /><br />
															</form>
														</td>
													</tr>
												</table>
											<?php } elseif ($action=='edit') { ?>
												<table cellspacing="0" cellpadding="0" border="0" width="100%">
													<tr><td class="infoBoxHeading"><?php echo $heading; ?></td></tr>
													<tr>
														<td class="infoBoxContent">
															<form name="frmList" method="post" action="ctct_list_manager.php?action=submit">
															List name:<br /><input type="text" name="txtName" value="<?php echo $active_list->getName(); ?>" /><?php echo TEXT_FIELD_REQUIRED; ?>
															<br /><br />
															Sort order:<br /><input type="text" name="txtSortOrder" value="<?php echo $active_list->getSortOrder(); ?>" /><?php echo TEXT_FIELD_REQUIRED; ?>
															<br /><br />
															Is default list:<br /><input type="radio" name="chkIsDefault" value="1" <?php echo ($active_list->getOptInDefault()=='true' ? ' checked ' : ''); ?> />&nbsp;Yes&nbsp;&nbsp;<input type="radio" name="chkIsDefault" value="0" <?php echo ($active_list->getOptInDefault()=='true' ? '' : ' checked '); ?> />No&nbsp;
															<br /><br />
															<input type="hidden" name="hdnId" value="<?php echo $active_list->getId(); ?>" />
															<div align="center">
																<input type="button" value="Cancel" onClick="location.href='<?php echo tep_href_link('ctct_list_manager.php', tep_get_all_get_params(array('action', 'id')) . 'id=' . $active_list->getId()); ?>'" />
																&nbsp;&nbsp;
																<input type="submit" value="Submit" />
															</div>
															<br /><br />															
															</form>
														</td>
													</tr>
												</table>
											<?php } else { ?>
												<table cellspacing="0" cellpadding="0" border="0" width="100%">
													<tr><td class="infoBoxHeading"><?php echo $heading; ?></td></tr>
													<tr>
														<td class="infoBoxContent">
															List name:<br /><?php echo $active_list->getName(); ?>
															<br /><br />
															Sort order:<br /><?php echo $active_list->getSortOrder(); ?>
															<br /><br />
															Is default list:<br /><?php echo ($active_list->getOptInDefault()=='true' ? 'Yes' : 'No') ; ?>
															<br /><br />
															<br /><br />
															<input type="button" value="Manage Subscribers" onclick="location.href='ctct_list_manager.php?action=manage_subscribers&id=<?php echo $active_list->getId(); ?>';" />
														</td>
													</tr>
												</table>
											<?php } ?>
											</td>
										</tr>
										<tr>
											<td align="left">
												<a href="<?php echo tep_href_link('ctct_list_manager.php', tep_get_all_get_params(array('action', 'id')) . 'action=new') ?>" ><?php echo tep_image(DIR_WS_IMAGES . 'icons/icon_new.png', 'Create new list', '16', '16') ?></a>
											</td>
											<td align="right">&nbsp;</td>
										</tr>
									</table>
								<?php } elseif ($action=='manage_subscribers' || $action=='view_ctc' || $action=='edit_ctc' || $action=='remove_ctc' || $action == 'new_ctc'){ ?>
									<table cellspacing="0" cellpadding="0" border="0" width="100%">
										<tr>
											<td width="75%" valign="top">
												<table cellpadding="0" cellspacing="0" class="ctct" border="0" width="100%">
													<tr class="smallText2">
														<th align="left" class="main"><nobr>Email</nobr></th>
														<th align="left" class="main"><nobr>Name</nobr></th>
														<th align="right" width="100%" class="main">&nbsp;</th>
													</tr>
									<?php
									$count = 0;
									foreach($contacts[0] as $contact){
										$count++;
										echo '	<tr ' . ($active_contact->getId()==$contact->getId() ? ' class="selected" ' : '') . ' class="smallText2">';
										echo '		<td class="main"><nobr>' . $contact->getEmailAddress() . '</nobr></td>' . 
											 '		<td class="main"><nobr>' . $contact->getFullName() . '</nobr></td>' .
											 '		<td align="right" class="main">' .
											 '			<a href="' . tep_href_link('ctct_list_manager.php', tep_get_all_get_params(array('action', 'ctc_id')) . 'action=remove_ctc&ctc_id=' . $contact->getId()) . '" onclick="return confirm(\'Are you sure you would like to remove selected contact?\')">' . tep_image(DIR_WS_IMAGES . 'icons/icon_delete.png', 'Delete', '16', '16') . '</a>&nbsp;' .
											 '			<a href="' . tep_href_link('ctct_list_manager.php', tep_get_all_get_params(array('action', 'ctc_id')) . 'action=edit_ctc&ctc_id=' . $contact->getId()) . '">' . tep_image(DIR_WS_IMAGES . 'icons/icon_edit.png', 'Edit', '16', '16') . '</a>&nbsp;' .
											 '			<a href="' . tep_href_link('ctct_list_manager.php', tep_get_all_get_params(array('action', 'ctc_id')) . 'action=view_ctc&ctc_id=' . $contact->getId()) . '">' . ($ctc_id==$contact->getId() ? tep_image(DIR_WS_IMAGES . 'icons/icon_active.png', 'Selected', '16', '16') : tep_image(DIR_WS_IMAGES . 'icons/icon_inactive.png', 'Select', '16', '16')) . '</a>&nbsp;' .
											 '		</td>';
										echo '	</tr>';
									} ?>
												</table>
											</td>
											<td width="25%" valign="top" style="height:100%;background-color:#eeeeee;padding:10px 5px 10px 5px;">
											<?php if ($action=='new_ctc'){ ?>
												<table cellspacing="0" cellpadding="0" border="0" width="100%">
													<tr><td class="infoBoxHeading"><?php echo $heading; ?></td></tr>
													<tr>
														<td class="infoBoxContent">
						    								<form name="form_subscribe" method="post" action="<?php echo tep_href_link('ctct_list_manager.php', tep_get_all_get_params(array('action', 'ctc_id')) . 'action=new_ctc_add') ?>">
						    								List(s)*<br />
						      								<?php foreach($lists as $list){ ?>
																<input type="checkbox" name="lists[]" value="<?php echo $list['ctct_list_id']; ?>" <?php echo ($list['list_is_default'] ? ' checked ' : '') ?> />
																<?php echo $list['list_name']; ?> <br />
						      								<?php } ?>
						      								Email*<br />
						      								<input type="text" name="email" value="" /><br />
						      								First name<br />
						      								<input type="text" name="fname" value="" /><br />
						      								Last name<br />
						      								<input type="text" name="lname" value="" /><br />
						      								<input type="reset" />&nbsp;&nbsp;<input type="submit" value="Insert"/>
						      								</form>
														</td>
													</tr>
												</table>
											<?php } ?>
											</td>
										</tr>
										<tr>
											<td>
												<input type="button" value="Back to Lists" onclick="location.href='ctct_list_manager.php?action=view&id=<?php echo $id; ?>'" />
												<div style="float:right;"><a href="<?php echo tep_href_link('ctct_list_manager.php', tep_get_all_get_params(array('action', 'ctc_id')) . 'action=new_ctc') ?>" ><?php echo tep_image(DIR_WS_IMAGES . 'icons/icon_new.png', 'Create new contact', '16', '16') ?></a></div>
											</td>
											<td>&nbsp;</td>
										</tr>
									</table>
								<?php } ?>
								</td>
							</tr>
          				</table>
          			</td>
          		</tr>
      			<tr><td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
    		</table>
		</td>
		<!-- body_text_eof //-->
  	</tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br/>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
