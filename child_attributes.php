<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require ('includes/application_top.php');



require (DIR_WS_CLASSES . 'currencies.php');



$currencies = new currencies();



$action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');



?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">

<html <?php  echo HTML_PARAMS; ?>><head>



<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">


<link rel="stylesheet" type="text/css" href="includes/stylesheet.css" />

<!-- AJAX Attribute Manager  -->
<?php require_once( 'attributeManager/includes/attributeManagerHeader.inc.php' )?>
<!-- AJAX Attribute Manager  end -->


</head>


<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="goOnLoad();">

<table border="0" width="780px" cellspacing="2" cellpadding="2" align="center" style="margin: 0px auto;">

 <!-- AJAX Attribute Manager  -->
          <tr>
          	<td colspan="2"><?php require_once( 'attributeManager/includes/attributeManagerPlaceHolder.inc.php' )?></td>
          </tr>
<!-- AJAX Attribute Manager end -->
</table>


<table cellspacing="2" cellpadding="2" width="100%">
	<tr>
    	<td align="center" colspan="2" ><input type="button" value="Close" onClick="ClosePOPup();"></td>
    </tr>
</table>
<script type="text/javascript">
function ClosePOPup(){
	//window.opener.refreshParentpage(window.opener.location.href+'&update_options=1');
	window.opener.showaddedProducts();
	window.close();
}
</script>
</body>
</html>

<?php

require (DIR_WS_INCLUDES . 'application_bottom.php');

?>