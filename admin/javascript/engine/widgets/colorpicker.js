/* --------------------------------------------------------------
 colorpicker.js 2015-10-16 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Colorpicker Widget
 *
 * Enables the colorpicker for an item.
 *
 * @link https://github.com/mrgrain/colpick
 *
 * @module Admin/Widgets/colorpicker
 * @requires jquery-colpick
 */
gx.widgets.module(
	'colorpicker',

	[],

	/** @lends module:Widgets/colorpicker */

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
			 * Button Element Selector
			 *
			 * @type {object}
			 */
			$button = null,

			/**
			 * Preview Element Selector
			 *
			 * @type {object}
			 */
			$preview = null,

			/**
			 * Input Element Selector
			 *
			 * @type {object}
			 */
			$input = null,

			/**
			 * Default Options for Widget
			 *
			 * @type {object}
			 */
			defaults = {
				'color': '#ffffff' // Default color
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
			$button = $this.find('.picker');
			$preview = $this.find('.color-preview');
			$input = $this.find('input[type="hidden"]');

			if ($input.val()) {
				options.color = $input.val();
			}

			// Enables the colorpicker.
			$button.colpick({
				                'submitText': jse.core.lang.translate('ok', 'buttons'),
				                'color': options.color,
				                'onSubmit': function (result) {
					                var hex = '#' + $.colpick.hsbToHex(result);
					                $preview.css('background-color', hex);
					                $input.val(hex);
					                $button.colpickHide();
				                }
			                });

			// Sets the default values in view.
			$preview.css('background-color', options.color);
			$input.val(options.color);

			done();
		};

		// Return data to module engine.
		return module;
	});
