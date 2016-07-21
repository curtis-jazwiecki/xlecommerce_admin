<?php
/*
  $Id: languages.php,v 1.6 2003/06/20 16:23:08 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

  function tep_get_languages_directory($code) {
    global $languages_id;

    $language_query = tep_db_query("select languages_id, directory from " . TABLE_LANGUAGES . " where code = '" . tep_db_input($code) . "'");
    if (tep_db_num_rows($language_query)) {
      $language = tep_db_fetch_array($language_query);
      $languages_id = $language['languages_id'];
      return $language['directory'];
    } else {
      return false;
    }
  }
?>