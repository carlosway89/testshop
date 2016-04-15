<?php
/* --------------------------------------------------------------
   HelloWorldExtender.inc.php 2012-01-26 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class HelloWorldExtender extends HelloWorldExtender_parent
{
	function proceed()
	{
		# print 'Hello World' at the bottom of the page
		$this->v_output_buffer['HELLO_WORLD'] = 'Hello World!';

		parent::proceed();
	}
}
?>