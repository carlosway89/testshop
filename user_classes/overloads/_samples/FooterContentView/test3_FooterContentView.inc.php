<?php
/* --------------------------------------------------------------
   test_ContentView.inc.php 2011-09-20 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


class test3_FooterContentView extends test3_FooterContentView_parent
{

	function get_html()
	{
		# get original output
		$t_html = parent::get_html();

		# modify output
		$t_html = ' TEST3 '. $t_html;
		
		# return modified output
		return $t_html;
	}
}

?>