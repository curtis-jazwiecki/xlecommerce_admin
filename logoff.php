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
                     <!-- START panel-->
                     <div class="panel panel-primary" style="margin-bottom:0;">
                        <div class="panel-heading">
                        <div class="panel-title"><?php echo HEADING_TITLE; ?></div>
                        <div class="panel-body">                        
                          <?php echo TEXT_MAIN; ?> 
                        </div>
                        <div>
                           <?php echo '<a class="login_heading" href="' . tep_href_link(FILENAME_LOGIN, '', 'SSL') . '">' . '<button type="button" class="btn btn-primary btn-sm">Back</button>' . '</a>'; ?>
                        </div>
                     </div>
                     <!-- END panel-->
               </div>
            </div>
            <div class="col-lg-12 centered">&copy; 2006-2016 Outdoor Business Network, Inc.</div>
            </div>
            <!-- END row-->
            </div>
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
