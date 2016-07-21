<?php
/*
 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require_once( 'Ebay_Defines.php' );
require_once( 'Ebay_Attribute.php' );

/**
 */
class Ebay_AttributeSet
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
   * Read accessor to the Attributes.
   * Holds the various Attributes of this set. This is a collection of Ebay_Attribute objects
   * 
   * @access public 
   * @param integer $index The index of the value to return
   * @return Ebay _Attribute Value of the Attributes property
   */
  function getAttributes( $index )
  {
    return $this->_props['Attributes'][$index];
  } 

  /**
   * Return the amount of Attributes actually declared
   * 
   * @access public 
   * @return Ebay _Attribute Value of the Attributes property
   */
  function getAttributesCount()
  {
    return count( $this->_props['Attributes'] );
  } 
  /**
   * Returns a copy of the Attributes array
   * 
   * @access public 
   * @return array of Ebay_Attribute
   */
  function getAttributesArray()
  {
    return $this->_props['Attributes'];
  } 

  /**
   * Write accessor to the Attributes.
   * Holds the various Attributes of this set. This is a collection of Ebay_Attribute objects
   * 
   * @access public 
   * @param Ebay $ _Attribute $value The new value for the Attributes property
   * @param integer $index The index of the value to update. if $index = -1, the value is added to the end of list.
   * @return void 
   */
  function setAttributes( $value, $index = -1 )
  {
    if ( -1 == $index )
    {
      $index = count( $this->_props['Attributes'] );
    } 
    $this->_props['Attributes'][$index] = $value;
  } 

  /**
   * Read accessor of Id. 
   * Id of the AttributeSet
   * 
   * @access public 
   * @return number Value of the Id property
   */
  function getId()
  {
    return $this->_props['Id'];
  } 

  /**
   * Write accessor of Id.  
   * Id of the AttributeSet
   * 
   * @access public 
   * @param number $value The new value for the Id property
   * @return void 
   */
  function setId( $value )
  {
    $this->_props['Id'] = $value;
  } 

  /**
   * Read accessor of Version. 
   * Version of the AttributeSet (given by the version of the CharactericsSet)
   * 
   * @access public 
   * @return <unspecified> Value of the Version property
   */
  function getVersion()
  {
    return $this->_props['Version'];
  } 

  /**
   * Write accessor of Version.  
   * Version of the AttributeSet (given by the version of the CharactericsSet)
   * 
   * @access public 
   * @param  $ <unspecified> $value The new value for the Version property
   * @return void 
   */
  function setVersion( $value )
  {
    $this->_props['Version'] = $value;
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['Attributes'] = array();

    $this->_props['Id'] = null;
    $this->_props['Version'] = null;
  } 

  /**
   * 
   * @access public 
   * @return void 
   */
  function Ebay_AttributeSet()
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_AttributeSet::_init(); 
    // insert code here...
  } 

  /**
   * 
   * @access public 
   * @param array $post should pass in the $_POST array of an http-post
   * @return void 
   */
  function initFromHttpPost( $post )
  {
    $foundAttributes = array();

    /**
     * echo "<hr>";
     * foreach( $post as $var => $value )
     * {
     * echo $var . " => " . $value . "<br>";
     * }
     * echo "<hr>";
     */

    foreach( $post as $var => $value )
    {
      $attrib = array(); 
      // If the var is  the vcsid, use it to set in the Attributes set element
      if ( $var == 'vcsid' )
      {
        $csSetId = $value;
        $this->setId( $value );
      } elseif ( substr( $var, 0, 4 ) == 'attr' )
      {
        $parts = split( '_', $var );
        if ( count( $parts ) == 2 )
        { 
          // this is an IdValue
          // 
          // the format should be attrCSID_ATTID
          $attrib['CsId'] = substr( $parts[0], 4 );
          $attrib['AttId'] = $parts[1]; 
          // IdValue for the moment
          $attrib['AttType'] = 1; 
          // here we should get also multiple data divided by spaces
          $attrib['AttValue'] = $value; 
          // if the value is -6 this is the marker for an 'other'
          // value
          if ( $value == -6 )
          { 
            // do we already have the associated text-value ?
            if ( isset( $foundAttributes[$attrib['AttId']] ) )
            { 
              // be sure the set the right type
              $attrib = $foundAttributes[$attrib['AttId']];
              $attrib['AttType'] = -6;
              $foundAttributes[$attrib['AttId']] = $attrib;
            } 
            else
            { 
              // store the attribute
              // for the moment the AttValue is empty
              $attrib['AttValue'] = '';
              $attrib['AttType'] = -6;
              $foundAttributes[$attrib['AttId']] = $attrib;
            } 
          } 
          else
          {
            $foundAttributes[$attrib['AttId']] = $attrib;
          } 
        } elseif ( count( $parts ) == 3 )
        { 
          // This is a TextValue
          // the format should be attr_tCSID_ATTID
          $attrib['CsId'] = substr( $parts[1], 1 );
          $attrib['AttId'] = $parts[2]; 
          // type is literal for the moment
          $attrib['AttType'] = -3;
          if ( isset( $foundAttributes[$attrib['AttId']] ) )
          {
            $attribStored = $foundAttributes[$attrib['AttId']];
            if ( $attribStored['AttType'] == -6 )
            { 
              // if we already got the marker for 'other'
              // under this AttId we are only store the current
              // value under the existing entry
              $attribStored['AttValue'] = $value;
              $foundAttributes[$attrib['AttId']] = $attribStored;
            } 
          } 
          else
          {
            $foundAttributes[$attrib['AttId']] = $attrib;
          } 
        } elseif ( count( $parts ) == 4 )
        { 
          // this is DateValue
          // (might be also a 'required' attribute;anyway this will
          // be ignored here)
          // the format should be attr_dCSID_ATTID_meta
          if ( substr( $parts[1], 0, 1 ) == 'd' )
          {
            $attrib['CsId'] = substr( $parts[1], 1 );
            $attrib['AttId'] = $parts[2];

            $metaMarker = $parts[3];

            if ( $metaMarker == 'c' )
            { 
              // if is a date from a textBox, so store it as a TextValue
              $attrib['AttType'] = -3;
              $attrib['AttValue'] = $value;
            } 
            else
            {
              $attrib['AttType'] = -5; 
              // maybe we already have parts of the date information
              // stored
              if ( isset( $foundAttributes[$attrib['AttId']] ) )
              {
                $attribStored = $foundAttributes[$attrib['AttId']];
              } 
              else
              {
                $attribStored = $attrib;
              } 

              if ( $metaMarker == 'y' )
              {
                $attribStored['AttValueYear'] = $value;
              } elseif ( $metaMarker == 'd' )
              {
                $attribStored['AttValueDay'] = $value;
              } elseif ( $metaMarker == 'm' )
              {
                $attribStored['AttValueMonth'] = $value;
              } 
              $foundAttributes[$attrib['AttId']] = $attribStored;
            } 
          } 
        } 
      } 
    } 
    // echo "<hr><pre>";
    // print_r( $foundAttributes );
    // echo "</pre><hr>";
    foreach( $foundAttributes as $attId => $attrib )
    {
      $att = new Ebay_Attribute();
      $att->setId( $attrib['AttId'] );
      $att->setType( $attrib['AttType'] );
      switch ( $attrib['AttType'] )
      {
        case 1:
          $values = split( " ", $attrib['AttValue'] );
          foreach( $values as $singleValue )
          {
            $attValue = new Ebay_AttributeIdValue();
            $attValue->setIdValue( $singleValue );
            $att->setValues( $attValue );
          } 
          break;
        case -3:
          $attValue = new Ebay_AttributeTextValue();
          $attValue->setTextValue( $attrib['AttValue'] );
          $att->setValues( $attValue );
          break;
        case -10:
          $attValue = new Ebay_AttributeTextValue(); 
          // single dash value
          $attValue->setTextValue( '-' );
          $att->setValues( $attValue );
          break;
        case -6:
          $attValue = new Ebay_AttributeTextValue(); 
          // single other value (stored as TextValue)
          $attValue->setTextValue( $attrib['AttValue'] );
          $att->setValues( $attValue );
          break;
        case -5:
          $attValue = new Ebay_AttributeDateValue();
          if ( isset( $attrib['AttValueDay'] ) )
          {
            $attValue->setDayValue( $attrib['AttValueDay'] );
          } 
          if ( isset( $attrib['AttValueMonth'] ) )
          {
            $attValue->setMonthValue( $attrib['AttValueMonth'] );
          } 
          if ( isset( $attrib['AttValueYear'] ) )
          {
            $attValue->setYearValue( $attrib['AttValueYear'] );
          } 
          $att->setValues( $attValue );
          break;
      } 

      $this->_props['Attributes'][] = $att;
    } 
    // echo "<hr><pre>";
    // print_r( $this );
    // echo "</pre><hr>";
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
    $attribs = $this->_props['Attributes'];
    if ( $mode == 'SelectedAttributes' )
    {
      $renderXml .= '<SelectedAttributes>';
      $renderXml .= '<AttributeSet id="' . $this->getId() . '"';
      if ( $this->getVersion() != EBAY_NOTHING )
      {
        $renderXml .= ' version="' . $this->getVersion() . '"';
      } 
      $renderXml .= '>';
      foreach( $attribs as $att )
      {
        $renderXml .= '<Attribute id="' . $att->getId() . '">';
        switch ( $att->getType() )
        {
          case 1:
            $c = $att->getValuesCount();
            for( $i = 0;$i < $c;$i++ )
            {
              $attValue = $att->getValues( $i );
              $renderXml .= '<Value id="' . $attValue->getIdValue() . '"/>';
            } 
            break;
          case -3:
            $attValue = $att->getValues( 0 );
            $renderXml .= '<Value><Name><!' . '[CDATA[';
            $renderXml .= $attValue->getTextValue();
            $renderXml .= ']]' . '></Name></Value>';
            break;
          case -5:
            $attValue = $att->getValues( 0 );
            $attValue = $att->getValues( 0 );
            $renderXml .= '<Value>';
            $renderXml .= '<Month><!' . '[CDATA[' . $attValue->getMonthValue() . ']]' . '></Month>';
            $renderXml .= '<Day><!' . '[CDATA[' . $attValue->getDayValue() . ']]' . '></Day>';
            $renderXml .= '<Year><!' . '[CDATA[' . $attValue->getYearValue() . ']]' . '></Year>';
            $renderXml .= '</Value>';
            break;
          case -6:
            $attValue = $att->getValues( 0 );
            $renderXml .= '<Value id="-6"><Name><!' . '[CDATA[';
            $renderXml .= $attValue->getTextValue();
            $renderXml .= ']]' . '></Name></Value>';

            break;
          case -10:
            $renderXml .= '<Value id="-10"/>';
            break;
        } 
        $renderXml .= '</Attribute>';
      } 
      $renderXml .= '</AttributeSet>';
      $renderXml .= '</SelectedAttributes>';
    } elseif ( $mode == 'AddItem' )
    {
      $renderXml .= "<AttributeSet id='" . $this->getId() . "'";
      /**
       * // it seems at eBay always throws an error, when a version is passed in
       * // so just leave it out for the moment.
       * if ( $this->getVersion() != EBAY_NOTHING )
       * {
       * $renderXml .= " version='" . $this->getVersion() . "'";
       * }
       */
      $renderXml .= '>';
      foreach( $attribs as $att )
      {
        $renderXml .= '<Attribute id="' . $att->getId() . '">';
        $renderXml .= '<ValueList>';
        switch ( $att->getType() )
        {
          case 1:
            $c = $att->getValuesCount();
            for( $i = 0;$i < $c;$i++ )
            {
              $attValue = $att->getValues( $i );
              $renderXml .= '<Value id="' . $attValue->getIdValue() . '"/>';
            } 
            break;
          case -3:
            $attValue = $att->getValues( 0 );
            $renderXml .= '<Value><ValueLiteral><!' . '[CDATA[';
            $renderXml .= $attValue->getTextValue();
            $renderXml .= ']]' . '></ValueLiteral></Value>';
            break;
          case -5:
            $attValue = $att->getValues( 0 );
            $attValue = $att->getValues( 0 );
            $renderXml .= '<Value>';
            $renderXml .= '<ValueLiteral><!' . '[CDATA[';

            $any = false;
            if ( $attValue->getYearValue() != EBAY_NOTHING )
            {
              $renderXml .= $attValue->getYearValue();
              $any = true;
            } 

            if ( $attValue->getMonthValue() != EBAY_NOTHING )
            {
              if ( $any == true )
              {
                $renderXml .= '-';
              } 
              $renderXml .= $attValue->getMonthValue();
              $any = true;
            } 

            if ( $attValue->getDayValue() != EBAY_NOTHING )
            {
              if ( $any == true )
              {
                $renderXml .= '-';
              } 
              $renderXml .= $attValue->getDayValue();
              $any = true;
            } 

            $renderXml .= ']]' . '></ValueLiteral></Value>';
            break;
          case -6:
            $attValue = $att->getValues( 0 );
            $renderXml .= '<Value id="-6"><ValueLiteral><!' . '[CDATA[';
            $renderXml .= $attValue->getTextValue();
            $renderXml .= ']]' . '></ValueLiteral></Value>';

            break;
          case -10:
            $renderXml .= '<Value id="-10"/>';
            break;
        } 
        $renderXml .= '</ValueList>';
        $renderXml .= '</Attribute>';
      } 
      $renderXml .= '</AttributeSet>';
    } elseif ( $mode == 'QueryAttributes' )
    {
      foreach( $attribs as $att )
      {
        $renderXml .= '<Attribute id="' . $att->getId() . '">';
        $renderXml .= '<ValueList>';
        switch ( $att->getType() )
        {
          case 1:
            $c = $att->getValuesCount();
            for( $i = 0;$i < $c;$i++ )
            {
              $attValue = $att->getValues( $i );
              $renderXml .= '<Value id="' . $attValue->getIdValue() . '"/>';
            } 
            break;
          case -3:
            $attValue = $att->getValues( 0 );
            $renderXml .= '<Value><ValueLiteral><!' . '[CDATA[';
            $renderXml .= $attValue->getTextValue();
            $renderXml .= ']]' . '></ValueLiteral></Value>';
            break;
          case -5:
            $attValue = $att->getValues( 0 );
            $attValue = $att->getValues( 0 );
            $renderXml .= '<Value>';
            $renderXml .= '<ValueLiteral><!' . '[CDATA[';

            $any = false;
            if ( $attValue->getYearValue() != EBAY_NOTHING )
            {
              $renderXml .= $attValue->getYearValue();
              $any = true;
            } 

            if ( $attValue->getMonthValue() != EBAY_NOTHING )
            {
              if ( $any == true )
              {
                $renderXml .= '-';
              } 
              $renderXml .= $attValue->getMonthValue();
              $any = true;
            } 

            if ( $attValue->getDayValue() != EBAY_NOTHING )
            {
              if ( $any == true )
              {
                $renderXml .= '-';
              } 
              $renderXml .= $attValue->getDayValue();
              $any = true;
            } 

            $renderXml .= ']]' . '></ValueLiteral></Value>';
            break;
          case -6:
            $attValue = $att->getValues( 0 );
            $renderXml .= '<Value id="-6"><ValueLiteral><!' . '[CDATA[';
            $renderXml .= $attValue->getTextValue();
            $renderXml .= ']]' . '></ValueLiteral></Value>';

            break;
          case -10:
            $renderXml .= '<Value id="-10"/>';
            break;
        } 
        $renderXml .= '</ValueList>';
        $renderXml .= '</Attribute>';
      } 
    } 

    return $renderXml;
  } 
} 

?>
	
		
		
		