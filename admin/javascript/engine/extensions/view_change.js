/* --------------------------------------------------------------
 view_change.js 2015-09-17 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## View Change Extension
 *
 * Extension to show or hide other elements corresponding to a checkbox state.
 *
 * @module Admin/Extensions/view_change
 */
gx.extensions.module(
	'view_change',

	[],

	/** @lends module:Extensions/view_change */

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
			 * Parent Selector (default body)
			 *
			 * @type {object}
			 */
			$parent = $('body'),

			/**
			 * Default Options for Extension
			 *
			 * @type {object}
			 */
			defaults = {
				on: null, // Selector for the elements that are shown if the checkbox is set
				off: null, // Selector for the elements that are shown if the checkbox is not set
				closest: null // Got to the closest X-element and search inside it for the views
			},

			/**
			 * Final Extension Options
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
		// FUNCTIONALITY
		// ------------------------------------------------------------------------

		/**
		 * Shows or hides elements corresponding to the checkbox state.
		 */
		var _changeHandler = function () {
			if ($this.prop('checked')) {
				$parent.find(options.on).show();
				$parent.find(options.off).hide();
				$this.attr('checked', 'checked');
			} else {
				$parent.find(options.on).hide();
				$parent.find(options.off).show();
				$this.removeAttr('checked');
			}

		};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Initialize method of the extension, called by the engine.
		 */
		module.init = function (done) {
			if (options.closest) {
				$parent = $this.closest(options.closest);
			}
			$this.on('change', _changeHandler);
			_changeHandler();

			done();
		};

		// Return data to module engine.
		return module;
	});
