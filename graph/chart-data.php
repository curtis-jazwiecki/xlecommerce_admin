<?php
include_once("../includes/configure.php");
$path = DIR_FS_ROOT;
$path .= "tmp/awstats/";
if(date('d') == 1) {
   $lastmonth = date('m') - 1;
   if(strlen($lastmonth) == 1)
      $name_of_file = "awstats0" . $lastmonth . date('Y') . "." . STATS_SITE_NAME . ".txt";
    else
      $name_of_file = "awstats" . $lastmonth . date('Y') . "." . STATS_SITE_NAME . ".txt";
} else {
    $name_of_file = "awstats" . date('mY') . "." . STATS_SITE_NAME . ".txt";
 }

for ($i=0;$i<6;$i++) {
 $months[] = array('text' => date("My", strtotime("-$i months")),
                   'filename' => "awstats" . date('mY',strtotime("-$i months")) . "." . STATS_SITE_NAME . ".txt");   
}


foreach ($months as $key => $value) {
$handle = @fopen($path . $value['filename'], "r"); // Open file form read.
if ($handle) {
  while (!feof($handle)) // Loop til end of file.
  {
   $line = fgets($handle, 4096); // Read a line.
   $pattern = "/^TotalVisits/";
   if(preg_match($pattern,$line)) {
      $data = explode(" ", $line);

      $months[$key]['totalvisits'] = $data[1];
    }
    $pattern = "/^TotalUnique/";
   if(preg_match($pattern,$line)) {
      $data = explode(" ", $line);
      $months[$key]['totalunique'] = $data[1];
    }
   }
  fclose($handle); // Close the file.
 } else {
    unset ($months[$key]);
 }
}

foreach ($months as $key => $value) {
    $totalunique_array[] = array($value['text'], $value['totalunique']);

    $recurrent_array[] = array($value['text'], ((int)$value['totalvisits'] - (int)$value['totalunique']));
}
$data=array(

  array(

     'label' => 'Recurrent',

     'color' => '#745fa4',

     'data' => $recurrent_array),

  array(

     'label' => 'Uniques',

     'color' => '#58a7e2',

     'data' => $totalunique_array)

);
  
  echo json_encode($data);

  exit;

?>