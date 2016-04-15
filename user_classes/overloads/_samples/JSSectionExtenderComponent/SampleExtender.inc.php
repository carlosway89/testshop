<?php
/* --------------------------------------------------------------
   SampleExtender.inc.php 2015-07-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SampleExtender extends SampleExtender_parent
{
	protected function filter_set_main()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/gm/javascript/jquery.mousewheel.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/gm/javascript/jquery.tinyscrollbar.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/filter/filter_set_main.js'));
	}
}