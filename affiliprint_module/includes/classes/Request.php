<?php
/**
 * Verwaltet die Anfragen und leitet diese in entsprechenden Methoden weiter
 *
 * Verwaltet die Anfragen in entsprechenden "Action"-Methoden, wird keine oder
 * eine nicht vorhandene "Action" angegeben wird die Methode "defaultAction"
 * aufgerufen. in der Methode "handle" werden Anfragen zentral weitergeleitet
 * bzw. die Actions aufgerufen. Die Rückgabe erfolgt bei AJAX-Requests im
 * JSON-Format, nur beim ersten Aufruf des Moduls wird das Template einmal
 * ausgegeben - dies erfolgt in der Methode "_defaultAction" bzw.
 * "_includeTemplateFile".
 *
 * @package AffiliPRINT
 * @author Patrick Taddey <p.taddey@affiliprint.de>
 * @version 1.1
 * @copyright Copyright (c) 2014 AffiliPRINT GmbH (http://www.affiliprint.de/)
 * @license Released under the GNU General Public License (Version 2) [http://www.gnu.org/licenses/gpl-2.0.html]
 */
class Request {
	/**
	 * Array mit den Request-Daten
	 *
	 * @var Array
	 */
	var $_requestArray;

	/**
	 * Array mit der Antwort
	 *
	 * @var Array
	 */
	var $_responseArray = array('success' => true);

	/**
	 * Array mit der CSS-Modul-Status-Klassen
	 *
	 * @var Array
	 */
	var $_moduleStatusCssClasses = array(
		0 => 'error',
		1 => 'success',
		2 => 'warning',
	);

	/**
	 * Objekt mit Konfigurationseinstellungen
	 *
	 * @var Object
	 */
	var $_moduleConfig = null;

	/**
	 * Instanz des Communicators
	 *
	 * @var object
	 */
	var $_communicator = null;

	/**
	 * Name des Schlüssels für die Konfiguration in der Datenbank/Tabelle
	 *
	 * @var String
	 */
	var $_confKeyName = 'AFFILIPRINT_CONFIGURATION_DATA';

	/**
	 * Konstruktor
	 *
	 * @return void
	 */
	function Request()
	{ 
		return;
	}

	/**
	 * $_REQUEST-Array setzen
	 *
	 * @param array $request
	 * @return void
	 */
	function setRequestArray($request)
	{
		$this->_requestArray = $request;
		return;
	}

	/**
	 * Konfigurationseinstellungen beziehen
	 *
	 * Sofern noch nicht in der Datenbank hinterlegt, werden diese hier entsprechend
	 * initial abgespeichert und einmal pro Sitzung über eine Schnittstelle mit dem
	 * System der AffiliPRINT GmbH abgeglichen
	 *
	 * @param array $configurationData
	 * @return void
	 */
	function setConfiguration($configurationData)
	{
		/* Konfigurationseinstellungen laden */
		$moduleConfig = $this->_loadConfiguration();

		/* überprüfen, ob Konfigurationseinstellungen schon abgespeichert worden sind und
		 * das Modul bereits installiert worden ist */
		if ((bool)$moduleConfig === false || AFFILIPRINT_DEBUG_MODE === true) {
			/* Konfigurationseinstellungen initial abspeichern */
			$this->_saveConfiguration($configurationData);

			/* Konfigurationseinstellungen erneut laden */
			$moduleConfig = $this->_loadConfiguration();
		}

		/* Konfigurationseinstellungen setzen */
		$this->_moduleConfig = $moduleConfig;

		/* Konfigurationseinstellungen über Schnittstelle aktualisieren, sofern Modul bereits authentifiziert */
		if ($this->_moduleConfig->authenticationStatus == 1) {
			$this->_updateAction();
		}
		return;
	}

	/**
	 * Instanz vom 'Communicator' erstellen, um mit AffiliPRINT zu kommunizieren
	 *
	 * @return void
	 */
	function _setCommunicator()
	{
		/* Instanz vom 'Communicator' erstellen, um mit AffiliPRINT zu kommunizieren */
		$this->_communicator = new Communicator();

		/* Konfigurationseinstellungen setzen */
		$this->_communicator->setModuleConfig($this->_moduleConfig);

		/* Timeout für die Herstellung der Verbindung setzen */
		$this->_communicator->setConnectTimeout(AFFILIPRINT_API_CONNECT_TIMEOUT);

		/* Timeout für die Übertragung setzen */
		$this->_communicator->setTimeout(AFFILIPRINT_API_TIMEOUT);

		/* Absender der Anfrage setzen */
		$this->_communicator->setRequestReferer(SHOP_URL);

		/* Standard-Übergabe-Parameter setzen, um Anfragen starten zu können */
		$this->_communicator->prepareRequest();

		return;
	}

	/**
	 * Verwaltet die Abfragen
	 *
	 * @return void
	 */
	function handle()
	{
		/* Namen der Abfrage-Methode zusammensetzen */
		$requestAction = '_' . $this->_requestArray['action'] . "Action";

		/* Sofern die Abfrage-Methode nicht existiert, wird in jedem Fall die
		 * Default-Methode aufgerufen */
		if (method_exists($this, $requestAction) === false) {
			$this->_defaultAction();
			/* Abfrage-Methode existiert und kann den Request verarbeiten */
		} else {
			$this->$requestAction();
		}
		return;
	}

	/**
	 * Standard-Methode in der das Template angezeigt wird
	 *
	 * @return void
	 */
	function _defaultAction()
	{
		/* Modul ist im Debug-Modus nicht aktiv */
		if (AFFILIPRINT_DEBUG_MODE === true) {
			define('AUTHENTICATION_STATUS', 0);
		} else {
			define('AUTHENTICATION_STATUS', $this->_moduleConfig->authenticationStatus);
		}
		/* Konstanten für Template setzen */
		define('VCP_URL', $this->_moduleConfig->vcpUrl);
		define('MODULE_STATUS', $this->_moduleConfig->moduleStatus);
		define('MODULE_STATUS_CSS_CLASS', $this->_moduleStatusCssClasses[MODULE_STATUS]);
		define('USE_ADDITIONAL_DATA', $this->_moduleConfig->useAdditionalData);
		define('IFRAME_URL', $this->_generateIframeUrl());
		define('IFRAME_INFO_URL', $this->_generateIframeUrl(true));

		define('API_USE_ADDITIONAL_DATA', $this->_moduleConfig->additionalUserData);
		define('HAS_CAMPAIGNS', $this->_moduleConfig->hasCampaigns);

		/* Template aufrufen */
		require_once (AFFILIPRINT_DIR . 'templates/admin.php');

		return;
	}

	/**
	 * Methode, um das Modul wieder zu resetten
	 *
	 * @return void
	 */
	function _resetAction()
	{
		/* Eintrag aus der Konfiguration wieder entfernen */
		mysql_query("DELETE FROM gm_configuration WHERE gm_key = '" . $this->_confKeyName . "'");
		/* Standard-Ansicht laden */
		header("location: " . $_SERVER['PHP_SELF'] . "");
		return;
	}

	/**
	 * Authentifizierungs- und Aktualisierungsmethode
	 *
	 * @return void
	 */
	function _updateAction()
	{
		/* Instanz vom 'Communicator' erstellen, um mit AffiliPRINT zu kommunizieren */
		$this->_setCommunicator();

		/* Authentifizierung abschicken */
		$response = $this->_communicator->authentication();
		//	die(print_r($response));
		if ((int)$response->success == 1) {

			/* Konfigurationseinstellungen überschreiben */
			$this->_moduleConfig->additionalUserData = $response->additionalUserData;
			$this->_moduleConfig->adgroupUid = $response->adgroups[0]->uid;
			$this->_moduleConfig->adsUrl = $response->adgroups[0]->adsUrl;
			$this->_moduleConfig->apiUrl = $response->apiUrl;
			$this->_moduleConfig->apiVersion = $response->apiVersion;
			$this->_moduleConfig->authenticationStatus = 1;
			$this->_moduleConfig->authenticationToken = $response->publisher_uid;
			$this->_moduleConfig->hasCampaigns = $response->adgroups[0]->hasCampaigns;
			$this->_moduleConfig->language = getLanguageIsoCode();
			$this->_moduleConfig->publisherUid = $response->publisher_uid;
			$this->_moduleConfig->shopSystem = SHOP_SYSTEM;
			$this->_moduleConfig->shopSystemVersion = SHOP_SYSTEM_VERSION;
			$this->_moduleConfig->shopUrl = SHOP_URL;
			$this->_moduleConfig->transactionIdWildcard = $response->adgroups[0]->transactionIdWildcard;
			$this->_moduleConfig->vcpUrl = $response->vcpUrl;

			/* Sofern Kampagnen vorhanden sind, kann das Modul aus dem "Warten"-Modus aktiviert werden
			 * und Gutscheine anzeigen */
			if ((int)$this->_moduleConfig->hasCampaigns == 1 && (int)$this->_moduleConfig->moduleStatus == 2) {
				$this->_moduleConfig->moduleStatus = 1;
			}

			/* Konfigurationseinstellungen abspeichern */
			$this->_saveConfiguration($this->_moduleConfig);
		}
		return $response;
	}

	/**
	 * Speichert die Rückgabe der Authentifizierung ab
	 *
	 * @return void
	 */
	function _authenticationAction()
	{
		$this->_moduleConfig->authenticationToken = $this->_requestArray['authenticationToken'];
		$response = $this->_updateAction();
		if ((int)$response->success == 1) {
				
			if ($this->_moduleConfig->moduleStatus == 1) {
				$this->_responseArray['status_message'] = MESSAGE_STATUS_CHANGED_ACTIVE;
				$this->_responseArray['cssClass'] = "success";
			} else {
				$this->_responseArray['status_message'] = MESSAGE_STATUS_NO_CAMPAIGNS;
				$this->_responseArray['cssClass'] = "warning";
			}
			
			$this->_responseArray['success'] = true;
			$this->_responseArray['module_status'] = $this->_moduleConfig->moduleStatus;
			$this->_responseArray['message'] = MESSAGE_AUTH_SUCCESSFUL;
		} else {
			$this->_responseArray['success'] = false;
			$this->_responseArray['message'] = MESSAGE_AUTH_FAILED;
		}

		$this->_jsonResponse();

		return;
	}

	/**
	 * Speichert den neuen Modulstatus ab
	 *
	 * @return void
	 */
	function _moduleStatusAction()
	{
		/* Modulstatus beziehen und abspeichern */
		$moduleStatus = $this->_requestArray['status'];
		$this->_moduleConfig->moduleStatus = $moduleStatus;
		/* Konfigurationseinstellungen abspeichern */
		$this->_saveConfiguration($this->_moduleConfig);

		if ((int)$moduleStatus == 0) {
			$this->_responseArray['message'] = MESSAGE_STATUS_CHANGED_INACTIVE;
		} else {
			$this->_responseArray['message'] = MESSAGE_STATUS_CHANGED_ACTIVE;
		}

		$this->_jsonResponse();
	}

	/**
	 * Zusätzliche Daten übertragen?
	 *
	 * @return void
	 */
	function _useAdditionalDataAction()
	{
		/* Status beziehen und abspeichern */
		$useAdditionalData = $this->_requestArray['useAdditionalData'];
		$this->_moduleConfig->useAdditionalData = $useAdditionalData;

		/* Konfigurationseinstellungen abspeichern */
		$this->_saveConfiguration($this->_moduleConfig);

		$this->_jsonResponse();
	}

	/**
	 * Helper: URL für die Iframe generieren
	 *
	 * @param boolean showInfo
	 * @return array
	 */
	function _generateIframeUrl($showInfo = false)
	{
		/* Bezieht die Registrierungsdaten vom Vertriebspartner bzw. angemeldeten
		 * Administrator, um diese im entsprechenden Formular auszugeben */
		$publisherRegistrationData = getPublisherRegistrationData();

		$iframeGetData = array(
			'showInfo' => (int)$showInfo,
			'userInfo' => array(
				'gender' => $publisherRegistrationData['gender'],
				'firstName' => $publisherRegistrationData['firstName'],
				'lastName' => $publisherRegistrationData['lastName'],
				'street' => $publisherRegistrationData['street'],
				'postcode' => $publisherRegistrationData['postcode'],
				'city' => $publisherRegistrationData['city'],
				'email' => $publisherRegistrationData['email'],
				'phone' => $publisherRegistrationData['phone'],
				'company' => $publisherRegistrationData['company'],
				'shopName' => STORE_NAME,
				'shopUrl' => SHOP_URL,
			),
			'systemInfo' => array(
				'adgroupUid' => $this->_moduleConfig->adgroupUid,
				'adsUrl' => $this->_moduleConfig->adsUrl,
				'apiUrl' => $this->_moduleConfig->apiUrl,
				'apiVersion' => $this->_moduleConfig->apiVersion,
				'authenticationStatus' => $this->_moduleConfig->authenticationStatus,
				'authenticationToken' => $this->_moduleConfig->authenticationToken,
				'language' => getLanguageIsoCode(),
				'iframeUrl' => $this->_moduleConfig->iframeUrl,
				'moduleStatus' => $this->_moduleConfig->moduleStatus,
				'publisherUid' => $this->_moduleConfig->publisherUid,
				'shopSystem' => SHOP_SYSTEM,
				'shopSystemVersion' => SHOP_SYSTEM_VERSION,
				'shopUrl' => SHOP_URL,
				'transactionIdWildcard' => $this->_moduleConfig->transactionIdWildcard,
				'vcpUrl' => $this->_moduleConfig->vcpUrl,
			)
		);
		/* URL kodieren */
		return $this->_moduleConfig->iframeUrl . base64_encode(jsonEncode($iframeGetData));
	}

	/**
	 * Helper: Aus- bzw. Rückgabe im JSON-Format
	 *
	 * @return void
	 */
	function _jsonResponse()
	{
		/* CSS-Klassenname für die Rückgabe definieren */
		if ($this->_responseArray['success'] === true) {
			$this->_responseArray['className'] = 'success';
		} else {
			$this->_responseArray['className'] = 'error';
		}
        header('Content-type: application/json');
		echo jsonEncode($this->_responseArray);
		return;
	}

	/**
	 * Helper: Konfigurationseinstellungen abspeichern
	 *
	 * @param mixed $moduleConfig
	 * @return mixed
	 */
	function _saveConfiguration($moduleConfig)
	{
		gm_set_conf($this->_confKeyName, jsonEncode($moduleConfig));

		return;
	}

	/**
	 * Helper: Konfigurationseinstellungen laden
	 *
	 * @return mixed
	 */
	function _loadConfiguration()
	{
		$moduleConfig = gm_get_conf($this->_confKeyName, 'ASSOC', true);

		if ((bool)$moduleConfig !== false) {
			return jsonDecode($moduleConfig);
		} else {
			return false;
		}
	}

}
?>