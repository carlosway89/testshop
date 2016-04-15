/* --------------------------------------------------------------
 products_upload_controller.js 2015-09-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Products Upload Module
 *
 * This module is reponsible for uploading multiple files at once.
 * In this case, its the ability to upload products images at once
 *
 * @module Compatibility/products_upload_controller
 * @requires jquery.fileupload
 */
gx.compatibility.module(
	// Module name
	'products_upload_controller',

	// Module dependencies
	[],

	/**  @lends module:Compatibility/products_upload_controller */

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------

		var $this = $(this);

		var $uploadList = $this.find('.list:first');
		var $addButton = $this.find('.product-image-uploader');
		var $template = $('#images-row-template');

		var currentCounter = !data.hasMopics ? 0 : data.lastId + 1;

		var module = {};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		var _add = function () {
			currentCounter++;

			var $markup = $(Mustache.render($template.html(), {
				id: currentCounter,
				idPlusOne: currentCounter + 1,
			}));

			$markup.hide();
			$markup
				.find('img.preview-image')
				.css({
					     opacity: 0.2
				     });

			$uploadList.append($markup);

			$markup
				.on('change', 'input:file', function () {
					var $image = $markup.find('img.preview-image');
					var $fileName = $markup.find('label.file-name');
					var $fileNameInput = $markup.find('[name*="gm_prd_img_name"]');

					var file = this.files[0];

					// Image preview
					var Reader = new FileReader();
					Reader.onload = function (event) {
						$image.attr('src', event.target.result);
					};
					Reader.readAsDataURL(file);

					// Shorten too long file name in markup
					if (file.name.length >= 40) {
						var fileNameText = file.name
							                   .substring(0, (40 - 3)) + '...';
						$fileName.text(fileNameText);
					} else {
						$fileName.text(file.name);
					}

					// Fade in addtional options
					var animationDuration = 250;
					$.each($markup.find('.hidden'), function (index, element) {
						$(element)
							.hide()
							.removeClass('hidden')
							.fadeIn(animationDuration);
						animationDuration += 250;
					});

					$image.animate({
						               opacity: 1
					               }, 500);

					$fileNameInput.val(file.name);
				});
			$markup.fadeIn();
			gx.widgets.init($markup.find('[name*=gm_gmotion_product_image]').parent());
		};

		var _remove = function (id) {
			currentCounter--;

			$uploadList
				.find('[data-id=' + id + ']')
				.remove();

			_recount();
		};

		var _recount = function () {
			$.each($uploadList.find('[data-id]'), function (index, element) {

				if (data.hasMopics) {
					index = data.lastId + index;
				}

				// Data ID
				$(element).attr('data-id', index);

				// Input label
				$(element)
					.find('[for*="mo_pics_"]:first')
					.attr('for', 'mo_pics_' + index);

				// File input
				$(element)
					.find('input:file')
					.attr('id', 'mo_pics_' + index)
					.attr('name', 'mo_pics_' + index);

				// File name
				$(element)
					.find('[name*="gm_prd_img_name_"]')
					.attr('name', 'gm_prd_img_name_' + index);

				// Alternative texts ID
				$.each(
					$(element).find('[name*="gm_alt_id"]'),
					function () {
						var langId = $(this).data('languageId');
						var newProp = [
							'gm_alt_id[', index, ']', '[', langId, ']'
						].join('');
						$(this).attr('name', newProp);
					}
				);
				// Alternative texts Value
				$.each(
					$(element).find('[name*="gm_alt_text"]'),
					function () {
						var langId = $(this).data('languageId');
						var newProp = [
							'gm_alt_text[', index, ']', '[', langId, ']'
						].join('');
						$(this).attr('name', newProp);
					}
				);
			});
		};

		var _onClick = function (event) {
			// Delete button
			if ($(event.target).is('.delete-button')) {
				var id = $(event.target).parents('[data-id]:first').data('id');
				_remove(id);
			}

			// Add button
			if ($(event.target).is($addButton)) {
				_add();
			}
		};

		module.init = function (done) {

			// Handle click event
			$this.bind('click', _onClick);

			// Register as finished
			done();
		};

		return module;
	});
