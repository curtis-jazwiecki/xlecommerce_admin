<?php
/*
 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require_once( 'Ebay_Defines.php' );
require_once( 'Ebay_Result.php' );
require_once( 'Ebay_Session.php' );

/**
 * Base class to ResultInformation returned by getResultInfo in all Query objects.
 * 
 * @see Ebay_FeedbackQueryInfo
 * @see Ebay_AccountQueryInfo
 * @see Ebay_CrossPromotionQueryInfo
 * @see Ebay_DescriptionTemplateQueryInfo
 * @see Ebay_ItemCategoryQueryInfo
 * @see Ebay_CategoryQueryInfo
 * @see Ebay_BidQueryInfo
 * @see Ebay_CharacteristicSetQueryInfo
 * @see Ebay_ItemQueryInfo
 * @see Ebay_CategoryCSQueryInfo
 * @see Ebay_ProductFamilyQueryInfo
 * @see Ebay_ItemShippingQueryInfo
 * @see Ebay_TransactionByItemQueryInfo
 * @see Ebay_EventQueryInfo
 * @see Ebay_TransactionQueryInfo
 * @see Ebay_ItemBidderQueryInfo
 * @see Ebay_ItemSellerQueryInfo
 * @see Ebay_ItemWatchlistQueryInfo
 * @see Ebay_DisputeQueryInfo
 */
class Ebay_QueryResultInfo
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
   * Read accessor of ResultCount. 
   * specifies the total number of elements with a resultset. Take care that if pagination support was used to retrieve the data, this will not always match the number of element for the current call.
   * 
   * @access public 
   * @return number Value of the ResultCount property
   */
  function getResultCount()
  {
    return $this->_props['ResultCount'];
  } 

  /**
   * Write accessor of ResultCount.  
   * specifies the total number of elements with a resultset. Take care that if pagination support was used to retrieve the data, this will not always match the number of element for the current call.
   * 
   * @access public 
   * @param number $value The new value for the ResultCount property
   * @return void 
   */
  function setResultCount( $value )
  {
    $this->_props['ResultCount'] = $value;
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['ResultCount'] = null;
  } 
} 

?>
	
		
		
		