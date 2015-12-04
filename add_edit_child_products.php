<?php

include 'includes/application_top.php';
function tep_get_top_parent_models($parent_model = ''){
    $model_str = '';
    $model_array = array();
    if($parent_model != ''){
        $parent_model_query = tep_db_query("select parent_products_model from products WHERE products_model like '".addslashes($parent_model)."' and parent_products_model != '' and parent_products_model is not null");
    }else{
        $parent_model_query = tep_db_query("select parent_products_model from products WHERE parent_products_model != '' and parent_products_model is not null");
    }
    if(tep_db_num_rows($parent_model_query)){
        while($parent_model = tep_db_fetch_array($parent_model_query)){
            if(!in_array($parent_model['parent_products_model'], $model_array)){
                //$model_array[] = $parent_model['parent_products_model'];
                $sub_model_array = tep_get_top_parent_models($parent_model['parent_products_model']);
                if($sub_model_array !== false){
                    if(is_array($sub_model_array) && !empty($sub_model_array)){
                        foreach($sub_model_array as $val){
                            if(!in_array($val, $model_array)){
                                $model_array = $val;
                            }
                        }
                    }
                }else{
                    $model_array[] = $parent_model['parent_products_model'];
                }
            }
        }
        if(!empty($model_array)){
            return $model_array;
        }else{
            return false;
        }
    }else{
        return false;
    }
}

function tep_check_is_parent($product_model){
    $parent_model_query = tep_db_query("select products_model from products WHERE parent_products_model like '".addslashes($parent_model)."'");
    if(tep_db_num_rows($parent_model_query)){
        return true;
    }else{
        return false;
    }
}
//$parent_model = array();
//$parent_model = tep_get_top_parent_models();
if(isset($_GET['action']) && $_GET['action'] == 'getProduct'){
    $category_array[] = $_GET['cid'];
    $parent_id = $_GET['parent_id'];
    $get_sub_cat_id_query = tep_db_query("SELECT `categories_id` FROM `categories` WHERE `parent_id` = '".$_GET['cid']."'");
    if(tep_db_num_rows($get_sub_cat_id_query)){
        while($get_sub_cat_id = tep_db_fetch_array($get_sub_cat_id_query) ){
            if(!in_array($get_sub_cat_id['categories_id'], $category_array)){
                $category_array[] = $get_sub_cat_id['categories_id'];
            }
        }        
    }
    $cat_str = implode(',', $category_array);

    
    $get_products_query = tep_db_query("SELECT p.parent_products_model, p.products_model, p.products_id, pd.products_name FROM products p, products_description pd, products_to_categories p2c WHERE  p.products_id = p2c.products_id and p.products_id = pd.products_id and `is_package` = '0' and p2c.categories_id in (".$cat_str.") ".(!empty($parent_id)?' and p.products_id != '.$parent_id.' ':'')." group by p.products_id order by pd.products_name");
    while($get_products = tep_db_fetch_array($get_products_query) ){
            
        /*if(tep_check_is_parent($get_products['products_model']) && ($get_products['parent_products_model'] == '' || $get_products['parent_products_model'] == null ) ){
            continue;
        }else{*/
            $option_text .= '<option value="'.$get_products['products_id'].'">'.$get_products['products_name'].'</option>';
        //}
    }
    $return_str = '<select id="selected_products" multiple style="width:" size="10" name="package_products[]">'.$option_text.'</select>';   
    echo $return_str;
    exit();
}elseif(isset($_GET['action']) && $_GET['action'] == 'getSelectedProduct' ){
    $pid_array = explode(',', $_GET['pid']);
    $prodListStr = '';
    if(is_array($pid_array) && !empty($pid_array)){
        if(!empty($_GET['parent_id'])){
            $parent_id = $_GET['parent_id'];
            $parent_model = $_GET['parent_model'];
            $parent_attributes_array = array();
			// get all attributes of parent product #start
			
			$parent_attributes_query = tep_db_query("select * from products_attributes where products_id = '".(int)$parent_id."'");
			if(tep_db_num_rows($parent_attributes_query)){
				
				while($result_attribute = tep_db_fetch_array($parent_attributes_query)){
					
					$parent_attributes_array[] = array(
						"options_id" => $result_attribute['options_id'],
						"options_values_id" => $result_attribute['options_values_id'],
						"options_values_price" => $result_attribute['options_values_price'],
						"price_prefix" => $result_attribute['price_prefix'],
						"products_options_sort_order" => $result_attribute['products_options_sort_order'],
						"attributes_hide_from_groups" => $result_attribute['attributes_hide_from_groups']
					);
				
				}
			}
			
					
			// get all attributes of parent product #ends
			
			foreach($pid_array as $val){
            
			    tep_db_query("update products set parent_products_model = '".$parent_model."' where products_id = '".$val."'");
			
				if(count($parent_attributes_array) > 0){
					foreach($parent_attributes_array as $parent_attributes){
						tep_db_query("insert into products_attributes set 
							products_id = '".(int)$val."',
							options_id = '".$parent_attributes['options_id']."',
							options_values_id = '".$parent_attributes['options_values_id']."',
							options_values_price = '".$parent_attributes['options_values_price']."',
							price_prefix = '".$parent_attributes['price_prefix']."',
							products_options_sort_order = '".$parent_attributes['products_options_sort_order']."',
							attributes_hide_from_groups = '".$parent_attributes['attributes_hide_from_groups']."'");
					}
					
				}
			
			}
			
        }
        $productListQuery = tep_db_query("select p.products_id, pd.products_name, p.products_model FROM products p, products_description pd WHERE p.products_id = pd.products_id and p.products_id in (".$_GET['pid'].")");
        while($productList = tep_db_fetch_array($productListQuery)){
            $prodListStr .= '<tr id="child-'.$productList['products_id'].'" class="dataTableRow" ><td  class="dataTableContent">'.$productList['products_name'].'</td><td align="center" class="dataTableContent"><a href="#" onclick="deleteThisChild(\''.$productList['products_id'].'\');" style="color:red;"><b>X</b></a></td></tr>';
        }
        $prodListStr = '<table width="80%" align="center" cellspacing="0" cellpadding="2" border="0"><tr class="dataTableHeadingRow"><td class="dataTableHeadingContent">Products Name</td><td class="dataTableHeadingContent" align="center">Delete </td></tr>'.$prodListStr.'</table>';
    
    }
    echo $prodListStr;
    exit();
}elseif(isset($_GET['action']) && $_GET['action'] == 'deletepackageChild' && !empty($_GET['pid'])){
    
	// delete all parent attributes from this child product #start
	
	$parent_attributes_query = tep_db_query("select * from products_attributes where products_id = ( select products_id from products where products_model like (select parent_products_model from products where products_id = '".(int)$_GET['pid']."'))");
	
	while($result_attributes = tep_db_fetch_array($parent_attributes_query)){
		tep_db_query("delete from products_attributes where 
		products_id = '".(int)$_GET['pid']."' and
		options_id = '".$result_attributes['options_id']."' and
		options_values_id = '".$result_attributes['options_values_id']."'");
	}
	
	// delete all parent attributes from this child product #ends
	
	
	
	tep_db_query("update products set parent_products_model = '' where products_id = '".$_GET['pid']."'");
	
	
	
    $allpid = explode(',',$_GET['allpid']);
    $newpid = array();
    if(!empty($allpid)){
        foreach($allpid as $val){
            if($val == $_GET['pid'])
                continue;
            $newpid[] = $val;
        }
    }
    if(is_array($newpid) && ! empty($newpid)){
        echo implode(',', $newpid);
    }else{
        echo '';
    }
    exit();
    //tep_redirect(tep_href_link('add_edit_sup_products.php',tep_get_all_get_params(array('action','ppid'))));
}


?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css" />
    <?php //BOF:range_manager ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script language="javascript" src="includes/general.js"></script>
    <script type="text/javascript">
        function get_products(cid){
            $('#add_button').css('display','none');
            $('#productsection').html('<img src="images/ajax_loader.gif" title="loading ..." alt="loading ...">');
            url = 'add_edit_child_products.php?action=getProduct&cid='+cid+'&parent_id=<?php echo $_GET['pID'];?>';
            $.post( url, function(data) {
                $('#productsection').html(data);
                $('#add_button').css('display','block');
            });
        }
        function showSelectedProducts(pid){
            if(pid != null ){
                var prev_pid = $('#child_id').val();
                //alert(prev_pid);
                if(prev_pid != '' && prev_pid != ' '){
                    pid = prev_pid + ',' + pid;
                }
                $('#child_id').val(pid);
                <?php
                if(isset($_GET['pID'])){ ?>
                    url = 'add_edit_child_products.php?action=getSelectedProduct&pid='+pid+'&parent_id=<?php echo $_GET['pID'].'&parent_model='.$_GET['model'];?>';
                    //alert(url);
                    <?php
                }else{?>
                    url = 'add_edit_child_products.php?action=getSelectedProduct&pid='+pid;
                <?php
                }?>
                $.post( url, function(data) {
                    $('#productList').html(data);
                });
            }
        }
        function deleteThisChild(pid){
            var allpid = $('#child_id').val();
            url = 'add_edit_child_products.php?action=getSelectedProduct&pid='+pid+'&action=deletepackageChild&allpid='+allpid;
            rowid = '#child-'+pid;
            $.post( url, function(data) { 
                $('#child_id').val(data);
                $(rowid).css('display','none');
            });
            
        }
        function closethis(){
            var allpid = $('#child_id').val();
            window.opener.document.getElementById("child_ids").value = allpid;
            window.opener.showaddedProducts();
            window.self.close();
        }
        
    </script>
    </head>
    <body color="white" marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="goOnLoad();">
        <table align="center" width="70%" cellpadding="0" cellspacing="0" style="padding-left: 10px;">
            <tr>
                <td class="pageHeading" width="20%"><?php echo 'Select Products'; ?></td>
                <td class="pageHeading" width="80%" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            </tr>
            <tr class="dataTableRow">
                <td class="dataTableContent" width="20%" valign="top">&nbsp;</td>
                <td class="dataTableContent" width="80%" valign="top">&nbsp;</td>
            </tr>
            <tr class="dataTableRow">
                <td class="dataTableContent" width="20%" valign="top">&nbsp;&nbsp;<b>Select Category:</b> </td>
                <td class="dataTableContent" width="80%" valign="top" align="left" ><?php echo tep_draw_pull_down_menu('cPath', tep_get_category_tree(), $current_category_id, 'onChange="get_products(this.value,\'1\');"');?></td>
            </tr>
            <tr class="dataTableRow">
                <td class="dataTableContent" width="20%" valign="top">&nbsp;</td>
                <td class="dataTableContent" width="80%" valign="top">&nbsp;</td>
            </tr>
            <tr class="dataTableRow">
                <td class="dataTableContent" width="20%" valign="top">&nbsp;&nbsp;<B>Select Products:</B></td>
                <td class="dataTableContent" width="80%" valign="top" align="left" id="productsection">Please Select Category First..</td>
            </tr>
             <tr class="dataTableRow">
                <td class="dataTableContent" valign="top">&nbsp;</td>
                <td class="dataTableContent" valign="top">&nbsp;</td>
            </tr>
            <tr class="dataTableRow">
                <td class="dataTableContent" valign="top">&nbsp;</td>
                <td class="dataTableContent" valign="top" aligh="center"><input id="add_button" style="display: none;" type="button" onClick="showSelectedProducts($('#selected_products').val());" value="add"></td>
            </tr>
            <?php
                $product_text = '';
                $package_product_array = array();
                $pid_str='';
                if(isset($_GET['pID']) && !empty($_GET['pID'])){
                    $productListQuery = tep_db_query("select p.products_id, pd.products_name FROM  products p, products_description pd WHERE  p.products_id = pd.products_id and parent_products_model = '".$_GET['model']."'");
                    while($productList = tep_db_fetch_array($productListQuery)){
                        $pid_str .= $productList['products_id'].',';
                        $prodListStr .= '<tr id="child-'.$productList['products_id'].'" class="dataTableRow" ><td class="dataTableContent">'.$productList['products_name'].'</td><td align="center" class="dataTableContent"><a href="#" onclick="deleteThisChild(\''.$productList['products_id'].'\');" style="color:red;"><b>X</b></a></td></tr>';
                    }
    
                }
                $pid_str = rtrim($pid_str,',');
                $prodListStr = '<table width="80%" align="center" cellspacing="0" cellpadding="2" border="0"><tr class="dataTableHeadingRow"><td class="dataTableHeadingContent">Products Name</td><td class="dataTableHeadingContent"  align="center">Delete </td></tr>'.$prodListStr.'</table>';
                            ?>
            <tr class="dataTableRow">
                <td class="dataTableContent" colspan="2" valign="top" id="productList"><?php echo $prodListStr;?></td>

            </tr>
            <tr class="dataTableRow">
                <td class="dataTableContent" valign="top">&nbsp;<input type="hidden" value="<?php echo $pid_str;?>" id="child_id" name="child_id"></td>
                <td class="dataTableContent" valign="top">&nbsp;</td>
            </tr>
        </table>
        <input type="button" value="close" name="close" onClick="closethis();">
    </body>
</html>