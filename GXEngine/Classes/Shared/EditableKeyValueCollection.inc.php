<?php
/* --------------------------------------------------------------
   EditableKeyValueCollection.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('KeyValueCollection');

/**
 * Class EditableKeyValueCollection
 * 
 * @category System
 * @package Shared
 */
class EditableKeyValueCollection extends KeyValueCollection
{
	/**
	 * Set new key-value pair.
	 * 
	 * @param string $p_keyName
	 * @param mixed  $p_value
	 */
	public function setValue($p_keyName, $p_value)
	{
		$this->collectionContentArray[$p_keyName] = $p_value;
	}
}