<?php
/* --------------------------------------------------------------
   Id.inc.php 2015-01-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('IdInterface');

/**
 * Class Id
 * 
 * IMPORTANT:
 * When you need to cast an Id object to integer, cast it first to string,
 * because otherwise the following command will return always 1:
 * 
 * EXAMPLE:
 * 		$id = new Id(948);
 * 		bad  - (int)$id 		>> 1
 * 		good - (int)(string)$id >> 948
 * 
 * @category System
 * @package Shared
 */
class Id implements IdInterface
{
	protected $id;
	
	/**
	 * Class Constructor
	 * 
	 * @param $id
	 */
	public function __construct($id)
	{
		$this->_validate($id);
		$this->id = (int)(string)$id;
	}

	/**
	 * Validate ID value.
	 * 
	 * @param $id
	 * @throws InvalidArgumentException $id is not numeric
	 * @throws InvalidArgumentException $id is not an integer
	 * @throws InvalidArgumentException $id is negative
	 */
	protected function _validate($id)
	{	
		if ($id instanceof Id)
		{
			return; // Validation is not required since $id is already an Id object.
		}
		
		if(!is_numeric($id))
		{
			throw new InvalidArgumentException('$id is not numeric');
		}
		elseif((int)$id != $id)
		{
			throw new InvalidArgumentException('$id is not an integer');
		}
		elseif((int)$id < 0)
		{
			throw new InvalidArgumentException('$id is negative');
		}
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return (string)$this->id;
	}
}