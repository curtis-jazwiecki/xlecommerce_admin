<?php
/*
  $Id: countries.php,v 1.28 2003/06/29 22:50:51 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

require('includes/application_top.php');

$action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

/*$query = tep_db_query("select COUNT(countries_id) from countries");
$num = tep_db_fetch_array($query);
$number = $num['COUNT(countries_id)'];
$query = tep_db_query("select COUNT(*) from countries_off");
$num = tep_db_fetch_array($query);
$number += $num['COUNT(countries_id)'];*/

$number = 300;



if($_POST['action'] == "update_countries")
  {
	  
	for($z=0; $z < $number; $z++)
	  {
		if($_POST[$z.'_value'] == "true" && $_POST[$z] == "false")
		  {
			tep_db_query("INSERT INTO countries_off (countries_id,countries_name,countries_iso_code_2,countries_iso_code_3,address_format_id) VALUES ('".$z."', '" .  $_POST[$z.'countries_name']."', '" .  $_POST[$z.'countries_iso_code_2']."', '" . $_POST[$z.'countries_iso_code_3']."', '" . $_POST[$z.'address_format_id']."')");
			tep_db_query("DELETE FROM countries WHERE countries_id = '".$z."'");
		  }
		elseif($_POST[$z.'_value'] == "false" && $_POST[$z] == "true")
		  {
			tep_db_query("INSERT INTO countries (countries_id,countries_name,countries_iso_code_2,countries_iso_code_3,address_format_id) VALUES ('".$z."', '" .  $_POST[$z.'countries_name']."', '" .  $_POST[$z.'countries_iso_code_2']."', '" . $_POST[$z.'countries_iso_code_3']."', '" . $_POST[$z.'address_format_id']."')");
			tep_db_query("DELETE FROM countries_off WHERE countries_id = '".$z."'");
		  }
	  }
  }
elseif($_POST['action'] == "update_country_info")
  {
	if($_POST['status'] == "on")
		$table_name = 'countries';
	elseif($_POST['status'] == "off")
		$table_name = 'countries_off';
		
	tep_db_query("Update ".$table_name." SET countries_name = '".$_POST['countries_name']."', countries_iso_code_2 = '".$_POST['countries_iso_code_2']."', countries_iso_code_3 = '".$_POST['countries_iso_code_3']."' WHERE countries_id = '".$_POST['id']."'");
  }

	$countries_array_id[$z] = array();
	$countries_array_name[$i] =  array();
	$countries_array_iso_code_2[$z] =  array();
	$countries_array_iso_code_3[$z] =  array();
	$countries_array_off_id[$z] =  array();
	$countries_array_off_name[$i] =  array();
	$countries_array_off_iso_code_2[$z] =  array();
	$countries_array_off_iso_code_3[$z] =  array();

	$countries_query_raw = tep_db_query("select c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id from countries c order by c.countries_name");
	$i=0;
	while($countries_query = tep_db_fetch_array($countries_query_raw))
      {
		$countries_array_id[$i] = $countries_query['countries_id'];
		$countries_array_name[$i] = $countries_query['countries_name'];
		$countries_array_iso_code_2[$i] = $countries_query['countries_iso_code_2'];
		$countries_array_iso_code_3[$i] = $countries_query['countries_iso_code_3'];
		$countries_array_address_format_id[$i] = $countries_query['address_format_id'];
		$i++;
	  }

	$countries_query_raw = tep_db_query("select c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id from countries_off c order by c.countries_name");
	$i=0;
	while($countries_query = tep_db_fetch_array($countries_query_raw))
      {
		$countries_array_off_id[$i] = $countries_query['countries_id'];
		$countries_array_off_name[$i] = $countries_query['countries_name'];
		$countries_array_off_iso_code_2[$i] = $countries_query['countries_iso_code_2'];
		$countries_array_off_iso_code_3[$i] = $countries_query['countries_iso_code_3'];
		$countries_array_off_address_format_id[$i] = $countries_query['address_format_id'];
		$i++;
	  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table width="780" border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
			<form method="post" action="<?php echo FILENAME_COUNTRIES; ?>" name="countries">
			<input type="hidden" name="action" value="update_countries" />
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" width="35%"><?php echo TABLE_HEADING_COUNTRY_NAME; ?></td>
                
                <td class="dataTableHeadingContent" align="center" colspan="2" width="27%"><?php echo TABLE_HEADING_COUNTRY_CODES; ?></td>
                <td class="dataTableHeadingContent" width="30%">Active Country</td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
// Below displays all Countries that are Active - OBN
	for($z=0; $z < sizeof($countries_array_id)-1; $z++)
	  {
?>
          <tr bgcolor="#DDDDDD">
            <td class="dataTableContent"><b><?php echo $countries_array_name[$z]; ?></b></td>
            <td class="dataTableContent" align="center" colspan="2"><b><?php echo $countries_array_iso_code_2[$z].' &nbsp; '.$countries_array_iso_code_3[$z]; ?></b></td>
            <td class="dataTableContent" align="left"><b>
            <input type="radio" name="<?php echo $countries_array_id[$z]; ?>" value="true" checked />Yes </b>
            <input type="radio" name="<?php echo $countries_array_id[$z]; ?>" value="false" />No
            <input type="hidden" name="<?php echo $countries_array_id[$z]; ?>_value" value="true" />
            <input type="hidden" name="<?php echo $countries_array_id[$z]; ?>countries_id" value="<?php echo $countries_array_id[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_id[$z]; ?>countries_name" value="<?php echo $countries_array_name[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_id[$z]; ?>countries_iso_code_2" value="<?php echo $countries_array_iso_code_2[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_id[$z]; ?>countries_iso_code_3" value="<?php echo $countries_array_iso_code_3[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_id[$z]; ?>address_format_id" value="<?php echo $countries_array_address_format_id[$z]; ?>" />
            </td>
            <td class="dataTableContent" align="center"><a href="<?php echo $_SERVER['PHP_SELF'] . "?action=edit&t=on&id=" . $countries_array_id[$z];?>" style="color: #222222; font-weight:bold;">[Edit]</a></td>
          </tr>
          
<?php
	  }
	for($z=0; $z < sizeof($countries_array_off_id)-1; $z++)
      {
?>
          <tr bgcolor="#DDDDDD">
            <td class="dataTableContent"><?php echo $countries_array_off_name[$z]; ?></td>
            <td class="dataTableContent" align="center" colspan="2"><?php echo $countries_array_off_iso_code_2[$z].' &nbsp; '.$countries_array_off_iso_code_3[$z]; ?></td>
            
            <td class="dataTableContent" align="left">
            <input type="radio" name="<?php echo $countries_array_off_id[$z]; ?>" value="true" />Yes
            <input type="radio" name="<?php echo $countries_array_off_id[$z]; ?>" value="false" checked /><b>No
            <input type="hidden" name="<?php echo $countries_array_off_id[$z]; ?>_value" value="false" />
            <input type="hidden" name="<?php echo $countries_array_off_id[$z]; ?>countried_id" value="<?php echo $countries_array_off_id[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_off_id[$z]; ?>countries_name" value="<?php echo $countries_array_off_name[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_off_id[$z]; ?>countries_iso_code_2" value="<?php echo $countries_array_off_iso_code_2[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_off_id[$z]; ?>countries_iso_code_3" value="<?php echo $countries_array_off_iso_code_3[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_off_id[$z]; ?>address_format_id" value="<?php echo $countries_array_off_address_format_id[$z]; ?>" />
            </b></td>
            <td class="dataTableContent" align="center"><a href="<?php echo $_SERVER['PHP_SELF'] . "?action=edit&t=off&id=" . $countries_array_off_id[$z];?>" style="color: #222222; font-weight:bold;">[Edit]</a></td>
          </tr>
<?php
	  }
?>
			  <tr style="background-color: #030C2C">
				<td colspan="5" align="right"><input border="0" type="image" title="Update" alt="Update" src="includes/languages/english/images/buttons/button_update_b.gif"></td>
              </tr>
			  </form>
              <tr>
              <td bgcolor="#FFFFFF">
              <?php echo "Total Active Countries: <b>".(sizeof($countries_array_id)-1).'</b><br /> Total In-Active Countries: <b>'.(sizeof($countries_array_off_id)-1).'</b>'; ?>
              </td>
              <td colspan="4">&nbsp;</td>
              </tr>

            </table></td>
<?php
	if($_GET['action'] == 'edit'){
		
		if($_GET['t'] == "on")
			$table_name = 'countries';
		elseif($_GET['t'] == "off")
			$table_name = 'countries_off';
		
		$countries_edit_raw = tep_db_query("SELECT c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id FROM ".$table_name." c WHERE countries_id = '". $_GET['id'] ."' ORDER BY c.countries_name");
		$countries_edit = tep_db_fetch_array($countries_edit_raw);
		$countries_edit[countries_id];
		$countries_edit[countries_name];
		$countries_edit[countries_iso_code_2];
		$countries_edit[countries_iso_code_3];
?>
		  <td valign="top" width="25%">
            <table width="100%" cellpadding="2" cellspacing="0">
              <tr class="infoBoxHeading">
                <td class="infoBoxHeading"><b>Edit Country</b></td>
              </tr>
            </table>
            <table width="100%" cellpadding="2" cellspacing="0">
              <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
              <input name="action" value="update_country_info" type="hidden" />
              <input name="id" value="<?php echo $_GET['id']; ?>" type="hidden" />
              <input name="status" value="<?php echo $_GET['t']; ?>" type="hidden" />
              <tr>
                <td class="infoBoxContent">
	              <table width="100%" cellpadding="2" cellspacing="0">
              		<tr class="infoBoxContent" valign="top">
                	  <td class="infoBoxContent">Countries Name:</td>
                      <td class="infoBoxContent"><input type="text" name="countries_name" value="<?php echo $countries_edit[countries_name]; ?>" style="width: 80px" /></td>
                    </tr>
              		<tr class="infoBoxContent" valign="top">
                	  <td class="infoBoxContent">Countries Code:<br />(2 digit)</td>
                      <td class="infoBoxContent"><input type="text" name="countries_iso_code_2" value="<?php echo $countries_edit[countries_iso_code_2]; ?>" style="width: 50px" /></td>
                    </tr>
              		<tr class="infoBoxContent" valign="top">
                	  <td class="infoBoxContent">Countries Code:<br />(3 digit)</td>
                      <td class="infoBoxContent"><input type="text" name="countries_iso_code_3" value="<?php echo $countries_edit[countries_iso_code_3]; ?>" style="width: 50px" /></td>
                    </tr>
					<tr class="infoBoxContent" valign="top">
                	  <td class="infoBoxContent" colspan="2" align="center"><input border="0" type="image" title=" Update " alt="Update" src="includes/languages/english/images/buttons/button_update.gif"></td>
                    </tr>
                  </table>
                </td>
              </tr>
              </form>
            </table>
          </td>
<?php
	  
	}
?>
          </tr>
        </table></td>        
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>