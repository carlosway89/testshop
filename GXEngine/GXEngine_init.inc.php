<?php
/* --------------------------------------------------------------
   GXEngine_init.inc.php 2015-10-07 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/*
 * @todo: Check BASEPATH-define and paths in requires (NC)
 * @todo: Check ampersands (and remove?)
 * @todo: Remove unused database drivers
 * @todo: Check MIT and GPL headers (NC)
 * @todo: Check connection charset collation (NC)
 * @todo: Check global $CI; (NC)
 */

require_once(DIR_FS_CATALOG . 'vendor/gambio/codeigniter-db/CIDB.php');
require_once(DIR_FS_CATALOG . 'GXEngine/Classes/SystemServices/Http/Interfaces/ContentViewInterface.inc.php');
