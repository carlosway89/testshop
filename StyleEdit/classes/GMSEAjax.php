<?php
	/* --------------------------------------------------------------
	  StyleEdit v2.0
	  Gambio GmbH
	  http://www.gambio.de
	  Copyright (c) 2010 Gambio GmbH
	  --------------------------------------------------------------
	*/

	/*
	*	this class provides functions for opening, reading and writing files 
	*/
	class GMSEAjax
	{
		private $v_param;
		private $v_error;
		private $cooMySQLi;

		/**
		 * constructor
		 * @param array        $p_param
		 * @param GMSEDatabase $dbConnection
		 */
		public function __construct($p_param, GMSEDatabase $dbConnection)
		{
			$this->v_param		= $p_param;
			$this->v_error		= new GMSEError();
			$this->cooMySQLi = $dbConnection;
		}

		/**
		 * function to handle logoff
		 * 
		 * @return json
		 */
		public function _die()
		{
			$t_response = array
			(
				"error"			=>	666, 
				"message"		=>	$this->v_error->get_error(666)
			);

			echo json_encode($t_response);
		}

		/**
		 * function to handle several requests
		 */
		public function _request()
		{
			switch($this->v_param['module'])
			{
				default:
				case 'boxes_edit': 	
					include('ajax/boxes_edit.php');
				break;

				case 'se_monitor': 
					$this->_request_monitor();
				break;

				case 'se_monitor_save': 
					$this->_request_monitor_save();
				break;

				case 'se_monitor_delete': 
					$this->_request_monitor_delete();
				break;

				case 'se_toolbox': 	
					$this->_request_toolbox();
				break;

				case 'se_load': 	
				case 'se_load_styles': 	
				case 'se_load_history': 	
				case 'se_save_styles': 	
				case 'se_load_font_styles': 
					$this->_request_styles();
				break;

				case 'se_image_delete': 
					$this->_request_image_delete();
				break;
				
				case 'se_image_delete_confirm': 
					$this->_request_image_delete_confirm();
				break;
				
				case 'se_image_upload': 
					$this->_request_image_upload();
				break;
				
				case 'se_image_info': 
					$this->_request_image_info();
				break;
				
				case 'se_view':
					$this->_request_view();
				break;

				case 'se_delete': 
					$this->_request_delete();
				break;

				case 'se_delete_confirm': 
					$this->_request_delete_confirm();
				break;

				case 'se_upload': 				
					$this->_request_upload();
				break;

				case 'se_import': 
					$this->_request_import();
				break;

				case 'se_export': 
					$this->_request_export();
				break;

				case 'se_export_name': 
					$this->_request_export_name();					
				break;

				case 'se_archive': 
					$this->_request_archive();
				break;

				case 'se_gradients': 
					$this->_request_gradient_images();
				break;

				case 'se_close': 
					$this->_response(
										0, 
										INFO_CLOSE
					);		
				break;
			}
		}

		/**
		 * function to handle responses
		 * @param int    $p_error
		 * @param string $p_message
		 * @param array  $p_data
		 * @param int    $p_data_count
		 * 
		 * @return json
		 */
		private function _response($p_error, $p_message, $p_data = array(),  $p_data_count = 0)
		{
			$t_response = array
			(
				"error"			=>	$p_error, 
				"message"		=>	$p_message,
				"data"			=>	$p_data,
				"data_count"	=>	$p_data_count
			);

			echo json_encode($t_response);			
		}

		/**
		 * function to load the monitor toolbox
		 */
		private function _request_monitor()
		{
			include('ajax/se_monitor.php');
		}

		/**
		 * function to save styles out of the monitor
		 */
		private function _request_monitor_save()
		{		
			if(empty($this->v_param['se_selector']))
			{
				$coo_import = new GMCSSImport($this->cooMySQLi, false, '');
			}
			else
			{
				$coo_import = new GMCSSImport($this->cooMySQLi, false, 'single');

				$coo_import->set_css_selector($this->v_param['se_selector']);

				$coo_import->_delete_styles(true);
			}
			
			$coo_import->set_css_content(stripslashes($this->v_param['se_styles']));

			$coo_import->_import();

			echo $coo_import->get_css_selector();
			
			unset($coo_import);
		}

		/**
		 * function to save styles out of the monitor
		 */
		private function _request_monitor_delete()
		{		
			$coo_import = new GMCSSImport($this->cooMySQLi, false, 'single');

			$coo_import->set_css_selector($this->v_param['se_selector']);
			
			$t_success = $coo_import->_delete_styles();

			if($t_success === false)
			{
				echo "fehler";
			}
			else
			{
				echo "geloescht";
			}

			unset($coo_import);
		}

		/**
		 * function to load the main toolbo
		 */
		private function _request_toolbox()
		{
			include('ajax/se_toolbox.php');
		}

		/**
		 * function to load style
		 */
		private function _request_styles()
		{
			$coo_manager = new GMCSSManager($this->cooMySQLi, $this->v_param['token']);

			if($this->v_param['module'] == 'se_load_styles')
			{
				$coo_manager->load_json_styles($this->v_param['se_selector']);
			}
			elseif($this->v_param['module'] == 'se_load_history')
			{
				$coo_manager->load_json_history($this->v_param['se_history_type']);
			}
			elseif($this->v_param['module'] == 'se_save_styles')
			{
				$t_error = $coo_manager->save_styles($this->v_param['se_id'], $this->v_param['se_styles']);

				/* return respone */
				$this->_response(
									0, 
									$this->v_error->get_error($t_error['error']),
									$t_error
				);		
			}
			elseif($this->v_param['module'] == 'se_load_font_styles')
			{
				$coo_manager->get_fonts($this->v_param['se_font']);
			}	
			elseif($this->v_param['module'] == 'se_load')
			{
				$coo_manager->load_styles();
			}	
			
			unset($coo_manager);
		}

		/**
		 * function to handle confirmation delete
		 */
		private function _request_image_delete_confirm()
		{
			$coo_manager = new GMCSSManager($this->cooMySQLi, $this->v_param['token']);
			
			$t_filename = $coo_manager->get_background_image($this->v_param['file_id']);	

			/* return respone */
			$this->_response(
								0, 
								str_replace('{#FILE#}', $t_filename, $this->v_error->get_error(-5))
			);		

			unset($coo_manager);
		}

		/**
		 * function to handle deletion of css style background-images
		 */
		private function _request_image_delete()
		{
			$coo_manager = new GMCSSManager($this->cooMySQLi, $this->v_param['token']);
			
			$t_error = array();
			
			$t_error = $coo_manager->delete_background_image($this->v_param['background_image']);
			
			if($t_error['error'] == -4)
			{
			}

			/* return respone */
			$this->_response(
								$t_error['error'], 
								$this->v_error->get_error($t_error),
								array('tmp_image_id' => $t_error['file_id'])
			);	
			
			unset($coo_manager);
		}

		/**
		 * function to handle css style background-images uploads
		 */
		private function _request_image_upload()
		{
			$coo_upload = new GMCSSUpload($_FILES, $this->cooMySQLi, $this->v_param['type']);

			$t_error = $coo_upload->_prepare_upload();			

			if((int)$t_error == 0)
			{
				$t_error = $coo_upload->_upload();

				if($t_error == -1)
				{		
					$t_image_url		= 'url(' . $coo_upload->get_css_image_path() . ')';

					$t_background_image = array('background-image' => $t_image_url);
					
					$coo_manager = new GMCSSManager($this->cooMySQLi, $this->v_param['token']);

					$coo_manager->save_styles($this->v_param['se_id'], $t_background_image, 'image');
					
					$t_image_url		= 'url(' . $coo_upload->get_css_filepath() . ')';

					unset($coo_manager);
					
				}
			}

			/* return respone */
			$this->_response(
								$t_error, 
								str_replace('{#FILE#}', $coo_upload->get_css_filename(), $this->v_error->get_error($t_error)),
								array('image' => $t_image_url)
								
			);		
			unset($coo_upload);
		}

		/**
		 * function to handle css style background-images uploads
		 */
		private function _request_image_info()
		{
			if(strstr($this->v_param['backup'], SE_CFG_IMAGES_GRADIENTS_PATH_CSS))
			{
				$t_backround_path	= SE_CFG_IMAGES_GRADIENTS_PATH;
				$t_file_path		= SE_CFG_IMAGES_GRADIENTS_PATH_RELATIVE;
			}
			else
			{
				$t_backround_path	= SE_CFG_IMAGES_BACKGROUNDS_PATH;
				$t_file_path		= SE_CFG_IMAGES_BACKGROUNDS_URL_RELATIVE;
			}

			$coo_manager		= new GMCSSManager($this->cooMySQLi, $this->v_param['token']);
			$t_filename			= $coo_manager->get_background_image($this->v_param['se_id'], $this->v_param['backup']);

			$t_error = 0;

			if($t_filename != false)
			{
				$t_file_size		= (@filesize($t_backround_path . $t_filename) / 1024) ;	
				
				$t_file_dimensions	= @getimagesize($t_backround_path . $t_filename);

				$t_file_info		= (int) $t_file_size . ' KB / ' . $t_file_dimensions[0] . 'px (B) x ' . $t_file_dimensions[0] . 'px (H)';
			
				$t_filepath			= $t_file_path . $t_filename;
			}
			else
			{
				$t_error = 13;
				$t_filename = '';
			}
			
			if(strstr($t_filename, SE_CFG_BACKUP_IMAGE_PREFIX))
			{
				$t_filename	= str_replace(SE_CFG_BACKUP_IMAGE_PREFIX, '', $t_filename);
				$t_pos		= strpos($t_filename, '_');
				$t_filename	= substr($t_filename, $t_pos+1);
			}

			/* return respone */
			$this->_response(
								$t_error,											
								$this->_load_background_images($t_filename),
								$t_file_info,
								$t_filepath
			);		

			unset($coo_manager);
		}

		private function _request_view()
		{
			$t_file_id = $this->v_param['file_id'];
			
			$coo_viewer = new GMCSS($t_file_id, $this->cooMySQLi);

			header("Content-Type: application/octet-stream");
			
			header('Content-Disposition: attachment; filename="' . $t_file_id . '"'); 
			
			readfile( $coo_viewer->get_css_file()); 

			unset($coo_viewer);
		}

		/**
		 * function to handle delete
		 */
		private function _request_delete_confirm()
		{
			$t_file_id = $this->v_param['file_id'];

			/* return respone */
			$this->_response(
								0, 
								str_replace('{#FILE#}', $t_file_id, $this->v_error->get_error(-5))
			);		

			unset($coo_delete);
		}

		/**
		 * function to handle delete
		 */
		private function _request_delete()
		{
			$t_file_id = $this->v_param['file_id'];
			$coo_delete = new GMCSS($t_file_id, $this->cooMySQLi);

			$t_error = $coo_delete->_delete_file();

			/* return respone */
			$this->_response(
								$t_error, 
								$this->v_error->get_error($t_error),
								'',
								$coo_delete->_count_files()
			);		

			unset($coo_delete);
		}

		/**
		 * function to handle uploads
		 */
		private function _request_upload()
		{
			$coo_upload = new GMCSSUpload($_FILES, $this->cooMySQLi, $this->v_param['type']);

			$t_error = $coo_upload->_prepare_upload();

			if((int)$t_error == 0)
			{
				$t_error = $coo_upload->_upload();
			}

			/* return respone */
			$this->_response(
								$t_error, 
								str_replace('{#FILE#}', $coo_upload->get_css_filename(), $this->v_error->get_error($t_error))					
			);		
			unset($coo_upload);
		}

		/**
		 * function to handle uploads
		 */
		private function _request_import()
		{
			$t_file_id = $this->v_param['file_id'];

			$coo_import = new GMCSSImport($this->cooMySQLi, $t_file_id);
			
			$t_error = $coo_import->_prepare_import();
			
			if((int)$t_error == 0)
			{
				$t_error = $coo_import->_import();

				if((int)$t_error == -3)
				{

				}
			}
			
			/* return respone */
			$this->_response(
								$t_error, 
								$this->v_error->get_error($t_error)					
			);		
			unset($coo_import);
		}

		/**
		 * function to handle uploads
		 */
		private function _request_export()
		{
			$coo_export = new GMCSSExport($this->v_param['se_filename'], $this->cooMySQLi);

			$t_error = $coo_export->_prepare_export();
			
			if((int)$t_error == 0)
			{
				$t_error = $coo_export->_export();
			}
			
			/* return respone */
			$this->_response(
								$t_error, 
								str_replace('{#FILE#}', $coo_export->get_css_filename(), $this->v_error->get_error($t_error))					
			);		
			unset($coo_export);
		}

		/**
		 * function to handle uploads
		 */
		private function _request_export_name()
		{
			/* return respone */
			$this->_response(
								$t_error, 
								'export_' . date("Y-m-d", mktime(0, 0, 0, date("m"), date ("d"), date("Y"))) . '.css'
			);	
		}

		/**
		 * function to handle archive
		 */
		private function _request_archive()
		{
			$t_files = array();

			$coo_archive = new GMCSSArchive($this->cooMySQLi);
		
			$t_files = $coo_archive->_load_archive();

			/* return respone */
			$this->_response(
								$t_files, 
								$this->v_error->get_error($t_files),
								$t_files['files'],
								$t_files['count']
			);
			
			unset($coo_archive);
		}

		/**
		 * function to load gradient images
		 */
		private function _request_gradient_images()
		{
			$coo_manager = new GMCSSManager($this->cooMySQLi, $this->v_param['token']);
			
			$t_gradient_images = $coo_manager->get_gradient_images($this->v_param['se_selected_gradient']);
			
			unset($coo_manager);
		}


		/**
		 * function to load all background images
		 * @param string $p_active_image
		 *
		 * @return array
		 */
		private function _load_background_images($p_active_image = '')
		{
			$t_images_array = array();
			$t_images_array[] = array('filename' => '', 'active' => false);
			if($p_active_image == '')
			{
				$t_images_array[0]['active'] = true;
			}

			$t_dir = opendir(SE_CFG_IMAGES_BACKGROUNDS_PATH);

			if($t_dir)
			{
				while(($t_file = readdir($t_dir)) !== false)
				{
					if(is_file(SE_CFG_IMAGES_BACKGROUNDS_PATH . basename($t_file)) && substr($t_file, 0, 1) != '.' && substr($t_file, -3) != '.db' && $t_file != 'index.html')
					{
						$t_images_array[] = array('filename' => basename($t_file),
													'active' => (basename($t_file) == $p_active_image) ? true : false);
					}
				}

				closedir($t_dir);
			}

			return $t_images_array;
		}
	}
?>