<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require ('includes/application_top.php');

if( (isset($_GET['mode'])) && ($_GET['mode'] == 'setstatus') ){
	
	tep_db_query("UPDATE `configuration` SET `configuration_value` = '".((int)$_POST['status'] == 1 ? 'true':'false')."' WHERE configuration_key = 'MODULE_ORDER_TOTAL_AVATAX_STATUS'");
	$json['success'] = 1;
	echo json_encode($json);
	exit;
}

if( (isset($_GET['mode'])) && ($_GET['mode'] == 'setsecurity') ){
	
	tep_db_query("UPDATE `configuration` SET `configuration_value` = '".((int)$_POST['enable_logging'])."' WHERE configuration_key = 'MODULE_ORDER_TOTAL_AVATAX_ENABLE_LOGGING'");
	
	tep_db_query("UPDATE `configuration` SET `configuration_value` = '".((int)$_POST['document_commiting'])."' WHERE configuration_key = 'MODULE_ORDER_TOTAL_AVATAX_DOCUMENT_COMMITING'");
	
	tep_db_query("UPDATE `configuration` SET `configuration_value` = '".tep_db_prepare_input($_POST['service_url'])."' WHERE configuration_key = 'MODULE_ORDER_TOTAL_AVATAX_SERVICE_URL'");
	
	tep_db_query("UPDATE `configuration` SET `configuration_value` = '".tep_db_prepare_input($_POST['avatax_code'])."' WHERE configuration_key = 'MODULE_ORDER_TOTAL_AVATAX_CODE'");
	
	tep_db_query("UPDATE `configuration` SET `configuration_value` = '".tep_db_prepare_input($_POST['license_key'])."' WHERE configuration_key = 'MODULE_ORDER_TOTAL_AVATAX_LICENSE_KEY'");
	
	tep_db_query("UPDATE `configuration` SET `configuration_value` = '".tep_db_prepare_input($_POST['account_number'])."' WHERE configuration_key = 'MODULE_ORDER_TOTAL_AVATAX_ACCOUNT_NUMBER'");
	
	tep_db_query("UPDATE `configuration` SET `configuration_value` = '".tep_db_prepare_input($_POST['exemption_no'])."' WHERE configuration_key = 'MODULE_ORDER_TOTAL_AVATAX_EXEMPTION_NO'");
	
	$json['success'] = 1;
	echo json_encode($json);
	exit;
}

if( (isset($_GET['mode'])) && ($_GET['mode'] == 'setaddressstatus') ){
	tep_db_query("UPDATE `configuration` SET `configuration_value` = '".((int)$_POST['address_validate'] == 0 ? 'false':'true')."' WHERE configuration_key = 'MODULE_ORDER_TOTAL_AVATAX_VALIDATE'");
	
	tep_db_query("UPDATE `configuration` SET `configuration_value` = '".tep_db_prepare_input($_POST['selected_country'])."' WHERE configuration_key = 'MODULE_ORDER_TOTAL_AVATAX_VALID_COUNTRIES'");
	
	
	$json['success'] = 1;
	echo json_encode($json);
	exit;
}
?>
<script language="javascript" src="includes/general.js"></script>
<?php include(DIR_WS_LANGUAGES.$language.'/'.FILENAME_AVALARA) ?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<style>
.tabnav{
        dispaly:block;
        padding: 0px;
        overflow: hidden;
    }

    .tabnav li{
    background-color: #eee;
    border: 1px solid #000;
    border-radius: 8px;
    display: block;
    float: left;
    margin: 0 5px;
    padding: 0 15px 5px;
    }
    .tabnav li a{
        color:#000;
    }
    .hideDiv{
        display: none;
    }
    .TabShowContent{
        width: 100%;
        padding: 10px;
        margin-top: 20px;
    }
    li.firstLi{
        background-color: #aaaaaa;
    }
</style>
<!-- script added for address validation start --> 
<script type="text/javascript">
jQuery(document).ready(function(e) {
    jQuery( "#tabs" ).tabs();
	var right_options = '';
	var left_options = '';
	jQuery('#push_right').click(function(){
		 left_options = jQuery('#left_items option:selected').sort().clone();
    	 
		 jQuery(left_options).each(function(index, item) {
			jQuery('#right_items').append(jQuery('<option>', { 
				value: item.value,
				text : item.text 
			}));
			
			jQuery("#left_items option[value='"+item.value+"']").remove();
			
		 });
		 
	});
	
	jQuery('#push_left').click(function(){
		right_options = jQuery('#right_items option:selected').sort().clone();
    	
		jQuery(right_options).each(function(index, item) {
			jQuery('#left_items').append(jQuery('<option>', { 
				value: item.value,
				text : item.text 
			}));
			
			jQuery("#right_items option[value='"+item.value+"']").remove();	
		 });
	});
	
});
</script> 
<!-- script added for address validation ends --> 

<!-- body //-->

<section>

<!-- START Page content-->

<section class="main-content">
<h3><?php echo HEADING_TITLE_AVALARA; ?> <br>
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
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td><table class="table table-bordered table-hover">
              <!-- BOF 10 JAN 2014 RANGE MANAGER////////////////////////////////////////////////////-->
              <tr>
                <td><div id="tabs">
                    <ul>
                      <li><a href="#tabs-1"><?php echo TAB1_HEADING ?></a></li>
                      <li><a href="#tabs-2"><?php echo TAB2_HEADING ?></a></li>
                      <li><a href="#tabs-3"><?php echo TAB3_HEADING ?></a></li>
                      <li><a href="#tabs-4"><?php echo TAB4_HEADING ?></a></li>
                    </ul>
                    <div id="tabs-1"> 
					  <table class="table table-bordered table-hover">
                        <tr>
                          <td class="formArea">
                          <table class="table table-bordered table-hover">
                              <tr>
                                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                              </tr>
                              <tr>
                                <td><?php echo ACCOUNT_VALUE; ?></td>
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('MODULE_ORDER_TOTAL_AVATAX_ACCOUNT_NUMBER',MODULE_ORDER_TOTAL_AVATAX_ACCOUNT_NUMBER,' id="account_number" ');?></td>
                              </tr>
                              <tr>
                                <td><?php echo LICENSE_KEY; ?></td>
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('MODULE_ORDER_TOTAL_AVATAX_LICENSE_KEY',MODULE_ORDER_TOTAL_AVATAX_LICENSE_KEY,' id="license_key" ');?></td>
                              </tr>
                              <tr>
                                <td><?php echo COMPANY_CODE; ?></td>
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('MODULE_ORDER_TOTAL_AVATAX_CODE',MODULE_ORDER_TOTAL_AVATAX_CODE,' id="avatax_code" ');?></td>
                              </tr>
                              <tr>
                                <td><?php echo SERVICE_URL; ?></td>
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('MODULE_ORDER_TOTAL_AVATAX_SERVICE_URL',MODULE_ORDER_TOTAL_AVATAX_SERVICE_URL,' id="service_url" ');?></td>
                              </tr>
                              <tr>
                                <td><?php echo DISABLE_DOCUMENT_COMMITING; ?></td>
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_checkbox_field('MODULE_ORDER_TOTAL_AVATAX_DOCUMENT_COMMITING',"1",MODULE_ORDER_TOTAL_AVATAX_DOCUMENT_COMMITING,'',' id="document_commiting" ');?></td>
                              </tr>
                              <tr>
                                <td><?php echo ENABLE_LOGGING; ?></td>
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_checkbox_field('MODULE_ORDER_TOTAL_AVATAX_ENABLE_LOGGING',"1",MODULE_ORDER_TOTAL_AVATAX_ENABLE_LOGGING,'',' id="enable_logging" ');?></td>
                              </tr>
                              
                              <!--<tr>
                                <td><?php //echo EXEMPTION_NO; ?></td>
                                <td><?php //echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('MODULE_ORDER_TOTAL_AVATAX_EXEMPTION_NO',MODULE_ORDER_TOTAL_AVATAX_EXEMPTION_NO,' id="exemption_no" ');?></td>
                              </tr>-->
                              
                              <input type="hidden" name="MODULE_ORDER_TOTAL_AVATAX_EXEMPTION_NO" value="<?php echo MODULE_ORDER_TOTAL_AVATAX_EXEMPTION_NO; ?>" id="exemption_no">
                              
                              
                            </table></td>
                        </tr>
                        <tr>
                          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                        </tr>
                        <tr>
                          <td align="right">
						  <?php echo tep_image_submit('button_test_connection.gif', IMAGE_TEST_CONNECTION,' id="btn_connection" ') . '&nbsp;'.tep_image_submit('button_update.gif', IMAGE_UPDATE,' id="btn_security" '); ?> <span id="ajaxloader"></span>
                          </td>
                        </tr>
                      </table>
                      
                    </div>
                     <script type="text/javascript">
                    jQuery('#btn_connection').click(function(){
						var service_url = jQuery('#service_url').val();
						var avatax_code = jQuery('#avatax_code').val();
						var license_key = jQuery('#license_key').val();
						var account_number = jQuery('#account_number').val();
						
						jQuery.ajax({
							
							
							url: 'commit_to_alavara.php?mode=validateConnection',
							
							type: 'post',
							
							dataType: 'json',
							
							data: 'service_url=' + encodeURIComponent(service_url) + '&entry_suburb_original=' + encodeURIComponent(avatax_code)+ '&entry_postcode_original=' + encodeURIComponent(avatax_code)+'&license_key=' + encodeURIComponent(license_key)+'&account_number=' + encodeURIComponent(account_number),
							beforeSend: function() {
								
								jQuery('#ajaxloader').html('<img src="images/loading.gif" alt="" class="attention" align="absmiddle" />');
								
							},
							complete: function() {
								
								jQuery('.attention').remove();
								
							},
							success: function(data) {
								
								if(data['error']){
									alert("Failed!");
								}else if(data['success']){
									alert("Success!");
								}
							}
						
						});
					
					});
					
					
					
					jQuery('#btn_security').click(function(){
						var enable_logging = '0';
						
						if(jQuery("#enable_logging").is(':checked')){
							enable_logging = '1';
						}
						
						var document_commiting = '0';
						
						if(jQuery("#document_commiting").is(':checked')){
							document_commiting = '1';
						}
						
						var service_url = jQuery('#service_url').val();
						var avatax_code = jQuery('#avatax_code').val();
						var license_key = jQuery('#license_key').val();
						var account_number = jQuery('#account_number').val();
						var exemption_no = jQuery('#exemption_no').val();
						
						jQuery.ajax({
							
							url: 'avalara.php?mode=setsecurity',
							
							type: 'post',
							
							dataType: 'json',
							
							data: 'enable_logging=' + encodeURIComponent(enable_logging)+'&document_commiting='+encodeURIComponent(document_commiting)+'&service_url='+encodeURIComponent(service_url)+'&avatax_code='+encodeURIComponent(avatax_code)+'&license_key='+encodeURIComponent(license_key)+'&account_number='+encodeURIComponent(account_number)+'&exemption_no='+encodeURIComponent(exemption_no),
							
							beforeSend: function() {
								jQuery('#ajaxloader').html('<img src="images/loading.gif" alt="" class="attention" align="absmiddle" />');
							},
							complete: function() {
								
								jQuery('.attention').remove();
								
							},
							success: function(data) {
								
								if(data['success']){
								
									alert("Data Updated Successfully!");
								
								}
								
							}
						
						});
							
					});
                    </script>
                    <!-- Tab 1 Ends --> 
                    
                    
                    
                    <!--- Tab 2 -- Start --->
                    <div id="tabs-2"> 
                      <table class="table table-bordered table-hover">
                        <tr>
                          <td class="formArea">
                          	<table class="table table-bordered table-hover">
                              <tr>
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                              </tr>
                              <tr>
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_checkbox_field('MODULE_ORDER_TOTAL_AVATAX_STATUS',"1",(MODULE_ORDER_TOTAL_AVATAX_STATUS == 'false' ? 1:0),'',' id="avatax_status" ');?> <strong><?php echo DISABLE_TAX_CALCULATION ?> </strong></td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                        </tr>
                        <tr>
                          <td align="right">
						  	<?php echo  tep_image_submit('button_update.gif', IMAGE_UPDATE,' id="btn_avatax_status" '); ?>
                            <span id="ajaxloader2"></span>
                          </td>
                        </tr>
                      </table>
                    </div>
                    <script type="text/javascript">
                    jQuery('#btn_avatax_status').click(function(){
						var status = '';
						if(jQuery("#avatax_status").is(':checked')){
							status = '0';
						}else{
							status = '1';
						}
						jQuery.ajax({
							
							url: 'avalara.php?mode=setstatus',
							
							type: 'post',
							
							dataType: 'json',
							
							data: 'status=' + encodeURIComponent(status),
							
							beforeSend: function() {
								jQuery('#ajaxloader2').html('<img src="images/loading.gif" alt="" class="attention" align="absmiddle" />');
							},
							complete: function() {
								
								jQuery('.attention').remove();
								
							},
							success: function(data) {
								
								if(data['success']){
								
									alert("Data Updated Successfully!");
								
								}
								
							}
						
						});
							
					});
                    </script>
                    
                    
                    
                    <!-- Tab 2 -- ends ---> 
                    
                    
                    <!--- Tab 3 -- Start --->
                    <div id="tabs-3"> 
					  <table class="table table-bordered table-hover">
                        <tr>
                          <td class="formArea"><table class="table table-bordered table-hover">
                              <tr>
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                              </tr>
                              <tr>
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_checkbox_field('MODULE_ORDER_TOTAL_AVATAX_VALIDATE',"1",(MODULE_ORDER_TOTAL_AVATAX_VALIDATE == 'false' ? 1:0),'',' id="address_validate" ');?> <strong><?php echo DISABLE_ADDRESS_VALIDATION; ?> </strong></td>
                              </tr>
                              <tr>
                                <td><!-- address validation code start -->
                                  
                                  <table align="center">
                                    <tr>
                                      <th colspan="3">Choose the countries which will be used for Address Validation.</th>
                                    </tr>
                                    <tr>
                                      <td><select multiple="multiple" name="left_items" id="left_items" style="width:300px; height:200px;">
                                          <?php
									   $countries = tep_valid_countries();
									   foreach($countries as $cid => $cname ){
									   	echo '<option value="'.$cid.'">'.$cname.'</option>';
									   }
									  ?>
                                        </select></td>
                                      <td><span id="push_right" style="cursor:pointer;">>>></span> <br />
                                        <br />
                                        <span id="push_left" style="cursor:pointer;"><<<</span></td>
                                      <td><select multiple="multiple" name="right_items" id="right_items"  style="width:300px; height:200px;">
                                      <?php
									   $countries = tep_selected_countries();
									   foreach($countries as $cid => $cname ){
									   	echo '<option value="'.$cid.'">'.$cname.'</option>';
									   }
									  ?>
                                      
                                      
                                      
                                        </select></td>
                                    </tr>
                                  </table>
                                  
                                  <!-- address validation code ends --></td>
                              </tr>
                            </table></td>
                        </tr>
                        <tr>
                          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                        </tr>
                        <tr>
                          <td align="right">
						  	<?php echo  tep_image_submit('button_update.gif', IMAGE_UPDATE,' id="btn_address_status" '); ?><span id="ajaxloader3"></span></td>
                        </tr>
                      </table>
                    </div>
                    <script type="text/javascript">
                    jQuery('#btn_address_status').click(function(){
						var address_validate = '';
						if(jQuery("#address_validate").is(':checked')){
							address_validate = '0';
						}else{
							address_validate = '1';
						}
						
						jQuery("#right_items option").prop("selected", "selected");
						
						var selected_country = jQuery('#right_items').val();
						
						if(selected_country == null){
							selected_country = '';
						}
						
						jQuery.ajax({
							
							url: 'avalara.php?mode=setaddressstatus',
							
							type: 'post',
							
							dataType: 'json',
							
							data: 'address_validate=' + encodeURIComponent(address_validate)+ '&selected_country='+encodeURIComponent(selected_country),
							
							beforeSend: function() {
								jQuery('#ajaxloader3').html('<img src="images/loading.gif" alt="" class="attention" align="absmiddle" />');
							},
							complete: function() {
								
								jQuery('.attention').remove();
								
							},
							success: function(data) {
								
								if(data['success']){
								
									alert("Data Updated Successfully!");
									jQuery("#right_items option").prop("selected", "");
								
								}
								
							}
						
						});
							
					});
                    </script>
                    <!-- Tab 3 -- ends ---> 
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    <!-- Tab 4 start -->
                    <div id="tabs-4">
                      <h3> <?php //echo TEXT_LOG; ?> </h3>
                      
                      
					  	
					  	<table class="table table-bordered table-hover">
                        	<tr>
                            	<th>Date Created</th>
                                <th>Action</th>
                                <th>Request</th>
                                <th>Response</th>
                            </tr>
                            <?php
							  $getlogs = getLogs();
							  foreach($getlogs as $getlog){?>
                                <tr class="dataTableRow">
                                    <td nowrap><?php echo $getlog['date_created']; ?></td>
                                    <td><?php echo str_replace("_"," ",$getlog['action']); ?></td>
                                    <td><textarea name="" id="" rows="10" cols="30"><?php echo $getlog['request']; ?></textarea></td>
                                    <td><textarea name="" id="" rows="10" cols="30"><?php echo $getlog['response']; ?></textarea></td>
                                </tr>
                                
                                <tr class="">
                                	<td colspan="4">&nbsp;</td>
                                </tr>
                                
                             <?php } ?>
                        </table>
					  
					  
                    </div>
                    <!-- Tab 4 ends --> 
                  </div></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
    <!-- body_text_eof //--> 
    
  </tr>
</table>

<!-- END your table--> 

<!-- body_eof //-->

<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
