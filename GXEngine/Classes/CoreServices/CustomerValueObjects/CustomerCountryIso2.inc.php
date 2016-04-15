<?php
/* --------------------------------------------------------------
   CustomerCountryIso2.php 2015-02-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryIso2Interface');

/**
 * Value Object
 * 
 * Class CustomerCountryIso2
 * 
 * Represents a customer country ISO2 code
 * 
 * @category System
 * @package Customers
 * @subpackage ValueObjects
 * @implements CustomerCountryIso2Interface
 */
class CustomerCountryIso2 implements CustomerCountryIso2Interface
{
	/**
	 * @var string
	 */
   protected $iso2;
	
	/**
	 * Constructor of the class CustomerCountryIso2
	 * 
	 * Validates the length and data type of the customer country iso2
	 * 
	 * @param $p_iso2
	 * @throws InvalidArgumentException if $p_iso2 is not a string
	 * @throws LengthException if trim($p_iso2) contains more characters than allowed. Maximal allowed characters: CHAR(2).
	 */
   public function __construct($p_iso2)
   {
      if(!is_string($p_iso2))
      {
         throw new InvalidArgumentException('$p_iso2 is not a string');
      }

      $dbFieldLengthIso2 = 2;
      $iso2 = trim($p_iso2);

      if(strlen_wrapper($iso2) > $dbFieldLengthIso2)
      {
         throw new LengthException('$iso2 is longer than ' . $dbFieldLengthIso2 . ' characters CHAR(2)');
      }
      
      $this->iso2 = $iso2;
   }


	/**
	 * @return string
	 */
	public function __toString()
   {
      return $this->iso2; 
   }
}