<?php
/*
  $Id: edit_orders.php v5.0.5 08/27/2007 djmonkey1 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License http://www.gnu.org/licenses/
  
    Order Editor is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
  
  For Order Editor support or to post bug reports, feature requests, etc, please visit the Order Editor support thread:
  http://forums.oscommerce.com/index.php?showtopic=54032
  
  The original Order Editor contribution was written by Jonathan Hilgeman of SiteCreative.com
  
  Much of Order Editor 5.x is based on the order editing file found within the MOECTOE Suite Public Betas written by Josh DeChant
  
  Many, many people have contributed to Order Editor in many, many ways.  Thanks go to all- it is truly a community project.  
  
*/

require('includes/application_top.php');
//BOF:authorization_check
handle_authorization(basename(__FILE__));
//EOF:authorization_check
  
if ($_POST['action']=='check_for_customer'){
	$resp = '';
	$type = $_POST['type'];
	$value = $_POST['value'];
	
	if (!empty($value)){
		$sql = null;
		$temp = "select a.customers_id, a.customers_firstname, a.customers_lastname, a.customers_telephone, a.customers_email_address, b.entry_company, b.entry_street_address, b.entry_suburb, b.entry_city, b.entry_postcode, c.zone_id, c.zone_code, c.zone_name, d.countries_id, d.countries_name from customers a left join address_book b on a.customers_default_address_id=b.address_book_id left join zones c on b.entry_zone_id=c.zone_id inner join countries d on b.entry_country_id=d.countries_id ";
		if ($type=='tel'){
			$sql = tep_db_query($temp . " where a.customers_telephone='" . tep_db_input($value) . "'");
		} elseif ($type=='name'){
			list($first_name, $last_name) = explode(' ', $value);
			$sql = tep_db_query($temp . " where customers_firstname='" . tep_db_input($first_name) . "' and customers_lastname='" . tep_db_input($last_name) . "'");
		}
		if (!empty($sql)){
			$info = tep_db_fetch_array($sql);
			$resp = json_encode($info);
		}
	}
	echo $resp;
	exit();
}

if ($_POST['action']=='check_for_product'){
    //BOF:range_manager
    /*
    //EOF:range_manager
	$resp = '';
    //BOF:range_manager
    */
    $resp = json_encode('');
    //EOF:range_manager
	$item_no = $_POST['item_no'];
	
	if (!empty($item_no)){
		$sql = tep_db_query("select a.products_id, b.categories_id from products a left join products_to_categories b on a.products_id=b.products_id  where a.products_model='" . tep_db_input($item_no) . "'");
		if (tep_db_num_rows($sql)){
			$info = tep_db_fetch_array($sql);
			$resp = json_encode($info);
		}
	}
	echo $resp;
	exit();
}

if ($_POST['action']=='register_cash_n_credit'){
	require(DIR_WS_CLASSES . 'currencies.php');
	$currencies = new currencies();
	$resp = '';
	$order_id = $_POST['order_id'];
	$cash = $_POST['cash'];
	$credit = $_POST['credit'];
	$change = $_POST['change'];
	$preferred_payment_method = $_POST['preferred_payment_method'];
	
	include(DIR_FS_CATALOG . DIR_WS_MODULES . 'order_total/ot_cashamount.php');
	$ot_cash = new ot_cashamount();
	$ot_cash->amount = (float)$cash;
	$ot_cash->process();
	
	$sql_data = array(	'title' => $ot_cash->output[0]['title'], 
						'text' => $ot_cash->output[0]['text'], 
						'value' => $ot_cash->output[0]['value'], 
				);
	$sql = tep_db_query("select orders_total_id from orders_total where orders_id='" . (int)$order_id . "' and class='" . $ot_cash->code . "'");
	if (tep_db_num_rows($sql)){
		$info = tep_db_fetch_array($sql);
		tep_db_perform('orders_total', $sql_data, 'update', "orders_total_id='" . (int)$info['orders_total_id'] . "'");
	} else {
		$sql_data['orders_id'] = $order_id;
		$sql_data['class'] = $ot_cash->code;
		$sql_data['sort_order'] = $ot_cash->sort_order;
		tep_db_perform('orders_total', $sql_data);
	}
	
	include(DIR_FS_CATALOG . DIR_WS_MODULES . 'order_total/ot_creditamount.php');
	$ot_credit = new ot_creditamount();
	$ot_credit->amount = (float)$credit;
	$ot_credit->process();
	
	$sql_data = array(	'title' => $ot_credit->output[0]['title'], 
						'text' => $ot_credit->output[0]['text'], 
						'value' => $ot_credit->output[0]['value'], 
				);
	$sql = tep_db_query("select orders_total_id from orders_total where orders_id='" . (int)$order_id . "' and class='" . $ot_credit->code . "'");
	if (tep_db_num_rows($sql)){
		$info = tep_db_fetch_array($sql);
		tep_db_perform('orders_total', $sql_data, 'update', "orders_total_id='" . (int)$info['orders_total_id'] . "'");
	} else {
		$sql_data['orders_id'] = $order_id;
		$sql_data['class'] = $ot_credit->code;
		$sql_data['sort_order'] = $ot_credit->sort_order;
		tep_db_perform('orders_total', $sql_data);
	}
	
	include(DIR_FS_CATALOG . DIR_WS_MODULES . 'order_total/ot_changeamount.php');
	$ot_change = new ot_changeamount();
	$ot_change->amount = (float)$change;
	$ot_change->process();
	
	$sql_data = array(	'title' => $ot_change->output[0]['title'], 
						'text' => $ot_change->output[0]['text'], 
						'value' => $ot_change->output[0]['value'], 
				);
	$sql = tep_db_query("select orders_total_id from orders_total where orders_id='" . (int)$order_id . "' and class='" . $ot_change->code . "'");
	if (tep_db_num_rows($sql)){
		$info = tep_db_fetch_array($sql);
		tep_db_perform('orders_total', $sql_data, 'update', "orders_total_id='" . (int)$info['orders_total_id'] . "'");
	} else {
		$sql_data['orders_id'] = $order_id;
		$sql_data['class'] = $ot_change->code;
		$sql_data['sort_order'] = $ot_change->sort_order;
		tep_db_perform('orders_total', $sql_data);
	}
	
	$sql = tep_db_query("select orders_status from orders where orders_id='" . (int)$order_id . "'");
	if (tep_db_num_rows($sql)){
		$info = tep_db_fetch_array($sql);
		if ($info['orders_status']!='13'){
			tep_db_query("update orders set orders_status='13' where orders_id='" . (int)$order_id . "'");
			/*
			$sql_data = array('orders_id' => $order_id, 
							  'orders_status_id' => $info['orders_status'], 
							  'date_added' => 'now()', );
			tep_db_perform('orders_status_history', $sql_data);
			*/
		}
	}
	
	//create customer (if new): check for email id
	$customer_name = $_POST['customer_name'];
	$customer_company = $_POST['customer_company'];
	$customer_street_address = $_POST['customer_street_address'];
	$customer_suburb = $_POST['customer_suburb'];
	$customer_city = $_POST['customer_city'];
	$customer_state = $_POST['customer_state'];
	$customer_postcode = $_POST['customer_postcode'];
	$customer_country_id = $_POST['customer_country_id'];
	$customer_telephone = $_POST['customer_telephone'];
	$customer_email_address = $_POST['customer_email_address'];
	
	if (!empty($customer_email_address)){
		$sql = tep_db_query("select customers_id from customers where customers_email_address='" . tep_db_input($customer_email_address) . "'");
		if (!tep_db_num_rows($sql)){
			/*$first_name = $last_name = '';
			$temp = explode(' ', $customer_name);
			if(count($temp)){
				$first_name = $temp[0];
				$temp_index = strpos($customer_name, ' ');
				if ($temp_index!==false){
					$last_name = substr($customer_name, $temp_index + 1);
				}
			}*/

			
			$sql_data = array(
				'customers_firstname' => $customer_name, 
				'customers_lastname' => '', 
				'customers_email_address' => $customer_email_address, 
				'customers_telephone' => $customer_telephone, 
				'customers_password' => tep_encrypt_password($customer_email_address), 
			);
			tep_db_perform('customers', $sql_data);
			$customer_id = tep_db_insert_id();
			
			$sql_data = array(
				'customers_info_id' => $customer_id, 
				'customers_info_date_account_created' => 'now()', 
			);
			tep_db_perform('customers_info', $sql_data);
			
			$sql_data = array(
				'customers_id' => $customer_id, 
				'entry_company' => $customer_company, 
				'entry_firstname' => $customer_name, 
				'entry_lastname' => '', 
				'entry_street_address' => $customer_street_address, 
				'entry_suburb' => $customer_suburb, 
				'entry_postcode' => $customer_postcode, 
				'entry_city' => $customer_city, 
				'entry_state' => $customer_state, 
				'entry_country_id' => $customer_country_id, 
			);
			tep_db_perform('address_book', $sql_data);
		}
	}
	
	//set appropriate payment method
	//tep_db_query("update orders set payment_method='" . (!empty($cash) ? 'Cash' : 'Credit Card : Authorize.net') . "' where orders_id='" . (int)$order_id . "'");
	tep_db_query("update orders set payment_method='" . (!empty($cash) ? 'Cash' : 'Credit Card : ' . preferred_payment_method) . "' where orders_id='" . (int)$order_id . "'");
	
	exit();
}

// include the appropriate functions & classes
include('order_editor/functions.php');
include('order_editor/cart.php');
include('order_editor/order.php');
include('order_editor/shipping.php');
include('order_editor/http_client.php');

// Include currencies class
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

//orders status
$orders_statuses = array();
$orders_status_array = array();
$orders_status_query = tep_db_query("SELECT orders_status_id, orders_status_name FROM " . TABLE_ORDERS_STATUS . " WHERE language_id = '" . (int)$languages_id . "'");
									   
while ($orders_status = tep_db_fetch_array($orders_status_query)) {
	$orders_statuses[] = array('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
	$orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
}

$action = (isset($_GET['action']) ? $_GET['action'] : 'edit');

if (isset($action)) {
	$process = false;

	switch ($action) {
		////
		// Update Order
		case 'update_order':
			$oID = tep_db_prepare_input($_GET['oID']);
			$status = tep_db_prepare_input($_POST['status']);

			// UPDATE STATUS HISTORY & SEND EMAIL TO CUSTOMER IF NECESSARY #####
			$check_status_query = tep_db_query("SELECT customers_name, customers_email_address, orders_status, usps_track_num, usps_track_num2, ups_track_num, ups_track_num2, fedex_track_num, fedex_track_num2, dhl_track_num, dhl_track_num2, date_purchased FROM " . TABLE_ORDERS . " WHERE orders_id = '" . (int)$oID . "'");
						  
			$check_status = tep_db_fetch_array($check_status_query); 
	
			if (($check_status['orders_status'] != $_POST['status']) || (tep_not_null($_POST['comments']))) {
				tep_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status = '" . tep_db_input($_POST['status']) . "', last_modified = now() WHERE orders_id = '" . (int)$oID . "'");
		
				// Notify Customer ?
				$customer_notified = '0';
				if (isset($_POST['notify']) && ($_POST['notify'] == 'on')) {
					$notify_comments = '';
					if (isset($_POST['notify_comments']) && ($_POST['notify_comments'] == 'on')) {
						$notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $_POST['comments']) . "\n\n";
					}
					$email = STORE_NAME . "\n" .
							 EMAIL_SEPARATOR . "\n" . 
							 EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" . 
							 EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "\n" . 
							 EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]) . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE2);
			  
					tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
			  
					$customer_notified = '1';
				}
				// Notify Customer ?
			}

			//delete or update comments
		    if (is_array($_POST['update_comments'])) {
				foreach($_POST['update_comments'] as $orders_status_history_id => $comments_details) {
	  
					if (isset($comments_details['delete'])){
			             $Query = "DELETE FROM " . TABLE_ORDERS_STATUS_HISTORY . "  WHERE orders_id = '" . (int)$oID . "'  AND orders_status_history_id = '$orders_status_history_id';";tep_db_query($Query);
					} else {
						$Query = "UPDATE " . TABLE_ORDERS_STATUS_HISTORY . " SET comments = '" . $comments_details["comments"] . "' WHERE orders_id = '" . (int)$oID . "' AND orders_status_history_id = '$orders_status_history_id';";
						tep_db_query($Query);
					}
				}	
			}//end comments update section

			break;
		case 'edit':
			if (!isset($_GET['oID'])) {
				$messageStack->add(ERROR_NO_ORDER_SELECTED, 'error');
				break;
			}
			$oID = tep_db_prepare_input($_GET['oID']);
			$orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");
			$order_exists = true;
			if (!tep_db_num_rows($orders_query)) {
				$order_exists = false;
				$messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
				break;
			}
        
			$order = new manualOrder($oID);
			$shippingKey = $order->adjust_totals($oID);
			$order->adjust_zones();
        
			$cart = new manualCart();
			$cart->restore_contents($oID);
			$_SESSION['cart'] = $cart;
			$total_count = $cart->count_contents();
			$total_weight = $cart->show_weight();

			// Get the shipping quotes
			$shipping_modules = new shipping;
			$shipping_quotes = $shipping_modules->quote();
			break;
	}
}

?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php include('order_editor/css.php');  
		//because if you haven't got your css, what have you got?
		?>

		<script language="javascript" src="includes/general.js"></script>

		<?php include('order_editor/javascript_POS.php');
		//because if you haven't got your javascript, what have you got?
		?>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				obtainTotals();
                $('input[name="item_no"]').focus();
				
				if ($('select#status').val()=='13'){
					$(':input').attr('disabled', 'disabled');
				}
				
				$('input[name="update_customer_telephone"]').keypress(function(event){
					if(event.keyCode=='13'){
						check_for_customer('tel', $(this).val());
					}
				});
				$('input[name="update_customer_name"]').keypress(function(event){
					if(event.keyCode=='13'){
						check_for_customer('name', $(this).val());
					}
				});
				$(document).on('keypress', 'input[name="item_no"]', function(event){
					if (event.keyCode=='13' || event.keyCode=='9'){
						//var name_attr = $(this).attr('name');
						//var temp = name_attr.substr(name_attr.indexOf('[')+1);
						//var products_id = temp.substr(0, temp.indexOf(']'));
						//check_for_product(products_id);
                        //BOF:range manager
                        $(this).css('display', 'none').parent().append('<span id="model_notification">Processing ...</span>');
                        //EOF:range manager
						check_for_product($(this).val());
					}
				});
				
				$('#customer_details').click(function(){
					if ($(this).html()=='[ - ]') {
						$(this).html('[ + ]');
						$('#customer_details_container').css({'display': 'none'});
					} else if ($(this).html()=='[ + ]') {
						$(this).html('[ - ]');
						$('#customer_details_container').css({'display': ''});
					}
				});
				
				$('input[name="update_info_cc_amount"]').change(function(){
					var span = $('#span_payment_error');
					$(span).html('&nbsp;');
					var total = get_order_total();
					if (isNaN(parseFloat($(this).val()))){
						$(span).html('Amount: ' + $(this).val() + ' seems to be invalid');
						$(this).val('');
					} else {
						$(this).val(parseFloat($(this).val()));
						if (parseFloat($(this).val()) > parseFloat(total)){
							$(span).html('Amount: ' + $(this).val() + '  should not exceed order total');
							$(this).val('');
						}
					}
					set_tendered_amount();
				});
				
				$('input[name="cash_check_amount"]').change(function(){
					/*var span = $('#span_payment_error');
					$(span).html(' ');
					var total = get_order_total();
					if (isNaN(parseFloat($(this).val()))){
						$(span).html('Amount: ' + $(this).val() + ' seems to be invalid');
						$(this).val('');
					} else {
						$(this).val(parseFloat($(this).val()));
						if (parseFloat($(this).val()) > parseFloat(total)){
							$(span).html('Amount: ' + $(this).val() + '  should not exceed order total');
							$(this).val('');
						}
					}*/
					set_tendered_amount();
				});
				
				$('#btn_invoice').click(function(){
					$('select#status').val('13');
					$.ajax({
						url: 'edit_orders_POS.php', 
						type: 'post', 
						data: {
							order_id: '<?php echo $_GET['oID'] ?>', 
							cash: $('input[name="cash_check_amount"]').val(), 
							credit: $('input[name="update_info_cc_amount"]').val(), 
							change: $('input[name="change_amount"]').val(), 
							preferred_payment_method: $('input[name="preferred_payment_method"]').val(), 
							
							customer_name: $('input[name="update_customer_name"]').val(), 
							customer_company: $('input[name="update_customer_company"]').val(), 
							customer_street_address: $('input[name="update_customer_street_address"]').val(), 
							customer_suburb: $('input[name="update_customer_suburb"]').val(), 
							customer_city: $('input[name="update_customer_city"]').val(), 
							customer_state: $('input[name="update_customer_state"]').val(), 
							customer_postcode: $('input[name="update_customer_postcode"]').val(), 
							customer_country_id: $('select[name="update_customer_country_id"]').val(), 
							customer_telephone: $('input[name="update_customer_telephone"]').val(), 
							customer_email_address: $('input[name="update_customer_email_address"]').val(), 
							
							action: 'register_cash_n_credit'}, 
							success: function(msg){
                                //BOF:ranges manager
                                <?php if ( !empty($_GET['range_order_ref']) || (!empty($_GET['ajaxmode']) && $_GET['ajaxmode']=='1') ){ ?>
                                //location.href = 'blank_page.php';
                                location.href = 'invoice.php?oID=' + '<?php echo $_GET['oID'] ?>';
                                <?php } else { ?>
                                //EOF:ranges manager
                                var _inv = window.open('invoice.php?oID=' + '<?php echo $_GET['oID'] ?>', 'invoice');
								location.href = 'orders_POS.php';
                                _inv.focus();
                                //BOF:ranges manager
                                <?php } ?>
                                //EOF:ranges manager
							}
					});
				});
				
				$('select#cc_mm, select#cc_yyyy').change(function(){
					if ($('select#cc_mm').val()!='' && $('select#cc_yyyy').val()!=''){
						var mm = $('select#cc_mm').val();
						var yyyy = $('select#cc_yyyy').val();
						var cc_expires_val = (mm.length==1 ? '0' : '') + mm + yyyy.substring(2);
						$('input:hidden[name="cc_expires"]').val(cc_expires_val);
						updateOrdersField('cc_expires', $('input:hidden[name="cc_expires"]').val());
					}
				});
				
				$('#btn_process_cc').click(function(event){
					event.preventDefault();
					var error = '';
					if ( $('input[name="update_info_cc_amount"]').val() == '' ){
						error += "CC Amount Missing!\n";
					}
					if ( $('input[name="update_info_cc_owner"]').val() == '' ){
						error += "CC Owner Missing!\n";
					}
					if ( $('input[name="update_info_cc_type"]').val() == '' ){
						error += "CC Type Missing!\n";
					}
					if ( $('input[name="update_info_cc_number"]').val() == '' ){
						error += "CC Number Missing!\n";
					}
					if ( $('#cc_mm').val() == '' ){
						error += "CC Expiry Month Missing!\n";
					}
					if ( $('#cc_yyyy').val() == '' ){
						error += "CC Expiry Year Missing!\n";
					}
					if ( $('input[name="update_info_cc_cvv"]').val() == '' ){
						error += "CC CVV Missing!\n";
					}
					
					if (error!=''){
						alert(error);
					} else {
						$.ajax({
							url: 'payment_handler.php', 
							method: 'post', 
							dataType: 'json', 
							data: {
								preferred_payment_method: '<?php echo PREFERRED_CC_PAYMENT_METHOD_POS; ?>', 
								order_id: '<?php echo $_GET['oID']; ?>', 
								cc_owner: $('input[name="update_info_cc_owner"]').val(), 
								cc_type: $('input[name="update_info_cc_type"]').val(), 
								cc_number: $('input[name="update_info_cc_number"]').val(), 
								cc_expiry_month: $('#cc_mm').val(), 
								cc_expiry_year: $('#cc_yyyy').val(), 
								cc_cvv: $('input[name="update_info_cc_cvv"]').val()
							}, 
							success: function(resp){
								if (resp.error && resp.error!=''){
									alert(resp.error);
								}
							}
						});
					}
					
				});
				
			});
			
			function set_tendered_amount(){
				var cc_amount 				= 0;
				var cash_amount 			= 0;
				var total_tendered_amount 	= 0;
				var change_amount 			= 0;
				
				if (!isNaN(parseFloat($('input[name="cash_check_amount"]').val()))){
					cash_amount = parseFloat($('input[name="cash_check_amount"]').val());
					total_tendered_amount += cash_amount;
				}
				if ($('table#optional').attr('class')!='hidden' && !isNaN(parseFloat($('input[name="update_info_cc_amount"]').val()))){
					cc_amount = parseFloat($('input[name="update_info_cc_amount"]').val());
					total_tendered_amount += cc_amount;
				}
				//BOF:mod sep2013
				total_tendered_amount = format_amount(total_tendered_amount);
				//EOF:mod sep2013
				$('input[name="total_tendered_amount"]').val(total_tendered_amount);
				
				var total = parseFloat(get_order_total());
				/*if (parseFloat(total_tendered_amount) == parseFloat(total)){
					$('#btn_invoice').removeAttr('disabled');
				} else {
					$('#btn_invoice').attr('disabled', 'disabled');
				}*/
				var disable_flag = false;
				if (total_tendered_amount >= total){ //if total tendered amount is greater than or equal to order total
					if (cc_amount > total){ //check if cc amount is geater than order total
						disable_flag = true;
					} else if (cc_amount == total && cash_amount>0){ //check if cc amount equals to order total
						disable_flag = true;
					}
					$('input[name="change_amount"]').val( (total_tendered_amount - total).toFixed(2));
				} else { //if total tendered amount is less than order total
					disable_flag = true;
					$('input[name="change_amount"]').val('0');
				}
				
				if (disable_flag){
					$('#btn_invoice').attr('disabled', 'disabled');
				} else {
					$('#btn_invoice').removeAttr('disabled');
				}
			}
			
			//BOF:mod sep2013
			function format_amount(amount){
				return amount.toFixed(2);
			}
			//EOF:mod sep2013
			
			function get_order_total(){
				var elem_name =  $('input:hidden[value="ot_total"]').attr('name').replace(/class/, 'value');
				return $('input:hidden[name="' + elem_name + '"]').val();
				
			}
			
			function check_for_product(item_no){
				if (item_no!=''){
					$.ajax({
						url: 'edit_orders_POS.php', 
						type: 'post', 
						data: {item_no: item_no, action: 'check_for_product'}, 
						success: function(resp){
							var info = JSON.parse(resp);
							if (info){
								$.ajax({
								    <?php if ( !empty($_GET['range_order_ref']) || (!empty($_GET['ajaxmode']) && $_GET['ajaxmode']=='1') ){ ?>
								        url: 'edit_orders_add_product_POS.php?action=add_product&oID=<?php echo $_GET['oID']; ?>&ajaxmode=1',     
								    <?php } else { ?>
									url: 'edit_orders_add_product_POS.php?action=add_product&oID=<?php echo $_GET['oID']; ?>', 
                                    <?php } ?>
									type: 'post', 
									data: ({step: '5', add_product_products_id: info.products_id, add_product_categories_id: info.categories_id, add_product_quantity: '1'}), 
									success: function(resp){
									  obtainTotalsAndReload();
									  
                                        //BOF:range_manager
                                        <?php /*if ( !empty($_GET['range_order_ref']) || (!empty($_GET['ajaxmode']) && $_GET['ajaxmode']=='1') ){ ?>
                                            location.href = 'edit_orders_POS.php?oID=<?php echo $_GET['oID']; ?>&ajaxmode=1';    
                                        <?php } else { ?>
                                        //EOF:range_manager
										location.href = 'edit_orders_POS.php?oID=<?php echo $_GET['oID']; ?>';
                                        //BOF:range_manager
                                        <?php }*/ ?>
                                        //EOF:range_manager
									}
								});
							}
                            //BOF: range_manager
                             else{
                                alert('Product model not recognized!');
                                $('input[name="item_no"]').css('display', '').focus();
                                $('#model_notification').remove();
							}
                            //EOF: range_manager
						}
					});
				}
			}
			
			function check_for_customer(type, value){
				$.ajax({
					url: 'edit_orders_POS.php', 
					type: 'post', 
					data: {type: type, value: value, action: 'check_for_customer'}, 
					success: function(resp){
						var info = JSON.parse(resp);
						if (info){
							$('input[name="update_customer_name"]').val(info.customers_firstname + ' ' + info.customers_lastname);
							$('input[name="update_customer_company"]').val(info.entry_company);
							$('input[name="update_customer_street_address"]').val(info.entry_street_address);
							$('input[name="update_customer_suburb"]').val(info.entry_suburb);
							$('input[name="update_customer_city"]').val(info.entry_city);
							$('input[name="update_customer_state"]').val(info.zone_name);
							$('input[name="update_customer_postcode"]').val(info.entry_postcode);
							$('select[name="update_customer_country_id"]').val(info.countries_id);
							$('input[name="update_customer_telephone"]').val(info.customers_telephone);
							$('input[name="update_customer_email_address"]').val(info.customers_email_address);
							
							//$('input[name="update_billing_name"]').val(info.customers_firstname + ' ' + info.customers_lastname);
							//$('input[name="update_billing_company"]').val(info.entry_company);
							//$('input[name="update_billing_street_address"]').val(info.entry_street_address);
							//$('input[name="update_billing_suburb"]').val(info.entry_suburb);
							//$('input[name="update_billing_city"]').val(info.entry_city);
							//$('input[name="update_billing_state"]').val(info.zone_name);
							//$('input[name="update_billing_postcode"]').val(info.entry_postcode);
							//$('select[name="update_billing_country_id"]').val(info.countries_id);
							
							updateOrdersField('customers_name', encodeURIComponent($('input[name="update_customer_name"]').val()));
							updateOrdersField('customers_company', encodeURIComponent($('input[name="update_customer_company"]').val()));
							updateOrdersField('customers_street_address', encodeURIComponent($('input[name="update_customer_street_address"]').val()));
							updateOrdersField('customers_suburb', encodeURIComponent($('input[name="update_customer_suburb"]').val()));
							updateOrdersField('customers_city', encodeURIComponent($('input[name="update_customer_city"]').val()));
							updateOrdersField('customers_state', encodeURIComponent($('input[name="update_customer_state"]').val()));
							updateOrdersField('customers_postcode', encodeURIComponent($('input[name="update_customer_postcode"]').val()));
							update_zone('update_customer_country_id', 'update_customer_zone_id', 'customerStateInput', 'customerStateMenu');
							updateOrdersField('customers_country', $('select[name="update_customer_country_id"]').find('option:selected').text());
							updateOrdersField('customers_telephone', encodeURIComponent($('input[name="update_customer_telephone"]').val()));
							updateOrdersField('customers_email_address', encodeURIComponent($('input[name="update_customer_email_address"]').val()));
							
							//updateOrdersField('billing_name', encodeURIComponent($('input[name="update_billing_name"]').val()));
							//updateOrdersField('billing_company', encodeURIComponent($('input[name="update_billing_company"]').val()));
							//updateOrdersField('billing_street_address', encodeURIComponent($('input[name="update_billing_street_address"]').val()));
							//updateOrdersField('billing_suburb', encodeURIComponent($('input[name="update_billing_suburb"]').val()));
							//updateOrdersField('billing_city', encodeURIComponent($('input[name="update_billing_city"]').val()));
							//updateOrdersField('billing_state', encodeURIComponent($('input[name="update_billing_state"]').val()));
							//updateOrdersField('billing_postcode', encodeURIComponent($('input[name="update_billing_postcode"]').val()));
							//update_zone('update_billing_country_id', 'update_billing_zone_id', 'billingStateInput', 'billingStateMenu');
							//updateOrdersField('billing_country', $('select[name="update_billing_country_id"]').find('option:selected').text());
						}
					}
				});
			}
		</script>
        <div id="dhtmltooltip"></div>
		<script type="text/javascript">
			/***********************************************
			* Cool DHTML tooltip script- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
			* This notice MUST stay intact for legal use
			* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
			***********************************************/

			/***********************************************
			* For Order Editor
			* This has to stay here for the tooltips to work correctly
			* I tried sticking it with the rest of the javascript, but it has to be inside the <body> tag
			*
			***********************************************/

			var offsetxpoint=-60 //Customize x offset of tooltip
			var offsetypoint=20 //Customize y offset of tooltip
			var ie=document.all
			var ns6=document.getElementById && !document.all
			var enabletip=false
			if (ie||ns6)
			var tipobj=document.all? document.all["dhtmltooltip"] : document.getElementById? document.getElementById("dhtmltooltip") : ""

			function ietruebody(){
				return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
			}

			function ddrivetip(thetext, thecolor, thewidth){
				if (ns6||ie){
					if (typeof thewidth!="undefined") tipobj.style.width=thewidth+"px"
					if (typeof thecolor!="undefined" && thecolor!="") tipobj.style.backgroundColor=thecolor
					tipobj.innerHTML=thetext
					enabletip=true
					return false
				}
			}

			function positiontip(e){
				if (enabletip){
					var curX=(ns6)?e.pageX : event.clientX+ietruebody().scrollLeft;
					var curY=(ns6)?e.pageY : event.clientY+ietruebody().scrollTop;
					//Find out how close the mouse is to the corner of the window
					var rightedge=ie&&!window.opera? ietruebody().clientWidth-event.clientX-offsetxpoint : window.innerWidth-e.clientX-offsetxpoint-20
					var bottomedge=ie&&!window.opera? ietruebody().clientHeight-event.clientY-offsetypoint : window.innerHeight-e.clientY-offsetypoint-20

					var leftedge=(offsetxpoint<0)? offsetxpoint*(-1) : -1000

					//if the horizontal distance isn't enough to accomodate the width of the context menu
					if (rightedge<tipobj.offsetWidth)
						//move the horizontal position of the menu to the left by it's width
						tipobj.style.left=ie? ietruebody().scrollLeft+event.clientX-tipobj.offsetWidth+"px" : window.pageXOffset+e.clientX-tipobj.offsetWidth+"px"
					else if (curX<leftedge)
						tipobj.style.left="5px"
					else
						//position the horizontal position of the menu where the mouse is positioned
						tipobj.style.left=curX+offsetxpoint+"px"

					//same concept with the vertical position
					if (bottomedge<tipobj.offsetHeight)
						tipobj.style.top=ie? ietruebody().scrollTop+event.clientY-tipobj.offsetHeight-offsetypoint+"px" : window.pageYOffset+e.clientY-tipobj.offsetHeight-offsetypoint+"px"
					else
						tipobj.style.top=curY+offsetypoint+"px"
					tipobj.style.visibility="visible"
				}
			}

			function hideddrivetip(){
				if (ns6||ie){
					enabletip=false
					tipobj.style.visibility="hidden"
					tipobj.style.left="-1000px"
					tipobj.style.backgroundColor='white'
					tipobj.style.width='200'
				}
			}

			document.onmousemove=positiontip
			function chargecard() {
				var myform = document.edit_order;
				myform.charge.value='yes';
				myform.submit();
			}
		</script>
<!-- header //-->
<?php
        //BOF:range_manager
        //if (empty($range_order_ref)){ 
        if (!( !empty($_GET['range_order_ref']) || (!empty($_GET['ajaxmode']) && $_GET['ajaxmode']=='1') )){
        //EOF:range_manager
        require(DIR_WS_INCLUDES . 'header.php');
        //BOF:range_manager
        } 
        //EOF:range_manager
        ?>
<!-- header_eof //-->

<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3><?php echo sprintf(HEADING_TITLE, $oID, tep_datetime_short($order->info['date_purchased'])); ?>
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading"><?php echo sprintf(HEADING_TITLE, $oID, tep_datetime_short($order->info['date_purchased'])); ?>
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
<table class="table table-bordered table-hover">
			<tr>
				<?php 
                //BOF:range_manager
                //if (empty($range_order_ref)){ 
                if (!( !empty($_GET['range_order_ref']) || (!empty($_GET['ajaxmode']) && $_GET['ajaxmode']=='1') )){
                //EOF:range_manager ?>
				<td>
					<table class="table table-bordered table-hover">
					<!-- left_navigation //-->
					<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					<!-- left_navigation_eof //-->
					</table>
				</td>
                <?php
                //BOF:range_manager
                } 
                //EOF:range_manager
                ?>
				<!-- body_text //-->
				<td>
				<?php
				if (($action == 'edit') && ($order_exists == true)) {
					echo tep_draw_form('edit_order', FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action')) . 'action=update_order');
				?>
						   
					<div id="ordersMessageStack">
					<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
					</div>
	   	   
					<?php if (ORDER_EDITOR_USE_AJAX != 'true') { ?>
					<!-- Begin Update Block, only for non-ajax use -->
					<div class="updateBlock">
						<div class="update1"><?php echo HINT_PRESS_UPDATE; ?></div>
						<div class="update2">&nbsp;</div>
						<div class="update3">&nbsp;</div>
						<div class="update4" align="center"><?php echo ENTRY_SEND_NEW_ORDER_CONFIRMATION; ?>&nbsp;<?php echo tep_draw_checkbox_field('nC1', '', false); ?></div>
						<div class="update5" align="center"><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></div>
					</div>
					<br>
					<br>
					<!-- End of Update Block -->
					<?php } ?>


					<!-- customer_info bof //-->
            
					<table class="table table-bordered table-hover">
						<tr>
							<td>
								<!-- customer_info bof //-->
								<table class="table table-bordered table-hover">
									<tr> 
										<td colspan="4"><?php echo ENTRY_CUSTOMER; ?>&nbsp;<span id="customer_details" style="font-weight:bolder;cursor:pointer;">[ - ]</span></td>
									</tr>
									<tr id="customer_details_container">
										<td colspan="4">
											<table>
												<tr> 
													<td align="right" nowrap><?php echo ENTRY_NAME; ?></td>
													<td colspan="3"><input name="update_customer_name" size="37" value="<?php echo stripslashes($order->customer['name']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_name', encodeURIComponent(this.value))"<?php } ?>></td>
												</tr>
												<tr> 
													<td align="right"><?php echo ENTRY_COMPANY; ?></td>
													<td colspan="3"><input name="update_customer_company" size="37" value="<?php echo stripslashes($order->customer['company']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_company', encodeURIComponent(this.value))"<?php } ?>></td>
												</tr>
												<tr> 
													<td align="right"><?php echo ENTRY_STREET_ADDRESS; ?></td>
													<td colspan="3"><input name="update_customer_street_address" size="37" value="<?php echo stripslashes($order->customer['street_address']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_street_address', encodeURIComponent(this.value))"<?php } ?>></td>
												</tr>
												<tr> 
													<td align="right"><?php echo ENTRY_SUBURB; ?></td>
													<td colspan="3"><input name="update_customer_suburb" size="37" value="<?php echo stripslashes($order->customer['suburb']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_suburb', encodeURIComponent(this.value))"<?php } ?>></td>
												</tr>
												<tr> 
													<td align="right" nowrap><?php echo ENTRY_CITY_STATE; ?></td>
													<td colspan="2"><input name="update_customer_city" size="15" value="<?php echo stripslashes($order->customer['city']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_city', encodeURIComponent(this.value))"<?php } ?>>,</td>
													<td><span id="customerStateMenu">
													<?php if (ORDER_EDITOR_USE_AJAX == 'true') {
													echo tep_draw_pull_down_menu('update_customer_zone_id', tep_get_country_zones($order->customer['country_id']), $order->customer['zone_id'], 'style="width: 200px;" onChange="updateOrdersField(\'customers_state\', this.options[this.selectedIndex].text);"'); 
													} else {
													echo tep_draw_pull_down_menu('update_customer_zone_id', tep_get_country_zones($order->customer['country_id']), $order->customer['zone_id'], 'style="width: 200px;"');
													}?></span><span id="customerStateInput"><input name="update_customer_state" size="15" value="<?php echo stripslashes($order->customer['state']); ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_state', encodeURIComponent(this.value))"<?php } ?>></span></td>
												</tr>
												<tr> 
													<td align="right"><?php echo ENTRY_POST_CODE; ?></td>
													<td><input name="update_customer_postcode" size="5" value="<?php echo $order->customer['postcode']; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_postcode', encodeURIComponent(this.value))"<?php } ?>></td>
													<td align="right" nowrap><?php echo ENTRY_COUNTRY; ?></td>
													<td>
													<?php if (ORDER_EDITOR_USE_AJAX == 'true') {
													echo tep_draw_pull_down_menu('update_customer_country_id', tep_get_countries(), $order->customer['country_id'], 'onChange="update_zone(\'update_customer_country_id\', \'update_customer_zone_id\', \'customerStateInput\', \'customerStateMenu\'); updateOrdersField(\'customers_country\', this.options[this.selectedIndex].text);"'); 
													} else {
													echo tep_draw_pull_down_menu('update_customer_country_id', tep_get_countries(), $order->customer['country_id'], 'onChange="update_zone(\'update_customer_country_id\', \'update_customer_zone_id\', \'customerStateInput\', \'customerStateMenu\');"'); 
													} ?></td>
												</tr>
												<tr> 
													<td colspan="4"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
												</tr>
												<tr> 
													<td align="right"><?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
													<td colspan="3"><input name="update_customer_telephone" size="15" value="<?php echo $order->customer['telephone']; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_telephone', encodeURIComponent(this.value))"<?php } ?>></td>
												</tr>
												<tr> 
													<td align="right"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
													<td colspan="3"><input name="update_customer_email_address" size="35" value="<?php echo $order->customer['email_address']; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('customers_email_address', encodeURIComponent(this.value))"<?php } ?>></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
			
							</td>
							<td>&nbsp;</td>
							<td>
								<table class="table table-bordered table-hover">
								</table>
							</td>
						</tr>
					</table>
					<div id="productsMessageStack">
					<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
					</div>
	
					<div width="100%"> 
						<a name="products"></a>
						<!-- product_listing bof //-->
                        <table class="table table-bordered table-hover" id="productsTable">
							<tr>
								<td><div align="center"><?php echo TABLE_HEADING_DELETE; ?></div></td>
								<td><div align="center"><?php echo TABLE_HEADING_QUANTITY; ?></div></td>
								<td><div align="center"><?php echo 'Item #'; ?></div></td>
								<td><?php echo TABLE_HEADING_PRODUCTS; ?></td>
								<td><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
								<td><?php echo TABLE_HEADING_TAX; ?></td>
								<td onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_BASE_PRICE); ?>')"; onMouseout="hideddrivetip()"><?php  echo TABLE_HEADING_BASE_PRICE; ?> 
									<script language="JavaScript" type="text/javascript">
									<!--
									document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
									//-->
									</script>
								</td>
								<td onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_PRICE_EXCL); ?>')"; onMouseout="hideddrivetip()"><?php  echo TABLE_HEADING_UNIT_PRICE; ?> 
									<script language="JavaScript" type="text/javascript">
									<!--
									document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
									//-->
									</script>
								</td>
								<td onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_PRICE_INCL); ?>')"; onMouseout="hideddrivetip()"><?php  echo TABLE_HEADING_UNIT_PRICE_TAXED; ?>
									<script language="JavaScript" type="text/javascript">
									<!--
									document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
									//-->
									</script>
								</td>
								<td onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_TOTAL_EXCL); ?>')"; onMouseout="hideddrivetip()"><?php  echo TABLE_HEADING_TOTAL_PRICE; ?> 
									<script language="JavaScript" type="text/javascript">
									<!--
									document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
									//-->
									</script>
								</td>
								<td onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_TOTAL_INCL); ?>')"; onMouseout="hideddrivetip()"><?php  echo TABLE_HEADING_TOTAL_PRICE_TAXED; ?> 
									<script language="JavaScript" type="text/javascript">
									<!--
									document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
									//-->
									</script>
								</td>
							</tr>
							<?php

							if (sizeof($order->products)) {
								for ($i=0; $i<sizeof($order->products); $i++) {
									$orders_products_id = $order->products[$i]['orders_products_id'];  ?>
			   
							<tr>
								<td valign="top"><div align="center"><input type="checkbox" name="<?php echo "update_products[" . $orders_products_id . "][delete]"; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onClick="updateProductsField('delete', '<?php echo $orders_products_id; ?>', 'delete', this.checked, this)"<?php } ?>></div></td>
								<td valign="top"><div align="center"><input name="<?php echo "update_products[" . $orders_products_id . "][qty]"; ?>" size="2" onKeyUp="updatePrices('qty', '<?php echo $orders_products_id; ?>')" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('reload1', '<?php echo $orders_products_id; ?>', 'products_quantity', encodeURIComponent(this.value))"<?php } ?> value="<?php echo $order->products[$i]['qty']; ?>" id="<?php echo "update_products[" . $orders_products_id . "][qty]"; ?>"></div></td>
								<td valign="top"><div align="center"><input name="<?php echo "update_products[" . $orders_products_id . "][item_no]"; ?>" value="<?php echo $order->products[$i]['model']; ?>" readonly ></div></td>
								<td valign="top"><input readonly name="<?php echo "update_products[" . $orders_products_id . "][name]"; ?>" size="50" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('update', '<?php echo $orders_products_id; ?>', 'products_name', encodeURIComponent(this.value))"<?php } ?> value='<?php echo oe_html_quotes($order->products[$i]['name']); ?>'>
								<?php
								// Has Attributes?
								if (isset($order->products[$i]['attributes']) && (sizeof($order->products[$i]['attributes']) > 0)) {
									for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
										$orders_products_attributes_id = $order->products[$i]['attributes'][$j]['orders_products_attributes_id'];
										if (ORDER_EDITOR_USE_AJAX == 'true') {
											echo '<br><nobr><small>&nbsp;<i> - ' . "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][option]' size='6' value='" . oe_html_quotes($order->products[$i]['attributes'][$j]['option']) . "' onChange=\"updateAttributesField('simple', 'products_options', '" . $orders_products_attributes_id . "', '" . $orders_products_id . "', encodeURIComponent(this.value))\">" . ': ' . "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][value]' size='10' value='" . oe_html_quotes($order->products[$i]['attributes'][$j]['value']) . "' onChange=\"updateAttributesField('simple', 'products_options_values', '" . $orders_products_attributes_id . "', '" . $orders_products_id . "', encodeURIComponent(this.value))\">" . ': ' . "</i><input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][prefix]' size='1' id='p" . $orders_products_id . "_" . $orders_products_attributes_id . "_prefix' value='" . $order->products[$i]['attributes'][$j]['prefix'] . "' onKeyUp=\"updatePrices('att_price', '" . $orders_products_id . "')\" onChange=\"updateAttributesField('hard', 'price_prefix', '" . $orders_products_attributes_id . "', '" . $orders_products_id . "', encodeURIComponent(this.value))\">" . ': ' . "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][price]' size='7' value='" . $order->products[$i]['attributes'][$j]['price'] . "' onKeyUp=\"updatePrices('att_price', '" . $orders_products_id . "')\" onChange=\"updateAttributesField('hard', 'options_values_price', '" . $orders_products_attributes_id . "', '" . $orders_products_id . "', encodeURIComponent(this.value))\" id='p". $orders_products_id . "a" . $orders_products_attributes_id . "'>";
										} else {
											echo '<br><nobr><small>&nbsp;<i> - ' . "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][option]' size='6' value='" . oe_html_quotes($order->products[$i]['attributes'][$j]['option']) . "'>" . ': ' . "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][value]' size='10' value='" . oe_html_quotes($order->products[$i]['attributes'][$j]['value']) . "'>" . ': ' . "</i><input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][prefix]' size='1' id='p" . $orders_products_id . "_" . $orders_products_attributes_id . "_prefix' value='" . $order->products[$i]['attributes'][$j]['prefix'] . "' onKeyUp=\"updatePrices('att_price', '" . $orders_products_id . "')\">" . ': ' . "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][price]' size='7' value='" . $order->products[$i]['attributes'][$j]['price'] . "' onKeyUp=\"updatePrices('att_price', '" . $orders_products_id . "')\" id='p". $orders_products_id . "a" . $orders_products_attributes_id . "'>";
										}
										echo '</small></nobr>';
									}  //end for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
									//Has downloads?
  
									if (DOWNLOAD_ENABLED == 'true') {
										$downloads_count = 1;
										$d_index = 0;
										$download_query_raw ="SELECT orders_products_download_id, orders_products_filename, download_maxdays, download_count FROM " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " WHERE orders_products_id='" . $orders_products_id . "' AND orders_id='" . (int)$oID . "' ORDER BY orders_products_download_id";

										$download_query = tep_db_query($download_query_raw);
	
										//
										if (isset($downloads->products)) unset($downloads->products);
										//
	
										if (tep_db_num_rows($download_query) > 0) {
											while ($download = tep_db_fetch_array($download_query)) {
	
												$downloads->products[$d_index] = array(
														'id' => $download['orders_products_download_id'],
														'filename' => $download['orders_products_filename'],
														'maxdays' => $download['download_maxdays'],
														'maxcount' => $download['download_count']);
	
												$d_index++; 
											} 
										} 
	
										if (isset($downloads->products) && (sizeof($downloads->products) > 0)) {
											for ($mm=0; $mm<sizeof($downloads->products); $mm++) {  
												$id =  $downloads->products[$mm]['id'];
												echo '<br><small>';
												echo '<nobr>' . ENTRY_DOWNLOAD_COUNT . $downloads_count . "";
												echo ' </nobr><br>' . "\n";

												if (ORDER_EDITOR_USE_AJAX == 'true') {
													echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_FILENAME . ": <input name='update_downloads[" . $id . "][filename]' size='12' value='" . $downloads->products[$mm]['filename'] . "' onChange=\"updateDownloads('orders_products_filename', '" . $id . "', '" . $orders_products_id . "', this.value)\">";
													echo ' </nobr><br>' . "\n";
													echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_MAXDAYS . ": <input name='update_downloads[" . $id . "][maxdays]' size='6' value='" . $downloads->products[$mm]['maxdays'] . "' onChange=\"updateDownloads('download_maxdays', '" . $id . "', '" . $orders_products_id . "', this.value)\">";
													echo ' </nobr><br>' . "\n";
													echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_MAXCOUNT . ": <input name='update_downloads[" . $id . "][maxcount]' size='6' value='" . $downloads->products[$mm]['maxcount'] . "' onChange=\"updateDownloads('download_count', '" . $id . "', '" . $orders_products_id . "', this.value)\">";
												} else {
													echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_FILENAME . ": <input name='update_downloads[" . $id . "][filename]' size='12' value='" . $downloads->products[$mm]['filename'] . "'>";
													echo ' </nobr><br>' . "\n";
													echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_MAXDAYS . ": <input name='update_downloads[" . $id . "][maxdays]' size='6' value='" . $downloads->products[$mm]['maxdays'] . "'>";
													echo ' </nobr><br>' . "\n";
													echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_MAXCOUNT . ": <input name='update_downloads[" . $id . "][maxcount]' size='6' value='" . $downloads->products[$mm]['maxcount'] . "'>";
												}

												echo ' </nobr>' . "\n";
												echo '<br></small>';
												$downloads_count++;
											} //end  for ($mm=0; $mm<sizeof($download_query); $mm++) {
										}
									} //end download
								} //end if (sizeof($order->products[$i]['attributes']) > 0) {
								?>
								</td>
								<td><input readonly name="<?php echo "update_products[" . $orders_products_id . "][model]"; ?>" size="12" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('update', '<?php echo $orders_products_id; ?>', 'products_model', encodeURIComponent(this.value))"<?php } ?> value="<?php echo $order->products[$i]['model']; ?>"></td>
								<td><input readonly name="<?php echo "update_products[" . $orders_products_id . "][tax]"; ?>" size="5" onKeyUp="updatePrices('tax', '<?php echo $orders_products_id; ?>')" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('reload1', '<?php echo $orders_products_id; ?>', 'products_tax', encodeURIComponent(this.value))"<?php } ?> value="<?php echo tep_display_tax_value($order->products[$i]['tax']); ?>" id="<?php echo "update_products[" . $orders_products_id . "][tax]"; ?>">%</td>
								<td><input readonly name="<?php echo "update_products[" . $orders_products_id . "][price]"; ?>" size="5" onKeyUp="updatePrices('price', '<?php echo $orders_products_id; ?>')" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')"<?php } ?> value="<?php echo number_format((float)$order->products[$i]['price'], 4, '.', ''); ?>" id="<?php echo "update_products[" . $orders_products_id . "][price]"; ?>"></td>
								<td><input readonly name="<?php echo "update_products[" . $orders_products_id . "][final_price]"; ?>" size="5" onKeyUp="updatePrices('final_price', '<?php echo $orders_products_id; ?>')" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')"<?php } ?> value="<?php echo number_format((float)$order->products[$i]['final_price'], 4, '.', ''); ?>" id="<?php echo "update_products[" . $orders_products_id . "][final_price]"; ?>"></td>
								<td><input readonly name="<?php echo "update_products[" . $orders_products_id . "][price_incl]"; ?>" size="5" value="<?php echo number_format((float)($order->products[$i]['final_price'] * (($order->products[$i]['tax']/100) + 1)), 4, '.', ''); ?>" onKeyUp="updatePrices('price_incl', '<?php echo $orders_products_id; ?>')" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')"<?php } ?> id="<?php echo "update_products[" . $orders_products_id . "][price_incl]"; ?>"></td>
								<td><input readonly name="<?php echo "update_products[" . $orders_products_id . "][total_excl]"; ?>" size="5" value="<?php echo number_format((float)$order->products[$i]['final_price'] * $order->products[$i]['qty'], 4, '.', ''); ?>" onKeyUp="updatePrices('total_excl', '<?php echo $orders_products_id; ?>')" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')"<?php } ?> id="<?php echo "update_products[" . $orders_products_id . "][total_excl]"; ?>"></td>
								<td><input readonly name="<?php echo "update_products[" . $orders_products_id . "][total_incl]"; ?>" size="5" value="<?php echo number_format((float)(($order->products[$i]['final_price'] * (($order->products[$i]['tax']/100) + 1))) * $order->products[$i]['qty'], 4, '.', ''); ?>" onKeyUp="updatePrices('total_incl', '<?php echo $orders_products_id; ?>')" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')"<?php } ?> id="<?php echo "update_products[" . $orders_products_id . "][total_incl]"; ?>"></td>
							</tr>
							<?php    
									$modelnumber = $order->products[$i]['model'];
									$wpcheck = substr($modelnumber, 0, 2);
									if ($wpcheck == 'WP' || $wpcheck=='wp') {
										$modelnum = substr($modelnumber, 2);
										$ch = curl_init('https://www.wpsorders.com/wpsonline/u1ITEM1.pgm?DEALER=D2040546&ITEM=' . $modelnum . '&OPTION=stock');
										curl_setopt($ch, CURLOPT_NOBODY, false);
										curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
										$response = curl_exec($ch);
										//$response = curl_getinfo($ch);
										curl_close($ch);
										$num_search = "SS" . $modelnumber;
										//echo $num_search;
										$product_quantity_query = tep_db_query("SELECT products_quantity FROM " . TABLE_PRODUCTS . " WHERE products_model = '" . $num_search . "'");
										//$product_quantity_query = tep_db_query("SELECT products_quantity, products_model FROM " . TABLE_PRODUCTS . " WHERE products_model LIKE '%" . $num_search . "%'");
										while ($order_quantity = tep_db_fetch_array($product_quantity_query)) {
											$quantity[] = array('qty' => $order_quantity['products_quantity'], 'model' => $order_quantity['products_model']);
										}
										$qty = $quantity[0]['qty'];
										if ($qty == NULL) $qty = 0;
										/*print_r($quantity);
										echo $qty;*/
										$stock_numbers = explode("|", $response);

										echo '<tr>';
										echo '<td align="right" valign="top"></td>';
										echo '<td align="right" valign="top"></td>';
										echo '<td align="left" valign="top">Model No.:' . $stock_numbers[0] . ' | Jazz: ' . $qty  . ' | ID:' . $stock_numbers[1] . ' | CA:' . $stock_numbers[2] . ' | TN:' . $stock_numbers[3] . ' | PA:' . $stock_numbers[4] . ' | IN:' . $stock_numbers[5] . '</td>';
										echo '</tr>';
									}
								}
							} else {
								//the order has no products
							?>
							<tr>
								<td colspan="10" style="padding: 20px 0 20px 0;"><?php echo TEXT_NO_ORDER_PRODUCTS; ?></td>
							</tr>
							<tr> 
								<td colspan="10"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
							</tr>
							<?php
							}
							?>
						</table><!-- product_listing_eof //-->
						<div id="totalsBlock">
							<table class="table table-bordered table-hover">
								<tr>
									<td>
										<table class="table table-bordered table-hover">
											<tr>
												<td>
													<br>
													<div>
														<input type="hidden" name="subaction" value="">
														<input name="item_no" id="item_no" />
													</div>
													<br>
												</td>
             
												<!-- order_totals bof //-->
												<td align="right" rowspan="2">
													<table class="table table-bordered table-hover">
														<tr>
															<td onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_TOTALS); ?>')"; onMouseout="hideddrivetip()"> 
																<script language="JavaScript" type="text/javascript">
																<!--
																document.write("<img src=\"images/icon_info.gif\" border= \"0\" width=\"13\" height=\"13\">");
																//-->
																</script>
															</td>
															<td><?php echo TABLE_HEADING_OT_TOTALS; ?></td>
															<td colspan="2" nowrap><?php echo TABLE_HEADING_OT_VALUES; ?></td>
														</tr>
														<?php
														for ($i=0; $i<sizeof($order->totals); $i++) {
  															$id = $order->totals[$i]['class'];
	
															if ($order->totals[$i]['class'] == 'ot_shipping') {
																if (tep_not_null($order->info['shipping_id'])) {
																	$shipping_module_id = $order->info['shipping_id'];
																} else {
																	//here we could create logic to attempt to determine the shipping module used if it's not in the database
																	$shipping_module_id = '';
																}
															} else {
																$shipping_module_id = '';
															} //end if ($order->totals[$i]['class'] == 'ot_shipping') {
	 
															$rowStyle = (($i % 2) ? 'dataTableRowOver' : 'dataTableRow');
															if ( ($order->totals[$i]['class'] == 'ot_total') || ($order->totals[$i]['class'] == 'ot_subtotal') || ($order->totals[$i]['class'] == 'ot_tax') || ($order->totals[$i]['class'] == 'ot_loworderfee') ) {
																echo '                  <tr class="' . $rowStyle . '">' . "\n";
																if ($order->totals[$i]['class'] != 'ot_total') {
																	echo '                    <td>
																	<script language="JavaScript" type="text/javascript">
																	<!--
																	document.write("<span id=\"update_totals['.$i.']\"><a href=\"javascript:setCustomOTVisibility(\'update_totals['.($i+1).']\', \'visible\', \'update_totals['.$i.']\');\"><img src=\"order_editor/images/plus.gif\" border=\"0\" alt=\"' . IMAGE_ADD_NEW_OT . '\" title=\"' . IMAGE_ADD_NEW_OT . '\"></a></span>");
																	//-->
																	</script></td>' . "\n";
																} else {
																	echo '                    <td>&nbsp;</td>' . "\n";
																}
      
																echo '                    <td align="right"><input name="update_totals['.$i.'][title]" value=\'' . trim($order->totals[$i]['title']) . '\' readonly="readonly"></td>' . "\n";
	  
																if ($order->info['currency'] != DEFAULT_CURRENCY) echo '                    <td>&nbsp;</td>' . "\n";
																echo '                    <td align="right">' . $order->totals[$i]['text'] . '<input name="update_totals['.$i.'][value]" type="hidden" value="' . number_format((float)$order->totals[$i]['value'], 2, '.', '') . '"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"></td>' . "\n" .'                  </tr>' . "\n";
															} else {
																if ($i % 2) {
																	echo '                  	    <script language="JavaScript" type="text/javascript">
																	<!--
																	document.write("<tr class=\"' . $rowStyle . '\" id=\"update_totals['.$i.']\" style=\"visibility: hidden; display: none;\"><td class=\"dataTableContent\" valign=\"middle\" height=\"15\"><a href=\"javascript:setCustomOTVisibility(\'update_totals['.($i).']\', \'hidden\', \'update_totals['.($i-1).']\');\"><img src=\"order_editor/images/minus.gif\" border=\"0\" alt=\"' . IMAGE_REMOVE_NEW_OT . '\" title=\"' . IMAGE_REMOVE_NEW_OT . '\"></a></td>");
																	//-->
																	</script>
			 
																	<noscript><tr class="' . $rowStyle . '" id="update_totals['.$i.']" >' . "\n" .
																	'                    <td></td></noscript>' . "\n";
																} else {
																	echo '                  <tr class="' . $rowStyle . '">' . "\n" .
																			'                    <td>
																			<script language="JavaScript" type="text/javascript">
																			<!--
																			document.write("<span id=\"update_totals['.$i.']\"><a href=\"javascript:setCustomOTVisibility(\'update_totals['.($i+1).']\', \'visible\', \'update_totals['.$i.']\');\"><img src=\"order_editor/images/plus.gif\" border=\"0\" alt=\"' . IMAGE_ADD_NEW_OT . '\" title=\"' . IMAGE_ADD_NEW_OT . '\"></a></span>");
																			//-->
																			</script></td>' . "\n";
																}

       if (ORDER_EDITOR_USE_AJAX == 'true') {
	  echo '                    <td align="right"><input name="update_totals['.$i.'][title]" id="'.$id.'[title]" value="' . strip_tags(trim($order->totals[$i]['title'])) . '" onChange="obtainTotals()"></td>' . "\n" .
           '                    <td align="right"><input name="update_totals['.$i.'][value]" id="'.$id.'[value]" value="' . number_format((float)$order->totals[$i]['value'], 2, '.', '') . '" size="6" onChange="obtainTotals()"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"><input name="update_totals['.$i.'][id]" type="hidden" value="' . $shipping_module_id . '" id="' . $id . '[id]"></td>' . "\n";
		   } else {
	  echo '                    <td align="right"><input name="update_totals['.$i.'][title]" id="'.$id.'[title]" value="' . trim($order->totals[$i]['title']) . '"></td>' . "\n" .
           '                    <td align="right"><input name="update_totals['.$i.'][value]" id="'.$id.'[value]" value="' . number_format((float)$order->totals[$i]['value'], 2, '.', '') . '" size="6"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"><input name="update_totals['.$i.'][id]" type="hidden" value="' . $shipping_module_id . '" id="' . $id . '[id]"></td>' . "\n";
		   }
		   
      if ($order->info['currency'] != DEFAULT_CURRENCY) echo '                    <td align="right" class="dataTableContent" nowrap>' . $order->totals[$i]['text'] . '</td>' . "\n";
      echo '                  </tr>' . "\n";
    }
  }
?>
                </table>
			  </td>
                <!-- order_totals_eof //-->
              </tr> 
              <tr>
                <td>
                
            </td>
              </tr> 
            </table>
		  
		  </td></tr>
		 </table> 
	  </div>
    </div> <!-- this is end of the master div for the whole totals/shipping area -->
	<?php

	?>
						<div id="cashCheckBlock">
							<table class="table table-bordered table-hover">
								<tr>
									<td colspan="2">
										<span id="span_payment_error">&nbsp;</span>
									</td>
								</tr>
								<tr>
									<td>Cash / Check Amount</td>
									<td>
										<input type="text" name="cash_check_amount" />
									</td>
								</tr>
								<tr>
									<td><?php echo 'CC Amount'; ?></td>
									<td>
										<input name="update_info_cc_amount">
									</td>
								</tr>
								<tr>
									<td><?php echo 'CC Owner'; ?></td>
									<td>
										<input name="update_info_cc_owner">
									</td>
								</tr>
								<tr>
									<td><?php echo 'CC Type' ?></td>
									<td>
										<select name="update_info_cc_type">
											<option value="" />
											<option value="VISA">Visa</option>
											<option value="MASTERCARD">MasterCard</option>
											<option value="DISCOVER">Discover Card</option>
											<option value="AMEX">American Express</option>
											
										</select>
									</td>
								</tr>
								<tr>
									<td><?php echo 'CC#'; ?></td>
									<td><input name="update_info_cc_number" value="<?php echo $order->info['cc_number']; ?>" <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>onChange="updateOrdersField('cc_number', encodeURIComponent(this.value))"<?php } ?>></td>
								</tr>
								<tr>
									<td><?php echo 'CC Expiration'; ?></td>
									<td>
										<select id="cc_mm">
										<?php
										$cc_expires = $order->info['cc_expires'];
										$mm = (int)substr($cc_expires, 0, 2);
										$yyyy = (int)('20' . substr($cc_expires, -2));
										echo '<option value="">MM</option>';
										for($i=1; $i<=12; $i++){
											echo '<option value="' . $i . '"' . ($i==$mm ? ' selected="selected" ' : '') . '>' . str_pad($i, 2, '0', STR_PAD_LEFT) . '</option>';
										}
										?>
										</select>
										<select id="cc_yyyy">
										<?php
										$cur_year = (int)date('Y', time());
										echo '<option value="">YYYY</option>';
										for($i=$cur_year; $i<=$cur_year+5; $i++){
											echo '<option value="' . $i . '"' . ($i==$yyyy ? ' selected="selected" ' : '') . '>' . $i . '</option>';
										}
										?>
										</select>
										<input type="hidden" name="cc_expires" value="" />
										<input type="hidden" name="preferred_payment_method" value="<?php echo PREFERRED_CC_PAYMENT_METHOD_POS; ?>" />
									</td>
								</tr>
								<tr>
									<td><?php echo 'CVV' ?></td>
									<td><input name="update_info_cc_cvv"></td>
								</tr>
								<tr>
									<td>Total Tendered Amount</td>
									<td>
										<input type="text" name="total_tendered_amount" readonly />
									</td>
								</tr>
								<tr>
									<td>Change/Return</td>
									<td>
										<input type="text" name="change_amount" readonly />
									</td>
								</tr>
							</table>
<!-- MOD -->
<!-- MOD -->
						</div>

	<?php if (ORDER_EDITOR_USE_AJAX != 'true') { ?> 
    <!-- Begin Update Block, only for non-javascript browsers -->

	  <br>
            <div class="updateBlock">
              <div class="update1"><?php echo HINT_PRESS_UPDATE; ?></div>
              <div class="update2">&nbsp;</div>
              <div class="update3">&nbsp;</div>
              <div class="update4" align="center"><?php echo ENTRY_SEND_NEW_ORDER_CONFIRMATION; ?>&nbsp;<?php echo tep_draw_checkbox_field('nC1', '', false); ?></div>
              <div class="update5" align="center"><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></div>
           </div>
		  
	       <br>
            <div><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></div>
	 
	 <!-- End of Update Block -->  
	 <?php } ?>
		
	  <div id="historyMessageStack">
	    <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
	  </div>

    <div id="commentsBlock">
    <table class="table table-bordered table-hover" id="commentsTable">
     <tr>
      <td align="left"><?php echo TABLE_HEADING_DELETE; ?></td>
      <td align="left"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
      <td align="left"><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></td>
      <td align="left"><?php echo TABLE_HEADING_STATUS; ?></td>
      <?php /* <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
      <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_COMMENTS; ?></td> */ ?>
    </tr>
    <?php
      $orders_history_query = tep_db_query("SELECT orders_status_history_id, orders_status_id, date_added, customer_notified, comments 
                                            FROM " . TABLE_ORDERS_STATUS_HISTORY . " 
									        WHERE orders_id = '" . (int)$oID . "' 
									        ORDER BY date_added");
        if (tep_db_num_rows($orders_history_query)) {
          while ($orders_history = tep_db_fetch_array($orders_history_query)) {
          
		   $r++;
           $rowClass = ((($r/2) == (floor($r/2))) ? 'dataTableRowOver' : 'dataTableRow');
        
	      if (ORDER_EDITOR_USE_AJAX == 'true') { 
		   echo '  <tr class="' . $rowClass . '" id="commentRow' . $orders_history['orders_status_history_id'] . '" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this, \'' . $rowClass . '\')">' . "\n" .
         '	  <td align="center"><div id="do_not_delete"><input name="update_comments[' . $orders_history['orders_status_history_id'] . '][delete]" type="checkbox" onClick="updateCommentsField(\'delete\', \'' . $orders_history['orders_status_history_id'] . '\', this.checked, \'\', this)"></div></td>' . "\n" . 
		 '    <td align="left" width="10"> </td>' . "\n" .
         '    <td align="center">' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n" .
         '    <td align="left"> </td>' . "\n" .
         '    <td align="center">';
		 } else {
		 echo '  <tr class="' . $rowClass . '" id="commentRow' . $orders_history['orders_status_history_id'] . '" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this, \'' . $rowClass . '\')">' . "\n" .
         '	  <td align="center"><div id="do_not_delete"><input name="update_comments[' . $orders_history['orders_status_history_id'] . '][delete]" type="checkbox"></div></td>' . "\n" . 
		 '    <td align="left" width="10"> </td>' . "\n" .
         '    <td align="center">' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n" .
         '    <td align="left" width="10"> </td>' . "\n" .
         '    <td align="center">';
		 }
      
	   if ($orders_history['customer_notified'] == '1') {
        echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK) . "</td>\n";
         } else {
        echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS) . "</td>\n";
         }
       
	    echo '    <td align="left">&nbsp;</td>' . "\n" .
             '    <td align="left">' . $orders_status_array[$orders_history['orders_status_id']] . '</td>' . "\n";
 
        echo '  </tr>' . "\n";
  
        }
        
       } else {
       echo '  <tr>' . "\n" .
            '    <td colspan="5">' . TEXT_NO_ORDER_HISTORY . '</td>' . "\n" .
            '  </tr>' . "\n";
       }

    ?>
  </table> 
  </div>
				  
      <div>
	  <?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?>
	  </div>
	  <br>
	
<table class="table table-bordered table-hover">
  <tr>
    <td align="left"><?php echo TABLE_HEADING_NEW_STATUS; ?></td>
    <?php /* <td class="main" width="10">&nbsp;</td> */ ?>
    <?php /* <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_COMMENTS; ?></td> */ ?>
  </tr>
	<tr>
	  <td>
		  <table class="table table-bordered table-hover">
		  
        <tr>
          <td><b><?php echo ENTRY_STATUS; ?></b></td>
          <td align="right"><?php echo tep_draw_pull_down_menu('status', $orders_statuses, $order->info['orders_status'], 'id="status"'); ?></td>
        </tr>
        <tr>
          <td><b><?php echo ENTRY_NOTIFY_CUSTOMER; ?></b></td>
          <td align="right"><?php echo oe_draw_checkbox_field('notify', '', false, '', 'id="notify"'); ?></td>
        </tr>
        <tr>
          <td><b><?php echo ENTRY_NOTIFY_COMMENTS; ?></b></td>
          <td align="right"><?php echo oe_draw_checkbox_field('notify_comments', '', false, '', 'id="notify_comments"'); ?></td>
        </tr>
     </table>
	  </td>
  </tr>
	<?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?> 
	<script language="JavaScript" type="text/javascript">
     <!--
	     document.write("<tr>");
         document.write("<td colspan=\"3\" align=\"right\">");
		 //document.write("<input type=\"button\" name=\"comments_button\" value=\"<?php echo oe_html_no_quote(AJAX_SUBMIT_COMMENT); ?>\" onClick=\"javascript:getNewComment();\">");
		 document.write("<input type=\"button\" name=\"comments_button\" value=\"Submit Status\" onClick=\"javascript:getNewComment();\">");
		 document.write("</td>");
		 document.write("</tr>");
	 //-->
    </script>
	<?php } ?>
				  
  </table>
  

    <div>
	  <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
	</div>
	
	<div align="right">
		<input type="button" id="btn_process_cc" value="Process CC" />
		<input type="button" id="btn_invoice" value="Generate Invoice" disabled="disabled" />
	</div>
    
	<!-- End of Status Block -->

	<?php if (ORDER_EDITOR_USE_AJAX != 'true') { ?> 
	<!-- Begin Update Block, only for non-javascript browsers -->
	       <div class="updateBlock">
              <div class="update1"><?php echo HINT_PRESS_UPDATE; ?></div>
              <div class="update2">&nbsp;</div>
              <div class="update3">&nbsp;</div>
              <div class="update4" align="center"><?php echo ENTRY_SEND_NEW_ORDER_CONFIRMATION; ?>&nbsp;<?php echo tep_draw_checkbox_field('nC1', '', false); ?></div>
              <div class="update5" align="center"><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></div>
          </div>
		  
	       <br>
            <div><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></div>
	
	<!-- End of Update Block -->
	<?php   }  //end if (ORDER_EDITOR_USE_AJAX != 'true') {
          echo '</form>';
        }
    ?>
  <!-- body_text_eof //-->
      </td>
    </tr>
  </table>
<!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>