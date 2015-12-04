<?php

/*



new_menu.php



Replacing the column_left.php with new css dropdown menu



*/



$selected_box = $_GET['selected_box'];



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

<style>



ul li#range ul {

    margin-left: 180px;

    margin-top: -5px;

    display:none;

}



li#range:hover ul{

    display:block;

}

</style>

<div class="wrapper_menu" style="width:100%;text-align:center; z-index: 101;">

  <div class="wrapper_menu_nav" style="width:100%;text-align:center;">

	<div class="wrapper_menu_item" style="width:100%;text-align:center;">

        <ul id="nav">

<?php

	// Home Button

	  echo '<div style="float: left; position: relative; padding: 0 5px; font-weight: bold; border: 1px #FFF;"><a href="'.HTTP_SERVER.DIR_WS_ADMIN.'">Home</a></div>';



	// Admin Section

	  echo '<li><a href="'.FILENAME_ADMIN_MEMBERS.'?selected_box=administrator">'.BOX_HEADING_ADMINISTRATOR.'</a>';

	  echo '  <ul>';

	  echo '  <li class="block"><a href="'.FILENAME_ADMIN_MEMBERS.'?selected_box=administrator">'.BOX_ADMINISTRATOR_MEMBERS.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_ADMIN_FILES.'?selected_box=administrator">'.BOX_ADMINISTRATOR_BOXES.'</a></li>';

	  echo '  <li class="block"><a href="admin_file_access.php?selected_box=administrator">User Access</a></li>';

          // #14 12Jan2014 (MA) BOF

          echo '  <li class="block"><a href="'.FILENAME_ADMIN_IP_RECORD.'">Admin Login Details</a></li>';

	  // #14 12Jan2014 (MA) EOF

          echo '  </ul>';

	  echo '</li>';

	// Catalog Section

	  echo '<li><a href="'.FILENAME_CATEGORIES.'?selected_box=catalog">'.BOX_HEADING_CATALOG.'</a>';

	  echo '  <ul>';

	  echo '  <li class="block"><a href="'.FILENAME_CATEGORIES.'?selected_box=catalog">'.BOX_CATALOG_CATEGORIES_PRODUCTS.'</a></li>';

	  echo '  <li class="block"><a href="categories_frontend.php?selected_box=catalog">Frontend categories</a></li>';

//	  echo '  <li class="block"><a href="'.FILENAME_PRODUCTS_ATTRIBUTES.'?selected_box=catalog">'.BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES.'</a></li>';

	  echo '  <li class="block"><a href="easypopulate.php?selected_box=catalog">Easy Populate</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_MANUFACTURERS.'?selected_box=catalog">'.BOX_CATALOG_MANUFACTURERS.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_REVIEWS.'?selected_box=catalog">'.BOX_CATALOG_REVIEWS.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_CATEGORY_SPECIALS.'?selected_box=catalog">'.BOX_CATALOG_CATEGORY_SPECIALS.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_SPECIALS.'?selected_box=catalog">'.BOX_CATALOG_SPECIALS.'</a></li>';

	  echo '  <li class="block"><a href="price_updater.php?selected_box=catalog">Quick Price Update</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_PRODUCTS_EXPECTED.'?selected_box=catalog">New Products Added</a></li>';

	    echo '  <li class="block"><a href="'.'products_discontinued.php'.'?selected_box=catalog">Products Discontinued</a></li>';

	    echo '  <li class="block" id="range">Featured Products 

		<ul> 

		<li class="block"><a href="'.FILENAME_FEATURED.'?selected_box=catalog">'.BOX_CATALOG_FEATURED_PRODUCTS.'</a></li>

		<li class="block"><a href="'.FILENAME_FEATURED.'?selected_box=catalog&featured_group=1">Group 1</a></li>

	  	<li class="block"><a href="'.FILENAME_FEATURED.'?selected_box=catalog&featured_group=2">Group 2</a></li>

		<li class="block"><a href="'.FILENAME_FEATURED.'?selected_box=catalog&featured_group=3">Group 3</a>

	    </ul> </li>';

	  

	  echo '  <li class="block"><a href="featured-manufacturers.php">Featured Manufacturers</a></li></li>';

	  echo '  <li class="block"><a href="'.FILENAME_XSELL_PRODUCTS.'?selected_box=catalog">'.BOX_CATALOG_XSELL_PRODUCTS.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_GOOGLESITEMAP.'?selected_box=catalog">'.BOX_CATALOG_GOOGLESITEMAP.'</a></li>';

      echo '  <li class="block"><a href="googlefeeder.php?noftp=1&selected_box=catalog">Fire Google Feed (No FTP)</a></li>';

      echo '  <li class="block"><a href="googlefeeder.php?selected_box=catalog">Fire Google Feed (Over FTP)</a></li>';

	  echo '  </ul>';

	  echo '</li>';

	// Configuration Section

	  // If top admin, show all config menus

		if($login_groups_id == 1)

		  {

			  echo '<li><a href="'.FILENAME_CONFIGURATION.'?gID=75?selected_box=configuration">'.BOX_HEADING_CONFIGURATION.'</a>';

			  echo '  <ul>';

			  $cfg_groups = '';

			  $configuration_groups_query = tep_db_query("select configuration_group_id as cgID, configuration_group_title as cgTitle from " . TABLE_CONFIGURATION_GROUP . " where visible = '1' order by sort_order");

			  while ($configuration_groups = tep_db_fetch_array($configuration_groups_query))

			    { echo '<li class="block"><a href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $configuration_groups['cgID'], 'NONSSL') . '">' . $configuration_groups['cgTitle'] . '</a></li>'; }

			//  echo '<li class="block"><a href="' . tep_href_link("configuration_frontend.php", 'gID=' . $configuration_groups['cgID'], 'NONSSL') . '">Frontend Categories</a></li>';
			
			echo '<li class="block"><a href="' . tep_href_link("store_logo.php",'', 'NONSSL') . '">Store Logo</a></li>';

	          echo '  </ul>';

	          echo '</li>';

		  }

		else

		  {

			  echo '<li><a href="'.FILENAME_CONFIGURATION.'?gID=75?selected_box=configuration">'.BOX_HEADING_CONFIGURATION.'</a>';

			  echo '  <ul>';

			  $cfg_groups = '';

			  $configuration_groups_query = tep_db_query("select configuration_group_id as cgID, configuration_group_title as cgTitle from " . TABLE_CONFIGURATION_GROUP . " where visible = '1' order by sort_order");

			  while ($configuration_groups = tep_db_fetch_array($configuration_groups_query))

			    {

				  if(ADMIN_LEVEL_OF_SERVICE == "gold")

				    {

					  if($configuration_groups['cgTitle'] == "My Store Options" || $configuration_groups['cgTitle'] == "Stock" || $configuration_groups['cgTitle'] == "MultiGeoZone MultiTable Shipping" || $configuration_groups['cgTitle'] == "Featured" || $configuration_groups['cgTitle'] == "XML Feed" || $configuration_groups['cgTitle'] == "Customer Details" || $configuration_groups['cgTitle'] == "Shipping/Packaging")

					    {

                    	  echo '<li class="block"><a href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $configuration_groups['cgID'], 'NONSSL') . '">' . $configuration_groups['cgTitle'] . '</a></li>';



						}

					}

				  elseif(ADMIN_LEVEL_OF_SERVICE == "ultimate")

				    {

					  if($configuration_groups['cgTitle'] == "My Store Options" || $configuration_groups['cgTitle'] == "Stock" || $configuration_groups['cgTitle'] == "Compare products side-by-side" ||  $configuration_groups['cgTitle'] == "MultiGeoZone MultiTable Shipping" || $configuration_groups['cgTitle'] == "Featured"  || $configuration_groups['cgTitle'] == "XML Feed" || $configuration_groups['cgTitle'] == "Customer Details" || $configuration_groups['cgTitle'] == "Shipping/Packaging"  || $configuration_groups['cgTitle'] == "Google XML Sitemap" || $configuration_groups['cgTitle'] == "Google Analytics" || $configuration_groups['cgTitle'] == "Social Bookmarks" ||  $configuration_groups['cgTitle'] == "Constant Contact")

					    {



                    	  echo '<li class="block"><a href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $configuration_groups['cgID'], 'NONSSL') . '">' . $configuration_groups['cgTitle'] . '</a></li>';



						}

					}

				}

	          echo '  </ul>';

	          echo '</li>';

		  }

/* config files being blocked		  

My Store

Minimum Values

Maximum Values

Images

Customer Details

Module Options

Shipping/Packaging

Product Listing

Logging

Cache

E-Mail Options

Download

GZip Compression 	GZip compression options

Sessions

SEO URLs

OBN Account

Order Editor

Compare products side-by-side

*/







	// Customers Section - moved to store information

/*	  echo '<li><a href="'.FILENAME_CUSTOMERS.'?selected_box=customers">'.BOX_HEADING_CUSTOMERS.'</a>';

	  echo '  <ul>';

	  echo '  <li><a href="'.FILENAME_CUSTOMERS.'?selected_box=customers">'.BOX_CUSTOMERS_CUSTOMERS.'</a></li>';

	  echo '  <li><a href="'.FILENAME_ORDERS.'?selected_box=customers">'.BOX_CUSTOMERS_ORDERS.'</a></li>';

	  echo '  </ul>';

	  echo '</li>'; */

	// GV_Admin

	  echo '<li><a href="'.FILENAME_COUPON_ADMIN.'?selected_box=gv_admin">Store Information</a>';

	  echo '  <ul>';

	  echo '  <li class="block"><a href="'.FILENAME_CUSTOMERS.'?selected_box=customers">'.BOX_CUSTOMERS_CUSTOMERS.'</a></li>';

          //spc starts

          echo '  <li class="block"><a style="font-weight:bold;" href="' . tep_href_link('customers_groups.php', '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CUSTOMERS_GROUPS . '</a></li>';

         //spc ends

          

	  echo '  <li class="block"><a href="'.FILENAME_ORDERS.'?selected_box=customers">'.BOX_CUSTOMERS_ORDERS.'</a></li>';

	  echo '  ';

	  echo '  <li class="block"><a href="'.FILENAME_COUPON_ADMIN.'?selected_box=gv_admin">'.BOX_COUPON_ADMIN.'</a></li>';

//	  echo '  <li class="block"><a href="'.FILENAME_GV_QUEUE.'?selected_box=gv_admin">'.BOX_GV_ADMIN_QUEUE.'</a></li>';

//	  echo '  <li class="block"><a href="'.FILENAME_GV_MAIL.'?selected_box=gv_admin">'.BOX_GV_ADMIN_MAIL.'</a></li>';

//	  echo '  <li class="block"><a href="'.FILENAME_GV_SENT.'?selected_box=gv_admin">'.BOX_GV_ADMIN_SENT.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_CURRENCIES.'?selected_box=localization">'.BOX_LOCALIZATION_CURRENCIES.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_LANGUAGES.'?selected_box=localization">'.BOX_LOCALIZATION_LANGUAGES.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_ORDERS_STATUS.'?selected_box=localization">'.BOX_LOCALIZATION_ORDERS_STATUS.'</a></li>';

	  echo '  ';

  	  echo '  <li class="block"><a href="'.FILENAME_COUNTRIES.'?selected_box=taxes">'.BOX_TAXES_COUNTRIES.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_ZONES.'?selected_box=taxes">'.BOX_TAXES_ZONES.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_GEO_ZONES.'?selected_box=taxes">'.BOX_TAXES_GEO_ZONES.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_TAX_CLASSES.'?selected_box=taxes">'.BOX_TAXES_TAX_CLASSES.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_TAX_RATES.'?selected_box=taxes">'.BOX_TAXES_TAX_RATES.'</a></li>';

	  // Points/Rewards Module V2.1rc2a BOF

      echo '  <li class="block"><a href="' . tep_href_link(FILENAME_CUSTOMERS_POINTS, '', 'NONSSL') . '">'.BOX_CUSTOMERS_POINTS.'</a></li>';

      echo '  <li class="block"><a href="' . tep_href_link(FILENAME_CUSTOMERS_POINTS_PENDING, '', 'NONSSL') . '">'.BOX_CUSTOMERS_POINTS_PENDING.'</a></li>';

      echo '  <li class="block"><a href="' . tep_href_link(FILENAME_CUSTOMERS_POINTS_REFERRAL, '', 'NONSSL') . '">'.BOX_CUSTOMERS_POINTS_REFERRAL.'</a></li>';

          

          // Points/Rewards Module V2.1rc2a EOF

      echo '  </ul>';

	  echo '</li>';

	// Header Tage Controller Section - moved to information manager

/*	  echo '<li><a href="'.FILENAME_HEADER_TAGS_CONTROLLER.'?selected_box=header_tags">'.BOX_HEADING_HEADER_TAGS_CONTROLLER.'</a>';

	  echo '  <ul>';

	  echo '  <li class="block"><a href="'.FILENAME_HEADER_TAGS_CONTROLLER.'?selected_box=header_tags">'.BOX_HEADER_TAGS_ADD_A_PAGE.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_HEADER_TAGS_ENGLISH.'?selected_box=header_tags">'.BOX_HEADER_TAGS_ENGLISH.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_HEADER_TAGS_FILL_TAGS.'?selected_box=header_tags">'.BOX_HEADER_TAGS_FILL_TAGS.'</a></li>';

	  echo '  </ul>';

	  echo '</li>';*/

	// Information Section

	  echo '<li><a href="'.FILENAME_INFORMATION_MANAGER.'?selected_box=information">'.BOX_HEADING_INFORMATION.'</a>';

	  echo '  <ul>';

			$info_groups = '';

			$information_groups_query = tep_db_query("select information_group_id as igID, information_group_title as igTitle from " . TABLE_INFORMATION_GROUP . " where visible = '1' order by sort_order");

			while ($information_groups = tep_db_fetch_array($information_groups_query))

			  {

				if(ADMIN_LEVEL_OF_SERVICE == "gold")

				  {

					if($information_groups['igTitle'] == "Information pages" || $information_groups['igTitle'] == "Define Mainpage" || $information_groups['igTitle'] == "Define Shop Page")

					  {

						echo '<li class="block"><a href="' . tep_href_link(FILENAME_INFORMATION_MANAGER, 'gID=' . $information_groups['igID'], 'NONSSL') . '">' . $information_groups['igTitle'] . '</a></li>';

					  }

				  }

				if(ADMIN_LEVEL_OF_SERVICE == "ultimate")

				  {

					if($information_groups['igTitle'] == "Information pages" || $information_groups['igTitle'] == "Define Mainpage" || $information_groups['igTitle'] == "Department Pages" || $information_groups['igTitle'] == "Define Shop Page" || $information_groups['igTitle'] == "Hidden Pages")

					  {

						echo '<li class="block"><a href="' . tep_href_link(FILENAME_INFORMATION_MANAGER, 'gID=' . $information_groups['igID'], 'NONSSL') . '">' . $information_groups['igTitle'] . '</a></li>';

					  }

				  }

			  }



	  echo '  <li class="block"><a href="'.FILENAME_HEADER_TAGS_CONTROLLER.'?selected_box=header_tags">'.BOX_HEADER_TAGS_ADD_A_PAGE.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_HEADER_TAGS_ENGLISH.'?selected_box=header_tags">'.BOX_HEADER_TAGS_ENGLISH.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_HEADER_TAGS_FILL_TAGS.'?selected_box=header_tags">'.BOX_HEADER_TAGS_FILL_TAGS.'</a></li>';

	  //echo '  <li class="block"><a href="edit_templates.php?selected_box=header_tags">Edit Templates</a>';

	  echo '  <li class="block" id="range">Manage Templates 

		<ul> 

			<li class="block"><a href="edit_templates.php?selected_box=header_tags">Edit Templates</a></li>

			<li class="block"><a href="layout_manager.php">Layout Manager</a></li>

			<li class="block"><a href="modules_boxes.php?type=b">Module Boxes</a></li>

	  		<li class="block"><a href="modules_boxes.php?type=m">Template Modules</a></li>

			<li class="block"><a href="modules_boxes_layout.php?type=b">Template Layout Boxes</a></li>

			<li class="block"><a href="modules_boxes_layout.php?type=b">Template Layout Modules</a></li>

	    </ul> 

		</li>';

	  

	  

	  echo '  <li class="block"><a href="select_template.php?select_box=header_tags">Product Listing Template</a></li>';

	  echo '  <li class="block"><a href="select_category_template.php?select_box=header_tags">Category Listing Template</a></li>';

	  echo '  </ul>';

	  echo '</li>';

	// Localization Section - moved to store information

/*	  echo '<li><a href="'.FILENAME_CURRENCIES.'?selected_box=localization">'.BOX_HEADING_LOCALIZATION.'</a>';

	  echo '  <ul>';

	  echo '  <li class="block"><a href="'.FILENAME_CURRENCIES.'?selected_box=localization">'.BOX_LOCALIZATION_CURRENCIES.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_LANGUAGES.'?selected_box=localization">'.BOX_LOCALIZATION_LANGUAGES.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_ORDERS_STATUS.'?selected_box=localization">'.BOX_LOCALIZATION_ORDERS_STATUS.'</a></li>';

	  echo '  </ul>';

	  echo '</li>'; */

	// Modules Section

	  echo '<li><a href="'.FILENAME_MODULES.'?set=payment&selected_box=modules">'.BOX_HEADING_MODULES.'</a>';

	  echo '  <ul>';

	  echo '  <li class="block"><a href="'.FILENAME_MODULES.'?set=payment">'.BOX_MODULES_PAYMENT.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_MODULES.'?set=shipping">'.BOX_MODULES_SHIPPING.'</a></li>';

//	  echo '  <li class="block"><a href="'.FILENAME_MODULES.'?set=sts">'.BOX_MODULES_STS.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_MODULES.'?set=ordertotal">'.BOX_MODULES_ORDER_TOTAL.'</a></li>';

	  //BOF:amazon/eBay integration

       echo '  <li class="block" id="range"><a href="#">Feeds</a>';

       echo '  <ul><li class="block"><a href="'.FILENAME_MODULES.'?set=amazon">Amazon</a></li><li class="block"><a href="'.FILENAME_MODULES.'?set=ebay">eBay</a></li><li class="block"><a href="'.FILENAME_MODULES.'?set=googlefeeder">Google Feeder</a></li></ul>'; 

	   //EOF:amazon/eBay integration

          // #12 10Jan2014 (MA) BOF

          echo '  <li class="block"><a href="'.FILENAME_EMAIL_TEMPLATE.'">Email Templates</a></li>';

          // #12 10Jan2014 (MA) EOF

    //BOF:01-09-2014

    echo '  <li class="block"><a href="fraud_prevention.php">Fraud Prevention</a></li>';

    //EOF:01-09-2014

       echo '  <li class="block" id="range"><a href="orders_POS.php">Store POS</a>';

       echo '  <ul><li class="block"><a href="orders_POS.php">POS Orders Listing</a></li><li class="block"><a href="create_order_process_POS.php">POS</a></li></ul>'; 

      //BOF RANGE MANAGER 25-DEC-2013

       echo '  <li class="block" id="range"><a href="'.FILENAME_RANGES_MANAGER.'?selected_box=catalog">'.BOX_CATALOG_RANGEMANAGER.'</a>';

       echo '  <ul><li class="block"><a href="'.FILENAME_RANGES_MANAGER.'?selected_box=catalog">'.BOX_CATALOG_RANGEMANAGER.'</a></li><li class="block"><a href="'.FILENAME_LANES_MANAGER.'?selected_box=catalog">'.BOX_CATALOG_LANESMANAGER.'</a></li><li class="block"><a href="range_operations.php?selected_box=catalog">Ranges POS</a></li></ul>'; 

       //EOF RANGE MANAGER 25-DEC-2013

	   //BOF:inventory_import

	   echo '  <li class="block"><a href="inventory_import.php">Inventory Import</a></li>';

	    //EOF:inventory_import

		//BOF MOBILESITE 10 JAN 2014	

	   echo '  <li class="block"><a href="mobilesite.php">Mobile Site</a></li>';

	   //EOF MOBILESITE 10 JAN 2014	

	   //BOF FACEBOOK STORE 12 JAN 2014	

	   echo '  <li class="block"><a href="'.FILENAME_FACEBOOKSTORE.'">Facebook Store</a></li>';

	   //EOF FACEBOOK STORE 12 JAN 2014	

	  //echo '  <li class="block"><a href="custom_shipping_modules.php">Custom Shipping Modules</a></li>';

	  /*echo '  <li class="block"><a href="'.FILENAME_VENDORS.'">' . BOX_VENDORS . '</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_PRODS_VENDORS.'">' . BOX_VENDORS_REPORTS_PROD . '</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_ORDERS_VENDORS.'">' . BOX_VENDORS_ORDERS . '</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_MOVE_VENDORS.'">' . BOX_MOVE_VENDOR_PRODS . '</a></li>';*/

      echo '<li class="block" id="range"><a href="'.FILENAME_VENDORS.'">Vendors Manager</a>';

      echo '<ul>';

      echo '<li class="block"><a href="'.FILENAME_VENDORS.'">Vendors Manager</a></li>';

      echo '<li class="block"><a href="products_to_vendor.php">Map Products with Vendor</a></li>';

      echo '  <li class="block"><a href="'.FILENAME_PRODS_VENDORS.'">Mapped Products Listing</a></li>';

      echo '  <li class="block"><a href="'.FILENAME_ORDERS_VENDORS.'">' . BOX_VENDORS_ORDERS . '</a></li>';

      echo '  <li class="block"><a href="'.FILENAME_MOVE_VENDORS.'">' . BOX_MOVE_VENDOR_PRODS . '</a></li>';

      echo '</ul>';



	  echo '  <li class="block"><a href="product_per_day.php">Product per Day Scheduler</a></li>';

	  echo '  </ul>';

	  echo '</li>';

	// Reports Section

	  echo '<li><a href="advanced_stats.php?selected_box=reports">'.BOX_HEADING_REPORTS.'</a>';

	  echo '  <ul>';

	  echo '  <li class="block"><a href="advanced_stats.php">Advanced Stats</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_STATS_PRODUCTS_VIEWED.'">'.BOX_REPORTS_PRODUCTS_VIEWED.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_STATS_PRODUCTS_PURCHASED.'">'.BOX_REPORTS_PRODUCTS_PURCHASED.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_STATS_CUSTOMERS.'">'.BOX_REPORTS_ORDERS_TOTAL.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_STATS_SALES_REPORT.'">'.BOX_REPORTS_SALES_REPORT.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_STATS_CREDITS.'">'.BOX_REPORTS_CREDITS.'</a></li>';

	  echo '  <li class="block"><a href="sales_report_export.php">Sales Report Export</a></li>';

          echo '  <li class="block"><a href="stats_wishlists.php">Customer Wishlists</a></li>';

		  echo '  <li class="block"><a href="marketing_feeds.php">Configurable Marketing Feeds</a></li>';

		  echo '  <li class="block"><a href="conversion_rate_report.php">Conversion Rate Report</a></li>';

	  echo '  </ul>';

	  echo '</li>';

	// Taxes Section - moved to store information

/*	  echo '<li><a href="'.FILENAME_COUNTRIES.'?selected_box=taxes">'.BOX_HEADING_LOCATION_AND_TAXES.'</a>';

	  echo '  <ul>';

	  echo '  <li class="block"><a href="'.FILENAME_COUNTRIES.'?selected_box=taxes">'.BOX_TAXES_COUNTRIES.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_ZONES.'?selected_box=taxes">'.BOX_TAXES_ZONES.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_GEO_ZONES.'?selected_box=taxes">'.BOX_TAXES_GEO_ZONES.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_TAX_CLASSES.'?selected_box=taxes">'.BOX_TAXES_TAX_CLASSES.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_TAX_RATES.'?selected_box=taxes">'.BOX_TAXES_TAX_RATES.'</a></li>';

	  echo '  </ul>';

	  echo '</li>'; */

	// Tools Section

	  echo '<li><a href="webmail.php?selected_box=tools">'.BOX_HEADING_TOOLS.'</a>';

	  echo '  <ul>';

	  echo '  <li class="block"><a href="webmail.php">Webmail</a></li>';

	  //BOF:amazon_integration

	  echo '  <li class="block"><a href="amazon_feeds_manager.php">Amazon Feeds Manager</a></li>';

	  //BOF:amazon_integration

	  //BOF:ebay_integration

	  echo '  <li class="block"><a href="ebay_feeds_manager.php">eBay Feeds Manager</a></li>';

	  //BOF:ebay_integration

	  echo '  <li class="block"><a href="'.FILENAME_BANNER_MANAGER.'">'.BOX_TOOLS_BANNER_MANAGER.'</a></li>';

//	  echo '  <li class="block"><a href="'.FILENAME_CACHE.'">'.BOX_TOOLS_CACHE.'</a></li>';

//	  echo '  <li class="block"><a href="'.FILENAME_DEFINE_LANGUAGE.'">'.BOX_TOOLS_DEFINE_LANGUAGE.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_FILE_MANAGER.'">'.BOX_TOOLS_FILE_MANAGER.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_MAIL.'">'.BOX_TOOLS_MAIL.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_NEWSLETTERS.'">'.BOX_TOOLS_NEWSLETTER_MANAGER.'</a></li>';

//	  echo '  <li class="block"><a href="'.FILENAME_SERVER_INFO.'">'.BOX_TOOLS_SERVER_INFO.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_WHOS_ONLINE.'">'.BOX_TOOLS_WHOS_ONLINE.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_QBI.'">'.BOX_CATALOG_QBI.'</a></li>';

//	  echo '  <li class="block"><a href="'.FILENAME_PACKAGING.'">'.BOX_TOOLS_PACKAGING.'</a></li>';

	  echo '  <li class="block"><a href="'.FILENAME_KEYWORDS.'">Keyword Manager</a></li>';

	  echo '  <li class="block"><a href="user_info_export.php">User Info Export</a></li>';

	  //echo '  <li class="block"><a href="ctct_list_manager.php">Constant Contact Manager</a></li>';

	  echo '  </ul>';

	  echo '</li>';

/*	  //BOF: constantContact

	  echo '<li><a href="ctct_list_manager.php">ConstantContact</a>';

	  echo '  <ul>';

	  echo '  <li class="block"><a href="ctct_list_manager.php">List Manager</a></li>';

	  echo '  </ul>';

	  echo '</li>';

	  //BOF: constantContact */

	// Training Section

	  echo '<li><a href="training_videos.php?selected_box=training_support">Training/Support</a>';

	  echo '  <ul>';

	  echo '  <li class="block"><a href="training_videos.php">Training Videos</a></li>';

	  echo '  <li class="block"><a href="support_ticket.php">Support Tickets</a></li>';

	  echo '  <li class="block"><a href="training_videos.php?vid_id=117">F.A.Q.</a></li>';

	  echo '  </ul>';

	  echo '</li>';

	?>

        </ul>

	</div>

  </div>

</div>