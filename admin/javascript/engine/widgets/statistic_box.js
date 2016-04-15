/* --------------------------------------------------------------
 statistic_box.js 2015-09-11 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Admin Statistic Box Widget
 *
 * Widget for showing statistics in the admin dashboard.
 *
 * **Example Markup:**
 * ```html
 * <div class="statistic-widget"
 *          data-gx-widget="statistic_box"
 *          data-statistic_box-item="sales"
 *          data-statistic_box-icon="fa-money"
 *          data-statistic_box-color="red">
 *
 *     <div class="statistic-icon"></div>
 *     <div class="statistic-text">
 *         <div class="statistic-heading"></div>
 *         <div class="statistic-subtext"></div>
 *         <div class="statistic-small-text"></div>
 *     </div>
 *
 * </div>
 * ```
 *
 * Parameters:
 * - item: The item fot which the statistics data will be shown
 * - icon: Icon to display
 * - color: Background color for the icon container
 *
 * Events:
 * - Triggering "get:data" with interval as parameter to this element will force the request.
 *
 * @module Admin/Widgets/statistic_box
 * @requires jQueryUI
 */
gx.widgets.module(
	'statistic_box',

	['loading_spinner'],

	/** @lends module:Widgets/statistic_box */

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// ELEMENT DEFINITION
		// ------------------------------------------------------------------------

		// Elements
		var $this = $(this),
			$dropdown = $('.js-interval-dropdown');

		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------

		// Widget defaults
		var defaults = {
				item: 'online',
				icon: 'fa-dashboard',
				color: 'gray'
			},
			options = $.extend(true, {}, defaults, data),
			module = {};

		// Dates
		var date = new Date(),
			todayDay = date.getDate(),
			todayMonth = date.getMonth() + 1,
			todayYear = date.getFullYear(),

			lastWeek = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 7),
			lastWeekDay = lastWeek.getDate(),
			lastWeekMonth = lastWeek.getMonth() + 1,
			lastWeekYear = lastWeek.getFullYear(),

			lastTwoWeeks = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 14),
			lastTwoWeekskDay = lastTwoWeeks.getDate(),
			lastTwoWeeksMonth = lastTwoWeeks.getMonth() + 1,
			lastTwoWeekskYear = lastTwoWeeks.getFullYear(),

			lastMonth = new Date(date.getFullYear(), date.getMonth() - 1, date.getDate()),
			lastMonthDay = lastMonth.getDate(),
			lastMonthMonth = lastMonth.getMonth() + 1,
			lastMonthYear = lastMonth.getFullYear(),

			lastThreeMonths = new Date(date.getFullYear(), date.getMonth() - 3, date.getDate()),
			lastThreeMonthsDay = lastThreeMonths.getDate(),
			lastThreeMonthsMonth = lastThreeMonths.getMonth() + 1,
			lastThreeMonthsYear = lastThreeMonths.getFullYear(),

			lastSixMonths = new Date(date.getFullYear(), date.getMonth() - 6, date.getDate()),
			lastSixMonthsDay = lastSixMonths.getDate(),
			lastSixMonthsMonth = lastSixMonths.getMonth() + 1,
			lastSixMonthsYear = lastSixMonths.getFullYear(),

			lastYear = new Date(date.getFullYear() - 1, date.getMonth(), date.getDate()),
			lastYearDay = lastYear.getDate(),
			lastYearMonth = lastYear.getMonth() + 1,
			lastYearYear = lastYear.getFullYear();

		// ------------------------------------------------------------------------
		// Maps
		// ------------------------------------------------------------------------

		// API map
		var map = {
			// Sales (Umsatz)
			sales: {
				apiUrl: 'admin.php?do=Dashboard/GetSales',
				heading: '%timespan%',
				subtext: jse.core.lang.translate('STATISTICS_SALES', 'start'),
				smallText: '(' + jse.core.lang.translate('STATISTICS_INTERVAL_TODAY', 'start') + ': ' + '%today%)',
				onClick: function () {
					switch ($dropdown.find('option:selected').val()) {
						case 'week':
							window.open('stats_sales_report.php?report=4&startD=' + lastWeekDay + '&startM=' +
							            lastWeekMonth + '&startY=' +
							            lastWeekYear + '&detail=0&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' +
							            todayMonth +
							            '&endY=' + todayYear, '_self');
							break;

						case 'two_weeks':
							window.open('stats_sales_report.php?report=3&startD=' + lastTwoWeekskDay + '&startM=' +
							            lastTwoWeeksMonth +
							            '&startY=' + lastTwoWeekskYear + '&detail=0&max=0&payment=0&export=0&sort=4&endD=' +
							            todayDay + '&endM=' +
							            todayMonth + '&endY=' + todayYear, '_self');
							break;

						case 'month':
							window.open('stats_sales_report.php?report=3&startD=' + lastMonthDay + '&startM=' +
							            lastMonthMonth +
							            '&startY=' + lastMonthYear + '&detail=0&max=0&payment=0&export=0&sort=4&endD=' +
							            todayDay + '&endM=' +
							            todayMonth + '&endY=' + todayYear, '_self');
							break;

						case 'three_months':
							window.open('stats_sales_report.php?report=2&startD=' + lastThreeMonthsDay + '&startM=' +
							            lastThreeMonthsMonth +
							            '&startY=' + lastThreeMonthsYear + '&detail=0&max=0&payment=0&export=0&sort=4&endD=' +
							            todayDay + '&endM=' +
							            todayMonth + '&endY=' + todayYear, '_self');
							break;

						case 'six_months':
							window.open('stats_sales_report.php?report=2&startD=' + lastSixMonthsDay + '&startM=' +
							            lastSixMonthsMonth +
							            '&startY=' + lastSixMonthsYear + '&detail=0&max=0&payment=0&export=0&sort=4&endD=' +
							            todayDay + '&endM=' +
							            todayMonth + '&endY=' + todayYear, '_self');
							break;

						case 'year':
							window.open('stats_sales_report.php?report=2&startD=' + lastYearDay + '&startM=' +
							            lastYearMonth + '&startY=' +
							            lastYearYear + '&detail=0&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' +
							            todayMonth +
							            '&endY=' + todayYear, '_self');
							break;
					}
				}
			},

			// Currently online (Jetzt online)
			online: {
				apiUrl: 'admin.php?do=Dashboard/GetUsersOnline',
				heading: '%timespan%',
				subtext: jse.core.lang.translate('STATISTICS_USERS_ONLINE', 'start'),
				smallText: '',
				onClick: function () {
					window.open('whos_online.php', '_self');
				}
			},

			// Visitors (Besucher)
			visitors: {
				apiUrl: 'admin.php?do=Dashboard/GetVisitors',
				heading: '%timespan%',
				subtext: jse.core.lang.translate('STATISTICS_VISITORS', 'start'),
				smallText: '(' + jse.core.lang.translate('STATISTICS_INTERVAL_TODAY', 'start') + ': ' + '%today%)',
				onClick: function () {
					window.open('gm_counter.php', '_self');
				}
			},

			// New Customers (Neue Kunden)
			newCustomers: {
				apiUrl: 'admin.php?do=Dashboard/GetNewCustomers',
				heading: '%timespan%',
				subtext: jse.core.lang.translate('STATISTICS_NEW_CUSTOMERS', 'start'),
				smallText: '(' + jse.core.lang.translate('STATISTICS_INTERVAL_TODAY', 'start') + ': ' + '%today%)',
				onClick: function () {
					window.open('customers.php', '_self');
				}
			},

			// Orders (Bestellungen)
			orders: {
				apiUrl: 'admin.php?do=Dashboard/GetOrdersCount',
				heading: '%timespan%',
				subtext: jse.core.lang.translate('STATISTICS_ORDERS_COUNT', 'start'),
				smallText: '(' + jse.core.lang.translate('STATISTICS_INTERVAL_TODAY', 'start') + ': ' + '%today%)',
				onClick: function () {
					window.open('orders.php', '_self');
				}
			},

			// Conversion Rate
			conversionRate: {
				apiUrl: 'admin.php?do=Dashboard/GetConversionRate',
				heading: '%timespan% %',
				subtext: jse.core.lang.translate('STATISTICS_CONVERSION_RATE', 'start'),
				smallText: '(' + jse.core.lang.translate('STATISTICS_INTERVAL_TODAY', 'start') + ': ' + '%today% %)',
				onClick: function () {
					window.open('gm_counter.php', '_self');
				}
			},

			// Average order total (Durchschnittlicher Bestellwert)
			avgOrderTotal: {
				apiUrl: 'admin.php?do=Dashboard/GetAverageOrderValue',
				heading: '%timespan%',
				subtext: jse.core.lang.translate('STATISTICS_AVERGAGE_ORDER_VALUE', 'start'),
				smallText: '(' + jse.core.lang.translate('STATISTICS_INTERVAL_TODAY', 'start') + ': ' + '%today%)',
				onClick: function () {
					switch ($dropdown.find('option:selected').val()) {
						case 'week':
							window.open('stats_sales_report.php?report=4&startD=' + lastWeekDay + '&startM=' +
							            lastWeekMonth + '&startY=' +
							            lastWeekYear + '&detail=2&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' +
							            todayMonth +
							            '&endY=' + todayYear, '_self');
							break;

						case 'two_weeks':
							window.open('stats_sales_report.php?report=3&startD=' + lastTwoWeekskDay + '&startM=' +
							            lastTwoWeeksMonth +
							            '&startY=' + lastTwoWeekskYear + '&detail=2&max=0&payment=0&export=0&sort=4&endD=' +
							            todayDay + '&endM=' +
							            todayMonth + '&endY=' + todayYear, '_self');
							break;

						case 'month':
							window.open('stats_sales_report.php?report=3&startD=' + lastMonthDay + '&startM=' +
							            lastMonthMonth +
							            '&startY=' + lastMonthYear + '&detail=2&max=0&payment=0&export=0&sort=4&endD=' +
							            todayDay + '&endM=' +
							            todayMonth + '&endY=' + todayYear, '_self');
							break;

						case 'three_months':
							window.open('stats_sales_report.php?report=2&startD=' + lastThreeMonthsDay + '&startM=' +
							            lastThreeMonthsMonth +
							            '&startY=' + lastThreeMonthsYear + '&detail=2&max=0&payment=0&export=0&sort=4&endD=' +
							            todayDay + '&endM=' +
							            todayMonth + '&endY=' + todayYear, '_self');
							break;

						case 'six_months':
							window.open('stats_sales_report.php?report=2&startD=' + lastSixMonthsDay + '&startM=' +
							            lastSixMonthsMonth +
							            '&startY=' + lastSixMonthsYear + '&detail=2&max=0&payment=0&export=0&sort=4&endD=' +
							            todayDay + '&endM=' +
							            todayMonth + '&endY=' + todayYear, '_self');
							break;

						case 'year':
							window.open('stats_sales_report.php?report=2&startD=' + lastYearDay + '&startM=' +
							            lastYearMonth + '&startY=' +
							            lastYearYear + '&detail=2&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' +
							            todayMonth +
							            '&endY=' + todayYear, '_self');
							break;
					}
				}
			}
		};

		// Interpolation map for replacing strings
		var interpolationMap = {
			today: '%today%',
			timespan: '%timespan%'
		};

		// ------------------------------------------------------------------------
		// PRIVATE METHODS
		// ------------------------------------------------------------------------

		/**
		 * Iterate over the interpolation map
		 * and interpolate strings with values
		 * @param {string} text - Text to interpolate
		 * @param {object} values - Values to put in
		 * @returns {string}
		 */
		var _interpolate = function (text, values) {
			for (var key in interpolationMap) {
				if (interpolationMap.hasOwnProperty(key)) {
					text = text
						.replace(interpolationMap[key], values[key]);
				}
			}
			return text;
		};

		/**
		 * Retrieves data from server
		 * @param {string} interval
		 * @private
		 */
		var _getData = function (interval) {
			// Show loading spinner
			var $spinner = window.gx.libs.loading_spinner.show($this);

			// Make AJAX call
			$.ajax({
				       url: map[options.item].apiUrl + '&interval=' + interval,
				       type: 'GET',
				       dataType: 'json'
			       })
				// On success
				.done(function (data) {
					var $heading = $this.find('.heading'),
						$subtext = $this.find('.subtext'),
						$smallText = $this.find('.small-text');

					/**
					 * Values map
					 * Keys should be the same as in the interpolationMap
					 * @type {object}
					 */
					var values = {
						timespan: data.timespan,
						today: data.today
					};

					window.gx.libs.loading_spinner.hide($spinner).done(function () {
						$this.find('.icon-container, .text-container').animate({
							                                                       opacity: 1
						                                                       }, 'slow');
					});

					// Interpolate heading text
					$heading.text(
						_interpolate(map[options.item].heading, values)
					);

					// Interpolate subtext
					$subtext.text(
						_interpolate(map[options.item].subtext, values)
					);

					// Interpolate small text
					$smallText.text(
						_interpolate(map[options.item].smallText, values)
					);
				})

				// On fail
				.fail(function (data) {
					throw new Error('Failed to load statistic resource');
				});
		};

		/**
		 * Adds classes, events and elements to the widget
		 * @private
		 */
		var _prepare = function () {
			var $iconContainer, $icon, $textContainer,
				$heading, $subtext, $smallText;

			// Prepare icon container
			$icon = $('<i>');
			$icon
				.addClass('fa fa-fw fa-lg')
				.addClass(options.icon);

			$iconContainer = $('<div>');
			$iconContainer
				.addClass('icon-container span4')
				.addClass(options.color)
				.append($icon);

			// Prepare text container
			$heading = $('<div>');
			$heading.addClass('heading');

			$subtext = $('<div>');
			$subtext.addClass('subtext');

			$smallText = $('<div>');
			$smallText.addClass('small-text');

			$textContainer = $('<div>');
			$textContainer
				.addClass('text-container span8')
				.append($heading)
				.append($subtext)
				.append($smallText);

			// Handle click event
			$this.on('click', function (event) {
				map[options.item].onClick(event);
			});

			// Compose HTML
			$this
				.addClass('toolbar grid')
				.append($iconContainer)
				.append($textContainer);

			// Event handler: Trigger data request
			$this.on('get:data', function (event, interval) {
				if (interval) {
					_getData(interval);
				}
			});
		};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Initialize method of the widget, called by the engine.
		 */
		module.init = function (done) {
			_prepare();
			done();
		};

		// Return data to module engine.
		return module;
	});
