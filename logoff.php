<?php
/*
  $Id: logoff.php,v 1.12 2003/02/13 03:01:51 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LOGOFF);

//tep_session_destroy();
  tep_session_unregister('login_id');
  tep_session_unregister('login_firstname');
  tep_session_unregister('login_groups_id');
  tep_session_unregister('viewable_functionality');
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

<table border="0" width="760" cellspacing="0" cellpadding="0" style="margin: 0px auto; background-color: #fff" align="center">
  <tr>
    <td>
      <table border="0" width="760" cellspacing="0" cellpadding="0" align="center">
        <tr>
          <td>
            <table border="0" width="760" cellspacing="0" cellpadding="0" background="../images/template/header2.jpg" style="background-position: top 15px;">
              <tr>
              	<td colspan="3" style="background-color: #999">
                  <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#26a5dc" style="font: Georgia, 'Times New Roman', Times, serif; margin: 0px; font-size: 12px; background-repeat:repeat-x">
                    <tr valign="center" style="height: 24px; max-height: 24px; ">
                        <td>
						</td>
					</tr>
				  </table>
                </td>
              </tr>
            </table>
            <table border="0" width="760" cellspacing="0" cellpadding="0" background="../images/template/header2.jpg" style="background-position: top 15px;">
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
            </table>
            <table border="0" width="760" cellspacing="0" cellpadding="0" background="../images/template/nav_bg.jpg">
              <tr>
              	<td colspan="3" style="max-height: 24px;">
					<?php // include("includes/new_menu.php"); ?>
                </td>
			  </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<?php // Before icons ?>
<table border="0" width="760" cellspacing="0" cellpadding="0"  style="margin: 45px auto;" align="center">
  <tr>
    <td>
      <table border="0" width="760" cellspacing="0" cellpadding="0" align="center">
        <tr>
          <td>
            <table border="0" width="760" cellspacing="0" cellpadding="0">
			  <tr>
              	<td colspan="3" align="center" valign="middle"><table width="280" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="login_heading" valign="top"><b><?php echo HEADING_TITLE; ?></b></td>
              </tr>
              <tr>
                <td class="login_heading"><?php echo TEXT_MAIN; ?></td>
              </tr>
              <tr>
                <td class="login_heading" align="right"><?php echo '<a class="login_heading" href="' . tep_href_link(FILENAME_LOGIN, '', 'SSL') . '">' . tep_image_button('button_back_b.gif', IMAGE_BACK) . '</a>'; ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '30'); ?></td>
              </tr>
            </table><table border="0" width="760" cellspacing="0" cellpadding="2">
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
&copy; 2006-2015 Outdoor Business Network, Inc.</font></center>
</body>

</html>