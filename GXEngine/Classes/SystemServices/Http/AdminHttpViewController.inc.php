<?php
/* --------------------------------------------------------------
   AdminHttpViewController.inc.php 2015-12-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');

/**
 * Class AdminHttpViewController
 *
 * This class extends the HttpViewController class with a customer status check.
 * Extend this class whenever you create new controllers that are for admin users only.
 *
 * @category   System
 * @package    Http
 * @extends    HttpViewController
 */
class AdminHttpViewController extends HttpViewController
{
	/**
	 * Process HttpContext Object and check allowed customer status
	 *
	 * @param \HttpContextInterface $httpContext
	 *
	 * @throws \LogicException
	 */
	public function proceed(HttpContextInterface $httpContext)
	{
		if($_SESSION['customers_status']['customers_status_id'] != 0)
		{
			throw new LogicException('unexpected execution context');
    	}
		
		parent::proceed($httpContext);
	}


}