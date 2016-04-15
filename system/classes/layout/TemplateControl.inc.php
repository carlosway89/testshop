<?php
/* --------------------------------------------------------------
   TemplateContol.inc.php 2014-07-14 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TemplateControl
 *
 * first load:	$coo_template_control =& MainFactory::create_object('TemplateControl', array(CURRENT_TEMPLATE), true);
 * later use:	$coo_template_control =& MainFactory::create_object('TemplateControl', array(), true);
 *
 * @author ncapuno
 */
class TemplateControl
{
	public $v_optional_template_settings_array = array();

	# set in load_template()
	protected $v_template_name = '';
	protected $v_template_presentation_version = '';
	
	# set in constructor
	protected $v_template_container = '';
	protected $v_coo_boxes_master = false;

	public static function &get_instance($p_template_name=false)
	{
		static $s_instance;

		if($s_instance === NULL)
		{
			if($p_template_name === false)
			{
				trigger_error('need template-parameter for creating instance', E_USER_ERROR);
				die();
			}
			$s_instance = MainFactory::create_object('TemplateControl', array($p_template_name));
		}
		return $s_instance;
	}

	public function TemplateControl($p_template_name)
	{
		if(isset($GLOBALS['gmBoxesMaster']))
		{
			$this->v_coo_boxes_master = $GLOBALS['gmBoxesMaster'];
		}
		else {
			$this->v_coo_boxes_master = MainFactory::create_object('SimpleBoxesMaster');
		}
		
		$this->v_template_container = DIR_FS_CATALOG.'templates/';
		$this->load_template($p_template_name);
	}

	public function load_template($p_template_name)
	{
		# all right. set template name
		$this->v_template_name = $p_template_name;

		# try to load template settings
		$t_settings_file = $this->v_template_container.$this->v_template_name.'/template_settings.php';
		if(file_exists($t_settings_file))
		{
			# include file with settings-array inside
			include($t_settings_file);
			
			# load settings
			$this->v_optional_template_settings_array = $t_template_settings_array;
			$this->v_template_presentation_version = $t_template_settings_array['TEMPLATE_PRESENTATION_VERSION'];
		}
		else
		{
			# old templates dont have settings files. use default version (=1.0)
			$this->v_template_presentation_version = 1.0;
		}
	}

	public function get_menubox_status($p_menubox)
	{
		if(isset($_SESSION['style_edit_mode']) && $_SESSION['style_edit_mode'] == 'edit')
		{
			# show all boxes, if style_edit is running
			$t_output = 1;
		}
		else {
			$t_output = $this->v_coo_boxes_master->get_status($p_menubox);
		}
		return $t_output;
	}

	public function get_menubox_position($p_menubox)
	{
		$t_output = $this->v_coo_boxes_master->get_position($p_menubox);
		return $t_output;
	}

	public function get_template_name()
	{
		return $this->v_template_name;
	}

	public function get_template_presentation_version()
	{
		return $this->v_template_presentation_version;
	}
}
?>