<?php
/* --------------------------------------------------------------
   DashboardController.inc.php 2015-09-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class DashboardController
 *
 * PHP controller class for the dashboard page of the admin section. The statistic results
 * are generated within this class and provided to the frontend through AJAX calls.
 *
 * @category System
 * @package  Controllers
 */
class DashboardController extends AdminHttpViewController
{
	/**
	 * @var GXCoreLoaderSettingsInterface
	 */
	private $settings;

	/**
	 * @var GXCoreLoaderInterface
	 */
	private $loader;


	/**
	 * Override "proceed" method of parent and use it for initialization.
	 *
	 * This method must call the parent "proceed" in order to work properly.
	 *
	 * @param HttpContextInterface $httpContext
	 */
	public function proceed(HttpContextInterface $httpContext)
	{
		$this->settings = MainFactory::create('GXCoreLoaderSettings');
		$this->loader   = MainFactory::create('GXCoreLoader', $this->settings);
		// Set the template directory.
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/');
		// Call the parent "proceed" method.
		parent::proceed($httpContext);
	}


	/**
	 * Get latest orders.
	 */
	public function actionGetLatestOrders()
	{
		$db       = $this->loader->getDatabaseQueryBuilder();
		$lang     = MainFactory::create_object('LanguageTextManager', array('orders', $_SESSION['languages_id']));

		include_once(DIR_FS_INC.'get_payment_title.inc.php');

		$db->select('*')
		   ->from('orders')
		   ->join('orders_total', 'orders_total.orders_id = orders.orders_id', 'inner')
		   ->where(array('orders_total.class' => 'ot_total'))
		   ->limit(5)
		   ->order_by('orders.date_purchased', 'desc');

		$data = $db->get()->result_array();

		$statuses = $db->get_where('orders_status', array('language_id' => $_SESSION['languages_id']))->result_array();

		// Parse order statuses with the "orders.php" logic.

		foreach($data as &$row)
		{
			if(!empty($row['payment_method']))
			{
				$row['payment_method'] = get_payment_title($row['payment_method']);
			}

			if($row['orders_status'] == '0')
			{
				$row['orders_status_name'] = $lang->get_text('TEXT_VALIDATING');
			}
			else
			{
				foreach($statuses as $status)
				{
					if($status['orders_status_id'] === $row['orders_status'])
					{
						$row['orders_status_name'] = $status['orders_status_name'];
						break 1;
					}
				}
			}
		}

		return new JsonHttpControllerResponse(array('data' => $data));
	}


	/**
	 * Gets the amount of users who are currently online
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionGetUsersOnline()
	{
		/**
		 * @var $service StatisticsService
		 */
		$service = $this->loader->getService('Statistics');

		return new JsonHttpControllerResponse(array(
			                                      'timespan' => $service->getUsersOnline(),
			                                      'today'    => $service->getUsersOnline()
		                                      ));
	}


	/**
	 * Gets the amount of Visitors in the given timespan
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionGetVisitors()
	{
		/**
		 * @var $service StatisticsService
		 */
		$service = $this->loader->getService('Statistics');

		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $service->getVisitorsLastWeek();
				break;

			case 'two_weeks':
				$timespan = $service->getVisitorsLastTwoWeeks();
				break;

			case 'month':
				$timespan = $service->getVisitorsLastMonth();
				break;

			case 'three_months':
				$timespan = $service->getVisitorsLastThreeMonths();
				break;

			case 'six_months':
				$timespan = $service->getVisitorsLastSixMonths();
				break;

			case 'year':
				$timespan = $service->getVisitorsLastYear();
				break;

			case 'today':
			default:
				$timespan = $service->getVisitorsToday();
				break;
		}

		return new JsonHttpControllerResponse(array(
			                                      'timespan' => $timespan,
			                                      'today'    => $service->getVisitorsToday()
		                                      ));
	}


	/**
	 * Gets the amount of Visitors in the given timespan
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionGetNewCustomers()
	{
		/**
		 * @var $service StatisticsService
		 */
		$service = $this->loader->getService('Statistics');

		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $service->getNewCustomersLastWeek();
				break;

			case 'two_weeks':
				$timespan = $service->getNewCustomersLastTwoWeeks();
				break;

			case 'month':
				$timespan = $service->getNewCustomersLastMonth();
				break;

			case 'three_months':
				$timespan = $service->getNewCustomersLastThreeMonths();
				break;

			case 'six_months':
				$timespan = $service->getNewCustomersLastSixMonths();
				break;

			case 'year':
				$timespan = $service->getNewCustomersLastYear();
				break;

			case 'today':
			default:
				$timespan = $service->getNewCustomersToday();
				break;
		}

		return new JsonHttpControllerResponse(array(
			                                      'timespan' => $timespan,
			                                      'today'    => $service->getNewCustomersToday()
		                                      ));
	}


	/**
	 * Gets the count of orders in the given timespan
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionGetOrdersCount()
	{
		/**
		 * @var $service StatisticsService
		 */
		$service = $this->loader->getService('Statistics');

		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $service->getOrdersCountLastWeek();
				break;

			case 'two_weeks':
				$timespan = $service->getOrdersCountLastTwoWeeks();
				break;

			case 'month':
				$timespan = $service->getOrdersCountLastMonth();
				break;

			case 'three_months':
				$timespan = $service->getOrdersCountLastThreeMonths();
				break;

			case 'six_months':
				$timespan = $service->getOrdersCountLastSixMonths();
				break;

			case 'year':
				$timespan = $service->getOrdersCountLastYear();
				break;

			case 'today':
			default:
				$timespan = $service->getOrdersCountToday();
				break;
		}

		return new JsonHttpControllerResponse(array(
			                                      'timespan' => $timespan,
			                                      'today'    => $service->getOrdersCountToday()
		                                      ));
	}


	/**
	 * Gets the conversion rate in the given timespan
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionGetConversionRate()
	{
		/**
		 * @var $service StatisticsService
		 */
		$service = $this->loader->getService('Statistics');

		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $service->getConversionRateLastWeek();
				break;

			case 'two_weeks':
				$timespan = $service->getConversionRateLastTwoWeeks();
				break;

			case 'month':
				$timespan = $service->getConversionRateLastMonth();
				break;

			case 'three_months':
				$timespan = $service->getConversionRateLastThreeMonths();
				break;

			case 'six_months':
				$timespan = $service->getConversionRateLastSixMonths();
				break;

			case 'year':
				$timespan = $service->getConversionRateLastYear();
				break;

			case 'today':
			default:
				$timespan = $service->getConversionRateToday();
				break;
		}

		return new JsonHttpControllerResponse(array(
			                                      'timespan' => $timespan,
			                                      'today'    => $service->getConversionRateToday()
		                                      ));
	}


	/**
	 * Get sales data for the dashboard statistic.
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionGetSalesStatisticsData()
	{
		/**
		 * @var $service StatisticsService
		 */
		$service = $this->loader->getService('Statistics');

		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $service->getSalesStatisticsDataLastWeek();
				break;
			case 'two_weeks':
				$timespan = $service->getSalesStatisticsDataLastTwoWeeks();
				break;
			case 'month':
				$timespan = $service->getSalesStatisticsDataLastMonth();
				break;
			case 'three_months':
				$timespan = $service->getSalesStatisticsDataLastThreeMonth();
				break;
			case 'six_months':
				$timespan = $service->getSalesStatisticsDataLastSixMonth();
				break;
			case 'year':
				$timespan = $service->getSalesStatisticsDataLastYear();
				break;
			default:
				$timespan = $service->getSalesStatisticsDataLastThreeMonth();
				break;
		}

		return new JsonHttpControllerResponse($timespan);
	}


	/**
	 * Get order data for the dashboard statistic.
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionGetOrderStatisticsData()
	{
		/**
		 * @var $service StatisticsService
		 */
		$service = $this->loader->getService('Statistics');

		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $service->getOrdersStatisticsDataLastWeek();
				break;
			case 'two_weeks':
				$timespan = $service->getOrdersStatisticsDataLastTwoWeek();
				break;
			case 'month':
				$timespan = $service->getOrdersStatisticsDataLastMonth();
				break;
			case 'three_months':
				$timespan = $service->getOrderStatisticsDataLastThreeMonth();
				break;
			case 'six_months':
				$timespan = $service->getOrderStatisticsDataLastSixMonth();
				break;
			case 'year':
				$timespan = $service->getOrderStatisticsDataLastYear();
				break;
			default:
				$timespan = $service->getOrderStatisticsDataLastSixMonth();
				break;
		}

		return new JsonHttpControllerResponse($timespan);
	}


	/**
	 * Get visitor data for the dashboard statistic.
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionGetVisitorsStatisticsData()
	{
		/**
		 * @var $service StatisticsService
		 */
		$service = $this->loader->getService('Statistics');

		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $service->getVisitorsStatisticsDataLastWeek();
				break;
			case 'two_weeks':
				$timespan = $service->getVisitorsStatisticsDataLastTwoWeeks();
				break;
			case 'month':
				$timespan = $service->getVisitorsStatisticsDataLastMonth();
				break;
			case 'three_months':
				$timespan = $service->getVisitorsStatisticsDataLastThreeMonth();
				break;
			case 'six_months':
				$timespan = $service->getVisitorsStatisticsDataLastSixMonth();
				break;
			case 'year':
				$timespan = $service->getVisitorsStatisticsDataLastYear();
				break;
			default:
				$timespan = $service->getVisitorsStatisticsDataLastThreeMonth();
				break;
		}

		return new JsonHttpControllerResponse($timespan);
	}


	/**
	 * Get new customer data for the dashboard statistic.
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionGetNewCustomerStatisticsData()
	{
		/**
		 * @var $service StatisticsService
		 */
		$service = $this->loader->getService('Statistics');

		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $service->getNewCustomersStatisticsDataLastWeek();
				break;
			case 'two_weeks':
				$timespan = $service->getNewCustomersStatisticsDataLastTwoWeeks();
				break;
			case 'month':
				$timespan = $service->getNewCustomersStatisticsDataLastMonth();
				break;
			case 'three_months':
				$timespan = $service->getNewCustomersStatisticsDataLastThreeMonth();
				break;
			case 'six_months':
				$timespan = $service->getNewCustomersStatisticsDataLastSixMonth();
				break;
			case 'year':
				$timespan = $service->getNewCustomersStatisticsDataLastYear();
				break;
			default:
				$timespan = $service->getNewCustomersStatisticsDataLastThreeMonth();
				break;
		}

		return new JsonHttpControllerResponse($timespan);
	}


	/**
	 * Gets the sales rate in the given timespan
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionGetSales()
	{
		/**
		 * @var $service StatisticsService
		 */
		$service = $this->loader->getService('Statistics');

		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $service->getSalesLastWeek();
				break;

			case 'two_weeks':
				$timespan = $service->getSalesLastTwoWeeks();
				break;

			case 'month':
				$timespan = $service->getSalesLastMonth();
				break;

			case 'three_months':
				$timespan = $service->getSalesLastThreeMonths();
				break;

			case 'six_months':
				$timespan = $service->getSalesLastSixMonths();
				break;

			case 'year':
				$timespan = $service->getSalesLastYear();
				break;

			case 'today':
			default:
				$timespan = $service->getSalesToday();
				break;
		}

		return new JsonHttpControllerResponse(array(
			                                      'timespan' => $timespan,
			                                      'today'    => $service->getSalesToday()
		                                      ));
	}


	/**
	 * Gets the average order value in the given timespan
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionGetAverageOrderValue()
	{
		/**
		 * @var $service StatisticsService
		 */
		$service = $this->loader->getService('Statistics');

		switch($this->_getQueryParameter('interval'))
		{
			case 'week':
				$timespan = $service->getAverageOrderValueLastWeek();
				break;

			case 'two_weeks':
				$timespan = $service->getAverageOrderValueLastTwoWeeks();
				break;

			case 'month':
				$timespan = $service->getAverageOrderValueLastMonth();
				break;

			case 'three_months':
				$timespan = $service->getAverageOrderValueLastThreeMonths();
				break;

			case 'six_months':
				$timespan = $service->getAverageOrderValueLastSixMonths();
				break;

			case 'year':
				$timespan = $service->getAverageOrderValueLastYear();
				break;

			case 'today':
			default:
				$timespan = $service->getAverageOrderValueToday();
				break;
		}

		return new JsonHttpControllerResponse(array(
			                                      'timespan' => $timespan,
			                                      'today'    => $service->getAverageOrderValueToday()
		                                      ));
	}
}
