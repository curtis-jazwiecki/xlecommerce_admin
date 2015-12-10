<?php
/*
  $Id: validproducts.php,v 0.01 2002/08/17 15:38:34 Richard Fielder

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  

  Copyright (c) 2002 Richard Fielder

  Released under the GNU General Public License
*/

require('includes/application_top.php');


?>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<title>Valid Categories/Products List</title>
<style type="text/css">
<!--
h4 {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: x-small; text-align: center}
p {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
th {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
td {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
-->
</style>
<head>
<body>
<table width="550" border="1" cellspacing="1" bordercolor="gray">
<tr>
<td colspan="4">
<h4><?php echo TEXT_VALID_PRODUCTS_LIST; ?></h4>
</td>
</tr>
<?
    echo "<tr><tr><th>Select <input type='checkbox' name='selall' id='selall' onclick='selectAllCheckboxes(this);'></th><th>". TEXT_VALID_PRODUCTS_ID . "</th><th>" . TEXT_VALID_PRODUCTS_NAME . "</th><th>" . TEXT_VALID_PRODUCTS_MODEL . "</th></tr><tr>";
    $result = tep_db_query("SELECT * FROM products, products_description WHERE products.products_id = products_description.products_id and products_description.language_id = '" . $languages_id . "' and (parent_products_model IS NULL or parent_products_model = '') ORDER BY products_description.products_name");
    if ($row = tep_db_fetch_array($result)) {
        do {
            echo "<tr>";
			echo '<td align="center"><input type="checkbox" value="'.$row["products_id"].'" name="chkproductbox[]" class="chk_products_id"></td>';
			echo "<td align='center'>".$row["products_id"]."</td>";
            echo "<td>".$row["products_name"]."</td>";
            echo "<td>".$row["products_model"]."</td>";
            echo "</tr>";
        }
        while($row = tep_db_fetch_array($result));
    }
    echo "</table>\n";
?>
<br>
<table width="550" border="0" cellspacing="1">
<tr>
<td align=middle><input type="button" value="Close Window" onClick="ClosePopUp();"></td>
</tr></table>
<script type="text/javascript">
function ClosePopUp(){
	var sThisVal = [];
	var product_string = '';
	$('input:checkbox.chk_products_id').each(function () {
       if(this.checked){
	   	 sThisVal[sThisVal.length] = $(this).val();
	   }
    });
	if(sThisVal.length > 0){
		product_string = sThisVal.join(",");
		window.opener.addProducts(product_string);
	}
	window.close();
}

function selectAllCheckboxes(ele){
	if($(ele).is(':checked')){
    	$(".chk_products_id").prop('checked', true);
	}else {
		$(".chk_products_id").prop('checked', false);
	}
}
</script>
</body>
</html>