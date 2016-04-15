<?php
/* --------------------------------------------------------------
   CustomerInputValidatorSettings.inc.php 2015-05-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerInputValidatorSettingsInterface');

/**
 * Value Object
 * 
 * Class CustomerInputValidatorSettings
 * 
 * CustomerInputValidatorSettings stores all min length values and error messages for registration form validation
 * 
 * @category System
 * @package Customers
 * @subpackage ValueObjects
 * @implements CustomerInputValidatorSettingsInterface
 */
class CustomerInputValidatorSettings implements CustomerInputValidatorSettingsInterface
{
	/**
	 * @var int
	 */
	protected $firstnameMinLength;
	/**
	 * @var int
	 */
	protected $lastnameMinLength;
	/**
	 * @var int
	 */
	protected $dateOfBirthMinLength;
	/**
	 * @var int
	 */
	protected $emailMinLength;
	/**
	 * @var int
	 */
	protected $streetMinLength;
	/**
	 * @var int
	 */
	protected $companyMinLength;
	/**
	 * @var int
	 */
	protected $postcodeMinLength;
	/**
	 * @var int
	 */
	protected $cityMinLength;
	/**
	 * @var int
	 */
	protected $countryZoneMinLength;
	/**
	 * @var int
	 */
	protected $telephoneNumberMinLength;
	/**
	 * @var int
	 */
	protected $passwordMinLength;

	/**
	 * @var string
	 */
	protected $genderErrorMessage;
	/**
	 * @var string
	 */
	protected $firstnameErrorMessage;
	/**
	 * @var string
	 */
	protected $lastnameErrorMessage;
	/**
	 * @var string
	 */
	protected $dateOfBirthErrorMessage;
	/**
	 * @var string
	 */
	protected $companyErrorMessage;
	/**
	 * @var string
	 */
	protected $vatNumberErrorMessage;
	/**
	 * @var string
	 */
	protected $emailErrorMessage;
	/**
	 * @var string
	 */
	protected $emailAddressCheckErrorMessage;
	/**
	 * @var string
	 */
	protected $emailConfirmationErrorMessage;
	/**
	 * @var string
	 */
	protected $emailExistsErrorMessage;
	/**
	 * @var string
	 */
	protected $streetErrorMessage;
	/**
	 * @var string
	 */
	protected $postcodeErrorMessage;
	/**
	 * @var string
	 */
	protected $cityErrorMessage;
	/**
	 * @var string
	 */
	protected $countryErrorMessage;
	/**
	 * @var string
	 */
	protected $countryZoneSelectionErrorMessage;
	/**
	 * @var string
	 */
	protected $countryZoneErrorMessage;
	/**
	 * @var string
	 */
	protected $telephoneNumberErrorMessage;
	/**
	 * @var string
	 */
	protected $passwordErrorMessage;
	/**
	 * @var string
	 */
	protected $passwordMismatchErrorMessage;
	/**
	 * @var string
	 */
	protected $invalidInputErrorMessage;

	/**
	 * @var bool
	 */
	protected $displayGender;
	/**
	 * @var bool
	 */
	protected $displayDateOfBirth;
	/**
	 * @var bool
	 */
	protected $displayCompany;
	/**
	 * @var bool
	 */
	protected $displayCountryZone;
	/**
	 * @var bool
	 */
	protected $displayTelephone;
	/**
	 * @var bool
	 */
	protected $displayFax;
	/**
	 * @var bool
	 */
	protected $displaySuburb;

	/**
	 * @var int
	 */
	protected $firstnameMaxLength;
	/**
	 * @var int
	 */
	protected $lastnameMaxLength;
	/**
	 * @var int
	 */
	protected $companyMaxLength;
	/**
	 * @var int
	 */
	protected $vatNumberMaxLength;
	/**
	 * @var int
	 */
	protected $streetMaxLength;
	/**
	 * @var int
	 */
	protected $postcodeMaxLength;
	/**
	 * @var int
	 */
	protected $cityMaxLength;
	/**
	 * @var int
	 */
	protected $countryZoneMaxLength;
	/**
	 * @var int
	 */
	protected $suburbMaxLength;
	/**
	 * @var int
	 */
	protected $callNumberMaxLength;
	

	/**
	 * Constructor of the class CustomerInputValidatorSettings
	 * Set min length values and error messages texts from constants
	 * Set max length values from DB field length
	 */
	public function __construct()
	{
		$this->firstnameMinLength       = (int)ENTRY_FIRST_NAME_MIN_LENGTH;
		$this->lastnameMinLength        = (int)ENTRY_LAST_NAME_MIN_LENGTH;
		$this->dateOfBirthMinLength     = (int)ENTRY_DOB_MIN_LENGTH;
		$this->emailMinLength           = (int)ENTRY_EMAIL_ADDRESS_MIN_LENGTH;
		$this->streetMinLength          = (int)ENTRY_STREET_ADDRESS_MIN_LENGTH;
		$this->companyMinLength         = (int)ENTRY_COMPANY_MIN_LENGTH;
		$this->postcodeMinLength        = (int)ENTRY_POSTCODE_MIN_LENGTH;
		$this->cityMinLength            = (int)ENTRY_CITY_MIN_LENGTH;
		$this->countryZoneMinLength     = (int)ENTRY_STATE_MIN_LENGTH;
		$this->telephoneNumberMinLength = (int)ENTRY_TELEPHONE_MIN_LENGTH;
		$this->passwordMinLength        = (int)ENTRY_PASSWORD_MIN_LENGTH;

		$this->genderErrorMessage               = ENTRY_GENDER_ERROR;
		$this->firstnameErrorMessage            = sprintf(ENTRY_FIRST_NAME_ERROR, ENTRY_FIRST_NAME_MIN_LENGTH);
		$this->lastnameErrorMessage             = sprintf(ENTRY_LAST_NAME_ERROR, ENTRY_LAST_NAME_MIN_LENGTH);
		$this->dateOfBirthErrorMessage          = ENTRY_DATE_OF_BIRTH_ERROR;
		$this->companyErrorMessage              = sprintf(ENTRY_COMPANY_ERROR, ENTRY_COMPANY_MIN_LENGTH);
		$this->vatNumberErrorMessage            = ENTRY_VAT_ERROR;
		$this->emailErrorMessage                = sprintf(ENTRY_EMAIL_ADDRESS_ERROR, ENTRY_EMAIL_ADDRESS_MIN_LENGTH);
		$this->emailAddressCheckErrorMessage    = ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
		$this->emailConfirmationErrorMessage    = ENTRY_EMAIL_ADDRESS_CONFIRM_DIFFERENT_ERROR;
		$this->emailExistsErrorMessage          = ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
		$this->streetErrorMessage               = sprintf(ENTRY_STREET_ADDRESS_ERROR, ENTRY_STREET_ADDRESS_MIN_LENGTH);
		$this->postcodeErrorMessage             = sprintf(ENTRY_POST_CODE_ERROR, ENTRY_POSTCODE_MIN_LENGTH);
		$this->cityErrorMessage                 = sprintf(ENTRY_CITY_ERROR, ENTRY_CITY_MIN_LENGTH);
		$this->countryErrorMessage              = ENTRY_COUNTRY_ERROR;
		$this->countryZoneErrorMessage          = sprintf(ENTRY_STATE_ERROR, ENTRY_STATE_MIN_LENGTH);
		$this->countryZoneSelectionErrorMessage = ENTRY_STATE_ERROR_SELECT;
		$this->telephoneNumberErrorMessage      = sprintf(ENTRY_TELEPHONE_NUMBER_ERROR, ENTRY_TELEPHONE_MIN_LENGTH);
		$this->passwordErrorMessage             = sprintf(ENTRY_PASSWORD_ERROR, ENTRY_PASSWORD_MIN_LENGTH);
		$this->passwordMismatchErrorMessage     = ENTRY_PASSWORD_ERROR_NOT_MATCHING;
		
		$this->invalidInputErrorMessage = ENTRY_MAX_LENGTH_ERROR;

		$this->displayGender      = (ACCOUNT_GENDER === 'true') ? true : false;
		$this->displayDateOfBirth = (ACCOUNT_DOB === 'true') ? true : false;
		$this->displayCompany     = (ACCOUNT_COMPANY === 'true') ? true : false;
		$this->displayCountryZone = (ACCOUNT_STATE === 'true') ? true : false;
		$this->displayTelephone   = (ACCOUNT_TELEPHONE === 'true') ? true : false;
		$this->displayFax         = (ACCOUNT_FAX === 'true') ? true : false;
		$this->displaySuburb      = (ACCOUNT_SUBURB === 'true') ? true : false;
		
		$this->firstnameMaxLength = 32;
		$this->lastnameMaxLength = 32;
		$this->companyMaxLength = 255;
		$this->vatNumberMaxLength = 20;
		$this->streetMaxLength = 64;
		$this->postcodeMaxLength = 10;
		$this->cityMaxLength = 32;
		$this->countryZoneMaxLength = 32;
		$this->suburbMaxLength = 32;
		$this->callNumberMaxLength = 32;
	}


	/**
	 * Getter method for the city error message
	 * 
	 * @return string error message
	 */
	public function getCityErrorMessage()
	{
		return $this->cityErrorMessage;
	}


	/**
	 * Getter method for city min length
	 * 
	 * @return int min length
	 */
	public function getCityMinLength()
	{
		return $this->cityMinLength;
	}


	/**
	 * Getter method for the company error message
	 * 
	 * @return string error message
	 */
	public function getCompanyErrorMessage()
	{
		return $this->companyErrorMessage;
	}


	/**
	 * Getter method for the company min length
	 * 
	 * @return int min length
	 */
	public function getCompanyMinLength()
	{
		return $this->companyMinLength;
	}


	/**
	 * Getter method for the country error message
	 * 
	 * @return mixed error message
	 */
	public function getCountryErrorMessage()
	{
		return $this->countryErrorMessage;
	}


	/**
	 * Getter method for the country zone error message
	 * 
	 * @return string error message
	 */
	public function getCountryZoneErrorMessage()
	{
		return $this->countryZoneErrorMessage;
	}


	/**
	 * Getter method for the country zone min length
	 * 
	 * @return int min length
	 */
	public function getCountryZoneMinLength()
	{
		return $this->countryZoneMinLength;
	}


	/**
	 * Getter method for the country zone selection error message
	 * 
	 * @return mixed error message
	 */
	public function getCountryZoneSelectionErrorMessage()
	{
		return $this->countryZoneSelectionErrorMessage;
	}


	/**
	 * Getter method for the date of birth error message
	 * 
	 * @return mixed error message
	 */
	public function getDateOfBirthErrorMessage()
	{
		return $this->dateOfBirthErrorMessage;
	}


	/**
	 * Getter method for the date of birth min length
	 * 
	 * @return int min length
	 */
	public function getDateOfBirthMinLength()
	{
		return $this->dateOfBirthMinLength;
	}


	/**
	 * Getter method for the e-mail-address check error message
	 * 
	 * @return mixed error message
	 */
	public function getEmailAddressCheckErrorMessage()
	{
		return $this->emailAddressCheckErrorMessage;
	}


	/**
	 * Getter method for the e-mail confirmation error message
	 * 
	 * @return mixed error message
	 */
	public function getEmailConfirmationErrorMessage()
	{
		return $this->emailConfirmationErrorMessage;
	}


	/**
	 * Getter method for e-mail error message
	 * 
	 * @return string error message
	 */
	public function getEmailErrorMessage()
	{
		return $this->emailErrorMessage;
	}


	/**
	 * Getter method for the e-mail exists error message
	 * 
	 * @return mixed error message
	 */
	public function getEmailExistsErrorMessage()
	{
		return $this->emailExistsErrorMessage;
	}


	/**
	 * Getter method for the e-mail min length
	 * 
	 * @return int min length
	 */
	public function getEmailMinLength()
	{
		return $this->emailMinLength;
	}


	/**
	 * Getter method for the first name error message
	 * 
	 * @return string error message
	 */
	public function getFirstnameErrorMessage()
	{
		return $this->firstnameErrorMessage;
	}


	/**
	 * Getter method for the first name min length
	 * 
	 * @return int min length
	 */
	public function getFirstnameMinLength()
	{
		return $this->firstnameMinLength;
	}


	/**
	 * Getter method for the gender error message
	 * 
	 * @return mixed error message
	 */
	public function getGenderErrorMessage()
	{
		return $this->genderErrorMessage;
	}


	/**
	 * Getter method for the last name error message
	 * 
	 * @return string error message
	 */
	public function getLastnameErrorMessage()
	{
		return $this->lastnameErrorMessage;
	}


	/**
	 * Getter method for the last name min length
	 * 
	 * @return int min length
	 */
	public function getLastnameMinLength()
	{
		return $this->lastnameMinLength;
	}


	/**
	 * Getter method for the password error message
	 * 
	 * @return string error message
	 */
	public function getPasswordErrorMessage()
	{
		return $this->passwordErrorMessage;
	}


	/**
	 * Getter method for the password min length
	 * 
	 * @return int min length
	 */
	public function getPasswordMinLength()
	{
		return $this->passwordMinLength;
	}


	/**
	 * Getter method for the password mismatch error message
	 * 
	 * @return mixed error message
	 */
	public function getPasswordMismatchErrorMessage()
	{
		return $this->passwordMismatchErrorMessage;
	}


	/**
	 * Getter method for the post code error message
	 * 
	 * @return string error message
	 */
	public function getPostcodeErrorMessage()
	{
		return $this->postcodeErrorMessage;
	}


	/**
	 * Getter method for the postcode min length
	 * 
	 * @return int min length
	 */
	public function getPostcodeMinLength()
	{
		return $this->postcodeMinLength;
	}


	/**
	 * Getter method for the street error message
	 * 
	 * @return string error message
	 */
	public function getStreetErrorMessage()
	{
		return $this->streetErrorMessage;
	}


	/**
	 * Getter method for the street min length
	 * 
	 * @return int min length
	 */
	public function getStreetMinLength()
	{
		return $this->streetMinLength;
	}


	/**
	 * Getter method for the telephone number error message
	 * 
	 * @return string error message
	 */
	public function getTelephoneNumberErrorMessage()
	{
		return $this->telephoneNumberErrorMessage;
	}


	/**
	 * Getter method for the telephone number min length
	 * 
	 * @return int min length
	 */
	public function getTelephoneNumberMinLength()
	{
		return $this->telephoneNumberMinLength;
	}


	/**
	 * Getter method for the VAT number error message
	 * 
	 * @return mixed error message
	 */
	public function getVatNumberErrorMessage()
	{
		return $this->vatNumberErrorMessage;
	}


	/**
	 * Getter method for the boolean value if the company should be displayed
	 * 
	 * @return bool
	 */
	public function getDisplayCompany()
	{
		return $this->displayCompany;
	}


	/**
	 * Getter method for the boolean value if the country zone should be displayed
	 * 
	 * @return bool
	 */
	public function getDisplayCountryZone()
	{
		return $this->displayCountryZone;
	}


	/**
	 * Getter method for the boolean value if the date of birth should be displayed
	 * 
	 * @return bool
	 */
	public function getDisplayDateOfBirth()
	{
		return $this->displayDateOfBirth;
	}


	/**
	 * Getter method for the boolean value if the gender should be displayed
	 * 
	 * @return bool
	 */
	public function getDisplayGender()
	{
		return $this->displayGender;
	}


	/**
	 * Getter method for the boolean value if the telephone number should be displayed
	 * 
	 * @return bool
	 */
	public function getDisplayTelephone()
	{
		return $this->displayTelephone;
	}



	/**
	 * Getter method for the boolean value if the fax number should be displayed
	 *
	 * @return bool
	 */
	public function getDisplayFax()
	{
		return $this->displayFax;
	}


	/**
	 * Getter method for the boolean value if the suburb should be displayed
	 * 
	 * @return bool
	 */
	public function getDisplaySuburb()
	{
		return $this->displaySuburb;
	}


	/**
	 * Getter method for default invalid input error message
	 *
	 * @return string error message
	 */
	public function getInvalidInputErrorMessage()
	{
		return $this->invalidInputErrorMessage;
	}


	/**
	 * Getter method for the firstname max length
	 * 
	 * @return int
	 */
	public function getFirstnameMaxLength()
	{
		return $this->firstnameMaxLength;
	}


	/**
	 * Getter method for the lastname max length
	 * 
	 * @return int
	 */
	public function getLastnameMaxLength()
	{
		return $this->lastnameMaxLength;
	}


	/**
	 * Getter method for the company max length
	 * 
	 * @return int
	 */
	public function getCompanyMaxLength()
	{
		return $this->companyMaxLength;
	}


	/**
	 * Getter method for the company max length
	 * 
	 * @return int
	 */
	public function getVatNumberMaxLength()
	{
		return $this->vatNumberMaxLength;
	}


	/**
	 * Getter method for the street max length
	 * 
	 * @return int
	 */
	public function getStreetMaxLength()
	{
		return $this->streetMaxLength;
	}


	/**
	 * Getter method for the postcode max length
	 * 
	 * @return int
	 */
	public function getPostcodeMaxLength()
	{
		return $this->postcodeMaxLength;
	}


	/**
	 * Getter method for the city max length
	 * 
	 * @return int
	 */
	public function getCityMaxLength()
	{
		return $this->cityMaxLength;
	}


	/**
	 * Getter method for the country zone max length
	 * 
	 * @return int
	 */
	public function getCountryZoneMaxLength()
	{
		$this->countryZoneMaxLength;
	}


	/**
	 * Getter method for the suburb max length
	 * 
	 * @return int
	 */
	public function getSuburbMaxLength()
	{
		return $this->suburbMaxLength;
	}


	/**
	 * Getter method for the call number max length
	 * 
	 * @return int
	 */
	public function getCallNumberMaxLength()
	{
		return $this->callNumberMaxLength;
	}
} 