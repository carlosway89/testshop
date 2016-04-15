<?php
/* --------------------------------------------------------------
   CustomerCountryZoneIsoCode.php 2015-02-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryZoneIsoCodeInterface');

/**
 * Value Object
 * 
 * Class CustomerCountryZoneIsoCode
 * 
 * Represents a customer country zone ISO code
 * 
 * @category System
 * @package Customers
 * @subpackage ValueObjects
 * @implements CustomerCountryZoneIsoCodeInterface
 */
class CustomerCountryZoneIsoCode implements CustomerCountryZoneIsoCodeInterface
{
	/**
	 * @var string
	 */
	protected $isoCode;


	/**
	 * Constructor of the class CustomerCountryZoneIsoCode
	 * 
	 * Validates the length and data type of the customer country zone iso code
	 * 
	 * @param $p_isoCode
	 * @throws InvalidArgumentException if $p_isoCode is not a string
	 * @throws LengthException if $p_isoCode contains more characters than allowed. Maximal allowed characters: CHAR(32).
	 */
	public function __construct($p_isoCode)
   {
      if(!is_string($p_isoCode))
      {
         throw new InvalidArgumentException('$p_isoCode is not a string');
      }

      $dbFieldLengthIsoCode = 32;
      $isoCode = trim($p_isoCode);

      if(strlen_wrapper($isoCode) > $dbFieldLengthIsoCode)
      {
         throw new LengthException('$isoCode is longer than ' . $dbFieldLengthIsoCode . ' characters CHAR(32)');
      }

      $this->isoCode = $isoCode;
   }


	/**
	 * @return string
	 */
	public function __toString()
   {
      return $this->isoCode; 
   }
}