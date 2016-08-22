<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
$cron_script='yes';
require('cron_application_top.php');

$amazon = new amazon_manager('mws');
$amazon->submit_product_feed();
$amazon->submit_relationships_feed();
$amazon->submit_price_feed();
$amazon->submit_image_feed();
$amazon->submit_inventory_feed();
?>