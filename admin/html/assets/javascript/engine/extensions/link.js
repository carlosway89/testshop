/* --------------------------------------------------------------
 link.js 2015-09-29 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals getSelection */

/**
 * ## Link Module
 *
 * This module opens a link on click event
 *
 * @module Admin/Extensions/link
 */
gx.extensions.module(
	'link',

	[],

	/**  @lends module:Extensions/link */
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
				url: '#'
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
		// INITIALIZATION
		// ------------------------------------------------------------------------

		module.init = function (done) {

			$this.on('mouseup', function (event) {

				// 1 = left click, 2 = middle click
				if (event.which === 1 || event.which === 2) {
					event.preventDefault();
					event.stopPropagation();

					var target = (event.which === 1) ? '_self' : '_blank';
					var sel = getSelection().toString();

					if (!sel) {
						window.open(options.url, target);
					}
				}

			});

			done();
		};

		return module;
	});
