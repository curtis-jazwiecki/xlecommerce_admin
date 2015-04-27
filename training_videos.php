<?php
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<!-- Code related to index.php only -->
<link type="text/css" rel="StyleSheet" href="includes/index.css" />
<link type="text/css" rel="StyleSheet" href="includes/helptip.css" />
<script type="text/javascript" src="includes/javascript/helptip.js"></script>
<!-- code related to index.php EOF -->
</head>
<body>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<table width="780"  border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
    <td valign="top">
      <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
   
      <!--BLOCK CODE START-->
    
     <table width="100%"  border="0" cellspacing="0" cellpadding="0" summary="Table holding Store Information">
    <tr valign="top">
    <td colspan=2>
    <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>    </td>
    </tr>
    <tr valign="top">
	  <td width="100%" align="center">
		<table width="800px" cellpadding="5px" cellspacing="0">
          <tr>
        	<td width="100px" valign="top" align="left" bgcolor="#FFFFFF">
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
    <tr valign="top">
    <td colspan=2>
    <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>    </td>
    </tr>
    </table>
    
    <!--BLOCK CODE ENDS -->
   
     
      <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0" summary="Footer Banner Table">
        <tr>
          <td align="center">      </td>
        </tr>
      </table></td>
  </tr>
</table>

<!-- former position of disclaimer  -->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>