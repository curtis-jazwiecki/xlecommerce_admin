<?php

require_once( 'Ebay_Defines.php' );

require_once( 'Ebay_User.php' );

/**
 * done, v1.0.0.1
 * Describes a Buyer in a sales transaction. As derivated from User the buyer's information adds Shipping-Adress-Information to the user data. Buyers can change there Shipping-Adress on a transaction base, so you should this information when handling fullfillment. When checkout is disabled for an auction you will never get information here as the buyers extra information is added during checkout.
 */
class Ebay_Buyer
extends Ebay_User
{
  /**
   * Read accessor of ShippingAddressCity. 
   * City portion of shipping address.
   * 
   * @access public 
   * @return string Value of the ShippingAddressCity property
   */
  function getShippingAddressCity()
  {
    return $this->_props['ShippingAddressCity'];
  } 

  /**
   * Read accessor of ShippingAddressCountryCode. 
   * City portion of shipping address.
   * 
   * @access public 
   * @return string Value of the ShippingAddressCountryCode property
   */
  function getShippingAddressCountryCode()
  {
    return $this->_props['ShippingAddressCountryCode'];
  } 

  /**
   * Read accessor of ShippingAddressCountry. 
   * Country portion of buyer's shipping address.
   * 
   * @access public 
   * @return string Value of the ShippingAddressCountry property
   */
  function getShippingAddressCountry()
  {
    return $this->_props['ShippingAddressCountry'];
  } 

  /**
   * Read accessor of ShippingAddressName. 
   * Name portion of buyer's shipping address.
   * 
   * @access public 
   * @return string Value of the ShippingAddressName property
   */
  function getShippingAddressName()
  {
    return $this->_props['ShippingAddressName'];
  } 

  /**
   * Read accessor of ShippingAddressPhone. 
   * Phone number portion of buyer's shipping address.
   * 
   * @access public 
   * @return string Value of the ShippingAddressPhone property
   */
  function getShippingAddressPhone()
  {
    return $this->_props['ShippingAddressPhone'];
  } 

  /**
   * Read accessor of ShippingAddressStateOrProvince. 
   * State (or region) portion of buyer's shipping address.
   * 
   * @access public 
   * @return string Value of the ShippingAddressStateOrProvince property
   */
  function getShippingAddressStateOrProvince()
  {
    return $this->_props['ShippingAddressStateOrProvince'];
  } 

  /**
   * Read accessor of ShippingAddressStreet1. 
   * First line of street address portion of buyer's shipping address.
   * 
   * @access public 
   * @return string Value of the ShippingAddressStreet1 property
   */
  function getShippingAddressStreet1()
  {
    return $this->_props['ShippingAddressStreet1'];
  } 

  /**
   * Read accessor of ShippingAddressStreet2. 
   * Second line of street address portion of buyer's shipping address.
   * 
   * @access public 
   * @return string Value of the ShippingAddressStreet2 property
   */
  function getShippingAddressStreet2()
  {
    return $this->_props['ShippingAddressStreet2'];
  } 

  /**
   * Read accessor of ShippingAddressZip. 
   * Zip or postal code portion of buyer's shipping address.
   * 
   * @access public 
   * @return string Value of the ShippingAddressZip property
   */
  function getShippingAddressZip()
  {
    return $this->_props['ShippingAddressZip'];
  } 

  /**
   * Read accessor of AddressId. 
   * returned if the buyer is used within a combined order, see ebay_order for details
   * 
   * @access public 
   * @return string Value of the AddressId property
   */
  function getAddressId()
  {
    return $this->_props['AddressId'];
  } 

  /**
   * Read accessor of AddressOwner. 
   * returned if the buyer is used within a combined order, see ebay_order for details
   * 
   * @access public 
   * @return string Value of the AddressOwner property
   */
  function getAddressOwner()
  {
    return $this->_props['AddressOwner'];
  } 

  /**
   * Read accessor of BuyerStoredAddress. 
   * returned if the buyer is used within a combined order, see ebay_order for details
   * 
   * @access public 
   * @return boolean Value of the BuyerStoredAddress property
   */
  function getBuyerStoredAddress()
  {
    return $this->_props['BuyerStoredAddress'];
  } 

  /**
   * Read accessor of ExternalAddressId. 
   * returned if the buyer is used within a combined order, see ebay_order for details
   * 
   * @access public 
   * @return string Value of the ExternalAddressId property
   */
  function getExternalAddressId()
  {
    return $this->_props['ExternalAddressId'];
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['ShippingAddressCity'] = EBAY_NOTHING;
    $this->_props['ShippingAddressCountryCode'] = EBAY_NOTHING;
    $this->_props['ShippingAddressCountry'] = EBAY_NOTHING;
    $this->_props['ShippingAddressName'] = EBAY_NOTHING;
    $this->_props['ShippingAddressPhone'] = EBAY_NOTHING;
    $this->_props['ShippingAddressStateOrProvince'] = EBAY_NOTHING;
    $this->_props['ShippingAddressStreet1'] = EBAY_NOTHING;
    $this->_props['ShippingAddressStreet2'] = EBAY_NOTHING;
    $this->_props['ShippingAddressZip'] = EBAY_NOTHING;
    $this->_props['AddressId'] = EBAY_NOTHING;
    $this->_props['AddressOwner'] = EBAY_NOTHING;
    $this->_props['BuyerStoredAddress'] = EBAY_NOTHING;
    $this->_props['ExternalAddressId'] = EBAY_NOTHING;
  } 

  /**
   * 
   * @access public 
   * @return void 
   */
  function Ebay_Buyer()
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_Buyer::_init();
    $this->Ebay_User();
  } 

  /**
   * 
   * @access public 
   * @param array $arrData 
   * @return boolean 
   */
  function InitFromDataStruct( $arrData )
  {
    parent::InitFromDataStruct( $arrData );

    $readAddressData = false;
    foreach ( $arrData as $data )
    {
      $tag = $data['tag'];
      $type = $data['type'];

      switch ( $tag )
      {
        default: 
          // print_r( "dangling tag (Buyer) : " . $tag . "<br>\r\n" );
          break; 
        // all directly maped data
        case 'AddressId':
        case 'AddressOwner':
        case 'BuyerStoredAddress':
        case 'ExternalAddressId':
          if ( $readAddressData )
            $this->_setProp( $tag, $data['value'] );
          break;

        case 'ShippingAddress':
          $readAddressData = ( $type == 'open' );
          break;
        case 'City':
        case 'CountryCode':
        case 'Country':
        case 'Name':
        case 'Phone':
        case 'Street1':
        case 'Street2':
        case 'Zip':
          if ( $readAddressData )
            $this->_setProp( "ShippingAddress" . $tag, $data['value'] );
          break;
        case 'StateOrProvince':
        case 'StateorProvince':
          if ( $readAddressData )
            $this->_setProp( "ShippingAddressStateOrProvince", $data['value'] );
          break;
      } 
    } 
    return true;
  } 
} 

?>
	
		
		
		