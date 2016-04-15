/* --------------------------------------------------------------
 spinner.js 2015-03-04 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Spinner Widget
 *
 * Converts a simple text input element to a value spinner.
 *
 * {@link http://api.jqueryui.com/1.10/slider jQueryUI Documentation - Spinner API}
 *
 * @module Admin/Widgets/spinner
 * @requires jQueryUI
 */
gx.widgets.module(
	'spinner',

	[],

	/** @lends module:Widgets/spinner */

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
			$this.spinner(options);
			done();
		};

		// Return data to module engine.
		return module;
	});
