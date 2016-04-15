<?php
/**
 * Konfigurationsdatei
 * 
 * @package AffiliPRINT
 * @author Patrick Taddey <p.taddey@affiliprint.de>
 * @version 1.1
 * @copyright Copyright (c) 2014 AffiliPRINT GmbH (http://www.affiliprint.de/)
 * @license Released under the GNU General Public License (Version 2) [http://www.gnu.org/licenses/gpl-2.0.html]
 */

/**
 * Funktion, die eine Konstante definiert, sofern diese noch nicht gesetzt worden
 * ist. Wird benötigt, um eigene Konstanten für eine Testumgebung zu definieren
 * und die Konstanten des Live-Systems zu überschreiben
 *
 * @param String $name
 * @param String $value
 * @return void
 */
function defineMe($name, $value)
{
	if(defined($name) === false) {
		define($name, $value);
	}
	return;
}

/* Überprüfen, ob eine Test-Konfigurationsdatei vorhanden ist in der
 * entsprechende Test-Konstanten vorhanden sind */
if(file_exists(dirname(__FILE__) . "/myConfig.php")) {
	include dirname(__FILE__) . '/myConfig.php';
}

/* Debug-Modus zentral de/aktivieren */
defineMe('AFFILIPRINT_DEBUG_MODE', false);

/* Verzeichnisse und Urls - Konstanten im Onlineshop in der Datei
 * "includes/configure.php" vorgegeben */
defineMe('AFFILIPRINT_LINK', 'http://www.affiliprint.de/');
defineMe('AFFILIPRINT_DIR', DIR_FS_CATALOG . 'affiliprint_module/');
defineMe('AFFILIPRINT_DIR_CSS', DIR_WS_CATALOG . 'affiliprint_module/templates/css/affiliprint.css');
defineMe('AFFILIPRINT_LOGO', DIR_WS_CATALOG . 'affiliprint_module/templates/css/images/logo_affiliprint.png');
defineMe('AFFILIPRINT_NURSE', DIR_WS_CATALOG . 'affiliprint_module/templates/css/images/malte_grundmann.jpg');
defineMe('AFFILIPRINT_DIR_JS', DIR_WS_CATALOG . 'affiliprint_module/templates/js/');
defineMe('AFFILIPRINT_AJAX_URL', 'affiliprint_module.php');

/* Informationen über den Onlineshop */
defineMe('SHOP_SYSTEM', 'gambio');
defineMe('SHOP_SYSTEM_VERSION', 'GX2');
defineMe('SHOP_SYSTEM_DEFAULT_LANGUAGE_ISO_CODE', 'de');
defineMe('SHOP_URL', HTTP_SERVER . DIR_WS_CATALOG);
defineMe('SHOP_ADMIN_IFRAME_URL', SHOP_URL . "admin/affiliprint_module.php");

/* Sonstige Einstellungen */

/* nach x Millisekunden wird die Nachricht ausgeblendet */
defineMe('DELAY_STATUS_MESSAGE', 5000);

/* Timeouts für die Verbindung zur AffiliPRINT-Api */
defineMe('AFFILIPRINT_API_CONNECT_TIMEOUT', 60);
defineMe('AFFILIPRINT_API_TIMEOUT', 60);

/* Timeouts für die Anzeige der Gutscheine */
defineMe('AFFILIPRINT_SNIPPET_CONNECT_TIMEOUT', 2);
defineMe('AFFILIPRINT_SNIPPET_TIMEOUT', 2);

/* Wichtig für die Anzeige der Gutscheine */
defineMe("AFFILIPRINT_SNIPPET_JS", '<script src="#JS_SOURCE#" type="text/javascript"></script>');
defineMe("AFFILIPRINT_SNIPPET_JS_SOURCE", '#JS_SOURCE#');

/* Modul-Konfiguration: wird in der Datenbank-Tabelle "gm_configuration" abgespeichert */
$configurationData = array(
		'additionalUserData' => false,
		'adgroupUid' => '',
		'adsUrl' => '',
		'apiUrl' => 'http://shopapi.kunden-bonus.de/ShopInterface.php',
		'apiVersion' => '1.0',
		'authenticationStatus' => 0,
		'authenticationToken' => '',
		'language' => '',
		'hasCampaigns' => false,
		'iframeUrl' => 'http://shopapi.kunden-bonus.de/form/?data=',
		'moduleStatus' => 2,
		'useAdditionalData' => 0,
		'publisherUid' => '',
		'shopSystem' => '',
		'shopSystemVersion' => '',
		'shopUrl' => '',
		'transactionIdWildcard' => '',
		'vcpUrl' => 'http://vcp.kunden-bonus.de/',
);
?>