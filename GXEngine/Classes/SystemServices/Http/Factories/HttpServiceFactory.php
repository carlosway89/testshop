<?php
/* --------------------------------------------------------------
   HttpServiceFactory.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpServiceFactoryInterface');

/**
 * Class HttpFactory
 * 
 * @todo Implement "GXCoreLoaderClientInterface" for Dependency Inversion
 *       
 * @category System
 * @package Http
 * @subpackage Factories
 * @implements HttpServiceFactoryInterface
 */
class HttpServiceFactory implements HttpServiceFactoryInterface
{

	/**
	 * @return HttpServiceInterface
	 */
	public function createService()
	{
		$httpContextFactory = $this->_createAbstractHttpContextFactory();
		$httpDispatcher     = $this->_createHttpDispatcher();

		return MainFactory::create('HttpService', $httpContextFactory, $httpDispatcher);
	}


	/**
	 * @return AbstractHttpViewControllerRegistryFactory
	 * @throws \InvalidArgumentException
	 */
	protected function _createAbstractHttpViewControllerRegistryFactory()
	{
		return MainFactory::create('EnvironmentHttpViewControllerRegistryFactory');
	}


	/**
	 * @return AbstractHttpContextFactory
	 */
	protected function _createAbstractHttpContextFactory()
	{
		return MainFactory::create('EnvironmentHttpContextFactory');
	}


	/**
	 * @return HttpDispatcherInterface
	 */
	protected function _createHttpDispatcher()
	{
		$httpContextReader         = $this->_createHttpContextReader();
		$httpViewControllerFactory = $this->_createHttpViewControllerFactory();

		return MainFactory::create('HttpDispatcher', $httpContextReader, $httpViewControllerFactory);
	}


	/**
	 * @return HttpContextReaderInterface
	 */
	protected function _createHttpContextReader()
	{
		return MainFactory::create('HttpContextReader');
	}


	/**
	 * @return HttpResponseProcessorInterface
	 */
	protected function _createHttpResponseProcessor()
	{
		return MainFactory::create('HttpResponseProcessor');
	}


	/**
	 * @return HttpViewControllerFactoryInterface
	 */
	protected function _createHttpViewControllerFactory()
	{
		$httpViewControllerRegistryFactory = $this->_createAbstractHttpViewControllerRegistryFactory();
		$httpViewControllerRegistry        = $httpViewControllerRegistryFactory->create();

		$httpContextReader     = $this->_createHttpContextReader();
		$httpResponseProcessor = $this->_createHttpResponseProcessor();

		return MainFactory::create('HttpViewControllerFactory',
		                           $httpViewControllerRegistry,
		                           $httpContextReader,
		                           $httpResponseProcessor);
	}
}