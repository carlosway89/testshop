<?php
/* --------------------------------------------------------------
   CustomerWriteService.inc.php 2015-06-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerWriteServiceInterface');

/**
 * Class CustomerWriteService
 *
 * This class provides methods for creating and deleting customer data
 *
 * @category System
 * @package Customers
 * @implements CustomerWriteServiceInterface
 */
class CustomerWriteService implements CustomerWriteServiceInterface
{
	/**
	 * @var AddressBookServiceInterface
	 */
	protected $addressBookService;
	/**
	 * @var CustomerRepositoryInterface
	 */
	protected $customerRepository;
	/**
	 * @var CustomerServiceSettingsInterface
	 */
	protected $customerServiceSettings;
	/**
	 * @var VatNumberValidatorInterface
	 */
	protected $vatNumberValidator;


	/**
	 * Constructor of the class CustomerService
	 *
	 * @param AddressBookServiceInterface      			$addressBookService
	 * @param CustomerRepositoryInterface     			$customerRepository
	 * @param CustomerServiceSettingsInterface 			$customerServiceSettings
	 * @param VatNumberValidatorInterface 				$vatNumberValidator
	 */
	public function __construct(AddressBookServiceInterface $addressBookService,
	                            CustomerRepositoryInterface $customerRepository,
	                            CustomerServiceSettingsInterface $customerServiceSettings,
	                            VatNumberValidatorInterface $vatNumberValidator)
	{
		$this->addressBookService = $addressBookService;
		$this->customerRepository = $customerRepository;
		$this->customerServiceSettings = $customerServiceSettings;
		$this->vatNumberValidator = $vatNumberValidator;
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
	public function createNewRegistree(CustomerEmailInterface $email,
	                                  CustomerPasswordInterface $password,
	                                  DateTime $dateOfBirth,
	                                  CustomerVatNumberInterface $vatNumber,
	                                  CustomerCallNumberInterface $telephoneNumber,
	                                  CustomerCallNumberInterface $faxNumber,
	                                  AddressBlockInterface $addressBlock)
	{
		if($this->customerRepository->getRegistreeByEmail($email) != null)
		{
			throw new UnexpectedValueException('E-Mail already used in existing customer.');
		}

		/* @var Customer $customer */
		$customer = $this->customerRepository->getNewCustomer();
		$customer->setStatusId($this->customerServiceSettings->getDefaultCustomerStatusId() ); // TODO: replaced by vat check?

		$customer->setCustomerNumber(MainFactory::create('CustomerNumber', (string)$customer->getId()));
		$customer->setGender($addressBlock->getGender());
		$customer->setFirstname($addressBlock->getFirstname());
		$customer->setLastname($addressBlock->getLastname());
		$customer->setEmail($email);
		$customer->setPassword($password);
		$customer->setDateOfBirth($dateOfBirth);
		$customer->setTelephoneNumber($telephoneNumber);
		$customer->setFaxNumber($faxNumber);

		// import addressBlock data into empty default address
		$this->addressBookService->updateAddress($addressBlock, $customer->getDefaultAddress());

		$vatNumberStatus = $this->vatNumberValidator->getVatNumberStatusCodeId($vatNumber, $addressBlock->getCountry()->getId(), false);
		$customer->setVatNumber($vatNumber);
		$customer->setVatNumberStatus($vatNumberStatus);

		$vatCustomerStatus = $this->vatNumberValidator->getCustomerStatusId($vatNumber, $addressBlock->getCountry()->getId(), false);
		$customer->setStatusId($vatCustomerStatus);

		$this->customerRepository->store($customer);

		return $customer;
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
		$this->customerRepository->deleteGuestByEmail($email);

		if($this->customerRepository->getRegistreeByEmail($email) != null)
		{
			throw new UnexpectedValueException('E-Mail already used in existing customer.');
		}

		/* @var Customer $customer */
		$customer = $this->customerRepository->getNewCustomer();
		$customer->setGuest(true);
		$customer->setStatusId($this->customerServiceSettings->getDefaultGuestStatusId() );

		$customer->setCustomerNumber(MainFactory::create('CustomerNumber', (string)$customer->getId()));
		$customer->setGender($addressBlock->getGender());
		$customer->setFirstname($addressBlock->getFirstname());
		$customer->setLastname($addressBlock->getLastname());
		$customer->setEmail($email);
		$customer->setDateOfBirth($dateOfBirth);
		$customer->setTelephoneNumber($telephoneNumber);
		$customer->setFaxNumber($faxNumber);

		// import addressBlock data into empty default address
		$this->addressBookService->updateAddress($addressBlock, $customer->getDefaultAddress());

		$vatNumberStatus = $this->vatNumberValidator->getVatNumberStatusCodeId($vatNumber,
		                                                                       $addressBlock->getCountry()->getId(),
		                                                                       true);
		$customer->setVatNumber($vatNumber);
		$customer->setVatNumberStatus($vatNumberStatus);

		$vatCustomerStatus = $this->vatNumberValidator->getCustomerStatusId($vatNumber,
		                                                                    $addressBlock->getCountry()->getId(),
		                                                                    true);
		$customer->setStatusId($vatCustomerStatus);

		$this->customerRepository->store($customer);

		return $customer;
	}


	/**
	 * Deletes the customer with the provided ID.
	 *
	 * @param IdInterface $customerId
	 */
	public function deleteCustomerById(IdInterface $customerId)
	{
		$this->customerRepository->deleteCustomerById($customerId);
	}




	/**
	 * Updates the data of a customer.
	 *
	 * @todo check if the new email address is used by another record
	 * 
	 * @param CustomerInterface $customer
	 *
	 * @return CustomerInterface
	 */
	public function updateCustomer(CustomerInterface $customer)
	{
		$vatNumberStatus = $this->vatNumberValidator->getVatNumberStatusCodeId($customer->getVatNumber(),
		                                                                       $customer->getDefaultAddress()->getCountry()->getId(),
		                                                                       false);
		$customer->setVatNumberStatus($vatNumberStatus);
		$this->customerRepository->store($customer);
		return $customer;
	}

}