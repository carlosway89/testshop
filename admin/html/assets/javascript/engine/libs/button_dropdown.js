/* --------------------------------------------------------------
 button_dropdown.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.button_dropdown = jse.libs.button_dropdown || {};

/**
 * Button Dropdown Library
 *
 * This library contains helper function that make the manipulation of a button dropdown
 * widget easier.
 *
 * You will need to provide the full URL in order to load this library as a dependency to a module:
 * e.g. jse.core.config.get('shopUrl') + '/admin/html/assets/javascript/engine/libs/button_dropdown'
 *
 * ```javascript
 * var $buttonDropdown = $('#my.js-button-dropdown');
 *
 * // Map an action to a dropdown item.
 * jse.libs.button_dropdown.mapAction($buttonDropdown, action, section, callback, $targetRecentButton);
 *
 * // Change recent button.
 * jse.libs.button_dropdown.changeDefualtButton($buttonDropdown, text, callback, $targetRecentButton);
 *
 * // Add a separator in a dropdown list.
 * jse.libs.button_dropdown.addDropdownSeperator($buttonDropdown);
 * ```
 *
 * @todo Further improve the code and the comments of this library.
 *
 * @namespace Admin/Libs/button_dropdown
 */
(function (/** @lends Admin/Libs/button_dropdown */ exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// PRIVATE METHODS
	// ------------------------------------------------------------------------

	/**
	 * Trigger a specific event from an element.
	 *
	 * Some situations require a different approach than just using the "trigger" method.
	 *
	 * @param {object} $element Destination element to be triggered.
	 * @param {object} event Event options can be used for creating new conditions.
	 *
	 * @private
	 */
	var _triggerEvent = function ($element, event) {
		if ($element.prop('tagName') === 'A' && event.type === 'click') {
			$element.get(0).click();
		} else {
			$element.trigger(event.type);
		}
	};

	/**
	 * Bind the event to a new dropdown action item.
	 *
	 * @param options See bind documentation.
	 *
	 * @private
	 */
	var _bindEvent = function (options) {
		var $dropdown = options.$dropdown,
			action = options.action,
			$target = options.$target,
			eventName = options.event,
			callback = options.callback || false,
			title = options.title || (options.$target.length ? options.$target.text() : '<No Action Title Provided>'),
			$li = $('<li></li>');

		$li.html('<span data-value="' + action + '">' + title + '</span>');
		$dropdown.find('ul').append($li);

		$li.find('span').on(eventName, function (event) {
			if (callback !== false) {
				//event.preventDefault();
				//event.stopPropagation();
				callback.call($li.find('span'), event);
			} else {
				_triggerEvent($target, event);
			}
		});
	};

	/**
	 * Initialize default button.
	 *
	 * @param {object} $dropdown The affected button dropdown selector.
	 * @param {object} configValue Configuration value that comes from the UserConfigurationService.
	 * @param {object} title The caption of the default action button.
	 * @param {object} callback (optional) Callback function for the new action.
	 * @param {object} $targetDefaultButton (optional) Selector for the default button.
	 *
	 * @private
	 */
	var _initDefaultAction = function ($dropdown, configValue, title, callback, $targetDefaultButton) {
		var interval = setInterval(function () {
			if (typeof $dropdown.attr('data-configuration_value') !== 'undefined') {
				// Sets the recent action button loaded from database.
				if ($dropdown.attr('data-configuration_value') === configValue) {
					exports.changeDefaultAction($dropdown, title, callback, $targetDefaultButton);
				}

				clearInterval(interval);
			}
		}, 300);
	};

	// ------------------------------------------------------------------------
	// PUBLIC METHODS
	// ------------------------------------------------------------------------

	/**
	 * Adds a new item to the dropdown.
	 *
	 * @param {string} translationPhrase Translation phrase key.
	 * @param {string} translationSection Translation section of the phrase.
	 * @param {function} customCallback Define a custom callback.
	 * @param {object} $targetDefaultButton (optional) A custom selector which dropdown buttons should be changed.
	 *
	 * @public
	 */
	exports.mapAction = function ($dropdown, translationPhrase, translationSection, customCallback, $targetDefaultButton) {
		var $target = $targetDefaultButton || $dropdown,
			title = (translationSection !== '')
				? jse.core.lang.translate(translationPhrase, translationSection)
				: translationPhrase;

		// Sets the first action as recent action button, if no recent action has benn set so far.
		if (!$dropdown.find('ul li').length && $dropdown.find('button:first').text().trim() === '') {
			exports.changeDefaultAction($dropdown, title, customCallback, $target);
		}

		_initDefaultAction($dropdown, translationPhrase, title, customCallback, $target);

		var options = {
			action: translationPhrase,
			$dropdown: $dropdown,
			title: title,
			event: 'perform:action',
			callback: function (event) {
				customCallback(event);
				exports.changeDefaultAction($(this), title, customCallback, $target);
			}
		};

		_bindEvent(options);
	};

	/**
	 * Adds a separator to the dropdown list.
	 *
	 * The separator will be added at the end of the list.
	 *
	 * @param {object} $dropdown
	 *
	 * @public
	 */
	exports.addSeparator = function ($dropdown) {
		$dropdown
			.find('ul')
			.append('<li><hr></li>');
	};

	/**
	 * Changes the default action of the button.
	 *
	 * @param {object} $button The affected button dropdown widget.
	 * @param {string} title Text of the new button.
	 * @param {string} callback The callback
	 * @param {object} $targetDefaultButton A custom element for which button should be changed.
	 *
	 * @public
	 */
	exports.changeDefaultAction = function ($dropdown, title, callback, $targetDefaultButton) {
		var $target = $targetDefaultButton || $dropdown,
			icon = $target.data('icon');

		if (title.length) {
			$target
				.find('button:first')
				.off('perform:action')
				.on('perform:action', callback);
		}

		$target
			.find('button:first')
			.text(title);

		$target
			.find('button:first')
			.prop('title', title.trim());

		if (typeof icon !== 'undefined') {
			$target
				.find('button:first')
				.prepend($('<i class="fa fa-' + icon + ' btn-icon"></i>'));
		}
	};

})(jse.libs.button_dropdown);
