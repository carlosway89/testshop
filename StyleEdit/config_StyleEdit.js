/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2010 Gambio GmbH
  --------------------------------------------------------------
*/

// JAVASCRIPT CONFIGURATION FILE


var style_edit_config_SOS							= <?php echo ($_SESSION['style_edit_mode'] == 'sos') ? 'true' : 'false'; ?>;
var style_edit_config_SESSION_ID					= '<?php echo htmlspecialchars($_GET['XTCsid']); ?>';
var style_edit_config_SESSION_NAME					= '<?php echo htmlspecialchars($_GET['XTCsid_name']); ?>';
var style_edit_config_CURRENT_TEMPLATE				= '<?php echo htmlspecialchars($_GET['current_template']); ?>';
var style_edit_config_TEMPLATE_DIR					= 'templates/' + style_edit_config_CURRENT_TEMPLATE + '/';
var style_edit_config_GRADIENTS_DIR					= 'backgrounds/gradients/';
var style_edit_config_GRADIENTS_DIR_RELATIVE		= 'templates/' + style_edit_config_CURRENT_TEMPLATE + '/backgrounds/gradients/';
var style_edit_config_BACKGROUNDS_DIR				= 'templates/' + style_edit_config_CURRENT_TEMPLATE + '/backgrounds/';
var style_edit_config_EDIT_CALL 					= 'index.php?style_edit_mode=edit';
var style_edit_config_EXIT_CALL 					= 'index.php?style_edit_mode=stop';
var style_edit_img_loading		 					= '<img src="StyleEdit/images/uploading.gif" WIDTH="29" HEIGHT="6" BORDER="0" ALT="loading">';

/* ADMIN TEMPLATE CONFIGURATION */
var style_edit_config_FANCY_BOX_ADMIN_CONF_LINK		= 'admin/template_configuration.php';
var style_edit_config_FANCY_BOX_WIDTH				= 600;
var style_edit_config_FANCY_BOX_HEIGHT				= 558;