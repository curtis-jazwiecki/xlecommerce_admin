<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
$cron_script='yes';
require_once('cron_application_top.php');
$prev_selection_sql = tep_db_query("select * from header_tags_selection");
  
if(tep_db_num_rows($prev_selection_sql)){
  
  while($prev_selection = tep_db_fetch_array($prev_selection_sql)){
	
	if(!empty($prev_selection['product_selection'])){
		$product_selection = $prev_selection['product_selection'];
	}
	
	if(!empty($prev_selection['manufacturers_selection'])){
		$manufacturers_selection = $prev_selection['manufacturers_selection'];
	}
	
	if(!empty($prev_selection['category_selection'])){
		$category_selection = $prev_selection['category_selection'];
	}
	
	if(!empty($prev_selection['fillmetadesc'])){
		$fillmetadesc = $prev_selection['fillmetadesc'];
	}
	
	if(!empty($prev_selection['fillmetadesc_length'])){
		$fillmetadesc_length = $prev_selection['fillmetadesc_length'];
	}
  
  }
  
  $langID = 1;
  
  // code to fill category tags #start
  if ((isset($category_selection)) && ($category_selection != 'none') ){
	  
	
      $categories_tags_query = tep_db_query("select categories_name, categories_id, categories_htc_title_tag, categories_htc_desc_tag, categories_htc_keywords_tag, language_id from  " . TABLE_CATEGORIES_DESCRIPTION . " where language_id = '" . $langID . "'");
      
	  while ($categories_tags = tep_db_fetch_array($categories_tags_query)){
        $updateDB = false;
        
        if ($category_selection == 'empty'){
        
		   if (! tep_not_null($categories_tags['categories_htc_title_tag'])){
             $updateDB = true;
           }  
        
		}else if ($category_selection == 'full'){
		
		   $updateDB = true;
        
		}else {     //assume clear all
           tep_db_query("update " . TABLE_CATEGORIES_DESCRIPTION . " set categories_htc_title_tag='', categories_htc_desc_tag = '', categories_htc_keywords_tag = '' where categories_id = '" . $categories_tags['categories_id']."' and language_id  = '" . $langID . "'");
        }      
             
        if ($updateDB){
          tep_db_query("update " . TABLE_CATEGORIES_DESCRIPTION . " set categories_htc_title_tag='".addslashes($categories_tags['categories_name'])."', categories_htc_desc_tag = '". addslashes($categories_tags['categories_name'])."', categories_htc_keywords_tag = '". addslashes($categories_tags['categories_name']) . "' where categories_id = '" . $categories_tags['categories_id']."' and language_id  = '" . $langID . "'");
		}
		
      }
	
  
  }
  // code to fill category tags #ends
  
  // code to fill manufacturers tags #start
  if ((isset($manufacturers_selection)) && ($manufacturers_selection != 'none')){
	  
	  $manufacturers_tags_query = tep_db_query("select m.manufacturers_name, m.manufacturers_id, mi.languages_id, mi.manufacturers_htc_title_tag, mi.manufacturers_htc_desc_tag, mi.manufacturers_htc_keywords_tag from " . TABLE_MANUFACTURERS . " m, " . TABLE_MANUFACTURERS_INFO . " mi where m.manufacturers_id=mi.manufacturers_id and mi.languages_id = '" . $langID . "'");
	  while ($manufacturers_tags = tep_db_fetch_array($manufacturers_tags_query)){
		$updateDB = false;
		
		if ($manufacturers_selection == 'empty'){
		   if (! tep_not_null($manufacturers_tags['manufacturers_htc_title_tag'])){
			 $updateDB = true;
		   }  
		}else if ($manufacturers_selection == 'full'){
		   $updateDB = true;
		}else{ //assume clear all
		
		   tep_db_query("update " . TABLE_MANUFACTURERS_INFO . " set manufacturers_htc_title_tag='', manufacturers_htc_desc_tag = '', manufacturers_htc_keywords_tag = '' where manufacturers_id = '" . $manufacturers_tags['manufacturers_id']."' and languages_id  = '" . $langID . "'");
		   
		}      
			 
		if ($updateDB){
		  tep_db_query("update " . TABLE_MANUFACTURERS_INFO . " set manufacturers_htc_title_tag='".addslashes($manufacturers_tags['manufacturers_name'])."', manufacturers_htc_desc_tag = '". addslashes($manufacturers_tags['manufacturers_name'])."', manufacturers_htc_keywords_tag = '". addslashes($manufacturers_tags['manufacturers_name']) . "' where manufacturers_id = '" . $manufacturers_tags['manufacturers_id']."' and languages_id  = '" . $langID . "'");
		}
		
	  }
  }
  // code to fill manufacturers tags #ends
  
  // code to fill products tags #starts
  if ( (isset($product_selection)) && ($product_selection != 'none') ){
	
      $products_tags_query = tep_db_query("select products_name, products_description, products_id, products_head_title_tag, products_head_desc_tag, products_head_keywords_tag, language_id from " . TABLE_PRODUCTS_DESCRIPTION . " where language_id = '" . $langID . "'");
	  
      while ($products_tags = tep_db_fetch_array($products_tags_query)){
        $updateDB = false;
        
        if ($product_selection == 'empty'){
          if (! tep_not_null($products_tags['products_head_title_tag'])){
            $updateDB = true;
          } 
		   
        }else if ($product_selection == 'full'){
			
          $updateDB = true;
		  
        }else {      //assume clear all
          tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_head_title_tag='', products_head_desc_tag = '', products_head_keywords_tag =  '' where products_id = '" . $products_tags['products_id'] . "' and language_id='". $langID ."'");
        }
               
        if ($updateDB){
			
          if ($fillmetadesc == 'yes'){
			  
		    //fill the description with all or part of the product description
            if (! empty($products_tags['products_description'])){
            
              if (isset($fillmetadesc_length) && (int)$fillmetadesc_length > 3 && (int)$fillmetadesc_length < strlen($products_tags['products_description']))
                $desc = substr($products_tags['products_description'], 0, (int)$fillmetadesc_length);
              else                                              //length not entered or too small    
                $desc = $products_tags['products_description']; //so use the whole description
            }else
              $desc = $products_tags['products_name'];  

		  }else{        
            $desc = $products_tags['products_name'];           
          }  

          tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_head_title_tag='".addslashes($products_tags['products_name'])."', products_head_desc_tag = '". addslashes(strip_tags($desc))."', products_head_keywords_tag =  '" . addslashes($products_tags['products_name']) . "' where products_id = '" . $products_tags['products_id'] . "' and language_id='". $langID ."'");
        } 
      }  
  }
  // code to fill products tags #ends
  
}
?>