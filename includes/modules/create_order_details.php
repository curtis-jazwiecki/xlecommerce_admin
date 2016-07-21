<?php
/*
  $Id: create_order_details.php,v 1.2 2005/09/04 04:42:56 loic Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

 tep_draw_hidden_field($account['customers_id']);    
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="formAreaTitle"><?php echo CATEGORY_CORRECT; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_CUSTOMERS_ID; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('customers_id', $account['customers_id']) . '&nbsp;' . ENTRY_CUSTOMERS_ID_TEXT; ?> </td>
          </tr>
		  <tr>
            <td class="main">&nbsp;<?php echo ENTRY_FIRST_NAME; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('firstname', $account['customers_firstname']) . '&nbsp;' . ENTRY_FIRST_NAME_TEXT; ?> </td>
          </tr>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_LAST_NAME; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('lastname', $account['customers_lastname']) . '&nbsp;' . ENTRY_LAST_NAME_TEXT; ?> </td>
          </tr>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_EMAIL_ADDRESS; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('email_address', $account['customers_email_address']) . '&nbsp;' . ENTRY_EMAIL_ADDRESS_TEXT; ?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  
<?php if (ACCOUNT_COMPANY == 'true') { ?>  
  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_COMPANY; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_COMPANY; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('company', $address['entry_company']) . '&nbsp;' . ENTRY_COMPANY_TEXT;?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
<?php } ?>
  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_ADDRESS; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_STREET_ADDRESS; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('street_address', $address['entry_street_address']) . '&nbsp;' . ENTRY_STREET_ADDRESS_TEXT; ?></td>
          </tr>
        <?php if (ACCOUNT_SUBURB == 'true') { ?>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_SUBURB; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('suburb', $address['entry_suburb']) . '&nbsp;' . ENTRY_SUBURB_TEXT; ?></td>
          </tr>
        <?php } ?>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_POST_CODE; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('postcode', $address['entry_postcode']) . '&nbsp;' . ENTRY_POST_CODE_TEXT; ?></td>
          </tr>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_CITY; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('city', $address['entry_city']) . '&nbsp;' . ENTRY_CITY_TEXT;?></td>
          </tr>
        <?php if (ACCOUNT_STATE == 'true') { ?>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_STATE; ?></td>
            <td class="main">
            <?php
              $zone_query = tep_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . $address['entry_country_id'] . "' and zone_id = '" . $address['entry_zone_id'] . "'");
              if (tep_db_num_rows($zone_query)) {
                $zone = tep_db_fetch_array($zone_query);
                $state = $zone['zone_name'];
              }else{
                $state = $default_zone;
              }
              echo tep_draw_input_field('state', $state) . '&nbsp;' . ENTRY_STATE_TEXT;
            ?>
            </td>
          </tr>
        <?php } ?>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_COUNTRY; ?></td>
            <td class="main">
              <?php
                if ($address['entry_country_id']){
                  echo tep_draw_pull_down_menu('country', tep_get_countries(), $address['entry_country_id']);
                }else{
                  echo tep_draw_pull_down_menu('country', tep_get_countries(), STORE_COUNTRY);
                }
                tep_draw_hidden_field('step', '3');
              ?>
            </td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_CONTACT; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('telephone', $account['customers_telephone']) . '&nbsp;' . ENTRY_TELEPHONE_NUMBER_TEXT; ?> </td>
          </tr>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_FAX_NUMBER; ?></td>
            <td class="main"> <?php echo tep_draw_input_field('fax', $account['customers_fax']) . '&nbsp;' . ENTRY_FAX_NUMBER_TEXT; ?></td>
          </tr>
        </table></td>
      </tr>
	  </table>
      <tr>
        <td class="formAreaTitle"><br> <?php echo TEXT_SELECT_CURRENCY; ?></td>
      </tr>
      <tr>
        <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
            <tr>
              <td class="main"><table border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main">&nbsp;<?php echo ENTRY_CURRENCY; ?></td>
                    <td class="main"><?php echo $SelectCurrencyBox ?></td>
                  </tr>
                </table></td>
            </tr>
          </table></td>
      </tr>
      <tr>
        <td class="formAreaTitle"><br> <?php echo TEXT_CS; ?></td>
      </tr>
      <tr>
        <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
            <tr>
              <td class="main"><table border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main">&nbsp;<?php echo ENTRY_ADMIN; ?></td>
                    <?php 
 					  $my_account_query = tep_db_query ("select a.admin_id, a.admin_firstname, a.admin_lastname, a.admin_email_address, a.admin_created, a.admin_modified, a.admin_logdate, a.admin_lognum, g.admin_groups_name from " . TABLE_ADMIN . " a, " . TABLE_ADMIN_GROUPS . " g where a.admin_id= " . $login_id . " and g.admin_groups_id= " . $login_groups_id . "");
					  $myAccount = tep_db_fetch_array($my_account_query);
                    ?>
                    <td class="main">&nbsp;<input type="text" value="<?php echo $myAccount['admin_firstname']; ?>" name="cust_service" readonly="readonly" />
                    <?php // OLD echo tep_draw_input_field('cust_service', $cs_id); ?>
					<br />
<?php //			<input type="text" name="cust_service" value="<?php echo $cs_id" /> ?>
                    </td>
                  </tr>
                  <tr>
                    <td class="main">&nbsp;Phone Order:</td>
                    <td class="main"><div style='float: left;'>&nbsp;<input type="checkbox" id="is_phone_order" name="is_phone_order" /></div><div style='margin: 2px 0 0 0;color:#666; font-family:Verdana,Arial,sans-serif; font-size:11px; font-weight:normal; text-decoration:none; float: left;'> - (leave blank if 'In-Store' order)</div></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>