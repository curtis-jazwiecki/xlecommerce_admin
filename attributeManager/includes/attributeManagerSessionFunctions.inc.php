<?php
/*
  $Id: attributeManagerSessionFunctions.inc.php,v 1.0 21/02/06 Sam West$

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

function amSessionUnregister($strSessionVar) {
	if(amSessionIsRegistered($strSessionVar)){
		tep_session_unregister($strSessionVar);
	}
	unset($GLOBALS[$strSessionVar]);
}

function amSessionRegister($strSessionVar,$value = '') {
	if(!amSessionIsRegistered($strSessionVar)) {
		tep_session_register($strSessionVar);
		$GLOBALS[$strSessionVar] = $value;
	}
}

function amSessionIsRegistered($strSessionVar) {
	return tep_session_is_registered($strSessionVar);
}

function amGetSessionVariable($strSessionVar) {
	if(isset($GLOBALS[$strSessionVar]))
		return $GLOBALS[$strSessionVar];
	return false;
}

function amSetSessionVariable($key, $value) {
	$GLOBALS[$key] = $value;
}
?>