<?php
/* --------------------------------------------------------------
   CustomerDeleterInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerDeleterInterface
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CustomerDeleterInterface 
{

	/**
	 * This method will delete all data of specific customer.
	 *
	 * @param CustomerInterface $customer
	 */
	public function delete(CustomerInterface $customer);
} 