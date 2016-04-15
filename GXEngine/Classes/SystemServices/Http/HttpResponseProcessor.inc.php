<?php
/* --------------------------------------------------------------
   HttpResponseProcessor.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpResponseProcessorInterface');

/**
 * Class HttpResponseProcessor
 * 
 * @category System
 * @package Http
 * @implements HttpResponseProcessorInterface
 */
class HttpResponseProcessor implements HttpResponseProcessorInterface
{

	/**
	 * @param \HttpControllerResponseInterface $response
	 */
	public function proceed(HttpControllerResponseInterface $response)
	{
		//var_dump($response);
		$this->_sendHeaders($response->getHeaders());
		$this->_sendBody($response->getBody());
	}


	/**
	 * @param array $httpHeadersArrays
	 */
	protected function _sendHeaders(array $httpHeadersArrays)
	{
		foreach($httpHeadersArrays as $headerItem)
		{
			header($headerItem);
		}
	}


	/**
	 * @param $p_httpBody
	 */
	protected function _sendBody($p_httpBody)
	{
		echo $p_httpBody;
	}
}