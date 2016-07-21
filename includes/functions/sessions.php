<?php
/*
  $Id: sessions.php,v 1.9 2003/06/23 01:20:05 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

  if (STORE_SESSIONS == 'mysql') {
    if (!$SESS_LIFE = get_cfg_var('session.gc_maxlifetime')) {
      $SESS_LIFE = 1440;
      $SESS_LIFE = SESSION_TIMEOUT_DURATION_IN_SECONDS;
    }

    function _sess_open($save_path, $session_name) {
      return true;
    }

    function _sess_close() {
      return true;
    }

    function _sess_read($key) {
      $qid = tep_db_query("select value from " . TABLE_SESSIONS . " where sesskey = '" . tep_db_input($key) . "' and expiry > '" . time() . "'");

      $value = tep_db_fetch_array($qid);
      if ($value['value']) {
        return $value['value'];
      }

      return false;
    }

    function _sess_write($key, $val) {
      global $SESS_LIFE;

      $expiry = time() + $SESS_LIFE;
      $value = $val;

      $qid = tep_db_query("select count(*) as total from " . TABLE_SESSIONS . " where sesskey = '" . tep_db_input($key) . "'");
      $total = tep_db_fetch_array($qid);

      if ($total['total'] > 0) {
        return tep_db_query("update " . TABLE_SESSIONS . " set expiry = '" . tep_db_input($expiry) . "', value = '" . tep_db_input($value) . "' where sesskey = '" . tep_db_input($key) . "'");
      } else {
        return tep_db_query("insert into " . TABLE_SESSIONS . " values ('" . tep_db_input($key) . "', '" . tep_db_input($expiry) . "', '" . tep_db_input($value) . "')");
      }
    }

    function _sess_destroy($key) {
      return tep_db_query("delete from " . TABLE_SESSIONS . " where sesskey = '" . tep_db_input($key) . "'");
    }

    function _sess_gc($maxlifetime) {
      tep_db_query("delete from " . TABLE_SESSIONS . " where expiry < '" . time() . "'");

      return true;
    }

    session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
  }

  function tep_session_start() {
    //return session_start();
	$success = session_start();
	
    if ($success && count($_SESSION))
    {
      $session_keys = array_keys($_SESSION);
      foreach($session_keys as $variable)
      {
        link_session_variable($variable, true);
      }
    }

    return $success;
  }

  function tep_session_register($variable) {
    //return session_register($variable);
    link_session_variable($variable, true);

    return true;
  }

  function tep_session_is_registered($variable) {
    //return session_is_registered($variable);
	    return isset($_SESSION[$variable]);
  }

  function tep_session_unregister($variable) {
    //return session_unregister($variable);
    link_session_variable($variable, false);
    unset($_SESSION[$variable]);
	return true;
  }

  function tep_session_id($sessid = '') {
    if ($sessid != '') {
      return session_id($sessid);
    } else {
      return session_id();
    }
  }

  function tep_session_name($name = '') {
    if ($name != '') {
      return session_name($name);
    } else {
      return session_name();
    }
  }

  function tep_session_close() {
    if (count($_SESSION))
    {
      $session_keys = array_keys($_SESSION);
      foreach($session_keys as $variable)
      {
        link_session_variable($variable, false);
      }
    }
    if (function_exists('session_close')) {
      return session_close();
    }
  }

  function tep_session_destroy() {
    if (count($_SESSION))
    {
      $session_keys = array_keys($_SESSION);
      foreach($session_keys as $variable)
      {
        link_session_variable($variable, false);
        unset($_SESSION[$variable]);
      }
    }
    return session_destroy();
  }

  function tep_session_save_path($path = '') {
    if ($path != '') {
      return session_save_path($path);
    } else {
      return session_save_path();
    }
  }
  
  function link_session_variable($var_name, $map)
  {
    if ($map)
    {
      // Map global to session variable. If the global variable is already set to some value
      // then its value overwrites the session variable. I **THINK** this is correct behaviour
      if (isset($GLOBALS[$var_name]))
      {
        $_SESSION[$var_name] = $GLOBALS[$var_name];
      }

      $GLOBALS[$var_name] =& $_SESSION[$var_name];
    }
    else
    {
      // Unmap global from session variable. Note that the global variable keeps the value of
      // the session variable. This should be unnecessary but it reflects the same behaviour
      // as having register_globals enabled, so in case the OSC code assumes this behaviour,
      // it is reproduced here
      $nothing = 0;
      $GLOBALS[$var_name] =& $nothing;
      unset($GLOBALS[$var_name]);
      $GLOBALS[$var_name] = $_SESSION[$var_name];
    }
  }
?>
