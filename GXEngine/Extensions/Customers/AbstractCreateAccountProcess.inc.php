<?php

/* --------------------------------------------------------------
   AbstractCreateAccountProcess.inc.php 2015-03-13 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


abstract class AbstractCreateAccountProcess
{
	/**
	 * @var KeyValueCollection $customerCollection
	 */
	protected $customerCollection;

	/**
	 * @var CustomerService $customerWriteService
	 */
	protected $customerWriteService;

	/**
	 * @var CountryService $countryService
	 */
	protected $countryService;

	/**
	 * @var econda $econda
	 */
	protected $econda;


	/**
	 * @param CustomerWriteServiceInterface $customerWriteService
	 * @param CountryServiceInterface       $countryService
	 * @param econda                        $econda
	 */
	public function __construct(CustomerWriteServiceInterface $customerWriteService,
	                            CountryServiceInterface $countryService,
	                            econda $econda = null)
	{
		$this->customerWriteService = $customerWriteService;
		$this->countryService       = $countryService;
		$this->econda               = $econda;
	}


	/**
	 * @param KeyValueCollection $customerCollection
	 * @param GMLogoManager      $logoManager
	 *
	 * @throws InvalidCustomerDataException
	 */
	public function proceedRegistree(KeyValueCollection $customerCollection, GMLogoManager $logoManager)
	{
		$this->customerCollection = $customerCollection;

		$this->_validateRegistree();
		$this->_saveRegistree();
		$this->_login();
		$this->_proceedVoucher();
		$this->_proceedTracking();
		$this->_proceedMail($logoManager);
	}


	/**
	 * @param KeyValueCollection $customerCollection
	 *
	 * @throws InvalidCustomerDataException
	 */
	public function proceedGuest(KeyValueCollection $customerCollection)
	{
		$this->customerCollection = $customerCollection;

		$this->_validateGuest();
		$this->_saveGuest();
		$this->_login();
	}


	/**
	 * @throws InvalidCustomerDataException
	 */
	abstract protected function _validateRegistree();


	/**
	 * @throws InvalidCustomerDataException
	 */
	abstract protected function _validateGuest();


	abstract protected function _saveRegistree();


	abstract protected function _saveGuest();


	abstract protected function _login();


	abstract protected function _proceedVoucher();


	abstract protected function _proceedTracking();


	/**
	 * @param GMLogoManager $logoManager
	 */
	abstract protected function _proceedMail(GMLogoManager $logoManager);
}