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
