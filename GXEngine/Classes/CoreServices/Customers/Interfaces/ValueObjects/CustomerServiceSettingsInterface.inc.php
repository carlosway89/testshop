<?php
/* --------------------------------------------------------------
   CustomerServiceSettingsInterface.inc.php 2015-02-18 gm
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
 * Interface CustomerServiceSettings
 *
 * Represents the default settings of a customer/guest
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CustomerServiceSettingsInterface
{

	public function getDefaultCustomerStatusId();


	public function getDefaultGuestStatusId();
}
 