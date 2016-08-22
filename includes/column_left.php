<?php
/*
  $Id: column_left.php,v 1.15 2002/01/11 05:03:25 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

  
  if (tep_admin_check_boxes('administrator.php') == true) {
    require(DIR_WS_BOXES . 'administrator.php');
  } 
  if (tep_admin_check_boxes('configuration.php') == true) {
    require(DIR_WS_BOXES . 'configuration.php');
  } 
  if (tep_admin_check_boxes('catalog.php') == true) {
    require(DIR_WS_BOXES . 'catalog.php');
  } 
  if (tep_admin_check_boxes('modules.php') == true) {
    require(DIR_WS_BOXES . 'modules.php');
  } 
  if (tep_admin_check_boxes('customers.php') == true) {
    require(DIR_WS_BOXES . 'customers.php');
  } 
  
  if (tep_admin_check_boxes('gv_admin.php') == true) {
    require(DIR_WS_BOXES . 'gv_admin.php');
  } 
  
  if (tep_admin_check_boxes('taxes.php') == true) {
    require(DIR_WS_BOXES . 'taxes.php');
  } 
  if (tep_admin_check_boxes('localization.php') == true) {
    require(DIR_WS_BOXES . 'localization.php');
  } 
  
  if (tep_admin_check_boxes('information.php') == true) {
    require(DIR_WS_BOXES . 'information.php');
  } 
  
  if (tep_admin_check_boxes('reports.php') == true) {
    require(DIR_WS_BOXES . 'reports.php');
  } 
  if (tep_admin_check_boxes('training_support.php') == true) {
    require(DIR_WS_BOXES . 'training_support.php');
  } 
  if (tep_admin_check_boxes('tools.php') == true) {
    require(DIR_WS_BOXES . 'tools.php');
  }
  
  if (tep_admin_check_boxes('header_tags_controller.php') == true) {
    require(DIR_WS_BOXES . 'header_tags_controller.php');
  }
  
?>
