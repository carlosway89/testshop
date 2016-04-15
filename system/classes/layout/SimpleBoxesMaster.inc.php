<?php
/* --------------------------------------------------------------
   SimpleBoxesMaster.inc.php 2010-11-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Description of SimpleBoxesMaster
 *
 * @author ncapuno
 */
class SimpleBoxesMaster
{
	function SimpleBoxesMaster()
	{
	}

	function get_status($p_menubox)
	{
		$t_output = false;
		$coo_template_control =& MainFactory::create_object('TemplateControl', array(), true);

		if(isset($coo_template_control->v_optional_template_settings_array['MENUBOXES'][$p_menubox]) == false)
		{
			trigger_error('menubox not found in settings-array: '. $p_menubox);
			$t_output = 0;
		}
		else {
			$t_output = $coo_template_control->v_optional_template_settings_array['MENUBOXES'][$p_menubox]['STATUS'];
		}
		return $t_output;
	}

	function get_position($p_menubox)
	{
		$t_output = false;
		$coo_template_control =& MainFactory::create_object('TemplateControl', array(), true);
		
		if(isset($coo_template_control->v_optional_template_settings_array['MENUBOXES'][$p_menubox]) == false)
		{
			trigger_error('menubox not found in settings-array: '. $p_menubox);
			$t_output = 0;
		}
		else {
			$t_output = $coo_template_control->v_optional_template_settings_array['MENUBOXES'][$p_menubox]['POSITION'];
		}
		return $t_output;
	}

}
?>