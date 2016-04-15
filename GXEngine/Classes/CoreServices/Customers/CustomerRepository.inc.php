<?php
/* --------------------------------------------------------------
   CustomerRepository.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerRepositoryInterface');

/**
 * Class CustomerRepository
 * 
 * This class contains basic methods for finding, creating and deleting customer data
 *
 * @category System
 * @package Customers
 * @implements CustomerRepositoryInterface
 */
class CustomerRepository implements CustomerRepositoryInterface
{
	/**
	 * @var CustomerWriterInterface $customerWriter
	 */
	protected $customerWriter;
	/**
	 * @var CustomerReaderInterface $customerReader
	 */
	protected $customerReader;
	/**
	 * @var CustomerDeleterInterface $customerDeleter
	 */
	protected $customerDeleter;
	/**
	 * @var CustomerAddressRepositoryInterface $customerAddressRepository
	 */
	protected $customerAddressRepository;
	/**
	 * @var AbstractCustomerFactory $customerFactory
	 */
	protected $customerFactory;


	/**
	 * Constructor of the class CustomerRepository
	 * 
	 * @param CustomerWriterInterface            $customerWriter
	 * @param CustomerReaderInterface            $customerReader
	 * @param CustomerDeleterInterface           $customerDeleter
	 * @param CustomerAddressRepositoryInterface $customerAddressRepository
	 * @param AbstractCustomerFactory            $customerFactory
	 */
	public function __construct(CustomerWriterInterface $customerWriter,
								CustomerReaderInterface $customerReader,
								CustomerDeleterInterface $customerDeleter,
								CustomerAddressRepositoryInterface $customerAddressRepository,
								AbstractCustomerFactory $customerFactory)
	{
		$this->customerWriter = $customerWriter;
		$this->customerReader = $customerReader;
		$this->customerDeleter = $customerDeleter;
		$this->customerAddressRepository = $customerAddressRepository;
		$this->customerFactory = $customerFactory;
	}


	/**
	 * @return Customer
	 */
	public function getNewCustomer()
	{
		/* @var Customer $customer */
		$customer = $this->customerFactory->createCustomer();

		$emptyAddress = $this->customerFactory->createCustomerAddress();
		$this->customerAddressRepository->store($emptyAddress);

		$customer->setDefaultAddress($emptyAddress);
		$this->store($customer);

		$emptyAddress->setCustomerId(MainFactory::create('Id', $customer->getId() ));
		$this->customerAddressRepository->store($emptyAddress);

		return $customer;
	}

	/**
	 * @param CustomerInterface $customer
	 */
	public function store(CustomerInterface $customer)
	{
		$this->customerWriter->write($customer);
	}


	/**
	 * Finds customer data based on an ID.
	 * 
	 * @param IdInterface $customerId
	 *
	 * @throws InvalidArgumentException if no customer is found by the given ID
	 * @return mixed
	 */
	public function getCustomerById(IdInterface $customerId)
	{
		$customer = $this->customerReader->findById($customerId);
		if($customer == null)
		{
			throw new InvalidArgumentException('No customer found by given id');
		}
		return $customer;
	}

	/**
	 * Finds a registered customer based on the e-mail address.
	 * 
	 * @param CustomerEmailInterface $email
	 *
	 * @return Customer|null if customer is found|not found
	 */
	public function getRegistreeByEmail(CustomerEmailInterface $email)
	{
		$customer = $this->customerReader->findRegistreeByEmail($email);
		return $customer;
	}

	/**
	 * Delete the customer with the provided ID.
	 * 
	 * @param IdInterface $customerId
	 */
	public function deleteCustomerById(IdInterface $customerId)
	{
		$customer = MainFactory::create('Customer'); 
		$customer->setId($customerId);
		$this->customerDeleter->delete($customer); 
	}
	
	
	/**
	 * Deletes a guest customer based on an e-mail-address.
	 * 
	 * @param CustomerEmailInterface $email
	 */
	public function deleteGuestByEmail(CustomerEmailInterface $email)
	{
		$customer = $this->customerReader->findGuestByEmail($email);
		if($customer != null)
		{
			$this->customerAddressRepository->deleteCustomerAddressesByCustomer($customer);
			$this->customerDeleter->delete($customer);
		}
	}

	
	/**
	 * Finds a guest customer based on the e-mail address.
	 * 
	 * @param CustomerEmailInterface $email
	 *
	 * @return Customer
	 */
	public function getGuestByEmail(CustomerEmailInterface $email)
	{
		$customer = $this->customerReader->findGuestByEmail($email);
		return $customer;
	}

	
	/**
	 * Filters customer records and returns an array with results.
	 *
	 * Example:
	 * 		$repository->filterCustomers('customers_id' => 1);
	 *
	 * @param array $conditions Associative array containing the desired field and value.
	 * @param int $limit Result limit
	 * @param int $offset Result offset
	 * 
	 * @return array Returns an array that contains customer objects.
	 */
	public function filterCustomers(array $conditions = array(), $limit = null, $offset = null)
	{
		return $this->customerReader->filterCustomers($conditions, $limit, $offset);
	}
}