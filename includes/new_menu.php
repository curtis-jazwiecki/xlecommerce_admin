<?php

/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.


new_menu.php



Replacing the column_left.php with new css dropdown menu



*/



$selected_box = $_GET['selected_box'];
$administrator=array('admin_account.php','admin_members.php','admin_files.php','admin_file_access.php','admin_ip_record.php');$catalog=array('categories.php','categories_frontend.php','easypopulate.php','manufacturers.php','reviews.php','category_specials.php','specials.php','price_updater.php','products_expected.php','products_discontinued.php','featured.php','featured-manufacturers.php','xsell.php','googlesitemap.php','googlefeeder.php');$configuration=array('configuration.php','avalara.php');$store_info=array('customers_groups.php','orders.php','coupon_admin.php','currencies.php','languages.php','orders_status.php','countries.php','zones.php','geo_zones.php','tax_classes.php','tax_rates.php','customers_points.php','customers_points_pending.php','customers_points_referral.php');$info_mgr=array('information_manager.php','header_tags_controller.php','header_tags_english.php','header_tags_fill_tags.php','edit_templates.php','layout_manager.php','modules_boxes.php','modules_boxes.php','modules_boxes_layout.php','select_template.php','select_category_template.php');$module=array('modules.php','email_template.php','fraud_prevention.php','orders_POS.php','create_order_process_POS.php','ranges_manager.php','lanes_manager.php','range_operations.php','inventory_import.php','mobilesite.php','fb-store_manage.php','vendors.php','products_to_vendor.php','prods_by_vendor.php','orders_by_vendor.php','move_vendor_prods.php','product_per_day.php');$reports=array('advanced_stats.php','stats_products_viewed.php','stats_products_purchased.php','stats_customers.php','stats_sales_report.php','stats_credits.php','sales_report_export.php','stats_wishlists.php','marketing_feeds.php','conversion_rate_report.php');$tools=array('webmail.php','amazon_feeds_manager.php','ebay_feeds_manager.php','banner_manager.php','file_manager.php','mail.php','newsletters.php','whos_online.php','qbi_create.php','stats_keywords.php','user_info_export.php','import_avatax_codes.php');$training_support=array('training_videos.php','support_ticket.php','training_videos.php');
$filename=basename($_SERVER['PHP_SELF']);

?>

<?php // Below is for the dropdown menu ?>

<script type="text/javascript">

startList = function() {

if (document.all&&document.getElementById) {

navRoot = document.getElementById("nav");

for (i=0; i<navRoot.childNodes.length; i++) {

node = navRoot.childNodes[i];

if (node.nodeName=="LI") {

node.onmouseover=function() {

this.className+=" over";

  }

  node.onmouseout=function() {

  this.className=this.className.replace(" over", "");

   }

   }

  }

 }

}

window.onload=startList;

</script>
<script type="text/jscript">
$(function(){
  function stripTrailingSlash(str) {
    if(str.substr(-1) == '/') {
      return str.substr(0, str.length - 1);
    }
    return str;
  }

  var url = window.location.pathname;  
  var activePage = stripTrailingSlash(url);

  $('.nav li a').each(function(){  
    var currentPage = stripTrailingSlash($(this).attr('href'));

    if (activePage == currentPage) {
      $(this).parent().addClass('active'); 
    } 
  });
});
</script>
         <nav class="sidebar">
            <ul class="nav">
               <!-- START user info-->
               <li>
                  <div data-toggle="collapse-next" class="item user-block has-submenu">
                     <!-- User picture-->
                     <div class="user-block-picture">
                        <img src="http://v6copy1.obndemo.com/images/store_logo.png" alt="Avatar" style="width:40px; height:40px;" class="img-thumbnail img-circle">
                        <!-- Status when collapsed-->
                        <div class="user-block-status">
                           <div class="point point-success point-lg"></div>
                        </div>
                     </div>
                     <!-- Name and Role-->
                     <div class="user-block-info">
                        <span class="user-block-name item-text">Welcome, <?php echo STORE_ADMIN_NAME; ?></span>
                        <!--<span class="user-block-role">Designer</span>-->
                        <!-- START Dropdown to change status-->
                        <div class="btn-group user-block-status">
                           <button type="button" data-toggle="dropdown" data-play="fadeIn" data-duration="0.2" class="btn btn-xs dropdown-toggle">
                              <div class="point point-success"></div>Online</button>
                           <!--<ul class="dropdown-menu text-left pull-right">
                              <li>
                                 <a href="#">
                                    <div class="point point-success"></div>Online</a>
                              </li>
                              <li>
                                 <a href="#">
                                    <div class="point point-warning"></div>Away</a>
                              </li>
                              <li>
                                 <a href="#">
                                    <div class="point point-danger"></div>Busy</a>
                              </li>
                           </ul>-->
                        </div>
                        <!-- END Dropdown to change status-->
                     </div>
                  </div>
                  <!-- START User links collapse-->
                 <!-- <ul class="nav collapse">
                     <li class="divider"></li>
                     <li><a href="#">Logout</a>
                     </li>
                  </ul>-->
                  <!-- END User links collapse-->
               </li>
               <!-- END user info-->
               <!-- START Menu-->
               <li>
                  <?php echo '<a href="'.HTTP_SERVER.DIR_WS_ADMIN.'"  title="Home"  data-toggle="" class="no-submenu">
                     <em class="fa fa-home"></em>
                     <span class="item-text">Home</span>
                  </a>'; ?>
               </li>
               <li>
               <?php echo '<a href="'.FILENAME_ADMIN_MEMBERS.'?selected_box=administrator"  title="Administrator" data-toggle="collapse-next" class="has-submenu">
                     <em class="fa fa-dashboard"></em>
                     <span class="item-text">'.BOX_HEADING_ADMINISTRATOR.'</span>
                  </a>'; ?>
                  <!-- START SubMenu item-->
                  <ul class="nav collapse <?php if(in_array($filename,$administrator)){ echo "in";} ?>">
                  <li>
                     <?php echo '<a href="' . tep_href_link(FILENAME_ADMIN_ACCOUNT, '', 'SSL') . '" class="headerLink"  title="Members" data-toggle=""  class="no-submenu">
                        <span class="item-text">' . HEADER_TITLE_ACCOUNT . '</span>
                        </a>'; ?>
                     </li>
                     <li>
                     <?php echo '<a href="'.FILENAME_ADMIN_MEMBERS.'?selected_box=administrator"  title="Members" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_ADMINISTRATOR_MEMBERS.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_ADMIN_FILES.'?selected_box=administrator"  title="File Access" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_ADMINISTRATOR_BOXES.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                     <?php echo '<a href="admin_file_access.php?selected_box=administrator" title="User Access" data-toggle="" class="no-submenu">
                        <span class="item-text">User Access</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_ADMIN_IP_RECORD.'" title="Admin Login Details" data-toggle="" class="no-submenu">
                        <span class="item-text">Admin Login Details</span>
                     </a>'; ?>
                     </li>
                  </ul>
                  <!-- END SubMenu item-->
               </li>
               <li>
               <?php echo '<a href="'.FILENAME_CATEGORIES.'?selected_box=catalog" title="Catalog" data-toggle="collapse-next" class="has-submenu">
                     <em class="fa fa-cogs"></em>
                     <span class="item-text">'.BOX_HEADING_CATALOG.'</span>
                  </a>'; ?>
                  <!-- START SubMenu item-->
                  <ul class="nav collapse <?php if(in_array($filename,$catalog)){ echo "in";} ?>">
                     <li>
                       <?php echo '<a href="categories.php?selected_box=catalog"  title="Categories/Products" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CATALOG_CATEGORIES_PRODUCTS.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="categories_frontend.php?selected_box=catalog" title="Frontend categories" data-toggle="" class="no-submenu">
                        <span class="item-text">Frontend categories</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="easypopulate.php?selected_box=catalog" title="Easy Populate" data-toggle="" class="no-submenu">
                        <span class="item-text">Easy Populate</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_MANUFACTURERS.'?selected_box=catalog" title="Manufacturers" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CATALOG_MANUFACTURERS.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_REVIEWS.'?selected_box=catalog" title="Reviews" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CATALOG_REVIEWS.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_CATEGORY_SPECIALS.'?selected_box=catalog" title="Special Categories" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CATALOG_CATEGORY_SPECIALS.'</span>
                     </a>'; ?>
                     </li>
                      <li>
                        <?php echo '<a href="'.FILENAME_SPECIALS.'?selected_box=catalog" title="Specials" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CATALOG_SPECIALS.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="price_updater.php?selected_box=catalog" title="Quick Price Update" data-toggle="" class="no-submenu">
                        <span class="item-text">Quick Price Update</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_PRODUCTS_EXPECTED.'?selected_box=catalog" title="New Products Added" data-toggle="" class="no-submenu">
                        <span class="item-text">New Products Added</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.'products_discontinued.php'.'?selected_box=catalog" title="Products Discontinued" data-toggle="" class="no-submenu">
                        <span class="item-text">Products Discontinued</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="#" id="range" title="Administrator" data-toggle="collapse-next" class="has-submenu">
                        <span class="item-text">Featured Products</span>
                     </a>'; ?>
                       <ul>
                         <li>
                        <?php echo '<a href="'.FILENAME_FEATURED.'?selected_box=catalog" title="Featured Products" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CATALOG_FEATURED_PRODUCTS.'</span>
                        </a>'; ?>
                        </li>
                        <li>
                        <?php echo '<a href="'.FILENAME_FEATURED.'?selected_box=catalog&featured_group=1" title="Group 1" data-toggle="" class="no-submenu">
                        <span class="item-text">Group 1</span>
                        </a>'; ?>
                        </li>
                        <li>
                        <?php echo '<a href="'.FILENAME_FEATURED.'?selected_box=catalog&featured_group=2" title="Group 2" data-toggle="" class="no-submenu">
                        <span class="item-text">Group 2</span>
                        </a>'; ?>
                        </li>
                        <li>
                        <?php echo '<a href="'.FILENAME_FEATURED.'?selected_box=catalog&featured_group=3" title="Group 3" data-toggle="" class="no-submenu">
                        <span class="item-text">Group 3</span>
                        </a>'; ?>
                        </li>
                       </ul>
                     </li>
                     <li>
                        <?php echo '<a href="featured-manufacturers.php" title="Featured Manufacturers" data-toggle="" class="no-submenu">
                        <span class="item-text">Featured Manufacturers</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_XSELL_PRODUCTS.'?selected_box=catalog" title="Related Products" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CATALOG_XSELL_PRODUCTS.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_GOOGLESITEMAP.'?selected_box=catalog" title="Google XML Sitemap" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CATALOG_GOOGLESITEMAP.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="googlefeeder.php?noftp=1&selected_box=catalog" title="Fire Google Feed (No FTP)" data-toggle="" class="no-submenu">
                        <span class="item-text">Fire Google Feed (No FTP)</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="googlefeeder.php?selected_box=catalog" title="Fire Google Feed (Over FTP)" data-toggle="" class="no-submenu">
                        <span class="item-text">Fire Google Feed (Over FTP)</span>
                        </a>'; ?>
                     </li>
                  </ul>
                  <!-- END SubMenu item-->
               </li>
               
               
               
               
               
               <?php // Configuration Section

	  // If top admin, show all config menus

		if($login_groups_id == 1)

		  {

			  echo '<li><a href="'.FILENAME_CONFIGURATION.'?gID=75?selected_box=configuration"  title="Configuration" data-toggle="collapse-next" class="has-submenu">
                     <em class="fa fa-wrench"></em>
                     <span class="item-text">'.BOX_HEADING_CONFIGURATION.'</span>
                  </a>'; $configuration_val="";
if(in_array($filename,$configuration)){ $configuration_val="in"; }
			  echo '<ul class="nav collapse '. $configuration_val .'">';

			  $cfg_groups = '';

			  $configuration_groups_query = tep_db_query("select configuration_group_id as cgID, configuration_group_title as cgTitle from " . TABLE_CONFIGURATION_GROUP . " where visible = '1' order by sort_order");

			  while ($configuration_groups = tep_db_fetch_array($configuration_groups_query))



			    { echo '<li><a href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $configuration_groups['cgID'], 'NONSSL') . '" title="Fire Google Feed (Over FTP)" data-toggle="" class="no-submenu">
                     <span class="item-text">' . $configuration_groups['cgTitle'] . '</span>
                  </a></li>'; }

			//  echo '<li class="block"><a href="' . tep_href_link("configuration_frontend.php", 'gID=' . $configuration_groups['cgID'], 'NONSSL') . '">Frontend Categories</a></li>';
			
			echo '<li><a href="' . tep_href_link("store_logo.php",'', 'NONSSL') . '" title="Fire Google Feed (Over FTP)" data-toggle="" class="no-submenu">
                        <span class="item-text">Store Logo</span>
                        </a></li>';
						
			// added on 05-05-2016 #start
			
			echo '<li><a href="' . tep_href_link(FILENAME_AVALARA_CONFIGURATION,'', 'NONSSL') . '" title="Avalara Configuration" data-toggle="" class="no-submenu"><span class="item-text">Avalara Configuration</span></a></li>';
			
			// added on 05-05-2016 #ends		

	          echo '  </ul>';

	          echo '</li>';

		  }else{
			  

			  echo '<li><a href="'.FILENAME_CONFIGURATION.'?gID=75?selected_box=configuration"  title="Configuration" data-toggle="collapse-next" class="has-submenu">
                     <em class="fa fa-wrench"></em>
                     <span class="item-text">'.BOX_HEADING_CONFIGURATION.'</span>
                  </a>';

			  echo '<ul class="nav collapse '. $configuration_val .'">';

			  $cfg_groups = '';

			  $configuration_groups_query = tep_db_query("select configuration_group_id as cgID, configuration_group_title as cgTitle from " . TABLE_CONFIGURATION_GROUP . " where visible = '1' order by sort_order");

			  while ($configuration_groups = tep_db_fetch_array($configuration_groups_query))

			    {

				  if(ADMIN_LEVEL_OF_SERVICE == "gold")

				    {

					  if($configuration_groups['cgTitle'] == "My Store Options" || $configuration_groups['cgTitle'] == "Stock" || $configuration_groups['cgTitle'] == "MultiGeoZone MultiTable Shipping" || $configuration_groups['cgTitle'] == "Featured" || $configuration_groups['cgTitle'] == "XML Feed" || $configuration_groups['cgTitle'] == "Customer Details" || $configuration_groups['cgTitle'] == "Shipping/Packaging")

					    {

                    	 echo '<li><a href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $configuration_groups['cgID'], 'NONSSL') . '"  title="Configuration" data-toggle="collapse-next" class="has-submenu">
                     <em class="fa fa-wrench"></em>
                     <span class="item-text">' . $configuration_groups['cgTitle'] . '</span>
                  </a></li>';



						}

					}

				  elseif(ADMIN_LEVEL_OF_SERVICE == "ultimate")

				    {

					  if($configuration_groups['cgTitle'] == "My Store Options" || $configuration_groups['cgTitle'] == "Stock" || $configuration_groups['cgTitle'] == "Compare products side-by-side" ||  $configuration_groups['cgTitle'] == "MultiGeoZone MultiTable Shipping" || $configuration_groups['cgTitle'] == "Featured"  || $configuration_groups['cgTitle'] == "XML Feed" || $configuration_groups['cgTitle'] == "Customer Details" || $configuration_groups['cgTitle'] == "Shipping/Packaging"  || $configuration_groups['cgTitle'] == "Google XML Sitemap" || $configuration_groups['cgTitle'] == "Google Analytics" || $configuration_groups['cgTitle'] == "Social Bookmarks" ||  $configuration_groups['cgTitle'] == "Constant Contact")

					    {



                    	  echo '<li><a href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $configuration_groups['cgID'], 'NONSSL') . '"  title="Configuration" data-toggle="collapse-next" class="has-submenu">
                     <em class="fa fa-wrench"></em>
                     <span class="item-text">' . $configuration_groups['cgTitle'] . '</span>
                  </a></li>';



						}

					}

				}

	          echo '  </ul>';

	          echo '</li>';

		  
		  }?>
               
                  <!-- END SubMenu item-->
               <li>
                 <?php echo '<a href="'.FILENAME_COUPON_ADMIN.'?selected_box=gv_admin" title="Store Information" data-toggle="collapse-next" class="has-submenu">
                     <em class="fa fa-file-text"></em>
                     <span class="item-text">Store Information</span>
                  </a>'; ?>
                  <!-- START SubMenu item-->
                  <ul class="nav collapse <?php if(in_array($filename,$store_info)){ echo "in";} ?>">
                     <li>
                        <?php echo '<a href="'.FILENAME_CUSTOMERS.'?selected_box=customers" title="Customers" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CUSTOMERS_CUSTOMERS.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="' . tep_href_link('customers_groups.php', '', 'NONSSL') . '" title="Customers Groups" data-toggle="" class="no-submenu">
                        <span class="item-text">' . BOX_CUSTOMERS_GROUPS . '</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_ORDERS.'?selected_box=customers" title="Orders" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CUSTOMERS_ORDERS.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_COUPON_ADMIN.'?selected_box=gv_admin" title="Coupon Admin" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_COUPON_ADMIN.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_CURRENCIES.'?selected_box=localization" title="Currencies" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_LOCALIZATION_CURRENCIES.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_LANGUAGES.'?selected_box=localization" title="Languages" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_LOCALIZATION_LANGUAGES.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_ORDERS_STATUS.'?selected_box=localization" title="Orders Status" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_LOCALIZATION_ORDERS_STATUS.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_COUNTRIES.'?selected_box=taxes" title="Countries" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_TAXES_COUNTRIES.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_ZONES.'?selected_box=taxes" title="Zones" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_TAXES_ZONES.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_GEO_ZONES.'?selected_box=taxes" title="Tax Zones" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_TAXES_GEO_ZONES.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_TAX_CLASSES.'?selected_box=taxes" title="Tax Classes" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_TAXES_TAX_CLASSES.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_TAX_RATES.'?selected_box=taxes" title="Tax Rates" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_TAXES_TAX_RATES.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="' . tep_href_link(FILENAME_CUSTOMERS_POINTS, '', 'NONSSL') . '" title="Customers Points" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CUSTOMERS_POINTS.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="' . tep_href_link(FILENAME_CUSTOMERS_POINTS_PENDING, '', 'NONSSL') . '" title="Pending Points" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CUSTOMERS_POINTS_PENDING.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="' . tep_href_link(FILENAME_CUSTOMERS_POINTS_REFERRAL, '', 'NONSSL') . '" title="Referral Points" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CUSTOMERS_POINTS_REFERRAL.'</span>
                        </a>'; ?>
                     </li>
                  </ul>
                  <!-- END SubMenu item-->
               </li>
               <!-- END SubMenu item-->
               
               <?php  echo '<li><a href="'.FILENAME_INFORMATION_MANAGER.'?selected_box=information"  title="Info manager" data-toggle="collapse-next" class="has-submenu">
                     <em class="fa fa-info"></em>
                     <span class="item-text">'.BOX_HEADING_INFORMATION.'</span>
                  </a>'; 
$info_mgr_val="";if(in_array($filename,$info_mgr)){ $info_mgr_val="in"; }
			  echo '<ul class="nav collapse '.$info_mgr_val.'">';

			$info_groups = '';

			$information_groups_query = tep_db_query("select information_group_id as igID, information_group_title as igTitle from " . TABLE_INFORMATION_GROUP . " where visible = '1' order by sort_order");

			while ($information_groups = tep_db_fetch_array($information_groups_query))

			  {

				if(ADMIN_LEVEL_OF_SERVICE == "gold")

				  {

					if($information_groups['igTitle'] == "Information pages" || $information_groups['igTitle'] == "Define Mainpage" || $information_groups['igTitle'] == "Define Shop Page")

					  { 
                      
                      
echo '<li><a href=' . tep_href_link(FILENAME_INFORMATION_MANAGER, 'gID=' . $information_groups['igID'], 'NONSSL') . '" title="Info manager" data-toggle="" class="no-submenu">
                     <span class="item-text">' . $information_groups['igTitle'] . '</span>
                  </a></li>';
                  
                  

					  }

				  }

				if(ADMIN_LEVEL_OF_SERVICE == "ultimate")

				  {

					if($information_groups['igTitle'] == "Information pages" || $information_groups['igTitle'] == "Define Mainpage" || $information_groups['igTitle'] == "Department Pages" || $information_groups['igTitle'] == "Define Shop Page" || $information_groups['igTitle'] == "Hidden Pages")

					  { 
                      
echo '<li><a href="' . tep_href_link(FILENAME_INFORMATION_MANAGER, 'gID=' . $information_groups['igID'], 'NONSSL') . '" title="Info manager" data-toggle="" class="no-submenu">
                     <span class="item-text">' . $information_groups['igTitle'] . '</span>
                  </a></li>';
                  

					  }

				  }

			  }
			  
              echo '<li><a href="'.FILENAME_HEADER_TAGS_CONTROLLER.'?selected_box=header_tags" title="Page Control" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_HEADER_TAGS_ADD_A_PAGE.'</span>
              </a></li>';
			  
			  echo '<li><a href="'.FILENAME_HEADER_TAGS_ENGLISH.'?selected_box=header_tags" title="Text Control" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_HEADER_TAGS_ENGLISH.'</span>
              </a></li>';
			  
			  echo '<li><a href="'.FILENAME_HEADER_TAGS_FILL_TAGS.'?selected_box=header_tags" title="Fill Tags" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_HEADER_TAGS_FILL_TAGS.'</span>
              </a></li>';
			  
			  
			  
			  echo '<li><a href="#"  title="Manage Templates" data-toggle="collapse-next" class="has-submenu">
                     <span class="item-text">Manage Templates</span>
                  </a>'; 

			  echo '<ul>';	
	
	              echo '<li><a href="edit_templates.php?selected_box=header_tags" title="Edit Templates" data-toggle="" class="no-submenu">
                     <span class="item-text">Edit Templates</span>
                  </a></li>';
				  
				  echo '<li><a href="layout_manager.php" title="Layout Manager" data-toggle="" class="no-submenu">
                     <span class="item-text">Layout Manager</span>
                  </a></li>';
				  
				   echo '<li><a href="modules_boxes.php?type=b" title="Module Boxes" data-toggle="" class="no-submenu">
                     <span class="item-text">Module Boxes</span>
                  </a></li>';
				   echo '<li><a href="modules_boxes.php?type=m" title="Template Modules" data-toggle="" class="no-submenu">
                     <span class="item-text">Template Modules</span>
                  </a></li>';
				   echo '<li><a href="modules_boxes_layout.php?type=b" title="Template Layout Boxes" data-toggle="" class="no-submenu">
                     <span class="item-text">Template Layout Boxes</span>
                  </a></li>';
				   echo '<li><a href="modules_boxes_layout.php?type=b" title="Template Layout Modules" data-toggle="" class="no-submenu">
                     <span class="item-text">Template Layout Modules</span>
                  </a></li>';
				  
	          echo '  </ul>';
	          echo '</li>';	
			  
			  echo '<li><a href="select_template.php?select_box=header_tags" title="Product Listing Template" data-toggle="" class="no-submenu">
                        <span class="item-text">Product Listing Template</span>
              </a></li>';
			  
			  echo '<li><a href="select_category_template.php?select_box=header_tags" title="Category Listing Template" data-toggle="" class="no-submenu">

                        <span class="item-text">Category Listing Template</span>
              </a></li>';
			  			  
			  echo '  </ul>';
 	          echo '</li>';
			  ?>
               <!-- END SubMenu item-->
               
             <li>
               <?php echo '<a href="'.FILENAME_MODULES.'?set=payment&selected_box=modules" title="Modules" data-toggle="collapse-next" class="has-submenu">
                     <em class="fa fa-folder-open"></em>
                     <span class="item-text">'.BOX_HEADING_MODULES.'</span>
                  </a>'; ?>
                  <!-- START SubMenu item-->
                  <ul class="nav collapse <?php if(in_array($filename,$module)){ echo "in";} ?>">
                     <li>
                       <?php echo '<a href="'.FILENAME_MODULES.'?set=payment"  title="Payment" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_MODULES_PAYMENT.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_MODULES.'?set=shipping" title="Shipping" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_MODULES_SHIPPING.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_MODULES.'?set=ordertotal" title="Order Total" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_MODULES_ORDER_TOTAL.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="#" title="Manufacturers" data-toggle="" class="no-submenu">
                        <span class="item-text">Feeds</span>
                     </a>'; ?>
                         <ul>
                             <li>
                               <?php echo '<a href="'.FILENAME_MODULES.'?set=amazon"  title="Amazon" data-toggle="" class="no-submenu">
                                <span class="item-text">Amazon</span>
                             </a>'; ?>
                             </li>
                             <li>
                               <?php echo '<a href="'.FILENAME_MODULES.'?set=ebay" title="eBay" data-toggle="" class="no-submenu">
                                <span class="item-text">eBay</span>
                             </a>'; ?>
                             </li>
                             <li>
                               <?php echo '<a href="'.FILENAME_MODULES.'?set=googlefeeder" title="Google Feeder" data-toggle="" class="no-submenu">
                                <span class="item-text">Google Feeder</span>
                             </a>'; ?>
                             </li>
                         </ul>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_EMAIL_TEMPLATE.'" title="Email Templates" data-toggle="" class="no-submenu">
                        <span class="item-text">Email Templates</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="fraud_prevention.php" title="Fraud Prevention" data-toggle="" class="no-submenu">
                        <span class="item-text">Fraud Prevention</span>
                     </a>'; ?>
                     </li>
                     
                    <li>
                        <?php echo '<a href="orders_POS.php" title="Store POS" data-toggle="" class="no-submenu">
                        <span class="item-text">Store POS</span>
                     </a>'; ?>
                         <ul>
                             <li>
                                <?php echo '<a href="orders_POS.php" title="POS Orders Listing" data-toggle="" class="no-submenu">
                                <span class="item-text">POS Orders Listing</span>
                             </a>'; ?>
                             </li>
                             <li>
                                <?php echo '<a href="create_order_process_POS.php" title="POS" data-toggle="" class="no-submenu">
                                <span class="item-text">POS</span>
                             </a>'; ?>
                             </li>
                         </ul>
                    <!-- <li>
                        <?php /*echo '<a href="'.FILENAME_RANGES_MANAGER.'?selected_box=catalog" title="Range Manager" data-toggle="" class="no-submenu">
                        //<span class="item-text">'.BOX_CATALOG_RANGEMANAGER.'</span>
                     </a>'; */?>
                     </li>-->
                     <li>
                        <?php echo '<a href="'.FILENAME_RANGES_MANAGER.'?selected_box=catalog" title="Range Manager" data-toggle="collapse-next" class="has-submenu">
                        <span class="item-text">'.BOX_CATALOG_RANGEMANAGER.'</span>
                     </a>'; ?>
                       <ul>
                         <li>
                        <?php echo '<a href="'.FILENAME_RANGES_MANAGER.'?selected_box=catalog" title="Range Manager" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CATALOG_RANGEMANAGER.'</span>
                        </a>'; ?>
                        </li>
                        <li>
                        <?php echo '<a href="'.FILENAME_LANES_MANAGER.'?selected_box=catalog" title="Lanes Manager" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CATALOG_LANESMANAGER.'</span>
                        </a>'; ?>
                        </li>
                        <li>
                        <?php echo '<a href="range_operations.php?selected_box=catalog" title="Ranges POS" data-toggle="" class="no-submenu">
                        <span class="item-text">Ranges POS</span>
                        </a>'; ?>
                        </li>
                       </ul>
                     </li>
                     <li>
                        <?php echo '<a href="inventory_import.php" title="Inventory Import" data-toggle="" class="no-submenu">
                        <span class="item-text">Inventory Import</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="mobilesite.php" title="Mobile Site" data-toggle="" class="no-submenu">
                        <span class="item-text">Mobile Site</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_FACEBOOKSTORE.'" title="Facebook Store" data-toggle="" class="no-submenu">
                        <span class="item-text">Facebook Store</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_VENDORS.'" title="Vendors Manager)" data-toggle="" class="no-submenu">
                        <span class="item-text">Vendors Manager</span>
                        </a>'; ?>
                          <ul>
                             <li>
                            <?php echo '<a href="'.FILENAME_VENDORS.'" title="Vendors Manager" data-toggle="" class="no-submenu">
                            <span class="item-text">Vendors Manager</span>
                            </a>'; ?>
                            </li>
                            <li>
                            <?php echo '<a href="products_to_vendor.php" title="Map Products with Vendor" data-toggle="" class="no-submenu">
                            <span class="item-text">Map Products with Vendor</span>
                            </a>'; ?>
                            </li>
                            <li>
                            <?php echo '<a href="'.FILENAME_PRODS_VENDORS.'" title="Mapped Products Listing" data-toggle="" class="no-submenu">
                            <span class="item-text">Mapped Products Listing</span>
                            </a>'; ?>
                            </li>
                            <li>
                            <?php echo '<a href="'.FILENAME_MOVE_VENDORS.'" title="Move Products between Vendors" data-toggle="" class="no-submenu">
                            <span class="item-text">' . BOX_MOVE_VENDOR_PRODS . '</span>
                            </a>'; ?>
                            </li>
                         </ul>
                     </li>
                     <li>
                        <?php echo '<a href="product_per_day.php" title="Product per Day Scheduler" data-toggle="" class="no-submenu">
                        <span class="item-text">Product per Day Scheduler</span>
                        </a>'; ?>
                     </li>
                  </ul>
                  <!-- END SubMenu item-->
               </li>
               
                
             <li>
               <?php echo '<a href="advanced_stats.php?selected_box=reports" title="Reports" data-toggle="collapse-next" class="has-submenu">
                     <em class="fa fa-pencil"></em>
                     <span class="item-text">'.BOX_HEADING_REPORTS.'</span>
                  </a>'; ?>
                  <!-- START SubMenu item-->
                  <ul class="nav collapse <?php if(in_array($filename,$reports)){ echo "in";} ?>">
                     <li>
                     <?php echo '<a href="advanced_stats.php" title="Advanced Stats" data-toggle="" class="no-submenu">
                        <span class="item-text">Advanced Stats</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_STATS_PRODUCTS_VIEWED.'" title="Products Viewed" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_REPORTS_PRODUCTS_VIEWED.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                     <?php echo '<a href="'.FILENAME_STATS_PRODUCTS_PURCHASED.'" title="Products Purchased" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_REPORTS_PRODUCTS_PURCHASED.'</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_STATS_CUSTOMERS.'" title="Customer Orders-Total" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_REPORTS_ORDERS_TOTAL.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_STATS_SALES_REPORT.'" title="Sales Report" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_REPORTS_SALES_REPORT.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_STATS_CREDITS.'" title="Current Customer Assets" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_REPORTS_CREDITS.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="sales_report_export.php" title="Sales Report Export" data-toggle="" class="no-submenu">
                        <span class="item-text">Sales Report Export</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="stats_wishlists.php" title="Customer Wishlists" data-toggle="" class="no-submenu">
                        <span class="item-text">Customer Wishlists</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="marketing_feeds.php" title="Configurable Marketing Feeds" data-toggle="" class="no-submenu">
                        <span class="item-text">Configurable Marketing Feeds</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="conversion_rate_report.php" title="Conversion Rate Report" data-toggle="" class="no-submenu">
                        <span class="item-text">Conversion Rate Report</span>
                     </a>'; ?>
                     </li>

                  </ul>
                  <!-- END SubMenu item-->
               </li>
                
             <li>
               <?php echo '<a href="webmail.php?selected_box=tools" title="Tools" data-toggle="collapse-next" class="has-submenu">
                     <em class="fa fa-magic"></em>
                     <span class="item-text">'.BOX_HEADING_TOOLS.'</span>
                  </a>'; ?>
                  <!-- START SubMenu item-->
                  <ul class="nav collapse <?php if(in_array($filename,$tools)){ echo "in";} ?>">
                     <li>
                     <?php echo '<a href="webmail.php" title="Webmail" data-toggle="" class="no-submenu">
                        <span class="item-text">Webmail</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="amazon_feeds_manager.php" title="Amazon Feeds Manager" data-toggle="" class="no-submenu">
                        <span class="item-text">Amazon Feeds Manager</span>
                     </a>'; ?>
                     </li>
                     <li>
                     <?php echo '<a href="ebay_feeds_manager.php" title="eBay Feeds Manager" data-toggle="" class="no-submenu">
                        <span class="item-text">eBay Feeds Manager</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_BANNER_MANAGER.'" title="Banner Manager" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_TOOLS_BANNER_MANAGER.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                         <?php echo '<a href="'.FILENAME_LANGUAGE_MANAGER.'" title="File Manager" data-toggle="" class="no-submenu">
                        <span class="item-text">Language Manager</span>
                     </a>'; ?>
				     </li>
                     
                     <li>
                        <?php echo '<a href="'.FILENAME_FILE_MANAGER.'" title="File Manager" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_TOOLS_FILE_MANAGER.'</span>
                     </a>'; ?>
                     </li>
                     
                     
                     <li>
                        <?php echo '<a href="'.FILENAME_MAIL.'" title="Send Email" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_TOOLS_MAIL.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_NEWSLETTERS.'" title="Newsletter Manager" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_TOOLS_NEWSLETTER_MANAGER.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_WHOS_ONLINE.'" title="Who is Online" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_TOOLS_WHOS_ONLINE.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_QBI.'" title="Quickbooks Import QBI" data-toggle="" class="no-submenu">
                        <span class="item-text">'.BOX_CATALOG_QBI.'</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="'.FILENAME_KEYWORDS.'" title="Keyword Manager" data-toggle="" class="no-submenu">
                        <span class="item-text">Keyword Manager</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="user_info_export.php" title="User Info Export" data-toggle="" class="no-submenu">
                        <span class="item-text">User Info Export</span>
                     </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="import_avatax_codes.php" title="Import Avatax Codes" data-toggle="" class="no-submenu">
                        <span class="item-text">Import Avatax Codes</span>
                     </a>'; ?>
                     </li>
                     
                  </ul>
                  <!-- END SubMenu item-->
               </li>

              <li>
               <?php echo '<a href="training_videos.php?selected_box=training_support" title="Training/Support" data-toggle="collapse-next" class="has-submenu">
                     <em class="fa fa-thumbs-up"></em>
                     <span class="item-text">Training/Support</span>
                  </a>'; ?>
                  <!-- START SubMenu item-->
                  <ul class="nav collapse <?php if(in_array($filename,$training_support)){ echo "in";} ?>">
                     <li>
                     <?php echo '<a href="https://www.obnit.com/knowledgebase.php" title="Training Videos" data-toggle="" class="no-submenu">
                        <span class="item-text">Training Videos</span>
                        </a>'; ?>
                     </li>
                     <li>
                        <?php echo '<a href="support_ticket.php" title="Support Tickets" data-toggle="" class="no-submenu">
                        <span class="item-text">Support Tickets</span>
                     </a>'; ?>
                     </li>
                     <li>
                     <?php echo '<a href="https://www.obnit.com/knowledgebase.php" title="F.A.Q." data-toggle="" class="no-submenu">
                        <span class="item-text">F.A.Q.</span>
                        </a>'; ?>
                     </li>
                  </ul>
                  <!-- END SubMenu item-->
               </li>


               
               <!-- END Menu-->
               <!-- Sidebar footer    -->
               <?php /*?><li class="nav-footer">
                  <div class="nav-footer-divider"></div>
                  <!-- START button group-->
                  <div class="btn-group text-center">
                     <button type="button" data-toggle="tooltip" data-title="Add Contact" class="btn btn-link">
                        <em class="fa fa-user text-muted"><sup class="fa fa-plus"></sup>
                        </em>
                     </button>
                     <button type="button" data-toggle="tooltip" data-title="Settings" class="btn btn-link">
                        <em class="fa fa-cog text-muted"></em>
                     </button>
                     
                     <?php   echo '<a href="' . tep_href_link(FILENAME_LOGOFF, '', 'NONSSL') . '" class="headerLink">' . '<button type="button" data-toggle="tooltip" data-title="Logout" class="btn btn-link">' . '<em class="fa fa-sign-out text-muted"></em>' . '</button>' . '</a>'; ?>
                  </div>
                  <!-- END button group-->
               </li><?php */?>
            </ul>
         </nav>
         <!-- END Sidebar (left)-->
