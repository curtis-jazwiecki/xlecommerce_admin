<?php
/*
  $Id: googlesitemap.php admin page,v 1.0 8/11/2005 bhakala@pc-productions.net
  Released under the GNU General Public License
*/

  require('includes/application_top.php');
    
  	function GenerateSubmitURL(){
		$url = urlencode(HTTP_SERVER . DIR_WS_CATALOG . 'sitemapindex.xml');
		return htmlspecialchars(utf8_encode('http://www.google.com/webmasters/sitemaps/ping?sitemap=' . $url));
	} # end function
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
      
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3>Google XML Sitemap Admin
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading">Google XML Sitemap Admin
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
<!-- body_text //-->
    <td><table class="table table-bordered table-hover">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2" align="right"><img src="images/google-sitemaps.gif" width="110" height="48"></td>
          </tr>
          <tr>
          <td colspan="2"></td>
          </tr>
        </table>
          <table class="table table-bordered table-hover">
            <tr>
              <td align="left"><p><strong>OVERVIEW:</strong></p>
                <p>This module automatically generates several XML Google compliant site maps for your oscommerce store: a main site map, one for categories, and one for your products.</p>
                <p><strong>INSTRUCTIONS: </strong></p>
                <p><strong><font color="#FF0000">STEP 1:</font></strong> Click <a href="javascript:(void 0)" class="splitPageLink" onClick="window.open('<?php echo $HTTP_SERVER . DIR_WS_CATALOG;?>googlesitemap/index.php','google','resizable=1,statusbar=5,width=600,height=400,top=0,left=50,scrollbars=yes')"><strong>[HERE]</strong></a> to create / update your site map. </p>
                <p>NOTE: Please ensure that you or your web developer has registered with Google SiteMaps, and submitted your initial site map before proceeding to step 2. </p>
                <p><strong><font color="#FF0000">STEP 2:</font></strong> Click <a href="javascript:(void 0)"  onClick="window.open('<?php echo $returned_url = GenerateSubmitURL();?>','google','resizable=1,statusbar=5,width=600,height=400,top=0,left=50,scrollbars=yes')" class="splitPageLink"><strong>[HERE]</strong></a> to PING the google server to notify them of the update to your XML sitemap.</p>
                <p>COMPLETE!</p>
                <p>&nbsp;</p></td>
              <td align="right"><table class="table table-bordered table-hover">
                <tr>
                  <td> <strong>What is Google SiteMaps?</strong></td>
                </tr>
                <tr>
                  <td><table class="table table-bordered table-hover">
                    <tr>
                      <td align="left"><p>Google SiteMaps allows you to upload an XML sitemap of all of your categories and products directly to google.com for faster indexing. </p>
                        <p>To register or login to your Google account, click <strong><a href="https://www.google.com/webmasters/sitemaps/login" target="_blank" class="splitPageLink">[HERE]</a></strong>.</p>
                        </td>
                    </tr>
                  </table></td>
                </tr>
              </table>
                <p>&nbsp;</p></td>
            </tr>
          </table>
          </td>
      </tr>
      <tr>
        <td></td>
          </tr>       
<!-- body_text_eof //-->
        </table></td>
      </tr>
    </table>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>