<?php
/* --------------------------------------------------------------
	ShipcloudShipmentFactory.inc.php 2015-10-16
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class ShipcloudShipmentFactory
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * @var GXCoreLoaderSettingsInterface
	 */
	private $settings;

	/**
	 * @var GXCoreLoaderInterface
	 */
	private $loader;

	protected $shipcloudText;
	protected $shipcloudConfigurationStorage;
	protected $shipcloudLogger;

	public function __construct()
	{
		$this->shipcloudText = MainFactory::create('ShipcloudText');
		$this->shipcloudConfigurationStorage = MainFactory::create('ShipcloudConfigurationStorage');
		$this->shipcloudLogger = MainFactory::create('ShipcloudLogger');
		$this->settings = MainFactory::create('GXCoreLoaderSettings');
		$this->loader = MainFactory::create('GXCoreLoader', $this->settings);
		$this->db = $this->loader->getDatabaseQueryBuilder();
	}

	public function getShipmentQuote(KeyValueCollection $postData)
	{
		$makeShipmentRequest = MainFactory::create('ShipcloudRestRequest', 'POST', '/v1/shipment_quotes');
		$makeShipmentData = $postData->getArray();
		$makeShipmentRequest->setData($makeShipmentData);

		$restService = MainFactory::create('ShipcloudRestService');
		$result = $restService->performRequest($makeShipmentRequest);
		$responseObject = $result->getResponseObject();
		if($result->getResponseCode() != '200')
		{
			if(is_array($responseObject->errors))
			{
				$errorMessage = implode('; ', $responseObject->errors);
			}
			else
			{
				$errorMessage = 'unspecified error';
			}
			throw new Exception($errorMessage);
		}
		$price = number_format((double)$responseObject->shipment_quote->price, 2, ',', '');
		$price .= ' EUR';
		return $price;
	}

	public function createShipment($orders_id, KeyValueCollection $postData)
	{
		$makeShipmentRequest = MainFactory::create('ShipcloudRestRequest', 'POST', '/v1/shipments');
		$makeShipmentData = $postData->getArray();
		$makeShipmentData['create_shipping_label'] = true;
		$makeShipmentData['reference_number'] = $orders_id;
		$makeShipmentRequest->setData($makeShipmentData);

		$restService = MainFactory::create('ShipcloudRestService');
		$result = $restService->performRequest($makeShipmentRequest);
		$responseObject = $result->getResponseObject();
		if($result->getResponseCode() != '200')
		{
			if(is_array($responseObject->errors))
			{
				$errorMessage = implode('; ', $responseObject->errors);
			}
			else
			{
				$errorMessage = 'unspecified error';
			}
			throw new Exception($errorMessage);
		}

		$parcelServiceId = $this->shipcloudConfigurationStorage->get('parcel_service_id');
		if($parcelServiceId > 0)
		{
			$parcelServiceReader = MainFactory::create('ParcelServiceReader');
			$parcelTrackingCodeWriter = MainFactory::create('ParcelTrackingCodeWriter');
			$parcelTrackingCodeWriter->insertTrackingUrl($orders_id, (string)$responseObject->tracking_url, $parcelServiceId, $parcelServiceReader);
		}

		$order_status_after_label = $this->shipcloudConfigurationStorage->get('order_status_after_label');
		if($order_status_after_label >= 0)
		{
			$orderStatusComment = sprintf("%s\nID %s", $this->shipcloudText->get_text('shipcloud_label_created'), $responseObject->id);
			$this->setOrderStatus($orders_id, $order_status_after_label, $orderStatusComment);
		}

		return $responseObject->id;
	}

	protected function setOrderStatus($orders_id, $order_status_id, $order_status_comment = '')
	{
		$this->db->where('orders_id', $orders_id);
		$this->db->update('orders', array('orders_status' => $order_status_id));

		$orders_status_history_entry = array(
			'orders_id' => $orders_id,
			'orders_status_id' => $order_status_id,
			'date_added' => date('Y-m-d H:i:s'),
			'customer_notified' => '0',
			'comments' => $order_status_comment,
		);
		$this->db->insert('orders_status_history', $orders_status_history_entry);
	}

	public function findShipments($orders_id)
	{
		$shipmentsRequest = MainFactory::create('ShipcloudRestRequest', 'GET', '/v1/shipments?reference_number='.(int)$orders_id);
		$restService = MainFactory::create('ShipcloudRestService');
		$result = $restService->performRequest($shipmentsRequest);
		$shipments = $result->getResponseObject();
		return $shipments;
	}

}
