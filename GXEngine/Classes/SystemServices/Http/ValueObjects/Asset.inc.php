<?php
/* --------------------------------------------------------------
   Asset.inc.php 2015-03-13 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AssetInterface');

/**
 * Class Asset
 * 
 * @category System
 * @package Http
 * @subpackage ValueObjects
 */
class Asset implements AssetInterface
{
	/**
	 * JavaScript Asset Type
	 */
	const JAVASCRIPT = 'javascript';

	/**
	 * CSS Asset Type
	 */
	const CSS = 'css';

	/**
	 * @var string Asset's relative path.
	 */
	protected $path;

	/**
	 * @var string Asset's type (defined by the file extension).
	 */
	protected $type;


	/**
	 * Class Constructor
	 * 
	 * @param string $p_path Relative path to the asset file (relative to the "src" directory). 
	 */
	public function __construct($p_path)
	{
		if(!is_string($p_path) || empty($p_path))
		{
			throw new InvalidArgumentException('Invalid argument $p_path provided (relative asset path - string expected): '
			                                   . print_r($p_path, true));
		}
		
		$this->path = (string)$p_path;
		
		if(substr($this->path, -3) === '.js')
		{
			$this->type = self::JAVASCRIPT; 
		}
		else if(substr($this->path, -4) === '.css')
		{
			$this->type = self::CSS; 
		}
		else 
		{
			throw new InvalidArgumentException('Provided asset is not supported, provide JavaScript(.js) and CSS (.css) assets.');
		}
	}


	/**
	 * Get asset HTML markup.  
	 * 
	 * @return string Returns the HTML markup that will load the file when the page is loaded.
	 */
	public function __toString()
	{
		switch($this->type)
		{
			case self::JAVASCRIPT:
				return '<script type="text/javascript" src="' . $this->path . '"></script>';
				break;
			case self::CSS:
				return '<link rel="stylesheet" type="text/css" href="' . $this->path . '" />';
				break;
			default:
				return ''; // Just in case the asset type was not set correctly.
		}
	}
}