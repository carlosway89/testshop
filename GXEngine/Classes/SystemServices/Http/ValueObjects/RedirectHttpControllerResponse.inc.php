<?php
/* --------------------------------------------------------------
   RedirectHttpControllerResponse.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpControllerResponse');

/**
 * Value object
 * 
 * Class RedirectHttpControllerResponse
 * 
 * @category System
 * @package Http
 * @subpackage ValueObjects
 * @extends HttpControllerResponse
 */
class RedirectHttpControllerResponse extends HttpControllerResponse
{
	/**
	 * @param string $p_location
	 * @param bool   $p_movedPermanently
	 */
	public function __construct($p_location, $p_movedPermanently = false)
	{
		if($p_movedPermanently)
		{
			$this->httpHeadersArray[] = 'HTTP/1.1 301 Moved Permanently';
		}
		$this->httpHeadersArray[] = 'Location: ' . $p_location;
	}
}