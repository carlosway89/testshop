<?php
/* --------------------------------------------------------------
   AddressClass.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Value Object
 * 
 * Class AddressClass
 *
 * Represents a customer address
 * 
 * @category System
 * @package Customers
 * @subpackage ValueObjects
 * @implements AddressClassInterface
 */
class AddressClass implements AddressClassInterface
{
	protected $addressClass;


	/**
	 * Constructor of the class AddressClass
	 * 
	 * Validates the data type of the address class
	 * 
	 * @param string $addressClass
	 * @throws InvalidArgumentException $p_city is not a string
	 */
	public function __construct($addressClass)
	{
		if(!is_string($addressClass))
		{
			throw new InvalidArgumentException('$addressClass is not a string');
		}

		$this->addressClass = $addressClass;
	}


	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->addressClass;
	}
} 