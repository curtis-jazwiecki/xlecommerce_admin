<?php
/*
  $Id: price_updater.php,v 1.1 2004/04/18 jck Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
 */

require('includes/application_top.php');

// added on 15-02-2016 #start
// added one extra column 'like_clause' to database table price_updates to save like clause if any
$check_column_exists = tep_db_num_rows(tep_db_query("SHOW COLUMNS FROM `price_updates` LIKE 'like_clause'"));
if ($check_column_exists == 0) {

    tep_db_query("ALTER TABLE `price_updates` ADD `like_clause` VARCHAR( 255 ) NOT NULL COMMENT 'contains like clause for product model' AFTER `price_update_manufacturer`");
}

// added on 15-02-2016 #ends




function tep_get_category_path($id, $from = 'category', $categories_array = '', $index = 0) {
    global $languages_id;

    if (!is_array($categories_array))
        $categories_array = array();

    if ($from == 'product') {
        $categories_query = tep_db_query("select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int) $id . "'");
        while ($categories = tep_db_fetch_array($categories_query)) {
            if ($categories['categories_id'] == '0') {
                $categories_array[$index][] = array('id' => '0', 'text' => TEXT_TOP);
            } else {
                $category_query = tep_db_query("select cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int) $categories['categories_id'] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int) $languages_id . "'");
                $category = tep_db_fetch_array($category_query);
                $categories_array[$index][] = array('id' => $categories['categories_id'], 'text' => $category['categories_name']);
                if ((tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0'))
                    $categories_array = tep_generate_category_path($category['parent_id'], 'category', $categories_array, $index);
                $categories_array[$index] = array_reverse($categories_array[$index]);
            }
            $index++;
        }
    } elseif ($from == 'category') {
        $category_query = tep_db_query("select cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int) $id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int) $languages_id . "'");
        $category = tep_db_fetch_array($category_query);
        $categories_array[$index] = $id;
        $index++;
        if ((tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0'))
            $categories_array = tep_get_category_path($category['parent_id'], 'category', $categories_array, $index);
    }

    return $categories_array;
}

// Functions to fill the dropdown boxes
function tep_get_manufacturers($manufacturers_array = '') { // Function borrowed from the Catalog side
    if (!is_array($manufacturers_array))
        $manufacturers_array = array();
    $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");
    while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
        $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']);
    }
    return $manufacturers_array;
}

function tep_get_child_categories($parent_id = '0', $category_tree_array = '') {

    if (!is_array($category_tree_array)) {
        $category_tree_array = array();

        $category_tree_array[] = $parent_id;
    }


    $categories_query = tep_db_query("select categories_id, parent_id from " . TABLE_CATEGORIES . " c where parent_id = '" . (int) $parent_id . "' order by sort_order");
    while ($categories = tep_db_fetch_array($categories_query)) {
        $category_tree_array[] = $categories['categories_id'];
        $category_tree_array = tep_get_child_categories($categories['categories_id'], $category_tree_array);
    }

    return $category_tree_array;
}
?>

<?php
if ($_GET['action'] == 'update_sort_order') {
    $loop_count = $_POST['count'];
    for ($aa = 0; $loop_count >= $aa; $aa++) {
        if (isset($_POST["sort_" . $aa])) {
            tep_db_query("UPDATE price_updates SET price_update_sort_order = '" . $_POST['sort_' . $aa] . "' WHERE price_updates_id = '" . $_POST['sort_item_' . $aa] . "'");
        }
    }
}

if ($_GET['action'] == 'delete_update' && isset($_GET['qid'])) {
    $categories_query = tep_db_query("DELETE from price_updates WHERE price_updates_id = '" . $_GET['qid'] . "'");
    $update_string = "Saved Price Update has been deleted.";
} elseif ($_GET['action'] == 'update_sucess' && isset($_GET['qid'])) {
    $update_string = "Highlighted item has been updated.";
}
// Process the request data
elseif (isset($_GET['action']) && $_GET['action'] == 'update') {
    // Get the data from the form and sanitize it
    if (isset($_POST['manufacturers_id'])) {
        $mfr = (int) $_POST['manufacturers_id'];
    } else {
        $mfr = 0;
    }
    if (isset($_POST['categories_id'])) {
        $cat = (int) $_POST['categories_id'];
    } else {
        $cat = 0;
    }
    if (isset($_POST['from_id'])) {
        $from = $_POST['from_id'];
    }
    if (isset($_POST['to_id'])) {
        $to = $_POST['to_id'];
    }
    if (isset($_POST['like'])) {
        $like = $_POST['like'];
    } else {
        $like = '';
    }
    if (isset($_POST['add'])) {
        $add = (int) $_POST['add'];
    } else {
        $add = 1;
    }
    if (isset($_POST['fixed'])) {
        $fixed = (int) $_POST['fixed'];
    } else {
        $fixed = 1;
    }
    if (isset($_POST['value'])) {
        $value = preg_replace('/[^0-9.]/', '', $_POST['value']);
    } else {
        $value = 0;
    }
    if (isset($_POST['above_below'])) {
        $above_below = $_POST['above_below'];
    } else {
        $above_below = 0;
    }
    if (isset($_POST['above_below_value'])) {
        $above_below_value = $_POST['above_below_value'];
    } else {
        $above_below_value = 0;
    }
    if (isset($_POST['above_below_2'])) {
        $above_below_2 = $_POST['above_below_2'];
    } else {
        $above_below_2 = 0;
    }
    if (isset($_POST['above_below_value_2'])) {
        $above_below_value_2 = $_POST['above_below_value_2'];
    } else {
        $above_below_value_2 = 0;
    }
    //BOF:mod
    if (isset($_POST['customer_group_id'])) {
        $customer_group_id = $_POST['customer_group_id'];
    } else {
        $customer_group_id = '0';
    }
    //EOF:mod
    // Set the SQL where function 
    if ($mfr == 0) {
        if ($cat == 0) {
            $where_string = '';
        } else {
            $cat_array = tep_get_child_categories($cat, $category_tree_array = '');
            $cat_string = implode(",", $cat_array);
            $where_string = ' AND pcat.categories_id in (' . $cat_string . ')';
        }
    } else {
        $where_string = ' AND manufacturers_id=' . $mfr;
        if ($cat != 0) {
            $cat_array = tep_get_child_categories($cat, $category_tree_array = '');
            $cat_string = implode(",", $cat_array);
            $where_string .= ' AND pcat.categories_id in (' . $cat_string . ')';
        }
    }

    if ($like == '') {
        if ($from != $first) {
            $where_string .= " AND p.products_model >= " . $from . "";
        }
        if ($to != $last) {
            $where_string .= " AND p.products_model <= " . $to . "";
        }
    } else {
        $where_string .= " AND p.products_model LIKE '" . $like . "'";
    }

    if ($fixed == 0) { // Fixed price change
        if ($add == 0) {  // Subtract
            $markup = (0 - $value);
        } else { // Add
            $markup = $value;
        }
    }
    //else // Percent change
    elseif ($fixed == 1) { // Percent change
        if ($add == 0) {// Subtract
            $markup = (0 - $value);
            $markup = $markup . '%';
        } else {  // Add
            $markup = $value . '%';
        }
    } elseif ($fixed == 2) { // Margin
        if ($add == 0) {// Subtract
            $markup = (0 - $value);
            $markup = $markup . '% Margin';
        } else {  // Add
            $markup = $value . '% Margin';
        }
    }


    // if greater than/less than value is set, add a modifier
    if ($above_below == '0') {
        
    } elseif ($above_below == '1' && $above_below_2 == '2') {
        $where_string .= " AND (p.base_price <= " . $above_below_value . " AND p.base_price > " . $above_below_value_2 . ")";
    } elseif ($above_below == '2' && $above_below_2 == '1') {
        $where_string .= " AND (p.base_price > " . $above_below_value . " AND p.base_price <= " . $above_below_value_2 . ")";
    } elseif ($above_below == '1') {
        $where_string .= " AND p.base_price <= '" . $above_below_value . "'";
    } elseif ($above_below == '2') {
        $where_string .= " AND p.base_price > '" . $above_below_value . "'";
    }
// Query to get the selected products and make the changes
//BOF
    /*
      //EOF
      $products_update_query = tep_db_query('SELECT p.products_id AS id,
      p.base_price AS price,
      p.lock_price
      FROM ' . TABLE_PRODUCTS . ' p, ' .
      TABLE_PRODUCTS_TO_CATEGORIES . ' pcat
      WHERE p.products_id = pcat.products_id' .
      $where_string
      );
      $insert_into_price_select = 'SELECT p.products_id AS id, p.base_price AS price, p.lock_price FROM ' . TABLE_PRODUCTS . ' p, ' . TABLE_PRODUCTS_TO_CATEGORIES . ' pcat WHERE p.products_id = pcat.products_id' . $where_string;
      //BOF
     */
    $products_update_query = tep_db_query("SELECT p.products_id AS id, p.base_price AS price, p.lock_price, pe.min_acceptable_price as MAP FROM " . TABLE_PRODUCTS . " p left join products_extended pe on p.products_id=pe.osc_products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " pcat WHERE p.products_id = pcat.products_id " . $where_string);

    $insert_into_price_select = 'SELECT p.products_id AS id, p.base_price AS price, p.lock_price, pe.min_acceptable_price as MAP FROM ' . TABLE_PRODUCTS . ' p left join products_extended pe on p.products_id=pe.osc_products_id, ' . TABLE_PRODUCTS_TO_CATEGORIES . ' pcat WHERE p.products_id = pcat.products_id' . $where_string;
//EOF
    $count = 0;

    while ($products_update = tep_db_fetch_array($products_update_query)) {
        if ($fixed == 0) {  // Fixed price change
            if ($add == 0) {  // Subtract
                $new_price = $products_update['price'] - $value;
            } else {  // Add
                $new_price = $products_update['price'] + $value;
            }
        } elseif ($fixed == 2) { // Margin change
            if ($add == 0) {// subtract
                $new_price = $products_update['price'] * (1 / (1 - (-$value / 100) ) );
            } else {// add
                $new_price = $products_update['price'] * (1 / (1 - ($value / 100) ) );
            }
        } else {  // Percent change
            if ($add == 0) {  // Subtract
                $new_price = $products_update['price'] * (1 - ($value / 100));
            } else {  // Add
                $new_price = $products_update['price'] * (1 + ($value / 100));
            }
        }
        if (isset($_POST['chk_roundoff'])) {
            $new_price = apply_roundoff($new_price);
        }
        //  echo $products_update['id'] . ' - ' . $products_update['price'] . ' ' . $new_price . '<br>';
        //BOF:mod
        $hide_price = false;
        if ($new_price < $products_update['MAP']) {
            //BOF:mod 06dec
            if ($customer_group_id >= '1') {
                //EOF:mod 06dec
                $hide_price = true;
                //BOF:mod 06dec
            }
            //EOF:mod 06dec
        }
        //EOF:mod

        if ($above_below == '1' && $above_below_2 == '2') {
            //BOF:mod 06dec
            if ($customer_group_id == '0') {
                //EOF:mod 06dec
                tep_db_query("UPDATE " . TABLE_PRODUCTS . " 
				SET products_price='" . $new_price . "', markup='" . $markup . "', 
				roundoff_flag='" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "'  
				WHERE products_id='" . $products_update['id'] . "' AND (base_price <= " . $above_below_value . " AND base_price > " . $above_below_value_2 . ")  AND lock_price = '0'");

                $insert_into_price_update = "UPDATE products SET products_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "' WHERE products_id='" . '$products_update["id"]' . "' AND (base_price <= " . '$above_below_value' . " AND base_price > " . '$above_below_value_2' . ") AND lock_price = '0'";
                //BOF:mod
            } else {
                $product_check_query = tep_db_query("select products_id from products where products_id='" . (int) $products_update["id"] . "' and (base_price<='" . $above_below_value . " and base_price>'" . $above_below_value_2 . ")");
                if (tep_db_num_rows($product_check_query)) {
                    tep_db_query("insert into products_groups (customers_group_id, products_id, customers_group_price, markup, roundoff_flag) values ('" . (int) $customer_group_id . "', '" . (int) $products_update['id'] . "', '" . $new_price . "', '" . $markup . "', '" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "') on duplicate key update customers_group_price='" . $new_price . "', markup='" . $markup . "', roundoff_flag='" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "'");

                    tep_db_query("update products set hide_price='" . ($hide_price ? '1' : '0') . "' where products_id='" . $products_update['id'] . "'");

                    $insert_into_price_update = "insert into products_groups (customers_group_id, products_id, customers_group_price, markup, roundoff_flag) values ('" . '$customer_group_id' . "', '" . '$products_update["id"]' . "', '" . '$new_price' . "', '" . '$markup' . "', '" . '$roundoff' . "') on duplicate key update customers_group_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "'";
                }
            }
            //EOF:mod
        } elseif ($above_below == '2' && $above_below_2 == '1') {
            //BOF:mod
            if ($customer_group_id == '0') {
                //EOF:mod
                tep_db_query("UPDATE " . TABLE_PRODUCTS . " 
				SET products_price='" . $new_price . "', markup='" . $markup . "', 
				roundoff_flag='" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "'  
				WHERE products_id='" . $products_update['id'] . "' AND (base_price > " . $above_below_value . " AND base_price <= " . $above_below_value_2 . ") AND lock_price = '0'");

                $insert_into_price_update = "UPDATE products SET products_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "' WHERE products_id='" . '$products_update["id"]' . "' AND (base_price > " . '$above_below_value' . " AND base_price <= " . '$above_below_value_2' . ") AND lock_price = '0'";
                //BOF:mod
            } else {
                //BOF:mod
                $product_check_query = tep_db_query("select products_id from products where products_id='" . (int) $products_update["id"] . "' and (base_price>'" . $above_below_value . " and base_price<='" . $above_below_value_2 . ")");

                if (tep_db_num_rows($product_check_query)) {
                    $sql = "insert into products_groups (customers_group_id, products_id, customers_group_price, markup, roundoff_flag) values ('" . (int) $customer_group_id . "', '" . (int) $products_update['id'] . "', '" . $new_price . "', '" . $markup . "', '" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "') on duplicate key update customers_group_price='" . $new_price . "', markup='" . $markup . "', roundoff_flag='" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "'";

                    tep_db_query("update products set hide_price='" . ($hide_price ? '1' : '0') . "' where products_id='" . $products_update['id'] . "'");


                    $insert_into_price_update = "insert into products_groups (customers_group_id, products_id, customers_group_price, markup, roundoff_flag) values ('" . '$customer_group_id' . "', '" . '$products_update["id"]' . "', '" . '$new_price' . "', '" . '$markup' . "', '" . '$roundoff' . "') on duplicate key update customers_group_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "'";
                }
            }
            //EOF:mod
        } elseif ($above_below == '1') {
            //BOF:mod
            if ($customer_group_id == '0') {
                //EOF:mod
                tep_db_query("UPDATE " . TABLE_PRODUCTS . " 
							SET products_price='" . $new_price . "', markup='" . $markup . "', 
							roundoff_flag='" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "'  
            	        	WHERE products_id='" . $products_update['id'] . "' AND base_price <= '" . $above_below_value . "' AND lock_price = '0'");

                $insert_into_price_update = "UPDATE products SET products_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "' WHERE products_id='" . '$products_update["id"]' . "' AND base_price <= '" . '$above_below_value' . "' AND lock_price = '0'";
                //BOF:mod
            } else {
                $product_check_query = tep_db_query("select products_id from products where products_id='" . $products_update['id'] . "' AND base_price <= '" . $above_below_value . "'");
                if (tep_db_num_rows($product_check_query)) {
                    tep_db_query("insert into products_groups (customers_group_id, products_id, customers_group_price, markup, roundoff_flag) values ('" . (int) $customer_group_id . "', '" . (int) $products_update['id'] . "', '" . $new_price . "', '" . $markup . "', '" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "') on duplicate key update customers_group_price='" . $new_price . "', markup='" . $markup . "', roundoff_flag='" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "'");

                    tep_db_query("update products set hide_price='" . ($hide_price ? '1' : '0') . "' where products_id='" . $products_update['id'] . "'");

                    $insert_into_price_update = "insert into products_groups (customers_group_id, products_id, customers_group_price, markup, roundoff_flag) values ('" . '$customer_group_id' . "', '" . '$products_update["id"]' . "', '" . '$new_price' . "', '" . '$markup' . "', '" . '$roundoff' . "') on duplicate key update customers_group_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "'";
                }
            }
            //EOF:mod
        } elseif ($above_below == '2') {
            //BOF:mod 06dec
            if ($customer_group_id == '0') {
                //EOF:mod
                tep_db_query("UPDATE " . TABLE_PRODUCTS . " 
							SET products_price='" . $new_price . "', markup='" . $markup . "', 
							roundoff_flag='" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "'  
            	        	WHERE products_id='" . $products_update['id'] . "' AND base_price > '" . $above_below_value . "' AND lock_price = '0'");

                $insert_into_price_update = "UPDATE products SET products_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "' WHERE products_id='" . '$products_update["id"]' . "' AND base_price > '" . '$above_below_value' . "' AND lock_price = '0'";
                //BOF:mod
            } else {
                $product_check_query = tep_db_query("select products_id from products where products_id='" . $products_update['id'] . "' AND base_price > '" . $above_below_value . "'");
                if (tep_db_num_rows($product_check_query)) {
                    tep_db_query("insert into products_groups (customers_group_id, products_id, customers_group_price, markup, roundoff_flag) values ('" . (int) $customer_group_id . "', '" . (int) $products_update['id'] . "', '" . $new_price . "', '" . $markup . "', '" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "') on duplicate key update customers_group_price='" . $new_price . "', markup='" . $markup . "', roundoff_flag='" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "'");

                    tep_db_query("update products set hide_price='" . ($hide_price ? '1' : '0') . "' where products_id='" . $products_update['id'] . "'");

                    $insert_into_price_update = "insert into products_groups (customers_group_id, products_id, customers_group_price, markup, roundoff_flag) values ('" . '$customer_group_id' . "', '" . '$products_update["id"]' . "', '" . '$new_price' . "', '" . '$markup' . "', '" . '$roundoff' . "') on duplicate key update customers_group_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "'";
                }
            }
            //EOF:mod
        } elseif ($products_update['lock_price']) {
            //BOF:mod
            if ($customer_group_id == '0') {
                //EOF:mod
                tep_db_query("UPDATE " . TABLE_PRODUCTS . " 
		    	            SET markup='" . $markup . "', 
							roundoff_flag='" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "'  
            	    	    WHERE products_id='" . $products_update['id'] . "' AND lock_price = '0'");

                $insert_into_price_update = "UPDATE products SET markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "' WHERE products_id='" . '$products_update["id"]' . "' AND lock_price = '0'";
                //BOF:mod
            } else {
                $product_check_query = tep_db_query("select products_id from products where products_id='" . (int) $products_update["id"] . "'");
                if (tep_db_num_rows($product_check_query)) {
                    tep_db_query("insert into products_groups (customers_group_id, products_id, customers_group_price, markup, roundoff_flag) values ('" . (int) $customer_group_id . "', '" . (int) $products_update['id'] . "', '" . $new_price . "', '" . $markup . "', '" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "') on duplicate key update customers_group_price='" . $new_price . "', markup='" . $markup . "', roundoff_flag='" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "'");

                    tep_db_query("update products set hide_price='" . ($hide_price ? '1' : '0') . "' where products_id='" . $products_update['id'] . "'");

                    $insert_into_price_update = "insert into products_groups (customers_group_id, products_id, customers_group_price, markup, roundoff_flag) values ('" . '$customer_group_id' . "', '" . '$products_update["id"]' . "', '" . '$new_price' . "', '" . '$markup' . "', '" . '$roundoff' . "') on duplicate key update customers_group_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "'";
                }
            }
            //EOF:mod
        } else {
            //BOF:mod 06dec
            if ($customer_group_id == '0') {
                //EOF:mod
                tep_db_query("UPDATE " . TABLE_PRODUCTS . " 
							SET products_price='" . $new_price . "', markup='" . $markup . "', 
							roundoff_flag='" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "'  
            	        	WHERE products_id='" . $products_update['id'] . "' AND lock_price = '0'");

                $insert_into_price_update = "UPDATE products SET products_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "' WHERE products_id='" . '$products_update["id"]' . "' AND lock_price = '0'";
                //BOF:mod
            } else {
                $product_check_query = tep_db_query("select products_id from products where products_id='" . (int) $products_update["id"] . "'");
                if (tep_db_num_rows($product_check_query)) {
                    tep_db_query("insert into products_groups (customers_group_id, products_id, customers_group_price, markup, roundoff_flag) values ('" . (int) $customer_group_id . "', '" . (int) $products_update['id'] . "', '" . $new_price . "', '" . $markup . "', '" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "') on duplicate key update customers_group_price='" . $new_price . "', markup='" . $markup . "', roundoff_flag='" . (isset($_POST['chk_roundoff']) ? 1 : 0) . "'");

                    tep_db_query("update products set hide_price='" . ($hide_price ? '1' : '0') . "' where products_id='" . $products_update['id'] . "'");

                    $insert_into_price_update = "insert into products_groups (customers_group_id, products_id, customers_group_price, markup, roundoff_flag) values ('" . '$customer_group_id' . "', '" . '$products_update["id"]' . "', '" . '$new_price' . "', '" . '$markup' . "', '" . '$roundoff' . "') on duplicate key update customers_group_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "'";
                }
            }
            //EOF:mod
        }
        $count++;
    }  // Products while loop
// If a manufacturer was selected, get the name
    //BOF:mod
    if ($customer_group_id == '') {
        //EOF:mod
        if ($mfr != 0) {
            $manufacturers_query = tep_db_query("SELECT manufacturers_name FROM " . TABLE_MANUFACTURERS . " WHERE manufacturers_id=" . $mfr);
            $manufacturers = tep_db_fetch_array($manufacturers_query);
            $manufacturer = $manufacturers['manufacturers_name'];
            tep_db_query("update manufacturers set markup='" . $markup . "', markup_modified = now() where manufacturers_id='" . (int) $mfr . "'");
        } else {
            $manufacturer = TEXT_ALL_MANUFACTURERS;
            tep_db_query("update manufacturers set markup='" . $markup . "', markup_modified = now()");
        }


// If a category was selected, get the name
        if ($cat != 0) {
            $categories_query = tep_db_query("SELECT cd.categories_name
                                        FROM " . TABLE_CATEGORIES_DESCRIPTION . " cd, 
                                             " . TABLE_LANGUAGES . " l 
									    WHERE l.languages_id = cd.language_id
									      AND l.name = '" . $language . "' 
									      AND categories_id = " . $cat);
            $categories = tep_db_fetch_array($categories_query);
            $category = TEXT_THE . $categories['categories_name'] . TEXT_CATEGORY;
            tep_db_query("update categories set markup='" . $markup . "', markup_modified = now() where categories_id in (" . $cat_string . ")");
        } else {
            $category = TEXT_ALL_CATEGORIES;
            tep_db_query("update categories set markup='" . $markup . "', markup_modified = now()");
        }


// Finish the rest of the update text information
        $fixed_string = '';
        if ($fixed == 1) {
            $fixed_string = TEXT_PERCENT;
        }

        $add_string = TEXT_DECREASED_BY;
        if ($add == 1) {
            $add_string = TEXT_INCREASED_BY;
        }

        $update_string = $manufacturer . TEXT_PRICES_IN . $category . TEXT_WERE . $add_string . $value . $fixed_string;

//BOF:mod
    }
//EOF:mod
} // End action=update
// Check to see if they want to save the price update for future price updates
if ($_POST['save_price_update'] == true) {
    $results = tep_db_query("SELECT price_updates_id FROM price_updates ORDER BY price_updates_id DESC");
    $num_rows_results = tep_db_fetch_array($results);

    if ($above_below > 0 && $above_below_2 > 0) {
        if ($above_below == 2 && $above_below_2 == 1) {
            $temp_above_below = $above_below;
            $temp_above_below_2 = $above_below_2;
            $above_below = $temp_above_below_2;
            $above_below_2 = $temp_above_below;

            $temp_above_below_value = $above_below_value;
            $temp_above_below_value_2 = $above_below_value_2;
            $above_below_value = $temp_above_below_value_2;
            $above_below_value_2 = $temp_above_below_value;
        }
    }
    //BOF:mod
    /*
      //EOF:mod
      $update_string_query = 'INSERT INTO price_updates (above_below, above_below_value, above_below_2, above_below_value_2,price_update_add, price_update_fixed, price_update_value, price_update_category, price_update_manufacturer, price_update_select_query, price_update_query, price_update_roundoff, price_update_sort_order) VALUES ('.$above_below.' , '.$above_below_value.', '.$above_below_2.', '.$above_below_value_2.', '.$add.', '.$fixed.', '.$value.', '.$cat.', '.$mfr.', "'.addslashes($insert_into_price_select).'", "'.addslashes($insert_into_price_update).'", '.(isset($_POST['chk_roundoff']) ? 1 : 0).', '.($num_rows_results['price_updates_id'] + 1).')';
      //BOF:mod
     */


    $update_string_query = 'INSERT INTO price_updates 
        (above_below, above_below_value, above_below_2, above_below_value_2,price_update_add, price_update_fixed, price_update_value, price_update_category, price_update_manufacturer, like_clause, price_update_select_query, price_update_query, price_update_roundoff, price_update_sort_order, customer_group_id) 
            VALUES 
                (
                    ' . $above_below . ' , 
                    ' . $above_below_value . ', 
                    ' . $above_below_2 . ', 
                    ' . $above_below_value_2 . ', 
                    ' . $add . ', 
                    ' . $fixed . ', 
                    ' . $value . ', 
                    ' . $cat . ', 
                    ' . $mfr . ', 
                    "' . addslashes($like) . '",
                    "' . addslashes($insert_into_price_select) . '", 
                    "' . addslashes($insert_into_price_update) . '", 
                    ' . (isset($_POST['chk_roundoff']) ? 1 : 0) . ', 
                    ' . ($num_rows_results['price_updates_id'] + 1) . ', 
                    ' . ($customer_group_id == '' ? "null" : "'" . $customer_group_id . "'") . ')';
    //EOF:mod
    tep_db_query($update_string_query);
}
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">

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
                            <td>
                                <table class="table table-bordered table-hover">
                                    <tr>
                                        <td>&nbsp;<?php echo TABLE_HEADING_UPDATES; ?>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php
                                            if (isset($count) && $count != 0) {
                                                echo $update_string;
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php
                                            if ($_GET['action'] == 'update_sucess' && isset($_GET['qid'])) {
                                                echo $update_string;
                                            } elseif ($_GET['action'] == 'delete_update' && isset($_GET['qid'])) {
                                                echo $update_string;
                                            } else {
                                                if (isset($count) && $count != 0) {
                                                    echo $count;
                                                } else {
                                                    echo TEXT_NO;
                                                }
                                                echo TEXT_PRODUCTS_UPDATED;
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo tep_draw_separator('pixel_trans.gif', "100%", 20); ?></td>
                        </tr>
                        <tr>
                            <td>
                                <form action="price_updater.php?action=update" method=post>
                                    <table class="table table-bordered table-hover">
                                        <tr>
                                            <td colspan="6"><?php echo tep_black_line(); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">&nbsp;<?php echo TABLE_HEADING_MANUFACTURER; ?>&nbsp;</td>
                                            <td colspan="3">&nbsp;<?php echo TABLE_HEADING_CATEGORY; ?>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan=8><?php echo tep_black_line(); ?></td>
                                        </tr>
                                        <tr colspan="3" class=attributes-odd>
                                            <td>
                                                &nbsp;<?php echo tep_draw_pull_down_menu('manufacturers_id', tep_get_manufacturers(array(array('id' => '0', 'text' => 'All Manufacturers ')))); ?>&nbsp;
                                            </td>
                                            <td colspan="3">
                                                &nbsp;<?php echo tep_draw_pull_down_menu('categories_id', tep_get_category_tree(), $current_category_id); ?>&nbsp;
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp; Range &nbsp;</td>
                                            <td>&nbsp;<?php echo TABLE_HEADING_PLUS_MINUS; ?>&nbsp;</td>
                                            <td>&nbsp;<?php echo TABLE_HEADING_FIXED; ?>&nbsp;</td>
                                            <td>&nbsp;<?php echo TABLE_HEADING_VALUE; ?>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?php echo tep_draw_pull_down_menu('above_below', array(array('id' => '0', 'text' => 'none'), array('id' => '1', 'text' => 'less than'), array('id' => '2', 'text' => 'greater than'))); ?>&nbsp;
                                                &nbsp;<?php echo tep_draw_input_field('above_below_value', '0'); ?>&nbsp;
                                                <br />
                                                <?php echo tep_draw_pull_down_menu('above_below_2', array(array('id' => '0', 'text' => 'none'), array('id' => '1', 'text' => 'less than'), array('id' => '2', 'text' => 'greater than'))); ?>&nbsp;
                                                &nbsp;<?php echo tep_draw_input_field('above_below_value_2', '0'); ?>&nbsp;
                                                <br />(leave second field source blank if<br />you only want to apply one range filter)
                                            </td>
                                            <td>
                                                &nbsp;<?php echo tep_draw_pull_down_menu('add', array(array('id' => '1', 'text' => '+'), array('id' => '0', 'text' => '-'))); ?>&nbsp;
                                            </td>
                                            <td>





                                                &nbsp;
                                                <?php
                                                //BOF:mod 20130424
                                                /*
                                                  //EOF:mod 20130424
                                                  echo tep_draw_pull_down_menu('fixed', array(array('id' => '1', 'text' => '%'), array('id' => '0', 'text' => 'Fixed')));





                                                  //BOF:mod 20130424
                                                 */
                                                echo tep_draw_pull_down_menu('fixed', array(array('id' => '1', 'text' => '%'), array('id' => '0', 'text' => 'Fixed'), array('id' => '2', 'text' => 'Margin')));
                                                //EOF:mod 20130424
                                                ?>&nbsp;
                                            </td>
                                            <td>
                                                &nbsp;
                                                <?php echo tep_draw_input_field('value', '0'); ?>
                                                <br />
                                                <?php echo tep_draw_checkbox_field('chk_roundoff', (ROUNDOFF_FLAG ? 1 : 0), (ROUNDOFF_FLAG ? true : false)); ?> Activate roundoff
                                                &nbsp;
                                            </td>
                                        </tr>
                                        <tr>

                                            <td colspan="6"><?php echo tep_black_line(); ?></td>

                                        </tr>
                                        <tr><td colspan="6">
                                                <?php echo tep_draw_pull_down_menu('customer_group_id', get_customer_groups()); ?>
                                                <?php /* <select name="customer_group_id">
                                                  <option value="">-- Select custmer group --</option>
                                                  <option value="1">Member</option>
                                                  <option value="0">Retail</option>
                                                  </select> */ ?>
                                            </td></tr>
                                        <tr>
                                        <tr>
                                            <td colspan="6"><?php echo tep_black_line(); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="right">
                                                <input type="checkbox" name="save_price_update" value="true" />
                                                <font color="#FF0000">Save Price Update (It will be applied to the price updates)</font>
                                            </td>
                                            <td colspan="1" align="right">
                                                &nbsp;<?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?>&nbsp;
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"><?php echo tep_black_line(); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6">&nbsp;<?php echo TABLE_HEADING_MODEL; ?>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"><?php echo tep_black_line(); ?></td>
                                        </tr>
                                        <tr class=attributes-odd>
                                            <td colspan="1" align="right">&nbsp;Like:&nbsp;</td>
                                            <td colspan="5" align="left">&nbsp;<?php echo tep_draw_input_field('like', ""); ?>&nbsp;&nbsp;&nbsp;<?php echo TEXT_NOTES ?>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"><?php echo tep_black_line(); ?></td>
                                        </tr>
                                    </table>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table class="table table-bordered table-hover">
                                    <tr>
                                        <td>Repeating Price Updates</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table class="table table-bordered table-hover">
                                                <tr>
                                                    <td>Customer Group</td>
                                                    <td>Category</td>
                                                    <td>Manufacturer</td>
                                                    <td>Price Range</td>
                                                    <td>+/-</td>
                                                    <td>Fixed/%</td>
                                                    <td>Markup</td>
                                                    <td>Round Off</td>
                                                    <td>Update Now</td>
                                                    <td>Delete</td>
                                                    <td>Sort Order</td>
                                                </tr>
                                                <?php
                                                $count = 0;
                                                $highest_updates_id = 0;
                                                echo '<form action="price_updater.php?action=update_sort_order" method="post">';
//BOF:mod
                                                /*
                                                  //EOF:mod
                                                  $price_update_query = tep_db_query("select price_updates_id, price_update_sort_order, above_below, above_below_value, above_below_2, above_below_value_2, price_update_add, price_update_fixed, price_update_value, price_update_category, price_update_manufacturer, price_update_select_query, price_update_query, price_update_roundoff FROM price_updates ORDER BY price_update_sort_order");
                                                  //BOF:mod
                                                 */
                                                $price_update_query = tep_db_query("select price_updates_id, price_update_sort_order, above_below, above_below_value, above_below_2, above_below_value_2, price_update_add, price_update_fixed, price_update_value, price_update_category, price_update_manufacturer, price_update_select_query, price_update_query, price_update_roundoff, customer_group_id FROM price_updates ORDER BY price_update_sort_order");
//EOF:mod

                                                while ($row = tep_db_fetch_array($price_update_query)) {
                                                    if ($_GET['action'] == 'update_sucess' && $_GET['qid'] == $row['price_updates_id'])
                                                        echo '<tr bgcolor="#00cc00">';
                                                    else
                                                        echo '<tr>';
                                                    //BOF:mod
                                                    echo '<td>';
                                                    if (!is_null($row['customer_group_id'])) {
                                                        $customer_group_query = tep_db_query("select customers_group_name from customers_groups
							where customers_group_id='" . (int) $row['customer_group_id'] . "'");
                                                        if (tep_db_num_rows($customer_group_query)) {
                                                            $customer_group = tep_db_fetch_array($customer_group_query);
                                                            echo $customer_group['customers_group_name'];
                                                        } else {
                                                            echo '??';
                                                        }
                                                    } else {
                                                        echo '--';
                                                    }
                                                    echo '</td>';
                                                    //EOF:mod
                                                    echo '<td>';
                                                    if ($row['price_update_category'] == 0)
                                                        echo 'all';
                                                    else {
                                                        $check_categories_query = tep_db_query("select categories_name from categories_description where categories_id = '" . $row['price_update_category'] . "'");
                                                        $check_categories_name = tep_db_fetch_array($check_categories_query);
                                                        echo $check_categories_name['categories_name'];
                                                    }
                                                    echo '</td>';
                                                    echo '<td>';
                                                    if ($row['price_update_manufacturer'] == 0)
                                                        echo 'all';
                                                    else {
                                                        $check_manufacturers_query = tep_db_query("select manufacturers_name from manufacturers where manufacturers_id = '" . $row['price_update_manufacturer'] . "'");
                                                        $check_manufacturers_name = tep_db_fetch_array($check_manufacturers_query);
                                                        echo $check_manufacturers_name['manufacturers_name'];
                                                    }
                                                    echo '</td>';

                                                    echo '<td>';
                                                    if ($row['above_below'] == 0 && $row['above_below_2'] == 0)
                                                        echo 'all';
                                                    elseif ($row['above_below'] != 0 && $row['above_below_2'] != 0) {
                                                        if ($row['above_below'] == 1)
                                                            echo "<&nbsp;&nbsp; : ";
                                                        elseif ($row['above_below'] == 2)
                                                            echo ">= : ";
                                                        echo $row['above_below_value'];
                                                        echo '<br />';
                                                        if ($row['above_below_2'] == 1)
                                                            echo "<&nbsp;&nbsp; : ";
                                                        elseif ($row['above_below_2'] == 2)
                                                            echo ">= : ";
                                                        echo $row['above_below_value_2'];
                                                    }
                                                    elseif ($row['above_below'] != 0 && $row['above_below_2'] == 0) {
                                                        if ($row['above_below'] == 1)
                                                            echo "<&nbsp;&nbsp; : ";
                                                        elseif ($row['above_below'] == 2)
                                                            echo ">= : ";
                                                        echo $row['above_below_value'];
                                                    }

                                                    echo '</td>';

                                                    echo '<td>';
                                                    if ($row['price_update_add'] == 1)
                                                        echo '+';
                                                    else
                                                        echo '-';
                                                    echo '</td>';
                                                    echo '<td>';
                                                    //BOF:mod 20130424
                                                    /*

                                                      //EOF:mod 20130424
                                                      if($row['price_update_fixed'] == 1) echo '%';
                                                      else echo 'Fixed';
                                                      //BOF:mod 20130424
                                                     */
                                                    //if($row['price_update_fixed'] == 1) echo 'Fixed';
                                                    //else echo '%';
                                                    //EOF:mod 20130424
                                                    if ($row['price_update_fixed'] == 1)
                                                        echo '%';
                                                    elseif ($row['price_update_fixed'] == 2)
                                                        echo 'Margin';
                                                    else
                                                        echo 'Fixed';
                                                    echo '</td>';
                                                    echo '<td>' . stripslashes($row['price_update_value']) . '</td>';


                                                    echo '<td>';
                                                    if ($row['price_update_roundoff'] == 1)
                                                        echo 'Yes';
                                                    else
                                                        echo 'No';
                                                    echo '</td>';
                                                    echo '<td><a href="price_updater_price_fix.php?action=update_price&qid=' . $row['price_updates_id'] . '" style="color: #333"><u>Update</u></a></td>';
                                                    echo '<td><a href="price_updater.php?action=delete_update&qid=' . $row['price_updates_id'] . '" style="color: #333"><u>Delete</u></a></td>';
                                                    echo '<input type="hidden" name="sort_item_' . $row['price_updates_id'] . '" value="' . $row['price_updates_id'] . '" /></td>';

                                                    echo '<td><input type="text" name="sort_' . $row['price_updates_id'] . '" value="' . $row['price_update_sort_order'] . '" size="5" /></td>';
                                                    echo '</tr>';
                                                    if ($highest_updates_id < $row['price_updates_id'])
                                                        $highest_updates_id = $row['price_updates_id'];
                                                    $count++;
                                                }
                                                echo '<input type="hidden" name="count" value="' . $highest_updates_id . '">';
                                                ?>
                                                <tr>
                                                    <td colspan="10" align="right">
                                                        <input border="0" type="image" title=" Update " alt="Update" src="includes/languages/english/images/buttons/button_update.gif">
                                                    </td>
                                                </tr>
                                                </form>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php /*
                                  // Begain Back End Add User - uses 'admin_add_user.php' to add then new user then re-directs back to this file to make a new order - OBN ////////////////////// ?>
                                  <script langauge="JavaScript" type="text/javascript">
                                  function doMenu(item) {
                                  obj=document.getElementById(item);
                                  col=document.getElementById("x" + item);
                                  if (obj.style.display=="none") {
                                  obj.style.display="block";
                                  col.innerHTML="[-]";
                                  }
                                  else {
                                  obj.style.display="none";
                                  col.innerHTML="[+]";
                                  }
                                  }
                                  </script>
                                  <?php echo tep_draw_form('create_order', 'admin_add_user.php', '', 'post', '', '') . tep_draw_hidden_field('customers_id', $account->customers_id); ?><table border="0" width="100%" cellspacing="0" cellpadding="0">
                                  <td width="100%" valign="top" style="font-family:Verdana,Arial,sans-serif; font-size:10px; color: #333">
                                  <br />&nbsp;&nbsp;&nbsp;&nbsp;
                                  <a href="JavaScript:doMenu('main');" id="xmain" style="font-family:Verdana,Arial,sans-serif; font-size:10px; color: #333">[+]</a> Show Catalog Markups
                                  <br />
                                  <br />
                                  <div id=main style="margin-left:1em; display: none;">
                                  <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                  <tr>
                                  <td width="50px"></td>
                                  <td valign="top" colspan="4">
                                  <div class="dtree"><form>
                                  <p><a href="javascript: d.openAll();">open all</a> | <a href="javascript: d.closeAll();">close all</a></p>
                                  <?php
                                  echo "<script type='text/javascript'>
                                  <!--

                                  d = new dTree('d'); \n
                                  d.add(0,-1,'Catalog','','');\n";



                                  $categories_query_raw = "SELECT c.categories_id, cd.categories_name, c.parent_id, c.markup, c.markup_modified FROM " . TABLE_CATEGORIES_DESCRIPTION . " AS cd INNER JOIN categories as c ON cd.categories_id = c.categories_id WHERE cd.language_id ='1' ORDER BY c.sort_order";
                                  $categories_query = tep_db_query($categories_query_raw);
                                  while ($categories = tep_db_fetch_array($categories_query)) {
                                  $calculated_category_path = array();
                                  $text = addslashes($categories['categories_name']) . ($categories['markup'] != '' ? '-> <b>' . $categories['markup'] . '</b> updated on ' . tep_date_short($categories['markup_modified']) : '');

                                  echo "d.add(" . $categories['categories_id'] . "," . $categories['parent_id'] . ",'" .$text . "');\n"; //,," . $categories['categories_id'] . ",,,); \n";

                                  } //end while

                                  ?>
                                  document.write(d);
                                  //-->
                                  </script>
                                  <?php
                                  //				  </td>
                                  //				  <td width="50%" class="smallText" valign="top"><b>Manufacturers</b><br>
                                  //
                                  //$man_query = tep_db_query("select manufacturers_id, manufacturers_name, markup, markup_modified from manufacturers order by manufacturers_name");
                                  //while ($man= tep_db_fetch_array($man_query)) {
                                  //	echo $man['manufacturers_name'] . ($man['markup'] != '' ? '-> <b>' . $man['markup'] . '</b> updated on ' . tep_date_short($man['markup_modified']) : '') . '<br>';
                                  //}
                                  ?>
                                  <br /><br />
                                  </td>
                                  </tr>
                                  </table>
                                  </div>
                                  </td>
                                  </tr>
                                  </table>
                                 */ ?>
                            </td>
                        </tr>
                    </table>
                    <!-- END your table-->
                    <!-- body_eof //-->

                    <!-- footer //-->
                    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
                    <!-- footer_eof //-->
                    <script type="text/javascript">
<?php echo ($HTTP_GET_VARS['action'] == 'new' ? 'displaySelection(\'div_listing\', \'C0\', \'F\');' : ''); ?>
                    </script>
                    <?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>