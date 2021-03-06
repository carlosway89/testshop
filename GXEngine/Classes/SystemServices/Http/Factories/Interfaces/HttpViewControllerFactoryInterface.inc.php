<?php
/* --------------------------------------------------------------
   HttpViewControllerFactoryInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface HttpViewControllerFactoryInterface
 * 
 * @category System
 * @package Http
 * @subpackage Interfaces
 */
interface HttpViewControllerFactoryInterface
{
	/**
	 * @param string $p_controllerName
	 *
	 * @return HttpViewControllerInterface
	 */
	public function createController($p_controllerName);
}