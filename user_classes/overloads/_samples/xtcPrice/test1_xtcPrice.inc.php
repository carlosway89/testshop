<?php
/* --------------------------------------------------------------
   xtcPrice.php 2008-11-27 gm
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(currencies.php,v 1.15 2003/03/17); www.oscommerce.com
   (c) 2003         nextcommerce (currencies.php,v 1.9 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtcPrice.php 1316 2005-10-21 15:30:58Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class test1_xtcPrice extends test1_xtcPrice_parent {

	function xtcFormat($price, $format, $tax_class = 0, $curr = false, $vpeStatus = 0, $pID = 0) 
	{
		# modify input parameter
		$price += 100000;
		
		$t_output = parent::xtcFormat($price, $format, $tax_class, $curr, $vpeStatus, $pID);
		return $t_output;
	}

}
?>