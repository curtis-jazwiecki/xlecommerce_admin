<?php
$cron_script='yes';
require_once('cron_application_top.php');

//$cat_query = tep_db_query("select categories_id from categories where parent_id='0' and categories_status='1' order by categories_id");
$cat_query = tep_db_query("select categories_id from categories where parent_id='0' order by categories_id");
while($entry = tep_db_fetch_array($cat_query)){
	echo $entry['categories_id'] . '(' . products_count($entry['categories_id']) . ')' . "\n";
	handle_empty_category($entry['categories_id']);
}

function handle_empty_category($categories_id, $tab = "\t"){
	//$cat_query = tep_db_query("select categories_id from categories where parent_id='" . (int)$categories_id . "' and categories_status='1' order by categories_id");
	$cat_query = tep_db_query("select categories_id from categories where parent_id='" . (int)$categories_id . "' order by categories_id");
	if (tep_db_num_rows($cat_query)){
		while($entry = tep_db_fetch_array($cat_query)){
			echo $tab . $entry['categories_id'] . '(' . products_count($entry['categories_id']) . ')' . "\n";
			handle_empty_category($entry['categories_id'], $tab . "\t");
		}
		$cat_query_2 = tep_db_query("select categories_id from categories where parent_id='" . (int)$categories_id . "' and categories_status='1'");
		if (!tep_db_num_rows($cat_query_2) && category_is_empty($categories_id)){
			tep_db_query("update categories set categories_status='0', last_modified=now() where categories_id='" . (int)$categories_id . "'");
			echo $tab . 'Disabled' . "\n";
		}
	} else {
		if (category_is_empty($categories_id)){
			tep_db_query("update categories set categories_status='0', last_modified=now() where categories_id='" . (int)$categories_id . "'");
			echo $tab . 'Disabled' . "\n";
		}
	}	
}

function category_is_empty($categories_id){
	//$prod_query = tep_db_query("select products_id from products_to_categories where categories_id='" . (int)$categories_id . "'");
	$prod_query = tep_db_query("select a.products_id from products_to_categories a inner join products b on a.products_id=b.products_id where a.categories_id='" . (int)$categories_id . "' and b.products_status='1'");
	if (tep_db_num_rows($prod_query)){
		return false;
	} else {
		return true;
	}
}

function products_count($categories_id){
	$prod_query = tep_db_query("select products_id from products_to_categories where categories_id='" . (int)$categories_id . "'");
	return tep_db_num_rows($prod_query);
}
?>