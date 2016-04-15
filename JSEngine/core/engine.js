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
