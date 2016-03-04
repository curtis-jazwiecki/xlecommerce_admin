<?php
/*
	$Id: qbi_productsmatch.php,v 2.10 2005/05/08 al Exp $
	
	Quickbooks Import QBI
	contribution for osCommerce
	ver 2.10 May 8, 2005
	(c) 2005 Adam Liberman
	www.libermansound.com
	info@libermansound.com
	Please use the osC forum for support.
	Released under the GNU General Public License

    This file is part of Quickbooks Import QBI.

    Quickbooks Import QBI is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Quickbooks Import QBI is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Quickbooks Import QBI; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define('RECORDS_IN_BATCH','10000');

require('includes/application_top.php');

if( (isset($_POST['mode'])) && ($_POST['mode'] == 'calculaterecords') ){
	
	
	$products_count = tep_db_num_rows(tep_db_query("SELECT * FROM ".TABLE_PRODUCTS." AS p LEFT JOIN ".TABLE_PRODUCTS_ATTRIBUTES." AS pa ON p.products_id=pa.products_id"));
	$response = array(
		'message' 		=> 'success', 
		'total_records' => $products_count,
		'total_batches'	=> ceil($products_count/RECORDS_IN_BATCH)
	);

	echo json_encode($response);
	exit;
}

if( (isset($_POST['mode'])) && ($_POST['mode'] == 'updaterecords') ){
	
	

	
	$itemid = $_POST['quickbook_value'];
	$limit_start = $_POST['record_starts'];
	$records_to_skip = $_POST['records_to_skip'];
		
	settype($itemid,'integer');
	settype($limit_start,'integer');
		
	if ($itemid>0) {
		
		if($records_to_skip == 0){
			tep_db_query("DELETE FROM ".TABLE_QBI_PRODUCTS_ITEMS); // empty table
		}
		
		 $products_query = tep_db_query("SELECT *, p.products_id AS pproducts_id FROM ".TABLE_PRODUCTS." AS p LEFT JOIN ".TABLE_PRODUCTS_ATTRIBUTES." AS pa ON p.products_id=pa.products_id order by pproducts_id ASC");
		 
		 if(tep_db_num_rows($products_query)){
			 $start = 0;
			 while ($result = tep_db_fetch_array($products_query)) {
				
				if($records_to_skip > 0){
					$records_to_skip--;
					$start++;
					continue;
				}
				
				if($start == $limit_start){
					break;
				}
				
				$prod_id = $result["pproducts_id"];
				$optval_id = $result["options_values_id"];
					
				$resultqbd = tep_db_query("SELECT * FROM ".TABLE_PRODUCTS." As p, ".TABLE_PRODUCTS_ATTRIBUTES." AS pa WHERE p.products_id='$prod_id' AND p.products_id=pa.products_id AND pa.options_values_id='$optval_id'");
				if ($myrowqbd = tep_db_fetch_array($resultqbd)) {
					tep_db_query("INSERT INTO ".TABLE_QBI_PRODUCTS_ITEMS." (products_id,products_options_values_id,qbi_groupsitems_refnum) VALUES ('".$prod_id."','".$myrowqbd["options_values_id"]."','$itemid')");
				}else {
					tep_db_query("INSERT INTO ".TABLE_QBI_PRODUCTS_ITEMS." (products_id,products_options_values_id,qbi_groupsitems_refnum) VALUES ('".$prod_id."','0','$itemid')");
				}
				
				$start++;
			 }
				
		 }
		
	}
	
	$response = array(
		'message' 		=> 'success'
	);

	echo json_encode($response);
	exit;
}

require(DIR_WS_LANGUAGES . $language . '/qbi_general.php');
require(DIR_WS_INCLUDES . 'qbi_version.php');
require(DIR_WS_INCLUDES . 'qbi_definitions.php');
require(DIR_WS_INCLUDES . 'qbi_page_top.php');
require(DIR_WS_INCLUDES . 'qbi_menu_tabs.php');

if (isset($stage) AND $stage=="produpdate") {
  set_time_limit(0);
  ignore_user_abort(1);
  prod_update($product_menu);
  echo MATCH_SUCCESS;
}
?>
<table class="table table-bordered table-hover" id="other_option">
<form action="<?php echo $_SERVER[PHP_SELF]?>" method="post" name="qbi_products" id="qbi_products">
<input name="stage" id="stage" type="hidden" value="produpdate" />
<input name="search_page" id="search_page" type="hidden" value="<?php echo $search_page?>" />
<?php
$Query = "SELECT COUNT(*) AS cnt FROM ".TABLE_PRODUCTS." AS p LEFT JOIN ".TABLE_PRODUCTS_ATTRIBUTES." AS pa ON p.products_id=pa.products_id"; 
$result = tep_db_query($Query) or die(mysql_error()); 
$row = tep_db_fetch_array($result); 
$count = $row['cnt']; 
if($count > 0){
	 
  //$page = new page_class($count,QBI_PROD_ROWS,10); 
  $page = new page_class($count); 
  $limit = $page->get_limit();
  $resultqbc = tep_db_query("SELECT *, p.products_id AS pproducts_id FROM ".TABLE_PRODUCTS." AS p LEFT JOIN ".TABLE_PRODUCTS_ATTRIBUTES." AS pa ON p.products_id=pa.products_id ORDER BY products_model, options_values_id ".$limit);
  $hstring = $page->make_head_string(PRODMATCH_TITLE); 
  $pstring = $page->make_page_string(); //add the other variables to pass to next page in a similar fashion 
  echo "<tr><th colspan='3' class='counter'>$hstring</th></tr>\r\n"; 
  echo "<tr><td colspan='3'>&nbsp;</td></tr>\r\n"; 
  echo "<tr><th class='colhead'>".MATCH_OSC."</th><th></th><th class='colhead'>".MATCH_QB."</th></tr>\r\n";  ?>
  <tr>
  	<th colspan="2">
    	<table class="lists" width="100%" bgcolor="#FFFFFF">
 <tr>
  	<th colspan="2" align="left">
    	<table width="100%">
        	<tr>
            	<td>Products</td>
                <td>Quickbooks</td>
            </tr>
            <tr>
            	<td>Match All <input type="checkbox" name="match_all_products" value="1" id="match_all_products" /></td>
                <?php echo item_menu(0,0); ?>
            </tr>
        </table>
    </th>
    <th>&nbsp;</th>
 </tr>
</table>
	</th>
	<th>&nbsp;</th>
    
  </tr>
  <tr>
  	<th id="progress-notifications" colspan="3" align="center"></th>
  </tr>
  <tr>
  	<th id="update-notifications" colspan="3" align="center"></th>
  </tr>
  
  
  
  
  
  <?php
  while ($myrowqbc = tep_db_fetch_array($resultqbc)) {
	$prod_id=$myrowqbc["pproducts_id"];
	$optval_id=$myrowqbc["options_values_id"];
	$resultqbx = tep_db_query("SELECT * FROM ".TABLE_PRODUCTS." AS p, ".TABLE_PRODUCTS_DESCRIPTION." AS pd WHERE p.products_id='$prod_id' AND p.products_id=pd.products_id AND language_id='$languages_id'");
	$myrowqbx = tep_db_fetch_array($resultqbx);
	$resultqbd = tep_db_query("SELECT * FROM ".TABLE_PRODUCTS." As p, ".TABLE_PRODUCTS_ATTRIBUTES." AS pa, ".TABLE_PRODUCTS_OPTIONS_VALUES." AS pov WHERE p.products_id='$prod_id' AND p.products_id=pa.products_id AND pov.products_options_values_id=pa.options_values_id AND pov.language_id='$languages_id' AND pa.options_values_id='$optval_id' ORDER BY pov.products_options_values_name");
	if ($myrowqbd = tep_db_fetch_array($resultqbd)) {
		echo "<tr><td class='oscmodel'>".substr($myrowqbd["products_model"].":".$myrowqbd["products_options_values_name"],0,24)."</td><td class='oscname'>".substr($myrowqbx["products_name"]." - ".$myrowqbd["products_options_values_name"],0,36)."</td>";
		item_menu($prod_id,$myrowqbd["products_options_values_id"]);
	} else {
		echo "<tr><td class='oscmodel'>".substr($myrowqbc["products_model"],0,24)."</td><td class='oscname'>".substr($myrowqbx["products_name"],0,36)."</td>";
		item_menu($prod_id,0);
	}
}
echo "<tr><td colspan=\"3\">&nbsp;</td></tr>\r\n";
echo "<tr><td colspan=\"3\" class='pagelist'>$pstring</td></tr>\r\n";
}
?>
<tr><td colspan="3"><input name="submit" type="submit" id="submit" value="<?php echo MATCH_BUTTON ?>" onclick="return calculateRecords();" /></td></tr>
</form>
</table>
<script type="text/javascript">
jQuery(document).ready(function(e) {
    
	jQuery("#match_all_products").click(function(){
		if(jQuery("#match_all_products").is(':checked')){
			jQuery("#other_option").find("select").attr("disabled", "disabled");
			jQuery('[name="product_menu[0-0]"]').removeAttr("disabled");
		}else{
    		jQuery("#other_option").find("select").removeAttr("disabled");
		}
	});

});

var batches = 0;
var current_batch = 1;

function calculateRecords(){
	jQuery('#update-notifications').empty();
	if(jQuery("#match_all_products").is(':checked')){
		
		jQuery.ajax({
					
			url: 'qbi_productsmatch.php',
			type: 'post',
			dataType: 'json',
			data: 'mode=calculaterecords',
			beforeSend: function() {
				jQuery('#progress-notifications').html('<p class="attention"><img src="images/loading.gif" alt="" align="absmiddle" /> Please wait...</p>');
				jQuery('#submit').attr('disabled', true);
			},
			success: function(data) {
				if (data['message'] == 'success') {
					jQuery('#progress-notifications').html('<p class="success">Total Records: <font color="red">'+data['total_records']+'</font> | Total Batches To Update: <font color="red">'+data['total_batches']+'</font></p>');
					
					batches = parseInt(data['total_batches']);
					
					if(batches > 0){
						updateRecords(current_batch);
					}
					
				}
			}
		});
		return false;
	
	
	}else{
		return true;
	}
}
var record_starts = <?php echo RECORDS_IN_BATCH; ?>;
var break_in_blocks = <?php echo RECORDS_IN_BATCH; ?>;

var records_to_skip = 0;
function updateRecords(batch_no){
	var quickbook_value = jQuery('[name="product_menu[0-0]"]').val();
	jQuery.ajax({
		url: 'qbi_productsmatch.php',
		type: 'post',
		dataType: 'json',
		data: 'mode=updaterecords&quickbook_value='+quickbook_value+'&record_starts='+record_starts+'&records_to_skip='+records_to_skip,
		beforeSend: function() {
			jQuery('#update-notifications').append('<p class="attention1"><img src="images/loading.gif" alt="" align="absmiddle" /> Updating Batch #'+batch_no+' Please wait...</p>');
			jQuery('#submit').attr('disabled', true);
		},
		complete: function() {
			
		},
		success: function(data) {
			if (data['message'] == 'success') {
				jQuery('.attention1').remove();
				jQuery('#update-notifications').append('<p class="success"><font color="red">Batch #'+batch_no+' updated successfully</p>');
				batch_no++;
				if(batch_no <= batches){
					records_to_skip = record_starts;
					record_starts = record_starts + parseInt(break_in_blocks);
					updateRecords(batch_no);
				}else{
					jQuery('#update-notifications').html('<p class="success"><img src="images/loading.gif" alt="" align="absmiddle" /> Please wait refreshing database...</p>');
					setTimeout(function(){
						location = 'qbi_productsmatch.php';
					},2000);
				}
				
			}
		}
	});
}
	
</script>

<style type="text/css">
.attention {
	color:#FF0000;
	font-weight:bold;
	text-align:center;
}
.attention1 {
	color:#FF0000;
	font-weight:bold;
	text-align:center;
}
.success {
	color: #063;
	font-weight:bold;
	text-align:center;
}
</style>
<?php
require(DIR_WS_INCLUDES . 'qbi_page_bot.php');
?>