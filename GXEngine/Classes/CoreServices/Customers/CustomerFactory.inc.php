<?php
/* --------------------------------------------------------------
   CustomerFactory.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCustomerFactory');

/**
 * Class CustomerFactory
 * 
 * Factory class for all needed customer data.
 * 
 * @category System
 * @package Customers
 * @extends AbstractCustomerFactory
 */
class CustomerFactory extends AbstractCustomerFactory
{
	/**
	 * Creates a new customer object
	 * 
	 * @return Customer
	 */
	public function createCustomer()
	{
		$customer = MainFactory::create('Customer');
		return $customer;	
	}

	/**
	 * Creates a new customer address object
	 * 
	 * @return CustomerAddress
	 */
	public function createCustomerAddress()
	{
		$address = MainFactory::create('CustomerAddress');
		return $address;
	}

	/**
	 * Creates a new customer country object with the given parameters
	 * 
	 * @param IdInterface $id
	 * @param CustomerCountryNameInterface $name
	 * @param CustomerCountryIso2Interface $iso2
	 * @param CustomerCountryIso3Interface $iso3
	 * @param IdInterface $addressFormatId
	 * @param bool $status
	 *
	 * @return CustomerCountry
	 */
	public function createCustomerCountry(IdInterface $id, 
										  CustomerCountryNameInterface $name, 
										  CustomerCountryIso2Interface $iso2, 
										  CustomerCountryIso3Interface $iso3, 
										  IdInterface $addressFormatId, 
										  $status)
	{
		$country = MainFactory::create('CustomerCountry',
									   $id,
									   $name,
									   $iso2,
									   $iso3,
									   $addressFormatId,
									   $status);
		return $country;
	}

	/**
	 * Creates a new customer country zone object with the given parameters
	 * 
	 * @param IdInterface $id
	 * @param CustomerCountryZoneNameInterface $name
	 * @param CustomerCountryZoneIsoCodeInterface $isoCode
	 *
	 * @return CustomerCountryZone
	 */
	public function createCustomerCountryZone(IdInterface $id, 
											  CustomerCountryZoneNameInterface $name, 
											  CustomerCountryZoneIsoCodeInterface $isoCode)
	{
		$countryZone = MainFactory::create('CustomerCountryZone',
										   $id,
										   $name,
										   $isoCode);
		return $countryZone;
	}

} 