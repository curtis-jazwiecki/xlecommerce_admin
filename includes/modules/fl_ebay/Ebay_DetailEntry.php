<?php

require_once( 'Ebay_Defines.php' );

/**
 */
class Ebay_DetailEntry
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
   * Read accessor of Amount. 
   * A fee amount, formatted according to the Entry.Currency (e.g., "14.95" if the currency is US Dollar). Only returned for fee details (Detail.Type = 2 or 3).
   * 
   * @access public 
   * @return money Value of the Amount property
   */
  function getAmount()
  {
    return $this->_props['Amount'];
  } 

  /**
   * Read accessor of Currency. 
   * The code for the currency in which the Entry.Amount is being returned (e.g., "1" for US Dollar). Always the currency associated with the requested SiteId. Only returned for fee details (Detail.Type = 2 or 3).
   * 
   * @access public 
   * @return string Value of the Currency property
   */
  function getCurrency()
  {
    return $this->_props['Currency'];
  } 

  /**
   * Read accessor of Description. 
   * Description of the detail entry. For example, for a Currency list, a Description field might return "us, US Dollar". For a ShippingOption list, a Description field might return "Site Only". The Description data does not necessarily correspond to the input arguments and return values for other functions. For example, the AddItem ShippingOption input argument requires a string with no spaces (e.g., "SiteOnly"). The AddItem Currency input argument required the currency ID (not the descriptive string), which is returned by GeteBayDetails in the Value field. See Sample Output Table for information about mapping GeteBayDetails output fields to other function input and output fields.
   * 
   * @access public 
   * @return string Value of the Description property
   */
  function getDescription()
  {
    return $this->_props['Description'];
  } 

  /**
   * Read accessor of PercentAmount. 
   * For fees that vary per price range, the price percentage that is used to calculate the fee. For example, if the Final Value Fee is 5.25% of the closing price when the closing price is within the range $0.00 - $25.00, the Entry would return PercentAmount = 5.25, RangeFrom = 0, RangeTo = 25. Only returned for range-fee details (Detail.Type = 3).
   * 
   * @access public 
   * @return number Value of the PercentAmount property
   */
  function getPercentAmount()
  {
    return $this->_props['PercentAmount'];
  } 

  /**
   * Read accessor of RangeFrom. 
   * For fees that vary per price range, the lowest value in the range. For example, if the Final Value Fee is 5.25% of the closing price when the closing price is within the range $0.00 - $25.00, the Entry would return PercentAmount = 5.25, RangeFrom = 0, RangeTo = 25. Can contain a decimal value. Only returned for range-fee details (Detail.Type = 3).
   * 
   * @access public 
   * @return number Value of the RangeFrom property
   */
  function getRangeFrom()
  {
    return $this->_props['RangeFrom'];
  } 

  /**
   * Read accessor of RangeTo. 
   * For fees that vary per price range, the highest value in the range. For example, if the Final Value Fee is 5.25% of the closing price when the closing price is within the range $0.00 - $25.00, the Entry would return PercentAmount = 5.25, RangeFrom = 0, RangeTo = 25. Can contain a decimal value. Only returned for range-fee details (Detail.Type = 3).
   * 
   * @access public 
   * @return number Value of the RangeTo property
   */
  function getRangeTo()
  {
    return $this->_props['RangeTo'];
  } 

  /**
   * Read accessor of Value. 
   * The detail entry's value, ID, or code. For example, for a Currency list, a Value might return "1" (for US Dollar). For a ShippingOption list, a Value might return "0" (for SiteOnly). The Value data does not necessarily correspond to the input arguments and return values for other functions. For example, the AddItem Currency input argument requires the currency ID, which is returned by GeteBayDetails in Value. But the AddItem ShippingOption input argument requires a string (e.g., "SiteOnly") instead. See Sample Output Table for information about mapping GeteBayDetails output fields to other function input and output fields.
   * 
   * @access public 
   * @return string Value of the Value property
   */
  function getValue()
  {
    return $this->_props['Value'];
  } 

  /**
   * Read accessor of CategoryId. 
   * For future use. Values currently returned in this field are not necessarily eBay item category IDs. Applications should ignore this field for now. Only returned for fee details (Detail.Type = 2 or 3).
   * 
   * @access public 
   * @return number Value of the CategoryId property
   */
  function getCategoryId()
  {
    return $this->_props['CategoryId'];
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['Amount'] = null;
    $this->_props['Currency'] = null;
    $this->_props['Description'] = null;
    $this->_props['PercentAmount'] = null;
    $this->_props['RangeFrom'] = null;
    $this->_props['RangeTo'] = null;
    $this->_props['Value'] = null;
    $this->_props['CategoryId'] = null;
  } 

  /**
   * 
   * @access public 
   * @param array $arrData 
   * @return boolean 
   */
  function InitFromDataStruct( $arrData )
  {
    foreach ( $arrData as $data )
    {
      $tag = $data['tag'];
      $type = $data['type'];

      switch ( $tag )
      {
        default: 
          // print_r( "dangling tag (DetailEntry) : " . $tag . "<br>\r\n" );
          break; 
        // all directly maped data
        case 'Amount':
        case 'Currency':
        case 'Description':
        case 'PercentAmount':
        case 'RangeFrom':
        case 'RangeTo':
        case 'Value':
        case 'CategoryId':
          $this->_setProp( $tag, $data['value'] );
          break;
      } 
    } 
    return true;
  } 

  /**
   * 
   * @access public 
   * @return void 
   */
  function Ebay_DetailEntry()
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_DetailEntry::_init(); 
    // insert code here...
  } 
} 

?>
	
		
		
		