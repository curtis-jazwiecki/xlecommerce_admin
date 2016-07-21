<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
include('cron_application_top.php');
$response = array();
$countries_query = tep_db_query("select countries_id, countries_name from countries " . (!empty($_GET['term']) ? " where countries_name like '%" . $_GET['term'] . "%' " : "" ) );
while ($entry = tep_db_fetch_array($countries_query)){
    $response[] = array('label' => $entry['countries_name'], 'value' => $entry['countries_id']);
}
echo json_encode($response);
?>