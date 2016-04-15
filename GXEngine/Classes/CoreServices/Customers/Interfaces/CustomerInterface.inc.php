<?php
/* --------------------------------------------------------------
   CustomerInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerInterface
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CustomerInterface
{
	/**
	 * Getter method for the customer ID
	 * 
	 * @return int customerId
	 */
	public function getId();


	/**
	 * Getter method for the customer gender
	 * 
	 * @return CustomerGenderInterface
	 */
	public function getGender();


	/**
	 * Getter method for the customer firstname
	 * 
	 * @return CustomerFirstnameInterface
	 */
	public function getFirstname();


	/**
	 * Getter method for the customer lastname
	 * 
	 * @return CustomerLastnameInterface
	 */
	public function getLastname();


	/**
	 * Getter method for the customers date of birth
	 * 
	 * @return DateTime date of birth
	 */
	public function getDateOfBirth();


	/**
	 * Getter method for the customer VAT number
	 * 
	 * @return CustomerVatNumberInterface
	 */
	public function getVatNumber();


	/**
	 * Getter method for the customers telephone number
	 * 
	 * @return CustomerCallNumberInterface
	 */
	public function getTelephoneNumber();


	/**
	 * Getter method for the customers fax number
	 * 
	 * @return CustomerCallNumberInterface
	 */
	public function getFaxNumber();


	/**
	 * Getter method for the customer email
	 * 
	 * @return CustomerEmailInterface
	 */
	public function getEmail();


	/**
	 * Getter method for the customer default address
	 * 
	 * @return CustomerAddressInterface
	 */
	public function getDefaultAddress();


	/**
	 * Setter method for the customer ID
	 * 
	 * @param IdInterface $id customerId
	 *
	 * @throws InvalidArgumentException
	 */
	public function setId(IdInterface $id);


	/**
	 * Setter method for the customer gender
	 * 
	 * @param CustomerGenderInterface $gender
	 */
	public function setGender(CustomerGenderInterface $gender);


	/**
	 * Setter method for the customer firstname
	 * 
	 * @param CustomerFirstnameInterface $firstname
	 */
	public function setFirstname(CustomerFirstnameInterface $firstname);


	/**
	 * Setter method for the customer lastname
	 * 
	 * @param CustomerLastnameInterface $lastname
	 */
	public function setLastname(CustomerLastnameInterface $lastname);


	/**
	 * Setter method for the customers date of birth
	 * 
	 * @param DateTime $dateOfBirth date of birth
	 */
	public function setDateOfBirth(DateTime $dateOfBirth);


	/**
	 * Setter method for the customers VAT number
	 * 
	 * @param CustomerVatNumberInterface $vatNumber
	 */
	public function setVatNumber(CustomerVatNumberInterface $vatNumber);


	/**
	 * Setter method for the customers telephone number
	 * 
	 * @param CustomerCallNumberInterface $telephoneNumber
	 */
	public function setTelephoneNumber(CustomerCallNumberInterface $telephoneNumber);


	/**
	 * Setter method for the customers fax number
	 * 
	 * @param CustomerCallNumberInterface $faxNumber
	 */
	public function setFaxNumber(CustomerCallNumberInterface $faxNumber);


	/**
	 * Setter method for the customer email
	 * 
	 * @param CustomerEmailInterface $email
	 */
	public function setEmail(CustomerEmailInterface $email);


	/**
	 * Setter method for the customer password
	 * 
	 * @param CustomerPasswordInterface $password
	 */
	public function setPassword(CustomerPasswordInterface $password);


	/**
	 * Setter method for the customer default address
	 * 
	 * @param CustomerAddressInterface $address
	 */
	public function setDefaultAddress(CustomerAddressInterface $address);


	/**
	 * Getter method for the customer password
	 * 
	 * @return CustomerPasswordInterface
	 */
	public function getPassword();


	/**
	 * Setter method to set the guest status for a customer
	 * 
	 * @param boolean $p_guest
	 */
	public function setGuest($p_guest);


	/**
	 * Checks if customers is a guest
	 * 
	 * @return bool
	 */
	public function isGuest();


	/**
	 * Getter method for the status ID
	 * 
	 * @return int customerStatusId
	 */
	public function getStatusId();


	/**
	 * Setter method for the status ID
	 * 
	 * @param int $p_statusId
	 */
	public function setStatusId($p_statusId);


	/**
	 * Getter method for the customer number
	 * 
	 * @return string customerNumber
	 */
	public function getCustomerNumber();


	/**
	 * Setter method for the customer number
	 * 
	 * @param CustomerNumberInterface $customerNumber
	 */
	public function setCustomerNumber(CustomerNumberInterface $customerNumber);


	/**
	 * Getter method for the status of the VAT number
	 * 
	 * @return int
	 */
	public function getVatNumberStatus();


	/**
	 * Setter method for the status of the VAT number
	 * 
	 * @param int $p_vatNumberStatus
	 */
	public function setVatNumberStatus($p_vatNumberStatus);
}
