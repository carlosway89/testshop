<?php
/* --------------------------------------------------------------
  G-Protector v1.2
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  --------------------------------------------------------------
*/

function gprotector_convert_to_integer($p_variable)
{
	return (string)(int)$p_variable;
}

function gprotector_only_alphabetic($p_variable)
{
	return preg_replace('/[^a-zA-Z]/', '', (string)$p_variable);
}

function gprotector_only_alphanumeric($p_variable)
{
	return preg_replace('/[^a-zA-Z0-9\s_-]/', '', (string)$p_variable);
}

function gprotector_only_safe_characters($p_variable)
{
	return preg_replace('/[^a-zA-Z0-9_.,*\s@-]/', '', (string)$p_variable);
}

function gprotector_htmlentities($p_variable)
{
	$t_flags = ENT_COMPAT;
	if(defined('ENT_HTML401'))
	{
		$t_flags = ENT_COMPAT | ENT_HTML401;
	}
	
	$t_encoding = 'ISO-8859-15';	
	if(preg_match('/(?:
		[\xC2-\xDF][\x80-\xBF]
		|\xE0[\xA0-\xBF][\x80-\xBF]
		|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
		|\xED[\x80-\x9F][\x80-\xBF]
		|\xF0[\x90-\xBF][\x80-\xBF]{2}
		|[\xF1-\xF3][\x80-\xBF]{3}
		|\xF4[\x80-\x8F][\x80-\xBF]{2}
		)+/xs', $p_variable))
	{
		$t_encoding = 'UTF-8';
	}
	
	return htmlentities((string)$p_variable, $t_flags, $t_encoding);
}

function gprotector_htmlspecialchars($p_variable)
{
	$t_flags = ENT_COMPAT;
	if(defined('ENT_HTML401'))
	{
		$t_flags = ENT_COMPAT | ENT_HTML401;
	}
	
	$t_encoding = 'ISO-8859-15';	
	if(preg_match('/(?:
		[\xC2-\xDF][\x80-\xBF]
		|\xE0[\xA0-\xBF][\x80-\xBF]
		|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
		|\xED[\x80-\x9F][\x80-\xBF]
		|\xF0[\x90-\xBF][\x80-\xBF]{2}
		|[\xF1-\xF3][\x80-\xBF]{3}
		|\xF4[\x80-\x8F][\x80-\xBF]{2}
		)+/xs', $p_variable))
	{
		$t_encoding = 'UTF-8';
	}
	
	return htmlspecialchars((string)$p_variable, $t_flags, $t_encoding);
}

function gprotector_filter_price($p_variable)
{
	$t_price = trim((string)$p_variable);
	if(substr($t_price, -1) == '%')
	{
		$t_number = substr($t_price, 0, -1);
		$t_number = (double)$t_number;
		$t_price = $t_number . '%';
	}
	else
	{
		$t_price = (string)(double)$t_price;
	}
	
	return $t_price;
}

function gprotector_filter_text($p_variable)
{
	$t_variable_array = array();
	$c_variable_array = array();
	
	if(!is_array($p_variable))
	{
		$t_variable_array[] = $p_variable;
	}
	else
	{
		$t_variable_array = $p_variable;
	}
	
	$t_forbidden_array = array();
	$t_forbidden_array[] = 'onclick';
	$t_forbidden_array[] = 'ondblclick';
	$t_forbidden_array[] = 'onmousedown';
	$t_forbidden_array[] = 'onmousemove';
	$t_forbidden_array[] = 'onmouseover';
	$t_forbidden_array[] = 'onmouseout';
	$t_forbidden_array[] = 'onmouseup';
	$t_forbidden_array[] = 'onkeydown';
	$t_forbidden_array[] = 'onkeypress';
	$t_forbidden_array[] = 'onkeyup';
	$t_forbidden_array[] = 'onabort';
	$t_forbidden_array[] = 'onerror';
	$t_forbidden_array[] = 'onload';
	$t_forbidden_array[] = 'onresize';
	$t_forbidden_array[] = 'onscroll';
	$t_forbidden_array[] = 'onunload';
	$t_forbidden_array[] = 'onblur';
	$t_forbidden_array[] = 'onchange';
	$t_forbidden_array[] = 'onfocus';
	$t_forbidden_array[] = 'onreset';
	$t_forbidden_array[] = 'onselect';
	$t_forbidden_array[] = 'onsubmit';
	$t_forbidden_array[] = 'src';
	$t_forbidden_array[] = '"';
	$t_forbidden_array[] = '<';
	
	$t_pattern_array = array();
	$t_pattern_array[] = '/onclick\s*=/i';
	$t_pattern_array[] = '/ondblclick\s*=/i';
	$t_pattern_array[] = '/onmousedown\s*=/i';
	$t_pattern_array[] = '/onmousemove\s*=/i';
	$t_pattern_array[] = '/onmouseover\s*=/i';
	$t_pattern_array[] = '/onmouseout\s*=/i';
	$t_pattern_array[] = '/onmouseup\s*=/i';
	$t_pattern_array[] = '/onkeydown\s*=/i';
	$t_pattern_array[] = '/onkeypress\s*=/i';
	$t_pattern_array[] = '/onkeyup\s*=/i';
	$t_pattern_array[] = '/onabort\s*=/i';
	$t_pattern_array[] = '/onerror\s*=/i';
	$t_pattern_array[] = '/onload\s*=/i';
	$t_pattern_array[] = '/onresize\s*=/i';
	$t_pattern_array[] = '/onscroll\s*=/i';
	$t_pattern_array[] = '/onunload\s*=/i';
	$t_pattern_array[] = '/onblur\s*=/i';
	$t_pattern_array[] = '/onchange\s*=/i';
	$t_pattern_array[] = '/onfocus\s*=/i';
	$t_pattern_array[] = '/onreset\s*=/i';
	$t_pattern_array[] = '/onselect\s*=/i';
	$t_pattern_array[] = '/onsubmit\s*=/i';
	$t_pattern_array[] = '/src\s*=/i';
	$t_pattern_array[] = '/".*</';
	
	foreach($t_variable_array as $t_key => $t_value)
	{
		$c_value = (string)$t_value;
		$c_variable_array[$t_key] = $c_value;
		
		foreach($t_pattern_array as $t_pattern)
		{
			if(preg_match($t_pattern, $c_value))
			{
				$c_value = str_replace($t_forbidden_array, '', $c_value);
				$t_search_array = array('&', '"', "'", '<', '>');
				$t_replace_array = array('&amp;', '&quot;', "&#039;", '&lt;', '&gt;');
				$c_variable_array[$t_key] = str_replace($t_search_array, $t_replace_array, str_replace($t_search_array, $t_replace_array, $c_value));
			}
		}
	}
	
	if(!is_array($p_variable))
	{
		$t_return_variable = array_pop($c_variable_array);
	}
	else
	{
		$t_return_variable = $c_variable_array;
	}
	
	return $t_return_variable;
}

function gprotector_only_numeric($p_variable)
{
	return preg_replace('/[^0-9.,-]/', '', (string)$p_variable);
}

function gprotector_only_hex_code($p_variable)
{
	return preg_replace('/[^a-fA-F0-9#]/', '', (string)$p_variable);
}

function gprotector_recursive_integer_value($p_variable)
{
	if(is_array($p_variable))
	{
		$c_variable = array();
		
		foreach($p_variable as $t_key => $t_value)
		{
			$c_variable[$t_key] = '';
			if($t_value !== '')
			{
				$c_variable[$t_key] = gprotector_recursive_integer_value($t_value);
			}
		}
	}
	else
	{
		$c_variable = gprotector_convert_to_integer($p_variable);
	}
	
	return $c_variable;
}


function gprotector_recursive_only_alphanumeric($p_variable)
{
	if(is_array($p_variable))
	{
		$c_variable = array();
		
		foreach($p_variable as $t_key => $t_value)
		{
			$c_variable[$t_key] = gprotector_recursive_only_alphanumeric($t_value);
		}
	}
	else
	{
		$c_variable = gprotector_only_alphanumeric($p_variable);
	}
	
	return $c_variable;
}


function gprotector_recursive_filter_text($p_variable)
{
	if(is_array($p_variable))
	{
		$c_variable = array();
		
		foreach($p_variable as $t_key => $t_value)
		{
			$c_variable[$t_key] = gprotector_recursive_filter_text($t_value);
		}
	}
	else
	{
		$c_variable = gprotector_filter_text($p_variable);
	}
	
	return $c_variable;
}


function gprotector_recursive_only_safe_characters($p_variable)
{
	if(is_array($p_variable))
	{
		$c_variable = array();
		
		foreach($p_variable as $t_key => $t_value)
		{
			$c_variable[$t_key] = gprotector_recursive_only_safe_characters($t_value);
		}
	}
	else
	{
		$c_variable = gprotector_only_safe_characters($p_variable);
	}
	
	return $c_variable;
}


function gprotector_recursive_htmlspecialchars($p_variable)
{
	if(is_array($p_variable))
	{
		$c_variable = array();
		
		foreach($p_variable as $t_key => $t_value)
		{
			$c_variable[$t_key] = gprotector_recursive_htmlspecialchars($t_value);
		}
	}
	else
	{
		$c_variable = gprotector_htmlspecialchars($p_variable);
	}
	
	return $c_variable;
}


function gprotector_basename($p_variable)
{
	$c_variable = '';
	
	if(is_string($p_variable))
	{
		$c_variable = basename($p_variable);
	}
	
	return $c_variable;
}
