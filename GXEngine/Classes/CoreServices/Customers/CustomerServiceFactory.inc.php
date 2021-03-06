<?php
/* --------------------------------------------------------------
   CustomerServiceFactory.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCustomerServiceFactory');

/**
 * Class CustomerServiceFactory
 * 
 * Factory class for all needed customer data.
 * 
 * @category System
 * @package Customers
 * @extends AbstractCustomerServiceFactory
 */
class CustomerServiceFactory extends AbstractCustomerServiceFactory
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $ciDatabaseQueryBuilder;

	public function __construct(CI_DB_query_builder $ciDatabaseQueryBuilder)
	{
		$this->ciDatabaseQueryBuilder = $ciDatabaseQueryBuilder;
	}

	/**
	 * Creates a new country service object
	 * 
	 * @return CountryService
	 */
	public function getCountryService()
	{
		$customerCountryRepo = $this->_getCustomerCountryRepository();
		$customerCountryZoneRepo = $this->_getCustomerCountryZoneRepository();

		$countryService = MainFactory::create('CountryService',
											  $customerCountryRepo,
											  $customerCountryZoneRepo);
		return $countryService;
	}

	/**
	 * Creates a customer service object
	 * 
	 * @return CustomerService
	 */
	public function getCustomerService()
	{
		$customerReadService = $this->createCustomerReadService();
		$customerWriteService = $this->createCustomerWriteService();
				
		$customerService = MainFactory::create('CustomerService',
		                                       $customerReadService,
		                                       $customerWriteService);
		return $customerService;
	}


	/**
	 * Creates a customer read service object
	 *
	 * @return CustomerReadService
	 */
	public function createCustomerReadService()
	{
		$customerRepository = $this->_getCustomerRepository();

		$customerReadService = MainFactory::create('CustomerReadService',
		                                           $customerRepository);

		return $customerReadService;
	}


	/**
	 * Creates a customer service object
	 *
	 * @return CustomerService
	 */
	public function createCustomerWriteService()
	{
		$addressBookService      = $this->getAddressBookService();
		$customerRepository      = $this->_getCustomerRepository();
		$customerServiceSettings = $this->_getCustomerServiceSettings();
		$vatValidator            = MainFactory::create('VatNumberValidator');

		$customerWriteService = MainFactory::create('CustomerWriteService',
		                                            $addressBookService,
		                                            $customerRepository,
		                                            $customerServiceSettings,
		                                            $vatValidator);

		return $customerWriteService;
	}
	
	
	/**
	 * Creates a address book service object
	 * 
	 * @return AddressBookService
	 */
	public function getAddressBookService()
	{
		$addressRepository = $this->_getCustomerAddressRepository();
		$addressBookService = MainFactory::create('AddressBookService',
												  $addressRepository);
		return $addressBookService;
	}


	/**
	 * @return CustomerRegistrationInputValidatorService
	 */
	public function getCustomerRegistrationInputValidatorService()
	{
		return $this->_getCustomerInputValidatorServiceByValidatorName('CustomerRegistrationInputValidatorService');
	}
	
	/**
	 * @return CustomerAccountInputValidator
	 */
	public function getCustomerAccountInputValidator()
	{
		return $this->_getCustomerInputValidatorServiceByValidatorName('CustomerAccountInputValidator');
	}

	/**
	 * @return CI_DB_query_builder
	 */
	public function getDatabaseQueryBuilder()
	{
		return $this->ciDatabaseQueryBuilder;
	}

	/*
	 * @todo: Inject CustomerFactory
	 */
	protected function _getCustomerFactory()
	{
		$customerFactory = MainFactory::create('CustomerFactory');
		return $customerFactory;
	}
	
	/**
	 * Creates customer repository object
	 * 
	 * @return CustomerRepository
	 */
	protected function _getCustomerRepository()
	{
		$customerWriter = $this->_getCustomerWriter();
		$customerReader = $this->_getCustomerReader();
		$customerDeleter = $this->_getCustomerDeleter();
		$addressRepository = $this->_getCustomerAddressRepository();
		$customerFactory = $this->_getCustomerFactory();

		$repository = MainFactory::create('CustomerRepository',
										  $customerWriter,
										  $customerReader,
										  $customerDeleter,
										  $addressRepository,
										  $customerFactory);
		return $repository;
	}


	/**
	 * @return CustomerAddressInputValidator
	 */
	public function getCustomerAddressInputValidatorService()
	{
		return $this->_getCustomerInputValidatorServiceByValidatorName('CustomerAddressInputValidator');
	}
	

	/**
	 * Creates a customer country repository object
	 * 
	 * @return CustomerCountryRepository
	 */
	protected function _getCustomerCountryRepository()
	{
		$reader = $this->_getCustomerCountryReader();
		$repo = MainFactory::create('CustomerCountryRepository', $reader);
		return $repo;
	}


	/**
	 * Creates a customer country zone repository object
	 * 
	 * @return CustomerCountryZoneRepository
	 */
	protected function _getCustomerCountryZoneRepository()
	{
		$reader = $this->_getCustomerCountryZoneReader();
		$customerFactory = $this->_getCustomerFactory();
		$repo = MainFactory::create('CustomerCountryZoneRepository', $reader, $customerFactory);
		return $repo;
	}

	/**
	 * Creates a customer address repository object
	 * 
	 * @return CustomerAddressRepository
	 */
	protected function _getCustomerAddressRepository()
	{
		$writer = $this->_getCustomerAddressWriter();
		$reader = $this->_getCustomerAddressReader();
		$deleter = $this->_getCustomerAddressDeleter();
		$factory = $this->_getCustomerFactory();
		$repository = MainFactory::create('CustomerAddressRepository',
										  $writer,
										  $deleter,
										  $reader,
										  $factory);
		return $repository;
	}

	/**
	 * @param string $inputValidatorName
	 *
	 * @return object
	 */
	protected function _getCustomerInputValidatorServiceByValidatorName($inputValidatorName)
	{
		$customerService = $this->getCustomerService();
		$countryService = $this->getCountryService();
		$settings = MainFactory::create('CustomerInputValidatorSettings');
		$countryRepo = $this->_getCustomerCountryRepository();
		$countryZoneRepo = $this->_getCustomerCountryZoneRepository();
		$vatNumberValidator = MainFactory::create('VatNumberValidator');

		$validator = MainFactory::create($inputValidatorName,
										 $customerService,
										 $countryService,
										 $settings,
										 $countryRepo,
										 $countryZoneRepo,
										 $vatNumberValidator);
		return $validator;
	}

	/**
	 * Creates a customer address deleter object
	 * 
	 * @return CustomerAddressDeleter
	 */
	protected function _getCustomerAddressDeleter()
	{
		$deleter = MainFactory::create('CustomerAddressDeleter',
									   $this->getDatabaseQueryBuilder());
		return $deleter;
	}
	
	/**
	 * Creates a customer address reader object
	 * 
	 * @return CustomerAddressReader
	 */
	protected function _getCustomerAddressReader()
	{
		$customerFactory = $this->_getCustomerFactory();
		$countryService = $this->getCountryService();

		$reader = MainFactory::create('CustomerAddressReader',
									  $customerFactory,
									  $countryService,
									  $this->getDatabaseQueryBuilder());
		return $reader;
	}

	/**
	 * Creates a customer country zone reader object
	 * 
	 * @return CustomerCountryZoneReader
	 */
	protected function _getCustomerCountryZoneReader()
	{
		$customerFactory = $this->_getCustomerFactory();
		$reader = MainFactory::create('CustomerCountryZoneReader',
									  $customerFactory,
									  $this->getDatabaseQueryBuilder());
		return $reader;
	}

	/**
	 * Creates a customer country reader object
	 * 
	 * @return CustomerCountryReader
	 */
	protected function _getCustomerCountryReader()
	{
		$customerFactory = $this->_getCustomerFactory();
		$reader = MainFactory::create('CustomerCountryReader',
									  $customerFactory,
									  $this->getDatabaseQueryBuilder());
		return $reader;
	}

	/**
	 * Creates a customer writer object
	 * 
	 * @return CustomerWriter
	 */
	protected function _getCustomerWriter()
	{
		$customerWriter = MainFactory::create('CustomerWriter',
											  $this->getDatabaseQueryBuilder());
		return $customerWriter ;
	}

	/**
	 * Creates a customer reader object
	 * 
	 * @return CustomerReader
	 */
	protected function _getCustomerReader()
	{
		$customerFactory = $this->_getCustomerFactory();
		$customerAddressRepository = $this->_getCustomerAddressRepository();
		$dbQueryBuilder = $this->getDatabaseQueryBuilder();

		$customerReader = MainFactory::create('CustomerReader', 
											  $customerFactory,
											  $customerAddressRepository,
											  $dbQueryBuilder);
		return $customerReader;
	}
	
	/**
	 * Creates a customer deleter object
	 * 
	 * @return CustomerDeleter
	 */
	protected function _getCustomerDeleter()
	{
		$customerDeleter = MainFactory::create('CustomerDeleter',
											   $this->getDatabaseQueryBuilder());
		return $customerDeleter ;
	}
	
	/**
	 * Creates a customer service settings object
	 * 
	 * @return CustomerServiceSettings
	 */
	protected function _getCustomerServiceSettings()
	{
		$settings = MainFactory::create('CustomerServiceSettings');
		return $settings;
	}

	/**
	 * Creates a customer address writer object
	 * 
	 * @return CustomerAddressWriter
	 */
	protected function _getCustomerAddressWriter()
	{
		$writer = MainFactory::create('CustomerAddressWriter',
									  $this->getDatabaseQueryBuilder());
		return $writer;
	}
} 