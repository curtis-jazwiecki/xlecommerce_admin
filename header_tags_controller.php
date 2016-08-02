<?php

/*

  $Id: header_tags_controller.php,v 1.2 2004/08/07 22:50:52 hpdl Exp $

  header_tags_controller Originally Created by: Jack York

 CloudCommerce - Multi-Channel eCommerce Solutions

  http://www.cloudcommerce.org

  Copyright(c)2016 Outdoor Business Network, Inc.
  

*/

 

  require('includes/application_top.php');

  require('includes/functions/header_tags.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_HEADER_TAGS_CONTROLLER);

 

  $filenameInc = DIR_FS_CATALOG . 'includes/header_tags.php';

  $filenameEng = DIR_FS_CATALOG . DIR_WS_LANGUAGES . $language . '/header_tags.php';



  if (GetPermissions(DIR_FS_CATALOG_IMAGES) != Getpermissions($filenameInc))

   $messageStack->add("Permissions settings for the $filenameInc file appear to be incorrect");

  if (GetPermissions(DIR_FS_CATALOG_IMAGES) != Getpermissions($filenameEng))

   $messageStack->add("Permissions settings for the $filenameEng file appear to be incorrect");   

  

  $action       = (isset($HTTP_POST_VARS['action']) ? $HTTP_POST_VARS['action'] : '');

  $actionDelete = (isset($HTTP_POST_VARS['action_delete']) ? $HTTP_POST_VARS['action_delete'] : '');

  $actionCheck  = (isset($HTTP_POST_VARS['action_check']) ? $HTTP_POST_VARS['action_check'] : '');

  

  if (tep_not_null($action)) 

  {

    $args = array();

    $args['page'] = $_POST['page'];

    $args['title'] = $_POST['title'];

    $args['desc'] = addslashes($_POST['desc']);

    $args['keyword'] = addslashes($_POST['keyword']);

    $args['htta'] = ($_POST['htta'] == 'on') ? 1 : 0;

    $args['htda'] = ($_POST['htda'] == 'on') ? 1 : 0;

    $args['htka'] = ($_POST['htka'] == 'on') ? 1 : 0;

    $args['htca'] = ($_POST['htca'] == 'on') ? 1 : 0;    

    

    if (($pos = strpos($args['page'], ".php")) !== FALSE)  //remove .php from page 

       $args['page'] = substr($args['page'], 0, $pos);     //if present

   

    $fp = file($filenameEng);  

    $checkOnce = true;

    $lastSection = '';

    $insertPoint = 0;

    $markPoint = count($fp) - 1; 

    

    if (ValidPageName($args['page']) && NotDuplicatePage($fp, $args['page']))

    {

      /*********************** ENGLISH SECTION ************************/

      for ($idx = 0; $idx < count($fp); ++$idx)  //find where to insert the new page

      {     

         if ($checkOnce && strpos($fp[$idx], "// DEFINE TAGS FOR INDIVIDUAL PAGES") === FALSE)

            continue;

         

         $checkOnce = false;   

         $section = GetSectionName($fp[$idx]);   

         

         if (! empty($section))

         {

            if (strcasecmp($section, $args['page']) < 0)

            {         

               $lastSection = $section;    

               $markPoint = $idx;       

            }   

            else if (strcasecmp($section, $args['page']) > 0)

            {

               if ($insertPoint == 0)

                 $insertPoint = $idx;

            }      

         }

      }

      

      if ($insertPoint != count($fp))              //backup one line for appearance

        $insertPoint--;

         

      $fileUpper = ConvertDash(strtoupper($args['page']));      //prepare the english array

      $engArray = array();

      $engArray['page'] = sprintf("// %s.php\n", $args['page']);  

      $engArray['htta'] = sprintf("define('HTTA_%s_ON','%d');\n", $fileUpper, $args['htta']);

      $engArray['htda'] = sprintf("define('HTDA_%s_ON','%d');\n", $fileUpper, $args['htda']);

      $engArray['htka'] = sprintf("define('HTKA_%s_ON','%d');\n", $fileUpper, $args['htka']);

      $engArray['title'] = sprintf("define('HEAD_TITLE_TAG_%s','%s');\n", $fileUpper, $args['title']);

      $engArray['desc'] = sprintf("define('HEAD_DESC_TAG_%s','%s');\n", $fileUpper, $args['desc']);

      $engArray['keyword'] = sprintf("define('HEAD_KEY_TAG_%s','%s');\n", $fileUpper, $args['keyword']);

               

      array_splice($fp, $insertPoint, 0, $engArray);  

      WriteHeaderTagsFile($filenameEng, $fp);   

           

      /*********************** INCLUDES SECTION ************************/     

      $fp = file($filenameInc); 

      $checkOnce = true;

      $insertPoint = 0;

      $markPoint = count($fp) - 1;

      

      for ($idx = 0; $idx < count($fp); ++$idx)  //find where to insert the new page

      {     

         if ($checkOnce && strpos($fp[$idx], "switch (true)") === FALSE)

            continue;

         $checkOnce = false;   

         $section = GetSectionName($fp[$idx]);   

                 

         if (! empty($section))

         {

            if (strcasecmp($section, $args['page']) < 0)

            {         

               $lastSection = $section;    

               $markPoint = $idx;       

            }   

            else if (strcasecmp($section, $args['page']) > 0)

            {

               if ($insertPoint == 0)

                 $insertPoint = $idx;

            }                  

         }

         else if (strpos($fp[$idx], "// ALL OTHER PAGES NOT DEFINED ABOVE") !== FALSE)

         { 

            $insertPoint = $idx;

            break;

         }    

      }

  

      if ($insertPoint != count($fp))              //backup one line for appearance

        $insertPoint--;      



      $incArray = array();

      $fileUpper = ConvertDash(strtoupper($args['page']));

      $spaces = 10;

      $incArray['page'] = sprintf("\n// %s.php\n", $args['page']);  

      $incArray['case'] = sprintf("  case (strstr(\$_SERVER['PHP_SELF'],FILENAME_%s) or strstr(\$PHP_SELF, FILENAME_%s));\n",$fileUpper, $fileUpper);

      $incArray['line'] = sprintf("    \$tags_array = tep_header_tag_page(HTTA_%s_ON, HEAD_TITLE_TAG_%s, \n%38sHTDA_%s_ON, HEAD_DESC_TAG_%s, \n%38sHTKA_%s_ON, HEAD_KEY_TAG_%s );\n   break;\n",$fileUpper, $fileUpper, " ", $fileUpper, $fileUpper, " ", $fileUpper, $fileUpper );  

   

      array_splice($fp, $insertPoint, 0, $incArray);  

      WriteHeaderTagsFile($filenameInc, $fp);   

    } 

    else

    {

      if (! ValidPageName($args['page']))

        $error = HEADING_TITLE_CONTROLLER_PAGENAME_INVALID_ERROR  . $args['page'];

      else

        $error = HEADING_TITLE_CONTROLLER_PAGENAME_ERROR . $args['page'];

      $messageStack->add($error);

    }

  } 

  else if (tep_not_null($actionDelete))

  {

     /******************** Delete the English entries ********************/

     $page_to_delete = $_POST['delete_page'].'.php';

     $nodelete_pages = array(0 => 'index.php', 

                             1 => 'product_info.php', 

                             2 => 'product_reviews_info.php', 

                             3 => 'products_new.php',

                             4 => 'specials.php',
                             5 => 'shop.php');

     $fp = file($filenameEng);

     $found = false; 

     $delStart = 0;

     $delStop = 0;

     for ($idx = 0; $idx < count($fp); ++$idx)

     {

        if (! $found && strpos($fp[$idx], $page_to_delete) !== FALSE)

        {

            $delStart = $idx;   //adjust for 0 start

            $found = true;

        }

        else if ($found && (tep_not_null($fp[$idx]) && strpos($fp[$idx], ".php") === FALSE))

            $delStop++;

        else if ($found && (! tep_not_null($fp[$idx]) || strpos($fp[$idx], ".php") !== FALSE))

        {

            $delStop++;

            break;

        }    

     }



     if ($found == true)          //page entry may not be present

     {

        if (in_array($page_to_delete, $nodelete_pages)) 

        {

          $error = sprintf(HEADING_TITLE_CONTROLLER_NO_DELETE_ERROR, $page_to_delete);

          $messageStack->add($error);

        }

        else

        {

          array_splice($fp, $delStart, $delStop);

          WriteHeaderTagsFile($filenameEng, $fp);

        }  

     } 

     

     /******************** Delete the includes entries *******************/

     $fp = file($filenameInc);

     $checkOnce = true;

     $found = false; 

     $delStart = 0;

     $delStop = 0;

     

     for ($idx = 0; $idx < count($fp); ++$idx)

     {

        if ($checkOnce && strpos($fp[$idx], "switch") === FALSE)

           continue;

        

        $checkOnce = false;

        if (! $found && (strpos($fp[$idx], $page_to_delete) !== FALSE || strpos($fp[$idx], strtoupper($page_to_delete))) !== FALSE)

        {

            $delStart = $idx; // + 1;  //adjust for 0 start

            $found = true;

        }

        else if ($found && ( strpos($fp[$idx], "ALL OTHER PAGES NOT DEFINED ABOVE") === FALSE && strpos($fp[$idx], ".php") === FALSE))

        {

           $delStop++;

        }   

        else if ($found && (strpos($fp[$idx], "ALL OTHER PAGES NOT DEFINED ABOVE") !== FALSE  || strpos($fp[$idx], ".php") !== FALSE))

        {

           $delStop++; 

           break;

        }                  

     }     

     

     if ($found == true)          //page entry may not be present

     {

        if (in_array($page_to_delete, $nodelete_pages))     

        {

          $error = sprintf(HEADING_TITLE_CONTROLLER_NO_DELETE_ERROR, $page_to_delete);

          $messageStack->add($error);

        }

        else

        {

          array_splice($fp, $delStart, $delStop);

          WriteHeaderTagsFile($filenameInc, $fp);

        }  

     }   

  }

  else if (tep_not_null($actionCheck)) 

  {

     $filelist = array();

     $newfiles = array();

     $fp = file($filenameEng);

  

     for ($idx = 0; $idx < count($fp); ++$idx) 

     {

        $section = GetSectionName($fp[$idx]);

        if (empty($section) || strpos($section, "header_tags") !== FALSE)

           continue;

        $section .= '.php';

        $section = str_replace("-", "_", $section);  //ensure the scoring is the same

        $filelist[] = $section;

     }

 

     if ($handle = opendir(DIR_FS_CATALOG)) 

     {

        $fp = file($filenameEng); 

        $found = false;

        while (false !== ($file = readdir($handle))) 

        { 

           if (strpos($file, '.php') === FALSE)

              continue;       

 

           if (FileNotUsingHeaderTags($file))

           {

              foreach($filelist as $name) 

              {           

                 $tmp_file = str_replace("-", "_", $file);  //ensure the scoring is the same

                 if (strcasecmp($name, $tmp_file) === 0)

                 {

                    $found = true;

                    break;

                 }

              }   

              if (! $found)

                 $newfiles[] = array('id' => $file, 'text' => $file);

              else

                 $found = false;

           }

        }

        closedir($handle); 

     }

  }

  

  $deleteArray = array();

  $fp = file($filenameEng);

  $checkOnce = true;

  for ($idx = 0; $idx < count($fp); ++$idx)

  {

     if ($checkOnce && strpos($fp[$idx], "// DEFINE TAGS FOR INDIVIDUAL PAGES") === FALSE)

        continue;

     $checkOnce = false;

     $l = GetSectionName($fp[$idx]);

     if (tep_not_null($l))

       $deleteArray[] = array('id' => $l, 'text' => $l);

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

            <h3><?php echo HEADING_TITLE_CONTROLLER; ?>

               <br>

            </h3>

            <!-- START panel-->

            <div class="panel panel-default">

               <div class="panel-heading"><?php echo HEADING_TITLE_CONTROLLER; ?>

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

          <td class="HTC_subHead"><?php echo TEXT_PAGE_TAGS; ?></td>

        </tr>

        <tr>

          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

        </tr>

      <!-- Begin of Header Tags - Add a Page -->

        <tr>

          <td><?php //echo tep_black_line(); ?></td>

        </tr>

        <tr>

          <td><?php echo TEXT_INFORMATION_ADD_PAGE; ?></td>

        </tr>

       <tr>

         <td align="right"><?php echo tep_draw_form('header_tags', FILENAME_HEADER_TAGS_CONTROLLER, '', 'post') . tep_draw_hidden_field('action', 'process'); ?></td>

       </tr>

       <tr>

         <td>

           <table class="table table-bordered table-hover">

             <tr>

               <td>

                 <table class="table table-bordered table-hover">

                   <tr>

                     <td><?php echo HEADING_TITLE_CONTROLLER_PAGENAME; ?></td>

                     <td><?php echo tep_draw_input_field('page', tep_not_null($page) ? $page : '', 'maxlength="255", size="30"', false); ?> </td>

                   <tr>             

                 </table>

               </td>

             </tr>

             <tr>

               <td>

                 <table class="table table-bordered table-hover">

                   <tr>

                     <td>Switches:</td>

                     <td>HTTA: </td>

                     <td><?php echo tep_draw_checkbox_field('htta', '', FALSE, ''); ?> </td>

                     <td>HTDA: </td>

                     <td ><?php echo tep_draw_checkbox_field('htda', '', FALSE, ''); ?> </td>

                     <td>HTKA: </td>

                     <td ><?php echo tep_draw_checkbox_field('htka', '', FALSE, ''); ?> </td>

                     <td>HTCA: </td>

                     <td><?php echo tep_draw_checkbox_field('htca', '', FALSE, ''); ?> </td>

                     <td> <script>document.writeln('<a style="cursor:hand" onclick="javascript:popup=window.open('

                                           + '\'<?php echo tep_href_link('header_tags_popup_help.php'); ?>\',\'popup\','

                                           + '\'scrollbars,resizable,width=520,height=550,left=50,top=50\'); popup.focus(); return false;">'

                                           + '<font color="red"><u><?php echo HEADING_TITLE_CONTROLLER_EXPLAIN; ?></u></font></a>');

                     </script>

                     </td>

                   </tr>

                 </table>

               </td>

             </tr>

             <tr>

               <td>

                 <table border="0" width="100%">

                   <tr>

                     <td><?php echo HEADING_TITLE_CONTROLLER_TITLE; ?></td>

                     <td><?php echo tep_draw_input_field('title', tep_not_null($title) ? $title : '', 'maxlength="255", size="60"', false); ?> </td>

                   </tr> 

                   <tr>

                     <td><?php echo HEADING_TITLE_CONTROLLER_DESCRIPTION; ?></td>

                     <td><?php echo tep_draw_input_field('desc', tep_not_null($desc) ? $desc : '', 'maxlength="255", size="60"', false); ?> </td>

                   </tr> 

                   <tr>

                     <td><?php echo HEADING_TITLE_CONTROLLER_KEYWORDS; ?></td>

                     <td><?php echo tep_draw_input_field('keyword', tep_not_null($key) ? $key : '', 'maxlength="255", size="60"', false); ?> </td>

                   </tr>

                 </table>

               </td>

             </tr>

             <tr> 

               <td align="center"><?php echo (tep_image_submit('button_update.gif', IMAGE_UPDATE) ) . ' <a href="' . tep_href_link(FILENAME_HEADER_TAGS_CONTROLLER, '') .'">' . '</a>'; ?></td>

             </tr>

             <tr>

               <td><?php //echo tep_black_line(); ?></td>

             </tr>

           </form>

           </td>

         </tr>

     <!-- end of Header Tags - Add a Page-->

  		  

     <!-- Begin of Header Tags - Delete a Page -->

         <tr>

           <td><?php //echo tep_black_line(); ?></td>

         </tr>

         <tr>

           <td><?php echo TEXT_INFORMATION_DELETE_PAGE; ?></td>

         </tr>     

         <tr>

           <td align="right"><?php echo tep_draw_form('header_tags_delete', FILENAME_HEADER_TAGS_CONTROLLER, '', 'post') . tep_draw_hidden_field('action_delete', 'process'); ?></td>

         </tr>

         <tr>

           <td>

             <table class="table table-bordered table-hover">

               <tr>

                 <td>

                   <table class="table table-bordered table-hover">

                     <tr>

                       <td><?php echo HEADING_TITLE_CONTROLLER_PAGENAME; ?></td>

                       <td align="left"><?php   echo tep_draw_pull_down_menu('delete_page', $deleteArray, '', '', false);?></td>

                     </tr>             

                   </table>

                 </td>

               </tr>        

               <tr> 

                 <td align="center"><?php echo (tep_image_submit('button_update.gif', IMAGE_UPDATE) ) . ' <a href="' . tep_href_link(FILENAME_HEADER_TAGS_CONTROLLER, '') .'">' . '</a>'; ?></td>

               </tr>       

               <tr>

                 <td><?php //echo tep_black_line(); ?></td>

               </tr>      

             </form>

             </td>

           </tr>

      <!-- end of Header Tags - Delete a Page-->    

     <!-- Begin of Header Tags - Auto Add Pages -->

           <tr>

             <td><?php //echo tep_black_line(); ?></td>

           </tr>

           <tr>

             <td><?php echo TEXT_INFORMATION_CHECK_PAGES; ?></td>

           </tr>     

           <tr>

             <td align="right"><?php echo tep_draw_form('header_tags_auto', FILENAME_HEADER_TAGS_CONTROLLER, '', 'post') . tep_draw_hidden_field('action_check', 'process'); ?></td>

           </tr>

           <tr>

             <td><table class="table table-bordered table-hover">

           </tr>

           <tr>

           <td>

             <table class="table table-bordered table-hover">

               <tr>

                 <td><?php echo HEADING_TITLE_CONTROLLER_PAGENAME; ?></td>

                 <td align="left"><?php   echo tep_draw_pull_down_menu('new_files', $newfiles, '', '', false);?></td>

               </tr>

             </table>

           </td>

         </tr>            

         <tr> 

           <td align="center"><?php echo (tep_image_submit('button_update.gif', IMAGE_UPDATE) ) . ' <a href="' . tep_href_link(FILENAME_HEADER_TAGS_CONTROLLER, '') .'">' . '</a>'; ?></td>

         </tr>       

         <tr>

           <td><?php //echo tep_black_line(); ?></td>

         </tr>      

       </form>

       </td>

     </tr>

     <!-- end of Header Tags - Auto Add Pages-->

   </table>

 </td>

<!-- body_text_eof //-->

</tr>

</table>

</table>

</table>

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