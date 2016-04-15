<?php
/* --------------------------------------------------------------
   moneyorder.php 2008-08-10 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(moneyorder.php,v 1.10 2003/01/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (moneyorder.php,v 1.7 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: moneyorder.php 998 2005-07-07 14:18:20Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class test1_moneyorder extends test1_moneyorder_parent {

	function test1_moneyorder() {
		parent::__construct();
		$this->title = 'TEST1 '. MODULE_PAYMENT_MONEYORDER_TEXT_TITLE;
	}
}
?>