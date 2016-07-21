<?php 
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require('includes/application_top.php');
$range_id = $_GET['range_id'];
if (!empty($range_id)){
    $sql = tep_db_query("select ranges_name, logo from ranges where ranges_id='" . (int)$range_id . "'");
    if (tep_db_num_rows($sql)){
        $fs_path = DIR_FS_CATALOG . DIR_WS_IMAGES . 'ranges/';
        $ws_path = HTTP_CATALOG_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . ranges . '/';
        $info = tep_db_fetch_array($sql);
        $name = $info['ranges_name'];
        $image = $info['logo'];
        if (!empty($image) && file_exists($fs_path . $image)){
?>
    <div id="container" align="center" style="overflow:hidden;">
        <?php /* <img style="max-width: 400px; max-height: 400px;" src="<?php echo $ws_path . $image; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>" />  */?>
        <img id="logo" src="<?php echo $ws_path . $image; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>" />
    </div>
<?php
        }
         
    }    
}
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $('#logo').css('overflow', 'hidden');
   $('#logo, #container').height(window.innerHeight) - 250; 
});
</script>