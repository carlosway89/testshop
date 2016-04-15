/* --------------------------------------------------------------
 tooltip.js 2015-03-04 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Tooltip Widget
 *
 * Enables qTip2 tooltips for child elements with a title attribute. You can
 * change the default tooltip position and other options if you set a data-tooltip-position
 * attribute to the parent element.
 *
 * **Important:** If you use this widgets on elements inside a modal then it will not work,
 * because the modal elements are reset before they are displayed.
 *
 * {@link http://api.jqueryui.com/1.10/tooltip jQueryUI Documentation - Tooltip API}
 *
 * ```html
 * <div data-gx-widget="tooltip">... Elements with title attribute will get a tooltip ... <div>
 * ```
 * @module Admin/Widgets/tooltip
 * @requires jQueryUI
 */
gx.widgets.module(
	'tooltip',

	[],

	/** @lends module:Widgets/tooltip */

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
				position: {
					my: 'left+10 center',
					at: 'right center'
				}
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
			$this.find('[title]').qtip({
				                           style: {
					                           classes: 'qtip-tipsy'
				                           },
				                           position: {
					                           my: 'top center',
					                           at: 'bottom center'
				                           }
			                           });
			done();
		};

		// Return data to module engine.
		return module;
	});
