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
