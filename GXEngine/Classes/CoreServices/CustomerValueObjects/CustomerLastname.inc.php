<?php
/* --------------------------------------------------------------
   CustomerLastname.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerLastnameInterface');

/**
 * Value Object
 * 
 * Class CustomerLastname
 * 
 * Represents a customer lastname
 * 
 * @category System
 * @package Customers
 * @subpackage ValueObjects
 * @implements CustomerLastnameInterface
 */
class CustomerLastname implements CustomerLastnameInterface
{
	/**
	 * @var string
	 */
	protected $lastname;


	/**
	 * Constructor for the class CustomerLastname
	 * 
	 * Validates the length and the data type of the customer lastname
	 * 
	 * @param string $p_lastname
	 * @throws InvalidArgumentException if $p_lastname is not a string
	 * @throws LengthException if trim($p_lastname) contains more characters than allowed. Maximal allowed characters: VARCHAR(32)
	 */
	public function __construct($p_lastname)
	{
		if(!is_string($p_lastname))
		{
			throw new InvalidArgumentException('$p_lastname is not a string');
		}

		$dbFieldLength = 32;
		$lastname = trim($p_lastname);

		if(strlen_wrapper($lastname) > $dbFieldLength)
		{
			throw new LengthException('$lastname is longer than ' . $dbFieldLength . ' characters VARCHAR(32)');
		}

		$this->lastname = trim($p_lastname);
	}


	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->lastname;
	}
} 