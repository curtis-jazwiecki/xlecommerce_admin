<?php
/*
  $Id: gv_admin.php,v 1.2.2.1 2003/04/18 21:13:51 wilt Exp $

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
?>
<!-- gv_admin //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_GV_ADMIN,
                     'link'  => tep_href_link(FILENAME_COUPON_ADMIN, 'selected_box=gv_admin'));

  if ($selected_box == 'gv_admin') {
    $contents[] = array('text'  => 
	/*
	'<a href="' . tep_href_link(FILENAME_COUPON_ADMIN, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_COUPON_ADMIN . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_GV_QUEUE, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_GV_ADMIN_QUEUE . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_GV_MAIL, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_GV_ADMIN_MAIL . '</a><br>' . 
                                   '<a href="' . tep_href_link(FILENAME_GV_SENT, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_GV_ADMIN_SENT . '</a>');
                                   */
                                   tep_admin_files_boxes(FILENAME_COUPON_ADMIN, BOX_COUPON_ADMIN) .
                                   tep_admin_files_boxes(FILENAME_GV_QUEUE, BOX_GV_ADMIN_QUEUE) .
                                   tep_admin_files_boxes(FILENAME_GV_MAIL, BOX_GV_ADMIN_MAIL) .
                                   tep_admin_files_boxes(FILENAME_GV_SENT, BOX_GV_ADMIN_SENT));
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- gv_admin_eof //-->