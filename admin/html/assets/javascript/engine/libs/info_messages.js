/* --------------------------------------------------------------
 info_messages.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.info_messages = jse.libs.info_messages || {};

/**
 * ## Info Messages library
 *
 * This library creates info message boxes and displays them on the screen.
 *
 * You will need to provide the full URL in order to load this library as a dependency to a module:
 * e.g. jse.core.config.get('shopUrl') + '/admin/html/assets/javascript/engine/libs/info_messages'
 *
 * @namespace Admin/Libs/info_messages
 */
(function (/** @lends Admin/Libs/info_messages */ exports) {

	'use strict';

	/**
	 * Container element for info messages
	 *
	 * @type {object}
	 */
	var $messagesContainer = $('.message_stack_container');

	/**
	 * Append a message box to info messages container and display it
	 *
	 * @param {string} message Message to be displayed.
	 * @param {string} type Message type can be one of the "info", "warning", "error" & "success".
	 *
	 * @private
	 */
	var _add = function (message, type) {
		var $alert = $('<div class="alert alert-' + type + '" data-gx-compatibility="close_alert_box">' +
		               '<button type="button" class="close" data-dismuss="alert">×</button>' + message + '</div>');

		$alert.find('.close').on('click', function () {
			$(this).parent('.alert').hide();
		});

		$messagesContainer.append($alert);
		$messagesContainer.show();
	};

	/**
	 * Add a red error message.
	 *
	 * @param {string} message Message to be displayed.
	 */
	exports.addError = function (message) {
		_add(message, 'danger');
	};

	/**
	 * Add a blue info message.
	 *
	 * @param {string} message Message to be displayed.
	 */
	exports.addInfo = function (message) {
		_add(message, 'info');
	};

	/**
	 * Add a green success message.
	 *
	 * @param {string} message Message to be displayed.
	 */
	exports.addSuccess = function (message) {
		_add(message, 'success');
	};

	/**
	 * Add a yellow warning message.
	 *
	 * @param {string} message Message to be displayed.
	 */
	exports.addWarning = function (message) {
		_add(message, 'warning');
	};

})(jse.libs.info_messages);
