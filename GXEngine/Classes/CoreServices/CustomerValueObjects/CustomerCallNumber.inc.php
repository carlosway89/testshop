<?php
/* --------------------------------------------------------------
   CustomerCallNumber.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCallNumberInterface');

/**
 * Value Object
 * 
 * Class CustomerCallNumber
 * 
 * Represents a phone or fax number
 * 
 * @category System
 * @package Customers
 * @subpackage ValueObjects
 * @implements  CustomerCallNumberInterface
 */
class CustomerCallNumber implements CustomerCallNumberInterface
{
	/**
	 * @var string
	 */
	protected $callNumber;


	/**
	 * Constructor of the class CustomerCallNumber
	 * 
	 * Validates the length and data type of the customer call number
	 * 
	 * @param string $p_callNumber
	 * @throws InvalidArgumentException if $p_callNumber is not a string
	 * @throws LengthException if trim($p_callNumber) contains more characters than allowed. Maximal allowed characters: VARCHAR(32)
	 */
	public function __construct($p_callNumber)
	{
		if(!is_string($p_callNumber))
		{
			throw new InvalidArgumentException('$p_callNumber is not a string');
		}

		$dbFieldLength = 32;
		$callNumber = trim($p_callNumber);

		if(strlen_wrapper($callNumber) > $dbFieldLength)
		{
			throw new LengthException('$callNumber is longer than ' . $dbFieldLength . ' characters VARCHAR(32)');
		}
		
		$this->callNumber = $callNumber;
	}


	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->callNumber;
	}
} 