<?php
/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  --------------------------------------------------------------
 */

// PHP CONFIGURATION FILE
if(defined('E_DEPRECATED'))
{
	error_reporting(
			E_ALL
			& ~E_NOTICE
			& ~E_DEPRECATED
			& ~E_STRICT
			& ~E_CORE_ERROR
			& ~E_CORE_WARNING
	);
}
else
{
	error_reporting(
			E_ALL
			& ~E_NOTICE
			& ~E_STRICT
			& ~E_CORE_ERROR
			& ~E_CORE_WARNING
	);
}

define('_STYLE_EDIT_VALID_CALL', 1);

define('SE_DOCUMENT_ROOT', str_replace('StyleEdit', '', dirname(__FILE__)));

// HELPER: SHOP CONFIGURATION
if(file_exists(SE_DOCUMENT_ROOT . 'includes/local/configure.php'))
{
	include_once(SE_DOCUMENT_ROOT . 'includes/local/configure.php');
}
else if(file_exists(SE_DOCUMENT_ROOT . 'includes/configure.php'))
{
	include_once(SE_DOCUMENT_ROOT . 'includes/configure.php');
}

//START CONFIGURATION
define('SE_CFG_SERVER', DB_SERVER);
define('SE_CFG_USERNAME', DB_SERVER_USERNAME);
define('SE_CFG_PASSWORD', DB_SERVER_PASSWORD);
define('SE_CFG_DATABASE', DB_DATABASE);

$_SE_REQUEST = array();

if(empty($_POST) && !empty($_GET))
{
	$_SE_REQUEST = $_GET;
}
elseif(!empty($_POST))
{
	$_SE_REQUEST = $_POST;
}

define('SE_CFG_STYLE_EDIT_PATH', 'StyleEdit/');
define('SE_CFG_STYLE_EDIT_FILES_PATH', DIR_FS_CATALOG . 'StyleEdit/files/');
define('SE_CURRENT_TEMPLATE', addslashes($_SE_REQUEST['current_template']));
define('SE_CURRENT_TEMPLATE_PATH', DIR_FS_CATALOG . 'templates/' . SE_CURRENT_TEMPLATE . '/');
define('SE_CFG_IMAGES_GRADIENTS_PATH_CSS', 'backgrounds/gradients/');
define('SE_CFG_IMAGES_GRADIENTS_PATH_RELATIVE', 'templates/' . SE_CURRENT_TEMPLATE . '/backgrounds/gradients/');
define('SE_CFG_IMAGES_GRADIENTS_PATH', DIR_FS_CATALOG . 'templates/' . SE_CURRENT_TEMPLATE . '/backgrounds/gradients/');
define('SE_CFG_IMAGES_BACKGROUNDS_PATH', DIR_FS_CATALOG . 'templates/' . SE_CURRENT_TEMPLATE . '/backgrounds/');
define('SE_CFG_IMAGES_BACKGROUNDS_URL', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . SE_CURRENT_TEMPLATE . '/backgrounds/');
define('SE_CFG_IMAGES_BACKGROUNDS_URL_RELATIVE', 'templates/' . SE_CURRENT_TEMPLATE . '/backgrounds/');
define('SE_CFG_BACKUP_IMAGE_PREFIX', 'gm_se_backup_file_');
define('SE_SECURITY_TOKEN_PATH', DIR_FS_CATALOG . 'cache/');


$se_cfg_frontend_areas = array
	(
	'all' => 'Überall',
	'home' => 'Startseite',
	'product_listings' => 'Kategorien, Angebote, Suche, eBay',
	'product_infos' => 'Artikeldetails, Bewertungen',
	'content' => 'Content',
	'cart' => 'Warenkorb, Merkzettel',
	'checkout' => 'Bestellvorgang',
	'account' => 'Mein Konto'
);

@date_default_timezone_set('Europe/Berlin');
