<?php
/*
  $Id$
admin side functions/database.php

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/

  function tep_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link') {
    global $$link;

    if (class_exists('mysqli')) {
      $$link = mysqli_connect($server, $username, $password, $database);
    } else {
      if (USE_PCONNECT == 'true') {
        $$link = mysql_pconnect($server, $username, $password);
      } else {
        $$link = mysql_connect($server, $username, $password);
      }

      if ($$link) mysql_select_db($database);
    }

    return $$link;
  }

  function tep_db_close($link = 'db_link') {
    global $$link;

    if (class_exists('mysqli')) {
      return mysqli_close($$link);
    } else {
      return mysql_close($$link);
    }
  }

  function tep_db_error($query, $errno, $error) { 
    die('<font color="#000000"><strong>' . $errno . ' - ' . $error . '<br /><br />' . $query . '<br /><br /><small><font color="#ff0000">[TEP STOP]</font></small><br /><br /></strong></font>');
  }

  function tep_db_query($query, $link = 'db_link') {
    global $$link, $logger;
    
    if(is_null($$link)) $$link = teb_db_connect();

    if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
      if (!is_object($logger)) $logger = new logger;
      $logger->write($query, 'QUERY');
    }

    if (class_exists('mysqli')) {
      $result = mysqli_query($$link, $query);
      if ($result === false) tep_db_error($query, mysqli_errno($$link), mysqli_error($$link));

      if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
        if (mysqli_error($$link)) $logger->write(mysqli_error($$link), 'ERROR');
      }
    } else {
      $result = mysql_query($query, $$link) or tep_db_error($query, mysql_errno(), mysql_error());

      if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
        if (mysql_error()) $logger->write(mysql_error(), 'ERROR');
      }
    }

    return $result;
  }

  function tep_db_perform($table, $data, $action = 'insert', $parameters = '', $link = 'db_link') {
    reset($data);
    if ($action == 'insert') {
      $query = 'insert ignore into ' . $table . ' (';
      while (list($columns, ) = each($data)) {
        $query .= $columns . ', ';
      }
      $query = substr($query, 0, -2) . ') values (';
      reset($data);
      while (list(, $value) = each($data)) {
        switch ((string)$value) {
          case 'now()':
            $query .= 'now(), ';
            break;
          case 'null':
            $query .= 'null, ';
            break;
          default:
            $query .= '\'' . tep_db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ')';
    } elseif ($action == 'update') {
      $query = 'update ' . $table . ' set ';
      while (list($columns, $value) = each($data)) {
        switch ((string)$value) {
          case 'now()':
            $query .= $columns . ' = now(), ';
            break;
          case 'null':
            $query .= $columns .= ' = null, ';
            break;
          default:
            $query .= $columns . ' = \'' . tep_db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ' where ' . $parameters;
    }

    return tep_db_query($query, $link);
  }

  function tep_db_fetch_array($db_query) {
    if (class_exists('mysqli')) {
      return mysqli_fetch_array($db_query, MYSQLI_ASSOC);
    } else {
      return mysql_fetch_array($db_query, MYSQL_ASSOC);
    }
  }

  function tep_db_result($result, $row, $field = 0) {
    if (class_exists('mysqli')) {
      if (mysqli_data_seek($result, $row)) {
        $tmp = mysqli_fetch_array($result, MYSQLI_BOTH);
        if (isset($tmp[$field])) {
          return $tmp[$field];
        } else {
          return false;
        }
      } else {
        return false;
      }
    } else {
      return mysql_result($result, $row, $field);
    }
  }

  function tep_db_num_rows($db_query) {
    if (class_exists('mysqli')) {
      return mysqli_num_rows($db_query);
    } else {
      return mysql_num_rows($db_query);
    }
  }

  function tep_db_data_seek($db_query, $row_number) {
    if (class_exists('mysqli')) {
      return mysqli_data_seek($db_query, $row_number);
    } else {
      return mysql_data_seek($db_query, $row_number);
    }
  }

  function tep_db_insert_id($link = 'db_link') {
    global $$link;

    if (class_exists('mysqli')) {
      return mysqli_insert_id($$link);
    } else {
      return mysql_insert_id($$link);
    }
  }

  function tep_db_free_result($db_query) {
    if (class_exists('mysqli')) {
      return mysqli_free_result($db_query);
    } else {
      return mysql_free_result($db_query);
    }
  }

  function tep_db_fetch_fields($db_query) {
    if (class_exists('mysqli')) {
      return mysqli_fetch_field($db_query);
    } else {
      return mysql_fetch_field($db_query);
    }
  }

  function tep_db_output($string) {
    return htmlspecialchars($string);
  }

  function tep_db_input($string, $link = 'db_link') {
    global $$link;
	if ($$link == null) tep_db_connect();
    if (class_exists('mysqli')) {
		return mysqli_real_escape_string($$link, $string);
    } elseif (function_exists('mysql_real_escape_string')) {
      return mysql_real_escape_string($string, $$link);
    } elseif (function_exists('mysql_escape_string')) {
      return mysql_escape_string($string);
    }

    return addslashes($string);
  }

  function tep_db_prepare_input($string) {
    if (is_string($string)) {
      return trim(stripslashes($string));
    } elseif (is_array($string)) {
      reset($string);
      while (list($key, $value) = each($string)) {
        $string[$key] = tep_db_prepare_input($value);
      }
      return $string;
    } else {
      return $string;
    }
  }
  
  
  function tep_db_affected_rows($link = 'db_link') {
    global $$link;

    if (class_exists('mysqli')) {
      return mysqli_affected_rows($$link);
    } else {
      return mysql_affected_rows($$link);
    }
  }
?>
