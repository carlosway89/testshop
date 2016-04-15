<?php
/* --------------------------------------------------------------
   DynamicShopMessages.inc.php 2015-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class DynamicShopMessages
 *
 * This controller fetches the dynamic shop messages from the shop portal through a CURL request.
 * 
 * @category System
 * @package  Controllers
 */
class DynamicShopMessagesController extends AdminHttpViewController
{
	public function actionDefault()
	{
		try
		{
			include DIR_FS_CATALOG . 'release_info.php'; 
			
			// Create data source URL.
			$params = array(
					'shop_version' => rawurlencode($gx_version),
			        'news_type' => 'DOM'
			);
			
			if (gm_get_conf('SHOP_KEY_VALID') === '1') 
			{
				$params['shop_url'] = rawurlencode(HTTP_SERVER . DIR_WS_CATALOG); 
				$params['shop_key'] = rawurlencode(GAMBIO_SHOP_KEY);
				$params['language'] = rawurlencode($_SESSION['language_code']);
			}

			$url = 'https://www.gambio-support.de/updateinfo/?' . implode('&', $params);
			
			$loadUrl = MainFactory::create('LoadUrl');
			
			$jsonString = $loadUrl->load_url($url, array('Accept: application/json'), '', false, false);
			
			$response = json_decode($jsonString, true);
		}
		catch(Exception $ex)
		{
			$response = AjaxException::response($ex);
		}
		
		if ($response === null) 
		{
			$response = array(
				'SOURCES' => array(), 
				'MESSAGES' => array()
			); // We must not pass a null value to the JsonHttpControllerResponse object.
		}

		return new JsonHttpControllerResponse($response);
	}
}