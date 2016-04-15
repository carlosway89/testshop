<?php
/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2010 Gambio GmbH
  --------------------------------------------------------------
*/
if(defined('_STYLE_EDIT_VALID_CALL') === false)
{	
	die('');
}
	include('config_StyleEdit.php');

$act = $_GET['act'];
$gmSeDatabase = new GMSEDatabase();
$gmBoxesMaster = new GMBoxesMaster($_POST['current_template'], $gmSeDatabase);

switch($act)
{
	case 'update_position':
		//print_r($_GET);
		//print_r($_POST);

		$box_name = addslashes($_POST['box_name']);
		$position = addslashes($_POST['position']);
		
		$box_name = substr($box_name, 2); //REMOVE prefix 'c_'
		
		if(strstr($box_name, ' ') !== false)
		{
			$box_name = substr($box_name, 0, strpos($box_name, ' '));
		}

		$gmBoxesMaster->set_position($box_name, $position);

		$gmBoxesMaster->organize_positions();
		break;
		
	case 'update_status':
		$box_name = addslashes($_POST['box_name']);
		$status 	= addslashes($_POST['status']);
		
		$box_name = substr($box_name, 2); //REMOVE prefix 'c_'
		
		if(strstr($box_name, ' ') !== false)
		{
			$box_name = substr($box_name, 0, strpos($box_name, ' '));
		}
		
		$gmBoxesMaster->set_status($box_name, $status);
		break;
		
	case 'get_status_json':
		$json_output = $gmBoxesMaster->get_status_json();
		echo $json_output;
		break;
		
	case 'get_page_menu':

		$box_name = addslashes($_POST['box_name']);
		$box_name = substr($box_name, 2); 

		if(strstr($box_name, ' ') !== false)
		{
			$box_name = substr($box_name, 0, strpos($box_name, ' '));
		}

		echo $gmBoxesMaster->get_page_menu($box_name, $se_cfg_frontend_areas); 

		break;

	case 'update_page_menu':

		$page_id		= addslashes($_POST['page_id']);
		$page_active	= addslashes($_POST['page_active']);
		$box_name		= addslashes($_POST['box_name']);
		$box_name		= substr($box_name, 2); 

		if(strstr($box_name, ' ') !== false)
		{
			$box_name = substr($box_name, 0, strpos($box_name, ' '));
		}

		$gmBoxesMaster->update_page_menu($box_name, $page_id, $page_active, $se_cfg_frontend_areas); 

		break;
		
	default:
	break;
}
$gmSeDatabase->getCooMySQLi()->close();
?>