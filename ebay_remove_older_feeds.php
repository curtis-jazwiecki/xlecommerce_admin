<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require_once('cron_application_top.php');

$dirs = array(DIR_FS_ROOT . 'eBay/feeds/', );
$files_to_delete = array();
$cutoff_days = 7;

$current_time = time();
$cutoff_time = $current_time - ($cutoff_days * 24 * 60 * 60);
$count = 0;
foreach($dirs as $dir){
	if (is_dir($dir)){
		if ($dh = opendir($dir)){
			while (($file = readdir($dh))!== false){
				if (is_file($dir . $file)){
					$file_time = filemtime($dir . $file);
					$diff = ($file_time - $cutoff_time);
					if ($diff<0){
						$files_to_delete[] = $dir . $file;
						$count++;
					}
					echo ($diff<0 ? '(' . $count . ')' : '') . $file . ' : ' . date('m-d-Y H:i:s', $file_time) . ' : ' . date('m-d-Y H:i:s', $current_time) . ' : ' . $diff . "\n";
				}
			}
			closedir($dh);
		}
	}
	echo '<br><br>';
}

$count=0;
foreach($files_to_delete as $file){
	$count++;
	echo '(' . $count . ') ' . $file . ' : ' . date('m-d-Y H:i:s', filemtime($file)) . ' deleted' . "\n";
	unlink($file);
}

?>