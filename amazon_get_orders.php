<?php
$cron_script='yes';
require('cron_application_top.php');

$amazon = new amazon_manager('mwso');
$ts = time() - (24 * 60 * 60);
$amazon->get_new_orders($ts);
echo 'done';
?>