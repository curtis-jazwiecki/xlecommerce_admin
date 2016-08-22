<?php
/*
$Id: qbi_discount.php,v 2.10 2005/05/08 al Exp $

CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.


ver 2.10 May 8, 2005
(c) 2005 Adam Liberman
www.libermansound.com
info@libermansound.com
Please use the osC forum for support.
Released under the GNU General Public License

    This file is part of Quickbooks Import QBI.

    Quickbooks Import QBI is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Quickbooks Import QBI is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Quickbooks Import QBI; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

require('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/qbi_general.php');
require(DIR_WS_INCLUDES . 'qbi_version.php');
require(DIR_WS_INCLUDES . 'qbi_definitions.php');
require(DIR_WS_INCLUDES . 'qbi_page_top.php');
require(DIR_WS_INCLUDES . 'qbi_menu_tabs.php');

$filenames=array("qbi_input/items.iif","qbi_input/items.IIF","qbi_input/lists.iif","qbi_input/lists.IIF");
if (!isset($stage)) { 
  foreach($filenames as $filename) {
    if (file_exists($filename)) {
      $filefound=1;
	  break;
    }
  }
  if ($filefound==1) { ?>
  <table class="lists" bgcolor="#FFFFFF" width="100%"><tr><td>
    <form action="<?php echo $_SERVER[PHP_SELF] ?>" method="post" name="additems">
    <input name="file_name" type="hidden" value="<?php echo $filename ?>" />	
    <input name="stage" type="hidden" value="processfile" /> <?php
    echo SETUP_FILE_FOUND1." $filename".SETUP_FILE_FOUND2; ?>
    <input name="submitfile" type="submit" id="submitfile" value="<?php echo SETUP_FILE_BUTTON ?>" />
    </form></td></tr></table><br /><br /> <?php
  } else {
    echo SETUP_FILE_MISSING;
  }
  disc_list();
} elseif (isset($stage) AND $stage=="processfile") {

// Open, read, and parse iif to import QB items
  $handle = fopen($file_name, "rb");
  unset($iif_refnum);
  echo '<table class="lists" bgcolor="#FFFFFF" width="100%">';
  while (($iifread=fgetcsv($handle, 512, "\t"))!==FALSE) {
    if ($iifread[0]=="!INVITEM") {
      $iifheader=$iifread;
      echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
      echo '<tr><th class="colhead">'.SETUP_NAME.'</th><th class="colhead">'.SETUP_DESC.'</th><th class="colhead">'.SETUP_ACCT.'</th><th class="colhead">'.SETUP_ACTION."</th></tr>";
    } elseif ($iifread[0]=="INVITEM") {
      $iifdetail=$iifread;
      $iifitem=arraycombine($iifheader,$iifdetail);
      if ($iifitem["INVITEMTYPE"]=="OTHC" OR $iifitem["INVITEMTYPE"]=="DISC") {
        $iif_refnum[]=$iifitem["REFNUM"];
        disc_process($iifitem["NAME"],$iifitem["REFNUM"],$iifitem["DESC"],$iifitem["ACCNT"],$iifitem["PRICE"],$iifitem["INVITEMTYPE"],$iifitem["TAXABLE"]);
      }
    }
  }
  if (isset($iif_refnum) AND count($iif_refnum)>=1) {
    disc_delete($iif_refnum);
	echo SETUP_SUCCESS."<br />";
  } else {
	echo SETUP_FAIL."<br />";
  }
  echo "</table>";
  fclose($handle);
}
require(DIR_WS_INCLUDES . 'qbi_page_bot.php');
?>