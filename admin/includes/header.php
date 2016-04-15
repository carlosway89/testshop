<?php
/* --------------------------------------------------------------
   header.php 2015-10-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE.
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(header.php,v 1.19 2002/04/13); www.oscommerce.com
   (c) 2003	 nextcommerce (header.php,v 1.17 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: header.php 1025 2005-07-14 11:57:54Z gwinger $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

	if(!isset($jsEngineLanguage))
	{
		$jsEngineLanguage = array(); // This variable is used in the "header.php" for the EngineConfiguration object.
	}

	// Added default translations to the $jsEngineLanguage array.
	$languageTextManager = MainFactory::create_object('LanguageTextManager', array('buttons', $_SESSION['languages_id']), true);
	$jsEngineLanguage['buttons'] = $languageTextManager->get_section_array('buttons');
	$jsEngineLanguage['messages'] = $languageTextManager->get_section_array('messages');
	$jsEngineLanguage['labels'] = $languageTextManager->get_section_array('labels');
	$jsEngineLanguage['admin_labels'] = $languageTextManager->get_section_array('admin_labels');

	// Determine if "dev" environment is enabled by checking whether the ".dev-environment" file is present
	// in the "src" directory.
	$devEnvironment = (file_exists(DIR_FS_CATALOG . '.dev-environment'));

	// Add FontAwesome Fallback Script
	include DIR_FS_ADMIN . 'html/compatibility/fontawesome_fallback.php';
?>

<!--
	GLOBAL JAVASCRIPT CONFIGURATION

	Place your global JS configuration inside this <script/> tag.
-->
<script type="text/javascript">
	// Session ID is used for AJAX calls in legacy JS files.
	var session_id = <?php echo '"' . xtc_session_id() . '"'; ?>;

	// Set console support fallback flag (legacy files).
	var fb = (typeof console !== 'undefined');

	// JSEngine Configuration, see AdminPageHttpControllerResponse class.
	window.JSEngineConfiguration = {
		environment  : <?php echo ($devEnvironment) ? '"development"' : '"production"'; ?>,
		shopUrl      : <?php echo json_encode(HTTP_SERVER . rtrim(DIR_WS_CATALOG, '/')) ?>,
		translations : <?php echo (isset($jsEngineLanguage)) ? json_encode($jsEngineLanguage) : '{}'; ?>,
		languageCode : <?php echo json_encode($_SESSION['language_code']); ?>,
		pageToken    : <?php echo (isset($_SESSION['coo_page_token']))
							? json_encode($_SESSION['coo_page_token']->generate_token()) : 'null'; ?>
	};

	// Do not show loading bar on ajax requests.
	window.paceOptions = {
		elements: false,
		restartOnPushState: false,
		restartOnRequestAfter: false
	};
</script>

<!--
	 JAVASCRIPT MODULE ENGINE

	 To enable the loading of the "dev" engine files create a ".dev-environment" file in the source's
	 root directory.
-->

<?php
$suffix = ($devEnvironment) ? '' : '.min';

echo '
	<link rel="stylesheet" href="' . DIR_WS_CATALOG . 'admin/html/assets/styles/gx-admin' . $suffix . '.css" />
	<link rel="stylesheet" href="' . DIR_WS_CATALOG . 'admin/html/assets/styles/vendor' . $suffix . '.css" />
	<link rel="stylesheet" href="' . DIR_WS_CATALOG . 'JSEngine/build/vendor' . $suffix . '.css" />
	
	<script src="' . DIR_WS_CATALOG . 'JSEngine/build/vendor' . $suffix . '.js"></script>
	<script src="' . DIR_WS_CATALOG . 'admin/html/assets/javascript/vendor' . $suffix . '.js"></script>
	<script src="' . DIR_WS_CATALOG . 'JSEngine/build/require' . $suffix . '.js"></script>
	<script src="' . DIR_WS_CATALOG . 'JSEngine/build/jse' . $suffix . '.js"></script>
';
?>


<!--
	OTHER CSS & JS DEPENDENCIES
-->
<link rel="stylesheet" href="<?php echo DIR_WS_ADMIN; ?>gm/css/admin_info_box.css">
<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>includes/ckeditor/ckeditor.js"></script>
<!--[if gte IE 10]>
<link rel="stylesheet" href="<?php echo DIR_WS_CATALOG . $stylesDirPath; ?>/ie.css" />
<![endif]-->


<?php if (strstr($_SERVER['PHP_SELF'], 'gm_pdf.php') == 'gm_pdf.php'): ?>
	<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/hoverIntent/hoverIntent.js"></script>
	<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/farbtastic/farbtastic.js"></script>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/gm_pdf.js"></script>

<?php elseif(strstr($_SERVER['PHP_SELF'], 'gm_sitemap.php') == 'gm_sitemap.php'): ?>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/gm_sitemap.js"></script>

<?php elseif(strstr($_SERVER['PHP_SELF'], 'gm_logo.php') == 'gm_logo.php'): ?>
<?php elseif(strstr($_SERVER['PHP_SELF'], 'gm_counter.php') == 'gm_counter.php' || strstr($_SERVER['PHP_SELF'], 'start.php') == 'start.php'): ?>
	<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/ui/datepicker/jquery-ui-datepicker.js"></script>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/gm_counter.js"></script>

<?php elseif(strstr($_SERVER['PHP_SELF'], 'gm_bookmarks.php') == 'gm_bookmarks.php'): ?>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/gm_bookmarks.js"></script>

<?php elseif(strstr($_SERVER['PHP_SELF'], 'orders.php') == 'orders.php'): ?>
	<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/hoverIntent/hoverIntent.js"></script>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/gm_orders.js"></script>
	<?php
		$coo_js_options_control = MainFactory::create_object('JSOptionsControl', array(false));
		$t_js_options_array =  $coo_js_options_control->get_options_array($_GET);
	?>
	<script type="text/javascript"> var js_options = <?php echo json_encode($t_js_options_array) ?>; </script>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/lightbox_plugin.js"></script>

	<link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_ADMIN; ?>gm/css/lightbox.css">
	<link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_ADMIN; ?>gm/css/buttons.css">

<?php elseif(strstr($_SERVER['PHP_SELF'], 'gm_style_edit.php') == 'gm_style_edit.php'): ?>
	<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/hoverIntent/hoverIntent.js"></script>
	<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/farbtastic/farbtastic.js"></script>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/gm_style_edit.js"></script>

<?php elseif(strstr($_SERVER['PHP_SELF'], 'gm_invoicing.php') == 'gm_invoicing.php'): ?>
	<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/ui/datepicker/jquery-ui-datepicker.js"></script>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/gm_invoicing.js"></script>

<?php elseif(strstr($_SERVER['PHP_SELF'], 'gm_slider.php') == 'gm_slider.php'): ?>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/image_mapper.js"></script>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>includes/ckeditor/ckeditor.js"></script>

<?php elseif(strstr($_SERVER['PHP_SELF'], 'properties.php') == 'properties.php'): ?>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/lightbox_plugin.js"></script>

<?php elseif(strstr($_SERVER['PHP_SELF'], 'properties_combis.php') == 'properties_combis.php'): ?>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/lightbox_plugin.js"></script>
	<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/ajaxfileupload/ajaxfileupload_uncompressed.js"></script>

<?php elseif(strstr($_SERVER['PHP_SELF'], 'content_manager.php') == 'content_manager.php'): ?>
	<?php
		$coo_js_options_control = MainFactory::create_object('JSOptionsControl', array(false));
		$t_js_options_array =  $coo_js_options_control->get_options_array($_GET);
	?>
	<script type="text/javascript">
		var js_options = <?php echo json_encode($t_js_options_array) ?>;
	</script>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/lightbox_plugin.js"></script>

<?php elseif(strstr($_SERVER['PHP_SELF'], 'categories.php') == 'categories.php'): ?>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/google_categories_administration.js"></script>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/max_upload_files.js"></script>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/lightbox_plugin.js"></script>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/lightbox_google_admin_categories.js"></script>
	<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>includes/ckeditor/ckeditor.js"></script>

<?php elseif(strstr($_SERVER['PHP_SELF'], 'hermes_collection.php') == 'hermes_collection.php'): ?>
	<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/ui/datepicker/jquery-ui-datepicker.js"></script>

<?php endif; ?>

<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/jquery.tooltip.pack.js"></script>

<?php
	// BOF GM_MOD GX-Customizer:
	require_once('../gm/modules/gm_gprint_admin_header.php');
?>

<script type="text/javascript">
	<?php
		$coo_js_options_control = MainFactory::create_object('JSOptionsControl', array(false));
		$t_js_options_array =  $coo_js_options_control->get_options_array($_GET);
	?>
	var js_options = <?php echo json_encode($t_js_options_array) ?>;
</script>


<!--
	ADMINISTRATION LEFT-MENU JS HANDLERS

	The following scripts handle the main left menu functionality.
-->
<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/GMFavMaster.js"></script>
<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>gm/javascript/GMLeftBoxes.js"></script>
<script type="text/javascript">
	var gmFavMaster = new GMFavMaster();
	var gmLeftBoxes = new GMLeftBoxes();
	gmLeftBoxes.init();
</script>

<script type="text/javascript">
	$(document).ready(function() {
		$('.template_warning').tooltip({
			delay: 0,
			showURL: false,
			track: true,
			positionLeft: true,
			top: 20,
			bodyHandler: function() {
				return '<?php echo TEMPLATE_ADVICE; ?>';
			}
		});
	});

	<?php if(empty($_SESSION['screen_width'])): ?>
	$(document).ready(function() {
		var content_width;

		if(screen.width < 1280) {
			content_width = 980;
		} else {
			content_width = 1236;
		}

		$.get("<?php echo xtc_href_link('gm_counter_action.php'); ?>", {
			screen_width: content_width,
			action: "gmc_user_screen"
		});

		$(".content_width").css({
			"width": content_width + "px"
		});

		$(".content_bg_width").css({
			"width": content_width -3 + "px",
			"background-image": "url(images/gm_head/bluehead_" + content_width + ".png)"
		});
	});
	<?php endif; ?>
</script>

<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>admin_javascript.js.php?XTCsid=<?php echo xtc_session_id(); ?>"></script>


<!--
	ADMIN HEADER ELEMENTS

	Defines a page wrapper that is closed in the footer.php file and the main admin header.

	The "page_loading" module will display the main <div> once the page is ready.
-->

<div class="hidden" style="width:100%; background-color:#2c2c2c;" align="center"
     data-gx-namespace="<?php echo DIR_WS_ADMIN . 'html/assets/javascript/engine'; ?>"
     data-gx-compatibility="admin_favicon_fix init_class_fixes page_loading init_html_fixes page_nav_tabs resize_page shortcuts scroll_top"
     data-gx-extension="dynamic_shop_messages"
     data-admin_favicon_fix-status="<?php echo (gm_get_conf('GM_LOGO_FAVICON_USE')) ? 'enabled' : 'disabled'; ?>">

	<?php
	if(empty($_SESSION['screen_width']))
	{
		$_SESSION['screen_width'] = 1236;
	}
	?>
	<div class="main-top-header content_width gx-container" style="width:<?php echo $_SESSION['screen_width']; ?>px;background-color:white; margin-top:30px;" align="left">

		<?php require DIR_FS_ADMIN . 'html/compatibility/main_top_header.php'; ?>

		<div class="content_bg_width hidden" style="width:<?php echo $_SESSION['screen_width']-3; ?>px;overflow:hidden; height:113px; background-image: url(images/gm_head/bluehead_<?php echo $_SESSION['screen_width']; ?>.png);">

			<?php include(DIR_FS_ADMIN . 'includes/version_info.inc.php'); ?>

			<div id="language_flags" style="float:right; padding: 0px 10px 0px 0px; margin: -2px 0 0 0;">
				<?php
				$gm_languages = xtc_get_languages();
				foreach($gm_languages as $gm_language){
					echo '<a href="' . str_replace('"', '', xtc_href_link(basename($_SERVER["SCRIPT_NAME"]), 'language='.$gm_language['code'])."&".xtc_get_all_get_params(array('language'))) . '"><img src="../lang/' . $gm_language['directory'] . '/' . $gm_language['image'] . '" border="0" style="height: 30px;" /></a> ';
				}
				?>
			</div>
		</div>

		<div class="hidden" style="width:100%; height:38px; background-image:url(images/gm_head/blackbg.png);">
			<div style="float:left; height:38px; overflow: hidden;"><img src="images/gm_head/blackleft.png"></div>
			<div style="float:right; height:38px; overflow: hidden;"><img src="images/gm_head/blackright.png"></div>

			<script type="text/javascript">
				function check_oID_quicksearch()
				{
					var t_oID = document.getElementById('oID_quickserach').value.replace(/\s+$/, "").replace(/^\s+/, "");
					var regex = /^\d+$/;
					if(regex.test(t_oID) == false)
					{
						alert('<?php echo ERROR_QUICKSEARCH_ORDER_ID; ?>');

						return false;
					}

					document.getElementById('oID_quickserach').value = t_oID;

					return true;
				}
			</script>

			<ul id="topmenu_left">
				<li><span style="font-weight:bold"><?php echo GM_QUICK_SEARCH; ?></span></li>
				<li><form action="<?php echo xtc_href_link('customers.php'); ?>" method="get"><?php echo GM_QUICK_SEARCH_CUSTOMER; ?>: <input type="text" name="search" size="10" /></form></li>
				<li><form action="<?php echo xtc_href_link('orders.php'); ?>" method="get" onsubmit="return check_oID_quicksearch();"><?php echo GM_QUICK_SEARCH_ORDER_ID; ?>: <input type="text" name="oID" size="5" id="oID_quickserach" /><input type="hidden" name="action" value="edit" /></form></li>
				<li><form action="<?php echo xtc_href_link('categories.php'); ?>" method="get"><?php echo GM_QUICK_SEARCH_ARTICLE; ?>: <input type="text" name="search" size="10" /></form></li>
			</ul>

			<ul id="topmenu_right" style="height:38px; overflow: hidden;">
				<li><a href="<?php echo xtc_href_link('start.php')?>"><?php echo GM_TOP_MENU_START; ?></a></li>
				<li><a href="<?php echo xtc_href_link('credits.php')?>"><?php echo GM_TOP_MENU_CREDITS; ?></a></li>
				<li><a href="<?php echo xtc_href_link('../index.php')?>" target="_top"><?php echo GM_TOP_MENU_SHOP; ?></a></li>
				<li><a href="<?php echo xtc_href_link('../index.php')?>" target="_blank"><?php echo GM_TOP_MENU_PREVIEW; ?></a></li>
				<li><a href="<?php echo xtc_href_link('../logoff.php')?>"><?php echo GM_TOP_MENU_LOGOUT; ?></a></li>
				<li><div class="admin_info_box_button"></div></li>
			</ul>

		</div>

	</div>

	<div class="main-page-content content_width" style="background-color:white; margin-bottom:0px;" align="left">

		<div class="message_stack_container hidden">
			<?php if($GLOBALS['messageStack']->size > 0) echo $GLOBALS['messageStack']->output() ?>
		</div>
