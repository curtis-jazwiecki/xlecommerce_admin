<?php
/*
  $Id: header.php,v 1.19 2002/04/13 16:11:52 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
  if ($messageStack->size > 0) {
    echo $messageStack->output();
  }

  $my_account_query = tep_db_query ("select a.admin_id, a.admin_firstname, a.admin_lastname, a.admin_email_address, a.admin_created, a.admin_modified, a.admin_logdate, a.admin_lognum, a.admin_email_username, a.admin_email_password, g.admin_groups_name from " . TABLE_ADMIN . " a, " . TABLE_ADMIN_GROUPS . " g where a.admin_id= " . $login_id . " and g.admin_groups_id= " . $login_groups_id . "");
  $myAccount = tep_db_fetch_array($my_account_query);
  define('STORE_ADMIN_NAME',$myAccount['admin_firstname'] . ' ' . $myAccount['admin_lastname']);
  define('TEXT_WELCOME','Welcome <strong>' . STORE_ADMIN_NAME . '</strong> to <strong>' . STORE_NAME . '</strong> Administration!');

  // Below is for the webmail to auto-populate username and password for webmail - OBN
  define ('LOGIN_EMAIL_USERNAME' , $login_email_username);
  define ('LOGIN_EMAIL_PASSWORD' , $login_email_password);
?>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script>
	var jQuery = jQuery.noConflict();
	jQuery(function(){
		jQuery('input[name="search_text"]').keydown(function(event){
			if (event.keyCode==13){
				if (jQuery('select[name="search_drop_box"]').val()==''){
					keyword = jQuery('input[name="search_text"]').val();
					location.href = 'view_keyword_match.php?keyword=' + escape(keyword);
					return false;
				}
			}
		});
	});
</script>
<table border="0" align="center" width="100%" cellspacing="0" cellpadding="0" background="../../images/template/admin_bg.jpg" style="margin: 0px auto; width:100%; background:url(../../images/template/admin_bg.jpg); background-repeat: repeat-x;">
  <tr>
    <td>
      <table border="0" align="center" width="100%" cellspacing="0" cellpadding="0" style="margin: 0px auto;">
        <tr>
          <td>
            <table border="0" align="center" width="760px" cellspacing="0" cellpadding="0" style="margin: 0px auto; background: url(../../images/template/header2.jpg)">
              <tr>
                <td colspan="3" style="background-color: #999">
                  <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#26a5dc" style="font: Georgia, 'Times New Roman', Times, serif; margin: 0px; font-size: 12px;">
                    <tr valign="center" style="height: 24px; max-height: 24px; overflow: auto ">
                      <form action="<?php echo HTTP_SERVER.DIR_WS_ADMIN.'index.php'; ?>" method="get">
                      <td align="left" valign="top" style="overflow: auto;">
                        <select name="search_drop_box" style="font-size: 12px" style="margin: 0; padding: 0;">
                          <option name="" value="" selected>Search For:</option>
                          <option name="product" value="product">Product</option>
                          <option name="order_number" value="order_number">Order Number</option>
                          <option name="customer" value="customer">Customer Name</option>
                        </select>
                        <input name="search_text" type="text" size="13" style="margin: 0px; font-size: 12px" />
                        <input type="image" src="../images/template/go_button.jpg" style="margin: 1px 0px -5px 0px; padding: 0px;" />
                      </td>
                      </form>
                      <td>
                          <span style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:12px; font-weight:bold">Welcome, <?php echo STORE_ADMIN_NAME; ?></span>
                      </td>
                      <td width="*" align="right" class="smallText2">
                <?php 
                  if (tep_session_is_registered('login_id')) {
                    echo '<a href="' . tep_href_link(FILENAME_ADMIN_ACCOUNT, '', 'SSL') . '" class="headerLink">' . HEADER_TITLE_ACCOUNT . '</a> | <a href="' . tep_href_link(FILENAME_LOGOFF, '', 'NONSSL') . '" class="headerLink">' . HEADER_TITLE_LOGOFF . '</a>';
                  } else {
                    echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'NONSSL') . '" class="headerLink">' . HEADER_TITLE_TOP . '</a>';
                  }
                //Admin end 
    
                echo ' | <a href="' . tep_catalog_href_link() . '" class="headerLink">' . HEADER_TITLE_ONLINE_CATALOG . '</a>'; ?></td>
    
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
            <table border="0" width="760" cellspacing="0" cellpadding="0" background="../images/template/header2.jpg" style="background-position: top;" align="center" style="margin: 0px auto;">
              <tr height="100">
                <td width="760px" height="100px"></td>
              </tr>
            </table>
            <table border="0" width="760px" cellspacing="0" cellpadding="0" background="../images/template/nav_bg.jpg" align="center" style="margin: 0px auto;">
              <tr>
                <td colspan="3" style="max-height: 24px;"><?php include("includes/new_menu.php"); ?></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>