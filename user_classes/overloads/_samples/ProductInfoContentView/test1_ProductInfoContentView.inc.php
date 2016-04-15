<?php
/* --------------------------------------------------------------
   main.php 2008-08-07 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(Coding Standards); www.oscommerce.com
   (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: main.php 1286 2005-10-07 10:10:18Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

 class test1_ProductInfoContentView extends test1_ProductInfoContentView_parent {


	function build_html($p_content_data_array=false, $p_template_file=false)
	{
		$this->set_content_data('FORM_ACTION', 'ABC');

		return parent::build_html($p_content_data_array, $p_template_file);
	}



 }


?>