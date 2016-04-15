<?php
/* --------------------------------------------------------------
   CustomerWriterInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerWriterInterface
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CustomerWriterInterface
{
	/**
	 * Writes customer data.
	 *
	 * If customer does not exists it will perform an _insert(), if not it will perform an _update().
	 * 
	 * @param CustomerInterface $customer
	 */
	public function write(CustomerInterface $customer);
}