<?php
/* --------------------------------------------------------------
   gm_sitemap_creator.php 2015-10-09
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
require 'includes/application_top.php';
require_once DIR_FS_INC . 'xtc_category_link.inc.php';
require_once DIR_FS_INC . 'xtc_product_link.inc.php';
require_once DIR_FS_INC . 'xtc_cleanName.inc.php';
require_once DIR_FS_CATALOG . 'gm/inc/gm_xtc_href_link.inc.php';
require_once DIR_FS_ADMIN . 'gm/classes/GMSitemapXML.php';

if($_GET['action'] == 'create_sitemap')
{
	$_SESSION['coo_page_token']->is_valid($_REQUEST['page_token']);
	$sitemap = new GMSitemapXML();

	echo $sitemap->create();
}