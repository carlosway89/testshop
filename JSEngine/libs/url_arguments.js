/* --------------------------------------------------------------
 url_arguments.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.url_arguments = jse.libs.url_arguments || {};

/**
 * ## Url arguments library
 *
 * This library is created to help coding when values of url are required.
 *
 * @namespace JSE/Libs/url_arguments
 */
(function (/** @lends JSE/Libs/url_arguments */ exports) {

	'use strict';

	/**
	 * Return an object which is equal to the PHPs magic $_GET array.
	 *
	 * @returns {{}}
	 */
	exports.getParameterArray = function () {
		var params = {},
			getParamString = window.location.search.substr(1),
			getParamArray,
			tmpArray,
			i;

		if (getParamString === null || getParamString === '') {
			return params;
		}
		getParamArray = getParamString.split('&');
		for (i = 0; i < getParamArray.length; i = i + 1) {
			tmpArray = getParamArray[i].split('=');
			params[tmpArray[0]] = tmpArray[1];
		}
		return params;
	};

	/**
	 * Return the current file name.
	 *
	 * @returns string
	 */
	exports.getCurrentFile = function () {
		var urlArray = window.location.pathname.split('/');

		return urlArray[urlArray.length - 1];
	};

})(jse.libs.url_arguments);
