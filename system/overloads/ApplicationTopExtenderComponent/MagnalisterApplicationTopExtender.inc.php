<?php
/* --------------------------------------------------------------
   MagnalisterApplicationTopExtender.inc.php 2014-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class MagnalisterApplicationTopExtender extends MagnalisterApplicationTopExtender_parent
{
	function proceed()
	{
		parent::proceed();

		if(defined('MAGNALISTER_PASSPHRASE') == false)
		{
			return false;
		}
		
		/* magnalister v1.0.0 */
		if (!defined('MAGNA_CALLBACK_MODE') && file_exists(DIR_FS_DOCUMENT_ROOT.'magnaCallback.php')) {
			ob_start();
			require_once(DIR_FS_DOCUMENT_ROOT.'magnaCallback.php');
			magnaExecute('magnaCollectStats');
			ob_end_clean();
		}
		/* END magnalister */		
	}
}