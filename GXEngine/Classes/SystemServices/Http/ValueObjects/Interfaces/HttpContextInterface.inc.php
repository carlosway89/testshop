<?php
/* --------------------------------------------------------------
   HttpContextInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface HttpContextInterface
 * @todo Add methods
 *       
 * @category System
 * @package Http
 * @subpackage Interfaces
 */
interface HttpContextInterface
{

   public function getServerItem($p_keyName);


   public function getGetItem($p_keyName);


   public function getPostItem($p_keyName);


   public function getCookieItem($p_keyName);


   public function getSessionItem($p_keyName);
}