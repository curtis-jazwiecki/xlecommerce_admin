<?php
/*
  $Id: catalog.php,v 1.21 2003/07/09 01:18:53 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
?>
<!-- catalog //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_CATALOG,
                     'link'  => tep_href_link(FILENAME_CATEGORIES, 'selected_box=catalog'));

  if ($selected_box == 'catalog') {
    $contents[] = array('text'  => tep_admin_files_boxes(FILENAME_CATEGORIES, BOX_CATALOG_CATEGORIES_PRODUCTS) .
                                   tep_admin_files_boxes(FILENAME_PRODUCTS_ATTRIBUTES, BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES) .
                                   tep_admin_files_boxes('easypopulate.php', 'Easy Populate') .
                                   tep_admin_files_boxes(FILENAME_MANUFACTURERS, BOX_CATALOG_MANUFACTURERS) .
                                   tep_admin_files_boxes(FILENAME_REVIEWS, BOX_CATALOG_REVIEWS) .
                                   tep_admin_files_boxes(FILENAME_CATEGORY_SPECIALS, BOX_CATALOG_CATEGORY_SPECIALS) .
                                   tep_admin_files_boxes(FILENAME_SPECIALS, BOX_CATALOG_SPECIALS) .
                                   tep_admin_files_boxes('price_updater.php', 'Quick Price Update') .
                                   tep_admin_files_boxes(FILENAME_PRODUCTS_EXPECTED, BOX_CATALOG_PRODUCTS_EXPECTED) .
                                   tep_admin_files_boxes('products_discontinued', 'Products Discontinued') .
								   tep_admin_files_boxes(FILENAME_FEATURED, BOX_CATALOG_FEATURED_PRODUCTS ).
								   tep_admin_files_boxes(FILENAME_XSELL_PRODUCTS, BOX_CATALOG_XSELL_PRODUCTS ).
								   tep_admin_files_boxes(FILENAME_GOOGLESITEMAP, BOX_CATALOG_GOOGLESITEMAP) );
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- catalog_eof //-->
