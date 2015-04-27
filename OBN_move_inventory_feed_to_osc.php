<?php
$cron_script='yes';
require_once('cron_application_top.php');
require_once('OBN_global_feed_to_osc.php');
$feed = new global_feed_to_osc();
$feed->inventory_feed_to_osc();
?>
