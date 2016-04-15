/* --------------------------------------------------------------
 modal.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals Mustache */

jse.libs.modal = jse.libs.modal || {};

/**
 * ## Modal Library
 *
 * Library that handles modal dialogs within the app. This function depends on jQuery & jQuery UI
 * dialog widget. You are able to select the HTML template to be displayed as a modal and set other
 * parameters to readjust the modal behavior. If no template option is provided the library will search
 * for a default "#modal_alert" element. Place the markup from the following example into your page
 * to quickly display messages to the user.
 *
 * #### Quick Usage Example (No Configuration)
 *
 * ```javascript
 * Modal.message({
 *      title: 'My Title',      // Required
 *      content: 'My Content'   // Required
 *      buttons: { ... }        // Optional
 *      // Other jQueryUI Dialog Widget Options
 * });
 * ```
 *
 * #### Example With Default HTML
 * ```html
 * <!--
 *      HTML
 *      Insert the following HTML into your page.
 * -->
 * <div id="modal_alert">
 *  <div>
 *      {{#content}}
 *      <div class="icon">&nbsp;</div>
 *      <p>{{{.}}}</p>
 *      {{/content}}
 *  </div>
 * </div>
 *
 * <!--
 *      JavaScript
 *      If you don't specify a template the library will search for the "#modal_alert" element.
 * -->
 * <script>
 *      Modal.alert({
 *          title: 'My Modal Title',
 *          content: 'My modal content.',
 *          position: { my: 'center', at: 'center', of: $('#parent-element-id') }
 *      });
 * </script>
 * ```
 *
 * @todo Refactor the file and remove the methods that are not needed (like alert).
 *
 * @namespace JSE/Libs/modal
 */
(function (exports) {

	'use strict';

	var
		/**
		 * Body Element Selector
		 *
		 * @type {object}
		 */
		$body = $('body'),

		/**
		 * Contains Default Modal Buttons
		 *
		 * @type {object}
		 */
		buttons = {
			'yes': {
				'name': jse.core.lang.translate('yes', 'buttons'),
				'type': 'success'
			},
			'no': {
				'name':  jse.core.lang.translate('no', 'buttons'),
				'type': 'fail'
			},
			'abort': {
				'name':  jse.core.lang.translate('abort', 'buttons'),
				'type': 'fail'
			},
			'ok': {
				'name':  jse.core.lang.translate('ok', 'buttons'),
				'type': 'success'
			},
			'close': {
				'name':  jse.core.lang.translate('close', 'buttons'),
				'type': 'fail'
			}
		};

	/**
	 * Get Form Data
	 *
	 * Function to get all form data stored inside the layer.
	 *
	 * @name Core/Modal._getFormData
	 * @private
	 * @method
	 *
	 * @param {object} $self jQuery selector of the layer.
	 * @param {bool} validateForm Flag that determines whether the form must be validated
	 * before we get the data.
	 *
	 * @return {json} Returns a JSON with all form data.
	 */
	var _getFormData = function ($self, validateForm) {
		var $forms = $self
				.filter('form')
				.add($self.find('form')),
			formData = {},
			promises = [];

		if ($forms.length) {
			$forms.each(function () {
				var $form = $(this);

				if (validateForm) {
					var localDeferred = $.Deferred();
					promises.push(localDeferred);
					$form.trigger('validator.validate', {
						'deferred': localDeferred
					});
				}

				var key = $form.attr('name') || $form.attr('id') || ('form_' + new Date().getTime() * Math.random());
				formData[key] = window.jse.lib.form.getData($form);
			});
		}

		return $.when
			.apply(undefined, promises)
			.then(function (result) {
				      return formData;
			      },
		          function (result) {
			          return formData;
		          })
			.promise();
	};

	/**
	 * Reject Handler
	 *
	 * @name Core/Modal._rejectHandler
	 * @private
	 * @method
	 *
	 * @param {object} $element Selector element.
	 * @param {object} deferred Deferred object.
	 */
	var _rejectHandler = function ($element, deferred) {
		_getFormData($element).always(function (result) {
			deferred.reject(result);
			$element
				.dialog('close')
				.remove();
		});
	};

	/**
	 * Resolve Handler
	 *
	 * @name Core/Modal._resolveHandler
	 * @private
	 * @method
	 *
	 * @param {object} $element Selector element.
	 * @param {object} deferred Deferred object.
	 */
	var _resolveHandler = function ($element, deferred) {
		_getFormData($element, true).done(function (result) {
			deferred.resolve(result);
			$element
				.dialog('close')
				.remove();
		});
	};

	/**
	 * Generate Buttons
	 *
	 * Function to transform the custom buttons object (which is incompatible with jQuery UI)
	 * to a jQuery UI compatible format.
	 *
	 * @name Core/Modal._generateButtons
	 * @private
	 * @method
	 *
	 * @param {object} dataset Custom buttons object for the dialog.
	 * @param {object} deferred Deferred-object to resolve/reject on close.
	 *
	 * @return {array} Returns a jQuery UI dialog compatible buttons array.
	 */
	var _generateButtons = function (dataset, deferred) {
		var newButtons = [],
			tmpButton = null;

		// Check if buttons are available.
		if (dataset) {
			$.each(dataset, function (k, v) {

				// Setup a new button.
				tmpButton = {};
				tmpButton.text = v.name || 'BUTTON';

				// Setup click handler.
				tmpButton.click = function () {
					var $self = $(this);

					// If a callback is given, execute it with the current scope.
					if (typeof v.callback === 'function') {
						v.callback.apply($self, []);
					}

					// Add the default behaviour for the close  functionality. On fail,
					// reject the deferred object, else resolve it.
					switch (v.type) {
						case 'fail':
							_rejectHandler($self, deferred);
							break;
						case 'success':
							_resolveHandler($self, deferred);
							break;
						default:
							break;
					}
				};

				// Add to the new buttons array.
				newButtons.push(tmpButton);
			});

		}

		return newButtons;
	};

	/**
	 * Get Template
	 *
	 * This method will return a promise object that can be used to execute code
	 * when once the template HTML of the modal is found. If
	 *
	 * @name Core/Modal._getTemplate
	 * @private
	 * @method
	 *
	 * @param {object} options Options to be applied to the template.
	 * @return {object} Returns a deferred object.
	 */
	var _getTemplate = function (options) {
		var $selection = [],
			deferred = $.Deferred();

		try {
			$selection = $(options.template);
		} catch (exception) {
			jse.core.debug(jse.core.lang.templateNotFound(options.template));
		}

		if ($selection.length) {
			deferred.resolve($selection.html());
		} else {
			window.jse.lib.ajax({
				'url': options.template,
				'dataType': 'html'
			}).done(function (result) {
				if (options.storeTemplate) {
					var $append = $('<div />')
						.attr('id', options.template)
						.html(result);
					$body.append($append);
				}
				deferred.resolve(result);
			}).fail(function () {
				deferred.reject();
			});
		}

		return deferred;
	};

	/**
	 * Create Modal Layer
	 *
	 * @name Core/Modal._createLayer
	 * @public
	 * @method
	 *
	 * @param {object} options Extra modal options to be applied to the
	 * @param {string} title Modal title
	 * @param {string} className Class name to be added to the modal element.
	 * @param {object} defaultButtons Modal buttons for the layer.
	 * @param {string} template Template name to be used for the modal.
	 * @return {object} Returns modal promise object.
	 */
	var _createLayer = function (options, title, className, defaultButtons, template) {
		// Setup defaults & deferred objects.
		var deferred = $.Deferred(),
			promise = deferred.promise(),
			$template = '',
			defaults = {
				'title': title || '',
				'dialogClass': className || '',
				'modal': true,
				'resizable': false,
				'buttons': defaultButtons || [buttons.close],
				'draggable': false,
				'closeOnEscape': false,
				'autoOpen': false,
				'template': template || '#modal_alert',
				'storeTemplate': false,
				'closeX': true,
				'modalClose': false
			},
			instance = null,
			$forms = null;

		// Merge custom settings with default settings
		options = options || {};
		options = $.extend({}, defaults, options);
		options.buttons = _generateButtons(options.buttons, deferred);

		_getTemplate(options).done(function (html) {
			// Generate template
			$template = $(Mustache.render(html, options));

			if (options.validator) {
				$template
					.find('form')
					.attr('data-gx-widget', 'validator')
					.find('input')
					.attr({
						'data-validator-validate': options.validator.validate,
						'data-validator-regex': options.validator.regex || ''
					})
					.addClass('validate');
			}

			// Setup dialog
			$template.dialog(options);
			try {
				instance = $template.dialog('instance');
			} catch (exception) {
				instance = $template.data('ui-dialog');
			}

			// Add bootstrap button classes to buttonSet.
			instance
				.uiButtonSet
				.children()
				.addClass('btn btn-default');

			// If the closeX-option is set to false, remove the button from the layout
			// else bind an event listener to reject the deferred object.
			if (options.closeX === false) {
				instance
					.uiDialogTitlebarClose
					.remove();
			} else {
				instance
					.uiDialogTitlebarClose
					.html('&times;')
					.one('click', function () {
						     _rejectHandler(instance.element, deferred);
					     });
			}

			// Add an event listener to the modal overlay if the option is set.
			if (options.modalClose) {
				$('body')
					.find('.ui-widget-overlay')
					.last()
					.one('click', function () {
						     _rejectHandler(instance.element, deferred);
					     });
			}

			// Prevent submit on enter in inner forms
			$forms = instance.element.find('form');
			if ($forms.length) {
				$forms.on('submit', function (event) {
					event.preventDefault();
				});
			}

			if (options.executeCode && typeof options.executeCode === 'function') {
				options.executeCode.call($(instance.element));
			}

			// Add a close layer method to the promise.
			// @todo Test that ...
			promise.close = function (fail) {
				if (fail) {
					_rejectHandler(instance.element, deferred);
				} else {
					_resolveHandler(instance.element, deferred);
				}
			};

			$template.dialog('open');
			if (window.gx && window.jse.widgets && window.jse.widgets.init) {
				window.jse.widgets.init($template);
				window.jse.controllers.init($template);
				window.jse.extensions.init($template);
			}
		}).fail(function () {
			deferred.reject({
				'error': 'Template not found'
			});
		});

		return promise;
	};

	/**
	 * Function to generate default alert layer.
	 *
	 * @name Core/Modal.alert
	 * @public
	 * @method
	 *
	 * @param {object} options Mix of jQuery UI dialog options and custom options
	 * @param {string} title Default title for the type of alert layer
	 * @param {string} className Default class for the type of alert layer
	 * @param {array} defbuttons Array wih the default buttons for the array type
	 * @param {string} template Selector for the jQuery-object used as template
	 *
	 * @return {object} Returns a promise object.
	 */
	var _alert = function (options) {
		var data = $.extend({}, {
			'draggable': true
		}, options);
		return _createLayer(data, jse.core.lang.translate('hint', 'labels'), '', [buttons.ok]);
	};

	/**
	 * Shortcut function for an confirm-layer.
	 *
	 * @name Core/Modal.confirm
	 * @public
	 * @method
	 *
	 * @param {object} options Mix of jQuery UI dialog options and custom options.
	 *
	 * @return {promise} Returns a promise
	 */
	var _confirm = function (options) {
		var data = $.extend({}, {
			'draggable': true
		}, options);
		return _createLayer(data, jse.core.lang.translate('confirm', 'labels'), 'confirm_dialog', [buttons.no, buttons.yes]);
	};

	/**
	 * Shortcut function for a prompt-layer.
	 *
	 * @name Core/Modal.prompt
	 * @public
	 * @method
	 *
	 * @param {object} options Mix of jQuery UI dialog options and custom options.
	 *
	 * @return {promise} Returns a promise object.
	 */
	var _prompt = function (options) {
		var data = $.extend({}, {
			'draggable': true
		}, options);
		return _createLayer(data, jse.core.lang.translate('prompt', 'labels'), 'prompt_dialog', [buttons.abort, buttons.ok],
		                    '#modal_prompt');
	};

	/**
	 * Shortcut function for an success-layer.
	 *
	 * @name Core/Modal.success
	 * @public
	 * @method
	 *
	 * @param {object} options Mix of jQuery UI dialog options and custom options.
	 *
	 * @return {object} Returns a promise object.
	 */
	var _success = function (options) {
		var data = $.extend({}, {
			'draggable': true
		}, options);
		return _createLayer(data, jse.core.lang.translate('success', 'labels'), 'success_dialog');
	};

	/**
	 * Shortcut function for an error-layer.
	 *
	 * @name Core/Modal.error
	 * @public
	 * @method
	 *
	 * @param {object} options Mix of jQuery UI dialog options and custom options.
	 *
	 * @return {object} Returns a promise object.
	 */
	var _error = function (options) {
		var data = $.extend({}, {
			'draggable': true
		}, options);
		return _createLayer(data, jse.core.lang.translate('error', 'labels'), 'error_dialog');
	};

	/**
	 * Shortcut function for a warning-layer.
	 *
	 * @name Core/Modal.warn
	 * @public
	 * @method
	 *
	 * @param {object} options Mix of jQuery UI dialog options and custom options.
	 *
	 * @return {object} Returns a promise object.
	 */
	var _warn = function (options) {
		var data = $.extend({}, {
			'draggable': true
		}, options);
		return _createLayer(data, jse.core.lang.translate('warning', 'labels'), 'warn_dialog');
	};

	/**
	 * Shortcut function for an info-layer.
	 *
	 * @name Core/Modal.info
	 * @public
	 * @method
	 *
	 * @param {object} options Mix of jQuery UI dialog options and custom options.
	 *
	 * @return {promise} Returns a promise object.
	 */
	var _info = function (options) {
		var data = $.extend({}, {
			'draggable': true
		}, options);
		return _createLayer(data, jse.core.lang.translate('info', 'labels'), 'info_dialog');
	};

	/**
	 * ## Quickly display message without settings up view files or JavaScript code.
	 *
	 * This method provides an easy way to display a message to the user without
	 * having to worry about templates and other dependencies of the library.
	 *
	 * @name Core/Modal.message
	 * @public
	 * @method
	 *
	 * @param {object} options Modal options are the same as the jQuery dialog widget.
	 */
	var _message = function (options) {
		// Create div element for modal dialog.
		$('body').append('<div class="modal-layer">' + options.content + '</div>');

		// Append options object with extra dialog options.
		options.modal = true;
		options.dialogClass = 'gx-container';

		// Set default buttons, if option wasn't provided.
		if (options.buttons === undefined) {
			options.buttons = [
				{
					text: buttons.close.name,
					click: function () {
						$(this).dialog('close');
						$(this).remove();
					}
				}
			];
		}

		// Display message to the user.
		$('.modal-layer:last').dialog(options);
	};

	// ------------------------------------------------------------------------
	// VARIABLE EXPORT
	// ------------------------------------------------------------------------

	exports.error = _error;
	exports.warn = _warn;
	exports.info = _info;
	exports.success = _success;
	exports.alert = _alert;
	exports.prompt = _prompt;
	exports.confirm = _confirm;
	exports.custom = _createLayer;
	exports.message = _message;

}(jse.libs.modal));
