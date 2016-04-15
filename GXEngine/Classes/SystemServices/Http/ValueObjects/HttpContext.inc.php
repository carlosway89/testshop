<?php
/* --------------------------------------------------------------
   HttpContext.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpContextInterface');

/**
 * Value object
 * 
 * Class HttpContext
 * 
 * @todo Add methods
 *       
 * @category System
 * @package Http
 * @subpackage ValueObjects
 * @extends HttpContextInterface
 */
class HttpContext implements HttpContextInterface
{
	/**
	 * @var array
	 */
	protected $serverArray;
	/**
	 * @var array
	 */
	protected $getArray;
	/**
	 * @var array
	 */
	protected $postArray;
	/**
	 * @var array
	 */
	protected $cookieArray;
	/**
	 * @var array
	 */
	protected $sessionArray;


	/**
	 * @param array $serverArray
	 * @param array $getArray
	 * @param array $postArray
	 * @param array $cookieArray
	 * @param array $sessionArray
	 */
	public function __construct(array $serverArray,
	                            array $getArray,
	                            array $postArray,
	                            array $cookieArray,
	                            array $sessionArray)
	{
		$this->serverArray  = $serverArray;
		$this->getArray     = $getArray;
		$this->postArray    = $postArray;
		$this->cookieArray  = $cookieArray;
		$this->sessionArray = $sessionArray;
	}


	/**
	 * @param string $p_keyName
	 *
	 * @return mixed
	 */
	public function getServerItem($p_keyName)
	{
		if(isset($this->serverArray[$p_keyName]) == false)
		{
			return null;
		}

		return $this->serverArray[$p_keyName];
	}


	/**
	 * @param string $p_keyName
	 *
	 * @return mixed
	 */
	public function getGetItem($p_keyName)
	{
		if(isset($this->getArray[$p_keyName]) == false)
		{
			return null;
		}

		return $this->getArray[$p_keyName];
	}


	/**
	 * @param string $p_keyName
	 *
	 * @return mixed
	 */
	public function getPostItem($p_keyName)
	{
		if(isset($this->postArray[$p_keyName]) == false)
		{
			return null;
		}

		return $this->postArray[$p_keyName];
	}


	/**
	 * @param string $p_keyName
	 *
	 * @return mixed
	 */
	public function getCookieItem($p_keyName)
	{
		if(isset($this->cookieArray[$p_keyName]) == false)
		{
			return null;
		}

		return $this->cookieArray[$p_keyName];
	}


	/**
	 * @param string $p_keyName
	 *
	 * @return mixed
	 */
	public function getSessionItem($p_keyName)
	{
		if(isset($this->sessionArray[$p_keyName]) == false)
		{
			return null;
		}

		return $this->sessionArray[$p_keyName];
	}


	/**
	 * @return array
	 */
	public function getGetArray()
	{
		return $this->getArray;
	}


	/**
	 * @return array
	 */
	public function getPostArray()
	{
		return $this->postArray;
	}
}