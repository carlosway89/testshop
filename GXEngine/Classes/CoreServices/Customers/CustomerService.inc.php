<?php
/* --------------------------------------------------------------
   CustomerService.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerServiceInterface');

/**
 * Class CustomerService
 *
 * This class provides methods for creating and deleting customer data
 *
 * @category   System
 * @package    Customers
 * @implements CustomerServiceInterface
 */
class CustomerService implements CustomerServiceInterface
{
	/**
	 * @var CustomerReadServiceInterface
	 */
	protected $customerReadService;
	/**
	 * @var CustomerWriteServiceInterface
	 */
	protected $customerWriteService;


	/**
	 * Constructor of the class CustomerService
	 *
	 * @param CustomerReadServiceInterface  $customerReadService
	 * @param CustomerWriteServiceInterface $customerWriteService
	 */
	public function __construct(CustomerReadServiceInterface $customerReadService,
	                            CustomerWriteServiceInterface $customerWriteService)
	{
		$this->customerReadService  = $customerReadService;
		$this->customerWriteService = $customerWriteService;
	}


	/**
	 * Creates a new customer based on the entered parameters.
	 *
	 * @param CustomerEmailInterface      $email
	 * @param CustomerPasswordInterface   $password
	 * @param DateTime                    $dateOfBirth
	 * @param CustomerVatNumberInterface  $vatNumber
	 * @param CustomerCallNumberInterface $telephoneNumber
	 * @param CustomerCallNumberInterface $faxNumber
	 * @param AddressBlockInterface       $addressBlock
	 *
	 * @throws UnexpectedValueException if the entered e-mail-address is already used for an other customer account
	 * @return Customer
	 *
	 * @todo Replaced by Vat Check
	 * @todo Rename to createNewRegistree
	 */
	public function createNewCustomer(CustomerEmailInterface $email,
	                                  CustomerPasswordInterface $password,
	                                  DateTime $dateOfBirth,
	                                  CustomerVatNumberInterface $vatNumber,
	                                  CustomerCallNumberInterface $telephoneNumber,
	                                  CustomerCallNumberInterface $faxNumber,
	                                  AddressBlockInterface $addressBlock)
	{
		return $this->customerWriteService->createNewRegistree($email,
		                                                       $password,
		                                                       $dateOfBirth,
		                                                       $vatNumber,
		                                                       $telephoneNumber,
		                                                       $faxNumber,
		                                                       $addressBlock);
	}


	/**
	 * Creates a new guest account based on the entered parameters
	 *
	 * @param CustomerEmailInterface      $email
	 * @param DateTime                    $dateOfBirth
	 * @param CustomerVatNumberInterface  $vatNumber
	 * @param CustomerCallNumberInterface $telephoneNumber
	 * @param CustomerCallNumberInterface $faxNumber
	 * @param AddressBlockInterface       $addressBlock
	 *
	 * @throws UnexpectedValueException if the entered e-mail-address is already used for an other customer account
	 * @return Customer
	 */
	public function createNewGuest(CustomerEmailInterface $email,
	                               DateTime $dateOfBirth,
	                               CustomerVatNumberInterface $vatNumber,
	                               CustomerCallNumberInterface $telephoneNumber,
	                               CustomerCallNumberInterface $faxNumber,
	                               AddressBlockInterface $addressBlock)
	{
		return $this->customerWriteService->createNewGuest($email,
		                                                   $dateOfBirth,
		                                                   $vatNumber,
		                                                   $telephoneNumber,
		                                                   $faxNumber,
		                                                   $addressBlock);
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
		return $this->customerReadService->getCustomerById($customerId);
	}


	/**
	 * Deletes the customer with the provided ID.
	 *
	 * @param IdInterface $customerId
	 */
	public function deleteCustomerById(IdInterface $customerId)
	{
		return $this->customerWriteService->deleteCustomerById($customerId);
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
		return $this->customerReadService->registreeEmailExists($email);
	}


	/**
	 * Updates the data of a customer.
	 *
	 * @param CustomerInterface $customer
	 *
	 * @return CustomerInterface
	 */
	public function updateCustomer(CustomerInterface $customer)
	{
		return $this->customerWriteService->updateCustomer($customer);
	}


	/**
	 * @param CustomerAddressInterface $customerAddress
	 *
	 * @return bool
	 */
	public function addressIsDefaultCustomerAddress(CustomerAddressInterface $customerAddress)
	{
		return $this->customerReadService->addressIsDefaultCustomerAddress($customerAddress);
	}


	/**
	 * Filters customer records and returns an array with results.
	 *
	 * Example:
	 *        $service->filterCustomers('customers_id' => 1);
	 *
	 * @param array $conditions Associative array containing the desired field and value.
	 * @param int   $limit      MySQL limit applied to the records.
	 * @param int   $offset     MySQL offset applied to the records.
	 *
	 * @return array Returns an array that contains customer objects.
	 */
	public function filterCustomers(array $conditions = array(), $limit = null, $offset = null)
	{
		return $this->customerReadService->filterCustomers($conditions, $limit, $offset);
	}
}