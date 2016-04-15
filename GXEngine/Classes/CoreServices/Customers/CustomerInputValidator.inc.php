<?php
/* --------------------------------------------------------------
   CustomerInputValidator.inc.php 2015-05-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerInputValidatorInterface');

require_once DIR_FS_INC . 'xtc_validate_email.inc.php';

/**
 * Class CustomerInputValidator
 *
 * Validator class that checks the entered user data.
 * 
 * @category System
 * @package Customers
 * @subpackage Validation
 * @implements CustomerInputValidatorInterface
 */
class CustomerInputValidator implements CustomerInputValidatorInterface
{
	/**
	 * @var CustomerCountryRepositoryInterface
	 */
	protected $customerCountryRepository;
	/**
	 * @var CustomerCountryZoneRepositoryInterface
	 */
	protected $customerCountryZoneRepository;
	/**
	 * @var CustomerServiceInterface
	 */
	protected $customerService;
	/**
	 * @var CountryServiceInterface
	 */
	protected $countryService;
	/**
	 * @var CustomerInputValidatorSettingsInterface
	 */
	protected $settings;
	/**
	 * @var VatNumberValidatorInterface
	 */
	protected $vatNumberValidator;

	/**
	 * @var EditableKeyValueCollection
	 */
	protected $errorMessageCollection;
	/**
	 * @var bool
	 */
	protected $errorStatus = false;


	/**
	 * Constructor of the class CustomerInputValidator
	 * 
	 * @param CustomerServiceInterface                    $customerService
	 * @param CountryServiceInterface 					  $countryService
	 * @param CustomerInputValidatorSettingsInterface     $customerInputValidatorSettings
	 * @param CustomerCountryRepositoryInterface          $customerCountryRepository
	 * @param CustomerCountryZoneRepositoryInterface      $customerCountryZoneRepository
	 * @param VatNumberValidatorInterface                 $vatNumberValidator
	 */
	public function __construct(CustomerServiceInterface $customerService,
								CountryServiceInterface $countryService,
								CustomerInputValidatorSettingsInterface $customerInputValidatorSettings,
								CustomerCountryRepositoryInterface $customerCountryRepository,
								CustomerCountryZoneRepositoryInterface $customerCountryZoneRepository,
								VatNumberValidatorInterface $vatNumberValidator)
	{
		$this->customerService = $customerService;
		$this->countryService = $countryService;
		$this->settings = $customerInputValidatorSettings;
		$this->customerCountryRepository = $customerCountryRepository;
		$this->customerCountryZoneRepository = $customerCountryZoneRepository;
		$this->vatNumberValidator = $vatNumberValidator;
		
		$this->errorMessageCollection = MainFactory::create('EditableKeyValueCollection', array());
	}
	

	/**
	 * Checks if the entered customer gender is valid
	 * 
	 * @param string $p_gender
	 * @return bool true|false if $p_gender is valid|invalid
	 */
	public function validateGender($p_gender)
	{
		if(!$this->settings->getDisplayGender())
		{
			return true;
		}
		if((string)$p_gender != 'm' && (string)$p_gender != 'f')
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_gender', $this->settings->getGenderErrorMessage());
			return false;
		}
		return true;
	}
	

	/**
	 * Checks if the entered customer firstname is valid
	 * 
	 * @param string $p_firstname
	 * @return bool true|false if $p_firstname is valid|invalid
	 */
	public function validateFirstname($p_firstname)
	{
		if(strlen_wrapper(trim((string)$p_firstname)) > $this->settings->getFirstnameMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_first_name', $this->settings->getInvalidInputErrorMessage());
			return false;
		}
		
		if(strlen_wrapper((string)$p_firstname) < $this->settings->getFirstnameMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_first_name', $this->settings->getFirstnameErrorMessage());
			return false;
		}
		return true;
	}


	/**
	 * Checks if the entered customer lastname is valid
	 * 
	 * @param string $p_lastname
	 * @return bool true|false if $p_lastname is valid|invalid
	 */
	public function validateLastname($p_lastname)
	{
		if(strlen_wrapper(trim((string)$p_lastname)) > $this->settings->getLastnameMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_last_name', $this->settings->getInvalidInputErrorMessage());
			return false;
		}
		
		if(strlen_wrapper((string)$p_lastname) < $this->settings->getLastnameMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_last_name', $this->settings->getLastnameErrorMessage());
			return false;
		}
		return true;
	}


	/**
	 * Checks if the entered date of birth is in a valid format
	 * 
	 * Valid format for entering is dd.mm.yyyy
	 * 
	 * @param string $p_dateOfBirth
	 * @return bool true|false if $p_dateOfBirth is valid|invalid
	 */
	public function validateDateOfBirth($p_dateOfBirth)
	{
		// @todo DisplayDateOfBirth setting is blocking the unit tests of the class.
		if(!$this->settings->getDisplayDateOfBirth()) 
		{
			return true;
		}
		$dateOfBirth = (string)$p_dateOfBirth;
		$minLength = $this->settings->getDateOfBirthMinLength();
		
		if($minLength > 0 || ($dateOfBirth != '' && $minLength === 0))
		{
			if(!preg_match('/^[0-9]{2}[\.\/]{1}[0-9]{2}[\.\/]{1}[0-9]{4}$/', $dateOfBirth) ||
			   checkdate(substr(xtc_date_raw($dateOfBirth), 4, 2), 
						 substr(xtc_date_raw($dateOfBirth), 6, 2), 
						 substr(xtc_date_raw($dateOfBirth), 0, 4)) == false)
			{
				$this->errorStatus = true;
				$this->errorMessageCollection->setValue('error_birth_day', $this->settings->getDateOfBirthErrorMessage());
				return false;
			}
		}
		return true;
	}


	/**
	 * Checks if the entered company is in a valid format
	 * 
	 * @param string $p_company
	 * @return bool true|false if $p_company is valid|invalid
	 */
	public function validateCompany($p_company)
	{
		if(!$this->settings->getDisplayCompany())
		{
			return true;
		}
		$company = (string)$p_company;

		if(strlen_wrapper(trim($company)) > $this->settings->getCompanyMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_company', $this->settings->getInvalidInputErrorMessage());
			return false;
		}
		
		if(strlen_wrapper($company) > 0 && strlen_wrapper($company) < $this->settings->getCompanyMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_company', $this->settings->getCompanyErrorMessage());
			return false;
		}
		return true;
	}


	/**
	 * Checks if the entered parameters are in a valid format
	 * 
	 * @param string $p_vatNumber
	 * @param int $p_countryId
	 * @param bool $p_isGuest
	 * @return bool true|false if the parameters are valid|invalid
	 */
	public function validateVatNumber($p_vatNumber, $p_countryId, $p_isGuest)
	{
		if(strlen_wrapper(trim((string)$p_vatNumber)) > $this->settings->getVatNumberMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_vat', $this->settings->getInvalidInputErrorMessage());
			return false;
		}
		
		if($this->vatNumberValidator->getErrorStatus($p_vatNumber, $p_countryId, $p_isGuest))		
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_vat', $this->settings->getVatNumberErrorMessage());
			return false;
		}
		return true;
	}
	

	/**
	 * Checks if the entered e-mail address is in a valid format.
	 * It will check the min-length, the address syntax, if the entered confirmation is equal
	 * and if the e-mail address is already existing.
	 * 
	 * @param string $p_email
	 * @param string $p_emailConfirmation
	 * @return bool true|false if all conditions are true|false
	 */
	public function validateEmailAndConfirmation($p_email, $p_emailConfirmation)
	{
		if($p_email != $p_emailConfirmation)
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_mail', $this->settings->getEmailConfirmationErrorMessage());
			return false;
		}

		if(!$this->validateEmail($p_email))
		{
			return false;
		}

		if(!$this->validateEmailExists($p_email))
		{
			return false;
		}

		return true;
	}


	/**
	 * Checks if the entered e-mail address is in a valid format.
	 * It will check the min-length, the address syntax and if the e-mail address is already existing.
	 * 
	 * @param string $p_email
	 * @return bool true|false if all conditions are true|false
	 */
	public function validateEmail($p_email)
	{
		if(strlen_wrapper($p_email) < $this->settings->getEmailMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_mail', $this->settings->getEmailErrorMessage());
			return false;
		}
		elseif(xtc_validate_email($p_email) == false)
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_mail', $this->settings->getEmailAddressCheckErrorMessage());
			return false;
		}
		elseif(!filter_var($p_email, FILTER_VALIDATE_EMAIL))
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_mail', $this->settings->getEmailAddressCheckErrorMessage());
			return false;
		}

		return true;
	}
	
	
	/**
	 * Checks if $p_email is already existing.
	 * 
	 * @param string            $p_email
	 * @param CustomerInterface $customer
	 *
	 * @return bool true|false if email address is not existing|is existing
	 */
	public function validateEmailExists($p_email, CustomerInterface $customer = null)
	{
		if($this->customerService->registreeEmailExists(MainFactory::create('CustomerEmail', $p_email)) && ($customer === null || $customer->getEmail() != $p_email))
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_mail', $this->settings->getEmailExistsErrorMessage());
			return false;
		}
		return true;
	}


	/**
	 * Checks if the entered street is valid
	 * 
	 * @param string $p_street
	 * @return bool true|false if street is valid|invalid
	 */
	public function validateStreet($p_street)
	{
		if(strlen_wrapper(trim((string)$p_street)) > $this->settings->getStreetMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_street', $this->settings->getInvalidInputErrorMessage());
			return false;
		}
		
		if(strlen_wrapper((string)$p_street) < $this->settings->getStreetMinLength())
		{
			$this->errorStatus                   = true;
			$this->errorMessageCollection->setValue('error_street', $this->settings->getStreetErrorMessage());
			return false;
		}
		return true;
	}


	/**
	 * Checks if the entered postcode is valid
	 * 
	 * @param string $p_postcode
	 * @return bool true|false if $p_postcode is valid|invalid
	 */
	public function validatePostcode($p_postcode)
	{
		if(strlen_wrapper(trim((string)$p_postcode)) > $this->settings->getPostcodeMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_post_code', $this->settings->getInvalidInputErrorMessage());
			return false;
		}
		
		if(strlen_wrapper((string)$p_postcode) < $this->settings->getPostcodeMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_post_code', $this->settings->getPostcodeErrorMessage());
			return false;
		}
		return true;
	}


	/**
	 * Checks if the entered city is valid
	 * 
	 * @param string $p_city
	 * @return bool true|false if $p_city is valid 
	 */
	public function validateCity($p_city)
	{
		if(strlen_wrapper(trim((string)$p_city)) > $this->settings->getCityMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_city', $this->settings->getInvalidInputErrorMessage());
			return false;
		}
		
		if(strlen_wrapper((string)$p_city) < $this->settings->getCityMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_city', $this->settings->getCityErrorMessage());
			return false;
		}
		return true;
	}


	/**
	 * Checks if country exists and is valid
	 * 
	 * @param int $p_countryId
	 * @return bool true|false if $p_countryId is existing and valid|is not existing and invalid
	 */
	public function validateCountry($p_countryId)
	{		
		$country = $this->customerCountryRepository->findById(MainFactory::create('Id', $p_countryId));
		
		if($country === null || $country->getStatus() === false)
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_country', $this->settings->getCountryErrorMessage());
			return false;
		}
		return true;
	}


	/**
	 * Checks if the entered country zone is valid.
	 * 
	 * If country has zones: check if zone belongs to country
	 * If country has no zones: check min length of zones name
	 *
	 * @param string|int $p_countryZone
	 * @param int $p_countryId
	 * @return bool true|false if country zone is valid|invalid
	 */
	public function validateCountryZone($p_countryZone, $p_countryId)
	{
		if(!$this->settings->getDisplayCountryZone())
		{
			return true;
		}

		$country = $this->customerCountryRepository->findById(MainFactory::create('Id', $p_countryId));
		
		if($country !== null && $this->countryService->countryHasCountryZones($country))
		{
			if(is_numeric($p_countryZone))
			{
				$countryZone = $this->customerCountryZoneRepository->findById(MainFactory::create('Id', $p_countryZone));
			}
			else
			{
				if(strlen_wrapper(trim((string)$p_countryZone)) > $this->settings->getCountryZoneMaxLength())
				{
					$this->errorStatus = true;
					$this->errorMessageCollection->setValue('error_state', $this->settings->getInvalidInputErrorMessage());
					return false;
				}
				
				$countryZone = $this->customerCountryZoneRepository->findByNameAndCountry(MainFactory::create('CustomerCountryZoneName', $p_countryZone), $country);
			}
			
			if($countryZone === null 
			   || $country->getStatus() === false 
			   || !$this->countryService->countryZoneExistsInCountry($countryZone, $country))
			{
				$this->errorStatus = true;
				$this->errorMessageCollection->setValue('error_state', $this->settings->getCountryZoneSelectionErrorMessage());
				return false;
			}
		}
		elseif(is_numeric($p_countryZone) 
			   || strlen_wrapper((string)$p_countryZone) < $this->settings->getCountryZoneMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_state', $this->settings->getCountryZoneErrorMessage());
			return false;
		}
		return true;
	}


	/**
	 * @param string $p_suburb
	 * @return bool
	 */
	public function validateSuburb($p_suburb)
	{
		if(!$this->settings->getDisplaySuburb())
		{
			return true;
		}

		if(strlen_wrapper(trim((string)$p_suburb)) > $this->settings->getSuburbMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_suburb', $this->settings->getInvalidInputErrorMessage());
			return false;
		}
		
		return true;
	}


	/**
	 * Checks if the entered telephone number is valid
	 * 
	 * @param string $p_telephoneNumber
	 * @return bool true|false if $p_telephoneNumber is valid|invalid
	 */
	public function validateTelephoneNumber($p_telephoneNumber)
	{
		if(!$this->settings->getDisplayTelephone())
		{
			return true;
		}

		if(strlen_wrapper(trim((string)$p_telephoneNumber)) > $this->settings->getCallNumberMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_tel', $this->settings->getInvalidInputErrorMessage());
			return false;
		}
		
		if(strlen_wrapper((string)$p_telephoneNumber) < $this->settings->getTelephoneNumberMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_tel', $this->settings->getTelephoneNumberErrorMessage());
			return false;
		}
		return true;
	}


	/**
	 * Checks if the entered fax number is valid
	 *
	 * @param string $p_faxNumber
	 * @return bool true|false if $p_faxNumber is valid|invalid
	 */
	public function validateFaxNumber($p_faxNumber)
	{
		if(!$this->settings->getDisplayFax())
		{
			return true;
		}

		if(strlen_wrapper(trim((string)$p_faxNumber)) > $this->settings->getCallNumberMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_fax', $this->settings->getInvalidInputErrorMessage());
			return false;
		}
		return true;
	}


	/**
	 * Checks if the entered password is valid.
	 * 
	 * It will check if the password and the password confirmation are equal
	 * 
	 * @param string $p_password
	 * @param string $p_passwordConfirmation
	 * @return bool true|false if the password is valid|invalid
	 */
	public function validatePassword($p_password, $p_passwordConfirmation)
	{
		if(strlen_wrapper($p_password) < $this->settings->getPasswordMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_password', $this->settings->getPasswordErrorMessage());
			return false;
		}
		elseif($p_password !== $p_passwordConfirmation)
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_password2', $this->settings->getPasswordMismatchErrorMessage());
			return false;
		}
		return true;
	}
	

	/**
	 * TODO DEPRECATED use getErrorMessageCollection() instead
	 * 
	 * @return array
	 */
	public function getErrorMessages()
	{
		return $this->errorMessageCollection->getArray();
	}


	/**
	 * @return EditableKeyValueCollection
	 */
	public function getErrorMessageCollection()
	{
		return $this->errorMessageCollection;
	}
	
	
	/**
	 * @return bool
	 */
	public function getErrorStatus()
	{
		return $this->errorStatus;	
	}
} 