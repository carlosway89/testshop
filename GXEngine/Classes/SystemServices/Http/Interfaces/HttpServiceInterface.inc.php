<?php
/* --------------------------------------------------------------
   HttpServiceInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface HttpServiceInterface
 * @todo       Add methods
 *
 * @category   System
 * @package    Http
 * @subpackage Interfaces
 */
interface HttpServiceInterface
{

	/**
	 * @param \AbstractHttpContextFactory $httpContextFactory
	 * @param \HttpDispatcherInterface    $httpDispatcher
	 */
	function __construct(AbstractHttpContextFactory $httpContextFactory, HttpDispatcherInterface $httpDispatcher);


	/**
	 * @return \HttpContextInterface
	 */
	public function getHttpContext();


	/**
	 * @param \HttpContextInterface $httpContext
	 */
	public function handle(HttpContextInterface $httpContext);
}