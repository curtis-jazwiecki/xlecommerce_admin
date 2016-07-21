<?php

namespace kcfinder;
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
chdir("..");
chdir("..");
require "core/autoload.php";
$theme = basename(dirname(__FILE__));
$min = new minifier("css");
$min->minify("cache/theme_$theme.css");

?>