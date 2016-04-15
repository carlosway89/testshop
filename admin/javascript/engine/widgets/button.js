/* --------------------------------------------------------------
 button.js 2015-03-04 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Button Widget
 *
 * Enables the jQuery button functionality to an existing HTML element. By passing
 * extra data attributes you can specify certain options for the widget.
 *
 * {@link http://api.jqueryui.com/1.10/button jQueryUI Documentation - Button API}
 *
 * @module Admin/Widgets/button
 * @requires jQueryUI
 */
gx.widgets.module(
	'button',

	[],

	/** @lends module:Widgets/button */

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
			defaults = {},

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
			$this.button(options);
			done();
		};

		// Return data to module engine.
		return module;
	});
