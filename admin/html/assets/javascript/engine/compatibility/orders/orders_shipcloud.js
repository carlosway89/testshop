/* --------------------------------------------------------------
	orders_shipcloud.js 2016-01-25
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * ## Orders Shipcloud Module
 *
 * This module implements the user interface for creating shipping labels via Shipcloud.io
 *
 * @module Compatibility/orders_shipcloud
 */
gx.compatibility.module(
	'orders_shipcloud',

	[
		jse.core.config.get('shopUrl') + '/admin/html/assets/javascript/engine/libs/action_mapper',
		jse.core.config.get('shopUrl') + '/admin/html/assets/javascript/engine/libs/button_dropdown'
	],

	/**  @lends module:Compatibility/orders_shipcloud */
	function (data) {

		'use strict';

		var
		/**
		 * Module Selector
		 *
		 * @var {object}
		 */
			$this = $(this),

			/**
			 * The mapper library
			 *
			 * @var {object}
			 */
			mapper = window.gx.libs.action_mapper,

			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};

		var _inputCheckHandler = function(e)
		{
			var carrier = $('#sc_single_form select[name="carrier"]').val(),
				input_name = $(this).attr('name'),
				input_value = $(this).val(),
				length_limits = {
					dhl: {
						'to[company]': {min: 2, max: 30, empty_allowed: true},
						'to[last_name]': {min: 1, max: 30, empty_allowed: false},
						'to[street]': {min: 1, max: 40, empty_allowed: false},
						'to[street_no]': {min: 1, max: 5, empty_allowed: false},
						'to[zip_code]': {min: 5, max: 5, empty_allowed: false},
						'to[city]': {min: 1, max: 50, empty_allowed: false}
					},
					dpd: {
						'to[company]': {min: 1, max: 35, empty_allowed: true},
						'to[street]': {min: 1, max: 35, empty_allowed: false},
						'to[street_no]': {min: 0, max: 8, empty_allowed: false},
						'to[zip_code]': {min: 1, max: 9, empty_allowed: false},
						'to[city]': {min: 1, max: 35, empty_allowed: false}
					},
					ups: {
						'to[company]': {min: 1, max: 200, empty_allowed: true},
						'to[last_name]': {min: 1, max: 200, empty_allowed: false},
						'to[street]': {min: 1, max: 200, empty_allowed: false},
						'to[street_no]': {min: 1, max: 10, empty_allowed: false},
						'to[zip_code]': {min: 0, max: 12, empty_allowed: false},
						'to[city]': {min: 1, max: 200, empty_allowed: false}
					},
					hermes: {
						'to[company]': {min: 1, max: 200, empty_allowed: true},
						'to[last_name]': {min: 1, max: 200, empty_allowed: false},
						'to[street]': {min: 1, max: 200, empty_allowed: false},
						'to[street_no]': {min: 1, max: 10, empty_allowed: false},
						'to[zip_code]': {min: 0, max: 12, empty_allowed: false},
						'to[city]': {min: 1, max: 200, empty_allowed: false}
					},
					gls: {
						'to[company]': {min: 1, max: 200, empty_allowed: true},
						'to[last_name]': {min: 1, max: 200, empty_allowed: false},
						'to[street]': {min: 1, max: 200, empty_allowed: false},
						'to[street_no]': {min: 1, max: 10, empty_allowed: false},
						'to[zip_code]': {min: 0, max: 12, empty_allowed: false},
						'to[city]': {min: 1, max: 200, empty_allowed: false}
					},
					fedex: {
						'to[company]': {min: 1, max: 200, empty_allowed: true},
						'to[last_name]': {min: 1, max: 200, empty_allowed: false},
						'to[street]': {min: 1, max: 200, empty_allowed: false},
						'to[street_no]': {min: 1, max: 10, empty_allowed: false},
						'to[zip_code]': {min: 0, max: 12, empty_allowed: false},
						'to[city]': {min: 1, max: 200, empty_allowed: false}
					},
					liefery: {
						'to[company]': {min: 1, max: 200, empty_allowed: true},
						'to[last_name]': {min: 1, max: 200, empty_allowed: false},
						'to[street]': {min: 1, max: 200, empty_allowed: false},
						'to[street_no]': {min: 1, max: 10, empty_allowed: false},
						'to[zip_code]': {min: 0, max: 12, empty_allowed: false},
						'to[city]': {min: 1, max: 200, empty_allowed: false}
					}
				},
				min_field = 1,
				max_field = 200,
				field_length;
			field_length = input_value ? input_value.length : 0;

			if(input_name && length_limits[carrier].hasOwnProperty(input_name))
			{
				min_field = length_limits[carrier][input_name].min;
				max_field = length_limits[carrier][input_name].max;
			}

			// alert('input change '+input_name+' to '+input_value+' for carrier '+carrier+
			//  ' min: '+min_field+' max: '+max_field+' length: '+field_length);
			if((field_length < min_field && !(length_limits[carrier][input_name].empty_allowed === true && field_length === 0)) ||
				field_length > max_field)
			{
				$(this).addClass('sc_input_invalid');
			}
			else
			{
				$(this).removeClass('sc_input_invalid');
			}

			if(carrier === 'dpd' && (input_name === 'to[last_name]' || input_name === 'to[first_name]'))
			{
				var added_length;
				added_length = $('#sc_single_form input[name="to[last_name]"]').val().length +
				               $('#sc_single_form input[name="to[first_name]"]').val().length;
				if(added_length < 1 || added_length > 35)
				{
					$('#sc_single_form input[name="to[last_name]"]').addClass('sc_input_invalid');
					$('#sc_single_form input[name="to[first_name]"]').addClass('sc_input_invalid');
				}
				else
				{
					$('#sc_single_form input[name="to[last_name]"]').removeClass('sc_input_invalid');
					$('#sc_single_form input[name="to[first_name]"]').removeClass('sc_input_invalid');
				}
			}
		};

		var _singleFormInit = function() {
			$('#sc_modal_content').removeClass('sc_loading');
			if ($('#sc_single_container').data('is_configured') == '1') {
				$('#sc_show_labels').show();
			}
			else {
				$('#sc_show_labels').hide();
			}
			// $('#sc_single_form input[type="text"]').on('change', _inputCheckHandler);
			$('#sc_single_form').on('submit', function(e) { e.preventDefault(); });
			$('#sc_single_form input.create_label').on('click', _singleFormSubmitHandler);
			$('#sc_single_form select[name="carrier"]').on('change', function(e) {
				$('#sc_single_form input[type="text"]').trigger('change');
				$('#sc_single_form .carrier-specific').not('.carrier-'+$(this).val()).hide('fast');
				$('#sc_single_form .carrier-'+$(this).val()).not(':visible').show('fast');
			});
			$('#sc_single_form .price_value').on('change', function() {
				$('#sc_single_form div.sc_quote').html('');
			});
			$('#sc_package_template').on('change', _templateSelectionHandler);
			$('#sc_single_form input.template_value').on('change', function() { $('#sc_package_template').val('-1'); });
			//_inputCheckHandler();
			$('#sc_get_quote').button('disable');
			$('#sc_single_form input[name="quote_carriers[]"]').on('change', function() {
				if($('#sc_single_form input[name="quote_carriers[]"]:checked').length > 0)
				{
					$('#sc_get_quote').button('enable');
				}
				else
				{
					$('#sc_get_quote').button('disable');
				}
			});
			$('#sc_single_form input[name="quote_carriers[]"]:first').trigger('change');
		};

		var _templateSelectionHandler = function(e) {
			var $form, $template;
			$form = $(this).closest('form');
			$template = $('option:selected', $(this));
			if($template.val() !== '-1')
			{
				$('input[name="package[weight]"]', $form).val($template.data('weight'));
				$('input[name="package[height]"]', $form).val($template.data('height'));
				$('input[name="package[width]"]', $form).val($template.data('width'));
				$('input[name="package[length]"]', $form).val($template.data('length'));
			}
		};

		var _openSingleFormModal = function(event)
		{
			var orderId = $(event.target).parents('tr').data('row-id') || $('body').find('#gm_order_id').val();
			$('#sc_modal_content').empty().addClass('sc_loading');
			var button_create_label = jse.core.lang.translate('create_label', 'shipcloud'),
				shipcloud_modal_buttons = [
					{
						'text': jse.core.lang.translate('close', 'buttons'),
						'class': 'btn',
						'click': function () {
							$(this).dialog('close');
							$('#sc_get_quote').show();
						}
					}
				];
			shipcloud_modal_buttons.push({
				'text': jse.core.lang.translate('show_existing_labels', 'shipcloud'),
				'class': 'btn',
				'click': _showLabelsHandler,
				'id': 'sc_show_labels'
			});
			/*
			shipcloud_modal_buttons.push({
				'text': jse.core.lang.translate('create_labels', 'shipcloud'),
				'class': 'btn btn-primary hidden',
				'click': _singleFormSubmitHandler,
				'id': 'sc_create_label'
			});
			*/
			shipcloud_modal_buttons.push({
				'text': jse.core.lang.translate('get_quotes', 'shipcloud'),
				'class': 'btn btn-primary',
				'click': _singleFormGetQuoteHandler,
				'id': 'sc_get_quote'
			});

			$('#shipcloud_modal').dialog({
				autoOpen: false,
				modal: true,
				'title': jse.core.lang.translate('create_label', 'shipcloud'),
				'dialogClass': 'gx-container',
				buttons: shipcloud_modal_buttons,
				width: 1000,
				position: { my: 'center top', at: 'center bottom', of: '.main-top-header' }
			});
			$('#shipcloud_modal').dialog('open');
			$('#sc_modal_content').load('admin.php?do=Shipcloud/CreateLabelForm&orders_id=' + orderId,
				_singleFormInit);
		};

		var _addShipcloudDropdownEntry = function () {
			$('.gx-orders-table tr').not('.dataTableHeadingRow').each(function () {
				jse.libs.button_dropdown.mapAction($(this), 'label', 'shipcloud', _openSingleFormModal);
			});
			jse.libs.button_dropdown.mapAction($('.order-footer'), 'label', 'shipcloud', _openSingleFormModal);
		};

		var _loadLabelList = function (orders_id)
		{
			//console.log('loading label list for '+ orders_id);
			$('#sc_modal_content').load('admin.php?do=Shipcloud/LoadLabelList&orders_id=' + orders_id,
			function () {
				$('#shipcloud_modal').dialog({
					'title': jse.core.lang.translate('labellist_for', 'shipcloud') + ' ' + orders_id
				});
				$('#sc_modal_content').removeClass('sc_loading');
			});
		};

		var _loadUnconfiguredNote = function ()
		{
			$('#sc_modal_content').load('admin.php?do=Shipcloud/UnconfiguredNote');
		};

		var _showLabelsHandler = function (e) {
			var orders_id = $('#sc_single_form input[name="orders_id"]').val();
			//console.log('show labels for ' + orders_id);
			$('#sc_modal_content').empty().addClass('sc_loading');
			_loadLabelList(orders_id);
			$('#sc_show_labels').hide();
			$('#sc_get_quote').hide();
			return false;
		};

		var _singleFormGetQuoteHandler = function()
		{
			var $form = $('#sc_single_form'),
			    quote = '';

			$('#sc_single_form .sc_quote').html('');
			$('#sc_single_form .sc_quote').attr('title', '');
			//$('#sc_single_form input.create_label').hide();

			$('input[name="quote_carriers[]"]:checked').each(function() {
				var carrier = $(this).val(),
				    $create_label = $('input.create_label', $(this).closest('tr'));
				$('input[name="carrier"]', $form).val(carrier);
				$('#sc_quote_'+carrier).html(jse.core.lang.translate('loading', 'shipcloud'));
				$.ajax({
					type: 'POST',
					url: jse.core.config.get('shopUrl') + '/admin/admin.php?do=Shipcloud/GetShipmentQuote',
					data: $form.serialize(),
					dataType: 'json'
				})
				.done(function (data) {
					// if(fb) console.log(data);
					if(data.result === 'OK')
					{
						//quote = jse.core.lang.translate('shipment_quote', 'shipcloud');
						//quote += ': ' + data.shipment_quote;
						quote = data.shipment_quote;
						$('#sc_quote_'+carrier).html(quote);
						// $create_label.show();
					}
					else if(data.result === 'ERROR')
					{
						$('#sc_quote_'+carrier).html(jse.core.lang.translate('not_possible', 'shipcloud'));
						//$('#sc_quote_'+carrier+' + span').html(data.error_message);
						//$('#sc_quote_'+carrier).html('--');
						$('#sc_quote_'+carrier).attr('title', data.error_message);
					}
					else if(data.result === 'UNCONFIGURED')
					{
						_loadUnconfiguredNote();
					}
					// $('#sc_quote').html(quote);
				})
				.fail(function (data) {
					quote = jse.core.lang.translate('get_quote_error', 'shipcloud');
					$('#sc_quote_'+carrier).html(quote);
				});
			});

			$('input[name="carrier"]', $form).val('');
		};

		var _singleFormSubmitHandler = function (e) {
			var carrier, formdata;
			$('#sc_show_labels').hide();
			$('#sc_get_quote').hide();
			carrier = $(this).attr('name');
			$('input[name="carrier"]').val(carrier);
			formdata = $('#sc_single_form').serialize();
			$('#sc_modal_content').empty().addClass('sc_loading');
			// alert('data: '+formdata);
			$.ajax({
				type: 'POST',
				url: jse.core.config.get('shopUrl') + '/admin/admin.php?do=Shipcloud/CreateLabelFormSubmit',
				data: formdata,
				dataType: 'json'
			})
			.done(function (data) {
				$('#sc_modal_content').removeClass('sc_loading');
				if (data.result === 'UNCONFIGURED')
				{
					_loadUnconfiguredNote();
				}
				else if (data.result === 'OK')
				{
					_loadLabelList(data.orders_id);
				}
				else
				{
					if (data.error_message)
					{
						$('#sc_modal_content').html('<div class="sc_error">'+data.error_message+'</div>');
					}
				}
			})
			.fail(function (data) {
				alert(jse.core.lang.translate('submit_error', 'shipcloud'));
			});
			return false;
		};

		var _multiDropdownHandler = function(e)
		{
			var selected_orders = [], orders_param = '';
			$('input[name="gm_multi_status[]"]:checked').each(function() {
				selected_orders.push($(this).val());
			});
			$('#sc_modal_content').empty().addClass('sc_loading');
			var shipcloud_modal_buttons = [];
			shipcloud_modal_buttons.push({
				'text': jse.core.lang.translate('close', 'buttons'),
				'class': 'btn',
				'click': function () {
					$(this).dialog('close');
					$('#sc_get_quote').show();
				}
			});
			/*
			shipcloud_modal_buttons.push({
				'text': jse.core.lang.translate('create_labels', 'shipcloud'),
				'class': 'btn btn-primary',
				'click': _multiFormSubmitHandler,
				'id': 'sc_create_label'
			});
			*/
			shipcloud_modal_buttons.push({
				'text': jse.core.lang.translate('get_quotes', 'shipcloud'),
				'class': 'btn btn-primary',
				'click': _multiFormGetQuoteHandler,
				'id': 'sc_get_quote'
			});

			$('#shipcloud_modal').dialog({
				autoOpen: false,
				modal: true,
				'title': jse.core.lang.translate('create_labels', 'shipcloud'),
				'dialogClass': 'gx-container',
				buttons: shipcloud_modal_buttons,
				width: 1000,
				position: { my: 'center top', at: 'center bottom', of: '.main-top-header' }
			});

			$('#shipcloud_modal').dialog('open');
			selected_orders.forEach(function(item) {
				orders_param += 'orders[]='+item+'&';
			});
			$('#sc_modal_content').load('admin.php?do=Shipcloud/CreateMultiLabelForm&'+orders_param, _multiFormInit);
		};

		var _multiFormInit = function() {
			$('#shipcloud_modal').dialog({
				'title': jse.core.lang.translate('create_labels', 'shipcloud')
			});
			$('#sc_modal_content').removeClass('sc_loading');
			$('#sc_multi_form').on('submit', function(e) { e.preventDefault(); return false; });
			$('#sc_create_label').hide();
			// $('#sc_get_quote').show();
			$('#sc_show_labels').hide();
			$('#sc_modal_content input, #sc_modal_content select').on('change', function() {
				// $('#sc_create_label').hide();
				// $('#sc_get_quote').show();
				// $('#sc_quote').html('&nbsp;');
				$('.sc_multi_quote').hide();
			});
			$('#sc_package_template').on('change', _templateSelectionHandler);
			$('input.create_label').on('click', _multiFormSubmitHandler);
		};

		var _multiFormSubmitHandler = function(event) {
			var formdata, carrier;
			carrier = $(this).attr('name');
			$('#sc_multi_form input[name="carrier"]').val(carrier);
			formdata = $('#sc_multi_form').serialize();
			$('#sc_modal_content').empty().addClass('sc_loading');
			$.ajax({
				type: 'POST',
				url: jse.core.config.get('shopUrl') + '/admin/admin.php?do=Shipcloud/CreateMultiLabelFormSubmit',
				data: formdata,
				dataType: 'json'
			})
			.done(function (data) {
				$('#sc_modal_content').removeClass('sc_loading');
				if (data.result === 'UNCONFIGURED')
				{
					_loadUnconfiguredNote();
				}
				else if (data.result === 'OK')
				{
					_loadMultiLabelList(data.orders_ids);
				}
				else
				{
					if (data.error_message)
					{
						$('#sc_modal_content').html('<div class="sc_error">'+data.error_message+'</div>');
					}
				}
			})
			.fail(function (data) {
				alert(jse.core.lang.translate('submit_error', 'shipcloud'));
			});
			return false;
		};

		var _loadMultiLabelList = function(orders_ids)
		{
			$('#sc_modal_content').load(
				jse.core.config.get('shopUrl') + '/admin/admin.php?do=Shipcloud/LoadMultiLabelList',
				{ 'orders_ids[]': orders_ids },
				function () {
					$('#shipcloud_modal').dialog({
						'title': jse.core.lang.translate('labellist', 'shipcloud')
					});
					$('#sc_modal_content').removeClass('sc_loading');
				}
			);
		};

		var _multiFormGetQuoteHandler = function() {
			var formdata;
			$('div.sc_quote').html('');
			formdata = $('#sc_multi_form').serialize();
			$.ajax({
				type: 'POST',
				url: jse.core.config.get('shopUrl') + '/admin/admin.php?do=Shipcloud/GetMultiShipmentQuote',
				data: formdata,
				dataType: 'json'
			})
			.done(function (data) {
				if(data.result === 'OK')
				{
					for(var squote in data.shipment_quotes) {
						$('#sc_multi_quote_' + data.shipment_quotes[squote].orders_id)
							.html(data.shipment_quotes[squote].shipment_quote);
					}
					$('div.sc_multi_quote').show('fast');

					for(var carrier in data.carriers_total)
					{
						$('#sc_quote_'+carrier).html(data.carriers_total[carrier]);
					}
				}
			})
			.fail(function (data) {
				alert(jse.core.lang.translate('submit_error', 'shipcloud'));
			});
			return false;
		};


		module.init = function (done) {
			$('body').prepend($('<div id="shipcloud_modal" title="' + jse.core.lang.translate(
					'create_label_window_title', 'shipcloud') +
				'" style="display: none;"><div id="sc_modal_content"></div></div>'));

			var interval_counter = 10,
			    interval = setInterval(function () {
				if ($('.js-button-dropdown').length) {
					clearInterval(interval);
					_addShipcloudDropdownEntry();
				}
				if(interval_counter-- === 0)
				{
					clearInterval(interval);
				}
			}, 400);

			jse.libs.button_dropdown.mapAction($('#orders-table-dropdown'), 'create_labels', 'shipcloud', _multiDropdownHandler);

			done();
		};

		return module;
	});
