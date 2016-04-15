<?php
/* --------------------------------------------------------------
   VatNumberValidatorInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface VatNumberValidatorInterface
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface VatNumberValidatorInterface
{

	/**
	 * Method to get the VAT number status code ID
	 * 
	 * @param string $p_vatNumber
	 * @param int $p_countryId
	 * @param bool $p_isGuest
	 *
	 * @return int
	 */
	public function getVatNumberStatusCodeId($p_vatNumber, $p_countryId, $p_isGuest);


	/**
	 * Method to get the customer status ID
	 * 
	 * @param string $p_vatNumber
	 * @param int $p_countryId
	 * @param bool $p_isGuest
	 *
	 * @return int
	 */
	public function getCustomerStatusId($p_vatNumber, $p_countryId, $p_isGuest);


	/**
	 * Method to get the error status
	 * 
	 * @param string $p_vatNumber
	 * @param int $p_countryId
	 * @param bool $p_isGuest
	 *
	 * @return bool
	 */
	public function getErrorStatus($p_vatNumber, $p_countryId, $p_isGuest);
}
 