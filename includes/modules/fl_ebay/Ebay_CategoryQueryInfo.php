<?php
/*
 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

require_once( 'Ebay_Defines.php' );

require_once( 'Ebay_QueryResultInfo.php' );

/**
 */
class Ebay_CategoryQueryInfo
extends Ebay_QueryResultInfo
{
  /**
   * Read accessor of UpdateGMTTime.
   * 
   * @access public 
   * @return datetime Value of the UpdateGMTTime property
   */
  function getUpdateGMTTime()
  {
    return $this->_props['UpdateGMTTime'];
  } 

  /**
   * Write accessor of UpdateGMTTime.
   * 
   * @access public 
   * @param datetime $value The new value for the UpdateGMTTime property
   * @return void 
   */
  function setUpdateGMTTime( $value )
  {
    $this->_props['UpdateGMTTime'] = $value;
  } 

  /**
   * Read accessor of UpdateTime.
   * 
   * @access public 
   * @return datetime Value of the UpdateTime property
   */
  function getUpdateTime()
  {
    return $this->_props['UpdateTime'];
  } 

  /**
   * Write accessor of UpdateTime.
   * 
   * @access public 
   * @param datetime $value The new value for the UpdateTime property
   * @return void 
   */
  function setUpdateTime( $value )
  {
    $this->_props['UpdateTime'] = $value;
  } 

  /**
   * Read accessor of Version.
   * 
   * @access public 
   * @return number Value of the Version property
   */
  function getVersion()
  {
    return $this->_props['Version'];
  } 

  /**
   * Write accessor of Version.
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
   * Read accessor of CategoryCount.
   * 
   * @access public 
   * @return number Value of the CategoryCount property
   */
  function getCategoryCount()
  {
    return $this->_props['CategoryCount'];
  } 

  /**
   * Write accessor of CategoryCount.
   * 
   * @access public 
   * @param number $value The new value for the CategoryCount property
   * @return void 
   */
  function setCategoryCount( $value )
  {
    $this->_props['CategoryCount'] = $value;
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['UpdateGMTTime'] = EBAY_NOTHING;
    $this->_props['UpdateTime'] = EBAY_NOTHING;
    $this->_props['Version'] = EBAY_NOTHING;
    $this->_props['CategoryCount'] = EBAY_NOTHING;
  } 

  /**
   * 
   * @access public 
   * @return void 
   */
  function Ebay_CategoryQueryInfo()
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_CategoryQueryInfo::_init(); 
    // insert code here...
  } 
} 

?>
	
		
		
		