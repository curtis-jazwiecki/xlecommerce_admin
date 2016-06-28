<?php require ('includes/application_top.php');

	include('order_editor/functions.php');
	include('order_editor/cart.php');
	include('order_editor/order.php');
	include('order_editor/shipping.php');
	include('order_editor/http_client.php');
	
	if( ($_GET['mode']) && ($_GET['mode'] == 'getLabel') ){
		
		$oID = (int)$_POST['oID'];
		
		$products = json_decode($_POST['products'],true);
		
		$order = new manualOrder($oID);
		$shippingKey = $order->adjust_totals($oID);
		$order->adjust_zones();
		
		$cart = new manualCart();
		$cart->restore_contents_shipping($oID,$products);
		$total_count = $cart->count_contents();
		$total_weight = $cart->show_weight();
		
		
		
		
		$selected_shipping = explode("__",$_POST['selected_shipping']);
		
		if($selected_shipping[0] == 'usps'){
			
			$label = getUSPSShippingLabel($selected_shipping[1],$oID,$total_weight,$products);
			if($label == -1){
				$label = '';
			}
			
		}else if($selected_shipping[0] == 'upsxml'){
			
			$label = getUPSShippingLabel($selected_shipping[1],$oID,$total_weight,$products);
			if($label == -1){
				$label = '';
			}
			
		}else if($selected_shipping[0] == 'fedexwebservices'){
			
			$response = getFedexShippingLabel($selected_shipping[1],$oID,$total_weight,$total_count,$products);
			
			$current_label = isset($_GET['label_id']) && is_numeric($_GET['label_id']) ? intval($_GET['label_id']) : 1;
			
			$total_labels = tep_get_label_count($oID);
			
			if ($total_labels == 0) {
				$label = '';
			} else {
				$label = sprintf('labels/shipExpressLabel-%s-%s.pdf',$oID, $current_label - 1);
			}
		}
		
		
		$json = array(
			'success' => "1",
			'label' => $label,
			'weight' => $total_weight
		
		);
		
		echo json_encode($json);
		die();
		
	}
	
	if( ($_GET['mode']) && ($_GET['mode'] == 'getShipping') ){
		
		
		$products = json_decode($_POST['products'],true);
		
		$oID = (int)$_POST['oID'];
		
		$order = new manualOrder($oID);
		$shippingKey = $order->adjust_totals($oID);
		$order->adjust_zones();
		
		$cart = new manualCart();
		$cart->restore_contents_shipping($oID,$products);
		$total_count = $cart->count_contents();
		$total_weight = $cart->show_weight();
		
		$_SESSION['cart'] = $cart;
		
		// Get the shipping quotes
		$shipping_modules = new shipping;
		$shipping_quotes = $shipping_modules->quote();
		$shipping = '';
		$r = 0;
		
		for ($i=0, $n=sizeof($shipping_quotes); $i<$n; $i++) {
      		if( $shipping_quotes[$i]['id'] == 'ffldealershipping' || $shipping_quotes[$i]['id'] == 'flat' || $shipping_quotes[$i]['id'] == 'table'  ){
				continue;
			}
			for ($j=0, $n2=sizeof($shipping_quotes[$i]['methods']); $j<$n2; $j++) {
				$r++;
				$shipping .= '<tr class="old_shipping dataTableRow"><td>'. $shipping_quotes[$i]['module']. ' ('.$shipping_quotes[$i]['methods'][$j]['title'].') </td><td><input type="radio" name="shipping" value="'.$shipping_quotes[$i]['id'] . '__' . $shipping_quotes[$i]['methods'][$j]['id'].'" id="'.$r.'" class="radio_shipping" /></td></tr>';
			}
		}
		
		
		$json = array(
			'success' => "1",
			'options' => $shipping,
			'weight' => $total_weight
		
		);
		
		echo json_encode($json);
		die();
		
	}
 
?>
<script language="javascript" src="includes/general.js"></script>
<?php include(DIR_WS_LANGUAGES.$language.'/'.FILENAME_MANAGE_SHIPPING_LABEL) ?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

<?php
include(DIR_WS_CLASSES . 'order.php');
$order = new order((int)$_GET['oID']);
?>
<!-- body //-->
<section>
<!-- START Page content-->
<section class="main-content">
<h3><?php echo HEADING_TITLE_MANAGE_SHIPPING_LABEL; ?> <br>
</h3>
<!-- START panel-->
<div class="panel panel-default">
<!-- START table-responsive-->
<div class="table-responsive">
<!-- START your table-->
<table class="table table-bordered table-hover">
  <tr> 
    <!-- body_text //-->
    <td><table class="table table-bordered table-hover">
        <tr>
          <td><?php echo HEADING_BEGIN_SHIPMENT;?> <span style="float:right;"><?php echo ' <a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params()) .'">' . tep_image_button('button_back.gif', IMAGE_BACK). '</a>'; ?></span></td>
        </tr>
        
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        
        <tr style="background-color: white;">
          <td><?php echo TEXT_NOTE; ?></td>
        </tr>
        
        <tr>
        	<td>
            	<table class="table table-bordered table-hover">
                    <tr style="background-color: #999; color:#FFFFFF;">
                        <th> <?php echo HEADING_PRODUCT_NAME;?> </th>
                        <th> <?php echo HEADING_QTY_ORDERED; ?> </th>
                       <!-- <th> <?PHP //echo HEADING_QTY_IN_STORE; ?> </th>-->
                        <th> <?php echo HEADING_QTY_TO_BE_SHIPPED; ?> </th>
                    </tr>
                    
                     <?php for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {

						  $store_quanity = getStoreQuanity($order->products[$i]['model']); ?>
                          	<tr class="dataTableRow">
                                <td><?php echo $order->products[$i]['name']; ?></td>
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'.$order->products[$i]['qty']; ?></td>
                                <!--<td><?php //echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .$store_quanity['store_quantity']; ?></td>-->
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field($store_quanity['products_id'],'',' size="4" class="txt_quantity" style="text-align:center;" ');?></td>
                          </tr>
                          
						  
						  
					<?php } ?>
                      
                    <tr>
                      <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                    </tr>
                    
                    <tr>
                      <td align="right" colspan="3"><?php echo tep_image_button('get_shipping.gif', IMAGE_GET_SHIPPING,' id="get_shipping"  style="cursor:pointer;" ');?><span id="ajaxloader"></span></td>
                    </tr>
                    
                  </table>
                
				<script type="text/javascript">
				  var oID = '<?php echo (int)$_GET['oID']; ?>';
				  var products = {};
                  	jQuery('#get_shipping').click(function(){
						
						
						
						products = {};
						
						jQuery(".txt_quantity").each(function() {
							
							if(jQuery(this).val() != '0' && jQuery(this).val() != ''){
								products[jQuery(this).attr('name')] = jQuery(this).val();
							}
							
						});
						
						if(Object.keys(products).length > 0){
						
							jQuery.ajax({
						
							url: 'manage_shipping_label.php?mode=getShipping',
							
							type: 'post',
							
							dataType: 'json',
							
							data: 'oID=' + encodeURIComponent(oID)+'&products='+JSON.stringify(products),
							
							beforeSend: function() {
								jQuery('.old_shipping').html('');
								jQuery('#shipping_options_html').hide();
								jQuery('#shipping_label').hide('slow');
								jQuery('#ajaxloader').html('<img src="images/loading.gif" alt="" class="attention" align="absmiddle" />');
							},
							complete: function() {
								
								jQuery('.attention').remove();
								jQuery('#shipping_options_html').show('slow');
									
								jQuery('html, body').animate({
									scrollTop: jQuery('#shipping_options_html').offset().top
								}, 1500);
								
							},
							success: function(data) {
								
								if(data['success']){
								
									jQuery('#shipping_options').after(data['options']);
								
								}
								
							}
						
						});
						
						}else{
						
							alert("Error:\n Please select atleast one product.");
							
						}
						
					
					});
                  </script>
            </td>
        </tr>
            
      </table></td>
  
  </tr>
  
  <!-- code for second block start -->
  <tr id="shipping_options_html" style="display:none;">
    <td>
    	<table class="table table-bordered table-hover">
            
            <tr style="background-color:#999; color:#FFFFFF;">
              <th colspan="2"><?php echo HEADING_SHIPPING_METHOD;?></th>
            </tr>
        
        	<tr id="shipping_options"></tr>
          	
            <tr>
              <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
            </tr>
            
            <tr>
              <td align="right" colspan="2"><?php echo tep_image_button('get_label.gif', IMAGE_GET_LABEL,' id="get_label" onclick="getShippingLabel();" style="cursor:pointer;" ');?><span id="ajaxloader2"></span></td>
            </tr>
            
            <script type="text/javascript">
				  var oID = '<?php echo (int)$_GET['oID']; ?>';
				  var selected_shipping = '';
				  
				  function getShippingLabel(){
					  selected_shipping = jQuery(".radio_shipping:checked").val();
					  
					 	products = {};
						
						jQuery(".txt_quantity").each(function() {
							
							if(jQuery(this).val() != '0' && jQuery(this).val() != ''){
								products[jQuery(this).attr('name')] = jQuery(this).val();
							}
							
						});
						
						if(Object.keys(products).length == 0){
					 
							alert("Error:\n Please select atleast one product."); 		
						
						}else if(selected_shipping == '' || selected_shipping == undefined){
							
							alert("Error:\n Please select shipping.");
						
						}else{ 
							
							jQuery.ajax({
						  
							url: 'manage_shipping_label.php?mode=getLabel',
							
							type: 'post',
							
							dataType: 'json',
							
							data: 'oID=' + encodeURIComponent(oID)+'&selected_shipping='+selected_shipping+'&products='+JSON.stringify(products),
							
							beforeSend: function() {
								jQuery('#shipping_label').hide('slow');
								jQuery('#ajaxloader2').html('<img src="images/loading.gif" alt="" class="attention" align="absmiddle" />');
								jQuery('#label_here').html('');
								
							},
							complete: function() {
								
								jQuery('.attention').remove();
								
								jQuery('#shipping_label').show('slow');
								jQuery('html, body').animate({
										scrollTop: jQuery('#shipping_label').offset().top
								}, 1500);
								
								
							},
							success: function(data) {
								
								if( data['success'] && data["label"] != '' ){
									
									jQuery('#label_here').html('<a href="'+data["label"]+'" target="_blank"><?php echo TEXT_DOWNLOAD; ?></a>');
								}else{
							
									jQuery('#label_here').html('<b><font color="#FF0000">Error: Unable to generate label!</font></b>');
								}
								
							}
						
						
					  });
							
						
						}
				  }
				 </script>
              
      </table>
    </td>
  </tr>  
  <!-- code for second block ends --> 
  
    
  <!-- code for third block start --> 
  
  <tr id="shipping_label" style="display:none;">
    <td>
    	<table class="table table-bordered table-hover">
            <tr style="background-color:#999; color:#FFFFFF;">
              <th colspan="2"><?php echo HEADING_SHIPPING_LABEL;?></th>
            </tr>
            <tr>
              <td id="label_here"></td>
            </tr>
       </table>
    </td>
  </tr>

	<!-- code for third block ends -->

</table>
<!-- END your table--> 
<!-- body_eof //-->
<?php
function getStoreQuanity($products_model){
	$products = array();
	
	$products_id_query = tep_db_fetch_array(tep_db_query("select products_id,store_quantity,products_quantity from products where products_model like '".$products_model."'"));
	
	return $products = array(
		'products_id' 	 	=> $products_id_query['products_id'],
		'products_quantity' => $products_id_query['products_quantity'],
		'store_quantity'    => $products_id_query['store_quantity']
	);
}

function getUPSShippingLabel($shipping_string,$order_id,$product_weight,$products){
	
	require('includes/UPSLabel.php');
	
	$vendor_id = 1;
	
	$query = tep_db_query("SELECT * FROM orders WHERE orders_id = $order_id");
	$result = tep_db_fetch_array($query);
	
	// code to get shipping method starts //
	$shipping_language_array = array(
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_01"		=>"UPS Next Day Air",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_02"		=>"UPS 2nd Day Air",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_03"		=>"UPS Ground",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_07"		=>"UPS Worldwide Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_08"		=>"UPS Worldwide Expedited",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_11"		=>"UPS Standard",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_12"		=>"UPS 3 Day Select",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_13"		=>"UPS Next Day Air Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_14"		=>"UPS Next Day Air Early A.M.",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_54"		=>"UPS Worldwide Express Plus",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_59"		=>"UPS 2nd Day Air A.M.",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_65"		=>"UPS Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_01"	=>"UPS Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_02"	=>"UPS Expedited",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_07"	=>"UPS Worldwide Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_08"	=>"UPS Worldwide Expedited",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_11"	=>"UPS Standard",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_12"	=>"UPS 3 Day Select",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_13"	=>"UPS Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_14"	=>"UPS Express Early A.M.",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_65"	=>"UPS Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_07"		=>"UPS Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_08"		=>"UPS Expedited",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_11"		=>"UPS Standard",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_54"		=>"UPS Worldwide Express Plus",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_65"		=>"UPS Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_82"		=>"UPS Today Standard",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_83"		=>"UPS Today Dedicated Courier",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_84"		=>"UPS Today Intercity",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_85"		=>"UPS Today Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_86"		=>"UPS Today Express Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_01"		=>"UPS Next Day Air",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_02"		=>"UPS 2nd Day Air",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_03"		=>"UPS Ground",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_07"		=>"UPS Worldwide Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_08"	    =>"UPS Worldwide Expedited",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_14"	    =>"UPS Next Day Air Early A.M.",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_54"	    =>"UPS Worldwide Express Plus",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_65"	    =>"UPS Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_MEXICO_ORIGIN_07" 	=>"UPS Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_MEXICO_ORIGIN_08" 	=>"UPS Expedited",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_MEXICO_ORIGIN_54" 	=>"UPS Express Plus",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_MEXICO_ORIGIN_65" 	=>"UPS Saver",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_OTHER_ORIGIN_07"  	 =>"UPS Express",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_OTHER_ORIGIN_08"  	 =>"UPS Worldwide Expedited",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_OTHER_ORIGIN_11"  	 =>"UPS Standard",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_OTHER_ORIGIN_54"  	 =>"UPS Worldwide Express Plus",
	"MODULE_SHIPPING_UPSXML_SERVICE_CODE_OTHER_ORIGIN_65"  	 =>"UPS Saver",);
	
	$parsed_shipping_method = '';
	$last_position =  strrpos($shipping_string,"(");
	$tmp = array();
	$selected_shipping = array();
	if($last_position){
		$parsed_shipping_method = str_replace(array( '(', ')' ), '', substr($shipping_string,$last_position));
		if(strpos($parsed_shipping_method,",")){
			$tmp = explode(",",$parsed_shipping_method);
			$parsed_shipping_method = trim($tmp[0]);
			if(in_array($parsed_shipping_method,$shipping_language_array)){
				$selected_shipping[] = substr(array_search($parsed_shipping_method, $shipping_language_array),-2); 
				$selected_shipping[] = $parsed_shipping_method; 
			}
		}
	}else{
		$parsed_shipping_method = $shipping_string;
		$parsed_shipping_method = str_replace(array( '(', ')' ), '', substr($shipping_string,$last_position));
		if(strpos($parsed_shipping_method,",")){
			$tmp = explode(",",$parsed_shipping_method);
			$parsed_shipping_method = trim($tmp[0]);
			
			if(in_array($parsed_shipping_method,$shipping_language_array)){
				$selected_shipping[] = substr(array_search($parsed_shipping_method, $shipping_language_array),-2); 
				$selected_shipping[] = $parsed_shipping_method; 
			}
		}else{
			if(in_array($parsed_shipping_method,$shipping_language_array)){
				$selected_shipping[] = substr(array_search($parsed_shipping_method, $shipping_language_array),-2); 
				$selected_shipping[] = $parsed_shipping_method; 
			}
		}
	}
	
	$params = array(
	'user_name'             => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_RATES_USERNAME_' . $vendor_id) : MODULE_SHIPPING_UPSXML_RATES_USERNAME),
    'password'              => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_RATES_PASSWORD_' . $vendor_id) : MODULE_SHIPPING_UPSXML_RATES_PASSWORD),
    'license_number'        => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_RATES_ACCESS_KEY_' . $vendor_id) : MODULE_SHIPPING_UPSXML_RATES_ACCESS_KEY),
    'from_company_name'     => STORE_NAME,
    'from_address1'         => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_ORIGIN_ADDRESS_' . $vendor_id) : MODULE_SHIPPING_UPSXML_ORIGIN_ADDRESS),
    'from_address2'         => '',
    'from_city'             => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_RATES_CITY_' . $vendor_id) : MODULE_SHIPPING_UPSXML_RATES_CITY),
    'from_state_code'       => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_RATES_STATEPROV_' . $vendor_id) : MODULE_SHIPPING_UPSXML_RATES_STATEPROV),
    'from_zip'              => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_RATES_POSTALCODE_' . $vendor_id) : MODULE_SHIPPING_UPSXML_RATES_POSTALCODE),
    'tax_id_type_code'      => '',
    'to_company_name'       => $result['delivery_company'],
    'to_name'               => $result['delivery_name'],
    'to_address1'           => $result['delivery_street_address'],
    'to_address2'           => $result['delivery_suburb'],
    'to_city'               => $result['delivery_city'],
	'to_telephone'          => $result['customers_telephone'],
    'to_state_code'         => convertStateTo($result['delivery_state']),
    'to_zip'                => $result['delivery_postcode'],
    'service_code'          => $selected_shipping[0],
    'service_desc'          => $selected_shipping[1],
    'package_type_code'     => $selected_shipping[0],
    'package_type_desc'     => $selected_shipping[1],
    'package_desc'          => '',
	'shipper_number'        => ($vendor_id ? @constant('MODULE_SHIPPING_UPSXML_RATES_UPS_ACCOUNT_NUMBER_' . $vendor_id) : MODULE_SHIPPING_UPSXML_RATES_UPS_ACCOUNT_NUMBER),
	'order_id'			  	=> $order_id,
	'weight'			    => $product_weight,
	'vendor_id'			 	=> $vendor_id);

	ob_start();
	$ups_digests = getDigestUPSLabel($params,1);
	$output = ob_get_clean();
	
	if($output == -1){
		return -1;	
	}else{
		preg_match_all('/"([^"]+)"/', $output, $m);
		updateShippingLabelTables($products,$order_id,$shipping_string,'UPS',$m[1][1]);
		return $m[1][0];
	}
}

function getUSPSShippingLabel($shipping_string,$order_id,$product_weight,$products){

	require('includes/USPSLabel.php');
	
	$vendor_id = 1;
	
	$query = tep_db_query("SELECT * FROM orders WHERE orders_id = $order_id");
	$result = tep_db_fetch_array($query);
	
	$parsed_shipping_method = '';
	$last_position =  strrpos($shipping_string,"(");
	$tmp = array();
	if($last_position){
		$parsed_shipping_method = str_replace(array( '(', ')' ), '', substr($shipping_string,$last_position));
		if(strpos($parsed_shipping_method,"<sup>")){
			$tmp = explode("<sup>",$parsed_shipping_method);
			$parsed_shipping_method = trim($tmp[0]);
		}
	}else{
		$parsed_shipping_method = $shipping_string;
		$parsed_shipping_method = str_replace(array( '(', ')' ), '', substr($shipping_string,$last_position));
		if(strpos($parsed_shipping_method,"<sup>")){
			$tmp = explode(",",$parsed_shipping_method);
			$parsed_shipping_method = trim($tmp[0]);
		}
	}
	
	$get_vendor_details = tep_db_fetch_array(tep_db_query("select * from vendors where vendors_id = '".(int)$vendor_id."'"));
	$from_params_array = array(
		"userName" 	   => @constant('MODULE_SHIPPING_USPS_USERID_'.$vendor_id),
		"FromName" 	   => $get_vendor_details['vendors_name'],
		"FromAddress2" => $get_vendor_details['vendor_street'],
		"FromCity" 	   => $get_vendor_details['vendor_city'],
		"FromState"    => $get_vendor_details['vendor_state'],
		"FromZip5" 	   => $get_vendor_details['vendors_zipcode'],
		"vendor_add2"  => $get_vendor_details['vendor_add2']
	);
	
	$to_params_array = array(
		"ToName" 			 => $result['customers_name'],
		"ToAddress2" 		 => $result['delivery_street_address'],
		"ToCity" 			 => $result['delivery_city'],
		"ToState" 	    	 => convert_state($result['delivery_state'],'abbrev'),
		"ToZip5" 	 	 	 => $result['delivery_postcode'],
		"to_delivery_company"=> $result['delivery_company'],
		"to_delivery_suburb" => $result['delivery_suburb'],
		"products_weight" 	 => $product_weight,
		"shipping_code" 	 => $parsed_shipping_code
	);

	$USPSResponse = USPSLabel($from_params_array,$to_params_array);
	
	$USPSLabel = $USPSResponse['DeliveryConfirmationV4.0Response']['DeliveryConfirmationLabel']['VALUE'];
	
	$tracking_number = $USPSResponse['DeliveryConfirmationV4.0Response']['DeliveryConfirmationNumber']['VALUE'];
	
	if(empty($tracking_number)){
		
		return -1;
		
	}else {
	
		updateShippingLabelTables($products,$order_id,$shipping_string,'USPS',$tracking_number);
		
		// turn label into pdf
		$label_pdf = base64_decode($USPSLabel);
		// write the pdf
		$label_name = $order_id.'_'.$vendor_id.'_usps_label.pdf';
		@file_put_contents('shipping_labels/'.$label_name, $label_pdf);
		
		// this is just the public path where we saved the pdf
		return $pdf_url = "/ad_obnv6/shipping_labels/$label_name";
	
	}
	
	
}

function getFedexShippingLabel($shipping_string,$order,$product_weight,$total_count,$products){
	
	include(DIR_FS_CATALOG.DIR_WS_MODULES . 'shipping/fedexwebservices.php');
	
	define('TABLE_SHIPPING_MANIFEST','shipping_manifest');
	
	// *** EDIT VARIABLES BELOW **
	$lastshipment = 17;            // Define last shipment time (ex: 17 would be 5pm on your server)
	$send_email_on_shipping = 1;   // Set to 0 to disable, set to 1 to enable automatic email of tracking number
	
	// Modify the variable here and in admin/fedex_popup.php
	$thermal_printing = 1;         // set the printing type, thermal_printing = 0 for laser, thermal_printing = 1 for label printer
	
	require(DIR_WS_INCLUDES . 'abbreviate.php'); // used to abbreviate state & country names
	require(DIR_WS_INCLUDES . 'fedexdc.php');
	
	$order_weight = round($product_weight,1);
	$order_weight = sprintf("%01.1f", $order_weight);
	
	// array of characters we don't want in phone numbers
		$unwanted = array('(',')','-','.',' ','/');

		// get the country we're shipping from
		$country_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'STORE_COUNTRY'");
		$country_value = tep_db_fetch_array($country_query);
		$country = tep_get_country_name($country_value['configuration_value']);
		// abbreviate it for fedex (United States = US etc.)
		$senders_country = abbreviate_country($country);

		// get sender's fedex info from configuration table

		// (requires installation & configuration of FedEx RealTime Quotes)

		$fedexRequestData = array();
		
		// get all information from the order record
		$order_query = tep_db_query("select * from orders where orders_id = $order");

		$order_info = tep_db_fetch_array($order_query);
		
		// abbreviate the delivery state (function is in abbreviate.php)
		$order_info['delivery_state'] = abbreviate_state($order_info['delivery_state']);

		// abbreviate the delivery country (function is in abbreviate.php)
		$order_info['delivery_country'] = abbreviate_country($order_info['delivery_country']);
		
		// get rid of dashes, parentheses and periods in customer's telephone number
		$order_info['customers_telephone'] = trim(str_replace($unwanted, '', $order_info['customers_telephone']));

		$fedexRequestData['order_info'] = $order_info;
		
		// format the form date (comes in as mm-dd-yyyy)
		$corrected_date = date("Y").date("m").date("d");

		// determine whether the ship date is today or later
		if ($corrected_date == date("Ymd")) {
			$future = 'N'; // today
		}else {
			$future = 'Y';  // later date
		}
		
		$service_type = array(
		
			"PRIORITYOVERNIGHT" 			=> "PRIORITY_OVERNIGHT",
			"STANDARDOVERNIGHT"				=> "STANDARD_OVERNIGHT",
			"FEDEX2DAY"						=> "FEDEX_2_DAY",
			"FEDEXEXPRESSSAVER"				=> "FEDEX_EXPRESS_SAVER",
			"FEDEXGROUND" 					=> "FEDEX_GROUND",
			
			"FEDEXGROUND"								=> "FEDEX_FIRST_FREIGHT",
			"FEDEXGROUND"								=> "FEDEX_1_DAY_FREIGHT",
			"FEDEXGROUND"								=> "FEDEX_3_DAY_FREIGHT",
			"FEDEXGROUND"								=> "GROUND_HOME_DELIVERY",
			"FEDEXGROUND"								=> "FEDEX_2_DAY_AM",
			"FEDEXGROUND"								=> "FEDEX_FREIGHT_ECONOMY",
			"FEDEXGROUND"								=> "FEDEX_FREIGHT_PRIORITY",
			"FEDEXGROUND"								=> "INTERNATIONAL_PRIORITY_FREIGHT",
			"FEDEXGROUND"								=> "INTERNATIONAL_ECONOMY_FREIGHT",
			"FEDEXGROUND"								=> "INTERNATIONAL_PRIORITY"
		);
		
		if(array_key_exists($shipping_string,$service_type)){
			$fedex_service_type = $service_type[$shipping_string];
		}else{
			$fedex_service_type = 'FEDEX_GROUND';
		}
		
		
		
		
		// start the array for fedex
		$shipData = array(
		      'signature_type'		=> 0,//$HTTP_POST_VARS['signature_type'], // signature type
		      'packaging_type'		=> 'YOUR_PACKAGING',//$HTTP_POST_VARS['packaging_type'], // packaging type (01 is customer packaging)
		      'service_type'		=> $fedex_service_type,//$shipping_string,//$HTTP_POST_VARS['service_type'], //
		      'delivery_city'		=> $order_info['delivery_city'],
		      'bill_type'			=> 'SENDER',//$HTTP_POST_VARS['bill_type'], // payment type (1 is bill to sender)
		      'dropoff_type'		=> 'REGULAR_PICKUP',//$HTTP_POST_VARS['dropoff_type'], // drop off type (1 is regular pickup)
		      'printing_type'		=> (($thermal_printing) ? 7 : 5), // label media (5 is plain paper, 7 is 4x6 image)
		      'package_invoice'		=> $order,//$HTTP_POST_VARS['package_invoice'], // invoice number
		      'package_reference'	=> $order,//$HTTP_POST_VARS['package_reference'], // reference number
		      'package_po'			=> '',//$HTTP_POST_VARS['package_po'], // purchase order number
		      'package_department'	=> '',//$HTTP_POST_VARS['package_department'], // department name
			  'ship_date'			=> $corrected_date,  // ship date
			  'future_day'			=> $future, // future day shipment
			  'saturday_delivery'	=> 0,//$HTTP_POST_VARS['saturday_delivery'], // Saturday delivery
			  'hold_at_location'	=> 0,//$HTTP_POST_VARS['hold_at_location'], // Hold At Fedex Location
			  'hal_address'			=> '',//$HTTP_POST_VARS['hal_address'], // Hold At Location address
			  'hal_city'			=> '',//$HTTP_POST_VARS['hal_city'], // Hold At Location city
			  'hal_state'			=> '',//$HTTP_POST_VARS['hal_state'], // Hold At Location state
			  'hal_postcode'		=> '',//$HTTP_POST_VARS['hal_postcode'], // Hold At Location postal code
			  'hal_phone'			=> '',//$HTTP_POST_VARS['hal_phone'], // Hold At Location phone number
			  'autopod'				=> 0,//$HTTP_POST_VARS['autopod'], // Automatic Proof of Delivery,
			  'hold_at_location'	=> 0,//$HTTP_POST_VARS['hold_at_location'],
		 	  'oversized'			=> 0,//$HTTP_POST_VARS['oversized'],
			  'LabelFormatType'		=> 'COMMON2D',//$HTTP_POST_VARS['LabelFormatType'],
			  'ImageType'			=> 'PDF',//$HTTP_POST_VARS['ImageType'],
			  'LabelStockType'		=> 'PAPER_8.5X11_BOTTOM_HALF_LABEL',//$HTTP_POST_VARS['LabelStockType']
		
		);
		
		
		
		$pakage_details = array();
		$unitUsed = MODULE_SHIPPING_FEDEX_WEB_SERVICES_WEIGHT;
		$pakage_details['PackageCount'] = $total_count;//$HTTP_POST_VARS['package_num'];
		$pakage_details['Total_Weight'] = $order_weight;//$HTTP_POST_VARS['package_weight'];
		$pakage_details['Units'] = $unitUsed;
		
		
		// get the order qty and item weights
		
      		
		foreach($products as $products_id => $qty){
			
			$products_weight_query = tep_db_query("select * from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
			if (tep_db_num_rows($products_weight_query)) {
				$products_weights = tep_db_fetch_array($products_weight_query);
				$item[] = array(
					"weight"		=>	$products_weights['products_weight'],
					"dim_length"	=>	'',
					"dim_width"		=>	'',
					"dim_height"	=>	''
				);
			}
		}
			
			
		
		
		
		###Validate multiple pakages###
		if($pakage_details['PackageCount'] > 1){
			$pakage_details['pakages'] = $item;
		}else{
			 $pakage_details['dimension'] = array(
				'dim_height' 	=> $item[0]['dim_height'], // "your package" height dimension
				'dim_width' 	=> $item[0]['dim_width'], // "your package" width dimension
				'dim_length' 	=> $item[0]['dim_length'] // "your package length dimension
			 );	
		}
		
		$fedexRequestData['shipData'] = $shipData;
		$fedexRequestData['pakage_details'] = $pakage_details;
		
		##Fedex Object
		$fedexServ = new fedexwebservices();
		$serviceResponse = $fedexServ->shipRequest($fedexRequestData);
		
		$tracking_number = $serviceResponse['trackingNo']->TrackingNumber;
		
		updateShippingLabelTables($products,$order,$shipping_string,'FEDEX',$tracking_number);
			
}

function tep_get_label_count($oID) {
	$count = 0;
	$file_prefix = "shipExpressLabel-$oID-";
	$dir = 'labels';
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (preg_match('/^'.$file_prefix.'.*.pdf/', $file)) {
					$count++;
					error_log("found match $file");
				}
			}
			closedir($dh);
		}
	}
	return $count;
}

function updateShippingLabelTables($products,$order_id,$shipping_string,$shipping_method,$tracking_number){
	
	tep_db_query("delete manage_shipping_labels, manage_order_shipping from manage_shipping_labels join manage_order_shipping  USING(manage_order_shipping_id) where orders_id = '".$order_id."' and products_id IN (".implode(",",array_keys($products)).")");
	
	foreach($products as $products_id => $qty){
	
		tep_db_query("insert into manage_order_shipping set orders_id = '".$order_id."',products_id = '".$products_id."'");
		
		$insert_id = tep_db_insert_id();
		
		tep_db_query("insert into manage_shipping_labels set manage_order_shipping_id = '".$insert_id."',courier = '".$shipping_string."',shipping_method = '".$shipping_method."',tracking_number = '".$tracking_number."'");
	
	}
}

function convert_state($name, $to='name') {
	
	$states = array(
	array('name'=>'Alabama', 'abbrev'=>'AL'),
	array('name'=>'Alaska', 'abbrev'=>'AK'),
	array('name'=>'Arizona', 'abbrev'=>'AZ'),
	array('name'=>'Arkansas', 'abbrev'=>'AR'),
	array('name'=>'California', 'abbrev'=>'CA'),
	array('name'=>'Colorado', 'abbrev'=>'CO'),
	array('name'=>'Connecticut', 'abbrev'=>'CT'),
	array('name'=>'Delaware', 'abbrev'=>'DE'),
	array('name'=>'Florida', 'abbrev'=>'FL'),
	array('name'=>'Georgia', 'abbrev'=>'GA'),
	array('name'=>'Hawaii', 'abbrev'=>'HI'),
	array('name'=>'Idaho', 'abbrev'=>'ID'),
	array('name'=>'Illinois', 'abbrev'=>'IL'),
	array('name'=>'Indiana', 'abbrev'=>'IN'),
	array('name'=>'Iowa', 'abbrev'=>'IA'),
	array('name'=>'Kansas', 'abbrev'=>'KS'),
	array('name'=>'Kentucky', 'abbrev'=>'KY'),
	array('name'=>'Louisiana', 'abbrev'=>'LA'),
	array('name'=>'Maine', 'abbrev'=>'ME'),
	array('name'=>'Maryland', 'abbrev'=>'MD'),
	array('name'=>'Massachusetts', 'abbrev'=>'MA'),
	array('name'=>'Michigan', 'abbrev'=>'MI'),
	array('name'=>'Minnesota', 'abbrev'=>'MN'),
	array('name'=>'Mississippi', 'abbrev'=>'MS'),
	array('name'=>'Missouri', 'abbrev'=>'MO'),
	array('name'=>'Montana', 'abbrev'=>'MT'),
	array('name'=>'Nebraska', 'abbrev'=>'NE'),
	array('name'=>'Nevada', 'abbrev'=>'NV'),
	array('name'=>'New Hampshire', 'abbrev'=>'NH'),
	array('name'=>'New Jersey', 'abbrev'=>'NJ'),
	array('name'=>'New Mexico', 'abbrev'=>'NM'),
	array('name'=>'New York', 'abbrev'=>'NY'),
	array('name'=>'North Carolina', 'abbrev'=>'NC'),
	array('name'=>'North Dakota', 'abbrev'=>'ND'),
	array('name'=>'Ohio', 'abbrev'=>'OH'),
	array('name'=>'Oklahoma', 'abbrev'=>'OK'),
	array('name'=>'Oregon', 'abbrev'=>'OR'),
	array('name'=>'Pennsylvania', 'abbrev'=>'PA'),
	array('name'=>'Rhode Island', 'abbrev'=>'RI'),
	array('name'=>'South Carolina', 'abbrev'=>'SC'),
	array('name'=>'South Dakota', 'abbrev'=>'SD'),
	array('name'=>'Tennessee', 'abbrev'=>'TN'),
	array('name'=>'Texas', 'abbrev'=>'TX'),
	array('name'=>'Utah', 'abbrev'=>'UT'),
	array('name'=>'Vermont', 'abbrev'=>'VT'),
	array('name'=>'Virginia', 'abbrev'=>'VA'),
	array('name'=>'Washington', 'abbrev'=>'WA'),
	array('name'=>'West Virginia', 'abbrev'=>'WV'),
	array('name'=>'Wisconsin', 'abbrev'=>'WI'),
	array('name'=>'Wyoming', 'abbrev'=>'WY'),
	array('name'=>'Armed Forces Europe', 'abbrev'=>'AE')
	);

	$return = false;
	foreach ($states as $state) {
		if ($to == 'name') {
			if (strtolower($state['abbrev']) == strtolower($name)){
				$return = $state['name'];
				break;
			}
		} else if ($to == 'abbrev') {
			if (strtolower($state['name']) == strtolower($name)){
				$return = strtoupper($state['abbrev']);
				break;
			}
		}
	}
	return $return;
}
?>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
