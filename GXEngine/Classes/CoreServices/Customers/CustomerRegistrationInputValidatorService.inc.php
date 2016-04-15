<?php
/* --------------------------------------------------------------
   CustomerRegistrationInputValidatorService.inc.php 2015-05-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerInputValidator');
MainFactory::load_class('CustomerRegistrationInputValidatorServiceInterface');

/**
 * Class CustomerRegistrationInputValidatorService
 * 
 * This class is used for validating customer input while registration
 *
 * @category System
 * @package Customers
 * @subpackage Validation
 * @extends CustomerInputValidator
 * @implements CustomerRegistrationInputValidatorServiceInterface   
 */
class CustomerRegistrationInputValidatorService extends CustomerInputValidator
	implements CustomerRegistrationInputValidatorServiceInterface
{
	/**
	 * Checks if the entered customer data is valid.
	 * 
	 * Expects an array with the following keys:
	 * gender, firstname, lastname, dob, company, email_address, email_address_confirm, postcode, city, country,
	 * state (ID or name), street_address, telephone, vat, password, confirmation
	 *
	 * @param array $inputArray
	 *
	 * @return bool true|false if customer data is valid|invalid
	 */
	public function validateCustomerDataByArray(array $inputArray)
	{
		$this->_validateDataByArray($inputArray);
		$this->validateVatNumber($inputArray['vat'], $inputArray['country'], false);
		$this->validatePassword($inputArray['password'], $inputArray['confirmation']);
		
		return !$this->getErrorStatus();
	}


	/**
	 * Checks if the entered guest data is valid.
	 * 
	 * Expects an array with the following keys:
	 * gender, firstname, lastname, dob, company, email_address, email_address_confirm, postcode, city, country,
	 * state (ID or name), telephone, vat
	 *
	 * @param array $inputArray
	 *
	 * @return bool true|false if guest data is valid|invalid
	 */
	public function validateGuestDataByArray(array $inputArray)
	{
		$this->_validateDataByArray($inputArray);
		$this->validateVatNumber($inputArray['vat'], $inputArray['country'], true);
		
		return !$this->getErrorStatus();
	}

	
	/**
	 * Checks if the entered data is valid.
	 * 
	 * Expects an array with the following keys:
	 * gender, firstname, lastname, dob, company, email_address, suburb, email_address_confirm, postcode, city, country,
	 * state (ID or name), telephone, fax, vat
	 *
	 * @param array $inputArray
	 *
	 * @return bool true|false if the entered data is valid|invalid
	 */
	protected function _validateDataByArray(array $inputArray)
	{
		$this->validateGender($inputArray['gender']);
		$this->validateFirstname($inputArray['firstname']);
		$this->validateLastname($inputArray['lastname']);
		$this->validateDateOfBirth($inputArray['dob']);
		$this->validateCompany($inputArray['company']);
		$this->validateEmailAndConfirmation($inputArray['email_address'], $inputArray['email_address_confirm']);
		$this->validateStreet($inputArray['street_address']);
		$this->validateSuburb($inputArray['suburb']);
		$this->validatePostcode($inputArray['postcode']);
		$this->validateCity($inputArray['city']);
		$this->validateCountry($inputArray['country']);
		$this->validateCountryZone($inputArray['state'], $inputArray['country']);
		$this->validateTelephoneNumber($inputArray['telephone']);
		$this->validateFaxNumber($inputArray['fax']);

		return !$this->getErrorStatus();
	}
}
