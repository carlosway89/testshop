<?php
/* --------------------------------------------------------------
   CustomerAddressReader.inc.php 2015-07-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerAddressReaderInterface');

/**
 * Class CustomerAddressReader
 *
 * This class is used for reading customer address data from the database
 * 
 * @category   System
 * @package    Customers
 * @subpackage Address
 * @implements CustomerAddressReaderInterface
 */
class CustomerAddressReader implements CustomerAddressReaderInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;
	/**
	 * @var AbstractCustomerFactory
	 */
	protected $customerFactory;
	/**
	 * @var CountryServiceInterface
	 */
	protected $countryService;
	/**
	 * @var StringHelperServiceInterface
	 */
	protected $stringHelperService;


	/**
	 * Constructor for the class CustomerAddressReader
	 *
	 * CrossCuttingLoader dependencies:
	 * - StringHelperService
	 *
	 * @param AbstractCustomerFactory $customerFactory
	 * @param CountryServiceInterface  $countryService
	 * @param CI_DB_query_builder      $dbQueryBuilder
	 */
	public function __construct(AbstractCustomerFactory $customerFactory, CountryServiceInterface $countryService,
								CI_DB_query_builder $dbQueryBuilder)
	{
		$this->customerFactory = $customerFactory;
		$this->countryService  = $countryService;
		$this->db              = $dbQueryBuilder;

		$this->stringHelperService = StaticCrossCuttingLoader::getObject('StringHelperService');
	}


	/**
	 * @param IdInterface $id
	 *
	 * @return CustomerAddress
	 * @throws InvalidArgumentException
	 */
	public function getById(IdInterface $id)
	{
		$address = $this->findById($id);
		if($address === null)
		{
			throw new InvalidArgumentException('No Address found for the given ID.');
		}
		
		return $address;
	}


	/**
	 * @param IdInterface $id
	 * @return CustomerAddress
	 */
	public function findById(IdInterface $id)
	{
		$addressDataResult = $this->db->get_where('address_book', array('address_book_id' => (int)(string)$id));
		$addressDataArray = $addressDataResult->row_array();
		if($addressDataResult->num_rows() == 0)
		{
			return null;
		}
		
		return $this->_createCustomerAddressByArray($addressDataArray);
	}


	/**
	 * This method will return an array of all customer's addresses
	 *
	 * @param CustomerInterface $customer
	 *
	 * @return array containing CustomerAddress objects
	 */
	public function findAddressesByCustomer(CustomerInterface $customer)
	{
		$addressesArray = $this->db->get_where('address_book', array('customers_id' => $customer->getId()))
								   ->result_array();

		foreach($addressesArray as &$address)
		{
			$address = $this->_createCustomerAddressByArray($address);
		}

		return $addressesArray;
	}


	/**
	 * Get all system addresses. 
	 */
	public function getAllAddresses()
	{
		$addressesArray = $this->db->get('address_book')->result_array();
		
		foreach($addressesArray as &$address)
		{
			$address = $this->_createCustomerAddressByArray($address); 
		}
		
		return $addressesArray; 
	}

	
	/**
	 * Filter existing addresses by keyword. 
	 * 
	 * This method is useful when creating a search mechanism for the registered addresses.
	 * 
	 * @param string $p_keyword The keyword to be used for filtering the records.
	 *                          
	 * @return array Returns an array of CustomerAddress objects.
	 */
	public function filterAddresses($p_keyword)
	{		
		// CodeIgniter DB library will automatically escape the keyword. 
		
		$this->db->like('entry_gender', $p_keyword);
		$this->db->or_like('entry_company', $p_keyword);
		$this->db->or_like('entry_firstname', $p_keyword);
		$this->db->or_like('entry_lastname', $p_keyword);
		$this->db->or_like('entry_street_address', $p_keyword);
		$this->db->or_like('entry_suburb', $p_keyword);
		$this->db->or_like('entry_postcode', $p_keyword);
		$this->db->or_like('entry_city', $p_keyword);
		$this->db->or_like('entry_state', $p_keyword);
		
		$addressesArray = $this->db->get('address_book')->result_array(); 
		
		foreach($addressesArray as &$address)
		{
			$address = $this->_createCustomerAddressByArray($address); 
		}
		
		return $addressesArray;
	}

	/**
	 * @param array $addressDataArray
	 *
	 * @return CustomerAddress
	 */
	protected function _createCustomerAddressByArray(array $addressDataArray)
	{
		$addressDataArray = $this->stringHelperService->convertNullValuesToStringInArray($addressDataArray);

		$customerAddress = $this->customerFactory->createCustomerAddress();
		$customerAddress->setId(MainFactory::create('Id', $addressDataArray['address_book_id']));
		$customerAddress->setCustomerId(MainFactory::create('Id', $addressDataArray['customers_id']));
		$customerAddress->setGender(MainFactory::create('CustomerGender', $addressDataArray['entry_gender']));
		$customerAddress->setCompany(MainFactory::create('CustomerCompany', $addressDataArray['entry_company']));
		$customerAddress->setB2BStatus(MainFactory::create('CustomerB2BStatus', (bool)(int)$addressDataArray['customer_b2b_status']));
		$customerAddress->setFirstname(MainFactory::create('CustomerFirstname', $addressDataArray['entry_firstname']));
		$customerAddress->setLastname(MainFactory::create('CustomerLastname', $addressDataArray['entry_lastname']));
		$customerAddress->setStreet(MainFactory::create('CustomerStreet', $addressDataArray['entry_street_address']));
		$customerAddress->setSuburb(MainFactory::create('CustomerSuburb', $addressDataArray['entry_suburb']));
		$customerAddress->setPostcode(MainFactory::create('CustomerPostcode', $addressDataArray['entry_postcode']));
		$customerAddress->setCity(MainFactory::create('CustomerCity', $addressDataArray['entry_city']));

		$country = $this->countryService->getCountryById(MainFactory::create('Id',
																			 $addressDataArray['entry_country_id']));
		$customerAddress->setCountry($country);

		$state = MainFactory::create('CustomerCountryZoneName', $addressDataArray['entry_state']);

		if($this->countryService->countryHasCountryZones($country) && (string)$state !== '')
		{
			$countryZone = $this->countryService->getCountryZoneByNameAndCountry($state, $country);
		}
		else
		{
			$countryZone = $this->customerFactory->createCustomerCountryZone(MainFactory::create('Id', 0), 
																			 $state,
																			 MainFactory::create('CustomerCountryZoneIsoCode', ''));
		}

		$customerAddress->setCountryZone($countryZone);

		return $customerAddress;
	}
}