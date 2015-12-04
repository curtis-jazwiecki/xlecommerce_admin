<?php
/*
  $Id: index.php,v 1.19 2003/06/27 09:38:31 dgw_ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

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


?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css" />
<style type="text/css"><!--
body {
background-image:url(../images/template/admin_bg.jpg);
background-repeat:repeat-x;
}
a { color:#080381; text-decoration:none; }
a:hover { color:#aabbdd; text-decoration:underline; }
a.text:link, a.text:visited { color: #000; text-decoration: none; }
a:text:hover { color: #000; text-decoration: underline; }
a.main:link, a.main:visited { color: #000; text-decoration: none; }
A.main:hover { color: #999; text-decoration: underline; }
a.sub:link, a.sub:visited { color: #222; text-decoration: none; }
A.sub:hover { color: #555; text-decoration: underline; }
.heading { font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 20px; font-weight: bold; line-height: 1.5; color: #D3DBFF; }
.main { font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 17px; font-weight: bold; line-height: 1.5; color: #999; }
.sub { font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.5; color: #dddddd; }
.text { font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; line-height: 1.5; color: #000000; }
.menuBoxHeading { font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 12px; color: #ffffff; font-weight: bold; background-color: #999; background-image:url(../images/OBN_r_header.jpg); padding: 1px 15px;  }
.infoBox { font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 12px; color: #080381; background-color: #f2f4ff; border-color: #7187bb; border-style: solid; border-width: 1px; }
.smallText { font-family: Calibri, Arial, sans-serif; font-size: 12px; }
body{ margin: 0px; }

a.headerLink {color:#FFFFFF; font-family:Verdana,Arial,sans-serif; font-size:10px; font-weight:bold; text-decoration:none;}




//--></style>

<style type="text/javascript">
startList = function()
  {
	if (document.all&&document.getElementById)
	  {
		navRoot = document.getElementById("nav");
		for (i=0; i<navRoot.childNodes.length; i++)
		  {
			node = navRoot.childNodes[i];
			if (node.nodeName=="LI")
			  {
				node.onmouseover=function()
				  {
					this.className+=" over";
				  }
				node.onmouseout=function()
				  {
					this.className=this.className.replace(" over", "");
				  }
			  }
		  }
	  }
  }
window.onload=startList;
</style>

</head>
<body bgcolor="#030c2c">
<?php include('includes/header.php'); ?>

<?php // the following is hidden, but forces a login to the webmail on admin login if user has credentials in system ?>
<iframe src="webmail/index.php?email_username=<?php echo LOGIN_EMAIL_USERNAME ;?>&email_password=<?php echo LOGIN_EMAIL_PASSWORD; ?>" scrolling="auto" width="756px" height="650px" border="0px" style="visibility: hidden; display: none "></iframe>

<table border="0" width="760" cellspacing="0" cellpadding="0"  style="margin: 5px auto;" align="center">
  <tr>
    <td>
      <table border="0" width="760" cellspacing="0" cellpadding="0" align="center">
        <tr>
          <td>
            <table border="0" width="760" cellspacing="0" cellpadding="0">
			  <tr>
              	<td colspan="3"> 
                <table width="760" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td align="center" width="108"><a href="index.php"><span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold; z-index: 99;">Home</span><br /><img src="<?php echo HTTP_SERVER; ?>/images/my_account.jpg" width="67" height="67" border="0" /></a><br /><img src="../images/my_account_r.jpg"></td>
                    <td align="center" width="108"><a href="categories.php"><span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold">Products</span><br /><img src="<?php echo HTTP_SERVER; ?>/images/related_products.jpg" width="67" height="67" border="0" /></a><br /><img src="../images/related_products_r.jpg"></td>
                    <td align="center" width="108"><a href="price_updater.php"><span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold; z-index: 99;">Pricing</span><br /><img src="<?php echo HTTP_SERVER; ?>/images/catalog.jpg" width="67" height="67" border="0" /></a><br /><img src="../images/catalog_r.jpg" height="43"></td>
                    <td align="center" width="108"><a href="xsell.php"><span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold">X-Sell</span><br /><img src="<?php echo HTTP_SERVER; ?>/images/related_products.jpg" width="67" height="67" border="0" /></a><br /><img src="../images/related_products_r.jpg"></td>
                    <td align="center" width="108"><a href="orders.php"><span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold">Orders</span><br /><img src="<?php echo HTTP_SERVER; ?>/images/customers.jpg" width="67" height="67" border="0" /></a><br /><img src="../images/customers_r.jpg"></td>
                                        <td align="center" width="108"><a href="create_order.php"><span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold">Create Order</span><br /><img src="<?php echo HTTP_SERVER; ?>/images/create_order.jpg" width="67" height="67" border="0" /></a><br /><img src="../images/create_order_r.jpg"></td>
                                        <td align="center" width="108"><a href="stats_sales_report.php"><span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold">Sales Reports</span><br /><img src="<?php echo HTTP_SERVER; ?>/images/sales_report.jpg" width="67" height="67" border="0" /></a><br /><img src="../images/sales_report_r.jpg"></td>
                  </tr>
                </table>
                <table width="760" border="0" cellspacing="0" cellpadding="0" style="margin-top: -30px;">
                  <tr>
                  <td align="center" width="108"><br /><a href="webmail.php"><span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold">Webmail</span><br /><img src="<?php echo HTTP_SERVER; ?>/images/email.jpg" width="67" height="67" border="0" /></a><br /><img src="../images/email_r.jpg"></td>
                    <td align="center" width="108"><br /><a href="advanced_stats.php"><span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold">Stats</span><br /><img src="<?php echo HTTP_SERVER; ?>/images/seo.jpg" width="67" height="67" border="0" /></a><br /><img src="../images/seo_r.jpg"></td>
                                        <td align="center" width="108"><br /><a href="support_ticket.php"><span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold">Support</span><br /><img src="<?php echo HTTP_SERVER; ?>/images/support.jpg" width="67" height="67" border="0" /></a><br /><img src="../images/support_r.jpg"></td>
                                    
                    <td align="center" width="108"><br /><a href="information_manager.php?gID=2"><span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold">Edit Home Page</span><br /><img src="<?php echo HTTP_SERVER; ?>/images/home_page.jpg" width="67" height="67" border="0" /></a><br /><img src="../images/home_page_r.jpg"></td>
                    <td align="center" width="108"><br /><a href="information_manager.php?selected_box=information"><span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold">Edit Info Pages</span><br /><img src="<?php echo HTTP_SERVER; ?>/images/info_page.jpg" width="67" height="67" border="0" /></a><br /><img src="../images/info_page_r.jpg"></td>
                    <td align="center" width="108"><br /><a href="range_operations.php"><span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold">Range</span><br /><img src="<?php echo HTTP_SERVER; ?>/images/admin_buttons_range_manager.jpg" width="67" height="67" border="0" /></a><br /><img src="../images/range_r.jpg"></td>
                     <td align="center" width="108"><br /><a href="create_order_process_POS.php"><span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold">Point of Sale</span><br /><img src="<?php echo HTTP_SERVER; ?>/images/admin_buttons_pos.jpg" width="67" height="67" border="0" /></a><br /><img src="../images/pos_r.jpg"></td> 
                  </tr>
                </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<?php // After icons ?>

<table border="0" width="760" cellspacing="0" cellpadding="0" style="margin: -5px auto 0px auto;" align="center">
<tr valign="top">
  <td width="760px" colspan="3">
    <table border="0" cellpadding="0" cellspacing="0" width="760px">
      <tr>
        <td background="../images/whats_new_header_760.png" style="z-index: 100;">
            <span style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#FFFFFF; margin: 0 0 0 20px">&nbsp;Store Statistics</span>
        </td>
      </tr>
      <tr height="5px" bgcolor="#FFFFFF">
        <td></td>
      </tr>
      <tr bgcolor="#ffffff">
        <td>
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
          <table width="740px" cellspacing="0" cellpadding="0" align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; margin: 0 0 0 20px">
            <tr height="30px" style="color: #000000" valign="middle">
              <td width="24%">
                <img src="images/icons/evnelope.png" width="25px" height="25px" style="margin: 4px 10px 4px 0; float: left" />
                <div style="float: left; margin-top: 10px"><a href="categories.php" style="color: #111; font-family: Verdana, Geneva, sans-serif; font-size: 10px">Total products: <?php echo $products_count_total; ?></a></div></td>
              <td width="19%">
                <img src="images/icons/orders.png" width="25px" height="25px" style="margin: 0 10px 0 0; float: left" />
                <div style="float: left; margin-top: 5px"> <?php echo str_replace('<a ','<a style="color: #111; " ',$orders_contents); ?></div></td>
              <td width="19%">
                <img src="images/icons/stats.png" width="25px" height="25px" style="margin: 0 10px 0 0; float: left" />
                <div style="float: left; margin-top: 5px"><a href="advanced_stats.php" style="color: #111; font-family: Verdana, Geneva, sans-serif; font-size: 10px">Hits today: <?php echo $hits_arr[2]; ?></a></div></td>
              <td width="19%">
                <img src="images/icons/stats2.png" width="25px" height="25px" style="margin: 0 10px 0 0; float: left" />
                <div style="float: left; margin-top: 5px"><a href="advanced_stats.php" style="color: #111; font-family: Verdana, Geneva, sans-serif; font-size: 10px">Page views: <?php echo $hits_arr[1]; ?></a></div></td>
              <td width="19%">
                <img src="images/icons/stats3.png" width="25px" height="25px" style="margin: 0 10px 0 0; float: left" />
                <div style="float: left; margin-top: 5px"><a href="advanced_stats.php"><a href="advanced_stats.php" style="color: #111; font-family: Verdana, Geneva, sans-serif; font-size: 10px">Visitors: <?php echo $hits_arr[4]; ?></a></div></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td><img src="../images/whats_new_bottom_760.png" width="760px" height="10px" style="margin: 0 0 0 0" /></td>
      </tr>
    </table>
  </td>
</tr>
<tr height="20px">
  <td colspan="3">
  </td>
</tr>
<tr valign="top">
  <td width="360px">
    <table border="0" cellpadding="0" cellspacing="0" width="360px">
      <tr>
        <td background="../images/whats_new_header.png" style="z-index: 100;">
            <span style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#FFFFFF; margin: 0 0 0 20px">&nbsp;What's New</span>
        </td>
      </tr>
      <tr height="360px" bgcolor="#ffffff">
        <td>
        <div style="height: 360px; width: 360px; overflow-y: scroll; overflow-x: hidden">
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
					echo "<div style='font-size: 10px; color:#404040; margin: 3px 10px; font-size: 14px'>". $output_array_value_2[(7 + (4*$x))] ."</div>";
				  }
			  }
			echo '</span>';
        ?>
        </div>
        </td>
      </tr>
      <tr>
        <td><img src="../images/whats_new_bottom.png" width="360px" height="10px" style="margin: 0 0 0 0" /></td>
      </tr>
    </table>
  </td>
  <td width="40">
  </td>
  <td width="360px" valign="top">
    <table border="0" cellpadding="0" cellspacing="0" width="360px">
      <tr>
        <td background="../images/whats_new_header.png">
            <span style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#FFFFFF; margin: 0 0 0 20px">New Stuff</span>
        </td>
      </tr>
      <tr height="360px" bgcolor="#ffffff">
        <td>
        <div style="height: 360px; width: 360px; overflow-y: scroll; overflow-x: hidden">
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
					echo "<div style='font-size: 10px; color:#404040; margin: 3px 10px; font-size: 14px'>". $output_array_value_2[(7 + (4*$x))] ."</div>";
				  }
			  }
			 echo '</span>';
        ?>
        </div>
        </td>
      </tr>
      <tr>
        <td><img src="../images/whats_new_bottom.png" width="360px" height="10px" style="margin: 0 0 0 0" /></td>
      </tr>
    </table>
  </td>
</tr>
<tr>
</table>
</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>