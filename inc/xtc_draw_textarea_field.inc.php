<?php
/* --------------------------------------------------------------
   xtc_draw_textarea_field.inc.php 2014-11-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(html_output.php,v 1.52 2003/03/19); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_draw_textarea_field.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_draw_textarea_field.inc.php 1300 2005-10-10 20:40:33Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

// Output a form textarea field
function xtc_draw_textarea_field($name, $wrap, $width, $height, $text = '', $parameters = '', $reinsert_value = true)
{
	$field = '<textarea name="' . xtc_parse_input_field_data($name, array('"' => '&quot;'))
			 . '" id="' . xtc_parse_input_field_data($name, array('"' => '&quot;'))
			 . '" cols="' . xtc_parse_input_field_data($width, array('"' => '&quot;'))
			 . '" rows="' . xtc_parse_input_field_data($height, array('"' => '&quot;')) . '"';

	if(xtc_not_null($parameters))
	{
		$field .= ' ' . $parameters;
	}

	$field .= '>';

	if((isset($GLOBALS[$name])) && ($reinsert_value == true))
	{
		$field .= htmlspecialchars_wrapper($GLOBALS[$name]);
	}
	elseif(xtc_not_null($text))
	{
		$field .= htmlspecialchars_wrapper($text);
	}

	$field .= '</textarea>';

	return $field;
}
