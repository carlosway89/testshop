<?php
/* --------------------------------------------------------------
   janolaw_agb.php 2009-12-06 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG.'gm/classes/GMJanolaw.php');

$t_janolaw_page = 'datenschutzerklaerung';

$coo_janolaw = new GMJanolaw();
$t_janolaw_page_content = $coo_janolaw->get_page_content($t_janolaw_page);

echo $t_janolaw_page_content;





?>