<?php
/* --------------------------------------------------------------
  G-Protector v1.2
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  --------------------------------------------------------------
*/

class GProtector
{
	private $secure_token = '';
	private $filter_array = array();
	private $log_header_template = '';
	private $separator = "\r\n";
	private $log_connectors_array = array();
	
	public function __construct()
	{
		$this->set_secure_token();
		$this->set_log_header_template("===========================================================\nIP: {IP}\nDatum: {DATETIME}\nScript: {SCRIPT}\nNachricht: {MESSAGE}\n\n");
		$this->init_log_connectors();
		$this->load_functions();
	}
	

	public function start()
	{
		$t_files_array = glob(GAMBIO_PROTECTOR_FILTER_DIR . $this->get_file_pattern());
		
		if(is_array($t_files_array))
		{
			foreach($t_files_array as $t_filepath)
			{
				include($t_filepath);
			}
			
			$this->filter();
		}
		else
		{
			// todo
			$this->log('no filters found', 'gprotector_error', 'warning');
		}
		
		$t_ip_blocker_result = $this->search_ip_in_blacklist($this->get_user_ip());
		if($t_ip_blocker_result)
		{
			$this->block_ip();
		}
		
		return true;
	}


	private function init_log_connectors()
	{
		$t_files_array = glob(GAMBIO_PROTECTOR_CONNECTORS_DIR . $this->get_file_pattern());

		if(is_array($t_files_array))
		{
			foreach($t_files_array as $t_file)
			{
				include_once($t_file);
			}
		}
	}
	
	
	/**
	 * Return the visitor's IP address
	 * 
	 * @return string Visitor's IP address
	 */
	private function get_user_ip()
	{
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$t_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$t_ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else
		{
			$t_ip = $_SERVER['REMOTE_ADDR'];
		}
		
		return $t_ip;
	}
	
		
	/**
	 * Search the given IP in blacklist and returns true if it is in the blacklist. 
	 * 
	 * @param string $p_user_ip User IP to check
	 * @return bool OK:false | blocked IP: true
	 * 
	 */
	private function search_ip_in_blacklist($p_user_ip)
	{
		if(file_exists($this->get_ip_blacklist_path()))
		{
			if(is_readable($this->get_ip_blacklist_path()))
			{
				$t_file_handle = fopen($this->get_ip_blacklist_path(), 'r');
				while(!feof($t_file_handle))
				{
					$t_blocked_ip = fgets($t_file_handle);
					$t_blocked_ip = trim($t_blocked_ip);
					
					if(!empty($t_blocked_ip))
					{
						if(strpos(trim($p_user_ip), trim($t_blocked_ip)) === 0)
						{
							fclose($t_file_handle);
							return true;
						}
					}
				}
				fclose($t_file_handle);
				return false;

			}
			else
			{
				$this->log('Can not read IP-blacklist', 'gprotector_error', 'error');
			}
		}
		else
		{
			$this->log($this->get_ip_blacklist_path() . ' not found', 'gprotector_error', 'error');
		}
	}
	
	
	/**
	 * Sends 403 Header to any blocked IPs
	 */
	private function block_ip()
	{
		header("HTTP/1.0 403 forbidden");
		echo 'forbidden';
		exit;
	}


	private function add_filter($p_key, $p_script_name, $p_variables, $p_function, $p_severity = 'error')
	{
		$t_variables = $p_variables;
		
		if(!is_array($t_variables))
		{
			$t_variables = array($t_variables);
		}
		
		$t_script_path_array = $p_script_name;
		
		if(!is_array($t_script_path_array))
		{
			$t_script_path_array = array($p_script_name);
		}
		
		$this->filter_array[$p_key] = array(	'script_name_array' => $t_script_path_array, 
												'variables_array' => $t_variables, 
												'function' => $p_function,
												'severity' => $p_severity);
	}


	private function filter()
	{
		if(is_array($this->filter_array))
		{
			foreach($this->filter_array as $t_filter_name => $t_data_array)
			{
				if(is_array($t_data_array) && isset($t_data_array['script_name_array']) && is_array($t_data_array['script_name_array']))
				{
					foreach($t_data_array['script_name_array'] as $t_script_path)
					{
						if($this->is_script($t_script_path) === true)
						{
							if(isset($t_data_array['function']))
							{
								$c_function = (string)$t_data_array['function'];
								$t_function_prefix = $this->get_function_prefix();
								$c_function = $t_function_prefix . $c_function;

								if(function_exists($c_function))
								{
									if(isset($t_data_array['variables_array']) && is_array($t_data_array['variables_array']))
									{
										foreach($t_data_array['variables_array'] as $t_variable)
										{
											$c_variable_string = (string)$t_variable;

											$t_array_bracket_pos = (int)strpos($c_variable_string, '[');
											$t_variable_name_end_pos = strlen($c_variable_string);
											
											if($t_array_bracket_pos > 0)
											{
												$t_variable_name_end_pos = $t_array_bracket_pos;
											}

											if($t_variable_name_end_pos > 0)
											{
												$t_variable_name = substr($c_variable_string, 0, $t_variable_name_end_pos);

												global ${$t_variable_name};

												$t_variable_reference =& ${$t_variable_name};

												preg_match_all('/\[("|\')?([^"\'\]]+)("|\')?]/', $c_variable_string, $t_matches_array);

												if(isset($t_matches_array[2]) && !empty($t_matches_array[2]))
												{
													foreach($t_matches_array[2] as $key)
													{
														if(!isset($value_reference) && isset($t_variable_reference[$key]))
														{
															$value_reference =& $t_variable_reference[$key];
														}
														elseif(isset($value_reference) && is_array($value_reference))
														{
															$value_reference =& $value_reference[$key];
														}
													}
												}
												else
												{
													$value_reference = $t_variable_reference;
												}

												if(isset($value_reference) && $value_reference !== '')
												{
													if($value_reference !== '')
													{
														// run filter
														$t_variable_copy = $value_reference;
														$value_reference = call_user_func($c_function, $value_reference);
														if($t_variable_copy != $value_reference)
														{
															$this->log('Die Regel "' . $t_filter_name . '" hat einen unerwarteten Variablenwert erkannt und erfolgreich gefiltert.', 'security', $t_data_array['severity']);
															if(is_array($t_variable_copy) || is_object($t_variable_copy))
															{
																$this->log("unerwarteter Variablenwert\r\nFilterregel: " . $t_filter_name . "\r\nVariable: $$c_variable_string\rnvorher: " . print_r($t_variable_copy, true) . "\r\nnachher: " . print_r($value_reference, true), 'security_debug', $t_data_array['severity']);
															}
															else
															{
																$this->log("unerwarteter Variablenwert\r\nFilterregel: " . $t_filter_name . "\r\nVariable: $$c_variable_string\r\nvorher: " . $t_variable_copy . "\r\nnachher: " . $value_reference, 'security_debug', $t_data_array['severity']);
															}
														}
													}

													unset($value_reference);
												}
											}
										}							
									}
									else
									{
										// todo 
										$this->log('filter variables are missing', 'gprotector_error', 'error');
									}
								}
								else
								{
									// todo 
									$this->log('filter function "' . $c_function . '" does not exist', 'gprotector_error', 'error');
								}
							}
							else
							{
								// todo 
								$this->log('filter function is not set', 'gprotector_error', 'error');
							}
						}
					}						
				}
				else
				{
					// todo 
					$this->log('filter data is missing', 'gprotector_error', 'error');
				}
			}
		}
		else
		{
			// todo 
			$this->log('v_filter_array is not set', 'gprotector_error', 'error');
		}
		
		return true;
	}


	private function load_functions()
	{
		$t_files_array = glob(GAMBIO_PROTECTOR_FUNCTIONS_DIR . $this->get_file_pattern());
		
		if(is_array($t_files_array))
		{
			foreach($t_files_array as $t_filepath)
			{
				include_once($t_filepath);
			}
			
			return true;
		}
		else
		{
			// todo
			$this->log('No functions found', 'gprotector_error', 'warning');
		}
		
		return false;		
	}


	private function get_file_pattern()
	{
		$t_file_pattern = '*.inc.php';
		if(defined('GAMBIO_PROTECTOR_FILE_PATTERN'))
		{
			$t_pattern = trim((string)GAMBIO_PROTECTOR_FILE_PATTERN);
			if($t_pattern != '')
			{
				$t_file_pattern = $t_pattern;
			}
		}
		
		return $t_file_pattern;
	}


	private function get_running_script_path()
	{
		$t_script_path = false;
		
		$t_backtrace_array = debug_backtrace();
		if(is_array($t_backtrace_array))
		{
			$t_running_script_data_array = array_pop($t_backtrace_array);
			$t_script_path = $t_running_script_data_array['file'];
			
			if(defined('GAMBIO_PROTECTOR_BASE_DIR') && is_string(GAMBIO_PROTECTOR_BASE_DIR))
			{
				$t_script_path = str_replace(GAMBIO_PROTECTOR_BASE_DIR, '', $t_script_path);
			}
		}
		
		if($t_script_path === false)
		{
			// todo
			$this->log('script name could not be determined', 'gprotector_error', 'warning');
		}	
		
		
		return $t_script_path;
	}


	private function get_function_prefix()
	{
		$t_function_prefix = 'gprotector_';
		
		if(defined('GAMBIO_PROTECTOR_FUNCTION_PREFIX'))
		{
			$t_prefix = preg_replace('/[^a-zA-Z_]/', '', trim((string)GAMBIO_PROTECTOR_FUNCTION_PREFIX));
			if($t_prefix != '')
			{
				$t_function_prefix = $t_prefix;
			}
		}
		
		return $t_function_prefix;		
	}


	private function get_token_prefix()
	{
		$t_token_prefix = 'gprotector_';
		
		if(defined('GAMBIO_PROTECTOR_TOKEN_FILE_PREFIX'))
		{
			$t_prefix = preg_replace('/[^a-zA-Z0-9_-]/', '', trim((string)GAMBIO_PROTECTOR_TOKEN_FILE_PREFIX));
			if($t_prefix != '')
			{
				$t_token_prefix = $t_prefix;
			}
		}
		
		return $t_token_prefix;
	}


	private function set_secure_token()
	{
		$t_files_array = glob(GAMBIO_PROTECTOR_TOKEN_DIR . $this->get_token_prefix() . '*');

		if(is_array($t_files_array))
		{
			foreach($t_files_array as $t_filepath)
			{
				$t_token_filename = basename($t_filepath);
				$t_token = str_replace($this->get_token_prefix(), '', $t_token_filename);

				if(strlen($t_token) > 0)
				{
					$this->secure_token = $t_token;
				}				
			}
		}
		elseif(is_writable(GAMBIO_PROTECTOR_TOKEN_DIR))
		{
			$t_token = md5(time() . rand());
			$t_token_file = GAMBIO_PROTECTOR_TOKEN_DIR . $this->get_token_prefix() . $t_token;

			if(function_exists('file_put_contents'))
			{
				@file_put_contents($t_token_file, 'empty');
			}
			else
			{
				$fp = @fopen($t_token_file, 'w');
				@fwrite($fp, 'empty');
				@fclose($fp);
			}

			if(!file_exists($t_token_file))
			{
				return false;
			}
			else
			{
				$this->secure_token = $t_token;
			}
		}

		return true;
	}


	private function get_secure_token()
	{
		return preg_replace('/[^a-zA-Z0-9_-]/', '', (trim((string)$this->secure_token)));
	}


	private function write_custom_log($p_message, $p_type, $p_severity = 'error')
	{
		$c_message = (string)$p_message;
		$t_message_details = $this->prepare_log_message($c_message);
		
		if(strpos($c_message, $this->separator) !== false)
		{
			$c_message = substr($c_message, 0, strpos($c_message, $this->separator));
		}

		foreach($this->log_connectors_array as $coo_gprotector_log_connector)
		{
			$t_error_type = 'GPROTECTOR ' . strtoupper($p_severity);
			$coo_gprotector_log_connector->log($c_message, 'security', $p_type, $p_severity, $t_error_type, $t_message_details);
		}
		
		return true;
	}


	private function prepare_log_message($p_string)
	{
		$c_string = (string)$p_string;
		$t_prepared_message = '';

		if(strpos($c_string, $this->separator) !== false)
		{ 
			$t_prepared_message = str_replace("'", "\\'", substr($c_string, strpos($c_string, $this->separator) + strlen($this->separator)));
		}
		
		return $t_prepared_message;
	}


	private function write_log($p_message, $p_type, $p_severity = 'error')
	{
		$c_message = (string)$p_message;
		$c_log_filename = $this->get_log_filename($p_type);
		if($c_log_filename !== false)
		{
			$t_logfile_path = GAMBIO_PROTECTOR_LOG_DIR . $c_log_filename;
			$t_written_bytes = false;

			if(is_dir(GAMBIO_PROTECTOR_LOG_DIR)
			   && is_writable(GAMBIO_PROTECTOR_LOG_DIR)
			   && ((	file_exists($t_logfile_path)
						&& is_writeable($t_logfile_path)
				   )
				   || (!file_exists($t_logfile_path)))
			)
			{
				if(function_exists('file_put_contents'))
				{
					$t_written_bytes = @file_put_contents($t_logfile_path, $this->get_substituted_log_content($this->log_header_template, $c_message, $p_severity), FILE_APPEND | LOCK_EX);
				}
				else
				{
					$fp = @fopen($t_logfile_path, 'a');
					$t_written_bytes = @fwrite($fp, $this->get_substituted_log_content($this->log_header_template, $c_message, $p_severity));
					@fclose($fp);
				}

				if((defined('GAMBIO_PROTECTOR_GZIP_LOG') && GAMBIO_PROTECTOR_GZIP_LOG === true) || defined('GAMBIO_PROTECTOR_GZIP_LOG') === false)
				{
					$t_max_filesize = 1 * 1024 * 1024; // standard: 1 megabyte
					if(defined('GAMBIO_PROTECTOR_LOG_MAX_FILESIZE') && (double)GAMBIO_PROTECTOR_LOG_MAX_FILESIZE > 0)
					{
						$t_max_filesize = (double)GAMBIO_PROTECTOR_LOG_MAX_FILESIZE * 1024 * 1024;
					}

					// compress logfile if larger than GAMBIO_PROTECTOR_LOG_MAX_FILESIZE megabyte
					if(filesize($t_logfile_path) > $t_max_filesize)
					{
						$fp = @fopen($t_logfile_path, 'r+');
						if($fp !== false)
						{
							@date_default_timezone_set('Europe/Berlin');
							$t_compressed_file_path = substr($t_logfile_path, 0, strpos($t_logfile_path, ".")) . '-' . date('Ymd_His') . '.log.gz';
							$t_compressed_file = @gzopen($t_compressed_file_path, 'w9');
							if($t_compressed_file !== false)
							{
								@gzwrite($t_compressed_file, fread($fp, filesize($t_logfile_path)));
								@gzclose($t_compressed_file);

								// delete content of log which was compressed before
								@ftruncate($fp, 0);
							}
							@fclose($fp);
						}
					}
				}
			}

			if($t_written_bytes === false || $t_written_bytes == 0)
			{
				return false;
			}

			return true;
		}

		return false;
	}


	private function log($p_message, $p_type, $p_severity = 'error')
	{
		if(!empty($this->log_connectors_array))
		{
			return $this->write_custom_log($p_message, $p_type, $p_severity);
		}

		return $this->write_log($p_message, $p_type, $p_severity);
	}


	private function get_log_filename($p_type)
	{
		$c_type = basename(trim((string)$p_type));
		$t_secure_token = $this->get_secure_token();
		
		if($c_type != '' && $t_secure_token != '')
		{
			$t_log_filename = $c_type . '-' . $t_secure_token . '.log';
			return $t_log_filename;
		}
		
		return false;		
	}


	private function set_log_header_template($p_template)
	{
		$c_template = (string)$p_template;
		$this->log_header_template = $c_template;
	}


	private function get_substituted_log_content($p_template, $p_message = '', $p_severity = 'error', $p_log_filename = 'security', $p_message_details = '')
	{
		@date_default_timezone_set('Europe/Berlin');
		
		$c_template = (string)$p_template;
		$c_message = (string)$p_message;
		$c_severity = (string)$p_severity;
		$c_log_filename = (string)$p_log_filename;
		$c_message_details = (string)$p_message_details;
		
		$c_template = $this->substitute($c_template, '{IP}', $this->get_user_ip());
		$c_template = $this->substitute($c_template, '{DATETIME}', date('Y-m-d H:i:s'));
		$c_template = $this->substitute($c_template, '{MESSAGE}', $c_message);
		$c_template = $this->substitute($c_template, '{SCRIPT}', $this->get_running_script_path());
		$c_template = $this->substitute($c_template, '{LOGFILE_NAME}', (string)$c_log_filename);
		$c_template = $this->substitute($c_template, '{SEVERITY}', $c_severity);
		$c_template = $this->substitute($c_template, '{ERROR_TYPE}', 'GPROTECTOR ' . strtoupper($p_severity));
		$c_template = $this->substitute($c_template, '{MESSAGE_DETAILS}', $c_message_details);
		
		return $c_template;
	}


	private function substitute($p_content, $p_place_holder, $p_substitution = '')
	{
		$t_content = (string)$p_content;
		
		if(strpos($p_content, $p_place_holder) !== false)
		{
			$t_content = str_replace((string)$p_place_holder, (string)$p_substitution, $t_content);
		}
		
		return $t_content;
	}


	private function is_script($p_script_path)
	{
		$c_script_path = (string)$p_script_path;
		if($this->get_running_script_path() == $c_script_path)
		{
			return true;
		}
		
		return false;	
	}


	private function get_ip_blacklist_path()
	{
		$t_dir = dirname(__FILE__) . '/';
		
		if(defined('GAMBIO_PROTECTOR_DIR') && is_string(GAMBIO_PROTECTOR_DIR) && @is_dir(GAMBIO_PROTECTOR_DIR))
		{
			$t_dir = GAMBIO_PROTECTOR_DIR;
		}
		
		return $t_dir . 'ip_blacklist.txt';
	}
}
