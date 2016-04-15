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
