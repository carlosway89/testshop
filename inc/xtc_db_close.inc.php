<?php
/* --------------------------------------------------------------
  xtc_db_close.inc.php 2014-11-10 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(database.php,v 1.19 2003/03/22); www.oscommerce.com
  (c) 2003	 nextcommerce (xtc_db_close.inc.php,v 1.4 2003/08/13); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_db_close.inc.php 899 2005-04-29 02:40:57Z hhgag $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

function xtc_db_close($p_link = 'db_link')
{
	$t_link = $GLOBALS[$p_link];
	
	if(STORE_SESSIONS === 'mysql')
	{
		session_write_close();
	}
	
	if(is_resource($t_link) && get_resource_type($t_link) === 'mysql link')
	{
		return mysql_close($t_link);
	}
	
	return false;
}