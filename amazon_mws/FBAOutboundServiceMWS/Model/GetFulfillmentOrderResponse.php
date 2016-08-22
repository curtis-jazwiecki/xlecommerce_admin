<?php
/** 
 *  PHP Version 5
 * CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
 *
 *  @category    Amazon
 *  @package     FBAOutboundServiceMWS
 *  @copyright   Copyright 2009 Amazon.com, Inc. All Rights Reserved.
 *  @link        http://mws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2010-10-01
 */
/******************************************************************************* 
 * 
 *  FBA Outbound Service MWS PHP5 Library
 *  Generated: Fri Oct 22 09:51:48 UTC 2010
 * 
 */

/**
 *  @see FBAOutboundServiceMWS_Model
 */
require_once ('FBAOutboundServiceMWS/Model.php');  

    

/**
 * FBAOutboundServiceMWS_Model_GetFulfillmentOrderResponse
 * 
 * Properties:
 * <ul>
 * 
 * <li>GetFulfillmentOrderResult: FBAOutboundServiceMWS_Model_GetFulfillmentOrderResult</li>
 * <li>ResponseMetadata: FBAOutboundServiceMWS_Model_ResponseMetadata</li>
 *
 * </ul>
 */ 
class FBAOutboundServiceMWS_Model_GetFulfillmentOrderResponse extends FBAOutboundServiceMWS_Model
{


    /**
     * Construct new FBAOutboundServiceMWS_Model_GetFulfillmentOrderResponse
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>GetFulfillmentOrderResult: FBAOutboundServiceMWS_Model_GetFulfillmentOrderResult</li>
     * <li>ResponseMetadata: FBAOutboundServiceMWS_Model_ResponseMetadata</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'GetFulfillmentOrderResult' => array('FieldValue' => null, 'FieldType' => 'FBAOutboundServiceMWS_Model_GetFulfillmentOrderResult'),
        'ResponseMetadata' => array('FieldValue' => null, 'FieldType' => 'FBAOutboundServiceMWS_Model_ResponseMetadata'),
        );
        parent::__construct($data);
    }

       
    /**
     * Construct FBAOutboundServiceMWS_Model_GetFulfillmentOrderResponse from XML string
     * 
     * @param string $xml XML string to construct from
     * @return FBAOutboundServiceMWS_Model_GetFulfillmentOrderResponse 
     */
    public static function fromXML($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
    	$xpath->registerNamespace('a', 'http://mws.amazonaws.com/FulfillmentOutboundShipment/2010-10-01/');
        $response = $xpath->query('//a:GetFulfillmentOrderResponse');
        if ($response->length == 1) {
            return new FBAOutboundServiceMWS_Model_GetFulfillmentOrderResponse(($response->item(0))); 
        } else {
            throw new Exception ("Unable to construct FBAOutboundServiceMWS_Model_GetFulfillmentOrderResponse from provided XML. 
                                  Make sure that GetFulfillmentOrderResponse is a root element");
        }
          
    }
    
    /**
     * Gets the value of the GetFulfillmentOrderResult.
     * 
     * @return GetFulfillmentOrderResult GetFulfillmentOrderResult
     */
    public function getGetFulfillmentOrderResult() 
    {
        return $this->_fields['GetFulfillmentOrderResult']['FieldValue'];
    }

    /**
     * Sets the value of the GetFulfillmentOrderResult.
     * 
     * @param GetFulfillmentOrderResult GetFulfillmentOrderResult
     * @return void
     */
    public function setGetFulfillmentOrderResult($value) 
    {
        $this->_fields['GetFulfillmentOrderResult']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the GetFulfillmentOrderResult  and returns this instance
     * 
     * @param GetFulfillmentOrderResult $value GetFulfillmentOrderResult
     * @return FBAOutboundServiceMWS_Model_GetFulfillmentOrderResponse instance
     */
    public function withGetFulfillmentOrderResult($value)
    {
        $this->setGetFulfillmentOrderResult($value);
        return $this;
    }


    /**
     * Checks if GetFulfillmentOrderResult  is set
     * 
     * @return bool true if GetFulfillmentOrderResult property is set
     */
    public function isSetGetFulfillmentOrderResult()
    {
        return !is_null($this->_fields['GetFulfillmentOrderResult']['FieldValue']);

    }

    /**
     * Gets the value of the ResponseMetadata.
     * 
     * @return ResponseMetadata ResponseMetadata
     */
    public function getResponseMetadata() 
    {
        return $this->_fields['ResponseMetadata']['FieldValue'];
    }

    /**
     * Sets the value of the ResponseMetadata.
     * 
     * @param ResponseMetadata ResponseMetadata
     * @return void
     */
    public function setResponseMetadata($value) 
    {
        $this->_fields['ResponseMetadata']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ResponseMetadata  and returns this instance
     * 
     * @param ResponseMetadata $value ResponseMetadata
     * @return FBAOutboundServiceMWS_Model_GetFulfillmentOrderResponse instance
     */
    public function withResponseMetadata($value)
    {
        $this->setResponseMetadata($value);
        return $this;
    }


    /**
     * Checks if ResponseMetadata  is set
     * 
     * @return bool true if ResponseMetadata property is set
     */
    public function isSetResponseMetadata()
    {
        return !is_null($this->_fields['ResponseMetadata']['FieldValue']);

    }



    /**
     * XML Representation for this object
     * 
     * @return string XML for this object
     */
    public function toXML() 
    {
        $xml = "";
        $xml .= "<GetFulfillmentOrderResponse xmlns=\"http://mws.amazonaws.com/FulfillmentOutboundShipment/2010-10-01/\">";
        $xml .= $this->_toXMLFragment();
        $xml .= "</GetFulfillmentOrderResponse>";
        return $xml;
    }

}