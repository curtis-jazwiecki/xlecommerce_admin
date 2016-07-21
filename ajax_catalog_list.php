<?php
/**
 * Facilitates ajax request calls for catalog listing
 * CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
**/
header('Content-Type: text/xml');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 01 Jul 2000 00:00:00 GMT");

require('includes/application_top.php');

$id = $HTTP_GET_VARS['id'];
$query_type = $HTTP_GET_VARS['type'];
if (empty($id)){
	$id = 'C0';
}
$type = strtoupper(substr($id, 0, 1));
$id_number = (int)substr($id, 1, strlen($id)-1);

if ($type=='C'){
	$xml = '<?xml version="1.0"?>';
	$sql = tep_db_query("select b.categories_name as `key`, a.categories_id as `value` " . 
						" from " . TABLE_CATEGORIES . " a " .
						" inner join " . TABLE_CATEGORIES_DESCRIPTION . " b on a.categories_id=b.categories_id " .
						" where a.categories_status='1' and b.language_id='" . (int)$languages_id . "' " .
						" and a.parent_id='" . $id_number . "' " .
						" order by a.sort_order, b.categories_name");
	if (tep_db_num_rows($sql)){
		$xml .= '<categories>';
		while ($row = tep_db_fetch_array($sql)){
			$xml .= '<element>' .
						'<key>' .
							htmlentities($row['key']) .
						'</key>' .
						'<value>' .
							'C' . htmlentities($row['value']) .
						'</value>' .
					'</element>';
		}
		$xml .= '</categories>';
	}else{
		if ($query_type=='F'){
			$sql = tep_db_query("select c.products_name as `key`, a.products_id as `value` " .
				   " from " . TABLE_PRODUCTS . " a " .
				   " inner join " . TABLE_PRODUCTS_TO_CATEGORIES . " b on a.products_id=b.products_id " .
				   " inner join  " . TABLE_PRODUCTS_DESCRIPTION  . " c on a.products_id=c.products_id " .
				   " where a.products_status='1' and c.language_id='" . (int)$languages_id . "' and b.categories_id='" . $id_number . "' " .
				   " and a.products_id not in (select d.products_id from " . TABLE_FEATURED . " d) " .
				   " and (a.parent_products_model is null or a.parent_products_model='') " .
				   " order by c.products_name");
		}elseif ($query_type=='X'){
			$sql = tep_db_query("select c.products_name as `key`, a.products_id as `value` " .
				   " from " . TABLE_PRODUCTS . " a " .
				   " inner join " . TABLE_PRODUCTS_TO_CATEGORIES . " b on a.products_id=b.products_id " .
				   " inner join  " . TABLE_PRODUCTS_DESCRIPTION  . " c on a.products_id=c.products_id " .
				   " where a.products_status='1' and c.language_id='" . (int)$languages_id . "' and b.categories_id='" . $id_number . "' " .
				   " and a.products_id not in (select d.products_id from " . TABLE_PRODUCTS_XSELL . " d) " .
				   " and (a.parent_products_model is null or a.parent_products_model='') " .
				   " order by c.products_name");
		}elseif ($query_type=='XR'){
			$sql = tep_db_query("select c.products_name as `key`, a.products_id as `value` " .
				   " from " . TABLE_PRODUCTS . " a " .
				   " inner join " . TABLE_PRODUCTS_TO_CATEGORIES . " b on a.products_id=b.products_id " .
				   " inner join  " . TABLE_PRODUCTS_DESCRIPTION  . " c on a.products_id=c.products_id " .
				   " where a.products_status='1' and c.language_id='" . (int)$languages_id . "' and b.categories_id='" . $id_number . "' " .
				   " and (a.parent_products_model is null or a.parent_products_model='') " .
				   " order by c.products_name");
		}
		if (tep_db_num_rows($sql)){
			$xml .= '<products>';
			while ($row = tep_db_fetch_array($sql)){
				$xml .= '<element>' .
							'<key>' .
								htmlspecialchars($row['key']) .
							'</key>' .
							'<value>' .
								'P' . htmlentities($row['value']) .
							'</value>' .
						'</element>';
			}
			$xml .= '</products>';
		}

	}
	echo $xml;
}



require(DIR_WS_INCLUDES . 'application_bottom.php');
?>