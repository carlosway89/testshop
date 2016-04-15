<?php
/* --------------------------------------------------------------
   HttpControllerResponse.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpControllerResponseInterface');

/**
 * Value object
 * 
 * Class HttpControllerResponse
 * 
 * @category System
 * @package Http
 * @subpackage ValueObjects
 * @extends HttpControllerResponseInterface
 */
class HttpControllerResponse implements HttpControllerResponseInterface
{
	/**
	 * @var array
	 */
	protected $httpHeadersArray = array();

	/**
	 * @var string
	 */
	protected $httpBody;


	/**
	 * @param string $p_responseBody
	 * @param array  $responseHeadersArray
	 */
	public function __construct($p_responseBody, $responseHeadersArray = null)
	{
		if($responseHeadersArray != null)
		{
			$this->httpHeadersArray = $responseHeadersArray;
		}
		$this->httpBody = $p_responseBody;
	}


	/**
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->httpHeadersArray;
	}


	/**
	 * @return string
	 */
	public function getBody()
	{
		return $this->httpBody;
	}
}