<?php
require_once ('includes/application_top.php');

$vendors_id = '1';

function tep_get_manufacturers($manufacturers_array = '') { // Function borrowed from the Catalog side
    if (!is_array($manufacturers_array)) $manufacturers_array = array();
    $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");
    while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
      $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']);
    }
    return $manufacturers_array;
}
  
function get_categories($parent_id='0'){
    $categories = array();
    $categories_query = tep_db_query("select c.categories_id as id, cd.categories_name as name from categories c inner join categories_description cd on (c.categories_id=cd.categories_id and cd.language_id='1') where c.parent_id='" . (int)$parent_id . "' order by cd.categories_name");
    while($category = tep_db_fetch_array($categories_query)){
        $categories[$category['id']] = $category['name'];
    }
    return $categories;
}
  
function get_categories_html($parent_id='0', $level='1'){
    $categories = get_categories($parent_id);
    if (!empty($categories)){
        $html = '<select name="group_' . $level . '_' . $parent_id . '" level="' . $level . '" style="width:200px;max-width:200px;">';
        $html .= '<option value="">Category: Level ' . $level . ' </option>';
        foreach($categories as $id => $name){
            $html .= '<option value="' . $id . '">' . $name . '</option>';
        }
        $html .= '</select>';
        
        return $html;
    } else {
        return false;
    }
}
  
function get_products_by_category($category_id){
    $products = array();
    $products_query = tep_db_query("select p.products_id as id, pd.products_name as name from products p inner join products_description pd on (p.products_id=pd.products_id and pd.language_id='1') inner join products_to_categories p2c on p.products_id=p2c.products_id where p2c.categories_id='" . (int)$category_id . "' order by pd.products_name");
    while($product = tep_db_fetch_array($products_query)){
        $products[$product['id']] = $product['name'];
    }
    return $products;
}
  
function get_products_html_by_category($category_id){
    $products = get_products_by_category($category_id);
    if (!empty($products)){
        $html = '<select name="product" style="width:200px;max-width:200px;">';
        $html .= '<option value="">Products</option>';
        foreach($products as $id => $name){
            $html .= '<option value="' . $id . '">' . $name . '</option>';
        }
        $html .= '</select>';
        
        return $html;
    } else {
        return false;
    }
}
  
function get_associated_products_html($ids){
    $html = '';
    if ($ids!=''){
        if (substr($ids, 0, 1)==',') $ids = substr($ids, 1);
        if (substr($ids, -1)==',') $ids = substr($ids, 0, -1);
        
        $sql = tep_db_query("select p.products_id, p.products_model, pd.products_name from products p inner join products_description pd on (p.products_id=pd.products_id and pd.language_id='1') where p.products_id in (" . $ids . ") order by pd.products_name");
        while($entry = tep_db_fetch_array($sql)){
            $html .= '<div><img id="icon_remove" src="' . DIR_WS_IMAGES . 'icons/cross.gif" width="16" height="16" productid="' . $entry['products_id'] . '" style="cursor:pointer;" />&nbsp;&nbsp;' . $entry['products_model'] . ' : ' . $entry['products_name'] . '</div>';
        }
    }
    return $html;
}

function get_products_count_by_vendor($vendors_id){
    $sql = tep_db_query("select count(*) as count from products where vendors_id='" . (int)$vendors_id . "'");
    $info = tep_db_fetch_array($sql);
    return $info['count'];
}

$vendors_array = array();
$vendors_query = tep_db_query ("select vendors_id, vendors_name from " . TABLE_VENDORS . " order by vendors_name");
while ($vendors = tep_db_fetch_array ($vendors_query) ) {
    $vendors_array[] = array ('id' => $vendors['vendors_id'], 'text' => $vendors['vendors_name'] );
}

if (isset($_POST['action'])){
    $action = $_POST['action'];    
}

switch($action){
    case 'generate_dropdown':
        $parent_name = $_POST['parent_name'];
        $category_id = $_POST['category_id'];
        list(,$level, $parent_category_id) = explode('_', $parent_name);
        $html = get_categories_html($category_id, ++$level);
        if (!$html){
            $html = get_products_html_by_category($category_id);
        }
        echo $html;
        exit;
        break;
    case 'products_list':
        $ids = $_POST['ids'];
        $html = get_associated_products_html($ids);
        echo $html;
        exit;
        break;
    case 'selectvendor':
        $vendors_id = $_POST['vendors_id'];
        break;
    case 'process':
        $operation = $_POST['operation'];
        $product_wildcard = $_POST['productswildcard'];
        $manufacturers_id = $_POST['manufacturers_id'];
        $categories_id = $_POST['categories_id'];
        $products = $_POST['products'];
        $vendors_id = $_POST['vendor_id'];
        
        if (!empty($vendors_id)){
            $where = "";
            if (!empty($product_wildcard)){
                $where .= " p.products_model like '%" . tep_db_input($product_wildcard) . "%' or ";
            }
            if (!empty($manufacturers_id)){
                $where .= " p.manufacturers_id in (" . implode(',', $manufacturers_id) . ") or ";
            }
            if (!empty($categories_id)){
                $where .= " p2c.categories_id in (" . implode(',', $categories_id) . ") or ";
            }
            $products_id = array();
            if (!empty($products)){
                $entries = explode(',', $products);
                foreach($entries as $id){
                    $id = trim($id);
                    if (!empty($id)){
                        $products_id[] = $id; 
                    }
                }
            }
            if (!empty($products_id)){
                $where .= " p.products_id in (" . implode(',', $products_id) . ") or ";
            }
            
            if (!empty($where)){
                $where = substr($where, 0, -3);
            }
            if (isset($_POST['is_ajax_call']) && $_POST['is_ajax_call']=='1'){
                if (!empty($where)){
                    $sql = tep_db_query("select count(p.products_id) as count from products p inner join products_to_categories p2c on p.products_id=p2c.products_id where ". $where);
                    $info = tep_db_fetch_array($sql);
                    echo $info['count'];
                } else {
                    echo '0';
                }
                exit;
            } else {
                if (!empty($where)){
                    switch($operation){
                        case 'set':
                            tep_db_query("update products p, products_to_categories p2c set p.vendors_id='" . (int)$vendors_id . "' where p.products_id=p2c.products_id and ( " . $where . ")");
                            $messageStack->add('Filtered product(s) mapped with vendor', 'success');
                            break;
                        case 'remove':
                            tep_db_query("update products p, products_to_categories p2c set p.vendors_id='1' where p.products_id=p2c.products_id and (" . $where . ")");
                            $messageStack->add('Filtered product(s) removed from vendor', 'success');
                            break;
                    }
                }
            }
        }
        break;
}

?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<style type="text/css">
#cancel{
 cursor: pointer !important;
    border-radius: 3px;
    box-shadow: 0 -1px 0 rgba(0, 0, 0, 0.15) inset !important;
    transition: none !important;
	border-radius: 4px !important;
    font-size: 12px !important;
    line-height: 1.5 !important;
	text-transform:capitalize !important;
	margin-right:2px !important;
    padding: 5px 10px !important;
	background-color: #3498db !important;
    border-color: #258cd1 !important;
    color: #ffffff !important;
	outline-color:#258cd1 !important;
	font-weight:bold !important;
	background-image:none !important;
  
}
</style>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<script>
var jQuery = jQuery.noConflict();
jQuery(function(){
    var img = new Image();
    img.src = '<?php echo DIR_WS_IMAGES?>' + 'icons/ajax-loader-small.gif'; 
    
    jQuery('input[type="submit"], button').button();
    
    //jQuery('div[id^=status_"]').buttonset();
    
    jQuery('button#cancel').click(function(event){
        event.preventDefault();
        jQuery('select[name="manufacturers_id[]"] option:selected').removeAttr('selected');
        jQuery('select[name="categories_id[]"] option:selected').removeAttr('selected');
        jQuery('div#products').html('');
        jQuery('td#display_products').html('');
        jQuery('input:hidden[name="products"]').val('');
        jQuery('input:hidden[name="vendor_id"]').val('');
        jQuery('input:hidden[name="action"]').val('process');
    });
    
    jQuery(document).on('change', 'select[name^="group_"]', function(){
       parent_name = jQuery(this).attr('name');
       temp = parent_name.split('_');
       var level = parseInt(temp[1]);
       
       jQuery('select[name^="group_"]').each(function(){
        name = jQuery(this).attr('name');
        //console.log(name);
        temp = name.split('_');
        cur_level = parseInt(temp[1]);
        if (cur_level>level){
            jQuery(this).remove();
        }
       });
       
       jQuery('select[name="product"]').remove();
       
       category_id = jQuery(this).val();
       jQuery('div#products').append('<div id="loader"><img src="<?php echo DIR_WS_IMAGES ?>icons/ajax-loader-small.gif" /></div>');
       jQuery.ajax({
        url: 'products_to_vendor.php', 
        method: 'post', 
        data: {parent_name: parent_name, category_id: category_id, action: 'generate_dropdown'},
        success: function(html){
           jQuery('#loader').remove();
           jQuery('div#products').append(html); 
        } 
       }) 
    });
    
    jQuery(document).on('change', 'select[name="product"]', function(){
        item = jQuery(this).find('option:selected').html();
        id = jQuery(this).val();
        
        products = jQuery('input:hidden[name="products"]').val();
        products_updated = products;
        if (products_updated==''){
            products_updated = ',' + id + ',';
            
        } else {
            if (products_updated.indexOf(',' + id + ',')==-1){
                products_updated += id + ',';
            }
        }
        jQuery('input:hidden[name="products"]').val(products_updated);
        jQuery('td#display_products').append('<div id="loader"><img src="<?php echo DIR_WS_IMAGES ?>icons/ajax-loader-small.gif" /></div>');
        jQuery.ajax({
           url: 'products_to_vendor.php', 
           method: 'post', 
           data: {action: 'products_list', ids: jQuery('input:hidden[name="products"]').val()}, 
           success: function(html){
            jQuery('td#display_products').html(html);
            set_count_by_filters();
           } 
        });
        
    })
    .on('click', 'img#icon_remove', function(){
       product_id = jQuery(this).attr('productid');
       associated_products = jQuery('input:hidden[name="products"]').val();
       updated_products = associated_products.replace(',' + product_id, '');
       if (updated_products==',') updated_products='';
       jQuery('input:hidden[name="products"]').val(updated_products);
jQuery('td#display_products').append('<div id="loader"><img src="<?php echo DIR_WS_IMAGES ?>icons/ajax-loader-small.gif" /></div>');
        jQuery.ajax({
           url: 'products_to_vendor.php', 
           method: 'post', 
           data: {action: 'products_list', ids: jQuery('input:hidden[name="products"]').val()}, 
           success: function(html){
            jQuery('td#display_products').html(html);
            set_count_by_filters();
           } 
        });
        
    })
    .on('change', 'input[name="productswildcard"], select[name="manufacturers_id[]"], select[name="categories_id[]"]', function(){
        set_count_by_filters();
    });
    
    /*jQuery('a[id^="vendor_"]').click(function(event){
        event.preventDefault();
        
        jQuery('select[name="manufacturers_id[]"] option:selected').removeAttr('selected');
        jQuery('select[name="categories_id[]"] option:selected').removeAttr('selected');
        
        manufacturer_ids = jQuery(this).attr('manufacturer_ids');
        category_ids = jQuery(this).attr('category_ids');
        product_ids = jQuery(this).attr('product_ids');
        
        jQuery('input[name="vendorref"]').val(jQuery(this).attr('vendorreference'));
        
        if (manufacturer_ids!=''){
            split_ids = manufacturer_ids.split(',');
            for(var i=0; i<split_ids.length; i++){
                jQuery('select[name="manufacturers_id[]"] option[value="' + split_ids[i] + '"]').attr('selected', 'selected');
            }
        }
        
        if (category_ids!=''){
            split_ids = category_ids.split(',');
            for(var i=0; i<split_ids.length; i++){
                jQuery('select[name="categories_id[]"] option[value="' + split_ids[i] + '"]').attr('selected', 'selected');
            }
        }
        
        if (product_ids!=''){
            jQuery('td#display_products').html('');
            jQuery('input:hidden[name="products"]').val(product_ids);
jQuery('td#display_products').append('<div id="loader"><img src="<?php //echo DIR_WS_IMAGES ?>icons/ajax-loader-small.gif" /></div>');
            jQuery.ajax({
               url: 'custom_shipping_modules.php', 
               method: 'post', 
               data: {action: 'products_list', ids: jQuery('input:hidden[name="products"]').val()}, 
               success: function(html){
                jQuery('td#display_products').html(html);
               } 
            });
        }
        
        jQuery('input[name="action"]').val('edit');
        jQuery('input[name="vendor_id"]').val(jQuery(this).attr('vendor_id'));
    });*/
    

});

function set_count_by_filters(){
    jQuery('input[name="is_ajax_call"]').val('1');
    jQuery.ajax({
       url: 'products_to_vendor.php', 
       method: 'post', 
       data: jQuery('form[name="form_mapping"]').serialize(), 
       success: function(html){
        jQuery('#matching_products_count').html(html);
        jQuery('input[name="is_ajax_call"]').val('0');
       } 
    });
}

</script>
<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3>Products to Vendors Mapping
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading">Products to Vendors Mapping
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
            <form name="form_vendor" method="post" action="products_to_vendor.php">
                <table>
                    <tr>
                        <td>
                            Select Vendor:
                        </td>
                        <td>
                            <?php echo tep_draw_pull_down_menu('vendors_id', $vendors_array,'','onChange="this.form.submit()";');?>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="action" value="selectvendor" />
            </form>
        </td>
    </tr>
    <tr>
        <td>
        <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
        </td>
    </tr>
    <tr>
        <td>
            <form name="form_mapping" method="post">
                <fieldset>
                    <legend><b>Manage Products</b></legend>
                    <table class="table table-bordered table-hover">
                        <tr>
                            <td colspan="4">
                            Currently associated products count: <a href="<?php echo tep_href_link(FILENAME_PRODS_VENDORS, 'vendors_id=' . $vendors_id); ?>" style="color: #000000;"><?php echo get_products_count_by_vendor($vendors_id); ?></a>
                            </td>
                        </tr>
                        <tr>
                            <td>Action:</td>
                            <td colspan="3">
                                <select name="operation">
                                    <option value="set">Set Products Mapping by Filter(s)</option>
                                    <option value="remove">Remove Products Mapping by Filter(s)</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                            Matching products count by below filters: <span id="matching_products_count" style="color: red;">--</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                            <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Products wildcard:</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td valign="top" align="left">
                                <input type="text" name="productswildcard" maxlength="50" style="width:100%;" />
                            </td>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Manufacturers:</td>
                            <td>Categories:</td>
                            <td>Products:</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="left">
                            <?php echo tep_draw_pull_down_menu('manufacturers_id[]', tep_get_manufacturers(), '', 'multiple="multiple" size="10" style="width:200px;"'); ?>
                            </td>
                            <td align="left">
                            <?php echo tep_draw_pull_down_menu('categories_id[]', tep_get_category_tree(), '', 'multiple="multiple" size="10" style="width:200px;"'); ?>
                            </td>
                            <td align="left">
                                <?php echo get_categories_html(); ?>
                                <div id="products"></div>

                            </td>
                            <td align="right">
                                <input type="submit" value="Process" style="width:100%;" />
                                <br>
                                <button id="cancel" style="width:100%;">Cancel</button>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Products:</b></td>
                        </tr>
                        <tr>
                            <td colspan="4" id="display_products"></td>
                        </tr>
                    </table>
                </fieldset>
                <input type="hidden" name="action" value="process" />
                <input type="hidden" name="vendor_id" value="<?php echo $vendors_id; ?>" />
                <input type="hidden" name="products" value="" />
                <input type="hidden" name="is_ajax_call" value="0" />
            </form>
        </td>
    </tr>
</table>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>