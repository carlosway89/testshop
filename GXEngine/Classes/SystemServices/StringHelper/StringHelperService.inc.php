<?php
/* --------------------------------------------------------------
   StringHelperService.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('StringHelperServiceInterface');
MainFactory::load_class('CrossCuttingObjectInterface');

/**
 * Class StringHelperService
 * 
 * @category System
 * @package StringHelper
 * @implements StringHelperServiceInterface, CrossCuttingObjectInterface
 */
class StringHelperService implements StringHelperServiceInterface, CrossCuttingObjectInterface
{
	/**
	 * Converts NULL values to empty string inside an array
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	public function convertNullValuesToStringInArray(array $array)
	{
		foreach($array as $key => $value)
		{
			if($value === null)
			{
				$array[$key] = (string)$value;
			}
		}

		return $array;
	}
}
