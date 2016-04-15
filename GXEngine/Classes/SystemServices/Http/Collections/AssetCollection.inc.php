<?php
/* --------------------------------------------------------------
   AssetCollection.inc.php 2015-03-13 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCollection');
MainFactory::load_class('AssetCollectionInterface');

/**
 * Class AssetCollection
 * 
 * Handles Asset objects (JavaScript and CSS). Use the getHtml() method
 * to get the HTML output for the collection. The elements will be outputted
 * in the same order they were added in the collection. 
 * 
 * @category System
 * @package Http
 * @subpackage Collections
 */
class AssetCollection extends AbstractCollection implements AssetCollectionInterface
{
	/**
	 * Add a new asset into the collection.
	 *
	 * @param AssetInterface $asset
	 */
	public function add(AssetInterface $asset)
	{
		$this->_add($asset);
	}


	/**
	 * Prints the HTML markup for the
	 *
	 * @return string Returns the HTML markup of the assets.
	 */
	public function getHtml() {
		$html = '';
		foreach($this->collectionContentArray as $asset) 
		{
			$html .= (string)$asset . PHP_EOL; 
		}
		return $html; 
	}

	/**
	 * Get the type of te collection items.
	 *
	 * @return string
	 */
	protected function _getValidType()
	{
		return 'AssetInterface';
	}
}