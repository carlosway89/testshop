<?php
/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2010 Gambio GmbH
  --------------------------------------------------------------
*/

function json_encode($p_array = false)
{
	if (is_null($p_array))
	{
		return 'null';
	}

	if ($p_array === false) 
	{
		return 'false';
	}

	if ($p_array === true)
	{
		return 'true';
	}

	if (is_scalar($p_array))
	{
		if (is_float($p_array))
		{
			return floatval(str_replace(",", ".", strval($p_array)));
		}

		if (is_string($p_array))
		{
			$t_replace = array(
									array(
											"\\", 
											"/", 
											"\n", 
											"\t", 
											"\r", 
											"\b", 
											"\f", 
											'"'
									), 
									array(
											'\\\\', 
											'\\/', 
											'\\n', 
											'\\t', 
											'\\r', 
											'\\b', 
											'\\f', 
											'\"'
									)
			);
			return '"' . str_replace($t_replace[0], $t_replace[1], $p_array) . '"';
		}
		else
		{
			return $p_array;
		}
	}
	
	$t_check = true;
	for ($i = 0, reset($p_array); $i < count($p_array); $i++, next($p_array))
	{
		if (key($p_array) !== $i)
		{
			$t_check = false;
			break;
		}
	}

	$t_result = array();
	if ($t_check)
	{
		foreach ($p_array as $v) 
		{
			$t_result[] = json_encode($v);
		}
		return '[' . join(',', $t_result) . ']';
	}
	else
	{
		foreach ($p_array as $k => $v)
		{
			$t_result[] = json_encode($k).':'.json_encode($v);
		}
		return '{' . join(',', $t_result) . '}';
	}
}

?>