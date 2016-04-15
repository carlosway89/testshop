<?php
/* --------------------------------------------------------------
   categories.php 2015-09-28 gm
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
   (c) 2002-2003 osCommerce(categories.php,v 1.140 2003/03/24); www.oscommerce.com
   (c) 2003  nextcommerce (categories.php,v 1.37 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: categories.php 1249 2005-09-27 12:06:40Z gwinger $)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Enable_Disable_Categories 1.3               Autor: Mikel Williams | mikel@ladykatcostumes.com
   New Attribute Manager v4b                   Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   Category Descriptions (Version: 1.5 MS2)    Original Author:   Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require_once ('includes/application_top.php');
// Include JS Language Vars
if(!isset($jsEngineLanguage))
{
	$jsEngineLanguage = array();
}
$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
$jsEngineLanguage['categories'] = $languageTextManager->get_section_array('categories');
$jsEngineLanguage['admin_buttons'] = $languageTextManager->get_section_array('admin_buttons');
$jsEngineLanguage['gm_general'] = $languageTextManager->get_section_array('gm_general');
$jsEngineLanguage['new_product'] = $languageTextManager->get_section_array('new_product');

$coo_lang_file_master->init_from_lang_file('lang/' . basename($_SESSION['language']). '/admin/gm_gmotion.php');
$coo_lang_file_master->init_from_lang_file('lang/' . basename($_SESSION['language']). '/admin/gm_product_images.php');
require_once ('includes/classes/'.FILENAME_IMAGEMANIPULATOR);
/* magnalister v1.0.1 */
if (function_exists('magnaExecute')) magnaExecute('magnaInventoryUpdate', array('action' => 'inventoryUpdate'), array('inventoryUpdate.php'));
/* END magnalister */
// BOF GM_MOD
include_once(DIR_FS_ADMIN . 'gm/classes/GMProductUpload.php');
include_once(DIR_FS_ADMIN . 'gm/classes/GMUpload.php');
include_once(DIR_FS_ADMIN . 'gm/classes/GMAltText.php');
// EOF GM_MOD
require_once ('includes/classes/categories.php');
require_once (DIR_FS_INC.'xtc_get_tax_rate.inc.php');
require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');
require_once (DIR_WS_CLASSES.'currencies.php');

$currencies = new currencies();
$catfunc = new categories();

//this is used only by group_prices
if ($_GET['function']) {
	switch ($_GET['function']) {
		case 'delete' :
			if($_SESSION['coo_page_token']->is_valid($_GET['page_token']))
			{
				xtc_db_query("DELETE FROM personal_offers_by_customers_status_".(int) $_GET['statusID']."
								WHERE
									products_id = '".(int) $_GET['pID']."' AND
									quantity    = '".(double) $_GET['quantity']."'");
			}
			break;
	}
	xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&action=new_product&pID='.(int) $_GET['pID']));
}

// Multi-Status Change, separated from $_GET['action']
// --- MULTI STATUS ---
if (isset ($_POST['multi_status_on'])) {
	if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
	{
		//set multi_categories status=on
		if (is_array($_POST['multi_categories'])) {
			foreach ($_POST['multi_categories'] AS $category_id) {
				$catfunc->set_category_recursive($category_id, '1');
			}
		}
		//set multi_products status=on
		if (is_array($_POST['multi_products'])) {
			foreach ($_POST['multi_products'] AS $product_id) {
				$product_id = substr($product_id, strrpos($product_id, '_') + 1);
				$catfunc->set_product_status($product_id, '1');
			}
		}
		xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&'.xtc_get_all_get_params(array ('cPath', 'action', 'pID', 'cID'))));
	}
}

if (isset ($_POST['multi_status_off'])) {
	if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
	{
		//set multi_categories status=off
		if (is_array($_POST['multi_categories'])) {
			foreach ($_POST['multi_categories'] AS $category_id) {
				$catfunc->set_category_recursive($category_id, "0");
			}
		}
		//set multi_products status=off
		if (is_array($_POST['multi_products'])) {
			foreach ($_POST['multi_products'] AS $product_id) {
				$product_id = substr($product_id, strrpos($product_id, '_') + 1);
				$catfunc->set_product_status($product_id, "0");
			}
		}
		xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&'.xtc_get_all_get_params(array ('cPath', 'action', 'pID', 'cID'))));
	}
}
// --- MULTI STATUS ENDS ---

//regular actions
if ($_REQUEST['action']) {
	switch ($_REQUEST['action']) {

		case 'setcflag':
			if($_SESSION['coo_page_token']->is_valid($_GET['page_token']))
			{
				if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
					if ($_GET['cID']) {
						$catfunc->set_category_recursive($_GET['cID'], $_GET['flag']);
					}
				}
			}
			$coo_cache_control = MainFactory::create_object('CacheControl');
			$coo_cache_control->clear_content_view_cache();
			$coo_cache_control->clear_data_cache();
			$coo_cache_control->remove_reset_token();
			xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&cID='.$_GET['cID']));
			break;
			//EOB setcflag

		case 'setpflag':
			if($_SESSION['coo_page_token']->is_valid($_GET['page_token']))
			{
				if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
					if ($_GET['pID']) {
						$catfunc->set_product_status($_GET['pID'], $_GET['flag']);
					}
				}
				if ($_GET['pID']) {
					xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&pID='.$_GET['pID']));
				} else {
					xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&cID='.$_GET['cID']));
				}
			}
			break;
			//EOB setpflag

		case 'setsflag':
			if($_SESSION['coo_page_token']->is_valid($_GET['page_token']))
			{
				if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
					if ($_GET['pID']) {
						$catfunc->set_product_startpage($_GET['pID'], $_GET['flag']);
						if ($_GET['flag'] == '1') $catfunc->link_product($_GET['pID'], 0);
					}
				}
				if ($_GET['pID']) {
					xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&pID='.$_GET['pID']));
				} else {
					xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&cID='.$_GET['cID']));
				}
			}
			break;
			//EOB setsflag

		case 'update_category' :
      		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				$t_categories_id = $catfunc->insert_category($_POST, '', 'update');
			}
			break;

		case 'insert_category' :
			if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				$t_categories_id = $catfunc->insert_category($_POST, $current_category_id);
			}
			break;

		case 'update_product' :
			if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				$t_products_id = $catfunc->insert_product($_POST, '', 'update');
			}
			break;

		case 'insert_product' :
			if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				$categories = array_key_exists('categories', $_POST) ? $_POST['categories'] : $current_category_id;
				$t_products_id = $catfunc->insert_product($_POST, $categories);
			}
			break;

		case 'edit_crossselling' :
			$catfunc->edit_cross_sell($_REQUEST);
			break;

		case 'multi_action_confirm' :
			if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				// --- MULTI DELETE ---
				if (isset ($_POST['multi_delete_confirm'])) {
					//delete multi_categories
					if (is_array($_POST['multi_categories'])) {
						foreach ($_POST['multi_categories'] AS $category_id) {
							$catfunc->remove_categories($category_id);
						}
					}
					//delete multi_products
					if (is_array($_POST['multi_products']) && is_array($_POST['multi_products_categories'])) {
						foreach ($_POST['multi_products'] AS $product_id) {
							$product_id = substr($product_id, strrpos($product_id, '_') + 1);
							$catfunc->delete_product($product_id, $_POST['multi_products_categories'][$product_id]);
						}
					}
				}
				// --- MULTI DELETE ENDS ---

				// --- MULTI MOVE ---
				if (isset ($_POST['multi_move_confirm'])) {
					//move multi_categories
					if (is_array($_POST['multi_categories']) && xtc_not_null($_POST['move_to_category_id'])) {
						foreach ($_POST['multi_categories'] AS $category_id) {
							$dest_category_id = xtc_db_prepare_input($_POST['move_to_category_id']);
							if ($category_id != $dest_category_id) {
								$catfunc->move_category($category_id, $dest_category_id);
							}
						}
					}
					//move multi_products
					if (is_array($_POST['multi_products']) && xtc_not_null($_POST['move_to_category_id'])) {
						foreach ($_POST['multi_products'] AS $product_id) {
							$category_id = substr($product_id, 0, strrpos($product_id, '_'));
							$product_id = substr($product_id, strrpos($product_id, '_') + 1);
							$product_id = xtc_db_prepare_input($product_id);
							$src_category_id = xtc_db_prepare_input($_POST['src_category_id']);
							if (!isset($_POST['src_category_id']) || empty($_POST['src_category_id']))
							{
								$src_category_id = $category_id;
							}
							$dest_category_id = xtc_db_prepare_input($_POST['move_to_category_id']);
							$catfunc->move_product($product_id, $src_category_id, $dest_category_id);
						}
					}
					$catfunc->set_redirect_url(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$dest_category_id.'&'.xtc_get_all_get_params(array ('cPath', 'action', 'pID', 'cID'))));
					break;
				}
				// --- MULTI MOVE ENDS ---

				// --- MULTI COPY ---
				if (isset ($_POST['multi_copy_confirm'])) {
					//copy multi_categories
					if (is_array($_POST['multi_categories']) && (is_array($_POST['dest_cat_ids']) || xtc_not_null($_POST['dest_category_id']))) {
						$_SESSION['copied'] = array ();
						foreach ($_POST['multi_categories'] AS $category_id) {
							if (is_array($_POST['dest_cat_ids'])) {
								foreach ($_POST['dest_cat_ids'] AS $dest_category_id) {
									if ($_POST['copy_as'] == 'link') {
										$catfunc->copy_category($category_id, $dest_category_id, 'link');
									}
									elseif ($_POST['copy_as'] == 'duplicate') {
										$catfunc->copy_category($category_id, $dest_category_id, 'duplicate');
									} else {
										$messageStack->add_session('Copy type not specified.', 'error');
									}
								}
							}
							elseif (xtc_not_null($_POST['dest_category_id'])) {
								if ($_POST['copy_as'] == 'link') {
									$catfunc->copy_category($category_id, $_POST['dest_category_id'], 'link');
								}
								elseif ($_POST['copy_as'] == 'duplicate') {
									$catfunc->copy_category($category_id, $_POST['dest_category_id'], 'duplicate');
								} else {
									$messageStack->add_session('Copy type not specified.', 'error');
								}
							}
						}
						unset ($_SESSION['copied']);
					}
					//copy multi_products
					if (is_array($_POST['multi_products']) && (is_array($_POST['dest_cat_ids']) || xtc_not_null($_POST['dest_category_id']))) {
						foreach ($_POST['multi_products'] AS $product_id) {
							$product_id = substr($product_id, strrpos($product_id, '_') + 1);
							$product_id = xtc_db_prepare_input($product_id);
							if (is_array($_POST['dest_cat_ids'])) {
								foreach ($_POST['dest_cat_ids'] AS $dest_category_id) {
									$dest_category_id = xtc_db_prepare_input($dest_category_id);
									if ($_POST['copy_as'] == 'link') {
										$catfunc->link_product($product_id, $dest_category_id);
									}
									elseif ($_POST['copy_as'] == 'duplicate') {
										$catfunc->duplicate_product($product_id, $dest_category_id);
									} else {
										$messageStack->add_session('Copy type not specified.', 'error');
									}
								}
							}
							elseif (xtc_not_null($_POST['dest_category_id'])) {
								$dest_category_id = xtc_db_prepare_input($_POST['dest_category_id']);
								if ($_POST['copy_as'] == 'link') {
									$catfunc->link_product($product_id, $dest_category_id);
								}
								elseif ($_POST['copy_as'] == 'duplicate') {
									$catfunc->duplicate_product($product_id, $dest_category_id);
								} else {
									$messageStack->add_session('Copy type not specified.', 'error');
								}
							}
						}
					}
					$catfunc->set_redirect_url(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$dest_category_id.'&'.xtc_get_all_get_params(array ('cPath', 'action', 'pID', 'cID'))));
					break;
				}
				// --- MULTI COPY ENDS ---

				$catfunc->set_redirect_url(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&'.xtc_get_all_get_params(array ('cPath', 'action', 'pID', 'cID'))));
			}
			break;
			#EOB multi_action_confirm

	} //EOB switch action
} //EOB if action

// CATEGORIES-FILTER
$coo_feature_helper = MainFactory::create_object('FeatureFunctionHelper');
$coo_control        = MainFactory::create_object('FeatureControl');
$feature_array      = $coo_control->get_feature_array();

// check if the catalog image directory exists
if (is_dir(DIR_FS_CATALOG_IMAGES)) {
	if (!is_writeable(DIR_FS_CATALOG_IMAGES))
		$messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE . DIR_FS_CATALOG_IMAGES, 'error');
} else {
	$messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST . DIR_FS_CATALOG_IMAGES, 'error');
}


$coo_admin_categories_extender_component = MainFactory::create_object('AdminCategoriesExtenderComponent');
$coo_admin_categories_extender_component->set_data('GET', $_GET);
$coo_admin_categories_extender_component->set_data('POST', $_POST);
if($t_products_id !== null && is_numeric($t_products_id))
{
	$coo_admin_categories_extender_component->set_data('products_id', $t_products_id);
}
if($t_categories_id !== null && is_numeric($t_categories_id))
{
	$coo_admin_categories_extender_component->set_data('categories_id', $t_categories_id);
}
$coo_admin_categories_extender_component->proceed();

$t_redirect_url = $catfunc->get_redirect_url();
if(xtc_not_null($t_redirect_url))
{
	xtc_redirect($t_redirect_url);
}


// redirect to categories overview to prevent adding content twice
if(isset($_GET['action'])
	&& in_array($_GET['action'], array('insert_category', 'insert_product', 'update_product', 'update_category')) == true )
{
	xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('action'))));
}

// end of pre-checks and actions, HTML output follows
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<?php
		if(preg_match('/MSIE [\d]{2}\./i', $_SERVER['HTTP_USER_AGENT']))
		{
		?>
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" />
		<?php
		}
		?>
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
		<link rel="stylesheet" type="text/css" href="gm/css/lightbox.css">
		<link rel="stylesheet" type="text/css" href="gm/css/buttons.css">
		<link rel="stylesheet" type="text/css" href="gm/css/feature_set.css">
		<link rel="stylesheet" type="text/css" href="gm/css/scrollpane.css">
		<link rel="stylesheet" type="text/css" href="gm/css/article_tabs.css">
		<script type="text/javascript" src="includes/general.js"></script>
		<script type="text/javascript" src="includes/javascript/categories.js"></script>

		<?php
		$coo_js_options_control = MainFactory::create_object('JSOptionsControl', array(false));
		$t_js_options_array =  $coo_js_options_control->get_options_array($_GET);
		?>
		<script type="text/javascript"> var js_options = <?php echo json_encode($t_js_options_array) ?>; </script>
</head>
<body style="margin: 0; background-color: #FFFFFF">

		<div id="spiffycalendar" class="text"></div>
		<!-- header //-->
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<!-- header_eof //-->

		<!-- body //-->
		<table width="100%" style="border:none" class="hide-second-tr">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
    				<!-- left_navigation //-->
					<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					<!-- left_navigation_eof //-->
				</td>
				<!-- body_text //-->
				<td class="boxCenter" valign="top" width="100%">
            <?php
            //----- new_category / edit_category (when ALLOW_CATEGORY_DESCRIPTIONS is 'true') -----
            if ($_GET['action'] == 'new_category' || $_GET['action'] == 'edit_category') {
	            include DIR_FS_ADMIN . 'html/compatibility/new_category.php';
            }
            elseif ($_GET['action'] == 'new_product') {
	            include DIR_FS_ADMIN . 'html/compatibility/product/new_product.inc.php';
            }
            elseif ($_GET['action'] == 'edit_crossselling') {
              include (DIR_WS_MODULES.'cross_selling.php');
            } else {
              //set $cPath to 0 if not set - FireFox workaround, didn't work when de/activating categories and $cPath wasn't set
              if (!$cPath) { $cPath = '0'; }
              include (DIR_WS_MODULES.'categories_view.php');
            }
            ?>
          <!-- close tables from above modules //-->
        </td>
				<!-- body_text_eof //-->
			</tr>
		</table>
		<!-- body_eof //-->

		<!-- footer //-->
        <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
