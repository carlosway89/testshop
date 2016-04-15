/**
 * Standard-JavaScript Datei mit ausgelagerten Funktionen
 *
 * Initialisiert den "clickHandler" und bindet diesen an
 * Elemente mit der Klasse '.clickHandler'
 *
 * @package AffiliPRINT
 * @author Patrick Taddey <p.taddey@affiliprint.de>
 * @version 1.1
 * @copyright Copyright (c) 2014 AffiliPRINT GmbH (http://www.affiliprint.de/)
 * @license Released under the GNU General Public License (Version 2) [http://www.gnu.org/licenses/gpl-2.0.html]
 */

$(document).ready(function()
{
	/* Click-Event an Elemente binden und ausführen */
	$('.clickHandler').ClickHandler();
});

/**
 * Helper: Startet einen AJAX-Request
 *
 * @param String postData
 * @param Object callback
 * @param Object beforeSend
 * @return object response
 */
function ajaxRequest(postData, callback, beforeSend)
{
	var response = new Object();
	$.ajax({
		data : postData,
		url : postUrl,
		dataType : 'json',
		type : 'POST',
		async : false,
		beforeSend : function()
		{

			if(beforeSend != undefined && typeof beforeSend == 'function') {
				beforeSend();
			}
		},
		success : function(response)
		{
			if(callback != undefined && typeof callback == 'function') {
				callback(response);
			}
		}

	});
	return response;
}

/**
 * Helper: Meldungen auf der JavaScript-Konsole ausgeben,
 * sofern vorhanden und
 * Debug-Modus aktiviert
 *
 * @param String message
 * @return void
 */
function consoleLog(message)
{
	/* Überprüfen, ob JavaScript-Konsole vorhanden und
	 * Debug-Modus aktiv */
	if(debugMode === true) {
		try {
			console.log(consoleLogPrefix + message);
		} catch(e) {
			/* Browser hat/unterstützt keine JavaScript Konsole */
		}
	}

	return;
}

/**
 * Helper: Status-Message anzeigen und nach x Sekunden
 * ausblenden
 *
 * @param String className
 * @param String message
 * @return void
 */
function showStatusMessage(className, message)
{
	$('#apStatus').removeClass('success warning error');
	$('#apStatus').addClass(className);
	$('#apStatus #apStatusMessage').html(message);
	$('#apStatus').show();
	$('#apStatus').delay(delayStatusMessage).fadeOut('slow');
	return;
}

/**
 * Helper: Überprüft ob ein Objekt in JS wirklich leer ist
 *
 * @param object obj
 * @return boolean
 */
function isEmpty(obj)
{
	if( typeof obj == 'undefined' || obj === null || obj === '') {
		return true;
	}
	if( typeof obj == 'number' && isNaN(obj)) {
		return true;
	}
	if( obj instanceof Date && isNaN(Number(obj))) {
		return true;
	}
	return false;
}