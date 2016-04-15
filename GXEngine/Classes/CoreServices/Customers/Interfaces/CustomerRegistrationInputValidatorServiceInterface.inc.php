<?php
/* --------------------------------------------------------------
   CustomerRegistrationInputValidatorServiceInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerRegistrationInputValidatorServiceInterface
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CustomerRegistrationInputValidatorServiceInterface
{
	/**
	 * Method to validate the entered customer data with an array of parameters
	 * 
	 * Expects array with following keys:
	 * gender, firstname, lastname, dob, company, email_address, email_address_confirm, postcode, city, country,
	 * state (ID or name), telephone, vat, password, confirmation
	 * 
	 * @param array $inputArray
	 *
	 * @return bool
	 */
	public function validateCustomerDataByArray(array $inputArray);


	/**
	 * Method to validate the entered guest data with an array of parameters
	 * 
	 * expects array with following keys:
	 * gender, firstname, lastname, dob, company, email_address, email_address_confirm, postcode, city, country,
	 * state (ID or name), telephone, vat
	 *
	 * @param array $inputArray
	 *
	 * @return bool
	 */
	public function validateGuestDataByArray(array $inputArray);
}
