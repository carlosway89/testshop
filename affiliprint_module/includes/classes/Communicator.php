<?php
/**
 * Wickelt die Kommunikation mit der AffiliPRINT-Schnittstelle ab
 *
 * Stellt Anfragen - je nach Verfügbarkeit - mit "cURL" oder "fsockopen" an die
 * AffiliPRINT-Schnittstelle. Dies wird in der Methode "_communicate"
 * entschieden. Bei den Methoden "authentication" und "registration" werden die
 * Daten jeweils in einem JSON-kodierten String übergeben. Beim Bezug der
 * Gutscheine über die Methode "getSnippet" ist dies nicht der Fall.
 *
 * @package AffiliPRINT
 * @author Patrick Taddey <p.taddey@affiliprint.de>
 * @version 1.1
 * @copyright Copyright (c) 2014 AffiliPRINT GmbH (http://www.affiliprint.de/)
 * @license Released under the GNU General Public License (Version 2) [http://www.gnu.org/licenses/gpl-2.0.html]
 */
class Communicator
{
	/**
	 * Objekt mit Konfigurationseinstellungen
	 *
	 * @var Object
	 */
	var $_moduleConfig = null;

	/**
	 * String, der den Referer der Kommunikation beinhaltet
	 *
	 * @var String
	 */
	var $_requestReferer;

	/**
	 * Array, das die Standard Anfrage-Parameter enthält
	 *
	 * @var array
	 */
	var $_requestParam;

	/**
	 * Timeout für die Herstellung der Verbindung in Sekunden
	 *
	 * @var int
	 */
	var $_connectTimeout;

	/**
	 * Timeout für die Übertragung in Sekunden
	 *
	 * @var int
	 */
	var $_timeout;

	/**
	 * Konstruktor
	 *
	 * @return void
	 */
	function Communicator()
	{
		return;
	}

	/**
	 * Konfigurationseinstellungen setzen
	 *
	 * @param Objekt $moduleConfig
	 * @return void
	 */
	function setModuleConfig($moduleConfig)
	{
		$this->_moduleConfig = $moduleConfig;
		return;
	}

	/**
	 * Timeout für die Herstellung der Verbindung in Sekunden
	 *
	 * @param String $_connectTimeout
	 * @return void
	 */
	function setConnectTimeout($connectTimeout)
	{
		$this->_connectTimeout = $connectTimeout;
		return;
	}

	/**
	 * Timeout für die Übertragung in Sekunden
	 *
	 * @param String $_timeout
	 * @return void
	 */
	function setTimeout($timeout)
	{
		$this->_timeout = $timeout;
		return;
	}

	/**
	 * Absender der Anfrage
	 *
	 * @param String $_requestReferer
	 * @return void
	 */
	function setRequestReferer($requestReferer)
	{
		$this->_requestReferer = $requestReferer;
		return;
	}

	/**
	 * Default-Übergabe-Parameter setzen, um Anfrage starten zu können. Nur für die
	 * Authentifizierung und Registrierung bei AffiliPRINT
	 *
	 * @param object $moduleConfig
	 * @return void
	 */
	function prepareRequest()
	{
		/* Default-Übergabe-Parameter setzen, um Anfrage starten zu können */
		$this->_requestParam = array(
				'shopUrl' => SHOP_URL,
				'shopSystem' => SHOP_SYSTEM,
				'shopSystemVersion' => SHOP_SYSTEM_VERSION,
				'language' => getLanguageIsoCode(),
				'apiVersion' => $this->_moduleConfig->apiVersion,
				'authenticationToken' => $this->_moduleConfig->authenticationToken,
		);

		return;
	}

	/**
	 * Nimmt die Authentifizierung bei AffiliPRINT vor
	 *
	 * @return mixed $response
	 */
	function authentication()
	{
		$this->_requestParam['do'] = 'makeTokenAuthentication';
		$requestArray = array('data' => jsonEncode($this->_requestParam));
		$response = $this->_communicate($requestArray);
		return $response;
	}

	/**
	 * Sendet die Anfrage per POST entweder über cURL oder fsockopen an AffiliPRINT
	 *
	 * @param array $_requestParam
	 * @param boolean $decodeJson
	 * @return mixed $response
	 */
	function _communicate($requestParam, $decodeJson = true)
	{
		$response = false;

		/* 'cURL' verwenden, sofern vorhanden */
		if(function_exists('curl_init') === true) {
			$response = $this->_getCurlResponse($requestParam);
			/* 'fsockopen' verwenden, sofern vorhanden */
		} elseif(function_exists('fsockopen') === true) {
			$response = $this->_getSocketResponse($requestParam);
		}
				
		/* Rückgabe verarbeiten, JSON zurückgeben sofern erwünscht */
		if($decodeJson === true && $response !== false) {
			return jsonDecode($response);
		} else {
			return $response;
		}
	}

	/**
	 * Stellt eine HTTP-POST-Anfrage mit Hilfe von 'cURL'
	 *
	 * @var array $_requestParam array('data' => 'jsonEncodedData')
	 * @return mixed $response
	 */
	function _getCurlResponse($requestParam)
	{
		/* cURL-Session starten */
		$curlRequest = curl_init();

		/* Optionen setzen */
		curl_setopt($curlRequest, CURLOPT_URL, $this->_moduleConfig->apiUrl);

		curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($curlRequest, CURLOPT_POST, true);

		curl_setopt($curlRequest, CURLOPT_CONNECTTIMEOUT, $this->_connectTimeout);

		curl_setopt($curlRequest, CURLOPT_TIMEOUT, $this->_timeout);

		curl_setopt($curlRequest, CURLOPT_POSTFIELDS, $requestParam);

		curl_setopt($curlRequest, CURLOPT_REFERER, $this->_requestReferer);

		/* cURL-Session ausführen */
		$response = curl_exec($curlRequest);

		/* cURL-Session beenden */
		curl_close($curlRequest);
		
		//die($response);
		if(empty($response) === true) {
			return false;
		} else {
			return $response;	
		}
	}

	/**
	 * Stellt eine HTTP-POST-Anfrage mit Hilfe von 'fsockopen'
	 *
	 * @var array $postData array('data' => 'jsonEncodedData')
	 * @return mixed $response
	 */
	function _getSocketResponse($_requestParam)
	{
		/* Protokoll entfernen */
		$requestUrl = str_replace('http://', '', $this->_moduleConfig->apiUrl);
		$requestUrl = str_replace('https://', '', $requestUrl);

		/* URL in Parameter und Host aufteilen */
		$urlArray = explode("/", $requestUrl);
		$requestUrl = $urlArray[0];
		unset($urlArray[0]);
		$requestPath = "/" . implode("/", $urlArray);

		/* Parameter für die API-Abfrage generieren - nicht für den Bezug der
		 * Gutscheine von AffiliPRINT */
		$requestParamString = "";
		if(isset($_requestParam['data']) === true) {
			$requestParamString = 'data=' . $_requestParam['data'];
		}

		/* Verbindung herstellen */
		$socketRequest = @fsockopen($requestUrl, 80, $errno, $errstr, $this->_connectTimeout);

		/* Anfrage schicken sofern die Verbindung hergestellt werden konnnte */
		if($socketRequest !== false) {

			/* Nachrichten-Kopf der Anfrage erstellen */
			$request = "POST " . $requestPath . " HTTP/1.1\r\n";
			$request .= "Host: " . $requestUrl . "\r\n";
			$request .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$request .= "Content-length: " . strlen($requestParamString) . "\r\n";
			$request .= "Referer: " . $this->_requestReferer . "\r\n";
			$request .= "Connection: close\r\n\r\n";
			$request .= $requestParamString . "\r\n\r\n";

			/* Anfrage stellen bzw. schicken */
			fwrite($socketRequest, $request);

			/* Antwort verarbeiten */
			while(!feof($socketRequest)) {
				$response .= fgets($socketRequest, 4096);
			}
			
			preg_match('~\{(?:[^{}]|(?R))*\}~', $response, $result);
			
			/* HTTP-Header entfernen */
			$response = substr($response, strpos($response, "\r\n\r\n") + 4);

			return $result[0];
		} else {
			return false;
		}
	}

}
?>