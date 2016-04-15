<?php
/**
 * Standard-Script für die Administration des AffiliPRINT-Modules im Gambio-Onlineshop
 *
 * Initialisiert eine Instanz der "Request-Handler"-Klasse, die die HTTP-Anfragen verwaltet
 *
 * @package AffiliPRINT
 * @author Patrick Taddey <p.taddey@affiliprint.de>
 * @version 1.1
 * @copyright Copyright (c) 2014 AffiliPRINT GmbH (http://www.affiliprint.de/)
 * @license Released under the GNU General Public License (Version 2) [http://www.gnu.org/licenses/gpl-2.0.html]
 */

require_once ('includes/application_top.php');
AdminMenuControl::connect_with_page('admin.php?do=ModuleCenter');
require_once (DIR_FS_CATALOG . 'affiliprint_module/includes/config.php');
require_once (DIR_FS_CATALOG . 'affiliprint_module/includes/functions.php');
require_once (AFFILIPRINT_DIR . 'includes/classes/Communicator.php');
require_once (AFFILIPRINT_DIR . 'includes/classes/Request.php');

/* Sprachdatei einbinden sofern diese existiert */
$languageFile = AFFILIPRINT_DIR . 'lang/german.php';
if (empty($_SESSION['language']) === false) {
	$tmpLanguageFile = AFFILIPRINT_DIR . 'lang/' . $_SESSION['language'] . '.php';
	if (file_exists($tmpLanguageFile) === true) {
		$languageFile = $tmpLanguageFile;
	}
}
require_once ($languageFile);

/* Anfragen verarbeiten */
$request = new Request();
$request->setConfiguration($configurationData);
$request->setRequestArray($_REQUEST);
$request->handle();
unset($request);
?>