/* --------------------------------------------------------------
 lightbox.js 2015-09-29 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Lightbox Widget
 *
 * Widget to easily configure and open lightboxes.
 *
 * {@link http://lokeshdhakar.com/projects/lightbox2 Lightbox Project Website}
 *
 * @module Admin/Widgets/lightbox
 * @requires Lightbox
 */
gx.widgets.module(
	'lightbox',

	[],

	/** @lends module:Widgets/lightbox */

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
			 * Default Options for Widget
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
		// EVENT HANDLERS
		// ------------------------------------------------------------------------

		/**
		 * Click handler that opens the lightbox and initializes the default behavior.
		 *
		 * @param {object} event jQuery-event-object
		 */
		var _clickHandler = function (event) {
			event.preventDefault();
			event.stopPropagation();

			var $self = $(this),
				dataset = module._data($self),
				settingDataSet = {},
				paramDataSet = {};

			$.each(dataset, function (key, value) {
				if (key.indexOf('setting') === 0) {
					settingDataSet[key.replace('setting_', '')] = value;
				} else {
					paramDataSet[key.replace('param_', '')] = value;
				}
			});

			$self.lightbox_plugin(settingDataSet, paramDataSet);
		};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Initialize method of the widget, called by the engine.
		 */
		module.init = function (done) {
			$this.on('click', '.open_lightbox', _clickHandler);
			done();
		};

		// Return data to module engine.
		return module;
	});
