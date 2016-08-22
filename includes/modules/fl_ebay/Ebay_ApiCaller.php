<?php
/*
 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require_once( 'Ebay_Defines.php' );
require_once( 'Ebay_Result.php' );
require_once( 'Ebay_Session.php' );

/**
 */
class Ebay_ApiCaller
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
   * 
   * @access private 
   * @var array 
   */

  var $_xmlEncodingStrings = array( 'utf-8', 'iso-8859-1', 'utf-8', 'iso-8859-1' );

  /**
   * 
   * @access private 
   * @var Ebay _Session
   */

  var $_session = EBAY_NOTHING;

  /**
   * 0 for utf-8, 1 for iso-8859-1
   * 
   * @access private 
   * @var boolean 
   */

  var $_XmlEncoding = 0;

  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
  } 

  /**
   * internal call method to interact with ebay
   * returns a raw XML string with the response data
   * 
   * @access public 
   * @param string $methodname ebay APIs method name to call
   * @param array $params any additional parameters to add to the request XML. If the API method does not need any parameter you can leave params empty (blank string or null). params could be either an array or just a xml fragment added to the request. If you give in an array the keys will be used to build a XML element with the value of the array element as the value. If you need to pass the value as CDATA then do the preparation in the array.
   * If the key starts with a double understore this indicates that we like to add raw data without adding a surounding tag with the key.
   * @param number $detaillevel Detail Level to pass through to the method call
   * @param array $options additional options, pass as assoc array
   * - 'returnRaw' = true
   * return the raw-result (xml) in the field in Ebay_Result
   * @return Ebay _Result
   */
  function call( $methodname, $params, $detaillevel, $options = null )
  {
    $s = &$this->_session;
    $ret = new Ebay_Result();

    if ( isset( $s->_debugSwitches['profiling'] ) )
    {
      $s->LogMsg( "API Call start - " . $methodname, 0, E_NOTICE );
    } 

    if ( isset( $s->_debugSwitches['pickoutlog'] ) )
    { 
      // should pickup data
      die( "debug data pickup not yet supported !" );
    } 
    else
    { 
      // normal processing
      $raw = $this->internalCall( $methodname, $params, $detaillevel, $ret );
    } 

    if ( isset( $s->_debugSwitches['profiling'] ) )
    {
      $s->LogMsg( "API Call end - " . $methodname, 0, E_NOTICE );
    } 

    if ( $s->getRawLogMode() )
    {
      $seq = $s->getRawLogSeq();
      $rlName = $s->getRawLogName();
      if ( $rlName == EBAY_NOTHING )
      {
        $rlName = $methodname;
      } 
      $filename = $s->getRawLogPath() . '/' . $rlName . '_' . $seq . '.xml';

      $seq++;
      $s->setRawLogSeq( $seq );
      $ret->setRawLogSeq( $seq );

      if ( !$handle = fopen( $filename, 'w' ) )
      {
        print_r( "Cannot open file for RawLogMode, check security and path ($filename)" );
      } 
      else
      { 
        // Write $somecontent to our opened file.
        if ( !fwrite( $handle, $raw ) )
        {
          print_r( "Cannot write to file for RawLogMode, check security ($filename)" );
        } 
        fclose( $handle );
      } 
    } 

    if ( $raw == "" )
    {
      $ret->addError( 1, "infrastructure problem, no data from server, check internet connection" , EBAY_ERR_ERROR );
      return $ret;
    } 

    if ( ! strstr( $raw, '?xml' ) )
    {
      $ret->addError( 2, "infrastructure problem, no xml-data from server (maybe proxy/gateway problem on eBay side)\n", EBAY_ERR_ERROR );
      return $ret;
    } 

    if ( $this->_XmlEncoding == 0 )
    { 
      // normally we would not need do the decoding here as the content should be in utf-8
      // format already. But waht ever the eBay system returns bring some trouble for the
      // PHP xml parser. The 'extra' encoding fixes it !
      if ( $s->getDoXmlUtf8Decoding() )
      {
        $raw = utf8_encode( $raw );
      } 
    } 

    /**
     * else
     * {
     * if ( $this->_XmlEncoding == 1 )
     * { 
     * // the encoding type 1 means that we get ISO-8859-1
     * // so we need a tranformation to utf-8 as PHP will
     * // need this for the xml parser
     * // actually not very nice as we add an extra work on the whole
     * // xml-fragment !
     * $raw = utf8_encode( $raw );
     * } elseif ( $this->_XmlEncoding == 2 )
     * {
     * $raw = utf8_decode( $raw );
     * } 
     * }
     */
    // might be a problem with the EA Sandbox here
    // $raw = str_replace( 'Ebay', 'eBay', $raw );

    if ( $options != null )
    {
      if ( $options['returnRaw'] == true )
      {
        $ret->setRawResult( $raw );
      } 
    } 

    if ( isset( $this->_session->_debugSwitches['showout'] ) )
    {
      print_r( "\n<pre>\n" );
      print_r( htmlentities( $raw ) . "\r\n" ); 
      // print_r( $raw );
      print_r( "\n</pre>\n" );
    } 

    if ( isset( $s->_debugSwitches['profiling'] ) )
    {
      $s->LogMsg( "parsing start - " . $methodname, 0, E_NOTICE );
    } 

    $p = xml_parser_create();
    xml_parser_set_option( $p, XML_OPTION_SKIP_WHITE, true );
    xml_parser_set_option( $p, XML_OPTION_CASE_FOLDING, false );
    xml_parse_into_struct( $p, $raw, $ret->_xmlValues, $ret->_xmlTags );
    xml_parser_free( $p );

    if ( isset( $s->_debugSwitches['profiling'] ) )
    {
      $s->LogMsg( "parsing end - " . $methodname, 0, E_NOTICE );
    } 

    if ( ! isset( $ret->_xmlTags['eBay'] ) )
    {
      $ret->addError( EBAY_ERR_ERROR, "not an eBay result", EBAY_ERR_ERROR );
      return $ret;
    } 

    if ( isset( $ret->_xmlTags['Errors'] ) )
    {
      $offset = $ret->_xmlTags['Errors'][0] + 1;
      $len = $ret->_xmlTags['Errors'][1] - $offset;
      $errors = array_slice( $ret->_xmlValues, $offset, $len );

      $errMessage = '';
      $errCode = 0;
      $errSeverity = 0;
      foreach( $errors as $data )
      {
        switch ( $data['tag'] )
        {
          case 'SeverityCode': 
            // $errSeverity = $data['value'];
            // $ret->setSeverity( $errSeverity );
            break;

          case 'Severity': 
            // as we encountered problems with
            // miss leading SeverityCode's
            // we now trust the textual reprensentation
            $severityText = $data['value'];
            if ( $severityText == 'Warning' )
            {
              $errSeverity = EBAY_ERR_WARNING;
            } elseif ( $severityText == 'Error' || $severityText == 'SeriousError' )
            {
              $errSeverity = EBAY_ERR_ERROR;
            } 
            else
            {
              $errSeverity = EBAY_ERR_SUCCESS;
            } 
            $ret->setSeverity( $errSeverity );
            break;

          case 'LongMessage':
            if ( $s->getErrorLevel() == 1 )
            {
              $errMessage .= $data['value'];
            } 
            break;

          case 'ShortMessage':
            if ( $s->getErrorLevel() == 0 )
            {
              $errMessage .= $data['value'];
            } 
            break;
          case 'Code':
            $errCode = $data['value'];
            break;
          case 'Error':
            if ( $data['type'] == 'open' )
            {
              $errMessage = '';
              $errCode = 0;
              $errSeverity = 0;
            } 
            else
            { 
              // treat the 'Call usage limit has been reached' as a warning
              // so that the application can continue with the
              // data !
              if ( $errCode == 518 )
                $errSeverity = EBAY_ERR_WARNING;

              $ret->addError( $errCode, $errMessage, $errSeverity );
            } 
            break;
        } 
      } 
    } 
    // we are running in TokenMode
    // so check for any soft-expirations
    // and hard-expiration warnings
    if ( $s->getTokenMode() )
    {
      /**
       * There is no RefreshToken anymore, lets leave the code in here maybe eBay will decide back
       * again !!!
       * $refreshedToken = $ret->getXmlStructTagContent( 'RefreshedToken' );
       * if ( $refreshedToken != null )
       * {
       * $ret->setRefreshedToken( $refreshedToken );
       * $ret->setHasRefreshedToken( true ); 
       * // if the pickup-file is used any refreshedToken is written
       * // to the token-file
       * if ( $s->getTokenUsePickupFile() )
       * {
       * $s->setRequestToken( $refreshedToken );
       * $s->WriteTokenFile();
       * } 
       * }
       */

      $hasTokenWarning = $ret->getXmlStructTagContent( 'TokenHardExpirationWarning' );
      if ( $hasTokenWarning != null )
      {
        $HardExpirationDate = $ret->getXmlStructTagContent( 'HardExpirationDate' );
        if ( $HardExpirationDate != null )
        {
          $ret->setHardExpirationDateToken( $HardExpirationDate );
          $ret->setHasNewTokenHardExpirationDate( true );
        } 
      } 
    } 

    return $ret;
  } 

  /**
   * 
   * @access public 
   * @param Ebay $ _Session $session
   * @return void 
   */
  function Ebay_ApiCaller( $session )
  { 
    // call to initialisation
    // (be sure to call this always on the actual class and prevent any overwriting)
    Ebay_ApiCaller::_init();
    $this->_session = $session;
    $this->_XmlEncoding = $session->getXmlEncoding();
  } 

  /**
   * internal call method to interact with ebay
   * returns a raw XML string with the response data
   * 
   * @access public 
   * @param string $methodname ebay APIs method name to call
   * @param array $params any additional parameters to add to the request XML. If the API method does not need any parameter you can leave params empty (blank string or null). params could be either an array or just a xml fragment added to the request. If you give in an array the keys will be used to build a XML element with the value of the array element as the value. If you need to pass the value as CDATA then do the preparation in the array.
   * if the key starts with a double understore this indicates that we like to add raw data without adding a surounding tag with the key.
   * @param number $detaillevel Detail Level to pass through to the method call
   * @param Ebay $ _Result $result
   * @return Ebay _Result
   */
  function internalCall( $methodname, $params, $detaillevel, &$result )
  {
    $s = $this->_session; 
    // setup the request XML part
    // with the informatino from the session
    $request = "<?xml version='1.0' encoding='"
     . $this->_xmlEncodingStrings[$this->_XmlEncoding]
     . "'?>"
     . "<request>";

    if ( $s->getTokenMode() <= 1 )
    {
      if ( $s->getTokenMode() )
      { 
        // when using the pickup-file for the token
        // we will load the content for each call from the token-file
        if ( $s->getTokenUsePickupFile() )
        {
          $s->ReadTokenFile();
        } 
        $request .= "<RequestToken>" . $s->getRequestToken() . "</RequestToken>";
      } 
      else
      {
        $request .= "<RequestUserId>" . $s->getRequestUser() . "</RequestUserId><RequestPassword>" . $s->getRequestPassword() . "</RequestPassword>";
      } 
    } 

    $request .= "<ErrorLevel>" . $s->getErrorLevel() . "</ErrorLevel>"
     . "<ErrorLanguage>" . $s->getErrorLanguage() . "</ErrorLanguage>"
     . "<DetailLevel>$detaillevel</DetailLevel>"
     . "<SiteId>" . $s->getSiteId() . "</SiteId>"
     . "<Verb>$methodname</Verb>";

    if ( $params != '' && $params != null )
    {
      if ( is_array( $params ) )
      {
        while ( list( $key, $val ) = each( $params ) )
        { 
          // if the key starts with a double understore
          // this indicates that we like to add raw data
          // without adding a surounding tag with the key
          if ( substr( $key, 0, 2 ) == '__' )
          {
            $request .= $val;
          } 
          else
          {
            $request .= "<" . $key . ">" . $val . "</" . $key . ">";
          } 
        } 
      } 
      else
      {
        $request .= $params;
      } 
    } 

    $request .= "</request>"; 
    // Setup all needed Headers
    $headers[] = "X-EBAY-API-COMPATIBILITY-LEVEL: " . $s->getCompatibilityLevel();
    $headers[] = "X-EBAY-API-SESSION-CERTIFICATE: " . $s->getDevId() . ";" . $s->getAppId() . ";" . $s->getCertId();
    $headers[] = "X-EBAY-API-DEV-NAME: " . $s->getDevId();
    $headers[] = "X-EBAY-API-APP-NAME: " . $s->getAppId();
    $headers[] = "X-EBAY-API-CERT-NAME: " . $s->getCertId();
    $headers[] = "X-EBAY-API-CALL-NAME: " . $methodname;
    $headers[] = "X-EBAY-API-SITEID: " . $s->getSiteId();
    $headers[] = "X-EBAY-API-DETAIL-LEVEL: " . $detaillevel;
    $headers[] = "Content-Type: text/xml";
    if ( $s->getUseHttpCompression() )
    {
      $headers[] = "Accept-Encoding: gzip";
    } 
    // important put lengh of expected post-data in the header
    $headers[] = "Content-Length: " . strlen( $request );

    if ( isset( $s->_debugSwitches['showin'] ) )
    {
      print_r( "<pre>" );
      print_r( htmlentities( $request ) . "\r\n" );
      print_r( $headers );
      print_r( "</pre>" );
    } 
    if ( isset( $s->_debugSwitches['profiling'] ) )
    {
      $rawLogMode = $s->getRawLogMode();
      if ( $rawLogMode >= 2 )
      {
        $s->LogMsg( "request : " . $request, 0, E_NOTICE );
      } 
    } 
    // initialize for the call
    $curl = curl_init ( $s->getApiUrl() ); 
    // no need for a CA certificate on this (client) side
    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false ); 
    // no message should come through
    if ( isset( $s->_debugSwitches['curl-verbose'] ) )
    {
      curl_setopt( $curl, CURLOPT_VERBOSE, 1 );
    } 
    else
    {
      curl_setopt( $curl, CURLOPT_VERBOSE, 0 );
    } 

    if ( $s->getUseHttpCompression() )
    {
      if ( PHP_OS == "Darwin" )
      {
        curl_setopt( $curl, CURLOPT_ENCODING, "gzip" ); 
        // curl_setopt( $curl, CURLOPT_ENCODING, "deflate" );
      } 
      else
      {
        curl_setopt( $curl, CURLOPT_ENCODING, "gzip" );
        curl_setopt( $curl, CURLOPT_ENCODING, "deflate" );
      } 
      // using binary transfer
      // check if this really speeds up the transfer
      curl_setopt ( $curl, CURLOPT_BINARYTRANSFER, 1 );
    } 
    // experimental !
    // buffer to 64k
    // curl_setopt ( $curl, CURLOPT_BUFFERSIZE, 65536 );
    // setup the proxy configuration if needed
    $proxyType = $s->getProxyServerType();
    if ( $proxyType != EBAY_NOTHING )
    {
      curl_setopt( $curl, CURLOPT_PROXYTYPE, $proxyType );

      $proxyServer = $s->getProxyServer();
      $proxyCredentials = $s->getProxyUidPwd();
      if ( $proxyServer != EBAY_NOTHING )
      {
        curl_setopt( $curl, CURLOPT_PROXY, $proxyServer );
      } 
      if ( $proxyCredentials != EBAY_NOTHING )
      {
        curl_setopt( $curl, CURLOPT_PROXYUSERPWD, $proxyCredentials );
      } 
      // TODO support other AuthModes for ProxyServers here
      // TODO support HTTPPROXYTUNNEL here
    } 
    // set timeout
    curl_setopt( $curl, CURLOPT_TIMEOUT, $s->getRequestTimeout() ); 
    // do a post
    curl_setopt( $curl, CURLOPT_POST, 1 ); 
    // do not return the http resonse header, only the body
    curl_setopt( $curl, CURLOPT_HEADER, 0 ); 
    // set the request headers
    curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers ); 
    // set the request as the post
    curl_setopt( $curl, CURLOPT_POSTFIELDS, $request ); 
    // retrieve the data and return it
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 0 );

    if ( isset( $s->_debugSwitches['profiling'] ) )
    {
      $s->LogMsg( "Executing call to " . $s->getApiUrl(), 0, E_NOTICE );
    } 

    ob_start();
    curl_exec( $curl );
    $curlErr = curl_error( $curl );
    $httpResult = ob_get_contents();
    ob_end_clean(); 
    // $httpResult = curl_exec( $curl );
    if ( isset( $s->_debugSwitches['profiling'] ) )
    {
      $s->LogMsg( "returning from call", 0, E_NOTICE );
    } 
    // $curlErr = curl_errno( $curl );
    if ( $curlErr == 0 )
    {
      curl_close( $curl );
      return $httpResult;
    } 
    else
    {
      $result->addError( 1, "http error code " . $curlErr, EBAY_ERR_ERROR );
      curl_close( $curl );
      return "";
    } 
  } 
} 

?>
	
		
		
		