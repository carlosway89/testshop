/* --------------------------------------------------------------
 event_driven_submit.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Event Driven Submit Extension
 *
 * @module Admin/Extensions/event_driven_submit
 */
gx.extensions.module(
	'event_driven_submit',

	[],

	/** @lends module:Extensions/event_driven_submit */

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------

		var
			/**
			 * Extension Reference
			 *
			 * @type {object}
			 */
			$this = $(this),

			/**
			 * Body Element Selector
			 *
			 * @type {object}
			 */
			$body = $('body'),

			/**
			 * Default Options for Extension
			 *
			 * @type {object}
			 */
			defaults = {},

			/**
			 * Final Extension Options
			 *
			 * @private
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),

			/**
			 * Module Object
			 *
			 * @private
			 * @type {object}
			 */
			module = {};

		// ------------------------------------------------------------------------
		// META INITIALIZE
		// ------------------------------------------------------------------------

		/**
		 * Initialize method of the extension, called by the engine.
		 */
		module.init = function (done) {

			$this.on('submitform', function (event, deferred) {
				jse.libs.form.prefillForm($this, deferred, false);
				$this.submit();
			});

			done();
		};

		// Return data to module engine.
		return module;
	});
