/* --------------------------------------------------------------
 close_alert_box.js 2015-09-30 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Close Alert Box
 *
 * This module will hide an alert box by clicking a button with the class "close".
 *
 * @module Compatibility/close_alert_box
 */
gx.compatibility.module(
	'close_alert_box',

	[],

	/**  @lends module:Compatibility/close_alert_box */

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
			defaults = {},

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
		// INITIALIZATION
		// ------------------------------------------------------------------------

		module.init = function (done) {
			var $createNewWrapper = $('.create-new-wrapper');

			$this.find('button.close').on('click', function () {

				$(this).parent('.alert').hide();
				if ($createNewWrapper.length > 0) {
					$createNewWrapper.removeClass('message-stack-active');
				}
			});

			done();
		};

		return module;
	});
