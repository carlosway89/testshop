<?php
/* --------------------------------------------------------------
   HttpService.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpServiceInterface');

/**
 * Class HttpService
 * 
 * @category System
 * @package Http
 * @implements HttpServiceInterface
 */
class HttpService implements HttpServiceInterface
{
	/**
	 * @var \AbstractHttpContextFactory
	 */
	protected $httpContextFactory;
	/**
	 * @var \HttpDispatcherInterface
	 */
	protected $httpDispatcher;


	/**
	 * @param \AbstractHttpContextFactory $httpContextFactory
	 * @param \HttpDispatcherInterface    $httpDispatcher
	 */
	function __construct(AbstractHttpContextFactory $httpContextFactory, HttpDispatcherInterface $httpDispatcher)
	{
		$this->httpContextFactory = $httpContextFactory;
		$this->httpDispatcher     = $httpDispatcher;
	}


	/**
	 * @return \HttpContextInterface
	 */
	public function getHttpContext()
	{
		return $this->httpContextFactory->create();
	}


	/**
	 * @param \HttpContextInterface $httpContext
	 */
	public function handle(HttpContextInterface $httpContext)
	{
		try
		{
			$this->httpDispatcher->dispatch($httpContext);
		} 
		catch(MissingControllerNameException $e)
		{
			// @todo Display error 404 "Page not found" to user.
			// do nothing?
		}
	}
}