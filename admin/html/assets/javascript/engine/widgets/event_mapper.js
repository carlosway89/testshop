/* --------------------------------------------------------------
 event_mapper.js 2015-09-17 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Event Mapper Widget
 *
 * Maps event from this element the target element.
 * Parameters provided as data attributes:
 * - target-element (Target element on which the event will be mapped)
 * - event-name (Name of the event, e.g.: 'click', 'hover')
 *
 * @module Admin/Widgets/event_mapper
 * @requires jQueryUI
 */
gx.widgets.module(
	'event_mapper',

	[],

	/** @lends module:Widgets/event_mapper */

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------

		var $this = $(this),
			options = $.extend(true, {}, data),
			module = {};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Initialize method of the widget, called by the engine.
		 */
		module.init = function (done) {
			$this
				.off(options.eventName)
				.on(options.eventName, function () {
					$(options.targetElement).trigger(options.eventName);
				});
			done();
		};

		// Return data to module engine.
		return module;
	});
