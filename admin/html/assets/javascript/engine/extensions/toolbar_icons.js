/* --------------------------------------------------------------
 toolbar_icons.js 2015-09-19 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Toolbar Icons Extension
 *
 * Extension to inject icons for toolbar HTML markup.
 *
 * @module Admin/Extensions/toolbar_icons
 */
gx.extensions.module(
	'toolbar_icons',

	[],

	/** @lends module:Extensions/toolbar_icons */

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
			defaults = {},

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
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Initialize method of the extension, called by the engine.
		 */
		module.init = function (done) {

			// Define class names and the respective Font-Awesome classes here
			var classes = {
				'.btn-edit': 'fa-pencil',
				'.btn-view': 'fa-eye',
				'.btn-editdoc': 'fa-pencil',
				'.btn-delete': 'fa-trash-o',
				'.btn-order': 'fa-shopping-cart',
				'.btn-caret': 'fa-caret-right',
				'.btn-folder': 'fa-folder-open',
				'.btn-multi-action': 'fa-check-square-o',
				'.btn-cash': 'fa-money',
				'.btn-add': 'fa-plus'
			};

			// Let's rock
			$.each(classes, function (key, value) {
				var composedClassName = [
					value,
					(options.large ? ' fa-lg' : ''),
					(options.fixedwidth ? ' fa-fw' : '')
				].join('');

				var $tag = $('<i class="fa ' + composedClassName + '"></i>');
				$this.find(key).prepend($tag);
			});

			done();
		};

		// Return data to module engine.
		return module;
	});
