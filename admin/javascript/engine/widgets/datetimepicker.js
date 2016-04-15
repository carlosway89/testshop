/* --------------------------------------------------------------
 datetimepicker.js 2016-02-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Datetimepicker Widget
 * 
 * This widget will convert itself or multiple elements into datetimepicker instances. Check the defaults object for a 
 * list of available options. 
 * 
 * You can also set this module in a container element and provide the "data-datetimepicker-container" attribute and 
 * this plugin will initialize all the child elements that have the "datetimepicker" class into datetimepicker widgets. 
 * 
 * @link http://xdsoft.net/jqplugins/datetimepicker
 * 
 * @module widgets/Datetimepicker
 */
gx.widgets.module(
	'datetimepicker',

	[],

	function(data) {

		var
			/**
			 * Module Selector
			 *
			 * @type {object}
			 */
			$this = $(this),

			/**
			 * Default Module Options
			 *
			 * @type {object}
			 */
			defaults = {
				format: 'd.m.Y H:i',
				lang: jse.core.config.get('languageCode') === 'en' ? 'en-GB' : 'de'
			},

			/**
			 * Final Module Options
			 */
			options = $.extend(true,  {}, defaults, data),

			/**
			 * Module Instance
			 *
			 * @type {object}
			 */
			module = {};

		/**
		 * Initialize Module
		 *
		 * @param {function} done Call this method once the module is initialized.
		 */
		module.init = function(done) {
			// Check if the datetimepicker plugin is already loaded. 
			if ($.fn.datetimepicker === undefined) {
				throw new Error('The $.fn.datetimepicker plugin must be loaded before the module is initialized.'); 
			}
			
			// Check if the current element is a container and thus need to initialize the children elements. 
			if (options.container !== undefined) {
				$this.find('.datetimepicker').datetimepicker(options); 
			} else {
				$this.datetimepicker(options);	
			} 
			
			done();
		};

		return module;
	}); 