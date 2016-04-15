<?php
/* --------------------------------------------------------------
   CustomerB2BStatus.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerB2BStatusInterface');

/**
 * Class CustomerB2BStatus
 * 
 * @category System
 * @package Customers
 * @subpackage ValueObjects
 */
class CustomerB2BStatus implements CustomerB2BStatusInterface
{
	/**
	 * @var bool
	 */
	protected $status;


	/**
	 * @param bool $p_status
	 */
	public function __construct($p_status)
	{
		if(!is_bool($p_status))
		{
			throw new InvalidArgumentException('$p_status (' . gettype($p_status) . ') is not a boolean');
		}
		
		$this->status = $p_status;
	}


	/**
	 * @return bool
	 */
	public function getStatus()
	{
		return $this->status;
	}


	/**
	 * @return string
	 */
	public function __toString()
	{
		return (string)(int)$this->status;
	}
}