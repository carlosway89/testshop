<?php
/* --------------------------------------------------------------
   CustomerCountryRepository.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryRepositoryInterface');

/**
 * Class CustomerCountryRepository
 *
 * This class provides basic methods for finding customer country data
 * 
 * @category System
 * @package Customers
 * @subpackage Country
 * @implements CustomerCountryRepositoryInterface
 */
class CustomerCountryRepository implements CustomerCountryRepositoryInterface
{
	/**
	 * @var CustomerCountryReaderInterface
	 */
	protected $customerCountryReader;

	/**
	 * Constructor of the class CustomerCountryRepository
	 * 
	 * @param CustomerCountryReader $customerCountryReader
	 */
	public function __construct(CustomerCountryReader $customerCountryReader)
	{
		$this->customerCountryReader = $customerCountryReader;
	}


	/**
	 * @param IdInterface $countryId
	 *
	 * @return CustomerCountry
	 * @throws Exception if country not found
	 */
	public function getById(IdInterface $countryId)
	{
		$country = $this->customerCountryReader->findById($countryId);
		
		if($country === null)
		{
			throw new Exception('country not found');
		}
		
		return $country;
	}
	
	/**
	 * This method will get a country if it exists else it will return null.
	 *
	 * @param IdInterface $countryId
	 * 
	 * @return CustomerCountry|null
	 */
	public function findById(IdInterface $countryId)
	{
		$country = $this->customerCountryReader->findById($countryId);
		
		return $country;
	}
}