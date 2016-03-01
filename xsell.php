<?php
/* $Id$
osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com
Copyright (c) 2002 osCommerce

Released under the GNU General Public License
xsell.php
Original Idea From Isaac Mualem im@imwebdesigning.com <mailto:im@imwebdesigning.com>
Complete Recoding From Stephen Walker admin@snjcomputers.com
*/

  require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'currencies.php');
   $currencies = new currencies();
  switch($_GET['action']){
  	  case 'delete':
  	  	$master_product_id = (int)$HTTP_GET_VARS['add_related_product_ID'];
  	  	tep_db_query('delete from ' . TABLE_PRODUCTS_XSELL . ' where products_id = "'. $master_product_id .'"');
  	  	header('Location: ' . FILENAME_XSELL_PRODUCTS . '?' . tep_get_all_get_params(array('add_related_product_ID', 'action')));
  	  	break;
	  case 'update_cross' :
	  	$master_product_id = (int)$HTTP_POST_VARS['products_id'];
	  	$related_products = explode('&join;', $HTTP_POST_VARS['related_products_id']);
	  	tep_db_query('delete from ' . TABLE_PRODUCTS_XSELL . ' where products_id = "'. $master_product_id .'"');
	  	
	  	if (count($related_products)){
		  	foreach($related_products as $prod){
	  		$prod_info = explode('|', $prod);
	  		if($prod_info[1]>0){
				$insert_array = array();
				$insert_array = array('products_id' => $master_product_id,
					                  'xsell_id' => $prod_info[1],
					                  'sort_order' => $prod_info[0]);
            	tep_db_perform(TABLE_PRODUCTS_XSELL, $insert_array);
				}
			}
		}	
	  	header('Location: ' . FILENAME_XSELL_PRODUCTS . '?' . tep_get_all_get_params(array('add_related_product_ID', 'action')));
		/*if ($_POST['product']){
	    foreach ($_POST['product'] as $temp_prod){
          tep_db_query('delete from ' . TABLE_PRODUCTS_XSELL . ' where xsell_id = "'.$temp_prod.'" and products_id = "'.$_GET['add_related_product_ID'].'"');
	    }
	  }

		$sort_start_query = tep_db_query('select sort_order from ' . TABLE_PRODUCTS_XSELL . ' where products_id = "'.$_GET['add_related_product_ID'].'" order by sort_order desc limit 1');
        $sort_start = tep_db_fetch_array($sort_start_query);

	    $sort = (($sort_start['sort_order'] > 0) ? $sort_start['sort_order'] : '0');
		if ($_POST['cross']){
        foreach ($_POST['cross'] as $temp){
			$sort++;
			$insert_array = array();
			$insert_array = array('products_id' => $_GET['add_related_product_ID'],
				                  'xsell_id' => $temp,
				                  'sort_order' => $sort);
              tep_db_perform(TABLE_PRODUCTS_XSELL, $insert_array);
		}
		}*/
        $messageStack->add(CROSS_SELL_SUCCESS, 'success');
	   break;
	  /*case 'update_sort' :
        foreach ($_POST as $key_a => $value_a){
         tep_db_query('update ' . TABLE_PRODUCTS_XSELL . ' set sort_order = "' . $value_a . '" where xsell_id = "' . $key_a . '"');
	    }
        $messageStack->add(SORT_CROSS_SELL_SUCCESS, 'success');
	   break;*/
  }
  
function get_category_id($prod_id){
	$sql = tep_db_query("select a.categories_id as cat_id, " .
							  " c.parent_id as cat_parent_id, " .
							  " d.categories_name as cat_name " .
							  " from " . TABLE_PRODUCTS_TO_CATEGORIES . " a " .
							  " inner join " . TABLE_PRODUCTS . " b on a.products_id=b.products_id " .
							  " inner join " . TABLE_CATEGORIES . " c on a.categories_id=c.categories_id " .
							  " inner join " . TABLE_CATEGORIES_DESCRIPTION . " d on a.categories_id=d.categories_id " .
							  " where d.language_id='1' " . 
							  " and a.products_id='" . (int)$prod_id . "'");
	$sql_info = tep_db_fetch_array($sql);
	return $sql_info; 
}

function get_separator(){
	return ' &gt;&gt; ';
}

function get_category_levels($cat_id){
	$resp = '';
	if (!empty($cat_id)){
		$sql = tep_db_query("select a.parent_id as cat_parent_id, " .
							" b.categories_name as cat_name " .
							" from " . TABLE_CATEGORIES . " a " .
							" inner join " . TABLE_CATEGORIES_DESCRIPTION . " b on a.categories_id=b.categories_id " .
							" where b.language_id='1' and a.categories_id='" . (int)$cat_id . "'");
		$sql_info = tep_db_fetch_array($sql);
		$resp = get_category_levels($sql_info['cat_parent_id']) . get_separator(). $sql_info['cat_name'];
	}
	return $resp; 
}

function generateJsRowFillCall($prodID){
	$resp = '';
	$sql = tep_db_query("select a.xsell_id as prodID, b.products_name as prodName " .
						" from " . TABLE_PRODUCTS_XSELL . " a " .
						" inner join " . TABLE_PRODUCTS_DESCRIPTION . " b on a.xsell_id=b.products_id " .
						" where b.language_id='1' and a.products_id='" . (int)$prodID . "' " .
						" order by a.sort_order, b.products_name");
	while($row = tep_db_fetch_array($sql)){
		$resp .= 'appendRow(getTableReference(), \'' . $row['prodName'] . '\', \'' . $row['prodID'] . '\');';
	}
	return $resp;
}
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
      
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
<!-- body_text //-->
  
<?php
  if ($_GET['add_related_product_ID'] == ''){
?>
  <table class="table table-bordered table-hover">
   <tr>
    <td><?php echo TABLE_HEADING_PRODUCT_ID;?></td>
    <td><?php echo TABLE_HEADING_PRODUCT_MODEL;?></td>
    <td><?php echo TABLE_HEADING_PRODUCT_NAME;?></td>
    <td><?php echo TABLE_HEADING_CURRENT_SELLS;?></td>
    <td colspan="2"><?php echo TABLE_HEADING_UPDATE_SELLS;?></td>
   </tr>
<?php
    $products_query_raw = 'select p.products_id, p.products_model, pd.products_name, p.products_id from '.TABLE_PRODUCTS.' p, '.TABLE_PRODUCTS_DESCRIPTION.' pd where p.products_id = pd.products_id and pd.language_id = "'.(int)$languages_id.'" and p.products_id in (select distinct(a.products_id) from ' . TABLE_PRODUCTS_XSELL . ' a) order by p.products_id asc';
    $products_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_query_raw, $products_query_numrows);
    $products_query = tep_db_query($products_query_raw);
    while ($products = tep_db_fetch_array($products_query)) {
?>
   <!--<tr onMouseOver="cOn(this); this.style.cursor='pointer'; this.style.cursor='hand';" onMouseOut="cOut(this);" bgcolor='#DFE4F4' onClick=document.location.href="<?php //echo tep_href_link(FILENAME_XSELL_PRODUCTS, 'add_related_product_ID=' . $products['products_id'], 'NONSSL');?>">-->
	<tr onMouseOver="cOn(this);" onMouseOut="cOut(this);" bgcolor='#DFE4F4'>   
    <td>&nbsp;<?php echo $products['products_id'];?>&nbsp;</td>
    <td>&nbsp;<?php echo $products['products_model'];?>&nbsp;</td>
    <td>&nbsp;<?php echo $products['products_name'];?>&nbsp;</td>
    <td><table class="table table-bordered table-hover">
<?php
    $products_cross_query = tep_db_query('select p.products_id, p.products_model, pd.products_name, p.products_id, x.products_id, x.xsell_id, x.sort_order, x.ID from '.TABLE_PRODUCTS.' p, '.TABLE_PRODUCTS_DESCRIPTION.' pd, '.TABLE_PRODUCTS_XSELL.' x where x.xsell_id = p.products_id and x.products_id = "'.$products['products_id'].'" and p.products_id = pd.products_id and pd.language_id = "'.(int)$languages_id.'" order by x.sort_order asc');
	$i=0;
    while ($products_cross = tep_db_fetch_array($products_cross_query)){
		$i++;
?>
	 <tr>
	  <td>&nbsp;<?php echo $i . '.&nbsp;&nbsp;<b>' . $products_cross['products_model'] . '</b>&nbsp;' . $products_cross['products_name'];?>&nbsp;</td>
	 </tr>
<?php
	}
    if ($i <= 0){
?>
	 <tr>
	  <td>&nbsp;--&nbsp;</td>
	 </tr>
<?php
	}else{
?>
	 <tr>
	  <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10');?></td>
	 </tr>
<?php
}
?>
    </table></td>
    <td>&nbsp;<a href="<?php echo tep_href_link(FILENAME_XSELL_PRODUCTS, tep_get_all_get_params(array('action')) . 'add_related_product_ID=' . $products['products_id'], 'NONSSL');?>"><?php echo tep_image_button('button_edit.gif', IMAGE_EDIT);?></a>&nbsp;</td>
    <td align="center">&nbsp;<?php echo (($i > 0) ? '<a href="' . tep_href_link(FILENAME_XSELL_PRODUCTS, tep_get_all_get_params(array('action')) . 'action=delete&add_related_product_ID=' . $products['products_id'], 'NONSSL') .'" onclick="javascript:return confirm(\'This action will release related product(s) association !!\');">'.tep_image_button('button_delete.gif', IMAGE_DELETE).'</a>&nbsp;' : '--')?></td>
   </tr>
<?php
	}
?>
   <tr>
    <td colspan="6"><table class="table table-bordered table-hover">
     <tr>
      <td><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
      <td align="right"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID', 'action'))); ?></td>
     </tr>
    </table></td>
   </tr>
  </table>
  <a href="<?php echo tep_href_link(FILENAME_XSELL_PRODUCTS, tep_get_all_get_params(array('action')) . 'add_related_product_ID=0', 'NONSSL');?>">Associate Related Products</a>
<?php
}elseif($_GET['add_related_product_ID'] != ''){
	echo tep_draw_form('update_cross', FILENAME_XSELL_PRODUCTS, tep_get_all_get_params(array('action')) . 'action=update_cross', 'post');
	echo tep_draw_hidden_field('products_id', (!empty($_GET['add_related_product_ID']) ? $_GET['add_related_product_ID'] : ''));
	echo tep_draw_hidden_field('related_products_id');	 
?>
	<table class="table table-bordered table-hover">
<?php
	if (!empty($_GET['add_related_product_ID'])){
		$master_product_id = (int)$_GET['add_related_product_ID'];
		$products_name_query = tep_db_query('select pd.products_name, p.products_model, p.products_image from '.TABLE_PRODUCTS.' p, '.TABLE_PRODUCTS_DESCRIPTION.' pd where p.products_id = "'.$master_product_id.'" and p.products_id = pd.products_id and pd.language_id ="'.(int)$languages_id.'"');
		$products_name = tep_db_fetch_array($products_name_query);
		$cat_info = get_category_id($master_product_id);
		echo '<tr><td colspan="3">';
		echo '<div id="div_listing">' . get_category_levels($cat_info['cat_id']) . get_separator() . $products_name['products_name'] . '</div>';
		echo '</td></tr>'; 
	}else{
		$master_product_id = 0;
		echo '<tr><td valign="top">Select Main Product</td><td valign="top">&nbsp;:&nbsp;</td><td>';
		echo '<div id="div_listing"></div><span id="span_loader" style="display:none;">' . tep_image('images/ajax-loader.gif') . '</span>';
		echo '</td></tr>';
	}	
?>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td>Append Related Product(s)</td>
		<td>&nbsp;:&nbsp;</td>
		<td>
			<div id="div_related" addbuttonid="btn_add"></div><span id="span_loader1" style="display:none;"><?php echo tep_image('images/ajax-loader.gif'); ?></span>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>
			<input id="btn_add" type="button" value="Append to List" style="display:none" onClick="javascript:addToList();">
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3"><b>Product(s) related so far</b></td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td>
            <table class="table table-bordered table-hover" id="tab_products"></table>
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td colspan="3" align="right">
			<?php echo tep_image_submit('button_update_b.gif', '', ' onclick="javascript:return saveSelection();" ') . '&nbsp;&nbsp;<a href="'.tep_href_link(FILENAME_XSELL_PRODUCTS, 'men_id=catalog').'">' . tep_image_button('button_cancel_b.gif') . '</a>';?>
		</td>
	</tr>	
	<tr><td colspan="3">&nbsp;</td></tr>
	</table>
	</form>
  <!--<table border="0" cellspacing="0" cellpadding="0" bgcolor="#999999" align="center">
   <tr>
    <td><?php //echo tep_draw_form('update_cross', FILENAME_XSELL_PRODUCTS, tep_get_all_get_params(array('action')) . 'action=update_cross', 'post');?><table cellpadding="1" cellspacing="1" border="0">
	 <tr>
	  <td colspan="6"><table cellpadding="3" cellspacing="0" border="0" width="100%">
	   <tr class="dataTableHeadingRow">
	    <td valign="top" align="center" colspan="2"><span class="pageHeading"><?php //echo TEXT_SETTING_SELLS.$products_name['products_name'].' ('.TEXT_MODEL.': '.$products_name['products_model'].') ('.TEXT_PRODUCT_ID.': '.$_GET['add_related_product_ID'].')';?></span></td>
	   </tr>
	   <tr class="dataTableHeadingRow">
	    <td align="right"><?php //echo tep_image('../images/'.$products_name['products_image']);?></td>
	    <td align="right" valign="bottom"><?php //echo tep_image_submit('button_update.gif') . '<br><br><a href="'.tep_href_link(FILENAME_XSELL_PRODUCTS, 'men_id=catalog').'">' . tep_image_button('button_cancel.gif') . '</a>';?></td>
	   </tr>
	  </table></td>
	 </tr>
     <tr class="dataTableHeadingRow">
      <td class="dataTableHeadingContent" width="75">&nbsp;<?php //echo TABLE_HEADING_PRODUCT_ID;?>&nbsp;</td>
      <td class="dataTableHeadingContent">&nbsp;<?php //echo TABLE_HEADING_PRODUCT_MODEL;?>&nbsp;</td>
	  <td class="dataTableHeadingContent">&nbsp;<?php //echo TABLE_HEADING_PRODUCT_IMAGE;?>&nbsp;</td>
	  <td class="dataTableHeadingContent">&nbsp;<?php //echo TABLE_HEADING_CROSS_SELL_THIS;?>&nbsp;</td>
      <td class="dataTableHeadingContent">&nbsp;<?php //echo TABLE_HEADING_PRODUCT_NAME;?>&nbsp;</td>
	  <td class="dataTableHeadingContent">&nbsp;<?php //echo TABLE_HEADING_PRODUCT_PRICE;?>&nbsp;</td>
	 </tr>
<?php
    //$products_query_raw = 'select p.products_id, p.products_model, p.products_image, p.products_price, pd.products_name, p.products_id from '.TABLE_PRODUCTS.' p, '.TABLE_PRODUCTS_DESCRIPTION.' pd where p.products_id = pd.products_id and pd.language_id = "'.(int)$languages_id.'" order by p.products_id asc';
    //$products_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_query_raw, $products_query_numrows);
    //$products_query = tep_db_query($products_query_raw);
    //while ($products = tep_db_fetch_array($products_query)) {
		//$xsold_query = tep_db_query('select * from '.TABLE_PRODUCTS_XSELL.' where products_id = "'.$_GET['add_related_product_ID'].'" and xsell_id = "'.$products['products_id'].'"');
?>
	 <tr bgcolor='#DFE4F4'>
	  <td class="dataTableContent" align="center">&nbsp;<?php //echo $products['products_id'];?>&nbsp;</td>
	  <td class="dataTableContent" align="center">&nbsp;<?php //echo $products['products_model'];?>&nbsp;</td>
	  <td class="dataTableContent" align="center">&nbsp;<?php //echo ((is_file('../images/'.$products['products_image'])) ?  tep_image('../images/'.$products['products_image'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) : '<br>No Image<br>');?>&nbsp;</td>
	  <td class="dataTableContent">&nbsp;<?php //echo tep_draw_hidden_field('product[]', $products['products_id']) . tep_draw_checkbox_field('cross[]', $products['products_id'], ((tep_db_num_rows($xsold_query) > 0) ? true : false), '', ' onMouseOver="this.style.cursor=\'hand\'"');?>&nbsp;<label onMouseOver="this.style.cursor='hand'"><?php echo TEXT_CROSS_SELL;?></label>&nbsp;</td>
	  <td class="dataTableContent">&nbsp;<?php //echo $products['products_name'];?>&nbsp;</td>
	  <td class="dataTableContent">&nbsp;<?php //echo $currencies->format($products['products_price']);?>&nbsp;</td>
	 </tr>
<?php
    //}
?>
	</table></form></td>
   </tr>
   <tr>
    <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxContent">
     <tr>
      <td><?php //echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
      <td><?php //echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID', 'action'))); ?></td>
     </tr>
    </table></td>
   </tr>
  </table>-->
<?php
}
//elseif($_GET['add_related_product_ID'] != '' && $_GET['sort'] != ''){
	//$products_name_query = tep_db_query('select pd.products_name, p.products_model, p.products_image from '.TABLE_PRODUCTS.' p, '.TABLE_PRODUCTS_DESCRIPTION.' pd where p.products_id = "'.$_GET['add_related_product_ID'].'" and p.products_id = pd.products_id and pd.language_id ="'.(int)$languages_id.'"');
	//$products_name = tep_db_fetch_array($products_name_query);
?>
  <!--<table border="0" cellspacing="0" cellpadding="0" bgcolor="#999999" align="center">
   <tr>
    <td><?php //echo tep_draw_form('update_sort', FILENAME_XSELL_PRODUCTS, tep_get_all_get_params(array('action')) . 'action=update_sort', 'post');?><table cellpadding="1" cellspacing="1" border="0">
	 <tr>
	  <td colspan="6"><table cellpadding="3" cellspacing="0" border="0" width="100%">
	   <tr class="dataTableHeadingRow">
	    <td valign="top" align="center" colspan="2"><span class="pageHeading"><?php //echo TEXT_SETTING_SELLS.': '.$products_name['products_name'].' ('.TEXT_MODEL.': '.$products_name['products_model'].') ('.TEXT_PRODUCT_ID.': '.$_GET['add_related_product_ID'].')';?></span></td>
	   </tr>
	   <tr class="dataTableHeadingRow">
	    <td align="right"><?php //echo tep_image('../images/'.$products_name['products_image']);?></td>
	    <td align="right" valign="bottom"><?php //echo tep_image_submit('button_update.gif') . '<br><br><a href="'.tep_href_link(FILENAME_XSELL_PRODUCTS, 'men_id=catalog').'">' . tep_image_button('button_cancel.gif') . '</a>';?></td>
	   </tr>
	  </table></td>
	 </tr>
     <tr class="dataTableHeadingRow">
	  <td class="dataTableHeadingContent">&nbsp;<?php //echo TABLE_HEADING_PRODUCT_ID;?>&nbsp;</td>
	  <td class="dataTableHeadingContent">&nbsp;<?php //echo TABLE_HEADING_PRODUCT_MODEL;?>&nbsp;</td>
	  <td class="dataTableHeadingContent">&nbsp;<?php //echo TABLE_HEADING_PRODUCT_IMAGE;?>&nbsp;</td>
	  <td class="dataTableHeadingContent" align="center">&nbsp;<?php //echo TABLE_HEADING_PRODUCT_NAME;?>&nbsp;</td>
	  <td class="dataTableHeadingContent">&nbsp;<?php //echo TABLE_HEADING_PRODUCT_PRICE;?>&nbsp;</td>
	  <td class="dataTableHeadingContent">&nbsp;<?php //echo //TABLE_HEADING_PRODUCT_SORT;?>&nbsp;</td>
	 </tr>
<?php
    //$products_query_raw = 'select p.products_id as products_id, p.products_price, p.products_image, p.products_model, pd.products_name, p.products_id, x.products_id as xproducts_id, x.xsell_id, x.sort_order, x.ID from '.TABLE_PRODUCTS.' p, '.TABLE_PRODUCTS_DESCRIPTION.' pd, '.TABLE_PRODUCTS_XSELL.' x where x.xsell_id = p.products_id and x.products_id = "'.$_GET['add_related_product_ID'].'" and p.products_id = pd.products_id and pd.language_id = "'.(int)$languages_id.'" order by x.sort_order asc';
    //$products_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_query_raw, $products_query_numrows);
	//$sort_order_drop_array = array();
	//for($i=1;$i<=$products_query_numrows;$i++){
	//$sort_order_drop_array[] = array('id' => $i, 'text' => $i);
	//}
    //$products_query = tep_db_query($products_query_raw);
 //while ($products = tep_db_fetch_array($products_query)){
?>
	 <tr bgcolor='#DFE4F4'>
	  <td class="dataTableContent" align="center">&nbsp;<?php //echo $products['products_id'];?>&nbsp;</td>
	  <td class="dataTableContent" align="center">&nbsp;<?php //echo $products['products_model'];?>&nbsp;</td>
	  <td class="dataTableContent" align="center">&nbsp;<?php //echo ((is_file('../images/'.$products['products_image'])) ?  tep_image('../images/'.$products['products_image'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) : '<br>'.TEXT_NO_IMAGE.'<br>');?>&nbsp;</td>
	  <td class="dataTableContent" align="center">&nbsp;<?php //echo $products['products_name'];?>&nbsp;</td>
	  <td class="dataTableContent" align="center">&nbsp;<?php //echo $currencies->format($products['products_price']);?>&nbsp;</td>
	  <td class="dataTableContent" align="center">&nbsp;<?php //echo tep_draw_pull_down_menu($products['products_id'], $sort_order_drop_array, $products['sort_order']);?>&nbsp;</td>
     </tr>
<?php
//}
?>
    </table></form></td>
   </tr>
   <tr>
    <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxContent">
     <tr>
      <td class="smallText" valign="top"><?php //echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
      <td class="smallText" align="right"><?php //echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID', 'action'))); ?></td>
     </tr>
    </table></td>
   </tr>
  </table>-->
<?php
//}
?>
<!-- body_text_eof //-->
  </td>
 </tr>
</table>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<style>
.productmenutitle{
cursor:pointer;
margin-bottom: 0px;
background-color:orange;
color:#FFFFFF;
font-weight:bold;
font-family:ms sans serif;
width:100%;
padding:3px;
font-size:12px;
text-align:center;
/*/*/border:1px solid #000000;/* */
}
.productmenutitle1{
cursor:pointer;
margin-bottom: 0px;
background-color: red;
color:#FFFFFF;
font-weight:bold;
font-family:ms sans serif;
width:100%;
padding:3px;
font-size:12px;
text-align:center;
/*/*/border:1px solid #000000;/* */
}
</style>
<script type="text/javascript" src="includes/xsell.js"></script>
<script type="text/javascript" src="includes/featured.js"></script>
<script language="JavaScript1.2">

function cOn(td)
{
if(document.getElementById||(document.all && !(document.getElementById)))
{
td.style.backgroundColor="#CCCCCC";
}
}

function cOnA(td)
{
if(document.getElementById||(document.all && !(document.getElementById)))
{
td.style.backgroundColor="#CCFFFF";
}
}

function cOut(td)
{
if(document.getElementById||(document.all && !(document.getElementById)))
{
td.style.backgroundColor="DFE4F4";
}
}
</script>
<script type="text/javascript">
 <?php echo ($_GET['add_related_product_ID']!= '' ? (empty($_GET['add_related_product_ID']) ? 'displaySelection(\'div_listing\', \'C0\', \'X\');displaySelection(\'div_related\', \'C0\', \'XR\');' : 'displaySelection(\'div_related\', \'C0\', \'XR\');' . generateJsRowFillCall($_GET['add_related_product_ID'])) : ''); ?> 
 </script>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>