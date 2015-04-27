<?php

require_once( 'Ebay_Defines.php' );
require_once( 'Ebay_ApiCaller.php' );
require_once( 'Ebay_Result.php' );

/**
 * done, v1.0.0.1
 * 
 * @see Ebay_Seller
 * @see Ebay_Buyer
 */
class Ebay_User
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
   * Read accessor of CountryCode. 
   * Two-letter abbreviation for the country.
   * 
   * @access public 
   * @return define Value of the CountryCode property
   */
  function getCountryCode()
  {
    return $this->_props['CountryCode'];
  } 

  /**
   * Read accessor of RegistrationAddressCity. 
   * City portion of user's registration address.
   * 
   * @access public 
   * @return string Value of the RegistrationAddressCity property
   */
  function getRegistrationAddressCity()
  {
    return $this->_props['RegistrationAddressCity'];
  } 

  /**
   * Read accessor of RegistrationAddressCountry. 
   * Country portion of user's registration address.
   * 
   * @access public 
   * @return string Value of the RegistrationAddressCountry property
   */
  function getRegistrationAddressCountry()
  {
    return $this->_props['RegistrationAddressCountry'];
  } 

  /**
   * Read accessor of RegistrationAddressName. 
   * Name portion of user's registration address.
   * 
   * @access public 
   * @return string Value of the RegistrationAddressName property
   */
  function getRegistrationAddressName()
  {
    return $this->_props['RegistrationAddressName'];
  } 

  /**
   * Read accessor of RegistrationAddressPhone. 
   * Phone number portion of user's registration address.
   * 
   * @access public 
   * @return string Value of the RegistrationAddressPhone property
   */
  function getRegistrationAddressPhone()
  {
    return $this->_props['RegistrationAddressPhone'];
  } 

  /**
   * Read accessor of RegistrationAddressStateOrProvince. 
   * State (or region) portion of user's registration address.
   * 
   * @access public 
   * @return string Value of the RegistrationAddressStateOrProvince property
   */
  function getRegistrationAddressStateOrProvince()
  {
    return $this->_props['RegistrationAddressStateOrProvince'];
  } 

  /**
   * Read accessor of RegistrationAddressStreet. 
   * street address portion of user's registration address.
   * 
   * @access public 
   * @return string Value of the RegistrationAddressStreet property
   */
  function getRegistrationAddressStreet()
  {
    return $this->_props['RegistrationAddressStreet'];
  } 

  /**
   * Read accessor of RegistrationAddressZip. 
   * Zip or postal code portion of user's registration address.
   * 
   * @access public 
   * @return string Value of the RegistrationAddressZip property
   */
  function getRegistrationAddressZip()
  {
    return $this->_props['RegistrationAddressZip'];
  } 

  /**
   * Read accessor of AboutMe. 
   * Indicates whether the user has an About Me page.
   * 
   * @access public 
   * @return boolean Value of the AboutMe property
   */
  function getAboutMe()
  {
    return $this->_props['AboutMe'];
  } 

  /**
   * Read accessor of AllowPaymentEdit. 
   * Indicates if a seller is allowing buyers to edit the total cost of an item. (Sellers enable this property in their My eBay user preferences on the eBay site.) Returns 0, otherwise.
   * 
   * @access public 
   * @return boolean Value of the AllowPaymentEdit property
   */
  function getAllowPaymentEdit()
  {
    return $this->_props['AllowPaymentEdit'];
  } 

  /**
   * Read accessor of CheckoutEnabled. 
   * Indicates whether a seller has enabled Checkout in his or her eBay user preferences. Returns 1 if the user is a seller and has enabled Checkout. Returns 0 if the user is not a seller or has not enabled Checkout.
   * 
   * @access public 
   * @return boolean Value of the CheckoutEnabled property
   */
  function getCheckoutEnabled()
  {
    return $this->_props['CheckoutEnabled'];
  } 

  /**
   * Read accessor of CIPBankAccountStored. 
   * If true, specifies that a user has stored bank account information with eBay in order to use the "CIP in checkout" function. Applicable to German site only.
   * 
   * @access public 
   * @return boolean Value of the CIPBankAccountStored property
   */
  function getCIPBankAccountStored()
  {
    return $this->_props['CIPBankAccountStored'];
  } 

  /**
   * Read accessor of eBayGoodStanding. 
   * If true, indicates that the user is in good standing with eBay.
   * 
   * @access public 
   * @return boolean Value of the eBayGoodStanding property
   */
  function geteBayGoodStanding()
  {
    return $this->_props['eBayGoodStanding'];
  } 

  /**
   * Read accessor of Email. 
   * Email address for the user. Returned as CDATA. As an anti-spam measure, email addresses are only returned under the conditions described in Appendix J: Anti-Spam Rules and the Email Note. When an email address cannot be returned, the string "Invalid Request" is returned in the Email node instead.
   * 
   * @access public 
   * @return string Value of the Email property
   */
  function getEmail()
  {
    return $this->_props['Email'];
  } 

  /**
   * Read accessor of FeedbackScore. 
   * Aggregate feedback score for the specified user. Feedback.Score is only returned if the user specified in UserId has not chosen to make his or her feedback Private. If Private = true for the user, then Feedback.Score is only returned if the RequestUserId input argument matches the UserId input argument.
   * 
   * @access public 
   * @return boolean Value of the FeedbackScore property
   */
  function getFeedbackScore()
  {
    return $this->_props['FeedbackScore'];
  } 

  /**
   * Read accessor of IDVerified. 
   * Indicates whether the user has been verified. See http://pages.ebay.com/services/buyandsell/idverify-login.html for more information about the ID Verify program.
   * 
   * @access public 
   * @return boolean Value of the IDVerified property
   */
  function getIDVerified()
  {
    return $this->_props['IDVerified'];
  } 

  /**
   * Read accessor of IsLAAuthorized. 
   * If true, indicates that a user is authorized to list Live Auction items.
   * 
   * @access public 
   * @return boolean Value of the IsLAAuthorized property
   */
  function getIsLAAuthorized()
  {
    return $this->_props['IsLAAuthorized'];
  } 

  /**
   * Read accessor of MerchandisingPref. 
   * Indicates whether the user has elected to participate as a seller in the Merchandising Manager feature. Will be one of the values:
   * 0 = No, not participating
   * 1 = Yes, without ViewItems
   * 2 = Yes, with ViewItems
   * 
   * @access public 
   * @return define Value of the MerchandisingPref property
   */
  function getMerchandisingPref()
  {
    return $this->_props['MerchandisingPref'];
  } 

  /**
   * Write accessor of MerchandisingPref.  
   * Indicates whether the user has elected to participate as a seller in the Merchandising Manager feature. Will be one of the values:
   * 0 = No, not participating
   * 1 = Yes, without ViewItems
   * 2 = Yes, with ViewItems
   * 
   * @access public 
   * @param define $value The new value for the MerchandisingPref property
   * @return void 
   */
  function setMerchandisingPref( $value )
  {
    $this->_props['MerchandisingPref'] = $value;
  } 

  /**
   * Read accessor of NewUser. 
   * Identifies a new user. If true (1), indicates that the user has been a registered eBay user for 30 days or less. Always false (0) after the user has been registered for more than 30 days. See http://pages.ebay.com/help/account/newid-icon.html for additional information. Does not indicate an ID change (see the UserIdChanged tag). For the corresponding eBay icon, see Table of URLs for Miscellaneous Images.
   * 
   * @access public 
   * @return boolean Value of the NewUser property
   */
  function getNewUser()
  {
    return $this->_props['NewUser'];
  } 

  /**
   * Read accessor of Private. 
   * Indicates whether the user selected to have feedback information private. Private is only returned when Private = true.
   * 
   * @access public 
   * @return boolean Value of the Private property
   */
  function getPrivate()
  {
    return $this->_props['Private'];
  } 

  /**
   * Read accessor of RegDate. 
   * Indicates the date the specified user originally registered with eBay. This will not be adapted to your local time regardless which settings to made to the Session object
   * 
   * @access public 
   * @return datetime Value of the RegDate property
   */
  function getRegDate()
  {
    return $this->_props['RegDate'];
  } 

  /**
   * Read accessor of MaxScheduledTime. 
   * Maximum number of minutes that a listing may be scheduled in advance.
   * 
   * @access public 
   * @return number Value of the MaxScheduledTime property
   */
  function getMaxScheduledTime()
  {
    return $this->_props['MaxScheduledTime'];
  } 

  /**
   * Read accessor of MinScheduleTime. 
   * Minimum number of minutes that a listing may be scheduled in advance.
   * 
   * @access public 
   * @return number Value of the MinScheduleTime property
   */
  function getMinScheduleTime()
  {
    return $this->_props['MinScheduleTime'];
  } 

  /**
   * Read accessor of MaxScheduledItems. 
   * Maximum number of Items that a user may schedule.
   * 
   * @access public 
   * @return number Value of the MaxScheduledItems property
   */
  function getMaxScheduledItems()
  {
    return $this->_props['MaxScheduledItems'];
  } 

  /**
   * Read accessor of SiteId. 
   * eBay site the user is registered with. See the SiteId Table for valid values.
   * 
   * @access public 
   * @return define Value of the SiteId property
   */
  function getSiteId()
  {
    return $this->_props['SiteId'];
  } 

  /**
   * Read accessor of Star. 
   * Visual indicator of user's feedback score. See the Seller Rating Table for values for the Star field and what the codes represent.
   * 
   * @access public 
   * @return number Value of the Star property
   */
  function getStar()
  {
    return $this->_props['Star'];
  } 

  /**
   * Read accessor of Status. 
   * Indicates the user's registration/user status. See the Status Table for possible values
   * 
   * @access public 
   * @return number Value of the Status property
   */
  function getStatus()
  {
    return $this->_props['Status'];
  } 

  /**
   * Read accessor of StoreLocation. 
   * URL pointing to the seller's eBay Stores page. This URL follows the format below, where "####" is replaced by the seller's eBay Stores ID (that uniquely identifies the eBay Store).
   * 
   * http://www.ebaystores.com/id=####
   * 
   * Returned as null for International Fixed Price items. Not returned if StoreOwner = 1 (false).
   * 
   * @access public 
   * @return string Value of the StoreLocation property
   */
  function getStoreLocation()
  {
    return $this->_props['StoreLocation'];
  } 

  /**
   * Read accessor of StoreOwner. 
   * Indicates whether the user is an eBay Stores storefront owner.
   * 
   * @access public 
   * @return boolean Value of the StoreOwner property
   */
  function getStoreOwner()
  {
    return $this->_props['StoreOwner'];
  } 

  /**
   * Read accessor of UserId. 
   * Unique identifier for the user.
   * 
   * @access public 
   * @return string Value of the UserId property
   */
  function getUserId()
  {
    return $this->_props['UserId'];
  } 

  /**
   * Read accessor of UserIdChanged. 
   * Identifies a user whose ID has changed. If true (1), indicates that the user's ID has changed within the last 30 days. See http://pages.ebay.com/help/account/changed-id-icon.html for information about user ID changes. Not applicable for new users (see the NewUser tag). For the corresponding eBay icon, see Table of URLs for Miscellaneous Images.
   * 
   * @access public 
   * @return boolean Value of the UserIdChanged property
   */
  function getUserIdChanged()
  {
    return $this->_props['UserIdChanged'];
  } 

  /**
   * Read accessor of UserIdLastChanged. 
   * Timestamp the user's data was last changed. Adapted to localtime
   * 
   * @access public 
   * @return datetime Value of the UserIdLastChanged property
   */
  function getUserIdLastChanged()
  {
    return $this->_props['UserIdLastChanged'];
  } 

  /**
   * Read accessor of VATStatus. 
   * If present, indicates whether or not the user is subject to VAT. Users who have registered with eBay as VAT-exempt are not subject to VAT. See VAT note. Not returned for users whose country of residence is outside the EU. Possible values for the user's status:
   * 2 = Residence in an EU country but user registered as VAT-exempt
   * 3 = Residence in an EU country and user not registered as VAT-exempt
   * 
   * @access public 
   * @return define Value of the VATStatus property
   */
  function getVATStatus()
  {
    return $this->_props['VATStatus'];
  } 

  /**
   * Read accessor of SellerLevel. 
   * The user's eBay PowerSeller tier. Possible values are:
   * 11 = Bronze
   * 22 = Silver 
   * 33 = Gold
   * 44 = Platinum
   * 55 = Titanium
   * Other values are valid, but indicate that the user is a non-PowerSeller. See the International Item Matrix.
   * See http://pages.ebay.com/services/buyandsell/powersellers.html for more information about the eBay PowerSellers program.
   * 
   * @access public 
   * @return define Value of the SellerLevel property
   */
  function getSellerLevel()
  {
    return $this->_props['SellerLevel'];
  } 

  /**
   * Read accessor of EIAS. 
   * Unique identifier for the user that does not change when the eBay user name is changed. Use when an application needs to associate a new eBay user name with the corresponding eBay user.
   * 
   * @access public 
   * @return string Value of the EIAS property
   */
  function getEIAS()
  {
    return $this->_props['EIAS'];
  } 

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['CountryCode'] = EBAY_NOTHING;
    $this->_props['RegistrationAddressCity'] = EBAY_NOTHING;
    $this->_props['RegistrationAddressCountry'] = EBAY_NOTHING;
    $this->_props['RegistrationAddressName'] = EBAY_NOTHING;
    $this->_props['RegistrationAddressPhone'] = EBAY_NOTHING;
    $this->_props['RegistrationAddressStateOrProvince'] = EBAY_NOTHING;
    $this->_props['RegistrationAddressStreet'] = EBAY_NOTHING;
    $this->_props['RegistrationAddressZip'] = EBAY_NOTHING;
    $this->_props['AboutMe'] = EBAY_NOTHING;
    $this->_props['AllowPaymentEdit'] = EBAY_NOTHING;
    $this->_props['CheckoutEnabled'] = EBAY_NOTHING;
    $this->_props['CIPBankAccountStored'] = EBAY_NOTHING;
    $this->_props['eBayGoodStanding'] = EBAY_NOTHING;
    $this->_props['Email'] = EBAY_NOTHING;
    $this->_props['FeedbackScore'] = EBAY_NOTHING;
    $this->_props['IDVerified'] = EBAY_NOTHING;
    $this->_props['IsLAAuthorized'] = EBAY_NOTHING;
    $this->_props['MerchandisingPref'] = EBAY_NOTHING;
    $this->_props['NewUser'] = EBAY_NOTHING;
    $this->_props['Private'] = EBAY_NOTHING;
    $this->_props['RegDate'] = EBAY_NOTHING;
    $this->_props['MaxScheduledTime'] = EBAY_NOTHING;
    $this->_props['MinScheduleTime'] = EBAY_NOTHING;
    $this->_props['MaxScheduledItems'] = EBAY_NOTHING;
    $this->_props['SiteId'] = EBAY_NOTHING;
    $this->_props['Star'] = EBAY_NOTHING;
    $this->_props['Status'] = EBAY_NOTHING;
    $this->_props['StoreLocation'] = EBAY_NOTHING;
    $this->_props['StoreOwner'] = EBAY_NOTHING;
    $this->_props['UserId'] = EBAY_NOTHING;
    $this->_props['UserIdChanged'] = EBAY_NOTHING;
    $this->_props['UserIdLastChanged'] = EBAY_NOTHING;
    $this->_props['VATStatus'] = EBAY_NOTHING;
    $this->_props['SellerLevel'] = EBAY_NOTHING;
    $this->_props['EIAS'] = EBAY_NOTHING;
  } 

  /**
   * 
   * @access public 
   * @return void 
   */
  function Ebay_User()
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_User::_init(); 
    // insert code here...
  } 

  /**
   * After registering a new user in the GUI test environment (the Sandbox GUI), call ValidateTestUserRegistration to validate that your application can make function calls on behalf of the test user.
   * 
   * @access public 
   * @param Ebay $ _Session $session
   * @param array $options passes extra information to the ValidateTestUserRegistration call. Use a assoc. array with the extra data you like to pass.
   * e.g. array ('NewFeedbackScore' => '1000', 'SubscribeSA' => '1')
   * see http://developer.ebay.com/DevZone/docs/API_Doc/Functions/ValidateTestUserRegistration/ValidateTestUserRegistrationInputArguments.htm for available options
   * @return Ebay _Result
   */
  function ValidateTestUserRegistration( $session = null, $options = null )
  {
    if ( !$this->_apiCaller )
      $this->_apiCaller = new Ebay_ApiCaller( $session );
    $res = new Ebay_Result();

    if ( $options != null )
    {
      $params = $options;
    } 
    else
    {
      $params = array();
    } 

    $res = $this->_apiCaller->call( 'ValidateTestUserRegistration', $params, 0 );
    if ( $res->isGood() )
    {
      $statusData = $res->getXmlStructTagContent( 'Status' );
      $res->setSingleValue( $statusData );
    } 

    return $res;
  } 

  /**
   * 
   * @access public 
   * @param array $arrData 
   * @return boolean 
   */
  function InitFromDataStruct( $arrData )
  {
    $readAddressData = false;
    foreach ( $arrData as $data )
    {
      $tag = $data['tag'];
      $type = $data['type'];

      switch ( $tag )
      {
        default:
          // print_r( "dangling tag (user) : " . $tag . "<br>\r\n" );
          break; 
        // all directly maped data
        case 'AboutMe':
        case 'Email':
        case 'IDVerified':
        case 'IsLAAuthorized':
        case 'NewUser':
        case 'Private':
        case 'SellerLevel':
        case 'RegDate':
        case 'SiteId':
        case 'Star':
        case 'Status':
        case 'StoreOwner':
        case 'UserId':
        case 'UserIdChanged':
        case 'UserIdLastChanged':
        case 'VATStatus':
        case 'StoreLocation':
        case 'EIAS': 
        // extra tags, not really sure if these tag
        // belongs to the user or maybe better to Seller/Buyer
        case 'AllowPaymentEdit':
        case 'CheckoutEnabled':
        case 'CIPBankAccountStored':
        case 'MerchandisingPref':
        case 'eBayGoodStanding':
          $this->_setProp( $tag, $data['value'] );
          break;
        case 'Feedback':
          break;
        case 'Score':
          $this->_setProp( 'FeedbackScore', $data['value'] );
          break;
        case 'RegistrationAddress':
          $readAddressData = ( $type == 'open' );
          break;
        case 'City':
        case 'Country':
        case 'Name':
        case 'Phone':
        case 'Street':
        case 'Zip':
          $this->_setProp( "RegistrationAddress" . $tag, $data['value'] );
          break;
        case 'StateorProvince':
        case 'StateOrProvince':
          $this->_setProp( "RegistrationAddressStateOrProvince", $data['value'] );
          break;
      } 
    } 
    return true;
  } 
} 

?>
	
		
		
		