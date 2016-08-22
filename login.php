<?php
/*
  $Id: login.php,v 1.17 2003/02/14 12:57:29 dgw_ Exp $

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

  require('includes/application_top.php');
  
  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process')) {
    $email_address = tep_db_prepare_input($HTTP_POST_VARS['email_address']);
    $password = tep_db_prepare_input($HTTP_POST_VARS['password']);
    // #14 12jan2014 (MA) BOF
    $ip_address = tep_get_ip_address();
    // #14 12jan2014 (MA) EOF
// Check if email exists
    $check_admin_query = tep_db_query("select admin_id as login_id, admin_groups_id as login_groups_id, admin_firstname as login_firstname, admin_lastname as login_lastname, admin_email_address as login_email_address, admin_password as login_password, admin_modified as login_modified, admin_logdate as login_logdate, admin_lognum as login_lognum, admin_email_username as login_email_username, admin_email_password as login_email_password from " . TABLE_ADMIN . " where admin_email_address = '" . tep_db_input($email_address) . "'");

    if (!tep_db_num_rows($check_admin_query)) {
      $HTTP_GET_VARS['login'] = 'fail';
      // #14 12jan2014 (MA) BOF
      tep_get_action_recorder($email_address, '0', $ip_address, $success = '0');
      // #14 12jan2014 (MA) EOF
    } else {
      $check_admin = tep_db_fetch_array($check_admin_query);
      
      // Check that password is good
      if (!tep_validate_password($password, $check_admin['login_password'])) {
        $HTTP_GET_VARS['login'] = 'fail';
        // #14 12jan2014 (MA) EOF
        tep_get_action_recorder($check_admin['login_firstname'] . ' ' .$check_admin['login_lastname'], $check_admin['login_id'], $ip_address, $success = '0');
        // #14 12jan2014 (MA) BOF
      } else {
        if (tep_session_is_registered('password_forgotten')) {
          tep_session_unregister('password_forgotten');
        }
        
        $login_id = $check_admin['login_id'];
		//BOF:mod 10jan2014
		/*
		//EOF:mod 10jan2014
        $login_groups_id = $check_admin[login_groups_id];
		//BOF:mod 10jan2014
		*/
		$login_groups_id = $check_admin['login_groups_id'];
		//EOF:mod 10jan2014
        $login_firstname = $check_admin['login_firstname'];
        $login_email_address = $check_admin['login_email_address'];
		$login_email_username = $check_admin['login_email_username'];
		$login_email_password = $check_admin['login_email_password'];
        $login_logdate = $check_admin['login_logdate'];
        $login_lognum = $check_admin['login_lognum'];
        $login_modified = $check_admin['login_modified'];

        tep_session_register('login_id');
        tep_session_register('login_groups_id');
        tep_session_register('login_first_name');
        tep_session_register('login_email_username');
        tep_session_register('login_email_password');
        tep_session_register('viewable_functionality');
        
        $viewable_functionality = get_admin_viewable_functionality();
        
        // #14 12jan2014 (MA) BOF
        tep_get_action_recorder($check_admin['login_firstname'] . ' ' .$check_admin['login_lastname'], $check_admin['login_id'], $ip_address, $success = '1');
        // #14 12jan2014 (MA) EOF
        
        //$date_now = date('Ymd');
        tep_db_query("update " . TABLE_ADMIN . " set admin_logdate = now(), admin_lognum = admin_lognum+1 where admin_id = '" . $login_id . "'");

        if (($login_lognum == 0) || !($login_logdate) || ($login_email_address == 'admin@localhost') || ($login_modified == '0000-00-00 00:00:00')) {
          tep_redirect(tep_href_link(FILENAME_ADMIN_ACCOUNT));
        } else {
          tep_redirect(tep_href_link(FILENAME_DEFAULT));
        }

      }
    }
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LOGIN);
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="ie ie6 lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="ie ie7 lt-ie9 lt-ie8"        lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="ie ie8 lt-ie9"               lang="en"> <![endif]-->
<!--[if IE 9]>    <html class="ie ie9"                      lang="en"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-ie">
<!--<![endif]-->

<head>
   <!-- Meta-->
   <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
   <meta name="description" content="">
   <meta name="keywords" content="">
   <meta name="author" content="">
   <title><?php echo TITLE; ?></title>
   <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
   <!--[if lt IE 9]><script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script><script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script><![endif]-->
   <!-- Bootstrap CSS-->
   <link rel="stylesheet" href="app/css/bootstrap.css">
   <!-- Vendor CSS-->
   <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
   <link rel="stylesheet" href="vendor/animo/animate+animo.css">
   <link rel="stylesheet" href="vendor/csspinner/csspinner.min.css">
   <!-- START Page Custom CSS-->
   <!-- END Page Custom CSS-->
   <!-- App CSS-->
   <link rel="stylesheet" href="app/css/app.css">
   <!-- Modernizr JS Script-->
   <script src="vendor/modernizr/modernizr.js" type="application/javascript"></script>
   <!-- FastClick for mobiles-->
   <script src="vendor/fastclick/fastclick.js" type="application/javascript"></script>
   
</head>
<body>
 <!-- START Main wrapper-->
   <div class="container">
   <div class="col-md-12" style="width:100%; height:65px; background:url(images/logo-cloud.png); background-repeat:no-repeat; background-position:center; margin-top:24px;"></div>
  
  
   <!-- START Page content-->
         <div style="height: 100%; padding: 10px 0;" class="row row-table">
      <div class="col-lg-3 col-md-6 col-sm-8 col-xs-12 align-middle">
      <div data-toggle="play-animation" data-play="zoomIn" data-offset="0" data-duration="300" class="panel panel-default panel-flat">
         <!-- START panel-->
            <!-- START row-->
            <div class="row">
               <div class="col-lg-12">
               <?php echo tep_draw_form('login', FILENAME_LOGIN, 'action=process'); ?>
                     <!-- START panel-->
                     <div class="panel panel-primary" style="margin-bottom:0;">
                        <div class="panel-heading">
                           <div class="panel-title"><?php echo HEADING_RETURNING_ADMIN; ?></div>
                                                           <?php
  if ($HTTP_GET_VARS['login'] == 'fail') {
    $info_message = TEXT_LOGIN_ERROR;
  }

  if (isset($info_message)) {
?>
                                <div><?php echo $info_message; ?></div>
                                <?php
  } else {
?>
                                <?php
  }
?>
                        </div>
                        <div class="panel-body">
                           <div class="form-group">
                              <label class="control-label"><?php echo ENTRY_EMAIL_ADDRESS; ?> *</label>
                              <?php echo tep_draw_input_field('email_address','','class="form-control"'); ?>
                           </div>
                           <div class="form-group">
                              <label class="control-label"><?php echo ENTRY_PASSWORD; ?>*</label>
                              <input type="password" maxlength="40" name="password" class="form-control">
                             
                           </div>
                           <div class="required"><?php echo '<a href=' . tep_href_link(FILENAME_PASSWORD_FORGOTTEN, '', 'SSL') . '>' . TEXT_PASSWORD_FORGOTTEN . '</a>'; ?></div>
                        </div>
                        <div class="panel-footer">
                           <?php echo '<button type="submit" class="btn btn-primary btn-sm">Confirm</button>'; ?>
                        </div>
                     </div>
                     <!-- END panel-->
                  </form>
               </div>
            </div>
            <div class="col-lg-12 centered">&copy; 2006-2015 Outdoor Business Network, Inc.</div>
            <!-- END row-->
            </div>
            </div>
         <!-- END panel-->
      </div>
         <!-- END Page content--> 
   
   </div>

   <!-- END Main wrapper-->
   <!-- START Scripts-->
   <!-- Main vendor Scripts-->
   <script src="vendor/jquery/jquery.min.js"></script>
   <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
   <!-- Animo-->
   <script src="vendor/animo/animo.min.js"></script>
   <!-- Custom script for pages-->
   <script src="app/js/pages.js"></script>
   <!-- END Scripts-->
</body>

</html>



