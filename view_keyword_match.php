<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require('includes/application_top.php');

$keyword = urldecode($_GET['keyword']);

$matches = array();
$matches_query = tep_db_query("select * from admin_functionality_search where keywords like '%" . tep_db_input($keyword) . "%' order by parent, name");
while($entry = tep_db_fetch_array($matches_query)){
	$parent = trim($entry['parent']);
	if (!array_key_exists($parent, $matches)) $matches[$parent] = array();
	
	$file_name = $entry['file'];
	$query_string = '';
	$pos = strpos($file_name, '?');
	if ($pos!==false){
		$file_name = substr($entry['file'], 0, $pos);
		$query_string = substr($entry['file'], $pos+1);
	}
	
	$matches[$parent][] = '<a href="' . tep_href_link($file_name, $query_string) . '" style="color:black;">' . $entry['name'] . '</a>';
}
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">

<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3>Matches for "<?php echo $keyword; ?>"
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading">Matches for "<?php echo $keyword; ?>"
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
					<table class="table table-bordered table-hover">
						<tr>
							<td>
							<?php if (empty($matches)) { ?>
								<div style="text-align:center;margin:50px 0 50px 0;color:red;"><b>No Match Located for: "<?php echo $keyword; ?>"</b></div>
							<?php } else { ?>
							<?php foreach($matches as $parent => $entries){ ?>
								<div style="margin:30px 0 10px 0"><b><?php echo $parent; ?></b></div>
								<?php foreach($entries as $entry) { ?>
								<div style="margin:5px 0 0 25px;;"><?php echo $entry; ?></div>
								<?php } ?>
							<?php } ?>
							<?php } ?>
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