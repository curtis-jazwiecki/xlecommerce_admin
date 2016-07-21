<?php
/*
  $Id: header.php,v 1.19 2002/04/13 16:11:52 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
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
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
   <meta name="description" content="">
   <meta name="keywords" content="">
   <meta name="author" content="">
   <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
   <title><?php echo TITLE; ?></title>
   <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
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
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<?php require('includes/account_check.js.php'); ?>
</head>

<body>
   <!-- START Main wrapper-->
   <section class="wrapper">
      <!-- START Top Navbar-->
      <nav role="navigation" class="navbar navbar-default navbar-top navbar-fixed-top">
         <!-- START navbar header-->
         <div class="navbar-header">
            <a href="#" class="navbar-brand">
               <div class="brand-logo"><img style="width: 57px; height: 33px;" src="images/logo.png" alt="Admin Logo" class="media-object"></div>
               <div class="brand-logo-collapsed"><img style="width: 57px; height: 33px;" src="images/logo.png" alt="Admin Logo sort" class="media-object"></div>
            </a>
         </div>

<div class="nav-wrapper">
            <!-- START Left navbar-->
            <ul class="nav navbar-nav">
               <li>
                  <a href="#" data-toggle="aside">
                     <em class="fa fa-align-left"></em>
                  </a>
               </li>
               <li>
                  <a href="#" data-toggle="navbar-search">
                     <em class="fa fa-search"></em>
                  </a>
               </li>
            </ul>
            <!-- END Left navbar-->
            <!-- START Right Navbar-->
            <ul class="nav navbar-nav navbar-right">
               <!-- START Messages menu (dropdown-list)-->
               <?php 
                  if (tep_session_is_registered('login_id')) {
                    echo '<li> <a href="' . tep_href_link(FILENAME_LOGOFF, '', 'NONSSL') . '" class="headerLink">' . '<button type="button" data-toggle="tooltip" data-title="Logout" class="btn-link">' . '<em class="fa fa-power-off text-muted"></em>' . '</button>' . '</a></li>';
                  } else {
                    echo '<li><a href="' . tep_href_link(FILENAME_DEFAULT, '', 'NONSSL') . '" class="headerLink">' . HEADER_TITLE_TOP . '</a></li>';
                  }
                //Admin end 
    
               // echo '<li> <a href="' . tep_catalog_href_link() . '" class="headerLink">' . HEADER_TITLE_ONLINE_CATALOG . '</a></li>'; ?>

<!--               <li>
                  <a href="#" data-toggle="offsidebar">
                     <em class="fa fa-align-right"></em>
                  </a>
               </li>
-->               <!-- END Contacts menu-->
            </ul>
            <!-- END Right Navbar-->
         </div>
         <!-- END Nav wrapper-->
         <!-- START Search form-->
        
           <form action="<?php echo HTTP_SERVER.DIR_WS_ADMIN.'index.php'; ?>" role="form"  method="get" role="search" class="form-inline navbar-form">
                    <div class="form-group has-feedback"> 
                     <div class="col-md-2 col-xs-6" style="padding:0 2px 0 0;">
                        <select name="search_drop_box" class="form-control m-b bg-primary">
                                      <option name="" value="" selected>Search For:</option>
                                      <option name="product" value="product">Product</option>
                                      <option name="order_number" value="order_number">Order Number</option>
                                      <option name="customer" value="customer">Customer Name</option>
                                    </select>
                      </div>
                     <div class="col-md-10 col-xs-6" style="padding:0;">
                        <input name="search_text" type="text" placeholder="Type and hit Enter.." class="form-control bg-primary">
                     </div>
                    </div>
             </form>
        
            
         
                       <?php /*?> <form action="<?php echo HTTP_SERVER.DIR_WS_ADMIN.'index.php'; ?>" method="get" role="search" class="navbar-form">
                          <div class="form-group has-feedback">
                              <div class="form-group">
                                 <div class="col-sm-3">
                                    <select name="search_drop_box">
                                      <option name="" value="" selected>Search For:</option>
                                      <option name="product" value="product">Product</option>
                                      <option name="order_number" value="order_number">Order Number</option>
                                      <option name="customer" value="customer">Customer Name</option>
                                    </select>
                                 </div>
                                 <div class="col-sm-3">
                                    <input name="search_text" type="text" placeholder="Type and hit Enter.." class="form-control">
                                 </div>
                             </div>
                           </form>
                              </div><?php */?>
         
         
         <?php /*?><form action="<?php echo HTTP_SERVER.DIR_WS_ADMIN.'index.php'; ?>" method="get" role="search" class="navbar-form">
            <div class="form-group has-feedback">
                        <select name="search_drop_box">
                          <option name="" value="" selected>Search For:</option>
                          <option name="product" value="product">Product</option>
                          <option name="order_number" value="order_number">Order Number</option>
                          <option name="customer" value="customer">Customer Name</option>
                        </select>
               <input name="search_text" type="text" placeholder="Type and hit Enter.." class="form-control">
               <div data-toggle="navbar-search-dismiss" class="fa fa-times form-control-feedback"></div>
            </div>
            <input type="image" src="../images/template/go_button.jpg" style="margin: 1px 0px -5px 0px; padding: 0px;" />
         </form><?php */?>
         <!-- END Search form-->
</nav>

      <!-- START aside-->
      <aside class="aside">
         <!-- START Sidebar (left)-->
         <?php include("includes/new_menu.php"); ?>
      </aside>
      <!-- End aside-->