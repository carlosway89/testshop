<?php


class GMSEDatabase
{
	/**
	 * @var \mysqli|null
	 */
	private $cooMySQLi;

	/**
	 * @var resource|null
	 */
	private $mySQLLink;


	/**
	 * @param string        $p_dbServer
	 * @param string        $p_dbUsername
	 * @param string        $p_dbPassword
	 * @param string        $p_dbDatabaseName
	 * @param resource|null $p_dbLink
	 *
	 * @throws \Exception
	 */
	public function __construct($p_dbServer = SE_CFG_SERVER,
	                            $p_dbUsername = SE_CFG_USERNAME,
	                            $p_dbPassword = SE_CFG_PASSWORD,
	                            $p_dbDatabaseName = SE_CFG_DATABASE,
	                            $p_dbLink = null)
	{
		$dbPort = ini_get('mysqli.default_port');
		$dbSocket = ini_get('mysqli.default_socket');
		
		if(strstr($p_dbServer, ':'))
		{
			$p_dbServer = explode(':', $p_dbServer);
			if(is_numeric($p_dbServer[1]))
			{
				$dbPort = $p_dbServer[1];
			}
			else
			{
				$dbSocket = $p_dbServer[1];
			}
			$p_dbServer = $p_dbServer[0];
		}
		
		
		if(!$p_dbLink)
		{
			$this->cooMySQLi = new mysqli($p_dbServer, $p_dbUsername, $p_dbPassword, $p_dbDatabaseName, $dbPort, $dbSocket);
			if($this->cooMySQLi->connect_errno)
			{
				throw new Exception('mysqli connect error (' . $this->cooMySQLi->connect_errno . '): '
				                    . $this->cooMySQLi->connect_error);
			}
		}
		else
		{
			$this->mySQLLink = $p_dbLink;
		}
	}


	public function query($query)
	{
		if($this->cooMySQLi)
		{
			return $this->cooMySQLi->query($query);
		}
		else
		{
			return mysql_query($query, $this->mySQLLink);
		}
	}


	public function real_escape_string($string)
	{
		if($this->cooMySQLi)
		{
			return $this->cooMySQLi->real_escape_string($string);
		}
		else
		{
			return mysql_real_escape_string($string, $this->mySQLLink);
		}
	}


	public function num_rows($result)
	{
		if($result instanceof mysqli_result)
		{
			return $result->num_rows;
		}
		else
		{
			return mysql_num_rows($result);
		}
	}


	public function fetch_array($result)
	{
		if($result instanceof mysqli_result)
		{
			return $result->fetch_array();
		}
		else
		{
			return mysql_fetch_array($result);
		}
	}


	public function close()
	{
		if($this->cooMySQLi && $this->cooMySQLi instanceof mysqli)
		{
			$this->cooMySQLi->close();
		}
	}


	/**
	 * @return \mysqli
	 */
	public function getCooMySQLi()
	{
		return $this->cooMySQLi;
	}
}
