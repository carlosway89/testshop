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

		// PHP Code
		// {...}
		
		// HTML Output
		echo '<div style="background: #fff; margin: 0 auto 20px auto; width: 1000px; line-height: 50px; text-align: center;">Gambio ApplicationBottomExtenderComponent</div>';
	}
}