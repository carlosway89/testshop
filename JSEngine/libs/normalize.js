/* --------------------------------------------------------------
 normalize.js 2015-10-14 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.normalize = jse.libs.normalize || {};

/**
 * Normalize input and output.
 *
 * This library contains methods that have to do with input and output normalization (XSS).
 *
 * @namespace JSE/Libs/normalize
 */
(function (/** @lends JSE/Libs/normalize */ exports) {

	'use strict';

	/**
	 * HTML Escape Entities for JS
	 *
	 * @link http://stackoverflow.com/a/25207
	 *
	 * @param {string} text The text to be escaped
	 *
	 * @return {string} Returns the escaped string.
	 *
	 * @public
	 */
	exports.escapeHtml = function(text) {
		return $('<div/>').text(text).html();
	};

})(jse.libs.normalize);
