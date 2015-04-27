<?php
//////////////////////////////////////////
//
//  Implement Webmail
//
//  Jeff Brutsche
//  Outdoor Business Network
//
//////////////////////////////////////////

  require('includes/application_top.php');

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

<table width="100%"  border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
    <td valign="top">
      <table width="100%"  border="0" cellspacing="0" cellpadding="0" summary="Table holding Store Information">
        <tr valign="top">
          <td colspan=2>
            <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
          </td>
        </tr>
        <tr valign="top">
          <td width="100%" align="center">
            <table width="900px" cellpadding="0" cellspacing="0">
              <tr>
        	    <td width="100px" valign="top" align="center">
                  <iframe src="webmail/index.php?email_username=<?php echo LOGIN_EMAIL_USERNAME ;?>&email_password=<?php echo LOGIN_EMAIL_PASSWORD; ?>" scrolling="auto" width="900px" height="650px" border="0px"></iframe>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr valign="top">
          <td colspan=2>
            <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
          </td>
        </tr>
      </table>
    </td>
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