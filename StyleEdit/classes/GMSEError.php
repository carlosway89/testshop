<?php
	/* --------------------------------------------------------------
	  StyleEdit v2.0
	  Gambio GmbH
	  http://www.gambio.de
	  Copyright (c) 2010 Gambio GmbH
	  --------------------------------------------------------------
	*/

	/*
	*	this class handles the error management
	*/
	class GMSEError
	{
		/*
		*	constructor
		*/
		function GMSEError()
		{
			return;
		}

		/*
		*	function return error
		*	@param int $p_error_id
		*/
		function get_error($p_error_id)
		{
			switch($p_error_id)
			{
				case 1:
					return GMSE_ERROR_FILE_EXISTS;
				break;

				case 2:
					return GMSE_ERROR_FILE_NOT_EXIST;
				break;

				case 3:
					return GMSE_ERROR_WRONG_DIR_PERM;
				break;

				case 4:
					return GMSE_ERROR_WRONG_FILE_PERM;
				break;

				case 5:
					return GMSE_ERROR_CANNOT_OPEN_FILE;
				break;

				case 6:
					return GMSE_ERROR_WRONG_FILE_TYP;
				break;

				case 7:
					return GMSE_ERROR_UPLOAD_FAILED;
				break;

				case 8:
					return GMSE_ERROR_ARCHIVE_EMPTY;
				break;

				case 9:
					return GMSE_ERROR_WRONG_FILENAME;
				break;

				case 10:
					return GMSE_ERROR_DELETE_FAILED;
				break;

				case 11:
					return '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . GMSE_ERROR_ARCHIVE_EMPTY;
				break;

				case 12:
					return GMSE_ERROR_STYLES_EMPTY;
				break;

				case 13:
					return GMSE_ERROR_NO_IMAGE;
				break;

				case 0:
				default:
					return GMSE_ERROR_NO_ERROR;
				break;

				case -1:
					return GMSE_ERROR_UPLOAD_SUCCESSFUL;
				break;

				case -2:
					return GMSE_ERROR_EXPORT_SUCCESSFUL;
				break;

				case -3:
					return GMSE_ERROR_IMPORT_SUCCESSFUL;
				break;

				case -4:
					return GMSE_ERROR_DELETE_SUCCESSFUL;
				break;

				case -5:
					return GMSE_ERROR_DELETE_CONFIRM;
				break;

				case -6:
					return GMSE_ERROR_STYLES_UPDATED;
				break;

				case 666:
					return GMSE_ERROR_LOGOFF;
				break;
			}
		}
	}
?>