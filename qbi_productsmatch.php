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

require('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/qbi_general.php');
require(DIR_WS_INCLUDES . 'qbi_version.php');
require(DIR_WS_INCLUDES . 'qbi_definitions.php');
require(DIR_WS_INCLUDES . 'qbi_page_top.php');
require(DIR_WS_INCLUDES . 'qbi_menu_tabs.php');

if (isset($stage) AND $stage=="produpdate") {
  prod_update($product_menu);
  echo MATCH_SUCCESS;
}
?>
<table class="table table-bordered table-hover">
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
<tr><td colspan="3"><input name="submit" type="submit" id="submit" value="<?php echo MATCH_BUTTON ?>" /></td></tr>
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
</script>
<?php
require(DIR_WS_INCLUDES . 'qbi_page_bot.php');
?>