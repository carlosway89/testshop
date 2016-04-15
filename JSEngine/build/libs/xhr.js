/* --------------------------------------------------------------
 xhr.js 2015-10-14 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.xhr = jse.libs.xhr || {};

/**
 * ## Contains AJAX helper methods.
 *
 * @namespace JSE/Libs/xhr
 */
(function (/** @lends JSE/Libs/xhr */ exports) {

	'use strict';

	/**
	 * Default AJAX Options
	 *
	 * @type {object}
	 */
	var defaultAjaxOptions = {
		type: 'post',
		dataType: 'json',
		cache: false,
		async: true
	};


	/**
	 * AJAX-Function
	 *
	 * @param {object} parameters AJAX-config object which gets merged with the default settings from config.
	 *
	 * @return {object} Returns an ajax compatible promise object.
	 *
	 * @public
	 */
	exports.ajax = function (parameters, ignoreFail) {

		var $pageToken = $('input[name="page_token"]');

		parameters = parameters || {};
		parameters.data = parameters.data || {};

		// If no page token where provided try to use the existing one.
		if (!parameters.data.page_token) {
			parameters.data.page_token = ($pageToken.length) ? $pageToken.val() : '';
		}

		var options = $.extend({}, defaultAjaxOptions, parameters),
			deferred = $.Deferred(),
			promise = deferred.promise();

		/**
		 *    Default fail handler
		 *    @param {string} message    Message that will be shown
		 */
		var _failHandler = function (message) {
			message = message || 'JavaScript AJAX Error';
			//Modal.error({"content": message});
			deferred.reject();
		};

		// The ajax call
		var ajax = $.ajax(options).done(function (result) {
			// Check if it is an JSON-compatible result if so, check the success message.
			if (result.success !== undefined && result.success === false && !ignoreFail) {
				_failHandler(result.message);
			} else {
				// set new page_token
				if (result.page_token !== undefined) {
					$pageToken.val(result.page_token);
				}
				deferred.resolve(result);
			}
		}).fail(function () {
			if (!ignoreFail) {
				_failHandler();
			} else {
				deferred.reject();
			}
		});

		// Add an ajax abort method to the promise, for cases where we need to abort
		// the AJAX request.
		promise.abort = function () {
			ajax.abort();
		};

		return promise;
	};


	/**
	 * Shortcut to use the default ajax function with GET
	 *
	 * @param {object} parameters AJAX-config object which gets merged with the default settings from config.
	 *
	 * @return {object} Returns an ajax compatible promise object.
	 *
	 * @public
	 */
	exports.get = function (parameters, ignoreFail) {
		return exports.ajax($.extend({}, {type: 'get'}, parameters), ignoreFail);
	};

	/**
	 * Shortcut to use the default ajax function with POST.
	 *
	 * @param {json} parameters AJAX-config object which gets merged with the default settings from config.
	 *
	 * @return {object} Returns an ajax compatible promise object.
	 *
	 * @public
	 */
	exports.post = function (parameters, ignoreFail) {
		return exports.ajax($.extend({}, {type: 'post'}, parameters), ignoreFail);
	};

})(jse.libs.xhr);
