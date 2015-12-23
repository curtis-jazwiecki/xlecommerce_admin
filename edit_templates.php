<?php
/*
  $Id: customers.php,v 1.82 2003/06/30 13:54:14 dgw_ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
require('includes/application_top.php');

$action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

$error = false;
$processed = false;

if(isset($_POST['file_name']) && $_POST['action'] == "edit"){
	//$myFile = $_POST['file_name'];
        $myFile = str_replace('$', '/', str_replace('dotc', '.css', str_replace('doth', '.html', str_replace('dotp', '.php', $_POST['file_name']))));
	$fh = fopen($myFile, 'w') or die("can't open file: $myFile");
	$stringData = stripslashes($_POST['file_text']);
	fwrite($fh, $stringData);
	fclose($fh);
}
// Begin Revert Template
if(isset($_POST['update_template'])){
	$new_template_selection = $_POST['selected'];
	$template_update_query = tep_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '".$new_template_selection."' where configuration_key = 'MODULE_STS_TEMPLATE_FOLDER'");
}

// Begin Template Check
$template_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_STS_TEMPLATE_FOLDER'");
$rowz = tep_db_fetch_array($template_query);
$selected_template = $rowz['configuration_value'];
// End Template Check
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
	<title><?php echo TITLE; ?></title>
	<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
        <style>
            a:link.file_link{
                color:red;
            }
            a:visited.file_link{
                color:red;
            }
        </style>
        
<!-- SCRIPT FOR ACCORDIAN -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"> </script>
<script type="text/javascript" src="../../../jquery.min.js"> </script>
<script type="text/javascript">
    $(document).ready(function() { 	 	
        //ACCORDION BUTTON ACTION (ON CLICK DO THE FOLLOWING)	
        $('.accordionButton').click(function() {		
            //REMOVE THE ON CLASS FROM ALL BUTTONS		
            $('.accordionButton').removeClass('on');		  		
            ////NO MATTER WHAT WE CLOSE ALL OPEN SLIDES	 	
            $('.accordionContent').slideUp('normal');   		//IF THE NEXT SLIDE WASN'T OPEN THEN OPEN IT		
            if($(this).next().is(':hidden') == true) {						
                //ADD THE ON CLASS TO THE BUTTON			
                $(this).addClass('on');			  		
                ////OPEN THE SLIDE			
                $(this).next().slideDown('normal');		 
            } 		  	 
        });	
        /*** REMOVE IF MOUSEOVER IS NOT REQUIRED ***/	
        //ADDS THE .OVER CLASS FROM THE STYLESHEET ON MOUSEOVER 	
        $('.accordionButton').mouseover(function() {		
            $(this).addClass('over');
        	//ON MOUSEOUT REMOVE THE OVER CLASS	
        }).mouseout(function() {		
            $(this).removeClass('over');
        });	
        /*** END REMOVE IF MOUSEOVER IS NOT REQUIRED ***/	
        /********************************************************************************************************************	CLOSES ALL S ON PAGE LOAD	********************************************************************************************************************/		
		$('.accordionContent').hide();		
        $("#open").trigger('click');});
</script> 
<!-- END SCRIPT FOR ACCORDIAN -->
<!-- CSS FOR ACCORDIAN -->
<style type="text/css">
    #wrapper {	width: 100%;	margin-left: auto;	margin-right: auto;	}	
    .accordionButton {		
                        width: 100%px;	
                        float: left;	
                        _float: none;  
                       /* Float works in all browsers but IE6 */	
                       border:1px solid #999999;    
                       border-radius:10px;    
                       -moz-border-radius:10px;	
                       padding:5px;	
                       background-color:#e9e9e9;	
                       /*cursor: pointer;	*/
                       /*background-image:url(../../../../images/product_temp_arrowL.png);	*/
                       background-repeat:no-repeat;	}
    .accordionContent {		
        width: 100%px;	
        float: left;	
        _float: none; 
        /* Float works in all browsers but IE6 */	
        background: #cccccc;	
    }
    .on {	
        background: #999999;	
        /*background-image:url(../../../../images/product_temp_arrowR.png);	*/
        background-repeat:no-repeat;	
    }
    .over {	
        background: #d7d7d7;	
        /*background-image:url(../../../../images/product_temp_arrowR.png);	*/
        background-repeat:no-repeat;	
    }		
	</style>
<!-- END CSS FOR ACCORDIAN -->
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
	<!-- header //-->
	<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
	<!-- header_eof //-->

	<!-- body //-->
	<table width="780" border="0" align="center" cellpadding="2" cellspacing="2">
		<tr>
		<?php
		if((!isset($_POST['action']) && !isset($_GET['action'])) || $_GET['action'] == "revert"){
		?>
			<td>
			<?php
			if(isset($_GET['file']) && isset($_GET['id']) && $_GET['action'] == "revert") {
				$replace_file = $_GET['file'];
				$replace_folder = DIR_FS_CATALOG.DIR_WS_INCLUDES."sts_templates/full/template".$_GET['id']."/";
				$default_file = $_GET['file'];
				$default_folder = DIR_FS_CATALOG.DIR_WS_INCLUDES."sts_templates/full/default/";

				if(!copy($default_folder.$default_file, $replace_folder.$replace_file)){
					echo '<span style="color: #ffffff">' . $replace_file. ' for template # ' . $_GET['id'] . ' has been reverted to the default settings.</span>';
				}
			}
			// End Revert Template
			?>
			</td>
		</tr>
                <tr>
                    <td width="100%" valign="top">
                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                            <tr>
                                <td>
                                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td class="pageHeading">Edit Templates<?php if(isset($_POST['update_template'])) echo '<span style="font-size: 14px;"> - New Template Selected</span>'; ?></td>
                                            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="template_update">
                                    <input type="hidden" name="update_template" value="update_template" />
                                <?php
                                $allowed_dirs = array('boxes', 'content', 'modules', 'blocks');
                                $struct_headers = array(
                                    'type',
                                    'file',
                                    'variable',
                                    'key',
                                    'value',
                                    'description', 
                                );
                                $templates = array();
                                $dir = DIR_FS_CATALOG . 'includes/sts_templates/full/';
                                if (is_dir($dir)){
                                    if ($dh = opendir($dir)){
                                        while(($file = readdir($dh))!==false){
                                            if (is_dir($dir . $file)){
                                                if (strpos($file, 'template')!==false && is_numeric(substr($file, -1))){
                                                    $templates[str_ireplace('template', '', $file)] = $dir . $file . '/';
                                                }
                                            }
                                        }
                                        closedir($dh);
                                    }
                                }
                                ksort($templates, SORT_NUMERIC);
                                ?>
                                <!-- START TEMPLATE TABLE -->
                                   <!-- BEGIN ACCORDIAN MOD -->
                                   <!-- ALSO SEE ACCORDIAN CSS AND SCRIPT IN <head> AREA -->
                                    <?php
                                    foreach($templates as $template){
                                       if (basename($template) != 'template13' && basename($template) != 'template14' && basename($template) != 'template15' && basename($template) != 'template16' && basename($template) != 'template17') {
                                        $current_template = 'full/' . basename($template);  
                                    ?>
                                    <div id="wrapper">	
<div id="open" style="width:100%" class="accordionButton">                                    
	<input type="radio" name="selected" value="<?php echo $current_template; ?>" <?php if($current_template == $selected_template) echo 'checked'; ?> />
	<?php echo strtoupper(basename($template)); ?>
</div>		
<div style="width:100%" class="accordionContent">
                                       <?php
                                            $dirs = $files = array();
                                            if (is_dir($template)){
                                                if (file_exists($template . 'struct.obn')){
                                                    $first_row_skipped = false;
                                                    $template_name = $template_osc_code = $template_id = '';
                                                    if (($handle = fopen($template . 'struct.obn', 'r'))!==false) {
                                                        while (($data=  fgetcsv($handle))!==false){
                                                            if(!$first_row_skipped){
                                                                $first_row_skipped = true;
                                                                continue;
                                                            } else {
                                                                $row = array_combine($struct_headers, $data);
                                                                if ($row['type']=='data'){
                                                                    $$row['key'] = $row['value'];
                                                                    //if ($row['key']=='template_name'){
                                                                        //echo 'Template Ref: ' . $row['value'] . '<br><br>';
                                                                    //}
                                                                    echo $row['key'] . ': ' . $row['value'] . '<br>';
                                                                } elseif($row['type']=='file'){
                                                                    echo '<div>';
                                                                    echo '<div style="float:left;">' . str_pad('<a class="file_link" href="' . $_SERVER['PHP_SELF'] . '?action=edit&file=' . $row['file'] . '&id=' . $template_id . '">' . (!empty($row['description']) ? $row['description'] : $row['file']) . '</a>', 50, '&nbsp;') . (!empty($row['variable']) ? ' (' . $row['variable'] . ')' : '') . '</div><div style="float:right;">' . (file_exists($dir . 'template'. $template_id . '/' . $row['file']) ? date('m/d/y', filemtime(utf8_decode($dir . 'template'. $template_id . '/' . $row['file']))):'') . '</div>';
                                                                    echo '</div><br>';
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    if ($dh = opendir($template)){
                                                        while(($file = readdir($dh))!==false){
                                                            $is_file = $is_dir = false;
                                                            if (is_dir($template . $file)){
                                                                if (in_array($file, $allowed_dirs)){
                                                                    $is_dir = true;
                                                                    $dirs[] = $file;
                                                                }
                                                            }elseif (substr($file, -3)=='php' || substr($file, -4)=='html'){
                                                                $is_file = true;
                                                                $files[] = $file;
                                                            }
                                                        }
                                                        closedir($dh);
                                                    }
                                                }
                                            }
                                            ?>
                                          
                                        </div>
                                    <?php
                                      }
                                    }
                                    ?>
                                    </div>
                                            <input border="0" type="image" title=" Update " alt="Update" src="includes/languages/english/images/buttons/button_update_b.gif">
                                    <!-- END ACCORDIAN MOD -->
                                    <!-- END TEMPLATE TABLE -->
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                    
		<tr>
			<!-- body_text //-->
			<td width="100%" valign="top">
				<table border="0" width="100%" cellspacing="0" cellpadding="2">
					<tr>
						<td>
							<table border="0" width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td class="pageHeading">Edit Templates<?php if(isset($_POST['update_template'])) echo '<span style="font-size: 14px;"> - New Template Selected</span>'; ?></td>
									<td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
								</tr>
							</table>
						</td>
					</tr>

				</table>
			</td>
			<!-- body_text_eof //-->
			<?php
			} elseif($_GET['action'] == "edit" || $_POST['action'] == "edit") {
				$id = $_GET['id'];
				$folder_name = DIR_FS_CATALOG.DIR_WS_INCLUDES."sts_templates/full/template".$id."/";
				$filename = $_GET['file'];

				$contents = file($folder_name.$filename);
				$string = implode($contents);
			?>
			<td width="100%" valign="top">
				<table border="0" width="100%" cellspacing="0" cellpadding="2">
					<tr>
						<td>
							<table border="0" width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td class="pageHeading">Edit Templates</td>
									<td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table border="0" width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td valign="top">
										<table border="0" width="100%" cellspacing="0" cellpadding="2">
											<tr class="dataTableHeadingRow">
												<td class="dataTableHeadingContent" align="left" colspan="4">File Text for "<?php echo $filename; ?>"</td>
											</tr>
											<form method='post' action='edit_templates.php?<?php echo tep_get_all_get_params();?>'>
											<!--<input type="hidden" name="file_name" value="<?php //echo $folder_name.$filename; ?>" /> -->
                                                                                            <input type="hidden" name="file_name" value="<?php echo str_replace('/', '$', str_replace('.css', 'dotc', str_replace('.html', 'doth', str_replace('.php', 'dotp', $folder_name.$filename)))); ?>" />
											<input type="hidden" name="action" value="edit" />
											<tr bgcolor="#FFFFFF">
												<td class="dataTableHeadingContent" align="center" colspan="4" align="center">
													<textarea name="file_text" style="width: 750px; height: 650px; margin: 5px 0"><?php echo $string; ?></textarea>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							<div style="float: right; margin: 5px 0 0 0;">
							<?php echo '<a href="edit_templates.php">' . tep_image_button('button_back_b.gif', IMAGE_BACK) . '</a>';?>
								<input border="0" type="image" title=" Update " alt="Update" src="includes/languages/english/images/buttons/button_update_b.gif">
							</div>
											</form>
						</td>
					</tr>
				</table>
			</td>
			<?php
			}
			?>
		</tr>
		<tr><td colspan="4"></td></tr>
	</table>
	<!-- body_eof //-->
	<!-- footer //-->
	<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
	<!-- footer_eof //-->
	<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>