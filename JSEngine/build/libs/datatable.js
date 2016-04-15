/* --------------------------------------------------------------
 datatable.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.datatable = jse.libs.datatable || {};

/**
 * ## DataTable Library
 *
 * This is a wrapper library for the manipulation of jQuery DataTables. Use the "create"
 * method with DataTable configuration to initialize a table on your page. All you
 * need when using this library is an empty `<table>` element. Visit the official
 * website of DataTables to check examples and other information about the plugin.
 *
 * {@link http://www.datatables.net Official DataTables Website}
 *
 * #### Example - Create A New Instance
 * ```javascript
 * var tableApi = jse.libs.datatable.create($('#my-table'), {
 *      ajax: 'http://shop.de/table-data.php',
 *      columns: [
 *          { title: 'Name', data: 'name' defaultContent: '...' },
 *          { title: 'Email', data: 'email' },
 *          { title: 'Actions', data: null, orderable: false, defaultContent: 'Add | Edit | Delete' },
 *      ]
 * });
 * ```
 *
 * #### Example - Add Error Handler
 * ```javascript
 * jse.libs.datatable.error($('#my-table'), function(event, settings, techNote, message) {
 *      // Log error in the JavaScript console.
 *      console.log('DataTable Error:', message);
 * });
 * ```
 *
 * @namespace JSE/Libs/datatable
 */
(function (/** @lends JSE/Libs/datatable */ exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// FUNCTIONALITY
	// ------------------------------------------------------------------------

	/**
	 * Creates a DataTable Instance
	 *
	 * This method will create a new instance of datatable into a `<table>` element. It enables
	 * developers to easily pass the configuration needed for different and more special situations.
	 *
	 * @param {object} $target jQuery object for the target table.
	 * @param {object} configuration DataTables configuration applied on the new instance.
	 *
	 * @return {object} Returns the DataTable API instance (different from the jQuery object).
	 */
	exports.create = function ($target, configuration) {
		return $target.DataTable(configuration);
	};

	/**
	 * Set error handler for specific DataTable.
	 *
	 * DataTables provide a useful mechanism that enables developers to control errors
	 * during data parsing. If there is an error in the AJAX response or some data are
	 * invalid in the JavaScript code you can use this method to control the behavior
	 * of the app and show or log the error messages.
	 *
	 * {@link http://datatables.net/reference/event/error}
	 *
	 * @param {object} $target jQuery object for the target table.
	 * @param {object} callback Provide a callback method called with the "event",
	 * "settings", "techNote", "message" arguments (see provided link).
	 */
	exports.error = function ($target, callback) {
		$.fn.dataTable.ext.errMode = 'none';
		$target.on('error.dt', callback);
	};

	/**
	 * Set callback method when ajax load of data is complete.
	 *
	 * This method is useful for checking PHP errors or modifying the data before
	 * they are displayed to the server.
	 *
	 * {@link http://datatables.net/reference/event/xhr}
	 *
	 * @param {object} $target jQuery object for the target table.
	 * @param {object} callback Provide a callback method called with the "event",
	 * "settings", "techNote", "message" arguments (see provided link).
	 */
	exports.ajaxComplete = function ($target, callback) {
		$target.on('xhr.dt', callback);
	};

	/**
	 * Set table column to be displayed as an index.
	 *
	 * This method will easily enable you to set a column as an index column, used
	 * for numbering the table rows regardless of the search, sorting and row count.
	 *
	 * {@link http://www.datatables.net/examples/api/counter_columns.html}
	 *
	 * @param {object} $target jQuery object for the target table.
	 * @param {number} columnIndex Zero based index of the column to be indexed.
	 */
	exports.indexColumn = function ($target, columnIndex) {
		$target.on('order.dt search.dt', function () {
			$target.DataTable().column(columnIndex, {
				search: 'applied',
				order: 'applied'
			}).nodes().each(function (cell, index) {
				cell.innerHTML = index + 1;
			});
		});
	};

	/**
	 * Get german translation of DataTables
	 *
	 * This method provides a quick way to get the language JSON without having to perform
	 * and AJAX request to the server. If you setup your DataTable manually you can set the
	 * "language" attribute with this method.
	 *
	 * @returns {object} Returns the german translation, must be the same as the "german.lang.json" file.
	 */
	exports.getGermanTranslation = function () {
		return {
			'sEmptyTable': 'Keine Daten in der Tabelle vorhanden',
			'sInfo': '_START_ bis _END_ von _TOTAL_ Einträgen',
			'sInfoEmpty': '0 bis 0 von 0 Einträgen',
			'sInfoFiltered': '(gefiltert von _MAX_ Einträgen)',
			'sInfoPostFix': '',
			'sInfoThousands': '.',
			'sLengthMenu': '_MENU_ Einträge anzeigen',
			'sLoadingRecords': 'Wird geladen...',
			'sProcessing': 'Bitte warten...',
			'sSearch': 'Suchen',
			'sZeroRecords': 'Keine Einträge vorhanden.',
			'oPaginate': {
				'sFirst': 'Erste',
				'sPrevious': 'Zurück',
				'sNext': 'Nächste',
				'sLast': 'Letzte'
			},
			'oAria': {
				'sSortAscending': ': aktivieren, um Spalte aufsteigend zu sortieren',
				'sSortDescending': ': aktivieren, um Spalte absteigend zu sortieren'
			}
		};
	};

}(jse.libs.datatable));
