<?php
/* --------------------------------------------------------------
   VatNumberValidator.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('VatNumberValidatorInterface');

/**
 * Class VatNumberValidator
 * 
 * This class provides methods for validating VAT numbers
 * 
 * @category System
 * @package Customers
 * @subpackage Validation
 * @implements VatNumberValidatorInterface
 */
class VatNumberValidator implements VatNumberValidatorInterface
{
	/**
	 * Gets the VAT number status code ID.
	 * 
	 * @param string $p_vatNumber
	 * @param int $p_countryId
	 * @param bool $p_isGuest
	 *
	 * @return int
	 */
	public function getVatNumberStatusCodeId($p_vatNumber, $p_countryId, $p_isGuest)
	{
		//MainFactory::create('vat_validation', $p_vatNumber, '', '', $p_countryId, (int)$p_isGuest);
		$vatValidation = new vat_validation($p_vatNumber, '', '', $p_countryId, (int)$p_isGuest);
		$numberStatusCodeId = (int)$vatValidation->vat_info['vat_id_status'];
		return $numberStatusCodeId;
	}


	/**
	 * Gets the customer status ID.
	 * 
	 * @param string $p_vatNumber
	 * @param int $p_countryId
	 * @param bool $p_isGuest
	 *
	 * @return int
	 */
	public function getCustomerStatusId($p_vatNumber, $p_countryId, $p_isGuest)
	{
		$vatValidation = new vat_validation($p_vatNumber, '', '', $p_countryId, (int)$p_isGuest);
		$customerStatusId = (int)$vatValidation->vat_info['status'];
		return $customerStatusId;
	}


	/**
	 * Gets the error status.
	 * 
	 * @param string $p_vatNumber
	 * @param int $p_countryId
	 * @param bool $p_isGuest
	 *
	 * @return bool
	 */
	public function getErrorStatus($p_vatNumber, $p_countryId, $p_isGuest)
	{
		$vatValidation = new vat_validation($p_vatNumber, '', '', $p_countryId, (int)$p_isGuest);
		$errorStatus = $vatValidation->vat_info['error'];
		return $errorStatus;
	}


	/**
	 * Writes the validation results to cache.
	 * 
	 * @param string $p_vatNumber
	 * @param int $p_countryId
	 * @param bool $p_isGuest
	 * 
	 * @todo Write validation results to cache.
	 */
	protected function _putValidationCache($p_vatNumber, $p_countryId, $p_isGuest)
	{
		/*
		$coo_vat_validation = new vat_validation($vatNumber, '', '', $country->getId(), $p_guest);

		$customerStatus = $coo_vat_validation->vat_info['status'];
		$numberStatus = $coo_vat_validation->vat_info['vat_id_status'];
		$infoError = $coo_vat_validation->vat_info['error'];
		*/
	}
}
 