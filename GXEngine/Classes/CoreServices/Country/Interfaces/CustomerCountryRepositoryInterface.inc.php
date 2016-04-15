<?php
/* --------------------------------------------------------------
   CustomerCountryRepositoryInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerCountryRepositoryInterface
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CustomerCountryRepositoryInterface 
{

	/**
	 * Method to get a customer country with a given country ID
	 * 
	 * @param IdInterface $countryId
     * 
     * @return CustomerCountryInterface
	 */
	public function getById(IdInterface $countryId);


	/**
	 * Method to find a country if exists else return null
	 * 
     * @param IdInterface $countryId
     * 
     * @return CustomerCountry|null
	 */
	public function findById(IdInterface $countryId);
}