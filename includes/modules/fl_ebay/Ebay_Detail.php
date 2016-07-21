<?php
/*
 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require_once( 'Ebay_Defines.php' );
require_once( 'Ebay_DetailEntry.php' );

/**
 */
class Ebay_Detail
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
   * Read accessor of Name. 
   * The name of a list of details (e.g., "Currency" or "ShippingOption").
   * 
   * @access public 
   * @return string Value of the Name property
   */
  function getName()
  {
    return $this->_props['Name'];
  } 

  /**
   * Read accessor of Type. 
   * Enumeration indicating the nature of the meta-data returned. Additional data is returned for entries that contain fixed fees, such as the Bold fee, and fees that vary according to price ranges (i.e., on a sliding scale), such as the Final Value Fee. Possible values: 1 = Non-fee data (Value and Description fields only) 2 = Fixed fees (Type 1 fields + Amount, Currency, CategoryId) 3 = Range fees (Type 2 fields + PercentAmount, RangeFrom, RangeTo)
   * 
   * @access public 
   * @return string Value of the Type property
   */
  function getType()
  {
    return $this->_props['Type'];
  } 

  /**
   * Read accessor to the Entries.
   * 
   * @access public 
   * @param integer $index The index of the value to return
   * @return Ebay _DetailEntry Value of the Entries property
   */
  function getEntries( $index )
  {
    return $this->_props['Entries'][$index];
  } 

  /**
   * Return the amount of Entries actually declared
   * 
   * @access public 
   * @return Ebay _DetailEntry Value of the Entries property
   */
  function getEntriesCount()
  {
    return count( $this->_props['Entries'] );
  } 
  /**
   * Returns a copy of the Entries array
   * 
   * @access public 
   * @return array of Ebay_DetailEntry
   */
  function getEntriesArray()
  {
    return $this->_props['Entries'];
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['Name'] = null;
    $this->_props['Type'] = null;
    $this->_props['Entries'] = array();
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
          // print_r( "dangling tag (Seller) : " . $tag . "<br>\r\n" );
          break; 
        // all directly maped data
        case 'Name':
        case 'Type':
          $this->_setProp( $tag, $data['value'] );
          break;
        case 'Entry':
          if ( $data['type'] == 'open' )
          {
            $resData = array();
          } 
          else
          {
            $detailEntry = new Ebay_DetailEntry();
            $detailEntry->InitFromDataStruct( $resData );
            $this->_props['Entries'][] = $detailEntry;
          } 
          break;
        default:
          $resData[] = $data;
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
  function Ebay_Detail()
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_Detail::_init(); 
    // insert code here...
  } 
} 

?>
	
		
		
		