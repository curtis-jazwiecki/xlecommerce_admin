<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require ('includes/application_top.php');

if( (isset($_GET['mode'])) && ($_GET['mode'] == 'getAddressDetails') ){
	
	$json = array();
	
	$query = tep_db_query("select ab.*,z.zone_code,c.countries_iso_code_2 from ".TABLE_ADDRESS_BOOK." as ab left join ".TABLE_COUNTRIES." as c on (ab.entry_country_id = c.countries_id) left join ".TABLE_ZONES." as z on (ab.entry_zone_id = z.zone_id) where address_book_id = '".(int)$_POST['address_book_id']."'");
	
	if(tep_db_num_rows($query)){
	
		$data = tep_db_fetch_array($query);
		$json['success'] = 1;
		$json['entry_street_address_original'] = $data['entry_street_address'];
		$json['entry_suburb_original'] = $data['entry_suburb'];
		$json['entry_postcode_original'] = $data['entry_postcode'];
		$json['entry_city_original'] = $data['entry_city'];
		$json['entry_state_original'] = $data['zone_code'];
		$json['entry_country_id_original'] = $data['countries_iso_code_2'];	
		$json['country_id'] = $data['entry_country_id'];	
		
	}else{
	
		$json['error'] = 1;
		
	}
	
	echo json_encode($json);
	exit;
}


?>
<?php include(DIR_WS_LANGUAGES.$language.'/'.FILENAME_AVATAX_ADDRESS_VALIDATION) ?>
<?php 
	$customers_id = (int)$_GET['cID']; 
	if(!isset($customers_id)){
		die(); // no direct access
	}
?>
<body bgcolor="#FFFFFF">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<?php
function getAllCustomerAddress($customers_id){
	$customer_address[] = array('id' => '','text' => 'Select Address');
	$query = tep_db_query("Select * from address_book as a ,countries as c where a.entry_country_id=c.countries_id and a.customers_id=".$customers_id);
	while ($query_values = tep_db_fetch_array($query)) {
		$customer_address[] = array('id' => $query_values['address_book_id']  , 'text' => $query_values['entry_street_address'].','.$query_values['entry_suburb']. ','. $query_values['entry_city'] .','. $query_values['entry_state'].','. $query_values['countries_name'].','.$query_values['entry_postcode']);
	}
	return $customer_address;
}
?>
<?php echo tep_draw_form('avalara_address_validation', FILENAME_AVATAX_ADDRESS_VALIDATION,'action=avatax_action_address_validate', 'post', 'enctype="multipart/form-data"');?>
<table align="center" width="100%" cellpadding="5" cellspacing="5" border="1">
  <caption>
  <strong><?php echo HEADING_AVTAX_ADDRESS_VALIDATION; ?></strong>
  </caption>
  <tr>
    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
    <td><?php echo ENTRY_CUSTOMER_CODE; ?></td>
    <td><?php echo tep_draw_input_field('Customer_Code',$customers_id, ' disabled="disabled" style="font-weight:bold;" ' );?></td>
  </tr>
  <tr>
    <td valign="top"><?php echo SELECT_AVAILABLE_ADDRESSES; ?></td>
    <td valign="top"><?php
		$customer_address= getAllCustomerAddress($customers_id);
		echo tep_draw_pull_down_menu('customer_addresses',$customer_address); 
	?>
      <span id="ajaxloader"></span></td>
  </tr>
</table>
<table align="center" width="100%" cellpadding="5" cellspacing="5" border="1">
  <tr>
    <td><table class="" width="100%" height="300px;">
        <caption align="center">
        <strong><?php echo HEADING_ADDRESS_SELECTED; ?></strong>
        </caption>
        <tr>
          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td><?php echo ENTRY_STREET_ADDRESS; ?></td>
          <td><?php echo tep_draw_input_field('entry_street_address_original','',' id="entry_street_address_original" '); ?></td>
        </tr>
        <tr>
          <td><?php echo ENTRY_SUBURB; ?></td>
          <td><?php echo tep_draw_input_field('entry_suburb_original','',' id="entry_suburb_original" '); ?></td>
        </tr>
        <tr>
          <td><?php echo ENTRY_POST_CODE; ?></td>
          <td><?php  echo tep_draw_input_field('entry_postcode_original','',' id="entry_postcode_original" '); ?></td>
        </tr>
        <tr>
          <td><?php echo ENTRY_CITY; ?></td>
          <td><?php echo tep_draw_input_field('entry_city_original','',' id="entry_city_original" '); ?></td>
        </tr>
        <tr>
          <td><?php echo ENTRY_STATE; ?></td>
          <td><?php echo tep_draw_input_field('entry_state_original','',' id="entry_state_original" '); ?></td>
        </tr>
        <tr>
          <td><?php echo ENTRY_COUNTRY; ?></td>
          <td><?php echo tep_draw_input_field('entry_country_id_original','',' id="entry_country_id_original" '); ?><input type="hidden" name="country_id" id="country_id" value=""></td>
        </tr>
      </table></td>
    <td><table class="" width="100%" height="300px;">
        <caption align="center">
        <strong><?php echo HEADING_ADDRESS_VALIDATED; ?></strong>
        </caption>
        <tr>
          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td><?php echo ENTRY_STREET_ADDRESS; ?></td>
          <td><?php echo tep_draw_input_field('entry_street_address_validated',"" , ' id="entry_street_address_validated" disabled="disabled" '); ?></td>
        </tr>
        <tr>
          <td><?php echo ENTRY_SUBURB; ?></td>
          <td><?php echo tep_draw_input_field('entry_suburb_validated', "", ' id="entry_suburb_validated" disabled="disabled" '); ?></td>
        </tr>
        <tr>
          <td><?php echo ENTRY_POST_CODE; ?></td>
          <td><?php echo tep_draw_input_field('entry_postcode_validated',"" , ' id="entry_postcode_validated" disabled="disabled" '); ?></td>
        </tr>
        <tr>
          <td><?php echo ENTRY_CITY; ?></td>
          <td><?php echo tep_draw_input_field('entry_city_validated',"", ' id="entry_city_validated" disabled="disabled" '); ?></td>
        </tr>
        <tr>
          <td><?php echo ENTRY_STATE; ?></td>
          <td><?php echo tep_draw_input_field('entry_state_validated',"",' id="entry_state_validated" disabled="disabled" '); ?></td>
        </tr>
        <tr>
          <td><?php echo ENTRY_COUNTRY; ?></td>
          <td><?php echo tep_draw_input_field('entry_country_id_validated',"", ' id="entry_country_id_validated" disabled="disabled" '); ?></td>
        </tr>
      </table></td>
  </tr>
</table>
<div style="margin-top:10px; text-align:center;"> <a onClick="self.close();" style="cursor:pointer;"><?php echo tep_image_button('button_close.gif', IMAGE_CLOSE); ?></a> &nbsp;&nbsp; <a onClick="validateAddress();" style="cursor:pointer;"><?php echo tep_image_button('button_validate.gif', IMAGE_VALIDATE); ?></a><span id="ajaxloader2"></span> </div>
</form>
<script type="text/javascript">
function clearOriginalFields(){
	jQuery('#entry_street_address_original').val('');
	jQuery('#entry_suburb_original').val('');
	jQuery('#entry_postcode_original').val('');
	jQuery('#entry_city_original').val('');
	jQuery('#entry_state_original').val('');
	jQuery('#entry_country_id_original').val('');
}

function clearValidatedFields(){
	jQuery('#entry_street_address_validated').val('');
	jQuery('#entry_suburb_validated').val('');
	jQuery('#entry_postcode_validated').val('');
	jQuery('#entry_city_validated').val('');
	jQuery('#entry_state_validated').val('');
	jQuery('#entry_country_id_validated').val('');
}


jQuery('select[name="customer_addresses"]').bind('change',function(){
	
	if(jQuery(this).val() == ''){
		clearOriginalFields();
		clearValidatedFields();
		return;
	}
	
	jQuery.ajax({
		
		url: 'avatax_address_validation.php?mode=getAddressDetails',
		
		type: 'post',
		
		dataType: 'json',
		
		data: 'address_book_id=' + encodeURIComponent(jQuery(this).val()),
		
		beforeSend: function() {
			clearOriginalFields();
			clearValidatedFields();
			jQuery('#ajaxloader').html('<img src="images/loading.gif" alt="" class="attention" align="absmiddle" />');
		},
		complete: function() {
			
			jQuery('.attention').remove();
			
		},
		success: function(data) {
			
			if(data['success']){
			
				jQuery('#entry_street_address_original').val(data['entry_street_address_original']);
				jQuery('#entry_suburb_original').val(data['entry_suburb_original']);
				jQuery('#entry_postcode_original').val(data['entry_postcode_original']);
				jQuery('#entry_city_original').val(data['entry_city_original']);
				jQuery('#entry_state_original').val(data['entry_state_original']);
				jQuery('#entry_country_id_original').val(data['entry_country_id_original']);
				jQuery('#country_id').val(data['country_id']);
			
			}
			
		}
	});
	
});

function validateAddress(){
	
	var entry_street_address_original =  jQuery('#entry_street_address_original').val();
	var entry_suburb_original = jQuery('#entry_suburb_original').val();
	var entry_postcode_original = jQuery('#entry_postcode_original').val();
	var entry_city_original = jQuery('#entry_city_original').val();
	var entry_state_original = jQuery('#entry_state_original').val();
	var entry_country_id_original = jQuery('#entry_country_id_original').val();
	var country_id = jQuery('#country_id').val();
	
	var list_of_valid_countries = '<?php echo MODULE_ORDER_TOTAL_AVATAX_VALID_COUNTRIES; ?>'.split(",");
	
	if(list_of_valid_countries.indexOf(country_id) == -1){
		alert("Error: This Country is not permissible for address validation!");
		return false;
	}
	
	jQuery.ajax({
		
		url: 'commit_to_alavara.php?mode=validateAddressDetails',
		
		type: 'post',
		
		dataType: 'json',
		
		data: 'entry_street_address_original=' + encodeURIComponent(entry_street_address_original) + '&entry_suburb_original=' + encodeURIComponent(entry_suburb_original)+ '&entry_postcode_original=' + encodeURIComponent(entry_postcode_original)+'&entry_city_original=' + encodeURIComponent(entry_city_original)+'&entry_state_original=' + encodeURIComponent(entry_state_original)+'&entry_country_id_original=' + encodeURIComponent(entry_country_id_original),
		beforeSend: function() {
			
			jQuery('#ajaxloader2').html('<img src="images/loading.gif" alt="" class="attention" align="absmiddle" />');
			
		},
		complete: function() {
			
			jQuery('.attention').remove();
			
		},
		success: function(data) {
			
			if(data['error']){
				alert("Error: Not a valid address!");
			}else if(data['success']){
				alert("Success: Address validated");
				jQuery('#entry_street_address_validated').val(data['entry_street_address_original']);
				jQuery('#entry_suburb_validated').val(data['entry_suburb_original']);
				jQuery('#entry_postcode_validated').val(data['entry_postcode_original']);
				jQuery('#entry_city_validated').val(data['entry_city_original']);
				jQuery('#entry_state_validated').val(data['entry_state_original']);
				jQuery('#entry_country_id_validated').val(data['entry_country_id_original']);
			}
		}
	});
}
</script>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>