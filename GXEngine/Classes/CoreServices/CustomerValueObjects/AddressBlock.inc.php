<?php
/* --------------------------------------------------------------
   AddressBlock.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AddressBlockInterface');

/**
 * Value Object
 *
 * Class AddressBlock
 *
 * Stores all customer address data
 *
 * @category   System
 * @package    Customers
 * @subpackage ValueObjects
 *
 * @implements AddressBlockInterface
 */
class AddressBlock implements AddressBlockInterface
{
	/**
	 * @var CustomerGenderInterface
	 */
	protected $gender;
	/**
	 * @var CustomerFirstnameInterface
	 */
	protected $firstname;
	/**
	 * @var CustomerLastnameInterface
	 */
	protected $lastname;
	/**
	 * @var CustomerCompanyInterface
	 */
	protected $company;
	/**
	 * @var CustomerStreetInterface
	 */
	protected $street;
	/**
	 * @var CustomerSuburbInterface
	 */
	protected $suburb;
	/**
	 * @var CustomerPostcodeInterface
	 */
	protected $postcode;
	/**
	 * @var CustomerCityInterface
	 */
	protected $city;
	/**
	 * @var CustomerCountryInterface
	 */
	protected $country;
	/**
	 * @var CustomerCountryZoneInterface
	 */
	protected $countryZone;
	/**
	 * @var CustomerB2BStatusInterface
	 */
	protected $b2bStatus;


	/**
	 * Constructor of the class AddressBlock
	 *
	 * @param CustomerGenderInterface      $gender
	 * @param CustomerFirstnameInterface   $firstname
	 * @param CustomerLastnameInterface    $lastname
	 * @param CustomerCompanyInterface     $company
	 * @param CustomerB2BStatusInterface   $b2bStatus
	 * @param CustomerStreetInterface      $street
	 * @param CustomerSuburbInterface      $suburb
	 * @param CustomerPostcodeInterface    $postcode
	 * @param CustomerCityInterface        $city
	 * @param CustomerCountryInterface     $country
	 * @param CustomerCountryZoneInterface $countryZone
	 * @param CustomerB2BStatusInterface   $b2bStatus
	 */
	public function __construct(CustomerGenderInterface $gender,
	                            CustomerFirstnameInterface $firstname,
	                            CustomerLastnameInterface $lastname,
	                            CustomerCompanyInterface $company,
	                            CustomerB2BStatusInterface $b2bStatus,
	                            CustomerStreetInterface $street,
	                            CustomerSuburbInterface $suburb,
	                            CustomerPostcodeInterface $postcode,
	                            CustomerCityInterface $city,
	                            CustomerCountryInterface $country,
	                            CustomerCountryZoneInterface $countryZone = null)
	{
		$this->gender      = $gender;
		$this->firstname   = $firstname;
		$this->lastname    = $lastname;
		$this->company     = $company;
		$this->b2bStatus   = $b2bStatus;
		$this->street      = $street;
		$this->suburb      = $suburb;
		$this->postcode    = $postcode;
		$this->city        = $city;
		$this->country     = $country;
		$this->countryZone = $countryZone;
	}


	/**
	 * Getter method for the gender of the customer
	 *
	 * @return CustomerGenderInterface
	 */
	public function getGender()
	{
		return $this->gender;
	}


	/**
	 * Getter method for the first name of the customer
	 *
	 * @return CustomerFirstnameInterface
	 */
	public function getFirstname()
	{
		return $this->firstname;
	}


	/**
	 * Getter method for the last name of customer
	 *
	 * @return CustomerLastnameInterface
	 */
	public function getLastname()
	{
		return $this->lastname;
	}


	/**
	 * Getter method for the company of the customer
	 *
	 * @return CustomerCompanyInterface
	 */
	public function getCompany()
	{
		return $this->company;
	}


	/**
	 * Getter method for the B2B status
	 * 
	 * @return CustomerB2BStatusInterface
	 */
	public function getB2BStatus()
	{
		return $this->b2bStatus;
	}
	

	/**
	 * Getter method for the street of the customer
	 *
	 * @return CustomerStreetInterface $street
	 */
	public function getStreet()
	{
		return $this->street;
	}


	/**
	 * Getter method for the suburb of customers
	 *
	 * @return CustomerSuburbInterface
	 */
	public function getSuburb()
	{
		return $this->suburb;
	}


	/**
	 * Getter method for the postcode of customers
	 *
	 * @return CustomerPostcodeInterface
	 */
	public function getPostcode()
	{
		return $this->postcode;
	}


	/**
	 * Getter method for the city of customers
	 *
	 * @return CustomerCityInterface
	 */
	public function getCity()
	{
		return $this->city;
	}


	/**
	 * Getter method for the country address part
	 *
	 * @return CustomerCountryInterface
	 */
	public function getCountry()
	{
		return $this->country;
	}


	/**
	 * Getter method for the country zone address part
	 *
	 * @return CustomerCountryZoneInterface
	 */
	public function getCountryZone()
	{
		return $this->countryZone;
	}
} 