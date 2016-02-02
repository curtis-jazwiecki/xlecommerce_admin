<?php
/*
  $Id: create_order.php,v 1 2003/08/17 23:21:34 frankl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/




//http://localhost:8888/catalog/admin/create_order.php?Customer=2



  require('includes/application_top.php');

  // #### Get Available Customers

  $query = tep_db_query("select a.customers_id, a.customers_firstname, a.customers_lastname, b.entry_company, b.entry_city, c.zone_code from " . TABLE_CUSTOMERS . " AS a, " . TABLE_ADDRESS_BOOK . " AS b LEFT JOIN " . TABLE_ZONES . " as c ON (b.entry_zone_id = c.zone_id) WHERE a.customers_default_address_id = b.address_book_id  ORDER BY entry_company,customers_lastname");
  $result = $query;



  if (tep_db_num_rows($result) > 0){
    // Query Successful
    $SelectCustomerBox = "<select name='Customer'><option value=''>" . TEXT_SELECT_CUST . "</option>\n";

    while($db_Row = tep_db_fetch_array($result)){ 

      $SelectCustomerBox .= "<option value='" . $db_Row['customers_id'] . "'";

      if(isSet($HTTP_GET_VARS['Customer']) and $db_Row['customers_id']==$HTTP_GET_VARS['Customer']){

        $SelectCustomerBox .= " SELECTED ";
        $SelectCustomerBox .= ">" . (empty($db_Row['entry_company']) ? "": strtoupper($db_Row['entry_company']) . " - " ) . $db_Row['customers_lastname'] . " , " . $db_Row['customers_firstname'] . " - " . $db_Row['entry_city'] . ", " . $db_Row['zone_code'] . "</option>\n";
      }else{

        $SelectCustomerBox .= ">" . (empty($db_Row['entry_company']) ? "": strtoupper($db_Row['entry_company']) . " - " ) . $db_Row['customers_lastname'] . " , " . $db_Row['customers_firstname'] . " - " . $db_Row['entry_city'] . ", " . $db_Row['zone_code'] . "</option>\n";


      }
    }

    $SelectCustomerBox .= "</select>\n";

          

	$query = tep_db_query("select code, value from " . TABLE_CURRENCIES . " ORDER BY code");
	$result = $query;
	
	if (tep_db_num_rows($result) > 0){
	  // Query Successful
	  $SelectCurrencyBox = "<select name='Currency'><option value=''>" . TEXT_SELECT_CURRENCY . "</option>\n";
	  while($db_Row = tep_db_fetch_array($result)){ 
	    $SelectCurrencyBox .= "<option value='" . $db_Row["code"] . " , " . $db_Row["value"] . "'";

	    if ($db_Row["code"] == DEFAULT_CURRENCY){

	      $SelectCurrencyBox .= " SELECTED ";

	    }

	    $SelectCurrencyBox .= ">" . $db_Row["code"] . "</option>\n";
	  }
	  $SelectCurrencyBox .= "</select>\n";
	}

    

	if(isSet($HTTP_GET_VARS['Customer'])){
 	  $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = '" . $HTTP_GET_VARS['Customer'] . "'");
 	  $account = tep_db_fetch_array($account_query);
 	  $customer = $account['customers_id'];
 	  $address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $HTTP_GET_VARS['Customer'] . "'");
 	  $address = tep_db_fetch_array($address_query);
	}elseif (isSet($HTTP_GET_VARS['Customer_nr'])){
 	  $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = '" . $HTTP_GET_VARS['Customer_nr'] . "'");
 	  $account = tep_db_fetch_array($account_query);
 	  $customer = $account['customers_id'];
 	  $address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $HTTP_GET_VARS['Customer_nr'] . "'");
 	  $address = tep_db_fetch_array($address_query);
	}

      

    require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CREATE_ORDER_PROCESS);

  // #### Generate Page
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
        <td><b><?php echo TEXT_STEP_1 ?></b></td>
      </tr>
    </table>
    <table class="table table-bordered table-hover"><tr><td>

      <?php
	    print "<form action='$PHP_SELF' method='GET'>\n";
	    print "<table border='0'>\n";
	    print "<tr>\n";
	    print "<td><br>$SelectCustomerBox</td>\n";
	    print "<td valign='bottom'><input type='submit' value=\"" . BUTTON_SUBMIT . "\"></td>\n";
	    print "</tr>\n";
	    print "</table>\n";
	    print "</form>\n";


  

	    print "<form action='$PHP_SELF' method='GET'>\n";
	    print "<table border='0'>\n";
	    print "<tr>\n";
	    print "<td><b><br>" . TEXT_OR_BY . "</b><br><br><input type=text name='Customer_nr'></td>\n";
	    print "<td valign='bottom'><input type='submit' value=\"" . BUTTON_SUBMIT . "\"></td>\n";
	    print "</tr>\n";
	    print "</table>\n";
	    print "</form>\n";
      ?>	
    <tr>
      <td><?php echo tep_draw_form('create_order', FILENAME_CREATE_ORDER_PROCESS, '', 'post', '', '') . tep_draw_hidden_field('customers_id', $account->customers_id); ?><table border="0" width="100%" cellspacing="0" cellpadding="0">
    </tr>

    <tr>
      <td><table class="table table-bordered table-hover">
        <tr>
          <td><?php echo HEADING_CREATE; ?></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
    <tr>
      <td>
        <?php
          //onSubmit="return check_form();"
          require(DIR_WS_MODULES . 'create_order_details.php');
        ?>
      </td>
    </tr>
    <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
    <tr>
      <td><table class="table table-bordered table-hover">
        <tr>
          <td><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'SSL') . '">' . tep_image_button('button_back_b.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
          <td align="right"><?php echo tep_image_submit('button_confirm_b.gif', IMAGE_BUTTON_CONFIRM); ?></td>
        </tr>
      </table></td>
    </tr>
  </table></form></td>
<!-- body_text_eof //-->
  </tr>
</table>
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php');}?>