/* --------------------------------------------------------------
 collapser.js 2015-08-28
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Collapser Widget
 *
 * This widget toggles the visibility state of target element and indicates
 * the current state by a plus or minus icon
 *
 * #### EXAMPLE
 * ```html
 *  <div class="headline-wrapper"
 *          data-gx-widget="collapser"
 *          data-collapser-target_selector=".content-wrapper"
 *          data-collapser-section="category_base_data"
 *          data-collapser-user_id="1"
 *          data-collapser-collapsed="true">
 *      Headline
 *  </div>
 *  <div class="content-wrapper">
 *      Toggled content
 *  </div>
 * ```
 *
 * @module Admin/Widgets/collapser
 */
gx.widgets.module(
	'collapser',

	['user_configuration_service'],

	/** @lends module:Widgets/collapser */

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
			 * UserConfigurationService Alias
			 *
			 * @type {object}
			 */
			userConfigurationService = jse.libs.user_configuration_service,

			/**
			 * Default Options for Widget
			 *
			 * @type {object}
			 */
			defaults = {
				collapsed: false,
				collapsed_icon_class: 'fa-plus-square-o',
				expanded_icon_class: 'fa-minus-square-o',
				additional_classes: 'pull-right',
				parent_selector: ''
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
			module = {};

		// ------------------------------------------------------------------------
		// PRIVATE METHODS
		// ------------------------------------------------------------------------

		/**
		 * Sets the cursor to pointer
		 * @private
		 */
		var _setMouseCursorPointer = function () {
			$this.addClass('cursor-pointer').children().addClass('cursor-pointer');
		};

		/**
		 * Sets the initial visibility according to the 'collapsed' value
		 * @private
		 */
		var _setInitialVisibilityState = function () {
			if (options.collapsed) {
				if (options.parent_selector) {
					$this.parents(options.parent_selector).next(options.target_selector).hide();
				}
				else {
					$this.next(options.target_selector).hide();
				}
			}
		};

		/**
		 * Creates the markup for the collapser and adds the click event handler
		 * @private
		 */
		var _createCollapser = function () {
			$this.append(
				$('<span></span>').addClass('collapser').addClass(options.additional_classes).append(
					$('<i></i>').addClass('fa').addClass(options.collapsed ? options.collapsed_icon_class :
					                                     options.expanded_icon_class)
				)
			).on('click', _toggleVisibilityState);
		};

		var _saveVisibilityState = function () {
			var collapseState = $this.find('.collapser > i.fa').hasClass(options.collapsed_icon_class);

			userConfigurationService.set({
				                             data: {
					                             userId: options.user_id,
					                             configurationKey: options.section + '_collapse',
					                             configurationValue: collapseState
				                             }
			                             });
		};

		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------

		/**
		 * Toggles the visibility state and switches between plus and minus icon.
		 *
		 * @private
		 */
		var _toggleVisibilityState = function () {
			if (options.parent_selector) {
				$this.parents(options.parent_selector).next(options.target_selector).toggle();
			}
			else {
				$this.next(options.target_selector).toggle();
			}

			$this.find('.collapser > i.fa').toggleClass(options.collapsed_icon_class);
			$this.find('.collapser > i.fa').toggleClass(options.expanded_icon_class);

			_saveVisibilityState();
		};

		// ------------------------------------------------------------------------
		// INITIALIZE
		// ------------------------------------------------------------------------

		/**
		 * Initialize method of the module, called by the engine.
		 */
		module.init = function (done) {
			_setMouseCursorPointer();
			_setInitialVisibilityState();
			_createCollapser();
			done();
		};

		// Return data to module engine
		return module;
	});
