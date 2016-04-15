/* --------------------------------------------------------------
 visibility_switcher.js 2015-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Visibility Switcher Extension
 *
 * On hover state all elements found by a css selector will be set to visible. On mouseleave event they will be set to
 * hidden again. The css selector is a data-attribute called "selector".
 *
 * @module Admin/Extensions/visibility_switcher
 */
gx.extensions.module(
	'visibility_switcher',

	[],

	/**  @lends module:Extensions/visibility_switcher */

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------

		var
			/**
			 * Module Selector
			 *
			 * @var {object}
			 */
			$this = $(this),

			/**
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {
				'rows': '.visibility_switcher',
				'selections': '.tooltip-icon'
			},

			/**
			 * Final Options
			 *
			 * @var {object}
			 */
			options = $.extend(true, {}, defaults, data),

			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};

		// ------------------------------------------------------------------------
		// PRIVATE FUNCTIONS
		// ------------------------------------------------------------------------

		var _visibility = function (e) {
			var $self = $(this);
			$self
				.filter(options.selections)
				.add($self.find(options.selections))
				.css('visibility', e.data.state);
		};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		module.init = function (done) {

			$this
				.on('mouseenter', options.rows, {'state': 'visible'}, _visibility)
				.on('mouseleave', options.rows, {'state': 'hidden'}, _visibility);
			
			$this
				.find(options.rows + ' ' + options.selections)
				.css('visibility', 'hidden');

			done();

		};

		return module;
	});
