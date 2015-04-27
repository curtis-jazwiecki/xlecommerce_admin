<?php

require_once( 'Ebay_Defines.php' );

require_once( 'Ebay_User.php' );

/**
 */
class Ebay_Seller
extends Ebay_User
{
  /**
   * Read accessor of PaymentAddressCity.
   * 
   * @access public 
   * @return string Value of the PaymentAddressCity property
   */
  function getPaymentAddressCity()
  {
    return $this->_props['PaymentAddressCity'];
  } 

  /**
   * Write accessor of PaymentAddressCity.
   * 
   * @access public 
   * @param string $value The new value for the PaymentAddressCity property
   * @return void 
   */
  function setPaymentAddressCity( $value )
  {
    $this->_props['PaymentAddressCity'] = $value;
  } 

  /**
   * Read accessor of PaymentAddressCountry.
   * 
   * @access public 
   * @return string Value of the PaymentAddressCountry property
   */
  function getPaymentAddressCountry()
  {
    return $this->_props['PaymentAddressCountry'];
  } 

  /**
   * Write accessor of PaymentAddressCountry.
   * 
   * @access public 
   * @param string $value The new value for the PaymentAddressCountry property
   * @return void 
   */
  function setPaymentAddressCountry( $value )
  {
    $this->_props['PaymentAddressCountry'] = $value;
  } 

  /**
   * Read accessor of PaymentAddressInternationalName.
   * 
   * @access public 
   * @return string Value of the PaymentAddressInternationalName property
   */
  function getPaymentAddressInternationalName()
  {
    return $this->_props['PaymentAddressInternationalName'];
  } 

  /**
   * Write accessor of PaymentAddressInternationalName.
   * 
   * @access public 
   * @param string $value The new value for the PaymentAddressInternationalName property
   * @return void 
   */
  function setPaymentAddressInternationalName( $value )
  {
    $this->_props['PaymentAddressInternationalName'] = $value;
  } 

  /**
   * Read accessor of PaymentAddressStateAndCity.
   * 
   * @access public 
   * @return string Value of the PaymentAddressStateAndCity property
   */
  function getPaymentAddressStateAndCity()
  {
    return $this->_props['PaymentAddressStateAndCity'];
  } 

  /**
   * Write accessor of PaymentAddressStateAndCity.
   * 
   * @access public 
   * @param string $value The new value for the PaymentAddressStateAndCity property
   * @return void 
   */
  function setPaymentAddressStateAndCity( $value )
  {
    $this->_props['PaymentAddressStateAndCity'] = $value;
  } 

  /**
   * Read accessor of PaymentAddressInternationalStreet.
   * 
   * @access public 
   * @return string Value of the PaymentAddressInternationalStreet property
   */
  function getPaymentAddressInternationalStreet()
  {
    return $this->_props['PaymentAddressInternationalStreet'];
  } 

  /**
   * Write accessor of PaymentAddressInternationalStreet.
   * 
   * @access public 
   * @param string $value The new value for the PaymentAddressInternationalStreet property
   * @return void 
   */
  function setPaymentAddressInternationalStreet( $value )
  {
    $this->_props['PaymentAddressInternationalStreet'] = $value;
  } 

  /**
   * Read accessor of PaymentAddressName.
   * 
   * @access public 
   * @return string Value of the PaymentAddressName property
   */
  function getPaymentAddressName()
  {
    return $this->_props['PaymentAddressName'];
  } 

  /**
   * Write accessor of PaymentAddressName.
   * 
   * @access public 
   * @param string $value The new value for the PaymentAddressName property
   * @return void 
   */
  function setPaymentAddressName( $value )
  {
    $this->_props['PaymentAddressName'] = $value;
  } 

  /**
   * Read accessor of PaymentAddressPhone.
   * 
   * @access public 
   * @return string Value of the PaymentAddressPhone property
   */
  function getPaymentAddressPhone()
  {
    return $this->_props['PaymentAddressPhone'];
  } 

  /**
   * Write accessor of PaymentAddressPhone.
   * 
   * @access public 
   * @param string $value The new value for the PaymentAddressPhone property
   * @return void 
   */
  function setPaymentAddressPhone( $value )
  {
    $this->_props['PaymentAddressPhone'] = $value;
  } 

  /**
   * Read accessor of PaymentAddressStateOrProvince.
   * 
   * @access public 
   * @return string Value of the PaymentAddressStateOrProvince property
   */
  function getPaymentAddressStateOrProvince()
  {
    return $this->_props['PaymentAddressStateOrProvince'];
  } 

  /**
   * Write accessor of PaymentAddressStateOrProvince.
   * 
   * @access public 
   * @param string $value The new value for the PaymentAddressStateOrProvince property
   * @return void 
   */
  function setPaymentAddressStateOrProvince( $value )
  {
    $this->_props['PaymentAddressStateOrProvince'] = $value;
  } 

  /**
   * Read accessor of PaymentAddressStreet1.
   * 
   * @access public 
   * @return string Value of the PaymentAddressStreet1 property
   */
  function getPaymentAddressStreet1()
  {
    return $this->_props['PaymentAddressStreet1'];
  } 

  /**
   * Write accessor of PaymentAddressStreet1.
   * 
   * @access public 
   * @param string $value The new value for the PaymentAddressStreet1 property
   * @return void 
   */
  function setPaymentAddressStreet1( $value )
  {
    $this->_props['PaymentAddressStreet1'] = $value;
  } 

  /**
   * Read accessor of PaymentAddressStreet2.
   * 
   * @access public 
   * @return string Value of the PaymentAddressStreet2 property
   */
  function getPaymentAddressStreet2()
  {
    return $this->_props['PaymentAddressStreet2'];
  } 

  /**
   * Write accessor of PaymentAddressStreet2.
   * 
   * @access public 
   * @param string $value The new value for the PaymentAddressStreet2 property
   * @return void 
   */
  function setPaymentAddressStreet2( $value )
  {
    $this->_props['PaymentAddressStreet2'] = $value;
  } 

  /**
   * Read accessor of PaymentAddressZip.
   * 
   * @access public 
   * @return string Value of the PaymentAddressZip property
   */
  function getPaymentAddressZip()
  {
    return $this->_props['PaymentAddressZip'];
  } 

  /**
   * Write accessor of PaymentAddressZip.
   * 
   * @access public 
   * @param string $value The new value for the PaymentAddressZip property
   * @return void 
   */
  function setPaymentAddressZip( $value )
  {
    $this->_props['PaymentAddressZip'] = $value;
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['PaymentAddressCity'] = EBAY_NOTHING;
    $this->_props['PaymentAddressCountry'] = EBAY_NOTHING;
    $this->_props['PaymentAddressInternationalName'] = EBAY_NOTHING;
    $this->_props['PaymentAddressStateAndCity'] = EBAY_NOTHING;
    $this->_props['PaymentAddressInternationalStreet'] = EBAY_NOTHING;
    $this->_props['PaymentAddressName'] = EBAY_NOTHING;
    $this->_props['PaymentAddressPhone'] = EBAY_NOTHING;
    $this->_props['PaymentAddressStateOrProvince'] = EBAY_NOTHING;
    $this->_props['PaymentAddressStreet1'] = EBAY_NOTHING;
    $this->_props['PaymentAddressStreet2'] = EBAY_NOTHING;
    $this->_props['PaymentAddressZip'] = EBAY_NOTHING;
  } 

  /**
   * Store the acutal PaymentAddress using API's SetSellerPaymentAddress. Attention this information will be saved for the user given through the session-object.
   * 
   * @access public 
   * @param Ebay $ _Session $session
   * @return Ebay _Result
   */
  function UpdatePaymentAddress( $session = null )
  {
    if ( !$this->_apiCaller )
      $this->_apiCaller = new Ebay_ApiCaller( $this->_session );

    $res = new Ebay_Result();

    $params = array();

    $this->addToParamsIfSet( $params, 'Name', 'cdata', 'PaymentAddressName', null );
    $this->addToParamsIfSet( $params, 'Street1', 'cdata', 'PaymentAddressStreet1', null );
    $this->addToParamsIfSet( $params, 'Street2', 'cdata', 'PaymentAddressStreet2', null );
    $this->addToParamsIfSet( $params, 'City', 'cdata', 'PaymentAddressCity', null );
    $this->addToParamsIfSet( $params, 'StateOrProvince', 'cdata', 'PaymentAddressStateOrProvince', null );
    $this->addToParamsIfSet( $params, 'City', 'cdata', 'PaymentAddressCity', null );
    $this->addToParamsIfSet( $params, 'Country', 'default', 'PaymentAddressCountry', null );
    $this->addToParamsIfSet( $params, 'Zip', 'cdata', 'PaymentAddressZip', null );
    $this->addToParamsIfSet( $params, 'Phone', 'cdata', 'PaymentAddressPhone', null );

    $res = $this->_apiCaller->call( 'SetSellerPaymentAddress', $params, 0 );
    if ( $res->isGood() )
    {
      $statusData = $res->getXmlStructTagContent( 'Status' );
      $res->addError( EBAY_ERR_SUCCESS, "SetSellerPaymentAddress status : " . $statusData, EBAY_ERR_SUCCESS );
    } 

    return $res;
  } 

  /**
   * 
   * @access public 
   * @return void 
   */
  function Ebay_Seller()
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_Seller::_init();
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

    foreach ( $arrData as $data )
    {
      $tag = $data['tag'];
      $type = $data['type'];

      switch ( $tag )
      {
        default:
          // print_r( "dangling tag (Seller) : " . $tag . "<br>\r\n" );
          break; 
        // all directly maped data
        case 'PaymentAddressCity':
        case 'PaymentAddressCountry':
        case 'PaymentAddressInternationalName':
        case 'PaymentAddressStateAndCity':
        case 'PaymentAddressInternationalStreet':
        case 'PaymentAddressName':
        case 'PaymentAddressPhone':
        case 'PaymentAddressStateOrProvince':
        case 'PaymentAddressStreet1':
        case 'PaymentAddressStreet2':
        case 'PaymentAddressZip':
          $this->_setProp( $tag, $data['value'] );
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
   * @return void 
   */
  function addToParamsIfSet( &$params, $key, $datatype = 'default', $classPropertyName = null, $reviseAttributes = null )
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
      if ( !array_key_exists( $retrieveKey, $reviseAttributes ) )
      {
        return;
      } 
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
        } 
      } 
    } 
    else
    {
      print_r( "Do not have the property " . $key . " (addToParamsIfSet)<br>\n" );
    } 
  } 
} 

?>
	
		
		
		