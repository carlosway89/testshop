<?php
/* --------------------------------------------------------------
   CountryService.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CountryServiceInterface');

/**
 * Class CountryService
 * 
 * This class is used for finding country data
 * 
 * @category System
 * @package Customers
 * @subpackage Country
 * @implements CountryServiceInterface
 */
class CountryService implements CountryServiceInterface
{
	/**
	 * @var CustomerCountryRepositoryInterface
	 */
	protected $customerCountryRepo;
	/**
	 * @var CustomerCountryZoneRepositoryInterface
	 */
	protected $customerCountryZoneRepo;

	/**
	 * Constructor of the class CountryService
	 * 
	 * @param CustomerCountryRepositoryInterface     $customerCountryRepo
	 * @param CustomerCountryZoneRepositoryInterface $customerCountryZoneRepo
	 */
	public function __construct(CustomerCountryRepositoryInterface $customerCountryRepo,
								CustomerCountryZoneRepositoryInterface $customerCountryZoneRepo)
	{
		$this->customerCountryRepo = $customerCountryRepo;
		$this->customerCountryZoneRepo = $customerCountryZoneRepo;
	}


	/**
	 * Getter method for the country ID
	 * 
	 * @param IdInterface $id
	 *
	 * @return CustomerCountryInterface
	 */
	public function getCountryById(IdInterface $id)
	{
		return $this->customerCountryRepo->getById($id);
	}
	
	
	/**
	 * Getter method for the country zone
	 * 
	 * @param $p_zoneName
	 * @param CustomerCountryInterface $customerCountry
	 *
	 * @return CustomerCountryZoneInterface
	 */
	public function getCountryZoneByNameAndCountry($p_zoneName, CustomerCountryInterface $customerCountry)
	{
		if(is_a($p_zoneName, 'CustomerCountryZoneNameInterface'))
		{
			$zoneName = $p_zoneName;
		}
		else
		{
			$zoneName = MainFactory::create('CustomerCountryZoneName', $p_zoneName);
		}
		return $this->customerCountryZoneRepo->getByNameAndCountry($zoneName, $customerCountry);
	}

	/**
	 * @param IdInterface $id
	 *
	 * @return CustomerCountryZoneInterface
	 */
	public function getCountryZoneById(IdInterface $id)
	{
		return $this->customerCountryZoneRepo->getById($id);
	}


	/**
	 * This method will return a new CustomerCountryZone object representing an unknown country zone.
	 * 
	 * @param string $p_zoneName
	 *
	 * @return CustomerCountryZone
	 */
	public function getUnknownCountryZoneByName($p_zoneName)
	{
		$countryZoneName = MainFactory::create('CustomerCountryZoneName', $p_zoneName);
		
		return $this->customerCountryZoneRepo->getUnknownCountryZoneByName($countryZoneName);
	}


	/**
	 * This method will return an array of CustomerCountryZone objects found by the country ID. If the country has
	 * no zones, an empty array will be returned
	 * 
	 * @param IdInterface $countryId
	 *
	 * @return array of CustomerCountryZone objects
	 */
	public function findCountryZonesByCountryId(IdInterface $countryId)
	{
		$countryZones = $this->customerCountryZoneRepo->findCountryZonesByCountryId($countryId);
		
		return $countryZones;
	}


	/**
	 * Checks if there is a country zone in a country
	 * 
	 * @param CustomerCountryZoneInterface $customerCountryZone
	 * @param CustomerCountryInterface     $customerCountry
	 *
	 * @return bool true|false if country zone exists|not exists
	 */
	public function countryZoneExistsInCountry(CustomerCountryZoneInterface $customerCountryZone,
											   CustomerCountryInterface $customerCountry)
	{
		$countryZones = $this->customerCountryZoneRepo->findCountryZonesByCountryId(MainFactory::create('Id', $customerCountry->getId()));
		$countryZoneExistsInCountry = in_array($customerCountryZone, $countryZones);

		return $countryZoneExistsInCountry;
	}


	/**
	 * Checks if country has country zones
	 * 
	 * @param CustomerCountryInterface $customerCountry
	 *
	 * @return bool true|false if there are|are not country zones
	 */
	public function countryHasCountryZones(CustomerCountryInterface $customerCountry)
	{
		$countryZones = $this->customerCountryZoneRepo->findCountryZonesByCountryId(MainFactory::create('Id', $customerCountry->getId()));
		$countryHasCountryZones = !empty($countryZones);

		return $countryHasCountryZones;
	}
}
 