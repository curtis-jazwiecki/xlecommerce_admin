<?php
/*
 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require_once( 'Ebay_Defines.php' );

/**
 */
class Ebay_Category
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
   * Read accessor of AutoPayEnabled.
   * 
   * @access public 
   * @return boolean Value of the AutoPayEnabled property
   */
  function getAutoPayEnabled()
  {
    return $this->_props['AutoPayEnabled'];
  } 

  /**
   * Read accessor of B2BVATEnabled.
   * 
   * @access public 
   * @return boolean Value of the B2BVATEnabled property
   */
  function getB2BVATEnabled()
  {
    return $this->_props['B2BVATEnabled'];
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
   * Read accessor of CategoryLevel.
   * 
   * @access public 
   * @return number Value of the CategoryLevel property
   */
  function getCategoryLevel()
  {
    return $this->_props['CategoryLevel'];
  } 

  /**
   * Read accessor of CategoryName.
   * 
   * @access public 
   * @return string Value of the CategoryName property
   */
  function getCategoryName()
  {
    return $this->_props['CategoryName'];
  } 

  /**
   * Read accessor of CategoryParentId.
   * 
   * @access public 
   * @return number Value of the CategoryParentId property
   */
  function getCategoryParentId()
  {
    return $this->_props['CategoryParentId'];
  } 

  /**
   * Read accessor of IsExpired.
   * 
   * @access public 
   * @return boolean Value of the IsExpired property
   */
  function getIsExpired()
  {
    return $this->_props['IsExpired'];
  } 

  /**
   * Read accessor of IsVirtual.
   * 
   * @access public 
   * @return boolean Value of the IsVirtual property
   */
  function getIsVirtual()
  {
    return $this->_props['IsVirtual'];
  } 

  /**
   * Read accessor of LeafCategory.
   * 
   * @access public 
   * @return boolean Value of the LeafCategory property
   */
  function getLeafCategory()
  {
    return $this->_props['LeafCategory'];
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
   * Read accessor of PercentItemFound. 
   * Percentage of the matching items that were found in this category, relative to other categories in which matching items were also found. Indicates the distribution of matching items across the suggested categories. 
   * The data is only set when the category is retrieved via Ebay_CategorySuggestedQuery !
   * 
   * @access public 
   * @return number Value of the PercentItemFound property
   */
  function getPercentItemFound()
  {
    return $this->_props['PercentItemFound'];
  } 

  /**
   * Write accessor of PercentItemFound.  
   * Percentage of the matching items that were found in this category, relative to other categories in which matching items were also found. Indicates the distribution of matching items across the suggested categories. 
   * The data is only set when the category is retrieved via Ebay_CategorySuggestedQuery !
   * 
   * @access public 
   * @param number $value The new value for the PercentItemFound property
   * @return void 
   */
  function setPercentItemFound( $value )
  {
    $this->_props['PercentItemFound'] = $value;
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['AutoPayEnabled'] = EBAY_NOTHING;
    $this->_props['B2BVATEnabled'] = EBAY_NOTHING;
    $this->_props['CategoryId'] = EBAY_NOTHING;
    $this->_props['CategoryLevel'] = EBAY_NOTHING;
    $this->_props['CategoryName'] = EBAY_NOTHING;
    $this->_props['CategoryParentId'] = EBAY_NOTHING;
    $this->_props['IsExpired'] = EBAY_NOTHING;
    $this->_props['IsVirtual'] = EBAY_NOTHING;
    $this->_props['LeafCategory'] = EBAY_NOTHING;
    $this->_props['CategoryCount'] = EBAY_NOTHING;
    $this->_props['UpdateGMTTime'] = EBAY_NOTHING;
    $this->_props['UpdateTime'] = EBAY_NOTHING;
    $this->_props['Version'] = EBAY_NOTHING;
    $this->_props['PercentItemFound'] = EBAY_NOTHING;
  } 

  /**
   * 
   * @access public 
   * @return void 
   */
  function Ebay_Category()
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_Category::_init(); 
    // insert code here...
  } 

  /**
   * 
   * @access public 
   * @param array $arrData 
   * @return boolean 
   */
  function InitFromDataStruct( $arrData )
  {
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
        case 'AutoPayEnabled':
        case 'B2BVATEnabled':
        case 'CategoryId':
        case 'CategoryLevel':
        case 'CategoryName':
        case 'CategoryParentId':
        case 'IsExpired':
        case 'IsVirtual':
        case 'LeafCategory':
        case 'PercentItemFound':
          $this->_setProp( $tag, $data['value'] );
          break;
      } 
    } 
    return true;
  } 
} 

?>
	
		
		
		