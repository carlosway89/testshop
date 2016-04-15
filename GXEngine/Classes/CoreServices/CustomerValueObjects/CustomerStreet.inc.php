<?php
/* --------------------------------------------------------------
   CustomerStreet.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerStreetInterface');

/**
 * Value Object
 * 
 * Class CustomerStreet
 * 
 * Represents a customer street
 * 
 * @category System
 * @package Customers
 * @subpackage ValueObjects
 * @implements CustomerStreetInterface
 */
class CustomerStreet implements CustomerStreetInterface
{
	/**
	 * @var string
	 */
	protected $street;


	/**
	 * Constructor of the class CustomerStreet
	 * 
	 * Validates the length and the data type of the street name
	 * 
	 * @param string $p_street
	 * @throws InvalidArgumentException if $p_street is not a string
	 * @throws LengthException if trim($p_street) contains more characters than allowed. Maximal allowed characters: VARCHAR(64)
	 */
	public function __construct($p_street)
	{
		if(!is_string($p_street))
		{
			throw new InvalidArgumentException('$p_street is not a string');
		}

		$dbFieldLength = 64;
		$street = trim($p_street);

		if(strlen_wrapper($street) > $dbFieldLength)
		{
			throw new LengthException('$street is longer than ' . $dbFieldLength . ' characters VARCHAR(64)');
		}

		$this->street = $street;
	}


	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->street;
	}
} 