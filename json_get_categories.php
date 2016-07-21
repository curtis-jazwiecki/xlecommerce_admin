<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
include('cron_application_top.php');
$response = array();
$countries_query = tep_db_query("select c.categories_id, cd.categories_name from categories c inner join categories_description cd on c.categories_id=cd.categories_id where (c.parent_id is null or c.parent_id='0') " . (!empty($_GET['term']) ? " and cd.categories_name like '%" . $_GET['term'] . "%' " : "" ) );
while ($entry = tep_db_fetch_array($countries_query)){
    $response[] = array('label' => $entry['categories_name'], 'value' => $entry['categories_id']);
}
echo json_encode($response);
?>