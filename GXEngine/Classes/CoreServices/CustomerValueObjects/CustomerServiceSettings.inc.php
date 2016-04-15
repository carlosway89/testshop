<?php
/* --------------------------------------------------------------
   CustomerServiceSettings.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerServiceSettingsInterface');

/**
 * Value Object
 * 
 * Class CustomerServiceSettings
 * 
 * Represents the default settings of a customer/guest
 * 
 * @category System
 * @package Customers
 * @subpackage ValueObjects
 * @implements CustomerServiceSettingsInterface
 */
class CustomerServiceSettings implements CustomerServiceSettingsInterface
{
	/**
	 * @var int
	 */
	protected $defaultCustomerStatusId;
	/**
	 * @var int
	 */
	protected $defaultGuestStatusId;


	/**
	 * Constructor of the class CustomerServiceSettings
	 * 
	 * Sets default customer status ids and and default guest status ids from constants
	 */
	public function __construct()
	{
		$this->defaultCustomerStatusId = DEFAULT_CUSTOMERS_STATUS_ID;
		$this->defaultGuestStatusId = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
	}


	/**
	 * Getter method of the default customer status ID
	 * 
	 * @return mixed
	 */
	public function getDefaultCustomerStatusId()
	{
		return $this->defaultCustomerStatusId;
	}


	/**
	 * Getter method for the default guest status ID
	 * 
	 * @return mixed
	 */
	public function getDefaultGuestStatusId()
	{
		return $this->defaultGuestStatusId;
	}

}
 