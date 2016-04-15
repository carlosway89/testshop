<?php
/* --------------------------------------------------------------
   gm_callback_service.php 2008-08-10 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(conditions.php,v 1.21 2003/02/13); www.oscommerce.com 
   (c) 2003	 nextcommerce (shop_content.php,v 1.1 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: shop_content.php 1303 2005-10-12 16:47:31Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/


// create smarty elements
$smarty_gm_callback_service = new Smarty;

$coo_captcha = MainFactory::create_object('Captcha');
$_SESSION['captcha_object'] = &$coo_captcha;

$get_content_text = xtc_db_query("SELECT content_text 
																	FROM content_manager 
																	WHERE 
																		content_group = '14' 
																		AND languages_id = '" . $_SESSION['languages_id'] ."'");
if(xtc_db_num_rows($get_content_text) == 1){
	$content_text = xtc_db_fetch_array($get_content_text);
	$smarty_gm_callback_service->assign('CONTENT_TEXT', $content_text['content_text']);
}

$smarty_gm_callback_service->assign('NECESSARY_INFO', GM_CALLBACK_SERVICE_NECESSARY_INFO);
$smarty_gm_callback_service->assign('NAME', GM_CALLBACK_SERVICE_NAME);
$smarty_gm_callback_service->assign('NAME_VALUE', $_SESSION['customer_first_name'] . ' ' . $_SESSION['customer_last_name']);
$smarty_gm_callback_service->assign('EMAIL', GM_CALLBACK_SERVICE_EMAIL);
$smarty_gm_callback_service->assign('TELEPHONE', GM_CALLBACK_SERVICE_TELEPHONE);
$smarty_gm_callback_service->assign('TIME', GM_CALLBACK_SERVICE_TIME);
$smarty_gm_callback_service->assign('MESSAGE', GM_CALLBACK_SERVICE_MESSAGE);
$smarty_gm_callback_service->assign('VALIDATION_ACTIVE', gm_get_conf('GM_CALLBACK_SERVICE_VVCODE'));
$smarty_gm_callback_service->assign('VALIDATION', GM_CALLBACK_SERVICE_VALIDATION);
$smarty_gm_callback_service->assign('GM_CAPTCHA', $coo_captcha->get_html());
$smarty_gm_callback_service->assign('SID', xtc_session_id());
$smarty_gm_callback_service->assign('SEND', 'templates/' . CURRENT_TEMPLATE . '/buttons/' . $_SESSION['language'] . '/button_continue.gif');

/* BOF GM PRIVACY LINK */	
$smarty_gm_callback_service->assign('GM_PRIVACY_LINK', gm_get_privacy_link('GM_CHECK_PRIVACY_CALLBACK')); 
/* EOF GM PRIVACY LINK */

$smarty_gm_callback_service->display(CURRENT_TEMPLATE.'/module/gm_callback_service.html');
?>