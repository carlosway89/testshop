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
