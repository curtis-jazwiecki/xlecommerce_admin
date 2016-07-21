<?php
/*
  $Id: tools.php,v 1.21 2003/07/09 01:18:53 hpdl Exp $

CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
?>
<!-- tools //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_TOOLS,
                     'link'  => tep_href_link(FILENAME_BACKUP, 'selected_box=tools'));

  if ($selected_box == 'tools') {
    $contents[] = array('text'  => tep_admin_files_boxes(FILENAME_BACKUP, BOX_TOOLS_BACKUP) .
                                   tep_admin_files_boxes(FILENAME_BANNER_MANAGER, BOX_TOOLS_BANNER_MANAGER) . 
                                   tep_admin_files_boxes(FILENAME_CACHE, BOX_TOOLS_CACHE) .
                                   tep_admin_files_boxes(FILENAME_DEFINE_LANGUAGE, BOX_TOOLS_DEFINE_LANGUAGE) .
                                   tep_admin_files_boxes(FILENAME_FILE_MANAGER, BOX_TOOLS_FILE_MANAGER) .
                                   tep_admin_files_boxes(FILENAME_MAIL, BOX_TOOLS_MAIL) .
                                   tep_admin_files_boxes(FILENAME_NEWSLETTERS, BOX_TOOLS_NEWSLETTER_MANAGER) .
                                   tep_admin_files_boxes(FILENAME_SERVER_INFO, BOX_TOOLS_SERVER_INFO) .
                                   tep_admin_files_boxes(FILENAME_WHOS_ONLINE, BOX_TOOLS_WHOS_ONLINE).
								   //QBI INSTALL
								   tep_admin_files_boxes(FILENAME_QBI, BOX_CATALOG_QBI) . 
								   tep_admin_files_boxes(FILENAME_PACKAGING, BOX_TOOLS_PACKAGING).
								   tep_admin_files_boxes('awstats.php', 'Awstats') . 
								   tep_admin_files_boxes(FILENAME_KEYWORDS, 'Keyword Manager').
								   tep_admin_files_boxes('user_info_export.php', 'User Info Export'));
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- tools_eof //-->
