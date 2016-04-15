<?php
/* --------------------------------------------------------------
  G-Protector v1.2
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  --------------------------------------------------------------
*/

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

require_once(dirname(__FILE__) . '/config.inc.php');
require_once(GAMBIO_PROTECTOR_CLASSES_DIR . '/GProtectorLogConnectorInterface.inc.php');
require_once(GAMBIO_PROTECTOR_CLASSES_DIR . '/GProtector.inc.php');

$coo_gprotector = new GProtector();
$coo_gprotector->start();
unset($coo_gprotector);
