/* --------------------------------------------------------------
 initialize.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

// Initialize base engine object. Every other part of the engine will refer to this
// central object for the core operations.

window.jse = {
	core: {},
	libs: {},
	constructors: {}
};

/**
 * ## JS Engine Initialization
 *
 * The document-ready event of the page will trigger the JavaScript Engine initialization. The
 * Engine requires a global configuration object "window.JSEngineConfiguration" to be pre-defined
 * in order to retrieve the basic configuration info. After a successful initialization the
 * EngineConfiguration object is removed from the global scope.
 *
 * Important: Place this file at the top of the concatenated files that will be included in the "jse.js".
 *
 * ## Configuration Sample
 *
 * ```javascript
 * window.JSEngineConfiguration = {
 *     environment: 'production',
 *     shopUrl: 'http://www.shop.de',
 *     translations: {
 *         'sectionName': { 'translationKey': 'translationValue' },
 *         'anotherSection': { ... }
 *     },
 *     languageCode: 'de',
 *     pageToken: '9asd7f9879sd8f79s98s7d98f'
 * };
 * ```
 *
 * @namespace JSE/Core/initialize
 */

$(document).ready(function () {

	'use strict';

	try {
		// Check if global JSEngineConfiguration object is defined.
		if (typeof window.JSEngineConfiguration === 'undefined') {
			throw new Error(
				'The "window.JSEngineConfiguration" object is not defined in the global scope. This ' +
				'object is required by the JSEngine upon its initialization. Check "core/initialize" ' +
				'documentation page.');
		}

		// Parse JSEngineConfiguration object.
		jse.core.config.init(window.JSEngineConfiguration);

		// Initialize engine module collections.
		window.engineStartTime = new Date().getTime();
		jse.core.engine.init([
			new jse.constructors.Collection('extensions', 'extension'),
			new jse.constructors.Collection('controllers', 'controller'),
			new jse.constructors.Collection('widgets', 'widget'),
			new jse.constructors.Collection('compatibility', 'compatibility')
		]);
	} catch (exception) {
		jse.core.debug.error('Unexpected error during JS Engine initialization!', exception);
	}
});
