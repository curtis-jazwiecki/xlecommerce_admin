<?php
if($_GET['action'] == 'update_price' && isset($_GET['qid']))
  {
	include("includes/application_top.php"); 
	
  function tep_get_manufacturers($manufacturers_array = '') { // Function borrowed from the Catalog side
    if (!is_array($manufacturers_array)) $manufacturers_array = array();
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
    $categories_query = tep_db_query("select categories_id, parent_id from " . TABLE_CATEGORIES . " c where parent_id = '" . (int)$parent_id . "' order by sort_order");
    while ($categories = tep_db_fetch_array($categories_query)) {
    $category_tree_array[] = $categories['categories_id'];
    $category_tree_array = tep_get_child_categories($categories['categories_id'], $category_tree_array);
    }
    return $category_tree_array;
  }

$price_update_query = tep_db_query("select price_updates_id, above_below, above_below_value, above_below_2, above_below_value_2, price_update_add, price_update_fixed, price_update_value, price_update_category, price_update_manufacturer, price_update_select_query, price_update_query, price_update_roundoff FROM price_updates WHERE price_updates_id = '".$_GET['qid']."'");
while ($row = tep_db_fetch_array($price_update_query))
  {
	$price_updates_id = stripslashes($row['price_updates_id']);
	$above_below = stripslashes($row['above_below']);
	$above_below_value = stripslashes($row['above_below_value']);
	$above_below_2 = stripslashes($row['above_below_2']);
	$above_below_value_2 = stripslashes($row['above_below_value_2']);
	$add = stripslashes($row['price_update_add']);
	$fixed =  stripslashes($row['price_update_fixed']);
	$value =  stripslashes($row['price_update_value']);
	$cat = stripslashes($row['price_update_category']);
	$mfr = stripslashes($row['price_update_manufacturer']);
//	$price_update_select_query = stripslashes($row['price_update_select_query']);
//	$price_update_query = stripslashes($row['price_update_query']);
	$roundoff = stripslashes($row['price_update_roundoff']);

	// Set the SQL where function 
    if ($mfr == 0)
	  {
		if ($cat == 0)
		  { $where_string = ''; }
		else
		  {
			$cat_array = tep_get_child_categories($cat, $category_tree_array = '');
			$cat_string = implode(",", $cat_array);
			$where_string = ' AND pcat.categories_id in (' . $cat_string . ')';
	  	  }
	  }
	else
	  {
		$where_string = ' AND manufacturers_id=' . $mfr; 
	  	if ($cat != 0)
		  {
        	$cat_array = tep_get_child_categories($cat, $category_tree_array = '');
	  		$cat_string = implode(",", $cat_array);
        	$where_string .= ' AND pcat.categories_id in (' . $cat_string . ')';
	  	  }
	  }

    if ($like == '')
	  {
		if ($from != $first)
		  { $where_string .= " AND p.products_model >= " . $from . ""; }
	    if ($to != $last)
	      { $where_string .= " AND p.products_model <= " . $to . ""; } 
	  }
	else
	  { $where_string .= " AND p.products_model LIKE '" . $like . "'"; }
  
	if ($fixed == 0) // Fixed price change
	  {
	    if ($add == 0)  // Subtract
		  { $markup = (0 - $value); }
		else // Add
		  { $markup = $value; }          
      }
	else // Percent change
	  {
	    if ($add == 0)// Subtract
		  {
            $markup = (0 - $value);
            $markup = $markup . '%';
          }
		else  // Add
		  { $markup = $value . '%'; }  
	  }

	// if greater than/less than value is set, add a modifier
	if($above_below == '0')
	  {
	  }
	elseif($above_below == '1' && $above_below_2 == '2')
	  {
		$where_string .= " AND (p.base_price <= " . $above_below_value . " AND p.base_price > " . $above_below_value_2 . ")";

	  }
	elseif($above_below == '2' && $above_below_2 == '1')
	  {
		$where_string .= " AND (p.base_price > " . $above_below_value . " AND p.base_price <= " . $above_below_value_2 . ")";
	  }
	elseif($above_below == '1')
	  {
		$where_string .= " AND p.base_price <= '" . $above_below_value . "'";
	  }
	elseif($above_below == '2')
	  {
		$where_string .= " AND p.base_price > '" . $above_below_value . "'";
	  }
// Query to get the selected products and make the changes
    $products_update_query = tep_db_query('SELECT p.products_id AS id, p.base_price AS price, p.lock_price FROM products p, products_to_categories pcat WHERE p.products_id = pcat.products_id ' . $where_string);

    $count = 0;

    while ($products_update = tep_db_fetch_array($products_update_query))
	  {
        if ($fixed == 0)
		  {  // Fixed price change
		    if ($add == 0)
			  {  // Subtract
				$new_price = $products_update['price'] - $value;
	    	  }
			else
			  {  // Add
				$new_price = $products_update['price'] + $value;
			  }

		  }
		else
		  {  // Percent change
	        if ($add == 0)
			  {  // Subtract
          		$new_price = $products_update['price'] * (1 - ($value / 100));
              }
			else
			  {  // Add
				$new_price = $products_update['price'] * (1 + ($value / 100));         
			  }
		  }
		$roundoff_flag = '0';
		if ($roundoff == '1')
		  {
			$new_price = apply_roundoff($new_price);
			$roundoff_flag = '1';
		  }
     //  echo $products_update['id'] . ' - ' . $products_update['price'] . ' ' . $new_price . '<br>';

		if($above_below == '1' && $above_below_2 == '2')
		  {
			tep_db_query("UPDATE " . TABLE_PRODUCTS . " 
				SET products_price='" . $new_price . "', markup='".$markup."', 
				roundoff_flag='" . $roundoff_flag . "'  
				WHERE products_id='" . $products_update['id'] . "' AND (base_price <= " . $above_below_value . " AND base_price > " . $above_below_value_2 . ") AND lock_price = '0'");

			$insert_into_price_update = "UPDATE products SET products_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "' WHERE products_id='" . '$products_update["id"]' . "' AND (base_price <= " . '$above_below_value' . " AND base_price > " . '$above_below_value_2' . ") AND lock_price = '0'";
		  }
		elseif($above_below == '2' && $above_below_2 == '1')
		  {
			tep_db_query("UPDATE " . TABLE_PRODUCTS . " 
				SET products_price='" . $new_price . "', markup='".$markup."', 
				roundoff_flag='" . $roundoff_flag . "'  
				WHERE products_id='" . $products_update['id'] . "' AND (base_price > " . $above_below_value . " AND base_price <= " . $above_below_value_2 . ") AND lock_price = '0'");
			
			$insert_into_price_update = "UPDATE products SET products_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "' WHERE products_id='" . '$products_update["id"]' . "' AND (base_price > " . '$above_below_value' . " AND base_price <= " . '$above_below_value_2' . ") AND lock_price = '0'";
		  }
		elseif($above_below == '1')
		  {
			  tep_db_query("UPDATE " . TABLE_PRODUCTS . " 
							SET products_price='" . $new_price . "', markup='".$markup."', 
							roundoff_flag='" . $roundoff_flag . "'  
            	        	WHERE products_id='" . $products_update['id'] . "' AND base_price <= '" . $above_below_value . "' AND lock_price = '0'");

			  $insert_into_price_update = "UPDATE products SET products_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "' WHERE products_id='" . '$products_update["id"]' . "' AND base_price <= '" . '$above_below_value' . "' AND lock_price = '0'";
		  }
		elseif($above_below == '2')
		  {
			  tep_db_query("UPDATE " . TABLE_PRODUCTS . " 
							SET products_price='" . $new_price . "', markup='".$markup."', 
							roundoff_flag='" . $roundoff_flag . "'  
            	        	WHERE products_id='" . $products_update['id'] . "' AND base_price > '" . $above_below_value . "' AND lock_price = '0'");

			  $insert_into_price_update = "UPDATE products SET products_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "' WHERE products_id='" . '$products_update["id"]' . "' AND base_price > '" . '$above_below_value' . "' AND lock_price = '0'";
		  }     
     	elseif ($products_update['lock_price'])
		  {
		  	  tep_db_query("UPDATE " . TABLE_PRODUCTS . " 
		    	            SET markup='".$markup."', 
							roundoff_flag='" . $roundoff_flag . "'  
            	    	    WHERE products_id='" . $products_update['id'] . "' AND lock_price = '0'");

				$insert_into_price_update = "UPDATE products SET markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "' WHERE products_id='" . '$products_update["id"]' . "' AND lock_price = '0'";
		  }
		else
		  {
			  tep_db_query("UPDATE " . TABLE_PRODUCTS . " 
							SET products_price='" . $new_price . "', markup='".$markup."', 
							roundoff_flag='" . $roundoff_flag . "'  
            	        	WHERE products_id='" . $products_update['id'] . "' AND lock_price = '0'");

				$insert_into_price_update = "UPDATE products SET products_price='" . '$new_price' . "', markup='" . '$markup' . "', roundoff_flag='" . '$roundoff' . "' WHERE products_id='" . '$products_update["id"]' . "' AND lock_price = '0'";
		  }
      $count++;
    }  // Products while loop
	

// If a manufacturer was selected, get the name
	if ($mfr != 0) {
	  $manufacturers_query = tep_db_query("SELECT manufacturers_name FROM " . TABLE_MANUFACTURERS . " WHERE manufacturers_id=" . $mfr);
      $manufacturers = tep_db_fetch_array($manufacturers_query);
      $manufacturer = $manufacturers['manufacturers_name'];
      tep_db_query("update manufacturers set markup='" .$markup . "', markup_modified = now() where manufacturers_id='" . (int)$mfr . "'");
    } else {
      $manufacturer = TEXT_ALL_MANUFACTURERS;
	  tep_db_query("update manufacturers set markup='" .$markup . "', markup_modified = now()");
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
      tep_db_query("update categories set markup='" .$markup . "', markup_modified = now() where categories_id in (" . $cat_string . ")");
    } else {
      $category = TEXT_ALL_CATEGORIES;
	  tep_db_query("update categories set markup='" .$markup . "', markup_modified = now()");
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

  } // End action=update

	header("Location: price_updater.php?action=update_sucess&qid=".$_GET['qid']);
  }
else
	header("Location: price_updater.php");
?>