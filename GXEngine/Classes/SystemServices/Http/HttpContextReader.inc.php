<?php
/* --------------------------------------------------------------
   HttpContextReader.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpContextReaderInterface');

/**
 * Class HttpContextReader
 * 
 * @category System
 * @package Http
 * @implements HttpContextReaderInterface
 */
class HttpContextReader implements HttpContextReaderInterface
{
	/**
	 * @param HttpContextInterface $httpContext
	 *
	 * @return string
	 * @todo outsource to strategies
	 */
	public function getControllerName(HttpContextInterface $httpContext)
	{
		$doValue      = (string)$httpContext->getGetItem('do');
		$doPartsArray = explode('/', $doValue);

		return $doPartsArray[0];
	}


	/**
	 * @param \HttpContextInterface $httpContext
	 *
	 * @return string
	 * @todo outsource to strategies
	 * @todo Shouldn't it return "Default" instead of empty string if there is no second item in the $doPartsArray?
	 */
	public function getActionName(HttpContextInterface $httpContext)
	{
		$doValue      = (string)$httpContext->getGetItem('do');
		$doPartsArray = explode('/', $doValue);
		if(sizeof($doPartsArray) < 2)
		{
			return '';
		}

		return $doPartsArray[1];
	}


	/**
	 * @param HttpContextInterface $httpContext
	 *
	 * @return array
	 */
	public function getQueryParameters(HttpContextInterface $httpContext)
	{
		return $httpContext->getGetArray();
	}


	/**
	 * @param HttpContextInterface $httpContext
	 *
	 * @return array
	 */
	public function getPostData(HttpContextInterface $httpContext)
	{
		return $httpContext->getPostArray();
	}
}