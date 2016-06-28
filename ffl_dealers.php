<?php
/*
  $Id: ffl_dealers.php,v 1.35 2016/03/18 22:50:52 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/
	define('LIC_REGN','Lic Regn');
	define('LIC_DIST','Lic Dist');
	define('LIC_CNTY','Lic Cnty');
	define('LIC_TYPE','Lic Type');
	define('LIC_XPRDTE','Lic Xprdte');
	define('LIC_SEQN','Lic Seqn');
	define('LICENSE_NAME','License Name');
	define('BUSINESS_NAME','Business Name');
	define('PREMISE_STREET','Premise Street');
	define('PREMISE_CITY','Premise City');
	define('PREMISE_STATE','Premise State');
	define('PREMISE_ZIP_CODE','Premise Zip Code');
	define('MAIL_STREET','Mail Street');
	define('MAIL_CITY','Mail City');
	define('MAIL_STATE','Mail State');
	define('MAIL_ZIP_CODE','Mail Zip Code');
	define('VOICE_PHONE','Voice Phone');

	require('includes/application_top.php');
    
	$action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  	if (tep_not_null($action)) {
		
    	switch ($action) {
			
		  case 'insert':
			
			$vendors_id = tep_db_prepare_input($HTTP_POST_VARS['vendors_id']); 
			
			$ffl_file = $vendors_id."-".tep_db_prepare_input($_FILES['ffl_dealers_doc']['name']);
			
			if (is_uploaded_file($_FILES['ffl_dealers_doc']['tmp_name'])) {
				
				move_uploaded_file($_FILES['ffl_dealers_doc']['tmp_name'],DIR_FS_DOCUMENT_ROOT."ffl_dealers/".$ffl_file);
			
				$result = array();
				
				$fp = fopen(DIR_FS_DOCUMENT_ROOT."ffl_dealers/".$ffl_file,'r');
				
				if (($headers = fgetcsv($fp, 0, "\t")) !== FALSE){
					if ($headers){
						while (($line = fgetcsv($fp, 0, "\t")) !== FALSE){
							if ($line){
								if (sizeof($line)==sizeof($headers)){
									$result[] = array_combine($headers,$line);    
								}
								
							}
						} 
					}    
				}
				
				fclose($fp);
				
				tep_db_query("insert into " . TABLE_FFL_DEALERS_DOCS . " (ffl_dealers_doc, vendors_id, date_added) values ('" . tep_db_input($ffl_file) . "', '" . tep_db_input($vendors_id) . "', NOW())");
				
				$ffl_dealers_docs_id = tep_db_insert_id();   
				
				foreach($result as $ffl_dealer){
					
					// code to get lat and long #start
                        $city = strtolower($ffl_dealer[PREMISE_CITY]);
                        $state = $ffl_dealer[PREMISE_STATE];
                        $zip_code = $ffl_dealer[PREMISE_ZIP_CODE];
                        
                        $lat = 0;
                        $long = 0;
                        
                        $query_lat_long = tep_db_query("select latitude,longitude from zip_csv where zip_code = '".$zip_code."' and state_code = '".$state."' and city = '".$city."'");
                        
                        if(tep_db_num_rows($query_lat_long)){
                            
                            $lat_long_data = tep_db_fetch_array($query_lat_long);
                            $lat = $lat_long_data['latitude'];
                            $long = $lat_long_data['longitude'];
                            
                        }
                        
                    // code to get lat and long #ends
                    
                    
                    tep_db_query("INSERT INTO `ffl_dealers_data` (`lic_regn`, `lic_dist`, `lic_cnty`, `lic_type`, `lic_xprdte`, `lic_seqn`, `license_name`, `business_name`, `premise_street`, `premise_city`, `premise_state`, `premise_zip_code`, `mail_street`, `mail_city`, `mail_state`, `mail_zip_code`, `voice_phone`,`ffl_dealers_docs_id`,`latitude`,`longitude`) VALUES ('" . tep_db_input($ffl_dealer[LIC_REGN]) . "', '" . tep_db_input($ffl_dealer[LIC_DIST]) . "', '" . tep_db_input($ffl_dealer[LIC_CNTY]) . "', '" . tep_db_input($ffl_dealer[LIC_TYPE]) . "', '" . tep_db_input($ffl_dealer[LIC_XPRDTE]) . "', '" . tep_db_input($ffl_dealer[LIC_SEQN]) . "', '" . tep_db_input($ffl_dealer[LICENSE_NAME]) . "', '" . tep_db_input($ffl_dealer[BUSINESS_NAME]) . "', '" . tep_db_input($ffl_dealer[PREMISE_STREET]) . "', '" . tep_db_input($ffl_dealer[PREMISE_CITY]) . "', '" . tep_db_input($ffl_dealer[PREMISE_STATE]) . "', '" . tep_db_input($ffl_dealer[PREMISE_ZIP_CODE]) . "', '" . tep_db_input($ffl_dealer[MAIL_STREET]) . "', '" . tep_db_input($ffl_dealer[MAIL_CITY]) . "', '" . tep_db_input($ffl_dealer[MAIL_STATE]) . "', '" . tep_db_input($ffl_dealer[MAIL_ZIP_CODE]) . "', '" . tep_db_input($ffl_dealer[VOICE_PHONE]) . "', '" . $ffl_dealers_docs_id . "', '" . $lat . "', '" . $long . "')");
					
				} 
                
                tep_redirect(tep_href_link(FILENAME_FFL_DEALER, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'dID=' . $ffl_dealers_docs_id. '&vendors_id=' . $vendors_id));
			
			}
            
            tep_redirect(tep_href_link(FILENAME_FFL_DEALER, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'vendors_id=' . $vendors_id));
			
			break;
		  
		  case 'delete':
			// delete code goes here
            
            $get_file_name = tep_db_fetch_array(tep_db_query("select ffl_dealers_doc from " . TABLE_FFL_DEALERS_DOCS . " where ffl_dealers_docs_id = '".(int)$_GET['dID']."'"));
            
            unlink(DIR_FS_DOCUMENT_ROOT."ffl_dealers/".$get_file_name['ffl_dealers_doc']);
            
            tep_db_query("delete from " . TABLE_FFL_DEALERS_DOCS . " where ffl_dealers_docs_id = '".(int)$_GET['dID']."'");
            
            tep_db_query("delete from ffl_dealers_data where ffl_dealers_docs_id = '".(int)$_GET['dID']."'");
            
            tep_redirect(tep_href_link(FILENAME_FFL_DEALER, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'vendors_id=' . $vendors_id));
            break;
            
            case 'details':
            
                $get_file_name = tep_db_fetch_array(tep_db_query("select ffl_dealers_doc from " . TABLE_FFL_DEALERS_DOCS . " where ffl_dealers_docs_id = '".(int)$_GET['dID']."'"));
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=".$get_file_name['ffl_dealers_doc']);
                header("Pragma: no-cache");
                header("Expires: 0");
                readfile(DIR_FS_DOCUMENT_ROOT."ffl_dealers/".$get_file_name['ffl_dealers_doc']);
                exit;
            
            break;
		
		}
  
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
            <h3><?php echo HEADING_TITLE; ?>
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading"><?php echo HEADING_TITLE; ?>
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

               <!-- body_text //-->
<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td><table class="table table-bordered table-hover">
              <tr>
                <td><?php echo TABLE_HEADING_FFL_DEALER_FILE_NAME; ?></td>
                <td><?php echo TABLE_HEADING_FFL_DEALER_ADDED_ON; ?></td>
                <td align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $ffl_dealer_query_raw = "select * from " . TABLE_FFL_DEALERS_DOCS . " where vendors_id = '".(int)$HTTP_GET_VARS['vendors_id']."' order by date_added DESC";
  $ffl_dealer_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $ffl_dealer_query_raw, $ffl_dealer_query_numrows);
  $ffl_dealer_query = tep_db_query($ffl_dealer_query_raw);

  while ($ffl_dealer = tep_db_fetch_array($ffl_dealer_query)) {
    if ((!isset($HTTP_GET_VARS['dID']) || (isset($HTTP_GET_VARS['dID']) && ($HTTP_GET_VARS['dID'] == $ffl_dealer['ffl_dealers_docs_id']))) && !isset($lInfo) && (substr($action, 0, 3) != 'new')) {
      $lInfo = new objectInfo($ffl_dealer);
    }

    if (isset($lInfo) && is_object($lInfo) && ($ffl_dealer['ffl_dealers_docs_id'] == $lInfo->ffl_dealers_docs_id) ) {
      echo '                  <tr id="defaultSelected"  onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_FFL_DEALER, 'page=' . $HTTP_GET_VARS['page'] . '&dID=' . $lInfo->ffl_dealers_docs_id .'&vendors_id=' . $HTTP_GET_VARS['vendors_id']. '&action=details') . '\'">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_FFL_DEALER, 'page=' . $HTTP_GET_VARS['page'] .'&vendors_id=' . $HTTP_GET_VARS['vendors_id']. '&dID=' . $ffl_dealer['ffl_dealers_docs_id']) . '\'">' . "\n";
    }

      echo ' <td>' . $ffl_dealer['ffl_dealers_doc'] . '</td>' . "\n";
    
?>
                <td><?php echo $ffl_dealer['date_added']; ?></td>
                
                <td align="right"><?php if (isset($lInfo) && is_object($lInfo) && ($ffl_dealer['ffl_dealers_docs_id'] == $lInfo->ffl_dealers_docs_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_FFL_DEALER, 'page=' . $HTTP_GET_VARS['page'] .'&vendors_id=' . $HTTP_GET_VARS['vendors_id']. '&dID=' . $ffl_dealer['ffl_dealers_docs_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>

              </tr>
<?php
  }
?>
              <tr>
                <td colspan="3"><table class="table table-bordered table-hover">
                  <tr>
                    <td><?php echo $ffl_dealer_split->display_count($ffl_dealer_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_FFL_DEALERS); ?></td>
                    <td align="right"><?php echo $ffl_dealer_split->display_links($ffl_dealer_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>
                  </tr>
<?php
  if (empty($action)) {
?>
                  <tr>
                    <td align="right" colspan="2"><?php echo '<a href="' . tep_href_link(FILENAME_FFL_DEALER, 'page=' . $HTTP_GET_VARS['page'] .'&vendors_id=' . $HTTP_GET_VARS['vendors_id']. '&dID=' . $lInfo->ffl_dealers_docs_id . '&action=new') . '">' . tep_image_button('button_new_ffl_dealer.gif', IMAGE_NEW_FFL_DEALER) . '</a> &nbsp; <a href="' . tep_href_link(FILENAME_VENDOR_MODULES, 'module=ffldealershipping'.'&vendors_id=' . $HTTP_GET_VARS['vendors_id']). '">' . tep_image_button('button_back.gif', IMAGE_BACK_FFL_DEALER) . '</a>'; ?></td>
                  </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_FFL_DEALER . '</b>');

      $contents = array('form' => tep_draw_form('frm_ffl_dealer', FILENAME_FFL_DEALER, 'action=insert ','post',' enctype="multipart/form-data" '));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_FFL_DEALER_FILE . '<br>' . tep_draw_file_field('ffl_dealers_doc').tep_draw_hidden_field('vendors_id',$HTTP_GET_VARS['vendors_id']));
      
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_insert.gif', IMAGE_INSERT) . ' <a href="' . tep_href_link(FILENAME_FFL_DEALER, 'page=' . $HTTP_GET_VARS['page'] .'&vendors_id=' . $HTTP_GET_VARS['vendors_id']. '&dID=' . $HTTP_GET_VARS['dID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_LANGUAGE . '</b>');

      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $lInfo->ffl_dealers_doc . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . (($remove_language) ? '<a href="' . tep_href_link(FILENAME_FFL_DEALER, 'page=' . $HTTP_GET_VARS['page'] .'&vendors_id=' . $HTTP_GET_VARS['vendors_id']. '&dID=' . $lInfo->ffl_dealers_docs_id . '&action=deleteconfirm') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>' : '') . ' <a href="' . tep_href_link(FILENAME_FFL_DEALER, 'page=' . $HTTP_GET_VARS['page'] .'&vendors_id=' . $HTTP_GET_VARS['vendors_id']. '&dID=' . $lInfo->ffl_dealers_docs_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
	
      if (is_object($lInfo)) {
        $heading[] = array('text' => '<b>' . $lInfo->ffl_dealers_doc . '</b>');

        $contents[] = array('align' => 'center', 'text' => ' <a href="' . tep_href_link(FILENAME_FFL_DEALER, 'page=' . $HTTP_GET_VARS['page'] . '&dID=' . $lInfo->ffl_dealers_docs_id .'&vendors_id=' . $HTTP_GET_VARS['vendors_id']. '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a> <a href="' . tep_href_link(FILENAME_FFL_DEALER, 'page=' . $HTTP_GET_VARS['page'] .'&vendors_id=' . $HTTP_GET_VARS['vendors_id']. '&dID=' . $lInfo->ffl_dealers_docs_id . '&action=details') . '">' . tep_image_button('button_download.gif', IMAGE_DOWNLOAD_FFL_DEALER) . '</a>');
        
		$contents[] = array('text' => '<br>' . TEXT_INFO_FFL_DEALER_FILE . ' ' . $lInfo->ffl_dealers_doc);
        
     }
     break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table>
<!-- body_text_eof //-->

               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>