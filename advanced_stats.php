<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
//////////////////////////////////////////
//
//  Implement JAWstats
//
//  Jeff Brutsche
//  Outdoor Business Network
//
//////////////////////////////////////////

  require('includes/application_top.php');

?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">

<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<link type="text/css" rel="StyleSheet" href="includes/index.css" />
<link type="text/css" rel="StyleSheet" href="includes/helptip.css" />
<script type="text/javascript" src="includes/javascript/helptip.js"></script>
<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3>
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading">
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
    <td valign="top">
    <table class="table table-bordered table-hover" summary="Table holding Store Information">
    <tr>
    <td colspan=2>
    <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>    </td>
    </tr>
    <tr>
	  <td align="center">
		<table class="table table-bordered table-hover">
          <tr>
        	<td align="left">
            <iframe src="jawstats/index.php" scrolling="auto" width="100%" height="700px" border="0px"></iframe>            </td>
          </tr>
        </table>	  </td>
    </tr>

    <tr>
    <td colspan=2>
    <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>    </td>
    </tr>
    </table>
    
    <!--BLOCK CODE ENDS -->
   
     
      <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
      <table class="table table-bordered table-hover" summary="Footer Banner Table">
        <tr>
          <td align="center">      </td>
        </tr>
      </table></td>
  </tr>
</table>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>