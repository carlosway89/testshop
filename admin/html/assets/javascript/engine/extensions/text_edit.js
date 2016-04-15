/* --------------------------------------------------------------
 text_edit.js 2015-09-17 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Text Edit Extension
 *
 * @module Admin/Extensions/text_edit
 */
gx.extensions.module(
	'text_edit',

	['xhr', 'modal'],

	/** @lends module:Extensions/text_edit */

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
			module = {},

			/**
			 * Filter Selector
			 *
			 * @type {object}
			 */
			$filter = null;

		// ------------------------------------------------------------------------
		// FUNCTIONALITY
		// ------------------------------------------------------------------------

		/**
		 * Reset Form Event Handler
		 *
		 * @param {object} $parent
		 * @param {boolean} resetValue
		 */
		var _resetForm = function ($parent, resetValue) {
			var $textarea = $parent.find('textarea'),
				$buttons = $parent.find('ul.actions li'),
				original = $textarea.data('data');

			$textarea.prop('disabled', true);

			if (resetValue) {
				$textarea.val(original);
			}

			$buttons
				.hide()
				.filter('.edit')
				.show();

			if ($textarea.data('texteditEdited')) {
				$buttons
					.filter('.reset')
					.show();
			} else {
				$buttons
					.filter('.reset')
					.hide();
			}

		};

		/**
		 * Edit Event Handler
		 */
		var _editHandler = function () {
			var $self = $(this),
				$parent = $self.closest('.dataTableRow'),
				$textarea = $parent.find('textarea'),
				$buttons = $parent.find('ul.actions li'),
				value = $textarea.val();

			$textarea
				.data('data', value)
				.val('')
				.prop('disabled', false)
				.focus()
				.val(value);

			$self
				.hide()
				.siblings()
				.show();

			if ($textarea.data('texteditEdited')) {
				$buttons
					.filter('.reset')
					.show();
			} else {
				$buttons
					.filter('.reset')
					.hide();
			}
		};

		/**
		 * Abort Event Handler
		 */
		var _abortHandler = function () {
			var $self = $(this),
				$parent = $self.closest('.dataTableRow'),
				$textarea = $parent.find('textarea'),
				value = $textarea.val(),
				original = $textarea.data('data');

			if (value !== original) {
				jse.libs.modal.confirm({
					                       'content': jse.core.lang.translate('discard_changes_prompt', 'messages'),
					                       'title': jse.core.lang.translate('abort', 'buttons'),
					                       'position': {
						                       'my': 'center',
						                       'at': 'center',
						                       'of': $parent
					                       }
				                       }).done(function () {
					_resetForm($parent, true);
				});
			} else {
				_resetForm($parent);
			}
		};

		/**
		 * Save Event Handler
		 */
		var _saveHandler = function () {
			var $self = $(this),
				$parent = $self.closest('.dataTableRow'),
				$textarea = $parent.find('textarea'),
				value = $textarea.val(),
				original = $textarea.data('data'),
				data = module._data($textarea);

			data.value = value;
			if (!$self.hasClass('pending')) {
				if (value !== original) {
					$self.addClass('pending');

					jse.libs.xhr.ajax({
						                  'url': options.url,
						                  'data': data
					                  }).done(function (result) {
						$textarea.data('texteditEdited', result.edited);
						$parent.find('.searchSection').attr('title', result.source);
						_resetForm($parent);
					}).fail(function () {
						jse.libs.modal.error({
							                     'content': 'Error',
							                     'title': 'Error',
							                     'position': {
								                     'my': 'center',
								                     'at': 'center',
								                     'of': $parent
							                     }
						                     });
					}).always(function () {
						$self.removeClass('pending');
					});
				} else {
					_resetForm($parent);
				}
			}
		};

		/**
		 * Reset Event Handler
		 */
		var _resetHandler = function () {
			var $self = $(this),
				$parent = $self.closest('.dataTableRow'),
				$textarea = $parent.find('textarea');
			data = module._data($self);

			if (!$self.hasClass('pending')) {
				$self.addClass('pending');

				jse.libs.xhr.ajax({
					                  'url': options.url,
					                  'data': data
				                  }).done(function (result) {
					if (result.success) {
						$parent.find('.searchSection').attr('title', result.source);
						$textarea.val(result.value);
						$textarea.data('texteditEdited', false);
						_resetForm($parent);
						$self.hide();
					}
				}).fail(function () {
					jse.libs.modal.error({
						                     'content': 'Error',
						                     'title': 'Error',
						                     'position': {
							                     'my': 'center',
							                     'at': 'center',
							                     'of': $parent
						                     }
					                     });
				}).always(function () {
					$self.removeClass('pending');
				});
			}
		};

		/**
		 * Filter Event Handler
		 */
		var _filterHandler = function () {
			var $self = $(this),
				settings = module._data($(this));

			$filter.trigger('submitform', [settings]);
			window.scrollTo(0, 0);
		};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Init function of the extension, called by the engine.
		 */
		module.init = function (done) {
			$filter = $('#' + options.filter);

			$this
				.on('click', '.edit', _editHandler)
				.on('click', '.save', _saveHandler)
				.on('click', '.abort', _abortHandler)
				.on('click', '.reset', _resetHandler);

			if ($filter.length) {
				$this.on('click', '.searchPhrase, .searchSection', _filterHandler);
			}

			$('#needle').focus();

			done();
		};

		// Return data to module engine.
		return module;
	});
