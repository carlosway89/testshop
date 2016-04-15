<?php
/* --------------------------------------------------------------
	StyleEdit v2.0
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	--------------------------------------------------------------
*/

include('config_StyleEdit.php');

if (!function_exists('json_encode')) 
{
	include_once('classes/GMSEJSON.php');
}

include_once('lang/german/german.php');
include_once('classes/GMSEDatabase.php');
include_once('classes/GMSESecurity.php');
include_once('classes/GMBoxesMaster.php');
include_once('classes/GMCSSManager.php');
include_once('classes/GMCSS.php');
include_once('classes/GMSEError.php');
include_once('classes/GMSEAjax.php');

$cooMySQLi = new GMSEDatabase();
$coo_sec_token = new GMSESecurity($cooMySQLi);
$coo_ajax = new GMSEAjax($_SE_REQUEST, $cooMySQLi);

$t_token = $_GET['token'];
if(empty($t_token))
{
	$t_token = $_POST['token'];
}

if($coo_sec_token->valid_sec_token($t_token) === false) 
{
	$coo_ajax->_die();
}
else
{
	$coo_ajax->_request();
}

$cooMySQLi->close();

unset($coo_ajax);
