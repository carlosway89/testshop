<?php
/* --------------------------------------------------------------
   CountryServiceInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CountryServiceInterface
 * 
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CountryServiceInterface 
{

	/**
	 * Method to get a country with a given id
	 * 
	 * @param IdInterface $id
	 *
	 * @return CustomerCountryInterface
	 */
	public function getCountryById(IdInterface $id);


	/**
	 * Method to get a country with a given name and country
	 * 
	 * @param $p_zoneName
	 * @param CustomerCountryInterface $customerCountry
	 *
	 * @return CustomerCountryZoneInterface
	 */
	public function getCountryZoneByNameAndCountry($p_zoneName, CustomerCountryInterface $customerCountry);


	/**
	 * Method to check if a country zone exists in a country
	 * 
	 * @param CustomerCountryZoneInterface $customerCountryZone
	 * @param CustomerCountryInterface     $customerCountry
	 *
	 * @return bool
	 */
	public function countryZoneExistsInCountry(CustomerCountryZoneInterface $customerCountryZone,
											   CustomerCountryInterface $customerCountry);


	/**
	 * Method to check if a country has country zones
	 * 
	 * @param CustomerCountryInterface $customerCountry
	 *
	 * @return bool
	 */
	public function countryHasCountryZones(CustomerCountryInterface $customerCountry);


	/**
	 * This method will return a new CustomerCountryZone object representing an unknown country zone.
	 * 
	 * @param string $p_zoneName
	 *
	 * @return CustomerCountryZone
	 */
	public function getUnknownCountryZoneByName($p_zoneName);


	/**
	 * This method will return an array of CustomerCountryZone objects found by the country ID. If the country has
	 * no zones, an empty array will be returned
	 * 
	 * @param IdInterface $countryId
	 *
	 * @return array of CustomerCountryZone objects
	 */
	public function findCountryZonesByCountryId(IdInterface $countryId);
}