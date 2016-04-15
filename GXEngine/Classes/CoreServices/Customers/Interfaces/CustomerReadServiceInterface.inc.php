<?php
/* --------------------------------------------------------------
   CustomerReadServiceInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerReadServiceInterface
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CustomerReadServiceInterface
{
	/**
	 * Finds a customer by an entered ID.
	 *
	 * @param IdInterface $customerId
	 *
	 * @return Customer
	 */
	public function getCustomerById(IdInterface $customerId);
	
		
	/**
	 * Method to check if the email address of the registree is already existing
	 * 
	 * @param CustomerEmailInterface $email
	 *
	 * @return bool
	 */
	public function registreeEmailExists(CustomerEmailInterface $email);



	/**
	 * Method to check if address is the default address of the customer
	 * 
	 * @param CustomerAddressInterface $customerAddress
	 *
	 * @return bool
	 */
	public function addressIsDefaultCustomerAddress(CustomerAddressInterface $customerAddress);

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
	public function filterCustomers(array $conditions, $limit, $offset);	
}