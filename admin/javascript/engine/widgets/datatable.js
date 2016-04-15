/* --------------------------------------------------------------
 datatable.js 2015-10-16 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## DataTable Widget
 *
 * Wrapper widget for the jquery datatables plugin. You can create a fully
 * DataTable table with sort, search, pagination and other useful utilities.
 *
 * {@link http://www.datatables.net DataTables Official Website}
 *
 * Place the ".disable-sort" class to <th> elements that shouldn't be sorted.
 *
 * @module Admin/Widgets/datatable
 * @requires datatables
 */
gx.widgets.module(
	'datatable',

	['datatable'],

	/** @lends module:Widgets/datatable */

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// VARIABLES
		// ------------------------------------------------------------------------

		var
			/**
			 * Widget Reference Selector
			 *
			 * @type {object}
			 */
			$this = $(this),

			/**
			 * DataTable plugin handler used for triggering API operations.
			 *
			 * @type {object}
			 */
			$table = {},

			/**
			 * Default options of Widget
			 *
			 * @type {object}
			 */
			defaults = {
				language: jse.libs.datatable.getGermanTranslation()
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

		/**  Define Views Data */
		module.view = {};

		/** Define Models Data */
		module.model = {};

		/** Define Dependencies */
		module.dependencies = {};

		/**
		 * Initialize method of the widget, called by the engine.
		 */
		module.init = function (done) {
			$table = $this.DataTable(options);
			done();
		};

		// Return data to module engine.
		return module;
	});
