<?php
/* --------------------------------------------------------------
   AbstractCustomerFactory.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AbstractCustomerFactory
 *
 * @category   System
 * @package    Customers
 * @subpackage Interfaces
 */
abstract class AbstractCustomerFactory
{
	/**
	 * Method to create a new customer object
	 *
	 * @return Customer
	 */
	abstract public function createCustomer();


	/**
	 * Method to create a new customer address object
	 *
	 * @return CustomerAddress
	 */
	abstract public function createCustomerAddress();


	/**
	 * Method to create a new customer country object with the given parameters
	 *
	 * @param IdInterface                  $id
	 * @param CustomerCountryNameInterface $name
	 * @param CustomerCountryIso2Interface $iso2
	 * @param CustomerCountryIso3Interface $iso3
	 * @param IdInterface                  $addressFormatId
	 * @param bool                         $status
	 *
	 * @return CustomerCountry
	 */
	abstract public function createCustomerCountry(IdInterface $id,
	                                               CustomerCountryNameInterface $name,
	                                               CustomerCountryIso2Interface $iso2,
	                                               CustomerCountryIso3Interface $iso3,
	                                               IdInterface $addressFormatId,
	                                               $status);


	/**
	 * Method to create a new customer country zone object with the given parameters
	 *
	 * @param IdInterface                         $id
	 * @param CustomerCountryZoneNameInterface    $name
	 * @param CustomerCountryZoneIsoCodeInterface $isoCode
	 *
	 * @return CustomerCountryZone
	 */
	abstract public function createCustomerCountryZone(IdInterface $id,
	                                                   CustomerCountryZoneNameInterface $name,
	                                                   CustomerCountryZoneIsoCodeInterface $isoCode);
}