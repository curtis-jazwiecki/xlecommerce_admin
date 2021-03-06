<?php
require('includes/application_top.php');

if (isset($_POST['validate_mode']) && $_POST['validate_mode']=='1'){
	$model = $_POST['model'];
	$sql = tep_db_query("select products_id, warehouse_quantity from products where products_model='" . tep_db_input($model) . "'");
	//echo tep_db_num_rows($sql) ? 'OK' : 'ERROR';
    echo tep_db_num_rows($sql) ? 'OK|' . $_POST['index'] : 'ER|' . $_POST['index'];
	exit();
}

if (isset($_POST['action']) && $_POST['action']=='save'){
	$inventory_import_01_data = array(
		'date_added' => 'now()', 
		'comment' => (!empty($_POST['comment']) ? $_POST['comment'] : 'null'),
	);
	tep_db_perform('inventory_import_01', $inventory_import_01_data);
	$id = tep_db_insert_id();
	for($i=0; $i<=$_POST['last_index']; $i++){
		$previous_stock_level = 'null';
		$sql = tep_db_query("select warehouse_quantity from products where products_model='" . tep_db_input($_POST['model_' . $i]) . "'");
		if (tep_db_num_rows($sql)){
			$info = tep_db_fetch_array($sql);
			$previous_stock_level = $info['warehouse_quantity'];
		}
	
		$inventory_import_02_data = array(
			'inventory_import_id' => $id, 
			'products_model' => $_POST['model_' . $i], 
			'stock_level' => $_POST['stock_' . $i], 
			'sort_order' => $i, 
			'previous_stock_level' => $previous_stock_level,
		);
		tep_db_perform('inventory_import_02', $inventory_import_02_data);
		
		tep_db_query("update products set warehouse_quantity=warehouse_quantity + " . (int)$_POST['stock_' . $i] . ", products_last_modified=now() where products_model='" . tep_db_input($_POST['model_' . $i]) . "'");
	}
	echo 'OK';
	exit();
}

function getItems($id){
	$resp = '<table>';
	$sql = tep_db_query("select products_model, stock_level from inventory_import_02 where inventory_import_id='" . $id . "' order by sort_order");
	while ($entry = tep_db_fetch_array($sql)){
		$resp .= '<tr>
					<td class="smallText" style="width:80px;">' . $entry['products_model'] . '</td>
					<td class="smallText">' . $entry['stock_level'] . '</td>
				 </tr>';
	}
	$resp .='</table>';
	return $resp;
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
		<link rel="stylesheet" type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/base/jquery-ui.css">
		<script language="javascript" src="includes/general.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js"></script>
		<script type="text/javascript">
			var loader = new Image();
			loader.src = '<?php echo DIR_WS_IMAGES ?>ajax-loader.gif';
			$(document).ready(function(){
				$('input[name="invoice_no"]').keypress(function(event){
					if (event.keyCode=='13'){
						location.href = 'inventory_import.php?invoice=' + $(this).val();
					}
				});
				
				$('#add_another_model').click(function(){
					add_new_entry();
				});
				
				$(document).on('keypress', 'input[name^="stock_"]', function(event){
					if(event.keyCode=='13'){
						add_new_entry();
					}
				});
				
				$('#btn_save').click(function(){
					var error = false;
					var items_count = 0;
					$('input[name^="model_"]').each(function(){
						if ($(this).val()!=''){
							items_count++;
						}
						if (error==false){
							if ($(this).attr('invalid')=='invalid'){
								alert('One of the model fields is holding invalid value!');
								$(this).select();
								error = true;
								return false;
							}
						}
					});
					if (error) return;
					if (items_count<=0){
						alert('No model(s) values!');
						error = true;
						return;
					}
					if ($('input[name="comment"]').val()==''){
						alert('Invoice# missing!');
						$('input[name="comment"]').focus();
						error = true;
						return;
					}
					if (!error){
						$(this).css('display', 'none').parent().prepend('<img id="loader" src=' + loader.src + ' />');
						$.ajax({
							type: 'post', 
							url: 'inventory_import.php', 
							data: $('form[name="frm_inventory"]').serialize(), 
							success: function(msg){
								if (msg=='OK'){
									reset_form();
									$('#btn_save').css('display', '');
								}
								$('#loader').remove();
								location.href = 'inventory_import.php';
							}
						});
					}
				});
				
				$('#btn_reset').click(function(){
					reset_form();
				});
				
				$(document).on('focus', 'input[name^="model_"]', function(){
					$(this).css('color', 'black');
				});
                
                $(document).on('keypress', 'input[name^="model_"]', function(event){
                    var cur_ref = $(this);
                    var index = cur_ref.attr('id').replace(/model_/ig, '');
				    if(event.keyCode=='13' || event.keyCode=='10'){
				        $('#model_' + index).trigger('blur');
                        /*$(document).trigger({ 
                            type:   'blur',
                            target: $('input[name="model_' + index + '"]')
                        });*/
                    }    
                });
                
				$(document).on('blur', 'input[name^="model_"]', function(){
					if ($(this).val()!=''){
						/*var pattern = /\d/;
						if (pattern.test($(this).val().substring(0, 1))){
							$(this).val('JM' + $(this).val());
						}*/
						$(this).css('color', 'black').parent().append('<img id="loader" src=' + loader.src + ' />');
						var cur_ref = $(this);
                        var index = cur_ref.attr('id').replace(/model_/ig, '');
						$.ajax({
							type: 'post', 
							url: 'inventory_import.php', 
							data: ({model: $(this).val(), validate_mode: 1, index: index}), 
							success: function(msg){
							 //console.log(msg);
							     suffix = '';
								if (msg.indexOf('OK|')!='-1'){
									$(cur_ref).css({'color': 'green'});
									$(cur_ref).removeAttr('invalid');
                                    suffix = msg.replace('OK|', '');
                                    //console.log(' OK ' + suffix);
								} else {
								    //console.log(' ER ');
									$(cur_ref).css({'color': 'red'});
									$(cur_ref).attr('invalid', 'invalid');
                                    suffix = msg.replace('ER|', '');
                                    //console.log(' ER ' + suffix);
								}
								$('#loader').remove();
                                //console.log("stock_" + suffix);
                                $('input[name="stock_' + suffix + '"]').focus();
                                //document.getElementById("stock_" + suffix).focus();
                                //setTimeout(function(){
                                //    $('input[name^="stock_' + suffix + '"]').focus();
                                //}, 0);
                                
							}
						});
					}
				})
                
                /*$('form[name="scanner"]').submit(function(event){
                    event.preventDefault();
                    var index = '';
                    $('input[name^="model_"]').each(function(){
                        index = $(this).attr('id').replace(/model_/i, '');
					});
                    console.log(index);
                });*/
			});

			function reset_form(){
				$('input:hidden[name="last_index"]').val('0');
				$('table#tab_entry tr:gt(1)').remove();
				$('textarea[name="comment"]').val('');
				$('#model_0').val('').removeAttr('invalid');;
				$('#stock_0').val('');
			}
			
			function add_new_entry(){
				var next_index = parseInt($('input:hidden[name="last_index"]').val()) + 1;
				$('table#tab_entry').append('<tr><td><input type="text" name="model_' + next_index + '" id="model_' + next_index + '" /></td><td><input type="text" name="stock_' + next_index + '" id="stock_' + next_index + '" /></td></tr>');
				$('input[name="model_' + next_index + '"]').focus();
				$('input:hidden[name="last_index"]').val(next_index);
			}
            
		</script>
	</head>
	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
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
					<table border="0" width="100%" cellspacing="0" cellpadding="2" style="background-color:#ffffff;">
						<tr>
							<td>
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td class="pageHeading"><?php echo 'Inventory Import'; ?></td>
										<td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="main">
								<?php /*<form name="scanner">
                                    <input name="scanner_value" id="scanner_value" />
                                </form> */?>
								<form name="frm_inventory">
									<fieldset>
										<legend><b>Update Stock level(s)</b></legend>
										<table>
											<tr>
												<td class="main" align="right">Invoice#&nbsp;</td>
												<td class="main">
													<input type="text" name="comment" id="comment" />
												</td>
											</tr>
											<tr>
												<td class="main" align="right" valign="top">Items&nbsp;</td>
												<td class="main">
													<table id="tab_entry">
														<tr>
															<td class="smallText" style="width:200px;">Product Model</td>
															<td class="smalltext">Stock Level</td>
														</tr>
														<tr>
															<td><input type="text" name="model_0" id="model_0" /></td>
															<td><input type="text" name="stock_0" id="stock_0" /></td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align="right" colspan="2">
													<input type="button" value="Add Another Model" id="add_another_model">
												</td>
											</tr>
											<tr>
												<td align="center" colspan="2">
													<input type="button" id="btn_save" value="Save" />
													&nbsp;&nbsp;
													<input type="button" id="btn_reset" value="Reset" />
												</td>
											</tr>
										</table>
									</fieldset>
									<input type="hidden" name="last_index" value="0" />
									<input type="hidden" name="action" value="save" />
								</form>
							</td>
						</tr>
						<tr>
							<td>
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td align="right" class="main" style="padding:15px;">
											Search by Invoice#: <input type="text" name="invoice_no" id="invoice_no" value="<?php echo (isset($_GET['invoice']) ? $_GET['invoice'] : ''); ?>" />
										</td>
									</tr>
									<tr>
										<td valign="top">
											<table border="0" width="100%" cellspacing="0" cellpadding="2">
												<tr class="dataTableHeadingRow">
													<td class="dataTableHeadingContent">Sno.</td>
													<td class="dataTableHeadingContent">Items/Inventory</td>
													<td class="dataTableHeadingContent">Invoice#</td>
													<td class="dataTableHeadingContent">Imported</td>
												</tr>
												<?php
												$query_raw = "select id, date_added, comment from inventory_import_01 " . (isset($_GET['invoice']) && !empty($_GET['invoice']) ? " where comment='" . tep_db_input($_GET['invoice']) . "'" : "") . " order by id desc";
												$query_split = new splitPageResults($HTTP_GET_VARS['page'], 5, $query_raw, $query_numrows);
												$query = tep_db_query($query_raw);
												$sno = (((empty($HTTP_GET_VARS['page']) ? 1 : (int)$HTTP_GET_VARS['page']) - 1) * 5);
												while ($entry = tep_db_fetch_array($query)) {
													if ((!isset($HTTP_GET_VARS['eID']) || (isset($HTTP_GET_VARS['eID']) && ($HTTP_GET_VARS['eID'] == $entry['id'])))) {
														$eInfo = new objectInfo($entry);
													}
                                                    ?>
												<tr class="dataTableRow" onMouseOver="rowOverEffect(this)" onMouseOut="rowOutEffect(this)">
													<td class="dataTableContent" valign="top"><?php echo ++$sno; ?></td>
													<td class="dataTableContent" valign="top"><?php echo getItems($entry['id']); ?></td>
													<td class="dataTableContent" valign="top"><?php echo $entry['comment']; ?></td>
													<td class="dataTableContent" valign="top"><?php echo $entry['date_added']; ?></td>
												</tr>
												<?php
												}
												?>
												<tr>
													<td colspan="4">
														<table border="0" width="100%" cellspacing="0" cellpadding="2">
															<tr>
																<td class="smallText" valign="top">
																<?php echo $query_split->display_count($query_numrows, 5, $HTTP_GET_VARS['page'], 'Displaying %d to %d (of %d entries)'); ?>
																</td>
																<td class="smallText" align="right">
																<?php echo $query_split->display_links($query_numrows, 5, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?>
																</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
										</td>
										<?php
										$heading = array();
										$contents = array();
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
					</table>
				</td>
				<!-- body_text_eof //-->
			</tr>
		</table>
		<!-- body_eof //-->
		<!-- footer //-->
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
		<br>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>