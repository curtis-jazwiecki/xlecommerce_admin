<?php

require_once( 'Ebay_Defines.php' );
require_once( 'Ebay_CategoryQueryInfo.php' );
require_once( 'Ebay_Category.php' );

require_once( 'Ebay_QueryBase.php' );

/**
 */
class Ebay_CategoryQuery
extends Ebay_QueryBase
{
  /**
   * Write accessor of CategorySiteId.
   * 
   * @access public 
   * @param number $value The new value for the CategorySiteId property
   * @return void 
   */
  function setCategorySiteId( $value )
  {
    $this->_props['CategorySiteId'] = $value;
  } 

  /**
   * Write accessor of CategoryParent.
   * 
   * @access public 
   * @param number $value The new value for the CategoryParent property
   * @return void 
   */
  function setCategoryParent( $value )
  {
    $this->_props['CategoryParent'] = $value;
  } 

  /**
   * Write accessor of LevelLimit.
   * 
   * @access public 
   * @param number $value The new value for the LevelLimit property
   * @return void 
   */
  function setLevelLimit( $value )
  {
    $this->_props['LevelLimit'] = $value;
  } 

  /**
   * Write accessor of ViewAllNodes.
   * 
   * @access public 
   * @param boolean $value The new value for the ViewAllNodes property
   * @return void 
   */
  function setViewAllNodes( $value )
  {
    $this->_props['ViewAllNodes'] = $value;
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['CategorySiteId'] = EBAY_NOTHING;
    $this->_props['CategoryParent'] = EBAY_NOTHING;
    $this->_props['LevelLimit'] = EBAY_NOTHING;
    $this->_props['ViewAllNodes'] = EBAY_NOTHING;
  } 

  /**
   * 
   * @access public 
   * @param Ebay $ _Session $session
   * @return void 
   */
  function Ebay_CategoryQuery( $session )
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_CategoryQuery::_init();
    $this->Ebay_QueryBase( $session );
    $this->_resultInfo = new Ebay_CategoryQueryInfo();
  } 

  /**
   * 
   * @access public 
   * @param number $detailLevel 
   * @param number $curPage 
   * @param number $pageSize 
   * @return Ebay _Result
   */
  function QueryWithPages( $detailLevel, $curPage, $pageSize )
  {
    $params = array();
    $this->addToParamsIfSet( $params, 'CategorySiteId' );
    $this->addToParamsIfSet( $params, 'CategoryParent' );
    $this->addToParamsIfSet( $params, 'LevelLimit' );
    $this->addToParamsIfSet( $params, 'ViewAllNodes' ); 
    // echo "params are : ";
    // print_r($params);
    $ret = $this->_apiCaller->call( 'GetCategories', $params, $detailLevel );
    if ( $ret->anyErrors() )
    {
      return $ret;
    } 
    else
    {
      $resultFragment = $ret->getXmlStructResultFragment( 'Categories' );

      $resData = array();
      foreach ( $resultFragment as $data )
      {
        switch ( $data['tag'] )
        {
          case 'Category':
            if ( $data['type'] == 'open' )
            {
              $resData = array();
            } 
            else
            {
              $cat = new Ebay_Category();
              $cat->InitFromDataStruct( $resData );
              $this->_resultList[] = $cat;

              if ( $this->checkLimiter( $ret ) )
              {
                return $ret;
              } 
            } 
            break;

          case 'CategoryCount':
          case 'UpdateGMTTime':
          case 'UpdateTime':
          case 'Version':
            $this->_resultInfo->_setProp( $data['tag'], $data['value'] );
            break;
          default:
            $resData[] = $data;
            break;
        } 
      } 
      // method does not needs paging
      $ret->_numberPages = 1;
      $this->_resultInfo->setResultCount( count( $this->_resultList ) );
      return $ret;
    } 
  } 
} 

?>
	
		
		
		