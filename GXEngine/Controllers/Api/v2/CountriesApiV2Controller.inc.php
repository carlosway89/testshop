<?php
/* --------------------------------------------------------------
   CountriesApiV2Controller.inc.php 2015-07-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class CountriesApiV2Controller
 *
 * Provides a gateway to the CountryService which handles the shop country resources.
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class CountriesApiV2Controller extends HttpApiV2Controller
{
	/**
	 * @var CountryServiceInterface
	 */
	protected $countryService;

	/**
	 * @var CountryJsonSerializer
	 */
	protected $countrySerializer;


	/**
	 * Initialize Controller
	 */
	public function __initialize()
	{
		$this->countryService    = StaticGXCoreLoader::getService('Country');
		$this->countrySerializer = MainFactory::create('CountryJsonSerializer');
	}


	/**
	 * @api        {get} /countries/:id Get Countries
	 * @apiVersion 2.0.0
	 * @apiName    GetCountry
	 * @apiGroup   Countries
	 *
	 * @apiDescription
	 * Get a single country or a specific country's zones. This method is currently limited to
	 * only fetching a single country resource and might be updated in a future version of the API.
	 *
	 * @apiParam {int} id (required) The country record ID to be returned.
	 *
	 * @apiExample {curl} Get Country With ID = 81
	 *             curl --user admin@shop.de:12345 http://shop.de/api.php/v2/countries/81
	 *
	 * @apiExample {curl} Get Zones of Country with ID = 81
	 *             curl --user admin@shop.de:12345 http://shop.de/api.php/v2/countries/81/zones
	 *
	 * @apiSuccess (Success 200 - OK) {string} Response-Body If successful, this method will
	 * return the country resource in JSON format.
	 *
	 * @apiError (Error 500 - Interal Error) {string} Response-Body If the record is not found or something
	 * else goes wrong the API will return a 500 error status. Read the message for more info.
	 * 
	 * @apiError (Error 501 - Not Implemented) {string} Response-Body If the ID is not included
	 * in the request URI then the API will return a 501 error status because it cannot return
	 * all the registered countries.
	 */
	public function get()
	{
		// Get all countries is not supported.
		if(!isset($this->uri[1]))
		{
			throw new HttpApiV2Exception('Cannot fetch all registered countries, operation is not implemented yet.',
			                             501);
		}

		// Country ID was not provided.
		if(!is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Provided country ID is invalid, integer expected: ' . gettype($this->uri[1]),
			                             400);
		}

		// Sub Resource Country Zones 
		if(isset($this->uri[2]) && $this->uri[2] === 'zones')
		{
			$this->_getCountryZones();

			return;
		}

		$country = $this->countryService->getCountryById(MainFactory::create('Id', (int)$this->uri[1]));
		$this->_writeResponse($this->countrySerializer->serialize($country, false));
	}


	/**
	 * POST operation is not supported by the CountryService.
	 *
	 * @throws HttpApiV2Exception
	 */
	public function post()
	{
		throw new HttpApiV2Exception('Operation is not currently allowed for the country resources.', 405);
	}


	/**
	 * PUT operation is not supported by the CountryService.
	 *
	 * @throws HttpApiV2Exception
	 */
	public function put()
	{
		throw new HttpApiV2Exception('Operation is not currently allowed for the country resources.', 405);
	}


	/**
	 * DELETE operation is not supported by the CountryService.
	 *
	 * @throws HttpApiV2Exception
	 */
	public function delete()
	{
		throw new HttpApiV2Exception('Operation is not currently allowed for the country resources.', 405);
	}


	/**
	 * Sub Resource for the country zones.
	 *
	 * The API consumer can easily get the zones that are related with a specific country.
	 */
	protected function _getCountryZones()
	{
		$zoneSerializer = MainFactory::create('ZoneJsonSerializer');

		$zones = $this->countryService->findCountryZonesByCountryId(MainFactory::create('Id', (int)$this->uri[1]));

		$response = array();
		foreach($zones as $zone)
		{
			$response[] = $zoneSerializer->serialize($zone, false);
		}

		$this->_writeResponse($response);
	}
}