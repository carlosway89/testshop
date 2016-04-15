<?php
/* --------------------------------------------------------------
   CustomerDateOfBirth.inc.php 2015-06-26 gm
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
 * Class CustomerDateOfBirth
 *
 * Represents a customer birth date
 *
 * @category   System
 * @package    Customers
 * @subpackage ValueObjects
 * @extends    DateTime
 */
class CustomerDateOfBirth extends DateTime
{
	/**
	 * @var bool
	 */
	private $isNullDate = false;


	/**
	 * Constructor of the class CustomerDateOfBirth
	 *
	 * Date of birth
	 *
	 * @param $p_dateOfBirth
	 */
	public function __construct($p_dateOfBirth = '0000-01-01 00:00:00')
	{
		$dateOfBirth = $p_dateOfBirth;
		if(strpos($dateOfBirth, '0000') === 0 || empty($dateOfBirth) || $dateOfBirth === '00.00.0000')
		{
			$dateOfBirth = '0000-01-01 00:00:00';
		}

		if($dateOfBirth == '0000-01-01 00:00:00')
		{
			$this->isNullDate = true;
		}

		parent::__construct($dateOfBirth);
	}


	/**
	 * Formats a date by a given pattern and ensures, that dates that represent empty data are formatted to
	 * '0000-00-00 00:00:00'
	 *
	 * @param string $p_format
	 *
	 * @return mixed|string
	 */
	public function format($p_format)
	{
		$formattedDate = parent::format($p_format);
		if($this->isNullDate)
		{
			$formattedDate = preg_replace('/\d/i', '0', $formattedDate);
		}

		return $formattedDate;
	}
}