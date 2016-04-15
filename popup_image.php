<?php
/* --------------------------------------------------------------
   popup_image.php 2013-10-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(popup_image.php,v 1.12 2001/12/12); www.oscommerce.com
   (c) 2004 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: popup_image.php 859 2005-04-14 18:15:06Z novalis $) 

   Released under the GNU General Public License 
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Modified by BIA Solutions (www.biasolutions.com) to create a bordered look to the image

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

require ('includes/application_top.php');

$coo_popup_image_view = MainFactory::create_object('PopupImageContentView');

$t_view_html = $coo_popup_image_view->get_html($_GET['pID'], $_GET['imgID'], $_SESSION['languages_id']);

echo $t_view_html;

xtc_db_close();
