<?php
/*
  $Id: object_info.php,v 1.6 2003/06/20 16:23:08 hpdl Exp $

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

  class objectInfo {

// class constructor
    function objectInfo($object_array) {
      reset($object_array);
      while (list($key, $value) = each($object_array)) {
        $this->$key = tep_db_prepare_input($value);
      }
    }
  }
?>
