<?php
/** 
 *  PHP Version 5
 * CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
 *
 *  @category    Amazon
 *  @package     FBAInventoryServiceMWS
 *  @copyright   Copyright 2009 Amazon.com, Inc. All Rights Reserved.
 *  @link        http://mws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2010-10-01
 */
/******************************************************************************* 
 * 
 *  FBA Inventory Service MWS PHP5 Library
 *  Generated: Fri Oct 22 09:52:21 UTC 2010
 * 
 */

/**
 *  @see FBAInventoryServiceMWS_Model
 */
require_once ('FBAInventoryServiceMWS/Model.php');  

    

/**
 * FBAInventoryServiceMWS_Model_InventorySupplyDetailList
 * 
 * Properties:
 * <ul>
 * 
 * <li>member: FBAInventoryServiceMWS_Model_InventorySupplyDetail</li>
 *
 * </ul>
 */ 
class FBAInventoryServiceMWS_Model_InventorySupplyDetailList extends FBAInventoryServiceMWS_Model
{


    /**
     * Construct new FBAInventoryServiceMWS_Model_InventorySupplyDetailList
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>member: FBAInventoryServiceMWS_Model_InventorySupplyDetail</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'member' => array('FieldValue' => array(), 'FieldType' => array('FBAInventoryServiceMWS_Model_InventorySupplyDetail')),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the member.
     * 
     * @return array of InventorySupplyDetail member
     */
    public function getmember() 
    {
        return $this->_fields['member']['FieldValue'];
    }

    /**
     * Sets the value of the member.
     * 
     * @param mixed InventorySupplyDetail or an array of InventorySupplyDetail member
     * @return this instance
     */
    public function setmember($member) 
    {
        if (!$this->_isNumericArray($member)) {
            $member =  array ($member);    
        }
        $this->_fields['member']['FieldValue'] = $member;
        return $this;
    }


    /**
     * Sets single or multiple values of member list via variable number of arguments. 
     * For example, to set the list with two elements, simply pass two values as arguments to this function
     * <code>withmember($member1, $member2)</code>
     * 
     * @param InventorySupplyDetail  $inventorySupplyDetailArgs one or more member
     * @return FBAInventoryServiceMWS_Model_InventorySupplyDetailList  instance
     */
    public function withmember($inventorySupplyDetailArgs)
    {
        foreach (func_get_args() as $member) {
            $this->_fields['member']['FieldValue'][] = $member;
        }
        return $this;
    }   



    /**
     * Checks if member list is non-empty
     * 
     * @return bool true if member list is non-empty
     */
    public function isSetmember()
    {
        return count ($this->_fields['member']['FieldValue']) > 0;
    }




}