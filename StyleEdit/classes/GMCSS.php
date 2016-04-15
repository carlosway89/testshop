<?php
/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  --------------------------------------------------------------
*/
	/*
	*	this class provides functions for opening, reading and writing files 
	*/
	class GMCSS
	{
		/**
		 * @var string
		 */
		private $v_type;
		
		/**
		 * @var string
		 */
		private $v_current_template;
		
		/**
		 * @var string
		 */
		private $v_css_file;
		
		/**
		 * @var string
		 */
		private $v_css_file_dir;
		
		/**
		 * @var string
		 */
		private $v_css_filename;
		
		/**
		 * @var string
		 */
		private $v_css_file_pointer;

		/**
		 * @var string
		 */
		private $v_css_se_files_dir;

		/**
		 * @var string
		 */
		private $v_css_se_img_dir_absolute;

		/**
		 * @var string
		 */
		private $v_css_se_img_dir_relative	= 'backgrounds/';
		private $cooMySQLi;

		/**
		 * constructor
		 * @param string       $p_css_file
		 * @param GMSEDatabase $dbConnection
		 * @param string       $p_type
		 */
		public function __construct($p_css_file, GMSEDatabase $dbConnection, $p_type='archive')
		{
			$this->v_current_template = trim(SE_CURRENT_TEMPLATE);

			switch($p_type)
			{
				case 'archive':
					$this->v_css_se_files_dir		= SE_CFG_STYLE_EDIT_FILES_PATH . $this->v_current_template . "/";
					break;
				case 'initialize':
					$this->v_css_se_files_dir		= SE_CURRENT_TEMPLATE_PATH;
					break;
				default:
					$this->v_css_se_files_dir		= SE_CFG_IMAGES_BACKGROUNDS_PATH;
			}
		
			$this->v_css_se_img_dir_absolute	= SE_CFG_IMAGES_BACKGROUNDS_URL_RELATIVE;
			$this->v_type						= $p_type;
			$this->v_css_file					= $this->v_css_se_files_dir	. $p_css_file;
			$this->v_css_filename				= $p_css_file;
			$this->cooMySQLi                    = $dbConnection;
		}


		/**
		 * @return string
		 */
		protected function get_current_template()
		{
			return $this->v_current_template;
		}


		/**
		 * @return string
		 */
		public function get_css_file()
		{
			return $this->v_css_file;
		}

		/**
		 * @return string
		 */
		public function get_css_filename()
		{
			return $this->v_css_filename;
		}

	   /**
		*	@return string
		*/
		public function get_css_filepath()
		{
			return $this->v_css_se_img_dir_absolute . $this->v_css_filename;
		}

		/**
		 * @return string
		 */
		public function get_css_image_path()
		{
			return $this->v_css_se_img_dir_relative . $this->v_css_filename;
		}

		/**
		 * @param string $p_filename
		 *
		 * @return string
		 */
		public function _clean_css_filename($p_filename)
		{
			$t_search	= "ÁáÉéÍíÓóÚúÇçÃãÀàÂâÊêÎîÔôÕõÛû&ŠŽšžŸÀÁÂÃÅÇÈÉÊËÌÍÎÏÑÒÓÔÕØÙÚÛÝàáâãåçèéêëìíîïñòóôõøùúûýÿ ";
			$t_replace	= "AaEeIiOoUuCcAaAaAaEeIiOoOoUueSZszYAAAAACEEEEIIIINOOOOOUUUYaaaaaceeeeiiiinooooouuuyy_";
			$t_replarray= array('ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss');
			$t_filename = strtolower(strtr($p_filename , $t_search, $t_replace));
			$t_filename = strtr($t_filename , $t_replarray);
			$t_filename = preg_replace("/[^a-zA-Z0-9\\.\\-\\_]/i", '', $t_filename );
			return strtolower($t_filename);
		}

		/**
		 * @return bool
		 */
		protected function _check_css_filename()
		{
			if(!empty($this->v_css_filename) && $this->_check_filetyp() == true)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * @return bool
		 */
		protected function _css_file_exists()
		{
			if(file_exists($this->v_css_file))
			{
				return true;
			}
			else
			{
				return false;
			}
		}


		/**
		 * @return bool
		 */
		protected function _css_dir_perms()
		{
			if (@is_writable($this->v_css_se_files_dir))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * @param string $p_mode
		 * <p>-> 'w+' (export) to create a new css file & for reading and writing;<br/>
		 *	-> 'r+' (import) for reading and writing an existing css file;</p>
		 *
		 * @return bool
		 */
		protected function _open_css_file($p_mode)
		{
			$this->v_css_file_pointer = fopen($this->v_css_file, $p_mode);

			if ($this->v_css_file_pointer != false) 
			{
				return true;
			}
			else
			{
				return false;
			}
		}


		/**
		 * @param $p_line
		 */
		protected function _write_css_file($p_line)
		{
			fwrite($this->v_css_file_pointer, $p_line);
		}	

		/**
		 * @return string 
		 */
		protected function _read_css_file()
		{
			$t_file_content = fread($this->v_css_file_pointer, filesize($this->v_css_file));

			return $t_file_content;
		}	

		protected function _close_css_file()
		{
			fclose($this->v_css_file_pointer);		
		}	

		/**
		 * read import/export dir
		 * @return array|bool
		 */
		protected function _get_dir_content()
		{
			$t_files = array();
			if(is_dir($this->v_css_se_files_dir)) 
			{				
				$t_resource = opendir($this->v_css_se_files_dir);
				if($t_resource != false) 
				{
					while (($t_file = readdir($t_resource)) != false) 
					{
						if($t_file != '.' && $t_file != '..' && strstr($t_file, '.css'))
						{
							$t_files[] = $t_file; 
						}
					}
					closedir($t_resource);
					sort($t_files);
					return $t_files;
				}
			}	
			else
			{
				return false;
			}
		}	

		/**
		 * @param string $p_file_type
		 *
		 * @return bool
		 */
		protected function _check_filetyp($p_file_type = '')
		{
			if($this->v_type == 'archive')
			{
				$t_filename	= explode('.', $this->v_css_filename);
				$t_suffix	= array_pop($t_filename);
				
				if($t_suffix == 'css')
				{
					return true;
				}	
				else
				{
					return false;
				}
			}
			else
			{			
				if(strstr($p_file_type, 'image/') == true)
				{ 
					return true; 
				} 
				else
				{
					return false;
				}	
			}
		}

		/**
		 * @return int
		 */
		public function _delete_file()
		{
			if(@unlink($this->v_css_file))
			{
				return -4;
			}
			else
			{
				return 10;
			}			
		}

		/**
		 * @return int
		 */
		public function _count_files()
		{
			$t_files = $this->_get_dir_content();

			return count($t_files);
		}

		/**
		 * get selector's specificity (id_count-class_count-tag_count)
		 * @param $p_selector
		 *
		 * @return string
		 */
		protected function get_selectors_specificity($p_selector)
		{
			$t_selectors_specificity = '';
			$t_selector = trim($p_selector);
			$t_selectors_specificity .= substr_count($t_selector, '#') . '-';
			$t_selectors_specificity .= substr_count($t_selector, '.') . '-';

			$t_tags_count = 0;
			if(substr($t_selector, 0, 1) != '.' && substr($t_selector, 0, 1) != '#')
			{
				$t_tags_count++;
			}

			preg_match_all('/\s+[^.#\s]{1}/', $t_selector, $t_matches_array);
			if(isset($t_matches_array[0]))
			{
				$t_tags_count += count($t_matches_array[0]);
			}

			$t_selectors_specificity .= $t_tags_count;
			
			return $t_selectors_specificity;
		}
	}


	/*
	*	this class provides several functions to import css files into a given database structure
	*/
	class GMCSSImport extends GMCSS
	{
		private $v_css_content;
		private $v_css_selector;
		private $v_import_mode;
		private $v_fopen_mode = 'r';
		private $cooMySQLi;


		/**
		 * @param GMSEDatabase $dbConnection
		 * @param bool         $p_css_file
		 * @param string       $p_import_mode
		 */
		public function __construct(GMSEDatabase $dbConnection, $p_css_file = false, $p_import_mode = '', $p_type = 'archive')
		{
			$this->v_import_mode = $p_import_mode;
			$this->cooMySQLi = $dbConnection;

			parent::__construct($p_css_file, $this->cooMySQLi, $p_type);
		}


		/**
		 * @param string $p_css_content
		 */
		public function set_css_content($p_css_content)
		{
			$this->v_css_content = $p_css_content;
		}


		/**
		 * @return mixed
		 */
		private function get_css_content()
		{
			return $this->v_css_content;
		}

		/**
		 * @param string $p_css_selector
		 */
		public function set_css_selector($p_css_selector)
		{
			$this->v_css_selector = $p_css_selector;
		}

		/**
		 * @return mixed
		 */
		public function get_css_selector()
		{
			return $this->v_css_selector;
		}

		/**
		 * <p>prepare import<br/>
		 *	-> check if file exists<br/>
		 *	-> open/read file/contents</p>
		 * <p>Returns:<br/>-> 0 no error<br/>
		 *	-> 1 file already exists<br/>
		 *	-> 2 file does not exist<br/>
		 *	-> 3 dirperms incorrect<br/>
		 *	-> 4 fileperms incorrect<br/>
		 *	-> 5 cannot open file<br/>
		 *	-> 6 wrong filetype - *.css only</p>
		 * @return int
		 */
		public function _prepare_import()
		{
			if(parent::_css_file_exists())
			{
				if(parent::_open_css_file($this->v_fopen_mode))
				{
					$this->set_css_content(parent::_read_css_file());

					/* prepare tables */
					$this->_delete_styles();

					return 0;
				}
				else
				{
					return 5;
				}
			}
			else
			{
				return 2;
			}
		}

		/**
		 * prepare database tables
		 * @param bool $p_mode_single_refresh
		 *
		 * @return bool
		 */
		public function _delete_styles($p_mode_single_refresh = false)
		{
			/* switch to single mode - delete actual style */
			if($this->v_import_mode == 'single')
			{
				$t_gm_css_style_id = $this->_get_gm_css_style_id($this->v_css_selector);

				if($t_gm_css_style_id !== false)
				{
					$t_sql = " AND gm_css_style_id = '" . (int)$t_gm_css_style_id . "'";
				}
				else
				{
					return false;
				}
			}

			/* prepare tables */
			$query = 	"SELECT
							`gm_css_style_id` AS `id`
						FROM
							`gm_css_style`
						WHERE
							`template_name` = '" . addslashes(parent::get_current_template()) . "'" . $t_sql;
			$result = $this->cooMySQLi->query($query);

			if((int)$this->cooMySQLi->num_rows($result) > 0)
			{
				while($t_row = $this->cooMySQLi->fetch_array($result))
				{
					$query = 	"DELETE FROM
									`gm_css_style_content`
								WHERE
									`gm_css_style_id` = '" . (int)$t_row['id'] . "'";
					$this->cooMySQLi->query($query);
					
					if($p_mode_single_refresh === false)
					{
						$query = 	"DELETE FROM
										`gm_css_style`
									WHERE
										`gm_css_style_id` = '" . (int)$t_row['id'] . "'";
						$this->cooMySQLi->query($query);
					}
				}
			}
			return;
		}

		/**
		 * @return int
		 */
		public function _import()
		{
			$t_array_styles	= $this->_filter_selector();

			/*
			*	quit single mode if there are more styles
			*/
			if(count($t_array_styles) > 1)
			{
				$this->v_import_mode = '';
			}

			foreach($t_array_styles as $t_selector => $t_attr_val)
			{				
				/* 
				*	handle comma separated selectors, 
				*/
				if(strstr($t_selector, ",") !== false)
				{
					$t_selector_array = array();

					$t_selector_array = explode(',', $t_selector);

					for($i = 0; $i < count($t_selector_array); $i++)
					{
						$this->_set_styles($t_selector_array[$i], $t_attr_val);
					}
				}
				/* 
				*	handle single selectors 
				*/
				else
				{
					$this->_set_styles($t_selector, $t_attr_val);
				}
			}			

			$query = 	"UPDATE
							`gm_css_style`
						SET
							`style_name` = CONCAT('.wrap_shop ', style_name)
						WHERE
							`style_name`
						LIKE
							'.ui%'";
			$this->cooMySQLi->query($query);

			$query = 	"SELECT
							`gm_css_style_id`,
							`style_name`
						FROM
							`gm_css_style`
						WHERE
							`style_name` LIKE '.ui%'";
			$result = $this->cooMySQLi->query($query);
			while($t_result_array = $this->cooMySQLi->fetch_array($result))
			{
				$query = 	"UPDATE
								`gm_css_style`
							SET
								`selectors_specificity` = '" . $this->cooMySQLi->real_escape_string($this->get_selectors_specificity($t_result_array['style_name'])) . "'
							WHERE
								`gm_css_style_id` = '" . (int)$t_result_array['gm_css_style_id'] . "'";
				$this->cooMySQLi->query($query);
			}

			return -3;
		}

		/**
		 * @return array
		 */
		private function _filter_selector()
		{
			$t_array_styles		= array();

			$t_content			= preg_replace('!(/\*.*?\*/)!s', '', $this->get_css_content());

			$t_array_content	= explode('}', $t_content);

			foreach($t_array_content as $t_style)
			{
				$t_array_style_tmp	= explode('{', $t_style);
				$t_selector			= trim($t_array_style_tmp[0]);				
				$t_attributes		= $this->_filter_attr_val($t_array_style_tmp[1]);
				$t_array_styles		= array_merge($t_array_styles, array($t_selector => $t_attributes));
			}

			/* remove last empty element */
			array_pop($t_array_styles);

			return $t_array_styles;
		}

		/**
		 * filter attributes and attributes values
		 * @param string $p_attr_val
		 *
		 * @return array
		 */
		private function _filter_attr_val($p_attr_val)
		{
			$t_array_attr_val	= array();
			$t_attr_val			= trim($p_attr_val);
			
			/* repair invalid syntax */
			// add missing semicolons
			$t_attr_val = preg_replace('/([^;\s])(\s+\S+)\s*:/', '$1;$2:', $t_attr_val);
			// remove whitespaces
			$t_attr_val = preg_replace('/[^\S ]+/', '', $t_attr_val);
			// remove double spaces
			$t_attr_val = preg_replace('/\s\s+/', ' ', $t_attr_val);

			$t_array_attr 		= explode(';', $t_attr_val);

			foreach($t_array_attr as $t_line) 
			{
				$t_array_attr_val_tmp	= explode(':', $t_line);
				$t_attr					= trim($t_array_attr_val_tmp[0]);
				$t_val					= trim($t_array_attr_val_tmp[1]);
				$t_array_attr_val		= array_merge($t_array_attr_val, array($t_attr => $t_val));
			}

			/* remove last empty element */
			if(empty($t_array_attr_val[count($t_array_attr_val)-1]))
			{
				array_pop($t_array_attr_val);
			}
			
			return $t_array_attr_val;
		}
	
		/**
		 * @param $p_selector
		 * @param $p_attr_val
		 */
		private function _set_styles($p_selector, $p_attr_val)
		{
			/* 
			*	check if a selector already is set in db 
			*	get gm_css_style_id 
			*/
			$t_gm_css_style_id = $this->_get_gm_css_style_id($p_selector);

			/* insert: gm_css_style_id does not exist in db */
			if($t_gm_css_style_id === false && $this->v_import_mode != 'single')
			{
				$t_gm_css_style_id = $this->_insert_gm_css_style($p_selector);
			}
			elseif($t_gm_css_style_id === false && $this->v_import_mode == 'single')
			{
				$t_gm_css_style_id = $this->_get_gm_css_style_id($this->v_css_selector);				
			}

			/* update: gm_css_style_id exists in db - but selector has been renamed */
			if($this->v_css_selector != $p_selector && $this->v_import_mode == 'single')
			{			
				$this->_update_gm_css_style($t_gm_css_style_id, $p_selector);	
			}

			$this->_insert_gm_css_style_contents($t_gm_css_style_id, $p_attr_val);
		}

		/**
		 * @param int   $p_gm_css_style_id
		 * @param array $p_attr_val
		 * 
		 * @return void
		 */
		private function _insert_gm_css_style_contents($p_gm_css_style_id, $p_attr_val)
		{
			$t_gm_css_style_id = (int)$p_gm_css_style_id;				

			foreach($p_attr_val as $t_attr => $t_val)
			{
				$t_attr = trim(addslashes($t_attr));
				$t_val	= trim(addslashes($t_val));
					
				/*
				*	check if an attribute already exits
				*/
				$t_gm_css_style_content_id = $this->_get_gm_css_style_content_id($t_gm_css_style_id, $t_attr);

				if($t_gm_css_style_content_id === false)
				{
					$query = 	"INSERT INTO
									`gm_css_style_content`
								SET
									`gm_css_style_id` = '" . $t_gm_css_style_id . "',
									`style_attribute` = '" . $t_attr . "',
									`style_value` = '" . $t_val . "'";
					$this->cooMySQLi->query($query);
				}
				else
				{
					$query = 	"UPDATE
									`gm_css_style_content`
								SET
									`style_value` = '" . $t_val . "'
								WHERE
									`gm_css_style_content_id` = '" . $t_gm_css_style_content_id . "'";
					$this->cooMySQLi->query($query);
				}
			}
			
			return;
		}

		/**
		 * @param int    $p_gm_css_style_id
		 * @param string $p_selector
		 * 
		 * @return void
		 */
		private function _update_gm_css_style($p_gm_css_style_id, $p_selector)
		{
			$t_gm_css_style_id		= (int)$p_gm_css_style_id;				
			$t_selector				= trim($p_selector);
			$this->v_css_selector	= $t_selector;

			$query = 	"UPDATE
							`gm_css_style`
						SET
							`style_name` = '" . addslashes($t_selector) . "'
						WHERE
							`gm_css_style_id` = '" . $t_gm_css_style_id . "'
						AND
							`template_name` = '" . addslashes(parent::get_current_template()) . "'
						AND
							`selectors_specificity` = '" . $this->cooMySQLi->real_escape_string($this->get_selectors_specificity($t_selector)) . "'";
			$this->cooMySQLi->query($query);
		}

		/**
		 * @param string $p_selector
		 *
		 * @return int
		 */
		private function _insert_gm_css_style($p_selector)
		{
			$t_selector = trim($p_selector);

			$query = 	"INSERT INTO
							`gm_css_style`
						SET
							`style_name` = '" . addslashes($t_selector) . "',
							`template_name` = '" . addslashes(parent::get_current_template()) . "',
							`selectors_specificity` = '" . $this->cooMySQLi->real_escape_string($this->get_selectors_specificity($t_selector)) . "'";
			$this->cooMySQLi->query($query);

			return $this->cooMySQLi->getCooMySQLi()->insert_id;
		}

		/**
		 * @param string $p_selector
		 *
		 * @return string|int|bool
		 */
		private function _get_gm_css_style_id($p_selector)
		{
			$t_selector = trim(addslashes($p_selector));

			$query = 	"SELECT
							`gm_css_style_id` AS `id`
						FROM
							`gm_css_style`
						WHERE
							`style_name` = '" . $t_selector . "'
						AND
							`template_name` = '" . addslashes(parent::get_current_template()) . "'";
			$result = $this->cooMySQLi->query($query);

			if((int)$this->cooMySQLi->num_rows($result) > 0)
			{
				$t_row = $this->cooMySQLi->fetch_array($result);
				return $t_row['id'];
			}
			else
			{
				return false;
			}
		}

		/**
		 * get gm_css_style_content_id of an attribute
		 * @param int    $p_gm_css_style_id
		 * @param string $p_attr
		 *
		 * @return int|bool
		 */
		private function _get_gm_css_style_content_id($p_gm_css_style_id, $p_attr)
		{
			$query = 	"SELECT
							`gm_css_style_content_id` AS `id`
						FROM
							`gm_css_style_content`
						WHERE
							`gm_css_style_id` = '" . (int)$p_gm_css_style_id . "'
						AND
							`style_attribute` = '" . $p_attr . "'";
			$result = $this->cooMySQLi->query($query);

			if((int)$this->cooMySQLi->num_rows($result) > 0)
			{
				$t_row = $this->cooMySQLi->fetch_array($result);
				return $t_row['id'];
			}
			else
			{
				return false;
			}
		}
	}

	/*
	*	this class provides several functions to export css code into files
	*/
	class GMCSSExport extends GMCSS
	{	
		private $v_fopen_mode = 'w+';
		private $cooMyQSLi;


		/**
		 * @param string       $p_css_file
		 * @param GMSEDatabase $dbConnection
		 */
		public function __construct($p_css_file, GMSEDatabase $dbConnection)
		{
			/* call parent class */
			$this->cooMyQSLi = $dbConnection;
			parent::__construct($p_css_file, $this->cooMyQSLi);
		}
	
		/*
		*	prepare export
		*	-> create file
		*	@return int t_error_id
		*	-> 0 no error
		*	-> 1 file already exists
		*	-> 2 file does not exist
		*	-> 3 dirperms incorrect
		*	-> 4 fileperms incorrect
		*	-> 5 cannot open file
		*	-> 6 wrong filetype - *.css only
		*	
		*/
		/**
		 * <p>prepare export<br/>
		 *	-> create file</p><br/>
		 * <p>-> 0 no error<br/>
		 *	-> 1 file already exists<br/>
		 *	-> 2 file does not exist<br/>
		 *	-> 3 dirperms incorrect<br/>
		 *	-> 4 fileperms incorrect<br/>
		 *	-> 5 cannot open file<br/>
		 *	-> 6 wrong filetype - *.css only</p>
		 * 
		 * @return int
		 */
		public function _prepare_export()
		{
			/* check css filename */
			if(parent::_check_css_filename() == true)
			{
				/* check if file exist */
				if(parent::_css_file_exists() == false)
				{
					/* check dir perms */
					if(parent::_css_dir_perms() == true)
					{
						/* try to open file */
						if(parent::_open_css_file($this->v_fopen_mode))
						{
							return 0;
						}
						else
						{
							return 5;
						}
					} 
					else
					{
						return 3;
					}			
				} 
				else
				{
					return 1;
				}			
			} 
			else
			{
				return 9;
			}			
		}
	
		/**
		 * @return int
		 */
		public function _export()
		{
			$query = 	"SELECT
							`style_name` AS `selector`,
							`gm_css_style_id` AS `id`
						FROM
							`gm_css_style`
						WHERE
							`template_name` = '" . addslashes(parent::get_current_template()) . "'
						ORDER BY
							`gm_css_style_id`
						ASC";
			$result = $this->cooMyQSLi->query($query);

			$t_css = '';
			while($t_css_selector = $this->cooMyQSLi->fetch_array($result))
			{
				$t_css .= $t_css_selector['selector'];
				$t_css .= "\n{";
				
				$css_attr_query = 	"SELECT
										`style_attribute` AS `atr`,
										`style_value` AS `val`
									FROM
										`gm_css_style_content`
									WHERE
										`gm_css_style_id` = '" . $t_css_selector['id'] . "'
									ORDER BY
										`style_attribute`
									ASC";
				$css_attr_result = $this->cooMyQSLi->query($css_attr_query);
				
				while(($t_css_atr = $this->cooMyQSLi->fetch_array($css_attr_result)))
				{	
					$t_css .= "\n";
					$t_css .= "\t" . $t_css_atr['atr'] . ": " . $t_css_atr['val'] . ";";
				}
				$t_css .= "\n}\n\n";
				parent::_write_css_file($t_css);
				$t_css ='';
			}
			parent::_close_css_file();	
			
			return -2;
		}
	}	
	
	/*
	*	this class provides several functions to export css code into files
	*/
	class GMCSSUpload extends GMCSS
	{	
		private $v_files = array();
		private $cooMySQLi;


		/**
		 * @param string       $p_files
		 * @param GMSEDatabase $dbConnection
		 * @param string       $p_type
		 */
		public function __construct($p_files, GMSEDatabase $dbConnection, $p_type)
		{
			$this->v_files = $p_files;
			$this->cooMySQLi = $dbConnection;
			
			$t_filename = parent::_clean_css_filename($this->v_files['userfile']['name']);
			
			/* call parent class */
			parent::__construct($t_filename, $this->cooMySQLi, $p_type);			
		}

		/**
		 * <p>-> 0 no error<br/>
		 *	-> 1 file already exists<br/>
		 *	-> 2 file does not exist<br/>
		 *	-> 3 dirperms incorrect<br/>
		 *	-> 4 fileperms incorrect<br/>
		 *	-> 5 cannot open file<br/>
		 *	-> 6 wrong filetype - *.css only<br/>
		 *	-> 7 upload failed</p>
		 * @return int
		 */
		public function _prepare_upload()
		{	
			if(parent::_check_filetyp($this->v_files['userfile']['type']) == true)
			{
				/* check if file exist */
				if(parent::_css_file_exists() == false)				
					/* check file permission */
					if(parent::_css_dir_perms() == true)
					{
						return 0;
					}
					else
					{
						return 3;
					}	
				else
				{
					return 1;
				}
			}
			else
			{
				return 6;
			}
		}

		/**
		 * @return int
		 */
		public function _upload()
		{
			if(move_uploaded_file($this->v_files['userfile']['tmp_name'], parent::get_css_file()))
			{
				@chmod(parent::get_css_file(), 0777);
				return -1;
			} 
			else 
			{
				return 7;
			}		
		}
	}

	/*
	*	this class provides several functions to export css code into files
	*/
	class GMCSSArchive extends GMCSS
	{	
		private $cooMySQLi;


		/**
		 * @param GMSEDatabase $dbConnection
		 */
		public function __construct(GMSEDatabase $dbConnection)
		{
			$this->cooMySQLi = $dbConnection;
			parent::__construct('', $this->cooMySQLi);
		}

		public function _load_archive()
		{		
			$t_files = parent::_get_dir_content();
			
			if(count($t_files) > 0)
			{
				$t_form = '';
				$t_file_list = '';
				for($i = 0; $i < count($t_files); $i++)
				{
					$t_form .= '<div id="se_archive_id_'	. str_replace('.', '_', $t_files[$i]) . '" class="se_archive_row">';
					$t_form .= '<div class="se_archive_row_info">';
					$t_form .= $t_files[$i]	. '';
					$t_form .= '</div>';
					$t_form .= '<div class="se_archive_row_buttons">';
					$t_form .= '<div class="se_import" id="se_import_id_'	. $t_files[$i] . '">' . BUTTON_TITLE_LOAD	. '</div>';
					$t_form .= '<div class="se_delete" id="se_delete_id_'	. $t_files[$i] . '">' . BUTTON_TITLE_DELETE	. '</div>';
					$t_form .= '<div class="se_open" id="se_open_'		. $t_files[$i] . '">' . BUTTON_TITLE_SAVE	. '</div>';
					$t_form .= '</div></div>';
					$t_file_list .= $t_form;
					$t_form = '';
				}

				return array('count' => count($t_files), 'files' => $t_file_list);
			}
			else
			{
				return 11;
			}
		}
	}