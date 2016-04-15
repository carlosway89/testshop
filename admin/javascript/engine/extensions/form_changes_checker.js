/* --------------------------------------------------------------
 form_changes_checker.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## From Changes Checker Extension
 *
 * Stores all form data inside $(this) an waits for an trigger to compare the data
 * with the original. A, with the trigger delivered deferred object gets resolved or
 * rejected depending on the result.
 *
 * @module Admin/Extensions/form_changes_checker
 */
gx.extensions.module(
	'form_changes_checker',

	['form'],

	/** @lends module:Extensions/form_changes_checker */

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
			 * Default Options for Extension
			 *
			 * @type {object}
			 */
			defaults = {
				'ignoreClass': '.ignore_changes'
			},

			/**
			 * Final Extension Options
			 *
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),

			/**
			 * Initial Form Data
			 *
			 * @type {array}
			 */
			formData = [],

			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};

		// ------------------------------------------------------------------------
		// EVENT HANDLER
		// ------------------------------------------------------------------------

		/**
		 * Check Forms
		 *
		 * Function to compare the original data with the data that is currently in the
		 * form. the given deferred object gets resolved or rejected.
		 *
		 * @param {object} event jQuery event object
		 * @param {object} deferred JSON object containing the deferred object.
		 */
		var _checkForms = function (event, deferred) {
			event.stopPropagation();

			deferred = deferred.deferred;

			var newData = jse.libs.form.getData($this, options.ignoreClass),
				cache = JSON.stringify(formData),
				current = JSON.stringify(newData),
				returnData = {
					'original': $.extend({}, formData),
					'current': $.extend({}, newData)
				};

			if (cache === current) {
				deferred.resolve(returnData);
			} else {
				deferred.reject(returnData);
			}
		};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Init function of the extension, called by the engine.
		 */
		module.init = function (done) {

			formData = jse.libs.form.getData($this, options.ignoreClass);
			$this
				.on('formchanges.check', _checkForms)
				.on('formchanges.update', function () {
					// Updates the form data stored in cache
					formData = jse.libs.form.getData($this, options.ignoreClass);
				});

			done();
		};

		// Return data to module engine.
		return module;
	});
