<?php
/* --------------------------------------------------------------
   CustomerWriteServiceInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerWriteServiceInterface
 *
 * @category   System
 * @package    Customers
 * @subpackage Interfaces
 */
interface CustomerWriteServiceInterface
{
	/**
	 * Method to create a new customer with the given parameters
	 *
	 * @param CustomerEmailInterface      $email
	 * @param CustomerPasswordInterface   $password
	 * @param DateTime                    $dateOfBirth
	 * @param CustomerVatNumberInterface  $vatNumber
	 * @param CustomerCallNumberInterface $telephoneNumber
	 * @param CustomerCallNumberInterface $faxNumber
	 * @param AddressBlockInterface       $addressBlock
	 *
	 * @return Customer
	 * @throws UnexpectedValueException
	 */
	public function createNewRegistree(CustomerEmailInterface $email,
	                                   CustomerPasswordInterface $password,
	                                   DateTime $dateOfBirth,
	                                   CustomerVatNumberInterface $vatNumber,
	                                   CustomerCallNumberInterface $telephoneNumber,
	                                   CustomerCallNumberInterface $faxNumber,
	                                   AddressBlockInterface $addressBlock);


	/**
	 * Method to create a new guest account with the given parameters
	 *
	 * @param CustomerEmailInterface      $email
	 * @param DateTime                    $dateOfBirth
	 * @param CustomerVatNumberInterface  $vatNumber
	 * @param CustomerCallNumberInterface $telephoneNumber
	 * @param CustomerCallNumberInterface $faxNumber
	 * @param AddressBlockInterface       $addressBlock
	 *
	 * @return Customer
	 * @throws UnexpectedValueException
	 */
	public function createNewGuest(CustomerEmailInterface $email,
	                               DateTime $dateOfBirth,
	                               CustomerVatNumberInterface $vatNumber,
	                               CustomerCallNumberInterface $telephoneNumber,
	                               CustomerCallNumberInterface $faxNumber,
	                               AddressBlockInterface $addressBlock);


	/**
	 * Method to update customer data
	 *
	 * @param CustomerInterface $customer
	 *
	 * @return CustomerInterface
	 */
	public function updateCustomer(CustomerInterface $customer);


	/**
	 * Deletes the customer with the provided ID.
	 *
	 * @param IdInterface $customerId
	 */
	public function deleteCustomerById(IdInterface $customerId);
}