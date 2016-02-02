<?php
/*
$Id: qbi_page_top.php,v 2.10 2005/05/08 al Exp $

Quickbooks Import QBI
contribution for osCommerce
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

require_once(DIR_WS_FUNCTIONS . 'qbi_functions.php');
require_once(DIR_WS_CLASSES . 'qbi_classes.php');
?>
<body>
<link rel="stylesheet" type="text/css" href="includes/qbi_styles.css">
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
               
               <!-- START table-responsive-->
               
               <div class="table-responsive">
               <!-- START your table-->
<!-- body //-->
<table class="table table-bordered table-hover">
  <tr>
<!-- body_text //-->
    <td>
	<table class="table table-bordered table-hover">
      <tr>
        <td>
		<table class="table table-bordered table-hover">
          <tr>
            <td><?php echo HEADING_TITLE; ?></td>
            <td align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
	</table>
<?php
$pageurl=$PHP_SELF;
?>               <!-- END your table-->
<!-- body_eof //-->

