<?php
/* --------------------------------------------------------------
   IdInterface.inc.php 2015-01-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface IdInterface
 * 
 * @category System
 * @package Shared
 */
interface IdInterface
{
   /**
    * To string magic method.
    * 
    * @return mixed
    */
   public function __toString();
}