<?php 
require('includes/application_top.php');
include(DIR_WS_CLASSES . 'awfile.php');
//include(DIR_WS_CLASSES . 'sales_report.php');

function get_orders_count($start='', $end=''){
    $orders_query = tep_db_query("select count(orders_id) as count from orders where date_purchased between '" . date('Y-m-d H:m:s', $start) . "' and '" . date('Y-m-d H:m:s', $end) . "'");
    $count = tep_db_fetch_array($orders_query);
    return $count['count'];
}

$type = empty($_GET['type']) ? 'm' : $_GET['type'];

$dir = DIR_FS_ROOT . 'tmp/awstats/';
$files = array();
$years = array();

if (is_dir($dir)){
    if ($handle = opendir($dir)){
        while ( ($file = readdir($handle)) !== false ){
            //filename format: awstats022014.obnv6.com.txt
            //$pattern = '/^awstats[0-9]*\.[0-9|a-z|A-Z]*\.[A-Z|a-z]*\.txt$/';
            $pattern = '/^awstats\d*\.[\d|\w]*\.[\w]*\.txt$/';
            if (preg_match($pattern, $file)){
                $month = substr($file, 7, 2);
                $year = substr($file, 9, 4);
                if (!in_array($year, $years)) $years[] = $year;
                $start_date_ts = mktime(0, 0, 0, $month, 1, $year);
                
                $end_day = date('t', $start_date_ts);
                $end_date_ts = mktime(23, 59, 59, $month, $end_day, $year);
                
                $files[$year . $month] = array(
                    'file' => $dir . $file, 
                    'month' => (int)$month, 
                    'year' => (int)$year,
                    'start_date_ts' => $start_date_ts,
                    'end_date_ts' => $end_date_ts, 
                    'start_date' => date('d-M-Y', $start_date_ts), 
                    'end_date' => date('d-M-Y', $end_date_ts),
                );
            }
        }
        closedir($handle);
    }
    krsort($files);
}

$rows = array();
switch ($type){
    case 'm':
        $heading_1 = 'Month-Year';
        foreach($files as $file){
            $month = str_pad($file['month'], 2, 0, STR_PAD_LEFT);
            $log = new awfile($file['file']);
            $orders_count = get_orders_count($file['start_date_ts'], $file['end_date_ts']);
            $conversion_rate_in_figures = ($orders_count / $log->GetUniqueVisits());
            $conversion_rate_in_percent = $conversion_rate_in_figures * 100;
            $rows[$month . $file['year']] = array(
                'unit' => $month . '-' . $file['year'], 
                'orders' => $orders_count,
                'unique_visits' =>  $log->GetUniqueVisits(), 
                'conversion_rate_in_figures' => $conversion_rate_in_figures, 
                'conversion_rate_in_percent' => $conversion_rate_in_percent,
            );
            
        }
        break;
    case 'y':
        $heading_1 = 'Year';
        foreach($files as $file){
            if (!array_key_exists($file['year'], $rows)) $rows[$file['year']] = array();
            $log = new awfile($file['file']);
            $orders_count = get_orders_count($file['start_date_ts'], $file['end_date_ts']);
            //$conversion_rate_in_figures = $orders_count / $log->GetUniqueVisits();
            //$conversion_rate_in_percent = $conversion_rate_in_figures * 100;
            
            if (empty($rows[$file['year']]['unit'])){
                $rows[$file['year']]['unit'] = $file['year'];
                $rows[$file['year']]['conversion_rate_in_figures'] = 0;
                $rows[$file['year']]['conversion_rate_in_percent'] = 0;
            }

            $rows[$file['year']]['orders'] += $orders_count;
            $rows[$file['year']]['unique_visits'] += $log->GetUniqueVisits();

        }
        
        foreach($rows as $id => $row){
            $rows[$id]['conversion_rate_in_figures'] = $row['orders'] / $row['unique_visits'];
            $rows[$id]['conversion_rate_in_percent'] = $rows[$id]['conversion_rate_in_figures'] * 100;
        }
        break;
}

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
        <title><?php echo TITLE; ?></title>
        <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    </head>
    <body style="margin: 0;">
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  
        <table width="780" border="0" align="center" cellpadding="2" cellspacing="2">
            <tr>
                <td width="100%" valign="top">
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                            <td>
                                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="pageHeading">
                                            Conversion Rate Report
                                        </td>
                                        <td class="pageHeading" align="right">
                                        <?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                            <td align="right" class="link4">
                            <?php
                            echo '<a href="' . tep_href_link('conversion_rate_report.php', 'type=m', 'NONSSL') . '">Monthly</a>  <a href="' . tep_href_link('conversion_rate_report.php', 'type=y', 'NONSSL') . '">Yearly</a>  ';
                            ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td valign="top" style="background-color:white;width:100%;">
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                            <td class="smallText"><b><?php echo $heading_1; ?></b></td>
                            <td class="smallText" align="right"><b>Orders (T)</b></td>
                            <td class="smallText" align="right"><b>Unique Visits (V)</b></td>
                            <td class="smallText" align="right"><b>Conversion Rate (T/V)</b></td>
                            <td class="smallText" align="right"><b>Conversion Rate (in %)</b></td>
                        </tr>
                        <?php foreach($rows as $id => $row) {
                            $xaxis .= '"'.$row['unit'].'", ';
                            $yaxis .= number_format($row['conversion_rate_in_percent'], 2).', ';
                        ?>
                        
                        <tr>
                            <td class="smallText">
                            <?php echo $row['unit']; ?>
                            </td>
                            <td class="smallText" align="right">
                            <?php echo $row['orders']; ?>
                            </td>
                            <td class="smallText" align="right">
                            <?php echo $row['unique_visits']; ?>
                            </td>
                            <td class="smallText" align="right">
                            <?php echo number_format($row['conversion_rate_in_figures'], 3) ; 
                            ?>
                            </td>
                            <td class="smallText" align="right">
                            <?php echo number_format($row['conversion_rate_in_percent'], 2) . '%' ; 
                            ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </td>
            </tr>
            <!--BOF BARCHART 05-March-2014-->    
            <tr>
                <td style="background-color: #fff;">
                    <link rel="stylesheet" type="text/css" hrf="jqplot/jquery.jqplot.min.css" />
                    <script class="include" type="text/javascript" src="jqplot/jquery.min.js"></script>
                    <!--<div><span>You Clicked: </span><span id="info1">Nothing yet</span></div>-->
        
    <div id="chart1" style="margin-top:20px; margin-left:20px; width:800px; height:400px;"></div>
    <script class="code" type="text/javascript">
        jQuery(document).ready(function(){
       jQuery.jqplot.config.enablePlugins = true;
       //var s1 = [2, 6, 7, 10];
       var s1 = [<?php echo substr($yaxis,0,-2);?>];
       //var ticks = ['a', 'b', 'c', 'd'];
       var ticks = [<?php echo substr($xaxis,0,-2);?>];
        
       plot1 = jQuery.jqplot('chart1', [s1], {
           // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
           animate: !jQuery.jqplot.use_excanvas,
            series: [
            {color: 'rgba(255, 0, 0, 0.9)'},
            
            ],
           seriesDefaults:{
               renderer:jQuery.jqplot.BarRenderer,
               pointLabels: { show: true }
           },
           axes: {
               
               xaxis: {
                   autoscale: true ,
                   renderer: jQuery.jqplot.CategoryAxisRenderer,
                   ticks: ticks,
                   min: 4,
                   max: 6
               }
           },
           highlighter: { show: false }
       });
    
       jQuery('#chart1').bind('jqplotDataClick', 
           function (ev, seriesIndex, pointIndex, data) {
               jQuery('#info1').html('series: '+seriesIndex+', point: '+pointIndex+', data: '+data);
           }
       );
   });
    </script>
      
    <script type="text/javascript" src="jqplot/jquery.jqplot.min.js"></script>
    <script type="text/javascript" src="jqplot/plugins/jqplot.barRenderer.min.js"></script>
    <script type="text/javascript" src="jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
    <script type="text/javascript" src="jqplot/plugins/jqplot.pointLabels.min.js"></script>
    
       
                </td>
            </tr>
             <!--EOF BARCHART 05-March-2014-->
            <tr>
                <td>
                <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
                </td>
            </tr>
        </table>
    </body>
</html>