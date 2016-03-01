<?php
/*
  $Id: stats_keywords.php,v 0.90 10/03/2002 03:15:00 Exp $
	by Cheng	


  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  if(isset($_GET['txtWord']) && $_GET['txtWord'] != '' && isset($_GET['txtReplacement']) && $_GET['txtReplacement'] != '' && !isset($_GET['updateword'])){
    $newword_sql = "INSERT INTO searchword_swap (sws_word, sws_replacement)VALUES('" . addslashes($_GET['txtWord']) . "', '" . addslashes($_GET['txtReplacement']) . "' )";
    $result = tep_db_query($newword_sql);
    header('location: ' . tep_href_link('stats_keywords.php', 'action=' . BUTTON_VIEW_WORD_LIST . ''));
    exit;	  	
  }
  
  if(isset($_GET['removeword']) && isset($_GET['delete'])){
   $word_delete_sql = "DELETE FROM searchword_swap WHERE sws_id = " . $_GET['delete'];
   $result = tep_db_query($word_delete_sql);
   header('location: ' . tep_href_link('stats_keywords.php', 'action=' . BUTTON_VIEW_WORD_LIST . ''));    	  	
  }

  if(isset($_GET['editword']) && isset($_GET['link'])){
   $word_select_sql = "SELECT * FROM searchword_swap WHERE sws_id = " . $_GET['edit'];
   $result = tep_db_query($word_select_sql);
   $word_select_result = tep_db_fetch_array($result);  	  	
  } 

  if(isset($_GET['editword']) && isset($_GET['updateword'])){
   $word_update_sql = "UPDATE searchword_swap SET sws_word= '" . addslashes($_GET['txtWord']) . "', sws_replacement = '" . addslashes($_GET['txtReplacement']) . "' WHERE  sws_id = " . $_GET['id'];
   $result = tep_db_query($word_update_sql);
   header('location: ' . tep_href_link('stats_keywords.php', 'action=' . BUTTON_VIEW_WORD_LIST . ''));    	  	
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
<table class="table table-bordered table-hover">
  <tr>
<!-- body_text //-->
    <td>
<table class="table table-bordered table-hover">
  <tr>
    <td colspan="2">
<?php
	
if ($_GET['action'] == 'Delete') {
	tep_db_query("delete from search_queries_sorted");
} // delete db					

if ($_GET['update'] == BUTTON_UPDATE_WORD_LIST) {
    $sql_q = tep_db_query("SELECT DISTINCT search_text, COUNT(*) AS ct FROM search_queries GROUP BY search_text");

       while ($sql_q_result = tep_db_fetch_array($sql_q)) {                        				
	   $update_q = tep_db_query("select search_text, search_count from search_queries_sorted where search_text = '" . $sql_q_result['search_text'] . "'");
           $update_q_result = tep_db_fetch_array($update_q);
           $count = $sql_q_result['ct'] + $update_q_result['search_count'];

             if ($update_q_result['search_count'] != '') {
	        tep_db_query("update search_queries_sorted set search_count = '" . $count . "' where search_text = '" . $sql_q_result['search_text'] . "'");
	     } else {
                tep_db_query("insert into search_queries_sorted (search_text, search_count) values ('" . 
		 $sql_q_result['search_text'] . "'," . $count . ")");
	     } // search_count

           tep_db_query("delete from search_queries");
        } // while
 } // updatedb

?>
<?php if(isset($_GET['action']) && $_GET['action']== BUTTON_VIEW_WORD_LIST)
//switch for view word list
{  echo tep_draw_form('addwords', 'stats_keywords.php', '', 'get');
	
	?>
<table class="table table-bordered table-hover">
<?php if(isset($_GET['add'])) { ?>
<tr><td colspan="4">
<table class="table table-bordered table-hover"><tr><td>
  <table class="table table-bordered table-hover"><tr>
    <td><br><?php echo WORD_ENTRY_ORIGINAL ?> 
    <input type="text" name="txtWord" value="<?php if(isset($word_select_result['sws_word'])){echo stripslashes($word_select_result['sws_word']);} ?>" size="12">&nbsp;
    <?php echo WORD_ENTRY_REPLACEMENT ?>
    <input type="text" name="txtReplacement" value="<?php if(isset($word_select_result['sws_replacement'])){echo stripslashes($word_select_result['sws_replacement']);} ?>" size="12"></td>
    <?php if(isset($word_select_result['sws_id'])){echo '<input type="hidden" name="id" value="' . $word_select_result['sws_id'] . '">';} ?>
  </tr>
  <tr>
    <td><?php  if(isset($_GET['editword']) && isset($_GET['link'])){ ?>
    <input type="submit" name="editword" value="<?php echo BUTTON_EDIT_WORD ?>">
    <input type="hidden" name="updateword" value="1">
    <br><br><?php }
    else { ?>
    <input type="submit" name="newword" value="<?php echo BUTTON_ADD_WORD ?>"><br><br>
    <?php } ?>
    </td>
  </tr>
  </table></td></tr></table>
  </d></tr>  
<?php } ?>  
  <tr>
    <td><?php echo WORD_ENTRY_ORIGINAL ?></td>
    <td colspan="3"><?php echo WORD_ENTRY_REPLACEMENT ?></td>
  </tr>
<?php

$pw_word_sql = "SELECT * FROM searchword_swap ORDER BY sws_word ASC" ;
$pw_words = tep_db_query($pw_word_sql);
    while ($pw_words_result = tep_db_fetch_array($pw_words)) { ?>
  <tr>
    <td><?php echo stripslashes($pw_words_result['sws_word']); ?></td>  
    <td><?php echo stripslashes($pw_words_result['sws_replacement']); ?></td>
    <td><a href="<?php echo tep_href_link('stats_keywords.php', 'editword=1&link=1&add=1&action=' . BUTTON_VIEW_WORD_LIST . '&edit=' . $pw_words_result['sws_id']); ?>"><u><?php echo LINK_EDIT ?></u></a></td>
    <td class="dataTableHeadingContent"><a href="<?php echo tep_href_link('stats_keywords.php', 'removeword=1&delete=' . $pw_words_result['sws_id']); ?>"><u><?php echo LINK_DELETE ?></u></a></td>
  </tr>
<?php    } // while 
?>
  <tr>
    <td colspan="4" align="right"><br><input type="submit" value="New Entry" name="add" method="post" />
    <input type="hidden" name="action" value="<?php echo BUTTON_VIEW_WORD_LIST ?>"></td>
  </tr>
</table></form>
    <?php } //end 'if' switch for view word list
    
    
    
    if(!isset($_GET['action']) && $_GET['action'] != BUTTON_VIEW_WORD_LIST){
    	?>
    	<table class="table table-bordered table-hover">
  <tr>
    <td><?php echo KEYWORD_TITLE ?></td>
    <td><?php echo KEYWORD_TITLE2 ?></td>
  </tr>
<?php

switch($_GET['sortorder']){
  case BUTTON_SORT_NAME:
    $pw_sql = "SELECT search_text, search_count FROM search_queries_sorted ORDER BY search_text ASC" ;
  break;
  case BUTTON_SORT_TOTAL:
    $pw_sql = "SELECT search_text, search_count FROM search_queries_sorted ORDER BY search_count DESC" ;
  break;
  default:
    $pw_sql = "SELECT search_text, search_count FROM search_queries_sorted ORDER BY search_text ASC" ;
  break;
}

$sql_q = tep_db_query($pw_sql);
    while ($sql_q_result = tep_db_fetch_array($sql_q)) { ?>
  <tr class="dataTableRow"  onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onMouseOut="this.className='dataTableRow'" onClick="document.location.href='<?php echo tep_catalog_href_link( 'advanced_search_result.php', 'keywords=' . urlencode($sql_q_result['search_text']). '&search_in_description=1' ); ?>'" >
    <td><a target="_blank" href="<?php echo tep_catalog_href_link( 'advanced_search_result.php', 'keywords=' . urlencode($sql_q_result['search_text']). '&search_in_description=1' ); ?>"><?php echo $sql_q_result['search_text']; ?></a></td>  
    <td><?php echo $sql_q_result['search_count']; ?></td>
  </tr>
<?php    } // while 
?>
    </td></tr></table>
    	
     <?php } ?>
    </td>
  </tr>
 </table>
    </td>
<!-- body_eof //-->
<!-- right_column_bof //-->
<td valign="top" width="25%">
<?php echo tep_draw_form('delete', 'stats_keywords.php', '', 'get'); ?>
<table class="table table-bordered table-hover">
  <tr>
    <td>
<?php
    $heading = array();
    $contents = array();

    $heading[]  = array('text'  => '<b>' . SIDEBAR_HEADING . '</b>');

    $contents[] = array('text'  => '<br>' . SIDEBAR_INFO_1);
    $contents[] = array('text'  => '<input type="submit" name="update" value="' . BUTTON_UPDATE_WORD_LIST . '">');
    $contents[] = array('text'  =>  tep_draw_separator());
    $contents[] = array('text'  => '<br><input type="submit" name="sortorder" value="' . BUTTON_SORT_NAME . '"><br><input type="submit" name="sortorder" value="' . BUTTON_SORT_TOTAL . '">');
    $contents[] = array('text'  =>  tep_draw_separator());
    $contents[] = array('text'  => '<br>' . SIDEBAR_INFO_2);
    $contents[] = array('text'  => '<input type="submit" value="' . BUTTON_DELETE . '" name="action">');
    $contents[] = array('text'  =>  tep_draw_separator());
    $contents[] = array('text'  => SIDEBAR_INFO_3);
    $contents[] = array('text'  => '<input type="submit" name="action" value="' . BUTTON_VIEW_WORD_LIST . '">');

    
  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {

    $box = new box;
    echo $box->infoBox($heading, $contents);
  } ?>    
</td></tr></table></form>
</td>
  </tr>
</table>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>