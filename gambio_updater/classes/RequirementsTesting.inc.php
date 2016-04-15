<?php
/* --------------------------------------------------------------
   RequirementsTesting.inc.php 2015-09-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

// This File should be in PHP4 Style!

/**
 * Class RequirementsTesting
 */
class RequirementsTesting
{

	/**
	 * @var array
	 */
	var $info = array('php' => '', 'mySQL' => '', 'updatedFiles' => array(), 'styleEditV2Files' => array());

	var $server;


	/**
	 * Initialize the requirement testing instance.
	 */
	function RequirementsTesting()
	{
		$this->server = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'windows' : '';
	}


	/**
	 *
	 * @param $p_minPHPVersion
	 * @param $p_minMySQLVersion
	 *
	 * @return bool
	 */
	function textPHPAndMySQLVersion($p_minPHPVersion, $p_minMySQLVersion)
	{
		$phpTestResult   = $this->testPHPVersion($p_minPHPVersion);
		$mysqlTestResult = $this->testMySQLVersion($p_minMySQLVersion);

		$res = ($phpTestResult && $mysqlTestResult);

		return $res;
	}


	/**
	 * Test if all updated files are uploaded in the last 6 hours after the
	 * updated_files.txt was uploaded.
	 *
	 * @param string $currentVersion Version of update.
	 * @param bool   $newest         True when it is the newest update.
	 *
	 * @return bool True when all files are uploaded in the expected time, false otherwise.
	 */
	function testUpdateFiles($currentVersion, $newest)
	{
		return $this->_checkFileList($currentVersion, $newest);
	}


	/**
	 * Test if all style edit files are uploaded in the last 6 hours after the
	 * style_edit_v2_files.txt was uploaded.
	 *
	 * @param string $currentVersion Version of update.
	 *
	 * @return bool True when all files are uploaded in the expected time, false otherwise.
	 */
	function testStyleEditFiles($currentVersion)
	{
		return $this->_checkFileList($currentVersion, false, 'styleEditV2Files');
	}


	/**
	 * Check the files list of an update package.
	 * The method arguments determine if the check is for updated or style edit files.
	 *
	 * @param string $currentVersion Current update version.
	 * @param bool   $newest         True when it is the newest update package.
	 * @param string $type           Testing type. Either 'updatedFiles' or 'styleEditV2Files'
	 *
	 * @return bool Either true when all required files are uploaded or false.
	 */
	function _checkFileList($currentVersion, $newest, $type = 'updatedFiles')
	{
		$rootDirExists    = true;
		$updatedFilesName = 'updated_files';
		if($type === 'styleEditV2Files')
		{
			$updatedFilesName = 'style_edit_v2_files';
			$rootDirExists    = (is_dir(DIR_FS_CATALOG . 'StyleEdit'));
		}

		$allFilesUploaded = true;
		$errors           = array();

		$maxUploadDuration = (60 * 60 * 6); # represent the max upload duration time in milliseconds.
		//$maxUploadDuration = (15); # represent the max upload duration time in milliseconds.

		$versionArray = explode(' ', $currentVersion);
		$version      = $versionArray[0];

		$updatedFilesPath =
			'.'
			. DIRECTORY_SEPARATOR
			. 'updates'
			. DIRECTORY_SEPARATOR
			. $version
			. DIRECTORY_SEPARATOR
			. $updatedFilesName
			. '.txt';

		if($rootDirExists && file_exists($updatedFilesPath))
		{
			$newFilesArray              = $this->_prepareNewFilesArray($updatedFilesPath, $newest, $currentVersion);
			$updateFileListCreationTime = $this->_fileTimeWrapper($updatedFilesPath);

			foreach($newFilesArray as $key => $value)
			{
				if($value < ($updateFileListCreationTime - $maxUploadDuration))
				{
					$allFilesUploaded = false;
					$errors[]         = $key;
				}
			}

			$this->info[$type] = (count($errors) > 0) ? $errors : array();
		}

		return $allFilesUploaded;
	}


	/**
	 * Prepare an array which hold the new files and the creation time of them.
	 * The file paths are stored as key and creation times as value.
	 *
	 * @param $updatedFilesPath
	 * @param $newest
	 * @param $currentVersion
	 *
	 * @return array
	 */
	function _prepareNewFilesArray($updatedFilesPath, $newest, $currentVersion)
	{
		$newFilesArray = array_map('trim', file($updatedFilesPath));

		$filesArray = array();

		foreach($newFilesArray as $newFile)
		{
			$filePath = DIR_FS_CATALOG . $newFile;

			if(file_exists($filePath))
			{
				$filesArray[str_replace(DIR_FS_CATALOG, '', $filePath)] = $this->_fileTimeWrapper($filePath);
			}
			else
			{
				$filesArray[str_replace(DIR_FS_CATALOG, '', $filePath)] = 0;
			}
		}

		if(!$newest)
		{
			$this->_filterMovedFilesFromFileArray($filesArray, $currentVersion)
			     ->_filterToDeleteFilesFromFileArray($filesArray, $currentVersion);
		}

		return $filesArray;
	}


	/**
	 * Prepare and merge the $filesArray with the list of the move.txt file.
	 *
	 * @param $filesArray
	 * @param $currentVersion
	 *
	 * @return $this
	 */
	function _filterMovedFilesFromFileArray(&$filesArray, $currentVersion)
	{
		$move =
			'.'
			. DIRECTORY_SEPARATOR
			. 'updates'
			. DIRECTORY_SEPARATOR
			. $currentVersion
			. DIRECTORY_SEPARATOR
			. 'move.txt';

		if(file_exists($move))
		{
			$filesToMoveArray = array_map('trim', file($move));

			foreach($filesToMoveArray as $fileToMove)
			{
				$moveArray           = explode('=>', $fileToMove);
				$preparedOldFilePath = trim(trim($moveArray[0]), "'");
				$preparedNewFilePath = trim(trim($moveArray[1]), "'");

				foreach($filesArray as $key => $timestamp)
				{
					if(strpos($key, $preparedOldFilePath) === 0)
					{
						$filesArray[$preparedNewFilePath] = $filesArray[$key];
						unset($filesArray[$key]);
					}
				}
			}
		}

		return $this;
	}


	/**
	 * Prepare and merge the $filesArray with the list of the to_delete.txt file.
	 *
	 * @param $filesArray
	 * @param $currentVersion
	 *
	 * @return $this
	 */
	function _filterToDeleteFilesFromFileArray(&$filesArray, $currentVersion)
	{
		$toDelete =
			'.'
			. DIRECTORY_SEPARATOR
			. 'updates'
			. DIRECTORY_SEPARATOR
			. $currentVersion
			. DIRECTORY_SEPARATOR
			. 'to_delete.txt';

		if(file_exists($toDelete))
		{
			$toDeleteArray = array_map('trim', file($toDelete));

			foreach($toDeleteArray as $toDeleteFile)
			{
				$needle = (substr($toDeleteFile, -1, 1) === '*') ? substr($toDeleteFile,
				                                                          0,
				                                                          strpos($toDeleteFile, '*')) : $toDeleteFile;

				foreach($filesArray as $key => $timestamp)
				{
					if(strpos($key, $needle) === 0)
					{
						unset($filesArray[$key]);
					}
				}
			}
		}

		return $this;
	}


	/**
	 * @param $p_minPHPVersion
	 *
	 * @return bool
	 */
	function testPHPVersion($p_minPHPVersion)
	{
		$testResult            = false;
		$minPHPMeetRequirement = version_compare(PHP_VERSION, $p_minPHPVersion, '>=');

		if($minPHPMeetRequirement)
		{
			$testResult = true;
		}

		$this->info['php'] = PHP_VERSION;

		return $testResult;
	}


	/**
	 * @param $p_minMySQLVersion
	 *
	 * @return bool
	 */
	function testMySQLVersion($p_minMySQLVersion)
	{
		$testResult = false;

		// check if mysqli is installed
		if(function_exists('mysqli_connect'))
		{
			$mysqli = $this->connect();

			$version = (string)$mysqli->server_version;
			$mysqli->close();

			$mainVersion = (int)($version / 10000);
			$version -= $mainVersion * 10000;

			$minorVersion = (int)($version / 100);
			$version -= $minorVersion * 100;

			$subVersion = $version;

			$actualMySQLVersion = $mainVersion . '.' . $minorVersion . '.' . $subVersion;

			$minMySQLMeetRequirement = version_compare($actualMySQLVersion, $p_minMySQLVersion, '>=');

			$this->info['mySQL'] = $actualMySQLVersion;

			if($minMySQLMeetRequirement)
			{
				$testResult = true;
			}
		}
		else
		{
			$this->info['mySQL'] = 'No mysqli - Extension! Unknown Mysql-Version';
		}

		return $testResult;
	}


	/**
	 * @return mysqli
	 */
	function connect()
	{
		if(file_exists(dirname(__FILE__) . '/../../includes/local/configure.php'))
		{
			require_once(dirname(__FILE__) . '/../../includes/local/configure.php');
		}
		else
		{
			require_once(dirname(__FILE__) . '/../../includes/configure.php');
		}

		$host   = DB_SERVER;
		$port   = ini_get("mysqli.default_port");
		$socket = ini_get("mysqli.default_socket");

		if(strstr($host, ':'))
		{
			$host = explode(':', $host);

			if(is_numeric($host[1]))
			{
				$port = $host[1];
			}
			else
			{
				$socket = $host[1];
			}

			$host = $host[0];
		}

		return new mysqli($host, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, $port, $socket);
	}


	/**
	 * Wrapper method for whether the filectime() or filemtime() spl function.
	 * On windows iis servers, the filemtime() function is used, otherwise filectime().
	 * After execution, the clearstatcache() functions gets execute.
	 *
	 * @param string $fileName Searched file.
	 *
	 * @return int Unix time stamp of file creation time.
	 */
	function _fileTimeWrapper($fileName)
	{
		$cTime = ($this->server === 'windows') ? filemtime($fileName) : filectime($fileName);
		clearstatcache();

		return $cTime;
	}


	/**
	 * @return array
	 */
	function getInfo()
	{
		return $this->info;
	}
}