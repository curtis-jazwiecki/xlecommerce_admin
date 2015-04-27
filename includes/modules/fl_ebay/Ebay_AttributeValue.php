<?php

require_once( 'Ebay_Defines.php' );

/**
 * 
 * @see Ebay_AttributeDateValue
 * @see EBay_AttributeTextValue
 * @see Ebay_AttributeIdValue
 */
class Ebay_AttributeValue
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
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
  } 
} 

?>
	
		
		
		