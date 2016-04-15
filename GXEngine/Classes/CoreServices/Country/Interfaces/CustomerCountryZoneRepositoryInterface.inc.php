<?php
/* --------------------------------------------------------------
   CustomerCountryZoneRepositoryInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerCountryZoneRepositoryInterface
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CustomerCountryZoneRepositoryInterface
{

	/**
	 * Method to get a country zone with a given ID
	 * 
	 * @param IdInterface $countryZoneId
	 *
	 * @throws Exception if country zone not found
	 *
	 * @return CustomerCountryZoneInterface
	 */
	public function getById(IdInterface $countryZoneId);


	/**
	 * Method to get a county zone with a given name and country
	 * 
	 * @param CustomerCountryZoneNameInterface $countryZoneName
	 * @param CustomerCountryInterface         $country
	 *
	 * @throws Exception if country zone not found
	 *
	 * @return CustomerCountryZoneInterface
	 */
	public function getByNameAndCountry(CustomerCountryZoneNameInterface $countryZoneName,
										CustomerCountryInterface $country);


	/**
	 * This method will get the country zone by its name and country if it exists, if not it will return null.
	 *
	 * @param CustomerCountryZoneNameInterface $countryZoneName
	 * @param CustomerCountryInterface         $country
	 *
	 * @return CustomerCountryZone|null
	 */
	public function findByNameAndCountry(CustomerCountryZoneNameInterface $countryZoneName,
										CustomerCountryInterface $country);


	/**
	 * This method will get all country zones by a country ID if it exists, if not it will return an empty array.
	 * 
	 * @param IdInterface $countryId
	 *
	 * @return array
	 */
	public function findCountryZonesByCountryId(IdInterface $countryId);


	/**
	 * Method to get a country zone by ID if exists else return null
	 *
	 * @param IdInterface $countryZoneId
	 *
	 * @return CustomerCountryZone|null
	 */
	public function findById(IdInterface $countryZoneId);


	/**
	 * This method will return a new CustomerCountryZoneName object representing an unknown country zone.
	 * ID is 0 and ISO code is empty.
	 * 
	 * @param CustomerCountryZoneNameInterface $countryZoneName
	 *
	 * @return CustomerCountryZone
	 */
	public function getUnknownCountryZoneByName(CustomerCountryZoneNameInterface $countryZoneName);
}