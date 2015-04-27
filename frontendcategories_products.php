<?php
/*
  $Id: frontendcategories_products.php
*/

  require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  require_once('includes/functions/frontend_category.php');
  include('includes/languages/english/categories.php');
  $currencies = new currencies();

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  if (isset($_POST['Update_without_preview']))
  	{
		$action = 'update_product';
		$pID = $HTTP_GET_VARS['Update_pID'];
	}


 if (isset($_POST['update']))
	$action_update = $_POST['update'];
  // Ultimate SEO URLs v2.1
// If the action will affect the cache entries
    if ( preg_match("/(insert|update|setflag)/i", $action) ) include_once('includes/reset_seo_cache.php');

	function get_products_price($markup, $base_price){
		if(strpos( $markup, '-')){
			$operator = '-';
		} else {
			$operator = '+';
		}
		$markup = str_replace("-","",$markup);	
		if(strpos( $markup, '%')){
			$markup = str_replace("%","",$markup);	
			$markup_price = $base_price * ((float)$markup/100);
		}else{
			$markup_price = (float)$markup;
		}
        if ($operator == '+') {
			$products_price = $base_price + $markup_price;
		} else {
			$products_price = $base_price - $markup_price;
		}
		return round($products_price,4);
	}
 	$roundoff_flag = ROUNDOFF_FLAG;
  if (tep_not_null($action)) {
    switch ($action) {
	  case 'set_frontend_cat':
	  	$ids = $HTTP_POST_VARS['hdn_ids'];
	  	$split_ids = explode('|', $ids);
	  	$frontend_cat_id = $HTTP_POST_VARS['drp_cat_frontend'];
	  if (isset($HTTP_POST_VARS['category_flag'])){	
	  	$category_flag =  $HTTP_POST_VARS['category_flag'];
	  	} else {
			$category_flag='1';
		}

	  	foreach($split_ids as $id){
	  		$type = substr($id, 0, 1);
	  		$num = substr($id, 1);
	  		switch ($type){
	  			case "P":
	  				if ($frontend_cat_id=='UNSET'){
	  					unset_frontend_category_by_product_id($num);
	  				} else {
	  					set_frontend_category_by_product_id($num, $frontend_cat_id,$category_flag);
	  				}
	  				break;
	  			case "C":
	  				if ($frontend_cat_id=='UNSET'){
	  					//NO ACTION
	  				} else {
	  					set_frontend_category_by_category_id($num, $frontend_cat_id,$category_flag);
	  				}
				  	break;	
	  		}
	  	}
	  	$params = 'cPath=' . $HTTP_GET_VARS['cPath'];
		if (isset($HTTP_GET_VARS['cID'])){
			$params .= '&' . 'cID=' . $HTTP_GET_VARS['cID'];
		}
		if (isset($HTTP_GET_VARS['pID'])){
			$params .= '&' . 'pID=' . $HTTP_GET_VARS['pID'];
		}
	  	tep_redirect(tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, $params)); 
	  	break;
      case 'setflag':
        if ( ($HTTP_GET_VARS['flag'] == '0') || ($HTTP_GET_VARS['flag'] == '1') ) {
          if (isset($HTTP_GET_VARS['pID'])) {
            tep_set_product_status($HTTP_GET_VARS['pID'], $HTTP_GET_VARS['flag']);
          }

          if (USE_CACHE == 'true') {
            tep_reset_cache_block('categories');
            tep_reset_cache_block('also_purchased');
          }

        }

        tep_redirect(tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $HTTP_GET_VARS['cPath'] . '&pID=' . $HTTP_GET_VARS['pID']));
        break;
        //Categroies Status MOD BEGIN by FIW
         case 'setflagc':
        if ( ($HTTP_GET_VARS['flag'] == '0') || ($HTTP_GET_VARS['flag'] == '1') ) {
          if (isset($HTTP_GET_VARS['cID'])) {
            tep_set_category_status($HTTP_GET_VARS['cID'], $HTTP_GET_VARS['flag']);
          }

          if (USE_CACHE == 'true') {
            tep_reset_cache_block('categories');
            tep_reset_cache_block('also_purchased');
          }

        }

        tep_redirect(tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $HTTP_GET_VARS['cPath'] . '&cID=' . $HTTP_GET_VARS['cID']));
        break;
        //Categories status MOD END by FIW
     
      case 'move_category_confirm':
        if (isset($HTTP_POST_VARS['categories_id']) && ($HTTP_POST_VARS['categories_id'] != $HTTP_POST_VARS['move_to_category_id'])) {
          $categories_id = tep_db_prepare_input($HTTP_POST_VARS['categories_id']);
          $new_parent_id = tep_db_prepare_input($HTTP_POST_VARS['move_to_category_id']);

          $path = explode('_', tep_get_generated_category_path_ids($new_parent_id));

          if (in_array($categories_id, $path)) {
            $messageStack->add_session(ERROR_CANNOT_MOVE_CATEGORY_TO_PARENT, 'error');

            tep_redirect(tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&cID=' . $categories_id));
          } else {
            tep_db_query("update " . TABLE_CATEGORIES . " set parent_id = '" . (int)$new_parent_id . "', last_modified = now() where categories_id = '" . (int)$categories_id . "'");
            //BOF:category_group
            if ((int)$new_parent_id>0){
				tep_db_query("update " . TABLE_CATEGORIES . " set is_category_group = '0' where categories_id = '" . (int)$categories_id . "'");            	
            }
			//EOF:category_group
            if (USE_CACHE == 'true') {
              tep_reset_cache_block('categories');
              tep_reset_cache_block('also_purchased');
            }

            tep_redirect(tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $new_parent_id . '&cID=' . $categories_id));
          }
        }

        break;
      case 'move_product_confirm':
        $products_id = tep_db_prepare_input($HTTP_POST_VARS['products_id']);
        $new_parent_id = tep_db_prepare_input($HTTP_POST_VARS['move_to_category_id']);

        $duplicate_check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$products_id . "' and categories_id = '" . (int)$new_parent_id . "'");
        $duplicate_check = tep_db_fetch_array($duplicate_check_query);
        if ($duplicate_check['total'] < 1) tep_db_query("update " . TABLE_PRODUCTS_TO_CATEGORIES . " set categories_id = '" . (int)$new_parent_id . "' where products_id = '" . (int)$products_id . "' and categories_id = '" . (int)$current_category_id . "'");

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('categories');
          tep_reset_cache_block('also_purchased');
        }
         

        tep_redirect(tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $new_parent_id . '&pID=' . $products_id));
        break;
      case 'insert_product':
      case 'update_product':
        if (isset($HTTP_POST_VARS['edit_x']) || isset($HTTP_POST_VARS['edit_y'])) {
          $action = 'new_product';
        } else {
       	  $manual_price_set = 0;
          if (isset($HTTP_GET_VARS['pID'])) $products_id = tep_db_prepare_input($HTTP_GET_VARS['pID']);
          $products_date_available = tep_db_prepare_input($HTTP_POST_VARS['products_date_available']);
           if (isset($HTTP_POST_VARS['manual_price']) && $HTTP_POST_VARS['manual_price'] > 0 && $HTTP_POST_VARS['manual_price'] != '') {
           	  $products_price = $HTTP_POST_VARS['manual_price'];
           	  $manual_price_set = 1;
           	  } else {
				if ($action == 'insert_product') {					
					$value = DEFAULT_MARKUP;
					$markup_value = $value;
					//$products_price = 	$HTTP_POST_VARS['base_price'] * (1 + ($value / 100));
					$products_price = get_products_price($markup_value, $HTTP_POST_VARS['base_price']);  
			  } else {
				$check_query = tep_db_query("select markup, roundoff_flag from products where products_id = '" . (int)$products_id . "'");
				$check = tep_db_fetch_array($check_query);
				$markup = $check['markup'];
				$roundoff_flag = $check['roundoff_flag'];
				$markup_value = $markup;
				$products_price = get_products_price($markup_value, $HTTP_POST_VARS['base_price']);
				/*if(strpos( $markup, '-')){
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
			$products_price = round($products_price,4);*/
			   }
			} 

          $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';

          $sql_data_array = array('products_quantity' => tep_db_prepare_input($HTTP_POST_VARS['products_quantity']),
                                  'products_model' => tep_db_prepare_input($HTTP_POST_VARS['products_model']),
                                  'products_size' => tep_db_prepare_input($HTTP_POST_VARS['products_size']),           
								  'disclaimer_needed' => tep_db_prepare_input($HTTP_POST_VARS['disclaimer_needed']),
								  'products_weight' => tep_db_prepare_input($HTTP_POST_VARS['products_weight']),
                                  'products_height' => tep_db_prepare_input($HTTP_POST_VARS['products_height']),
                                  'products_length' => tep_db_prepare_input($HTTP_POST_VARS['products_length']),
                                  'products_width' => tep_db_prepare_input($HTTP_POST_VARS['products_width']),
                                  'products_ready_to_ship' => tep_db_prepare_input($HTTP_POST_VARS['products_ready_to_ship']),					  
								  'base_price' => tep_db_prepare_input($HTTP_POST_VARS['base_price']),
                                  'manual_price' => tep_db_prepare_input($HTTP_POST_VARS['manual_price']),
                                  'products_date_available' => $products_date_available,
                                  'products_weight' => tep_db_prepare_input($HTTP_POST_VARS['products_weight']),
                                  'markup'=> $markup_value,
								  
                                  'products_status' => tep_db_prepare_input($HTTP_POST_VARS['products_status']),
								    
								  
                                  'products_tax_class_id' => tep_db_prepare_input($HTTP_POST_VARS['products_tax_class_id']),
                                  'manufacturers_id' => tep_db_prepare_input($HTTP_POST_VARS['manufacturers_id']),
								  'free_shipping' => tep_db_prepare_input($HTTP_POST_VARS['free_shipping']),								  
								  'in_store_pickup' => tep_db_prepare_input($HTTP_POST_VARS['in_store_pickup']),
								  'lock_price' => tep_db_prepare_input($HTTP_POST_VARS['lock_price']));

          if (isset($HTTP_POST_VARS['products_image']) && tep_not_null($HTTP_POST_VARS['products_image']) && ($HTTP_POST_VARS['products_image'] != 'none')) {
            $sql_data_array['products_image'] = tep_db_prepare_input($HTTP_POST_VARS['products_image']);
          }
          
           if (isset($HTTP_POST_VARS['products_mediumimage']) && tep_not_null($HTTP_POST_VARS['products_mediumimage']) && ($HTTP_POST_VARS['products_mediumimage'] != 'none')) {
            $sql_data_array['products_mediumimage'] = tep_db_prepare_input($HTTP_POST_VARS['products_mediumimage']);
          }
          
           if (isset($HTTP_POST_VARS['products_largeimage']) && tep_not_null($HTTP_POST_VARS['products_largeimage']) && ($HTTP_POST_VARS['products_largeimage'] != 'none')) {
            $sql_data_array['products_largeimage'] = tep_db_prepare_input($HTTP_POST_VARS['products_largeimage']);
          }

          if ($action == 'insert_product') {
            $insert_sql_data = array('products_date_added' => 'now()',
									 'roundoff_flag'=>ROUNDOFF_FLAG,
									 'products_price' => (ROUNDOFF_FLAG && !$manual_price_set ? apply_roundoff($products_price) : $products_price));

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            tep_db_perform(TABLE_PRODUCTS, $sql_data_array);
            $products_id = tep_db_insert_id();

            tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id) values ('" . (int)$products_id . "', '" . (int)$current_category_id . "')");
            //$flags = tep_db_prepare_input($HTTP_POST_VARS['prod_inventory_flag']) .
            //			tep_db_prepare_input($HTTP_POST_VARS['prod_price_flag']) .
            //			tep_db_prepare_input($HTTP_POST_VARS['prod_category_flag']);
            //tep_db_query("insert into products_xml_feed_flags (products_id, flags, last_modified) values ('" . (int)$products_id . "', '" . $flags . "', now())");
          } elseif ($action == 'update_product') {
          	$sql = tep_db_query("select roundoff_flag from ". TABLE_PRODUCTS . " where products_id='" . (int)$products_id . "'");
          	$sql_info = tep_db_fetch_array($sql);
			 $roundoff_flag = $sql_info['roundoff_flag'];  
            $update_sql_data = array('products_last_modified' => 'now()',
								'products_price' => ($roundoff_flag && !$manual_price_set ? apply_roundoff($products_price) : $products_price));

            $sql_data_array = array_merge($sql_data_array, $update_sql_data);

            tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "'");
            
            $flags = tep_db_prepare_input($HTTP_POST_VARS['prod_inventory_flag']) .
            			tep_db_prepare_input($HTTP_POST_VARS['prod_price_flag']) .
            			tep_db_prepare_input($HTTP_POST_VARS['prod_category_flag']) . 
						tep_db_prepare_input($HTTP_POST_VARS['prod_desc_flag']) . 
						tep_db_prepare_input($HTTP_POST_VARS['prod_image_flag']);
						
            //if (!empty($flags)){
				tep_db_query("update products_xml_feed_flags set flags='" . $flags . "', last_modified=now() where products_id='" . (int)$products_id . "'");
			//}else{
            //	tep_db_query("insert into products_xml_feed_flags (products_id, flags, last_modified) values ('" . (int)$products_id . "', '" . $flags . "', now())");				
			//}
          }
          /** AJAX Attribute Manager  **/ 
  require_once('attributeManager/includes/attributeManagerUpdateAtomic.inc.php'); 
/** AJAX Attribute Manager  end **/

          $languages = tep_get_languages();
          for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
            $language_id = $languages[$i]['id'];

            $sql_data_array = array('products_name' => tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id]),
                                    'products_description' => tep_db_prepare_input($HTTP_POST_VARS['products_description'][$language_id]),
                                    'products_url' => tep_db_prepare_input($HTTP_POST_VARS['products_url'][$language_id]),
                                     'products_head_title_tag' => ((tep_not_null($HTTP_POST_VARS['products_head_title_tag'][$language_id])) ? tep_db_prepare_input($HTTP_POST_VARS['products_head_title_tag'][$language_id]) : tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id])),
                                    'products_head_desc_tag' => ((tep_not_null($HTTP_POST_VARS['products_head_desc_tag'][$language_id])) ? tep_db_prepare_input($HTTP_POST_VARS['products_head_desc_tag'][$language_id]) : tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id])),
                                    'products_head_keywords_tag' => ((tep_not_null($HTTP_POST_VARS['products_head_keywords_tag'][$language_id])) ? tep_db_prepare_input($HTTP_POST_VARS['products_head_keywords_tag'][$language_id]) : tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id])));     

            if ($action == 'insert_product') {
              $insert_sql_data = array('products_id' => $products_id,
                                       'language_id' => $language_id);

              $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

              tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
            } elseif ($action == 'update_product') {
              tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "' and language_id = '" . (int)$language_id . "'");
            }
          }

          if (USE_CACHE == 'true') {
            tep_reset_cache_block('categories');
            tep_reset_cache_block('also_purchased');
          }
			 
          tep_redirect(tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $products_id));
        }
        break;
      case 'copy_to_confirm':
        if (isset($HTTP_POST_VARS['products_id']) && isset($HTTP_POST_VARS['categories_id'])) {
          $products_id = tep_db_prepare_input($HTTP_POST_VARS['products_id']);
          $categories_id = tep_db_prepare_input($HTTP_POST_VARS['categories_id']);

          if ($HTTP_POST_VARS['copy_as'] == 'link') {
            if ($categories_id != $current_category_id) {
              $check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$products_id . "' and categories_id = '" . (int)$categories_id . "'");
              $check = tep_db_fetch_array($check_query);
              if ($check['total'] < '1') {
                tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id) values ('" . (int)$products_id . "', '" . (int)$categories_id . "')");
              }
            } else {
              $messageStack->add_session(ERROR_CANNOT_LINK_TO_SAME_CATEGORY, 'error');
            }
          } elseif ($HTTP_POST_VARS['copy_as'] == 'duplicate') {
            $product_query = tep_db_query("select products_quantity, products_model, products_image, products_mediumimage, products_largeimage,  products_price, products_date_available, products_weight, products_length, products_width, products_height, products_ready_to_ship,products_tax_class_id, manufacturers_id, free_shipping, disclaimer_needed, in_store_pickup, lock_price from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
            $product = tep_db_fetch_array($product_query);

            tep_db_query("insert into " . TABLE_PRODUCTS . " (products_quantity, products_model,products_image, products_mediumimage, products_largeimage, products_price, products_date_added, products_date_available, products_weight, products_length, products_width, products_height, products_ready_to_ship,products_status, disclaimer_needed, products_tax_class_id, manufacturers_id, free_shipping, in_store_pickup, lock_price) values ('" . tep_db_input($product['products_quantity']) . "', '" . tep_db_input($product['products_model']) . "', '" . tep_db_input($product['products_image']) . "', '" . tep_db_input($product['products_mediumimage']) . "', '" . tep_db_input($product['products_largeimage']) . "', '" . tep_db_input($product['products_price']) . "',  now(), " . (empty($product['products_date_available']) ? "null" : "'" . tep_db_input($product['products_date_available']) . "'") . ", '" . tep_db_input($product['products_weight']) . "', '" . tep_db_input($product['products_length']) . "',  '" . tep_db_input($product['products_width']) . "', '" . tep_db_input($product['products_height']) . "', '" . tep_db_input($product['products_ready_to_ship']) . "','0', '" . (int)$product['products_tax_class_id'] . "', '" 
			. (int)$product['manufacturers_id'] . "', '" . (int)$product['free_shipping'] . "', '" . (int)$product['in_store_pickup'] . "', '" . (int)$product['lock_price'] . "')");
            $dup_products_id = tep_db_insert_id();

            $description_query = tep_db_query("select language_id, products_name, products_description, products_head_title_tag, products_head_desc_tag, products_head_keywords_tag, products_url from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$products_id . "'");
            while ($description = tep_db_fetch_array($description_query)) {
              tep_db_query("insert into " . TABLE_PRODUCTS_DESCRIPTION . " (products_id, language_id, products_name, products_description, products_head_title_tag, products_head_desc_tag, products_head_keywords_tag, products_url, products_viewed) values ('" . (int)$dup_products_id . "', '" . (int)$description['language_id'] . "', '" . tep_db_input($description['products_name']) . "', '" . tep_db_input($description['products_description']) . "', '" . tep_db_input($description['products_head_title_tag']) . "', '" . tep_db_input($description['products_head_desc_tag']) . "', '" . tep_db_input($description['products_head_keywords_tag']) . "', '" . tep_db_input($description['products_url']) . "', '0')");
            }

            tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id) values ('" . (int)$dup_products_id . "', '" . (int)$categories_id . "')");
            $products_id = $dup_products_id;
          }

          if (USE_CACHE == 'true') {
            tep_reset_cache_block('categories');
            tep_reset_cache_block('also_purchased');
          }
           
        }

        tep_redirect(tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $categories_id . '&pID=' . $products_id));
        break;
      case 'new_product_preview':
// copy image only if modified
        $products_image = new upload('products_image');
        $products_image->set_destination(DIR_FS_CATALOG_IMAGES);
        if ($products_image->parse() && $products_image->save()) {
          $products_image_name = $products_image->filename;
        } else {
          $products_image_name = (isset($HTTP_POST_VARS['products_previous_image']) ? $HTTP_POST_VARS['products_previous_image'] : '');
        }
        
        $products_mediumimage = new upload('products_mediumimage');
        $products_mediumimage->set_destination(DIR_FS_CATALOG_IMAGES);
        if ($products_mediumimage->parse() && $products_mediumimage->save()) {
          $products_mediumimage_name = $products_mediumimage->filename;
        } else {
          $products_mediumimage_name = (isset($HTTP_POST_VARS['products_previous_mediumimage']) ? $HTTP_POST_VARS['products_previous_mediumimage'] : '');
        }
        
        $products_largeimage = new upload('products_largeimage');
        $products_largeimage->set_destination(DIR_FS_CATALOG_IMAGES);
        if ($products_largeimage->parse() && $products_largeimage->save()) {
          $products_largeimage_name = $products_largeimage->filename;
        } else {
          $products_largeimage_name = (isset($HTTP_POST_VARS['products_previous_largeimage']) ? $HTTP_POST_VARS['products_previous_largeimage'] : '');
        }        
       
        $flag_prod_qty = tep_db_prepare_input($HTTP_POST_VARS['prod_inventory_flag']);
        $flag_prod_price = tep_db_prepare_input($HTTP_POST_VARS['prod_price_flag']);
        $flag_prod_cat = tep_db_prepare_input($HTTP_POST_VARS['prod_category_flag']);
		$flag_prod_desc = tep_db_prepare_input($HTTP_POST_VARS['prod_desc_flag']);
		$flag_prod_image = tep_db_prepare_input($HTTP_POST_VARS['prod_image_flag']);
        break;
    }
  }
  

// check if the catalog image directory exists
  if (is_dir(DIR_FS_CATALOG_IMAGES)) {
    if (!is_writeable(DIR_FS_CATALOG_IMAGES)) $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
  } else {
    $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
  mode : "textareas",
  editor_selector : "mceEditor",
  theme : "advanced",
  plugins : "table,advhr,advimage,advlink,emotions,preview,flash,print,contextmenu",
theme_advanced_buttons1_add : "fontselect,fontsizeselect",
theme_advanced_buttons2_add : "separator,preview,separator,forecolor,backcolor",
theme_advanced_buttons2_add_before: "cut,copy,paste,separator",
theme_advanced_buttons3_add_before : "tablecontrols,separator",
theme_advanced_buttons3_add : "emotions,flash,advhr,separator,print",
theme_advanced_toolbar_location : "top",
theme_advanced_toolbar_align : "left",
theme_advanced_path_location : "bottom",
extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
external_link_list_url : "example_data/example_link_list.js",
external_image_list_url : "example_data/example_image_list.js",
flash_external_list_url : "example_data/example_flash_list.js"
});

//BOF: bulk_category_movement
function stop_propogation(e){
	if (!e){
		var e = window.event;
	}
	e.cancelBubble = true;	
	if (e.stopPropogation){
		e.stopPropogation();
	}
}

function drp_cat_frontend_onchange(drpRef){
	try{
		var selectedIDs = '';
		var inputElems = document.getElementsByTagName('input');
		for (var i=0; i<inputElems.length; i++){
			if (inputElems[i].type.toLowerCase()=='checkbox'){
				if (inputElems[i].getAttribute('name')=='chk_set_frontend_cat'){
					if (inputElems[i].checked){
						selectedIDs += inputElems[i].value + '|';
					}
				}
			}
		}
		if (selectedIDs!=''){
			selectedIDs = selectedIDs.substring(0, selectedIDs.length-1);
		}
		document.getElementById('hdn_ids').value = selectedIDs;
		
		if (selectedIDs=='' || drpRef.options.selectedIndex==0){
			alert('No selection. Request cannot be processed.');
			drpRef.options.selectedIndex = 0;
			return false;
		}
		var val = drpRef.options[drpRef.options.selectedIndex].value;
		if (val=='UNSET'){
			if (confirm('This action will dissociate frontend category with checked products.\n\nNote:As of now, this action is set to fire only for checked products. The products in a checked category will not be taken into consideration.\n\n\nTo proceed anyways, click "ok" else "cancel"')){
				return true;
			} else {
				drpRef.options.selectedIndex = 0;
				return false;
			}
		} else {
			if (confirm('This action will associate selected frontend category with checked products/catagories.\n\nNote: For the products, in the categories selected by you (if any), only those products that are not associated with any frontend category will be taken into consideration.\n\n\nTo proceed anyways, click "ok" else "cancel"')){
				return true;
			} else {
				drpRef.options.selectedIndex = 0;
				return false;
			}
		}
		
	} catch(e){
		alert(e);
	}
}

function toggle_selection(chkToggleRef){
	var stat = (chkToggleRef.checked ? true : false);
	var inputElems = document.getElementsByTagName('input');
	for (var i=0; i<inputElems.length; i++){
		if (inputElems[i].type.toLowerCase()=='checkbox'){
			if (inputElems[i].getAttribute('name')=='chk_set_frontend_cat'){
				inputElems[i].checked = stat;
			}
		}
	}
}
//EOF: bulk_category_movement
</script>
<script language="javascript" src="includes/general.js"></script>

</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="goOnLoad();">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="780px" cellspacing="2" cellpadding="2" align="center" style="margin: 0px auto;">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top">
<?php
// If Updated_without_prieview was chosen, then  - OBN
  if (isset($HTTP_GET_VARS['Update_without_preview']))
  	{
		$action = 'update_product';
		$pID = $HTTP_GET_VARS['Update_pID'];
	}
  if ($action == 'new_product') {
    $parameters = array('products_name' => '',
                       'products_description' => '',
                       'products_url' => '',
                       'products_id' => '',
                       'products_quantity' => '',
                       'products_model' => '',
                       'products_size'  => '',
					   'disclaimer_needed'=>'',
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
					   'free_shipping'=>'',
					   'in_store_pickup'=>'',
					   'lock_price'=>'',
					   'markup' =>DEFAULT_MARKUP,
					   'manual_price' => '',
					   'base_price'=>'',
					   'roundoff_flag'=>'');
					   
    $pInfo = new objectInfo($parameters);
    if (isset($HTTP_GET_VARS['pID']) && empty($HTTP_POST_VARS)) {
    	/********************PRODUCT SIZE MOD BY FIW**************************/
      $product_query = tep_db_query("select pd.products_name, pd.products_description, pd.products_head_title_tag, pd.products_head_desc_tag, pd.products_head_keywords_tag, pd.products_url, p.products_id, p.products_quantity, p.products_size, p.disclaimer_needed, p.products_model, p.products_image, p.products_mediumimage, p.products_largeimage, p.products_price, p.base_price, p.markup, p.manual_price, p.products_weight, p.free_shipping, p.in_store_pickup, p.lock_price, p.roundoff_flag, products_length, products_width, products_height, products_ready_to_ship,p.products_date_added, p.products_last_modified, date_format(p.products_date_available, '%Y-%m-%d') as products_date_available, p.products_status, p.products_tax_class_id, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . (int)$HTTP_GET_VARS['pID'] . "' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'");
      /********************PRODUCT SIZE MOD BY FIW**************************/
      $product = tep_db_fetch_array($product_query);
      $pInfo->objectInfo($product);
      $roundoff_flag = $product['roundoff_flag'];
      $flag_prod_qty = 0;
	  $flag_prod_price = 0;
	  $flag_prod_cat = 0;
	  $flag_prod_desc = 0;
	  $flag_prod_image = 0;	   
	  $sql = tep_db_query("select flags from products_xml_feed_flags where products_id='" . (int)tep_db_prepare_input($HTTP_GET_VARS['pID']) . "'");
		if (tep_db_num_rows($sql)){
			$show_xml_feed_flags = 1;
			$sql_info = tep_db_fetch_array($sql);
			$flags = $sql_info['flags'];
			$flag_prod_qty = substr($flags, 0, 1);
			$flag_prod_price = substr($flags, 1, 1);
			$flag_prod_cat = substr($flags, 2, 1);
			$flag_prod_desc = substr($flags, 3, 1);
			$flag_prod_image = substr($flags, 4, 1);
		}else{
			$show_xml_feed_flags = 0;
		}
      
    } elseif (tep_not_null($HTTP_POST_VARS)) {
      $pInfo->objectInfo($HTTP_POST_VARS);
      $products_name = $HTTP_POST_VARS['products_name'];
      $products_description = $HTTP_POST_VARS['products_description'];
      $products_url = $HTTP_POST_VARS['products_url'];
      
			$flag_prod_qty = $HTTP_POST_VARS['flag_prod_qty'];
			$flag_prod_price = $HTTP_POST_VARS['flag_prod_price'];
			$flag_prod_cat = $HTTP_POST_VARS['flag_prod_cat'];
			$flag_prod_desc = $HTTP_POST_VARS['flag_prod_desc'];
			$flag_prod_image = $HTTP_POST_VARS['flag_prod_image'];
    }

    $manufacturers_array = array(array('id' => '', 'text' => TEXT_NONE));
    $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");
    while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
      $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'],
                                     'text' => $manufacturers['manufacturers_name']);
    }

    $tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $tax_class_query = tep_db_query("select tax_class_id, tax_class_title from " . TABLE_TAX_CLASS . " order by tax_class_title");
    while ($tax_class = tep_db_fetch_array($tax_class_query)) {
      $tax_class_array[] = array('id' => $tax_class['tax_class_id'],
                                 'text' => $tax_class['tax_class_title']);
    }

    $languages = tep_get_languages();

    if (!isset($pInfo->products_status)) $pInfo->products_status = '1';
    switch ($pInfo->products_status) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
    }
    /********************PRODUCT SIZE MOD BY FIW**************************/
     if (!isset($pInfo->products_size)) $pInfo->products_size = '0';
    	switch ($pInfo->products_size) {
      case '1': $in_size = false; $out_size = true; break;
      case '0':
      default: $in_size = true; $out_size = false;
    }
	
    /********************PRODUCT SIZE MOD BY FIW**************************/
	
	 /********************DISCLAIMER NEEDED**************************/
	
	 if (!isset($pInfo->disclaimer_needed))
	 	$pInfo->disclaimer_needed = '0';	 
     switch ($pInfo->disclaimer_needed) {	
      case '0': $disclaimer_yes = false; $disclaimer_no = true;	  
	  break;	  
      case '1':	  
      default: $disclaimer_yes = true;  $disclaimer_no = false;
    }
	 /********************DISCLAIMER NEEDED**************************/
	if (!isset($pInfo->free_shipping)){
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
	if (!isset($pInfo->lock_price)){
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

	if (!isset($pInfo->in_store_pickup)){
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
	if (!isset($flag_prod_qty)){
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
	if (!isset($flag_prod_price)){
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
	if (!isset($flag_prod_cat)){
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
	
	if (!isset($flag_prod_desc)){
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
	
	if (!isset($flag_prod_image)){
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
	if (empty($markup)){
		$markup = '0';
	}
		if(strpos( $markup, '-')){
			$operator = '-';
		} else {
			$operator = '+';
		}
		$markup = str_replace("-","",$markup);	
			if(strpos( $markup, '%')){
				$markup_flag = 'p';
				$markup = str_replace("%","",$markup);	
				$markup_price = $pInfo->base_price * ((float)$markup/100);
			}else{
				$markup_flag = 'f';
				$markup_price = (float)$markup;
			}
            if ($operator == '+') {
				$price = $pInfo->base_price + $markup_price;
			} else {
				$price = $pInfo->base_price - $markup_price;
			}
		$pInfo->qpu_price = ($roundoff_flag ? apply_roundoff(round($price,4)) : round($price,4));
		//echo 	$pInfo->qpu_price;

?>
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript"><!--
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo $pInfo->products_date_available; ?>",scBTNMODE_CUSTOMBLUE);
//--></script>
<script language="javascript"><!--
var tax_rates = new Array();
<?php
    for ($i=0, $n=sizeof($tax_class_array); $i<$n; $i++) {
      if ($tax_class_array[$i]['id'] > 0) {
        echo 'tax_rates["' . $tax_class_array[$i]['id'] . '"] = ' . tep_get_tax_rate_value($tax_class_array[$i]['id']) . ';' . "\n";
      }
    }
?>

function doRound(x, places) {
  return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
}

function getTaxRate() {
  var selected_value = document.forms["new_product"].products_tax_class_id.selectedIndex;
  var parameterVal = document.forms["new_product"].products_tax_class_id[selected_value].value;

  if ( (parameterVal > 0) && (tax_rates[parameterVal] > 0) ) {
    return tax_rates[parameterVal];
  } else {
    return 0;
  }
}

function updateGross() {
  var operator = "<?php echo $operator; ?>";
  var markup_flag = "<?php echo $markup_flag; ?>";
  var markup = parseFloat(<?php echo $markup; ?>);
  var roundoff_flag = '<?php echo $roundoff_flag; ?>';
  var markupPrice = 0;
  var grossValue = parseFloat(document.forms["new_product"].base_price.value);

if (markup_flag == 'p') {
  if (operator == "+") {
    grossValue = grossValue * ((markup / 100) + 1);
    } else {
	 grossValue = grossValue * (1 - (markup / 100));	
	}
  } else {
  if (operator == "+") {
    grossValue = grossValue + markup;
    } else {
	 grossValue =  grossValue - markup;	
	}
 }
  document.forms["new_product"].qpu_price.value = (roundoff_flag=='1' ? apply_roundoff(doRound(grossValue, 4)) : doRound(grossValue, 4));
}

function apply_roundoff(price_value){
	try{
	var response;
	if ((price_value+'').indexOf('.')==-1){
		response = (parseInt(price_value)-1) + '.99';
	}else{
		var value_parts = (price_value+'').split('.');
		if ((value_parts[1]+'').length>2){
			value_parts[1] = (value_parts[1]+'').substring(0, 2);
		}
		response = value_parts[0] + '.' + (parseInt(value_parts[1]) + (99 - parseInt(value_parts[1])));
	}
	return response;
	} catch(e){alert(e);}
}

//--></script>
    <?php
	// Old form button, this negates the need for the preview. - OBN
     echo tep_draw_form('new_product', FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . (isset($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '') . '&action=new_product_preview', 'post', 'enctype="multipart/form-data"');
    //echo tep_draw_form('new_product', FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . (isset($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '') . '&action=update_product', 'post', 'enctype="multipart/form-data"'); ?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sprintf(TEXT_NEW_PRODUCT, tep_output_generated_category_path($current_category_id)); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2" bgcolor="#FFFFFF">
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_STATUS; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('products_status', '1', $in_status) . '&nbsp;' . TEXT_PRODUCT_AVAILABLE . '&nbsp;' . tep_draw_radio_field('products_status', '0', $out_status) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE; ?></td>
          </tr>
          
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>

           <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_SIZE;?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('products_size', '0', $in_size) . '&nbsp;Standard&nbsp;' . tep_draw_radio_field('products_size', '1', $out_size) . '&nbsp;Oversized'; ?></td>
          </tr>
          
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
        
		  <tr>
            <td class="main"><?php echo TEXT_DISCLAIMER_NEEDED;?></td>
			
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('disclaimer_needed', '0', $disclaimer_no) . '&nbsp;No&nbsp;' . tep_draw_radio_field('disclaimer_needed', '1', $disclaimer_yes) . '&nbsp;Yes'; ?></td>
			
          </tr>		  
		<tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>  
		  <tr>
            <td class="main" valign="top"><?php echo 'Flags Status';?></td>			
            <td class="main">
            	<table>
            		<tr>
            			<td rowspan="8"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?></td>
            			<td class="main"><?php echo 'Is free shipping';?></td>
            			<td class="main"><?php echo '&nbsp;:&nbsp;';?></td>
            			<td class="main"><?php echo tep_draw_radio_field('free_shipping', '1', $free_shipping_yes) . '&nbsp;Yes&nbsp;' . tep_draw_radio_field('free_shipping', '0', $free_shipping_no) . '&nbsp;No'; ?></td>
					</tr>
            		<tr>
            			<td class="main"><?php echo 'Is in-store pickup';?></td>
            			<td class="main"><?php echo '&nbsp;:&nbsp;';?></td>
            			<td class="main"><?php echo tep_draw_radio_field('in_store_pickup', '1', $in_store_pickup_yes) . '&nbsp;Yes&nbsp;' . tep_draw_radio_field('in_store_pickup', '0', $in_store_pickup_no) . '&nbsp;No'; ?></td>
					</tr>
            		<tr>
            			<td class="main"><?php echo 'Lock Price';?></td>
            			<td class="main"><?php echo '&nbsp;:&nbsp;';?></td>
            			<td class="main"><?php echo tep_draw_radio_field('lock_price', '1', $lock_price_yes) . '&nbsp;Yes&nbsp;' . tep_draw_radio_field('lock_price', '0', $lock_price_no) . '&nbsp;No'; ?></td>
					</tr>
					<?php //if ($show_xml_feed_flags) { ?>
	            		<tr>
	            			<td class="main"><?php echo 'XML feed -> Update product inventory';?></td>
	            			<td class="main"><?php echo '&nbsp;:&nbsp;';?></td>
	            			<td class="main"><?php echo tep_draw_radio_field('prod_inventory_flag', '1', $update_prod_inventory_yes) . '&nbsp;Yes&nbsp;' . tep_draw_radio_field('prod_inventory_flag', '0', $update_prod_inventory_no) . '&nbsp;No'; ?></td>
						</tr>
	            		<tr>
	            			<td class="main"><?php echo 'XML feed -> Update product price';?></td>
	            			<td class="main"><?php echo '&nbsp;:&nbsp;';?></td>
	            			<td class="main"><?php echo tep_draw_radio_field('prod_price_flag', '1', $update_prod_price_yes) . '&nbsp;Yes&nbsp;' . tep_draw_radio_field('prod_price_flag', '0', $update_prod_price_no) . '&nbsp;No'; ?></td>
						</tr>
	            		<tr>
	            			<td class="main"><?php echo 'XML feed -> Update category';?></td>
	            			<td class="main"><?php echo '&nbsp;:&nbsp;';?></td>
	            			<td class="main"><?php echo tep_draw_radio_field('prod_category_flag', '1', $update_prod_category_yes) . '&nbsp;Yes&nbsp;' . tep_draw_radio_field('prod_category_flag', '0', $update_prod_category_no) . '&nbsp;No'; ?></td>
						</tr>
	            		<tr>
	            			<td class="main"><?php echo 'XML feed -> Update product description';?></td>
	            			<td class="main"><?php echo '&nbsp;:&nbsp;';?></td>
	            			<td class="main"><?php echo tep_draw_radio_field('prod_desc_flag', '1', $update_prod_desc_yes) . '&nbsp;Yes&nbsp;' . tep_draw_radio_field('prod_desc_flag', '0', $update_prod_desc_no) . '&nbsp;No'; ?></td>
						</tr>
	            		<tr>
	            			<td class="main"><?php echo 'XML feed -> Update product image';?></td>
	            			<td class="main"><?php echo '&nbsp;:&nbsp;';?></td>
	            			<td class="main"><?php echo tep_draw_radio_field('prod_image_flag', '1', $update_prod_image_yes) . '&nbsp;Yes&nbsp;' . tep_draw_radio_field('prod_image_flag', '0', $update_prod_image_no) . '&nbsp;No'; ?></td>
						</tr>
					<?php //} ?>
				</table>
			</td>
			
          </tr>          
		<tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>		  
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_DATE_AVAILABLE; ?><br><small>(YYYY-MM-DD)</small></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?>
			<script language="javascript">dateAvailable.writeControl(); dateAvailable.dateFormat="yyyy-MM-dd";</script></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_MANUFACTURER; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $pInfo->manufacturers_id); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main"><?php if ($i == 0) echo TEXT_PRODUCTS_NAME; ?></td>
            <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('products_name[' . $languages[$i]['id'] . ']', (isset($products_name[$languages[$i]['id']]) ? stripslashes($products_name[$languages[$i]['id']]) : tep_get_products_name($pInfo->products_id, $languages[$i]['id']))); ?></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr bgcolor="#ebebff">
            <td class="main"><?php echo TEXT_PRODUCTS_TAX_CLASS; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $pInfo->products_tax_class_id); ?></td>
          </tr>
          <tr bgcolor="#ebebff">
            <td class="main"><?php echo 'Product Cost:'; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('base_price', ((int)$pInfo->base_price<=0 ? '' : $pInfo->base_price), 'onKeyUp="updateGross()"'); ?></td>
          </tr>
          <tr bgcolor="#ebebff">
            <td class="main"><?php echo 'QPU Price: '; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?><input type="text" name="qpu_price" value="<?php echo (int)$pInfo->base_price<=0 ? '' : $pInfo->qpu_price; ?>" readonly></td>
          </tr>
           <tr bgcolor="#ebebff">
            <td class="main"><?php echo 'Manual Price: '; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('manual_price',$pInfo->manual_price); ?></td>
          </tr>
  <!-- AJAX Attribute Manager  -->
          <tr>
          	<td colspan="2"><?php require_once( 'attributeManager/includes/attributeManagerPlaceHolder.inc.php' )?></td>
          </tr>
<!-- AJAX Attribute Manager end -->        
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
        
<!-- HTC BOC //-->
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
         <tr>
            <td class="main" valign="top"><?php if ($i == 0) echo TEXT_PRODUCTS_DESCRIPTION; ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('products_description[' . $languages[$i]['id'] . ']', 'soft', '70', '15',(isset($products_description[$languages[$i]['id']]) ? stripslashes($products_description[$languages[$i]['id']]) : tep_get_products_description($pInfo->products_id, $languages[$i]['id'])),'class="mceEditor"'); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2" class="main"><hr><?php echo TEXT_PRODUCT_METTA_INFO; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>          
<?php         
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) echo TEXT_PRODUCTS_PAGE_TITLE; ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('products_head_title_tag[' . $languages[$i]['id'] . ']', 'soft', '70', '5', (isset($products_head_title_tag[$languages[$i]['id']]) ? stripslashes($products_head_title_tag[$languages[$i]['id']]) : tep_get_products_head_title_tag($pInfo->products_id, $languages[$i]['id']))); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>          
           <tr>
            <td class="main" valign="top"><?php if ($i == 0) echo TEXT_PRODUCTS_HEADER_DESCRIPTION; ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('products_head_desc_tag[' . $languages[$i]['id'] . ']', 'soft', '70', '5', (isset($products_head_desc_tag[$languages[$i]['id']]) ? stripslashes($products_head_desc_tag[$languages[$i]['id']]) : tep_get_products_head_desc_tag($pInfo->products_id, $languages[$i]['id']))); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>          
           <tr>
            <td class="main" valign="top"><?php if ($i == 0) echo TEXT_PRODUCTS_KEYWORDS; ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('products_head_keywords_tag[' . $languages[$i]['id'] . ']', 'soft', '70', '5', (isset($products_head_keywords_tag[$languages[$i]['id']]) ? stripslashes($products_head_keywords_tag[$languages[$i]['id']]) : tep_get_products_head_keywords_tag($pInfo->products_id, $languages[$i]['id']))); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td colspan="2" class="main"><hr></td>
          </tr>
<!-- HTC EOC //-->
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_QUANTITY; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_quantity', $pInfo->products_quantity); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_MODEL; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_model', $pInfo->products_model); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_IMAGE; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_file_field('products_image') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $pInfo->products_image . tep_draw_hidden_field('products_previous_image', $pInfo->products_image); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo 'Medium Image: '; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_file_field('products_mediumimage') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $pInfo->products_mediumimage . tep_draw_hidden_field('products_previous_mediumimage', $pInfo->products_mediumimage); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo 'Large Image: '; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_file_field('products_largeimage') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $pInfo->products_largeimage . tep_draw_hidden_field('products_previous_largeimage', $pInfo->products_largeimage); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main"><?php if ($i == 0) echo TEXT_PRODUCTS_URL . '<br><small>' . TEXT_PRODUCTS_URL_WITHOUT_HTTP . '</small>'; ?></td>
            <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('products_url[' . $languages[$i]['id'] . ']', (isset($products_url[$languages[$i]['id']]) ? stripslashes($products_url[$languages[$i]['id']]) : tep_get_products_url($pInfo->products_id, $languages[$i]['id']))); ?></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_WEIGHT; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_weight', $pInfo->products_weight); ?></td>
          </tr>
           <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_LENGTH; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_length', $pInfo->products_length); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_WIDTH; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_width', $pInfo->products_width); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_HEIGHT; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_height', $pInfo->products_height); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_READY_TO_SHIP; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_checkbox_field('products_ready_to_ship', '1', (($product['products_ready_to_ship'] == '1') ? true : false)); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main" align="right">
		  <?php // have two different submit buttons - OBN
		  echo '<input border="0" type="image" title=" Quick Update " value="Update_without_preview" alt="Quick Update" src="includes/languages/english/images/buttons/button_quick_update.gif" name="Update_without_preview" />';
		  echo '&nbsp;&nbsp';
		  echo '<input type="hidden" value="' . $HTTP_GET_VARS['pID'] . '" name="Update_pID">';
          echo tep_draw_hidden_field('products_date_added', (tep_not_null($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d')));
		  echo tep_image_submit('button_preview_b.gif', IMAGE_PREVIEW) . '&nbsp;&nbsp;';
		  echo '<a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . (isset($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '')) . '">';
		  echo tep_image_button('button_cancel_b.gif', IMAGE_CANCEL) . '</a>'; ?>
        </td>
      </tr>
    </table></form>
<?php
  } elseif ($action == 'new_product_preview') {
    if (tep_not_null($HTTP_POST_VARS)) {
      $pInfo = new objectInfo($HTTP_POST_VARS);
      $products_name = $HTTP_POST_VARS['products_name'];
      $products_description = $HTTP_POST_VARS['products_description'];
      $products_head_title_tag = $HTTP_POST_VARS['products_head_title_tag'];
      $products_head_desc_tag = $HTTP_POST_VARS['products_head_desc_tag'];
      $products_head_keywords_tag = $HTTP_POST_VARS['products_head_keywords_tag'];
      $products_url = $HTTP_POST_VARS['products_url'];
    } else {
    	/********************PRODUCT SIZE MOD BY FIW**************************/
      $product_query = tep_db_query("select p.products_id, pd.language_id, pd.products_name, pd.products_description,pd.products_head_title_tag, pd.products_head_desc_tag, pd.products_head_keywords_tag, pd.products_url, p.products_quantity, p.products_model, p.products_image, p.products_mediumimage, p.products_largeimage,p.products_price, p.products_weight, p.products_length, p.products_width, p.products_height, p.products_ready_to_ship, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_size, p.disclaimer_needed, p.manufacturers_id, p.free_shipping, p.in_store_pickup, p.lock_price  from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and p.products_id = '" . (int)$HTTP_GET_VARS['pID'] . "'");
      /********************PRODUCT SIZE MOD BY FIW**************************/
      $product = tep_db_fetch_array($product_query);

      $pInfo = new objectInfo($product);
      $products_image_name = $pInfo->products_image;
    }

    $form_action = (isset($HTTP_GET_VARS['pID'])) ? 'update_product' : 'insert_product';

    echo tep_draw_form($form_action, FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . (isset($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '') . '&action=' . $form_action, 'post', 'enctype="multipart/form-data"');
    $languages = tep_get_languages();
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
      if (isset($HTTP_GET_VARS['read']) && ($HTTP_GET_VARS['read'] == 'only')) {
        $pInfo->products_name = tep_get_products_name($pInfo->products_id, $languages[$i]['id']);
        $pInfo->products_description = tep_get_products_description($pInfo->products_id, $languages[$i]['id']);
        $pInfo->products_head_title_tag = tep_db_prepare_input($products_head_title_tag[$languages[$i]['id']]);
        $pInfo->products_head_desc_tag = tep_db_prepare_input($products_head_desc_tag[$languages[$i]['id']]);
        $pInfo->products_head_keywords_tag = tep_db_prepare_input($products_head_keywords_tag[$languages[$i]['id']]);
		$pInfo->products_url = tep_get_products_url($pInfo->products_id, $languages[$i]['id']);
      } else {
        $pInfo->products_name = tep_db_prepare_input($products_name[$languages[$i]['id']]);
        $pInfo->products_description = tep_db_prepare_input($products_description[$languages[$i]['id']]);
        $pInfo->products_head_title_tag = tep_db_prepare_input($products_head_title_tag[$languages[$i]['id']]);
        $pInfo->products_head_desc_tag = tep_db_prepare_input($products_head_desc_tag[$languages[$i]['id']]);
        $pInfo->products_head_keywords_tag = tep_db_prepare_input($products_head_keywords_tag[$languages[$i]['id']]);
        $pInfo->products_url = tep_db_prepare_input($products_url[$languages[$i]['id']]);
      }
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $pInfo->products_name; ?></td>
            <td class="pageHeading" align="right">
<?php
// Old Price - OBN
//		echo $currencies->format($pInfo->products_price);
		if($HTTP_POST_VARS['manual_price'] > 0.0000)
			echo $currencies->format($HTTP_POST_VARS['manual_price']);
		else
			echo $currencies->format($HTTP_POST_VARS['base_price']);
?>
            </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main">
		<?php
		echo '<img src="'.$products_image_name.'" align="right" hspace="5" vspace="5" title="'.$pInfo->products_name.' alt="'.$pInfo->products_name.'">'. $pInfo->products_description;
		// Old display image - OBN
		//echo tep_image($products_image_name, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'align="right" hspace="5" vspace="5"') . $pInfo->products_description;
		?></td>
      </tr>
<?php
      if ($pInfo->products_url) {
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><?php echo sprintf(TEXT_PRODUCT_MORE_INFORMATION, $pInfo->products_url); ?></td>
      </tr>
<?php
      }
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
<?php
      if ($pInfo->products_date_available > date('Y-m-d')) {
?>
      <tr>
        <td align="center" class="smallText"><?php echo sprintf(TEXT_PRODUCT_DATE_AVAILABLE, tep_date_long($pInfo->products_date_available)); ?></td>
      </tr>
<?php
      } else {
?>
      <tr>
        <td align="center" class="smallText"><?php echo sprintf(TEXT_PRODUCT_DATE_ADDED, tep_date_long($pInfo->products_date_added)); ?></td>
      </tr>
<?php
      }
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
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
        $back_url = FILENAME_FRONTENDCATEGORIES_PRODUCTS;
        $back_url_params = 'cPath=' . $cPath . '&pID=' . $pInfo->products_id;
      }
?>
      <tr>
        <td align="right"><?php echo '<a href="' . tep_href_link($back_url, $back_url_params, 'NONSSL') . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
      </tr>
<?php
    } else {
?>
      <tr>
        <td align="right" class="smallText">
<?php
/* Re-Post all POST'ed variables */
      reset($HTTP_POST_VARS);
      while (list($key, $value) = each($HTTP_POST_VARS)) {
        if (!is_array($HTTP_POST_VARS[$key])) {
          echo tep_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
        }
      }
      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        echo tep_draw_hidden_field('products_name[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_name[$languages[$i]['id']])));
        echo tep_draw_hidden_field('products_description[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_description[$languages[$i]['id']])));
        echo tep_draw_hidden_field('products_head_title_tag[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_head_title_tag[$languages[$i]['id']])));
        echo tep_draw_hidden_field('products_head_desc_tag[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_head_desc_tag[$languages[$i]['id']])));
        echo tep_draw_hidden_field('products_head_keywords_tag[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_head_keywords_tag[$languages[$i]['id']])));
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

      echo tep_image_submit('button_back.gif', IMAGE_BACK, 'name="edit"') . '&nbsp;&nbsp;';

      if (isset($HTTP_GET_VARS['pID'])) {
		echo tep_image_submit('button_update.gif', IMAGE_UPDATE);
      } else {
        echo tep_image_submit('button_insert.gif', IMAGE_INSERT);
      }
      echo '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . (isset($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>';
?></td>
      </tr>
    </table></form>
<?php
    }
  } else {
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td align="right"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="smallText" align="right">
<?php
    echo tep_draw_form('search', FILENAME_FRONTENDCATEGORIES_PRODUCTS, '', 'get');
    echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('search');
    echo '</form>';
?>
                </td>
              </tr>
              <tr>
                <td class="smallText" align="right">
<?php
    echo tep_draw_form('goto', FILENAME_FRONTENDCATEGORIES_PRODUCTS, '', 'get');
    echo HEADING_TITLE_GOTO . ' ' . tep_draw_pull_down_menu('cPath', tep_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"');
    echo '</form>';
?>
                </td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" width="300px"><?php echo TABLE_HEADING_CATEGORIES_PRODUCTS; ?></td>
                <?php //BOF:category_group ?>
                <td class="dataTableHeadingContent"><?php echo 'Is Cat Grp'; ?></td>
                <?php //BOF:category_group ?>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                <!-- BOF: bulk_category_movement -->
                <td class="dataTableHeadingContent" align="right"><?php echo 'Frontend Category<br>[ ' . tep_draw_selection_field('chk_set_frontend_cat_all', 'checkbox', '', false, '', 'onclick="javascript:toggle_selection(this);"') . ' ]'; ?></td>
                <!-- EOF: bulk_category_movement -->
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $categories_count = 0;
    $rows = 0;
    if (isset($HTTP_GET_VARS['search']))
	  {
    	$search = tep_db_prepare_input($HTTP_GET_VARS['search']);
//Categry Status MOD BEGIN by FIW
	  //BOF:category_group
      $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.banner_image, c.parent_id, c.sort_order, c.categories_status, c.date_added, c.last_modified, cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_htc_description from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and cd.categories_name like '%" . tep_db_input($search) . "%' order by c.sort_order, cd.categories_name");
		//$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.banner_image, c.parent_id, c.sort_order, c.categories_status, c.date_added, c.last_modified, c.is_category_group, cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_htc_description from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and cd.categories_name like '%" . tep_db_input($search) . "%' order by c.sort_order, cd.categories_name");
      //EOF:category_group
      }
	else
	  {
   	  //BOF:category_group
      //$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.banner_image, c.parent_id, c.sort_order, c.categories_status, c.date_added, c.last_modified, cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_htc_description from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by c.sort_order, cd.categories_name");
		$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.banner_image, c.parent_id, c.sort_order, c.categories_status, c.date_added, c.last_modified, c.is_category_group, cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_htc_description from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by c.sort_order, cd.categories_name");
      //EOF:category_group
	  }
//Categry Status MOD END by FIW
	while ($categories = tep_db_fetch_array($categories_query))
	  {
		$categories_count++;
		$rows++;

// Get parent_id for subcategories if search
		if (isset($HTTP_GET_VARS['search'])) $cPath= $categories['parent_id'];

		if ((!isset($HTTP_GET_VARS['cID']) && !isset($HTTP_GET_VARS['pID']) || (isset($HTTP_GET_VARS['cID']) && ($HTTP_GET_VARS['cID'] == $categories['categories_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new'))
		  {
			$category_childs = array('childs_count' => tep_childs_in_category_count($categories['categories_id']));
			$category_products = array('products_count' => tep_products_in_category_count($categories['categories_id']));
			$cInfo_array = array_merge($categories, $category_childs, $category_products);
			$cInfo = new objectInfo($cInfo_array);
          //Categories status MOD bEGIN by FIW 
	    	if (!isset($cInfo->categories_status)) $cInfo->categories_status = '1';
			switch ($cInfo->categories_status)
			  {
			    case '0': $in_status = false; $out_status = true; break;
	    		case '1':
				default: $in_status = true; $out_status = false;
			  }
       //Categories status MOD END by FIW
	   //BOF:category_box
			if (!isset($cInfo->is_category_group))
			  {
				$cInfo->is_category_group = '0';
			  }
			switch ($cInfo->is_category_group)
			  {
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
		if (isset($cInfo) && is_object($cInfo) && ($categories['categories_id'] == $cInfo->categories_id) )
		  {
        	echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, tep_get_path($categories['categories_id'])) . '\'">' . "\n";
		  }
		else
		  {
	        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '\'">' . "\n";
		  }
?>
                <td class="dataTableContent" valign="top"><?php echo '<a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, tep_get_path($categories['categories_id'])) . '">' . tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a>&nbsp;<b>' . $categories['categories_name'] . '</b>'; ?></td>
                <?php //BOF:category_group ?>
                <td class="dataTableContent" valign="top"><?php echo ($categories['is_category_group'] ? 'Y' : 'N'); ?></td>
                <?php //EOF:category_group ?>
                <td class="dataTableContent" valign="top" align="center"><?php
                //Categroies Status MOD BEGIN by FIW
		if ($categories['categories_status'] == '1')
		  {
        	echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'action=setflagc&flag=0&cPath=' . $cPath) . '&cID=' . $categories['categories_id'] . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
		  }
		else
		  {
			echo '<a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'action=setflagc&flag=1&cPath=' . $cPath) . '&cID=' . $categories['categories_id'] . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
		  }
  //Categroies Status MOD END by FIW
  ?></td>
  <!-- BOF: bulk_category_movement -->
  <td valign="top" align="right" class="dataTableContent">
	<?php 
			echo tep_draw_selection_field('chk_set_frontend_cat', 'checkbox', 'C' . $categories['categories_id'], false, '', 'id="' . 'C' . $categories['categories_id'] . '"');
			echo '<script type="text/javascript">' .
					'var elem = document.getElementById("' . 'C' . $categories['categories_id'] . '");' .
					//'elem.addEventListener("click", function(e) { stop_propogation(e); }, true);' .
					'elem.onclick = function(e) { stop_propogation(e); }' .
				 '</script>';	
	?> 	
  </td>
  <!-- EOF: bulk_category_movement -->
                <td class="dataTableContent" valign="top" align="right"><?php if (isset($cInfo) && is_object($cInfo) && ($categories['categories_id'] == $cInfo->categories_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
	  }
  // Added a search p.products_model to search - OBN
    $products_count = 0;
    if (isset($HTTP_GET_VARS['search'])) {
		$products_query = tep_db_query("select p.products_id, pd.products_name, p.products_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_model, p2c.categories_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and (pd.products_name like '%" . tep_db_input($search) . "%' or p.products_model like '%" . tep_db_input($search) . "%') order by pd.products_name");
// OLD SEARCH
//      $products_query = tep_db_query("select p.products_id, pd.products_name, p.products_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_model, p2c.categories_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and pd.products_name like '%" . tep_db_input($search) . "%' order by pd.products_name");
    } else {
      $products_query = tep_db_query("select p.products_id, pd.products_name, p.products_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_model from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$current_category_id . "' order by pd.products_name");
    }
    while ($products = tep_db_fetch_array($products_query)) {
      $products_count++;
      $rows++;

// Get categories_id for product if search
      if (isset($HTTP_GET_VARS['search'])) $cPath = $products['categories_id'];

      if ( (!isset($HTTP_GET_VARS['pID']) && !isset($HTTP_GET_VARS['cID']) || (isset($HTTP_GET_VARS['pID']) && ($HTTP_GET_VARS['pID'] == $products['products_id']))) && !isset($pInfo) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
// find out the rating average from customer reviews
        $reviews_query = tep_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . (int)$products['products_id'] . "'");
        $reviews = tep_db_fetch_array($reviews_query);
        $pInfo_array = array_merge($products, $reviews);
        $pInfo = new objectInfo($pInfo_array);
      }

      if (isset($pInfo) && is_object($pInfo) && ($products['products_id'] == $pInfo->products_id) ) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only') . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $products['products_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '<a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $products['products_name']; ?></td>
                <?php //BOF:category_group ?>
                <td class="dataTableContent"><?php echo '--'; ?></td>
                <?php //EOF:category_group ?>
                <td class="dataTableContent" align="center">
<?php
      if ($products['products_status'] == '1') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'action=setflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
  <!-- BOF: bulk_category_movement -->
	<td align="right" class="dataTableContent">
<?php
	$frontend_cat_name = get_frontend_category_name_by_product_id($products['products_id']);
	//if (empty($frontend_cat_name)){
		echo tep_draw_selection_field('chk_set_frontend_cat', 'checkbox', 'P' . $products['products_id'], false, '', 'id="' . 'P' . $products['products_id'] . '"');
		echo (empty($frontend_cat_name) ? '' : '&nbsp;' . $frontend_cat_name);
		echo '<script type="text/javascript">' .
			'var elem = document.getElementById("' . 'P' . $products['products_id'] . '");' .
			//'elem.addEventListener("click", function(e) { stop_propogation(e); }, true);' .
			'elem.onclick = function(e) { stop_propogation(e); }' .
		 '</script>';
	//} else {
	//	echo $frontend_cat_name;
	//}
?>
	</td>
  <!-- EOF: bulk_category_movement -->
                <td class="dataTableContent" align="right"><?php if (isset($pInfo) && is_object($pInfo) && ($products['products_id'] == $pInfo->products_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $products['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }

    $cPath_back = '';
    if (sizeof($cPath_array) > 0) {
      for ($i=0, $n=sizeof($cPath_array)-1; $i<$n; $i++) {
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
		<td colspan="5" align="right" class="main" style="padding:10px 0px 10px 0px;">
						<?php
							$form_params = 'cPath=' . $HTTP_GET_VARS['cPath'];
							if (isset($HTTP_GET_VARS['cID'])){
								$form_params .= '&' . 'cID=' . $HTTP_GET_VARS['cID'];
							}
							if (isset($HTTP_GET_VARS['pID'])){
								$form_params .= '&' . 'pID=' . $HTTP_GET_VARS['pID'];
							}
							$form_params .= '&' . 'action=set_frontend_cat';
						    echo tep_draw_form('frontend', FILENAME_FRONTENDCATEGORIES_PRODUCTS, $form_params);
						    echo tep_draw_hidden_field('hdn_ids', '', 'id="hdn_ids"');
						    echo '<span class="smallText2">Lock Category: </span> ' . tep_draw_checkbox_field('category_flag','0',true) . '<br>';
						    //echo 'Select Forntend Category: ' . ' ' . tep_draw_pull_down_menu('drp_cat_frontend', array_merge(get_frontend_category_tree(), array(array('id' => 'UNSET', 'text'=> 'UNSET FRONTEND CATEGORY'))), '', 'onchange="javascript:if (drp_cat_frontend_onchange(this)) { this.form.submit(); };"');
						    $frontend_cat_tree = get_frontend_category_tree();
						    $frontend_cat_tree[0]['text'] = '-- Select  --';
							echo '<span class="smallText2">Select Frontend Category:</span> ' . ' ' . tep_draw_pull_down_menu('drp_cat_frontend', array_merge($frontend_cat_tree, array(array('id' => 'UNSET', 'text'=> 'UNSET FRONTEND CATEGORY'))), '', 'onchange="javascript:if (drp_cat_frontend_onchange(this)) { this.form.submit(); };"');
						    echo '</form>';
						?>
		</td>
	</tr>
<!-- EOF: bulk_category_movement -->
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText2"><?php echo TEXT_CATEGORIES . '&nbsp;' . $categories_count . '<br>' . TEXT_PRODUCTS . '&nbsp;' . $products_count; ?></td>
                    <td align="right" class="smallText"><?php if (sizeof($cPath_array) > 0) echo '<a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, $cPath_back . 'cID=' . $current_category_id) . '">' . tep_image_button('button_back_b.gif', IMAGE_BACK) . '</a>&nbsp;'; ?></td>
                  </tr>
                </table></td>
              </tr>
            </table>
			</td>
 
<?php
    $heading = array();
    $contents = array();
    switch ($action) {
      case 'move_category':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_CATEGORY . '</b>');

        $contents = array('form' => tep_draw_form('categories', FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'action=move_category_confirm&cPath=' . $cPath) . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
        $contents[] = array('text' => sprintf(TEXT_MOVE_CATEGORIES_INTRO, $cInfo->categories_name));
        $contents[] = array('text' => '<br>' . sprintf(TEXT_MOVE, $cInfo->categories_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree(), $current_category_id));
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'delete_product':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_PRODUCT . '</b>');

        $contents = array('form' => tep_draw_form('products', FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'action=delete_product_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id));
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
          $product_categories_string .= tep_draw_checkbox_field('product_categories[]', $product_categories[$i][sizeof($product_categories[$i])-1]['id'], true) . '&nbsp;' . $category_path . '<br>';
        }
        $product_categories_string = substr($product_categories_string, 0, -4);

        $contents[] = array('text' => '<br>' . $product_categories_string);
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'move_product':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_PRODUCT . '</b>');

        $contents = array('form' => tep_draw_form('products', FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'action=move_product_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id));
        $contents[] = array('text' => sprintf(TEXT_MOVE_PRODUCTS_INTRO, $pInfo->products_name));
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br><b>' . tep_output_generated_category_path($pInfo->products_id, 'product') . '</b>');
        $contents[] = array('text' => '<br>' . sprintf(TEXT_MOVE, $pInfo->products_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree(), $current_category_id));
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'copy_to':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_COPY_TO . '</b>');

        $contents = array('form' => tep_draw_form('copy_to', FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'action=copy_to_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id));
        $contents[] = array('text' => TEXT_INFO_COPY_TO_INTRO);
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br><b>' . tep_output_generated_category_path($pInfo->products_id, 'product') . '</b>');
        $contents[] = array('text' => '<br>' . TEXT_CATEGORIES . '<br>' . tep_draw_pull_down_menu('categories_id', tep_get_category_tree(), $current_category_id));
        $contents[] = array('text' => '<br>' . TEXT_HOW_TO_COPY . '<br>' . tep_draw_radio_field('copy_as', 'link', true) . ' ' . TEXT_COPY_AS_LINK . '<br>' . tep_draw_radio_field('copy_as', 'duplicate') . ' ' . TEXT_COPY_AS_DUPLICATE);
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_copy.gif', IMAGE_COPY) . ' <a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      default:
        if ($rows > 0) {
          if (isset($cInfo) && is_object($cInfo)) { // category info box contents
            $heading[] = array('text' => '<b>' . $cInfo->categories_name . '</b>');

            $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link('categopries_frontend.php', 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=edit_category') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link('categopries_frontend.php', 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=delete_category') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a> <a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=move_category') . '">' . tep_image_button('button_move.gif', IMAGE_MOVE) . '</a>');
            $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . tep_date_short($cInfo->date_added));
            if (tep_not_null($cInfo->last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($cInfo->last_modified));
            $contents[] = array('text' => '<br>' . tep_info_image($cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT) . '<br>' . $cInfo->categories_image);
            $contents[] = array('text' => '<br>' . TEXT_SUBCATEGORIES . ' ' . $cInfo->childs_count . '<br>' . TEXT_PRODUCTS . ' ' . $cInfo->products_count);
          } elseif (isset($pInfo) && is_object($pInfo)) { // product info box contents
            $heading[] = array('text' => '<b>' . tep_get_products_name($pInfo->products_id, $languages_id) . '</b>');
/* Ebay modification here !!!! */
            $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=new_product') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=delete_product') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a> <a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=move_product') . '">' . tep_image_button('button_move.gif', IMAGE_MOVE) . '</a> <a href="' . tep_href_link(FILENAME_FRONTENDCATEGORIES_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=copy_to') . '">' . tep_image_button('button_copy_to.gif', IMAGE_COPY_TO) . '</a>');

            $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . tep_date_short($pInfo->products_date_added));
            if (tep_not_null($pInfo->products_last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($pInfo->products_last_modified));
            if (date('Y-m-d') < $pInfo->products_date_available) $contents[] = array('text' => TEXT_DATE_AVAILABLE . ' ' . tep_date_short($pInfo->products_date_available));
            $contents[] = array('text' => '<br>' . tep_info_image($pInfo->products_image, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '<br>' . $pInfo->products_image);
            $contents[] = array('text' => '<br>' . TEXT_PRODUCTS_PRICE_INFO . ' ' . $currencies->format($pInfo->products_price) . '<br>' . TEXT_PRODUCTS_QUANTITY_INFO . ' ' . $pInfo->products_quantity);
            $contents[] = array('text' => '<br>' . TEXT_PRODUCTS_AVERAGE_RATING . ' ' . number_format($pInfo->average_rating, 2) . '%');
          }
        } else { // create category/product info
          $heading[] = array('text' => '<b>' . EMPTY_CATEGORY . '</b>');

          $contents[] = array('text' => TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS);
        }
        break;
    }

    if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
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
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>