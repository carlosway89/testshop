/* --------------------------------------------------------------
 slider.js 2015-03-04 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Slider Widget
 *
 * Enables the jQuery UI slider widget in the selected element.
 *
 * {@link http://api.jqueryui.com/1.10/slider jQueryUI Documentation - Slider API}
 *
 * @module Admin/Widgets/slider
 * @requires jQueryUI
 */
gx.widgets.module(
	'slider',

	[],

	/** @lends module:Widgets/slider */

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------

		var
			/**
			 * Widget Reference
			 *
			 * @type {object}
			 */
			$this = $(this),

			/**
			 * Default Widget Options
			 *
			 * @type {object}
			 */
			defaults = {
				value: 0
			},

			/**
			 * Final Widget Options
			 *
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),

			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Initialize method of the widget, called by the engine.
		 */
		module.init = function (done) {
			$this.slider(options);
			done();
		};

		// Return data to module engine.
		return module;
	});
