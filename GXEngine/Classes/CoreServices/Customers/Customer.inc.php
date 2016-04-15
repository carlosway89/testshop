<?php
/* --------------------------------------------------------------
   Customer.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerInterface');

/**
 * Class Customer
 * 
 * This class is used for managing customer data
 * 
 * @category System
 * @package    Customers
 * @implements CustomerInterface
 */
class Customer implements CustomerInterface
{
	/**
	 * @var int
	 */
	protected $id;
	/**
	 * @var CustomerNumberInterface
	 */
	protected $customerNumber;

	/**
	 * @var CustomerGenderInterface
	 */
	protected $gender;
	/**
	 * @var CustomerFirstnameInterface
	 */
	protected $firstname;
	/**
	 * @var CustomerLastnameInterface
	 */
	protected $lastname;
	/**
	 * @var DateTime
	 */
	protected $dateOfBirth;
	/**
	 * @var CustomerVatNumberInterface
	 */
	protected $vatNumber;
	/**
	 * @var int
	 */
	protected $vatNumberStatus = 0;
	/**
	 * @var CustomerCallNumberInterface
	 */
	protected $telephoneNumber;
	/**
	 * @var CustomerCallNumberInterface
	 */
	protected $faxNumber;

	/**
	 * @var CustomerEmailInterface
	 */
	protected $email;
	/**
	 * @var CustomerPasswordInterface
	 */
	protected $password;

	/**
	 * @var CustomerAddressInterface
	 */
	protected $defaultAddress;

	/**
	 * @var int
	 */
	protected $customerStatusId = 0;
	/**
	 * @var bool
	 */
	protected $isGuest = false;


	/**
	 * Constructor of the class Customer
	 */
	public function __construct()
	{
		$this->customerNumber = MainFactory::create('CustomerNumber', '');
		$this->gender = MainFactory::create('CustomerGender', '');
		$this->firstname = MainFactory::create('CustomerFirstname', '');
		$this->lastname = MainFactory::create('CustomerLastname', '');
		$this->dateOfBirth = MainFactory::create('CustomerDateOfBirth', '0000-01-01 00:00:00');
		$this->vatNumber = MainFactory::create('CustomerVatNumber', '');
		$this->telephoneNumber = MainFactory::create('CustomerCallNumber', '');
		$this->faxNumber = MainFactory::create('CustomerCallNumber', '');
		$this->email = MainFactory::create('CustomerEmail', 'temp@example.org');
		$this->password = MainFactory::create('CustomerPassword', md5(time().rand(1, 999000)));
	}

	/**
	 * Getter method for the customer ID.
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Getter method for the customer number
	 * 
	 * @return CustomerNumberInterface
	 */
	public function getCustomerNumber()
	{
		return $this->customerNumber;
	}

	/**
	 * Setter method for the customer number
	 * 
	 * @param CustomerNumberInterface
	 */
	public function setCustomerNumber(CustomerNumberInterface $customerNumber)
	{
		$this->customerNumber = $customerNumber;
	}


	/**
	 * Getter method for the customer status ID
	 * 
	 * @return int customerStatusId
	 */
	public function getStatusId()
	{
		return $this->customerStatusId;
	}


	/**
	 * Setter method for the status ID
	 * 
	 * @param int $p_statusId
	 *
	 * @throws InvalidArgumentException
	 */
	public function setStatusId($p_statusId)
	{	
		if(!is_numeric($p_statusId) || ((int)$p_statusId != (double)$p_statusId))
		{
			throw new InvalidArgumentException('$p_statusId int expected.'); 
		}
		
		$this->customerStatusId = (int)$p_statusId;
	}


	/**
	 * Method to validate if customer is a guest account
	 * 
	 * @return bool
	 */
	public function isGuest()
	{
		return $this->isGuest;
	}


	/**
	 * Getter method for the gender of the customer
	 * 
	 * @return CustomerGenderInterface
	 */
	public function getGender()
	{
		return $this->gender;
	}


	/**
	 * Getter method for the first name of the customer
	 * 
	 * @return CustomerFirstnameInterface
	 */
	public function getFirstname()
	{
		return $this->firstname;
	}

	
	/**
	 * Getter method for the last name of the customer
	 * 
	 * @return CustomerLastnameInterface
	 */
	public function getLastname()
	{
		return $this->lastname;
	}


	/**
	 * Getter method for the date of birth of the customer
	 * 
	 * @return DateTime date of birth
	 */
	public function getDateOfBirth()
	{
		return $this->dateOfBirth;
	}


	/**
	 * Getter method for the VAT number of the customer
	 * 
	 * @return CustomerVatNumberInterface
	 */
	public function getVatNumber()
	{
		return $this->vatNumber;
	}


	/**
	 * Getter method for the status of the VAT number
	 * 
	 * @return int
	 */
	public function getVatNumberStatus()
	{
		return $this->vatNumberStatus;
	}


	/**
	 * Getter method for the telephone number of the customer
	 * 
	 * @return CustomerCallNumberInterface
	 */
	public function getTelephoneNumber()
	{
		return $this->telephoneNumber;
	}


	/**
	 * Getter method for the fax number of the customer
	 * 
	 * @return CustomerCallNumberInterface
	 */
	public function getFaxNumber()
	{
		return $this->faxNumber;
	}


	/**
	 * Getter method for E-Mail-Address of the customer
	 * 
	 * @return CustomerEmailInterface
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Getter method for the password of the customer
	 * 
	 * @return CustomerPasswordInterface
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * Getter method for the default address of the customer
	 * 
	 * @return CustomerAddressInterface
	 */
	public function getDefaultAddress()
	{
		return $this->defaultAddress;
	}

	/**
	 * Setter method to set the guest status of the customer
	 * 
	 * @param boolean $p_isPGuest
	 * @throws InvalidArgumentException if $p_isGuest is not a boolean value
	 */
	public function setGuest($p_isPGuest)
	{
		if(!is_bool($p_isPGuest))
		{
			throw new InvalidArgumentException('$p_isGuest bool expected.');
		}
		$this->isGuest = (boolean)$p_isPGuest;
	}

	/**
	 * Setter method for the customer ID
	 * 
	 * @param IdInterface $id customerId
	 *
	 * @throws InvalidArgumentException if $p_id is not an integer or if $p_id is lower than 1
	 */
	public function setId(IdInterface $id)
	{
		$this->id = (int)(string)$id;
	}


	/**
	 * Setter method for the customer gender
	 * 
	 * @param CustomerGenderInterface $gender
	 */
	public function setGender(CustomerGenderInterface $gender)
	{
		$this->gender = $gender;
	}


	/**
	 * Setter method for the first name of the customer
	 * 
	 * @param CustomerFirstnameInterface $firstname
	 */
	public function setFirstname(CustomerFirstnameInterface $firstname)
	{
		$this->firstname = $firstname;
	}


	/**
	 * Setter method for the last name of the customer
	 * 
	 * @param CustomerLastnameInterface $lastname
	 */
	public function setLastname(CustomerLastnameInterface $lastname)
	{
		$this->lastname = $lastname;
	}


	/**
	 * Setter method for the date of birth of the customer
	 * 
	 * @param DateTime $dateOfBirth date of birth
	 */
	public function setDateOfBirth(DateTime $dateOfBirth)
	{
		$this->dateOfBirth = $dateOfBirth;
	}


	/**
	 * Setter method for the VAT number of the customer
	 * 
	 * @param CustomerVatNumberInterface $vatNumber
	 */
	public function setVatNumber(CustomerVatNumberInterface $vatNumber)
	{
		$this->vatNumber = $vatNumber;
	}


	/**
	 * Setter method for the status of the VAT number
	 * 
	 * @param int $p_vatNumberStatus
	 */
	public function setVatNumberStatus($p_vatNumberStatus)
	{
		$this->vatNumberStatus = (int)$p_vatNumberStatus;
	}


	/**
	 * Setter method for the telephone number of the customer
	 * 
	 * @param CustomerCallNumberInterface $telephoneNumber
	 */
	public function setTelephoneNumber(CustomerCallNumberInterface $telephoneNumber)
	{
		$this->telephoneNumber = $telephoneNumber;
	}


	/**
	 * Setter method for the fax number of the customer
	 * 
	 * @param CustomerCallNumberInterface $faxNumber
	 */
	public function setFaxNumber(CustomerCallNumberInterface $faxNumber)
	{
		$this->faxNumber = $faxNumber;
	}


	/**
	 * Setter method for the E-Mail-Address of the customer
	 * 
	 * @param CustomerEmailInterface $email
	 */
	public function setEmail(CustomerEmailInterface $email)
	{
		$this->email = $email;
	}


	/**
	 * Setter method for the Password of the customer
	 * 
	 * @param CustomerPasswordInterface $password
	 */
	public function setPassword(CustomerPasswordInterface $password)
	{
		$this->password = $password;
	}


	/**
	 * Setter method for the default address of the customer
	 * 
	 * @param CustomerAddressInterface $address
	 */
	public function setDefaultAddress(CustomerAddressInterface $address)
	{
		$this->defaultAddress = $address;
	}
}