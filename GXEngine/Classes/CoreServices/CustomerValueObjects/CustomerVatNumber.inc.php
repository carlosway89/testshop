<?php
/* --------------------------------------------------------------
   CustomerVatNumber.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerVatNumberInterface');

/**
 * Value Object
 *
 * Class CustomerVatNumber
 *
 * Represents a tax ID number (VATIN)
 * 
 * @category System
 * @package Customers
 * @subpackage ValueObjects
 * @implements CustomerVatNumberInterface
 */
class CustomerVatNumber implements CustomerVatNumberInterface
{
	/**
	 * @var string
	 */
	protected $vatNumber;


	/**
	 * Constructor of the class CustomerVatNumber
	 * 
	 * Validates the length and the data type of the customer VAT number
	 * 
	 * @param string $p_vatNumber
	 * 
	 * @throws InvalidArgumentException if $p_vatNumber is not a string
	 * @throws LengthException if trim($p_vatNumber) contains more characters than allowed. Maximal allowed characters: VARCHAR(20)
	 */
	public function __construct($p_vatNumber)
	{
		if(!is_string($p_vatNumber))
		{
			throw new InvalidArgumentException('$p_vatNumber is not a string');
		}

		$dbFieldLength = 20;
		$vatNumber = trim($p_vatNumber);

		if(strlen_wrapper($vatNumber) > $dbFieldLength)
		{
			throw new LengthException('$vatNumber is longer than ' . $dbFieldLength . ' characters VARCHAR(20)');
		}

		$this->vatNumber = $vatNumber;
	}


	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->vatNumber;
	}
} 