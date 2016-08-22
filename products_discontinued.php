<?php
/*
  $Id: products_discontinued.php
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

  require('includes/application_top.php');

  tep_db_query("update " . TABLE_PRODUCTS . " set products_date_available = '' where to_days(now()) > to_days(products_date_available)");


// Below changes the status of new items
  if(isset($HTTP_GET_VARS['action']) || isset($HTTP_POST_VARS['action']))
  	{
	  if($HTTP_GET_VARS['action'] == 'setflag')
		{
		  if($HTTP_GET_VARS['flag'] == 0)
		    {
			  $pID = $HTTP_GET_VARS['pID'];
			  tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '0' where products_id = $pID");
			}
		  if($HTTP_GET_VARS['flag'] == 1)
		  	{
			  $pID = $HTTP_GET_VARS['pID'];
			  tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '1' where products_id = $pID");
			}
		}
	  if($HTTP_POST_VARS['action'] == 'update_markup')
	    {
			// Begin round-off-flag-check
			  $round_off_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'ROUNDOFF_FLAG'");
			  $round_off_check = tep_db_fetch_array($round_off_query);
			// End round-off-flag-check
		  for($q=0;$q<20;$q++)
		    {
			  $product_id = $HTTP_POST_VARS['product_id_'.$q];
			  $product_base_price = $HTTP_POST_VARS['product_base_price_'.$q];
			  $product_markup = $HTTP_POST_VARS['product_markup_'.$q];
			  $product_markup_type = $HTTP_POST_VARS['product_markup_type_'.$q];
			  
			  if($HTTP_POST_VARS['product_id_'.$q] != '')
			    {
				  if($product_markup_type == "true")
				    {
					  $product_markup_type = '%';
					  $markup = $product_markup.$product_markup_type;
					  $new_price = ($product_base_price * ('1.'.$product_markup));
					}
				  else 
				    {
					  $product_markup_type = '';
					  $markup = $product_markup.$product_markup_type;
					  $new_price = $product_base_price + $product_markup;
					}
				  if($round_off_check['configuration_value'] == 1)
				    {
						$string = $new_price;
						$strings = explode('.', $string);
						$string_2 = $strings[0].'.9900';
						$new_price = $string_2;
					}
					
				  tep_db_query("UPDATE products SET markup='".$markup."', products_price='".$new_price."' WHERE products_id='".$product_id."'");
				}
			}
	    }
	}

?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
         
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
<!-- body_text //-->
    <td>
     <table class="table table-bordered table-hover">
        <tr>
          <td>
            <table class="table table-bordered table-hover">
              <tr>
                <td>Products that have been Discontinued within last 30 days</td>
                <td align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <table class="table table-bordered table-hover">
              <tr>
                <td>
                  <table class="table table-bordered table-hover">
                    <tr>
                      <td>Products</td>
                      <td>Date Discontinued</td>
                      <td>Status</td>
					  <td>Category</td>
                      <td>Base Price</td>
                      <td>Markup</td>
                      <td>Fixed/%</td>
                      <td>Price</td>
                    </tr>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" name="action" value="update_markup" />
<?php
// Show Products from the last 30 days
  $compare_date = date("Y-m-d", time()-(((60*60)*24)*(30)));
  
  $products_query_raw = "select pd.products_id, pd.products_name, p.products_date_available, p.products_date_added, p.products_status, cd.categories_name, p.markup, p.base_price, p.products_price from " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p LEFT JOIN products_to_categories p2c ON p.products_id = p2c.products_id LEFT JOIN categories_description cd ON p2c.categories_id = cd.categories_id where p.products_id = pd.products_id and p.products_status='0' and p.products_date_disabled >= '" . $compare_date . "' and pd.language_id = '" . (int)$languages_id . "' order by p.products_date_disabled";
//	Old Query
//	$products_query_raw = "select pd.products_id, pd.products_name, p.products_date_available from " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p where p.products_id = pd.products_id and p.products_date_available != '' and pd.language_id = '" . (int)$languages_id . "' order by p.products_date_available DESC";
  $x=0;
  $products_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_query_raw, $products_query_numrows);
  $products_query = tep_db_query($products_query_raw);
  while ($products = tep_db_fetch_array($products_query))
    {
      if ((!isset($HTTP_GET_VARS['pID']) || (isset($HTTP_GET_VARS['pID']) && ($HTTP_GET_VARS['pID'] == $products['products_id']))) && !isset($pInfo))
	    { $pInfo = new objectInfo($products); }
?>
					<input type="hidden" name="product_id_<?php echo $x ?>" value="<?php echo $products['products_id'] ?>" />
                    <input type="hidden" name="product_base_price_<?php echo $x ?>" value="<?php echo $products['base_price'] ?>" />
					  <tr class="dataTableRow">
                        <td class="dataTableContent"><a style="color: #222" href="categories.php?pID=<?php echo $products['products_id'] ?>&action=new_product"><?php echo $products['products_name']; ?></a></td>
                        <td class="dataTableContent" align="center"><?php echo tep_date_short($products['products_date_disabled']); ?></td>
                        <td class="dataTableContent" align="center">

<?php			if ($products['products_status'] == '1')
                  {
                    echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link('products_discontinued.php', 'action=setflag&flag=0&pID=' . $products['products_id'] . '&page=' . $HTTP_GET_VARS['page']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                  }
				else
				  {
                    echo '<a href="' . tep_href_link('products_discontinued.php', 'action=setflag&flag=1&pID=' . $products['products_id'] . '&page=' . $HTTP_GET_VARS['page']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                  }
?>
					    </td>
                        <td align="left"><?php echo $products['categories_name'];?></td>
					    <td align="right"><?php echo $products['base_price'];?></td>
                        <td align="center">*
<?php
				$subject = $products['markup'];
				$pattern = '/%/';
				if(preg_match($pattern, $subject, $matches))
					echo "<input type='text' name='product_markup_".$x."' value='".str_replace("%","",$products['markup'])."' style='width: 40px' />";
				else
					echo "<input type='text' name='product_markup_".$x."' value='".$products['markup']."' style='width: 40px' />";
?>
						</td>
                        <td>
                        <span style="font-size: 14px; font-family: Verdana, Geneva, sans-serif">
<?php
				if(preg_match($pattern, $subject, $matches))
				  {
					echo "%<input type='radio' name='product_markup_type_".$x."' value='true' checked />";
                    echo "$<input type='radio' name='product_markup_type_".$x."' value='false'  />";
				  }
				else
				  {
					echo "%<input type='radio' name='product_markup_type_".$x."' value='true' />";
                    echo "$<input type='radio' name='product_markup_type_".$x."' value='false' checked />";
				  }
?>
						</span>
                        </td>
					    <td align="right"><?php echo $products['products_price'];?></td>
                      </tr>

<?php
	  $x++;
	}
?>
                    <tr>
                      <td colspan="3">
                        <table class="table table-bordered table-hover">
                          <tr>
                            <td><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS_DISCONTINUED); ?></td>
                            <td align="right"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>
                          </tr>
                        </table>
                      </td>
					  <td colspan="5">
                        <table class="table table-bordered table-hover">
                          <tr>
                            <td align="right">
		                      <input border="0" type="image" title="Update" alt="Update" src="includes/languages/english/images/buttons/button_update_b.gif" />
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    </form>
                  </table>
                  <table class="table table-bordered table-hover">
                    <tr>
                      <td align="left">* (Only numbers, don't include '$' or '%')</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
<!-- body_text_eof //-->
  </tr>
</table>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>