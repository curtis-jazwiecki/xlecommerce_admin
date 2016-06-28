<?php

require ('includes/application_top.php');



if( (isset($_GET['mode'])) && ($_GET['mode'] == 'updateVariationId') ){

	

	$json = array();

	
    $sql = "update products_options set variation_id =". (int)$_POST['variation_id'] ." where products_options_id =". (int)$_POST['option_id'];
    $query = tep_db_query($sql);

	$json['success'] = 1;
	

	echo json_encode($json);

	exit;

}





?>
<?php
    $amazonvariation = "select variation_id, variation_name from amazon_variations order by variation_name";
    $amazon_variations = tep_db_query($amazonvariation);
    $variations[] = array('id' => '','text' => 'Select Variation');
    foreach($amazon_variations as $var){
        $variations[] = array('id' => $var['variation_id']  , 'text' => $var['variation_name']);
    }
    $product_option_query = "Select products_options_id,products_options_name,variation_id from products_options where language_id=1 order by products_options_name";
    //$product_option_numrows=10;
    $product_option_split = new splitPageResults($HTTP_GET_VARS['page'], 50, $product_option_query, $product_option_numrows);
    $product_option_result = tep_db_query($product_option_query);

?>

<?php  include(DIR_WS_LANGUAGES.$language.'/option_variation.php') ?>


<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<!-- header //-->

<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

<!-- header_eof //-->





  <section>

         <!-- START Page content-->

         <section class="main-content">

            <h3><?php echo HEADING_TITLE; ?>

               <br>

            </h3>

            <!-- START panel-->

            <div class="panel panel-default">

               <div class="panel-heading"><?php echo TABLE_HEADING; ?>

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

<!-- body_text //-->

    

      </tr>

      <tr>
        <td><table class="table table-bordered table-hover">
 
<?php //echo tep_draw_form('mapvariation_'.$i, ,'action=avatax_action_address_validate', 'post', 'enctype="multipart/form-data"');?>

<table class="table table-bordered table-hover">

  <tr>

    <th ><?php echo HEADING_PRODUCT_OPTION; ?></th>
    

    <th><?php echo HEADING_AMAZON_VARIATION ;?></th>

  </tr>
<?php

$i=0;
 while($product_option = tep_db_fetch_array($product_option_result)){ $i++; ?>
  <tr>

    <td valign="top" ><?php echo $product_option['products_options_name'] ; ?>
     <input type="hidden" name="option_<?php echo $i ?>" value="<?php echo $product_option['products_options_id'] ?>"> </td>
    <td valign="top" ><?php

		

		echo tep_draw_pull_down_menu('variation_'.$i,$variations,$product_option['variation_id']); 

	?> 

      <span id="ajaxloader_<?php echo $i;?>"></span></td>

  </tr>
  <?php } ?>
  
  <tr>

                <td colspan="2"><table class="table table-bordered table-hover">

                  <tr>

                    <td><?php echo $product_option_split->display_count($product_option_numrows, 50, $HTTP_GET_VARS['page'], TEXT_DISPLAY_PRODUCT_OPTION); ?></td>

                    <td align="right"><?php echo $product_option_split->display_links($product_option_numrows, 50, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>

                  </tr>

                </table></td>

              </tr>

</table>
<!--</form>-->

</table>
</td>
</tr>
</table>
<script type="text/javascript">
<?php for($j=1; $j<=$i;$j++) { ?>
jQuery('select[name="variation_<?php echo $j ?>"]').bind('change',function(){


	if(jQuery("select[name='variation_<?php echo $j ?>'] option:selected").index()>0){
	   
	   var product_option_id = jQuery('input[name="option_<?php echo $j ?>"]').val();
        
	jQuery.ajax({

		

		url: 'option_variation.php?mode=updateVariationId',

		

		type: 'post',

		

		dataType: 'json',

		

		data: 'option_id='+ encodeURIComponent(product_option_id) +'&variation_id=' + encodeURIComponent(jQuery(this).val()),

		

		beforeSend: function() {

			jQuery('#ajaxloader_<?php echo $j;?>').html('<img src="images/loading.gif" alt="" class="attention" align="absmiddle" />');

		},

		complete: function() {

			

			jQuery('.attention').remove();

			

		},

		success: function(data) {

			

			if(data['success']){

			
				jQuery('#ajaxloader_<?php echo $j;?>').html('<?php echo SUCCESS_MESSAGE?>')

			

			}

			

		}

	});

	}

});

<?php } ?>

</script>
<!-- footer //-->

<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>

<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>