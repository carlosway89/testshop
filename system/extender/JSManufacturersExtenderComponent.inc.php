<?php

/* --------------------------------------------------------------
  JSManufacturersExtenderComponent.inc.php 2012-01-18 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2012 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

MainFactory::load_class('ExtenderComponent');

class JSManufacturersExtenderComponent extends ExtenderComponent
{
	function get_permission_status($p_customers_id = NULL)
	{
		return true;
	}
}
?>