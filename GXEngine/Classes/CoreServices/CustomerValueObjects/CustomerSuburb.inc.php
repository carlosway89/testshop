<?php
/* --------------------------------------------------------------
   CustomerSuburb.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
MainFactory::load_class('CustomerSuburbInterface');

/**
 * Class CustomerSuburb
 * 
 * Represents a customer suburb
 * 
 * @category System
 * @package Customers
 * @subpackage ValueObjects
 */
class CustomerSuburb implements CustomerSuburbInterface
{
	/**
	 * @var string
	 */
	protected $suburb;


	/**
	 * Constructor of the class CustomerSuburb
	 * 
	 * Validates the length and the data type of the customer suburb
	 * 
	 * @param string $p_suburb
	 * @throws InvalidArgumentException if $p_suburb is not a string
	 * @throws LengthException if trim($p_suburb) contains more characters than allowed. Maximal allowed characters: VARCHAR(32)
	 */
	public function __construct($p_suburb)
	{
		if(!is_string($p_suburb))
		{
			throw new InvalidArgumentException('$p_suburb is not a string');
		}

		$dbFieldLength = 32;
		$suburb = trim($p_suburb);

		if(strlen_wrapper($suburb) > $dbFieldLength)
		{
			throw new LengthException('$suburb is longer than ' . $dbFieldLength . ' characters VARCHAR(32)');
		}

		$this->suburb = $suburb;
	}


	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->suburb;
	}
} 