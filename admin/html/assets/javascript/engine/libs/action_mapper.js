/* --------------------------------------------------------------
 action_mapper.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.action_mapper = jse.libs.action_mapper || {};

/**
 * ## Action Mapper
 *
 * Binds maps a dropdown button action item event to another page element ($button). This library
 * must be used to quickly redirect user actions to existing but hidden UI elements like table row
 * actions. When a callback function is passed as an argument the action item will override the default
 * behaviour.
 *
 * You will need to provide the full URL in order to load this library as a dependency to a module:
 * e.g. jse.core.config.get('shopUrl') + '/admin/html/assets/javascript/engine/libs/action_mapper'
 *
 * #### Example
 *
 * ```html
 * <button id="button1">Button 1</button>
 * ```
 *
 * ```javascript
 * // Define a custom callback function.
 * function customCallbackFunc(event) {
 *     console.log('Function called!');
 * };
 *
 * // Map an event to a new dropdown action item.
 * var options = {
 *   $dropdown: $('#button-dropdown'), // A new action item will be created in this widget.
 *   $target: $('#target-button'),     // Target element will be triggered when the user clicks the dropdown action item.
 *   event: 'click',                   // Target event name to be triggered.
 *   callback: customCallbackFunc,     // (optional) Provide a function to override the default event handler.
 *   title: 'Action Title'             // (optional) Add a custom action title for the dropdown button.
 * }
 * jse.libs.action_mapper.bind(options);
 * ```
 *
 * By clicking on the "Button 1" you will receive a "Function called!" in the console!
 *
 * @namespace Admin/Libs/action_mapper
 */
(function (/** @lends Admin/Libs/action_mapper */ exports) {

	'use strict';

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
	 * Binds the event
	 *
	 * This method is the initializing point for all event bindings.
	 *
	 * @param {object} options Contains all elements, function and event description
	 * @param {string} options.$dropdown Selector for the button dropdown element (div).
	 * @param {string} [options.$target] (optional) Selector for the target element of the mapping.
	 * @param {string} options.event The name of the event. The event will be triggered on source and
	 * destination element (e.g. "click", "mouseleave").
	 * @param {function} [options.callback] (optional) Function that will be called when the event of the
	 * destination element is triggered. OVERWRITES THE ACTUAL EVENT FOR THE  DESTINATION ELEMENT.
	 * @param {string} title (optional) Provide an action title for the dropdown if no $target was defined.
	 *
	 * @public
	 */
	exports.bind = function (options) {
		_bindEvent(options);
	};

})(jse.libs.action_mapper);
