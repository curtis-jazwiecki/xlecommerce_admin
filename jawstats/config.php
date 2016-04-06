<?php

  require_once "../includes/configure.php";

  // core config parameters
  $sConfigDefaultView    = "thismonth.all";
  $bConfigChangeSites    = true;
  $bConfigUpdateSites    = true;
  $sUpdateSiteFilename   = "xml_update.php";


  // Get path to home directory
  $path = DIR_FS_DOCUMENT_ROOT;
  $path = substr($path,0,-12);
	
  // individual site configuration
  $aConfig[STATS_SITE_NAME] = array(
	"statspath"   => $path."tmp/awstats/ssl/",
	"statsname"   => "awstats[MM][YYYY]." . STATS_SITE_NAME . ".txt",
	"updatepath"  => "/usr/local/cpanel/base/awstats.pl/",
	"siteurl"     => HTTP_SERVER,
	"theme"       => "default",
	"fadespeed"   => 250,
	"password"    => "",
	"includes"    => ""
  );
/*
$aConfig["site1"] = array(
  "statspath"   => "/path/to/data/",
  "updatepath"  => "/path/to/awstats.pl/",
  "siteurl"     => "http://www.my-1st-domain.com",
  "sitename"    => "My 1st Domain",
  "theme"       => "default",
  "fadespeed"   => 250,
  "password"    => "my-1st-password",
  "includes"    => "",
  "language"    => "en-gb"
);
*/
?>
