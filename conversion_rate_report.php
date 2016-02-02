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

            $pattern = '/^awstats\d*\.[\w\W]+\.txt$/';

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

<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">



<!-- header //-->

<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

<!-- header_eof //-->



<!-- body //-->



         <section>

         <!-- START Page content-->

         <section class="main-content">

            <h3>Conversion Rate Report

               <br>

            </h3>

            <!-- START panel-->

            <div class="panel panel-default">

               <div class="panel-heading">Conversion Rate Report

                  <a href="#" data-perform="panel-dismiss" data-toggle="tooltip" title="Close Panel" class="pull-right">

                     <em class="fa fa-times"></em>

                  </a>

                  <a href="#" data-perform="panel-collapse" data-toggle="tooltip" title="Collapse Panel" class="pull-right">

                     <em class="fa fa-minus"></em>

                  </a>

               </div>

               <!-- START table-responsive-->

               

               <div class="table-responsive">

               <!-- START your table-->

<table class="table table-bordered table-hover">

            <tr>

                <td>

                    <table class="table table-bordered table-hover">

                        <tr>

                            <td align="right">

                            <?php

                            echo '<a href="' . tep_href_link('conversion_rate_report.php', 'type=m', 'NONSSL') . '">Monthly</a>  <a href="' . tep_href_link('conversion_rate_report.php', 'type=y', 'NONSSL') . '">Yearly</a>  ';

                            ?>

                            </td>

                        </tr>

                    </table>

                </td>

            </tr>

            <tr>

                <td>

                   <table class="table table-bordered table-hover">

                        <tr>

                            <td><b><?php echo $heading_1; ?></b></td>

                            <td align="right"><b>Orders (T)</b></td>

                            <td align="right"><b>Unique Visits (V)</b></td>

                            <td align="right"><b>Conversion Rate (T/V)</b></td>

                            <td align="right"><b>Conversion Rate (in %)</b></td>

                        </tr>

                        <?php foreach($rows as $id => $row) {

                            $xaxis .= '"'.$row['unit'].'", ';

                            $yaxis .= number_format($row['conversion_rate_in_percent'], 2).', ';

                        ?>

                        

                        <tr>

                            <td>

                            <?php echo $row['unit']; ?>

                            </td>

                            <td align="right">

                            <?php echo $row['orders']; ?>

                            </td>

                            <td align="right">

                            <?php echo $row['unique_visits']; ?>

                            </td>

                            <td align="right">

                            <?php echo number_format($row['conversion_rate_in_figures'], 3) ; 

                            ?>

                            </td>

                            <td align="right">

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

                <td>

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

        </table>

               <!-- END your table-->

<!-- body_eof //-->



<!-- footer //-->

<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>

<!-- footer_eof //-->



<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>