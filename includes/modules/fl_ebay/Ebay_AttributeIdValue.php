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
class Ebay_AttributeIdValue
extends Ebay_AttributeValue
{
  /**
   * Read accessor of IdValue. 
   * The Value of the Id of this AttributeValue.
   * 
   * @access public 
   * @return number Value of the IdValue property
   */
  function getIdValue()
  {
    return $this->_props['IdValue'];
  } 

  /**
   * Write accessor of IdValue.  
   * The Value of the Id of this AttributeValue.
   * 
   * @access public 
   * @param number $value The new value for the IdValue property
   * @return void 
   */
  function setIdValue( $value )
  {
    $this->_props['IdValue'] = $value;
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['IdValue'] = null;
  } 

  /**
   * 
   * @access public 
   * @return void 
   */
  function Ebay_AttributeIdValue()
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_AttributeIdValue::_init(); 
    // insert code here...
  } 
} 

?>
	
		
		
		