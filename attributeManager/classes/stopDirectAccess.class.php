<?php
/*
  $Id: stopDirectAccess.class.php,v 1.0 21/02/06 Sam West$

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require_once('attributeManager/includes/attributeManagerSessionFunctions.inc.php');

/**
 * Try and stop direct access to the script
 * As far as i know there is no way for a remote user to set a session var without high jacking the session, in which case it doesn't really matter what this script does anyway.
 * If there is i will have to rethink this
 */

class stopDirectAccess {
	
	/**
	 * Sets the global session variable
	 * @static authorise()
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @param $sessionVar string session variable name
	 * @return void
	 */
	static function authorise($sessionVar) {
		amSessionRegister($sessionVar);
		$GLOBALS[$sessionVar] = stopDirectAccess::makeSessionId();
	}
	
	/**
	 * deletes the global session variable
	 * @static deAuthorise()
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @param $sessionVar string session variable name
	 * @return void
	 */
	function deAuthorise($sessionVar) {
		amSessionUnregister($sessionVar);
	}
	
	/**
	 * Checks the session var
	 * @static checkAuthorisation()
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @param $sessionVar string session variable name
	 * @return void
	 */
	static function checkAuthorisation($sessionVar) {
		if(!amSessionIsRegistered($sessionVar))
			exit("Session not registered - You cant access this page directly");
		
		if($GLOBALS[$sessionVar] != stopDirectAccess::makeSessionId()) 
			exit("Session ids don't match - You cant access this page directly");
			
	}
	
	/**
	 * makes encoded session var
	 * @static makeSessionId()
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @return void
	 */
	static function makeSessionId() {
		return sha1(md5(AM_VALID_INCLUDE_PASSWORD));
	}
	
}

?>
