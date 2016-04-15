/* --------------------------------------------------------------
 loading_spinner.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.loading_spinner = jse.libs.loading_spinner || {};

/**
 * ## Loading Spinner Library
 *
 * This library provides an easy and simple way to display a loading spinner inside any container
 * element to provide a smooth "loading" experience to the UI. If no container is specified then
 * the whole page will be taken for the display. The loading spinner comes from the Font Awesome
 * "fa-spinner" class. You can load this library as a dependency to existing modules.
 *
 * The following usage example will show you how to display and hide the spinner inside an element.
 *
 * ```javascript
 * // Create a selector variable for the target element.
 * var $targetElement = $('#my-div');
 *
 * // The $targetElement will be overlayed by the spinner.
 * var $spinner = window.jse.libs.loading_spinner.show($targetElement);
 *
 * // Do some stuff ...
 *
 * // Hide the spinner when the job is done.
 * window.jse.loading_spinner.hide($spinner);
 * ```
 *
 * @namespace JSE/Libs/loading_spinner
 */
(function (/** @lends JSE/Libs/loading_spinner */  exports) {

	'use strict';

	var
		/**
		 * Contains a list of the active spinners so that they can be validated
		 * before they are destroyed.
		 *
		 * @type {Array}
		 */
		instances = [];

	/**
	 * Show the loading spinner to the target element.
	 *
	 * @param {object} $targetElement (optional) The target element will be overlayed by the spinner. If no
	 * argument is provided then the spinner will overlay the whole page.
	 *
	 * @return {object} Returns the selector of the spinner div element. You can further manipulate the spinner
	 * if required, but you have to provide this selector as a parameter to the "hide" method below.
	 */
	exports.show = function ($targetElement) {
		if (typeof $targetElement !== 'undefined' && typeof $targetElement !== 'object') {
			throw 'Invalid argument provided for the "show" method: ' + typeof $targetElement;
		}

		$targetElement = $targetElement || $('body'); // set default value

		var $spinner = $('<div class="loading-spinner"></div>'),
			fontSize = 80;

		$spinner
			.html('<i class="fa fa-spinner fa-spin"></i>')
			.css({
				'width': $targetElement.innerWidth() + 'px',
				'height': $targetElement.innerHeight() + 'px',
				'box-sizing': 'border-box',
				'background': '#FFF',
				'opacity': '0.8',
				'position': 'absolute',
				'top': $targetElement.offset().top,
				'left': $targetElement.offset().left,
				'font-size': fontSize + 'px',
				'color': '#2196F3' // primary color
			})
			.appendTo('body');

		$spinner.find('i').css({
			'position': 'absolute',
			'left': $spinner.width() / 2 - fontSize / 2,
			'top': $spinner.height() / 2 - fontSize / 2
		});

		instances.push($spinner);

		return $spinner;
	};

	/**
	 * Hide an existing spinner.
	 *
	 * This method will hide and remove the loading spinner markup from the document entirely.
	 *
	 * @param {object} $spinner Must be the selector provided from the "show" method. If the selector
	 * is invalid or no elements were found then an exception will be thrown.
	 *
	 * @return {object} Returns a promise object that will be resolved once the spinner is removed.
	 */
	exports.hide = function ($spinner) {
		var index = instances.indexOf($spinner),
			deferred = $.Deferred();

		if (index === -1) {
			throw 'The provided spinner instance does not exist.';
		}

		instances.splice(index, 1);

		$spinner.fadeOut(400, function () {
			$spinner.remove();
			deferred.resolve();
		});

		return deferred.promise();
	};

})(window.jse.libs.loading_spinner);
