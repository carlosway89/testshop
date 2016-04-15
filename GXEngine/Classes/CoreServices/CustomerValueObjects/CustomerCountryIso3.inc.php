<?php
/* --------------------------------------------------------------
   CustomerCountryIso3.php 2015-02-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryIso3Interface');

/**
 * Value Object
 * 
 * Class CustomerCountryIso3
 * 
 * Represents a customer country ISO3 code
 * 
 * @category System
 * @package Customers
 * @subpackage ValueObjects
 * @implements CustomerCountryIso3Interface
 */
class CustomerCountryIso3 implements CustomerCountryIso3Interface
{
	/**
	 * @var string
	 */
	protected $iso3;


	/**
	 * Constructor of the class CustomerCountryIso3
	 * 
	 * Validates the length and data type of the customer country iso3
	 * 
	 * @param $p_iso3
	 * @throws InvalidArgumentException if $p_iso3 is not a string.
	 * @throws LengthException if trim($p_iso3) contains more characters than allowed. Maximal allowed characters: CHAR(3).
	 */
	public function __construct($p_iso3)
   {
      if(!is_string($p_iso3))
      {
         throw new InvalidArgumentException('$p_iso3 is not a string');
      }

      $dbFieldLengthIso3 = 3;
      $iso3 = trim($p_iso3);

      if(strlen_wrapper($iso3) > $dbFieldLengthIso3)
      {
         throw new LengthException('$iso3 is longer than ' . $dbFieldLengthIso3 . ' characters CHAR(3)');
      }
      
      $this->iso3 = $iso3;
   }


	/**
	 * @return string
	 */
   public function __toString()
   {
      return $this->iso3; 
   }
}