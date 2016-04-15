/* --------------------------------------------------------------
 ckeditor.js 2015-11-03 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## CKEditor Widgets
 *
 * Widget that starts the "ckeditor" on textareas with the "wysiwyg"-class.
 *
 * {@link http://ckeditor.com CKEditor Official Website}
 *
 * @module Admin/Widgets/ckeditor
 * @requires CKEditor
 */
gx.widgets.module(
	'ckeditor',
	
	[
		jse.core.config.get('shopUrl') + '/admin/html/assets/javascript/engine/libs/info_messages',
		jse.core.config.get('shopUrl') + '/admin/html/assets/javascript/engine/libs/button_dropdown',
		jse.core.config.get('shopUrl') + '/admin/html/assets/javascript/engine/libs/action_mapper'
	],

	/** @lends module:Widgets/ckeditor */

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
			defaults = { // Configuration gets passed to the ckeditor.
				'filebrowserBrowseUrl': jse.core.config.get('filemanager'),
				'baseHref': jse.core.config.get('shopUrl') + '/admin',
				'enterMode': CKEDITOR.ENTER_BR,
				'shiftEnterMode': CKEDITOR.ENTER_P,
				'language': jse.core.config.get('languageCode')
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
			module = {},

			/**
			 * Editors Selector Object
			 *
			 * @type {object}
			 */
			$editors = null;

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Initialize method of the widget, called by the engine.
		 */
		module.init = function (done) {
			$editors = $this
				.filter('.wysiwyg')
				.add($this.find('.wysiwyg'));

			$editors
				.each(function () {
					var $self = $(this),
						dataset = $.extend({}, options, $self.data()), // Get textarea specific configuration.
						name = $self.attr('name');
					$self.removeClass('wysiwyg');
					CKEDITOR.replace(name, dataset);
				});

			// Event handler for the update event, which is updating the ckeditor with the value
			// of the textarea.
			$this.on('ckeditor.update', function () {
				$editors
					.each(function () {
						var $self = $(this),
							name = $self.attr('name'),
							editor = (CKEDITOR) ? CKEDITOR.instances[name] : null;

						if (editor) {
							editor.setData($self.val());
						}
					});
			});

			$this.trigger('widget.initialized', 'ckeditor');

			done();
		};

		// Return data to module engine.
		return module;
	});
