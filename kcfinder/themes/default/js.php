<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
namespace kcfinder;

chdir("..");
chdir("..");
require "core/autoload.php";
$theme = basename(dirname(__FILE__));
$min = new minifier("js");
$min->minify("cache/theme_$theme.js");

?>