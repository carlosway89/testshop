/* --------------------------------------------------------------
 delete_parcel_service.js 2014-10-14 tb@gambio
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2014 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Delete Parcel Service Controller
 *
 * @module Controllers/delete_parcel_service
 */
gx.controllers.module(
	'delete_parcel_service',

	[],

	/** @lends module:Controllers/delete_parcel_service */

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------

		var $this = $(this),
			defaults = {},
			options = $.extend(true, {}, defaults, data),
			lightbox_parameters = $this.data().lightboxParams,
			module = {};

		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------

		var _deleteHandler = function (event) {
			event.preventDefault();
			event.stopPropagation();

			var $self = $(this),
				dataset = $.extend({}, $this.data(), module._data($this));

			if ($(this).hasClass('active')) {
				return false;
			}
			$(this).addClass('active');

			jse.libs.post({
				              'url': 'request_port.php?module=ParcelServices&action=delete_parcel_service',
				              'data': {
					              'parcel_service_id': lightbox_parameters.parcel_service_id,
					              'page_token': lightbox_parameters.page_token
				              }
			              }).done(function (response) {
				$('#parcel_services_wrapper').html(response.html);
				$.lightbox_plugin('close', lightbox_parameters.identifier);
			}).fail(function (jqXHR, exception) {
				$.lightbox_plugin('error', lightbox_parameters.identifier, jqXHR, exception);
			});

		};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Init function of the widget
		 */
		module.init = function (done) {
			$this.on('click', '.delete', _deleteHandler);
			done();
		};

		// Return data to widget engine
		return module;
	});
