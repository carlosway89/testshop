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

/* --------------------------------------------------------------
 collection.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

(function() {

	'use strict';

	/**
	 * Class Collection
	 *
	 * @param {string} name The collection name - must be unique.
	 * @param {string} attribute The attribute that will trigger collection's modules.
	 * @param {object} namespace (optional) The namespace instance where the collection belongs.
	 *
	 * @constructor JSE/Core/Collection
	 */
	function Collection(name, attribute, namespace) {
		this.name = name;
		this.attribute = attribute;
		this.namespace = namespace;
		this.cache = {
			modules: {},
			data: {}
		};
	}

	/**
	 * Define a new engine module.
	 *
	 * This function will define a new module into the engine. Each extension will
	 * be stored in the collection's cache to prevent unnecessary file transfers via RequireJS.
	 * The same happens with the default configuration that append to the module definition.
	 *
	 * @param {string} name Name of the module.
	 * @param {array} dependencies Array of libraries that this module depends on (will be loaded asynchronously
	 *                  & apply only file names without extension e.g. ["emails"]).
	 * @param {object} code Contains the module code (function).
	 *
	 * @name core/collection.module
	 * @public
	 * @method
	 */
	Collection.prototype.module = function(name, dependencies, code) {
		// Collection instance alias.
		var collection = this;

		// Check if required values are available and of correct type.
		if (!name || typeof name !== 'string' || typeof code !== 'function') {
			jse.core.debug.warn('Registration of the module failed, due to bad function call', arguments);
			return false;
		}

		// Check if the module is already defined.
		if (collection.cache.modules[name]) {
			jse.core.debug.warn('Registration of module "' + name + '" skipped, because it already exists.');
			return false;
		}

		// Store the module to cache so that it can be used later.
		collection.cache.modules[name] = {
			code: code,
			dependencies: dependencies
		};
	};

	/**
	 * [DEPRECATED] Register a module definition
	 *
	 * This method exists only for fallback support. It will be removed in the future so please
	 * use the "module" function above.
	 *
	 * @todo Remove the fallback in the next engine version.
	 *
	 * @deprecated since version 1.2.0
	 */
	Collection.prototype.register = function(name, version, dependencies, code, defaults) {
		jse.core.debug.warn('The Collection.prototype.register() method is deprecated as of v1.2.0, use the '
		                    + 'Collection.prototype.module() method instead -- ' + name);
		this.module(name, dependencies, code);
	};

	/**
	 * Initialize Module Collection
	 *
	 * This method will trigger the page modules initialization. It will search all
	 * the DOM for the "data-gx-extension", "data-gx-controller" or
	 * "data-gx-widget" attributes and load the relevant scripts through RequireJS.
	 *
	 * @param {object} $parent Parent element will be used to search for the required modules.
	 * @param {object} namespaceDeferred Deferred object that gets processed after the module initialization is finished.
	 *
	 * @name core/engine.init
	 * @public
	 * @method
	 */
	Collection.prototype.init = function($parent, namespaceDeferred) {
		// Collection instance alias.
		var collection = this;

		// Store the namespaces reference of the collection.
		if (!collection.namespace) {
			throw new Error('Collection cannot be initialized without its parent namespace instance.');
		}

		// Set the default parent-object if none was given.
		if (typeof $parent === 'undefined' || $parent === null) {
			$parent = $('html');
		}

		var attribute = 'data-' + collection.namespace.name + '-' + collection.attribute,
			deferredCollection = [];

		$parent
			.filter('[' + attribute + ']')
			.add($parent.find('[' + attribute + ']'))
			.each(function() {
				var $element = $(this),
					modules = $element.attr(attribute);

				$element.removeAttr(attribute);
				$.each(modules.trim().split(' '), function(index, name) {
					var deferred = $.Deferred();
					deferredCollection.push(deferred);

					jse.core.module_loader
					   .load($element, name, collection)
					   .done(function(module) {
						   module.init(deferred);
					   })
					   .fail(function(error) {
						   deferred.reject();
						   // Log the error in the console but do not stop the engine execution.
						   jse.core.debug.error('Could not load module: ' + name, error);
					   });
				});
			});

		// If an namespaceDeferred is given resolve or reject it depending on the module initialization status.
		if (namespaceDeferred) {
			if (deferredCollection.length === 0 && namespaceDeferred) {
				namespaceDeferred.resolve();
			}

			$.when.apply(undefined, deferredCollection).promise()
			 .done(function() {
				 namespaceDeferred.resolve();
			 })
			 .fail(function() {
				 namespaceDeferred.fail();
			 });
		}
	};

	jse.constructors.Collection = Collection;
})();

/* --------------------------------------------------------------
 module.js 2016-02-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 ----------------------------------------------------------------
 */

(function() {

	'use strict';

	/**
	 * Class Module
	 *
	 * @param {object} $element Module element selector object.
	 * @param {string} name The module name (might contain the path)
	 * @param {object} collection The collection instance of the module.
	 *
	 * @constructor JSE/Core/Module
	 */
	function Module($element, name, collection) {
		this.$element = $element;
		this.name = name;
		this.collection = collection;
	}

	/**
	 * Initialize the module execution.
	 *
	 * This function will execute the "init" method of each module.
	 *
	 * @param {object} collectionDeferred Deferred object that gets processed after the module initialization is finished
	 *
	 * @public
	 */
	Module.prototype.init = function(collectionDeferred) {
		// Store module instance alias.
		var module = this,
			cached = module.collection.cache.modules[module.name],
			promise = collectionDeferred.promise(),
			watchdog = null;

		try {
			if (!cached) {
				throw new Error('Module "' + module.name + '" could not be found in the collection cache.');
			}

			var data = _getModuleData(module),
				instance = cached.code.call(module.$element, data);

			// Provide a done function that needs to be called from the module, in order to inform that the module 
			// "init" function was completed successfully.
			var done = function() {
				module.$element.trigger('module.initialized', [
					{
						module: module.name
					}
				]);
				jse.core.debug.info('Module "' + module.name + '" initialized successfully.');
				collectionDeferred.resolve();
				clearTimeout(watchdog);
			};

			// Fallback support for the _initFinished function.
			// @todo Remove the fallback in the next engine version.
			instance._initFinished = function() {
				jse.core.debug.warn('The "_initFinished" function is deprecated as of v1.2.0, use the new '
				                    + 'module initialization instead -- ' + module.name);
				done();
			};

			// Fallback support for the _data function.
			// @todo Remove the fallback in the next engine version.
			instance._data = function($element) {
				jse.core.debug.warn('The "_data" function is deprecated as of v1.2.0, use jQuery data() '
				                    + 'function instead -- ' + module.name);

				var initialData = $element.data(),
					filteredData = {};

				// Searches for module relevant data inside the main-data-object.
				// Data for other widgets will not get passed to this widget
				$.each(initialData, function(key, value) {
					if (key.indexOf(module.name) === 0 || key.indexOf(module.name.toLowerCase()) === 0) {
						var newKey = key.substr(module.name.length);
						newKey = newKey.substr(0, 1).toLowerCase() + newKey.substr(1);
						filteredData[newKey] = value;
					}
				});

				return filteredData;
			};

			// Load the module data before the module is loaded.
			_loadModuleData(instance)
				.done(function() {
					_syncLibsFallback();
					
					// Reject the collectionDeferred if the module isn't initialized after 15 seconds.
					watchdog = setTimeout(function() {
						jse.core.debug.warn('Module was not initialized after 15 seconds! -- ' + module.name);
						collectionDeferred.reject();
					}, 15000);
					
					instance.init(done);
				})
				.fail(function(error) {
					collectionDeferred.reject();
					jse.core.debug.error('Could not load module\'s meta data.', error);
				});
		} catch (exception) {
			collectionDeferred.reject();
			jse.core.debug.error('Cannot initialize module "' + module.name + '"', exception);
		}

		return promise;
	};

	/**
	 * Parse the module data attributes.
	 *
	 * @param {object} module The module instance to be parsed.
	 *
	 * @returns {object} Returns an object that contains the data of the module.
	 *
	 * @private
	 */
	var _getModuleData = function(module) {
		var data = {};

		$.each(module.$element.data(), function(name, value) {
			if (name.indexOf(module.name) === 0 || name.indexOf(module.name.toLowerCase()) === 0) {
				var key = name.substr(module.name.length);
				key = key.substr(0, 1).toLowerCase() + key.substr(1);
				data[key] = value;
				module.$element.removeAttr('data-' + module.name + '-' + key);
			}
		});

		return data;
	};

	/**
	 * Modules return objects which might contain requirements.
	 *
	 * @param {object} instance Module instance object.
	 *
	 * @return {object} Returns a promise object that will be resolved when the data are fetched.
	 *
	 * @private
	 */
	var _loadModuleData = function(instance) {
		var deferred = $.Deferred();

		try {
			var promises = [];

			if (instance.model) {
				$.each(instance.model, function(index, url) {
					var modelDeferred = $.Deferred();
					promises.push(modelDeferred);
					$.getJSON(url)
					 .done(function(response) {
						 instance.model[index] = response;
						 modelDeferred.resolve(response);
					 })
					 .fail(function(error) {
						 modelDeferred.reject(error);
					 });
				});
			}

			if (instance.view) {
				$.each(instance.view, function(index, url) {
					var viewDeferred = $.Deferred();
					promises.push(viewDeferred);
					$.get(url)
					 .done(function(response) {
						 instance.view[index] = response;
						 viewDeferred.resolve(response);
					 })
					 .fail(function(error) {
						 viewDeferred.reject(error);
					 });
				});
			}

			$.when
			 .apply(undefined, promises)
			 .promise()
			 .done(function() {
				 deferred.resolve();
			 })
			 .fail(function(error) {
				 deferred.reject(new Error('Cannot load data for module "' + module.name + '".', error));
			 });
		} catch (exception) {
			deferred.resolve(exception);
		}

		return deferred.promise();
	};

	/**
	 * Engine Libs fallback definition.
	 *
	 * Old modules use the libs under the window.gx.libs object. This method will make sure
	 * that this object is synchronized with the jse.libs until every old module definition is
	 * updated.
	 *
	 * @todo Remove the fallback in the next engine version.
	 *
	 * @private
	 */
	var _syncLibsFallback = function() {
		if (typeof window.gx === 'undefined') {
			window.gx = {};
		}
		window.gx.libs = jse.libs;
	};

	jse.constructors.Module = Module;
})();

/* --------------------------------------------------------------
 namespace.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

(function () {

	'use strict';

	/**
	 * Class Namespace
	 *
	 * @param {string} name The namespace name must be unique within the app.
	 * @param {string} source Complete URL to the namespace modules directory (without trailing slash).
	 * @param {array} collections Contains collection instances to be included in the namespace.
	 *
	 * @constructor JSE/Core/Namespace
	 */
	function Namespace(name, source, collections) {
		this.name = name;
		this.source = source;
		this.collections = collections; // contains the default instances
	}

	/**
	 * Initialize the namespace collections.
	 *
	 * This method will create new collection instances based in the original ones.
	 */
	Namespace.prototype.init = function () {
		var deferredCollection = [];

		for (var index in this.collections) {
			var collection = this.collections[index],
			    deferred = $.Deferred();

			deferredCollection.push(deferred);
			
			this[collection.name] = new jse.constructors.Collection(collection.name, collection.attribute, this);
			this[collection.name].init(null, deferred);
		}

		if (deferredCollection.length === 0) {
			return $.Deferred().resolve();
		}

		return $.when.apply(undefined, deferredCollection).promise();

	};

	jse.constructors.Namespace = Namespace;
})();

/* --------------------------------------------------------------
 about.js 2015-10-14 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Get information about the JS Engine.
 *
 * Execute the `jse.about()` command and you will get a new log entry in the
 * console with info about the engine. The "about" method is only available in
 * the "development" environment of the engine.
 *
 * @namespace JSE/Core/about
 */
$(document).ready(function() {

	'use strict';

	if (jse.core.config.get('environment') === 'production') {
		return;
	}

	jse.about = function () {
		var info = [
			'JS ENGINE v' + jse.core.config.get('version') + ' © GAMBIO GMBH',
			'----------------------------------------------------------------',
			'The JS Engine enables developers to load automatically small pieces of javascript code by',
			'placing specific data attributes to the HTML markup of a page. It was built with modularity',
			'in mind so that modules can be reused into multiple places without extra effort. The engine',
			'contains namespaces which contain collections of modules, each one of whom serve a different',
			'generic purpose.',
			'',
			'Visit http://developers.gambio.de for complete reference of the JS Engine.',
			'',
			'FALLBACK INFORMATION',
			'----------------------------------------------------------------',
			'Since the engine code becomes bigger there are sections that need to be refactored in order',
			'to become more flexible. In most cases a warning log will be displayed at the browser\'s console',
			'whenever there is a use of a deprecated function. Below there is a quick list of fallback support',
			'that will be removed in the future versions of the engine.',
			'',
			'1. The main engine object was renamed from "gx" to "jse" which stands for the JavaScript Engine.',
			'2. The "gx.lib" object is removed after a long deprecation period. You should update the modules ',
			'   that contained calls to the functions of this object.',
			'3. The gx.<collection-name>.register function is deprecated by v1.2, use the ',
			'   <namespace>.<collection>.module() instead.'
		];

		jse.core.debug.info(info.join('\n'));
	};

});

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

/* --------------------------------------------------------------
 debug.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.debug = jse.core.debug || {};

/**
 * JS Engine debug object.
 *
 * This object provides an wrapper to the console.log function and enables easy use
 * of the different log types like "info", "warning", "error" etc.
 *
 * @namespace JSE/Core/debug
 */
(function (/** @lends JSE/Core/debug */ exports) {
	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	/**
	 * All possible debug levels in the order of importance.
	 *
	 * @name Core/Debug.levels
	 * @public
	 * @type {array}
	 */
	var levels = ['DEBUG', 'INFO', 'LOG', 'WARN', 'ERROR', 'ALERT', 'SILENT'];

	/**
	 * Executes the correct console/alert statement.
	 *
	 * @name Core/Debug._execute
	 * @private
	 * @method
	 *
	 * @param {object} caller (optional) Contains the caller information to be displayed.
	 * @param {object} data (optional) Contains any additional data to be included in the debug output.
	 */
	var _execute = function (caller, data) {
		var currentLogIndex = levels.indexOf(caller),
			allowedLogIndex = levels.indexOf(jse.core.config.get('debug')),
			consoleMethod = null;

		if (currentLogIndex >= allowedLogIndex) {
			consoleMethod = caller.toLowerCase();

			if (consoleMethod === 'alert') {
				alert(JSON.stringify(data));
				return;
			}

			if (consoleMethod === 'mobile') {
				var $dbgLayer = $('.mobileDbgLayer');
				if (!$dbgLayer.length) {
					$dbgLayer = $('<div />');
					$dbgLayer.addClass('mobileDbgLayer');
					$dbgLayer.css({
						'position': 'fixed',
						'top': 0,
						'left': 0,
						'max-height': '50%',
						'min-width': '200px',
						'max-width': '300px',
						'background-color': 'crimson',
						'z-index': 100000,
						'overflow': 'scroll'
					});
					$('body').append($dbgLayer);
				}

				$dbgLayer.append('<p>' + JSON.stringify(data) + '</p>');
				return;
			}

			if (typeof console === 'undefined') {
				return; // There is no console support so do not proceed.
			}

			if (typeof console[consoleMethod].apply === 'function' || typeof console.log.apply === 'function') {
				if (typeof console[consoleMethod] !== 'undefined') {
					console[consoleMethod].apply(console, data);
				} else {
					console.log.apply(console, data);
				}
			} else {
				console.log(data);
			}

			return true;
		}
		return false;
	};

	// ------------------------------------------------------------------------
	// VARIABLE EXPORT
	// ------------------------------------------------------------------------

	/**
	 * Replaces console.debug
	 *
	 * @params {all} Any data that should be shown in the console statement
	 *
	 * @name Core/Debug.debug
	 * @public
	 * @method
	 */
	exports.debug = function () {
		_execute('DEBUG', arguments);
	};

	/**
	 * Replaces console.info
	 *
	 * @params {all} Any data that should be shown in the console statement
	 *
	 * @name Core/Debug.info
	 * @public
	 * @method
	 */
	exports.info = function () {
		_execute('INFO', arguments);
	};

	/**
	 * Replaces console.log
	 *
	 * @params {all} Any data that should be shown in the console statement
	 *
	 * @name Core/Debug.log
	 * @public
	 * @method
	 */
	exports.log = function () {
		_execute('LOG', arguments);
	};

	/**
	 * Replaces console.warn
	 *
	 * @params {all} Any data that should be shown in the console statement
	 *
	 * @name Core/Debug.warn
	 * @public
	 * @method
	 */
	exports.warn = function () {
		_execute('WARN', arguments);
	};

	/**
	 * Replaces console.error
	 *
	 * @param {all} Any data that should be shown in the console statement
	 *
	 * @name Core/Debug.error
	 * @public
	 * @method
	 */
	exports.error = function () {
		_execute('ERROR', arguments);
	};

	/**
	 * Replaces alert
	 *
	 * @param {all} Any data that should be shown in the console statement
	 *
	 * @name Core/Debug.alert
	 * @public
	 * @method
	 */
	exports.alert = function () {
		_execute('ALERT', arguments);
	};

	/**
	 * Debug info for mobile devices.
	 */
	exports.mobile = function () {
		_execute('MOBILE', arguments);
	};

}(jse.core.debug));

/* --------------------------------------------------------------
 engine.js 2016-02-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.engine = jse.core.engine || {};

/**
 * Main JS Engine Object
 *
 * This object will initialize the page namespaces and collections.
 *
 * @namespace JSE/Core/engine
 */
(function(/** @lends JSE/Core/engine */ exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// PRIVATE FUNCTIONS
	// ------------------------------------------------------------------------

	/**
	 * Initialize the page namespaces.
	 *
	 * This method will search the page HTML for available namespaces.
	 *
	 * @param {array} collections Contains the module collection instances to be included in the namespaces.
	 *
	 * @return {array} Returns an array with the page namespace names.
	 *
	 * @private
	 */
	var _initNamespaces = function(collections) {
		var pageNamespaceNames = [];

		// Use the custom pseudo selector defined at extend.js in order to fetch the available namespaces.
		$(':attr(data-\(.*\)-namespace)').each(function() {
			var $element = $(this);

			$.each($element.data(), function(name, source) {
				if (name.indexOf('Namespace') === -1) {
					return true; // Not a namespace related value.
				}

				name = name.replace('Namespace', ''); // Remove "Namespace" from the data name.

				// Check if the namespace is already defined.
				if (pageNamespaceNames.indexOf(name) > -1) {
					if (window[name].source !== source) {
						jse.core.debug.error('Element with the duplicate namespace name: ', $element[0]);
						throw new Error('The namespace "' + name + '" is already defined. Please select another ' +
							'name for your namespace.');
					}
					return true; // The namespace is already defined, continue loop.
				}

				if (source === '') {
					throw new SyntaxError('Namespace source is empty: ' + name);
				}

				// Create a new namespaces instance in the global scope (the global scope
				// is used for fallback support of old module definitions).
				if (name === 'jse') { // Modify the engine object with Namespace attributes.
					_convertEngineToNamespace(source, collections);
				} else {
					window[name] = new jse.constructors.Namespace(name, source, collections);
				}

				pageNamespaceNames.push(name);
				$element.removeAttr('data-' + name + '-namespace');
			});
		});

		// Throw an error if no namespaces were found.
		if (pageNamespaceNames.length === 0) {
			throw new Error('No module namespaces were found, without namespaces it is not possible to ' +
				'load any modules.');
		}

		// Initialize the namespace instances.
		var deferredCollection = [];
		
		$.each(pageNamespaceNames, function(index, name) {
			var deferred = $.Deferred();
			
			deferredCollection.push(deferred);
			
			window[name]
				.init()
				.done(function() {
					deferred.resolve();
				})
				.fail(function() {
					deferred.reject();
				})
				.always(function() {
					jse.core.debug.info('Namespace promises were resolved: ' , name); 
				});
		});

		// Trigger an event after the engine has initialized all new modules.
		$.when.apply(undefined, deferredCollection).promise().always(function() {
			$('body').trigger('JSENGINE_INIT_FINISHED', []);
		});

		return pageNamespaceNames;
	};

	/**
	 * Convert the "jse" object to a Namespace compatible object.
	 *
	 * In order to support the "jse" namespace name for the core modules placed in the "JSEngine"
	 * directory, we will need to modify the already existing "jse" object so that it can operate
	 * as a namespace without losing its initial attributes.
	 *
	 * @param {string} source Namespace source path for the module files.
	 * @param {array} collections Contain instances to the protoype collection instances.
	 *
	 * @private
	 */
	var _convertEngineToNamespace = function(source, collections) {
		var tmpNamespace = new jse.constructors.Namespace('jse', source, collections);
		jse.name = tmpNamespace.name;
		jse.source = tmpNamespace.source;
		jse.collections = tmpNamespace.collections;
		jse.init = jse.constructors.Namespace.prototype.init;
	};

	// ------------------------------------------------------------------------
	// PUBLIC FUNCTIONS
	// ------------------------------------------------------------------------

	/**
	 * Initialize the engine.
	 *
	 * @param {array} collections Contains the supported module collections prototypes.
	 */
	exports.init = function(collections) {
		// Initialize the page namespaces.
		var pageNamespaceNames = _initNamespaces(collections);

		// Log the page namespaces (for debugging only).
		jse.core.debug.info('Page Namespaces: ' + pageNamespaceNames.join());

		// Update the engine registry.
		jse.core.registry.set('namespaces', pageNamespaceNames);
	};

})(jse.core.engine);

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

/* --------------------------------------------------------------
 fallback.js 2015-10-16 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.fallback = jse.core.fallback || {};

/**
 * Fallback Library
 *
 * This library contains a set of deprecated functions that are still present for fallback
 * support. Each function will be removed from the engine after two minor releases.
 *
 * @namespace JSE/Core/fallback
 */
(function (/** @lends JSE/Core/fallback */exports) {

	'use strict';


	$(document).ready(function () {

		// Event listener that performs on every validate
		// validate trigger that isn't handled by the validator
		$('body').on('validator.validate', function (e, d) {
			if (d && d.deferred) {
				d.deferred.resolve();
			}
		});

		// Event listener that performs on every formchanges.check
		// trigger that isn't handled by the form_changes_checker
		$('body').on('formchanges.check', function (e, d) {
			if (d && d.deferred) {
				d.deferred.resolve();
			}
		});

		// Apply touch class to body
		// for touch-devices
		if (jse.core.config.get('hasTouch')) {
			$('body').addClass('has-touch');
		}
	});

	/**
	 * Add a deprecation warning in the console.
	 *
	 * As the JS engine evolves many old features will need to be changed in order to let a
	 * finer and clearer API for the JS Engine core mechanisms. Use this method to create a
	 * deprecation warning for the functions placed within this library.
	 *
	 * @param {string} functionName The deprecated function name.
	 * @param {string} deprecationVersion Deprecation version without the "v".
	 * @param {string} removalVersion Removal version withou the "v"
	 *
	 * @private
	 */
	var _deprecation = function (functionName, deprecationVersion, removalVersion) {
		jse.core.debug.warn('The "' + functionName + '" function is deprecated as of v' + deprecationVersion +
		                    ' and will be removed in v' + removalVersion);
	};

	/**
	 * Setup Widget Attribute
	 *
	 * @param {object} $element Change the widget attribute of an element.
	 *
	 * @deprecated since version 1.2.0 - will be removed in 1.4.0
	 *
	 * @public
	 */
	exports.setupWidgetAttr = function ($element) {
		_deprecation('setupWidgetAttr', '1.2.0', '1.4.0');

		$element
			.filter(':attr(^data-gx-_), :attr(^data-gambio-_)')
			.add($element.find(':attr(^data-gx-_), :attr(^data-gambio-_)'))
			.each(function () {
				var $self = $(this),
					attributes = $self[0].attributes,
					matchedAttribute,
					namespaceName;

				$.each(attributes, function (index, attribute) {
					if (attribute === undefined) {
						return true; // wrong attribute, continue loop
					}

					matchedAttribute = attribute.name.match(/data-(gambio|gx)-_.*/g);

					if (matchedAttribute !== null && matchedAttribute.length > 0) {
						namespaceName = matchedAttribute[0].match(/(gambio|gx)/g)[0];

						$self
							.attr(attribute.name.replace('data-' + namespaceName + '-_',
							                             'data-' + namespaceName + '-'), attribute.value);
					}
				});
			});
	};

	/**
	 * @deprecated since version 1.2.0 - will be removed in 1.4.0
	 * @param {object} data
	 * @param {object} $target
	 * @public
	 */
	exports.fill = function (data, $target) {
		_deprecation('fill', '1.2.0', '1.4.0');

		$.each(data, function (i, v) {
			var $elements = $target
				.find(v.selector)
				.add($target.filter(v.selector));

			$elements.each(function () {
				var $element = $(this);

				switch (v.type) {
					case 'html':
						$element.html(v.value);
						break;
					case 'attribute':
						$element.attr(v.key, v.value);
						break;
					case 'replace':
						if (v.value) {
							$element.replaceWith(v.value);
						} else {
							$element
								.addClass('hidden')
								.empty();
						}
						break;
					default:
						$element.text(v.value);
						break;
				}
			});

		});
	};

	/**
	 * @deprecated since version 1.2.0 - will be removed in 1.4.0
	 * @param url
	 * @param deep
	 * @returns {{}}
	 */
	exports.getUrlParams = function (url, deep) {
		_deprecation('getUrlParams', '1.2.0', '1.4.0');

		url = decodeURIComponent(url || location.href);

		var splitUrl = url.split('?'),
			splitParam = (splitUrl.length > 1) ? splitUrl[1].split('&') : [],
			regex = new RegExp(/\[(.*?)\]/g),
			result = {};

		$.each(splitParam, function (i, v) {
			var keyValue = v.split('='),
				regexResult = regex.exec(keyValue[0]),
				base = null,
				basename = keyValue[0].substring(0, keyValue[0].search('\\[')),
				keys = [],
				lastKey = null;

			if (!deep || regexResult === null) {
				result[keyValue[0]] = keyValue[1].split('#')[0];
			} else {

				result[basename] = result[basename] || [];
				base = result[basename];

				do {
					keys.push(regexResult[1]);
					regexResult = regex.exec(keyValue[0]);
				} while (regexResult !== null);

				$.each(keys, function (i, v) {
					var next = keys[i + 1];
					v = v || '0';

					if (typeof (next) === 'string') {
						base[v] = base[v] || [];
						base = base[v];
					} else {
						base[v] = base[v] || undefined;
						lastKey = v;
					}
				});

				if (lastKey !== null) {
					base[lastKey] = keyValue[1];
				} else {
					base = keyValue[1];
				}
			}

		});

		return result;
	};

	/**
	 * Fallback getData method.
	 *
	 * This method was included in v1.0 of JS Engine and is replaced by the
	 * "jse.libs.form.getData" method.
	 *
	 * @deprecated since version 1.2.0 - will be removed in 1.4.0
	 *
	 * @param {object} $form Selector of the form to be parsed.
	 * @param {string} ignore (optional) jQuery selector string of form elements to be ignored.
	 *
	 * @returns {object} Returns the data of the form as an object.
	 */
	exports.getData = function ($form, ignore) {
		var $elements = $form.find('input, textarea, select'),
			result = {};

		if (ignore) {
			$elements = $elements.filter(':not(' + ignore + ')');
		}

		$elements.each(function () {
			var $self = $(this),
				type = $self.prop('tagName').toLowerCase(),
				name = $self.attr('name'),
				$selected = null;

			type = (type !== 'input') ? type : $self.attr('type').toLowerCase();

			switch (type) {
				case 'radio':
					$form
						.find('input[name="' + name + '"]:checked')
						.val();
					break;
				case 'checkbox':
					if (name.search('\\[') !== -1) {
						if ($self.prop('checked')) {
							name = name.substring(0, name.search('\\['));
							if (typeof result[name] === 'undefined') {
								result[name] = [];
							}
							result[name].push($(this).val());
						}
					} else {
						result[name] = $self.prop('checked');
					}
					break;
				case 'select':
					$selected = $self.find(':selected');
					if ($selected.length > 1) {
						result[name] = [];
						$selected.each(function () {
							result[name].push($(this).val());
						});
					} else {
						result[name] = $selected.val();
					}
					break;
				case 'button':
					break;
				default:
					if (name) {
						result[name] = $self.val();
					}
					break;
			}
		});
		return result;
	};

})(jse.core.fallback);

/* --------------------------------------------------------------
 lang.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.lang = jse.core.lang || {};

/**
 * ## JS Engine Localization Library
 *
 * The global Lang object contains language information that can be easily used in your
 * JavaScript code. The object contains constance translations and dynamic sections that
 * can be loaded and used in different page.
 *
 * #### Important
 * The engine will automatically load translation sections that are present in the
 * `window.JSEngineConfiguration.translations` property upon initialization. For more
 * information look at the "core/initialize" page of documentation reference.
 *
 * ```javascript
 * jse.core.lang.addSection('sectionName', { translationKey: 'translationValue' }); // Add translation section.
 * jse.core.translate('translationKey', 'sectionName'); // Get the translated string.
 * jse.core.getSections(); // returns array with sections e.g. ['admin_buttons', 'general']
 * ```
 *
 * @namespace JSE/Core/lang
 */
(function (exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
	 * Contains various translation sections.
	 *
	 * @type {object}
	 */
	var sections = {};

	// ------------------------------------------------------------------------
	// PUBLIC METHODS
	// ------------------------------------------------------------------------

	/**
	 * Add a translation section.
	 *
	 * @param {string} name Name of the section, used later for accessing translation strings.
	 * @param {object} translations Key - value object containing the translations.
	 *
	 * @throws Exception if "name" or "translations" arguments are invalid.
	 *
	 * @name core/lang.addSection
	 * @public
	 * @method
	 */
	exports.addSection = function (name, translations) {
		if (typeof name !== 'string' || typeof translations !== 'object' || translations === null ||
		    translations.length === 0) {
			throw new Error('window.gx.core.lang.addSection: Invalid arguments provided (name: ' + (typeof name) +
			                ', translations: ' + (typeof translations) + ')');
		}

		sections[name] = translations;
	};

	/**
	 * Get loaded translation sections.
	 *
	 * Useful for asserting present translation sections.
	 *
	 * @return {array} Returns array with the existing sections.
	 *
	 * @name core/lang.getSections
	 * @public
	 * @method
	 */
	exports.getSections = function () {
		var result = [];
		$.each(sections, function (name, content) {
			result.push(name);
		});
		return result;
	};

	/**
	 * Translate string in Javascript code.
	 *
	 * @param {string} phrase Name of the phrase containing the translation.
	 * @param {string} section Section name containing the translation string.
	 *
	 * @returns {string} Returns the translated string.
	 *
	 * @throws Exception if provided arguments are invalid.
	 * @throws Exception if required section does not exist or translation could not be found.
	 *
	 * @name core/lang.translate
	 * @public
	 * @method
	 */
	exports.translate = function (phrase, section) {
		// Validate provided arguments.
		if (typeof phrase !== 'string' || typeof section !== 'string') {
			throw new Error('Invalid arguments provided in translate method (phrase: ' + (typeof phrase) +
			                ', section: ' + (typeof section) + ').');
		}

		// Check if translation exists.
		if (typeof sections[section] === 'undefined' || typeof sections[section][phrase] === 'undefined') {
			jse.core.debug.warn('Could not found requested translation (phrase: ' + phrase + ', section: '
			                    + section + ').');
			return '{' + section + '.' + phrase + '}';
		}

		return sections[section][phrase];
	};

}(jse.core.lang));

/* --------------------------------------------------------------
 module_loader.js 2015-10-16 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.module_loader = jse.core.module_loader || {};

/**
 * ## JS Engine Module Loader
 *
 * This object is an adapter between the engine and RequireJS which is used to
 * load the required files into the client.
 *
 * @namespace JSE/Core/module_loader
 */
(function (/** @lends JSE/Core/module_loader */ exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// PUBLIC METHODS
	// ------------------------------------------------------------------------

	/**
	 * Initialize the module loader.
	 *
	 * Execute this method after the engien config is inialized. It will configure requirejs
	 * so that it will be able to find the project files.
	 */
	exports.init = function () {
		var config = {
			baseUrl: jse.core.config.get('shopUrl'),
			urlArgs: jse.core.config.get('cacheBust') ? 'bust=' + (new Date()).getTime() : '',
			onError: function (error) {
				jse.core.debug.error('RequireJS Error:', error);
			}
		};

		require.config(config);
	};

	/**
	 * Load a module file with the use of requirejs.
	 *
	 * @param {object} $element Selector of the element which has the module definition.
	 * @param {string} name Module name to be loaded. Modules have the same names as their files.
	 * @param {object} collection Current collection instance.
	 *
	 * @return {object} Returns a promise object to be resolved with the module
	 * instance as a parameter.
	 */
	exports.load = function ($element, name, collection) {
		var deferred = $.Deferred();

		try {
			if (name === '') {
				deferred.reject(new Error('Module name cannot be empty.'));
			}

			var baseModuleName = name.replace(/.*\/(.*)/, '$1'); // Name without the parent directory.

			// Try to load the cached instance of the module.
			var cached = collection.cache.modules[baseModuleName];
			if (cached && cached.code === 'function') {
				console.log(collection, collection.namespace);
				deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
				return true; // continue loop
			}

			// Try to load the module file from the server.
			var fileExtension = jse.core.config.get('debug') !== 'DEBUG' ? '.min.js' : '.js',
				url = collection.namespace.source + '/' + collection.name + '/' + name + fileExtension;

			require([url], function () {
				if (typeof collection.cache.modules[baseModuleName] === 'undefined') {
					throw new Error('Module "' + name + '" wasn\'t defined correctly. Check the module code for '
					                + 'further troubleshooting.');
				}

				var dependencies = collection.cache.modules[baseModuleName].dependencies.slice(); // use slice for copying the array

				if (dependencies.length === 0) { // no dependencies
					deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
					return true; // continue loop
				}

				// Load the dependencies first.
				$.each(dependencies, function (index, dependency) {
					if (dependency.indexOf('http') === -1) { // Then convert the relative path to JSEngine/libs directory.
						dependencies[index] = jse.core.config.get('engineUrl') + '/libs/' + dependency + fileExtension;
					} else if (dependency.indexOf('.js') === -1) { // Then add the dynamic file extension to the URL.
						dependencies[index] += fileExtension;
					}
				});

				require(dependencies, function () {
					deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
				});
			});
		} catch (exception) {
			deferred.reject(exception);
		}

		return deferred.promise();
	};

})(jse.core.module_loader);

/* --------------------------------------------------------------
 polyfills.js 2015-10-28 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Polyfills for cross-browser compatibility.
 *
 * @namespace JSE/Core/polyfills
 */
(function () {

	'use strict';

	if (!Array.prototype.indexOf) {
		Array.prototype.indexOf = function (searchElement, fromIndex) {
			var k;
			if (this == null) {
				throw new TypeError('"this" is null or not defined');
			}

			var O = Object(this);
			var len = O.length >>> 0;

			if (len === 0) {
				return -1;
			}

			var n = +fromIndex || 0;

			if (Math.abs(n) === Infinity) {
				n = 0;
			}

			if (n >= len) {
				return -1;
			}

			k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);

			while (k < len) {
				var kValue;
				if (k in O && O[k] === searchElement) {
					return k;
				}
				k++;
			}
			return -1;
		};
	}

	// Internet Explorer does not support the origin property of the window.location object.
	// @link http://tosbourn.com/a-fix-for-window-location-origin-in-internet-explorer
	if (!window.location.origin) {
		window.location.origin = window.location.protocol + '//' +
		                         window.location.hostname + (window.location.port ? ':' + window.location.port : '');
	}

	// Date.now method polyfill
	// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/now
	if (!Date.now) {
		Date.now = function now() {
			return new Date().getTime();
		};
	}
})();



/* --------------------------------------------------------------
 registry.js 2015-10-13 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.registry = jse.core.registry || {};

/**
 * ## JS Engine Registry
 *
 * This object contains string data that other sections of the engine need in order to
 * operate correctly.
 *
 * @namespace JSE/Core/registry
 */

(function (/** @lends Admin/Libs/registry */ exports) {

	'use strict';

	var registry = [];

	/**
	 * Set a value in the registry.
	 *
	 * @param {string} name Contains the name of the entry to be added.
	 * @param {string} value The value to be written in the registry.
	 *
	 * @public
	 */
	exports.set = function (name, value) {
		// If a registry entry with the same name exists already the following console warning will
		// inform developers that they are overwriting an existing value, something useful when debugging.
		if (typeof registry[name] !== 'undefined') {
			jse.core.debug.warn('The registry value with the name "' + name + '" will be overwritten.');
		}

		registry[name] = value;
	};

	/**
	 * Get a value from the registry.
	 *
	 * @param {string} name The name of the entry value to be returned.
	 *
	 * @returns {*} Returns the value that matches the name.
	 */
	exports.get = function (name) {
		return registry[name];
	};

	/**
	 * Check the current content of the registry object.
	 *
	 * This method is only available when the engine environment is turned into
	 * development.
	 *
	 * @public
	 */
	exports.debug = function () {
		if (jse.core.config.get('environment') === 'development') {
			jse.core.debug.log('Registry Object:', registry);
		} else {
			throw new Error('This function is not allowed in a production environment.');
		}
	};

})(jse.core.registry);

//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImluaXRpYWxpemUuanMiLCJjb2xsZWN0aW9uLmpzIiwibW9kdWxlLmpzIiwibmFtZXNwYWNlLmpzIiwiYWJvdXQuanMiLCJjb25maWcuanMiLCJkZWJ1Zy5qcyIsImVuZ2luZS5qcyIsImV4dGVuZC5qcyIsImZhbGxiYWNrLmpzIiwibGFuZy5qcyIsIm1vZHVsZV9sb2FkZXIuanMiLCJwb2x5ZmlsbHMuanMiLCJyZWdpc3RyeS5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzNFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2xLQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDbE9BO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDekRBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDekRBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUN4T0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDL0xBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzNKQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzVEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzFSQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUM3SEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2hIQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDekVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoianNlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIGluaXRpYWxpemUuanMgMjAxNS0xMC0xMyBnbVxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8vIEluaXRpYWxpemUgYmFzZSBlbmdpbmUgb2JqZWN0LiBFdmVyeSBvdGhlciBwYXJ0IG9mIHRoZSBlbmdpbmUgd2lsbCByZWZlciB0byB0aGlzXHJcbi8vIGNlbnRyYWwgb2JqZWN0IGZvciB0aGUgY29yZSBvcGVyYXRpb25zLlxyXG5cclxud2luZG93LmpzZSA9IHtcclxuXHRjb3JlOiB7fSxcclxuXHRsaWJzOiB7fSxcclxuXHRjb25zdHJ1Y3RvcnM6IHt9XHJcbn07XHJcblxyXG4vKipcclxuICogIyMgSlMgRW5naW5lIEluaXRpYWxpemF0aW9uXHJcbiAqXHJcbiAqIFRoZSBkb2N1bWVudC1yZWFkeSBldmVudCBvZiB0aGUgcGFnZSB3aWxsIHRyaWdnZXIgdGhlIEphdmFTY3JpcHQgRW5naW5lIGluaXRpYWxpemF0aW9uLiBUaGVcclxuICogRW5naW5lIHJlcXVpcmVzIGEgZ2xvYmFsIGNvbmZpZ3VyYXRpb24gb2JqZWN0IFwid2luZG93LkpTRW5naW5lQ29uZmlndXJhdGlvblwiIHRvIGJlIHByZS1kZWZpbmVkXHJcbiAqIGluIG9yZGVyIHRvIHJldHJpZXZlIHRoZSBiYXNpYyBjb25maWd1cmF0aW9uIGluZm8uIEFmdGVyIGEgc3VjY2Vzc2Z1bCBpbml0aWFsaXphdGlvbiB0aGVcclxuICogRW5naW5lQ29uZmlndXJhdGlvbiBvYmplY3QgaXMgcmVtb3ZlZCBmcm9tIHRoZSBnbG9iYWwgc2NvcGUuXHJcbiAqXHJcbiAqIEltcG9ydGFudDogUGxhY2UgdGhpcyBmaWxlIGF0IHRoZSB0b3Agb2YgdGhlIGNvbmNhdGVuYXRlZCBmaWxlcyB0aGF0IHdpbGwgYmUgaW5jbHVkZWQgaW4gdGhlIFwianNlLmpzXCIuXHJcbiAqXHJcbiAqICMjIENvbmZpZ3VyYXRpb24gU2FtcGxlXHJcbiAqXHJcbiAqIGBgYGphdmFzY3JpcHRcclxuICogd2luZG93LkpTRW5naW5lQ29uZmlndXJhdGlvbiA9IHtcclxuICogICAgIGVudmlyb25tZW50OiAncHJvZHVjdGlvbicsXHJcbiAqICAgICBzaG9wVXJsOiAnaHR0cDovL3d3dy5zaG9wLmRlJyxcclxuICogICAgIHRyYW5zbGF0aW9uczoge1xyXG4gKiAgICAgICAgICdzZWN0aW9uTmFtZSc6IHsgJ3RyYW5zbGF0aW9uS2V5JzogJ3RyYW5zbGF0aW9uVmFsdWUnIH0sXHJcbiAqICAgICAgICAgJ2Fub3RoZXJTZWN0aW9uJzogeyAuLi4gfVxyXG4gKiAgICAgfSxcclxuICogICAgIGxhbmd1YWdlQ29kZTogJ2RlJyxcclxuICogICAgIHBhZ2VUb2tlbjogJzlhc2Q3Zjk4NzlzZDhmNzlzOThzN2Q5OGYnXHJcbiAqIH07XHJcbiAqIGBgYFxyXG4gKlxyXG4gKiBAbmFtZXNwYWNlIEpTRS9Db3JlL2luaXRpYWxpemVcclxuICovXHJcblxyXG4kKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbiAoKSB7XHJcblxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHJcblx0dHJ5IHtcclxuXHRcdC8vIENoZWNrIGlmIGdsb2JhbCBKU0VuZ2luZUNvbmZpZ3VyYXRpb24gb2JqZWN0IGlzIGRlZmluZWQuXHJcblx0XHRpZiAodHlwZW9mIHdpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb24gPT09ICd1bmRlZmluZWQnKSB7XHJcblx0XHRcdHRocm93IG5ldyBFcnJvcihcclxuXHRcdFx0XHQnVGhlIFwid2luZG93LkpTRW5naW5lQ29uZmlndXJhdGlvblwiIG9iamVjdCBpcyBub3QgZGVmaW5lZCBpbiB0aGUgZ2xvYmFsIHNjb3BlLiBUaGlzICcgK1xyXG5cdFx0XHRcdCdvYmplY3QgaXMgcmVxdWlyZWQgYnkgdGhlIEpTRW5naW5lIHVwb24gaXRzIGluaXRpYWxpemF0aW9uLiBDaGVjayBcImNvcmUvaW5pdGlhbGl6ZVwiICcgK1xyXG5cdFx0XHRcdCdkb2N1bWVudGF0aW9uIHBhZ2UuJyk7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gUGFyc2UgSlNFbmdpbmVDb25maWd1cmF0aW9uIG9iamVjdC5cclxuXHRcdGpzZS5jb3JlLmNvbmZpZy5pbml0KHdpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb24pO1xyXG5cclxuXHRcdC8vIEluaXRpYWxpemUgZW5naW5lIG1vZHVsZSBjb2xsZWN0aW9ucy5cclxuXHRcdHdpbmRvdy5lbmdpbmVTdGFydFRpbWUgPSBuZXcgRGF0ZSgpLmdldFRpbWUoKTtcclxuXHRcdGpzZS5jb3JlLmVuZ2luZS5pbml0KFtcclxuXHRcdFx0bmV3IGpzZS5jb25zdHJ1Y3RvcnMuQ29sbGVjdGlvbignZXh0ZW5zaW9ucycsICdleHRlbnNpb24nKSxcclxuXHRcdFx0bmV3IGpzZS5jb25zdHJ1Y3RvcnMuQ29sbGVjdGlvbignY29udHJvbGxlcnMnLCAnY29udHJvbGxlcicpLFxyXG5cdFx0XHRuZXcganNlLmNvbnN0cnVjdG9ycy5Db2xsZWN0aW9uKCd3aWRnZXRzJywgJ3dpZGdldCcpLFxyXG5cdFx0XHRuZXcganNlLmNvbnN0cnVjdG9ycy5Db2xsZWN0aW9uKCdjb21wYXRpYmlsaXR5JywgJ2NvbXBhdGliaWxpdHknKVxyXG5cdFx0XSk7XHJcblx0fSBjYXRjaCAoZXhjZXB0aW9uKSB7XHJcblx0XHRqc2UuY29yZS5kZWJ1Zy5lcnJvcignVW5leHBlY3RlZCBlcnJvciBkdXJpbmcgSlMgRW5naW5lIGluaXRpYWxpemF0aW9uIScsIGV4Y2VwdGlvbik7XHJcblx0fVxyXG59KTtcclxuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIGNvbGxlY3Rpb24uanMgMjAxNS0xMC0xMyBnbVxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbihmdW5jdGlvbigpIHtcclxuXHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cclxuXHQvKipcclxuXHQgKiBDbGFzcyBDb2xsZWN0aW9uXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge3N0cmluZ30gbmFtZSBUaGUgY29sbGVjdGlvbiBuYW1lIC0gbXVzdCBiZSB1bmlxdWUuXHJcblx0ICogQHBhcmFtIHtzdHJpbmd9IGF0dHJpYnV0ZSBUaGUgYXR0cmlidXRlIHRoYXQgd2lsbCB0cmlnZ2VyIGNvbGxlY3Rpb24ncyBtb2R1bGVzLlxyXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBuYW1lc3BhY2UgKG9wdGlvbmFsKSBUaGUgbmFtZXNwYWNlIGluc3RhbmNlIHdoZXJlIHRoZSBjb2xsZWN0aW9uIGJlbG9uZ3MuXHJcblx0ICpcclxuXHQgKiBAY29uc3RydWN0b3IgSlNFL0NvcmUvQ29sbGVjdGlvblxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIENvbGxlY3Rpb24obmFtZSwgYXR0cmlidXRlLCBuYW1lc3BhY2UpIHtcclxuXHRcdHRoaXMubmFtZSA9IG5hbWU7XHJcblx0XHR0aGlzLmF0dHJpYnV0ZSA9IGF0dHJpYnV0ZTtcclxuXHRcdHRoaXMubmFtZXNwYWNlID0gbmFtZXNwYWNlO1xyXG5cdFx0dGhpcy5jYWNoZSA9IHtcclxuXHRcdFx0bW9kdWxlczoge30sXHJcblx0XHRcdGRhdGE6IHt9XHJcblx0XHR9O1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogRGVmaW5lIGEgbmV3IGVuZ2luZSBtb2R1bGUuXHJcblx0ICpcclxuXHQgKiBUaGlzIGZ1bmN0aW9uIHdpbGwgZGVmaW5lIGEgbmV3IG1vZHVsZSBpbnRvIHRoZSBlbmdpbmUuIEVhY2ggZXh0ZW5zaW9uIHdpbGxcclxuXHQgKiBiZSBzdG9yZWQgaW4gdGhlIGNvbGxlY3Rpb24ncyBjYWNoZSB0byBwcmV2ZW50IHVubmVjZXNzYXJ5IGZpbGUgdHJhbnNmZXJzIHZpYSBSZXF1aXJlSlMuXHJcblx0ICogVGhlIHNhbWUgaGFwcGVucyB3aXRoIHRoZSBkZWZhdWx0IGNvbmZpZ3VyYXRpb24gdGhhdCBhcHBlbmQgdG8gdGhlIG1vZHVsZSBkZWZpbml0aW9uLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtzdHJpbmd9IG5hbWUgTmFtZSBvZiB0aGUgbW9kdWxlLlxyXG5cdCAqIEBwYXJhbSB7YXJyYXl9IGRlcGVuZGVuY2llcyBBcnJheSBvZiBsaWJyYXJpZXMgdGhhdCB0aGlzIG1vZHVsZSBkZXBlbmRzIG9uICh3aWxsIGJlIGxvYWRlZCBhc3luY2hyb25vdXNseVxyXG5cdCAqICAgICAgICAgICAgICAgICAgJiBhcHBseSBvbmx5IGZpbGUgbmFtZXMgd2l0aG91dCBleHRlbnNpb24gZS5nLiBbXCJlbWFpbHNcIl0pLlxyXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBjb2RlIENvbnRhaW5zIHRoZSBtb2R1bGUgY29kZSAoZnVuY3Rpb24pLlxyXG5cdCAqXHJcblx0ICogQG5hbWUgY29yZS9jb2xsZWN0aW9uLm1vZHVsZVxyXG5cdCAqIEBwdWJsaWNcclxuXHQgKiBAbWV0aG9kXHJcblx0ICovXHJcblx0Q29sbGVjdGlvbi5wcm90b3R5cGUubW9kdWxlID0gZnVuY3Rpb24obmFtZSwgZGVwZW5kZW5jaWVzLCBjb2RlKSB7XHJcblx0XHQvLyBDb2xsZWN0aW9uIGluc3RhbmNlIGFsaWFzLlxyXG5cdFx0dmFyIGNvbGxlY3Rpb24gPSB0aGlzO1xyXG5cclxuXHRcdC8vIENoZWNrIGlmIHJlcXVpcmVkIHZhbHVlcyBhcmUgYXZhaWxhYmxlIGFuZCBvZiBjb3JyZWN0IHR5cGUuXHJcblx0XHRpZiAoIW5hbWUgfHwgdHlwZW9mIG5hbWUgIT09ICdzdHJpbmcnIHx8IHR5cGVvZiBjb2RlICE9PSAnZnVuY3Rpb24nKSB7XHJcblx0XHRcdGpzZS5jb3JlLmRlYnVnLndhcm4oJ1JlZ2lzdHJhdGlvbiBvZiB0aGUgbW9kdWxlIGZhaWxlZCwgZHVlIHRvIGJhZCBmdW5jdGlvbiBjYWxsJywgYXJndW1lbnRzKTtcclxuXHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIENoZWNrIGlmIHRoZSBtb2R1bGUgaXMgYWxyZWFkeSBkZWZpbmVkLlxyXG5cdFx0aWYgKGNvbGxlY3Rpb24uY2FjaGUubW9kdWxlc1tuYW1lXSkge1xyXG5cdFx0XHRqc2UuY29yZS5kZWJ1Zy53YXJuKCdSZWdpc3RyYXRpb24gb2YgbW9kdWxlIFwiJyArIG5hbWUgKyAnXCIgc2tpcHBlZCwgYmVjYXVzZSBpdCBhbHJlYWR5IGV4aXN0cy4nKTtcclxuXHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIFN0b3JlIHRoZSBtb2R1bGUgdG8gY2FjaGUgc28gdGhhdCBpdCBjYW4gYmUgdXNlZCBsYXRlci5cclxuXHRcdGNvbGxlY3Rpb24uY2FjaGUubW9kdWxlc1tuYW1lXSA9IHtcclxuXHRcdFx0Y29kZTogY29kZSxcclxuXHRcdFx0ZGVwZW5kZW5jaWVzOiBkZXBlbmRlbmNpZXNcclxuXHRcdH07XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogW0RFUFJFQ0FURURdIFJlZ2lzdGVyIGEgbW9kdWxlIGRlZmluaXRpb25cclxuXHQgKlxyXG5cdCAqIFRoaXMgbWV0aG9kIGV4aXN0cyBvbmx5IGZvciBmYWxsYmFjayBzdXBwb3J0LiBJdCB3aWxsIGJlIHJlbW92ZWQgaW4gdGhlIGZ1dHVyZSBzbyBwbGVhc2VcclxuXHQgKiB1c2UgdGhlIFwibW9kdWxlXCIgZnVuY3Rpb24gYWJvdmUuXHJcblx0ICpcclxuXHQgKiBAdG9kbyBSZW1vdmUgdGhlIGZhbGxiYWNrIGluIHRoZSBuZXh0IGVuZ2luZSB2ZXJzaW9uLlxyXG5cdCAqXHJcblx0ICogQGRlcHJlY2F0ZWQgc2luY2UgdmVyc2lvbiAxLjIuMFxyXG5cdCAqL1xyXG5cdENvbGxlY3Rpb24ucHJvdG90eXBlLnJlZ2lzdGVyID0gZnVuY3Rpb24obmFtZSwgdmVyc2lvbiwgZGVwZW5kZW5jaWVzLCBjb2RlLCBkZWZhdWx0cykge1xyXG5cdFx0anNlLmNvcmUuZGVidWcud2FybignVGhlIENvbGxlY3Rpb24ucHJvdG90eXBlLnJlZ2lzdGVyKCkgbWV0aG9kIGlzIGRlcHJlY2F0ZWQgYXMgb2YgdjEuMi4wLCB1c2UgdGhlICdcclxuXHRcdCAgICAgICAgICAgICAgICAgICAgKyAnQ29sbGVjdGlvbi5wcm90b3R5cGUubW9kdWxlKCkgbWV0aG9kIGluc3RlYWQgLS0gJyArIG5hbWUpO1xyXG5cdFx0dGhpcy5tb2R1bGUobmFtZSwgZGVwZW5kZW5jaWVzLCBjb2RlKTtcclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBJbml0aWFsaXplIE1vZHVsZSBDb2xsZWN0aW9uXHJcblx0ICpcclxuXHQgKiBUaGlzIG1ldGhvZCB3aWxsIHRyaWdnZXIgdGhlIHBhZ2UgbW9kdWxlcyBpbml0aWFsaXphdGlvbi4gSXQgd2lsbCBzZWFyY2ggYWxsXHJcblx0ICogdGhlIERPTSBmb3IgdGhlIFwiZGF0YS1neC1leHRlbnNpb25cIiwgXCJkYXRhLWd4LWNvbnRyb2xsZXJcIiBvclxyXG5cdCAqIFwiZGF0YS1neC13aWRnZXRcIiBhdHRyaWJ1dGVzIGFuZCBsb2FkIHRoZSByZWxldmFudCBzY3JpcHRzIHRocm91Z2ggUmVxdWlyZUpTLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtvYmplY3R9ICRwYXJlbnQgUGFyZW50IGVsZW1lbnQgd2lsbCBiZSB1c2VkIHRvIHNlYXJjaCBmb3IgdGhlIHJlcXVpcmVkIG1vZHVsZXMuXHJcblx0ICogQHBhcmFtIHtvYmplY3R9IG5hbWVzcGFjZURlZmVycmVkIERlZmVycmVkIG9iamVjdCB0aGF0IGdldHMgcHJvY2Vzc2VkIGFmdGVyIHRoZSBtb2R1bGUgaW5pdGlhbGl6YXRpb24gaXMgZmluaXNoZWQuXHJcblx0ICpcclxuXHQgKiBAbmFtZSBjb3JlL2VuZ2luZS5pbml0XHJcblx0ICogQHB1YmxpY1xyXG5cdCAqIEBtZXRob2RcclxuXHQgKi9cclxuXHRDb2xsZWN0aW9uLnByb3RvdHlwZS5pbml0ID0gZnVuY3Rpb24oJHBhcmVudCwgbmFtZXNwYWNlRGVmZXJyZWQpIHtcclxuXHRcdC8vIENvbGxlY3Rpb24gaW5zdGFuY2UgYWxpYXMuXHJcblx0XHR2YXIgY29sbGVjdGlvbiA9IHRoaXM7XHJcblxyXG5cdFx0Ly8gU3RvcmUgdGhlIG5hbWVzcGFjZXMgcmVmZXJlbmNlIG9mIHRoZSBjb2xsZWN0aW9uLlxyXG5cdFx0aWYgKCFjb2xsZWN0aW9uLm5hbWVzcGFjZSkge1xyXG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ0NvbGxlY3Rpb24gY2Fubm90IGJlIGluaXRpYWxpemVkIHdpdGhvdXQgaXRzIHBhcmVudCBuYW1lc3BhY2UgaW5zdGFuY2UuJyk7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gU2V0IHRoZSBkZWZhdWx0IHBhcmVudC1vYmplY3QgaWYgbm9uZSB3YXMgZ2l2ZW4uXHJcblx0XHRpZiAodHlwZW9mICRwYXJlbnQgPT09ICd1bmRlZmluZWQnIHx8ICRwYXJlbnQgPT09IG51bGwpIHtcclxuXHRcdFx0JHBhcmVudCA9ICQoJ2h0bWwnKTtcclxuXHRcdH1cclxuXHJcblx0XHR2YXIgYXR0cmlidXRlID0gJ2RhdGEtJyArIGNvbGxlY3Rpb24ubmFtZXNwYWNlLm5hbWUgKyAnLScgKyBjb2xsZWN0aW9uLmF0dHJpYnV0ZSxcclxuXHRcdFx0ZGVmZXJyZWRDb2xsZWN0aW9uID0gW107XHJcblxyXG5cdFx0JHBhcmVudFxyXG5cdFx0XHQuZmlsdGVyKCdbJyArIGF0dHJpYnV0ZSArICddJylcclxuXHRcdFx0LmFkZCgkcGFyZW50LmZpbmQoJ1snICsgYXR0cmlidXRlICsgJ10nKSlcclxuXHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XHJcblx0XHRcdFx0dmFyICRlbGVtZW50ID0gJCh0aGlzKSxcclxuXHRcdFx0XHRcdG1vZHVsZXMgPSAkZWxlbWVudC5hdHRyKGF0dHJpYnV0ZSk7XHJcblxyXG5cdFx0XHRcdCRlbGVtZW50LnJlbW92ZUF0dHIoYXR0cmlidXRlKTtcclxuXHRcdFx0XHQkLmVhY2gobW9kdWxlcy50cmltKCkuc3BsaXQoJyAnKSwgZnVuY3Rpb24oaW5kZXgsIG5hbWUpIHtcclxuXHRcdFx0XHRcdHZhciBkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcclxuXHRcdFx0XHRcdGRlZmVycmVkQ29sbGVjdGlvbi5wdXNoKGRlZmVycmVkKTtcclxuXHJcblx0XHRcdFx0XHRqc2UuY29yZS5tb2R1bGVfbG9hZGVyXHJcblx0XHRcdFx0XHQgICAubG9hZCgkZWxlbWVudCwgbmFtZSwgY29sbGVjdGlvbilcclxuXHRcdFx0XHRcdCAgIC5kb25lKGZ1bmN0aW9uKG1vZHVsZSkge1xyXG5cdFx0XHRcdFx0XHQgICBtb2R1bGUuaW5pdChkZWZlcnJlZCk7XHJcblx0XHRcdFx0XHQgICB9KVxyXG5cdFx0XHRcdFx0ICAgLmZhaWwoZnVuY3Rpb24oZXJyb3IpIHtcclxuXHRcdFx0XHRcdFx0ICAgZGVmZXJyZWQucmVqZWN0KCk7XHJcblx0XHRcdFx0XHRcdCAgIC8vIExvZyB0aGUgZXJyb3IgaW4gdGhlIGNvbnNvbGUgYnV0IGRvIG5vdCBzdG9wIHRoZSBlbmdpbmUgZXhlY3V0aW9uLlxyXG5cdFx0XHRcdFx0XHQgICBqc2UuY29yZS5kZWJ1Zy5lcnJvcignQ291bGQgbm90IGxvYWQgbW9kdWxlOiAnICsgbmFtZSwgZXJyb3IpO1xyXG5cdFx0XHRcdFx0ICAgfSk7XHJcblx0XHRcdFx0fSk7XHJcblx0XHRcdH0pO1xyXG5cclxuXHRcdC8vIElmIGFuIG5hbWVzcGFjZURlZmVycmVkIGlzIGdpdmVuIHJlc29sdmUgb3IgcmVqZWN0IGl0IGRlcGVuZGluZyBvbiB0aGUgbW9kdWxlIGluaXRpYWxpemF0aW9uIHN0YXR1cy5cclxuXHRcdGlmIChuYW1lc3BhY2VEZWZlcnJlZCkge1xyXG5cdFx0XHRpZiAoZGVmZXJyZWRDb2xsZWN0aW9uLmxlbmd0aCA9PT0gMCAmJiBuYW1lc3BhY2VEZWZlcnJlZCkge1xyXG5cdFx0XHRcdG5hbWVzcGFjZURlZmVycmVkLnJlc29sdmUoKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0JC53aGVuLmFwcGx5KHVuZGVmaW5lZCwgZGVmZXJyZWRDb2xsZWN0aW9uKS5wcm9taXNlKClcclxuXHRcdFx0IC5kb25lKGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdCBuYW1lc3BhY2VEZWZlcnJlZC5yZXNvbHZlKCk7XHJcblx0XHRcdCB9KVxyXG5cdFx0XHQgLmZhaWwoZnVuY3Rpb24oKSB7XHJcblx0XHRcdFx0IG5hbWVzcGFjZURlZmVycmVkLmZhaWwoKTtcclxuXHRcdFx0IH0pO1xyXG5cdFx0fVxyXG5cdH07XHJcblxyXG5cdGpzZS5jb25zdHJ1Y3RvcnMuQ29sbGVjdGlvbiA9IENvbGxlY3Rpb247XHJcbn0pKCk7XHJcbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBtb2R1bGUuanMgMjAxNi0wMi0wNFxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiAqL1xyXG5cclxuKGZ1bmN0aW9uKCkge1xyXG5cclxuXHQndXNlIHN0cmljdCc7XHJcblxyXG5cdC8qKlxyXG5cdCAqIENsYXNzIE1vZHVsZVxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtvYmplY3R9ICRlbGVtZW50IE1vZHVsZSBlbGVtZW50IHNlbGVjdG9yIG9iamVjdC5cclxuXHQgKiBAcGFyYW0ge3N0cmluZ30gbmFtZSBUaGUgbW9kdWxlIG5hbWUgKG1pZ2h0IGNvbnRhaW4gdGhlIHBhdGgpXHJcblx0ICogQHBhcmFtIHtvYmplY3R9IGNvbGxlY3Rpb24gVGhlIGNvbGxlY3Rpb24gaW5zdGFuY2Ugb2YgdGhlIG1vZHVsZS5cclxuXHQgKlxyXG5cdCAqIEBjb25zdHJ1Y3RvciBKU0UvQ29yZS9Nb2R1bGVcclxuXHQgKi9cclxuXHRmdW5jdGlvbiBNb2R1bGUoJGVsZW1lbnQsIG5hbWUsIGNvbGxlY3Rpb24pIHtcclxuXHRcdHRoaXMuJGVsZW1lbnQgPSAkZWxlbWVudDtcclxuXHRcdHRoaXMubmFtZSA9IG5hbWU7XHJcblx0XHR0aGlzLmNvbGxlY3Rpb24gPSBjb2xsZWN0aW9uO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogSW5pdGlhbGl6ZSB0aGUgbW9kdWxlIGV4ZWN1dGlvbi5cclxuXHQgKlxyXG5cdCAqIFRoaXMgZnVuY3Rpb24gd2lsbCBleGVjdXRlIHRoZSBcImluaXRcIiBtZXRob2Qgb2YgZWFjaCBtb2R1bGUuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge29iamVjdH0gY29sbGVjdGlvbkRlZmVycmVkIERlZmVycmVkIG9iamVjdCB0aGF0IGdldHMgcHJvY2Vzc2VkIGFmdGVyIHRoZSBtb2R1bGUgaW5pdGlhbGl6YXRpb24gaXMgZmluaXNoZWRcclxuXHQgKlxyXG5cdCAqIEBwdWJsaWNcclxuXHQgKi9cclxuXHRNb2R1bGUucHJvdG90eXBlLmluaXQgPSBmdW5jdGlvbihjb2xsZWN0aW9uRGVmZXJyZWQpIHtcclxuXHRcdC8vIFN0b3JlIG1vZHVsZSBpbnN0YW5jZSBhbGlhcy5cclxuXHRcdHZhciBtb2R1bGUgPSB0aGlzLFxyXG5cdFx0XHRjYWNoZWQgPSBtb2R1bGUuY29sbGVjdGlvbi5jYWNoZS5tb2R1bGVzW21vZHVsZS5uYW1lXSxcclxuXHRcdFx0cHJvbWlzZSA9IGNvbGxlY3Rpb25EZWZlcnJlZC5wcm9taXNlKCksXHJcblx0XHRcdHdhdGNoZG9nID0gbnVsbDtcclxuXHJcblx0XHR0cnkge1xyXG5cdFx0XHRpZiAoIWNhY2hlZCkge1xyXG5cdFx0XHRcdHRocm93IG5ldyBFcnJvcignTW9kdWxlIFwiJyArIG1vZHVsZS5uYW1lICsgJ1wiIGNvdWxkIG5vdCBiZSBmb3VuZCBpbiB0aGUgY29sbGVjdGlvbiBjYWNoZS4nKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0dmFyIGRhdGEgPSBfZ2V0TW9kdWxlRGF0YShtb2R1bGUpLFxyXG5cdFx0XHRcdGluc3RhbmNlID0gY2FjaGVkLmNvZGUuY2FsbChtb2R1bGUuJGVsZW1lbnQsIGRhdGEpO1xyXG5cclxuXHRcdFx0Ly8gUHJvdmlkZSBhIGRvbmUgZnVuY3Rpb24gdGhhdCBuZWVkcyB0byBiZSBjYWxsZWQgZnJvbSB0aGUgbW9kdWxlLCBpbiBvcmRlciB0byBpbmZvcm0gdGhhdCB0aGUgbW9kdWxlIFxyXG5cdFx0XHQvLyBcImluaXRcIiBmdW5jdGlvbiB3YXMgY29tcGxldGVkIHN1Y2Nlc3NmdWxseS5cclxuXHRcdFx0dmFyIGRvbmUgPSBmdW5jdGlvbigpIHtcclxuXHRcdFx0XHRtb2R1bGUuJGVsZW1lbnQudHJpZ2dlcignbW9kdWxlLmluaXRpYWxpemVkJywgW1xyXG5cdFx0XHRcdFx0e1xyXG5cdFx0XHRcdFx0XHRtb2R1bGU6IG1vZHVsZS5uYW1lXHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XSk7XHJcblx0XHRcdFx0anNlLmNvcmUuZGVidWcuaW5mbygnTW9kdWxlIFwiJyArIG1vZHVsZS5uYW1lICsgJ1wiIGluaXRpYWxpemVkIHN1Y2Nlc3NmdWxseS4nKTtcclxuXHRcdFx0XHRjb2xsZWN0aW9uRGVmZXJyZWQucmVzb2x2ZSgpO1xyXG5cdFx0XHRcdGNsZWFyVGltZW91dCh3YXRjaGRvZyk7XHJcblx0XHRcdH07XHJcblxyXG5cdFx0XHQvLyBGYWxsYmFjayBzdXBwb3J0IGZvciB0aGUgX2luaXRGaW5pc2hlZCBmdW5jdGlvbi5cclxuXHRcdFx0Ly8gQHRvZG8gUmVtb3ZlIHRoZSBmYWxsYmFjayBpbiB0aGUgbmV4dCBlbmdpbmUgdmVyc2lvbi5cclxuXHRcdFx0aW5zdGFuY2UuX2luaXRGaW5pc2hlZCA9IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdGpzZS5jb3JlLmRlYnVnLndhcm4oJ1RoZSBcIl9pbml0RmluaXNoZWRcIiBmdW5jdGlvbiBpcyBkZXByZWNhdGVkIGFzIG9mIHYxLjIuMCwgdXNlIHRoZSBuZXcgJ1xyXG5cdFx0XHRcdCAgICAgICAgICAgICAgICAgICAgKyAnbW9kdWxlIGluaXRpYWxpemF0aW9uIGluc3RlYWQgLS0gJyArIG1vZHVsZS5uYW1lKTtcclxuXHRcdFx0XHRkb25lKCk7XHJcblx0XHRcdH07XHJcblxyXG5cdFx0XHQvLyBGYWxsYmFjayBzdXBwb3J0IGZvciB0aGUgX2RhdGEgZnVuY3Rpb24uXHJcblx0XHRcdC8vIEB0b2RvIFJlbW92ZSB0aGUgZmFsbGJhY2sgaW4gdGhlIG5leHQgZW5naW5lIHZlcnNpb24uXHJcblx0XHRcdGluc3RhbmNlLl9kYXRhID0gZnVuY3Rpb24oJGVsZW1lbnQpIHtcclxuXHRcdFx0XHRqc2UuY29yZS5kZWJ1Zy53YXJuKCdUaGUgXCJfZGF0YVwiIGZ1bmN0aW9uIGlzIGRlcHJlY2F0ZWQgYXMgb2YgdjEuMi4wLCB1c2UgalF1ZXJ5IGRhdGEoKSAnXHJcblx0XHRcdFx0ICAgICAgICAgICAgICAgICAgICArICdmdW5jdGlvbiBpbnN0ZWFkIC0tICcgKyBtb2R1bGUubmFtZSk7XHJcblxyXG5cdFx0XHRcdHZhciBpbml0aWFsRGF0YSA9ICRlbGVtZW50LmRhdGEoKSxcclxuXHRcdFx0XHRcdGZpbHRlcmVkRGF0YSA9IHt9O1xyXG5cclxuXHRcdFx0XHQvLyBTZWFyY2hlcyBmb3IgbW9kdWxlIHJlbGV2YW50IGRhdGEgaW5zaWRlIHRoZSBtYWluLWRhdGEtb2JqZWN0LlxyXG5cdFx0XHRcdC8vIERhdGEgZm9yIG90aGVyIHdpZGdldHMgd2lsbCBub3QgZ2V0IHBhc3NlZCB0byB0aGlzIHdpZGdldFxyXG5cdFx0XHRcdCQuZWFjaChpbml0aWFsRGF0YSwgZnVuY3Rpb24oa2V5LCB2YWx1ZSkge1xyXG5cdFx0XHRcdFx0aWYgKGtleS5pbmRleE9mKG1vZHVsZS5uYW1lKSA9PT0gMCB8fCBrZXkuaW5kZXhPZihtb2R1bGUubmFtZS50b0xvd2VyQ2FzZSgpKSA9PT0gMCkge1xyXG5cdFx0XHRcdFx0XHR2YXIgbmV3S2V5ID0ga2V5LnN1YnN0cihtb2R1bGUubmFtZS5sZW5ndGgpO1xyXG5cdFx0XHRcdFx0XHRuZXdLZXkgPSBuZXdLZXkuc3Vic3RyKDAsIDEpLnRvTG93ZXJDYXNlKCkgKyBuZXdLZXkuc3Vic3RyKDEpO1xyXG5cdFx0XHRcdFx0XHRmaWx0ZXJlZERhdGFbbmV3S2V5XSA9IHZhbHVlO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0pO1xyXG5cclxuXHRcdFx0XHRyZXR1cm4gZmlsdGVyZWREYXRhO1xyXG5cdFx0XHR9O1xyXG5cclxuXHRcdFx0Ly8gTG9hZCB0aGUgbW9kdWxlIGRhdGEgYmVmb3JlIHRoZSBtb2R1bGUgaXMgbG9hZGVkLlxyXG5cdFx0XHRfbG9hZE1vZHVsZURhdGEoaW5zdGFuY2UpXHJcblx0XHRcdFx0LmRvbmUoZnVuY3Rpb24oKSB7XHJcblx0XHRcdFx0XHRfc3luY0xpYnNGYWxsYmFjaygpO1xyXG5cdFx0XHRcdFx0XHJcblx0XHRcdFx0XHQvLyBSZWplY3QgdGhlIGNvbGxlY3Rpb25EZWZlcnJlZCBpZiB0aGUgbW9kdWxlIGlzbid0IGluaXRpYWxpemVkIGFmdGVyIDE1IHNlY29uZHMuXHJcblx0XHRcdFx0XHR3YXRjaGRvZyA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XHJcblx0XHRcdFx0XHRcdGpzZS5jb3JlLmRlYnVnLndhcm4oJ01vZHVsZSB3YXMgbm90IGluaXRpYWxpemVkIGFmdGVyIDE1IHNlY29uZHMhIC0tICcgKyBtb2R1bGUubmFtZSk7XHJcblx0XHRcdFx0XHRcdGNvbGxlY3Rpb25EZWZlcnJlZC5yZWplY3QoKTtcclxuXHRcdFx0XHRcdH0sIDE1MDAwKTtcclxuXHRcdFx0XHRcdFxyXG5cdFx0XHRcdFx0aW5zdGFuY2UuaW5pdChkb25lKTtcclxuXHRcdFx0XHR9KVxyXG5cdFx0XHRcdC5mYWlsKGZ1bmN0aW9uKGVycm9yKSB7XHJcblx0XHRcdFx0XHRjb2xsZWN0aW9uRGVmZXJyZWQucmVqZWN0KCk7XHJcblx0XHRcdFx0XHRqc2UuY29yZS5kZWJ1Zy5lcnJvcignQ291bGQgbm90IGxvYWQgbW9kdWxlXFwncyBtZXRhIGRhdGEuJywgZXJyb3IpO1xyXG5cdFx0XHRcdH0pO1xyXG5cdFx0fSBjYXRjaCAoZXhjZXB0aW9uKSB7XHJcblx0XHRcdGNvbGxlY3Rpb25EZWZlcnJlZC5yZWplY3QoKTtcclxuXHRcdFx0anNlLmNvcmUuZGVidWcuZXJyb3IoJ0Nhbm5vdCBpbml0aWFsaXplIG1vZHVsZSBcIicgKyBtb2R1bGUubmFtZSArICdcIicsIGV4Y2VwdGlvbik7XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuIHByb21pc2U7XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogUGFyc2UgdGhlIG1vZHVsZSBkYXRhIGF0dHJpYnV0ZXMuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge29iamVjdH0gbW9kdWxlIFRoZSBtb2R1bGUgaW5zdGFuY2UgdG8gYmUgcGFyc2VkLlxyXG5cdCAqXHJcblx0ICogQHJldHVybnMge29iamVjdH0gUmV0dXJucyBhbiBvYmplY3QgdGhhdCBjb250YWlucyB0aGUgZGF0YSBvZiB0aGUgbW9kdWxlLlxyXG5cdCAqXHJcblx0ICogQHByaXZhdGVcclxuXHQgKi9cclxuXHR2YXIgX2dldE1vZHVsZURhdGEgPSBmdW5jdGlvbihtb2R1bGUpIHtcclxuXHRcdHZhciBkYXRhID0ge307XHJcblxyXG5cdFx0JC5lYWNoKG1vZHVsZS4kZWxlbWVudC5kYXRhKCksIGZ1bmN0aW9uKG5hbWUsIHZhbHVlKSB7XHJcblx0XHRcdGlmIChuYW1lLmluZGV4T2YobW9kdWxlLm5hbWUpID09PSAwIHx8IG5hbWUuaW5kZXhPZihtb2R1bGUubmFtZS50b0xvd2VyQ2FzZSgpKSA9PT0gMCkge1xyXG5cdFx0XHRcdHZhciBrZXkgPSBuYW1lLnN1YnN0cihtb2R1bGUubmFtZS5sZW5ndGgpO1xyXG5cdFx0XHRcdGtleSA9IGtleS5zdWJzdHIoMCwgMSkudG9Mb3dlckNhc2UoKSArIGtleS5zdWJzdHIoMSk7XHJcblx0XHRcdFx0ZGF0YVtrZXldID0gdmFsdWU7XHJcblx0XHRcdFx0bW9kdWxlLiRlbGVtZW50LnJlbW92ZUF0dHIoJ2RhdGEtJyArIG1vZHVsZS5uYW1lICsgJy0nICsga2V5KTtcclxuXHRcdFx0fVxyXG5cdFx0fSk7XHJcblxyXG5cdFx0cmV0dXJuIGRhdGE7XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogTW9kdWxlcyByZXR1cm4gb2JqZWN0cyB3aGljaCBtaWdodCBjb250YWluIHJlcXVpcmVtZW50cy5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBpbnN0YW5jZSBNb2R1bGUgaW5zdGFuY2Ugb2JqZWN0LlxyXG5cdCAqXHJcblx0ICogQHJldHVybiB7b2JqZWN0fSBSZXR1cm5zIGEgcHJvbWlzZSBvYmplY3QgdGhhdCB3aWxsIGJlIHJlc29sdmVkIHdoZW4gdGhlIGRhdGEgYXJlIGZldGNoZWQuXHJcblx0ICpcclxuXHQgKiBAcHJpdmF0ZVxyXG5cdCAqL1xyXG5cdHZhciBfbG9hZE1vZHVsZURhdGEgPSBmdW5jdGlvbihpbnN0YW5jZSkge1xyXG5cdFx0dmFyIGRlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xyXG5cclxuXHRcdHRyeSB7XHJcblx0XHRcdHZhciBwcm9taXNlcyA9IFtdO1xyXG5cclxuXHRcdFx0aWYgKGluc3RhbmNlLm1vZGVsKSB7XHJcblx0XHRcdFx0JC5lYWNoKGluc3RhbmNlLm1vZGVsLCBmdW5jdGlvbihpbmRleCwgdXJsKSB7XHJcblx0XHRcdFx0XHR2YXIgbW9kZWxEZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcclxuXHRcdFx0XHRcdHByb21pc2VzLnB1c2gobW9kZWxEZWZlcnJlZCk7XHJcblx0XHRcdFx0XHQkLmdldEpTT04odXJsKVxyXG5cdFx0XHRcdFx0IC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XHJcblx0XHRcdFx0XHRcdCBpbnN0YW5jZS5tb2RlbFtpbmRleF0gPSByZXNwb25zZTtcclxuXHRcdFx0XHRcdFx0IG1vZGVsRGVmZXJyZWQucmVzb2x2ZShyZXNwb25zZSk7XHJcblx0XHRcdFx0XHQgfSlcclxuXHRcdFx0XHRcdCAuZmFpbChmdW5jdGlvbihlcnJvcikge1xyXG5cdFx0XHRcdFx0XHQgbW9kZWxEZWZlcnJlZC5yZWplY3QoZXJyb3IpO1xyXG5cdFx0XHRcdFx0IH0pO1xyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRpZiAoaW5zdGFuY2Uudmlldykge1xyXG5cdFx0XHRcdCQuZWFjaChpbnN0YW5jZS52aWV3LCBmdW5jdGlvbihpbmRleCwgdXJsKSB7XHJcblx0XHRcdFx0XHR2YXIgdmlld0RlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xyXG5cdFx0XHRcdFx0cHJvbWlzZXMucHVzaCh2aWV3RGVmZXJyZWQpO1xyXG5cdFx0XHRcdFx0JC5nZXQodXJsKVxyXG5cdFx0XHRcdFx0IC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XHJcblx0XHRcdFx0XHRcdCBpbnN0YW5jZS52aWV3W2luZGV4XSA9IHJlc3BvbnNlO1xyXG5cdFx0XHRcdFx0XHQgdmlld0RlZmVycmVkLnJlc29sdmUocmVzcG9uc2UpO1xyXG5cdFx0XHRcdFx0IH0pXHJcblx0XHRcdFx0XHQgLmZhaWwoZnVuY3Rpb24oZXJyb3IpIHtcclxuXHRcdFx0XHRcdFx0IHZpZXdEZWZlcnJlZC5yZWplY3QoZXJyb3IpO1xyXG5cdFx0XHRcdFx0IH0pO1xyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQkLndoZW5cclxuXHRcdFx0IC5hcHBseSh1bmRlZmluZWQsIHByb21pc2VzKVxyXG5cdFx0XHQgLnByb21pc2UoKVxyXG5cdFx0XHQgLmRvbmUoZnVuY3Rpb24oKSB7XHJcblx0XHRcdFx0IGRlZmVycmVkLnJlc29sdmUoKTtcclxuXHRcdFx0IH0pXHJcblx0XHRcdCAuZmFpbChmdW5jdGlvbihlcnJvcikge1xyXG5cdFx0XHRcdCBkZWZlcnJlZC5yZWplY3QobmV3IEVycm9yKCdDYW5ub3QgbG9hZCBkYXRhIGZvciBtb2R1bGUgXCInICsgbW9kdWxlLm5hbWUgKyAnXCIuJywgZXJyb3IpKTtcclxuXHRcdFx0IH0pO1xyXG5cdFx0fSBjYXRjaCAoZXhjZXB0aW9uKSB7XHJcblx0XHRcdGRlZmVycmVkLnJlc29sdmUoZXhjZXB0aW9uKTtcclxuXHRcdH1cclxuXHJcblx0XHRyZXR1cm4gZGVmZXJyZWQucHJvbWlzZSgpO1xyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIEVuZ2luZSBMaWJzIGZhbGxiYWNrIGRlZmluaXRpb24uXHJcblx0ICpcclxuXHQgKiBPbGQgbW9kdWxlcyB1c2UgdGhlIGxpYnMgdW5kZXIgdGhlIHdpbmRvdy5neC5saWJzIG9iamVjdC4gVGhpcyBtZXRob2Qgd2lsbCBtYWtlIHN1cmVcclxuXHQgKiB0aGF0IHRoaXMgb2JqZWN0IGlzIHN5bmNocm9uaXplZCB3aXRoIHRoZSBqc2UubGlicyB1bnRpbCBldmVyeSBvbGQgbW9kdWxlIGRlZmluaXRpb24gaXNcclxuXHQgKiB1cGRhdGVkLlxyXG5cdCAqXHJcblx0ICogQHRvZG8gUmVtb3ZlIHRoZSBmYWxsYmFjayBpbiB0aGUgbmV4dCBlbmdpbmUgdmVyc2lvbi5cclxuXHQgKlxyXG5cdCAqIEBwcml2YXRlXHJcblx0ICovXHJcblx0dmFyIF9zeW5jTGlic0ZhbGxiYWNrID0gZnVuY3Rpb24oKSB7XHJcblx0XHRpZiAodHlwZW9mIHdpbmRvdy5neCA9PT0gJ3VuZGVmaW5lZCcpIHtcclxuXHRcdFx0d2luZG93Lmd4ID0ge307XHJcblx0XHR9XHJcblx0XHR3aW5kb3cuZ3gubGlicyA9IGpzZS5saWJzO1xyXG5cdH07XHJcblxyXG5cdGpzZS5jb25zdHJ1Y3RvcnMuTW9kdWxlID0gTW9kdWxlO1xyXG59KSgpO1xyXG4iLCIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gbmFtZXNwYWNlLmpzIDIwMTUtMTAtMTMgZ21cclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG4oZnVuY3Rpb24gKCkge1xyXG5cclxuXHQndXNlIHN0cmljdCc7XHJcblxyXG5cdC8qKlxyXG5cdCAqIENsYXNzIE5hbWVzcGFjZVxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtzdHJpbmd9IG5hbWUgVGhlIG5hbWVzcGFjZSBuYW1lIG11c3QgYmUgdW5pcXVlIHdpdGhpbiB0aGUgYXBwLlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBzb3VyY2UgQ29tcGxldGUgVVJMIHRvIHRoZSBuYW1lc3BhY2UgbW9kdWxlcyBkaXJlY3RvcnkgKHdpdGhvdXQgdHJhaWxpbmcgc2xhc2gpLlxyXG5cdCAqIEBwYXJhbSB7YXJyYXl9IGNvbGxlY3Rpb25zIENvbnRhaW5zIGNvbGxlY3Rpb24gaW5zdGFuY2VzIHRvIGJlIGluY2x1ZGVkIGluIHRoZSBuYW1lc3BhY2UuXHJcblx0ICpcclxuXHQgKiBAY29uc3RydWN0b3IgSlNFL0NvcmUvTmFtZXNwYWNlXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gTmFtZXNwYWNlKG5hbWUsIHNvdXJjZSwgY29sbGVjdGlvbnMpIHtcclxuXHRcdHRoaXMubmFtZSA9IG5hbWU7XHJcblx0XHR0aGlzLnNvdXJjZSA9IHNvdXJjZTtcclxuXHRcdHRoaXMuY29sbGVjdGlvbnMgPSBjb2xsZWN0aW9uczsgLy8gY29udGFpbnMgdGhlIGRlZmF1bHQgaW5zdGFuY2VzXHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBJbml0aWFsaXplIHRoZSBuYW1lc3BhY2UgY29sbGVjdGlvbnMuXHJcblx0ICpcclxuXHQgKiBUaGlzIG1ldGhvZCB3aWxsIGNyZWF0ZSBuZXcgY29sbGVjdGlvbiBpbnN0YW5jZXMgYmFzZWQgaW4gdGhlIG9yaWdpbmFsIG9uZXMuXHJcblx0ICovXHJcblx0TmFtZXNwYWNlLnByb3RvdHlwZS5pbml0ID0gZnVuY3Rpb24gKCkge1xyXG5cdFx0dmFyIGRlZmVycmVkQ29sbGVjdGlvbiA9IFtdO1xyXG5cclxuXHRcdGZvciAodmFyIGluZGV4IGluIHRoaXMuY29sbGVjdGlvbnMpIHtcclxuXHRcdFx0dmFyIGNvbGxlY3Rpb24gPSB0aGlzLmNvbGxlY3Rpb25zW2luZGV4XSxcclxuXHRcdFx0ICAgIGRlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xyXG5cclxuXHRcdFx0ZGVmZXJyZWRDb2xsZWN0aW9uLnB1c2goZGVmZXJyZWQpO1xyXG5cdFx0XHRcclxuXHRcdFx0dGhpc1tjb2xsZWN0aW9uLm5hbWVdID0gbmV3IGpzZS5jb25zdHJ1Y3RvcnMuQ29sbGVjdGlvbihjb2xsZWN0aW9uLm5hbWUsIGNvbGxlY3Rpb24uYXR0cmlidXRlLCB0aGlzKTtcclxuXHRcdFx0dGhpc1tjb2xsZWN0aW9uLm5hbWVdLmluaXQobnVsbCwgZGVmZXJyZWQpO1xyXG5cdFx0fVxyXG5cclxuXHRcdGlmIChkZWZlcnJlZENvbGxlY3Rpb24ubGVuZ3RoID09PSAwKSB7XHJcblx0XHRcdHJldHVybiAkLkRlZmVycmVkKCkucmVzb2x2ZSgpO1xyXG5cdFx0fVxyXG5cclxuXHRcdHJldHVybiAkLndoZW4uYXBwbHkodW5kZWZpbmVkLCBkZWZlcnJlZENvbGxlY3Rpb24pLnByb21pc2UoKTtcclxuXHJcblx0fTtcclxuXHJcblx0anNlLmNvbnN0cnVjdG9ycy5OYW1lc3BhY2UgPSBOYW1lc3BhY2U7XHJcbn0pKCk7XHJcbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBhYm91dC5qcyAyMDE1LTEwLTE0IGdtXHJcbiBHYW1iaW8gR21iSFxyXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcclxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxyXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXHJcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cclxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiAqL1xyXG5cclxuLyoqXHJcbiAqIEdldCBpbmZvcm1hdGlvbiBhYm91dCB0aGUgSlMgRW5naW5lLlxyXG4gKlxyXG4gKiBFeGVjdXRlIHRoZSBganNlLmFib3V0KClgIGNvbW1hbmQgYW5kIHlvdSB3aWxsIGdldCBhIG5ldyBsb2cgZW50cnkgaW4gdGhlXHJcbiAqIGNvbnNvbGUgd2l0aCBpbmZvIGFib3V0IHRoZSBlbmdpbmUuIFRoZSBcImFib3V0XCIgbWV0aG9kIGlzIG9ubHkgYXZhaWxhYmxlIGluXHJcbiAqIHRoZSBcImRldmVsb3BtZW50XCIgZW52aXJvbm1lbnQgb2YgdGhlIGVuZ2luZS5cclxuICpcclxuICogQG5hbWVzcGFjZSBKU0UvQ29yZS9hYm91dFxyXG4gKi9cclxuJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKSB7XHJcblxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHJcblx0aWYgKGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2Vudmlyb25tZW50JykgPT09ICdwcm9kdWN0aW9uJykge1xyXG5cdFx0cmV0dXJuO1xyXG5cdH1cclxuXHJcblx0anNlLmFib3V0ID0gZnVuY3Rpb24gKCkge1xyXG5cdFx0dmFyIGluZm8gPSBbXHJcblx0XHRcdCdKUyBFTkdJTkUgdicgKyBqc2UuY29yZS5jb25maWcuZ2V0KCd2ZXJzaW9uJykgKyAnIMKpIEdBTUJJTyBHTUJIJyxcclxuXHRcdFx0Jy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0nLFxyXG5cdFx0XHQnVGhlIEpTIEVuZ2luZSBlbmFibGVzIGRldmVsb3BlcnMgdG8gbG9hZCBhdXRvbWF0aWNhbGx5IHNtYWxsIHBpZWNlcyBvZiBqYXZhc2NyaXB0IGNvZGUgYnknLFxyXG5cdFx0XHQncGxhY2luZyBzcGVjaWZpYyBkYXRhIGF0dHJpYnV0ZXMgdG8gdGhlIEhUTUwgbWFya3VwIG9mIGEgcGFnZS4gSXQgd2FzIGJ1aWx0IHdpdGggbW9kdWxhcml0eScsXHJcblx0XHRcdCdpbiBtaW5kIHNvIHRoYXQgbW9kdWxlcyBjYW4gYmUgcmV1c2VkIGludG8gbXVsdGlwbGUgcGxhY2VzIHdpdGhvdXQgZXh0cmEgZWZmb3J0LiBUaGUgZW5naW5lJyxcclxuXHRcdFx0J2NvbnRhaW5zIG5hbWVzcGFjZXMgd2hpY2ggY29udGFpbiBjb2xsZWN0aW9ucyBvZiBtb2R1bGVzLCBlYWNoIG9uZSBvZiB3aG9tIHNlcnZlIGEgZGlmZmVyZW50JyxcclxuXHRcdFx0J2dlbmVyaWMgcHVycG9zZS4nLFxyXG5cdFx0XHQnJyxcclxuXHRcdFx0J1Zpc2l0IGh0dHA6Ly9kZXZlbG9wZXJzLmdhbWJpby5kZSBmb3IgY29tcGxldGUgcmVmZXJlbmNlIG9mIHRoZSBKUyBFbmdpbmUuJyxcclxuXHRcdFx0JycsXHJcblx0XHRcdCdGQUxMQkFDSyBJTkZPUk1BVElPTicsXHJcblx0XHRcdCctLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tJyxcclxuXHRcdFx0J1NpbmNlIHRoZSBlbmdpbmUgY29kZSBiZWNvbWVzIGJpZ2dlciB0aGVyZSBhcmUgc2VjdGlvbnMgdGhhdCBuZWVkIHRvIGJlIHJlZmFjdG9yZWQgaW4gb3JkZXInLFxyXG5cdFx0XHQndG8gYmVjb21lIG1vcmUgZmxleGlibGUuIEluIG1vc3QgY2FzZXMgYSB3YXJuaW5nIGxvZyB3aWxsIGJlIGRpc3BsYXllZCBhdCB0aGUgYnJvd3NlclxcJ3MgY29uc29sZScsXHJcblx0XHRcdCd3aGVuZXZlciB0aGVyZSBpcyBhIHVzZSBvZiBhIGRlcHJlY2F0ZWQgZnVuY3Rpb24uIEJlbG93IHRoZXJlIGlzIGEgcXVpY2sgbGlzdCBvZiBmYWxsYmFjayBzdXBwb3J0JyxcclxuXHRcdFx0J3RoYXQgd2lsbCBiZSByZW1vdmVkIGluIHRoZSBmdXR1cmUgdmVyc2lvbnMgb2YgdGhlIGVuZ2luZS4nLFxyXG5cdFx0XHQnJyxcclxuXHRcdFx0JzEuIFRoZSBtYWluIGVuZ2luZSBvYmplY3Qgd2FzIHJlbmFtZWQgZnJvbSBcImd4XCIgdG8gXCJqc2VcIiB3aGljaCBzdGFuZHMgZm9yIHRoZSBKYXZhU2NyaXB0IEVuZ2luZS4nLFxyXG5cdFx0XHQnMi4gVGhlIFwiZ3gubGliXCIgb2JqZWN0IGlzIHJlbW92ZWQgYWZ0ZXIgYSBsb25nIGRlcHJlY2F0aW9uIHBlcmlvZC4gWW91IHNob3VsZCB1cGRhdGUgdGhlIG1vZHVsZXMgJyxcclxuXHRcdFx0JyAgIHRoYXQgY29udGFpbmVkIGNhbGxzIHRvIHRoZSBmdW5jdGlvbnMgb2YgdGhpcyBvYmplY3QuJyxcclxuXHRcdFx0JzMuIFRoZSBneC48Y29sbGVjdGlvbi1uYW1lPi5yZWdpc3RlciBmdW5jdGlvbiBpcyBkZXByZWNhdGVkIGJ5IHYxLjIsIHVzZSB0aGUgJyxcclxuXHRcdFx0JyAgIDxuYW1lc3BhY2U+Ljxjb2xsZWN0aW9uPi5tb2R1bGUoKSBpbnN0ZWFkLidcclxuXHRcdF07XHJcblxyXG5cdFx0anNlLmNvcmUuZGVidWcuaW5mbyhpbmZvLmpvaW4oJ1xcbicpKTtcclxuXHR9O1xyXG5cclxufSk7XHJcbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBjb25maWcuanMgMjAxNS0xMC0yMCBnbVxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbmpzZS5jb3JlLmNvbmZpZyA9IGpzZS5jb3JlLmNvbmZpZyB8fCB7fTtcclxuXHJcbi8qKlxyXG4gKiBKUyBFbmdpbmUgQ29uZmlndXJhdGlvbiBPYmplY3RcclxuICpcclxuICogT25jZSB0aGUgY29uZmlnIG9iamVjdCBpcyBpbml0aWFsaXplZCB5b3UgY2Fubm90IGNoYW5nZSBpdHMgdmFsdWVzLiBUaGlzIGlzIGRvbmUgaW4gb3JkZXIgdG9cclxuICogcHJldmVudCB1bnBsZWFzYW50IHNpdHVhdGlvbnMgd2hlcmUgb25lIGNvZGUgc2VjdGlvbiBjaGFuZ2VzIGEgY29yZSBjb25maWcgc2V0dGluZyB0aGF0IGFmZmVjdHNcclxuICogYW5vdGhlciBjb2RlIHNlY3Rpb24gaW4gYSB3YXkgdGhhdCBpcyBoYXJkIHRvIGRpc2NvdmVyLlxyXG4gKlxyXG4gKiBgYGBqYXZhc2NyaXB0XHJcbiAqIHZhciBzaG9wVXJsID0ganNlLmNvcmUuY29uZmlnLmdldCgnc2hvcFVybCcpO1xyXG4gKiBgYGBcclxuICpcclxuICogQG5hbWVzcGFjZSBKU0UvQ29yZS9jb25maWdcclxuICovXHJcbihmdW5jdGlvbiAoZXhwb3J0cykge1xyXG5cclxuXHQndXNlIHN0cmljdCc7XHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIENPTkZJR1VSQVRJT04gVkFMVUVTXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cdHZhciBjb25maWcgPSB7XHJcblx0XHQvKipcclxuXHRcdCAqIEVuZ2luZSBWZXJzaW9uXHJcblx0XHQgKlxyXG5cdFx0ICogQHR5cGUge3N0cmluZ31cclxuXHRcdCAqL1xyXG5cdFx0dmVyc2lvbjogJzEuMi4wJyxcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFNob3AgVVJMXHJcblx0XHQgKlxyXG5cdFx0ICogZS5nLiAnaHR0cDovL3Nob3AuZGVcclxuXHRcdCAqXHJcblx0XHQgKiBAdHlwZSB7c3RyaW5nfVxyXG5cdFx0ICovXHJcblx0XHRzaG9wVXJsOiBudWxsLFxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogVVJMIHRvIEpTRW5naW5lIERpcmVjdG9yeS5cclxuXHRcdCAqXHJcblx0XHQgKiBlLmcuICdodHRwOi8vc2hvcC5kZS9KU0VuZ2luZVxyXG5cdFx0ICpcclxuXHRcdCAqIEB0eXBlIHtzdHJpbmd9XHJcblx0XHQgKi9cclxuXHRcdGVuZ2luZVVybDogbnVsbCxcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEVuZ2luZSBFbnZpcm9ubWVudFxyXG5cdFx0ICpcclxuXHRcdCAqIERlZmluZXMgdGhlIGZ1bmN0aW9uYWxpdHkgb2YgdGhlIGVuZ2luZSBpbiBtYW55IHNlY3Rpb25zLlxyXG5cdFx0ICpcclxuXHRcdCAqIEB0eXBlIHtzdHJpbmd9XHJcblx0XHQgKi9cclxuXHRcdGVudmlyb25tZW50OiAncHJvZHVjdGlvbicsXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBIVE1MIEF0dHJpYnV0ZSBQcmVmaXhcclxuXHRcdCAqXHJcblx0XHQgKiBUaGlzIHdpbGwgcHJlZml4IHRoZSBIVE1MIGF0dHJpYnV0ZXMgdGhhdCBoYXZlIGEgc3BlY2lhbCBtZWFuaW5nIHRvIHRoZSBKU0VuZ2luZS4gU2hvdWxkXHJcblx0XHQgKiBiZSBtb3N0bHkgYSBzaG9ydCBzdHJpbmcuXHJcblx0XHQgKlxyXG5cdFx0ICogQHR5cGUge3N0cmluZ31cclxuXHRcdCAqL1xyXG5cdFx0cHJlZml4OiAnZ3gnLFxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogVHJhbnNsYXRpb25zIE9iamVjdFxyXG5cdFx0ICpcclxuXHRcdCAqIENvbnRhaW5zIHRoZSBsb2FkZWQgdHJhbnNsYXRpb25zIHRvIGJlIHVzZWQgd2l0aGluIEpTRW5naW5lLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBzZWUgZ3gubGlicy5sYW5nIG9iamVjdFxyXG5cdFx0ICogQHR5cGUge29iamVjdH1cclxuXHRcdCAqL1xyXG5cdFx0dHJhbnNsYXRpb25zOiB7fSxcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEN1cnJlbnQgTGFuZ3VhZ2UgQ29kZVxyXG5cdFx0ICpcclxuXHRcdCAqIEB0eXBlIHtzdHJpbmd9XHJcblx0XHQgKi9cclxuXHRcdGxhbmd1YWdlQ29kZTogJycsXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBTZXQgdGhlIGRlYnVnIGxldmVsIHRvIG9uZSBvZiB0aGUgZm9sbG93aW5nOiAnREVCVUcnLCAnSU5GTycsICdMT0cnLCAnV0FSTicsICdFUlJPUicsICdBTEVSVCcsICdTSUxFTlQnXHJcblx0XHQgKlxyXG5cdFx0ICogQHR5cGUge3N0cmluZ31cclxuXHRcdCAqL1xyXG5cdFx0ZGVidWc6ICdTSUxFTlQnLFxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogVXNlIGNhY2hlIGJ1c3RpbmcgdGVjaG5pcXVlIHdoZW4gbG9hZGluZyBtb2R1bGVzLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBzZWUganNlLmNvcmUuZGVidWcgb2JqZWN0XHJcblx0XHQgKiBAdHlwZSB7Ym9vbH1cclxuXHRcdCAqL1xyXG5cdFx0Y2FjaGVCdXN0OiB0cnVlLFxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogTG9hZCBtaW5pZmllZCBmaWxlcy5cclxuXHRcdCAqXHJcblx0XHQgKiBAdHlwZSB7Ym9vbH1cclxuXHRcdCAqL1xyXG5cdFx0bWluaWZpZWQ6IHRydWUsXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBXaGV0aGVyIHRoZSBjbGllbnQgaGFzIGEgbW9iaWxlIGludGVyZmFjZS5cclxuXHRcdCAqXHJcblx0XHQgKiBAdHlwZSB7Ym9vbH1cclxuXHRcdCAqL1xyXG5cdFx0bW9iaWxlOiBmYWxzZSxcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFdoZXRoZXIgdGhlIGNsaWVudCBzdXBwb3J0cyB0b3VjaCBldmVudHMuXHJcblx0XHQgKlxyXG5cdFx0ICogQHR5cGUge2Jvb2x9XHJcblx0XHQgKi9cclxuXHRcdHRvdWNoOiAod2luZG93Lm9udG91Y2hzdGFydCB8fCB3aW5kb3cub25tc2dlc3R1cmVjaGFuZ2UpID8gdHJ1ZSA6IGZhbHNlLFxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogU3BlY2lmeSB0aGUgcGF0aCBmb3IgdGhlIGZpbGUgbWFuYWdlci5cclxuXHRcdCAqXHJcblx0XHQgKiBAdHlwZSB7c3RyaW5nfVxyXG5cdFx0ICovXHJcblx0XHRmaWxlbWFuYWdlcjogJ2luY2x1ZGVzL2NrZWRpdG9yL2ZpbGVtYW5hZ2VyL2luZGV4Lmh0bWwnLFxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogUGFnZSB0b2tlbiB0byBpbmNsdWRlIGluIGV2ZXJ5IEFKQVggcmVxdWVzdC5cclxuXHRcdCAqXHJcblx0XHQgKiBUaGUgcGFnZSB0b2tlbiBpcyB1c2VkIHRvIGF2b2lkIENTUkYgYXR0YWNrcy4gSXQgbXVzdCBiZSBwcm92aWRlZCBieSB0aGVcclxuXHRcdCAqIGJhY2tlbmQgYW5kIGl0IHdpbGwgYmUgdmFsaWRhdGVkIHRoZXJlLlxyXG5cdFx0ICpcclxuXHRcdCAqIEB0eXBlIHtzdHJpbmd9XHJcblx0XHQgKi9cclxuXHRcdHBhZ2VUb2tlbjogJycsXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBEZWZpbmVzIHdoZXRoZXIgdGhlIGhpc3Rvcnkgb2JqZWN0IGlzIGF2YWlsYWJsZS5cclxuXHRcdCAqL1xyXG5cdFx0aGlzdG9yeTogaGlzdG9yeSAmJiBoaXN0b3J5LnJlcGxhY2VTdGF0ZSAmJiBoaXN0b3J5LnB1c2hTdGF0ZVxyXG5cdH07XHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFBVQkxJQyBNRVRIT0RTXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cdC8qKlxyXG5cdCAqIEdldCBhIGNvbmZpZ3VyYXRpb24gdmFsdWUuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge3N0cmluZ30gbmFtZSBUaGUgY29uZmlndXJhdGlvbiB2YWx1ZSBuYW1lIHRvIGJlIHJldHJpZXZlZC5cclxuXHQgKlxyXG5cdCAqIEByZXR1cm5zIHsqfSBSZXR1cm5zIHRoZSBjb25maWcgdmFsdWUuXHJcblx0ICpcclxuXHQgKiBAbmFtZSBjb3JlL2NvbmZpZy5pbml0XHJcblx0ICogQHB1YmxpY1xyXG5cdCAqIEBtZXRob2RcclxuXHQgKi9cclxuXHRleHBvcnRzLmdldCA9IGZ1bmN0aW9uIChuYW1lKSB7XHJcblx0XHRyZXR1cm4gY29uZmlnW25hbWVdO1xyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIEluaXRpYWxpemUgdGhlIEpTIEVuZ2luZSBjb25maWcgb2JqZWN0LlxyXG5cdCAqXHJcblx0ICogVGhpcyBtZXRob2Qgd2lsbCBwYXJzZSB0aGUgZ2xvYmFsIFwiSlNFbmdpbmVDb25maWd1cmF0aW9uXCIgb2JqZWN0IGFuZCB0aGVuIHJlbW92ZVxyXG5cdCAqIGl0IGZyb20gdGhlIGdsb2JhbCBzY29wZSBzbyB0aGF0IGl0IGJlY29tZXMgdGhlIG9ubHkgY29uZmlnIHNvdXJjZSBmb3IgamF2YXNjcmlwdC5cclxuXHQgKlxyXG5cdCAqIE5vdGljZTogVGhlIG9ubHkgcmVxdWlyZWQgSlNFbmdpbmVDb25maWd1cmF0aW9uIHZhbHVlcyBhcmUgdGhlIFwiZW52aXJvbm1lbnRcIiBhbmQgdGhlIFwic2hvcFVybFwiLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtvYmplY3R9IGpzRW5naW5lQ29uZmlndXJhdGlvbiBNdXN0IGNvbnRhaW4gaW5mb3JtYXRpb24gdGhhdCBkZWZpbmUgY29yZSBvcGVyYXRpb25zXHJcblx0ICogb2YgdGhlIGVuZ2luZS4gQ2hlY2sgdGhlIFwibGlicy9pbml0aWFsaXplXCIgZW50cnkgb2YgdGhlIGVuZ2luZSBkb2N1bWVudGF0aW9uLlxyXG5cdCAqXHJcblx0ICogQG5hbWUgY29yZS9jb25maWcuaW5pdFxyXG5cdCAqIEBwdWJsaWNcclxuXHQgKiBAbWV0aG9kXHJcblx0ICovXHJcblx0ZXhwb3J0cy5pbml0ID0gZnVuY3Rpb24gKGpzRW5naW5lQ29uZmlndXJhdGlvbikge1xyXG5cdFx0Y29uZmlnLmVudmlyb25tZW50ID0ganNFbmdpbmVDb25maWd1cmF0aW9uLmVudmlyb25tZW50O1xyXG5cdFx0Y29uZmlnLnNob3BVcmwgPSBqc0VuZ2luZUNvbmZpZ3VyYXRpb24uc2hvcFVybDtcclxuXHJcblx0XHRpZiAoY29uZmlnLmVudmlyb25tZW50ID09PSAnZGV2ZWxvcG1lbnQnKSB7XHJcblx0XHRcdGNvbmZpZy5jYWNoZUJ1c3QgPSBmYWxzZTtcclxuXHRcdFx0Y29uZmlnLm1pbmlmaWVkID0gZmFsc2U7XHJcblx0XHRcdGNvbmZpZy5kZWJ1ZyA9ICdERUJVRyc7XHJcblx0XHR9XHJcblxyXG5cdFx0aWYgKHR5cGVvZiBqc0VuZ2luZUNvbmZpZ3VyYXRpb24uZW5naW5lVXJsICE9PSAndW5kZWZpbmVkJykge1xyXG5cdFx0XHRjb25maWcuZW5naW5lVXJsID0ganNFbmdpbmVDb25maWd1cmF0aW9uLmVuZ2luZVVybDtcclxuXHRcdH0gZWxzZSB7XHJcblx0XHRcdGNvbmZpZy5lbmdpbmVVcmwgPSBjb25maWcuc2hvcFVybCArICcvSlNFbmdpbmUvYnVpbGQnO1xyXG5cdFx0fVxyXG5cclxuXHRcdGlmICh0eXBlb2YganNFbmdpbmVDb25maWd1cmF0aW9uLnRyYW5zbGF0aW9ucyAhPT0gJ3VuZGVmaW5lZCcpIHtcclxuXHRcdFx0Y29uZmlnLnRyYW5zbGF0aW9ucyA9IGpzRW5naW5lQ29uZmlndXJhdGlvbi50cmFuc2xhdGlvbnM7XHJcblxyXG5cdFx0XHQkLmVhY2goY29uZmlnLnRyYW5zbGF0aW9ucywgZnVuY3Rpb24gKHNlY3Rpb25OYW1lLCBzZWN0aW9uVHJhbnNsYXRpb25zKSB7XHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy5hZGRTZWN0aW9uKHNlY3Rpb25OYW1lLCBzZWN0aW9uVHJhbnNsYXRpb25zKTtcclxuXHRcdFx0fSk7XHJcblx0XHR9XHJcblxyXG5cdFx0aWYgKHR5cGVvZiBqc0VuZ2luZUNvbmZpZ3VyYXRpb24ucHJlZml4ICE9PSAndW5kZWZpbmVkJykge1xyXG5cdFx0XHRjb25maWcucHJlZml4ID0ganNFbmdpbmVDb25maWd1cmF0aW9uLnByZWZpeDtcclxuXHRcdH1cclxuXHJcblx0XHRpZiAodHlwZW9mIGpzRW5naW5lQ29uZmlndXJhdGlvbi5sYW5ndWFnZUNvZGUgIT09ICd1bmRlZmluZWQnKSB7XHJcblx0XHRcdGNvbmZpZy5sYW5ndWFnZUNvZGUgPSBqc0VuZ2luZUNvbmZpZ3VyYXRpb24ubGFuZ3VhZ2VDb2RlO1xyXG5cdFx0fVxyXG5cclxuXHRcdGlmICh0eXBlb2YganNFbmdpbmVDb25maWd1cmF0aW9uLnBhZ2VUb2tlbiAhPT0gJ3VuZGVmaW5lZCcpIHtcclxuXHRcdFx0Y29uZmlnLnBhZ2VUb2tlbiA9IGpzRW5naW5lQ29uZmlndXJhdGlvbi5wYWdlVG9rZW47XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gSW5pdGlhbGl6ZSB0aGUgbW9kdWxlIGxvYWRlciBvYmplY3QuXHJcblx0XHRqc2UuY29yZS5tb2R1bGVfbG9hZGVyLmluaXQoKTtcclxuXHJcblx0XHQvLyBEZXN0cm95IGdsb2JhbCBFbmdpbmVDb25maWd1cmF0aW9uIG9iamVjdC5cclxuXHRcdGRlbGV0ZSB3aW5kb3cuSlNFbmdpbmVDb25maWd1cmF0aW9uO1xyXG5cdH07XHJcblxyXG59KGpzZS5jb3JlLmNvbmZpZykpO1xyXG4iLCIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gZGVidWcuanMgMjAxNS0xMC0xMyBnbVxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbmpzZS5jb3JlLmRlYnVnID0ganNlLmNvcmUuZGVidWcgfHwge307XHJcblxyXG4vKipcclxuICogSlMgRW5naW5lIGRlYnVnIG9iamVjdC5cclxuICpcclxuICogVGhpcyBvYmplY3QgcHJvdmlkZXMgYW4gd3JhcHBlciB0byB0aGUgY29uc29sZS5sb2cgZnVuY3Rpb24gYW5kIGVuYWJsZXMgZWFzeSB1c2VcclxuICogb2YgdGhlIGRpZmZlcmVudCBsb2cgdHlwZXMgbGlrZSBcImluZm9cIiwgXCJ3YXJuaW5nXCIsIFwiZXJyb3JcIiBldGMuXHJcbiAqXHJcbiAqIEBuYW1lc3BhY2UgSlNFL0NvcmUvZGVidWdcclxuICovXHJcbihmdW5jdGlvbiAoLyoqIEBsZW5kcyBKU0UvQ29yZS9kZWJ1ZyAqLyBleHBvcnRzKSB7XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cdC8qKlxyXG5cdCAqIEFsbCBwb3NzaWJsZSBkZWJ1ZyBsZXZlbHMgaW4gdGhlIG9yZGVyIG9mIGltcG9ydGFuY2UuXHJcblx0ICpcclxuXHQgKiBAbmFtZSBDb3JlL0RlYnVnLmxldmVsc1xyXG5cdCAqIEBwdWJsaWNcclxuXHQgKiBAdHlwZSB7YXJyYXl9XHJcblx0ICovXHJcblx0dmFyIGxldmVscyA9IFsnREVCVUcnLCAnSU5GTycsICdMT0cnLCAnV0FSTicsICdFUlJPUicsICdBTEVSVCcsICdTSUxFTlQnXTtcclxuXHJcblx0LyoqXHJcblx0ICogRXhlY3V0ZXMgdGhlIGNvcnJlY3QgY29uc29sZS9hbGVydCBzdGF0ZW1lbnQuXHJcblx0ICpcclxuXHQgKiBAbmFtZSBDb3JlL0RlYnVnLl9leGVjdXRlXHJcblx0ICogQHByaXZhdGVcclxuXHQgKiBAbWV0aG9kXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge29iamVjdH0gY2FsbGVyIChvcHRpb25hbCkgQ29udGFpbnMgdGhlIGNhbGxlciBpbmZvcm1hdGlvbiB0byBiZSBkaXNwbGF5ZWQuXHJcblx0ICogQHBhcmFtIHtvYmplY3R9IGRhdGEgKG9wdGlvbmFsKSBDb250YWlucyBhbnkgYWRkaXRpb25hbCBkYXRhIHRvIGJlIGluY2x1ZGVkIGluIHRoZSBkZWJ1ZyBvdXRwdXQuXHJcblx0ICovXHJcblx0dmFyIF9leGVjdXRlID0gZnVuY3Rpb24gKGNhbGxlciwgZGF0YSkge1xyXG5cdFx0dmFyIGN1cnJlbnRMb2dJbmRleCA9IGxldmVscy5pbmRleE9mKGNhbGxlciksXHJcblx0XHRcdGFsbG93ZWRMb2dJbmRleCA9IGxldmVscy5pbmRleE9mKGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2RlYnVnJykpLFxyXG5cdFx0XHRjb25zb2xlTWV0aG9kID0gbnVsbDtcclxuXHJcblx0XHRpZiAoY3VycmVudExvZ0luZGV4ID49IGFsbG93ZWRMb2dJbmRleCkge1xyXG5cdFx0XHRjb25zb2xlTWV0aG9kID0gY2FsbGVyLnRvTG93ZXJDYXNlKCk7XHJcblxyXG5cdFx0XHRpZiAoY29uc29sZU1ldGhvZCA9PT0gJ2FsZXJ0Jykge1xyXG5cdFx0XHRcdGFsZXJ0KEpTT04uc3RyaW5naWZ5KGRhdGEpKTtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGlmIChjb25zb2xlTWV0aG9kID09PSAnbW9iaWxlJykge1xyXG5cdFx0XHRcdHZhciAkZGJnTGF5ZXIgPSAkKCcubW9iaWxlRGJnTGF5ZXInKTtcclxuXHRcdFx0XHRpZiAoISRkYmdMYXllci5sZW5ndGgpIHtcclxuXHRcdFx0XHRcdCRkYmdMYXllciA9ICQoJzxkaXYgLz4nKTtcclxuXHRcdFx0XHRcdCRkYmdMYXllci5hZGRDbGFzcygnbW9iaWxlRGJnTGF5ZXInKTtcclxuXHRcdFx0XHRcdCRkYmdMYXllci5jc3Moe1xyXG5cdFx0XHRcdFx0XHQncG9zaXRpb24nOiAnZml4ZWQnLFxyXG5cdFx0XHRcdFx0XHQndG9wJzogMCxcclxuXHRcdFx0XHRcdFx0J2xlZnQnOiAwLFxyXG5cdFx0XHRcdFx0XHQnbWF4LWhlaWdodCc6ICc1MCUnLFxyXG5cdFx0XHRcdFx0XHQnbWluLXdpZHRoJzogJzIwMHB4JyxcclxuXHRcdFx0XHRcdFx0J21heC13aWR0aCc6ICczMDBweCcsXHJcblx0XHRcdFx0XHRcdCdiYWNrZ3JvdW5kLWNvbG9yJzogJ2NyaW1zb24nLFxyXG5cdFx0XHRcdFx0XHQnei1pbmRleCc6IDEwMDAwMCxcclxuXHRcdFx0XHRcdFx0J292ZXJmbG93JzogJ3Njcm9sbCdcclxuXHRcdFx0XHRcdH0pO1xyXG5cdFx0XHRcdFx0JCgnYm9keScpLmFwcGVuZCgkZGJnTGF5ZXIpO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0JGRiZ0xheWVyLmFwcGVuZCgnPHA+JyArIEpTT04uc3RyaW5naWZ5KGRhdGEpICsgJzwvcD4nKTtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGlmICh0eXBlb2YgY29uc29sZSA9PT0gJ3VuZGVmaW5lZCcpIHtcclxuXHRcdFx0XHRyZXR1cm47IC8vIFRoZXJlIGlzIG5vIGNvbnNvbGUgc3VwcG9ydCBzbyBkbyBub3QgcHJvY2VlZC5cclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0aWYgKHR5cGVvZiBjb25zb2xlW2NvbnNvbGVNZXRob2RdLmFwcGx5ID09PSAnZnVuY3Rpb24nIHx8IHR5cGVvZiBjb25zb2xlLmxvZy5hcHBseSA9PT0gJ2Z1bmN0aW9uJykge1xyXG5cdFx0XHRcdGlmICh0eXBlb2YgY29uc29sZVtjb25zb2xlTWV0aG9kXSAhPT0gJ3VuZGVmaW5lZCcpIHtcclxuXHRcdFx0XHRcdGNvbnNvbGVbY29uc29sZU1ldGhvZF0uYXBwbHkoY29uc29sZSwgZGF0YSk7XHJcblx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdGNvbnNvbGUubG9nLmFwcGx5KGNvbnNvbGUsIGRhdGEpO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRjb25zb2xlLmxvZyhkYXRhKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHR9XHJcblx0XHRyZXR1cm4gZmFsc2U7XHJcblx0fTtcclxuXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gVkFSSUFCTEUgRVhQT1JUXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cdC8qKlxyXG5cdCAqIFJlcGxhY2VzIGNvbnNvbGUuZGVidWdcclxuXHQgKlxyXG5cdCAqIEBwYXJhbXMge2FsbH0gQW55IGRhdGEgdGhhdCBzaG91bGQgYmUgc2hvd24gaW4gdGhlIGNvbnNvbGUgc3RhdGVtZW50XHJcblx0ICpcclxuXHQgKiBAbmFtZSBDb3JlL0RlYnVnLmRlYnVnXHJcblx0ICogQHB1YmxpY1xyXG5cdCAqIEBtZXRob2RcclxuXHQgKi9cclxuXHRleHBvcnRzLmRlYnVnID0gZnVuY3Rpb24gKCkge1xyXG5cdFx0X2V4ZWN1dGUoJ0RFQlVHJywgYXJndW1lbnRzKTtcclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBSZXBsYWNlcyBjb25zb2xlLmluZm9cclxuXHQgKlxyXG5cdCAqIEBwYXJhbXMge2FsbH0gQW55IGRhdGEgdGhhdCBzaG91bGQgYmUgc2hvd24gaW4gdGhlIGNvbnNvbGUgc3RhdGVtZW50XHJcblx0ICpcclxuXHQgKiBAbmFtZSBDb3JlL0RlYnVnLmluZm9cclxuXHQgKiBAcHVibGljXHJcblx0ICogQG1ldGhvZFxyXG5cdCAqL1xyXG5cdGV4cG9ydHMuaW5mbyA9IGZ1bmN0aW9uICgpIHtcclxuXHRcdF9leGVjdXRlKCdJTkZPJywgYXJndW1lbnRzKTtcclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBSZXBsYWNlcyBjb25zb2xlLmxvZ1xyXG5cdCAqXHJcblx0ICogQHBhcmFtcyB7YWxsfSBBbnkgZGF0YSB0aGF0IHNob3VsZCBiZSBzaG93biBpbiB0aGUgY29uc29sZSBzdGF0ZW1lbnRcclxuXHQgKlxyXG5cdCAqIEBuYW1lIENvcmUvRGVidWcubG9nXHJcblx0ICogQHB1YmxpY1xyXG5cdCAqIEBtZXRob2RcclxuXHQgKi9cclxuXHRleHBvcnRzLmxvZyA9IGZ1bmN0aW9uICgpIHtcclxuXHRcdF9leGVjdXRlKCdMT0cnLCBhcmd1bWVudHMpO1xyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIFJlcGxhY2VzIGNvbnNvbGUud2FyblxyXG5cdCAqXHJcblx0ICogQHBhcmFtcyB7YWxsfSBBbnkgZGF0YSB0aGF0IHNob3VsZCBiZSBzaG93biBpbiB0aGUgY29uc29sZSBzdGF0ZW1lbnRcclxuXHQgKlxyXG5cdCAqIEBuYW1lIENvcmUvRGVidWcud2FyblxyXG5cdCAqIEBwdWJsaWNcclxuXHQgKiBAbWV0aG9kXHJcblx0ICovXHJcblx0ZXhwb3J0cy53YXJuID0gZnVuY3Rpb24gKCkge1xyXG5cdFx0X2V4ZWN1dGUoJ1dBUk4nLCBhcmd1bWVudHMpO1xyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIFJlcGxhY2VzIGNvbnNvbGUuZXJyb3JcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7YWxsfSBBbnkgZGF0YSB0aGF0IHNob3VsZCBiZSBzaG93biBpbiB0aGUgY29uc29sZSBzdGF0ZW1lbnRcclxuXHQgKlxyXG5cdCAqIEBuYW1lIENvcmUvRGVidWcuZXJyb3JcclxuXHQgKiBAcHVibGljXHJcblx0ICogQG1ldGhvZFxyXG5cdCAqL1xyXG5cdGV4cG9ydHMuZXJyb3IgPSBmdW5jdGlvbiAoKSB7XHJcblx0XHRfZXhlY3V0ZSgnRVJST1InLCBhcmd1bWVudHMpO1xyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIFJlcGxhY2VzIGFsZXJ0XHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge2FsbH0gQW55IGRhdGEgdGhhdCBzaG91bGQgYmUgc2hvd24gaW4gdGhlIGNvbnNvbGUgc3RhdGVtZW50XHJcblx0ICpcclxuXHQgKiBAbmFtZSBDb3JlL0RlYnVnLmFsZXJ0XHJcblx0ICogQHB1YmxpY1xyXG5cdCAqIEBtZXRob2RcclxuXHQgKi9cclxuXHRleHBvcnRzLmFsZXJ0ID0gZnVuY3Rpb24gKCkge1xyXG5cdFx0X2V4ZWN1dGUoJ0FMRVJUJywgYXJndW1lbnRzKTtcclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBEZWJ1ZyBpbmZvIGZvciBtb2JpbGUgZGV2aWNlcy5cclxuXHQgKi9cclxuXHRleHBvcnRzLm1vYmlsZSA9IGZ1bmN0aW9uICgpIHtcclxuXHRcdF9leGVjdXRlKCdNT0JJTEUnLCBhcmd1bWVudHMpO1xyXG5cdH07XHJcblxyXG59KGpzZS5jb3JlLmRlYnVnKSk7XHJcbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBlbmdpbmUuanMgMjAxNi0wMi0wNFxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbmpzZS5jb3JlLmVuZ2luZSA9IGpzZS5jb3JlLmVuZ2luZSB8fCB7fTtcclxuXHJcbi8qKlxyXG4gKiBNYWluIEpTIEVuZ2luZSBPYmplY3RcclxuICpcclxuICogVGhpcyBvYmplY3Qgd2lsbCBpbml0aWFsaXplIHRoZSBwYWdlIG5hbWVzcGFjZXMgYW5kIGNvbGxlY3Rpb25zLlxyXG4gKlxyXG4gKiBAbmFtZXNwYWNlIEpTRS9Db3JlL2VuZ2luZVxyXG4gKi9cclxuKGZ1bmN0aW9uKC8qKiBAbGVuZHMgSlNFL0NvcmUvZW5naW5lICovIGV4cG9ydHMpIHtcclxuXHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBQUklWQVRFIEZVTkNUSU9OU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHQvKipcclxuXHQgKiBJbml0aWFsaXplIHRoZSBwYWdlIG5hbWVzcGFjZXMuXHJcblx0ICpcclxuXHQgKiBUaGlzIG1ldGhvZCB3aWxsIHNlYXJjaCB0aGUgcGFnZSBIVE1MIGZvciBhdmFpbGFibGUgbmFtZXNwYWNlcy5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7YXJyYXl9IGNvbGxlY3Rpb25zIENvbnRhaW5zIHRoZSBtb2R1bGUgY29sbGVjdGlvbiBpbnN0YW5jZXMgdG8gYmUgaW5jbHVkZWQgaW4gdGhlIG5hbWVzcGFjZXMuXHJcblx0ICpcclxuXHQgKiBAcmV0dXJuIHthcnJheX0gUmV0dXJucyBhbiBhcnJheSB3aXRoIHRoZSBwYWdlIG5hbWVzcGFjZSBuYW1lcy5cclxuXHQgKlxyXG5cdCAqIEBwcml2YXRlXHJcblx0ICovXHJcblx0dmFyIF9pbml0TmFtZXNwYWNlcyA9IGZ1bmN0aW9uKGNvbGxlY3Rpb25zKSB7XHJcblx0XHR2YXIgcGFnZU5hbWVzcGFjZU5hbWVzID0gW107XHJcblxyXG5cdFx0Ly8gVXNlIHRoZSBjdXN0b20gcHNldWRvIHNlbGVjdG9yIGRlZmluZWQgYXQgZXh0ZW5kLmpzIGluIG9yZGVyIHRvIGZldGNoIHRoZSBhdmFpbGFibGUgbmFtZXNwYWNlcy5cclxuXHRcdCQoJzphdHRyKGRhdGEtXFwoLipcXCktbmFtZXNwYWNlKScpLmVhY2goZnVuY3Rpb24oKSB7XHJcblx0XHRcdHZhciAkZWxlbWVudCA9ICQodGhpcyk7XHJcblxyXG5cdFx0XHQkLmVhY2goJGVsZW1lbnQuZGF0YSgpLCBmdW5jdGlvbihuYW1lLCBzb3VyY2UpIHtcclxuXHRcdFx0XHRpZiAobmFtZS5pbmRleE9mKCdOYW1lc3BhY2UnKSA9PT0gLTEpIHtcclxuXHRcdFx0XHRcdHJldHVybiB0cnVlOyAvLyBOb3QgYSBuYW1lc3BhY2UgcmVsYXRlZCB2YWx1ZS5cclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdG5hbWUgPSBuYW1lLnJlcGxhY2UoJ05hbWVzcGFjZScsICcnKTsgLy8gUmVtb3ZlIFwiTmFtZXNwYWNlXCIgZnJvbSB0aGUgZGF0YSBuYW1lLlxyXG5cclxuXHRcdFx0XHQvLyBDaGVjayBpZiB0aGUgbmFtZXNwYWNlIGlzIGFscmVhZHkgZGVmaW5lZC5cclxuXHRcdFx0XHRpZiAocGFnZU5hbWVzcGFjZU5hbWVzLmluZGV4T2YobmFtZSkgPiAtMSkge1xyXG5cdFx0XHRcdFx0aWYgKHdpbmRvd1tuYW1lXS5zb3VyY2UgIT09IHNvdXJjZSkge1xyXG5cdFx0XHRcdFx0XHRqc2UuY29yZS5kZWJ1Zy5lcnJvcignRWxlbWVudCB3aXRoIHRoZSBkdXBsaWNhdGUgbmFtZXNwYWNlIG5hbWU6ICcsICRlbGVtZW50WzBdKTtcclxuXHRcdFx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKCdUaGUgbmFtZXNwYWNlIFwiJyArIG5hbWUgKyAnXCIgaXMgYWxyZWFkeSBkZWZpbmVkLiBQbGVhc2Ugc2VsZWN0IGFub3RoZXIgJyArXHJcblx0XHRcdFx0XHRcdFx0J25hbWUgZm9yIHlvdXIgbmFtZXNwYWNlLicpO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0cmV0dXJuIHRydWU7IC8vIFRoZSBuYW1lc3BhY2UgaXMgYWxyZWFkeSBkZWZpbmVkLCBjb250aW51ZSBsb29wLlxyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0aWYgKHNvdXJjZSA9PT0gJycpIHtcclxuXHRcdFx0XHRcdHRocm93IG5ldyBTeW50YXhFcnJvcignTmFtZXNwYWNlIHNvdXJjZSBpcyBlbXB0eTogJyArIG5hbWUpO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0Ly8gQ3JlYXRlIGEgbmV3IG5hbWVzcGFjZXMgaW5zdGFuY2UgaW4gdGhlIGdsb2JhbCBzY29wZSAodGhlIGdsb2JhbCBzY29wZVxyXG5cdFx0XHRcdC8vIGlzIHVzZWQgZm9yIGZhbGxiYWNrIHN1cHBvcnQgb2Ygb2xkIG1vZHVsZSBkZWZpbml0aW9ucykuXHJcblx0XHRcdFx0aWYgKG5hbWUgPT09ICdqc2UnKSB7IC8vIE1vZGlmeSB0aGUgZW5naW5lIG9iamVjdCB3aXRoIE5hbWVzcGFjZSBhdHRyaWJ1dGVzLlxyXG5cdFx0XHRcdFx0X2NvbnZlcnRFbmdpbmVUb05hbWVzcGFjZShzb3VyY2UsIGNvbGxlY3Rpb25zKTtcclxuXHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0d2luZG93W25hbWVdID0gbmV3IGpzZS5jb25zdHJ1Y3RvcnMuTmFtZXNwYWNlKG5hbWUsIHNvdXJjZSwgY29sbGVjdGlvbnMpO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0cGFnZU5hbWVzcGFjZU5hbWVzLnB1c2gobmFtZSk7XHJcblx0XHRcdFx0JGVsZW1lbnQucmVtb3ZlQXR0cignZGF0YS0nICsgbmFtZSArICctbmFtZXNwYWNlJyk7XHJcblx0XHRcdH0pO1xyXG5cdFx0fSk7XHJcblxyXG5cdFx0Ly8gVGhyb3cgYW4gZXJyb3IgaWYgbm8gbmFtZXNwYWNlcyB3ZXJlIGZvdW5kLlxyXG5cdFx0aWYgKHBhZ2VOYW1lc3BhY2VOYW1lcy5sZW5ndGggPT09IDApIHtcclxuXHRcdFx0dGhyb3cgbmV3IEVycm9yKCdObyBtb2R1bGUgbmFtZXNwYWNlcyB3ZXJlIGZvdW5kLCB3aXRob3V0IG5hbWVzcGFjZXMgaXQgaXMgbm90IHBvc3NpYmxlIHRvICcgK1xyXG5cdFx0XHRcdCdsb2FkIGFueSBtb2R1bGVzLicpO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIEluaXRpYWxpemUgdGhlIG5hbWVzcGFjZSBpbnN0YW5jZXMuXHJcblx0XHR2YXIgZGVmZXJyZWRDb2xsZWN0aW9uID0gW107XHJcblx0XHRcclxuXHRcdCQuZWFjaChwYWdlTmFtZXNwYWNlTmFtZXMsIGZ1bmN0aW9uKGluZGV4LCBuYW1lKSB7XHJcblx0XHRcdHZhciBkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcclxuXHRcdFx0XHJcblx0XHRcdGRlZmVycmVkQ29sbGVjdGlvbi5wdXNoKGRlZmVycmVkKTtcclxuXHRcdFx0XHJcblx0XHRcdHdpbmRvd1tuYW1lXVxyXG5cdFx0XHRcdC5pbml0KClcclxuXHRcdFx0XHQuZG9uZShmdW5jdGlvbigpIHtcclxuXHRcdFx0XHRcdGRlZmVycmVkLnJlc29sdmUoKTtcclxuXHRcdFx0XHR9KVxyXG5cdFx0XHRcdC5mYWlsKGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdFx0ZGVmZXJyZWQucmVqZWN0KCk7XHJcblx0XHRcdFx0fSlcclxuXHRcdFx0XHQuYWx3YXlzKGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdFx0anNlLmNvcmUuZGVidWcuaW5mbygnTmFtZXNwYWNlIHByb21pc2VzIHdlcmUgcmVzb2x2ZWQ6ICcgLCBuYW1lKTsgXHJcblx0XHRcdFx0fSk7XHJcblx0XHR9KTtcclxuXHJcblx0XHQvLyBUcmlnZ2VyIGFuIGV2ZW50IGFmdGVyIHRoZSBlbmdpbmUgaGFzIGluaXRpYWxpemVkIGFsbCBuZXcgbW9kdWxlcy5cclxuXHRcdCQud2hlbi5hcHBseSh1bmRlZmluZWQsIGRlZmVycmVkQ29sbGVjdGlvbikucHJvbWlzZSgpLmFsd2F5cyhmdW5jdGlvbigpIHtcclxuXHRcdFx0JCgnYm9keScpLnRyaWdnZXIoJ0pTRU5HSU5FX0lOSVRfRklOSVNIRUQnLCBbXSk7XHJcblx0XHR9KTtcclxuXHJcblx0XHRyZXR1cm4gcGFnZU5hbWVzcGFjZU5hbWVzO1xyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIENvbnZlcnQgdGhlIFwianNlXCIgb2JqZWN0IHRvIGEgTmFtZXNwYWNlIGNvbXBhdGlibGUgb2JqZWN0LlxyXG5cdCAqXHJcblx0ICogSW4gb3JkZXIgdG8gc3VwcG9ydCB0aGUgXCJqc2VcIiBuYW1lc3BhY2UgbmFtZSBmb3IgdGhlIGNvcmUgbW9kdWxlcyBwbGFjZWQgaW4gdGhlIFwiSlNFbmdpbmVcIlxyXG5cdCAqIGRpcmVjdG9yeSwgd2Ugd2lsbCBuZWVkIHRvIG1vZGlmeSB0aGUgYWxyZWFkeSBleGlzdGluZyBcImpzZVwiIG9iamVjdCBzbyB0aGF0IGl0IGNhbiBvcGVyYXRlXHJcblx0ICogYXMgYSBuYW1lc3BhY2Ugd2l0aG91dCBsb3NpbmcgaXRzIGluaXRpYWwgYXR0cmlidXRlcy5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBzb3VyY2UgTmFtZXNwYWNlIHNvdXJjZSBwYXRoIGZvciB0aGUgbW9kdWxlIGZpbGVzLlxyXG5cdCAqIEBwYXJhbSB7YXJyYXl9IGNvbGxlY3Rpb25zIENvbnRhaW4gaW5zdGFuY2VzIHRvIHRoZSBwcm90b3lwZSBjb2xsZWN0aW9uIGluc3RhbmNlcy5cclxuXHQgKlxyXG5cdCAqIEBwcml2YXRlXHJcblx0ICovXHJcblx0dmFyIF9jb252ZXJ0RW5naW5lVG9OYW1lc3BhY2UgPSBmdW5jdGlvbihzb3VyY2UsIGNvbGxlY3Rpb25zKSB7XHJcblx0XHR2YXIgdG1wTmFtZXNwYWNlID0gbmV3IGpzZS5jb25zdHJ1Y3RvcnMuTmFtZXNwYWNlKCdqc2UnLCBzb3VyY2UsIGNvbGxlY3Rpb25zKTtcclxuXHRcdGpzZS5uYW1lID0gdG1wTmFtZXNwYWNlLm5hbWU7XHJcblx0XHRqc2Uuc291cmNlID0gdG1wTmFtZXNwYWNlLnNvdXJjZTtcclxuXHRcdGpzZS5jb2xsZWN0aW9ucyA9IHRtcE5hbWVzcGFjZS5jb2xsZWN0aW9ucztcclxuXHRcdGpzZS5pbml0ID0ganNlLmNvbnN0cnVjdG9ycy5OYW1lc3BhY2UucHJvdG90eXBlLmluaXQ7XHJcblx0fTtcclxuXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gUFVCTElDIEZVTkNUSU9OU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHQvKipcclxuXHQgKiBJbml0aWFsaXplIHRoZSBlbmdpbmUuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge2FycmF5fSBjb2xsZWN0aW9ucyBDb250YWlucyB0aGUgc3VwcG9ydGVkIG1vZHVsZSBjb2xsZWN0aW9ucyBwcm90b3R5cGVzLlxyXG5cdCAqL1xyXG5cdGV4cG9ydHMuaW5pdCA9IGZ1bmN0aW9uKGNvbGxlY3Rpb25zKSB7XHJcblx0XHQvLyBJbml0aWFsaXplIHRoZSBwYWdlIG5hbWVzcGFjZXMuXHJcblx0XHR2YXIgcGFnZU5hbWVzcGFjZU5hbWVzID0gX2luaXROYW1lc3BhY2VzKGNvbGxlY3Rpb25zKTtcclxuXHJcblx0XHQvLyBMb2cgdGhlIHBhZ2UgbmFtZXNwYWNlcyAoZm9yIGRlYnVnZ2luZyBvbmx5KS5cclxuXHRcdGpzZS5jb3JlLmRlYnVnLmluZm8oJ1BhZ2UgTmFtZXNwYWNlczogJyArIHBhZ2VOYW1lc3BhY2VOYW1lcy5qb2luKCkpO1xyXG5cclxuXHRcdC8vIFVwZGF0ZSB0aGUgZW5naW5lIHJlZ2lzdHJ5LlxyXG5cdFx0anNlLmNvcmUucmVnaXN0cnkuc2V0KCduYW1lc3BhY2VzJywgcGFnZU5hbWVzcGFjZU5hbWVzKTtcclxuXHR9O1xyXG5cclxufSkoanNlLmNvcmUuZW5naW5lKTtcclxuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIGV4dGVuc2lvbnMuanMgMjAxNS0xMC0xMyBnbVxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiAjIyBFeHRlbmQgSlMgRW5naW5lXHJcbiAqXHJcbiAqIEV4dGVuZCB0aGUgZGVmYXVsdCBiZWhhdmlvdXIgb2YgZW5naW5lIGNvbXBvbmVudHMgb3IgZXh0ZXJuYWwgcGx1Z2lucyBiZWZvcmUgdGhleSBhcmUgbG9hZGVkLlxyXG4gKlxyXG4gKiBAbmFtZXNwYWNlIEpTRS9Db3JlL2V4dGVuZFxyXG4gKi9cclxuKGZ1bmN0aW9uICgpIHtcclxuXHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBOQU1FU1BBQ0UgUFNFVURPIFNFTEVDVE9SIERFRklOSVRJT05cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblx0aWYgKHR5cGVvZiAkLmV4cHIucHNldWRvcy5hdHRyID09PSAndW5kZWZpbmVkJykge1xyXG5cdFx0JC5leHByLnBzZXVkb3MuYXR0ciA9ICQuZXhwci5jcmVhdGVQc2V1ZG8oZnVuY3Rpb24oc2VsZWN0b3IpIHtcclxuXHRcdFx0dmFyIHJlZ2V4cCA9IG5ldyBSZWdFeHAoc2VsZWN0b3IpO1xyXG5cdFx0XHRyZXR1cm4gZnVuY3Rpb24oZWxlbSkge1xyXG5cdFx0XHRcdGZvcih2YXIgaSA9IDA7IGkgPCBlbGVtLmF0dHJpYnV0ZXMubGVuZ3RoOyBpKyspIHtcclxuXHRcdFx0XHRcdHZhciBhdHRyID0gZWxlbS5hdHRyaWJ1dGVzW2ldO1xyXG5cdFx0XHRcdFx0aWYocmVnZXhwLnRlc3QoYXR0ci5uYW1lKSkge1xyXG5cdFx0XHRcdFx0XHRyZXR1cm4gdHJ1ZTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0XHR9O1xyXG5cdFx0fSk7XHJcblx0fVxyXG5cclxuXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gRVhURU5TSU9OIERFRklOSVRJT05cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblx0LyoqXHJcblx0ICogU2V0IGpRdWVyeSBVSSBkYXRlcGlja2VyIHdpZGdldCBkZWZhdWxzLlxyXG5cdCAqXHJcblx0ICogQG5hbWUgY29yZS9leHRlbmQuZGF0ZXBpY2tlclxyXG5cdCAqIEBwdWJsaWNcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtvYmplY3R9XHJcblx0ICovXHJcblx0JC5kYXRlcGlja2VyLnJlZ2lvbmFsLmRlID0ge1xyXG5cdFx0ZGF0ZUZvcm1hdDogJ2RkLm1tLnl5JyxcclxuXHRcdGZpcnN0RGF5OiAxLFxyXG5cdFx0aXNSVEw6IGZhbHNlXHJcblx0fTtcclxuXHQkLmRhdGVwaWNrZXIuc2V0RGVmYXVsdHMoJC5kYXRlcGlja2VyLnJlZ2lvbmFsLmRlKTtcclxufSgpKTtcclxuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIGZhbGxiYWNrLmpzIDIwMTUtMTAtMTYgZ21cclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG5qc2UuY29yZS5mYWxsYmFjayA9IGpzZS5jb3JlLmZhbGxiYWNrIHx8IHt9O1xyXG5cclxuLyoqXHJcbiAqIEZhbGxiYWNrIExpYnJhcnlcclxuICpcclxuICogVGhpcyBsaWJyYXJ5IGNvbnRhaW5zIGEgc2V0IG9mIGRlcHJlY2F0ZWQgZnVuY3Rpb25zIHRoYXQgYXJlIHN0aWxsIHByZXNlbnQgZm9yIGZhbGxiYWNrXHJcbiAqIHN1cHBvcnQuIEVhY2ggZnVuY3Rpb24gd2lsbCBiZSByZW1vdmVkIGZyb20gdGhlIGVuZ2luZSBhZnRlciB0d28gbWlub3IgcmVsZWFzZXMuXHJcbiAqXHJcbiAqIEBuYW1lc3BhY2UgSlNFL0NvcmUvZmFsbGJhY2tcclxuICovXHJcbihmdW5jdGlvbiAoLyoqIEBsZW5kcyBKU0UvQ29yZS9mYWxsYmFjayAqL2V4cG9ydHMpIHtcclxuXHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cclxuXHJcblx0JChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24gKCkge1xyXG5cclxuXHRcdC8vIEV2ZW50IGxpc3RlbmVyIHRoYXQgcGVyZm9ybXMgb24gZXZlcnkgdmFsaWRhdGVcclxuXHRcdC8vIHZhbGlkYXRlIHRyaWdnZXIgdGhhdCBpc24ndCBoYW5kbGVkIGJ5IHRoZSB2YWxpZGF0b3JcclxuXHRcdCQoJ2JvZHknKS5vbigndmFsaWRhdG9yLnZhbGlkYXRlJywgZnVuY3Rpb24gKGUsIGQpIHtcclxuXHRcdFx0aWYgKGQgJiYgZC5kZWZlcnJlZCkge1xyXG5cdFx0XHRcdGQuZGVmZXJyZWQucmVzb2x2ZSgpO1xyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHJcblx0XHQvLyBFdmVudCBsaXN0ZW5lciB0aGF0IHBlcmZvcm1zIG9uIGV2ZXJ5IGZvcm1jaGFuZ2VzLmNoZWNrXHJcblx0XHQvLyB0cmlnZ2VyIHRoYXQgaXNuJ3QgaGFuZGxlZCBieSB0aGUgZm9ybV9jaGFuZ2VzX2NoZWNrZXJcclxuXHRcdCQoJ2JvZHknKS5vbignZm9ybWNoYW5nZXMuY2hlY2snLCBmdW5jdGlvbiAoZSwgZCkge1xyXG5cdFx0XHRpZiAoZCAmJiBkLmRlZmVycmVkKSB7XHJcblx0XHRcdFx0ZC5kZWZlcnJlZC5yZXNvbHZlKCk7XHJcblx0XHRcdH1cclxuXHRcdH0pO1xyXG5cclxuXHRcdC8vIEFwcGx5IHRvdWNoIGNsYXNzIHRvIGJvZHlcclxuXHRcdC8vIGZvciB0b3VjaC1kZXZpY2VzXHJcblx0XHRpZiAoanNlLmNvcmUuY29uZmlnLmdldCgnaGFzVG91Y2gnKSkge1xyXG5cdFx0XHQkKCdib2R5JykuYWRkQ2xhc3MoJ2hhcy10b3VjaCcpO1xyXG5cdFx0fVxyXG5cdH0pO1xyXG5cclxuXHQvKipcclxuXHQgKiBBZGQgYSBkZXByZWNhdGlvbiB3YXJuaW5nIGluIHRoZSBjb25zb2xlLlxyXG5cdCAqXHJcblx0ICogQXMgdGhlIEpTIGVuZ2luZSBldm9sdmVzIG1hbnkgb2xkIGZlYXR1cmVzIHdpbGwgbmVlZCB0byBiZSBjaGFuZ2VkIGluIG9yZGVyIHRvIGxldCBhXHJcblx0ICogZmluZXIgYW5kIGNsZWFyZXIgQVBJIGZvciB0aGUgSlMgRW5naW5lIGNvcmUgbWVjaGFuaXNtcy4gVXNlIHRoaXMgbWV0aG9kIHRvIGNyZWF0ZSBhXHJcblx0ICogZGVwcmVjYXRpb24gd2FybmluZyBmb3IgdGhlIGZ1bmN0aW9ucyBwbGFjZWQgd2l0aGluIHRoaXMgbGlicmFyeS5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBmdW5jdGlvbk5hbWUgVGhlIGRlcHJlY2F0ZWQgZnVuY3Rpb24gbmFtZS5cclxuXHQgKiBAcGFyYW0ge3N0cmluZ30gZGVwcmVjYXRpb25WZXJzaW9uIERlcHJlY2F0aW9uIHZlcnNpb24gd2l0aG91dCB0aGUgXCJ2XCIuXHJcblx0ICogQHBhcmFtIHtzdHJpbmd9IHJlbW92YWxWZXJzaW9uIFJlbW92YWwgdmVyc2lvbiB3aXRob3UgdGhlIFwidlwiXHJcblx0ICpcclxuXHQgKiBAcHJpdmF0ZVxyXG5cdCAqL1xyXG5cdHZhciBfZGVwcmVjYXRpb24gPSBmdW5jdGlvbiAoZnVuY3Rpb25OYW1lLCBkZXByZWNhdGlvblZlcnNpb24sIHJlbW92YWxWZXJzaW9uKSB7XHJcblx0XHRqc2UuY29yZS5kZWJ1Zy53YXJuKCdUaGUgXCInICsgZnVuY3Rpb25OYW1lICsgJ1wiIGZ1bmN0aW9uIGlzIGRlcHJlY2F0ZWQgYXMgb2YgdicgKyBkZXByZWNhdGlvblZlcnNpb24gK1xyXG5cdFx0ICAgICAgICAgICAgICAgICAgICAnIGFuZCB3aWxsIGJlIHJlbW92ZWQgaW4gdicgKyByZW1vdmFsVmVyc2lvbik7XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogU2V0dXAgV2lkZ2V0IEF0dHJpYnV0ZVxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtvYmplY3R9ICRlbGVtZW50IENoYW5nZSB0aGUgd2lkZ2V0IGF0dHJpYnV0ZSBvZiBhbiBlbGVtZW50LlxyXG5cdCAqXHJcblx0ICogQGRlcHJlY2F0ZWQgc2luY2UgdmVyc2lvbiAxLjIuMCAtIHdpbGwgYmUgcmVtb3ZlZCBpbiAxLjQuMFxyXG5cdCAqXHJcblx0ICogQHB1YmxpY1xyXG5cdCAqL1xyXG5cdGV4cG9ydHMuc2V0dXBXaWRnZXRBdHRyID0gZnVuY3Rpb24gKCRlbGVtZW50KSB7XHJcblx0XHRfZGVwcmVjYXRpb24oJ3NldHVwV2lkZ2V0QXR0cicsICcxLjIuMCcsICcxLjQuMCcpO1xyXG5cclxuXHRcdCRlbGVtZW50XHJcblx0XHRcdC5maWx0ZXIoJzphdHRyKF5kYXRhLWd4LV8pLCA6YXR0ciheZGF0YS1nYW1iaW8tXyknKVxyXG5cdFx0XHQuYWRkKCRlbGVtZW50LmZpbmQoJzphdHRyKF5kYXRhLWd4LV8pLCA6YXR0ciheZGF0YS1nYW1iaW8tXyknKSlcclxuXHRcdFx0LmVhY2goZnVuY3Rpb24gKCkge1xyXG5cdFx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXHJcblx0XHRcdFx0XHRhdHRyaWJ1dGVzID0gJHNlbGZbMF0uYXR0cmlidXRlcyxcclxuXHRcdFx0XHRcdG1hdGNoZWRBdHRyaWJ1dGUsXHJcblx0XHRcdFx0XHRuYW1lc3BhY2VOYW1lO1xyXG5cclxuXHRcdFx0XHQkLmVhY2goYXR0cmlidXRlcywgZnVuY3Rpb24gKGluZGV4LCBhdHRyaWJ1dGUpIHtcclxuXHRcdFx0XHRcdGlmIChhdHRyaWJ1dGUgPT09IHVuZGVmaW5lZCkge1xyXG5cdFx0XHRcdFx0XHRyZXR1cm4gdHJ1ZTsgLy8gd3JvbmcgYXR0cmlidXRlLCBjb250aW51ZSBsb29wXHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0bWF0Y2hlZEF0dHJpYnV0ZSA9IGF0dHJpYnV0ZS5uYW1lLm1hdGNoKC9kYXRhLShnYW1iaW98Z3gpLV8uKi9nKTtcclxuXHJcblx0XHRcdFx0XHRpZiAobWF0Y2hlZEF0dHJpYnV0ZSAhPT0gbnVsbCAmJiBtYXRjaGVkQXR0cmlidXRlLmxlbmd0aCA+IDApIHtcclxuXHRcdFx0XHRcdFx0bmFtZXNwYWNlTmFtZSA9IG1hdGNoZWRBdHRyaWJ1dGVbMF0ubWF0Y2goLyhnYW1iaW98Z3gpL2cpWzBdO1xyXG5cclxuXHRcdFx0XHRcdFx0JHNlbGZcclxuXHRcdFx0XHRcdFx0XHQuYXR0cihhdHRyaWJ1dGUubmFtZS5yZXBsYWNlKCdkYXRhLScgKyBuYW1lc3BhY2VOYW1lICsgJy1fJyxcclxuXHRcdFx0XHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdkYXRhLScgKyBuYW1lc3BhY2VOYW1lICsgJy0nKSwgYXR0cmlidXRlLnZhbHVlKTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9KTtcclxuXHRcdFx0fSk7XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogQGRlcHJlY2F0ZWQgc2luY2UgdmVyc2lvbiAxLjIuMCAtIHdpbGwgYmUgcmVtb3ZlZCBpbiAxLjQuMFxyXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBkYXRhXHJcblx0ICogQHBhcmFtIHtvYmplY3R9ICR0YXJnZXRcclxuXHQgKiBAcHVibGljXHJcblx0ICovXHJcblx0ZXhwb3J0cy5maWxsID0gZnVuY3Rpb24gKGRhdGEsICR0YXJnZXQpIHtcclxuXHRcdF9kZXByZWNhdGlvbignZmlsbCcsICcxLjIuMCcsICcxLjQuMCcpO1xyXG5cclxuXHRcdCQuZWFjaChkYXRhLCBmdW5jdGlvbiAoaSwgdikge1xyXG5cdFx0XHR2YXIgJGVsZW1lbnRzID0gJHRhcmdldFxyXG5cdFx0XHRcdC5maW5kKHYuc2VsZWN0b3IpXHJcblx0XHRcdFx0LmFkZCgkdGFyZ2V0LmZpbHRlcih2LnNlbGVjdG9yKSk7XHJcblxyXG5cdFx0XHQkZWxlbWVudHMuZWFjaChmdW5jdGlvbiAoKSB7XHJcblx0XHRcdFx0dmFyICRlbGVtZW50ID0gJCh0aGlzKTtcclxuXHJcblx0XHRcdFx0c3dpdGNoICh2LnR5cGUpIHtcclxuXHRcdFx0XHRcdGNhc2UgJ2h0bWwnOlxyXG5cdFx0XHRcdFx0XHQkZWxlbWVudC5odG1sKHYudmFsdWUpO1xyXG5cdFx0XHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHRcdGNhc2UgJ2F0dHJpYnV0ZSc6XHJcblx0XHRcdFx0XHRcdCRlbGVtZW50LmF0dHIodi5rZXksIHYudmFsdWUpO1xyXG5cdFx0XHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHRcdGNhc2UgJ3JlcGxhY2UnOlxyXG5cdFx0XHRcdFx0XHRpZiAodi52YWx1ZSkge1xyXG5cdFx0XHRcdFx0XHRcdCRlbGVtZW50LnJlcGxhY2VXaXRoKHYudmFsdWUpO1xyXG5cdFx0XHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0XHRcdCRlbGVtZW50XHJcblx0XHRcdFx0XHRcdFx0XHQuYWRkQ2xhc3MoJ2hpZGRlbicpXHJcblx0XHRcdFx0XHRcdFx0XHQuZW1wdHkoKTtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHRcdGRlZmF1bHQ6XHJcblx0XHRcdFx0XHRcdCRlbGVtZW50LnRleHQodi52YWx1ZSk7XHJcblx0XHRcdFx0XHRcdGJyZWFrO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSk7XHJcblxyXG5cdFx0fSk7XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogQGRlcHJlY2F0ZWQgc2luY2UgdmVyc2lvbiAxLjIuMCAtIHdpbGwgYmUgcmVtb3ZlZCBpbiAxLjQuMFxyXG5cdCAqIEBwYXJhbSB1cmxcclxuXHQgKiBAcGFyYW0gZGVlcFxyXG5cdCAqIEByZXR1cm5zIHt7fX1cclxuXHQgKi9cclxuXHRleHBvcnRzLmdldFVybFBhcmFtcyA9IGZ1bmN0aW9uICh1cmwsIGRlZXApIHtcclxuXHRcdF9kZXByZWNhdGlvbignZ2V0VXJsUGFyYW1zJywgJzEuMi4wJywgJzEuNC4wJyk7XHJcblxyXG5cdFx0dXJsID0gZGVjb2RlVVJJQ29tcG9uZW50KHVybCB8fCBsb2NhdGlvbi5ocmVmKTtcclxuXHJcblx0XHR2YXIgc3BsaXRVcmwgPSB1cmwuc3BsaXQoJz8nKSxcclxuXHRcdFx0c3BsaXRQYXJhbSA9IChzcGxpdFVybC5sZW5ndGggPiAxKSA/IHNwbGl0VXJsWzFdLnNwbGl0KCcmJykgOiBbXSxcclxuXHRcdFx0cmVnZXggPSBuZXcgUmVnRXhwKC9cXFsoLio/KVxcXS9nKSxcclxuXHRcdFx0cmVzdWx0ID0ge307XHJcblxyXG5cdFx0JC5lYWNoKHNwbGl0UGFyYW0sIGZ1bmN0aW9uIChpLCB2KSB7XHJcblx0XHRcdHZhciBrZXlWYWx1ZSA9IHYuc3BsaXQoJz0nKSxcclxuXHRcdFx0XHRyZWdleFJlc3VsdCA9IHJlZ2V4LmV4ZWMoa2V5VmFsdWVbMF0pLFxyXG5cdFx0XHRcdGJhc2UgPSBudWxsLFxyXG5cdFx0XHRcdGJhc2VuYW1lID0ga2V5VmFsdWVbMF0uc3Vic3RyaW5nKDAsIGtleVZhbHVlWzBdLnNlYXJjaCgnXFxcXFsnKSksXHJcblx0XHRcdFx0a2V5cyA9IFtdLFxyXG5cdFx0XHRcdGxhc3RLZXkgPSBudWxsO1xyXG5cclxuXHRcdFx0aWYgKCFkZWVwIHx8IHJlZ2V4UmVzdWx0ID09PSBudWxsKSB7XHJcblx0XHRcdFx0cmVzdWx0W2tleVZhbHVlWzBdXSA9IGtleVZhbHVlWzFdLnNwbGl0KCcjJylbMF07XHJcblx0XHRcdH0gZWxzZSB7XHJcblxyXG5cdFx0XHRcdHJlc3VsdFtiYXNlbmFtZV0gPSByZXN1bHRbYmFzZW5hbWVdIHx8IFtdO1xyXG5cdFx0XHRcdGJhc2UgPSByZXN1bHRbYmFzZW5hbWVdO1xyXG5cclxuXHRcdFx0XHRkbyB7XHJcblx0XHRcdFx0XHRrZXlzLnB1c2gocmVnZXhSZXN1bHRbMV0pO1xyXG5cdFx0XHRcdFx0cmVnZXhSZXN1bHQgPSByZWdleC5leGVjKGtleVZhbHVlWzBdKTtcclxuXHRcdFx0XHR9IHdoaWxlIChyZWdleFJlc3VsdCAhPT0gbnVsbCk7XHJcblxyXG5cdFx0XHRcdCQuZWFjaChrZXlzLCBmdW5jdGlvbiAoaSwgdikge1xyXG5cdFx0XHRcdFx0dmFyIG5leHQgPSBrZXlzW2kgKyAxXTtcclxuXHRcdFx0XHRcdHYgPSB2IHx8ICcwJztcclxuXHJcblx0XHRcdFx0XHRpZiAodHlwZW9mIChuZXh0KSA9PT0gJ3N0cmluZycpIHtcclxuXHRcdFx0XHRcdFx0YmFzZVt2XSA9IGJhc2Vbdl0gfHwgW107XHJcblx0XHRcdFx0XHRcdGJhc2UgPSBiYXNlW3ZdO1xyXG5cdFx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdFx0YmFzZVt2XSA9IGJhc2Vbdl0gfHwgdW5kZWZpbmVkO1xyXG5cdFx0XHRcdFx0XHRsYXN0S2V5ID0gdjtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9KTtcclxuXHJcblx0XHRcdFx0aWYgKGxhc3RLZXkgIT09IG51bGwpIHtcclxuXHRcdFx0XHRcdGJhc2VbbGFzdEtleV0gPSBrZXlWYWx1ZVsxXTtcclxuXHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0YmFzZSA9IGtleVZhbHVlWzFdO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cclxuXHRcdH0pO1xyXG5cclxuXHRcdHJldHVybiByZXN1bHQ7XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogRmFsbGJhY2sgZ2V0RGF0YSBtZXRob2QuXHJcblx0ICpcclxuXHQgKiBUaGlzIG1ldGhvZCB3YXMgaW5jbHVkZWQgaW4gdjEuMCBvZiBKUyBFbmdpbmUgYW5kIGlzIHJlcGxhY2VkIGJ5IHRoZVxyXG5cdCAqIFwianNlLmxpYnMuZm9ybS5nZXREYXRhXCIgbWV0aG9kLlxyXG5cdCAqXHJcblx0ICogQGRlcHJlY2F0ZWQgc2luY2UgdmVyc2lvbiAxLjIuMCAtIHdpbGwgYmUgcmVtb3ZlZCBpbiAxLjQuMFxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtvYmplY3R9ICRmb3JtIFNlbGVjdG9yIG9mIHRoZSBmb3JtIHRvIGJlIHBhcnNlZC5cclxuXHQgKiBAcGFyYW0ge3N0cmluZ30gaWdub3JlIChvcHRpb25hbCkgalF1ZXJ5IHNlbGVjdG9yIHN0cmluZyBvZiBmb3JtIGVsZW1lbnRzIHRvIGJlIGlnbm9yZWQuXHJcblx0ICpcclxuXHQgKiBAcmV0dXJucyB7b2JqZWN0fSBSZXR1cm5zIHRoZSBkYXRhIG9mIHRoZSBmb3JtIGFzIGFuIG9iamVjdC5cclxuXHQgKi9cclxuXHRleHBvcnRzLmdldERhdGEgPSBmdW5jdGlvbiAoJGZvcm0sIGlnbm9yZSkge1xyXG5cdFx0dmFyICRlbGVtZW50cyA9ICRmb3JtLmZpbmQoJ2lucHV0LCB0ZXh0YXJlYSwgc2VsZWN0JyksXHJcblx0XHRcdHJlc3VsdCA9IHt9O1xyXG5cclxuXHRcdGlmIChpZ25vcmUpIHtcclxuXHRcdFx0JGVsZW1lbnRzID0gJGVsZW1lbnRzLmZpbHRlcignOm5vdCgnICsgaWdub3JlICsgJyknKTtcclxuXHRcdH1cclxuXHJcblx0XHQkZWxlbWVudHMuZWFjaChmdW5jdGlvbiAoKSB7XHJcblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXHJcblx0XHRcdFx0dHlwZSA9ICRzZWxmLnByb3AoJ3RhZ05hbWUnKS50b0xvd2VyQ2FzZSgpLFxyXG5cdFx0XHRcdG5hbWUgPSAkc2VsZi5hdHRyKCduYW1lJyksXHJcblx0XHRcdFx0JHNlbGVjdGVkID0gbnVsbDtcclxuXHJcblx0XHRcdHR5cGUgPSAodHlwZSAhPT0gJ2lucHV0JykgPyB0eXBlIDogJHNlbGYuYXR0cigndHlwZScpLnRvTG93ZXJDYXNlKCk7XHJcblxyXG5cdFx0XHRzd2l0Y2ggKHR5cGUpIHtcclxuXHRcdFx0XHRjYXNlICdyYWRpbyc6XHJcblx0XHRcdFx0XHQkZm9ybVxyXG5cdFx0XHRcdFx0XHQuZmluZCgnaW5wdXRbbmFtZT1cIicgKyBuYW1lICsgJ1wiXTpjaGVja2VkJylcclxuXHRcdFx0XHRcdFx0LnZhbCgpO1xyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblx0XHRcdFx0Y2FzZSAnY2hlY2tib3gnOlxyXG5cdFx0XHRcdFx0aWYgKG5hbWUuc2VhcmNoKCdcXFxcWycpICE9PSAtMSkge1xyXG5cdFx0XHRcdFx0XHRpZiAoJHNlbGYucHJvcCgnY2hlY2tlZCcpKSB7XHJcblx0XHRcdFx0XHRcdFx0bmFtZSA9IG5hbWUuc3Vic3RyaW5nKDAsIG5hbWUuc2VhcmNoKCdcXFxcWycpKTtcclxuXHRcdFx0XHRcdFx0XHRpZiAodHlwZW9mIHJlc3VsdFtuYW1lXSA9PT0gJ3VuZGVmaW5lZCcpIHtcclxuXHRcdFx0XHRcdFx0XHRcdHJlc3VsdFtuYW1lXSA9IFtdO1xyXG5cdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0XHRyZXN1bHRbbmFtZV0ucHVzaCgkKHRoaXMpLnZhbCgpKTtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdFx0cmVzdWx0W25hbWVdID0gJHNlbGYucHJvcCgnY2hlY2tlZCcpO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblx0XHRcdFx0Y2FzZSAnc2VsZWN0JzpcclxuXHRcdFx0XHRcdCRzZWxlY3RlZCA9ICRzZWxmLmZpbmQoJzpzZWxlY3RlZCcpO1xyXG5cdFx0XHRcdFx0aWYgKCRzZWxlY3RlZC5sZW5ndGggPiAxKSB7XHJcblx0XHRcdFx0XHRcdHJlc3VsdFtuYW1lXSA9IFtdO1xyXG5cdFx0XHRcdFx0XHQkc2VsZWN0ZWQuZWFjaChmdW5jdGlvbiAoKSB7XHJcblx0XHRcdFx0XHRcdFx0cmVzdWx0W25hbWVdLnB1c2goJCh0aGlzKS52YWwoKSk7XHJcblx0XHRcdFx0XHRcdH0pO1xyXG5cdFx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdFx0cmVzdWx0W25hbWVdID0gJHNlbGVjdGVkLnZhbCgpO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblx0XHRcdFx0Y2FzZSAnYnV0dG9uJzpcclxuXHRcdFx0XHRcdGJyZWFrO1xyXG5cdFx0XHRcdGRlZmF1bHQ6XHJcblx0XHRcdFx0XHRpZiAobmFtZSkge1xyXG5cdFx0XHRcdFx0XHRyZXN1bHRbbmFtZV0gPSAkc2VsZi52YWwoKTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdGJyZWFrO1xyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHRcdHJldHVybiByZXN1bHQ7XHJcblx0fTtcclxuXHJcbn0pKGpzZS5jb3JlLmZhbGxiYWNrKTtcclxuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIGxhbmcuanMgMjAxNS0xMC0xMyBnbVxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbmpzZS5jb3JlLmxhbmcgPSBqc2UuY29yZS5sYW5nIHx8IHt9O1xyXG5cclxuLyoqXHJcbiAqICMjIEpTIEVuZ2luZSBMb2NhbGl6YXRpb24gTGlicmFyeVxyXG4gKlxyXG4gKiBUaGUgZ2xvYmFsIExhbmcgb2JqZWN0IGNvbnRhaW5zIGxhbmd1YWdlIGluZm9ybWF0aW9uIHRoYXQgY2FuIGJlIGVhc2lseSB1c2VkIGluIHlvdXJcclxuICogSmF2YVNjcmlwdCBjb2RlLiBUaGUgb2JqZWN0IGNvbnRhaW5zIGNvbnN0YW5jZSB0cmFuc2xhdGlvbnMgYW5kIGR5bmFtaWMgc2VjdGlvbnMgdGhhdFxyXG4gKiBjYW4gYmUgbG9hZGVkIGFuZCB1c2VkIGluIGRpZmZlcmVudCBwYWdlLlxyXG4gKlxyXG4gKiAjIyMjIEltcG9ydGFudFxyXG4gKiBUaGUgZW5naW5lIHdpbGwgYXV0b21hdGljYWxseSBsb2FkIHRyYW5zbGF0aW9uIHNlY3Rpb25zIHRoYXQgYXJlIHByZXNlbnQgaW4gdGhlXHJcbiAqIGB3aW5kb3cuSlNFbmdpbmVDb25maWd1cmF0aW9uLnRyYW5zbGF0aW9uc2AgcHJvcGVydHkgdXBvbiBpbml0aWFsaXphdGlvbi4gRm9yIG1vcmVcclxuICogaW5mb3JtYXRpb24gbG9vayBhdCB0aGUgXCJjb3JlL2luaXRpYWxpemVcIiBwYWdlIG9mIGRvY3VtZW50YXRpb24gcmVmZXJlbmNlLlxyXG4gKlxyXG4gKiBgYGBqYXZhc2NyaXB0XHJcbiAqIGpzZS5jb3JlLmxhbmcuYWRkU2VjdGlvbignc2VjdGlvbk5hbWUnLCB7IHRyYW5zbGF0aW9uS2V5OiAndHJhbnNsYXRpb25WYWx1ZScgfSk7IC8vIEFkZCB0cmFuc2xhdGlvbiBzZWN0aW9uLlxyXG4gKiBqc2UuY29yZS50cmFuc2xhdGUoJ3RyYW5zbGF0aW9uS2V5JywgJ3NlY3Rpb25OYW1lJyk7IC8vIEdldCB0aGUgdHJhbnNsYXRlZCBzdHJpbmcuXHJcbiAqIGpzZS5jb3JlLmdldFNlY3Rpb25zKCk7IC8vIHJldHVybnMgYXJyYXkgd2l0aCBzZWN0aW9ucyBlLmcuIFsnYWRtaW5fYnV0dG9ucycsICdnZW5lcmFsJ11cclxuICogYGBgXHJcbiAqXHJcbiAqIEBuYW1lc3BhY2UgSlNFL0NvcmUvbGFuZ1xyXG4gKi9cclxuKGZ1bmN0aW9uIChleHBvcnRzKSB7XHJcblxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gVkFSSUFCTEVTXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cdC8qKlxyXG5cdCAqIENvbnRhaW5zIHZhcmlvdXMgdHJhbnNsYXRpb24gc2VjdGlvbnMuXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7b2JqZWN0fVxyXG5cdCAqL1xyXG5cdHZhciBzZWN0aW9ucyA9IHt9O1xyXG5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBQVUJMSUMgTUVUSE9EU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHQvKipcclxuXHQgKiBBZGQgYSB0cmFuc2xhdGlvbiBzZWN0aW9uLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtzdHJpbmd9IG5hbWUgTmFtZSBvZiB0aGUgc2VjdGlvbiwgdXNlZCBsYXRlciBmb3IgYWNjZXNzaW5nIHRyYW5zbGF0aW9uIHN0cmluZ3MuXHJcblx0ICogQHBhcmFtIHtvYmplY3R9IHRyYW5zbGF0aW9ucyBLZXkgLSB2YWx1ZSBvYmplY3QgY29udGFpbmluZyB0aGUgdHJhbnNsYXRpb25zLlxyXG5cdCAqXHJcblx0ICogQHRocm93cyBFeGNlcHRpb24gaWYgXCJuYW1lXCIgb3IgXCJ0cmFuc2xhdGlvbnNcIiBhcmd1bWVudHMgYXJlIGludmFsaWQuXHJcblx0ICpcclxuXHQgKiBAbmFtZSBjb3JlL2xhbmcuYWRkU2VjdGlvblxyXG5cdCAqIEBwdWJsaWNcclxuXHQgKiBAbWV0aG9kXHJcblx0ICovXHJcblx0ZXhwb3J0cy5hZGRTZWN0aW9uID0gZnVuY3Rpb24gKG5hbWUsIHRyYW5zbGF0aW9ucykge1xyXG5cdFx0aWYgKHR5cGVvZiBuYW1lICE9PSAnc3RyaW5nJyB8fCB0eXBlb2YgdHJhbnNsYXRpb25zICE9PSAnb2JqZWN0JyB8fCB0cmFuc2xhdGlvbnMgPT09IG51bGwgfHxcclxuXHRcdCAgICB0cmFuc2xhdGlvbnMubGVuZ3RoID09PSAwKSB7XHJcblx0XHRcdHRocm93IG5ldyBFcnJvcignd2luZG93Lmd4LmNvcmUubGFuZy5hZGRTZWN0aW9uOiBJbnZhbGlkIGFyZ3VtZW50cyBwcm92aWRlZCAobmFtZTogJyArICh0eXBlb2YgbmFtZSkgK1xyXG5cdFx0XHQgICAgICAgICAgICAgICAgJywgdHJhbnNsYXRpb25zOiAnICsgKHR5cGVvZiB0cmFuc2xhdGlvbnMpICsgJyknKTtcclxuXHRcdH1cclxuXHJcblx0XHRzZWN0aW9uc1tuYW1lXSA9IHRyYW5zbGF0aW9ucztcclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBHZXQgbG9hZGVkIHRyYW5zbGF0aW9uIHNlY3Rpb25zLlxyXG5cdCAqXHJcblx0ICogVXNlZnVsIGZvciBhc3NlcnRpbmcgcHJlc2VudCB0cmFuc2xhdGlvbiBzZWN0aW9ucy5cclxuXHQgKlxyXG5cdCAqIEByZXR1cm4ge2FycmF5fSBSZXR1cm5zIGFycmF5IHdpdGggdGhlIGV4aXN0aW5nIHNlY3Rpb25zLlxyXG5cdCAqXHJcblx0ICogQG5hbWUgY29yZS9sYW5nLmdldFNlY3Rpb25zXHJcblx0ICogQHB1YmxpY1xyXG5cdCAqIEBtZXRob2RcclxuXHQgKi9cclxuXHRleHBvcnRzLmdldFNlY3Rpb25zID0gZnVuY3Rpb24gKCkge1xyXG5cdFx0dmFyIHJlc3VsdCA9IFtdO1xyXG5cdFx0JC5lYWNoKHNlY3Rpb25zLCBmdW5jdGlvbiAobmFtZSwgY29udGVudCkge1xyXG5cdFx0XHRyZXN1bHQucHVzaChuYW1lKTtcclxuXHRcdH0pO1xyXG5cdFx0cmV0dXJuIHJlc3VsdDtcclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBUcmFuc2xhdGUgc3RyaW5nIGluIEphdmFzY3JpcHQgY29kZS5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBwaHJhc2UgTmFtZSBvZiB0aGUgcGhyYXNlIGNvbnRhaW5pbmcgdGhlIHRyYW5zbGF0aW9uLlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBzZWN0aW9uIFNlY3Rpb24gbmFtZSBjb250YWluaW5nIHRoZSB0cmFuc2xhdGlvbiBzdHJpbmcuXHJcblx0ICpcclxuXHQgKiBAcmV0dXJucyB7c3RyaW5nfSBSZXR1cm5zIHRoZSB0cmFuc2xhdGVkIHN0cmluZy5cclxuXHQgKlxyXG5cdCAqIEB0aHJvd3MgRXhjZXB0aW9uIGlmIHByb3ZpZGVkIGFyZ3VtZW50cyBhcmUgaW52YWxpZC5cclxuXHQgKiBAdGhyb3dzIEV4Y2VwdGlvbiBpZiByZXF1aXJlZCBzZWN0aW9uIGRvZXMgbm90IGV4aXN0IG9yIHRyYW5zbGF0aW9uIGNvdWxkIG5vdCBiZSBmb3VuZC5cclxuXHQgKlxyXG5cdCAqIEBuYW1lIGNvcmUvbGFuZy50cmFuc2xhdGVcclxuXHQgKiBAcHVibGljXHJcblx0ICogQG1ldGhvZFxyXG5cdCAqL1xyXG5cdGV4cG9ydHMudHJhbnNsYXRlID0gZnVuY3Rpb24gKHBocmFzZSwgc2VjdGlvbikge1xyXG5cdFx0Ly8gVmFsaWRhdGUgcHJvdmlkZWQgYXJndW1lbnRzLlxyXG5cdFx0aWYgKHR5cGVvZiBwaHJhc2UgIT09ICdzdHJpbmcnIHx8IHR5cGVvZiBzZWN0aW9uICE9PSAnc3RyaW5nJykge1xyXG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ0ludmFsaWQgYXJndW1lbnRzIHByb3ZpZGVkIGluIHRyYW5zbGF0ZSBtZXRob2QgKHBocmFzZTogJyArICh0eXBlb2YgcGhyYXNlKSArXHJcblx0XHRcdCAgICAgICAgICAgICAgICAnLCBzZWN0aW9uOiAnICsgKHR5cGVvZiBzZWN0aW9uKSArICcpLicpO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIENoZWNrIGlmIHRyYW5zbGF0aW9uIGV4aXN0cy5cclxuXHRcdGlmICh0eXBlb2Ygc2VjdGlvbnNbc2VjdGlvbl0gPT09ICd1bmRlZmluZWQnIHx8IHR5cGVvZiBzZWN0aW9uc1tzZWN0aW9uXVtwaHJhc2VdID09PSAndW5kZWZpbmVkJykge1xyXG5cdFx0XHRqc2UuY29yZS5kZWJ1Zy53YXJuKCdDb3VsZCBub3QgZm91bmQgcmVxdWVzdGVkIHRyYW5zbGF0aW9uIChwaHJhc2U6ICcgKyBwaHJhc2UgKyAnLCBzZWN0aW9uOiAnXHJcblx0XHRcdCAgICAgICAgICAgICAgICAgICAgKyBzZWN0aW9uICsgJykuJyk7XHJcblx0XHRcdHJldHVybiAneycgKyBzZWN0aW9uICsgJy4nICsgcGhyYXNlICsgJ30nO1xyXG5cdFx0fVxyXG5cclxuXHRcdHJldHVybiBzZWN0aW9uc1tzZWN0aW9uXVtwaHJhc2VdO1xyXG5cdH07XHJcblxyXG59KGpzZS5jb3JlLmxhbmcpKTtcclxuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIG1vZHVsZV9sb2FkZXIuanMgMjAxNS0xMC0xNiBnbVxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbmpzZS5jb3JlLm1vZHVsZV9sb2FkZXIgPSBqc2UuY29yZS5tb2R1bGVfbG9hZGVyIHx8IHt9O1xyXG5cclxuLyoqXHJcbiAqICMjIEpTIEVuZ2luZSBNb2R1bGUgTG9hZGVyXHJcbiAqXHJcbiAqIFRoaXMgb2JqZWN0IGlzIGFuIGFkYXB0ZXIgYmV0d2VlbiB0aGUgZW5naW5lIGFuZCBSZXF1aXJlSlMgd2hpY2ggaXMgdXNlZCB0b1xyXG4gKiBsb2FkIHRoZSByZXF1aXJlZCBmaWxlcyBpbnRvIHRoZSBjbGllbnQuXHJcbiAqXHJcbiAqIEBuYW1lc3BhY2UgSlNFL0NvcmUvbW9kdWxlX2xvYWRlclxyXG4gKi9cclxuKGZ1bmN0aW9uICgvKiogQGxlbmRzIEpTRS9Db3JlL21vZHVsZV9sb2FkZXIgKi8gZXhwb3J0cykge1xyXG5cclxuXHQndXNlIHN0cmljdCc7XHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFBVQkxJQyBNRVRIT0RTXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cdC8qKlxyXG5cdCAqIEluaXRpYWxpemUgdGhlIG1vZHVsZSBsb2FkZXIuXHJcblx0ICpcclxuXHQgKiBFeGVjdXRlIHRoaXMgbWV0aG9kIGFmdGVyIHRoZSBlbmdpZW4gY29uZmlnIGlzIGluaWFsaXplZC4gSXQgd2lsbCBjb25maWd1cmUgcmVxdWlyZWpzXHJcblx0ICogc28gdGhhdCBpdCB3aWxsIGJlIGFibGUgdG8gZmluZCB0aGUgcHJvamVjdCBmaWxlcy5cclxuXHQgKi9cclxuXHRleHBvcnRzLmluaXQgPSBmdW5jdGlvbiAoKSB7XHJcblx0XHR2YXIgY29uZmlnID0ge1xyXG5cdFx0XHRiYXNlVXJsOiBqc2UuY29yZS5jb25maWcuZ2V0KCdzaG9wVXJsJyksXHJcblx0XHRcdHVybEFyZ3M6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2NhY2hlQnVzdCcpID8gJ2J1c3Q9JyArIChuZXcgRGF0ZSgpKS5nZXRUaW1lKCkgOiAnJyxcclxuXHRcdFx0b25FcnJvcjogZnVuY3Rpb24gKGVycm9yKSB7XHJcblx0XHRcdFx0anNlLmNvcmUuZGVidWcuZXJyb3IoJ1JlcXVpcmVKUyBFcnJvcjonLCBlcnJvcik7XHJcblx0XHRcdH1cclxuXHRcdH07XHJcblxyXG5cdFx0cmVxdWlyZS5jb25maWcoY29uZmlnKTtcclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBMb2FkIGEgbW9kdWxlIGZpbGUgd2l0aCB0aGUgdXNlIG9mIHJlcXVpcmVqcy5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSAkZWxlbWVudCBTZWxlY3RvciBvZiB0aGUgZWxlbWVudCB3aGljaCBoYXMgdGhlIG1vZHVsZSBkZWZpbml0aW9uLlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBuYW1lIE1vZHVsZSBuYW1lIHRvIGJlIGxvYWRlZC4gTW9kdWxlcyBoYXZlIHRoZSBzYW1lIG5hbWVzIGFzIHRoZWlyIGZpbGVzLlxyXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBjb2xsZWN0aW9uIEN1cnJlbnQgY29sbGVjdGlvbiBpbnN0YW5jZS5cclxuXHQgKlxyXG5cdCAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyBhIHByb21pc2Ugb2JqZWN0IHRvIGJlIHJlc29sdmVkIHdpdGggdGhlIG1vZHVsZVxyXG5cdCAqIGluc3RhbmNlIGFzIGEgcGFyYW1ldGVyLlxyXG5cdCAqL1xyXG5cdGV4cG9ydHMubG9hZCA9IGZ1bmN0aW9uICgkZWxlbWVudCwgbmFtZSwgY29sbGVjdGlvbikge1xyXG5cdFx0dmFyIGRlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xyXG5cclxuXHRcdHRyeSB7XHJcblx0XHRcdGlmIChuYW1lID09PSAnJykge1xyXG5cdFx0XHRcdGRlZmVycmVkLnJlamVjdChuZXcgRXJyb3IoJ01vZHVsZSBuYW1lIGNhbm5vdCBiZSBlbXB0eS4nKSk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHZhciBiYXNlTW9kdWxlTmFtZSA9IG5hbWUucmVwbGFjZSgvLipcXC8oLiopLywgJyQxJyk7IC8vIE5hbWUgd2l0aG91dCB0aGUgcGFyZW50IGRpcmVjdG9yeS5cclxuXHJcblx0XHRcdC8vIFRyeSB0byBsb2FkIHRoZSBjYWNoZWQgaW5zdGFuY2Ugb2YgdGhlIG1vZHVsZS5cclxuXHRcdFx0dmFyIGNhY2hlZCA9IGNvbGxlY3Rpb24uY2FjaGUubW9kdWxlc1tiYXNlTW9kdWxlTmFtZV07XHJcblx0XHRcdGlmIChjYWNoZWQgJiYgY2FjaGVkLmNvZGUgPT09ICdmdW5jdGlvbicpIHtcclxuXHRcdFx0XHRjb25zb2xlLmxvZyhjb2xsZWN0aW9uLCBjb2xsZWN0aW9uLm5hbWVzcGFjZSk7XHJcblx0XHRcdFx0ZGVmZXJyZWQucmVzb2x2ZShuZXcganNlLmNvbnN0cnVjdG9ycy5Nb2R1bGUoJGVsZW1lbnQsIGJhc2VNb2R1bGVOYW1lLCBjb2xsZWN0aW9uKSk7XHJcblx0XHRcdFx0cmV0dXJuIHRydWU7IC8vIGNvbnRpbnVlIGxvb3BcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gVHJ5IHRvIGxvYWQgdGhlIG1vZHVsZSBmaWxlIGZyb20gdGhlIHNlcnZlci5cclxuXHRcdFx0dmFyIGZpbGVFeHRlbnNpb24gPSBqc2UuY29yZS5jb25maWcuZ2V0KCdkZWJ1ZycpICE9PSAnREVCVUcnID8gJy5taW4uanMnIDogJy5qcycsXHJcblx0XHRcdFx0dXJsID0gY29sbGVjdGlvbi5uYW1lc3BhY2Uuc291cmNlICsgJy8nICsgY29sbGVjdGlvbi5uYW1lICsgJy8nICsgbmFtZSArIGZpbGVFeHRlbnNpb247XHJcblxyXG5cdFx0XHRyZXF1aXJlKFt1cmxdLCBmdW5jdGlvbiAoKSB7XHJcblx0XHRcdFx0aWYgKHR5cGVvZiBjb2xsZWN0aW9uLmNhY2hlLm1vZHVsZXNbYmFzZU1vZHVsZU5hbWVdID09PSAndW5kZWZpbmVkJykge1xyXG5cdFx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKCdNb2R1bGUgXCInICsgbmFtZSArICdcIiB3YXNuXFwndCBkZWZpbmVkIGNvcnJlY3RseS4gQ2hlY2sgdGhlIG1vZHVsZSBjb2RlIGZvciAnXHJcblx0XHRcdFx0XHQgICAgICAgICAgICAgICAgKyAnZnVydGhlciB0cm91Ymxlc2hvb3RpbmcuJyk7XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHR2YXIgZGVwZW5kZW5jaWVzID0gY29sbGVjdGlvbi5jYWNoZS5tb2R1bGVzW2Jhc2VNb2R1bGVOYW1lXS5kZXBlbmRlbmNpZXMuc2xpY2UoKTsgLy8gdXNlIHNsaWNlIGZvciBjb3B5aW5nIHRoZSBhcnJheVxyXG5cclxuXHRcdFx0XHRpZiAoZGVwZW5kZW5jaWVzLmxlbmd0aCA9PT0gMCkgeyAvLyBubyBkZXBlbmRlbmNpZXNcclxuXHRcdFx0XHRcdGRlZmVycmVkLnJlc29sdmUobmV3IGpzZS5jb25zdHJ1Y3RvcnMuTW9kdWxlKCRlbGVtZW50LCBiYXNlTW9kdWxlTmFtZSwgY29sbGVjdGlvbikpO1xyXG5cdFx0XHRcdFx0cmV0dXJuIHRydWU7IC8vIGNvbnRpbnVlIGxvb3BcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdC8vIExvYWQgdGhlIGRlcGVuZGVuY2llcyBmaXJzdC5cclxuXHRcdFx0XHQkLmVhY2goZGVwZW5kZW5jaWVzLCBmdW5jdGlvbiAoaW5kZXgsIGRlcGVuZGVuY3kpIHtcclxuXHRcdFx0XHRcdGlmIChkZXBlbmRlbmN5LmluZGV4T2YoJ2h0dHAnKSA9PT0gLTEpIHsgLy8gVGhlbiBjb252ZXJ0IHRoZSByZWxhdGl2ZSBwYXRoIHRvIEpTRW5naW5lL2xpYnMgZGlyZWN0b3J5LlxyXG5cdFx0XHRcdFx0XHRkZXBlbmRlbmNpZXNbaW5kZXhdID0ganNlLmNvcmUuY29uZmlnLmdldCgnZW5naW5lVXJsJykgKyAnL2xpYnMvJyArIGRlcGVuZGVuY3kgKyBmaWxlRXh0ZW5zaW9uO1xyXG5cdFx0XHRcdFx0fSBlbHNlIGlmIChkZXBlbmRlbmN5LmluZGV4T2YoJy5qcycpID09PSAtMSkgeyAvLyBUaGVuIGFkZCB0aGUgZHluYW1pYyBmaWxlIGV4dGVuc2lvbiB0byB0aGUgVVJMLlxyXG5cdFx0XHRcdFx0XHRkZXBlbmRlbmNpZXNbaW5kZXhdICs9IGZpbGVFeHRlbnNpb247XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fSk7XHJcblxyXG5cdFx0XHRcdHJlcXVpcmUoZGVwZW5kZW5jaWVzLCBmdW5jdGlvbiAoKSB7XHJcblx0XHRcdFx0XHRkZWZlcnJlZC5yZXNvbHZlKG5ldyBqc2UuY29uc3RydWN0b3JzLk1vZHVsZSgkZWxlbWVudCwgYmFzZU1vZHVsZU5hbWUsIGNvbGxlY3Rpb24pKTtcclxuXHRcdFx0XHR9KTtcclxuXHRcdFx0fSk7XHJcblx0XHR9IGNhdGNoIChleGNlcHRpb24pIHtcclxuXHRcdFx0ZGVmZXJyZWQucmVqZWN0KGV4Y2VwdGlvbik7XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuIGRlZmVycmVkLnByb21pc2UoKTtcclxuXHR9O1xyXG5cclxufSkoanNlLmNvcmUubW9kdWxlX2xvYWRlcik7XHJcbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBwb2x5ZmlsbHMuanMgMjAxNS0xMC0yOCBnbVxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiBQb2x5ZmlsbHMgZm9yIGNyb3NzLWJyb3dzZXIgY29tcGF0aWJpbGl0eS5cclxuICpcclxuICogQG5hbWVzcGFjZSBKU0UvQ29yZS9wb2x5ZmlsbHNcclxuICovXHJcbihmdW5jdGlvbiAoKSB7XHJcblxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHJcblx0aWYgKCFBcnJheS5wcm90b3R5cGUuaW5kZXhPZikge1xyXG5cdFx0QXJyYXkucHJvdG90eXBlLmluZGV4T2YgPSBmdW5jdGlvbiAoc2VhcmNoRWxlbWVudCwgZnJvbUluZGV4KSB7XHJcblx0XHRcdHZhciBrO1xyXG5cdFx0XHRpZiAodGhpcyA9PSBudWxsKSB7XHJcblx0XHRcdFx0dGhyb3cgbmV3IFR5cGVFcnJvcignXCJ0aGlzXCIgaXMgbnVsbCBvciBub3QgZGVmaW5lZCcpO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHR2YXIgTyA9IE9iamVjdCh0aGlzKTtcclxuXHRcdFx0dmFyIGxlbiA9IE8ubGVuZ3RoID4+PiAwO1xyXG5cclxuXHRcdFx0aWYgKGxlbiA9PT0gMCkge1xyXG5cdFx0XHRcdHJldHVybiAtMTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0dmFyIG4gPSArZnJvbUluZGV4IHx8IDA7XHJcblxyXG5cdFx0XHRpZiAoTWF0aC5hYnMobikgPT09IEluZmluaXR5KSB7XHJcblx0XHRcdFx0biA9IDA7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGlmIChuID49IGxlbikge1xyXG5cdFx0XHRcdHJldHVybiAtMTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0ayA9IE1hdGgubWF4KG4gPj0gMCA/IG4gOiBsZW4gLSBNYXRoLmFicyhuKSwgMCk7XHJcblxyXG5cdFx0XHR3aGlsZSAoayA8IGxlbikge1xyXG5cdFx0XHRcdHZhciBrVmFsdWU7XHJcblx0XHRcdFx0aWYgKGsgaW4gTyAmJiBPW2tdID09PSBzZWFyY2hFbGVtZW50KSB7XHJcblx0XHRcdFx0XHRyZXR1cm4gaztcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0aysrO1xyXG5cdFx0XHR9XHJcblx0XHRcdHJldHVybiAtMTtcclxuXHRcdH07XHJcblx0fVxyXG5cclxuXHQvLyBJbnRlcm5ldCBFeHBsb3JlciBkb2VzIG5vdCBzdXBwb3J0IHRoZSBvcmlnaW4gcHJvcGVydHkgb2YgdGhlIHdpbmRvdy5sb2NhdGlvbiBvYmplY3QuXHJcblx0Ly8gQGxpbmsgaHR0cDovL3Rvc2JvdXJuLmNvbS9hLWZpeC1mb3Itd2luZG93LWxvY2F0aW9uLW9yaWdpbi1pbi1pbnRlcm5ldC1leHBsb3JlclxyXG5cdGlmICghd2luZG93LmxvY2F0aW9uLm9yaWdpbikge1xyXG5cdFx0d2luZG93LmxvY2F0aW9uLm9yaWdpbiA9IHdpbmRvdy5sb2NhdGlvbi5wcm90b2NvbCArICcvLycgK1xyXG5cdFx0ICAgICAgICAgICAgICAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ob3N0bmFtZSArICh3aW5kb3cubG9jYXRpb24ucG9ydCA/ICc6JyArIHdpbmRvdy5sb2NhdGlvbi5wb3J0IDogJycpO1xyXG5cdH1cclxuXHJcblx0Ly8gRGF0ZS5ub3cgbWV0aG9kIHBvbHlmaWxsXHJcblx0Ly8gaHR0cHM6Ly9kZXZlbG9wZXIubW96aWxsYS5vcmcvZW4tVVMvZG9jcy9XZWIvSmF2YVNjcmlwdC9SZWZlcmVuY2UvR2xvYmFsX09iamVjdHMvRGF0ZS9ub3dcclxuXHRpZiAoIURhdGUubm93KSB7XHJcblx0XHREYXRlLm5vdyA9IGZ1bmN0aW9uIG5vdygpIHtcclxuXHRcdFx0cmV0dXJuIG5ldyBEYXRlKCkuZ2V0VGltZSgpO1xyXG5cdFx0fTtcclxuXHR9XHJcbn0pKCk7XHJcblxyXG5cclxuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIHJlZ2lzdHJ5LmpzIDIwMTUtMTAtMTMgZ21cclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG5qc2UuY29yZS5yZWdpc3RyeSA9IGpzZS5jb3JlLnJlZ2lzdHJ5IHx8IHt9O1xyXG5cclxuLyoqXHJcbiAqICMjIEpTIEVuZ2luZSBSZWdpc3RyeVxyXG4gKlxyXG4gKiBUaGlzIG9iamVjdCBjb250YWlucyBzdHJpbmcgZGF0YSB0aGF0IG90aGVyIHNlY3Rpb25zIG9mIHRoZSBlbmdpbmUgbmVlZCBpbiBvcmRlciB0b1xyXG4gKiBvcGVyYXRlIGNvcnJlY3RseS5cclxuICpcclxuICogQG5hbWVzcGFjZSBKU0UvQ29yZS9yZWdpc3RyeVxyXG4gKi9cclxuXHJcbihmdW5jdGlvbiAoLyoqIEBsZW5kcyBBZG1pbi9MaWJzL3JlZ2lzdHJ5ICovIGV4cG9ydHMpIHtcclxuXHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cclxuXHR2YXIgcmVnaXN0cnkgPSBbXTtcclxuXHJcblx0LyoqXHJcblx0ICogU2V0IGEgdmFsdWUgaW4gdGhlIHJlZ2lzdHJ5LlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtzdHJpbmd9IG5hbWUgQ29udGFpbnMgdGhlIG5hbWUgb2YgdGhlIGVudHJ5IHRvIGJlIGFkZGVkLlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSB2YWx1ZSBUaGUgdmFsdWUgdG8gYmUgd3JpdHRlbiBpbiB0aGUgcmVnaXN0cnkuXHJcblx0ICpcclxuXHQgKiBAcHVibGljXHJcblx0ICovXHJcblx0ZXhwb3J0cy5zZXQgPSBmdW5jdGlvbiAobmFtZSwgdmFsdWUpIHtcclxuXHRcdC8vIElmIGEgcmVnaXN0cnkgZW50cnkgd2l0aCB0aGUgc2FtZSBuYW1lIGV4aXN0cyBhbHJlYWR5IHRoZSBmb2xsb3dpbmcgY29uc29sZSB3YXJuaW5nIHdpbGxcclxuXHRcdC8vIGluZm9ybSBkZXZlbG9wZXJzIHRoYXQgdGhleSBhcmUgb3ZlcndyaXRpbmcgYW4gZXhpc3RpbmcgdmFsdWUsIHNvbWV0aGluZyB1c2VmdWwgd2hlbiBkZWJ1Z2dpbmcuXHJcblx0XHRpZiAodHlwZW9mIHJlZ2lzdHJ5W25hbWVdICE9PSAndW5kZWZpbmVkJykge1xyXG5cdFx0XHRqc2UuY29yZS5kZWJ1Zy53YXJuKCdUaGUgcmVnaXN0cnkgdmFsdWUgd2l0aCB0aGUgbmFtZSBcIicgKyBuYW1lICsgJ1wiIHdpbGwgYmUgb3ZlcndyaXR0ZW4uJyk7XHJcblx0XHR9XHJcblxyXG5cdFx0cmVnaXN0cnlbbmFtZV0gPSB2YWx1ZTtcclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBHZXQgYSB2YWx1ZSBmcm9tIHRoZSByZWdpc3RyeS5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBuYW1lIFRoZSBuYW1lIG9mIHRoZSBlbnRyeSB2YWx1ZSB0byBiZSByZXR1cm5lZC5cclxuXHQgKlxyXG5cdCAqIEByZXR1cm5zIHsqfSBSZXR1cm5zIHRoZSB2YWx1ZSB0aGF0IG1hdGNoZXMgdGhlIG5hbWUuXHJcblx0ICovXHJcblx0ZXhwb3J0cy5nZXQgPSBmdW5jdGlvbiAobmFtZSkge1xyXG5cdFx0cmV0dXJuIHJlZ2lzdHJ5W25hbWVdO1xyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIENoZWNrIHRoZSBjdXJyZW50IGNvbnRlbnQgb2YgdGhlIHJlZ2lzdHJ5IG9iamVjdC5cclxuXHQgKlxyXG5cdCAqIFRoaXMgbWV0aG9kIGlzIG9ubHkgYXZhaWxhYmxlIHdoZW4gdGhlIGVuZ2luZSBlbnZpcm9ubWVudCBpcyB0dXJuZWQgaW50b1xyXG5cdCAqIGRldmVsb3BtZW50LlxyXG5cdCAqXHJcblx0ICogQHB1YmxpY1xyXG5cdCAqL1xyXG5cdGV4cG9ydHMuZGVidWcgPSBmdW5jdGlvbiAoKSB7XHJcblx0XHRpZiAoanNlLmNvcmUuY29uZmlnLmdldCgnZW52aXJvbm1lbnQnKSA9PT0gJ2RldmVsb3BtZW50Jykge1xyXG5cdFx0XHRqc2UuY29yZS5kZWJ1Zy5sb2coJ1JlZ2lzdHJ5IE9iamVjdDonLCByZWdpc3RyeSk7XHJcblx0XHR9IGVsc2Uge1xyXG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ1RoaXMgZnVuY3Rpb24gaXMgbm90IGFsbG93ZWQgaW4gYSBwcm9kdWN0aW9uIGVudmlyb25tZW50LicpO1xyXG5cdFx0fVxyXG5cdH07XHJcblxyXG59KShqc2UuY29yZS5yZWdpc3RyeSk7XHJcbiJdLCJzb3VyY2VSb290IjoiL3NvdXJjZS8ifQ==
