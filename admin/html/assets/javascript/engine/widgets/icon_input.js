/* --------------------------------------------------------------
 icon_input.js 2015-08-11 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Icon Input Widget
 *
 * Turns normal input fields into input fields with an specific background-image.
 *
 *
 * **Example:**
 * ```html
 *  <input data-gx-widget="icon_input" data-icon="url/to/image"/>
 * ```
 * "icon-input" activates the widget and attaches the needed styles for the background-image.
 *
 * @module Admin/Widgets/icon_input
 */
gx.widgets.module(
	'icon_input',

	[],

	/** @lends module:Widgets/icon_input */

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
		// PRIVATE METHODS
		// ------------------------------------------------------------------------

		/**
		 * Add the dropdown functionality to the button.
		 *
		 * Developers can manually add new <li> items to the list in order to display more options to
		 * the users.
		 *
		 * @private
		 */
		var _setBackgroundImage = function () {
			var iconValue = $this.attr('data-icon');
			$this.css('background', 'url(' + iconValue + ')' + ' no-repeat right 8px center white');
		};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Initialize method of the widget, called by the engine.
		 */
		module.init = function (done) {
			_setBackgroundImage();
			done();
		};

		// Return data to module engine.
		return module;
	});
