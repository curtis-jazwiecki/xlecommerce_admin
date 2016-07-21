<?php

/*

  $Id: login.php,v 1.17 2003/02/14 12:57:29 dgw_ Exp $



 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.

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

 

 

   <div class="container">

   <!-- START Page content-->

      <div style="height: 100%; padding: 10px 0;" class="row row-table">

      <div class="col-lg-3 col-md-6 col-sm-8 col-xs-12 align-middle">

      <div data-toggle="play-animation" data-play="zoomIn" data-offset="0" data-duration="300" class="panel panel-default panel-flat">

            <!--<h3>Password Forgotten</h3>-->

            <!-- START row-->

            <div class="row">

               <div class="col-lg-12">

               <?php echo tep_draw_form('login', FILENAME_PASSWORD_FORGOTTEN, 'action=process'); ?>

                     <!-- START panel-->

                     <div class="panel panel-primary" style="margin-bottom:0;">

                        <div class="panel-heading">

                           <div class="panel-title"><?php echo HEADING_PASSWORD_FORGOTTEN; ?></div>

                        </div>

                        

                        

                        <?php

  if ($HTTP_GET_VARS['login'] == 'success') {

    $success_message = TEXT_FORGOTTEN_SUCCESS;

  } elseif ($HTTP_GET_VARS['login'] == 'fail') {

    $info_message = TEXT_FORGOTTEN_ERROR;

  }

  if (tep_session_is_registered('password_forgotten')) {

?>

                                    <tr>

                                      <td><?php echo TEXT_FORGOTTEN_FAIL; ?></td>

                                    </tr>

                                    <tr>

                                      <td align="center" valign="top"><?php echo '<a href="' . tep_href_link(FILENAME_LOGIN, '' , 'SSL') . '">' . '<button type="button" class="btn btn-primary btn-sm">Back</button>' . '</a>'; ?></td>

                                    </tr>

<?php

  } elseif (isset($success_message)) {

?>

                                    <tr>

                                      <td><?php echo $success_message; ?></td>

                                    </tr>

                                    <tr>

                                      <td align="center" valign="top"><?php echo '<a href="' . tep_href_link(FILENAME_LOGIN, '' , 'SSL') . '">' . '<button type="button" class="btn btn-primary btn-sm">Back</button>' . '</a>'; ?></td>

                                    </tr>

<?php

  } else {

    if (isset($info_message)) {

?>

                                    <tr>

                                      <td colspan="2" align="center"><?php echo $info_message; ?><?php echo tep_draw_hidden_field('log_times', $log_times); ?></td>

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

<?php

  }

?>  

                        

                        

                        

                        <div class="panel-body">

                           <div class="form-group">

                              <label class="control-label"><?php echo ENTRY_FIRSTNAME; ?></label>

                              <?php echo tep_draw_input_field('firstname','','class="form-control"'); ?>

                           </div>

                           <div class="form-group">

                              <label class="control-label"><?php echo ENTRY_EMAIL_ADDRESS; ?></label>

                              <input type="text" maxlength="40" name="email_address" class="form-control">

                             

                           </div>

                           <div class="required"><?php echo '<a href="' . tep_href_link(FILENAME_LOGIN, '' , 'SSL') . '">' . '<button type="button" class="btn btn-primary btn-sm">Back</button>' . '</a> ' . '<button type="submit" class="btn btn-primary btn-sm">Confirm</button>'; ?>



</div>

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

         </div>

         <!-- END Page content--> 

   

   </div>



   <!-- END Main wrapper-->

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

