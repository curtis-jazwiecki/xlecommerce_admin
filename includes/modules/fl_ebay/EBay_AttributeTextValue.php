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
class EBay_AttributeTextValue
extends Ebay_AttributeValue
{
  /**
   * Read accessor of TextValue.
   * 
   * @access public 
   * @return string Value of the TextValue property
   */
  function getTextValue()
  {
    return $this->_props['TextValue'];
  } 

  /**
   * Write accessor of TextValue.
   * 
   * @access public 
   * @param string $value The new value for the TextValue property
   * @return void 
   */
  function setTextValue( $value )
  {
    $this->_props['TextValue'] = $value;
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['TextValue'] = null;
  } 

  /**
   * 
   * @access public 
   * @return void 
   */
  function EBay_AttributeTextValue()
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    EBay_AttributeTextValue::_init(); 
    // insert code here...
  } 
} 

?>
	
		
		
		