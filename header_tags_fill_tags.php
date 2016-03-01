<?php
/*
  $Id: header_tags_fill_tags.php,v 1.0 2005/08/25
  Originally Created by: Jack York - http://www.oscommerce-solution.com
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
 
  require('includes/application_top.php'); 
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_HEADER_TAGS_CONTROLLER);
  
  /*Array
(
    [action] => process
    [fill_language] => 1
    [group4] => fillmetaDesc_no
    [fillMetaDescrlength] => 
    [group1] => empty
    [group2] => empty
    [group3] => empty
    [x] => 51
    [y] => 10
)*/
 
  /****************** READ IN FORM DATA ******************/
  $categories_fill = $_POST['group1'];
  $manufacturers_fill = $_POST['group2'];
  $products_fill = $_POST['group3'];
  $productsMetaDesc = $_POST['group4'];
  $productsMetaDescLength = $_POST['fillMetaDescrlength'];
 
  $checkedCats = array();
  $checkedManuf = array();
  $checkedProds = array();
  $checkedMetaDesc = array();
  
  $languages = tep_get_languages();
  $languages_array = array();
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $languages_array[] = array('id' => $languages[$i]['id'], // $i + 1, 
                               'text' => $languages[$i]['name']);
  }
  $langID = $languages_id; 
  $updateDB = false;
  $updateTextCat = '';
  $updateTextManuf = '';
  $updateTextProd = '';
    
  /****************** FILL THE CATEGORIES ******************/
   
  if (isset($categories_fill)){
	  
    $langID = $_POST['fill_language'];
	$category_selection = "";
    
    if ($categories_fill == 'none'){
       $checkedCats['none'] = 'Checked';
	   $category_selection = "none";
    }else{
	   
      $categories_tags_query = tep_db_query("select categories_name, categories_id, categories_htc_title_tag, categories_htc_desc_tag, categories_htc_keywords_tag, language_id from  " . TABLE_CATEGORIES_DESCRIPTION . " where language_id = '" . $langID . "'");
      while ($categories_tags = tep_db_fetch_array($categories_tags_query)){
        $updateDB = false;
        
        if ($categories_fill == 'empty'){
        
		   if (! tep_not_null($categories_tags['categories_htc_title_tag'])){
             $updateDB = true;
             $updateTextCat = 'Empty Category tags have been filled.';
           }  
           $checkedCats['empty'] = 'Checked';
		   $category_selection = "empty";
        
		}else if ($categories_fill == 'full'){
		
		   $updateDB = true;
           $updateTextCat = 'All Category tags have been filled.';
           $checkedCats['full'] = 'Checked';
		   $category_selection = "full";
		
		}else {     //assume clear all
           tep_db_query("update " . TABLE_CATEGORIES_DESCRIPTION . " set categories_htc_title_tag='', categories_htc_desc_tag = '', categories_htc_keywords_tag = '' where categories_id = '" . $categories_tags['categories_id']."' and language_id  = '" . $langID . "'");
           $updateTextCat = 'All Category tags have been cleared.';
           $checkedCats['clear'] = 'Checked';
		   $category_selection = "clear";
        }      
             
        if ($updateDB)
          tep_db_query("update " . TABLE_CATEGORIES_DESCRIPTION . " set categories_htc_title_tag='".addslashes($categories_tags['categories_name'])."', categories_htc_desc_tag = '". addslashes($categories_tags['categories_name'])."', categories_htc_keywords_tag = '". addslashes($categories_tags['categories_name']) . "' where categories_id = '" . $categories_tags['categories_id']."' and language_id  = '" . $langID . "'");
      }
	}
	
	if(count($checkedCats) > 0){
		tep_db_query("delete from header_tags_selection where category_selection <> ''");
		tep_db_query("INSERT INTO header_tags_selection set category_selection = '".$category_selection."'"); 
	}
	
  }
  else
    $checkedCats['none'] = 'Checked';
  

  /****************** FILL THE MANUFACTURERS ******************/
   
  if (isset($manufacturers_fill)){
		  
		$langID = $_POST['fill_language'];
		$manufacturers_selection = '';
		
		if ($manufacturers_fill == 'none') 
		{
		   $checkedManuf['none'] = 'Checked';
		   $manufacturers_selection = 'none';
		}
		else
		{ 
		  $manufacturers_tags_query = tep_db_query("select m.manufacturers_name, m.manufacturers_id, mi.languages_id, mi.manufacturers_htc_title_tag, mi.manufacturers_htc_desc_tag, mi.manufacturers_htc_keywords_tag from " . TABLE_MANUFACTURERS . " m, " . TABLE_MANUFACTURERS_INFO . " mi where m.manufacturers_id=mi.manufacturers_id and mi.languages_id = '" . $langID . "'");
		  while ($manufacturers_tags = tep_db_fetch_array($manufacturers_tags_query))
		  {
			$updateDB = false;
			
			if ($manufacturers_fill == 'empty')
			{
			   if (! tep_not_null($manufacturers_tags['manufacturers_htc_title_tag']))
			   {
				 $updateDB = true;
				 $updateTextManuf = 'Empty Manufacturers tags have been filled.';
			   }  
			   $checkedManuf['empty'] = 'Checked';
			   $manufacturers_selection = 'empty';
			}
			else if ($manufacturers_fill == 'full')
			{
			   $updateDB = true;
			   $updateTextManuf = 'All Manufacturers tags have been filled.';
			   $checkedManuf['full'] = 'Checked';
			   $manufacturers_selection = 'full';
			}
			else      //assume clear all
			{
			   tep_db_query("update " . TABLE_MANUFACTURERS_INFO . " set manufacturers_htc_title_tag='', manufacturers_htc_desc_tag = '', manufacturers_htc_keywords_tag = '' where manufacturers_id = '" . $manufacturers_tags['manufacturers_id']."' and languages_id  = '" . $langID . "'");
			   $updateTextManuf = 'All Manufacturers tags have been cleared.';
			   $checkedManuf['clear'] = 'Checked';
			   $manufacturers_selection = 'clear';
			}      
				 
			if ($updateDB)
			  tep_db_query("update " . TABLE_MANUFACTURERS_INFO . " set manufacturers_htc_title_tag='".addslashes($manufacturers_tags['manufacturers_name'])."', manufacturers_htc_desc_tag = '". addslashes($manufacturers_tags['manufacturers_name'])."', manufacturers_htc_keywords_tag = '". addslashes($manufacturers_tags['manufacturers_name']) . "' where manufacturers_id = '" . $manufacturers_tags['manufacturers_id']."' and languages_id  = '" . $langID . "'");
		  }
		}
		
		if(count($checkedManuf) > 0){
			tep_db_query("delete from header_tags_selection where manufacturers_selection <> ''");
			tep_db_query("INSERT INTO header_tags_selection set manufacturers_selection = '".$manufacturers_selection."'"); 
		}
	  
  }
  else
    $checkedManuf['none'] = 'Checked';



  /****************** FILL THE PRODUCTS ******************/  
  
  if (isset($products_fill)){
	  
    $langID = $_POST['fill_language'];
	$product_selection = '';
    
    if ($products_fill == 'none') 
    {
       $checkedProds['none'] = 'Checked';
	   $product_selection = 'none';
    }
    else
    { 
      $products_tags_query = tep_db_query("select products_name, products_description, products_id, products_head_title_tag, products_head_desc_tag, products_head_keywords_tag, language_id from " . TABLE_PRODUCTS_DESCRIPTION . " where language_id = '" . $langID . "'");
      while ($products_tags = tep_db_fetch_array($products_tags_query))
      {
        $updateDB = false;
        
        if ($products_fill == 'empty')
        {
          if (! tep_not_null($products_tags['products_head_title_tag']))
          {
            $updateDB = true;
            $updateTextProd = 'Empty Product tags have been filled.';
          }  
          $checkedProds['empty'] = 'Checked';
		  $product_selection = 'empty';
        }
        else if ($products_fill == 'full')
        {
          $updateDB = true;
          $updateTextProd = 'All Product tags have been filled.';
          $checkedProds['full'] = 'Checked';
		  $product_selection = 'full';
        }
        else      //assume clear all
        {
          tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_head_title_tag='', products_head_desc_tag = '', products_head_keywords_tag =  '' where products_id = '" . $products_tags['products_id'] . "' and language_id='". $langID ."'");
          $updateTextProd = 'All Product tags have been cleared.';
          $checkedProds['clear'] = 'Checked';
		  $product_selection = 'clear';
        }
               
        if ($updateDB)
        {
          if ($productsMetaDesc == 'fillMetaDesc_yes')          //fill the description with all or part of the 
          {                                                     //product description
            if (! empty($products_tags['products_description']))
            {
              if (isset($productsMetaDescLength) && (int)$productsMetaDescLength > 3 && (int)$productsMetaDescLength < strlen($products_tags['products_description']))
                $desc = substr($products_tags['products_description'], 0, (int)$productsMetaDescLength);
              else                                              //length not entered or too small    
                $desc = $products_tags['products_description']; //so use the whole description
            }   
            else
              $desc = $products_tags['products_name'];  

            $checkedMetaDesc['no'] = '';
            $checkedMetaDesc['yes'] = 'Checked';
          }  
          else
          {        
            $desc = $products_tags['products_name'];           
            $checkedMetaDesc['no'] = 'Checked';
            $checkedMetaDesc['yes'] = '';
          }  

          tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_head_title_tag='".addslashes($products_tags['products_name'])."', products_head_desc_tag = '". addslashes(strip_tags($desc))."', products_head_keywords_tag =  '" . addslashes($products_tags['products_name']) . "' where products_id = '" . $products_tags['products_id'] . "' and language_id='". $langID ."'");
        } 
      }  
    }
	
	$fillmetadesc = 'no';
	if($checkedMetaDesc['yes'] == 'Checked'){
		$fillmetadesc = 'yes';
	}
	
	$fillmetadesc_length = "";
	if(isset($productsMetaDescLength) && ($productsMetaDescLength != '')){
		$fillmetadesc_length = $productsMetaDescLength;
	}
	
	if(count($checkedProds) > 0){
		tep_db_query("delete from header_tags_selection where product_selection <> ''");
		tep_db_query("delete from header_tags_selection where fillmetadesc <> ''");
		tep_db_query("delete from header_tags_selection where fillmetadesc_length <> ''");
		
		tep_db_query("INSERT INTO header_tags_selection set product_selection = '".$product_selection."'");
		tep_db_query("INSERT INTO header_tags_selection set fillmetadesc = '".$fillmetadesc."'");
		tep_db_query("INSERT INTO header_tags_selection set fillmetadesc_length = '".$fillmetadesc_length."'"); 
	}
  
  }else{ 
    $checkedProds['none']   = 'Checked';
    $checkedMetaDesc['no']  = 'Checked';
    $checkedMetaDesc['yes'] = '';
  }
  
  
  $prev_selection_sql = tep_db_query("select * from header_tags_selection");
  if(tep_db_num_rows($prev_selection_sql)){
	  while($prev_selection = tep_db_fetch_array($prev_selection_sql)){
	  	if(!empty($prev_selection['product_selection'])){
	  		$checkedProds[$prev_selection['product_selection']] = 'Checked';
	  	}
		
		if(!empty($prev_selection['manufacturers_selection'])){
	  		$checkedManuf[$prev_selection['manufacturers_selection']] = 'Checked';
	  	}
		
		if(!empty($prev_selection['category_selection'])){
	  		$checkedCats[$prev_selection['category_selection']] = 'Checked';
	  	}
		
		if(!empty($prev_selection['fillmetadesc_length'])){
			$productsMetaDescLength = $prev_selection['fillmetadesc_length'];
		}
		
		if(!empty($prev_selection['fillmetadesc'])){
			$checkedMetaDesc = array();
			$checkedMetaDesc[$prev_selection['fillmetadesc']] = 'Checked';
		}
		
	  }
  }
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3><?php echo HEADING_TITLE_FILL_TAGS; ?>
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               <div class="panel-heading"><?php echo HEADING_TITLE_FILL_TAGS; ?>
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
      <td class="HTC_subHead"><?php echo TEXT_FILL_TAGS; ?></td>
     </tr>
     
     <!-- Begin of Header Tags -->      
     
     <tr>
      <td align="right"><?php echo tep_draw_form('header_tags', FILENAME_HEADER_TAGS_FILL_TAGS, '', 'post') . tep_draw_hidden_field('action', 'process'); ?></td>
       <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
     </tr>
     <tr>
      <td><table class="table table-bordered table-hover">
       <tr>
        <td>Language:&nbsp;</td>
        <td><?php echo tep_draw_pull_down_menu('fill_language', $languages_array, $langID);?></td>
       </tr>
      </table> 

      <table class="table table-bordered table-hover">
       <tr> 
        <td>Fill products meta description with Products Description?</td>
        <td><INPUT TYPE="radio" NAME="group4" VALUE="fillMetaDesc_yes" <?php echo $checkedMetaDesc['yes']; ?>> Yes</td>
        <td><INPUT TYPE="radio" NAME="group4" VALUE="fillmetaDesc_no" <?php echo $checkedMetaDesc['no']; ?>> No</td>
        <td align="right"><?php echo 'Limit to '. tep_draw_input_field('fillMetaDescrlength', $productsMetaDescLength, 'maxlength="255", size="5"', false) . ' characters.'; ?> </td>
       </tr>
      </table></td> 
     </tr>     
       <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
       </tr>
       
       <tr>
        <td><table class="table table-bordered table-hover">
         <tr>
          <th><?php echo HEADING_TITLE_CONTROLLER_CATEGORIES; ?></th>
          <th><?php echo HEADING_TITLE_CONTROLLER_MANUFACTURERS; ?></th>          
          <th><?php echo HEADING_TITLE_CONTROLLER_PRODUCTS; ?></th>
         </tr> 
         <tr>          
          <td align=left><INPUT TYPE="radio" NAME="group1" VALUE="none" <?php echo $checkedCats['none']; ?>> <?php echo HEADING_TITLE_CONTROLLER_SKIPALL; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group2" VALUE="none" <?php echo $checkedManuf['none']; ?>> <?php echo HEADING_TITLE_CONTROLLER_SKIPALL; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group3" VALUE="none" <?php echo $checkedProds['none']; ?>> <?php echo HEADING_TITLE_CONTROLLER_SKIPALL; ?></td>
         </tr>
         <tr> 
          <td align=left><INPUT TYPE="radio" NAME="group1" VALUE="empty" <?php echo $checkedCats['empty']; ?> > <?php echo HEADING_TITLE_CONTROLLER_FILLONLY; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group2" VALUE="empty" <?php echo $checkedManuf['empty']; ?>> <?php echo HEADING_TITLE_CONTROLLER_FILLONLY; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group3" VALUE="empty" <?php echo $checkedProds['empty']; ?>> <?php echo HEADING_TITLE_CONTROLLER_FILLONLY; ?></td>
         </tr>
         <tr> 
          <td align=left><INPUT TYPE="radio" NAME="group1" VALUE="full" <?php echo $checkedCats['full']; ?>> <?php echo HEADING_TITLE_CONTROLLER_FILLALL; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group2" VALUE="full" <?php echo $checkedManuf['full']; ?>> <?php echo HEADING_TITLE_CONTROLLER_FILLALL; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group3" VALUE="full" <?php echo $checkedProds['full']; ?>> <?php echo HEADING_TITLE_CONTROLLER_FILLALL; ?></td>
         </tr>
         <tr> 
          <td align=left><INPUT TYPE="radio" NAME="group1" VALUE="clear" <?php echo $checkedCats['clear']; ?>> <?php echo HEADING_TITLE_CONTROLLER_CLEARALL; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group2" VALUE="clear" <?php echo $checkedManuf['clear']; ?>> <?php echo HEADING_TITLE_CONTROLLER_CLEARALL; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group3" VALUE="clear" <?php echo $checkedProds['clear']; ?>> <?php echo HEADING_TITLE_CONTROLLER_CLEARALL; ?></td>
         </tr>
        </table></td>
       </tr> 
       
       <tr>
        <td><table class="table table-bordered table-hover">
         <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
         </tr>
         <tr> 
          <td align="center"><?php echo (tep_image_submit('button_update.gif', IMAGE_UPDATE) ) . ' <a href="' . tep_href_link(FILENAME_HEADER_TAGS_ENGLISH, tep_get_all_get_params(array('action'))) .'">' . '</a>'; ?></td>
         </tr>
         <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
         </tr>         
         <?php if (tep_not_null($updateTextCat)) { ?>
          <tr>
           <td class="HTC_subHead"><?php echo $updateTextCat; ?></td>
          </tr> 
          <?php }  
           if (tep_not_null($updateTextManuf)) { ?>
          <tr>
           <td class="HTC_subHead"><?php echo $updateTextManuf; ?></td>
          </tr>
         <?php } 
           if (tep_not_null($updateTextProd)) { ?>
          <tr>
           <td class="HTC_subHead"><?php echo $updateTextProd; ?></td>
          </tr>
         <?php } ?> 
        </table></td>
       </tr>
      </form>
      </td>
     </tr>
     <!-- end of Header Tags -->
   </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<style type="text/css">
td.HTC_Head {color: sienna; font-size: 24px; font-weight: bold; } 
td.HTC_subHead {color: #000000; font-size: 14px; } 
.dataTableHeadingRow { background-color: #C9C9C9; background-image:url(../../images/template/admin_table_heading_content.jpg); background-repeat:repeat-x; }
.dataTableHeadingContent { font-family: Verdana, Arial, sans-serif; font-size: 10px; color: #ffffff; font-weight: bold; }
.pageHeading { font-family: Verdana, Arial, sans-serif; font-size: 18px; color: #FFFFFF; font-weight: bold; }
</style>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>