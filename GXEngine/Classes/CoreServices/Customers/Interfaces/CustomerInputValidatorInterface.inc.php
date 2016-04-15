<?php
/* --------------------------------------------------------------
   CustomerInputValidatorInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerInputValidatorInterface
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CustomerInputValidatorInterface
{
	/**
	 * Method to check if the entered customer gender is valid
	 * 
	 * @param string $p_gender
	 */
	public function validateGender($p_gender);


	/**
	 * Method to check if the entered customer firstname is valid
	 * 
	 * @param string $p_firstname
	 */
	public function validateFirstname($p_firstname);


	/**
	 * Method to check if the entered customer lastname is valid
	 * 
	 * @param string $p_lastname
	 */
	public function validateLastname($p_lastname);


	/**
	 * Method to check if the entered customer date of birth is valid
	 * Valid date of birth format is dd.mm.yyyy
	 *
	 * @param string $p_dateOfBirth
	 */
	public function validateDateOfBirth($p_dateOfBirth);


	/**
	 * Method to check if the entered customer company is valid
	 * 
	 * @param string $p_company
	 */
	public function validateCompany($p_company);


	/**
	 * Method to check if the entered email and email confirmation are valid
	 * It will check the min-length, address syntax, confirmation matching and existence of e-mail address
	 *
	 * @param string $p_email
	 * @param string $p_emailConfirmation
	 */
	public function validateEmailAndConfirmation($p_email, $p_emailConfirmation);


	/**
	 * Method to check if the entered email is valid
	 * 
	 * @param string $p_email
	 */
	public function validateEmail($p_email);


	/**
	 * Method to check if the entered email is already existing
	 * 
	 * @param string            $p_email
	 * @param CustomerInterface $customer
	 */
	public function validateEmailExists($p_email, CustomerInterface $customer = null);
	

	/**
	 * Method to check if the entered street is valid
	 * 
	 * @param string $p_street
	 */
	public function validateStreet($p_street);


	/**
	 * Method to check if the entered postcode is valid
	 * 
	 * @param string $p_postcode
	 */
	public function validatePostcode($p_postcode);


	/**
	 * Method to check if the entered city is valid
	 * 
	 * @param string $p_city
	 */
	public function validateCity($p_city);


	/**
	 * Method to check if the entered country exists
	 *
	 * @param int $p_countryId
	 */
	public function validateCountry($p_countryId);


	/**
	 * Method to check if the entered suburb is valid
	 * 
	 * @param $p_suburb
	 */
	public function validateSuburb($p_suburb);
	

	/**
	 * Method to check if the entered country zone is valid
	 * 
	 * If country has zones: check if zone belongs to country
	 * If country has no zones: check min length of zones name
	 *
	 * @param $p_countryZoneName
	 * @param $p_countryId
	 */
	public function validateCountryZone($p_countryZoneName, $p_countryId);


	/**
	 * Method to check if the entered telephone number is valid
	 * 
	 * @param string $p_telephoneNumber
	 */
	public function validateTelephoneNumber($p_telephoneNumber);


	/**
	 * Method to check if the entered password is valid
	 * 
	 * @param string $p_password
	 * @param string $p_passwordConfirmation
	 */
	public function validatePassword($p_password, $p_passwordConfirmation);


	/**
	 * Getter method for the error messages
	 * 
	 * @return array
	 */
	public function getErrorMessages();


	/**
	 * Getter method for the error message collection
	 * 
	 * @return EditableKeyValueCollection
	 */
	public function getErrorMessageCollection();
	

	/**
	 * Getter method for the error status
	 * 
	 * @return bool
	 */
	public function getErrorStatus();
} 