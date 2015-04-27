<?php
/*
  $Id: login.php,v 1.17 2003/02/14 12:57:29 dgw_ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LOGIN);
  
  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process')) {
    $email_address = tep_db_prepare_input($HTTP_POST_VARS['email_address']);
    $firstname = tep_db_prepare_input($HTTP_POST_VARS['firstname']);
    $log_times = $HTTP_POST_VARS['log_times']+1;
    if ($log_times >= 4) {
      tep_session_register('password_forgotten');
    }
      
// Check if email exists
    $check_admin_query = tep_db_query("select admin_id as check_id, admin_firstname as check_firstname, admin_lastname as check_lastname, admin_email_address as check_email_address from " . TABLE_ADMIN . " where admin_email_address = '" . tep_db_input($email_address) . "'");
    if (!tep_db_num_rows($check_admin_query)) {
      $HTTP_GET_VARS['login'] = 'fail';
    } else {
      $check_admin = tep_db_fetch_array($check_admin_query);
      if ($check_admin['check_firstname'] != $firstname) {
        $HTTP_GET_VARS['login'] = 'fail';
      } else {
        $HTTP_GET_VARS['login'] = 'success';
        
        function randomize() {
          $salt = "ABCDEFGHIJKLMNOPQRSTUVWXWZabchefghjkmnpqrstuvwxyz0123456789";
          srand((double)microtime()*1000000); 
          $i = 0;
    
          while ($i <= 7) {
            $num = rand() % 33;
    	    $tmp = substr($salt, $num, 1);
    	    $pass = $pass . $tmp;
    	    $i++;
  	  }
  	  return $pass;
        }
        $makePassword = randomize();
      
        tep_mail($check_admin['check_firstname'] . ' ' . $check_admin['admin_lastname'], $check_admin['check_email_address'], ADMIN_EMAIL_SUBJECT, sprintf(ADMIN_EMAIL_TEXT, $check_admin['check_firstname'], HTTP_SERVER . DIR_WS_ADMIN, $check_admin['check_email_address'], $makePassword, STORE_OWNER), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);            
        tep_db_query("update " . TABLE_ADMIN . " set admin_password = '" . tep_encrypt_password($makePassword) . "' where admin_id = '" . $check_admin['check_id'] . "'");
      }
    }
  }

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<style type="text/css">
<!--
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
//.login {font-family: Verdana, Arial, sans-serif; font-size: 12px; color: #000000;}
.login_heading {font-family: Verdana, Arial, sans-serif; font-size: 12px; color: #ffffff;}
.smallText1 {font-family: Verdana, Arial, sans-serif; font-size: 10px; }
--></style>
</head>
<body bgcolor="#030c2c">

<table border="0" width="760" cellspacing="0" cellpadding="0" style="margin: 0px auto; background-color: #fff">
  <tr>
    <td>
      <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#26a5dc" style="font: Georgia, 'Times New Roman', Times, serif; margin: 0px; font-size: 12px; background-repeat:repeat-x">
        <tr valign="center" style="height: 24px; max-height: 24px; ">
          <td></td>
        </tr>
      </table>
      <table border="0" width="760" cellspacing="0" cellpadding="0" background="../images/template/header2.jpg">
        <tr height="100">
          <td width="550" height="100">
            <?php //echo tep_image(DIR_WS_IMAGES . 'outdoorbusinessnetwork.gif', 'Outdoor Business Network', '204', '50'); ?>
          </td>
          <td width="50">
          </td>
          <td align="right" class="text" width="160" nowrap>
            <?php //echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . HEADER_TITLE_ADMINISTRATION . '</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="' . tep_catalog_href_link() . '">' . HEADER_TITLE_ONLINE_CATALOG . '</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="http://www.outdoorbusinessnetwork.net/training" target="_blank">' . HEADER_TITLE_SUPPORT_SITE . '</a>'; ?>&nbsp;&nbsp;
          </td>
        </tr>
        <tr>
          <td colspan="3" style="background-color: #999">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" background="../images/template/nav_bg.jpg" style="font: Georgia, 'Times New Roman', Times, serif; margin: 0px; font-size: 12px; background-repeat:repeat-x">
              <tr valign="center" style="height: 24px;">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                  <td width="300px">
                    <div style="padding-left: 20px"></div>
                  </td>
                  <td width="160px" align="center">&nbsp;</td>
                  <td width="100px" align="center" style="float: right;">&nbsp;</td><td>&nbsp;</td>
                </form>
                  <td width="*" align="center">&nbsp;</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<?php // Before icons ?>
<table border="0" width="760" cellspacing="0" cellpadding="0"  style="margin: 15px auto;">
  <tr>
    <td>
      <table border="0" width="760" cellspacing="0" cellpadding="0" align="center">
        <tr>
          <td>
            <table border="0" width="760" cellspacing="0" cellpadding="0">
			  <tr>
              	<td colspan="3" align="center" valign="middle"><?php echo tep_draw_form('login', FILENAME_PASSWORD_FORGOTTEN, 'action=process'); ?>
                            <table width="280" border="0" cellspacing="0" cellpadding="2">
                              <tr>
                                <td class="login_heading" valign="top">&nbsp;<b><?php echo HEADING_PASSWORD_FORGOTTEN; ?></b></td>
                              </tr>
                              <tr>
                                <td height="100%" width="100%" valign="top" align="center">
                                <table border="0" height="100%" width="100%" cellspacing="0" cellpadding="1" bgcolor="#666666">
                                  <tr><td><table border="0" width="100%" height="100%" cellspacing="3" cellpadding="2" bgcolor="#F0F0FF">

<?php
  if ($HTTP_GET_VARS['login'] == 'success') {
    $success_message = TEXT_FORGOTTEN_SUCCESS;
  } elseif ($HTTP_GET_VARS['login'] == 'fail') {
    $info_message = TEXT_FORGOTTEN_ERROR;
  }
  if (tep_session_is_registered('password_forgotten')) {
?>
                                    <tr>
                                      <td class="smallText"><?php echo TEXT_FORGOTTEN_FAIL; ?></td>
                                    </tr>
                                    <tr>
                                      <td align="center" valign="top"><?php echo '<a href="' . tep_href_link(FILENAME_LOGIN, '' , 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
                                    </tr>
<?php
  } elseif (isset($success_message)) {
?>
                                    <tr>
                                      <td class="smallText"><?php echo $success_message; ?></td>
                                    </tr>
                                    <tr>
                                      <td align="center" valign="top"><?php echo '<a href="' . tep_href_link(FILENAME_LOGIN, '' , 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
                                    </tr>
<?php
  } else {
    if (isset($info_message)) {
?>
                                    <tr>
                                      <td colspan="2" class="smallText" align="center"><?php echo $info_message; ?><?php echo tep_draw_hidden_field('log_times', $log_times); ?></td>
                                    </tr>
<?php
    } else {
?>
                                    <tr>
                                      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?><?php echo tep_draw_hidden_field('log_times', '0'); ?></td>
                                    </tr>
<?php
    }
?>                                    
                                    <tr>
                                      <td class="login"><?php echo ENTRY_FIRSTNAME; ?></td>
                                      <td class="login"><?php echo tep_draw_input_field('firstname'); ?></td>
                                    </tr>
                                    <tr>
                                      <td class="login"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
                                      <td class="login"><?php echo tep_draw_input_field('email_address'); ?></td>
                                    </tr>
                                    <tr>
                                      <td colspan="2" align="right" valign="top"><?php echo '<a href="' . tep_href_link(FILENAME_LOGIN, '' , 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a> ' . tep_image_submit('button_confirm.gif', IMAGE_BUTTON_LOGIN); ?>&nbsp;</td>
                                    </tr>
<?php
  }
?>   
                                  </table></td></tr>
                                </table>
                                </td>
                              </tr>
                            </table>
                          </form>

         
<table border="0" width="760" cellspacing="0" cellpadding="2">
					<?php
                   /*$col = 2;
                  $counter = 0;
                     for ($i = 0, $n = sizeof($cat); $i < $n; $i++) {
                       $counter++;
                        if ($counter < $col) {
                          echo '              <tr>' . "\n";
                        }
                    
                        echo '                  <td>'. "\n" .
                             '				      <table border="0" cellspacing="0" cellpadding="2">' . "\n" .
                             '                      <tr>' . "\n" .
                             '                        <td>' . "\n" .
                    //		 '                          <a href="' . $cat[$i]['href'] . '">' . tep_image(DIR_WS_IMAGES . 'categories/' . $cat[$i]['image'], $cat[$i]['title'], '32', '32') . '</a></td>' . "\n" .
                             '                        <td>' . "\n" .
                             '                          <table border="0" cellspacing="0" cellpadding="2">' . "\n" .
                             '                            <tr>' . "\n" .
                             '                              <td class="main"><a href="' . $cat[$i]['href'] . '" class="main">' . $cat[$i]['title'] . '</a></td>' . "\n" .
                             '                            </tr>' . "\n" .
                             '                            <tr>' . "\n" .
                             '                              <td class="sub">';
                    
                        $children = '';
                        for ($j = 0, $k = sizeof($cat[$i]['children']); $j < $k; $j++) {
                          $children .= '<a href="' . $cat[$i]['children'][$j]['link'] . '" class="sub">' . $cat[$i]['children'][$j]['title'] . '</a>, ';
                        }
                        echo substr($children, 0, -2);
                    
                        echo '</td> ' . "\n" .
                             '                            </tr>' . "\n" .
                             '                          </table>' . "\n" .
                             '                        </td>' . "\n" .
                             '                      </tr>' . "\n" .
                             '                    </table>' . "\n" .
                             '                  </td>' . "\n"; 
                        if ($counter >= $col) {
                          echo '              </tr>' . "\n";
                          $counter = 0;
                        }
                      }
                    
                    */?>
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
<center><font color="#FFFFFF"  face="Arial, Helvetica, sans-serif" pointsize="12">
&copy; 2006-2010 Outdoor Business Network, Inc.</font></center>
</body>

</html>