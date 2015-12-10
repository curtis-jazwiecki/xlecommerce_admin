<?php
/*
  $Id: validcategories.php,v 0.01 2002/08/17 15:38:34 Richard Fielder
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
	<td colspan="3" align="center">
    	<input type="radio" name="categories_selection" id="obn_categories" value="1" onChange="getCategories(this.value);" <?php if($_GET['cat_type'] == 'obn'){ echo 'checked';  } ?> > <b>OBN Categories</b> 
    	&nbsp; 
    	<input type="radio" name="categories_selection" id="frontend_categories" value="0" onChange="getCategories(this.value);" <?php if($_GET['cat_type'] == 'frontend'){ echo 'checked';  } ?>> <b>Front-end Categories</b> 
    </td>
</tr>
<tr>
	<td colspan="3" align="center">&nbsp;
    	
    </td>
</tr>


<tr>
	<td colspan="3"><h4><?php echo TEXT_VALID_CATEGORIES_LIST; ?></h4></td>
</tr>
<?
    echo "<tr><th>Select <br> <input type='checkbox' name='selall' id='selall' onclick='selectAllCheckboxes(this);'> </th><th>" . TEXT_VALID_CATEGORIES_ID . "</th><th>" . TEXT_VALID_CATEGORIES_NAME . "</th></tr>";
    
	if( (isset($_GET['cat_type'])) && ($_GET['cat_type'] == 'obn') ){
	
		$result = tep_db_query("SELECT * FROM categories, categories_description WHERE categories.categories_id = categories_description.categories_id and categories_description.language_id = '" . $languages_id . "' ORDER BY categories.categories_id");	
	
	}else if( (isset($_GET['cat_type'])) && ($_GET['cat_type'] == 'frontend') ){
		
		$result = tep_db_query("SELECT * FROM frontend_categories, frontend_categories_description WHERE frontend_categories.categories_id = frontend_categories_description.categories_id and frontend_categories_description.language_id = '" . $languages_id . "' ORDER BY frontend_categories.categories_id");
	
	}
	
	
    while($row = tep_db_fetch_array($result)){
		echo "<tr>";
		echo '<td align="center"><input type="checkbox" value="'.$row["categories_id"].'" name="chkcatbox[]" class="chk_categories_id"></td>';
		echo "<td align='center'>".$row["categories_id"]."</td>";
        echo "<td>".$row["categories_name"]."</td>";
        echo "</tr>";
	}
	
    echo "</table>\n";
?>
<br>
<table width="550" border="0" cellspacing="1">
<tr>
<td align=middle><input type="button" value="Close Window" onClick="ClosePopUp();"></td>
</tr></table>
<script type="text/javascript">

function getCategories(category_type){
	if(category_type == 1){
		location = 'validcategories.php?cat_type=obn';
	}else if(category_type == 0){
		location = 'validcategories.php?cat_type=frontend';
	}
}

function ClosePopUp(){
	var sThisVal = [];
	var category_string = '';
	$('input:checkbox.chk_categories_id').each(function () {
       if(this.checked){
	   	 sThisVal[sThisVal.length] = $(this).val();
	   }
    });
	if(sThisVal.length > 0){
		category_string = sThisVal.join(",");
		window.opener.addCategories(category_string);
	}
	window.close();
}

function selectAllCheckboxes(ele){
	if($(ele).is(':checked')){
    	$(".chk_categories_id").prop('checked', true);
	}else {
		$(".chk_categories_id").prop('checked', false);
	}
}
</script>
</body>
</html>