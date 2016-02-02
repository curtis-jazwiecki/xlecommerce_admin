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
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- Code related to index.php only -->
<link type="text/css" rel="StyleSheet" href="includes/index.css" />
<link type="text/css" rel="StyleSheet" href="includes/helptip.css" />
<script type="text/javascript" src="includes/javascript/helptip.js"></script>
<!-- code related to index.php EOF -->
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3>Web Mail
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading">Web Mail
                  <a href="#" data-perform="panel-dismiss" data-toggle="tooltip" title="Close Panel" class="pull-right">
                     <em class="fa fa-times"></em>
                  </a>
                  <a href="#" data-perform="panel-collapse" data-toggle="tooltip" title="Collapse Panel" class="pull-right">
                     <em class="fa fa-minus"></em>
                  </a>
               </div>
               <!-- START table-responsive-->
               
               <div class="table-responsive">
               <!-- START your table-->
<table class="table table-bordered table-hover">
  <tr>
    <td>
    <table class="table table-bordered table-hover" summary="Table holding Store Information">
        <tr>
          <td colspan=2>
            <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
          </td>
        </tr>
        <tr>
          <td align="center">
             <table class="table table-bordered table-hover">
              <tr>
        	    <td align="center">
                  <iframe src="webmail/index.php?email_username=<?php echo LOGIN_EMAIL_USERNAME ;?>&email_password=<?php echo LOGIN_EMAIL_PASSWORD; ?>" scrolling="auto" width="100%" height="650px" border="0px"></iframe>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>