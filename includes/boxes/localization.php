<?php
/*
  $Id: localization.php,v 1.16 2003/07/09 01:18:53 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
?>
<!-- localization //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_LOCALIZATION,
                     'link'  => tep_href_link(FILENAME_CURRENCIES, 'selected_box=localization'));

  if ($selected_box == 'localization') {
    $contents[] = array('text'  => tep_admin_files_boxes(FILENAME_CURRENCIES, BOX_LOCALIZATION_CURRENCIES) .
                                   tep_admin_files_boxes(FILENAME_LANGUAGES, BOX_LOCALIZATION_LANGUAGES) .
                                   tep_admin_files_boxes(FILENAME_ORDERS_STATUS, BOX_LOCALIZATION_ORDERS_STATUS));
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- localization_eof //-->
