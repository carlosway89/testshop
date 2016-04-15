<?php
/* --------------------------------------------------------------
   CustomerEmail.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerEmailInterface');

/**
 * Value Object
 * 
 * Class CustomerEmail
 * 
 * Represents a customer email
 * 
 * @category System
 * @package Customers
 * @subpackage ValueObjects
 * @implements CustomerEmailInterface
 */
class CustomerEmail implements CustomerEmailInterface
{
	/**
	 * @var string
	 */
	protected $email;


	/**
	 * Constructor of the class CustomerEmail
	 * 
	 * Validates the data type and format of the customer email
	 * 
	 * @param $p_email
	 * 
	 * @throws InvalidArgumentException if $p_email is not a string
	 * @throws UnexpectedValueException if $p_email is not a valid e-mail address
	 */
	public function __construct($p_email)
	{
		if(!is_string($p_email))
		{
			throw new InvalidArgumentException('$p_email is not a string');
		}

		if(!filter_var($p_email, FILTER_VALIDATE_EMAIL))
		{
			throw new UnexpectedValueException('$p_email is not a valid e-mail address');
		}
		
		$this->email = trim($p_email);
	}


	/**
	 * @return string e-mail address
	 */
	public function __toString()
	{
		return $this->email;
	}
} 