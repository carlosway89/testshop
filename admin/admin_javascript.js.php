<?php
/* --------------------------------------------------------------
   admin_javascript.js.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.	
   --------------------------------------------------------------
*/
?><?php

require_once('includes/application_top.php');

if(isset($_SESSION['language_charset']))
{
	header('Content-Type: text/javascript; charset=' . $_SESSION['language_charset']);
}
else
{
	header('Content-Type: text/javascript; charset=utf-8');
}

include_once(DIR_FS_ADMIN . 'gm/javascript/admin_info_box.js.php');

?>