<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
include('includes/application_top.php');
$handle = fopen(DIR_FS_CATALOG . 'temp/taxonomy.en-US.txt', 'r');
if ($handle){
	while ( ($entry = fgets($handle) ) !== false ){
		$entry = trim($entry);
		$pos = strpos($entry, '#');
		if ( empty($entry) || $pos=== true ) continue;
		
		$categories = explode('>', $entry);
		
		$level = 1;
		$parent_category_id = 0;
		foreach($categories as $category){
			$category_name = trim($category);
			$category_id = getCategoryId($category_name, $level, $parent_category_id);
			$parent_category_id = $category_id;
			$level++;
		}
	}
	fclose($handle);
}
echo 'done';
function getCategoryId($category_name, $level = 1, $parent_id = 0){
	$sql = tep_db_query("select category_id from google_categories where category_name='" . tep_db_input($category_name) . "' and category_level='" . (int)$level . "' and parent_category_id='" . (int)$parent_id . "'");
	if (tep_db_num_rows($sql)){
		$info = tep_db_fetch_array($sql);
		return $info['category_id'];
	} else {
		tep_db_query("insert into google_categories (category_level, category_name, parent_category_id) values ('" . (int)$level . "', '" . tep_db_input($category_name) . "', '" . (int)$parent_id . "')");
		$id = tep_db_insert_id();
		return $id;
	}
}

include('includes/application_bottom.php');
?>