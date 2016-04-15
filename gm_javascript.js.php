<?php
/* --------------------------------------------------------------
   gm_javascript.js.php 2015-10-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once('includes/application_top.php');


if(gm_get_env_info('TEMPLATE_VERSION') >= 3	&& (!isset($_GET['globals']) || $_GET['globals'] !== 'off'))
{
	$page = (isset($_GET['page'])) ? (string)$_GET['page'] : 'Global';
	
	$usermodJsMaster = MainFactory::create('UsermodJSMaster', $page);
	$files = $usermodJsMaster->get_files();
	foreach($files as $file)
	{
		// print new line avoiding conflicts with comments
		echo "\n";
		
		if(file_exists($file))
		{
			include_once($file);
		}
	}

	// print new line avoiding conflicts with comments
	echo "\n";

	xtc_db_close();
	exit;
}


if(MOBILE_ACTIVE == 'true' && (isset($_GET['section']) == false || trim($_GET['section']) != 'MobileCandy'))
{ 
	xtc_db_close(); 
	exit; 
}
if(isset($_SESSION['language_charset']))
{
	header('Content-Type: text/javascript; charset=' . $_SESSION['language_charset']);
}
else
{
	header('Content-Type: text/javascript; charset=utf-8');
}

$_SESSION['lightbox']->set_actual('false');

$coo_http_caching = MainFactory::create_object('HTTPCaching');
$coo_http_caching->start_output_buffer();

if(!isset($_GET['globals']) || $_GET['globals'] != 'off')
{
	$coo_js_options_control = MainFactory::create_object('JSOptionsControl');
	$t_js_options_array =  $coo_js_options_control->get_options_array($_GET);
	?>

	var js_options = <?php echo json_encode($t_js_options_array) ?>;

	var t_php_helper = '';
	<?php

	if(is_object($GLOBALS['coo_debugger']) && $GLOBALS['coo_debugger']->is_enabled('js') == true && gm_get_env_info('TEMPLATE_VERSION') >= FIRST_GX2_TEMPLATE_VERSION)
	{
		include_once(DIR_FS_CATALOG . 'templates/'.CURRENT_TEMPLATE.'/javascript/StopWatch.js');
	}

	//JQuery
	include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/jquery.min.js'));
	//JQuery Migrate
	include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/jquery-migrate.min.js'));
	/* BOF StyleEdit */
	if($_SESSION['style_edit_mode'] == 'edit' || $_SESSION['style_edit_mode'] == 'sos')
	{
		echo 'var style_edit_sectoken = "' . xtc_session_id() . '";';

		include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/ui/jquery-ui.js'));

		/* IE6 FIX for transparent PNGs & z-index Bug @ select boxes, iframes, etc. */
		if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0') !== FALSE)
		{
			include_once(DIR_FS_CATALOG.'StyleEdit/javascript/jquery/plugins/DD_belatedPNG.js');
			include_once(DIR_FS_CATALOG.'StyleEdit/javascript/jquery/plugins/jquery.bgiframe.js');
		}

		include_once(DIR_FS_CATALOG.'StyleEdit/javascript/jquery/plugins/fancybox/jquery.mousewheel-3.0.2.pack.js');
		include_once(DIR_FS_CATALOG.'StyleEdit/javascript/jquery/plugins/fancybox/jquery.fancybox-1.3.0.pack.js');
		include_once(DIR_FS_CATALOG.'StyleEdit/javascript/jquery/plugins/ajaxupload.js');
		include_once(DIR_FS_CATALOG.'StyleEdit/config_StyleEdit.js');

		include_once(DIR_FS_CATALOG.'StyleEdit/javascript/GMColorizer.js');
		include_once(DIR_FS_CATALOG.'StyleEdit/javascript/GMStyleMonitor.js');
		include_once(DIR_FS_CATALOG.'StyleEdit/javascript/GMStyleEditToolBox.js');
		include_once(DIR_FS_CATALOG.'StyleEdit/javascript/GMStyleEditHandler.js');
		include_once(DIR_FS_CATALOG.'StyleEdit/javascript/GMStyleEditSelector.js');
		include_once(DIR_FS_CATALOG.'StyleEdit/javascript/GMStyleEditControl.js');
		include_once(DIR_FS_CATALOG.'StyleEdit/javascript/GMBoxesPageMenu.js');
		include_once(DIR_FS_CATALOG.'StyleEdit/javascript/GMBoxesMaster.js');
		include_once(DIR_FS_CATALOG.'StyleEdit/javascript/GMUploader.js');
		include_once(DIR_FS_CATALOG.'StyleEdit/javascript/style_edit.js.php');
	}	
	/* EOF StyleEdit */

	$coo_global_extender_component = MainFactory::create_object('JSGlobalExtenderComponent');
	$coo_global_extender_component->set_data('GET', $_GET);
	$coo_global_extender_component->proceed();
}

$t_section = $_GET['section'];
if(preg_match('/[\W]+/', $t_section)) trigger_error ('gm_javascript: $_GET["section"] contains unexpected characters', E_USER_ERROR);

$f_page = $_GET['page'];
$c_page = '';
if(is_string($f_page))
{
	$c_page = trim((string)$f_page);
}

if($c_page != '')
{
	$t_class_name_suffix = 'ExtenderComponent';

	$coo_request_router = MainFactory::create_object('RequestRouter', array($t_class_name_suffix));
	$coo_request_router->set_data('GET', $_GET);
	$t_class_name = 'JS' . $c_page;
	$t_proceed_status = $coo_request_router->proceed($t_class_name);
	if($t_proceed_status != true)
	{
		trigger_error('could not proceed module ['.htmlentities_wrapper($t_class_name).']', E_USER_ERROR);
	}
}

mysql_close();


$t_js_content = $coo_http_caching->stop_output_buffer();

$coo_http_caching->send_header($t_js_content, false, false, 'public', '', '');
$coo_http_caching->check_cache($t_js_content);
$coo_http_caching->start_gzip();

echo $t_js_content;
