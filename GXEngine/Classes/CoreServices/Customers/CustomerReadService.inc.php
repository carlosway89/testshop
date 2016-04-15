<?php
/* --------------------------------------------------------------
   CustomerReadService.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerReadServiceInterface');

/**
 * Class CustomerReadService
 * 
 * This class provides methods for creating and deleting customer data
 *
 * @category System
 * @package Customers
 * @implements CustomerReadServiceInterface
 */
class CustomerReadService implements CustomerReadServiceInterface
{
	/**
	 * @var CustomerRepositoryInterface
	 */
	protected $customerRepository;
	

	/**
	 * Constructor of the class CustomerService
	 * 
	 * @param CustomerRepositoryInterface     			$customerRepository
	 */
	public function __construct(CustomerRepositoryInterface $customerRepository)
	{
		$this->customerRepository = $customerRepository;
	}

	
	/**
	 * Finds a customer by an entered ID.
	 * 
	 * @param IdInterface $customerId
	 * 
	 * @return Customer
	 */
	public function getCustomerById(IdInterface $customerId)
	{
		return $this->customerRepository->getCustomerById($customerId);
	}
	

	/**
	 * Checks if the e-mail-address entered for registration is currently in use for an other account.
	 * 
	 * @param CustomerEmailInterface $email
	 *
	 * @return bool true|false if the e-mail-address is used|is not used
	 */
	public function registreeEmailExists(CustomerEmailInterface $email)
	{
		$customer = $this->customerRepository->getRegistreeByEmail($email);
		if($customer === null)
		{
			return false;
		}
		return true;
	}

	
	/**
	 * @param CustomerAddressInterface $customerAddress
	 *
	 * @return bool
	 */
	public function addressIsDefaultCustomerAddress(CustomerAddressInterface $customerAddress)
	{
		$customer = $this->getCustomerById(MainFactory::create('Id', (string)$customerAddress->getCustomerId()));
		return $customer->getDefaultAddress()->getId() == MainFactory::create('Id', (string)$customerAddress->getCustomerId());
	}

	
	/**
	 * Filters customer records and returns an array with results.
	 * 
	 * Example:
	 * 		$service->filterCustomers('customers_id' => 1);
	 * 
	 * @param array $conditions Associative array containing the desired field and value.
	 * @param int $limit MySQL limit applied to the records.
	 * @param int $offset MySQL offset applied to the records.
	 * 
	 * @return array Returns an array that contains customer objects.
	 */
	public function filterCustomers(array $conditions = array(), $limit = null, $offset = null)
	{
		return $this->customerRepository->filterCustomers($conditions, $limit, $offset);
	}
}