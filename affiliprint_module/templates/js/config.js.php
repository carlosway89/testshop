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
?>
var debugMode = Boolean(<?php echo AFFILIPRINT_DEBUG_MODE; ?>);
var consoleLogPrefix = '<?php echo AFFILIPRINT_CONSOLE_LOG_PREFIX; ?>';
var sessionName = '<?php echo xtc_session_name(); ?>';
var sessionId = '<?php echo xtc_session_id(); ?>';
var postUrl = '<?php echo AFFILIPRINT_AJAX_URL; ?>?' + sessionName + '=' + sessionId;
var MESSAGE_FORM_EMPTY = '<?php echo MESSAGE_FORM_EMPTY; ?>';
var MESSAGE_LOADING = '<?php echo MESSAGE_LOADING; ?>';
var delayStatusMessage = '<?php echo DELAY_STATUS_MESSAGE; ?>';
