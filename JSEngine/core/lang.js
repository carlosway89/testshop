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
