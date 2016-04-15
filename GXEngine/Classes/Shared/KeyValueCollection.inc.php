<?php
/* --------------------------------------------------------------
   KeyValueCollection.inc.php 2015-01-24 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCollection');


/**
 * Class KeyValueCollection
 * 
 * @category System
 * @package  Shared
 */
class KeyValueCollection extends AbstractCollection
{
	/**
	 * Class Constructor
	 * 
	 * @param array $keyValueArray
	 */
	public function __construct(array $keyValueArray)
	{
		foreach($keyValueArray as $itemKey => $itemValue)
		{
			$this->collectionContentArray[$itemKey] = $itemValue;
		}
	}


	/**
	 * Get the value that corresponds to the provided key.
	 * 
	 * @param string $p_keyName
	 *
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public function getValue($p_keyName)
	{
		if(isset($this->collectionContentArray[$p_keyName]) == false)
		{
			throw new InvalidArgumentException('Given keyName not found: ' . htmlentities($p_keyName));
		}

		return $this->collectionContentArray[$p_keyName];
	}


	/**
	 * Get valid item type.
	 * 
	 * @return string
	 */
	protected function _getValidType()
	{
		return 'string';
	}
}
 