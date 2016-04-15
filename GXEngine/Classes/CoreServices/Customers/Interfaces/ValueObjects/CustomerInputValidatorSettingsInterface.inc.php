<?php
/* --------------------------------------------------------------
   CustomerInputValidatorSettingsInterface.inc.php 2015-05-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Value Object
 * 
 * Interface CustomerInputValidatorSettingsInterface
 * 
 * CustomerInputValidatorSettings stores all min length values and error messages for registration form validation
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CustomerInputValidatorSettingsInterface 
{

	/**
	 * @return string
	 */
	public function getCityErrorMessage();


	/**
	 * @return int
	 */
	public function getCityMinLength();


	/**
	 * @return string
	 */
	public function getCompanyErrorMessage();


	/**
	 * @return int
	 */
	public function getCompanyMinLength();


	/**
	 * @return mixed
	 */
	public function getCountryErrorMessage();


	/**
	 * @return string
	 */
	public function getCountryZoneErrorMessage();


	/**
	 * @return int
	 */
	public function getCountryZoneMinLength();


	/**
	 * @return mixed
	 */
	public function getCountryZoneSelectionErrorMessage();


	/**
	 * @return mixed
	 */
	public function getDateOfBirthErrorMessage();


	/**
	 * @return int
	 */
	public function getDateOfBirthMinLength();


	/**
	 * @return mixed
	 */
	public function getEmailAddressCheckErrorMessage();


	/**
	 * @return mixed
	 */
	public function getEmailConfirmationErrorMessage();


	/**
	 * @return string
	 */
	public function getEmailErrorMessage();


	/**
	 * @return mixed
	 */
	public function getEmailExistsErrorMessage();


	/**
	 * @return int
	 */
	public function getEmailMinLength();


	/**
	 * @return string
	 */
	public function getFirstnameErrorMessage();


	/**
	 * @return int
	 */
	public function getFirstnameMinLength();


	/**
	 * @return mixed
	 */
	public function getGenderErrorMessage();


	/**
	 * @return string
	 */
	public function getLastnameErrorMessage();


	/**
	 * @return int
	 */
	public function getLastnameMinLength();


	/**
	 * @return string
	 */
	public function getPasswordErrorMessage();


	/**
	 * @return int
	 */
	public function getPasswordMinLength();


	/**
	 * @return mixed
	 */
	public function getPasswordMismatchErrorMessage();


	/**
	 * @return string
	 */
	public function getPostcodeErrorMessage();


	/**
	 * @return int
	 */
	public function getPostcodeMinLength();


	/**
	 * @return string
	 */
	public function getStreetErrorMessage();


	/**
	 * @return int
	 */
	public function getStreetMinLength();


	/**
	 * @return string
	 */
	public function getTelephoneNumberErrorMessage();


	/**
	 * @return int
	 */
	public function getTelephoneNumberMinLength();


	/**
	 * @return mixed
	 */
	public function getVatNumberErrorMessage();


	/**
	 * @return boolean
	 */
	public function getDisplayCompany();


	/**
	 * @return boolean
	 */
	public function getDisplayCountryZone();


	/**
	 * @return boolean
	 */
	public function getDisplayDateOfBirth();


	/**
	 * @return boolean
	 */
	public function getDisplayGender();


	/**
	 * @return boolean
	 */
	public function getDisplayTelephone();
	
	
	/**
	 * @return boolean
	 */
	public function getDisplaySuburb();


	/**
	 * @return boolean
	 */
	public function getDisplayFax();
	

	/**
	 * Getter method for default invalid input error message
	 *
	 * @return string error message
	 */
	public function getInvalidInputErrorMessage();


	/**
	 * @return int
	 */
	public function getFirstnameMaxLength();


	/**
	 * @return int
	 */
	public function getLastnameMaxLength();


	/**
	 * @return int
	 */
	public function getCompanyMaxLength();


	/**
	 * @return int
	 */
	public function getVatNumberMaxLength();


	/**
	 * @return int
	 */
	public function getStreetMaxLength();


	/**
	 * @return int
	 */
	public function getPostcodeMaxLength();


	/**
	 * @return int
	 */
	public function getCityMaxLength();


	/**
	 * @return int
	 */
	public function getCountryZoneMaxLength();


	/**
	 * @return int
	 */
	public function getSuburbMaxLength();


	/**
	 * @return int
	 */
	public function getCallNumberMaxLength();
} 