/* --------------------------------------------------------------
 products_image_controller.js 2015-09-10 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Products Image Module
 *
 * This module handles the controls for the products images
 *
 * @module Compatibility/products_image_controller
 * @requires jquery.fileupload
 */
gx.compatibility.module(
	// Module name
	'products_image_controller',

	// Module dependencies
	['image_resizer'],

	/**  @lends module:Compatibility/products_image_controller */

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------

		// Element: Module selector
		var $this = $(this);

		// Image Resizer Library Reference
		var resize = window.gx.libs.image_resizer.resize;

		// Meta object
		var module = {};

		// ------------------------------------------------------------------------
		// PRIVATE METHODS
		// ------------------------------------------------------------------------

		// Handles primary product image controls
		var _initPrimaryImage = function () {
			// Elements definition
			var $input = $this.find('input:file');
			var $image = $this.find('img.preview-image');
			var $fileNameContainer = $this.find('label.file-name');
			var $visibilityElements = $this.find('.js-toggle-visibility');
			var $deleteCheckbox = $this.find('.js-delete-checkbox');

			// Hide controls if no image is there
			if (!data.hasPrimaryImage) {
				$visibilityElements.hide();
				$image.hide();
			}

			// Show picture and fade in controls when a picture is selected
			// Read image file and output it
			// as dataURL scheme for preview
			$input.on('change', function (event) {
				var file = event.target.files[0];
				var Reader = new FileReader();
				Reader.onload = function (event) {
					$image.show();
					$image.attr('src', event.target.result);
					resize($image);
				};
				Reader.readAsDataURL(file);
				$this.find('input[name*="gm_alt_text"]').val('');
				if ($visibilityElements.is(':hidden')) {
					$visibilityElements.fadeIn();
					if (!data.hasPrimaryImage) {
						$deleteCheckbox.hide();
					}
				}
				$this.find('input[name="gm_prd_img_name"]').val(file.name);
				$fileNameContainer.text(file.name);
			});
		};

		// Handles mo pics controls
		var _initMoPic = function () {
			var $input = $this.find('input:file');
			var $image = $this.find('img.preview-image');
			var $fileNameContainer = $this.find('label.file-name');

			$input.on('change', function (event) {
				var file = event.target.files[0];
				var Reader = new FileReader();
				Reader.onload = function (event) {
					$image.attr('src', event.target.result);
					resize($image);
				};
				Reader.readAsDataURL(file);
				$this.find('input[name*="gm_alt_text"]').val('');
				$this.find('input[name="gm_prd_img_name"]').val(file.name);
				$fileNameContainer.text(file.name);
			});
		};

		// Initializes image hander
		var _initialize = function () {
			// Check if this container is the primary image container
			if ($this.is('.primary-image')) {
				_initPrimaryImage();
			} else {
				_initMoPic();
			}
		};

		module.init = function (done) {
			// Initialize
			_initialize();

			// Register as finished
			done();
		};

		return module;
	});
