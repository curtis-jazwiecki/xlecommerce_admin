<?php
/** 
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     MarketplaceWebServiceSellers
 *  @copyright   Copyright 2009 Amazon Technologies, Inc.
 *  @link        http://aws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2011-07-01
 */
/******************************************************************************* 
 *  
 *  Marketplace Web Service Sellers PHP5 Library
 *  Generated: Tue Jul 05 17:50:53 GMT 2011
 * 
 */

/**
 *  @see MarketplaceWebServiceSellers_Model
 */
require_once ('MarketplaceWebServiceSellers/Model.php');  

    

/**
 * MarketplaceWebServiceSellers_Model_Participation
 * 
 * Properties:
 * <ul>
 * 
 * <li>MarketplaceId: string</li>
 * <li>SellerId: string</li>
 * <li>HasSellerSuspendedListings: HasSellerSuspendedListingsEnum</li>
 *
 * </ul>
 */ 
class MarketplaceWebServiceSellers_Model_Participation extends MarketplaceWebServiceSellers_Model
{


    /**
     * Construct new MarketplaceWebServiceSellers_Model_Participation
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>MarketplaceId: string</li>
     * <li>SellerId: string</li>
     * <li>HasSellerSuspendedListings: HasSellerSuspendedListingsEnum</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'MarketplaceId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'SellerId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'HasSellerSuspendedListings' => array('FieldValue' => null, 'FieldType' => 'HasSellerSuspendedListingsEnum'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the MarketplaceId property.
     * 
     * @return string MarketplaceId
     */
    public function getMarketplaceId() 
    {
        return $this->_fields['MarketplaceId']['FieldValue'];
    }

    /**
     * Sets the value of the MarketplaceId property.
     * 
     * @param string MarketplaceId
     * @return this instance
     */
    public function setMarketplaceId($value) 
    {
        $this->_fields['MarketplaceId']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the MarketplaceId and returns this instance
     * 
     * @param string $value MarketplaceId
     * @return MarketplaceWebServiceSellers_Model_Participation instance
     */
    public function withMarketplaceId($value)
    {
        $this->setMarketplaceId($value);
        return $this;
    }


    /**
     * Checks if MarketplaceId is set
     * 
     * @return bool true if MarketplaceId  is set
     */
    public function isSetMarketplaceId()
    {
        return !is_null($this->_fields['MarketplaceId']['FieldValue']);
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
     * @return MarketplaceWebServiceSellers_Model_Participation instance
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
     * Gets the value of the HasSellerSuspendedListings property.
     * 
     * @return HasSellerSuspendedListingsEnum HasSellerSuspendedListings
     */
    public function getHasSellerSuspendedListings() 
    {
        return $this->_fields['HasSellerSuspendedListings']['FieldValue'];
    }

    /**
     * Sets the value of the HasSellerSuspendedListings property.
     * 
     * @param HasSellerSuspendedListingsEnum HasSellerSuspendedListings
     * @return this instance
     */
    public function setHasSellerSuspendedListings($value) 
    {
        $this->_fields['HasSellerSuspendedListings']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the HasSellerSuspendedListings and returns this instance
     * 
     * @param HasSellerSuspendedListingsEnum $value HasSellerSuspendedListings
     * @return MarketplaceWebServiceSellers_Model_Participation instance
     */
    public function withHasSellerSuspendedListings($value)
    {
        $this->setHasSellerSuspendedListings($value);
        return $this;
    }


    /**
     * Checks if HasSellerSuspendedListings is set
     * 
     * @return bool true if HasSellerSuspendedListings  is set
     */
    public function isSetHasSellerSuspendedListings()
    {
        return !is_null($this->_fields['HasSellerSuspendedListings']['FieldValue']);
    }




}
