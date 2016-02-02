<?php
/*
  $Id: general.php,v 1.160 2003/07/12 08:32:47 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

////

//Admin begin
////
//Check login and file access
function tep_admin_check_login() {
  global $PHP_SELF, $login_groups_id;
  if (!tep_session_is_registered('login_id')) {
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  } else {
    $filename = basename( $PHP_SELF );
    if ($filename != FILENAME_DEFAULT && $filename != FILENAME_FORBIDEN && $filename != FILENAME_LOGOFF && $filename != FILENAME_ADMIN_ACCOUNT && $filename != FILENAME_POPUP_IMAGE && $filename != 'packingslip.php' && $filename != 'invoice.php') {
      $db_file_query = tep_db_query("select admin_files_name from " . TABLE_ADMIN_FILES . " where FIND_IN_SET( '" . $login_groups_id . "', admin_groups_id) and admin_files_name = '" . $filename . "'");
      if (!tep_db_num_rows($db_file_query)) {
        tep_redirect(tep_href_link(FILENAME_FORBIDEN));
      }
    }
  }  
}

////
//Return 'true' or 'false' value to display boxes and files in index.php and column_left.php
function tep_admin_check_boxes($filename, $boxes='') {
  global $login_groups_id;
  
  $is_boxes = 1;
  if ($boxes == 'sub_boxes') {
    $is_boxes = 0;
  }
  $dbquery = tep_db_query("select admin_files_id from " . TABLE_ADMIN_FILES . " where FIND_IN_SET( '" . $login_groups_id . "', admin_groups_id) and admin_files_is_boxes = '" . $is_boxes . "' and admin_files_name = '" . $filename . "'");
  
  $return_value = false;
  if (tep_db_num_rows($dbquery)) {
    $return_value = true;
  }
  return $return_value;
}

////
//Return files stored in box that can be accessed by user
function tep_admin_files_boxes($filename, $sub_box_name) {
  global $login_groups_id;
  $sub_boxes = '';
  
  $dbquery = tep_db_query("select admin_files_name from " . TABLE_ADMIN_FILES . " where FIND_IN_SET( '" . $login_groups_id . "', admin_groups_id) and admin_files_is_boxes = '0' and admin_files_name = '" . $filename . "'");
  if (tep_db_num_rows($dbquery)) {
    $sub_boxes = '<a href="' . tep_href_link($filename) . '" class="menuBoxContentLink">' . $sub_box_name . '</a><br>';
  }
  return $sub_boxes;
}

////
//Get selected file for index.php
function tep_selected_file($filename) {
  global $login_groups_id;
  $randomize = FILENAME_ADMIN_ACCOUNT;
  
  $dbquery = tep_db_query("select admin_files_id as boxes_id from " . TABLE_ADMIN_FILES . " where FIND_IN_SET( '" . $login_groups_id . "', admin_groups_id) and admin_files_is_boxes = '1' and admin_files_name = '" . $filename . "'");
  if (tep_db_num_rows($dbquery)) {
    $boxes_id = tep_db_fetch_array($dbquery);
    $randomize_query = tep_db_query("select admin_files_name from " . TABLE_ADMIN_FILES . " where FIND_IN_SET( '" . $login_groups_id . "', admin_groups_id) and admin_files_is_boxes = '0' and admin_files_to_boxes = '" . $boxes_id['boxes_id'] . "'");
    if (tep_db_num_rows($randomize_query)) {
      $file_selected = tep_db_fetch_array($randomize_query);
      $randomize = $file_selected['admin_files_name'];
    }
  }
  return $randomize;
}
//Admin end

// Redirect to another page or site
  function tep_redirect($url) {
    global $logger;

    if ( (strstr($url, "\n") != false) || (strstr($url, "\r") != false) ) {
      tep_redirect(tep_href_link(FILENAME_DEFAULT, '', 'NONSSL', false));
    }

    header('Location: ' . $url);

    if (STORE_PAGE_PARSE_TIME == 'true') {
      if (!is_object($logger)) $logger = new logger;
      $logger->timer_stop();
    }

    exit;
  }

////
// Parse the data used in the html tags to ensure the tags will not break
  function tep_parse_input_field_data($data, $parse) {
    return strtr(trim($data), $parse);
  }

  function tep_output_string($string, $translate = false, $protected = false) {
    if ($protected == true) {
      return htmlspecialchars($string);
    } else {
      if ($translate == false) {
        return tep_parse_input_field_data($string, array('"' => '&quot;'));
      } else {
        return tep_parse_input_field_data($string, $translate);
      }
    }
  }

  function tep_output_string_protected($string) {
    return tep_output_string($string, false, true);
  }

  function tep_sanitize_string($string) {
    $string = preg_replace('/ +/', ' ', $string);

    return preg_replace("/[<>]/", '_', $string);
  }

  function tep_customers_name($customers_id) {
    $customers = tep_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customers_id . "'");
    $customers_values = tep_db_fetch_array($customers);

    return $customers_values['customers_firstname'] . ' ' . $customers_values['customers_lastname'];
  }

  function tep_get_path($current_category_id = '') {
    global $cPath_array;

    if ($current_category_id == '') {
      $cPath_new = implode('_', $cPath_array);
    } else {
      if (sizeof($cPath_array) == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';
        $last_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$cPath_array[(sizeof($cPath_array)-1)] . "'");
        $last_category = tep_db_fetch_array($last_category_query);

        $current_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$current_category_id . "'");
        $current_category = tep_db_fetch_array($current_category_query);

        if ($last_category['parent_id'] == $current_category['parent_id']) {
          for ($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        } else {
          for ($i = 0, $n = sizeof($cPath_array); $i < $n; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        }

        $cPath_new .= '_' . $current_category_id;

        if (substr($cPath_new, 0, 1) == '_') {
          $cPath_new = substr($cPath_new, 1);
        }
      }
    }

    return 'cPath=' . $cPath_new;
  }

  function tep_get_all_get_params($exclude_array = '') {
    global $HTTP_GET_VARS;

    if ($exclude_array == '') $exclude_array = array();

    $get_url = '';

    reset($HTTP_GET_VARS);
    while (list($key, $value) = each($HTTP_GET_VARS)) {
      if (($key != tep_session_name()) && ($key != 'error') && (!in_array($key, $exclude_array))) $get_url .= $key . '=' . $value . '&';
    }

    return $get_url;
  }

  function tep_date_long($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = (int)substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    return strftime(DATE_FORMAT_LONG, mktime($hour, $minute, $second, $month, $day, $year));
  }

////
// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
// NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
  function tep_date_short($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {
      return date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
    } else {
      return preg_replace('/2037' . '$/', $year, date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, 2037)));
    }

  }

  function tep_datetime_short($raw_datetime) {
    if ( ($raw_datetime == '0000-00-00 00:00:00') || ($raw_datetime == '') ) return false;

    $year = (int)substr($raw_datetime, 0, 4);
    $month = (int)substr($raw_datetime, 5, 2);
    $day = (int)substr($raw_datetime, 8, 2);
    $hour = (int)substr($raw_datetime, 11, 2);
    $minute = (int)substr($raw_datetime, 14, 2);
    $second = (int)substr($raw_datetime, 17, 2);

    return strftime(DATE_TIME_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
  }

  function tep_get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
    global $languages_id;

    if (!is_array($category_tree_array)) $category_tree_array = array();
    if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

    if ($include_itself) {
      $category_query = tep_db_query("select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.language_id = '" . (int)$languages_id . "' and cd.categories_id = '" . (int)$parent_id . "'");
      $category = tep_db_fetch_array($category_query);
      $category_tree_array[] = array('id' => $parent_id, 'text' => $category['categories_name']);
    }

    $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
    while ($categories = tep_db_fetch_array($categories_query)) {
      if ($exclude != $categories['categories_id']) $category_tree_array[] = array('id' => $categories['categories_id'], 'text' => $spacing . $categories['categories_name']);
      $category_tree_array = tep_get_category_tree($categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
    }

    return $category_tree_array;
  }

  //function tep_draw_products_pull_down($name, $parameters = '', $exclude = '') {
  function tep_draw_products_pull_down($name, $parameters = '', $exclude = '',  $filter_type = '') {
    global $currencies, $languages_id;

    if ($exclude == '') {
      $exclude = array();
    }

    $select_string = '<select name="' . $name . '"';

    if ($parameters) {
      $select_string .= ' ' . $parameters;
    }

    $select_string .= '>';
	switch ($filter_type){
		case 'specials':
		case 'featured':
			$products_query = tep_db_query("select p.products_id, p.products_model, pd.products_name, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and (parent_products_model is null or parent_products_model='') order by products_name");
			break;
		default:
		/*	$products_query = tep_db_query("select p.products_id, p.products_model, pd.products_name, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by products_name");
	}
    while ($products = tep_db_fetch_array($products_query)) {
      if (!in_array($products['products_id'], $exclude)) {
        $select_string .= '<option value="' . $products['products_id'] . '">' . $products['products_name'] . ' (' . $currencies->format($products['products_price']) . ') (' . $products['products_model'] . ')</option>';
      }
    } */
                
      // BOF Separate Pricing Per Customer
      $all_groups=array();
      $customers_groups_query = tep_db_query("select customers_group_name, customers_group_id from " . TABLE_CUSTOMERS_GROUPS . " order by customers_group_id ");
      while ($existing_groups =  tep_db_fetch_array($customers_groups_query)) {
          $all_groups[$existing_groups['customers_group_id']]=$existing_groups['customers_group_name'];
      }
// EOF Separate Pricing Per Customer
    $products_query = tep_db_query("select p.products_id, pd.products_name, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by products_name");
        }
        while ($products = tep_db_fetch_array($products_query)) {
// BOF Separate Price Per Customer
     if (!in_array($products['products_id'], $exclude)) {
         $price_query=tep_db_query("select customers_group_price, customers_group_id from " . TABLE_PRODUCTS_GROUPS . " where products_id = " . $products['products_id']);
         $product_prices=array();
         while($prices_array=tep_db_fetch_array($price_query)){
             $product_prices[$prices_array['customers_group_id']]=$prices_array['customers_group_price'];
         }
         reset($all_groups);
         $price_string="";
         $sde=0;
         while(list($sdek,$sdev)=each($all_groups)){
             if (!in_array((int)$products['products_id'].":".(int)$sdek, $exclude)) {
                 if($sde)
                    $price_string.=", ";
                 $price_string.=$sdev.": ".$currencies->format(isset($product_prices[$sdek]) ? $product_prices[$sdek]:$products['products_price']);
                 $sde=1;
             }
         }
         $select_string .= '<option value="' . $products['products_id'] . '">' . $products['products_name'] . ' (' . $price_string . ')</option>\n';
      }
// EOF 	Separate Pricing Per Customer
      } // end while ($products = tep_db_fetch_array($products_query))
              

    $select_string .= '</select>';

    return $select_string;
  }

  function tep_options_name($options_id) {
    global $languages_id;

    $options = tep_db_query("select products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . (int)$options_id . "' and language_id = '" . (int)$languages_id . "'");
    $options_values = tep_db_fetch_array($options);

    return $options_values['products_options_name'];
  }

  function tep_values_name($values_id) {
    global $languages_id;

    $values = tep_db_query("select products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . (int)$values_id . "' and language_id = '" . (int)$languages_id . "'");
    $values_values = tep_db_fetch_array($values);

    return $values_values['products_options_values_name'];
  }

  function tep_info_image($image, $alt, $width = '', $height = '') {
    if (tep_not_null($image) && (file_exists(DIR_FS_CATALOG_IMAGES . $image)) ) {
      $image = tep_image(DIR_WS_CATALOG_IMAGES . $image, $alt, $width, $height);
    } else {
      $image = TEXT_IMAGE_NONEXISTENT;
    }

    return $image;
  }

  function tep_break_string($string, $len, $break_char = '-') {
    $l = 0;
    $output = '';
    for ($i=0, $n=strlen($string); $i<$n; $i++) {
      $char = substr($string, $i, 1);
      if ($char != ' ') {
        $l++;
      } else {
        $l = 0;
      }
      if ($l > $len) {
        $l = 1;
        $output .= $break_char;
      }
      $output .= $char;
    }

    return $output;
  }

  function tep_get_country_name($country_id) {
    $country_query = tep_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$country_id . "'");

    if (!tep_db_num_rows($country_query)) {
      return $country_id;
    } else {
      $country = tep_db_fetch_array($country_query);
      return $country['countries_name'];
    }
  }

  function tep_get_zone_name($country_id, $zone_id, $default_zone) {
    $zone_query = tep_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country_id . "' and zone_id = '" . (int)$zone_id . "'");
    if (tep_db_num_rows($zone_query)) {
      $zone = tep_db_fetch_array($zone_query);
      return $zone['zone_name'];
    } else {
      return $default_zone;
    }
  }

  function tep_not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if ( (is_string($value) || is_int($value)) && ($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  }

  function tep_browser_detect($component) {
    //global $HTTP_USER_AGENT;

    //return stristr($HTTP_USER_AGENT, $component);
	return stristr($_SERVER['HTTP_USER_AGENT'], $component);
  }

  function tep_tax_classes_pull_down($parameters, $selected = '') {
    $select_string = '<select ' . $parameters . '>';
    $classes_query = tep_db_query("select tax_class_id, tax_class_title from " . TABLE_TAX_CLASS . " order by tax_class_title");
    while ($classes = tep_db_fetch_array($classes_query)) {
      $select_string .= '<option value="' . $classes['tax_class_id'] . '"';
      if ($selected == $classes['tax_class_id']) $select_string .= ' SELECTED';
      $select_string .= '>' . $classes['tax_class_title'] . '</option>';
    }
    $select_string .= '</select>';

    return $select_string;
  }

  function tep_geo_zones_pull_down($parameters, $selected = '') {
    $select_string = '<select ' . $parameters . '>';
    $zones_query = tep_db_query("select geo_zone_id, geo_zone_name from " . TABLE_GEO_ZONES . " order by geo_zone_name");
    while ($zones = tep_db_fetch_array($zones_query)) {
      $select_string .= '<option value="' . $zones['geo_zone_id'] . '"';
      if ($selected == $zones['geo_zone_id']) $select_string .= ' SELECTED';
      $select_string .= '>' . $zones['geo_zone_name'] . '</option>';
    }
    $select_string .= '</select>';

    return $select_string;
  }

  function tep_get_geo_zone_name($geo_zone_id) {
    $zones_query = tep_db_query("select geo_zone_name from " . TABLE_GEO_ZONES . " where geo_zone_id = '" . (int)$geo_zone_id . "'");

    if (!tep_db_num_rows($zones_query)) {
      $geo_zone_name = $geo_zone_id;
    } else {
      $zones = tep_db_fetch_array($zones_query);
      $geo_zone_name = $zones['geo_zone_name'];
    }

    return $geo_zone_name;
  }

  function tep_address_format($address_format_id, $address, $html, $boln, $eoln) {
    $address_format_query = tep_db_query("select address_format as format from " . TABLE_ADDRESS_FORMAT . " where address_format_id = '" . (int)$address_format_id . "'");
    $address_format = tep_db_fetch_array($address_format_query);

    $company = tep_output_string_protected($address['company']);
    if (isset($address['firstname']) && tep_not_null($address['firstname'])) {
      $firstname = tep_output_string_protected($address['firstname']);
      $lastname = tep_output_string_protected($address['lastname']);
    } elseif (isset($address['name']) && tep_not_null($address['name'])) {
      $firstname = tep_output_string_protected($address['name']);
      $lastname = '';
    } else {
      $firstname = '';
      $lastname = '';
    }
    $street = tep_output_string_protected($address['street_address']);
    $suburb = tep_output_string_protected($address['suburb']);
    $city = tep_output_string_protected($address['city']);
    $state = tep_output_string_protected($address['state']);
    if (isset($address['country_id']) && tep_not_null($address['country_id'])) {
      $country = tep_get_country_name($address['country_id']);

      if (isset($address['zone_id']) && tep_not_null($address['zone_id'])) {
        $state = tep_get_zone_code($address['country_id'], $address['zone_id'], $state);
      }
    } elseif (isset($address['country']) && tep_not_null($address['country'])) {
      $country = tep_output_string_protected($address['country']);
    } else {
      $country = '';
    }
    $postcode = tep_output_string_protected($address['postcode']);
    $zip = $postcode;

    if ($html) {
// HTML Mode
      $HR = '<hr>';
      $hr = '<hr>';
      if ( ($boln == '') && ($eoln == "\n") ) { // Values not specified, use rational defaults
        $CR = '<br>';
        $cr = '<br>';
        $eoln = $cr;
      } else { // Use values supplied
        $CR = $eoln . $boln;
        $cr = $CR;
      }
    } else {
// Text Mode
      $CR = $eoln;
      $cr = $CR;
      $HR = '----------------------------------------';
      $hr = '----------------------------------------';
    }

    $statecomma = '';
    $streets = $street;
    if ($suburb != '') $streets = $street . $cr . $suburb;
    if ($country == '') $country = tep_output_string_protected($address['country']);
    if ($state != '') $statecomma = $state . ', ';

    $fmt = $address_format['format'];
    eval("\$address = \"$fmt\";");

    if ( (ACCOUNT_COMPANY == 'true') && (tep_not_null($company)) ) {
      $address = $company . $cr . $address;
    }

    return $address;
  }

  ////////////////////////////////////////////////////////////////////////////////////////////////
  //
  // Function    : tep_get_zone_code
  //
  // Arguments   : country           country code string
  //               zone              state/province zone_id
  //               def_state         default string if zone==0
  //
  // Return      : state_prov_code   state/province code
  //
  // Description : Function to retrieve the state/province code (as in FL for Florida etc)
  //
  ////////////////////////////////////////////////////////////////////////////////////////////////
  function tep_get_zone_code($country, $zone, $def_state) {

    $state_prov_query = tep_db_query("select zone_code from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "' and zone_id = '" . (int)$zone . "'");

    if (!tep_db_num_rows($state_prov_query)) {
      $state_prov_code = $def_state;
    }
    else {
      $state_prov_values = tep_db_fetch_array($state_prov_query);
      $state_prov_code = $state_prov_values['zone_code'];
    }
    
    return $state_prov_code;
  }

  function tep_get_uprid($prid, $params) {
    $uprid = $prid;
    if ( (is_array($params)) && (!strstr($prid, '{')) ) {
      while (list($option, $value) = each($params)) {
        $uprid = $uprid . '{' . $option . '}' . $value;
      }
    }

    return $uprid;
  }

  function tep_get_prid($uprid) {
    $pieces = explode('{', $uprid);

    return $pieces[0]; 
  }

  function tep_get_languages() {
    $languages_query = tep_db_query("select languages_id, name, code, image, directory from " . TABLE_LANGUAGES . " order by sort_order");
    while ($languages = tep_db_fetch_array($languages_query)) {
      $languages_array[] = array('id' => $languages['languages_id'],
                                 'name' => $languages['name'],
                                 'code' => $languages['code'],
                                 'image' => $languages['image'],
                                 'directory' => $languages['directory']);
    }

    return $languages_array;
  }

  function tep_get_category_name($category_id, $language_id) {
    $category_query = tep_db_query("select categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
    $category = tep_db_fetch_array($category_query);

    return $category['categories_name'];
  }

  function tep_get_orders_status_name($orders_status_id, $language_id = '') {
    global $languages_id;

    if (!$language_id) $language_id = $languages_id;
    $orders_status_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . (int)$orders_status_id . "' and language_id = '" . (int)$language_id . "'");
    $orders_status = tep_db_fetch_array($orders_status_query);

    return $orders_status['orders_status_name'];
  }

  function tep_get_orders_status() {
    global $languages_id;

    $orders_status_array = array();
    $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int)$languages_id . "' order by orders_status_id");
    while ($orders_status = tep_db_fetch_array($orders_status_query)) {
      $orders_status_array[] = array('id' => $orders_status['orders_status_id'],
                                     'text' => $orders_status['orders_status_name']);
    }

    return $orders_status_array;
  }

  function tep_get_products_name($product_id, $language_id = 0) {
    global $languages_id;

    if ($language_id == 0) $language_id = $languages_id;
    $product_query = tep_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_name'];
  }

  function tep_get_products_description($product_id, $language_id) {
    $product_query = tep_db_query("select products_description from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_description'];
  }
  
  function tep_get_products_specifications($product_id, $language_id) {
    $product_query = tep_db_query("select products_specifications from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_specifications'];
  }

  function tep_get_products_url($product_id, $language_id) {
    $product_query = tep_db_query("select products_url from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_url'];
  }

////
// Return the manufacturers URL in the needed language
// TABLES: manufacturers_info
  function tep_get_manufacturer_url($manufacturer_id, $language_id) {
    $manufacturer_query = tep_db_query("select manufacturers_url from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
    $manufacturer = tep_db_fetch_array($manufacturer_query);

    return $manufacturer['manufacturers_url'];
  }

////
// Wrapper for class_exists() function
// This function is not available in all PHP versions so we test it before using it.
  function tep_class_exists($class_name) {
    if (function_exists('class_exists')) {
      return class_exists($class_name);
    } else {
      return true;
    }
  }

////
// Count how many products exist in a category
// TABLES: products, products_to_categories, categories
  function tep_products_in_category_count($categories_id, $include_deactivated = false) {
    $products_count = 0;

    if ($include_deactivated) {
      $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$categories_id . "'");
    } else {
      $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '" . (int)$categories_id . "'");
    }

    $products = tep_db_fetch_array($products_query);

    $products_count += $products['total'];

    $childs_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$categories_id . "'");
    if (tep_db_num_rows($childs_query)) {
      while ($childs = tep_db_fetch_array($childs_query)) {
        $products_count += tep_products_in_category_count($childs['categories_id'], $include_deactivated);
      }
    }

    return $products_count;
  }

////
// Count how many subcategories exist in a category
// TABLES: categories
  function tep_childs_in_category_count($categories_id) {
    $categories_count = 0;

    $categories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$categories_id . "'");
    while ($categories = tep_db_fetch_array($categories_query)) {
      $categories_count++;
      $categories_count += tep_childs_in_category_count($categories['categories_id']);
    }

    return $categories_count;
  }


////
// Returns an array with countries
// TABLES: countries
  function tep_get_countries($default = '') {
    $countries_array = array();
    if ($default) {
      $countries_array[] = array('id' => '',
                                 'text' => $default);
    }
    $countries_query = tep_db_query("select countries_id, countries_name from " . TABLE_COUNTRIES . " order by countries_name");
    while ($countries = tep_db_fetch_array($countries_query)) {
      $countries_array[] = array('id' => $countries['countries_id'],
                                 'text' => $countries['countries_name']);
    }

    return $countries_array;
  }

////
// return an array with country zones
  function tep_get_country_zones($country_id) {
    $zones_array = array();
    $zones_query = tep_db_query("select zone_id, zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country_id . "' order by zone_name");
    while ($zones = tep_db_fetch_array($zones_query)) {
      $zones_array[] = array('id' => $zones['zone_id'],
                             'text' => $zones['zone_name']);
    }

    return $zones_array;
  }

  function tep_prepare_country_zones_pull_down($country_id = '') {
// preset the width of the drop-down for Netscape
    $pre = '';
    if ( (!tep_browser_detect('MSIE')) && (tep_browser_detect('Mozilla/4')) ) {
      for ($i=0; $i<45; $i++) $pre .= '&nbsp;';
    }

    $zones = tep_get_country_zones($country_id);

    if (sizeof($zones) > 0) {
      $zones_select = array(array('id' => '', 'text' => PLEASE_SELECT));
      $zones = array_merge($zones_select, $zones);
    } else {
      $zones = array(array('id' => '', 'text' => TYPE_BELOW));
// create dummy options for Netscape to preset the height of the drop-down
      if ( (!tep_browser_detect('MSIE')) && (tep_browser_detect('Mozilla/4')) ) {
        for ($i=0; $i<9; $i++) {
          $zones[] = array('id' => '', 'text' => $pre);
        }
      }
    }

    return $zones;
  }

////
// Get list of address_format_id's
  function tep_get_address_formats() {
    $address_format_query = tep_db_query("select address_format_id from " . TABLE_ADDRESS_FORMAT . " order by address_format_id");
    $address_format_array = array();
    while ($address_format_values = tep_db_fetch_array($address_format_query)) {
      $address_format_array[] = array('id' => $address_format_values['address_format_id'],
                                      'text' => $address_format_values['address_format_id']);
    }
    return $address_format_array;
  }

////
// Alias function for Store configuration values in the Administration Tool
  function tep_cfg_pull_down_xml_feed_price_type($default_id, $key = '') {
  	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
  	$price_type_array = array();
  	$price_type_array[] = array('id'=>'UP', 'text'=>'Unit cost');
  	$price_type_array[] = array('id'=>'MS', 'text'=>'Unit MSRP');
  	$price_type_array[] = array('id'=>'MA', 'text'=>'Minimum acceptable price');
  	$price_type_array[] = array('id'=>'SP', 'text'=>'Sales price');
    return tep_draw_pull_down_menu($name, $price_type_array, $default_id);
  }
  
  function tep_cfg_pull_down_country_list($country_id) {
    return tep_draw_pull_down_menu('configuration_value', tep_get_countries(), $country_id);
  }

  function tep_cfg_pull_down_zone_list($zone_id) {
    return tep_draw_pull_down_menu('configuration_value', tep_get_country_zones(STORE_COUNTRY), $zone_id);
  }

  function tep_cfg_pull_down_tax_classes($tax_class_id, $key = '') {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $tax_class_query = tep_db_query("select tax_class_id, tax_class_title from " . TABLE_TAX_CLASS . " order by tax_class_title");
    while ($tax_class = tep_db_fetch_array($tax_class_query)) {
      $tax_class_array[] = array('id' => $tax_class['tax_class_id'],
                                 'text' => $tax_class['tax_class_title']);
    }

    return tep_draw_pull_down_menu($name, $tax_class_array, $tax_class_id);
  }

////
// Function to read in text area in admin
 function tep_cfg_textarea($text) {
    return tep_draw_textarea_field('configuration_value', false, 35, 5, $text);
  }

  function tep_cfg_get_zone_name($zone_id) {
    $zone_query = tep_db_query("select zone_name from " . TABLE_ZONES . " where zone_id = '" . (int)$zone_id . "'");

    if (!tep_db_num_rows($zone_query)) {
      return $zone_id;
    } else {
      $zone = tep_db_fetch_array($zone_query);
      return $zone['zone_name'];
    }
  }
  
function tep_cfg_pull_down_POS_payment_methods($payment_method) {
	return tep_draw_pull_down_menu('configuration_value', tep_get_POS_payment_methods(), $payment_method);
}

function tep_cfg_get_POS_payment_method($payment_method) {
	return $payment_method;
}

function tep_get_POS_payment_methods(){
	$options = array();
	$dir = realpath(DIR_FS_DOCUMENT_ROOT . DIR_WS_ADMIN . DIR_WS_MODULES . 'payment');
	$options[] = array(
		'id' => '', 
		'text' => '', 
	);
	if ( is_dir($dir) ){
		if ( $dh = opendir($dir) ){
			while ( ( $file = readdir($dh) ) !== false ){
				if ( substr($file, -4)=='.php' ){
					$method = substr($file, 0, -4);
					$options[] = array(
						'id' => $method, 
						'text' => $method, 
					);
				}
			}
			closedir($dh);
		}
	}
	return $options;
}

////
// Sets the status of a banner
  function tep_set_banner_status($banners_id, $status) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_BANNERS . " set status = '1', expires_impressions = NULL, expires_date = NULL, date_status_change = NULL where banners_id = '" . $banners_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_BANNERS . " set status = '0', date_status_change = now() where banners_id = '" . $banners_id . "'");
    } else {
      return -1;
    }
  }

////
// Sets the status of a product
  function tep_set_product_status($products_id, $status) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '1', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '0', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
    } else {
      return -1;
    }
  }
  
//Category status MOD BEGIN by FIW
////
// Sets the status of a product
  function tep_set_category_status($categories_id, $status) {
    $categories = tep_get_category_tree($categories_id, '', '0', '', true);
        	for ($i=0, $n=sizeof($categories); $i<$n; $i++) {
        		$update_status = tep_db_query("update `categories` set `categories_status` = '".(int)$status."' where `categories_id` = '".(int)$categories[$i]['id']."' ");
        	}
  	if ($status == '1') {
      return tep_db_query("update " . TABLE_CATEGORIES . " set categories_status = '1', last_modified = now() where categories_id = '" . (int)$categories_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_CATEGORIES . " set categories_status = '0', last_modified = now() where categories_id = '" . (int)$categories_id . "'");
    } else {
      return -1;
    }

  }  
//Category Status MOD END by FIW


////
// Sets the status of a product on special
  function tep_set_specials_status($specials_id, $status) {

    if ($status == '1') {
      return tep_db_query("update " . TABLE_SPECIALS . " set status = '1', expires_date = NULL, date_status_change = NULL where specials_id = '" . (int)$specials_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_SPECIALS . " set status = '0', date_status_change = now() where specials_id = '" . (int)$specials_id . "'");
    } else {
      return -1;
    }
  }

////
// Sets timeout for the current script.
// Cant be used in safe mode.
  function tep_set_time_limit($limit) {
    if (!get_cfg_var('safe_mode')) {
      set_time_limit($limit);
    }
  }

////
// Alias function for Store configuration values in the Administration Tool
  function tep_cfg_select_option($select_array, $key_value, $key = '') {
    $string = '';

    for ($i=0, $n=sizeof($select_array); $i<$n; $i++) {
      $name = ((tep_not_null($key)) ? 'configuration[' . $key . ']' : 'configuration_value');

      $string .= '<br><input type="radio" name="' . $name . '" value="' . $select_array[$i] . '"';

      if ($key_value == $select_array[$i]) $string .= ' CHECKED';

      $string .= '> ' . $select_array[$i];
    }

    return $string;
  }

////
// Alias function for module configuration keys
  function tep_mod_select_option($select_array, $key_name, $key_value) {
    reset($select_array);
    while (list($key, $value) = each($select_array)) {
      if (is_int($key)) $key = $value;
      $string .= '<br><input type="radio" name="configuration[' . $key_name . ']" value="' . $key . '"';
      if ($key_value == $key) $string .= ' CHECKED';
      $string .= '> ' . $value;
    }

    return $string;
  }

////
// Retreive server information
  function tep_get_system_information() {
    global $HTTP_SERVER_VARS;

    $db_query = tep_db_query("select now() as datetime");
    $db = tep_db_fetch_array($db_query);

    list($system, $host, $kernel) = preg_split('/[\s,]+/', @exec('uname -a'), 5);

    return array('date' => tep_datetime_short(date('Y-m-d H:i:s')),
                 'system' => $system,
                 'kernel' => $kernel,
                 'host' => $host,
                 'ip' => gethostbyname($host),
                 'uptime' => @exec('uptime'),
                 'http_server' => $HTTP_SERVER_VARS['SERVER_SOFTWARE'],
                 'php' => PHP_VERSION,
                 'zend' => (function_exists('zend_version') ? zend_version() : ''),
                 'db_server' => DB_SERVER,
                 'db_ip' => gethostbyname(DB_SERVER),
                 'db_version' => 'MySQL ' . (function_exists('mysql_get_server_info') ? mysql_get_server_info() : ''),
                 'db_date' => tep_datetime_short($db['datetime']));
  }

  function tep_generate_category_path($id, $from = 'category', $categories_array = '', $index = 0) {
    global $languages_id;

    if (!is_array($categories_array)) $categories_array = array();

    if ($from == 'product') {
      $categories_query = tep_db_query("select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$id . "'");
      while ($categories = tep_db_fetch_array($categories_query)) {
        if ($categories['categories_id'] == '0') {
          $categories_array[$index][] = array('id' => '0', 'text' => TEXT_TOP);
        } else {
          $category_query = tep_db_query("select cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int)$categories['categories_id'] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");
          $category = tep_db_fetch_array($category_query);
          $categories_array[$index][] = array('id' => $categories['categories_id'], 'text' => $category['categories_name']);
          if ( (tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0') ) $categories_array = tep_generate_category_path($category['parent_id'], 'category', $categories_array, $index);
          $categories_array[$index] = array_reverse($categories_array[$index]);
        }
        $index++;
      }
    } elseif ($from == 'category') {
      $category_query = tep_db_query("select cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int)$id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");
      $category = tep_db_fetch_array($category_query);
      $categories_array[$index][] = array('id' => $id, 'text' => $category['categories_name']);
      if ( (tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0') ) $categories_array = tep_generate_category_path($category['parent_id'], 'category', $categories_array, $index);
    }

    return $categories_array;
  }

  function tep_output_generated_category_path($id, $from = 'category') {
    $calculated_category_path_string = '';
    $calculated_category_path = tep_generate_category_path($id, $from);
    for ($i=0, $n=sizeof($calculated_category_path); $i<$n; $i++) {
      for ($j=0, $k=sizeof($calculated_category_path[$i]); $j<$k; $j++) {
        $calculated_category_path_string .= $calculated_category_path[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
      }
      $calculated_category_path_string = substr($calculated_category_path_string, 0, -16) . '<br>';
    }
    $calculated_category_path_string = substr($calculated_category_path_string, 0, -4);

    if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = TEXT_TOP;

    return $calculated_category_path_string;
  }

  function tep_get_generated_category_path_ids($id, $from = 'category') {
    $calculated_category_path_string = '';
    $calculated_category_path = tep_generate_category_path($id, $from);
    for ($i=0, $n=sizeof($calculated_category_path); $i<$n; $i++) {
      for ($j=0, $k=sizeof($calculated_category_path[$i]); $j<$k; $j++) {
        $calculated_category_path_string .= $calculated_category_path[$i][$j]['id'] . '_';
      }
      $calculated_category_path_string = substr($calculated_category_path_string, 0, -1) . '<br>';
    }
    $calculated_category_path_string = substr($calculated_category_path_string, 0, -4);

    if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = TEXT_TOP;

    return $calculated_category_path_string;
  }

  function tep_remove_category($category_id) {
    $category_image_query = tep_db_query("select categories_image from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$category_id . "'");
    $category_image = tep_db_fetch_array($category_image_query);

    $duplicate_image_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where categories_image = '" . tep_db_input($category_image['categories_image']) . "'");
    $duplicate_image = tep_db_fetch_array($duplicate_image_query);

    if ($duplicate_image['total'] < 2) {
      if (file_exists(DIR_FS_CATALOG_IMAGES . $category_image['categories_image'])) {
        @unlink(DIR_FS_CATALOG_IMAGES . $category_image['categories_image']);
      }
    }

    tep_db_query("delete from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$category_id . "'");
    tep_db_query("delete from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . (int)$category_id . "'");

    if (USE_CACHE == 'true') {
      tep_reset_cache_block('categories');
      tep_reset_cache_block('also_purchased');
    }
  }

  /*function tep_remove_product($product_id) {
    $product_image_query = tep_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
    $product_image = tep_db_fetch_array($product_image_query);

    $duplicate_image_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " where products_image = '" . tep_db_input($product_image['products_image']) . "'");
    $duplicate_image = tep_db_fetch_array($duplicate_image_query);

    if ($duplicate_image['total'] < 2) {
      if (file_exists(DIR_FS_CATALOG_IMAGES . $product_image['products_image'])) {
        @unlink(DIR_FS_CATALOG_IMAGES . $product_image['products_image']);
      }
    }

    tep_db_query("delete from " . TABLE_SPECIALS . " where products_id = '" . (int)$product_id . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
    
    // BOF Separate Pricing Per Customer
    tep_db_query("delete from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" . (int)$product_id . "'");
// EOF Separate Pricing Per Customer
    tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$product_id . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$product_id . "'");
    tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where products_id = '" . (int)$product_id . "' or products_id like '" . (int)$product_id . "{%'");
    tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where products_id = '" . (int)$product_id . "' or products_id like '" . (int)$product_id . "{%'");

    $product_reviews_query = tep_db_query("select reviews_id from " . TABLE_REVIEWS . " where products_id = '" . (int)$product_id . "'");
    while ($product_reviews = tep_db_fetch_array($product_reviews_query)) {
      tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . (int)$product_reviews['reviews_id'] . "'");
    }
    tep_db_query("delete from " . TABLE_REVIEWS . " where products_id = '" . (int)$product_id . "'");

    if (USE_CACHE == 'true') {
      tep_reset_cache_block('categories');
      tep_reset_cache_block('also_purchased');
    }
  }*/
  function tep_remove_product($product_id) {
    // begin Bundled Products
    global $messageStack, $languages_id;
    tep_db_query("DELETE FROM " . TABLE_PRODUCTS_BUNDLES . " WHERE bundle_id = " . (int)$product_id);
    $bundle_check = tep_db_query('select p.products_model, pd.products_name from ' . TABLE_PRODUCTS . ' p, ' . TABLE_PRODUCTS_DESCRIPTION . ' pd, ' . TABLE_PRODUCTS_BUNDLES . ' pb where p.products_id = pd.products_id and pd.language_id = ' . (int)$languages_id . ' and p.products_id = pb.bundle_id and pb.subproduct_id = ' . (int)$product_id);
    // if product being deleted is contained in any bundles warn the user
    while ($bundle = tep_db_fetch_array($bundle_check)) {
      $messageStack->add_session(WARNING_PRODUCT_IN_BUNDLE . '(' . $bundle['products_model'] . ') ' . $bundle['products_name'], 'warning');
    }
    tep_db_query("DELETE FROM " . TABLE_PRODUCTS_BUNDLES . " WHERE subproduct_id = " . (int)$product_id);
    // end Bundled Products
    $product_image_query = tep_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
    $product_image = tep_db_fetch_array($product_image_query);

    $duplicate_image_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " where products_image = '" . tep_db_input($product_image['products_image']) . "'");
    $duplicate_image = tep_db_fetch_array($duplicate_image_query);

    if ($duplicate_image['total'] < 2) {
      if (file_exists(DIR_FS_CATALOG_IMAGES . $product_image['products_image'])) {
        @unlink(DIR_FS_CATALOG_IMAGES . $product_image['products_image']);
      }
    }

    tep_db_query("delete from " . TABLE_SPECIALS . " where products_id = '" . (int)$product_id . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
    
    // BOF Separate Pricing Per Customer
    tep_db_query("delete from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" . (int)$product_id . "'");
// EOF Separate Pricing Per Customer
    tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$product_id . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$product_id . "'");
    tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where products_id = '" . (int)$product_id . "' or products_id like '" . (int)$product_id . "{%'");
    tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where products_id = '" . (int)$product_id . "' or products_id like '" . (int)$product_id . "{%'");

    $product_reviews_query = tep_db_query("select reviews_id from " . TABLE_REVIEWS . " where products_id = '" . (int)$product_id . "'");
    while ($product_reviews = tep_db_fetch_array($product_reviews_query)) {
      tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . (int)$product_reviews['reviews_id'] . "'");
    }
    tep_db_query("delete from " . TABLE_REVIEWS . " where products_id = '" . (int)$product_id . "'");

    if (USE_CACHE == 'true') {
      tep_reset_cache_block('categories');
      tep_reset_cache_block('also_purchased');
    }
  }


  /*function tep_remove_order($order_id, $restock = false) {
    if ($restock == 'on') {
      $order_query = tep_db_query("select products_id, products_quantity from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$order_id . "'");
      while ($order = tep_db_fetch_array($order_query)) {
        tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = products_quantity + " . $order['products_quantity'] . ", products_ordered = products_ordered - " . $order['products_quantity'] . " where products_id = '" . (int)$order['products_id'] . "'");
      }
    }

    tep_db_query("delete from " . TABLE_ORDERS . " where orders_id = '" . (int)$order_id . "'");
    tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$order_id . "'");
    tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . (int)$order_id . "'");
    tep_db_query("delete from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . (int)$order_id . "'");
    tep_db_query("delete from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$order_id . "'");
  }*/
  
  function tep_remove_order($order_id, $restock = false) {
// begin Bundled Products
    function restock_bundle($bundle_id, $restock_qty) {
      $bundle_query = tep_db_query('select pb.subproduct_id, pb.subproduct_qty, p.products_bundle from ' . TABLE_PRODUCTS_BUNDLES . ' pb, ' . TABLE_PRODUCTS . ' p where p.products_id = pb.subproduct_id and bundle_id = ' . (int)$bundle_id);
      while ($bundle_info = tep_db_fetch_array($bundle_query)) {
        $qty_restocked = $bundle_info['subproduct_qty'] * $restock_qty;
        if ($bundle_info['products_bundle'] == 'yes') {
          restock_bundle($bundle_info['subproduct_id'], $qty_restocked);
        } else {
          tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = products_quantity + " . (int)$qty_restocked . ", products_ordered = products_ordered - " . (int)$qty_restocked . " where products_id = " . (int)$bundle_info['subproduct_id']);
        }
      }
      // reduce number of bundle sold
      tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered - " . (int)$restock_qty . " where products_id = " . (int)$bundle_id);
    } // end function restock_bundle
    if ($restock == 'on') {
      $order_query = tep_db_query("select o.products_id, o.products_quantity, p.products_bundle from " . TABLE_ORDERS_PRODUCTS . " o, " . TABLE_PRODUCTS . " p where o.products_id = p.products_id and orders_id = " . (int)$order_id);
      while ($order = tep_db_fetch_array($order_query)) {
        if ($order['products_bundle'] == 'yes') {
          restock_bundle($order['products_id'], $order['products_quantity']);
        } else {
          tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = products_quantity + " . (int)$order['products_quantity'] . ", products_ordered = products_ordered - " . (int)$order['products_quantity'] . " where products_id = '" . (int)$order['products_id'] . "'");
        }
      }
    }
// end Bundled Products
    tep_db_query("delete from " . TABLE_ORDERS . " where orders_id = '" . (int)$order_id . "'");
    tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$order_id . "'");
    tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . (int)$order_id . "'");
    tep_db_query("delete from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . (int)$order_id . "'");
    tep_db_query("delete from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$order_id . "'");
    ######## Points/Rewards Module V2.1rc2a BOF ##################
    tep_db_query("delete from " . TABLE_CUSTOMERS_POINTS_PENDING . " where orders_id = '" . (int)$order_id . "'");
    $sql = "optimize table " . TABLE_CUSTOMERS_POINTS_PENDING . "";
    ######## Points/Rewards Module V2.1rc2a EOF ##################
// MVS
    tep_db_query("delete from " . TABLE_ORDERS_SHIPPING . " where orders_id = '" . (int)$order_id . "'");
  }


  function tep_reset_cache_block($cache_block) {
    global $cache_blocks;

    for ($i=0, $n=sizeof($cache_blocks); $i<$n; $i++) {
      if ($cache_blocks[$i]['code'] == $cache_block) {
        if ($cache_blocks[$i]['multiple']) {
          if ($dir = @opendir(DIR_FS_CACHE)) {
            while ($cache_file = readdir($dir)) {
              $cached_file = $cache_blocks[$i]['file'];
              $languages = tep_get_languages();
              for ($j=0, $k=sizeof($languages); $j<$k; $j++) {
                $cached_file_unlink = preg_replace('/-language/', '-' . $languages[$j]['directory'], $cached_file);
                if (preg_match('/^' . $cached_file_unlink . '/', $cache_file)) {
                  @unlink(DIR_FS_CACHE . $cache_file);
                }
              }
            }
            closedir($dir);
          }
        } else {
          $cached_file = $cache_blocks[$i]['file'];
          $languages = tep_get_languages();
          for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
            $cached_file = preg_replace('/-language/', '-' . $languages[$i]['directory'], $cached_file);
            @unlink(DIR_FS_CACHE . $cached_file);
          }
        }
        break;
      }
    }
    @unlink(DIR_FS_CACHE . 'url.cache');
  }

  function tep_get_file_permissions($mode) {
// determine type
    if ( ($mode & 0xC000) == 0xC000) { // unix domain socket
      $type = 's';
    } elseif ( ($mode & 0x4000) == 0x4000) { // directory
      $type = 'd';
    } elseif ( ($mode & 0xA000) == 0xA000) { // symbolic link
      $type = 'l';
    } elseif ( ($mode & 0x8000) == 0x8000) { // regular file
      $type = '-';
    } elseif ( ($mode & 0x6000) == 0x6000) { //bBlock special file
      $type = 'b';
    } elseif ( ($mode & 0x2000) == 0x2000) { // character special file
      $type = 'c';
    } elseif ( ($mode & 0x1000) == 0x1000) { // named pipe
      $type = 'p';
    } else { // unknown
      $type = '?';
    }

// determine permissions
    $owner['read']    = ($mode & 00400) ? 'r' : '-';
    $owner['write']   = ($mode & 00200) ? 'w' : '-';
    $owner['execute'] = ($mode & 00100) ? 'x' : '-';
    $group['read']    = ($mode & 00040) ? 'r' : '-';
    $group['write']   = ($mode & 00020) ? 'w' : '-';
    $group['execute'] = ($mode & 00010) ? 'x' : '-';
    $world['read']    = ($mode & 00004) ? 'r' : '-';
    $world['write']   = ($mode & 00002) ? 'w' : '-';
    $world['execute'] = ($mode & 00001) ? 'x' : '-';

// adjust for SUID, SGID and sticky bit
    if ($mode & 0x800 ) $owner['execute'] = ($owner['execute'] == 'x') ? 's' : 'S';
    if ($mode & 0x400 ) $group['execute'] = ($group['execute'] == 'x') ? 's' : 'S';
    if ($mode & 0x200 ) $world['execute'] = ($world['execute'] == 'x') ? 't' : 'T';

    return $type .
           $owner['read'] . $owner['write'] . $owner['execute'] .
           $group['read'] . $group['write'] . $group['execute'] .
           $world['read'] . $world['write'] . $world['execute'];
  }

  function tep_remove($source) {
    global $messageStack, $tep_remove_error;

    if (isset($tep_remove_error)) $tep_remove_error = false;

    if (is_dir($source)) {
      $dir = dir($source);
      while ($file = $dir->read()) {
        if ( ($file != '.') && ($file != '..') ) {
          if (is_writeable($source . '/' . $file)) {
            tep_remove($source . '/' . $file);
          } else {
            $messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source . '/' . $file), 'error');
            $tep_remove_error = true;
          }
        }
      }
      $dir->close();

      if (is_writeable($source)) {
        rmdir($source);
      } else {
        $messageStack->add(sprintf(ERROR_DIRECTORY_NOT_REMOVEABLE, $source), 'error');
        $tep_remove_error = true;
      }
    } else {
      if (is_writeable($source)) {
        unlink($source);
      } else {
        $messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source), 'error');
        $tep_remove_error = true;
      }
    }
  }

////
// Output the tax percentage with optional padded decimals
  function tep_display_tax_value($value, $padding = TAX_DECIMAL_PLACES) {
    if (strpos($value, '.')) {
      $loop = true;
      while ($loop) {
        if (substr($value, -1) == '0') {
          $value = substr($value, 0, -1);
        } else {
          $loop = false;
          if (substr($value, -1) == '.') {
            $value = substr($value, 0, -1);
          }
        }
      }
    }

    if ($padding > 0) {
      if ($decimal_pos = strpos($value, '.')) {
        $decimals = strlen(substr($value, ($decimal_pos+1)));
        for ($i=$decimals; $i<$padding; $i++) {
          $value .= '0';
        }
      } else {
        $value .= '.';
        for ($i=0; $i<$padding; $i++) {
          $value .= '0';
        }
      }
    }

    return $value;
  }

  function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address,$filecontents='',$filename='', $filetype='') {
    if (SEND_EMAILS != 'true') return false;

    // Instantiate a new mail object
    $message = new email(array('X-Mailer: osCommerce'));

    // Build the text version
    $text = strip_tags($email_text);
    if (EMAIL_USE_HTML == 'true') {
      $message->add_html($email_text, $text);
    } else {
      $message->add_text($text);
    }

   if ($filecontents != '' && $filename != '' && $filetype != '') {
	$message->add_attachment($filecontents, $filename,$filetype);
}
    // Send message
    $message->build_message();
    $message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
  }

  function tep_get_tax_class_title($tax_class_id) {
    if ($tax_class_id == '0') {
      return TEXT_NONE;
    } else {
      $classes_query = tep_db_query("select tax_class_title from " . TABLE_TAX_CLASS . " where tax_class_id = '" . (int)$tax_class_id . "'");
      $classes = tep_db_fetch_array($classes_query);

      return $classes['tax_class_title'];
    }
  }

  function tep_banner_image_extension() {
    if (function_exists('imagetypes')) {
      if (imagetypes() & IMG_PNG) {
        return 'png';
      } elseif (imagetypes() & IMG_JPG) {
        return 'jpg';
      } elseif (imagetypes() & IMG_GIF) {
        return 'gif';
      }
    } elseif (function_exists('imagecreatefrompng') && function_exists('imagepng')) {
      return 'png';
    } elseif (function_exists('imagecreatefromjpeg') && function_exists('imagejpeg')) {
      return 'jpg';
    } elseif (function_exists('imagecreatefromgif') && function_exists('imagegif')) {
      return 'gif';
    }

    return false;
  }

////
// Wrapper function for round() for php3 compatibility
  function tep_round($value, $precision) {
    if (PHP_VERSION < 4) {
      $exp = pow(10, $precision);
      return round($value * $exp) / $exp;
    } else {
      return round($value, $precision);
    }
  }

////
// Add tax to a products price
  function tep_add_tax($price, $tax) {
    global $currencies;

    if (DISPLAY_PRICE_WITH_TAX == 'true') {
      return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) + tep_calculate_tax($price, $tax);
    } else {
      return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
    }
  }

// Calculates Tax rounding the result
  function tep_calculate_tax($price, $tax) {
    global $currencies;

    return tep_round($price * $tax / 100, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
  }

////
// Returns the tax rate for a zone / class
// TABLES: tax_rates, zones_to_geo_zones
  function tep_get_tax_rate($class_id, $country_id = -1, $zone_id = -1) {
    global $customer_zone_id, $customer_country_id;

    if ( ($country_id == -1) && ($zone_id == -1) ) {
      if (!tep_session_is_registered('customer_id')) {
        $country_id = STORE_COUNTRY;
        $zone_id = STORE_ZONE;
      } else {
        $country_id = $customer_country_id;
        $zone_id = $customer_zone_id;
      }
    }

    $tax_query = tep_db_query("select SUM(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za ON tr.tax_zone_id = za.geo_zone_id left join " . TABLE_GEO_ZONES . " tz ON tz.geo_zone_id = tr.tax_zone_id WHERE (za.zone_country_id IS NULL OR za.zone_country_id = '0' OR za.zone_country_id = '" . (int)$country_id . "') AND (za.zone_id IS NULL OR za.zone_id = '0' OR za.zone_id = '" . (int)$zone_id . "') AND tr.tax_class_id = '" . (int)$class_id . "' GROUP BY tr.tax_priority");
    if (tep_db_num_rows($tax_query)) {
      $tax_multiplier = 0;
      while ($tax = tep_db_fetch_array($tax_query)) {
        $tax_multiplier += $tax['tax_rate'];
      }
      return $tax_multiplier;
    } else {
      return 0;
    }
  }

////
// Returns the tax rate for a tax class
// TABLES: tax_rates
  function tep_get_tax_rate_value($class_id) {
    $tax_query = tep_db_query("select SUM(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " where tax_class_id = '" . (int)$class_id . "' group by tax_priority");
    if (tep_db_num_rows($tax_query)) {
      $tax_multiplier = 0;
      while ($tax = tep_db_fetch_array($tax_query)) {
        $tax_multiplier += $tax['tax_rate'];
      }
      return $tax_multiplier;
    } else {
      return 0;
    }
  }

  function tep_call_function($function, $parameter, $object = '') {
    if ($object == '') {
      return call_user_func($function, $parameter);
    } elseif (PHP_VERSION < 4) {
      return call_user_method($function, $object, $parameter);
    } else {
      return call_user_func(array($object, $function), $parameter);
    }
  }

  function tep_get_zone_class_title($zone_class_id) {
    if ($zone_class_id == '0') {
      return TEXT_NONE;
    } else {
      $classes_query = tep_db_query("select geo_zone_name from " . TABLE_GEO_ZONES . " where geo_zone_id = '" . (int)$zone_class_id . "'");
      $classes = tep_db_fetch_array($classes_query);

      return $classes['geo_zone_name'];
    }
  }

  function tep_cfg_pull_down_zone_classes($zone_class_id, $key = '') {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $zone_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $zone_class_query = tep_db_query("select geo_zone_id, geo_zone_name from " . TABLE_GEO_ZONES . " order by geo_zone_name");
    while ($zone_class = tep_db_fetch_array($zone_class_query)) {
      $zone_class_array[] = array('id' => $zone_class['geo_zone_id'],
                                  'text' => $zone_class['geo_zone_name']);
    }

    return tep_draw_pull_down_menu($name, $zone_class_array, $zone_class_id);
  }

  function tep_cfg_pull_down_order_statuses($order_status_id, $key = '') {
    global $languages_id;

    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $statuses_array = array(array('id' => '0', 'text' => TEXT_DEFAULT));
    $statuses_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int)$languages_id . "' order by orders_status_name");
    while ($statuses = tep_db_fetch_array($statuses_query)) {
      $statuses_array[] = array('id' => $statuses['orders_status_id'],
                                'text' => $statuses['orders_status_name']);
    }

    return tep_draw_pull_down_menu($name, $statuses_array, $order_status_id);
  }

  function tep_get_order_status_name($order_status_id, $language_id = '') {
    global $languages_id;

    if ($order_status_id < 1) return TEXT_DEFAULT;

    if (!is_numeric($language_id)) $language_id = $languages_id;

    $status_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . (int)$order_status_id . "' and language_id = '" . (int)$language_id . "'");
    $status = tep_db_fetch_array($status_query);

    return $status['orders_status_name'];
  }

////
// Return a random value
  function tep_rand($min = null, $max = null) {
    static $seeded;

    if (!$seeded) {
      mt_srand((double)microtime()*1000000);
      $seeded = true;
    }

    if (isset($min) && isset($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
  }

// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)
  function tep_convert_linefeeds($from, $to, $string) {
    if ((PHP_VERSION < "4.0.5") && is_array($from)) {
      return preg_replace('/(' . implode('|', $from) . ')/', $to, $string);
    } else {
      return str_replace($from, $to, $string);
    }
  }

  function tep_string_to_int($string) {
    return (int)$string;
  }

////
// Parse and secure the cPath parameter values
  function tep_parse_category_path($cPath) {
// make sure the category IDs are integers
    $cPath_array = array_map('tep_string_to_int', explode('_', $cPath));

// make sure no duplicate category IDs exist which could lock the server in a loop
    $tmp_array = array();
    $n = sizeof($cPath_array);
    for ($i=0; $i<$n; $i++) {
      if (!in_array($cPath_array[$i], $tmp_array)) {
        $tmp_array[] = $cPath_array[$i];
      }
    }

    return $tmp_array;
  }
  
  function tep_get_category_htc_title($category_id, $language_id) {
    $category_query = tep_db_query("select categories_htc_title_tag from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
    $category = tep_db_fetch_array($category_query);

    return $category['categories_htc_title_tag'];
  }
    
  function tep_get_category_htc_desc($category_id, $language_id) {
    $category_query = tep_db_query("select categories_htc_desc_tag from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
    $category = tep_db_fetch_array($category_query);

    return $category['categories_htc_desc_tag'];
  }
   
  function tep_get_category_htc_keywords($category_id, $language_id) {
    $category_query = tep_db_query("select categories_htc_keywords_tag from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
    $category = tep_db_fetch_array($category_query);

    return $category['categories_htc_keywords_tag'];
  }
  
  function tep_get_category_htc_description($category_id, $language_id) {
    $category_query = tep_db_query("select categories_htc_description from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
    $category = tep_db_fetch_array($category_query);

    return $category['categories_htc_description'];
  }

  function tep_get_products_head_title_tag($product_id, $language_id) {
    $product_query = tep_db_query("select products_head_title_tag from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_head_title_tag'];
  }
  
  function tep_get_products_tags($product_id, $language_id) {
    $product_query = tep_db_query("select products_tags from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_tags'];
  }

  function tep_get_products_head_desc_tag($product_id, $language_id) {
    $product_query = tep_db_query("select products_head_desc_tag from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_head_desc_tag'];
  }

  function tep_get_products_head_keywords_tag($product_id, $language_id) {
    $product_query = tep_db_query("select products_head_keywords_tag from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_head_keywords_tag'];
  }
  function tep_get_manufacturer_htc_title($manufacturer_id, $language_id) {
    $manufacturer_query = tep_db_query("select manufacturers_htc_title_tag from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
    $manufacturer = tep_db_fetch_array($manufacturer_query);

    return $manufacturer['manufacturers_htc_title_tag'];
  }
    
  function tep_get_manufacturer_htc_desc($manufacturer_id, $language_id) {
    $manufacturer_query = tep_db_query("select manufacturers_htc_desc_tag from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
    $manufacturer = tep_db_fetch_array($manufacturer_query);

    return $manufacturer['manufacturers_htc_desc_tag'];
  }
   
  function tep_get_manufacturer_htc_keywords($manufacturer_id, $language_id) {
    $manufacturer_query = tep_db_query("select manufacturers_htc_keywords_tag from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
    $manufacturer = tep_db_fetch_array($manufacturer_query);

    return $manufacturer['manufacturers_htc_keywords_tag'];
  } 
   
  function tep_get_manufacturer_htc_description($manufacturer_id, $language_id) {
    $manufacturer_query = tep_db_query("select manufacturers_htc_description from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
    $manufacturer = tep_db_fetch_array($manufacturer_query);

    return $manufacturer['manufacturers_htc_description'];
  }  
  
  // Function to reset SEO URLs database cache entries 
// Ultimate SEO URLs v2.1
function tep_reset_cache_data_seo_urls($action){	
	switch ($action){
		case 'reset':
			tep_db_query("DELETE FROM cache WHERE cache_name LIKE '%seo_urls%'");
			tep_db_query("UPDATE configuration SET configuration_value='false' WHERE configuration_key='SEO_URLS_CACHE_RESET'");
			break;
		default:
			break;
	}
	# The return value is used to set the value upon viewing
	# It's NOT returining a false to indicate failure!!
	return 'false';
}
 
 function tep_set_categories_status($category_id) {
	$parent_cat_query = tep_db_query("select categories_id from categories where parent_id='" . (int)$category_id . "' and categories_status='1'");

if(tep_db_num_rows($parent_cat_query) > 0){ 
	while ($parent=tep_db_fetch_array($parent_cat_query)) {
	 $categories = tep_get_category_tree($parent['categories_id'], '', '0', '', true);
	 $categories = array_reverse($categories);
	       	for ($i=0, $n=sizeof($categories); $i<$n; $i++) { 		
        		$products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '" . (int)$categories[$i]['id']. "'");
    		$products = tep_db_fetch_array($products_query);
    		$products_count = $products['total'];
    		 if($products_count <= 0 ){
		 $categories_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$categories[$i]['id']. "' and categories_status = '1'");
		 $categ = tep_db_fetch_array($categories_query);
		 $categ_count = $categ['total'];
		 if($categ_count <= 0){
			tep_db_query("update " . TABLE_CATEGORIES . " set categories_status = '0', last_modified = now() where categories_id = '" . (int)$categories[$i]['id'] . "'");
	
		 }
		 }
       }
      		
     }
   }
}

 function tep_get_category_tree2($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
    global $languages_id;

    if (!is_array($category_tree_array)) $category_tree_array = array();
    if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

    if ($include_itself) {
      $category_tree_array[] = $parent_id;
    }

    $categories_query = tep_db_query("select c.categories_id, c.parent_id from " . TABLE_CATEGORIES . " c where  c.parent_id = '" . (int)$parent_id . "' order by c.sort_order");
    while ($categories = tep_db_fetch_array($categories_query)) {
      if ($exclude != $categories['categories_id']) $category_tree_array[] = $categories['categories_id'];
      $category_tree_array = tep_get_category_tree2($categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
    }

    return $category_tree_array;
  }
  //bof featured category 06-MARCH-2014
  function tep_get_category_tree3($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
    global $languages_id;

    if (!is_array($category_tree_array)) $category_tree_array = array();
    if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

    if ($include_itself) {
      $category_query = tep_db_query("select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.language_id = '" . (int)$languages_id . "' and cd.categories_id = '" . (int)$parent_id . "'");
      $category = tep_db_fetch_array($category_query);
      $category_tree_array[] = array('id' => $parent_id, 'text' => $category['categories_name']);
    }

    $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
    while ($categories = tep_db_fetch_array($categories_query)) {
      if ($exclude != $categories['categories_id']) $category_tree_array[] = array('id' => 0, 'text' => $spacing . $categories['categories_name']);
      $category_tree_array = tep_get_category_tree($categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
    }

    return $category_tree_array;
  }
  function tep_draw_pull_down_menu3($name, $values, $default = '', $parameters = '', $required = false) {
    $field = '<select name="' . tep_output_string($name) . '"';

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    if (empty($default) && isset($GLOBALS[$name])) $default = stripslashes($GLOBALS[$name]);

    for ($i=0, $n=sizeof($values); $i<$n; $i++) {
     if(tep_output_string($values[$i]['id'])==0)
     {
      $field .= '<optgroup label="';
      

      $field .=  tep_output_string($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '"></optgroup>';
    }
 else {
     $field .= '<option value="' . tep_output_string($values[$i]['id']) . '"';
      if ($default == $values[$i]['id']) {
        $field .= ' SELECTED';
      }

      $field .= '>' . tep_output_string($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
    }    
    }
    
    $field .= '</select>';

    if ($required == true) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }
  //eof featured category 06-MARCH-2014
/*  
  // ###### Added CCGV Contribution #########
// ICW Credit class gift voucher begin
////
// Sets the status of a coupon
  function tep_set_coupon_status($coupon_id, $status) {
    if ($status == 'Y') {
      return tep_db_query("update " . TABLE_COUPONS . " set coupon_active = 'Y', date_modified = now() where coupon_id = '" . (int)$coupon_id . "'");
    } elseif ($status == 'N') {
      return tep_db_query("update " . TABLE_COUPONS . " set coupon_active = 'N', date_modified = now() where coupon_id = '" . (int)$coupon_id . "'");
    } else {
      return -1;
    }
  }
// ICW Credit class gift voucher end
// ###### end CCGV Contribution #########
*/
 // USPS Methods 3.0
// Alias function for Store configuration values in the Administration Tool
  function tep_cfg_select_multioption($select_array, $key_value, $key = '') {
    for ($i=0; $i<sizeof($select_array); $i++) {
      $name = (($key) ? 'configuration[' . $key . '][]' : 'configuration_value');
      $string .= '<br><input type="checkbox" name="' . $name . '" value="' . $select_array[$i] . '"';
      $key_values = explode( ", ", $key_value);
      if ( in_array($select_array[$i], $key_values) ) $string .= ' CHECKED';
      $string .= '> ' . $select_array[$i];
    }
    $string .= '<input type="hidden" name="' . $name . '" value="--none--">';
    return $string;
  }
  
   function tep_draw_pull_multiselect_menu($name, $values, $defaults, $parameters = '') {
    $field = '<select multiple name="' . tep_output_string($name) . '"';

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>' . "\n";

    if (empty($default) && isset($GLOBALS[$name])) $default = stripslashes($GLOBALS[$name]);

    for ($i = 0, $n = sizeof($values); $i < $n; $i++) {
      $field .= '<option value="' . tep_output_string($values[$i]['id']) . '"';
      if (in_array($values[$i]['id'], $defaults)) {
        $field .= ' SELECTED';
      }

      $field .= '>' . tep_output_string($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
    }
    $field .= '</select>';

    return $field;
  }
  
   function tep_get_categories($categories_array = '', $parent_id = '0', $indent = '') {
    global $languages_id;

    if (!is_array($categories_array)) $categories_array = array();

    $categories_query = tep_db_query("select c.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where parent_id = '" . (int)$parent_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by cd.categories_name, sort_order");
    while ($categories = tep_db_fetch_array($categories_query)) {
      $categories_array[] = array('id' => $categories['categories_id'],
                                  'text' => $indent . $categories['categories_name']);

      if ($categories['categories_id'] != $parent_id) {
        $categories_array = tep_get_categories($categories_array, $categories['categories_id'], $indent . '&nbsp;&nbsp;');
      }
    }

    return $categories_array;
  }

//allows selecting many categories a ta time
  function tep_cfg_select_multicategories($key_value = '', $key) {
					 if (tep_not_null($key_value))
					   $value_array = explode(', ',$key_value);
					 else
					   $value_array = array();

      $name = ((tep_not_null($key)) ? 'configuration[' . $key . ']' : 'configuration_value').'[]';

					 $string = tep_draw_pull_multiselect_menu($name,tep_get_categories(), $value_array,' size="8" ');

    return $string;
  }

	function tep_cfg_show_multicategories($key_value = ''){
		global  $languages_id;

   $cat_str = '';

		  if (tep_not_null($key_value)){
					 $value_array = explode(', ',$key_value);
					 for($i=0, $x=sizeof($value_array); $i<$x; $i++){
              	$cat_str .= tep_get_category_name((int)$value_array[$i], $languages_id).', ';
					 }
      		 $cat_str = substr($cat_str, 0, -2);
			}

		return $cat_str;
	}
	
		function apply_roundoff($price_value){	
			$pos = strpos($price_value, '.');
			if ($pos===false){
				$response = ($price_value-1) . '.99'; 
			}
			else{
				$response = $price_value;
				$value_parts = explode('.', $response);
				if (strlen($value_parts[1])>2){
					$value_parts[1] = substr($value_parts[1], 0, 2);
				}
				$response = $value_parts[0] . '.' . ($value_parts[1] + (99 - $value_parts[1]));
				
			}
			return $response;
		}
		
  function tep_cfg_pull_down_orders_status($orders_status_id) {
  	return tep_draw_pull_down_menu('configuration_value', tep_get_orders_status(), $orders_status_id);
    //return tep_draw_pull_down_menu('configuration_value', tep_get_countries(), $country_id);
  } 
  
  //////create a pull down for all payment installed payment methods for Order Editor configuration
   
  // Get list of all payment modules available
  function tep_cfg_pull_down_payment_methods() {
  global $language;
  $enabled_payment = array();
  $module_directory = DIR_FS_CATALOG_MODULES . 'payment/';
  $file_extension = '.php';

  if ($dir = @dir($module_directory)) {
    while ($file = $dir->read()) {
      if (!is_dir( $module_directory . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          $directory_array[] = $file;
        }
      }
    }
    sort($directory_array);
    $dir->close();
  }

  // For each available payment module, check if enabled
  for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) {
    $file = $directory_array[$i];

    include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/' . $file);
    include($module_directory . $file);

    $class = substr($file, 0, strrpos($file, '.'));
    if (tep_class_exists($class)) {
      $module = new $class;
      if ($module->check() > 0) {
        // If module enabled create array of titles
      	$enabled_payment[] = array('id' => $module->title, 'text' => $module->title);
		
      }
   }
 }
 				
    $enabled_payment[] = array('id' => 'Other', 'text' => 'Other');	
		
		//draw the dropdown menu for payment methods and default to the order value
	  return tep_draw_pull_down_menu('configuration_value', $enabled_payment, '', ''); 
		}


/////end payment method dropdown
//BOF:range_manager
function get_shooting_ranges(){
    $response = array();
    $response[] = array('id'=>'', 'text'=>'-- Select Range --');
	$ranges_query = tep_db_query("select ranges_name, ranges_id from ranges where status='1' order by ranges_name");
    while ($entry = tep_db_fetch_array($ranges_query)){
        $response[] = array('id'=>$entry['ranges_id'], 'text'=>$entry['ranges_name']);
    }
    return $response; 
}

function get_shooting_lanes_by_range_id($range_id = ''){
    $response = array();
    $response[] = array('id'=>'', 'text'=>'-- Select Lane --');
    if (!empty($range_id)){
        $ranges_query = tep_db_query("select lanes_name, lanes_id from lanes where ranges_id='" . (int)$range_id . "' and status='1' order by sort_order, lanes_name");
        while ($entry = tep_db_fetch_array($ranges_query)){
            $oID = '';
            $range_order_ref = '';
            $start = '';
            $stop = '';
            $interval = '0';
            $product_id = ''; 
            $product_price = ''; 
            $calculate_via = '';
            $open_order_query = tep_db_query("select id, orders_id, timer_start, timer_stop from range_orders where range_id='" . (int)$range_id . "' and lane_id='" . (int)$entry['lanes_id'] . "' and is_active='1'");
            if (tep_db_num_rows($open_order_query)){
                $info = tep_db_fetch_array($open_order_query);
                $oID = $info['orders_id'];
                $range_order_ref = $info['id'];
                $start = $info['timer_start'];
                $stop = $info['timer_stop'];
                if (!empty($start)){
                    if (!empty($stop))
                        $interval = abs($stop - $start);
                    else
                        $interval = abs(time() - $start);
                }

                $item_query = tep_db_query("select p.products_id, p.base_price as price, r.calculate_via from products p, range_orders ro, ranges r where ro.id='" . (int)$range_order_ref . "' and ro.range_id=r.ranges_id and p.range_id=ro.range_id and p.lane_id=ro.lane_id and p.is_lane_item='1'");
                if (tep_db_num_rows($item_query)){
                    $item_info = tep_db_fetch_array($item_query);
                    $product_id =$item_info['products_id']; 
                    $product_price = $item_info['price']; 
                    $calculate_via = $item_info['calculate_via'];
                }
            }
            $response[] = array(
                'id'=>$entry['lanes_id'], 
                'text'=>$entry['lanes_name'], 
                'oID' => $oID, 
                'range_order_ref' => $range_order_ref, 
                'start' => $start, 
                'stop' => $stop,
                'interval' => $interval,
                'lane_id' => $entry['lanes_id'],
                'range_id' => $range_id,
                'product_id' => $product_id, 
                'product_price' => $product_price, 
                'calculate_via' => $calculate_via,
            );
        }
    }
    return $response; 
}

function get_shooting_lanes_by_range_id_html($range_id){
    $html = '';
    $entries = get_shooting_lanes_by_range_id($range_id);
    foreach($entries as $entry){
        $html .= '<option value="' . $entry['id'] . '">' . $entry['text'] . '</option>';
    }
    return $html;
}

function create_preliminary_order($range_id, $lane_id){
    $sql_data = array(
        'range_id' => $range_id, 
        'lane_id' => $lane_id, 
        'timer_start' => time(), 
        'last_modified' => time(), 
    );
    tep_db_perform('open_orders', $sql_data);
    return tep_db_insert_id();
}

/*function get_shooting_lanes_by_range_id_timewise($range_id){
    global $currencies;
    $response = array();
    if (!empty($range_id)){
        $ranges_query = tep_db_query("select l.lanes_name, l.lanes_id, p.products_id, p.products_model, p.products_price from lanes l inner join products p on p.lane_id=l.lanes_id where l.ranges_id='" . (int)$range_id . "' and l.status='1' and p.is_fullday_price='0' order by l.sort_order, l.lanes_name");
        while ($entry = tep_db_fetch_array($ranges_query)){
            $response[] = array(
                'range_id' => $range_id, 
                'lane_id' => $entry['lanes_id'], 
                'lane_name' => $entry['lanes_name'], 
                'product_id' => $entry['products_id'], 
                'model' => $entry['products_model'],
                //'custom_model' => $range_id . '_' . $entry['lanes_id'] . '_' . (stripos($entry['products_model'], 'full')!==false ? 'FULL' : ''),
                'custom_model' => $range_id . '_' . $entry['lanes_id'], 
                'price' => $entry['products_price'], 
                'formatted_price' => $currencies->format($entry['products_price']),  
            );
        }
    }
    return $response;
}*/

function get_states_by_country_id($country_id){
    $response = array(
        'id' => '', 
        'text' => '-- Select State --',
    );
    $states_query = teb_db_query("select zone_id, zone_code, zone_name from zones where zone_country_id='" . (int)$country_id . "'");
    while ($entry = tep_db_fetch_array($states_query)){
        $response[] = array(
            'id' => $entry['zone_id'], 
            'text' => $entry['zone_name'] . ' (' . $entry['zone_code'] . ')',  
        );
    }
    return $response;
}
 
//EOF:range_manager
//BOF:authorization_check
function is_authorized($functionality){
    global $login_groups_id;
    
    $sql = tep_db_query("select admin_files_id from admin_files where is_functionality='1' and admin_files_name='" . tep_db_input($functionality) . "' and instr(admin_groups_id, '" . $login_groups_id . "') > 0");
    if (!tep_db_num_rows($sql)){
        return false;
    } else {
        return true;
    }
}

function handle_authorization($file_name){
    switch(basename($file_name)){
        case 'orders_POS.php': 
        case 'create_order_process_POS.php':
            //POS functionality
            if (!is_authorized('POS')){
                tep_redirect(tep_href_link('not_authorized.php'));
            }
            break;
        case 'ranges_manager.php':
        case 'lanes_manager.php':
        case 'range_operations.php':
            //Ranges POS
            if (!is_authorized('RangeManager')){
                tep_redirect(tep_href_link('not_authorized.php'));
            }
            break;
    }
}
//EOF:authorization_check
//BOF:fraud_prevention
/*similar functions showing under front-end's functions/general.php file*/
function fraud_prevention_is_negative($order_id){
    global $like_string;
    if (FRAUD_PREVENTION_FUNCTIONALITY_STATUS=='1'){
        $order_query = tep_db_query("select o.customers_name as name, concat(o.customers_street_address, o.customers_suburb) as customer_address, o.customers_telephone as telephone, concat(o.billing_street_address, o.billing_suburb) as billing_address, concat(o.delivery_street_address, o.delivery_suburb) as shipping_address, o.customers_country as country, o.customers_email_address as email, o.shipping_module, o.ip_address, ot.value as subtotal from orders o left join orders_total ot on (o.orders_id=ot.orders_id and ot.class='ot_subtotal') where o.orders_id='" . (int)$order_id . "'");
        if (tep_db_num_rows($order_query)){
            $order = tep_db_fetch_array($order_query);
            
            $like_string = '';
            $fraud_ship_methods_string = FRAUD_PREVENTION_SHIPPING_METHODS;
            if (!empty($fraud_ship_methods_string)){
                if (!empty($order['shipping_module'])){
                    list($ship_method, ) = explode('_', $order['shipping_module']);
                    if (!empty($ship_method)){
                        $fraud_ship_methods = explode(',', $fraud_ship_methods_string);
                        if (in_array($ship_method, $fraud_ship_methods)){
                            return false;
                        }
                    }
                }
            }
            
            $like_string = '';
            $fraud_countries_string = FRAUD_PREVENTION_COUNTRIES;
            if (!empty($fraud_countries_string)){
                if (!empty($order['country'])){
                    $country_query = tep_db_query("select countries_id as id from countries where countries_name='" . tep_db_input($order['country']) . "'");
                    if (tep_db_num_rows($country_query)){
                        $country = tep_db_fetch_array($country_query);
                        $fraud_countries = explode(',', $fraud_countries_string);
                        if (in_array($country['id'], $fraud_countries)){
                            return false;
                        }
                    }
                }
            }
            
            $like_string = '';
            $fraud_ip_addresses_string = FRAUD_PREVENTION_IP_ADDRESSES;
            if (!empty($fraud_ip_addresses_string)){
                if (!empty($order['ip_address'])){
                    list($op1, $op2, $op3, $op4) = explode('.', $order['ip_address']); 
                    $fraud_ip_addresses = explode(',', $fraud_ip_addresses_string);
                    foreach($fraud_ip_addresses as $ip){
                        list($p1, $p2, $p3, $p4) = explode('.', $ip);
                        if ($p1==$op1 || $p1=='*'){
                            if ($p2==$op2 || $p2=='*'){
                                if ($p3==$op3 || $p3=='*'){
                                    if ($p4==$op4 || $p4=='*'){
                                        return false;
                                        break;
                                    }
                                }
                            }
                        } 
                    }
                }
            }
            
            $like_string = '';
            $fraud_customer_names_string = FRAUD_PREVENTION_CUSTOMER_NAMES;
            if (!empty($fraud_customer_names_string)){
                if (!empty($order['name'])){
                    $like_string = '';

                    
                    $fraud_customer_names = explode(',', $fraud_customer_names_string);
                    array_walk($fraud_customer_names, 'set_query_names_compatible');
                    if (!empty($like_string)){
                        $like_string = substr($like_string, 0, -4);
                        $custmer_names_query = tep_db_query("select orders_id from orders where orders_id='" . (int)$order_id . "' and (" . $like_string . ")");
                        if (tep_db_num_rows($custmer_names_query)){
                            return false;
                        }
                    }
                    
                }
            }
            
            $like_string = '';
            $fraud_address_string = FRAUD_PREVENTION_ADDRESSES;
            if (!empty($fraud_address_string)){
                if (!empty($order['customer_address'])){
                    $like_string = '';

                    $fraud_adresses = explode(',', $fraud_address_string);
                    array_walk($fraud_adresses, 'set_query_addresses_compatible');
                    if (!empty($like_string)){
                        $like_string = substr($like_string, 0, -4);
                        $addresses_query = tep_db_query("select orders_id from orders where orders_id='" . (int)$order_id . "' and (" . $like_string . ")");
                        if (tep_db_num_rows($addresses_query)){
                            return false;
                        }
                    }
                }
            }
            
            $like_string = '';
            $dollar_value = FRAUD_PREVENTION_DOLLAR_VALUE;
            if (!empty($dollar_value)){
                if (!empty($order['subtotal'])){
                    if ($order['subtotal']>$dollar_value){
                        return false;
                    }
                }
            }
            
            $like_string = '';
            $fraud_email_addresses_string = FRAUD_PREVENTION_EMAIL_ADDRESSES;
            if (!empty($fraud_email_addresses_string)){
                if (!empty($order['email'])){
                    $fraud_email_addresses = explode(',', $fraud_email_addresses_string);
                    array_walk($fraud_email_addresses, 'set_query_emails_compatible');
                    if (!empty($like_string)){
                        $like_string = substr($like_string, 0, -4);
                        $emails_query = tep_db_query("select orders_id from orders where orders_id='" . (int)$order_id . "' and (" . $like_string . ")");
                        if (tep_db_num_rows($emails_query)){
                            return false;
                        }
                    }
                    
                }
            }
            
            $like_string = '';
            $check_address_mismatch = FRAUD_PREVENTION_SHIPPING_BILLING_MISMATCH;
            if ($check_address_mismatch=='1'){
                if ($order['billing_address']!=$order['shipping_address']){
                    return false;
                }
            }
            
            $like_string = '';
            $fraud_telephone_numbers_string = FRAUD_PREVENTION_PHONE_NUMBERS;
            if (!empty($fraud_telephone_numbers_string)){
                if (!empty($order['telephone'])){
                    $like_string = '';

                    
                    $fraud_telephone_numbers = explode(',', $fraud_telephone_numbers_string);
                    array_walk($fraud_telephone_numbers, 'set_query_telephone_numbers_compatible');
                    if (!empty($like_string)){
                        $like_string = substr($like_string, 0, -4);
                        $custmer_names_query = tep_db_query("select orders_id from orders where orders_id='" . (int)$order_id . "' and (" . $like_string . ")");
                        if (tep_db_num_rows($custmer_names_query)){
                            return false;
                        }
                    }
                    
                }
            }
            
        }
    }
    return true;
}

function set_query_names_compatible(&$val){
	global $like_string;
	
	$like_string .= " customers_name like '%" . $val . "%' or ";
}

function set_query_addresses_compatible(&$val){
	global $like_string;
	
	$like_string .= " customers_street_address like '%" . $val . "%' or customers_suburb like '%" . $val . "%' or ";
}

function set_query_emails_compatible(&$val){
	global $like_string;
	$like_string .= " customers_email_address='" . $val . "' or ";
}

function set_query_telephone_numbers_compatible(&$val){
	global $like_string;
	
	$like_string .= " customers_telephone='" . $val . "' or ";
}
//EOF:fraud_prevention

// #14 12jan2014 (MA) BOF
  function tep_validate_ip_address($ip_address) {
    if (function_exists('filter_var') && defined('FILTER_VALIDATE_IP')) {
      return filter_var($ip_address, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_IPV4));
    }

    if (preg_match('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/', $ip_address)) {
      $parts = explode('.', $ip_address);

      foreach ($parts as $ip_parts) {
        if ( (intval($ip_parts) > 255) || (intval($ip_parts) < 0) ) {
          return false; // number is not within 0-255
        }
      }

      return true;
    }

    return false;
  }

  function tep_get_ip_address() {
    global $HTTP_SERVER_VARS;

    $ip_address = null;
    $ip_addresses = array();

    if (isset($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR']) && !empty($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'])) {
      foreach ( array_reverse(explode(',', $HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'])) as $x_ip ) {
        $x_ip = trim($x_ip);

        if (tep_validate_ip_address($x_ip)) {
          $ip_addresses[] = $x_ip;
        }
      }
    }

    if (isset($HTTP_SERVER_VARS['HTTP_CLIENT_IP']) && !empty($HTTP_SERVER_VARS['HTTP_CLIENT_IP'])) {
      $ip_addresses[] = $HTTP_SERVER_VARS['HTTP_CLIENT_IP'];
    }

    if (isset($HTTP_SERVER_VARS['HTTP_X_CLUSTER_CLIENT_IP']) && !empty($HTTP_SERVER_VARS['HTTP_X_CLUSTER_CLIENT_IP'])) {
      $ip_addresses[] = $HTTP_SERVER_VARS['HTTP_X_CLUSTER_CLIENT_IP'];
    }

    if (isset($HTTP_SERVER_VARS['HTTP_PROXY_USER']) && !empty($HTTP_SERVER_VARS['HTTP_PROXY_USER'])) {
      $ip_addresses[] = $HTTP_SERVER_VARS['HTTP_PROXY_USER'];
    }

    $ip_addresses[] = $HTTP_SERVER_VARS['REMOTE_ADDR'];

    foreach ( $ip_addresses as $ip ) {
      if (!empty($ip) && tep_validate_ip_address($ip)) {
        $ip_address = $ip;
        break;
      }
    }

    return $ip_address;
  }
  
  function tep_get_action_recorder($user_name, $user_id, $ip_address, $success = '0'){
      $module = 'ar_admin_login';
      tep_db_query("insert into action_recorder (module, user_id, user_name, identifier, success, date_added) values ('" . $module . "', '" . (int)$user_id . "', '" . tep_db_input($user_name) . "', '" . tep_db_input($ip_address) . "', '" . $success . "', now())");
      
  }
  // #14 12jan2014 (MA) EOF
  
  function link_get_variable($var_name)
  {
    // Map global to GET variable
    if (isset($_GET[$var_name]))
    {
      $GLOBALS[$var_name] =& $_GET[$var_name];
    }
  }

  function link_post_variable($var_name)
  {
    // Map global to POST variable
    if (isset($_POST[$var_name]))
    {
      $GLOBALS[$var_name] =& $_POST[$var_name];
    }
  }
  
  function get_customer_groups(){
    $response = array();
    $response[] = array('id'=>'', 'text'=>'-- Select Customer Group --');
    $query = tep_db_query("select customers_group_id, customers_group_name from customers_groups order by customers_group_name");
    while ($entry = tep_db_fetch_array($query)){
        $response[] = array('id'=>$entry['customers_group_id'], 'text'=>$entry['customers_group_name']);
    }
    return $response;
  }
  
  function get_admin_viewable_functionality(){
    $response = array();
    
    /*$admin_group_query = tep_db_query("select admin_groups_id from admin where admin_id='" . $_SESSION['login_id'] . "'");
    if (tep_db_num_rows($admin_group_query)){
        $info = tep_db_fetch_array($admin_group_query);
        $group_id = $info['admin_groups_id'];
    }*/
    $group_id = $_SESSION['login_groups_id'];
    
    if ($group_id=='1'){
        $response = array('pos', 'rangemanager', 'amazon', 'ebay');
        return $response;
    }
    
    if (!empty($group_id)){
        $functionality_query = tep_db_query("select admin_files_name, admin_groups_id from admin_files where is_functionality='1' and (admin_files_name='pos' or admin_files_name='rangemanager' or admin_files_name='amazon' or admin_files_name='ebay' )");
        while($functionality = tep_db_fetch_array($functionality_query)){
            $pos = strpos($functionality['admin_groups_id'], ',' . $group_id);
            switch(strtolower($functionality['admin_files_name'])){
                case 'pos':
                    if ($pos!==false) $response[] = 'pos';
                    break;
                case 'rangepos':
                    if ($pos!==false) $response[] = 'rangepos';
                    break;
                case 'amazon':
                    if ($pos!==false) $response[] = 'amazon';
                    break;
                case 'ebay':
                    if ($pos!==false) $response[] = 'ebay';
                    break;
            }
        }
    }
    return $response;
  }
  
  function get_google_category_path($child_category_id, $path='', $delimiter = ' > '){
	$sql = tep_db_query("select category_level, category_name, parent_category_id from google_categories where category_id='" . (int)$child_category_id  . "'");
	if (tep_db_num_rows($sql)){
		$info = tep_db_fetch_array($sql);
		$path = $delimiter . $info['category_name'] . $path;
		if($info['category_level']=='1'){
			return substr($path, strlen($delimiter));
		} else {
			return get_google_category_path($info['parent_category_id'], $path, $delimiter);
		}
	}
	return false;
  }
  
    function get_ebay_category_path($child_category_id, $path='', $delimiter = ' > '){
	$sql = tep_db_query("select category_level, category_name, parent_category_id from ebay_categories where category_id='" . (int)$child_category_id  . "'");
	if (tep_db_num_rows($sql)){
		$info = tep_db_fetch_array($sql);
		$path = $delimiter . $info['category_name'] . $path;
		if($info['category_level']=='1'){
			return substr($path, strlen($delimiter));
		} else {
			return get_ebay_category_path($info['parent_category_id'], $path, $delimiter);
		}
	}
	return false;
  }
  
  function associate_ebay_category_with_products($osc_category_id, $ebay_category_id){
    $products_query = tep_db_query("select products_id from products_to_categories where categories_id='" . (int)$osc_category_id . "'");
    if (tep_db_num_rows($products_query)){
        while($product = tep_db_fetch_array($products_query)){
            tep_db_query("update products set ebay_category_id='" . (int)$ebay_category_id . "' where products_id='" . (int)$product['products_id'] . "'");
        }
    }
    $categories_query = tep_db_query("select categories_id from categories where parent_id='" . (int)$osc_category_id . "'");
    if (tep_db_num_rows($categories_query)){
        while($category = tep_db_fetch_array($categories_query)){
            tep_db_query("update categories set ebay_category_id='" . (int)$ebay_category_id . "' where categories_id='" . (int)$category['categories_id'] . "'");
            associate_ebay_category_with_products($category['categories_id'], $ebay_category_id);
        } 
    }
  }
  
  function associate_google_category_with_products($osc_category_id, $google_category_id, $google_category_path = ''){
    if (empty($google_category_path)){
        $google_category_path = get_google_category_path($google_category_id);
    }
    $products_query = tep_db_query("select products_id from products_to_categories where categories_id='" . (int)$osc_category_id . "'");
    if (tep_db_num_rows($products_query)){
        while($product = tep_db_fetch_array($products_query)){
            tep_db_query("update products set google_category_id='" . (int)$google_category_id . "', google_category_path='" . tep_db_input($google_category_path) . "' where products_id='" . (int)$product['products_id'] . "'");
        }
    }
    $categories_query = tep_db_query("select categories_id from categories where parent_id='" . (int)$osc_category_id . "'");
    if (tep_db_num_rows($categories_query)){
        while($category = tep_db_fetch_array($categories_query)){
            tep_db_query("update categories set google_category_id='" . (int)$google_category_id . "', google_category_path='" . tep_db_input($google_category_path) . "' where categories_id='" . (int)$category['categories_id'] . "'");
            associate_google_category_with_products($category['categories_id'], $google_category_id, $google_category_path);
        } 
    }
  }
  
  function get_vendor_details($vendor_id){
    $response = array();
    $query = tep_db_query("select id, reference, status from custom_shipping_vendor where id='" . (int)$vendor_id . "'");
    if (tep_db_num_rows($query)){
        $response = tep_db_fetch_array($query);
    }
    return $response;
  }
  
  function get_vendor_manufacturers($vendor_id){
    $response = array();
    
    $sql = tep_db_query("select csm.manufacturer_id, m.manufacturers_name from custom_shipping_manufacturer csm inner join manufacturers m on (csm.manufacturer_id=m.manufacturers_id) where csm.custom_shipping_vendor_id='" . (int)$vendor_id  . "'");
    
    if (tep_db_num_rows($sql)){
        while ($entry = tep_db_fetch_array($sql)){
            $response[$entry['manufacturer_id']] = $entry['manufacturers_name'];
        }
    }
    return $response;
  }
  
  function format_vendor_manufacturers($vendor_id){
    $response = array('html' => '', 'ids' => '');
    $html = '';
    $ids = '';
    $manufacturers = get_vendor_manufacturers($vendor_id);
    foreach($manufacturers as $id => $name){
        //$html .= $name . ' (' . $id . ')';
        $html .= '<span class="smallText"><b>' . ( strlen($name)>20 ? substr($name, 0, 17) . '...' : $name )  . '</b><br>';
        $ids .= $id . ',';
    }
    if ( !empty($ids) ) $ids = substr($ids, 0, -1);
    $response['html'] = $html;
    $response['ids'] = $ids;
    return $response;
  }
  
  function get_vendor_categories($vendor_id){
    $response = array();
    
    $sql = tep_db_query("select csc.category_id, cd.categories_name from custom_shipping_category csc inner join categories_description cd on (csc.category_id=cd.categories_id) where csc.custom_shipping_vendor_id='" . (int)$vendor_id  . "'");
    
    if (tep_db_num_rows($sql)){
        while ($entry = tep_db_fetch_array($sql)){
            $response[$entry['category_id']] = $entry['categories_name'];
        }
    }
    return $response;
  }
  
  function format_vendor_categories($vendor_id){
    $response = array('html' => '', 'ids' => '');
    $html = '';
    $ids = '';
    $categories = get_vendor_categories($vendor_id);
    foreach($categories as $id => $name){
        //$html .= $name . ' (' . $id . ')';
        $html .= '<span class="smallText"><b>' . ( strlen($name)>20 ? substr($name, 0, 17) . '...' : $name )  . '</b><br>';
        $ids .= $id . ',';
    }
    if ( !empty($ids) ) $ids = substr($ids, 0, -1);
    $response['html'] = $html;
    $response['ids'] = $ids;
    return $response;
  }
  
  function get_vendor_products($vendor_id){
    $response = array();
    
    $sql = tep_db_query("select csp.product_id, pd.products_name from custom_shipping_product csp inner join products_description pd on (csp.product_id=pd.products_id) where csp.custom_shipping_vendor_id='" . (int)$vendor_id  . "'");
    
    if (tep_db_num_rows($sql)){
        while ($entry = tep_db_fetch_array($sql)){
            $response[$entry['product_id']] = $entry['products_name'];
        }
    }
    return $response;
  }
  
  function format_vendor_products($vendor_id){
    $response = array('html' => '', 'ids' => '');
    $html = '';
    $ids = '';
    $products = get_vendor_products($vendor_id);
    foreach($products as $id => $name){
        //$html .= $name . ' (' . $id . ')';
        $html .= '<span class="smallText"><b>' . ( strlen($name)>20 ? substr($name, 0, 17) . '...' : $name )  . '</b><br>';
        $ids .= $id . ',';
    }
    if ( !empty($ids) ) $ids = ',' . $ids;
    $response['html'] = $html;
    $response['ids'] = $ids;
    return $response;
  }
 
// MVS start
////
// Sets the Vendor Send Order Emails
  function tep_set_vendor_email($vendors_id, $vendors_send_email) {
    if ($vendors_send_email == '1') {
      return tep_db_query("update " . TABLE_VENDORS . " set vendors_send_email = '1' where vendors_id = '" . (int)$vendors_id . "'");
    } elseif ($vendors_send_email == '0') {
      return tep_db_query("update " . TABLE_VENDORS . " set vendors_send_email = '0' where vendors_id = '" . (int)$vendors_id . "'");
    } else {
      return -1;
    }
  }

////
// Get vendor info by language
  function tep_get_vendors_info($product_id, $vendors_id, $language_id = 0) {
    global $languages_id;

    if ($language_id == 0) $language_id = $languages_id;
    $product_query = tep_db_query("select * from " . TABLE_VENDORS . " where " . TABLE_VENDORS . " .vendors_id = " . TABLE_PRODUCTS . " .vendors_id and products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['vendors_name'];
  }

////
// Get vendor comments
  function tep_get_vendors_prod_comments($product_id) {
    $product_query = tep_db_query("select vendors_prod_comments from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['vendors_prod_comments'];
  }

////
// Get vendor URL by language
  function tep_get_vendor_url($vendor_id, $language_id) {
    $vendor_query = tep_db_query("select vendors_url from " . TABLE_VENDORS_INFO . " where vendors_id = '" . (int)$vendor_id . "' and languages_id = '" . (int)$language_id . "'");
    $vendor = tep_db_fetch_array($vendor_query);

    return $vendor['vendors_url'];
  }
  //works to send copy of EVERY EMAIL SENT FROM STORE uncomment to use
  /*
    if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
      $message->send('', SEND_EXTRA_ORDER_EMAILS_TO, $from_email_name, $from_email_address,
$email_subject);
    }   */

////
// UPSXML
// Alias function for Store configuration values in the Administration Tool
  /*function tep_cfg_select_multioption ($select_array, $key_value, $key = '') {
    for ($i=0; $i<sizeof($select_array); $i++) {
      $name = (($key) ? 'configuration[' . $key . '][]' : 'configuration_value');
      $string .= '<br><input type="checkbox" name="' . $name . '" value="' . $select_array[$i] . '"';
      $key_values = explode (", ", $key_value);
      if (in_array ($select_array[$i], $key_values) ) $string .= ' CHECKED';
      $string .= '> ' . $select_array[$i];
    } 
    $string .= '<input type="hidden" name="' . $name . '" value="--none--">';
    return $string;
  }*/
//MVS End

//function created for registering authorization output for authorize.net and fastcharge. after successfull integration comment function's code
function debug_register_order_authorization_details($order_id, $details){
	tep_db_query("update debug_order_authorization set transaction_details = concat('" . tep_db_prepare_input($details) . "', transaction_details) where orders_id='" . (int)$order_id . "'");
}

//BOF:mvs_internal_mod
function is_multi_vendor_order($order_id){
	$query = tep_db_query("select count(orders_products_id) as count from orders_products where orders_id='" . (int)$order_id . "' and vendors_id>0");
	$info = tep_db_fetch_array($query);
	if ($info['count']){
		return true;
	} else {
		return false;
	}
}
//EOF:mvs_internal_mod

//BOF:amazon_integration
// Sets the amazon status of a product
    function tep_set_amazon_product_status($products_id, $status) {
        if ($status == '1') {
            return tep_db_query("update " . TABLE_PRODUCTS . " set is_amazon_ok = '1', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
        } elseif ($status == '0') {
            return tep_db_query("update " . TABLE_PRODUCTS . " set is_amazon_ok = '0', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
        } else {
            return -1;
        }
    }

    // Sets the amazon status of a product
    function tep_set_amazon_category_status($categories_id, $status) {
        $categories = tep_get_category_tree($categories_id, '', '0', '', true);
        for ($i=0, $n=sizeof($categories); $i<$n; $i++) {
            $update_status = tep_db_query("update `categories` set `is_amazon_ok` = '".$status."' where `categories_id` = '".(int)$categories[$i]['id']."' ");
			tep_db_query("update products p, products_to_categories p2c set p.is_amazon_ok='" .(int)$status . "', p.products_last_modified=now() where p.products_id=p2c.products_id and p2c.categories_id='".(int)$categories[$i]['id']."'");
        }
        if ($status == '1') {
			tep_db_query("update products p, products_to_categories p2c set p.is_amazon_ok='" .(int)$status . "', p.products_last_modified=now() where p.products_id=p2c.products_id and p2c.categories_id='".(int)$categories[$i]['id']."'");
            return tep_db_query("update " . TABLE_CATEGORIES . " set is_amazon_ok = '1', last_modified = now() where categories_id = '" . (int)$categories_id . "'");
        } elseif ($status == '0') {
			tep_db_query("update products p, products_to_categories p2c set p.is_amazon_ok='" .(int)$status . "', p.products_last_modified=now() where p.products_id=p2c.products_id and p2c.categories_id='".(int)$categories[$i]['id']."'");
			return tep_db_query("update " . TABLE_CATEGORIES . " set is_amazon_ok = '0', last_modified = now() where categories_id = '" . (int)$categories_id . "'");
        } else {
            return -1;
        }
    }
//EOF:amazon_integration

//BOF:ebay_integration
// Sets the ebay status of a product
    function tep_set_ebay_product_status($products_id, $status) {
        if ($status == '1') {
            return tep_db_query("update " . TABLE_PRODUCTS . " set is_ebay_ok = '1', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
        } elseif ($status == '0') {
            return tep_db_query("update " . TABLE_PRODUCTS . " set is_ebay_ok = '0', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
        } else {
            return -1;
        }
    }

    // Sets the ebay status of a product
    function tep_set_ebay_category_status($categories_id, $status) {
        $categories = tep_get_category_tree($categories_id, '', '0', '', true);
        for ($i=0, $n=sizeof($categories); $i<$n; $i++) {
            $update_status = tep_db_query("update `categories` set `is_ebay_ok` = '" . (int)$status . "', last_modified=now() where `categories_id` = '".(int)$categories[$i]['id']."' ");
			tep_db_query("update products p, products_to_categories p2c set p.is_ebay_ok='" .(int)$status . "', p.products_last_modified=now() where p.products_id=p2c.products_id and p2c.categories_id='".(int)$categories[$i]['id']."'");
        }
        if ($status == '1') {
			tep_db_query("update products p, products_to_categories p2c set p.is_ebay_ok='" .(int)$status . "', p.products_last_modified=now() where p.products_id=p2c.products_id and p2c.categories_id='".(int)$categories[$i]['id']."'");
            return tep_db_query("update " . TABLE_CATEGORIES . " set is_ebay_ok = '1', last_modified = now() where categories_id = '" . (int)$categories_id . "'");
        } elseif ($status == '0') {
			tep_db_query("update products p, products_to_categories p2c set p.is_ebay_ok='" .(int)$status . "', p.products_last_modified=now() where p.products_id=p2c.products_id and p2c.categories_id='".(int)$categories[$i]['id']."'");
            return tep_db_query("update " . TABLE_CATEGORIES . " set is_ebay_ok = '0', last_modified = now() where categories_id = '" . (int)$categories_id . "'");
        } else {
            return -1;
        }
    }
//EOF:ebay_integration

//Delete all cache files

function tep_reset_cache_files($action){	
	switch ($action){
		case 'reset':
        if (DIR_FS_CACHE != '' && (strpos(DIR_FS_CACHE,'cache/')>0)) {
        $dir = DIR_FS_CACHE . 'full/';
        foreach(glob($dir . '/*') as $file) { 
           if(is_dir($file)) delete_files($file); else unlink($file); 
          }
         }
        $action = 'true'; 
 	    tep_db_query("UPDATE configuration SET configuration_value='true' WHERE configuration_key='USE_CACHE'");
		default:
			break;
	}
	# The return value is used to set the value upon viewing
	# It's NOT returining a false to indicate failure!!
	return $action;
}

function delete_files($dir) { 
  foreach(glob($dir . '/*') as $file) { 
    if(is_dir($file)) delete_files($file); else unlink($file); 
  } rmdir($dir); 
}



////

// Return date in raw format

// $date should be in format mm/dd/yyyy

// raw date is in format YYYYMMDD, or DDMMYYYY

function tep_date_raw($date, $reverse = false) {

  if ($reverse) {

    return substr($date, 3, 2) . substr($date, 0, 2) . substr($date, 6, 4);

  } else {

    return substr($date, 6, 4) . substr($date, 0, 2) . substr($date, 3, 2);

  }

}

?>