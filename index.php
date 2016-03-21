<?php



/*



  $Id: index.php,v 1.19 2003/06/27 09:38:31 dgw_ Exp $







  osCommerce, Open Source E-Commerce Solutions



  http://www.oscommerce.com







  Copyright (c) 2003 osCommerce







  Released under the GNU General Public License



*/







  require('includes/application_top.php');

  include(DIR_WS_CLASSES . 'awfile.php');







//  include("jawstats/xml_history.php");











  $languages = tep_get_languages();



  $languages_array = array();



  $languages_selected = DEFAULT_LANGUAGE;



  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {



    $languages_array[] = array('id' => $languages[$i]['code'],



                               'text' => $languages[$i]['name']);



    if ($languages[$i]['directory'] == $language) {



      $languages_selected = $languages[$i]['code'];



    }



  }





function get_conversion_rate() { 

    

$dir = DIR_FS_ROOT . 'tmp/awstats/';

$totalvisits= 0;



if (is_dir($dir)){



    if ($handle = opendir($dir)){



        while ( ($file = readdir($handle)) !== false ){



            $pattern = '/^awstats\d*\.[\w\W]+\.txt$/';



            if (preg_match($pattern, $file)){

                  $log = new awfile($dir . $file);

                  $totalvisits +=  $log->GetUniqueVisits();

            } 

        }



        if ($totalvisits > 0) {         

                  $orders_count = count(getOrdersForDashboard());

                  $conversion_rate_in_figures = ($orders_count / $totalvisits);

                  $conversion_rate_in_percent = (int)($conversion_rate_in_figures * 100);

                  return $conversion_rate_in_percent;

       } else {

        return '0';

       }           

    }

   } 

}



function getTotalNumberOfCustomers(){



	$customers_query_raw = tep_db_query("select c.customers_ip_address, c.customers_id, c.customers_lastname, c.customers_firstname, c.customers_email_address, c.customers_group_id, c.customers_group_ra, a.entry_country_id, a.entry_company, cg.customers_group_name from " . TABLE_CUSTOMERS . " c left join " . TABLE_ADDRESS_BOOK . " a on c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id left join " . TABLE_CUSTOMERS_GROUPS . " cg on c.customers_group_id = cg.customers_group_id ");



	return tep_db_num_rows($customers_query_raw);



}







function getOrdersForDashboard($limit = '',$order_status = ''){



	 global $languages_id;



	 



	 $cond = '';



	 if(!empty($order_status)){



	 	$cond = " and s.orders_status_id = '".$order_status."' ";



	 }



	 



	 if(!empty($limit)){



	 	$limit = "limit $limit";



	 }



	 



	 $orders_query_raw = tep_db_query("select o.orders_id, o.customers_id,o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total, o.customer_service_id, o.is_phone_order from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and ot.class = 'ot_total' $cond order by o.orders_id DESC $limit");



	 



	 $data = array();



	 while($result = tep_db_fetch_array($orders_query_raw)){



	 	



		$data[] = array(



		



			'orders_id' 	       => $result['orders_id'],



            'customers_name'       => $result['customers_name'],



            'order_total'    	   => $result['order_total'],



            'orders_status_name'   => $result['orders_status_name']



		



		);



	 



	 }



	



	return $data;



}







function getNewProducts(){



    $sql='SELECT COUNT(*) as total from products where DATEDIFF( CURDATE(),products_date_added) <= 30 AND products_status =1';



    $result = tep_db_query($sql);



    $row = tep_db_fetch_array($result);



    return $row['total'];



}







function getAvgorderAndTotalIncome(&$avg_order,&$total_order_sales){



    require(DIR_WS_CLASSES . 'currencies.php');



  	$currencies = new currencies();



	$sales_report_view = 5;



    $startDate = "";



    $endDate = "";



    require(DIR_WS_CLASSES . 'sales_report.php');



    $report = new sales_report($sales_report_view, $startDate, $endDate);



    



    $last_value = 0;



    $sum = 0;



    $order_cnt = 0;



    



    for ($i = 0; $i < $report->size; $i++) {



        if ($last_value != 0) {



          $percent = 100 * $report->info[$i]['sum'] / $last_value - 100;



        } else {



          $percent = "0";



        }



        $sum += $report->info[$i]['sum'];



        $avg += $report->info[$i]['avg'];



        $last_value = $report->info[$i]['sum'];



        $order_cnt += $report->info[$i]['count'];



    }



    



    $avg_order = $currencies->format($sum / $order_cnt);



    $total_order_sales = $currencies->format($sum);



}



?>



<body>



<?php include("includes/header.php"); ?> 







<!-- START Main section-->



      <section>



         <!-- START Page content-->



         <section class="main-content">



            <!--<button type="button" class="btn btn-labeled btn-primary pull-right">



               <span class="btn-label"><i class="fa fa-plus-circle"></i>



               </span>Add Item</button>-->



            <h3>



               Dashboard



               <br>



               <small>Welcome user</small>



            </h3>



            



            <?php



              $orders_contents = '';



              $orders_contents2 = '';



              $orders_status_query = tep_db_query("select orders_status_name, orders_status_id from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "' LIMIT 0,6");



			  // Get total Number of Products in the store



				$products_total = tep_db_query("select * from " . TABLE_PRODUCTS );



				$products_count_total = tep_db_num_rows($products_total);







              //Below Displays Order information



			    $orders_pending_query = tep_db_query("select count(*) as count from " . TABLE_ORDERS . " where orders_status = '1'");



                $orders_pending = tep_db_fetch_array($orders_pending_query);







                $orders_contents .= '<a href="' . tep_href_link(FILENAME_ORDERS, 'selected_box=customers&status=' . $orders_status['orders_status_id']) . '"> New Orders: ' . $orders_pending['count'] . '</a><br>';



                



			  // Get path to home directory



			  $path = DIR_FS_DOCUMENT_ROOT;



			  $path = substr($path,0,-12);



			  // individual site configuration



				$path .= "tmp/awstats/";



				if(date('d') == 1)



				  {



					$lastmonth = date('m') - 1;



					if(strlen($lastmonth) == 1)



						$name_of_file = "awstats0" . $lastmonth . date('Y') . "." . STATS_SITE_NAME . ".txt";



					else



						$name_of_file = "awstats" . $lastmonth . date('Y') . "." . STATS_SITE_NAME . ".txt";



				  }



				else



				  {



					$name_of_file = "awstats" . date('mY') . "." . STATS_SITE_NAME . ".txt";



				  }



				$count = 0;



				$count2 = 0;



				$hits_arr = array();



				$hits_arr2 = array();



				$handle = @fopen($path . $name_of_file, "r"); // Open file form read.



	



			if ($handle)



			  {



				while (!feof($handle)) // Loop til end of file.



				  {



					$line = fgets($handle, 4096); // Read a line.



					$pattern = "/BEGIN_DAY/";



					if(preg_match($pattern,$line))



					  {



						$begin_day_pos = $count;



						fgets($handle);



					  }



					$pattern = "/END_DAY/";



					if(preg_match($pattern,$line))



					  {



						$end_day_pos = $count;



					  }



					$hits_arr2[$count] = $line;



					$count++;



				  }



				fclose($handle); // Close the file.



			  }



//			echo $begin_day_pos . ' - begin day <br />';



//			echo $end_day_pos . ' - end day <br />';



			



			for($z=$begin_day_pos; $z < $end_day_pos; $z++)



			  {



//				echo $hits_arr2[$z] . ' - ' . $z . '<br />';



				$today = $hits_arr2[$z];



			  }



			$hits_arr = explode(" ", $today);



			?>



            <div class="row">



               <!-- START dashboard main content-->



               <div class="col-md-9">



                  <!-- START summary widgets-->



                  <div class="row">



                     <div class="col-lg-3 col-sm-6">



                        <!-- START widget-->



                        <div data-toggle="play-animation" data-play="fadeInDown" data-offset="0" data-delay="100" class="panel widget"> 



                           <a href="customers.php?selected_box=customers">



                           <div class="panel-body bg-primary">



                              <div class="row row-table row-flush" style="max-height:40px;">



                                 <div class="col-xs-8">



                                    <p class="mb0">Customers</p>



                                    <h3 class="m0"><?php echo getTotalNumberOfCustomers();//echo $hits_arr[2]; ?></h3>



                                 </div>



                                 <div class="col-xs-4 text-center">



                                    <em class="fa fa-user fa-2x"><sup class="fa fa-plus"></sup>



                                    </em>



                                 </div>



                              </div>



                           </div>



                           </a>



                           <div class="panel-body">



                              <!-- Bar chart-->



                              <div class="text-center">



                                 <div data-bar-color="primary" data-height="30" data-bar-width="6" data-bar-spacing="6" class="inlinesparkline inline">



                                    5,3,4,6,5,9,4,4,10,5,9,6,4</div>



                              </div>



                           </div>



                        </div>



                     </div>



                     <div class="col-lg-3 col-sm-6">



                        <!-- START widget-->



                        <div data-toggle="play-animation" data-play="fadeInDown" data-offset="0" data-delay="500" class="panel widget">



                           <a href="categories.php">



                           <div class="panel-body bg-warning">



                              <div class="row row-table row-flush" style="max-height:40px;">



                                 <div class="col-xs-8">



                                    <p class="mb0">Total products</p>



                                    <h3 class="m0"><?php echo $products_count_total; ?></h4></h3>



                                 </div>



                                 <div class="col-xs-4 text-center">



                                    <em class="fa fa-users fa-2x"></em>



                                 </div>



                              </div>



                           </div>



                           </a>



                           <div class="panel-body">



                              <!-- Bar chart-->



                              <div class="text-center">



                                 <div data-bar-color="warning" data-height="30" data-bar-width="6" data-bar-spacing="6" class="inlinesparkline inline">



                                    10,30,40,70,50,90,70,50,90,40,40,60,40</div>



                              </div>



                           </div>



                        </div>



                     </div>



                     <div class="col-lg-3 col-sm-6">



                        <!-- START widget-->



                        <div data-toggle="play-animation" data-play="fadeInDown" data-offset="0" data-delay="1000" class="panel widget">



                        <a href="orders.php">



                           <div class="panel-body bg-danger">



                              <div class="row row-table row-flush" style="max-height:40px;">



                                 <div class="col-xs-8">



                                    <p class="mb0">Orders</p>



                                    <h3 class="m0"><?php echo count(getOrdersForDashboard()); ?></h3>



                                 </div>



                                 <div class="col-xs-4 text-center">



                                    <em class="fa fa-search fa-2x"></em>



                                 </div>



                              </div>



                           </div>



                           </a>



                           <div class="panel-body">



                              <!-- Bar chart-->



                              <div class="text-center">



                                 <div data-bar-color="danger" data-height="30" data-bar-width="6" data-bar-spacing="6" class="inlinesparkline inline">



                                    2,7,5,9,4,2,7,5,7,5,9,6,4</div>



                              </div>



                           </div>



                        </div>



                     </div>



                     <div class="col-lg-3 col-sm-6">



                        <!-- START widget-->



                        <div data-toggle="play-animation" data-play="fadeInDown" data-offset="0" data-delay="1500" class="panel widget"> 



                           <a href="products_expected.php">



                           <div class="panel-body bg-success">



                              <div class="row row-table row-flush" style="max-height:40px;">



                                 <div class="col-xs-8">



                                    <p class="mb0">New Products</p>



                                    <h3 class="m0"><?php echo getNewProducts(); ?></h3>



                                 </div>



                                 <div class="col-xs-4 text-center">



                                    <em class="fa fa-globe fa-2x"></em>



                                 </div>



                              </div>



                           </div>



                           </a>



                           <div class="panel-body">



                              <!-- Bar chart-->



                              <div class="text-center">



                                 <div data-bar-color="success" data-height="30" data-bar-width="6" data-bar-spacing="6" class="inlinesparkline inline">



                                    4,7,5,9,6,4,8,6,3,4,7,5,9</div>



                              </div>



                           </div>



                        </div>



                     </div>



                  </div>



                  <!-- END summary widgets-->



                  <!-- START chart-->



                  <div class="row">



                     <div class="col-lg-12">



                        <div class="panel panel-default">



                           <div class="panel-collapse">



                              <div class="panel-body">



                                 <div style="height: 350px;" data-source="graph/chart-data.php" class="chart-area flot-chart"><canvas class="flot-base" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 1061px; height: 250px;" width="1061" height="250"></canvas></div>



                              </div>



                           </div>



                        </div>



                     </div>



                  </div>



                  <!-- END chart-->



                  <!-- START Secondary Widgets-->



                  <div class="row">



                     <div class="col-md-4">



                        <!-- START widget-->



                        <div data-toggle="play-animation" data-play="fadeInLeft" data-offset="0" data-delay="1400" class="panel widget">



                          <a href="conversion_rate_report.php">



                           <div class="panel-body">



                              <div class="text-right text-muted">



                                 <em class="fa fa-users fa-2x"></em>



                              </div>



                              <h3 class="mt0">

                              <?php 

                              $conversion_rate = get_conversion_rate();

                              echo $conversion_rate; ?>%</h3>



                              <p class="text-muted">Conversion Rate</p>



                              <div class="progress progress-striped progress-xs">



                                 <div role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $conversion_rate; ?>%;" class="progress-bar progress-bar-success">



                                    <span class="sr-only">80% Complete</span>



                                 </div>



                              </div>



                           </div>



                           </a>



                        </div>



                        <!-- END widget-->



                     </div>



                     



                     <?php



					 $avg_order = 0;



					 $total_order_sales = 0;



					 getAvgorderAndTotalIncome($avg_order,$total_order_sales);



					 ?>



                     <div class="col-md-4">



                        <!-- START widget-->



                        <div data-toggle="play-animation" data-play="fadeInLeft" data-offset="0" data-delay="1400" class="panel widget">



                        <a href="stats_sales_report.php?report=5">



                           <div class="panel-body">



                              <div class="text-right text-muted">



                                 <em class="fa fa-bar-chart-o fa-2x"></em>



                              </div>



                              <h3 class="mt0"><?php echo $avg_order; ?></h3>



                              <p class="text-muted">Average Order</p>



                              <div class="progress progress-striped progress-xs">



                                 <div role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%;" class="progress-bar progress-bar-info">



                                    <span class="sr-only">40% Complete</span>



                                 </div>



                              </div>



                           </div>



                           </a>



                        </div>



                        <!-- END widget-->



                     </div>



                     <div class="col-md-4">



                        <!-- START widget-->



                        <div data-toggle="play-animation" data-play="fadeInLeft" data-offset="0" data-delay="1400" class="panel widget">



                           <a href="stats_sales_report.php?report=5">



                           <div class="panel-body">



                              <div class="text-right text-muted">



                                 <em class="fa fa-trophy fa-2x"></em>



                              </div>



                              <h3 class="mt0"><?php echo $total_order_sales; ?></h3>



                              <p class="text-muted">Total Income</p>



                              <div class="progress progress-striped progress-xs">



                                 <div role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;" class="progress-bar progress-bar-warning">



                                    <span class="sr-only">60% Complete</span>



                                 </div>



                              </div>



                           </div>



                           </a>



                        </div>



                        <!-- END widget-->



                     </div>



                  </div>



                  <!-- END Secondary Widgets-->



                  <!-- START table-->



                  <div class="row">



                     <div class="col-lg-12">



                        <!-- START panel-->



                        <div class="panel panel-default">



                           <div class="panel-heading">Pending Orders



                              <a href="#" data-perform="panel-dismiss" data-toggle="tooltip" title="Close Panel" class="pull-right">



                                 <em class="fa fa-times"></em>



                              </a>



                              <a href="#" data-perform="panel-collapse" data-toggle="tooltip" title="Collapse Panel" class="pull-right">



                                 <em class="fa fa-minus"></em>



                              </a>



                           </div>



                           <!-- START table-responsive-->



                           <div class="table-responsive">



                              <table class="table table-striped table-bordered table-hover">



                                 <thead>



                                    <tr>



                                       <th>Name</th>



                                       <th>Order Status</th>



                                       <th>Amount</th>



                                       <th class="text-center">Action</th>



                                    </tr>



                                 </thead>



                                 <tbody>



                                 <?php



								 $dashboard_orders = getOrdersForDashboard(3,1);



                                 if(count($dashboard_orders) > 0){



								 	foreach($dashboard_orders as $orders){ ?>



								 		



                                        <tr>



                                           <td><?php echo $orders['customers_name']; ?></td>



                                           <td><?php echo $orders['orders_status_name']; ?></td>



                                           <td><?php echo $orders['order_total']; ?></td>



                                           <td class="text-center">



                                            <div class="btn-group">



                                            	<a href="<?php echo tep_href_link(FILENAME_ORDERS_EDIT,'oID='.$orders['orders_id']); ?>">



                                                	<i class="fa fa-cog"></i>



                                                </a>



                                            </div>



                                           </td>



                                        </tr>



                                   



								   <?php



                                    }



								 }else{?>



                                 



                                 	<tr>



                                       <td colspan="4" align="center">No records present..</td>



                                    </tr>



								 



								 <?php



                                 }?>



                                    



                                 </tbody>



                              </table>



                           </div>



                           <!-- END table-responsive-->



                           <div class="panel-footer text-right">



                              <a href="<?php echo tep_href_link(FILENAME_ORDERS); ?>">



                                 <small>View all</small>



                              </a>



                           </div>



                        </div>



                        <!-- END panel-->



                     </div>



                  </div>



                  <!-- END table-->



               </div>



               <!-- END dashboard main content-->



               <!-- START dashboard sidebar-->



               <div class="col-md-3">



                  <!-- START messages-->



                  <div class="panel panel-default">



                     <div class="panel-heading">



                        <!--<div class="pull-right label label-info">33</div>-->



                        <div class="panel-title">What's New</div>



                     </div>



                     <!-- START list group-->



                     <div class="list-group">



                        <!-- START list group item-->



                        <!-- START panel-->



                  <div class="panel panel-default" style="margin-bottom:0;">



                     <div class="panel-body" style="height:435px; overflow:scroll; overflow-x: hidden;">



                       <?php



		// set name of XML file		



		$file = "http://67.227.172.78/admin/rss_feeds/obn_rss_feed.xml";







		// load file



		$xml = simplexml_load_file($file) or die ("Unable to load XML file!");







		//	echo "$count" . $xml->getName() . "<br />";



			$count=0;



			$count2=0;



			$output_array_name = array();



			$output_array_value = array();



			$output_array_name_2 = array();



			$output_array_value_2 = array();



			



			foreach($xml->children() as $child)



			  {



				foreach($child->children() as $child_child)



				  {



//					echo "&nbsp;&nbsp;&nbsp;&nbsp;" . "$count" . $child_child->getName() . ": " . $child_child . "<br />";



					$output_array_name[$count] .= $child_child->getName();



					$output_array_value[$count] .= $child_child;



					$count++;







					foreach($child_child->children() as $child_child_child)



					  {



//						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . "$count2" . $child_child_child->getName() . ": " . $child_child_child . "<br />";



						$output_array_name_2[$count2] .= $child_child_child->getName();



						$output_array_value_2[$count2] .= stripslashes($child_child_child);



						$count2++;



					  }



				  }



			  }



		// Display from the feed



			echo '<span style="font-family: Verdana, Geneva, sans-serif;">';



			echo "<a href='http://www.outdoorbusinessnetwork.com'><img src='" . $output_array_value_2[1] . "' title='" . $output_array_value_2[0] . "' width='". $output_array_value_2[3] ."' height='". $output_array_value_2[4] ."' border='0' align='center' style='margin: 0px auto;' /></a>";



			for($x=0;$x<5;$x++)



			  {



				if($output_array_value_2[(5 + (4*$x))] != "" && $output_array_value_2[(7 + (4*$x))] != "" && $output_array_value_2[(8 + (4*$x))] != "")



				  {



					if($output_array_value_2[(6 + (4*$x))]!='')



						echo "<a href='". $output_array_value_2[(6 + (4*$x))] ."' style='color:#000000'>";



					echo "<div style='margin: 10px 0px 2px 10px; font-size: 20px'>". $output_array_value_2[(5 + (4*$x))] ."</div>";



					if($output_array_value_2[(6 + (4*$x))]!='')



						echo "</a>";



					echo "<div style='font-size: 10px; color:#404040; margin: 0 0 0 25px;'>Date Added - ". $output_array_value_2[(8 + (4*$x))];



					if($output_array_value_2[(6+ (4*$x))]!='')



						echo " - <a href='". $output_array_value_2[(6 + (4*$x))] ."' style='color:#28a5dd'>Read More</a>";



					echo "</div>";



					echo "<div style='font-size: 10px; color:#404040; margin: 3px 10px; font-size: 14px'>". html_entity_decode($output_array_value_2[(7 + (4*$x))]) ."</div>";



				  }



			  }



			echo '</span>';



        ?>



                  </div>



                  <!-- END panel-->



                        <!-- END list group item-->



                     </div>



                     <!-- END list group-->



                     <!-- START panel footer-->



                     <!--<div class="panel-footer clearfix">



                        <a href="#" class="pull-left">



                           <small>Read All</small>



                        </a>



                        <a href="#" class="pull-right">



                           <small>Dismiss All</small>



                        </a>



                     </div>-->



                     <!-- END panel-footer-->



                  </div>



                  </div>



                  <!-- END messages-->



                  <!-- START activity-->



                  <div class="panel panel-default">



                     <div class="panel-heading">



                        <div class="panel-title">New Stuff</div>



                     </div>



                     <!-- START list group-->



                     <div class="list-group">



                        <!-- START list group item-->



                        <div class="panel panel-default" style="margin-bottom:0;">



                     <div class="panel-body" style="height:435px; overflow:scroll; overflow-x: hidden;">



                       <?php



		// set name of XML file		



		$file = "http://67.227.172.78/admin/rss_feeds/obn_manufacturer_rss_feed.xml";







		// load file



		$xml = simplexml_load_file($file) or die ("Unable to load XML file!");







		//	echo "$count" . $xml->getName() . "<br />";



			$count=0;



			$count2=0;



			unset($output_array_name);



			unset($output_array_value);



			unset($output_array_name_2);



			unset($output_array_value_2);



			



			foreach($xml->children() as $child)



			  {



				foreach($child->children() as $child_child)



				  {



//					echo "&nbsp;&nbsp;&nbsp;&nbsp;" . "$count" . $child_child->getName() . ": " . $child_child . "<br />";



					$output_array_name[$count] .= $child_child->getName();



					$output_array_value[$count] .= $child_child;



					$count++;







					foreach($child_child->children() as $child_child_child)



					  {



//						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . "$count2" . $child_child_child->getName() . ": " . $child_child_child . "<br />";



						$output_array_name_2[$count2] .= $child_child_child->getName();



						$output_array_value_2[$count2] .= stripslashes($child_child_child);



						$count2++;



					  }



				  }



			  }



		// Display from the feed



			echo '<span style="font-family: Verdana, Geneva, sans-serif;">';



			echo "<a href='http://www.outdoorbusinessnetwork.com'><img src='" . $output_array_value_2[1] . "' title='" . $output_array_value_2[0] . "' width='". $output_array_value_2[3] ."' height='". $output_array_value_2[4] ."' border='0' align='center' style='margin: 0px auto;' /></a>";



			for($x=0;$x<5;$x++)



			  {



				if($output_array_value_2[(5 + (4*$x))] != "" && $output_array_value_2[(7 + (4*$x))] != "" && $output_array_value_2[(8 + (4*$x))] != "")



				  {



					if($output_array_value_2[(6 + (4*$x))]!='')



						echo "<a href='". $output_array_value_2[(6 + (4*$x))] ."' style='color:#000000'>";



					echo "<div style='margin: 10px 0px 2px 10px; font-size: 20px'>". $output_array_value_2[(5 + (4*$x))] ."</div>";



					if($output_array_value_2[(6 + (4*$x))]!='')



						echo "</a>";



					echo "<div style='font-size: 10px; color:#404040; margin: 0 0 0 25px;'>Date Added - ". $output_array_value_2[(8 + (4*$x))];



					if($output_array_value_2[(6+ (4*$x))]!='')



						echo " - <a href='". $output_array_value_2[(6 + (4*$x))] ."' style='color:#28a5dd'>Reade More</a>";



					echo "</div>";



					echo "<div style='font-size: 10px; color:#404040; margin: 3px 10px; font-size: 14px'>". html_entity_decode($output_array_value_2[(7 + (4*$x))]) ."</div>";



				  }



			  }



			 echo '</span>';



        ?>



                  </div>



                  <!-- END panel-->



                 </div>



                        <!-- END list group item-->



                     </div>



                     <!-- END list group-->



                     <!-- START panel footer-->



                     <!--<div class="panel-footer clearfix">



                        <a href="#" class="pull-left">



                           <small>Load more</small>



                        </a>



                     </div>-->



                     <!-- END panel-footer-->



                  </div>



               </div>



               <!-- END dashboard sidebar-->



            </div>



        











<!-- body_eof //-->







<!-- footer //-->



<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>



<!-- footer_eof //-->







<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>



