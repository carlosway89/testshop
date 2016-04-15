<?php
/* --------------------------------------------------------------
	ShipcloudController.inc.php 2015-12-28
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
* Class ShipcloudController
* @package HttpViewControllers
*/
class ShipcloudController extends AdminHttpViewController
{
	/**
	 * @var GXCoreLoaderSettingsInterface
	 */
	private $settings;

	/**
	 * @var GXCoreLoaderInterface
	 */
	private $loader;

	private $db;

	protected $shipcloudText;
	protected $shipcloudConfigurationStorage;
	protected $shipcloudLogger;

	public function __construct(HttpContextReaderInterface $httpContextReader,
	                            HttpResponseProcessorInterface $httpResponseProcessor,
	                            ContentViewInterface $contentView)
	{
		parent::__construct($httpContextReader, $httpResponseProcessor, $contentView);
		$this->shipcloudText = MainFactory::create('ShipcloudText');
		$this->shipcloudConfigurationStorage = MainFactory::create('ShipcloudConfigurationStorage');
		$this->shipcloudLogger = MainFactory::create('ShipcloudLogger');
	}

	/**
	 * determines if Shipcloud is configured and ready to use
	 */
	protected function isConfigured()
	{
		$mode = $this->shipcloudConfigurationStorage->get('mode');
		$apiKey = $this->shipcloudConfigurationStorage->get('api-key/'.$mode);
		$isConfigured = !empty($apiKey);
		return $isConfigured;
	}

	/**
	 * Override "proceed" method of parent and use it for initialization.
	 *
	 * This method must call the parent "proceed" in order to work properly.
	 *
	 * @param HttpContextInterface $httpContext
	 */
	public function proceed(HttpContextInterface $httpContext)
	{
		$this->settings = MainFactory::create('GXCoreLoaderSettings');
		$this->loader   = MainFactory::create('GXCoreLoader', $this->settings);
		$this->db = $this->loader->getDatabaseQueryBuilder();
		// Set the template directory.
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/');
		// Call the parent "proceed" method.
		parent::proceed($httpContext);
	}

	/**
	 * Run the actionDefault method.
	 */
	public function actionDefault()
	{
		# return new HttpControllerResponse('not implemented');
		return new RedirectHttpControllerResponse(GM_HTTP_SERVER.DIR_WS_CATALOG);
	}

	protected function splitStreet($street_address)
	{
		$street_address = trim($street_address);
		$splitStreet = array(
			'street' => $street_address,
			'house_no' => '',
		);
		$matches = array();
		if(preg_match('_(\d.*?)\s(.+)_', $street_address, $matches) === 1)
		{
			$splitStreet['street'] = $matches[2];
			$splitStreet['house_no'] = $matches[1];
		}
		else if(preg_match('_(.+?)\s?(\d.*)_', $street_address, $matches) === 1)
		{
			$splitStreet['street'] = $matches[1];
			$splitStreet['house_no'] = $matches[2];
		}

		return $splitStreet;
	}

	/**
	 * retrieves an order's total
	 *
	 * @todo get this data from OrderService
	 * @param int $orders_id the order's id
	 * @return double
	 */
	protected function getDeclaredValue($orders_id)
	{
		$declared_value = 0;
		$loader = MainFactory::create('GXCoreLoader', MainFactory::create('GXCoreLoaderSettings'));
		$db = $loader->getDatabaseQueryBuilder();
		$db->select('*')
		   ->from('orders_total')
		   ->where(array('orders_id' => $orders_id, 'class' => 'ot_total'));
		foreach($db->get()->result() as $row)
		{
			$declared_value = (double)$row->value;
		}
		return $declared_value;
	}

	public function actionCreateLabelForm()
	{
		require_once DIR_FS_ADMIN .'includes/classes/order.php';
		$orders_id = (int)$this->_getQueryParameter('orders_id');
		$order = new order($orders_id);
		$splitStreet = $this->splitStreet($order->delivery['street_address']);
		$declared_value = $this->getDeclaredValue((int)$orders_id);
		$cod_value = $declared_value;
		if($declared_value < (double)$this->shipcloudConfigurationStorage->get('declared_value/minimum'))
		{
			$declared_value = 0;
		}
		$default_package = $this->shipcloudConfigurationStorage->get('default_package');
		$default_package_data = $this->shipcloudConfigurationStorage->get_all_tree('packages/'.$default_package.'/');
		$default_package_dimensions = $default_package_data['packages'][$default_package];
		$formdata = array(
			'isConfigured' => $this->isConfigured() == true ? '1' : '0',
			'orders_id' => $orders_id,
			'is_cod' => $order->info['payment_method'] == 'cod',
			'cod' => array(
				'amount' => number_format($cod_value, 2, '.', ''),
				'currency' => $order->info['currency'],
			),
			'to' => array(
				'company' => $order->delivery['company'],
				'first_name' => $order->delivery['firstname'],
				'last_name' => $order->delivery['lastname'],
				'street' => $splitStreet['street'],
				'street_no' => $splitStreet['house_no'],
				'city' => $order->delivery['city'],
				'zip_code' => $order->delivery['postcode'],
				'country' => $order->delivery['country_iso_code_2'],
			),
			'package' => array(
				'weight' => $default_package_dimensions['weight'],
				'width' => $default_package_dimensions['width'],
				'length' => $default_package_dimensions['length'],
				'height' => $default_package_dimensions['height'],
				'declared_value' => array(
					'amount' => number_format($declared_value, 2, '.', ''),
					'currency' => $order->info['currency'],
				),
			),
			'package_templates' => $this->shipcloudConfigurationStorage->get_all_tree('packages'),
			'carriers' => $this->shipcloudConfigurationStorage->getCarriers(),
			'preselected_carriers' => $this->shipcloudConfigurationStorage->get_all_tree('preselected_carriers'),
			'checked_carriers' => $this->shipcloudConfigurationStorage->get_all_tree('checked_carriers'),
			'default_package_template' => $default_package,
			'carrier' => 'dhl',
			'service' => 'standard',
			'notification_email' => $order->customer['email_address'],
		);
		$html = $this->_render('shipcloud_form_single.html', $formdata);
		$html = $this->shipcloudText->replaceLanguagePlaceholders($html);
		return new HttpControllerResponse($html);
	}

	protected function _prepareSingleFormDataForShipmentRequest(array $postDataArray, $anon_from = false, $language_code = null)
	{
		$language_code = $language_code ?: $_SESSION['language_code'];
		unset($postDataArray['package_template']);
		if(empty($postDataArray['from']))
		{
			$postDataArray['from'] = array(
				'street' => $this->shipcloudConfigurationStorage->get('from/street'),
				'street_no' => $this->shipcloudConfigurationStorage->get('from/street_no'),
				'city' => $this->shipcloudConfigurationStorage->get('from/city'),
				'zip_code' => $this->shipcloudConfigurationStorage->get('from/zip_code'),
				'country' => $this->shipcloudConfigurationStorage->get('from/country'),
			);
			if($anon_from === false)
			{
				$postDataArray['from']['company'] = $this->shipcloudConfigurationStorage->get('from/company');
				$postDataArray['from']['first_name'] = $this->shipcloudConfigurationStorage->get('from/first_name');
				$postDataArray['from']['last_name'] = $this->shipcloudConfigurationStorage->get('from/last_name');
				$postDataArray['from']['phone'] = $this->shipcloudConfigurationStorage->get('from/phone');
			}
		}
		if((double)$postDataArray['package']['declared_value']['amount'] == 0)
		{
			unset($postDataArray['package']['declared_value']);
		}
		if($postDataArray['carrier'] == 'dhl' && !empty($postDataArray['cod']))
		{
			$codData = $postDataArray['cod'];
			unset($postDataArray['cod']);
			$postDataArray['additional_services'] = is_array($postDataArray['additional_services']) ? $postDataArray['additional_services'] : array();
			$postDataArray['additional_services'][] = array(
				'name' => 'cash_on_delivery',
				'properties' => array(
					'amount' => $codData['amount'],
					'currency' => $codData['currency'],
					'bank_account_holder' => $this->shipcloudConfigurationStorage->get('cod-account/bank_account_holder'),
					'bank_name' => $this->shipcloudConfigurationStorage->get('cod-account/bank_name'),
					'bank_account_number' => $this->shipcloudConfigurationStorage->get('cod-account/bank_account_number'),
					'bank_code' => $this->shipcloudConfigurationStorage->get('cod-account/bank_code'),
				),
			);
		}
		if($postDataArray['carrier'] == 'dpd')
		{
			if($this->shipcloudConfigurationStorage->get('additional_services/dpd-predict') == true)
			{
				$postDataArray['additional_services'] = is_array($postDataArray['additional_services']) ? $postDataArray['additional_services'] : array();
				$postDataArray['additional_services'][] = array(
					'name' => 'advance_notice',
					'properties' => array(
						'email' => $postDataArray['notification_email'],
						'language' => $language_code,
					),
				);
			}
		}
		$postDataArray['to'] = $this->_enforceLengthLimits($postDataArray['carrier'], $postDataArray['to']);
		$shipmentData = MainFactory::create('KeyValueCollection', $postDataArray);
		return $shipmentData;
	}

	protected function getOrderLanguageCode($orders_id)
	{
		$language_code = 'de';
		$this->db->select('code');
		$this->db->from('languages');
		$this->db->join('orders', 'orders_id = '.(int)$orders_id.' AND orders.language = languages.directory');
		$query = $this->db->get();
		foreach($query->result() as $row)
		{
			$language_code = $row->code;
		}
		return $language_code;
	}

	public function actionCreateLabelFormSubmit()
	{
		$postDataArray = $this->_getPostDataCollection()->getArray();
		$orders_id = (int)$postDataArray['orders_id'];
		unset($postDataArray['orders_id']);
		$this->shipcloudLogger->notice(__FUNCTION__."\n".print_r($postDataArray, true));
		try
		{
			if($this->isConfigured() === true)
			{
				$shipmentFactory = MainFactory::create('ShipcloudShipmentFactory');
				$shipmentData = $this->_prepareSingleFormDataForShipmentRequest($postDataArray, false, $this->getOrderLanguageCode($orders_id));
				$shipmentId = $shipmentFactory->createShipment($orders_id, $shipmentData);
				$contentArray = array(
					'orders_id' => $orders_id,
					'result' => 'OK',
					'shipment_id' => $shipmentId,
				);
			}
			else
			{
				$contentArray = array(
					'orders_id' => $orders_id,
					'result' => 'UNCONFIGURED',
					'shipment_id' => 'n/a',
				);
			}
		}
		catch(Exception $e)
		{
			$contentArray = array(
				'orders_id' => $orders_id,
				'result' => 'ERROR',
				'error_message' => $e->getMessage()
			);
		}
		return new JsonHttpControllerResponse($contentArray);
	}

	public function actionGetShipmentQuote()
	{
		$postDataArray = $this->_getPostDataCollection()->getArray();
		$orders_id = (int)$postDataArray['orders_id'];
		unset($postDataArray['orders_id']);
		unset($postDataArray['to']['company']);
		unset($postDataArray['to']['first_name']);
		unset($postDataArray['to']['last_name']);
		unset($postDataArray['notification_email']);
		unset($postDataArray['package']['declared_value']);
		unset($postDataArray['quote_carriers']);
		unset($postDataArray['cod']);

		try
		{
			if($this->isConfigured() === true)
			{
				$shipmentFactory = MainFactory::create('ShipcloudShipmentFactory');
				$shipmentData = $this->_prepareSingleFormDataForShipmentRequest($postDataArray, true);
				$shipmentQuote = $shipmentFactory->getShipmentQuote($shipmentData);
				$contentArray = array(
					'orders_id' => $orders_id,
					'result' => 'OK',
					'shipment_quote' => $shipmentQuote,
				);
			}
			else
			{
				$contentArray = array(
					'orders_id' => $orders_id,
					'result' => 'UNCONFIGURED',
					'shipment_quote' => '',
				);
			}
		}
		catch(Exception $e)
		{
			$contentArray = array(
				'orders_id' => $orders_id,
				'result' => 'ERROR',
				'error_message' => $e->getMessage()
			);
		}
		return new JsonHttpControllerResponse($contentArray);
	}

	public function actionGetMultiShipmentQuote()
	{
		require_once DIR_FS_ADMIN.'includes/classes/order.php';
		$postDataArray = $this->_getPostDataCollection()->getArray();
		$orders_ids = $postDataArray['orders'];
		$contentArray = array(
			'result' => 'OK',
			'shipment_quotes' => array(),
			'quote_total' => 0,
			'carriers_total' => array(),
		);
		foreach($orders_ids as $orders_id)
		{
			$contentArray['shipment_quotes'][$orders_id] = array(
				'orders_id' => $orders_id,
				'shipment_quote' => '',
			);
			$order = new order($orders_id);
			$splitStreet = $this->splitStreet($order->delivery['street_address']);
			foreach($postDataArray['quote_carriers'] as $carrier)
			{
				if(!isset($contentArray['carriers_total'][$carrier]))
				{
					$contentArray['carriers_total'][$carrier] = 0;
				}
				$getShipmentQuoteParams = array(
					'to' => array(
						'street' => $splitStreet['street'],
						'street_no' => $splitStreet['house_no'],
						'city' => $order->delivery['city'],
						'zip_code' => $order->delivery['postcode'],
						'country' => $order->delivery['country_iso_code_2'],
					),
					'package' => $postDataArray['package'],
					'carrier' => $carrier,
					'service' => $postDataArray['service'],
					'from' => array(
						'street' => $this->shipcloudConfigurationStorage->get('from/street'),
						'street_no' => $this->shipcloudConfigurationStorage->get('from/street_no'),
						'city' => $this->shipcloudConfigurationStorage->get('from/city'),
						'zip_code' => $this->shipcloudConfigurationStorage->get('from/zip_code'),
						'country' => $this->shipcloudConfigurationStorage->get('from/country'),
					)
				);
				$getShipmentQuoteParams['to'] = $this->_enforceLengthLimits($getShipmentQuoteParams['carrier'], $getShipmentQuoteParams['to']);
				try
				{
					$shipmentFactory = MainFactory::create('ShipcloudShipmentFactory');
					$shipmentQuote = $shipmentFactory->getShipmentQuote(MainFactory::create('KeyValueCollection', $getShipmentQuoteParams));
					$contentArray['carriers_total'][$carrier] += (double)str_replace(',', '.', $shipmentQuote);
					$shipment_quote = '<span>'.$this->shipcloudText->get_text('carrier_'.$carrier).' '.$shipmentQuote.'</span><br>';
				}
				catch(Exception $e)
				{
					$shipment_quote = '<span title="'.$e->getMessage().'">'.$this->shipcloudText->get_text('carrier_'.$carrier).' --</span><br>';
				}

				$contentArray['shipment_quotes'][$orders_id]['shipment_quote'] .= $shipment_quote;
			}
		}
		foreach($contentArray['carriers_total'] as $carrier => $total)
		{
			$contentArray['carriers_total'][$carrier] = sprintf('%.2f EUR', $total);
		}
		return new JsonHttpControllerResponse($contentArray);
	}

	public function actionUnconfiguredNote()
	{
		$templateData = array(
			'sc_link' => $this->shipcloudConfigurationStorage->get('boarding_url'),
			'config_link' => xtc_href_link('admin.php', 'do=ShipcloudModuleCenterModule'),
		);
		$html = $this->_render('shipcloud_unconfigurednote.html', $templateData);
		$html = $this->shipcloudText->replaceLanguagePlaceholders($html);
		return new HttpControllerResponse($html);
	}

	public function actionLoadLabelList()
	{
		$orders_id = (int)$this->_getQueryParameter('orders_id');
		try
		{
			$shipmentFactory = MainFactory::create('ShipcloudShipmentFactory');
			$shipments = $shipmentFactory->findShipments($orders_id);
			$templateData = array(
				'orders_id' => $orders_id,
				'shipments' => $shipments->shipments,
			);
			$html = $this->_render('shipcloud_labellist.html', $templateData);
			$html = $this->shipcloudText->replaceLanguagePlaceholders($html);
			//$html .= sprintf("\n<pre>\n%s\n</pre>\n", print_r($shipments->shipments, true));
		}
		catch(Exception $e)
		{
			$html .= '<p>ERROR: '.$e->getMessage().'</p>';
		}
		return new HttpControllerResponse($html);
	}

	public function actionLoadMultiLabelList()
	{
		$postData = $this->_getPostDataCollection()->getArray();
		$orders_ids = $postData['orders_ids'];
		$shipmentFactory = MainFactory::create('ShipcloudShipmentFactory');
		$shipments = array();
		foreach($orders_ids as $orders_id)
		{
			try
			{
				$shipment = $shipmentFactory->findShipments($orders_id);
				$shipments[$orders_id] = $shipment;
			}
			catch(Exception $e)
			{
				$this->shipcloudLogger->debug_notice(sprintf('no shipment found for orders_id %s: %s', $orders_id, $e->getMessage()));
			}
		}
		$templateData = array(
			'shipments' => $shipments,
		);
		$html = $this->_render('shipcloud_multilabellist.html', $templateData);
		$html = $this->shipcloudText->replaceLanguagePlaceholders($html);

		return new HttpControllerResponse($html);
	}

	public function actionCreateMultiLabelForm()
	{
		if($this->isConfigured() !== true)
		{
			return $this->actionUnconfiguredNote();
		}
		else
		{
			require DIR_FS_ADMIN .'includes/classes/order.php';
			$orders_ids = $this->_getQueryParameter('orders');
			$orders = array();
			foreach($orders_ids as $orders_id)
			{
				$orders[$orders_id] = new order($orders_id);
			}
			$default_package = $this->shipcloudConfigurationStorage->get('default_package');
			$default_package_data = $this->shipcloudConfigurationStorage->get_all_tree('packages/'.$default_package.'/');
			$default_package_dimensions = $default_package_data['packages'][$default_package];
			$templateData = array(
				'orders' => $orders,
				'package' => array(
					'weight' => $default_package_dimensions['weight'],
					'width' => $default_package_dimensions['width'],
					'length' => $default_package_dimensions['length'],
					'height' => $default_package_dimensions['height'],
				),
				'package_templates' => $this->shipcloudConfigurationStorage->get_all_tree('packages'),
				'default_package_template' => $default_package,
				'carriers' => $this->shipcloudConfigurationStorage->getCarriers(),
				'preselected_carriers' => $this->shipcloudConfigurationStorage->get_all_tree('preselected_carriers'),
				'checked_carriers' => $this->shipcloudConfigurationStorage->get_all_tree('checked_carriers'),
			);
			$html = $this->_render('shipcloud_form_multi.html', $templateData);
			$html = $this->shipcloudText->replaceLanguagePlaceholders($html);
			return new HttpControllerResponse($html);
		}
	}

	public function actionCreateMultiLabelFormSubmit()
	{
		require DIR_FS_ADMIN .'includes/classes/order.php';
		$postDataArray = $this->_getPostDataCollection()->getArray();
		$orders_ids = $postDataArray['orders'];
		unset($postDataArray['orders']);
		$orders = array();
		foreach($orders_ids as $orders_id)
		{
			$orders[$orders_id] = new order($orders_id);
		}
		$this->shipcloudLogger->notice(__FUNCTION__."\n".print_r($postDataArray, true));

		$contentArray = array(
			'orders_ids' => $orders_ids,
			'result' => 'UNDEFINED',
		);

		$shipmentFactory = MainFactory::create('ShipcloudShipmentFactory');
		foreach($orders as $orders_id => $order)
		{
			$this->shipcloudLogger->notice(sprintf('creating label for order %s', $orders_id));
			try
			{
				$splitStreet = $this->splitStreet($order->delivery['street_address']);
				$singlePostDataArray = array_merge($postDataArray);
			    $singlePostDataArray['to'] = array(
					'company' => $order->delivery['company'],
					'first_name' => $order->delivery['firstname'],
					'last_name' => $order->delivery['lastname'],
					'street' => $splitStreet['street'],
					'street_no' => $splitStreet['house_no'],
					'city' => $order->delivery['city'],
					'zip_code' => $order->delivery['postcode'],
					'country' => $order->delivery['country_iso_code_2'],
			    );
			    $singlePostDataArray['to'] = $this->_enforceLengthLimits($postDataArray['carrier'], $singlePostDataArray['to']);
			    $singlePostDataArray['notification_email'] = $order->customer['email_address'];
				$shipmentData = $this->_prepareSingleFormDataForShipmentRequest($singlePostDataArray);
				$shipmentId = $shipmentFactory->createShipment($orders_id, $shipmentData);
				$contentArray['shipments'][] = array(
					'orders_id' => $orders_id,
					'shipment_id' => $shipmentId,
					'result' => 'OK',
				);
			}
			catch(Exception $e)
			{
				$contentArray['shipments'][] = array(
					'orders_id' => $orders_id,
					'error_message' => $e->getMessage(),
					'result' => 'ERROR',
				);
			}
		}
		$contentArray['result'] = 'OK';

		return new JsonHttpControllerResponse($contentArray);
	}

	protected function _enforceLengthLimits($carrier, $toArray)
	{
		$lengthLimits = array(
			'dhl' => array(
				'company' => array('min' => 2, 'max' => 30, 'empty_allowed' => true),
				'last_name' => array('min' => 1, 'max' => 30, 'empty_allowed' => false),
				'street' => array('min' => 1, 'max' => 40, 'empty_allowed' => false),
				'street_no' => array('min' => 1, 'max' => 5, 'empty_allowed' => false),
				'zip_code' => array('min' => 5, 'max' => 5, 'empty_allowed' => false),
				'city' => array('min' => 1, 'max' => 50, 'empty_allowed' => false),
			),
			'dpd' => array(
				'company' => array('min' => 1, 'max' => 35, 'empty_allowed' => true),
				'street' => array('min' => 1, 'max' => 35, 'empty_allowed' => false),
				'street_no' => array('min' => 0, 'max' => 8, 'empty_allowed' => false),
				'zip_code' => array('min' => 1, 'max' => 9, 'empty_allowed' => false),
				'city' => array('min' => 1, 'max' => 35, 'empty_allowed' => false),
			),
			'ups' => array(
				'company' => array('min' => 1, 'max' => 200, 'empty_allowed' => true),
				'last_name' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street_no' => array('min' => 1, 'max' => 10, 'empty_allowed' => false),
				'zip_code' => array('min' => 0, 'max' => 12, 'empty_allowed' => false),
				'city' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
			),
			'hermes' => array(
				'company' => array('min' => 1, 'max' => 200, 'empty_allowed' => true),
				'last_name' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street_no' => array('min' => 1, 'max' => 10, 'empty_allowed' => false),
				'zip_code' => array('min' => 0, 'max' => 12, 'empty_allowed' => false),
				'city' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
			),
			'gls' => array(
				'company' => array('min' => 1, 'max' => 200, 'empty_allowed' => true),
				'last_name' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street_no' => array('min' => 1, 'max' => 10, 'empty_allowed' => false),
				'zip_code' => array('min' => 0, 'max' => 12, 'empty_allowed' => false),
				'city' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
			),
			'fedex' => array(
				'company' => array('min' => 1, 'max' => 200, 'empty_allowed' => true),
				'last_name' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street_no' => array('min' => 1, 'max' => 10, 'empty_allowed' => false),
				'zip_code' => array('min' => 0, 'max' => 12, 'empty_allowed' => false),
				'city' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
			),
			'liefery' => array(
				'company' => array('min' => 1, 'max' => 200, 'empty_allowed' => true),
				'last_name' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street_no' => array('min' => 1, 'max' => 10, 'empty_allowed' => false),
				'zip_code' => array('min' => 0, 'max' => 12, 'empty_allowed' => false),
				'city' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
			),
		);
		$lengthLimitsName = array(
			'dhl' => 30,
			'ups' => 35,
		);
		$padding = '-';

		if(!in_array($carrier, array_keys($lengthLimits)))
		{
			throw new Exception('invalid carrier '.$carrier.' in '.__CLASS__.'::'.__METHOD__);
		}

		foreach($toArray as $key => $value)
		{
			if(!in_array($key, array_keys($lengthLimits[$carrier])))
			{
				// throw new Exception('invalid field '.$key.' in '.__CLASS__.'::'.__METHOD__);
				continue;
			}

			$valueLen = mb_strlen($value);
			if($valueLen < $lengthLimits[$carrier][$key]['min'])
			{
				$toArray[$key] = $value . str_repeat($padding, $lengthLimits[$carrier][$key]['min'] - $valueLen);
			}
			$toArray[$key] = mb_substr($value, 0, $lengthLimits[$carrier][$key]['max']);

		}

		if(in_array($carrier, array_keys($lengthLimitsName)))
		{
			$nameLength = mb_strlen($toArray['last_name'].$toArray['first_name']);
			if($nameLength > $lengthLimitsName[$carrier])
			{
				$toArray['first_name'] = mb_substr($toArray['first_name'], 0, 1).'.';
			}
			$nameLength = mb_strlen($toArray['last_name'].$toArray['first_name']);
			if($nameLength > $lengthLimitsName[$carrier])
			{
				$toArray['last_name'] = mb_substr($toArray['last_name'], 0, $lengthLimitsName[$carrier] - 3);
			}
		}

		return $toArray;
	}


}
