<?php
/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  --------------------------------------------------------------
*/

	class GMCSSManager
	{
		private $v_token_id;

		private $v_current_template;

		private  $v_style_area_contents = array(
											'fonts'			=> array(
																'font-family'			=> array('value' => 'inherit',		'isset' => false),
																'font-size'				=> array('value' => 'inherit',		'isset' => false),
																'font-style'			=> array('value' => 'inherit',		'isset' => false),
																'font-weight'			=> array('value' => 'inherit',		'isset' => false),
																'text-decoration'		=> array('value' => 'inherit',		'isset' => false),
																'text-align'			=> array('value' => 'inherit',		'isset' => false),
																'color'					=> array('value' => 'inherit',		'isset' => false)
											),
											'backgrounds'	=> array(
																'background-color'		=> array('value' => 'transparent',	'isset' => false),
																'background-image'		=> array('value' => 'none',			'isset' => false),
																'background-position'	=> array('value' => '0px 0px',		'isset' => false),
																'background-repeat'		=> array('value' => 'repeat',		'isset' => false)
											)
		);
		private $v_style_areas		= array(
											'fonts'			=> false,
											'backgrounds'	=> false,
											'borders'		=> false,
											'dimensions'	=> false
		);

		private $cooMySQLi;

		/**
		 * constructor
		 * @param GMSEDatabase $dbConnection
		 * @param              $p_token
		 */
		public function __construct(GMSEDatabase $dbConnection, $p_token = '')
		{
			$this->cooMySQLi = $dbConnection;
			$this->v_current_template = trim(SE_CURRENT_TEMPLATE);
			if($p_token != '')
			{
				$coo_sec = new GMSESecurity($this->cooMySQLi);

				$this->v_token_id = $coo_sec->get_token_id_by_token($p_token);

				unset($coo_sec);
			}

			return;
		}		
		
		/**
		 * function to save modified css styles
		 * @param        $p_id
		 * @param array  $p_styles
		 * @param string $p_type
		 *
		 * @return json
		 */
		public function save_styles($p_id, $p_styles, $p_type = '')
		{
			if(is_array($p_styles))
			{
				foreach($p_styles as $t_key => $t_val)
				{
					$query = 	"SELECT
									`gm_css_style_content_id`
								AS
									`id`
								FROM
									`gm_css_style_content`
								WHERE
									`gm_css_style_id` = '" . $p_id . "'
								AND
									`style_attribute` = '" . $t_key . "'
								LIMIT 1";
					$result = $this->cooMySQLi->query($query);

					if((int)$result->num_rows > 0)
					{
						if($t_key == 'background-image')
						{
							if(strstr($t_val, SE_CFG_IMAGES_BACKGROUNDS_URL_RELATIVE))
							{		
								$t_val = str_replace(SE_CFG_IMAGES_BACKGROUNDS_URL_RELATIVE, 'backgrounds/', $t_val);
							}
							elseif(strstr($t_val, SE_CFG_IMAGES_GRADIENTS_PATH_RELATIVE))
							{
								$t_val = str_replace(SE_CFG_IMAGES_GRADIENTS_PATH_RELATIVE, 'img/gradients/', $t_val);							
							}
							$t_background_image = $t_val;
						}
						$t_row = $result->fetch_assoc();
						$query = 	"UPDATE
										`gm_css_style_content`
									SET
										`style_value` = '" . $t_val . "'
									WHERE
										`gm_css_style_id` = '" . $p_id . "'
									AND
										`gm_css_style_content_id` = '" . $t_row['id'] . "'
									LIMIT 1";
						$this->cooMySQLi->query($query);
					}
					elseif
					(
						(
								array_key_exists($t_key, $this->v_style_area_contents['fonts']) === true
							||
								array_key_exists($t_key, $this->v_style_area_contents['backgrounds']) === true
							||
								strstr($t_key, 'margin')
							||
								strstr($t_key, 'padding')
							||
								strstr($t_key, 'border')
						) 
						&& (int)$result->num_rows == 0
					)
					{	
						$query = 	"INSERT
										`gm_css_style_content`
									SET
										`style_attribute` = '" . $t_key	. "',
										`style_value` = '" . $t_val	. "',
										`gm_css_style_id` = '" . $p_id	. "'";
						$this->cooMySQLi->query($query);
					}
				}
			}
			else
			{		
			}

			/* clean table from global border styles */			
			$this->border_handling($p_id);

			/* SET HISTORY */
			$p_styles['id']			= $p_id;
			$p_styles['selector']	= $this->get_style_selector_by_id($p_id); 
			
			if($p_type == 'image')
			{
				$p_styles = $this->image_handling($p_styles);
			}
			$this->set_history($p_styles);

			return array('error' => -6, 'file' => $t_background_image);
		}

		/**
		 * function to handle image uploads
		 * @param $p_styles
		 *
		 * @return string|bool
		 */
		private function image_handling($p_styles)
		{				
			$t_styles = $this->get_style_content_by_selector_id($p_styles['id'], $p_styles['selector']);			
			$t_styles['background-image'] = $p_styles['background-image'];
			$this->backup_background_image($t_styles);			
			return $t_styles;
		}

		/**
		 * function to clean table from global border styles
		 * @param $p_id
		 * @return void
		 */
		private function border_handling($p_id)
		{
			$query = 	"DELETE FROM
							`gm_css_style_content`
						WHERE
							`gm_css_style_id` = '" . $p_id . "'
						AND
							(
								`style_attribute` = 'margin'
							OR
								`style_attribute` = 'padding'
							)";
			$this->cooMySQLi->query($query);
		}

		/**
		 * function to load css pseudo classes
		 * @param        $p_selector
		 * @param string $p_active_selector
		 *
		 * @return array|bool
		 */
		private function load_pseudo_classes($p_selector, $p_active_selector = '')
		{
			$t_pseudo_class = $p_selector . ":";

			$query = 	"SELECT
							`gm_css_style_id`,
							`style_name`
						FROM
							`gm_css_style`
						WHERE
							`editable` = '1'
						AND
							`style_name` LIKE '" . $t_pseudo_class . "%'
						AND
							`template_name` = '" . addslashes($this->v_current_template) . "'
						ORDER BY
							`style_name` ASC";
			$result = $this->cooMySQLi->query($query);

			if((int)$result->num_rows > 0)
			{		
				$t_pseudo_classes = array();

				if($p_selector != $p_active_selector)
				{
					$t_pseudo_classes[$p_selector] = FORM_TITLE_PSEUDO_CLASSES_STANDARD;
				}				

				while($t_row = $result->fetch_assoc())
				{
					$t_pseudo_class			= substr(strstr($t_row['style_name'], ':'), 1);
					$t_pseudo_class_text	= constant('FORM_TITLE_PSEUDO_CLASSES_' . strtoupper($t_pseudo_class));

					$t_pseudo_classes[$t_row['style_name']] = $t_pseudo_class_text;
				}	

				return $t_pseudo_classes;
			}
			else
			{
				return false;
			}
		}

		/**
		 * function to load css styles
		 * @param $p_selector
		 * @return	json
		 */
		public function load_json_styles($p_selector)
		{
			$t_id = $this->get_style_id_by_selector($p_selector);
			
			if($t_id != false)
			{
				$t_styles = $this->get_style_content_by_selector_id($t_id, $p_selector);
				
				/* load pseudo classes */
				$t_pos = strpos($p_selector, ':');
				$t_active_selector = $p_selector;
				if($t_pos !== false)
				{
					$t_styles['pseudo_class'] = $p_selector;
					$p_selector = substr($p_selector, 0, $t_pos);
				}				

				$t_pseudo_classes = $this->load_pseudo_classes($p_selector, $t_active_selector);	

				if($t_pseudo_classes !== false)
				{
					$t_styles['pseudo_classes'] = $t_pseudo_classes;
				}				

				/* SET HISTORY */
				$this->set_history($t_styles);				

				if($t_styles != false)
				{
					echo json_encode($t_styles);
				}
			}
		}


		private function _verifyDB($p_current_template)
		{
			$check = $this->cooMySQLi->query('SHOW TABLES LIKE "gm_css_style"');
			if($check->num_rows === 0)
			{
				$this->cooMySQLi->query("CREATE TABLE IF NOT EXISTS `gm_css_style` (
							  `gm_css_style_id` int(11) NOT NULL auto_increment,
							  `template_name` varchar(32) NOT NULL default '',
							  `style_name` varchar(255) NOT NULL default '',
							  `editable` tinyint(4) NOT NULL default '1',
							  `selectors_specificity` varchar(8) NOT NULL,
							  `gm_css_look_id` int(11) unsigned NOT NULL default '1',
							  PRIMARY KEY  (`gm_css_style_id`),
							  KEY `name` (`style_name`),
							  KEY `editable` (`editable`,`template_name`,`selectors_specificity`,`gm_css_style_id`)
							) ENGINE=MyISAM  COMMENT='Gambio StyleEdit INTERFACE TABLE'");

				$this->cooMySQLi->query("CREATE TABLE IF NOT EXISTS `gm_css_style_content` (
							  `gm_css_style_content_id` int(11) NOT NULL auto_increment,
							  `gm_css_style_id` int(11) NOT NULL default '0',
							  `style_attribute` varchar(32) NOT NULL default '',
							  `style_value` text NOT NULL,
							  `editable` tinyint(4) NOT NULL default '1',
							  PRIMARY KEY  (`gm_css_style_content_id`),
							  KEY `gm_css_style_id` (`gm_css_style_id`)
							) ENGINE=MyISAM  COMMENT='Gambio StyleEdit INTERFACE TABLE'");

				$this->cooMySQLi->query("CREATE TABLE IF NOT EXISTS `gm_css_style_fonts` (
							  `gm_css_style_fonts_id` int(10) unsigned NOT NULL auto_increment,
							  `font` varchar(255) NOT NULL,
							  PRIMARY KEY  (`gm_css_style_fonts_id`)
							) ENGINE=MyISAM  COMMENT='Gambio StyleEdit INTERFACE TABLE'");

				$this->cooMySQLi->query("CREATE TABLE IF NOT EXISTS `gm_css_style_history` (
							  `gm_css_style_history_id` int(10) unsigned NOT NULL auto_increment,
							  `gm_css_style_security_id` int(10) unsigned NOT NULL,
							  `gm_css_style_id` int(10) unsigned NOT NULL,
							  `gm_css_history_active` enum('true','false') NOT NULL default 'true',
							  `date` datetime default NULL,
							  PRIMARY KEY  (`gm_css_style_history_id`)
							) ENGINE=MyISAM  COMMENT='Gambio StyleEdit INTERFACE TABLE'");

				$this->cooMySQLi->query("CREATE TABLE IF NOT EXISTS `gm_css_style_history_content` (
							  `gm_css_style_history_content_id` int(10) unsigned NOT NULL auto_increment,
							  `gm_css_style_history_id` int(10) unsigned NOT NULL,
							  `style_attribute` varchar(32) default NULL,
							  `style_value` text,
							  PRIMARY KEY  (`gm_css_style_history_content_id`)
							) ENGINE=MyISAM  COMMENT='Gambio StyleEdit INTERFACE TABLE'");
				
				$this->cooMySQLi->query("REPLACE INTO `gm_css_style_fonts` VALUES(1, 'Arial, Helvetica, Sans-Serif')");
				$this->cooMySQLi->query("REPLACE INTO `gm_css_style_fonts` VALUES(2, 'Courier New, Courier, Monospace')");
				$this->cooMySQLi->query("REPLACE INTO `gm_css_style_fonts` VALUES(3, 'Georgia, Times New Roman, Times, Serif')");
				$this->cooMySQLi->query("REPLACE INTO `gm_css_style_fonts` VALUES(4, 'Tahoma, Arial, Helvetica, Sans-Serif')");
				$this->cooMySQLi->query("REPLACE INTO `gm_css_style_fonts` VALUES(5, 'Times New Roman, Times, Serif')");
				$this->cooMySQLi->query("REPLACE INTO `gm_css_style_fonts` VALUES(6, 'Verdana, Arial, Helvetica, Sans-Serif')");
				
				$t_template_css_filename = basename($p_current_template) . '.css';
				if(file_exists(SE_CURRENT_TEMPLATE_PATH . $t_template_css_filename))
				{
					include_once(SE_CFG_STYLE_EDIT_PATH . 'classes/GMCSS.php');
					
					$coo_import = new GMCSSImport($this->cooMySQLi, $t_template_css_filename, '', 'initialize');

					$t_error = $coo_import->_prepare_import();
					if((int)$t_error == 0)
					{
						$t_error = $coo_import->_import();
						if($t_error == -3)
						{
							if(file_exists(SE_CFG_STYLE_EDIT_FILES_PATH . basename($p_current_template) . '/' . $t_template_css_filename))
							{
								@unlink(SE_CFG_STYLE_EDIT_FILES_PATH . basename($p_current_template) . '/' . $t_template_css_filename);   
							}
							
							$coo_export = new GMCSSExport($t_template_css_filename, $this->cooMySQLi);

							$t_error = $coo_export->_prepare_export();
							if((int)$t_error == 0)
							{
								$coo_export->_export();
							}
						}
					}
				}
			}
		}
		

		/**
		 * function to load all editable css styles
		 * @param $p_current_template
		 * @return	string
		 */
		public function load_styles($p_current_template)
		{		
			$c_current_template = addslashes(trim($p_current_template));
			$coo_sec_token = new GMSESecurity($this->cooMySQLi);
			$coo_sec_token->delete_sec_token();
			unset($coo_sec_token);

			$this->_verifyDB($c_current_template);
			
			$query = 	"SELECT
							`gm_css_style_id`,
							`style_name`
						FROM
							`gm_css_style`
						WHERE
							`editable` = '1'
						AND
							`style_name` NOT LIKE '%:%'
						AND
							`template_name` = '" . $c_current_template . "'
						ORDER BY
							`selectors_specificity` ASC,
							`gm_css_style_id` ASC";
			$result = $this->cooMySQLi->query($query);

			while(($t_row = $result->fetch_assoc())) 
			{
				$contentsQuery = 	"SELECT
										`style_attribute` AS `attribute`,
										`style_value` AS `value`
									FROM
										`gm_css_style_content`
									WHERE
										`gm_css_style_id` = '" . $t_row['gm_css_style_id'] . "'
									AND
										(
											`style_attribute` = 'background-image'
										OR
											`style_attribute` = 'background-color'
										)
									ORDER BY
										`style_attribute`";
				$contentsResult = $this->cooMySQLi->query($contentsQuery);
				
				if((int)$contentsResult->num_rows > 0)
				{
					while($t_row_contents = $contentsResult->fetch_assoc())
					{
						$t_contents[$t_row_contents['attribute']] = $t_row_contents['value'];
					}				
				}	
				
				$t_contents['background-image'] = str_replace('"', '', $t_contents['background-image']);
				$t_contents['background-image'] = str_replace("'", '', $t_contents['background-image']);
				
				echo 'if($(\''. $t_row['style_name'] . '\').length>0){gmslc.bnd(\''. $t_row['style_name'] .'\', "'. $t_contents['background-color'] .'", "'. $t_contents['background-image'] .'");}' . "\n"; 
				unset($t_contents);

				$t_contents = array();
			}
		}

		/**
		 * function to get css style selector by css id
		 * @param int $p_id
		 *
		 * @return string|boolean
		 */
		private function get_style_selector_by_id($p_id)
		{
			$query = 	"SELECT
							`style_name`AS `selector`
						FROM
							`gm_css_style`
						WHERE
							`gm_css_style_id` = '" . $p_id . "'
						LIMIT 1";
			$result = $this->cooMySQLi->query($query);

			if((int)$result->num_rows > 0)
			{
				$t_row = $result->fetch_assoc();

				return $t_row['selector'];
			}
			else
			{
				return false;
			}
		}

		/**
		 * function to get css style id by css selector
		 * @param string $p_selector
		 *
		 * @return string|bool
		 */
		private function get_style_id_by_selector($p_selector)
		{
			$query = 	"SELECT
							`gm_css_style_id` AS `id`
						FROM
							`gm_css_style`
						WHERE
							`style_name` = '" . $p_selector . "'
						AND
							`template_name` = '" . addslashes($this->v_current_template) . "'
						LIMIT 1";
			$result = $this->cooMySQLi->query($query);

			if((int)$result->num_rows > 0)
			{
				$t_row = $result->fetch_assoc();

				return $t_row['id'];
			}
			else
			{
				return false;
			}
		}

		/**
		 * function to get style content by css selector id
		 * @param string $p_id
		 * @param string $p_selector
		 *
		 * @return array|bool
		 */
		private function get_style_content_by_selector_id($p_id, $p_selector)
		{
			$query = 	"SELECT
							`style_attribute`AS `attribute`,
							`style_value` AS `value`
						FROM
							`gm_css_style_content`
						WHERE
							`gm_css_style_id` = '" . $p_id . "'
						ORDER BY
							`style_attribute`";
			$result = $this->cooMySQLi->query($query);

			if((int)$result->num_rows > 0)
			{
				$t_contents['id']		= $p_id;
				
				$t_contents['selector']	= $p_selector;
				
				$t_pos = strpos($p_selector, ':');
				if($t_pos !== false)
				{
					$t_contents['selector'] = substr($p_selector, 0, $t_pos);
				}				

				while($t_row = $result->fetch_assoc())
				{
					$t_contents[$t_row['attribute']] = $t_row['value'];
					
					/* check if style area "borders" & "dimensions" isset */
					if(strstr(strtolower($t_row['attribute']), 'border') !== false)
					{
						$this->v_style_areas['borders'] = true;
					}
					elseif
					(
							strstr(strtolower($t_row['attribute']), 'margin')	!== false
						||
							strstr(strtolower($t_row['attribute']), 'padding')	!== false
						||
							strstr(strtolower($t_row['attribute']), 'width')	!== false
						||
							strstr(strtolower($t_row['attribute']), 'height')	!== false
					)
					{
						$this->v_style_areas['dimensions'] = true;
					}

					/* complete style areas */
					foreach($this->v_style_area_contents as $t_area => $t_area_val)
					{
						if(array_key_exists(strtolower($t_row['attribute']), $this->v_style_area_contents[$t_area]))
						{					
							$this->v_style_areas[$t_area] = true;
							$this->v_style_area_contents[$t_area][$t_row['attribute']]['isset'] = true;
						}
					}
				}

				/* complete style areas */
				$t_contents = $this->complete_style_areas($t_contents);
				
				$t_contents['areas'] = $this->v_style_areas;
				
				return $t_contents;
			}
			else
			{
				return false;
			}
		}

		/**
		 * function to get fonts
		 * @param string $p_contents
		 *
		 * @return array|mixed
		 */
		private function complete_style_areas($p_contents)
		{
			foreach($this->v_style_area_contents as $t_area => $t_area_val)
			{
				if($this->v_style_areas[$t_area] === true)
				{
					foreach($this->v_style_area_contents[$t_area] as $t_key => $t_val)
					{
						if($t_val['isset'] === false)
						{
							$p_contents[$t_key] = $t_val['value'];
						}
					}
				}
			}
			return $p_contents;
		}

		/*
		*	function to get fonts
		*	@param	string	$p_font
		*	@return	array
		*/
		/**
		 * function to get fonts
		 * @param string $p_font
		 *
		 * @return array|bool
		 */
		public function get_fonts($p_font)
		{
			$query = 	"SELECT
							`gm_css_style_fonts_id` AS `id`,
							`font`
						FROM
							`gm_css_style_fonts`";
			$result = $this->cooMySQLi->query($query);
			
			if((int)$result->num_rows > 0)
			{
				$t_font_styles = '';
				$t_font_style = '<select id="font-family" class="se_input_box" style="height:18px;width:80px">';
				while($t_row = $result->fetch_assoc())
				{	
					if($t_row['font'] == $p_font)
					{
						$t_font			= '<option title="' . $t_row['font'] . '" selected="selected" value="' . $t_row['font'] . '" >' . $t_row['font'] . '</option>';
					}
					else
					{
						$t_font			= '<option title="' . $t_row['font'] . '" value="' . $t_row['font'] . '" >' . $t_row['font'] . '</option>';
					}
					$t_font_styles .= $t_font;
				}
				
				$t_font_style .= $t_font_styles . '</select>';

				echo $t_font_style;
			}
			else
			{
				return false;
			}
		}

		/**
		 * function to get gradient images
		 * @param string $p_gradient_image
		 * @return void
		 */
		public function get_gradient_images($p_gradient_image)
		{
			$t_gradient_dir = SE_CFG_IMAGES_GRADIENTS_PATH;

			if(is_dir($t_gradient_dir)) 
			{
				$t_gradients = '';
				$t_resource = opendir($t_gradient_dir);
			
				if($t_resource != false) 
				{
					while (($t_file = readdir($t_resource)) != false) 
					{
						if($t_file != '.' && $t_file != '..' && strstr($t_file, '.png'))
						{
							$t_gradients_name = explode('.', $t_file);

							$t_gradients_url	= 'url(' . SE_CFG_IMAGES_GRADIENTS_PATH_RELATIVE . $t_file . ')';

							if($t_file == $this->get_plain_background_image($p_gradient_image))
							{
								$t_selected = 'selected="selected"';
							}

							$t_gradients .= '<option title="' . $t_gradients_name[0] . '" ' . $t_selected . ' value="' . $t_gradients_url . '" >' . $t_gradients_name[0] . '</option>';							
							
						}
					}

					closedir($t_resource);

					$t_gradient_images = '
					<select id="background_gradient" class="se_input_box">
					<option title="' . FORM_TITLE_BACKGROUND_GRADIENT . '" value="0" >'	. FORM_TITLE_BACKGROUND_GRADIENT		. '</option>
					<option title="' . FORM_TITLE_BACKGROUND_NO_GRADIENT . '" value="">' . FORM_TITLE_BACKGROUND_NO_GRADIENT	. '</option>';				

					$t_gradient_images = $t_gradient_images . $t_gradients . '</select>';

					echo $t_gradient_images;
				}
			}	
			else
			{
				echo 0;
			}
		}

		/**
		 * function to get the css background image
		 * @param int $p_id
		 * @param int $p_backup
		 *
		 * @return string|bool|mixed
		 */
		public function get_background_image($p_id, $p_backup=0)
		{
			if(strstr($p_backup, SE_CFG_BACKUP_IMAGE_PREFIX))
			{						
				$t_filename				= $this->get_plain_background_image($p_backup);
				$t_filename				= str_replace(SE_CFG_BACKUP_IMAGE_PREFIX, '', $t_filename);
				$t_style_history_pos	= strpos($t_filename, '_');
				$t_style_history_id		= trim(substr($t_filename, 0, $t_style_history_pos));

				$query = 	"SELECT
								`style_value` AS `value`
							FROM
								`gm_css_style_history_content`
							WHERE
								`gm_css_style_history_id` = '" . (int)$t_style_history_id . "'
							AND
								`style_attribute` = 'background-image'
							LIMIT 1";
				$result = $this->cooMySQLi->query($query);
			}
			else
			{
				$query = 	"SELECT
								`style_value` AS `value`
							FROM
								`gm_css_style_content`
							WHERE
								`gm_css_style_id` = '" . (int)$p_id . "'
							AND
								`style_attribute` = 'background-image'
							LIMIT 1";
				$result = $this->cooMySQLi->query($query);
			}

			if((int)$result->num_rows > 0)
			{
				$t_row		= $result->fetch_assoc();
				$t_filename =  $this->get_plain_background_image($t_row['value']);				
				if(!empty($t_filename) && file_exists(SE_CFG_IMAGES_BACKGROUNDS_PATH . $t_filename))
				{
					return $t_filename;
				}
				elseif(strstr($t_row['value'], SE_CFG_IMAGES_GRADIENTS_PATH_CSS))
				{					
					$t_filename =  $this->get_plain_background_image($t_row['value']);	

					if(!empty($t_filename) && file_exists(SE_CFG_IMAGES_GRADIENTS_PATH . $t_filename))
					{
						return $t_filename;
					}
					else
					{
						return false;
					}					
				}
				else
				{
					return false;
				}
			}
		}

		/**
		 * function to backup the background image
		 * @param string $p_styles
		 */
		private function backup_background_image($p_styles)
		{
			$query = 	"SELECT
							`gm_css_style_history_id` AS `id`
						FROM
							`gm_css_style_history`
						WHERE
							`gm_css_style_id` = '" . $p_styles['id'] . "'
						AND
							`gm_css_style_security_id` = '" . $this->v_token_id . "'";
			$result = $this->cooMySQLi->query($query);

			if($result->num_rows > 0)
			{
				while($t_row = $result->fetch_assoc())
				{
					$fileQuery = 	"SELECT
										`style_value` AS `value`
									FROM
										`gm_css_style_history_content`
									WHERE
										`gm_css_style_history_id` = '" . (int)$t_row['id'] . "'
									AND
										`style_attribute` = 'background-image'
									LIMIT 1";
					$fileResult = $this->cooMySQLi->query($fileQuery);

					if($fileResult->num_rows > 0)
					{
						$fileUpdateQuery = 	"UPDATE
													`gm_css_style_history_content`
												SET
													`style_value` = '" . $p_styles['background-image']	. "'
												WHERE
													`gm_css_style_history_id` = '" . (int)$t_row['id'] . "'
												AND
													`style_attribute` = 'background-image'";
						$this->cooMySQLi->query($fileUpdateQuery);

						$t_file_row				= $fileResult->fetch_assoc();
						$t_file					= $this->get_plain_background_image($t_file_row['value']);

						if(file_exists(SE_CFG_IMAGES_BACKGROUNDS_PATH . $t_file))
						{
							//unlink(SE_CFG_IMAGES_BACKGROUNDS_PATH . $t_file);
						}
					}
				}			
			}		
			return;
		}

		/**
		 * function to delete background images
		 * @param string $p_filename
		 *
		 * @return array
		 */
		public function delete_background_image($p_filename)
		{
			$t_filename = rawurldecode(basename($p_filename));
			
			if(file_exists(SE_CFG_IMAGES_BACKGROUNDS_PATH . $t_filename) && is_file(SE_CFG_IMAGES_BACKGROUNDS_PATH . $t_filename))
			{
				$query = 	"SELECT
								`h`.`gm_css_style_history_id` AS `id`
							FROM
								`gm_css_style_history` `h`,
								`gm_css_style_content` `s`
							WHERE
								`h`.`gm_css_style_id` = `s`.`gm_css_style_id`
							AND
								`s`.`style_value` LIKE 'url(%backgrounds/" . $this->cooMySQLi->real_escape_string($t_filename) . "%)'
							AND
								`gm_css_style_security_id` = '" . $this->v_token_id . "'";
				$result = $this->cooMySQLi->query($query);

				while($t_row = $result->fetch_assoc())
				{
					$query = 	"UPDATE
									`gm_css_style_history_content`
								SET
									`style_value` = 'none'
								WHERE
									`gm_css_style_history_id` = '" . $t_row['id'] . "'
								AND
									`style_attribute` = 'background-image'";
					$result = $this->cooMySQLi->query($query);
				}

				$sql = 	"SELECT
							`sc`.`gm_css_style_content_id`
						FROM
							`gm_css_style` `s`,
							`gm_css_style_content` `sc`
						WHERE
							`s`.`template_name` = '" . $this->cooMySQLi->real_escape_string($this->v_current_template) . "'
						AND
							`s`.`gm_css_style_id` = `sc`.`gm_css_style_id`
						AND
							`sc`.`style_value` LIKE 'url(%backgrounds/" . $this->cooMySQLi->real_escape_string($t_filename) . "%)'
						AND
							`sc`.`style_attribute` = 'background-image'";
				$sqlResult = $this->cooMySQLi->query($sql);
				
				while($t_result_array = $sqlResult->fetch_assoc())
				{
					$query = 	"UPDATE
									`gm_css_style_content`
								SET
									`style_value` = 'none'
								WHERE
									`gm_css_style_content_id` = '" . (int)$t_result_array['gm_css_style_content_id'] . "'";
					$this->cooMySQLi->query($query);
				}

				unlink(SE_CFG_IMAGES_BACKGROUNDS_PATH . $t_filename);

				return array(
								'error'		=> -4, 
								'file_id'	=> $t_bck_file
				);
			}
			else
			{				
				return array(
								'error'		=> 10
				);
			}		
		}

		/**
		 * function to get the plain filename of a css background image
		 * @param  string $p_file
		 *
		 * @return string|mixed
		 */
		private function get_plain_background_image($p_file)
		{
			$t_file		= explode('/', $p_file);
			$t_filename = $t_file[count($t_file)-1];
			$t_filename = str_replace("'", "",	$t_filename);
			$t_filename = str_replace('"', '',	$t_filename);
			$t_filename = str_replace(')', '',	$t_filename);
			$t_filename = str_replace('(', '',	$t_filename);
			$t_filename = str_replace("\\", "",	$t_filename);
			
			return $t_filename;
		}

		/**
		 * function to set the history
		 * @param string $p_styles
		 * @return	string/boolean
		 */
		private function set_history($p_styles)
		{
			/* clean history remove all rows after the actual active history id */
			$t_active_history_id = $this->get_active_history_id();

			if($t_active_history_id != false)
			{
				$this->clean_history($t_active_history_id);
			}

			/* set all rows inactive */
			$query = 	"UPDATE
							`gm_css_style_history`
						SET
							`gm_css_history_active` = 'false'
						WHERE
							`gm_css_style_security_id` = '" . $this->v_token_id . "'";
			$this->cooMySQLi->query($query);

			/* insert new history item */
			$query = 	"INSERT INTO
							`gm_css_style_history`
						SET
							`gm_css_style_id` = '" . $p_styles['id'] . "',
							`gm_css_history_active` = 'true',
							`gm_css_style_security_id` = '" . $this->v_token_id . "',
							`date` = NOW()";
			$this->cooMySQLi->query($query);

			$query = 	"SELECT
							`gm_css_style_history_id` AS `id`
						FROM
							`gm_css_style_history`
						WHERE
							`gm_css_style_security_id` = '" . $this->v_token_id . "'
						ORDER BY
							`gm_css_style_history_id` DESC
						LIMIT 1";
			$result = $this->cooMySQLi->query($query);

			$t_row = $this->cooMySQLi->fetch_array($result);
			
			unset($p_styles['selector']);
			unset($p_styles['id']);
			
			if(is_array($p_styles))
			{
				/* insert new history items */
				foreach($p_styles as $t_key => $t_val)
				{
					$query = 	"INSERT INTO
									`gm_css_style_history_content`
								SET
									`gm_css_style_history_id` = '" . $t_row['id'] . "',
									`style_attribute` = '" . $t_key . "',
									`style_value` = '" . $t_val . "'";
					$this->cooMySQLi->query($query);
				}
			}
		}

		/*
		*	function to remove all rows after the actual active history id
		*	@param int $p_history_id
		*	@return	string/boolean
		*/
		private function clean_history($p_history_id)
		{
			$history_query = 	"SELECT
									`gm_css_style_history_id` AS `id`
								FROM
									`gm_css_style_history`
								WHERE
									`gm_css_style_history_id` > '" . $p_history_id . "'
								AND
									`gm_css_style_security_id` = '" . $this->v_token_id . "'";
			$history_result = $this->cooMySQLi->query($history_query);
			
			if($history_result->num_rows > 0)
			{
				while($t_row_history = $history_result->fetch_assoc())
				{
					/* delete old images */
					$query = 	"SELECT
									`style_value` AS `value`
								FROM
									`gm_css_style_history_content`
								WHERE
									`gm_css_style_history_id` > '" . $t_row_history['id'] . "'
								AND
									`style_attribute` = 'background-image'
								AND
									`style_value` != 'url()'
								AND
									`style_value` != 'none'";
					$result = $this->cooMySQLi->query($query);

					if($result->num_rows > 0)
					{
						while($t_row = $result->fetch_assoc())
						{
							$t_file	= $this->get_plain_background_image($t_row['value']);

							if(file_exists(SE_CFG_IMAGES_BACKGROUNDS_PATH . $t_file))
							{
								//unlink(SE_CFG_IMAGES_BACKGROUNDS_PATH . $t_file);
							}
						}
					}

					$query = 	"DELETE FROM
									`gm_css_style_history_content`
								WHERE
									`gm_css_style_history_id` = '" . $t_row_history['id'] . "'";
					$this->cooMySQLi->query($query);

					$query = 	"DELETE FROM
									`gm_css_style_history`
								WHERE
									`gm_css_style_history_id` = '" . $t_row_history['id']  . "'";
					$this->cooMySQLi->query($query);
				}
			}
		}

		/**
		 * function to load css styles out of history
		 * @param  string $p_history_type
		 *
		 * @return json|bool
		 */
		public function load_json_history($p_history_type)
		{
			$t_styles = $this->load_history($p_history_type);

			if($t_styles != false)
			{
				echo json_encode($t_styles);
			}
			else
			{
				return false;
			}
		}

		/**
		 * @param string $p_history_type
		 *
		 * @return array|bool
		 */
		private function load_history($p_history_type)
		{
			/* change sql statement by type */
			if($p_history_type == 'forward')
			{
				$t_sql		= ' >';
				$t_sql_sort	= ' ASC';
			}
			else if($p_history_type == 'backward')
			{
				$t_sql		= ' <';
				$t_sql_sort	= ' DESC';
			}

			/* get active history id */
			$t_active_history_id = $this->get_active_history_id();			

			/* load styles */
			$t_contents = array();

			$query = 	"SELECT
							`gm_css_style_history_id` AS `h_id`,
							`gm_css_style_id` AS `id`
						FROM
							`gm_css_style_history`
						WHERE
							`gm_css_style_history_id` " . $t_sql . " '" . $t_active_history_id . "'
						AND
							`gm_css_style_security_id` = '" . $this->v_token_id . "'
						ORDER BY
							`gm_css_style_history_id` " . $t_sql_sort . "
						LIMIT 1";
			$result = $this->cooMySQLi->query($query);
			
			if($this->cooMySQLi->num_rows($result) > 0)
			{
				$t_history			= $this->cooMySQLi->fetch_array($result);
				$t_history_id		= $t_history['h_id'];					
				$t_contents['id']	= $t_history['id'];
				
				$query = 	"SELECT
								`style_attribute` AS `attribute`,
								`style_value` AS `value`
							FROM
								`gm_css_style_history_content`
							WHERE
								`gm_css_style_history_id` = '" . $t_history_id . "'";
				$result = $this->cooMySQLi->query($query);

				if($this->cooMySQLi->num_rows($result) > 0)
				{

					while($t_row = $result->fetch_assoc())
					{
						$t_contents[$t_row['attribute']]	= $t_row['value'];
					}	

					$t_contents['selector'] = $this->get_style_selector_by_id($t_contents['id']);
					
					/* set active row inactive */
					$query = 	"UPDATE
									`gm_css_style_history`
								SET
									`gm_css_history_active` = 'false'
								WHERE
									`gm_css_style_security_id` = '" . $this->v_token_id . "'";
					$this->cooMySQLi->query($query);

					/* set actual loaded row active */
					$query = 	"UPDATE
									`gm_css_style_history`
								SET
									`gm_css_history_active` = 'true'
								WHERE
									`gm_css_style_history_id` = '" . $t_history_id . "'
								AND
									`gm_css_style_security_id` = '" . $this->v_token_id . "'";
					$this->cooMySQLi->query($query);

					/* count remaining history items */
					$t_count = $this->count_history_elements($t_history_id, $t_sql); 

					$t_contents['count'] = $t_count;

					return $t_contents;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}			
		}

		/**
		 * function to count remaining history items
		 * @param string $p_history_id
		 * @param string $p_sql
		 *
		 * @return int
		 */
		private function count_history_elements($p_history_id, $p_sql)
		{
			$query = 	"SELECT
							count(*) AS `count`
						FROM
							`gm_css_style_history`
						WHERE
							`gm_css_style_history_id` " . $p_sql . " '" . $p_history_id . "'
						AND
							`gm_css_style_security_id` = '" . $this->v_token_id . "'";
			$result = $this->cooMySQLi->query($query);
			
			if($result->num_rows > 0)
			{
				$t_count = $result->fetch_assoc();
				return $t_count['count'];
			}
			else
			{
				return 0;
			}
		}

		/**
		 * function to get the actual active history id
		 * @return string|bool
		 */
		private function get_active_history_id()
		{
			$query = 	"SELECT
							`gm_css_style_history_id` AS `id`
						FROM
							`gm_css_style_history`
						WHERE
							`gm_css_history_active` = 'true'
						AND
							`gm_css_style_security_id` = '" . $this->v_token_id . "'";
			$result = $this->cooMySQLi->query($query);
			
			if($result->num_rows > 0)
			{				
				$t_history_id = $result->fetch_assoc();
				return $t_history_id['id'];
			}
			else
			{
				return false;
			}
		}
	}