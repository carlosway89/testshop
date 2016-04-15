<?php
/* --------------------------------------------------------------
   CreateGuestController.inc.php 2015-06-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');

include_once DIR_FS_CATALOG . 'gm/inc/gm_get_privacy_link.inc.php';
include_once DIR_FS_CATALOG . 'gm/inc/gm_get_content_by_group_id.inc.php';
include_once DIR_FS_INC . 'xtc_create_password.inc.php';

// TODO
if(!isset($GLOBALS['breadcrumb']))
{
	$GLOBALS['breadcrumb'] = new breadcrumb();
}
include_once DIR_FS_CATALOG . 'includes/classes/boxes.php';
if(!isset($GLOBALS['messageStack']))
{
	$GLOBALS['messageStack'] = new messageStack();
}


/**
 * Class CreateGuestController
 */
class CreateGuestController extends HttpViewController
{
	/**
	 * @return HttpControllerResponse|RedirectHttpControllerResponse
	 */
	public function actionDefault()
	{
		if(isset($_SESSION['customer_id']))
		{
			return new RedirectHttpControllerResponse(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
		}

		return $this->_getResponse($this->_getMainContentHtml());
	}


	/**
	 * TODO use of MainFactory is only an interim solution
	 *
	 * @return HttpControllerResponse|RedirectHttpControllerResponse
	 */
	public function actionProceed()
	{
		if(isset($_SESSION['customer_id']))
		{
			return new RedirectHttpControllerResponse(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
		}

		$econda = null;
		if(isset($GLOBALS['econda']))
		{
			$econda = $GLOBALS['econda'];
		}

		/**
		 * @var CountryService $countryService
		 */
		$countryService = StaticGXCoreLoader::getService('Country');
		
		$inputTransformer     = MainFactory::create('CustomerInputToCollectionTransformer');
		$customerCollection   = $inputTransformer->getGuestCollectionFromInputArray($this->_getPostDataCollection()
		                                                                                 ->getArray(), $countryService);
		$createAccountProcess = MainFactory::create('CreateAccountProcess', StaticGXCoreLoader::getService('CustomerWrite'),
		                                            $countryService, $econda);
		try
		{
			$createAccountProcess->proceedGuest($customerCollection);

			$redirectUrl = FILENAME_SHOPPING_CART;

			if($this->_getQueryParameter('checkout_started') === '1')
			{
				$redirectUrl = FILENAME_CHECKOUT_SHIPPING;
			}

			return new RedirectHttpControllerResponse(xtc_href_link($redirectUrl, '', 'SSL'));
		}
		catch(InvalidCustomerDataException $e)
		{
			return $this->_getResponse($this->_getMainContentHtml($customerCollection, $e->getErrorMessageCollection(),
			                                                      true));
		}
	}


	/**
	 * @param string $p_mainContentHtml
	 *
	 * @return HttpControllerResponse|RedirectHttpControllerResponse
	 */
	protected function _getResponse($p_mainContentHtml)
	{
		$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_CREATE_ACCOUNT,
		                            xtc_href_link('shop.php', xtc_get_all_get_params(), 'SSL'));

		$layoutContentControl = MainFactory::create_object('LayoutContentControl');
		$layoutContentControl->set_data('GET', $this->_getQueryParametersCollection()->getArray());
		$layoutContentControl->set_data('POST', $this->_getPostDataCollection()->getArray());
		$layoutContentControl->set_('coo_breadcrumb', $GLOBALS['breadcrumb']);
		$layoutContentControl->set_('coo_product', $GLOBALS['product']);
		$layoutContentControl->set_('coo_xtc_price', $GLOBALS['xtPrice']);
		$layoutContentControl->set_('c_path', $GLOBALS['cPath']);
		$layoutContentControl->set_('main_content', $p_mainContentHtml);
		$layoutContentControl->set_('request_type', $GLOBALS['request_type']);
		$layoutContentControl->proceed();

		$redirectUrl = $layoutContentControl->get_redirect_url();
		if(!empty($redirectUrl))
		{
			return new RedirectHttpControllerResponse($redirectUrl);
		}

		return new HttpControllerResponse($layoutContentControl->get_response());
	}


	/**
	 * @param KeyValueCollection $customerCollection
	 * @param KeyValueCollection $errorMessageCollection
	 * @param bool               $p_process
	 *
	 * @return string
	 */
	protected function _getMainContentHtml(KeyValueCollection $customerCollection = null,
	                                       KeyValueCollection $errorMessageCollection = null,
	                                       $p_process = false)
	{
		$customerArray = array();
		if($customerCollection !== null)
		{
			$customerArray = $customerCollection->getArray();
		}

		$errorMessages = array();
		if($errorMessageCollection !== null)
		{
			$errorMessages = $errorMessageCollection->getArray();
		}

		$createAccountContentView = MainFactory::create_object('CreateAccountContentView');
		$createAccountContentView->set_content_template('module/create_account_guest.html');

		$createAccountContentView->set_('guest_account', true);
		$createAccountContentView->set_('customer_data_array', $customerArray);
		$createAccountContentView->set_('error_array', $errorMessages);
		$createAccountContentView->set_('process', $p_process);
		$createAccountContentView->set_('checkout_started', (int)$this->_getQueryParameter('checkout_started'));

		return $createAccountContentView->get_html();
	}
}