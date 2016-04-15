/* --------------------------------------------------------------
 sitemap_generator.js 2015-10-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Sitemap Generator Controller
 *
 * This module will execute the sitemap generation
 *
 * @module Compatibility/sitemap_generator
 */
gx.compatibility.module(
	'sitemap_generator',

	[
		jse.core.config.get('shopUrl') + '/admin/html/assets/javascript/engine/libs/info_messages'
	],

	/**  @lends module:Compatibility/sitemap_generator */

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
			defaults = {'url': 'gm_sitemap_creator.php'},

			/**
			 * Final Options
			 *
			 * @var {object}
			 */
			options = $.extend(true, {}, defaults, data),

			/**
			 * Reference to the info messages library
			 * @var {object}
			 */
			messages = window.gx.libs.info_messages,

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
			$this.on('click', function () {
				$.ajax({
					       url: options.url,
					       data: options.params,
					       type: 'GET'
				       })
					// On success
					.done(function (response) {
						messages.addSuccess(response);
					})
					// On Failure
					.fail(function (response) {
						console.log('Error!', response);
					});
				$this.blur();
				return false;
			});

			done();
		};

		return module;
	});
