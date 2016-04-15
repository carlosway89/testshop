<?php
/* --------------------------------------------------------------
  database.php 2014-08-05 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
  --------------------------------------------------------------

  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(database.php,v 1.22 2003/03/22); www.oscommerce.com
  (c) 2003	 nextcommerce (database.php,v 1.6 2003/08/18); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: database.php 950 2005-05-14 16:45:21Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_FS_INC . 'xtc_db_perform.inc.php');

function xtc_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link')
{
	global $$link;

	if(USE_PCONNECT == 'true')
	{
		$$link = mysql_pconnect($server, $username, $password);
	}
	else
	{
		$$link = mysql_connect($server, $username, $password);
	}

	if($$link)
	{
		$t_mysql_version = @mysql_get_server_info();
		if(!empty($t_mysql_version) && version_compare($t_mysql_version, '5', '>='))
		{
			@mysql_query("SET SESSION sql_mode=''", $$link);
		}

		@mysql_query("SET SQL_BIG_SELECTS=1", $$link);

		mysql_select_db($database, $$link);

		if(version_compare(PHP_VERSION, '5.2.3', '>='))
		{
			mysql_set_charset('utf8', $$link);
		}
		else
		{
			mysql_query("SET NAMES utf8", $$link);
		}
	}

	return $$link;
}

// db connection for Servicedatabase  
function service_xtc_db_connect($server_service = SERVICE_DB_SERVER, $username_service = SERVICE_DB_SERVER_USERNAME, $password_service = SERVICE_DB_SERVER_PASSWORD, $database_service = SERVICE_DB_DATABASE, $link_service = 'db_link_service')
{
	global $$link_service;

	if(SERVICE_USE_PCONNECT == 'true')
	{
		$$link_service = mysql_pconnect($server_service, $username_service, $password_service);
	}
	else
	{
		$$link_service = mysql_connect($server_service, $username_service, $password_service);
	}

	if($$link_service)
	{
		$t_mysql_version = @mysql_get_server_info();
		if(!empty($t_mysql_version) && version_compare($t_mysql_version, '5', '>='))
		{
			@mysql_query("SET SESSION sql_mode=''", $$link_service);
		}

		@mysql_query("SET SQL_BIG_SELECTS=1", $$link_service);

		mysql_select_db($database_service, $$link_service);

		if(version_compare(PHP_VERSION, '5.2.3', '>='))
		{
			mysql_set_charset('utf8', $$link_service);
		}
		else
		{
			mysql_query("SET NAMES utf8", $$link_service);
		}
	}

	return $$link_service;
}

function xtc_db_close($p_link = 'db_link')
{
	$t_link = $GLOBALS[$p_link];
	
	if(STORE_SESSIONS === 'mysql')
	{
		session_write_close();
	}
	
	$t_close_result = mysql_close($t_link);
	
	return $t_close_result;
}

// db connection for Servicedatabase  
function service_xtc_db_close($link_service = 'db_link_service')
{
	global $$link_service;

	return mysql_close($$link_service);
}

function xtc_db_error($p_query, $p_errno, $p_error)
{
	$coo_logger = LogControl::get_instance();
	$coo_logger->notice($p_error, 'error_handler', 'errors', 'notice', 'SQL ERROR', $p_errno, 'Query:' . "\r\n" . trim($p_query));
	trigger_error('SQL Error', E_USER_ERROR);
}

function xtc_db_query($query, $link = 'db_link', $p_enable_data_cache = false)
{
	global $$link;

	$result = mysql_query($query, $$link) or xtc_db_error($query, mysql_errno(), mysql_error());
	
	$coo_logger = LogControl::get_instance();
	$coo_logger->write_sql_log($query);

	return $result;
}

// db connection for Servicedatabase 
function service_xtc_db_query($query, $link_service = 'db_link_service')
{
	global $$link_service;

	$result = mysql_query($query, $$link_service) or xtc_db_error($query, mysql_errno(), mysql_error());
	
	$coo_logger = LogControl::get_instance();
	$coo_logger->write_sql_log($query);

	return $result;
}

function xtc_db_fetch_array($db_query)
{
	return mysql_fetch_array($db_query, MYSQL_ASSOC);
}

function xtc_db_result($result, $row, $field = '')
{
	return mysql_result($result, $row, $field);
}

function xtc_db_num_rows($db_query)
{
	return mysql_num_rows($db_query);
}

function xtc_db_data_seek($db_query, $row_number)
{
	return mysql_data_seek($db_query, $row_number);
}

function xtc_db_insert_id()
{
	return mysql_insert_id();
}

function xtc_db_free_result($db_query)
{
	return mysql_free_result($db_query);
}

function xtc_db_fetch_fields($db_query)
{
	return mysql_fetch_field($db_query);
}

function xtc_db_output($string)
{
	return htmlspecialchars_wrapper($string);
}

function xtc_db_input($string)
{
	return addslashes($string);
}

function xtc_db_prepare_input($string)
{
	if(is_string($string))
	{
		return trim(stripslashes($string));
	}
	elseif(is_array($string))
	{
		reset($string);
		
		while(list($key, $value) = each($string))
		{
			$string[$key] = xtc_db_prepare_input($value);
		}
		
		return $string;
	}
	else
	{
		return $string;
	}
}
