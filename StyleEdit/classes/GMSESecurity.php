<?php
	/* --------------------------------------------------------------
	  StyleEdit v2.0
	  Gambio GmbH
	  http://www.gambio.de
	  Copyright (c) 2015 Gambio GmbH
	  --------------------------------------------------------------
	*/

	/*
	*	class to handle the security sessions
	*/
	class GMSESecurity
	{
		private $cooMySQLi;


		/**
		 * constructor
		 * @param GMSEDatabase $dbConnection
		 */
		public function __construct(GMSEDatabase $dbConnection)
		{
			$this->cooMySQLi = $dbConnection;

			$this->cooMySQLi->query("CREATE TABLE IF NOT EXISTS `gm_css_style_security` (
							  `gm_css_style_security_id` int(10) unsigned NOT NULL auto_increment,
							  `gm_css_style_security_token` varchar(64) default NULL,
							  `gm_css_style_security_date` datetime default NULL,
							  PRIMARY KEY  (`gm_css_style_security_id`)
							) ENGINE=MyISAM  COMMENT='Gambio StyleEdit INTERFACE TABLE'");

			return;
		}

		/**
		 * function to set the security token in db
		 * @param $p_token
		 * @return bool
		 */
		public function set_sec_token($p_token)
		{
			$t_sec_token = $this->clean_sec_token($p_token);
			if(!empty($t_sec_token))
			{
				if($this->valid_sec_token($t_sec_token) == false)
				{
					$query = 	"INSERT INTO 
									`gm_css_style_security` 
								SET 
									`gm_css_style_security_token` = '" . $t_sec_token . "', 
									`gm_css_style_security_date` = NOW()";
					$this->cooMySQLi->query($query);
				}
			}
			return;
		}

		/**
		 * function to update the security token in db
		 * @param  int $p_sec_id
		 * @return bool
		 */
		protected function update_sec_token($p_sec_id)
		{
			$query = 	"UPDATE 
							`gm_css_style_security` 
						SET 
							`gm_css_style_security_date` = NOW() 
						WHERE 
							`gm_css_style_security_id` = '" . (int)$p_sec_id . "'";
			$this->cooMySQLi->query($query);
			return;
		}

		/**
		 * function to clean security table in db
		 * @param string $p_token
		 */
		public function delete_sec_token($p_token = '')
		{
			if($p_token == '')
			{
				$query = 	"SELECT
								`gm_css_style_security_id`
							AS
								`id` 
							FROM
								`gm_css_style_security`
							WHERE
								UNIX_TIMESTAMP(gm_css_style_security_date) < (UNIX_TIMESTAMP()-30*60)";
				$result = $this->cooMySQLi->query($query);
			}
			else
			{
				$t_sec_token= $this->clean_sec_token($p_token);
				$query = 	"SELECT 
								`gm_css_style_security_id` 
							AS 
								`id` 
							FROM 
								`gm_css_style_security`
							WHERE
								`gm_css_style_security_token` = '" . $t_sec_token . "'";
				$result = $this->cooMySQLi->query($query);
			}

			if($result->num_rows > 0)
			{
				while($t_row = $result->fetch_assoc())
				{	
					$history_query = 	"SELECT 
											`gm_css_style_history_id`
										AS
											`id`
										FROM
											`gm_css_style_history`
										WHERE
											`gm_css_style_security_id` = '" . (int)$t_row['id']  . "'";
					
					$history_result = $this->cooMySQLi->query($history_query);
					
					if($history_result->num_rows > 0)
					{
						while($t_row_history = $history_result->fetch_assoc())
						{
							$query = 	"DELETE FROM
											`gm_css_style_history_content`
										WHERE
											`gm_css_style_history_id` = '" . (int)$t_row_history['id'] . "'";
							$this->cooMySQLi->query($query);
						}
						$query = 	"DELETE FROM
										`gm_css_style_history`
									WHERE
										`gm_css_style_security_id` = '" . (int)$t_row['id']  . "'";
						$this->cooMySQLi->query($query);
					}

					if($p_token == '')
					{
						$query = 	"DELETE FROM
										`gm_css_style_security`
									WHERE
										`gm_css_style_security_id` = '" . (int)$t_row['id'] . "'";
						$this->cooMySQLi->query($query);
					}
				}				
			}
			return;
		}

		/**
		 * function to valid the security token in db
		 * @param string $p_token
		 *
		 * @return bool
		 */
		public function valid_sec_token($p_token)
		{
			$t_sec_token	= $this->clean_sec_token($p_token);

			$query = 	"SELECT
							`gm_css_style_security_id`
						AS
							`id`
						FROM
							`gm_css_style_security`
						WHERE
							`gm_css_style_security_token` = '" . $t_sec_token . "'
						AND
							UNIX_TIMESTAMP(`gm_css_style_security_date`)	>= (UNIX_TIMESTAMP()-30*60)";
			$result = $this->cooMySQLi->query($query);

			if($result->num_rows > 0)
			{
				$t_row = $result->fetch_assoc();
				$this->update_sec_token($t_row['id']);
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * function to clean the security token
		 * @param  string $p_token
		 *
		 * @return string|mixed
		 */
		protected function clean_sec_token($p_token)
		{
			$t_token = $this->cooMySQLi->real_escape_string($p_token);
			
			$t_token = str_replace('|', '', $t_token);
			$t_token = str_replace('/', '', $t_token);
			$t_token = str_replace(',', '', $t_token);
			$t_token = str_replace('.', '', $t_token);
			$t_token = str_replace(' ', '', $t_token);
			
			return $t_token;
		}

		/**
		 * function to get the security token id by the security token
		 * @param  string $p_token
		 *
		 * @return bool
		 */
		public function get_token_id_by_token($p_token)
		{
			$t_sec_token = $this->clean_sec_token($p_token);
			
			$query = 	"SELECT
							`gm_css_style_security_id`
						AS
							`id`
						FROM
							`gm_css_style_security`
						WHERE
							`gm_css_style_security_token` = '" . $t_sec_token . "'";
			$result = $this->cooMySQLi->query($query);

			if($this->cooMySQLi->num_rows($result) > 0)
			{
				$t_token_id = $this->cooMySQLi->fetch_array($result);

				return $t_token_id['id'];
			}
			else
			{
				return false;
			}
		}
	}	
?>