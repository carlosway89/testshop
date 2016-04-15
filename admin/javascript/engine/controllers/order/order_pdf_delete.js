/* --------------------------------------------------------------
 order_pdf_delete.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Order PDF Delete Controller
 *
 * @module Controllers/order_pdf_delete
 */
gx.controllers.module(
	'order_pdf_delete',

	['xhr'],

	/** @lends module:Controllers/order_pdf_delete */

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------

		var $this = $(this),
			defaults = {},
			options = $.extend(true, {}, defaults, data),
			module = {};

		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------

		var _deleteHandler = function (event) {
			event.preventDefault();
			event.stopPropagation();

			var $self = $(this),
				dataset = $.extend({}, $this.data(), module._data($this));

			var href =
				'lightbox_confirm.html?section=admin_orders&amp;message=DELETE_PDF_CONFIRM_MESSAGE&amp;' +
				'buttons=cancel-delete';

			var t_a_tag = $(
				'<a href="' + href + '"></a>'
			);
			var tmp_lightbox_identifier = $(t_a_tag).lightbox_plugin(
				{
					'lightbox_width': '360px'
				});

			$('#lightbox_package_' + tmp_lightbox_identifier).on('click', '.delete', function () {
				$.lightbox_plugin('close', tmp_lightbox_identifier);
				if ($self.hasClass('active')) {
					return false;
				}
				$self.addClass('active');

				var type = $self.closest('tr').attr('class');

				jse.libs.xhr.post({
					                  'url': 'request_port.php?module=OrderAdmin&action=deletePdf',
					                  'data': {
						                  'type': type,
						                  'file': $self.attr('rel')
					                  }
				                  }).done(function (response) {
					$self.closest('tr').remove();
					if ($('tr.' + type).length === 1) {
						$('tr.' + type).show();
					}
					$('.page_token').val(response.page_token);
				});
			});
		};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Init function of the widget
		 */
		module.init = function (done) {
			$this.on('click', '.delete_pdf', _deleteHandler);
			done();
		};

		// Return data to widget engine
		return module;
	});
