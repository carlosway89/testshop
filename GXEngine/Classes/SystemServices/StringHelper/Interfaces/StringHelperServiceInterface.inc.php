<?php
/* --------------------------------------------------------------
   StringHelperServiceInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface StringHelperServiceInterface
 * 
 * @category System
 * @package StringHelper
 * @subpackage Interfaces
 */
interface StringHelperServiceInterface 
{
   /**
    * Converts NULL values to empty string inside an array
    * 
    * @param array $array
    *
    * @return array
    */
   public function convertNullValuesToStringInArray(array $array);
}