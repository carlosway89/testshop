<?php
/* --------------------------------------------------------------
   AbstractCollection.inc.php 2015-01-24 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class AbstractCollection
 * 
 * @category System
 * @package Shared
 */
abstract class AbstractCollection implements IteratorAggregate
{
	/**
	 * Content Collection
	 * 
	 * @var array
	 */
	protected $collectionContentArray = array();


	/**
	 * Class Constructor
	 */
	public function __construct()
	{
		$argsArray = func_get_args();

		foreach($argsArray as $argsItem)
		{
			try
			{
				$this->_add($argsItem);
			}
			catch(InvalidArgumentException $e)
			{
				throw $e;
			}
		}
	}


	/**
	 * Get collection item count.
	 * 
	 * @return int
	 */
	public function count()
	{
		return sizeof($this->collectionContentArray);
	}


	/**
	 * Get the collection as an array.
	 * 
	 * TODO rename to toArray()
	 * 
	 * @return array
	 */
	public function getArray()
	{
		return $this->collectionContentArray;
	}


	/**
	 * Get specific collection item by index.
	 * 
	 * @param $p_index
	 *
	 * @return mixed
	 */
	public function getItem($p_index)
	{
		if(is_numeric($p_index) == false)
		{
			throw new InvalidArgumentException('Given $p_index not numeric');
		}

		if($p_index < 0 || $p_index >= sizeof($this->collectionContentArray))
		{
			throw new OutOfBoundsException('$p_index is out of bounds');
		}
		return $this->collectionContentArray[$p_index];
	}


	/**
	 * Determine whether the collection is empty or not.
	 * 
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty($this->collectionContentArray);
	}
	

	/**
	 * Add a new item. 
	 * 
	 * This method must be used by child-collection classes.
	 * 
	 * @param mixed $item
	 */
	protected function _add($item)
	{
		if($this->_itemIsValid($item) == false)
		{
			$exceptionText = $this->_getExceptionText();
			throw new InvalidArgumentException($exceptionText);
		}
		else
		{
			$this->collectionContentArray[] = $item;
		}
	}


	/**
	 * Check if a new item has the valid collection type.
	 * 
	 * @param mixed $dataItem
	 *
	 * @return bool
	 */
	protected function _itemIsValid($dataItem)
	{
		if(is_object($dataItem) == false)
		{
			return false;
		}

		if(is_a($dataItem, $this->_getValidType()) == false)
		{
			return false;
		}
		
		return true;
	}


	/**
	 * Get exception text.
	 * 
	 * @return string
	 */
	protected function _getExceptionText()
	{
		return 'Given item has invalid type ('. $this->_getValidType() .' needed)';
	}
	
	
	public function getIterator()
	{
		return new ArrayIterator($this->collectionContentArray);
	}


	/**
	 * Get valid type. 
	 * 
	 * This method must be implemented in the child-collection classes.
	 * 
	 * @return string
	 */
	abstract protected function _getValidType();
}
 