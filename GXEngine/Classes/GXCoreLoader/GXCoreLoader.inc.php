<?php
/* --------------------------------------------------------------
   GXCoreLoader.inc.php 2015-10-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('GXCoreLoaderInterface');

/**
 * Class GXCoreLoader
 *
 * @category System
 * @package  GXCoreLoader
 */
class GXCoreLoader implements GXCoreLoaderInterface
{
	/**
	 * Contains the loader settings.
	 *
	 * @var GXCoreLoaderSettingsInterface
	 */
	protected $gxCoreLoaderSettings;

	/**
	 * Database Layer Object
	 *
	 * @var CI_DB_query_builder
	 */
	protected $ciDatabaseQueryBuilder;


	/**
	 * Class Constructor
	 *
	 * @param GXCoreLoaderSettingsInterface $gxCoreLoaderSettings
	 */
	public function __construct(GXCoreLoaderSettingsInterface $gxCoreLoaderSettings)
	{
		$this->gxCoreLoaderSettings = $gxCoreLoaderSettings;
	}


	/**
	 * Get the requested server object.
	 *
	 * @param string $serviceName
	 *
	 * @return AddressBookServiceInterface|CountryServiceInterface|CustomerServiceInterface
	 *
	 * @throws DomainException
	 *
	 * @todo Delegate to GXServiceFactory
	 */
	public function getService($serviceName)
	{
		switch($serviceName)
		{
			case 'Customer': // DEPRECATED!!
				$customerServiceFactory = $this->_getCustomerServiceFactory();
				$customerService        = $customerServiceFactory->getCustomerService();

				return $customerService;

			case 'CustomerRead':
				$customerServiceFactory = $this->_getCustomerServiceFactory();
				$customerReadService    = $customerServiceFactory->createCustomerReadService();

				return $customerReadService;

			case 'CustomerWrite':
				$customerServiceFactory = $this->_getCustomerServiceFactory();
				$customerWriteService   = $customerServiceFactory->createCustomerWriteService();

				return $customerWriteService;

			case 'AddressBook':
				$customerServiceFactory = $this->_getCustomerServiceFactory();
				$addressBookService     = $customerServiceFactory->getAddressBookService();

				return $addressBookService;

			case 'Country':
				$customerServiceFactory = $this->_getCustomerServiceFactory();
				$countryService         = $customerServiceFactory->getCountryService();

				return $countryService;

			case 'RegistrationInputValidator':
				$customerServiceFactory     = $this->_getCustomerServiceFactory();
				$registrationInputValidator = $customerServiceFactory->getCustomerRegistrationInputValidatorService();

				return $registrationInputValidator;

			case 'AccountInputValidator':
				$customerServiceFactory = $this->_getCustomerServiceFactory();
				$accountInputValidator  = $customerServiceFactory->getCustomerAccountInputValidator();

				return $accountInputValidator;

			case 'AddressInputValidator':
				$customerServiceFactory = $this->_getCustomerServiceFactory();
				$accountInputValidator  = $customerServiceFactory->getCustomerAddressInputValidatorService();

				return $accountInputValidator;

			case 'UserConfiguration':
				$db                       = $this->getDatabaseQueryBuilder();
				$userConfigurationReader  = MainFactory::create('UserConfigurationReader', $db);
				$userConfigurationWriter  = MainFactory::create('UserConfigurationWriter', $db);
				$userConfigurationService = MainFactory::create('UserConfigurationService', $userConfigurationReader,
				                                                $userConfigurationWriter);

				return $userConfigurationService;

			case 'Statistics':
				$db                = $this->getDatabaseQueryBuilder();
				$xtcPrice          = new xtcPrice($_SESSION['currency'],
				                                  $_SESSION['customers_status']['customers_status_id']);
				$statisticsService = MainFactory::create('StatisticsService', $db, $xtcPrice);

				return $statisticsService;

			case 'Email':
				$emailFactory = $this->_getEmailFactory();

				return $emailFactory->createService();

			case 'Http':
				$httpServiceFactory = MainFactory::create('HttpServiceFactory');

				return $httpServiceFactory->createService();

			default:
				throw new DomainException('Unknown service: ' . htmlentities($serviceName));
		}
	}


	/**
	 * Method depends on CodeIgniter database library
	 *
	 * @return CI_DB_query_builder
	 * 
	 * @todo check connection errors
	 * @todo escape special characters in mysqli connection string (AT)
	 * @todo use GXDatabaseAccessorInterface
	 */
	public function getDatabaseQueryBuilder()
	{
		if($this->ciDatabaseQueryBuilder == null)
		{
			$dbUser     = $this->gxCoreLoaderSettings->getDatabaseUser();
			$dbPassword = $this->gxCoreLoaderSettings->getDatabasePassword();
			$dbServer   = $this->gxCoreLoaderSettings->getDatabaseServer();
			$dbName     = $this->gxCoreLoaderSettings->getDatabaseName();
			$dbSocket   = ($this->gxCoreLoaderSettings->getDatabaseSocket()) ? '?socket='
			                                                                   . $this->gxCoreLoaderSettings->getDatabaseSocket() : '';

			$connectionString = 'mysqli://' . $dbUser . ':' . $dbPassword . '@' . $dbServer . '/' . $dbName . $dbSocket;

			$this->ciDatabaseQueryBuilder = CIDB($connectionString);
		}

		return $this->ciDatabaseQueryBuilder;
	}


	/**
	 * Get a customer service factory object.
	 * 
	 * @return CustomerServiceFactory
	 */
	protected function _getCustomerServiceFactory()
	{
		$ciDatabaseQueryBuilder = $this->getDatabaseQueryBuilder();
		$customerServiceFactory = MainFactory::create('CustomerServiceFactory', $ciDatabaseQueryBuilder);

		return $customerServiceFactory;
	}


	/**
	 * Get an email factory object.
	 * 
	 * @return EmailFactory
	 */
	protected function _getEmailFactory()
	{
		$db           = $this->getDatabaseQueryBuilder();
		$emailFactory = MainFactory::create('EmailFactory', $db);

		return $emailFactory;
	}
}
