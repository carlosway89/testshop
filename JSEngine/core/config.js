/* --------------------------------------------------------------
 config.js 2015-10-20 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.config = jse.core.config || {};

/**
 * JS Engine Configuration Object
 *
 * Once the config object is initialized you cannot change its values. This is done in order to
 * prevent unpleasant situations where one code section changes a core config setting that affects
 * another code section in a way that is hard to discover.
 *
 * ```javascript
 * var shopUrl = jse.core.config.get('shopUrl');
 * ```
 *
 * @namespace JSE/Core/config
 */
(function (exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// CONFIGURATION VALUES
	// ------------------------------------------------------------------------

	var config = {
		/**
		 * Engine Version
		 *
		 * @type {string}
		 */
		version: '1.2.0',

		/**
		 * Shop URL
		 *
		 * e.g. 'http://shop.de
		 *
		 * @type {string}
		 */
		shopUrl: null,

		/**
		 * URL to JSEngine Directory.
		 *
		 * e.g. 'http://shop.de/JSEngine
		 *
		 * @type {string}
		 */
		engineUrl: null,

		/**
		 * Engine Environment
		 *
		 * Defines the functionality of the engine in many sections.
		 *
		 * @type {string}
		 */
		environment: 'production',

		/**
		 * HTML Attribute Prefix
		 *
		 * This will prefix the HTML attributes that have a special meaning to the JSEngine. Should
		 * be mostly a short string.
		 *
		 * @type {string}
		 */
		prefix: 'gx',

		/**
		 * Translations Object
		 *
		 * Contains the loaded translations to be used within JSEngine.
		 *
		 * @see gx.libs.lang object
		 * @type {object}
		 */
		translations: {},

		/**
		 * Current Language Code
		 *
		 * @type {string}
		 */
		languageCode: '',

		/**
		 * Set the debug level to one of the following: 'DEBUG', 'INFO', 'LOG', 'WARN', 'ERROR', 'ALERT', 'SILENT'
		 *
		 * @type {string}
		 */
		debug: 'SILENT',

		/**
		 * Use cache busting technique when loading modules.
		 *
		 * @see jse.core.debug object
		 * @type {bool}
		 */
		cacheBust: true,

		/**
		 * Load minified files.
		 *
		 * @type {bool}
		 */
		minified: true,

		/**
		 * Whether the client has a mobile interface.
		 *
		 * @type {bool}
		 */
		mobile: false,

		/**
		 * Whether the client supports touch events.
		 *
		 * @type {bool}
		 */
		touch: (window.ontouchstart || window.onmsgesturechange) ? true : false,

		/**
		 * Specify the path for the file manager.
		 *
		 * @type {string}
		 */
		filemanager: 'includes/ckeditor/filemanager/index.html',

		/**
		 * Page token to include in every AJAX request.
		 *
		 * The page token is used to avoid CSRF attacks. It must be provided by the
		 * backend and it will be validated there.
		 *
		 * @type {string}
		 */
		pageToken: '',

		/**
		 * Defines whether the history object is available.
		 */
		history: history && history.replaceState && history.pushState
	};

	// ------------------------------------------------------------------------
	// PUBLIC METHODS
	// ------------------------------------------------------------------------

	/**
	 * Get a configuration value.
	 *
	 * @param {string} name The configuration value name to be retrieved.
	 *
	 * @returns {*} Returns the config value.
	 *
	 * @name core/config.init
	 * @public
	 * @method
	 */
	exports.get = function (name) {
		return config[name];
	};

	/**
	 * Initialize the JS Engine config object.
	 *
	 * This method will parse the global "JSEngineConfiguration" object and then remove
	 * it from the global scope so that it becomes the only config source for javascript.
	 *
	 * Notice: The only required JSEngineConfiguration values are the "environment" and the "shopUrl".
	 *
	 * @param {object} jsEngineConfiguration Must contain information that define core operations
	 * of the engine. Check the "libs/initialize" entry of the engine documentation.
	 *
	 * @name core/config.init
	 * @public
	 * @method
	 */
	exports.init = function (jsEngineConfiguration) {
		config.environment = jsEngineConfiguration.environment;
		config.shopUrl = jsEngineConfiguration.shopUrl;

		if (config.environment === 'development') {
			config.cacheBust = false;
			config.minified = false;
			config.debug = 'DEBUG';
		}

		if (typeof jsEngineConfiguration.engineUrl !== 'undefined') {
			config.engineUrl = jsEngineConfiguration.engineUrl;
		} else {
			config.engineUrl = config.shopUrl + '/JSEngine/build';
		}

		if (typeof jsEngineConfiguration.translations !== 'undefined') {
			config.translations = jsEngineConfiguration.translations;

			$.each(config.translations, function (sectionName, sectionTranslations) {
				jse.core.lang.addSection(sectionName, sectionTranslations);
			});
		}

		if (typeof jsEngineConfiguration.prefix !== 'undefined') {
			config.prefix = jsEngineConfiguration.prefix;
		}

		if (typeof jsEngineConfiguration.languageCode !== 'undefined') {
			config.languageCode = jsEngineConfiguration.languageCode;
		}

		if (typeof jsEngineConfiguration.pageToken !== 'undefined') {
			config.pageToken = jsEngineConfiguration.pageToken;
		}

		// Initialize the module loader object.
		jse.core.module_loader.init();

		// Destroy global EngineConfiguration object.
		delete window.JSEngineConfiguration;
	};

}(jse.core.config));
