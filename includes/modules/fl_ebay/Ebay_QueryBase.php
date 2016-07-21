<?php
/*
 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require_once( 'Ebay_Defines.php' );
require_once( 'Ebay_ApiCaller.php' );
require_once( 'Ebay_Result.php' );
require_once( 'Ebay_QueryResultInfo.php' );
require_once( 'Ebay_Session.php' );

/**
 * 
 * @see Ebay_ItemSellerQuery
 * @see Ebay_OrderQuery
 * @see Ebay_BidQuery
 * @see Ebay_CategoryCSQuery
 * @see Ebay_ProductQuery
 * @see Ebay_UserQuery
 * @see Ebay_AccessRuleQuery
 * @see Ebay_ItemBidderQuery
 * @see Ebay_CrossPromotionQuery
 * @see Ebay_ShippingRateQuery
 * @see Ebay_HighBidQuery
 * @see Ebay_MyEbayListQuery
 * @see Ebay_ProductFinderStyleSheetQuery
 * @see Ebay_CharacteristicSetQuery
 * @see Ebay_AdLeadQuery
 * @see Ebay_EventQuery
 * @see Ebay_AccountQuery
 * @see Ebay_CategoryQuery
 * @see Ebay_ProductSearchPageQuery
 * @see Ebay_CustomCategoryQuery
 * @see Ebay_TransactionByItemQuery
 * @see Ebay_PreferenceSetQuery
 * @see Ebay_OutageScheduleQuery
 * @see Ebay_AttributesStyleSheetQuery
 * @see Ebay_ProductFinderQuery
 * @see Ebay_PromotionRuleQuery
 * @see Ebay_FeedbackQuery
 * @see Ebay_ItemCategoryQuery
 * @see Ebay_CategorySuggestedQuery
 * @see Ebay_ItemQuery
 * @see Ebay_ProductFamilyQuery
 * @see Ebay_ItemShippingQuery
 * @see Ebay_DescriptionTemplateQuery
 * @see Ebay_ItemWatchlistQuery
 * @see Ebay_DetailQuery
 * @see Ebay_NotificationPreferenceQuery
 * @see Ebay_TransactionQuery
 * @see Ebay_DisputeQuery
 */
class Ebay_QueryBase
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
   * 
   * @access private 
   * @var Ebay _ApiCaller
   */

  var $_apiCaller = EBAY_NOTHING;

  /**
   * 
   * @access private 
   * @var array 
   */

  var $_resultList = array();

  /**
   * 
   * @access private 
   * @var Ebay _QueryResultInfo
   */

  var $_resultInfo = null;

  /**
   * Gives a way to limit the size of the resultList. Acutally if set the attribute will break out when this limit is reached. Anyway the breakout will only happen after the current page is retrieved, e.g. using a pagesize of 100 and a limit of 90 might result in a list containing 100 elements anyway.
   * 0 (zero) mean no limit.
   * 
   * @access private 
   * @var number 
   */

  var $_resultLimit = 0;

  /**
   * 
   * @access private 
   * @var number 
   */

  var $_pageSize = null;

  /**
   * 
   * @access private 
   * @var Ebay _Session
   */

  var $_session = EBAY_NOTHING;

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
  } 

  /**
   * Standard method to do a query. Setup the apropricate query object and specifiy the filter through the properties, then call Query to get back a Result.
   * 
   * @access public 
   * @param number $detailLevel 
   * @param number $aPageSize could be used to overwrite the standard page-size given in the session. e.g. QueryItem (->GetSearchResult) normally works with a pageSize of 100
   * @param number $delta defines the direction how the various page will get called. 
   * Here 1 is the default so the pages will get retrieve with ascending direction. If you specify -1 the data retrieval will be done with descending direction (so last page first).
   * @param number $currentPage specifies the starting page for the retrieval. This makes only sense in conjunction with a -1 delta.
   * @return Ebay _Result
   */
  function Query( $detailLevel = 0, $aPageSize = null, $delta = 1, $currentPage = null )
  {
    if ( $delta == -1 )
    {
      if ( $currentPage == null )
      {
        die( "please give a currentPage also when using a reverse retrieval." );
      } 
      $curPage = $currentPage;
    } 
    else
    {
      $curPage = 0;
    } 

    if ( $aPageSize != null )
    {
      $pageSize = $aPageSize;
    } 
    else
    {
      if ( $this->_pageSize != null )
      {
        $pageSize = $this->_pageSize;
      } 
      else
      {
        $pageSize = $this->_session->getPageSize();
      } 
    } 

    if ( $pageSize <= 0 )
    { 
      // be sure to have a minimal pageSize of 1
      // make not really sense, but is an error proven value
      $pageSize = 1;
    } 
    if ( $pageSize != 25 || $pageSize != 50 || $pageSize != 100 || $pageSize != 200 )
    {
      $pageSize = 200;
    } 

    $res = $this->QueryWithPages( $detailLevel, $curPage, $pageSize ); 
    // token-mode
    if ( $res->getHasRefreshedToken() )
    {
      $s = $this->_session;
      $s->setRequestToken( $res->getRefreshedToken() );

      $ap = $this->_apiCaller;
      $s = $ap->_session;
      $s->setRequestToken( $res->getRefreshedToken() );
    } 
    if ( $res->isGood() )
    {
      $curPage = $curPage + $delta;
      while ( $res->getNumberOfPages() > $curPage )
      {
        $res = $this->QueryWithPages( $detailLevel, $curPage, $pageSize ); 
        // token-mode
        if ( $res->getHasRefreshedToken() )
        {
          $s = $this->_session;
          $s->setRequestToken( $res->getRefreshedToken() );

          $ap = $this->_apiCaller;
          $s = $ap->_session;
          $s->setRequestToken( $res->getRefreshedToken() );
        } 
        if ( $res->anyErrors() )
        { 
          // we had errors so finish the loop
          break;
        } 
        if ( $res->hasUserBreak() )
        { 
          // the retrieval process was aborted
          // e.g. the data limit had reached
          break;
        } 
        $curPage = $curPage + $delta;
      } 
    } 

    return $res;
  } 

  /**
   * 
   * @access public 
   * @param number $detailLevel 
   * @param number $curPage 
   * @param number $pageSize 
   * @return Ebay _Result
   */
  function QueryWithPages( $detailLevel, $curPage, $pageSize ) // prepared for overwrite, should not be called directly
  {
    die( "prepared for overwrite, no direct call here" );
  } 

  /**
   * 
   * @access public 
   * @return array 
   */
  function getResultList()
  {
    return $this->_resultList;
  } 

  /**
   * 
   * @access public 
   * @return <unspecified>
   */
  function getResultInfo()
  {
    return $this->_resultInfo;
  } 

  /**
   * 
   * @access public 
   * @param Ebay $ _Session $session
   * @return void 
   */
  function Ebay_QueryBase( $session )
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_QueryBase::_init();
    $this->_session = $session;
    $this->_apiCaller = new Ebay_ApiCaller( $this->_session );
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
   * array2xmlstruct = lets xmlTag be the element-name of an surrounding xml-tag, $key is handled as the key for an array property for each entry an extra row is added
   * @param string $classPropertyName specifies the name of the property in the object (so _props array index). If not given the parameter key is assumed as the property name also.
   * @param string $xmlTag give the ability to surround array data in given tag, used in conjunction with datatype = 'array2xmlstruct'
   * @return void 
   */
  function addToParamsIfSet( &$params, $key, $datatype = 'default', $classPropertyName = null, $xmlTag = null )
  {
    if ( $classPropertyName != null )
    { 
      // get the data from the index given in $classPropertyName
      $thePropValue = $this->_getProp( $classPropertyName );
      $thePropKey = $classPropertyName;
    } 
    else
    {
      $thePropValue = $this->_getProp( $key );
      $thePropKey = $key;
    } 

    if ( array_key_exists( $thePropKey, $this->_props ) )
    {
      if ( $classPropertyName != null )
      { 
        // get the data from the index given in $classPropertyName
        $thePropValue = $this->_getProp( $classPropertyName );
      } 
      else
      {
        $thePropValue = $this->_getProp( $key );
      } 

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
          case 'array2xmlstruct':
            {
              $dataToAdd = "";
              foreach ( $thePropValue as $entry )
              {
                $dataToAdd = '<' . $key . '>' . $entry . '</' . $key . '>';
              } 
              $params["$xmlTag"] = $dataToAdd;
            } 
            break;
        } 
      } 
    } 
    else
    {
      print_r( "Do not have the property " . $key . "(addToParamsIfSet)" );
    } 
  } 

  /**
   * rounds up to the next integer number
   * 
   * @access private 
   * @param number $x 
   * @return number 
   */
  function _roundUp( $x )
  {
    $r = round( $x );
    $d = ( $x - $r );

    if ( $d > 0 )
    {
      $x = ( $r + 1 );
    } 
    return $x;
  } 

  /**
   * Gives a way to limit the size of the resultList. Acutally if set the attribute will break out when this limit is reached. Anyway the breakout will only happen after the current page is retrieved, e.g. using a pagesize of 100 and a limit of 90 might result in a list containing 100 elements anyway.
   * 
   * @access public 
   * @param number $value 
   * @return void 
   */
  function setResultLimit( $value )
  {
    $this->_resultLimit = $value;
  } 

  /**
   * Checks if the size of the current resultList should break the operation. This function will check againt _resultLimit set by setResultLimit (@see setResultLimit).
   * checkLimiter will return true (1) if the current operation has to aborted (the flag in the result object will be set accordently) or false (0) if the limit is not reached and the retrieval should be continued.
   * 
   * @access public 
   * @param Ebay $ _Result $res
   * @return boolean 
   */
  function checkLimiter( &$res )
  {
    if ( $this->_resultLimit > 0 )
    {
      if ( count( $this->_resultList ) >= $this->_resultLimit )
      {
        $res->setUserBreak();
        return true;
      } 
    } 

    return false;
  } 

  /**
   * Regarding all paged resultsets the size of the page is normally retrieved by property PageSize within the Session object. Some calls needs to be adapted to new limits (e.g. GetSearchResults, as here the limit is 100). If using setPageSize the default from the session is overwriten by this value.
   * 
   * @access public 
   * @param number $value 
   * @return void 
   */
  function setPageSize( $value )
  {
    $this->_pageSize = $value;
  } 
} 

?>
	
		
		
		