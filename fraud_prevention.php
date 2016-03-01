<? require('includes/application_top.php'); ?>
<?php
if (!empty($_GET['action']) && $_GET['action']=='save'){
    $ship_methods = '';
    for($i=1; $i<=$_POST['ship_methods_count']; $i++){
        if (!empty($_POST['ship_method_' . $i])){
            $ship_methods .= $_POST['ship_method_' . $i] . ',';
        }
    }
    if (!empty($ship_methods)){
        $ship_methods = substr($ship_methods, 0, -1);
    }
    $params = array(
        'status' => tep_db_prepare_input($_POST['status']),
        'shipping_status' => tep_db_prepare_input($_POST['shipping_status']),
        'ship_methods' => tep_db_prepare_input($ship_methods), 
        'countries_status' => tep_db_prepare_input($_POST['countries_status']),
        'countries' => (is_array($_POST['countries']) ? tep_db_prepare_input(implode(',', $_POST['countries'])) : ''),
        'ip_status' => tep_db_prepare_input($_POST['ip_status']),
        'ip_addresses' => tep_db_prepare_input($_POST['ip_addresses']), 
        'customer_name_status' => tep_db_prepare_input($_POST['customer_name_status']),
        'customer_names' => tep_db_prepare_input($_POST['customer_names']), 
        'address_status' => tep_db_prepare_input($_POST['address_status']),
        'addresses' => tep_db_prepare_input($_POST['addresses']),
        'amount_status' => tep_db_prepare_input($_POST['amount_status']), 
        'dollar_value' => tep_db_prepare_input($_POST['dollar_value']),
        'email_status' => tep_db_prepare_input($_POST['email_status']), 
        'email_addresses' => tep_db_prepare_input($_POST['email_addresses']),
        'category_status' => tep_db_prepare_input($_POST['category_status']), 
        'categories' => (is_array($_POST['categories']) ? tep_db_prepare_input(implode(',', $_POST['categories'])) : ''),
        'address_mismatch_check' => tep_db_prepare_input($_POST['mismatch_status']),
        'phone_status' => tep_db_prepare_input($_POST['phone_status']), 
        'phone_numbers' => tep_db_prepare_input($_POST['phone_numbers']), 
    );

    
    tep_db_query("update configuration set configuration_value='" . $params['status'] . "' where configuration_key='FRAUD_PREVENTION_FUNCTIONALITY_STATUS'");
    
    tep_db_query("update configuration set configuration_value='" . $params['shipping_status'] . "' where configuration_key='FRAUD_PREVENTION_SHIPPING_STATUS'");
    
    tep_db_query("update configuration set configuration_value='" . $params['address_mismatch_check'] . "' where configuration_key='FRAUD_PREVENTION_SHIPPING_BILLING_MISMATCH'");
    
    tep_db_query("update configuration set configuration_value='" . $params['countries_status'] . "' where configuration_key='FRAUD_PREVENTION_COUNTRIES_STATUS'");
    
    tep_db_query("update configuration set configuration_value='" . $params['ip_status'] . "' where configuration_key='FRAUD_PREVENTION_IP_STATUS'");
    
    tep_db_query("update configuration set configuration_value='" . $params['customer_name_status'] . "' where configuration_key='FRAUD_PREVENTION_CUSTOMER_NAME_STATUS'");
    
    tep_db_query("update configuration set configuration_value='" . $params['address_status'] . "' where configuration_key='FRAUD_PREVENTION_ADDRESS_STATUS'");
    
    tep_db_query("update configuration set configuration_value='" . $params['amount_status'] . "' where configuration_key='FRAUD_PREVENTION_AMOUNT_STATUS'");
    
    tep_db_query("update configuration set configuration_value='" . $params['email_status'] . "' where configuration_key='FRAUD_PREVENTION_EMAIL_STATUS'");
    
    tep_db_query("update configuration set configuration_value='" . $params['category_status'] . "' where configuration_key='FRAUD_PREVENTION_CATEGORY_STATUS'");
    
        tep_db_query("update configuration set configuration_value='" . $params['address_mismatch_check'] . "' where configuration_key='FRAUD_PREVENTION_SHIPPING_BILLING_MISMATCH'");
        
    tep_db_query("update configuration set configuration_value='" . $params['phone_status'] . "' where configuration_key='FRAUD_PREVENTION_PHONE_STATUS'");
    
    tep_db_query("update configuration set configuration_value='" . $params['ship_methods'] . "' where configuration_key='FRAUD_PREVENTION_SHIPPING_METHODS'");
    
    tep_db_query("update configuration set configuration_value='" . $params['countries'] . "' where configuration_key='FRAUD_PREVENTION_COUNTRIES'");
    
    tep_db_query("update configuration set configuration_value='" . $params['ip_addresses'] . "' where configuration_key='FRAUD_PREVENTION_IP_ADDRESSES'");
    
    tep_db_query("update configuration set configuration_value='" . $params['customer_names'] . "' where configuration_key='FRAUD_PREVENTION_CUSTOMER_NAMES'");
    
    tep_db_query("update configuration set configuration_value='" . $params['addresses'] . "' where configuration_key='FRAUD_PREVENTION_ADDRESSES'");
    
    tep_db_query("update configuration set configuration_value='" . $params['dollar_value'] . "' where configuration_key='FRAUD_PREVENTION_DOLLAR_VALUE'");
    
    tep_db_query("update configuration set configuration_value='" . $params['email_addresses'] . "' where configuration_key='FRAUD_PREVENTION_EMAIL_ADDRESSES'");
    
    tep_db_query("update configuration set configuration_value='" . $params['categories'] . "' where configuration_key='FRAUD_PREVENTION_PRODUCT_CATEGORIES'");
    
    tep_db_query("update configuration set configuration_value='" . $params['phone_numbers'] . "' where configuration_key='FRAUD_PREVENTION_PHONE_NUMBERS'");
    
    tep_redirect(tep_href_link('fraud_prevention.php'));
}
    
function getShippingMethods(){
    $selected_methods = explode(',', FRAUD_PREVENTION_SHIPPING_METHODS);
    $methods = array();
    $dir = DIR_FS_CATALOG . 'includes/modules/shipping/';
    if (is_dir($dir)){
        if ($dh = opendir($dir)){
            while( ($file = readdir($dh) ) !== false ){
                if ( strtolower( substr($file, -4) ) == '.php' ){
                    include_once(DIR_FS_CATALOG . 'includes/languages/english/modules/shipping/' . $file);
                    include_once($dir . $file);
                    $code = substr($file, 0, -4);
                    $obj = new $code;
                     
                    $methods[] = array(
                        'code' => $code, 
                        'title' => $obj->title,
                        'checked' => (in_array($code, $selected_methods) ? '1' : '0' ),
                        //'types' => $types,  
                    );
					
                    $types = array();
                    switch ($code){
                        case 'fedex1':
                            $types = array_merge((array)$obj->domestic_types, (array)$obj->international_types);
							foreach($types as $key => $value){
								$temp_code = str_replace(' ', '-', $code . '_' . $key);
								$methods[] = array(
									'code' => $temp_code, 
									'title' => '---->' . $value,
									'checked' => (in_array($temp_code, $selected_methods) ? '1' : '0' ),
									//'types' => $types,  
								);
							}
                            break;
                        case 'usps':
                            $types = array_merge((array)$obj->types, (array)$obj->intl_types);
							foreach($types as $key => $value){
								$temp_code = str_replace(' ', '-', $code . '_' . $key);
								$methods[] = array(
									'code' => $temp_code, 
									'title' => '---->' . $value,
									'checked' => (in_array($temp_code, $selected_methods) ? '1' : '0' ),
									//'types' => $types,  
								);
							}
                            break;
                    }
					
                    unset($obj);
                }
            }
            closedir($dh);
        }
    }
    return $methods;
}

$status = FRAUD_PREVENTION_FUNCTIONALITY_STATUS;
$shipping_status = FRAUD_PREVENTION_SHIPPING_STATUS;
$countries_status = FRAUD_PREVENTION_COUNTRIES_STATUS;
$ip_status = FRAUD_PREVENTION_IP_STATUS;
$customer_name_status = FRAUD_PREVENTION_CUSTOMER_NAME_STATUS;
$amount_status = FRAUD_PREVENTION_AMOUNT_STATUS;
$address_status = FRAUD_PREVENTION_ADDRESS_STATUS;
$category_status = FRAUD_PREVENTION_CATEGORY_STATUS;
$phone_status = FRAUD_PREVENTION_PHONE_STATUS;
$mismatch_status = FRAUD_PREVENTION_SHIPPING_BILLING_MISMATCH;
$email_status = FRAUD_PREVENTION_EMAIL_STATUS;
$shipping_methods = getShippingMethods();

/*
//FRAUD_PREVENTION_FUNCTIONALITY_STATUS
//FRAUD_PREVENTION_SHIPPING_METHODS
//FRAUD_PREVENTION_COUNTRIES
//FRAUD_PREVENTION_IP_ADDRESSES
//FRAUD_PREVENTION_CUSTOMER_NAMES
//FRAUD_PREVENTION_ADDRESSES
//FRAUD_PREVENTION_DOLLAR_VALUE
//FRAUD_PREVENTION_EMAIL_ADDRESSES
//FRAUD_PREVENTION_PRODUCT_CATEGORIES
//FRAUD_PREVENTION_SHIPPING_BILLING_MISMATCH
//FRAUD_PREVENTION_PHONE_NUMBERS
*/

?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<style type="text/css">
		.ui-button-text{
			font-size:12px;
		}
		h3.ui-accordion-header{
			font-size:12px;
			font-weight:bold;
		}
		.ui-widget input, .ui-widget select, .ui-widget textarea, .ui-widget button{
			font-size:12px;
		}

		</style>
        <script src="//code.jquery.com/jquery-1.9.1.js"></script>
        <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script>
            $(document).ready(function(){
                
                var countries = '<?php echo FRAUD_PREVENTION_COUNTRIES; ?>';
                if (countries!=''){
                    arr_countries = countries.split(',');
                    for(var i=0; i<arr_countries.length; i++){
                        if (arr_countries[i]!=''){
                            $('select[name="countries[]"] option[value="' + arr_countries[i] + '"]').attr('selected', 'selected');
                        }
                    }
                }
                
                var categories = '<?php echo FRAUD_PREVENTION_PRODUCT_CATEGORIES; ?>';
                if (categories!=''){
                    arr_categories = categories.split(',');
                    for(var i=0; i<arr_categories.length; i++){
                        if (arr_categories[i]!=''){
                            $('select[name="categories[]"] option[value="' + arr_categories[i] + '"]').attr('selected', 'selected');
                        }
                    }
                }
                
                
               $('#status, span#shipping_status, span#countries_status, span#ip_status, span#customer_name_status, span#address_status, span#amount_status, span#email_status, span#category_status, span#mismatch_status, span#phone_status').buttonset();
               
               $('#types').accordion({
                heightStyle: 'content'
               });
               
               $('#shipping_methods').buttonset();
               
               /*$('#countries').autocomplete({
                source: 'json_get_countries.php', 
                minLength: 2, 
                select: function(event, ui){
                    event.preventDefault();
                    $('input[name="countries"]').val(ui.item.label);
                    if ($('div#selected_countries span#cntry_' + ui.item.value).length==0){
                        $('div#selected_countries').append('<span id="cntry_' + ui.item.value + '">' + ui.item.label + '</span><br>');
                        if ($('input[name="selected_countries"]').val().indexOf('[' + ui.item.value + ']')==-1 ){
                            $('input[name="selected_countries"]').val($('input[name="selected_countries"]').val() + '[' + ui.item.value + '],');
                        }
                    }
                    
                    $('input[name="countries"]').val('');
                }
               });
               
               $('#categories').autocomplete({
                source: 'json_get_categories.php', 
                minLength: 2, 
                select: function(event, ui){
                        event.preventDefault();
                        $('input[name="categories"]').val(ui.item.label);
                        if ($('div#selected_categories span#cat_' + ui.item.value).length==0){
                            $('div#selected_categories').append('<span id="cat_' + ui.item.value + '">' + ui.item.label + '</span><br>');
                            if ($('input[name="selected_categories"]').val().indexOf('[' + ui.item.value + ']')==-1 ){
                                $('input[name="selected_categories"]').val($('input[name="selected_categories"]').val() + '[' + ui.item.value + '],');
                            }
                        }
                    
                        $('input[name="categories"]').val('');
                    }
                });*/
               
               $('#address_mismatch').buttonset();
               
               $('input[type="submit"]').button().click(function(event){
                    event.preventDefault();
                    $('form[name="fraud_prevention"]').submit();
               });
               
            });
        </script>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3>Fraud Prevention
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading">Fraud Prevention
                  <a href="#" data-perform="panel-dismiss" data-toggle="tooltip" title="Close Panel" class="pull-right">
                     <em class="fa fa-times"></em>
                  </a>
                  <a href="#" data-perform="panel-collapse" data-toggle="tooltip" title="Collapse Panel" class="pull-right">
                     <em class="fa fa-minus"></em>
                  </a>
               </div>
               <!-- START table-responsive-->
               
               <div class="table-responsive">
               <!-- START your table-->
<form name="fraud_prevention" method="post" action="fraud_prevention.php?action=save">
            <table class="table table-bordered table-hover">
                <tr>
                    <td>
                        <div id="status">
                            <input type="radio" id="status_on" name="status" value="1" <?php echo ($status=='1' ? ' checked="checked" ' : ''); ?> /><label for="status_on">On</label>
                            <input type="radio" id="status_off" name="status" value="0" <?php echo ($status=='0' ? ' checked="checked" ' : ''); ?> /><label for="status_off">Off</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr>
                    <td>
                        <div id="types">
                            <h3>
                                Shipping Methods
                            </h3>
                            <div style="height: auto;">
								<table class="table">
									<tr>
										<td>
											<span id="shipping_status">
												<input type="radio" id="shipping_status_on" name="shipping_status" value="1" <?php echo ($shipping_status=='1' ? ' checked="checked" ' : ''); ?> /><label for="shipping_status_on">On</label>
												<input type="radio" id="shipping_status_off" name="shipping_status" value="0" <?php echo ($shipping_status=='0' ? ' checked="checked" ' : ''); ?> /><label for="shipping_status_off">Off</label>
											</span>
										</td>
										<td>Apply Shipping Modules & Methods Check</td>
									</tr>
									<tr>
										<td colspan="2"><b>Available Shipping Modules & Methods</b></td>
									</tr>
									<tr>
										<td colspan="2">
											<div id="shipping_methods">
										<table class="table">
											<?php 
											$ship_methods_count = 0;
											foreach ($shipping_methods as $method){
												$ship_methods_count++;
											?>
													<tr>
														<td>
															<input type="checkbox" value="<?php echo $method['code']; ?>" id="ship_method_<?php echo $ship_methods_count;?>" name="ship_method_<?php echo $ship_methods_count;?>" <?php echo (!empty($method['checked']) ? ' checked="checked" ' : ''); ?> /><label for="ship_method_<?php echo $ship_methods_count;?>"><?php echo $method['title']; ?></label>
														</td>
													</tr>
											<?php } ?>
												</table>
											</div>
										</td>
									</tr>
								</table>
                            </div>
                            
                            <h3>
                                Countries
                            </h3>
                            <div>
								<table class="table">
									<tr>
										<td>
											<span id="countries_status">
												<input type="radio" id="countries_status_on" name="countries_status" value="1" <?php echo ($countries_status=='1' ? ' checked="checked" ' : ''); ?> /><label for="countries_status_on">On</label>
												<input type="radio" id="countries_status_off" name="countries_status" value="0" <?php echo ($countries_status=='0' ? ' checked="checked" ' : ''); ?> /><label for="countries_status_off">Off</label>
											</span>
										</td>
										<td>Apply Country Check</td>
									</tr>
									<tr>
										<td colspan="2">
										<?php echo tep_draw_pull_down_menu('countries[]', tep_get_countries(), '', ' multiple style="width:100%;"') ; ?>
										</td>
									</tr>
								</table>
                            </div>
                            
                            <h3>
                                IP Addresses
                            </h3>
                            <div>
								<table class="table">
									<tr>
										<td>
											<span id="ip_status">
												<input type="radio" id="ip_status_on" name="ip_status" value="1" <?php echo ($ip_status=='1' ? ' checked="checked" ' : ''); ?> /><label for="ip_status_on">On</label>
												<input type="radio" id="ip_status_off" name="ip_status" value="0" <?php echo ($ip_status=='0' ? ' checked="checked" ' : ''); ?> /><label for="ip_status_off">Off</label>
											</span>
										</td>
										<td>Apply IP Address Check</td>
									</tr>
									<tr>
										<td colspan="2">
											<input type="text" name="ip_addresses" style="width:100%;" value="<?php echo FRAUD_PREVENTION_IP_ADDRESSES; ?>" />
										</td>
									</tr>
								</table>
                            </div>
                            
                            <h3>

                                Customer Names
                            </h3>
                            <div>
								<table class="table">
									<tr>
										<td>
											<span id="customer_name_status">
												<input type="radio" id="customer_name_status_on" name="customer_name_status" value="1" <?php echo ($customer_name_status=='1' ? ' checked="checked" ' : ''); ?> /><label for="customer_name_status_on">On</label>
												<input type="radio" id="customer_name_status_off" name="customer_name_status" value="0" <?php echo ($customer_name_status=='0' ? ' checked="checked" ' : ''); ?> /><label for="customer_name_status_off">Off</label>
											</span>
										</td>
										<td>Apply Customer Name(s) Check</td>
									</tr>
									<tr>
										<td colspan="2">
											<input type="text" name="customer_names" style="width:100%;" value="<?php echo FRAUD_PREVENTION_CUSTOMER_NAMES; ?>" />
										</td>
									</tr>
								</table>
                            </div>
                            
                            <h3>

                                Addresses
                            </h3>
                            <div>
								<table class="table">
									<tr>
										<td>
											<span id="address_status">
												<input type="radio" id="address_status_on" name="address_status" value="1" <?php echo ($address_status=='1' ? ' checked="checked" ' : ''); ?> /><label for="address_status_on">On</label>
												<input type="radio" id="address_status_off" name="address_status" value="0" <?php echo ($address_status=='0' ? ' checked="checked" ' : ''); ?> /><label for="address_status_off">Off</label>
											</span>
										</td>
										<td>Apply Address Check</td>
									</tr>
									<tr>
										<td colspan="2">
											<input type="text" name="addresses" style="width:100%;" value="<?php echo FRAUD_PREVENTION_ADDRESSES; ?>" />
										</td>
									</tr>
								</table>
                            </div>
                            
                            <h3>

                                Dollar Value
                            </h3>
                            <div>
								<table class="table">
									<tr>
										<td>
											<span id="amount_status">
												<input type="radio" id="amount_status_on" name="amount_status" value="1" <?php echo ($amount_status=='1' ? ' checked="checked" ' : ''); ?> /><label for="amount_status_on">On</label>
												<input type="radio" id="amount_status_off" name="amount_status" value="0" <?php echo ($amount_status=='0' ? ' checked="checked" ' : ''); ?> /><label for="amount_status_off">Off</label>
											</span>
										</td>
										<td>Apply Amount Check</td>
									</tr>
									<tr>
										<td colspan="2">
											<input type="text" name="dollar_value" style="width:100%;" value="<?php echo FRAUD_PREVENTION_DOLLAR_VALUE; ?>" />
										</td>
									</tr>
								</table>
                            </div>
                            
                            <h3>

                                Email Address
                            </h3>
                            <div>
								<table class="table">
									<tr>
										<td>
											<span id="email_status">
												<input type="radio" id="email_status_on" name="email_status" value="1" <?php echo ($email_status=='1' ? ' checked="checked" ' : ''); ?> /><label for="email_status_on">On</label>
												<input type="radio" id="email_status_off" name="email_status" value="0" <?php echo ($email_status=='0' ? ' checked="checked" ' : ''); ?> /><label for="email_status_off">Off</label>
											</span>
										</td>
										<td>Apply Email Address(es) Check</td>
									</tr>
									<tr>
										<td colspan="2">
											<input type="text" name="email_addresses" style="width:100%;" value="<?php echo FRAUD_PREVENTION_EMAIL_ADDRESSES; ?>" />
										</td>
									</tr>
								</table>
                            </div>
                            
                            <h3>

                                Product Category List
                            </h3>
                            <div>
								<table class="table">
									<tr>
										<td>
											<span id="category_status">
												<input type="radio" id="category_status_on" name="category_status" value="1" <?php echo ($category_status=='1' ? ' checked="checked" ' : ''); ?> /><label for="category_status_on">On</label>
												<input type="radio" id="category_status_off" name="category_status" value="0" <?php echo ($category_status=='0' ? ' checked="checked" ' : ''); ?> /><label for="category_status_off">Off</label>
											</span>
										</td>
										<td>Apply Categories Check</td>
									</tr>
									<tr>
										<td colspan="2">
											<?php echo tep_draw_pull_down_menu('categories[]', tep_get_category_tree(), '', ' multiple style="width:100%;"') ; ?>
										</td>
									</tr>
								</table>
                            </div>
                            
                            <h3>

                                Shipping/Billing Mismatch
                            </h3>
                            <div>
								<table class="table">
									<tr>
										<td>
											<span id="mismatch_status">
												<input type="radio" id="mismatch_status_on" name="mismatch_status" value="1" <?php echo ($mismatch_status=='1' ? ' checked="checked" ' : ''); ?> /><label for="mismatch_status_on">On</label>
												<input type="radio" id="mismatch_status_off" name="mismatch_status" value="0" <?php echo ($mismatch_status=='0' ? ' checked="checked" ' : ''); ?> /><label for="mismatch_status_off">Off</label>
											</span>
										</td>
										<td>Apply Shipping / Billing Mismatch Check</td>
									</tr>
								</table>
     
                            </div>
                            
                            <h3>

                                Phone Number
                            </h3>
                            <div>
								<table class="table">
									<tr>
										<td>
											<span id="phone_status">
												<input type="radio" id="phone_status_on" name="phone_status" value="1" <?php echo ($phone_status=='1' ? ' checked="checked" ' : ''); ?> /><label for="phone_status_on">On</label>
												<input type="radio" id="phone_status_off" name="phone_status" value="0" <?php echo ($phone_status=='0' ? ' checked="checked" ' : ''); ?> /><label for="phone_status_off">Off</label>
											</span>
										</td>
										<td>Apply Phone Number(s) Check</td>
									</tr>
									<tr>
										<td colspan="2">
											<input type="text" name="phone_numbers" style="width:100%;" value="<?php echo FRAUD_PREVENTION_PHONE_NUMBERS; ?>" />
										</td>
									</tr>
								</table>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" value="Click to Save" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
            </table>
            <input type="hidden" name="selected_countries" value="<?php echo FRAUD_PREVENTION_COUNTRIES; ?>" />
            <input type="hidden" name="selected_categories" value="<?php echo FRAUD_PREVENTION_PRODUCT_CATEGORIES; ?>" />
            <input type="hidden" name="ship_methods_count" value="<?php echo $ship_methods_count; ?>" />
        </form>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>