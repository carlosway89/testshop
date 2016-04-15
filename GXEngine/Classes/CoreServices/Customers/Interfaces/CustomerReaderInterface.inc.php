<?php
/* --------------------------------------------------------------
   CustomerReaderInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerReaderInterface
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CustomerReaderInterface 
{
	/**
	 * Method to find a customer with a given ID if it exists else it will return null
	 * 
	 * @param IdInterface $id
	 * @return Customer|null
	 */
	public function findById(IdInterface $id);


	/**
	 * Method to find a registree with its email address if it exists else it will return null
	 * 
	 * @param CustomerEmailInterface $email
	 *
     * @return Customer|null
	 */
	public function findRegistreeByEmail(CustomerEmailInterface $email);

	/**
	 * Method to find a guest with its email address if it exists else it will return null
	 * 
	 * @param CustomerEmailInterface $email
	 *
	 * @return Customer|null
 	 */
	public function findGuestByEmail(CustomerEmailInterface $email);

}