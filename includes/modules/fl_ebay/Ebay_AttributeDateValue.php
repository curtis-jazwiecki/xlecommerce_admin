<?php
/*
 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require_once( 'Ebay_Defines.php' );

require_once( 'Ebay_AttributeValue.php' );

/**
 */
class Ebay_AttributeDateValue
extends Ebay_AttributeValue
{
  /**
   * Read accessor of DayValue.
   * 
   * @access public 
   * @return number Value of the DayValue property
   */
  function getDayValue()
  {
    return $this->_props['DayValue'];
  } 

  /**
   * Write accessor of DayValue.
   * 
   * @access public 
   * @param number $value The new value for the DayValue property
   * @return void 
   */
  function setDayValue( $value )
  {
    $this->_props['DayValue'] = $value;
  } 

  /**
   * Read accessor of MonthValue.
   * 
   * @access public 
   * @return number Value of the MonthValue property
   */
  function getMonthValue()
  {
    return $this->_props['MonthValue'];
  } 

  /**
   * Write accessor of MonthValue.
   * 
   * @access public 
   * @param number $value The new value for the MonthValue property
   * @return void 
   */
  function setMonthValue( $value )
  {
    $this->_props['MonthValue'] = $value;
  } 

  /**
   * Read accessor of YearValue.
   * 
   * @access public 
   * @return number Value of the YearValue property
   */
  function getYearValue()
  {
    return $this->_props['YearValue'];
  } 

  /**
   * Write accessor of YearValue.
   * 
   * @access public 
   * @param number $value The new value for the YearValue property
   * @return void 
   */
  function setYearValue( $value )
  {
    $this->_props['YearValue'] = $value;
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['DayValue'] = EBAY_NOTHING;
    $this->_props['MonthValue'] = EBAY_NOTHING;
    $this->_props['YearValue'] = EBAY_NOTHING;
  } 

  /**
   * 
   * @access public 
   * @return void 
   */
  function Ebay_AttributeDateValue()
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_AttributeDateValue::_init(); 
    // insert code here...
  } 
} 

?>
	
		
		
		