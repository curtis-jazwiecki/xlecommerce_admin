<?php
/**
 * CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc. 
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     FBAInboundServiceMWS
 *  @copyright   Copyright 2009 Amazon.com, Inc. All Rights Reserved.
 *  @link        http://mws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2010-10-01
 */
/******************************************************************************* 
 * 
 *  FBA Inbound Service MWS PHP5 Library
 *  Generated: Fri Oct 22 09:52:55 UTC 2010
 * 
 */

/**
 *  @see FBAInboundServiceMWS_Model
 */
require_once ('FBAInboundServiceMWS/Model.php');  

    

/**
 * FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest
 * 
 * Properties:
 * <ul>
 * 
 * <li>SellerId: string</li>
 * <li>Marketplace: string</li>
 * <li>ShipFromAddress: FBAInboundServiceMWS_Model_Address</li>
 * <li>LabelPrepPreference: string</li>
 * <li>InboundShipmentPlanRequestItems: FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItemList</li>
 *
 * </ul>
 */ 
class FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest extends FBAInboundServiceMWS_Model
{


    /**
     * Construct new FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>SellerId: string</li>
     * <li>Marketplace: string</li>
     * <li>ShipFromAddress: FBAInboundServiceMWS_Model_Address</li>
     * <li>LabelPrepPreference: string</li>
     * <li>InboundShipmentPlanRequestItems: FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItemList</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'SellerId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'Marketplace' => array('FieldValue' => null, 'FieldType' => 'string'),
        'ShipFromAddress' => array('FieldValue' => null, 'FieldType' => 'FBAInboundServiceMWS_Model_Address'),
        'LabelPrepPreference' => array('FieldValue' => null, 'FieldType' => 'string'),
        'InboundShipmentPlanRequestItems' => array('FieldValue' => null, 'FieldType' => 'FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItemList'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the SellerId property.
     * 
     * @return string SellerId
     */
    public function getSellerId() 
    {
        return $this->_fields['SellerId']['FieldValue'];
    }

    /**
     * Sets the value of the SellerId property.
     * 
     * @param string SellerId
     * @return this instance
     */
    public function setSellerId($value) 
    {
        $this->_fields['SellerId']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the SellerId and returns this instance
     * 
     * @param string $value SellerId
     * @return FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest instance
     */
    public function withSellerId($value)
    {
        $this->setSellerId($value);
        return $this;
    }


    /**
     * Checks if SellerId is set
     * 
     * @return bool true if SellerId  is set
     */
    public function isSetSellerId()
    {
        return !is_null($this->_fields['SellerId']['FieldValue']);
    }

    /**
     * Gets the value of the Marketplace property.
     * 
     * @return string Marketplace
     */
    public function getMarketplace() 
    {
        return $this->_fields['Marketplace']['FieldValue'];
    }

    /**
     * Sets the value of the Marketplace property.
     * 
     * @param string Marketplace
     * @return this instance
     */
    public function setMarketplace($value) 
    {
        $this->_fields['Marketplace']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the Marketplace and returns this instance
     * 
     * @param string $value Marketplace
     * @return FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest instance
     */
    public function withMarketplace($value)
    {
        $this->setMarketplace($value);
        return $this;
    }


    /**
     * Checks if Marketplace is set
     * 
     * @return bool true if Marketplace  is set
     */
    public function isSetMarketplace()
    {
        return !is_null($this->_fields['Marketplace']['FieldValue']);
    }

    /**
     * Gets the value of the ShipFromAddress.
     * 
     * @return Address ShipFromAddress
     */
    public function getShipFromAddress() 
    {
        return $this->_fields['ShipFromAddress']['FieldValue'];
    }

    /**
     * Sets the value of the ShipFromAddress.
     * 
     * @param Address ShipFromAddress
     * @return void
     */
    public function setShipFromAddress($value) 
    {
        $this->_fields['ShipFromAddress']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ShipFromAddress  and returns this instance
     * 
     * @param Address $value ShipFromAddress
     * @return FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest instance
     */
    public function withShipFromAddress($value)
    {
        $this->setShipFromAddress($value);
        return $this;
    }


    /**
     * Checks if ShipFromAddress  is set
     * 
     * @return bool true if ShipFromAddress property is set
     */
    public function isSetShipFromAddress()
    {
        return !is_null($this->_fields['ShipFromAddress']['FieldValue']);

    }

    /**
     * Gets the value of the LabelPrepPreference property.
     * 
     * @return string LabelPrepPreference
     */
    public function getLabelPrepPreference() 
    {
        return $this->_fields['LabelPrepPreference']['FieldValue'];
    }

    /**
     * Sets the value of the LabelPrepPreference property.
     * 
     * @param string LabelPrepPreference
     * @return this instance
     */
    public function setLabelPrepPreference($value) 
    {
        $this->_fields['LabelPrepPreference']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the LabelPrepPreference and returns this instance
     * 
     * @param string $value LabelPrepPreference
     * @return FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest instance
     */
    public function withLabelPrepPreference($value)
    {
        $this->setLabelPrepPreference($value);
        return $this;
    }


    /**
     * Checks if LabelPrepPreference is set
     * 
     * @return bool true if LabelPrepPreference  is set
     */
    public function isSetLabelPrepPreference()
    {
        return !is_null($this->_fields['LabelPrepPreference']['FieldValue']);
    }

    /**
     * Gets the value of the InboundShipmentPlanRequestItems.
     * 
     * @return InboundShipmentPlanRequestItemList InboundShipmentPlanRequestItems
     */
    public function getInboundShipmentPlanRequestItems() 
    {
        return $this->_fields['InboundShipmentPlanRequestItems']['FieldValue'];
    }

    /**
     * Sets the value of the InboundShipmentPlanRequestItems.
     * 
     * @param InboundShipmentPlanRequestItemList InboundShipmentPlanRequestItems
     * @return void
     */
    public function setInboundShipmentPlanRequestItems($value) 
    {
        $this->_fields['InboundShipmentPlanRequestItems']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the InboundShipmentPlanRequestItems  and returns this instance
     * 
     * @param InboundShipmentPlanRequestItemList $value InboundShipmentPlanRequestItems
     * @return FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest instance
     */
    public function withInboundShipmentPlanRequestItems($value)
    {
        $this->setInboundShipmentPlanRequestItems($value);
        return $this;
    }


    /**
     * Checks if InboundShipmentPlanRequestItems  is set
     * 
     * @return bool true if InboundShipmentPlanRequestItems property is set
     */
    public function isSetInboundShipmentPlanRequestItems()
    {
        return !is_null($this->_fields['InboundShipmentPlanRequestItems']['FieldValue']);

    }




}