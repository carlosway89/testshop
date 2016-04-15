<?php
/* --------------------------------------------------------------
   DefaultApiV2Controller.inc.php 2015-09-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class DefaultApiV2Controller
 *
 * The default APIv2 controller will be triggered when client consumers hit the "api.php/v2"
 * URI and it will return information about the API.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class DefaultApiV2Controller extends HttpApiV2Controller
{
	public function get()
	{
		$this->_returnHelpResponse();
	}


	public function post()
	{
		$this->_returnHelpResponse();
	}


	public function put()
	{
		$this->_returnHelpResponse();
	}


	public function patch()
	{
		$this->_returnHelpResponse();
	}


	public function delete()
	{
		$this->_returnHelpResponse();
	}


	public function head()
	{
		$this->_returnHelpResponse();
	}


	public function options()
	{
		$this->_returnHelpResponse();
	}


	protected function _returnHelpResponse()
	{
		$apiUrl = HTTP_SERVER . $this->api->request->getRootUri() . '/v2/';

		$response = array(
				'customers' => $apiUrl . 'customers',
				'emails'    => $apiUrl . 'emails',
				'addresses' => $apiUrl . 'addresses',
				'countries' => $apiUrl . 'countries',
				'zones'     => $apiUrl . 'zones'
		);

		$this->_writeResponse($response);
	}
}