/* --------------------------------------------------------------
 user_configuration_service.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.user_configuration_service = jse.libs.user_configuration_service || {};

/**
 * ## User Configuration Service
 *
 * Performs an AJAX request for getting/saving user configuration values using
 * the UserConfigurationService on the server side.
 *
 *```js
 * var options= {
 *     data: {
 *         userId: 1,  // Current user ID
 *         configurationKey: 'recentSearchArea', // Configuration key
 *         configurationValue: '', // Configuration value (only for posting)
 *     },
 *
 *     onSuccess: function (data) {}, // Callback function, that will be executed on successful request,
 *                                    // contains the response as argument
 *
 *     onError: function (data) {},   // Callback function, that will be executed on failed request
 * }
 *
 * jse.libs.user_configuration_service.set(options); // Set values
 *
 * jse.libs.user_configuration_service.get(options); // Get values
 * ```
 *
 * @namespace JSE/Libs/user_configuration_service
 */
(function (/** @lends JSE/Libs/user_configuration_service */ exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// DEFAULTS
	// ------------------------------------------------------------------------

	/**
	 * Default Library Settings
	 *
	 * @type {object}
	 */
	var defaults = {
		// URL
		baseUrl: 'admin.php?do=UserConfiguration',
		urlSet: '/set',
		urlGet: '/get'
	};

	// ------------------------------------------------------------------------
	// PRIVATE METHODS
	// ------------------------------------------------------------------------

	/**
	 * Performs AJAX request
	 *
	 * @param {object} params Contains the request parameters.
	 * @param {string} params.type - type of request
	 * @param {function} params.onSuccess - callback on success
	 * @param {function} params.onError - callback on success
	 * @param {object} params.data - request parameter
	 *
	 * @throws Error
	 *
	 * @public
	 */
	var _request = function (params) {
		$.ajax({
			url: [
				defaults.baseUrl,
				(params.type === 'set' ? defaults.urlSet : defaults.urlGet)
			].join(''),
			dataType: 'json',
			data: params.data,
			method: (params.type === 'set' ? 'post' : 'get'),
			success: function (data) {
				if (params.type === 'get') { // GET
					_handleSuccess(data, params);
				} else { // POST
					_handleSuccess({}, params);
				}
			},
			error: function (data) {
				if (typeof params.onError === 'function') {
					params.onError(data);
				}
			}
		});
	};

	/**
	 * Handles success requests.
	 *
	 * @param {object} data - Data returned from server
	 * @param {object} params - Parameters
	 */
	var _handleSuccess = function (data, params) {
		var response = {};
		if (data.success && data.configurationValue) {
			response = data;
		}
		if (typeof params.onSuccess === 'function') {
			params.onSuccess(response);
		}
	};

	// ------------------------------------------------------------------------
	// PUBLIC METHODS
	// ------------------------------------------------------------------------

	/**
	 * Get user configuration value.
	 *
	 * @param {object} options
	 * @param {function} options.onSuccess - callback on success
	 * @param {function} options.onError - callback on success
	 * @param {object} options.data - request parameter
	 *
	 * @public
	 */
	exports.get = function (options) {
		options.type = 'get';
		_request(options);
	};

	/**
	 * Set user configuration value.
	 *
	 * @param {object} options
	 * @param {function} options.onSuccess - callback on success
	 * @param {function} options.onError - callback on success
	 * @param {object} options.data - request parameter
	 *
	 * @public
	 */
	exports.set = function (options) {
		options.type = 'set';
		_request(options);
	};

})(jse.libs.user_configuration_service);
