<?php
/*
  $Id: categories.php,v 1.26 2003/07/11 14:40:28 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Categories / Products');
define('HEADING_TITLE_SEARCH', 'Search:');
define('HEADING_TITLE_GOTO', 'Go To:');

define('TABLE_HEADING_ID', 'ID');
define('TABLE_HEADING_CATEGORIES_PRODUCTS', 'Categories / Products');
define('TABLE_HEADING_ACTION', 'Action');
define('TABLE_HEADING_STATUS', 'Status');

define('TEXT_NEW_PRODUCT', 'New Product in &quot;%s&quot;');
define('TEXT_CATEGORIES', 'Categories:');
define('TEXT_SUBCATEGORIES', 'Subcategories:');
define('TEXT_PRODUCTS', 'Products:');
define('TEXT_PRODUCTS_PRICE_INFO', 'Price:');
define('TEXT_PRODUCTS_TAX_CLASS', 'Tax Class:');
define('TEXT_PRODUCTS_AVERAGE_RATING', 'Average Rating:');
define('TEXT_PRODUCTS_QUANTITY_INFO', 'Quantity:');
define('TEXT_DATE_ADDED', 'Date Added:');
define('TEXT_DATE_AVAILABLE', 'Date Available:');
define('TEXT_LAST_MODIFIED', 'Last Modified:');
define('TEXT_IMAGE_NONEXISTENT', 'IMAGE DOES NOT EXIST');
define('TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS', 'Please insert a new category or product in this level.');
define('TEXT_PRODUCT_MORE_INFORMATION', 'For more information, please visit this products <a href="http://%s" target="blank"><u>webpage</u></a>.');
define('TEXT_PRODUCT_DATE_ADDED', 'This product was added to our catalog on %s.');
define('TEXT_PRODUCT_DATE_AVAILABLE', 'This product will be in stock on %s.');

define('TEXT_EDIT_INTRO', 'Please make any necessary changes');
define('TEXT_EDIT_CATEGORIES_ID', 'Category ID:');
define('TEXT_EDIT_CATEGORIES_NAME', 'Category Name:');
define('TEXT_EDIT_CATEGORIES_IMAGE', 'Category Image:');
define('TEXT_EDIT_SORT_ORDER', 'Sort Order:');

define('TEXT_INFO_COPY_TO_INTRO', 'Please choose a new category you wish to copy this product to');
define('TEXT_INFO_CURRENT_CATEGORIES', 'Current Categories:');

define('TEXT_INFO_HEADING_NEW_CATEGORY', 'New Category');
define('TEXT_INFO_HEADING_EDIT_CATEGORY', 'Edit Category');
define('TEXT_INFO_HEADING_DELETE_CATEGORY', 'Delete Category');
define('TEXT_INFO_HEADING_MOVE_CATEGORY', 'Move Category');
define('TEXT_INFO_HEADING_DELETE_PRODUCT', 'Delete Product');
define('TEXT_INFO_HEADING_MOVE_PRODUCT', 'Move Product');
define('TEXT_INFO_HEADING_COPY_TO', 'Copy To');

define('TEXT_DELETE_CATEGORY_INTRO', 'Are you sure you want to delete this category?');
define('TEXT_DELETE_PRODUCT_INTRO', 'Are you sure you want to permanently delete this product?');

define('TEXT_DELETE_WARNING_CHILDS', '<b>WARNING:</b> There are %s (child-)categories still linked to this category!');
define('TEXT_DELETE_WARNING_PRODUCTS', '<b>WARNING:</b> There are %s products still linked to this category!');

define('TEXT_MOVE_PRODUCTS_INTRO', 'Please select which category you wish <b>%s</b> to reside in');
define('TEXT_MOVE_CATEGORIES_INTRO', 'Please select which category you wish <b>%s</b> to reside in');
define('TEXT_MOVE', 'Move <b>%s</b> to:');

define('TEXT_NEW_CATEGORY_INTRO', 'Please fill out the following information for the new category');
define('TEXT_CATEGORIES_NAME', 'Category Name:');
define('TEXT_CATEGORIES_IMAGE', 'Category Image:');
define('TEXT_SORT_ORDER', 'Sort Order:');

define('TEXT_PRODUCTS_STATUS', 'Products Status:');
define('TEXT_PRODUCTS_DATE_AVAILABLE', 'Date Available:');
define('TEXT_PRODUCT_AVAILABLE', 'Active');
define('TEXT_PRODUCT_NOT_AVAILABLE', 'Inactive');
define('TEXT_PRODUCTS_MANUFACTURER', 'Products Manufacturer:');
define('TEXT_PRODUCTS_NAME', 'Products Name:');
define('TEXT_PRODUCTS_DESCRIPTION', 'Products Description:');
define('TEXT_PRODUCTS_QUANTITY', 'Products Quantity:');
define('TEXT_PRODUCTS_MODEL', 'Products Model:');
define('TEXT_PRODUCTS_IMAGE', 'Products Image:');
define('TEXT_PRODUCTS_URL', 'Products URL:');
define('TEXT_PRODUCTS_URL_WITHOUT_HTTP', '<small>(without http://)</small>');
define('TEXT_PRODUCTS_PRICE_NET', 'Products Price (Net):');
define('TEXT_PRODUCTS_PRICE_GROSS', 'Products Price (Gross):');
define('TEXT_PRODUCTS_WEIGHT', 'Products Weight:');
define('TEXT_PRODUCTS_SIZE', 'Products Size:');
define('TEXT_DISCLAIMER_NEEDED', 'Disclaimer Needed:');


define('EMPTY_CATEGORY', 'Empty Category');

define('TEXT_HOW_TO_COPY', 'Copy Method:');
define('TEXT_COPY_AS_LINK', 'Link product');
define('TEXT_COPY_AS_DUPLICATE', 'Duplicate product');

define('ERROR_CANNOT_LINK_TO_SAME_CATEGORY', 'Error: Can not link products in the same category.');
define('ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE', 'Error: Catalog images directory is not writeable: ' . DIR_FS_CATALOG_IMAGES);
define('ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST', 'Error: Catalog images directory does not exist: ' . DIR_FS_CATALOG_IMAGES);
define('ERROR_CANNOT_MOVE_CATEGORY_TO_PARENT', 'Error: Category cannot be moved into child category.');

define('TEXT_PRODUCT_METTA_INFO', '<b>Meta Tag Information</b>');
define('TEXT_PRODUCTS_PAGE_TITLE', 'Product Title Tag:');
define('TEXT_PRODUCTS_HEADER_DESCRIPTION', 'Product Description Tag:');
define('TEXT_PRODUCTS_KEYWORDS', 'Product Keywords Tag:');

define('TEXT_CATEGORIES_BANNER_IMAGE', 'Banner Image');
define('TEXT_EDIT_BANNER_IMAGE', 'Banner Image: ');

define('TEXT_PRODUCTS_HEIGHT', 'Height:');
  define('TEXT_PRODUCTS_LENGTH', 'Length:');
  define('TEXT_PRODUCTS_WIDTH', 'Width:');
  define('TEXT_PRODUCTS_READY_TO_SHIP', 'Ready to ship:');
  define('TEXT_PRODUCTS_READY_TO_SHIP_SELECTION', 'Product can be shipped in its own container.');
  define('TEXT_PRODUCTS_SPLIT_PRODUCT','Split in several items:');
  define('NAME_WINDOW_SPLIT_PRODUCTS_POPUP', 'SplitProducts');
  define('TEXT_MOUSE_OVER_SPLIT_PRODUCTS', 'Edit splitting and splitted items in a pop-up window');
 // BOF SKU RANGE MANAGER IS_FULL DAY PRICE 26-DEC-2013
 define('ERROR_SELECT_RANGE_LANE', 'Please Select Range and Lane fields');
 // EOF SKU RANGE MANAGER IS_FULL DAY PRICE 26-DEC-2013 
 
 // BOF Bundled Products
define('TEXT_PRODUCTS_BUNDLE', 'Set Packages:');
define('TEXT_ADD_LINE', 'Add a  line');
define('TEXT_ADD_PRODUCT', 'Add to package: ');
define('TEXT_REMOVE_PRODUCT', 'Remove from package');
define('TEXT_BUNDLE_HEADING', 'Product Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Qty&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;select product to add from pull down menu below');
define('TEXT_PRODUCTS_BY_BUNDLE', 'This product contains the following items:');
define('TEXT_RATE_COSTS', 'Cost of separate parts:');
define('TEXT_IT_SAVE', 'You save');
define('ENTRY_AVAILABLE_SEPARATELY', 'This product is available for individual sale.');
define('ENTRY_IN_BUNDLE_ONLY', 'This product is available for sale only as a part of a package.');
define('TEXT_SOLD_IN_BUNDLE', 'This product may be purchased only as a part of the following package(s):');
// EOF Bundled Products

//MVS Start
define('TEXT_VENDOR', 'Vendor: ');
define('TEXT_PRODUCTS_VENDORS', 'Products Vendors');
define('TEXT_VENDORS_PRODUCT_PRICE_BASE', 'Vendors Price(Base):');
define('TEXT_PRODUCTS_VENDORS', 'Products Vendors');
define('TEXT_VENDORS_PROD_COMMENTS', 'Vendors Comments or <br>Special Instructions');
define('TEXT_VENDORS_PROD_ID', 'Vendors Item Number');
define('TEXT_VENDORS_PRODUCT_PRICE_INFO', 'Vendors Price: ');
define('TEXT_VENDORS_PRODUCT_PRICE_TITLE', ' Prod. Price:&nbsp;&nbsp; <br>');
define('TEXT_VENDORS_PRICE_TITLE', 'Vendor Price: <br>');
define('TEXT_PRODUCTS_LENGTH', 'Length:');
define('TEXT_PRODUCTS_WIDTH', 'Width:');
define('TEXT_PRODUCTS_HEIGHT', 'Height:');
//MVS End
?>
