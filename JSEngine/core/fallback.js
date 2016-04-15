/* --------------------------------------------------------------
 fallback.js 2015-10-16 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.fallback = jse.core.fallback || {};

/**
 * Fallback Library
 *
 * This library contains a set of deprecated functions that are still present for fallback
 * support. Each function will be removed from the engine after two minor releases.
 *
 * @namespace JSE/Core/fallback
 */
(function (/** @lends JSE/Core/fallback */exports) {

	'use strict';


	$(document).ready(function () {

		// Event listener that performs on every validate
		// validate trigger that isn't handled by the validator
		$('body').on('validator.validate', function (e, d) {
			if (d && d.deferred) {
				d.deferred.resolve();
			}
		});

		// Event listener that performs on every formchanges.check
		// trigger that isn't handled by the form_changes_checker
		$('body').on('formchanges.check', function (e, d) {
			if (d && d.deferred) {
				d.deferred.resolve();
			}
		});

		// Apply touch class to body
		// for touch-devices
		if (jse.core.config.get('hasTouch')) {
			$('body').addClass('has-touch');
		}
	});

	/**
	 * Add a deprecation warning in the console.
	 *
	 * As the JS engine evolves many old features will need to be changed in order to let a
	 * finer and clearer API for the JS Engine core mechanisms. Use this method to create a
	 * deprecation warning for the functions placed within this library.
	 *
	 * @param {string} functionName The deprecated function name.
	 * @param {string} deprecationVersion Deprecation version without the "v".
	 * @param {string} removalVersion Removal version withou the "v"
	 *
	 * @private
	 */
	var _deprecation = function (functionName, deprecationVersion, removalVersion) {
		jse.core.debug.warn('The "' + functionName + '" function is deprecated as of v' + deprecationVersion +
		                    ' and will be removed in v' + removalVersion);
	};

	/**
	 * Setup Widget Attribute
	 *
	 * @param {object} $element Change the widget attribute of an element.
	 *
	 * @deprecated since version 1.2.0 - will be removed in 1.4.0
	 *
	 * @public
	 */
	exports.setupWidgetAttr = function ($element) {
		_deprecation('setupWidgetAttr', '1.2.0', '1.4.0');

		$element
			.filter(':attr(^data-gx-_), :attr(^data-gambio-_)')
			.add($element.find(':attr(^data-gx-_), :attr(^data-gambio-_)'))
			.each(function () {
				var $self = $(this),
					attributes = $self[0].attributes,
					matchedAttribute,
					namespaceName;

				$.each(attributes, function (index, attribute) {
					if (attribute === undefined) {
						return true; // wrong attribute, continue loop
					}

					matchedAttribute = attribute.name.match(/data-(gambio|gx)-_.*/g);

					if (matchedAttribute !== null && matchedAttribute.length > 0) {
						namespaceName = matchedAttribute[0].match(/(gambio|gx)/g)[0];

						$self
							.attr(attribute.name.replace('data-' + namespaceName + '-_',
							                             'data-' + namespaceName + '-'), attribute.value);
					}
				});
			});
	};

	/**
	 * @deprecated since version 1.2.0 - will be removed in 1.4.0
	 * @param {object} data
	 * @param {object} $target
	 * @public
	 */
	exports.fill = function (data, $target) {
		_deprecation('fill', '1.2.0', '1.4.0');

		$.each(data, function (i, v) {
			var $elements = $target
				.find(v.selector)
				.add($target.filter(v.selector));

			$elements.each(function () {
				var $element = $(this);

				switch (v.type) {
					case 'html':
						$element.html(v.value);
						break;
					case 'attribute':
						$element.attr(v.key, v.value);
						break;
					case 'replace':
						if (v.value) {
							$element.replaceWith(v.value);
						} else {
							$element
								.addClass('hidden')
								.empty();
						}
						break;
					default:
						$element.text(v.value);
						break;
				}
			});

		});
	};

	/**
	 * @deprecated since version 1.2.0 - will be removed in 1.4.0
	 * @param url
	 * @param deep
	 * @returns {{}}
	 */
	exports.getUrlParams = function (url, deep) {
		_deprecation('getUrlParams', '1.2.0', '1.4.0');

		url = decodeURIComponent(url || location.href);

		var splitUrl = url.split('?'),
			splitParam = (splitUrl.length > 1) ? splitUrl[1].split('&') : [],
			regex = new RegExp(/\[(.*?)\]/g),
			result = {};

		$.each(splitParam, function (i, v) {
			var keyValue = v.split('='),
				regexResult = regex.exec(keyValue[0]),
				base = null,
				basename = keyValue[0].substring(0, keyValue[0].search('\\[')),
				keys = [],
				lastKey = null;

			if (!deep || regexResult === null) {
				result[keyValue[0]] = keyValue[1].split('#')[0];
			} else {

				result[basename] = result[basename] || [];
				base = result[basename];

				do {
					keys.push(regexResult[1]);
					regexResult = regex.exec(keyValue[0]);
				} while (regexResult !== null);

				$.each(keys, function (i, v) {
					var next = keys[i + 1];
					v = v || '0';

					if (typeof (next) === 'string') {
						base[v] = base[v] || [];
						base = base[v];
					} else {
						base[v] = base[v] || undefined;
						lastKey = v;
					}
				});

				if (lastKey !== null) {
					base[lastKey] = keyValue[1];
				} else {
					base = keyValue[1];
				}
			}

		});

		return result;
	};

	/**
	 * Fallback getData method.
	 *
	 * This method was included in v1.0 of JS Engine and is replaced by the
	 * "jse.libs.form.getData" method.
	 *
	 * @deprecated since version 1.2.0 - will be removed in 1.4.0
	 *
	 * @param {object} $form Selector of the form to be parsed.
	 * @param {string} ignore (optional) jQuery selector string of form elements to be ignored.
	 *
	 * @returns {object} Returns the data of the form as an object.
	 */
	exports.getData = function ($form, ignore) {
		var $elements = $form.find('input, textarea, select'),
			result = {};

		if (ignore) {
			$elements = $elements.filter(':not(' + ignore + ')');
		}

		$elements.each(function () {
			var $self = $(this),
				type = $self.prop('tagName').toLowerCase(),
				name = $self.attr('name'),
				$selected = null;

			type = (type !== 'input') ? type : $self.attr('type').toLowerCase();

			switch (type) {
				case 'radio':
					$form
						.find('input[name="' + name + '"]:checked')
						.val();
					break;
				case 'checkbox':
					if (name.search('\\[') !== -1) {
						if ($self.prop('checked')) {
							name = name.substring(0, name.search('\\['));
							if (typeof result[name] === 'undefined') {
								result[name] = [];
							}
							result[name].push($(this).val());
						}
					} else {
						result[name] = $self.prop('checked');
					}
					break;
				case 'select':
					$selected = $self.find(':selected');
					if ($selected.length > 1) {
						result[name] = [];
						$selected.each(function () {
							result[name].push($(this).val());
						});
					} else {
						result[name] = $selected.val();
					}
					break;
				case 'button':
					break;
				default:
					if (name) {
						result[name] = $self.val();
					}
					break;
			}
		});
		return result;
	};

})(jse.core.fallback);
