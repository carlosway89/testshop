/* --------------------------------------------------------------
 debug.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.debug = jse.core.debug || {};

/**
 * JS Engine debug object.
 *
 * This object provides an wrapper to the console.log function and enables easy use
 * of the different log types like "info", "warning", "error" etc.
 *
 * @namespace JSE/Core/debug
 */
(function (/** @lends JSE/Core/debug */ exports) {
	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	/**
	 * All possible debug levels in the order of importance.
	 *
	 * @name Core/Debug.levels
	 * @public
	 * @type {array}
	 */
	var levels = ['DEBUG', 'INFO', 'LOG', 'WARN', 'ERROR', 'ALERT', 'SILENT'];

	/**
	 * Executes the correct console/alert statement.
	 *
	 * @name Core/Debug._execute
	 * @private
	 * @method
	 *
	 * @param {object} caller (optional) Contains the caller information to be displayed.
	 * @param {object} data (optional) Contains any additional data to be included in the debug output.
	 */
	var _execute = function (caller, data) {
		var currentLogIndex = levels.indexOf(caller),
			allowedLogIndex = levels.indexOf(jse.core.config.get('debug')),
			consoleMethod = null;

		if (currentLogIndex >= allowedLogIndex) {
			consoleMethod = caller.toLowerCase();

			if (consoleMethod === 'alert') {
				alert(JSON.stringify(data));
				return;
			}

			if (consoleMethod === 'mobile') {
				var $dbgLayer = $('.mobileDbgLayer');
				if (!$dbgLayer.length) {
					$dbgLayer = $('<div />');
					$dbgLayer.addClass('mobileDbgLayer');
					$dbgLayer.css({
						'position': 'fixed',
						'top': 0,
						'left': 0,
						'max-height': '50%',
						'min-width': '200px',
						'max-width': '300px',
						'background-color': 'crimson',
						'z-index': 100000,
						'overflow': 'scroll'
					});
					$('body').append($dbgLayer);
				}

				$dbgLayer.append('<p>' + JSON.stringify(data) + '</p>');
				return;
			}

			if (typeof console === 'undefined') {
				return; // There is no console support so do not proceed.
			}

			if (typeof console[consoleMethod].apply === 'function' || typeof console.log.apply === 'function') {
				if (typeof console[consoleMethod] !== 'undefined') {
					console[consoleMethod].apply(console, data);
				} else {
					console.log.apply(console, data);
				}
			} else {
				console.log(data);
			}

			return true;
		}
		return false;
	};

	// ------------------------------------------------------------------------
	// VARIABLE EXPORT
	// ------------------------------------------------------------------------

	/**
	 * Replaces console.debug
	 *
	 * @params {all} Any data that should be shown in the console statement
	 *
	 * @name Core/Debug.debug
	 * @public
	 * @method
	 */
	exports.debug = function () {
		_execute('DEBUG', arguments);
	};

	/**
	 * Replaces console.info
	 *
	 * @params {all} Any data that should be shown in the console statement
	 *
	 * @name Core/Debug.info
	 * @public
	 * @method
	 */
	exports.info = function () {
		_execute('INFO', arguments);
	};

	/**
	 * Replaces console.log
	 *
	 * @params {all} Any data that should be shown in the console statement
	 *
	 * @name Core/Debug.log
	 * @public
	 * @method
	 */
	exports.log = function () {
		_execute('LOG', arguments);
	};

	/**
	 * Replaces console.warn
	 *
	 * @params {all} Any data that should be shown in the console statement
	 *
	 * @name Core/Debug.warn
	 * @public
	 * @method
	 */
	exports.warn = function () {
		_execute('WARN', arguments);
	};

	/**
	 * Replaces console.error
	 *
	 * @param {all} Any data that should be shown in the console statement
	 *
	 * @name Core/Debug.error
	 * @public
	 * @method
	 */
	exports.error = function () {
		_execute('ERROR', arguments);
	};

	/**
	 * Replaces alert
	 *
	 * @param {all} Any data that should be shown in the console statement
	 *
	 * @name Core/Debug.alert
	 * @public
	 * @method
	 */
	exports.alert = function () {
		_execute('ALERT', arguments);
	};

	/**
	 * Debug info for mobile devices.
	 */
	exports.mobile = function () {
		_execute('MOBILE', arguments);
	};

}(jse.core.debug));
