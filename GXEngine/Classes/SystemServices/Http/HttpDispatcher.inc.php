<?php
/* --------------------------------------------------------------
   HttpDispatcher.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpDispatcherInterface');

/**
 * Class HttpDispatcher
 * 
 * @category System
 * @package Http
 * @implements HttpDispatcherInterface
 */
class HttpDispatcher implements HttpDispatcherInterface
{
	/**
	 * @var \HttpContextReaderInterface
	 */
	protected $httpContextReader;
	/**
	 * @var \HttpViewControllerFactoryInterface
	 */
	protected $httpViewControllerFactory;


	/**
	 * @param \HttpContextReaderInterface         $httpContextReader
	 * @param \HttpViewControllerFactoryInterface $httpViewControllerFactory
	 */
	public function __construct(HttpContextReaderInterface $httpContextReader,
	                            HttpViewControllerFactoryInterface $httpViewControllerFactory)
	{
		$this->httpContextReader         = $httpContextReader;
		$this->httpViewControllerFactory = $httpViewControllerFactory;
	}


	public function dispatch(HttpContextInterface $httpContext)
	{
		$controllerName = $this->httpContextReader->getControllerName($httpContext);
		if(empty($controllerName))
		{
			throw new MissingControllerNameException('No controller name found in given HttpContext');
		}

		$controller = $this->httpViewControllerFactory->createController($controllerName);
		$controller->proceed($httpContext);
	}
}
