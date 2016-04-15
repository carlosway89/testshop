<?php
/* --------------------------------------------------------------
   xtc_db_connect_installer.inc.php 2013-09-11 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(database.php,v 1.2 2002/03/02); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_db_connect_installer.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_db_connect_installer.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
  function xtc_db_connect_installer($server, $username, $password, $link = 'db_link') {
    global $$link, $db_error;

    $db_error = false;

    if (!$server) {
      $db_error = 'No Server selected.';
      return false;
    }

    $$link = @mysql_connect($server, $username, $password) or $db_error = mysql_error();
	
	if ($$link) {
		$t_mysql_version = @mysql_get_server_info();
		if (!empty($t_mysql_version) && version_compare($t_mysql_version, '5', '>=')) @mysql_query("SET SESSION sql_mode=''", $$link);

		@mysql_query("SET SQL_BIG_SELECTS=1", $$link);

		if (version_compare(PHP_VERSION, '5.2.3', '>=')) {
			mysql_set_charset('utf8', $$link);
		} else {
			mysql_query("SET NAMES utf8", $$link);
		}
	}
    
    return $$link;
  }
