<?php
/* --------------------------------------------------------------
   CustomerAddressInterface.inc.php 2015-05-19 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerAddressInterface
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CustomerAddressInterface
{

	/**
	 * Getter method for the ID
	 * 
	 * @return int
	 */
	public function getId();

	/**
	 * Getter method for the customer ID
	 * 
	 * @return int
	 */
	public function getCustomerId();

	/**
	 * Getter method for the customer city
	 * 
	 * @return CustomerCityInterface
	 */
	public function getCity();


	/**
	 * Getter method for the customer country
	 * 
	 * @return CustomerCountryInterface
	 */
	public function getCountry();


	/**
	 * Getter method for the customer country zone
	 * 
	 * @return CustomerCountryZoneInterface
	 */
	public function getCountryZone();


	/**
	 * Getter method for the customer firstname
	 * 
	 * @return CustomerFirstnameInterface
	 */
	public function getFirstname();


	/**
	 * Getter method for the customer gender
	 * 
	 * @return CustomerGenderInterface
	 */
	public function getGender();


	/**
	 * Getter method for the customer lastname
	 * 
	 * @return CustomerLastnameInterface
	 */
	public function getLastname();


	/**
	 * Getter method for the customer postcode
	 * 
	 * @return CustomerPostcodeInterface
	 */
	public function getPostcode();


	/**
	 * Getter method for the customer street
	 * 
	 * @return CustomerStreetInterface
	 */
	public function getStreet();


	/**
	 * Getter method for the customer company
	 * 
	 * @return CustomerCompanyInterface
	 */
	public function getCompany();


	/**
	 * Getter method for the customer suburb
	 * 
	 * @return CustomerSuburbInterface
	 */
	public function getSuburb();


	/**
	 * Getter method for the address class
	 * 
	 * @return AddressClassInterface
	 */
	public function getAddressClass();


	/**
	 * Setter method for the address class
	 * 
	 * @param CustomerB2BStatusInterface $b2bStatus
	 */
	public function setB2BStatus(CustomerB2BStatusInterface $b2bStatus);


	/**
	 * Getter method of the address class
	 * 
	 * @return CustomerB2BStatusInterface
	 */
	public function getB2BStatus();
} 