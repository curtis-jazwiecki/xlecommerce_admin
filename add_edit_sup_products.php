<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
include 'includes/application_top.php';
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
    $get_products_for_package = tep_db_query("SELECT p.products_id, pd.products_name FROM products p, products_description pd, products_to_categories p2c WHERE p.products_id = p2c.products_id and p.products_id = pd.products_id and p.products_id != '".$parent_id."' and `is_package` = '0' and p2c.categories_id in (".$cat_str.") group by p.products_id order by pd.products_name");
    while($get_products = tep_db_fetch_array($get_products_for_package) ){
            $option_text .= '<option value="'.$get_products['products_id'].'">'.$get_products['products_name'].'</option>';
    }
    $return_str = '<select id="selected_products" multiple style="width:" size="10" name="package_products[]">'.$option_text.'</select>';   
    echo $return_str;
    exit();
}elseif(isset($_GET['action']) && $_GET['action'] == 'getSelectedProduct' && !empty($_GET['parent_id'])){
    $pid_array = explode(',', $_GET['pid']);
    $parent_id = $_GET['parent_id'];
    $prodListStr = '';
    tep_db_query("DELETE FROM `products_packages` WHERE `parent_products_packages_id` = '".$parent_id."' and `child_products_packages_id` in (".$_GET['pid'].")");
    if(is_array($pid_array) && !empty($pid_array)){
        foreach($pid_array as $val){
            tep_db_query("INSERT INTO `products_packages`(`parent_products_packages_id`, `child_products_packages_id`) VALUES ('".$parent_id."','".$val."')");
        }
        $productListQuery = tep_db_query("select `products_packages_id`,p.products_id, pd.products_name FROM products_packages, products p, products_description pd WHERE `child_products_packages_id` = p.products_id and  p.products_id = pd.products_id and parent_products_packages_id = '".$parent_id."'");
        while($productList = tep_db_fetch_array($productListQuery)){
            $prodListStr .= '<tr  class="dataTableRow" ><td  class="dataTableContent">'.$productList['products_name'].'</td><td align="center" class="dataTableContent"><a href="#" onclick="deleteThisChild(\''.$productList['products_packages_id'].'\');" style="color:red;"><b>X</b></a></td></tr>';
        }
        $prodListStr = '<table width="80%" align="center" cellspacing="0" cellpadding="2" border="0"><tr class="dataTableHeadingRow"><td class="dataTableHeadingContent">Products Name</td><td class="dataTableHeadingContent" align="center">Delete </td></tr>'.$prodListStr.'</table>';
    
    }
    echo $prodListStr;
    exit();
}elseif(isset($_GET['action']) && $_GET['action'] == 'deletepackageChild' && !empty($_GET['ppid'])){
    tep_db_query("DELETE FROM `products_packages` WHERE products_packages_id = '".$_GET['ppid']."'");
    tep_redirect(tep_href_link('add_edit_sup_products.php',tep_get_all_get_params(array('action','ppid'))));
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
        function get_products(cid,is_package){
            $('#add_button').css('display','none');
            $('#productsection').html('<img src="images/ajax_loader.gif" title="loading ..." alt="loading ...">');
            url = 'add_edit_sup_products.php?action=getProduct&cid='+cid+'&is_package='+is_package+'&parent_id=<?php echo $_GET['pID'];?>';
            $.post( url, function(data) {
                $('#productsection').html(data);
                $('#add_button').css('display','block');
            });
        }
        function showSelectedProducts(pid,is_package){
            if(pid != null ){
                url = 'add_edit_sup_products.php?action=getSelectedProduct&pid='+pid+'&is_package='+is_package+'&parent_id=<?php echo $_GET['pID'];?>';
                //alert(url);
                $.post( url, function(data) {
                    $('#productList').html(data);
                });
            }
        }
        function deleteThisChild(ppid){
            location.href="<?php echo tep_href_link('add_edit_sup_products.php',tep_get_all_get_params(array('action','ppid')).'action=deletepackageChild&ppid=')?>"+ppid;
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
                <td class="dataTableContent" valign="top" aligh="center"><input id="add_button" style="display: none;" type="button" onclick="showSelectedProducts($('#selected_products').val(),'1');" value="add"></td>
            </tr>
            <?php
                $product_text = '';
                $package_product_array = array();
                if(isset($_GET['pID']) && !empty($_GET['pID'])){
                    $productListQuery = tep_db_query("select `products_packages_id`,p.products_id, pd.products_name FROM products_packages, products p, products_description pd WHERE `child_products_packages_id` = p.products_id and  p.products_id = pd.products_id and parent_products_packages_id = '".$_GET['pID']."'");
                    while($productList = tep_db_fetch_array($productListQuery)){
                        $prodListStr .= '<tr  class="dataTableRow" ><td  class="dataTableContent">'.$productList['products_name'].'</td><td align="center" class="dataTableContent"><a href="#" onclick="deleteThisChild(\''.$productList['products_packages_id'].'\');" style="color:red;"><b>X</b></a></td></tr>';
                    }
    
                }
                $prodListStr = '<table width="80%" align="center" cellspacing="0" cellpadding="2" border="0"><tr class="dataTableHeadingRow"><td class="dataTableHeadingContent">Products Name</td><td class="dataTableHeadingContent"  align="center">Delete </td></tr>'.$prodListStr.'</table>';
                            ?>
            <tr class="dataTableRow">
                <td class="dataTableContent" colspan="2" valign="top" id="productList"><?php echo $prodListStr;?></td>

            </tr>
            <tr class="dataTableRow">
                <td class="dataTableContent" valign="top">&nbsp;</td>
                <td class="dataTableContent" valign="top">&nbsp;</td>
            </tr>
        </table>
    </body>
</html>