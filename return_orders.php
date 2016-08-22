<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
 require ('includes/application_top.php');

	include('order_editor/functions.php');
	include('order_editor/cart.php');
	include('order_editor/order.php');
	include('order_editor/shipping.php');
	include('order_editor/http_client.php');
	require('order_editor/order_total.php');
	require(DIR_WS_CLASSES . 'currencies.php');
  	$currencies = new currencies();
	
	if( ($_GET['mode']) && ($_GET['mode'] == 'returnProducts') ){
		
		
		$products = json_decode($_POST['products'],true);
		
		$status = 12;
		
		$parent_orders_id = (int)$_POST['oID'];
		
		$get_origin_order = tep_db_fetch_array(tep_db_query("select * from orders where orders_id = '".$parent_orders_id."'"));
		
		
		unset($get_origin_order['orders_id']); 
		
		$get_origin_order['orders_status'] = $status;
		
		foreach($get_origin_order as $okey => $ovalue){
			
			$sql_data_array[$okey] = $ovalue;
		}
		
		
		$sql_data_array['parent_orders_id'] = $parent_orders_id;
		
		tep_db_perform(TABLE_ORDERS, $sql_data_array);
		
		$oID = tep_db_insert_id();
		
        // Insert Products
		$get_products_query = tep_db_query("select * from orders_products where orders_id = '".$parent_orders_id."' and products_id IN (".implode(",",$products).")");
		
		
		 
		while($get_products = tep_db_fetch_array($get_products_query)){
			
			tep_db_query("INSERT INTO orders_products set 
				orders_id = '".$oID."',
				products_id = '".$get_products['products_id']."',
				products_model = '".$get_products['products_model']."',
				products_name = '".$get_products['products_name']."',
				products_price = '".($get_products['products_price'] * (-1))."',
				final_price = '".($get_products['final_price'] * (-1))."',
				products_tax = '".$get_products['products_tax']."',
				products_quantity = '".$get_products['products_quantity']."',
				is_ok_for_shipping = '".$get_products['is_ok_for_shipping']."',
				vendors_id = '".$get_products['vendors_id']."',
				sq = '".$get_products['sq']."',
				wq = '".$get_products['wq']."'");
			
		}
	  
	  	$get_order_history_query = tep_db_query("select * from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '".$parent_orders_id."'");  
		
		while($get_order_history = tep_db_fetch_array($get_order_history_query)){
			
			tep_db_query("INSERT INTO " . TABLE_ORDERS_STATUS_HISTORY . " set 
				comments = '".$get_order_history['comments']."',
				orders_id = '".$oID."',
				orders_status_id = '".$get_order_history['orders_status_id']."',
				date_added = '".$get_order_history['date_added']."',
				customer_notified = '".$get_order_history['customer_notified']."'");
			
		}
		
		
		$order = new manualOrder($oID);
		$shippingKey = $order->adjust_totals($oID);
		$order->adjust_zones();
		
		$cart = new manualCart();
		$cart->restore_contents_return($oID,$products);
		$total_count = $cart->count_contents();
		$total_weight = $cart->show_weight();
		$_SESSION['cart'] = $cart;
		
		$oldPath = getcwd();
		chdir('../');
		require_once DIR_WS_MODULES . 'avatax/ava_tax.php';
		require_once DIR_WS_MODULES . 'avatax/credentials.php'; 
		
		$order_total_modules = new order_total();
		
		//$order_total_modules->modules = array_diff($order_total_modules->modules, array('ot_avatax.php'));
		$new_order_totals = $order_total_modules->process(); 
		
		$tax_data = commit_to_avalara($parent_orders_id,$order,$oID);
		
		chdir($oldPath);
		
		$tax_amt = $tax_data['tax_amount'];
		
		if(!empty($tax_amt)){
			$new_order_totals[] = array(
				'orders_id' 	=> $oID,
				'title' 		=> 'Sales Tax ('.$order->delivery['city'].') :',
				'text' 			=> $currencies->format($tax_amt, true, $order->info['currency'], $order->info['currency_value']) ,
				'value' 		=> $tax_amt, 
				'class' 		=> 'ot_avatax', 
				'sort_order' 	=> 99
			);
		}
		
		for ($i=0, $n=sizeof($new_order_totals); $i<$n; $i++) {
		  
		  if($new_order_totals[$i]['code'] == 'ot_total'){
			  $new_order_totals[$i]['value'] += $tax_amt; 
			  $new_order_totals[$i]['text'] = "<b>".$currencies->format($new_order_totals[$i]['value'], true, $order->info['currency'], $order->info['currency_value'])."</b>";
		  }
		  
		  $sql_data_array = array('orders_id' => $oID,
								  'title' => $new_order_totals[$i]['title'],
								  'text' =>  $new_order_totals[$i]['text'],
								  'value' => $new_order_totals[$i]['value'], 
								  'class' => $new_order_totals[$i]['code'], 
								  'sort_order' => $new_order_totals[$i]['sort_order']);
								  
		  tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
		}
		
		$json = array(
			'success' => "1",
			'new_order_id' => $oID
		);
		
		
		
		
		
		echo json_encode($json);
		die();
		
	}
	
function commit_to_avalara($parent_orders_id,$order,$return_oID){
	
	
	
    require_once DIR_WS_MODULES . 'avatax/ava_tax.php';

    require_once DIR_WS_MODULES . 'avatax/credentials.php';

    global $db, $messageStack;

    $new_order_id = $parent_orders_id;

    $client = new TaxServiceSoap(MODULE_ORDER_TOTAL_AVATAX_DEV_STATUS);

    $request = new GetTaxRequest();

    // Construct Origin Address

    $origin = new AvaAddress();

    $origin->setCity(MODULE_ORDER_TOTAL_AVATAX_CITY);

    $origin->setRegion(MODULE_ORDER_TOTAL_AVATAX_STATE);

    $origin->setPostalCode(MODULE_ORDER_TOTAL_AVATAX_ZIPCODE);

    $request->setOriginAddress($origin);

    //Add Destination address

    $destination = new AvaAddress();

    $destination->setLine1($order->delivery['street_address']);

    $destination->setLine2($order->delivery['suburb']);

    $destination->setCity($order->delivery['city']);

    $destination->setRegion($order->delivery['state']);

    $destination->setPostalCode($order->delivery['postcode']);

    $request->setDestinationAddress($destination);     //Address

    $request->setCompanyCode(MODULE_ORDER_TOTAL_AVATAX_CODE);

    $request->setDocType('ReturnInvoice');
	
	$get_avalara_data_query = tep_db_fetch_array(tep_db_query("select date_purchased,avalara_data from orders where orders_id = '".$parent_orders_id."'"));
	
	$get_avalara_data = unserialize($get_avalara_data_query['avalara_data']); 
	
    
	
	
    //$request->setDocCode('os-' . $new_order_id . '');
	$request->setDocCode("OBN-".$return_oID);
	
	$request->setReferenceCode($get_avalara_data['doc_code']);

    $dateTime = new DateTime();

    $request->setDocDate(date("Y-m-d"));           //date

    // $request->setSalespersonCode("");             // string Optional
    
	$request->setCustomerCode($order->customer['id']); // $account - string Required
	
    $request->setCustomerUsageType("");   //string   Entity Usage

    $request->setDiscount(0.00);            //decimal

    $request->setPurchaseOrderNo("");     //string Optional
	
	$request->setCommit(true);

    
	$exemption = '';
	
	$customer_exemption_query = tep_db_fetch_array(tep_db_query("select is_tax_exempt,entry_company_tax_id from customers where customers_id  = '".$order->customer['id']."'"));
		
	if( ( $customer_exemption_query['is_tax_exempt'] == '1') ){
		
		if(!empty($customer_exemption_query['entry_company_tax_id'])){
			$exemption = $customer_exemption_query['entry_company_tax_id'];
		}
		
	}

	$request->setExemptionNo($exemption);         //string   if not using ECMS which keys on customer code
    $request->setDetailLevel(DetailLevel::$Tax);         //Summary or Document or Line or Tax or Diagnostic
	
    $i = 1;
	
	for ($x = 0, $n = sizeof($order->products); $x < $n; $x++) {
		
		$product_upc_query = tep_db_fetch_array(tep_db_query("select p.avalara_tax_code,pe.upc_ean from products as p left join products_extended as pe on (p.products_id = pe.osc_products_id) where p.products_id = '".tep_get_prid($order->products[$x]['products_id'])."'"));
		
		if(!empty($product_upc_query['upc_ean'])){
			$item_code = $product_upc_query['upc_ean'];
		}else{
			$item_code = $product['model'];
		}
		
		$product_tax_code = '';
		if(!empty($product_upc_query['avalara_tax_code'])){
			$product_tax_code = $product_upc_query['avalara_tax_code'];
		}
		
		
		${'line' . $i} = new Line();

        ${'line' . $i}->setNo($i);

        ${'line' . $i}->setItemCode($item_code);

        ${'line' . $i}->setDescription($order->products[$x]['name']);

        ${'line' . $i}->setTaxCode($product_tax_code);

        ${'line' . $i}->setQty($order->products[$x]['qty']);

        ${'line' . $i}->setAmount($order->products[$x]['final_price'] * $order->products[$x]['qty']);

        ${'line' . $i}->setDiscounted('false');

        ${'line' . $i}->setRevAcct('');

        ${'line' . $i}->setRef1('');

        ${'line' . $i}->setRef2('');

        ${'line' . $i}->setExemptionNo('');

        ${'line' . $i}->setCustomerUsageType('');

        $lines[] = ${'line' . $i};

        $i++;

    }
	$request->setLines($lines);
	
	// set original order date on return #start
	$get_old_tax = tep_db_fetch_array(tep_db_query("select `value` as old_tax_amount from orders_total where orders_id = '".$parent_orders_id."' and class = 'ot_avatax'"));
	
	$tax_over_ride = new TaxOverride();
	$tax_over_ride->setTaxOverrideType('TaxDate');
	$tax_over_ride->setTaxAmount(number_format($get_old_tax['old_tax_amount'],2));
	$tax_over_ride->setTaxDate(date("Y-m-d",strtotime($get_avalara_data_query['date_purchased'])));
	$tax_over_ride->setReason('Return');
	
	$request->setTaxOverride($tax_over_ride);
	
	// set original order date on return #ends
	
	

	// Try AvaTax

	try {

        $getTaxResult = $client->getTax($request);
		
		// update avalara data of return order #start
		$ava['avalara_data']['doc_type']       = $getTaxResult->getDocType(); 
        $ava['avalara_data']['doc_code']       = $getTaxResult->getDocCode();
        $ava['avalara_data']['doc_date']       = $getTaxResult->getDocDate();
        $ava['avalara_data']['doc_amount']     = $getTaxResult->getTotalAmount();
        $ava['avalara_data']['doc_total_tax']  = $getTaxResult->getTotalTax();
        $ava['avalara_data']['doc_hash_code']  = $getTaxResult->getHashCode();
		// update avalara data of return order #ends
		
		tep_db_query("update " . TABLE_ORDERS . " set avalara_data = '" . tep_db_prepare_input(serialize($ava['avalara_data'])) . "' where orders_id = '" . $return_oID . "'");
		
		// code to commit #start
        if ($getTaxResult->getResultCode() == SeverityLevel::$Success) {

            $tax_data = array(

                'tax_amount' => $getTaxResult->getTotalTax(),

                'taxable_amount' => $getTaxResult->getTotalTaxable(),

                'total_amount' => $getTaxResult->getTotalAmount(),

            );

        } else {

            foreach ($getTaxResult->getMessages() as $msg) {

                //$messageStack->add('header', 'AvaTax error: ' . $msg->getName() . ": " . $msg->getSummary() . '', 'error');

            }

            return FALSE;

        }

    } catch (SoapFault $exception) {

        $msg = 'SOAP Exception: ';

        if ($exception) {

            $msg .= $exception->faultstring;

        }

       

        return FALSE;

    }
	 updateAvataxLogTable($client->__getLastRequest(),$client->__getLastResponse(),'return_calculation');

    return $tax_data;
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
          <td><span style="float:right;"><?php echo ' <a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params()) .'">' . tep_image_button('button_back.gif', IMAGE_BACK). '</a>'; ?></span></td>
        </tr>
        
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        
        <tr>
        	<td>
            	<table class="table table-bordered table-hover">
                    <tr style="background-color: #999; color:#FFFFFF;">
                        <th> <input type="checkbox" name="all" id="all" onClick="jQuery('input[name*=\'products\']').prop('checked', this.checked);"> </th>
                        <th> <?php echo HEADING_PRODUCT_NAME;?> </th>
                        <th> <?php echo HEADING_QTY_ORDERED; ?> </th>
                    </tr>
                    
                     <?php for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {

						  $store_quanity = getStoreQuanity($order->products[$i]['model']); ?>
                          	<tr class="dataTableRow">
                                <td><?php echo '<input type="checkbox" name="products[]" value="'.$store_quanity['products_id'].'" class="txt_products">';?></td>
                                <td><?php echo $order->products[$i]['name']; ?></td>
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'.$order->products[$i]['qty']; ?></td>
                          </tr>
                          
					<?php } ?>
                      
                    <tr>
                      <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                    </tr>
                    
                    <tr>
                      <td align="right" colspan="3"><?php echo tep_image_button('button_return_products.gif', IMAGE_RETURN_PRODUCTS,' id="return_products"  style="cursor:pointer;" ');?><span id="ajaxloader"></span></td>
                    </tr>
                    
                  </table>
                
				<script type="text/javascript">
				  var oID = '<?php echo (int)$_GET['oID']; ?>';
				  var products = {};
                  	jQuery('#return_products').click(function(){
						
						products = [];
						
						jQuery(".txt_products").each(function() {
							
							if(jQuery(this).prop('checked')){
								products.push(jQuery(this).val());
							}
							
						});
						
						if(products.length > 0){
						
							jQuery.ajax({
						
							url: 'return_orders.php?mode=returnProducts',
							
							type: 'post',
							
							dataType: 'json',
							
							data: 'oID=' + encodeURIComponent(oID)+'&products='+JSON.stringify(products),
							
							beforeSend: function() {
								jQuery('#ajaxloader').html('<img src="images/loading.gif" alt="" class="attention" align="absmiddle" />');
							},
							complete: function() {
								
								jQuery('.attention').remove();
									
							},
							success: function(data) {
								
								if(data['success']){
								
									// success product returned
									alert("Selected product returned successfully..");
									location = 'orders.php?selected_box=customers&oID='+data['new_order_id']+'&action=edit';
								
								}
								
							}
						
						});
						
						}else{
							alert("Error:\n Please select atleast one product to return.");
						}
					});
                  </script>
            </td>
        </tr>
            
      </table></td>
  
  </tr>
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
}?>

<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>