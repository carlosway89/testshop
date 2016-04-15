<?php
/**
 * Ausgelagerte Funktionen
 *
 * @package AffiliPRINT
 * @author Patrick Taddey <p.taddey@affiliprint.de>
 * @version 1.1
 * @copyright Copyright (c) 2014 AffiliPRINT GmbH (http://www.affiliprint.de/)
 * @license Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

/**
 * JSON-String dekodieren
 *
 * @param String $jsonString
 * @return mixed
 */
function jsonDecode($jsonString)
{
	require_once (DIR_FS_CATALOG . 'gm/classes/JSON.php');
	$jsonService = new Services_JSON();
	return $jsonService->decode($jsonString);
}

/**
 * Variable in JSON-Zeichenkette kodieren
 *
 * @param mixed $var
 * @return String - a JSON encoded string
 */
function jsonEncode($var)
{
	require_once (DIR_FS_CATALOG . 'gm/classes/JSON.php');
	$jsonService = new Services_JSON();

	$jsonString = $jsonService->encode($var);
	/* Content-Type-Header von der "encode"-Funktion überschreiben, da im Standard
	 * text/html ausgegeben werden soll und sonst die Admin-Page zerhackt wird ;-) */
	header('Content-type: text/html');
	return $jsonString;
}

/**
 * Sprache beziehen
 *
 * @return String ISO CODE, zweistellig, z.B. 'de'
 */
function getLanguageIsoCode()
{
	if (empty($_SESSION['language_code']) === false) {
		return $_SESSION['language_code'];
	} else {
		return SHOP_SYSTEM_DEFAULT_LANGUAGE_ISO_CODE;
	}
}

/**
 * Bezieht die Registrierungsdaten vom Vertriebspartner bzw. angemeldeten
 * Administrator
 *
 * @return array
 */
function getPublisherRegistrationData()
{
	$publisherRegistrationData = array();
	mysql_query("set names 'utf8'");
	$result = xtc_db_query("
		SELECT
			a.entry_gender AS gender,
			a.entry_firstname AS firstName,
			a.entry_lastname AS lastName,
			a.entry_company AS company,
			a.entry_street_address AS street,
			a.entry_postcode AS postcode,
			a.entry_city AS city,
			c.customers_email_address AS email,
			c.customers_telephone AS phone
		FROM " . TABLE_CUSTOMERS . " c, " . TABLE_ADDRESS_BOOK . " a
		WHERE
			c.customers_id = '" . (int)$_SESSION['customer_id'] . "' 
		AND
			c.customers_default_address_id = a.address_book_id");

	if (xtc_db_num_rows($result) == 1) {
		$publisherRegistrationData = xtc_db_fetch_array($result);
	}

	return $publisherRegistrationData;
}

/**
 * Bezieht die Gutscheine von AffiliPRINT über eine entsprechende Schnittstelle
 *
 * @var array $order / array of order-objects
 * @return mixed
 */
function getVouchers($order)
{
	/* letzte Bestellung/Bestellnr beziehen */
	$lastOrder = $order["orders_id"];

	/* Konfiguration beziehen */
	require_once (DIR_FS_CATALOG . 'affiliprint_module/includes/config.php');

	/* Sofern die Gutscheine zur Bestellung $lastOrder bereits in der aktuellen
	 * $_SESSION gecached worden sind, gebe diese aus */
	if (isset($_SESSION['affiliprint'][$lastOrder]) && AFFILIPRINT_DEBUG_MODE === false) {
		return $_SESSION['affiliprint'][$lastOrder];
	}

	/* Konfigurationseinstellungen für das Modul beziehen */
	$moduleConfig = jsonDecode(gm_get_conf('AFFILIPRINT_CONFIGURATION_DATA', 'ASSOC', true));
	/* Sofern das Modul aktiviert worden ist und auch eine Bestellnummer vorhanden
	 * ist, beziehe die Gutscheine zur Bestellung $lastOrder */
	if (empty($lastOrder) === false && $moduleConfig->moduleStatus == 1) {
		/* Überprüfen, ob eine SSL-Verschlüsselung im Onlineshop verwendet wird */
		$protocol = "http://";
		if (ENABLE_SSL === true || ENABLE_SSL === 'true') {
			$protocol = "https://";
		}

		/* Gutschein-Schnipsel generieren */
		$snippetUrl = $protocol . str_replace($moduleConfig->transactionIdWildcard, $lastOrder, $moduleConfig->adsUrl);

		/* Onlineshop-Version beziehen */
		$gx_version = $moduleConfig->shopSystemVersion;
		if (file_exists(DIR_FS_CATALOG . 'release_info.php') === true) {
			require_once (DIR_FS_CATALOG . 'release_info.php');
		}
		$snippetUrl .= "&shop_system=" . $moduleConfig->shopSystem;
		$snippetUrl .= "&shop_system_version=" . urlencode($gx_version);

		/* optional zusätzliche Kunden-Informationen der URL übergeben */
		if ((int)$moduleConfig->useAdditionalData == 1) {
			$additionalDataString = httpBuildQuery($order["coo_order"]->customer);
			if ($additionalDataString !== null) {
				$snippetUrl .= "&additional_data=" . $additionalDataString;
			}
		}
		/* Schnipsel-Script-Tag generieren */
		$snippetSource = str_replace(AFFILIPRINT_SNIPPET_JS_SOURCE, $snippetUrl, AFFILIPRINT_SNIPPET_JS);
		/* Schnipsel in der $_SESSION cachen, um erneute Aufrufe zu vermeiden */
		$_SESSION['affiliprint'][$lastOrder] = $snippetSource;

		return $snippetSource;
	} else {
		return false;
	}
}

/**
 * Erstellen eines URL-kodierten Query-Strings
 *
 * @var array $additionalData
 * @return mixed $additionalDataString
 */
function httpBuildQuery($additionalData)
{
	$additionalDataString = null;
	if (is_array($additionalData) === true) {
		unset($additionalData['vat_id']);
		unset($additionalData['csID']);
		unset($additionalData['id']);
		$additionalDataString = base64_encode(jsonEncode($additionalData));
	}
	return $additionalDataString;
}
?>