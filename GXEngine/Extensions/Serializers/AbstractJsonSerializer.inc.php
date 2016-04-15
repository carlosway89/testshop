<?php
/* --------------------------------------------------------------
   JsonSerializer.inc.php 2015-07-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('SerializerInterface');

/**
 * Abstract Json Serializer
 * 
 * Serializers that extend this class should parse and encode entities
 * so that they can be used in the shop's APIs.
 *
 * Serialization must follow the "null" approach in order to enhance response clarity.
 * That means that serializers must provide a null value than an empty string or an omitted node.
 *
 * @category System
 * @package Extensions
 * @subpackage Serializers
 */
abstract class AbstractJsonSerializer implements SerializerInterface
{
	abstract public function serialize($object, $encode = true);

	abstract public function deserialize($string, $baseObject = null);

	/**
	 * JSON Encode Wrapper 
	 * 
	 * This function provides PHP v5.3 compatibility and it should be used when serialized objects 
	 * need to be encoded directly from the serializer instance. 
	 * 
	 * @param array $data Contains the data to be JSON encoded.
	 *
	 * @return string Returns the encoded JSON string that represents the data. 
	 */
	public function jsonEncode(array $data)
	{
		if(defined(JSON_PRETTY_PRINT) && defined(JSON_UNESCAPED_SLASHES))
		{
			$dataJsonString = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		}
		else
		{
			$dataJsonString = json_encode($data); // PHP v5.3
		}
		
		return $dataJsonString; 
	}
}