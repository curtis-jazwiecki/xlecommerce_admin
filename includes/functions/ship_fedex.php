<?php
/*
  $Id: general.php,v 1.1.1.1 2003/09/18 19:05:10 wilt Exp $

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
  
  function name_case($name){
   $newname = strtoupper($name[0]);   
   for ($i=1; $i < strlen($name); $i++)
   {
       $subed = substr($name, $i, 1);   
       if (((ord($subed) > 64) && (ord($subed) < 123)) ||
           ((ord($subed) > 48) && (ord($subed) < 58)))
       {
           $word_check = substr($name, $i - 2, 2);
           if (!strcasecmp($word_check, 'Mc') || !strcasecmp($word_check, "O'"))
           {
               $newname .= strtoupper($subed); 
           }
           else if ($break)
           {
              
               $newname .= strtoupper($subed);
           }
           else     
           {
               $newname .= strtolower($subed);
           }
             $break=0;
       }
       else
       {
           // not a letter - a boundary
             $newname .= $subed;
           $break=1;
       }
   }   
   return $newname;
}  

?>