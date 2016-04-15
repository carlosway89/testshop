<?php
/* --------------------------------------------------------------
   HttpViewControllerFactory.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewControllerFactoryInterface');

/**
 * Class HttpViewControllerFactory
 * 
 * @category System
 * @package Http
 * @subpackage Factories
 * @implements HttpViewControllerFactoryInterface
 */
class HttpViewControllerFactory implements HttpViewControllerFactoryInterface
{
	/**
	 * @var \HttpViewControllerRegistryInterface
	 */
	protected $httpViewControllerRegistry;
	/**
	 * @var \HttpContextReaderInterface
	 */
	protected $httpContextReader;
	/**
	 * @var \HttpResponseProcessorInterface
	 */
	protected $httpResponseProcessor;


	/**
	 * @param \HttpViewControllerRegistryInterface $httpViewControllerRegistry
	 * @param \HttpContextReaderInterface          $httpContextReader
	 * @param \HttpResponseProcessorInterface      $httpResponseProcessor
	 */
	public function __construct(HttpViewControllerRegistryInterface $httpViewControllerRegistry,
	                            HttpContextReaderInterface $httpContextReader,
	                            HttpResponseProcessorInterface $httpResponseProcessor)
	{
		$this->httpViewControllerRegistry = $httpViewControllerRegistry;
		$this->httpContextReader          = $httpContextReader;
		$this->httpResponseProcessor      = $httpResponseProcessor;
	}


	/**
	 * @param string $p_controllerName
	 *
	 * @return HttpViewControllerInterface
	 * @throws \InvalidArgumentException
	 * @throws \LogicException
	 */
	public function createController($p_controllerName)
	{
		$className   = $this->_getControllerClassName($p_controllerName);
		$contentView = $this->_createControllerContentView($p_controllerName);

		$controller = MainFactory::create($className,
		                                  $this->httpContextReader,
		                                  $this->httpResponseProcessor,
		                                  $contentView);

		return $controller;
	}


	/**
	 * @param string $p_controllerName
	 *
	 * @return string
	 * @throws \LogicException
	 */
	protected function _getControllerClassName($p_controllerName)
	{
		$className = $this->httpViewControllerRegistry->get($p_controllerName);

		if(empty($className))
		{
			throw new LogicException('No controller class found for [' . htmlentities($p_controllerName) . ']');
		}

		if(in_array('HttpViewControllerInterface', class_implements($className)) == false)
		{
			throw new LogicException('HttpViewControllerInterface not implemented in called controller class ['
			                         . htmlentities($p_controllerName) . ']');
		}

		return $className;
	}


	/**
	 * @param string $p_controllerName
	 *
	 * @return \ContentViewInterface
	 * @throws \InvalidArgumentException
	 * @throws \LogicException
	 */
	protected function _createControllerContentView($p_controllerName)
	{
		$contentViewClassName = $p_controllerName . 'ContentView';

		if(class_exists($contentViewClassName) == false)
		{
			$contentView = MainFactory::create('ContentView');
			$contentView->set_flat_assigns(true);
			return $contentView;
		}

		if(in_array('ContentViewInterface', class_implements($contentViewClassName)) == false)
		{
			throw new LogicException('ContentViewInterface not implemented in found ContentView class for called controller ['
			                         . htmlentities($p_controllerName) . ']');
		}

		return MainFactory::create($contentViewClassName);
	}
}