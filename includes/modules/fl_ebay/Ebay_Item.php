<?php
/*
 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require_once( 'includes/modules/fl_ebay/Ebay_Defines.php' );
require_once( 'includes/modules/fl_ebay/Ebay_ShippingServiceOption.php' );
require_once( 'includes/modules/fl_ebay/Ebay_Seller.php' );
require_once( 'includes/modules/fl_ebay/Ebay_ApiCaller.php' );
require_once( 'includes/modules/fl_ebay/Ebay_Session.php' );
require_once( 'includes/modules/fl_ebay/Ebay_Buyer.php' );
require_once( 'includes/modules/fl_ebay/Ebay_Result.php' );
require_once( 'includes/modules/fl_ebay/Ebay_AttributeSet.php' );

/**
 * done, v1.0.0.1
 * 
 * @see Ebay_SecondChanceItem
 */
class Ebay_Item
{ 
  // this array holds all attribute data of the object
  var $_props = array();
  /**
   * sets a property by name and value
   */
  function _setProp( $key, $value )
  {
    $this->_props[$key] = $value;
  } 

  /**
   * gets a property by name
   */
  function _getProp( $key )
  {
    return $this->_props[$key];
  } 

  /**
   * Read accessor of ApplicationData. 
   * 32 chars long value to be associated with the item. Will posted and retrieved to and from eBay.
   * 
   * @access public 
   * @return string Value of the ApplicationData property
   */
  function getApplicationData()
  {
    return $this->_props['ApplicationData'];
  } 

  /**
   * Write accessor of ApplicationData.  
   * 32 chars long value to be associated with the item. Will posted and retrieved to and from eBay.
   * 
   * @access public 
   * @param string $value The new value for the ApplicationData property
   * @return void 
   */
  function setApplicationData( $value )
  {
    $this->_props['ApplicationData'] = $value;
  } 

  /**
   * Read accessor to the AttributeSet.
   * Contains a set of Attribute nodes (Item Specifics). Currently, an AddItem request can contain a maximum of 2 AttributeSet nodes?one for each category in which the item is listed, if Category and Category2 are mapped to different characteristics sets. See Using AddItem with Item Specifics (Attributes). Required if attributes are identified as required in the characteristics set meta-data. See GetAttributesCS Return Values. 
   * id (integer): Identifier for the attributes set. Unique across all eBay sites. See IDs Needed for Listing Items. 
   * version (integer): Optional. Version number of the attribute set you're using. Unique across all eBay sites. If you specify the version number of the AttributeSet's corresponding characteristics set that you have stored locally, eBay will compare it to the current version on the site and return a warning if the versions do not match. If an error occurs due to invalid attribute data, this warning can be useful to help determine if you might be sending outdated data. 
   * 
   * As using the eBay Accelerator Toolkit for PHP you have to pass in Object of Type Ebay_AttributeSet as index 0 for the 1st category and as index 1 for the 2nd category. You might set the Version within the AttributeSet as returned from the CharactericsSet.
   * 
   * @access public 
   * @param integer $index The index of the value to return
   * @return Ebay _AttributeSet Value of the AttributeSet property
   */
  function getAttributeSet( $index )
  {
    return $this->_props['AttributeSet'][$index];
  } 

  /**
   * Return the amount of AttributeSet actually declared
   * 
   * @access public 
   * @return Ebay _AttributeSet Value of the AttributeSet property
   */
  function getAttributeSetCount()
  {
    return count( $this->_props['AttributeSet'] );
  } 
  /**
   * Returns a copy of the AttributeSet array
   * 
   * @access public 
   * @return array of Ebay_AttributeSet
   */
  function getAttributeSetArray()
  {
    return $this->_props['AttributeSet'];
  } 

  /**
   * Write accessor to the AttributeSet.
   * Contains a set of Attribute nodes (Item Specifics). Currently, an AddItem request can contain a maximum of 2 AttributeSet nodes?one for each category in which the item is listed, if Category and Category2 are mapped to different characteristics sets. See Using AddItem with Item Specifics (Attributes). Required if attributes are identified as required in the characteristics set meta-data. See GetAttributesCS Return Values. 
   * id (integer): Identifier for the attributes set. Unique across all eBay sites. See IDs Needed for Listing Items. 
   * version (integer): Optional. Version number of the attribute set you're using. Unique across all eBay sites. If you specify the version number of the AttributeSet's corresponding characteristics set that you have stored locally, eBay will compare it to the current version on the site and return a warning if the versions do not match. If an error occurs due to invalid attribute data, this warning can be useful to help determine if you might be sending outdated data. 
   * 
   * As using the eBay Accelerator Toolkit for PHP you have to pass in Object of Type Ebay_AttributeSet as index 0 for the 1st category and as index 1 for the 2nd category. You might set the Version within the AttributeSet as returned from the CharactericsSet.
   * 
   * @access public 
   * @param Ebay $ _AttributeSet $value The new value for the AttributeSet property
   * @param integer $index The index of the value to update. if $index = -1, the value is added to the end of list.
   * @return void 
   */
  function setAttributeSet( $value, $index = -1 )
  {
    if ( -1 == $index )
    {
      $index = count( $this->_props['AttributeSet'] );
    } 
    $this->_props['AttributeSet'][$index] = $value;
  } 

  /**
   * Read accessor of BidCount. 
   * Number of bids placed so far against the item. Returned as null for International Fixed Price items.
   * 
   * @access public 
   * @return number Value of the BidCount property
   */
  function getBidCount()
  {
    return $this->_props['BidCount'];
  } 

  /**
   * Read accessor of BidIncrement. 
   * Smallest amount a bid must be above the current high bid. Only applicable to auction-type listings; returns zero for all fixed-price type and Ad-type listings (ItemProperties.Type returns a 6, 7, or 9). The amount of the next bid placed on the item must be at least CurrentPrice plus BidIncrement.
   * 
   * @access public 
   * @return money Value of the BidIncrement property
   */
  function getBidIncrement()
  {
    return $this->_props['BidIncrement'];
  } 

  /**
   * Read accessor of BuyItNowPrice. 
   * Amount a Buyer would need to bid to take advantage of the Buy It Now feature. Not applicable to Fixed-Price or Ad-type items (ItemType returns a 6, 7, or 9). For Fixed-Price items, see StartPrice instead.
   * 
   * @access public 
   * @return money Value of the BuyItNowPrice property
   */
  function getBuyItNowPrice()
  {
    return $this->_props['BuyItNowPrice'];
  } 

  /**
   * Write accessor of BuyItNowPrice.  
   * Amount a Buyer would need to bid to take advantage of the Buy It Now feature. Not applicable to Fixed-Price or Ad-type items (ItemType returns a 6, 7, or 9). For Fixed-Price items, see StartPrice instead.
   * 
   * @access public 
   * @param money $value The new value for the BuyItNowPrice property
   * @return void 
   */
  function setBuyItNowPrice( $value )
  {
    $this->_props['BuyItNowPrice'] = $value;
  } 

  /**
   * Read accessor of BuyerProtection. 
   * Indicates the status of the item's eligibility for the Buyer Protection Program. Possible values:
   * 0 = Item is ineligible (e.g., category not applicable)
   * 1 = Item is eligible per standard criteria 
   * 2 = Item marked ineligible per special criteria (e.g., seller's account closed)
   * 3 = Item marked elegible per other criteria
   * Applicable for items listed to the US site and for the Parts & Accessories category (6028) or Everything Else category (10368) (or their subcategories) on the eBay Motors site.
   * 
   * @access public 
   * @return number Value of the BuyerProtection property
   */
  function getBuyerProtection()
  {
    return $this->_props['BuyerProtection'];
  } 

  /**
   * Read accessor of Category2Id.
   * 
   * @access public 
   * @return number Value of the Category2Id property
   */
  function getCategory2Id()
  {
    return $this->_props['Category2Id'];
  } 

  /**
   * Write accessor of Category2Id.
   * 
   * @access public 
   * @param number $value The new value for the Category2Id property
   * @return void 
   */
  function setCategory2Id( $value )
  {
    $this->_props['Category2Id'] = $value;
  } 

  /**
   * Read accessor of CategoryId.
   * 
   * @access public 
   * @return number Value of the CategoryId property
   */
  function getCategoryId()
  {
    return $this->_props['CategoryId'];
  } 

  /**
   * Write accessor of CategoryId.
   * 
   * @access public 
   * @param number $value The new value for the CategoryId property
   * @return void 
   */
  function setCategoryId( $value )
  {
    $this->_props['CategoryId'] = $value;
  } 

  /**
   * Read accessor of CheckoutAdditionalShippingCosts.
   * 
   * @access public 
   * @return money Value of the CheckoutAdditionalShippingCosts property
   */
  function getCheckoutAdditionalShippingCosts()
  {
    return $this->_props['CheckoutAdditionalShippingCosts'];
  } 

  /**
   * Read accessor of CheckoutBuyerEditTotal.
   * 
   * @access public 
   * @return boolean Value of the CheckoutBuyerEditTotal property
   */
  function getCheckoutBuyerEditTotal()
  {
    return $this->_props['CheckoutBuyerEditTotal'];
  } 

  /**
   * Read accessor of CheckoutConvertedAmountPaid.
   * 
   * @access public 
   * @return money Value of the CheckoutConvertedAmountPaid property
   */
  function getCheckoutConvertedAmountPaid()
  {
    return $this->_props['CheckoutConvertedAmountPaid'];
  } 

  /**
   * Read accessor of CheckoutConvertedTransactionPrice.
   * 
   * @access public 
   * @return money Value of the CheckoutConvertedTransactionPrice property
   */
  function getCheckoutConvertedTransactionPrice()
  {
    return $this->_props['CheckoutConvertedTransactionPrice'];
  } 

  /**
   * Read accessor of CheckoutInstructions.
   * 
   * @access public 
   * @return string Value of the CheckoutInstructions property
   */
  function getCheckoutInstructions()
  {
    return $this->_props['CheckoutInstructions'];
  } 

  /**
   * Write accessor of CheckoutInstructions.
   * 
   * @access public 
   * @param string $value The new value for the CheckoutInstructions property
   * @return void 
   */
  function setCheckoutInstructions( $value )
  {
    $this->_props['CheckoutInstructions'] = $value;
  } 

  /**
   * Read accessor of CheckoutInsuranceFee.
   * 
   * @access public 
   * @return money Value of the CheckoutInsuranceFee property
   */
  function getCheckoutInsuranceFee()
  {
    return $this->_props['CheckoutInsuranceFee'];
  } 

  /**
   * Write accessor of CheckoutInsuranceFee.
   * 
   * @access public 
   * @param money $value The new value for the CheckoutInsuranceFee property
   * @return void 
   */
  function setCheckoutInsuranceFee( $value )
  {
    $this->_props['CheckoutInsuranceFee'] = $value;
  } 

  /**
   * Read accessor of CheckoutInsuranceOption. 
   * NotOffered 0, Optional 1, Required 2, InShippingHandlingCosts 3
   * 
   * @access public 
   * @return define Value of the CheckoutInsuranceOption property
   */
  function getCheckoutInsuranceOption()
  {
    return $this->_props['CheckoutInsuranceOption'];
  } 

  /**
   * Write accessor of CheckoutInsuranceOption.  
   * NotOffered 0, Optional 1, Required 2, InShippingHandlingCosts 3
   * 
   * @access public 
   * @param define $value The new value for the CheckoutInsuranceOption property
   * @return void 
   */
  function setCheckoutInsuranceOption( $value )
  {
    $this->_props['CheckoutInsuranceOption'] = $value;
  } 

  /**
   * Read accessor of CheckoutInsuranceTotal.
   * 
   * @access public 
   * @return money Value of the CheckoutInsuranceTotal property
   */
  function getCheckoutInsuranceTotal()
  {
    return $this->_props['CheckoutInsuranceTotal'];
  } 

  /**
   * Read accessor of CheckoutInsuranceWanted.
   * 
   * @access public 
   * @return boolean Value of the CheckoutInsuranceWanted property
   */
  function getCheckoutInsuranceWanted()
  {
    return $this->_props['CheckoutInsuranceWanted'];
  } 

  /**
   * Read accessor of CheckoutSalesTaxAmount.
   * 
   * @access public 
   * @return money Value of the CheckoutSalesTaxAmount property
   */
  function getCheckoutSalesTaxAmount()
  {
    return $this->_props['CheckoutSalesTaxAmount'];
  } 

  /**
   * Read accessor of CheckoutSalesTaxPercent.
   * 
   * @access public 
   * @return decimal Value of the CheckoutSalesTaxPercent property
   */
  function getCheckoutSalesTaxPercent()
  {
    return $this->_props['CheckoutSalesTaxPercent'];
  } 

  /**
   * Read accessor of CheckoutSalesTaxState.
   * 
   * @access public 
   * @return string Value of the CheckoutSalesTaxState property
   */
  function getCheckoutSalesTaxState()
  {
    return $this->_props['CheckoutSalesTaxState'];
  } 

  /**
   * Read accessor of CheckoutShippingHandlingCosts.
   * 
   * @access public 
   * @return money Value of the CheckoutShippingHandlingCosts property
   */
  function getCheckoutShippingHandlingCosts()
  {
    return $this->_props['CheckoutShippingHandlingCosts'];
  } 

  /**
   * Read accessor of CheckoutShippingInTax.
   * 
   * @access public 
   * @return boolean Value of the CheckoutShippingInTax property
   */
  function getCheckoutShippingInTax()
  {
    return $this->_props['CheckoutShippingInTax'];
  } 

  /**
   * Read accessor of CheckoutSpecified.
   * 
   * @access public 
   * @return boolean Value of the CheckoutSpecified property
   */
  function getCheckoutSpecified()
  {
    return $this->_props['CheckoutSpecified'];
  } 

  /**
   * Write accessor of CheckoutSpecified.
   * 
   * @access public 
   * @param boolean $value The new value for the CheckoutSpecified property
   * @return void 
   */
  function setCheckoutSpecified( $value )
  {
    $this->_props['CheckoutSpecified'] = $value;
  } 

  /**
   * Read accessor of CheckoutAllowPaymentEdit.
   * 
   * @access public 
   * @return boolean Value of the CheckoutAllowPaymentEdit property
   */
  function getCheckoutAllowPaymentEdit()
  {
    return $this->_props['CheckoutAllowPaymentEdit'];
  } 

  /**
   * Read accessor of CheckoutPackagingHandlingCosts.
   * 
   * @access public 
   * @return money Value of the CheckoutPackagingHandlingCosts property
   */
  function getCheckoutPackagingHandlingCosts()
  {
    return $this->_props['CheckoutPackagingHandlingCosts'];
  } 

  /**
   * Read accessor of CheckoutShipFromZipCode.
   * 
   * @access public 
   * @return string Value of the CheckoutShipFromZipCode property
   */
  function getCheckoutShipFromZipCode()
  {
    return $this->_props['CheckoutShipFromZipCode'];
  } 

  /**
   * Read accessor of CheckoutShippingIrregular.
   * 
   * @access public 
   * @return boolean Value of the CheckoutShippingIrregular property
   */
  function getCheckoutShippingIrregular()
  {
    return $this->_props['CheckoutShippingIrregular'];
  } 

  /**
   * Read accessor of CheckoutShippingPackage. 
   *   0 = None, 
   * 1 = Letter,
   * 2 = Large envelope,
   * 3 = USPS flat rate envelope,
   * 4 = Package/thick envelope,
   * 5 = USPS large package/oversize 1,
   * 6 = Very large package/oversize 2
   * 7 = UPS Letter
   * 
   * @access public 
   * @return define Value of the CheckoutShippingPackage property
   */
  function getCheckoutShippingPackage()
  {
    return $this->_props['CheckoutShippingPackage'];
  } 

  /**
   * Read accessor of CheckoutShippingService. 
   * 3 = UPS Ground
   * 4 = UPS 3rd Day
   * 5 = UPS 2nd Day
   * 6 = UPS Next Day
   * 7 = USPS Priority
   * 8 = USPS Parcel
   * 9 = USPS Media
   * 10 = USPS First Class
   * 
   * @access public 
   * @return define Value of the CheckoutShippingService property
   */
  function getCheckoutShippingService()
  {
    return $this->_props['CheckoutShippingService'];
  } 

  /**
   * Read accessor of CheckoutShippingType. 
   * 1 = Flat shipping rate
   * 2 = Calculated shipping rate
   * 
   * @access public 
   * @return define Value of the CheckoutShippingType property
   */
  function getCheckoutShippingType()
  {
    return $this->_props['CheckoutShippingType'];
  } 

  /**
   * Write accessor of CheckoutShippingType.  
   * 1 = Flat shipping rate
   * 2 = Calculated shipping rate
   * 
   * @access public 
   * @param define $value The new value for the CheckoutShippingType property
   * @return void 
   */
  function setCheckoutShippingType( $value )
  {
    $this->_props['CheckoutShippingType'] = $value;
  } 

  /**
   * Read accessor of CheckoutWeightMajor.
   * 
   * @access public 
   * @return number Value of the CheckoutWeightMajor property
   */
  function getCheckoutWeightMajor()
  {
    return $this->_props['CheckoutWeightMajor'];
  } 

  /**
   * Write accessor of CheckoutWeightMajor.
   * 
   * @access public 
   * @param number $value The new value for the CheckoutWeightMajor property
   * @return void 
   */
  function setCheckoutWeightMajor( $value )
  {
    $this->_props['CheckoutWeightMajor'] = $value;
  } 

  /**
   * Read accessor of CheckoutWeightMinor.
   * 
   * @access public 
   * @return number Value of the CheckoutWeightMinor property
   */
  function getCheckoutWeightMinor()
  {
    return $this->_props['CheckoutWeightMinor'];
  } 

  /**
   * Write accessor of CheckoutWeightMinor.
   * 
   * @access public 
   * @param number $value The new value for the CheckoutWeightMinor property
   * @return void 
   */
  function setCheckoutWeightMinor( $value )
  {
    $this->_props['CheckoutWeightMinor'] = $value;
  } 

  /**
   * Read accessor of CheckoutWeightUnit.
   * 
   * @access public 
   * @return number Value of the CheckoutWeightUnit property
   */
  function getCheckoutWeightUnit()
  {
    return $this->_props['CheckoutWeightUnit'];
  } 

  /**
   * Write accessor of CheckoutWeightUnit.
   * 
   * @access public 
   * @param number $value The new value for the CheckoutWeightUnit property
   * @return void 
   */
  function setCheckoutWeightUnit( $value )
  {
    $this->_props['CheckoutWeightUnit'] = $value;
  } 

  /**
   * Read accessor of ConvertedBuyItNowPrice. 
   * Converted value of the BuyItNowPrice in the currency indicated by SiteCurrency. This value must be refreshed every 24 hours to pick up the current conversion rates.
   * 
   * @access public 
   * @return money Value of the ConvertedBuyItNowPrice property
   */
  function getConvertedBuyItNowPrice()
  {
    return $this->_props['ConvertedBuyItNowPrice'];
  } 

  /**
   * Read accessor of CategoryFullName.
   * 
   * @access public 
   * @return string Value of the CategoryFullName property
   */
  function getCategoryFullName()
  {
    return $this->_props['CategoryFullName'];
  } 

  /**
   * Read accessor of Category2FullName.
   * 
   * @access public 
   * @return string Value of the Category2FullName property
   */
  function getCategory2FullName()
  {
    return $this->_props['Category2FullName'];
  } 

  /**
   * Read accessor of AutoPay. 
   * If true (1), indicates that the seller requested immediate payment for the item. False (0) if immediate payment was not requested. (Does not indicate whether the item is still a candidate for puchase via immediate payment.) Only applicable for items listed on US and UK sites in categories that support immediate payment, when seller has a Premier or Business PayPal account.
   * 
   * @access public 
   * @return boolean Value of the AutoPay property
   */
  function getAutoPay()
  {
    return $this->_props['AutoPay'];
  } 

  /**
   * Write accessor of AutoPay.  
   * If true (1), indicates that the seller requested immediate payment for the item. False (0) if immediate payment was not requested. (Does not indicate whether the item is still a candidate for puchase via immediate payment.) Only applicable for items listed on US and UK sites in categories that support immediate payment, when seller has a Premier or Business PayPal account.
   * 
   * @access public 
   * @param boolean $value The new value for the AutoPay property
   * @return void 
   */
  function setAutoPay( $value )
  {
    $this->_props['AutoPay'] = $value;
  } 

  /**
   * Read accessor of CharityListing. 
   * This field is for future use and should be ignored by applications.
   * 
   * @access public 
   * @return boolean Value of the CharityListing property
   */
  function getCharityListing()
  {
    return $this->_props['CharityListing'];
  } 

  /**
   * Read accessor of CharityName. 
   * This field is for future use and should be ignored by applications.
   * 
   * @access public 
   * @return string Value of the CharityName property
   */
  function getCharityName()
  {
    return $this->_props['CharityName'];
  } 

  /**
   * Read accessor of CharityNumber. 
   * This field is for future use and should be ignored by applications.
   * 
   * @access public 
   * @return number Value of the CharityNumber property
   */
  function getCharityNumber()
  {
    return $this->_props['CharityNumber'];
  } 

  /**
   * Read accessor of CharityDonationPercent. 
   * This field is for future use and should be ignored by applications.
   * 
   * @access public 
   * @return number Value of the CharityDonationPercent property
   */
  function getCharityDonationPercent()
  {
    return $this->_props['CharityDonationPercent'];
  } 

  /**
   * Read accessor of ConvertedPrice. 
   * Converted value of the CurrentPrice field in the currency indicated by SiteCurrency. This value must be refreshed every 24 hours to pick up the current conversion rates.
   * 
   * @access public 
   * @return money Value of the ConvertedPrice property
   */
  function getConvertedPrice()
  {
    return $this->_props['ConvertedPrice'];
  } 

  /**
   * Read accessor of ConvertedStartPrice. 
   * Converted value of the StartPrice field in the currency indicated by SiteCurrency. This value must be refreshed every 24 hours to pick up the current conversion rates.
   * 
   * @access public 
   * @return money Value of the ConvertedStartPrice property
   */
  function getConvertedStartPrice()
  {
    return $this->_props['ConvertedStartPrice'];
  } 

  /**
   * Read accessor of Counter. 
   * Optional hit counter for the item's listing page. Possible values: 0, 1, 2, or 3 (0, 1, or 2 for non-US items).
   * 
   * @access public 
   * @return number Value of the Counter property
   */
  function getCounter()
  {
    return $this->_props['Counter'];
  } 

  /**
   * Write accessor of Counter.  
   * Optional hit counter for the item's listing page. Possible values: 0, 1, 2, or 3 (0, 1, or 2 for non-US items).
   * 
   * @access public 
   * @param number $value The new value for the Counter property
   * @return void 
   */
  function setCounter( $value )
  {
    $this->_props['Counter'] = $value;
  } 

  /**
   * Read accessor of Country. 
   * The Country field is a two-letter abbreviation for the country.
   * 
   * @access public 
   * @return string Value of the Country property
   */
  function getCountry()
  {
    return $this->_props['Country'];
  } 

  /**
   * Write accessor of Country.  
   * The Country field is a two-letter abbreviation for the country.
   * 
   * @access public 
   * @param string $value The new value for the Country property
   * @return void 
   */
  function setCountry( $value )
  {
    $this->_props['Country'] = $value;
  } 

  /**
   * Read accessor of Currency. 
   * Numeric code for the currency used to list the item.
   * 
   * @access public 
   * @return number Value of the Currency property
   */
  function getCurrency()
  {
    return $this->_props['Currency'];
  } 

  /**
   * Write accessor of Currency.  
   * Numeric code for the currency used to list the item.
   * 
   * @access public 
   * @param number $value The new value for the Currency property
   * @return void 
   */
  function setCurrency( $value )
  {
    $this->_props['Currency'] = $value;
  } 

  /**
   * Read accessor of CurrencyId. 
   * Character symbol for the currency used to list the item symbol for the auction.
   * 
   * @access public 
   * @return string Value of the CurrencyId property
   */
  function getCurrencyId()
  {
    return $this->_props['CurrencyId'];
  } 

  /**
   * Read accessor of CurrentPrice. 
   * For auction-type listings (ItemProperties.Type returns a 1, 2, or 5), returns start price (if no bids have been placed yet) or current high bid (if at least one bid has been placed). For all fixed-price type and Ad-type listings (ItemProperties.Type is a 6, 7, or 9), returns the price specified when the item was originally listed/re-listed or the new price after the item was revised.
   * 
   * @access public 
   * @return money Value of the CurrentPrice property
   */
  function getCurrentPrice()
  {
    return $this->_props['CurrentPrice'];
  } 

  /**
   * Read accessor of Description. 
   * Description of item. Returned as CDATA to prevent possible reserved characters in the description from breaking the XML parser.
   * 
   * @access public 
   * @return string Value of the Description property
   */
  function getDescription()
  {
    return $this->_props['Description'];
  } 

  /**
   * Write accessor of Description.  
   * Description of item. Returned as CDATA to prevent possible reserved characters in the description from breaking the XML parser.
   * 
   * @access public 
   * @param string $value The new value for the Description property
   * @return void 
   */
  function setDescription( $value )
  {
    $this->_props['Description'] = $value;
  } 

  /**
   * Read accessor of DescriptionLen. 
   * Length (in characters) of the text in Description.
   * 
   * @access public 
   * @return number Value of the DescriptionLen property
   */
  function getDescriptionLen()
  {
    return $this->_props['DescriptionLen'];
  } 

  /**
   * Read accessor of DomainKey. 
   * Static Key name for the domain that the item is listed in.
   * 
   * @access public 
   * @return string Value of the DomainKey property
   */
  function getDomainKey()
  {
    return $this->_props['DomainKey'];
  } 

  /**
   * Write accessor of DomainKey.  
   * Static Key name for the domain that the item is listed in.
   * 
   * @access public 
   * @param string $value The new value for the DomainKey property
   * @return void 
   */
  function setDomainKey( $value )
  {
    $this->_props['DomainKey'] = $value;
  } 

  /**
   * Read accessor of EndTime. 
   * Time stamp for the end of the listing. Will be converted to local time if the setTimeOffset feature is used in Session.
   * 
   * @access public 
   * @return datetime Value of the EndTime property
   */
  function getEndTime()
  {
    return $this->_props['EndTime'];
  } 

  /**
   * Read accessor of GalleryURL. 
   * URL for the gallery for the item. Returned as CDATA. The data for this field will be no longer than 255 characters.
   * 
   * @access public 
   * @return string Value of the GalleryURL property
   */
  function getGalleryURL()
  {
    return $this->_props['GalleryURL'];
  } 

  /**
   * Write accessor of GalleryURL.  
   * URL for the gallery for the item. Returned as CDATA. The data for this field will be no longer than 255 characters.
   * 
   * @access public 
   * @param string $value The new value for the GalleryURL property
   * @return void 
   */
  function setGalleryURL( $value )
  {
    $this->_props['GalleryURL'] = $value;
  } 

  /**
   * Read accessor of GiftIcon. 
   * If true, a generic gift icon displays in the listing's Title. For Motors items, use the integer values specified in eBay Motors: GiftIcon. You must set GiftIcon to true to be able to set GiftExpressShipping, GiftShipToRecipient, or GiftWrap to "true."
   * 
   * @access public 
   * @return boolean Value of the GiftIcon property
   */
  function getGiftIcon()
  {
    return $this->_props['GiftIcon'];
  } 

  /**
   * Write accessor of GiftIcon.  
   * If true, a generic gift icon displays in the listing's Title. For Motors items, use the integer values specified in eBay Motors: GiftIcon. You must set GiftIcon to true to be able to set GiftExpressShipping, GiftShipToRecipient, or GiftWrap to "true."
   * 
   * @access public 
   * @param boolean $value The new value for the GiftIcon property
   * @return void 
   */
  function setGiftIcon( $value )
  {
    $this->_props['GiftIcon'] = $value;
  } 

  /**
   * Read accessor of GiftExpressShipping. 
   * If true, indicates that the seller is offering to ship the item via an express shipping method as described in the item Description.
   * 
   * @access public 
   * @return boolean Value of the GiftExpressShipping property
   */
  function getGiftExpressShipping()
  {
    return $this->_props['GiftExpressShipping'];
  } 

  /**
   * Write accessor of GiftExpressShipping.  
   * If true, indicates that the seller is offering to ship the item via an express shipping method as described in the item Description.
   * 
   * @access public 
   * @param boolean $value The new value for the GiftExpressShipping property
   * @return void 
   */
  function setGiftExpressShipping( $value )
  {
    $this->_props['GiftExpressShipping'] = $value;
  } 

  /**
   * Read accessor of GiftShipToRecipient. 
   * If true, indicates that the seller is offering to ship to the gift recipient, not the buyer, when payment clears.
   * 
   * @access public 
   * @return boolean Value of the GiftShipToRecipient property
   */
  function getGiftShipToRecipient()
  {
    return $this->_props['GiftShipToRecipient'];
  } 

  /**
   * Write accessor of GiftShipToRecipient.  
   * If true, indicates that the seller is offering to ship to the gift recipient, not the buyer, when payment clears.
   * 
   * @access public 
   * @param boolean $value The new value for the GiftShipToRecipient property
   * @return void 
   */
  function setGiftShipToRecipient( $value )
  {
    $this->_props['GiftShipToRecipient'] = $value;
  } 

  /**
   * Read accessor of GiftWrap. 
   * If true, indicates that the seller is offering to wrap the item (and optionally include a card) as described in the item Description.
   * 
   * @access public 
   * @return boolean Value of the GiftWrap property
   */
  function getGiftWrap()
  {
    return $this->_props['GiftWrap'];
  } 

  /**
   * Write accessor of GiftWrap.  
   * If true, indicates that the seller is offering to wrap the item (and optionally include a card) as described in the item Description.
   * 
   * @access public 
   * @param boolean $value The new value for the GiftWrap property
   * @return void 
   */
  function setGiftWrap( $value )
  {
    $this->_props['GiftWrap'] = $value;
  } 

  /**
   * Read accessor of GoodTillCanceled. 
   * If true, indicates that the store owner listed the item as "GTC". (See eBay Stores Overview.) If the item does not sell within the specified listing period, the item is automatically relisted at the end of the specified period. Applicable for eBay Stores items only. Always false for other items.
   * 
   * @access public 
   * @return boolean Value of the GoodTillCanceled property
   */
  function getGoodTillCanceled()
  {
    return $this->_props['GoodTillCanceled'];
  } 

  /**
   * Read accessor of HighBidder. 
   * Contains one User node representing the current high bidder. GetItem returns a high bidder for auctions that have ended and have a winning bidder. For Fixed Price listings, in-progress auctions, or auction items that received no bids, GetItem returns a HighBidder node with empty tags.
   * 
   * @access public 
   * @return Ebay _User Value of the HighBidder property
   */
  function getHighBidder()
  {
    return $this->_props['HighBidder'];
  } 

  /**
   * Read accessor of Id. 
   * Item Id, you can set the ID but only if you want to use Retrieve afterward. Anyway you will overwrite the acutal ID in memory, so do not use to an Item you already retrieved. When using Add any changes will have an effect.
   * 
   * @access public 
   * @return string Value of the Id property
   */
  function getId()
  {
    return $this->_props['Id'];
  } 

  /**
   * Write accessor of Id.  
   * Item Id, you can set the ID but only if you want to use Retrieve afterward. Anyway you will overwrite the acutal ID in memory, so do not use to an Item you already retrieved. When using Add any changes will have an effect.
   * 
   * @access public 
   * @param string $value The new value for the Id property
   * @return void 
   */
  function setId( $value )
  {
    $this->_props['Id'] = $value;
  } 

  /**
   * Read accessor of PropAdult. 
   * Indicates whether it is an adult-oriented item. Users cannot retrieve information for items listed in Mature categories unless they have accepted the Mature Category agreement on the eBay site. Users do not need to sign this agreement to be able to list items in Mature Catgories.
   * 
   * @access public 
   * @return boolean Value of the PropAdult property
   */
  function getPropAdult()
  {
    return $this->_props['PropAdult'];
  } 

  /**
   * Read accessor of PropBindingAuction. 
   * For Real Estate auctions, indicates whether buyers and sellers are expected to follow through on the transaction. If BindingAuction = 0 (false), then the bids for a Real Estate auction are only a show of interest.
   * 
   * @access public 
   * @return boolean Value of the PropBindingAuction property
   */
  function getPropBindingAuction()
  {
    return $this->_props['PropBindingAuction'];
  } 

  /**
   * Read accessor of PropBuyItNowAdded. 
   * If true, indicates that a Buy It Now Price was added for the item. Only returned for Motors items.
   * 
   * @access public 
   * @return boolean Value of the PropBuyItNowAdded property
   */
  function getPropBuyItNowAdded()
  {
    return $this->_props['PropBuyItNowAdded'];
  } 

  /**
   * Read accessor of PropBuyItNowLowered. 
   * Replaces BinLowered as of API version 305. If true, indicates that the Buy It Now Price was lowered for the item. Only returned for Motors items.
   * 
   * @access public 
   * @return boolean Value of the PropBuyItNowLowered property
   */
  function getPropBuyItNowLowered()
  {
    return $this->_props['PropBuyItNowLowered'];
  } 

  /**
   * Read accessor of PropBoldTitle. 
   * Indicates whether the bolding option was used.
   * 
   * @access public 
   * @return boolean Value of the PropBoldTitle property
   */
  function getPropBoldTitle()
  {
    return $this->_props['PropBoldTitle'];
  } 

  /**
   * Write accessor of PropBoldTitle.  
   * Indicates whether the bolding option was used.
   * 
   * @access public 
   * @param boolean $value The new value for the PropBoldTitle property
   * @return void 
   */
  function setPropBoldTitle( $value )
  {
    $this->_props['PropBoldTitle'] = $value;
  } 

  /**
   * Read accessor of PropCheckoutEnabled. 
   * Indicates whether checkout is enabled for this item. (Checkout is enabled for all fixed items, regardless of the seller's user preferences.)
   * 
   * @access public 
   * @return boolean Value of the PropCheckoutEnabled property
   */
  function getPropCheckoutEnabled()
  {
    return $this->_props['PropCheckoutEnabled'];
  } 

  /**
   * Read accessor of PropFeatured. 
   * Indicates whether it is a featured item.
   * 
   * @access public 
   * @return boolean Value of the PropFeatured property
   */
  function getPropFeatured()
  {
    return $this->_props['PropFeatured'];
  } 

  /**
   * Write accessor of PropFeatured.  
   * Indicates whether it is a featured item.
   * 
   * @access public 
   * @param boolean $value The new value for the PropFeatured property
   * @return void 
   */
  function setPropFeatured( $value )
  {
    $this->_props['PropFeatured'] = $value;
  } 

  /**
   * Read accessor of PropGallery. 
   * Included in the gallery.
   * 
   * @access public 
   * @return boolean Value of the PropGallery property
   */
  function getPropGallery()
  {
    return $this->_props['PropGallery'];
  } 

  /**
   * Write accessor of PropGallery.  
   * Included in the gallery.
   * 
   * @access public 
   * @param boolean $value The new value for the PropGallery property
   * @return void 
   */
  function setPropGallery( $value )
  {
    $this->_props['PropGallery'] = $value;
  } 

  /**
   * Read accessor of PropGalleryFeatured. 
   * Featured in the gallery.
   * 
   * @access public 
   * @return boolean Value of the PropGalleryFeatured property
   */
  function getPropGalleryFeatured()
  {
    return $this->_props['PropGalleryFeatured'];
  } 

  /**
   * Write accessor of PropGalleryFeatured.  
   * Featured in the gallery.
   * 
   * @access public 
   * @param boolean $value The new value for the PropGalleryFeatured property
   * @return void 
   */
  function setPropGalleryFeatured( $value )
  {
    $this->_props['PropGalleryFeatured'] = $value;
  } 

  /**
   * Read accessor of PropHighlight. 
   * If true, item's listing is highlighted.
   * 
   * @access public 
   * @return boolean Value of the PropHighlight property
   */
  function getPropHighlight()
  {
    return $this->_props['PropHighlight'];
  } 

  /**
   * Write accessor of PropHighlight.  
   * If true, item's listing is highlighted.
   * 
   * @access public 
   * @param boolean $value The new value for the PropHighlight property
   * @return void 
   */
  function setPropHighlight( $value )
  {
    $this->_props['PropHighlight'] = $value;
  } 

  /**
   * Read accessor of PropPrivate. 
   * Private auction. Limits participation in auction.
   * 
   * @access public 
   * @return boolean Value of the PropPrivate property
   */
  function getPropPrivate()
  {
    return $this->_props['PropPrivate'];
  } 

  /**
   * Write accessor of PropPrivate.  
   * Private auction. Limits participation in auction.
   * 
   * @access public 
   * @param boolean $value The new value for the PropPrivate property
   * @return void 
   */
  function setPropPrivate( $value )
  {
    $this->_props['PropPrivate'] = $value;
  } 

  /**
   * Read accessor of PropReserve. 
   * If true, indicates that the item has a Reserve Price.
   * 
   * @access public 
   * @return boolean Value of the PropReserve property
   */
  function getPropReserve()
  {
    return $this->_props['PropReserve'];
  } 

  /**
   * Read accessor of PropReserveLowered. 
   * If true, indicates that the Reserve Price was lowered for the item. Only returned for Motors items.
   * 
   * @access public 
   * @return boolean Value of the PropReserveLowered property
   */
  function getPropReserveLowered()
  {
    return $this->_props['PropReserveLowered'];
  } 

  /**
   * Read accessor of PropReserveMet. 
   * Returns true if the reserve price was met or no reserve price was specified.
   * 
   * @access public 
   * @return boolean Value of the PropReserveMet property
   */
  function getPropReserveMet()
  {
    return $this->_props['PropReserveMet'];
  } 

  /**
   * Read accessor of PropReserveRemoved. 
   * If true, indicates that the Reserve Price was removed from the item. Only returned for eBay Motors items.
   * 
   * @access public 
   * @return boolean Value of the PropReserveRemoved property
   */
  function getPropReserveRemoved()
  {
    return $this->_props['PropReserveRemoved'];
  } 

  /**
   * Read accessor of PropSuperFeatured. 
   * Indicates whether it is a super featured item.
   * 
   * @access public 
   * @return boolean Value of the PropSuperFeatured property
   */
  function getPropSuperFeatured()
  {
    return $this->_props['PropSuperFeatured'];
  } 

  /**
   * Write accessor of PropSuperFeatured.  
   * Indicates whether it is a super featured item.
   * 
   * @access public 
   * @param boolean $value The new value for the PropSuperFeatured property
   * @return void 
   */
  function setPropSuperFeatured( $value )
  {
    $this->_props['PropSuperFeatured'] = $value;
  } 

  /**
   * Read accessor of PropType. 
   * Indicates the auction format for the listing specified in the Id input argument. Possible values:
   * 0 = Unknown auction type,
   * 1 = Chinese auction,
   * 2 = Dutch auction,
   * 5 = Live Auctions-type auction,
   * 6 = Ad type auction,
   * 7 = Stores Fixed-Price auction (US only),
   * 8 = Personal Offer auction, 
   * 9 = Fixed-Price item
   * 
   * @access public 
   * @return define Value of the PropType property
   */
  function getPropType()
  {
    return $this->_props['PropType'];
  } 

  /**
   * Write accessor of PropType.  
   * Indicates the auction format for the listing specified in the Id input argument. Possible values:
   * 0 = Unknown auction type,
   * 1 = Chinese auction,
   * 2 = Dutch auction,
   * 5 = Live Auctions-type auction,
   * 6 = Ad type auction,
   * 7 = Stores Fixed-Price auction (US only),
   * 8 = Personal Offer auction, 
   * 9 = Fixed-Price item
   * 
   * @access public 
   * @param define $value The new value for the PropType property
   * @return void 
   */
  function setPropType( $value )
  {
    $this->_props['PropType'] = $value;
  } 

  /**
   * Read accessor of ItemRevised. 
   * Indicates whether the item was revised since the auction started.
   * 
   * @access public 
   * @return boolean Value of the ItemRevised property
   */
  function getItemRevised()
  {
    return $this->_props['ItemRevised'];
  } 

  /**
   * Read accessor of LeadCount. 
   * Applicable to ad-format items only. Indicates how many leads to potential buyers are associated with this item. For other item types (other than ad-format items), returns a value of 0 (zero).
   * 
   * @access public 
   * @return number Value of the LeadCount property
   */
  function getLeadCount()
  {
    return $this->_props['LeadCount'];
  } 

  /**
   * Read accessor of ListDesignLayoutId. 
   * Identifies the Layout template associated with the item.
   * 
   * @access public 
   * @return number Value of the ListDesignLayoutId property
   */
  function getListDesignLayoutId()
  {
    return $this->_props['ListDesignLayoutId'];
  } 

  /**
   * Write accessor of ListDesignLayoutId.  
   * Identifies the Layout template associated with the item.
   * 
   * @access public 
   * @param number $value The new value for the ListDesignLayoutId property
   * @return void 
   */
  function setListDesignLayoutId( $value )
  {
    $this->_props['ListDesignLayoutId'] = $value;
  } 

  /**
   * Read accessor of ListDesignThemeId. 
   * Identifies the Theme template associated with the item.
   * 
   * @access public 
   * @return number Value of the ListDesignThemeId property
   */
  function getListDesignThemeId()
  {
    return $this->_props['ListDesignThemeId'];
  } 

  /**
   * Write accessor of ListDesignThemeId.  
   * Identifies the Theme template associated with the item.
   * 
   * @access public 
   * @param number $value The new value for the ListDesignThemeId property
   * @return void 
   */
  function setListDesignThemeId( $value )
  {
    $this->_props['ListDesignThemeId'] = $value;
  } 

  /**
   * Read accessor of Location. 
   * Where the item is at time of auction.
   * 
   * @access public 
   * @return string Value of the Location property
   */
  function getLocation()
  {
    return $this->_props['Location'];
  } 

  /**
   * Write accessor of Location.  
   * Where the item is at time of auction.
   * 
   * @access public 
   * @param string $value The new value for the Location property
   * @return void 
   */
  function setLocation( $value )
  {
    $this->_props['Location'] = $value;
  } 

  /**
   * Read accessor of MinimumToBid. 
   * Smallest amount the next bid on the item may be. Only applicable to auction-type listings; returns zero for all fixed-price and Ad-type listings (ItemProperties.Type returns a 6, 7, or 9). For auction-type listings, returns same value as StartPrice (if no bids have yet been placed) or CurrentPrice plus BidIncrement (if at least one bid has been placed).
   * 
   * @access public 
   * @return money Value of the MinimumToBid property
   */
  function getMinimumToBid()
  {
    return $this->_props['MinimumToBid'];
  } 

  /**
   * Read accessor of OriginalItemId. 
   * Component of Second Chance Offer feature, this field returns the item ID for the original item a seller offers through a Second Chance Offer listing. The field is only returned if the item specified in the ItemId input argument is a Second Chance Offer listing.
   * 
   * @access public 
   * @return string Value of the OriginalItemId property
   */
  function getOriginalItemId()
  {
    return $this->_props['OriginalItemId'];
  } 

  /**
   * Write accessor of OriginalItemId.  
   * Component of Second Chance Offer feature, this field returns the item ID for the original item a seller offers through a Second Chance Offer listing. The field is only returned if the item specified in the ItemId input argument is a Second Chance Offer listing.
   * 
   * @access public 
   * @param string $value The new value for the OriginalItemId property
   * @return void 
   */
  function setOriginalItemId( $value )
  {
    $this->_props['OriginalItemId'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsAmEx. 
   * Payment options, American Express is acceptable payment method. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsAmEx property
   */
  function getPaymentTermsAmEx()
  {
    return $this->_props['PaymentTermsAmEx'];
  } 

  /**
   * Write accessor of PaymentTermsAmEx.  
   * Payment options, American Express is acceptable payment method. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsAmEx property
   * @return void 
   */
  function setPaymentTermsAmEx( $value )
  {
    $this->_props['PaymentTermsAmEx'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsCashOnPickupAccepted. 
   * Payment on delivery acceptable payment term. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsCashOnPickupAccepted property
   */
  function getPaymentTermsCashOnPickupAccepted()
  {
    return $this->_props['PaymentTermsCashOnPickupAccepted'];
  } 

  /**
   * Write accessor of PaymentTermsCashOnPickupAccepted.  
   * Payment on delivery acceptable payment term. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsCashOnPickupAccepted property
   * @return void 
   */
  function setPaymentTermsCashOnPickupAccepted( $value )
  {
    $this->_props['PaymentTermsCashOnPickupAccepted'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsCCAccepted. 
   * Credit card acceptable payment term. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsCCAccepted property
   */
  function getPaymentTermsCCAccepted()
  {
    return $this->_props['PaymentTermsCCAccepted'];
  } 

  /**
   * Write accessor of PaymentTermsCCAccepted.  
   * Credit card acceptable payment term. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsCCAccepted property
   * @return void 
   */
  function setPaymentTermsCCAccepted( $value )
  {
    $this->_props['PaymentTermsCCAccepted'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsCOD. 
   * Cash On Delivery is acceptable payment method. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsCOD property
   */
  function getPaymentTermsCOD()
  {
    return $this->_props['PaymentTermsCOD'];
  } 

  /**
   * Write accessor of PaymentTermsCOD.  
   * Cash On Delivery is acceptable payment method. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsCOD property
   * @return void 
   */
  function setPaymentTermsCOD( $value )
  {
    $this->_props['PaymentTermsCOD'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsDiscover. 
   * Discover Card is acceptable payment method. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsDiscover property
   */
  function getPaymentTermsDiscover()
  {
    return $this->_props['PaymentTermsDiscover'];
  } 

  /**
   * Write accessor of PaymentTermsDiscover.  
   * Discover Card is acceptable payment method. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsDiscover property
   * @return void 
   */
  function setPaymentTermsDiscover( $value )
  {
    $this->_props['PaymentTermsDiscover'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsEscrow. 
   * Online Escrow paid for by buyer.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsEscrow property
   */
  function getPaymentTermsEscrow()
  {
    return $this->_props['PaymentTermsEscrow'];
  } 

  /**
   * Write accessor of PaymentTermsEscrow.  
   * Online Escrow paid for by buyer.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsEscrow property
   * @return void 
   */
  function setPaymentTermsEscrow( $value )
  {
    $this->_props['PaymentTermsEscrow'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsEscrowBySeller. 
   * Online Escrow paid for by seller.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsEscrowBySeller property
   */
  function getPaymentTermsEscrowBySeller()
  {
    return $this->_props['PaymentTermsEscrowBySeller'];
  } 

  /**
   * Write accessor of PaymentTermsEscrowBySeller.  
   * Online Escrow paid for by seller.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsEscrowBySeller property
   * @return void 
   */
  function setPaymentTermsEscrowBySeller( $value )
  {
    $this->_props['PaymentTermsEscrowBySeller'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsMOCashiers. 
   * Money orders and cashiers checks are acceptable payment methods. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsMOCashiers property
   */
  function getPaymentTermsMOCashiers()
  {
    return $this->_props['PaymentTermsMOCashiers'];
  } 

  /**
   * Write accessor of PaymentTermsMOCashiers.  
   * Money orders and cashiers checks are acceptable payment methods. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsMOCashiers property
   * @return void 
   */
  function setPaymentTermsMOCashiers( $value )
  {
    $this->_props['PaymentTermsMOCashiers'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsMoneyXferAccepted. 
   * Direct transfer of money is an acceptable payment method. See the eBay online help (e.g., http://pages.ebay.com.au/help/sell/bank-transfer-intro.html) for more information about accepting direct money transfers. Applicable for certain sites. See International Item Matrix. At least one of the payment methods (VisaMaster, etc.) is set to 1 (true). Payment methods are not applicable for Real Estate listings. 
   * 
   * If MoneyXferAcceptedinCheckout is 1 (true) and Checkout has been enabled for the seller, MoneyXferAccepted returns 1 (true) automatically. If MoneyXferAcceptedinCheckout is 1 (true), but Checkout has been disabled, eBay uses the value specified for MoneyXferAccepted (i.e., MoneyXferAcceptedinCheckout has no effect, as if it were never specified).
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsMoneyXferAccepted property
   */
  function getPaymentTermsMoneyXferAccepted()
  {
    return $this->_props['PaymentTermsMoneyXferAccepted'];
  } 

  /**
   * Write accessor of PaymentTermsMoneyXferAccepted.  
   * Direct transfer of money is an acceptable payment method. See the eBay online help (e.g., http://pages.ebay.com.au/help/sell/bank-transfer-intro.html) for more information about accepting direct money transfers. Applicable for certain sites. See International Item Matrix. At least one of the payment methods (VisaMaster, etc.) is set to 1 (true). Payment methods are not applicable for Real Estate listings. 
   * 
   * If MoneyXferAcceptedinCheckout is 1 (true) and Checkout has been enabled for the seller, MoneyXferAccepted returns 1 (true) automatically. If MoneyXferAcceptedinCheckout is 1 (true), but Checkout has been disabled, eBay uses the value specified for MoneyXferAccepted (i.e., MoneyXferAcceptedinCheckout has no effect, as if it were never specified).
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsMoneyXferAccepted property
   * @return void 
   */
  function setPaymentTermsMoneyXferAccepted( $value )
  {
    $this->_props['PaymentTermsMoneyXferAccepted'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsMoneyXferAcceptedinCheckout. 
   * Direct transfer of money is an acceptable payment method in Checkout. If the seller has bank account information on file and eBay Checkout has been enabled for the seller (see the Checkout Preferences page in My eBay), setting MoneyXferAcceptedinCheckout to 1 (true) causes the bank account information to be displayed in Checkout. If 1 (true) is passed but Checkout has been disabled, eBay returns a warning and resets MoneyXferAcceptedinCheckout to 0 (false). See the eBay online help (e.g., http://pages.ebay.com.au/help/sell/bank-transfer-intro.html) for more information about accepting direct money transfers in Checkout. Applicable for certain sites. See International Item Matrix. At least one of the payment methods (VisaMaster, etc.) is set to 1 (true). Payment methods are not applicable for Real Estate listings.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsMoneyXferAcceptedinCheckout property
   */
  function getPaymentTermsMoneyXferAcceptedinCheckout()
  {
    return $this->_props['PaymentTermsMoneyXferAcceptedinCheckout'];
  } 

  /**
   * Write accessor of PaymentTermsMoneyXferAcceptedinCheckout.  
   * Direct transfer of money is an acceptable payment method in Checkout. If the seller has bank account information on file and eBay Checkout has been enabled for the seller (see the Checkout Preferences page in My eBay), setting MoneyXferAcceptedinCheckout to 1 (true) causes the bank account information to be displayed in Checkout. If 1 (true) is passed but Checkout has been disabled, eBay returns a warning and resets MoneyXferAcceptedinCheckout to 0 (false). See the eBay online help (e.g., http://pages.ebay.com.au/help/sell/bank-transfer-intro.html) for more information about accepting direct money transfers in Checkout. Applicable for certain sites. See International Item Matrix. At least one of the payment methods (VisaMaster, etc.) is set to 1 (true). Payment methods are not applicable for Real Estate listings.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsMoneyXferAcceptedinCheckout property
   * @return void 
   */
  function setPaymentTermsMoneyXferAcceptedinCheckout( $value )
  {
    $this->_props['PaymentTermsMoneyXferAcceptedinCheckout'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsOther. 
   * Other is an acceptable payment method. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsOther property
   */
  function getPaymentTermsOther()
  {
    return $this->_props['PaymentTermsOther'];
  } 

  /**
   * Write accessor of PaymentTermsOther.  
   * Other is an acceptable payment method. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsOther property
   * @return void 
   */
  function setPaymentTermsOther( $value )
  {
    $this->_props['PaymentTermsOther'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsOtherPaymentsOnline. 
   * Non-eBay online payment is an acceptable payment method. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsOtherPaymentsOnline property
   */
  function getPaymentTermsOtherPaymentsOnline()
  {
    return $this->_props['PaymentTermsOtherPaymentsOnline'];
  } 

  /**
   * Write accessor of PaymentTermsOtherPaymentsOnline.  
   * Non-eBay online payment is an acceptable payment method. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsOtherPaymentsOnline property
   * @return void 
   */
  function setPaymentTermsOtherPaymentsOnline( $value )
  {
    $this->_props['PaymentTermsOtherPaymentsOnline'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsPayPalAccepted. 
   * If true, indicates that the seller accepts PayPal as a form of payment for this item. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsPayPalAccepted property
   */
  function getPaymentTermsPayPalAccepted()
  {
    return $this->_props['PaymentTermsPayPalAccepted'];
  } 

  /**
   * Write accessor of PaymentTermsPayPalAccepted.  
   * If true, indicates that the seller accepts PayPal as a form of payment for this item. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsPayPalAccepted property
   * @return void 
   */
  function setPaymentTermsPayPalAccepted( $value )
  {
    $this->_props['PaymentTermsPayPalAccepted'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsPayPalEmailAddress. 
   * Seller's email address on file with PayPal that is associated with the item. Only returned when the seller is the user calling Item->Retrieve.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsPayPalEmailAddress property
   */
  function getPaymentTermsPayPalEmailAddress()
  {
    return $this->_props['PaymentTermsPayPalEmailAddress'];
  } 

  /**
   * Write accessor of PaymentTermsPayPalEmailAddress.  
   * Seller's email address on file with PayPal that is associated with the item. Only returned when the seller is the user calling Item->Retrieve.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsPayPalEmailAddress property
   * @return void 
   */
  function setPaymentTermsPayPalEmailAddress( $value )
  {
    $this->_props['PaymentTermsPayPalEmailAddress'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsPersonalCheck. 
   * Personal checks is acceptable payment method. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsPersonalCheck property
   */
  function getPaymentTermsPersonalCheck()
  {
    return $this->_props['PaymentTermsPersonalCheck'];
  } 

  /**
   * Write accessor of PaymentTermsPersonalCheck.  
   * Personal checks is acceptable payment method. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsPersonalCheck property
   * @return void 
   */
  function setPaymentTermsPersonalCheck( $value )
  {
    $this->_props['PaymentTermsPersonalCheck'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsSeeDescription. 
   * Acceptable payment method is in Description. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsSeeDescription property
   */
  function getPaymentTermsSeeDescription()
  {
    return $this->_props['PaymentTermsSeeDescription'];
  } 

  /**
   * Write accessor of PaymentTermsSeeDescription.  
   * Acceptable payment method is in Description. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsSeeDescription property
   * @return void 
   */
  function setPaymentTermsSeeDescription( $value )
  {
    $this->_props['PaymentTermsSeeDescription'] = $value;
  } 

  /**
   * Read accessor of PaymentTermsVisaMaster. 
   * Visa & Master Card is acceptable payment methods. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @return boolean Value of the PaymentTermsVisaMaster property
   */
  function getPaymentTermsVisaMaster()
  {
    return $this->_props['PaymentTermsVisaMaster'];
  } 

  /**
   * Write accessor of PaymentTermsVisaMaster.  
   * Visa & Master Card is acceptable payment methods. Not applicable for Real Estate listings.
   * 
   * @access public 
   * @param boolean $value The new value for the PaymentTermsVisaMaster property
   * @return void 
   */
  function setPaymentTermsVisaMaster( $value )
  {
    $this->_props['PaymentTermsVisaMaster'] = $value;
  } 

  /**
   * Read accessor of PhotoCount. 
   * Indicates the number of photos used for PhotoHosting slide show.
   * 
   * @access public 
   * @return number Value of the PhotoCount property
   */
  function getPhotoCount()
  {
    return $this->_props['PhotoCount'];
  } 

  /**
   * Write accessor of PhotoCount.  
   * Indicates the number of photos used for PhotoHosting slide show.
   * 
   * @access public 
   * @param number $value The new value for the PhotoCount property
   * @return void 
   */
  function setPhotoCount( $value )
  {
    $this->_props['PhotoCount'] = $value;
  } 

  /**
   * Read accessor of PhotoDisplayType. 
   * Type of display for photos used for PhotoHosting slide show. 
   * 0 = No special Picture Services features.,  1 = Slideshow of multiple pictures.,  2 = Large format picture., 3 = Picture Pack.
   * 
   * @access public 
   * @return define Value of the PhotoDisplayType property
   */
  function getPhotoDisplayType()
  {
    return $this->_props['PhotoDisplayType'];
  } 

  /**
   * Write accessor of PhotoDisplayType.  
   * Type of display for photos used for PhotoHosting slide show. 
   * 0 = No special Picture Services features.,  1 = Slideshow of multiple pictures.,  2 = Large format picture., 3 = Picture Pack.
   * 
   * @access public 
   * @param define $value The new value for the PhotoDisplayType property
   * @return void 
   */
  function setPhotoDisplayType( $value )
  {
    $this->_props['PhotoDisplayType'] = $value;
  } 

  /**
   * Read accessor of PictureURL. 
   * URL for the picture(s) for the item. Returned as CDATA. The data for this field will be no longer than 1,024 characters. Files referenced would be in the formats JPEG, BMP, TIF, or GIF (for non-Motors items). If there is more than one photo for the item, the URLs are expressed as a semicolon-separated list.
   * 
   * @access public 
   * @return string Value of the PictureURL property
   */
  function getPictureURL()
  {
    return $this->_props['PictureURL'];
  } 

  /**
   * Write accessor of PictureURL.  
   * URL for the picture(s) for the item. Returned as CDATA. The data for this field will be no longer than 1,024 characters. Files referenced would be in the formats JPEG, BMP, TIF, or GIF (for non-Motors items). If there is more than one photo for the item, the URLs are expressed as a semicolon-separated list.
   * 
   * @access public 
   * @param string $value The new value for the PictureURL property
   * @return void 
   */
  function setPictureURL( $value )
  {
    $this->_props['PictureURL'] = $value;
  } 

  /**
   * Read accessor of Quantity. 
   * Number of items being sold in the auction.
   * 
   * @access public 
   * @return number Value of the Quantity property
   */
  function getQuantity()
  {
    return $this->_props['Quantity'];
  } 

  /**
   * Write accessor of Quantity.  
   * Number of items being sold in the auction.
   * 
   * @access public 
   * @param number $value The new value for the Quantity property
   * @return void 
   */
  function setQuantity( $value )
  {
    $this->_props['Quantity'] = $value;
  } 

  /**
   * Read accessor of QuantitySold. 
   * Number of items purchased so far. (Subtract from the value returned in the Quantity field to calculate the number of items remaining.)
   * 
   * @access public 
   * @return number Value of the QuantitySold property
   */
  function getQuantitySold()
  {
    return $this->_props['QuantitySold'];
  } 

  /**
   * Read accessor of Region. 
   * Region where the item is listed. See Region Table for values. If the item is listed with a Region of 0 (zero), then this field returns as an empty XML element.
   * 
   * @access public 
   * @return string Value of the Region property
   */
  function getRegion()
  {
    return $this->_props['Region'];
  } 

  /**
   * Write accessor of Region.  
   * Region where the item is listed. See Region Table for values. If the item is listed with a Region of 0 (zero), then this field returns as an empty XML element.
   * 
   * @access public 
   * @param string $value The new value for the Region property
   * @return void 
   */
  function setRegion( $value )
  {
    $this->_props['Region'] = $value;
  } 

  /**
   * Read accessor of RelistId. 
   * Returns the new item ID for a relisted item. When an item is relisted, the old (expired) listing is annotated with the new (relist) item ID. This field only appears when the old listing is retrieved. For more information about relisting items, see: http://pages.ebay.com/help/sellerguide/selling-item.html.
   * 
   * @access public 
   * @return number Value of the RelistId property
   */
  function getRelistId()
  {
    return $this->_props['RelistId'];
  } 

  /**
   * Write accessor of RelistId.  
   * Returns the new item ID for a relisted item. When an item is relisted, the old (expired) listing is annotated with the new (relist) item ID. This field only appears when the old listing is retrieved. For more information about relisting items, see: http://pages.ebay.com/help/sellerguide/selling-item.html.
   * 
   * @access public 
   * @param number $value The new value for the RelistId property
   * @return void 
   */
  function setRelistId( $value )
  {
    $this->_props['RelistId'] = $value;
  } 

  /**
   * Read accessor of ReservePrice. 
   * Indicates the reserve price for a reserve auction. Returned only if DetailLevel = 4. ReservePrice is only returned when the user calling GetItem is the item's seller. Returned as a zero value for non-auction type items. For more information on reserve price auctions, see http://pages.ebay.com/help/basics/f-format.html#1.
   * 
   * @access public 
   * @return money Value of the ReservePrice property
   */
  function getReservePrice()
  {
    return $this->_props['ReservePrice'];
  } 

  /**
   * Write accessor of ReservePrice.  
   * Indicates the reserve price for a reserve auction. Returned only if DetailLevel = 4. ReservePrice is only returned when the user calling GetItem is the item's seller. Returned as a zero value for non-auction type items. For more information on reserve price auctions, see http://pages.ebay.com/help/basics/f-format.html#1.
   * 
   * @access public 
   * @param money $value The new value for the ReservePrice property
   * @return void 
   */
  function setReservePrice( $value )
  {
    $this->_props['ReservePrice'] = $value;
  } 

  /**
   * Read accessor of SecondChanceEligible. 
   * Component of Second Chance Offer feature, this field indicates whether the item is eligible to be offered as a Second Chance Offer listing.
   * 
   * @access public 
   * @return boolean Value of the SecondChanceEligible property
   */
  function getSecondChanceEligible()
  {
    return $this->_props['SecondChanceEligible'];
  } 

  /**
   * Read accessor of Seller. 
   * Returns the Seller's user object
   * (readonly)
   * 
   * @access public 
   * @return Ebay _User Value of the Seller property
   */
  function getSeller()
  {
    return $this->_props['Seller'];
  } 

  /**
   * Read accessor of ShippingOption. 
   * Basic shipping options: "SiteOnly", "WorldWide", "WillNotShip", or "SitePlusRegions". A value of "SitePlusRegions" indicates the seller will ship within the country associated with the item's site plus any region represented with a true value in the tags: NorthAmerica, Europe, Oceania, Asia, SouthAmerica, Africa, LatinAmerica, MiddleEast, and Caribbean.
   * 
   * @access public 
   * @return string Value of the ShippingOption property
   */
  function getShippingOption()
  {
    return $this->_props['ShippingOption'];
  } 

  /**
   * Write accessor of ShippingOption.  
   * Basic shipping options: "SiteOnly", "WorldWide", "WillNotShip", or "SitePlusRegions". A value of "SitePlusRegions" indicates the seller will ship within the country associated with the item's site plus any region represented with a true value in the tags: NorthAmerica, Europe, Oceania, Asia, SouthAmerica, Africa, LatinAmerica, MiddleEast, and Caribbean.
   * 
   * @access public 
   * @param string $value The new value for the ShippingOption property
   * @return void 
   */
  function setShippingOption( $value )
  {
    $this->_props['ShippingOption'] = $value;
  } 

  /**
   * Read accessor of ShippingRegionsAfrica. 
   * Shipping regions, seller will ship to Africa.
   * 
   * @access public 
   * @return boolean Value of the ShippingRegionsAfrica property
   */
  function getShippingRegionsAfrica()
  {
    return $this->_props['ShippingRegionsAfrica'];
  } 

  /**
   * Write accessor of ShippingRegionsAfrica.  
   * Shipping regions, seller will ship to Africa.
   * 
   * @access public 
   * @param boolean $value The new value for the ShippingRegionsAfrica property
   * @return void 
   */
  function setShippingRegionsAfrica( $value )
  {
    $this->_props['ShippingRegionsAfrica'] = $value;
  } 

  /**
   * Read accessor of ShippingRegionsAsia. 
   * Seller will ship to Asia.
   * 
   * @access public 
   * @return boolean Value of the ShippingRegionsAsia property
   */
  function getShippingRegionsAsia()
  {
    return $this->_props['ShippingRegionsAsia'];
  } 

  /**
   * Write accessor of ShippingRegionsAsia.  
   * Seller will ship to Asia.
   * 
   * @access public 
   * @param boolean $value The new value for the ShippingRegionsAsia property
   * @return void 
   */
  function setShippingRegionsAsia( $value )
  {
    $this->_props['ShippingRegionsAsia'] = $value;
  } 

  /**
   * Read accessor of ShippingRegionsCaribbean. 
   * Seller will ship to Caribbean.
   * 
   * @access public 
   * @return boolean Value of the ShippingRegionsCaribbean property
   */
  function getShippingRegionsCaribbean()
  {
    return $this->_props['ShippingRegionsCaribbean'];
  } 

  /**
   * Write accessor of ShippingRegionsCaribbean.  
   * Seller will ship to Caribbean.
   * 
   * @access public 
   * @param boolean $value The new value for the ShippingRegionsCaribbean property
   * @return void 
   */
  function setShippingRegionsCaribbean( $value )
  {
    $this->_props['ShippingRegionsCaribbean'] = $value;
  } 

  /**
   * Read accessor of ShippingRegionsEurope. 
   * Seller will ship to Europe.
   * 
   * @access public 
   * @return boolean Value of the ShippingRegionsEurope property
   */
  function getShippingRegionsEurope()
  {
    return $this->_props['ShippingRegionsEurope'];
  } 

  /**
   * Write accessor of ShippingRegionsEurope.  
   * Seller will ship to Europe.
   * 
   * @access public 
   * @param boolean $value The new value for the ShippingRegionsEurope property
   * @return void 
   */
  function setShippingRegionsEurope( $value )
  {
    $this->_props['ShippingRegionsEurope'] = $value;
  } 

  /**
   * Read accessor of ShippingRegionsLatinAmerica. 
   * Seller will ship to Latin America.
   * 
   * @access public 
   * @return boolean Value of the ShippingRegionsLatinAmerica property
   */
  function getShippingRegionsLatinAmerica()
  {
    return $this->_props['ShippingRegionsLatinAmerica'];
  } 

  /**
   * Write accessor of ShippingRegionsLatinAmerica.  
   * Seller will ship to Latin America.
   * 
   * @access public 
   * @param boolean $value The new value for the ShippingRegionsLatinAmerica property
   * @return void 
   */
  function setShippingRegionsLatinAmerica( $value )
  {
    $this->_props['ShippingRegionsLatinAmerica'] = $value;
  } 

  /**
   * Read accessor of ShippingRegionsMiddleEast. 
   * Seller will ship to Middle East.
   * 
   * @access public 
   * @return boolean Value of the ShippingRegionsMiddleEast property
   */
  function getShippingRegionsMiddleEast()
  {
    return $this->_props['ShippingRegionsMiddleEast'];
  } 

  /**
   * Write accessor of ShippingRegionsMiddleEast.  
   * Seller will ship to Middle East.
   * 
   * @access public 
   * @param boolean $value The new value for the ShippingRegionsMiddleEast property
   * @return void 
   */
  function setShippingRegionsMiddleEast( $value )
  {
    $this->_props['ShippingRegionsMiddleEast'] = $value;
  } 

  /**
   * Read accessor of ShippingRegionsNorthAmerica. 
   * Seller will ship to North America.
   * 
   * @access public 
   * @return boolean Value of the ShippingRegionsNorthAmerica property
   */
  function getShippingRegionsNorthAmerica()
  {
    return $this->_props['ShippingRegionsNorthAmerica'];
  } 

  /**
   * Write accessor of ShippingRegionsNorthAmerica.  
   * Seller will ship to North America.
   * 
   * @access public 
   * @param boolean $value The new value for the ShippingRegionsNorthAmerica property
   * @return void 
   */
  function setShippingRegionsNorthAmerica( $value )
  {
    $this->_props['ShippingRegionsNorthAmerica'] = $value;
  } 

  /**
   * Read accessor of ShippingRegionsOceania. 
   * Seller will ship to Oceania (Pacific region other than Asia).
   * 
   * @access public 
   * @return boolean Value of the ShippingRegionsOceania property
   */
  function getShippingRegionsOceania()
  {
    return $this->_props['ShippingRegionsOceania'];
  } 

  /**
   * Write accessor of ShippingRegionsOceania.  
   * Seller will ship to Oceania (Pacific region other than Asia).
   * 
   * @access public 
   * @param boolean $value The new value for the ShippingRegionsOceania property
   * @return void 
   */
  function setShippingRegionsOceania( $value )
  {
    $this->_props['ShippingRegionsOceania'] = $value;
  } 

  /**
   * Read accessor of ShippingRegionsSouthAmerica. 
   * Seller will ship to South America.
   * 
   * @access public 
   * @return boolean Value of the ShippingRegionsSouthAmerica property
   */
  function getShippingRegionsSouthAmerica()
  {
    return $this->_props['ShippingRegionsSouthAmerica'];
  } 

  /**
   * Write accessor of ShippingRegionsSouthAmerica.  
   * Seller will ship to South America.
   * 
   * @access public 
   * @param boolean $value The new value for the ShippingRegionsSouthAmerica property
   * @return void 
   */
  function setShippingRegionsSouthAmerica( $value )
  {
    $this->_props['ShippingRegionsSouthAmerica'] = $value;
  } 

  /**
   * Read accessor of ShippingTermsSellerPays. 
   * Seller pays all shipping.
   * 
   * @access public 
   * @return boolean Value of the ShippingTermsSellerPays property
   */
  function getShippingTermsSellerPays()
  {
    return $this->_props['ShippingTermsSellerPays'];
  } 

  /**
   * Write accessor of ShippingTermsSellerPays.  
   * Seller pays all shipping.
   * 
   * @access public 
   * @param boolean $value The new value for the ShippingTermsSellerPays property
   * @return void 
   */
  function setShippingTermsSellerPays( $value )
  {
    $this->_props['ShippingTermsSellerPays'] = $value;
  } 

  /**
   * Read accessor of SiteCurrency. 
   * Numeric code corresponding to the currency of the site that is requesting the item.
   * 
   * @access public 
   * @return number Value of the SiteCurrency property
   */
  function getSiteCurrency()
  {
    return $this->_props['SiteCurrency'];
  } 

  /**
   * Read accessor of SiteId. 
   * eBay site on which the requested item is listed. See the SiteId Table for valid values.
   * Normally the SiteId will be set automatically via the Session's SiteId. Create a new special Session you like to list on other sites.
   * 
   * @access public 
   * @return number Value of the SiteId property
   */
  function getSiteId()
  {
    return $this->_props['SiteId'];
  } 

  /**
   * Write accessor of SiteId.  
   * eBay site on which the requested item is listed. See the SiteId Table for valid values.
   * Normally the SiteId will be set automatically via the Session's SiteId. Create a new special Session you like to list on other sites.
   * 
   * @access public 
   * @param number $value The new value for the SiteId property
   * @return void 
   */
  function setSiteId( $value )
  {
    $this->_props['SiteId'] = $value;
  } 

  /**
   * Read accessor of StartPrice. 
   * Returns the price the seller specified when originally listing or re-listing the item. Returns the new start price if the item is revised and the starting price is changed. For auction-type listings, competitive bidding starts at this value. Once at least one bid has been placed, StartPrice remains the same but CurrentPrice is incremented to the amount of each succeeding bid. For fixed-price listings (ItemProperties.Type returns a 7 or 9), returns the unchanging price at which a buyer may purchase the item.
   * 
   * @access public 
   * @return money Value of the StartPrice property
   */
  function getStartPrice()
  {
    return $this->_props['StartPrice'];
  } 

  /**
   * Write accessor of StartPrice.  
   * Returns the price the seller specified when originally listing or re-listing the item. Returns the new start price if the item is revised and the starting price is changed. For auction-type listings, competitive bidding starts at this value. Once at least one bid has been placed, StartPrice remains the same but CurrentPrice is incremented to the amount of each succeeding bid. For fixed-price listings (ItemProperties.Type returns a 7 or 9), returns the unchanging price at which a buyer may purchase the item.
   * 
   * @access public 
   * @param money $value The new value for the StartPrice property
   * @return void 
   */
  function setStartPrice( $value )
  {
    $this->_props['StartPrice'] = $value;
  } 

  /**
   * Read accessor of StartTime. 
   * Time stamp for the start of the listing. Will be converted automatically to local time, depending on the settings of the Session
   * 
   * @access public 
   * @return datetime Value of the StartTime property
   */
  function getStartTime()
  {
    return $this->_props['StartTime'];
  } 

  /**
   * Read accessor of StorefrontInfoDepartmentNumber. 
   * Department number. Internal reference number specific to an eBay Store seller's operations. Corresponds to the eBay Stores AddItem input argument StoreCategory (see Using AddItem for eBay Stores). Returned as null for International Fixed-Price items.
   * 
   * @access public 
   * @return number Value of the StorefrontInfoDepartmentNumber property
   */
  function getStorefrontInfoDepartmentNumber()
  {
    return $this->_props['StorefrontInfoDepartmentNumber'];
  } 

  /**
   * Write accessor of StorefrontInfoDepartmentNumber.  
   * Department number. Internal reference number specific to an eBay Store seller's operations. Corresponds to the eBay Stores AddItem input argument StoreCategory (see Using AddItem for eBay Stores). Returned as null for International Fixed-Price items.
   * 
   * @access public 
   * @param number $value The new value for the StorefrontInfoDepartmentNumber property
   * @return void 
   */
  function setStorefrontInfoDepartmentNumber( $value )
  {
    $this->_props['StorefrontInfoDepartmentNumber'] = $value;
  } 

  /**
   * Read accessor of StorefrontInfoStoreLocation. 
   * URL pointing to the seller's eBay Store page. Returned as null for International Fixed Price items. This URL follows the format below, where "####" is replaced by the seller's eBay Stores ID (that uniquely identifies the eBay Store).
   * 
   * http://www.ebaystores.com/id=####
   * 
   * @access public 
   * @return string Value of the StorefrontInfoStoreLocation property
   */
  function getStorefrontInfoStoreLocation()
  {
    return $this->_props['StorefrontInfoStoreLocation'];
  } 

  /**
   * Write accessor of StorefrontInfoStoreLocation.  
   * URL pointing to the seller's eBay Store page. Returned as null for International Fixed Price items. This URL follows the format below, where "####" is replaced by the seller's eBay Stores ID (that uniquely identifies the eBay Store).
   * 
   * http://www.ebaystores.com/id=####
   * 
   * @access public 
   * @param string $value The new value for the StorefrontInfoStoreLocation property
   * @return void 
   */
  function setStorefrontInfoStoreLocation( $value )
  {
    $this->_props['StorefrontInfoStoreLocation'] = $value;
  } 

  /**
   * Read accessor of StorefrontItem. 
   * Indicates whether the item is an eBay Stores item. Use the Type return field in conjunction with StorefrontItem to determine whether the item is a fixed-price eBay Stores item (7 in Type) or an eBay Stores auction item. Returned as null for International Fixed Price items.
   * 
   * @access public 
   * @return boolean Value of the StorefrontItem property
   */
  function getStorefrontItem()
  {
    return $this->_props['StorefrontItem'];
  } 

  /**
   * Write accessor of StorefrontItem.  
   * Indicates whether the item is an eBay Stores item. Use the Type return field in conjunction with StorefrontItem to determine whether the item is a fixed-price eBay Stores item (7 in Type) or an eBay Stores auction item. Returned as null for International Fixed Price items.
   * 
   * @access public 
   * @param boolean $value The new value for the StorefrontItem property
   * @return void 
   */
  function setStorefrontItem( $value )
  {
    $this->_props['StorefrontItem'] = $value;
  } 

  /**
   * Read accessor of TimeLeftDays. 
   * Time left for active auction period, days portion.
   * 
   * @access public 
   * @return number Value of the TimeLeftDays property
   */
  function getTimeLeftDays()
  {
    return $this->_props['TimeLeftDays'];
  } 

  /**
   * Read accessor of TimeLeftHours. 
   * Time left for active auction period, hours portion.
   * 
   * @access public 
   * @return number Value of the TimeLeftHours property
   */
  function getTimeLeftHours()
  {
    return $this->_props['TimeLeftHours'];
  } 

  /**
   * Read accessor of TimeLeftMinutes. 
   * Time left for active auction period, minutes portion.
   * 
   * @access public 
   * @return number Value of the TimeLeftMinutes property
   */
  function getTimeLeftMinutes()
  {
    return $this->_props['TimeLeftMinutes'];
  } 

  /**
   * Read accessor of TimeLeftSeconds. 
   * Time left for active auction period, seconds portion.
   * 
   * @access public 
   * @return number Value of the TimeLeftSeconds property
   */
  function getTimeLeftSeconds()
  {
    return $this->_props['TimeLeftSeconds'];
  } 

  /**
   * Read accessor of Title. 
   * Name of the item as it appears for auctions. Returned as CDATA.
   * 
   * @access public 
   * @return string Value of the Title property
   */
  function getTitle()
  {
    return $this->_props['Title'];
  } 

  /**
   * Write accessor of Title.  
   * Name of the item as it appears for auctions. Returned as CDATA.
   * 
   * @access public 
   * @param string $value The new value for the Title property
   * @return void 
   */
  function setTitle( $value )
  {
    $this->_props['Title'] = $value;
  } 

  /**
   * Read accessor of TitleBarImage. 
   * Indicates whether an image for the item appears in the title bar of the item's listing page (View Item page) on the eBay site.
   * 
   * @access public 
   * @return boolean Value of the TitleBarImage property
   */
  function getTitleBarImage()
  {
    return $this->_props['TitleBarImage'];
  } 

  /**
   * Read accessor of UUID. 
   * Universally unique constraint tag. The UUID is unique to a category. The UUID can only contain digits from 0-9 and letters from A-F. The UUID must be 32 characters long.
   * 
   * @access public 
   * @return string Value of the UUID property
   */
  function getUUID()
  {
    return $this->_props['UUID'];
  } 

  /**
   * Write accessor of UUID.  
   * Universally unique constraint tag. The UUID is unique to a category. The UUID can only contain digits from 0-9 and letters from A-F. The UUID must be 32 characters long.
   * 
   * @access public 
   * @param string $value The new value for the UUID property
   * @return void 
   */
  function setUUID( $value )
  {
    $this->_props['UUID'] = $value;
  } 

  /**
   * Read accessor of Zip. 
   * Zip code for the seller.
   * 
   * @access public 
   * @return string Value of the Zip property
   */
  function getZip()
  {
    return $this->_props['Zip'];
  } 

  /**
   * Read accessor of FeesAuctionLengthFee. 
   * read only
   * Fee for 10-day auctions.
   * 
   * @access public 
   * @return money Value of the FeesAuctionLengthFee property
   */
  function getFeesAuctionLengthFee()
  {
    return $this->_props['FeesAuctionLengthFee'];
  } 

  /**
   * Read accessor of FeesBoldFee. 
   * read only
   * Fee to boldface the title for the item's listing.
   * 
   * @access public 
   * @return money Value of the FeesBoldFee property
   */
  function getFeesBoldFee()
  {
    return $this->_props['FeesBoldFee'];
  } 

  /**
   * Read accessor of FeesBuyItNowFee. 
   * read only
   * Fee to add the Buy It Now option to the item.
   * 
   * @access public 
   * @return money Value of the FeesBuyItNowFee property
   */
  function getFeesBuyItNowFee()
  {
    return $this->_props['FeesBuyItNowFee'];
  } 

  /**
   * Read accessor of FeesCategoryFeaturedFee. 
   * read only
   * Fee to have the item featured in its category.
   * 
   * @access public 
   * @return money Value of the FeesCategoryFeaturedFee property
   */
  function getFeesCategoryFeaturedFee()
  {
    return $this->_props['FeesCategoryFeaturedFee'];
  } 

  /**
   * Read accessor of FeesCurrencyId. 
   * read only
   * Billing currency of the seller. (See CurrencyId Table.)
   * 
   * @access public 
   * @return define Value of the FeesCurrencyId property
   */
  function getFeesCurrencyId()
  {
    return $this->_props['FeesCurrencyId'];
  } 

  /**
   * Read accessor of FeesFeaturedFee. 
   * read only
   * Fee to have the item appear at the top of item listings.
   * 
   * @access public 
   * @return money Value of the FeesFeaturedFee property
   */
  function getFeesFeaturedFee()
  {
    return $this->_props['FeesFeaturedFee'];
  } 

  /**
   * Read accessor of FeesFeaturedGalleryFee. 
   * read only
   * Fee to have the item featured in its gallery.
   * 
   * @access public 
   * @return money Value of the FeesFeaturedGalleryFee property
   */
  function getFeesFeaturedGalleryFee()
  {
    return $this->_props['FeesFeaturedGalleryFee'];
  } 

  /**
   * Read accessor of FeesFixedPriceDurationFee. 
   * read only
   * Fee for listing a fixed price item for a certain duration.
   * 
   * @access public 
   * @return money Value of the FeesFixedPriceDurationFee property
   */
  function getFeesFixedPriceDurationFee()
  {
    return $this->_props['FeesFixedPriceDurationFee'];
  } 

  /**
   * Read accessor of FeesGalleryFee. 
   * read only
   * Fee to have the item included in the gallery.
   * 
   * @access public 
   * @return money Value of the FeesGalleryFee property
   */
  function getFeesGalleryFee()
  {
    return $this->_props['FeesGalleryFee'];
  } 

  /**
   * Read accessor of FeesGiftIconFee. 
   * read only
   * Fee for displaying a gift icon next to the listing.
   * 
   * @access public 
   * @return money Value of the FeesGiftIconFee property
   */
  function getFeesGiftIconFee()
  {
    return $this->_props['FeesGiftIconFee'];
  } 

  /**
   * Read accessor of FeesHighLightFee. 
   * read only
   * Fee to have the item's listing appear highlighted.
   * 
   * @access public 
   * @return money Value of the FeesHighLightFee property
   */
  function getFeesHighLightFee()
  {
    return $this->_props['FeesHighLightFee'];
  } 

  /**
   * Read accessor of FeesInsertionFee. 
   * read only
   * Basic fee for listing the item. EU residents who sell items on EU sites may be subject to VAT.
   * 
   * @access public 
   * @return money Value of the FeesInsertionFee property
   */
  function getFeesInsertionFee()
  {
    return $this->_props['FeesInsertionFee'];
  } 

  /**
   * Read accessor of FeesListingDesignerFee. 
   * read only
   * Fee charged for the optional use of a Listing Designer layout or theme template.
   * 
   * @access public 
   * @return money Value of the FeesListingDesignerFee property
   */
  function getFeesListingDesignerFee()
  {
    return $this->_props['FeesListingDesignerFee'];
  } 

  /**
   * Read accessor of FeesListingFee. 
   * read only
   * Total fee for listing the item. Includes basic fee (InsertionFee) plus any speciality listing features (GalleryFee, HighLightFee, FeaturedFee, etc.). EU residents who sell items on EU sites may be subject to VAT.
   * 
   * @access public 
   * @return money Value of the FeesListingFee property
   */
  function getFeesListingFee()
  {
    return $this->_props['FeesListingFee'];
  } 

  /**
   * Read accessor of FeesPhotoDisplayFee. 
   * read only
   * Fee for use of Photo Hosting feature, a slideshow of multiple images.
   * 
   * @access public 
   * @return money Value of the FeesPhotoDisplayFee property
   */
  function getFeesPhotoDisplayFee()
  {
    return $this->_props['FeesPhotoDisplayFee'];
  } 

  /**
   * Read accessor of FeesPhotoFee. 
   * read only
   * Fee for associating 1-6 photos with an item's listing.
   * 
   * @access public 
   * @return money Value of the FeesPhotoFee property
   */
  function getFeesPhotoFee()
  {
    return $this->_props['FeesPhotoFee'];
  } 

  /**
   * Read accessor of FeesReserveFee. 
   * read only
   * Fee for specifying a reserve price for the item's auction.
   * 
   * @access public 
   * @return money Value of the FeesReserveFee property
   */
  function getFeesReserveFee()
  {
    return $this->_props['FeesReserveFee'];
  } 

  /**
   * Read accessor of FeesSchedulingFee. 
   * read only
   * Fee for scheduling the Item to be listed at a later date.
   * 
   * @access public 
   * @return money Value of the FeesSchedulingFee property
   */
  function getFeesSchedulingFee()
  {
    return $this->_props['FeesSchedulingFee'];
  } 

  /**
   * 
   * @access private 
   * @var Ebay _Session
   */

  var $_session = null;

  /**
   * 
   * @access private 
   * @var Ebay _ApiCaller
   */

  var $_apiCaller = null;

  /**
   * Read accessor of Charity.
   * 
   * @access public 
   * @return number Value of the Charity property
   */
  function getCharity()
  {
    return $this->_props['Charity'];
  } 

  /**
   * Write accessor of Charity.
   * 
   * @access public 
   * @param number $value The new value for the Charity property
   * @return void 
   */
  function setCharity( $value )
  {
    $this->_props['Charity'] = $value;
  } 

  /**
   * Read accessor of Link. 
   * 32 chars long value to be associated with the item. Will posted and retrieved to and from eBay.
   * 
   * @access public 
   * @return string Value of the Link property
   */
  function getLink()
  {
    return $this->_props['Link'];
  } 

  /**
   * Write accessor of Link.  
   * 32 chars long value to be associated with the item. Will posted and retrieved to and from eBay.
   * 
   * @access public 
   * @param string $value The new value for the Link property
   * @return void 
   */
  function setLink( $value )
  {
    $this->_props['Link'] = $value;
  } 

  /**
   * Read accessor of LocalizedCurrentPrice. 
   * read only
   * Fee for scheduling the Item to be listed at a later date.
   * 
   * @access public 
   * @return money Value of the LocalizedCurrentPrice property
   */
  function getLocalizedCurrentPrice()
  {
    return $this->_props['LocalizedCurrentPrice'];
  } 

  /**
   * Write accessor of LocalizedCurrentPrice.  
   * read only
   * Fee for scheduling the Item to be listed at a later date.
   * 
   * @access public 
   * @param money $value The new value for the LocalizedCurrentPrice property
   * @return void 
   */
  function setLocalizedCurrentPrice( $value )
  {
    $this->_props['LocalizedCurrentPrice'] = $value;
  } 

  /**
   * Read accessor of PropRestrictedToBusiness. 
   * Indicates whether it is a super featured item.
   * 
   * @access public 
   * @return boolean Value of the PropRestrictedToBusiness property
   */
  function getPropRestrictedToBusiness()
  {
    return $this->_props['PropRestrictedToBusiness'];
  } 

  /**
   * Write accessor of PropRestrictedToBusiness.  
   * Indicates whether it is a super featured item.
   * 
   * @access public 
   * @param boolean $value The new value for the PropRestrictedToBusiness property
   * @return void 
   */
  function setPropRestrictedToBusiness( $value )
  {
    $this->_props['PropRestrictedToBusiness'] = $value;
  } 

  /**
   * Read accessor of PropVATPercent. 
   * Indicates whether it is a super featured item.
   * 
   * @access public 
   * @return number Value of the PropVATPercent property
   */
  function getPropVATPercent()
  {
    return $this->_props['PropVATPercent'];
  } 

  /**
   * Write accessor of PropVATPercent.  
   * Indicates whether it is a super featured item.
   * 
   * @access public 
   * @param number $value The new value for the PropVATPercent property
   * @return void 
   */
  function setPropVATPercent( $value )
  {
    $this->_props['PropVATPercent'] = $value;
  } 

  /**
   * Read accessor of undocMobileInspection. 
   * undocumented
   * 
   * @access public 
   * @return string Value of the undocMobileInspection property
   */
  function getundocMobileInspection()
  {
    return $this->_props['undocMobileInspection'];
  } 

  /**
   * Write accessor of undocMobileInspection.  
   * undocumented
   * 
   * @access public 
   * @param string $value The new value for the undocMobileInspection property
   * @return void 
   */
  function setundocMobileInspection( $value )
  {
    $this->_props['undocMobileInspection'] = $value;
  } 

  /**
   * Read accessor of undocIsHomesDirect. 
   * undocumented
   * 
   * @access public 
   * @return boolean Value of the undocIsHomesDirect property
   */
  function getundocIsHomesDirect()
  {
    return $this->_props['undocIsHomesDirect'];
  } 

  /**
   * Write accessor of undocIsHomesDirect.  
   * undocumented
   * 
   * @access public 
   * @param boolean $value The new value for the undocIsHomesDirect property
   * @return void 
   */
  function setundocIsHomesDirect( $value )
  {
    $this->_props['undocIsHomesDirect'] = $value;
  } 

  /**
   * Read accessor of undocIsSecurePay. 
   * undocumented
   * 
   * @access public 
   * @return boolean Value of the undocIsSecurePay property
   */
  function getundocIsSecurePay()
  {
    return $this->_props['undocIsSecurePay'];
  } 

  /**
   * Write accessor of undocIsSecurePay.  
   * undocumented
   * 
   * @access public 
   * @param boolean $value The new value for the undocIsSecurePay property
   * @return void 
   */
  function setundocIsSecurePay( $value )
  {
    $this->_props['undocIsSecurePay'] = $value;
  } 

  /**
   * Read accessor of SubtitleText. 
   * 32 chars long value to be associated with the item. Will posted and retrieved to and from eBay.
   * 
   * @access public 
   * @return string Value of the SubtitleText property
   */
  function getSubtitleText()
  {
    return $this->_props['SubtitleText'];
  } 

  /**
   * Write accessor of SubtitleText.  
   * 32 chars long value to be associated with the item. Will posted and retrieved to and from eBay.
   * 
   * @access public 
   * @param string $value The new value for the SubtitleText property
   * @return void 
   */
  function setSubtitleText( $value )
  {
    $this->_props['SubtitleText'] = $value;
  } 

  /**
   * Write accessor of Duration.  
   * Number of days the auction will be active. Max length 3. Only certain values are allowed, and the choice of values depends on the listing type. See Durations Table. Specify GTC for the Good 'Til Cancel feature (eBay Stores Inventory items only). See Durations Table.
   * As of July 2003, durations of 3, 5, 7, 10 and 20 days are no longer available for eBay Stores Fixed-Price listings.
   * 
   * @access public 
   * @param number $value The new value for the Duration property
   * @return void 
   */
  function setDuration( $value )
  {
    $this->_props['Duration'] = $value;
  } 

  /**
   * Write accessor of ApplyShippingDiscount.  
   * Specifies whether a shipping discount is applied for the item when its transaction is combined into a buyer-created Combined Payment order. If not specified flag for applying shipping discount is set based on user's seller preferences on the My eBay page. (See Combined Payment.)
   * 
   * @access public 
   * @param boolean $value The new value for the ApplyShippingDiscount property
   * @return void 
   */
  function setApplyShippingDiscount( $value )
  {
    $this->_props['ApplyShippingDiscount'] = $value;
  } 

  /**
   * Write accessor of BusinessSeller.  
   * If 1 (true), indicates that the seller is a business user and intends to use listing features that are offered to business users only. This declaration is up to the seller and is not validated by eBay. Applicable for business sellers residing in Germany, Austria, or Switzerland and listing in a B2B VAT-enabled category on the eBay Germany (DE), Austria (AT), or Switzerland (CH) site only. Required and must be set to 1 (true) if RestrictedToBusiness is set to 1 (true). No effect if RestrictedToBusiness is set to (0) false. See Listing Items with Business Features and the International Item Matrix.
   * 
   * @access public 
   * @param boolean $value The new value for the BusinessSeller property
   * @return void 
   */
  function setBusinessSeller( $value )
  {
    $this->_props['BusinessSeller'] = $value;
  } 

  /**
   * Write accessor of IsAdFormat.  
   * If true, specifies that a real estate item is listed as an ad.
   * 
   * @access public 
   * @param boolean $value The new value for the IsAdFormat property
   * @return void 
   */
  function setIsAdFormat( $value )
  {
    $this->_props['IsAdFormat'] = $value;
  } 

  /**
   * Write accessor of Version.  
   * Used to specify which set of shipping/handling and tax set of tags to use. 
   * 
   * If you pass compatibility level 305, the API forces the use of Version = 2.
   * 
   * If you pass a version lower than 305 in the compatibility level header and you do not specify a value in the Version tag, 0 is used as the default version. 
   * 
   * If you pass a compatibility level lower than 305, you may set Version to 1 if your code still needs to use the obsolete IPShippingHandlingCosts, IPAdditionalHandlingCosts, IPSalesTax, and IPSalesTaxState tags. In this case, the API will convert the values in these tags to the appropriate values for the current tags: ShippingHandlingCosts, AdditionalHandlingCosts, SalesTax, and SalesTaxState. (If your application has already converted to these newer tags, set Version = 2.)
   * 
   * @access public 
   * @param number $value The new value for the Version property
   * @return void 
   */
  function setVersion( $value )
  {
    $this->_props['Version'] = $value;
  } 

  /**
   * Write accessor of ScheduleTime.  
   * Time (in GMT) that the Item is scheduled to be listed on eBay. Might result in extra fees.
   * 
   * @access public 
   * @param datetime $value The new value for the ScheduleTime property
   * @return void 
   */
  function setScheduleTime( $value )
  {
    $this->_props['ScheduleTime'] = $value;
  } 

  /**
   * Read accessor of FeesSubtitleFee. 
   * read only
   * Fee to add a subtitle to the item.
   * 
   * @access public 
   * @return money Value of the FeesSubtitleFee property
   */
  function getFeesSubtitleFee()
  {
    return $this->_props['FeesSubtitleFee'];
  } 

  /**
   * Read accessor of localIntegrationState. 
   * 0 = stored locally
   * 1 = prepared for listing
   * 2 = added, so data retrievable from eBay
   * 
   * @access public 
   * @return number Value of the localIntegrationState property
   */
  function getlocalIntegrationState()
  {
    return $this->_props['localIntegrationState'];
  } 

  /**
   * Write accessor of localIntegrationState.  
   * 0 = stored locally
   * 1 = prepared for listing
   * 2 = added, so data retrievable from eBay
   * 
   * @access public 
   * @param number $value The new value for the localIntegrationState property
   * @return void 
   */
  function setlocalIntegrationState( $value )
  {
    $this->_props['localIntegrationState'] = $value;
  } 

  /**
   * Read accessor of ShippingTermsBuyerPayBySellersFixed.
   * 
   * @access public 
   * @return boolean Value of the ShippingTermsBuyerPayBySellersFixed property
   */
  function getShippingTermsBuyerPayBySellersFixed()
  {
    return $this->_props['ShippingTermsBuyerPayBySellersFixed'];
  } 

  /**
   * Write accessor of ShippingTermsBuyerPayBySellersFixed.
   * 
   * @access public 
   * @param boolean $value The new value for the ShippingTermsBuyerPayBySellersFixed property
   * @return void 
   */
  function setShippingTermsBuyerPayBySellersFixed( $value )
  {
    $this->_props['ShippingTermsBuyerPayBySellersFixed'] = $value;
  } 

  /**
   * Read accessor of ShippingTermsBuyerPaysActual.
   * 
   * @access public 
   * @return boolean Value of the ShippingTermsBuyerPaysActual property
   */
  function getShippingTermsBuyerPaysActual()
  {
    return $this->_props['ShippingTermsBuyerPaysActual'];
  } 

  /**
   * Write accessor of ShippingTermsBuyerPaysActual.
   * 
   * @access public 
   * @param boolean $value The new value for the ShippingTermsBuyerPaysActual property
   * @return void 
   */
  function setShippingTermsBuyerPaysActual( $value )
  {
    $this->_props['ShippingTermsBuyerPaysActual'] = $value;
  } 

  /**
   * Read accessor of PropGift.
   * 
   * @access public 
   * @return boolean Value of the PropGift property
   */
  function getPropGift()
  {
    return $this->_props['PropGift'];
  } 

  /**
   * Write accessor of PropGift.
   * 
   * @access public 
   * @param boolean $value The new value for the PropGift property
   * @return void 
   */
  function setPropGift( $value )
  {
    $this->_props['PropGift'] = $value;
  } 

  /**
   * Read accessor of PropIsFixedPrice.
   * 
   * @access public 
   * @return boolean Value of the PropIsFixedPrice property
   */
  function getPropIsFixedPrice()
  {
    return $this->_props['PropIsFixedPrice'];
  } 

  /**
   * Write accessor of PropIsFixedPrice.
   * 
   * @access public 
   * @param boolean $value The new value for the PropIsFixedPrice property
   * @return void 
   */
  function setPropIsFixedPrice( $value )
  {
    $this->_props['PropIsFixedPrice'] = $value;
  } 

  /**
   * Read accessor of PropBuyItNow.
   * 
   * @access public 
   * @return boolean Value of the PropBuyItNow property
   */
  function getPropBuyItNow()
  {
    return $this->_props['PropBuyItNow'];
  } 

  /**
   * Write accessor of PropBuyItNow.
   * 
   * @access public 
   * @param boolean $value The new value for the PropBuyItNow property
   * @return void 
   */
  function setPropBuyItNow( $value )
  {
    $this->_props['PropBuyItNow'] = $value;
  } 

  /**
   * Read accessor of PropNew.
   * 
   * @access public 
   * @return boolean Value of the PropNew property
   */
  function getPropNew()
  {
    return $this->_props['PropNew'];
  } 

  /**
   * Write accessor of PropNew.
   * 
   * @access public 
   * @param boolean $value The new value for the PropNew property
   * @return void 
   */
  function setPropNew( $value )
  {
    $this->_props['PropNew'] = $value;
  } 

  /**
   * Read accessor of PropPicture.
   * 
   * @access public 
   * @return boolean Value of the PropPicture property
   */
  function getPropPicture()
  {
    return $this->_props['PropPicture'];
  } 

  /**
   * Write accessor of PropPicture.
   * 
   * @access public 
   * @param boolean $value The new value for the PropPicture property
   * @return void 
   */
  function setPropPicture( $value )
  {
    $this->_props['PropPicture'] = $value;
  } 

  /**
   * Read accessor of GiftIconURL. 
   * URL for the gallery for the item. Returned as CDATA. The data for this field will be no longer than 255 characters.
   * 
   * @access public 
   * @return string Value of the GiftIconURL property
   */
  function getGiftIconURL()
  {
    return $this->_props['GiftIconURL'];
  } 

  /**
   * Write accessor of GiftIconURL.  
   * URL for the gallery for the item. Returned as CDATA. The data for this field will be no longer than 255 characters.
   * 
   * @access public 
   * @param string $value The new value for the GiftIconURL property
   * @return void 
   */
  function setGiftIconURL( $value )
  {
    $this->_props['GiftIconURL'] = $value;
  } 

  /**
   * Read accessor of CheckoutAdjustmentAmount.
   * 
   * @access public 
   * @return money Value of the CheckoutAdjustmentAmount property
   */
  function getCheckoutAdjustmentAmount()
  {
    return $this->_props['CheckoutAdjustmentAmount'];
  } 

  /**
   * Read accessor of CheckoutConvertedAdjustmentAmount.
   * 
   * @access public 
   * @return money Value of the CheckoutConvertedAdjustmentAmount property
   */
  function getCheckoutConvertedAdjustmentAmount()
  {
    return $this->_props['CheckoutConvertedAdjustmentAmount'];
  } 

  /**
   * Read accessor of CheckoutPaymentEdit. 
   * Indicates whether the buyer edited the payment amount.
   * 
   * @access public 
   * @return boolean Value of the CheckoutPaymentEdit property
   */
  function getCheckoutPaymentEdit()
  {
    return $this->_props['CheckoutPaymentEdit'];
  } 

  /**
   * Read accessor of ProductInfoId. 
   * ProductInfo Id of the Product the Item should be listed with
   * 
   * @access public 
   * @return number Value of the ProductInfoId property
   */
  function getProductInfoId()
  {
    return $this->_props['ProductInfoId'];
  } 

  /**
   * Write accessor of ProductInfoId.  
   * ProductInfo Id of the Product the Item should be listed with
   * 
   * @access public 
   * @param number $value The new value for the ProductInfoId property
   * @return void 
   */
  function setProductInfoId( $value )
  {
    $this->_props['ProductInfoId'] = $value;
  } 

  /**
   * Read accessor of ProductInfoIncludeStockPhotoURL. 
   * set only when using AddWithProduct
   * If true, specifies that the listing should include the stock photo (if any). If you do not pass PictureURL and you set IncludeStockPhotoURL to true, the stock photo is used at the top of the View Item page and in the Item Specifics section of the listing. If you also pass PictureURL, the stock photo only appears in the Item Specifics section of the listing. Other pictures you specify appear in a separate section of the listing.
   * 
   * @access public 
   * @return boolean Value of the ProductInfoIncludeStockPhotoURL property
   */
  function getProductInfoIncludeStockPhotoURL()
  {
    return $this->_props['ProductInfoIncludeStockPhotoURL'];
  } 

  /**
   * Write accessor of ProductInfoIncludeStockPhotoURL.  
   * set only when using AddWithProduct
   * If true, specifies that the listing should include the stock photo (if any). If you do not pass PictureURL and you set IncludeStockPhotoURL to true, the stock photo is used at the top of the View Item page and in the Item Specifics section of the listing. If you also pass PictureURL, the stock photo only appears in the Item Specifics section of the listing. Other pictures you specify appear in a separate section of the listing.
   * 
   * @access public 
   * @param boolean $value The new value for the ProductInfoIncludeStockPhotoURL property
   * @return void 
   */
  function setProductInfoIncludeStockPhotoURL( $value )
  {
    $this->_props['ProductInfoIncludeStockPhotoURL'] = $value;
  } 

  /**
   * Read accessor of ProductInfoIncludePrefilledItemInformation. 
   * set only when using AddWithProduct
   * If true, specifies that the listing should include additional information about the product, such as a publisher's description or film credits. This information is hosted through the eBay site and cannot be edited.
   * 
   * @access public 
   * @return boolean Value of the ProductInfoIncludePrefilledItemInformation property
   */
  function getProductInfoIncludePrefilledItemInformation()
  {
    return $this->_props['ProductInfoIncludePrefilledItemInformation'];
  } 

  /**
   * Write accessor of ProductInfoIncludePrefilledItemInformation.  
   * set only when using AddWithProduct
   * If true, specifies that the listing should include additional information about the product, such as a publisher's description or film credits. This information is hosted through the eBay site and cannot be edited.
   * 
   * @access public 
   * @param boolean $value The new value for the ProductInfoIncludePrefilledItemInformation property
   * @return void 
   */
  function setProductInfoIncludePrefilledItemInformation( $value )
  {
    $this->_props['ProductInfoIncludePrefilledItemInformation'] = $value;
  } 

  /**
   * Read accessor of ProductInfoUseStockPhotoURLAsGallery. 
   * set only when using AddWithProduct
   * If true, IncludeStockPhotoURL must also be true, and the listing uses the stock photo as the Gallery image. If true, either pass Gallery or GalleryFeatured with a value of 1 (true), and do not pass GalleryURL.
   * 
   * If UseStockPhotoURLAsGallery is false or not specified, this logic is used: 
   * If IncludeStockPhotoURL is true, the system looks for the first URL in PictureURL to use as the Gallery image. If PictureURL is not specified, no image appears in the Gallery. 
   * If IncludeStockPhotoURL is false, use GalleryURL instead (see Gallery Note).
   * 
   * @access public 
   * @return boolean Value of the ProductInfoUseStockPhotoURLAsGallery property
   */
  function getProductInfoUseStockPhotoURLAsGallery()
  {
    return $this->_props['ProductInfoUseStockPhotoURLAsGallery'];
  } 

  /**
   * Write accessor of ProductInfoUseStockPhotoURLAsGallery.  
   * set only when using AddWithProduct
   * If true, IncludeStockPhotoURL must also be true, and the listing uses the stock photo as the Gallery image. If true, either pass Gallery or GalleryFeatured with a value of 1 (true), and do not pass GalleryURL.
   * 
   * If UseStockPhotoURLAsGallery is false or not specified, this logic is used: 
   * If IncludeStockPhotoURL is true, the system looks for the first URL in PictureURL to use as the Gallery image. If PictureURL is not specified, no image appears in the Gallery. 
   * If IncludeStockPhotoURL is false, use GalleryURL instead (see Gallery Note).
   * 
   * @access public 
   * @param boolean $value The new value for the ProductInfoUseStockPhotoURLAsGallery property
   * @return void 
   */
  function setProductInfoUseStockPhotoURLAsGallery( $value )
  {
    $this->_props['ProductInfoUseStockPhotoURLAsGallery'] = $value;
  } 

  /**
   * Read accessor of RelistLink. 
   * If true, creates a link from the old listing for the item to the new relist page, which accommodates users who might still look for the item under its old item ID. Also adds the relist ID to the old listing's record in the eBay database, which can be returned by calling GetItem for the old ItemId. If your application creates the listing page for the user, you need to add the relist link option to your application for your users.
   * 
   * @access public 
   * @return boolean Value of the RelistLink property
   */
  function getRelistLink()
  {
    return $this->_props['RelistLink'];
  } 

  /**
   * Write accessor of RelistLink.  
   * If true, creates a link from the old listing for the item to the new relist page, which accommodates users who might still look for the item under its old item ID. Also adds the relist ID to the old listing's record in the eBay database, which can be returned by calling GetItem for the old ItemId. If your application creates the listing page for the user, you need to add the relist link option to your application for your users.
   * 
   * @access public 
   * @param boolean $value The new value for the RelistLink property
   * @return void 
   */
  function setRelistLink( $value )
  {
    $this->_props['RelistLink'] = $value;
  } 

  /**
   * Read accessor of StoreCategory. 
   * Custom categories for subdividing the items within an eBay Store. Store owners can create up to 19 custom categories for their stores. (One additional Store category cannot be customized and retains the value of "Other") If specified, must be an integer between 0 and 20. 0=Not an eBay Store item 1=Other 2=Category 1 3=Category 2 ... 19=Category 18 20=Category 19If you specify an invalid value (e.g., 21), the system resets the value to 1 (Other).
   * 
   * @access public 
   * @return number Value of the StoreCategory property
   */
  function getStoreCategory()
  {
    return $this->_props['StoreCategory'];
  } 

  /**
   * Write accessor of StoreCategory.  
   * Custom categories for subdividing the items within an eBay Store. Store owners can create up to 19 custom categories for their stores. (One additional Store category cannot be customized and retains the value of "Other") If specified, must be an integer between 0 and 20. 0=Not an eBay Store item 1=Other 2=Category 1 3=Category 2 ... 19=Category 18 20=Category 19If you specify an invalid value (e.g., 21), the system resets the value to 1 (Other).
   * 
   * @access public 
   * @param number $value The new value for the StoreCategory property
   * @return void 
   */
  function setStoreCategory( $value )
  {
    $this->_props['StoreCategory'] = $value;
  } 

  /**
   * Read accessor of CheckoutRedirect. 
   * undocumented
   * 
   * @access public 
   * @return boolean Value of the CheckoutRedirect property
   */
  function getCheckoutRedirect()
  {
    return $this->_props['CheckoutRedirect'];
  } 

  /**
   * Read accessor of BiddingPropertiesMaxBid. 
   * Indicates the maximum amount the user has agreed to pay for the item when the user last submitted a bid. Under conditions where this value would be N/A on the eBay site, this element is not returned at all.
   * 
   * @access public 
   * @return money Value of the BiddingPropertiesMaxBid property
   */
  function getBiddingPropertiesMaxBid()
  {
    return $this->_props['BiddingPropertiesMaxBid'];
  } 

  /**
   * Read accessor of BiddingPropertiesConvertedMaxBid. 
   * Converted value of the value in MaxBid, in the currency indicated by SiteCurrency. This value must be refreshed every 24 hours to pick up the current conversion rates. Under conditions where this value would be N/A on the eBay site, this element is not returned at all.
   * 
   * @access public 
   * @return money Value of the BiddingPropertiesConvertedMaxBid property
   */
  function getBiddingPropertiesConvertedMaxBid()
  {
    return $this->_props['BiddingPropertiesConvertedMaxBid'];
  } 

  /**
   * Read accessor of BiddingPropertiesQuantityBid. 
   * Number of items from the listing the user agreed to purchase with a bid. For single-item listings, always 1. For multi-item listings, will be between 1 and the number of items offered in the auction.
   * 
   * @access public 
   * @return number Value of the BiddingPropertiesQuantityBid property
   */
  function getBiddingPropertiesQuantityBid()
  {
    return $this->_props['BiddingPropertiesQuantityBid'];
  } 

  /**
   * Read accessor of BiddingPropertiesQuantityWon. 
   * Number of items the user stands to win if the user is a current winning bidder. For Dutch auctions, this number may be less than that returned in QuantityBid as the lowest winning bidder might not win the number of items the user has bid on. (See Dutch Auction Logic.) For all other auction formats, this number will be the same as that returned in QuantityBid.
   * 
   * @access public 
   * @return number Value of the BiddingPropertiesQuantityWon property
   */
  function getBiddingPropertiesQuantityWon()
  {
    return $this->_props['BiddingPropertiesQuantityWon'];
  } 

  /**
   * Read accessor of BiddingPropertiesWinning. 
   * Indicates whether the user is the current high bidder in a currently active listing. If true (1), the user is the high bidder. Otherwise returns a value of false (0).
   * 
   * @access public 
   * @return boolean Value of the BiddingPropertiesWinning property
   */
  function getBiddingPropertiesWinning()
  {
    return $this->_props['BiddingPropertiesWinning'];
  } 

  /**
   * Write accessor of ExternalProductIdentifierId.  
   * Unique identifier for the product. Can be an ISBN value, UPC value, or an eBay catalog product ID (dictated by the type XML attribute below). If Category and Category2 are both catalog-enabled, this ID will correspond to Category (not Category2). Max length 10 for ISBN, 12 for UPC, and 4000 for ProductID. 
   * This attribute regards to an AddItem with Pre-filled Item Information, so listings with UPC/ISBN/ProductId only
   * 
   * @access public 
   * @param string $value The new value for the ExternalProductIdentifierId property
   * @return void 
   */
  function setExternalProductIdentifierId( $value )
  {
    $this->_props['ExternalProductIdentifierId'] = $value;
  } 

  /**
   * Write accessor of ExternalProductIdentifierType.  
   * The nature of the identifier being passed in. Possible values:
   * - ISBN = The value passed in id is an ISBN value.
   * - UPC = The value passed in id is a UPC value.
   * - ProductID = The value passed in id is an eBay catalog product ID. 
   * This attribute regards to an AddItem with Pre-filled Item Information, so listings with UPC/ISBN/ProductId only
   * 
   * @access public 
   * @param string $value The new value for the ExternalProductIdentifierType property
   * @return void 
   */
  function setExternalProductIdentifierType( $value )
  {
    $this->_props['ExternalProductIdentifierType'] = $value;
  } 

  /**
   * Write accessor of ExternalProductIdentifierReturnSearchResultWithError.  
   * Instructions specifying what eBay should do if more than one product matches the ISBN or UPC value. If true (1), returnd an error and all matching product IDs. If false (0), return an error but does not return the matching product IDs. 
   * 
   * This attribute regards to an AddItem with Pre-filled Item Information, so listings with UPC/ISBN/ProductId only
   * 
   * @access public 
   * @param boolean $value The new value for the ExternalProductIdentifierReturnSearchResultWithError property
   * @return void 
   */
  function setExternalProductIdentifierReturnSearchResultWithError( $value )
  {
    $this->_props['ExternalProductIdentifierReturnSearchResultWithError'] = $value;
  } 

  /**
   * Read accessor to the ShippingServiceOption.
   * 
   * @access public 
   * @param integer $index The index of the value to return
   * @return Ebay _ShippingServiceOption Value of the ShippingServiceOption property
   */
  function getShippingServiceOption( $index )
  {
    return $this->_props['ShippingServiceOption'][$index];
  } 

  /**
   * Return the amount of ShippingServiceOption actually declared
   * 
   * @access public 
   * @return Ebay _ShippingServiceOption Value of the ShippingServiceOption property
   */
  function getShippingServiceOptionCount()
  {
    return count( $this->_props['ShippingServiceOption'] );
  } 
  /**
   * Returns a copy of the ShippingServiceOption array
   * 
   * @access public 
   * @return array of Ebay_ShippingServiceOption
   */
  function getShippingServiceOptionArray()
  {
    return $this->_props['ShippingServiceOption'];
  } 

  /**
   * Write accessor to the ShippingServiceOption.
   * 
   * @access public 
   * @param Ebay $ _ShippingServiceOption $value The new value for the ShippingServiceOption property
   * @param integer $index The index of the value to update. if $index = -1, the value is added to the end of list.
   * @return void 
   */
  function setShippingServiceOption( $value, $index = -1 )
  {
    if ( -1 == $index )
    {
      $index = count( $this->_props['ShippingServiceOption'] );
    } 
    $this->_props['ShippingServiceOption'][$index] = $value;
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['ApplicationData'] = EBAY_NOTHING;
    $this->_props['AttributeSet'] = array();

    $this->_props['BidCount'] = EBAY_NOTHING;
    $this->_props['BidIncrement'] = EBAY_NOTHING;
    $this->_props['BuyItNowPrice'] = EBAY_NOTHING;
    $this->_props['BuyerProtection'] = EBAY_NOTHING;
    $this->_props['Category2Id'] = EBAY_NOTHING;
    $this->_props['CategoryId'] = EBAY_NOTHING;
    $this->_props['CheckoutAdditionalShippingCosts'] = EBAY_NOTHING;
    $this->_props['CheckoutBuyerEditTotal'] = EBAY_NOTHING;
    $this->_props['CheckoutConvertedAmountPaid'] = EBAY_NOTHING;
    $this->_props['CheckoutConvertedTransactionPrice'] = EBAY_NOTHING;
    $this->_props['CheckoutInstructions'] = EBAY_NOTHING;
    $this->_props['CheckoutInsuranceFee'] = EBAY_NOTHING;
    $this->_props['CheckoutInsuranceOption'] = EBAY_NOTHING;
    $this->_props['CheckoutInsuranceTotal'] = EBAY_NOTHING;
    $this->_props['CheckoutInsuranceWanted'] = EBAY_NOTHING;
    $this->_props['CheckoutSalesTaxAmount'] = EBAY_NOTHING;
    $this->_props['CheckoutSalesTaxPercent'] = EBAY_NOTHING;
    $this->_props['CheckoutSalesTaxState'] = EBAY_NOTHING;
    $this->_props['CheckoutShippingHandlingCosts'] = EBAY_NOTHING;
    $this->_props['CheckoutShippingInTax'] = EBAY_NOTHING;
    $this->_props['CheckoutSpecified'] = EBAY_NOTHING;
    $this->_props['CheckoutAllowPaymentEdit'] = EBAY_NOTHING;
    $this->_props['CheckoutPackagingHandlingCosts'] = EBAY_NOTHING;
    $this->_props['CheckoutShipFromZipCode'] = EBAY_NOTHING;
    $this->_props['CheckoutShippingIrregular'] = EBAY_NOTHING;
    $this->_props['CheckoutShippingPackage'] = EBAY_NOTHING;
    $this->_props['CheckoutShippingService'] = EBAY_NOTHING;
    $this->_props['CheckoutShippingType'] = EBAY_NOTHING;
    $this->_props['CheckoutWeightMajor'] = EBAY_NOTHING;
    $this->_props['CheckoutWeightMinor'] = EBAY_NOTHING;
    $this->_props['CheckoutWeightUnit'] = EBAY_NOTHING;
    $this->_props['ConvertedBuyItNowPrice'] = EBAY_NOTHING;
    $this->_props['CategoryFullName'] = EBAY_NOTHING;
    $this->_props['Category2FullName'] = EBAY_NOTHING;
    $this->_props['AutoPay'] = EBAY_NOTHING;
    $this->_props['CharityListing'] = EBAY_NOTHING;
    $this->_props['CharityName'] = EBAY_NOTHING;
    $this->_props['CharityNumber'] = EBAY_NOTHING;
    $this->_props['CharityDonationPercent'] = EBAY_NOTHING;
    $this->_props['ConvertedPrice'] = EBAY_NOTHING;
    $this->_props['ConvertedStartPrice'] = EBAY_NOTHING;
    $this->_props['Counter'] = EBAY_NOTHING;
    $this->_props['Country'] = EBAY_NOTHING;
    $this->_props['Currency'] = EBAY_NOTHING;
    $this->_props['CurrencyId'] = EBAY_NOTHING;
    $this->_props['CurrentPrice'] = EBAY_NOTHING;
    $this->_props['Description'] = EBAY_NOTHING;
    $this->_props['DescriptionLen'] = EBAY_NOTHING;
    $this->_props['DomainKey'] = EBAY_NOTHING;
    $this->_props['EndTime'] = EBAY_NOTHING;
    $this->_props['GalleryURL'] = EBAY_NOTHING;
    $this->_props['GiftIcon'] = EBAY_NOTHING;
    $this->_props['GiftExpressShipping'] = EBAY_NOTHING;
    $this->_props['GiftShipToRecipient'] = EBAY_NOTHING;
    $this->_props['GiftWrap'] = EBAY_NOTHING;
    $this->_props['GoodTillCanceled'] = EBAY_NOTHING;
    $this->_props['HighBidder'] = EBAY_NOTHING;
    $this->_props['Id'] = EBAY_NOTHING;
    $this->_props['PropAdult'] = EBAY_NOTHING;
    $this->_props['PropBindingAuction'] = EBAY_NOTHING;
    $this->_props['PropBuyItNowAdded'] = EBAY_NOTHING;
    $this->_props['PropBuyItNowLowered'] = EBAY_NOTHING;
    $this->_props['PropBoldTitle'] = EBAY_NOTHING;
    $this->_props['PropCheckoutEnabled'] = EBAY_NOTHING;
    $this->_props['PropFeatured'] = EBAY_NOTHING;
    $this->_props['PropGallery'] = EBAY_NOTHING;
    $this->_props['PropGalleryFeatured'] = EBAY_NOTHING;
    $this->_props['PropHighlight'] = EBAY_NOTHING;
    $this->_props['PropPrivate'] = EBAY_NOTHING;
    $this->_props['PropReserve'] = EBAY_NOTHING;
    $this->_props['PropReserveLowered'] = EBAY_NOTHING;
    $this->_props['PropReserveMet'] = EBAY_NOTHING;
    $this->_props['PropReserveRemoved'] = EBAY_NOTHING;
    $this->_props['PropSuperFeatured'] = EBAY_NOTHING;
    $this->_props['PropType'] = EBAY_NOTHING;
    $this->_props['ItemRevised'] = EBAY_NOTHING;
    $this->_props['LeadCount'] = EBAY_NOTHING;
    $this->_props['ListDesignLayoutId'] = EBAY_NOTHING;
    $this->_props['ListDesignThemeId'] = EBAY_NOTHING;
    $this->_props['Location'] = EBAY_NOTHING;
    $this->_props['MinimumToBid'] = EBAY_NOTHING;
    $this->_props['OriginalItemId'] = EBAY_NOTHING;
    $this->_props['PaymentTermsAmEx'] = EBAY_NOTHING;
    $this->_props['PaymentTermsCashOnPickupAccepted'] = EBAY_NOTHING;
    $this->_props['PaymentTermsCCAccepted'] = EBAY_NOTHING;
    $this->_props['PaymentTermsCOD'] = EBAY_NOTHING;
    $this->_props['PaymentTermsDiscover'] = EBAY_NOTHING;
    $this->_props['PaymentTermsEscrow'] = EBAY_NOTHING;
    $this->_props['PaymentTermsEscrowBySeller'] = EBAY_NOTHING;
    $this->_props['PaymentTermsMOCashiers'] = EBAY_NOTHING;
    $this->_props['PaymentTermsMoneyXferAccepted'] = EBAY_NOTHING;
    $this->_props['PaymentTermsMoneyXferAcceptedinCheckout'] = EBAY_NOTHING;
    $this->_props['PaymentTermsOther'] = EBAY_NOTHING;
    $this->_props['PaymentTermsOtherPaymentsOnline'] = EBAY_NOTHING;
    $this->_props['PaymentTermsPayPalAccepted'] = EBAY_NOTHING;
    $this->_props['PaymentTermsPayPalEmailAddress'] = EBAY_NOTHING;
    $this->_props['PaymentTermsPersonalCheck'] = EBAY_NOTHING;
    $this->_props['PaymentTermsSeeDescription'] = EBAY_NOTHING;
    $this->_props['PaymentTermsVisaMaster'] = EBAY_NOTHING;
    $this->_props['PhotoCount'] = EBAY_NOTHING;
    $this->_props['PhotoDisplayType'] = EBAY_NOTHING;
    $this->_props['PictureURL'] = EBAY_NOTHING;
    $this->_props['Quantity'] = EBAY_NOTHING;
    $this->_props['QuantitySold'] = EBAY_NOTHING;
    $this->_props['Region'] = EBAY_NOTHING;
    $this->_props['RelistId'] = EBAY_NOTHING;
    $this->_props['ReservePrice'] = EBAY_NOTHING;
    $this->_props['SecondChanceEligible'] = EBAY_NOTHING;
    $this->_props['Seller'] = EBAY_NOTHING;
    $this->_props['ShippingOption'] = EBAY_NOTHING;
    $this->_props['ShippingRegionsAfrica'] = EBAY_NOTHING;
    $this->_props['ShippingRegionsAsia'] = EBAY_NOTHING;
    $this->_props['ShippingRegionsCaribbean'] = EBAY_NOTHING;
    $this->_props['ShippingRegionsEurope'] = EBAY_NOTHING;
    $this->_props['ShippingRegionsLatinAmerica'] = EBAY_NOTHING;
    $this->_props['ShippingRegionsMiddleEast'] = EBAY_NOTHING;
    $this->_props['ShippingRegionsNorthAmerica'] = EBAY_NOTHING;
    $this->_props['ShippingRegionsOceania'] = EBAY_NOTHING;
    $this->_props['ShippingRegionsSouthAmerica'] = EBAY_NOTHING;
    $this->_props['ShippingTermsSellerPays'] = EBAY_NOTHING;
    $this->_props['SiteCurrency'] = EBAY_NOTHING;
    $this->_props['SiteId'] = EBAY_NOTHING;
    $this->_props['StartPrice'] = EBAY_NOTHING;
    $this->_props['StartTime'] = EBAY_NOTHING;
    $this->_props['StorefrontInfoDepartmentNumber'] = EBAY_NOTHING;
    $this->_props['StorefrontInfoStoreLocation'] = EBAY_NOTHING;
    $this->_props['StorefrontItem'] = EBAY_NOTHING;
    $this->_props['TimeLeftDays'] = EBAY_NOTHING;
    $this->_props['TimeLeftHours'] = EBAY_NOTHING;
    $this->_props['TimeLeftMinutes'] = EBAY_NOTHING;
    $this->_props['TimeLeftSeconds'] = EBAY_NOTHING;
    $this->_props['Title'] = EBAY_NOTHING;
    $this->_props['TitleBarImage'] = EBAY_NOTHING;
    $this->_props['UUID'] = EBAY_NOTHING;
    $this->_props['Zip'] = EBAY_NOTHING;
    $this->_props['FeesAuctionLengthFee'] = EBAY_NOTHING;
    $this->_props['FeesBoldFee'] = EBAY_NOTHING;
    $this->_props['FeesBuyItNowFee'] = EBAY_NOTHING;
    $this->_props['FeesCategoryFeaturedFee'] = EBAY_NOTHING;
    $this->_props['FeesCurrencyId'] = EBAY_NOTHING;
    $this->_props['FeesFeaturedFee'] = EBAY_NOTHING;
    $this->_props['FeesFeaturedGalleryFee'] = EBAY_NOTHING;
    $this->_props['FeesFixedPriceDurationFee'] = EBAY_NOTHING;
    $this->_props['FeesGalleryFee'] = EBAY_NOTHING;
    $this->_props['FeesGiftIconFee'] = EBAY_NOTHING;
    $this->_props['FeesHighLightFee'] = EBAY_NOTHING;
    $this->_props['FeesInsertionFee'] = EBAY_NOTHING;
    $this->_props['FeesListingDesignerFee'] = EBAY_NOTHING;
    $this->_props['FeesListingFee'] = EBAY_NOTHING;
    $this->_props['FeesPhotoDisplayFee'] = EBAY_NOTHING;
    $this->_props['FeesPhotoFee'] = EBAY_NOTHING;
    $this->_props['FeesReserveFee'] = EBAY_NOTHING;
    $this->_props['FeesSchedulingFee'] = EBAY_NOTHING;
    $this->_props['Charity'] = EBAY_NOTHING;
    $this->_props['Link'] = EBAY_NOTHING;
    $this->_props['LocalizedCurrentPrice'] = EBAY_NOTHING;
    $this->_props['PropRestrictedToBusiness'] = EBAY_NOTHING;
    $this->_props['PropVATPercent'] = EBAY_NOTHING;
    $this->_props['undocMobileInspection'] = EBAY_NOTHING;
    $this->_props['undocIsHomesDirect'] = EBAY_NOTHING;
    $this->_props['undocIsSecurePay'] = EBAY_NOTHING;
    $this->_props['SubtitleText'] = EBAY_NOTHING;
    $this->_props['Duration'] = EBAY_NOTHING;
    $this->_props['ApplyShippingDiscount'] = EBAY_NOTHING;
    $this->_props['BusinessSeller'] = EBAY_NOTHING;
    $this->_props['IsAdFormat'] = EBAY_NOTHING;
    $this->_props['Version'] = EBAY_NOTHING;
    $this->_props['ScheduleTime'] = EBAY_NOTHING;
    $this->_props['FeesSubtitleFee'] = EBAY_NOTHING;
    $this->_props['localIntegrationState'] = EBAY_NOTHING;
    $this->_props['ShippingTermsBuyerPayBySellersFixed'] = EBAY_NOTHING;
    $this->_props['ShippingTermsBuyerPaysActual'] = EBAY_NOTHING;
    $this->_props['PropGift'] = EBAY_NOTHING;
    $this->_props['PropIsFixedPrice'] = EBAY_NOTHING;
    $this->_props['PropBuyItNow'] = EBAY_NOTHING;
    $this->_props['PropNew'] = EBAY_NOTHING;
    $this->_props['PropPicture'] = EBAY_NOTHING;
    $this->_props['GiftIconURL'] = EBAY_NOTHING;
    $this->_props['CheckoutAdjustmentAmount'] = EBAY_NOTHING;
    $this->_props['CheckoutConvertedAdjustmentAmount'] = EBAY_NOTHING;
    $this->_props['CheckoutPaymentEdit'] = EBAY_NOTHING;
    $this->_props['ProductInfoId'] = EBAY_NOTHING;
    $this->_props['ProductInfoIncludeStockPhotoURL'] = EBAY_NOTHING;
    $this->_props['ProductInfoIncludePrefilledItemInformation'] = EBAY_NOTHING;
    $this->_props['ProductInfoUseStockPhotoURLAsGallery'] = EBAY_NOTHING;
    $this->_props['RelistLink'] = EBAY_NOTHING;
    $this->_props['StoreCategory'] = EBAY_NOTHING;
    $this->_props['CheckoutRedirect'] = EBAY_NOTHING;
    $this->_props['BiddingPropertiesMaxBid'] = EBAY_NOTHING;
    $this->_props['BiddingPropertiesConvertedMaxBid'] = EBAY_NOTHING;
    $this->_props['BiddingPropertiesQuantityBid'] = EBAY_NOTHING;
    $this->_props['BiddingPropertiesQuantityWon'] = EBAY_NOTHING;
    $this->_props['BiddingPropertiesWinning'] = EBAY_NOTHING;
    $this->_props['ExternalProductIdentifierId'] = EBAY_NOTHING;
    $this->_props['ExternalProductIdentifierType'] = EBAY_NOTHING;
    $this->_props['ExternalProductIdentifierReturnSearchResultWithError'] = EBAY_NOTHING;
    $this->_props['ShippingServiceOption'] = array();
  } 

  /**
   * 
   * @access public 
   * @param Ebay $ _Session $session
   * @return void 
   */
  function Ebay_Item( $session = null )
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_Item::_init();
    if ( $session )
    {
      $this->_session = $session;
    } 
  } 

  /**
   * 
   * @access public 
   * @param boolean $withProduct switches listing with pre-filled information on
   * @return Ebay _Result
   */
  function Add( $withProduct = false )
  {
    if ( !$this->_apiCaller )
      $this->_apiCaller = new Ebay_ApiCaller( $this->_session );

    $ret = new Ebay_Result();

    $params = array();

    $this->_setupParamsAddOrVerify( $params );

    $ret = $this->_apiCaller->call( 'AddItem', $params, 0 );
    if ( $ret->isGood() )
    {
      $resultFragment = $ret->getXmlStructResultFragment( 'Item' );
      $this->InitFromDataStruct( $resultFragment );
    } 

    return $ret;
  } 

  /**
   * 
   * @access public 
   * @return Ebay _Result
   */
  function AddWithProduct()
  {
    return $this->Add( true );
  } 

  /**
   * 
   * @access public 
   * @param string $additionalDescription 
   * @return Ebay _Result
   */
  function AddToDescription( $additionalDescription )
  {
    if ( !$this->_apiCaller )
      $this->_apiCaller = new Ebay_ApiCaller( $this->_session );

    $res = new Ebay_Result();

    $params = array();
    $params['ItemId'] = $this->getId();
    $this->addToParamsIfSet( $params, 'Description', 'cdata' );

    $res = $this->_apiCaller->call( 'AddToItemDescription', $params, 0 );
    if ( $res->isGood() )
    {
      $statusData = $res->getXmlStructTagContent( 'Status' );
      $res->addError( EBAY_ERR_SUCCESS, "AddToItemDescription status : " . $statusData, EBAY_ERR_SUCCESS );
    } 

    return $res;
  } 

  /**
   * 
   * @access public 
   * @param boolean $iwthProduct switches retrieval of Pre-filled Item Information on. Actually the call will be made to GetItemWithProduct instead of GetItem. Be sure to retrieve only data from items which had been listed with Add and withProduct switched on.
   * @return Ebay _Result
   */
  function Verify( $iwthProduct = false )
  {
    if ( !$this->_apiCaller )
      $this->_apiCaller = new Ebay_ApiCaller( $this->_session );

    $res = new Ebay_Result();

    $params = array();

    $this->_setupParamsAddOrVerify( $params );
    $params['SiteId'] = $this->_session->getSiteId();

    $res = $this->_apiCaller->call( 'VerifyAddItem', $params, 0 );
    if ( $res->isGood() )
    {
      $resultFragment = $res->getXmlStructResultFragment( 'Item' );
      $this->InitFromDataStruct( $resultFragment );

      $messageData = $res->getXmlStructTagContent( 'Message' );
      $res->addError( EBAY_ERR_SUCCESS, "VerifyAddItem message : " . $messageData, EBAY_ERR_SUCCESS );
    } 

    return $res;
  } 

  /**
   * 
   * @access public 
   * @return Ebay _Result
   */
  function VerifyWithProduct()
  {
    return $this->Verify( true );
  } 

  /**
   * 
   * @access public 
   * @param boolean $withProduct switches listing with pre-filled information on
   * @return Ebay _Result
   */
  function Relist( $withProduct = false )
  {
    if ( !$this->_apiCaller )
      $this->_apiCaller = new Ebay_ApiCaller( $this->_session );

    $res = new Ebay_Result();

    $params = array();
    $this->_setupParamsAddOrVerify( $params );
    $params['ItemId'] = $this->getId();

    $res = $this->_apiCaller->call( 'RelistItem', $params, 0 );
    if ( $res->isGood() )
    {
      $resultFragment = $res->getXmlStructResultFragment( 'Item' );
      $this->InitFromDataStruct( $resultFragment );
    } 

    return $res;
  } 

  /**
   * 
   * @access public 
   * @return Ebay _Result
   */
  function RelistWithProduct()
  {
    return $this->Relist( true );
  } 

  /**
   * 
   * @access public 
   * @param number $detailLevel 
   * @param boolean $withProduct switches retrieval of Pre-filled Item Information on. Actually the call will be made to GetItemWithProduct instead of GetItem. Be sure to retrieve only data from items which had been listed with Add and withProduct switched on.
   * @return Ebay _Result
   */
  function Retrieve( $detailLevel, $withProduct = false )
  {
    if ( !$this->_apiCaller )
      $this->_apiCaller = new Ebay_ApiCaller( $this->_session );

    $params = array( 'Id' => $this->getId() );
    $res = $this->_apiCaller->call( 'GetItem', $params, $detailLevel );
    if ( $res->isGood() )
    {
      $resultFragment = $res->getXmlStructResultFragment( 'Item' );
      $this->InitFromDataStruct( $resultFragment );
    } 

    return $res;
  } 

  /**
   * 
   * @access public 
   * @param number $detailLevel 
   * @param boolean $withProduct switches retrieval of Pre-filled Item Information on. Actually the call will be made to GetItemWithProduct instead of GetItem. Be sure to retrieve only data from items which had been listed with Add and withProduct switched on.
   * @return Ebay _Result
   */
  function RetrieveWithProduct( $detailLevel, $withProduct = false )
  {
    return $this->Retrieve( $detailLevel, true );
  } 

  /**
   * 
   * @access public 
   * @param number $endCode Seller's reason for ending the listing early. EndCode is required if the seller ended the listing early and the Item did not successfully sell. Possible values are:
   * 1 = The item was lost or broken.
   * 2 = The item is no longer available for sale.
   * 3 = The Minimum Bid or Reserve Price is incorrect.
   * 4 = The listing contained an error (not in Minimum Bid or Reserve Price).
   * @return Ebay _Result
   */
  function End( $endCode = null )
  {
    if ( !$this->_apiCaller )
      $this->_apiCaller = new Ebay_ApiCaller( $this->_session );

    $res = new Ebay_Result();

    $params = array();
    if ( $endCode != null )
    {
      $params['EndCode'] = $endCode;
    } 
    $params['ItemId'] = $this->getId();

    $res = $this->_apiCaller->call( 'EndItem', $params, 0 );
    if ( $res->isGood() )
    { 
      // TODO : rework this part
      // we need some extra return data structure for the
      // single data
      $data = $res->getXmlStructTagContent( 'EndTime' );
      $this->_setProp( 'EndTime', $data );
      $res->setSingleValue( $data );
    } 

    return $res;
  } 

  /**
   * 
   * @access public 
   * @param array $reviseAttributes this is an array of attribute-name which should be revised. Set the array to all attributes which should be placed into the param-array. The method will only add attributes which are set AND in the reviseAttributes-array.
   * @param boolean $withProduct switches listing with pre-filled information on
   * @return Ebay _Result
   */
  function Revise( $reviseAttributes, $withProduct = false )
  {
    if ( !$this->_apiCaller )
      $this->_apiCaller = new Ebay_ApiCaller( $this->_session );

    $res = new Ebay_Result();

    $params = array();
    $this->_setupParamsAddOrVerify( $params, $reviseAttributes );
    $params['ItemId'] = $this->getId();

    $res = $this->_apiCaller->call( 'ReviseItem', $params, 0 );
    if ( $res->isGood() )
    {
      $resultFragment = $res->getXmlStructResultFragment( 'Item' );
      $this->InitFromDataStruct( $resultFragment );
    } 

    return $res;
  } 

  /**
   * 
   * @access public 
   * @param array $reviseAttributes this is an array of attribute-name which should be revised. Set the array to all attributes which should be placed into the param-array. The method will only add attributes which are set AND in the reviseAttributes-array.
   * @return Ebay _Result
   */
  function ReviseWithProduct( $reviseAttributes )
  {
    return $this->Revise( $reviseAttributes, true );
  } 

  /**
   * Creates a UUID for the Item and sets the attribute accordently. The Algorithm differs from OS to OS. If you have network-card the ID is created based on the MAC-adress, plus a random value (time, etc.).
   * 
   * Actual only the following OSes are supported :
   * 
   * - Microsoft Windows (W2K, WINNT, WIN SRV 2003)
   * - Mac OS X
   * - Linux
   * 
   * Actual implementation is very basic, just getting a value. Should be adapted to real use of mac-adress and real random data.
   * 
   * @access public 
   * @return void 
   */
  function AssignUUID()
  {
    srand( ( double )microtime() * 1000000 );
    $r = rand ;
    $u = uniqid( getmypid() . $r . ( double )microtime() * 1000000, 1 );
    $uuid = md5 ( $u );

    $this->_props['UUID'] = $uuid;
    return true;
  } 

  /**
   * 
   * @access public 
   * @param array $arrData 
   * @return boolean 
   */
  function InitFromDataStruct( $arrData )
  {
    $attribute = null;
    $user = null; 
    // recordTillClose is flag, when true the tag-information
    // will be recorded to the array recordedData and the foreach will continue
    // when a closing tag is encountered the we will fall in the switch
    // the case for the tag is responsible to proceed with the recorded data
    // e.g. passing it down to a sub-object (see Seller, HighBidder, Attribute)
    $recordTillClose = false;
    $recordedData = array();
    $waitForClosingTagName = '';

    foreach ( $arrData as $data )
    {
      $tag = $data['tag'];
      $type = $data['type'];

      if ( $recordTillClose == true )
      {
        if ( $type != 'close' )
        {
          $recordedData[] = $data;
          continue;
        } 
        else
        {
          if ( $waitForClosingTagName != $tag )
          {
            $recordedData[] = $data;
            continue;
          } 
          else
          {
            $recordTillClose = false;
            $waitForClosingTagName = '';
          } 
        } 
      } 

      switch ( $tag )
      {
        default: 
          // print_r( "dangling tag (item) : " . $tag . "<br>\r\n" );
          break; 
        // deprecate
        case 'Charity':
        case 'CharityItem':
        case 'BillPointRegistered':
        case 'InsuranceFeePerItem':
          break; 
        // undocumented
        case 'IsHomesDirect':
        case 'IsSecurePay':
        case 'MobileInspection':
          $this->_setProp( "undoc" . $tag, $data['value'] );
          break;
        case 'Type':
          $this->_setProp( "PropType", $data['value'] );
          break;

        case 'CategoryId': 
        // all directly maped data
        case 'ApplicationData':
        case 'AutoPay':
        case 'BidCount':
        case 'BidIncrement':
        case 'BuyerProtection':
        case 'CharityListing':
        case 'ConvertedBuyItNowPrice':
        case 'ConvertedPrice':
        case 'ConvertedStartPrice':
        case 'Counter':
        case 'Country':
        case 'Currency':
        case 'CurrencyId':
        case 'CurrentPrice':
        case 'Description':
        case 'DescriptionLen':
        case 'DomainKey':
        case 'EndTime':
        case 'GiftIcon':
        case 'GoodTillCanceled':
        case 'Id':
        case 'ItemRevised':
        case 'LeadCount':
        case 'Location':
        case 'MinimumToBid':
        case 'OriginalItemId':
        case 'PhotoCount':
        case 'PhotoDisplayType':
        case 'PictureURL':
        case 'Quantity':
        case 'QuantitySold':
        case 'Region':
        case 'RelistId':
        case 'ReservePrice':
        case 'SecondChanceEligible':
        case 'ShippingOption':
        case 'SiteCurrency':
        case 'SiteId':
        case 'StartPrice':
        case 'StartTime':
        case 'SubtitleText':
        case 'Title':
        case 'TitleBarImage':
        case 'UUID':
        case 'Zip':
        case 'Link':
        case 'LocalizedCurrentPrice':
        case 'GalleryURL':
        case 'GiftIconURL':
          $this->_setProp( $tag, $data['value'] );
          break; 
        // Price and ItemId are used in GetItemTransactions
        // we remap to the common names
        case 'Price':
          $this->_setProp( 'CurrentPrice', $data['value'] );
          break;
        case 'ItemId':
          $this->_setProp( 'Id', $data['value'] );
          break; 
        // we might better go and save the value only once !
        // most other calls return 'BuyItNowPrice'
        case 'BuyItNowPrice': 
        // BINPrice -> provided in GetSearchResult
        // acutally not documented but includs CurrencySign and Price
        case 'BINPrice':
          $this->_setProp( 'BuyItNowPrice', $data['value'] );
          break; 
        // eBay Store Information
        case 'StorefrontItem':
          $this->_setProp( $tag, $data['value'] );
          break;
        case 'StorefrontInfo':
          break;
        case 'DepartmentNumber':
        case 'StoreLocation':
          $this->_setProp( "StorefrontInfo" . $tag, $data['value'] );
          break;
        case 'MerchandisingRules': 
          // print_r( "eBay Store Information (container) " . $tag . " currently not supported<br>\r\n" );
          // deprecated
          break;

        case 'TimeLeft':
          break;

        case 'Days':
        case 'Hours':
        case 'Minutes':
        case 'Seconds':
          $this->_setProp( "TimeLeft" . $tag, $data['value'] );
          break;

        case 'Fees':
          break;
        case 'AuctionLengthFee':
        case 'BoldFee':
        case 'BuyItNowFee':
        case 'CategoryFeaturedFee':
        case 'CurrencyId':
        case 'FeaturedFee':
        case 'FeaturedGalleryFee':
        case 'FixedPriceDurationFee':
        case 'GalleryFee':
        case 'GiftIconFee':
        case 'HighLightFee':
        case 'InsertionFee':
        case 'ListingDesignerFee':
        case 'ListingFee':
        case 'PhotoDisplayFee':
        case 'PhotoFee':
        case 'ReserveFee':
        case 'SubtitleFee':
        case 'SchedulingFee':
          $this->_setProp( "Fees" . $tag, $data['value'] );
          break;

        case 'ShippingTerms':
          break; 
        // I think these 2 will deprecate
        case 'BuyerPayBySellersFixed':
        case 'BuyerPaysActual':
        case 'SellerPays':
          $this->_setProp( "ShippingTerms" . $tag, $data['value'] );
          break;

        case 'ShippingRegions':
          break;
        case 'Africa':
        case 'Asia':
        case 'Caribbean':
        case 'Europe':
        case 'LatinAmerica':
        case 'MiddleEast':
        case 'NorthAmerica':
        case 'Oceania':
        case 'SouthAmerica':
          $this->_setProp( "ShippingRegions" . $tag, $data['value'] );
          break; 
        // special handling if Email and UserId are
        // returned directly (GetSellerList)
        case 'HighBidderUserId':
          $user = $this->getHighBidder();
          if ( $user == EBAY_NOTHING )
          {
            $user = new Ebay_Buyer();
          } 
          $user->_setProp( 'UserId', $data['value'] );
          $this->_setProp( 'HighBidder', $user );
          break;

        case 'HighBidderEmail':
          $user = $this->getHighBidder();
          if ( $user == EBAY_NOTHING )
          {
            $user = new Ebay_Buyer();
          } 
          $user->_setProp( 'Email', $data['value'] );
          $this->_setProp( 'HighBidder', $user );
          break;

        case 'BiddingProperties':
          break;

        case 'MaxBid':
        case 'ConvertedMaxBid':
        case 'QuantityBid':
        case 'QuantityWon':
        case 'Winning':
          $this->_setProp( "BiddingProperties" . $tag, $data['value'] );
          break; 
        // special handling if SellerUserId is
        // returned directly (GetMyeBay, BiddingProperties)
        case 'SellerUserId':

          $user = $this->getSeller();
          if ( $user == EBAY_NOTHING )
          {
            $user = new Ebay_Seller();
          } 
          $user->_setProp( 'UserId', $data['value'] );
          $this->_setProp( 'Seller', $user );
          break; 
        // we just create a new user on open and store the user object
        // with the close
        // in between there should the user-element with its own processing
        // acutally setting up the user data !
        case 'HighBidder':
          if ( $type == 'open' )
          {
            $user = new Ebay_Buyer();
          } 
          else
          {
            $this->_setProp( 'HighBidder', $user ); 
            // the user is stored so we do not need the data anymore
            $user = null;
          } 
          break; 
        // we just create a new user on open and store the user object
        // with the close
        // in between there should the user-element with its own processing
        // acutally setting up the user data !
        case 'Seller':
          if ( $type == 'open' )
          {
            $user = new Ebay_Seller();
          } 
          else
          {
            $this->_setProp( 'Seller', $user ); 
            // the user is stored so we do not need the data anymore
            $user = null;
          } 
          break; 
        // we record all elements between open and close
        // finally we pass through the data to the user object
        // the Seller and HighBidder elements will setup the user object
        // if we have just a single user-element (not sure if it exists)
        // we create also a user-object
        case 'User':
          if ( $type == 'open' )
          {
            $recordTillClose = true;
            $recordedData = array();
            $waitForClosingTagName = 'User';
            if ( $user == null )
            {
              $user = new Ebay_User();
            } 
          } 
          else
          {
            $user->InitFromDataStruct( $recordedData );
          } 
          break;

        case 'PaymentTerms':
          break;
        case 'AmEx':
        case 'CashOnPickupAccepted':
        case 'COD':
        case 'CCAccepted':
        case 'Discover':
        case 'Escrow':
        case 'EscrowBySeller':
        case 'MOCashiers':
        case 'MoneyXferAccepted':
        case 'MoneyXferAcceptedinCheckout':
        case 'Other':
        case 'OtherPaymentsOnline':
        case 'PayPalAccepted':
        case 'PayPalEmailAddress':
        case 'PersonalCheck':
        case 'SeeDescription':
        case 'VisaMaster':
          $this->_setProp( "PaymentTerms" . $tag, $data['value'] );
          break;

        case 'ListingDesigner':
          break;

        case 'LayoutId':
        case 'ThemeId':
          $this->_setProp( "ListDesign" . $tag, $data['value'] );
          break;

        case 'ItemProperties':
          break;
        case 'FeaturedGallery':
          $this->_setProp( 'PropGalleryFeatured', $data['value'] );
          break;

        case 'Adult':
        case 'BindingAuction':
        case 'BuyItNowAdded':
        case 'BuyItNowLowered':
        case 'BoldTitle':
        case 'CheckoutEnabled':
        case 'Featured':
        case 'Gallery':
        case 'GalleryFeatured':
        case 'Highlight':
        case 'Private':
        case 'Reserve':
        case 'ReserveLowered':
        case 'ReserveMet':
        case 'ReserveRemoved':
        case 'RestrictedToBusiness':
        case 'SuperFeatured':
        case 'Type':
        case 'VATPercent':
        case 'Picture':
        case 'New':
        case 'Gift':
        case 'BuyItNow':
        case 'IsFixedPrice':
          $this->_setProp( "Prop" . $tag, $data['value'] );
          break;

        case 'GiftServices':
          break;
        case 'GiftExpressShipping':
        case 'GiftShipToRecipient':
        case 'GiftWrap':
          $this->_setProp( $tag, $data['value'] );
          break;

        case 'CharityListingInfo':
          break;
        case 'CharityNumber':
        case 'CharityName':
        case 'DonationPercent':
          $this->_setProp( $tag, $data['value'] );
          break;

        case 'Category2':
        case 'Category':
          break;

        case 'CategoryFullName':
        case 'CategoryId':
        case 'Category2FullName':
        case 'Category2Id':
          $this->_setProp( $tag, $data['value'] );
          break;

        case 'Attributes': 
          // this is deprecated, so just ignore
          break;

        case 'AttributeSet':
          if ( $type == 'open' )
          {
            $attributeSet = new Ebay_AttributeSet();
          } 
          else
          {
            $this->setAttributeSet( $attributeSet );
          } 
          break;

        case 'Attribute':
          if ( $type == 'open' )
          {
            $attribute = new Ebay_Attribute(); 
            // record the data for the attribute
            $recordTillClose = true;
            $recordedData = array();
          } 
          else
          {
            if ( $attribute != null )
            {
              $attribute->InitFromDataStruct( $recordedData );
              if ( $attributeSet != null )
              {
                $attributeSet->_props['Attributes'][] = $attribute;
              } 
            } 
          } 
          break;

        case 'Checkout':
        case 'Details':
          break;
        case 'AdjustmentAmount':
        case 'AdditionalShippingCosts':
        case 'AllowPaymentEdit':
        case 'InsuranceFee':
        case 'InsuranceOption':
        case 'PackagingHandlingCosts':
        case 'SalesTaxPercent':
        case 'SalesTaxState':
        case 'ShippingHandlingCosts':
        case 'ShippingInTax':
        case 'ShippingIrregular':
        case 'ShippingPackage':
        case 'ShippingType':
        case 'WeightMajor':
        case 'WeightMinor':
        case 'WeightUnit':
        case 'ConvertedAdjustmentAmount':
        case 'ConvertedAmountPaid':
        case 'ConvertedTransactionPrice':
        case 'InsuranceTotal':
        case 'InsuranceWanted':
        case 'PaymentEdit':
        case 'SalesTaxAmount':
          $this->_setProp( "Checkout" . $tag, $data['value'] );
          break; 
        // this is the obsolete field from >= 353
        case 'ShippingService':
          $this->_setProp( "Checkout" . $tag, $data['value'] );
          break;

        case 'ShippingServiceOptions':
          break;

        case 'ShippingServiceOption':
          if ( $type == 'open' )
          {
            $shippingServiceOption = new Ebay_ShippingServiceOption(); 
            // record the data for the ShippingServiveOption
            $recordTillClose = true;
            $recordedData = array();
            $waitForClosingTagName = 'ShippingServiceOption';
          } 
          else
          {
            if ( $shippingServiceOption != null )
            {
              $shippingServiceOption->InitFromDataStruct( $recordedData );
              $this->setShippingServiceOption( $shippingServiceOption );
            } 
          } 
          break;

        case 'CheckoutSpecified':
        case 'CheckoutDetailsSpecified':
          $this->_setProp( 'CheckoutSpecified', $data['value'] );
          break;
        case 'CheckoutInstructions':
          $this->_setProp( 'CheckoutInstructions', $data['value'] );
          break;
        case 'CheckoutRedirect':
          $this->_setProp( 'CheckoutRedirect', $data['value'] );
          break;
      } 
    } 

    return true;
  } 

  /**
   * helper function that adds the current objects properties' value to the params array if the property was set
   * 
   * @access public 
   * @param array $params 
   * @param string $key 
   * @param string $datatype use one of the following types :
   * default = maps on the data provided in the property
   * cdata = encloses the data into a CDATA Section
   * boolean = transform data to 0/false or 1/true
   * flattenarray = transforms an array property to a flatten list devided by comma
   * flattenarray_cdata = transforms an array property to a flatten list devided by comma, all enclosed into a CDATA
   * @param string $classPropertyName specifies the name of the property in the object (so _props array index). If not given the parameter key is assumed as the property name also.
   * @param array $reviseAttributes this is an array of attribute-name which should be revised. Set the array to all attributes which should be placed into the param-array. The method will only add attributes which are set AND in the reviseAttributes-array.
   * @param string $xmlTag 
   * @return void 
   */
  function addToParamsIfSet( &$params, $key, $datatype = 'default', $classPropertyName = null, $reviseAttributes = null, $xmlTag = null )
  {
    if ( $classPropertyName != null )
    { 
      // get the data from the index given in $classPropertyName
      $retrieveKey = $classPropertyName;
    } 
    else
    {
      $retrieveKey = $key;
    } 
    // check for revise
    if ( $reviseAttributes != null )
    {
      $bFound = false;
      foreach ( $reviseAttributes as $k )
      if ( $bFound = ( $retrieveKey == $k ) )
        break;

      if ( ! $bFound )
        return;
    } 
    if ( array_key_exists( $retrieveKey, $this->_props ) )
    {
      $thePropValue = $this->_getProp( $retrieveKey );

      if ( $thePropValue != EBAY_NOTHING )
      {
        switch ( $datatype )
        {
          case 'default':
            $params["$key"] = $thePropValue;
            break;
          case 'cdata': 
            // TODO
            // make the right encoding here !
            $params["$key"] = "<![CDATA[" . $thePropValue . "]]" . ">";
            break;
          case 'boolean':
          case 'bool':
            if ( $thePropValue )
            {
              $params["$key"] = "1";
            } 
            else
            {
              $params["$key"] = "0";
            } 
            break;

          case 'flattenarray':
            {
              if ( count( $thePropValue ) )
              {
                $vals = array_values( $thePropValue );
                $params["$key"] = implode( ",", $vals );
              } 
            } 
            break;
          case 'flattenarray_cdata':
            {
              if ( count( $thePropValue ) )
              {
                $vals = array_values( $thePropValue );
                $params["$key"] = "<![CDATA[" . implode( ",", $vals ) . "]]" . ">";
              } 
            } 
            break;
          case 'array2xmlstruct':
            {
              $dataToAdd = "";
              foreach ( $thePropValue as $entry )
              {
                $dataToAdd = '<' . $key . '>' . $entry . '</' . $key . '>';
              } 
              $params["$xmlTag"] = $dataToAdd;
            } 
            break;
        } 
      } 
    } 
    else
    {
      print_r( "The Item does not have the property " . $key . " (addToParamsIfSet)<br>\n" );
    } 
  } 

  /**
   * 
   * @access private 
   * @param array $params 
   * @param array $reviseAttributes this is an array of attribute-name which should be revised. Set the array to all attributes which should be placed into the param-array. The method will only add attributes which are set AND in the reviseAttributes-array.
   * @return void 
   */
  function _setupParamsAddOrVerify( &$params, $reviseAttributes = null )
  { 
    // general
    $this->addToParamsIfSet( $params, 'Category', 'default', 'CategoryId', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'Category2', 'default', 'Category2Id', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ApplicationData', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'Description', 'cdata' );
    $this->addToParamsIfSet( $params, 'Duration', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'Country', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'Currency', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'Title', 'default' );
    $this->addToParamsIfSet( $params, 'BuyItNowPrice', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'Location', 'default', null, $reviseAttributes ); 
    // check this here
    // we might need to switch on the Type
    // also FixedPrice Items maybe need special consideration
    $this->addToParamsIfSet( $params, 'MinimumBid', 'default', 'StartPrice', $reviseAttributes );

    $this->addToParamsIfSet( $params, 'Type', 'default', 'PropType', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'UUID', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ReservePrice', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'Quantity', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'Region', 'default', null, $reviseAttributes ); 
    // special attributes to  AddItem
    $this->addToParamsIfSet( $params, 'ScheduleTime', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'Version', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ApplyShippingDiscount', 'boolean', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'BusinessSeller', 'boolean', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'IsAdFormat', 'boolean', null, $reviseAttributes ); 
    // new !
    $this->addToParamsIfSet( $params, 'SubtitleText', 'default', null, $reviseAttributes ); 
    // checkout
    $this->addToParamsIfSet( $params, 'AdditionalShippingCosts', 'money', 'CheckoutAdditionalShippingCosts', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'CheckoutDetailsSpecified', 'boolean', 'CheckoutSpecified', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'CheckoutInstructions', 'cdata', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'InsuranceFee', 'default', 'CheckoutInsuranceFee', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'InsuranceOption', 'default', 'CheckoutInsuranceOption', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'SalesTaxPercent', 'default', 'CheckoutSalesTaxPercent', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'SalesTaxState', 'default', 'CheckoutSalesTaxState', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'WeightMajor', 'default', 'CheckoutWeightMajor', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'WeightMinor', 'default', 'CheckoutWeightMinor', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'WeightUnit', 'default', 'CheckoutWeightUnit', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'PackagingHandlingCosts', 'default', 'CheckoutPackagingHandlingCosts', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShipFromZipCode', 'default', 'CheckoutShipFromZipCode', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShippingHandlingCosts', 'default', 'CheckoutShippingHandlingCosts', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShippingInTax', 'default', 'CheckoutShippingInTax', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShippingIrregular', 'default', 'CheckoutShippingIrregular', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShippingOption', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShippingPackage', 'default', 'CheckoutShippingPackage', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShippingService', 'default', 'CheckoutShippingService', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShippingType', 'default', 'CheckoutShippingType', $reviseAttributes ); 
    // shipping	options
    $this->addToParamsIfSet( $params, 'ShipToAfrica', 'boolean', 'ShippingRegionsAfrica', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShipToAsia', 'boolean', 'ShippingRegionsAsia', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShipToCaribbean', 'boolean', 'ShippingRegionsCaribbean', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShipToEurope', 'boolean', 'ShippingRegionsEurope', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShipToLatinAmerica', 'boolean', 'ShippingRegionsLatinAmerica', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShipToMiddleEast', 'boolean', 'ShippingRegionsMiddleEast', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShipToNorthAmerica', 'boolean', 'ShippingRegionsNorthAmerica', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShipToOceania', 'boolean', 'ShippingRegionsOceania', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ShipToSouthAmerica', 'boolean', 'ShippingRegionsSouthAmerica', $reviseAttributes );

    $this->addToParamsIfSet( $params, 'SellerPays', 'boolean', 'ShippingTermsSellerPays', $reviseAttributes ); 
    // payment termns
    $this->addToParamsIfSet( $params, 'AmEx', 'boolean', 'PaymentTermsAmEx', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'CashOnPickupAccepted', 'boolean', 'PaymentTermsCashOnPickupAccepted', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'CCAccepted', 'boolean', 'PaymentTermsCCAccepted', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'Discover', 'boolean', 'PaymentTermsDiscover', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'MOCashiers', 'boolean', 'PaymentTermsMOCashiers', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'MoneyXferAccepted', 'boolean', 'PaymentTermsMoneyXferAccepted', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'MoneyXferAcceptedinCheckout', 'boolean', 'PaymentTermsMoneyXferAcceptedinCheckout', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'PaymentOther', 'boolean', 'PaymentTermsOther', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'PaymentOtherOnline', 'boolean', 'PaymentTermsOtherPaymentsOnline', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'PaymentSeeDescription', 'boolean', 'PaymentTermsSeeDescription', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'PayPalAccepted', 'default', 'PaymentTermsPayPalAccepted', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'PayPalEmailAddress', 'default', 'PaymentTermsPayPalEmailAddress', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'PersonalCheck', 'boolean', 'PaymentTermsPersonalCheck', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'COD', 'boolean', 'PaymentTermsCOD', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'VisaMaster', 'boolean', 'PaymentTermsVisaMaster', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'Escrow', 'boolean', 'PaymentTermsEscrow', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'EscrowBySeller', 'boolean', 'PaymentTermsEscrowBySeller', $reviseAttributes ); 
    // visual attributes and features
    $this->addToParamsIfSet( $params, 'AutoPay', 'boolean', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'BoldTitle', 'boolean', 'PropBoldTitle', $reviseAttributes );

    $this->addToParamsIfSet( $params, 'LayoutId', 'default', 'ListDesignLayoutId', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'ThemeId', 'default', 'ListDesignThemeId', $reviseAttributes );

    $this->addToParamsIfSet( $params, 'Counter', 'default', null, $reviseAttributes ); 
    // will deprecate soon
    $this->addToParamsIfSet( $params, 'DomainKey', 'default', null, $reviseAttributes );

    $this->addToParamsIfSet( $params, 'Featured', 'boolean', 'PropFeatured', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'Gallery', 'boolean', 'PropGallery', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'GalleryFeatured', 'boolean', 'PropGalleryFeatured', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'GalleryURL', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'Highlight', 'boolean', 'PropHighlight', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'SuperFeatured', 'boolean', 'PropSuperFeatured', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'VATPercent', 'boolean', 'PropVATPercent', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'Private', 'boolean', 'PropPrivate', $reviseAttributes );
    $this->addToParamsIfSet( $params, 'RestrictedToBusiness', 'boolean', 'PropRestrictedToBusiness', $reviseAttributes ); 
    // gift service
    $this->addToParamsIfSet( $params, 'GiftExpressShipping', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'GiftIcon', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'GiftShipToRecipient', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'GiftWrap', 'default', null, $reviseAttributes ); 
    // picture
    $this->addToParamsIfSet( $params, 'PhotoCount', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'PhotoDisplayType', 'default', null, $reviseAttributes );
    $this->addToParamsIfSet( $params, 'PictureURL', 'default', null, $reviseAttributes ); 
    // StoreInformation
    $this->addToParamsIfSet( $params, 'StoreCategory', 'default', null, $reviseAttributes );

    if ( $this->getAttributeSetCount() > 0 )
    {
      $c = $this->getAttributeSetCount();
      $setXml = '';
      for( $i = 0; $i < $c; $i++ )
      {
        $attSet = $this->getAttributeSet( $i );
        $setXml .= $attSet->renderToXml( 'AddItem' );
      } 

      $params['Attributes'] = $setXml;
    } 

    if ( $this->getShippingServiceOptionCount() > 0 )
    {
      $c = $this->getShippingServiceOptionCount();
      $setXml = '';
      for( $i = 0; $i < $c; $i++ )
      {
        $shipOption = $this->getShippingServiceOption( $i );
        $setXml .= $shipOption->renderToXml( 'AddItem' );
      } 

      $params['ShippingServiceOptions'] = $setXml;
    } 
    // we will add Prefilled Item Information only if
    // a productId is set
    if ( $this->getProductInfoId() != EBAY_NOTHING )
    {
      $prodParams = array();
      $this->addToParamsIfSet( $prodParams, 'IncludeStockPhotoURL', 'boolean', 'ProductInfoIncludeStockPhotoURL', $reviseAttributes );
      $this->addToParamsIfSet( $prodParams, 'IncludePrefilledItemInformation', 'boolean', 'ProductInfoIncludePrefilledItemInformation', $reviseAttributes );
      $this->addToParamsIfSet( $prodParams, 'UseStockPhotoURLAsGallery', 'boolean', 'ProductInfoUseStockPhotoURLAsGallery', $reviseAttributes );

      $rawXml = '<ProductInfo id="' . $this->getProductInfoId() . '">';
      foreach ( $prodParams as $key => $value )
      {
        $rawXml .= "<" . $key . ">" . $value . "</" . $key . ">";
      } 
      $rawXml = '</ProductInfo>'; 
      // double understore indicates raw information
      $params['__ProductInfo'] = $rawXml;
    } 
    // we will add Prefilled Item Information with an external id
    // only if the attribute 'ExternalProductIdentifierId' is set
    if ( $this->_getProp( 'ExternalProductIdentifierId' ) != EBAY_NOTHING )
    {
      if ( $this->_getProp( 'ExternalProductIdentifierReturnSearchResultWithError' ) )
      {
        $showErr = 1;
      } 
      else
      {
        $showErr = 0;
      } 
      $rawXml = '<ExternalProductIdentifier id="' . $this->_getProp( 'ExternalProductIdentifierId' ) . '" type="' . $this->_getProp( 'ExternalProductIdentifierType' ) . '" returnSearchResultWithError="' . $showErr
       . '">'; 
      // double understore indicates raw information
      $params['__ExternalProductIdentifier'] = $rawXml;
    } 
  } 
} 

?>
	
		
		
		