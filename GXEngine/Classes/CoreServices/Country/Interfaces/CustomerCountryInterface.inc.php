<?php
/* --------------------------------------------------------------
   CustomerCountryInterface.inc.php 2014-12-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerCountryInterface
 *
 * @category System
 * @package Customers
 * @subpackage Interfaces
 */
interface CustomerCountryInterface
{
	public function getId();
	public function getName();

	public function getIso2();
	public function getIso3();

	public function getAddressFormatId();
	public function getStatus();
} 