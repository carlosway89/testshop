/* --------------------------------------------------------------
 polyfills.js 2015-10-28 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Polyfills for cross-browser compatibility.
 *
 * @namespace JSE/Core/polyfills
 */
(function () {

	'use strict';

	if (!Array.prototype.indexOf) {
		Array.prototype.indexOf = function (searchElement, fromIndex) {
			var k;
			if (this == null) {
				throw new TypeError('"this" is null or not defined');
			}

			var O = Object(this);
			var len = O.length >>> 0;

			if (len === 0) {
				return -1;
			}

			var n = +fromIndex || 0;

			if (Math.abs(n) === Infinity) {
				n = 0;
			}

			if (n >= len) {
				return -1;
			}

			k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);

			while (k < len) {
				var kValue;
				if (k in O && O[k] === searchElement) {
					return k;
				}
				k++;
			}
			return -1;
		};
	}

	// Internet Explorer does not support the origin property of the window.location object.
	// @link http://tosbourn.com/a-fix-for-window-location-origin-in-internet-explorer
	if (!window.location.origin) {
		window.location.origin = window.location.protocol + '//' +
		                         window.location.hostname + (window.location.port ? ':' + window.location.port : '');
	}

	// Date.now method polyfill
	// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/now
	if (!Date.now) {
		Date.now = function now() {
			return new Date().getTime();
		};
	}
})();


