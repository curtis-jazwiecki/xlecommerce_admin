<?php
/*
 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require_once( 'Ebay_Defines.php' );
require_once( 'Ebay_Detail.php' );

require_once( 'Ebay_QueryBase.php' );

/**
 */
class Ebay_DetailQuery
extends Ebay_QueryBase
{
  /**
   * Write accessor to the DetailName.
   * The name of a list of details (e.g., SiteId) to retrieve. If Name is included but empty, an error will be returned.
   * 
   * @access public 
   * @param string $value The new value for the DetailName property
   * @param integer $index The index of the value to update. if $index = -1, the value is added to the end of list.
   * @return void 
   */
  function setDetailName( $value, $index = -1 )
  {
    if ( -1 == $index )
    {
      $index = count( $this->_props['DetailName'] );
    } 
    $this->_props['DetailName'][$index] = $value;
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['DetailName'] = array();
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
    $paramsFragment = '';
    if ( count( $this->_props['DetailName'] ) > 0 )
    {
      $paramsFragment .= '<Details>';

      foreach( $this->_props['DetailName'] as $detailName )
      {
        $paramsFragment .= '<Detail><Name>' . $detailName . '</Name></Detail>';
      } 

      $paramsFragment .= '</Details>';
    } 

    $ret = $this->_apiCaller->call( 'GeteBayDetails', $paramsFragment, $detailLevel );
    if ( $ret->isGood() )
    {
      $resultFragment = $ret->getXmlStructResultFragment( 'Details' );

      $resData = array();
      foreach ( $resultFragment as $data )
      {
        switch ( $data['tag'] )
        {
          case 'Detail':
            if ( $data['type'] == 'open' )
            {
              $resData = array();
            } 
            else
            {
              $detail = new Ebay_Detail();
              $detail->InitFromDataStruct( $resData );
              $this->_resultList[] = $detail;

              if ( $this->checkLimiter( $ret ) )
              {
                return $ret;
              } 
            } 
            break;
          default:
            $resData[] = $data;
            break;
        } 
      } 
      // method does not needs paging
      $ret->_numberPages = 1;
      $this->_resultInfo->setResultCount( count( $this->_resultList ) );
    } 
    return $ret;
  } 

  /**
   * 
   * @access public 
   * @param Ebay $ _Session $session
   * @return void 
   */
  function Ebay_DetailQuery( $session )
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_DetailQuery::_init();
    $this->Ebay_QueryBase( $session );
    $this->_resultInfo = new Ebay_QueryResultInfo();
  } 
} 

?>
	
		
		
		