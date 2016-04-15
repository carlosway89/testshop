<?php
/* --------------------------------------------------------------
   SampleExtender.inc.php 2014-01-01 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SampleExtender extends SampleExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		echo '<meta http-equiv="Content-Script-Type" content="text/javascript">';
	}
}