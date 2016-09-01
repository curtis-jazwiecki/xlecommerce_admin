<?php
/*
  $Id: categories.php,v 1.146 2003/07/11 14:40:27 hpdl Exp $
 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
 */
require ('includes/application_top.php');
if ((isset($_GET['mode'])) && ($_GET['mode'] == 'update_spec_value')) {
    tep_db_query("delete from product_specifications where  specification_id = '" . (int) $_POST['old_specification_id'] . "' and obn_spec = '0' and products_id = '" . (int) $_POST['products_id'] . "'");
    tep_db_query("INSERT IGNORE into product_specifications set specification_id = '" . (int) $_POST['specification_id'] . "', obn_spec = '0', products_id = '" . (int) $_POST['products_id'] . "'");
    die("OK");
}
if(isset($_POST['mode']) && ($_POST['mode'] == 'setAmazonTheme') ){
			
	tep_db_query("UPDATE `products` SET `variation_theme_id` = '".(int)$_POST['val']."' WHERE `products_id` = '".(int)$_POST['pID']."'");
	
	// delete all child product variations
	
	tep_db_query("delete from products_variations where products_id IN (select products_id FROM `products` WHERE `parent_products_model` LIKE (select products_model from `products` WHERE products_id = '".(int)$_POST['pID']."') )");
	
	
	$json_array = array("success" => "1"); 
	echo json_encode($json_array);
	
	die();
}
if ((isset($_GET['mode'])) && ($_GET['mode'] == 'getspecname')) {
    $json = array();
    $specification_query = tep_db_query("select name from  product_specification_names where name like  '%" . $_GET['filter_name'] . "%'");
    while ($result = tep_db_fetch_array($specification_query)) {
        $json[] = array(
            'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
        );
    }
    echo json_encode($json);
    exit;
}
if ((isset($_GET['mode'])) && ($_GET['mode'] == 'getavataxcode')) {
    $json = array();
    $avatax_code_query = tep_db_query("select tax_code from avatax_code_list where LCASE(tax_code) like  '%" . strtolower($_GET['filter_name']) . "%'");
    while ($result = tep_db_fetch_array($avatax_code_query)) {
        $json[] = array(
            'name' => strip_tags(html_entity_decode($result['tax_code'], ENT_QUOTES, 'UTF-8')),
        );
    }
    echo json_encode($json);
    exit;
}
if ((isset($_GET['mode'])) && ($_GET['mode'] == 'remove_spec')) {
    tep_db_query("delete from  product_specifications where products_id = '" . (int) $_POST['products_id'] . "' and specification_id = '" . (int) $_POST['specification_id'] . "'");
    die("OK");
}
if ((isset($_GET['mode'])) && ($_GET['mode'] == 'get_spec_value')) {
    $specification_value_query = tep_db_query("select id,value from product_specification_values as psv left join product_specifications as ps on (psv.id=ps.specification_id and ps.products_id='" . $_GET['products_id'] . "') where ps.specification_id is null and specification_name_id = '" . (int) $_POST['id'] . "'");
    $str_specification_values = '';
    $selected_specification_id = 0;
    if (isset($_POST['specification_id']) && $_POST['specification_id'] != '') {
        $selected_specification_id = $_POST['specification_id'];
    }
    while ($result = tep_db_fetch_array($specification_value_query)) {
        $str = '';
        if ($selected_specification_id == $result['id']) {
            $str = 'selected="selected"';
        }
        $str_specification_values .= '<option value="' . $result['id'] . '" ' . $str . '>' . $result['value'] . '</option>';
    }
    die($str_specification_values);
}
if ((isset($_GET['mode'])) && ($_GET['mode'] == 'add_new_spec_value')) {
    $specification_name_new = tep_db_prepare_input($HTTP_POST_VARS['specification_name_new']);
    $specification_value_new = tep_db_prepare_input($HTTP_POST_VARS['specification_value_new']);
    $products_id = tep_db_prepare_input($HTTP_POST_VARS['products_id']);
    $check_specification_name_query = tep_db_query("select id,name from  product_specification_names where name like '" . $specification_name_new . "'");
    if (tep_db_num_rows($check_specification_name_query)) {
        $specification_name_id = tep_db_fetch_array($check_specification_name_query);
        $check_specification_value_query = tep_db_query("select value from  product_specification_values where value like '" . $specification_value_new . "' and specification_name_id = '" . $specification_name_id['id'] . "'");
        if (tep_db_num_rows($check_specification_value_query)) {
            die("-2");
        }
    }
    if (isset($specification_name_id['id']) && !empty($specification_name_id['id'])) {
        $specification_name_id = $specification_name_id['id'];
    } else {
        // insert new specification
        tep_db_query("insert into product_specification_names set name = '" . $specification_name_new . "'");
        $specification_name_id = tep_db_insert_id();
    }
    tep_db_query("insert into product_specification_values set value = '" . $specification_value_new . "',specification_name_id = '" . $specification_name_id . "'");
    $specification_value_id = tep_db_insert_id();
    die('<option value="' . $specification_name_id . '">' . $specification_name_new . '</option>');
}
//parent-child 25Feb2014 (MA) BOF
if (isset($_GET['action']) && $_GET['action'] == 'getParentProduct') {
    $pr_model = $_GET['pr_model'];
    $productListQuery = tep_db_query("select p.products_id, pd.products_name FROM products p, products_description pd WHERE p.products_id = pd.products_id and p.products_model = '" .
            $pr_model . "'");
    while ($productList = tep_db_fetch_array($productListQuery)) {
        $prodListStr .= '' . $pr_model . '<br>' . $productList['products_name'] . '';
    }
    $prodListStr = $prodListStr;
    echo $prodListStr;
    exit();
}
//parent-child 25Feb2014 (MA) EOF
// begin bundled products
function bundle_avoid($bundle_id) { // returns an array of bundle_ids containing the specified bundle
    $avoid_list = array();
    $check_query = tep_db_query('select bundle_id from ' . TABLE_PRODUCTS_BUNDLES .
            ' where subproduct_id = ' . (int) $bundle_id);
    while ($check = tep_db_fetch_array($check_query)) {
        $avoid_list[] = $check['bundle_id'];
        $tmp = bundle_avoid($check['bundle_id']);
        $avoid_list = array_merge($avoid_list, $tmp);
    }
    return $avoid_list;
}
if (isset($_GET['action']) && $_GET['action'] == 'getProduct') {
    $category_array[] = $_GET['cid'];
    //$parent_id = $_GET['parent_id'];
    $get_sub_cat_id_query = tep_db_query("SELECT `categories_id` FROM `categories` WHERE `parent_id` = '" .
            $_GET['cid'] . "'");
    if (tep_db_num_rows($get_sub_cat_id_query)) {
        while ($get_sub_cat_id = tep_db_fetch_array($get_sub_cat_id_query)) {
            if (!in_array($get_sub_cat_id['categories_id'], $category_array)) {
                $category_array[] = $get_sub_cat_id['categories_id'];
            }
        }
    }
    $cat_str = implode(',', $category_array);
    $return_str .= TEXT_ADD_PRODUCT .
            '<br><select style="width:500px;" name="subproduct_selector" onChange="fillCodes()">';
    $return_str .= '<option name="null" value="" SELECTED></option>';
    $where_str = '';
    if (isset($HTTP_GET_VARS['pID']) && !empty($HTTP_GET_VARS['pID'])) {
        $bundle_check = bundle_avoid($HTTP_GET_VARS['pID']);
        if (!empty($bundle_check)) {
            $where_str = ' and (not (p.products_id in (' . implode(',', $bundle_check) .
                    ')))';
        }
    }
    $products = tep_db_query("select pd.products_name, p.products_id, p.products_model from " .
            TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .
            " pd, products_to_categories p2c where p.products_id = p2c.products_id and p2c.categories_id in (" .
            $cat_str . ") and pd.products_id = p.products_id and pd.language_id = '" . (int)
            $languages_id . "' and p.products_id <> " . (int) $HTTP_GET_VARS['pID'] . $where_str .
            " order by p.products_model");
    while ($products_values = tep_db_fetch_array($products)) {
        $return_str .= "\n" . '<option name="' . $products_values['products_id'] .
                '" value="' . $products_values['products_id'] . '">' . $products_values['products_name'] .
                " (" . $products_values['products_model'] . ')</option>';
    }
    $return_str .= '</select>';
    /* $get_products_query = tep_db_query("SELECT p.products_id, pd.products_name FROM products p, products_description pd, products_to_categories p2c WHERE p.products_model not in (SELECT `parent_products_model` FROM `products` group by `parent_products_model`) and (parent_products_model = '' or parent_products_model is null) and p.products_id = p2c.products_id and p.products_id = pd.products_id  and `is_package` = '0' and p2c.categories_id in (".$cat_str.") ".(!empty($parent_id)?' and p.products_id != '.$parent_id.' ':'')." group by p.products_id order by pd.products_name");
      while($get_products = tep_db_fetch_array($get_products_query) ){
      $option_text .= '<option value="'.$get_products['products_id'].'">'.$get_products['products_name'].'</option>';
      }
      $return_str = '<select id="selected_products" multiple style="width:" size="10" name="package_products[]">'.$option_text.'</select>';
     */
    echo $return_str;
    exit();
}
// end bundled products
if ($_POST['action'] == 'setAvalaraTaxCode') {
	
	$is_category = $_POST['is_category'];
	$osc_object_id = $_POST['osc_object_id'];
	$tax_code = $_POST['tax_code'];
	
	if(!empty($tax_code)){ // check if valid tax code or not
		if(tep_db_num_rows(tep_db_query("select 1 from avatax_code_list where tax_code = '".$tax_code."'")) == 0){
			die(json_encode(array("error" => true)));
		}
	}
	
	
	if ($is_category == 'true') {
        tep_db_query("update categories set avalara_tax_code ='" . $tax_code ."' where categories_id='" . (int) $osc_object_id . "'");
        tep_db_query("update products p, products_to_categories p2c set p.avalara_tax_code ='" . $tax_code ."' where p.products_id=p2c.products_id and p2c.categories_id='" . (int) $osc_object_id . "'");
    } else {
        tep_db_query("update products set avalara_tax_code='" . $tax_code ."' where products_id='" . (int) $osc_object_id . "'");
    }
	
    die(json_encode(array("success" => 'mapped')));
}
if ($_POST['action'] == 'get_ebay_categories_html') {
    $parent_id = $_POST['parent_id'];
    $parent_level = $_POST['parent_level'];
    echo get_eBay_categories_html($parent_id, $parent_level);
    exit();
} elseif ($_POST['action'] == 'mapebaycategory') {
    $is_category = $_POST['is_category'];
    $osc_object_id = $_POST['osc_object_id'];
    $ebay_category_id = $_POST['ebay_category_id'];
    if ($is_category == 'true') {
        tep_db_query("update categories set ebay_category_id='" . (int) $ebay_category_id .
                "' where categories_id='" . (int) $osc_object_id . "'");
        tep_db_query("update products p, products_to_categories p2c set p.ebay_category_id='" . (int) $ebay_category_id . "' where p.products_id=p2c.products_id and p2c.categories_id='" . (int) $osc_object_id . "'");
    } else {
        tep_db_query("update products set ebay_category_id='" . (int) $ebay_category_id .
                "' where products_id='" . (int) $osc_object_id . "'");
    }
    echo 'mapped';
    exit();
} elseif ($_POST['action'] == 'get_google_categories_html') {
    $parent_id = $_POST['parent_id'];
    $parent_level = $_POST['parent_level'];
    echo get_google_categories_html($parent_id, $parent_level);
    exit();
} elseif ($_POST['action'] == 'mapgooglecategory') {
    $is_category = $_POST['is_category'];
    $osc_object_id = $_POST['osc_object_id'];
    $google_category_id = $_POST['google_category_id'];
    $google_category_path = get_google_category_path($google_category_id);
    if ($is_category == 'true') {
        tep_db_query("update categories set google_category_id='" . (int) $google_category_id .
                "' where categories_id='" . (int) $osc_object_id . "'");
    } else {
        tep_db_query("update products set google_category_id='" . (int) $google_category_id .
                "', google_category_path='" . tep_db_prepare_input($google_category_path) . "' where products_id='" . (int) $osc_object_id . "'");
    }
    echo 'mapped';
    exit();
}  elseif ($_POST['action'] == 'get_amazon_categories_html') {
    $parent_id = $_POST['parent_id'];
    $parent_level = $_POST['parent_level'];
    echo get_amazon_categories_html($parent_id, $parent_level);
    exit();
} elseif ($_POST['action'] == 'mapamazoncategory') {
    $is_category = $_POST['is_category'];
    $osc_object_id = $_POST['osc_object_id'];
    $amazon_category_id = $_POST['amazon_category_id'];
    if ($is_category == 'true') {
  tep_db_query("update categories set amazon_category_id='" . (int) $amazon_category_id ."' where categories_id='" . (int) $osc_object_id . "'");
    } else {
  tep_db_query("update products set amazon_category_id='" . (int) $amazon_category_id . "' where products_id='" . (int) $osc_object_id . "'");
    }
    
 $cat_query = tep_db_query("select item_type from amazon_tree_guide where id='" . $amazon_category_id . "'");
 $result = tep_db_fetch_array($cat_query);
 echo $result['item_type'];
    exit();
}
//EOF:ebay_integration
//BOF:range_manager
if ($_POST['action'] == 'get_lanes') {
    $range_id = $_POST['range_id'];
    echo get_shooting_lanes_by_range_id_html($range_id);
    exit();
}
//EOF:range_manager
require (DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();
if (isset($_GET['action']) && $_GET['action'] == 'getSelectedProduct') {
    $_GET['pid'] = trim($_GET['pid']);
    $pid_array = explode(',', $_GET['pid']);
    $prodListStr = '';
    if (is_array($pid_array) && !empty($pid_array) && !empty($_GET['pid'])) {
        /* if(!empty($_GET['parent_id'])){
          $parent_id = $_GET['parent_id'];
          $parent_model = $_GET['parent_model'];
          foreach($pid_array as $val){
          tep_db_query("update products set parent_products_model = '".$parent_model."' where products_id = '".$val."'");
          }
          } */
        // get parent options if exists #start
        $parent_options = array();
        $cond = "";
		// added on 05-05-2016 #start
		$variation_theme_id = tep_db_fetch_array(tep_db_query("select variation_theme_id from products where products_id = '" . $_GET['parent_id'] . "'"));
        $get_parent_attributes = tep_db_query("select options_id from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . $_GET['parent_id'] . "'");
		
		
		while ($res_options = tep_db_fetch_array($get_parent_attributes)) {
            $parent_options[] = $res_options['options_id'];
        }
        if (count($parent_options) > 0) {
            $cond = " AND patrib.options_id NOT IN (" . implode(",", $parent_options) . ")";
        }
        // get parent options if exists #ends 
        $productListQuery = tep_db_query("select p.products_id, pd.products_name, p.products_model, p.variation_theme_id FROM products p, products_description pd WHERE p.products_id = pd.products_id and p.products_id in (" .
                $_GET['pid'] . ")");
				
				
		
        while ($productList = tep_db_fetch_array($productListQuery)) {
            /* get child attributes #start  */
            $key = $productList['products_id'];
            $display_child_products_attribute = '';
            $products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . $key . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int) $languages_id . "' and popt.is_xml_feed_option='0'  $cond");
            $products_attributes = tep_db_fetch_array($products_attributes_query);
            if ($products_attributes['total'] > 0) {
                $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . $key . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int) $languages_id . "' and popt.is_xml_feed_option='0'  $cond order by patrib.products_options_sort_order");
                while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
                    $products_options_array = array();
                    $products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . $key . "' and pa.options_id = '" . (int) $products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int) $languages_id . "' order by pa.products_options_sort_order");
                    while ($products_options = tep_db_fetch_array($products_options_query)) {
                        $products_options_array[] = array(
                            'id' => $products_options['products_options_values_id'],
                            'text' => $products_options['products_options_values_name']);
                        if ($products_options['options_values_price'] != '0') {
                            $products_options_array[sizeof($products_options_array) - 1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . ') ';
                        }
                    }
                    $display_child_products_attribute .= '<strong>' . $products_options_name['products_options_name'] . '</strong>:' . $products_options_array[0]['text'] . '<br/>';
                }
            }
            /* get child attributes #ends  */
			$manage_amazon_variation = '';
			if($variation_theme_id['variation_theme_id'] > 0){
				
				$manage_amazon_variation = '&nbsp; <span onClick="manageAmazonVariations(' . $productList['products_id'] . ', '.$variation_theme_id['variation_theme_id'].');" style="cursor:pointer;color:#0033FF;"><strong> [Manage Amazon Variations] </strong></span>';
			}
            $prodListStr .= '<tr id="child-' . $productList['products_id'] . '"><td>' . $productList['products_name'] . '<span onClick="manageChildAttributes(' . $productList['products_id'] . ');" style="cursor:pointer;color:#0033FF;"><strong> [Manage Attribute] </strong></span>'. $manage_amazon_variation. '<br>' . $display_child_products_attribute . '</td></tr><tr class="dataTableRow"><td class="dataTableContent">&nbsp;</td></tr>';
        }
        $prodListStr = '<table width="80%" align="center" cellspacing="0" cellpadding="2" border="0"><tr class="dataTableHeadingRow"><td class="">Child Products Name</td></tr>' .
                $prodListStr . '</table>';
    }
    echo $prodListStr;
    exit();
}
$action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');
if (isset($_POST['Update_without_preview']) || isset($_POST['Update_without_preview_x']) ||
        isset($_POST['Update_without_preview_y'])) {
    $action = 'update_product';
    $pID = $HTTP_GET_VARS['Update_pID'];
}
if (isset($_POST['update']))
    $action_update = $_POST['update'];
// Ultimate SEO URLs v2.1
// If the action will affect the cache entries
if (preg_match("/(insert|update|setflag)/i", $action))
    include_once ('includes/reset_seo_cache.php');
function get_products_price($markup, $base_price) {
    if (strpos($markup, '-')) {
        $operator = '-';
    } else {
        $operator = '+';
    }
    $markup = str_replace("-", "", $markup);
    if (strpos($markup, '%')) {
        $markup = str_replace("%", "", $markup);
        $markup_price = $base_price * ((float) $markup / 100);
    } else {
        $markup_price = (float) $markup;
    }
    if ($operator == '+') {
        $products_price = $base_price + $markup_price;
    } else {
        $products_price = $base_price - $markup_price;
    }
    return round($products_price, 4);
}
$roundoff_flag = ROUNDOFF_FLAG;
if (tep_not_null($action)) {
    switch ($action) {
        //BOF: bulk_category_movement
        case 'move_bulk_to_category':
            $desired_category_id = tep_db_prepare_input($HTTP_POST_VARS['move_selection_to_category_id']);
            $prod = $_POST['prod'];
            $cat = $_POST['cat'];
            if (isset($HTTP_POST_VARS['category_flag'])) {
                $category_flag = $HTTP_POST_VARS['category_flag'];
            } else {
                $category_flag = '1';
            }
            if (!empty($cat)) {
                for ($i = 0; $i < count($cat); $i++) {
                    tep_db_query("update " . TABLE_CATEGORIES . " set parent_id = '" . (int) $desired_category_id .
                            "', last_modified = now() where categories_id = '" . (int) $cat[$i] . "'");
                    if ((int) $desired_category_id > 0) {
                        tep_db_query("update " . TABLE_CATEGORIES .
                                " set is_category_group = '0' where categories_id = '" . (int) $cat[$i] . "'");
                    }
                }
            }
            if (!empty($prod)) {
                for ($i = 0; $i < count($prod); $i++) {
                    $duplicate_check_query = tep_db_query("select count(*) as total from " .
                            TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int) $prod[$i] .
                            "' and categories_id = '" . (int) $desired_category_id . "'");
                    $duplicate_check = tep_db_fetch_array($duplicate_check_query);
                    if ($duplicate_check['total'] < 1)
                        tep_db_query("update " . TABLE_PRODUCTS_TO_CATEGORIES . " set categories_id = '" .
                                (int) $desired_category_id . "' where products_id = '" . (int) $prod[$i] .
                                "' and categories_id = '" . (int) $current_category_id . "'");
                    if ($category_flag == '0') {
                        tep_db_query("update products_xml_feed_flags set flags=concat(substr(flags,1,2),'0',substr(flags,4,2)), last_modified=now() where products_id='" .
                                (int) $prod[$i] . "'");
                    } else {
                        tep_db_query("update products_xml_feed_flags set flags=concat(substr(flags,1,2),'1',substr(flags,4,2)), last_modified=now() where products_id='" .
                                (int) $prod[$i] . "'");
                    }
                }
            }
            tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $desired_category_id));
            break;
        //BOF: bulk_category_movement
        case 'setflag':
            if (($HTTP_GET_VARS['flag'] == '0') || ($HTTP_GET_VARS['flag'] == '1')) {
                if (isset($HTTP_GET_VARS['pID'])) {
                    tep_set_product_status($HTTP_GET_VARS['pID'], $HTTP_GET_VARS['flag']);
                }
                if (USE_CACHE == 'true') {
                    tep_reset_cache_block('categories');
                    tep_reset_cache_block('also_purchased');
                }
            }
            tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $HTTP_GET_VARS['cPath'] .
                            '&pID=' . $HTTP_GET_VARS['pID']));
            break;
        //Categroies Status MOD BEGIN by FIW
        case 'setflagc':
            if (($HTTP_GET_VARS['flag'] == '0') || ($HTTP_GET_VARS['flag'] == '1')) {
                if (isset($HTTP_GET_VARS['cID'])) {
                    tep_set_category_status($HTTP_GET_VARS['cID'], $HTTP_GET_VARS['flag']);
                }
                if (USE_CACHE == 'true') {
                    tep_reset_cache_block('categories');
                    tep_reset_cache_block('also_purchased');
                }
            }
            tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $HTTP_GET_VARS['cPath'] .
                            '&cID=' . $HTTP_GET_VARS['cID']));
            break;
        //Categories status MOD END by FIW
        //BOF:amazon_integration
        case 'setamazonflag':
            if (($HTTP_GET_VARS['amazonflag'] == '0') || ($HTTP_GET_VARS['amazonflag'] ==
                    '1')) {
                if (isset($HTTP_GET_VARS['pID'])) {
                    tep_set_amazon_product_status($HTTP_GET_VARS['pID'], $HTTP_GET_VARS['amazonflag']);
                }
                if (USE_CACHE == 'true') {
                    tep_reset_cache_block('categories');
                    tep_reset_cache_block('also_purchased');
                }
            }
            tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $HTTP_GET_VARS['cPath'] .
                            '&pID=' . $HTTP_GET_VARS['pID']));
            break;
        case 'setamazonflagc':
            if (($HTTP_GET_VARS['amazonflag'] == '0') || ($HTTP_GET_VARS['amazonflag'] ==
                    '1')) {
                if (isset($HTTP_GET_VARS['cID'])) {
                    tep_set_amazon_category_status($HTTP_GET_VARS['cID'], $HTTP_GET_VARS['amazonflag']);
                }
                if (USE_CACHE == 'true') {
                    tep_reset_cache_block('categories');
                    tep_reset_cache_block('also_purchased');
                }
            }
            tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $HTTP_GET_VARS['cPath'] .
                            '&cID=' . $HTTP_GET_VARS['cID']));
            break;
        //EOF:amazon_integration
        //BOF:ebay_integration
        case 'setebayflag':
            if (($HTTP_GET_VARS['ebayflag'] == '0') || ($HTTP_GET_VARS['ebayflag'] == '1')) {
                if (isset($HTTP_GET_VARS['pID'])) {
                    tep_set_ebay_product_status($HTTP_GET_VARS['pID'], $HTTP_GET_VARS['ebayflag']);
                }
                if (USE_CACHE == 'true') {
                    tep_reset_cache_block('categories');
                    tep_reset_cache_block('also_purchased');
                }
            }
            tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $HTTP_GET_VARS['cPath'] .
                            '&pID=' . $HTTP_GET_VARS['pID']));
            break;
        case 'setebayflagc':
            if (($HTTP_GET_VARS['ebayflag'] == '0') || ($HTTP_GET_VARS['ebayflag'] == '1')) {
                if (isset($HTTP_GET_VARS['cID'])) {
                    tep_set_ebay_category_status($HTTP_GET_VARS['cID'], $HTTP_GET_VARS['ebayflag']);
                }
                if (USE_CACHE == 'true') {
                    tep_reset_cache_block('categories');
                    tep_reset_cache_block('also_purchased');
                }
            }
            tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $HTTP_GET_VARS['cPath'] .
                            '&cID=' . $HTTP_GET_VARS['cID']));
            break;
        //EOF:ebay_integration
        case 'insert_category':
        case 'update_category':
            if (isset($HTTP_POST_VARS['categories_id']))
                $categories_id = tep_db_prepare_input($HTTP_POST_VARS['categories_id']);
            $sort_order = tep_db_prepare_input($HTTP_POST_VARS['sort_order']);
            //BOF:category_group
            $is_category_group = tep_db_prepare_input($HTTP_POST_VARS['is_category_group']);
            //EOF:category_group
            //Category Status MOD BEGIN by FIW
            $categories_status = tep_db_prepare_input($HTTP_POST_VARS['categories_status']);
            $sql_data_array = array(
                'sort_order' => empty($sort_order) ? '0' : $sort_order,
                //BOF:category_group
                //'categories_status' => $categories_status);
                'categories_status' => $categories_status == '0' || $categories_status == '1' ? $categories_status : '1',
                'is_category_group' => $is_category_group);
            //EOF:category_group
            //Category Status MOD END by FIW
            if ($action == 'insert_category') {
                $sql_data_array['markup'] = '0';
                $sql_data_array['markup_modified'] = 'now()';
                $sql_data_array['ebay_category_id'] = '0';
                $insert_sql_data = array('parent_id' => $current_category_id, 'date_added' =>
                    'now()');
                $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
                tep_db_perform(TABLE_CATEGORIES, $sql_data_array);
                $categories_id = tep_db_insert_id();
            } elseif ($action == 'update_category') {
                $update_sql_data = array('last_modified' => 'now()');
                $sql_data_array = array_merge($sql_data_array, $update_sql_data);
                tep_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', "categories_id = '" .
                        (int) $categories_id . "'");
                //Category Status MOD BEGIN by FIW
                if (isset($categories_status)) {
                    $categories = tep_get_category_tree($categories_id, '', '0', '', true);
                    for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
                        $update_status = tep_db_query("update `categories` set `categories_status` = '" .
                                (int) $categories_status . "' where `categories_id` = '" . (int) $categories[$i]['id'] .
                                "' ");
                    }
                }
                //Category Status MOD END by FIW
            }
            $languages = tep_get_languages();
            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                $categories_name_array = $HTTP_POST_VARS['categories_name'];
                //HTC BOC
                $categories_htc_title_array = $HTTP_POST_VARS['categories_htc_title_tag'];
                $categories_htc_desc_array = $HTTP_POST_VARS['categories_htc_desc_tag'];
                $categories_htc_keywords_array = $HTTP_POST_VARS['categories_htc_keywords_tag'];
                $categories_htc_description_array = $HTTP_POST_VARS['categories_htc_description'];
                //HTC EOC
                $language_id = $languages[$i]['id'];
                //HTC BOC
                $sql_data_array = array(
                    'categories_name' => tep_db_prepare_input($categories_name_array[$language_id]),
                    'categories_htc_title_tag' => (tep_not_null($categories_htc_title_array[$language_id]) ?
                            tep_db_prepare_input($categories_htc_title_array[$language_id]) :
                            tep_db_prepare_input($categories_name_array[$language_id])),
                    'categories_htc_desc_tag' => (tep_not_null($categories_htc_desc_array[$language_id]) ?
                            tep_db_prepare_input($categories_htc_desc_array[$language_id]) :
                            tep_db_prepare_input($categories_name_array[$language_id])),
                    'categories_htc_keywords_tag' => (tep_not_null($categories_htc_keywords_array[$language_id]) ?
                            tep_db_prepare_input($categories_htc_keywords_array[$language_id]) :
                            tep_db_prepare_input($categories_name_array[$language_id])),
                    'categories_htc_description' => tep_db_prepare_input($categories_htc_description_array[$language_id]));
                //HTC EOC
                if ($action == 'insert_category') {
                    $insert_sql_data = array('categories_id' => $categories_id, 'language_id' => $languages[$i]['id']);
                    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
                    tep_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
                } elseif ($action == 'update_category') {
                    tep_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', "categories_id = '" . (int) $categories_id . "' and language_id = '" . (int) $languages[$i]['id'] .
                            "'");
                }
            }
            if ($categories_image = new upload('categories_image', DIR_FS_CATALOG_IMAGES)) {
                tep_db_query("update " . TABLE_CATEGORIES . " set categories_image = '" .
                        tep_db_input($categories_image->filename) . "' where categories_id = '" . (int)
                        $categories_id . "'");
            }
            if ($banner_image = new upload('banner_image', DIR_FS_CATALOG_IMAGES)) {
                tep_db_query("update " . TABLE_CATEGORIES . " set banner_image = '" .
                        tep_db_input($banner_image->filename) . "' where categories_id = '" . (int) $categories_id .
                        "'");
            }
            if (USE_CACHE == 'true') {
                tep_reset_cache_block('categories');
                tep_reset_cache_block('also_purchased');
            }
            tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $categories_id));
            break;
        case 'delete_category_confirm':
            if (isset($HTTP_POST_VARS['categories_id'])) {
                $categories_id = tep_db_prepare_input($HTTP_POST_VARS['categories_id']);
                $categories = tep_get_category_tree($categories_id, '', '0', '', true);
                $products = array();
                $products_delete = array();
                for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
                    $product_ids_query = tep_db_query("select products_id from " .
                            TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . (int) $categories[$i]['id'] .
                            "'");
                    while ($product_ids = tep_db_fetch_array($product_ids_query)) {
                        $products[$product_ids['products_id']]['categories'][] = $categories[$i]['id'];
                    }
                }
                reset($products);
                while (list($key, $value) = each($products)) {
                    $category_ids = '';
                    for ($i = 0, $n = sizeof($value['categories']); $i < $n; $i++) {
                        $category_ids .= "'" . (int) $value['categories'][$i] . "', ";
                    }
                    $category_ids = substr($category_ids, 0, -2);
                    $check_query = tep_db_query("select count(*) as total from " .
                            TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int) $key .
                            "' and categories_id not in (" . $category_ids . ")");
                    $check = tep_db_fetch_array($check_query);
                    if ($check['total'] < '1') {
                        $products_delete[$key] = $key;
                    }
                }
                // removing categories can be a lengthy process
                tep_set_time_limit(0);
                for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
                    tep_remove_category($categories[$i]['id']);
                }
                reset($products_delete);
                while (list($key) = each($products_delete)) {
                    tep_remove_product($key);
                }
            }
            if (USE_CACHE == 'true') {
                tep_reset_cache_block('categories');
                tep_reset_cache_block('also_purchased');
            }
            tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath));
            break;
        case 'delete_product_confirm':
            if (isset($HTTP_POST_VARS['products_id']) && isset($HTTP_POST_VARS['product_categories']) &&
                    is_array($HTTP_POST_VARS['product_categories'])) {
                $product_id = tep_db_prepare_input($HTTP_POST_VARS['products_id']);
                $product_categories = $HTTP_POST_VARS['product_categories'];
                for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
                    tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES .
                            " where products_id = '" . (int) $product_id . "' and categories_id = '" . (int)
                            $product_categories[$i] . "'");
                }
                // BOF Separate Pricing Per Customer
                tep_db_query("delete from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" .
                        tep_db_input($product_id) . "' ");
                // EOF Separate Pricing Per Customer
                //bof AMAZON INTEGRATION product extended 17 dec 2013 start
                tep_db_query("delete from " . TABLE_PRODUCTS_EXTENDED .
                        " where osc_products_id = '" . tep_db_input($product_id) . "' ");
                //eof AMAZON INTEGRATION product extended 17 dec 2013 end
                $product_categories_query = tep_db_query("select count(*) as total from " .
                        TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int) $product_id . "'");
                $product_categories = tep_db_fetch_array($product_categories_query);
                if ($product_categories['total'] == '0') {
                    tep_remove_product($product_id);
                }
            }
            if (USE_CACHE == 'true') {
                tep_reset_cache_block('categories');
                tep_reset_cache_block('also_purchased');
            }
            tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath));
            break;
        case 'move_category_confirm':
            if (isset($HTTP_POST_VARS['categories_id']) && ($HTTP_POST_VARS['categories_id'] !=
                    $HTTP_POST_VARS['move_to_category_id'])) {
                $categories_id = tep_db_prepare_input($HTTP_POST_VARS['categories_id']);
                $new_parent_id = tep_db_prepare_input($HTTP_POST_VARS['move_to_category_id']);
                $path = explode('_', tep_get_generated_category_path_ids($new_parent_id));
                if (in_array($categories_id, $path)) {
                    $messageStack->add_session(ERROR_CANNOT_MOVE_CATEGORY_TO_PARENT, 'error');
                    tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $categories_id));
                } else {
                    tep_db_query("update " . TABLE_CATEGORIES . " set parent_id = '" . (int) $new_parent_id .
                            "', last_modified = now() where categories_id = '" . (int) $categories_id . "'");
                    //BOF:category_group
                    if ((int) $new_parent_id > 0) {
                        tep_db_query("update " . TABLE_CATEGORIES .
                                " set is_category_group = '0' where categories_id = '" . (int) $categories_id .
                                "'");
                    }
                    //EOF:category_group
                    if (USE_CACHE == 'true') {
                        tep_reset_cache_block('categories');
                        tep_reset_cache_block('also_purchased');
                    }
                    tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $new_parent_id .
                                    '&cID=' . $categories_id));
                }
            }
            break;
        case 'move_product_confirm':
            $products_id = tep_db_prepare_input($HTTP_POST_VARS['products_id']);
            $new_parent_id = tep_db_prepare_input($HTTP_POST_VARS['move_to_category_id']);
            $duplicate_check_query = tep_db_query("select count(*) as total from " .
                    TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int) $products_id .
                    "' and categories_id = '" . (int) $new_parent_id . "'");
            $duplicate_check = tep_db_fetch_array($duplicate_check_query);
            if ($duplicate_check['total'] < 1)
                tep_db_query("update " . TABLE_PRODUCTS_TO_CATEGORIES . " set categories_id = '" .
                        (int) $new_parent_id . "' where products_id = '" . (int) $products_id .
                        "' and categories_id = '" . (int) $current_category_id . "'");
            if (USE_CACHE == 'true') {
                tep_reset_cache_block('categories');
                tep_reset_cache_block('also_purchased');
            }
            tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $new_parent_id .
                            '&pID=' . $products_id));
            break;
        case 'insert_product':
        case 'update_product':
            if (isset($HTTP_POST_VARS['edit_x']) || isset($HTTP_POST_VARS['edit_y'])) {
                $action = 'new_product';
            } else {
                $manual_price_set = 0;
                if (isset($HTTP_GET_VARS['pID']))
                    $products_id = tep_db_prepare_input($HTTP_GET_VARS['pID']);
                $products_date_available = tep_db_prepare_input($HTTP_POST_VARS['products_date_available']);
                if (isset($HTTP_POST_VARS['manual_price']) && $HTTP_POST_VARS['manual_price'] >
                        0 && $HTTP_POST_VARS['manual_price'] != '') {
                    $products_price = $HTTP_POST_VARS['manual_price'];
                    $manual_price_set = 1;
                } else {
                    if ($action == 'insert_product') {
                        $value = DEFAULT_MARKUP;
                        $markup_value = $value;
                        //$products_price = 	$HTTP_POST_VARS['base_price'] * (1 + ($value / 100));
                        $products_price = get_products_price($markup_value, $HTTP_POST_VARS['base_price']);
                    } else {
                        $check_query = tep_db_query("select markup, roundoff_flag from products where products_id = '" .
                                (int) $products_id . "'");
                        $check = tep_db_fetch_array($check_query);
                        $markup = $check['markup'];
                        $roundoff_flag = $check['roundoff_flag'];
                        $markup_value = $markup;
                        $products_price = get_products_price($markup_value, $HTTP_POST_VARS['base_price']);
                        /* if(strpos( $markup, '-')){
                          $operator = '-';
                          } else {
                          $operator = '+';
                          }
                          $markup = str_replace("-","",$markup);
                          if(strpos( $markup, '%')){
                          $markup = str_replace("%","",$markup);
                          $markup_price = $HTTP_POST_VARS['base_price'] * ((float)$markup/100);
                          }else{
                          $markup_price = (float)$markup;
                          }
                          if ($operator == '+') {
                          $products_price = $HTTP_POST_VARS['base_price'] + $markup_price;
                          } else {
                          $products_price = $HTTP_POST_VARS['base_price'] - $markup_price;
                          }
                          $products_price = round($products_price,4); */
                    }
                }
                $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available :
                        'null';
                $sql_data_array = array(
                    'products_quantity' => (int) tep_db_prepare_input($HTTP_POST_VARS['products_quantity']),
                    'products_bundle' => ($HTTP_POST_VARS['products_bundle'] == 'yes' ? 'yes' : 'no'),
                    'sold_in_bundle_only' => ($HTTP_POST_VARS['sold_in_bundle_only'] == 'yes' ?
                            'yes' : 'no'),
                    'store_quantity' => tep_db_prepare_input($HTTP_POST_VARS['store_quantity']),
                    'products_model' => tep_db_prepare_input($HTTP_POST_VARS['products_model']),
//MVS start
                    //'vendors_prod_id' => tep_db_prepare_input($HTTP_POST_VARS['vendors_prod_id']),
                    //'vendors_product_price' => tep_db_prepare_input($HTTP_POST_VARS['vendors_product_price']),
                    //'vendors_id' => tep_db_prepare_input($HTTP_POST_VARS['vendors_id']),
                    //'vendors_prod_comments' => tep_db_prepare_input($HTTP_POST_VARS['vendors_prod_comments']),
//MVS end
                    'products_size' => tep_db_prepare_input($HTTP_POST_VARS['products_size']),
                    'disclaimer_needed' => tep_db_prepare_input($HTTP_POST_VARS['disclaimer_needed']),
                    //'products_weight' => tep_db_prepare_input($HTTP_POST_VARS['products_weight']),
                    //'products_height' => tep_db_prepare_input($HTTP_POST_VARS['products_height']),
                    //'products_length' => tep_db_prepare_input($HTTP_POST_VARS['products_length']),
                    //'products_width' => tep_db_prepare_input($HTTP_POST_VARS['products_width']),
                    //'products_ready_to_ship' => tep_db_prepare_input($HTTP_POST_VARS['products_ready_to_ship']),
                    'base_price' => tep_db_prepare_input($HTTP_POST_VARS['base_price']),
                    //'manual_price' => tep_db_prepare_input($HTTP_POST_VARS['manual_price']),
                    'products_date_available' => $products_date_available,
                    //'products_weight' => tep_db_prepare_input($HTTP_POST_VARS['products_weight']),
                    'markup' => $markup_value,
                    'products_status' => tep_db_prepare_input($HTTP_POST_VARS['products_status']),
                    'products_tax_class_id' => tep_db_prepare_input($HTTP_POST_VARS['products_tax_class_id']),
                    'manufacturers_id' => (int) tep_db_prepare_input($HTTP_POST_VARS['manufacturers_id']),
                    'free_shipping' => tep_db_prepare_input($HTTP_POST_VARS['free_shipping']),
                    'lock_title' => tep_db_prepare_input($HTTP_POST_VARS['lock_title_flag']),
                    'lock_status' => tep_db_prepare_input($HTTP_POST_VARS['lock_status_flag']),
                    'lock_status' => tep_db_prepare_input($HTTP_POST_VARS['lock_status_flag']),
                    'lock_status' => tep_db_prepare_input($HTTP_POST_VARS['lock_status_flag']),
                    'lock_specs' => (int) tep_db_prepare_input($HTTP_POST_VARS['lock_specs_flag']),
                    'is_store_item' => tep_db_prepare_input($HTTP_POST_VARS['store_item_flag']),
                    'in_store_pickup' => tep_db_prepare_input($HTTP_POST_VARS['in_store_pickup']),
                    'lock_price' => tep_db_prepare_input($HTTP_POST_VARS['lock_price']),
                    'hide_price' => (int) tep_db_prepare_input($HTTP_POST_VARS['hide_price']),
                    //BOF:range_manager
                    'is_lane_item' => tep_db_prepare_input(($HTTP_POST_VARS['is_lane_item'] ? '1' :
                                    '0')),
                    'range_id' => (int) tep_db_prepare_input($HTTP_POST_VARS['range_id']),
                    'lane_id' => (int) tep_db_prepare_input($HTTP_POST_VARS['lane_id']),
                    //EOF:range_manager
                    // BOF SKU RANGE MANAGER IS_FULL DAY PRICE 26-DEC-2013
                    'is_fullday_price' => tep_db_prepare_input(($HTTP_POST_VARS['is_fullday_price'] ?
                                    '1' : '0'))
                        // EOF SKU RANGE MANAGER IS_FULL DAY PRICE 26-DEC-2013
                );
                if (!empty($HTTP_POST_VARS['parent_products_model'])) {
                    $sql_data_array['parent_products_model'] = tep_db_prepare_input($HTTP_POST_VARS['parent_products_model']);
                } else {
                    //added on 27-10-2015 to insert NULL value to parent_products_model column
                    $sql_data_array['parent_products_model'] = NULL;
                }
                if (isset($HTTP_POST_VARS['products_weight']) && tep_not_null($HTTP_POST_VARS['products_weight'])) {
                    $sql_data_array['products_weight'] = tep_db_prepare_input($HTTP_POST_VARS['products_weight']);
                }
                if (isset($HTTP_POST_VARS['products_height']) && tep_not_null($HTTP_POST_VARS['products_height'])) {
                    $sql_data_array['products_height'] = tep_db_prepare_input($HTTP_POST_VARS['products_height']);
                }
                if (isset($HTTP_POST_VARS['products_length']) && tep_not_null($HTTP_POST_VARS['products_length'])) {
                    $sql_data_array['products_length'] = tep_db_prepare_input($HTTP_POST_VARS['products_length']);
                }
                if (isset($HTTP_POST_VARS['products_width']) && tep_not_null($HTTP_POST_VARS['products_width'])) {
                    $sql_data_array['products_width'] = tep_db_prepare_input($HTTP_POST_VARS['products_width']);
                }
                if (isset($HTTP_POST_VARS['products_ready_to_ship']) && tep_not_null($HTTP_POST_VARS['products_ready_to_ship'])) {
                    $sql_data_array['products_ready_to_ship'] = tep_db_prepare_input($HTTP_POST_VARS['products_ready_to_ship']);
                }
                if (isset($HTTP_POST_VARS['manual_price']) && tep_not_null($HTTP_POST_VARS['manual_price'])) {
                    $sql_data_array['manual_price'] = tep_db_prepare_input($HTTP_POST_VARS['manual_price']);
                }
                if (isset($HTTP_POST_VARS['products_image']) && tep_not_null($HTTP_POST_VARS['products_image']) &&
                        ($HTTP_POST_VARS['products_image'] != 'none')) {
                    $sql_data_array['products_image'] = tep_db_prepare_input($HTTP_POST_VARS['products_image']);
                }
                if (isset($HTTP_POST_VARS['products_mediumimage']) && tep_not_null($HTTP_POST_VARS['products_mediumimage']) &&
                        ($HTTP_POST_VARS['products_mediumimage'] != 'none')) {
                    $sql_data_array['products_mediumimage'] = tep_db_prepare_input($HTTP_POST_VARS['products_mediumimage']);
                }
                if (isset($HTTP_POST_VARS['products_largeimage']) && tep_not_null($HTTP_POST_VARS['products_largeimage']) &&
                        ($HTTP_POST_VARS['products_largeimage'] != 'none')) {
                    $sql_data_array['products_largeimage'] = tep_db_prepare_input($HTTP_POST_VARS['products_largeimage']);
                }
                if (isset($HTTP_POST_VARS['products_image_2']) && tep_not_null($HTTP_POST_VARS['products_image_2']) &&
                        ($HTTP_POST_VARS['products_image_2'] != 'none')) {
                    $sql_data_array['product_image_2'] = tep_db_prepare_input($HTTP_POST_VARS['products_image_2']);
                }
                if (isset($HTTP_POST_VARS['products_image_3']) && tep_not_null($HTTP_POST_VARS['products_image_3']) &&
                        ($HTTP_POST_VARS['products_image_3'] != 'none')) {
                    $sql_data_array['product_image_3'] = tep_db_prepare_input($HTTP_POST_VARS['products_image_3']);
                }
                if (isset($HTTP_POST_VARS['products_image_4']) && tep_not_null($HTTP_POST_VARS['products_image_4']) &&
                        ($HTTP_POST_VARS['products_image_4'] != 'none')) {
                    $sql_data_array['product_image_4'] = tep_db_prepare_input($HTTP_POST_VARS['products_image_4']);
                }
                if (isset($HTTP_POST_VARS['products_image_5']) && tep_not_null($HTTP_POST_VARS['products_image_5']) &&
                        ($HTTP_POST_VARS['products_image_5'] != 'none')) {
                    $sql_data_array['product_image_5'] = tep_db_prepare_input($HTTP_POST_VARS['products_image_5']);
                }
                if (isset($HTTP_POST_VARS['products_image_6']) && tep_not_null($HTTP_POST_VARS['products_image_6']) &&
                        ($HTTP_POST_VARS['products_image_6'] != 'none')) {
                    $sql_data_array['product_image_6'] = tep_db_prepare_input($HTTP_POST_VARS['products_image_6']);
                }
                if ($action == 'insert_product') {
                    //BOF 10 JAN 2014 RANGE MANAGER
                    if ($HTTP_POST_VARS['range_id'] && $HTTP_POST_VARS['range_id'] > 0) {
                        $lanesQry = tep_db_query("SELECT `lanes_id`,`lanes_name` FROM `" . TABLE_LANES .
                                "` WHERE `ranges_id` = '" . tep_db_prepare_input($HTTP_POST_VARS['range_id']) .
                                "'");
                        while ($lanesArr = tep_db_fetch_array($lanesQry)) {
                            $sql_data_array['lane_id'] = $lanesArr['lanes_id'];
                            $sql_data_array['products_model'] = 'R' . $HTTP_POST_VARS['range_id'] . 'L' . $lanesArr['lanes_id'];
                            $insert_sql_data = array(
                                'products_date_added' => 'now()',
                                'roundoff_flag' => ROUNDOFF_FLAG,
                                'products_price' => (ROUNDOFF_FLAG && !$manual_price_set ? apply_roundoff($products_price) :
                                        $products_price));
                            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
                            tep_db_perform(TABLE_PRODUCTS, $sql_data_array);
                            $products_id = tep_db_insert_id();
                            $products_id_arr[] = $products_id;
                            $lanesArrProid[$products_id] = $lanesArr['lanes_name'];
                            tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES .
                                    " (products_id, categories_id) values ('" . (int) $products_id . "', '" . (int) $current_category_id .
                                    "')");
                            tep_db_query("insert into " . TABLE_PRODUCTS_EXTENDED .
                                    " (`osc_products_id`,`upc_ean`,`brand_name`,`date_added`,`last_modified`) VALUES('" .
                                    (int) $products_id . "','" . tep_db_prepare_input($HTTP_POST_VARS['upc_ean']) .
                                    "','" . tep_db_prepare_input($HTTP_POST_VARS['brand_name']) . "',now(),now())");
                        }
                    } else {
                        //EOF 10 JAN 2014 RANGE MANAGER
                        $insert_sql_data = array(
                            'products_date_added' => 'now()',
                            'roundoff_flag' => ROUNDOFF_FLAG,
                            'products_price' => (ROUNDOFF_FLAG && !$manual_price_set ? apply_roundoff($products_price) :
                                    $products_price));
                        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
                        tep_db_perform(TABLE_PRODUCTS, $sql_data_array);
                        $products_id = tep_db_insert_id();
                        tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES .
                                " (products_id, categories_id) values ('" . (int) $products_id . "', '" . (int) $current_category_id .
                                "')");
                        //$flags = tep_db_prepare_input($HTTP_POST_VARS['prod_inventory_flag']) .
                        //			tep_db_prepare_input($HTTP_POST_VARS['prod_price_flag']) .
                        //			tep_db_prepare_input($HTTP_POST_VARS['prod_category_flag']);
                        //tep_db_query("insert into products_xml_feed_flags (products_id, flags, last_modified) values ('" . (int)$products_id . "', '" . $flags . "', now())");
                        //BOF AMAZON INTEGRATION PRODUCT EXTENDED 17 DEC 2013 START
                        tep_db_query("insert into " . TABLE_PRODUCTS_EXTENDED .
                                " (`osc_products_id`,`upc_ean`,`brand_name`,`date_added`,`last_modified`) VALUES('" .
                                (int) $products_id . "','" . tep_db_prepare_input($HTTP_POST_VARS['upc_ean']) .
                                "','" . tep_db_prepare_input($HTTP_POST_VARS['brand_name']) . "',now(),now())");
                        //EOF AMAZON INTEGRATION PRODUCT EXTENDED 17 DEC 2013 END
                        //BOF RANGE MANAGER 10-JAN-2014
                    }
                    //EOF RANGE MANAGER 10-JAN-2014
                } elseif ($action == 'update_product') {
                    $sql = tep_db_query("select roundoff_flag from " . TABLE_PRODUCTS .
                            " where products_id='" . (int) $products_id . "'");
                    $sql_info = tep_db_fetch_array($sql);
                    $roundoff_flag = $sql_info['roundoff_flag'];
                    $update_sql_data = array('products_last_modified' => 'now()', 'products_price' =>
                        ($roundoff_flag && !$manual_price_set ? apply_roundoff($products_price) : $products_price));
                    $sql_data_array = array_merge($sql_data_array, $update_sql_data);
                    //BOF RANGE MANAGER 10-JAN-2014
                    if ($HTTP_POST_VARS['range_id'] && $HTTP_POST_VARS['range_id'] > 0) {
                        $range_product_list = tep_db_query("SELECT `products_id` FROM `" .
                                TABLE_PRODUCTS . "` WHERE `range_id` = '" . $HTTP_POST_VARS['range_id'] . "'");
                        while ($range_product_id = tep_db_fetch_array($range_product_list)) {
                            $products_id_arr[] = $range_product_id['products_id'];
                            tep_db_query("UPDATE `" . TABLE_PRODUCTS . "` SET `products_price` = '" . $products_price .
                                    "' ,`base_price` = '" . tep_db_prepare_input($HTTP_POST_VARS['base_price']) .
                                    "' ,`markup` = '" . $markup_value . "' WHERE `products_id` = '" . $range_product_id['products_id'] .
                                    "' ");
                        }
                    } else {
                        //EOF RANGE MANAGER 10-JAN-2014
                        tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)
                                $products_id . "'");
                        //BOF RANGE MANAGER 10-JAN-2014
                    }
                    //EOF RANGE MANAGER 10-JAN-2014
                    // BOF Separate Pricing Per Customer
                    $customers_group_query = tep_db_query("select customers_group_id, customers_group_name from " .
                            TABLE_CUSTOMERS_GROUPS .
                            " where customers_group_id != '0' order by customers_group_id");
                    while ($customers_group = tep_db_fetch_array($customers_group_query)) {
                        // Gets all of the customers groups
                        $attributes_query = tep_db_query("select customers_group_id, customers_group_price from " .
                                TABLE_PRODUCTS_GROUPS . " where ((products_id = '" . $products_id .
                                "') && (customers_group_id = " . $customers_group['customers_group_id'] .
                                ")) order by customers_group_id");
                        $attributes = tep_db_fetch_array($attributes_query);
                        if (tep_db_num_rows($attributes_query) > 0) {
                            if ($_POST['sppcoption'][$customers_group['customers_group_id']]) { // this is checking if the check box is checked
                                if (($_POST['sppcprice'][$customers_group['customers_group_id']] <> $attributes['customers_group_price']) &&
                                        ($attributes['customers_group_id'] == $customers_group['customers_group_id'])) {
                                    tep_db_query("update " . TABLE_PRODUCTS_GROUPS .
                                            " set customers_group_price = '" . $_POST['sppcprice'][$customers_group['customers_group_id']] .
                                            "' where customers_group_id = '" . $attributes['customers_group_id'] .
                                            "' and products_id = '" . $products_id . "'");
                                    $attributes = tep_db_fetch_array($attributes_query);
                                } elseif (($_POST['sppcprice'][$customers_group['customers_group_id']] == $attributes['customers_group_price'])) {
                                    $attributes = tep_db_fetch_array($attributes_query);
                                }
                            } else {
                                tep_db_query("delete from " . TABLE_PRODUCTS_GROUPS .
                                        " where customers_group_id = '" . $customers_group['customers_group_id'] .
                                        "' and products_id = '" . $products_id . "'");
                                $attributes = tep_db_fetch_array($attributes_query);
                            }
                        } elseif (($_POST['sppcoption'][$customers_group['customers_group_id']]) && ($_POST['sppcprice'][$customers_group['customers_group_id']] !=
                                '')) {
                            tep_db_query("insert into " . TABLE_PRODUCTS_GROUPS .
                                    " (products_id, customers_group_id, customers_group_price) values ('" . $products_id .
                                    "', '" . $customers_group['customers_group_id'] . "', '" . $_POST['sppcprice'][$customers_group['customers_group_id']] .
                                    "')");
                            $attributes = tep_db_fetch_array($attributes_query);
                        }
                    }
                    // EOF Separate Pricing Per Customer
                    $flags = tep_db_prepare_input($HTTP_POST_VARS['prod_inventory_flag']) .
                            tep_db_prepare_input($HTTP_POST_VARS['prod_price_flag']) . tep_db_prepare_input($HTTP_POST_VARS['prod_category_flag']) .
                            tep_db_prepare_input($HTTP_POST_VARS['prod_desc_flag']) . tep_db_prepare_input($HTTP_POST_VARS['prod_image_flag']);
                    //if (!empty($flags)){
                    tep_db_query("update products_xml_feed_flags set flags='" . $flags .
                            "', last_modified=now() where products_id='" . (int) $products_id . "'");
                    //}else{
                    //	tep_db_query("insert into products_xml_feed_flags (products_id, flags, last_modified) values ('" . (int)$products_id . "', '" . $flags . "', now())");
                    //}
                }
                // added on 27-10-2015 
                if (empty($HTTP_POST_VARS['parent_products_model'])) {
                    tep_db_query("update products set parent_products_model = NULL where products_id='" . (int) $products_id . "'");
                }
                //parent-child 25Feb2014 (MA) BOF
                if (!empty($HTTP_POST_VARS['child_ids']) && !empty($HTTP_POST_VARS['products_model'])) {
                    $parent_model = tep_db_prepare_input($HTTP_POST_VARS['products_model']);
                    $child_ids = explode(',', $HTTP_POST_VARS['child_ids']);
                    if (is_array($child_ids) && !empty($child_ids)) {
                        foreach ($child_ids as $val) {
                            if (!empty($val)) {
                                tep_db_query("update products set parent_products_model = '" . (!empty($parent_model) ? $parent_model : NULL) .
                                        "' where products_id = '" . $val . "'");
                            }
                        }
                    }
                }
                //parent-child 25Feb2014 (MA) EOF
                // BOF Bundled Products
                if ($HTTP_POST_VARS['products_bundle'] == "yes") {
                    $to_avoid = bundle_avoid($products_id);
                    $subprods = array();
                    $subprodqty = array();
                    tep_db_query("DELETE FROM " . TABLE_PRODUCTS_BUNDLES . " WHERE bundle_id = '" .
                            (int) $products_id . "'");
                    for ($i = 0, $n = 100; $i < $n; $i++) {
                        if (isset($HTTP_POST_VARS['subproduct_' . $i . '_qty']) && ((int) $HTTP_POST_VARS['subproduct_' .
                                $i . '_qty'] > 0) && !in_array($HTTP_POST_VARS['subproduct_' . $i . '_id'], $to_avoid)) {
                            if (in_array($HTTP_POST_VARS['subproduct_' . $i . '_id'], $subprods)) {
                                $subprodqty[$HTTP_POST_VARS['subproduct_' . $i . '_id']] += (int) $HTTP_POST_VARS['subproduct_' .
                                        $i . '_qty'];
                                tep_db_query('update ' . TABLE_PRODUCTS_BUNDLES . ' set subproduct_qty = ' . (int)
                                        $subprodqty[$HTTP_POST_VARS['subproduct_' . $i . '_id']] . ' where bundle_id = ' .
                                        (int) $products_id . ' and subproduct_id = ' . (int) $HTTP_POST_VARS['subproduct_' .
                                        $i . '_id']);
                            } else {
                                $subprods[] = $HTTP_POST_VARS['subproduct_' . $i . '_id'];
                                $subprodqty[$HTTP_POST_VARS['subproduct_' . $i . '_id']] = (int) $HTTP_POST_VARS['subproduct_' .
                                        $i . '_qty'];
                                tep_db_query("INSERT INTO " . TABLE_PRODUCTS_BUNDLES .
                                        " (bundle_id, subproduct_id, subproduct_qty) VALUES ('" . (int) $products_id .
                                        "', '" . (int) $HTTP_POST_VARS['subproduct_' . $i . '_id'] . "', '" . (int) $HTTP_POST_VARS['subproduct_' .
                                        $i . '_qty'] . "')");
                            }
                        }
                    }
                    if (empty($subprods)) { // not a bundle if no subproducts set
                        tep_db_query('update ' . TABLE_PRODUCTS .
                                ' set products_bundle = "no" where products_id = ' . (int) $products_id);
                    } else { // calculate total MSRP and weight from subproducts
                        $msrp = 0;
                        $weight = 0;
                        foreach ($subprodqty as $id => $qty) {
                            $subprod_query = tep_db_query('select products_weight from ' . TABLE_PRODUCTS .
                                    ' where products_id = ' . (int) $id);
                            $subprod = tep_db_fetch_array($subprod_query);
                            //$msrp += ($subprod['products_msrp'] * $qty);
                            $weight += ($subprod['products_weight'] * $qty);
                        }
                        tep_db_query('update ' . TABLE_PRODUCTS .
                                ' set products_quantity = 1, products_weight = "' . tep_db_input($weight) .
                                '" where products_id = ' . (int) $products_id);
                    }
                }
                // EOF Bundled Products
                /** AJAX Attribute Manager  * */
                require_once ('attributeManager/includes/attributeManagerUpdateAtomic.inc.php');
                /** AJAX Attribute Manager  end * */
                $languages = tep_get_languages();
                //BOF RANGE Manager 10-JAN-2014
                if ($HTTP_POST_VARS['range_id'] && $HTTP_POST_VARS['range_id'] > 0 && $action ==
                        'insert_product') {
                    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                        $language_id = $languages[$i]['id'];
                        foreach ($products_id_arr as $val) {
                            $sql_data_array = array(
                                'products_name' => tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id]) .
                                '-' . $lanesArrProid[$val],
                                'products_description' => tep_db_prepare_input($HTTP_POST_VARS['products_description'][$language_id]),
                                'products_specifications' => tep_db_prepare_input($HTTP_POST_VARS['products_specifications'][$language_id]),
                                'products_url' => tep_db_prepare_input($HTTP_POST_VARS['products_url'][$language_id]),
                                'products_head_title_tag' => ((tep_not_null($HTTP_POST_VARS['products_head_title_tag'][$language_id])) ?
                                        tep_db_prepare_input($HTTP_POST_VARS['products_head_title_tag'][$language_id]) :
                                        tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id]) . '-' . $lanesArrProid[$val]),
                                'products_tags' => ((tep_not_null($HTTP_POST_VARS['products_tags'][$language_id])) ?
                                        tep_db_prepare_input($HTTP_POST_VARS['products_tags'][$language_id]) :
                                        tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id]) . '-' . $lanesArrProid[$val]),
                                'products_head_desc_tag' => ((tep_not_null($HTTP_POST_VARS['products_head_desc_tag'][$language_id])) ?
                                        tep_db_prepare_input($HTTP_POST_VARS['products_head_desc_tag'][$language_id]) :
                                        tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id]) . '-' . $lanesArrProid[$val]),
                                'products_head_keywords_tag' => ((tep_not_null($HTTP_POST_VARS['products_head_keywords_tag'][$language_id])) ?
                                        tep_db_prepare_input($HTTP_POST_VARS['products_head_keywords_tag'][$language_id]) :
                                        tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id]) . '-' . $lanesArrProid[$val]));
                            $products_id = $val;
                            if ($action == 'insert_product') {
                                $insert_sql_data = array('products_id' => $products_id, 'language_id' => $language_id);
                                $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
                                tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
                            } elseif ($action == 'update_product') {
                                tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = '" . (int) $products_id . "' and language_id = '" . (int) $language_id .
                                        "'");
                                //BOF AMAZON INTEGRATION PRODUCT EXTENDED 17 DEC 2013 START
                                tep_db_query("UPDATE " . TABLE_PRODUCTS_EXTENDED . " SET `upc_ean` = '" .
                                        tep_db_prepare_input($HTTP_POST_VARS['upc_ean']) . "' ,`brand_name` = '" .
                                        tep_db_prepare_input($HTTP_POST_VARS['brand_name']) .
                                        "' WHERE `osc_products_id` = '" . (int) $products_id . "' ");
                                //EOF AMAZON INTEGRATION PRODUCT EXTENDED 17 DEC 2013 END
                            }
                        }
                    }
                } else {
                    //EOF RANGE Manager 10-JAN-2014
                    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                        $language_id = $languages[$i]['id'];
                        $sql_data_array = array(
                            'products_name' => tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id]),
                            'products_description' => tep_db_prepare_input($HTTP_POST_VARS['products_description'][$language_id]),
                            'products_specifications' => tep_db_prepare_input($HTTP_POST_VARS['products_specifications'][$language_id]),
                            'products_url' => tep_db_prepare_input($HTTP_POST_VARS['products_url'][$language_id]),
                            'products_head_title_tag' => ((tep_not_null($HTTP_POST_VARS['products_head_title_tag'][$language_id])) ?
                                    tep_db_prepare_input($HTTP_POST_VARS['products_head_title_tag'][$language_id]) :
                                    tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id])),
                            'products_tags' => ((tep_not_null($HTTP_POST_VARS['products_tags'][$language_id])) ?
                                    tep_db_prepare_input($HTTP_POST_VARS['products_tags'][$language_id]) :
                                    tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id])),
                            'products_head_desc_tag' => ((tep_not_null($HTTP_POST_VARS['products_head_desc_tag'][$language_id])) ?
                                    tep_db_prepare_input($HTTP_POST_VARS['products_head_desc_tag'][$language_id]) :
                                    tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id])),
                            'products_head_keywords_tag' => ((tep_not_null($HTTP_POST_VARS['products_head_keywords_tag'][$language_id])) ?
                                    tep_db_prepare_input($HTTP_POST_VARS['products_head_keywords_tag'][$language_id]) :
                                    tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id])));
                        if ($action == 'insert_product') {
                            $insert_sql_data = array('products_id' => $products_id, 'language_id' => $language_id);
                            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
                            tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
                        } elseif ($action == 'update_product') {
                            tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = '" . (int) $products_id . "' and language_id = '" . (int) $language_id .
                                    "'");
                            //BOF AMAZON INTEGRATION PRODUCT EXTENDED 17 DEC 2013 START
                            tep_db_query("UPDATE " . TABLE_PRODUCTS_EXTENDED . " SET `upc_ean` = '" .
                                    tep_db_prepare_input($HTTP_POST_VARS['upc_ean']) . "' ,`brand_name` = '" .
                                    tep_db_prepare_input($HTTP_POST_VARS['brand_name']) .
                                    "' WHERE `osc_products_id` = '" . (int) $products_id . "' ");
                            //EOF AMAZON INTEGRATION PRODUCT EXTENDED 17 DEC 2013 END
                        }
                        //BOF:hash task
                        // code added on 09-10-2015 #start
                        $specifications = $_POST['specification_name_value'];
                        if (!empty($specifications)) {
                            foreach ($specifications as $specification) {
                                if (!empty($specification)) {
                                    $sql_data = array(
                                        'products_id' => (int) $products_id,
                                        'specification_id' => $specification,
                                    );
                                    tep_db_perform('product_specifications', $sql_data);
                                }
                            }
                        }
                        // code added on 09-10-2015 #ends
                        //EOF:hash task
                    }
                    //BOF RANGE MANAGER 10-JAN-2014
                }
                //EOF RANGE MANAGER 10-JAN-2014
                if (USE_CACHE == 'true') {
                    tep_reset_cache_block('categories');
                    tep_reset_cache_block('also_purchased');
                }
                if ((isset($_POST['btn_update_attribute'])) && ($_POST['btn_update_attribute'] == '1')) {
                    tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $products_id . '&action=new_product&update_attribute=1'));
                } else {
                    tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $products_id));
                }
            }
            break;
        case 'copy_to_confirm':
            if (isset($HTTP_POST_VARS['products_id']) && isset($HTTP_POST_VARS['categories_id'])) {
                $products_id = tep_db_prepare_input($HTTP_POST_VARS['products_id']);
                $categories_id = tep_db_prepare_input($HTTP_POST_VARS['categories_id']);
                if ($HTTP_POST_VARS['copy_as'] == 'link') {
                    if ($categories_id != $current_category_id) {
                        $check_query = tep_db_query("select count(*) as total from " .
                                TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int) $products_id .
                                "' and categories_id = '" . (int) $categories_id . "'");
                        $check = tep_db_fetch_array($check_query);
                        if ($check['total'] < '1') {
                            tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES .
                                    " (products_id, categories_id) values ('" . (int) $products_id . "', '" . (int) $categories_id .
                                    "')");
                            // BOF Separate Pricing Per Customer originally 2006-04-26 by Infobroker
                            $cg_price_query = tep_db_query("select customers_group_id, customers_group_price from " .
                                    TABLE_PRODUCTS_GROUPS . " where products_id = '" . $products_id .
                                    "' order by customers_group_id");
                            // insert customer group prices in table products_groups when there are any for the copied product
                            if (tep_db_num_rows($cg_price_query) > 0) {
                                while ($cg_prices = tep_db_fetch_array($cg_price_query)) {
                                    tep_db_query("insert into " . TABLE_PRODUCTS_GROUPS .
                                            " (customers_group_id, customers_group_price, products_id) values ('" . (int) $cg_prices['customers_group_id'] .
                                            "', '" . tep_db_input($cg_prices['customers_group_price']) . "', '" . (int) $dup_products_id .
                                            "')");
                                } // end while ( $cg_prices = tep_db_fetch_array($cg_price_query))
                            } // end if (tep_db_num_rows($cg_price_query) > 0)
                            // EOF Separate Pricing Per Customer originally 2006-04-26 by Infobroker
                        }
                    } else {
                        $messageStack->add_session(ERROR_CANNOT_LINK_TO_SAME_CATEGORY, 'error');
                    }
                } elseif ($HTTP_POST_VARS['copy_as'] == 'duplicate') {
                    //$product_query = tep_db_query("select products_quantity, products_model, products_image, products_mediumimage, products_largeimage,  products_price, products_date_available, products_weight, products_length, products_width, products_height, products_ready_to_ship,products_tax_class_id, manufacturers_id, free_shipping, disclaimer_needed, in_store_pickup, lock_price,hide_price from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
                    //BOF:range_manager
                    /*
                      //EOF:range_manager
                      $product_query = tep_db_query("select products_quantity, store_quantity, products_model, products_image, products_mediumimage, products_largeimage,  products_price, products_date_available, products_weight, products_length, products_width, products_height, products_ready_to_ship,products_tax_class_id, manufacturers_id, free_shipping, disclaimer_needed, in_store_pickup, lock_price,hide_price from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
                      //BOF:range_manager
                     */
//MVS
                    $product_query = tep_db_query("select products_quantity, store_quantity, products_model, products_image, products_mediumimage, products_largeimage,  products_price, products_date_available, products_weight, products_length, products_width, products_height, products_ready_to_ship,products_tax_class_id, manufacturers_id, free_shipping, lock_status, lock_title, lock_specs, is_store_item, disclaimer_needed, in_store_pickup, lock_price,hide_price, is_lane_item, range_id, lane_id, is_fullday_price, products_bundle, sold_in_bundle_only, vendors_prod_id, vendors_prod_comments, vendors_id from " .
                            TABLE_PRODUCTS . " where products_id = '" . (int) $products_id . "'");
                    //EOF:range_manager
                    $product = tep_db_fetch_array($product_query);
                    //tep_db_query("insert into " . TABLE_PRODUCTS . " (products_quantity, products_model,products_image, products_mediumimage, products_largeimage, products_price, products_date_added, products_date_available, products_weight, products_length, products_width, products_height, products_ready_to_ship,products_status, disclaimer_needed, products_tax_class_id, manufacturers_id, free_shipping, in_store_pickup, lock_price,hide_price) values ('" . tep_db_input($product['products_quantity']) . "', '" . tep_db_input($product['products_model']) . "', '" . tep_db_input($product['products_image']) . "', '" . tep_db_input($product['products_mediumimage']) . "', '" . tep_db_input($product['products_largeimage']) . "', '" . tep_db_input($product['products_price']) . "',  now(), " . (empty($product['products_date_available']) ? "null" : "'" . tep_db_input($product['products_date_available']) . "'") . ", '" . tep_db_input($product['products_weight']) . "', '" . tep_db_input($product['products_length']) . "',  '" . tep_db_input($product['products_width']) . "', '" . tep_db_input($product['products_height']) . "', '" . tep_db_input($product['products_ready_to_ship']) . "','0', '" . (int)$product['products_tax_class_id'] . "', '" . (int)$product['manufacturers_id'] . "', '" . (int)$product['free_shipping'] . "', '" . (int)$product['in_store_pickup'] . "', '" . (int)$product['lock_price'] . "', '" . (int)$product['hide_price'] . "')");
                    //BOF:range_manager
                    /*
                      //EOF:range_manager
                      tep_db_query("insert into " . TABLE_PRODUCTS . " (products_quantity, store_quantity, products_model,products_image, products_mediumimage, products_largeimage, products_price, products_date_added, products_date_available, products_weight, products_length, products_width, products_height, products_ready_to_ship,products_status, disclaimer_needed, products_tax_class_id, manufacturers_id, free_shipping, in_store_pickup, lock_price,hide_price) values ('" . tep_db_input($product['products_quantity']) . "', '" . tep_db_input($product['store_quantity']) . "', '" . tep_db_input($product['products_model']) . "', '" . tep_db_input($product['products_image']) . "', '" . tep_db_input($product['products_mediumimage']) . "', '" . tep_db_input($product['products_largeimage']) . "', '" . tep_db_input($product['products_price']) . "',  now(), " . (empty($product['products_date_available']) ? "null" : "'" . tep_db_input($product['products_date_available']) . "'") . ", '" . tep_db_input($product['products_weight']) . "', '" . tep_db_input($product['products_length']) . "',  '" . tep_db_input($product['products_width']) . "', '" . tep_db_input($product['products_height']) . "', '" . tep_db_input($product['products_ready_to_ship']) . "','0', '" . (int)$product['products_tax_class_id'] . "', '" . (int)$product['manufacturers_id'] . "', '" . (int)$product['free_shipping'] . "', '" . (int)$product['in_store_pickup'] . "', '" . (int)$product['lock_price'] . "', '" . (int)$product['hide_price'] . "')");
                      //BOF:range_manager
                     */
                    tep_db_query("insert into " . TABLE_PRODUCTS .
                            " (products_quantity, "
                            . "store_quantity, "
                            . "products_model,"
                            . "products_image, "
                            . "products_mediumimage, "
                            . "products_largeimage, "
                            . "products_price, "
                            . "products_date_added, "
                            . "products_date_available, "
                            . "products_weight, "
                            . "products_length, "
                            . "products_width, "
                            . "products_height, "
                            . "products_ready_to_ship,"
                            . "products_status, "
                            . "disclaimer_needed, "
                            . "products_tax_class_id, "
                            . "manufacturers_id, "
                            . "free_shipping, "
                            . "in_store_pickup, "
                            . "lock_price,"
                            . "hide_price, "
                            . "is_lane_item, "
                            . "range_id, "
                            . "lane_id, "
                            . "is_fullday_price, "
                            . "lock_title, "
                            . "lock_status, "
                            . "lock_specs, "
                            . "is_store_item, "
                            . "products_bundle, "
                            . "sold_in_bundle_only ) "
                            . "values ('"
                            . "" . tep_db_input($product['products_quantity']) . "', "
                            . "'" . tep_db_input($product['store_quantity']) . "', "
                            . "'" . tep_db_input($product['products_model']) . "', "
                            . "'" . tep_db_input($product['products_image']) . "', "
                            . "'" . tep_db_input($product['products_mediumimage']) . "', "
                            . "'" . tep_db_input($product['products_largeimage']) . "', "
                            . "'" . tep_db_input($product['products_price']) . "',  "
                            . " now(), "
                            . "" . (empty($product['products_date_available']) ? "null" : "'" . tep_db_input($product['products_date_available']) . "'") . ", "
                            . "'" . tep_db_input($product['products_weight']) . "', "
                            . "'" . tep_db_input($product['products_length']) . "',  "
                            . "'" . tep_db_input($product['products_width']) . "', "
                            . "'" . tep_db_input($product['products_height']) . "', "
                            . "'" . tep_db_input($product['products_ready_to_ship']) . "',"
                            . "'0', "
                            . "'" . tep_db_input($product['disclaimer_needed']) . "',"
                            . "'" . (int) $product['products_tax_class_id'] . "', "
                            . "'" . (int) $product['manufacturers_id'] . "', "
                            . "'" . (int) $product['free_shipping'] . "', "
                            . "'" . (int) $product['in_store_pickup'] . "', "
                            . "'" . (int) $product['lock_price'] . "', "
                            . "'" . (int) $product['hide_price'] . "', "
                            . "'" . (int) $product['is_lane_item'] . "', "
                            . "'" . (int) $product['range_id'] . "', "
                            . "'" . (int) $product['lane_id'] . "', "
                            . "'" . (int) $product['is_fullday_price'] . "', "
                            . "'" . (int) $product['lock_title'] . "', "
                            . "'" . (int) $product['lock_status'] . "', "
                            . "'" . (int) $product['lock_specs'] . "', "
                            . "'" . (int) $product['is_store_item'] . "',"
                            . "'" . tep_db_input($product['products_bundle']) . "',"
                            . "'" . tep_db_input($product['sold_in_bundle_only']) . "')");
                    //EOF:range_manager
                    $dup_products_id = tep_db_insert_id();
                    // bundled products begin
                    if ($product['products_bundle'] == 'yes') {
                        $bundle_query = tep_db_query('select subproduct_id, subproduct_qty from ' .
                                TABLE_PRODUCTS_BUNDLES . ' where bundle_id = ' . (int) $products_id);
                        while ($subprod = tep_db_fetch_array($bundle_query)) {
                            tep_db_query('insert into ' . TABLE_PRODUCTS_BUNDLES .
                                    " (bundle_id, subproduct_id, subproduct_qty) VALUES ('" . (int) $dup_products_id .
                                    "', '" . (int) $subprod['subproduct_id'] . "', '" . (int) $subprod['subproduct_qty'] .
                                    "')");
                        }
                    }
                    // bundled products end
                    $description_query = tep_db_query("select language_id, products_name, products_description, products_specifications, products_head_title_tag, products_head_desc_tag, products_head_keywords_tag, products_url, products_tags from " .
                            TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int) $products_id . "'");
                    while ($description = tep_db_fetch_array($description_query)) {
                        tep_db_query("insert into " . TABLE_PRODUCTS_DESCRIPTION .
                                " (products_id, language_id, products_name, products_description, products_head_title_tag, products_head_desc_tag, products_head_keywords_tag, products_url, products_viewed, products_specifications, products_tags) values ('" .
                                (int) $dup_products_id . "', '" . (int) $description['language_id'] . "', '" .
                                tep_db_input($description['products_name']) . "', '" . tep_db_input($description['products_description']) .
                                "', '" . tep_db_input($description['products_head_title_tag']) . "', '" .
                                tep_db_input($description['products_head_desc_tag']) . "', '" . tep_db_input($description['products_head_keywords_tag']) .
                                "', '" . tep_db_input($description['products_url']) . "', '0', '" . tep_db_input
                                        ($description['products_specifications']) . "', '" . tep_db_input($description['products_tags']) .
                                "')");
                    }
                    tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES .
                            " (products_id, categories_id) values ('" . (int) $dup_products_id . "', '" . (int)
                            $categories_id . "')");
                    $products_id = $dup_products_id;
                }
                if (USE_CACHE == 'true') {
                    tep_reset_cache_block('categories');
                    tep_reset_cache_block('also_purchased');
                }
            }
            tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $categories_id .
                            '&pID=' . $products_id));
            break;
        case 'new_product_preview':
            // BOF SKU RANGE MANAGER IS_FULL DAY PRICE 26-DEC-2013
            //BOF RANGE MANAGER 10-JAN-2014
            //if (isset($HTTP_POST_VARS['is_lane_item']) && ( $HTTP_POST_VARS['range_id']=='' || $HTTP_POST_VARS['lane_id']=='')) {
            if (isset($HTTP_POST_VARS['is_lane_item']) && $HTTP_POST_VARS['range_id'] == '') {
                //EOF RANGE MANAGER 10-JAN-2014
                $messageStack->add_session('ERROR_SELECT_RANGE_LANE', 'error');
                if ($_GET['pID'])
                    tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=&pID=' . $_GET['pID'] .
                                    '&action=new_product'));
                else
                    tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=&action=new_product'));
            }
            // EOF SKU RANGE MANAGER IS_FULL DAY PRICE 26-DEC-2013
            // copy image only if modified
            $products_image = new upload('products_image');
            $products_image->set_destination(DIR_FS_CATALOG_IMAGES);
            if ($products_image->parse() && $products_image->save()) {
                $products_image_name = $products_image->filename;
            } else {
                $products_image_name = (isset($HTTP_POST_VARS['products_previous_image']) ? $HTTP_POST_VARS['products_previous_image'] :
                                '');
            }
            $products_mediumimage = new upload('products_mediumimage');
            $products_mediumimage->set_destination(DIR_FS_CATALOG_IMAGES);
            if ($products_mediumimage->parse() && $products_mediumimage->save()) {
                $products_mediumimage_name = $products_mediumimage->filename;
            } else {
                $products_mediumimage_name = (isset($HTTP_POST_VARS['products_previous_mediumimage']) ?
                                $HTTP_POST_VARS['products_previous_mediumimage'] : '');
            }
            $products_largeimage = new upload('products_largeimage');
            $products_largeimage->set_destination(DIR_FS_CATALOG_IMAGES);
            if ($products_largeimage->parse() && $products_largeimage->save()) {
                $products_largeimage_name = $products_largeimage->filename;
            } else {
                $products_largeimage_name = (isset($HTTP_POST_VARS['products_previous_largeimage']) ?
                                $HTTP_POST_VARS['products_previous_largeimage'] : '');
            }
            $flag_prod_qty = tep_db_prepare_input($HTTP_POST_VARS['prod_inventory_flag']);
            $flag_prod_price = tep_db_prepare_input($HTTP_POST_VARS['prod_price_flag']);
            $flag_prod_cat = tep_db_prepare_input($HTTP_POST_VARS['prod_category_flag']);
            $flag_prod_desc = tep_db_prepare_input($HTTP_POST_VARS['prod_desc_flag']);
            $flag_prod_image = tep_db_prepare_input($HTTP_POST_VARS['prod_image_flag']);
            //BOF astro - update database with new images
            $products_image_2 = new upload('products_image_2');
            $products_image_3 = new upload('products_image_3');
            $products_image_4 = new upload('products_image_4');
            $products_image_5 = new upload('products_image_5');
            $products_image_6 = new upload('products_image_6');
            //BOF:mod 20120831
            /*
              //EOF:mod 20120831
              $products_image->set_destination(DIR_FS_CATALOG_IMAGES);
              //BOF:mod 20120831
             */
            //EOF:mod 20120831
            $products_image_2->set_destination(DIR_FS_CATALOG_IMAGES);
            $products_image_3->set_destination(DIR_FS_CATALOG_IMAGES);
            $products_image_4->set_destination(DIR_FS_CATALOG_IMAGES);
            $products_image_5->set_destination(DIR_FS_CATALOG_IMAGES);
            $products_image_6->set_destination(DIR_FS_CATALOG_IMAGES);
            //BOF:mod 20120831
            /*
              //EOF:mod 20120831
              if ($products_image->parse() && $products_image->save()) {
              $products_image_name = $products_image->filename;
              } else {
              $products_image_name = (isset($HTTP_POST_VARS['products_previous_image']) ? $HTTP_POST_VARS['products_previous_image'] : '');
              }
              //BOF:mod 20120831
             */
            //EOF:mod 20120831
            if ($products_image_2->parse() && $products_image_2->save()) {
                $products_image_2_name = $products_image_2->filename;
            } else {
                $products_image_2_name = (isset($HTTP_POST_VARS['products_previous_image_2']) ?
                                $HTTP_POST_VARS['products_previous_image_2'] : '');
            }
            if ($products_image_3->parse() && $products_image_3->save()) {
                $products_image_3_name = $products_image_3->filename;
            } else {
                $products_image_3_name = (isset($HTTP_POST_VARS['products_previous_image_3']) ?
                                $HTTP_POST_VARS['products_previous_image_3'] : '');
            }
            if ($products_image_4->parse() && $products_image_4->save()) {
                $products_image_4_name = $products_image_4->filename;
            } else {
                $products_image_4_name = (isset($HTTP_POST_VARS['products_previous_image_4']) ?
                                $HTTP_POST_VARS['products_previous_image_4'] : '');
            }
            if ($products_image_5->parse() && $products_image_5->save()) {
                $products_image_5_name = $products_image_5->filename;
            } else {
                $products_image_5_name = (isset($HTTP_POST_VARS['products_previous_image_5']) ?
                                $HTTP_POST_VARS['products_previous_image_5'] : '');
            }
            if ($products_image_6->parse() && $products_image_6->save()) {
                $products_image_6_name = $products_image_6->filename;
            } else {
                $products_image_6_name = (isset($HTTP_POST_VARS['products_previous_image_6']) ?
                                $HTTP_POST_VARS['products_previous_image_6'] : '');
            }
            // EOF astro - update database with new images
            break;
    }
}
// check if the catalog image directory exists
if (is_dir(DIR_FS_CATALOG_IMAGES)) {
    if (!is_writeable(DIR_FS_CATALOG_IMAGES))
        $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
} else {
    $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');
}
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
    <script>
        var jQuery = jQuery.noConflict();
        jQuery(document).ready(function () {
            jQuery('input[name="is_lane_item"]').click(function () {
                var is_lane_item = jQuery(this).prop('checked') ? true : false;
                if (!is_lane_item) {
                    jQuery('select[name="range_id"]').val('').prop('disabled', 'disabled');
                    jQuery('select[name="lane_id"]').val('').prop('disabled', 'disabled');
                    //BOF SKU is_fullday_price 26-DEC-2013
                    jQuery('input[name="is_fullday_price"]').prop('disabled', 'disabled');
                    //EOF SKU is_fullday_price 26-DEC-2013
                    //BOF RANGE MANAGER 10 JAN 2014
                    jQuery('input[name^="products_name"]').removeProp('readonly');
                    jQuery('input[name="products_model"]').removeProp('readonly');
                    //EOF RANGE MANAGER 10 JAN 2014
                } else {
                    jQuery('select[name="range_id"]').val('').removeProp('disabled');
                    //BOF SKU is_fullday_price 26-DEC-2013
                    jQuery('input[name="is_fullday_price"]').removeProp('disabled');
                    //EOF SKU is_fullday_price 26-DEC-2013
                    jQuery('select[name="lane_id"]').val('').prop('disabled', 'disabled');
                    //BOF RANGE MANAGER 10 JAN 2014
                    jQuery('input[name^="products_name"]').prop('readonly', 'readonly');
                    jQuery('input[name="products_model"]').prop('readonly', 'readonly');
                    //EOF RANGE MANAGER 10 JAN 2014
                }
            });
            jQuery('select[name="range_id"]').change(function () {
                range_id = jQuery(this).val();
                //BOF RANGE MANAGER 10 JAN 2014
                range_name = jQuery("select[name='range_id'] option[value='" + range_id + "']").text();
                jQuery('input[name^="products_name"]').val(range_name);
                //EOF RANGE MANAGER 10 JAN 2014
                jQuery.ajax({
                    url: '<?php
echo FILENAME_CATEGORIES;
?>',
                    method: 'post',
                    data: {
                        action: 'get_lanes',
                        range_id: range_id
                    },
                    success: function (html) {
                        jQuery('select[name="lane_id"]').val('').find('option').remove();
                        jQuery('select[name="lane_id"]').html(html).removeProp('disabled');
                    }
                });
            });
        });
        //parent-child 25Feb2014 (MA) BOF 
        function showParentProducts() {
            pr_model = jQuery('#pr_model').val();
            url = 'categories.php?action=getParentProduct&pr_model=' + pr_model + '&prod_id=<?php
echo $_GET['pID'];
?>';
            jQuery.post(url, function (data) {
                jQuery('#prModelText').html(data);
            });
        }
        function showaddedProducts() {
            //if(pid != null ){
            var prev_pid = jQuery('#child_ids').val();
            //alert(prev_pid);
            if (prev_pid != '' && prev_pid != ' ') {
                pid = prev_pid;
            } else {
                pid = '';
            }
            jQuery('#child_ids').val(pid);
<?php
if (isset($_GET['pID'])) {
    ?>
                url = 'categories.php?action=getSelectedProduct&pid=' + pid + '&parent_id=<?php
    echo $_GET['pID'] . '&parent_model=' . $_GET['model'];
    ?>';
                //alert(url);
    <?php
} else {
    ?>
                url = 'categories.php?action=getSelectedProduct&pid=' + pid;
    <?php
}
?>
            jQuery.post(url, function (data) {
                jQuery('#childprod').html(data);
            });
            //}
        }
        //parent-child 25Feb2014 (MA) EOF 
    </script>
    <?php
//EOF:range_manager
    ?>
    <script language="javascript" type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
    <script language="javascript" type="text/javascript">
        tinyMCE.init({
            mode: "textareas",
            editor_selector: "mceEditor",
            theme: "advanced",
            plugins: "table,advhr,advimage,advlink,emotions,preview,flash,print,contextmenu",
            theme_advanced_buttons1_add: "fontselect,fontsizeselect",
            theme_advanced_buttons2_add: "separator,preview,separator,forecolor,backcolor",
            theme_advanced_buttons2_add_before: "cut,copy,paste,separator",
            theme_advanced_buttons3_add_before: "tablecontrols,separator",
            theme_advanced_buttons3_add: "emotions,flash,advhr,separator,print",
            theme_advanced_toolbar_location: "top",
            theme_advanced_toolbar_align: "left",
            theme_advanced_path_location: "bottom",
            extended_valid_elements: "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
            external_link_list_url: "example_data/example_link_list.js",
            external_image_list_url: "example_data/example_image_list.js",
            flash_external_list_url: "example_data/example_flash_list.js"
        });
        //BOF: bulk_category_movement
        function stop_propogation(e) {
            if (!e) {
                var e = window.event;
            }
            e.cancelBubble = true;
            if (e.stopPropogation) {
                e.stopPropogation();
            }
        }
        function bulk_selection(bulkSelector) {
            var stat = (bulkSelector.checked ? true : false);
            var inputElems = document.getElementsByTagName('input');
            for (var i = 0; i < inputElems.length; i++) {
                if (inputElems[i].type.toLowerCase() == 'checkbox') {
                    if ((inputElems[i].getAttribute('name') == null) == false) {
                        if (inputElems[i].getAttribute('name').indexOf('prod') !== -1 || inputElems[i].getAttribute('name').indexOf('cat') !== -1) {
                            inputElems[i].checked = stat;
                        }
                    }
                }
            }
        }
        function popupWindow(url) {
            window.open(url, 'popupWindow', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=800,height=600,screenX=150,screenY=150,top=150,left=150')
        }
        //EOF: bulk_category_movement
    </script>
    <script language="javascript" src="includes/general.js"></script>
    <!-- AJAX Attribute Manager  -->
    <?php
    $_SESSION['current_products_id'] = $_GET['pID'];
//modification startrs here for attributemanager
    $query_to_checkparent = tep_db_query("select parent_products_model from products where products_id='" . $_SESSION['current_products_id'] . "' ");
    $rows = tep_db_fetch_array($query_to_checkparent);
    require_once ('attributeManager/includes/attributeManagerHeader.inc.php');
    ?>
    <!-- AJAX Attribute Manager  end -->
    <?php require('includes/account_check.js.php'); ?>
    <div id="spiffycalendar" class="text"></div>         
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <section>
        <!-- START Page content-->
        <section class="main-content">
            <h3><?php echo HEADING_TITLE; ?>
                <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
                <div class="panel-heading"><?php echo HEADING_TITLE; ?>
                    <a href="#" data-perform="panel-dismiss" data-toggle="tooltip" title="Close Panel" class="pull-right">
                        <em class="fa fa-times"></em>
                    </a>
                    <a href="#" data-perform="panel-collapse" data-toggle="tooltip" title="Collapse Panel" class="pull-right">
                        <em class="fa fa-minus"></em>
                    </a>
                </div>
                <!-- START table-responsive-->
                <div class="table-responsive">
                    <!-- START your table-->
                    <table class="table table-bordered table-hover">
                        <tr>
                            <!-- body_text //-->
                            <td>
                                <?php
// If Updated_without_prieview was chosen, then  - OBN
                                if (isset($HTTP_GET_VARS['Update_without_preview'])) {
                                    $action = 'update_product';
                                    $pID = $HTTP_GET_VARS['Update_pID'];
                                }
                                if ($action == 'new_product') {
                                    $parameters = array(
                                        'products_name' => '',
                                        'products_bundle' => '',
                                        'sold_in_bundle_only' => 'no',
                                        'products_description' => '',
                                        'products_specifications' => '',
                                        'products_url' => '',
                                        'products_id' => '',
                                        'products_quantity' => '',
                                        'store_quantity' => '',
                                        'products_model' => '',
                                        'products_size' => '',
                                        'disclaimer_needed' => '',
                                        'products_image' => '',
                                        'products_mediumimage' => '',
                                        'products_largeimage' => '',
                                        'products_price' => '',
                                        'products_weight' => '',
                                        'products_length' => '',
                                        'products_width' => '',
                                        'products_height' => '',
                                        'products_ready_to_ship' => '',
                                        'products_date_added' => '',
                                        'products_last_modified' => '',
                                        'products_date_available' => '',
                                        'products_status' => '',
                                        'products_tax_class_id' => '',
                                        'manufacturers_id' => '',
                                        'free_shipping' => '',
                                        'lock_title' => '',
                                        'lock_status' => '',
                                        'lock_specs' => '',
                                        'is_store_item' => '',
                                        'in_store_pickup' => '',
                                        'lock_price' => '',
                                        'hide_price' => '',
                                        'parent_products_model' => '',
                                        'markup' => DEFAULT_MARKUP,
                                        'manual_price' => '',
                                        'base_price' => '',
                                        'roundoff_flag' => '',
                                        //BOF:range_manager
                                        'is_lane_item' => '',
                                        'range_id' => '',
                                        'lane_id' => '',
                                        'product_image_2' => '',
                                        'product_image_3' => '',
                                        'product_image_4' => '',
                                        'product_image_5' => '',
                                        'product_image_6' => '',
                                        //EOF:range_manager
// MVS start
                                        'vendors_product_price' => '',
                                        'vendors_prod_comments' => '',
                                        'vendors_prod_id' => '',
                                        'vendors_id' => '',
//MVS end
                                    );
                                    $pInfo = new objectInfo($parameters);
                                    if (isset($HTTP_GET_VARS['pID']) && empty($HTTP_POST_VARS)) {
                                        /*                                         * ******************PRODUCT SIZE MOD BY FIW************************* */
                                        //$product_query = tep_db_query("select pd.products_name, pd.products_description, pd.products_head_title_tag, pd.products_head_desc_tag, pd.products_head_keywords_tag, pd.products_url, p.products_id, p.products_quantity, p.products_size, p.disclaimer_needed, p.products_model, p.products_image, p.products_mediumimage, p.products_largeimage, p.products_price, p.base_price, p.markup, p.manual_price, p.products_weight, p.free_shipping, p.in_store_pickup, p.lock_price, p.hide_price, p.roundoff_flag, products_length, products_width, products_height, products_ready_to_ship,p.products_date_added, p.products_last_modified, date_format(p.products_date_available, '%Y-%m-%d') as products_date_available, p.products_status, p.products_tax_class_id, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . (int)$HTTP_GET_VARS['pID'] . "' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'");
                                        //BOF:range_manager
                                        /*
                                          //EOF:range_manager
                                          $product_query = tep_db_query("select pd.products_name, pd.products_description, pd.products_head_title_tag, pd.products_head_desc_tag, pd.products_head_keywords_tag, pd.products_url, p.products_id, p.products_quantity, p.store_quantity, p.products_size, p.disclaimer_needed, p.products_model, p.products_image, p.products_mediumimage, p.products_largeimage, p.products_price, p.base_price, p.markup, p.manual_price, p.products_weight, p.free_shipping, p.in_store_pickup, p.lock_price, p.hide_price, p.roundoff_flag, products_length, products_width, products_height, products_ready_to_ship,p.products_date_added, p.products_last_modified, date_format(p.products_date_available, '%Y-%m-%d') as products_date_available, p.products_status, p.products_tax_class_id, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . (int)$HTTP_GET_VARS['pID'] . "' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'");
                                          //BOF:range_manager
                                         */
                                        $product_query = tep_db_query("select parent_products_model, pd.products_tags, pd.products_name, pd.products_description, pd.products_specifications, pd.products_head_title_tag, pd.products_head_desc_tag, pd.products_head_keywords_tag, pd.products_url, p.products_id, p.products_quantity, p.store_quantity, p.products_size, p.disclaimer_needed, p.products_model, p.products_image, p.products_mediumimage, p.products_largeimage, p.products_price, p.base_price, p.markup, p.product_image_2, p.product_image_3, p.product_image_4, p.product_image_5, p.product_image_6, p.manual_price, p.products_weight, p.free_shipping, p.lock_title, p.lock_status, p.lock_specs, p.is_store_item, p.in_store_pickup, p.lock_price, p.hide_price, p.roundoff_flag, products_length, products_width, products_height, products_ready_to_ship,p.products_date_added, p.products_last_modified, date_format(p.products_date_available, '%Y-%m-%d') as products_date_available, p.products_status, p.products_tax_class_id, p.manufacturers_id, p.is_lane_item, p.range_id, p.lane_id, p.is_fullday_price, p.products_bundle, p.sold_in_bundle_only, p.vendors_id,p.variation_theme_id from " .
                                                TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .
                                                " pd where p.products_id = '" . (int) $HTTP_GET_VARS['pID'] .
                                                "' and p.products_id = pd.products_id and pd.language_id = '" . (int) $languages_id .
                                                "'");
                                        //EOF:range_manager
                                        /*                                         * ******************PRODUCT SIZE MOD BY FIW************************* */
                                        $product = tep_db_fetch_array($product_query);
                                        $pInfo->objectInfo($product);
                                        if (empty($pInfo->parent_products_model)) {
                                            $checkForChildQuery = tep_db_query("select p.products_model, pd.products_name, p.products_id from " .
                                                    TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .
                                                    " pd where p.parent_products_model = '" . $pInfo->products_model .
                                                    "' and p.products_id = pd.products_id order by pd.products_name");
                                            $child_products_array = array();
                                            if (tep_db_num_rows($checkForChildQuery)) {
                                                while ($checkForChild = tep_db_fetch_array($checkForChildQuery)) {
                                                    $child_products_array[$checkForChild['products_id']] = array(
                                                        'name' => $checkForChild['products_name'],
                                                        'model' => $checkForChild['products_model']
                                                    );
                                                }
                                            }
                                        }
                                        $roundoff_flag = $product['roundoff_flag'];
                                        $flag_prod_qty = 0;
                                        $flag_prod_price = 0;
                                        $flag_prod_cat = 0;
                                        $flag_prod_desc = 0;
                                        $flag_prod_image = 0;
                                        $sql = tep_db_query("select flags from products_xml_feed_flags where products_id='" .
                                                (int) tep_db_prepare_input($HTTP_GET_VARS['pID']) . "'");
                                        if (tep_db_num_rows($sql)) {
                                            $show_xml_feed_flags = 1;
                                            $sql_info = tep_db_fetch_array($sql);
                                            $flags = $sql_info['flags'];
                                            $flag_prod_qty = substr($flags, 0, 1);
                                            $flag_prod_price = substr($flags, 1, 1);
                                            $flag_prod_cat = substr($flags, 2, 1);
                                            $flag_prod_desc = substr($flags, 3, 1);
                                            $flag_prod_image = substr($flags, 4, 1);
                                        } else {
                                            $show_xml_feed_flags = 0;
                                        }
                                        //bof AMAZON INTEGRATION product extented 17 dec 2013 start
                                        $products_extended_qry = tep_db_query("SELECT `upc_ean`,`brand_name` FROM `" .
                                                TABLE_PRODUCTS_EXTENDED . "` WHERE `osc_products_id` = '" . (int) $HTTP_GET_VARS['pID'] .
                                                "'");
                                        $products_extended = tep_db_fetch_array($products_extended_qry);
                                        //eof AMAZON INTEGRATION product extented 17 dec 2013 end
                                    } elseif (tep_not_null($HTTP_POST_VARS)) {
                                        $pInfo->objectInfo($HTTP_POST_VARS);
                                        $products_name = $HTTP_POST_VARS['products_name'];
                                        $products_description = $HTTP_POST_VARS['products_description'];
                                        $products_specifications = $HTTP_POST_VARS['products_specifications'];
                                        $products_url = $HTTP_POST_VARS['products_url'];
                                        $flag_prod_qty = $HTTP_POST_VARS['flag_prod_qty'];
                                        $flag_prod_price = $HTTP_POST_VARS['flag_prod_price'];
                                        $flag_prod_cat = $HTTP_POST_VARS['flag_prod_cat'];
                                        $flag_prod_desc = $HTTP_POST_VARS['flag_prod_desc'];
                                        $flag_prod_image = $HTTP_POST_VARS['flag_prod_image'];
                                    }
                                    // BOF Bundled Products
                                    if (isset($pInfo->products_bundle) && $pInfo->products_bundle == "yes") {
                                        // this product is a bundle so get contents data
                                        $bundle_query = tep_db_query("SELECT pb.subproduct_id, pb.subproduct_qty, pd.products_name FROM " .
                                                TABLE_PRODUCTS_DESCRIPTION . " pd INNER JOIN " . TABLE_PRODUCTS_BUNDLES .
                                                " pb ON pb.subproduct_id=pd.products_id WHERE pb.bundle_id = '" . (int) $HTTP_GET_VARS['pID'] .
                                                "' and language_id = '" . (int) $languages_id . "'");
                                        while ($bundle_contents = tep_db_fetch_array($bundle_query)) {
                                            $bundle_array[] = array(
                                                'id' => $bundle_contents['subproduct_id'],
                                                'qty' => $bundle_contents['subproduct_qty'],
                                                'name' => $bundle_contents['products_name']);
                                        }
                                    }
                                    $bundle_count = count($bundle_array);
                                    // EOF Bundled Products
                                    $manufacturers_array = array(array('id' => '', 'text' => TEXT_NONE));
                                    $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " .
                                            TABLE_MANUFACTURERS . " order by manufacturers_name");
                                    while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
                                        $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'],
                                            'text' => $manufacturers['manufacturers_name']);
                                    }
                                    //MVS start
                                    /* $vendors_array = array(array('id' => '1', 'text' => 'NONE'));
                                      $vendors_query = tep_db_query("select vendors_id, vendors_name from " . TABLE_VENDORS . " order by vendors_name");
                                      while ($vendors = tep_db_fetch_array($vendors_query)) {
                                      $vendors_array[] = array('id' => $vendors['vendors_id'],
                                      'text' => $vendors['vendors_name']);
                                      } */
//MVS end
                                    $tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
                                    $tax_class_query = tep_db_query("select tax_class_id, tax_class_title from " .
                                            TABLE_TAX_CLASS . " order by tax_class_title");
                                    while ($tax_class = tep_db_fetch_array($tax_class_query)) {
                                        $tax_class_array[] = array('id' => $tax_class['tax_class_id'], 'text' => $tax_class['tax_class_title']);
                                    }
                                    $languages = tep_get_languages();
                                    if (!isset($pInfo->products_status))
                                        $pInfo->products_status = '1';
                                    switch ($pInfo->products_status) {
                                        case '0':
                                            $in_status = false;
                                            $out_status = true;
                                            break;
                                        case '1':
                                        default:
                                            $in_status = true;
                                            $out_status = false;
                                    }
                                    /*                                     * ******************PRODUCT SIZE MOD BY FIW************************* */
                                    if (!isset($pInfo->products_size))
                                        $pInfo->products_size = '0';
                                    switch ($pInfo->products_size) {
                                        case '1':
                                            $in_size = false;
                                            $out_size = true;
                                            break;
                                        case '0':
                                        default:
                                            $in_size = true;
                                            $out_size = false;
                                    }
                                    /*                                     * ******************PRODUCT SIZE MOD BY FIW************************* */
                                    /*                                     * ******************DISCLAIMER NEEDED************************* */
                                    if (!isset($pInfo->disclaimer_needed))
                                        $pInfo->disclaimer_needed = '0';
                                    switch ($pInfo->disclaimer_needed) {
                                        case '0':
                                            $disclaimer_yes = false;
                                            $disclaimer_no = true;
                                            break;
                                        case '1':
                                        default:
                                            $disclaimer_yes = true;
                                            $disclaimer_no = false;
                                    }
                                    /*                                     * ******************DISCLAIMER NEEDED************************* */
                                    if (!isset($pInfo->free_shipping)) {
                                        $pInfo->free_shipping = '0';
                                    }
                                    switch ($pInfo->free_shipping) {
                                        case '0':
                                            $free_shipping_yes = false;
                                            $free_shipping_no = true;
                                            break;
                                        case '1':
                                            $free_shipping_yes = true;
                                            $free_shipping_no = false;
                                            break;
                                        default:
                                            $free_shipping_yes = false;
                                            $free_shipping_no = true;
                                    }
                                    if (!isset($pInfo->lock_status)) {
                                        $pInfo->lock_status = '0';
                                    }
                                    switch ($pInfo->lock_status) {
                                        case '1':
                                            $lock_status_flag_yes = true;
                                            $lock_status_flag_no = false;
                                            break;
                                        case '0':
                                        default:
                                            $lock_status_flag_yes = false;
                                            $lock_status_flag_no = true;
                                            break;
                                    }
                                    if (!isset($pInfo->lock_title)) {
                                        $pInfo->lock_title = '0';
                                    }
                                    switch ($pInfo->lock_title) {
                                        case '1':
                                            $lock_title_flag_yes = true;
                                            $lock_title_flag_no = false;
                                            break;
                                        case '0':
                                        default:
                                            $lock_title_flag_yes = false;
                                            $lock_title_flag_no = true;
                                            break;
                                    }
                                    if (!isset($pInfo->lock_specs)) {
                                        $pInfo->lock_specs = '0';
                                    }
                                    switch ($pInfo->lock_specs) {
                                        case '1':
                                            $lock_specs_flag_yes = true;
                                            $lock_specs_flag_no = false;
                                            break;
                                        case '0':
                                        default:
                                            $lock_specs_flag_yes = false;
                                            $lock_specs_flags_no = true;
                                            break;
                                    }
                                    if (!isset($pInfo->is_store_item)) {
                                        $pInfo->is_store_item = '0';
                                    }
                                    switch ($pInfo->is_store_item) {
                                        case '1':
                                            $store_item_flag_yes = true;
                                            $store_item_flag_no = false;
                                            break;
                                        case '0':
                                        default:
                                            $store_item_flag_yes = false;
                                            $store_item_flag_no = true;
                                            break;
                                    }
                                    if (!isset($pInfo->lock_price)) {
                                        $pInfo->lock_price = '0';
                                    }
                                    switch ($pInfo->lock_price) {
                                        case '0':
                                            $lock_price_yes = false;
                                            $lock_price_no = true;
                                            break;
                                        case '1':
                                            $lock_price_yes = true;
                                            $lock_price_no = false;
                                            break;
                                        default:
                                            $lock_price_yes = false;
                                            $lock_price_no = true;
                                    }
                                    if (!isset($pInfo->hide_price)) {
                                        $pInfo->hide_price = '0';
                                    }
                                    if (!isset($pInfo->in_store_pickup)) {
                                        $pInfo->in_store_pickup = '0';
                                    }
                                    switch ($pInfo->in_store_pickup) {
                                        case '0':
                                            $in_store_pickup_yes = false;
                                            $in_store_pickup_no = true;
                                            break;
                                        case '1':
                                            $in_store_pickup_yes = true;
                                            $in_store_pickup_no = false;
                                            break;
                                        default:
                                            $in_store_pickup_yes = false;
                                            $in_store_pickup_no = true;
                                    }
                                    if (!isset($flag_prod_qty)) {
                                        $flag_prod_qty = '0';
                                    }
                                    switch ($flag_prod_qty) {
                                        case '0':
                                            $update_prod_inventory_yes = false;
                                            $update_prod_inventory_no = true;
                                            break;
                                        case '1':
                                            $update_prod_inventory_yes = true;
                                            $update_prod_inventory_no = false;
                                            break;
                                    }
                                    if (!isset($flag_prod_price)) {
                                        $flag_prod_price = '0';
                                    }
                                    switch ($flag_prod_price) {
                                        case '0':
                                            $update_prod_price_yes = false;
                                            $update_prod_price_no = true;
                                            break;
                                        case '1':
                                            $update_prod_price_yes = true;
                                            $update_prod_price_no = false;
                                            break;
                                    }
                                    if (!isset($flag_prod_cat)) {
                                        $flag_prod_cat = '0';
                                    }
                                    switch ($flag_prod_cat) {
                                        case '0':
                                            $update_prod_category_yes = false;
                                            $update_prod_category_no = true;
                                            break;
                                        case '1':
                                            $update_prod_category_yes = true;
                                            $update_prod_category_no = false;
                                            break;
                                    }
                                    if (!isset($flag_prod_desc)) {
                                        $flag_prod_desc = '0';
                                    }
                                    switch ($flag_prod_desc) {
                                        case '0':
                                            $update_prod_desc_yes = false;
                                            $update_prod_desc_no = true;
                                            break;
                                        case '1':
                                            $update_prod_desc_yes = true;
                                            $update_prod_desc_no = false;
                                            break;
                                    }
                                    if (!isset($flag_prod_image)) {
                                        $flag_prod_image = '0';
                                    }
                                    switch ($flag_prod_image) {
                                        case '0':
                                            $update_prod_image_yes = false;
                                            $update_prod_image_no = true;
                                            break;
                                        case '1':
                                            $update_prod_image_yes = true;
                                            $update_prod_image_no = false;
                                            break;
                                    }
                                    $markup = $pInfo->markup;
                                    if (empty($markup)) {
                                        $markup = '0';
                                    }
                                    if (strpos($markup, '-')) {
                                        $operator = '-';
                                    } else {
                                        $operator = '+';
                                    }
                                    $markup = str_replace("-", "", $markup);
                                    
                                    if( strpos($markup, 'Margin')){
                                    
                                        $markup_flag = 'm';
                                        
                                        $markup = str_replace("% Margin", "", $markup);
                                        
                                        if($operator == '+'){
                                            $markup_price = $pInfo->base_price * ((float) (1 / (1 - ($markup / 100) ) ));    
                                        }else{
                                            $markup_price = $pInfo->base_price * ((float) (1 / (1 - (-$markup / 100) ) ));
                                        }
                                        
                                       
                                    
                                    
                                    }else if (strpos($markup, '%')) {
                                        $markup_flag = 'p';
                                        $markup = str_replace("%", "", $markup);
                                        $markup_price = $pInfo->base_price * ((float) $markup / 100);
                                    } else {
                                        $markup_flag = 'f';
                                        $markup_price = (float) $markup;
                                    }
                                    
                                    
                                    if ($operator == '+') {
                                        $price = $pInfo->base_price + $markup_price;
                                    } else {
                                        $price = $pInfo->base_price - $markup_price;
                                    }
                                    $pInfo->qpu_price = ($roundoff_flag ? apply_roundoff(round($price, 4)) : round($price, 4));
                                    //echo 	$pInfo->qpu_price;
                                    ?>
                                    <link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
                                    <script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
                                    <script language="javascript"><!--
                                        var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available", "btnDate1", "<?php
                                echo $pInfo->products_date_available;
                                ?>", scBTNMODE_CUSTOMBLUE);
                                        //--></script>
                                    <script language="javascript"><!--
                                        var tax_rates = new Array();
    <?php
    for ($i = 0, $n = sizeof($tax_class_array); $i < $n; $i++) {
        if ($tax_class_array[$i]['id'] > 0) {
            echo 'tax_rates["' . $tax_class_array[$i]['id'] . '"] = ' .
            tep_get_tax_rate_value($tax_class_array[$i]['id']) . ';' . "\n";
        }
    }
    ?>
                                        function doRound(x, places) {
                                            return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
                                        }
                                        function getTaxRate() {
                                            var selected_value = document.forms["new_product"].products_tax_class_id.selectedIndex;
                                            var parameterVal = document.forms["new_product"].products_tax_class_id[selected_value].value;
                                            if ((parameterVal > 0) && (tax_rates[parameterVal] > 0)) {
                                                return tax_rates[parameterVal];
                                            } else {
                                                return 0;
                                            }
                                        }
                                        function updateGross() {
                                            var operator = "<?php  echo $operator; ?>";
                                            var markup_flag = "<?php echo $markup_flag; ?>";
                                            var markup = parseFloat(<?php echo $markup; ?>);
                                            var roundoff_flag = '<?php echo $roundoff_flag; ?>';
                                            var markupPrice = 0;
                                            var grossValue = parseFloat(document.forms["new_product"].base_price.value);
                                            if (markup_flag == 'm') {
                                                if (operator == "+") {
                                                    grossValue = grossValue * ((1 / (1 - (markup / 100) ) ));
                                                } else {
                                                    grossValue = grossValue * ((1 / (1 - (-markup / 100) ) ));
                                                }
                                            }else if (markup_flag == 'p') {
                                                if (operator == "+") {
                                                    grossValue = grossValue * ((markup / 100) + 1);
                                                } else {
                                                    grossValue = grossValue * (1 - (markup / 100));
                                                }
                                            } else {
                                                if (operator == "+") {
                                                    grossValue = grossValue + markup;
                                                } else {
                                                    grossValue = grossValue - markup;
                                                }
                                            }
                                            document.forms["new_product"].qpu_price.value = (roundoff_flag == '1' ? apply_roundoff(doRound(grossValue, 4)) : doRound(grossValue, 4));
                                        }
                                        function apply_roundoff(price_value) {
                                            try {
                                                var response;
                                                if ((price_value + '').indexOf('.') == -1) {
                                                    response = (parseInt(price_value) - 1) + '.99';
                                                } else {
                                                    var value_parts = (price_value + '').split('.');
                                                    if ((value_parts[1] + '').length > 2) {
                                                        value_parts[1] = (value_parts[1] + '').substring(0, 2);
                                                    }
                                                    response = value_parts[0] + '.' + (parseInt(value_parts[1]) + (99 - parseInt(value_parts[1])));
                                                }
                                                return response;
                                            } catch (e) {
                                                alert(e);
                                            }
                                        }
                                        //--></script>
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
                                    <script>
                                        function showDiv(divId) {
                                            jQuery('.TabShowContent').css('display', 'none');
                                            jQuery('#' + divId).css('display', 'block');
                                            jQuery('.tabnavLi').css('background-color', '#eeeeee');
                                            jQuery('.Tab' + divId).css('background-color', '#aaaaaa');
                                        }
                                    </script>
                                    <script>
                                        jQuery(function () {
                                            jQuery("#tabs").tabs();
                                        });
                                    </script>
                                    <?php
                                    // Old form button, this negates the need for the preview. - OBN
                                    echo tep_draw_form('new_product', FILENAME_CATEGORIES, 'cPath=' . $cPath . (isset
                                                    ($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '') .
                                            '&action=new_product_preview', 'post', 'enctype="multipart/form-data"');
                                    //echo tep_draw_form('new_product', FILENAME_CATEGORIES, 'cPath=' . $cPath . (isset($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '') . '&action=update_product', 'post', 'enctype="multipart/form-data"');
                                    ?>
                                    <table class="table table-bordered table-hover">
                                        <tr>
                                            <td><table class="table table-bordered table-hover">
                                                    <tr>
                                                        <td><?php
                                                            echo sprintf(TEXT_NEW_PRODUCT, tep_output_generated_category_path($current_category_id));
                                                            ?></td>
                                                        <td><?php
                                                            echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT);
                                                            ?></td>
                                                    </tr>
                                                </table></td>
                                        </tr>
                                        <tr>
                                            <td><table class="table table-bordered table-hover">
                                                    <!-- BOF 10 JAN 2014 RANGE MANAGER////////////////////////////////////////////////////-->
                                                    <?php
                                                    //BOF:range_manager
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div id="tabs">
                                                                <ul>
                                                                    <li><a href="#tabs-1">Status</a></li>
                                                                    <li><a href="#tabs-2">Product Info</a></li>
                                                                    <li><a href="#tabs-3">Images</a></li>
                                                                    <li><a href="#tabs-4">Quantity</a></li>
                                                                    <li><a href="#tabs-5">Pricing</a></li>
                                                                    <li><a href="#tabs-6">Packages</a></li>
                                                                    <li><a href="#tabs-7">Specifications</a></li>
                                                                    <li><a href="#tabs-8">Parent/Child</a></li>
                                                                    <li><a href="#tabs-9">Options</a></li>
                                                                </ul>
                                                                <div id="tabs-1">
                                                                    <table class="table table-bordered table-hover">
                                                                        <tr>
                                                                            <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Is Lane Item: </td>
                                                                            <td>
                                                                                <?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_checkbox_field('is_lane_item', '1', ($pInfo->is_lane_item ? true : false));
                                                                                ?>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Shooting Range(s): </td>
                                                                            <td>
                                                                                <?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_pull_down_menu('range_id', get_shooting_ranges(), $pInfo->range_id, ($pInfo->
                                                                                        is_lane_item ? '' : 'disabled="disabled"'));
                                                                                ?>
                                                                            </td>
                                                                        </tr>
                                                                        <tr style="display:none;">
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr style="display:none;">
                                                                            <td>Lane No: </td>
                                                                            <td>
                                                                                <?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_pull_down_menu('lane_id', get_shooting_lanes_by_range_id($pInfo->
                                                                                                range_id), $pInfo->lane_id, ($pInfo->is_lane_item ? '' : 'disabled="disabled"'));
                                                                                ?>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <?php
                                                                        //EOF:range_manager
                                                                        ?>
                                                                        <?php
                                                                        // BOF SKU RANGE MANAGER IS_FULL DAY PRICE 26-DEC-2013
                                                                        ?>
                                                                        <tr>
                                                                            <td>Is Flat Rate: </td>
                                                                            <td>
                                                                                <?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_checkbox_field('is_fullday_price', '1', ($pInfo->is_fullday_price ? true : false), '', ($pInfo->is_lane_item ? '' : 'disabled="disabled"'));
                                                                                ?>
                                                                            </td>
                                                                        </tr>
                                                                        <tr> 
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <?php
                                                                        // EOF SKU RANGE MANAGER IS_FULL DAY PRICE 26-DEC-2013
                                                                        ?>
                                                                        <!-- EOF 10 JAN 2014 RANGE MANAGER////////////////////////////////////////////////////-->
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_STATUS;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_radio_field('products_status', '1', $in_status) . '&nbsp;' .
                                                                                TEXT_PRODUCT_AVAILABLE . '&nbsp;' . tep_draw_radio_field('products_status', '0', $out_status) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE;
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_SIZE;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_radio_field('products_size', '0', $in_size) . '&nbsp;Standard&nbsp;' .
                                                                                tep_draw_radio_field('products_size', '1', $out_size) . '&nbsp;Oversized';
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_DISCLAIMER_NEEDED;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_radio_field('disclaimer_needed', '0', $disclaimer_no) .
                                                                                '&nbsp;No&nbsp;' . tep_draw_radio_field('disclaimer_needed', '1', $disclaimer_yes) .
                                                                                '&nbsp;Yes';
                                                                                ?></td>
                                                                        </tr>	
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>  
                                                                        <tr>
                                                                            <td><?php
                                                                                echo 'Flags Status';
                                                                                ?></td>			
                                                                            <td>
                                                                                <table>
                                                                                    <tr>
                                                                                        <td><?php
                                                                                            echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo 'Is free shipping';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo '&nbsp;:&nbsp;';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo tep_draw_radio_field('free_shipping', '1', $free_shipping_yes) .
                                                                                            '&nbsp;Yes&nbsp;' . tep_draw_radio_field('free_shipping', '0', $free_shipping_no) .
                                                                                            '&nbsp;No';
                                                                                            ?></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td><?php
                                                                                            echo 'Is in-store pickup';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo '&nbsp;:&nbsp;';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo tep_draw_radio_field('in_store_pickup', '1', $in_store_pickup_yes) .
                                                                                            '&nbsp;Yes&nbsp;' . tep_draw_radio_field('in_store_pickup', '0', $in_store_pickup_no) .
                                                                                            '&nbsp;No';
                                                                                            ?></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td><?php
                                                                                            echo 'Lock Price';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo '&nbsp;:&nbsp;';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo tep_draw_radio_field('lock_price', '1', $lock_price_yes) .
                                                                                            '&nbsp;Yes&nbsp;' . tep_draw_radio_field('lock_price', '0', $lock_price_no) .
                                                                                            '&nbsp;No';
                                                                                            ?></td>
                                                                                    </tr>
                                                                                    <?php
                                                                                    //if ($show_xml_feed_flags) {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td><?php
                                                                                            echo 'XML feed -> Update product inventory';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo '&nbsp;:&nbsp;';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo tep_draw_radio_field('prod_inventory_flag', '1', $update_prod_inventory_yes) .
                                                                                            '&nbsp;Yes&nbsp;' . tep_draw_radio_field('prod_inventory_flag', '0', $update_prod_inventory_no) .
                                                                                            '&nbsp;No';
                                                                                            ?></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td><?php
                                                                                            echo 'XML feed -> Update product price';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo '&nbsp;:&nbsp;';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo tep_draw_radio_field('prod_price_flag', '1', $update_prod_price_yes) .
                                                                                            '&nbsp;Yes&nbsp;' . tep_draw_radio_field('prod_price_flag', '0', $update_prod_price_no) .
                                                                                            '&nbsp;No';
                                                                                            ?></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td><?php
                                                                                            echo 'XML feed -> Update category';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo '&nbsp;:&nbsp;';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo tep_draw_radio_field('prod_category_flag', '1', $update_prod_category_yes) .
                                                                                            '&nbsp;Yes&nbsp;' . tep_draw_radio_field('prod_category_flag', '0', $update_prod_category_no) .
                                                                                            '&nbsp;No';
                                                                                            ?></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td><?php
                                                                                            echo 'XML feed -> Update product description';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo '&nbsp;:&nbsp;';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo tep_draw_radio_field('prod_desc_flag', '1', $update_prod_desc_yes) .
                                                                                            '&nbsp;Yes&nbsp;' . tep_draw_radio_field('prod_desc_flag', '0', $update_prod_desc_no) .
                                                                                            '&nbsp;No';
                                                                                            ?></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td><?php
                                                                                            echo 'XML feed -> Update product image';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo '&nbsp;:&nbsp;';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo tep_draw_radio_field('prod_image_flag', '1', $update_prod_image_yes) .
                                                                                            '&nbsp;Yes&nbsp;' . tep_draw_radio_field('prod_image_flag', '0', $update_prod_image_no) .
                                                                                            '&nbsp;No';
                                                                                            ?></td>
                                                                                    </tr>
                                                                                    <?php
                                                                                    //}
                                                                                    ?>
                                                                                    <tr><td><hr/></td></tr>
                                                                                    <tr>
                                                                                        <td><?php
                                                                                            echo 'Lock Status';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo '&nbsp;:&nbsp;';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo tep_draw_radio_field('lock_status_flag', '1', $lock_status_flag_yes) .
                                                                                            '&nbsp;Yes&nbsp;' . tep_draw_radio_field('lock_status_flag', '0', $lock_status_flag_no) .
                                                                                            '&nbsp;No';
                                                                                            ?></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td><?php
                                                                                            echo 'Lock Title';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo '&nbsp;:&nbsp;';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo tep_draw_radio_field('lock_title_flag', '1', $lock_title_flag_yes) .
                                                                                            '&nbsp;Yes&nbsp;' . tep_draw_radio_field('lock_title_flag', '0', $lock_title_flag_no) .
                                                                                            '&nbsp;No';
                                                                                            ?></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td><?php
                                                                                            echo 'Lock Product Specifications';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo '&nbsp;:&nbsp;';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo tep_draw_radio_field('lock_specs_flag', '1', $lock_specs_flag_yes) .
                                                                                            '&nbsp;Yes&nbsp;' . tep_draw_radio_field('lock_specs_flag', '0', $lock_specs_flag_no) .
                                                                                            '&nbsp;No';
                                                                                            ?></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td><?php
                                                                                            echo 'Is Store Item';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo '&nbsp;:&nbsp;';
                                                                                            ?></td>
                                                                                        <td><?php
                                                                                            echo tep_draw_radio_field('store_item_flag', '1', $store_item_flag_yes) .
                                                                                            '&nbsp;Yes&nbsp;' . tep_draw_radio_field('store_item_flag', '0', $store_item_flag_no) .
                                                                                            '&nbsp;No';
                                                                                            ?></td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_READY_TO_SHIP;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_checkbox_field('products_ready_to_ship', '1', (($product['products_ready_to_ship'] ==
                                                                                        '1') ? true : false));
                                                                                ?></td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                                <div id="tabs-2">
                                                                    <table class="table table-bordered table-hover">
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>		  
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_DATE_AVAILABLE;
                                                                                ?><br><small>(YYYY-MM-DD)</small></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;';
                                                                                ?>
                                                                                <script language="javascript">dateAvailable.writeControl();
                                                                                    dateAvailable.dateFormat = "yyyy-MM-dd";</script></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <?php /* //MVS start ?>
                                                                          <tr>
                                                                          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                                                                          </tr>
                                                                          <tr>
                                                                          <td class="main"><?php echo TEXT_PRODUCTS_VENDORS; ?></td>
                                                                          <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('vendors_id', $vendors_array, $pInfo->vendors_id); ?></td>
                                                                          </tr>
                                                                          <?php */ //MVS end ?>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_MANUFACTURER;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $pInfo->
                                                                                        manufacturers_id);
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <?php
                                                                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                                                            ?>
                                                                            <tr>
                                                                                <td><?php
                                                                                    if ($i == 0)
                                                                                        echo TEXT_PRODUCTS_NAME;
                                                                                    ?></td>
                                                                                <td><?php
                                                                                    echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                                            '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .
                                                                                    tep_draw_input_field('products_name[' . $languages[$i]['id'] . ']', (isset($products_name[$languages[$i]['id']]) ?
                                                                                                    stripslashes($products_name[$languages[$i]['id']]) : tep_get_products_name($pInfo->
                                                                                                            products_id, $languages[$i]['id'])));
                                                                                    ?></td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                        <!-- HTC BOC //-->
                                                                        <?php
                                                                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                                                            ?>
                                                                            <tr>
                                                                                <td><?php
                                                                                    if ($i == 0)
                                                                                        echo TEXT_PRODUCTS_DESCRIPTION;
                                                                                    ?><br />
                                                                                    <table class="table table-bordered table-hover">
                                                                                        <tr>
                                                                                            <td><?php
                                                                                                echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                                                        '/images/' . $languages[$i]['image'], $languages[$i]['name']);
                                                                                                ?>&nbsp;</td>
                                                                                            <td><?php
                                                                                                echo tep_draw_textarea_field('products_description[' . $languages[$i]['id'] .
                                                                                                        ']', 'soft', '110', '30', (isset($products_description[$languages[$i]['id']]) ?
                                                                                                                stripslashes($products_description[$languages[$i]['id']]) :
                                                                                                                tep_get_products_description($pInfo->products_id, $languages[$i]['id'])), 'class="mceEditor"');
                                                                                                ?></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <!-- commented on 12-10-2015 #start -->
                                                                            <input type="hidden" name="products_specifications[<?php echo $languages[$i]['id']; ?>]" value="<?php
                                                                            echo (isset($products_specifications[$languages[$i]['id']]) ?
                                                                                    stripslashes($products_specifications[$languages[$i]['id']]) :
                                                                                    tep_get_products_specifications($pInfo->products_id, $languages[$i]['id']));
                                                                            ?>">
                                 <!--<tr>
                                    <td class="main" valign="top" colspan="2"><?php
                                                                            if ($i == 0)
                                                                            //echo 'Product Specifications';
                                                                                
                                                                                ?><br />
                                      <table border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td class="main" valign="top"><?php
                                                                            /* echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                              '/images/' . $languages[$i]['image'], $languages[$i]['name']); */
                                                                            ?>&nbsp;</td>
                                          <td class="main"><?php
                                                                            /* echo tep_draw_textarea_field('products_specifications[' . $languages[$i]['id'] .
                                                                              ']', 'soft', '110', '30', (isset($products_specifications[$languages[$i]['id']]) ?
                                                                              stripslashes($products_specifications[$languages[$i]['id']]) :
                                                                              tep_get_products_specifications($pInfo->products_id, $languages[$i]['id'])),
                                                                              'class="mceEditor"'); */
                                                                            ?></td>
                                        </tr>
                                      </table>
                                    </td>
                                  </tr>-->
                                                                            <!-- commented on 12-10-2015 #ends --> 
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                        <?php /*  //MVS start ?>
                                                                          <tr>
                                                                          <td class="main"><?php echo TEXT_VENDORS_PROD_COMMENTS; ?></td>
                                                                          <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_textarea_field('vendors_prod_comments', 'soft', '70', '5', (isset($vendors_prod_comments) ? $vendors_prod_comments : tep_get_vendors_prod_comments($pInfo->products_id))); ?></td>
                                                                          </tr>
                                                                          <tr>
                                                                          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                                                                          </tr>
                                                                          <?php */  //MVS end ?>
                                                                        <tr>
                                                                            <td><hr><?php
                                                                                echo TEXT_PRODUCT_METTA_INFO;
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>          
                                                                        <?php
                                                                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                                                            ?>
                                                                            <tr>
                                                                                <td><?php
                                                                                    echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                    ?></td>
                                                                            </tr>          
                                                                            <tr>
                                                                                <td><?php
                                                                                    if ($i == 0)
                                                                                        echo 'Products Keywords:';
                                                                                    ?></td>
                                                                                <td><table class="table table-bordered table-hover">
                                                                                        <tr>
                                                                                            <td><?php
                                                                                                echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                                                        '/images/' . $languages[$i]['image'], $languages[$i]['name']);
                                                                                                ?>&nbsp;</td>
                                                                                            <td><?php
                                                                                                echo tep_draw_textarea_field('products_tags[' . $languages[$i]['id'] . ']', 'soft', '70', '5', (isset($products_tags[$languages[$i]['id']]) ? stripslashes($products_tags[$languages[$i]['id']]) :
                                                                                                                tep_get_products_tags($pInfo->products_id, $languages[$i]['id'])));
                                                                                                ?></td>
                                                                                        </tr>
                                                                                    </table></td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                                                            ?>
                                                                            <tr>
                                                                                <td><?php
                                                                                    if ($i == 0)
                                                                                        echo TEXT_PRODUCTS_PAGE_TITLE;
                                                                                    ?></td>
                                                                                <td><table class="table table-bordered table-hover">
                                                                                        <tr>
                                                                                            <td><?php
                                                                                                echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                                                        '/images/' . $languages[$i]['image'], $languages[$i]['name']);
                                                                                                ?>&nbsp;</td>
                                                                                            <td><?php
                                                                                                echo tep_draw_textarea_field('products_head_title_tag[' . $languages[$i]['id'] .
                                                                                                        ']', 'soft', '70', '5', (isset($products_head_title_tag[$languages[$i]['id']]) ?
                                                                                                                stripslashes($products_head_title_tag[$languages[$i]['id']]) :
                                                                                                                tep_get_products_head_title_tag($pInfo->products_id, $languages[$i]['id'])));
                                                                                                ?></td>
                                                                                        </tr>
                                                                                    </table></td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                                                            ?>
                                                                            <tr>
                                                                                <td><?php
                                                                                    echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                    ?></td>
                                                                            </tr>          
                                                                            <tr>
                                                                                <td><?php
                                                                                    if ($i == 0)
                                                                                        echo TEXT_PRODUCTS_HEADER_DESCRIPTION;
                                                                                    ?></td>
                                                                                <td><table class="table table-bordered table-hover">
                                                                                        <tr>
                                                                                            <td><?php
                                                                                                echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                                                        '/images/' . $languages[$i]['image'], $languages[$i]['name']);
                                                                                                ?>&nbsp;</td>
                                                                                            <td><?php
                                                                                                echo tep_draw_textarea_field('products_head_desc_tag[' . $languages[$i]['id'] .
                                                                                                        ']', 'soft', '70', '5', (isset($products_head_desc_tag[$languages[$i]['id']]) ?
                                                                                                                stripslashes($products_head_desc_tag[$languages[$i]['id']]) :
                                                                                                                tep_get_products_head_desc_tag($pInfo->products_id, $languages[$i]['id'])));
                                                                                                ?></td>
                                                                                        </tr>
                                                                                    </table></td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                                                            ?>
                                                                            <tr>
                                                                                <td><?php
                                                                                    echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                    ?></td>
                                                                            </tr>          
                                                                            <tr>
                                                                                <td><?php
                                                                                    if ($i == 0)
                                                                                        echo TEXT_PRODUCTS_KEYWORDS;
                                                                                    ?></td>
                                                                                <td><table class="table table-bordered table-hover">
                                                                                        <tr>
                                                                                            <td><?php
                                                                                                echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                                                        '/images/' . $languages[$i]['image'], $languages[$i]['name']);
                                                                                                ?>&nbsp;</td>
                                                                                            <td><?php
                                                                                                echo tep_draw_textarea_field('products_head_keywords_tag[' . $languages[$i]['id'] .
                                                                                                        ']', 'soft', '70', '5', (isset($products_head_keywords_tag[$languages[$i]['id']]) ?
                                                                                                                stripslashes($products_head_keywords_tag[$languages[$i]['id']]) :
                                                                                                                tep_get_products_head_keywords_tag($pInfo->products_id, $languages[$i]['id'])));
                                                                                                ?></td>
                                                                                        </tr>
                                                                                    </table></td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <?php /*  //MVS start ?>
                                                                          <tr>
                                                                          <td class="main"><?php echo TEXT_VENDORS_PROD_ID; ?></td>
                                                                          <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('vendors_prod_id', $pInfo->vendors_prod_id); ?></td>
                                                                          </tr>
                                                                          <?php */  //MVS end ?>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_MODEL;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_input_field('products_model', $pInfo->products_model);
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <!-- BOF AMAZON INTEGRATION CODE 17-Dec -->		  
                                                                        <tr>
                                                                            <td>UPC Number</td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_input_field('upc_ean', $products_extended['upc_ean']);
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Manufacturer Part Num</td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_input_field('brand_name', $products_extended['brand_name']);
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <!--EOF INTEGRATION CODE 17-Dec -->		  
                                                                        <?php
                                                                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                                                            ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <?php
                                                                                    //if ($i == 0) echo TEXT_PRODUCTS_URL . '<br><small>' . TEXT_PRODUCTS_URL_WITHOUT_HTTP . '</small>';
                                                                                    echo '&nbsp';
                                                                                    ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?php
                                                                                    //echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('products_url[' . $languages[$i]['id'] . ']', (isset($products_url[$languages[$i]['id']]) ? stripslashes($products_url[$languages[$i]['id']]) : tep_get_products_url($pInfo->products_id, $languages[$i]['id'])));
                                                                                    echo tep_draw_hidden_field('products_url[' . $languages[$i]['id'] . ']', (isset
                                                                                                    ($products_url[$languages[$i]['id']]) ? stripslashes($products_url[$languages[$i]['id']]) :
                                                                                                    tep_get_products_url($pInfo->products_id, $languages[$i]['id'])));
                                                                                    ?>
                                                                                </td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_WEIGHT;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_input_field('products_weight', $pInfo->products_weight);
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_LENGTH;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_input_field('products_length', $pInfo->products_length);
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_WIDTH;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_input_field('products_width', $pInfo->products_width);
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_HEIGHT;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_input_field('products_height', $pInfo->products_height);
                                                                                ?></td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                                <div id="tabs-3">
                                                                    <table class="table table-bordered table-hover">
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_IMAGE;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_file_field('products_image') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $pInfo->products_image . tep_draw_hidden_field('products_previous_image', $pInfo->products_image);
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo 'Medium Image: ';
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_file_field('products_mediumimage') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $pInfo->products_mediumimage . tep_draw_hidden_field('products_previous_mediumimage', $pInfo->products_mediumimage);
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo 'Large Image: ';
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_file_field('products_largeimage') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $pInfo->products_largeimage . tep_draw_hidden_field('products_previous_largeimage', $pInfo->products_largeimage);
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><b>Optional extra product images</b></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_IMAGE;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_file_field('products_image_2') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $pInfo->product_image_2 . tep_draw_hidden_field('products_previous_image_2', $pInfo->product_image_2);
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_IMAGE;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_file_field('products_image_3') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $pInfo->product_image_3 . tep_draw_hidden_field('products_previous_image_3', $pInfo->product_image_3);
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_IMAGE;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_file_field('products_image_4') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $pInfo->product_image_4 . tep_draw_hidden_field('products_previous_image_4', $pInfo->product_image_4);
                                                                                ?></td>
                                                                        </tr><tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_IMAGE;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_file_field('products_image_5') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $pInfo->product_image_5 . tep_draw_hidden_field('products_previous_image_5', $pInfo->product_image_5);
                                                                                ?></td>
                                                                        </tr><tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_IMAGE;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_file_field('products_image_6') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $pInfo->product_image_6 . tep_draw_hidden_field('products_previous_image_6', $pInfo->product_image_6);
                                                                                ?></td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                                <div id="tabs-4">
                                                                    <table class="table table-bordered table-hover">
                                                                        <!-- HTC EOC //-->
                                                                        <tr>
                                                                            <td>
                                                                                <?php
                                                                                //echo TEXT_PRODUCTS_QUANTITY;
                                                                                echo 'Warehouse Quantity';
                                                                                ?>
                                                                            </td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_input_field('products_quantity', $pInfo->products_quantity);
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Store Quantity</td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_input_field('store_quantity', $pInfo->store_quantity);
                                                                                ?></td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                                <div id="tabs-5">
                                                                    <table class="table table-bordered table-hover">
                                                                        <!-- BOF Separate Pricing Per Customer -->
                                                                        <?php
                                                                        $customers_group_query = tep_db_query("select customers_group_id, customers_group_name from " .
                                                                                TABLE_CUSTOMERS_GROUPS .
                                                                                " where customers_group_id != '0' order by customers_group_id");
                                                                        $header = false;
                                                                        while ($customers_group = tep_db_fetch_array($customers_group_query)) {
                                                                            if (tep_db_num_rows($customers_group_query) > 0) {
                                                                                $attributes_query = tep_db_query("select customers_group_id, customers_group_price from " .
                                                                                        TABLE_PRODUCTS_GROUPS . " where products_id = '" . $pInfo->products_id .
                                                                                        "' and customers_group_id = '" . $customers_group['customers_group_id'] .
                                                                                        "' order by customers_group_id");
                                                                            } else {
                                                                                $attributes = array('customers_group_id' => 'new');
                                                                            }
                                                                            if (!$header) {
                                                                                ?>
                                                                                <tr>
                                                                                    <td style="font-style: italic"><?php
                                                                                        echo "Customer Groups";
                                                                                        ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php
                                                                                $header = true;
                                                                            } // end if (!header), makes sure this is only shown once
                                                                            ?>
                                                                            <tr>
                                                                                <td><?php
                                                                                    // only change in version 4.1.1
                                                                                    if (isset($pInfo->sppcoption)) {
                                                                                        echo tep_draw_checkbox_field('sppcoption[' . $customers_group['customers_group_id'] .
                                                                                                ']', 'sppcoption[' . $customers_group['customers_group_id'] . ']', (isset($pInfo->
                                                                                                        sppcoption[$customers_group['customers_group_id']])) ? 1 : 0);
                                                                                    } else {
                                                                                        echo tep_draw_checkbox_field('sppcoption[' . $customers_group['customers_group_id'] .
                                                                                                ']', 'sppcoption[' . $customers_group['customers_group_id'] . ']', true) .
                                                                                        '&nbsp;' . $customers_group['customers_group_name'];
                                                                                    }
                                                                                    ?>
                                                                                    &nbsp;</td>
                                                                                <td><?php
                                                                                    if ($attributes = tep_db_fetch_array($attributes_query)) {
                                                                                        echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                        tep_draw_input_field('sppcprice[' . $customers_group['customers_group_id'] . ']', $attributes['customers_group_price']);
                                                                                    } else {
                                                                                        if (isset($pInfo->sppcprice[$customers_group['customers_group_id']])) { // when a preview was done and the back button used
                                                                                            $sppc_cg_price = $pInfo->sppcprice[$customers_group['customers_group_id']];
                                                                                        } else { // nothing in the db, nothing in the post variables
                                                                                            $sppc_cg_price = '';
                                                                                        }
                                                                                        echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                        tep_draw_input_field('sppcprice[' . $customers_group['customers_group_id'] . ']', $sppc_cg_price);
                                                                                    }
                                                                                    ?></td>
                                                                            </tr>
                                                                            <?php
                                                                        } // end while ($customers_group = tep_db_fetch_array($customers_group_query))
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <!-- EOF Separate Pricing Per Customer -->
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo TEXT_PRODUCTS_TAX_CLASS;
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $pInfo->
                                                                                        products_tax_class_id);
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo 'Product Cost:';
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_input_field('base_price', ((int) $pInfo->base_price <= 0 ? '' : $pInfo->
                                                                                                base_price), 'onKeyUp="updateGross()"');
                                                                                ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo 'QPU Price: ';
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;';
                                                                                ?><input type="text" name="qpu_price" value="<?php
                                                                                echo (int) $pInfo->base_price <= 0 ? '' : $pInfo->products_price;
                                                                                ?>" readonly></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <?php
                                                                                //echo 'Manual Price: ';
                                                                                echo 'MAP: ';
                                                                                ?>
                                                                            </td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_input_field('manual_price', $pInfo->manual_price);
                                                                                ?></td>
                                                                        </tr>
                                                                        <?php /* //MVS start ?>
                                                                          <tr bgcolor="#ebebff">
                                                                          <td class="main"><?php echo TEXT_VENDORS_PRODUCT_PRICE_BASE; ?></td>
                                                                          <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('vendors_product_price', $pInfo->vendors_product_price, 'onKeyUp="updateNet()"'); ?></td>
                                                                          </tr>
                                                                          <?php */  //MVS end ?>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo 'Hide Price: ';
                                                                                ?></td>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                                                                                tep_draw_checkbox_field('hide_price', '1', $pInfo->hide_price);
                                                                                ?></td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                                <div id="tabs-6">
                                                                    <table class="table table-bordered table-hover">
                                                                        <!-- BOF Bundled Products -->
                                                                        <tr>
                                                                            <td></td>
                                                                            <td>
                                                                                <?php
                                                                                echo tep_draw_radio_field('sold_in_bundle_only', 'no', true, $pInfo->
                                                                                        sold_in_bundle_only) . ENTRY_AVAILABLE_SEPARATELY . '<br />' .
                                                                                tep_draw_radio_field('sold_in_bundle_only', 'yes', false, $pInfo->
                                                                                        sold_in_bundle_only) . ENTRY_IN_BUNDLE_ONLY;
                                                                                ?>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <?php
                                                                                echo TEXT_PRODUCTS_BUNDLE;
                                                                                ?>
                                                                            </td>
                                                                            <td>
                                                                                <table>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <?php
                                                                                            echo tep_draw_separator('pixel_trans.gif', '24', '15') . tep_draw_pull_down_menu('products_bundle', array(array('id' => 'no', 'text' => 'No'), array('id' => 'yes', 'text' => 'Yes')), $pInfo->products_bundle) .
                                                                                            '<br><a href="javascript:" onclick="addSubproduct()">' . TEXT_ADD_LINE .
                                                                                            '</a><br>';
                                                                                            ?>
                                                                                        </td>
                                                                                        <td>
                                                                                            <script type="text/javascript"><!--
                                                                                                function fillCodes() {
                                                                                                    for (var n = 0; n < 100; n++) {
                                                                                                        var this_subproduct_id = eval("document.new_product.subproduct_" + n + "_id")
                                                                                                        var this_subproduct_name = eval("document.new_product.subproduct_" + n + "_name")
                                                                                                        var this_subproduct_qty = eval("document.new_product.subproduct_" + n + "_qty")
                                                                                                        if (this_subproduct_id.value == "") {
                                                                                                            this_subproduct_id.value = document.new_product.subproduct_selector.value
                                                                                                            this_subproduct_qty.value = "1"
                                                                                                            var name = document.new_product.subproduct_selector[document.new_product.subproduct_selector.selectedIndex].text
                                                                                                            this_subproduct_name.value = name
                                                                                                            document.returnValue = true;
                                                                                                            return true;
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                                function clearSubproduct(n) {
                                                                                                    var this_subproduct_id = eval("document.new_product.subproduct_" + n + "_id");
                                                                                                    var this_subproduct_name = eval("document.new_product.subproduct_" + n + "_name");
                                                                                                    var this_subproduct_qty = eval("document.new_product.subproduct_" + n + "_qty");
                                                                                                    this_subproduct_id.value = "";
                                                                                                    this_subproduct_name.value = "";
                                                                                                    this_subproduct_qty.value = "";
                                                                                                }
                                                                                                function addSubproduct() {
                                                                                                    var n = parseInt(document.getElementById('bundled_subproducts_i').value);
                                                                                                    var HTML = document.getElementById('bundled_subproducts');
                                                                                                    currentElement = document.createElement("input");
                                                                                                    currentElement.setAttribute("disabled", "");
                                                                                                    currentElement.setAttribute("size", "30");
                                                                                                    currentElement.setAttribute("type", "text");
                                                                                                    currentElement.setAttribute("name", 'subproduct_' + n + '_name');
                                                                                                    currentElement.setAttribute("value", "");
                                                                                                    HTML.appendChild(currentElement);
                                                                                                    currentElement = document.createElement("input");
                                                                                                    currentElement.setAttribute("size", "3");
                                                                                                    currentElement.setAttribute("type", "hidden");
                                                                                                    currentElement.setAttribute("name", 'subproduct_' + n + '_id');
                                                                                                    currentElement.setAttribute("value", "");
                                                                                                    HTML.appendChild(currentElement);
                                                                                                    currentElement = document.createTextNode(' ');
                                                                                                    HTML.appendChild(currentElement);
                                                                                                    currentElement = document.createElement("input");
                                                                                                    currentElement.setAttribute("size", "3");
                                                                                                    currentElement.setAttribute("type", "text");
                                                                                                    currentElement.setAttribute("name", 'subproduct_' + n + '_qty');
                                                                                                    currentElement.setAttribute("value", "");
                                                                                                    HTML.appendChild(currentElement);
                                                                                                    document.createTextNode('&nbsp;');
                                                                                                    HTML.appendChild(currentElement);
                                                                                                    var myLink = document.createElement('a');
                                                                                                    var href = document.createAttribute('href');
                                                                                                    myLink.setAttribute('href', 'javascript:');
                                                                                                    myLink.setAttribute('onclick', 'clearSubproduct(' + n + ')');
    <?php
    echo "myLink.innerText = ' [x] " . TEXT_REMOVE_PRODUCT . "';\n";
    ?>
                                                                                                    HTML.appendChild(myLink);
                                                                                                    currentElement = document.createElement("br");
                                                                                                    HTML.appendChild(currentElement);
                                                                                                    document.getElementById('bundled_subproducts_i').value = n + 1;
                                                                                                }
                                                                                                function get_products(cid) {
                                                                                                    jQuery('#productsection').html('<img src="images/ajax_loader.gif" title="loading ..." alt="loading ...">');
                                                                                                    url = 'categories.php?action=getProduct&cid=' + cid + '&pID=<?php
    echo $_GET['pID'];
    ?>';
                                                                                                    jQuery.post(url, function (data) {
                                                                                                        jQuery('#productsection').html(data);
                                                                                                        //$('#add_button').css('display','block');
                                                                                                    });
                                                                                                }
                                                                                                //--></script>
                                                                                            <div id="bundled_subproducts">
                                                                                                <?php
                                                                                                echo TEXT_BUNDLE_HEADING . "<br />\n";
                                                                                                for ($i = 0, $n = $bundle_count ? $bundle_count + 1 : 3; $i < $n; $i++) {
                                                                                                    echo '<input type="text" disabled size="30" name="subproduct_' . $i .
                                                                                                    '_name" value="' . tep_output_string($bundle_array[$i]['name']) . '">' . "\n";
                                                                                                    echo '<input type="hidden" size="3" name="subproduct_' . $i . '_id" value="' . $bundle_array[$i]['id'] .
                                                                                                    '">' . "\n";
                                                                                                    echo '<input type="text" size="3" name="subproduct_' . $i . '_qty" value="' . $bundle_array[$i]['qty'] .
                                                                                                    '">' . "\n";
                                                                                                    echo '<a href="javascript:clearSubproduct(' . $i . ')">[x] ' .
                                                                                                    TEXT_REMOVE_PRODUCT . "</a><br>\n";
                                                                                                }
                                                                                                ?>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <?php
                                                                                            echo tep_draw_hidden_field('bundled_subproducts_i', $i, 'id="bundled_subproducts_i"');
                                                                                            echo 'Select category : <br/>' . tep_draw_pull_down_menu('cPath', tep_get_category_tree(), '', 'onChange="get_products(this.value);"');
                                                                                            ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td id="productsection">
                                                                                            please select category first....
                                                                                            <?php
                                                                                            /* echo TEXT_ADD_PRODUCT . '<select name="subproduct_selector" onChange="fillCodes()">';
                                                                                              echo '<option name="null" value="" SELECTED></option>';
                                                                                              $where_str = '';
                                                                                              if (isset($HTTP_GET_VARS['pID'])) {
                                                                                              $bundle_check = bundle_avoid($HTTP_GET_VARS['pID']);
                                                                                              if (!empty($bundle_check)) {
                                                                                              $where_str = ' and (not (p.products_id in (' . implode(',', $bundle_check) . ')))';
                                                                                              }
                                                                                              }
                                                                                              /*$products = tep_db_query("select pd.products_name, p.products_id, p.products_model from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id <> " . (int)$HTTP_GET_VARS['pID'] . $where_str . " order by p.products_model");
                                                                                              while($products_values = tep_db_fetch_array($products)) {
                                                                                              echo "\n" . '<option name="' . $products_values['products_id'] . '" value="' . $products_values['products_id'] . '">' . $products_values['products_name'] . " (" . $products_values['products_model'] . ')</option>';
                                                                                              }
                                                                                              echo '</select>'; */
                                                                                            ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                        <!-- EOF Bundled Products -->
                                                                    </table>
                                                                </div>
                                                                <div id="tabs-7">
                                                                    <table class="table table-bordered table-hover">
                                                                        <?php
//BOF:hash task
//if (empty($pInfo->parent_products_model)){
                                                                        ?>
                                                                        <tr>
                                                                            <td>
                                                                                <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <table class="table table-bordered table-hover">
                                                                                    <?php
                                                                                    $specifications = array();
                                                                                    $specifications_query = tep_db_query("select psn.id,psn.name,psv.value,ps.specification_id from product_specification_names as psn left join product_specification_values as psv on (psn.id=psv.specification_name_id) left join product_specifications as ps on (ps.specification_id = psv.id) where ps.products_id='" . (int) $pInfo->products_id . "' order by psn.name");
                                                                                    //$specifications_query = tep_db_query("select po.products_options_name as text, po.products_options_id as id from products_options po inner join  products_attributes pa on po.products_options_id=pa.options_id where pa.products_id='" . (int)$pInfo->products_id . "' and po.language_id='1' order by po.products_options_name");
                                                                                    $exclude_spec_array = array(0);
                                                                                    if (tep_db_num_rows($specifications_query)) {
                                                                                        while ($entry = tep_db_fetch_array($specifications_query)) {
                                                                                            $specifications[] = array(
                                                                                                'id' => $entry['id'],
                                                                                                'specification_id' => $entry['specification_id'],
                                                                                                'name' => $entry['name'],
                                                                                                'value' => $entry['value']
                                                                                            );
                                                                                            //$exclude_spec_array[] = $entry['id'];
                                                                                        }
                                                                                    }
                                                                                    $options_query = tep_db_query("select id,name from product_specification_names where id NOT IN (" . implode(",", $exclude_spec_array) . ") order by name");
                                                                                    $str_options = '<option value="">-- Select Specification --</option>';
                                                                                    while ($option = tep_db_fetch_array($options_query)) {
                                                                                        $str_options .= '<option value="' . $option['id'] . '">' . $option['name'] . '</option>';
                                                                                    }
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td>Associated Specifications:</td>
                                                                                    </tr>
                                                                                    <?php if (!empty($specifications)) { ?>
                                                                                        <tr>
                                                                                            <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <th>Specification Name</th>
                                                                                            <th>Specification value</th>
                                                                                            <th>Action</th>
                                                                                        </tr>
                                                                                        <?php
                                                                                        $x = 0;
                                                                                        foreach ($specifications as $specification) {
                                                                                            $x++;
                                                                                            ?>
                                                                                            <tr id="spec<?php echo $specification['specification_id']; ?>">
                                                                                                <td><?php echo $specification['name']; ?></td>
                                                                                                <td id="editspecvalue<?php echo $x; ?>"><?php echo $specification['value']; ?></td>
                                                                                                <td>
                                                                                                    <img style="cursor:pointer;" width="15" src="attributeManager/images/edit_icon.png" alt="Edit" title="Edit" onClick="editSpec('<?php echo $specification['id']; ?>', '<?php echo $pInfo->products_id; ?>', '<?php echo $specification['specification_id']; ?>', '<?php echo $x; ?>');" id="<?php echo $specification['specification_id']; ?>" class="edit_button_row<?php echo $x; ?>">
                                                                                                    |
                                                                                                    <img style="cursor:pointer;" src="attributeManager/images/icon_delete.png" alt="Remove" title="Remove" onClick="removeSpec('<?php echo $pInfo->products_id; ?>', '<?php echo $specification['specification_id']; ?>');"></td>
                                                                                            </tr>
                                                                                            <?php
                                                                                        }
                                                                                    }
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td>Associate New Specification(s): </th>
                                                                                        <th><img src="attributeManager/images/icon_add_new.png" alt="click to associate new specification" align="absmiddle" title="click to associate new specification" onClick="addSpec();" style="cursor:pointer;"></th>
                                                                                    </tr> 
                                                                                    <tr id="before_spec"></tr>
                                                                                    <tr>
                                                                                        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                                                                                    </tr>    
                                                                                    <script type="text/javascript">
                                                                                        var old_spec_value = [];
                                                                                        function editSpec(id, products_id, specification_id, row_no) {
                                                                                            old_spec_value[row_no] = jQuery('#editspecvalue' + row_no).html();
                                                                                            var specification_id = jQuery('.edit_button_row' + row_no).attr('id');
                                                                                            jQuery.ajax({
                                                                                                type: "POST",
                                                                                                url: "categories.php?mode=get_spec_value",
                                                                                                data: "id=" + id + "&products_id=" + products_id + "&specification_id=" + specification_id,
                                                                                                success: function (response) {
                                                                                                    if (response != '') {
                                                                                                        jQuery('#editspecvalue' + row_no).html('<select name="edited_spec' + row_no + '" id="edited_spec' + row_no + '">' + response + '</select>&nbsp;<img onclick="updateNewSpecification(' + products_id + ',' + row_no + ');" title="save" alt="save" src="attributeManager/images/icon_add.png">&nbsp;<img onclick="oldSpecification(' + specification_id + ',' + row_no + ')" title="Remove" alt="Remove" src="attributeManager/images/icon_delete.png" style="cursor:pointer;">');
                                                                                                    }
                                                                                                }
                                                                                            });
                                                                                        }
                                                                                        function oldSpecification(specification_id, row_no) {
                                                                                            jQuery('#editspecvalue' + row_no).empty().html(old_spec_value[row_no]);
                                                                                        }
                                                                                        function updateNewSpecification(products_id, row_no) {
                                                                                            var specification_id_value = jQuery('#edited_spec' + row_no).val();
                                                                                            var specification_id_text = jQuery('#edited_spec' + row_no + ' option:selected').text();
                                                                                            var old_specification_id = jQuery('.edit_button_row' + row_no).attr('id');
                                                                                            jQuery.ajax({
                                                                                                type: "POST",
                                                                                                url: "categories.php?mode=update_spec_value",
                                                                                                data: "products_id=" + products_id + "&specification_id=" + specification_id_value + "&old_specification_id=" + old_specification_id,
                                                                                                success: function (response) {
                                                                                                    if (response == 'OK') {
                                                                                                        jQuery('#editspecvalue' + row_no).empty().html(specification_id_text);
                                                                                                        old_spec_value[row_no] = specification_id_text;
                                                                                                        jQuery('.edit_button_row' + row_no).attr('id', specification_id_value);
                                                                                                    }
                                                                                                }
                                                                                            });
                                                                                        }
                                                                                        function removeSpec(products_id, specification_id) {
                                                                                            jQuery.ajax({
                                                                                                type: "POST",
                                                                                                url: "categories.php?mode=remove_spec",
                                                                                                data: "products_id=" + products_id + "&specification_id=" + specification_id,
                                                                                                success: function (response) {
                                                                                                    if (response == 'OK') {
                                                                                                        jQuery("#spec" + specification_id).fadeOut('slow');
                                                                                                    }
                                                                                                }
                                                                                            });
                                                                                        }
                                                                                        var spec_row = 1;
                                                                                        var str_options = '<?php echo $str_options; ?>';
                                                                                        function addSpec() {
                                                                                            var html = '<tr id="remove_spec' + spec_row + '"><td  valign="top" class="main">' + spec_row + '&nbsp;<select name="specification[]" id="" onChange="fetchValue(this.value,' + spec_row + ');">' + str_options + '</select></td><td valign="top" class="main"><select name="specification_name_value[]" id="spec_value' + spec_row + '"><option value="">-- Select Specification Value --</option></select>&nbsp;<img src="attributeManager/images/icon_delete.png" alt="remove" title="remove" style="cursor:pointer;" onClick="removeSpecRow(' + spec_row + ');"></td></tr>';
                                                                                            spec_row++;
                                                                                            jQuery('#before_spec').before(html);
                                                                                        }
                                                                                        function removeSpecRow(row_id) {
                                                                                            jQuery('#remove_spec' + row_id).remove();
                                                                                            spec_row--;
                                                                                        }
                                                                                        function fetchValue(id, spec_row) {
                                                                                            if (id == '') {
                                                                                                jQuery('#spec_value' + spec_row).html('<option value="">-- Select Specification Value --</option>');
                                                                                                return false;
                                                                                            }
                                                                                            jQuery.ajax({
                                                                                                type: "POST",
                                                                                                url: "categories.php?mode=get_spec_value",
                                                                                                data: "id=" + id + "&products_id=" +<?php echo $_GET['pID']; ?>,
                                                                                                success: function (response) {
                                                                                                    if (response != '') {
                                                                                                        jQuery('#spec_value' + spec_row).html(response);
                                                                                                    }
                                                                                                }
                                                                                            });
                                                                                        }
                                                                                        function addNewSpec() {
                                                                                            jQuery('#new_spec_here').show();
                                                                                        }
                                                                                        var new_specifications_created = [];
                                                                                        function saveNewSpecification() {
                                                                                            var specification_name_new = jQuery('#specification_name_new').val();
                                                                                            var specification_value_new = jQuery('#specification_value_new').val();
                                                                                            var products_id = '<?php echo $_GET['pID']; ?>';
                                                                                            if (specification_name_new == '') {
                                                                                                alert("Error: Specification name required!");
                                                                                                return false;
                                                                                            } else if (specification_value_new == '') {
                                                                                                alert("Error: Specification value required!");
                                                                                                return false;
                                                                                            } else {
                                                                                                jQuery.ajax({
                                                                                                    type: "POST",
                                                                                                    url: "categories.php?mode=add_new_spec_value",
                                                                                                    data: "specification_name_new=" + specification_name_new + '&specification_value_new=' + specification_value_new + "&products_id=" + products_id,
                                                                                                    success: function (response) {
                                                                                                        if (response == '-1') {
                                                                                                            alert("Error: Specification name already exists!");
                                                                                                        } else if (response == '-2') {
                                                                                                            alert("Error: Specification value already exists!");
                                                                                                        } else if (response != '') {
                                                                                                            alert("Specification added successfully...");
                                                                                                            if (new_specifications_created.indexOf(specification_name_new) == -1) {
                                                                                                                str_options += response;
                                                                                                                new_specifications_created[new_specifications_created.length] = specification_name_new;
                                                                                                            }
                                                                                                            jQuery('#specification_name_new').val('');
                                                                                                            jQuery('#specification_value_new').val('');
                                                                                                            jQuery('#new_spec_here').hide();
                                                                                                        } else {
                                                                                                            alert("Error: Please try after sometime!");
                                                                                                        }
                                                                                                    }
                                                                                                });
                                                                                                return true;
                                                                                            }
                                                                                        }
                                                                                    </script>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <table>
                                                                                    <tr>
                                                                                        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td colspan="3" class="main">&nbsp;<img src="attributeManager/images/add-new-spec.png" alt="click to add new specification" align="absmiddle" title="click to add new specification" onClick="addNewSpec();" style="cursor:pointer;"><span id="new_spec_here" style="display:none;">&nbsp;&nbsp;<input type="text" name="specification_name_new" id="specification_name_new" placeholder="Specification Name" /> &nbsp; <input type="text" name="specification_value_new" id="specification_value_new" placeholder="Specification Value" />&nbsp;<img style="cursor:pointer;" src="attributeManager/images/icon_delete.png" alt="Remove" title="Remove" onClick="jQuery('#new_spec_here').hide();">&nbsp;<img src="attributeManager/images/icon_add.png" alt="save" title="save" onClick="saveNewSpecification();"></span></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <input type="hidden" name="btn_update_attribute" id="btn_update_attribute" value="0">
                                                                                <img src="attributeManager/images/update.png" align="" alt="" title="" onClick="updateAttribute();" name="btn_update_attr">
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
//}
//else {
//EOF:hash task
                                                                        ?>
                                                                        <!-- AJAX Attribute Manager  -->
                                                                        <tr>
                                                                            <td>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <!-- AJAX Attribute Manager end -->
                                                                        <?php
//BOF:hash task
//}
//EOF:hash task
                                                                        ?>
<script type="text/javascript">
jQuery('input[name=\'specification_name_new\']').autocomplete({
	delay: 500,
	source: function (request, response) {
		jQuery.ajax({
			url: 'categories.php?mode=getspecname&filter_name=' + encodeURIComponent(request.term),
			dataType: 'json',
			success: function (json) {
				response(jQuery.map(json, function (item) {
					return {
						label: item.name,
					}
				}));
			}
		});
	},
	select: function (event, ui) {
		jQuery('#specification_name_new').val(ui.item.label);
		return false;
	},
	focus: function (event, ui) {
		return false;
	}
});
                                                                            function updateAttribute() {
                                                                                jQuery('#btn_update_attribute').val('1');
                                                                                jQuery('#btn_without_prev').trigger('click');
                                                                            }
                                                                        </script>
                                                                        <tr>
                                                                            <td><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                                <div id="tabs-8">
                                                                    <table class="table table-bordered table-hover">
                                                                        <?php
                                                                        //parent-child 25Feb2014 (MA) BOF
                                                                        if (!empty($pInfo->parent_products_model)) {
                                                                            $get_parent_name_query = tep_db_query("select p.products_id, pd.products_name FROM products p, products_description pd WHERE p.products_id = pd.products_id and p.products_model = '" .
                                                                                    $pInfo->parent_products_model . "'");
                                                                            $get_parent_name = tep_db_fetch_array($get_parent_name_query);
                                                                            ?>
                                                                            <tr>
                                                                                <td><?php echo 'Parent Product:'; ?></td>
                                                                                <td id="prModelText">
                                                                                    <?php echo $pInfo->parent_products_model . '<br>' . $get_parent_name['products_name']; ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td> Edit Parent Products: </td><td><?php
                                                                                    echo '<input onclick="popupWindow(\'' . tep_href_link('add_edit_parent_products.php', (!empty($_GET['pID']) ? 'pID=' . $_GET['pID'] . '&model=' . $pInfo->
                                                                                                    products_model : '')) . '\')" type="button" value="Edit Parent" name="editParent" ><input type="hidden" name="parent_products_model" value="' .
                                                                                    $pInfo->parent_products_model . '" id="pr_model">';
                                                                                    ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><?php
                                                                                    echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                    ?></td>
                                                                            </tr>
                                                                            <?php
                                                                        } else {
                                                                            $childProductStr = '';
                                                                            $keystr = '';
                                                                            if (!empty($child_products_array)) {
                                                                                $childProductStr = '<table align="center" width="80%" cellspacing="0" cellpading="0">
                        <tr class="dataTableHeadingRow">
                        <td>Child Products Name</td></tr>';
                                                                                $keystr = '';
                                                                                // get parent options if exists #start
                                                                                $parent_options = array();
                                                                                $cond = "";
                                                                                $get_parent_attributes = tep_db_query("select options_id from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . $_GET['pID'] . "'");
                                                                                while ($res_options = tep_db_fetch_array($get_parent_attributes)) {
                                                                                    $parent_options[] = $res_options['options_id'];
                                                                                }
                                                                                if (count($parent_options) > 0) {
                                                                                    $cond = " AND patrib.options_id NOT IN (" . implode(",", $parent_options) . ")";
                                                                                }
                                                                                // get parent options if exists #ends 
                                                                                foreach ($child_products_array as $key => $val) {
                                                                                    /* get child attributes #start  */
                                                                                    $display_child_products_attribute = '';
                                                                                    $products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . $key . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int) $languages_id . "' and popt.is_xml_feed_option='0'  $cond");
                                                                                    $products_attributes = tep_db_fetch_array($products_attributes_query);
                                                                                    if ($products_attributes['total'] > 0) {
                                                                                        $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . $key . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int) $languages_id . "' and popt.is_xml_feed_option='0'  $cond order by patrib.products_options_sort_order");
                                                                                        while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
                                                                                            $products_options_array = array();
                                                                                            $products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . $key . "' and pa.options_id = '" . (int) $products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int) $languages_id . "' order by pa.products_options_sort_order");
                                                                                            while ($products_options = tep_db_fetch_array($products_options_query)) {
                                                                                                $products_options_array[] = array(
                                                                                                    'id' => $products_options['products_options_values_id'],
                                                                                                    'text' => $products_options['products_options_values_name']);
                                                                                                if ($products_options['options_values_price'] != '0') {
                                                                                                    $products_options_array[sizeof($products_options_array) - 1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . ') ';
                                                                                                }
                                                                                            }
                                                                                            $display_child_products_attribute .= '<strong>' . $products_options_name['products_options_name'] . '</strong>:' . $products_options_array[0]['text'] . '<br/>';
                                                                                        }
                                                                                    }
                                                                                    /* get child attributes #ends  */
                                                                                    $keystr .= $key . ',';
$manage_amazon_variation = '';
if($pInfo->variation_theme_id > 0){
	
	$manage_amazon_variation = '&nbsp; <span onClick="manageAmazonVariations(' . $key . ', '.$pInfo->variation_theme_id.');" style="cursor:pointer;color:#0033FF;"><strong> [Manage Amazon Variations] </strong></span>';
}
                                                                                    $childProductStr .= '<tr>
                     <td>' . $child_products_array[$key]['name'] .
                                                                                            '<span onClick="manageChildAttributes(' . $key . ');" style="cursor:pointer;color:#0033FF;"><strong> [Manage Attribute] </strong></span>'. $manage_amazon_variation  .'<br>' . $display_child_products_attribute . '</td></tr><tr class="dataTableRow"><td class="dataTableContent">&nbsp;</td></tr>';
                                                                                }
                                                                                $childProductStr .= '</table>';
                                                                            }
                                                                            $keystr = '<input id="child_ids" type="hidden" value="' . rtrim($keystr, ',') . '" name="child_ids">';
                                                                            ?>
		<script type="text/javascript">
            function manageChildAttributes(products_id) {
                var left = (window.screen.width / 2) - 150;
                var top = (window.screen.height / 2) - 200;
                window.open('child_attributes.php?pID=' + products_id + '&action=new_product', '1444818673688', "status=no,height=350,width=800,resizable=yes,left=" + left + ",top=" + top + ",screenX=" + left + ",screenY=" + top + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no");
                return false;
            }
			
			function manageAmazonVariations(products_id,variation_theme_id) {
                var left = (window.screen.width / 2) - 150;
                var top = (window.screen.height / 2) - 200;
                window.open('manage_amazon_variations.php?variation_theme_id='+variation_theme_id+'&pID=' + products_id + '&action=new_variations', '1444818673688', "status=no,height=350,width=800,resizable=yes,left=" + left + ",top=" + top + ",screenX=" + left + ",screenY=" + top + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no");
                return false;
            }
        </script>
<!-- added on 05-05-2016 #start -->
<tr>
	<td> <strong>Select Amazon Theme:</strong>  <?php 
		$amazon_theme_query = tep_db_query("select * from amazon_variation_themes order by variation_theme_name	ASC");
		
		$amazon_theme_array = array(array('id' => '', 'text' => TEXT_NONE));
		
		while($amazon_theme = tep_db_fetch_array($amazon_theme_query)){
			
			$amazon_theme_array[] = array(
										'id' => $amazon_theme['variation_theme_id'],
										'text' => $amazon_theme['variation_theme_name']
									 );
		}
		
		echo tep_draw_pull_down_menu('variation_theme_id', $amazon_theme_array, $pInfo->variation_theme_id, ' onchange="setAmazonTheme(this.value,'.(int)$_GET['pID'].')" '); ?>
        
        <script type="text/javascript">
        	function setAmazonTheme(val,pID){
				
				if(!confirm("Warning: changing theme will reset all variation data associated with child products.\n Are you sure you want to do this?")){
					return false;
				}
				
				jQuery.ajax({
								
					url: 'categories.php',
					type: 'post',
					data: "mode=setAmazonTheme&pID="+pID+"&val="+val,
					dataType: 'json',
					beforeSend: function() {
						jQuery('#amazon_theme_message').hide().fadeIn('slow').html('<img src="images/ajax-loader.gif">');
					},
					success: function(json) {
						if (json['success']) {
							jQuery('#amazon_theme_message').hide().fadeIn('slow').html('<img src="images/tick.gif">');
							showaddedProducts();
						}
					}
				
				});
			}
        </script>
        <span id="amazon_theme_message"></span>
        <br><span style="color:#F00;">Warning: changing theme will reset all variation data associated with child products</span>
        
        </td>
    <td>&nbsp;
        
    </td>
</tr>
<!-- added on 05-05-2016 #ends -->
<tr>
	<td> Add Edit Child Products: </td>
    <td><?php echo '<input onclick="popupWindow(\'' . tep_href_link('add_edit_child_products.php', (!empty($_GET['pID']) ? 'pID=' . $_GET['pID'] . '&model=' . $pInfo->products_model : '') . '&linkchild=true') . '\')" type="button" value="Add/Edit Child" name="edit_child">';
        ?>
    </td>
</tr>
                                                                            <tr>
                                                                                <td><div id="childprod">
                                                                                        <?php echo $childProductStr; ?></div>
                                                                                    <?php echo $keystr; ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><?php
                                                                                    echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                    ?></td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                        //parent-child 25Feb2014 (MA) EOF
                                                                        ?>
                                                                    </table>
                                                                </div>
                                                                <div id="tabs-9">
                                                                    <table class="table table-bordered table-hover">
                                                                        <!-- AJAX Attribute Manager  -->
                                                                        <tr>
                                                                            <td>
                                                                                <?php
                                                                                include('attributeManager/includes/attributeManagerPlaceHolder.inc.php');
                                                                                ?>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <!-- AJAX Attribute Manager end -->
                                                                        <tr>
                                                                            <td colspan="2"><?php
                                                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                                                ?></td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table></td>
                                        </tr>
                                        <tr>
                                            <td><?php
                                                echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                ?></td>
                                        </tr>
                                        <tr>
                                            <td align="right">
                                                <?php
                                                // have two different submit buttons - OBN
                                                echo '<input border="0" type="image" title=" Quick Update " value="Update_without_preview" alt="Quick Update" src="includes/languages/english/images/buttons/button_quick_update.gif" name="Update_without_preview" id="btn_without_prev" />';
                                                echo '&nbsp;&nbsp';
                                                echo '<input type="hidden" value="' . $HTTP_GET_VARS['pID'] .
                                                '" name="Update_pID">';
                                                echo tep_draw_hidden_field('products_date_added', (tep_not_null($pInfo->
                                                                products_date_added) ? $pInfo->products_date_added : date('Y-m-d')));
                                                echo tep_image_submit('button_preview_b.gif', IMAGE_PREVIEW) . '&nbsp;&nbsp;';
                                                echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . (isset
                                                                ($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '')) . '">';
                                                echo tep_image_button('button_cancel_b.gif', IMAGE_CANCEL) . '</a>';
                                                ?>
                                            </td>
                                        </tr>
                                    </table></form>
                                    <?php
                                } elseif ($action == 'new_product_preview') {
                                    // begin bundled products
                                    function display_bundle($bundle_id, $bundle_price, $lid) {
                                        global $pInfo, $currencies, $HTTP_POST_VARS, $HTTP_GET_VARS;
                                        ?>
                                        <table class="table table-bordered table-hover">
                                            <tr class="menuBoxContent">
                                                <td>
                                                    <table class="table table-bordered table-hover">
                                                        <tr>
                                                            <td colspan="5"><b>
                                                                    <?php
                                                                    $bundle_sum = 0;
                                                                    $bdata = array();
                                                                    echo TEXT_PRODUCTS_BY_BUNDLE . "</b></td></tr>\n";
                                                                    if ((isset($HTTP_GET_VARS['read']) && ($HTTP_GET_VARS['read'] == 'only')) || (isset
                                                                                    ($HTTP_GET_VARS['pID']) && ($HTTP_GET_VARS['pID'] != $bundle_id)) || (!isset($HTTP_GET_VARS['pID']) &&
                                                                            is_numeric($bundle_id))) {
                                                                        $bundle_query = tep_db_query(" SELECT pd.products_name, pb.*, p.products_bundle, p.products_id, p.products_model, p.products_price, p.products_image FROM " .
                                                                                TABLE_PRODUCTS . " p INNER JOIN " . TABLE_PRODUCTS_DESCRIPTION .
                                                                                " pd ON p.products_id=pd.products_id INNER JOIN " . TABLE_PRODUCTS_BUNDLES .
                                                                                " pb ON pb.subproduct_id=pd.products_id WHERE pb.bundle_id = " . (int) $bundle_id .
                                                                                " and language_id = '" . (int) $lid . "'");
                                                                        while ($bundle_data = tep_db_fetch_array($bundle_query)) {
                                                                            $bdata[] = $bundle_data;
                                                                        }
                                                                    } else {
                                                                        for ($i = 0, $n = 100; $i < $n; $i++) {
                                                                            if (isset($HTTP_POST_VARS['subproduct_' . $i . '_qty']) && $HTTP_POST_VARS['subproduct_' .
                                                                                    $i . '_qty'] > 0) {
                                                                                $tmp = array(
                                                                                    'bundle_id' => $bundle_id,
                                                                                    'subproduct_id' => (int) $HTTP_POST_VARS['subproduct_' . $i . '_id'],
                                                                                    'subproduct_qty' => (int) $HTTP_POST_VARS['subproduct_' . $i . '_qty']);
                                                                                $bundle_query = tep_db_query(" SELECT pd.products_name, p.products_bundle, p.products_id, p.products_model, p.products_price, p.products_image FROM " .
                                                                                        TABLE_PRODUCTS . " p INNER JOIN " . TABLE_PRODUCTS_DESCRIPTION .
                                                                                        " pd ON p.products_id=pd.products_id WHERE p.products_id = " . (int) $HTTP_POST_VARS['subproduct_' .
                                                                                        $i . '_id'] . " and language_id = '" . (int) $lid . "'");
                                                                                while ($bundle_data = tep_db_fetch_array($bundle_query)) {
                                                                                    $bdata[] = array_merge($tmp, $bundle_data);
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                    foreach ($bdata as $bundle_data) {
                                                                        echo "<tr><td>";
                                                                       if(file_exists(DIR_WS_CATALOG_IMAGES . $bundle_data['products_image'])){
                                                                        echo tep_image(DIR_WS_CATALOG_IMAGES . $bundle_data['products_image'], $bundle_data['products_name'], intval(SMALL_IMAGE_WIDTH / 2), intval(SMALL_IMAGE_HEIGHT / 2), 'hspace="1" vspace="1"');
}
echo '</td>';
                                                                        // comment out the following line to hide the subproduct qty
                                                                        echo "<td class=main align=right><b>" . $bundle_data['subproduct_qty'] .
                                                                        "&nbsp;x&nbsp;</b></td>";
                                                                        echo '<td class=main><a href="' . tep_catalog_href_link('product_info.php', 'products_id=' . (int) $bundle_data['products_id']) .
                                                                        '" target="_blank"><b>&nbsp;(' . $bundle_data['products_model'] . ') ' . $bundle_data['products_name'] .
                                                                        '</b></a>';
                                                                        if ($bundle_data['products_bundle'] == "yes")
                                                                            display_bundle($bundle_data['subproduct_id'], $bundle_data['products_price'], $lid);
                                                                        echo '</td>';
                                                                        echo '<td align=right class=main><b>&nbsp;' . $currencies->display_price($bundle_data['products_price'], tep_get_tax_rate($pInfo->products_tax_class_id)) . "</b></td></tr>\n";
                                                                        $bundle_sum += $bundle_data['products_price'] * $bundle_data['subproduct_qty'];
                                                                    }
                                                                    $bundle_saving = $bundle_sum - $bundle_price;
                                                                    $bundle_sum = $currencies->display_price($bundle_sum, tep_get_tax_rate($pInfo->
                                                                                    products_tax_class_id));
                                                                    $bundle_saving = $currencies->display_price($bundle_saving, tep_get_tax_rate($pInfo->
                                                                                    products_tax_class_id));
                                                                    // comment out the following line to hide the "saving" text
                                                                    echo "<tr><td colspan=5 class=main><p><b>" . TEXT_RATE_COSTS . '&nbsp;' . $bundle_sum .
                                                                    '</b></td></tr><tr><td class=main colspan=5><font color="red"><b>' .
                                                                    TEXT_IT_SAVE . '&nbsp;' . $bundle_saving . "</font></b></td></tr>\n";
                                                                    ?>
                                                    </table></td>
                                            </tr>
                                        </table>
                                        <?php
                                    }
                                    // end bundled products
                                    if (tep_not_null($HTTP_POST_VARS)) {
                                        $pInfo = new objectInfo($HTTP_POST_VARS);
                                        $products_name = $HTTP_POST_VARS['products_name'];
                                        $products_description = $HTTP_POST_VARS['products_description'];
                                        $products_specifications = $HTTP_POST_VARS['products_specifications'];
                                        $products_head_title_tag = $HTTP_POST_VARS['products_head_title_tag'];
                                        $products_tags = $HTTP_POST_VARS['products_tags'];
                                        $products_head_desc_tag = $HTTP_POST_VARS['products_head_desc_tag'];
                                        $products_head_keywords_tag = $HTTP_POST_VARS['products_head_keywords_tag'];
                                        $products_url = $HTTP_POST_VARS['products_url'];
                                    } else {
                                        /*                                         * ******************PRODUCT SIZE MOD BY FIW************************* */
                                        //$product_query = tep_db_query("select p.products_id, pd.language_id, pd.products_name, pd.products_description,pd.products_head_title_tag, pd.products_head_desc_tag, pd.products_head_keywords_tag, pd.products_url, p.products_quantity, p.products_model, p.products_image, p.products_mediumimage, p.products_largeimage,p.products_price, p.products_weight, p.products_length, p.products_width, p.products_height, p.products_ready_to_ship, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_size, p.disclaimer_needed, p.manufacturers_id, p.free_shipping, p.in_store_pickup, p.lock_price,p.hide_price  from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and p.products_id = '" . (int)$HTTP_GET_VARS['pID'] . "'");
                                        //BOF:range_manager
                                        /*
                                          //EOF:range_manager
                                          $product_query = tep_db_query("select p.products_id, pd.language_id, pd.products_name, pd.products_description,pd.products_head_title_tag, pd.products_head_desc_tag, pd.products_head_keywords_tag, pd.products_url, p.products_quantity, p.store_quantity, p.products_model, p.products_image, p.products_mediumimage, p.products_largeimage,p.products_price, p.products_weight, p.products_length, p.products_width, p.products_height, p.products_ready_to_ship, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_size, p.disclaimer_needed, p.manufacturers_id, p.free_shipping, p.in_store_pickup, p.lock_price,p.hide_price  from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and p.products_id = '" . (int)$HTTP_GET_VARS['pID'] . "'");
                                          //BOF:range_manager
                                         */
                                        //MVS
                                        $product_query = tep_db_query("select pd.products_tags, p.products_id, pd.language_id, pd.products_name, pd.products_description, pd.products_specifications, pd.products_head_title_tag, pd.products_head_desc_tag, pd.products_head_keywords_tag, pd.products_url, p.products_quantity, p.store_quantity, p.products_model, p.products_image, p.products_mediumimage, p.products_largeimage,p.products_price, p.products_weight, p.products_length, p.products_width, p.products_height, p.products_ready_to_ship, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_size, p.disclaimer_needed, p.manufacturers_id, p.free_shipping, p.lock_status, p.lock_title, p.lock_specs, p.is_store_item, p.in_store_pickup, p.lock_price,p.hide_price, p.is_lane_item, p.range_id, p.lane_id, p.products_bundle, p.sold_in_bundle_only,p.vendors_product_price, p.vendors_prod_comments, p.vendors_id,p.variation_theme_id  from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and p.products_id = '" . (int) $HTTP_GET_VARS['pID'] . "'");
                                        //EOF:range_manager
                                        /*                                         * ******************PRODUCT SIZE MOD BY FIW************************* */
                                        $product = tep_db_fetch_array($product_query);
                                        $pInfo = new objectInfo($product);
                                        $products_image_name = $pInfo->products_image;
                                    }
                                    $form_action = (isset($HTTP_GET_VARS['pID'])) ? 'update_product' :
                                            'insert_product';
                                    echo tep_draw_form($form_action, FILENAME_CATEGORIES, 'cPath=' . $cPath . (isset
                                                    ($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '') . '&action=' . $form_action, 'post', 'enctype="multipart/form-data"');
                                    $languages = tep_get_languages();
                                    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                        if (isset($HTTP_GET_VARS['read']) && ($HTTP_GET_VARS['read'] == 'only')) {
                                            $pInfo->products_name = tep_get_products_name($pInfo->products_id, $languages[$i]['id']);
                                            $pInfo->products_description = tep_get_products_description($pInfo->products_id, $languages[$i]['id']);
                                            $pInfo->products_specifications = tep_get_products_specifications($pInfo->
                                                    products_id, $languages[$i]['id']);
                                            $pInfo->products_head_title_tag = tep_db_prepare_input($products_head_title_tag[$languages[$i]['id']]);
                                            $pInfo->products_tags = tep_db_prepare_input($products_tags[$languages[$i]['id']]);
                                            $pInfo->products_head_desc_tag = tep_db_prepare_input($products_head_desc_tag[$languages[$i]['id']]);
                                            $pInfo->products_head_keywords_tag = tep_db_prepare_input($products_head_keywords_tag[$languages[$i]['id']]);
                                            $pInfo->products_url = tep_get_products_url($pInfo->products_id, $languages[$i]['id']);
                                        } else {
                                            $pInfo->products_name = tep_db_prepare_input($products_name[$languages[$i]['id']]);
                                            $pInfo->products_description = tep_db_prepare_input($products_description[$languages[$i]['id']]);
                                            $pInfo->products_specifications = tep_db_prepare_input($products_specifications[$languages[$i]['id']]);
                                            $pInfo->products_head_title_tag = tep_db_prepare_input($products_head_title_tag[$languages[$i]['id']]);
                                            $pInfo->products_tags = tep_db_prepare_input($products_tags[$languages[$i]['id']]);
                                            $pInfo->products_head_desc_tag = tep_db_prepare_input($products_head_desc_tag[$languages[$i]['id']]);
                                            $pInfo->products_head_keywords_tag = tep_db_prepare_input($products_head_keywords_tag[$languages[$i]['id']]);
                                            $pInfo->products_url = tep_db_prepare_input($products_url[$languages[$i]['id']]);
                                        }
                                        ?>
                                        <table class="table table-bordered table-hover">
                                            <?php if (!empty($HTTP_GET_VARS['pID']) && $HTTP_GET_VARS['read'] == 'only') { ?>
                                                <tr>
                                                    <td>
                                                        <iframe  src="<?php echo HTTPS_CATALOG_SERVER . '/product_info.php?products_id=' . (int) $HTTP_GET_VARS['pID']; ?>" style="background-color: white; width:100%; min-height:600px;"></iframe>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <tr>
                                                <td>
                                                    <table class="table table-bordered table-hover">
                                                        <tr>
                                                            <td><?php
                                                                echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                        '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $pInfo->
                                                                products_name;
                                                                ?></td>
                                                            <?php /*  //MVS start?>
                                                              <td class="pageHeading" align="right"><?php echo TEXT_VENDORS_PRODUCT_PRICE_TITLE . $currencies->format($pInfo->products_price); ?></td>
                                                              <td class="pageHeading" align="right"><?php echo TEXT_VENDORS_PRICE_TITLE . $currencies->format($pInfo->vendors_product_price); ?></td>
                                                              </tr>
                                                              <?php */ //MVS end ?>
                                                            <td>
                                                                <?php
                                                                // Old Price - OBN
                                                                //		echo $currencies->format($pInfo->products_price);
                                                                if ($HTTP_POST_VARS['manual_price'] > 0.0000)
                                                                    echo $currencies->format($HTTP_POST_VARS['manual_price']);
                                                                else
                                                                    echo $currencies->format($HTTP_POST_VARS['base_price']);
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><br /></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td><?php
                                                    echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                    ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?php
                                                    echo '<img src="' . $products_image_name .
                                                    '" align="right" hspace="5" vspace="5" title="' . $pInfo->products_name .
                                                    ' alt="' . $pInfo->products_name . '">' . $pInfo->products_description;
                                                    // Old display image - OBN
                                                    //echo tep_image($products_image_name, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'align="right" hspace="5" vspace="5"') . $pInfo->products_description;
                                                    ?></td>
                                            </tr>
                                            <!-- BOF Bundled Products-->          
                                            <tr>
                                                <td>
                                                    <?php
                                                    $pid = (isset($HTTP_GET_VARS['pID']) ? $HTTP_GET_VARS['pID'] : $pInfo->
                                                                    products_id);
                                                    if ($pInfo->products_bundle == "yes") {
                                                        display_bundle($pid, $pInfo->products_price, $languages[$i]['id'], $languages[$i]['directory']);
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?php
                                                    if ($pInfo->sold_in_bundle_only == "yes") {
                                                        echo '<b>' . TEXT_SOLD_IN_BUNDLE . '</b><blockquote>';
                                                        $bquery = tep_db_query('select bundle_id from ' . TABLE_PRODUCTS_BUNDLES .
                                                                ' where subproduct_id = ' . (int) $pid);
                                                        while ($bid = tep_db_fetch_array($bquery)) {
                                                            $binfo_query = tep_db_query('select p.products_model, pd.products_name from ' .
                                                                    TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .
                                                                    " pd where p.products_id = '" . (int) $bid['bundle_id'] .
                                                                    "' and pd.products_id = p.products_id and pd.language_id = " . (int) $languages[$i]['id']);
                                                            $binfo = tep_db_fetch_array($binfo_query);
                                                            echo '<a href="' . tep_catalog_href_link('product_info.php', 'products_id=' . (int)
                                                                    $bid['bundle_id']) . '" target="_blank">[' . $binfo['products_model'] . '] ' . $binfo['products_name'] .
                                                            '</a><br />';
                                                        }
                                                        echo '</blockquote>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <!-- EOF Bundled Products-->
                                            <tr>
                                                <td><?php
                                                    echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                    ?></td>
                                            </tr>
                                            <?php
                                            if ($pInfo->products_url) {
                                                ?>
                                                <tr>
                                                    <td><?php
                                                        echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                        ?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php
                                                        echo sprintf(TEXT_PRODUCT_MORE_INFORMATION, $pInfo->products_url);
                                                        ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr>
                                                <td><?php
                                                    echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                    ?></td>
                                            </tr>
                                            <?php
                                            if ($pInfo->products_date_available > date('Y-m-d')) {
                                                ?>
                                                <tr>
                                                    <td><?php
                                                        echo sprintf(TEXT_PRODUCT_DATE_AVAILABLE, tep_date_long($pInfo->
                                                                        products_date_available));
                                                        ?></td>
                                                </tr>
                                                <?php
                                            } else {
                                                ?>
                                                <tr>
                                                    <td><?php
                                                        echo sprintf(TEXT_PRODUCT_DATE_ADDED, tep_date_long($pInfo->products_date_added));
                                                        ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr>
                                                <td><?php
                                                    echo tep_draw_separator('pixel_trans.gif', '1', '10');
                                                    ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if (isset($HTTP_GET_VARS['read']) && ($HTTP_GET_VARS['read'] == 'only')) {
                                            if (isset($HTTP_GET_VARS['origin'])) {
                                                $pos_params = strpos($HTTP_GET_VARS['origin'], '?', 0);
                                                if ($pos_params != false) {
                                                    $back_url = substr($HTTP_GET_VARS['origin'], 0, $pos_params);
                                                    $back_url_params = substr($HTTP_GET_VARS['origin'], $pos_params + 1);
                                                } else {
                                                    $back_url = $HTTP_GET_VARS['origin'];
                                                    $back_url_params = '';
                                                }
                                            } else {
                                                $back_url = FILENAME_CATEGORIES;
                                                $back_url_params = 'cPath=' . $cPath . '&pID=' . $pInfo->products_id;
                                            }
                                            ?>
                                            <tr>
                                                <td><?php
                                                    echo '<a href="' . tep_href_link($back_url, $back_url_params, 'NONSSL') . '">' .
                                                    tep_image_button('button_back.gif', IMAGE_BACK) . '</a>';
                                                    ?></td>
                                            </tr>
                                            <?php
                                        } else {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    /* Re-Post all POST'ed variables */
                                                    reset($HTTP_POST_VARS);
                                                    while (list($key, $value) = each($HTTP_POST_VARS)) {
                                                        //   if (!is_array($HTTP_POST_VARS[$key])) {
                                                        // BOF Separate Pricing per Customer
                                                        if (is_array($value)) {
                                                            while (list($k, $v) = each($value)) {
                                                                echo tep_draw_hidden_field($key . '[' . $k . ']', htmlspecialchars(stripslashes
                                                                                        ($v)));
                                                            }
                                                        } else {
                                                            // EOF Separate Pricing per Customer
                                                            echo tep_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
                                                        }
                                                    }
                                                    $languages = tep_get_languages();
                                                    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                                        echo tep_draw_hidden_field('products_name[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_name[$languages[$i]['id']])));
                                                        echo tep_draw_hidden_field('products_description[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_description[$languages[$i]['id']])));
                                                        echo tep_draw_hidden_field('products_specifications[' . $languages[$i]['id'] .
                                                                ']', htmlspecialchars(stripslashes($products_specifications[$languages[$i]['id']])));
                                                        echo tep_draw_hidden_field('products_tags[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_tags[$languages[$i]['id']])));
                                                        echo tep_draw_hidden_field('products_head_title_tag[' . $languages[$i]['id'] .
                                                                ']', htmlspecialchars(stripslashes($products_head_title_tag[$languages[$i]['id']])));
                                                        echo tep_draw_hidden_field('products_head_desc_tag[' . $languages[$i]['id'] .
                                                                ']', htmlspecialchars(stripslashes($products_head_desc_tag[$languages[$i]['id']])));
                                                        echo tep_draw_hidden_field('products_head_keywords_tag[' . $languages[$i]['id'] .
                                                                ']', htmlspecialchars(stripslashes($products_head_keywords_tag[$languages[$i]['id']])));
                                                        echo tep_draw_hidden_field('products_url[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_url[$languages[$i]['id']])));
                                                    }
                                                    echo tep_draw_hidden_field('products_image', stripslashes($products_image_name));
                                                    echo tep_draw_hidden_field('products_mediumimage', stripslashes($products_mediumimage_name));
                                                    echo tep_draw_hidden_field('products_largeimage', stripslashes($products_largeimage_name));
                                                    echo tep_draw_hidden_field('products_largeimage', stripslashes($products_largeimage_name));
                                                    echo tep_draw_hidden_field('flag_prod_qty', $flag_prod_qty);
                                                    echo tep_draw_hidden_field('flag_prod_price', $flag_prod_price);
                                                    echo tep_draw_hidden_field('flag_prod_cat', $flag_prod_cat);
                                                    echo tep_draw_hidden_field('flag_prod_desc', $flag_prod_desc);
                                                    echo tep_draw_hidden_field('flag_prod_image', $flag_prod_image);
                                                    echo tep_draw_hidden_field('products_image_2', stripslashes($products_image_2_name));
                                                    echo tep_draw_hidden_field('products_image_3', stripslashes($products_image_3_name));
                                                    echo tep_draw_hidden_field('products_image_4', stripslashes($products_image_4_name));
                                                    echo tep_draw_hidden_field('products_image_5', stripslashes($products_image_5_name));
                                                    echo tep_draw_hidden_field('products_image_6', stripslashes($products_image_6_name));
                                                    echo tep_image_submit('button_back.gif', IMAGE_BACK, 'name="edit"') .
                                                    '&nbsp;&nbsp;';
                                                    if (isset($HTTP_GET_VARS['pID'])) {
                                                        echo tep_image_submit('button_update.gif', IMAGE_UPDATE);
                                                    } else {
                                                        echo tep_image_submit('button_insert.gif', IMAGE_INSERT);
                                                    }
                                                    echo '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .
                                                            (isset($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '')) . '">' .
                                                    tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>';
                                                    ?></td>
                                            </tr>
                                        </table></form>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <table class="table table-bordered table-hover">
                                        <tr>
                                            <td><table class="table table-bordered table-hover">
                                                    <tr>
                                                        <td><?php
                                                            echo HEADING_TITLE;
                                                            ?></td>
                                                        <td><?php
                                                            echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT);
                                                            ?></td>
                                                        <td><table class="table table-bordered table-hover">
                                                                <tr>
                                                                    <td>
                                                                        <?php
                                                                        echo tep_draw_form('search', FILENAME_CATEGORIES, '', 'get');
                                                                        echo '<span>' . HEADING_TITLE_SEARCH . ' ' .
                                                                        tep_draw_input_field('search') . "</span>";
                                                                        echo '</form>';
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <?php
                                                                        echo tep_draw_form('goto', FILENAME_CATEGORIES, '', 'get');
                                                                        echo '<span>' . HEADING_TITLE_GOTO . ' ' .
                                                                        tep_draw_pull_down_menu('cPath', tep_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"') . "</span>";
                                                                        echo '</form>';
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            </table></td>
                                                    </tr>
                                                </table></td>
                                        </tr>
                                        <tr>
                                            <td><table class="table table-bordered table-hover">
                                                    <tr>
                                                        <td valign="top">
                                                            <!-- BOF: bulk_category_movement -->
                                                            <form name="move_bulk_to_category" method="post" action="<?php
                                                            echo FILENAME_CATEGORIES . '?' . tep_get_all_get_params(array('action')) .
                                                            'action=move_bulk_to_category';
                                                            ?>" >
                                                                <!-- EOF: bulk_category_movement -->
                                                                <table class="table table-bordered table-hover">
                                                                    <tr>
                                                                        <td width="300px"><?php
                                                                            echo TABLE_HEADING_CATEGORIES_PRODUCTS;
                                                                            ?></td>
                                                                        <?php
                                                                        //BOF:category_group
                                                                        ?>
                                                                        <td><?php
                                                                            echo 'Is Cat Grp';
                                                                            ?></td>
                                                                        <?php
                                                                        //BOF:category_group
                                                                        ?>
                                                                        <td><?php
                                                                            echo TABLE_HEADING_STATUS;
                                                                            ?></td>
                                                                        <?php
                                                                        //BOF:amazon_integration
                                                                        ?>
                                                                        <td><?php
                                                                            echo 'Amazon Status';
                                                                            ?></td>
                                                                        <?php
                                                                        //EOF:amazon_integration
                                                                        ?>
                                                                        <?php
                                                                        //BOF:ebay_integration
                                                                        ?>
                                                                        <td><?php
                                                                            echo 'eBay Status';
                                                                            ?></td>
                                                                        <?php
                                                                        //EOF:ebay_integration
                                                                        ?>
                                                                        <!-- BOF: bulk_category_movement -->
                                                                        <td><input type="checkbox" onClick="bulk_selection(this);" />&nbsp;</td>
                                                                        <!-- EOF: bulk_category_movement -->
                                                                        <td><?php
                                                                            echo TABLE_HEADING_ACTION;
                                                                            ?>&nbsp;</td>
                                                                    </tr>
                                                                    <?php
                                                                    $categories_count = 0;
                                                                    $rows = 0;
                                                                    if (isset($HTTP_GET_VARS['search'])) {
                                                                        $search = tep_db_prepare_input($HTTP_GET_VARS['search']);
                                                                        //Categry Status MOD BEGIN by FIW
                                                                        //BOF:category_group
                                                                        //BOF:amazon_integration
                                                                        /*
                                                                          //EOF:amazon_integration
                                                                          $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.banner_image, c.parent_id, c.sort_order, c.categories_status, c.date_added, c.last_modified, cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_htc_description from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and cd.categories_name like '%" . tep_db_input($search) . "%' order by c.sort_order, cd.categories_name");
                                                                          //BOF:amazon_integration / eBay_integration
                                                                         */
                                                                        $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.banner_image, c.parent_id, c.sort_order, c.categories_status, c.date_added, c.last_modified, cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_htc_description, c.is_amazon_ok, c.is_ebay_ok from " .
                                                                                TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION .
                                                                                " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int) $languages_id .
                                                                                "' and cd.categories_name like '%" . tep_db_input($search) .
                                                                                "%' order by c.sort_order, cd.categories_name");
                                                                        //EOF:amazon_integration
                                                                        //$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.banner_image, c.parent_id, c.sort_order, c.categories_status, c.date_added, c.last_modified, c.is_category_group, cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_htc_description from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and cd.categories_name like '%" . tep_db_input($search) . "%' order by c.sort_order, cd.categories_name");
                                                                        //EOF:category_group
                                                                    } else {
                                                                        //BOF:category_group
                                                                        //$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.banner_image, c.parent_id, c.sort_order, c.categories_status, c.date_added, c.last_modified, cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_htc_description from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by c.sort_order, cd.categories_name");
                                                                        //BOF:amazon_integration
                                                                        /*
                                                                          //EOF:amazon_integration
                                                                          $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.banner_image, c.parent_id, c.sort_order, c.categories_status, c.date_added, c.last_modified, c.is_category_group, cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_htc_description from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by c.sort_order, cd.categories_name");
                                                                          //BOF:amazon_integration / ebay_integration
                                                                         */
                                                                        $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.banner_image, c.parent_id, c.sort_order, c.categories_status, c.date_added, c.last_modified, c.is_category_group, cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_htc_description, c.is_amazon_ok, c.is_ebay_ok,c.amazon_category_id from " .
                                                                                TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION .
                                                                                " cd where c.parent_id = '" . (int) $current_category_id .
                                                                                "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int) $languages_id .
                                                                                "' order by c.sort_order, cd.categories_name");
                                                                        //EOF:amazon_integration
                                                                        //EOF:category_group
                                                                        //EOF:category_group
                                                                    }
                                                                    //Categry Status MOD END by FIW
                                                                    while ($categories = tep_db_fetch_array($categories_query)) {
                                                                        $categories_count++;
                                                                        $rows++;
                                                                        // Get parent_id for subcategories if search
                                                                        if (isset($HTTP_GET_VARS['search']))
                                                                            $cPath = $categories['parent_id'];
                                                                        if ((!isset($HTTP_GET_VARS['cID']) && !isset($HTTP_GET_VARS['pID']) || (isset($HTTP_GET_VARS['cID']) &&
                                                                                ($HTTP_GET_VARS['cID'] == $categories['categories_id']))) && !isset($cInfo) && (substr
                                                                                        ($action, 0, 3) != 'new')) {
                                                                            $category_childs = array('childs_count' => tep_childs_in_category_count($categories['categories_id']));
                                                                            $category_products = array('products_count' => tep_products_in_category_count($categories['categories_id']));
                                                                            $cInfo_array = array_merge($categories, $category_childs, $category_products);
                                                                            $cInfo = new objectInfo($cInfo_array);
                                                                            //Categories status MOD bEGIN by FIW
                                                                            if (!isset($cInfo->categories_status))
                                                                                $cInfo->categories_status = '1';
                                                                            switch ($cInfo->categories_status) {
                                                                                case '0':
                                                                                    $in_status = false;
                                                                                    $out_status = true;
                                                                                    break;
                                                                                case '1':
                                                                                default:
                                                                                    $in_status = true;
                                                                                    $out_status = false;
                                                                            }
                                                                            //Categories status MOD END by FIW
                                                                            //BOF:category_box
                                                                            if (!isset($cInfo->is_category_group)) {
                                                                                $cInfo->is_category_group = '0';
                                                                            }
                                                                            switch ($cInfo->is_category_group) {
                                                                                case '1':
                                                                                    $cat_status_set = true;
                                                                                    $cat_status_unset = false;
                                                                                    break;
                                                                                case '0':
                                                                                default:
                                                                                    $cat_status_set = false;
                                                                                    $cat_status_unset = true;
                                                                                    break;
                                                                            }
                                                                            //EOF:category_box
                                                                        }
                                                                        if (isset($cInfo) && is_object($cInfo) && ($categories['categories_id'] == $cInfo->
                                                                                categories_id)) {
                                                                            echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' .
                                                                            tep_href_link(FILENAME_CATEGORIES, tep_get_path($categories['categories_id'])) .
                                                                            '\'">' . "\n";
                                                                        } else {
                                                                            echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' .
                                                                            tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) .
                                                                            '\'">' . "\n";
                                                                        }
                                                                        ?>
                                                                        <td><?php
                                                                            echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, tep_get_path($categories['categories_id'])) .
                                                                            '">' . tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a>&nbsp;<b>' . $categories['categories_name'] .
                                                                            '</b>';
                                                                            ?></td>
                                                                        <?php
                                                                        //BOF:category_group
                                                                        ?>
                                                                        <td><?php
                                                                            echo ($categories['is_category_group'] ? 'Y' : 'N');
                                                                            ?></td>
                                                                        <?php
                                                                        //EOF:category_group
                                                                        ?>
                                                                        <td><?php
                                                                            //Categroies Status MOD BEGIN by FIW
                                                                            if ($categories['categories_status'] == '1') {
                                                                                echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setflagc&flag=0&cPath=' . $cPath) . '&cID=' . $categories['categories_id'] .
                                                                                '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                                                                            } else {
                                                                                echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setflagc&flag=1&cPath=' . $cPath) . '&cID=' . $categories['categories_id'] .
                                                                                '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES .
                                                                                        'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                                                                            }
                                                                            //Categroies Status MOD END by FIW
                                                                            ?></td>
                                                                        <?php
                                                                        //BOF:amazon_integration
                                                                        ?>
                                                                        <td>
                                                                            <?php
                                                                            if ($categories['is_amazon_ok'] == '1') {
                                                                                echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setamazonflagc&amazonflag=0&cPath=' . $cPath) . '&cID=' . $categories['categories_id'] .
                                                                                '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                                                                            } else {
                                                                                echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setamazonflagc&amazonflag=1&cPath=' . $cPath) . '&cID=' . $categories['categories_id'] .
                                                                                '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES .
                                                                                        'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <?php
                                                                        //EOF:amazon_integration
                                                                        ?>
                                                                        <?php
                                                                        //BOF:ebay_integration
                                                                        ?>
                                                                        <td>
                                                                            <?php
                                                                            if ($categories['is_ebay_ok'] == '1') {
                                                                                echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setebayflagc&ebayflag=0&cPath=' . $cPath) . '&cID=' . $categories['categories_id'] .
                                                                                '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                                                                            } else {
                                                                                echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setebayflagc&ebayflag=1&cPath=' . $cPath) . '&cID=' . $categories['categories_id'] .
                                                                                '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES .
                                                                                        'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <?php
                                                                        //EOF:ebay_integration
                                                                        ?>
                                                                        <!-- BOF: bulk_category_movement -->
                                                                        <td>
                                                                            <input type="checkbox" name="cat[]" value="<?php
                                                                            echo $categories['categories_id'];
                                                                            ?>" id="C<?php
                                                                                   echo $categories['categories_id'];
                                                                                   ?>"  />&nbsp;
                                                                            <script type="text/javascript">
                                                                                var elem = document.getElementById('C<?php
                                                                           echo $categories['categories_id'];
                                                                           ?>');
                                                                                elem.onclick = function (e) {
                                                                                    stop_propogation(e);
                                                                                }
                                                                            </script>  	
                                                                        </td>
                                                                        <!-- EOF: bulk_category_movement -->
                                                                        <td><?php
                                                                            if (isset($cInfo) && is_object($cInfo) && ($categories['categories_id'] == $cInfo->
                                                                                    categories_id)) {
                                                                                echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', '');
                                                                            } else {
                                                                                echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .
                                                                                        '&cID=' . $categories['categories_id']) . '">' . tep_image(DIR_WS_IMAGES .
                                                                                        'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                                                                            }
                                                                            ?>&nbsp;</td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                    // Added a search p.products_model to search - OBN
                                                                    $products_count = 0;
                                                                    if (isset($HTTP_GET_VARS['search'])) {
                                                                        //BOF:amazon_integration
                                                                        /*
                                                                          //EOF:amazon_integration
                                                                          $products_query = tep_db_query("select p.products_id, pd.products_name, p.products_quantity, p.store_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_model, p2c.categories_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and (pd.products_name like '%" . tep_db_input($search) . "%' or p.products_model like '%" . tep_db_input($search) . "%') order by pd.products_name");
                                                                          //BOF:amazon_integration
                                                                         */
                                                                        //MVS
                                                                        $products_query = tep_db_query("select p.products_id, pd.products_name, p.products_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_model, p2c.categories_id, p.is_amazon_ok, p.is_ebay_ok, p.parent_products_model, p.vendors_product_price, p.vendors_prod_comments,p.variation_theme_id from " .
                                                                                TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " .
                                                                                TABLE_PRODUCTS_TO_CATEGORIES .
                                                                                " p2c where p.products_id = pd.products_id and pd.language_id = '" . (int) $languages_id .
                                                                                "' and p.products_id = p2c.products_id and (pd.products_name like '%" .
                                                                                tep_db_input($search) . "%' or p.products_model like '%" . tep_db_input($search) .
                                                                                "%') order by pd.products_name");
                                                                        //EOF:amazon_integration
                                                                        // OLD SEARCH
                                                                        //      $products_query = tep_db_query("select p.products_id, pd.products_name, p.products_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_model, p2c.categories_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and pd.products_name like '%" . tep_db_input($search) . "%' order by pd.products_name");
                                                                    } else {
                                                                        //BOF:amazon_integration
                                                                        /*
                                                                          //EOF:amazon_integration
                                                                          $products_query = tep_db_query("select p.products_id, pd.products_name, p.products_quantity, p.store_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_model from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$current_category_id . "' order by pd.products_name");
                                                                          //BOF:amazon_integration
                                                                         */
                                                                        //MVS
                                                                        $products_query = tep_db_query("select p.products_id, pd.products_name, p.products_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_model, p.is_amazon_ok, p.is_ebay_ok, p.parent_products_model, p.vendors_product_price, p.vendors_prod_comments, p.store_quantity,p.variation_theme_id from " .
                                                                                TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " .
                                                                                TABLE_PRODUCTS_TO_CATEGORIES .
                                                                                " p2c where p.products_id = pd.products_id and pd.language_id = '" . (int) $languages_id .
                                                                                "' and p.products_id = p2c.products_id and p2c.categories_id = '" . (int) $current_category_id .
                                                                                "' order by pd.products_name");
                                                                        //EOF:amazon_integration
                                                                    }
                                                                    while ($products = tep_db_fetch_array($products_query)) {
                                                                        $products_count++;
                                                                        $rows++;
                                                                        // Get categories_id for product if search
                                                                        if (isset($HTTP_GET_VARS['search']))
                                                                            $cPath = $products['categories_id'];
                                                                        if ((!isset($HTTP_GET_VARS['pID']) && !isset($HTTP_GET_VARS['cID']) || (isset($HTTP_GET_VARS['pID']) &&
                                                                                ($HTTP_GET_VARS['pID'] == $products['products_id']))) && !isset($pInfo) && !
                                                                                isset($cInfo) && (substr($action, 0, 3) != 'new')) {
                                                                            // find out the rating average from customer reviews
                                                                            $reviews_query = tep_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from " .
                                                                                    TABLE_REVIEWS . " where products_id = '" . (int) $products['products_id'] . "'");
                                                                            $reviews = tep_db_fetch_array($reviews_query);
                                                                            $pInfo_array = array_merge($products, $reviews);
                                                                            $pInfo = new objectInfo($pInfo_array);
                                                                        }
                                                                        if (isset($pInfo) && is_object($pInfo) && ($products['products_id'] == $pInfo->
                                                                                products_id)) {
                                                                            echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' .
                                                                            tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $products['products_id'] .
                                                                                    '&action=new_product_preview&read=only') . '\'">' . "\n";
                                                                        } else {
                                                                            echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' .
                                                                            tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $products['products_id']) .
                                                                            '\'">' . "\n";
                                                                        }
                                                                        ?>
                                                                        <td><?php
                                                                            echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .
                                                                                    '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only') .
                                                                            '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . (!
                                                                            empty($products['parent_products_model']) ? '&nbsp;&nbsp;&nbsp;' : '') . $products['products_name'];
                                                                            ?></td>
                                                                        <?php
                                                                        //BOF:category_group
                                                                        ?>
                                                                        <td><?php
                                                                            echo '--';
                                                                            ?></td>
                                                                        <?php
                                                                        //EOF:category_group
                                                                        ?>
                                                                        <td>
                                                                            <?php
                                                                            if ($products['products_status'] == '1') {
                                                                                echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) .
                                                                                '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                                                                            } else {
                                                                                echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) .
                                                                                '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES .
                                                                                        'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                                                                            }
                                                                            ?></td>
                                                                        <?php
                                                                        //BOF:amazon_integration
                                                                        ?>
                                                                        <td>
                                                                            <?php
                                                                            if ($products['is_amazon_ok'] == '1') {
                                                                                echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setamazonflag&amazonflag=0&pID=' . $products['products_id'] . '&cPath=' .
                                                                                        $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                                                                            } else {
                                                                                echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setamazonflag&amazonflag=1&pID=' . $products['products_id'] . '&cPath=' .
                                                                                        $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES .
                                                                                        'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <?php
                                                                        //EOF:amazon_integration
                                                                        ?>
                                                                        <?php
                                                                        //BOF:ebay_integration
                                                                        ?>
                                                                        <td>
                                                                            <?php
                                                                            if ($products['is_ebay_ok'] == '1') {
                                                                                echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setebayflag&ebayflag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) .
                                                                                '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                                                                            } else {
                                                                                echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setebayflag&ebayflag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) .
                                                                                '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES .
                                                                                        'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <?php
                                                                        //EOF:ebay_integration
                                                                        ?>
                                                                        <!-- BOF: bulk_category_movement -->
                                                                        <td>
                                                                            <input type="checkbox" name="prod[]" value="<?php
                                                                            echo $products['products_id'];
                                                                            ?>" id="P<?php
                                                                                   echo $products['products_id'];
                                                                                   ?>" />&nbsp;
                                                                            <script type="text/javascript">
                                                                                var elem = document.getElementById('P<?php
                                                                           echo $products['products_id'];
                                                                           ?>');
                                                                                elem.onclick = function (e) {
                                                                                    stop_propogation(e);
                                                                                }
                                                                            </script>
                                                                        </td>
                                                                        <!-- EOF: bulk_category_movement -->
                                                                        <td><?php
                                                                            if (isset($pInfo) && is_object($pInfo) && ($products['products_id'] == $pInfo->
                                                                                    products_id)) {
                                                                                echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', '');
                                                                            } else {
                                                                                echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .
                                                                                        '&pID=' . $products['products_id']) . '">' . tep_image(DIR_WS_IMAGES .
                                                                                        'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                                                                            }
                                                                            ?>&nbsp;</td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                    $cPath_back = '';
                                                                    if (sizeof($cPath_array) > 0) {
                                                                        for ($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
                                                                            if (empty($cPath_back)) {
                                                                                $cPath_back .= $cPath_array[$i];
                                                                            } else {
                                                                                $cPath_back .= '_' . $cPath_array[$i];
                                                                            }
                                                                        }
                                                                    }
                                                                    $cPath_back = (tep_not_null($cPath_back)) ? 'cPath=' . $cPath_back . '&' : '';
                                                                    ?>
                                                                    <!-- BOF: bulk_category_movement -->
                                                                    <tr>
                                                                        <td colspan="6" align="right">
                                                                            <?php
                                                                            echo '<span>Lock Category: </span> ' .
                                                                            tep_draw_checkbox_field('category_flag', '0', true) . '<br><br>';
                                                                            ?>
                                                                            <span>Move selected products/categories to:</span> <?php
                                                                            echo tep_draw_pull_down_menu('move_selection_to_category_id', tep_get_category_tree(), $current_category_id, 'onchange="javascript:this.form.submit();"');
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                    <!-- EOF: bulk_category_movement -->
                                                                    <tr>
                                                                        <td colspan="6"><table class="table table-bordered table-hover">
                                                                                <tr>
                                                                                    <td><?php
                                                                                        echo TEXT_CATEGORIES . '&nbsp;' . $categories_count . '<br>' . TEXT_PRODUCTS .
                                                                                        '&nbsp;' . $products_count;
                                                                                        ?></td>
                                                                                    <td><?php
                                                                                        if (sizeof($cPath_array) > 0)
                                                                                            echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, $cPath_back . 'cID=' . $current_category_id) .
                                                                                            '">' . tep_image_button('button_back_b.gif', IMAGE_BACK) . '</a>&nbsp;';
                                                                                        if (!isset($HTTP_GET_VARS['search']))
                                                                                            echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .
                                                                                                    '&action=new_category') . '">' . tep_image_button('button_new_category.gif', IMAGE_NEW_CATEGORY) . '</a>&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&action=new_product') . '">' . tep_image_button('button_new_product.gif', IMAGE_NEW_PRODUCT) . '</a>';
                                                                                        ?>&nbsp;</td>
                                                                                </tr>
                                                                            </table></td>
                                                                    </tr>
                                                                </table>
                                                                <!-- BOF: bulk_category_movement -->
                                                            </form>
                                                            <!-- EOF: bulk_category_movement -->
                                                        </td>
                                                        <?php
                                                        $heading = array();
                                                        $contents = array();
                                                        switch ($action) {
                                                            case 'new_category':
                                                                $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_CATEGORY . '</b>');
                                                                $contents = array('form' => tep_draw_form('newcategory', FILENAME_CATEGORIES, 'action=insert_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"'));
                                                                $contents[] = array('text' => TEXT_NEW_CATEGORY_INTRO);
                                                                $category_inputs_string = '';
                                                                $languages = tep_get_languages();
                                                                for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                                                    $category_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                                    '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .
                                                                            tep_draw_input_field('categories_name[' . $languages[$i]['id'] . ']');
                                                                    // HTC BOC
                                                                    $category_htc_title_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                                    '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .
                                                                            tep_draw_input_field('categories_htc_title_tag[' . $languages[$i]['id'] . ']');
                                                                    $category_htc_desc_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                                    '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .
                                                                            tep_draw_input_field('categories_htc_desc_tag[' . $languages[$i]['id'] . ']');
                                                                    $category_htc_keywords_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                                    '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .
                                                                            tep_draw_input_field('categories_htc_keywords_tag[' . $languages[$i]['id'] . ']');
                                                                    $category_htc_description_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES .
                                                                                    $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) .
                                                                            '&nbsp;' . tep_draw_textarea_field('categories_htc_description[' . $languages[$i]['id'] .
                                                                                    ']', 'hard', 30, 5, '');
                                                                    // HTC EOC
                                                                }
                                                                $contents[] = array('text' => '<br>' . TEXT_CATEGORIES_NAME . $category_inputs_string);
                                                                //BOF:category_group
                                                                $contents[] = array('text' => '<br>Is category Group<br>' . tep_draw_radio_field
                                                                            ('is_category_group', '1', false) . '&nbsp;Yes&nbsp;' . tep_draw_radio_field('is_category_group', '0', true) . '&nbsp;No');
                                                                //EOF:category_group
                                                                $contents[] = array('text' => '<br>' . TEXT_CATEGORIES_IMAGE . '<br>' .
                                                                    tep_draw_file_field('categories_image'));
                                                                $contents[] = array('text' => '<br>' . TEXT_CATEGORIES_BANNER_IMAGE . '<br>' .
                                                                    tep_draw_file_field('banner_image'));
                                                                $contents[] = array('text' => '<br>' . 'Categories Description' . $category_htc_description_string);
                                                                $contents[] = array('text' => '<br>' . TEXT_SORT_ORDER . '<br>' .
                                                                    tep_draw_input_field('sort_order', '', 'size="2"'));
                                                                //Categroies Status MOD BEGIN by FIW
                                                                $contents[] = array('text' => '<br>Categories Status<br>' . tep_draw_radio_field
                                                                            ('categories_status', '1', '') . '&nbsp;Active&nbsp;' . tep_draw_radio_field('categories_status', '0', '') . '&nbsp;Inactive');
                                                                //Categroies Status MOD END by FIW
                                                                $contents[] = array('text' => '<br>' . 'Header Tags Category Title' . $category_htc_title_string);
                                                                $contents[] = array('text' => '<br>' . 'Header Tags Category Description' . $category_htc_desc_string);
                                                                $contents[] = array('text' => '<br>' . 'Header Tags Category Keywords' . $category_htc_keywords_string);
                                                                $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath) .
                                                                    '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                                                                break;
                                                            case 'edit_category':
                                                                $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_CATEGORY . '</b>');
                                                                $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES, 'action=update_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"') . tep_draw_hidden_field('categories_id', $cInfo->
                                                                            categories_id));
                                                                $contents[] = array('text' => TEXT_EDIT_INTRO);
                                                                $category_inputs_string = '';
                                                                $languages = tep_get_languages();
                                                                for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                                                    $category_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                                    '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .
                                                                            tep_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', tep_get_category_name($cInfo->categories_id, $languages[$i]['id']));
                                                                    // HTC BOC
                                                                    $category_htc_title_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                                    '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .
                                                                            tep_draw_input_field('categories_htc_title_tag[' . $languages[$i]['id'] . ']', tep_get_category_htc_title($cInfo->categories_id, $languages[$i]['id']));
                                                                    $category_htc_desc_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                                    '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .
                                                                            tep_draw_input_field('categories_htc_desc_tag[' . $languages[$i]['id'] . ']', tep_get_category_htc_desc($cInfo->categories_id, $languages[$i]['id']));
                                                                    $category_htc_keywords_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
                                                                                    '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .
                                                                            tep_draw_input_field('categories_htc_keywords_tag[' . $languages[$i]['id'] . ']', tep_get_category_htc_keywords($cInfo->categories_id, $languages[$i]['id']));
                                                                    $category_htc_description_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES .
                                                                                    $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) .
                                                                            '&nbsp;' . tep_draw_textarea_field('categories_htc_description[' . $languages[$i]['id'] .
                                                                                    ']', 'hard', 30, 5, tep_get_category_htc_description($cInfo->categories_id, $languages[$i]['id']));
                                                                    // HTC EOC
                                                                }
                                                                $contents[] = array('text' => '<br>' . TEXT_EDIT_CATEGORIES_NAME . $category_inputs_string);
                                                                //BOF:category_group
                                                                $contents[] = array('text' => '<br>Is category Group<br>' . tep_draw_radio_field
                                                                            ('is_category_group', '1', $cat_status_set, '', (!$cInfo->parent_id ? '' :
                                                                                    'disabled')) . '&nbsp;Yes&nbsp;' . tep_draw_radio_field('is_category_group', '0', $cat_status_unset) . '&nbsp;No');
                                                                //EOF:category_group
                                                                $contents[] = array('text' => '<br>' . tep_image(DIR_WS_CATALOG_IMAGES . $cInfo->
                                                                            categories_image, $cInfo->categories_name) . '<br>' . DIR_WS_CATALOG_IMAGES .
                                                                    '<br><b>' . $cInfo->categories_image . '</b>');
                                                                $contents[] = array('text' => '<br>' . TEXT_EDIT_CATEGORIES_IMAGE . '<br>' .
                                                                    tep_draw_file_field('categories_image'));
                                                                if ($cInfo->banner_image != '') {
                                                                    $contents[] = array('text' => '<br>' . '<a href="' . tep_href_link(HTTP_CATALOG_SERVER .
                                                                                DIR_WS_CATALOG_IMAGES . $cInfo->banner_image) . '">' . '<b>' . $cInfo->
                                                                        banner_image . '</b></a>');
                                                                }
                                                                $contents[] = array('text' => '<br>' . TEXT_EDIT_BANNER_IMAGE . '<br>' .
                                                                    tep_draw_file_field('banner_image'));
                                                                $contents[] = array('text' => '<br>' . 'Categories Description' . $category_htc_description_string);
                                                                $contents[] = array('text' => '<br>' . TEXT_EDIT_SORT_ORDER . '<br>' .
                                                                    tep_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"'));
                                                                //Categroies Status MOD BEGIN by FIW
                                                                $contents[] = array('text' => '<br>Categories Status<br>' . tep_draw_radio_field
                                                                            ('categories_status', '1', $in_status) . '&nbsp;Active&nbsp;' .
                                                                    tep_draw_radio_field('categories_status', '0', $out_status) . '&nbsp;Inactive');
                                                                //Categroies Status MOD END by FIW
                                                                // HTC BOC
                                                                $contents[] = array('text' => '<br>' . 'Header Tags Category Title' . $category_htc_title_string);
                                                                $contents[] = array('text' => '<br>' . 'Header Tags Category Description' . $category_htc_desc_string);
                                                                $contents[] = array('text' => '<br>' . 'Header Tags Category Keywords' . $category_htc_keywords_string);
                                                                $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .
                                                                            '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                                                                break;
                                                            case 'delete_category':
                                                                $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CATEGORY . '</b>');
                                                                $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES, 'action=delete_category_confirm&cPath=' . $cPath) . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
                                                                $contents[] = array('text' => TEXT_DELETE_CATEGORY_INTRO);
                                                                $contents[] = array('text' => '<br><b>' . $cInfo->categories_name . '</b>');
                                                                if ($cInfo->childs_count > 0)
                                                                    $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_CHILDS, $cInfo->
                                                                                childs_count));
                                                                if ($cInfo->products_count > 0)
                                                                    $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $cInfo->
                                                                                products_count));
                                                                $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .
                                                                            '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                                                                break;
                                                            case 'move_category':
                                                                $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_CATEGORY . '</b>');
                                                                $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES, 'action=move_category_confirm&cPath=' . $cPath) . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
                                                                $contents[] = array('text' => sprintf(TEXT_MOVE_CATEGORIES_INTRO, $cInfo->
                                                                            categories_name));
                                                                $contents[] = array('text' => '<br>' . sprintf(TEXT_MOVE, $cInfo->
                                                                            categories_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree(), $current_category_id));
                                                                $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .
                                                                            '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                                                                break;
                                                            case 'delete_product':
                                                                $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_PRODUCT . '</b>');
                                                                $contents = array('form' => tep_draw_form('products', FILENAME_CATEGORIES, 'action=delete_product_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id));
                                                                $contents[] = array('text' => TEXT_DELETE_PRODUCT_INTRO);
                                                                $contents[] = array('text' => '<br><b>' . $pInfo->products_name . '</b>');
                                                                $product_categories_string = '';
                                                                $product_categories = tep_generate_category_path($pInfo->products_id, 'product');
                                                                for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
                                                                    $category_path = '';
                                                                    for ($j = 0, $k = sizeof($product_categories[$i]); $j < $k; $j++) {
                                                                        $category_path .= $product_categories[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
                                                                    }
                                                                    $category_path = substr($category_path, 0, -16);
                                                                    $product_categories_string .= tep_draw_checkbox_field('product_categories[]', $product_categories[$i][sizeof
                                                                                            ($product_categories[$i]) - 1]['id'], true) . '&nbsp;' . $category_path . '<br>';
                                                                }
                                                                $product_categories_string = substr($product_categories_string, 0, -4);
                                                                $contents[] = array('text' => '<br>' . $product_categories_string);
                                                                $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .
                                                                            '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                                                                break;
                                                            case 'move_product':
                                                                $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_PRODUCT . '</b>');
                                                                $contents = array('form' => tep_draw_form('products', FILENAME_CATEGORIES, 'action=move_product_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id));
                                                                $contents[] = array('text' => sprintf(TEXT_MOVE_PRODUCTS_INTRO, $pInfo->
                                                                            products_name));
                                                                $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br><b>' .
                                                                    tep_output_generated_category_path($pInfo->products_id, 'product') . '</b>');
                                                                $contents[] = array('text' => '<br>' . sprintf(TEXT_MOVE, $pInfo->products_name) .
                                                                    '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree(), $current_category_id));
                                                                $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .
                                                                            '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                                                                break;
                                                            case 'copy_to':
                                                                $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_COPY_TO . '</b>');
                                                                $contents = array('form' => tep_draw_form('copy_to', FILENAME_CATEGORIES, 'action=copy_to_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id));
                                                                $contents[] = array('text' => TEXT_INFO_COPY_TO_INTRO);
                                                                $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br><b>' .
                                                                    tep_output_generated_category_path($pInfo->products_id, 'product') . '</b>');
                                                                $contents[] = array('text' => '<br>' . TEXT_CATEGORIES . '<br>' .
                                                                    tep_draw_pull_down_menu('categories_id', tep_get_category_tree(), $current_category_id));
                                                                $contents[] = array('text' => '<br>' . TEXT_HOW_TO_COPY . '<br>' .
                                                                    tep_draw_radio_field('copy_as', 'link', true) . ' ' . TEXT_COPY_AS_LINK . '<br>' .
                                                                    tep_draw_radio_field('copy_as', 'duplicate') . ' ' . TEXT_COPY_AS_DUPLICATE);
                                                                $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_copy.gif', IMAGE_COPY) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .
                                                                            '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                                                                break;
                                                            default:
                                                                if ($rows > 0) {
																	
$avalara_tax_code = 'Not Set';
if (isset($cInfo) && is_object($cInfo)) { // category info box contents
	$heading[] = array('text' => '<b>' . $cInfo->categories_name . '</b>');
	$contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=edit_category') .'">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' .
	tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=delete_category') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a> <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' .$cPath . '&cID=' . $cInfo->categories_id . '&action=move_category') . '">' .tep_image_button('button_move.gif', IMAGE_MOVE) . '</a>');
	$contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . tep_date_short($cInfo->date_added));
	if (tep_not_null($cInfo->last_modified))
		$contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($cInfo->last_modified));
		$contents[] = array('text' => '<br>' . tep_info_image($cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT) . '<br>' . $cInfo->categories_image);
		$contents[] = array('text' => '<br>' . TEXT_SUBCATEGORIES . ' ' . $cInfo->childs_count . '<br>' . TEXT_PRODUCTS . ' ' . $cInfo->products_count);
} elseif (isset($pInfo) && is_object($pInfo)) {
	 // product info box contents
//MVS start
	$vendors_query_2 = tep_db_query("select v.vendors_id, v.vendors_name from vendors v, products p where v.vendors_id=p.vendors_id and p.products_id='" . $pInfo->products_id . "'");
	while ($vendors_2 = tep_db_fetch_array($vendors_query_2)) {
		$current_vendor_name = $vendors_2['vendors_name'];
	}
// MVS end
	$heading[] = array('text' => '<b>' . tep_get_products_name($pInfo->products_id, $languages_id) . '</b>');
	/* Ebay modification here !!!! */
	$contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=new_product') .'">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' .	tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=delete_product') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a> <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' .$cPath . '&pID=' . $pInfo->products_id . '&action=move_product') . '">' .tep_image_button('button_move.gif', IMAGE_MOVE) . '</a> <a href="' .tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=copy_to') . '">' . tep_image_button('button_copy_to.gif', IMAGE_COPY_TO) . '</a>');
	//  $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=new_product') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=delete_product') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a> <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=move_product') . '">' . tep_image_button('button_move.gif', IMAGE_MOVE) . '</a> <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=copy_to') . '">' . tep_image_button('button_copy_to.gif', IMAGE_COPY_TO) . '</a>');
	$contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . tep_date_short($pInfo->products_date_added));
	if (tep_not_null($pInfo->products_last_modified))
		$contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($pInfo->products_last_modified));
	
	if (date('Y-m-d') < $pInfo->products_date_available)
		$contents[] = array('text' => TEXT_DATE_AVAILABLE . ' ' . tep_date_short($pInfo->products_date_available));
//MVS start
	//$contents[] = array('text' => '<br>' . TEXT_PRODUCTS_PRICE_INFO . '<b> ' . $currencies->format($pInfo->products_price) . '</b><br>' . TEXT_VENDOR . '<b>' . $current_vendor_name . '</b><br>' . TEXT_VENDORS_PRODUCT_PRICE_INFO . '<b>' . $currencies->format($pInfo->vendors_product_price) . '</b><br>' . TEXT_PRODUCTS_QUANTITY_INFO . ' <b>' . $pInfo->products_quantity . '</b>');
	$contents[] = array('text' => '<br>' . TEXT_PRODUCTS_PRICE_INFO . '<b> ' . $currencies->format($pInfo->products_price) . '</b><br>' . TEXT_VENDOR . '<b>' . $current_vendor_name . '</b><br>' . TEXT_PRODUCTS_QUANTITY_INFO . ' <b>' . $pInfo->products_quantity . '</b>');
//MVS end
	$contents[] = array('text' => '<br>' . tep_info_image($pInfo->products_image, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '<br>' . $pInfo->products_image);
	//$contents[] = array('text' => '<br>' . TEXT_PRODUCTS_PRICE_INFO . ' ' . $currencies->format($pInfo->products_price) . '<br>' . TEXT_PRODUCTS_QUANTITY_INFO . ' ' . $pInfo->products_quantity);
	$contents[] = array('text' => '<br>' . TEXT_PRODUCTS_PRICE_INFO . ' ' . $currencies->format($pInfo->products_price) . '<br>Store Quantity: ' . $pInfo->store_quantity . '<br>Warehouse Quantity: ' . $pInfo->products_quantity);
	$contents[] = array('text' => '<br>' . TEXT_PRODUCTS_AVERAGE_RATING . ' ' .number_format($pInfo->average_rating, 2) . '%');
}
                                                                    //BOF:ebay_integration
                                                                    $ebay_category_name = 'Not Set';
                                                                    $google_category_name = 'Not Set';
                                                                    $amazon_category_name = 'Not Set';   
                                                                    echo '<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css"> <script src="//code.jquery.com/jquery-1.10.2.min.js"></script> <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>';
                                                                    if (isset($cInfo) && is_object($cInfo)) {
                                                                        $is_category = true;
                                                                        $id = $cInfo->categories_id;
                                                                        $sql = tep_db_query("select ec.category_name from categories c left join ebay_categories ec on c.ebay_category_id=ec.category_id where c.categories_id='" .
                                                                                $cInfo->categories_id . "'");
                                                                        $info = tep_db_fetch_array($sql);
                                                                        if (!empty($info['category_name'])) {
                                                                            $ebay_category_name = $info['category_name'];
                                                                        }
                                                                        $sql = tep_db_query("select gc.category_name from categories c left join google_categories gc on c.google_category_id=gc.category_id where c.categories_id='" .
                                                                                $cInfo->categories_id . "'");
                                                                        $info = tep_db_fetch_array($sql);
                                                                        if (!empty($info['category_name'])) {
                                                                            $google_category_name = $info['category_name'];
                                                                        }
                                                                        
if ($cInfo->amazon_category_id > 0) {                                                                        
 $sql = tep_db_query("select item_type from amazon_tree_guide where id='" .$cInfo->amazon_category_id . "'");
 $info = tep_db_fetch_array($sql);
 $amazon_category_name = $info['item_type'];
 }
 
 
 // code added for avalara tax code #start
 $avalara_tax_query = tep_db_fetch_array(tep_db_query("select avalara_tax_code from categories where categories_id = '".$cInfo->categories_id."'"));
 if(!empty($avalara_tax_query['avalara_tax_code'])){
	 $avalara_tax_code = $avalara_tax_query['avalara_tax_code'];
 }
 // code added for avalara tax code #ends
 
 
} elseif (isset($pInfo) && is_object($pInfo)) {
	
                                                                        $is_category = false;
                                                                        $id = $pInfo->products_id;
                                                                        $sql = tep_db_query("select ec.category_name from products p left join ebay_categories ec on p.ebay_category_id=ec.category_id where p.products_id='" .
                                                                                $pInfo->products_id . "'");
                                                                        $info = tep_db_fetch_array($sql);
                                                                        if (!empty($info['category_name'])) {
                                                                            $ebay_category_name = $info['category_name'];
                                                                        }
                                                                        /* $sql = tep_db_query("select gc.category_name from products p left join google_categories gc on p.google_category_id=gc.category_id where p.products_id='" .
                                                                          $pInfo->products_id . "'");
                                                                          $info = tep_db_fetch_array($sql);
                                                                          if (!empty($info['category_name']))
                                                                          {
                                                                          $google_category_name = $info['category_name'];
                                                                          } */
                                                                        $sql = tep_db_query("select google_category_id from products where products_id='" . $pInfo->products_id . "'");
                                                                        $info = tep_db_fetch_array($sql);
                                                                        if (!empty($info['google_category_id'])) {
                                                                            $google_category_name = get_google_category_path($info['google_category_id']);
  
 if ($pInfo->amazon_category_id >0)    {                                                                   }
 $sql = tep_db_query("select node_path, item_type from products where products_id='" . $pInfo->amazon_category_id . "'");
 $info = tep_db_fetch_array($sql);
 $amazon_category_name = $info['node_path'];
 }
                                                                                                                                               
                                                                    
	
	 // code added for avalara tax code #start
	 $avalara_tax_query = tep_db_fetch_array(tep_db_query("select avalara_tax_code from products where products_id = '".$pInfo->products_id."'"));
	 if(!empty($avalara_tax_query['avalara_tax_code'])){
		 $avalara_tax_code = $avalara_tax_query['avalara_tax_code'];
	 }
	 // code added for avalara tax code #ends																	
}
                                                                    $ebay_categories = get_eBay_categories();
                                                                    $google_categories = get_google_categories();
                                                                     $amazon_categories = get_amazon_categories();
                                                                    $contents[] = array('text' =>
                                                                        '<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
                       <script>
                        var jQuery = jQuery.noConflict();
                        var is_category = ' . ($is_category ? 'true' : 'false') .';' . 'var id = \'' . $id . '\';' . '</script>
                       <br><br>
                       Map eBay Category: <br><b>' . $ebay_category_name .
                                                                        '<b><br>' . '<a href="#" id="clicktomap" style="color:black;">Click to Map</a>' .
                                                                        '<br><br>' . '<div id="ebay_categories" style="visibility:hidden;">' .
                                                                        '<span id="ebaycategory_1">' . tep_draw_pull_down_menu('ebaycategory_1', $ebay_categories, '', 'style="width:100%;"') . '<br><br></span>' . '</div>',);
    //EOF:ebay_integration
$contents[] = array('text' => 'Map google Category: <br><b>' . $google_category_name .
                                                                        '<b><br>' . '<a href="#" id="clicktomapgooglecat" style="color:black;">Click to Map</a>' .
                                                                        '<br><br>' . '<div id="google_categories" style="visibility:hidden;">' .
                                                                        '<span id="googlecategory_1">' . tep_draw_pull_down_menu('googlecategory_1', $google_categories, '', 'style="width:100%;"') . '<br><br></span>' . '</div><span id="mapgooglebutton" style="visibility:hidden;"><input type="button" id="mapgooglebutton" value="Map Google Category" style="width:100%;" /><br><br></span>',);
                                                                        
 $contents[] = array('text' => 'Map Amazon Category (item-type): <br><b><div id="amazoncatname">' . $amazon_category_name . '</div>' . 
                                                                        '<b><br>' . '<a href="#" id="clicktomapamazoncat" style="color:black;">Click to Map</a>' .'<br><br>' . '<div id="amazon_categories" style="visibility:hidden;">' .
                                                                        '<span id="amazoncategory_1">' . tep_draw_pull_down_menu('amazoncategory_1', $amazon_categories, '', 'style="width:100%;"') . '<br><br></span>' . '</div><span id="mapamazonbutton" style="visibility:hidden;"><input type="button" id="mapamazonbutton" value="Map Amazon Category" style="width:100%;" /><br><br></span>',);
																		
																		
		// avalara tax code mapping
		
		$contents[] = array('text' => 'Map Avalara Tax Code: <b><div id="avalaracatname">' . $avalara_tax_code . '</div>' . 
                                                                        '<b><br>' . '<a href="#" id="clicktomapavalarataxcode" style="color:black;">Click to Map</a>' .'<br><br>' . '<div class="avalara_tax_code_container" style="display:none;">' .
                                                                        '<span id="avalarataxcode_1">' . tep_draw_input_field('avalara_tax_codes','',' id="avalara_tax_codes" placeholder="autocomplete" ') . '<br><br></span>' . '<input type="button" id="mapavalarabutton" value="Map Avalara Tax Code" style="width:100%;" /><br><br></div>',);
                                                                
																} else {
																	 // create category/product info
                                                                    $heading[] = array('text' => '<b>' . EMPTY_CATEGORY . '</b>');
                                                                    $contents[] = array('text' => TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS);
                                                                
																}
                                                            break;
                                                        }
                                                        if ((tep_not_null($heading)) && (tep_not_null($contents))) {
                                                            echo '            <td width="25%" valign="top">' . "\n";
                                                            $box = new box;
                                                            echo $box->infoBox($heading, $contents);
                                                            echo '            </td>' . "\n";
                                                        }
                                                        ?>
                                                    </tr>
                                                </table></td>
                                        </tr>
                                    </table>
                                    <?php
                                }
                                ?>
                            </td>
                            <!-- body_text_eof //-->
                        </tr>
                    </table> 
                    <!-- END your table-->
                    <!-- body_eof //-->
                    <!-- footer //-->
                    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
                    <script type="text/javascript">
                        function refreshParentpage(url) {
                            location = url;
                        }
                        //BOF:ebay_integration
                        jQuery(document).ready(function () {
<?php if ((isset($_GET['update_options'])) && ($_GET['update_options'] == '1')) { ?>
                                jQuery('#ui-id-9').trigger('click');
<?php } ?>
                            //jQuery('select[name^="ebaycategory_"]').on('change', function(){
jQuery(document).ready(function(e) {
    jQuery('#clicktomapavalarataxcode').on('click', function() {
		jQuery('.avalara_tax_code_container').toggle('slow');
	});
	
	jQuery('#mapavalarabutton').on('click',function() {
		
		var avalara_tax_codes = jQuery('input[name^="avalara_tax_codes"]').val();
		
		if(avalara_tax_codes != ''){
			jQuery.ajax({
				url: 'categories.php',
				dataType: 'json',
				method: 'post',
				data: {
					 is_category: is_category,
					 action: 'setAvalaraTaxCode',
					 osc_object_id: id,
					 tax_code: encodeURIComponent(avalara_tax_codes)
	            },
				beforeSend: function(){
					jQuery('#avalaracatname').append('<div class="attention"><img src="images/ajax-loader.gif" alt="" title=""></div>');
				},
				success: function (json) {
					
					jQuery('.attention').remove();
					
					if(json['error']){
					
						alert("Please enter a valid avatax code!");
						return false;
					
					}else if(json['success']){
					
						alert("Tax Code Mapped.");
						jQuery('input[name^="avalara_tax_codes"]').val('');
						jQuery('.avalara_tax_code_container').toggle('slow');
						jQuery('#avalaracatname').html(avalara_tax_codes);
						return true;
					
					}else{
						
						alert("Please enter a valid avatax code!");
						return false;
						
					}
				}
			});
		}else{
			
			alert("Please enter a valid avatax code!");
			return false;
		
		}
	});
	$('input[name=\'avalara_tax_codes\']').autocomplete({
	delay: 500,
	source: function (request, response) {
		$.ajax({
			
	
			url: 'categories.php?mode=getavataxcode&filter_name=' + encodeURIComponent(request.term),
	
			dataType: 'json',
			
			beforeSend: function(){
				jQuery('#avalarataxcode_1').append('<span class="attention"><img src="images/ajax-loader.gif" alt="" title=""></span>');
			},
	
			success: function (json) {
				
				jQuery('.attention').remove();
				
				response($.map(json, function (item) {
	
					return {
	
						label: item.name,
	
					}
	
				}));
	
			}
	
		
		});
	},
	select: function (event, ui) {
		$('#avalara_tax_codes').val(ui.item.label);
		return false;
	},
	focus: function (event, ui) {
		return false;
	}
	});
});
   jQuery(document).on('change', 'select[name^="ebaycategory_"]', function() {
     jQuery('span#mapebaybutton').remove();
     var parent_id = jQuery(this).val();
     var parent_level = jQuery(this).attr('name').replace(/ebaycategory_/i, '');
     parent_level = parseInt(parent_level);
     jQuery('span[id^="ebaycategory_"]').each(function() {
         var cur_level = jQuery(this).attr('id').replace(/ebaycategory_/i, '');
         cur_level = parseInt(cur_level);
         if (cur_level > parent_level) {
             jQuery(this).remove();
         }
     });
     jQuery.ajax({
         url: 'categories.php',
         method: 'post',
         data: {
             'action': 'get_ebay_categories_html',
             'parent_id': parent_id,
             'parent_level': parent_level
         },
         success: function(html) {
             if (html != '') {
                 jQuery('div#ebay_categories').append(html);
             } else {
                 jQuery('div#ebay_categories').append('<span id="mapebaybutton"><input type="button" id="mapebaybutton" value="Map eBay Category" style="width:100%;" /><br><br></span>');
             }
         }
     });
 }).on('click', 'input#mapebaybutton', function() {
     var final_level = 1;
     jQuery('select[name^="ebaycategory_"]').each(function() {
         var cur_level = jQuery(this).attr('name').replace(/ebaycategory_/i, '');
         cur_level = parseInt(cur_level);
         if (cur_level > final_level) {
             final_level = cur_level;
         }
     });
     if (final_level > 0) {
         var category_id = jQuery('select[name="ebaycategory_' + final_level + '"]').val();
         jQuery.ajax({
             url: 'categories.php',
             method: 'post',
             data: {
                 action: 'mapebaycategory',
                 is_category: is_category,
                 osc_object_id: id,
                 ebay_category_id: category_id
             },
             success: function(message) {
                 //console.log(message);
                 if (message == 'mapped') {
                     alert('Category/Item successfully mapped with eBay category.');
                 } else {
                     alert('Error encountered whie mapping Category/Item.');
                 }
             }
         });
     }
 }).on('click', 'a#clicktomap', function(event) {
     event.preventDefault();
     jQuery('div#ebay_categories').css('visibility', 'visible');
 }).on('change', 'select[name^="googlecategory_"]', function() {
     jQuery('span#mapgooglebutton').remove();
     var parent_id = jQuery(this).val();
     var parent_level = jQuery(this).attr('name').replace(/googlecategory_/i, '');
     parent_level = parseInt(parent_level);
     jQuery('span[id^="googlecategory_"]').each(function() {
         var cur_level = jQuery(this).attr('id').replace(/googlecategory_/i, '');
         cur_level = parseInt(cur_level);
         if (cur_level > parent_level) {
             jQuery(this).remove();
         }
     });
     //console.log(parent_id + ' | ' + parent_level);
     jQuery.ajax({
         url: 'categories.php',
         method: 'post',
         data: {
             action: 'get_google_categories_html',
             parent_id: parent_id,
             parent_level: parent_level
         },
         success: function(html) {
             if (html!=''){
             jQuery('div#google_categories').append(html);
             } else {
             jQuery('div#google_categories').append('<span id="mapgooglebutton"><input type="button" id="mapgooglebutton" value="Map Google Category" style="width:100%;" /><br><br></span>');
             } 
         }
     });
 }) .on('click', 'input#mapgooglebutton', function() {
     var final_level = 1;
     jQuery('select[name^="googlecategory_"]').each(function() {
         var cur_level = jQuery(this).attr('name').replace(/googlecategory_/i, '');
         cur_level = parseInt(cur_level);
         if (cur_level > final_level) {
             final_level = cur_level;
         }
     });
     if (final_level > 0) {
         var category_id = false;
         category_id = jQuery('select[name="googlecategory_' + final_level + '"]').val();
         if (!category_id) {
             category_id = jQuery('select[name="googlecategory_' + (final_level - 1) + '"]').val();
         }
         jQuery.ajax({
             url: 'categories.php',
             method: 'post',
             data: {
                 action: 'mapgooglecategory',
                 is_category: is_category,
                 osc_object_id: id,
                 google_category_id: category_id
             },
             success: function(message) {
                 //console.log(message);
                 if (message == 'mapped') {
                     alert('Category/Item successfully mapped with Google category.');
                 } else {
                     alert('Error encountered whie mapping Category/Item.');
                 }
             }
         });
     }
 }).on('click', 'a#clicktomapgooglecat', function(event) {
     event.preventDefault();
     jQuery('div#google_categories').css('visibility', 'visible');
    // jQuery('span#mapgooglebutton').css('visibility', 'visible');
 }) .on('change', 'select[name^="amazoncategory_"]', function() {
     jQuery('span#mapamazonbutton').remove();
     var parent_id = jQuery(this).val();
     var parent_level = jQuery(this).attr('name').replace(/amazoncategory_/i, '');
     parent_level = parseInt(parent_level);
     jQuery('span[id^="amazoncategory_"]').each(function() {
         var cur_level = jQuery(this).attr('id').replace(/amazoncategory_/i, '');
         cur_level = parseInt(cur_level);
         if (cur_level > parent_level) {
             jQuery(this).remove();
         }
     });
     //console.log(parent_id + ' | ' + parent_level);
     jQuery.ajax({
         url: 'categories.php',
         method: 'post',
         data: {
             action: 'get_amazon_categories_html',
             parent_id: parent_id,
             parent_level: parent_level
         },
         success: function(html) {
             if (html!=''){
             jQuery('div#amazon_categories').append(html);
             } else {
             jQuery('div#amazon_categories').append('<span id="mapamazonbutton"><input type="button" id="mapamazonbutton" value="Map Amazon Category" style="width:100%;" /><br><br></span>');
             } 
         }
     });
 }) .on('click', 'input#mapamazonbutton', function() {
     var final_level = 1;
     jQuery('select[name^="amazoncategory_"]').each(function() {
         var cur_level = jQuery(this).attr('name').replace(/amazoncategory_/i, '');
         cur_level = parseInt(cur_level);
         if (cur_level > final_level) {
             final_level = cur_level;
         }
     });
     if (final_level > 0) {
         var category_id = false;
         category_id = jQuery('select[name="amazoncategory_' + final_level + '"]').val();
         if (!category_id) {
             category_id = jQuery('select[name="amazoncategory_' + (final_level - 1) + '"]').val();
         }
         jQuery.ajax({
             url: 'categories.php',
             method: 'post',
             data: {
                 action: 'mapamazoncategory',
                 is_category: is_category,
                 osc_object_id: id,
                 amazon_category_id: category_id
             },
             success: function(message) {
                 //console.log(message);
                 if (message != 'error') {
                     alert('Category/Item successfully mapped with Amazon category.');
                     jQuery("#amazoncatname").html(message);
                 } else {
                     alert('Error encountered whie mapping Category/Item.');
                 }
             }
         });
     }
 }).on('click', 'a#clicktomapamazoncat', function(event) {
     event.preventDefault();
     jQuery('div#amazon_categories').css('visibility', 'visible');
    // jQuery('span#mapgooglebutton').css('visibility', 'visible');
 });
 jQuery('div#spiffycalendar').css('z-index', '100');
 });
 //EOF:ebay_integration
 attributeManagerInit();
 SetFocus();
<?php if ((isset($_GET['update_options'])) && ($_GET['update_options'] == '1')) { ?>
                            jQuery('#tabs-8').trigger('click');
<?php } ?>
                    </script>
                    <!-- footer_eof //-->
                    <?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>