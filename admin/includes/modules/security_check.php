<?php
/* --------------------------------------------------------------
   security_check.php 2015-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on:
   (c) 2003	 nextcommerce (security_check.php,v 1.2 2003/08/23); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: security_check.php 1221 2005-09-20 16:44:09Z mz $)

   Released under the GNU General Public License
 --------------------------------------------------------------*/

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

$file_warning     = '';
$obsolete_warning = '';

if(@is_writable(DIR_FS_CATALOG . 'includes/configure.php'))
{
	$file_warning .= '<br>' . DIR_WS_CATALOG . 'includes/configure.php';
}

if(@is_writable(DIR_FS_CATALOG . 'includes/configure.org.php'))
{
	$file_warning .= '<br>' . DIR_WS_CATALOG . 'includes/configure.org.php';
}

if(@is_writable(DIR_FS_ADMIN . 'includes/configure.php'))
{
	$file_warning .= '<br>' . DIR_WS_ADMIN . 'includes/configure.php';
}

if(@is_writable(DIR_FS_ADMIN . 'includes/configure.org.php'))
{
	$file_warning .= '<br>' . DIR_WS_ADMIN . 'includes/configure.org.php';
}

if(!@is_writable(DIR_FS_CATALOG . 'templates_c/'))
{
	$folder_warning .= '<br>' . DIR_WS_CATALOG . 'templates_c/';
}

if(!@is_writable(DIR_FS_CATALOG . 'cache/'))
{
	$folder_warning .= '<br>' . DIR_WS_CATALOG . 'cache/';
}

if(!@is_writable(DIR_FS_CATALOG . 'media/'))
{
	$folder_warning .= '<br>' . DIR_WS_CATALOG . 'media/';
}

if(!@is_writable(DIR_FS_CATALOG . 'media/content/'))
{
	$folder_warning .= '<br>' . DIR_WS_CATALOG . 'media/content/';
}

if(!@is_writable(DIR_FS_CATALOG . 'uploads/attachments'))
{
	$folder_warning .= '<br>' . DIR_FS_CATALOG . 'uploads/attachments';
}

if(!@is_writable(DIR_FS_CATALOG . 'uploads/tmp'))
{
	$folder_warning .= '<br>' . DIR_FS_CATALOG . 'uploads/tmp';
}

if(ini_get('register_globals'))
{
	$messageStack->add(TEXT_REGISTER_GLOBAL, 'error');
}

// check if robots.txt obsolete
require_once(DIR_FS_CATALOG . 'gm/inc/get_robots.php');
$check_robots_result = check_robots(DIR_WS_CATALOG);
if(!$check_robots_result)
{
	$obsolete_warning .= '<br>' . HTTP_SERVER . '/robots.txt - <a href="' . DIR_WS_ADMIN
	                     . 'robots_download.php">download robots.txt</a>';
}

if($file_warning != '')
{
	$messageStack->add(TEXT_FILE_WARNING . '<b>' . $file_warning . '</b>', 'error');
}

if($folder_warning != '')
{
	$messageStack->add(TEXT_FOLDER_WARNING . '<b>' . $folder_warning . '</b>', 'error');
}

// if any file obsolete
if($obsolete_warning != '')
{
	$messageStack->add(TEXT_OBSOLETE_WARNING . '<b>' . $obsolete_warning . '</b>', 'error');
}

// memory_limit to low
if($t_memory_limit_ok === false)
{
	$messageStack->add(sprintf(TEXT_MEMORY_LIMIT_WARNING, $t_memory_limit), 'error');
}

$payment_query = xtc_db_query("SELECT * FROM " . TABLE_CONFIGURATION . "
								WHERE
									configuration_key = 'MODULE_PAYMENT_INSTALLED' AND
									configuration_value = ''");
if(xtc_db_num_rows($payment_query))
{
	$messageStack->add(TEXT_PAYMENT_ERROR, 'warning');
}

$shipping_query = xtc_db_query("SELECT * FROM " . TABLE_CONFIGURATION . "
								WHERE
									configuration_key = 'MODULE_SHIPPING_INSTALLED' AND
									configuration_value = ''");
if(xtc_db_num_rows($shipping_query))
{
	$messageStack->add(TEXT_SHIPPING_ERROR, 'warning');
}
