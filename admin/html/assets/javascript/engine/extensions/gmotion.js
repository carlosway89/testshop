/* --------------------------------------------------------------
 gmotion.js 2015-10-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## G-Motion Control Module
 *
 * This extension allows you to make use of
 * G-Motion controls for a product image.
 *
 * Each product picture has a G-Motion control section,
 * where the user is able to change G-Motion settings
 * for the respective picture.
 * This extension is responsible for showing the G-Motion options under
 * each picture and to change the values of position coordinates.
 *
 * @module Admin/Extensions/gmotion
 */
gx.extensions.module(
	'gmotion',

	[],

	/** @lends module:Extensions/gmotion */

	function (data) {

		'use strict';

		// =====================================================================
		// VARIABLE DEFINITIONS
		// =====================================================================

		/**
		 * @description Engine Meta object.
		 * @type {object}
		 */
		var module = {};

		/**
		 * @description Containing element's selector.
		 * @type {jQuery}
		 */
		var $this = $(this);

		// ELEMENT SELECTORS
		// =================

		// GENERAL ACTIVATOR
		// =================

		/**
		 * @description Element: General product-specific G-Motion activation checkbox.
		 * @type {jQuery}
		 */
		var $activator = $('input#gm_gmotion_activate');

		// G-MOTION SPECIFIC SETTINGS PER IMAGE
		// ====================================

		/**
		 * @description Element: G-Motion checkbox and control panel container.
		 * @type {jQuery}
		 */
		var $settingsContainers = $this.find('.gmotion-setting');

		/**
		 * @description Element: Use G-Motion checkbox.
		 * @type {jQuery}
		 */
		var $checkbox = $this.find('.gm_gmotion_image:first');

		/**
		 * @description Element: G-Motion settings controller panel.
		 * @type {jQuery}
		 */
		var $panel = $this.find('.js-gmotion-panel:first');

		// DRAGGER ICONS
		// =============

		/**
		 * @description Element: Start position dragger.
		 * @type {jQuery}
		 */
		var $startDragger = $this.find('.gm_gmotion_start:first');

		/**
		 * @description Element: End position dragger.
		 * @type {jQuery}
		 */
		var $endDragger = $this.find('.gm_gmotion_end:first');

		// IMAGE
		// =====

		/**
		 * @description Element: G-Motion playground image.
		 * @type {jQuery}
		 */
		var $image = $this.find('.js-gmotion-image:first');

		// INPUT FIELDS
		// ============

		/**
		 * @description Element: Start percentage value input field.
		 * @type {jQuery}
		 */
		var $startInput = $panel.find('[name^="gm_gmotion_position_from_"]');

		/**
		 * @description Element: End percentage value input field.
		 * @type {jQuery}
		 */
		var $endInput = $panel.find('[name^="gm_gmotion_position_to_"]');

		/**
		 * @description Element: Start zoom select field.
		 * @type {jQuery}
		 */
		var $zoomStartInput = $panel.find('[name^="gm_gmotion_zoom_from_"]');

		/**
		 * @description Element: End zoom select field.
		 * @type {jQuery}
		 */
		var $zoomEndInput = $panel.find('[name^="gm_gmotion_zoom_to_"]');

		/**
		 * @description Element: Animation duration in seconds input field.
		 * @type {jQuery}
		 */
		var $animationDurationInput = $panel.find('[name^="gm_gmotion_duration_"]');

		/**
		 * @description Element: Sorting input field.
		 * @type {jQuery}
		 */
		var $sortingInput = $panel.find('[name^="gm_gmotion_sort_order_"]');


		// DEFAULT VALUES
		// ==============

		/**
		 * @description Start horizontal swing animation position default value.
		 * @type {number}
		 */
		var defaultAnimationStartLeft = 0;

		/**
		 * @description Start vertical swing animation position default value.
		 * @type {number}
		 */
		var defaultAnimationStartTop = 50;

		/**
		 * @description End horizontal swing animation position default value.
		 * @type {number}
		 */
		var defaultAnimationEndLeft = 100;

		/**
		 * @description End vertical swing animation position default value.
		 * @type {number}
		 */
		var defaultAnimationEndTop = 50;

		/**
		 * @description Zoom factor start default value.
		 * @type {number}
		 */
		var defaultZoomStart = 1;

		/**
		 * @description Zoom factor end default value.
		 * @type {number}
		 */
		var defaultZoomEnd = 1;

		/**
		 * @description Animation duration default value.
		 * @type {number}
		 */
		var defaultAnimationDuration = 10;

		// DRAGGER ICON VALUES
		// ===================

		/**
		 * @description Dragger icon width.
		 * @type {number}
		 */
		var draggerWidth = 12;

		/**
		 * @description Dragger icon height.
		 * @type {number}
		 */
		var draggerHeight = 14;

		// =====================================================================
		// METHODS
		// =====================================================================

		// INPUT VALUE DATA PROCESSING
		// ===========================

		/**
		 * @description Refreshes values in the appropriate input field.
		 * @param {jQuery|HTMLElement} inputField The field which should be updated.
		 * @param {number} leftPosition Horizontal position percentage.
		 * @param {number} topPosition Vertical position percentage.
		 * @private
		 */
		var _refreshInputValues = function (inputField, leftPosition, topPosition) {

			var $input = $startDragger.is(inputField) ? $startInput : $endInput;

			var value = [
				leftPosition + '%',
				topPosition + '%'
			].join(' ');

			$input.val(value);
		};

		/**
		 * @description Fetches percent values from input field and updates
		 * the position of the respective dragger element.
		 * Aborts on abnormal values.
		 * @param {jQuery|HTMLElement} inputElement Input element.
		 * @private
		 */
		var _updateDraggerFromInput = function (inputElement) {
			// Input value.
			var value = _extractValues($(inputElement).val());

			// Return immediately on abnormal values.
			var falseValues = (
				// No values extracted.
				value === null ||

				// Left values exceeds maximum.
				value[0] > 100 ||

				// Top value exceed
				value[1] > 100
			);
			if (falseValues) {
				return;
			}

			// Position container with values in pixel.
			var positionInPixel = _convertPercentToPixel(value[0], value[1]);

			// Assign appropriate dragger element.
			var $draggerToMove = $startInput.is(inputElement) ?
			                     $startDragger : $endDragger;

			// Reposition dragger element to new position values.
			_setDraggerPosition($draggerToMove, positionInPixel);
		};

		// DRAGGER PROCESSING
		// ==================

		/**
		 * @description Draws the draggable handler for coordinating
		 * swing start and end positions. Uses jQueryUI to handle dragging.
		 * If no values are set, the default position values will be set.
		 * @see jQueryUI 'draggable' API documentation.
		 * @requires jQueryUI
		 * @private
		 */
		var _initializeDraggers = function () {
			var options = {
				containment: $image,
				drag: function () {
					var percentage = _convertPixelToPercent(
						$(this).css('left').replace('px', ''),
						$(this).css('top').replace('px', '')
					);
					_refreshInputValues(this, percentage.left, percentage.top);
				}
			};

			$startDragger.draggable(options);
			$endDragger.draggable(options);
		};

		/**
		 * @description Sets the position of a dragger.
		 * @param {jQuery|HTMLElement} element Dragger element.
		 * @param {object} position Positions to set.
		 * @param {number} position.left Horizontal position.
		 * @param {number} position.top Vertical position.
		 * @private
		 */
		var _setDraggerPosition = function (element, position) {
			$(element).css(position);
		};

		// VALUES CONVERSION METHODS
		// =========================

		/**
		 * @description Converts pixel values to the relative percent values.
		 * Note: Dimensions of $startDragger is used for calculation,
		 * which does not affect the end result,
		 * as both draggers have the same dimensions.
		 * @param {number} leftPosition
		 * @param {number} topPosition
		 * @returns {object}
		 * @private
		 */
		var _convertPixelToPercent = function (leftPosition, topPosition) {
			// Result object, which will be returned.
			var result = {
				left: null,
				top: null
			};

			// Calculate left position.
			var realWidth = $image.width() - draggerWidth;
			var leftPercentage = (leftPosition / realWidth) * 100;
			result.left = Math.round(leftPercentage);

			// Calculate top position.
			var realHeight = $image.height() - draggerHeight;
			var topPercentage = (topPosition / realHeight) * 100;
			result.top = Math.round(topPercentage);
			return result;
		};

		/**
		 * @description Converts percent values to the respective pixel values.
		 * @param {number} leftPosition
		 * @param {number} topPosition
		 * @returns {object}
		 * @private
		 */
		var _convertPercentToPixel = function (leftPosition, topPosition) {
			// Result object, which will be returned.
			var result = {
				left: null,
				top: null
			};

			// Calculate left position.
			var realWidth = $image.width() - draggerWidth;
			result.left = realWidth / 100 * leftPosition;

			// Calculate top position.
			var realHeight = $image.height() - draggerHeight;
			result.top = realHeight / 100 * topPosition;

			return result;
		};

		// HELPER METHODS
		// ==============

		/**
		 * @description Extracts numeric values from string and
		 * returns the first two values in an array.
		 * Has to return at least two extracted values,
		 * otherwise it will return null.
		 * @param {string} value
		 * @returns {Array|null}
		 * @private
		 */
		var _extractValues = function (value) {
			// Result which will be returned.
			var result;

			// Regex to extract numeric values.
			var regex = /([\d]+)/g;

			// Extracted values from array.
			var extractedValues = value.match(regex);

			// Check if at least two values have been extracted
			// and assign them to result variable,
			// otherwise null will be assigned.
			if (extractedValues === null || extractedValues.length < 2) {
				result = null;
			} else {
				result = [
					extractedValues[0],
					extractedValues[1]
				];
			}

			return result;
		};

		// ELEMENT TOGGLING
		// ================

		/**
		 * @description Shows/hides checkbox for display G-Motion control panel.
		 * @param {boolean} doShow Determines whether to show/hide.
		 * @private
		 */
		var _toggleCheckbox = function (doShow) {
			if (doShow) {
				$settingsContainers
					.not($panel)
					.removeClass('hidden');
			} else {
				$settingsContainers.addClass('hidden');
			}
		};

		/**
		 * @description Shows/hides G-Motion animation control panel.
		 * @param {boolean} doShow Determines whether to show/hide.
		 * @private
		 */
		var _toggleControlPanel = function (doShow) {
			if (doShow) {
				$panel
					.removeClass('hidden')
					.css({
						opacity: 0.1
					});

				setTimeout(function () {
					_initializeDraggers();
					_initializeValues();

					$panel
						.animate({
						     opacity: 1
					     });
				}, 1000);
			} else {
				$panel.addClass('hidden');
			}

			$panel
				.find('input, select')
				.prop('disabled', !doShow);
		};

		// INIT
		// ====

		/**
		 * @description Initializes event handlers.
		 * @private
		 */
		var _initializeEventHandlers = function () {
			/**
			 * @description (De-)activates G-Motion option checkboxes in images settings.
			 * @listens click
			 */
			$activator
				.parent()
				.on('click', function () {
					_toggleCheckbox($activator.is(':checked'));
					_toggleControlPanel(($checkbox.is(':checked') && $activator.is(':checked')));
				});

			/**
			 * @description Shows/Hides G-Motion control panel on checkbox click.
			 * @listens click
			 */
			$checkbox
				.parent()
				.on('click', function () {
					_toggleControlPanel($checkbox.is(':checked'));
				});

			/**
			 * @description Update dragger position.
			 * @listens keyup
			 */
			$startInput
				.on('keyup', function () {
					_updateDraggerFromInput(this);
				});

			/**
			 * @description Update dragger position.
			 * @listens keyup
			 */
			$endInput
				.on('keyup', function () {
					_updateDraggerFromInput(this);
				});
		};

		/**
		 * @description Set values.
		 * @private
		 */
		var _initializeValues = function () {
			// Position start value
			// ====================
			if (data.positionFrom) {
				$startInput.val(data.positionFrom);
			} else {
				_refreshInputValues(
					$startInput,
					defaultAnimationStartLeft,
					defaultAnimationStartTop
				);
			}
			_updateDraggerFromInput($startInput);

			// Position end value
			// ==================
			if (data.positionTo) {
				$endInput.val(data.positionTo);
			} else {
				_refreshInputValues(
					$endInput,
					defaultAnimationEndLeft,
					defaultAnimationEndTop
				);
			}
			_updateDraggerFromInput($endInput);

			// Zoom start value
			// ================
			var zoomStartValue = data.zoomFrom ? data.zoomFrom : defaultZoomStart;
			$zoomStartInput.val(zoomStartValue);

			// Zoom end value
			// ==============
			var zoomEndValue = data.zoomTo ? data.zoomTo : defaultZoomEnd;
			$zoomEndInput.val(zoomEndValue);

			// Animation duration
			// ==================
			var durationValue = data.duration ?
			                    data.duration :
			                    defaultAnimationDuration;
			$animationDurationInput.val(durationValue);
		};

		// =====================================================================
		// INITIALIZATION
		// =====================================================================

		module.init = function (done) {
			// Set up event listeners
			_initializeEventHandlers();

			// Handle initial visibility state of G-Motion controls.
			if ($activator.is(':checked')) {
				_toggleCheckbox(true);
				if ($checkbox.is(':checked')) {
					_toggleControlPanel(true);
				}
			}

			done();
		};

		return module;
	}
);
