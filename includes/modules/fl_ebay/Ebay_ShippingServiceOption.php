<?php

require_once( 'Ebay_Defines.php' );

/**
 */
class Ebay_ShippingServiceOption
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
   * Read accessor of Carrier. 
   * Shipping carrier for the item. The possible values depend also an the site where to list. Please consult also the international item matrix (http://developer.ebay.com/DevZone/docs/API_Doc/Appendixes/AppendixN.htm)
   * 
   * @access public 
   * @return number Value of the Carrier property
   */
  function getCarrier()
  {
    return $this->_props['Carrier'];
  } 

  /**
   * Write accessor of Carrier.  
   * Shipping carrier for the item. The possible values depend also an the site where to list. Please consult also the international item matrix (http://developer.ebay.com/DevZone/docs/API_Doc/Appendixes/AppendixN.htm)
   * 
   * @access public 
   * @param number $value The new value for the Carrier property
   * @return void 
   */
  function setCarrier( $value )
  {
    $this->_props['Carrier'] = $value;
  } 

  /**
   * Read accessor of AdditionalCost. 
   * Specifies the shipping cost for each item beyond the first sold to the same buyer. Applicable if ShippingType = 1. Do not specify this tag if ShippingType = 2 (i.e., calculated shipping is selected). Should be zero for single-item listings. Also see Specifying Shipping Costs when Listing or Revising an Item. Default value is 0.00. See International Item Matrix for Money data type site differences. Do not specify a value in this field unless CheckoutDetailsSpecified = true (1). (See CheckoutDetailsSpecified Note.) Shipping arguments are not applicable for Real Estate listings.
   * 
   * @access public 
   * @return money Value of the AdditionalCost property
   */
  function getAdditionalCost()
  {
    return $this->_props['AdditionalCost'];
  } 

  /**
   * Write accessor of AdditionalCost.  
   * Specifies the shipping cost for each item beyond the first sold to the same buyer. Applicable if ShippingType = 1. Do not specify this tag if ShippingType = 2 (i.e., calculated shipping is selected). Should be zero for single-item listings. Also see Specifying Shipping Costs when Listing or Revising an Item. Default value is 0.00. See International Item Matrix for Money data type site differences. Do not specify a value in this field unless CheckoutDetailsSpecified = true (1). (See CheckoutDetailsSpecified Note.) Shipping arguments are not applicable for Real Estate listings.
   * 
   * @access public 
   * @param money $value The new value for the AdditionalCost property
   * @return void 
   */
  function setAdditionalCost( $value )
  {
    $this->_props['AdditionalCost'] = $value;
  } 

  /**
   * Read accessor of Cost. 
   * Specifies the shipping cost for the first (or only) item sold to the buyer. Shipping costs for additional items are specified in ..ShippingServiceOption.ShippingServiceAdditionalCost. Applicable if ShippingType = 1. Do not specify this tag if ShippingType = 2 (i.e., calculated shipping is selected). Default value is 0.00. Also see Specifying Shipping Costs when Listing or Revising an Item. See International Item Matrix for Money data type site differences. Do not specify a value in this field unless CheckoutDetailsSpecified = true (1). (See CheckoutDetailsSpecified Note.) Shipping arguments are not applicable for Real Estate listings..
   * 
   * @access public 
   * @return money Value of the Cost property
   */
  function getCost()
  {
    return $this->_props['Cost'];
  } 

  /**
   * Write accessor of Cost.  
   * Specifies the shipping cost for the first (or only) item sold to the buyer. Shipping costs for additional items are specified in ..ShippingServiceOption.ShippingServiceAdditionalCost. Applicable if ShippingType = 1. Do not specify this tag if ShippingType = 2 (i.e., calculated shipping is selected). Default value is 0.00. Also see Specifying Shipping Costs when Listing or Revising an Item. See International Item Matrix for Money data type site differences. Do not specify a value in this field unless CheckoutDetailsSpecified = true (1). (See CheckoutDetailsSpecified Note.) Shipping arguments are not applicable for Real Estate listings..
   * 
   * @access public 
   * @param money $value The new value for the Cost property
   * @return void 
   */
  function setCost( $value )
  {
    $this->_props['Cost'] = $value;
  } 

  /**
   * Read accessor of Priority. 
   * For multiple shipping service options, ShippingServicePriority specifieds the priority of choices for this option. The valid values are:
   * 1 = First Choice
   * 2 = Second Choice
   * 3 = Third Choice
   * 
   * @access public 
   * @return number Value of the Priority property
   */
  function getPriority()
  {
    return $this->_props['Priority'];
  } 

  /**
   * Write accessor of Priority.  
   * For multiple shipping service options, ShippingServicePriority specifieds the priority of choices for this option. The valid values are:
   * 1 = First Choice
   * 2 = Second Choice
   * 3 = Third Choice
   * 
   * @access public 
   * @param number $value The new value for the Priority property
   * @return void 
   */
  function setPriority( $value )
  {
    $this->_props['Priority'] = $value;
  } 

  /**
   * Read accessor of InsuranceCost. 
   * Amount of insurance. Applicable if ShippingType = 1 or 2. (See Managing Item Shipping Costs.) Actually only returned by a GetItem Call
   * 
   * @access public 
   * @return number Value of the InsuranceCost property
   */
  function getInsuranceCost()
  {
    return $this->_props['InsuranceCost'];
  } 

  /**
   * Write accessor of InsuranceCost.  
   * Amount of insurance. Applicable if ShippingType = 1 or 2. (See Managing Item Shipping Costs.) Actually only returned by a GetItem Call
   * 
   * @access public 
   * @param number $value The new value for the InsuranceCost property
   * @return void 
   */
  function setInsuranceCost( $value )
  {
    $this->_props['InsuranceCost'] = $value;
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['Carrier'] = null;
    $this->_props['AdditionalCost'] = null;
    $this->_props['Cost'] = null;
    $this->_props['Priority'] = null;
    $this->_props['InsuranceCost'] = null;
  } 

  /**
   * Use this Method to add an ShippingServiceOption to the Item.
   * 
   * @access public 
   * @return void 
   */
  function Ebay_ShippingServiceOption()
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_ShippingServiceOption::_init(); 
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
    foreach ( $arrData as $data )
    {
      $tag = $data['tag'];
      $type = $data['type'];

      switch ( $tag )
      {
        default: 
          // print_r( "dangling tag (ShippingService) : " . $tag . "<br>\r\n" );
          break; 
        // all directly maped data
        case 'ShippingService':
          $this->_setProp( 'Carrier', $data['value'] );
          break;
        case 'ShippingServiceAdditionalCost':
          $this->_setProp( 'AdditionalCost', $data['value'] );
          break;
        case 'ShippingServiceCost':
          $this->_setProp( 'Cost', $data['value'] );
          break;
        case 'ShippingServicePriority':
          $this->_setProp( 'Priority', $data['value'] );
          break;
        case 'ShippingInsuranceCost':
          $this->_setProp( 'InsuranceCost', $data['value'] );
          break;
      } 
    } 
    return true;
  } 

  /**
   * return the XML part of an AttributeSet for usage in various situations, set mode apropricate to the situation
   * 
   * @access public 
   * @param string $mode SelectedAttributes ->  for attributeRendering (SelectedAttribute-Tag)
   * AddItem -> for usage in a AddItem Calls ( Ebay_Item->Add() )
   * @return void 
   */
  function renderToXml( $mode = null )
  { 
    // the cdata-tag are splitted off to not confuse the generator
    $renderXml = '';
    $renderXml .= '<ShippingServiceOption>';

    $renderXml .= '<ShippingService>';
    $renderXml .= $this->_props['Carrier'];
    $renderXml .= '</ShippingService>';

    $renderXml .= '<ShippingServiceAdditionalCost>';
    $renderXml .= $this->_props['AdditionalCost'];
    $renderXml .= '</ShippingServiceAdditionalCost>';

    $renderXml .= '<ShippingServiceCost>';
    $renderXml .= $this->_props['Cost'];
    $renderXml .= '</ShippingServiceCost>';

    $renderXml .= '<ShippingServicePriority>';
    $renderXml .= $this->_props['Priority'];
    $renderXml .= '</ShippingServicePriority>';

    $renderXml .= '<ShippingInsuranceCost>';
    $renderXml .= $this->_props['InsuranceCost'];
    $renderXml .= '</ShippingInsuranceCost>';

    $renderXml .= '</ShippingServiceOption>';

    return $renderXml;
  } 
} 

?>
	
		
		
		