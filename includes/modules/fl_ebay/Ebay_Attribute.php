<?php

require_once( 'Ebay_Defines.php' );
require_once( 'EBay_AttributeTextValue.php' );
require_once( 'Ebay_AttributeIdValue.php' );
require_once( 'Ebay_AttributeDateValue.php' );
require_once( 'Ebay_AttributeValue.php' );

/**
 */
class Ebay_Attribute
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
   * Read accessor to the Values.
   * this is an array of objects. The type of the objects will depend on the 'Type' of the Attribute
   * 
   * @access public 
   * @param integer $index The index of the value to return
   * @return Ebay _AttributeValue Value of the Values property
   */
  function getValues( $index )
  {
    return $this->_props['Values'][$index];
  } 

  /**
   * Return the amount of Values actually declared
   * 
   * @access public 
   * @return Ebay _AttributeValue Value of the Values property
   */
  function getValuesCount()
  {
    return count( $this->_props['Values'] );
  } 
  /**
   * Returns a copy of the Values array
   * 
   * @access public 
   * @return array of Ebay_AttributeValue
   */
  function getValuesArray()
  {
    return $this->_props['Values'];
  } 

  /**
   * Write accessor to the Values.
   * this is an array of objects. The type of the objects will depend on the 'Type' of the Attribute
   * 
   * @access public 
   * @param Ebay $ _AttributeValue $value The new value for the Values property
   * @param integer $index The index of the value to update. if $index = -1, the value is added to the end of list.
   * @return void 
   */
  function setValues( $value, $index = -1 )
  {
    if ( -1 == $index )
    {
      $index = count( $this->_props['Values'] );
    } 
    $this->_props['Values'][$index] = $value;
  } 

  /**
   * Read accessor of Type. 
   * Identifies the type of the Attribute
   * 1  : IdAttribute; the value(s) of the Attribute is an Id (Ebay_AttributeIdValue)
   * -3 : LiteralAttribute; the value(s) of the Attribute is a Literal (Ebay_AttributeTextValue)
   * -5 : FulldateAttribute; the value(s) of the Attribute is a FullDate (Ebay_AttributeDateValue)
   * -6 : OtherValue; the value(s) of the  the Attribute is a Other (Ebay_AttributeTextValue)
   * -10 : DashValue; the value of the Attribute is a Dash (-), anyway the display value could be retrieved by a Literal (Ebay_AttributeTextValue) with a fixed Value as a '--' (dashes)
   * 
   * @access public 
   * @return number Value of the Type property
   */
  function getType()
  {
    return $this->_props['Type'];
  } 

  /**
   * Write accessor of Type.  
   * Identifies the type of the Attribute
   * 1  : IdAttribute; the value(s) of the Attribute is an Id (Ebay_AttributeIdValue)
   * -3 : LiteralAttribute; the value(s) of the Attribute is a Literal (Ebay_AttributeTextValue)
   * -5 : FulldateAttribute; the value(s) of the Attribute is a FullDate (Ebay_AttributeDateValue)
   * -6 : OtherValue; the value(s) of the  the Attribute is a Other (Ebay_AttributeTextValue)
   * -10 : DashValue; the value of the Attribute is a Dash (-), anyway the display value could be retrieved by a Literal (Ebay_AttributeTextValue) with a fixed Value as a '--' (dashes)
   * 
   * @access public 
   * @param number $value The new value for the Type property
   * @return void 
   */
  function setType( $value )
  {
    $this->_props['Type'] = $value;
  } 

  /**
   * Read accessor of Id.
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
   * Read accessor of DisplaySequence. 
   * Only returned for Product / ProductFamily searches
   * 
   * Relative position of the attribute within the product data row. Not necessarily indexed from 0 (zero). Compare the sequence values to determine the lowest sequence value and the overall sequence of the attributes.
   * 
   * @access public 
   * @return number Value of the DisplaySequence property
   */
  function getDisplaySequence()
  {
    return $this->_props['DisplaySequence'];
  } 

  /**
   * Write accessor of DisplaySequence.  
   * Only returned for Product / ProductFamily searches
   * 
   * Relative position of the attribute within the product data row. Not necessarily indexed from 0 (zero). Compare the sequence values to determine the lowest sequence value and the overall sequence of the attributes.
   * 
   * @access public 
   * @param number $value The new value for the DisplaySequence property
   * @return void 
   */
  function setDisplaySequence( $value )
  {
    $this->_props['DisplaySequence'] = $value;
  } 

  /**
   * Read accessor of DateFormat. 
   * Only returned for Product / ProductFamily searches
   * 
   * Only returned if the attribute is a Date data type. This specifies the pattern to use when presenting the date to a user. Possible values:
   * Day/Month/Year
   * Month/Year
   * Year Only
   * For example, the Year Only format would indicate that the ValueLiteral field would return a value like 1999. Either DateFormat or DisplayUOM (or neither) can be returned, but not both.
   * 
   * @access public 
   * @return string Value of the DateFormat property
   */
  function getDateFormat()
  {
    return $this->_props['DateFormat'];
  } 

  /**
   * Write accessor of DateFormat.  
   * Only returned for Product / ProductFamily searches
   * 
   * Only returned if the attribute is a Date data type. This specifies the pattern to use when presenting the date to a user. Possible values:
   * Day/Month/Year
   * Month/Year
   * Year Only
   * For example, the Year Only format would indicate that the ValueLiteral field would return a value like 1999. Either DateFormat or DisplayUOM (or neither) can be returned, but not both.
   * 
   * @access public 
   * @param string $value The new value for the DateFormat property
   * @return void 
   */
  function setDateFormat( $value )
  {
    $this->_props['DateFormat'] = $value;
  } 

  /**
   * Read accessor of DisplayUOM. 
   * Only returned for Product / ProductFamily searches
   * 
   * The unit of measure (if any) to use for the specified numeric attribute. Not returned if not applicable. Either DateFormat or DisplayUOM (or neither) can be returned, but not both.
   * 
   * @access public 
   * @return string Value of the DisplayUOM property
   */
  function getDisplayUOM()
  {
    return $this->_props['DisplayUOM'];
  } 

  /**
   * Write accessor of DisplayUOM.  
   * Only returned for Product / ProductFamily searches
   * 
   * The unit of measure (if any) to use for the specified numeric attribute. Not returned if not applicable. Either DateFormat or DisplayUOM (or neither) can be returned, but not both.
   * 
   * @access public 
   * @param string $value The new value for the DisplayUOM property
   * @return void 
   */
  function setDisplayUOM( $value )
  {
    $this->_props['DisplayUOM'] = $value;
  } 

  /**
   * Read accessor of Label. 
   * Only returned for Product / ProductFamily searches
   * 
   * The label to display when presenting the attribute to a user (e.g., "Title" or "Manufacturer"). The label is defined for the product, and is therefore not necessarily the same as the label that is defined in the characteristics set.
   * 
   * @access public 
   * @return string Value of the Label property
   */
  function getLabel()
  {
    return $this->_props['Label'];
  } 

  /**
   * Write accessor of Label.  
   * Only returned for Product / ProductFamily searches
   * 
   * The label to display when presenting the attribute to a user (e.g., "Title" or "Manufacturer"). The label is defined for the product, and is therefore not necessarily the same as the label that is defined in the characteristics set.
   * 
   * @access public 
   * @param string $value The new value for the Label property
   * @return void 
   */
  function setLabel( $value )
  {
    $this->_props['Label'] = $value;
  } 

  /**
   * Read accessor of LabelVisible. 
   * Only returned for Product / ProductFamily searches
   * 
   * If true (1), the value of Label is visible on the eBay site. If false (0), the label is not visible. Usage of this information is optional. You are not required to display labels in the same manner as eBay.
   * 
   * @access public 
   * @return boolean Value of the LabelVisible property
   */
  function getLabelVisible()
  {
    return $this->_props['LabelVisible'];
  } 

  /**
   * Write accessor of LabelVisible.  
   * Only returned for Product / ProductFamily searches
   * 
   * If true (1), the value of Label is visible on the eBay site. If false (0), the label is not visible. Usage of this information is optional. You are not required to display labels in the same manner as eBay.
   * 
   * @access public 
   * @param boolean $value The new value for the LabelVisible property
   * @return void 
   */
  function setLabelVisible( $value )
  {
    $this->_props['LabelVisible'] = $value;
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['Values'] = array();

    $this->_props['Type'] = null;
    $this->_props['Id'] = null;
    $this->_props['DisplaySequence'] = null;
    $this->_props['DateFormat'] = null;
    $this->_props['DisplayUOM'] = null;
    $this->_props['Label'] = null;
    $this->_props['LabelVisible'] = null;
  } 

  /**
   * 
   * @access public 
   * @return void 
   */
  function Ebay_Attribute()
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_Attribute::_init(); 
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
          print_r( "dangling tag (Ebay_Attribute) : " . $tag . "<br>\r\n" );
          break;
        case 'DateFormat':
        case 'DisplayUOM':
          $this->_setProp( $tag, $data['value'] );
          break;
        case 'Label':
          if ( $type == 'open' || $type == 'complete' )
          {
            $this->_setProp( $tag, $data['value'] );
            $attribs = $data['attributes'];
            $this->_setProp( 'LabelVisible', $attribs['visible'] );
          } 
          break;
        case 'ValueList':
          break;
        case 'Value':
          if ( $type == 'open' || $type == 'complete' )
          {
            $attribs = $data['attributes'];
            $valueId = $attribs['id'];
            if ( $type == 'complete' )
            {
              $attValue = new Ebay_AttributeIdValue();
              $attValue->setIdValue( $valueId );
              $this->setType( $valueId );
              $this->setValues( $attValue );
            } 
          } 
          break;
        case 'ValueLiteral':
          if ( $type == 'open' || $type == 'complete' )
          {
            $attTextValue = new EBay_AttributeTextValue();
            $attTextValue->setTextValue( $data['value'] );
            $this->setType( $valueId );
            $this->setValues( $attTextValue );
          } 
          break;
      } 
    } 
    return true;
  } 
} 

?>
	
		
		
		