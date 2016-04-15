/* --------------------------------------------------------------
 extensions.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Extend JS Engine
 *
 * Extend the default behaviour of engine components or external plugins before they are loaded.
 *
 * @namespace JSE/Core/extend
 */
(function () {

	'use strict';

	// ------------------------------------------------------------------------
	// NAMESPACE PSEUDO SELECTOR DEFINITION
	// ------------------------------------------------------------------------

	if (typeof $.expr.pseudos.attr === 'undefined') {
		$.expr.pseudos.attr = $.expr.createPseudo(function(selector) {
			var regexp = new RegExp(selector);
			return function(elem) {
				for(var i = 0; i < elem.attributes.length; i++) {
					var attr = elem.attributes[i];
					if(regexp.test(attr.name)) {
						return true;
					}
				}
				return false;
			};
		});
	}


	// ------------------------------------------------------------------------
	// EXTENSION DEFINITION
	// ------------------------------------------------------------------------

	/**
	 * Set jQuery UI datepicker widget defauls.
	 *
	 * @name core/extend.datepicker
	 * @public
	 *
	 * @type {object}
	 */
	$.datepicker.regional.de = {
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false
	};
	$.datepicker.setDefaults($.datepicker.regional.de);
}());
