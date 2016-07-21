<?php
/*
  $Id$

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.

*/

  if ( file_exists(DIR_FS_ADMIN . DIR_WS_MODULES . 'dashboard/d_paypal_app.php') ) {
    include(DIR_FS_ADMIN . DIR_WS_MODULES . 'dashboard/d_paypal_app.php');

    $d_paypal_app = new d_paypal_app();

    echo $d_paypal_app->getOutput();
  }
?>
