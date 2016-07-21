<?php

/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require ('includes/application_top.php');



if( isset($_POST['action']) && $_POST['action'] == 'save_variations'){

	

	if(is_array($_POST['variation_id'])){

	

		foreach($_POST['variation_id'] as $key => $value){

			if($value != ''){

				tep_db_query("insert into products_variations set products_id = '".tep_db_prepare_input($_POST['pID'])."',variation_id = '".tep_db_prepare_input($key)."',value = '".tep_db_prepare_input($value)."' on duplicate key update value = '".tep_db_prepare_input($value)."'");

			}

		

		}

		echo '<script>alert("variation added/updated successfully..."); window.close(); </script>';	

	}

}



?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">

<html <?php  echo HTML_PARAMS; ?>>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">

<link rel="stylesheet" type="text/css" href="includes/stylesheet.css" />

</head>



<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="goOnLoad();">

<div style="width:100%; font-family:Verdana, Geneva, sans-serif;">

<form name="frm_amazon" id="frm_amazon" method="post" action="manage_amazon_variations.php">

<input type="hidden" name="action" value="save_variations">

<input type="hidden" name="pID" value="<?php echo $_GET['pID']; ?>">



<table width="50%" cellspacing="5" cellpadding="5" align="center" style="margin: 0px auto; color:#FFFFFF; background-color:#999999; margin-top:20px; border-radius: 5px;">

 <tr>

 	<th align="left" style="border-bottom:1px solid #FFF;">Variation Name</th>

    <th align="left" style="border-bottom:1px solid #FFF;">Variation Value</th>

 </tr>

    	<?php

		$query1 = tep_db_fetch_array(tep_db_query("select variation_ids from amazon_variation_themes where variation_theme_id = '".(int)$_GET['variation_theme_id']."'"));

		$query = tep_db_query("select a.*, p.value from amazon_variations a left join products_variations p on a.variation_id=p.variation_id and p.products_id='" . $_GET['pID'] . "' where a.variation_id IN (".$query1['variation_ids'].")");

		

		while($data = tep_db_fetch_array($query)){

			echo '<tr><td align="left">'.$data['variation_name'].':</td>';

			echo '<td align="left">' .  tep_draw_input_field('variation_id['.$data['variation_id'].']', $data['value'], 'id="'.$data['variation_id'].'"') . '</td></tr>';

		}?>

        

    

  <tr>

    <td align="center" colspan="2"><input type="submit" name="btn-submit" value="ADD">&nbsp;<input type="button" value="Close" onClick="ClosePOPup();"></td>

  </tr>

</table>



</form>

</div>

<script type="text/javascript">

function ClosePOPup(){

	window.close();

}

</script>

</body>

</html>

<?php

require (DIR_WS_INCLUDES . 'application_bottom.php');

?>