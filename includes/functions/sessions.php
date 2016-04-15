<?php
/* --------------------------------------------------------------
   sessions.php 2014-11-14 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(sessions.php,v 1.16 2003/04/02); www.oscommerce.com 
   (c) 2003	 nextcommerce (sessions.php,v 1.5 2003/08/13); www.nextcommerce.org 
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: sessions.php 1195 2005-11-28 21:10:52Z mz $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

   @ini_set("session.gc_maxlifetime", 1440);
   @ini_set("session.gc_probability", 100);

	if(!defined('STORE_SESSIONS'))
	{
		define('STORE_SESSIONS', '');
	}

  if (STORE_SESSIONS == 'mysql') {
    if (!$SESS_LIFE = get_cfg_var('session.gc_maxlifetime')) {
      $SESS_LIFE = 1440;
    }

    function _sess_open($save_path, $session_name) {
      return true;
    }

    function _sess_close() {
      return true;
    }

    function _sess_read($key) {
      $qid = xtc_db_query("select value from " . TABLE_SESSIONS . " where sesskey = '" . $key . "' and expiry > '" . time() . "'", 'db_link', false);

      $value = xtc_db_fetch_array($qid);
      if ($value['value']) {
        return $value['value'];
      }

      return false;
    }

    function _sess_write($key, $val) {
      global $SESS_LIFE;

      $expiry = time() + $SESS_LIFE;
      $value = addslashes($val);

      $qid = xtc_db_query("select count(*) as total from " . TABLE_SESSIONS . " where sesskey = '" . $key . "'", 'db_link', false);
      $total = xtc_db_fetch_array($qid);

      if ($total['total'] > 0) {
        return xtc_db_query("update " . TABLE_SESSIONS . " set expiry = '" . $expiry . "', value = '" . $value . "' where sesskey = '" . $key . "'", 'db_link', false);
      } else {
        return xtc_db_query("insert into " . TABLE_SESSIONS . " values ('" . $key . "', '" . $expiry . "', '" . $value . "')", 'db_link', false);
      }
      
    }

    function _sess_destroy($key) {
      return xtc_db_query("delete from " . TABLE_SESSIONS . " where sesskey = '" . $key . "'", 'db_link', false);
    }

    function _sess_gc($maxlifetime) {
      xtc_db_query("delete from " . TABLE_SESSIONS . " where expiry < '" . time() . "'", 'db_link', false);

      return true;
    }

    session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
  }

  function xtc_session_start() {
    return session_start();
  }

  function xtc_session_register($p_variable) {
    global $session_started;

    if ($session_started == true) {
		if(!isset($_SESSION[$p_variable]))
		{
			$_SESSION[$p_variable] = $GLOBALS[$p_variable];
			return true;
		}
		else
		{
			return false;
		}
    }
  }

  function xtc_session_is_registered($p_variable) {
	return isset($_SESSION[$p_variable]);
  }

  function xtc_session_unregister($p_variable) {
	unset($_SESSION[$p_variable]);
  }

  function xtc_session_id($sessid = '') {
    if (!empty($sessid)) {
      return session_id($sessid);
    } else {
      return session_id();
    }
  }

  function xtc_session_name($name = '') {
    if (!empty($name)) {
      return session_name($name);
    } else {
      return session_name();
    }
  }

  function xtc_session_close() {
    if (function_exists('session_close')) {
      return session_close();
    }
  }

  function xtc_session_destroy() {
    return session_destroy();
  }

  function xtc_session_save_path($path = '') {
    if (!empty($path)) {
      return session_save_path($path);
    } else {
      return session_save_path();
    }
  }

  function xtc_session_recreate() {

      $session_backup = $_SESSION;

      unset($_COOKIE[xtc_session_name()]);

      xtc_session_destroy();

      if (STORE_SESSIONS == 'mysql') {
        session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
      }

      xtc_session_start();

      $_SESSION = $session_backup;
      unset($session_backup);
    
  }
?>