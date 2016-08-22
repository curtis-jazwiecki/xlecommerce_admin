<?
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
 require('includes/application_top.php'); ?>
<?php
    define('TAB_PRODUCT',               'products');
    define('TAB_PRODUCT_DESC',          'products_description');
    define('TAB_EXTENDED',              'products_extended');
    define('TAB_MANUFACTURER',          'manufacturers');
    /**
     * 
     * 
     * add more fields that exist in above tables by modifying '$fields' variable.
     * FIELD MUST BE COLUMNS EXISTING IN ABOVE TABLES
     * 
     * left column holds all avaiable fields.
     * drag field(s) from left and drop o nthe left side.
     * CSV report format customizable by adding/sorting fields 
     * 
     * $fields variable is critical.
     * BELOW IS EXPLANATION:
     * : key is actual data column name
     * : key in turn holds three values -> id, title, and desc
     * : in case of adding new fields, ensure 'id' is <tableName>.<columnName>
     * : value for 'title' can be modified
     * : value for 'desc' can be modified
     * 
     * 
     * let us know in case of creating provision for fields existing in tables other than above
     */
     function getQpuPrice($base_price,$markup,$roundoff_flag){
        $qpu_price = 0;
            
        if (empty($markup)){
            $markup = '0';
        }
    
        if (strpos($markup, '-')){
            $operator = '-';
        } else{
            $operator = '+';
        }
    
        $markup = str_replace("-", "", $markup);
    
        if (strpos($markup, '%')){
            $markup_flag = 'p';
            $markup = str_replace("%", "", $markup);
            $markup_price = $base_price * ((float)$markup / 100);
        } else {
            $markup_flag = 'f';
            $markup_price = (float)$markup;
        }
    
        if ($operator == '+'){
            $price = $base_price + $markup_price;
        } else{
            $price = $base_price - $markup_price;
        }
            
        if ($operator == '+'){
            $price = $base_price + $markup_price;
        } else{
            $price = $base_price - $markup_price;
        }
            
        $qpu_price = ($roundoff_flag ? apply_roundoff(round($price, 4)) : round($price,4));
        
        return $qpu_price;
    
    }
 
    $fields = array(
        'products_id' => array(
            'id' => TAB_PRODUCT. '.products_id', 
            'title' => 'Product ID', 
            'desc'  => 'Product ID for uniquely identifying product',  
        ), 
        'products_name' => array(
            'id' => TAB_PRODUCT_DESC. '.products_name', 
            'title' => 'Product Name', 
            'desc'  => 'Product Name',  
        ), 
        'products_description' => array(
            'id' => TAB_PRODUCT_DESC. '.products_description', 
            'title' => 'Product Description', 
            'desc'  => 'Product Description',  
        ), 
        'products_quantity' => array(
            'id' => TAB_PRODUCT . '.products_quantity', 
            'title' => 'Warehouse Qty', 
            'desc'  => 'Current product quantity in warehouse',
        ),
        'store_quantity' => array(
            'id' => TAB_PRODUCT . '.store_quantity', 
            'title' => 'Store Qty', 
            'desc'  => 'Current product quantity in store',
        ),
        'products_model' => array(
            'id' => TAB_PRODUCT . '.products_model', 
            'title' => 'product Model', 
            'desc'  => 'Product model',
        ),
        'products_url' => array(
            'id' => TAB_PRODUCT. '.products_url', 
            'title' => 'Product URL', 
            'desc'  => 'Product URL',
        ),
        'manufacturers_name' => array(
            'id' => TAB_MANUFACTURER . '.manufacturers_name', 
            'title' => 'Product Manufacturer', 
            'desc'  => 'Product Manufacturer',
        ),
        'upc_ean' => array(
            'id' => TAB_EXTENDED . '.upc_ean', 
            'title' => 'Product UPC', 
            'desc'  => 'Product UPC',
        ),
        'products_image' => array(
            'id' => TAB_PRODUCT . '.products_image', 
            'title' => 'product Small Image', 
            'desc'  => 'Product\'s small image',
        ),
        'products_mediumimage' => array(
            'id' => TAB_PRODUCT . '.products_mediumimage', 
            'title' => 'product Medium Image', 
            'desc'  => 'Product\'s medium image',
        ),
        'products_largeimage' => array(
            'id' => TAB_PRODUCT . '.products_largeimage', 
            'title' => 'product Large Image', 
            'desc'  => 'Product\'s large image',
        ),
        'products_size' => array(
            'id' => TAB_PRODUCT . '.products_size', 
            'title' => 'Product Size', 
            'desc'  => 'Product\'s size information',
        ),
        'qpu_price' => array(
            'id' => TAB_PRODUCT . '.qpu_price', 
            'title' => 'Product Price', 
            'desc'  => 'Product\'s QPU Price',
        ),
        'products_weight' => array(
            'id' => TAB_PRODUCT . '.products_weight', 
            'title' => 'Product Weight', 
            'desc'  => 'Product\'s weight',
        ),
        'base_price' => array(
            'id' => TAB_PRODUCT . '.base_price', 
            'title' => 'Base Price', 
            'desc'  => 'Product\'s base price',
        ),
        'products_length' => array(
            'id' => TAB_PRODUCT . '.products_length', 
            'title' => 'Length', 
            'desc'  => 'Product\'s length',
        ),
        'products_width' => array(
            'id' => TAB_PRODUCT . '.products_width', 
            'title' => 'Width', 
            'desc'  => 'Product\'s width',
        ),
        'products_height' => array(
            'id' => TAB_PRODUCT . '.products_height', 
            'title' => 'Height', 
            'desc'  => 'Product\'s height',
        ),
        'manual_price' => array(
            'id' => TAB_PRODUCT . '.manual_price', 
            'title' => 'Manual Price', 
            'desc'  => 'Manual Price',
        ),
        'parent_products_model' => array(
            'id' => TAB_PRODUCT . '.parent_products_model', 
            'title' => 'Parent Product', 
            'desc'  => 'Parent Product',
        ),
        'is_amazon_ok' => array(
            'id' => TAB_PRODUCT . '.is_amazon_ok', 
            'title' => 'Product activated for amazon', 
            'desc'  => 'Amazon Status',
        ),
        'is_ebay_ok' => array(
            'id' => TAB_PRODUCT . '.is_ebay_ok', 
            'title' => 'Product activated for ebay', 
            'desc'  => 'Ebay Status',
        ),
        'ebay_category_id' => array(
            'id' => TAB_PRODUCT . '.ebay_category_id', 
            'title' => 'Ebay category id', 
            'desc'  => 'Ebay Category',
        ),
        'is_store_item' => array(
            'id' => TAB_PRODUCT . '.is_store_item', 
            'title' => 'Product sellable in store', 
            'desc'  => 'Store Item',
        ),
        'is_package' => array(
            'id' => TAB_PRODUCT . '.is_package', 
            'title' => 'Product is part of package', 
            'desc'  => 'Packaged Product',
        ),
        'google_category_id' => array(
            'id' => TAB_PRODUCT . '.google_category_id', 
            'title' => 'Google category ID', 
            'desc'  => 'Google Category ID',
        ),
        'google_category_path' => array(
            'id' => TAB_PRODUCT . '.google_category_path', 
            'title' => 'Google category path', 
            'desc'  => 'Google Category Path',
        ),
    ); 

    /**
     * DO NO EDIT BELOW THIS LINE
     */
    
    $action = $_POST['action'];
    switch($action){
        case 'generate_csv':
            $file_name = time() . '.csv';
            $columns = $_POST['columns'];
            
            $select_columns = explode(",", $columns);
            foreach ($select_columns as $key => $data) {
               foreach ($fields as $a => $b) {
                if ($data == $b['id']) {
                    $show[] = $a;
                    $header[] = $b['title'];
                }
               } 
               if ($data == 'products.qpu_price') {
                $select_fields .= 'products.base_price, products.markup, products.roundoff_flag,';
               }else if($data == 'products.products_url'){
                     $select_fields .= 'products.products_id,';
               } else
                $select_fields .= $data . ",";
            }
            
            
            $query = tep_db_query("select " . substr($select_fields,0,-1) . " from " . 
            TAB_PRODUCT . " 
            inner join " . TAB_PRODUCT_DESC . " on (products.products_id=products_description.products_id and products_description.language_id='" . (int)$languages_id . "') 
            left join " . TAB_EXTENDED . " on products.products_id=products_extended.osc_products_id 
            left join " . TAB_MANUFACTURER . " on products.manufacturers_id=manufacturers.manufacturers_id");
            
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Description: File Transfer');
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename={$file_name}");
            header("Expires: 0");
            header("Pragma: public");
            
            $handle = @fopen( 'php://output', 'w' );
            
            fputcsv($handle, $header);
            while($row = tep_db_fetch_array($query)){
                if(in_array('products.products_url',$select_columns)){
                    $row['products_url'] = tep_catalog_href_link('product_info.php', 'products_id=' . $row['products_id']);
                }
                
                if(in_array('products.qpu_price',$select_columns)){
                    
                    $row['qpu_price'] = getQpuPrice($row['base_price'],$row['markup'],$row['roundoff_flag']);
                                    
                }
               
                foreach ($show as $key => $value) {
                    $output[$value] = $row[$value];
                }
                fputcsv($handle, array_values($output));
            }
            fclose($handle);
            exit;
            break;
    }
    

?>
<title>Marketing Feeds</title>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
         <style>
        .column {
        width: 300px;
        float: left;
        padding-bottom: 100px;
        }
        .portlet {
        margin: 0 1em 1em 0;
        padding: 0.3em;
        }
        .portlet-header {
        padding: 0.2em 0.3em;
        margin-bottom: 0.5em;
        position: relative;
        font-size:12px;
        }
        .portlet-toggle {
        position: absolute;
        top: 50%;
        right: 0;
        margin-top: -8px;
        }
        .portlet-content {
        padding: 0.4em;
        font-size:12px;
        }
        .portlet-placeholder {
        border: 1px dotted black;
        margin: 0 1em 1em 0;
        height: 50px;
        }
        </style>
        <link rel="stylesheet" type="text/css" href="includes/stylesheet.css" />
        <link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="//code.jquery.com/jquery-1.9.1.js"></script>
        <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
 <script>
$(function() {
    $( ".column" ).sortable({
    connectWith: ".column",
    handle: ".portlet-header",
    cancel: ".portlet-toggle",
    placeholder: "portlet-placeholder ui-corner-all", 
    cursor: 'move'
    });
    
    $( ".portlet" )
    .addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
    .find( ".portlet-header" )
    .addClass( "ui-widget-header ui-corner-all" )
    .prepend( "<span class='ui-icon ui-icon-minusthick portlet-toggle'></span>");
    
    $( ".portlet-toggle" ).click(function() {
    var icon = $( this );
    icon.toggleClass( "ui-icon-minusthick ui-icon-plusthick" );
    icon.closest( ".portlet" ).find( ".portlet-content" ).toggle();
    });
    
    $( ".portlet-toggle" ).each(function(){
        var icon = $( this );
        icon.toggleClass( "ui-icon-minusthick ui-icon-plusthick" );
        icon.closest( ".portlet" ).find( ".portlet-content" ).toggle();
    });
    
    $('button#generate_report')
    .button()
    .click(function(event){
       event.preventDefault();
       var columns = '';
       $('td#selection .column .portlet').each(function(){
            columns += $(this).attr('column') + ',';
       });
       if (columns!=''){
            columns = columns.substring(0, columns.length-1);
            $('input:hidden[name="columns"]').val(columns);
            $('form[name="form_csv"]').submit();
       }
    });
    
    
});
</script>

<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3>Marketing Feeds
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading">Marketing Feeds
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
                Drag from left and drop on right
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table table-bordered table-hover">
                        <tr>
                            <td>
                                <div class="column">
                            <?php 
                            // reference: https://jqueryui.com/sortable/#portlets
                            /*$columns_products = tep_db_query("show columns from " . TAB_PRODUCT);
                            while ($column = tep_db_fetch_array($columns_products)){
                            ?>
                                    <div class="portlet">
                                        <div class="portlet-header">
                                        <?php echo $column['Field']; ?>
                                        </div>
                                        <div class="portlet-content">
                                        <?php echo $column['Field']; ?> content
                                        </div>
                                    </div>
                            <?php
                            } */ 
                            ?>
                            <?php foreach($fields as $field => $details){?>
                                    <div class="portlet" column="<?php echo $details['id']; ?>">
                                        <div class="portlet-header">
                                        <?php echo $details['title']; ?>
                                        </div>
                                        <div class="portlet-content">
                                        <?php echo $details['desc']; ?>
                                        </div>
                                    </div>
                            <?php } ?>
                                </div>
                            </td>
                            <td id="selection">
                            <div style="margin-bottom: 15px;"><button id="generate_report">Click to Generate Report</button></div>
                                <div class="column">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <button id="generate_report">Click to Generate Report</button>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <form name="form_csv" method="post">
            <input type="hidden" name="columns" value="" />
            <input type="hidden" name="action" value="generate_csv" />
        </form>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>