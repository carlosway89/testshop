<?php
/* --------------------------------------------------------------
   EcondaApplicationTopExtender.inc.php 2012-01-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class EcondaApplicationTopExtender extends EcondaApplicationTopExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		// econda tracking
		if (TRACKING_ECONDA_ACTIVE=='true') {
			include_once(DIR_WS_INCLUDES . 'econda/emos.php');
		}		
	}
}