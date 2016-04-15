<?php
/* --------------------------------------------------------------
   janolaw_agb.php 2015-07-24 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_janolaw_page = 'agb';

if(defined('DIR_FS_CATALOG'))
{
	require_once(DIR_FS_CATALOG.'gm/classes/GMJanolaw.php');
	
	$coo_janolaw = new GMJanolaw();
	$t_janolaw_page_content = $coo_janolaw->get_page_content($t_janolaw_page);
	echo $t_janolaw_page_content;
}
else
{
	include('../../cache/checkout-'.$t_janolaw_page.'.html');
}

?>