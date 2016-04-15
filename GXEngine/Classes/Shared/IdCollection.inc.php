<?php
/* --------------------------------------------------------------
   IdCollection.inc.php 2015-01-24 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCollection');


/**
 * Class IdCollection
 * 
 * @category System
 * @package Shared
 */
class IdCollection extends AbstractCollection
{
   /**
    * Get valid item type.
    * 
    * @return string
    */
   protected function _getValidType()
	{
		return 'IdInterface';
	}
}
 