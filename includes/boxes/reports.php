<?php
/*
  $Id: reports.php,v 1.5 2003/07/09 01:18:53 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- reports //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_REPORTS,
                     'link'  => tep_href_link("advanced_stats.php", 'selected_box=reports'));

  if ($selected_box == 'reports') {
    $contents[] = array('text'  => tep_admin_files_boxes("advanced_stats.php", "Advanced Stats") .
								   tep_admin_files_boxes(FILENAME_STATS_PRODUCTS_VIEWED, BOX_REPORTS_PRODUCTS_VIEWED) .
                                   tep_admin_files_boxes(FILENAME_STATS_PRODUCTS_PURCHASED, BOX_REPORTS_PRODUCTS_PURCHASED) .
                                   tep_admin_files_boxes(FILENAME_STATS_CUSTOMERS, BOX_REPORTS_ORDERS_TOTAL) .
								   tep_admin_files_boxes(FILENAME_STATS_SALES_REPORT, BOX_REPORTS_SALES_REPORT) .
								   tep_admin_files_boxes(FILENAME_STATS_CREDITS, BOX_REPORTS_CREDITS));
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- reports_eof //-->
