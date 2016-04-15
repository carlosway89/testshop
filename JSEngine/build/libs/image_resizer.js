/* --------------------------------------------------------------
 image_resizer.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.image_resizer = jse.libs.image_resizer || {};

/**
 * ## Image Resizer
 *
 * Resizes images with respective aspect ratio.
 *
 * @namespace JSE/Libs/image_resizer
 */
(function (exports) {

	'use strict';

	exports.resize = function (element, options) {

		var $that = $(element);
		var settings = {
			width: 150,
			height: 150
		};
		options = $.extend(settings, options);

		var maxWidth = options.width;
		var maxHeight = options.height;
		var ratio = 0;
		var width = $that.width();
		var height = $that.height();

		if (width > maxWidth) {
			ratio = maxWidth / width;
			$that.css('width', maxWidth);
			$that.css('height', height * ratio);

		}

		if (height > maxHeight) {
			ratio = maxHeight / height;
			$that.css('height', maxHeight);
			$that.css('width', width * ratio);

		}

	};

})(jse.libs.image_resizer);
