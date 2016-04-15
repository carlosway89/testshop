<?php
/* --------------------------------------------------------------
   AbstractHttpContextFactory.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class AbstractHttpViewControllerRegistryFactory
 *
 * @category   System
 * @package    Http
 * @subpackage Factories
 */
abstract class AbstractHttpViewControllerRegistryFactory
{
	/**
	 * @return HttpViewControllerRegistryInterface
	 */
	public abstract function create();
}