<?php
/*
  $Id: countries.php,v 1.28 2003/06/29 22:50:51 hpdl Exp $

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
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
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">

<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3><?php echo HEADING_TITLE; ?>
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading"><?php echo HEADING_TITLE; ?>
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
<!-- body_text //-->
    <td><table class="table table-bordered table-hover">
      <tr>
        <td>
		<table class="table table-bordered table-hover">
          <tr>
            <td><table class="table table-bordered table-hover">
			<form method="post" action="<?php echo FILENAME_COUNTRIES; ?>" name="countries">
			<input type="hidden" name="action" value="update_countries" />
              <tr>
                <td><?php echo TABLE_HEADING_COUNTRY_NAME; ?></td>
                
                <td align="center" colspan="2"><?php echo TABLE_HEADING_COUNTRY_CODES; ?></td>
                <td>Active Country</td>
                <td align="center"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
// Below displays all Countries that are Active - OBN
	for($z=0; $z < sizeof($countries_array_id)-1; $z++)
	  {
?>
          <tr>
            <td><b><?php echo $countries_array_name[$z]; ?></b></td>
            <td align="center" colspan="2"><b><?php echo $countries_array_iso_code_2[$z].' &nbsp; '.$countries_array_iso_code_3[$z]; ?></b></td>
            <td align="left"><b>
            <input type="radio" name="<?php echo $countries_array_id[$z]; ?>" value="true" checked />Yes </b>
            <input type="radio" name="<?php echo $countries_array_id[$z]; ?>" value="false" />No
            <input type="hidden" name="<?php echo $countries_array_id[$z]; ?>_value" value="true" />
            <input type="hidden" name="<?php echo $countries_array_id[$z]; ?>countries_id" value="<?php echo $countries_array_id[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_id[$z]; ?>countries_name" value="<?php echo $countries_array_name[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_id[$z]; ?>countries_iso_code_2" value="<?php echo $countries_array_iso_code_2[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_id[$z]; ?>countries_iso_code_3" value="<?php echo $countries_array_iso_code_3[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_id[$z]; ?>address_format_id" value="<?php echo $countries_array_address_format_id[$z]; ?>" />
            </td>
            <td align="center"><a href="<?php echo $_SERVER['PHP_SELF'] . "?action=edit&t=on&id=" . $countries_array_id[$z];?>" style="color: #222222; font-weight:bold;">[Edit]</a></td>
          </tr>
          
<?php
	  }
	for($z=0; $z < sizeof($countries_array_off_id)-1; $z++)
      {
?>
          <tr>
            <td><?php echo $countries_array_off_name[$z]; ?></td>
            <td align="center" colspan="2"><?php echo $countries_array_off_iso_code_2[$z].' &nbsp; '.$countries_array_off_iso_code_3[$z]; ?></td>
            
            <td align="left">
            <input type="radio" name="<?php echo $countries_array_off_id[$z]; ?>" value="true" />Yes
            <input type="radio" name="<?php echo $countries_array_off_id[$z]; ?>" value="false" checked /><b>No
            <input type="hidden" name="<?php echo $countries_array_off_id[$z]; ?>_value" value="false" />
            <input type="hidden" name="<?php echo $countries_array_off_id[$z]; ?>countried_id" value="<?php echo $countries_array_off_id[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_off_id[$z]; ?>countries_name" value="<?php echo $countries_array_off_name[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_off_id[$z]; ?>countries_iso_code_2" value="<?php echo $countries_array_off_iso_code_2[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_off_id[$z]; ?>countries_iso_code_3" value="<?php echo $countries_array_off_iso_code_3[$z]; ?>" />
            <input type="hidden" name="<?php echo $countries_array_off_id[$z]; ?>address_format_id" value="<?php echo $countries_array_off_address_format_id[$z]; ?>" />
            </b></td>
            <td align="center"><a href="<?php echo $_SERVER['PHP_SELF'] . "?action=edit&t=off&id=" . $countries_array_off_id[$z];?>" style="color: #222222; font-weight:bold;">[Edit]</a></td>
          </tr>
<?php
	  }
?>
			  <tr>
				<td colspan="5" align="right"><input border="0" type="image" title="Update" alt="Update" src="includes/languages/english/images/buttons/button_update_b.gif"></td>
              </tr>
			  </form>
              <tr>
              <td>
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
            <table class="table table-bordered table-hover">
              <tr>
                <td><b>Edit Country</b></td>
              </tr>
            </table>
            <table class="table table-bordered table-hover">
              <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
              <input name="action" value="update_country_info" type="hidden" />
              <input name="id" value="<?php echo $_GET['id']; ?>" type="hidden" />
              <input name="status" value="<?php echo $_GET['t']; ?>" type="hidden" />
              <tr>
                <td>
	              <table class="table table-bordered table-hover">
              		<tr>
                	  <td>Countries Name:</td>
                      <td><input type="text" name="countries_name" value="<?php echo $countries_edit[countries_name]; ?>" style="width: 80px" /></td>
                    </tr>
              		<tr>
                	  <td>Countries Code:<br />(2 digit)</td>
                      <td><input type="text" name="countries_iso_code_2" value="<?php echo $countries_edit[countries_iso_code_2]; ?>" style="width: 50px" /></td>
                    </tr>
              		<tr>
                	  <td>Countries Code:<br />(3 digit)</td>
                      <td><input type="text" name="countries_iso_code_3" value="<?php echo $countries_edit[countries_iso_code_3]; ?>" style="width: 50px" /></td>
                    </tr>
					<tr>
                	  <td colspan="2" align="center"><input border="0" type="image" title=" Update " alt="Update" src="includes/languages/english/images/buttons/button_update.gif"></td>
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
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>