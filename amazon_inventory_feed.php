<?php
$cron_script='yes';
require('cron_application_top.php');

$amazon = new amazon_manager('mws');
$amazon->submit_inventory_feed();
?>