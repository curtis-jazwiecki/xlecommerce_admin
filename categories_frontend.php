<?php

/*

  $Id: categories_frontend.php

*/

require('includes/application_top.php');

require(DIR_WS_CLASSES . 'currencies.php');

include('includes/languages/english/categories.php');

require_once('includes/functions/frontend_category.php');

 



$currencies = new currencies();



$action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

if (tep_not_null($action)) {

	switch ($action) {

		case 'setflag':

			if ( ($HTTP_GET_VARS['flag'] == '0') || ($HTTP_GET_VARS['flag'] == '1') ) {

          		if (isset($HTTP_GET_VARS['pID'])) {

					tep_set_product_status($HTTP_GET_VARS['pID'], $HTTP_GET_VARS['flag']);

				}

        	}

        	tep_redirect(tep_href_link('categories_frontend.php', 'cPath=' . $HTTP_GET_VARS['cPath'] . '&pID=' . $HTTP_GET_VARS['pID']));

        	break;

		case 'setflagc':

	        if ( ($HTTP_GET_VARS['flag'] == '0') || ($HTTP_GET_VARS['flag'] == '1') ) {

	          if (isset($HTTP_GET_VARS['cID'])) {

	            set_frontend_category_status($HTTP_GET_VARS['cID'], $HTTP_GET_VARS['flag']);

	          }

	        }

	        tep_redirect(tep_href_link('categories_frontend.php', 'cPath=' . $HTTP_GET_VARS['cPath'] . '&cID=' . $HTTP_GET_VARS['cID']));

	        break;

		case 'insert_category':

		case 'update_category':

        	if (isset($HTTP_POST_VARS['categories_id'])){

        		$categories_id = tep_db_prepare_input($HTTP_POST_VARS['categories_id']);

        	}

        	$sort_order = tep_db_prepare_input($HTTP_POST_VARS['sort_order']);

        	$sql_data_array = array('sort_order' => (int)$sort_order);



        	if ($action == 'insert_category') {

          		$insert_sql_data = array('parent_id' => $current_category_id,

                	                     'date_added' => 'now()');

          		$sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          		tep_db_perform('frontend_categories', $sql_data_array);

				$categories_id = tep_db_insert_id();

    	    } elseif ($action == 'update_category') {

         		$update_sql_data = array('last_modified' => 'now()');

          		$sql_data_array = array_merge($sql_data_array, $update_sql_data);

          		tep_db_perform('frontend_categories', $sql_data_array, 'update', "categories_id = '" . (int)$categories_id . "'");

        	}

        	$languages = tep_get_languages();

        	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {

          		$categories_name_array = $HTTP_POST_VARS['categories_name'];
                $categories_htc_title_array = $HTTP_POST_VARS['categories_htc_title_tag'];
                $categories_htc_desc_array = $HTTP_POST_VARS['categories_htc_desc_tag'];
                $categories_htc_keywords_array = $HTTP_POST_VARS['categories_htc_keywords_tag'];
          		$categories_description_array = $HTTP_POST_VARS['categories_description'];

          		$language_id = $languages[$i]['id'];

          		$sql_data_array = array('categories_name' => tep_db_prepare_input($categories_name_array[$language_id]),
                                        'categories_htc_title_tag' => (tep_not_null($categories_htc_title_array[$language_id]) ? tep_db_prepare_input($categories_htc_title_array[$language_id]) : tep_db_prepare_input($categories_name_array[$language_id])),
                                        'categories_htc_desc_tag' => (tep_not_null($categories_htc_desc_array[$language_id]) ? tep_db_prepare_input($categories_htc_desc_array[$language_id]) : tep_db_prepare_input($categories_name_array[$language_id])),
                                        'categories_htc_keywords_tag' => (tep_not_null($categories_htc_keywords_array[$language_id]) ? tep_db_prepare_input($categories_htc_keywords_array[$language_id]) : tep_db_prepare_input($categories_name_array[$language_id])),
                                        'categories_htc_description' => tep_db_prepare_input($categories_description_array[$language_id]));

          		if ($action == 'insert_category') {

            		$insert_sql_data = array('categories_id' => $categories_id,

                    		                 'language_id' => $languages[$i]['id']);

            		$sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            		tep_db_perform('frontend_categories_description', $sql_data_array);

          		} elseif ($action == 'update_category') {

            		tep_db_perform('frontend_categories_description', $sql_data_array, 'update', "categories_id = '" . (int)$categories_id . "' and language_id = '" . (int)$languages[$i]['id'] . "'");

          		}

        	}

        	$categories_image = new upload('categories_image');

        	$categories_image->set_destination(DIR_FS_CATALOG_IMAGES);

        	if ($categories_image->parse() && $categories_image->save()) {

          		tep_db_query("update frontend_categories set categories_image = '" . tep_db_input($categories_image->filename) . "' where categories_id = '" . (int)$categories_id . "'");

        	}

        	

        	$categories_rectangle_image = new upload('categories_rectangle_image');

        	$categories_rectangle_image->set_destination(DIR_FS_CATALOG_IMAGES);

        	if ($categories_rectangle_image->parse() && $categories_rectangle_image->save()) {

          		tep_db_query("update frontend_categories set rectangle_image = '" . tep_db_input($categories_rectangle_image->filename) . "' where categories_id = '" . (int)$categories_id . "'");

        	}

        	

        	$categories_square_image = new upload('categories_square_image');

        	$categories_square_image->set_destination(DIR_FS_CATALOG_IMAGES);

        	if ($categories_square_image->parse() && $categories_square_image->save()) {

          		tep_db_query("update frontend_categories set square_image = '" . tep_db_input($categories_square_image->filename) . "' where categories_id = '" . (int)$categories_id . "'");

        	}

        	

        	tep_redirect(tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&cID=' . $categories_id));

        	break;

		case 'delete_category_confirm':

        	if (isset($HTTP_POST_VARS['categories_id'])) {

          		$categories_id = tep_db_prepare_input($HTTP_POST_VARS['categories_id']);

          		$categories = tep_get_category_tree($categories_id, '', '0', '', true);

          		$products = array();

          		$products_delete = array();

          		for ($i=0, $n=sizeof($categories); $i<$n; $i++) {

            		$product_ids_query = tep_db_query("select products_id from frontend_products_to_categories where categories_id = '" . (int)$categories[$i]['id'] . "'");

		            while ($product_ids = tep_db_fetch_array($product_ids_query)) {

        		      $products[$product_ids['products_id']]['categories'][] = $categories[$i]['id'];

            		}

          		}

          		reset($products);

          		while (list($key, $value) = each($products)) {

            		$category_ids = '';

		            for ($i=0, $n=sizeof($value['categories']); $i<$n; $i++) {

              			$category_ids .= "'" . (int)$value['categories'][$i] . "', ";

            		}

            		$category_ids = substr($category_ids, 0, -2);

            		$check_query = tep_db_query("select count(*) as total from frontend_products_to_categories where products_id = '" . (int)$key . "' and categories_id not in (" . $category_ids . ")");

            		$check = tep_db_fetch_array($check_query);

            		if ($check['total'] < '1') {

              			$products_delete[$key] = $key;

            		}

          		}



				// removing categories can be a lengthy process

          		tep_set_time_limit(0);

          		for ($i=0, $n=sizeof($categories); $i<$n; $i++) {

            		remove_frontend_category($categories[$i]['id']);

          		}

          		reset($products_delete);

        	}

        	tep_redirect(tep_href_link('categories_frontend.php', 'cPath=' . $cPath));

        	break;

		

		case 'move_category_confirm':

        	if (isset($HTTP_POST_VARS['categories_id']) && ($HTTP_POST_VARS['categories_id'] != $HTTP_POST_VARS['move_to_category_id'])) {

          		$categories_id = tep_db_prepare_input($HTTP_POST_VARS['categories_id']);

          		$new_parent_id = tep_db_prepare_input($HTTP_POST_VARS['move_to_category_id']);

          		$path = explode('_', tep_get_generated_category_path_ids($new_parent_id));

          		if (in_array($categories_id, $path)) {

            		$messageStack->add_session(ERROR_CANNOT_MOVE_CATEGORY_TO_PARENT, 'error');

            		tep_redirect(tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&cID=' . $categories_id));

          		} else {

            		tep_db_query("update frontend_categories set parent_id = '" . (int)$new_parent_id . "', last_modified = now() where categories_id = '" . (int)$categories_id . "'");

            		tep_redirect(tep_href_link('categories_frontend.php', 'cPath=' . $new_parent_id . '&cID=' . $categories_id));

          		}

        	}

        	break;

      	case 'move_product_confirm':

        	$products_id = tep_db_prepare_input($HTTP_POST_VARS['products_id']);

        	$new_parent_id = tep_db_prepare_input($HTTP_POST_VARS['move_to_category_id']);

			tep_db_query("update frontend_products_to_categories set categories_id='" . $new_parent_id . "' where products_id='" . $products_id . "'");

        	tep_redirect(tep_href_link('categories_frontend.php', 'cPath=' . $new_parent_id . '&pID=' . $products_id));

        	break;

      	

      case 'unset_product':

      	$products_id = tep_db_prepare_input($HTTP_GET_VARS['pID']);

      	unset_frontend_category_by_product_id($products_id);

      	tep_redirect(tep_href_link('categories_frontend.php', tep_get_all_get_params(array('pID', 'action'))));

      	break;

    }

  }



if($_POST['action'] == "update_frontend_categories" && $_POST['value'] == "true")

  {

	tep_db_query("UPDATE configuration SET configuration_value = 'true' WHERE configuration_key = 'USE_FRONTEND_CATEGORIES'");

  }

elseif($_POST['action'] == "update_frontend_categories" && $_POST['value'] == "false")

  {

	tep_db_query("UPDATE configuration SET configuration_value = 'false' WHERE configuration_key = 'USE_FRONTEND_CATEGORIES'");

  }



// check if the catalog image directory exists

	if (is_dir(DIR_FS_CATALOG_IMAGES)) {

    	if (!is_writeable(DIR_FS_CATALOG_IMAGES)){

    		$messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');

    	} 

  	} else {

    	$messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');

  	}

?>

<!DOCTYPE html public "-//W3C//DTD HTML 4.01 Transitional//EN">

<html <?php echo HTML_PARAMS; ?>>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">

<title><?php echo TITLE; ?></title>

<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">

<script language="javascript" src="includes/general.js"></script>

</head>

<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">

<div id="spiffycalendar" class="text"></div>

<!-- header //-->

<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

<!-- header_eof //-->

<!-- body //-->

<table border="0" width="780px" cellspacing="2" cellpadding="2" align="center" style="margin: 0px auto;">

  <tr>

<!-- body_text //-->

    <td width="100%" valign="top">

	<table border="0" width="100%" cellspacing="0" cellpadding="2">

		<tr>

			<td>

				<table border="0" width="100%" cellspacing="0" cellpadding="0">

          			<tr>

            			<td class="pageHeading"><?php echo 'Frontend Categories w Associated Products'; ?></td>

            			<td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>

            			<td align="right">

							<table border="0" width="100%" cellspacing="0" cellpadding="0">

              					<tr>

                					<td class="smallText2" align="right">

									<?php

    									echo tep_draw_form('search', 'categories_frontend.php', '', 'get');

    									echo 'Search in Frontend Categories' . ' ' . tep_draw_input_field('search');

    									echo '</form>';

									?>

                					</td>

              					</tr>

              					<tr>

                					<td class="smallText2" align="right">

									<?php

    									echo tep_draw_form('goto', 'categories_frontend.php', '', 'get');

    									echo 'Go To' . ' ' . tep_draw_pull_down_menu('cPath', get_frontend_category_tree(), $current_category_id, 'onChange="this.form.submit();"');

    									echo '</form>';

									?>

                					</td>

              					</tr>

            				</table>

						</td>

          			</tr>

                    <tr height="15px">

                      <td colspan="3" width="100%"></td>

                    </tr>

                    <tr valign="top">

					  <td class="smallText" align="left">

					    <?php echo '<a href="'.tep_href_link('frontendcategories_products.php').'"><b>Move Products To Frontend Categories</b></a><br>'; ?>

                      </td>

                      <td class="smallText2" colspan="2" align="right">

                <?php

                $frontend_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'USE_FRONTEND_CATEGORIES'");

				$frontend_result = tep_db_fetch_array($frontend_query);

				$use_frontend_categories = $frontend_result ['configuration_value'];

				if($use_frontend_categories == "true")

				  {

					echo 'Frontend Categories are <b>Enabled</b><br />';

					echo '<form method="post" action="'. $_SERVER['PHP_SELF'] .'"> ' .

						 '  <input type="hidden" name="action" value="update_frontend_categories">' .

						 '  <input type="hidden" name="value" value="false">' .

						 '  <input border="0" type="submit" title=" Turn Off Frontend Categories " alt=" Turn Off Frontend Categories " name=" Turn Off Frontend Categories " value=" Turn Off Frontend Categories ">' . 

						 '</form>';

				  }

				else

				  {

					echo 'Frontend Categories are <b>Disabled</b><br />';

					echo '<form method="post" action="'. $_SERVER['PHP_SELF'] .'"> ' .

						 '  <input type="hidden" name="action" value="update_frontend_categories">' .

						 '  <input type="hidden" name="value" value="true">' .

						 '  <input border="0" type="submit" title=" Turn On Frontend Categories " alt=" Turn On Frontend Categories " name=" Turn On Frontend Categories " value=" Turn On Frontend Categories ">' . 

						 '</form>';

				  }

				?>

                      </td>

                    </tr>

        		</table>

			</td>

      	</tr>

		<tr>

        	<td>

				<table border="0" width="100%" cellspacing="0" cellpadding="0">

          			<tr>

            			<td valign="top">

							<table border="0" width="100%" cellspacing="0" cellpadding="2">

              					<tr class="dataTableHeadingRow">

                					<td class="dataTableHeadingContent"><?php echo 'Frontend Categories / Associated Products'; ?></td>

                					<td class="dataTableHeadingContent" align="center"><?php echo 'Status'; ?></td>

                					<td class="dataTableHeadingContent" align="right"><?php echo 'Action'; ?>&nbsp;</td>

              					</tr>

								<?php

    								$categories_count = 0;

    								$rows = 0;

    								if (isset($HTTP_GET_VARS['search'])) {

      									$search = tep_db_prepare_input($HTTP_GET_VARS['search']);

      									$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status, c.rectangle_image, c.square_image,cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_htc_description from frontend_categories c, frontend_categories_description cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and cd.categories_name like '%" . tep_db_input($search) . "%' order by c.sort_order, cd.categories_name");

    								} else {

										$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status, c.rectangle_image, c.square_image,cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_htc_description from frontend_categories c, frontend_categories_description cd where c.parent_id = '" . (int)$current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by c.sort_order, cd.categories_name");

    								}

    								while ($categories = tep_db_fetch_array($categories_query)) {

      									$categories_count++;

      									$rows++;

										// Get parent_id for subcategories if search

      									if (isset($HTTP_GET_VARS['search'])){

      										$cPath= $categories['parent_id'];

      									}

      									//if ((!isset($HTTP_GET_VARS['cID']) && !isset($HTTP_GET_VARS['pID']) || (isset($HTTP_GET_VARS['cID']) && ($HTTP_GET_VARS['cID'] == $categories['categories_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {

      									if ((!isset($HTTP_GET_VARS['cID']) && !isset($HTTP_GET_VARS['pID']) || (isset($HTTP_GET_VARS['cID']) && ($HTTP_GET_VARS['cID'] == $categories['categories_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {

        									$category_childs = array('childs_count' => childs_in_frontend_category_count($categories['categories_id']));

        									$category_products = array('products_count' => products_in_frontend_category_count($categories['categories_id']));

        									$cInfo_array = array_merge($categories, $category_childs, $category_products);

        									$cInfo = new objectInfo($cInfo_array);

      									}



      									if (isset($cInfo) && is_object($cInfo) && ($categories['categories_id'] == $cInfo->categories_id) ) {

        									echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link('categories_frontend.php', get_frontend_path($categories['categories_id'])) . '\'">' . "\n";

      									} else {

        									echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '\'">' . "\n";

      									}

								?>

                					<td class="dataTableContent">

										<?php 

											echo '<a href="' . tep_href_link('categories_frontend.php', get_frontend_path($categories['categories_id'])) . '">' . tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a>&nbsp;<b>' . $categories['categories_name'] . '</b>'; 

										?>

									</td>

                					<td class="dataTableContent" align="center">

									<?php

	      								if ($categories['categories_status'] == '1') {

	        								echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link('categories_frontend.php', 'action=setflagc&flag=0&cID=' . $categories['categories_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';

	      								} else {

	        								echo '<a href="' . tep_href_link('categories_frontend.php', 'action=setflagc&flag=1&cID=' . $categories['categories_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);

	      								}

									?>

									</td>

                					<td class="dataTableContent" align="right">

									<?php 

										if (isset($cInfo) && is_object($cInfo) && ($categories['categories_id'] == $cInfo->categories_id) ) { 

											echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); 

										} else { 

											echo '<a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 

										} 

									?>&nbsp;

									</td>

              					</tr>

								<?php

    								}

    							$products_count = 0;

    							if (isset($HTTP_GET_VARS['search'])) {

      								$products_query = tep_db_query("select p.products_id, pd.products_name, p.products_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p2c.categories_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, frontend_products_to_categories p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and pd.products_name like '%" . tep_db_input($search) . "%' order by pd.products_name");

    							} else {

      								$products_query = tep_db_query("select p.products_id, pd.products_name, p.products_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, frontend_products_to_categories p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$current_category_id . "' order by pd.products_name");

    							}

    							while ($products = tep_db_fetch_array($products_query)) {

      								$products_count++;

      								$rows++;

									// Get categories_id for product if search

      								if (isset($HTTP_GET_VARS['search'])){

      									$cPath = $products['categories_id'];

      								}

      								if ( (!isset($HTTP_GET_VARS['pID']) && !isset($HTTP_GET_VARS['cID']) || (isset($HTTP_GET_VARS['pID']) && ($HTTP_GET_VARS['pID'] == $products['products_id']))) && !isset($pInfo) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {

										// find out the rating average from customer reviews

        								$reviews_query = tep_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . (int)$products['products_id'] . "'");

        								$reviews = tep_db_fetch_array($reviews_query);

        								$pInfo_array = array_merge($products, $reviews);

        								$pInfo = new objectInfo($pInfo_array);

      								}

      								if (isset($pInfo) && is_object($pInfo) && ($products['products_id'] == $pInfo->products_id) ) {

        								echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only') . '\'">' . "\n";

      								} else {

        								echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&pID=' . $products['products_id']) . '\'">' . "\n";

      								}

									?>

                				<td class="dataTableContent">

								<?php 

									echo '<a href="' . tep_href_link('categories.php', 'cPath=' . get_cpath_by_products_id($products['products_id']) . '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $products['products_name']; 

								?>

								</td>

                				<td class="dataTableContent" align="center">

								<?php

      								if ($products['products_status'] == '1') {

        								echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link('categories_frontend.php', 'action=setflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';

      								} else {

        								echo '<a href="' . tep_href_link('categories_frontend.php', 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);

      								}

								?>

								</td>

                				<td class="dataTableContent" align="right">

								<?php 

									if (isset($pInfo) && is_object($pInfo) && ($products['products_id'] == $pInfo->products_id)) { 

										echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); 

									} else { 

										echo '<a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&pID=' . $products['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 

									} 

								?>&nbsp;

								</td>

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

              				<tr>

                				<td colspan="3">

									<table border="0" width="100%" cellspacing="0" cellpadding="2">

                  						<tr>

                    						<td class="smallText2">

											<?php 

												echo TEXT_CATEGORIES . '&nbsp;' . $categories_count . '<br>' . TEXT_PRODUCTS . '&nbsp;' . $products_count; 

											?>

											</td>

                    						<td align="right" class="smallText">

											<?php 

												if (sizeof($cPath_array) > 0)

													echo '<a href="' . tep_href_link('categories_frontend.php', $cPath_back . 'cID=' . $current_category_id) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>&nbsp;'; 

													if (!isset($HTTP_GET_VARS['search']))														

														echo '<a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&action=new_category') . '">' . tep_image_button('button_new_category.gif', IMAGE_NEW_CATEGORY) . '</a>';

														//echo '&nbsp;<a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&action=new_product') . '">' . tep_image_button('button_new_product.gif', IMAGE_NEW_PRODUCT) . '</a>';

											?>&nbsp;

											</td>

                  						</tr>

                					</table>

								</td>

              				</tr>

            			</table>

					</td>

					<?php

    					$heading = array();

    					$contents = array();

    					switch ($action) {

      						case 'new_category':

        						$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_CATEGORY . '</b>');

        						$contents = array('form' => tep_draw_form('newcategory', 'categories_frontend.php', 'action=insert_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"'));

        						$contents[] = array('text' => TEXT_NEW_CATEGORY_INTRO);

        						$category_inputs_string = '';

        						$languages = tep_get_languages();

        						for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {

          							$category_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_name[' . $languages[$i]['id'] . ']');

          							//echo tep_draw_textarea_field('products_description[' . $languages[$i]['id'] . ']', 'soft', '70', '15', (isset($products_description[$languages[$i]['id']]) ? stripslashes($products_description[$languages[$i]['id']]) : tep_get_products_description($pInfo->products_id, $languages[$i]['id'])));

									$category_inputs_string .= '<br>' . tep_draw_textarea_field('categories_description[' . $languages[$i]['id'] . ']', 'soft', '45', '5');
                                    
                                    $category_htc_title_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_htc_title_tag[' . $languages[$i]['id'] . ']');
                                   
                                    $category_htc_desc_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .'/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_htc_desc_tag[' . $languages[$i]['id'] . ']');

                                    $category_htc_keywords_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_htc_keywords_tag[' . $languages[$i]['id'] . ']');

        						}

        						$contents[] = array('text' => '<br>' . 'Category Name/Description' . $category_inputs_string);

        						$contents[] = array('text' => '<br>' . 'Thumbnail' . '<br>' . tep_draw_file_field('categories_image'));

        						$contents[] = array('text' => '<br>' . 'Rectangle Image' . '<br>' . tep_draw_file_field('categories_rectangle_image'));

        						$contents[] = array('text' => '<br>' . 'Square Image' . '<br>' . tep_draw_file_field('categories_square_image'));

        						$contents[] = array('text' => '<br>' . TEXT_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', '', 'size="2"'));
                                
                                $contents[] = array('text' => '<br>' . 'Header Tags Category Title' . $category_htc_title_string);

                                $contents[] = array('text' => '<br>' . 'Header Tags Category Description' . $category_htc_desc_string);

                                $contents[] = array('text' => '<br>' . 'Header Tags Category Keywords' . $category_htc_keywords_string);

        						$contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');

        						break;

      						case 'edit_category':

        						$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_CATEGORY . '</b>');

        						$contents = array('form' => tep_draw_form('categories', 'categories_frontend.php', 'action=update_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"') . tep_draw_hidden_field('categories_id', $cInfo->categories_id));

        						$contents[] = array('text' => TEXT_EDIT_INTRO);

        						$category_inputs_string = '';

        						$languages = tep_get_languages();

        						for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {

          							$category_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', get_frontend_category_name($cInfo->categories_id, $languages[$i]['id']));

									$category_inputs_string .= '<br>' . tep_draw_textarea_field('categories_description[' . $languages[$i]['id'] . ']', 'soft', '45', '5', get_frontend_category_desc($cInfo->categories_id, $languages[$i]['id']));  
                                    $category_htc_title_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_htc_title_tag[' . $languages[$i]['id'] . ']',tep_get_frontend_category_htc_title($cInfo->categories_id, $languages[$i]['id']));
                                   
                                    $category_htc_desc_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .'/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_htc_desc_tag[' . $languages[$i]['id'] . ']',tep_get_frontend_category_htc_desc($cInfo->categories_id, $languages[$i]['id']));

                                    $category_htc_keywords_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_htc_keywords_tag[' . $languages[$i]['id'] . ']',tep_get_frontend_category_htc_keywords($cInfo->categories_id, $languages[$i]['id']));        							

        						}

        						$contents[] = array('text' => '<br>' . 'Category Name/Description' . $category_inputs_string);

        						$contents[] = array('text' => '<br>' . tep_image(DIR_WS_CATALOG_IMAGES . $cInfo->categories_image, $cInfo->categories_name) . '<br>' . DIR_WS_CATALOG_IMAGES . '<br><b>' . $cInfo->categories_image . '</b>');

        						$contents[] = array('text' => '<br>' . 'Thumbnail' . '<br>' . tep_draw_file_field('categories_image'));

								$contents[] = array('text' => '<br>' . tep_image(DIR_WS_CATALOG_IMAGES . $cInfo->rectangle_image, $cInfo->categories_name) . '<br>' . DIR_WS_CATALOG_IMAGES . '<br><b>' . $cInfo->rectangle_image . '</b>');        						

        						$contents[] = array('text' => '<br>' . 'Rectangle Image' . '<br>' . tep_draw_file_field('categories_rectangle_image'));

        						$contents[] = array('text' => '<br>' . tep_image(DIR_WS_CATALOG_IMAGES . $cInfo->square_image, $cInfo->categories_name) . '<br>' . DIR_WS_CATALOG_IMAGES . '<br><b>' . $cInfo->square_image . '</b>');

        						$contents[] = array('text' => '<br>' . 'Square Image' . '<br>' . tep_draw_file_field('categories_square_image'));

        						$contents[] = array('text' => '<br>' . TEXT_EDIT_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"'));
                                
                                 $contents[] = array('text' => '<br>' . 'Header Tags Category Title' . $category_htc_title_string);

                                $contents[] = array('text' => '<br>' . 'Header Tags Category Description' . $category_htc_desc_string);

                                $contents[] = array('text' => '<br>' . 'Header Tags Category Keywords' . $category_htc_keywords_string);

        						$contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');

        						break;

      case 'delete_category':

        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CATEGORY . '</b>');



        $contents = array('form' => tep_draw_form('categories', 'categories_frontend.php', 'action=delete_category_confirm&cPath=' . $cPath) . tep_draw_hidden_field('categories_id', $cInfo->categories_id));

        $contents[] = array('text' => TEXT_DELETE_CATEGORY_INTRO);

        $contents[] = array('text' => '<br><b>' . $cInfo->categories_name . '</b>');

        if ($cInfo->childs_count > 0) $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_CHILDS, $cInfo->childs_count));

        if ($cInfo->products_count > 0) $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $cInfo->products_count));

        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');

        break;

      case 'move_category':

        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_CATEGORY . '</b>');



        $contents = array('form' => tep_draw_form('categories', 'categories_frontend.php', 'action=move_category_confirm&cPath=' . $cPath) . tep_draw_hidden_field('categories_id', $cInfo->categories_id));

        $contents[] = array('text' => sprintf(TEXT_MOVE_CATEGORIES_INTRO, $cInfo->categories_name));

        $contents[] = array('text' => '<br>' . sprintf(TEXT_MOVE, $cInfo->categories_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', get_frontend_category_tree(), $current_category_id));

        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');

        break;

     

      case 'move_product':

      	

        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_PRODUCT . '</b>');



        $contents = array('form' => tep_draw_form('products', 'categories_frontend.php', 'action=move_product_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id));

        $contents[] = array('text' => sprintf(TEXT_MOVE_PRODUCTS_INTRO, $pInfo->products_name));

        $contents[] = array('text' => '<br>' . 'Current frontend category:' . '<br><b>' . get_output_generated_category_path($pInfo->products_id, 'product') . '</b>');

        $contents[] = array('text' => '<br>' . sprintf(TEXT_MOVE, $pInfo->products_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', get_frontend_category_tree(), $current_category_id));

        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');

        

		break;

      default:

        if ($rows > 0) {

          if (isset($cInfo) && is_object($cInfo)) { // category info box contents

            $category_path_string = '';

            $category_path = generate_frontend_category_path($cInfo->categories_id);

            for ($i=(sizeof($category_path[0])-1); $i>0; $i--) {

              $category_path_string .= $category_path[0][$i]['id'] . '_';

            }

			

			//echo '<pre>';

				//print_r($category_path);

			//echo '</pre>';

            $category_path_string = substr($category_path_string, 0, -1);



            $heading[] = array('text' => '<b>' . $cInfo->categories_name . '</b>');



            $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $category_path_string . '&cID=' . $cInfo->categories_id . '&action=edit_category') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $HTTP_GET_VARS['cPath'] . '&cID=' . $cInfo->categories_id . '&action=delete_category') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a> <a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $category_path_string . '&cID=' . $cInfo->categories_id . '&action=move_category') . '">' . tep_image_button('button_move.gif', IMAGE_MOVE) . '</a>');

            $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . tep_date_short($cInfo->date_added));

            if (tep_not_null($cInfo->last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($cInfo->last_modified));

            $contents[] = array('text' => '<br>' . tep_info_image($cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT) . '<br>' . $cInfo->categories_image);

            $contents[] = array('text' => '<br>' . TEXT_SUBCATEGORIES . ' ' . $cInfo->childs_count . '<br>' . TEXT_PRODUCTS . ' ' . $cInfo->products_count);

          } elseif (isset($pInfo) && is_object($pInfo)) { // product info box contents

            $heading[] = array('text' => '<b>' . tep_get_products_name($pInfo->products_id, $languages_id) . '</b>');



            $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link('categories.php', 'cPath=' . get_cpath_by_products_id($pInfo->products_id) . '&pID=' . $pInfo->products_id . '&action=new_product') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> ' 

			//. '<a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=delete_product') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>' . 

			. '<a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=move_product') . '">' . tep_image_button('button_move.gif', IMAGE_MOVE) . '</a>'  

			//. '<a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=copy_to') . '">' . tep_image_button('button_copy_to.gif', IMAGE_COPY_TO) . '</a>'

			. '<a href="' . tep_href_link('categories_frontend.php', 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=unset_product') . '">' . tep_image_button('button_unset.gif', 'Unset') . '</a>'

			);

			

            $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . tep_date_short($pInfo->products_date_added));

            if (tep_not_null($pInfo->products_last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($pInfo->products_last_modified));

            if (date('Y-m-d') < $pInfo->products_date_available) $contents[] = array('text' => TEXT_DATE_AVAILABLE . ' ' . tep_date_short($pInfo->products_date_available));

            $contents[] = array('text' => '<br>' . tep_info_image($pInfo->products_image, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '<br>' . $pInfo->products_image);

            $contents[] = array('text' => '<br>' . TEXT_PRODUCTS_PRICE_INFO . ' ' . $currencies->format($pInfo->products_price) . '<br>' . TEXT_PRODUCTS_QUANTITY_INFO . ' ' . $pInfo->products_quantity);

            $contents[] = array('text' => '<br>' . TEXT_PRODUCTS_AVERAGE_RATING . ' ' . number_format($pInfo->average_rating, 2) . '%');

          }

        } else { // create category/product info

          $heading[] = array('text' => '<b>' . EMPTY_CATEGORY . '</b>');



          $contents[] = array('text' => 'Please insert a new frontend category in this level.');

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

