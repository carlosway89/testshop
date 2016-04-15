<?php
/* --------------------------------------------------------------
   ZonesApiV2Controller.inc.php 2015-07-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class ZonesApiV2Controller
 *
 * Provides a gateway to the CountryService which handles the shop zone resources.
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class ZonesApiV2Controller extends HttpApiV2Controller
{
	/**
	 * @var CountryServiceInterface
	 */
	protected $countryService;

	/**
	 * @var ZoneJsonSerializer
	 */
	protected $zoneSerializer;


	/**
	 * Initialize Controller
	 */
	public function __initialize()
	{
		$this->countryService = StaticGXCoreLoader::getService('Country');
		$this->zoneSerializer = MainFactory::create('ZoneJsonSerializer');
	}


	/**
	 * @api        {get} /zones/:id Get Zones
	 * @apiVersion 2.0.0
	 * @apiName    GetZone
	 * @apiGroup   Zones
	 *
	 * @apiDescription
	 * Get a single registered zone resource. This method is currently limited to only fetching
	 * a single zone and might be updated in a future version of the API.
	 *
	 * @apiParam {int} id (required) The zone record ID to be returned.
	 *
	 * @apiExample {curl} Get Zone With ID = 84
	 *             curl --user admin@shop.de:12345 http://shop.de/api.php/v2/zones/84
	 *
	 * @apiSuccess (Success 200 - OK) {string} Response-Body If successful, this method will
	 * return the zone resource in JSON format.
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
		if(!isset($this->uri[1]))
		{
			throw new HttpApiV2Exception('Cannot fetch all registered zones, operation is not implemented yet.', 501);
		}

		if(!is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Provided zone ID is invalid, integer expected: ' . gettype($this->uri[1]),
			                             400);
		}

		$zone = $this->countryService->getCountryZoneById(MainFactory::create('Id', (int)$this->uri[1]));
		$this->_writeResponse($this->zoneSerializer->serialize($zone, false));
	}


	/**
	 * POST operation is not supported by the CountryService.
	 *
	 * @throws HttpApiV2Exception
	 */
	public function post()
	{
		throw new HttpApiV2Exception('Operation is not currently allowed for the zone resources.', 405);
	}


	/**
	 * PUT operation is not supported by the CountryService.
	 *
	 * @throws HttpApiV2Exception
	 */
	public function put()
	{
		throw new HttpApiV2Exception('Operation is not currently allowed for the zone resources.', 405);
	}


	/**
	 * DELETE operation is not supported by the CountryService.
	 *
	 * @throws HttpApiV2Exception
	 */
	public function delete()
	{
		throw new HttpApiV2Exception('Operation is not currently allowed for the zone resources.', 405);
	}
}