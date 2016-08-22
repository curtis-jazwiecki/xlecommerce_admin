<?php
/*
  $Id: database_tables.php,v 1.1 2003/06/20 00:18:30 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
//Admin begin
  define('TABLE_ADMIN', 'admin');
  define('TABLE_ADMIN_FILES', 'admin_files');
  define('TABLE_ADMIN_GROUPS', 'admin_groups');
//Admin end
// define the database table names used in the project
  define('TABLE_ADDRESS_BOOK', 'address_book');
  define('TABLE_ADDRESS_FORMAT', 'address_format');
  define('TABLE_BANNERS', 'banners');
  define('TABLE_BANNERS_HISTORY', 'banners_history');
  define('TABLE_CATEGORIES', 'categories');
  define('TABLE_CATEGORIES_DESCRIPTION', 'categories_description');
  define('TABLE_CONFIGURATION', 'configuration');
  define('TABLE_CONFIGURATION_GROUP', 'configuration_group');
  define('TABLE_COUNTRIES', 'countries');
  define('TABLE_CURRENCIES', 'currencies');
  define('TABLE_CUSTOMERS', 'customers');
  define('TABLE_CUSTOMERS_BASKET', 'customers_basket');
  define('TABLE_CUSTOMERS_BASKET_ATTRIBUTES', 'customers_basket_attributes');
  define('TABLE_CUSTOMERS_INFO', 'customers_info');
  define('TABLE_LANGUAGES', 'languages');
  define('TABLE_MANUFACTURERS', 'manufacturers');
  define('TABLE_MANUFACTURERS_INFO', 'manufacturers_info');
  define('TABLE_NEWSLETTERS', 'newsletters');
  define('TABLE_ORDERS', 'orders');
  define('TABLE_ORDERS_PRODUCTS', 'orders_products');
  define('TABLE_ORDERS_PRODUCTS_ATTRIBUTES', 'orders_products_attributes');
  define('TABLE_ORDERS_PRODUCTS_DOWNLOAD', 'orders_products_download');
  define('TABLE_ORDERS_STATUS', 'orders_status');
  define('TABLE_ORDERS_STATUS_HISTORY', 'orders_status_history');
  define('TABLE_ORDERS_TOTAL', 'orders_total');
  define('TABLE_PRODUCTS', 'products');
  define('TABLE_PRODUCTS_ATTRIBUTES', 'products_attributes');
  define('TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD', 'products_attributes_download');
  define('TABLE_PRODUCTS_DESCRIPTION', 'products_description');
  define('TABLE_PRODUCTS_NOTIFICATIONS', 'products_notifications');
  define('TABLE_PRODUCTS_OPTIONS', 'products_options');
  define('TABLE_PRODUCTS_OPTIONS_VALUES', 'products_options_values');
  define('TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS', 'products_options_values_to_products_options');
  define('TABLE_PRODUCTS_TO_CATEGORIES', 'products_to_categories');
  define('TABLE_REVIEWS', 'reviews');
  define('TABLE_REVIEWS_DESCRIPTION', 'reviews_description');
  define('TABLE_SESSIONS', 'sessions');
  define('TABLE_SPECIALS', 'specials');
  define('TABLE_TAX_CLASS', 'tax_class');
  define('TABLE_TAX_RATES', 'tax_rates');
  define('TABLE_GEO_ZONES', 'geo_zones');
  define('TABLE_ZONES_TO_GEO_ZONES', 'zones_to_geo_zones');
  define('TABLE_WHOS_ONLINE', 'whos_online');
  define('TABLE_ZONES', 'zones');
  // ###### Category Specials #######
  define('TABLE_SPECIAL_CATEGORY', 'special_category');
  define('TABLE_SPECIAL_PRODUCT', 'special_product');
  // ###### end Category Specials #######
  
    // Add-on - Information Pages Unlimited
  define('TABLE_INFORMATION', 'information');
  define('TABLE_INFORMATION_GROUP', 'information_group');
  //QBI INSTALL
  define('TABLE_QBI_CONFIG', 'qbi_config');
  define('TABLE_QBI_DISC', 'qbi_disc');
  define('TABLE_QBI_GROUPS', 'qbi_groups');
  define('TABLE_QBI_GROUPS_ITEMS', 'qbi_groups_items');
  define('TABLE_QBI_ITEMS', 'qbi_items');
  define('TABLE_QBI_OT', 'qbi_ot');
  define('TABLE_QBI_OT_DISC', 'qbi_ot_disc');
  define('TABLE_QBI_PAYOSC', 'qbi_payosc');
  define('TABLE_QBI_PAYOSC_PAYQB', 'qbi_payosc_payqb');
  define('TABLE_QBI_PAYQB', 'qbi_payqb');
  define('TABLE_QBI_PRODUCTS_ITEMS', 'qbi_products_items');  
  define('TABLE_QBI_SHIPOSC', 'qbi_shiposc');
  define('TABLE_QBI_SHIPQB', 'qbi_shipqb');
  define('TABLE_QBI_SHIPOSC_SHIPQB', 'qbi_shiposc_shipqb');
  define('TABLE_QBI_TAXES', 'qbi_taxes');
  define('TABLE_FEATURED', 'featured');
//QBI INSTALL
define('TABLE_PRODUCTS_XSELL', 'products_xsell');
define('TABLE_PACKAGING', 'packaging');

// BOF Separate Pricing Per Customer
  define('TABLE_PRODUCTS_GROUPS', 'products_groups');
  define('TABLE_CUSTOMERS_GROUPS', 'customers_groups');
  define('TABLE_PRODUCTS_GROUP_PRICES', 'products_group_prices_cg_');
  define('TABLE_PRODUCTS_ATTRIBUTES_GROUPS', 'products_attributes_groups');
// EOF Separate Pricing Per Customer
//BOF AMAZON INTEGRATION PRODUCT EXTENDED
define('TABLE_PRODUCTS_EXTENDED', 'products_extended');
//EOF AMAZON INTEGRATION PRODUCT EXTENDED
//BOF RANGES
define('TABLE_RANGES', 'ranges');
define('TABLE_LANES', 'lanes');
define('TABLE_RANGES_TO_LANES', 'ranges_to_lanes');
//EOF RANGES
// BOF Facebook Store 12_JAN_2014
define('TABLE_FACEBOOKSTORE', 'facebook_products');
// EOF Facebook Store 12_JAN_2014

// BOF wishlist 12_FEB_2014
define('TABLE_WISHLIST', 'customers_wishlist'); 
// EOF wishlist 12_FEB_2014

// BOF Bundled Products
  define('TABLE_PRODUCTS_BUNDLES', 'products_bundles');
// EOF Bundled Products
  
  define('TABLE_CUSTOMERS_POINTS_PENDING', 'customers_points_pending');//Points/Rewards Module V2.1rc2a
//MVS start
  define('TABLE_VENDORS', 'vendors');
  define('TABLE_VENDORS_INFO', 'vendors_info');
  define('TABLE_VENDOR_CONFIGURATION', 'vendor_configuration');
  define('TABLE_ORDERS_SHIPPING', 'orders_shipping');
  define('TABLE_PACKAGING', 'packaging');
//MVS end
  define('TABLE_LAYOUT_GROUPS', 'layout_groups');
  define('TABLE_LAYOUT_GROUPS_FILES', 'layout_groups_files');
  
  define('TABLE_MODULE_BOXES', 'module_boxes');
  define('TABLE_MODULE_BOXES_LAYOUT', 'module_boxes_layout');
  define('TABLE_FFL_DEALERS_DOCS', 'ffl_dealers_docs');
  define('AVATAX_LOG', 'avatax_log');
  define('AVATAX_CODE_LIST', 'avatax_code_list'); // added on 01-06-2016 
  define('TABLE_ORDERS_FFL', 'orders_ffl'); // added on 01-06-2016 
?>