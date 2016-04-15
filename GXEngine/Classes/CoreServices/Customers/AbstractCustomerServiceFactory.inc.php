<?php
/* --------------------------------------------------------------
   AbstractCustomerServiceFactory.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractCustomerServiceFactory
 *
 * @category System
 * @package Customers
 */
abstract class AbstractCustomerServiceFactory
{
	/**
	 * Getter method for the country service
	 * 
	 * @return CountryService
	 *
	 */
	abstract public function getCountryService();


	/**
	 * Getter method for the customer service
	 * 
	 * @return CustomerService
	 */
	abstract public function getCustomerService();


	/**
	 * Getter method for the customer registration input validator service
	 * 
	 * @return CustomerRegistrationInputValidatorService
	 */
	abstract public function getCustomerRegistrationInputValidatorService();


	/**
	 * Getter method for the customer account input validator
	 * 
	 * @return CustomerAccountInputValidator
	 * @todo rename to xyService
	 */
	abstract public function getCustomerAccountInputValidator();


	/**
	 * Getter method for the customer input validator
	 * 
	 * @return CustomerAddressInputValidator
	 * @todo rename to xyService
	 */
	abstract public function getCustomerAddressInputValidatorService();

	/**
	 * Getter method for the database query builder
	 * 
	 * @return CI_DB_query_builder
	 */
	abstract public function getDatabaseQueryBuilder();


	/**
	 * Getter method for the address book service
	 * 
	 * @return AddressBookService
	 */
	abstract public function getAddressBookService();


	/**
	 * Creates a customer read service object
	 *
	 * @return CustomerReadService
	 */
	abstract public function createCustomerReadService();


	/**
	 * Creates a customer service object
	 *
	 * @return CustomerService
	 */
	abstract public function createCustomerWriteService();
}