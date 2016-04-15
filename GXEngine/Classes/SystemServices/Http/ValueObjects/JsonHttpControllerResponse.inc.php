<?php
/* --------------------------------------------------------------
   JsonHttpControllerResponse.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpControllerResponse');

/**
 * Value object
 * 
 * Class JsonHttpControllerResponse
 * 
 * @category System
 * @package Http
 * @subpackage ValueObjects
 * @extends HttpControllerResponse
 */
class JsonHttpControllerResponse extends HttpControllerResponse
{

	public function __construct(array $contentArray)
	{
		//$this->_utf8_encode_deep($contentArray);
		$this->httpBody = json_encode($contentArray);
   }


	protected function _utf8_encode_deep(&$input)
	{
		if(is_string($input))
		{
			$input = utf8_encode($input);
		}
		else
		{
			if(is_array($input))
			{
				foreach($input as &$value)
				{
					$this->_utf8_encode_deep($value);
				}

				unset($value);
			}
			else
			{
				if(is_object($input))
				{
					$vars = array_keys(get_object_vars($input));

					foreach($vars as $var)
					{
						$this->_utf8_encode_deep($input->$var);
					}
				}
			}
		}
	}
}