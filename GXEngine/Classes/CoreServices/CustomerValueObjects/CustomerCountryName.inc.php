<?php
/* --------------------------------------------------------------
   CustomerCountryName.php 2015-02-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryNameInterface');

/**
 * Value Object
 * 
 * Class CustomerCountryName
 * 
 * Represents a customer country name
 * 
 * @category System
 * @package Customers
 * @subpackage ValueObjects
 * @implements CustomerCountryNameInterface
 */
class CustomerCountryName implements CustomerCountryNameInterface
{
	/**
	 * @var string
	 */
	protected $name;


	/**
	 * Constructor of the class CustomerCountryName
	 * 
	 * Validates the length and data type of the customer country name
	 * 
	 * @param $p_name
	 * @throws InvalidArgumentException if $p_name is not a string
	 * @throws LengthException if trim($p_name) contains more characters than allowed. Maximal allowed characters: VARCHAR(64).
	 */
	public function __construct($p_name)
   {
      if(!is_string($p_name))
      {
         throw new InvalidArgumentException('$p_name is not a string');
      }

      $dbFieldLengthName = 64;
      $name = trim($p_name);

      if(strlen_wrapper($name) > $dbFieldLengthName)
      {
         throw new LengthException('$name is longer than ' . $dbFieldLengthName . ' characters VARCHAR(64)');
      }
      
      $this->name = $name;
   }
	
	/**
	 * @return string
	 */
	public function __toString()
   {
      return $this->name; 
   }
}