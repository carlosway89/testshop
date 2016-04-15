/* --------------------------------------------------------------
 ajax_file_upload.js 2015-09-17 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## AJAX File Upload Extension
 *
 * This extension will enable an existing **input[type=file]** element to upload files through AJAX.
 * The upload method can be invoked either manually by calling the "upload" function, or automatically
 * once the file is selected. A "validate" event is triggered before upload starts so that you can
 * validate the selected file before it is uploaded and stop the procedure upon request.
 *
 * Currently the module supports the basic upload functionality but you can add extra on your own
 * by following code examples in the official page of the plugin.
 *
 * {@link https://github.com/blueimp/jQuery-File-Upload/wiki/Basic-plugin}
 *
 * #### Important
 * If you need to support older versions of Internet Explorer just use the automatic upload mode
 * because the manual mode uses the JavaScript File API and this is supported from IE 10+.
 *
 * #### Methods
 * ```javascript
 * $('#upload-file').validate(); // Trigger selected file validation, returns a bool value.
 * $('#upload-file').upload(callback); // Trigger Upload, callback argument is optional.
 * ```
 *
 * #### Events
 * ```javascript
 * $('#upload-file').on('validate', function(event) {}); // Add your validation rules, triggered
 *                                                       // before upload (Manual Mode - Requires File API).
 * $('#upload-file').on('upload', function(event, response) {}); // Triggered when server responds to upload
 *                                                               // request (Manual + Auto Mode).
 * ```
 *
 * #### Example - Automatic Upload
 * ```html
 * <!-- HTML -->
 * <input id="upload-file" type="file" data-gx-extension="ajax_file_upload"
 *             data-ajax_file_upload-url="http://url/to/upload-script.php" />
 *
 * <!-- JavaScript -->
 * <script>
 *     $('#upload-file').on('validate', function(event, file) {
 *          // Validation Checks (Only IE 10+) ...
 *          return true; // Return true for success or false for failure - will stop the upload.
 *     });
 *
 *     $('#upload-file').on('upload', function(event, response) {
 *          // "Response" variable contains information about the server response on file upload.
 *     });
 * </script>
 * ```
 *
 * #### Example - Manual Upload
 * ```html
 * <!-- HTML -->
 * <input id="upload-file" type="file" data-gx-extension="ajax_file_upload"
 *         data-ajax_file_upload-url="http://url/to/upload-script.php" data-ajax_file_upload-auto="false" />
 * <button id="upload-file-button">Trigger Upload</button>
 *
 * <!-- JavaScript -->
 * <script>
 *     $('#upload-file-button').on('click', function() {
 *          $('#upload-file').upload(function(response) {
 *              // Callback Function (Optional)
 *          });
 *     });
 * </script>
 * ```
 *
 * @module Admin/Extensions/ajax_file_upload
 * @requires jQuery-AjaxFileUpload
 */
gx.extensions.module(
	'ajax_file_upload',

	[],

	/** @lends module:Extensions/ajax_file_upload */

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------

		var
			/**
			 * Extension Reference Selector
			 *
			 * @type {object}
			 */
			$this = $(this),

			/**
			 * Default Options for Extension.
			 *
			 * @type {object}
			 */
			defaults = {
				auto: true
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
		 * Check method element type.
		 *
		 * The element that uses the extended jquery methods must be an input[type=file].
		 * Otherwise an exception is thrown.
		 *
		 * @param {object} $element jQuery selector for the element to be checked.
		 *
		 * @throws Exception when the element called is not a valid input[type=file].
		 *
		 * @private
		 */
		var _checkElementType = function ($element) {
			if (!$element.is('input[type=file]')) {
				throw '$.upload() method is supported only in input[type=file] elements.';
			}
		};

		/**
		 * Uploads selected file to server.
		 *
		 * This method uses the JavaScript File API that is supported from IE10+. If
		 * you need to support older browser just enable the auto-upload option and do
		 * not use this method.
		 *
		 * @param callback
		 */
		var _upload = function (callback) {
			// Trigger "validate" event for file upload element.
			var file = $this.get(0).files[0];
			if (!_validate(file) || !$this.trigger('validate', [file])) {
				return; // Do not continue as validation checks failed.
			}

			// Create a new instance of the plugin and upload the selected file.
			$this.fileupload({
				                 url: options.url,
				                 dataType: 'json'
			                 });

			$this.fileupload('send', {
					files: [file]
				})
				.success(function (result, textStatus, jqXHR, file) {
					jse.core.debug.info('AJAX File Upload Success Response:', result, textStatus);
					if (typeof callback === 'function') {
						callback(result);
					}
				})
				.error(function (jqXHR, textStatus, errorThrown) {
					jse.core.debug.error('AJAX File Upload Failure Response:', jqXHR, textStatus, errorThrown);
				})
				.complete(function (result, textStatus, jqXHR) {
					$this.fileupload('destroy'); // Not necessary anymore.
				});
		};

		/**
		 * Default Validation Rules
		 *
		 * This method will check for invalid filenames or exceeded file size (if necessary).
		 *
		 * @param {object} file Contains the information of the file to be uploaded.
		 */
		var _validate = function (file) {
			// @todo Implement default file validation.
			try {
				// Check if a file was selected.
				if (file === undefined) {
					throw 'No file was selected for upload.';
				}
				return true;
			} catch (exception) {
				jse.core.debug.error(exception);
				return false;
			}
		};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Initialize function of the extension, called by the engine.
		 */
		module.init = function (done) {
			// Check if upload script URL was provided (required value).
			if (options.url === undefined || options.url === '') {
				jse.core.debug.error('Upload URL was not provided for "ajax_file_upload" extension.');
				return;
			}

			if (options.auto === true) {
				$this.fileupload({
					                 'dataType': 'json',
					                 'url': options.url,
					                 done: function (event, data) {
						                 $(this).trigger('upload', [data.result]);
					                 }
				                 });
			} else {
				// Extend jQuery object with upload method for element.
				$.fn.extend({
					            upload: function (callback) {
						            _checkElementType($(this));
						            _upload(callback); // Trigger upload handler
					            },
					            validate: function () {
						            _checkElementType($(this));
						            return _validate(this.files[0]);
					            }
				            });
			}

			// Notify engine that the extension initialization is complete.
			done();
		};

		// Return data to module engine.
		return module;
	});
