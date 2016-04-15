<?php
/* --------------------------------------------------------------
   HttpViewControllerInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface HttpViewControllerInterface
 * @todo Add methods
 *       
 * @category System
 * @package Http
 * @subpackage Interfaces
 */
interface HttpViewControllerInterface
{

	/**
	 * @param \HttpContextInterface $httpContext
	 *
	 * @throws \LogicException
	 */
	public function proceed(HttpContextInterface $httpContext);


	/**
	 * @return \HttpControllerResponseInterface
	 */
	public function actionDefault();
}