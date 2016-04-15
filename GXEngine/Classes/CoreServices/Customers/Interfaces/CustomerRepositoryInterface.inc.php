<?php
/* --------------------------------------------------------------
   CustomerRepositoryInterface.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerRepositoryInterface
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CustomerRepositoryInterface 
{
	/**
	 * Method to create a new customer
	 * 
	 * @return Customer
	 */
	public function getNewCustomer();


	/**
	 * Method to store customer data in the database
	 * 
	 * @param CustomerInterface $customer
	 */
	public function store(CustomerInterface $customer);


	/**
	 * Finds a registered customer based on the e-mail address if it exists else it will return null
	 * 
	 * @param CustomerEmailInterface $email
	 *
	 * @return Customer|null
	 */
	public function getRegistreeByEmail(CustomerEmailInterface $email);


	/**
	 * Method to delete a guest account with its email address
	 * 
	 * @param CustomerEmailInterface $email
	 */
	public function deleteGuestByEmail(CustomerEmailInterface $email);


	/**
	 * Gets a guest account based on its email address
	 * 
	 * @param CustomerEmailInterface $email
	 *
	 * @return Customer
	 */
	public function getGuestByEmail(CustomerEmailInterface $email);
}