<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
//////////////////////////////////////////
//
//  Load training videos
//
//  Jeff Brutsche
//  Outdoor Business Network
//
//////////////////////////////////////////

  require('includes/application_top.php');

  $my_account_query = tep_db_query ("select a.admin_id, a.admin_firstname, a.admin_lastname, a.admin_email_address, a.admin_created, a.admin_modified, a.admin_logdate, a.admin_lognum, g.admin_groups_name from " . TABLE_ADMIN . " a, " . TABLE_ADMIN_GROUPS . " g where a.admin_id= " . $login_id . " and g.admin_groups_id= " . $login_groups_id . "");
  $myAccount = tep_db_fetch_array($my_account_query);
  define('STORE_ADMIN_NAME',$myAccount['admin_firstname'] . ' ' . $myAccount['admin_lastname']);
  define('TEXT_WELCOME','Welcome <strong>' . STORE_ADMIN_NAME . '</strong> to <strong>' . STORE_NAME . '</strong> Administration!');

// Store Status code 
if (DOWN_FOR_MAINTENANCE == 'false'){
  $store_status = '<font color="#009900">Active</font>';
  } else {
  $store_status = '<font color="#FF0000">Maintanace</font>';
  }
// Store Status Code EOF


//Customer Count Code
$customer_query = tep_db_query("select count(customers_id) as customercnt from " . TABLE_CUSTOMERS);
$customercount = tep_db_fetch_array($customer_query);
define('CUSTOMER_COUNT',$customercount['customercnt']);

//Customer Subscribed Count Code
$customer_query = tep_db_query("select count(customers_id) as customercnt from " . TABLE_CUSTOMERS." where customers_newsletter=1");
$customercount = tep_db_fetch_array($customer_query);
define('CUSTOMER_SUBSCRIBED_COUNT',$customercount['customercnt']);





//get path of directory containing this script
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- Code related to index.php only -->
<link type="text/css" rel="StyleSheet" href="includes/index.css" />
<link type="text/css" rel="StyleSheet" href="includes/helptip.css" />
<script type="text/javascript" src="includes/javascript/helptip.js"></script>
<!-- code related to index.php EOF -->
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3>
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading">
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
   <table class="table table-bordered table-hover" summary="Table holding Store Information">
    <tr>
    <td colspan=2>
    <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>    </td>
    </tr>
    <tr>
	  <td>
		<table class="table table-bordered table-hover">
          <tr>
        	<td align="left">
    <?php
	/*
	// Select OBN DV for ADMIN Videos - OBN
	mysql_select_db("obn123_joomla") or die('Could not connect: ' . mysql_error());

	$vid_id = $_GET['vid_id'];
	
	$vid_menu_query = tep_db_query('SELECT id, title, introtext, state, sectionid, catid, created, ordering FROM jos_content WHERE sectionid=\'12\' AND state=\'1\' order by ordering');
	while ($row = tep_db_fetch_array($vid_menu_query))
		{
			echo '<a href="training_videos.php?vid_id='.$row['id'].'" style="color: #444">'.$row['title'].'</a><br />';
		}
	?>			</td>
			<td width="*" valign="top" align="left" bgcolor="#FFFFFF">
	<?php
	if($vid_id != '')
	  {
		$vid_query = tep_db_query('SELECT title, introtext, state, sectionid, catid, created FROM jos_content WHERE id=\''.$vid_id.'\'');
		while ($row = tep_db_fetch_array($vid_query))
		  {
			$introtext = str_replace('param value="/images/', 'param value="http://www.outdoorbusinessnetwork.com/images/', $row['introtext']);
			$introtext = str_replace('thumb=/images/','src="thumb=http://www.outdoorbusinessnetwork.com/images/',$introtext);
			$introtext = str_replace('src="images/','src="http://www.outdoorbusinessnetwork.com/images/',$introtext);
			$introtext = str_replace('templates/siteground-j15-35/','http://www.outdoorbusinessnetwork.com/templates/siteground-j15-35', $introtext);
			$introtext = str_replace('<a href=', '<a style="color: #444" href', $introtext);
			echo "$introtext";
		  }
	  }
	else
	  {
		$vid_query = tep_db_query('SELECT title, introtext, state, sectionid, catid, created FROM jos_content WHERE id=\'83\' and sectionid=\'12\'');
		while ($row = tep_db_fetch_array($vid_query))
		  {
			$introtext = str_replace('param value="/images/', 'param value="http://www.outdoorbusinessnetwork.com/images/', $row['introtext']);
			$introtext = str_replace('thumb=/images/','src="thumb=http://www.outdoorbusinessnetwork.com/images/',$introtext);
			$introtext = str_replace('src="images/','src="http://www.outdoorbusinessnetwork.com/images/',$introtext);
			$introtext = str_replace('templates/siteground-j15-35/images/training_videos_header.jpg','http://www.outdoorbusinessnetwork.com/templates/siteground-j15-35/images/training_videos_header.jpg', $introtext);
			$introtext = str_replace('<a href=', '<a style="color: #444" href', $introtext);
			echo "$introtext";
		  }
	  }
str_replace("","",$row['introtext']);

// Go back to standard DB
mysql_select_db(DB_DATABASE) or die('Could not connect: ' . mysql_error());

    ?>            </td>
          </tr>
        </table>	  </td>
    </tr>
<?php
// Select Database back to default DB
mysql_select_db("DB_DATABASE");
*/
?>
    <tr>
    <td colspan=2>
    <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>    </td>
    </tr>
    </table>
    
    <!--BLOCK CODE ENDS -->
   
     
      <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
      <table class="table table-bordered table-hover" summary="Footer Banner Table">
        <tr>
          <td align="center">      </td>
        </tr>
      </table></td>
  </tr>
</table>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>