<?php
/*
  $Id: reports.php,v 1.5 2003/07/09 01:18:53 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- reports -->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => "Training/Support",
                     'link'  => tep_href_link("training_videos.php", 'selected_box=training_support'));

  if ($selected_box == 'training_support') {
    $contents[] = array('text'  => tep_admin_files_boxes("training_videos.php", 'Training Videos') .
								   tep_admin_files_boxes("support_ticket.php", "Supoort Tickets"));
  }
  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- reports_eof //-->
